<?php
/**
 * Rendicontazione Ore: JSON API. Every call is api/index.php?action=<name>.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/bootstrap.php';
require_once dirname(__DIR__) . '/lib/auth.php';
require_once dirname(__DIR__) . '/lib/timesheet.php';
require_once dirname(__DIR__) . '/lib/history.php';
require_once dirname(__DIR__) . '/lib/xlsx.php';

header('X-Content-Type-Options: nosniff');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

$action = (string)($_GET['action'] ?? '');
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

/** Changing actions need POST and our header, which another site cannot send: this stops CSRF. */
function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') fail('Metodo non consentito.', 405);
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'rendicontazione') fail('Richiesta non valida.', 403, 'csrf');
}

/** Whose hours this request is about: yourself, or anybody if you are an administrator. */
function target_user(array $me): int
{
    $id = (int)($_GET['user'] ?? body()['user'] ?? $_POST['user'] ?? 0);
    if ($id <= 0 || $id === $me['id']) return $me['id'];
    if ($me['role'] < ROLE_ADMIN) fail('Puoi vedere solo le tue ore.', 403, 'forbidden');
    if (!user_by('id', (string)$id)) fail('Utente non trovato.', 404);
    return $id;
}

function year_param(): int
{
    $year = (int)($_GET['year'] ?? date('Y'));
    if ($year < 2000 || $year > 2100) fail('Anno non valido.', 422);
    return $year;
}

function date_param(): string
{
    $date = body()['date'] ?? null;
    if (!valid_date($date)) fail('Data non valida.', 422);
    return $date;
}

function month_response(int $userId, string $date): array
{
    return month_data($userId, (int)substr($date, 0, 4), (int)substr($date, 5, 2)) + ['usual' => usual_slots($userId)];
}

function week_response(int $userId, string $date): array
{
    return week_data($userId, $date) + ['usual' => usual_slots($userId)];
}

/** After a change the app gets back what it has on screen: the week it names, otherwise the month. */
function days_response(int $userId, string $date): array
{
    $week = body()['week'] ?? null;
    return valid_date($week) ? week_response($userId, $week) : month_response($userId, $date);
}

/** What the app needs to start. */
function boot_response(): array
{
    $user = current_user();
    $isAdmin = $user && $user['status'] === STATUS_ACTIVE && $user['role'] >= ROLE_ADMIN;
    return [
        'ok' => true,
        'googleClientId' => (string)config_value('google_client_id', ''),
        'devLogin' => is_local_dev(),
        'user' => $user ? user_public($user) : null,
        'workers' => $isAdmin ? workers_list() : [],
        'pending' => $isAdmin ? pending_count() : 0,
    ];
}

try {
    if (!credentials() && !is_local_dev()) {
        fail('credentials.php manca sul server.', 503, 'not_configured');
    }

    switch ($action) {
        case 'boot':
            send_json(boot_response());
            break;

        case 'auth.google':
            require_post();
            $credential = trim((string)(body()['credential'] ?? ''));
            if ($credential === '') fail('Accesso con Google non riuscito. Riprova.', 422, 'google_token');
            $user = user_from_google(verify_google_token($credential));
            login_user((int)$user['id']);
            send_json(['ok' => true]);
            break;

        case 'auth.dev':
            // Only for `php -S` on this machine: see is_local_dev()
            if (!is_local_dev()) fail('Azione non riconosciuta.', 404, 'not_found');
            require_post();
            $role = (int)(body()['role'] ?? ROLE_USER);
            $email = ['1' => 'lavoratore@dev.invalid', '9' => 'admin@dev.invalid', '10' => 'master@dev.invalid'][(string)$role] ?? null;
            if (!$email) fail('Ruolo non valido.', 422);
            $user = user_by('email', $email);
            $id = $user ? (int)$user['id'] : user_create(['1' => 'Lavoratore di prova', '9' => 'Admin di prova', '10' => 'Master di prova'][(string)$role], $email, $role, STATUS_ACTIVE);
            login_user($id);
            send_json(['ok' => true]);
            break;

        case 'auth.logout':
            require_post();
            logout_user();
            send_json(['ok' => true]);
            break;

        case 'month':
            $me = require_active();
            $year = year_param();
            $month = (int)($_GET['month'] ?? date('n'));
            if ($month < 1 || $month > 12) fail('Mese non valido.', 422);
            $userId = target_user($me);
            send_json(['ok' => true, 'presets' => presets_get($userId)] + month_response($userId, sprintf('%04d-%02d-01', $year, $month)));
            break;

        case 'week':
            $me = require_active();
            $date = (string)($_GET['start'] ?? date('Y-m-d'));
            if (!valid_date($date)) fail('Data non valida.', 422);
            $userId = target_user($me);
            send_json(['ok' => true, 'presets' => presets_get($userId)] + week_response($userId, $date));
            break;

        case 'year':
            $me = require_active();
            send_json(['ok' => true] + year_data(target_user($me), year_param()));
            break;

        case 'day.save':
            require_post();
            $me = require_active();
            $userId = target_user($me);
            $date = date_param();
            day_change($me['id'], $userId, $date, normalize_day(body()));
            send_json(['ok' => true] + days_response($userId, $date));
            break;

        case 'day.delete':
            require_post();
            $me = require_active();
            $userId = target_user($me);
            $date = date_param();
            day_change($me['id'], $userId, $date, null);
            send_json(['ok' => true] + days_response($userId, $date));
            break;

        case 'days.fill':
            // Empty days only: a day recorded in the meantime is never overwritten
            require_post();
            $me = require_active();
            $userId = target_user($me);
            $dates = body()['dates'] ?? null;
            if (!is_array($dates) || !$dates || count($dates) > 7) fail('Giorni non validi.', 422);
            $snapshot = normalize_day(['kind' => 'lavoro', 'slots' => body()['slots'] ?? null]);
            $snapshots = [];
            foreach ($dates as $date) {
                if (!valid_date($date)) fail('Data non valida.', 422);
                if (day_get($userId, $date) === null) $snapshots[$date] = $snapshot;
            }
            ksort($snapshots);
            $changed = days_import($me['id'], $userId, $snapshots, 'Segnati ' . count($snapshots) . " giorni con l'orario solito");
            send_json(['ok' => true, 'changed' => $changed] + days_response($userId, (string)$dates[0]));
            break;

        case 'presets.save':
            require_post();
            $me = require_active();
            $userId = target_user($me);
            $presets = presets_save($userId, body()['presets'] ?? null);
            history_add($me['id'], $userId, 'presets', 'Orari rapidi modificati');
            send_json(['ok' => true, 'presets' => $presets]);
            break;

        case 'history':
            $me = require_active();
            $limit = 30;
            send_json(['ok' => true] + history_list(target_user($me), $me['role'] >= ROLE_ADMIN, $limit, (int)($_GET['before'] ?? 0)));
            break;

        case 'history.undo':
            require_post();
            $me = require_admin();
            $result = history_undo((int)(body()['id'] ?? 0), $me, !empty(body()['force']));
            if (!$result['ok']) {
                fail('Questi giorni sono stati cambiati ancora dopo.', 409, 'conflict', ['conflicts' => $result['conflicts']]);
            }
            send_json($result);
            break;

        case 'export':
            $me = require_active();
            $userId = target_user($me);
            $year = year_param();
            $file = xlsx_export($userId, $year);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Rendicontazione ' . $year . '.xlsx"');
            header('Content-Length: ' . strlen($file));
            echo $file;
            exit;

        case 'import.preview':
            require_post();
            $me = require_admin();
            $userId = target_user($me);
            $upload = $_FILES['file'] ?? null;
            if (!$upload || $upload['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($upload['tmp_name'])) {
                fail('Caricamento del file non riuscito.', 422);
            }
            if ($upload['size'] > 10 * 1024 * 1024) fail('Il file è troppo grande (massimo 10 MB).', 422);
            $parsed = timesheet_parse(xlsx_read((string)file_get_contents($upload['tmp_name'])), (int)date('Y'));
            send_json(['ok' => true] + import_preview($userId, $parsed));
            break;

        case 'import.apply':
            require_post();
            $me = require_admin();
            $userId = target_user($me);
            $input = body()['days'] ?? null;
            if (!is_array($input) || !$input) fail('Nessun giorno da importare.', 422);
            $snapshots = [];
            foreach ($input as $date => $day) {
                if (!valid_date($date) || !is_array($day)) fail('Dati non validi.', 422);
                $snapshots[$date] = normalize_day($day);
            }
            ksort($snapshots);
            $changed = days_import($me['id'], $userId, $snapshots, 'Importati ' . count($snapshots) . ' giorni da Excel');
            send_json(['ok' => true, 'changed' => $changed]);
            break;

        case 'users':
            require_admin();
            send_json(['ok' => true, 'users' => users_list(), 'pending' => pending_count()]);
            break;

        case 'users.add':
            // An account prepared in advance: its owner goes straight in with Google, no approval
            require_post();
            $me = require_admin();
            $email = mb_strtolower(trim((string)(body()['email'] ?? '')));
            $name = trim((string)(body()['name'] ?? ''));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail("Controlla l'indirizzo email.", 422);
            if ($name === '' || mb_strlen($name) > 100) fail('Scrivi il nome della persona.', 422);
            if (user_by('email', $email)) fail('Esiste già un account con questa email.', 409);
            $id = user_create($name, $email, ROLE_USER, STATUS_ACTIVE);
            history_add($me['id'], $id, 'user.add', "Account creato per $name");
            send_json(['ok' => true, 'id' => $id, 'users' => users_list(), 'workers' => workers_list(), 'pending' => pending_count()]);
            break;

        case 'users.update':
            require_post();
            $me = require_admin();
            $id = (int)(body()['id'] ?? 0);
            $target = user_by('id', (string)$id);
            if (!$target) fail('Utente non trovato.', 404);
            if ($id === $me['id']) fail('Non puoi cambiare il tuo stesso account.', 403, 'forbidden');
            if ((int)$target['role'] >= ROLE_MASTER) fail('Il Master Admin non si può modificare.', 403, 'forbidden');

            $b = body();
            $changes = [];
            if (array_key_exists('status', $b)) {
                if ((int)$target['role'] >= ROLE_ADMIN && $me['role'] < ROLE_MASTER) {
                    fail('Solo il Master Admin può cambiare un amministratore.', 403, 'forbidden');
                }
                if (!in_array($b['status'], [STATUS_ACTIVE, STATUS_REJECTED, STATUS_DISABLED], true)) fail('Stato non valido.', 422);
                $changes['status'] = $b['status'];
            }
            if (array_key_exists('role', $b)) {
                if ($me['role'] < ROLE_MASTER) fail('Solo il Master Admin può cambiare i ruoli.', 403, 'forbidden');
                if (!in_array($b['role'], [ROLE_USER, ROLE_ADMIN], true)) fail('Ruolo non valido.', 422);
                $changes['role'] = $b['role'];
            }
            if (!$changes) fail('Niente da cambiare.', 422);
            user_set($id, $changes);

            $texts = [STATUS_ACTIVE => 'Accesso attivato', STATUS_REJECTED => 'Accesso rifiutato', STATUS_DISABLED => 'Accesso disattivato'];
            $summary = isset($changes['status'])
                ? $texts[$changes['status']] . ' per ' . $target['name']
                : ($changes['role'] === ROLE_ADMIN ? $target['name'] . ' è ora amministratore' : $target['name'] . ' non è più amministratore');
            history_add($me['id'], $id, isset($changes['status']) ? 'user.status' : 'user.role', $summary);
            send_json(['ok' => true, 'users' => users_list(), 'workers' => workers_list(), 'pending' => pending_count()]);
            break;

        case 'users.edit':
            require_post();
            $me = require_master();
            $id = (int)(body()['id'] ?? 0);
            $target = user_by('id', (string)$id);
            if (!$target) fail('Utente non trovato.', 404);
            $name = trim((string)(body()['name'] ?? ''));
            $email = mb_strtolower(trim((string)(body()['email'] ?? '')));
            if ($name === '' || mb_strlen($name) > 100) fail('Scrivi il nome della persona.', 422);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail("Controlla l'indirizzo email.", 422);

            $fields = ['name' => $name];
            if ($email !== $target['email']) {
                if ((int)$target['role'] >= ROLE_MASTER) fail("L'email del Master Admin si cambia in credentials.php.", 403, 'forbidden');
                if (user_by('email', $email)) fail('Esiste già un account con questa email.', 409);
                // The old Google account must stop working: it is linked by google_sub, not by email
                $fields += ['email' => $email, 'google_sub' => null];
            }
            in_transaction(function () use ($me, $id, $fields, $target) {
                user_set($id, $fields);
                if (isset($fields['email'])) {
                    db()->prepare('DELETE FROM ' . t('auth_tokens') . ' WHERE user_id = ?')->execute([$id]);
                }
                history_add($me['id'], $id, 'user.edit', 'Account modificato: ' . $target['name'] . (isset($fields['email']) ? ' (nuova email)' : ''));
            });
            send_json(['ok' => true, 'users' => users_list(), 'workers' => workers_list(), 'pending' => pending_count()]);
            break;

        case 'users.delete':
            require_post();
            $me = require_master();
            $id = (int)(body()['id'] ?? 0);
            $target = user_by('id', (string)$id);
            if (!$target) fail('Utente non trovato.', 404);
            if ($id === $me['id'] || (int)$target['role'] >= ROLE_MASTER) fail('Il Master Admin non si può eliminare.', 403, 'forbidden');
            $st = db()->prepare('SELECT COUNT(*) FROM ' . t('days') . ' WHERE user_id = ?');
            $st->execute([$id]);
            if ((int)$st->fetchColumn() > 0) fail('Questa persona ha ore registrate: si può solo disattivare.', 409, 'has_hours');
            user_delete($id);
            history_add($me['id'], $me['id'], 'user.delete', 'Account eliminato: ' . $target['name']);
            send_json(['ok' => true, 'users' => users_list(), 'workers' => workers_list(), 'pending' => pending_count()]);
            break;

        default:
            fail('Azione non riconosciuta.', 404, 'not_found');
    }
} catch (UserError $e) {
    fail($e->getMessage(), 422, 'invalid');
} catch (PDOException $e) {
    error_log('rendicontazione: ' . $e->getMessage());
    fail((bool)config_value('debug', false) ? $e->getMessage() : 'Il database non risponde. Riprova tra poco.', 503, 'database');
} catch (Throwable $e) {
    error_log('rendicontazione: ' . $e->getMessage());
    fail((bool)config_value('debug', false) ? $e->getMessage() : 'Si è verificato un errore. Riprova.', 500, 'server_error');
}
