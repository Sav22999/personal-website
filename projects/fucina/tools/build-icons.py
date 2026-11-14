"""Regenerates every icon of Fucina (web, PWA, Android, Play Store).
The mark: two code brackets of glowing metal with a bolt of light between them, on midnight blue.
Needs Pillow: python3 tools/build-icons.py"""
from pathlib import Path
from PIL import Image, ImageChops, ImageDraw, ImageFilter

ROOT = Path(__file__).resolve().parent.parent
NIGHT_TOP, NIGHT_BOTTOM = (43, 40, 118), (17, 16, 58)
HOT_TOP, HOT_BOTTOM = (255, 214, 92), (255, 72, 28)
BOLT = (255, 244, 205, 255)
STEEL = (240, 236, 255, 255)
SS = 4  # drawn larger and scaled down for smooth edges


def gradient(s, top, bottom, y0=0.0, y1=1.0):
    """A vertical gradient: top colour above y0, bottom colour below y1 (fractions of the side)."""
    col = Image.new('RGB', (1, 256))
    for y in range(256):
        t = min(1, max(0, (y / 255 - y0) / (y1 - y0)))
        col.putpixel((0, y), tuple(round(a + (b - a) * t) for a, b in zip(top, bottom)))
    return col.resize((s, s), Image.BICUBIC)


def stroke(d, pts, width):
    """A thick line with round ends and joints."""
    d.line(pts, fill=255, width=width)
    r = width / 2
    for x, y in pts:
        d.ellipse((x - r, y - r, x + r, y + r), fill=255)


def rounded(size, pts, radius):
    """A polygon with soft corners, as a mask."""
    m = Image.new('L', (size, size), 0)
    ImageDraw.Draw(m).polygon(pts, fill=255)
    m = m.filter(ImageFilter.GaussianBlur(radius))
    return m.point(lambda v: 255 if v > 127 else 0)


def mark(s, scale, anvil=True, detail=True):
    """The mark on a transparent square of side s, drawn in a 100×100 grid."""
    k = s / 100 * scale
    o = s * (1 - scale) / 2
    P = lambda pts: [(x * k + o, y * k + o) for x, y in pts]
    img = Image.new('RGBA', (s, s), (0, 0, 0, 0))
    cy = 41 if anvil else 50
    half = 21 if anvil else 25
    # Brackets: metal just out of the fire, white-hot at the top
    mask = Image.new('L', (s, s), 0)
    d = ImageDraw.Draw(mask)
    w = round(11.5 * k)
    stroke(d, P([(27, cy - half), (8.5, cy), (27, cy + half)]), w)
    stroke(d, P([(73, cy - half), (91.5, cy), (73, cy + half)]), w)
    hot = gradient(s, HOT_TOP, HOT_BOTTOM, .12, .78).convert('RGBA')
    # A soft glow behind everything hot
    glow = Image.new('RGBA', (s, s), (255, 96, 30, 0))
    glow.putalpha(mask.filter(ImageFilter.GaussianBlur(5 * k)).point(lambda v: int(v * .55)))
    bolt_pts = [(57, 4), (33, 47), (47.5, 47), (41, 78), (68, 33), (53, 33)] if anvil else [(58, 8), (31, 55), (47, 55), (40, 92), (70, 41), (54, 41)]
    bolt = rounded(s, P(bolt_pts), 1.1 * k)
    glow2 = Image.new('RGBA', (s, s), (255, 197, 61, 0))
    glow2.putalpha(bolt.filter(ImageFilter.GaussianBlur(6 * k)).point(lambda v: int(v * .7)))
    if detail:
        img.alpha_composite(glow)
        img.alpha_composite(glow2)
    img.paste(hot, (0, 0), mask)
    if anvil:
        # The anvil: horn on the left, waist, and a wide foot
        body = P([(16, 72), (84, 72), (84, 77), (70, 80), (63, 84), (63, 89), (73, 94), (73, 98), (27, 98), (27, 94), (37, 89), (37, 84), (30, 80), (16, 77)])
        a = rounded(s, body, .8 * k)
        img.paste(gradient(s, (232, 228, 255), (150, 146, 214), .7, 1).convert('RGBA'), (0, 0), a)
    img.paste(Image.new('RGBA', (s, s), BOLT), (0, 0), bolt)
    if detail:
        d = ImageDraw.Draw(img)
        sparks = [(24, 66, 1.7), (15, 58, 1.2), (76, 66, 1.7), (86, 59, 1.2), (66, 58, 1.1), (33, 60, 1.0)] if anvil else [(30, 14, 1.7), (72, 88, 1.7), (20, 20, 1.1), (82, 82, 1.1)]
        for x, y, r in sparks:
            cx, cy2, rr = x * k + o, y * k + o, r * k
            d.ellipse((cx - rr, cy2 - rr, cx + rr, cy2 + rr), fill=(255, 214, 92, 255))
    return img


def icon(size, rounded_corners=True, scale=.82, background=True):
    s = size * SS
    img = Image.new('RGBA', (s, s), (0, 0, 0, 0))
    if background:
        bg = gradient(s, NIGHT_TOP, NIGHT_BOTTOM).convert('RGBA')
        if rounded_corners:
            shape = Image.new('L', (s, s), 0)
            ImageDraw.Draw(shape).rounded_rectangle((0, 0, s - 1, s - 1), radius=s * .23, fill=255)
            img.paste(bg, (0, 0), shape)
        else:
            img = bg
    img.alpha_composite(mark(s, scale, anvil=False, detail=size >= 64))
    return img.resize((size, size), Image.LANCZOS)


out = ROOT / 'assets/icons'
out.mkdir(parents=True, exist_ok=True)
icon(32).save(out / 'favicon-32.png')
icon(192).save(out / 'icon-192.png')
icon(512).save(out / 'icon-512.png')
icon(512, rounded_corners=False, scale=.66).save(out / 'icon-maskable-512.png')
icon(180, rounded_corners=False).save(out / 'apple-touch-icon.png')
(out / 'favicon.svg').write_text('''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="n" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2B2876"/><stop offset="1" stop-color="#11103A"/></linearGradient><linearGradient id="h" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFD65C"/><stop offset="1" stop-color="#FF481C"/></linearGradient></defs><rect width="100" height="100" rx="23" fill="url(#n)"/><g transform="translate(9 9) scale(.82)"><path d="M27 25 8.500 50 27 75M73 25l18.500 25L73 75" fill="none" stroke="url(#h)" stroke-width="11.500" stroke-linecap="round" stroke-linejoin="round"/><path fill="#FFF4CD" d="M58 8 31 55h16l-7 37 30-51H54z"/></g></svg>
''')
icon(512, rounded_corners=False).save(ROOT / 'android/play-store-icon-512.png')
res = ROOT / 'android/app/src/main/res'
for name, px in {'mdpi': 48, 'hdpi': 72, 'xhdpi': 96, 'xxhdpi': 144, 'xxxhdpi': 192}.items():
    folder = res / f'mipmap-{name}'
    folder.mkdir(parents=True, exist_ok=True)
    icon(px).save(folder / 'ic_launcher.png')
    icon(px).save(folder / 'ic_launcher_round.png')
    icon(px * 9 // 4, scale=.56, background=False).save(folder / 'ic_launcher_foreground.png')
print('Icons written')
