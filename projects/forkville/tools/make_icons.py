"""Forkville icons: python3 tools/make_icons.py (needs Pillow and Inkscape).

The logo is icons/favicon.svg, drawn by tools/icon_svg.py: an isometric low-poly restaurant like the ones in the game
(striped awnings, lit windows, a round golden sign with a fork on the roof, a terrace table under a parasol) on a
lawn under a sunny sky. The PNGs are that same SVG rendered by Inkscape (the maskable one without the rounded
corners and with the drawing scaled into the safe zone).
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


def og_image(W=1200, H=630):
    k = 2
    sy = H / 630  # the feature graphic (1024×500) is the same picture, a little shorter
    img = Image.new('RGBA', (W * k, H * k))
    g = ImageDraw.Draw(img)
    for y in range(H * k):
        t = y / (H * k - 1)
        g.line([(0, y), (W * k, y)], fill=tuple(round(a + (b - a) * t) for a, b in zip((255, 236, 214), (255, 246, 233))) + (255,))
    d = ImageDraw.Draw(img)
    # Awning stripes along the top
    for i in range(0, W * k, 120 * k):
        d.rectangle([i, 0, i + 60 * k, int(64 * sy * k)], fill=(224, 72, 60))
    d.rectangle([0, int(64 * sy * k), W * k, int(70 * sy * k)], fill=INK)
    # A little village along the bottom
    ground = int((H - 70) * k)
    d.rectangle([0, ground, W * k, H * k], fill=(156, 191, 106))
    hk = sy * sy  # shorter houses on the shorter picture, so they stay clear of the tagline
    for i, x in enumerate(range(int(W * (0.53 if sy >= 1 else 0.6)), W, 110)):
        house(d, x * k, ground + 2 * k, 80 * k, int((90 + (i % 3) * 22) * hk) * k, (255, 243, 224), ROOFS[i % 4], k)
    # The icon
    size = int(300 * sy)
    big = logo(size * k)
    img.alpha_composite(big, (int(90 * W / 1200) * k, int((H - size) / 2 + 10 * sy) * k))
    font_paths = ['/System/Library/Fonts/SFNSRounded.ttf', '/System/Library/Fonts/Supplemental/Arial Rounded Bold.ttf', '/System/Library/Fonts/Supplemental/Arial Bold.ttf']
    font = small = None
    for p in font_paths:
        if Path(p).exists():
            font = ImageFont.truetype(p, 116 * k)
            small = ImageFont.truetype(p, 40 * k)
            break
    font = font or ImageFont.load_default()
    small = small or ImageFont.load_default()
    x0 = int(90 * W / 1200) + size + 40
    d.text((x0 * k, int(180 * sy) * k), 'Forkville', fill=INK, font=font)
    d.text(((x0 + 6) * k, int(330 * sy) * k), 'Your own little restaurant, in 3D', fill=(138, 110, 92), font=small)
    return img.convert('RGB').resize((W, H), Image.LANCZOS)


ICONS.mkdir(exist_ok=True)
logo(192).save(ICONS / 'icon-192.png')
logo(512).save(ICONS / 'icon-512.png')
logo(512, maskable=True).save(ICONS / 'icon-maskable-512.png')
logo(180, radius=False).save(ICONS / 'apple-touch-icon.png')
logo(32).save(ICONS / 'favicon-32.png')
og_image().save(ICONS / 'og-image.png')

# ---------------------------------------------------------------- the Android app (android/)

def no_sky(svg):
    """The logo without its sky (background, clouds and sun): the restaurant alone, for the adaptive icon's layer."""
    svg = re.sub(r'<rect width="64" height="64" rx="15" fill="url\(#fvbg\)"/>', '', svg)
    svg = re.sub(r'<g fill="#FFFFFF" opacity="\.85">.*?</g>', '', svg)
    return re.sub(r'<circle cx="51" cy="7\.5"[^>]*/>', '', svg)


def layer(size, scale=0.8):
    """The adaptive icon's foreground (108 dp): the restaurant in the middle, inside the 66 dp safe zone."""
    body = re.search(r'(</defs>)(.*)(</svg>)', no_sky(SVG), re.S)
    svg = no_sky(SVG)[:body.start(2)] + f'<g transform="translate(32 33) scale({scale}) translate(-32 -33)">' + body.group(2) + '</g>' + no_sky(SVG)[body.end(2):]
    return render(svg, size)


def round_icon(size):
    """The round launcher icon (older launchers): the whole logo in a circle."""
    return render(SVG.replace('<rect width="64" height="64" rx="15"', '<rect width="64" height="64" rx="32"'), size)


A = ROOT / 'android'
if A.is_dir():
    (A / 'store' / 'en-GB').mkdir(parents=True, exist_ok=True)
    logo(512, radius=False).convert('RGB').save(A / 'play-store-icon-512.png')  # 512×512, no transparency
    og_image(1024, 500).save(A / 'store' / 'en-GB' / 'feature-graphic-1024x500.png', optimize=True)
    for name, launcher, layers in (('mdpi', 48, 108), ('hdpi', 72, 162), ('xhdpi', 96, 216), ('xxhdpi', 144, 324), ('xxxhdpi', 192, 432)):
        r = A / 'app' / 'src' / 'main' / 'res' / f'mipmap-{name}'
        r.mkdir(parents=True, exist_ok=True)
        logo(launcher).save(r / 'ic_launcher.png')
        round_icon(launcher).save(r / 'ic_launcher_round.png')
        layer(layers).save(r / 'ic_launcher_foreground.png')

site = ROOT.parent.parent / 'images' / 'projects'
if site.is_dir():
    logo(256).save(site / 'forkville.png')
print('icons written')
