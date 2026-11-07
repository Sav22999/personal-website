"""Regenerates every icon of Fucina (web, PWA, Android, Play Store).
Favilla, a happy flame, glowing on a molten background that goes from coral to sunshine.
Needs Pillow: python3 tools/build-icons.py"""
from pathlib import Path
from PIL import Image, ImageDraw, ImageFilter

ROOT = Path(__file__).resolve().parent.parent
TOP, BOTTOM = (255, 122, 56), (226, 45, 22)
CREAM, SUN, INK, BLUSH = (255, 247, 228, 255), (255, 197, 61, 255), (27, 27, 75, 255), (255, 138, 92, 255)
SS = 4  # drawn larger and scaled down for smooth edges


def curve(points, steps=24):
    """A closed shape through Bezier segments: a start, then (control, control, end) triples."""
    out = [points[0]]
    p0 = points[0]
    for c1, c2, p3 in points[1:]:
        for i in range(1, steps + 1):
            t = i / steps
            u = 1 - t
            out.append(tuple(u ** 3 * a + 3 * u * u * t * b + 3 * u * t * t * c + t ** 3 * d for a, b, c, d in zip(p0, c1, c2, p3)))
        p0 = p3
    return out


OUTER = [(52, 3), ((58, 27), (88, 39), (88, 77)), ((88, 102), (71, 117), (50, 117)), ((29, 117), (12, 102), (12, 77)), ((12, 60), (22, 49), (31, 41)), ((33, 53), (39, 58), (44, 54)), ((51, 43), (43, 23), (52, 3))]
INNER = [(51, 36), ((55, 51), (75, 60), (75, 82)), ((75, 98), (64, 108), (50, 108)), ((36, 108), (25, 98), (25, 82)), ((25, 71), (32, 64), (38, 60)), ((40, 67), (43, 69), (46, 67)), ((51, 60), (46, 47), (51, 36))]


def mark(size, scale, shadow=True):
    """The flame with its face, on a transparent square."""
    img = Image.new('RGBA', (size, size), (0, 0, 0, 0))
    k = size / 124 * scale
    ox, oy = (size - 100 * k) / 2, (size - 120 * k) / 2
    p = lambda pts: [(x * k + ox, y * k + oy) for x, y in pts]
    if shadow:
        sh = Image.new('RGBA', (size, size), (0, 0, 0, 0))
        ImageDraw.Draw(sh).polygon([(x, y + 4 * k) for x, y in p(curve(OUTER))], fill=(122, 20, 6, 150))
        img.alpha_composite(sh.filter(ImageFilter.GaussianBlur(3 * k)))
    d = ImageDraw.Draw(img)
    d.polygon(p(curve(OUTER)), fill=CREAM)
    d.polygon(p(curve(INNER)), fill=SUN)
    for x in (28, 72):
        cx, cy, r = x * k + ox, 80 * k + oy, 5.5 * k
        d.ellipse((cx - r, cy - r, cx + r, cy + r), fill=BLUSH)
    for x in (37, 63):
        cx, cy, r = x * k + ox, 69 * k + oy, 7 * k
        d.ellipse((cx - r, cy - r, cx + r, cy + r), fill=INK)
        cx, cy, r = (x + 2.4) * k + ox, 66.4 * k + oy, 2.4 * k
        d.ellipse((cx - r, cy - r, cx + r, cy + r), fill=(255, 255, 255, 255))
    d.arc((37 * k + ox, 66 * k + oy, 63 * k + ox, 92 * k + oy), 30, 150, fill=INK, width=max(1, round(5.5 * k)))
    return img


def molten(s):
    """The background: a diagonal glow from the top."""
    small = Image.new('RGB', (64, 64))
    for y in range(64):
        for x in range(64):
            t = min(1, (x * .35 + y) / 84)
            small.putpixel((x, y), tuple(round(a + (b - a) * t) for a, b in zip(TOP, BOTTOM)))
    return small.resize((s, s), Image.BICUBIC).convert('RGBA')


def icon(size, rounded=True, scale=.8, background=True):
    s = size * SS
    img = Image.new('RGBA', (s, s), (0, 0, 0, 0))
    if background:
        bg = molten(s)
        if rounded:
            shape = Image.new('L', (s, s), 0)
            ImageDraw.Draw(shape).rounded_rectangle((0, 0, s - 1, s - 1), radius=s * .23, fill=255)
            img.paste(bg, (0, 0), shape)
        else:
            img = bg
    img.alpha_composite(mark(s, scale, shadow=background))
    return img.resize((size, size), Image.LANCZOS)


out = ROOT / 'assets/icons'
out.mkdir(parents=True, exist_ok=True)
icon(32).save(out / 'favicon-32.png')
icon(192).save(out / 'icon-192.png')
icon(512).save(out / 'icon-512.png')
icon(512, rounded=False, scale=.66).save(out / 'icon-maskable-512.png')
icon(180, rounded=False).save(out / 'apple-touch-icon.png')
(out / 'favicon.svg').write_text('''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="m" x1="0" y1="0" x2=".3" y2="1"><stop offset="0" stop-color="#FF7A38"/><stop offset="1" stop-color="#E22D16"/></linearGradient></defs><rect width="100" height="100" rx="23" fill="url(#m)"/><g transform="translate(17.5 11) scale(.65)"><path fill="#FFF7E4" d="M52 3c6 24 36 36 36 74 0 25-17 40-38 40S12 102 12 77c0-17 10-28 19-36 2 12 8 17 13 13 7-11-1-31 8-51Z"/><path fill="#FFC53D" d="M51 36c4 15 24 24 24 46 0 16-11 26-25 26S25 98 25 82c0-11 7-18 13-22 2 7 5 9 8 7 5-7 0-20 5-31Z"/><circle cx="37" cy="69" r="7" fill="#1B1B4B"/><circle cx="63" cy="69" r="7" fill="#1B1B4B"/><path d="M39 84c5 6 17 6 22 0" fill="none" stroke="#1B1B4B" stroke-width="5.5" stroke-linecap="round"/></g></svg>
''')
icon(512, rounded=False).save(ROOT / 'android/play-store-icon-512.png')
res = ROOT / 'android/app/src/main/res'
for name, px in {'mdpi': 48, 'hdpi': 72, 'xhdpi': 96, 'xxhdpi': 144, 'xxxhdpi': 192}.items():
    folder = res / f'mipmap-{name}'
    folder.mkdir(parents=True, exist_ok=True)
    icon(px).save(folder / 'ic_launcher.png')
    icon(px).save(folder / 'ic_launcher_round.png')
    icon(px * 9 // 4, scale=.5, background=False).save(folder / 'ic_launcher_foreground.png')
print('Icons written')
