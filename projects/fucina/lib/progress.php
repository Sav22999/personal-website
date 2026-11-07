<?php
/**
 * Fucina — gamification: XP, levels, streaks, badges and the summary shown by the app.
 * All the rules live here, so that the server is the only one deciding what a learner has earned.
 */

declare(strict_types=1);

const ROLE_ADMIN = 5;
const ROLE_MASTER = 10;

/** Rank of each level band: from the first spark to the master of the forge. */
const RANKS = [
    1 => ['id' => 'spark', 'it' => 'Scintilla', 'en' => 'Spark'],
    3 => ['id' => 'apprentice', 'it' => 'Apprendista', 'en' => 'Apprentice'],
    6 => ['id' => 'smith', 'it' => 'Fabbro', 'en' => 'Smith'],
    10 => ['id' => 'artisan', 'it' => 'Artigiano', 'en' => 'Artisan'],
    15 => ['id' => 'master', 'it' => 'Mastro', 'en' => 'Master'],
    25 => ['id' => 'legend', 'it' => 'Leggenda della forgia', 'en' => 'Legend of the forge'],
];

/** Bilingual text of the code (badges). The catalogue itself comes from the database. */
function L(string $it, string $en): array
{
    return ['it' => $it, 'en' => $en];
}

function today(): string
{
    return (new DateTimeImmutable('now', new DateTimeZone(APP_TIMEZONE)))->format('Y-m-d');
}

function day_offset(string $day, int $days): string
{
    return (new DateTimeImmutable($day))->modify(($days >= 0 ? '+' : '') . $days . ' days')->format('Y-m-d');
}

/** Level n starts at 50·n·(n−1) XP: 0, 100, 300, 600, 1000… */
function level_info(int $xp): array
{
    $level = 1;
    while (50 * ($level + 1) * $level <= $xp) $level++;
    $start = 50 * $level * ($level - 1);
    $next = 50 * ($level + 1) * $level;
    $rank = RANKS[1];
    foreach (RANKS as $from => $r) if ($level >= $from) $rank = $r;
    return ['level' => $level, 'xp' => $xp, 'into' => $xp - $start, 'need' => $next - $start, 'rank' => $rank[lang()], 'rankId' => $rank['id']];
}

/** Adds XP (and as many Sparks) and today's activity, and moves the streak forward. Any earned XP keeps the streak alive. */
function add_xp(int $uid, int $xp, int $lessons = 0, int $games = 0, int $gameXp = 0): void
{
    $day = today();
    db()->prepare('INSERT INTO ' . t('activity') . ' (user_id, day, xp, game_xp, lessons, games) VALUES (?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE xp = xp + VALUES(xp), game_xp = game_xp + VALUES(game_xp), lessons = lessons + VALUES(lessons), games = games + VALUES(games)')
        ->execute([$uid, $day, $xp, $gameXp, $lessons, $games]);
    $st = db()->prepare('SELECT streak, best_streak, last_day FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$uid]);
    $u = $st->fetch();
    $streak = (int)$u['streak'];
    if ($u['last_day'] !== $day) $streak = $u['last_day'] === day_offset($day, -1) ? $streak + 1 : 1;
    db()->prepare('UPDATE ' . t('users') . ' SET xp = xp + ?, points = points + ?, streak = ?, best_streak = ?, last_day = ? WHERE id = ?')
        ->execute([$xp, $xp, $streak, max($streak, (int)$u['best_streak']), $day, $uid]);
}

/** The streak shown to the learner: it is already broken if neither today nor yesterday has activity. */
function live_streak(array $u): int
{
    $day = today();
    return in_array($u['last_day'] ?? null, [$day, day_offset($day, -1)], true) ? (int)$u['streak'] : 0;
}

/** Badges: fixed ones, plus one for each published course. */
function badge_catalog(): array
{
    $courses = [];
    foreach (course_ids() as $id) {
        $title = course_raw($id)['title'];
        $courses['course_' . $id] = ['icon' => 'trophy', 'title' => $title, 'desc' => L('Completa tutti i livelli del corso', 'Complete every level of the course')];
    }
    return badge_fixed() + $courses;
}

function badge_fixed(): array
{
    return [
        'first_lesson' => ['icon' => 'spark', 'title' => L('Prima scintilla', 'First spark'), 'desc' => L('Completa la tua prima lezione', 'Complete your first lesson')],
        'lessons_10' => ['icon' => 'book', 'title' => L('Sul pezzo', 'On it'), 'desc' => L('Completa 10 lezioni', 'Complete 10 lessons')],
        'lessons_25' => ['icon' => 'book', 'title' => L('Martello instancabile', 'Tireless hammer'), 'desc' => L('Completa 25 lezioni', 'Complete 25 lessons')],
        'lessons_50' => ['icon' => 'book', 'title' => L('Metà del cammino', 'Halfway there'), 'desc' => L('Completa 50 lezioni', 'Complete 50 lessons')],
        'lessons_100' => ['icon' => 'book', 'title' => L('Cento colpi', 'A hundred blows'), 'desc' => L('Completa 100 lezioni', 'Complete 100 lessons')],
        'stars_30' => ['icon' => 'star', 'title' => L('Costellazione', 'Constellation'), 'desc' => L('Raccogli 30 stelle nelle lezioni', 'Collect 30 stars in the lessons')],
        'drills_10' => ['icon' => 'bolt', 'title' => L('Riscaldamento', 'Warm-up'), 'desc' => L('Completa 10 sfide lampo', 'Complete 10 quick drills')],
        'drills_50' => ['icon' => 'bolt', 'title' => L('Mani veloci', 'Quick hands'), 'desc' => L('Completa 50 sfide lampo', 'Complete 50 quick drills')],
        'drills_perfect' => ['icon' => 'star', 'title' => L('Senza sbavature', 'Flawless'), 'desc' => L('Completa 25 sfide senza errori', 'Complete 25 drills without mistakes')],
        'tasks_10' => ['icon' => 'code', 'title' => L('Mani in pasta', 'Hands on'), 'desc' => L('Risolvi 10 esercizi di codice', 'Solve 10 coding exercises')],
        'streak_3' => ['icon' => 'fire', 'title' => L('Fuoco acceso', 'Fire lit'), 'desc' => L('3 giorni di fila', '3 days in a row')],
        'streak_7' => ['icon' => 'fire', 'title' => L('Una settimana di fuoco', 'A week on fire'), 'desc' => L('7 giorni di fila', '7 days in a row')],
        'streak_10' => ['icon' => 'fire', 'title' => L('Dieci di fila', 'Ten in a row'), 'desc' => L('10 giorni di fila nell’app', '10 days in a row in the app')],
        'streak_100' => ['icon' => 'fire', 'title' => L('Cento giorni di fuoco', 'A hundred days on fire'), 'desc' => L('100 giorni di fila', '100 days in a row')],
        'wins_row_5' => ['icon' => 'trophy', 'title' => L('In serie', 'On a roll'), 'desc' => L('5 giochi di fila senza sbagliare', '5 games in a row without mistakes')],
        'wins_row_10' => ['icon' => 'trophy', 'title' => L('Imbattibile', 'Unbeatable'), 'desc' => L('10 giochi di fila senza sbagliare', '10 games in a row without mistakes')],
        'wins_50' => ['icon' => 'game', 'title' => L('Campione', 'Champion'), 'desc' => L('Vinci 50 partite', 'Win 50 games')],
        'drills_row_10' => ['icon' => 'bolt', 'title' => L('Occhio di falco', 'Eagle eye'), 'desc' => L('10 sfide di fila senza errori', '10 drills in a row without mistakes')],
        'board_podium' => ['icon' => 'trophy', 'title' => L('Sul podio', 'On the podium'), 'desc' => L('Finisci tra i primi 3 della classifica del giorno', 'Finish in the top 3 of the daily leaderboard')],
        'board_first' => ['icon' => 'trophy', 'title' => L('Re della forgia', 'King of the forge'), 'desc' => L('Vinci la classifica del giorno', 'Win the daily leaderboard')],
        'certs_3' => ['icon' => 'cert', 'title' => L('Parete dei diplomi', 'Wall of diplomas'), 'desc' => L('Ottieni 3 certificati', 'Earn 3 certificates')],
        'js_master' => ['icon' => 'cert', 'title' => L('Mastro JavaScript', 'JavaScript master'), 'desc' => L('Tutti i certificati JavaScript', 'All the JavaScript certificates')],
        'php_master' => ['icon' => 'cert', 'title' => L('Mastro PHP', 'PHP master'), 'desc' => L('Certificati PHP base e avanzato', 'PHP Basics and Advanced certificates')],
        'streak_30' => ['icon' => 'fire', 'title' => L('Fiamma eterna', 'Eternal flame'), 'desc' => L('30 giorni di fila', '30 days in a row')],
        'first_game' => ['icon' => 'game', 'title' => L('Si gioca', 'Game on'), 'desc' => L('Finisci la tua prima partita', 'Finish your first game')],
        'all_games' => ['icon' => 'game', 'title' => L('Tuttofare', 'All-rounder'), 'desc' => L('Prova tutti i giochi', 'Try every game')],
        'games_25' => ['icon' => 'game', 'title' => L('Sala giochi', 'Arcade'), 'desc' => L('Finisci 25 partite', 'Finish 25 games')],
        'daily_7' => ['icon' => 'calendar', 'title' => L('Puntuale', 'Punctual'), 'desc' => L('Fai 7 sfide del giorno', 'Do 7 daily challenges')],
        'daily_perfect' => ['icon' => 'calendar', 'title' => L('Giornata perfetta', 'Perfect day'), 'desc' => L('Sfida del giorno senza errori', 'A daily challenge with no mistakes')],
        'first_cert' => ['icon' => 'cert', 'title' => L('Forgiato', 'Forged'), 'desc' => L('Ottieni il tuo primo certificato', 'Earn your first certificate')],
        'html_master' => ['icon' => 'cert', 'title' => L('Mastro HTML', 'HTML master'), 'desc' => L('Certificati HTML base e avanzato', 'HTML Basics and Advanced certificates')],
        'css_master' => ['icon' => 'cert', 'title' => L('Mastro CSS', 'CSS master'), 'desc' => L('Certificati CSS base e avanzato', 'CSS Basics and Advanced certificates')],
        'exam_100' => ['icon' => 'star', 'title' => L('Esame perfetto', 'Perfect exam'), 'desc' => L('Supera un esame con il 100%', 'Pass an exam with 100%')],
        'level_5' => ['icon' => 'hammer', 'title' => L('Livello 5', 'Level 5'), 'desc' => L('Raggiungi il livello 5', 'Reach level 5')],
        'level_10' => ['icon' => 'hammer', 'title' => L('Livello 10', 'Level 10'), 'desc' => L('Raggiungi il livello 10', 'Reach level 10')],
    ];
}

function user_totals(int $uid): array
{
    $one = function (string $sql) use ($uid) {
        $st = db()->prepare($sql);
        $st->execute([$uid]);
        return $st->fetch(PDO::FETCH_NUM);
    };
    [$lessons, $stars, $tasks] = $one('SELECT COUNT(*), COALESCE(SUM(stars),0), COALESCE(SUM(task_done),0) FROM ' . t('lesson_progress') . ' WHERE user_id = ?');
    [$games, $kinds, $wins] = $one('SELECT COUNT(*), COUNT(DISTINCT game), COALESCE(SUM(won),0) FROM ' . t('game_results') . ' WHERE user_id = ?');
    [$daily, $perfect] = $one('SELECT COUNT(*), COALESCE(SUM(correct = total),0) FROM ' . t('daily_results') . ' WHERE user_id = ?');
    [$drills, $drillsPerfect] = $one('SELECT COUNT(*), COALESCE(SUM(stars = 3),0) FROM ' . t('drill_done') . ' WHERE user_id = ?');
    [$certs, $bestExam] = $one('SELECT COUNT(*), COALESCE(MAX(score),0) FROM ' . t('certificates') . ' WHERE user_id = ?');
    return ['lessons' => (int)$lessons, 'stars' => (int)$stars, 'tasks' => (int)$tasks, 'games' => (int)$games, 'gameKinds' => (int)$kinds, 'wins' => (int)$wins,
        'daily' => (int)$daily, 'dailyPerfect' => (int)$perfect, 'certificates' => (int)$certs, 'bestExam' => (int)$bestExam, 'drills' => (int)$drills, 'drillsPerfect' => (int)$drillsPerfect];
}

function user_certificates(int $uid): array
{
    $st = db()->prepare('SELECT code, course, name, score, issued_at FROM ' . t('certificates') . ' WHERE user_id = ? ORDER BY issued_at DESC');
    $st->execute([$uid]);
    $out = [];
    foreach ($st->fetchAll() as $c) $out[$c['course']] = ['code' => $c['code'], 'course' => $c['course'], 'name' => $c['name'], 'score' => (int)$c['score'], 'issuedAt' => $c['issued_at']];
    return $out;
}

/** Awards the badges just reached and returns them, ready to be celebrated by the app. */
function award_badges(int $uid): array
{
    $st = db()->prepare('SELECT xp, best_streak, best_win_streak, best_drill_streak FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$uid]);
    $u = $st->fetch();
    $n = user_totals($uid);
    $certs = user_certificates($uid);
    $level = level_info((int)$u['xp'])['level'];
    $streak = (int)$u['best_streak'];
    $st = db()->prepare('SELECT COALESCE(MIN(place),0) FROM ' . t('board_prizes') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    $bestPlace = (int)$st->fetchColumn();
    $reached = [
        'first_lesson' => $n['lessons'] >= 1, 'lessons_10' => $n['lessons'] >= 10, 'lessons_25' => $n['lessons'] >= 25,
        'stars_30' => $n['stars'] >= 30, 'tasks_10' => $n['tasks'] >= 10,
        'drills_10' => $n['drills'] >= 10, 'drills_50' => $n['drills'] >= 50, 'drills_perfect' => $n['drillsPerfect'] >= 25,
        'streak_3' => $streak >= 3, 'streak_7' => $streak >= 7, 'streak_30' => $streak >= 30,
        'first_game' => $n['games'] >= 1, 'all_games' => $n['gameKinds'] >= max(1, count(games_raw())), 'games_25' => $n['games'] >= 25,
        'daily_7' => $n['daily'] >= 7, 'daily_perfect' => $n['dailyPerfect'] >= 1,
        'first_cert' => $n['certificates'] >= 1, 'exam_100' => $n['bestExam'] >= 100,
        'html_master' => isset($certs['html-base'], $certs['html-advanced']), 'css_master' => isset($certs['css-base'], $certs['css-advanced']),
        'level_5' => $level >= 5, 'level_10' => $level >= 10,
        'lessons_50' => $n['lessons'] >= 50, 'lessons_100' => $n['lessons'] >= 100, 'streak_10' => $streak >= 10, 'streak_100' => $streak >= 100,
        'wins_row_5' => (int)$u['best_win_streak'] >= 5, 'wins_row_10' => (int)$u['best_win_streak'] >= 10, 'wins_50' => $n['wins'] >= 50,
        'drills_row_10' => (int)$u['best_drill_streak'] >= 10, 'certs_3' => $n['certificates'] >= 3,
        'js_master' => isset($certs['js-base'], $certs['js-intermediate'], $certs['js-advanced']), 'php_master' => isset($certs['php-base'], $certs['php-advanced']),
        'board_podium' => $bestPlace > 0, 'board_first' => $bestPlace === 1,
    ];
    foreach (courses_summary($uid) as $c) $reached['course_' . $c['id']] = $c['lessons'] > 0 && $c['done'] === $c['lessons'];
    $st = db()->prepare('SELECT badge FROM ' . t('badges') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    $have = array_column($st->fetchAll(), 'badge');
    $catalog = badge_catalog();
    $new = [];
    foreach ($reached as $id => $ok) {
        if (!$ok || in_array($id, $have, true)) continue;
        db()->prepare('INSERT IGNORE INTO ' . t('badges') . ' (user_id, badge, earned_at) VALUES (?,?,?)')->execute([$uid, $id, now_utc()]);
        $new[] = ['id' => $id] + deep_pick($catalog[$id]);
    }
    return $new;
}

function lesson_rows(int $uid): array
{
    $st = db()->prepare('SELECT course, lesson, stars, task_done FROM ' . t('lesson_progress') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    $out = [];
    foreach ($st->fetchAll() as $r) $out[$r['course']][$r['lesson']] = ['stars' => (int)$r['stars'], 'taskDone' => (bool)$r['task_done']];
    return $out;
}

/** Courses open to a learner: one with a prerequisite stays locked until all the lessons of that course are done. */
function courses_summary(int $uid): array
{
    $done = lesson_rows($uid);
    $certs = user_certificates($uid);
    $st = db()->prepare('SELECT course, MAX(score) AS best FROM ' . t('exams') . ' WHERE user_id = ? AND score >= ? GROUP BY course');
    $st->execute([$uid, setting('exam_pass_percent')]);
    $passed = array_map('intval', array_column($st->fetchAll(), 'best', 'course'));
    $out = [];
    $finished = [];
    foreach (course_ids() as $id) {
        $c = course_raw($id);
        $valid = array_intersect_key($done[$id] ?? [], array_flip(array_column($c['lessons'], 'id')));
        $finished[$id] = count($c['lessons']) > 0 && count($valid) === count($c['lessons']);
        $out[] = [
            'id' => $id, 'title' => pick($c['title']), 'short' => pick($c['short']), 'desc' => pick($c['desc']), 'level' => pick($c['level']), 'family' => $c['family'],
            'after' => $c['after'], 'lessons' => count($c['lessons']), 'done' => count($valid), 'stars' => array_sum(array_column($valid, 'stars')), 'certificate' => $certs[$id] ?? null, 'examScore' => $passed[$id] ?? 0,
        ];
    }
    foreach ($out as &$o) $o['locked'] = $o['after'] !== null && in_array($o['after'], course_ids(), true) && !$finished[$o['after']];
    return $out;
}

function course_locked(int $uid, string $id): bool
{
    foreach (courses_summary($uid) as $c) if ($c['id'] === $id) return $c['locked'];
    return true;
}

/** Games with the levels already won: the first level not won is the one that is open. */
function games_summary(int $uid): array
{
    $st = db()->prepare('SELECT game, level, stars FROM ' . t('game_progress') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    $won = [];
    foreach ($st->fetchAll() as $r) $won[$r['game']][(int)$r['level']] = (int)$r['stars'];
    return array_map(function ($g) use ($won) {
        $levels = $won[$g['id']] ?? [];
        $unlocked = 1;
        while (isset($levels[$unlocked]) && $unlocked < $g['levels']) $unlocked++;
        return ['id' => $g['id'], 'title' => pick($g['title']), 'desc' => pick($g['desc']), 'icon' => $g['icon'], 'levels' => $g['levels'], 'unlocked' => $unlocked, 'won' => (object)$levels];
    }, games_raw());
}

/** Everything the app shows outside a lesson, in one request. */
function state_payload(array $u): array
{
    $uid = (int)$u['id'];
    settle_boards();
    $st = db()->prepare('SELECT game_xp FROM ' . t('activity') . ' WHERE user_id = ? AND day = ?');
    $st->execute([$uid, today()]);
    $gameToday = (int)$st->fetchColumn();
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$uid]);
    $row = $st->fetch();
    $day = today();

    $from = day_offset($day, -111); // 16 weeks for the calendar
    $st = db()->prepare('SELECT day, xp, lessons, games FROM ' . t('activity') . ' WHERE user_id = ? AND day >= ? ORDER BY day');
    $st->execute([$uid, $from]);
    $activity = array_map(fn($a) => ['day' => $a['day'], 'xp' => (int)$a['xp'], 'lessons' => (int)$a['lessons'], 'games' => (int)$a['games']], $st->fetchAll());

    $st = db()->prepare('SELECT game, COUNT(*) AS played, MAX(score) AS best, SUM(won) AS wins FROM ' . t('game_results') . ' WHERE user_id = ? GROUP BY game');
    $st->execute([$uid]);
    $played = [];
    foreach ($st->fetchAll() as $g) $played[$g['game']] = ['played' => (int)$g['played'], 'best' => (int)$g['best'], 'wins' => (int)$g['wins']];
    $games = array_map(fn($g) => $g + ($played[$g['id']] ?? ['played' => 0, 'best' => 0, 'wins' => 0]), games_summary($uid));

    $st = db()->prepare('SELECT correct, total FROM ' . t('daily_results') . ' WHERE user_id = ? AND day = ?');
    $st->execute([$uid, $day]);
    $daily = $st->fetch();

    $st = db()->prepare('SELECT badge, earned_at FROM ' . t('badges') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    $earned = array_column($st->fetchAll(), 'earned_at', 'badge');
    $badges = [];
    foreach (badge_catalog() as $id => $b) $badges[] = ['id' => $id, 'earnedAt' => $earned[$id] ?? null] + deep_pick($b);

    $user = public_user($row);
    $user['streak'] = live_streak($row);
    return [
        'user' => $user, 'level' => level_info((int)$row['xp']), 'today' => $day, 'practicedToday' => $row['last_day'] === $day,
        'courses' => courses_summary($uid), 'totals' => user_totals($uid), 'activity' => $activity, 'games' => $games,
        'daily' => $daily ? ['correct' => (int)$daily['correct'], 'total' => (int)$daily['total']] : null, 'badges' => $badges,
        'costs' => ['question' => setting('hint_question'), 'task' => setting('hint_task'), 'game' => setting('hint_game'), 'drill' => setting('hint_drill'), 'certificate' => setting('cert_cost')],
        'gameSparks' => ['today' => $gameToday, 'cap' => setting('xp_game_daily_cap')],
        'board' => board_position($uid), 'prizes' => unseen_prizes($uid),
    ];
}

// ---------------------------------------------------------------- daily leaderboard

/** How a learner appears to the others: first name and the initial of the surname, never the email. */
function public_name(string $name): string
{
    $p = preg_split('/\s+/', trim($name)) ?: [$name];
    return $p[0] . (isset($p[1]) ? ' ' . mb_strtoupper(mb_substr($p[1], 0, 1)) . '.' : '');
}

/** Who earned XP on a day, from the first. Learners who left the leaderboard are not counted. */
function board_rows(string $day): array
{
    $st = db()->prepare('SELECT u.id, u.name, u.xp AS total, a.xp FROM ' . t('activity') . ' a JOIN ' . t('users') . " u ON u.id = a.user_id
        WHERE a.day = ? AND a.xp > 0 AND u.on_board = 1 AND u.status = 'active' ORDER BY a.xp DESC, u.id");
    $st->execute([$day]);
    return $st->fetchAll();
}

/** Sparks of the first three places: the more participants, the bigger the prize. */
function board_prizes(int $participants): array
{
    $first = min(setting('board_max'), $participants * setting('board_per_player'));
    return [1 => $first, 2 => $participants >= 2 ? (int)round($first / 2) : 0, 3 => $participants >= 3 ? (int)round($first / 4) : 0];
}

/**
 * Pays the days already over. There is no job to wait for: the first learner of the new day triggers it.
 * The primary key on day and place makes it safe if two requests arrive together.
 */
function settle_boards(): void
{
    $today = today();
    $st = db()->prepare('SELECT v FROM ' . t('meta') . " WHERE k = 'board_settled'");
    $st->execute();
    $last = (string)($st->fetchColumn() ?: '2000-01-01');
    if ($last >= day_offset($today, -1)) return;
    $st = db()->prepare('SELECT DISTINCT day FROM ' . t('activity') . ' WHERE day > ? AND day < ? ORDER BY day');
    $st->execute([$last, $today]);
    foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $day) {
        $rows = board_rows($day);
        $prizes = board_prizes(count($rows));
        foreach (array_slice($rows, 0, 3) as $i => $r) {
            $place = $i + 1;
            if (!$prizes[$place]) continue;
            $in = db()->prepare('INSERT IGNORE INTO ' . t('board_prizes') . ' (day, place, user_id, participants, points) VALUES (?,?,?,?,?)');
            $in->execute([$day, $place, $r['id'], count($rows), $prizes[$place]]);
            if ($in->rowCount()) db()->prepare('UPDATE ' . t('users') . ' SET points = points + ? WHERE id = ?')->execute([$prizes[$place], $r['id']]);
        }
    }
    db()->prepare('INSERT INTO ' . t('meta') . " (k, v) VALUES ('board_settled', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute([day_offset($today, -1)]);
}

function board_position(int $uid): array
{
    $rows = board_rows(today());
    $place = array_search($uid, array_map('intval', array_column($rows, 'id')), true);
    return ['place' => $place === false ? 0 : $place + 1, 'participants' => count($rows)];
}

function unseen_prizes(int $uid): array
{
    $st = db()->prepare('SELECT day, place, participants, points FROM ' . t('board_prizes') . ' WHERE user_id = ? AND seen = 0 ORDER BY day');
    $st->execute([$uid]);
    return array_map(fn($p) => ['day' => $p['day'], 'place' => (int)$p['place'], 'participants' => (int)$p['participants'], 'points' => (int)$p['points']], $st->fetchAll());
}
