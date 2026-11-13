/**
 * MyCurriculum — the CV as something to show.
 *
 * buildModel() turns the stored document (settings + profile + one language of content) into what a template
 * prints, applying the "show" switches. renderCV() draws it as HTML: the same model feeds the PDF (pdf.js), so the
 * editor preview, the public page and the PDF always show the same things.
 */

import {CV_WORDS, SECTION_KINDS} from './i18n.js';

export const TEMPLATES = ['classic', 'modern', 'minimal', 'elegant'];
export const ACCENTS = ['#3D52D5', '#1F7A6D', '#B4443C', '#7A4FC4', '#C2770E', '#2B6CB0', '#22313F', '#B83B7D'];

/** Simple line icons (24×24, stroked): the same paths are used in the HTML and in the PDF. */
export const ICONS = {
    email: '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    phone: '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
    location: '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
    website: '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/>',
    birth: '<rect x="4" y="5" width="16" height="16" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/>',
    link: '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
};

export function emptyLang() {
    return {headline: '', location: '', summary: '', fields: [], sections: [], footer: ''};
}

export function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, (c) => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'})[c]);
}

// ---------------------------------------------------------------- dates

/** "2020-03", "03/2020", "3.2020" or "2020": a month or a year. Anything else is printed as written. */
export function parseDate(s) {
    s = String(s || '').trim();
    let m;
    if ((m = s.match(/^(\d{4})-(\d{1,2})$/))) return {y: +m[1], m: +m[2]};
    if ((m = s.match(/^(\d{1,2})[/.\-](\d{4})$/))) return {y: +m[2], m: +m[1]};
    if ((m = s.match(/^(\d{4})$/))) return {y: +m[1]};
    return null;
}

export function fmtDate(s, lang) {
    const d = parseDate(s);
    if (!d) return String(s || '').trim();
    if (!d.m || d.m < 1 || d.m > 12) return String(d.y);
    try {
        return new Intl.DateTimeFormat(lang, {month: 'short', year: 'numeric', timeZone: 'UTC'}).format(new Date(Date.UTC(d.y, d.m - 1, 1)));
    } catch (e) {
        return String(d.m).padStart(2, '0') + '/' + d.y;
    }
}

export function fmtRange(start, end, current, lang) {
    const a = fmtDate(start, lang);
    const b = current ? CV_WORDS[lang]?.present || 'present' : fmtDate(end, lang);
    if (a && b) return a === b ? a : a + ' – ' + b;
    return a || b;
}

function fmtBirth(iso, lang) {
    const m = String(iso || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (!m) return '';
    try {
        return new Intl.DateTimeFormat(lang, {day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC'}).format(new Date(Date.UTC(+m[1], +m[2] - 1, +m[3])));
    } catch (e) {
        return `${m[3]}/${m[2]}/${m[1]}`;
    }
}

function prettyUrl(u) {
    return String(u || '').replace(/^mailto:/i, '').replace(/^https?:\/\/(www\.)?/i, '').replace(/\/$/, '');
}

// ---------------------------------------------------------------- model

/** Paragraphs and bullet lists from free text ("- " or "• " at the start of a line makes a bullet). */
export function blocks(text) {
    const out = [];
    for (const raw of String(text || '').split('\n')) {
        const line = raw.trim();
        if (!line) {
            out.push({gap: true});
            continue;
        }
        const b = line.match(/^[-•*▪◦●–]\s+(.*)$/);
        if (b) {
            const last = out[out.length - 1];
            if (last && last.ul) last.ul.push(b[1]);
            else out.push({ul: [b[1]]});
        } else {
            const last = out[out.length - 1];
            if (last && last.p !== undefined) last.p += '\n' + line;
            else out.push({p: line});
        }
    }
    return out.filter((x) => !x.gap);
}

function sectionHasContent(s) {
    if (s.type === 'text') return !!String(s.text || '').trim();
    return (s.items || []).some((i) => Object.values(i).some((v) => typeof v === 'string' && v.trim()));
}

/**
 * Everything a template prints, for one language. photoUrl: the image to show (or null).
 * The switches of settings.show are applied here too, so the owner's preview matches the public copy.
 */
export function buildModel(cv, lang, photoUrl = null) {
    const s = cv.settings;
    const p = cv.profile || {};
    const show = s.show || {};
    const c = cv.content?.[lang] || emptyLang();
    const words = CV_WORDS[lang] || CV_WORDS.en;
    const contacts = [];
    if (show.email && p.email) contacts.push({kind: 'email', text: p.email, href: 'mailto:' + p.email});
    if (show.phone && p.phone) contacts.push({kind: 'phone', text: p.phone, href: 'tel:' + p.phone.replace(/[^\d+]/g, '')});
    if (show.location && c.location) contacts.push({kind: 'location', text: c.location});
    if (show.website && p.website) contacts.push({kind: 'website', text: prettyUrl(p.website), href: p.website});
    if (show.links) for (const l of p.links || []) if (l.url) contacts.push({kind: 'link', text: l.label || prettyUrl(l.url), href: l.url});
    if (show.birth && p.birthDate) contacts.push({kind: 'birth', text: fmtBirth(p.birthDate, lang), label: words.born});
    const sections = (c.sections || []).filter((x) => x.show !== false && sectionHasContent(x)).map((x) => ({
        ...x,
        title: x.title || SECTION_KINDS[x.kind]?.[lang] || '',
        items: (x.items || []).map((i) => (x.type === 'timeline' ? {...i, when: fmtRange(i.start, i.end, i.current, lang)} : i)),
    }));
    return {
        lang,
        template: s.template || 'modern',
        accent: s.accent || '#3D52D5',
        font: s.font || 'sans',
        name: [p.firstName, p.lastName].filter(Boolean).join(' '),
        firstName: p.firstName || '',
        lastName: p.lastName || '',
        headline: c.headline || '',
        photo: show.photo ? photoUrl : null,
        contacts,
        fields: (c.fields || []).filter((f) => f.show !== false && (f.label || f.value)),
        summary: c.summary || '',
        summaryTitle: words.profile,
        contactsTitle: words.contacts,
        sections,
        side: sections.filter((x) => x.type === 'skills' || x.type === 'langs'),
        main: sections.filter((x) => x.type !== 'skills' && x.type !== 'langs'),
        footer: c.footer || '',
    };
}

// ---------------------------------------------------------------- HTML

function icon(kind) {
    return `<svg class="cv-ico" viewBox="0 0 24 24" aria-hidden="true">${ICONS[kind] || ICONS.link}</svg>`;
}

function rich(text) {
    return blocks(text).map((b) => (b.ul ? '<ul>' + b.ul.map((li) => `<li>${esc(li)}</li>`).join('') + '</ul>' : `<p>${esc(b.p).replace(/\n/g, '<br>')}</p>`)).join('');
}

function contactsHtml(m) {
    if (!m.contacts.length) return '';
    return '<ul class="cv-contacts">' + m.contacts.map((c) => {
        const text = c.label ? `<span class="cv-c-label">${esc(c.label)}:</span> ${esc(c.text)}` : esc(c.text);
        const inner = icon(c.kind) + `<span>${text}</span>`;
        return `<li>${c.href ? `<a href="${esc(c.href)}" rel="noopener" target="${c.kind === 'email' || c.kind === 'phone' ? '_self' : '_blank'}">${inner}</a>` : inner}</li>`;
    }).join('') + '</ul>';
}

function fieldsHtml(m) {
    if (!m.fields.length) return '';
    return '<dl class="cv-fields">' + m.fields.map((f) => `<div><dt>${esc(f.label)}</dt><dd>${esc(f.value)}</dd></div>`).join('') + '</dl>';
}

function itemHtml(sec, i) {
    switch (sec.type) {
        case 'timeline':
            return `<article class="cv-item">
                <div class="cv-when">${esc(i.when)}</div>
                <div class="cv-what">
                    <h4>${esc(i.title)}</h4>
                    ${i.org || i.place ? `<div class="cv-org">${esc(i.org)}${i.org && i.place ? ' · ' : ''}${i.place ? `<span class="cv-place">${esc(i.place)}</span>` : ''}</div>` : ''}
                    ${i.text ? `<div class="cv-text">${rich(i.text)}</div>` : ''}
                </div></article>`;
        case 'skills':
            return `<li class="cv-skill"><span>${esc(i.name)}</span>${i.level ? `<span class="cv-dots" aria-label="${i.level}/5">${[1, 2, 3, 4, 5].map((n) => `<i class="${n <= i.level ? 'on' : ''}"></i>`).join('')}</span>` : ''}</li>`;
        case 'langs':
            return `<li class="cv-lang"><span>${esc(i.name)}</span>${i.level ? `<span class="cv-lvl">${esc(i.level)}</span>` : ''}</li>`;
        default:
            return `<article class="cv-item cv-item-list">
                ${i.date ? `<div class="cv-when">${esc(i.date)}</div>` : ''}
                <div class="cv-what">
                    <h4>${i.url ? `<a href="${esc(i.url)}" target="_blank" rel="noopener">${esc(i.title)}</a>` : esc(i.title)}</h4>
                    ${i.subtitle ? `<div class="cv-org">${esc(i.subtitle)}</div>` : ''}
                    ${i.text ? `<div class="cv-text">${rich(i.text)}</div>` : ''}
                </div></article>`;
    }
}

function sectionHtml(sec) {
    let body;
    if (sec.type === 'text') body = `<div class="cv-text">${rich(sec.text)}</div>`;
    else if (sec.type === 'skills' || sec.type === 'langs') body = `<ul class="cv-${sec.type}">${sec.items.filter((i) => i.name).map((i) => itemHtml(sec, i)).join('')}</ul>`;
    else body = sec.items.filter((i) => Object.values(i).some((v) => typeof v === 'string' && v.trim())).map((i) => itemHtml(sec, i)).join('');
    return `<section class="cv-sec cv-sec-${sec.type}" data-sec="${esc(sec.id)}"><h3>${esc(sec.title)}</h3>${body}</section>`;
}

function photoHtml(m) {
    return m.photo ? `<img class="cv-photo" src="${esc(m.photo)}" alt="">` : '';
}

function summaryHtml(m, titled) {
    if (!m.summary) return '';
    return `<section class="cv-sec cv-summary">${titled ? `<h3>${esc(m.summaryTitle)}</h3>` : ''}<div class="cv-text">${rich(m.summary)}</div></section>`;
}

function footerHtml(m) {
    return m.footer ? `<footer class="cv-footer">${esc(m.footer).replace(/\n/g, '<br>')}</footer>` : '';
}

function nameHtml(m) {
    return `<h1 class="cv-name">${esc(m.name)}</h1>${m.headline ? `<p class="cv-headline">${esc(m.headline)}</p>` : ''}`;
}

const LAYOUTS = {
    classic: (m) => `
        <header class="cv-head">${photoHtml(m)}<div class="cv-head-text">${nameHtml(m)}${contactsHtml(m)}</div></header>
        ${fieldsHtml(m)}${summaryHtml(m, true)}${m.sections.map(sectionHtml).join('')}${footerHtml(m)}`,
    modern: (m) => `
        <div class="cv-cols">
            <aside class="cv-side">${photoHtml(m)}
                ${m.contacts.length ? `<section class="cv-sec"><h3>${esc(m.contactsTitle)}</h3>${contactsHtml(m)}</section>` : ''}
                ${m.fields.length ? `<section class="cv-sec">${fieldsHtml(m)}</section>` : ''}
                ${m.side.map(sectionHtml).join('')}
            </aside>
            <main class="cv-main"><header class="cv-head">${nameHtml(m)}</header>${summaryHtml(m, false)}${m.main.map(sectionHtml).join('')}${footerHtml(m)}</main>
        </div>`,
    minimal: (m) => `
        <header class="cv-head"><div class="cv-head-text">${nameHtml(m)}</div>${photoHtml(m)}</header>
        ${contactsHtml(m)}${fieldsHtml(m)}${summaryHtml(m, false)}${m.sections.map(sectionHtml).join('')}${footerHtml(m)}`,
    elegant: (m) => `
        <header class="cv-head">${photoHtml(m)}${nameHtml(m)}${contactsHtml(m)}</header>
        ${fieldsHtml(m)}${summaryHtml(m, true)}${m.sections.map(sectionHtml).join('')}${footerHtml(m)}`,
};

/** The CV as an HTML string. mode: "paper" (A4 page, editor preview) or "web" (responsive public page). */
export function renderCV(m, mode = 'paper') {
    const layout = LAYOUTS[m.template] || LAYOUTS.modern;
    return `<div class="cv cv-${m.template} cv-font-${m.font} cv-${mode}${m.photo ? ' has-photo' : ''}" lang="${esc(m.lang)}" style="--cv-accent:${esc(m.accent)}">${layout(m)}</div>`;
}
