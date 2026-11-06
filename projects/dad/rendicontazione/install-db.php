<?php
/**
 * Rendicontazione Ore — Installatore e configuratore database.
 * Crea le tabelle (con prefisso dad_rend_), configura le credenziali e
 * inizializza il Master Admin con il caricamento dei dati 2026.
 */

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/schema.php';
require_once __DIR__ . '/lib/models.php';
require_once __DIR__ . '/lib/seed-2026.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$existing = credentials();
$pdo = null;
$dbConnected = false;
$isSqlite = false;
$installed = false;

try {
    $pdo = db();
    $dbConnected = true;
    $isSqlite = is_sqlite($pdo);
    $installed = (int)$pdo->query('SELECT COUNT(*) FROM ' . t('users') . ' WHERE role >= ' . ROLE_MASTER)->fetchColumn() > 0;
} catch (Throwable $e) {
    $dbConnected = false;
}

$message = null;
$error = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'seed') {
        try {
            $pdo = db();
            $master = $pdo->query('SELECT id FROM ' . t('users') . ' WHERE role >= ' . ROLE_MASTER . ' LIMIT 1')->fetch();
            if (!$master) {
                $masterId = user_create([
                    'name' => 'Saverio Morelli',
                    'email' => config_value('master_admin_email', 'saverio.morelli@gmail.com'),
                    'role' => ROLE_MASTER,
                    'status' => STATUS_ACTIVE,
                ]);
            } else {
                $masterId = (int)$master['id'];
            }
            $res = seed_father_timesheet($masterId);
            $message = 'Dati 2026 caricati con successo: ' . ($res['days_count'] ?? 0) . ' giornate e ' . ($res['shifts_count'] ?? 0) . ' turni.';
        } catch (Throwable $e) {
            $error = 'Errore durante il caricamento dati: ' . $e->getMessage();
        }
    } elseif ($action === 'save_credentials') {
        $host = trim((string)($_POST['localhost_db'] ?? ''));
        $user = trim((string)($_POST['username_db'] ?? ''));
        $pass = (string)($_POST['password_db'] ?? '');
        $name = trim((string)($_POST['database_name'] ?? ''));
        $prefix = trim((string)($_POST['table_prefix'] ?? 'dad_rend_')) ?: 'dad_rend_';
        $google = trim((string)($_POST['google_client_id'] ?? ''));
        $adminEmail = mb_strtolower(trim((string)($_POST['master_admin_email'] ?? '')));

        if ($host === '' || $user === '' || $name === '') {
            $error = 'Compila tutti i campi obbligatori del database MySQL.';
        } else {
            $code = "<?php\n"
                . "/**\n * Rendicontazione Ore — database credentials.\n */\n\n"
                . '$localhost_db  = ' . var_export($host, true) . ";\n"
                . '$username_db   = ' . var_export($user, true) . ";\n"
                . '$password_db   = ' . var_export($pass, true) . ";\n"
                . '$database_name = ' . var_export($name, true) . ";\n\n"
                . '$table_prefix  = ' . var_export($prefix, true) . ";\n\n"
                . '$google_client_id = ' . var_export($google, true) . ";\n"
                . '$master_admin_email = ' . var_export($adminEmail, true) . ";\n\n"
                . "\$debug = false;\n";

            if (@file_put_contents(credentials_file(), $code)) {
                $message = 'Credenziali salvate con successo!';
                // Reconnect
                try {
                    $pdo = db(null);
                    ensure_schema($pdo);
                    $dbConnected = true;
                    $isSqlite = is_sqlite($pdo);
                } catch (Throwable $e) {
                    $error = 'Credenziali salvate, ma errore di connessione: ' . $e->getMessage();
                }
            } else {
                $error = 'Impossibile scrivere il file credentials.php. Verifica i permessi di scrittura.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Installazione &middot; Rendicontazione Ore</title>
    <link rel="stylesheet" href="assets/app.css">
    <style>
        .install-box { max-width: 680px; margin: 40px auto; padding: 32px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); }
        .form-row { margin-bottom: 20px; }
        .form-row label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; color: var(--text); }
        .form-row input, .form-row select { width: 100%; padding: 12px 14px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text); font-size: 1rem; }
        .badge-status { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .badge-success { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
        .badge-warning { background: rgba(234, 179, 8, 0.15); color: #eab308; }
        .badge-info { background: rgba(102, 203, 255, 0.15); color: var(--accent); }
    </style>
</head>
<body>
    <div class="install-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; border-bottom:1px solid var(--border); padding-bottom:16px;">
            <div>
                <h1 style="font-size:1.6rem; margin-bottom:4px;">Rendicontazione Ore</h1>
                <p style="color:var(--text-muted); font-size:0.95rem;">Installazione e Stato Database</p>
            </div>
            <div>
                <?php if ($dbConnected): ?>
                    <span class="badge-status badge-success">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <?= $isSqlite ? 'SQLite Locale (Attivo)' : 'MySQL Aruba (Attivo)' ?>
                    </span>
                <?php else: ?>
                    <span class="badge-status badge-warning">Non Connesso</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($message): ?>
            <div style="padding:14px; background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#22c55e; border-radius:8px; margin-bottom:20px;">
                <?= h($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="padding:14px; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#ef4444; border-radius:8px; margin-bottom:20px;">
                <?= h($error) ?>
            </div>
        <?php endif; ?>

        <div style="background:var(--bg-elevated); padding:20px; border-radius:8px; margin-bottom:24px; font-size:0.95rem;">
            <p style="margin-bottom:8px;"><strong>Stato installazione:</strong></p>
            <ul style="padding-left:20px; color:var(--text-secondary); line-height:1.7;">
                <li>Motore database: <strong><?= $isSqlite ? 'SQLite (data/rendicontazione.sqlite)' : 'MySQL (' . h(config_value('host')) . ')' ?></strong></li>
                <li>Prefisso tabelle: <code><?= h(config_value('prefix', 'dad_rend_')) ?></code></li>
                <li>Google Client ID: <code><?= h(substr((string)config_value('google_client_id'), 0, 25)) ?>...</code></li>
                <li>Email Master Admin: <strong><?= h(config_value('master_admin_email')) ?></strong></li>
            </ul>
        </div>

        <?php if ($dbConnected): ?>
            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:30px;">
                <a href="./" class="btn btn-primary" style="flex:1; text-align:center;">Vai al Portale Ore &rarr;</a>
                <form method="post" style="flex:1;">
                    <input type="hidden" name="action" value="seed">
                    <button type="submit" class="btn btn-secondary" style="width:100%;">
                        Ricarica / Sincronizza Dati 2026 (CSV)
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <details style="border-top:1px solid var(--border); padding-top:20px;">
            <summary style="cursor:pointer; font-weight:600; color:var(--text-secondary); margin-bottom:16px;">
                Modifica impostazioni MySQL / credentials.php
            </summary>
            <form method="post">
                <input type="hidden" name="action" value="save_credentials">
                <div class="form-row">
                    <label>Host MySQL (es. Aruba IP o localhost)</label>
                    <input type="text" name="localhost_db" value="<?= h(config_value('host', '89.46.111.125')) ?>" required>
                </div>
                <div class="form-row">
                    <label>Utente Database</label>
                    <input type="text" name="username_db" value="<?= h(config_value('user', 'Sql1362232')) ?>" required>
                </div>
                <div class="form-row">
                    <label>Password Database</label>
                    <input type="password" name="password_db" value="<?= h(config_value('pass', '')) ?>">
                </div>
                <div class="form-row">
                    <label>Nome Database</label>
                    <input type="text" name="database_name" value="<?= h(config_value('name', 'Sql1362232_4')) ?>" required>
                </div>
                <div class="form-row">
                    <label>Prefisso Tabelle</label>
                    <input type="text" name="table_prefix" value="<?= h(config_value('prefix', 'dad_rend_')) ?>" required>
                </div>
                <div class="form-row">
                    <label>Google Client ID</label>
                    <input type="text" name="google_client_id" value="<?= h(config_value('google_client_id')) ?>">
                </div>
                <div class="form-row">
                    <label>Email Master Admin</label>
                    <input type="email" name="master_admin_email" value="<?= h(config_value('master_admin_email', 'saverio.morelli@gmail.com')) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Salva Impostazioni</button>
            </form>
        </details>
    </div>
</body>
</html>
