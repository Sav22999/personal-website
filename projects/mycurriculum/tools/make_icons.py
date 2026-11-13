"""MyCurriculum icons: python3 tools/make_icons.py (needs Pillow and Inkscape).

The logo is icons/favicon.svg: a CV sheet (photo, title, text lines, folded corner) on an indigo tile, with an
amber globe badge for the many languages. The PNGs are that SVG rendered by Inkscape; the maskable one has no
rounded corners and the drawing scaled into the safe zone. og-image.png is the logo with the name and the tagline,
written with the CV fonts of the app (fonts/).
"""
import re
import shutil
import subprocess
import tempfile
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parent.parent
ICONS = ROOT / 'icons'
SITE_IMAGE = ROOT.parent.parent / 'images' / 'projects' / 'mycurriculum.png'
SVG = (ICONS / 'favicon.svg').read_text()
INKSCAPE = shutil.which('inkscape') or '/Applications/Inkscape.app/Contents/MacOS/inkscape'


def render(svg, size):
    with tempfile.TemporaryDirectory() as tmp:
        src, out = Path(tmp) / 'logo.svg', Path(tmp) / 'logo.png'
        src.write_text(svg)
        subprocess.run([INKSCAPE, str(src), '--export-type=png', f'--export-filename={out}', f'--export-width={size}', f'--export-height={size}'],
                       check=True, capture_output=True)
        return Image.open(out).convert('RGBA')


def logo(size, maskable=False):
    svg = SVG
    if maskable:
        svg = svg.replace('<rect width="64" height="64" rx="15"', '<rect width="64" height="64" rx="0"')
        body = re.search(r'(<rect width="64" height="64" rx="0" fill="url\(#bg\)"/>)(.*)(</svg>)', svg, re.S)
        svg = svg[:body.start(2)] + '<g transform="translate(32 32) scale(.8) translate(-32 -32)">' + body.group(2) + '</g>' + svg[body.end(2):]
    return render(svg, size)


def og_image():
    W, H = 1200, 630
    img = Image.new('RGB', (W, H), (244, 242, 236))
    d = ImageDraw.Draw(img)
    # soft indigo band on the right with a sample CV sheet
    d.rounded_rectangle((690, -40, 1260, 670), radius=60, fill=(61, 82, 213))
    sheet = (770, 90, 1130, 560)
    d.rounded_rectangle((sheet[0] + 10, sheet[1] + 14, sheet[2] + 10, sheet[3] + 14), radius=18, fill=(40, 52, 150))
    d.rounded_rectangle(sheet, radius=18, fill=(255, 255, 255))
    d.rectangle((sheet[0], sheet[1] + 18, sheet[0] + 110, sheet[3] - 18), fill=(61, 82, 213))
    d.rounded_rectangle((sheet[0], sheet[1], sheet[0] + 110, sheet[1] + 40), radius=18, fill=(61, 82, 213))
    d.rounded_rectangle((sheet[0], sheet[3] - 40, sheet[0] + 110, sheet[3]), radius=18, fill=(61, 82, 213))
    d.ellipse((sheet[0] + 22, sheet[1] + 28, sheet[0] + 88, sheet[1] + 94), fill=(255, 255, 255))
    for i, w in enumerate([70, 54, 62, 40, 58, 46]):
        y = sheet[1] + 130 + i * 26
        d.rounded_rectangle((sheet[0] + 20, y, sheet[0] + 20 + w, y + 8), radius=4, fill=(150, 162, 235))
    x0 = sheet[0] + 135
    d.rounded_rectangle((x0, sheet[1] + 34, x0 + 170, sheet[1] + 52), radius=9, fill=(30, 34, 48))
    d.rounded_rectangle((x0, sheet[1] + 64, x0 + 110, sheet[1] + 76), radius=6, fill=(61, 82, 213))
    for block in range(3):
        y = sheet[1] + 120 + block * 120
        d.rounded_rectangle((x0, y, x0 + 70, y + 10), radius=5, fill=(61, 82, 213))
        for j, w in enumerate([190, 160, 175]):
            d.rounded_rectangle((x0, y + 26 + j * 20, x0 + w, y + 34 + j * 20), radius=4, fill=(210, 214, 228))

    serif = ImageFont.truetype(str(ROOT / 'fonts' / 'PT_Serif-Web-Bold.ttf'), 76)
    sans = ImageFont.truetype(str(ROOT / 'fonts' / 'Roboto-Regular.ttf'), 29)
    sans_b = ImageFont.truetype(str(ROOT / 'fonts' / 'Roboto-Medium.ttf'), 29)
    mark = logo(96)
    img.paste(mark, (80, 90), mark)
    d.text((196, 104), 'My', font=ImageFont.truetype(str(ROOT / 'fonts' / 'Roboto-Medium.ttf'), 46), fill=(30, 34, 48))
    d.text((196 + d.textlength('My', font=ImageFont.truetype(str(ROOT / 'fonts' / 'Roboto-Medium.ttf'), 46)), 104), 'Curriculum', font=ImageFont.truetype(str(ROOT / 'fonts' / 'Roboto-Regular.ttf'), 46), fill=(100, 106, 130))
    d.text((80, 250), 'Your CV,', font=serif, fill=(27, 31, 59))
    d.rectangle((80, 392, 80 + d.textlength('every language', font=serif), 412), fill=(255, 205, 140))
    d.text((80, 336), 'every language', font=serif, fill=(61, 82, 213))
    d.text((80, 470), 'Templates · PDF download · Web page', font=sans, fill=(100, 106, 130))
    d.text((80, 520), 'Import your existing CV from a PDF', font=sans_b, fill=(27, 31, 59))
    return img


def main():
    logo(32).save(ICONS / 'favicon-32.png')
    flat = logo(180)
    bg = Image.new('RGBA', flat.size, (61, 82, 213, 255))
    apple = logo(180, maskable=True)
    bg.alpha_composite(apple)
    bg.convert('RGB').save(ICONS / 'apple-touch-icon.png')
    logo(192).save(ICONS / 'icon-192.png')
    logo(512).save(ICONS / 'icon-512.png')
    logo(512, maskable=True).save(ICONS / 'icon-maskable-512.png')
    logo(256).save(SITE_IMAGE)
    og_image().save(ICONS / 'og-image.png')
    print('icons written')


if __name__ == '__main__':
    main()
