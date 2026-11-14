<?php
/**
 * Fucina — draws a certificate as an image, on lib/certificate/template.png (see tools/build-certificate.py).
 * It is always in English, so that it can be shown anywhere. The fonts are in lib/certificate/fonts, all under the OFL.
 */

declare(strict_types=1);

const CERT_ACCENTS = ['html' => [217, 58, 20], 'css' => [31, 111, 201], 'scss' => [199, 42, 107], 'sass' => [199, 42, 107], 'js' => [176, 124, 0], 'ts' => [18, 92, 158], 'php' => [82, 48, 186]];

function cert_font(string $name): string
{
    return __DIR__ . '/certificate/fonts/' . $name . '.ttf';
}

/** GD measures in points at 96 dpi: sizes here are pixels. */
function cert_width(string $font, float $px, string $text): int
{
    $b = imagettfbbox($px * .75, 0, cert_font($font), $text);
    return abs($b[2] - $b[0]);
}

function cert_text($img, string $font, float $px, int $x, int $y, $colour, string $text): void
{
    imagettftext($img, $px * .75, 0, $x - intdiv(cert_width($font, $px, $text), 2), $y, $colour, cert_font($font), $text);
}

/** The name on one line, as large as it fits, or on two lines when it is very long. */
function cert_name_lines(string $name, int $max): array
{
    for ($px = 158; $px >= 104; $px -= 6) if (cert_width('Gelasio-Bold', $px, $name) <= $max) return [$px, [$name]];
    $words = preg_split('/\s+/', $name) ?: [$name];
    $half = (int)ceil(count($words) / 2);
    $lines = [implode(' ', array_slice($words, 0, $half)), implode(' ', array_slice($words, $half))];
    $px = 118;
    while ($px > 40 && max(cert_width('Gelasio-Bold', $px, $lines[0]), cert_width('Gelasio-Bold', $px, $lines[1])) > $max) $px -= 4;
    return [$px, array_values(array_filter($lines, 'strlen'))];
}

const CERT_LANGUAGES = ['it' => 'Italian', 'en' => 'English'];

/** The picture of a certificate: name, course title, family, score, code, date (Y-m-d H:i:s, UTC) and the language it was taken in. */
function certificate_image(string $name, string $course, string $family, int $score, string $code, string $issued, string $lang = 'en')
{
    $img = imagecreatefrompng(__DIR__ . '/certificate/template.png');
    imagealphablending($img, true);
    $ink = imagecolorallocate($img, 27, 27, 75);
    $soft = imagecolorallocate($img, 90, 84, 120);
    $gold = imagecolorallocate($img, 185, 138, 46);
    $accent = imagecolorallocate($img, ...(CERT_ACCENTS[$family] ?? CERT_ACCENTS['html']));
    $mid = 1200;

    cert_text($img, 'Gelasio-Italic', 104, $mid, 452, $ink, 'Certificate of completion' . ($score >= 100 ? ' with honours' : ''));
    cert_text($img, 'Gelasio-Regular', 44, $mid, 552, $soft, 'This is proudly awarded to');
    [$px, $lines] = cert_name_lines($name, 1900);
    $y = 700;
    foreach ($lines as $i => $line) {
        if ($i) $y += (int)($px * 1.12);
        cert_text($img, 'Gelasio-Bold', $px, $mid, $y, $ink, $line);
    }
    $y += 54;
    imagesetthickness($img, 2);
    imageline($img, $mid - 520, $y, $mid - 26, $y, $gold);
    imageline($img, $mid + 26, $y, $mid + 520, $y, $gold);
    imagefilledpolygon($img, [$mid, $y - 11, $mid + 11, $y, $mid, $y + 11, $mid - 11, $y], $gold);
    cert_text($img, 'Gelasio-Regular', 44, $mid, $y + 84, $soft, 'for completing the course and passing its final exam');
    cert_text($img, 'Gelasio-Bold', 88, $mid, $y + 196, $accent, $course);
    $language = CERT_LANGUAGES[$lang] ?? 'English';
    cert_text($img, 'JetBrainsMono-Medium', 32, $mid, $y + 268, $soft, 'Score ' . ($score / 10) . " of 10  ·  Taken in {$language}  ·  No. {$code}");

    $date = (new DateTimeImmutable($issued, new DateTimeZone('UTC')))->format('j M Y');
    cert_text($img, 'JetBrainsMono-Medium', 46, 530, 1472, $ink, $date);
    cert_text($img, 'Gelasio-Regular', 32, 530, 1534, $soft, 'Date');
    cert_text($img, 'Comforter-Regular', 128, 1870, 1472, $ink, 'Saverio Morelli');
    cert_text($img, 'Gelasio-Regular', 32, 1870, 1534, $soft, 'Founder of Fucina');
    cert_text($img, 'Gelasio-Regular', 25, $mid, 1580, $soft, 'Personal achievement issued by Fucina, with no legal value. Fucina is a project curated by Saverio Morelli.');
    cert_text($img, 'Gelasio-Regular', 25, $mid, 1614, $soft, 'Its authenticity can be checked with its number on the official Fucina website.');
    return $img;
}

/** An A4 landscape PDF with the picture on the whole page. */
function certificate_pdf($img): string
{
    ob_start();
    imagejpeg($img, null, 92);
    $jpeg = (string)ob_get_clean();
    $w = imagesx($img);
    $h = imagesy($img);
    $content = "q 841.89 0 0 595.28 0 0 cm /Im0 Do Q";
    $objects = [
        '<< /Type /Catalog /Pages 2 0 R >>',
        '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
        '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 841.89 595.28] /Resources << /XObject << /Im0 4 0 R >> >> /Contents 5 0 R >>',
        "<< /Type /XObject /Subtype /Image /Width $w /Height $h /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($jpeg) . " >>\nstream\n$jpeg\nendstream",
        '<< /Length ' . strlen($content) . " >>\nstream\n$content\nendstream",
    ];
    $pdf = "%PDF-1.4\n";
    $offsets = [];
    foreach ($objects as $i => $o) {
        $offsets[] = strlen($pdf);
        $pdf .= ($i + 1) . " 0 obj\n$o\nendobj\n";
    }
    $xref = strlen($pdf);
    $pdf .= 'xref' . "\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
    foreach ($offsets as $o) $pdf .= sprintf("%010d 00000 n \n", $o);
    return $pdf . 'trailer << /Size ' . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xref\n%%EOF\n";
}
