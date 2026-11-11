<?php
/**
 * Forkville — admin API (routes admin.*, included by api/index.php). Only accounts with role ≥ ROLE_ADMIN.
 *
 *   admin.overview                 numbers, the latest changes
 *   admin.users {q, page}          players, searchable by username, name or email
 *   admin.user {id}                one player: account, restaurant, pantry, recipes, staff, garden, history
 *   admin.user.update {id, …}      status, role, email confirmed, restaurant name
 *   admin.user.coins {id, delta, reason}
 *   admin.user.xp {id, xp}
 *   admin.user.ingredients {id, set: {ing: qty}}
 *   admin.user.recipe {id, dish, level}   level 0 forgets the recipe
 *   admin.user.layout.reset {id}   back to the starter restaurant (furniture refunded at full price)
 *   admin.user.delete {id}
 *   admin.catalog                  defaults and current values of every price and rule
 *   admin.catalog.save {catalog}   only what differs from the defaults is stored
 *   admin.log {page, user}
 *
 * Every change is written to admin_log.
 */

declare(strict_types=1);

function require_admin_writer(): array
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail('bad_request', 405);
    $u = require_user();
    require_csrf($u);
    if ((int)$u['role'] < ROLE_ADMIN) fail('forbidden', 403);
    return $u;
}

function admin_log(int $adminId, ?int $userId, string $action, string $detail = ''): void
{
    db()->prepare('INSERT INTO ' . t('admin_log') . ' (admin_id, user_id, action, detail, created_at) VALUES (?,?,?,?,?)')
        ->execute([$adminId, $userId, $action, mb_substr($detail, 0, 500), now_utc()]);
}

function admin_target(array $b): array
{
    $id = (int)($b['id'] ?? 0);
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    $u = $st->fetch();
    if (!$u) fail('not_found', 404);
    $u['id'] = (int)$u['id'];
    return $u;
}

function admin_user_row(array $u): array
{
    return [
        'id' => (int)$u['id'], 'username' => $u['username'], 'name' => $u['name'], 'email' => $u['email'],
        'verified' => !empty($u['email_verified_at']), 'status' => $u['status'], 'admin' => (int)$u['role'] >= ROLE_ADMIN,
        'google' => !empty($u['google_sub']), 'password' => ($u['password_hash'] ?? '') !== '',
        'createdAt' => $u['created_at'], 'lastLogin' => $u['last_login_at'],
        'level' => isset($u['level']) ? (int)$u['level'] : null, 'coins' => isset($u['coins']) ? (int)$u['coins'] : null,
        'popularity' => isset($u['popularity']) ? (int)$u['popularity'] : null, 'lastSeen' => $u['last_seen_at'] ?? null,
    ];
}

function admin_user_detail(int $id): array
{
    $st = db()->prepare('SELECT u.*, r.level, r.coins, r.popularity, r.last_seen_at FROM ' . t('users') . ' u LEFT JOIN ' . t('restaurants') . ' r ON r.user_id = u.id WHERE u.id = ?');
    $st->execute([$id]);
    $u = $st->fetch();
    if (!$u) fail('not_found', 404);
    $r = load_restaurant($id);
    $friends = db()->prepare('SELECT COUNT(*) FROM ' . t('friends') . ' WHERE user_id = ? AND accepted = 1');
    $friends->execute([$id]);
    $log = db()->prepare('SELECT l.action, l.detail, l.created_at, a.username AS admin FROM ' . t('admin_log') . ' l LEFT JOIN ' . t('users') . ' a ON a.id = l.admin_id WHERE l.user_id = ? ORDER BY l.id DESC LIMIT 30');
    $log->execute([$id]);
    return [
        'ok' => true,
        'user' => admin_user_row($u),
        'restaurant' => $r ? [
            'name' => $r['name'], 'coins' => $r['coins'], 'xp' => $r['xp'], 'level' => $r['level'], 'size' => $r['size'] . '×' . $r['depth'], 'popularity' => $r['popularity'],
            'stars' => stars_for($r['popularity']), 'served' => $r['served_total'], 'items' => count($r['layout']), 'staff' => $r['staff'], 'menu' => $r['menu'],
            'rating' => $r['rating_n'] ? round($r['rating_sum'] / $r['rating_n'], 2) : null, 'friendRating' => $r['friend_n'] ? round($r['friend_sum'] / $r['friend_n'], 2) : null,
            'createdAt' => $r['created_at'], 'lastSeen' => $r['last_seen_at'],
        ] : null,
        'ingredients' => (object)load_ingredients($id),
        'recipes' => (object)load_recipes($id),
        'garden' => garden_rows($id),
        'friends' => (int)$friends->fetchColumn(),
        'log' => $log->fetchAll(),
    ];
}

/** Locks the player's restaurant, runs $fn on it, saves, logs, answers with the fresh detail. */
function admin_mutate(array $admin, array $target, string $action, callable $fn): void
{
    db()->beginTransaction();
    $r = load_restaurant($target['id'], true);
    if (!$r) {
        db()->rollBack();
        fail('no_restaurant', 409);
    }
    $detail = (string)$fn($r);
    refresh_popularity($r);
    save_restaurant($r);
    admin_log((int)$admin['id'], $target['id'], $action, $detail);
    db()->commit();
    send_json(admin_user_detail($target['id']));
}

// ---------------------------------------------------------------- catalog editing

/** Every editable value: [default, current] per field, grouped like the panel shows them. */
function admin_catalog(): array
{
    $items = [];
    $cur = items();
    foreach (ITEMS as $k => $d) $items[$k] = ['cat' => $d[0], 'size' => $d[3] . '×' . $d[4],
        'default' => ['price' => $d[1], 'level' => $d[2], 'pop' => $d[5], 'speed' => $d[6], 'enabled' => true],
        'value' => ['price' => $cur[$k][1], 'level' => $cur[$k][2], 'pop' => $cur[$k][5], 'speed' => $cur[$k][6], 'enabled' => $cur[$k][7]]];
    $dishes = [];
    $cur = dishes();
    foreach (DISHES as $k => $d) $dishes[$k] = ['ings' => $d[4],
        'default' => ['price' => $d[0], 'cook' => $d[1], 'eat' => $d[2], 'level' => $d[3], 'enabled' => true],
        'value' => ['price' => $cur[$k][0], 'cook' => $cur[$k][1], 'eat' => $cur[$k][2], 'level' => $cur[$k][3], 'enabled' => $cur[$k][5]]];
    $crops = [];
    $cur = crops();
    foreach (CROPS as $k => $d) $crops[$k] = [
        'default' => ['cost' => $d[0], 'secs' => $d[1], 'yield' => $d[2], 'level' => $d[3], 'enabled' => true],
        'value' => ['cost' => $cur[$k][0], 'secs' => $cur[$k][1], 'yield' => $cur[$k][2], 'level' => $cur[$k][3], 'enabled' => $cur[$k][4]]];
    $market = [];
    $cur = market_prices();
    foreach (MARKET_PRICES as $k => $p) $market[$k] = ['default' => ['price' => $p, 'enabled' => true], 'value' => ['price' => $cur[$k] ?? (catalog_changes()['market'][$k]['price'] ?? $p), 'enabled' => isset($cur[$k])]];
    $staff = [];
    $cur = staff_costs();
    foreach (STAFF as $role => $d) $staff[$role] = ['default' => ['base' => $d[0], 'step' => $d[1]], 'value' => ['base' => $cur[$role][0], 'step' => $cur[$role][1]]];
    $rules = [];
    foreach (RULES as $k => [$def, $min, $max]) $rules[$k] = ['default' => $def, 'value' => rule($k), 'min' => $min, 'max' => $max];
    return compact('items', 'dishes', 'crops', 'market', 'staff', 'rules');
}

/** Field limits: name => [min, max, is_float] */
const ADMIN_FIELDS = [
    'price' => [0, 10000000, false], 'level' => [1, MAX_LEVEL, false], 'pop' => [0, 1000, false], 'speed' => [0.1, 5, true],
    'cook' => [1, 600, true], 'eat' => [1, 600, true], 'cost' => [0, 10000000, false], 'secs' => [10, 604800, false], 'yield' => [1, 100, false],
    'base' => [0, 10000000, false], 'step' => [0, 10000000, false],
];

/** Keeps only the fields that differ from the defaults; refuses values outside the limits. */
function clean_changes(array $sent, array $defaults, array $fields): array
{
    $out = [];
    foreach ($defaults as $key => $def) {
        $row = $sent[$key] ?? null;
        if (!is_array($row)) continue;
        foreach ($fields as $f) {
            if (!array_key_exists($f, $row)) continue;
            if ($f === 'enabled') {
                if (!$row[$f]) $out[$key]['enabled'] = false;
                continue;
            }
            if (!is_numeric($row[$f])) fail('bad_value', 422);
            [$min, $max, $float] = ADMIN_FIELDS[$f];
            $v = $float ? round((float)$row[$f], 2) : (int)$row[$f];
            if ($v == $def[$f]) continue; // unchanged (e.g. speed 0 of a table): nothing to check or store
            if ($v < $min || $v > $max) send_json(['ok' => false, 'error' => 'bad_value', 'field' => "$key.$f"], 422);
            $out[$key][$f] = $v;
        }
    }
    return $out;
}

function save_catalog(array $admin, array $c): array
{
    $cat = admin_catalog();
    $pick = fn(string $group) => array_map(fn($x) => $x['default'], $cat[$group]);
    $changes = [
        'items' => clean_changes((array)($c['items'] ?? []), $pick('items'), ['price', 'level', 'pop', 'speed', 'enabled']),
        'dishes' => clean_changes((array)($c['dishes'] ?? []), $pick('dishes'), ['price', 'cook', 'eat', 'level', 'enabled']),
        'crops' => clean_changes((array)($c['crops'] ?? []), $pick('crops'), ['cost', 'secs', 'yield', 'level', 'enabled']),
        'market' => clean_changes((array)($c['market'] ?? []), $pick('market'), ['price', 'enabled']),
        'staff' => clean_changes((array)($c['staff'] ?? []), $pick('staff'), ['base', 'step']),
        'rules' => [],
    ];
    foreach (RULES as $k => [$def, $min, $max]) {
        if (!isset($c['rules'][$k])) continue;
        $v = $c['rules'][$k];
        if (!is_numeric($v)) fail('bad_value', 422);
        $v = is_float($def) ? round((float)$v, 3) : (int)$v;
        if ($v < $min || $v > $max) send_json(['ok' => false, 'error' => 'bad_value', 'field' => "rules.$k"], 422);
        if ($v != $def) $changes['rules'][$k] = $v;
    }
    // The market needs at least one ingredient on sale
    if (count(array_filter($changes['market'], fn($m) => ($m['enabled'] ?? true) === false)) >= count(MARKET_PRICES)) fail('market_empty', 422);
    $changes = array_filter($changes);
    db()->prepare('INSERT INTO ' . t('settings') . " (k, v, updated_at) VALUES ('catalog', ?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v), updated_at = VALUES(updated_at)")
        ->execute([json_encode($changes ?: new stdClass()), now_utc()]);
    $n = array_sum(array_map(fn($g) => is_array($g) ? count($g) : 0, $changes));
    admin_log((int)$admin['id'], null, 'catalog', $n . ' changed entries');
    catalog_changes(true);
    return admin_catalog();
}

// ---------------------------------------------------------------- routes

function admin_route(string $r): void
{
    $admin = require_admin_writer();
    $b = body();
    switch ($r) {
        case 'admin.overview':
            $q = fn(string $sql) => (int)db()->query($sql)->fetchColumn();
            $u = t('users');
            $rest = t('restaurants');
            $log = db()->query('SELECT l.action, l.detail, l.created_at, a.username AS admin, p.username AS player FROM ' . t('admin_log') . ' l LEFT JOIN ' . $u . ' a ON a.id = l.admin_id LEFT JOIN ' . $u . ' p ON p.id = l.user_id ORDER BY l.id DESC LIMIT 15')->fetchAll();
            $st = db()->prepare('SELECT u.username, w.coins FROM ' . t('weekly') . ' w JOIN ' . $u . ' u ON u.id = w.user_id WHERE w.week = ? ORDER BY w.coins DESC LIMIT 5');
            $st->execute([week_key()]);
            send_json(['ok' => true,
                'users' => $q("SELECT COUNT(*) FROM $u"), 'verified' => $q("SELECT COUNT(*) FROM $u WHERE email_verified_at IS NOT NULL"),
                'disabled' => $q("SELECT COUNT(*) FROM $u WHERE status = 'disabled'"), 'newWeek' => $q("SELECT COUNT(*) FROM $u WHERE created_at > UTC_TIMESTAMP() - INTERVAL 7 DAY"),
                'active24' => $q("SELECT COUNT(*) FROM $rest WHERE last_seen_at > UTC_TIMESTAMP() - INTERVAL 1 DAY"), 'restaurants' => $q("SELECT COUNT(*) FROM $rest"),
                'coins' => $q("SELECT COALESCE(SUM(coins), 0) FROM $rest"), 'served' => $q("SELECT COALESCE(SUM(served_total), 0) FROM $rest"),
                'topWeek' => $st->fetchAll(), 'log' => $log]);

        case 'admin.users':
            $q = trim((string)($b['q'] ?? ''));
            $page = max(0, (int)($b['page'] ?? 0));
            $where = '';
            $params = [];
            if ($q !== '') {
                $where = 'WHERE u.username LIKE ? OR u.email LIKE ? OR u.name LIKE ?';
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $params = [$like, $like, $like];
            }
            $total = db()->prepare('SELECT COUNT(*) FROM ' . t('users') . " u $where");
            $total->execute($params);
            $st = db()->prepare('SELECT u.*, r.level, r.coins, r.popularity, r.last_seen_at FROM ' . t('users') . ' u LEFT JOIN ' . t('restaurants') . " r ON r.user_id = u.id $where ORDER BY u.id DESC LIMIT 50 OFFSET " . ($page * 50));
            $st->execute($params);
            send_json(['ok' => true, 'total' => (int)$total->fetchColumn(), 'page' => $page, 'users' => array_map('admin_user_row', $st->fetchAll())]);

        case 'admin.user':
            send_json(admin_user_detail(admin_target($b)['id']));

        case 'admin.user.update':
            $t = admin_target($b);
            $self = $t['id'] === (int)$admin['id'];
            $done = [];
            if (isset($b['status'])) {
                $status = $b['status'] === 'disabled' ? 'disabled' : 'active';
                if ($self && $status === 'disabled') fail('not_yourself', 422);
                db()->prepare('UPDATE ' . t('users') . ' SET status = ? WHERE id = ?')->execute([$status, $t['id']]);
                if ($status === 'disabled') db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$t['id']]); // signed out at once
                $done[] = 'status=' . $status;
            }
            if (isset($b['admin'])) {
                if ($self && !$b['admin']) fail('not_yourself', 422);
                db()->prepare('UPDATE ' . t('users') . ' SET role = ? WHERE id = ?')->execute([$b['admin'] ? ROLE_ADMIN : 0, $t['id']]);
                $done[] = 'admin=' . ($b['admin'] ? 'yes' : 'no');
            }
            if (!empty($b['verify']) && empty($t['email_verified_at'])) {
                db()->prepare('UPDATE ' . t('users') . ' SET email_verified_at = ? WHERE id = ?')->execute([now_utc(), $t['id']]);
                $done[] = 'email confirmed';
            }
            if (isset($b['restaurantName'])) {
                $name = s($b, 'restaurantName', 40);
                if (mb_strlen($name) < 2) fail('name_required', 422);
                db()->prepare('UPDATE ' . t('restaurants') . ' SET name = ? WHERE user_id = ?')->execute([$name, $t['id']]);
                $done[] = 'restaurant name=' . $name;
            }
            if ($done) admin_log((int)$admin['id'], $t['id'], 'update', implode(', ', $done));
            send_json(admin_user_detail($t['id']));

        case 'admin.user.coins':
            $t = admin_target($b);
            $delta = (int)($b['delta'] ?? 0);
            if ($delta === 0 || abs($delta) > 100000000) fail('bad_value', 422);
            $reason = s($b, 'reason', 200);
            admin_mutate($admin, $t, 'coins', function (array &$r) use ($delta, $reason) {
                $before = $r['coins'];
                $r['coins'] = max(0, $r['coins'] + $delta);
                ledger((int)$r['user_id'], 'admin', $r['coins'] - $before, $reason);
                return sprintf('%+d (%d → %d)%s', $delta, $before, $r['coins'], $reason !== '' ? ' — ' . $reason : '');
            });

        case 'admin.user.xp':
            $t = admin_target($b);
            $xp = (int)($b['xp'] ?? -1);
            if ($xp < 0 || $xp > 100000000) fail('bad_value', 422);
            admin_mutate($admin, $t, 'xp', function (array &$r) use ($xp) {
                $before = [$r['xp'], $r['level']];
                $r['xp'] = $xp;
                $r['level'] = level_for_xp($xp);
                return sprintf('xp %d → %d (level %d → %d)', $before[0], $xp, $before[1], $r['level']);
            });

        case 'admin.user.ingredients':
            $t = admin_target($b);
            $set = (array)($b['set'] ?? []);
            admin_mutate($admin, $t, 'ingredients', function (array &$r) use ($set) {
                $have = load_ingredients((int)$r['user_id'], true);
                $parts = [];
                foreach ($set as $ing => $qty) {
                    $ing = (string)$ing;
                    if (!isset(INGREDIENTS[$ing]) || !is_numeric($qty)) fail('bad_value', 422);
                    $qty = max(0, min(100000, (int)$qty));
                    if (($have[$ing] ?? 0) === $qty) continue;
                    db()->prepare('INSERT INTO ' . t('ingredients') . ' (user_id, ing, qty) VALUES (?,?,?) ON DUPLICATE KEY UPDATE qty = VALUES(qty)')->execute([$r['user_id'], $ing, $qty]);
                    $parts[] = $ing . ' ' . ($have[$ing] ?? 0) . '→' . $qty;
                }
                return implode(', ', $parts);
            });

        case 'admin.user.recipe':
            $t = admin_target($b);
            $dish = (string)($b['dish'] ?? '');
            $level = (int)($b['level'] ?? -1);
            if (!isset(DISHES[$dish]) || $level < 0 || $level > DISH_MAX_LEVEL) fail('bad_value', 422);
            admin_mutate($admin, $t, 'recipe', function (array &$r) use ($dish, $level) {
                if ($level === 0) {
                    db()->prepare('DELETE FROM ' . t('recipes') . ' WHERE user_id = ? AND dish = ?')->execute([$r['user_id'], $dish]);
                    $r['menu'] = array_values(array_filter($r['menu'], fn($d) => $d !== $dish));
                } else {
                    db()->prepare('INSERT INTO ' . t('recipes') . ' (user_id, dish, level) VALUES (?,?,?) ON DUPLICATE KEY UPDATE level = VALUES(level)')->execute([$r['user_id'], $dish, $level]);
                }
                return $dish . ' → ' . ($level ?: 'forgotten');
            });

        case 'admin.user.layout.reset':
            $t = admin_target($b);
            admin_mutate($admin, $t, 'layout reset', function (array &$r) {
                $items = items();
                $value = 0;
                foreach ($r['layout'] as $it) $value += $items[$it['k']][1] ?? 0;
                $starter = 0;
                foreach (starter_layout() as $it) $starter += $items[$it['k']][1] ?? 0;
                $refund = max(0, $value - $starter);
                $r['layout'] = starter_layout();
                $r['next_item_id'] = max($r['next_item_id'], 11);
                $r['coins'] += $refund;
                return 'refunded ' . $refund;
            });

        case 'admin.user.delete':
            $t = admin_target($b);
            if ($t['id'] === (int)$admin['id']) fail('not_yourself', 422);
            db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$t['id']]);
            admin_log((int)$admin['id'], null, 'delete', '@' . $t['username'] . ' (#' . $t['id'] . ')');
            send_json(['ok' => true]);

        case 'admin.catalog':
            send_json(['ok' => true, 'catalog' => admin_catalog()]);

        case 'admin.catalog.save':
            send_json(['ok' => true, 'catalog' => save_catalog($admin, (array)($b['catalog'] ?? []))]);

        case 'admin.log':
            $page = max(0, (int)($b['page'] ?? 0));
            $uid = (int)($b['user'] ?? 0);
            $st = db()->prepare('SELECT l.id, l.action, l.detail, l.created_at, a.username AS admin, p.username AS player, l.user_id FROM ' . t('admin_log') . ' l LEFT JOIN ' . t('users') . ' a ON a.id = l.admin_id LEFT JOIN ' . t('users') . ' p ON p.id = l.user_id'
                . ($uid ? ' WHERE l.user_id = ?' : '') . ' ORDER BY l.id DESC LIMIT 50 OFFSET ' . ($page * 50));
            $st->execute($uid ? [$uid] : []);
            send_json(['ok' => true, 'log' => $st->fetchAll(), 'page' => $page]);

        default:
            fail('not_found', 404);
    }
}
