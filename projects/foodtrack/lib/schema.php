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
  `password_hash` VARCHAR(255) NOT NULL,
  `role` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 utente, 9 amministratore, 10 master admin (uno solo)',
  `status` ENUM('active','disabled') NOT NULL DEFAULT 'active',
  `must_change_password` TINYINT(1) NOT NULL DEFAULT 0,
  `active_household_id` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  `last_login_at` DATETIME NULL,
  `last_seen_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_role` (`role`)
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
  `expiry_tip` TEXT NULL,
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
  `icon` VARCHAR(40) NOT NULL DEFAULT 'home',
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
  `published` TINYINT(1) NOT NULL DEFAULT 1,
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
    ];
}

/** Creates the tables (idempotent) and records the schema version. */
function install_schema(PDO $pdo, string $prefix): void
{
    foreach (schema_statements($prefix) as $sql) $pdo->exec($sql);
    $pdo->prepare("INSERT INTO `{$prefix}meta` (k, v) VALUES ('schema_version', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")
        ->execute([(string)SCHEMA_VERSION]);
}
