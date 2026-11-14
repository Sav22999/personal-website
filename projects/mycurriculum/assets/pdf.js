/**
 * MyCurriculum — the PDF of a CV, made in the browser with pdfmake (real text: it can be selected and searched).
 *
 * Each template here mirrors its HTML twin in cv.css, from the same model (cv.js buildModel). Measures are written
 * in CSS pixels and converted with P(): an A4 page is 794 px wide in the preview and 595 pt in the PDF.
 */

import {ICONS, blocks, FONTS, langDots} from './cv.js';

const P = (px) => px * 0.75;
const INK = '#1E2230';
const SOFT = '#5B6175';
const LINE = '#E3E5EC';
const PAGE_W = 595.28;
const PAGE_H = 841.89;

let ready = null;
const fontLoads = {};

/** Loads pdfmake once. base: URL of the app folder. */
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
        window.pdfMake.vfs = {};
        window.pdfMake.fonts = {};
        return window.pdfMake;
    })().catch((e) => {
        ready = null;
        throw e;
    });
    return ready;
}

/** Loads the four files of one typeface (only the one the CV uses) into pdfmake. */
function loadFont(pdfMake, base, id) {
    const font = FONTS[id] || FONTS.sans;
    fontLoads[id] ||= Promise.all(Object.values(font.files).map(async (f) => {
        if (pdfMake.vfs[f]) return;
        const buf = await (await fetch(base + 'fonts/' + f)).arrayBuffer();
        let bin = '';
        const bytes = new Uint8Array(buf);
        for (let i = 0; i < bytes.length; i += 0x8000) bin += String.fromCharCode.apply(null, bytes.subarray(i, i + 0x8000));
        pdfMake.vfs[f] = btoa(bin);
    })).then(() => {
        pdfMake.fonts[id] = font.files;
    }).catch((e) => {
        delete fontLoads[id];
        throw e;
    });
    return fontLoads[id];
}

/** The photo cut to a circle (or a rounded square) as a PNG, ready for the PDF. ring: a light border (modern). */
async function photoData(url, shape, ring, ringAlpha = 0.35) {
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
        g.strokeStyle = `rgba(255,255,255,${ringAlpha})`;
        g.beginPath();
        g.arc(S / 2, S / 2, S / 2 - g.lineWidth / 2, 0, Math.PI * 2);
        g.stroke();
    }
    return c.toDataURL('image/png');
}

/**
 * Contacts on lines no wider than width, joined by sep only between contacts of the same line: a separator never
 * starts a line (pdfmake would wrap one long text anywhere). Widths are measured with the typeface of the CV.
 */
function contactLines(m, width, {sep, sepColor, color, labelColor, labelBold = false, size, align = 'left', gap = P(3)}) {
    const family = (FONTS[m.font] || FONTS.sans).css;
    const sepW = textWidth(sep, size, false, family);
    const lines = [];
    let line = [];
    let used = 0;
    for (const c of m.contacts) {
        const w = textWidth((c.label ? c.label + ': ' : '') + c.text, size, labelBold, family);
        if (line.length && used + sepW + w > width) {
            lines.push(line);
            line = [];
            used = 0;
        }
        used += (line.length ? sepW : 0) + w;
        line.push(c);
    }
    if (line.length) lines.push(line);
    return {stack: lines.map((l) => ({
        text: l.flatMap((c, i) => [i ? {text: sep, color: sepColor} : '', c.label ? {text: c.label + ': ', color: labelColor, bold: labelBold} : '', {text: c.text, link: c.href || undefined}]),
        color, fontSize: size, alignment: align, margin: [0, 0, 0, gap],
    }))};
}

/** Monogram: the initials in a circle of the accent, drawn with the typeface of the CV, as a PNG. */
function initialsData(text, accent, family) {
    const S = 360;
    const c = document.createElement('canvas');
    c.width = c.height = S;
    const g = c.getContext('2d');
    g.fillStyle = accent;
    g.beginPath();
    g.arc(S / 2, S / 2, S / 2, 0, Math.PI * 2);
    g.fill();
    g.fillStyle = '#FFFFFF';
    g.font = `600 ${Math.round(S * (text.length > 2 ? 0.3 : 0.38))}px ${family}, sans-serif`;
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.fillText(text, S / 2, S / 2 + S * 0.02);
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
function textWidth(text, size, bold, family, spacing = 0) {
    measureCtx ||= document.createElement('canvas').getContext('2d');
    measureCtx.font = `${bold ? 600 : 400} ${size}px ${family}, sans-serif`;
    return measureCtx.measureText(text).width + spacing * text.length + 2;
}

function heading(m, title, style, width) {
    const t = title.toUpperCase();
    const serif = (FONTS[m.font] || FONTS.sans).css; // the CSS family of the typeface, to measure with it
    switch (style) {
        case 'classic':
            return {stack: [{text: t, color: m.accent, bold: true, fontSize: P(13), characterSpacing: P(1)}, {...hline(width), margin: [0, P(4), 0, 0]}], margin: [0, P(22), 0, P(8)]};
        case 'modern':
        {
            const tw = Math.min(width, textWidth(t, P(13), true, serif, P(1)));
            return {columns: [{text: t, color: m.accent, bold: true, fontSize: P(13), characterSpacing: P(1), width: tw}, {...hline(Math.max(0, width - tw - P(10))), width: '*', margin: [0, P(8), 0, 0]}], columnGap: P(10), margin: [0, P(22), 0, P(8)]};
        }
        case 'side':
            return {stack: [{text: t, color: '#FFFFFF', bold: true, fontSize: P(13), characterSpacing: P(1)}, {...hline(width, mix(m.accent, '#FFFFFF', 0.4), 0.75), margin: [0, P(4), 0, 0]}], margin: [0, P(22), 0, P(8)]};
        case 'minimal':
            return {text: t, color: INK, bold: true, fontSize: P(11.5), characterSpacing: P(1.8), margin: [0, P(26), 0, P(8)]};
        case 'banner':
            return {stack: [
                {columns: [{canvas: [{type: 'ellipse', x: P(3.5), y: P(8), r1: P(3.5), r2: P(3.5), color: m.accent}], width: P(16)}, {text: t, color: INK, bold: true, fontSize: P(14), characterSpacing: P(0.3)}], columnGap: 0},
                {...hline(width, m.accent, 1.1), margin: [0, P(3), 0, 0]},
            ], margin: [0, P(22), 0, P(8)]};
        case 'timeline':
            return {text: '  ' + t + '  ', color: '#FFFFFF', background: m.accent, bold: true, fontSize: P(11.5), characterSpacing: P(1.1), margin: [0, P(22), 0, P(10)]};
        case 'compact':
            return {stack: [{text: t, color: INK, bold: true, fontSize: P(11.5), characterSpacing: P(0.9)}, {...hline(width, m.accent, 1.5), margin: [0, P(3), 0, 0]}], margin: [0, P(14), 0, P(6)]};
        case 'studio':
            return {text: t, color: INK, bold: true, fontSize: P(13), characterSpacing: P(1), margin: [0, P(22), 0, P(8)]};
        case 'monogram':
            return {columns: [{canvas: [{type: 'rect', x: 0, y: 0, w: P(4), h: P(15), color: m.accent}], width: P(13)}, {text: t, color: INK, bold: true, fontSize: P(13), characterSpacing: P(1)}], columnGap: 0, margin: [0, P(22), 0, P(8)]};
        case 'contrast':
            return {stack: [{text: t, color: INK, bold: true, fontSize: P(15), characterSpacing: P(0.6)}, {canvas: [{type: 'rect', x: 0, y: 0, w: P(36), h: P(3), r: P(1.5), color: m.accent}], margin: [0, P(5), 0, 0]}], margin: [0, P(22), 0, P(8)]};
        case 'swiss':
            return {text: t, color: m.accent, bold: true, fontSize: P(11), characterSpacing: P(1.5), margin: [0, P(2), 0, 0]};
        default: // elegant
        {
            const tw = Math.min(width, textWidth(t, P(12), false, serif, P(2.6)));
            const side = Math.max(0, (width - tw - P(28)) / 2);
            return {
                columns: [{...hline(side), width: side, margin: [0, P(8), 0, 0]}, {text: t, color: m.accent, fontSize: P(12), characterSpacing: P(2.6), width: tw, alignment: 'center'}, {...hline(side), width: side, margin: [0, P(8), 0, 0]}],
                columnGap: P(14), margin: [0, P(22), 0, P(12)],
            };
        }
    }
}

function hasText(i) {
    return Object.values(i).some((v) => typeof v === 'string' && v.trim());
}

/**
 * An entry is never split between two pages (pdfmake moves it whole to the next one), unless it is so long that it
 * could not fit on a page anyway: about 3000 characters of description fill an A4 page.
 */
function fitsOnePage(i) {
    return String(i.text || '').length < 3000;
}

/** first: the first entry of its section (no space above: the heading already has it). */
function itemPdf(m, sec, i, style, first = false) {
    const isList = sec.type !== 'timeline';
    const when = isList ? i.date : i.when;
    const title = {text: i.title, bold: true, fontSize: P(14), color: INK, link: i.url || undefined}; // the title is a link when there is one
    const subParts = isList ? [i.subtitle] : [i.org, i.place];
    const sub = subParts.filter(Boolean).length
        ? {text: isList ? i.subtitle : [i.org || '', i.org && i.place ? ' · ' : '', i.place ? {text: i.place, italics: true} : ''], color: SOFT, fontSize: P(12.5)}
        : null;
    const small = style === 'compact';
    const text = i.text ? {stack: rich(i.text, m.accent, INK, P(small ? 11.5 : 12.5)), margin: [0, P(4), 0, 0]} : null;
    const accentWhen = ['timeline', 'studio', 'contrast'].includes(style); // these templates print the dates in the accent
    const whenTxt = when ? {text: when, color: accentWhen ? m.accent : SOFT, bold: accentWhen, fontSize: P(small ? 10.5 : 11.5)} : null;
    // EQF level and grade (education), link (projects)
    const meta = !isList && i.meta?.length ? {text: i.meta.flatMap((x, n) => [n ? {text: '     '} : '', {text: x, bold: n === 0}]), color: SOFT, fontSize: P(11.5), margin: [0, P(3), 0, 0]} : null;
    // the address written out under the entry (the title is a link too, but a PDF reader may block links)
    const link = i.url ? {columns: [{svg: svgIcon('link', m.accent), width: P(12), height: P(12), margin: [0, P(1), 0, 0]}, {text: i.urlText, link: i.url, color: m.accent, fontSize: P(11.5)}], columnGap: P(5), margin: [0, P(3), 0, 0]} : null;
    if (small) {
        title.fontSize = P(12.5);
        if (sub) sub.fontSize = P(11.5);
    }
    let node;
    if (style === 'banner') {
        const up = (x) => String(x || '').toUpperCase();
        const head = {text: [
            {text: up(isList ? i.title : i.title), bold: true, color: '#444A5C', link: title.link},
            ...(isList ? (i.subtitle ? [' – ', {text: up(i.subtitle), bold: true, color: '#444A5C'}] : []) : (i.org ? [' – ', {text: up(i.org), bold: true, color: '#444A5C'}] : [])),
            ...(when ? [{text: ' – ' + when, color: SOFT, fontSize: P(12)}] : []),
            ...(!isList && i.place ? [{text: ' · ' + i.place, color: SOFT, fontSize: P(12)}] : []),
        ], fontSize: P(12.5)};
        const body = text ? {stack: [hline(PAGE_W - P(88) - P(16), LINE), {...text, margin: [0, P(4), 0, 0]}], margin: [0, P(4), 0, 0]} : null;
        node = {stack: [head, body, meta, link].filter(Boolean), margin: [P(16), 0, 0, 0]};
    } else if (style === 'timeline') {
        // The line is the background of a thin first column: backgrounds are painted before the content, so the dot
        // (drawn with the content of the second column) sits on top of it. A table border would be drawn over the dot.
        // The ring is two filled circles (accent, then white): a thick stroked outline looks uneven in some PDF readers.
        const lineW = P(2);
        const gap = P(20); // from the line to the text
        const dot = {canvas: [
            {type: 'ellipse', x: 0, y: 0, r1: P(6), r2: P(6), color: m.accent},
            {type: 'ellipse', x: 0, y: 0, r1: P(3), r2: P(3), color: '#FFFFFF'},
        ], relativePosition: {x: -gap - lineW / 2, y: P(6)}}; // centred on the line
        const inner = {stack: [dot, whenTxt, title, sub, text, meta, link].filter(Boolean)};
        const short = fitsOnePage(i);
        return {
            table: {widths: [lineW, '*'], body: [[{text: '', fillColor: LINE}, inner]]},
            layout: {hLineWidth: () => 0, vLineWidth: () => 0, paddingLeft: (col) => (col === 1 ? gap : 0), paddingRight: () => 0,
                paddingTop: (row) => P(2), paddingBottom: () => P(12)},
            margin: [P(5), 0, 0, 0], unbreakable: short,
        };
    } else if (['modern', 'compact', 'studio', 'swiss'].includes(style)) {
        node = {stack: [title, whenTxt, sub, text, meta, link].filter(Boolean)};
    } else if (style === 'minimal' || style === 'contrast') {
        node = {columns: [{stack: [title, sub, text, meta, link].filter(Boolean), width: '*'}, whenTxt ? {...whenTxt, width: 'auto', alignment: 'right', margin: [0, P(2), 0, 0]} : {text: '', width: 0}], columnGap: P(14)};
    } else if (!whenTxt && isList) {
        node = {stack: [title, sub, text, link].filter(Boolean)};
    } else {
        node = {columns: [{...(whenTxt || {text: ''}), width: P(118), margin: [0, P(2), 0, 0]}, {stack: [title, sub, text, meta, link].filter(Boolean), width: '*'}], columnGap: P(14)};
    }
    const short = fitsOnePage(i);
    // entries of one section are 12 px apart (9 in compact): space between them, none around
    return {...node, margin: [node.margin?.[0] || 0, first ? 0 : P(small ? 9 : 12), 0, 0], unbreakable: short};
}

function tagsPdf(m, sec, cols, onSide) {
    const color = onSide ? '#FFFFFF' : INK;
    const soft = onSide ? '#E8EAF6' : SOFT;
    const on = onSide ? '#FFFFFF' : m.template === 'minimal' ? INK : m.accent;
    const off = onSide ? mix(m.accent, '#FFFFFF', 0.3) : LINE;
    const cells = sec.items.filter((i) => i.name).map((i) => ({
        columns: [
            {text: i.name, color, fontSize: P(13), width: '*'},
            ...(sec.type === 'skills'
                ? [i.level ? dots(i.level, on, off) : {text: '', width: 0}]
                // languages: the written level, then the dots (from the level, or chosen by hand)
                : [{text: i.level || '', color: soft, fontSize: P(12), width: 'auto', alignment: 'right'}, langDots(i) ? dots(langDots(i), on, off) : {text: '', width: 0}]),
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
    const indent = style === 'banner' ? [P(16), 0, 0, 0] : [0, 0, 0, 0]; // banner: text under the dot of the heading
    if (sec.type === 'text') out.push({stack: rich(sec.text, m.accent, onSide ? '#FFFFFF' : INK, P(style === 'compact' ? 11.5 : 13)), margin: indent});
    else if (sec.type === 'skills' || sec.type === 'langs') out.push({...tagsPdf(m, sec, onSide || style === 'compact' || style === 'studio' ? 1 : 2, onSide), margin: indent});
    else sec.items.filter(hasText).forEach((i, n) => out.push(itemPdf(m, sec, i, style, n === 0)));
    if (style === 'swiss') return swissRow(width, out[0], out.slice(1));
    // keep the title with its first item
    return {stack: [{stack: out.slice(0, 2), unbreakable: true}, ...out.slice(2)]};
}

/** Swiss: a fine rule, then the title in a 150 px column and the content beside it. */
function swissRow(width, title, body) {
    const short = JSON.stringify(body).length < 6000; // a short section never splits between pages
    return {stack: [hline(width, LINE), {columns: [{stack: [title], width: P(150)}, {stack: body, width: '*'}], columnGap: P(24), margin: [0, P(14), 0, 0]}], margin: [0, P(22), 0, 0], unbreakable: short};
}

function summaryPdf(m, titled, style, width, opts = {}) {
    if (!m.summary) return null;
    if (style === 'swiss') return swissRow(width, heading(m, m.summaryTitle, 'swiss', width), [{stack: rich(m.summary, m.accent, INK, P(13))}]);
    return {
        stack: [titled ? heading(m, m.summaryTitle, style, width) : null, {stack: rich(m.summary, m.accent, opts.color || INK, P(opts.size || 13)), alignment: opts.center ? 'center' : 'left', italics: !!opts.italics, margin: [style === 'banner' ? P(16) : 0, titled ? 0 : P(opts.top ?? 16), 0, 0]}].filter(Boolean),
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
            ? {...contactLines(m, W, {sep: '   ·   ', sepColor: m.accent, color: SOFT, labelColor: SOFT, size: P(12), align: 'center'}), margin: [0, P(10), 0, 0]}
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
    banner(m, photo) {
        const pad = P(44);
        const W = PAGE_W - pad * 2;
        const white = '#FFFFFF';
        const contacts = m.contacts.length
            ? {...contactLines(m, W - (photo ? P(140) : 0), {sep: '   |   ', sepColor: mix(m.accent, white, 0.5), color: white, labelBold: true, size: P(12), gap: P(5)}), margin: [0, P(10), 0, 0]}
            : null;
        const headText = {stack: [
            {text: m.name, fontSize: P(28), bold: true, color: white},
            {...hline(W - (photo ? P(140) : 0), mix(m.accent, white, 0.55), 0.75), margin: [0, P(8), 0, 0]},
            m.headline ? {text: m.headline, fontSize: P(15), color: white, margin: [0, P(6), 0, 0]} : null,
            contacts,
        ].filter(Boolean), width: '*'};
        // the band is a full-width table cell: negative margins take it to the edges of the page
        const band = {
            table: {widths: ['*'], body: [[{columns: [photo ? {image: photo, width: P(116), height: P(116)} : null, headText].filter(Boolean), columnGap: P(24), fillColor: m.accent}]]},
            layout: {hLineWidth: () => 0, vLineWidth: () => 0, paddingLeft: () => pad, paddingRight: () => pad, paddingTop: () => P(30), paddingBottom: () => P(30)},
            margin: [-pad, -P(36), -pad, P(6)],
        };
        return {
            pageMargins: [pad, P(36), pad, P(36)],
            content: [
                band,
                m.fields.length ? {...fieldsPdf(m), margin: [P(16), P(16), 0, 0]} : null,
                summaryPdf(m, true, 'banner', W),
                ...m.sections.map((s) => sectionPdf(m, s, 'banner', W)),
                footerPdf(m, W),
            ].filter(Boolean),
        };
    },
    timeline(m, photo) {
        const pad = P(48);
        const W = PAGE_W - pad * 2;
        const head = {stack: [...nameBlock(m, {size: 32}), m.contacts.length ? {...contactsGrid(m, 2, SOFT, m.accent), margin: [0, P(12), 0, 0]} : null].filter(Boolean), width: '*'};
        return {
            pageMargins: [pad, pad, pad, pad],
            content: [
                {columns: [head, photo ? {image: photo, width: P(104), height: P(104)} : null].filter(Boolean), columnGap: P(22)},
                fieldsPdf(m), summaryPdf(m, true, 'timeline', W),
                ...m.sections.map((s) => sectionPdf(m, s, 'timeline', W)),
                footerPdf(m, W),
            ].filter(Boolean),
        };
    },
    studio(m, photo) {
        const pad = P(48);
        const W = PAGE_W - pad * 2;
        const SIDE = P(228);
        const GAP = P(28);
        const MW = W - SIDE - GAP;
        const IN = SIDE - P(36); // inside the tinted box
        const side = [
            m.contacts.length ? {stack: [heading(m, m.contactsTitle, 'studio', IN), ...m.contacts.map((c) => contactRow(c, SOFT, m.accent, P(11.5)))]} : null,
            m.fields.length ? {...fieldsPdf(m, {vertical: true, color: INK, soft: SOFT}), margin: [0, P(16), 0, 0]} : null,
            ...m.side.map((s) => sectionPdf(m, s, 'studio', IN)),
        ].filter(Boolean);
        const box = {table: {widths: ['*'], body: [[{stack: side, fillColor: mix(m.accent, '#FFFFFF', 0.92)}]]},
            layout: {hLineWidth: () => 0, vLineWidth: () => 0, paddingLeft: () => P(18), paddingRight: () => P(18), paddingTop: () => 0, paddingBottom: () => P(20)}};
        return {
            pageMargins: [pad, pad, pad, pad],
            content: [
                {columns: [photo ? {image: photo, width: P(92), height: P(92)} : null, {stack: nameBlock(m, {size: 32}), width: '*', margin: [0, photo ? P(18) : 0, 0, 0]}].filter(Boolean), columnGap: P(20)},
                {...hline(W, m.accent, P(3)), margin: [0, P(16), 0, 0]},
                {columns: [{stack: [summaryPdf(m, true, 'studio', MW), ...m.main.map((s) => sectionPdf(m, s, 'studio', MW))].filter(Boolean), width: MW},
                    {stack: [box], width: SIDE, margin: [0, P(22), 0, 0]}], columnGap: GAP},
                footerPdf(m, W),
            ].filter(Boolean),
        };
    },
    monogram(m, photo) {
        const pad = P(48);
        const W = PAGE_W - pad * 2;
        const badge = photo || initialsData(m.initials, m.accent, (FONTS[m.font] || FONTS.sans).css);
        return {
            pageMargins: [pad, pad, pad, pad],
            content: [
                {columns: [{image: badge, width: P(96), height: P(96)}, {stack: [...nameBlock(m), m.contacts.length ? {...contactsGrid(m, 2, SOFT, m.accent), margin: [0, P(10), 0, 0]} : null].filter(Boolean), width: '*', margin: [0, P(6), 0, 0]}], columnGap: P(22)},
                {...hline(W, LINE), margin: [0, P(18), 0, 0]},
                fieldsPdf(m), summaryPdf(m, true, 'monogram', W),
                ...m.sections.map((s) => sectionPdf(m, s, 'monogram', W)),
                footerPdf(m, W),
            ].filter(Boolean),
        };
    },
    contrast(m, photo) {
        const pad = P(48);
        const W = PAGE_W - pad * 2;
        const light = mix(m.accent, '#FFFFFF', 0.45);
        const contacts = m.contacts.length
            ? {text: m.contacts.flatMap((c, i) => [i ? {text: '    ', color: '#C9CCD8'} : '', c.label ? {text: c.label + ': ', color: light} : '', {text: c.text, link: c.href || undefined}]), color: '#C9CCD8', fontSize: P(12), lineHeight: 1.45, margin: [0, P(14), 0, 0]}
            : null;
        const headText = {stack: [
            {text: m.name, fontSize: P(36), bold: true, color: '#FFFFFF'},
            m.headline ? {text: m.headline, fontSize: P(15), color: light, margin: [0, P(4), 0, 0]} : null,
            contacts,
        ].filter(Boolean), width: '*'};
        const band = {
            table: {widths: ['*'], body: [[{columns: [headText, photo ? {image: photo, width: P(100), height: P(100)} : null].filter(Boolean), columnGap: P(24), fillColor: '#1E2230'}]]},
            layout: {hLineWidth: () => 0, vLineWidth: () => 0, paddingLeft: () => pad, paddingRight: () => pad, paddingTop: () => P(34), paddingBottom: () => P(30)},
            margin: [-pad, -pad, -pad, P(4)],
        };
        return {
            pageMargins: [pad, pad, pad, pad],
            content: [band, fieldsPdf(m), summaryPdf(m, false, 'contrast', W, {size: 13.5, top: 16}), ...m.sections.map((s) => sectionPdf(m, s, 'contrast', W)), footerPdf(m, W)].filter(Boolean),
        };
    },
    swiss(m, photo) {
        const pad = P(52);
        const W = PAGE_W - pad * 2;
        const contacts = m.contacts.length ? {stack: m.contacts.map((c) => contactRow(c, SOFT, m.accent, P(11.5))), width: 'auto'} : null;
        return {
            pageMargins: [pad, pad, pad, pad],
            content: [
                {columns: [{stack: [{text: m.name, fontSize: P(38), bold: true, color: INK, characterSpacing: -P(0.8)}, m.headline ? {text: m.headline, fontSize: P(15), color: SOFT, margin: [0, P(4), 0, 0]} : null].filter(Boolean), width: '*'},
                    contacts, photo ? {image: photo, width: P(76), height: P(76)} : null].filter(Boolean), columnGap: P(22)},
                {...hline(W, INK, P(2)), margin: [0, P(18), 0, 0]},
                fieldsPdf(m), summaryPdf(m, true, 'swiss', W),
                ...m.sections.map((s) => sectionPdf(m, s, 'swiss', W)),
                footerPdf(m, W),
            ].filter(Boolean),
        };
    },
    compact(m, photo) {
        const pad = P(36);
        const W = PAGE_W - pad * 2;
        const SIDE = P(210);
        const GAP = P(26);
        const MW = W - SIDE - GAP;
        const contacts = m.contacts.length ? {stack: m.contacts.map((c) => contactRow(c, SOFT, m.accent, P(11))), width: W * 0.42} : null;
        const side = [m.fields.length ? {...fieldsPdf(m, {vertical: true, color: INK, soft: SOFT}), margin: [0, P(14), 0, 0]} : null, ...m.side.map((s) => sectionPdf(m, s, 'compact', SIDE))].filter(Boolean);
        return {
            pageMargins: [pad, pad, pad, pad],
            defaultSize: P(11.5),
            content: [
                {columns: [photo ? {image: photo, width: P(72), height: P(72)} : null, {stack: nameBlock(m, {size: 25}), width: '*', margin: [0, photo ? P(12) : 0, 0, 0]}, contacts].filter(Boolean), columnGap: P(16)},
                {...hline(W, INK, P(2)), margin: [0, P(12), 0, 0]},
                summaryPdf(m, false, 'compact', W, {size: 11.5, top: 12}),
                {columns: [{stack: m.main.map((s) => sectionPdf(m, s, 'compact', MW)), width: MW}, {stack: side, width: SIDE}], columnGap: GAP},
                footerPdf(m, W),
            ].filter(Boolean),
        };
    },
};

/** Builds the PDF of one language and downloads it. base: URL of the app folder (e.g. "./"). */
export async function downloadPdf(m, base, fileName) {
    const doc = await createPdf(m, base);
    // Android app: a WebView cannot download, the app saves the file where the user chooses
    const native = window.MyCurriculumAndroid;
    if (native?.savePdf) {
        const b64 = await new Promise((ok) => doc.getBase64(ok));
        native.savePdf(fileName, b64);
        return;
    }
    await new Promise((ok) => doc.download(fileName, ok));
}

/** The pdfmake document of one language (download it, or getBuffer() to read it). */
export async function createPdf(m, base) {
    const pdfMake = await loadPdfMake(base);
    const fontId = FONTS[m.font] ? m.font : 'sans';
    await loadFont(pdfMake, base, fontId);
    // the browser must have the same font loaded to measure the headings (see textWidth)
    try {
        await document.fonts.load(`600 12px ${FONTS[fontId].css}`);
        await document.fonts.load(`400 12px ${FONTS[fontId].css}`);
    } catch (e) { /* measuring falls back to a similar font */ }
    const shape = ['minimal', 'compact', 'contrast', 'swiss'].includes(m.template) ? 'square' : 'circle';
    const photo = await photoData(m.photo, shape, m.template === 'modern' || m.template === 'banner', m.template === 'banner' ? 0.85 : 0.35);
    const def = BUILD[m.template] ? BUILD[m.template](m, photo) : BUILD.modern(m, photo);
    def.pageSize = 'A4';
    def.defaultStyle = {font: fontId, fontSize: def.defaultSize || P(13), lineHeight: 1.18, color: INK};
    delete def.defaultSize;
    def.info = {title: m.name ? m.name + ' — CV' : 'CV', author: m.name || '', creator: 'MyCurriculum', producer: 'MyCurriculum'};
    def.language = m.lang;
    if (m.pageNumbers) {
        // "Pagina 1 di 2": small, at the bottom right, inside the bottom margin of every page
        const right = m.template === 'modern' ? P(38) : (def.pageMargins?.[2] || P(48));
        def.footer = (current, total) => ({text: m.pageOf.replace('{x}', current).replace('{y}', total), alignment: 'right', fontSize: P(9.5), color: SOFT, margin: [0, P(12), right, 0]});
    }
    return pdfMake.createPdf(def);
}

export function pdfFileName(m) {
    const slug = (m.name || 'cv').normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^A-Za-z0-9]+/g, '_').replace(/^_|_$/g, '');
    return `CV_${slug || 'MyCurriculum'}_${m.lang.toUpperCase()}.pdf`;
}
