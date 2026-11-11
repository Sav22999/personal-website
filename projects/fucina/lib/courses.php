<?php
/**
 * Fucina — reads the catalogue from the database (managed from the admin portal).
 *
 * Quiz answers never reach the browser in advance: quiz.check reveals them one question at a time, after the
 * learner has answered. That is what gives a paid hint its value, and it keeps the exam pool out of the page.
 */

declare(strict_types=1);

require_once __DIR__ . '/drills.php';

const DEFAULT_SETTINGS = [
    'xp_lesson_base' => 10, 'xp_lesson_star' => 10, 'xp_task' => 10, 'xp_daily_correct' => 5, 'xp_daily_perfect' => 10, 'xp_certificate' => 100,
    'xp_game_max' => 30, 'xp_game_daily_cap' => 150, 'xp_drill' => 5, 'xp_drill_perfect' => 5,
    'hint_question' => 10, 'hint_task' => 20, 'hint_game' => 15, 'hint_drill' => 5,
    'exam_questions' => 10, 'exam_pass_percent' => 70, 'exam_minutes' => 15, 'cert_cost' => 200,
    'board_per_player' => 10, 'board_max' => 500, 'xp_badge' => 50,
];

function setting(string $key): int
{
    static $cache = null;
    if ($cache === null) {
        $cache = DEFAULT_SETTINGS;
        foreach (db()->query('SELECT k, v FROM ' . t('settings'))->fetchAll() as $r) if (isset($cache[$r['k']])) $cache[$r['k']] = (int)$r['v'];
    }
    return $cache[$key];
}

function js($v)
{
    return $v === null ? null : json_decode((string)$v, true);
}

/** Is this value a text with its languages, like {"it": "…", "en": "…"}? */
function is_lang_map($v): bool
{
    return is_array($v) && $v !== [] && !array_diff(array_keys($v), LANGS) && array_keys($v) !== range(0, count($v) - 1);
}

/** The text in the current language, or in another one while its translation is missing. */
function pick($v)
{
    if (!is_lang_map($v)) return $v;
    if (($v[lang()] ?? '') !== '') return $v[lang()];
    foreach ($v as $text) if ($text !== '') return $text;
    return '';
}

function deep_pick($v)
{
    if (!is_array($v)) return $v;
    return is_lang_map($v) ? pick($v) : array_map('deep_pick', $v);
}

/** Published course ids, in display order. */
function course_ids(): array
{
    static $ids = null;
    return $ids ?? ($ids = db()->query('SELECT id FROM ' . t('courses') . " WHERE status = 'published' ORDER BY sort, id")->fetchAll(PDO::FETCH_COLUMN));
}

/** A course with its published lessons and questions, or null. */
function course_raw(string $id): ?array
{
    static $cache = [];
    if (array_key_exists($id, $cache)) return $cache[$id];
    if (!in_array($id, course_ids(), true)) return $cache[$id] = null;
    $st = db()->prepare('SELECT * FROM ' . t('courses') . ' WHERE id = ?');
    $st->execute([$id]);
    $c = $st->fetch();
    $d = js($c['data']);
    $st = db()->prepare('SELECT * FROM ' . t('lessons') . " WHERE course = ? AND status = 'published' ORDER BY sort, id");
    $st->execute([$id]);
    $lessons = [];
    $byId = [];
    foreach ($st->fetchAll() as $l) {
        $lessons[] = ['id' => $l['slug'], 'dbId' => (int)$l['id'], 'title' => js($l['title']), 'difficulty' => (int)$l['difficulty'], 'body' => js($l['body']), 'task' => js($l['task']), 'quiz' => [], 'drills' => []];
        $byId[(int)$l['id']] = count($lessons) - 1;
    }
    if ($byId) {
        $in = implode(',', array_keys($byId));
        foreach (db()->query('SELECT * FROM ' . t('questions') . " WHERE lesson_id IN ($in) ORDER BY sort, id")->fetchAll() as $q) {
            $lessons[$byId[(int)$q['lesson_id']]]['quiz'][] = ['id' => (string)$q['id'], 'q' => js($q['q']), 'options' => js($q['options']), 'a' => (int)$q['answer'], 'why' => js($q['why']), 'hint' => js($q['hint']), 'difficulty' => (int)$q['difficulty']];
        }
        foreach (drills_by_lesson(array_keys($byId)) as $lessonId => $drills) $lessons[$byId[$lessonId]]['drills'] = $drills;
    }
    return $cache[$id] = ['id' => $id, 'family' => $c['family'], 'after' => $c['after_id'], 'title' => $d['title'], 'short' => $d['short'], 'desc' => $d['desc'], 'level' => $d['level'], 'examMinutes' => $d['examMinutes'] ?? null, 'categories' => $d['categories'] ?? [], 'lessons' => $lessons];
}

/** The categories a course can have (see FAMILY_CATEGORIES in lib/seed.php for the defaults). */
const COURSE_CATEGORIES_ALL = ['frontend', 'backend', 'mobile', 'data', 'systems', 'desktop', 'ai'];

/** Minutes of a course's final exam: its own value if the admin set one, otherwise the general setting (0 = no limit). */
function exam_minutes(array $c): int
{
    return isset($c['examMinutes']) ? (int)$c['examMinutes'] : setting('exam_minutes');
}

function course_questions(array $c): array
{
    $out = [];
    foreach ($c['lessons'] as $l) foreach ($l['quiz'] as $q) $out[$q['id']] = $q;
    return $out;
}

function all_questions(): array
{
    $out = [];
    foreach (course_ids() as $id) foreach (course_questions(course_raw($id)) as $qid => $q) $out[$id . '/' . $qid] = $q + ['course' => $id];
    return $out;
}

/** Question for the browser: never with the answer, unless $withAnswers. */
function question_out(array $q, bool $withAnswers = false): array
{
    $o = ['id' => $q['id'], 'q' => pick($q['q']), 'options' => array_map('pick', $q['options']), 'difficulty' => $q['difficulty'] ?? 1];
    if ($withAnswers) {
        $o['a'] = $q['a'];
        $o['why'] = pick($q['why']);
    }
    return $o;
}

/** Enabled games, or all of them for the portal. */
function games_raw(bool $all = false): array
{
    $rows = db()->query('SELECT * FROM ' . t('games') . ($all ? '' : ' WHERE enabled = 1') . ' ORDER BY sort, id')->fetchAll();
    return array_map(fn($g) => ['id' => $g['id'], 'enabled' => (bool)$g['enabled'], 'levels' => (int)$g['levels'], 'sort' => (int)$g['sort']] + js($g['data']), $rows);
}
