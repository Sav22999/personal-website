<?php
/**
 * Forkville — guided database installation.
 *
 * Asks for the database credentials and the first administrator, creates the tables (prefix "fv_") and writes
 * credentials.php (git-ignored, blocked from the browser).
 *
 * Once installed the page locks itself: it can only be re-run (to repair the tables) by typing the database
 * password already saved in credentials.php. For safety, delete it from the server afterwards.
 */

declare(strict_types=1);

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/schema.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function credentials_php(string $host, string $user, string $pass, string $name, string $prefix, string $google): string
{
    $q = fn(string $v): string => '"' . addcslashes($v, "\\\"\$") . '"';
    return "<?php\n/**\n * Forkville — database credentials. Created by install-db.php on " . gmdate('Y-m-d H:i') . " UTC.\n"
        . " * NEVER commit this file (it is listed in .gitignore) and never publish it.\n */\n\n"
        . '$localhost_db  = ' . $q($host) . ";\n" . '$username_db   = ' . $q($user) . ";\n"
        . '$password_db   = ' . $q($pass) . ";\n" . '$database_name = ' . $q($name) . ";\n\n"
        . '$table_prefix  = ' . $q($prefix) . ";\n\n"
        . "// Sign in with Google (optional): OAuth client ID of type \"Web application\"\n"
        . '$google_client_id = ' . $q($google) . ";\n\n\$debug = false;\n";
}

$existing = credentials();
$locked = false;
$connected = false; // credentials.php works: only the tables and the first administrator are missing
if ($existing) {
    try {
        $pdo = pdo_connect($existing['host'], $existing['user'], $existing['pass'], $existing['name']);
        $connected = true;
        // The tables may have been created by the API: the page stays open until there is an administrator
        $locked = (int)$pdo->query("SELECT COUNT(*) FROM `{$existing['prefix']}users` WHERE role >= " . ROLE_ADMIN)->fetchColumn() > 0;
    } catch (Throwable $e) {
        $locked = false; // tables missing or wrong credentials: the installer can be used again
    }
}

$errors = [];
$done = false;
$manualFile = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $pass = (string)($_POST['password_db'] ?? '');
    $host = $connected ? $existing['host'] : trim((string)($_POST['localhost_db'] ?? ''));
    $user = $connected ? $existing['user'] : trim((string)($_POST['username_db'] ?? ''));
    $name = $connected ? $existing['name'] : trim((string)($_POST['database_name'] ?? ''));
    $prefix = $connected ? $existing['prefix'] : (trim((string)($_POST['table_prefix'] ?? 'fv_')) ?: 'fv_');
    $google = trim((string)($_POST['google_client_id'] ?? ''));
    $adminName = trim((string)($_POST['admin_name'] ?? ''));
    $adminUsername = trim((string)($_POST['admin_username'] ?? ''));
    $adminEmail = mb_strtolower(trim((string)($_POST['admin_email'] ?? '')));
    $adminPw = (string)($_POST['admin_password'] ?? '');

    if ($existing && !hash_equals($existing['pass'], $pass)) $errors[] = 'La password del database non corrisponde a quella salvata in credentials.php.';
    if ($google !== '' && !preg_match('/^[A-Za-z0-9\-_.]{3,200}\.apps\.googleusercontent\.com$/', $google)) $errors[] = 'Client ID di Google non valido.';
    if (!$locked) {
        if ($host === '' || $user === '' || $name === '') $errors[] = 'Compila host, utente e nome del database.';
        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $adminUsername)) $errors[] = 'Username non valido: 3–20 lettere, numeri o _.';
        if (!preg_match('/^[A-Za-z0-9_]{1,20}$/', $prefix)) $errors[] = 'Prefisso tabelle non valido (solo lettere, numeri e _).';
        if (mb_strlen($adminName) < 2 || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL) || mb_strlen($adminPw) < 8) $errors[] = 'Inserisci nome, email e password (min. 8 caratteri) del primo amministratore.';
    }

    if (!$errors) {
        try {
            $pdo = pdo_connect($host, $user, $pass, $name);
            install_schema($pdo, $prefix);
            if (!$locked) {
                $pdo->prepare("INSERT INTO `{$prefix}users` (name, username, email, password_hash, role, created_at, email_verified_at) VALUES (?,?,?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE role = VALUES(role), password_hash = VALUES(password_hash), email_verified_at = COALESCE(email_verified_at, VALUES(email_verified_at))")
                    ->execute([$adminName, $adminUsername, $adminEmail, password_hash($adminPw, PASSWORD_DEFAULT), ROLE_ADMIN, now_utc(), now_utc()]);
            }
            if (!$locked || ($google !== '' && $google !== $existing['google_client_id'])) {
                $content = credentials_php($host, $user, $pass, $name, $prefix, $google !== '' ? $google : (string)($existing['google_client_id'] ?? ''));
                if (@file_put_contents(credentials_file(), $content, LOCK_EX) === false) $manualFile = $content;
                else @chmod(credentials_file(), 0640);
            }
            $done = true;
        } catch (Throwable $e) {
            error_log('[forkville] install: ' . get_class($e) . ': ' . $e->getMessage());
            $errors[] = 'Installazione non riuscita: controlla host, utente, password e nome del database (dettagli nel log del server).';
        }
    }
}
?><!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Installazione · Forkville</title>
    <link rel="stylesheet" href="assets/forkville.css">
    <style>
        main { max-width: 520px; margin: 0 auto; padding: 32px 16px; }
        input { width: 100%; height: 44px; padding: 0 12px; border: 1px solid var(--line); border-radius: 11px; background: var(--surface); }
        fieldset { border: 1px solid var(--line); border-radius: 14px; margin: 0 0 14px; display: flex; flex-direction: column; gap: 10px; }
        .err { color: var(--wrong); }
        pre { overflow: auto; padding: 12px; background: var(--surface-2); border-radius: 10px; }
    </style>
</head>
<body>
<main>
    <h1>Forkville</h1>
    <?php if ($done): ?>
        <p>Fatto: le tabelle sono pronte. Per sicurezza elimina <code>install-db.php</code> dal server.</p>
        <?php if ($manualFile !== null): ?>
            <p class="err">Non è stato possibile scrivere <code>credentials.php</code>: crealo a mano con questo contenuto.</p>
            <pre><?= h($manualFile) ?></pre>
        <?php endif; ?>
        <p><a class="btn btn-primary" href="./">Apri il gioco</a></p>
    <?php else: ?>
        <p><?= $locked ? 'Forkville è già installato: per riparare le tabelle inserisci la password del database.' : ($connected ? 'Il database è collegato: crea il primo amministratore. Per confermare inserisci la password del database salvata in credentials.php.' : 'Inserisci i dati del database MySQL e crea il primo amministratore. Va bene anche un database già usato da altre app: le tabelle hanno il prefisso fv_.') ?></p>
        <?php foreach ($errors as $e): ?><p class="err"><?= h($e) ?></p><?php endforeach; ?>
        <form method="post" class="form" autocomplete="off">
            <fieldset>
                <legend>Database</legend>
                <?php if (!$connected): ?>
                    <label class="field"><span>Host</span><input name="localhost_db" value="<?= h($_POST['localhost_db'] ?? 'localhost') ?>" required></label>
                    <label class="field"><span>Utente</span><input name="username_db" value="<?= h($_POST['username_db'] ?? '') ?>" required></label>
                    <label class="field"><span>Nome del database</span><input name="database_name" value="<?= h($_POST['database_name'] ?? '') ?>" required></label>
                    <label class="field"><span>Prefisso tabelle</span><input name="table_prefix" value="<?= h($_POST['table_prefix'] ?? 'fv_') ?>"></label>
                <?php endif; ?>
                <label class="field"><span>Password del database</span><input type="password" name="password_db"></label>
            </fieldset>
            <?php if (!$locked): ?>
                <fieldset>
                    <legend>Primo amministratore</legend>
                    <label class="field"><span>Nome</span><input name="admin_name" value="<?= h($_POST['admin_name'] ?? '') ?>" required></label>
                    <label class="field"><span>Username pubblico</span><input name="admin_username" value="<?= h($_POST['admin_username'] ?? '') ?>" required></label>
                    <label class="field"><span>Email</span><input type="email" name="admin_email" value="<?= h($_POST['admin_email'] ?? '') ?>" required></label>
                    <label class="field"><span>Password (min. 8 caratteri)</span><input type="password" name="admin_password" required></label>
                </fieldset>
            <?php endif; ?>
            <fieldset>
                <legend>Accesso con Google (facoltativo)</legend>
                <label class="field"><span>Client ID «Applicazione web»</span><input name="google_client_id" value="<?= h($_POST['google_client_id'] ?? ($existing['google_client_id'] ?? '')) ?>"></label>
            </fieldset>
            <button class="btn btn-primary" type="submit"><?= $locked ? 'Ripara le tabelle' : 'Installa' ?></button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
