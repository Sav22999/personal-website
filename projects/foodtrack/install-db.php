<?php
/**
 * FoodTrack — guided database installation.
 *
 * Asks for the database credentials ($localhost_db, $username_db, $password_db, $database_name),
 * creates the tables (prefix "ft_"), loads categories, products and recipes, creates the master admin
 * and writes credentials.php (git-ignored, blocked from the browser).
 *
 * After the installation the page locks itself: it can only be re-run (to repair or update the tables)
 * by typing the database password already saved in credentials.php. For safety, delete it from the
 * server once you are done.
 */

declare(strict_types=1);

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/schema.php';
require __DIR__ . '/lib/seed.php';
require __DIR__ . '/lib/households.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function credentials_php(string $host, string $user, string $pass, string $name, string $prefix, string $google = ''): string
{
    $q = function (string $v): string {
        return '"' . addcslashes($v, "\\\"\$") . '"';
    };
    return "<?php\n"
            . "/**\n * FoodTrack — database credentials. Created by install-db.php on " . gmdate('Y-m-d H:i') . " UTC.\n"
            . " * NEVER commit this file (it is listed in .gitignore) and never publish it.\n */\n\n"
            . '$localhost_db  = ' . $q($host) . ";\n"
            . '$username_db   = ' . $q($user) . ";\n"
            . '$password_db   = ' . $q($pass) . ";\n"
            . '$database_name = ' . $q($name) . ";\n\n"
            . '$table_prefix  = ' . $q($prefix) . ";\n\n"
            . "// Sign in with Google (optional): OAuth client ID of type \"Web application\"\n"
            . '$google_client_id = ' . $q($google) . ";\n\n"
            . "\$debug = false;\n";
}

$existing = credentials();
$locked = false;
if ($existing) {
    try {
        $pdo = pdo_connect($existing['host'], $existing['user'], $existing['pass'], $existing['name']);
        $p = $existing['prefix'];
        $locked = (int)$pdo->query("SELECT COUNT(*) FROM `{$p}users` WHERE role >= " . ROLE_MASTER)->fetchColumn() > 0;
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
    $prefix = trim((string)($_POST['table_prefix'] ?? 'ft_')) ?: 'ft_';
    $google = trim((string)($_POST['google_client_id'] ?? ''));
    if ($google !== '' && !preg_match('/^[A-Za-z0-9\-_.]{3,200}\.apps\.googleusercontent\.com$/', $google)) $errors[] = 'Client ID di Google non valido: deve terminare con .apps.googleusercontent.com.';

    if ($mode === 'update') {
        // Locked: only whoever knows the saved database password can run it again
        $host = $existing['host'];
        $user = $existing['user'];
        $name = $existing['name'];
        $prefix = $existing['prefix'];
        if (!hash_equals($existing['pass'], $pass)) $errors[] = 'La password del database non corrisponde a quella salvata in credentials.php.';
    } else {
        if ($existing && !hash_equals($existing['pass'], $pass)) {
            $errors[] = 'Esiste già un file credentials.php: per sovrascriverlo inserisci la stessa password del database salvata al suo interno (oppure elimina il file via FTP).';
        }
        if ($host === '' || $user === '' || $name === '') $errors[] = 'Compila host, utente e nome del database.';
        if (!preg_match('/^[A-Za-z0-9_]{1,20}$/', $prefix)) $errors[] = 'Prefisso tabelle non valido (solo lettere, numeri e _).';
        $adminName = trim((string)($_POST['admin_name'] ?? ''));
        $adminEmail = mb_strtolower(trim((string)($_POST['admin_email'] ?? '')));
        $adminPw = (string)($_POST['admin_password'] ?? '');
        if (mb_strlen($adminName) < 2) $errors[] = 'Inserisci il nome del master admin.';
        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email del master admin non valida.';
        if (mb_strlen($adminPw) < 10) $errors[] = 'La password del master admin deve avere almeno 10 caratteri.';
        if ($adminPw !== (string)($_POST['admin_password2'] ?? '')) $errors[] = 'Le due password del master admin non coincidono.';
    }

    if (!$errors) {
        try {
            $pdo = pdo_connect($host, $user, $pass, $name);
        } catch (Throwable $e) {
            error_log('[foodtrack] install: connection failed: ' . $e->getMessage());
            $errors[] = 'Connessione al database non riuscita: controlla host, utente, password e nome del database.';
        }
    }

    if (!$errors) {
        try {
            install_schema($pdo, $prefix);
            $p = $prefix;
            $adminId = null;
            if ($mode === 'install') {
                $st = $pdo->prepare("SELECT id FROM `{$p}users` WHERE email = ?");
                $st->execute([$adminEmail]);
                $adminId = $st->fetchColumn();
                $now = gmdate('Y-m-d H:i:s');
                if ($adminId) {
                    $pdo->prepare("UPDATE `{$p}users` SET name = ?, password_hash = ?, role = ?, status = 'active', must_change_password = 0, email_verified_at = COALESCE(email_verified_at, ?) WHERE id = ?")
                            ->execute([$adminName, password_hash($adminPw, PASSWORD_DEFAULT), ROLE_MASTER, $now, $adminId]);
                } else {
                    $pdo->prepare("INSERT INTO `{$p}users` (name, email, password_hash, role, status, created_at, email_verified_at) VALUES (?,?,?,?, 'active', ?, ?)")
                            ->execute([$adminName, $adminEmail, password_hash($adminPw, PASSWORD_DEFAULT), ROLE_MASTER, $now, $now]);
                    $adminId = (int)$pdo->lastInsertId();
                }
                // Only one master admin: any other one becomes a normal administrator
                $pdo->prepare("UPDATE `{$p}users` SET role = ? WHERE role >= ? AND id <> ?")->execute([ROLE_ADMIN, ROLE_MASTER, $adminId]);
            }
            $added = seed_database($pdo, $p, $adminId ? (int)$adminId : null);

            if ($mode === 'update' && $google !== '' && $google !== ($existing['google_client_id'] ?? '')) {
                $content = credentials_php($host, $user, $pass, $name, $prefix, $google);
                if (@file_put_contents(credentials_file(), $content, LOCK_EX) === false) $manualFile = $content;
            }
            if ($mode === 'install') {
                // Personal household (with the default spaces) for the master admin
                $st = $pdo->prepare("SELECT COUNT(*) FROM `{$p}household_members` WHERE user_id = ?");
                $st->execute([$adminId]);
                if ((int)$st->fetchColumn() === 0) create_household_raw($pdo, $p, (int)$adminId, tr("{name}'s home", ['name' => first_name($adminName)]));

                $content = credentials_php($host, $user, $pass, $name, $prefix, $google);
                if (@file_put_contents(credentials_file(), $content, LOCK_EX) === false) {
                    $manualFile = $content;
                } else {
                    @chmod(credentials_file(), 0640);
                }
            }
            $done = ['added' => $added, 'email' => $adminEmail ?? null, 'mode' => $mode];
        } catch (Throwable $e) {
            error_log('[foodtrack] install: ' . get_class($e) . ': ' . $e->getMessage());
            $errors[] = 'Installazione non riuscita durante la creazione delle tabelle: ' . ($e instanceof PDOException ? 'errore del database (dettagli nel log del server).' : h($e->getMessage()));
        }
    }
}

/** Household creation with an explicit prefix (used before credentials.php can be read). */
function create_household_raw(PDO $pdo, string $p, int $userId, string $name): void
{
    $now = gmdate('Y-m-d H:i:s');
    do {
        $code = random_code(8);
        $st = $pdo->prepare("SELECT 1 FROM `{$p}households` WHERE invite_code = ?");
        $st->execute([$code]);
    } while ($st->fetchColumn());
    $pdo->prepare("INSERT INTO `{$p}households` (name, invite_code, owner_id, created_at) VALUES (?,?,?,?)")->execute([$name, $code, $userId, $now]);
    $hid = (int)$pdo->lastInsertId();
    $pdo->prepare("INSERT INTO `{$p}household_members` (household_id, user_id, role, joined_at) VALUES (?,?, 'owner', ?)")->execute([$hid, $userId, $now]);
    $ins = $pdo->prepare("INSERT INTO `{$p}spaces` (household_id, name, icon, environment, notes, sort, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?)");
    foreach (default_spaces() as $i => [$sname, $icon, $notes, $env]) $ins->execute([$hid, tr($sname), $icon, $env, tr($notes), $i * 10, $userId, $now, $now]);
    $pdo->prepare("UPDATE `{$p}users` SET active_household_id = ? WHERE id = ?")->execute([$hid, $userId]);
}
?><!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>FoodTrack · Installazione</title>
    <link rel="icon" type="image/png" sizes="32x32" href="assets/icons/favicon-32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/icons/icon-192.png">
    <link rel="apple-touch-icon" href="assets/icons/apple-touch-icon.png">
    <style>
        :root {
            --green: #4CAF50;
            --green-dark: #2E7D32;
            --bg: #F3F6F3;
            --card: #fff;
            --text: #1C1B1F;
            --muted: #5F6368;
            --border: #DADCE0;
            --red: #C62828;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #121412;
                --card: #1E211E;
                --text: #E6E1E5;
                --muted: #A8ADA8;
                --border: #353935;
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
            color: var(--green-dark);
            font-weight: 700;
        }

        .brand img {
            width: 28px;
            height: 28px;
        }

        h1 {
            margin: 6px 0 4px;
            font-size: 1.6rem;
        }

        h2 {
            font-size: 1rem;
            margin: 26px 0 4px;
            color: var(--green-dark);
        }

        @media (prefers-color-scheme: dark) {
            .brand, h2 {
                color: #81C784;
            }
        }

        p.lead {
            color: var(--muted);
            margin: 0 0 18px;
        }

        label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: var(--muted);
            margin: 12px 0 5px;
        }

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font: inherit;
            background: var(--bg);
            color: var(--text);
        }

        input:focus {
            outline: 2px solid var(--green);
            border-color: transparent;
        }

        code {
            font-size: .88em;
            background: var(--bg);
            padding: 1px 5px;
            border-radius: 5px;
        }

        button {
            margin-top: 24px;
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 999px;
            background: var(--green);
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: var(--green-dark);
        }

        .err {
            background: rgba(198, 40, 40, .08);
            border: 1px solid var(--red);
            color: var(--red);
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .ok {
            background: rgba(76, 175, 80, .1);
            border: 1px solid var(--green);
            border-radius: 10px;
            padding: 12px 14px;
        }

        .hint {
            font-size: .84rem;
            color: var(--muted);
            margin-top: 4px;
        }

        .row {
            display: grid;
            grid-template-columns:1fr 1fr;
            gap: 0 12px;
        }

        @media (max-width: 480px) {
            .row {
                grid-template-columns:1fr;
            }
        }

        pre {
            white-space: pre-wrap;
            word-break: break-all;
            background: var(--bg);
            padding: 12px;
            border-radius: 10px;
            font-size: .82rem;
        }

        a {
            color: var(--green-dark);
            font-weight: 600;
        }
    </style>
</head>
<body>
<main>
    <div class="brand"><img src="assets/icons/icon-color.png" alt=""> FoodTrack</div>
    <h1><?= $mode === 'update' && !$done ? 'Aggiorna il database' : 'Installazione' ?></h1>
    <p class="lead">Crea le tabelle (prefisso <code>ft_</code>), carica categorie, prodotti e ricette e configura il
        master admin.</p>

    <?php foreach ($errors as $e): ?>
        <div class="err"><?= h($e) ?></div><?php endforeach; ?>

    <?php if ($done): ?>
        <div class="ok">
            <strong><?= $done['mode'] === 'install' ? 'Installazione completata.' : 'Database aggiornato.' ?></strong><br>
            Tabelle create o verificate. Aggiunti: <?= (int)$done['added']['categories'] ?>
            categorie, <?= (int)$done['added']['products'] ?> prodotti, <?= (int)$done['added']['recipes'] ?>
            ricette.<br>
            <?php if ($done['email']): ?>Master admin: <strong><?= h($done['email']) ?></strong><?php endif; ?>
        </div>
        <?php if ($manualFile): ?>
            <div class="err" style="margin-top:14px">Non è stato possibile scrivere <code>credentials.php</code>
                (permessi della cartella). Crealo tu via FTP nella cartella di FoodTrack con questo contenuto:
            </div>
            <pre><?= h($manualFile) ?></pre>
        <?php endif; ?>
        <p><strong>Per sicurezza ora elimina <code>install-db.php</code> dal server</strong> (puoi ricaricarlo via FTP
            quando serve aggiornare il database).</p>
        <p><a href="./">Apri FoodTrack →</a> &nbsp; <a href="admin.html">Portale admin →</a></p>

    <?php elseif ($mode === 'update'): ?>
        <div class="ok">FoodTrack è già installato e il master admin esiste. Puoi ricontrollare le tabelle e aggiungere
            i dati iniziali mancanti (non cancella nulla).
        </div>
        <form method="post" autocomplete="off">
            <label for="password_db">Password del database ($password_db)</label>
            <input id="password_db" name="password_db" type="password" required>
            <p class="hint">È la password salvata in <code>credentials.php</code>: serve a confermare che sei il
                proprietario.</p>
            <label for="google_client_id">Client ID di Google (facoltativo)</label>
            <input id="google_client_id" name="google_client_id"
                   placeholder="1234567890-xxxx.apps.googleusercontent.com"
                   value="<?= h($existing['google_client_id'] ?? '') ?>">
            <p class="hint">Per attivare «Continua con Google». Lascia vuoto per non cambiarlo.</p>
            <button type="submit">Verifica e aggiorna le tabelle</button>
        </form>

    <?php else: ?>
        <form method="post" autocomplete="off">
            <h2>1 · Database MySQL</h2>
            <p class="hint">Li trovi nel pannello del tuo hosting (su Aruba: Hosting Linux → Database MySQL). Verranno
                salvati in <code>credentials.php</code>, che non va mai pubblicato su git.</p>
            <label for="localhost_db">Host ($localhost_db)</label>
            <input id="localhost_db" name="localhost_db" required placeholder="es. 89.46.111.xxx"
                   value="<?= h($_POST['localhost_db'] ?? '') ?>">
            <div class="row">
                <div>
                    <label for="username_db">Utente ($username_db)</label>
                    <input id="username_db" name="username_db" required value="<?= h($_POST['username_db'] ?? '') ?>">
                </div>
                <div>
                    <label for="database_name">Nome database ($database_name)</label>
                    <input id="database_name" name="database_name" required
                           value="<?= h($_POST['database_name'] ?? '') ?>">
                </div>
            </div>
            <label for="password_db">Password ($password_db)</label>
            <input id="password_db" name="password_db" type="password" required>
            <label for="table_prefix">Prefisso tabelle</label>
            <input id="table_prefix" name="table_prefix" value="<?= h($_POST['table_prefix'] ?? 'ft_') ?>"
                   pattern="[A-Za-z0-9_]{1,20}">

            <h2>2 · Master admin</h2>
            <p class="hint">L’unico account con il controllo completo: gestisce gli amministratori, le ricette, i
                prodotti e gli utenti.</p>
            <label for="admin_name">Nome e cognome</label>
            <input id="admin_name" name="admin_name" required value="<?= h($_POST['admin_name'] ?? '') ?>">
            <label for="admin_email">Email</label>
            <input id="admin_email" name="admin_email" type="email" required
                   value="<?= h($_POST['admin_email'] ?? '') ?>">
            <div class="row">
                <div>
                    <label for="admin_password">Password (min. 10 caratteri)</label>
                    <input id="admin_password" name="admin_password" type="password" minlength="10" required
                           autocomplete="new-password">
                </div>
                <div>
                    <label for="admin_password2">Ripeti password</label>
                    <input id="admin_password2" name="admin_password2" type="password" minlength="10" required
                           autocomplete="new-password">
                </div>
            </div>
            <h2>3 · Accesso con Google (facoltativo)</h2>
            <label for="google_client_id">Client ID di Google</label>
            <input id="google_client_id" name="google_client_id"
                   placeholder="1234567890-xxxx.apps.googleusercontent.com"
                   value="<?= h($_POST['google_client_id'] ?? '') ?>">
            <p class="hint">Se lo lasci vuoto puoi aggiungerlo dopo in <code>credentials.php</code> (variabile <code>$google_client_id</code>).
            </p>
            <button type="submit">Crea le tabelle e installa FoodTrack</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
