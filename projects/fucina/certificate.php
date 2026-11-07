<?php
/**
 * Fucina — public page of a certificate: certificate.php?c=CODE, ready to show, share and print on A4.
 * The code is long and random: only whoever received the link can open it.
 * It is a personal achievement with no legal value, and the page says so.
 */

declare(strict_types=1);

// The app passes its language in the link; anyone else gets the language of their browser
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

$code = preg_replace('/[^A-Z0-9]/', '', strtoupper((string)($_GET['c'] ?? '')));
$cert = null;
$courseTitle = '';
try {
    if ($code !== '' && credentials()) {
        $st = db()->prepare('SELECT c.*, co.data AS course_data FROM ' . t('certificates') . ' c LEFT JOIN ' . t('courses') . ' co ON co.id = c.course WHERE c.code = ?');
        $st->execute([$code]);
        $cert = $st->fetch() ?: null;
        if ($cert) $courseTitle = $cert['course_data'] ? pick(js($cert['course_data'])['title']) : $cert['course'];
    }
} catch (Throwable $e) {
    error_log('Fucina certificate: ' . $e->getMessage());
}

$it = lang() === 'it';
$T = $it
    ? ['title' => 'Certificato di completamento', 'to' => 'Si certifica con orgoglio che', 'done' => 'ha completato tutti i livelli e superato l’esame finale del corso', 'score' => 'Punteggio', 'date' => 'Data', 'code' => 'Codice', 'note' => 'Riconoscimento personale rilasciato da Fucina: non ha valore legale.', 'print' => 'Stampa o salva in PDF', 'copy' => 'Copia il link', 'copied' => 'Link copiato', 'none' => 'Certificato non trovato', 'noneText' => 'Il link non è corretto, oppure il certificato non esiste più.', 'back' => 'Vai a Fucina']
    : ['title' => 'Certificate of completion', 'to' => 'This is proudly awarded to', 'done' => 'for completing every level and passing the final exam of the course', 'score' => 'Score', 'date' => 'Date', 'code' => 'Code', 'note' => 'Personal achievement issued by Fucina: it has no legal value.', 'print' => 'Print or save as PDF', 'copy' => 'Copy the link', 'copied' => 'Link copied', 'none' => 'Certificate not found', 'noneText' => 'The link is not correct, or the certificate no longer exists.', 'back' => 'Go to Fucina'];
if (!$cert) http_response_code(404);
$date = $cert ? (new DateTimeImmutable($cert['issued_at'], new DateTimeZone('UTC')))->format($it ? 'd/m/Y' : 'j M Y') : '';
?><!doctype html>
<html lang="<?= hh(lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= $cert ? hh($T['title'] . ' · ' . $cert['name']) : hh($T['none']) ?></title>
    <link href="assets/icons/favicon.svg?v=2" rel="icon" type="image/svg+xml">
    <style>
        @page { size: A4 landscape; margin: 0; }
        :root { --ink: #1B1B4B; --ember: #D93A14; --gold: #B7791F; --paper: #FFFBF3; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 18px; background: #17173F; color: var(--ink); font-family: Georgia, 'Times New Roman', serif; display: grid; place-items: center; gap: 14px; }
        .sheet { width: min(100%, 1000px); aspect-ratio: 297 / 210; background: var(--paper); padding: clamp(10px, 2vw, 22px); box-shadow: 0 12px 40px rgba(0, 0, 0, .5); }
        .frame { position: relative; height: 100%; border: 3px solid var(--ink); outline: 1px solid var(--gold); outline-offset: -9px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 3% 6%; gap: 1.2vmin; }
        .brand { display: flex; align-items: center; gap: 10px; font: 700 clamp(.8rem, 2vw, 1.15rem) ui-monospace, Menlo, Consolas, monospace; letter-spacing: .3em; text-transform: uppercase; color: var(--ember); }
        .brand img { width: clamp(24px, 4vw, 42px); height: auto; }
        h1 { margin: .2em 0 0; font-size: clamp(1.1rem, 3.6vw, 2.5rem); font-weight: 400; font-style: italic; }
        .to, .done { margin: 0; font-size: clamp(.72rem, 1.7vw, 1.05rem); color: #5B4F46; }
        .name { margin: .1em 0; font-size: clamp(1.5rem, 5.6vw, 3.9rem); font-weight: 700; border-bottom: 2px solid var(--gold); padding: 0 .5em .08em; overflow-wrap: anywhere; }
        .course { margin: 0; font: 700 clamp(.95rem, 2.8vw, 1.8rem) ui-monospace, Menlo, Consolas, monospace; color: var(--ember); }
        .meta { display: flex; gap: clamp(14px, 5vw, 56px); margin-top: 1.6vmin; font-size: clamp(.64rem, 1.4vw, .9rem); }
        .meta b { display: block; font: 700 .72em ui-monospace, Menlo, monospace; text-transform: uppercase; color: var(--gold); letter-spacing: .1em; }
        .seal { position: absolute; right: 5%; bottom: 8%; width: clamp(44px, 10vw, 104px); }
        .note { position: absolute; bottom: 3.2%; left: 0; right: 0; margin: 0; font-size: clamp(.5rem, 1vw, .68rem); color: #7A6D62; }
        .tools { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
        .tools button, .tools a { padding: 10px 16px; border: 2px solid #000; border-radius: 6px; background: #FF7A4D; color: #1B1B4B; font: 700 .9rem ui-monospace, Menlo, monospace; cursor: pointer; text-decoration: none; }
        .missing { color: #F5EDE3; text-align: center; font-family: ui-monospace, Menlo, monospace; }
        @media print {
            body { background: none; padding: 0; display: block; min-height: 0; }
            .sheet { width: 297mm; height: 210mm; aspect-ratio: auto; box-shadow: none; padding: 8mm; }
            .tools { display: none; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<?php if (!$cert): ?>
    <div class="missing">
        <h1><?= hh($T['none']) ?></h1>
        <p><?= hh($T['noneText']) ?></p>
        <div class="tools"><a href="./"><?= hh($T['back']) ?></a></div>
    </div>
<?php else: ?>
    <main class="sheet">
        <div class="frame">
            <div class="brand"><img src="assets/icons/favicon.svg?v=2" alt="">Fucina</div>
            <h1><?= hh($T['title']) ?></h1>
            <p class="to"><?= hh($T['to']) ?></p>
            <p class="name"><?= hh($cert['name']) ?></p>
            <p class="done"><?= hh($T['done']) ?></p>
            <p class="course"><?= hh($courseTitle) ?></p>
            <div class="meta">
                <span><b><?= hh($T['score']) ?></b><?= (int)$cert['score'] ?>%</span>
                <span><b><?= hh($T['date']) ?></b><?= hh($date) ?></span>
                <span><b><?= hh($T['code']) ?></b><?= hh($cert['code']) ?></span>
            </div>
            <svg class="seal" viewBox="0 0 100 100" aria-hidden="true">
                <path fill="#D93A14" d="M50 3l11 9 14-3 6 13 13 6-3 14 9 11-9 11 3 14-13 6-6 13-14-3-11 9-11-9-14 3-6-13-13-6 3-14-9-11 9-11-3-14 13-6 6-13 14 3z"/>
                <circle cx="50" cy="50" r="30" fill="none" stroke="#FFFBF3" stroke-width="2"/>
                <path fill="none" stroke="#FFFBF3" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" d="M34 38 24 50l10 12M66 38l10 12-10 12"/><path fill="#FFC53D" d="M54 30 42 53h8l-4 18 13-24h-8z"/>
            </svg>
            <p class="note"><?= hh($T['note']) ?></p>
        </div>
    </main>
    <div class="tools">
        <button type="button" onclick="print()"><?= hh($T['print']) ?></button>
        <button type="button" onclick="navigator.clipboard.writeText(location.href).then(()=>{this.textContent=<?= hh(json_encode($T['copied'], JSON_UNESCAPED_UNICODE)) ?>})"><?= hh($T['copy']) ?></button>
    </div>
<?php endif ?>
</body>
</html>
