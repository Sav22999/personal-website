"""Forkville icons: python3 tools/make_icons.py (needs Pillow and Inkscape).

The logo is icons/favicon.svg: a little restaurant (cream walls, a red-and-cream striped awning, two warm lit windows
and a wooden door) under a terracotta roof with a golden fork as its spire, glowing on a deep night-violet gradient,
in the same bold, outline-free style as the other projects' icons. The PNGs are that same SVG rendered by Inkscape
(the maskable one without the rounded corners and with the drawing scaled into the safe zone).
"""
import re
import shutil
import subprocess
import tempfile
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parent.parent
ICONS = ROOT / 'icons'
SVG = (ICONS / 'favicon.svg').read_text()
INK = (59, 36, 24)
ROOFS = [(180, 62, 92), (122, 139, 58), (226, 178, 58), (62, 124, 180)]
INKSCAPE = shutil.which('inkscape') or '/Applications/Inkscape.app/Contents/MacOS/inkscape'


def render(svg, size):
    """An SVG string rendered to a size x size RGBA image."""
    with tempfile.TemporaryDirectory() as tmp:
        src, out = Path(tmp) / 'logo.svg', Path(tmp) / 'logo.png'
        src.write_text(svg)
        subprocess.run([INKSCAPE, str(src), '--export-type=png', f'--export-filename={out}', f'--export-width={size}', f'--export-height={size}'],
                       check=True, capture_output=True)
        return Image.open(out).convert('RGBA')


def logo(size, maskable=False, radius=True):
    svg = SVG
    if maskable or not radius:
        svg = svg.replace('<rect width="64" height="64" rx="15"', '<rect width="64" height="64" rx="0"')
    if maskable:  # everything inside the safe zone (the centre 80%), the background full bleed
        body = re.search(r'(</defs><rect[^>]*/>)(.*)(</svg>)', svg, re.S)
        svg = svg[:body.start(2)] + '<g transform="translate(32 32) scale(.78) translate(-32 -32)">' + body.group(2) + '</g>' + svg[body.end(2):]
    return render(svg, size)


def house(d, x, y, w, h, wall, roof, k):
    """A small village house for the social image (x, y = bottom-left, in px)."""
    d.rectangle([x, y - h, x + w, y], fill=wall)
    d.polygon([(x - w * 0.12, y - h), (x + w / 2, y - h - w * 0.55), (x + w * 1.12, y - h)], fill=roof)
    d.rectangle([x + w * 0.38, y - h * 0.5, x + w * 0.62, y], fill=INK)
    d.rectangle([x + w * 0.12, y - h * 0.82, x + w * 0.3, y - h * 0.6], fill=(255, 226, 150))
    d.rectangle([x + w * 0.7, y - h * 0.82, x + w * 0.88, y - h * 0.6], fill=(255, 226, 150))


def og_image():
    W, H, k = 1200, 630, 2
    img = Image.new('RGBA', (W * k, H * k))
    g = ImageDraw.Draw(img)
    for y in range(H * k):
        t = y / (H * k - 1)
        g.line([(0, y), (W * k, y)], fill=tuple(round(a + (b - a) * t) for a, b in zip((255, 236, 214), (255, 246, 233))) + (255,))
    d = ImageDraw.Draw(img)
    # Awning stripes along the top
    for i in range(0, W * k, 120 * k):
        d.rectangle([i, 0, i + 60 * k, 64 * k], fill=(224, 72, 60))
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
