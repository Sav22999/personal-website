"""Forkville icons: python3 tools/make_icons.py (needs Pillow).

The logo is a serving cloche that is also a little restaurant: a cream dome with a door and two lit windows, a
golden fork for the handle, on a plate, against a sunset gradient with soft rays. Same geometry as
icons/favicon.svg (a 64-unit grid), drawn with 4x supersampling.
"""
import math
from pathlib import Path
from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = Path(__file__).resolve().parent.parent
ICONS = ROOT / 'icons'
TOP, BOTTOM = (255, 183, 77), (214, 64, 92)
CREAM, INK, SHADOW = (255, 246, 233), (59, 36, 24), (110, 30, 40)
PLATE_IN, BAND, DOOR, GOLD, LIGHT = (241, 228, 207), (235, 220, 196), (217, 101, 59), (226, 178, 58), (255, 210, 122)
ROOFS = [(180, 62, 92), (122, 139, 58), (226, 178, 58), (62, 124, 180)]
FORK = [(30.8, 20.5, 33.2, 30, 1), (27, 18, 37, 21.6, 1.6)] + [(x, 10.5, x + 1.9, 19.5, 0.95) for x in (27.4, 31.05, 34.7)]


def gradient(w, h, top=TOP, bottom=BOTTOM):
    img = Image.new('RGBA', (w, h))
    d = ImageDraw.Draw(img)
    for y in range(h):
        t = y / max(1, h - 1)
        d.line([(0, y), (w, y)], fill=tuple(round(a + (b - a) * t) for a, b in zip(top, bottom)) + (255,))
    return img


def draw_logo(img, ox, oy, s, shadow=True, rays=True, sparkle=True):
    """The logo content (no background) on img, 64-grid scaled by s and moved to (ox, oy)."""
    P = lambda x, y: (ox + x * s, oy + y * s)
    R = lambda x0, y0, x1, y1: [P(x0, y0), P(x1, y1)]
    w = lambda u: max(1, round(u * s))
    if rays:
        layer = Image.new('RGBA', img.size, (0, 0, 0, 0))
        d = ImageDraw.Draw(layer)
        for i in range(0, 12, 2):
            a0, a1 = math.radians(i * 30 - 6), math.radians(i * 30 + 6)
            d.polygon([P(32, 34), P(32 + math.cos(a0) * 60, 34 + math.sin(a0) * 60), P(32 + math.cos(a1) * 60, 34 + math.sin(a1) * 60)], fill=CREAM + (41,))
        alpha = Image.composite(layer.getchannel('A'), Image.new('L', img.size, 0), img.getchannel('A'))
        layer.putalpha(alpha)
        img.alpha_composite(layer)
    if shadow:
        layer = Image.new('RGBA', img.size, (0, 0, 0, 0))
        d = ImageDraw.Draw(layer)
        d.ellipse(R(10, 45.5, 56, 55.5), fill=SHADOW + (70,))
        d.pieslice(R(15, 31, 51, 67), 180, 360, fill=SHADOW + (60,))
        img.alpha_composite(layer.filter(ImageFilter.GaussianBlur(0.9 * s)))
    d = ImageDraw.Draw(img)
    # The plate
    d.ellipse(R(9, 43.5, 55, 53.5), fill=CREAM, outline=INK, width=w(1.6))
    d.ellipse(R(15, 45.4, 49, 51), fill=PLATE_IN)
    # The dome, its shine and its rim
    d.pieslice(R(14 - 0.9, 29 - 0.9, 50 + 0.9, 65 + 0.9), 180, 360, fill=INK)
    d.pieslice(R(14 + 0.9, 29 + 0.9, 50 - 0.9, 65 - 0.9), 180, 360, fill=CREAM)
    d.arc(R(18, 33, 46, 61), 205, 235, fill=(255, 255, 255), width=w(1.8))
    d.rounded_rectangle(R(13, 45, 51, 48.4), radius=1.7 * s, fill=BAND, outline=INK, width=w(1.4))
    # The door (with a round top) and the two lit windows
    d.rounded_rectangle(R(28.5 - 0.65, 32.5 - 0.65, 35.5 + 0.65, 45 + 0.65), radius=4.1 * s, fill=INK)
    d.rounded_rectangle(R(28.5 + 0.65, 32.5 + 0.65, 35.5 - 0.65, 45), radius=2.9 * s, fill=DOOR)
    d.rectangle(R(28.5 - 0.65, 39, 35.5 + 0.65, 45.2), fill=INK)  # a flat bottom, like a real door
    d.rectangle(R(28.5 + 0.65, 39, 35.5 - 0.65, 45), fill=DOOR)
    d.ellipse(R(33.15, 40.45, 34.65, 41.95), fill=GOLD)
    for x in (21.8, 42.2):
        d.ellipse(R(x - 3.1, 36.4, x + 3.1, 42.6), fill=LIGHT, outline=INK, width=w(1.2))
        d.line([P(x - 3.1, 39.5), P(x + 3.1, 39.5)], fill=INK, width=w(0.8))
        d.line([P(x, 36.4), P(x, 42.6)], fill=INK, width=w(0.8))
    # The golden fork on top: stem, neck and three tines
    for x0, y0, x1, y1, r in FORK:
        d.rounded_rectangle(R(x0 - 0.6, y0 - 0.6, x1 + 0.6, y1 + 0.6), radius=(r + 0.6) * s, fill=INK)
    for x0, y0, x1, y1, r in FORK:
        d.rounded_rectangle(R(x0, y0, x1, y1), radius=r * s, fill=GOLD)
    if sparkle:
        d.polygon([P(52, 8.5), P(53, 11.1), P(55.6, 12.1), P(53, 13.1), P(52, 15.7), P(51, 13.1), P(48.4, 12.1), P(51, 11.1)], fill=CREAM)
        d.ellipse(R(9.3, 12.8, 11.7, 15.2), fill=CREAM + (205,))


def logo(size, maskable=False, radius=True):
    k = 4
    S = size * k
    bg = gradient(S, S)
    if maskable:
        img = bg
        s = S / 64 * 0.74  # keep everything inside the safe zone
        off = (S - 64 * s) / 2
        draw_logo(img, off, off, s, sparkle=False, rays=False)
    else:
        mask = Image.new('L', (S, S), 0)
        ImageDraw.Draw(mask).rounded_rectangle([0, 0, S - 1, S - 1], radius=S * 15 / 64 if radius else 0, fill=255)
        img = Image.new('RGBA', (S, S), (0, 0, 0, 0))
        img.paste(bg, (0, 0), mask)
        draw_logo(img, 0, 0, S / 64, sparkle=size >= 64, rays=size >= 48)
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
