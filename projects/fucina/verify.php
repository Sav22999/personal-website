<?php
/**
 * Fucina — public check of a certificate from its number, with no account: verify.php?c=NUMBER.
 * The number is the primary key of the certificate, so it is unique. Wrong attempts are limited, so numbers cannot be guessed.
 */

declare(strict_types=1);

if (in_array($_GET['lang'] ?? '', ['it', 'en'], true)) $_SERVER['HTTP_X_LANG'] = $_GET['lang'];

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/courses.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');
header('X-Robots-Tag: noindex');

function hh($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$it = lang() === 'it';
$T = $it
    ? ['title' => 'Verifica un certificato', 'lead' => 'Inserisci il numero stampato sul certificato per controllare che sia autentico.', 'number' => 'Numero del certificato', 'check' => 'Verifica', 'valid' => 'Certificato autentico', 'invalid' => 'Certificato non trovato', 'invalidText' => 'Nessun certificato con questo numero. Controlla di averlo scritto bene.', 'many' => 'Troppi tentativi: riprova tra qualche minuto.', 'error' => 'Verifica non disponibile: riprova più tardi.', 'name' => 'Rilasciato a', 'course' => 'Corso', 'score' => 'Punteggio', 'date' => 'Data', 'open' => 'Apri il certificato', 'note' => 'Riconoscimento personale rilasciato da Fucina: non ha valore legale.', 'back' => 'Vai a Fucina']
    : ['title' => 'Verify a certificate', 'lead' => 'Enter the number printed on the certificate to check that it is genuine.', 'number' => 'Certificate number', 'check' => 'Verify', 'valid' => 'Genuine certificate', 'invalid' => 'Certificate not found', 'invalidText' => 'No certificate has this number. Check that you typed it correctly.', 'many' => 'Too many attempts: please try again in a few minutes.', 'error' => 'Verification is not available: please try again later.', 'name' => 'Issued to', 'course' => 'Course', 'score' => 'Score', 'date' => 'Date', 'open' => 'Open the certificate', 'note' => 'Personal achievement issued by Fucina: it has no legal value.', 'back' => 'Go to Fucina'];

$code = substr(preg_replace('/[^A-Z0-9]/', '', strtoupper((string)($_GET['c'] ?? ''))), 0, 12);
$state = '';
$cert = null;
$course = '';
if ($code !== '') {
    try {
        if (too_many_attempts('verify', 20, 15)) {
            $state = 'many';
            http_response_code(429);
        } else {
            $st = db()->prepare('SELECT c.*, co.data AS course_data FROM ' . t('certificates') . ' c LEFT JOIN ' . t('courses') . ' co ON co.id = c.course WHERE c.code = ?');
            $st->execute([$code]);
            $cert = $st->fetch() ?: null;
            if ($cert) {
                $state = 'valid';
                $course = $cert['course_data'] ? pick(js($cert['course_data'])['title']) : $cert['course'];
            } else {
                $state = 'invalid';
                record_attempt('verify');
                http_response_code(404);
            }
        }
    } catch (Throwable $e) {
        error_log('Fucina verify: ' . $e->getMessage());
        $state = 'error';
        http_response_code(503);
    }
}
$date = $cert ? (new DateTimeImmutable($cert['issued_at'], new DateTimeZone('UTC')))->format($it ? 'd/m/Y' : 'j M Y') : '';
?><!doctype html>
<html lang="<?= hh(lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= hh($T['title']) ?> · Fucina</title>
    <link href="assets/icons/favicon.svg?v=2" rel="icon" type="image/svg+xml">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 24px 16px; background: #12113A; color: #F4F2FF; font: 16px/1.5 system-ui, -apple-system, 'Segoe UI', sans-serif; display: grid; place-content: center; }
        main { width: min(100vw - 32px, 480px); padding: 24px; border-radius: 20px; background: #1E1C52; border: 2px solid #2F2C72; }
        .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; color: inherit; font-size: 1.3rem; font-weight: 700; text-decoration: none; }
        .brand img { width: 38px; height: 38px; border-radius: 9px; }
        h1 { margin: 0 0 6px; font-size: 1.5rem; line-height: 1.2; }
        p { margin: 0 0 14px; color: #B9B6E0; }
        label { display: block; margin-bottom: 6px; font-weight: 700; }
        input { width: 100%; min-height: 48px; padding: 10px 12px; border: 2px solid #37348A; border-radius: 12px; background: #12113A; color: inherit; font: 600 1.05rem ui-monospace, Menlo, Consolas, monospace; letter-spacing: .06em; text-transform: uppercase; }
        input:focus { outline: 3px solid #FF7A4D; outline-offset: 1px; }
        .btn { display: inline-block; min-height: 46px; margin-top: 12px; padding: 11px 18px; border: 0; border-radius: 12px; background: #FF7A4D; color: #2A0E05; font: 700 1rem system-ui, sans-serif; text-decoration: none; cursor: pointer; }
        .result { margin-top: 18px; padding: 14px 16px; border-radius: 14px; }
        .result h2 { margin: 0 0 6px; font-size: 1.15rem; }
        .result.ok { background: #0F4A3A; }
        .result.ok h2 { color: #4FE3B4; }
        .result.ko { background: #55172A; }
        .result.ko h2 { color: #FF8199; }
        .result p { margin: 0; color: #F4F2FF; }
        dl { display: grid; grid-template-columns: auto 1fr; gap: 3px 14px; margin: 8px 0 0; }
        dt { color: #B9D9CE; }
        dd { margin: 0; font-weight: 700; overflow-wrap: anywhere; }
        .small { margin: 10px 0 0; font-size: .82rem; color: #B9B6E0; }
    </style>
</head>
<body>
<main>
    <a class="brand" href="./"><img src="assets/icons/favicon.svg?v=2" alt="">Fucina</a>
    <h1><?= hh($T['title']) ?></h1>
    <p><?= hh($T['lead']) ?></p>
    <form method="get" action="verify.php">
        <label for="c"><?= hh($T['number']) ?></label>
        <input id="c" name="c" value="<?= hh($code) ?>" maxlength="20" autocomplete="off" autocapitalize="characters" spellcheck="false" required>
        <button class="btn" type="submit"><?= hh($T['check']) ?></button>
    </form>
<?php if ($state === 'valid'): ?>
    <section class="result ok" aria-live="polite">
        <h2>✓ <?= hh($T['valid']) ?></h2>
        <dl>
            <dt><?= hh($T['name']) ?></dt><dd><?= hh($cert['name']) ?></dd>
            <dt><?= hh($T['course']) ?></dt><dd><?= hh($course) ?></dd>
            <dt><?= hh($T['score']) ?></dt><dd><?= (int)$cert['score'] ?>%</dd>
            <dt><?= hh($T['date']) ?></dt><dd><?= hh($date) ?></dd>
        </dl>
        <a class="btn" href="certificate.php?c=<?= hh(rawurlencode($cert['code'])) ?>"><?= hh($T['open']) ?></a>
        <p class="small"><?= hh($T['note']) ?></p>
    </section>
<?php elseif ($state !== ''): ?>
    <section class="result ko" aria-live="polite">
        <h2><?= hh($state === 'invalid' ? $T['invalid'] : $T['title']) ?></h2>
        <p><?= hh($state === 'invalid' ? $T['invalidText'] : $T[$state]) ?></p>
    </section>
<?php endif ?>
</main>
</body>
</html>
