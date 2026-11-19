<?php
/**
 * Checks the catalogue: drills, questions, code exercises, figures and demo pages.
 *   php tools/check-drills.php              every course
 *   php tools/check-drills.php css-base …   only these courses (also course files not registered yet)
 */
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
$only = array_slice($argv, 1);
$courses = $only ?: SEED_COURSES;
$count = [];
// A figure is an SVG shown as an image: well formed, with a viewBox, nothing that runs or loads from elsewhere, a sane size
$figProblem = function (string $svg): ?string {
    if (!preg_match('~^\s*<svg\b~i', $svg)) return 'does not start with <svg';
    if (strlen($svg) > 40000) return 'larger than 40 KB';
    if (!preg_match('~<svg\b[^>]*\bviewBox=~i', $svg)) return 'has no viewBox';
    if (!preg_match('~<svg\b[^>]*\bxmlns="http://www\.w3\.org/2000/svg"~i', $svg)) return 'has no xmlns';
    if (preg_match('~<script|<foreignObject|\son[a-z]+\s*=|javascript:~i', $svg)) return 'contains scripts or event handlers';
    if (preg_match('~(href|src)\s*=\s*["\'](?!#|data:)~i', $svg) || preg_match('~url\(\s*["\']?(?!#|data:)~i', $svg) || preg_match('~@import~i', $svg)) return 'loads something from outside';
    libxml_use_internal_errors(true);
    $ok = simplexml_load_string($svg) !== false;
    libxml_clear_errors();
    if (!$ok) return 'is not well-formed XML';
    if (preg_match('~(@keyframes|<animate)~i', $svg) && !preg_match('~prefers-reduced-motion~i', $svg)) return 'moves without stopping for prefers-reduced-motion';
    return null;
};
foreach (seed_drills() as $lesson => $drills) foreach ($drills as $i => $d) {
    if ($only && !in_array(explode('/', $lesson)[0], $only, true)) continue;
    $count[$d['kind']] = ($count[$d['kind']] ?? 0) + 1;
    $problem = drill_problem($d['kind'], $d);
    if ($problem) $say("$lesson drill #$i ({$d['kind']}): $problem");
}
$drilled = seed_drills();
$total = 0;
$questions = 0;
foreach ($courses as $id) {
    if (!is_file(__DIR__ . '/../lib/seed/' . $id . '.php')) { $say("$id: no course file"); continue; }
    $c = seed_course($id);
    // Course files not registered yet carry their drills in their lessons
    foreach ($c['lessons'] as $l) if (!isset($drilled["$id/{$l['id']}"]) && !empty($l['drills'])) $drilled["$id/{$l['id']}"] = $l['drills'];
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
        foreach ($l['body'] as $bi => $bl) {
            if ($bl['t'] === 'fig') {
                $figs = ($figs ?? 0) + 1;
                foreach (is_array($bl['svg']) ? $bl['svg'] : ['' => $bl['svg']] as $lg => $svg) if ($p = $figProblem((string)$svg)) $say("$key figure #$bi $lg: $p");
                if (!is_array($bl['alt']) || array_filter(LANGS, fn($lg) => trim((string)($bl['alt'][$lg] ?? '')) === '')) $say("$key figure #$bi: the description is missing in a language");
            } elseif ($bl['t'] === 'code') {
                // Code and demo are one text, or one per language (both filled)
                foreach (['code', 'demo'] as $part) {
                    if (!isset($bl[$part]) || !is_array($bl[$part])) continue;
                    if (array_filter(LANGS, fn($lg) => trim((string)($bl[$part][$lg] ?? '')) === '')) $say("$key code #$bi: the $part is missing in a language");
                }
                if (isset($bl['demo'])) {
                    $demos = ($demos ?? 0) + 1;
                    foreach ((array)$bl['demo'] as $demo) {
                        if (trim($demo) === '') $say("$key code #$bi: empty demo page");
                        if (preg_match('~<script\b~i', $demo) && !in_array($c['family'], ['savcomponents', 'bootstrap', 'tailwind'], true)) $say("$key code #$bi: the demo page has a script that will not run");
                    }
                }
            }
        }
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
foreach ($courses as $id) if (is_file(__DIR__ . '/../lib/seed/' . $id . '.php')) foreach (seed_course($id, false)['lessons'] as $l) {
    $known["$id/{$l['id']}"] = $l['task'] !== null;
    foreach ($l['quiz'] as $q) $known["$id/{$l['id']}/{$q['id']}"] = $q;
}
$inScope = fn(string $key) => !$only || in_array(explode('/', $key)[0], $only, true);
foreach ($fixes['options'] as $key => $replace) {
    if (!$inScope($key)) continue;
    $q = $known[$key] ?? null;
    if (!is_array($q)) { $say("fixes: $key is not a question"); continue; }
    foreach ($replace as $i => $option) {
        if (!isset($q['options'][$i])) $say("fixes: $key has no option $i");
        if ($i === $q['a']) $say("fixes: $key replaces the right answer");
    }
}
foreach ($fixes['checks'] as $key => $checks) if ($inScope($key) && ($known[$key] ?? false) !== true) $say("fixes: $key is not an exercise");
foreach ($only as $id) if (!in_array($id, SEED_COURSES, true)) echo "note: $id is not registered in SEED_COURSES yet\n";
echo count($courses) . " courses, $total lessons, $questions questions, " . array_sum($count) . ' drills: ' . json_encode($count) . ', ' . ($figs ?? 0) . ' figures, ' . ($demos ?? 0) . " demo pages, $errors errors\n";
exit($errors ? 1 : 0);
