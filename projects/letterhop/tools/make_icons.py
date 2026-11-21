"""Draws every icon and image of LetterHop from the same geometry: python make_icons.py <project dir> <work dir>"""
import io, os, sys
from fontTools.ttLib import TTFont
from PIL import Image, ImageDraw, ImageFont

P, W = sys.argv[1], sys.argv[2]
TOP, BOTTOM = (251, 146, 60), (194, 65, 12)
ORANGE, DARK, CREAM = (232, 89, 12), (42, 26, 16), (255, 248, 241)
AA = 4

def gradient(size, a=TOP, b=BOTTOM):
    w, h = size
    img = Image.new('RGB', (64, 64))
    for y in range(64):
        for x in range(64):
            k = (x + y) / 126
            img.putpixel((x, y), tuple(round(a[i] + (b[i] - a[i]) * k) for i in range(3)))
    return img.resize(size, Image.BICUBIC)

def bezier(p0, p1, p2, t):
    return tuple((1 - t) ** 2 * p0[i] + 2 * (1 - t) * t * p1[i] + t * t * p2[i] for i in range(2))

# The mark, on a 512 grid: two tiles and a hop from L to H
TILE, RAD = 168, 34
LX, HX, TY = 72, 272, 226
ARC = ((156, 206), (256, 40), (344, 188))
DOTS = [0.04, 0.2, 0.36, 0.52, 0.68, 0.82]

def mark_shapes():
    tiles = [(LX, TY), (HX, TY)]
    l = [(LX + 52, TY + 34, 26, 100), (LX + 52, TY + 110, 70, 24)]
    h = [(HX + 42, TY + 34, 26, 100), (HX + 100, TY + 34, 26, 100), (HX + 42, TY + 72, 84, 24)]
    dots = [bezier(*ARC, t) for t in DOTS]
    return tiles, l + h, dots

ARROW = [(374, 216), (326, 190), (364, 158)]

def draw_mark(d, scale, ox, oy, tile_fill, letter_fill, dot_fill):
    tiles, bars, dots = mark_shapes()
    s = lambda v: v * scale
    for x, y in tiles:
        d.rounded_rectangle((ox + s(x), oy + s(y), ox + s(x + TILE), oy + s(y + TILE)), s(RAD), fill=tile_fill)
    for x, y, w, h in bars:
        d.rounded_rectangle((ox + s(x), oy + s(y), ox + s(x + w), oy + s(y + h)), s(5), fill=letter_fill)
    for x, y in dots:
        d.ellipse((ox + s(x - 12), oy + s(y - 12), ox + s(x + 12), oy + s(y + 12)), fill=dot_fill)
    d.polygon([(ox + s(x), oy + s(y)) for x, y in ARROW], fill=dot_fill)

def icon(size, radius=116, inset=1.0, bleed=False):
    big = size * AA
    img = gradient((big, big)).convert('RGBA')
    if not bleed:
        m = Image.new('L', (big, big), 0)
        ImageDraw.Draw(m).rounded_rectangle((0, 0, big - 1, big - 1), radius * big / 512, fill=255)
        img.putalpha(m)
    scale = big / 512 * inset
    off = (big - 512 * scale) / 2
    draw_mark(ImageDraw.Draw(img), scale, off, off, (255, 255, 255, 255), ORANGE + (255,), (255, 255, 255, 255))
    return img.resize((size, size), Image.LANCZOS)

def layer(size, inset, tile, letter, dot):
    big = size * AA
    img = Image.new('RGBA', (big, big), (0, 0, 0, 0))
    scale = big / 512 * inset
    off = (big - 512 * scale) / 2
    draw_mark(ImageDraw.Draw(img), scale, off, off, tile, letter, dot)
    return img.resize((size, size), Image.LANCZOS)

def monochrome(size, inset):
    big = size * AA
    img = Image.new('L', (big, big), 0)
    d = ImageDraw.Draw(img)
    scale = big / 512 * inset
    off = (big - 512 * scale) / 2
    draw_mark(d, scale, off, off, 255, 0, 255)
    out = Image.new('RGBA', (big, big), (255, 255, 255, 0))
    out.putalpha(img)
    return out.resize((size, size), Image.LANCZOS)

# ---- SVG, same geometry
def svg():
    tiles, bars, dots = mark_shapes()
    body = ''.join(f'<rect x="{x}" y="{y}" width="{TILE}" height="{TILE}" rx="{RAD}" fill="#fff"/>' for x, y in tiles)
    body += ''.join(f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="5" fill="#E8590C"/>' for x, y, w, h in bars)
    body += ''.join(f'<circle cx="{x:.1f}" cy="{y:.1f}" r="12" fill="#fff"/>' for x, y in dots)
    body += '<polygon points="' + ' '.join(f'{x},{y}' for x, y in ARROW) + '" fill="#fff"/>'
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512"><defs><linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">'
            '<stop offset="0" stop-color="#FB923C"/><stop offset="1" stop-color="#C2410C"/></linearGradient></defs>'
            f'<rect width="512" height="512" rx="116" fill="url(#bg)"/>{body}</svg>\n')

def font(name, size, weight):
    path = f'{W}/{name}.ttf'
    if not os.path.exists(path):
        f = TTFont(f'{P}/fonts/{name}-latin.woff2'); f.flavor = None; f.save(path)
    ft = ImageFont.truetype(path, size)
    try: ft.set_variation_by_axes([weight] + [100] * (len(ft.get_variation_axes()) - 1))
    except OSError: pass
    return ft

def word_tile(d, x, y, size, ch, fill, color, f, border=None):
    d.rounded_rectangle((x, y, x + size, y + size), size * .2, fill=fill, outline=border, width=max(2, size // 22) if border else 0)
    d.text((x + size / 2, y + size / 2), ch, font=f, fill=color, anchor='mm')

def ladder(d, x, y, size, gap, words):
    f = font('fredoka', int(size * .56), 600)
    for r, w in enumerate(words):
        for c, ch in enumerate(w):
            prev = words[r - 1] if r else w
            changed = prev[c] != ch
            last = r == len(words) - 1
            word_tile(d, x + c * (size + gap), y + r * (size + gap), size, ch,
                      ORANGE if changed else (255, 255, 255), (255, 255, 255) if changed else DARK, f)

def banner(size, title_size, sub, sub_size, tile):
    w, h = size
    img = gradient(size, (253, 186, 116), (194, 65, 12))
    d = ImageDraw.Draw(img)
    d.text((w * .06, h * .36), 'LetterHop', font=font('fredoka', title_size, 600), fill=(255, 255, 255), anchor='lm')
    y = h * .36 + title_size * .75
    for line in sub:
        d.text((w * .062, y), line, font=font('nunito', sub_size, 800), fill=(255, 255, 255), anchor='lm')
        y += sub_size * 1.3
    words = ['SHOOT', 'SHORT', 'SHIRT']
    gap = tile // 8
    ladder(d, w - (tile + gap) * 5 - w * .05, (h - (tile + gap) * 3) / 2, tile, gap, words)
    return img

os.makedirs(f'{P}/icons', exist_ok=True)
open(f'{P}/icons/favicon.svg', 'w').write(svg())
icon(32).save(f'{P}/icons/favicon-32.png')
icon(180, radius=0, bleed=True).save(f'{P}/icons/apple-touch-icon.png')
icon(192).save(f'{P}/icons/icon-192.png')
icon(512).save(f'{P}/icons/icon-512.png')
icon(512, inset=.78, bleed=True).save(f'{P}/icons/icon-maskable-512.png')
icon(256).save(f'{P}/../../images/projects/letterhop.png')
banner((1200, 630), 104, ['From one word to another,', 'one letter at a time'], 38, 88).convert('RGB').save(f'{P}/og-image.png', optimize=True)

A = f'{P}/android'
if os.path.isdir(A):
    icon(512, radius=0, bleed=True).convert('RGB').save(f'{A}/play-store-icon-512.png')
    banner((1024, 500), 92, ['One letter at a time'], 36, 76).convert('RGB').save(f'{A}/store/en-GB/feature-graphic-1024x500.png', optimize=True)
    for name, launcher, layers in (('mdpi', 48, 108), ('hdpi', 72, 162), ('xhdpi', 96, 216), ('xxhdpi', 144, 324), ('xxxhdpi', 192, 432)):
        r = f'{A}/app/src/main/res/mipmap-{name}'
        icon(launcher).save(f'{r}/ic_launcher.png')
        rnd = icon(launcher, radius=256)
        rnd.save(f'{r}/ic_launcher_round.png')
        gradient((layers, layers)).save(f'{r}/ic_launcher_background.png')
        layer(layers, .62, (255, 255, 255, 255), ORANGE + (255,), (255, 255, 255, 255)).save(f'{r}/ic_launcher_foreground.png')
        monochrome(layers, .62).save(f'{r}/ic_launcher_monochrome.png')
