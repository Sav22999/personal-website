<?php
/**
 * Rendicontazione Ore — REST API controller.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';
require_once dirname(__DIR__) . '/lib/models.php';
require_once dirname(__DIR__) . '/lib/importer.php';
require_once dirname(__DIR__) . '/lib/seed-2026.php';

header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

$action = (string)($_GET['action'] ?? $_POST['action'] ?? '');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Helper to determine the effective user for hours operations
// If current user is Admin and target_user_id is provided, use that; otherwise use self.
function get_target_user_id(array $currentUser): int
{
    $target = (int)($_GET['target_user_id'] ?? $_POST['target_user_id'] ?? body()['target_user_id'] ?? 0);
    if ($target > 0 && $target !== $currentUser['id']) {
        if ($currentUser['role'] < ROLE_ADMIN) {
            fail('Solo gli amministratori possono consultare le ore di altri utenti.', 403, 'forbidden');
        }
        $targetUser = user_get_by_id($target);
        if (!$targetUser) {
            fail('Utente selezionato non trovato.', 404, 'user_not_found');
        }
        return $target;
    }
    return $currentUser['id'];
}

try {
    switch ($action) {
        // --- CONFIG & PUBLIC ---
        case 'config':
            send_json([
                'ok' => true,
                'appName' => 'Rendicontazione Ore',
                'googleClientId' => (string)config_value('google_client_id', ''),
                'isSqlite' => is_sqlite(),
                'debug' => (bool)config_value('debug', false),
            ]);
            break;

        // --- AUTHENTICATION ---
        case 'auth.google':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $b = body();
            $credential = trim((string)($b['credential'] ?? ''));
            if ($credential === '') fail('Token di autenticazione Google mancante.', 400);

            $g = verify_google_token($credential);
            $email = $g['email'];
            $sub = $g['sub'];
            $name = $g['name'];
            $picture = $g['picture'];

            // Check if user already exists
            $user = user_get_by_google($sub);
            if (!$user) {
                $user = user_get_by_email($email);
            }

            $masterEmail = (string)config_value('master_admin_email', 'saverio.morelli@gmail.com');
            $isMasterCandidate = (strcasecmp($email, $masterEmail) === 0);

            $totalUsers = (int)db()->query('SELECT COUNT(*) FROM ' . t('users'))->fetchColumn();

            if (!$user) {
                // New user registration
                // If it's the master admin candidate or the very first user in the DB: auto-approve as Master Admin!
                $isFirstUser = ($totalUsers === 0);
                $role = ($isMasterCandidate || $isFirstUser) ? ROLE_MASTER : ROLE_USER;
                $status = ($isMasterCandidate || $isFirstUser) ? STATUS_ACTIVE : STATUS_PENDING;

                $newId = user_create([
                    'name' => $name,
                    'email' => $email,
                    'google_sub' => $sub,
                    'avatar_url' => $picture,
                    'role' => $role,
                    'status' => $status,
                ]);

                // Auto-seed 2026 data for the first user / father
                if ($isFirstUser || $status === STATUS_ACTIVE) {
                    try {
                        seed_father_timesheet($newId);
                    } catch (Throwable $e) {
                        // Keep going if seed fails
                    }
                }

                $user = user_get_by_id($newId);
            } else {
                // Existing user: update details
                $updateData = [
                    'name' => $name,
                    'avatar_url' => $picture,
                ];
                if (empty($user['google_sub'])) {
                    $updateData['google_sub'] = $sub;
                }
                // If email matches master admin, ensure master role and active status
                if ($isMasterCandidate && ($user['role'] < ROLE_MASTER || $user['status'] !== STATUS_ACTIVE)) {
                    $updateData['role'] = ROLE_MASTER;
                    $updateData['status'] = STATUS_ACTIVE;
                }

                user_update((int)$user['id'], $updateData);
                $user = user_get_by_id((int)$user['id']);
            }

            $tokenRaw = login_user((int)$user['id']);
            $pendingCount = ($user['role'] >= ROLE_ADMIN) ? get_pending_users_count() : 0;

            send_json([
                'ok' => true,
                'token' => $tokenRaw,
                'user' => [
                    'id' => (int)$user['id'],
                    'name' => (string)$user['name'],
                    'email' => (string)$user['email'],
                    'role' => (int)$user['role'],
                    'role_label' => ROLE_LABELS[(int)$user['role']] ?? 'Utente',
                    'status' => (string)$user['status'],
                    'status_label' => STATUS_LABELS[(string)$user['status']] ?? $user['status'],
                    'avatar_url' => (string)($user['avatar_url'] ?? ''),
                ],
                'pending_users_count' => $pendingCount,
            ]);
            break;

        case 'auth.dev_login':
            // Dev login for local testing
            if (!config_value('debug', false) && !is_sqlite()) {
                fail('Dev login disabilitato.', 403);
            }
            $targetRole = (int)($_POST['role'] ?? body()['role'] ?? ROLE_MASTER);
            $email = $targetRole >= ROLE_MASTER ? 'saverio.morelli@gmail.com' : ($targetRole >= ROLE_ADMIN ? 'admin@famiglia.local' : 'papa@famiglia.local');
            $name = $targetRole >= ROLE_MASTER ? 'Saverio Morelli' : ($targetRole >= ROLE_ADMIN ? 'Amministratore' : 'Papà');

            $user = user_get_by_email($email);
            if (!$user) {
                $newId = user_create([
                    'name' => $name,
                    'email' => $email,
                    'role' => $targetRole,
                    'status' => STATUS_ACTIVE,
                ]);
                $user = user_get_by_id($newId);
                // Seed 2026 data
                try {
                    seed_father_timesheet($newId);
                } catch (Throwable $e) {}
            } else {
                user_update((int)$user['id'], ['status' => STATUS_ACTIVE, 'role' => $targetRole]);
                $user = user_get_by_id((int)$user['id']);
            }

            $tokenRaw = login_user((int)$user['id']);
            send_json([
                'ok' => true,
                'token' => $tokenRaw,
                'user' => [
                    'id' => (int)$user['id'],
                    'name' => (string)$user['name'],
                    'email' => (string)$user['email'],
                    'role' => (int)$user['role'],
                    'role_label' => ROLE_LABELS[(int)$user['role']] ?? 'Utente',
                    'status' => (string)$user['status'],
                    'status_label' => STATUS_LABELS[(string)$user['status']] ?? $user['status'],
                    'avatar_url' => (string)($user['avatar_url'] ?? ''),
                ],
            ]);
            break;

        case 'auth.me':
            $u = current_user();
            if (!$u) {
                send_json(['ok' => true, 'authenticated' => false, 'user' => null]);
            }
            $pendingCount = ($u['role'] >= ROLE_ADMIN) ? get_pending_users_count() : 0;
            send_json([
                'ok' => true,
                'authenticated' => true,
                'user' => $u,
                'pending_users_count' => $pendingCount,
            ]);
            break;

        case 'auth.logout':
            logout_user();
            send_json(['ok' => true]);
            break;

        // --- HOURS & TIMESHEET ---
        case 'hours.month':
            $u = require_active_user();
            $targetUserId = get_target_user_id($u);
            $year = (int)($_GET['year'] ?? date('Y'));
            $month = (int)($_GET['month'] ?? date('n'));
            if ($year < 2020 || $year > 2040) $year = (int)date('Y');
            if ($month < 1 || $month > 12) $month = (int)date('n');

            $data = work_days_get_month($targetUserId, $year, $month);
            $targetUser = user_get_by_id($targetUserId);

            send_json([
                'ok' => true,
                'data' => $data,
                'target_user' => [
                    'id' => (int)$targetUser['id'],
                    'name' => (string)$targetUser['name'],
                    'email' => (string)$targetUser['email'],
                ],
                'is_own' => ($targetUserId === $u['id']),
            ]);
            break;

        case 'hours.year':
            $u = require_active_user();
            $targetUserId = get_target_user_id($u);
            $year = (int)($_GET['year'] ?? date('Y'));
            if ($year < 2020 || $year > 2040) $year = (int)date('Y');

            $data = work_days_get_year_summary($targetUserId, $year);
            $targetUser = user_get_by_id($targetUserId);

            send_json([
                'ok' => true,
                'data' => $data,
                'target_user' => [
                    'id' => (int)$targetUser['id'],
                    'name' => (string)$targetUser['name'],
                    'email' => (string)$targetUser['email'],
                ],
                'is_own' => ($targetUserId === $u['id']),
            ]);
            break;

        case 'hours.save_day':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_active_user();
            $targetUserId = get_target_user_id($u);
            $b = body();

            $date = trim((string)($b['date'] ?? ''));
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                fail('Data non valida. Formato richiesto: AAAA-MM-GG.', 422);
            }

            $dayType = trim((string)($b['day_type'] ?? 'work'));
            $notes = trim((string)($b['notes'] ?? ''));
            $shifts = is_array($b['shifts'] ?? null) ? $b['shifts'] : [];

            $dayId = work_day_save($targetUserId, $date, $dayType, $notes ?: null, $shifts);

            // Return updated month data so UI immediately syncs
            $parts = explode('-', $date);
            $updatedMonth = work_days_get_month($targetUserId, (int)$parts[0], (int)$parts[1]);

            send_json([
                'ok' => true,
                'day_id' => $dayId,
                'date' => $date,
                'month_data' => $updatedMonth,
            ]);
            break;

        case 'hours.delete_day':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_active_user();
            $targetUserId = get_target_user_id($u);
            $b = body();
            $date = trim((string)($b['date'] ?? ''));
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                fail('Data non valida.', 422);
            }

            work_day_delete($targetUserId, $date);

            $parts = explode('-', $date);
            $updatedMonth = work_days_get_month($targetUserId, (int)$parts[0], (int)$parts[1]);

            send_json([
                'ok' => true,
                'deleted' => $date,
                'month_data' => $updatedMonth,
            ]);
            break;

        case 'hours.quick_preset':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_active_user();
            $targetUserId = get_target_user_id($u);
            $b = body();
            $date = trim((string)($b['date'] ?? date('Y-m-d')));
            $presetId = (int)($b['preset_id'] ?? 1);

            $presets = presets_get_for_user($targetUserId);
            $found = null;
            foreach ($presets as $p) {
                if ($p['id'] === $presetId) {
                    $found = $p;
                    break;
                }
            }
            if (!$found && !empty($presets)) {
                $found = $presets[0];
            }
            if (!$found) fail('Nessun turno predefinito trovato.', 404);

            work_day_save($targetUserId, $date, 'work', null, $found['shifts']);

            $parts = explode('-', $date);
            $updatedMonth = work_days_get_month($targetUserId, (int)$parts[0], (int)$parts[1]);

            send_json([
                'ok' => true,
                'preset_applied' => $found['name'],
                'date' => $date,
                'month_data' => $updatedMonth,
            ]);
            break;

        // --- PRESETS ---
        case 'presets.list':
            $u = require_active_user();
            $targetUserId = get_target_user_id($u);
            send_json([
                'ok' => true,
                'presets' => presets_get_for_user($targetUserId),
            ]);
            break;

        // --- EXPORT & IMPORT ---
        case 'export.csv':
            $u = require_active_user();
            $targetUserId = get_target_user_id($u);
            $year = (int)($_GET['year'] ?? date('Y'));
            $month = isset($_GET['month']) && $_GET['month'] !== '' ? (int)$_GET['month'] : null;

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="rendicontazione-' . ($month ? sprintf('%04d-%02d', $year, $month) : $year) . '.csv"');

            $out = fopen('php://output', 'w');
            fputcsv($out, ['Data', 'Orario inizio', 'Orario fine', 'Straordinario', 'Ore ordinarie', 'Ore straordinarie', 'Totale parziale', 'Note']);

            if ($month) {
                $mData = work_days_get_month($targetUserId, $year, $month);
                foreach ($mData['days'] as $d) {
                    if (empty($d['shifts'])) {
                        if ($d['notes'] || $d['day_type'] !== 'work') {
                            fputcsv($out, [$d['date'], '', '', 'False', '00:00:00', '00:00:00', $d['cumulative_hours_formatted'], $d['notes'] ?: $d['day_type']]);
                        }
                    } else {
                        foreach ($d['shifts'] as $i => $s) {
                            $dateCol = ($i === 0) ? $d['date'] : '';
                            $ordCol = $s['hours_ordinary'] > 0 ? format_hours_hms($s['hours_ordinary']) : '';
                            $strCol = $s['hours_overtime'] > 0 ? format_hours_hms($s['hours_overtime']) : '';
                            $noteCol = $s['notes'] ?: ($i === 0 ? $d['notes'] : '');
                            fputcsv($out, [$dateCol, $s['start_time'], $s['end_time'], $s['is_overtime'] ? 'True' : 'False', $ordCol, $strCol, $d['cumulative_hours_formatted'], $noteCol]);
                        }
                    }
                }
            } else {
                for ($m = 1; $m <= 12; $m++) {
                    $mData = work_days_get_month($targetUserId, $year, $m);
                    fputcsv($out, [MONTH_NAMES[$m] . ' ' . $year, '', '', '', '', '', '', '']);
                    foreach ($mData['days'] as $d) {
                        if (!empty($d['shifts'])) {
                            foreach ($d['shifts'] as $i => $s) {
                                $dateCol = ($i === 0) ? $d['date'] : '';
                                $ordCol = $s['hours_ordinary'] > 0 ? format_hours_hms($s['hours_ordinary']) : '';
                                $strCol = $s['hours_overtime'] > 0 ? format_hours_hms($s['hours_overtime']) : '';
                                $noteCol = $s['notes'] ?: ($i === 0 ? $d['notes'] : '');
                                fputcsv($out, [$dateCol, $s['start_time'], $s['end_time'], $s['is_overtime'] ? 'True' : 'False', $ordCol, $strCol, $d['cumulative_hours_formatted'], $noteCol]);
                            }
                        } elseif ($d['notes']) {
                            fputcsv($out, [$d['date'], '', '', 'False', '00:00:00', '00:00:00', $d['cumulative_hours_formatted'], $d['notes']]);
                        }
                    }
                }
            }
            fclose($out);
            exit;

        case 'import.csv':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_admin();
            $targetUserId = get_target_user_id($u);
            $b = body();
            $csvContent = (string)($b['csv_content'] ?? '');
            if (empty($csvContent) && !empty($_FILES['file']['tmp_name'])) {
                $csvContent = file_get_contents($_FILES['file']['tmp_name']);
            }
            if (trim($csvContent) === '') {
                fail('Contenuto CSV mancante.', 400);
            }

            $year = (int)($b['year'] ?? date('Y'));
            $res = import_timesheet_csv($targetUserId, $csvContent, $year);
            send_json($res);
            break;

        // --- ADMIN: USERS & APPROVALS ---
        case 'users.list':
            $u = require_admin();
            $status = isset($_GET['status']) ? (string)$_GET['status'] : null;
            $users = users_list($status);
            send_json([
                'ok' => true,
                'users' => $users,
                'pending_count' => get_pending_users_count(),
            ]);
            break;

        case 'users.approve':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_admin();
            $b = body();
            $targetId = (int)($b['user_id'] ?? 0);
            if ($targetId <= 0) fail('ID utente non valido.', 422);

            $targetUser = user_get_by_id($targetId);
            if (!$targetUser) fail('Utente non trovato.', 404);

            user_update($targetId, ['status' => STATUS_ACTIVE]);

            // If user has no work records yet, seed the 2026 data
            $hasDays = (int)db()->prepare('SELECT COUNT(*) FROM ' . t('work_days') . ' WHERE user_id = ?')->execute([$targetId]);
            $count = (int)db()->query('SELECT COUNT(*) FROM ' . t('work_days') . ' WHERE user_id = ' . $targetId)->fetchColumn();
            if ($count === 0) {
                try {
                    seed_father_timesheet($targetId);
                } catch (Throwable $e) {}
            }

            send_json([
                'ok' => true,
                'approved_user_id' => $targetId,
                'pending_count' => get_pending_users_count(),
            ]);
            break;

        case 'users.reject':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_admin();
            $b = body();
            $targetId = (int)($b['user_id'] ?? 0);
            if ($targetId <= 0) fail('ID utente non valido.', 422);
            if ($targetId === $u['id']) fail('Non puoi rifiutare il tuo stesso account.', 400);

            $targetUser = user_get_by_id($targetId);
            if (!$targetUser) fail('Utente non trovato.', 404);
            if ((int)$targetUser['role'] >= ROLE_MASTER) fail('Non puoi modificare il Master Admin.', 403);

            user_update($targetId, ['status' => STATUS_REJECTED]);
            send_json([
                'ok' => true,
                'rejected_user_id' => $targetId,
                'pending_count' => get_pending_users_count(),
            ]);
            break;

        case 'users.set_role':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_master(); // Only Master Admin can change user roles
            $b = body();
            $targetId = (int)($b['user_id'] ?? 0);
            $newRole = (int)($b['role'] ?? ROLE_USER);
            if ($targetId <= 0) fail('ID utente non valido.', 422);
            if ($targetId === $u['id']) fail('Non puoi declassare te stesso da Master Admin.', 400);
            if (!in_array($newRole, [ROLE_USER, ROLE_ADMIN], true)) {
                fail('Ruolo non valido.', 422);
            }

            user_update($targetId, ['role' => $newRole]);
            send_json([
                'ok' => true,
                'user_id' => $targetId,
                'new_role' => $newRole,
                'role_label' => ROLE_LABELS[$newRole],
            ]);
            break;

        case 'users.set_status':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_admin();
            $b = body();
            $targetId = (int)($b['user_id'] ?? 0);
            $newStatus = (string)($b['status'] ?? STATUS_ACTIVE);
            if ($targetId <= 0) fail('ID utente non valido.', 422);
            if ($targetId === $u['id'] && $newStatus !== STATUS_ACTIVE) {
                fail('Non puoi disabilitare il tuo account.', 400);
            }

            $targetUser = user_get_by_id($targetId);
            if (!$targetUser) fail('Utente non trovato.', 404);
            if ((int)$targetUser['role'] >= ROLE_MASTER && $targetId !== $u['id']) {
                fail('Non puoi modificare il Master Admin.', 403);
            }

            if (!in_array($newStatus, [STATUS_PENDING, STATUS_ACTIVE, STATUS_REJECTED, STATUS_DISABLED], true)) {
                fail('Stato non valido.', 422);
            }

            user_update($targetId, ['status' => $newStatus]);
            send_json([
                'ok' => true,
                'user_id' => $targetId,
                'new_status' => $newStatus,
                'status_label' => STATUS_LABELS[$newStatus] ?? $newStatus,
                'pending_count' => get_pending_users_count(),
            ]);
            break;

        case 'users.delete':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_master();
            $b = body();
            $targetId = (int)($b['user_id'] ?? 0);
            if ($targetId <= 0 || $targetId === $u['id']) {
                fail('Non puoi eliminare il tuo stesso account.', 400);
            }

            db()->prepare('DELETE FROM ' . t('users') . ' WHERE id = ?')->execute([$targetId]);
            send_json(['ok' => true, 'deleted_user_id' => $targetId]);
            break;

        case 'admin.seed_2026':
            if ($method !== 'POST') fail('Metodo non consentito', 405);
            $u = require_admin();
            $targetUserId = get_target_user_id($u);
            $res = seed_father_timesheet($targetUserId);
            send_json($res);
            break;

        default:
            fail("Azione '{$action}' non riconosciuta.", 404, 'not_found');
    }
} catch (Throwable $e) {
    $debug = (bool)config_value('debug', false);
    fail(
        $debug ? ($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()) : 'Si è verificato un errore interno. Riprova più tardi.',
        500,
        'server_error'
    );
}
