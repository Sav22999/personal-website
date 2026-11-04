<?php
/**
 * Giftwise — guided database installation.
 *
 * Asks for the database credentials ($localhost_db, $username_db, $password_db, $database_name),
 * creates the tables (prefix "gw_") and writes credentials.php (git-ignored, blocked from the browser).
 *
 * Once installed the page locks itself: it can only be re-run (to repair or update the tables) by typing
 * the database password already saved in credentials.php. For safety, delete it from the server afterwards.
 */

declare(strict_types=1);

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/schema.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function credentials_php(string $host, string $user, string $pass, string $name, string $prefix, string $google = ''): string
{
    $q = function (string $v): string { return '"' . addcslashes($v, "\\\"\$") . '"'; };
    return "<?php\n"
        . "/**\n * Giftwise — database credentials. Created by install-db.php on " . gmdate('Y-m-d H:i') . " UTC.\n"
        . " * NEVER commit this file (it is listed in .gitignore) and never publish it.\n */\n\n"
        . '$localhost_db  = ' . $q($host) . ";\n"
        . '$username_db   = ' . $q($user) . ";\n"
        . '$password_db   = ' . $q($pass) . ";\n"
        . '$database_name = ' . $q($name) . ";\n\n"
        . '$table_prefix  = ' . $q($prefix) . ";\n\n"
        . "// Sign in with Google (optional): OAuth client ID of type \"Web application\"\n"
        . '$google_client_id = ' . $q($google) . ";\n\n"
        . "// Emails (optional): null = use the site's contact-form settings (/.mail-config.php), see credentials-sample.php\n"
        . "\$mail_config = null;\n\n"
        . "\$debug = false;\n";
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
$done = null;
$manualFile = null;
$mode = $locked ? 'update' : 'install';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $host = trim((string)($_POST['localhost_db'] ?? ''));
    $user = trim((string)($_POST['username_db'] ?? ''));
    $pass = (string)($_POST['password_db'] ?? '');
    $name = trim((string)($_POST['database_name'] ?? ''));
    $prefix = trim((string)($_POST['table_prefix'] ?? 'gw_')) ?: 'gw_';
    $google = trim((string)($_POST['google_client_id'] ?? ''));
    if ($google !== '' && !preg_match('/^[A-Za-z0-9\-_.]{3,200}\.apps\.googleusercontent\.com$/', $google)) $errors[] = 'Client ID di Google non valido: deve terminare con .apps.googleusercontent.com.';

    if ($mode === 'update') {
        $host = $existing['host']; $user = $existing['user']; $name = $existing['name']; $prefix = $existing['prefix'];
        if (!hash_equals($existing['pass'], $pass)) $errors[] = 'La password del database non corrisponde a quella salvata in credentials.php.';
    } else {
        if ($existing && !hash_equals($existing['pass'], $pass)) $errors[] = 'Esiste già un file credentials.php: per sovrascriverlo inserisci la stessa password del database salvata al suo interno (oppure elimina il file via FTP).';
        if ($host === '' || $user === '' || $name === '') $errors[] = 'Compila host, utente e nome del database.';
        if (!preg_match('/^[A-Za-z0-9_]{1,20}$/', $prefix)) $errors[] = 'Prefisso tabelle non valido (solo lettere, numeri e _).';
    }

    if (!$errors) {
        try {
            $pdo = pdo_connect($host, $user, $pass, $name);
        } catch (Throwable $e) {
            error_log('[giftwise] install: connection failed: ' . $e->getMessage());
            $errors[] = 'Connessione al database non riuscita: controlla host, utente, password e nome del database.';
        }
    }

    if (!$errors) {
        try {
            install_schema($pdo, $prefix);
            $write = $mode === 'install' || ($google !== '' && $google !== ($existing['google_client_id'] ?? ''));
            if ($write) {
                $content = credentials_php($host, $user, $pass, $name, $prefix, $google !== '' ? $google : (string)($existing['google_client_id'] ?? ''));
                if (@file_put_contents(credentials_file(), $content, LOCK_EX) === false) $manualFile = $content;
                else @chmod(credentials_file(), 0640);
            }
            $done = ['mode' => $mode];
        } catch (Throwable $e) {
            error_log('[giftwise] install: ' . get_class($e) . ': ' . $e->getMessage());
            $errors[] = 'Installazione non riuscita durante la creazione delle tabelle: ' . ($e instanceof PDOException ? 'errore del database (dettagli nel log del server).' : h($e->getMessage()));
        }
    }
}
?><!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Giftwise · Installazione</title>
<link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg">
<style>
  :root { --brand:#E84A7F; --brand-dark:#B4235A; --bg:#FBF6F7; --card:#fff; --text:#22191C; --muted:#6B5E63; --border:#EAD9DE; --red:#C62828; }
  @media (prefers-color-scheme: dark) { :root { --bg:#150F11; --card:#211A1C; --text:#F1E4E8; --muted:#B9A6AD; --border:#3A2D31; --brand-dark:#FFB1C8; } }
  * { box-sizing: border-box; }
  body { margin:0; padding:32px 16px; font:15px/1.55 system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background:var(--bg); color:var(--text); }
  main { max-width:560px; margin:0 auto; background:var(--card); border:1px solid var(--border); border-radius:20px; padding:28px; }
  .brand { display:flex; align-items:center; gap:10px; color:var(--brand-dark); font-weight:700; }
  .brand img { width:28px; height:28px; }
  h1 { margin:6px 0 4px; font-size:1.6rem; }
  h2 { font-size:1rem; margin:26px 0 4px; color:var(--brand-dark); }
  p.lead { color:var(--muted); margin:0 0 18px; }
  label { display:block; font-size:.82rem; font-weight:600; color:var(--muted); margin:12px 0 5px; }
  input { width:100%; padding:11px 12px; border:1px solid var(--border); border-radius:10px; font:inherit; background:var(--bg); color:var(--text); }
  input:focus { outline:2px solid var(--brand); border-color:transparent; }
  code { font-size:.88em; background:var(--bg); padding:1px 5px; border-radius:5px; }
  button { margin-top:24px; width:100%; padding:14px; border:0; border-radius:999px; background:var(--brand-dark); color:#fff; font:inherit; font-weight:700; cursor:pointer; }
  @media (prefers-color-scheme: dark) { button { color:#3E001D; } }
  .err { background:rgba(198,40,40,.08); border:1px solid var(--red); color:var(--red); border-radius:10px; padding:10px 12px; margin-bottom:10px; }
  .ok { background:rgba(232,74,127,.08); border:1px solid var(--brand); border-radius:10px; padding:12px 14px; }
  .hint { font-size:.84rem; color:var(--muted); margin-top:4px; }
  .row { display:grid; grid-template-columns:1fr 1fr; gap:0 12px; }
  @media (max-width:480px) { .row { grid-template-columns:1fr; } }
  pre { white-space:pre-wrap; word-break:break-all; background:var(--bg); padding:12px; border-radius:10px; font-size:.82rem; }
  a { color:var(--brand-dark); font-weight:600; }
</style>
</head>
<body>
<main>
  <div class="brand"><img src="assets/icons/favicon.svg" alt=""> Giftwise</div>
  <h1><?= $mode === 'update' && !$done ? 'Aggiorna il database' : 'Installazione' ?></h1>
  <p class="lead">Crea le tabelle di Giftwise (prefisso <code>gw_</code>) e salva le credenziali in <code>credentials.php</code>.</p>

  <?php foreach ($errors as $e): ?><div class="err"><?= h($e) ?></div><?php endforeach; ?>

  <?php if ($done): ?>
    <div class="ok"><strong><?= $done['mode'] === 'install' ? 'Installazione completata.' : 'Database aggiornato.' ?></strong><br>Tabelle create o verificate. Il primo account lo crei dall’app, con «Crea account».</div>
    <?php if ($manualFile): ?>
      <div class="err" style="margin-top:14px">Non è stato possibile scrivere <code>credentials.php</code> (permessi della cartella). Crealo tu via FTP nella cartella di Giftwise con questo contenuto:</div>
      <pre><?= h($manualFile) ?></pre>
    <?php endif; ?>
    <p><strong>Per sicurezza ora elimina <code>install-db.php</code> dal server</strong> (puoi ricaricarlo via FTP quando serve aggiornare il database).</p>
    <p><a href="./">Apri Giftwise →</a></p>

  <?php elseif ($mode === 'update'): ?>
    <div class="ok">Giftwise è già installato. Puoi ricontrollare le tabelle (non cancella nulla).</div>
    <form method="post" autocomplete="off">
      <label for="password_db">Password del database ($password_db)</label>
      <input id="password_db" name="password_db" type="password" required>
      <p class="hint">È la password salvata in <code>credentials.php</code>: serve a confermare che sei il proprietario.</p>
      <label for="google_client_id">Client ID di Google (facoltativo)</label>
      <input id="google_client_id" name="google_client_id" placeholder="1234567890-xxxx.apps.googleusercontent.com" value="<?= h($existing['google_client_id'] ?? '') ?>">
      <button type="submit">Verifica e aggiorna le tabelle</button>
    </form>

  <?php else: ?>
    <form method="post" autocomplete="off">
      <h2>1 · Database MySQL</h2>
      <p class="hint">Li trovi nel pannello del tuo hosting (su Aruba: Hosting Linux → Database MySQL). Puoi usare lo stesso database di FoodTrack: le tabelle hanno un prefisso diverso.</p>
      <label for="localhost_db">Host ($localhost_db)</label>
      <input id="localhost_db" name="localhost_db" required placeholder="es. 89.46.111.xxx" value="<?= h($_POST['localhost_db'] ?? '') ?>">
      <div class="row">
        <div><label for="username_db">Utente ($username_db)</label><input id="username_db" name="username_db" required value="<?= h($_POST['username_db'] ?? '') ?>"></div>
        <div><label for="database_name">Nome database ($database_name)</label><input id="database_name" name="database_name" required value="<?= h($_POST['database_name'] ?? '') ?>"></div>
      </div>
      <label for="password_db">Password ($password_db)</label>
      <input id="password_db" name="password_db" type="password" required>
      <label for="table_prefix">Prefisso tabelle</label>
      <input id="table_prefix" name="table_prefix" value="<?= h($_POST['table_prefix'] ?? 'gw_') ?>" pattern="[A-Za-z0-9_]{1,20}">
      <h2>2 · Accesso con Google (facoltativo)</h2>
      <label for="google_client_id">Client ID di Google</label>
      <input id="google_client_id" name="google_client_id" placeholder="1234567890-xxxx.apps.googleusercontent.com" value="<?= h($_POST['google_client_id'] ?? '') ?>">
      <p class="hint">Puoi usare lo stesso client «Applicazione web» di FoodTrack (lo stesso sito è già un’origine autorizzata). Se lo lasci vuoto puoi aggiungerlo dopo in <code>credentials.php</code>.</p>
      <button type="submit">Crea le tabelle e installa Giftwise</button>
    </form>
  <?php endif; ?>
</main>
</body>
</html>
