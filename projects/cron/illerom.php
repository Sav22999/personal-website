<?php
/**
 * The jobs of the projects moved to www.illerom.eu run there: the scripts here forward to them while ../.illerom-live
 * exists, so the jobs already scheduled on this address keep working. The key is the same on both sites.
 */

/** Runs the same script on illerom.eu: [HTTP status, its answer]. null = not moved yet (run it here). */
function illerom_cron(string $script): ?array
{
    if (!is_file(__DIR__ . '/../.illerom-live') || !function_exists('curl_init')) return null;
    $credentials = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
    $query = ['key' => (string)($credentials['cron_key'] ?? '')];
    if (isset($_GET['force']) || in_array('--force', $GLOBALS['argv'] ?? [], true)) $query['force'] = 1;
    @set_time_limit(320);
    $ch = curl_init("https://www.illerom.eu/cron/$script?" . http_build_query($query));
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 15, CURLOPT_TIMEOUT => 300]);
    $body = json_decode((string)curl_exec($ch), true);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return [$code ?: 502, is_array($body) ? $body : ['error' => 'no answer from www.illerom.eu']];
}
