<?php
/**
 * OpenNewsletter — guided database installation.
 *
 * Asks for the database credentials, the master admins and (optionally) the Google client ID, creates the tables
 * (prefix "on_") and writes credentials.php (git-ignored, blocked from the browser) with a new random secret_key for the
 * SMTP settings of the groups. Once installed the page locks itself: it can only be re-run (to repair the tables or
 * change the master admins / Google client ID) by typing the database password already saved in credentials.php;
 * the secret_key is always kept. For safety, delete it from the server afterwards.
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

function credentials_php(string $host, string $user, string $pass, string $name, string $prefix, string $secret, array $masters, string $google): string
{
    $q = fn(string $v): string => '"' . addcslashes($v, "\\\"\$") . '"';
    return "<?php\n/**\n * OpenNewsletter — credentials. Created by install-db.php on " . gmdate('Y-m-d H:i') . " UTC.\n"
        . " * NEVER commit this file (it is listed in .gitignore) and never publish it.\n */\n\n"
        . '$localhost_db  = ' . $q($host) . ";\n" . '$username_db   = ' . $q($user) . ";\n"
        . '$password_db   = ' . $q($pass) . ";\n" . '$database_name = ' . $q($name) . ";\n\n"
        . '$table_prefix  = ' . $q($prefix) . ";\n\n"
        . "// Encrypts the SMTP settings of the groups: NEVER change it, or their passwords can no longer be read\n"
        . '$secret_key = ' . $q($secret) . ";\n\n"
        . "// Master admins of the whole platform (account emails, once confirmed)\n"
        . '$master_emails = [' . implode(', ', array_map($q, $masters)) . "];\n\n"
        . "// Sign in with Google (optional): OAuth client ID of type \"Web application\"\n"
        . '$google_client_id = ' . $q($google) . ";\n\n"
        . "// null: the site's contact-form SMTP settings (/.mail-config.php) send the platform's emails\n\$mail_config = null;\n\n"
        . "\$default_timezone = \"Europe/Rome\";\n\n\$debug = false;\n";
}

$existing = credentials();
$locked = false;
if ($existing) {
    try {
        $pdo = pdo_connect($existing['host'], $existing['user'], $existing['pass'], $existing['name']);
        $pdo->query("SELECT 1 FROM `{$existing['prefix']}users` LIMIT 1");
        $locked = true;
    } catch (Throwable $e) {
        $locked = false; // tables missing or wrong credentials: the installer can be used again
    }
}

$errors = [];
$done = false;
$manualFile = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $pass = (string)($_POST['password_db'] ?? '');
    $host = $locked ? $existing['host'] : trim((string)($_POST['localhost_db'] ?? ''));
    $user = $locked ? $existing['user'] : trim((string)($_POST['username_db'] ?? ''));
    $name = $locked ? $existing['name'] : trim((string)($_POST['database_name'] ?? ''));
    $prefix = $locked ? $existing['prefix'] : (trim((string)($_POST['table_prefix'] ?? 'on_')) ?: 'on_');
    $google = trim((string)($_POST['google_client_id'] ?? ''));
    $masters = array_values(array_unique(array_filter(array_map(fn($e) => mb_strtolower(trim($e)), preg_split('/[\s,;]+/', (string)($_POST['master_emails'] ?? '')) ?: []))));
    $secret = $existing && preg_match('/^[a-f0-9]{64}$/i', $existing['secret_key']) ? $existing['secret_key'] : bin2hex(random_bytes(32));

    if ($existing && !hash_equals($existing['pass'], $pass)) $errors[] = 'La password del database non corrisponde a quella salvata in credentials.php.';
    if ($google !== '' && !preg_match('/^[A-Za-z0-9\-_.]{3,200}\.apps\.googleusercontent\.com$/', $google)) $errors[] = 'Client ID di Google non valido.';
    foreach ($masters as $m) if (!filter_var($m, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email master non valida: ' . $m;
    if (!$locked) {
        if ($host === '' || $user === '' || $name === '') $errors[] = 'Compila host, utente e nome del database.';
        if (!preg_match('/^[A-Za-z0-9_]{1,20}$/', $prefix)) $errors[] = 'Prefisso tabelle non valido (solo lettere, numeri e _).';
    }

    if (!$errors) {
        try {
            $pdo = pdo_connect($host, $user, $pass, $name);
            install_schema($pdo, $prefix);
            $content = credentials_php($host, $user, $pass, $name, $prefix, $secret, $masters, $google);
            if (@file_put_contents(credentials_file(), $content, LOCK_EX) === false) $manualFile = $content;
            else @chmod(credentials_file(), 0640);
            $done = true;
        } catch (Throwable $e) {
            error_log('[opennewsletter] install: ' . get_class($e) . ': ' . $e->getMessage());
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
    <title>Installazione · OpenNewsletter</title>
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/app.css">
    <style>
        main { max-width: 560px; margin: 0 auto; padding: 32px 16px; }
        fieldset { border: 1px solid var(--line); border-radius: 14px; margin: 0 0 14px; padding: 14px; display: flex; flex-direction: column; gap: 10px; background: var(--surface); }
        legend { font-weight: 700; padding: 0 6px; }
        .err { color: var(--danger); font-weight: 700; }
        pre { overflow: auto; padding: 12px; background: var(--surface-2); border-radius: 10px; font-size: 12px; }
    </style>
</head>
<body>
<main>
    <h1><img src="icons/favicon.svg" width="34" height="34" alt="" style="vertical-align:-6px"> OpenNewsletter</h1>
    <?php if ($done): ?>
        <p>Fatto: le tabelle sono pronte. Per sicurezza elimina <code>install-db.php</code> dal server.</p>
        <p>Ricorda di programmare <code>projects/cron/opennewsletter-schedule.php</code> e <code>opennewsletter-reminders.php</code> ogni 30 minuti (vedi il README di <code>projects/cron</code>).</p>
        <?php if ($manualFile !== null): ?>
            <p class="err">Non è stato possibile scrivere <code>credentials.php</code>: crealo a mano con questo contenuto.</p>
            <pre><?= h($manualFile) ?></pre>
        <?php endif; ?>
        <p><a class="btn btn-primary" href="./">Apri OpenNewsletter</a></p>
    <?php else: ?>
        <p><?= $locked ? 'OpenNewsletter è già installato: per riparare le tabelle o cambiare gli amministratori master e il Client ID di Google inserisci la password del database. La chiave di cifratura resta quella attuale.' : 'Inserisci i dati del database MySQL. Va bene anche un database già usato da altre app: le tabelle hanno il prefisso on_.' ?></p>
        <?php foreach ($errors as $e): ?><p class="err"><?= h($e) ?></p><?php endforeach; ?>
        <form method="post" autocomplete="off">
            <fieldset>
                <legend>Database</legend>
                <?php if (!$locked): ?>
                    <label class="field"><span class="field-label">Host</span><input class="input" name="localhost_db" value="<?= h($_POST['localhost_db'] ?? 'localhost') ?>" required></label>
                    <label class="field"><span class="field-label">Utente</span><input class="input" name="username_db" value="<?= h($_POST['username_db'] ?? '') ?>" required></label>
                    <label class="field"><span class="field-label">Nome del database</span><input class="input" name="database_name" value="<?= h($_POST['database_name'] ?? '') ?>" required></label>
                    <label class="field"><span class="field-label">Prefisso tabelle</span><input class="input" name="table_prefix" value="<?= h($_POST['table_prefix'] ?? 'on_') ?>"></label>
                <?php endif; ?>
                <label class="field"><span class="field-label">Password del database</span><input class="input" type="password" name="password_db"></label>
            </fieldset>
            <fieldset>
                <legend>Amministratori master</legend>
                <label class="field"><span class="field-label">Email (separate da virgole)</span><input class="input" name="master_emails" value="<?= h($_POST['master_emails'] ?? implode(', ', $existing['master_emails'] ?? [])) ?>"></label>
                <span class="field-hint">Vedono tutti i gruppi (mai le impostazioni SMTP). Valgono quando l’account con quell’email è confermato.</span>
            </fieldset>
            <fieldset>
                <legend>Accesso con Google (facoltativo)</legend>
                <label class="field"><span class="field-label">Client ID «Applicazione web»</span><input class="input" name="google_client_id" value="<?= h($_POST['google_client_id'] ?? ($existing['google_client_id'] ?? '')) ?>"></label>
            </fieldset>
            <button class="btn btn-primary" type="submit"><?= $locked ? 'Aggiorna' : 'Installa' ?></button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
