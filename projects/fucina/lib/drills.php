<?php
/**
 * Fucina — drills: short playful exercises inside a level, between the theory and the quiz.
 *
 * Kinds (a text is a string, or {"it": …, "en": …} when it depends on the language):
 *   blanks  code with ___ gaps; gaps: the right tiles in order; extra: wrong tiles
 *   order   lines: the lines in the right order, shown shuffled
 *   bug     lines of code; bug: index of the wrong line; fix: the same line corrected
 *   match   pairs: [[left, right], …] to join
 *   flash   cards: [[statement, true|false], …] against the clock
 *   tune    a figure to copy with sliders and choices: layout box|flex|grid, items, base, itemBase, props
 * Every kind has prompt and why.
 *
 * Like the games, a drill is played entirely in the browser, for immediate feedback, so the browser knows its solution.
 * That is why drills are not exam material and each one pays XP only the first time: see drill.finish.
 */

declare(strict_types=1);

const DRILL_KINDS = ['blanks', 'order', 'bug', 'match', 'flash', 'tune'];

/** Drills of some lessons, grouped by the id of the lesson. */
function drills_by_lesson(array $lessonIds): array
{
    if (!$lessonIds) return [];
    $in = implode(',', array_map('intval', $lessonIds));
    $out = [];
    foreach (db()->query('SELECT * FROM ' . t('drills') . " WHERE lesson_id IN ($in) ORDER BY sort, id")->fetchAll() as $d) {
        $out[(int)$d['lesson_id']][] = ['id' => (int)$d['id'], 'kind' => $d['kind']] + js($d['data']);
    }
    return $out;
}

function drill_stars(int $mistakes): int
{
    return $mistakes === 0 ? 3 : ($mistakes <= 2 ? 2 : 1);
}

/** Stars of the drills already solved by a learner, by drill id. */
function drill_rows(int $uid): array
{
    $st = db()->prepare('SELECT drill_id, stars FROM ' . t('drill_done') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    return array_map('intval', array_column($st->fetchAll(), 'stars', 'drill_id'));
}

function drill_out(array $d, array $done): array
{
    return deep_pick($d) + ['stars' => $done[$d['id']] ?? 0];
}

/** Is this a text: a non-empty string, or a language map with at least one language? */
function drill_text($v): bool
{
    if (is_string($v)) return trim($v) !== '';
    return is_lang_map($v) && array_filter($v, fn($x) => is_string($x) && trim($x) !== '');
}

function drill_texts($list, int $min): bool
{
    return is_array($list) && count($list) >= $min && array_keys($list) === range(0, count($list) - 1) && !array_filter($list, fn($x) => !drill_text($x));
}

/** What is wrong with a drill, in English for the portal and for tools/check-drills.php, or null when it can be played. */
function drill_problem(string $kind, array $d): ?string
{
    if (!in_array($kind, DRILL_KINDS, true)) return 'Unknown kind of exercise.';
    if (!drill_text($d['prompt'] ?? null)) return 'Write the instructions.';
    if (!drill_text($d['why'] ?? null)) return 'Write the explanation.';
    switch ($kind) {
        case 'blanks':
            if (!is_string($d['code'] ?? null)) return 'code must be a text with ___ in place of the gaps.';
            if (!drill_texts($d['gaps'] ?? null, 1)) return 'gaps must list the right tiles.';
            if (substr_count($d['code'], '___') !== count($d['gaps'])) return 'code must have one ___ for each item of gaps.';
            if (!drill_texts($d['extra'] ?? null, 1)) return 'extra must list at least one wrong tile.';
            if (array_intersect(array_map('json_encode', $d['extra']), array_map('json_encode', $d['gaps']))) return 'A wrong tile cannot also be right.';
            return null;
        case 'order':
            if (!drill_texts($d['lines'] ?? null, 3)) return 'lines must list at least 3 lines of code.';
            if (count(array_unique(array_map('json_encode', $d['lines']))) < 3) return 'lines must have at least 3 different lines.';
            return null;
        case 'bug':
            if (!drill_texts($d['lines'] ?? null, 2)) return 'lines must list at least 2 lines of code.';
            if (!is_int($d['bug'] ?? null) || !isset($d['lines'][$d['bug']])) return 'bug must be the index of a line, starting from 0.';
            if (!is_string($d['fix'] ?? null) || trim($d['fix']) === '' || $d['fix'] === $d['lines'][$d['bug']]) return 'fix must be the corrected line.';
            return null;
        case 'match':
            $pairs = $d['pairs'] ?? null;
            if (!is_array($pairs) || count($pairs) < 3) return 'pairs must have at least 3 pairs.';
            foreach ($pairs as $p) if (!is_array($p) || count($p) !== 2 || !drill_text($p[0] ?? null) || !drill_text($p[1] ?? null)) return 'Every pair must have two texts.';
            return null;
        case 'flash':
            $cards = $d['cards'] ?? null;
            if (!is_array($cards) || count($cards) < 4) return 'cards must have at least 4 cards.';
            foreach ($cards as $c) if (!is_array($c) || !drill_text($c[0] ?? null) || !is_bool($c[1] ?? null)) return 'Every card must be [text, true or false].';
            return null;
        case 'tune':
            if (!in_array($d['layout'] ?? '', ['box', 'flex', 'grid'], true)) return 'layout must be box, flex or grid.';
            if (!is_array($d['props'] ?? null) || !$d['props']) return 'props must list at least one property.';
            $away = false;
            foreach ($d['props'] as $p) {
                if (!is_array($p) || !preg_match('/^[a-z-]+$/', (string)($p['prop'] ?? ''))) return 'Every property needs a CSS name in prop.';
                if (isset($p['choices'])) {
                    if (!is_array($p['choices']) || count($p['choices']) < 2 || !in_array($p['target'] ?? null, $p['choices'], true)) return 'target must be one of the choices.';
                    if (($p['start'] ?? $p['choices'][0]) !== $p['target']) $away = true;
                } else {
                    foreach (['min', 'max', 'step', 'target'] as $k) if (!is_numeric($p[$k] ?? null)) return "A slider needs min, max, step and target: $k is missing.";
                    if ($p['step'] <= 0 || $p['target'] < $p['min'] || $p['target'] > $p['max'] || fmod($p['target'] - $p['min'], $p['step']) != 0) return 'target must be a position of the slider.';
                    if (($p['start'] ?? $p['min']) != $p['target']) $away = true;
                }
            }
            return $away ? null : 'The figure must not start already solved.';
    }
    return null;
}
