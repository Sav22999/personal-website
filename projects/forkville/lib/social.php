<?php
/**
 * Forkville — friends, visits, gifts and leaderboards.
 * The username is the only public data of an account: no name or email ever leaves through these calls.
 */

declare(strict_types=1);

/** Active user by username, or fails with not_found. */
function user_by_username(string $username): array
{
    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) fail('not_found', 404);
    $st = db()->prepare('SELECT id, username FROM ' . t('users') . " WHERE username = ? AND status = 'active' AND email_verified_at IS NOT NULL");
    $st->execute([$username]);
    $u = $st->fetch();
    if (!$u) fail('not_found', 404);
    $u['id'] = (int)$u['id'];
    return $u;
}

function are_friends(int $a, int $b): bool
{
    $st = db()->prepare('SELECT 1 FROM ' . t('friends') . ' WHERE user_id = ? AND friend_id = ? AND accepted = 1');
    $st->execute([$a, $b]);
    return (bool)$st->fetchColumn();
}

function require_friend(array $me, string $username): array
{
    $f = user_by_username($username);
    if ($f['id'] === (int)$me['id'] || !are_friends((int)$me['id'], $f['id'])) fail('not_friends', 403);
    return $f;
}

/** Friends with what the app shows on each card, plus the requests waiting for an answer. */
function friends_out(int $uid): array
{
    $day = today_utc();
    $st = db()->prepare('SELECT u.id, u.username, r.level, r.popularity, r.wish,
            (SELECT COUNT(*) FROM ' . t('visits') . ' v WHERE v.user_id = ? AND v.host_id = u.id AND v.day = ?) AS visited,
            (SELECT COUNT(*) FROM ' . t('gifts') . ' g WHERE g.user_id = ? AND g.to_id = u.id AND g.day = ?) AS gifted
        FROM ' . t('friends') . ' f JOIN ' . t('users') . ' u ON u.id = f.friend_id LEFT JOIN ' . t('restaurants') . " r ON r.user_id = u.id
        WHERE f.user_id = ? AND f.accepted = 1 AND u.status = 'active' ORDER BY u.username");
    $st->execute([$uid, $day, $uid, $day, $uid]);
    $friends = array_map(fn($x) => [
        'username' => $x['username'], 'level' => (int)($x['level'] ?? 1), 'stars' => stars_for((int)($x['popularity'] ?? 10)),
        'wish' => $x['wish'], 'visited' => (bool)$x['visited'], 'gifted' => (bool)$x['gifted'],
    ], $st->fetchAll());
    $st = db()->prepare('SELECT u.username FROM ' . t('friends') . ' f JOIN ' . t('users') . " u ON u.id = f.user_id WHERE f.friend_id = ? AND f.accepted = 0 AND u.status = 'active' ORDER BY f.created_at");
    $st->execute([$uid]);
    $incoming = array_column($st->fetchAll(), 'username');
    $st = db()->prepare('SELECT u.username FROM ' . t('friends') . ' f JOIN ' . t('users') . " u ON u.id = f.friend_id WHERE f.user_id = ? AND f.accepted = 0 ORDER BY f.created_at");
    $st->execute([$uid]);
    $outgoing = array_column($st->fetchAll(), 'username');
    return ['friends' => $friends, 'incoming' => $incoming, 'outgoing' => $outgoing];
}

function friend_count(int $uid): int
{
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('friends') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    return (int)$st->fetchColumn();
}

/** Sends a request, or accepts at once when the other player had already asked. */
function friend_request(int $me, int $other): string
{
    if (are_friends($me, $other)) return 'friends';
    if (friend_count($me) >= MAX_FRIENDS) fail('too_many_friends', 422);
    $st = db()->prepare('SELECT 1 FROM ' . t('friends') . ' WHERE user_id = ? AND friend_id = ? AND accepted = 0');
    $st->execute([$other, $me]);
    if ($st->fetchColumn()) {
        friend_accept($me, $other);
        return 'friends';
    }
    db()->prepare('INSERT IGNORE INTO ' . t('friends') . ' (user_id, friend_id, accepted, created_at) VALUES (?,?,0,?)')->execute([$me, $other, now_utc()]);
    return 'requested';
}

function friend_accept(int $me, int $other): void
{
    $st = db()->prepare('SELECT 1 FROM ' . t('friends') . ' WHERE user_id = ? AND friend_id = ? AND accepted = 0');
    $st->execute([$other, $me]);
    if (!$st->fetchColumn()) fail('not_found', 404);
    db()->prepare('UPDATE ' . t('friends') . ' SET accepted = 1 WHERE user_id = ? AND friend_id = ?')->execute([$other, $me]);
    db()->prepare('INSERT INTO ' . t('friends') . ' (user_id, friend_id, accepted, created_at) VALUES (?,?,1,?) ON DUPLICATE KEY UPDATE accepted = 1')->execute([$me, $other, now_utc()]);
}

function friend_remove(int $me, int $other): void
{
    db()->prepare('DELETE FROM ' . t('friends') . ' WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)')->execute([$me, $other, $other, $me]);
}

/** A visit: the friend's restaurant, and coins for both on the first visit of the day. */
function visit(int $me, int $host): array
{
    db()->beginTransaction();
    $ins = db()->prepare('INSERT IGNORE INTO ' . t('visits') . ' (user_id, host_id, day) VALUES (?,?,?)');
    $ins->execute([$me, $host, today_utc()]);
    $reward = 0;
    $hr = load_restaurant($host);
    if (!$hr) {
        db()->rollBack();
        fail('not_found', 404);
    }
    if ($ins->rowCount()) {
        $reward = rule('visit_base') + 5 * $hr['level'];
        db()->prepare('UPDATE ' . t('restaurants') . ' SET coins = coins + ? WHERE user_id = ?')->execute([$reward, $me]);
        db()->prepare('UPDATE ' . t('restaurants') . ' SET coins = coins + ? WHERE user_id = ?')->execute([rule('visit_host'), $host]);
        ledger($me, 'visit', $reward, (string)($hr['name'] ?? ''));
        ledger($host, 'visited', rule('visit_host'));
    }
    $st = db()->prepare('SELECT rating FROM ' . t('visits') . ' WHERE user_id = ? AND host_id = ? AND day = ?');
    $st->execute([$me, $host, today_utc()]);
    $rated = $st->fetchColumn();
    db()->commit();
    return ['restaurant' => restaurant_public($hr, load_recipes($host)), 'reviews' => array_slice(reviews_out($host, 6)['reviews'], 0, 6), 'reward' => $reward, 'rated' => $rated !== null && $rated !== false ? (int)$rated : null];
}

function rate_visit(int $me, int $host, int $stars): void
{
    if ($stars < 1 || $stars > 5) fail('bad_request', 422);
    db()->beginTransaction();
    $up = db()->prepare('UPDATE ' . t('visits') . ' SET rating = ? WHERE user_id = ? AND host_id = ? AND day = ? AND rating IS NULL');
    $up->execute([$stars, $me, $host, today_utc()]);
    if (!$up->rowCount()) {
        db()->rollBack();
        fail('already_rated', 409);
    }
    $r = load_restaurant($host, true);
    $r['friend_sum'] += $stars;
    $r['friend_n'] += 1;
    refresh_popularity($r);
    db()->prepare('UPDATE ' . t('restaurants') . ' SET friend_sum = ?, friend_n = ?, popularity = ? WHERE user_id = ?')->execute([$r['friend_sum'], $r['friend_n'], $r['popularity'], $host]);
    db()->commit();
}

/** Gifts are free for the giver: one ingredient per friend per day. */
function send_gift(int $me, int $to, string $ing): void
{
    if (!isset(INGREDIENTS[$ing])) fail('bad_request', 422);
    $ins = db()->prepare('INSERT IGNORE INTO ' . t('gifts') . ' (user_id, to_id, day, ing, created_at) VALUES (?,?,?,?,?)');
    $ins->execute([$me, $to, today_utc(), $ing, now_utc()]);
    if (!$ins->rowCount()) fail('already_gifted', 409);
}

function inbox_out(int $uid): array
{
    $st = db()->prepare('SELECT g.id, g.ing, u.username FROM ' . t('gifts') . ' g JOIN ' . t('users') . ' u ON u.id = g.user_id WHERE g.to_id = ? AND g.claimed_at IS NULL ORDER BY g.id LIMIT 100');
    $st->execute([$uid]);
    return array_map(fn($g) => ['id' => (int)$g['id'], 'ing' => $g['ing'], 'from' => $g['username']], $st->fetchAll());
}

/** Claims the gifts in the inbox (all, or one by id). Returns the ingredients received. */
function claim_gifts(int $uid, int $id = 0): array
{
    db()->beginTransaction();
    $sql = 'SELECT id, ing FROM ' . t('gifts') . ' WHERE to_id = ? AND claimed_at IS NULL' . ($id ? ' AND id = ?' : '') . ' FOR UPDATE';
    $st = db()->prepare($sql);
    $st->execute($id ? [$uid, $id] : [$uid]);
    $got = [];
    foreach ($st->fetchAll() as $g) {
        add_ingredient($uid, $g['ing'], 1);
        $got[$g['ing']] = ($got[$g['ing']] ?? 0) + 1;
        db()->prepare('UPDATE ' . t('gifts') . ' SET claimed_at = ? WHERE id = ?')->execute([now_utc(), $g['id']]);
    }
    db()->commit();
    return $got;
}

/**
 * Leaderboards. scope: friends | global; period: week (coins earned this week) | all (popularity).
 * Only usernames, levels and the value ranked.
 */
function board_out(int $uid, string $scope, string $period): array
{
    $friendsOnly = $scope === 'friends';
    $join = $friendsOnly ? ' AND (u.id = :me OR u.id IN (SELECT friend_id FROM ' . t('friends') . ' WHERE user_id = :me2 AND accepted = 1))' : '';
    if ($period === 'week') {
        $sql = 'SELECT u.username, r.level, w.coins AS value FROM ' . t('weekly') . ' w JOIN ' . t('users') . ' u ON u.id = w.user_id JOIN ' . t('restaurants') . " r ON r.user_id = u.id
            WHERE w.week = :week AND u.status = 'active'" . $join . ' ORDER BY w.coins DESC, u.username LIMIT 50';
        $params = ['week' => week_key()];
    } else {
        $sql = 'SELECT u.username, r.level, r.popularity AS value FROM ' . t('restaurants') . ' r JOIN ' . t('users') . " u ON u.id = r.user_id
            WHERE u.status = 'active' AND u.email_verified_at IS NOT NULL" . $join . ' ORDER BY r.popularity DESC, r.xp DESC, u.username LIMIT 50';
        $params = [];
    }
    if ($friendsOnly) $params += ['me' => $uid, 'me2' => $uid];
    $st = db()->prepare($sql);
    $st->execute($params);
    $rows = [];
    foreach ($st->fetchAll() as $i => $x) $rows[] = ['rank' => $i + 1, 'username' => $x['username'], 'level' => (int)$x['level'], 'value' => (int)$x['value']];
    return $rows;
}
