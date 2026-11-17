"""OpenNewsletter icons: python3 tools/make_icons.py (needs Pillow and Inkscape).

The logo is icons/favicon.svg: an "O" ring (Open) around an "N" (Newsletter) filled with the red, white and blue
diagonal stripes of a classic British airmail envelope, on an off-white tile. The PNGs are that SVG rendered by Inkscape. icon-64.png is the small logo in the footer of every
newsletter (email clients do not show SVG).
"""
import shutil
import subprocess
import tempfile
from pathlib import Path
from PIL import Image

ROOT = Path(__file__).resolve().parent.parent
ICONS = ROOT / 'icons'
INKSCAPE = shutil.which('inkscape') or '/Applications/Inkscape.app/Contents/MacOS/inkscape'


def render(size):
    with tempfile.TemporaryDirectory() as tmp:
        out = Path(tmp) / 'logo.png'
        subprocess.run([INKSCAPE, str(ICONS / 'favicon.svg'), '--export-type=png', f'--export-filename={out}', f'--export-width={size}', f'--export-height={size}'],
                       check=True, capture_output=True)
        return Image.open(out).convert('RGBA')


for name, size in [('favicon-32.png', 32), ('icon-64.png', 64), ('icon-192.png', 192), ('icon-512.png', 512)]:
    render(size).save(ICONS / name, optimize=True)

# apple-touch-icon: no transparency (iOS paints it black)
img = render(180)
bg = Image.new('RGBA', img.size, '#FFFDF8')
bg.alpha_composite(img)
bg.convert('RGB').save(ICONS / 'apple-touch-icon.png', optimize=True)
print('icons written to', ICONS)
