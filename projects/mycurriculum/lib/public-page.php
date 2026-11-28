<?php
/**
 * MyCurriculum — the public web page of a CV (index.php?user=NAME[&lang=xx]).
 *
 * The public copy (public_cv in lib/cv.php: hidden contacts, fields and sections already removed) is embedded in
 * the page and drawn by assets/public.js with the same templates as the editor and the PDF. A private CV is shown
 * only to its owner, as a preview. The page asks search engines not to index it: sharing the link is up to the owner.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/cv.php';

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Robots-Tag: noindex');

$data = null;
$preview = false;
try {
    if (credentials()) {
        $owner = user_by_username((string)$_GET['user']);
        $cv = $owner ? load_cv((int)$owner['id']) : null;
        if ($cv) {
            $me = current_user();
            $isOwner = $me && (int)$me['id'] === (int)$owner['id'];
            if ($cv['settings']['public'] || $isOwner) {
                $data = public_cv($owner, $cv);
                $preview = !$cv['settings']['public'];
                // A simple visit counter: nothing about the visitor is stored
                if (!$isOwner) db()->prepare('UPDATE ' . t('cvs') . ' SET views = views + 1 WHERE user_id = ?')->execute([$owner['id']]);
            }
        }
    }
} catch (Throwable $e) {
    error_log('MyCurriculum public page: ' . $e->getMessage());
    $data = null;
}

$langs = $data['settings']['langs'] ?? [];
$lang = in_array($_GET['lang'] ?? '', $langs, true) ? $_GET['lang'] : ($data['settings']['defaultLang'] ?? 'en');
$name = $data ? trim($data['profile']['firstName'] . ' ' . $data['profile']['lastName']) : '';
$headline = $data['content'][$lang]['headline'] ?? '';
$esc = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
if (!$data) http_response_code(404);
header('Cache-Control: private, no-store');
?><!doctype html>
<html lang="<?= $esc($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex">
    <title><?= $data ? $esc(($name ?: 'CV') . ' — CV') : 'CV · MyCurriculum' ?></title>
    <?php if ($data): ?>
        <meta name="description" content="<?= $esc($headline ?: 'CV') ?>">
        <meta property="og:title" content="<?= $esc(($name ?: 'CV') . ' — CV') ?>">
        <meta property="og:description" content="<?= $esc($headline) ?>">
        <meta property="og:type" content="profile">
    <?php endif; ?>
    <meta name="theme-color" content="<?= $esc($data['settings']['accent'] ?? '#3D52D5') ?>">
    <link rel="icon" href="icons/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="icons/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="stylesheet" href="assets/app.css?v=2">
    <link rel="stylesheet" href="assets/cv.css?v=2">
</head>
<body>
<div id="pub"><noscript><?= $data ? $esc($name) : 'Not found' ?></noscript></div>
<script type="application/json" id="cv-data"><?= json_encode(['cv' => $data, 'lang' => $lang, 'preview' => $preview], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script type="module" src="assets/public.js?v=2"></script>
</body>
</html>
