<?php
/**
 * Portal API. Every call names its action with ?r=name.
 *
 * Public:            auth.register, auth.login, auth.logout, auth.me
 * User (>=1):        content, state.get, state.set, auth.password, auth.profile
 *   (after a password reset only auth.password is allowed until the password is changed)
 * Content moderator (>=5): admin.overview (aggregated, anonymous), admin.content, admin.question.save|toggle|delete, admin.ref.save|delete, admin.flags
 * Administrator (>=9): admin.users, admin.user.stats (read-only), admin.user.update|reset_password|delete (users and moderators only)
 *                      admin.user.send_activation, admin.user.send_activation_all ("account activated" email)
 *                      admin.documents, admin.document.save (multipart) | delete | move ("Other useful documents" only)
 * Master admin (10): same as above, also on administrators, plus admin.user.transfer_master
 */

declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/schema.php';
require __DIR__ . '/../lib/content.php';
require __DIR__ . '/../lib/mailer.php';
require __DIR__ . '/../lib/email-template.php';
require __DIR__ . '/../lib/users.php';
require __DIR__ . '/../lib/documents.php';

set_exception_handler(function (Throwable $e) {
    error_log('[fipav-ripasso] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    $debug = false;
    try { $debug = !empty(config()['debug']); } catch (Throwable $ignored) {}
    fail('Errore interno del server.' . ($debug ? ' ' . $e->getMessage() : ''), 500, 'server_error');
});

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$route  = (string)($_GET['r'] ?? '');

// Every request that changes data must be a POST with a JSON body:
// forms on other sites cannot send JSON without a CORS preflight, which is not allowed here.
if ($method === 'POST') {
    $ct = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
    // Only exception: PDF uploads use multipart/form-data (protected by the CSRF token header)
    $isUpload = $route === 'admin.document.save' && strpos($ct, 'multipart/form-data') === 0;
    if (strpos($ct, 'application/json') !== 0 && !$isUpload) {
        // A file larger than post_max_size empties the request: report it clearly
        if ($route === 'admin.document.save' && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            fail('Il PDF è troppo grande: il limite è ' . round(documents_max_bytes() / 1048576) . ' MB.', 413, 'too_large');
        }
        fail('Content-Type non supportato.', 415, 'bad_content_type');
    }
} elseif ($method !== 'GET') {
    fail('Metodo non consentito.', 405, 'method');
}

function only(string $m): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $m) fail('Metodo non consentito.', 405, 'method');
}

switch ($route) {

    // ---------- Authentication ----------

    case 'auth.me':
        only('GET');
        $s = current_session();
        if (!$s) send_json(['ok' => true, 'user' => null]);
        send_json(['ok' => true, 'user' => public_user($s), 'csrf' => $s['csrf_token']]);

    case 'auth.register':
        only('POST');
        $u = register_user(request_body());
        send_json(['ok' => true, 'message' => 'Registrazione completata. Potrai accedere quando un amministratore avrà abilitato il tuo account.', 'user' => $u], 201);

    case 'auth.login':
        only('POST');
        send_json(['ok' => true] + login_user(request_body()));

    case 'auth.logout':
        only('POST');
        $s = current_session();
        if ($s) require_csrf($s);
        destroy_auth_session();
        send_json(['ok' => true]);

    case 'auth.password':
        only('POST');
        $s = require_user(PERM_USER, true);
        require_csrf($s);
        $u = change_own_password($s, request_body());
        send_json(['ok' => true, 'user' => public_user($u)]);

    case 'auth.profile':
        only('POST');
        $s = require_user();
        require_csrf($s);
        $name = str_param(request_body(), 'name', 100, true);
        if (mb_strlen($name) < 2) fail('Nome troppo corto.', 422, 'validation');
        db()->prepare('UPDATE ' . t('users') . ' SET name = ? WHERE id = ?')->execute([$name, $s['id']]);
        send_json(['ok' => true, 'user' => public_user(find_user($s['id']))]);

    // ---------- Quiz content ----------

    case 'content':
        only('GET');
        require_user();
        $version = meta_get('content_version', '0');
        $etag = '"c' . $version . '"';
        header('ETag: ' . $etag);
        if (trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
            header('Cache-Control: private, no-cache');
            http_response_code(304);
            exit;
        }
        if (!headers_sent() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            ob_start('ob_gzhandler');
        }
        $data = fetch_content(false);
        $data['documents'] = list_documents(false);
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: private, no-cache');
        header('X-Content-Type-Options: nosniff');
        echo json_encode(['ok' => true] + $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;

    // ---------- Personal state (statistics, bookmarks, suspended quizzes) ----------

    case 'state.get':
        only('GET');
        $s = require_user();
        $st = db()->prepare('SELECT state_key, value, updated_at FROM ' . t('user_state') . ' WHERE user_id = ?');
        $st->execute([$s['id']]);
        $out = [];
        foreach ($st as $r) $out[$r['state_key']] = $r['value'];
        send_json(['ok' => true, 'state' => $out]);

    case 'state.set':
        only('POST');
        $s = require_user();
        require_csrf($s);
        $body = request_body();
        $items = $body['items'] ?? null;
        if (!is_array($items) || !$items) fail('Nessun dato da salvare.', 422, 'validation');
        $up = db()->prepare('INSERT INTO ' . t('user_state') . ' (user_id, state_key, value, updated_at) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = VALUES(updated_at)');
        $del = db()->prepare('DELETE FROM ' . t('user_state') . ' WHERE user_id = ? AND state_key = ?');
        foreach ($items as $key => $value) {
            if (!in_array($key, STATE_KEYS, true)) fail('Chiave non valida: ' . $key, 422, 'validation');
            if ($value === null) { $del->execute([$s['id'], $key]); continue; }
            if (!is_string($value)) fail('Valore non valido.', 422, 'validation');
            if (strlen($value) > 4 * 1024 * 1024) fail('Dati troppo grandi.', 413, 'too_large');
            json_decode($value);
            if (json_last_error() !== JSON_ERROR_NONE) fail('Valore non valido (JSON atteso).', 422, 'validation');
            $up->execute([$s['id'], $key, $value, now_utc()]);
        }
        send_json(['ok' => true]);

    // ---------- Content editing (permissions >= 5) ----------

    case 'admin.content':
        only('GET');
        require_user(PERM_EDITOR);
        send_json(['ok' => true, 'sources' => SOURCES] + fetch_content(true));

    case 'admin.question.save':
        only('POST');
        $s = require_user(PERM_EDITOR);
        require_csrf($s);
        $code = save_question(request_body(), $s['id']);
        send_json(['ok' => true, 'code' => $code]);

    case 'admin.question.toggle':
        only('POST');
        $s = require_user(PERM_EDITOR);
        require_csrf($s);
        $b = request_body();
        set_question_active(str_param($b, 'code', 32, true), !empty($b['active']), $s['id']);
        send_json(['ok' => true]);

    case 'admin.question.delete':
        only('POST');
        $s = require_user(PERM_EDITOR);
        require_csrf($s);
        delete_question(str_param(request_body(), 'code', 32, true));
        send_json(['ok' => true]);

    case 'admin.ref.save':
        only('POST');
        $s = require_user(PERM_EDITOR);
        require_csrf($s);
        $ref = save_ref(request_body(), $s['id']);
        send_json(['ok' => true, 'code' => $ref['code']]);

    case 'admin.ref.delete':
        only('POST');
        $s = require_user(PERM_EDITOR);
        require_csrf($s);
        delete_ref(str_param(request_body(), 'code', 32, true));
        send_json(['ok' => true]);

    case 'admin.flags':
        only('GET');
        require_user(PERM_EDITOR);
        send_json(['ok' => true, 'flags' => admin_list_flags()]);

    // ---------- User management (permissions >= 9; administrators only by the master admin) ----------

    case 'admin.users':
        only('GET');
        $s = require_user(PERM_ADMIN);
        send_json(['ok' => true, 'users' => admin_list_users($s), 'levels' => PERMISSION_LEVELS, 'assignable' => ASSIGNABLE_LEVELS, 'mailEnabled' => mail_enabled()]);

    case 'admin.overview':
        only('GET');
        require_user(PERM_EDITOR);
        send_json(['ok' => true, 'overview' => admin_overview()]);

    case 'admin.user.stats':
        only('GET');
        require_user(PERM_ADMIN);
        send_json(['ok' => true, 'stats' => admin_user_stats((int)($_GET['id'] ?? 0))]);

    case 'admin.user.update':
        only('POST');
        $s = require_user(PERM_ADMIN);
        require_csrf($s);
        $u = admin_update_user($s, request_body());
        // "mail" is present only when an email was attempted (account enabled for the first time, or role changed)
        send_json(['ok' => true, 'user' => public_user($u), 'mail' => $GLOBALS['last_account_mail'] ?? null, 'mailEnabled' => mail_enabled()]);

    case 'admin.user.send_activation':
        only('POST');
        $s = require_user(PERM_ADMIN);
        require_csrf($s);
        admin_send_activation($s, (int)(request_body()['id'] ?? 0));
        send_json(['ok' => true]);

    case 'admin.user.send_activation_all':
        only('POST');
        $s = require_user(PERM_ADMIN);
        require_csrf($s);
        send_json(['ok' => true] + admin_send_activation_all($s));

    case 'admin.user.reset_password':
        only('POST');
        $s = require_user(PERM_ADMIN);
        require_csrf($s);
        $id = (int)(request_body()['id'] ?? 0);
        $pw = admin_reset_password($s, $id);
        // The temporary password is also emailed to the user (it only works until they choose a new one)
        $mail = mail_enabled() ? send_account_emails([find_user($id)], 'password_reset', ['temp_password' => $pw])[0] : null;
        send_json(['ok' => true, 'password' => $pw, 'mail' => $mail]);

    case 'admin.user.delete':
        only('POST');
        $s = require_user(PERM_ADMIN);
        require_csrf($s);
        admin_delete_user($s, (int)(request_body()['id'] ?? 0));
        send_json(['ok' => true, 'mail' => $GLOBALS['last_account_mail'] ?? null]);

    // ---------- Other useful documents (permissions >= 9) ----------

    case 'admin.documents':
        only('GET');
        require_user(PERM_ADMIN);
        send_json(['ok' => true, 'documents' => list_documents(true), 'maxBytes' => documents_max_bytes()]);

    case 'admin.document.save':
        only('POST');
        $s = require_user(PERM_ADMIN);
        require_csrf($s);
        if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            fail('Il PDF è troppo grande: il limite è ' . round(documents_max_bytes() / 1048576) . ' MB.', 413, 'too_large');
        }
        $doc = save_document($_POST, $_FILES['file'] ?? null, $s['id']);
        send_json(['ok' => true, 'document' => $doc]);

    case 'admin.document.delete':
        only('POST');
        $s = require_user(PERM_ADMIN);
        require_csrf($s);
        delete_document((int)(request_body()['id'] ?? 0));
        send_json(['ok' => true]);

    case 'admin.document.move':
        only('POST');
        $s = require_user(PERM_ADMIN);
        require_csrf($s);
        $b = request_body();
        move_document((int)($b['id'] ?? 0), (int)($b['dir'] ?? 0));
        send_json(['ok' => true]);

    case 'admin.user.transfer_master':
        only('POST');
        $s = require_user(PERM_MASTER);
        require_csrf($s);
        $u = admin_transfer_master($s, request_body());
        send_json(['ok' => true, 'user' => public_user($u), 'mail' => $GLOBALS['last_account_mail'] ?? null]);

    default:
        fail('Azione sconosciuta.', 404, 'not_found');
}
