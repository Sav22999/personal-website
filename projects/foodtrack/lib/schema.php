<?php
/**
 * FoodTrack database structure. Every table has the configured prefix (default "ft_").
 * The same statements are exported to install/schema.sql for a manual import in phpMyAdmin
 * (see tools/build-sql.php).
 */

declare(strict_types=1);

function schema_statements(string $p = 'ft_'): array
{
    $opts = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    return [
        "CREATE TABLE IF NOT EXISTS `{$p}meta` (
  `k` VARCHAR(64) NOT NULL,
  `v` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`k`)
) $opts",

        "CREATE TABLE IF NOT EXISTS `{$p}users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'empty = account created with Google, no password',
  `google_sub` VARCHAR(64) NULL COMMENT 'Google account id (Sign in with Google)',
  `role` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 utente, 9 amministratore, 10 master admin (uno solo)',
  `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
  `must_change_password` TINYINT(1) NOT NULL DEFAULT 0,
  `notify_off` VARCHAR(200) NULL COMMENT 'notification categories turned off, as JSON',
  `active_household_id` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  `last_login_at` DATETIME NULL,
  `last_seen_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  UNIQUE KEY `uq_google` (`google_sub`),
  KEY `idx_role` (`role`)
) $opts",

        // Devices to wake up with Web Push (see lib/push.php)
        "CREATE TABLE IF NOT EXISTS `{$p}push_subscriptions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `endpoint` VARCHAR(700) NOT NULL,
  `endpoint_hash` CHAR(64) NOT NULL,
  `last_push_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_endpoint` (`endpoint_hash`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_{$p}push_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
) $opts",

        "CREATE TABLE IF NOT EXISTS `{$p}auth_tokens` (
  `token_hash` CHAR(64) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `csrf_token` CHAR(48) NOT NULL,
  `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
  `ip` VARCHAR(45) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL,
  `last_seen_at` DATETIME NOT NULL,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`token_hash`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_{$p}tok_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
) $opts",

        "CREATE TABLE IF NOT EXISTS `{$p}attempts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kind` VARCHAR(16) NOT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_kind_ip` (`kind`, `ip`, `created_at`)
) $opts",

// A household is the shared workspace: spaces, products, shopping lists and meal plan belong to it.
        "CREATE TABLE IF NOT EXISTS `{$p}households` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `invite_code` CHAR(8) NOT NULL,
  `owner_id` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_invite` (`invite_code`),
  CONSTRAINT `fk_{$p}hh_owner` FOREIGN KEY (`owner_id`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
) $opts",

        "CREATE TABLE IF NOT EXISTS `{$p}household_members` (
  `household_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `role` ENUM('owner','member') NOT NULL DEFAULT 'member',
  `joined_at` DATETIME NOT NULL,
  PRIMARY KEY (`household_id`, `user_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_{$p}hm_hh` FOREIGN KEY (`household_id`) REFERENCES `{$p}households` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}hm_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE
) $opts",

// Product categories (managed by the admins) with a generic tip about expiry
        "CREATE TABLE IF NOT EXISTS `{$p}categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL,
  `expiry_tip` TEXT NULL,
  `i18n` TEXT NULL COMMENT 'translations as JSON, keyed by language code',
  `sort` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`)
) $opts",

// Product catalog (managed by the admins): used for suggestions, icons and recipe ingredients
        "CREATE TABLE IF NOT EXISTS `{$p}products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `icon` VARCHAR(40) NOT NULL DEFAULT 'vegetable',
  `category_id` INT UNSIGNED NULL,
  `default_unit` VARCHAR(16) NOT NULL DEFAULT 'pz',
  `environment` VARCHAR(10) NULL COMMENT 'cold, cool, ambient or warm; NULL = not set',
  `expiry_tip` TEXT NULL,
  `i18n` TEXT NULL COMMENT 'translations as JSON, keyed by language code',
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`),
  CONSTRAINT `fk_{$p}prod_cat` FOREIGN KEY (`category_id`) REFERENCES `{$p}categories` (`id`) ON DELETE SET NULL
) $opts",

// Spaces of the house (fridge, pantry, freezer...)
        "CREATE TABLE IF NOT EXISTS `{$p}spaces` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `household_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(60) NOT NULL,
  `icon` VARCHAR(40) NOT NULL DEFAULT 'fridge',
  `environment` VARCHAR(10) NOT NULL DEFAULT 'ambient' COMMENT 'cold, cool, ambient or warm',
  `notes` VARCHAR(500) NOT NULL DEFAULT '',
  `sort` INT NOT NULL DEFAULT 0,
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hh` (`household_id`),
  CONSTRAINT `fk_{$p}sp_hh` FOREIGN KEY (`household_id`) REFERENCES `{$p}households` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}sp_user` FOREIGN KEY (`created_by`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
) $opts",

// Food stored in a space
        "CREATE TABLE IF NOT EXISTS `{$p}items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `household_id` INT UNSIGNED NOT NULL,
  `space_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `name` VARCHAR(80) NOT NULL,
  `brand` VARCHAR(80) NOT NULL DEFAULT '',
  `category_id` INT UNSIGNED NULL,
  `icon` VARCHAR(40) NOT NULL DEFAULT 'vegetable',
  `quantity` DECIMAL(10,3) NULL,
  `unit` VARCHAR(16) NOT NULL DEFAULT 'pz',
  `expiry_date` DATE NULL,
  `expiry_type` ENUM('use_by','best_before') NOT NULL DEFAULT 'use_by' COMMENT 'use_by = da consumarsi entro, best_before = preferibilmente entro',
  `nutriscore` CHAR(1) NULL,
  `barcode` VARCHAR(32) NOT NULL DEFAULT '',
  `notes` VARCHAR(500) NOT NULL DEFAULT '',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hh_exp` (`household_id`, `expiry_date`),
  KEY `idx_space` (`space_id`),
  CONSTRAINT `fk_{$p}it_hh` FOREIGN KEY (`household_id`) REFERENCES `{$p}households` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}it_space` FOREIGN KEY (`space_id`) REFERENCES `{$p}spaces` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}it_prod` FOREIGN KEY (`product_id`) REFERENCES `{$p}products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_{$p}it_cat` FOREIGN KEY (`category_id`) REFERENCES `{$p}categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_{$p}it_user` FOREIGN KEY (`created_by`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
) $opts",

// Shopping lists
        "CREATE TABLE IF NOT EXISTS `{$p}lists` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `household_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(60) NOT NULL,
  `icon` VARCHAR(40) NOT NULL DEFAULT 'cart',
  `notes` VARCHAR(500) NOT NULL DEFAULT '',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hh` (`household_id`),
  CONSTRAINT `fk_{$p}li_hh` FOREIGN KEY (`household_id`) REFERENCES `{$p}households` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}li_user` FOREIGN KEY (`created_by`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
) $opts",

        "CREATE TABLE IF NOT EXISTS `{$p}list_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `list_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `name` VARCHAR(80) NOT NULL,
  `icon` VARCHAR(40) NOT NULL DEFAULT 'vegetable',
  `quantity` DECIMAL(10,3) NULL,
  `unit` VARCHAR(16) NOT NULL DEFAULT 'pz',
  `notes` VARCHAR(500) NOT NULL DEFAULT '',
  `environment` VARCHAR(10) NULL COMMENT 'overrides the one of the product; NULL = use the product one',
  `in_cart` TINYINT(1) NOT NULL DEFAULT 0,
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_list` (`list_id`),
  CONSTRAINT `fk_{$p}lit_list` FOREIGN KEY (`list_id`) REFERENCES `{$p}lists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}lit_prod` FOREIGN KEY (`product_id`) REFERENCES `{$p}products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_{$p}lit_user` FOREIGN KEY (`created_by`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
) $opts",

// Recipes (managed by the admins)
        "CREATE TABLE IF NOT EXISTS `{$p}recipes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `description` VARCHAR(500) NOT NULL DEFAULT '',
  `image` VARCHAR(160) NOT NULL DEFAULT '',
  `prep_minutes` SMALLINT UNSIGNED NULL,
  `cook_minutes` SMALLINT UNSIGNED NULL,
  `cook_notes` VARCHAR(255) NOT NULL DEFAULT '',
  `servings` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `difficulty` ENUM('facile','media','difficile') NOT NULL DEFAULT 'facile',
  `origin` VARCHAR(120) NOT NULL DEFAULT '' COMMENT 'countries of origin: comma-separated ISO 3166 codes, empty = not defined',
  `published` TINYINT(1) NOT NULL DEFAULT 1,
  `i18n` MEDIUMTEXT NULL COMMENT 'translations as JSON, keyed by language code',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pub` (`published`),
  CONSTRAINT `fk_{$p}rec_user` FOREIGN KEY (`created_by`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
) $opts",

        "CREATE TABLE IF NOT EXISTS `{$p}recipe_ingredients` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `name` VARCHAR(80) NOT NULL,
  `quantity` DECIMAL(10,3) NULL,
  `unit` VARCHAR(16) NOT NULL DEFAULT 'pz',
  `notes` VARCHAR(255) NOT NULL DEFAULT '',
  `i18n` TEXT NULL COMMENT 'translations as JSON, keyed by language code',
  `sort` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_recipe` (`recipe_id`),
  CONSTRAINT `fk_{$p}ri_rec` FOREIGN KEY (`recipe_id`) REFERENCES `{$p}recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}ri_prod` FOREIGN KEY (`product_id`) REFERENCES `{$p}products` (`id`) ON DELETE SET NULL
) $opts",

        "CREATE TABLE IF NOT EXISTS `{$p}recipe_steps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` INT UNSIGNED NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `text` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_recipe` (`recipe_id`),
  CONSTRAINT `fk_{$p}rs_rec` FOREIGN KEY (`recipe_id`) REFERENCES `{$p}recipes` (`id`) ON DELETE CASCADE
) $opts",

        "CREATE TABLE IF NOT EXISTS `{$p}favorites` (
  `user_id` INT UNSIGNED NOT NULL,
  `recipe_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`user_id`, `recipe_id`),
  CONSTRAINT `fk_{$p}fav_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}fav_rec` FOREIGN KEY (`recipe_id`) REFERENCES `{$p}recipes` (`id`) ON DELETE CASCADE
) $opts",

// Weekly meal plan of a household
        "CREATE TABLE IF NOT EXISTS `{$p}meal_plan` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `household_id` INT UNSIGNED NOT NULL,
  `plan_date` DATE NOT NULL,
  `slot` ENUM('colazione','pranzo','merenda','cena') NOT NULL,
  `recipe_id` INT UNSIGNED NULL,
  `title` VARCHAR(120) NOT NULL DEFAULT '',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hh_date` (`household_id`, `plan_date`),
  CONSTRAINT `fk_{$p}mp_hh` FOREIGN KEY (`household_id`) REFERENCES `{$p}households` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}mp_rec` FOREIGN KEY (`recipe_id`) REFERENCES `{$p}recipes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_{$p}mp_user` FOREIGN KEY (`created_by`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
) $opts",

// Household activity history (item consume, add, delete, edit) with undo support
        "CREATE TABLE IF NOT EXISTS `{$p}history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `household_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `user_name` VARCHAR(80) NOT NULL DEFAULT '',
  `action` VARCHAR(32) NOT NULL,
  `item_id` INT UNSIGNED NULL,
  `item_name` VARCHAR(80) NOT NULL,
  `space_id` INT UNSIGNED NULL,
  `space_name` VARCHAR(60) NOT NULL DEFAULT '',
  `quantity` DECIMAL(10,3) NULL,
  `unit` VARCHAR(16) NOT NULL DEFAULT 'pz',
  `details` MEDIUMTEXT NULL,
  `undone` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hh_created` (`household_id`, `created_at`),
  CONSTRAINT `fk_{$p}hist_hh` FOREIGN KEY (`household_id`) REFERENCES `{$p}households` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_{$p}hist_user` FOREIGN KEY (`user_id`) REFERENCES `{$p}users` (`id`) ON DELETE SET NULL
) $opts",
    ];
}

/**
 * Upgrades a database created by an older version. Every step is idempotent.
 *  v2: Sign in with Google (users.google_sub, password optional)
 *  v3: new SVG icon set (more specific icons for some catalog products)
 *  v4: multilingual content (i18n JSON column on categories, products, recipes, ingredients)
 *  v5: countries of origin of the recipes (one or more)
 *  v6: publication status per language (existing translations become published)
 *  v7: second batch of content (new catalog products and recipes from many countries)
 *  v8: third batch of content (more products and recipes)
 *  v9: cover images of the initial recipes of batches 2 and 3 (only where the recipe still has no image)
 *  v10: household activity history table (`history`)
 *  v11: remaining cover images for initial recipes (only where the recipe still has no image)
 *  v12: storage environment of products, list items and spaces (existing rows get a value from the seed and from the space icon)
 *  v13: fourth batch of content (many more catalog products, more specific icons; a product icon changed by an admin stays)
 *  v14: notification preferences of the users (the table of the push subscriptions is created with the others)
 *  v15: fourth batch of recipes (everyday basics, without a cover photo for now)
 *  v16: fifth and sixth batch of recipes (regional classics, cakes and European traditions, without a cover photo for now) and the products they need
 *  v17: corrected countries of origin (a recipe whose origin was changed by an admin stays as it is)
 *  v18: apple pie added; mashed potatoes and carrot cake moved to England (also from the values of the first v17)
 *  v19: carrot cake from both England and Switzerland
 */
function migrate_schema(PDO $pdo, string $p, int $from): void
{
    if ($from < 2) {
        $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}users`")->fetchAll(), 'Field');
        if (!in_array('google_sub', $cols, true)) {
            $pdo->exec("ALTER TABLE `{$p}users` ADD COLUMN `google_sub` VARCHAR(64) NULL AFTER `password_hash`, ADD UNIQUE KEY `uq_google` (`google_sub`)");
        }
        $pdo->exec("ALTER TABLE `{$p}users` MODIFY `password_hash` VARCHAR(255) NOT NULL DEFAULT ''");
    }
    if ($from < 3) {
        require_once __DIR__ . '/seed.php';
        $st = $pdo->prepare("UPDATE `{$p}products` SET icon = ? WHERE name = ? AND icon <> ?");
        foreach (seed_products() as $row) $st->execute([$row[1], $row[0], $row[1]]);
    }
    if ($from < 4) {
        foreach (['categories' => 'TEXT', 'products' => 'TEXT', 'recipes' => 'MEDIUMTEXT', 'recipe_ingredients' => 'TEXT'] as $table => $type) {
            $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}{$table}`")->fetchAll(), 'Field');
            if (!in_array('i18n', $cols, true)) $pdo->exec("ALTER TABLE `{$p}{$table}` ADD COLUMN `i18n` {$type} NULL");
        }
        require_once __DIR__ . '/seed-en.php';
        seed_translations($pdo, $p);
    }
    if ($from < 5) {
        $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}recipes`")->fetchAll(), 'Field');
        if (!in_array('origin', $cols, true)) $pdo->exec("ALTER TABLE `{$p}recipes` ADD COLUMN `origin` VARCHAR(120) NOT NULL DEFAULT '' AFTER `difficulty`");
        require_once __DIR__ . '/seed.php';
        $st = $pdo->prepare("UPDATE `{$p}recipes` SET origin = ? WHERE name = ? AND origin = ''");
        foreach (seed_recipes() as $r) $st->execute([$r['origin'], $r['name']]);
    }
    if ($from < 6) {
        $up = $pdo->prepare("UPDATE `{$p}recipes` SET i18n = ? WHERE id = ?");
        foreach ($pdo->query("SELECT id, i18n FROM `{$p}recipes` WHERE i18n IS NOT NULL")->fetchAll() as $r) {
            $d = json_decode((string)$r['i18n'], true) ?: [];
            $changed = false;
            foreach ($d as $lg => $tr) if (is_array($tr) && !empty($tr['name']) && empty($tr['status'])) {
                $d[$lg]['status'] = 'published';
                $changed = true;
            }
            if ($changed) $up->execute([json_encode($d, JSON_UNESCAPED_UNICODE), $r['id']]);
        }
    }
    if ($from < 8) {
        require_once __DIR__ . '/seed.php';
        $admin = $pdo->query("SELECT id FROM `{$p}users` WHERE role >= 10 ORDER BY id LIMIT 1")->fetchColumn();
        seed_database($pdo, $p, $admin ? (int)$admin : null);
    }
    if ($from < 9) {
        require_once __DIR__ . '/seed.php';
        $now = gmdate('Y-m-d H:i:s');
        $st = $pdo->prepare("UPDATE `{$p}recipes` SET image = ?, updated_at = ? WHERE name = ? AND image = ''");
        foreach (seed_recipes() as $r) if ($r['image'] !== '') $st->execute([$r['image'], $now, $r['name']]);
    }
    if ($from < 10) {
        $opts = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$p}history` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `household_id` INT UNSIGNED NOT NULL,
          `user_id` INT UNSIGNED NULL,
          `user_name` VARCHAR(80) NOT NULL DEFAULT '',
          `action` VARCHAR(32) NOT NULL,
          `item_id` INT UNSIGNED NULL,
          `item_name` VARCHAR(80) NOT NULL,
          `space_id` INT UNSIGNED NULL,
          `space_name` VARCHAR(60) NOT NULL DEFAULT '',
          `quantity` DECIMAL(10,3) NULL,
          `unit` VARCHAR(16) NOT NULL DEFAULT 'pz',
          `details` MEDIUMTEXT NULL,
          `undone` TINYINT(1) NOT NULL DEFAULT 0,
          `created_at` DATETIME NOT NULL,
          PRIMARY KEY (`id`),
          KEY `idx_hh_created` (`household_id`, `created_at`)
        ) $opts");
    }
    if ($from < 11) {
        require_once __DIR__ . '/seed.php';
        $now = gmdate('Y-m-d H:i:s');
        $st = $pdo->prepare("UPDATE `{$p}recipes` SET image = ?, updated_at = ? WHERE name = ? AND image = ''");
        foreach (seed_recipes() as $r) if ($r['image'] !== '') $st->execute([$r['image'], $now, $r['name']]);
    }
    if ($from < 12) {
        require_once __DIR__ . '/seed.php';
        $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}products`")->fetchAll(), 'Field');
        if (!in_array('environment', $cols, true)) $pdo->exec("ALTER TABLE `{$p}products` ADD COLUMN `environment` VARCHAR(10) NULL AFTER `default_unit`");
        $st = $pdo->prepare("UPDATE `{$p}products` SET environment = ? WHERE name = ? AND environment IS NULL");
        foreach (seed_products() as $row) $st->execute([seed_product_environment($row[0], $row[2]), $row[0]]);
        $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}list_items`")->fetchAll(), 'Field');
        if (!in_array('environment', $cols, true)) $pdo->exec("ALTER TABLE `{$p}list_items` ADD COLUMN `environment` VARCHAR(10) NULL AFTER `notes`");
        $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}spaces`")->fetchAll(), 'Field');
        if (!in_array('environment', $cols, true)) {
            $pdo->exec("ALTER TABLE `{$p}spaces` ADD COLUMN `environment` VARCHAR(10) NOT NULL DEFAULT 'ambient' AFTER `icon`");
            $pdo->exec("UPDATE `{$p}spaces` SET environment = 'cold' WHERE icon = 'freezer'");
            $pdo->exec("UPDATE `{$p}spaces` SET environment = 'cool' WHERE icon IN ('fridge', 'snow')");
        }
    }
    if ($from < 13) {
        require_once __DIR__ . '/seed.php';
        $admin = $pdo->query("SELECT id FROM `{$p}users` WHERE role >= 10 ORDER BY id LIMIT 1")->fetchColumn();
        seed_database($pdo, $p, $admin ? (int)$admin : null);
        $st = $pdo->prepare("UPDATE `{$p}products` SET icon = ? WHERE name = ? AND icon = ?");
        foreach (seed4_icon_updates() as $name => [$old, $new]) $st->execute([$new, $name, $old]);
        $pdo->exec("UPDATE `{$p}recipe_ingredients` ri JOIN `{$p}products` pr ON pr.name = ri.name SET ri.product_id = pr.id WHERE ri.product_id IS NULL");
    }
    if ($from < 14) {
        $cols = array_column($pdo->query("SHOW COLUMNS FROM `{$p}users`")->fetchAll(), 'Field');
        if (!in_array('notify_off', $cols, true)) $pdo->exec("ALTER TABLE `{$p}users` ADD COLUMN `notify_off` VARCHAR(200) NULL AFTER `must_change_password`");
    }
    if ($from < 16) {
        require_once __DIR__ . '/seed.php';
        $admin = $pdo->query("SELECT id FROM `{$p}users` WHERE role >= 10 ORDER BY id LIMIT 1")->fetchColumn();
        seed_database($pdo, $p, $admin ? (int)$admin : null);
    }
    if ($from < 17) {
        require_once __DIR__ . '/seed.php';
        $st = $pdo->prepare("UPDATE `{$p}recipes` SET origin = ? WHERE name = ? AND origin = ?");
        foreach (seed_origin_fixes() as $name => [$old, $new]) $st->execute([$new, $name, $old]);
    }
    if ($from < 18) {
        require_once __DIR__ . '/seed.php';
        $admin = $pdo->query("SELECT id FROM `{$p}users` WHERE role >= 10 ORDER BY id LIMIT 1")->fetchColumn();
        seed_database($pdo, $p, $admin ? (int)$admin : null);
        $st = $pdo->prepare("UPDATE `{$p}recipes` SET origin = 'GB-ENG' WHERE name = ? AND origin = ?");
        $st->execute(['Purè di patate', 'FR']);
        $st->execute(['Torta di carote', 'CH']);
    }
    if ($from < 19) {
        $pdo->exec("UPDATE `{$p}recipes` SET origin = 'GB-ENG,CH' WHERE name = 'Torta di carote' AND origin = 'GB-ENG'");
    }
    if ($from < 20) {
        require_once __DIR__ . '/seed.php';
        $now = gmdate('Y-m-d H:i:s');
        $st = $pdo->prepare("UPDATE `{$p}recipes` SET image = ?, updated_at = ? WHERE name = ? AND image = ''");
        foreach (seed_recipes() as $r) if ($r['image'] !== '') $st->execute([$r['image'], $now, $r['name']]);
    }
    $pdo->prepare("INSERT INTO `{$p}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute([(string)SCHEMA_VERSION]);
}

/** Creates the tables (idempotent), upgrades older ones and records the schema version. */
function install_schema(PDO $pdo, string $prefix): void
{
    $from = 0;
    try {
        $from = (int)$pdo->query("SELECT v FROM `{$prefix}meta` WHERE k = 'schema_version'")->fetchColumn();
    } catch (Throwable $e) {
        $from = 0;
    }
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    if ($from > 0 && $from < SCHEMA_VERSION) migrate_schema($pdo, $prefix, $from);
    $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")
        ->execute([(string)SCHEMA_VERSION]);
}
