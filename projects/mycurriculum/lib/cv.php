<?php
/**
 * MyCurriculum — the CV document: cleaning what the editor sends, and the public copy.
 *
 * settings  { template, accent, font, langs[], defaultLang, public, allowPdf, show{photo,email,phone,website,links,birth,location} }
 * profile   { firstName, lastName, email, phone, website, birthDate, links[{label,url}] }   (shared by every language)
 * content   { <lang>: { headline, location, summary, fields[{label,value,show}], sections[], footer } }
 * section   { id, type (timeline|skills|langs|list|text), kind, title, show, text, items[] }
 *
 * Everything the browser sends is rebuilt here field by field: unknown keys are dropped, strings are cut,
 * lists are capped. The public copy (web page and PDF) never contains what the owner chose to hide.
 */

declare(strict_types=1);

const TEMPLATES = ['classic', 'modern', 'minimal', 'elegant'];
const SECTION_TYPES = ['timeline', 'skills', 'langs', 'list', 'text'];
const SHOW_KEYS = ['photo', 'email', 'phone', 'website', 'links', 'birth', 'location'];

function str_of($v, int $max): string
{
    if (!is_string($v) && !is_numeric($v)) return '';
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string)$v) ?? '';
    $v = str_replace("\r\n", "\n", $v);
    return mb_substr(trim($v), 0, $max);
}

function bool_of($v): bool
{
    return $v === true || $v === 1 || $v === '1';
}

function list_of($v, int $max): array
{
    return is_array($v) ? array_slice(array_values(array_filter($v, 'is_array')), 0, $max) : [];
}

/** Only http(s) and mailto links, so a CV page can never carry a javascript: link. */
function url_of($v): string
{
    $u = str_of($v, 300);
    if ($u === '') return '';
    if (stripos($u, 'mailto:') === 0) return filter_var(substr($u, 7), FILTER_VALIDATE_EMAIL) ? 'mailto:' . substr($u, 7) : '';
    if (!preg_match('~^https?://~i', $u)) $u = 'https://' . ltrim($u, '/');
    return filter_var($u, FILTER_VALIDATE_URL) ? $u : '';
}

function default_settings(string $lang): array
{
    return [
        'template' => 'modern', 'accent' => '#3D52D5', 'font' => 'sans',
        'langs' => [$lang], 'defaultLang' => $lang, 'public' => false, 'allowPdf' => true,
        'show' => ['photo' => true, 'email' => true, 'phone' => true, 'website' => true, 'links' => true, 'birth' => false, 'location' => true],
    ];
}

function clean_settings($s, array $contentLangs): array
{
    $s = is_array($s) ? $s : [];
    $langs = [];
    foreach ((array)($s['langs'] ?? []) as $l) if (in_array($l, CV_LANGS, true) && in_array($l, $contentLangs, true) && !in_array($l, $langs, true)) $langs[] = $l;
    foreach ($contentLangs as $l) if (!in_array($l, $langs, true)) $langs[] = $l;
    $show = [];
    foreach (SHOW_KEYS as $k) $show[$k] = bool_of($s['show'][$k] ?? false);
    $accent = (string)($s['accent'] ?? '');
    return [
        'template' => in_array($s['template'] ?? '', TEMPLATES, true) ? $s['template'] : 'modern',
        'accent' => preg_match('/^#[0-9a-fA-F]{6}$/', $accent) ? strtoupper($accent) : '#3D52D5',
        'font' => ($s['font'] ?? '') === 'serif' ? 'serif' : 'sans',
        'langs' => $langs,
        'defaultLang' => in_array($s['defaultLang'] ?? '', $langs, true) ? $s['defaultLang'] : ($langs[0] ?? 'en'),
        'public' => bool_of($s['public'] ?? false),
        'allowPdf' => bool_of($s['allowPdf'] ?? true),
        'show' => $show,
    ];
}

function clean_profile($p): array
{
    $p = is_array($p) ? $p : [];
    $links = [];
    foreach (list_of($p['links'] ?? [], 8) as $l) {
        $url = url_of($l['url'] ?? '');
        if ($url !== '') $links[] = ['label' => str_of($l['label'] ?? '', 40), 'url' => $url];
    }
    $birth = str_of($p['birthDate'] ?? '', 10);
    $email = str_of($p['email'] ?? '', 190);
    return [
        'firstName' => str_of($p['firstName'] ?? '', 60),
        'lastName' => str_of($p['lastName'] ?? '', 60),
        'email' => $email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '',
        'phone' => str_of($p['phone'] ?? '', 40),
        'website' => url_of($p['website'] ?? ''),
        'birthDate' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth) ? $birth : '',
        'links' => $links,
    ];
}

function clean_item(string $type, array $i): array
{
    switch ($type) {
        case 'timeline':
            return [
                'title' => str_of($i['title'] ?? '', 120), 'org' => str_of($i['org'] ?? '', 120), 'place' => str_of($i['place'] ?? '', 80),
                'start' => str_of($i['start'] ?? '', 20), 'end' => str_of($i['end'] ?? '', 20), 'current' => bool_of($i['current'] ?? false),
                'text' => str_of($i['text'] ?? '', 3000),
            ];
        case 'skills':
            return ['name' => str_of($i['name'] ?? '', 80), 'level' => max(0, min(5, (int)($i['level'] ?? 0)))];
        case 'langs':
            return ['name' => str_of($i['name'] ?? '', 60), 'level' => str_of($i['level'] ?? '', 60)];
        default: // list
            return [
                'title' => str_of($i['title'] ?? '', 160), 'subtitle' => str_of($i['subtitle'] ?? '', 160), 'date' => str_of($i['date'] ?? '', 40),
                'url' => url_of($i['url'] ?? ''), 'text' => str_of($i['text'] ?? '', 2000),
            ];
    }
}

function clean_lang_content($c): array
{
    $c = is_array($c) ? $c : [];
    $fields = [];
    foreach (list_of($c['fields'] ?? [], 12) as $f) {
        $f = ['label' => str_of($f['label'] ?? '', 40), 'value' => str_of($f['value'] ?? '', 200), 'show' => bool_of($f['show'] ?? true)];
        if ($f['label'] !== '' || $f['value'] !== '') $fields[] = $f;
    }
    $sections = [];
    foreach (list_of($c['sections'] ?? [], 20) as $s) {
        $type = in_array($s['type'] ?? '', SECTION_TYPES, true) ? $s['type'] : 'list';
        $items = [];
        foreach (list_of($s['items'] ?? [], 40) as $i) $items[] = clean_item($type, $i);
        $id = preg_match('/^[a-z0-9]{1,16}$/', (string)($s['id'] ?? '')) ? $s['id'] : substr(bin2hex(random_bytes(4)), 0, 8);
        $sections[] = [
            'id' => $id, 'type' => $type, 'kind' => preg_replace('/[^a-z]/', '', str_of($s['kind'] ?? '', 20)),
            'title' => str_of($s['title'] ?? '', 80), 'show' => bool_of($s['show'] ?? true),
            'text' => $type === 'text' ? str_of($s['text'] ?? '', 4000) : '', 'items' => $type === 'text' ? [] : $items,
        ];
    }
    return [
        'headline' => str_of($c['headline'] ?? '', 120),
        'location' => str_of($c['location'] ?? '', 120),
        'summary' => str_of($c['summary'] ?? '', 4000),
        'fields' => $fields,
        'sections' => $sections,
        'footer' => str_of($c['footer'] ?? '', 1000),
    ];
}

function clean_content($c): array
{
    $out = [];
    if (!is_array($c)) return $out;
    foreach ($c as $lang => $v) if (in_array($lang, CV_LANGS, true)) $out[$lang] = clean_lang_content($v);
    return $out;
}

// ---------------------------------------------------------------- storage

function load_cv(int $uid): ?array
{
    $st = db()->prepare('SELECT * FROM ' . t('cvs') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    $r = $st->fetch();
    if (!$r) return null;
    $content = json_decode((string)$r['content'], true) ?: [];
    return [
        'settings' => clean_settings(json_decode((string)$r['settings'], true), array_keys($content)),
        'profile' => clean_profile(json_decode((string)$r['profile'], true)),
        'content' => $content,
        'views' => (int)$r['views'],
        'updatedAt' => $r['updated_at'],
    ];
}

function save_cv(int $uid, array $settings, array $profile, array $content): void
{
    $now = now_utc();
    db()->prepare('INSERT INTO ' . t('cvs') . ' (user_id, settings, profile, content, created_at, updated_at) VALUES (?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE settings = VALUES(settings), profile = VALUES(profile), content = VALUES(content), updated_at = VALUES(updated_at)')
        ->execute([$uid, json_out($settings), json_out($profile), json_out($content), $now, $now]);
}

function json_out($v): string
{
    return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function photo_hash(int $uid): ?string
{
    $st = db()->prepare('SELECT hash FROM ' . t('photos') . ' WHERE user_id = ?');
    $st->execute([$uid]);
    $h = $st->fetchColumn();
    return $h ? (string)$h : null;
}

/** A new account starts with the profile it can already know and the usual sections, titled in its language. */
function new_cv(array $u, string $lang): array
{
    $lang = in_array($lang, CV_LANGS, true) ? $lang : 'en';
    $parts = preg_split('/\s+/', trim((string)$u['name']), 2);
    return [
        'settings' => default_settings($lang),
        'profile' => clean_profile(['firstName' => $parts[0] ?? '', 'lastName' => $parts[1] ?? '', 'email' => $u['email']]),
        'content' => [$lang => clean_lang_content(['sections' => [
            ['id' => 'exp', 'type' => 'timeline', 'kind' => 'experience', 'title' => section_title('experience', $lang)],
            ['id' => 'edu', 'type' => 'timeline', 'kind' => 'education', 'title' => section_title('education', $lang)],
            ['id' => 'skl', 'type' => 'skills', 'kind' => 'skills', 'title' => section_title('skills', $lang)],
            ['id' => 'lng', 'type' => 'langs', 'kind' => 'languages', 'title' => section_title('languages', $lang)],
        ]])],
    ];
}

function section_title(string $kind, string $lang): string
{
    static $T = [
        'experience' => ['it' => 'Esperienza professionale', 'en' => 'Work experience', 'fr' => 'Expérience professionnelle', 'de' => 'Berufserfahrung', 'es' => 'Experiencia laboral', 'pt' => 'Experiência profissional', 'nl' => 'Werkervaring', 'pl' => 'Doświadczenie zawodowe', 'ro' => 'Experiență profesională', 'sv' => 'Arbetslivserfarenhet'],
        'education' => ['it' => 'Istruzione e formazione', 'en' => 'Education', 'fr' => 'Formation', 'de' => 'Ausbildung', 'es' => 'Formación', 'pt' => 'Formação', 'nl' => 'Opleiding', 'pl' => 'Wykształcenie', 'ro' => 'Educație', 'sv' => 'Utbildning'],
        'skills' => ['it' => 'Competenze', 'en' => 'Skills', 'fr' => 'Compétences', 'de' => 'Kenntnisse', 'es' => 'Habilidades', 'pt' => 'Competências', 'nl' => 'Vaardigheden', 'pl' => 'Umiejętności', 'ro' => 'Competențe', 'sv' => 'Färdigheter'],
        'languages' => ['it' => 'Lingue', 'en' => 'Languages', 'fr' => 'Langues', 'de' => 'Sprachen', 'es' => 'Idiomas', 'pt' => 'Idiomas', 'nl' => 'Talen', 'pl' => 'Języki', 'ro' => 'Limbi', 'sv' => 'Språk'],
    ];
    return $T[$kind][$lang] ?? $T[$kind]['en'] ?? '';
}

// ---------------------------------------------------------------- the public copy

function user_by_username(string $username): ?array
{
    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9-]{1,28}[A-Za-z0-9]$/', $username)) return null;
    $st = db()->prepare('SELECT * FROM ' . t('users') . ' WHERE username = ? AND status = \'active\'');
    $st->execute([$username]);
    return $st->fetch() ?: null;
}

/**
 * What a visitor may see: the same data the PDF shows. Hidden contacts, fields and sections are removed here,
 * on the server, so they never reach the browser of a visitor.
 */
function public_cv(array $user, array $cv): array
{
    $s = $cv['settings'];
    $p = $cv['profile'];
    $show = $s['show'];
    $profile = [
        'firstName' => $p['firstName'], 'lastName' => $p['lastName'],
        'email' => $show['email'] ? $p['email'] : '', 'phone' => $show['phone'] ? $p['phone'] : '',
        'website' => $show['website'] ? $p['website'] : '', 'birthDate' => $show['birth'] ? $p['birthDate'] : '',
        'links' => $show['links'] ? $p['links'] : [],
    ];
    $content = [];
    foreach ($s['langs'] as $lang) {
        $c = clean_lang_content($cv['content'][$lang] ?? []);
        if (!$show['location']) $c['location'] = '';
        $c['fields'] = array_values(array_map(fn($f) => ['label' => $f['label'], 'value' => $f['value'], 'show' => true], array_filter($c['fields'], fn($f) => $f['show'])));
        $c['sections'] = array_values(array_filter($c['sections'], fn($x) => $x['show']));
        $content[$lang] = $c;
    }
    $hash = $show['photo'] ? photo_hash((int)$user['id']) : null;
    return [
        'username' => $user['username'],
        'settings' => ['template' => $s['template'], 'accent' => $s['accent'], 'font' => $s['font'], 'langs' => $s['langs'], 'defaultLang' => $s['defaultLang'], 'allowPdf' => $s['allowPdf'], 'show' => $show],
        'profile' => $profile,
        'content' => $content,
        'photo' => $hash ? 'api/?r=cv.photo&user=' . rawurlencode($user['username']) . '&v=' . $hash : null,
    ];
}
