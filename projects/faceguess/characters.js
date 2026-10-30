const PALETTE = {
    skin: { light: '#F6D5C0', fair: '#EBC09E', medium: '#D39B74', tan: '#B57C55', brown: '#8C5A3C', dark: '#5E3B28' },
    hair: { black: '#231C1A', brown: '#5B3A24', blonde: '#D9B26A', red: '#A4472B', gray: '#8E8C8A', white: '#E6E2DA' },
    eye: { brown: '#6B3E1F', blue: '#3C7BC4', green: '#3E8E5A', hazel: '#8C6A2E', gray: '#7C8A96' },
    shirt: { red: '#D9534F', blue: '#3D7DD8', green: '#3FA36B', yellow: '#E8C547', purple: '#8A5CC9', orange: '#E8833A', black: '#2B2B33', white: '#EDEDF0' },
    hat: { beanie: '#3E5C8A', cap: '#C94A3B', fedora: '#4A3B30', headband: '#E05A9A' },
};

const FACE_GEOMETRY = {
    round:  { hw: 23,   top: 22, chin: 72 },
    oval:   { hw: 21,   top: 21, chin: 74 },
    square: { hw: 22,   top: 22, chin: 73 },
    heart:  { hw: 22.5, top: 21, chin: 74 },
    long:   { hw: 19.5, top: 19, chin: 76 },
};

const EYE_Y = 47;
const EYE_DX = 9;
const MOUTH_Y = 64;

let svgIdSeq = 0;

function shade(hex, amount) {
    const n = parseInt(hex.slice(1), 16);
    const mixTo = amount < 0 ? 0 : 255;
    const a = Math.abs(amount);
    const ch = (v) => Math.round(v + (mixTo - v) * a);
    const r = ch((n >> 16) & 255), g = ch((n >> 8) & 255), b = ch(n & 255);
    return '#' + ((1 << 24) | (r << 16) | (g << 8) | b).toString(16).slice(1);
}

function luminance(hex) {
    const n = parseInt(hex.slice(1), 16);
    const lin = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
    return 0.2126 * lin((n >> 16) & 255) + 0.7152 * lin((n >> 8) & 255) + 0.0722 * lin(n & 255);
}

function contrastRatio(a, b) {
    const la = luminance(a), lb = luminance(b);
    return (Math.max(la, lb) + 0.05) / (Math.min(la, lb) + 0.05);
}

const MIN_HAIR_SKIN_CONTRAST = 1.3;

// Nudges the hair tone away from the skin tone until the two read as separate shapes.
function separateFromSkin(hair, skin) {
    if (contrastRatio(hair, skin) >= MIN_HAIR_SKIN_CONTRAST) return hair;
    const away = luminance(hair) >= luminance(skin) ? 1 : -1;
    for (const dir of [away, -away]) {
        for (let step = 1; step <= 20; step++) {
            const candidate = shade(hair, dir * 0.05 * step);
            if (contrastRatio(candidate, skin) >= MIN_HAIR_SKIN_CONTRAST) return candidate;
        }
    }
    return shade(hair, -0.5);
}

function headPath(f) {
    const { hw, top, chin } = f;
    const L = 50 - hw, R = 50 + hw;
    switch (f.shape) {
        case 'square':
            return `M${L} ${top + 13} Q${L} ${top} 50 ${top} Q${R} ${top} ${R} ${top + 13} L${R} ${chin - 12} Q${R - 1} ${chin - 1} ${50 + 8} ${chin} L${50 - 8} ${chin} Q${L + 1} ${chin - 1} ${L} ${chin - 12} Z`;
        case 'heart':
            return `M${L} ${top + 18} Q${L} ${top} 50 ${top} Q${R} ${top} ${R} ${top + 18} Q${R - 1} ${chin - 12} 50 ${chin} Q${L + 1} ${chin - 12} ${L} ${top + 18} Z`;
        default: {
            const cy = (top + chin) / 2, ry = (chin - top) / 2;
            return `M${L} ${cy} A${hw} ${ry} 0 1 0 ${R} ${cy} A${hw} ${ry} 0 1 0 ${L} ${cy} Z`;
        }
    }
}

function browColor(c) {
    if (c.hairColor === 'blonde') return shade(PALETTE.hair.blonde, -0.4);
    if (c.hairColor === 'gray') return '#6F6D6B';
    if (c.hairColor === 'white') return '#B9B5AD';
    return shade(PALETTE.hair[c.hairColor], -0.1);
}

function curlRow(cx, cy, rx, ry, from, to, count, r, color) {
    let out = '';
    for (let i = 0; i < count; i++) {
        const a = from + (to - from) * (i / (count - 1));
        out += `<circle cx="${(cx + Math.cos(a) * rx).toFixed(2)}" cy="${(cy + Math.sin(a) * ry).toFixed(2)}" r="${r}" fill="${color}"/>`;
    }
    return out;
}

function hairBack(c, f, hc) {
    const { top } = f;
    const L = 50 - f.hw - 2, R = 50 + f.hw + 2;
    const dark = shade(hc, -0.18);
    switch (c.hairStyle) {
        case 'long-straight':
            return `<path d="M${L - 2} 44 Q${L - 3} ${top - 5} 50 ${top - 6} Q${R + 3} ${top - 5} ${R + 2} 44 L${R + 4} 92 Q${50 + 12} 95 ${50 + 8} 84 L${50 - 8} 84 Q${50 - 12} 95 ${L - 4} 92 Z" fill="${hc}"/>`;
        case 'wavy':
            return `<path d="M${L - 2} 44 Q${L - 3} ${top - 5} 50 ${top - 6} Q${R + 3} ${top - 5} ${R + 2} 44 Q${R + 7} 54 ${R + 2} 62 Q${R - 2} 70 ${R + 3} 78 Q${R + 7} 86 ${R + 2} 92 L${50 + 8} 86 L${50 - 8} 86 L${L - 2} 92 Q${L - 7} 86 ${L - 3} 78 Q${L + 2} 70 ${L - 2} 62 Q${L - 7} 54 ${L - 2} 44 Z" fill="${hc}"/>`;
        case 'long-curly': {
            let s = `<path d="M${L - 3} 46 Q${L - 4} ${top - 6} 50 ${top - 7} Q${R + 4} ${top - 6} ${R + 3} 46 L${R + 5} 86 L${L - 5} 86 Z" fill="${hc}"/>`;
            for (let y = 40; y <= 86; y += 7) {
                s += `<circle cx="${L - 3}" cy="${y}" r="5.5" fill="${y % 2 ? hc : dark}"/>`;
                s += `<circle cx="${R + 3}" cy="${y}" r="5.5" fill="${y % 2 ? hc : dark}"/>`;
            }
            return s;
        }
        case 'bob':
            return `<path d="M${L - 2} 44 Q${L - 3} ${top - 5} 50 ${top - 6} Q${R + 3} ${top - 5} ${R + 2} 44 L${R + 2} 68 Q${R - 1} 72 ${R - 4} 67 L${L + 4} 67 Q${L + 1} 72 ${L - 2} 68 Z" fill="${hc}"/>`;
        case 'ponytail':
            return `<path d="M${R - 4} ${top + 4} Q${R + 15} ${top + 6} ${R + 10} 62 Q${R + 7} 72 ${R + 2} 66 Q${R + 7} 48 ${R - 2} ${top + 14} Z" fill="${hc}"/><circle cx="${R + 1}" cy="${top + 9}" r="2.2" fill="${shade(PALETTE.hat.headband, -0.1)}"/>`;
        case 'bun':
            return `<circle cx="50" cy="${top - 5}" r="9" fill="${hc}"/><path d="M44 ${top - 7} Q50 ${top - 11} 56 ${top - 7}" stroke="${dark}" stroke-width="1" fill="none"/>`;
        case 'braids': {
            let s = '';
            for (const x of [L - 1, R + 1]) {
                const toward = x < 50 ? 1 : -1;
                s += `<path d="M${x + toward * 4} 36 L${x - toward * 2.5} 38 L${x - toward * 3} 54 L${x + toward * 3} 54 Z" fill="${hc}"/>`;
                for (let y = 54; y <= 90; y += 6) {
                    s += `<ellipse cx="${x}" cy="${y}" rx="3.6" ry="3.8" fill="${y % 12 ? hc : dark}"/>`;
                }
                s += `<rect x="${x - 2}" y="91" width="4" height="2.5" rx="1" fill="${PALETTE.hat.headband}"/>`;
            }
            return s;
        }
        default:
            return '';
    }
}

function hairFront(c, f, hc) {
    const { top } = f;
    const L = 50 - f.hw - 2, R = 50 + f.hw + 2;
    const dark = shade(hc, -0.2);
    const cap = (vol, hairline, opacity = 1) =>
        `<path d="M${L} 44 Q${L - 1} ${top - vol} 50 ${top - vol - 1} Q${R + 1} ${top - vol} ${R} 44 Q${R - 3} ${top + hairline + 1} 50 ${top + hairline} Q${L + 3} ${top + hairline + 1} ${L} 44 Z" fill="${hc}" opacity="${opacity}"/>`;

    // Same outline as cap(), but with a slightly irregular fringe so it doesn't read as a helmet.
    const textured = (vol, hairline) => {
        const n = 9;
        let d = `M${L} 44 Q${L - 1} ${top - vol} 50 ${top - vol - 1} Q${R + 1} ${top - vol} ${R} 44`;
        for (let i = 1; i < n; i++) {
            const t = i / n;
            const x = R - (R - L) * t;
            const arch = 1 - Math.pow(2 * t - 1, 2);
            const y = 44 + (top + hairline - 44) * arch + (i % 2 ? 1.6 : -0.4);
            d += ` L${x.toFixed(2)} ${y.toFixed(2)}`;
        }
        return `<path d="${d} L${L} 44 Z" fill="${hc}"/>`;
    };

    switch (c.hairStyle) {
        case 'bald':
            return `<ellipse cx="43" cy="${top + 6}" rx="6" ry="3" fill="#fff" opacity="0.18"/>`;
        case 'short':
            return textured(4, 12);
        case 'buzz':
            return `<path d="M0 0 H100 V44 L${R} 44 Q${R - 3} ${top + 12} 50 ${top + 11} Q${L + 3} ${top + 12} ${L} 44 L0 44 Z" fill="${hc}" opacity="0.9" clip-path="url(#${f.clipId})"/>`;
        case 'slicked':
            return cap(8, 9) +
                `<path d="M42 ${top - 4} Q50 ${top - 7} 58 ${top - 4} M40 ${top} Q50 ${top - 3} 60 ${top}" stroke="${dark}" stroke-width="0.8" fill="none"/>`;
        case 'side-part':
            return `<path d="M${L} 44 Q${L - 1} ${top - 5} 50 ${top - 6} Q${R + 1} ${top - 5} ${R} 44 Q${R - 2} ${top + 10} ${50 + 6} ${top + 9} Q44 ${top + 11} ${L + 2} ${top + 17} Q${L} 32 ${L} 44 Z" fill="${hc}"/>` +
                `<path d="M43 ${top - 4} Q42 ${top + 3} 43 ${top + 10}" stroke="${dark}" stroke-width="0.9" fill="none"/>`;
        case 'curly':
            return cap(3, 12) + curlRow(50, top + 14, f.hw + 1, 17, Math.PI * 1.02, Math.PI * 1.98, 11, 5, hc) +
                curlRow(50, top + 14, f.hw - 6, 15, Math.PI * 1.15, Math.PI * 1.85, 6, 4, dark);
        case 'spiky': {
            const n = 7, w = (R - L) / n;
            let d = `M${L} 44 L${L} ${top + 4}`;
            for (let i = 0; i < n; i++) {
                const x = L + i * w;
                d += ` L${(x + w / 2).toFixed(2)} ${top - 7 - (i % 2) * 2} L${(x + w).toFixed(2)} ${top + 1}`;
            }
            d += ` L${R} 44 Q${R - 3} ${top + 13} 50 ${top + 12} Q${L + 3} ${top + 13} ${L} 44 Z`;
            return `<path d="${d}" fill="${hc}"/>`;
        }
        case 'mohawk':
            return cap(1, 11, 0.3) +
                `<path d="M46 ${top + 10} Q44 ${top - 12} 50 ${top - 15} Q56 ${top - 12} 54 ${top + 10} Z" fill="${hc}"/>`;
        case 'long-straight':
        case 'wavy':
            return `<path d="M${L} 72 L${L} 38 C${L} ${top - 2} ${50 - 12} ${top - 5} 50 ${top - 5} C${50 + 12} ${top - 5} ${R} ${top - 2} ${R} 38 L${R} 72 Q${R - 4} 52 ${R - 3} 40 Q${R - 6} ${top + 10} 50 ${top + 8} Q${L + 6} ${top + 10} ${L + 3} 40 Q${L + 4} 52 ${L} 72 Z" fill="${hc}"/>` +
                `<path d="M50 ${top - 4} L50 ${top + 8}" stroke="${dark}" stroke-width="0.8"/>`;
        case 'long-curly':
            return cap(5, 11) + curlRow(50, top + 13, f.hw + 2, 16, Math.PI * 1.0, Math.PI * 2.0, 12, 4.5, hc);
        case 'bob':
            return `<path d="M${L} 66 L${L} 38 C${L} ${top - 2} ${50 - 12} ${top - 5} 50 ${top - 5} C${50 + 12} ${top - 5} ${R} ${top - 2} ${R} 38 L${R} 66 Q${R - 3} 52 ${R - 3} 40 L${R - 3} ${top + 16} Q50 ${top + 18} ${L + 3} ${top + 16} L${L + 3} 40 Q${L + 3} 52 ${L} 66 Z" fill="${hc}"/>`;
        case 'ponytail':
        case 'bun':
            return cap(3, 10) +
                `<path d="M44 ${top - 2} Q50 ${top - 4} 56 ${top - 2}" stroke="${dark}" stroke-width="0.7" fill="none"/>`;
        case 'pixie':
            return `<path d="M${L} 50 Q${L - 2} ${top - 7} 50 ${top - 8} Q${R + 2} ${top - 7} ${R} 48 Q${R - 1} ${top + 12} ${R - 6} ${top + 12} Q${50 + 4} ${top + 11} ${50 - 2} ${top + 16} Q${50 - 9} ${top + 21} ${L + 3} ${top + 21} Q${L + 1} 42 ${L} 50 Z" fill="${hc}"/>` +
                `<path d="M${R} 44 Q${R + 0.6} 50 ${R - 1} 54 Q${R - 2.2} 49 ${R - 2.6} 44 Z M${L} 44 Q${L - 0.6} 50 ${L + 1} 54 Q${L + 2.2} 49 ${L + 2.6} 44 Z" fill="${hc}"/>` +
                `<path d="M${50 + 7} ${top - 5} Q${50 + 6} ${top + 2} ${50 + 9} ${top + 9}" stroke="${dark}" stroke-width="0.8" fill="none"/>`;
        case 'braids':
            return cap(3, 10) + `<path d="M50 ${top - 3} L50 ${top + 10}" stroke="${dark}" stroke-width="0.8"/>`;
        default:
            return '';
    }
}

const STYLES_COVERING_EARS = ['long-straight', 'wavy', 'long-curly', 'bob'];
const STYLES_WITH_SIDEBURNS =['short', 'buzz', 'side-part', 'slicked', 'curly', 'spiky'];

function sideburns(c, f, hc) {
    if (c.gender !== 'male' || !STYLES_WITH_SIDEBURNS.includes(c.hairStyle)) return '';
    const L = 50 - f.hw, R = 50 + f.hw;
    const opacity = c.hairStyle === 'buzz' ? 0.9 : 1;
    return `<path d="M${L} 39 L${L + 3.4} 40 L${L + 2.8} 51 L${L} 52.5 Z M${R} 39 L${R - 3.4} 40 L${R - 2.8} 51 L${R} 52.5 Z" fill="${hc}" opacity="${opacity}" clip-path="url(#${f.clipId})"/>`;
}

function facialHair(c, f, hc) {
    if (c.gender !== 'male' || c.facialHair === 'none') return '';
    const L = 50 - f.hw, R = 50 + f.hw;
    const mustache = `<path d="M43.5 61.6 Q46 58.6 50 59.8 Q54 58.6 56.5 61.6 Q53 60.9 50 61.5 Q47 60.9 43.5 61.6 Z" fill="${hc}"/>`;
    const mouthHole = `M42.5 ${MOUTH_Y} A7.5 3.2 0 1 0 57.5 ${MOUTH_Y} A7.5 3.2 0 1 0 42.5 ${MOUTH_Y} Z`;

    switch (c.facialHair) {
        case 'stubble':
            return `<path d="M${L} 53 Q${L + 2} ${f.chin + 2} 50 ${f.chin + 2} Q${R - 2} ${f.chin + 2} ${R} 53 Q${R - 3} 59.5 ${50 + 8} 61.6 Q50 60.4 ${50 - 8} 61.6 Q${L + 3} 59.5 ${L} 53 Z" fill="url(#${f.clipId}-stubble)" clip-path="url(#${f.clipId})"/>`;
        case 'mustache':
            return mustache;
        case 'goatee':
            return mustache + `<path d="M46 67.5 Q50 66.4 54 67.5 Q54 ${f.chin} 50 ${f.chin + 1.5} Q46 ${f.chin} 46 67.5 Z" fill="${hc}"/>`;
        case 'beard':
            return `<path fill-rule="evenodd" d="M${L} 48 L${L} 54 Q${L + 1} ${f.chin + 5} 50 ${f.chin + 6} Q${R - 1} ${f.chin + 5} ${R} 54 L${R} 48 Q${R - 2} 58 ${50 + 9} 60 Q50 58.5 ${50 - 9} 60 Q${L + 2} 58 ${L} 48 Z ${mouthHole}" fill="${hc}"/>` + mustache;
        default:
            return '';
    }
}

function eyes(c, skin) {
    const iris = PALETTE.eye[c.eyeColor];
    const lid = c.gender === 'female' ? '#2A1A14' : shade(skin, -0.45);
    const lidWidth = c.gender === 'female' ? 1.1 : 0.8;
    let s = '';

    if (c.makeup === 'eyeshadow' || c.makeup === 'full') {
        for (const x of [50 - EYE_DX, 50 + EYE_DX]) {
            s += `<ellipse cx="${x}" cy="${EYE_Y - 1.8}" rx="5.2" ry="2.8" fill="#8E5BB5" opacity="0.5"/>`;
        }
    }
    for (const [x, dir] of [[50 - EYE_DX, -1], [50 + EYE_DX, 1]]) {
        s += `<ellipse cx="${x}" cy="${EYE_Y}" rx="4" ry="2.6" fill="#fff"/>`;
        s += `<circle cx="${x}" cy="${EYE_Y}" r="2" fill="${iris}"/>`;
        s += `<circle cx="${x}" cy="${EYE_Y}" r="1" fill="#141414"/>`;
        s += `<circle cx="${x + 0.7}" cy="${EYE_Y - 0.7}" r="0.55" fill="#fff"/>`;
        s += `<path d="M${x - 4.2} ${EYE_Y + 0.3} Q${x} ${EYE_Y - 3.6} ${x + 4.2} ${EYE_Y + 0.3}" stroke="${lid}" stroke-width="${lidWidth}" fill="none" stroke-linecap="round"/>`;
        if (c.gender === 'female') {
            s += `<path d="M${x + dir * 4} ${EYE_Y - 0.4} l${dir * 1.4} -1.2" stroke="${lid}" stroke-width="0.8" stroke-linecap="round"/>`;
        }
    }
    return s;
}

function eyebrows(c) {
    const w = { thin: 0.9, medium: 1.5, thick: 2.3 }[c.eyebrows];
    const col = browColor(c);
    let s = '';
    for (const x of [50 - EYE_DX, 50 + EYE_DX]) {
        s += `<path d="M${x - 5} 42 Q${x} 39 ${x + 5} 41.2" stroke="${col}" stroke-width="${w}" fill="none" stroke-linecap="round"/>`;
    }
    return s;
}

function nose(c, skin) {
    const w = { small: 2.2, medium: 3, large: 4 }[c.nose];
    const col = shade(skin, -0.3);
    return `<path d="M51 48.5 Q52 53 ${50 + w * 0.6} 55.5" stroke="${col}" stroke-width="0.8" fill="none" opacity="0.6" stroke-linecap="round"/>` +
        `<path d="M${50 - w} 56 Q50 58.6 ${50 + w} 56" stroke="${col}" stroke-width="1.1" fill="none" stroke-linecap="round"/>`;
}

function mouth(c, skin) {
    const lipstick = c.makeup === 'lipstick' || c.makeup === 'full';
    const lip = lipstick ? '#C43C5A' : shade(skin, -0.38);
    const width = lipstick ? 2.2 : 1.4;
    switch (c.mouth) {
        case 'grin':
            return `<path d="M44 62.6 Q50 69.5 56 62.6 Q50 64.4 44 62.6 Z" fill="#5A2626" stroke="${lip}" stroke-width="${lipstick ? 1.4 : 0.8}" stroke-linejoin="round"/>` +
                `<path d="M45.6 63.3 Q50 64.6 54.4 63.3 L53.8 64.5 Q50 65.6 46.2 64.5 Z" fill="#fff"/>`;
        case 'smile':
            return `<path d="M44.5 63 Q50 67.5 55.5 63" stroke="${lip}" stroke-width="${width}" fill="none" stroke-linecap="round"/>`;
        default:
            return `<path d="M45 ${MOUTH_Y} Q50 ${MOUTH_Y + 1.3} 55 ${MOUTH_Y}" stroke="${lip}" stroke-width="${width}" fill="none" stroke-linecap="round"/>`;
    }
}

function freckles(skin) {
    const pts = [[43, 54], [45.5, 56.2], [41, 57], [57, 54], [54.5, 56.2], [59, 57], [47, 53.4], [53, 53.4]];
    return pts.map(([x, y]) => `<circle cx="${x}" cy="${y}" r="0.55" fill="${shade(skin, -0.42)}" opacity="0.7"/>`).join('');
}

function glasses(c, f) {
    if (c.glasses === 'none') return '';
    const frame = '#262626';
    const L = 50 - f.hw, R = 50 + f.hw;
    const lx = 50 - EYE_DX, rx = 50 + EYE_DX;
    let s = `<path d="M${L} 45.5 L${lx - 6.5} 46 M${R} 45.5 L${rx + 6.5} 46" stroke="${frame}" stroke-width="1"/>`;
    s += `<path d="M${lx + 6} 46.4 Q50 44.6 ${rx - 6} 46.4" stroke="${frame}" stroke-width="1" fill="none"/>`;

    if (c.glasses === 'round') {
        for (const x of [lx, rx]) s += `<circle cx="${x}" cy="${EYE_Y}" r="6" fill="#fff" fill-opacity="0.08" stroke="${frame}" stroke-width="1.1"/>`;
    } else if (c.glasses === 'square') {
        for (const x of [lx, rx]) s += `<rect x="${x - 6.5}" y="${EYE_Y - 4.5}" width="13" height="9" rx="2" fill="#fff" fill-opacity="0.08" stroke="${frame}" stroke-width="1.3"/>`;
    } else {
        for (const x of [lx, rx]) {
            s += `<path d="M${x - 6.5} ${EYE_Y - 4} L${x + 6.5} ${EYE_Y - 4} Q${x + 6.5} ${EYE_Y + 5} ${x} ${EYE_Y + 5} Q${x - 6.5} ${EYE_Y + 5} ${x - 6.5} ${EYE_Y - 4} Z" fill="#17171D" stroke="${frame}" stroke-width="1"/>`;
            s += `<path d="M${x - 4.5} ${EYE_Y + 0.5} L${x - 1.5} ${EYE_Y - 2.5}" stroke="#fff" stroke-width="0.9" opacity="0.3" stroke-linecap="round"/>`;
        }
    }
    return s;
}

function hat(c, f) {
    if (c.hat === 'none') return '';
    const { top, hw } = f;
    const L = 50 - hw - 2, R = 50 + hw + 2;
    const col = PALETTE.hat[c.hat];
    const dark = shade(col, -0.25);
    switch (c.hat) {
        case 'beanie':
            return `<path d="M${L - 2} ${top + 16} Q${L - 2} ${top - 14} 50 ${top - 14} Q${R + 2} ${top - 14} ${R + 2} ${top + 16} Z" fill="${col}"/>` +
                `<rect x="${L - 3}" y="${top + 9}" width="${R - L + 6}" height="8" rx="3" fill="${dark}"/>` +
                `<circle cx="50" cy="${top - 15}" r="4" fill="${shade(col, 0.35)}"/>`;
        case 'cap':
            return `<path d="M${L - 1} ${top + 11} Q${L - 1} ${top - 11} 50 ${top - 11} Q${R + 1} ${top - 11} ${R + 1} ${top + 11} Z" fill="${col}"/>` +
                `<path d="M${L - 3} ${top + 11} Q50 ${top + 5} ${R + 3} ${top + 11} Q${R - 2} ${top + 18} 50 ${top + 17} Q${L + 2} ${top + 18} ${L - 3} ${top + 11} Z" fill="${dark}"/>` +
                `<circle cx="50" cy="${top - 11}" r="1.6" fill="${dark}"/>`;
        case 'fedora':
            return `<path d="M${50 - hw + 3} ${top + 8} L${50 - hw + 6} ${top - 12} Q50 ${top - 6} ${50 + hw - 6} ${top - 12} L${50 + hw - 3} ${top + 8} Z" fill="${col}"/>` +
                `<rect x="${50 - hw + 3.5}" y="${top + 1}" width="${2 * hw - 7}" height="5" fill="${shade(col, -0.45)}"/>` +
                `<ellipse cx="50" cy="${top + 8.5}" rx="${hw + 14}" ry="4" fill="${dark}"/>`;
        case 'headband':
            return `<path d="M${L} ${top + 12} Q50 ${top - 2} ${R} ${top + 12} L${R} ${top + 17} Q50 ${top + 3} ${L} ${top + 17} Z" fill="${col}"/>`;
        default:
            return '';
    }
}

function earrings(c, f) {
    if (c.earrings === 'none') return '';
    const gold = '#E2B53E';
    let s = '';
    for (const x of [50 - f.hw - 0.5, 50 + f.hw + 0.5]) {
        if (c.earrings === 'studs') s += `<circle cx="${x}" cy="55.5" r="1.3" fill="${gold}"/>`;
        if (c.earrings === 'hoops') s += `<circle cx="${x}" cy="58" r="2.8" fill="none" stroke="${gold}" stroke-width="0.9"/>`;
        if (c.earrings === 'drops') s += `<path d="M${x} 55 L${x} 59" stroke="${gold}" stroke-width="0.7"/><circle cx="${x}" cy="60.5" r="1.6" fill="#4FB3BF"/>`;
    }
    return s;
}

function renderCharacterSVG(c) {
    const geo = FACE_GEOMETRY[c.faceShape] || FACE_GEOMETRY.oval;
    const f = { ...geo, shape: c.faceShape, clipId: 'fg-face-' + (++svgIdSeq) };
    const skin = PALETTE.skin[c.skinTone] || PALETTE.skin.medium;
    const hc = separateFromSkin(PALETTE.hair[c.hairColor] || PALETTE.hair.brown, skin);
    const hairOutline = (markup) => markup && c.hairStyle !== 'bald' && c.hairStyle !== 'buzz'
        ? `<g stroke="${shade(hc, -0.38)}" stroke-width="0.7" stroke-linejoin="round">${markup}</g>`
        : markup;
    const shirt = PALETTE.shirt[c.shirtColor] || PALETTE.shirt.blue;
    const neck = shade(skin, -0.12);
    const head = headPath(f);

    let s = `<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="${escapeAttr(c.name)}">`;
    s += `<defs><clipPath id="${f.clipId}"><path d="${head}"/></clipPath>` +
        `<radialGradient id="${f.clipId}-bg" cx="50%" cy="40%" r="62%"><stop offset="0" stop-color="#5A5A92"/><stop offset="0.55" stop-color="#3E3E6A"/><stop offset="1" stop-color="#2B2B4A"/></radialGradient>` +
        `<pattern id="${f.clipId}-stubble" width="2.2" height="2.2" patternUnits="userSpaceOnUse"><circle cx="0.55" cy="0.55" r="0.3" fill="${hc}" opacity="0.6"/><circle cx="1.65" cy="1.65" r="0.3" fill="${hc}" opacity="0.6"/></pattern></defs>`;
    s += `<rect width="100" height="100" fill="url(#${f.clipId}-bg)"/>`;

    s += `<path d="M10 100 C12 87 28 82 43 81 L57 81 C72 82 88 87 90 100 Z" fill="${shirt}"/>`;
    s += hairOutline(hairBack(c, f, hc));
    s += `<rect x="43" y="${f.chin - 8}" width="14" height="${89 - f.chin}" fill="${neck}"/>`;
    s += `<path d="M43 81 Q50 89 57 81 Z" fill="${neck}"/>`;
    s += `<path d="M40 81.5 Q50 92 60 81.5" stroke="${shade(shirt, -0.25)}" stroke-width="1.4" fill="none"/>`;

    if (!STYLES_COVERING_EARS.includes(c.hairStyle)) {
        for (const x of [50 - f.hw, 50 + f.hw]) {
            s += `<ellipse cx="${x}" cy="49.5" rx="3.6" ry="5.6" fill="${skin}"/>`;
            s += `<ellipse cx="${x}" cy="49.5" rx="1.6" ry="3.2" fill="${shade(skin, -0.15)}"/>`;
        }
    }
    s += `<path d="${head}" fill="${skin}"/>`;

    if (c.makeup === 'full') {
        s += `<ellipse cx="37" cy="57" rx="4" ry="2.6" fill="#F06A8A" opacity="0.22"/><ellipse cx="63" cy="57" rx="4" ry="2.6" fill="#F06A8A" opacity="0.22"/>`;
    }
    if (c.freckles) s += freckles(skin);

    s += eyes(c, skin);
    s += eyebrows(c);
    s += nose(c, skin);
    s += facialHair(c, f, hc);
    s += mouth(c, skin);
    s += sideburns(c, f, hc);
    s += hairOutline(hairFront(c, f, hc));
    s += earrings(c, f);
    s += glasses(c, f);
    s += hat(c, f);

    s += `</svg>`;
    return s;
}

function escapeAttr(str) {
    return String(str).replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
}
