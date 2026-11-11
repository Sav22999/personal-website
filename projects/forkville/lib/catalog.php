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
const MAX_LEVEL = 30;
const XP_PER_CUSTOMER = 2;
const XP_PER_HARVEST = 1;
const SELL_BACK = 0.5;            // furniture sells back at half price
const SEAT_SECONDS = 18;          // the fastest a customer can sit, order, eat and pay
const REPORT_WINDOW = 600;        // a report never counts more than 10 minutes of service
const OFFLINE_MAX = 4 * 3600;     // earnings while away: at most 4 hours…
const OFFLINE_RATE = 0.25;        // …at a quarter of the speed of live service
const OFFLINE_MIN = 300;          // nothing for absences under 5 minutes
const VISIT_COINS_HOST = 25;
const VISIT_COINS_BASE = 40;      // the visitor gets this + 5 per host level
const MARKET_DAILY_LIMIT = 5;     // pieces of each market ingredient per day

/** Restaurant sizes: [side in tiles, cost, level required]. */
const SIZES = [[10, 0, 1], [12, 2500, 4], [14, 7000, 8], [16, 15000, 12]];

/** XP needed for level L: 50·L·(L−1) (L2 = 100, L3 = 300, L4 = 600…). */
function xp_for_level(int $level): int
{
    return 50 * $level * ($level - 1);
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
    'table_round' => ['table', 260, 3, 1, 1, 1, 0],
    'table_marble' => ['table', 520, 6, 1, 1, 3, 0],
    'table_long' => ['table', 480, 5, 2, 1, 2, 0],
    // Chairs
    'chair_wood' => ['chair', 50, 1, 1, 1, 0, 0],
    'chair_cushion' => ['chair', 130, 2, 1, 1, 1, 0],
    'stool_bar' => ['chair', 90, 3, 1, 1, 1, 0],
    'chair_velvet' => ['chair', 320, 7, 1, 1, 3, 0],
    // Kitchen
    'stove_basic' => ['stove', 350, 1, 1, 1, 0, 1.0],
    'stove_steel' => ['stove', 900, 4, 1, 1, 1, 1.35],
    'oven_brick' => ['stove', 1800, 8, 1, 1, 4, 1.7],
    'counter_wood' => ['counter', 200, 1, 1, 1, 0, 0],
    'counter_tile' => ['counter', 450, 5, 1, 1, 2, 0],
    // Decorations
    'plant_pot' => ['deco', 90, 1, 1, 1, 2, 0],
    'plant_tall' => ['deco', 180, 2, 1, 1, 4, 0],
    'lamp_floor' => ['deco', 220, 2, 1, 1, 4, 0],
    'bookshelf' => ['deco', 380, 3, 1, 1, 6, 0],
    'jukebox' => ['deco', 650, 4, 1, 1, 9, 0],
    'piano' => ['deco', 1400, 9, 2, 1, 18, 0],
    'aquarium' => ['deco', 900, 6, 2, 1, 12, 0],
    'fountain' => ['deco', 1600, 10, 1, 1, 20, 0],
    'statue_fork' => ['deco', 2400, 14, 1, 1, 28, 0],
    'clock_tall' => ['deco', 500, 5, 1, 1, 7, 0],
    'cake_stand' => ['deco', 300, 3, 1, 1, 5, 0],
    // More tables and chairs
    'table_picnic' => ['table', 340, 2, 2, 1, 1, 0],
    'table_bar' => ['table', 280, 4, 1, 1, 1, 0],
    'table_glass' => ['table', 720, 7, 1, 1, 4, 0],
    'chair_rattan' => ['chair', 160, 3, 1, 1, 2, 0],
    'bench_wood' => ['chair', 210, 4, 1, 1, 1, 0],
    'chair_modern' => ['chair', 240, 5, 1, 1, 2, 0],
    'chair_armchair' => ['chair', 420, 8, 1, 1, 4, 0],
    // Kitchen and service
    'stove_grill' => ['stove', 1300, 6, 1, 1, 2, 1.5],
    'fridge' => ['kitchen', 600, 2, 1, 1, 0, 0],
    'sink' => ['kitchen', 450, 1, 1, 1, 0, 0],
    'cash_register' => ['service', 380, 2, 1, 1, 1, 0],
    'trash_bin' => ['service', 120, 1, 1, 1, 0, 0],
    'menu_board' => ['deco', 150, 1, 1, 1, 3, 0],
    // Lounge and fun
    'coffee_machine' => ['lounge', 500, 3, 1, 1, 2, 0],
    'sofa' => ['lounge', 650, 3, 2, 1, 4, 0],
    'tv' => ['deco', 900, 5, 1, 1, 6, 0],
    'arcade' => ['deco', 1100, 7, 1, 1, 9, 0],
    'gramophone' => ['deco', 800, 5, 1, 1, 7, 0],
    'fireplace' => ['deco', 2000, 9, 2, 1, 16, 0],
    'vending' => ['deco', 800, 5, 1, 1, 3, 0],
    // More decorations
    'cactus' => ['deco', 70, 1, 1, 1, 1, 0],
    'sunflowers' => ['deco', 160, 2, 1, 1, 3, 0],
    'coat_rack' => ['deco', 110, 1, 1, 1, 1, 0],
    'umbrella_stand' => ['deco', 90, 1, 1, 1, 1, 0],
    'candles' => ['deco', 200, 2, 1, 1, 3, 0],
    'globe' => ['deco', 300, 3, 1, 1, 4, 0],
    'lantern' => ['deco', 240, 3, 1, 1, 3, 0],
    'palm' => ['deco', 380, 4, 1, 1, 5, 0],
    'easel' => ['deco', 350, 4, 1, 1, 4, 0],
    'bonsai' => ['deco', 450, 4, 1, 1, 6, 0],
    'neon_sign' => ['deco', 700, 6, 1, 1, 8, 0],
    'trophy_case' => ['deco', 1200, 8, 1, 1, 12, 0],
    'flower_arch' => ['deco', 1500, 11, 2, 1, 18, 0],
    // Rugs: their own layer, people walk on them and furniture may stand on them
    'rug_round' => ['rug', 140, 1, 2, 2, 2, 0],
    'rug_stripes' => ['rug', 180, 2, 2, 1, 2, 0],
    'rug_red' => ['rug', 260, 3, 2, 2, 3, 0],
    'rug_checker' => ['rug', 420, 4, 2, 2, 4, 0],
    'rug_persian' => ['rug', 650, 6, 3, 2, 7, 0],
    // Equipment in tiers (budget → luxury) and different looks for the same tier
    'stove_retro' => ['stove', 420, 2, 1, 1, 1, 1.1],
    'stove_pro' => ['stove', 2400, 10, 1, 1, 3, 1.9],
    'stove_gold' => ['stove', 5200, 15, 1, 1, 8, 2.2],
    'counter_marble' => ['counter', 1100, 8, 1, 1, 4, 0],
    'counter_gold' => ['counter', 2600, 13, 1, 1, 8, 0],
    'fridge_retro' => ['kitchen', 900, 4, 1, 1, 2, 0],
    'fridge_pro' => ['kitchen', 1900, 9, 1, 1, 2, 0],
    'sink_steel' => ['kitchen', 1200, 7, 1, 1, 1, 0],
    'coffee_luxe' => ['lounge', 2200, 12, 1, 1, 6, 0],
    'table_rustic' => ['table', 150, 1, 1, 1, 0, 0],
    'chair_folding' => ['chair', 40, 1, 1, 1, 0, 0],
    'chair_bistro' => ['chair', 190, 3, 1, 1, 1, 0],
    // Outdoors: the terrace in front and the yard at the side (people queue on benches when no new guests are let in)
    'bench_park' => ['outdoor', 160, 1, 2, 1, 1, 0],
    'tree_oak' => ['outdoor', 200, 1, 1, 1, 3, 0],
    'tree_pine' => ['outdoor', 300, 2, 1, 1, 4, 0],
    'tree_cherry' => ['outdoor', 480, 4, 1, 1, 6, 0],
    'bush_round' => ['outdoor', 60, 1, 1, 1, 1, 0],
    'hedge' => ['outdoor', 110, 2, 2, 1, 2, 0],
    'planter_flowers' => ['outdoor', 120, 1, 1, 1, 2, 0],
    'street_lamp' => ['outdoor', 260, 3, 1, 1, 2, 0],
    'parasol' => ['outdoor', 340, 3, 1, 1, 3, 0],
    'menu_sign' => ['outdoor', 140, 2, 1, 1, 3, 0],
    'bike_rack' => ['outdoor', 110, 2, 1, 1, 1, 0],
    'gnome' => ['outdoor', 80, 2, 1, 1, 2, 0],
    'garden_fountain' => ['outdoor', 1400, 9, 1, 1, 12, 0],
    'food_truck' => ['outdoor', 6000, 18, 3, 2, 10, 0],
    // Floors
    'floor_oak' => ['floor', 0, 1, 0, 0, 0, 0],
    'floor_checker' => ['floor', 400, 2, 0, 0, 4, 0],
    'floor_terracotta' => ['floor', 700, 4, 0, 0, 7, 0],
    'floor_mint' => ['floor', 1000, 6, 0, 0, 10, 0],
    'floor_marble' => ['floor', 2200, 10, 0, 0, 18, 0],
    'floor_blue' => ['floor', 900, 5, 0, 0, 8, 0],
    'floor_herringbone' => ['floor', 1500, 8, 0, 0, 12, 0],
    'floor_hex' => ['floor', 1900, 12, 0, 0, 15, 0],
    // Walls
    'wall_cream' => ['wall', 0, 1, 0, 0, 0, 0],
    'wall_stripes' => ['wall', 350, 2, 0, 0, 3, 0],
    'wall_brick' => ['wall', 650, 4, 0, 0, 6, 0],
    'wall_sage' => ['wall', 900, 6, 0, 0, 9, 0],
    'wall_berry' => ['wall', 1800, 10, 0, 0, 15, 0],
    'wall_tile' => ['wall', 800, 5, 0, 0, 8, 0],
    'wall_wood' => ['wall', 1200, 7, 0, 0, 11, 0],
    'wall_flowers' => ['wall', 1500, 9, 0, 0, 13, 0],
];

/**
 * Quality tier of equipment and furniture: 1 budget, 2 standard, 3 premium, 4 luxury.
 * Stoves, fridges and counters make better dishes; sinks wash faster; chairs and tables are more comfortable.
 */
const ITEM_TIERS = [
    'stove_basic' => 1, 'stove_retro' => 1, 'stove_steel' => 2, 'stove_grill' => 3, 'oven_brick' => 3, 'stove_pro' => 3, 'stove_gold' => 4,
    'counter_wood' => 1, 'counter_tile' => 2, 'counter_marble' => 3, 'counter_gold' => 4,
    'fridge' => 1, 'fridge_retro' => 2, 'fridge_pro' => 3, 'sink' => 1, 'sink_steel' => 3, 'coffee_machine' => 2, 'coffee_luxe' => 4,
    'table_small' => 1, 'table_rustic' => 1, 'table_picnic' => 1, 'table_round' => 2, 'table_bar' => 2, 'table_long' => 2, 'table_marble' => 3, 'table_glass' => 4,
    'chair_wood' => 1, 'chair_folding' => 1, 'stool_bar' => 1, 'bench_wood' => 1, 'chair_cushion' => 2, 'chair_rattan' => 2, 'chair_bistro' => 2,
    'chair_modern' => 3, 'chair_velvet' => 3, 'chair_armchair' => 4,
];

/** What some items do in the live service (the simulation in assets/sim.js reads these tags). */
const ITEM_TAGS = [
    'fridge' => 'fridge', 'sink' => 'sink', 'cash_register' => 'register', 'trash_bin' => 'trash',
    'coffee_machine' => 'coffee', 'coffee_luxe' => 'coffee', 'fridge_retro' => 'fridge', 'fridge_pro' => 'fridge', 'sink_steel' => 'sink', 'sofa' => 'rest', 'jukebox' => 'music', 'gramophone' => 'music', 'piano' => 'music',
    'tv' => 'fun', 'arcade' => 'fun', 'fireplace' => 'cozy', 'vending' => 'snack',
    'bench_park' => 'queue', 'menu_sign' => 'attract', 'food_truck' => 'truck', 'street_lamp' => 'light',
    'lamp_floor' => 'light', 'lantern' => 'light', 'neon_sign' => 'light', 'candles' => 'light',
];

/** Ingredients: key => source ('garden' or 'market'). */
const INGREDIENTS = [
    'tomato' => 'garden', 'basil' => 'garden', 'potato' => 'garden', 'onion' => 'garden', 'garlic' => 'garden',
    'carrot' => 'garden', 'lemon' => 'garden', 'strawberry' => 'garden', 'pepper' => 'garden', 'mushroom' => 'garden',
    'flour' => 'market', 'cheese' => 'market', 'egg' => 'market', 'milk' => 'market', 'rice' => 'market',
    'fish' => 'market', 'beef' => 'market', 'chicken' => 'market', 'chocolate' => 'market', 'coffee' => 'market',
    'olive' => 'market', 'sugar' => 'market',
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
];

/** Market prices of the market ingredients (the stall sells MARKET_SLOTS of them each day). */
const MARKET_PRICES = [
    'flour' => 14, 'cheese' => 22, 'egg' => 12, 'milk' => 12, 'rice' => 16, 'fish' => 34, 'beef' => 36,
    'chicken' => 28, 'chocolate' => 30, 'coffee' => 20, 'olive' => 15, 'sugar' => 10,
];
const MARKET_SLOTS = 7;

/**
 * Dishes: key => [base price, cook seconds, eat seconds, level, [3 ingredients]]
 * Learning a dish costs 1 of each ingredient; raising it to level L (2..5) costs L−1 of each.
 * Price at level L = base × (1 + 0.25·(L−1)).
 */
const DISHES = [
    'bruschetta' => [12, 6, 8, 1, ['tomato', 'basil', 'olive']],
    'fries' => [10, 5, 7, 1, ['potato', 'olive', 'onion']],
    'tomato_soup' => [15, 8, 9, 1, ['tomato', 'onion', 'garlic']],
    'lemonade' => [8, 3, 5, 2, ['lemon', 'sugar', 'strawberry']],
    'cappuccino' => [9, 3, 5, 2, ['coffee', 'milk', 'sugar']],
    'omelette' => [18, 7, 9, 2, ['egg', 'cheese', 'onion']],
    'pasta_tomato' => [22, 9, 10, 3, ['flour', 'tomato', 'basil']],
    'pizza' => [28, 11, 11, 3, ['flour', 'tomato', 'cheese']],
    'carrot_cake' => [20, 8, 8, 4, ['carrot', 'flour', 'egg']],
    'burger' => [32, 10, 11, 4, ['beef', 'flour', 'onion']],
    'risotto' => [30, 12, 11, 5, ['rice', 'mushroom', 'cheese']],
    'strawberry_tart' => [26, 9, 8, 5, ['strawberry', 'flour', 'sugar']],
    'grilled_fish' => [38, 12, 12, 6, ['fish', 'lemon', 'garlic']],
    'stuffed_peppers' => [34, 12, 11, 7, ['pepper', 'rice', 'beef']],
    'roast_chicken' => [42, 14, 13, 8, ['chicken', 'potato', 'garlic']],
    'chocolate_cake' => [36, 10, 9, 9, ['chocolate', 'egg', 'milk']],
];
const DISH_MAX_LEVEL = 5;
const REVIEW_WINDOW = 50; // reviews that make the reputation

/** The restaurant's title as it grows: [from level, key] (the app translates the key). */
const LEVEL_TITLES = [[1, 'kiosk'], [3, 'snack_bar'], [5, 'trattoria'], [8, 'bistro'], [11, 'restaurant'], [15, 'fine_dining'], [20, 'gourmet'], [25, 'legend']];
/** What a review can praise or blame (see reviewFor in assets/sim.js). */
const REVIEW_FACTORS = ['fast', 'slow', 'tasty', 'plain', 'pretty', 'bare', 'clean', 'dirty', 'ambience', 'friendly', 'dark', 'cozy', 'comfy'];
const START_DISHES = ['bruschetta', 'fries'];
const START_INGREDIENTS = ['tomato' => 4, 'basil' => 4, 'potato' => 4, 'onion' => 3, 'olive' => 4, 'garlic' => 2, 'flour' => 2, 'cheese' => 2];

/** Staff: role => [base hire cost, cost step per worker already hired]. */
const STAFF = ['chef' => [250, 150], 'waiter' => [180, 120], 'cleaner' => [200, 140], 'manager' => [1500, 1000]];
/**
 * Managers run the restaurant while the player is away, as if the game were open (at MANAGER_RATE of the live
 * speed) for the hours of their rank; ranks go up with a promotion. Their hours add up.
 */
const MANAGER_HOURS = [1 => 2, 2 => 4, 3 => 6, 4 => 9, 5 => 12];
const MANAGER_PROMOTE = [2 => 800, 3 => 1800, 4 => 3500, 5 => 6000]; // cost to reach each rank
const MANAGER_LEVEL = 6;   // first level that can hire one
const MANAGER_RATE = 0.6;
/** Every person has a CV: a skill from 0 to 10 for each role. Hiring cost grows with the CV. */
const ROLES = ['chef', 'waiter', 'cleaner', 'manager'];
/** The outdoor area: OUTDOOR tiles in front of the restaurant and at its side (east). */
const OUTDOOR = 3;
/** Daily wages: base by role + per skill point in the role + per manager rank. Paid when a calendar day ends. */
const WAGE_BASE = ['chef' => 40, 'waiter' => 30, 'cleaner' => 25, 'manager' => 70];
const WAGE_PER_SKILL = 6;
const WAGE_PER_RANK = 25;
const WAGE_MAX_DAYS = 7; // after a long absence at most a week is paid
/**
 * Kinds of restaurant: key => [price ×, customers ×, eating time ×, level, cost to switch].
 * Luxury guests expect comfort and beauty; fast-food guests want speed; an inn loves a fire and warm light.
 */
const KINDS = [
    'generic' => [1.0, 1.0, 1.0, 1, 0],
    'fast_food' => [0.75, 1.5, 0.6, 3, 800],
    'cafe' => [0.9, 1.25, 0.8, 4, 1000],
    'inn' => [1.1, 1.0, 1.1, 5, 1500],
    'luxury' => [1.9, 0.55, 1.3, 12, 6000],
];
/**
 * Uniforms (any role can wear any of them): key => [price, level, top, bottom, accent, cut].
 * cut: apron | vest | tie | tux | overalls | stripes. 'default' is the role's own uniform, free.
 */
const UNIFORMS = [
    'default' => [0, 1, '', '', '', ''],
    'terracotta' => [250, 2, '#D9653B', '#F4E6D0', '#7B4E2C', 'apron'],
    'stripes' => [300, 2, '#FFFFFF', '#2F4B7C', '#2F4B7C', 'stripes'],
    'olive' => [400, 4, '#7A8B3A', '#5B4630', '#E2B23A', 'vest'],
    'mint' => [500, 5, '#9ED2C0', '#FFFFFF', '#2F9E8F', 'apron'],
    'berry' => [700, 6, '#B43E5C', '#22201F', '#F7A1C4', 'vest'],
    'sailor' => [650, 7, '#FFFFFF', '#2F4B7C', '#D7263D', 'tie'],
    'denim' => [550, 5, '#5B8DB8', '#2F4B7C', '#E9C46A', 'overalls'],
    'tuxedo' => [1200, 10, '#1E1E24', '#1E1E24', '#FFFFFF', 'tux'],
    'gold' => [2500, 14, '#F4EAD5', '#3B2418', '#E2B23A', 'tux'],
];
const HAIRSTYLES = ['short', 'bob', 'bun', 'ponytail', 'curly', 'spiky', 'long', 'bald'];
const HAIR_COLORS = ['#2B1B12', '#5A3825', '#A0522D', '#E8C07D', '#1C1C1C', '#B23A48', '#8E8E8E', '#3E7CB4', '#F7A1C4'];
const HAIR_PRICE = 80; // a new hairstyle or colour at the hairdresser
const CANDIDATES = 4;               // people looking for a job each day
const CANDIDATE_REFRESH = 60;       // coins for a new list before tomorrow
const SKILL_GROWTH = 0.004;         // skill gained in one's role per customer served
const SEAT_CYCLE = 45;     // while away: a seat serves at most one customer every 45 seconds (order, wait, eat)
/** Personality traits, one per worker (Sims style): they change speed, energy and the mood they spread. */
const STAFF_TRAITS = ['cheerful', 'speedy', 'tireless', 'careful', 'chatty', 'calm'];
const STAFF_NAMES = ['Ada', 'Bruno', 'Carla', 'Dario', 'Elsa', 'Fabio', 'Gemma', 'Hugo', 'Irene', 'Jonas', 'Kira', 'Leo', 'Mara', 'Nico', 'Olga', 'Pino', 'Rita', 'Sami', 'Tina', 'Ugo', 'Vera', 'Walt', 'Zoe', 'Remy', 'Lia'];
const STAFF_COLORS = ['#D9653B', '#7A8B3A', '#B43E5C', '#3E7CB4', '#E0A526', '#6B4FA0', '#2F9E8F', '#C2572B'];

function max_waiters(int $level): int
{
    return min(6, 1 + intdiv($level, 3));
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
    return min(6, 3 + intdiv($level, 4));
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
    // Reputation: the average of the latest REVIEW_WINDOW reviews (old ones stop counting, so a restaurant can climb)
    if (isset($r['recent_avg'])) $p += (int)round(($r['recent_avg'] - 3) * 18);
    elseif ($r['rating_n'] > 0) $p += (int)round(($r['rating_sum'] / $r['rating_n'] - 3) * 12);
    if ($r['friend_n'] > 0) $p += (int)round(($r['friend_sum'] / $r['friend_n'] - 3) * 8);
    return max(1, $p);
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
        'sizes' => sizes(), 'levels' => $levels, 'staff' => staff_costs(), 'tags' => ITEM_TAGS, 'roles' => ROLES, 'outdoor' => OUTDOOR, 'wages' => ['base' => WAGE_BASE, 'skill' => WAGE_PER_SKILL, 'rank' => WAGE_PER_RANK], 'kinds' => array_map(fn($k) => ['price' => $k[0], 'customers' => $k[1], 'eat' => $k[2], 'level' => $k[3], 'cost' => $k[4]], KINDS), 'uniforms' => array_map(fn($u) => ['price' => $u[0], 'level' => $u[1], 'top' => $u[2], 'bottom' => $u[3], 'accent' => $u[4], 'cut' => $u[5]], UNIFORMS), 'hairstyles' => HAIRSTYLES, 'hairColors' => HAIR_COLORS, 'hairPrice' => HAIR_PRICE, 'refresh' => CANDIDATE_REFRESH, 'manager' => ['hours' => MANAGER_HOURS, 'promote' => MANAGER_PROMOTE, 'level' => MANAGER_LEVEL, 'rate' => MANAGER_RATE], 'traits' => STAFF_TRAITS, 'titles' => LEVEL_TITLES, 'levelReward' => rule('level_reward'), 'dishMax' => DISH_MAX_LEVEL,
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

/** [[side, cost, level], …] */
function sizes(): array
{
    $ch = catalog_changes();
    return array_map(function ($s) use ($ch) {
        $o = $ch['sizes'][(string)$s[0]] ?? [];
        return [$s[0], (int)($o['cost'] ?? $s[1]), (int)($o['level'] ?? $s[2])];
    }, SIZES);
}

function rule(string $name)
{
    $v = catalog_changes()['rules'][$name] ?? RULES[$name][0];
    return is_float(RULES[$name][0]) ? (float)$v : (is_int(RULES[$name][0]) ? (int)$v : $v);
}
