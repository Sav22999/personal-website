<?php
/**
 * Rendicontazione Ore: reads and writes the Excel timesheet, in plain PHP.
 *
 * Only zlib is needed. The layout is the one of the original sheet: a "Panoramica" sheet with the
 * year in A1 and one sheet per month with columns Data, Orario inizio, Orario fine, Straordinario,
 * Ore ordinarie, Ore straordinarie, Totale ore parziale, Note. Exported files can be imported back.
 */

declare(strict_types=1);

const XLSX_MAX_ENTRY = 32 * 1024 * 1024;

/** Reads the entries of a zip file: path => content. */
function xlsx_unzip(string $data): array
{
    $eocd = strrpos($data, "PK\x05\x06");
    if ($eocd === false) throw new UserError('Questo file non è un Excel valido (.xlsx).');
    $head = unpack('vdisk/vcdDisk/vdiskEntries/ventries/Vsize/Voffset', substr($data, $eocd + 4, 16));

    $files = [];
    $pos = $head['offset'];
    for ($i = 0; $i < $head['entries']; $i++) {
        if (substr($data, $pos, 4) !== "PK\x01\x02") throw new UserError('Il file Excel è danneggiato.');
        $c = unpack('vmade/vneeded/vflags/vmethod/vtime/vdate/Vcrc/Vcsize/Vusize/vnameLen/vextraLen/vcommentLen/vdiskStart/vinternal/Vexternal/Voffset', substr($data, $pos + 4, 42));
        $name = substr($data, $pos + 46, $c['nameLen']);
        $pos += 46 + $c['nameLen'] + $c['extraLen'] + $c['commentLen'];
        if ($c['usize'] > XLSX_MAX_ENTRY) throw new UserError('Il file Excel è troppo grande.');

        $local = unpack('vnameLen/vextraLen', substr($data, $c['offset'] + 26, 4));
        $raw = substr($data, $c['offset'] + 30 + $local['nameLen'] + $local['extraLen'], $c['csize']);
        if ($c['method'] === 0) {
            $files[$name] = $raw;
        } elseif ($c['method'] === 8) {
            $inflated = @gzinflate($raw, XLSX_MAX_ENTRY);
            if ($inflated === false) throw new UserError('Il file Excel è danneggiato.');
            $files[$name] = $inflated;
        }
    }
    return $files;
}

function xml_load(string $xml): SimpleXMLElement
{
    $doc = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET);
    if ($doc === false) throw new UserError('Il file Excel è danneggiato.');
    return $doc;
}

function xml_rich_text(SimpleXMLElement $node): string
{
    if (isset($node->t)) return (string)$node->t;
    $text = '';
    foreach ($node->r as $run) $text .= (string)$run->t;
    return $text;
}

/** Sheets of a workbook: name => [row number => [column letter => value]]. */
function xlsx_read(string $data): array
{
    $files = xlsx_unzip($data);
    if (!isset($files['xl/workbook.xml'], $files['xl/_rels/workbook.xml.rels'])) {
        throw new UserError('Questo file non è un Excel valido (.xlsx).');
    }

    $shared = [];
    if (isset($files['xl/sharedStrings.xml'])) {
        foreach (xml_load($files['xl/sharedStrings.xml'])->si as $si) $shared[] = xml_rich_text($si);
    }

    $targets = [];
    foreach (xml_load($files['xl/_rels/workbook.xml.rels'])->Relationship as $rel) {
        $targets[(string)$rel['Id']] = ltrim(preg_replace('#^/?xl/#', '', (string)$rel['Target']), '/');
    }

    $sheets = [];
    foreach (xml_load($files['xl/workbook.xml'])->sheets->sheet as $sheet) {
        $rid = (string)$sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
        $path = 'xl/' . ($targets[$rid] ?? '');
        if (!isset($files[$path])) continue;

        $rows = [];
        foreach (xml_load($files[$path])->sheetData->row as $row) {
            foreach ($row->c as $c) {
                if (!preg_match('/^([A-Z]+)(\d+)$/', (string)$c['r'], $m)) continue;
                $type = (string)$c['t'];
                if ($type === 'inlineStr') {
                    $value = xml_rich_text($c->is);
                } elseif (!isset($c->v)) {
                    continue;
                } elseif ($type === 's') {
                    $value = $shared[(int)$c->v] ?? '';
                } elseif ($type === 'b') {
                    $value = (string)$c->v === '1';
                } elseif ($type === 'str' || $type === 'e') {
                    $value = (string)$c->v;
                } else {
                    $value = (float)(string)$c->v;
                }
                $rows[(int)$m[2]][$m[1]] = $value;
            }
        }
        $sheets[(string)$sheet['name']] = $rows;
    }
    return $sheets;
}

/** A time cell (fraction of a day, or text like "7:00") as "HH:MM". */
function cell_time($value): ?string
{
    if (is_float($value)) {
        $minutes = (int)round(($value - floor($value)) * 1440);
        return $minutes < 1440 ? minutes_to_time($minutes) : null;
    }
    if (is_string($value)) {
        $minutes = time_to_minutes(str_replace('.', ':', trim($value)));
        return $minutes === null ? null : minutes_to_time($minutes);
    }
    return null;
}

/** Day of the month written in a "Data" cell: "lun 5", "martedi 3", "02/03/26" or a real date. */
function cell_day($value): ?int
{
    if (is_float($value)) {
        return (int)gmdate('j', (int)round(($value - 25569) * 86400));
    }
    return preg_match('/\d+/', (string)$value, $m) ? (int)$m[0] : null;
}

function kind_from_note(string $note): string
{
    $note = mb_strtolower($note);
    if (strpos($note, 'malattia') !== false) return 'malattia';
    if (strpos($note, 'ferie') !== false) return 'ferie';
    if (strpos($note, 'permesso') !== false) return 'permesso';
    if (strpos($note, 'pioggia') !== false || strpos($note, 'pioveva') !== false) return 'pioggia';
    return 'lavoro';
}

/**
 * Turns the sheets into day snapshots. Months come from the sheet names, never from their order
 * (the original file has Ottobre before Settembre). Nothing is corrected silently: whatever does
 * not add up goes to the warnings for the person importing to check.
 */
function timesheet_parse(array $sheets, int $fallbackYear): array
{
    $year = $fallbackYear;
    foreach ($sheets as $name => $rows) {
        if (mb_strtolower(trim($name)) === 'panoramica' && is_float($rows[1]['A'] ?? null)) {
            $year = (int)$rows[1]['A'];
        }
    }
    if ($year < 2000 || $year > 2100) throw new UserError("Non riesco a capire l'anno del foglio.");

    $days = [];
    $warnings = [];
    foreach ($sheets as $name => $rows) {
        $month = array_search(mb_strtolower(trim($name)), MONTH_NAMES, true);
        if ($month === false) continue;
        $label = MONTH_NAMES[$month];

        ksort($rows);
        $current = null;
        foreach ($rows as $number => $row) {
            if ($number === 1) continue;
            $dateCell = $row['A'] ?? '';
            if ($dateCell !== '' && $dateCell !== null) {
                $day = cell_day($dateCell);
                if ($day === null || !checkdate($month, $day, $year)) {
                    $warnings[] = "$label, riga $number: data «" . (is_string($dateCell) ? trim($dateCell) : $dateCell) . '» non riconosciuta, riga saltata.';
                    $current = null;
                    continue;
                }
                $current = sprintf('%04d-%02d-%02d', $year, $month, $day);
                if (isset($days[$current])) {
                    $warnings[] = "$label: il giorno $day compare due volte, le righe sono state unite.";
                } else {
                    $days[$current] = ['slots' => [], 'notes' => []];
                }
                if (is_string($dateCell) && preg_match('/^\s*(lun|mar|mer|gio|ven|sab|dom)/iu', $dateCell, $w)) {
                    $real = WEEKDAY_NAMES[(int)date('N', strtotime($current))];
                    if (mb_substr($real, 0, 3) !== mb_strtolower($w[1])) {
                        $warnings[] = "$label: nel foglio c'è «" . trim($dateCell) . "», ma il $day $label $year è $real.";
                    }
                }
            }
            if ($current === null) continue;

            $start = cell_time($row['B'] ?? null);
            $end = cell_time($row['C'] ?? null);
            if ($start !== null && $end !== null) {
                $days[$current]['slots'][] = slot($start, $end, ($row['D'] ?? false) === true);
            } elseif (($row['B'] ?? null) !== null || ($row['C'] ?? null) !== null) {
                $warnings[] = "$label, riga $number: orario incompleto, saltato.";
            }
            $note = preg_replace('/\s+/u', ' ', trim((string)($row['H'] ?? '')));
            if ($note !== '') $days[$current]['notes'][] = $note;
        }
    }

    $snapshots = [];
    ksort($days);
    foreach ($days as $date => $day) {
        $note = implode(' · ', array_unique($day['notes']));
        if (!$day['slots'] && $note === '') continue;   // a date written in advance, with nothing in it
        try {
            $snapshots[$date] = normalize_day(['kind' => kind_from_note($note), 'note' => $note, 'slots' => $day['slots']]);
        } catch (UserError $e) {
            $warnings[] = date_label($date) . ': ' . $e->getMessage() . ' Giornata saltata.';
        }
    }
    return ['year' => $year, 'days' => $snapshots, 'warnings' => $warnings];
}

/** What an import would do: totals per month and how many recorded days it would overwrite. */
function import_preview(int $userId, array $parsed): array
{
    $months = [];
    $overwrites = 0;
    foreach ($parsed['days'] as $date => $snapshot) {
        $m = (int)substr($date, 5, 2);
        if (!isset($months[$m])) $months[$m] = ['month' => $m, 'days' => 0, 'ordinary' => 0, 'overtime' => 0];
        [$ordinary, $overtime] = slots_minutes($snapshot['slots']);
        $months[$m]['days']++;
        $months[$m]['ordinary'] += $ordinary;
        $months[$m]['overtime'] += $overtime;
        $existing = day_get($userId, $date);
        if ($existing !== null && $existing !== $snapshot) $overwrites++;
    }
    ksort($months);
    return [
        'year' => $parsed['year'],
        'days' => $parsed['days'],
        'months' => array_values($months),
        'warnings' => $parsed['warnings'],
        'overwrites' => $overwrites,
    ];
}

function xlsx_zip(array $files): string
{
    $body = '';
    $directory = '';
    foreach ($files as $name => $content) {
        $packed = gzdeflate($content, 6);
        $crc = crc32($content);
        $header = pack('vvvvvVVVvv', 20, 0x0800, 8, 0, 0x5021, $crc, strlen($packed), strlen($content), strlen($name), 0);
        $directory .= "PK\x01\x02" . pack('v', 20) . $header . pack('vvvVV', 0, 0, 0, 0, strlen($body)) . $name;
        $body .= "PK\x03\x04" . $header . $name . $packed;
    }
    return $body . $directory . "PK\x05\x06" . pack('vvvvVVv', 0, 0, count($files), count($files), strlen($directory), strlen($body), 0);
}

function xml_escape(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function xlsx_cell(string $ref, $value, int $style = 0): string
{
    if ($value === null || $value === '') return $style ? "<c r=\"$ref\" s=\"$style\"/>" : '';
    if (is_bool($value)) return "<c r=\"$ref\" s=\"$style\" t=\"b\"><v>" . ($value ? 1 : 0) . '</v></c>';
    if (is_int($value) || is_float($value)) return "<c r=\"$ref\" s=\"$style\"><v>$value</v></c>";
    return "<c r=\"$ref\" s=\"$style\" t=\"inlineStr\"><is><t xml:space=\"preserve\">" . xml_escape((string)$value) . '</t></is></c>';
}

/** Rows are lists of [value, style]. Styles: 0 plain, 1 header, 2 time, 3 total hours, 4 bold total hours, 5 bold. */
function xlsx_sheet(array $rows, array $widths): string
{
    $cols = '';
    foreach ($widths as $i => $w) {
        $n = $i + 1;
        $cols .= "<col min=\"$n\" max=\"$n\" width=\"$w\" customWidth=\"1\"/>";
    }
    $data = '';
    foreach ($rows as $r => $row) {
        $n = $r + 1;
        $data .= "<row r=\"$n\">";
        foreach ($row as $c => $cell) {
            $data .= xlsx_cell(chr(65 + $c) . $n, $cell[0], $cell[1]);
        }
        $data .= '</row>';
    }
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
        . "<cols>$cols</cols><sheetData>$data</sheetData></worksheet>";
}

/** The year in the same layout as the original workbook. */
function xlsx_export(int $userId, int $year): string
{
    $hours = function (int $minutes): float {
        return round($minutes / 1440, 8);
    };
    $time = function (string $hm) use ($hours): float {
        return $hours(time_to_minutes($hm));
    };

    $overview = [[[$year, 5], ['Ordinarie', 1], ['Straordinarie', 1], ['Totale', 1], ['Giorni lavorati', 1]]];
    $sheets = [];
    $data = year_data($userId, $year);
    foreach ($data['months'] as $m) {
        $overview[] = [
            [ucfirst(MONTH_NAMES[$m['month']]), 0],
            [$hours($m['ordinary']), 3],
            [$hours($m['overtime']), 3],
            [$hours($m['total']), 3],
            [$m['worked'], 0],
        ];
    }
    $t = $data['totals'];
    $overview[] = [['Totale anno', 5], [$hours($t['ordinary']), 4], [$hours($t['overtime']), 4], [$hours($t['total']), 4], [$t['worked'], 5]];

    foreach (MONTH_NAMES as $month => $name) {
        $rows = [array_map(function ($title) {
            return [$title, 1];
        }, ['Data', 'Orario inizio', 'Orario fine', 'Straordinario', 'Ore ordinarie', 'Ore straordinarie', 'Totale ore parziale', 'Note'])];

        foreach (month_data($userId, $year, $month)['days'] as $day) {
            $ts = strtotime($day['date'] . ' 12:00:00');
            $dateLabel = WEEKDAY_NAMES[(int)date('N', $ts)] . ' ' . (int)date('j', $ts);
            $note = $day['note'] !== '' ? $day['note'] : ($day['kind'] !== 'lavoro' ? $day['kind'] : '');
            if (!$day['slots']) {
                $rows[] = [[$dateLabel, 0], ['', 0], ['', 0], [false, 0], ['', 0], ['', 0], [$hours($day['running']), 3], [$note, 0]];
                continue;
            }
            $running = $day['running'] - $day['ordinary'] - $day['overtime'];
            foreach ($day['slots'] as $i => $slot) {
                $duration = time_to_minutes($slot['end']) - time_to_minutes($slot['start']);
                $running += $duration;
                $rows[] = [
                    [$i === 0 ? $dateLabel : '', 0],
                    [$time($slot['start']), 2],
                    [$time($slot['end']), 2],
                    [$slot['overtime'], 0],
                    [$slot['overtime'] ? '' : $hours($duration), 3],
                    [$slot['overtime'] ? $hours($duration) : '', 3],
                    [$hours($running), 3],
                    [$i === 0 ? $note : '', 0],
                ];
            }
        }
        $sheets[ucfirst($name)] = xlsx_sheet($rows, [18, 12, 12, 13, 13, 16, 18, 60]);
    }

    $all = ['Panoramica' => xlsx_sheet($overview, [18, 13, 14, 12, 15])] + $sheets;

    $files = [];
    $types = '';
    $list = '';
    $rels = '';
    $i = 0;
    foreach ($all as $name => $xml) {
        $i++;
        $files["xl/worksheets/sheet$i.xml"] = $xml;
        $types .= "<Override PartName=\"/xl/worksheets/sheet$i.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml\"/>";
        $list .= '<sheet name="' . xml_escape($name) . "\" sheetId=\"$i\" r:id=\"rId$i\"/>";
        $rels .= "<Relationship Id=\"rId$i\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet\" Target=\"worksheets/sheet$i.xml\"/>";
    }
    $n = $i + 1;
    $head = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $files['[Content_Types].xml'] = $head . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' . $types . '</Types>';
    $files['_rels/.rels'] = $head . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    $files['xl/workbook.xml'] = $head . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>' . $list . '</sheets></workbook>';
    $files['xl/_rels/workbook.xml.rels'] = $head . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . $rels . "<Relationship Id=\"rId$n\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles\" Target=\"styles.xml\"/></Relationships>";
    $files['xl/styles.xml'] = $head . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<numFmts count="2"><numFmt numFmtId="164" formatCode="hh:mm"/><numFmt numFmtId="165" formatCode="[h]:mm"/></numFmts>'
        . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
        . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFE4EFEB"/></patternFill></fill></fills>'
        . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="6"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
        . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
        . '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
        . '<xf numFmtId="165" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
        . '<xf numFmtId="165" fontId="1" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1"/>'
        . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
        . '</styleSheet>';

    return xlsx_zip($files);
}
