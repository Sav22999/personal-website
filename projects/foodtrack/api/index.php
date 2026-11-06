<?php
/**
 * FoodTrack API. Every call names its action with ?r=name and answers JSON.
 * Changes are POST with a JSON body + X-CSRF-Token header (except the recipe image upload: multipart).
 *
 * Public:      auth.me, auth.register, auth.login, auth.logout
 * User:        auth.profile, auth.password, auth.delete, bootstrap, state, recipes,
 *              household.*, space.*, item.*, list.*, listitem.*, favorite.toggle, plan.*, barcode.lookup
 * Content moderator (5): admin.catalog, admin.recipes, admin.recipe.save|image, admin.product.save (create and edit only)
 * Admin (9):   + admin.overview, admin.users, admin.user.*, admin.recipe.delete, admin.product.delete,
 *              admin.category.*, admin.households
 * Master (10): also manages administrators (role changes) and admin.user.transfer_master
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/households.php';
require __DIR__ . '/../lib/mail.php';

set_exception_handler(function (Throwable $e) {
    error_log('[foodtrack] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if ($e->getMessage() === 'not_installed') fail('FoodTrack is not installed yet: open install-db.php.', 503, 'not_installed');
    $debug = (bool)config_value('debug', false);
    fail('Internal server error.' . ($debug ? ' ' . $e->getMessage() : ''), 500, 'server_error');
});

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$route = (string)($_GET['r'] ?? '');

if ($method === 'POST') {
    $ct = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
    $isUpload = $route === 'admin.recipe.image' && strpos($ct, 'multipart/form-data') === 0;
    if (strpos($ct, 'application/json') !== 0 && !$isUpload) {
        if ($route === 'admin.recipe.image') fail('Image too large.', 413, 'too_large');
        fail('Unsupported Content-Type.', 415, 'bad_content_type');
    }
} elseif ($method !== 'GET') {
    fail('Method not allowed.', 405, 'method');
}

function only(string $m): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $m) fail('Method not allowed.', 405, 'method');
}

/** Signed-in user for a GET request. */
function reader(int $minRole = ROLE_USER, bool $allowPasswordChange = false): array
{
    only('GET');
    return require_user($minRole, $allowPasswordChange);
}

/** Signed-in user + CSRF check for a POST that changes data. */
function writer(int $minRole = ROLE_USER, bool $allowPasswordChange = false): array
{
    only('POST');
    $u = require_user($minRole, $allowPasswordChange);
    require_csrf($u);
    return $u;
}

// ---------- Row formatting ----------

function f_num($v): ?float
{
    return $v === null ? null : (float)$v;
}

function fmt_space(array $r): array
{
    return ['id' => (int)$r['id'], 'name' => $r['name'], 'icon' => $r['icon'], 'environment' => $r['environment'], 'notes' => $r['notes'], 'sort' => (int)$r['sort']];
}

function fmt_item(array $r): array
{
    return [
        'id' => (int)$r['id'], 'spaceId' => (int)$r['space_id'], 'productId' => $r['product_id'] !== null ? (int)$r['product_id'] : null,
        'name' => $r['name'], 'brand' => $r['brand'], 'categoryId' => $r['category_id'] !== null ? (int)$r['category_id'] : null,
        'icon' => $r['icon'], 'quantity' => f_num($r['quantity']), 'unit' => $r['unit'], 'expiryDate' => $r['expiry_date'],
        'expiryType' => $r['expiry_type'], 'nutriscore' => $r['nutriscore'], 'barcode' => $r['barcode'], 'notes' => $r['notes'],
        'createdAt' => $r['created_at'], 'createdBy' => $r['created_by_name'] ?? null,
    ];
}

function fmt_list(array $r): array
{
    return ['id' => (int)$r['id'], 'name' => $r['name'], 'icon' => $r['icon'], 'notes' => $r['notes'], 'updatedAt' => $r['updated_at']];
}

function fmt_list_item(array $r): array
{
    return [
        'id' => (int)$r['id'], 'listId' => (int)$r['list_id'], 'productId' => $r['product_id'] !== null ? (int)$r['product_id'] : null,
        'name' => $r['name'], 'icon' => $r['icon'], 'quantity' => f_num($r['quantity']), 'unit' => $r['unit'], 'notes' => $r['notes'],
        'environment' => $r['environment'], 'inCart' => (bool)$r['in_cart'],
    ];
}

/** Decoded translations of a content row ({"en": {...}}). */
function i18n_of(array $r): array
{
    $d = isset($r['i18n']) && $r['i18n'] ? json_decode((string)$r['i18n'], true) : null;
    return is_array($d) ? $d : [];
}

/** Value of a field in the language of the request (the base columns are Italian). */
function loc(array $r, string $field, $base)
{
    if (lang() === 'it') return $base;
    $v = i18n_of($r)[lang()][$field] ?? null;
    return ($v === null || $v === '' || $v === []) ? $base : $v;
}

/** Validated translations sent by the admin portal: {"en": {field: text}} with the given fields only. */
function i18n_input($raw, array $fields, int $max = 2000): ?string
{
    if (!is_array($raw)) return null;
    $out = [];
    foreach (LANGS as $lg) {
        if ($lg === 'it' || !isset($raw[$lg]) || !is_array($raw[$lg])) continue;
        foreach ($fields as $f) {
            $v = $raw[$lg][$f] ?? null;
            if ($f === 'status') {
                if (in_array($v, ['published', 'draft'], true)) $out[$lg][$f] = $v;
                continue;
            }
            if ($f === 'steps') {
                $v = is_array($v) ? array_values(array_filter(array_map(function ($x) {
                    return mb_substr(trim((string)$x), 0, 1000);
                }, array_slice($v, 0, 60)), 'strlen')) : [];
                if ($v) $out[$lg][$f] = $v;
            } else {
                $v = mb_substr(trim((string)($v ?? '')), 0, $max);
                if ($v !== '') $out[$lg][$f] = $v;
            }
        }
    }
    return $out ? json_encode($out, JSON_UNESCAPED_UNICODE) : null;
}

function fmt_product(array $r, bool $raw = false): array
{
    $o = [
        'id' => (int)$r['id'], 'name' => loc($r, 'name', $r['name']), 'nameBase' => $r['name'], 'icon' => $r['icon'],
        'categoryId' => $r['category_id'] !== null ? (int)$r['category_id'] : null,
        'unit' => $r['default_unit'], 'environment' => $r['environment'], 'expiryTip' => loc($r, 'expiry_tip', $r['expiry_tip']), 'active' => (bool)$r['active'],
    ];
    if ($raw) {
        $o['name'] = $r['name'];
        $o['expiryTip'] = $r['expiry_tip'];
        $o['i18n'] = (object)i18n_of($r);
    }
    return $o;
}

function fmt_category(array $r, bool $raw = false): array
{
    $o = ['id' => (int)$r['id'], 'name' => loc($r, 'name', $r['name']), 'expiryTip' => loc($r, 'expiry_tip', $r['expiry_tip']), 'sort' => (int)$r['sort']];
    if ($raw) {
        $o['name'] = $r['name'];
        $o['expiryTip'] = $r['expiry_tip'];
        $o['i18n'] = (object)i18n_of($r);
    }
    return $o;
}

/**
 * Publication status of a recipe in a language: 'published' | 'draft' | 'none' (not translated).
 * The base language (Italian) uses the `published` column, the others i18n[lang].status.
 */
function recipe_status(array $r, string $lg): string
{
    if ($lg === 'it') return !empty($r['published']) ? 'published' : 'draft';
    $tr = i18n_of($r)[$lg] ?? null;
    if (!is_array($tr) || !$tr) return 'none';
    return ($tr['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
}

/** Recipes with ingredients and steps; $onlyPublished = only those published in the request language; $raw = base fields + translations (admin portal). */
function load_recipes(bool $onlyPublished, bool $raw = false): array
{
    $recipes = [];
    $rows = [];
    foreach (db()->query('SELECT * FROM ' . t('recipes') . ' ORDER BY name') as $r) {
        if ($onlyPublished && recipe_status($r, lang()) !== 'published') continue;
        $rows[(int)$r['id']] = $r;
        $recipes[(int)$r['id']] = [
            'id' => (int)$r['id'], 'name' => $raw ? $r['name'] : loc($r, 'name', $r['name']),
            'description' => $raw ? $r['description'] : loc($r, 'description', $r['description']), 'image' => $r['image'],
            'prepMinutes' => $r['prep_minutes'] !== null ? (int)$r['prep_minutes'] : null,
            'cookMinutes' => $r['cook_minutes'] !== null ? (int)$r['cook_minutes'] : null,
            'cookNotes' => $raw ? $r['cook_notes'] : loc($r, 'cook_notes', $r['cook_notes']), 'servings' => (int)$r['servings'], 'difficulty' => $r['difficulty'], 'origin' => array_values(array_filter(explode(',', (string)($r['origin'] ?? '')))),
            'published' => (bool)$r['published'], 'updatedAt' => $r['updated_at'], 'ingredients' => [], 'steps' => [],
        ];
        if ($raw) {
            $recipes[(int)$r['id']]['i18n'] = (object)i18n_of($r);
            $recipes[(int)$r['id']]['status'] = array_combine(LANGS, array_map(function ($lg) use ($r) {
                return recipe_status($r, $lg);
            }, LANGS));
        }
    }
    if (!$recipes) return [];
    $prodNames = [];
    if (!$raw && lang() !== 'it') {
        foreach (db()->query('SELECT id, name, i18n FROM ' . t('products')) as $p) $prodNames[(int)$p['id']] = loc($p, 'name', $p['name']);
    }
    foreach (db()->query('SELECT * FROM ' . t('recipe_ingredients') . ' ORDER BY recipe_id, sort, id') as $i) {
        $rid = (int)$i['recipe_id'];
        if (!isset($recipes[$rid])) continue;
        $pid = $i['product_id'] !== null ? (int)$i['product_id'] : null;
        $ing = [
            'productId' => $pid, 'name' => $raw ? $i['name'] : ($pid && isset($prodNames[$pid]) ? $prodNames[$pid] : loc($i, 'name', $i['name'])),
            'quantity' => f_num($i['quantity']), 'unit' => $i['unit'], 'notes' => $raw ? $i['notes'] : loc($i, 'notes', $i['notes']),
        ];
        if ($raw) $ing['i18n'] = (object)i18n_of($i);
        $recipes[$rid]['ingredients'][] = $ing;
    }
    foreach (db()->query('SELECT * FROM ' . t('recipe_steps') . ' ORDER BY recipe_id, sort, id') as $st) {
        $rid = (int)$st['recipe_id'];
        if (isset($recipes[$rid])) $recipes[$rid]['steps'][] = $st['text'];
    }
    if (!$raw && lang() !== 'it') {
        foreach ($recipes as $rid => &$rec) {
            $steps = i18n_of($rows[$rid])[lang()]['steps'] ?? null;
            if (is_array($steps) && $steps) $rec['steps'] = $steps;
        }
        unset($rec);
    }
    return array_values($recipes);
}

function load_catalog(bool $raw = false): array
{
    $fc = function ($r) use ($raw) {
        return fmt_category($r, $raw);
    };
    $fp = function ($r) use ($raw) {
        return fmt_product($r, $raw);
    };
    $cats = array_map($fc, db()->query('SELECT * FROM ' . t('categories') . ' ORDER BY sort, name')->fetchAll());
    $where = $raw ? '' : 'WHERE active = 1';
    $prods = array_map($fp, db()->query('SELECT * FROM ' . t('products') . " $where ORDER BY name")->fetchAll());
    if (!$raw) usort($prods, function ($a, $b) {
        return strcoll(mb_strtolower($a['name']), mb_strtolower($b['name']));
    });
    return ['categories' => $cats, 'products' => $prods];
}

function household_payload(array $u, int $hid): array
{
    $st = db()->prepare('SELECT id, name, invite_code, owner_id, created_at FROM ' . t('households') . ' WHERE id = ?');
    $st->execute([$hid]);
    $h = $st->fetch();
    $role = membership($hid, $u['id']);
    $st = db()->prepare('SELECT u.id, u.name, m.role, m.joined_at FROM ' . t('household_members') . ' m JOIN ' . t('users') . ' u ON u.id = m.user_id WHERE m.household_id = ? ORDER BY (m.role = \'owner\') DESC, m.joined_at');
    $st->execute([$hid]);
    $members = array_map(function ($m) {
        return ['id' => (int)$m['id'], 'name' => $m['name'], 'role' => $m['role'], 'joinedAt' => $m['joined_at']];
    }, $st->fetchAll());
    return [
        'id' => (int)$h['id'], 'name' => $h['name'], 'role' => $role,
        'inviteCode' => $h['invite_code'], 'members' => $members,
    ];
}

function state_payload(array $u): array
{
    $hid = active_household($u);
    $q = function (string $sql, array $args) {
        $st = db()->prepare($sql);
        $st->execute($args);
        return $st->fetchAll();
    };
    $spaces = array_map('fmt_space', $q('SELECT * FROM ' . t('spaces') . ' WHERE household_id = ? ORDER BY sort, id', [$hid]));
    $items = array_map('fmt_item', $q('SELECT i.*, u.name AS created_by_name FROM ' . t('items') . ' i LEFT JOIN ' . t('users') . ' u ON u.id = i.created_by WHERE i.household_id = ? ORDER BY i.expiry_date IS NULL, i.expiry_date, i.name', [$hid]));
    $lists = array_map('fmt_list', $q('SELECT * FROM ' . t('lists') . ' WHERE household_id = ? ORDER BY updated_at DESC, id DESC', [$hid]));
    $listItems = array_map('fmt_list_item', $q('SELECT li.* FROM ' . t('list_items') . ' li JOIN ' . t('lists') . ' l ON l.id = li.list_id WHERE l.household_id = ? ORDER BY li.in_cart, li.created_at, li.id', [$hid]));
    $plan = array_map(function ($r) {
        return ['id' => (int)$r['id'], 'date' => $r['plan_date'], 'slot' => $r['slot'], 'recipeId' => $r['recipe_id'] !== null ? (int)$r['recipe_id'] : null, 'title' => $r['title']];
    }, $q('SELECT * FROM ' . t('meal_plan') . ' WHERE household_id = ? AND plan_date >= (UTC_DATE() - INTERVAL 7 DAY) AND plan_date <= (UTC_DATE() + INTERVAL 35 DAY) ORDER BY plan_date, FIELD(slot, \'colazione\',\'pranzo\',\'merenda\',\'cena\')', [$hid]));
    $fav = array_map('intval', array_column($q('SELECT recipe_id FROM ' . t('favorites') . ' WHERE user_id = ?', [$u['id']]), 'recipe_id'));
    return [
        'household' => household_payload($u, $hid),
        'households' => user_households($u['id']),
        'spaces' => $spaces, 'items' => $items, 'lists' => $lists, 'listItems' => $listItems,
        'plan' => $plan, 'favorites' => $fav,
    ];
}

// ---------- Ownership checks ----------

function own_row(string $table, int $id, int $hid): array
{
    $st = db()->prepare('SELECT * FROM ' . t($table) . ' WHERE id = ? AND household_id = ?');
    $st->execute([$id, $hid]);
    $r = $st->fetch();
    if (!$r) fail('Item not found (another household member may have deleted it).', 404, 'not_found');
    return $r;
}

function own_list_item(int $id, int $hid): array
{
    $st = db()->prepare('SELECT li.* FROM ' . t('list_items') . ' li JOIN ' . t('lists') . ' l ON l.id = li.list_id WHERE li.id = ? AND l.household_id = ?');
    $st->execute([$id, $hid]);
    $r = $st->fetch();
    if (!$r) fail('Product not found (another household member may have deleted it).', 404, 'not_found');
    return $r;
}

function product_row(?int $id): ?array
{
    if (!$id) return null;
    $st = db()->prepare('SELECT * FROM ' . t('products') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function valid_category(?int $id): ?int
{
    if (!$id) return null;
    $st = db()->prepare('SELECT id FROM ' . t('categories') . ' WHERE id = ?');
    $st->execute([$id]);
    return $st->fetchColumn() ? $id : null;
}

function touch_list(int $listId): void
{
    db()->prepare('UPDATE ' . t('lists') . ' SET updated_at = ? WHERE id = ?')->execute([now_utc(), $listId]);
}

/** Validated item fields from the request (used by item.save and listitem.tospace). */
function item_fields(array $b): array
{
    $product = product_row(int_or_null($b['productId'] ?? $b['product_id'] ?? null));
    $name = s($b, 'name', 80, false, 'Name');
    if ($name === '' && $product) $name = $product['name'];
    if ($name === '') fail('Enter the product name.', 422, 'validation');
    $ns = strtoupper(s($b, 'nutriscore', 1));
    $type = ($b['expiryType'] ?? $b['expiry_type'] ?? '') === 'best_before' ? 'best_before' : 'use_by';
    $categoryId = valid_category(int_or_null($b['categoryId'] ?? $b['category_id'] ?? null));
    if (!$categoryId && $product && $product['category_id']) $categoryId = (int)$product['category_id'];
    return [
        'product_id' => $product ? (int)$product['id'] : null,
        'name' => $name,
        'brand' => s($b, 'brand', 80, false, 'Brand'),
        'category_id' => $categoryId,
        'icon' => icon_name($b['icon'] ?? ($product['icon'] ?? ''), 'products', $product['icon'] ?? 'generic'),
        'quantity' => num_or_null($b['quantity'] ?? null),
        'unit' => unit_or_default($b['unit'] ?? ($product['default_unit'] ?? 'pz')),
        'expiry_date' => date_or_null($b['expiryDate'] ?? $b['expiry_date'] ?? null),
        'expiry_type' => $type,
        'nutriscore' => in_array($ns, NUTRISCORES, true) ? $ns : null,
        'barcode' => preg_replace('/\D/', '', s($b, 'barcode', 32)),
        'notes' => s($b, 'notes', 500, false, 'Notes'),
    ];
}

function insert_item(int $hid, int $spaceId, array $f, int $userId): int
{
    $now = now_utc();
    db()->prepare('INSERT INTO ' . t('items') . ' (household_id, space_id, product_id, name, brand, category_id, icon, quantity, unit, expiry_date, expiry_type, nutriscore, barcode, notes, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$hid, $spaceId, $f['product_id'], $f['name'], $f['brand'], $f['category_id'], $f['icon'], $f['quantity'], $f['unit'], $f['expiry_date'], $f['expiry_type'], $f['nutriscore'], $f['barcode'], $f['notes'], $userId, $now, $now]);
    return (int)db()->lastInsertId();
}

/** Logs an action into the household activity history (for audit and undo). */
function log_history(int $hid, ?int $userId, string $userName, string $action, ?int $itemId, string $itemName, ?int $spaceId, string $spaceName, ?float $quantity, string $unit, array $details): int
{
    $now = now_utc();
    $sql = 'INSERT INTO ' . t('history') . ' (household_id, user_id, user_name, action, item_id, item_name, space_id, space_name, quantity, unit, details, undone, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,0,?)';
    $params = [$hid, $userId, $userName, $action, $itemId, $itemName, $spaceId, $spaceName, $quantity, $unit, json_encode($details, JSON_UNESCAPED_UNICODE), $now];
    try {
        $st = db()->prepare($sql);
        $st->execute($params);
        return (int)db()->lastInsertId();
    } catch (Throwable $e) {
        try {
            require_once __DIR__ . '/../lib/schema.php';
            install_schema(db(), (string)config_value('prefix', 'ft_'));
            $st = db()->prepare($sql);
            $st->execute($params);
            return (int)db()->lastInsertId();
        } catch (Throwable $ignored) {
            return 0;
        }
    }
}

/** Records a household event that is not about a product in a space (these events cannot be undone). */
function log_event(array $u, int $hid, string $action, string $name, array $details = [], string $listName = ''): void
{
    log_history($hid, (int)$u['id'], (string)$u['name'], $action, null, mb_substr($name, 0, 80), null, mb_substr($listName, 0, 60), null, 'pz', $details);
}

/** Creates the first household of a new user. */
function create_first_household(int $userId, string $userName): void
{
    $name = tr("{name}'s home", ['name' => first_name($userName)]);
    log_event(['id' => $userId, 'name' => $userName], create_household($userId, $name), 'household_create', $name);
}

/** Records in each household of a user that is about to be deleted that the user left. */
function log_account_removal(array $user): void
{
    foreach (user_households((int)$user['id']) as $hh) log_event($user, $hh['id'], 'member_leave', (string)$user['name']);
}

/** Name of a shopping list, for the history. */
function list_name(int $listId): string
{
    $st = db()->prepare('SELECT name FROM ' . t('lists') . ' WHERE id = ?');
    $st->execute([$listId]);
    return (string)$st->fetchColumn();
}

/** What a meal plan entry is called: the recipe name, otherwise its free title. */
function plan_name(?int $recipeId, string $title): string
{
    if (!$recipeId) return $title;
    $st = db()->prepare('SELECT * FROM ' . t('recipes') . ' WHERE id = ?');
    $st->execute([$recipeId]);
    $r = $st->fetch();
    return $r ? loc($r, 'name', $r['name']) : $title;
}

// ---------- Admin helpers ----------

/** Can $actor change/delete $target? Admins act on users only, the master also on admins. Nobody on the master or on themselves. */
function can_manage(array $actor, array $target): bool
{
    if ((int)$target['id'] === (int)$actor['id']) return false;
    if ((int)$target['role'] >= ROLE_MASTER) return false;
    if ((int)$target['role'] >= ROLE_ADMIN) return (int)$actor['role'] >= ROLE_MASTER;
    return (int)$actor['role'] >= ROLE_ADMIN;
}

function user_row(int $id): array
{
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE id = ?');
    $st->execute([$id]);
    $r = $st->fetch();
    if (!$r) fail('User not found.', 404, 'not_found');
    return $r;
}

function save_recipe_image(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $e = (int)($file['error'] ?? 4);
        fail($e === UPLOAD_ERR_INI_SIZE || $e === UPLOAD_ERR_FORM_SIZE ? 'Image too large.' : 'Upload failed.', 422, 'upload');
    }
    if ($file['size'] > 6 * 1024 * 1024) fail('The image cannot exceed 6 MB.', 422, 'upload');
    $info = @getimagesize($file['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($types[$info[2]])) fail('Unsupported format: use JPG, PNG or WebP.', 422, 'upload');
    $dir = dirname(__DIR__) . '/uploads/recipes';
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) fail('The uploads/recipes folder is not writable.', 500, 'upload');
    $name = bin2hex(random_bytes(10));

    // Re-encode with GD when available: removes metadata and limits the size to 1600 px
    if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
        $src = @imagecreatefromstring((string)file_get_contents($file['tmp_name']));
        if ($src) {
            $w = imagesx($src);
            $h = imagesy($src);
            $scale = min(1, 1600 / max($w, $h));
            $nw = (int)round($w * $scale);
            $nh = (int)round($h * $scale);
            $dst = imagecreatetruecolor($nw, $nh);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            $path = "$dir/$name.jpg";
            if (!imagejpeg($dst, $path, 84)) fail('Could not save the image.', 500, 'upload');
            imagedestroy($src);
            imagedestroy($dst);
            return "uploads/recipes/$name.jpg";
        }
    }
    $ext = $types[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], "$dir/$name.$ext")) fail('Could not save the image.', 500, 'upload');
    return "uploads/recipes/$name.$ext";
}

function delete_upload(string $path): void
{
    if (strpos($path, 'uploads/recipes/') !== 0 || !preg_match('#^uploads/recipes/[a-f0-9]{20}\.(jpg|png|webp)$#', $path)) return;
    $full = dirname(__DIR__) . '/' . $path;
    if (is_file($full)) @unlink($full);
}

// =====================================================================

switch ($route) {

    // ---------- Authentication ----------

    case 'auth.me':
        only('GET');
        $u = current_user();
        if (!$u) send_json(['ok' => true, 'user' => null]);
        send_json(['ok' => true, 'user' => public_user($u), 'csrf' => $u['csrf_token']]);

    case 'auth.config':
        // Public settings for the login page
        only('GET');
        send_json(['ok' => true, 'googleClientId' => (string)config_value('google_client_id', '')]);

    case 'auth.google':
        // Sign in (or sign up) with a Google account. A new account needs the privacy consent:
        // the first call answers "needs_signup" and the app asks for it, then calls again.
        only('POST');
        if (too_many_attempts('login', 20, 15)) fail('Too many attempts: try again in a few minutes.', 429, 'rate_limited');
        $b = body();
        $g = verify_google_token((string)($b['credential'] ?? ''));
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE google_sub = ?');
        $st->execute([$g['sub']]);
        $row = $st->fetch();
        if (!$row) {
            // Existing account with the same (verified) email: link it to Google
            $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
            $st->execute([$g['email']]);
            $row = $st->fetch();
            if ($row) db()->prepare('UPDATE ' . t('users') . ' SET google_sub = ? WHERE id = ?')->execute([$g['sub'], $row['id']]);
        }
        if (!$row) {
            if (empty($b['acceptPrivacy'])) send_json(['ok' => false, 'error' => 'needs_signup', 'message' => tr('Create your FoodTrack account'), 'name' => $g['name'], 'email' => $g['email']], 200);
            if (too_many_attempts('register', 5, 60)) fail('Too many sign-ups from this network: try again later.', 429, 'rate_limited');
            record_attempt('register');
            $name = $g['name'] ?: explode('@', $g['email'])[0];
            db()->prepare('INSERT INTO ' . t('users') . " (name, email, password_hash, google_sub, role, status, created_at) VALUES (?,?, '', ?, ?, 'active', ?)")
                ->execute([$name, $g['email'], $g['sub'], ROLE_USER, now_utc()]);
            $uid = (int)db()->lastInsertId();
            create_first_household($uid, $name);
            $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string)($b['inviteCode'] ?? '')));
            if ($code !== '') {
                $st = db()->prepare('SELECT id FROM ' . t('households') . ' WHERE invite_code = ?');
                $st->execute([$code]);
                $hid = $st->fetchColumn();
                if ($hid) {
                    db()->prepare('INSERT IGNORE INTO ' . t('household_members') . " (household_id, user_id, role, joined_at) VALUES (?,?, 'member', ?)")->execute([$hid, $uid, now_utc()]);
                    db()->prepare('UPDATE ' . t('users') . ' SET active_household_id = ? WHERE id = ?')->execute([$hid, $uid]);
                    log_event(['id' => $uid, 'name' => $name], (int)$hid, 'member_join', $name);
                }
            }
            $row = user_row($uid);
            mail_account_created($row, 'google');
        }
        if ($row['status'] !== 'active') fail('Your account has been disabled. Contact the administrator.', 403, 'disabled');
        db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        $csrf = create_auth_session((int)$row['id']);
        send_json(['ok' => true, 'user' => public_user(user_row((int)$row['id'])), 'csrf' => $csrf]);

    case 'auth.google.link':
        $u = writer();
        $g = verify_google_token((string)(body()['credential'] ?? ''));
        $st = db()->prepare('SELECT id FROM ' . t('users') . ' WHERE google_sub = ? AND id <> ?');
        $st->execute([$g['sub'], $u['id']]);
        if ($st->fetchColumn()) fail('This Google account is already linked to another FoodTrack account.', 409, 'google_taken');
        db()->prepare('UPDATE ' . t('users') . ' SET google_sub = ? WHERE id = ?')->execute([$g['sub'], $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.google.unlink':
        $u = writer();
        $row = user_row($u['id']);
        if ($row['password_hash'] === '') fail('Set a password first, otherwise you could no longer sign in.', 422, 'no_password');
        db()->prepare('UPDATE ' . t('users') . ' SET google_sub = NULL WHERE id = ?')->execute([$u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

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
        db()->prepare('INSERT INTO ' . t('users') . " (name, email, password_hash, role, status, created_at, last_login_at) VALUES (?,?,?,?, 'active', ?, ?)")
            ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT), ROLE_USER, now_utc(), now_utc()]);
        $uid = (int)db()->lastInsertId();
        create_first_household($uid, $name);
        // Optional: join a household right away with an invite code
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string)($b['inviteCode'] ?? '')));
        if ($code !== '') {
            $st = db()->prepare('SELECT id FROM ' . t('households') . ' WHERE invite_code = ?');
            $st->execute([$code]);
            $hid = $st->fetchColumn();
            if ($hid) {
                db()->prepare('INSERT IGNORE INTO ' . t('household_members') . " (household_id, user_id, role, joined_at) VALUES (?,?, 'member', ?)")->execute([$hid, $uid, now_utc()]);
                db()->prepare('UPDATE ' . t('users') . ' SET active_household_id = ? WHERE id = ?')->execute([$hid, $uid]);
                log_event(['id' => $uid, 'name' => $name], (int)$hid, 'member_join', $name);
            }
        }
        mail_account_created(user_row($uid), 'email');
        $csrf = create_auth_session($uid);
        send_json(['ok' => true, 'user' => public_user(user_row($uid)), 'csrf' => $csrf], 201);

    case 'auth.login':
        only('POST');
        if (too_many_attempts('login', 10, 15)) fail('Too many attempts: try again in a few minutes.', 429, 'rate_limited');
        $b = body();
        $email = mb_strtolower(s($b, 'email', 190, true, 'Email'));
        $pw = (string)($b['password'] ?? '');
        $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        $row = $st->fetch();
        if ($row && $row['password_hash'] === '') fail('This account uses Google sign-in: tap “Continue with Google”.', 401, 'use_google');
        if (!$row || !password_verify($pw, $row['password_hash'])) {
            record_attempt('login');
            fail('Wrong email or password.', 401, 'bad_credentials');
        }
        if ($row['status'] !== 'active') fail('Your account has been disabled. Contact the administrator.', 403, 'disabled');
        clear_attempts('login');
        if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) {
            db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $row['id']]);
        }
        db()->prepare('UPDATE ' . t('users') . ' SET last_login_at = ? WHERE id = ?')->execute([now_utc(), $row['id']]);
        $csrf = create_auth_session((int)$row['id']);
        send_json(['ok' => true, 'user' => public_user($row), 'csrf' => $csrf]);

    case 'auth.logout':
        only('POST');
        $u = current_user();
        if ($u) require_csrf($u);
        destroy_auth_session();
        send_json(['ok' => true]);

    case 'auth.profile':
        $u = writer();
        $name = s(body(), 'name', 80, true, 'Name');
        if (mb_strlen($name) < 2) fail('Enter your name.', 422, 'validation');
        db()->prepare('UPDATE ' . t('users') . ' SET name = ? WHERE id = ?')->execute([$name, $u['id']]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.password':
        $u = writer(ROLE_USER, true);
        $b = body();
        $row = user_row($u['id']);
        if ($row['password_hash'] !== '' && !password_verify((string)($b['current'] ?? ''), $row['password_hash'])) fail('The current password is wrong.', 422, 'bad_password');
        $new = (string)($b['password'] ?? '');
        validate_password($new);
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ?, must_change_password = 0 WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $u['id']]);
        // Sign out the other devices
        $st = db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ? AND token_hash <> ?');
        $st->execute([$u['id'], hash('sha256', (string)($_COOKIE[AUTH_COOKIE] ?? ''))]);
        send_json(['ok' => true, 'user' => public_user(user_row($u['id']))]);

    case 'auth.delete':
        $u = writer(ROLE_USER, true);
        if ($u['role'] >= ROLE_MASTER) fail('The master admin cannot delete their own account: first hand the role over to another administrator.', 422, 'master');
        $row = user_row($u['id']);
        if ($row['password_hash'] === '') {
            if (!in_array(mb_strtoupper(trim((string)(body()['confirm'] ?? ''))), ['ELIMINA', 'DELETE'], true)) fail('Type DELETE to confirm.', 422, 'bad_password');
        } elseif (!password_verify((string)(body()['password'] ?? ''), $row['password_hash'])) fail('Wrong password.', 422, 'bad_password');
        destroy_auth_session();
        log_account_removal($u);
        delete_user_account($u['id']);
        send_json(['ok' => true]);

    // ---------- App data ----------

    case 'bootstrap':
        only('GET');
        $u = require_user();
        send_json(['ok' => true, 'catalog' => load_catalog(), 'recipes' => load_recipes(true)] + state_payload($u));

    case 'state':
        only('GET');
        $u = require_user();
        send_json(['ok' => true] + state_payload($u));

    case 'recipes':
        only('GET');
        require_user();
        send_json(['ok' => true, 'catalog' => load_catalog(), 'recipes' => load_recipes(true)]);

    // ---------- Households (shared spaces) ----------

    case 'household.create':
        $u = writer();
        $name = s(body(), 'name', 80, true, 'Name');
        $st = db()->prepare('SELECT COUNT(*) FROM ' . t('household_members') . ' WHERE user_id = ?');
        $st->execute([$u['id']]);
        if ((int)$st->fetchColumn() >= 10) fail('You can be a member of at most 10 households.', 422, 'limit');
        log_event($u, create_household($u['id'], $name), 'household_create', $name);
        send_json(['ok' => true] + state_payload(require_user()));

    case 'household.switch':
        $u = writer();
        $hid = (int)(body()['id'] ?? 0);
        if (!membership($hid, $u['id'])) fail('You are not a member of this household.', 403, 'forbidden');
        db()->prepare('UPDATE ' . t('users') . ' SET active_household_id = ? WHERE id = ?')->execute([$hid, $u['id']]);
        $u['active_household_id'] = $hid;
        send_json(['ok' => true] + state_payload($u));

    case 'household.rename':
        $u = writer();
        $hid = active_household($u);
        if (membership($hid, $u['id']) !== 'owner') fail('Only the owner can rename the household.', 403, 'forbidden');
        $name = s(body(), 'name', 80, true, 'Name');
        $st = db()->prepare('SELECT name FROM ' . t('households') . ' WHERE id = ?');
        $st->execute([$hid]);
        $oldName = (string)$st->fetchColumn();
        db()->prepare('UPDATE ' . t('households') . ' SET name = ? WHERE id = ?')->execute([$name, $hid]);
        if ($oldName !== $name) log_event($u, $hid, 'household_rename', $name, ['from' => $oldName, 'to' => $name]);
        send_json(['ok' => true] + state_payload($u));

    case 'household.code':
        $u = writer();
        $hid = active_household($u);
        if (membership($hid, $u['id']) !== 'owner') fail('Only the owner can regenerate the invite code.', 403, 'forbidden');
        db()->prepare('UPDATE ' . t('households') . ' SET invite_code = ? WHERE id = ?')->execute([unique_invite_code(), $hid]);
        log_event($u, $hid, 'household_code', '');
        send_json(['ok' => true] + state_payload($u));

    case 'household.join':
        $u = writer();
        if (too_many_attempts('join', 10, 30)) fail('Too many attempts: try again later.', 429, 'rate_limited');
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', s(body(), 'code', 20, true, 'Code')));
        $st = db()->prepare('SELECT id FROM ' . t('households') . ' WHERE invite_code = ?');
        $st->execute([$code]);
        $hid = $st->fetchColumn();
        if (!$hid) {
            record_attempt('join');
            fail('Invalid invite code.', 404, 'not_found');
        }
        $hid = (int)$hid;
        if (!membership($hid, $u['id'])) {
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('household_members') . ' WHERE household_id = ?');
            $st->execute([$hid]);
            if ((int)$st->fetchColumn() >= 20) fail('This household already has the maximum number of members (20).', 422, 'limit');
            db()->prepare('INSERT INTO ' . t('household_members') . " (household_id, user_id, role, joined_at) VALUES (?,?, 'member', ?)")->execute([$hid, $u['id'], now_utc()]);
            log_event($u, $hid, 'member_join', $u['name']);
        }
        db()->prepare('UPDATE ' . t('users') . ' SET active_household_id = ? WHERE id = ?')->execute([$hid, $u['id']]);
        $u['active_household_id'] = $hid;
        send_json(['ok' => true] + state_payload($u));

    case 'household.leave':
        $u = writer();
        $hid = active_household($u);
        log_event($u, $hid, 'member_leave', $u['name']);
        leave_household($hid, $u['id']);
        db()->prepare('UPDATE ' . t('users') . ' SET active_household_id = NULL WHERE id = ?')->execute([$u['id']]);
        $u['active_household_id'] = null;
        send_json(['ok' => true] + state_payload($u));

    case 'household.member.remove':
        $u = writer();
        $hid = active_household($u);
        if (membership($hid, $u['id']) !== 'owner') fail('Only the owner can remove members.', 403, 'forbidden');
        $target = (int)(body()['userId'] ?? 0);
        if ($target === $u['id']) fail('To leave the household use “Leave this household”.', 422, 'validation');
        if (membership($hid, $target) !== null) log_event($u, $hid, 'member_remove', user_row($target)['name']);
        db()->prepare('DELETE FROM ' . t('household_members') . ' WHERE household_id = ? AND user_id = ?')->execute([$hid, $target]);
        send_json(['ok' => true] + state_payload($u));

    case 'household.transfer':
        $u = writer();
        $hid = active_household($u);
        if (membership($hid, $u['id']) !== 'owner') fail('Only the owner can hand over the household.', 403, 'forbidden');
        $target = (int)(body()['userId'] ?? 0);
        if (membership($hid, $target) === null) fail('This user is not a member of this household.', 422, 'validation');
        db()->prepare('UPDATE ' . t('household_members') . " SET role = 'member' WHERE household_id = ?")->execute([$hid]);
        db()->prepare('UPDATE ' . t('household_members') . " SET role = 'owner' WHERE household_id = ? AND user_id = ?")->execute([$hid, $target]);
        db()->prepare('UPDATE ' . t('households') . ' SET owner_id = ? WHERE id = ?')->execute([$target, $hid]);
        log_event($u, $hid, 'household_transfer', user_row($target)['name']);
        send_json(['ok' => true] + state_payload($u));

    case 'household.delete':
        $u = writer();
        $hid = active_household($u);
        if (membership($hid, $u['id']) !== 'owner') fail('Only the owner can delete the household.', 403, 'forbidden');
        db()->prepare('DELETE FROM ' . t('households') . ' WHERE id = ?')->execute([$hid]);
        db()->prepare('UPDATE ' . t('users') . ' SET active_household_id = NULL WHERE id = ?')->execute([$u['id']]);
        $u['active_household_id'] = null;
        send_json(['ok' => true] + state_payload($u));

    // ---------- Spaces ----------

    case 'space.save':
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $name = s($b, 'name', 60, true, 'Name');
        $icon = icon_name($b['icon'] ?? '', 'spaces', 'pantry');
        $notes = s($b, 'notes', 500, false, 'Notes');
        $env = environment_or_null($b['environment'] ?? null);
        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $sp = own_row('spaces', $id, $hid);
            db()->prepare('UPDATE ' . t('spaces') . ' SET name = ?, icon = ?, environment = ?, notes = ?, updated_at = ? WHERE id = ?')->execute([$name, $icon, $env ?? $sp['environment'], $notes, now_utc(), $id]);
            if ($sp['name'] !== $name) log_event($u, $hid, 'space_rename', $name, ['from' => $sp['name'], 'to' => $name]);
            elseif ($sp['icon'] !== $icon || $sp['environment'] !== ($env ?? $sp['environment']) || $sp['notes'] !== $notes) log_event($u, $hid, 'space_edit', $name);
        } else {
            $st = db()->prepare('SELECT COUNT(*), COALESCE(MAX(sort), 0) FROM ' . t('spaces') . ' WHERE household_id = ?');
            $st->execute([$hid]);
            [$count, $max] = $st->fetch(PDO::FETCH_NUM);
            if ((int)$count >= 50) fail('You reached the maximum number of spaces (50).', 422, 'limit');
            db()->prepare('INSERT INTO ' . t('spaces') . ' (household_id, name, icon, environment, notes, sort, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?)')
                ->execute([$hid, $name, $icon, $env ?? 'ambient', $notes, (int)$max + 10, $u['id'], now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
            log_event($u, $hid, 'space_create', $name);
        }
        send_json(['ok' => true, 'id' => $id] + state_payload($u));

    case 'space.delete':
        $u = writer();
        $hid = active_household($u);
        $id = (int)(body()['id'] ?? 0);
        $sp = own_row('spaces', $id, $hid);
        $st = db()->prepare('SELECT COUNT(*) FROM ' . t('items') . ' WHERE space_id = ?');
        $st->execute([$id]);
        $products = (int)$st->fetchColumn();
        db()->prepare('DELETE FROM ' . t('spaces') . ' WHERE id = ?')->execute([$id]);
        log_event($u, $hid, 'space_delete', $sp['name'], ['n' => $products]);
        send_json(['ok' => true] + state_payload($u));

    // ---------- Items in the spaces ----------

    case 'item.save':
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $spaceId = (int)($b['spaceId'] ?? 0);
        own_row('spaces', $spaceId, $hid);
        $f = item_fields($b);
        $id = (int)($b['id'] ?? 0);
        $spRow = db()->query('SELECT name FROM ' . t('spaces') . ' WHERE id = ' . $spaceId)->fetch();
        $spName = $spRow ? $spRow['name'] : '';
        if ($id) {
            $prev = own_row('items', $id, $hid);
            db()->prepare('UPDATE ' . t('items') . ' SET space_id = ?, product_id = ?, name = ?, brand = ?, category_id = ?, icon = ?, quantity = ?, unit = ?, expiry_date = ?, expiry_type = ?, nutriscore = ?, barcode = ?, notes = ?, updated_at = ? WHERE id = ?')
                ->execute([$spaceId, $f['product_id'], $f['name'], $f['brand'], $f['category_id'], $f['icon'], $f['quantity'], $f['unit'], $f['expiry_date'], $f['expiry_type'], $f['nutriscore'], $f['barcode'], $f['notes'], now_utc(), $id]);
            log_history($hid, $u['id'], $u['name'], 'edit', $id, $f['name'], $spaceId, $spName, $f['quantity'], $f['unit'], ['prev_snapshot' => $prev]);
        } else {
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('items') . ' WHERE household_id = ?');
            $st->execute([$hid]);
            if ((int)$st->fetchColumn() >= 3000) fail('You reached the maximum number of products (3000).', 422, 'limit');
            $id = insert_item($hid, $spaceId, $f, $u['id']);
            log_history($hid, $u['id'], $u['name'], 'add', $id, $f['name'], $spaceId, $spName, $f['quantity'], $f['unit'], [
                'item_snapshot' => array_merge($f, ['id' => $id, 'space_id' => $spaceId, 'household_id' => $hid])
            ]);
        }
        send_json(['ok' => true, 'id' => $id] + state_payload($u));

    case 'item.delete':
        $u = writer();
        $hid = active_household($u);
        $id = (int)(body()['id'] ?? 0);
        $row = own_row('items', $id, $hid);
        $spRow = db()->query('SELECT name FROM ' . t('spaces') . ' WHERE id = ' . (int)$row['space_id'])->fetch();
        db()->prepare('DELETE FROM ' . t('items') . ' WHERE id = ?')->execute([$id]);
        log_history($hid, $u['id'], $u['name'], 'delete', $id, $row['name'], (int)$row['space_id'], $spRow ? $spRow['name'] : '', $row['quantity'] !== null ? (float)$row['quantity'] : null, $row['unit'], [
            'item_snapshot' => $row
        ]);
        send_json(['ok' => true] + state_payload($u));

    case 'item.consume':
        // Marks an item as consumed: entirely (deleted) or only a part of the quantity
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $id = (int)($b['id'] ?? 0);
        $row = own_row('items', $id, $hid);
        $used = num_or_null($b['quantity'] ?? null);
        $spRow = db()->query('SELECT name FROM ' . t('spaces') . ' WHERE id = ' . (int)$row['space_id'])->fetch();
        $spName = $spRow ? $spRow['name'] : '';
        if ($used !== null && $row['quantity'] !== null && $used < (float)$row['quantity']) {
            $newQty = round((float)$row['quantity'] - $used, 3);
            db()->prepare('UPDATE ' . t('items') . ' SET quantity = ?, updated_at = ? WHERE id = ?')->execute([$newQty, now_utc(), $id]);
            log_history($hid, $u['id'], $u['name'], 'consume_part', $id, $row['name'], (int)$row['space_id'], $spName, $used, $row['unit'], [
                'qty_before' => (float)$row['quantity'],
                'qty_consumed' => $used,
                'qty_after' => $newQty,
                'item_snapshot' => $row
            ]);
        } else {
            db()->prepare('DELETE FROM ' . t('items') . ' WHERE id = ?')->execute([$id]);
            log_history($hid, $u['id'], $u['name'], 'consume', $id, $row['name'], (int)$row['space_id'], $spName, $row['quantity'] !== null ? (float)$row['quantity'] : null, $row['unit'], [
                'item_snapshot' => $row
            ]);
        }
        send_json(['ok' => true] + state_payload($u));

    // ---------- Shopping lists ----------

    case 'list.save':
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $name = s($b, 'name', 60, true, 'Name');
        $icon = icon_name($b['icon'] ?? '', 'lists', 'cart');
        $notes = s($b, 'notes', 500, false, 'Notes');
        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $old = own_row('lists', $id, $hid);
            db()->prepare('UPDATE ' . t('lists') . ' SET name = ?, icon = ?, notes = ?, updated_at = ? WHERE id = ?')->execute([$name, $icon, $notes, now_utc(), $id]);
            if ($old['name'] !== $name) log_event($u, $hid, 'list_rename', $name, ['from' => $old['name'], 'to' => $name]);
            elseif ($old['icon'] !== $icon || $old['notes'] !== $notes) log_event($u, $hid, 'list_edit', $name);
        } else {
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('lists') . ' WHERE household_id = ?');
            $st->execute([$hid]);
            if ((int)$st->fetchColumn() >= 100) fail('You reached the maximum number of lists (100).', 422, 'limit');
            db()->prepare('INSERT INTO ' . t('lists') . ' (household_id, name, icon, notes, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?)')
                ->execute([$hid, $name, $icon, $notes, $u['id'], now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
            log_event($u, $hid, 'list_create', $name);
        }
        send_json(['ok' => true, 'id' => $id] + state_payload($u));

    case 'list.delete':
        $u = writer();
        $hid = active_household($u);
        $id = (int)(body()['id'] ?? 0);
        $old = own_row('lists', $id, $hid);
        db()->prepare('DELETE FROM ' . t('lists') . ' WHERE id = ?')->execute([$id]);
        log_event($u, $hid, 'list_delete', $old['name']);
        send_json(['ok' => true] + state_payload($u));

    case 'list.clearcart':
        // Removes from the list all the products already in the cart
        $u = writer();
        $hid = active_household($u);
        $id = (int)(body()['id'] ?? 0);
        $old = own_row('lists', $id, $hid);
        $st = db()->prepare('DELETE FROM ' . t('list_items') . ' WHERE list_id = ? AND in_cart = 1');
        $st->execute([$id]);
        if ($st->rowCount()) log_event($u, $hid, 'list_clearcart', $old['name'], ['n' => $st->rowCount()], $old['name']);
        touch_list($id);
        send_json(['ok' => true] + state_payload($u));

    case 'listitem.save':
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $listId = (int)($b['listId'] ?? 0);
        own_row('lists', $listId, $hid);
        $product = product_row(int_or_null($b['productId'] ?? null));
        $name = s($b, 'name', 80, false, 'Name');
        if ($name === '' && $product) $name = $product['name'];
        if ($name === '') fail('Enter the product name.', 422, 'validation');
        $icon = icon_name($b['icon'] ?? ($product['icon'] ?? ''), 'products', $product['icon'] ?? 'generic');
        $qty = num_or_null($b['quantity'] ?? null);
        $unit = unit_or_default($b['unit'] ?? ($product['default_unit'] ?? 'pz'));
        $notes = s($b, 'notes', 500, false, 'Notes');
        $env = environment_or_null($b['environment'] ?? null);
        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $old = own_list_item($id, $hid);
            if (!array_key_exists('environment', $b)) $env = $old['environment'];
            db()->prepare('UPDATE ' . t('list_items') . ' SET product_id = ?, name = ?, icon = ?, quantity = ?, unit = ?, notes = ?, environment = ?, updated_at = ? WHERE id = ?')
                ->execute([$product ? (int)$product['id'] : null, $name, $icon, $qty, $unit, $notes, $env, now_utc(), $id]);
            $oldQty = $old['quantity'] === null ? null : (float)$old['quantity'];
            $newQty = $qty === null ? null : (float)$qty;
            if ($old['name'] !== $name || $oldQty !== $newQty || $old['unit'] !== $unit || $old['notes'] !== $notes || $old['environment'] !== $env) log_event($u, $hid, 'listitem_edit', $name, [], list_name($listId));
        } else {
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('list_items') . ' WHERE list_id = ?');
            $st->execute([$listId]);
            if ((int)$st->fetchColumn() >= 500) fail('This list already has too many products (500).', 422, 'limit');
            db()->prepare('INSERT INTO ' . t('list_items') . ' (list_id, product_id, name, icon, quantity, unit, notes, environment, in_cart, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,0,?,?,?)')
                ->execute([$listId, $product ? (int)$product['id'] : null, $name, $icon, $qty, $unit, $notes, $env, $u['id'], now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
            log_event($u, $hid, 'listitem_add', $name, [], list_name($listId));
        }
        touch_list($listId);
        send_json(['ok' => true, 'id' => $id] + state_payload($u));

    case 'listitem.addmany':
        // Adds several products at once (e.g. the missing ingredients of a recipe or of the meal plan)
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $listId = (int)($b['listId'] ?? 0);
        own_row('lists', $listId, $hid);
        $rows = is_array($b['items'] ?? null) ? array_slice($b['items'], 0, 100) : [];
        $ins = db()->prepare('INSERT INTO ' . t('list_items') . ' (list_id, product_id, name, icon, quantity, unit, notes, in_cart, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,0,?,?,?)');
        $n = 0;
        foreach ($rows as $r) {
            if (!is_array($r)) continue;
            $product = product_row(int_or_null($r['productId'] ?? null));
            $name = mb_substr(trim((string)($r['name'] ?? '')), 0, 80) ?: ($product['name'] ?? '');
            if ($name === '') continue;
            $ins->execute([$listId, $product ? (int)$product['id'] : null, $name, $product['icon'] ?? 'generic', num_or_null($r['quantity'] ?? null), unit_or_default($r['unit'] ?? ''), mb_substr(trim((string)($r['notes'] ?? '')), 0, 500), $u['id'], now_utc(), now_utc()]);
            $n++;
        }
        if ($n) log_event($u, $hid, 'listitem_addmany', list_name($listId), ['n' => $n], list_name($listId));
        touch_list($listId);
        send_json(['ok' => true, 'added' => $n] + state_payload($u));

    case 'listitem.delete':
        $u = writer();
        $hid = active_household($u);
        $row = own_list_item((int)(body()['id'] ?? 0), $hid);
        db()->prepare('DELETE FROM ' . t('list_items') . ' WHERE id = ?')->execute([$row['id']]);
        log_event($u, $hid, 'listitem_delete', $row['name'], [], list_name((int)$row['list_id']));
        touch_list((int)$row['list_id']);
        send_json(['ok' => true] + state_payload($u));

    case 'listitem.cart':
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $row = own_list_item((int)($b['id'] ?? 0), $hid);
        db()->prepare('UPDATE ' . t('list_items') . ' SET in_cart = ?, updated_at = ? WHERE id = ?')->execute([!empty($b['inCart']) ? 1 : 0, now_utc(), $row['id']]);
        if ((bool)$row['in_cart'] !== !empty($b['inCart'])) log_event($u, $hid, !empty($b['inCart']) ? 'listitem_cart' : 'listitem_uncart', $row['name'], [], list_name((int)$row['list_id']));
        touch_list((int)$row['list_id']);
        send_json(['ok' => true] + state_payload($u));

    case 'listitem.tospace':
        // Moves a bought product from a shopping list into a space of the house
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $row = own_list_item((int)($b['id'] ?? 0), $hid);
        $spaceId = (int)($b['spaceId'] ?? 0);
        own_row('spaces', $spaceId, $hid);
        if (!isset($b['productId'])) $b['productId'] = $row['product_id'];
        if (!isset($b['name']) || trim((string)$b['name']) === '') $b['name'] = $row['name'];
        if (!isset($b['icon'])) $b['icon'] = $row['icon'];
        $f = item_fields($b);
        db()->beginTransaction();
        $newId = insert_item($hid, $spaceId, $f, $u['id']);
        db()->prepare('DELETE FROM ' . t('list_items') . ' WHERE id = ?')->execute([$row['id']]);
        db()->commit();
        $spRow = db()->query('SELECT name FROM ' . t('spaces') . ' WHERE id = ' . $spaceId)->fetch();
        log_history($hid, $u['id'], $u['name'], 'move_from_list', $newId, $f['name'], $spaceId, $spRow ? $spRow['name'] : '', $f['quantity'], $f['unit'], [
            'item_snapshot' => array_merge($f, ['id' => $newId, 'space_id' => $spaceId, 'household_id' => $hid])
        ]);
        touch_list((int)$row['list_id']);
        send_json(['ok' => true, 'id' => $newId] + state_payload($u));

    // ---------- Recipes: favorites and meal plan ----------

    case 'favorite.toggle':
        $u = writer();
        $rid = (int)(body()['recipeId'] ?? 0);
        $st = db()->prepare('SELECT * FROM ' . t('recipes') . ' WHERE id = ?');
        $st->execute([$rid]);
        $rec = $st->fetch();
        if (!$rec || recipe_status($rec, lang()) !== 'published') fail('Recipe not found.', 404, 'not_found');
        $st = db()->prepare('DELETE FROM ' . t('favorites') . ' WHERE user_id = ? AND recipe_id = ?');
        $st->execute([$u['id'], $rid]);
        $fav = false;
        if ($st->rowCount() === 0) {
            db()->prepare('INSERT INTO ' . t('favorites') . ' (user_id, recipe_id, created_at) VALUES (?,?,?)')->execute([$u['id'], $rid, now_utc()]);
            $fav = true;
        }
        send_json(['ok' => true, 'favorite' => $fav]);

    case 'plan.save':
        $u = writer();
        $hid = active_household($u);
        $b = body();
        $date = date_or_null($b['date'] ?? null);
        if (!$date) fail('Choose the day.', 422, 'validation');
        $slot = (string)($b['slot'] ?? '');
        if (!in_array($slot, MEAL_SLOTS, true)) fail('Invalid meal.', 422, 'validation');
        $rid = int_or_null($b['recipeId'] ?? null);
        if ($rid) {
            $st = db()->prepare('SELECT 1 FROM ' . t('recipes') . ' WHERE id = ?');
            $st->execute([$rid]);
            if (!$st->fetchColumn()) $rid = null;
        }
        $title = s($b, 'title', 120, false, 'Title');
        if (!$rid && $title === '') fail('Choose a recipe or write what you want to eat.', 422, 'validation');
        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $old = own_row('meal_plan', $id, $hid);
            db()->prepare('UPDATE ' . t('meal_plan') . ' SET plan_date = ?, slot = ?, recipe_id = ?, title = ? WHERE id = ?')->execute([$date, $slot, $rid, $title, $id]);
            $oldRecipe = $old['recipe_id'] !== null ? (int)$old['recipe_id'] : null;
            if ($old['plan_date'] !== $date || $old['slot'] !== $slot || $oldRecipe !== $rid || $old['title'] !== $title) log_event($u, $hid, 'plan_edit', plan_name($rid, $title), ['date' => $date, 'slot' => $slot]);
        } else {
            db()->prepare('INSERT INTO ' . t('meal_plan') . ' (household_id, plan_date, slot, recipe_id, title, created_by, created_at) VALUES (?,?,?,?,?,?,?)')
                ->execute([$hid, $date, $slot, $rid, $title, $u['id'], now_utc()]);
            log_event($u, $hid, 'plan_add', plan_name($rid, $title), ['date' => $date, 'slot' => $slot]);
        }
        send_json(['ok' => true] + state_payload($u));

    case 'plan.delete':
        $u = writer();
        $hid = active_household($u);
        $id = (int)(body()['id'] ?? 0);
        $old = own_row('meal_plan', $id, $hid);
        db()->prepare('DELETE FROM ' . t('meal_plan') . ' WHERE id = ?')->execute([$id]);
        log_event($u, $hid, 'plan_delete', plan_name($old['recipe_id'] !== null ? (int)$old['recipe_id'] : null, (string)$old['title']), ['date' => $old['plan_date'], 'slot' => $old['slot']]);
        send_json(['ok' => true] + state_payload($u));

    // ---------- Activity history & undo ----------

    case 'history.list':
        only('GET');
        $u = require_user();
        $hid = active_household($u);
        try {
            // Pages of 100, from the newest; "before" is the id of the last row already shown
            $before = (int)($_GET['before'] ?? 0);
            $st = db()->prepare('SELECT id, user_id, user_name, action, item_id, item_name, space_id, space_name, quantity, unit, details, undone, created_at FROM ' . t('history') . ' WHERE household_id = ?' . ($before ? ' AND id < ' . $before : '') . ' ORDER BY id DESC LIMIT 100');
            $st->execute([$hid]);
            $rows = array_map(function ($r) {
                return [
                    'id' => (int)$r['id'],
                    'userId' => $r['user_id'] ? (int)$r['user_id'] : null,
                    'userName' => $r['user_name'],
                    'action' => $r['action'],
                    'itemId' => $r['item_id'] ? (int)$r['item_id'] : null,
                    'itemName' => $r['item_name'],
                    'spaceId' => $r['space_id'] ? (int)$r['space_id'] : null,
                    'spaceName' => $r['space_name'],
                    'quantity' => $r['quantity'] !== null ? (float)$r['quantity'] : null,
                    'unit' => $r['unit'],
                    'details' => json_decode((string)$r['details'], true) ?: [],
                    'undone' => (bool)$r['undone'],
                    'undoable' => in_array($r['action'], UNDOABLE_ACTIONS, true),
                    'createdAt' => $r['created_at'],
                ];
            }, $st->fetchAll());
        } catch (Throwable $e) {
            try {
                require_once __DIR__ . '/../lib/schema.php';
                install_schema(db(), (string)config_value('prefix', 'ft_'));
            } catch (Throwable $ignored) {
            }
            $rows = [];
        }
        send_json(['ok' => true, 'history' => $rows]);

    case 'history.undo':
        $u = writer();
        $hid = active_household($u);
        $id = (int)(body()['id'] ?? 0);
        $st = db()->prepare('SELECT * FROM ' . t('history') . ' WHERE id = ? AND household_id = ?');
        $st->execute([$id, $hid]);
        $h = $st->fetch();
        if (!$h) fail('History entry not found.', 404);
        if ($h['undone']) fail('Action already undone.', 400);
        if (!in_array($h['action'], UNDOABLE_ACTIONS, true)) fail('This action cannot be undone.', 422, 'validation');

        $act = $h['action'];
        $details = json_decode((string)$h['details'], true) ?: [];

        db()->beginTransaction();
        if ($act === 'consume_part') {
            // Restore consumed quantity
            $consumedQty = (float)$h['quantity'];
            $itemId = (int)$h['item_id'];
            $item = db()->query('SELECT id, quantity FROM ' . t('items') . ' WHERE id = ' . $itemId . ' AND household_id = ' . $hid)->fetch();
            if ($item) {
                $restored = round((float)$item['quantity'] + $consumedQty, 3);
                db()->prepare('UPDATE ' . t('items') . ' SET quantity = ?, updated_at = ? WHERE id = ?')->execute([$restored, now_utc(), $itemId]);
            } else {
                // Item was deleted later, recreate it from snapshot with the consumed quantity
                $snap = $details['item_snapshot'] ?? [];
                if ($snap) {
                    $snap['quantity'] = $consumedQty;
                    $spId = (int)($snap['space_id'] ?? 0);
                    $spExists = (int)db()->query('SELECT COUNT(*) FROM ' . t('spaces') . ' WHERE id = ' . $spId . ' AND household_id = ' . $hid)->fetchColumn();
                    if (!$spExists) {
                        $firstSp = (int)db()->query('SELECT id FROM ' . t('spaces') . ' WHERE household_id = ' . $hid . ' ORDER BY sort, id LIMIT 1')->fetchColumn();
                        if ($firstSp) $snap['space_id'] = $firstSp;
                    }
                    insert_item($hid, (int)$snap['space_id'], item_fields($snap), $u['id']);
                }
            }
        } elseif ($act === 'consume' || $act === 'delete') {
            // Re-create the item from snapshot
            $snap = $details['item_snapshot'] ?? [];
            if ($snap) {
                $spId = (int)($snap['space_id'] ?? 0);
                $spExists = (int)db()->query('SELECT COUNT(*) FROM ' . t('spaces') . ' WHERE id = ' . $spId . ' AND household_id = ' . $hid)->fetchColumn();
                if (!$spExists) {
                    $firstSp = (int)db()->query('SELECT id FROM ' . t('spaces') . ' WHERE household_id = ' . $hid . ' ORDER BY sort, id LIMIT 1')->fetchColumn();
                    if ($firstSp) $snap['space_id'] = $firstSp;
                }
                insert_item($hid, (int)$snap['space_id'], item_fields($snap), $u['id']);
            }
        } elseif ($act === 'add' || $act === 'move_from_list') {
            // Remove the added item if still present
            $itemId = (int)$h['item_id'];
            if ($itemId) {
                db()->prepare('DELETE FROM ' . t('items') . ' WHERE id = ? AND household_id = ?')->execute([$itemId, $hid]);
            }
        } elseif ($act === 'edit') {
            $prev = $details['prev_snapshot'] ?? [];
            $itemId = (int)$h['item_id'];
            if ($prev && $itemId) {
                $itemExists = (int)db()->query('SELECT COUNT(*) FROM ' . t('items') . ' WHERE id = ' . $itemId . ' AND household_id = ' . $hid)->fetchColumn();
                if ($itemExists) {
                    $f = item_fields($prev);
                    db()->prepare('UPDATE ' . t('items') . ' SET space_id = ?, product_id = ?, name = ?, brand = ?, category_id = ?, icon = ?, quantity = ?, unit = ?, expiry_date = ?, expiry_type = ?, nutriscore = ?, barcode = ?, notes = ?, updated_at = ? WHERE id = ?')
                        ->execute([(int)$prev['space_id'], $f['product_id'], $f['name'], $f['brand'], $f['category_id'], $f['icon'], $f['quantity'], $f['unit'], $f['expiry_date'], $f['expiry_type'], $f['nutriscore'], $f['barcode'], $f['notes'], now_utc(), $itemId]);
                }
            }
        }

        db()->prepare('UPDATE ' . t('history') . ' SET undone = 1 WHERE id = ?')->execute([$id]);
        db()->commit();

        send_json(['ok' => true] + state_payload($u));

    // ---------- Barcode lookup (Open Food Facts) ----------

    case 'barcode.lookup':
        only('GET');
        require_user();
        $code = preg_replace('/\D/', '', (string)($_GET['code'] ?? ''));
        if (strlen($code) < 6 || strlen($code) > 14) fail('Invalid barcode.', 422, 'validation');

        $candidates = [$code];
        if (strlen($code) === 12) $candidates[] = '0' . $code;
        elseif (strlen($code) === 13 && $code[0] === '0') $candidates[] = substr($code, 1);

        $fetchOff = static function (string $c): ?array {
            $fields = 'product_name,product_name_it,product_name_en,generic_name,generic_name_it,abbreviated_product_name,brands,nutriscore_grade,quantity,product_quantity,product_quantity_unit,categories_tags';
            $url = 'https://world.openfoodfacts.org/api/v2/product/' . $c . '.json?fields=' . $fields;
            $ua = 'FoodTrack/1.6 (https://www.saveriomorelli.com)';
            $raw = false;
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 6,
                    CURLOPT_CONNECTTIMEOUT => 4,
                    CURLOPT_USERAGENT => $ua,
                    CURLOPT_HTTPHEADER => ['Accept: application/json'],
                ]);
                $raw = curl_exec($ch);
                curl_close($ch);
            }
            if ($raw === false || $raw === '') {
                $ctx = stream_context_create(['http' => ['timeout' => 6, 'header' => "User-Agent: $ua\r\nAccept: application/json\r\n"]]);
                $raw = @file_get_contents($url, false, $ctx);
            }
            $data = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
            if (!is_array($data) || (int)($data['status'] ?? 0) !== 1 || empty($data['product']) || !is_array($data['product'])) return null;
            return $data['product'];
        };

        $p = null;
        foreach ($candidates as $cand) {
            $p = $fetchOff($cand);
            if ($p !== null) break;
        }

        if ($p !== null) {
            $brand = mb_substr(trim(explode(',', (string)($p['brands'] ?? ''))[0]), 0, 80);
            $nameCandidates = [
                $p['product_name_it'] ?? '',
                $p['product_name'] ?? '',
                $p['product_name_en'] ?? '',
                $p['abbreviated_product_name'] ?? '',
                $p['generic_name_it'] ?? '',
                $p['generic_name'] ?? '',
            ];
            $name = '';
            foreach ($nameCandidates as $nc) {
                $nc = trim((string)$nc);
                if ($nc !== '') {
                    $name = mb_substr($nc, 0, 80);
                    break;
                }
            }
            $genericName = mb_substr(trim((string)(($p['generic_name_it'] ?? '') ?: ($p['generic_name'] ?? ''))), 0, 120);
            if ($name === '' && $brand !== '') $name = $brand;
            if ($brand !== '' && mb_strtolower($name) === 'bueno') $name = $brand . ' Bueno';

            $ns = strtoupper(trim((string)($p['nutriscore_grade'] ?? '')));
            $qty = null;
            $unit = null;
            $qStr = (string)($p['quantity'] ?? '');
            if (preg_match('/(?:^\s*\d+\s*[x×*]\s*)?([\d.,]+)\s*(kg|g|ml|cl|l)\b/i', $qStr, $m)) {
                $qty = (float)str_replace(',', '.', $m[1]);
                $unit = strtolower($m[2]);
            } elseif (!empty($p['product_quantity'])) {
                $qty = (float)$p['product_quantity'];
                $uRaw = strtolower(trim((string)($p['product_quantity_unit'] ?? 'g')));
                $unit = in_array($uRaw, UNITS, true) ? $uRaw : 'g';
            }
            $cats = [];
            if (!empty($p['categories_tags']) && is_array($p['categories_tags'])) {
                foreach (array_slice($p['categories_tags'], 0, 25) as $ct) {
                    if (is_string($ct)) $cats[] = strtolower($ct);
                }
            }
            if ($name !== '') {
                send_json(['ok' => true, 'found' => true, 'product' => [
                    'name' => $name,
                    'genericName' => $genericName,
                    'brand' => $brand,
                    'nutriscore' => in_array($ns, NUTRISCORES, true) ? $ns : null,
                    'quantity' => $qty,
                    'unit' => $unit,
                    'categories' => $cats,
                    'barcode' => $code,
                ]]);
            }
        }

        // Fallback to any previously saved item with the same barcode in the local database
        $inPlaceholders = implode(',', array_fill(0, count($candidates), '?'));
        $stLocal = db()->prepare('SELECT name, brand, nutriscore, quantity, unit, icon, category_id, product_id FROM ' . t('items') . " WHERE barcode IN ($inPlaceholders) AND name != '' ORDER BY id DESC LIMIT 1");
        $stLocal->execute($candidates);
        $loc = $stLocal->fetch();
        if ($loc) {
            send_json(['ok' => true, 'found' => true, 'product' => [
                'name' => (string)$loc['name'],
                'genericName' => '',
                'brand' => (string)($loc['brand'] ?? ''),
                'nutriscore' => in_array($loc['nutriscore'] ?? '', NUTRISCORES, true) ? $loc['nutriscore'] : null,
                'quantity' => $loc['quantity'] !== null ? (float)$loc['quantity'] : null,
                'unit' => $loc['unit'] ?: null,
                'icon' => $loc['icon'] ?: null,
                'categoryId' => $loc['category_id'] !== null ? (int)$loc['category_id'] : null,
                'productId' => $loc['product_id'] !== null ? (int)$loc['product_id'] : null,
                'categories' => [],
                'barcode' => $code,
            ]]);
        }

        send_json(['ok' => true, 'found' => false]);

    // =================== Admin portal ===================

    case 'admin.overview':
        only('GET');
        require_user(ROLE_ADMIN);
        $c = function (string $sql) {
            return (int)db()->query($sql)->fetchColumn();
        };
        send_json(['ok' => true, 'stats' => [
            'users' => $c('SELECT COUNT(*) FROM ' . t('users')),
            'usersActive30' => $c('SELECT COUNT(*) FROM ' . t('users') . ' WHERE last_seen_at > (UTC_TIMESTAMP() - INTERVAL 30 DAY) OR last_login_at > (UTC_TIMESTAMP() - INTERVAL 30 DAY)'),
            'usersNew7' => $c('SELECT COUNT(*) FROM ' . t('users') . ' WHERE created_at > (UTC_TIMESTAMP() - INTERVAL 7 DAY)'),
            'admins' => $c('SELECT COUNT(*) FROM ' . t('users') . ' WHERE role >= ' . ROLE_ADMIN),
            'editors' => $c('SELECT COUNT(*) FROM ' . t('users') . ' WHERE role = ' . ROLE_EDITOR),
            'households' => $c('SELECT COUNT(*) FROM ' . t('households')),
            'sharedHouseholds' => $c('SELECT COUNT(*) FROM (SELECT household_id FROM ' . t('household_members') . ' GROUP BY household_id HAVING COUNT(*) > 1) x'),
            'items' => $c('SELECT COUNT(*) FROM ' . t('items')),
            'expired' => $c('SELECT COUNT(*) FROM ' . t('items') . ' WHERE expiry_date < UTC_DATE()'),
            'lists' => $c('SELECT COUNT(*) FROM ' . t('lists')),
            'recipes' => $c('SELECT COUNT(*) FROM ' . t('recipes')),
            'recipesPublished' => count(array_filter(db()->query('SELECT * FROM ' . t('recipes'))->fetchAll(), function ($r) {
                foreach (LANGS as $lg) if (recipe_status($r, $lg) === 'published') return true;
                return false;
            })),
            'products' => $c('SELECT COUNT(*) FROM ' . t('products')),
            'favorites' => $c('SELECT COUNT(*) FROM ' . t('favorites')),
        ], 'topRecipes' => db()->query('SELECT r.name, COUNT(*) AS n FROM ' . t('favorites') . ' f JOIN ' . t('recipes') . ' r ON r.id = f.recipe_id GROUP BY r.id, r.name ORDER BY n DESC LIMIT 5')->fetchAll(),
            'topProducts' => db()->query('SELECT name, COUNT(*) AS n FROM ' . t('items') . ' GROUP BY name ORDER BY n DESC LIMIT 8')->fetchAll(),
        ]);

    case 'admin.users':
        only('GET');
        $me = require_user(ROLE_ADMIN);
        $rows = db()->query('SELECT u.id, u.name, u.email, u.role, u.status, u.must_change_password, u.created_at, u.last_login_at, u.last_seen_at, (u.google_sub IS NOT NULL) AS google, (u.password_hash = \'\') AS no_password,
            (SELECT COUNT(*) FROM ' . t('household_members') . ' m WHERE m.user_id = u.id) AS households
            FROM ' . t('users') . ' u ORDER BY u.role DESC, u.created_at DESC')->fetchAll();
        send_json(['ok' => true, 'users' => array_map(function ($r) use ($me) {
            return [
                'id' => (int)$r['id'], 'name' => $r['name'], 'email' => $r['email'], 'role' => (int)$r['role'],
                'roleLabel' => tr(ROLE_LABELS[(int)$r['role']] ?? 'User'), 'status' => $r['status'],
                'mustChangePassword' => (bool)$r['must_change_password'], 'createdAt' => $r['created_at'],
                'lastLoginAt' => $r['last_login_at'], 'lastSeenAt' => $r['last_seen_at'], 'households' => (int)$r['households'],
                'google' => (bool)$r['google'], 'hasPassword' => !$r['no_password'],
                'canManage' => can_manage($me, $r), 'isMe' => (int)$r['id'] === $me['id'],
            ];
        }, $rows)]);

    case 'admin.user.update':
        $me = writer(ROLE_ADMIN);
        $b = body();
        $target = user_row((int)($b['id'] ?? 0));
        if (!can_manage($me, $target)) fail('You cannot edit this account.', 403, 'forbidden');
        $name = s($b, 'name', 80, true, 'Name');
        $email = mb_strtolower(s($b, 'email', 190, true, 'Email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.', 422, 'validation');
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE email = ? AND id <> ?');
        $st->execute([$email, $target['id']]);
        if ($st->fetchColumn()) fail('This email is already used by another account.', 409, 'email_taken');
        $status = ($b['status'] ?? 'active') === 'disabled' ? 'disabled' : 'active';
        $role = (int)($b['role'] ?? $target['role']);
        if (!in_array($role, [ROLE_USER, ROLE_EDITOR, ROLE_ADMIN], true)) $role = (int)$target['role'];
        // Administrators can appoint content moderators; only the master admin appoints or removes administrators
        if ($role !== (int)$target['role'] && ($role >= ROLE_ADMIN || (int)$target['role'] >= ROLE_ADMIN) && $me['role'] < ROLE_MASTER) fail('Only the master admin can appoint or remove administrators.', 403, 'forbidden');
        db()->prepare('UPDATE ' . t('users') . ' SET name = ?, email = ?, status = ?, role = ? WHERE id = ?')->execute([$name, $email, $status, $role, $target['id']]);
        if ($status === 'disabled') db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$target['id']]);
        send_json(['ok' => true]);

    case 'admin.user.reset_password':
        $me = writer(ROLE_ADMIN);
        $target = user_row((int)(body()['id'] ?? 0));
        if (!can_manage($me, $target)) fail('You cannot edit this account.', 403, 'forbidden');
        $pw = temp_password();
        db()->prepare('UPDATE ' . t('users') . ' SET password_hash = ?, must_change_password = 1 WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $target['id']]);
        db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$target['id']]);
        send_json(['ok' => true, 'password' => $pw]);

    case 'admin.user.delete':
        $me = writer(ROLE_ADMIN);
        $target = user_row((int)(body()['id'] ?? 0));
        if (!can_manage($me, $target)) fail('You cannot delete this account.', 403, 'forbidden');
        log_account_removal($target);
        delete_user_account((int)$target['id']);
        send_json(['ok' => true]);

    case 'admin.user.create':
        $me = writer(ROLE_ADMIN);
        $b = body();
        $name = s($b, 'name', 80, true, 'Name');
        $email = mb_strtolower(s($b, 'email', 190, true, 'Email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.', 422, 'validation');
        $st = db()->prepare('SELECT 1 FROM ' . t('users') . ' WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetchColumn()) fail('An account with this email already exists.', 409, 'email_taken');
        $role = (int)($b['role'] ?? ROLE_USER);
        if (!in_array($role, [ROLE_USER, ROLE_EDITOR, ROLE_ADMIN], true)) $role = ROLE_USER;
        if ($role === ROLE_ADMIN && $me['role'] < ROLE_MASTER) fail('Only the master admin can create administrators.', 403, 'forbidden');
        $pw = temp_password();
        db()->prepare('INSERT INTO ' . t('users') . " (name, email, password_hash, role, status, must_change_password, created_at) VALUES (?,?,?,?, 'active', 1, ?)")
            ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT), $role, now_utc()]);
        $uid = (int)db()->lastInsertId();
        create_first_household($uid, $name);
        mail_account_created(user_row($uid), 'admin');
        send_json(['ok' => true, 'password' => $pw]);

    case 'admin.user.transfer_master':
        $me = writer(ROLE_MASTER);
        $b = body();
        $target = user_row((int)($b['id'] ?? 0));
        if ((int)$target['id'] === $me['id']) fail('You are already the master admin.', 422, 'validation');
        if ($target['status'] !== 'active') fail('The new master admin must have an active account.', 422, 'validation');
        $row = user_row($me['id']);
        if ($row['password_hash'] === '') fail('To hand over the role, first set a password for your account (Profile → Set a password).', 422, 'no_password');
        if (!password_verify((string)($b['password'] ?? ''), $row['password_hash'])) fail('Wrong password.', 422, 'bad_password');
        db()->beginTransaction();
        db()->prepare('UPDATE ' . t('users') . ' SET role = ? WHERE id = ?')->execute([ROLE_ADMIN, $me['id']]);
        db()->prepare('UPDATE ' . t('users') . ' SET role = ? WHERE id = ?')->execute([ROLE_MASTER, $target['id']]);
        db()->commit();
        normalize_master_admin();
        send_json(['ok' => true]);

    case 'admin.households':
        only('GET');
        require_user(ROLE_ADMIN);
        $rows = db()->query('SELECT h.id, h.name, h.created_at, o.name AS owner_name, o.email AS owner_email,
            (SELECT COUNT(*) FROM ' . t('household_members') . ' m WHERE m.household_id = h.id) AS members,
            (SELECT COUNT(*) FROM ' . t('spaces') . ' s WHERE s.household_id = h.id) AS spaces,
            (SELECT COUNT(*) FROM ' . t('items') . ' i WHERE i.household_id = h.id) AS items,
            (SELECT COUNT(*) FROM ' . t('lists') . ' l WHERE l.household_id = h.id) AS lists
            FROM ' . t('households') . ' h LEFT JOIN ' . t('users') . ' o ON o.id = h.owner_id ORDER BY h.created_at DESC LIMIT 1000')->fetchAll();
        send_json(['ok' => true, 'households' => array_map(function ($r) {
            return ['id' => (int)$r['id'], 'name' => $r['name'], 'createdAt' => $r['created_at'], 'owner' => $r['owner_name'], 'ownerEmail' => $r['owner_email'],
                'members' => (int)$r['members'], 'spaces' => (int)$r['spaces'], 'items' => (int)$r['items'], 'lists' => (int)$r['lists']];
        }, $rows)]);

    case 'admin.catalog':
        only('GET');
        require_user(ROLE_EDITOR);
        $usage = [];
        foreach (db()->query('SELECT product_id, COUNT(*) AS n FROM ' . t('recipe_ingredients') . ' WHERE product_id IS NOT NULL GROUP BY product_id') as $r) $usage[(int)$r['product_id']] = (int)$r['n'];
        $icons = sprite_icons('products');
        send_json(['ok' => true, 'usage' => $usage, 'icons' => $icons, 'units' => UNITS, 'langs' => LANGS] + load_catalog(true));

    case 'admin.product.save':
        $me = writer(ROLE_EDITOR);
        $b = body();
        $name = s($b, 'name', 80, true, 'Name');
        $icon = icon_name($b['icon'] ?? '', 'products', 'generic');
        $cat = valid_category(int_or_null($b['categoryId'] ?? null));
        $unit = unit_or_default($b['unit'] ?? 'pz');
        $env = environment_or_null($b['environment'] ?? null);
        $tip = s($b, 'expiryTip', 2000, false, 'Expiry information');
        $active = !empty($b['active']) ? 1 : 0;
        $i18n = i18n_input($b['i18n'] ?? null, ['name', 'expiry_tip']);
        $id = (int)($b['id'] ?? 0);
        $st = db()->prepare('SELECT id FROM ' . t('products') . ' WHERE name = ? AND id <> ?');
        $st->execute([$name, $id]);
        if ($st->fetchColumn()) fail('A product with this name already exists.', 409, 'duplicate');
        if ($id) {
            product_row($id) ?: fail('Product not found.', 404, 'not_found');
            db()->prepare('UPDATE ' . t('products') . ' SET name = ?, icon = ?, category_id = ?, default_unit = ?, environment = ?, expiry_tip = ?, i18n = ?, active = ?, updated_at = ? WHERE id = ?')
                ->execute([$name, $icon, $cat, $unit, $env, $tip !== '' ? $tip : null, $i18n, $active, now_utc(), $id]);
        } else {
            db()->prepare('INSERT INTO ' . t('products') . ' (name, icon, category_id, default_unit, environment, expiry_tip, i18n, active, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)')
                ->execute([$name, $icon, $cat, $unit, $env, $tip !== '' ? $tip : null, $i18n, $active, now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
        }
        send_json(['ok' => true, 'id' => $id]);

    case 'admin.product.delete':
        writer(ROLE_ADMIN);
        db()->prepare('DELETE FROM ' . t('products') . ' WHERE id = ?')->execute([(int)(body()['id'] ?? 0)]);
        send_json(['ok' => true]);

    case 'admin.category.save':
        writer(ROLE_ADMIN);
        $b = body();
        $name = s($b, 'name', 60, true, 'Name');
        $tip = s($b, 'expiryTip', 2000, false, 'Expiry information');
        $sort = (int)($b['sort'] ?? 0);
        $i18n = i18n_input($b['i18n'] ?? null, ['name', 'expiry_tip']);
        $id = (int)($b['id'] ?? 0);
        $st = db()->prepare('SELECT id FROM ' . t('categories') . ' WHERE name = ? AND id <> ?');
        $st->execute([$name, $id]);
        if ($st->fetchColumn()) fail('A category with this name already exists.', 409, 'duplicate');
        if ($id) {
            db()->prepare('UPDATE ' . t('categories') . ' SET name = ?, expiry_tip = ?, i18n = ?, sort = ? WHERE id = ?')->execute([$name, $tip !== '' ? $tip : null, $i18n, $sort, $id]);
        } else {
            db()->prepare('INSERT INTO ' . t('categories') . ' (name, expiry_tip, i18n, sort) VALUES (?,?,?,?)')->execute([$name, $tip !== '' ? $tip : null, $i18n, $sort]);
            $id = (int)db()->lastInsertId();
        }
        send_json(['ok' => true, 'id' => $id]);

    case 'admin.category.delete':
        writer(ROLE_ADMIN);
        db()->prepare('DELETE FROM ' . t('categories') . ' WHERE id = ?')->execute([(int)(body()['id'] ?? 0)]);
        send_json(['ok' => true]);

    case 'admin.recipes':
        only('GET');
        require_user(ROLE_EDITOR);
        $favs = [];
        foreach (db()->query('SELECT recipe_id, COUNT(*) AS n FROM ' . t('favorites') . ' GROUP BY recipe_id') as $r) $favs[(int)$r['recipe_id']] = (int)$r['n'];
        send_json(['ok' => true, 'recipes' => load_recipes(false, true), 'favorites' => $favs, 'units' => UNITS, 'langs' => LANGS] + load_catalog(true));

    case 'admin.recipe.image':
        only('POST');
        $me = require_user(ROLE_EDITOR);
        require_csrf($me);
        if (empty($_FILES['image'])) fail('No image received.', 422, 'upload');
        send_json(['ok' => true, 'image' => save_recipe_image($_FILES['image'])]);

    case 'admin.recipe.save':
        $me = writer(ROLE_EDITOR);
        $b = body();
        $name = s($b, 'name', 120, true, 'Name');
        $desc = s($b, 'description', 500, false, 'Description');
        $image = s($b, 'image', 160, false, 'Image');
        if ($image !== '' && !preg_match('#^(uploads/recipes/[a-f0-9]{20}\.(jpg|png|webp)|assets/img/recipes/[a-z0-9_]+\.jpg)$#', $image)) $image = '';
        $prep = int_or_null($b['prepMinutes'] ?? null);
        $cook = int_or_null($b['cookMinutes'] ?? null);
        $cookNotes = s($b, 'cookNotes', 255, false, 'Cooking notes');
        $servings = max(1, min(50, (int)($b['servings'] ?? 2)));
        $diff = in_array($b['difficulty'] ?? '', DIFFICULTIES, true) ? $b['difficulty'] : 'facile';
        // Countries of origin: one or more ISO 3166 codes (none = not defined)
        $origin = is_array($b['origin'] ?? null) ? $b['origin'] : explode(',', (string)($b['origin'] ?? ''));
        $origin = array_values(array_unique(array_filter(array_map(function ($c) {
            return strtoupper(trim((string)$c));
        }, $origin), function ($c) {
            return (bool)preg_match('/^[A-Z]{2}(-(ENG|SCT|WLS|NIR))?$/', $c);
        })));
        $origin = implode(',', array_slice($origin, 0, 20));
        $pub = !empty($b['published']) ? 1 : 0;
        $i18n = i18n_input($b['i18n'] ?? null, ['name', 'description', 'cook_notes', 'steps', 'status']);
        // A translation needs at least a name: without it the language counts as "not translated"
        if ($i18n !== null) {
            $tmp = json_decode($i18n, true);
            foreach ($tmp as $lg => $fields) if (empty($fields['name'])) unset($tmp[$lg]);
            $i18n = $tmp ? json_encode($tmp, JSON_UNESCAPED_UNICODE) : null;
        }
        $ings = is_array($b['ingredients'] ?? null) ? array_slice($b['ingredients'], 0, 60) : [];
        $steps = is_array($b['steps'] ?? null) ? array_slice($b['steps'], 0, 60) : [];
        $id = (int)($b['id'] ?? 0);
        $oldImage = '';
        db()->beginTransaction();
        if ($id) {
            $st = db()->prepare('SELECT image FROM ' . t('recipes') . ' WHERE id = ?');
            $st->execute([$id]);
            $old = $st->fetchColumn();
            if ($old === false) {
                db()->rollBack();
                fail('Recipe not found.', 404, 'not_found');
            }
            $oldImage = (string)$old;
            db()->prepare('UPDATE ' . t('recipes') . ' SET name = ?, description = ?, image = ?, prep_minutes = ?, cook_minutes = ?, cook_notes = ?, servings = ?, difficulty = ?, origin = ?, published = ?, i18n = ?, updated_at = ? WHERE id = ?')
                ->execute([$name, $desc, $image, $prep, $cook, $cookNotes, $servings, $diff, $origin, $pub, $i18n, now_utc(), $id]);
            db()->prepare('DELETE FROM ' . t('recipe_ingredients') . ' WHERE recipe_id = ?')->execute([$id]);
            db()->prepare('DELETE FROM ' . t('recipe_steps') . ' WHERE recipe_id = ?')->execute([$id]);
        } else {
            db()->prepare('INSERT INTO ' . t('recipes') . ' (name, description, image, prep_minutes, cook_minutes, cook_notes, servings, difficulty, origin, published, i18n, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$name, $desc, $image, $prep, $cook, $cookNotes, $servings, $diff, $origin, $pub, $i18n, $me['id'], now_utc(), now_utc()]);
            $id = (int)db()->lastInsertId();
        }
        $insI = db()->prepare('INSERT INTO ' . t('recipe_ingredients') . ' (recipe_id, product_id, name, quantity, unit, notes, i18n, sort) VALUES (?,?,?,?,?,?,?,?)');
        foreach (array_values($ings) as $i => $ing) {
            if (!is_array($ing)) continue;
            $product = product_row(int_or_null($ing['productId'] ?? null));
            $iname = mb_substr(trim((string)($ing['name'] ?? '')), 0, 80) ?: ($product['name'] ?? '');
            if ($iname === '') continue;
            $insI->execute([$id, $product ? (int)$product['id'] : null, $iname, num_or_null($ing['quantity'] ?? null), unit_or_default($ing['unit'] ?? ''), mb_substr(trim((string)($ing['notes'] ?? '')), 0, 255), i18n_input($ing['i18n'] ?? null, ['name', 'notes'], 255), $i]);
        }
        $insS = db()->prepare('INSERT INTO ' . t('recipe_steps') . ' (recipe_id, sort, text) VALUES (?,?,?)');
        foreach (array_values($steps) as $i => $text) {
            $text = mb_substr(trim((string)$text), 0, 1000);
            if ($text !== '') $insS->execute([$id, $i, $text]);
        }
        db()->commit();
        if ($oldImage !== '' && $oldImage !== $image) delete_upload($oldImage);
        send_json(['ok' => true, 'id' => $id]);

    case 'admin.recipe.delete':
        writer(ROLE_ADMIN);
        $id = (int)(body()['id'] ?? 0);
        $st = db()->prepare('SELECT image FROM ' . t('recipes') . ' WHERE id = ?');
        $st->execute([$id]);
        $img = $st->fetchColumn();
        db()->prepare('DELETE FROM ' . t('recipes') . ' WHERE id = ?')->execute([$id]);
        if ($img) delete_upload((string)$img);
        send_json(['ok' => true]);

    default:
        fail('Unknown action.', 404, 'unknown_route');
}
