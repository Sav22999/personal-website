<?php
/**
 * Guided installation (to be used once).
 * Creates the tables, imports questions and references and creates the master admin.
 * After installation it locks itself; for safety, delete it from the server anyway.
 */

declare(strict_types=1);

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/schema.php';
require __DIR__ . '/lib/content.php';
require __DIR__ . '/lib/documents.php';

header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$errors = [];
$done = null;
$state = 'form';

try {
    config();
    db();
} catch (Throwable $e) {
    $state = 'config';
    $errors[] = $e instanceof PDOException
        ? 'Connessione al database non riuscita: controlla host, nome, utente e password in lib/config.php. (' . $e->getMessage() . ')'
        : $e->getMessage();
}

$adminExists = false;
if ($state === 'form') {
    try {
        $adminExists = (int)db()->query('SELECT COUNT(*) FROM ' . t('users') . ' WHERE permissions >= ' . PERM_MASTER . " AND status = 'active'")->fetchColumn() > 0;
    } catch (Throwable $e) {
        $adminExists = false; // tables not created yet
    }
    if ($adminExists) $state = 'locked';
}

if ($state === 'form' && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name  = trim((string)($_POST['name'] ?? ''));
    $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
    $pw    = (string)($_POST['password'] ?? '');
    $pw2   = (string)($_POST['password2'] ?? '');
    $dbpw  = (string)($_POST['dbpass'] ?? '');

    if (!hash_equals((string)config()['db']['pass'], $dbpw)) $errors[] = 'La password del database non corrisponde a quella in lib/config.php.';
    if (mb_strlen($name) < 2) $errors[] = 'Inserisci il tuo nome.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email non valida.';
    if (mb_strlen($pw) < 10) $errors[] = 'La password deve avere almeno 10 caratteri.';
    if ($pw !== $pw2) $errors[] = 'Le due password non coincidono.';

    if (!$errors) {
        try {
            foreach (schema_statements() as $sql) db()->exec($sql);
            ensure_schema(); // creates or updates the columns added in later versions

            $imported = null;
            $hasQuestions = (int)db()->query('SELECT COUNT(*) FROM ' . t('questions'))->fetchColumn() > 0;
            if (!$hasQuestions) {
                $seedFile = __DIR__ . '/install/seed.json';
                if (!is_file($seedFile)) throw new RuntimeException('File install/seed.json mancante.');
                $seed = json_decode((string)file_get_contents($seedFile), true);
                if (!is_array($seed) || !isset($seed['refs'], $seed['questions'])) throw new RuntimeException('install/seed.json non valido.');
                $imported = import_seed($seed);
            }

            $st = db()->prepare('SELECT id FROM ' . t('users') . ' WHERE email = ?');
            $st->execute([$email]);
            $existing = $st->fetchColumn();
            if ($existing) {
                db()->prepare('UPDATE ' . t('users') . " SET name = ?, password_hash = ?, permissions = ?, status = 'active', approved_at = ? WHERE id = ?")
                    ->execute([$name, password_hash($pw, PASSWORD_DEFAULT), PERM_MASTER, now_utc(), $existing]);
            } else {
                db()->prepare('INSERT INTO ' . t('users') . " (name, email, password_hash, permissions, status, created_at, approved_at) VALUES (?,?,?,?, 'active', ?, ?)")
                    ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT), PERM_MASTER, now_utc(), now_utc()]);
            }
            normalize_master_admin();
            $done = ['imported' => $imported, 'email' => $email];
            $state = 'done';
        } catch (Throwable $e) {
            $errors[] = 'Installazione non riuscita: ' . $e->getMessage();
        }
    }
}
?><!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex">
<title>Fischietto</title>
<style>
  :root { --navy:#1B2A4A; --gold:#D4953A; --surface:#F5F3F0; --raised:#fff; --text:#1B2A4A; --muted:#5A6578; --border:#D8D4CE; --red:#C44B3F; --green:#3A8F5C; }
  @media (prefers-color-scheme: dark) { :root { --surface:#0F1724; --raised:#1A2538; --text:#E8E5E0; --muted:#9AA3B4; --border:#2A3A52; } }
  * { box-sizing: border-box; }
  body { margin:0; padding:32px 16px; font:15px/1.55 system-ui, -apple-system, 'Segoe UI', sans-serif; background:var(--surface); color:var(--text); }
  main { max-width:520px; margin:0 auto; background:var(--raised); border:1px solid var(--border); border-radius:16px; padding:28px; }
  h1 { font-family: Georgia, serif; font-weight:400; margin:0 0 6px; }
  p.lead { color:var(--muted); margin:0 0 20px; }
  label { display:block; font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); margin:14px 0 6px; }
  input { width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font:inherit; background:var(--surface); color:var(--text); }
  input:focus { outline:2px solid var(--gold); border-color:transparent; }
  button { margin-top:22px; width:100%; padding:13px; border:0; border-radius:10px; background:var(--navy); color:#fff; font:inherit; font-weight:700; cursor:pointer; }
  .err { background:rgba(196,75,63,.1); border:1px solid var(--red); color:var(--red); border-radius:8px; padding:10px 12px; margin-bottom:10px; }
  .ok { background:rgba(58,143,92,.1); border:1px solid var(--green); border-radius:8px; padding:12px 14px; }
  .hint { font-size:.85rem; color:var(--muted); margin-top:4px; }
  a { color:var(--gold); }
  hr { border:0; border-top:1px solid var(--border); margin:22px 0 4px; }
</style>
</head>
<body>
<main>
  <h1>Installazione</h1>
  <p class="lead">Fischietto · configurazione iniziale del database</p>

  <?php foreach ($errors as $e): ?><div class="err"><?= h($e) ?></div><?php endforeach; ?>

  <?php if ($state === 'config'): ?>
    <p>Sistema la configurazione in <code>lib/config.php</code> e ricarica questa pagina.</p>

  <?php elseif ($state === 'locked'): ?>
    <div class="ok">L'installazione è già stata completata. Per sicurezza <strong>elimina il file <code>install.php</code></strong> dal server.</div>
    <p><a href="./">Vai al portale →</a></p>

  <?php elseif ($state === 'done'): ?>
    <div class="ok">
      <strong>Installazione completata.</strong><br>
      <?php if ($done['imported']): ?>
        Importati <?= (int)$done['imported']['questions'] ?> domande e <?= (int)$done['imported']['refs'] ?> riferimenti.<br>
      <?php else: ?>
        Domande e riferimenti erano già presenti: nessun import.<br>
      <?php endif; ?>
      Master admin: <?= h($done['email']) ?>
    </div>
    <p><strong>Ora elimina <code>install.php</code> e la cartella <code>install/</code> dal server.</strong></p>
    <p><a href="./">Vai al portale e accedi →</a></p>

  <?php else: ?>
    <form method="post" autocomplete="off">
      <label for="name">Nome e cognome</label>
      <input id="name" name="name" required value="<?= h($_POST['name'] ?? '') ?>">
      <label for="email">Email amministratore</label>
      <input id="email" name="email" type="email" required value="<?= h($_POST['email'] ?? '') ?>">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" minlength="10" required autocomplete="new-password">
      <label for="password2">Ripeti password</label>
      <input id="password2" name="password2" type="password" minlength="10" required autocomplete="new-password">
      <hr>
      <label for="dbpass">Password del database</label>
      <input id="dbpass" name="dbpass" type="password" required>
      <p class="hint">Conferma di essere il proprietario: è la stessa password scritta in <code>lib/config.php</code>.</p>
      <button type="submit">Crea tabelle, importa i dati e crea l'amministratore</button>
    </form>
  <?php endif; ?>
</main>
</body>
</html>
