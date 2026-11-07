<?php
/**
 * Fucina — JSON API.  api/index.php?r=<action>
 *
 * Public:   auth.config, auth.me, auth.register, auth.login, auth.google, auth.logout
 * Account:  state, auth.profile, auth.password, auth.delete
 * Ranking:  board.get, board.seen, board.join
 * Friends:  auth.username, friends.list, friends.search, friends.add, friends.remove
 * Learning: course.get, quiz.check, hint.buy, lesson.complete, drill.finish, drill.mix, exam.start, exam.submit, certificate.rename
 * Playing:  daily.get, daily.submit, game.get, game.finish
 * Portal:   admin.* (see lib/admin-api.php)
 *
 * Every POST needs the X-CSRF-Token header (given at sign-in). XP, Sparks, stars and certificates are decided here:
 * quiz answers reach the browser only after answering, and the XP of the games (the only result reported by the
 * browser) is limited per game and per day.
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/mail.php';
require __DIR__ . '/../lib/courses.php';
require __DIR__ . '/../lib/progress.php';

header('X-Robots-Tag: noindex');
set_exception_handler(function (Throwable $e) {
    if ($e->getMessage() === 'not_installed') fail('Fucina is not installed yet: open install-db.php.', 503, 'not_installed');
    error_log('Fucina API: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    $debug = (bool)config_value('debug', false);
    send_json(['ok' => false, 'error' => 'server', 'message' => tr('Internal server error.') . ($debug ? ' ' . $e->getMessage() : '')], 500);
});

if (!credentials()) fail('Fucina is not installed yet: open install-db.php.', 503, 'not_installed');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && stripos((string)($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json') !== 0) fail('Unsupported Content-Type.', 415, 'content_type');

$r = (string)($_GET['r'] ?? '');

function only(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) fail('Method not allowed.', 405, 'method');
}

function reader(): array
{
    only('GET');
    return require_user();
}

function writer(): array
{
    only('POST');
    $u = require_user();
    require_csrf($u);
    return $u;
}

// ---------------------------------------------------------------- helpers

function user_row(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function validate_password(string $pw): void
{
    if (mb_strlen($pw) < 8) fail('The password must have at least 8 characters.', 422, 'validation');
    if (strlen($pw) > 200) fail('The password is too long.', 422, 'validation');
}

function new_user(string $name, string $email, string $hash, ?string $googleSub): int
{
    db()->prepare('INSERT INTO ' . t('users') . ' (name, email, password_hash, google_sub, lang, created_at, last_login_at) VALUES (?,?,?,?,?,?,?)')
        ->execute([$name, $email, $hash, $googleSub, lang(), now_utc(), now_utc()]);
    return (int)db()->lastInsertId();
}

function course_or_404(string $id): array
{
    $c = course_raw($id);
    if (!$c) fail('Course not found.', 404, 'not_found');
    return $c;
}

/** The state after an action that may have earned something, with what is new to celebrate. */
function reward(array $u, int $xp, int $levelBefore, array $extra = []): void
{
    $badges = award_badges((int)$u['id']);
    $state = state_payload($u);
    send_json(['ok' => true, 'xp' => $xp, 'newBadges' => $badges, 'levelUp' => $state['level']['level'] > $levelBefore ? $state['level'] : null] + $extra + $state);
}

/** Can the learner reach this lesson: it is done, or it is the first one not done of an open course? */
function lesson_open(int $uid, array $c, string $slug): bool
{
    if (course_locked($uid, $c['id'])) return false;
    $mine = lesson_rows($uid)[$c['id']] ?? [];
    foreach ($c['lessons'] as $l) {
        if ($l['id'] === $slug) return true;
        if (!isset($mine[$l['id']])) return isset($mine[$slug]);
    }
    return false;
}

/** Games or drills in a row without mistakes, for the badges. $kind is "win" or "drill". */
function record_streak(int $uid, string $kind, bool $clean): void
{
    $now = $kind . '_streak';
    $best = 'best_' . $now;
    if ($clean) db()->prepare('UPDATE ' . t('users') . " SET `$best` = GREATEST(`$best`, `$now` + 1), `$now` = `$now` + 1 WHERE id = ?")->execute([$uid]);
    else db()->prepare('UPDATE ' . t('users') . " SET `$now` = 0 WHERE id = ?")->execute([$uid]);
}

/** A username: 3 to 20 lowercase letters, digits, dots or underscores. It is the only public data of an account. */
function clean_username($v): string
{
    $name = mb_strtolower(trim((string)$v));
    if (!preg_match('/^[a-z0-9_.]{3,20}$/', $name)) fail('Use 3 to 20 letters, numbers, dots or underscores.', 422, 'validation');
    if (in_array($name, ['admin', 'administrator', 'fucina', 'root', 'staff', 'support', 'moderator'], true)) fail('This username is already taken.', 409, 'username_taken');
    return $name;
}

function level_of(int $uid): int
{
    return level_info((int)user_row($uid)['xp'])['level'];
}

/** Today's questions: the same for everyone, drawn from all the courses with the date as seed. */
function daily_questions(string $day): array
{
    $pool = all_questions();
    $keys = array_keys($pool);
    sort($keys);
    mt_srand(crc32('fucina' . $day));
    for ($i = count($keys) - 1; $i > 0; $i--) {
        $j = mt_rand(0, $i);
        [$keys[$i], $keys[$j]] = [$keys[$j], $keys[$i]];
    }
    mt_srand();
    return array_map(fn($k) => ['key' => $k] + $pool[$k], array_slice($keys, 0, 5));
}

function unique_certificate_code(): string
{
    for ($i = 0; $i < 20; $i++) {
        $c = random_code(12);
        $st = db()->prepare('SELECT 1 FROM ' . t('certificates') . ' WHERE code = ?');
        $st->execute([$c]);
        if (!$st->fetchColumn()) return $c;
    }
    throw new RuntimeException('certificate code');
}

// ---------------------------------------------------------------- routes

switch ($r) {

    // ======================================================== authentication

    case 'auth.config':
        send_json(['ok' => true, 'googleClientId' => (string)config_value('google_client_id', '')]);

    case 'auth.me':
        only('GET');
        $u = current_user();
        if (!$u || $u['status'] !== 'active') send_json(['ok' => true, 'user' => null]);
        send_json(['ok' => true, 'user' => public_user(user_row((int)$u['id'])), 'csrf' => $u['csrf_token']]);

    case 'auth.register':
        only('POST');
        if (too_many_attempts('register', 5, 60)) fail('Too many sign-ups from this network: try again later.', 429, 'rate_limited');
        $b = body();
        $name = s($b, 'name', 80, true, 'Name');
        $email = mb_strtolower(s($b, 'email', 190, true, 'Email'));
        $pw = (string)($b['password'] ?? '');
        if (mb_strlen($name) < 2) fail('Enter your name.', 422, 'validation');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.', 422, 'validation');
        validate_password($pw);
        if (empty($b['acceptPrivacy'])) fail('To create the account you must accept the privacy policy.', 422, 'validation');
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetchColumn()) fail('An account with this email already exists: sign in.', 409, 'email_taken');
        record_attempt('register');
        $uid = new_user($name, $email, password_hash($pw, PASSWORD_DEFAULT), null);
        mail_account_created(user_row($uid), 'email');
        $csrf = create_auth_session($uid);
        send_json(['ok' => true, 'user' => public_user(user_row($uid)), 'csrf' => $csrf]);

    case 'auth.login':
        only('POST');
        if (too_many_attempts('login', 10, 15)) fail('Too many attempts: try again in a few minutes.', 429, 'rate_limited');
        $b = body();
        $email = mb_strtolower(s($b, 'email', 190));
        $pw = (string)($b['password'] ?? '');
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        if (!$row || $row['password_hash'] === '' || !password_verify($pw, $row['password_hash'])) {
            record_attempt('login');
            if ($row && $row['password_hash'] === '' && $row['google_sub']) fail('This account uses Google: sign in with “Continue with Google”.', 401, 'use_google');
            fail('Wrong email or password.', 401, 'bad_credentials');
        }
        if ($row['status'] !== 'active') fail('Your account has been disabled.', 403, 'disabled');
        clear_attempts('login');
        if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $row['id']]);
        db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        $csrf = create_auth_session((int)$row['id']);
        send_json(['ok' => true, 'user' => public_user(user_row((int)$row['id'])), 'csrf' => $csrf]);

    case 'auth.google':
        // Sign in (or sign up) with Google. A new account needs the privacy consent: the first call answers
        // "needs_signup" and the app asks for it, then calls again.
        only('POST');
        if (too_many_attempts('login', 20, 15)) fail('Too many attempts: try again in a few minutes.', 429, 'rate_limited');
        $b = body();
        $g = verify_google_token((string)($b['credential'] ?? ''));
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE google_sub = ?');
        $st->execute([$g['sub']]);
        $row = $st->fetch();
        if (!$row) {
            $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
            $st->execute([$g['email']]);
            $row = $st->fetch();
            if ($row) db()->prepare('UPDATE ' . t('users') . ' SET google_sub = ? WHERE id = ?')->execute([$g['sub'], $row['id']]);
        }
        if (!$row) {
            if (empty($b['acceptPrivacy'])) send_json(['ok' => false, 'error' => 'needs_signup', 'message' => tr('Create your Fucina account'), 'name' => $g['name'], 'email' => $g['email']], 200);
            if (too_many_attempts('register', 5, 60)) fail('Too many sign-ups from this network: try again later.', 429, 'rate_limited');
            record_attempt('register');
            $uid = new_user($g['name'] ?: explode('@', $g['email'])[0], $g['email'], '', $g['sub']);
            $row = user_row($uid);
            mail_account_created($row, 'google');
        }
        if ($row['status'] !== 'active') fail('Your account has been disabled.', 403, 'disabled');
        db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        $csrf = create_auth_session((int)$row['id']);
        send_json(['ok' => true, 'user' => public_user(user_row((int)$row['id'])), 'csrf' => $csrf]);

    case 'auth.logout':
        only('POST');
        $u = current_user();
        if ($u) require_csrf($u);
        destroy_auth_session();
        send_json(['ok' => true]);

    case 'auth.profile':
        $u = writer();
        $b = body();
        $name = s($b, 'name', 80, true, 'Name');
        $email = mb_strtolower(s($b, 'email', 190, true, 'Email'));
        if (mb_strlen($name) < 2) fail('Enter your name.', 422, 'validation');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.', 422, 'validation');
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE email = ? AND id <> ?');
        $st->execute([$email, $u['id']]);
        if ($st->fetchColumn()) fail('This email is already used by another account.', 409, 'email_taken');
        db()->prepare('UPDATE ' . t('users') . ' SET name = ?, email = ? WHERE id = ?')->execute([$name, $email, $u['id']]);
        send_json(['ok' => true] + state_payload($u));

    case 'auth.password':
        $u = writer();
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['current'] ?? ''), $row['password_hash'])) fail('The current password is not correct.', 422, 'bad_password');
        $pw = (string)($b['password'] ?? '');
        validate_password($pw);
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
        send_json(['ok' => true]);

    case 'auth.delete':
        $u = writer();
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['password'] ?? ''), $row['password_hash'])) fail('The password is not correct.', 422, 'bad_password');
        db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$u['id']]); // progress and certificates follow (ON DELETE CASCADE)
        set_auth_cookie('', time() - 3600);
        send_json(['ok' => true]);

    // ======================================================== state

    case 'state':
        $u = reader();
        send_json(['ok' => true] + state_payload($u));

    // ======================================================== learning

    case 'course.get':
        $u = reader();
        $c = course_or_404((string)($_GET['id'] ?? ''));
        $mine = lesson_rows((int)$u['id'])[$c['id']] ?? [];
        $drillsDone = drill_rows((int)$u['id']);
        $lessons = [];
        foreach ($c['lessons'] as $l) {
            $task = deep_pick($l['task']);
            if ($task) unset($task['hint']); // a paid hint: see hint.buy
            $lessons[] = [
                'id' => $l['id'], 'title' => pick($l['title']), 'difficulty' => $l['difficulty'], 'body' => deep_pick($l['body']), 'task' => $task,
                'quiz' => array_map('question_out', $l['quiz']), 'drills' => array_map(fn($d) => drill_out($d, $drillsDone), $l['drills']), 'progress' => $mine[$l['id']] ?? null,
            ];
        }
        $summary = array_values(array_filter(courses_summary((int)$u['id']), fn($x) => $x['id'] === $c['id']))[0];
        send_json(['ok' => true, 'course' => $summary + ['items' => $lessons, 'examQuestions' => min(setting('exam_questions'), count(course_questions($c))), 'examPass' => setting('exam_pass_percent')]]);

    case 'quiz.check':
        // The answer to a single question, revealed once the learner has answered
        $u = writer();
        $b = body();
        $c = course_or_404(s($b, 'course', 30));
        $q = course_questions($c)[(string)($b['question'] ?? '')] ?? null;
        if (!$q) fail('Lesson not found.', 404, 'not_found');
        send_json(['ok' => true, 'correct' => (int)($b['answer'] ?? -1) === $q['a'], 'answer' => $q['a'], 'why' => pick($q['why'])]);

    case 'hint.buy':
        // Spends Sparks. kind: question (removes wrong options), task (shows a solution), game (the game gives its own help)
        $u = writer();
        $uid = (int)$u['id'];
        $b = body();
        $kind = s($b, 'kind', 10);
        $out = [];
        if ($kind === 'question' || $kind === 'task') {
            $c = course_or_404(s($b, 'course', 30));
            if ($kind === 'question') {
                $q = course_questions($c)[(string)($b['question'] ?? '')] ?? null;
                if (!$q) fail('Lesson not found.', 404, 'not_found');
                $ref = 'q:' . $q['id'];
                $cost = setting('hint_question');
                // Removes half of the wrong options, always the same ones for this question
                $wrong = array_values(array_diff(array_keys($q['options']), [$q['a']]));
                $out = ['remove' => array_slice($wrong, 0, (int)ceil(count($wrong) / 2)), 'text' => pick($q['hint'])];
            } else {
                $l = array_values(array_filter($c['lessons'], fn($x) => $x['id'] === (string)($b['lesson'] ?? '')))[0] ?? null;
                if (!$l || !$l['task']) fail('Lesson not found.', 404, 'not_found');
                $ref = 't:' . $l['dbId'];
                $cost = setting('hint_task');
                $out = ['code' => (string)($l['task']['hint'] ?? '')];
            }
        } elseif ($kind === 'game') {
            $game = s($b, 'game', 20);
            if (!in_array($game, array_column(games_raw(), 'id'), true)) fail('Game not found.', 404, 'not_found');
            $ref = 'g:' . $game . ':' . bin2hex(random_bytes(6)); // every help in a game is a new purchase
            $cost = setting('hint_game');
        } elseif ($kind === 'drill') {
            $st = db()->prepare('SELECT id FROM ' . t('drills') . ' WHERE id = ?');
            $st->execute([(int)($b['drill'] ?? 0)]);
            $drillId = $st->fetchColumn();
            if (!$drillId) fail('Lesson not found.', 404, 'not_found');
            $ref = 'd:' . $drillId . ':' . bin2hex(random_bytes(6)); // every hint in a drill is a new purchase
            $cost = setting('hint_drill');
        } else {
            fail('Invalid request.', 400, 'bad_kind');
        }
        $st = db()->prepare('SELECT 1 FROM ' . t('hints') . ' WHERE user_id = ? AND ref = ?');
        $st->execute([$uid, $ref]);
        if (!$st->fetchColumn()) {
            // One statement: two requests at once cannot spend the same Sparks
            $st = db()->prepare('UPDATE ' . t('users') . ' SET points = points - ? WHERE id = ? AND points >= ?');
            $st->execute([$cost, $uid, $cost]);
            if ($st->rowCount() === 0) fail('Not enough Sparks: earn more with lessons and games.', 402, 'no_points');
            db()->prepare('INSERT INTO ' . t('hints') . ' (user_id, ref, cost, created_at) VALUES (?,?,?,?)')->execute([$uid, $ref, $cost, now_utc()]);
        }
        send_json(['ok' => true, 'hint' => $out, 'points' => (int)user_row($uid)['points']]);

    case 'lesson.complete':
        $u = writer();
        $uid = (int)$u['id'];
        $b = body();
        $c = course_or_404(s($b, 'course', 30));
        $lessonId = s($b, 'lesson', 40);
        $index = array_search($lessonId, array_column($c['lessons'], 'id'), true);
        if ($index === false) fail('Lesson not found.', 404, 'not_found');
        if (course_locked($uid, $c['id'])) fail('Complete the previous lessons first.', 403, 'locked');
        $lesson = $c['lessons'][$index];
        $mine = lesson_rows($uid)[$c['id']] ?? [];
        // Lessons follow one another: the previous one must be done
        if ($index > 0 && !isset($mine[$c['lessons'][$index - 1]['id']])) fail('Complete the previous lessons first.', 403, 'locked');

        // "answers" holds the first answer given to each question: it decides the stars
        $answers = is_array($b['answers'] ?? null) ? $b['answers'] : [];
        $total = count($lesson['quiz']);
        $correct = 0;
        foreach ($lesson['quiz'] as $q) if (isset($answers[$q['id']]) && (int)$answers[$q['id']] === $q['a']) $correct++;
        $stars = $correct === $total ? 3 : ($correct * 3 >= $total * 2 ? 2 : 1);
        $taskDone = !empty($b['taskDone']) && $lesson['task'];

        $before = $mine[$lessonId] ?? null;
        $levelBefore = level_of($uid);
        $xp = 0;
        if (!$before) $xp += setting('xp_lesson_base') * $lesson['difficulty'] + $stars * setting('xp_lesson_star'); // harder levels pay more
        elseif ($stars > $before['stars']) $xp += ($stars - $before['stars']) * setting('xp_lesson_star'); // a replay only pays what it improves
        if ($taskDone && !($before['taskDone'] ?? false)) $xp += setting('xp_task');

        db()->prepare('INSERT INTO ' . t('lesson_progress') . ' (user_id, course, lesson, stars, task_done, completed_at) VALUES (?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE stars = GREATEST(stars, VALUES(stars)), task_done = GREATEST(task_done, VALUES(task_done))')
            ->execute([$uid, $c['id'], $lessonId, $stars, $taskDone ? 1 : 0, now_utc()]);
        add_xp($uid, $xp, $before ? 0 : 1);
        reward($u, $xp, $levelBefore, ['stars' => $stars, 'correct' => $correct, 'total' => $total]);

    case 'drill.finish':
        // Solved in the browser, like a game: it pays once, and a replay only pays what it improves
        $u = writer();
        $uid = (int)$u['id'];
        $b = body();
        $st = db()->prepare('SELECT d.id, l.course, l.slug FROM ' . t('drills') . ' d JOIN ' . t('lessons') . ' l ON l.id = d.lesson_id WHERE d.id = ?');
        $st->execute([(int)($b['drill'] ?? 0)]);
        $drill = $st->fetch();
        if (!$drill) fail('Lesson not found.', 404, 'not_found');
        $c = course_or_404($drill['course']);
        if (!lesson_open($uid, $c, $drill['slug'])) fail('Complete the previous lessons first.', 403, 'locked');
        $stars = drill_stars(max(0, (int)($b['mistakes'] ?? 0)));
        $id = (int)$drill['id'];
        $before = drill_rows($uid)[$id] ?? 0;
        $levelBefore = level_of($uid);
        $xp = 0;
        if (!$before) $xp += setting('xp_drill');
        if ($stars === 3 && $before < 3) $xp += setting('xp_drill_perfect');
        db()->prepare('INSERT INTO ' . t('drill_done') . ' (user_id, drill_id, stars, done_at) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE stars = GREATEST(stars, VALUES(stars))')
            ->execute([$uid, $id, $stars, now_utc()]);
        add_xp($uid, $xp);
        record_streak($uid, 'drill', (int)($b['mistakes'] ?? 0) === 0);
        reward($u, $xp, $levelBefore, ['stars' => $stars]);

    case 'drill.mix':
        // A quick workout: drills of the levels the learner can reach, the ones not solved yet first
        $u = reader();
        $uid = (int)$u['id'];
        $done = drill_rows($uid);
        $pool = [];
        foreach (course_ids() as $courseId) {
            $c = course_raw($courseId);
            foreach ($c['lessons'] as $l) {
                if (!$l['drills'] || !lesson_open($uid, $c, $l['id'])) continue;
                foreach ($l['drills'] as $d) $pool[] = drill_out($d, $done) + ['from' => pick($l['title'])];
            }
        }
        shuffle($pool);
        usort($pool, fn($a, $b) => ($a['stars'] > 0) <=> ($b['stars'] > 0));
        send_json(['ok' => true, 'drills' => array_slice($pool, 0, 6)]);

    case 'exam.start':
        $u = writer();
        $uid = (int)$u['id'];
        $b = body();
        $c = course_or_404(s($b, 'course', 30));
        $mine = lesson_rows($uid)[$c['id']] ?? [];
        if (!$c['lessons']) fail('Complete all the lessons of the course first.', 403, 'locked');
        foreach ($c['lessons'] as $l) if (!isset($mine[$l['id']])) fail('Complete all the lessons of the course first.', 403, 'locked');
        $pool = course_questions($c);
        $ids = array_keys($pool);
        shuffle($ids);
        $ids = array_map('strval', array_slice($ids, 0, setting('exam_questions')));
        db()->prepare('INSERT INTO ' . t('exams') . ' (user_id, course, questions, started_at) VALUES (?,?,?,?)')->execute([$uid, $c['id'], json_encode($ids), now_utc()]);
        $examId = (int)db()->lastInsertId();
        send_json(['ok' => true, 'exam' => $examId, 'pass' => setting('exam_pass_percent'), 'questions' => array_map(fn($id) => question_out($pool[$id]), $ids)]);

    case 'exam.submit':
        $u = writer();
        $uid = (int)$u['id'];
        $b = body();
        $st = db()->prepare('SELECT * FROM ' . t('exams') . ' WHERE id = ? AND user_id = ? AND submitted_at IS NULL');
        $st->execute([(int)($b['exam'] ?? 0), $uid]);
        $exam = $st->fetch();
        if (!$exam) fail('Exam not found: start it again.', 404, 'not_found');
        $c = course_or_404($exam['course']);
        $pool = course_questions($c);
        $ids = json_decode($exam['questions'], true);
        $answers = is_array($b['answers'] ?? null) ? $b['answers'] : [];
        $correct = 0;
        $review = [];
        foreach ($ids as $id) {
            $ok = isset($answers[$id]) && (int)$answers[$id] === $pool[$id]['a'];
            if ($ok) $correct++;
            else $review[] = ['q' => pick($pool[$id]['q']), 'answer' => pick($pool[$id]['options'][$pool[$id]['a']]), 'why' => pick($pool[$id]['why'])];
        }
        $score = (int)round($correct * 100 / max(1, count($ids)));
        $passed = $score >= setting('exam_pass_percent');
        $st = db()->prepare('SELECT 1 FROM ' . t('exams') . ' WHERE user_id = ? AND course = ? AND score >= ? LIMIT 1');
        $st->execute([$uid, $c['id'], setting('exam_pass_percent')]);
        $passedBefore = (bool)$st->fetchColumn();
        db()->prepare('UPDATE ' . t('exams') . ' SET score = ?, submitted_at = ? WHERE id = ?')->execute([$score, now_utc(), $exam['id']]);

        $levelBefore = level_of($uid);
        $xp = 0;
        $cert = user_certificates($uid)[$c['id']] ?? null;
        // The first pass pays XP; the certificate itself is then unlocked with Sparks: see certificate.claim
        if ($passed && !$cert && !$passedBefore) {
            $xp = setting('xp_certificate');
            add_xp($uid, $xp);
        } elseif ($passed && $cert && $score > $cert['score']) {
            // A better result improves the certificate, without changing its link
            db()->prepare('UPDATE ' . t('certificates') . ' SET score = ? WHERE code = ?')->execute([$score, $cert['code']]);
        }
        $cert = user_certificates($uid)[$c['id']] ?? null;
        reward($u, $xp, $levelBefore, ['score' => $score, 'passed' => $passed, 'correct' => $correct, 'total' => count($ids), 'review' => $review, 'certificate' => $cert]);

    case 'certificate.claim':
        // A passed exam opens the certificate; the Sparks earned along the way unlock it
        $u = writer();
        $uid = (int)$u['id'];
        $c = course_or_404(s(body(), 'course', 30));
        $st = db()->prepare('SELECT MAX(score) FROM ' . t('exams') . ' WHERE user_id = ? AND course = ? AND score >= ?');
        $st->execute([$uid, $c['id'], setting('exam_pass_percent')]);
        $score = (int)$st->fetchColumn();
        if (!$score) fail('Pass the exam first.', 403, 'locked');
        if (user_certificates($uid)[$c['id']] ?? null) fail('You already have this certificate.', 409, 'already');
        $cost = setting('cert_cost');
        // One transaction: the unique key stops a double click, and the Sparks go only if the certificate is created
        db()->beginTransaction();
        try {
            db()->prepare('INSERT INTO ' . t('certificates') . ' (code, user_id, course, name, score, issued_at) VALUES (?,?,?,?,?,?)')
                ->execute([unique_certificate_code(), $uid, $c['id'], user_row($uid)['name'], $score, now_utc()]);
            $st = db()->prepare('UPDATE ' . t('users') . ' SET points = points - ? WHERE id = ? AND points >= ?');
            $st->execute([$cost, $uid, $cost]);
            if ($st->rowCount() === 0) {
                db()->rollBack();
                fail('Not enough Sparks: earn more with lessons and games.', 402, 'no_points');
            }
            db()->commit();
        } catch (PDOException $e) {
            if (db()->inTransaction()) db()->rollBack();
            throw $e;
        }
        reward($u, 0, level_of($uid), ['certificate' => user_certificates($uid)[$c['id']]]);

    case 'certificate.rename':
        // The name printed on a certificate can differ from the account's one (a full name, for example)
        $u = writer();
        $b = body();
        $name = s($b, 'name', 80, true, 'Name');
        if (mb_strlen($name) < 2) fail('Enter your name.', 422, 'validation');
        $st = db()->prepare('UPDATE ' . t('certificates') . ' SET name = ? WHERE code = ? AND user_id = ?');
        $st->execute([$name, s($b, 'code', 12), $u['id']]);
        send_json(['ok' => true] + state_payload($u));

    // ======================================================== playing

    case 'daily.get':
        $u = reader();
        $day = today();
        send_json(['ok' => true, 'day' => $day, 'questions' => array_map(fn($q) => ['key' => $q['key']] + question_out($q), daily_questions($day))]);

    case 'daily.submit':
        $u = writer();
        $uid = (int)$u['id'];
        $day = today();
        $st = db()->prepare('SELECT 1 FROM ' . t('daily_results') . ' WHERE user_id = ? AND day = ?');
        $st->execute([$uid, $day]);
        if ($st->fetchColumn()) fail('Today’s challenge is already done: come back tomorrow.', 409, 'already_done');
        $b = body();
        $answers = is_array($b['answers'] ?? null) ? $b['answers'] : [];
        $questions = daily_questions($day);
        $correct = 0;
        $review = [];
        foreach ($questions as $q) {
            $ok = isset($answers[$q['key']]) && (int)$answers[$q['key']] === $q['a'];
            if ($ok) $correct++;
            $review[] = ['ok' => $ok, 'q' => pick($q['q']), 'answer' => pick($q['options'][$q['a']]), 'why' => pick($q['why'])];
        }
        $total = count($questions);
        db()->prepare('INSERT INTO ' . t('daily_results') . ' (user_id, day, correct, total, done_at) VALUES (?,?,?,?,?)')->execute([$uid, $day, $correct, $total, now_utc()]);
        $levelBefore = level_of($uid);
        $xp = $correct * setting('xp_daily_correct') + ($correct === $total ? setting('xp_daily_perfect') : 0);
        add_xp($uid, $xp);
        reward($u, $xp, $levelBefore, ['correct' => $correct, 'total' => $total, 'review' => $review]);

    case 'game.get':
        // What a level needs: its items and, for the games played with questions, questions up to its difficulty.
        // Questions come with their answers: a game is played for a small, capped reward.
        $u = reader();
        $id = (string)($_GET['id'] ?? '');
        $level = (int)($_GET['level'] ?? 1);
        $g = array_values(array_filter(games_summary((int)$u['id']), fn($x) => $x['id'] === $id))[0] ?? null;
        if (!$g) fail('Game not found.', 404, 'not_found');
        if ($level < 1 || $level > $g['unlocked']) fail('Win the previous level first.', 403, 'locked');
        // A game with content for each level uses that level; cards shared by all levels sit on level 1
        $st = db()->prepare('SELECT level, data FROM ' . t('game_items') . " WHERE game = ? AND status = 'published'");
        $st->execute([$id]);
        $rows = $st->fetchAll();
        $mine = array_values(array_filter($rows, fn($r) => (int)$r['level'] === $level)) ?: array_values(array_filter($rows, fn($r) => (int)$r['level'] === 1));
        $items = array_map(fn($r) => deep_pick(js($r['data'])), $mine);
        $questions = [];
        if ($id === 'fourline') {
            $maxDifficulty = min(3, (int)ceil($level * 3 / max(1, $g['levels'])));
            $pool = array_values(array_filter(all_questions(), fn($q) => $q['difficulty'] <= $maxDifficulty));
            shuffle($pool);
            $questions = array_map(fn($q) => question_out($q, true), array_slice($pool, 0, 30));
        }
        send_json(['ok' => true, 'game' => $g, 'level' => $level, 'items' => $items, 'questions' => $questions]);

    case 'game.finish':
        $u = writer();
        $uid = (int)$u['id'];
        $b = body();
        $game = s($b, 'game', 20);
        $g = array_values(array_filter(games_summary($uid), fn($x) => $x['id'] === $game))[0] ?? null;
        if (!$g) fail('Game not found.', 404, 'not_found');
        $level = (int)($b['level'] ?? 1);
        if ($level < 1 || $level > $g['unlocked']) fail('Win the previous level first.', 403, 'locked');
        $stars = max(0, min(3, (int)($b['stars'] ?? 0)));
        $won = !empty($b['won']);
        $score = max(0, min(100000, (int)($b['score'] ?? 0)));
        $seconds = max(0, min(86400, (int)($b['seconds'] ?? 0)));
        db()->prepare('INSERT INTO ' . t('game_results') . ' (user_id, game, score, stars, won, seconds, created_at) VALUES (?,?,?,?,?,?,?)')
            ->execute([$uid, $game, $score, $stars, $won ? 1 : 0, $seconds, now_utc()]);
        $firstWin = false;
        if ($won) {
            $previous = ((array)$g['won'])[$level] ?? 0;
            $firstWin = !$previous;
            db()->prepare('INSERT INTO ' . t('game_progress') . ' (user_id, game, level, stars) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE stars = GREATEST(stars, VALUES(stars))')
                ->execute([$uid, $game, $level, max(1, $stars)]);
        }
        $st = db()->prepare('SELECT game_xp FROM ' . t('activity') . ' WHERE user_id = ? AND day = ?');
        $st->execute([$uid, today()]);
        $left = max(0, setting('xp_game_daily_cap') - (int)$st->fetchColumn());
        // Higher levels pay more, and the first win of a level pays double
        $base = ($stars * 3 + ($won ? 3 : 0)) * (1 + intdiv($level, 2));
        $xp = min($left, min(setting('xp_game_max'), $firstWin ? $base * 2 : $base));
        $levelBefore = level_of($uid);
        add_xp($uid, $xp, 0, 1, $xp);
        record_streak($uid, 'win', $won && $stars === 3);
        reward($u, $xp, $levelBefore, ['capped' => $left === 0, 'firstWin' => $firstWin]);

    // ======================================================== daily leaderboard

    case 'board.get':
        // Two leaderboards of the day, XP and daily challenge, among everyone or among friends. Only usernames go out.
        $u = reader();
        $uid = (int)$u['id'];
        settle_boards();
        $today = today();
        $daily = ($_GET['type'] ?? '') !== 'xp';
        $rows = $daily ? daily_board_rows($today) : board_rows($today);
        if (($_GET['scope'] ?? '') === 'friends') {
            $circle = array_merge(friend_ids($uid), [$uid]);
            $rows = array_values(array_filter($rows, fn($r) => in_array((int)$r['id'], $circle, true)));
        }
        $me = null;
        $out = [];
        foreach ($rows as $i => $r) {
            $line = ['place' => $i + 1, 'name' => $r['username'], 'score' => $daily ? $r['correct'] . '/' . $r['total'] : $r['xp'] . ' XP', 'me' => (int)$r['id'] === $uid];
            if ($line['me']) $me = $line;
            if ($i < 20) $out[] = $line;
        }
        $st = db()->prepare('SELECT p.place, p.points, u.username FROM ' . t('board_prizes') . ' p JOIN ' . t('users') . ' u ON u.id = p.user_id WHERE p.day = ? AND u.username IS NOT NULL ORDER BY p.place');
        $st->execute([day_offset($today, -1)]);
        $yesterday = array_map(fn($p) => ['place' => (int)$p['place'], 'points' => (int)$p['points'], 'name' => $p['username']], $st->fetchAll());
        $prizes = board_prizes(count(daily_board_rows($today)));
        $seconds = (new DateTimeImmutable('tomorrow', new DateTimeZone(APP_TIMEZONE)))->getTimestamp() - time();
        send_json(['ok' => true, 'rows' => $out, 'me' => $me, 'participants' => count($rows), 'prizes' => array_values($prizes), 'yesterday' => $yesterday, 'secondsLeft' => $seconds, 'onBoard' => (bool)user_row($uid)['on_board']]);

    // ======================================================== username and friends

    case 'auth.username':
        $u = writer();
        $name = clean_username(body()['username'] ?? '');
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE username = ? AND id <> ?');
        $st->execute([$name, $u['id']]);
        if ($st->fetchColumn()) fail('This username is already taken.', 409, 'username_taken');
        try {
            db()->prepare('UPDATE ' . t('users') . ' SET username = ? WHERE id = ?')->execute([$name, $u['id']]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') fail('This username is already taken.', 409, 'username_taken'); // two requests at once
            throw $e;
        }
        send_json(['ok' => true] + state_payload($u));

    case 'friends.list':
        $u = reader();
        $st = db()->prepare('SELECT u.username, COALESCE(a.xp, 0) AS xp FROM ' . t('friends') . ' f JOIN ' . t('users') . ' u ON u.id = f.friend_id
            LEFT JOIN ' . t('activity') . " a ON a.user_id = u.id AND a.day = ? WHERE f.user_id = ? AND u.status = 'active' AND u.username IS NOT NULL ORDER BY u.username");
        $st->execute([today(), $u['id']]);
        send_json(['ok' => true, 'friends' => array_map(fn($f) => ['name' => $f['username']], $st->fetchAll())]);

    case 'friends.search':
        // Usernames only, starting with what was typed
        $u = reader();
        $q = mb_strtolower(trim((string)($_GET['q'] ?? '')));
        if (!preg_match('/^[a-z0-9_.]{2,20}$/', $q)) send_json(['ok' => true, 'results' => []]);
        $st = db()->prepare('SELECT u.username, (f.friend_id IS NOT NULL) AS added FROM ' . t('users') . ' u LEFT JOIN ' . t('friends') . " f ON f.friend_id = u.id AND f.user_id = ?
            WHERE u.username LIKE ? AND u.id <> ? AND u.status = 'active' ORDER BY u.username LIMIT 10");
        $st->execute([$u['id'], addcslashes($q, '%_\\') . '%', $u['id']]);
        send_json(['ok' => true, 'results' => array_map(fn($r) => ['name' => $r['username'], 'added' => (bool)$r['added']], $st->fetchAll())]);

    case 'friends.add':
        $u = writer();
        $st = db()->prepare('SELECT id FROM ' . t('users') . " WHERE username = ? AND status = 'active'");
        $st->execute([s(body(), 'username', 20)]);
        $id = (int)$st->fetchColumn();
        if (!$id) fail('User not found.', 404, 'not_found');
        if ($id === (int)$u['id']) fail('You cannot add yourself.', 422, 'validation');
        db()->prepare('INSERT IGNORE INTO ' . t('friends') . ' (user_id, friend_id, created_at) VALUES (?,?,?)')->execute([$u['id'], $id, now_utc()]);
        reward($u, 0, level_of((int)$u['id']));

    case 'friends.remove':
        $u = writer();
        db()->prepare('DELETE f FROM ' . t('friends') . ' f JOIN ' . t('users') . ' u ON u.id = f.friend_id WHERE f.user_id = ? AND u.username = ?')->execute([$u['id'], s(body(), 'username', 20)]);
        send_json(['ok' => true]);

    case 'board.seen':
        $u = writer();
        db()->prepare('UPDATE ' . t('board_prizes') . ' SET seen = 1 WHERE user_id = ?')->execute([$u['id']]);
        send_json(['ok' => true]);

    case 'board.join':
        // Taking part is a choice: who leaves disappears from the leaderboard and gets no prizes
        $u = writer();
        db()->prepare('UPDATE ' . t('users') . ' SET on_board = ? WHERE id = ?')->execute([empty(body()['on']) ? 0 : 1, $u['id']]);
        send_json(['ok' => true] + state_payload($u));

    default:
        if (strpos($r, 'admin.') === 0) {
            require __DIR__ . '/../lib/admin-api.php';
            admin_route($r);
        }
        fail('Invalid request.', 404, 'not_found');
}
