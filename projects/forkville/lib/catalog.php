<?php
/**
 * Forkville — the game catalog: items, dishes, ingredients, crops, staff and progression rules.
 * The server is the only authority on prices and rules; the app receives this same data (game.catalog)
 * and uses it for the shop, the menu and the simulation, so the two never disagree.
 *
 * The constants below are the defaults. The admin panel stores changes in the `settings` table
 * (key 'catalog'); always read the catalog through items(), dishes(), crops(), market_prices(),
 * staff_costs(), sizes() and rule(), which apply those changes.
 */

declare(strict_types=1);

const START_COINS = 1500;
const MAX_LEVEL = 40;
const XP_PER_CUSTOMER = 2;
const XP_PER_HARVEST = 1;
const SELL_BACK = 0.5;            // furniture sells back at half price
const SEAT_SECONDS = 18;          // the fastest a customer can sit, order, eat and pay
const REPORT_WINDOW = 600;        // a report never counts more than 10 minutes of service
const OFFLINE_MAX = 3 * 3600;     // earnings while away: at most 3 hours…
const OFFLINE_RATE = 0.2;         // …at a fifth of the speed of live service
const OFFLINE_MIN = 300;          // nothing for absences under 5 minutes
const VISIT_COINS_HOST = 25;
const VISIT_COINS_BASE = 40;      // the visitor gets this + 5 per host level
const MARKET_DAILY_LIMIT = 5;     // pieces of each market ingredient per day

/**
 * The room grows one row (depth, towards the street) or one column (width) at a time: each side goes from 10 tiles
 * up to max_side(level), and every new strip costs more as the room gets bigger (expand_cost).
 */
/**
 * Upper floors: [level, cost] for floor 1, 2, 3, 4 (the fifth storey at most). A new floor starts small
 * (FLOOR_START × FLOOR_START, at the back left of the room) and grows like the ground floor, but never past
 * the floor below it. People go up by stairs (one floor) or by lift (every floor it fits on).
 */
const FLOORS = [1 => [14, 25000], 2 => [18, 45000], 3 => [22, 70000], 4 => [26, 100000]];
const FLOOR_START = 6;
/** Door styles: key => [price, level]. Moving the door elsewhere on the front wall costs DOOR_MOVE. */
const DOORS = ['wood' => [0, 1], 'rustic' => [300, 2], 'glass' => [450, 4], 'red' => [500, 5], 'double' => [800, 7], 'arched' => [1000, 10], 'french' => [1400, 13], 'gold' => [3200, 20],
    'wide_wood' => [1800, 8], 'wide_glass' => [2600, 12], 'wide_french' => [3800, 18], 'wide_grand' => [6500, 26]];
/** Doors two tiles wide (the door tile and the one at its right; both kept free inside and outside). */
const DOOR_WIDE = ['wide_wood', 'wide_glass', 'wide_french', 'wide_grand'];
function door_width(string $style): int
{
    return in_array($style, DOOR_WIDE, true) ? 2 : 1;
}
const DOOR_MOVE = 250;
/**
 * Seasonal events follow the real (Gregorian) calendar: seasonal pieces can be bought only while their event is
 * on. Once bought they stay, and can be moved like any other piece. Easter moves every year (see event_windows).
 */
const EVENTS = ['valentine', 'easter', 'summer', 'autumn', 'christmas'];
const SEASONAL = [
    'xmas_tree' => 'christmas', 'pumpkins' => 'autumn', 'heart_arch' => 'valentine', 'rose_vase' => 'valentine', 'heart_balloons' => 'valentine', 'cupid_statue' => 'valentine',
    'rug_heart' => 'valentine', 'easter_egg_big' => 'easter', 'bunny_statue' => 'easter', 'egg_basket' => 'easter', 'easter_tree' => 'easter', 'chick_planter' => 'easter',
    'lemonade_stand' => 'summer', 'surfboard' => 'summer', 'watermelon_cart' => 'summer', 'hammock' => 'summer', 'deck_chair' => 'summer', 'flamingo' => 'summer',
    'jack_lantern' => 'autumn', 'scarecrow' => 'autumn', 'hay_bales' => 'autumn', 'autumn_wreath' => 'autumn', 'friendly_ghost' => 'autumn', 'snowman' => 'christmas',
    'gift_pile' => 'christmas', 'nutcracker' => 'christmas', 'reindeer' => 'christmas', 'xmas_arch' => 'christmas', 'advent_candles' => 'christmas',
];

/** Easter Sunday of a year (Gregorian, Western churches): the anonymous Gregorian algorithm. */
function easter_sunday(int $y): int
{
    $a = $y % 19; $b = intdiv($y, 100); $c = $y % 100; $d = intdiv($b, 4); $e = $b % 4;
    $f = intdiv($b + 8, 25); $g = intdiv($b - $f + 1, 3); $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4); $k = $c % 4; $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7; $m = intdiv($a + 11 * $h + 22 * $l, 451);
    $month = intdiv($h + $l - 7 * $m + 114, 31); $day = (($h + $l - 7 * $m + 114) % 31) + 1;
    return gmmktime(0, 0, 0, $month, $day, $y);
}

/**
 * The windows of each event around a year: event => [[from, to], …] as Unix days (inclusive). Christmas runs into
 * January, so the previous and the next year are included too.
 */
function event_windows(int $year): array
{
    $day = fn(int $m, int $d, int $y) => intdiv(gmmktime(0, 0, 0, $m, $d, $y), 86400);
    $out = [];
    foreach ([$year - 1, $year, $year + 1] as $y) {
        $e = intdiv(easter_sunday($y), 86400);
        $out['valentine'][] = [$day(2, 1, $y), $day(2, 20, $y)];          // 1–20 February
        $out['easter'][] = [$e - 21, $e + 7];                              // three weeks before Easter to a week after
        $out['summer'][] = [$day(6, 15, $y), $day(9, 10, $y)];             // mid June to 10 September
        $out['autumn'][] = [$day(10, 1, $y), $day(11, 15, $y)];            // October to mid November (Halloween)
        $out['christmas'][] = [$day(12, 1, $y), $day(1, 10, $y + 1)];      // 1 December to 10 January
    }
    return $out;
}

/** The event going on at a moment (or null). */
function event_at(int $time): ?string
{
    $today = intdiv($time, 86400);
    foreach (event_windows((int)gmdate('Y', $time)) as $ev => $wins) foreach ($wins as [$a, $b]) if ($today >= $a && $today <= $b) return $ev;
    return null;
}

/** For the app: the event now, and for each event its current or next window (ISO dates). */
function events_out(): array
{
    $now = time();
    $today = intdiv($now, 86400);
    $out = ['now' => event_at($now), 'dates' => []];
    foreach (event_windows((int)gmdate('Y', $now)) as $ev => $wins) {
        foreach ($wins as [$a, $b]) if ($b >= $today) {
            $out['dates'][$ev] = ['from' => gmdate('Y-m-d', $a * 86400), 'to' => gmdate('Y-m-d', $b * 86400)];
            break;
        }
    }
    return $out;
}

/** Is a seasonal piece on sale now? (Half a day of slack either way for time zones.) */
function in_season(string $item): bool
{
    $ev = SEASONAL[$item] ?? null;
    if ($ev === null) return true;
    return event_at(time() - 43200) === $ev || event_at(time() + 43200) === $ev;
}
const MIN_SIDE = 10;
const MAX_SIDE = 26;
const EXPAND_RATE = 18;

function max_side(int $level): int
{
    return min(MAX_SIDE, MIN_SIDE + intdiv($level, 2));
}

/** Coins to grow a room of $w × $d by one row or column (the new area, priced more as the room gets bigger). */
function expand_cost(int $w, int $d, string $dir): int
{
    $area = $dir === 'col' ? ($w + 1) * $d : $w * ($d + 1);
    return (int)round(rule('expand_rate') * $area * (1 + max(0, $area - 100) / 200));
}

/**
 * The restaurant car park, west of the garden, along the street: [cost, level, spaces].
 * Guests who come by car order more (CAR_BONUS: +80% on the bill) and tip better.
 */
const PARKING = [1 => [4000, 10, 4], 2 => [9000, 17, 8], 3 => [18000, 24, 12]];
// Underground car park (floor −1): [cost, level, spaces]. Opens later, holds far more cars than the street one
const GARAGE = [1 => [30000, 18, 16], 2 => [55000, 21, 28], 3 => [90000, 24, 42], 4 => [140000, 27, 60], 5 => [200000, 30, 80]];
/** All the guests' parking spaces: street car park plus underground. */
function parking_spaces(array $r): int
{
    return (PARKING[$r['parking'] ?? 0][2] ?? 0) + (GARAGE[$r['garage'] ?? 0][2] ?? 0);
}
const CAR_BONUS = 0.8;
/** Tips a player can leave at a friend's restaurant while visiting: amounts, and at most this much per friend per day. */
const FRIEND_TIPS = [10, 25, 50, 100];
const FRIEND_TIP_DAILY = 200;

/** XP needed for level L: 50·L·(L−1) (L2 = 100, L3 = 300, L4 = 600…). */
/** XP to reach a level: steeper than before, so levels take longer (level 10 ≈ 9 000, 20 ≈ 47 000, 30 ≈ 157 000, 40 ≈ 343 000). */
function xp_for_level(int $level): int
{
    return (int)round(60 * $level * ($level - 1) * (1 + $level / 15));
}

function level_for_xp(int $xp): int
{
    $l = 1;
    while ($l < MAX_LEVEL && $xp >= xp_for_level($l + 1)) $l++;
    return $l;
}

/**
 * Items: key => [category, price, level, width, depth, popularity, extra]
 *   categories: table, chair, stove, counter, deco; floor and wall are finishes (no footprint).
 *   stove extra = cooking speed (1 = normal); chair/table extra = 0.
 */
const ITEMS = [
    // Tables
    'table_small' => ['table', 120, 1, 1, 1, 0, 0],
    'table_round' => ['table', 260, 5, 1, 1, 1, 0],
    'table_marble' => ['table', 520, 12, 1, 1, 3, 0],
    'table_long' => ['table', 480, 11, 2, 1, 2, 0],
    // Chairs
    'chair_wood' => ['chair', 50, 1, 1, 1, 0, 0],
    'chair_cushion' => ['chair', 130, 1, 1, 1, 1, 0],
    'stool_bar' => ['chair', 90, 1, 1, 1, 1, 0],
    'chair_velvet' => ['chair', 320, 8, 1, 1, 3, 0],
    // Kitchen
    'stove_basic' => ['stove', 350, 1, 1, 1, 0, 1.0],
    'stove_steel' => ['stove', 900, 8, 1, 1, 1, 1.35],
    'oven_brick' => ['stove', 1800, 16, 1, 1, 4, 1.7],
    'counter_wood' => ['counter', 200, 1, 1, 1, 0, 0],
    'counter_tile' => ['counter', 450, 4, 1, 1, 2, 0],
    // Decorations
    'plant_pot' => ['deco', 90, 1, 1, 1, 2, 0],
    'plant_tall' => ['deco', 180, 2, 1, 1, 4, 0],
    'lamp_floor' => ['deco', 220, 4, 1, 1, 4, 0],
    'bookshelf' => ['deco', 380, 9, 1, 1, 6, 0],
    'jukebox' => ['deco', 650, 14, 1, 1, 9, 0],
    'piano' => ['deco', 1400, 23, 2, 1, 18, 0],
    'aquarium' => ['deco', 900, 18, 2, 1, 12, 0],
    'fountain' => ['deco', 1600, 25, 1, 1, 20, 0],
    'statue_fork' => ['deco', 2400, 28, 1, 1, 28, 0],
    'clock_tall' => ['deco', 500, 12, 1, 1, 7, 0],
    'cake_stand' => ['deco', 300, 6, 1, 1, 5, 0],
    // More tables and chairs
    'table_picnic' => ['table', 340, 8, 2, 1, 1, 0],
    'table_bar' => ['table', 280, 6, 1, 1, 1, 0],
    'table_glass' => ['table', 720, 16, 1, 1, 4, 0],
    'chair_rattan' => ['chair', 160, 2, 1, 1, 2, 0],
    'bench_wood' => ['chair', 210, 3, 1, 1, 1, 0],
    'chair_modern' => ['chair', 240, 4, 1, 1, 2, 0],
    'chair_armchair' => ['chair', 420, 11, 1, 1, 4, 0],
    // Kitchen and service
    'stove_grill' => ['stove', 1300, 11, 1, 1, 2, 1.5],
    'fridge' => ['kitchen', 600, 2, 1, 1, 0, 0],
    'sink' => ['kitchen', 450, 1, 1, 1, 0, 0],
    'cash_register' => ['service', 380, 3, 1, 1, 1, 0],
    'trash_bin' => ['service', 120, 1, 1, 1, 0, 0],
    'cleaning_cart' => ['service', 260, 2, 1, 1, 0, 0],
    'cleaning_station' => ['service', 950, 12, 1, 1, 1, 0],
    'menu_board' => ['deco', 150, 1, 1, 1, 3, 0],
    // Lounge and fun
    'coffee_machine' => ['lounge', 500, 3, 1, 1, 2, 0],
    'sofa' => ['lounge', 650, 4, 2, 1, 4, 0],
    'tv' => ['deco', 900, 18, 1, 1, 6, 0],
    'arcade' => ['deco', 1100, 20, 1, 1, 9, 0],
    'gramophone' => ['deco', 800, 16, 1, 1, 7, 0],
    'fireplace' => ['deco', 2000, 27, 2, 1, 16, 0],
    'woodstove' => ['deco', 900, 17, 1, 1, 7, 0],
    'fireplace_stone' => ['deco', 1500, 23, 2, 1, 12, 0],
    'fireplace_modern' => ['deco', 2800, 29, 2, 1, 18, 0],
    'fireplace_marble' => ['deco', 4800, 30, 2, 1, 26, 0],
    'vending' => ['deco', 800, 17, 1, 1, 3, 0],
    // More decorations
    'cactus' => ['deco', 70, 1, 1, 1, 1, 0],
    'sunflowers' => ['deco', 160, 2, 1, 1, 3, 0],
    'coat_rack' => ['deco', 110, 1, 1, 1, 1, 0],
    'umbrella_stand' => ['deco', 90, 1, 1, 1, 1, 0],
    'candles' => ['deco', 200, 3, 1, 1, 3, 0],
    'globe' => ['deco', 300, 7, 1, 1, 4, 0],
    'lantern' => ['deco', 240, 4, 1, 1, 3, 0],
    'palm' => ['deco', 380, 9, 1, 1, 5, 0],
    'easel' => ['deco', 350, 8, 1, 1, 4, 0],
    'bonsai' => ['deco', 450, 11, 1, 1, 6, 0],
    'neon_sign' => ['deco', 700, 15, 1, 1, 8, 0],
    'trophy_case' => ['deco', 1200, 21, 1, 1, 12, 0],
    'flower_arch' => ['deco', 1500, 24, 2, 1, 18, 0],
    // Rugs: their own layer, people walk on them and furniture may stand on them
    'rug_round' => ['rug', 140, 1, 2, 2, 2, 0],
    'rug_stripes' => ['rug', 180, 2, 2, 1, 2, 0],
    'rug_red' => ['rug', 260, 5, 2, 2, 3, 0],
    'rug_checker' => ['rug', 420, 10, 2, 2, 4, 0],
    'rug_persian' => ['rug', 650, 14, 3, 2, 7, 0],
    // Equipment in tiers (budget → luxury) and different looks for the same tier
    'stove_retro' => ['stove', 420, 3, 1, 1, 1, 1.1],
    'stove_pro' => ['stove', 2400, 22, 1, 1, 3, 1.9],
    'stove_gold' => ['stove', 5200, 28, 1, 1, 8, 2.2],
    'counter_marble' => ['counter', 1100, 16, 1, 1, 4, 0],
    'counter_gold' => ['counter', 2600, 26, 1, 1, 8, 0],
    'fridge_retro' => ['kitchen', 900, 9, 1, 1, 2, 0],
    'fridge_pro' => ['kitchen', 1900, 21, 1, 1, 2, 0],
    'sink_steel' => ['kitchen', 1200, 13, 1, 1, 1, 0],
    'coffee_luxe' => ['lounge', 2200, 18, 1, 1, 6, 0],
    'table_rustic' => ['table', 150, 1, 1, 1, 0, 0],
    'chair_folding' => ['chair', 40, 1, 1, 1, 0, 0],
    'chair_bistro' => ['chair', 190, 3, 1, 1, 1, 0],
    // Outdoors: the terrace in front and the yard at the side (people queue on benches when no new guests are let in)
    'bench_park' => ['outdoor', 160, 2, 2, 1, 1, 0],
    'tree_oak' => ['outdoor', 200, 3, 1, 1, 3, 0],
    'tree_pine' => ['outdoor', 300, 6, 1, 1, 4, 0],
    'tree_cherry' => ['outdoor', 480, 11, 1, 1, 6, 0],
    'bush_round' => ['outdoor', 60, 1, 1, 1, 1, 0],
    'hedge' => ['outdoor', 110, 1, 2, 1, 2, 0],
    'planter_flowers' => ['outdoor', 120, 1, 1, 1, 2, 0],
    'street_lamp' => ['outdoor', 260, 5, 1, 1, 2, 0],
    'parasol' => ['outdoor', 340, 8, 1, 1, 3, 0],
    'menu_sign' => ['outdoor', 140, 2, 1, 1, 3, 0],
    'bike_rack' => ['outdoor', 110, 1, 1, 1, 1, 0],
    'gnome' => ['outdoor', 80, 1, 1, 1, 2, 0],
    'garden_fountain' => ['outdoor', 1400, 23, 1, 1, 12, 0],
    'food_truck' => ['outdoor', 6000, 30, 3, 2, 10, 0],
    // Restroom for the customers (without one they complain in their reviews)
    'toilet' => ['restroom', 300, 1, 1, 1, 0, 0],
    'toilet_luxe' => ['restroom', 950, 20, 1, 1, 2, 0],
    'washbasin' => ['restroom', 220, 1, 1, 1, 0, 0],
    'washbasin_marble' => ['restroom', 800, 17, 1, 1, 2, 0],
    'hand_dryer' => ['restroom', 150, 1, 1, 1, 0, 0],
    'toilet_cubicle' => ['restroom', 650, 5, 1, 1, 1, 0], // a cubicle with its own toilet inside
    'wc_sign' => ['restroom', 60, 1, 1, 1, 0, 0],
    // Partitions, screens and wall blocks (people walk around them)
    'wall_thin' => ['partition', 80, 1, 1, 1, 0, 0],
    'wall_medium' => ['partition', 120, 1, 1, 1, 0, 0],
    'wall_thick' => ['partition', 180, 2, 1, 1, 0, 0],
    'wall_long' => ['partition', 220, 4, 2, 1, 0, 0],
    'wall_brick_block' => ['partition', 200, 3, 1, 1, 1, 0],
    'half_wall_planter' => ['partition', 380, 9, 2, 1, 4, 0],
    'column' => ['partition', 250, 5, 1, 1, 2, 0],
    'screen_folding' => ['partition', 260, 5, 1, 1, 3, 0],
    'screen_bamboo' => ['partition', 300, 7, 1, 1, 3, 0],
    'glass_partition' => ['partition', 600, 13, 2, 1, 3, 0],
    // More plants and rugs inside
    'plant_monstera' => ['deco', 260, 5, 1, 1, 5, 0],
    'plant_ficus' => ['deco', 340, 8, 1, 1, 5, 0],
    'plant_stand' => ['deco', 200, 3, 1, 1, 3, 0],
    'plant_trough' => ['deco', 300, 7, 2, 1, 5, 0],
    'rug_jute' => ['rug', 220, 4, 2, 2, 2, 0],
    'rug_runner' => ['rug', 260, 5, 3, 1, 3, 0],
    'rug_large' => ['rug', 900, 19, 3, 3, 9, 0],
    // Floors
    // Stairs (to the floor above) and lifts (to every floor): they need an upper floor
    'stairs_wood' => ['stairs', 1500, 14, 1, 2, 2, 0],
    'stairs_stone' => ['stairs', 2600, 16, 1, 2, 4, 0],
    'stairs_spiral' => ['stairs', 3400, 18, 1, 1, 6, 0],
    'lift_glass' => ['stairs', 6000, 20, 1, 1, 4, 0],
    // Seasonal pieces: on sale only during their event (see SEASONAL), kept for ever once bought
    'heart_arch' => ['deco', 520, 2, 1, 1, 7, 0],
    'rose_vase' => ['deco', 260, 1, 1, 1, 4, 0],
    'heart_balloons' => ['deco', 180, 1, 1, 1, 3, 0],
    'cupid_statue' => ['deco', 700, 3, 1, 1, 8, 0],
    'rug_heart' => ['rug', 380, 1, 2, 2, 5, 0],
    'easter_egg_big' => ['deco', 450, 1, 1, 1, 6, 0],
    'bunny_statue' => ['deco', 380, 1, 1, 1, 5, 0],
    'egg_basket' => ['deco', 200, 1, 1, 1, 3, 0],
    'easter_tree' => ['deco', 320, 2, 1, 1, 5, 0],
    'chick_planter' => ['outdoor', 260, 1, 1, 1, 4, 0],
    'lemonade_stand' => ['outdoor', 650, 2, 1, 1, 6, 0],
    'surfboard' => ['deco', 300, 1, 1, 1, 4, 0],
    'watermelon_cart' => ['outdoor', 500, 2, 1, 1, 5, 0],
    'hammock' => ['outdoor', 700, 3, 2, 1, 7, 0],
    'deck_chair' => ['outdoor', 220, 1, 1, 1, 3, 0],
    'flamingo' => ['outdoor', 120, 1, 1, 1, 3, 0],
    'jack_lantern' => ['deco', 260, 1, 1, 1, 5, 0],
    'scarecrow' => ['outdoor', 420, 2, 1, 1, 6, 0],
    'hay_bales' => ['outdoor', 300, 1, 2, 1, 4, 0],
    'autumn_wreath' => ['deco', 240, 1, 1, 1, 4, 0],
    'friendly_ghost' => ['deco', 350, 2, 1, 1, 5, 0],
    'snowman' => ['outdoor', 300, 1, 1, 1, 6, 0],
    'gift_pile' => ['deco', 280, 1, 1, 1, 5, 0],
    'nutcracker' => ['deco', 450, 2, 1, 1, 6, 0],
    'reindeer' => ['outdoor', 600, 3, 1, 1, 7, 0],
    'xmas_arch' => ['outdoor', 800, 3, 1, 1, 8, 0],
    'advent_candles' => ['deco', 220, 1, 1, 1, 4, 0],
    // Cold service: the waiter takes drinks and ice creams straight from these (no cooking)
    'drinks_fridge' => ['kitchen', 700, 4, 1, 1, 1, 0],
    'gelato_freezer' => ['kitchen', 1100, 6, 1, 1, 3, 0],
    // Joints for the walls: corners, T and crosses (thin, medium and thick, like the straight pieces)
    'wall_corner_thin' => ['partition', 90, 1, 1, 1, 0, 0],
    'wall_corner' => ['partition', 130, 1, 1, 1, 0, 0],
    'wall_corner_thick' => ['partition', 190, 2, 1, 1, 0, 0],
    'wall_tee_thin' => ['partition', 110, 1, 1, 1, 0, 0],
    'wall_tee' => ['partition', 150, 2, 1, 1, 0, 0],
    'wall_tee_thick' => ['partition', 210, 3, 1, 1, 0, 0],
    'wall_cross_thin' => ['partition', 130, 2, 1, 1, 0, 0],
    'wall_cross' => ['partition', 170, 3, 1, 1, 0, 0],
    'floor_oak' => ['floor', 0, 1, 0, 0, 0, 0],
    'floor_checker' => ['floor', 400, 10, 0, 0, 4, 0],
    'floor_terracotta' => ['floor', 700, 15, 0, 0, 7, 0],
    'floor_mint' => ['floor', 1000, 20, 0, 0, 10, 0],
    'floor_marble' => ['floor', 2200, 27, 0, 0, 18, 0],
    'floor_blue' => ['floor', 900, 18, 0, 0, 8, 0],
    'floor_herringbone' => ['floor', 1500, 24, 0, 0, 12, 0],
    'floor_hex' => ['floor', 1900, 26, 0, 0, 15, 0],
    // Walls
    'wall_cream' => ['wall', 0, 1, 0, 0, 0, 0],
    'wall_stripes' => ['wall', 350, 8, 0, 0, 3, 0],
    'wall_brick' => ['wall', 650, 14, 0, 0, 6, 0],
    'wall_sage' => ['wall', 900, 18, 0, 0, 9, 0],
    'wall_berry' => ['wall', 1800, 25, 0, 0, 15, 0],
    'wall_tile' => ['wall', 800, 17, 0, 0, 8, 0],
    'wall_wood' => ['wall', 1200, 21, 0, 0, 11, 0],
    'wall_flowers' => ['wall', 1500, 24, 0, 0, 13, 0],
    // The big catalogue: many more tables, chairs, kitchen gear, lounge, decorations, rugs, walls and garden pieces
    'table_white' => ['table', 140, 2, 1, 1, 1, 0],
    'table_iron' => ['table', 230, 4, 1, 1, 2, 0],
    'table_oak' => ['table', 300, 7, 1, 1, 2, 0],
    'table_hex' => ['table', 380, 10, 1, 1, 3, 0],
    'table_mosaic' => ['table', 460, 11, 1, 1, 4, 0],
    'table_cafe' => ['table', 200, 3, 1, 1, 2, 0],
    'table_marble_round' => ['table', 900, 19, 1, 1, 6, 0],
    'table_gold' => ['table', 1800, 26, 1, 1, 9, 0],
    'table_rustic_long' => ['table', 620, 14, 2, 1, 3, 0],
    'table_banquet' => ['table', 1400, 23, 3, 1, 8, 0],
    'table_booth' => ['table', 700, 16, 2, 1, 3, 0],
    'table_picnic_blue' => ['table', 360, 9, 2, 1, 2, 0],
    'chair_white' => ['chair', 60, 1, 1, 1, 0, 0],
    'chair_bistro_red' => ['chair', 140, 2, 1, 1, 1, 0],
    'chair_ladder' => ['chair', 150, 2, 1, 1, 1, 0],
    'chair_wicker' => ['chair', 220, 4, 1, 1, 2, 0],
    'chair_tolix' => ['chair', 200, 3, 1, 1, 1, 0],
    'chair_ghost' => ['chair', 480, 12, 1, 1, 3, 0],
    'chair_leather' => ['chair', 650, 15, 1, 1, 4, 0],
    'chair_throne' => ['chair', 2200, 28, 1, 1, 10, 0],
    'chair_bamboo' => ['chair', 230, 4, 1, 1, 2, 0],
    'chair_egg' => ['chair', 900, 20, 1, 1, 6, 0],
    'stool_wood' => ['chair', 45, 1, 1, 1, 0, 0],
    'stool_cushion' => ['chair', 260, 6, 1, 1, 1, 0],
    'chair_booth' => ['chair', 520, 12, 1, 1, 2, 0],
    'bench_cushion' => ['chair', 320, 7, 1, 1, 2, 0],
    'stove_rustic' => ['stove', 500, 5, 1, 1, 0, 1.15],
    'stove_induction' => ['stove', 1600, 14, 1, 1, 1, 1.45],
    'wok_station' => ['stove', 2000, 19, 1, 1, 2, 1.6],
    'oven_pizza_steel' => ['stove', 3200, 25, 1, 1, 3, 1.85],
    'counter_steel' => ['counter', 600, 7, 1, 1, 1, 0],
    'counter_butcher' => ['counter', 800, 10, 1, 1, 2, 0],
    'counter_pastry' => ['counter', 1500, 19, 1, 1, 4, 0],
    'counter_bar' => ['counter', 950, 13, 1, 1, 3, 0],
    'fridge_double' => ['kitchen', 1500, 17, 1, 1, 1, 0],
    'freezer_chest' => ['kitchen', 700, 6, 1, 1, 0, 0],
    'dishwasher' => ['kitchen', 900, 9, 1, 1, 0, 0],
    'spice_rack' => ['kitchen', 180, 3, 1, 1, 1, 0],
    'shelf_pots' => ['kitchen', 260, 5, 1, 1, 1, 0],
    'mixer_station' => ['kitchen', 650, 14, 1, 1, 1, 0],
    'host_stand' => ['service', 340, 7, 1, 1, 2, 0], // the manager's station (MANAGER_LEVEL)
    'register_modern' => ['service', 900, 19, 1, 1, 2, 0],
    'trash_recycle' => ['service', 260, 5, 1, 1, 1, 0],
    'water_station' => ['service', 300, 7, 1, 1, 2, 0],
    'bean_bags' => ['lounge', 260, 6, 1, 1, 2, 0],
    'sofa_leather' => ['lounge', 1400, 23, 2, 1, 6, 0],
    'sofa_corner' => ['lounge', 1800, 26, 2, 2, 7, 0],
    'espresso_bar' => ['lounge', 1600, 24, 1, 1, 4, 0],
    'water_cooler' => ['lounge', 220, 4, 1, 1, 0, 0],
    'radio_retro' => ['deco', 350, 8, 1, 1, 3, 0],
    'guitar_stand' => ['deco', 450, 11, 1, 1, 4, 0],
    'drum_kit' => ['deco', 1300, 22, 1, 1, 8, 0],
    'harp' => ['deco', 2600, 29, 1, 1, 14, 0],
    'dj_booth' => ['deco', 3200, 30, 2, 1, 12, 0],
    'piano_grand' => ['deco', 5200, 30, 2, 2, 26, 0],
    'pinball' => ['deco', 1000, 20, 1, 1, 7, 0],
    'foosball' => ['deco', 900, 18, 2, 1, 6, 0],
    'dartboard' => ['deco', 260, 6, 1, 1, 3, 0],
    'pool_table' => ['deco', 2200, 27, 2, 1, 10, 0],
    'board_games' => ['deco', 300, 6, 1, 1, 3, 0],
    'claw_machine' => ['deco', 1200, 22, 1, 1, 8, 0],
    'stove_tile' => ['deco', 1700, 25, 1, 1, 12, 0],
    'electric_fire' => ['deco', 600, 13, 1, 1, 5, 0],
    'popcorn_machine' => ['deco', 700, 15, 1, 1, 4, 0],
    'ice_cream_cart' => ['deco', 1400, 22, 1, 1, 7, 0],
    'candy_jars' => ['deco', 380, 9, 1, 1, 3, 0],
    'plant_snake' => ['deco', 140, 1, 1, 1, 2, 0],
    'plant_olive' => ['deco', 420, 10, 1, 1, 5, 0],
    'plant_fern' => ['deco', 180, 3, 1, 1, 3, 0],
    'plant_lemon' => ['deco', 520, 12, 1, 1, 6, 0],
    'plant_bamboo' => ['deco', 300, 7, 1, 1, 4, 0],
    'plant_orchid' => ['deco', 260, 6, 1, 1, 4, 0],
    'plant_hanging' => ['deco', 230, 4, 1, 1, 3, 0],
    'plant_lavender' => ['deco', 200, 3, 1, 1, 3, 0],
    'terrarium' => ['deco', 320, 7, 1, 1, 4, 0],
    'cactus_trio' => ['deco', 120, 1, 1, 1, 2, 0],
    'statue_bust' => ['deco', 900, 19, 1, 1, 9, 0],
    'vase_tall' => ['deco', 380, 9, 1, 1, 5, 0],
    'mirror_standing' => ['deco', 500, 12, 1, 1, 5, 0],
    'telescope' => ['deco', 700, 16, 1, 1, 6, 0],
    'armor_suit' => ['deco', 1600, 25, 1, 1, 12, 0],
    'bird_cage' => ['deco', 450, 11, 1, 1, 5, 0],
    'china_cabinet' => ['deco', 1100, 21, 1, 1, 9, 0],
    'wine_rack' => ['deco', 800, 17, 1, 1, 7, 0],
    'wine_barrels' => ['deco', 650, 14, 1, 1, 6, 0],
    'magazine_rack' => ['deco', 160, 2, 1, 1, 2, 0],
    'record_shelf' => ['deco', 600, 13, 1, 1, 6, 0],
    'flower_cart' => ['deco', 1200, 22, 2, 1, 10, 0],
    'xmas_tree' => ['deco', 500, 1, 1, 1, 8, 0],
    'pumpkins' => ['deco', 120, 1, 1, 1, 3, 0],
    'fishbowl' => ['deco', 280, 6, 1, 1, 3, 0],
    'sculpture' => ['deco', 1900, 26, 1, 1, 14, 0],
    'lamp_arc' => ['deco', 600, 13, 1, 1, 5, 0],
    'lamp_tiffany' => ['deco', 750, 16, 1, 1, 6, 0],
    'lamp_paper' => ['deco', 260, 5, 1, 1, 3, 0],
    'lamp_tripod' => ['deco', 380, 9, 1, 1, 4, 0],
    'candelabra_floor' => ['deco', 900, 19, 1, 1, 7, 0],
    'rug_oval' => ['rug', 200, 3, 2, 1, 2, 0],
    'rug_zigzag' => ['rug', 380, 9, 2, 2, 4, 0],
    'rug_sun' => ['rug', 450, 11, 2, 2, 5, 0],
    'rug_kilim' => ['rug', 420, 10, 2, 1, 4, 0],
    'rug_green' => ['rug', 300, 7, 2, 2, 3, 0],
    'rug_star' => ['rug', 520, 12, 2, 2, 5, 0],
    'rug_hall' => ['rug', 700, 16, 4, 1, 6, 0],
    'rug_shag' => ['rug', 600, 13, 2, 2, 6, 0],
    'toilet_gold' => ['restroom', 2400, 28, 1, 1, 4, 0],
    'washbasin_bowl' => ['restroom', 650, 14, 1, 1, 2, 0],
    'mirror_led' => ['restroom', 700, 16, 1, 1, 3, 0],
    'towel_rack' => ['restroom', 120, 1, 1, 1, 1, 0],
    'baby_change' => ['restroom', 400, 10, 1, 1, 2, 0],
    'soap_station' => ['restroom', 90, 1, 1, 1, 1, 0],
    'wall_stone_block' => ['partition', 260, 6, 1, 1, 1, 0],
    'wall_glass_brick' => ['partition', 380, 10, 1, 1, 2, 0],
    'lattice_screen' => ['partition', 300, 7, 1, 1, 3, 0],
    'curtain_red' => ['partition', 340, 8, 1, 1, 4, 0],
    'bookshelf_divider' => ['partition', 700, 15, 2, 1, 6, 0],
    'screen_shoji' => ['partition', 420, 10, 1, 1, 4, 0],
    'wall_ivy' => ['partition', 600, 14, 1, 1, 6, 0],
    'half_wall_wood' => ['partition', 280, 6, 2, 1, 2, 0],
    'rope_barrier' => ['partition', 350, 9, 1, 1, 3, 0],
    'arch_flowers' => ['partition', 900, 19, 1, 1, 9, 0],
    'lamp_lantern_post' => ['outdoor', 300, 6, 1, 1, 2, 0],
    'lamp_double' => ['outdoor', 480, 11, 1, 1, 3, 0],
    'lamp_bollard' => ['outdoor', 120, 1, 1, 1, 1, 0],
    'lamp_string' => ['outdoor', 380, 9, 2, 1, 4, 0],
    'lamp_torch' => ['outdoor', 160, 2, 1, 1, 2, 0],
    'lamp_modern' => ['outdoor', 560, 13, 1, 1, 3, 0],
    'lamp_victorian' => ['outdoor', 900, 19, 1, 1, 6, 0],
    'heater_patio' => ['outdoor', 700, 15, 1, 1, 3, 0],
    'flower_bed' => ['outdoor', 260, 5, 2, 1, 4, 0],
    'pond' => ['outdoor', 1200, 21, 2, 2, 10, 0],
    'swing_garden' => ['outdoor', 900, 19, 2, 1, 7, 0],
    'bird_bath' => ['outdoor', 240, 4, 1, 1, 3, 0],
    'statue_garden' => ['outdoor', 700, 16, 1, 1, 6, 0],
    'wheelbarrow' => ['outdoor', 220, 4, 1, 1, 3, 0],
    'pergola' => ['outdoor', 1800, 26, 2, 2, 12, 0],
    'rock_garden' => ['outdoor', 650, 15, 2, 1, 6, 0],
    'bamboo_clump' => ['outdoor', 320, 8, 1, 1, 4, 0],
    'tree_maple' => ['outdoor', 420, 10, 1, 1, 5, 0],
    'tree_birch' => ['outdoor', 360, 9, 1, 1, 4, 0],
    'tree_palm' => ['outdoor', 620, 14, 1, 1, 6, 0],
    'bush_hydrangea' => ['outdoor', 140, 2, 1, 1, 2, 0],
    'topiary' => ['outdoor', 380, 10, 1, 1, 4, 0],
    'mailbox' => ['outdoor', 90, 1, 1, 1, 1, 0],
    'bicycle' => ['outdoor', 200, 3, 1, 1, 2, 0],
    'slide_kids' => ['outdoor', 800, 17, 1, 2, 5, 0],
    'sandbox' => ['outdoor', 350, 8, 2, 1, 3, 0],
    'bench_stone' => ['outdoor', 300, 7, 2, 1, 2, 0],
    'parasol_big' => ['outdoor', 600, 13, 1, 1, 4, 0],
    'fountain_wall' => ['outdoor', 1100, 21, 1, 1, 9, 0],
    'beehive' => ['outdoor', 300, 7, 1, 1, 3, 0],
    'floor_walnut' => ['floor', 1300, 22, 0, 0, 11, 0],
    'floor_slate' => ['floor', 1600, 24, 0, 0, 13, 0],
    'floor_terrazzo' => ['floor', 1100, 20, 0, 0, 9, 0],
    'floor_diagonal' => ['floor', 800, 16, 0, 0, 6, 0],
    'floor_cork' => ['floor', 500, 12, 0, 0, 5, 0],
    'floor_mosaic' => ['floor', 2000, 27, 0, 0, 16, 0],
    'floor_gold_parquet' => ['floor', 3000, 29, 0, 0, 22, 0],
    'floor_concrete' => ['floor', 600, 13, 0, 0, 4, 0],
    'floor_red_tiles' => ['floor', 900, 18, 0, 0, 7, 0],
    'wall_navy' => ['wall', 700, 15, 0, 0, 6, 0],
    'wall_mint_stripes' => ['wall', 600, 13, 0, 0, 5, 0],
    'wall_stone' => ['wall', 1400, 22, 0, 0, 11, 0],
    'wall_gold_damask' => ['wall', 2800, 29, 0, 0, 20, 0],
    'wall_greenery' => ['wall', 1600, 25, 0, 0, 13, 0],
    'wall_chalk' => ['wall', 800, 17, 0, 0, 7, 0],
    'wall_tropical' => ['wall', 1300, 22, 0, 0, 10, 0],
    'wall_concrete' => ['wall', 500, 12, 0, 0, 4, 0],
    'wall_pastel' => ['wall', 400, 10, 0, 0, 3, 0],
    // Windows (own layer, on the inner edge of an outer wall; the rotation says which wall): width in tiles
    'window_small' => ['window', 120, 1, 1, 1, 1, 0],
    'window_round' => ['window', 200, 3, 1, 1, 2, 0],
    'window_wide' => ['window', 260, 4, 2, 1, 2, 0],
    'window_tall' => ['window', 240, 6, 1, 1, 2, 0],
    'window_large' => ['window', 420, 8, 3, 1, 3, 0],
    'window_arched' => ['window', 380, 11, 1, 1, 3, 0],
    'glass_wall_2' => ['window', 760, 10, 2, 1, 4, 0],
    'window_stained' => ['window', 650, 17, 1, 1, 4, 0],
    'glass_wall_3' => ['window', 1150, 15, 3, 1, 6, 0],
    'glass_wall_4' => ['window', 1600, 20, 4, 1, 8, 0],
    'window_panorama' => ['window', 2600, 31, 4, 1, 12, 0],
    // Outdoor paving (own layer, like rugs: pieces stand on it, people walk on it, also on the path to the door)
    'ground_grass' => ['ground', 20, 1, 1, 1, 0, 0],
    'ground_gravel' => ['ground', 25, 1, 1, 1, 0, 0],
    'ground_stone' => ['ground', 45, 2, 1, 1, 0, 0],
    'ground_deck' => ['ground', 60, 3, 1, 1, 0, 0],
    'ground_brick' => ['ground', 55, 5, 1, 1, 0, 0],
    'ground_terracotta' => ['ground', 70, 8, 1, 1, 0, 0],
    'ground_flowers' => ['ground', 90, 11, 1, 1, 1, 0],
    'ground_mosaic' => ['ground', 140, 16, 1, 1, 1, 0],
    'ground_marble' => ['ground', 220, 26, 1, 1, 1, 0],
    // Later levels (up to 40)
    'table_glass' => ['table', 900, 21, 1, 1, 4, 0],
    'table_banquet' => ['table', 1600, 31, 2, 1, 6, 0],
    'table_gold_round' => ['table', 2600, 35, 1, 1, 9, 0],
    'chair_designer' => ['chair', 700, 24, 1, 1, 5, 0],
    'chair_royal' => ['chair', 3000, 36, 1, 1, 12, 0],
    'stove_teppanyaki' => ['stove', 4200, 31, 1, 1, 4, 2.0],
    'stove_master' => ['stove', 7000, 37, 1, 1, 6, 2.4],
    'counter_marble' => ['counter', 2400, 27, 1, 1, 5, 0],
    'counter_chef_table' => ['counter', 3600, 33, 1, 1, 7, 0],
    'knife_rack' => ['kitchen', 240, 4, 1, 1, 1, 0],
    'herb_wall' => ['kitchen', 900, 15, 1, 1, 3, 0],
    'sous_vide' => ['kitchen', 1200, 21, 1, 1, 1, 0],
    'blast_chiller' => ['kitchen', 2200, 29, 1, 1, 1, 0],
    'menu_tablet' => ['service', 1400, 28, 1, 1, 3, 0],
    'dish_lift' => ['service', 2400, 14, 1, 1, 0, 0], // a dumbwaiter: chefs upstairs send dishes down (sim.js sendByLift)
    'armchair_wing' => ['lounge', 1100, 20, 1, 1, 5, 0],
    'bonsai' => ['deco', 600, 14, 1, 1, 6, 0],
    'wine_wall' => ['deco', 2800, 30, 2, 1, 12, 0],
    'chandelier_crystal' => ['deco', 4200, 32, 1, 1, 18, 0],
    'sculpture_modern' => ['deco', 3400, 34, 1, 1, 14, 0],
    'aquarium_wall' => ['deco', 6500, 38, 2, 1, 24, 0],
    'planter_box_long' => ['outdoor', 300, 4, 2, 1, 3, 0],
    'trellis_roses' => ['outdoor', 520, 9, 1, 1, 5, 0],
    'olive_tree_big' => ['outdoor', 900, 18, 1, 1, 7, 0],
    'fire_pit' => ['outdoor', 1100, 22, 1, 1, 6, 0],
    'outdoor_bar' => ['outdoor', 2600, 29, 2, 1, 9, 0],
    'gazebo' => ['outdoor', 4500, 33, 2, 2, 14, 0],
    'water_wall' => ['outdoor', 3800, 36, 1, 1, 15, 0],
    'floor_black_marble' => ['floor', 4200, 34, 0, 0, 26, 0],
    'floor_terrazzo' => ['floor', 2200, 27, 0, 0, 16, 0],
    'wall_velvet' => ['wall', 3600, 33, 0, 0, 24, 0],
    'wall_wood_panels' => ['wall', 2000, 26, 0, 0, 14, 0],
];

/**
 * Quality tier of equipment and furniture: 1 budget, 2 standard, 3 premium, 4 luxury.
 * Stoves, fridges and counters make better dishes; sinks wash faster; chairs and tables are more comfortable.
 */
const ITEM_TIERS = [
    'table_glass' => 3, 'table_banquet' => 4, 'table_gold_round' => 4, 'chair_designer' => 3, 'chair_royal' => 4, 'stove_teppanyaki' => 4, 'stove_master' => 4, 'counter_marble' => 4, 'counter_chef_table' => 4,
    'stove_basic' => 1, 'stove_retro' => 1, 'stove_steel' => 2, 'stove_grill' => 3, 'oven_brick' => 3, 'stove_pro' => 3, 'stove_gold' => 4,
    'counter_wood' => 1, 'counter_tile' => 2, 'counter_marble' => 3, 'counter_gold' => 4,
    'drinks_fridge' => 2, 'gelato_freezer' => 3, 'fridge' => 1, 'fridge_retro' => 2, 'fridge_pro' => 3, 'sink' => 1, 'sink_steel' => 3, 'coffee_machine' => 2, 'coffee_luxe' => 4,
    'table_small' => 1, 'table_rustic' => 1, 'table_picnic' => 1, 'table_round' => 2, 'table_bar' => 2, 'table_long' => 2, 'table_marble' => 3, 'table_glass' => 4,
    'chair_wood' => 1, 'chair_folding' => 1, 'stool_bar' => 1, 'bench_wood' => 1, 'chair_cushion' => 2, 'chair_rattan' => 2, 'chair_bistro' => 2,
    'chair_modern' => 3, 'chair_velvet' => 3, 'chair_armchair' => 4,
    'cleaning_cart' => 1, 'cleaning_station' => 3, 'toilet' => 1, 'toilet_cubicle' => 2, 'toilet_luxe' => 3, 'washbasin' => 1, 'washbasin_marble' => 3,
    'woodstove' => 1, 'fireplace_stone' => 2, 'fireplace' => 2, 'fireplace_modern' => 3, 'fireplace_marble' => 4,
    'table_white' => 1, 'table_iron' => 2, 'table_oak' => 2, 'table_hex' => 2, 'table_mosaic' => 3, 'table_cafe' => 1, 'table_marble_round' => 3, 'table_gold' => 4,
    'table_rustic_long' => 2, 'table_banquet' => 3, 'table_booth' => 2, 'table_picnic_blue' => 1, 'chair_white' => 1, 'chair_bistro_red' => 2, 'chair_ladder' => 2, 'chair_wicker' => 2,
    'chair_tolix' => 2, 'chair_ghost' => 3, 'chair_leather' => 4, 'chair_throne' => 4, 'chair_bamboo' => 2, 'chair_egg' => 4, 'stool_wood' => 1, 'stool_cushion' => 3,
    'chair_booth' => 3, 'bench_cushion' => 2, 'stove_rustic' => 1, 'stove_induction' => 3, 'wok_station' => 3, 'oven_pizza_steel' => 4, 'counter_steel' => 2, 'counter_butcher' => 3,
    'counter_pastry' => 3, 'counter_bar' => 2, 'fridge_double' => 3, 'freezer_chest' => 2, 'dishwasher' => 2, 'espresso_bar' => 3, 'water_cooler' => 1, 'stove_tile' => 2,
    'electric_fire' => 1, 'toilet_gold' => 4, 'washbasin_bowl' => 2, 'mirror_led' => 3,
];

/** What some items do in the live service (the simulation in assets/sim.js reads these tags). */
const ITEM_TAGS = [
    'fridge' => 'fridge', 'sink' => 'sink', 'cash_register' => 'register', 'trash_bin' => 'trash', 'cleaning_cart' => 'janitor', 'cleaning_station' => 'janitor', 'host_stand' => 'host',
    'coffee_machine' => 'coffee', 'coffee_luxe' => 'coffee', 'fridge_retro' => 'fridge', 'fridge_pro' => 'fridge', 'sink_steel' => 'sink', 'sofa' => 'rest', 'jukebox' => 'music', 'gramophone' => 'music', 'piano' => 'music',
    'tv' => 'fun', 'arcade' => 'fun', 'fireplace' => 'cozy', 'woodstove' => 'cozy', 'fireplace_stone' => 'cozy', 'fireplace_modern' => 'cozy', 'fireplace_marble' => 'cozy', 'vending' => 'snack',
    'toilet' => 'toilet', 'toilet_luxe' => 'toilet', 'washbasin' => 'washbasin', 'washbasin_marble' => 'washbasin', 'hand_dryer' => 'dryer',
    'bench_park' => 'queue', 'menu_sign' => 'attract', 'food_truck' => 'truck', 'street_lamp' => 'light',
    'lamp_floor' => 'light', 'lantern' => 'light', 'neon_sign' => 'light', 'candles' => 'light',
    'stairs_wood' => 'stairs', 'stairs_stone' => 'stairs', 'stairs_spiral' => 'stairs', 'lift_glass' => 'lift', 'drinks_fridge' => 'drinks', 'gelato_freezer' => 'icecream', 'toilet_cubicle' => 'toilet',
    'lemonade_stand' => 'snack', 'jack_lantern' => 'light', 'friendly_ghost' => 'light', 'xmas_arch' => 'light', 'advent_candles' => 'light',
    'fridge_double' => 'fridge', 'freezer_chest' => 'fridge', 'dishwasher' => 'sink', 'register_modern' => 'register', 'trash_recycle' => 'trash', 'bean_bags' => 'rest', 'sofa_leather' => 'rest', 'sofa_corner' => 'rest',
    'espresso_bar' => 'coffee', 'water_cooler' => 'coffee', 'radio_retro' => 'music', 'guitar_stand' => 'music', 'drum_kit' => 'music', 'harp' => 'music', 'dj_booth' => 'music', 'piano_grand' => 'music',
    'pinball' => 'fun', 'foosball' => 'fun', 'dartboard' => 'fun', 'pool_table' => 'fun', 'board_games' => 'fun', 'claw_machine' => 'fun', 'stove_tile' => 'cozy', 'electric_fire' => 'cozy',
    'popcorn_machine' => 'snack', 'ice_cream_cart' => 'snack', 'candy_jars' => 'snack', 'lamp_arc' => 'light', 'lamp_tiffany' => 'light', 'lamp_paper' => 'light', 'lamp_tripod' => 'light', 'candelabra_floor' => 'light',
    'toilet_gold' => 'toilet', 'washbasin_bowl' => 'washbasin', 'mirror_led' => 'washbasin', 'towel_rack' => 'dryer', 'lamp_lantern_post' => 'light', 'lamp_double' => 'light', 'lamp_bollard' => 'light', 'lamp_string' => 'light',
    'lamp_torch' => 'light', 'lamp_modern' => 'light', 'lamp_victorian' => 'light', 'heater_patio' => 'light', 'bench_stone' => 'queue',
    'chandelier_crystal' => 'light', 'fire_pit' => 'light', 'armchair_wing' => 'rest', 'outdoor_bar' => 'snack',
];

/** Ingredients: key => source ('garden' or 'market'). */
const INGREDIENTS = [
    'tomato' => 'garden', 'basil' => 'garden', 'potato' => 'garden', 'onion' => 'garden', 'garlic' => 'garden',
    'carrot' => 'garden', 'lemon' => 'garden', 'strawberry' => 'garden', 'pepper' => 'garden', 'mushroom' => 'garden',
    'flour' => 'market', 'cheese' => 'market', 'egg' => 'market', 'milk' => 'market', 'rice' => 'market',
    'fish' => 'market', 'beef' => 'market', 'chicken' => 'market', 'chocolate' => 'market', 'coffee' => 'market',
    'olive' => 'market', 'sugar' => 'market',
    'lettuce' => 'garden', 'corn' => 'garden', 'eggplant' => 'garden', 'pumpkin' => 'garden',
    'shrimp' => 'market', 'pork' => 'market', 'cream' => 'market', 'butter' => 'market', 'honey' => 'market', 'banana' => 'market', 'beans' => 'market', 'tea' => 'market',
];

/** Garden crops: ingredient => [seed cost, grow seconds, yield, level]. */
const CROPS = [
    'tomato' => [10, 120, 3, 1],
    'basil' => [8, 90, 3, 1],
    'potato' => [12, 300, 3, 1],
    'onion' => [12, 240, 3, 2],
    'carrot' => [14, 360, 3, 2],
    'garlic' => [18, 600, 3, 3],
    'lemon' => [25, 900, 2, 3],
    'strawberry' => [22, 720, 3, 4],
    'pepper' => [24, 1200, 3, 5],
    'mushroom' => [30, 1800, 3, 6],
    'lettuce' => [10, 150, 3, 2],
    'corn' => [16, 480, 3, 3],
    'eggplant' => [22, 900, 3, 5],
    'pumpkin' => [28, 1500, 2, 5],
];

/** Market prices of the market ingredients (the stall sells MARKET_SLOTS of them each day). */
const MARKET_PRICES = [
    'flour' => 14, 'cheese' => 22, 'egg' => 12, 'milk' => 12, 'rice' => 16, 'fish' => 34, 'beef' => 36,
    'chicken' => 28, 'chocolate' => 30, 'coffee' => 20, 'olive' => 15, 'sugar' => 10,
    'shrimp' => 32, 'pork' => 30, 'cream' => 14, 'butter' => 12, 'honey' => 18, 'banana' => 16, 'beans' => 10, 'tea' => 14,
];
const MARKET_SLOTS = 7;

/**
 * Dishes: key => [base price, cook seconds, eat seconds, level, [3 ingredients]]
 * Cook seconds is the dish's own base time (a salad is quick, a roast is slow): the live service shortens it with
 * the stove's speed, the chef's skill and trait, the fridges and the kitchen equipment (sim.js cookTime()).
 * Learning a dish costs 1 of each ingredient; raising it to level L (2..5) costs L−1 of each.
 * Price at level L = base × (1 + 0.25·(L−1)).
 */
const DISHES = [
    'bruschetta' => [12, 5, 8, 1, ['tomato', 'basil', 'olive']],
    'fries' => [10, 7, 7, 1, ['potato', 'olive', 'onion']],
    'tomato_soup' => [15, 12, 9, 2, ['tomato', 'onion', 'garlic']],
    'lemonade' => [8, 2, 5, 4, ['lemon', 'sugar', 'strawberry']],
    'cappuccino' => [9, 3, 5, 4, ['coffee', 'milk', 'sugar']],
    'omelette' => [18, 6, 9, 5, ['egg', 'cheese', 'onion']],
    'pasta_tomato' => [22, 11, 10, 6, ['flour', 'tomato', 'basil']],
    'pizza' => [28, 14, 11, 8, ['flour', 'tomato', 'cheese']],
    'carrot_cake' => [20, 16, 8, 11, ['carrot', 'flour', 'egg']],
    'burger' => [32, 10, 11, 11, ['beef', 'flour', 'onion']],
    'risotto' => [30, 20, 11, 16, ['rice', 'mushroom', 'cheese']],
    'strawberry_tart' => [26, 15, 8, 14, ['strawberry', 'flour', 'sugar']],
    'grilled_fish' => [38, 13, 12, 22, ['fish', 'lemon', 'garlic']],
    'stuffed_peppers' => [34, 17, 11, 19, ['pepper', 'rice', 'beef']],
    'roast_chicken' => [42, 24, 13, 24, ['chicken', 'potato', 'garlic']],
    'chocolate_cake' => [36, 18, 9, 21, ['chocolate', 'egg', 'milk']],
    'caprese' => [16, 3, 7, 3, ['tomato', 'cheese', 'basil']],
    'garden_salad' => [12, 3, 7, 4, ['lettuce', 'tomato', 'olive']],
    'iced_tea' => [7, 2, 5, 3, ['tea', 'lemon', 'sugar']],
    'pancakes' => [14, 6, 8, 6, ['flour', 'egg', 'honey']],
    'milkshake' => [12, 3, 6, 5, ['milk', 'strawberry', 'cream']],
    'corn_soup' => [15, 12, 9, 7, ['corn', 'cream', 'onion']],
    'banana_split' => [16, 4, 7, 9, ['banana', 'cream', 'chocolate']],
    'tacos' => [24, 8, 9, 13, ['beef', 'corn', 'beans']],
    'pumpkin_soup' => [22, 13, 9, 12, ['pumpkin', 'cream', 'onion']],
    'veggie_lasagna' => [30, 22, 11, 17, ['flour', 'eggplant', 'cheese']],
    'cream_puffs' => [28, 14, 9, 15, ['flour', 'butter', 'cream']],
    'shrimp_pasta' => [36, 12, 11, 21, ['shrimp', 'flour', 'garlic']],
    'parmigiana' => [32, 19, 11, 18, ['eggplant', 'tomato', 'cheese']],
    'pork_ribs' => [40, 26, 13, 23, ['pork', 'honey', 'garlic']],
    'tiramisu' => [34, 9, 9, 20, ['coffee', 'cream', 'egg']],
    'sushi' => [44, 10, 11, 24, ['fish', 'rice', 'shrimp']],
    'paella' => [46, 25, 13, 25, ['rice', 'shrimp', 'pepper']],
    'steak_fries' => [52, 15, 13, 26, ['beef', 'potato', 'butter']],
    'gelato' => [14, 3, 6, 5, ['cream', 'sugar', 'strawberry']],
    'cold_brew' => [10, 2, 5, 6, ['coffee', 'milk', 'sugar']],
    'sorbet' => [12, 3, 5, 7, ['lemon', 'sugar', 'strawberry']],
];
const DISH_MAX_LEVEL = 5;
/** Cold dishes: with a drinks fridge or a gelato freezer the waiter serves them straight away (no cooking). */
const DISH_KINDS = ['lemonade' => 'drink', 'iced_tea' => 'drink', 'milkshake' => 'drink', 'cold_brew' => 'drink',
    'banana_split' => 'icecream', 'gelato' => 'icecream', 'sorbet' => 'icecream'];
const REVIEW_WINDOW = 50; // reviews that make the reputation

/** The restaurant's title as it grows: [from level, key] (the app translates the key). */
const LEVEL_TITLES = [[1, 'kiosk'], [3, 'snack_bar'], [5, 'trattoria'], [8, 'bistro'], [11, 'restaurant'], [15, 'fine_dining'], [20, 'gourmet'], [25, 'renowned'], [30, 'star_house'], [35, 'legend']];
/** What a review can praise or blame (see reviewFor in assets/sim.js). */
const REVIEW_FACTORS = ['fast', 'slow', 'tasty', 'plain', 'pretty', 'bare', 'clean', 'dirty', 'ambience', 'friendly', 'dark', 'cozy', 'comfy', 'no_toilet', 'toilet_dirty', 'toilet_clean'];
const START_DISHES = ['bruschetta', 'fries'];
const START_INGREDIENTS = ['tomato' => 4, 'basil' => 4, 'potato' => 4, 'onion' => 3, 'olive' => 4, 'garlic' => 2, 'flour' => 2, 'cheese' => 2];

/** Staff: role => [base hire cost, cost step per worker already hired]. */
const STAFF = ['chef' => [250, 150], 'waiter' => [180, 120], 'cleaner' => [200, 140], 'manager' => [1500, 1000], 'cashier' => [700, 400]];
/**
 * The cashier (from CASHIER_LEVEL, needs a cash register): guests pay at the till, the bill is CASHIER_BONUS higher
 * and a tip of at least CASHIER_TIP of the bill is always left.
 */
const CASHIER_LEVEL = 9;
const CASHIER_BONUS = 0.1;
const CASHIER_TIP = 0.08;
/**
 * Managers run the restaurant while the player is away, as if the game were open (at MANAGER_RATE of the live
 * speed) for the hours of their rank; ranks go up with a promotion. The best manager counts in full, every other one
 * adds a quarter of their hours, and never more than MANAGER_MAX_HOURS in all.
 */
const MANAGER_HOURS = [1 => 1, 2 => 2, 3 => 3, 4 => 4.5, 5 => 6];
const MANAGER_PROMOTE = [2 => 800, 3 => 1800, 4 => 3500, 5 => 6000]; // cost to reach each rank
const MANAGER_LEVEL = 7;   // first level that can hire one
const MANAGER_RATE = 0.4;
const MANAGER_MAX_HOURS = 8;
const OFFLINE_XP = 0.5;    // share of the XP per guest (xp_per_customer) given for guests served while away
/** Every person has a CV: a skill from 0 to 10 for each role. Hiring cost grows with the CV. */
const ROLES = ['chef', 'waiter', 'cleaner', 'manager', 'cashier'];
/** The outdoor area: OUTDOOR tiles in front of the restaurant, at its side (east) and behind it (the garden). */
const OUTDOOR = 5;
/** The game calendar: time passes only while playing (2 game minutes per second at 1×, ×2 or ×5 with the speed buttons). */
const MAX_SPEED = 5;
/** Opening hours: how long "open for" can last (game hours). */
const HOURS_TIMERS = [2, 4, 6, 8, 10, 12];
const GAME_MINUTES_PER_SECOND = 2;
const DAYS_PER_MONTH = 10; // 12 months a year; year 0 starts on 1 March
/** Daily wages: base by role + per skill point in the role + per manager rank. Paid when a calendar day ends. */
/**
 * Daily wages: (base of the role + WAGE_PER_SKILL per skill point in that role [+ WAGE_PER_RANK per manager rank])
 * × (1 + WAGE_PER_LEVEL × (restaurant level − 1)). A famous restaurant pays its people more, and earns more too.
 */
const WAGE_BASE = ['chef' => 40, 'waiter' => 28, 'cleaner' => 22, 'manager' => 70, 'cashier' => 32];
const WAGE_PER_SKILL = 7;
const WAGE_PER_RANK = 25;
const WAGE_PER_LEVEL = 0.09; // level 10 ×1.8, level 20 ×2.7, level 30 ×3.6
const WAGE_MAX_DAYS = 7; // after a long absence at most a week is paid
/**
 * Kinds of restaurant: key => [price ×, customers ×, eating time ×, level, cost to switch].
 * Luxury guests expect comfort and beauty; fast-food guests want speed; an inn loves a fire and warm light.
 */
const KINDS = [
    'generic' => [1.0, 1.0, 1.0, 1, 0],
    'fast_food' => [0.75, 1.5, 0.6, 4, 800],
    'cafe' => [0.9, 1.25, 0.8, 6, 1000],
    'inn' => [1.1, 1.0, 1.1, 9, 1500],
    'luxury' => [1.9, 0.55, 1.3, 16, 6000],
];
/**
 * Uniforms (any role can wear any of them): key => [price, level, top, bottom, accent, cut].
 * cut: apron | vest | tie | tux | overalls | stripes. 'default' is the role's own uniform, free.
 */
const UNIFORMS = [
    'default' => [0, 1, '', '', '', ''],
    'terracotta' => [250, 2, '#D9653B', '#F4E6D0', '#7B4E2C', 'apron'],
    'stripes' => [300, 3, '#FFFFFF', '#2F4B7C', '#2F4B7C', 'stripes'],
    'olive' => [400, 6, '#7A8B3A', '#5B4630', '#E2B23A', 'vest'],
    'mint' => [500, 9, '#9ED2C0', '#FFFFFF', '#2F9E8F', 'apron'],
    'berry' => [700, 16, '#B43E5C', '#22201F', '#F7A1C4', 'vest'],
    'sailor' => [650, 14, '#FFFFFF', '#2F4B7C', '#D7263D', 'tie'],
    'denim' => [550, 10, '#5B8DB8', '#2F4B7C', '#E9C46A', 'overalls'],
    'tuxedo' => [1200, 24, '#1E1E24', '#1E1E24', '#FFFFFF', 'tux'],
    'gold' => [2500, 25, '#F4EAD5', '#3B2418', '#E2B23A', 'tux'],
    'sunny' => [350, 4, '#F6C945', '#3B2418', '#E07B39', 'apron'],
    'ocean' => [450, 7, '#3E7CB4', '#F4F1E8', '#FFFFFF', 'stripes'],
    'candy' => [480, 8, '#FFFFFF', '#F7A1C4', '#9ED2C0', 'stripes'],
    'lavender' => [550, 12, '#B9A3E0', '#4A3A6B', '#FFFFFF', 'apron'],
    'forest' => [600, 13, '#2F6F3E', '#5B4630', '#E9C46A', 'vest'],
    'chef_white' => [650, 15, '#FFFFFF', '#2C2C30', '#2C2C30', 'jacket'],
    'diner' => [700, 18, '#F7A1C4', '#FFFFFF', '#D7263D', 'tie'],
    'ranch' => [800, 19, '#C98F5A', '#2F4B7C', '#7B3F22', 'vest'],
    'chef_black' => [900, 20, '#1E1E24', '#2C2C30', '#E2B23A', 'jacket'],
    'night' => [900, 21, '#22315A', '#1E1E24', '#C9CED4', 'tux'],
    'chef_red' => [1100, 22, '#B5302A', '#1E1E24', '#FFFFFF', 'jacket'],
    'royal' => [3500, 26, '#4A1E5A', '#1E1E24', '#E2B23A', 'tux'],
];
// The last one (bald) is never given at random
const HAIRSTYLES = ['short', 'bob', 'bun', 'ponytail', 'curly', 'spiky', 'long', 'afro', 'mohawk', 'braids', 'pigtails', 'pixie', 'topknot', 'wavy', 'sidepart', 'bald'];
// The first seven are natural colours (the ones given at random)
const HAIR_COLORS = ['#2B1B12', '#5A3825', '#A0522D', '#E8C07D', '#1C1C1C', '#B23A48', '#8E8E8E', '#3E7CB4', '#F7A1C4', '#C9CED4', '#F08A24', '#2E8B57', '#9B5DE5', '#FFFFFF'];
/** Accessories at the shop: glasses, moustache or beard, something on the head. Each change costs ACCESSORY_PRICE. */
const ACCESSORIES = [
    'glasses' => ['none', 'round', 'square', 'sun', 'heart'],
    'facial' => ['none', 'moustache', 'beard', 'goatee'],
    'headwear' => ['none', 'cap', 'beret', 'bandana', 'beanie', 'flower', 'bow'],
];
const ACCESSORY_PRICE = 60;
const HAIR_PRICE = 80; // a new hairstyle or colour at the hairdresser
const CANDIDATES = 4;               // people looking for a job each day
const CANDIDATE_REFRESH = 60;       // coins for a new list before tomorrow
/**
 * Experience: a worker improves in the role they do, very slowly, as game time passes while the restaurant is open
 * (not while closed: they are at home). About +1 point every 8 game years at the start, slower near 10:
 * growth per year = SKILL_PER_YEAR × (1 − skill / 12). A game year is 120 game days (about 24 hours of play at 1×).
 */
const SKILL_PER_YEAR = 0.125;
const GAME_YEAR_MINUTES = 120 * 1440;
const SEAT_CYCLE = 45;     // while away: a seat serves at most one customer every 45 seconds (order, wait, eat)
/** Personality traits, one per worker (Sims style): they change speed, energy and the mood they spread. */
const STAFF_TRAITS = ['cheerful', 'speedy', 'tireless', 'careful', 'chatty', 'calm'];
const STAFF_NAMES = ['Ada', 'Bruno', 'Carla', 'Dario', 'Elsa', 'Fabio', 'Gemma', 'Hugo', 'Irene', 'Jonas', 'Kira', 'Leo', 'Mara', 'Nico', 'Olga', 'Pino', 'Rita', 'Sami', 'Tina', 'Ugo', 'Vera', 'Walt', 'Zoe', 'Remy', 'Lia'];
const STAFF_COLORS = ['#D9653B', '#7A8B3A', '#B43E5C', '#3E7CB4', '#E0A526', '#6B4FA0', '#2F9E8F', '#C2572B'];

/** Chefs the level allows (each one also needs a stove of their own). */
function max_chefs(int $level): int
{
    return min(8, 1 + intdiv($level + 1, 4)); // 1 at the start, 2 from level 3, 3 from 7 … 8 from level 27
}

function max_waiters(int $level): int
{
    return min(6, 1 + intdiv($level, 3));
}

function max_cashiers(int $level): int
{
    return $level < CASHIER_LEVEL ? 0 : min(2, 1 + intdiv($level - CASHIER_LEVEL, 8));
}

function max_managers(int $level): int
{
    return $level < MANAGER_LEVEL ? 0 : min(3, 1 + intdiv($level - MANAGER_LEVEL, 6));
}

function max_cleaners(int $level): int
{
    return min(4, 1 + intdiv($level, 5));
}

function max_menu(int $level): int
{
    return min(8, 3 + intdiv($level, 4)); // 8 dishes on the menu by level 20
}

function garden_plots(int $level): int
{
    return min(12, 4 + intdiv($level, 2));
}

function dish_price(string $dish, int $level): int
{
    return (int)round(dishes()[$dish][0] * (1 + 0.25 * ($level - 1)));
}

/** Ingredients needed to take a dish to $toLevel (1 = learning it). */
function dish_cost(string $dish, int $toLevel): array
{
    $n = $toLevel <= 1 ? 1 : $toLevel - 1;
    $out = [];
    foreach (DISHES[$dish][4] as $ing) $out[$ing] = $n;
    return $out;
}

/** Popularity: 10 + decorations and finishes + 4 per menu dish level + ratings from customers and friends. */
function popularity(array $r, array $recipes): int
{
    $p = 10;
    $items = items();
    foreach ($r['layout'] as $it) $p += (int)($items[$it['k']][5] ?? 0);
    $p += (int)($items[$r['floor']][5] ?? 0) + (int)($items[$r['wall']][5] ?? 0);
    foreach ($r['menu'] as $d) $p += 4 * (int)($recipes[$d] ?? 0);
    // Reputation: the guests' rolling average (the last RATING_WINDOW ratings: old ones fade, so a restaurant can climb)
    if ($r['rating_n'] > 0) $p += (int)round(($r['rating_sum'] / $r['rating_n'] - 3) * 18);
    if ($r['friend_n'] > 0) $p += (int)round(($r['friend_sum'] / $r['friend_n'] - 3) * 8);
    return max(1, $p);
}

/** How many guests' ratings make the rating (a rolling average: older ones fade out). */
const RATING_WINDOW = 50;

/**
 * The restaurant's stars: its guests' average rating, rounded (0 = not rated yet, under 3 ratings). The same number
 * everywhere: the top bar, the reviews, friends, the board.
 */
function rating_stars(int $sum, int $n): int
{
    return $n >= 3 ? max(1, min(5, (int)round($sum / $n))) : 0;
}

function stars_for(int $popularity): int
{
    $s = 1;
    foreach ([40, 90, 160, 260] as $i => $min) if ($popularity >= $min) $s = $i + 2;
    return $s;
}

/** Today's market: MARKET_SLOTS ingredients chosen by the date, the same for every player. */
function market_today(): array
{
    $keys = array_keys(market_prices());
    $seed = crc32('forkville-market-' . today_utc());
    usort($keys, fn($a, $b) => crc32($seed . $a) <=> crc32($seed . $b));
    return array_slice($keys, 0, rule('market_slots'));
}

/** The catalog as the app receives it. */
function catalog_out(): array
{
    $items = [];
    foreach (items() as $k => [$cat, $price, $lvl, $w, $d, $pop, $extra, $on]) $items[$k] = ['cat' => $cat, 'price' => $price, 'level' => $lvl, 'w' => $w, 'd' => $d, 'pop' => $pop, 'speed' => $extra, 'enabled' => $on, 'tier' => ITEM_TIERS[$k] ?? 0];
    $dishes = [];
    foreach (dishes() as $k => [$price, $cook, $eat, $lvl, $ings, $on]) $dishes[$k] = ['price' => $price, 'cook' => $cook, 'eat' => $eat, 'level' => $lvl, 'ings' => $ings, 'enabled' => $on];
    $crops = [];
    foreach (crops() as $k => [$cost, $secs, $yield, $lvl, $on]) $crops[$k] = ['cost' => $cost, 'secs' => $secs, 'yield' => $yield, 'level' => $lvl, 'enabled' => $on];
    $levels = [];
    for ($l = 1; $l <= MAX_LEVEL; $l++) $levels[] = xp_for_level($l);
    return [
        'items' => $items, 'dishes' => $dishes, 'crops' => $crops, 'ingredients' => INGREDIENTS, 'market' => market_prices(),
        'expand' => ['min' => MIN_SIDE, 'max' => MAX_SIDE, 'rate' => rule('expand_rate')], 'parking' => array_map(fn($p) => ['cost' => $p[0], 'level' => $p[1], 'spaces' => $p[2]], PARKING), 'garage' => array_map(fn($p) => ['cost' => $p[0], 'level' => $p[1], 'spaces' => $p[2]], GARAGE), 'floors' => array_map(fn($f) => ['level' => $f[0], 'cost' => $f[1]], FLOORS), 'events' => events_out(), 'seasonal' => SEASONAL, 'dishKinds' => DISH_KINDS, 'cashier' => ['level' => CASHIER_LEVEL, 'bonus' => CASHIER_BONUS, 'tip' => CASHIER_TIP], 'doors' => array_map(fn($d) => ['price' => $d[0], 'level' => $d[1]], DOORS), 'doorWide' => DOOR_WIDE, 'doorMove' => DOOR_MOVE, 'hoursTimers' => HOURS_TIMERS, 'floorStart' => FLOOR_START, 'carBonus' => CAR_BONUS, 'friendTips' => FRIEND_TIPS, 'friendTipDaily' => FRIEND_TIP_DAILY, 'levels' => $levels, 'staff' => staff_costs(), 'tags' => ITEM_TAGS, 'roles' => ROLES, 'outdoor' => OUTDOOR, 'calendar' => ['daysPerMonth' => DAYS_PER_MONTH, 'minutesPerSecond' => GAME_MINUTES_PER_SECOND], 'wages' => ['base' => WAGE_BASE, 'skill' => WAGE_PER_SKILL, 'rank' => WAGE_PER_RANK, 'level' => WAGE_PER_LEVEL], 'kinds' => array_map(fn($k) => ['price' => $k[0], 'customers' => $k[1], 'eat' => $k[2], 'level' => $k[3], 'cost' => $k[4]], KINDS), 'uniforms' => array_map(fn($u) => ['price' => $u[0], 'level' => $u[1], 'top' => $u[2], 'bottom' => $u[3], 'accent' => $u[4], 'cut' => $u[5]], UNIFORMS), 'hairstyles' => HAIRSTYLES, 'hairColors' => HAIR_COLORS, 'hairPrice' => HAIR_PRICE, 'accessories' => ACCESSORIES, 'accessoryPrice' => ACCESSORY_PRICE, 'refresh' => CANDIDATE_REFRESH, 'manager' => ['hours' => MANAGER_HOURS, 'promote' => MANAGER_PROMOTE, 'level' => MANAGER_LEVEL, 'rate' => MANAGER_RATE], 'traits' => STAFF_TRAITS, 'titles' => LEVEL_TITLES, 'levelReward' => rule('level_reward'), 'dishMax' => DISH_MAX_LEVEL,
        'rules' => ['seatSeconds' => SEAT_SECONDS, 'sellBack' => rule('sell_back'), 'marketLimit' => rule('market_limit'), 'visitHost' => rule('visit_host'), 'visitBase' => rule('visit_base')],
    ];
}

// ---------------------------------------------------------------- defaults + changes from the admin panel

/** Rules the admin panel can change: name => [default, min, max]. */
const RULES = [
    'start_coins' => [START_COINS, 0, 1000000],
    'sell_back' => [SELL_BACK, 0, 1],
    'offline_hours' => [OFFLINE_MAX / 3600, 0, 48],
    'offline_rate' => [OFFLINE_RATE, 0, 1],
    'visit_host' => [VISIT_COINS_HOST, 0, 100000],
    'visit_base' => [VISIT_COINS_BASE, 0, 100000],
    'market_limit' => [MARKET_DAILY_LIMIT, 1, 100],
    'market_slots' => [MARKET_SLOTS, 1, 12],
    'xp_customer' => [XP_PER_CUSTOMER, 0, 100],
    'xp_harvest' => [XP_PER_HARVEST, 0, 100],
    'level_reward' => [100, 0, 100000],
    'expand_rate' => [EXPAND_RATE, 0, 10000],
];

/** The changes saved by the admin panel ({} when there are none, or before the table exists). */
function catalog_changes(bool $reload = false): array
{
    static $c = null;
    if ($c !== null && !$reload) return $c;
    $c = [];
    try {
        $st = db()->prepare('SELECT v FROM ' . t('settings') . " WHERE k = 'catalog'");
        $st->execute();
        $v = $st->fetchColumn();
        if ($v !== false) $c = json_decode((string)$v, true) ?: [];
    } catch (Throwable $e) { /* table not there yet: the defaults */
    }
    return $c;
}

/** key => [category, price, level, width, depth, popularity, speed, enabled] */
function items(): array
{
    static $cache = null, $ver = null;
    $ch = catalog_changes();
    if ($cache !== null && $ver === $ch) return $cache;
    $out = [];
    foreach (ITEMS as $k => $d) {
        $o = $ch['items'][$k] ?? [];
        $out[$k] = [$d[0], (int)($o['price'] ?? $d[1]), (int)($o['level'] ?? $d[2]), $d[3], $d[4], (int)($o['pop'] ?? $d[5]), (float)($o['speed'] ?? $d[6]), (bool)($o['enabled'] ?? true)];
    }
    $ver = $ch;
    return $cache = $out;
}

/** key => [base price, cook seconds, eat seconds, level, ingredients, enabled] */
function dishes(): array
{
    $ch = catalog_changes();
    $out = [];
    foreach (DISHES as $k => $d) {
        $o = $ch['dishes'][$k] ?? [];
        $out[$k] = [(int)($o['price'] ?? $d[0]), (float)($o['cook'] ?? $d[1]), (float)($o['eat'] ?? $d[2]), (int)($o['level'] ?? $d[3]), $d[4], (bool)($o['enabled'] ?? true)];
    }
    return $out;
}

/** ingredient => [seed cost, grow seconds, yield, level, enabled] */
function crops(): array
{
    $ch = catalog_changes();
    $out = [];
    foreach (CROPS as $k => $d) {
        $o = $ch['crops'][$k] ?? [];
        $out[$k] = [(int)($o['cost'] ?? $d[0]), (int)($o['secs'] ?? $d[1]), (int)($o['yield'] ?? $d[2]), (int)($o['level'] ?? $d[3]), (bool)($o['enabled'] ?? true)];
    }
    return $out;
}

/** Market ingredients on sale => price (switched-off ones are left out). */
function market_prices(): array
{
    $ch = catalog_changes();
    $out = [];
    foreach (MARKET_PRICES as $k => $p) {
        $o = $ch['market'][$k] ?? [];
        if (($o['enabled'] ?? true) === false) continue;
        $out[$k] = (int)($o['price'] ?? $p);
    }
    return $out;
}

/** role => [first hire cost, extra per worker already hired] */
function staff_costs(): array
{
    $ch = catalog_changes();
    $out = [];
    foreach (STAFF as $role => [$base, $step]) {
        $o = $ch['staff'][$role] ?? [];
        $out[$role] = [(int)($o['base'] ?? $base), (int)($o['step'] ?? $step)];
    }
    return $out;
}


function rule(string $name)
{
    $v = catalog_changes()['rules'][$name] ?? RULES[$name][0];
    return is_float(RULES[$name][0]) ? (float)$v : (is_int(RULES[$name][0]) ? (int)$v : $v);
}
