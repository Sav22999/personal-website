<?php
/**
 * Households (shared workspaces) and account helpers.
 */

declare(strict_types=1);

require_once __DIR__ . '/seed.php';

function unique_invite_code(): string
{
    for ($i = 0; $i < 20; $i++) {
        $code = random_code(8);
        $st = db()->prepare('SELECT 1 FROM ' . t('households') . ' WHERE invite_code = ?');
        $st->execute([$code]);
        if (!$st->fetchColumn()) return $code;
    }
    throw new RuntimeException('Impossibile generare un codice invito');
}

/** Creates a household with the default spaces and makes $userId its owner and active household. */
function create_household(int $userId, string $name): int
{
    $now = now_utc();
    db()->prepare('INSERT INTO ' . t('households') . ' (name, invite_code, owner_id, created_at) VALUES (?,?,?,?)')
        ->execute([$name, unique_invite_code(), $userId, $now]);
    $hid = (int)db()->lastInsertId();
    db()->prepare('INSERT INTO ' . t('household_members') . " (household_id, user_id, role, joined_at) VALUES (?,?, 'owner', ?)")
        ->execute([$hid, $userId, $now]);
    $ins = db()->prepare('INSERT INTO ' . t('spaces') . ' (household_id, name, icon, notes, sort, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)');
    foreach (default_spaces() as $i => [$sname, $icon, $notes]) $ins->execute([$hid, tr($sname), $icon, tr($notes), $i * 10, $userId, $now, $now]);
    db()->prepare('UPDATE ' . t('users') . ' SET active_household_id = ? WHERE id = ?')->execute([$hid, $userId]);
    return $hid;
}

function user_households(int $userId): array
{
    $st = db()->prepare(
        'SELECT h.id, h.name, h.owner_id, m.role, (SELECT COUNT(*) FROM ' . t('household_members') . ' x WHERE x.household_id = h.id) AS members
         FROM ' . t('household_members') . ' m JOIN ' . t('households') . ' h ON h.id = m.household_id
         WHERE m.user_id = ? ORDER BY h.name'
    );
    $st->execute([$userId]);
    return array_map(function ($r) {
        return ['id' => (int)$r['id'], 'name' => $r['name'], 'role' => $r['role'], 'members' => (int)$r['members']];
    }, $st->fetchAll());
}

/** Returns the role of the user in the household ('owner' / 'member') or null. */
function membership(int $householdId, int $userId): ?string
{
    $st = db()->prepare('SELECT role FROM ' . t('household_members') . ' WHERE household_id = ? AND user_id = ?');
    $st->execute([$householdId, $userId]);
    $r = $st->fetchColumn();
    return $r === false ? null : (string)$r;
}

/**
 * Active household of the user. If it is missing or the user is no longer a member,
 * falls back to the first one, or creates a new one.
 */
function active_household(array $u): int
{
    $hid = $u['active_household_id'];
    if ($hid && membership($hid, $u['id'])) return $hid;
    $list = user_households($u['id']);
    if ($list) {
        $hid = $list[0]['id'];
        db()->prepare('UPDATE ' . t('users') . ' SET active_household_id = ? WHERE id = ?')->execute([$hid, $u['id']]);
        return $hid;
    }
    return create_household($u['id'], tr("{name}'s home", ['name' => first_name($u['name'])]));
}

function first_name(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    return $parts[0] ?: $name;
}

/**
 * Removes a user from a household. If they were the owner, ownership passes to the oldest member;
 * if nobody is left, the household and all its data are deleted.
 */
function leave_household(int $householdId, int $userId): void
{
    $pdo = db();
    $pdo->prepare('DELETE FROM ' . t('household_members') . ' WHERE household_id = ? AND user_id = ?')->execute([$householdId, $userId]);
    $st = $pdo->prepare('SELECT user_id FROM ' . t('household_members') . ' WHERE household_id = ? ORDER BY (role = \'owner\') DESC, joined_at ASC LIMIT 1');
    $st->execute([$householdId]);
    $next = $st->fetchColumn();
    if ($next === false) {
        $pdo->prepare('DELETE FROM ' . t('households') . ' WHERE id = ?')->execute([$householdId]);
        return;
    }
    $st = $pdo->prepare('SELECT COUNT(*) FROM ' . t('household_members') . " WHERE household_id = ? AND role = 'owner'");
    $st->execute([$householdId]);
    if ((int)$st->fetchColumn() === 0) {
        $pdo->prepare('UPDATE ' . t('household_members') . " SET role = 'owner' WHERE household_id = ? AND user_id = ?")->execute([$householdId, $next]);
        $pdo->prepare('UPDATE ' . t('households') . ' SET owner_id = ? WHERE id = ?')->execute([$next, $householdId]);
    }
}

/** Deletes a user and handles the households they belong to. */
function delete_user_account(int $userId): void
{
    $st = db()->prepare('SELECT household_id FROM ' . t('household_members') . ' WHERE user_id = ?');
    $st->execute([$userId]);
    foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $hid) leave_household((int)$hid, $userId);
    db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$userId]);
}

function validate_password(string $pw): void
{
    if (mb_strlen($pw) < 8) fail('The password must be at least 8 characters long.', 422, 'validation');
    if (mb_strlen($pw) > 200) fail('The password is too long.', 422, 'validation');
}

function temp_password(): string
{
    return random_code(4) . '-' . random_code(4) . '-' . random_code(4);
}
