<?php
$current_page = 'projects';
$title = 'Projects status';
$description = 'Live status of the services behind the projects of Saverio Morelli: websites, web apps, databases, sync servers and email delivery.';
$canonical = '/projects/status/';
$groups = require __DIR__ . '/services.php';

function status_attr(array $s): string
{
    $attrs = ['data-id' => $s['id'], 'data-type' => $s['type']];
    if ($s['type'] === 'page') $attrs['data-url'] = $s['url'];
    $out = '';
    foreach ($attrs as $k => $v) $out .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
    return $out;
}

$total = 0;
foreach ($groups as $g) $total += count($g['services']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__, 2) . '/include/head.php'); ?>
</head>
<body>

<?php include_once(dirname(__DIR__, 2) . '/include/nav.php'); ?>

<div class="page-header">
    <a href="/projects/" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Back to projects
    </a>
    <p class="section-label">Status</p>
    <h1 class="section-title">Projects status</h1>
    <p class="page-description">Live checks of the websites, web apps, databases, sync servers and email delivery behind
        my projects.</p>
</div>

<section class="section status-section">
    <div class="status-hero" id="status-hero" data-state="checking" aria-live="polite">
        <span class="status-hero-dot" aria-hidden="true"></span>
        <div class="status-hero-text">
            <h2 id="status-hero-title">Checking <?= $total ?> services…</h2>
            <p id="status-hero-meta">Running the checks now.</p>
        </div>
        <button type="button" class="status-refresh" id="status-refresh" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <path d="M21 12a9 9 0 1 1-2.64-6.36"/>
                <polyline points="21 3 21 9 15 9"/>
            </svg>
            <span>Check again</span>
        </button>
    </div>

    <ul class="status-legend" aria-label="Legend">
        <li><span class="status-dot" data-status="up"></span>Operational</li>
        <li><span class="status-dot" data-status="degraded"></span>Degraded</li>
        <li><span class="status-dot" data-status="down"></span>Down</li>
    </ul>

    <div class="status-groups">
        <?php foreach ($groups as $g): ?>
            <article class="status-group" data-state="checking">
                <header class="status-group-head">
                    <img class="status-group-icon" src="<?= htmlspecialchars($g['icon']) ?>" alt="" width="28"
                         height="28">
                    <h3><a href="<?= htmlspecialchars($g['url']) ?>"><?= htmlspecialchars($g['name']) ?></a></h3>
                    <?php if (!empty($g['more'])): ?>
                        <a class="status-group-more" href="<?= htmlspecialchars($g['more']['url']) ?>" target="_blank"
                           rel="noopener"><?= htmlspecialchars($g['more']['label']) ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 aria-hidden="true">
                                <path d="M7 17L17 7M17 7H7M17 7v10"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                    <span class="status-group-chip">Checking</span>
                </header>
                <ul class="status-list">
                    <?php foreach ($g['services'] as $s): ?>
                        <li class="status-row" data-status="checking"<?= status_attr($s) ?>>
                            <span class="status-dot" aria-hidden="true"></span>
                            <span class="status-name"><?= htmlspecialchars($s['name']) ?></span>
                            <span class="status-detail">Checking…</span>
                            <span class="status-ms"></span>
                            <span class="status-label">Checking</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>

    <p class="status-note">Checks run from my server (databases, mail servers, external sites) and from your browser
        (web pages), and refresh every minute while this page is open. Response times are measured by whoever runs
        the check, so they also depend on the network in between.</p>
</section>

<?php include_once(dirname(__DIR__, 2) . '/include/footer.php'); ?>

<script>
    (function () {
        var REFRESH_MS = 60000;
        var PAGE_TIMEOUT_MS = 10000;
        var LABELS = {up: 'Operational', degraded: 'Degraded', down: 'Down', checking: 'Checking'};

        var hero = document.getElementById('status-hero');
        var heroTitle = document.getElementById('status-hero-title');
        var heroMeta = document.getElementById('status-hero-meta');
        var refreshBtn = document.getElementById('status-refresh');
        var rows = Array.prototype.slice.call(document.querySelectorAll('.status-row'));
        var groups = Array.prototype.slice.call(document.querySelectorAll('.status-group'));
        var timer = null;
        var running = false;

        function setRow(row, r) {
            row.dataset.status = r.status;
            row.querySelector('.status-detail').textContent = r.detail || '';
            row.querySelector('.status-ms').textContent = typeof r.ms === 'number' ? r.ms + ' ms' : '';
            row.querySelector('.status-label').textContent = LABELS[r.status] || r.status;
        }

        function worst(list) {
            if (list.indexOf('checking') !== -1) return 'checking';
            if (list.indexOf('down') !== -1) return 'down';
            if (list.indexOf('degraded') !== -1) return 'degraded';
            return 'up';
        }

        function summarise(checkedAt) {
            groups.forEach(function (g) {
                var st = worst(Array.prototype.map.call(g.querySelectorAll('.status-row'), function (r) {
                    return r.dataset.status;
                }));
                g.dataset.state = st;
                g.querySelector('.status-group-chip').textContent = LABELS[st];
            });

            var all = rows.map(function (r) { return r.dataset.status; });
            var state = worst(all);
            if (state === 'checking') return;
            var down = all.filter(function (s) { return s === 'down'; }).length;
            var degraded = all.filter(function (s) { return s === 'degraded'; }).length;

            hero.dataset.state = state;
            if (state === 'up') {
                heroTitle.textContent = 'All systems operational';
            } else if (state === 'degraded') {
                heroTitle.textContent = degraded === 1 ? '1 service degraded' : degraded + ' services degraded';
            } else {
                heroTitle.textContent = (down === 1 ? '1 service down' : down + ' services down') +
                    (degraded ? ', ' + degraded + ' degraded' : '');
            }
            var when = checkedAt ? new Date(checkedAt) : new Date();
            heroMeta.textContent = (all.length - down - degraded) + ' of ' + all.length + ' services operational · last checked ' +
                when.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit', second: '2-digit'});
        }

        function checkPage(row) {
            var ctrl = 'AbortController' in window ? new AbortController() : null;
            var t = ctrl ? setTimeout(function () { ctrl.abort(); }, PAGE_TIMEOUT_MS) : null;
            var start = performance.now();
            return fetch(row.dataset.url, {method: 'HEAD', cache: 'no-store', redirect: 'follow', signal: ctrl ? ctrl.signal : undefined})
                .then(function (res) {
                    var ms = Math.round(performance.now() - start);
                    if (!res.ok) return {status: 'down', detail: 'HTTP ' + res.status, ms: ms};
                    if (ms > 4000) return {status: 'degraded', detail: 'Slow response', ms: ms};
                    return {status: 'up', detail: 'Online', ms: ms};
                })
                .catch(function (e) {
                    var aborted = e && e.name === 'AbortError';
                    return {status: 'down', detail: aborted ? 'Timed out' : 'Not reachable', ms: null};
                })
                .then(function (r) {
                    if (t) clearTimeout(t);
                    setRow(row, r);
                });
        }

        function checkServer(force) {
            var serverRows = rows.filter(function (r) { return r.dataset.type !== 'page'; });
            return fetch('check.php' + (force ? '?refresh=1' : ''), {cache: 'no-store', headers: {Accept: 'application/json'}})
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    var results = (json && json.results) || {};
                    serverRows.forEach(function (row) {
                        setRow(row, results[row.dataset.id] || {status: 'down', detail: 'No result', ms: null});
                    });
                    return json.checkedAt;
                })
                .catch(function () {
                    serverRows.forEach(function (row) {
                        setRow(row, {status: 'down', detail: 'Status checker not reachable', ms: null});
                    });
                    return null;
                });
        }

        function run(force) {
            if (running) return;
            running = true;
            refreshBtn.disabled = true;
            hero.dataset.state = 'checking';
            heroTitle.textContent = 'Checking ' + rows.length + ' services…';
            rows.forEach(function (r) {
                r.dataset.status = 'checking';
                r.querySelector('.status-label').textContent = LABELS.checking;
            });
            summarise();

            var pages = rows.filter(function (r) { return r.dataset.type === 'page'; }).map(checkPage);
            var server = checkServer(force);
            Promise.all([server].concat(pages)).then(function (out) {
                running = false;
                refreshBtn.disabled = false;
                summarise(out[0]);
                schedule();
            });
        }

        function schedule() {
            clearTimeout(timer);
            timer = setTimeout(function () {
                if (document.visibilityState === 'visible') run(false); else schedule();
            }, REFRESH_MS);
        }

        refreshBtn.addEventListener('click', function () { run(true); });
        run(false);
    })();
</script>
</body>
</html>
