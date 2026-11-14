"""Regenerates lib/certificate/template.png: everything of the certificate that never changes (paper, frame, logo, seal).
The texts are written on it by lib/certificate.php. Needs Pillow: python3 tools/build-certificate.py"""
import math
from pathlib import Path
from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = Path(__file__).resolve().parent.parent
W, H, SS = 2400, 1697, 2
GOLD, INK, BRAND = (185, 138, 46, 255), (27, 27, 75, 255), (217, 58, 20, 255)
s = lambda v: round(v * SS)

# Paper: lighter in the middle, a little warmer at the edges
glow = Image.radial_gradient('L').resize((W * SS, H * SS), Image.BICUBIC)
page = Image.composite(Image.new('RGBA', glow.size, (244, 233, 210, 255)), Image.new('RGBA', glow.size, (255, 253, 247, 255)), glow.point(lambda v: min(255, int(v * 1.0))))
d = ImageDraw.Draw(page)

# Frame
d.rectangle((s(53), s(53), s(W - 53), s(H - 53)), outline=GOLD, width=s(8))
d.rectangle((s(68), s(68), s(W - 68), s(H - 68)), outline=GOLD, width=s(2))

# Corners: an L and a code bracket
def corner(x, y, fx, fy):
    p = lambda a, b: (s(x + a * fx), s(y + b * fy))
    d.line([p(0, 96), p(0, 0), p(96, 0)], fill=GOLD, width=s(3), joint='curve')
    d.line([p(48, 24), p(24, 48), p(48, 72)], fill=GOLD, width=s(6), joint='curve')

corner(90, 90, 1, 1); corner(W - 90, 90, -1, 1); corner(90, H - 90, 1, -1); corner(W - 90, H - 90, -1, -1)

# Logo and name, the first thing to read
logo = Image.open(ROOT / 'assets/icons/icon-512.png').convert('RGBA').resize((s(176), s(176)), Image.LANCZOS)
font = ImageFont.truetype(str(ROOT / 'lib/certificate/fonts/Fredoka-SemiBold.ttf'), s(158))
word = d.textlength('Fucina', font=font)
total = s(176) + s(34) + word
x = (W * SS - total) / 2
page.alpha_composite(logo, (round(x), s(112)))
d.text((x + s(176) + s(34), s(200)), 'Fucina', font=font, fill=BRAND, anchor='lm')

# Lines of the date and the signature
for a, b in ((230, 830), (1570, 2170)):
    d.line([(s(a), s(1490)), (s(b), s(1490))], fill=INK, width=s(2))

# Seal
cx, cy, r = 1200, 1432, 128
foil = Image.new('RGBA', page.size, (0, 0, 0, 0))
pts = []
for i in range(48):
    a = math.pi * i / 24 - math.pi / 2
    rad = r if i % 2 == 0 else r * .9
    pts.append((s(cx + rad * math.cos(a)), s(cy + rad * math.sin(a))))
col = Image.new('RGBA', page.size, (0, 0, 0, 0))
top, bottom = (243, 221, 154), (200, 151, 47)
band = Image.new('RGBA', (1, 256))
for y in range(256):
    t = abs(y / 255 - .5) * 2
    band.putpixel((0, y), tuple(round(b + (a - b) * t) for a, b in zip(top, bottom)) + (255,))
band = band.resize((s(2 * r), s(2 * r)), Image.BICUBIC)
col.paste(band, (s(cx - r), s(cy - r)))
mask = Image.new('L', page.size, 0)
ImageDraw.Draw(mask).polygon(pts, fill=255)
shadow = Image.new('RGBA', page.size, (120, 80, 10, 0))
shadow.putalpha(mask.filter(ImageFilter.GaussianBlur(s(8))).point(lambda v: int(v * .4)))
page.alpha_composite(shadow, (0, s(6)))
page.paste(col, (0, 0), mask)
d = ImageDraw.Draw(page)
c = lambda rad: (s(cx - rad), s(cy - rad), s(cx + rad), s(cy + rad))
d.ellipse(c(r * .68), fill=INK)
d.ellipse(c(r * .6), outline=(231, 200, 119, 255), width=s(2))
k = r * .0100
P = lambda pts2: [(s(cx + (x2 - 50) * k * 1.0), s(cy + (y2 - 50) * k * 1.0)) for x2, y2 in pts2]
for arm in ([(34, 34), (19, 50), (34, 66)], [(66, 34), (81, 50), (66, 66)]):
    d.line(P(arm), fill=(255, 122, 77, 255), width=s(9), joint='curve')
d.polygon(P([(55, 22), (39, 54), (49, 54), (45, 78), (63, 45), (52, 45)]), fill=(255, 244, 205, 255))

page.resize((W, H), Image.LANCZOS).convert('RGB').save(ROOT / 'lib/certificate/template.png', optimize=True)
print('Written lib/certificate/template.png')
