<?php
/**
 * The way to illerom.eu for the projects that moved there.
 *
 * The .htaccess of each moved project sends its pages here (never its API, scripts or images: see there), and the root
 * .htaccess sends the old addresses of the project pages. The updated Android apps open it as ?app=<slug>&to=<address>.
 *
 * Everything keeps its path, parameters and, in browsers, its #fragment: room invites (?code=, ?join=), email links
 * (?verify=, ?reset=), public CVs (?user=), subscriptions, certificates. The first page of a project also takes along
 * what it had saved in the browser (games, records, preferences) and removes its old service worker.
 */

declare(strict_types=1);

const NEW_SITE = 'https://www.illerom.eu';
// project => prefix of its keys in the browser's storage
const MOVED = [
    'chess' => 'chess_', 'opennewsletter' => 'on_', 'thinkhop' => 'thinkhop.', 'foodtrack' => 'ft.', 'sudoku' => 'sudoku.',
    'mycurriculum' => 'mc_', 'forkville' => 'forkville-', 'prism-mine' => 'pm_', 'fucina' => 'fc.', 'connect-four' => 'c4_',
    'letterhop' => 'letterhop.', 'giftwise' => 'gw.', 'sea-battle' => 'sb_', 'faceguess' => 'fg_',
];
const ALIASES = ['forza-quattro' => 'connect-four'];

header('X-Robots-Tag: noindex');
header('Vary: User-Agent, Sec-Fetch-Dest');

function go(string $to, int $code): void
{
    header('Cache-Control: ' . ($code === 301 ? 'public, max-age=3600' : 'no-store'));
    header('Location: ' . $to, true, $code);
    exit;
}

$uri = (string)($_SERVER['REQUEST_URI'] ?? '');
$path = (string)parse_url($uri, PHP_URL_PATH);
$query = (string)parse_url($uri, PHP_URL_QUERY);
$host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
$slug = $rest = '';

if ($host === 'fucina.saveriomorelli.com') {
    $slug = 'fucina';
    $rest = ltrim($path, '/');
} elseif (preg_match('#^/projects/about/([a-z0-9-]+)(?:/.*)?$#', $path, $m)) {
    $page = ALIASES[$m[1]] ?? $m[1];
    go(NEW_SITE . (isset(MOVED[$page]) ? "/projects/$page/" : '/'), 301);
} elseif (preg_match('#^/projects/([a-z0-9-]+)(?:/(.*))?$#', $path, $m) && $m[1] !== 'illerom') {
    $slug = ALIASES[$m[1]] ?? $m[1];
    $rest = $m[2] ?? '';
} elseif (is_string($_GET['app'] ?? null)) {
    $slug = $_GET['app'];
    $query = '';
}
if (!isset(MOVED[$slug])) go(NEW_SITE . '/', 302);

$base = NEW_SITE . "/$slug/";
$to = $base . $rest . ($query !== '' ? '?' . $query : '');
// ?app=…&to=…: only inside the same project
$asked = $_GET['to'] ?? '';
if ($rest === '' && $query === '' && is_string($asked) && strpos($asked, $base) === 0 && !preg_match('/[\x00-\x20\\\\]/', $asked)) $to = $asked;

$method = (string)($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method !== 'GET' && $method !== 'HEAD') go($to, 308);

$agent = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
$dest = (string)($_SERVER['HTTP_SEC_FETCH_DEST'] ?? '');
// "empty": the page asked through the old service worker
$browser = in_array($dest, ['document', 'empty', ''], true) && strpos($agent, 'Mozilla/') === 0
    && !preg_match('/bot\b|bot[\/;)]|crawl|spider|slurp|preview|facebookexternalhit|whatsapp|telegram|lighthouse/i', $agent);
$first = in_array($rest, ['', 'index.php', 'index.html'], true);
// Fucina inside the frame of its own subdomain: its sign-in would not work in there, so it opens on the whole page
$framed = in_array($dest, ['iframe', 'frame'], true) && $slug === 'fucina';

if (!$framed && (!$browser || !$first)) go($to, 301);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
header('Referrer-Policy: strict-origin-when-cross-origin');
$json = fn($v) => json_encode($v, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
?>
<!doctype html>
<html lang="it">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>illerom.eu</title>
<link rel="canonical" href="<?= htmlspecialchars($base) ?>">
<noscript><meta http-equiv="refresh" content="0;url=<?= htmlspecialchars($to) ?>"></noscript>
<style>
    body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f4f1f7; color: #2a1430; font: 1.05rem/1.55 system-ui, sans-serif; text-align: center }
    main { padding: 1.5rem; max-width: 26rem }
    a { display: inline-block; margin-top: 1rem; padding: .8rem 1.4rem; background: #ffd94f; color: #2a1430; font-weight: 600; text-decoration: none; transform: rotate(-1.5deg) }
    main.quiet { visibility: hidden }
    @media (prefers-color-scheme: dark) { body { background: #170f1c; color: #f3ecf7 } }
</style>
<main id="m"<?= $framed ? '' : ' class="quiet"' ?>>
    <p>Questo progetto ora è su illerom.eu.<br><span lang="en">This project is now on illerom.eu.</span></p>
    <a id="go" href="<?= htmlspecialchars($to) ?>" target="_top">illerom.eu/<?= htmlspecialchars($slug) ?></a>
</main>
<script>
    (function () {
        var app = <?= $json($slug) ?>, prefix = <?= $json(MOVED[$slug]) ?>, to = <?= $json($to) ?>, framed = <?= $framed ? 'true' : 'false' ?>;
        var show = function () { document.getElementById('m').className = ''; };
        if (location.hash && to.indexOf('#') < 0) to += location.hash;
        document.getElementById('go').href = to;

        // what the project saved in this browser, smallest first: a too long address would not open
        var data = {}, room = 700000;
        try {
            Object.keys(localStorage).filter(function (k) { return k.indexOf(prefix) === 0; })
                .map(function (k) { return [k, localStorage.getItem(k)]; })
                .sort(function (a, b) { return a[1].length - b[1].length; })
                .forEach(function (x) {
                    var n = encodeURIComponent(x[0] + x[1]).length + 20;
                    if (n < room) { data[x[0]] = x[1]; room -= n; }
                });
        } catch (e) {}
        var next = <?= $json(NEW_SITE) ?> + '/move/#' + encodeURIComponent(JSON.stringify({app: app, to: to, data: data}));

        // the old service worker and its copies are not needed any more (its notifications stop too: they start again on illerom)
        var clean = Promise.resolve();
        try {
            var scope = location.origin + (location.hostname.indexOf('fucina.') === 0 ? '/' : '/projects/' + app + '/');
            if (navigator.serviceWorker) clean = navigator.serviceWorker.getRegistrations().then(function (l) {
                return Promise.all(l.filter(function (r) { return r.scope === scope; }).map(function (r) { return r.unregister(); }));
            });
            if (window.caches) clean = clean.then(function () { return caches.keys(); }).then(function (l) {
                return Promise.all(l.filter(function (k) { return k.indexOf(app + '-') === 0 || (app === 'foodtrack' && k === 'ft-state'); }).map(function (k) { return caches.delete(k); }));
            });
        } catch (e) {}
        if (framed) { document.getElementById('go').href = next; return; }
        var left = false, leave = function () { if (!left) { left = true; location.replace(next); } };
        clean.then(leave, leave);
        setTimeout(leave, 1500);
        setTimeout(show, 2500);
    })();
</script>
</html>
