<?php
/**
 * Fucina — automatic database update using the credentials already configured in credentials.php.
 *
 * Creates any missing tables and runs pending schema migrations without requiring manual password entry.
 */

declare(strict_types=1);

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/schema.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');

function h_esc($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

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

        $tables = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($prefix . '%'))->fetchAll(PDO::FETCH_COLUMN);

        $result = [
                'ok' => true,
                'fromVersion' => $fromVersion,
                'toVersion' => SCHEMA_VERSION,
                'tables' => $tables,
        ];
    } catch (Throwable $e) {
        error_log('[fucina] update.php: ' . get_class($e) . ': ' . $e->getMessage());
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
        fwrite(STDERR, "[Fucina] ERRORE: {$error}\n");
        exit(1);
    }
    echo "[Fucina] Database aggiornato con successo (schema v{$result['fromVersion']} -> v{$result['toVersion']}).\n";
    echo "Tabelle (" . count($result['tables']) . "): " . implode(', ', $result['tables']) . "\n";
    exit(0);
}
?><!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Fucina · Aggiornamento database</title>
    <link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg">
    <style>
        :root {
            --brand: #FF5B2E;
            --brand-dark: #D93A14;
            --bg: #FFF6EA;
            --card: #fff;
            --text: #1B1B4B;
            --muted: #6A5F55;
            --border: #D9CCBA;
            --red: #C62828;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #12113A;
                --card: #1E1C52;
                --text: #F4F2FF;
                --muted: #B9A6AD;
                --border: #3A2D31;
                --brand-dark: #FFB1C8;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 16px;
            font: 15px/1.55 system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        main {
            max-width: 560px;
            margin: 0 auto;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--brand-dark);
            font-weight: 700;
        }

        .brand img {
            width: 28px;
            height: 28px;
        }

        h1 {
            margin: 8px 0 6px;
            font-size: 1.5rem;
        }

        p.lead {
            color: var(--muted);
            margin: 0 0 18px;
        }

        code {
            font-size: .88em;
            background: var(--bg);
            padding: 2px 6px;
            border-radius: 5px;
        }

        .err {
            background: rgba(198, 40, 40, .08);
            border: 1px solid var(--red);
            color: var(--red);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 14px;
        }

        .ok {
            background: rgba(232, 74, 127, .1);
            border: 1px solid var(--brand);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }

        .tables {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 12px 0 20px;
        }

        .btn-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 20px;
            border-radius: 999px;
            background: var(--brand);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
<main>
    <div class="brand"><img src="assets/icons/favicon.svg" alt=""> Fucina</div>
    <h1>Aggiornamento database</h1>
    <p class="lead">Verifica e aggiorna automaticamente tutte le tabelle usando le credenziali configurate in <code>credentials.php</code>.
    </p>

    <?php if ($error): ?>
        <div class="err"><?= h_esc($error) ?></div>
    <?php else: ?>
        <div class="ok">
            <strong>Database aggiornato con successo.</strong><br>
            Versione schema: <code>v<?= (int)$result['fromVersion'] ?></code> →
            <code>v<?= (int)$result['toVersion'] ?></code>
        </div>
        <div>
            <strong>Tabelle verificate (<?= count($result['tables']) ?>):</strong>
            <div class="tables">
                <?php foreach ($result['tables'] as $tb): ?><code><?= h_esc($tb) ?></code><?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="btn-row">
        <a class="btn" href="./">Apri Fucina →</a>
    </div>
</main>
</body>
</html>
