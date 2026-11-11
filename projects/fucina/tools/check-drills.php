<?php
/** Checks the whole catalogue: drills, questions and code exercises of every course. php tools/check-drills.php */
declare(strict_types=1);
require __DIR__ . '/../lib/i18n.php';
function js($v) { return json_decode((string)$v, true); }
function is_lang_map($v): bool { return is_array($v) && $v !== [] && !array_diff(array_keys($v), LANGS) && array_keys($v) !== range(0, count($v) - 1); }
require __DIR__ . '/../lib/drills.php';
require __DIR__ . '/../lib/seed.php';
$errors = 0;
$say = function (string $text) use (&$errors) {
    echo $text, "\n";
    $errors++;
};
$count = [];
foreach (seed_drills() as $lesson => $drills) foreach ($drills as $i => $d) {
    $count[$d['kind']] = ($count[$d['kind']] ?? 0) + 1;
    $problem = drill_problem($d['kind'], $d);
    if ($problem) $say("$lesson drill #$i ({$d['kind']}): $problem");
}
$drilled = seed_drills();
$total = 0;
$questions = 0;
foreach (SEED_COURSES as $id) {
    $c = seed_course($id);
    if ($c['id'] !== $id) $say("$id: wrong id in the file");
    if (!isset(SEED_TIERS[$id])) $say("$id: missing in SEED_TIERS");
    if (($c['after'] ?? null) !== null && !in_array($c['after'], SEED_COURSES, true)) $say("$id: after points to a missing course");
    $slugs = [];
    foreach ($c['lessons'] as $l) {
        $total++;
        $key = "$id/{$l['id']}";
        if (isset($slugs[$l['id']])) $say("$key: repeated slug");
        $slugs[$l['id']] = 1;
        if (strlen($l['id']) > 40) $say("$key: slug too long");
        if (count($l['quiz']) < 3) $say("$key: fewer than 3 questions");
        if (count($drilled[$key] ?? []) < 2) $say("$key: fewer than 2 drills");
        foreach ($l['quiz'] as $q) {
            $questions++;
            if (!isset($q['options'][$q['a']])) $say("$key {$q['id']}: the answer is not an option");
        }
        if ($l['task']) {
            foreach ($l['task']['checks'] as $ch) if (isset($ch['re']) && @preg_match('~' . str_replace('~', '\~', $ch['re']) . '~im', '') === false) $say("$key: bad pattern {$ch['re']}");
            $hint = $l['task']['hint'] ?: (SEED_TASK_HINTS[$key] ?? '');
            if ($hint === '') $say("$key: the exercise has no solution for the hint");
            // The solution of the hint must pass the pattern checks of its own exercise
            if ($l['task']['hint'] !== '' && !in_array($l['task']['lang'], ['html', 'css'], true)) {
                foreach ($l['task']['checks'] as $ch) if (isset($ch['re']) && !preg_match('~' . str_replace('~', '\~', $ch['re']) . '~im', $hint)) $say("$key: the hint fails the check {$ch['re']}");
            }
        }
    }
}
// The corrections of lib/seed/fixes.php must point to real questions and exercises, and never replace the right answer
$fixes = seed_fixes();
$known = [];
foreach (SEED_COURSES as $id) foreach (seed_course($id, false)['lessons'] as $l) {
    $known["$id/{$l['id']}"] = $l['task'] !== null;
    foreach ($l['quiz'] as $q) $known["$id/{$l['id']}/{$q['id']}"] = $q;
}
foreach ($fixes['options'] as $key => $replace) {
    $q = $known[$key] ?? null;
    if (!is_array($q)) { $say("fixes: $key is not a question"); continue; }
    foreach ($replace as $i => $option) {
        if (!isset($q['options'][$i])) $say("fixes: $key has no option $i");
        if ($i === $q['a']) $say("fixes: $key replaces the right answer");
    }
}
foreach ($fixes['checks'] as $key => $checks) if (($known[$key] ?? false) !== true) $say("fixes: $key is not an exercise");
echo count(SEED_COURSES) . " courses, $total lessons, $questions questions, " . array_sum($count) . ' drills: ' . json_encode($count) . ", $errors errors\n";
exit($errors ? 1 : 0);
