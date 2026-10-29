<?php
/**
 * "Other useful documents" (PDF) of the Documents section, managed by administrators.
 * The official documents that questions and references come from (Regole di gioco, Casistica, etc.)
 * stay fixed in documenti/ and cannot be changed from here: these files live in documenti/altri/.
 */

declare(strict_types=1);

const DOC_MAX_BYTES_DEFAULT = 24 * 1024 * 1024;

/** Folder of the managed PDFs (configurable with 'documents_dir' in lib/config.php). */
function documents_dir(): string
{
    $dir = (string)(config()['documents_dir'] ?? '');
    if ($dir === '') $dir = dirname(__DIR__) . '/documenti/altri';
    return rtrim($dir, '/');
}

/** Public path (relative to the portal) of the managed PDFs. */
const DOCUMENTS_PUBLIC_PATH = 'documenti/altri/';

function ini_bytes(string $v): int
{
    $v = trim($v);
    if ($v === '') return 0;
    $n = (int)$v;
    switch (strtolower(substr($v, -1))) {
        case 'g': $n *= 1024;
        case 'm': $n *= 1024;
        case 'k': $n *= 1024;
    }
    return $n;
}

/** Maximum PDF size: the lowest of the portal limit and the PHP limits. */
function documents_max_bytes(): int
{
    $limits = [DOC_MAX_BYTES_DEFAULT];
    foreach (['upload_max_filesize', 'post_max_size'] as $k) {
        $b = ini_bytes((string)ini_get($k));
        if ($b > 0) $limits[] = $b;
    }
    return min($limits);
}

function document_row_out(array $d, bool $forAdmin): array
{
    $path = documents_dir() . '/' . $d['file_name'];
    $out = [
        'id'          => (int)$d['id'],
        'title'       => $d['title'],
        'edition'     => $d['edition'],
        'description' => $d['description'],
        'file'        => DOCUMENTS_PUBLIC_PATH . $d['file_name'],
        'size'        => (int)$d['file_size'],
        'version'     => strtotime($d['updated_at'] . ' UTC') ?: 0,
        'position'    => (int)$d['position'],
    ];
    if ($forAdmin) {
        $out['exists'] = is_file($path);
        $out['updated_at'] = $d['updated_at'];
        $out['updated_by'] = $d['updated_by'] !== null ? (int)$d['updated_by'] : null;
    }
    return $out;
}

function list_documents(bool $forAdmin = false): array
{
    $rows = db()->query('SELECT * FROM ' . t('documents') . ' ORDER BY position, id')->fetchAll();
    $out = [];
    foreach ($rows as $d) {
        $item = document_row_out($d, $forAdmin);
        // The portal only shows documents whose file actually exists
        if (!$forAdmin && !is_file(documents_dir() . '/' . $d['file_name'])) continue;
        $out[] = $item;
    }
    return $out;
}

function find_document(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('documents') . ' WHERE id = ?');
    $st->execute([$id]);
    $d = $st->fetch();
    return $d ?: null;
}

/** Safe, readable file name chosen by the server, e.g. "manuale-segnapunti-2021-2022.pdf". */
function document_file_name(string $title, string $edition, int $excludeId = 0): string
{
    $base = $title . ' ' . $edition;
    if (function_exists('iconv')) {
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $base);
        if ($t !== false) $base = $t;
    }
    $slug = strtolower($base);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim((string)$slug, '-');
    $slug = substr($slug, 0, 80);
    if ($slug === '') $slug = 'documento';
    $name = $slug . '.pdf';
    $i = 2;
    $own = $excludeId ? (find_document($excludeId)['file_name'] ?? '') : '';
    while (true) {
        $st = db()->prepare('SELECT id FROM ' . t('documents') . ' WHERE file_name = ? AND id <> ?');
        $st->execute([$name, $excludeId]);
        $takenInDb = (bool)$st->fetchColumn();
        $takenOnDisk = $name !== $own && is_file(documents_dir() . '/' . $name);
        if (!$takenInDb && !$takenOnDisk) break;
        $name = $slug . '-' . $i++ . '.pdf';
    }
    return $name;
}

/** Checks the uploaded file: it must be a real PDF within the size limit. */
function validate_uploaded_pdf(array $f): string
{
    $err = (int)($f['error'] ?? UPLOAD_ERR_NO_FILE);
    $maxMb = round(documents_max_bytes() / 1048576);
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) fail("Il PDF è troppo grande: il limite è $maxMb MB.", 413, 'too_large');
    if ($err === UPLOAD_ERR_NO_FILE) fail('Scegli un file PDF.', 422, 'validation');
    if ($err !== UPLOAD_ERR_OK) fail('Caricamento non riuscito (errore ' . $err . '). Riprova.', 400, 'upload_error');
    $tmp = (string)($f['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) fail('Caricamento non valido.', 400, 'upload_error');
    $size = (int)filesize($tmp);
    if ($size <= 0) fail('Il file è vuoto.', 422, 'validation');
    if ($size > documents_max_bytes()) fail("Il PDF è troppo grande: il limite è $maxMb MB.", 413, 'too_large');
    $h = fopen($tmp, 'rb');
    $head = $h ? (string)fread($h, 1024) : '';
    if ($h) fclose($h);
    if (strpos($head, '%PDF-') === false) fail('Il file non è un PDF valido.', 422, 'not_pdf');
    if (function_exists('finfo_open')) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $fi ? (string)finfo_file($fi, $tmp) : '';
        if ($fi) finfo_close($fi);
        if ($mime !== '' && !in_array($mime, ['application/pdf', 'application/x-pdf', 'application/octet-stream'], true)) {
            fail('Il file non è un PDF valido.', 422, 'not_pdf');
        }
    }
    return $tmp;
}

function ensure_documents_dir_writable(): void
{
    $dir = documents_dir();
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) fail('La cartella dei documenti non esiste e non può essere creata.', 500, 'storage');
    if (!is_writable($dir)) fail('Il server non può scrivere nella cartella dei documenti: controlla i permessi di «documenti/» (ad esempio 755) dal pannello di Aruba.', 500, 'storage');
}

/**
 * Creates or updates a document. Fields: id (empty = new), title, edition, description, file (PDF, required when new).
 */
function save_document(array $in, ?array $file, int $userId): array
{
    $id = (int)($in['id'] ?? 0);
    $title = str_param($in, 'title', 160, true);
    $edition = str_param($in, 'edition', 120);
    $description = str_param($in, 'description', 600);

    $existing = $id ? find_document($id) : null;
    if ($id && !$existing) fail('Documento non trovato.', 404, 'not_found');
    $hasFile = $file && (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    if (!$existing && !$hasFile) fail('Scegli il file PDF da caricare.', 422, 'validation');

    $dir = documents_dir();
    $newName = $existing ? $existing['file_name'] : null;
    $size = $existing ? (int)$existing['file_size'] : 0;
    $tmpTarget = null;

    if ($hasFile) {
        ensure_documents_dir_writable();
        $tmp = validate_uploaded_pdf($file);
        $newName = document_file_name($title, $edition, $id);
        $tmpTarget = $dir . '/.upload-' . bin2hex(random_bytes(6)) . '.tmp';
        if (!move_uploaded_file($tmp, $tmpTarget)) fail('Impossibile salvare il file sul server.', 500, 'storage');
        @chmod($tmpTarget, 0644);
        $size = (int)filesize($tmpTarget);
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        if ($existing) {
            $pdo->prepare('UPDATE ' . t('documents') . ' SET title = ?, edition = ?, description = ?, file_name = ?, file_size = ?, updated_at = ?, updated_by = ? WHERE id = ?')
                ->execute([$title, $edition, $description, $newName, $size, now_utc(), $userId, $id]);
        } else {
            $pos = (int)$pdo->query('SELECT COALESCE(MAX(position), 0) + 1 FROM ' . t('documents'))->fetchColumn();
            $pdo->prepare('INSERT INTO ' . t('documents') . ' (title, edition, description, file_name, file_size, position, created_at, updated_at, updated_by) VALUES (?,?,?,?,?,?,?,?,?)')
                ->execute([$title, $edition, $description, $newName, $size, $pos, now_utc(), now_utc(), $userId]);
            $id = (int)$pdo->lastInsertId();
        }
        if ($tmpTarget !== null) {
            // Replace the file only after the database has been updated
            if (!@rename($tmpTarget, $dir . '/' . $newName)) throw new RuntimeException('Impossibile salvare il file sul server.');
            $tmpTarget = null;
            if ($existing && $existing['file_name'] !== $newName) {
                @unlink($dir . '/' . basename($existing['file_name']));
            }
        }
        bump_content_version();
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        if ($tmpTarget !== null) @unlink($tmpTarget);
        throw $e;
    }
    return document_row_out(find_document($id), true);
}

function delete_document(int $id): void
{
    $d = find_document($id);
    if (!$d) fail('Documento non trovato.', 404, 'not_found');
    db()->prepare('DELETE FROM ' . t('documents') . ' WHERE id = ?')->execute([$id]);
    @unlink(documents_dir() . '/' . basename($d['file_name']));
    bump_content_version();
}

/** Moves a document up (-1) or down (+1) in the list. */
function move_document(int $id, int $dir): void
{
    $rows = db()->query('SELECT id FROM ' . t('documents') . ' ORDER BY position, id')->fetchAll(PDO::FETCH_COLUMN);
    $rows = array_map('intval', $rows);
    $i = array_search($id, $rows, true);
    if ($i === false) fail('Documento non trovato.', 404, 'not_found');
    $j = $i + ($dir < 0 ? -1 : 1);
    if ($j < 0 || $j >= count($rows)) return;
    [$rows[$i], $rows[$j]] = [$rows[$j], $rows[$i]];
    $st = db()->prepare('UPDATE ' . t('documents') . ' SET position = ? WHERE id = ?');
    foreach ($rows as $pos => $docId) $st->execute([$pos + 1, $docId]);
    bump_content_version();
}

/** Documents already in documenti/altri/ at update time (migration v3). */
function seed_documents(): void
{
    if ((int)db()->query('SELECT COUNT(*) FROM ' . t('documents'))->fetchColumn() > 0) return;
    $seed = [
        ['Manuale del segnapunti', 'Referto cartaceo, stagione 2021/2022', 'Guida alla compilazione del referto cartaceo: utile per controllare il lavoro del segnapunti.', 'manuale-segnapunti-referto-cartaceo-2021-2022.pdf'],
    ];
    $st = db()->prepare('INSERT INTO ' . t('documents') . ' (title, edition, description, file_name, file_size, position, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)');
    foreach ($seed as $i => [$title, $edition, $desc, $file]) {
        $path = documents_dir() . '/' . $file;
        if (!is_file($path)) continue;
        $st->execute([$title, $edition, $desc, $file, (int)filesize($path), $i + 1, now_utc(), now_utc()]);
    }
}
