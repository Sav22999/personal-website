<?php
/**
 * Giftwise — link preview: reads title, image, price and shop name of a product page
 * (Open Graph, Twitter cards, JSON-LD, microdata), so a wish can be added just by pasting/sharing a link.
 *
 * Security (SSRF): only http(s) on ports 80/443, the host must resolve to public IP addresses only,
 * the connection is pinned to the checked address (CURLOPT_RESOLVE), redirects are followed manually
 * (each hop is checked again), small timeouts and at most 1.5 MB are read.
 */

declare(strict_types=1);

function preview_link(string $url): array
{
    $out = ['url' => $url, 'title' => '', 'image' => '', 'price' => null, 'currency' => '', 'site' => ''];
    $html = '';
    $final = $url;
    for ($hop = 0; $hop < 4; $hop++) {
        $r = preview_fetch($final);
        if ($r === null) break;
        if ($r['redirect']) { $final = preview_absolute($r['redirect'], $final); continue; }
        $html = $r['body'];
        break;
    }
    $out['url'] = $final;
    $host = (string)parse_url($final, PHP_URL_HOST);
    $out['site'] = preg_replace('/^(www|m|smile)\./i', '', $host);
    if ($html !== '') {
        $meta = preview_meta($html);
        $out['title'] = $meta['og:title'] ?? $meta['twitter:title'] ?? '';
        if ($out['title'] === '' && preg_match('#<title[^>]*>(.*?)</title>#is', $html, $m)) $out['title'] = $m[1];
        $img = $meta['og:image:secure_url'] ?? $meta['og:image'] ?? $meta['twitter:image'] ?? $meta['twitter:image:src'] ?? '';
        if ($img === '' && preg_match('#<link[^>]+rel=["\']image_src["\'][^>]*href=["\']([^"\']+)#i', $html, $m)) $img = $m[1];
        if ($img === '' && preg_match('#id=["\']landingImage["\'][^>]*(?:data-old-hires|src)=["\']([^"\']+)#i', $html, $m)) $img = $m[1]; // Amazon
        if ($img !== '') $out['image'] = preview_absolute(html_entity_decode($img, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $final);
        $price = $meta['product:price:amount'] ?? $meta['og:price:amount'] ?? '';
        $cur = $meta['product:price:currency'] ?? $meta['og:price:currency'] ?? '';
        if ($price === '') [$price, $cur] = preview_jsonld_price($html, $cur);
        if ($price === '' && preg_match('#itemprop=["\']price["\'][^>]*content=["\']([0-9.,]+)#i', $html, $m)) $price = $m[1];
        if ($price !== '') $out['price'] = preview_number($price);
        $out['currency'] = in_array(strtoupper($cur), CURRENCIES, true) ? strtoupper($cur) : '';
        if (!empty($meta['og:site_name'])) $out['site'] = $meta['og:site_name'];
    }
    // Some shops (e.g. Amazon) often answer bots with a captcha: use the readable part of the URL as a title
    $title = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string)$out['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    if ($title === '' || preg_match('/^(amazon\.[a-z.]+|robot check|captcha|access denied|just a moment)/i', $title)) $title = preview_title_from_url($final);
    $out['title'] = mb_substr($title, 0, 160);
    $out['site'] = mb_substr(trim(html_entity_decode((string)$out['site'], ENT_QUOTES | ENT_HTML5, 'UTF-8')), 0, 60);
    if (strlen($out['image']) > 2000 || !preg_match('#^https?://#i', $out['image'])) $out['image'] = '';
    return $out;
}

/** One HTTP GET without following redirects. Returns ['redirect' => url|'', 'body' => html] or null. */
function preview_fetch(string $url): ?array
{
    $p = parse_url($url);
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http', 'https'], true) || empty($p['host'])) return null;
    $port = (int)($p['port'] ?? (strtolower($p['scheme']) === 'https' ? 443 : 80));
    if (!in_array($port, [80, 443], true)) return null;
    $host = strtolower($p['host']);
    $ips = @gethostbynamel($host);
    if (!$ips) return null;
    foreach ($ips as $ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return null; // internal network: refused
    }
    if (!function_exists('curl_init')) return null;
    $body = '';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RESOLVE => [$host . ':' . $port . ':' . $ips[0]],
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_HEADER => false,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT => 7,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_ENCODING => '',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Mobile Safari/537.36 Giftwise/1.0',
        CURLOPT_HTTPHEADER => ['Accept: text/html,application/xhtml+xml;q=0.9,*/*;q=0.5', 'Accept-Language: ' . (lang() === 'it' ? 'it-IT,it;q=0.9,en;q=0.6' : 'en-GB,en;q=0.9')],
        CURLOPT_WRITEFUNCTION => function ($ch, $chunk) use (&$body) {
            $body .= $chunk;
            return strlen($body) > 1500000 ? 0 : strlen($chunk); // stop after 1.5 MB
        },
    ]);
    curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc = (string)(curl_getinfo($ch, CURLINFO_REDIRECT_URL) ?: '');
    $type = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    if ($code >= 300 && $code < 400 && $loc !== '') return ['redirect' => $loc, 'body' => ''];
    if ($type !== '' && stripos($type, 'html') === false) return ['redirect' => '', 'body' => ''];
    return ['redirect' => '', 'body' => $body];
}

/** <meta property|name="..." content="..."> in a lower-cased map (first value wins). */
function preview_meta(string $html): array
{
    $meta = [];
    if (preg_match_all('#<meta\s[^>]*>#i', $html, $all)) {
        foreach ($all[0] as $tag) {
            if (!preg_match('#(?:property|name|itemprop)\s*=\s*["\']([^"\']+)["\']#i', $tag, $k)) continue;
            if (!preg_match('#content\s*=\s*("([^"]*)"|\'([^\']*)\')#i', $tag, $v)) continue;
            $key = strtolower(trim($k[1]));
            $val = html_entity_decode($v[2] !== '' ? $v[2] : ($v[3] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (!isset($meta[$key]) && trim($val) !== '') $meta[$key] = trim($val);
        }
    }
    return $meta;
}

/** Price from schema.org JSON-LD (Product → offers). */
function preview_jsonld_price(string $html, string $cur): array
{
    if (!preg_match_all('#<script[^>]+application/ld\+json[^>]*>(.*?)</script>#is', $html, $all)) return ['', $cur];
    foreach ($all[1] as $json) {
        $d = json_decode(trim($json), true);
        if (!is_array($d)) continue;
        $stack = [$d];
        while ($stack) {
            $n = array_pop($stack);
            if (!is_array($n)) continue;
            if (isset($n['offers'])) {
                $o = isset($n['offers'][0]) ? $n['offers'][0] : $n['offers'];
                $price = $o['price'] ?? $o['lowPrice'] ?? '';
                if ($price !== '' && is_scalar($price)) return [(string)$price, (string)($o['priceCurrency'] ?? $cur)];
            }
            foreach ($n as $v) if (is_array($v)) $stack[] = $v;
        }
    }
    return ['', $cur];
}

function preview_number(string $s): ?float
{
    $s = preg_replace('/[^0-9.,]/', '', $s);
    if ($s === '') return null;
    if (strpos($s, ',') !== false && strpos($s, '.') !== false) $s = strrpos($s, ',') > strrpos($s, '.') ? str_replace(['.', ','], ['', '.'], $s) : str_replace(',', '', $s);
    else $s = str_replace(',', '.', $s);
    $n = (float)$s;
    return $n > 0 && $n < 100000000 ? round($n, 2) : null;
}

function preview_absolute(string $u, string $base): string
{
    if (preg_match('#^https?://#i', $u)) return $u;
    $b = parse_url($base);
    if (!$b || empty($b['host'])) return $u;
    $root = $b['scheme'] . '://' . $b['host'];
    if (strpos($u, '//') === 0) return $b['scheme'] . ':' . $u;
    if (strpos($u, '/') === 0) return $root . $u;
    $dir = isset($b['path']) ? preg_replace('#/[^/]*$#', '/', $b['path']) : '/';
    return $root . $dir . $u;
}

/** "https://www.amazon.it/Lego-Classic-Mattoncini-Creativi/dp/B00NHQFA1I" → "Lego Classic Mattoncini Creativi". */
function preview_title_from_url(string $url): string
{
    $path = (string)parse_url($url, PHP_URL_PATH);
    $best = '';
    foreach (explode('/', $path) as $seg) {
        $seg = urldecode($seg);
        if (preg_match('/^(dp|gp|product|p|d|item|itm|ref=.*|[A-Z0-9]{8,12}|\d+)$/i', $seg)) continue;
        $seg = preg_replace('/\.(html?|php|aspx?)$/i', '', $seg);
        if (substr_count($seg, '-') + substr_count($seg, '_') >= 1 && mb_strlen($seg) > mb_strlen($best)) $best = $seg;
    }
    $t = trim(preg_replace('/[-_]+/', ' ', $best));
    return $t !== '' ? mb_strtoupper(mb_substr($t, 0, 1)) . mb_substr($t, 1) : '';
}
