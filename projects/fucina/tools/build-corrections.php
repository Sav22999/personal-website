<?php
/**
 * Builds lib/seed/corrections-v29.json: the texts the catalogue corrected since earlier copies of lib/, so that the
 * installations made before can receive them too (install_seed_corrections_v29 in lib/seed.php).
 *
 *   php tools/build-corrections.php [/path/to/old/lib ...]
 *
 * For every course field, lesson field (title, body, exercise), question and drill that changed, it stores the hashes of
 * its old values (one per earlier catalogue that had it different) and the new value: the migration replaces only what is
 * still exactly as one of those catalogues installed it, so what was edited in the admin portal stays. Lessons that no
 * earlier catalogue had need no entry: the normal update inserts them.
 */
declare(strict_types=1);

const FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

if (($argv[1] ?? '') === '--dump') {
    dump_catalogue($argv[2]);
    exit;
}
$olds = array_slice($argv, 1);
$file = __DIR__ . '/../lib/seed/corrections-v29.json';
// The corrections already built are kept: their old texts stay recognised, and so do the texts they brought, in case they
// were published before this new round. So only the catalogues published since the last build need to be passed.
$previous = is_file($file) ? (json_decode((string)file_get_contents($file), true) ?: []) : [];
if (!$olds && !$previous || array_filter($olds, fn($d) => !is_file($d . '/seed.php'))) {
    fwrite(STDERR, "Usage: php tools/build-corrections.php /path/to/old/lib [...] (the old libs can be left out when lib/seed/corrections-v29.json exists)\n");
    exit(1);
}

$read = function (string $lib): array {
    $json = shell_exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' --dump ' . escapeshellarg($lib));
    $data = json_decode((string)$json, true);
    if (!is_array($data)) {
        fwrite(STDERR, "Cannot read the catalogue in $lib\n");
        exit(1);
    }
    return $data;
};
$new = $read(realpath(__DIR__ . '/../lib'));
$hash = fn($v) => sha1(json_encode($v, FLAGS));

$out = ['courses' => [], 'lessons' => [], 'questions' => [], 'drills' => []];
// Adds an old value that differs from the new one: [hashes of the old values, new value]
$add = function (?array &$slot, $oldValue, $newValue) use ($hash): void {
    if ($oldValue === $newValue) return;
    $slot = [array_values(array_unique([...($slot[0] ?? []), $hash($oldValue)])), $newValue];
};
foreach ($olds as $dir) {
    $old = $read(realpath($dir));
    foreach ($new['courses'] as $id => $c) {
        if (!isset($old['courses'][$id])) continue;
        foreach (['title', 'short', 'desc', 'level'] as $k) {
            if ($old['courses'][$id][$k] !== $c[$k]) $add($out['courses'][$id][$k], $old['courses'][$id][$k], $c[$k]);
        }
    }
    foreach ($new['lessons'] as $key => $l) {
        if (!isset($old['lessons'][$key])) continue;
        foreach (['title', 'body', 'task'] as $k) {
            if ($old['lessons'][$key][$k] !== $l[$k]) $add($out['lessons'][$key][$k], $old['lessons'][$key][$k], $l[$k]);
        }
    }
    foreach (['questions', 'drills'] as $kind) {
        foreach ($new[$kind] as $key => $item) {
            if (isset($old[$kind][$key]) && $old[$kind][$key] !== $item) $add($out[$kind][$key], $old[$kind][$key], $item);
        }
    }
}

// What the previous build recognised, and what it brought, stays recognised as an old text of the same item
$keep = function (?array &$slot, array $before, $newValue) use ($hash): void {
    $hashes = [...$before[0], $hash($before[1])];
    $hashes = array_values(array_diff(array_unique([...($slot[0] ?? []), ...$hashes]), [$hash($newValue)]));
    if (!$hashes) return;
    $slot = [$hashes, $newValue];
};
foreach ($previous['courses'] ?? [] as $id => $fields) {
    foreach ($fields as $k => $before) if (isset($new['courses'][$id][$k])) $keep($out['courses'][$id][$k], $before, $new['courses'][$id][$k]);
}
foreach ($previous['lessons'] ?? [] as $key => $fields) {
    foreach ($fields as $k => $before) if (isset($new['lessons'][$key]) && array_key_exists($k, $new['lessons'][$key])) $keep($out['lessons'][$key][$k], $before, $new['lessons'][$key][$k]);
}
foreach (['questions', 'drills'] as $kind) {
    foreach ($previous[$kind] ?? [] as $key => $before) if (isset($new[$kind][$key])) $keep($out[$kind][$key], $before, $new[$kind][$key]);
}
$out = array_map(fn($part) => array_filter($part, fn($v) => $v !== null && $v !== []), $out);

file_put_contents($file, json_encode($out, FLAGS) . "\n");
printf("%d course fields, %d lessons, %d questions, %d drills corrected -> %s\n",
    array_sum(array_map('count', $out['courses'])), count($out['lessons']), count($out['questions']), count($out['drills']), realpath($file));

/** The catalogue of a lib/ folder as the database stores it: decoded values, keyed like the migration looks them up. */
function dump_catalogue(string $lib): void
{
    require $lib . '/i18n.php';
    require $lib . '/seed.php';
    $j = fn($v) => $v === null ? null : json_decode($v, true);
    $out = ['courses' => [], 'lessons' => [], 'questions' => [], 'drills' => []];
    $rows = seed_rows();
    foreach ($rows['courses'] as $c) $out['courses'][$c['id']] = $j($c['data']);
    foreach ($rows['lessons'] as $l) $out['lessons'][$l['course'] . '/' . $l['slug']] = ['title' => $j($l['title']), 'body' => $j($l['body']), 'task' => $j($l['task'])];
    foreach ($rows['questions'] as $q) {
        $out['questions'][$q['_lesson'] . '/' . $q['sort']] = ['q' => $j($q['q']), 'options' => $j($q['options']), 'answer' => (int)$q['answer'], 'why' => $j($q['why'])];
    }
    foreach (seed_drills() as $lesson => $drills) {
        foreach (array_values($drills) as $i => $d) {
            $kind = $d['kind'];
            unset($d['kind']);
            // Through JSON, as the database keeps it
            $out['drills'][$lesson . '/' . ($i + 1)] = ['kind' => $kind, 'data' => json_decode(json_encode($d, FLAGS), true)];
        }
    }
    echo json_encode($out, FLAGS);
}
