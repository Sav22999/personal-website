<?php
/**
 * Builds lib/seed/corrections-v26.json: the texts the catalogue corrected since a previous copy of lib/, so that the
 * installations made before can receive them too (install_seed_corrections_v26 in lib/seed.php).
 *
 *   php tools/build-corrections.php /path/to/old/lib
 *
 * For every course field, lesson field (title, body, exercise), question and drill that exists in both catalogues and
 * changed, it stores a hash of the old value and the new value: the migration replaces only what is still exactly as
 * the old catalogue installed it, so what was edited in the admin portal stays. New lessons need no entry: the normal
 * update inserts them.
 */
declare(strict_types=1);

const FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

if (($argv[1] ?? '') === '--dump') {
    dump_catalogue($argv[2]);
    exit;
}
if (!isset($argv[1]) || !is_file($argv[1] . '/seed.php')) {
    fwrite(STDERR, "Usage: php tools/build-corrections.php /path/to/old/lib\n");
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
$old = $read(realpath($argv[1]));
$new = $read(realpath(__DIR__ . '/../lib'));
$hash = fn($v) => sha1(json_encode($v, FLAGS));

$out = ['courses' => [], 'lessons' => [], 'questions' => [], 'drills' => []];
foreach ($new['courses'] as $id => $c) {
    foreach (['title', 'short', 'desc', 'level'] as $k) {
        if (isset($old['courses'][$id]) && $old['courses'][$id][$k] !== $c[$k]) $out['courses'][$id][$k] = [$hash($old['courses'][$id][$k]), $c[$k]];
    }
}
foreach ($new['lessons'] as $key => $l) {
    if (!isset($old['lessons'][$key])) continue;
    foreach (['title', 'body', 'task'] as $k) {
        if ($old['lessons'][$key][$k] !== $l[$k]) $out['lessons'][$key][$k] = [$hash($old['lessons'][$key][$k]), $l[$k]];
    }
}
foreach (['questions', 'drills'] as $kind) {
    foreach ($new[$kind] as $key => $item) {
        if (isset($old[$kind][$key]) && $old[$kind][$key] !== $item) $out[$kind][$key] = [$hash($old[$kind][$key]), $item];
    }
}

$file = __DIR__ . '/../lib/seed/corrections-v26.json';
file_put_contents($file, json_encode($out, FLAGS | JSON_PRETTY_PRINT) . "\n");
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
