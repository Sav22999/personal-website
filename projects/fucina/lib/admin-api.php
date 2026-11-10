<?php
/**
 * Fucina — API of the admin portal (admin.html). Everything here needs the role of administrator (5) or master admin (10).
 *
 * Read:   admin.overview, admin.courses, admin.course, admin.games, admin.game, admin.users, admin.certificates, admin.settings
 * Write:  admin.course.save/delete/sort, admin.lesson.save/delete/sort, admin.question.save/delete, admin.game.save,
 *         admin.drill.save/delete, admin.item.save/delete, admin.user.save/delete, admin.certificate.rename/delete, admin.settings.save
 *
 * Texts arrive as {"it": "…", "en": "…"}: a language may be empty, and the app falls back to the other one meanwhile.
 * Deleting a lesson leaves the progress rows behind on purpose: they simply stop counting.
 */

declare(strict_types=1);

function admin_reader(): array
{
    only('GET');
    return admin_user();
}

function admin_writer(): array
{
    only('POST');
    $u = admin_user();
    require_csrf($u);
    return $u;
}

function admin_user(): array
{
    $u = require_user();
    if ((int)$u['role'] < ROLE_ADMIN) fail('You don’t have permission for this action.', 403, 'forbidden');
    return $u;
}

/** A text with its languages, cleaned. With $required at least one language must be filled in. */
function lang_field($v, bool $required = true, int $max = 20000): array
{
    $out = [];
    foreach (LANGS as $l) {
        $text = is_array($v) && is_string($v[$l] ?? null) ? trim($v[$l]) : '';
        $out[$l] = mb_substr($text, 0, $max);
    }
    if ($required && !array_filter($out, 'strlen')) fail('Fill in at least one language.', 422, 'validation');
    return $out;
}

function json_out($v): string
{
    return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function slug_of($v, int $max = 40): string
{
    $s = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', (string)$v), '-'));
    return substr($s, 0, $max);
}

function status_of($v): string
{
    return $v === 'published' ? 'published' : 'draft';
}

function difficulty_of($v): int
{
    return max(1, min(3, (int)$v));
}

/** Languages still empty in a text: what the portal shows as "to translate". */
function missing_languages($v): array
{
    if (!is_lang_map($v)) return [];
    return array_keys(array_filter($v, fn($t) => $t === ''));
}

function count_missing($v): int
{
    if (!is_array($v)) return 0;
    if (is_lang_map($v)) return count(missing_languages($v)) && array_filter($v, 'strlen') ? 1 : 0;
    return array_sum(array_map('count_missing', $v));
}

function lesson_out(array $l): array
{
    return ['id' => (int)$l['id'], 'course' => $l['course'], 'slug' => $l['slug'], 'sort' => (int)$l['sort'], 'difficulty' => (int)$l['difficulty'], 'status' => $l['status'],
        'title' => js($l['title']), 'body' => js($l['body']), 'task' => js($l['task'])];
}

function question_admin(array $q): array
{
    return ['id' => (int)$q['id'], 'lessonId' => (int)$q['lesson_id'], 'sort' => (int)$q['sort'], 'difficulty' => (int)$q['difficulty'], 'q' => js($q['q']), 'options' => js($q['options']),
        'answer' => (int)$q['answer'], 'why' => js($q['why']), 'hint' => js($q['hint'])];
}

function course_row(string $id): array
{
    $st = db()->prepare('SELECT * FROM ' . t('courses') . ' WHERE id = ?');
    $st->execute([$id]);
    $c = $st->fetch();
    if (!$c) fail('Not found.', 404, 'not_found');
    return $c;
}

function clean_body($blocks): array
{
    $out = [];
    foreach (is_array($blocks) ? $blocks : [] as $b) {
        $type = $b['t'] ?? '';
        if ($type === 'code') {
            $code = trim((string)($b['code'] ?? ''), "\n");
            if ($code !== '') $out[] = ['t' => 'code', 'code' => $code, 'lang' => slug_of($b['lang'] ?? 'html', 10) ?: 'html'];
        } elseif ($type === 'p' || $type === 'tip') {
            $text = lang_field($b['text'] ?? null, false);
            if (array_filter($text, 'strlen')) $out[] = ['t' => $type, 'text' => $text];
        }
    }
    return $out;
}

function clean_task($t): ?array
{
    if (!is_array($t) || empty($t['lang'])) return null;
    // html and css get a live preview; every other language (sql, python, rust…) is checked by its patterns only
    $lang = slug_of($t['lang'], 12) ?: 'html';
    $checks = [];
    foreach (is_array($t['checks'] ?? null) ? $t['checks'] : [] as $c) {
        $label = lang_field($c['label'] ?? null, false, 300);
        if (!array_filter($label, 'strlen')) continue;
        if (!empty($c['re'])) $checks[] = ['re' => (string)$c['re'], 'label' => $label];
        elseif (!empty($c['prop'])) $checks[] = ['sel' => (string)($c['sel'] ?? ''), 'prop' => (string)$c['prop'], 'value' => (string)($c['value'] ?? ''), 'label' => $label];
        elseif (!empty($c['sel'])) $checks[] = ['sel' => (string)$c['sel'], 'text' => (string)($c['text'] ?? ''), 'label' => $label];
    }
    return ['lang' => $lang, 'prompt' => lang_field($t['prompt'] ?? null), 'start' => (string)($t['start'] ?? ''), 'html' => (string)($t['html'] ?? ''), 'hint' => (string)($t['hint'] ?? ''), 'checks' => $checks];
}

function save_order(string $table, array $ids, string $extraWhere = '', array $extra = []): void
{
    $st = db()->prepare('UPDATE ' . t($table) . ' SET sort = ? WHERE id = ?' . $extraWhere);
    foreach (array_values($ids) as $i => $id) $st->execute(array_merge([$i + 1, $id], $extra));
}

/** Keeps a game item to the shape its game expects. */
function clean_item(string $game, $data): array
{
    if ($game === 'selectors') {
        $sel = trim((string)($data['selector'] ?? ''));
        if ($sel === '') fail('Fill in at least one language.', 422, 'validation');
        return ['selector' => mb_substr($sel, 0, 200)];
    }
    if ($game === 'linehop') {
        $lines = array_values(array_filter(array_map('strval', is_array($data['lines'] ?? null) ? $data['lines'] : []), fn($l) => trim($l) !== ''));
        if (count($lines) < 2) fail('Write at least two options.', 422, 'validation');
        return ['title' => lang_field($data['title'] ?? null, true, 200), 'lines' => array_slice($lines, 0, 30)];
    }
    $name = trim((string)($data['name'] ?? ''));
    if ($name === '') fail('Fill in at least one language.', 422, 'validation');
    $traits = array_values(array_intersect(['block', 'inline', 'text', 'semantic', 'attr', 'interactive', 'void', 'media', 'form', 'list', 'table'], is_array($data['traits'] ?? null) ? $data['traits'] : []));
    return ['name' => mb_substr($name, 0, 30), 'traits' => $traits];
}

function admin_route(string $r): void
{
    switch ($r) {
        case 'admin.overview':
            admin_reader();
            $one = fn(string $sql) => (int)db()->query($sql)->fetchColumn();
            $since = day_offset(today(), -6);
            $st = db()->prepare('SELECT COUNT(DISTINCT user_id) FROM ' . t('activity') . ' WHERE day >= ?');
            $st->execute([$since]);
            $courses = [];
            foreach (db()->query('SELECT c.id, c.data, c.status, (SELECT COUNT(DISTINCT lp.user_id) FROM ' . t('lesson_progress') . ' lp WHERE lp.course = c.id) AS learners,
                (SELECT COUNT(*) FROM ' . t('certificates') . ' ce WHERE ce.course = c.id) AS certs FROM ' . t('courses') . ' c ORDER BY c.sort')->fetchAll() as $c) {
                $courses[] = ['id' => $c['id'], 'title' => pick(js($c['data'])['title']), 'status' => $c['status'], 'learners' => (int)$c['learners'], 'certificates' => (int)$c['certs']];
            }
            $st2 = db()->prepare('SELECT day, COUNT(*) AS users, SUM(xp) AS xp FROM ' . t('activity') . ' WHERE day >= ? GROUP BY day ORDER BY day');
            $st2->execute([day_offset(today(), -29)]);
            send_json(['ok' => true, 'users' => $one('SELECT COUNT(*) FROM ' . t('users')), 'activeWeek' => (int)$st->fetchColumn(), 'lessonsDone' => $one('SELECT COUNT(*) FROM ' . t('lesson_progress')),
                'gamesPlayed' => $one('SELECT COUNT(*) FROM ' . t('game_results')), 'certificates' => $one('SELECT COUNT(*) FROM ' . t('certificates')), 'hintsBought' => $one('SELECT COUNT(*) FROM ' . t('hints')),
                'questions' => $one('SELECT COUNT(*) FROM ' . t('questions')), 'lessons' => $one('SELECT COUNT(*) FROM ' . t('lessons')), 'courses' => $courses, 'days' => $st2->fetchAll()]);

        case 'admin.courses':
            admin_reader();
            $out = [];
            foreach (db()->query('SELECT * FROM ' . t('courses') . ' ORDER BY sort, id')->fetchAll() as $c) {
                $st = db()->prepare('SELECT * FROM ' . t('lessons') . ' WHERE course = ?');
                $st->execute([$c['id']]);
                $lessons = $st->fetchAll();
                $missing = count_missing(js($c['data']));
                foreach ($lessons as $l) $missing += count_missing(js($l['title'])) + count_missing(js($l['body'])) + count_missing(js($l['task']));
                $st = db()->prepare('SELECT q.* FROM ' . t('questions') . ' q JOIN ' . t('lessons') . ' l ON l.id = q.lesson_id WHERE l.course = ?');
                $st->execute([$c['id']]);
                $questions = $st->fetchAll();
                foreach ($questions as $q) $missing += count_missing(js($q['q'])) + count_missing(js($q['options'])) + count_missing(js($q['why']));
                $out[] = ['id' => $c['id'], 'family' => $c['family'], 'sort' => (int)$c['sort'], 'after' => $c['after_id'], 'status' => $c['status'], 'lessons' => count($lessons), 'questions' => count($questions), 'missing' => $missing] + js($c['data']);
            }
            send_json(['ok' => true, 'courses' => $out, 'languages' => LANGS, 'examMinutes' => setting('exam_minutes')]);

        case 'admin.course':
            admin_reader();
            $c = course_row((string)($_GET['id'] ?? ''));
            $st = db()->prepare('SELECT * FROM ' . t('lessons') . ' WHERE course = ? ORDER BY sort, id');
            $st->execute([$c['id']]);
            $lessons = array_map('lesson_out', $st->fetchAll());
            $st = db()->prepare('SELECT q.* FROM ' . t('questions') . ' q JOIN ' . t('lessons') . ' l ON l.id = q.lesson_id WHERE l.course = ? ORDER BY q.sort, q.id');
            $st->execute([$c['id']]);
            $byLesson = [];
            foreach ($st->fetchAll() as $q) $byLesson[(int)$q['lesson_id']][] = question_admin($q);
            $drills = drills_by_lesson(array_column($lessons, 'id'));
            foreach ($lessons as &$l) {
                $l['questions'] = $byLesson[$l['id']] ?? [];
                $l['drills'] = $drills[$l['id']] ?? [];
            }
            send_json(['ok' => true, 'course' => ['id' => $c['id'], 'family' => $c['family'], 'after' => $c['after_id'], 'status' => $c['status']] + js($c['data']) + ['items' => $lessons]]);

        case 'admin.course.save':
            admin_writer();
            $b = body();
            $isNew = empty($b['existing']);
            $id = slug_of($b['id'] ?? '', 30);
            if ($id === '') fail('Fill in at least one language.', 422, 'validation');
            $data = json_out(['title' => lang_field($b['title'] ?? null, true, 120), 'short' => lang_field($b['short'] ?? ($b['title'] ?? null), true, 60), 'desc' => lang_field($b['desc'] ?? null, false, 400), 'level' => lang_field($b['level'] ?? null, false, 40)]
                // What the course is for, to filter the catalogue (frontend, backend…)
                + ['categories' => array_values(array_intersect(COURSE_CATEGORIES_ALL, (array)($b['categories'] ?? [])))]
                // Time limit of this course's final exam in minutes; empty = the general one in the settings, 0 = no limit
                + (($b['examMinutes'] ?? '') === '' || $b['examMinutes'] === null ? [] : ['examMinutes' => max(0, min(240, (int)$b['examMinutes']))]));
            $after = slug_of($b['after'] ?? '', 30) ?: null;
            if ($after === $id) $after = null;
            $family = slug_of($b['family'] ?? 'html', 20) ?: 'html';
            if ($isNew) {
                $st = db()->prepare('SELECT 1 FROM ' . t('courses') . ' WHERE id = ?');
                $st->execute([$id]);
                if ($st->fetchColumn()) fail('This id is already used.', 409, 'taken');
                $sort = (int)db()->query('SELECT COALESCE(MAX(sort),0)+1 FROM ' . t('courses'))->fetchColumn();
                db()->prepare('INSERT INTO ' . t('courses') . ' (id, family, sort, after_id, status, data) VALUES (?,?,?,?,?,?)')->execute([$id, $family, $sort, $after, status_of($b['status'] ?? ''), $data]);
            } else {
                course_row($id);
                db()->prepare('UPDATE ' . t('courses') . ' SET family = ?, after_id = ?, status = ?, data = ? WHERE id = ?')->execute([$family, $after, status_of($b['status'] ?? ''), $data, $id]);
            }
            send_json(['ok' => true, 'id' => $id]);

        case 'admin.course.delete':
            admin_writer();
            $id = (string)(body()['id'] ?? '');
            course_row($id);
            db()->prepare('UPDATE ' . t('courses') . ' SET after_id = NULL WHERE after_id = ?')->execute([$id]);
            db()->prepare('DELETE FROM ' . t('courses') . ' WHERE id = ?')->execute([$id]); // lessons and questions follow
            send_json(['ok' => true]);

        case 'admin.course.sort':
            admin_writer();
            save_order('courses', (array)(body()['ids'] ?? []));
            send_json(['ok' => true]);

        case 'admin.lesson.save':
            admin_writer();
            $b = body();
            $id = (int)($b['id'] ?? 0);
            $course = course_row((string)($b['course'] ?? ''));
            $title = lang_field($b['title'] ?? null, true, 200);
            $slug = slug_of($b['slug'] ?? '') ?: slug_of(pick($title));
            if ($slug === '') $slug = 'lesson-' . random_code(4);
            $st = db()->prepare('SELECT 1 FROM ' . t('lessons') . ' WHERE course = ? AND slug = ? AND id <> ?');
            $st->execute([$course['id'], $slug, $id]);
            if ($st->fetchColumn()) fail('This id is already used.', 409, 'taken');
            $task = clean_task($b['task'] ?? null);
            $params = [$slug, difficulty_of($b['difficulty'] ?? 1), status_of($b['status'] ?? ''), json_out($title), json_out(clean_body($b['body'] ?? [])), $task ? json_out($task) : null];
            if ($id) {
                $st = db()->prepare('UPDATE ' . t('lessons') . ' SET slug = ?, difficulty = ?, status = ?, title = ?, body = ?, task = ? WHERE id = ? AND course = ?');
                $st->execute(array_merge($params, [$id, $course['id']]));
            } else {
                $st = db()->prepare('SELECT COALESCE(MAX(sort),0)+1 FROM ' . t('lessons') . ' WHERE course = ?');
                $st->execute([$course['id']]);
                $sort = (int)$st->fetchColumn();
                db()->prepare('INSERT INTO ' . t('lessons') . ' (slug, difficulty, status, title, body, task, course, sort) VALUES (?,?,?,?,?,?,?,?)')->execute(array_merge($params, [$course['id'], $sort]));
                $id = (int)db()->lastInsertId();
            }
            send_json(['ok' => true, 'id' => $id]);

        case 'admin.lesson.delete':
            admin_writer();
            db()->prepare('DELETE FROM ' . t('lessons') . ' WHERE id = ?')->execute([(int)(body()['id'] ?? 0)]);
            send_json(['ok' => true]);

        case 'admin.lesson.sort':
            admin_writer();
            save_order('lessons', (array)(body()['ids'] ?? []));
            send_json(['ok' => true]);

        case 'admin.question.save':
            admin_writer();
            $b = body();
            $id = (int)($b['id'] ?? 0);
            $lessonId = (int)($b['lessonId'] ?? 0);
            $st = db()->prepare('SELECT 1 FROM ' . t('lessons') . ' WHERE id = ?');
            $st->execute([$lessonId]);
            if (!$st->fetchColumn()) fail('Not found.', 404, 'not_found');
            $options = [];
            foreach (is_array($b['options'] ?? null) ? $b['options'] : [] as $o) {
                $text = lang_field($o, false, 400);
                if (array_filter($text, 'strlen')) $options[] = $text;
            }
            if (count($options) < 2) fail('Write at least two options.', 422, 'validation');
            $answer = (int)($b['answer'] ?? -1);
            if ($answer < 0 || $answer >= count($options)) fail('The correct answer is not valid.', 422, 'validation');
            $hint = lang_field($b['hint'] ?? null, false, 400);
            $params = [difficulty_of($b['difficulty'] ?? 1), json_out(lang_field($b['q'] ?? null, true, 600)), json_out($options), $answer, json_out(lang_field($b['why'] ?? null, false, 600)), array_filter($hint, 'strlen') ? json_out($hint) : null];
            if ($id) {
                db()->prepare('UPDATE ' . t('questions') . ' SET difficulty = ?, q = ?, options = ?, answer = ?, why = ?, hint = ? WHERE id = ?')->execute(array_merge($params, [$id]));
            } else {
                $st = db()->prepare('SELECT COALESCE(MAX(sort),0)+1 FROM ' . t('questions') . ' WHERE lesson_id = ?');
                $st->execute([$lessonId]);
                $sort = (int)$st->fetchColumn();
                db()->prepare('INSERT INTO ' . t('questions') . ' (difficulty, q, options, answer, why, hint, lesson_id, sort) VALUES (?,?,?,?,?,?,?,?)')->execute(array_merge($params, [$lessonId, $sort]));
            }
            send_json(['ok' => true]);

        case 'admin.question.delete':
            admin_writer();
            db()->prepare('DELETE FROM ' . t('questions') . ' WHERE id = ?')->execute([(int)(body()['id'] ?? 0)]);
            send_json(['ok' => true]);

        case 'admin.drill.save':
            admin_writer();
            $b = body();
            $id = (int)($b['id'] ?? 0);
            $lessonId = (int)($b['lessonId'] ?? 0);
            $st = db()->prepare('SELECT 1 FROM ' . t('lessons') . ' WHERE id = ?');
            $st->execute([$lessonId]);
            if (!$st->fetchColumn()) fail('Not found.', 404, 'not_found');
            $kind = s($b, 'kind', 10);
            $data = is_array($b['data'] ?? null) ? $b['data'] : [];
            unset($data['id'], $data['kind']);
            $data['prompt'] = lang_field($b['prompt'] ?? null, true, 400);
            $data['why'] = lang_field($b['why'] ?? null, true, 600);
            $problem = drill_problem($kind, $data);
            if ($problem) fail($problem, 422, 'validation');
            if ($id) {
                db()->prepare('UPDATE ' . t('drills') . ' SET kind = ?, data = ? WHERE id = ?')->execute([$kind, json_out($data), $id]);
            } else {
                $st = db()->prepare('SELECT COALESCE(MAX(sort),0)+1 FROM ' . t('drills') . ' WHERE lesson_id = ?');
                $st->execute([$lessonId]);
                db()->prepare('INSERT INTO ' . t('drills') . ' (lesson_id, sort, kind, data) VALUES (?,?,?,?)')->execute([$lessonId, (int)$st->fetchColumn(), $kind, json_out($data)]);
            }
            send_json(['ok' => true]);

        case 'admin.drill.delete':
            admin_writer();
            db()->prepare('DELETE FROM ' . t('drills') . ' WHERE id = ?')->execute([(int)(body()['id'] ?? 0)]);
            send_json(['ok' => true]);

        case 'admin.games':
            admin_reader();
            $counts = array_column(db()->query('SELECT game, COUNT(*) AS n FROM ' . t('game_items') . ' GROUP BY game')->fetchAll(), 'n', 'game');
            $played = array_column(db()->query('SELECT game, COUNT(*) AS n FROM ' . t('game_results') . ' GROUP BY game')->fetchAll(), 'n', 'game');
            send_json(['ok' => true, 'games' => array_map(fn($g) => $g + ['items' => (int)($counts[$g['id']] ?? 0), 'played' => (int)($played[$g['id']] ?? 0)], games_raw(true))]);

        case 'admin.game':
            admin_reader();
            $st = db()->prepare('SELECT * FROM ' . t('game_items') . ' WHERE game = ? ORDER BY level, id');
            $st->execute([(string)($_GET['id'] ?? '')]);
            send_json(['ok' => true, 'items' => array_map(fn($i) => ['id' => (int)$i['id'], 'level' => (int)$i['level'], 'status' => $i['status'], 'data' => js($i['data'])], $st->fetchAll())]);

        case 'admin.game.save':
            admin_writer();
            $b = body();
            $id = (string)($b['id'] ?? '');
            $st = db()->prepare('SELECT data FROM ' . t('games') . ' WHERE id = ?');
            $st->execute([$id]);
            $data = js($st->fetchColumn() ?: null);
            if (!$data) fail('Game not found.', 404, 'not_found');
            $data['title'] = lang_field($b['title'] ?? null, true, 80);
            $data['desc'] = lang_field($b['desc'] ?? null, false, 300);
            db()->prepare('UPDATE ' . t('games') . ' SET enabled = ?, levels = ?, data = ? WHERE id = ?')->execute([empty($b['enabled']) ? 0 : 1, max(1, min(30, (int)($b['levels'] ?? 6))), json_out($data), $id]);
            send_json(['ok' => true]);

        case 'admin.item.save':
            admin_writer();
            $b = body();
            $game = (string)($b['game'] ?? '');
            if (!in_array($game, ['whotag', 'selectors', 'linehop'], true)) fail('Game not found.', 404, 'not_found');
            $data = json_out(clean_item($game, $b['data'] ?? []));
            $level = max(1, min(30, (int)($b['level'] ?? 1)));
            $status = status_of($b['status'] ?? 'published');
            if (!empty($b['id'])) db()->prepare('UPDATE ' . t('game_items') . ' SET level = ?, status = ?, data = ? WHERE id = ? AND game = ?')->execute([$level, $status, $data, (int)$b['id'], $game]);
            else db()->prepare('INSERT INTO ' . t('game_items') . ' (level, status, data, game) VALUES (?,?,?,?)')->execute([$level, $status, $data, $game]);
            send_json(['ok' => true]);

        case 'admin.item.delete':
            admin_writer();
            db()->prepare('DELETE FROM ' . t('game_items') . ' WHERE id = ?')->execute([(int)(body()['id'] ?? 0)]);
            send_json(['ok' => true]);

        case 'admin.users':
            $me = admin_reader();
            $q = trim((string)($_GET['q'] ?? ''));
            $sql = 'SELECT u.id, u.name, u.username, u.email, u.email_verified_at, u.role, u.status, u.xp, u.points, u.streak, u.created_at, u.last_seen_at, (SELECT COUNT(*) FROM ' . t('lesson_progress') . ' lp WHERE lp.user_id = u.id) AS lessons,
                (SELECT COUNT(*) FROM ' . t('certificates') . ' c WHERE c.user_id = u.id) AS certificates FROM ' . t('users') . ' u';
            $params = [];
            if ($q !== '') {
                $sql .= ' WHERE u.name LIKE ? OR u.email LIKE ? OR u.username LIKE ?';
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $params = [$like, $like, $like];
            }
            $st = db()->prepare($sql . ' ORDER BY u.id DESC LIMIT 100');
            $st->execute($params);
            send_json(['ok' => true, 'users' => $st->fetchAll(), 'me' => (int)$me['id'], 'isMaster' => (int)$me['role'] >= ROLE_MASTER]);

        case 'admin.user.save':
            $me = admin_writer();
            $b = body();
            $id = (int)($b['id'] ?? 0);
            $target = user_row($id);
            if (!$target) fail('Not found.', 404, 'not_found');
            $self = $id === (int)$me['id'];
            // XP and Sparks: every change is written down and shows in the activity of the user
            $values = ['xp' => max(0, min(10000000, (int)($b['xp'] ?? $target['xp']))), 'points' => max(0, min(1000000, (int)($b['points'] ?? $target['points'])))];
            $log = db()->prepare('INSERT INTO ' . t('point_changes') . ' (user_id, admin_id, field, before_value, after_value, created_at) VALUES (?,?,?,?,?,?)');
            foreach ($values as $field => $v) if ($v !== (int)$target[$field]) $log->execute([$id, (int)$me['id'], $field, (int)$target[$field], $v, now_utc()]);
            if ($self) {
                // On your own account only the points: role and status stay as they are
                db()->prepare('UPDATE ' . t('users') . ' SET xp = ?, points = ? WHERE id = ?')->execute([$values['xp'], $values['points'], $id]);
                send_json(['ok' => true]);
            }
            $isMaster = (int)$me['role'] >= ROLE_MASTER;
            $role = in_array((int)($b['role'] ?? 0), [0, ROLE_ADMIN], true) ? (int)$b['role'] : 0;
            if (!$isMaster && ($role !== (int)$target['role'] || (int)$target['role'] >= ROLE_ADMIN)) fail('Only the master admin can change roles.', 403, 'forbidden');
            $status = ($b['status'] ?? '') === 'disabled' ? 'disabled' : 'active';
            db()->prepare('UPDATE ' . t('users') . ' SET role = ?, status = ?, xp = ?, points = ? WHERE id = ?')->execute([$role, $status, $values['xp'], $values['points'], $id]);
            if ($status === 'disabled') db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$id]);
            send_json(['ok' => true]);

        case 'admin.user.activity':
            // Read only: what the user did, newest first, with date and time (UTC; the portal shows local time)
            admin_reader();
            $id = (int)($_GET['id'] ?? 0);
            if (!user_row($id)) fail('Not found.', 404, 'not_found');
            $parts = [
                "SELECT 'lesson' AS kind, completed_at AS at, course AS a, lesson AS b, stars AS n, skipped AS m FROM " . t('lesson_progress') . ' WHERE user_id = :u',
                "SELECT 'drill', d.done_at, l.course, l.slug, d.stars, NULL FROM " . t('drill_done') . ' d JOIN ' . t('drills') . ' x ON x.id = d.drill_id JOIN ' . t('lessons') . ' l ON l.id = x.lesson_id WHERE d.user_id = :u',
                "SELECT 'game', created_at, game, NULL, stars, won FROM " . t('game_results') . ' WHERE user_id = :u',
                "SELECT 'daily', done_at, NULL, NULL, correct, total FROM " . t('daily_results') . ' WHERE user_id = :u AND done_at IS NOT NULL',
                "SELECT 'exam', submitted_at, course, NULL, score, NULL FROM " . t('exams') . ' WHERE user_id = :u AND submitted_at IS NOT NULL',
                "SELECT 'certificate', issued_at, course, code, score, NULL FROM " . t('certificates') . ' WHERE user_id = :u',
                "SELECT 'badge', earned_at, badge, NULL, NULL, NULL FROM " . t('badges') . ' WHERE user_id = :u',
                "SELECT 'hint', created_at, ref, NULL, cost, NULL FROM " . t('hints') . ' WHERE user_id = :u',
                "SELECT 'prize', CONCAT(day, ' 23:59:59'), NULL, NULL, points, place FROM " . t('board_prizes') . ' WHERE user_id = :u',
                "SELECT 'points', created_at, field, NULL, before_value, after_value FROM " . t('point_changes') . ' WHERE user_id = :u',
                "SELECT 'avatar', bought_at, item_id, NULL, price, NULL FROM " . t('user_items') . ' WHERE user_id = :u',
            ];
            $sql = implode(' UNION ALL ', array_map(fn($q, $i) => str_replace(':u', ':u' . $i, $q), $parts, array_keys($parts))) . ' ORDER BY at DESC LIMIT 300';
            $st = db()->prepare($sql);
            foreach (array_keys($parts) as $i) $st->bindValue(':u' . $i, $id, PDO::PARAM_INT);
            $st->execute();
            // Games and courses by name, in the language of the portal
            $names = [];
            foreach (db()->query('SELECT id, data FROM ' . t('games'))->fetchAll() as $g) $names['game:' . $g['id']] = pick(json_decode($g['data'], true)['title'] ?? $g['id']);
            foreach (db()->query('SELECT id, data FROM ' . t('courses'))->fetchAll() as $c) $names['course:' . $c['id']] = pick(json_decode($c['data'], true)['title'] ?? $c['id']);
            $label = fn($r) => match ($r['kind']) {
                'game' => $names['game:' . $r['a']] ?? $r['a'],
                'lesson', 'drill', 'exam', 'certificate' => $names['course:' . $r['a']] ?? $r['a'],
                default => $r['a'],
            };
            $events = array_map(fn($r) => ['kind' => $r['kind'], 'at' => str_replace(' ', 'T', $r['at']) . 'Z', 'a' => $label($r), 'b' => $r['b'], 'n' => $r['n'] === null ? null : (int)$r['n'], 'm' => $r['m'] === null ? null : (int)$r['m']], $st->fetchAll());
            $st = db()->prepare('SELECT day, xp FROM ' . t('activity') . ' WHERE user_id = ? ORDER BY day DESC LIMIT 60');
            $st->execute([$id]);
            send_json(['ok' => true, 'events' => $events, 'days' => $st->fetchAll()]);

        case 'admin.user.delete':
            $me = admin_writer();
            $id = (int)(body()['id'] ?? 0);
            if ($id === (int)$me['id']) fail('You cannot change your own account here.', 403, 'self');
            $target = user_row($id);
            if (!$target) fail('Not found.', 404, 'not_found');
            if ((int)$target['role'] >= ROLE_ADMIN && (int)$me['role'] < ROLE_MASTER) fail('Only the master admin can change roles.', 403, 'forbidden');
            db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$id]);
            send_json(['ok' => true]);

        case 'admin.avatars':
            admin_reader();
            $rows = db()->query('SELECT i.id, i.kind, i.price, i.enabled, (SELECT COUNT(*) FROM ' . t('user_items') . ' u WHERE u.item_id = i.id) AS sold FROM ' . t('avatar_items') . ' i ORDER BY i.kind DESC, i.price, i.sort')->fetchAll();
            send_json(['ok' => true, 'items' => array_map(fn($r) => ['id' => $r['id'], 'kind' => $r['kind'], 'price' => (int)$r['price'], 'enabled' => (int)$r['enabled'], 'sold' => (int)$r['sold']], $rows)]);

        case 'admin.avatar.save':
            admin_writer();
            $b = body();
            $st = db()->prepare('UPDATE ' . t('avatar_items') . ' SET price = ?, enabled = ? WHERE id = ?');
            $st->execute([max(0, min(100000, (int)($b['price'] ?? 0))), empty($b['enabled']) ? 0 : 1, (string)($b['id'] ?? '')]);
            send_json(['ok' => true]);

        case 'admin.certificates':
            admin_reader();
            $rows = db()->query('SELECT c.code, c.course, c.name, c.score, c.issued_at, u.email FROM ' . t('certificates') . ' c JOIN ' . t('users') . ' u ON u.id = c.user_id ORDER BY c.issued_at DESC LIMIT 200')->fetchAll();
            send_json(['ok' => true, 'certificates' => $rows]);

        case 'admin.certificate.rename':
            admin_writer();
            $b = body();
            $name = s($b, 'name', 80, true, 'Name');
            if (mb_strlen($name) < 2) fail('Enter your name.', 422, 'validation');
            db()->prepare('UPDATE ' . t('certificates') . ' SET name = ? WHERE code = ?')->execute([$name, s($b, 'code', 12)]);
            send_json(['ok' => true]);

        case 'admin.certificate.delete':
            admin_writer();
            db()->prepare('DELETE FROM ' . t('certificates') . ' WHERE code = ?')->execute([(string)(body()['code'] ?? '')]);
            send_json(['ok' => true]);

        case 'admin.settings':
            admin_reader();
            $out = [];
            foreach (DEFAULT_SETTINGS as $k => $default) $out[$k] = setting($k);
            send_json(['ok' => true, 'settings' => $out, 'defaults' => DEFAULT_SETTINGS]);

        case 'admin.settings.save':
            admin_writer();
            $values = body()['settings'] ?? [];
            $st = db()->prepare('INSERT INTO ' . t('settings') . ' (k, v) VALUES (?,?) ON DUPLICATE KEY UPDATE v = VALUES(v)');
            foreach (DEFAULT_SETTINGS as $k => $default) if (isset($values[$k])) $st->execute([$k, (string)max(0, min(100000, (int)$values[$k]))]);
            send_json(['ok' => true]);
    }
}
