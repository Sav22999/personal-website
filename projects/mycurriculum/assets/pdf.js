/**
 * MyCurriculum — the PDF of a CV, made in the browser with pdfmake (real text: it can be selected and searched).
 *
 * Each template here mirrors its HTML twin in cv.css, from the same model (cv.js buildModel). Measures are written
 * in CSS pixels and converted with P(): an A4 page is 794 px wide in the preview and 595 pt in the PDF.
 */

import {ICONS, blocks} from './cv.js';

const P = (px) => px * 0.75;
const INK = '#1E2230';
const SOFT = '#5B6175';
const LINE = '#E3E5EC';
const PAGE_W = 595.28;
const PAGE_H = 841.89;

const FONT_FILES = {
    Sans: {normal: 'Roboto-Regular.ttf', bold: 'Roboto-Medium.ttf', italics: 'Roboto-Italic.ttf', bolditalics: 'Roboto-MediumItalic.ttf'},
    Serif: {normal: 'PT_Serif-Web-Regular.ttf', bold: 'PT_Serif-Web-Bold.ttf', italics: 'PT_Serif-Web-Italic.ttf', bolditalics: 'PT_Serif-Web-BoldItalic.ttf'},
};

let ready = null;

/** Loads pdfmake and the fonts once. base: URL of the app folder. */
function loadPdfMake(base) {
    if (ready) return ready;
    ready = (async () => {
        if (!window.pdfMake) {
            await new Promise((ok, ko) => {
                const s = document.createElement('script');
                s.src = base + 'vendor/pdfmake.min.js';
                s.onload = ok;
                s.onerror = () => ko(new Error('pdfmake'));
                document.head.appendChild(s);
            });
        }
        const vfs = {};
        const files = [...new Set(Object.values(FONT_FILES).flatMap((f) => Object.values(f)))];
        await Promise.all(files.map(async (f) => {
            const buf = await (await fetch(base + 'fonts/' + f)).arrayBuffer();
            let bin = '';
            const bytes = new Uint8Array(buf);
            for (let i = 0; i < bytes.length; i += 0x8000) bin += String.fromCharCode.apply(null, bytes.subarray(i, i + 0x8000));
            vfs[f] = btoa(bin);
        }));
        window.pdfMake.vfs = vfs;
        window.pdfMake.fonts = FONT_FILES;
        return window.pdfMake;
    })().catch((e) => {
        ready = null;
        throw e;
    });
    return ready;
}

/** The photo cut to a circle (or a rounded square) as a PNG, ready for the PDF. ring: a light border (modern). */
async function photoData(url, shape, ring) {
    if (!url) return null;
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.src = url;
    try {
        await img.decode();
    } catch (e) {
        return null;
    }
    const S = 360;
    const c = document.createElement('canvas');
    c.width = c.height = S;
    const g = c.getContext('2d');
    const side = Math.min(img.naturalWidth, img.naturalHeight);
    g.save();
    g.beginPath();
    if (shape === 'circle') g.arc(S / 2, S / 2, S / 2, 0, Math.PI * 2);
    else g.roundRect(0, 0, S, S, S * 0.09);
    g.clip();
    g.drawImage(img, (img.naturalWidth - side) / 2, (img.naturalHeight - side) / 2, side, side, 0, 0, S, S);
    g.restore();
    if (ring) {
        g.lineWidth = S * 0.052;
        g.strokeStyle = 'rgba(255,255,255,.35)';
        g.beginPath();
        g.arc(S / 2, S / 2, S / 2 - g.lineWidth / 2, 0, Math.PI * 2);
        g.stroke();
    }
    return c.toDataURL('image/png');
}

function svgIcon(kind, color) {
    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${ICONS[kind] || ICONS.link}</svg>`;
}

function rich(text, accent, color = INK, size = P(12.5)) {
    return blocks(text).map((b, i) => (b.ul
        ? {ul: b.ul.map((li) => ({text: li})), markerColor: accent, color, fontSize: size, margin: [0, i ? 3 : 0, 0, 0]}
        : {text: b.p, color, fontSize: size, margin: [0, i ? 3 : 0, 0, 0]}));
}

function hline(width, color = LINE, lineWidth = 0.75) {
    return {canvas: [{type: 'line', x1: 0, y1: 0, x2: width, y2: 0, lineWidth, lineColor: color}]};
}

function dots(level, on, off) {
    return {
        canvas: [1, 2, 3, 4, 5].map((n) => ({type: 'ellipse', x: P(3.5) + (n - 1) * P(10), y: P(4), r1: P(3.5), r2: P(3.5), color: n <= level ? on : off})),
        width: P(50), margin: [0, P(2), 0, 0],
    };
}

/** One contact as a row with its icon. */
function contactRow(c, color, iconColor, size = P(12)) {
    const text = c.label ? [{text: c.label + ': ', color: color === '#FFFFFF' ? '#FFFFFF' : SOFT}, c.text] : c.text;
    return {
        columns: [{svg: svgIcon(c.kind, iconColor), width: P(13), height: P(13), margin: [0, P(1.5), 0, 0]}, {text, color, fontSize: size, link: c.href || undefined}],
        columnGap: P(6),
        margin: [0, 0, 0, P(5)],
    };
}

function contactsGrid(m, cols, color, iconColor) {
    if (!m.contacts.length) return null;
    const rows = [];
    for (let i = 0; i < m.contacts.length; i += cols) {
        rows.push({columns: m.contacts.slice(i, i + cols).map((c) => contactRow(c, color, iconColor)).concat(Array(Math.max(0, cols - (m.contacts.length - i))).fill({text: ''})), columnGap: P(16)});
    }
    return {stack: rows};
}

function fieldsPdf(m, opts = {}) {
    if (!m.fields.length) return null;
    if (opts.vertical) {
        return {stack: m.fields.map((f) => ({stack: [{text: f.label, bold: true, color: opts.color}, {text: f.value, color: opts.soft}], margin: [0, 0, 0, P(6)], fontSize: P(12)}))};
    }
    return {
        text: m.fields.flatMap((f, i) => [i ? {text: '     '} : '', {text: f.label + ' ', bold: true}, {text: f.value, color: SOFT}]),
        fontSize: P(12), margin: [0, P(12), 0, 0], alignment: opts.center ? 'center' : 'left',
    };
}

// ---------------------------------------------------------------- sections

let measureCtx = null;

/** Width in pt of a line of text, measured with the same font files the PDF embeds (loaded by cv.css). */
function textWidth(text, size, bold, serif, spacing = 0) {
    measureCtx ||= document.createElement('canvas').getContext('2d');
    measureCtx.font = `${bold ? 600 : 400} ${size}px ${serif ? '"CV Serif", Georgia, serif' : '"CV Sans", Roboto, sans-serif'}`;
    return measureCtx.measureText(text).width + spacing * text.length + 2;
}

function heading(m, title, style, width) {
    const t = title.toUpperCase();
    const serif = m.font === 'serif';
    switch (style) {
        case 'classic':
            return {stack: [{text: t, color: m.accent, bold: true, fontSize: P(13), characterSpacing: P(1)}, {...hline(width), margin: [0, P(4), 0, 0]}], margin: [0, P(20), 0, P(8)]};
        case 'modern':
        {
            const tw = Math.min(width, textWidth(t, P(13), true, serif, P(1)));
            return {columns: [{text: t, color: m.accent, bold: true, fontSize: P(13), characterSpacing: P(1), width: tw}, {...hline(Math.max(0, width - tw - P(10))), width: '*', margin: [0, P(8), 0, 0]}], columnGap: P(10), margin: [0, P(20), 0, P(8)]};
        }
        case 'side':
            return {stack: [{text: t, color: '#FFFFFF', bold: true, fontSize: P(13), characterSpacing: P(1)}, {...hline(width, mix(m.accent, '#FFFFFF', 0.4), 0.75), margin: [0, P(4), 0, 0]}], margin: [0, P(20), 0, P(8)]};
        case 'minimal':
            return {text: t, color: INK, bold: true, fontSize: P(11.5), characterSpacing: P(1.8), margin: [0, P(26), 0, P(8)]};
        default: // elegant
        {
            const tw = Math.min(width, textWidth(t, P(12), false, serif, P(2.6)));
            const side = Math.max(0, (width - tw - P(28)) / 2);
            return {
                columns: [{...hline(side), width: side, margin: [0, P(8), 0, 0]}, {text: t, color: m.accent, fontSize: P(12), characterSpacing: P(2.6), width: tw, alignment: 'center'}, {...hline(side), width: side, margin: [0, P(8), 0, 0]}],
                columnGap: P(14), margin: [0, P(20), 0, P(12)],
            };
        }
    }
}

function hasText(i) {
    return Object.values(i).some((v) => typeof v === 'string' && v.trim());
}

function itemPdf(m, sec, i, style) {
    const isList = sec.type !== 'timeline';
    const when = isList ? i.date : i.when;
    const title = {text: isList ? i.title : i.title, bold: true, fontSize: P(14), color: INK, link: isList && i.url ? i.url : undefined};
    const subParts = isList ? [i.subtitle] : [i.org, i.place];
    const sub = subParts.filter(Boolean).length
        ? {text: isList ? i.subtitle : [i.org || '', i.org && i.place ? ' · ' : '', i.place ? {text: i.place, italics: true} : ''], color: SOFT, fontSize: P(12.5)}
        : null;
    const text = i.text ? {stack: rich(i.text, m.accent), margin: [0, P(4), 0, 0]} : null;
    const whenTxt = when ? {text: when, color: SOFT, fontSize: P(11.5)} : null;
    let node;
    if (style === 'modern') {
        node = {stack: [title, whenTxt, sub, text].filter(Boolean)};
    } else if (style === 'minimal') {
        node = {columns: [{stack: [title, sub, text].filter(Boolean), width: '*'}, whenTxt ? {...whenTxt, width: 'auto', alignment: 'right', margin: [0, P(2), 0, 0]} : {text: '', width: 0}], columnGap: P(14)};
    } else if (!whenTxt && isList) {
        node = {stack: [title, sub, text].filter(Boolean)};
    } else {
        node = {columns: [{...(whenTxt || {text: ''}), width: P(118), margin: [0, P(2), 0, 0]}, {stack: [title, sub, text].filter(Boolean), width: '*'}], columnGap: P(14)};
    }
    const short = String(i.text || '').length < 700;
    return {...node, margin: [0, P(6), 0, P(6)], unbreakable: short};
}

function tagsPdf(m, sec, cols, onSide) {
    const color = onSide ? '#FFFFFF' : INK;
    const soft = onSide ? '#E8EAF6' : SOFT;
    const on = onSide ? '#FFFFFF' : m.template === 'minimal' ? INK : m.accent;
    const off = onSide ? mix(m.accent, '#FFFFFF', 0.3) : LINE;
    const cells = sec.items.filter((i) => i.name).map((i) => ({
        columns: [
            {text: i.name, color, fontSize: P(13), width: '*'},
            sec.type === 'skills' ? (i.level ? dots(i.level, on, off) : {text: '', width: 0}) : {text: i.level || '', color: soft, fontSize: P(12), width: 'auto', alignment: 'right'},
        ],
        columnGap: P(10),
        margin: [0, 0, 0, P(5)],
    }));
    // pdfmake has no alpha colours: the faded white of the sidebar is a light tint of the accent
    if (cols === 1) return {stack: cells};
    const rows = [];
    for (let i = 0; i < cells.length; i += cols) rows.push({columns: [...cells.slice(i, i + cols), ...Array(Math.max(0, cols - (cells.length - i))).fill({text: ''})], columnGap: P(24)});
    return {stack: rows};
}

function mix(a, b, t) {
    const p = (h) => [1, 3, 5].map((i) => parseInt(h.slice(i, i + 2), 16));
    const [x, y] = [p(a), p(b)];
    return '#' + x.map((v, i) => Math.round(v + (y[i] - v) * t).toString(16).padStart(2, '0')).join('');
}

function sectionPdf(m, sec, style, width, onSide = false) {
    const out = [heading(m, sec.title, onSide ? 'side' : style, width)];
    if (sec.type === 'text') out.push({stack: rich(sec.text, m.accent, onSide ? '#FFFFFF' : INK, P(13))});
    else if (sec.type === 'skills' || sec.type === 'langs') out.push(tagsPdf(m, sec, onSide ? 1 : 2, onSide));
    else for (const i of sec.items.filter(hasText)) out.push(itemPdf(m, sec, i, style));
    // keep the title with its first item
    return {stack: [{stack: out.slice(0, 2), unbreakable: true}, ...out.slice(2)]};
}

function summaryPdf(m, titled, style, width, opts = {}) {
    if (!m.summary) return null;
    return {
        stack: [titled ? heading(m, m.summaryTitle, style, width) : null, {stack: rich(m.summary, m.accent, opts.color || INK, P(opts.size || 13)), alignment: opts.center ? 'center' : 'left', italics: !!opts.italics, margin: [0, titled ? 0 : P(opts.top ?? 16), 0, 0]}].filter(Boolean),
    };
}

function footerPdf(m, width, center) {
    if (!m.footer) return null;
    return {stack: [hline(width), {text: m.footer, fontSize: P(10), color: SOFT, margin: [0, P(10), 0, 0], alignment: center ? 'center' : 'left'}], margin: [0, P(28), 0, 0], unbreakable: true};
}

function nameBlock(m, opts = {}) {
    return [
        {text: opts.upper ? m.name.toUpperCase() : m.name, fontSize: P(opts.size || 30), bold: opts.bold !== false, color: opts.color || INK, characterSpacing: opts.spacing || 0, alignment: opts.center ? 'center' : 'left'},
        m.headline ? {text: m.headline, fontSize: P(15), color: opts.headColor || m.accent, bold: opts.headBold !== false, italics: !!opts.headItalic, margin: [0, P(4), 0, 0], alignment: opts.center ? 'center' : 'left'} : null,
    ].filter(Boolean);
}

// ---------------------------------------------------------------- templates

const BUILD = {
    classic(m, photo) {
        const pad = P(48);
        const W = PAGE_W - pad * 2;
        const head = {stack: [...nameBlock(m), m.contacts.length ? {...contactsGrid(m, 2, SOFT, m.accent), margin: [0, P(10), 0, 0]} : null].filter(Boolean), width: '*'};
        return {
            pageMargins: [pad, pad, pad, pad],
            content: [
                {columns: [photo ? {image: photo, width: P(104), height: P(104)} : null, head].filter(Boolean), columnGap: P(22)},
                {...hline(W, m.accent, P(3)), margin: [0, P(16), 0, 0]},
                fieldsPdf(m), summaryPdf(m, true, 'classic', W),
                ...m.sections.map((s) => sectionPdf(m, s, 'classic', W)),
                footerPdf(m, W),
            ].filter(Boolean),
        };
    },
    modern(m, photo) {
        const SIDE = P(250);
        const sidePad = P(26);
        const mainPad = P(38);
        const SW = SIDE - sidePad * 2;
        const MW = PAGE_W - SIDE - mainPad * 2;
        const side = [
            photo ? {image: photo, width: P(150), height: P(150), alignment: 'center', margin: [0, 0, 0, P(6)]} : null,
            m.contacts.length ? {stack: [heading(m, m.contactsTitle, 'side', SW), ...m.contacts.map((c) => contactRow(c, '#FFFFFF', '#FFFFFF'))]} : null,
            m.fields.length ? {...fieldsPdf(m, {vertical: true, color: '#FFFFFF', soft: '#E8EAF6'}), margin: [0, P(20), 0, 0]} : null,
            ...m.side.map((s) => sectionPdf(m, s, 'modern', SW, true)),
        ].filter(Boolean);
        const main = [
            ...nameBlock(m, {size: 32}),
            summaryPdf(m, false, 'modern', MW, {color: SOFT, top: 14}),
            ...m.main.map((s) => sectionPdf(m, s, 'modern', MW)),
            footerPdf(m, MW),
        ].filter(Boolean);
        return {
            pageMargins: [0, P(40), 0, P(40)],
            background: () => ({canvas: [{type: 'rect', x: 0, y: 0, w: SIDE, h: PAGE_H, color: m.accent}]}),
            // in pdfmake a column's width includes its margins
            content: [{columns: [{stack: side, width: SIDE, margin: [sidePad, 0, sidePad, 0]}, {stack: main, width: '*', margin: [mainPad, 0, mainPad, 0]}], columnGap: 0}],
        };
    },
    minimal(m, photo) {
        const pad = P(56);
        const W = PAGE_W - pad * 2;
        return {
            pageMargins: [pad, pad, pad, pad],
            content: [
                {columns: [{stack: nameBlock(m, {size: 34, bold: false, color: m.accent, headColor: SOFT, headBold: false}), width: '*'}, photo ? {image: photo, width: P(92), height: P(92)} : null].filter(Boolean), columnGap: P(20)},
                m.contacts.length ? {...contactsGrid(m, 2, SOFT, SOFT), margin: [0, P(16), 0, 0]} : null,
                fieldsPdf(m), summaryPdf(m, false, 'minimal', W, {size: 13.5, top: 22}),
                ...m.sections.map((s) => sectionPdf(m, s, 'minimal', W)),
                footerPdf(m, W),
            ].filter(Boolean),
        };
    },
    elegant(m, photo) {
        const pad = P(52);
        const W = PAGE_W - pad * 2;
        const contacts = m.contacts.length
            ? {text: m.contacts.flatMap((c, i) => [i ? {text: '   ·   ', color: m.accent} : '', c.label ? {text: c.label + ': ', color: SOFT} : '', {text: c.text, color: SOFT, link: c.href || undefined}]), fontSize: P(12), alignment: 'center', margin: [0, P(10), 0, 0]}
            : null;
        return {
            pageMargins: [pad, pad, pad, pad],
            content: [
                photo ? {image: photo, width: P(96), height: P(96), alignment: 'center', margin: [0, 0, 0, P(10)]} : null,
                ...nameBlock(m, {size: 32, bold: false, upper: true, spacing: P(1.9), center: true, headColor: SOFT, headBold: false, headItalic: true}),
                contacts,
                fieldsPdf(m, {center: true}),
                {text: '', margin: [0, P(14), 0, 0]},
                summaryPdf(m, true, 'elegant', W, {color: SOFT, center: true, italics: true}),
                ...m.sections.map((s) => sectionPdf(m, s, 'elegant', W)),
                footerPdf(m, W, true),
            ].filter(Boolean),
        };
    },
};

/** Builds the PDF of one language and downloads it. base: URL of the app folder (e.g. "./"). */
export async function downloadPdf(m, base, fileName) {
    const doc = await createPdf(m, base);
    await new Promise((ok) => doc.download(fileName, ok));
}

/** The pdfmake document of one language (download it, or getBuffer() to read it). */
export async function createPdf(m, base) {
    const pdfMake = await loadPdfMake(base);
    const shape = m.template === 'minimal' ? 'square' : 'circle';
    const photo = await photoData(m.photo, shape, m.template === 'modern');
    const def = BUILD[m.template] ? BUILD[m.template](m, photo) : BUILD.modern(m, photo);
    def.pageSize = 'A4';
    def.defaultStyle = {font: m.font === 'serif' ? 'Serif' : 'Sans', fontSize: P(13), lineHeight: 1.18, color: INK};
    def.info = {title: m.name ? m.name + ' — CV' : 'CV', author: m.name || '', creator: 'MyCurriculum', producer: 'MyCurriculum'};
    def.language = m.lang;
    return pdfMake.createPdf(def);
}

export function pdfFileName(m) {
    const slug = (m.name || 'cv').normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^A-Za-z0-9]+/g, '_').replace(/^_|_$/g, '');
    return `CV_${slug || 'MyCurriculum'}_${m.lang.toUpperCase()}.pdf`;
}
