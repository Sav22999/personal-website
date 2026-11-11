"""Forkville icons: python3 tools/make_icons.py (needs Pillow).

The logo is a fork whose four tines are little houses with coloured roofs: a village of forks, on a warm
sunset gradient with a plate-shaped halo. Same geometry as icons/favicon.svg (a 64-unit grid), drawn with 4x
supersampling.
"""
from pathlib import Path
from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = Path(__file__).resolve().parent.parent
ICONS = ROOT / 'icons'
TOP, BOTTOM = (246, 160, 92), (198, 75, 41)
CREAM, INK, SHADOW = (255, 246, 233), (59, 36, 24), (126, 46, 22)
ROOFS = [(180, 62, 92), (122, 139, 58), (226, 178, 58), (62, 124, 180)]
TINES = (19.5, 27.83, 36.17, 44.5)


def gradient(w, h, top=TOP, bottom=BOTTOM):
    img = Image.new('RGBA', (w, h))
    d = ImageDraw.Draw(img)
    for y in range(h):
        t = y / max(1, h - 1)
        d.line([(0, y), (w, y)], fill=tuple(round(a + (b - a) * t) for a, b in zip(top, bottom)) + (255,))
    return img


def fork_shapes(d, P, s, color):
    d.rounded_rectangle([P(28.5, 35), P(35.5, 56)], radius=3.5 * s, fill=color)
    d.rounded_rectangle([P(15.5, 29.5), P(48.5, 37)], radius=3.75 * s, fill=color)
    for c in TINES:
        d.rectangle([P(c - 1.4, 21), P(c + 1.4, 31)], fill=color)
        d.rounded_rectangle([P(c - 3.5, 15), P(c + 3.5, 23)], radius=1.2 * s, fill=color)


def draw_logo(img, ox, oy, s, shadow=True, halo=True, sparkle=True):
    """The logo content (no background) on img, 64-grid scaled by s and moved to (ox, oy)."""
    P = lambda x, y: (ox + x * s, oy + y * s)
    if halo:
        layer = Image.new('RGBA', img.size, (0, 0, 0, 0))
        d = ImageDraw.Draw(layer)
        d.ellipse([P(9, 12), P(55, 58)], fill=CREAM + (36,), outline=CREAM + (90,), width=max(1, round(1.6 * s)))
        img.alpha_composite(layer)
    if shadow:
        layer = Image.new('RGBA', img.size, (0, 0, 0, 0))
        fork_shapes(ImageDraw.Draw(layer), lambda x, y: P(x + 1.2, y + 1.8), s, SHADOW + (90,))
        img.alpha_composite(layer.filter(ImageFilter.GaussianBlur(0.6 * s)))
    d = ImageDraw.Draw(img)
    fork_shapes(d, P, s, CREAM)
    for c, col in zip(TINES, ROOFS):
        d.polygon([P(c - 4.9, 16.6), P(c, 9.1), P(c + 4.9, 16.6)], fill=col)
        d.rounded_rectangle([P(c - 1.25, 18), P(c + 1.25, 21.2)], radius=0.6 * s, fill=INK)
    if sparkle:
        d.polygon([P(52, 8.5), P(53, 11.1), P(55.6, 12.1), P(53, 13.1), P(52, 15.7), P(51, 13.1), P(48.4, 12.1), P(51, 11.1)], fill=CREAM)


def logo(size, maskable=False, radius=True):
    k = 4
    S = size * k
    bg = gradient(S, S)
    if maskable:
        img = bg
        s = S / 64 * 0.74  # keep everything inside the safe zone
        off = (S - 64 * s) / 2
        draw_logo(img, off, off, s, sparkle=False)
    else:
        mask = Image.new('L', (S, S), 0)
        ImageDraw.Draw(mask).rounded_rectangle([0, 0, S - 1, S - 1], radius=S * 15 / 64 if radius else 0, fill=255)
        img = Image.new('RGBA', (S, S), (0, 0, 0, 0))
        img.paste(bg, (0, 0), mask)
        draw_logo(img, 0, 0, S / 64, sparkle=size >= 64, halo=size >= 48)
    return img.resize((size, size), Image.LANCZOS)


def house(d, x, y, w, h, wall, roof, k):
    """A small village house for the social image (x, y = bottom-left, in px)."""
    d.rectangle([x, y - h, x + w, y], fill=wall)
    d.polygon([(x - w * 0.12, y - h), (x + w / 2, y - h - w * 0.55), (x + w * 1.12, y - h)], fill=roof)
    d.rectangle([x + w * 0.38, y - h * 0.5, x + w * 0.62, y], fill=INK)
    d.rectangle([x + w * 0.12, y - h * 0.82, x + w * 0.3, y - h * 0.6], fill=(255, 226, 150))
    d.rectangle([x + w * 0.7, y - h * 0.82, x + w * 0.88, y - h * 0.6], fill=(255, 226, 150))


def og_image():
    W, H, k = 1200, 630, 2
    img = gradient(W * k, H * k, (255, 236, 210), (255, 246, 233))
    d = ImageDraw.Draw(img)
    # Awning stripes along the top
    for i in range(0, W * k, 120 * k):
        d.rectangle([i, 0, i + 60 * k, 64 * k], fill=(217, 101, 59))
    d.rectangle([0, 64 * k, W * k, 70 * k], fill=INK)
    # A little village along the bottom
    d.rectangle([0, 560 * k, W * k, H * k], fill=(156, 191, 106))
    for i, x in enumerate(range(640, 1200, 110)):
        house(d, x * k, 562 * k, 80 * k, (90 + (i % 3) * 22) * k, (255, 243, 224), ROOFS[i % 4], k)
    # The icon
    big = logo(300 * k)
    img.alpha_composite(big, (90 * k, 140 * k))
    font_paths = ['/System/Library/Fonts/SFNSRounded.ttf', '/System/Library/Fonts/Supplemental/Arial Rounded Bold.ttf', '/System/Library/Fonts/Supplemental/Arial Bold.ttf']
    font = small = None
    for p in font_paths:
        if Path(p).exists():
            font = ImageFont.truetype(p, 116 * k)
            small = ImageFont.truetype(p, 40 * k)
            break
    font = font or ImageFont.load_default()
    small = small or ImageFont.load_default()
    d.text((430 * k, 180 * k), 'Forkville', fill=INK, font=font)
    d.text((436 * k, 330 * k), 'Your own little restaurant, in 3D', fill=(138, 110, 92), font=small)
    return img.convert('RGB').resize((W, H), Image.LANCZOS)


ICONS.mkdir(exist_ok=True)
logo(192).save(ICONS / 'icon-192.png')
logo(512).save(ICONS / 'icon-512.png')
logo(512, maskable=True).save(ICONS / 'icon-maskable-512.png')
logo(180, radius=False).save(ICONS / 'apple-touch-icon.png')
logo(32).save(ICONS / 'favicon-32.png')
og_image().save(ICONS / 'og-image.png')
site = ROOT.parent.parent / 'images' / 'projects'
if site.is_dir():
    logo(256).save(site / 'forkville.png')
print('icons written')
