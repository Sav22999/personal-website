<?php
/**
 * Fucina — a certificate: certificate.php?c=CODE, with &format=png or &format=pdf for the files.
 * The certificate is a picture made by the server: the page around it holds no text of the certificate to change.
 * The code is long and random: only whoever received the link can open it.
 * It is a personal achievement with no legal value, and the certificate says so.
 */

declare(strict_types=1);

// The app passes its language in the link; anyone else gets the language of their browser. The picture is always in English.
if (in_array($_GET['lang'] ?? '', ['it', 'en'], true)) $_SERVER['HTTP_X_LANG'] = $_GET['lang'];

require __DIR__ . '/lib/bootstrap.php';
require __DIR__ . '/lib/courses.php';
require __DIR__ . '/lib/certificate.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');
header('X-Robots-Tag: noindex');

function hh($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$code = preg_replace('/[^A-Z0-9]/', '', strtoupper((string)($_GET['c'] ?? '')));
$cert = null;
$course = '';
try {
    if ($code !== '' && credentials()) {
        $st = db()->prepare('SELECT c.*, co.data AS course_data, co.family FROM ' . t('certificates') . ' c LEFT JOIN ' . t('courses') . ' co ON co.id = c.course WHERE c.code = ?');
        $st->execute([$code]);
        $cert = $st->fetch() ?: null;
        if ($cert) {
            $title = $cert['course_data'] ? js($cert['course_data'])['title'] : null;
            $course = ($title['en'] ?? '') !== '' ? $title['en'] : ($title ? pick($title) : $cert['course']);
        }
    }
} catch (Throwable $e) {
    error_log('Fucina certificate: ' . $e->getMessage());
}

$format = (string)($_GET['format'] ?? '');
$canDraw = function_exists('imagettftext') && function_exists('imagecreatefrompng');

if ($format === 'png' || $format === 'pdf') {
    if (!$cert || !$canDraw) {
        http_response_code($cert ? 503 : 404);
        exit;
    }
    $img = certificate_image($cert['name'], $course, (string)$cert['family'], (int)$cert['score'], $cert['code'], $cert['issued_at'], (string)($cert['lang'] ?? 'en'));
    header('Cache-Control: no-cache');
    $file = 'fucina-certificate-' . $cert['code'];
    if ($format === 'pdf') {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $file . '.pdf"');
        echo certificate_pdf($img);
        exit;
    }
    header('Content-Type: image/png');
    if (!empty($_GET['dl'])) header('Content-Disposition: attachment; filename="' . $file . '.png"');
    imagepng($img, null, 6);
    exit;
}

$it = lang() === 'it';
$T = $it
    ? ['title' => 'Certificato', 'verify' => 'Verifica', 'png' => 'Salva in PNG', 'pdf' => 'Salva in PDF', 'print' => 'Stampa', 'copy' => 'Copia il link', 'copied' => 'Link copiato', 'none' => 'Certificato non trovato', 'noneText' => 'Il link non è corretto, oppure il certificato non esiste più.', 'back' => 'Vai a Fucina', 'noDraw' => 'Il certificato ora non può essere generato: riprova più tardi.']
    : ['title' => 'Certificate', 'verify' => 'Verify', 'png' => 'Save as PNG', 'pdf' => 'Save as PDF', 'print' => 'Print', 'copy' => 'Copy the link', 'copied' => 'Link copied', 'none' => 'Certificate not found', 'noneText' => 'The link is not correct, or the certificate no longer exists.', 'back' => 'Go to Fucina', 'noDraw' => 'The certificate cannot be generated right now: please try again later.'];
if (!$cert) http_response_code(404);
$link = 'certificate.php?c=' . rawurlencode($code);
?><!doctype html>
<html lang="<?= hh(lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= $cert ? hh($T['title'] . ' · Fucina') : hh($T['none']) ?></title>
    <link href="assets/icons/favicon.svg?v=2" rel="icon" type="image/svg+xml">
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 18px; background: #12113A; color: #F4F2FF; font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; display: grid; place-content: center; justify-items: center; gap: 16px; }
        .sheet { display: block; width: min(100%, 1040px); height: auto; box-shadow: 0 12px 40px rgba(0, 0, 0, .5); }
        .tools { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
        .tools button, .tools a { min-height: 44px; padding: 10px 16px; border: 0; border-radius: 12px; background: #FF7A4D; color: #2A0E05; font: 700 .95rem system-ui, sans-serif; cursor: pointer; text-decoration: none; }
        .tools .plain { background: #2A2768; color: #F4F2FF; }
        .missing { text-align: center; }
        @media print {
            body { background: none; padding: 0; display: block; min-height: 0; }
            .sheet { width: 297mm; height: 210mm; box-shadow: none; }
            .tools { display: none; }
        }
    </style>
</head>
<body>
<?php if (!$cert || !$canDraw): ?>
    <div class="missing">
        <h1><?= hh($cert ? $T['title'] : $T['none']) ?></h1>
        <p><?= hh($cert ? $T['noDraw'] : $T['noneText']) ?></p>
        <div class="tools"><a href="./"><?= hh($T['back']) ?></a></div>
    </div>
<?php else: ?>
    <img class="sheet" src="<?= hh($link) ?>&amp;format=png" width="2400" height="1697" alt="<?= hh('Fucina certificate: ' . $cert['name'] . ', ' . $course) ?>">
    <div class="tools">
        <a href="<?= hh($link) ?>&amp;format=png&amp;dl=1"><?= hh($T['png']) ?></a>
        <a href="<?= hh($link) ?>&amp;format=pdf"><?= hh($T['pdf']) ?></a>
        <a class="plain" href="verify.php?c=<?= hh(rawurlencode($code)) ?>"><?= hh($T['verify']) ?></a>
        <button class="plain" type="button" onclick="print()"><?= hh($T['print']) ?></button>
        <button class="plain" type="button" onclick="navigator.clipboard.writeText(location.href).then(()=>{this.textContent=<?= hh(json_encode($T['copied'], JSON_UNESCAPED_UNICODE)) ?>})"><?= hh($T['copy']) ?></button>
    </div>
<?php endif ?>
</body>
</html>
