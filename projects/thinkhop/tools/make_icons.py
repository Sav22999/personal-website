"""Draws every icon and image of ThinkHop from the same geometry: python tools/make_icons.py"""
import os
from PIL import Image, ImageDraw, ImageFont

P = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
# The look of the game: a notebook page, thick ink outlines, hard shadows, violet and yellow
VIOLET, YELLOW, INK, PAPER, WHITE = (91, 63, 214), (255, 210, 63), (27, 22, 64), (255, 251, 242), (255, 255, 255)
GRID_ON_VIOLET, GRID_ON_PAPER = (112, 86, 226), (232, 226, 242)
AA = 4
FONT = f'{P}/fonts/inter-latin-wght-normal.woff2'

def bezier(p0, p1, p2, t):
    return tuple((1 - t) ** 2 * p0[i] + 2 * (1 - t) * t * p1[i] + t * t * p2[i] for i in range(2))

# The mark, on a 512 grid: a dotted hop that lands on a question mark
ARC = ((92, 392), (112, 120), (214, 132))
DOTS = [0.0, 0.33, 0.66]
RING, RADIUS, STROKE = (316, 196), 78, 58
STEM, DOT = (287, 245, 345, 334), (316, 402, 34)
OUTLINE, SHADOW, DOT_R = 14, 14, 19

def question(d, X, Y, s, grow, fill, dx=0, dy=0):
    """The question mark, `grow` units fatter all around: it is drawn three times for shadow, outline and fill."""
    cx, cy = RING[0] + dx, RING[1] + dy
    o = RADIUS + STROKE / 2 + grow
    d.arc((X(cx - o), Y(cy - o), X(cx + o), Y(cy + o)), 180, 90, fill=fill, width=round((STROKE + 2 * grow) * s))
    r = STROKE / 2 + grow
    d.ellipse((X(cx - RADIUS - r), Y(cy - r), X(cx - RADIUS + r), Y(cy + r)), fill=fill)
    x0, y0, x1, y1 = STEM
    d.rounded_rectangle((X(x0 + dx - grow), Y(y0 + dy - grow), X(x1 + dx + grow), Y(y1 + dy + grow)), (14 + grow) * s, fill=fill)
    x, y, r = DOT
    d.ellipse((X(x + dx - r - grow), Y(y + dy - r - grow), X(x + dx + r + grow), Y(y + dy + r + grow)), fill=fill)

def draw_mark(d, s, ox, oy):
    X = lambda v: ox + v * s
    Y = lambda v: oy + v * s
    for t in DOTS:
        x, y = bezier(*ARC, t)
        for r, fill in ((DOT_R + 9, INK), (DOT_R, WHITE)):
            d.ellipse((X(x - r), Y(y - r), X(x + r), Y(y + r)), fill=fill)
    question(d, X, Y, s, OUTLINE, INK, SHADOW, SHADOW)
    question(d, X, Y, s, OUTLINE, INK)
    question(d, X, Y, s, 0, YELLOW)

def page(size, color, lines, step):
    img = Image.new('RGBA', size, color + (255,))
    d = ImageDraw.Draw(img)
    for v in range(step, max(size), step):
        d.line((v, 0, v, size[1]), fill=lines, width=max(1, step // 24))
        d.line((0, v, size[0], v), fill=lines, width=max(1, step // 24))
    return img

def icon(size, radius=116, inset=1.0, bleed=False):
    big = size * AA
    # A grid on a tiny icon is only noise
    img = page((big, big), VIOLET, GRID_ON_VIOLET, big // 8) if size >= 96 else Image.new('RGBA', (big, big), VIOLET + (255,))
    s = big / 512 * inset
    off = (big - 512 * s) / 2
    draw_mark(ImageDraw.Draw(img), s, off, off)
    if not bleed:
        r = radius * big / 512
        ImageDraw.Draw(img).rounded_rectangle((0, 0, big - 1, big - 1), r, outline=INK, width=round(18 * big / 512))
        m = Image.new('L', (big, big), 0)
        ImageDraw.Draw(m).rounded_rectangle((0, 0, big - 1, big - 1), r, fill=255)
        img.putalpha(m)
    return img.resize((size, size), Image.LANCZOS)

def layer(size, inset):
    big = size * AA
    img = Image.new('RGBA', (big, big), (0, 0, 0, 0))
    s = big / 512 * inset
    off = (big - 512 * s) / 2
    draw_mark(ImageDraw.Draw(img), s, off, off)
    return img.resize((size, size), Image.LANCZOS)

def font(size, weight):
    f = ImageFont.truetype(FONT, size)
    f.set_variation_by_axes([weight])
    return f

def banner(size, title, tagline):
    w, hgt = size
    img = page(size, PAPER, GRID_ON_PAPER, hgt // 12)
    mark = round(hgt * .62)
    x0, y0 = round(w * .07), round((hgt - mark) / 2)
    d = ImageDraw.Draw(img)
    d.rounded_rectangle((x0 + 12, y0 + 12, x0 + mark + 12, y0 + mark + 12), mark * .22, fill=INK)
    img.alpha_composite(icon(mark), (x0, y0))
    x = x0 + mark + w * .05
    d.text((x, hgt * .29), title, font=font(round(hgt * .2), 800), fill=INK)
    d.text((x, hgt * .56), tagline, font=font(round(hgt * .062), 500), fill=VIOLET)
    return img.convert('RGB')

def favicon_svg():
    hexa = lambda c: '#%02X%02X%02X' % c
    ink, yellow, violet = hexa(INK), hexa(YELLOW), hexa(VIOLET)
    dots = ''.join('<circle cx="%.1f" cy="%.1f" r="%d" fill="#fff" stroke="%s" stroke-width="18"/>' % (*bezier(*ARC, t), DOT_R + 4, ink) for t in DOTS)
    cx, cy = RING
    x0, y0, x1, y1 = STEM
    def q(grow, color):
        path = f'<path d="M{cx - RADIUS} {cy}a{RADIUS} {RADIUS} 0 1 1 {RADIUS} {RADIUS}" fill="none" stroke="{color}" stroke-width="{STROKE + 2 * grow}" stroke-linecap="round"/>'
        stem = f'<rect x="{x0 - grow}" y="{y0 - grow}" width="{x1 - x0 + 2 * grow}" height="{y1 - y0 + 2 * grow}" rx="{14 + grow}" fill="{color}"/>'
        dot = f'<circle cx="{DOT[0]}" cy="{DOT[1]}" r="{DOT[2] + grow}" fill="{color}"/>'
        return path + stem + dot
    return f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><rect x="9" y="9" width="494" height="494" rx="108" fill="{violet}" stroke="{ink}" stroke-width="18"/>{dots}<g transform="translate({SHADOW} {SHADOW})">{q(OUTLINE, ink)}</g>{q(OUTLINE, ink)}{q(0, yellow)}</svg>
'''

os.makedirs(f'{P}/icons', exist_ok=True)
open(f'{P}/icons/favicon.svg', 'w').write(favicon_svg())
icon(32).save(f'{P}/icons/favicon-32.png')
icon(180, radius=0, bleed=True).convert('RGB').save(f'{P}/icons/apple-touch-icon.png')
icon(192).save(f'{P}/icons/icon-192.png')
icon(512).save(f'{P}/icons/icon-512.png')
icon(512, radius=0, inset=.72, bleed=True).convert('RGB').save(f'{P}/icons/icon-maskable-512.png')
banner((1200, 630), 'ThinkHop', 'Logic puzzles, one hop at a time').save(f'{P}/og-image.png', optimize=True)

A = f'{P}/android'
os.makedirs(f'{A}/store/en-GB', exist_ok=True)
icon(512, radius=0, bleed=True).convert('RGB').save(f'{A}/play-store-icon-512.png')
banner((1024, 500), 'ThinkHop', 'Logic puzzles, one hop at a time').save(f'{A}/store/en-GB/feature-graphic-1024x500.png', optimize=True)
for name, launcher, layers in (('mdpi', 48, 108), ('hdpi', 72, 162), ('xhdpi', 96, 216), ('xxhdpi', 144, 324), ('xxxhdpi', 192, 432)):
    r = f'{A}/app/src/main/res/mipmap-{name}'
    os.makedirs(r, exist_ok=True)
    icon(launcher).save(f'{r}/ic_launcher.png')
    icon(launcher, radius=256).save(f'{r}/ic_launcher_round.png')
    layer(layers, .62).save(f'{r}/ic_launcher_foreground.png')
