<?php
/**
 * FoodTrack — automatic database update using the credentials already configured in credentials.php.
 *
 * Creates any missing tables (e.g. ft_history), runs pending schema migrations, and loads any new
 * initial categories, products, recipes, or translations without requiring manual password entry.
 */

declare(strict_types=1);

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/schema.php';
require __DIR__ . '/lib/seed.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');

function h_esc($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$cfg = credentials();
$error = null;
$result = null;

if (!$cfg) {
    $error = 'File credentials.php non trovato o incompleto. Esegui prima install-db.php.';
} else {
    try {
        $pdo = pdo_connect($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name']);
        db($pdo);
        $prefix = $cfg['prefix'];

        $fromVersion = 0;
        try {
            $fromVersion = (int)$pdo->query("SELECT v FROM `{$prefix}meta` WHERE k = 'schema_version'")->fetchColumn();
        } catch (Throwable $e) {
            $fromVersion = 0;
        }

        install_schema($pdo, $prefix);

        $adminId = null;
        try {
            $admin = $pdo->query("SELECT id FROM `{$prefix}users` WHERE role >= " . ROLE_MASTER . " ORDER BY id LIMIT 1")->fetchColumn();
            $adminId = $admin ? (int)$admin : null;
        } catch (Throwable $e) {
            $adminId = null;
        }

        $added = seed_database($pdo, $prefix, $adminId);
        normalize_master_admin();

        $tables = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($prefix . '%'))->fetchAll(PDO::FETCH_COLUMN);

        $result = [
            'ok' => true,
            'fromVersion' => $fromVersion,
            'toVersion' => SCHEMA_VERSION,
            'added' => $added,
            'tables' => $tables,
        ];
    } catch (Throwable $e) {
        error_log('[foodtrack] update.php: ' . get_class($e) . ': ' . $e->getMessage());
        $error = 'Aggiornamento non riuscito: ' . $e->getMessage();
    }
}

$wantsJson = (($_GET['format'] ?? '') === 'json')
    || (stripos((string)($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') !== false && stripos((string)($_SERVER['HTTP_ACCEPT'] ?? ''), 'text/html') === false);

if ($wantsJson) {
    if ($error) send_json(['ok' => false, 'message' => $error], 500);
    send_json($result);
}

if (PHP_SAPI === 'cli') {
    if ($error) {
        fwrite(STDERR, "[FoodTrack] ERRORE: {$error}\n");
        exit(1);
    }
    echo "[FoodTrack] Database aggiornato con successo (schema v{$result['fromVersion']} -> v{$result['toVersion']}).\n";
    echo "Tabelle (" . count($result['tables']) . "): " . implode(', ', $result['tables']) . "\n";
    echo "Dati aggiunti: {$result['added']['categories']} categorie, {$result['added']['products']} prodotti, {$result['added']['recipes']} ricette.\n";
    exit(0);
}
?><!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>FoodTrack · Aggiornamento database</title>
<link rel="icon" type="image/png" sizes="32x32" href="assets/icons/favicon-32.png">
<link rel="icon" type="image/png" sizes="192x192" href="assets/icons/icon-192.png">
<link rel="apple-touch-icon" href="assets/icons/apple-touch-icon.png">
<style>
  :root { --green:#4CAF50; --green-dark:#2E7D32; --bg:#F3F6F3; --card:#fff; --text:#1C1B1F; --muted:#5F6368; --border:#DADCE0; --red:#C62828; }
  @media (prefers-color-scheme: dark) { :root { --bg:#121412; --card:#1E211E; --text:#E6E1E5; --muted:#A8ADA8; --border:#353935; } }
  * { box-sizing: border-box; }
  body { margin:0; padding:32px 16px; font:15px/1.55 system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background:var(--bg); color:var(--text); }
  main { max-width:560px; margin:0 auto; background:var(--card); border:1px solid var(--border); border-radius:20px; padding:28px; }
  .brand { display:flex; align-items:center; gap:10px; color:var(--green-dark); font-weight:700; }
  .brand img { width:28px; height:28px; }
  @media (prefers-color-scheme: dark) { .brand { color:#81C784; } }
  h1 { margin:8px 0 6px; font-size:1.5rem; }
  p.lead { color:var(--muted); margin:0 0 18px; }
  code { font-size:.88em; background:var(--bg); padding:2px 6px; border-radius:5px; }
  .err { background:rgba(198,40,40,.08); border:1px solid var(--red); color:var(--red); border-radius:12px; padding:12px 14px; margin-bottom:14px; }
  .ok { background:rgba(76,175,80,.1); border:1px solid var(--green); border-radius:12px; padding:14px 16px; margin-bottom:16px; }
  .tables { display:flex; flex-wrap:wrap; gap:6px; margin:12px 0 20px; }
  .btn-row { display:flex; gap:12px; flex-wrap:wrap; margin-top:20px; }
  .btn { display:inline-flex; align-items:center; justify-content:center; padding:11px 20px; border-radius:999px; background:var(--green); color:#fff; text-decoration:none; font-weight:700; }
  .btn.sec { background:var(--bg); color:var(--text); border:1px solid var(--border); }
</style>
</head>
<body>
<main>
  <div class="brand"><img src="assets/icons/icon-color.png" alt=""> FoodTrack</div>
  <h1>Aggiornamento database</h1>
  <p class="lead">Verifica e aggiorna automaticamente tutte le tabelle usando le credenziali configurate in <code>credentials.php</code>.</p>

  <?php if ($error): ?>
    <div class="err"><?= h_esc($error) ?></div>
  <?php else: ?>
    <div class="ok">
      <strong>Database aggiornato con successo.</strong><br>
      Versione schema: <code>v<?= (int)$result['fromVersion'] ?></code> → <code>v<?= (int)$result['toVersion'] ?></code><br>
      Dati aggiunti: <?= (int)$result['added']['categories'] ?> categorie, <?= (int)$result['added']['products'] ?> prodotti, <?= (int)$result['added']['recipes'] ?> ricette.
    </div>
    <div>
      <strong>Tabelle verificate (<?= count($result['tables']) ?>):</strong>
      <div class="tables">
        <?php foreach ($result['tables'] as $tb): ?><code><?= h_esc($tb) ?></code><?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="btn-row">
    <a class="btn" href="./">Apri FoodTrack →</a>
    <a class="btn sec" href="admin.html">Portale admin →</a>
  </div>
</main>
</body>
</html>
