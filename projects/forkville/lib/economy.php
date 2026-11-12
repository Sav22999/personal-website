<?php
/**
 * Forkville — the restaurant and its economy. Every purchase, upgrade and earning is decided here:
 * the app only proposes, the server checks it against lib/catalog.php inside a locked transaction.
 */

declare(strict_types=1);

require_once __DIR__ . '/catalog.php';

const FURNITURE = ['table', 'chair', 'stove', 'counter', 'deco', 'kitchen', 'service', 'lounge', 'rug', 'outdoor', 'restroom', 'partition', 'stairs'];
const MAX_ITEMS = 220;

/** The door is in the middle of the front wall (the row z = size − 1); its tile must stay free. */
function door_x(int $width): int
{
    return intdiv($width, 2);
}

/** The first restaurant: a small kitchen at the back, two tables, a plant. */
function starter_layout(): array
{
    return [
        ['id' => 1, 'k' => 'stove_basic', 'x' => 1, 'z' => 0, 'r' => 0],
        ['id' => 2, 'k' => 'counter_wood', 'x' => 3, 'z' => 0, 'r' => 0],
        ['id' => 3, 'k' => 'table_small', 'x' => 3, 'z' => 5, 'r' => 0],
        ['id' => 4, 'k' => 'chair_wood', 'x' => 2, 'z' => 5, 'r' => 1],
        ['id' => 5, 'k' => 'chair_wood', 'x' => 4, 'z' => 5, 'r' => 3],
        ['id' => 6, 'k' => 'table_small', 'x' => 7, 'z' => 5, 'r' => 0],
        ['id' => 7, 'k' => 'chair_wood', 'x' => 6, 'z' => 5, 'r' => 1],
        ['id' => 8, 'k' => 'chair_wood', 'x' => 8, 'z' => 5, 'r' => 3],
        ['id' => 9, 'k' => 'plant_pot', 'x' => 0, 'z' => 9, 'r' => 0],
        ['id' => 10, 'k' => 'plant_pot', 'x' => 9, 'z' => 9, 'r' => 0],
    ];
}

function random_staff(string $role, int $id): array
{
    $s = ['id' => $id, 'role' => $role, 'name' => STAFF_NAMES[random_int(0, count(STAFF_NAMES) - 1)], 'color' => STAFF_COLORS[random_int(0, count(STAFF_COLORS) - 1)],
        'trait' => STAFF_TRAITS[random_int(0, count(STAFF_TRAITS) - 1)]];
    if ($role === 'manager') $s['rank'] = 1;
    $s['skills'] = random_skills($role);
    return $s + random_look();
}

/** A look: uniform of the role, a random hairstyle and colour. */
function random_look(): array
{
    // Now and then a pair of glasses or a moustache; nothing on the head (that is the player's choice)
    return ['outfit' => 'default', 'hair' => HAIRSTYLES[random_int(0, count(HAIRSTYLES) - 2)], 'hairColor' => HAIR_COLORS[random_int(0, 6)],
        'glasses' => random_int(0, 5) === 0 ? ACCESSORIES['glasses'][random_int(1, 2)] : 'none', 'facial' => random_int(0, 6) === 0 ? ACCESSORIES['facial'][random_int(1, 3)] : 'none', 'headwear' => 'none'];
}

/** A CV: the main role between 3 and 6 for a new hire (starters), others lower. */
function random_skills(?string $main = null, int $min = 3, int $max = 6): array
{
    $out = [];
    foreach (ROLES as $role) $out[$role] = $role === $main ? random_int($min, $max) : random_int(0, 4);
    return $out;
}

/** Skills of a worker, filling in old workers hired before CVs existed. */
function staff_skills(array $st): array
{
    $sk = (array)($st['skills'] ?? []);
    foreach (ROLES as $role) if (!isset($sk[$role])) $sk[$role] = $role === $st['role'] ? 5 : 2;
    return $sk;
}

/** Today's people looking for a job: generated once a day (or when the list is refreshed or someone is hired). */
function candidates_for(array &$r, bool $fresh = false): array
{
    $c = json_decode((string)($r['candidates'] ?? ''), true);
    if ($fresh || !is_array($c) || ($c['day'] ?? '') !== today_utc() || empty($c['list'])) {
        $list = [];
        for ($i = 0; $i < CANDIDATES; $i++) {
            // Each candidate is strong in one or two roles, with room to grow; better CVs ask for more
            $best = ROLES[random_int(0, count(ROLES) - 1)];
            $skills = random_skills($best, 2, min(10, 5 + intdiv($r['level'], 3)));
            if (random_int(0, 2) === 0) $skills[ROLES[random_int(0, count(ROLES) - 1)]] = random_int(3, 8);
            $list[] = ['name' => STAFF_NAMES[random_int(0, count(STAFF_NAMES) - 1)], 'color' => STAFF_COLORS[random_int(0, count(STAFF_COLORS) - 1)],
                'trait' => STAFF_TRAITS[random_int(0, count(STAFF_TRAITS) - 1)], 'skills' => $skills, 'cost' => hire_cost($skills)] + random_look();
        }
        $c = ['day' => today_utc(), 'list' => $list];
        $r['candidates'] = json_encode($c);
    }
    return $c['list'];
}

function hire_cost(array $skills): int
{
    return 80 + (int)round(array_sum(array_map(fn($v) => $v * $v, $skills)) * 4);
}

function create_restaurant(array $u): void
{
    $now = now_utc();
    $staff = [random_staff('chef', 1), random_staff('waiter', 2)];
    db()->prepare('INSERT IGNORE INTO ' . t('restaurants') . ' (user_id, name, coins, layout, staff, menu, next_item_id, last_report_at, last_seen_at, created_at) VALUES (?,?,?,?,?,?,?,?,?,?)')
        ->execute([$u['id'], mb_substr($u['username'] . ' Bistro', 0, 40), rule('start_coins'), json_encode(starter_layout()), json_encode($staff), json_encode(START_DISHES), 11, $now, $now, $now]);
    $rec = db()->prepare('INSERT IGNORE INTO ' . t('recipes') . ' (user_id, dish, level) VALUES (?,?,1)');
    foreach (START_DISHES as $d) $rec->execute([$u['id'], $d]);
    $ing = db()->prepare('INSERT IGNORE INTO ' . t('ingredients') . ' (user_id, ing, qty) VALUES (?,?,?)');
    foreach (START_INGREDIENTS as $k => $q) $ing->execute([$u['id'], $k, $q]);
    $r = load_restaurant((int)$u['id']);
    $r['popularity'] = popularity($r, load_recipes((int)$u['id']));
    db()->prepare('UPDATE ' . t('restaurants') . ' SET popularity = ? WHERE user_id = ?')->execute([$r['popularity'], $u['id']]);
}

/** The restaurant row with its JSON decoded; $lock reads it FOR UPDATE (call inside a transaction). */
function load_restaurant(int $uid, bool $lock = false): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('restaurants') . ' WHERE user_id = ?' . ($lock ? ' FOR UPDATE' : ''));
    $st->execute([$uid]);
    $r = $st->fetch();
    if (!$r) return null;
    foreach (['layout', 'staff', 'menu', 'finishes'] as $k) $r[$k] = json_decode((string)$r[$k], true) ?: [];
    $r['depth'] = (int)($r['depth'] ?? 0) ?: (int)$r['size']; // rooms from before rows and columns: square
    $r['parking'] = (int)($r['parking'] ?? 0);
    // The door: where the player put it on the front wall (rooms from before: in the middle), and its style
    $r['door'] = isset($r['door']) && $r['door'] !== null ? (int)$r['door'] : door_x((int)$r['size']);
    $r['door_style'] = isset(DOORS[$r['door_style'] ?? '']) ? $r['door_style'] : 'wood';
    // Upper floors: [[width, depth, x offset], …] in the ground floor's coordinates
    $r['floors'] = array_values(array_filter(array_map(fn($f) => is_array($f) && count($f) === 3 ? array_map('intval', $f) : null, json_decode((string)($r['floors'] ?? ''), true) ?: [])));
    foreach (['coins', 'xp', 'level', 'size', 'popularity', 'rating_sum', 'rating_n', 'friend_sum', 'friend_n', 'served_total', 'next_item_id'] as $k) $r[$k] = (int)$r[$k];
    return $r;
}

function save_restaurant(array $r): void
{
    db()->prepare('UPDATE ' . t('restaurants') . ' SET name = ?, kind = ?, coins = ?, xp = ?, level = ?, size = ?, depth = ?, parking = ?, instance = ?, floors = ?, door = ?, door_style = ?, floor = ?, wall = ?, layout = ?, staff = ?, menu = ?, finishes = ?, wish = ?, status = ?, paid_day = ?, closed_from = ?, closed_secs = ?, game_minutes = ?, candidates = ?,
        popularity = ?, rating_sum = ?, rating_n = ?, friend_sum = ?, friend_n = ?, served_total = ?, next_item_id = ?, last_report_at = ?, last_seen_at = ? WHERE user_id = ?')
        ->execute([$r['name'], $r['kind'] ?? 'generic', $r['coins'], $r['xp'], $r['level'], $r['size'], $r['depth'], $r['parking'] ?? 0, $r['instance'] ?? null, json_encode(array_values($r['floors'] ?? [])), $r['door'], $r['door_style'] ?? 'wood', $r['floor'], $r['wall'], json_encode(array_values($r['layout'])), json_encode(array_values($r['staff'])),
            json_encode(array_values($r['menu'])), json_encode(array_values($r['finishes'])), $r['wish'], $r['status'] ?? 'open', $r['paid_day'] ?? null, $r['closed_from'] ?? null, (int)($r['closed_secs'] ?? 0), (int)($r['game_minutes'] ?? 540), $r['candidates'] ?? null, $r['popularity'], $r['rating_sum'], $r['rating_n'], $r['friend_sum'], $r['friend_n'],
            $r['served_total'], $r['next_item_id'], $r['last_report_at'], $r['last_seen_at'], $r['user_id']]);
}

function load_recipes(int $uid): array
{
    $st = db()->prepare('SELECT dish, level FROM ' . t('recipes') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    $out = [];
    foreach ($st->fetchAll() as $row) if (isset(dishes()[$row['dish']])) $out[$row['dish']] = (int)$row['level'];
    return $out;
}

function load_ingredients(int $uid, bool $lock = false): array
{
    $st = db()->prepare('SELECT ing, qty FROM ' . t('ingredients') . ' WHERE user_id = ? AND qty > 0' . ($lock ? ' FOR UPDATE' : ''));
    $st->execute([$uid]);
    $out = [];
    foreach ($st->fetchAll() as $row) $out[$row['ing']] = (int)$row['qty'];
    return $out;
}

function add_ingredient(int $uid, string $ing, int $qty): void
{
    if ($qty < 0) { // using up: the column is unsigned, so never insert a negative value
        db()->prepare('UPDATE ' . t('ingredients') . ' SET qty = qty - ? WHERE user_id = ? AND ing = ? AND qty >= ?')->execute([-$qty, $uid, $ing, -$qty]);
        return;
    }
    db()->prepare('INSERT INTO ' . t('ingredients') . ' (user_id, ing, qty) VALUES (?,?,?) ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)')
        ->execute([$uid, $ing, $qty]);
}

/** Takes the ingredients or fails with no_ingredients (nothing is taken in that case). */
function spend_ingredients(int $uid, array $cost): void
{
    $have = load_ingredients($uid, true);
    foreach ($cost as $ing => $n) if (($have[$ing] ?? 0) < $n) fail('no_ingredients', 422);
    foreach ($cost as $ing => $n) add_ingredient($uid, $ing, -$n);
}

function spend_coins(array &$r, int $amount): void
{
    if ($amount > 0 && $r['coins'] < $amount) fail('no_coins', 422);
    $r['coins'] -= $amount;
}

/** Writes one coin movement to the statement (positive earned, negative spent). */
function ledger(int $uid, string $kind, int $amount, string $detail = ''): void
{
    static $pruned = false;
    if ($amount === 0) return;
    db()->prepare('INSERT INTO ' . t('ledger') . ' (user_id, kind, amount, detail, created_at) VALUES (?,?,?,?,?)')->execute([$uid, $kind, $amount, mb_substr($detail, 0, 120), now_utc()]);
    if (!$pruned && random_int(1, 50) === 1) {
        $pruned = true;
        db()->prepare('DELETE FROM ' . t('ledger') . ' WHERE created_at < ?')->execute([gmdate('Y-m-d H:i:s', time() - 30 * 86400)]);
    }
    ledger_recorded($amount);
}

/** Coins already written to the statement in this request (so mutate() does not count them twice). */
function ledger_recorded(int $add = 0): int
{
    static $sum = 0;
    return $sum += $add;
}

/** XP and the level that follows from it; returns true when the level went up. */
function add_xp(array &$r, int $xp): bool
{
    $r['xp'] += $xp;
    $before = $r['level'];
    $r['level'] = level_for_xp($r['xp']);
    // Each new level brings a gift: level × reward coins
    for ($l = $before + 1; $l <= $r['level']; $l++) {
        $r['coins'] += $l * rule('level_reward');
        ledger((int)$r['user_id'], 'level', $l * rule('level_reward'), (string)$l);
    }
    return $r['level'] > $before;
}

function add_weekly(int $uid, int $coins): void
{
    if ($coins <= 0) return;
    db()->prepare('INSERT INTO ' . t('weekly') . ' (user_id, week, coins) VALUES (?,?,?) ON DUPLICATE KEY UPDATE coins = coins + VALUES(coins)')->execute([$uid, week_key(), $coins]);
}

function refresh_popularity(array &$r): void
{
    $avg = recent_review_average((int)$r['user_id']);
    if ($avg !== null) $r['recent_avg'] = $avg;
    $r['popularity'] = popularity($r, load_recipes((int)$r['user_id']));
}

/** Tiles covered by an item: [w, d] swap on quarter turns. */
function footprint(array $it): array
{
    $def = items()[$it['k']];
    [$w, $d] = $it['r'] % 2 === 1 ? [$def[4], $def[3]] : [$def[3], $def[4]];
    $tiles = [];
    for ($dx = 0; $dx < $w; $dx++) for ($dz = 0; $dz < $d; $dz++) $tiles[] = [$it['x'] + $dx, $it['z'] + $dz];
    return $tiles;
}

/** The rectangle of a floor: [x0, width, depth] (floor 0 is the ground floor, the others are upper floors). */
function floor_rect(int $f, int $w, int $d, array $floors): ?array
{
    if ($f === 0) return [0, $w, $d];
    $fl = $floors[$f - 1] ?? null;
    return $fl ? [$fl[2], $fl[0], $fl[1]] : null;
}

function in_rect(int $x, int $z, array $rect): bool
{
    return $x >= $rect[0] && $x < $rect[0] + $rect[1] && $z >= 0 && $z < $rect[2];
}

/**
 * Checks one layout: known furniture, inside its floor, no overlaps, door free, stairs and lifts that lead somewhere.
 * Above a staircase (on the next floor) and above a lift (on every floor) the tiles stay free: that is the way up.
 */
function clean_layout($list, int $w, int $d, array $floors = [], ?int $door = null): array
{
    if (!is_array($list) || count($list) > MAX_ITEMS) fail('layout_invalid', 422);
    $items = [];
    foreach ($list as $raw) {
        if (!is_array($raw)) fail('layout_invalid', 422);
        $k = (string)($raw['k'] ?? '');
        if (!isset(items()[$k]) || !in_array(items()[$k][0], FURNITURE, true)) fail('layout_invalid', 422);
        $f = max(0, (int)($raw['f'] ?? 0));
        if ($f > count($floors)) fail('layout_outside', 422);
        $it = ['id' => (int)($raw['id'] ?? 0), 'k' => $k, 'x' => (int)($raw['x'] ?? -1), 'z' => (int)($raw['z'] ?? -1), 'r' => ((int)($raw['r'] ?? 0) % 4 + 4) % 4];
        if ($f) $it['f'] = $f;
        $items[] = $it;
    }
    // First the ways up: the tiles they keep free on the floors above
    $door ??= door_x($w);
    $grid = ['0:' . $door . ',' . ($d - 1) => true];
    foreach ($items as $it) {
        if (items()[$it['k']][0] !== 'stairs') continue;
        $f = $it['f'] ?? 0;
        $lift = (ITEM_TAGS[$it['k']] ?? '') === 'lift';
        if ($lift && $f !== 0) fail('layout_outside', 422); // lifts start on the ground floor
        $reached = 0;
        for ($up = $f + 1; $up <= ($lift ? count($floors) : $f + 1); $up++) {
            $rect = floor_rect($up, $w, $d, $floors);
            if (!$rect) break;
            $fits = true;
            foreach (footprint($it) as [$x, $z]) if (!in_rect($x, $z, $rect)) $fits = false;
            if (!$fits) break;
            foreach (footprint($it) as [$x, $z]) $grid["$up:$x,$z"] = true;
            $reached++;
        }
        if (!$reached) fail('stairs_nowhere', 422);
    }
    $rugs = []; // rugs have their own layer: they may lie under furniture and on the door tile, not on each other
    foreach ($items as $it) {
        $cat = items()[$it['k']][0];
        $f = $it['f'] ?? 0;
        $rect = floor_rect($f, $w, $d, $floors);
        $outdoor = $cat === 'outdoor';
        if ($outdoor && $f) fail('layout_outside', 422);
        foreach (footprint($it) as [$x, $z]) {
            // Outdoor pieces go on the terrace and in the yard (never on the path to the door); the rest inside its floor
            if ($outdoor ? !outdoor_tile($x, $z, $w, $d, $door) : !in_rect($x, $z, $rect)) fail('layout_outside', 422);
            if ($cat === 'rug') {
                if (isset($rugs["$f:$x,$z"])) fail('layout_overlap', 422);
                $rugs["$f:$x,$z"] = true;
                continue;
            }
            if (isset($grid["$f:$x,$z"])) fail('layout_overlap', 422);
            $grid["$f:$x,$z"] = true;
        }
    }
    return $items;
}

/** The outdoor area: OUTDOOR rows in front, OUTDOOR columns at the east side and OUTDOOR rows behind (the back garden), minus the path to the door. */
function outdoor_tile(int $x, int $z, int $w, int $d, ?int $door = null): bool
{
    $front = $x >= 0 && $x < $w + OUTDOOR && $z >= $d && $z < $d + OUTDOOR;
    $side = $x >= $w && $x < $w + OUTDOOR && $z >= 0 && $z < $d;
    $back = $x >= 0 && $x < $w + OUTDOOR && $z >= -OUTDOOR && $z < 0; // the back garden
    return ($front || $side || $back) && !($x === ($door ?? door_x($w)) && $z >= $d);
}

/** Price, customers and eating time of a kind of restaurant. */
function kind_of(array $r): array
{
    return KINDS[$r['kind'] ?? 'generic'] ?? KINDS['generic'];
}

function count_items(array $r, string $cat): int
{
    $n = 0;
    foreach ($r['layout'] as $it) if (items()[$it['k']][0] === $cat) $n++;
    return $n;
}

/** Opening and closing: the closed time is counted, because the staff are not paid for it. */
function set_status(array &$r, string $status): void
{
    $was = $r['status'] ?? 'open';
    if ($was === 'closed' && $status !== 'closed' && !empty($r['closed_from'])) {
        $r['closed_secs'] = (int)($r['closed_secs'] ?? 0) + max(0, time() - strtotime($r['closed_from'] . ' UTC'));
        $r['closed_from'] = null;
    }
    if ($status === 'closed' && $was !== 'closed') $r['closed_from'] = now_utc();
    $r['status'] = $status;
}

/** A worker's daily wage: role, skill in the role, rank for managers. */
function wage(array $st): int
{
    $sk = staff_skills($st);
    return (WAGE_BASE[$st['role']] ?? 30) + (int)round($sk[$st['role']] * WAGE_PER_SKILL) + ($st['role'] === 'manager' ? ((int)($st['rank'] ?? 1) - 1) * WAGE_PER_RANK : 0);
}

function payroll(array $r): int
{
    return array_sum(array_map('wage', $r['staff']));
}

/**
 * Wages for every calendar day that ended since the last payday (at most WAGE_MAX_DAYS).
 * The balance never goes below zero. Returns ['days' => n, 'paid' => coins] or null.
 */
function pay_wages(array &$r): ?array
{
    $today = today_utc();
    if (empty($r['paid_day'])) {
        $r['paid_day'] = $today;
        return null;
    }
    $days = (int)floor((strtotime($today . ' UTC') - strtotime($r['paid_day'] . ' UTC')) / 86400);
    if ($days <= 0) return null;
    // Closed time is not paid: the staff were at home
    $period = $days * 86400;
    $closed = (int)($r['closed_secs'] ?? 0) + (!empty($r['closed_from']) ? max(0, time() - strtotime($r['closed_from'] . ' UTC')) : 0);
    $open = max(0, 1 - min($closed, $period) / $period);
    $r['closed_secs'] = 0;
    if (!empty($r['closed_from'])) $r['closed_from'] = now_utc();
    $due = (int)round(payroll($r) * min($days, WAGE_MAX_DAYS) * $open);
    $paid = min($due, max(0, $r['coins']));
    $r['coins'] -= $paid;
    $r['paid_day'] = $today;
    ledger((int)$r['user_id'], 'wages', -$paid, (string)count($r['staff']) . '|' . min($days, WAGE_MAX_DAYS));
    return ['days' => min($days, WAGE_MAX_DAYS), 'paid' => $paid, 'due' => $due, 'openPct' => (int)round($open * 100)];
}

/** Hours a restaurant runs by itself while the player is away: the sum of its managers' ranks. */
function manager_hours(array $r): int
{
    $h = 0;
    foreach ($r['staff'] as $st) if ($st['role'] === 'manager') $h += (MANAGER_HOURS[(int)($st['rank'] ?? 1)] ?? 2) * (0.6 + 0.08 * staff_skills($st)['manager']);
    return (int)round($h);
}

/** Does the layout have a piece with this tag (register, toilet…)? */
function has_tag(array $r, string $tag): bool
{
    foreach ($r['layout'] as $it) if ((ITEM_TAGS[$it['k']] ?? '') === $tag) return true;
    return false;
}

function count_staff(array $r, string $role): int
{
    return count(array_filter($r['staff'], fn($s) => $s['role'] === $role));
}

/** Can the restaurant serve at all? (used for the earnings while away) */
function can_serve(array $r): bool
{
    return count_items($r, 'stove') > 0 && count_items($r, 'counter') > 0 && count_items($r, 'chair') > 0 && count_items($r, 'table') > 0
        && count_staff($r, 'chef') > 0 && count_staff($r, 'waiter') > 0 && count($r['menu']) > 0;
}

/** Seconds between two customers at this popularity (the app uses the same formula). */
function arrival_seconds(int $popularity): float
{
    return max(3.0, min(16.0, 16 - $popularity / 12));
}

function menu_average_price(array $r, array $recipes): float
{
    $sum = 0;
    $n = 0;
    foreach ($r['menu'] as $d) {
        if (!isset($recipes[$d])) continue;
        $sum += dish_price($d, $recipes[$d]);
        $n++;
    }
    return $n ? $sum / $n : 0;
}

/** Earnings while the player was away: capped hours, a quarter of the live speed. */
function apply_offline(array &$r): ?array
{
    $start = strtotime($r['last_seen_at'] . ' UTC');
    $elapsed = time() - $start;
    $r['last_seen_at'] = now_utc();
    $r['last_report_at'] = now_utc();
    if ($elapsed < OFFLINE_MIN || !can_serve($r) || ($r['status'] ?? 'open') !== 'open') return null;
    $recipes = load_recipes((int)$r['user_id']);
    // First the hours covered by managers, almost as if the game were open; then the usual slow trickle
    $managed = min($elapsed, manager_hours($r) * 3600);
    $rest = min($elapsed - $managed, (int)(rule('offline_hours') * 3600));
    $secs = $managed + $rest;
    $arrive = arrival_seconds($r['popularity']) / kind_of($r)[1];
    $served = (int)floor($managed / $arrive * MANAGER_RATE + $rest / $arrive * rule('offline_rate'));
    $served = min($served, (int)floor($secs * count_items($r, 'chair') / SEAT_CYCLE));
    $coins = (int)round($served * menu_average_price($r, $recipes) * kind_of($r)[0] * (1 + 0.1 * ($r['parking'] ?? 0)));
    if ($served <= 0) return null;
    $r['coins'] += $coins;
    ledger((int)$r['user_id'], 'away', $coins, $served . '|' . (int)round($managed / 60));
    $r['served_total'] += $served;
    $levelFrom = $r['level'];
    $up = add_xp($r, $served);
    add_weekly((int)$r['user_id'], $coins);
    // When it happened, for the message: managers first, then the staff alone at a slower pace, then nothing
    return ['served' => $served, 'coins' => $coins, 'minutes' => (int)round($secs / 60), 'managed' => (int)round($managed / 60), 'levelUp' => $up, 'levelFrom' => $levelFrom, 'levelTo' => $r['level'],
        'from' => $start, 'managedTo' => $start + $managed, 'aloneTo' => $start + $managed + $rest, 'now' => time(),
        'alonePct' => (int)round(rule('offline_rate') * 100), 'managerPct' => (int)round(MANAGER_RATE * 100), 'hasManager' => manager_hours($r) > 0];
}

/** Customers served live, reported by the app in batches and capped by time and seats. */
function apply_report(array &$r, array $b): array
{
    $now = time();
    $elapsed = min(REPORT_WINDOW, max(0, $now - strtotime($r['last_report_at'] . ' UTC')));
    // The game calendar moves only while playing: never back, never faster than the top speed allows
    $clock = (int)($b['clock'] ?? 0);
    $old = (int)($r['game_minutes'] ?? 540);
    if ($clock > $old) $r['game_minutes'] = min($clock, $old + (int)ceil(max($elapsed, 1) * GAME_MINUTES_PER_SECOND * MAX_SPEED) + 30);
    // Never more customers than the seats can hold, nor than can arrive at this popularity (with some slack)
    // The app has speed controls up to MAX_SPEED×: the cap allows that much, and no more
    $cap = min((int)floor($elapsed * count_items($r, 'chair') * MAX_SPEED / SEAT_SECONDS), (int)floor($elapsed / arrival_seconds($r['popularity']) * kind_of($r)[1] * MAX_SPEED * 2.2)) + 2; // top speed at a 2× rush hour
    $recipes = load_recipes((int)$r['user_id']);
    $served = 0;
    $coins = 0;
    foreach ((array)($b['dishes'] ?? []) as $dish => $n) {
        $dish = (string)$dish;
        if (!in_array($dish, $r['menu'], true) || !isset($recipes[$dish])) continue;
        $n = max(0, min((int)$n, $cap - $served));
        $served += $n;
        $coins += (int)round($n * dish_price($dish, $recipes[$dish]) * kind_of($r)[0]);
    }
    // A cashier at the till: every bill is CASHIER_BONUS higher (the mandatory tip comes with the tips below)
    if (count_staff($r, 'cashier') > 0 && has_tag($r, 'register')) $coins += (int)round($coins * CASHIER_BONUS);
    // Guests who came by car (the restaurant car park) spend CAR_BONUS more: no more than the spaces can bring
    $spaces = PARKING[$r['parking'] ?? 0][2] ?? 0;
    $cars = $served > 0 ? max(0, min((int)($b['cars'] ?? 0), $served, (int)floor($elapsed * $spaces * MAX_SPEED / 60) + 1)) : 0;
    if (!$spaces) $cars = 0;
    $carCoins = $served > 0 ? (int)round($cars * ($coins / $served) * CAR_BONUS) : 0;
    $coins += $carCoins;
    $tips = max(0, min((int)($b['tips'] ?? 0), (int)floor($coins * 0.3) + 20)); // tips and snacks from the food truck
    $coins += $tips;
    $ratings = array_slice(array_values(array_filter(array_map('intval', (array)($b['ratings'] ?? [])), fn($x) => $x >= 1 && $x <= 5)), 0, $served);
    if ($ratings) {
        $r['rating_sum'] += array_sum($ratings);
        $r['rating_n'] += count($ratings);
        if ($r['rating_n'] > 400) { // recent customers count more: halve the history
            $r['rating_sum'] = intdiv($r['rating_sum'], 2);
            $r['rating_n'] = intdiv($r['rating_n'], 2);
        }
    }
    save_reviews((int)$r['user_id'], (array)($b['reviews'] ?? []), max(1, $served));
    ledger((int)$r['user_id'], 'customers', $coins - $tips, (string)$served);
    ledger((int)$r['user_id'], 'tips', $tips);
    // Experience: with game time (only while open), everyone gets very slowly better at their role
    $years = max(0, (int)($r['game_minutes'] ?? 540) - $old) / GAME_YEAR_MINUTES;
    if ($years > 0 && ($r['status'] ?? 'open') !== 'closed') foreach ($r['staff'] as &$st) {
        $sk = staff_skills($st);
        $sk[$st['role']] = min(10, round($sk[$st['role']] + $years * SKILL_PER_YEAR * max(0, 1 - $sk[$st['role']] / 12), 5));
        $st['skills'] = $sk;
        $st['years'] = round((float)($st['years'] ?? 0) + $years, 5); // seniority, shown in the staff panel
    }
    unset($st);
    $r['coins'] += $coins;
    $r['served_total'] += $served;
    $up = add_xp($r, $served * rule('xp_customer'));
    add_weekly((int)$r['user_id'], $coins);
    $r['last_report_at'] = gmdate('Y-m-d H:i:s', $now);
    $r['last_seen_at'] = gmdate('Y-m-d H:i:s', $now);
    refresh_popularity($r);
    return ['served' => $served, 'coins' => $coins, 'levelUp' => $up];
}

/** New layout and finishes from the editor: new pieces are bought, missing ones sold back at half price. */
function apply_layout(array &$r, array $b): array
{
    $layout = clean_layout($b['layout'] ?? null, $r['size'], $r['depth'], $r['floors'] ?? [], $r['door']);
    $old = [];
    foreach ($r['layout'] as $it) $old[$it['id']] = $it;
    $cost = 0;
    $kept = [];
    foreach ($layout as &$it) {
        if ($it['id'] && isset($old[$it['id']]) && !isset($kept[$it['id']])) {
            if ($old[$it['id']]['k'] !== $it['k']) fail('layout_invalid', 422);
            $kept[$it['id']] = true;
            continue;
        }
        $def = items()[$it['k']];
        if (!$def[7]) fail('not_on_sale', 422); // switched off in the admin panel: no new ones
        if ($def[2] > $r['level']) fail('level_locked', 422);
        if (!in_season($it['k'])) fail('out_of_season', 422); // seasonal pieces: only during their event (real calendar)
        $cost += $def[1];
        $it['id'] = $r['next_item_id']++;
    }
    unset($it);
    $refund = 0;
    foreach ($old as $id => $it) if (!isset($kept[$id])) $refund += (int)floor(items()[$it['k']][1] * rule('sell_back'));
    foreach (['floor', 'wall'] as $kind) {
        $k = (string)($b[$kind] ?? $r[$kind]);
        if ($k === $r[$kind]) continue;
        if (!isset(items()[$k]) || items()[$k][0] !== $kind) fail('layout_invalid', 422);
        if (!in_array($k, $r['finishes'], true)) {
            if (!items()[$k][7]) fail('not_on_sale', 422);
            if (items()[$k][2] > $r['level']) fail('level_locked', 422);
            $cost += items()[$k][1];
            $r['finishes'][] = $k;
        }
        $r[$kind] = $k;
    }
    spend_coins($r, $cost - $refund);
    $r['layout'] = $layout;
    refresh_popularity($r);
    return ['spent' => $cost, 'refund' => $refund];
}

/** Stores up to 5 reviews per report (never more than the customers served), keeps the latest 120. */
function save_reviews(int $uid, array $list, int $max): void
{
    $ins = db()->prepare('INSERT INTO ' . t('reviews') . ' (user_id, stars, dish, name, good, bad, created_at) VALUES (?,?,?,?,?,?,?)');
    $n = 0;
    foreach ($list as $rv) {
        if (!is_array($rv) || $n >= min(5, $max)) break;
        $stars = (int)($rv['stars'] ?? 0);
        $dish = (string)($rv['dish'] ?? '');
        if ($stars < 1 || $stars > 5 || !isset(DISHES[$dish])) continue;
        $name = preg_replace('/[^A-Za-zÀ-ÿ]/u', '', (string)($rv['name'] ?? '')) ?: 'Guest';
        $clean = fn($x) => implode(',', array_slice(array_values(array_intersect(array_map('strval', (array)$x), REVIEW_FACTORS)), 0, 4));
        $ins->execute([$uid, $stars, $dish, mb_substr($name, 0, 20), $clean($rv['good'] ?? []), $clean($rv['bad'] ?? []), now_utc()]);
        $n++;
    }
    if ($n) db()->prepare('DELETE FROM ' . t('reviews') . ' WHERE user_id = ? AND id < (SELECT id FROM (SELECT id FROM ' . t('reviews') . ' WHERE user_id = ? ORDER BY id DESC LIMIT 1 OFFSET 119) x)')->execute([$uid, $uid]);
}

/** Average stars of the latest REVIEW_WINDOW reviews, or null when there are none. */
function recent_review_average(int $uid): ?float
{
    $st = db()->prepare('SELECT AVG(stars) FROM (SELECT stars FROM ' . t('reviews') . ' WHERE user_id = ? ORDER BY id DESC LIMIT ' . REVIEW_WINDOW . ') x');
    $st->execute([$uid]);
    $v = $st->fetchColumn();
    return $v === null || $v === false ? null : (float)$v;
}

/** The statement: totals per day and kind (last 7 days) and the movements of one day. */
function ledger_out(int $uid, string $day): array
{
    $from = gmdate('Y-m-d 00:00:00', strtotime($day . ' UTC') - 6 * 86400);
    $st = db()->prepare('SELECT DATE(created_at) d, kind, SUM(amount) s, COUNT(*) n FROM ' . t('ledger') . ' WHERE user_id = ? AND created_at >= ? GROUP BY d, kind ORDER BY d');
    $st->execute([$uid, $from]);
    $days = [];
    foreach ($st->fetchAll() as $row) $days[$row['d']][$row['kind']] = ['sum' => (int)$row['s'], 'n' => (int)$row['n']];
    $st = db()->prepare('SELECT kind, amount, detail, created_at FROM ' . t('ledger') . ' WHERE user_id = ? AND created_at >= ? AND created_at < ? ORDER BY id DESC LIMIT 300');
    $st->execute([$uid, $day . ' 00:00:00', gmdate('Y-m-d 00:00:00', strtotime($day . ' UTC') + 86400)]);
    $rows = array_map(fn($x) => ['kind' => $x['kind'], 'amount' => (int)$x['amount'], 'detail' => $x['detail'], 'at' => strtotime($x['created_at'] . ' UTC')], $st->fetchAll());
    return ['day' => $day, 'days' => (object)$days, 'entries' => $rows];
}

function reviews_out(int $uid, int $limit = 30): array
{
    $st = db()->prepare('SELECT stars, dish, name, good, bad, created_at FROM ' . t('reviews') . ' WHERE user_id = ? ORDER BY id DESC LIMIT ' . $limit);
    $st->execute([$uid]);
    $rows = array_map(fn($r) => ['stars' => (int)$r['stars'], 'dish' => $r['dish'], 'name' => $r['name'], 'good' => array_filter(explode(',', $r['good'])), 'bad' => array_filter(explode(',', $r['bad'])), 'at' => strtotime($r['created_at'] . ' UTC')], $st->fetchAll());
    // Distribution and what customers praise or blame, over the reviews that make the reputation
    $st = db()->prepare('SELECT stars, good, bad FROM ' . t('reviews') . ' WHERE user_id = ? ORDER BY id DESC LIMIT ' . REVIEW_WINDOW);
    $st->execute([$uid]);
    $dist = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    $good = $bad = [];
    foreach ($st->fetchAll() as $d) {
        $dist[(int)$d['stars']]++;
        foreach (array_filter(explode(',', $d['good'])) as $f) $good[$f] = ($good[$f] ?? 0) + 1;
        foreach (array_filter(explode(',', $d['bad'])) as $f) $bad[$f] = ($bad[$f] ?? 0) + 1;
    }
    arsort($good);
    arsort($bad);
    $total = array_sum($dist);
    return ['reviews' => $rows, 'distribution' => $dist, 'count' => $total, 'window' => REVIEW_WINDOW, 'good' => (object)$good, 'bad' => (object)$bad, 'average' => $total ? round(array_sum(array_map(fn($s, $n) => $s * $n, array_keys($dist), $dist)) / $total, 2) : null];
}

function garden_rows(int $uid): array
{
    $st = db()->prepare('SELECT plot, crop, ready_at FROM ' . t('garden') . ' WHERE user_id = ? ORDER BY plot');
    $st->execute([$uid]);
    return array_map(fn($g) => ['plot' => (int)$g['plot'], 'crop' => $g['crop'], 'readyAt' => strtotime($g['ready_at'] . ' UTC')], $st->fetchAll());
}

function market_rows(int $uid): array
{
    $st = db()->prepare('SELECT ing, qty FROM ' . t('market_buys') . ' WHERE user_id = ? AND day = ?');
    $st->execute([$uid, today_utc()]);
    $bought = [];
    foreach ($st->fetchAll() as $row) $bought[$row['ing']] = (int)$row['qty'];
    return array_map(fn($ing) => ['ing' => $ing, 'price' => market_prices()[$ing], 'left' => max(0, rule('market_limit') - ($bought[$ing] ?? 0))], market_today());
}

function inbox_count(int $uid): int
{
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('gifts') . ' WHERE to_id = ? AND claimed_at IS NULL');
    $st->execute([$uid]);
    $n = (int)$st->fetchColumn();
    $st = db()->prepare('SELECT COUNT(*) FROM ' . t('friends') . ' WHERE friend_id = ? AND accepted = 0');
    $st->execute([$uid]);
    return $n + (int)$st->fetchColumn();
}

/** What a restaurant looks like from outside: shared with friends who visit (never the owner's email or name). */
function restaurant_public(array $r, array $recipes): array
{
    $menu = [];
    foreach ($r['menu'] as $d) if (isset($recipes[$d])) $menu[] = ['dish' => $d, 'level' => $recipes[$d]];
    return [
        'name' => $r['name'], 'level' => $r['level'], 'size' => $r['size'], 'depth' => $r['depth'], 'parking' => $r['parking'] ?? 0, 'floors' => $r['floors'] ?? [], 'door' => $r['door'], 'doorStyle' => $r['door_style'] ?? 'wood', 'floor' => $r['floor'], 'wall' => $r['wall'],
        'layout' => $r['layout'], 'staff' => $r['staff'], 'menu' => $menu, 'popularity' => $r['popularity'], 'stars' => stars_for($r['popularity']),
        'wish' => $r['wish'], 'status' => $r['status'] ?? 'open', 'kind' => $r['kind'] ?? 'generic',
        'rating' => $r['rating_n'] ? round($r['rating_sum'] / $r['rating_n'], 2) : null,
    ];
}

/** Everything the app needs about the player's own restaurant. */
function game_state(int $uid, ?array $away = null): array
{
    $r = load_restaurant($uid);
    $recipes = load_recipes($uid);
    return [
        'ok' => true,
        'now' => time(),
        'restaurant' => restaurant_public($r, $recipes) + [
                'coins' => $r['coins'], 'xp' => $r['xp'], 'finishes' => $r['finishes'], 'menuKeys' => $r['menu'], 'served' => $r['served_total'],
                'rating' => $r['rating_n'] ? round($r['rating_sum'] / $r['rating_n'], 2) : null,
                'friendRating' => $r['friend_n'] ? round($r['friend_sum'] / $r['friend_n'], 2) : null,
                'gameMinutes' => (int)($r['game_minutes'] ?? 540), 'payroll' => payroll($r), 'wages' => array_map(fn($st) => ['id' => $st['id'], 'wage' => wage($st)], $r['staff']),
                'maxWaiters' => max_waiters($r['level']), 'maxCleaners' => max_cleaners($r['level']), 'maxManagers' => max_managers($r['level']), 'maxCashiers' => max_cashiers($r['level']), 'managerHours' => manager_hours($r), 'maxMenu' => max_menu($r['level']), 'events' => events_out(), 'plots' => garden_plots($r['level']),
            ],
        'recipes' => (object)$recipes,
        'ingredients' => (object)load_ingredients($uid),
        'garden' => garden_rows($uid),
        'market' => market_rows($uid),
        'inbox' => inbox_count($uid),
        'away' => $away,
    ];
}
