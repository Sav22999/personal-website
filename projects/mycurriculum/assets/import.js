/**
 * MyCurriculum — reading an existing CV from a PDF, in the browser (the file is never uploaded).
 *
 * pdf.js gives positioned pieces of text. They are joined into lines, two-column layouts are read one column at a
 * time, then simple rules find contacts (email, phone, links), the name (the biggest text at the top), the
 * section headings (known words in ten languages, or short lines in capitals) and, inside timeline sections,
 * one entry per date range. The result always needs a human check: the editor says so.
 */

import {SECTION_KINDS} from './i18n.js';

/** Words that open a section, per kind (lower case, without accents). */
const HEADINGS = {
    summary: ['profilo', 'profilo personale', 'profilo professionale', 'chi sono', 'sommario', 'presentazione', 'su di me', 'obiettivo', 'obiettivi', 'summary', 'profile', 'about', 'about me', 'professional summary', 'objective', 'career objective', 'personal statement', 'profil', 'a propos', 'resume', 'kurzprofil', 'uber mich', 'perfil', 'sobre mi', 'resumen', 'sobre mim', 'profiel', 'over mij', 'o mnie', 'podsumowanie', 'despre mine', 'om mig', 'sammanfattning'],
    experience: ['esperienza', 'esperienze', 'esperienza professionale', 'esperienze professionali', 'esperienza lavorativa', 'esperienze lavorative', 'carriera', 'experience', 'work experience', 'professional experience', 'employment', 'employment history', 'work history', 'career', 'experience professionnelle', 'experiences professionnelles', 'experiences', 'berufserfahrung', 'berufliche erfahrung', 'werdegang', 'experiencia', 'experiencia laboral', 'experiencia profesional', 'experiencia profissional', 'werkervaring', 'ervaring', 'doswiadczenie', 'doswiadczenie zawodowe', 'experienta', 'experienta profesionala', 'arbetslivserfarenhet', 'erfarenhet'],
    education: ['istruzione', 'formazione', 'istruzione e formazione', 'studi', 'percorso di studi', 'titoli di studio', 'education', 'education and training', 'academic background', 'qualifications', 'formation', 'formations', 'etudes', 'ausbildung', 'bildung', 'studium', 'educacion', 'formacion', 'formacion academica', 'educacao', 'formacao', 'formacao academica', 'opleiding', 'opleidingen', 'wyksztalcenie', 'edukacja', 'educatie', 'studii', 'utbildning'],
    skills: ['competenze', 'competenze tecniche', 'competenze personali', 'competenze digitali', 'capacita', 'abilita', 'conoscenze', 'conoscenze informatiche', 'skills', 'technical skills', 'key skills', 'core skills', 'competencies', 'hard skills', 'soft skills', 'tools', 'competences', 'kenntnisse', 'fahigkeiten', 'kompetenzen', 'habilidades', 'competencias', 'aptitudes', 'vaardigheden', 'competenties', 'umiejetnosci', 'kompetencje', 'competente', 'abilitati', 'fardigheter', 'kompetenser'],
    languages: ['lingue', 'lingue straniere', 'conoscenze linguistiche', 'competenze linguistiche', 'languages', 'language skills', 'langues', 'sprachen', 'sprachkenntnisse', 'idiomas', 'lenguas', 'talen', 'talenkennis', 'jezyki', 'jezyki obce', 'limbi', 'limbi straine', 'sprak', 'sprakkunskaper'],
    certifications: ['certificazioni', 'certificati', 'attestati', 'certifications', 'certificates', 'licenses', 'licenses and certifications', 'zertifikate', 'zertifizierungen', 'certificaciones', 'certificacoes', 'certificaten', 'certyfikaty', 'certificari', 'certifieringar'],
    projects: ['progetti', 'projects', 'personal projects', 'projets', 'projekte', 'proyectos', 'projetos', 'projecten', 'projekty', 'proiecte', 'projekt'],
    volunteering: ['volontariato', 'volunteering', 'volunteer experience', 'volunteer', 'benevolat', 'ehrenamt', 'voluntariado', 'vrijwilligerswerk', 'wolontariat', 'voluntariat', 'ideellt arbete'],
    awards: ['premi', 'riconoscimenti', 'premi e riconoscimenti', 'awards', 'honors', 'honours', 'achievements', 'distinctions', 'auszeichnungen', 'premios', 'premios y reconocimientos', 'premios', 'prijzen', 'nagrody', 'premii', 'utmarkelser'],
    publications: ['pubblicazioni', 'publications', 'publikationen', 'publicaciones', 'publicacoes', 'publicaties', 'publikacje', 'publicatii', 'publikationer'],
    interests: ['interessi', 'hobby', 'hobbies', 'interessi personali', 'tempo libero', 'interests', 'hobbies and interests', 'centres d interet', 'loisirs', 'interessen', 'hobbys', 'intereses', 'aficiones', 'interesses', 'interesses', 'hobbys', 'zainteresowania', 'interese', 'intressen', 'fritidsintressen'],
    contacts: ['contatti', 'recapiti', 'informazioni personali', 'dati personali', 'contact', 'contacts', 'contact information', 'personal information', 'personal details', 'coordonnees', 'informations personnelles', 'kontakt', 'personliche daten', 'contacto', 'datos personales', 'contacto', 'dados pessoais', 'contactgegevens', 'persoonlijke gegevens', 'dane osobowe', 'date personale', 'kontaktuppgifter', 'personuppgifter'],
    references: ['referenze', 'references', 'referenzen', 'referencias', 'referenties', 'referencje', 'referinte', 'referenser'],
};

const MONTHS = 'gen|genn|gennaio|feb|febb|febbraio|mar|marzo|apr|aprile|mag|maggio|giu|giugno|lug|luglio|ago|agosto|set|sett|settembre|ott|ottobre|nov|novembre|dic|dicembre|jan|january|february|march|april|may|june|jul|july|aug|august|sep|sept|september|oct|october|november|dec|december|janv|janvier|fevr|fevrier|mars|avr|avril|mai|juin|juil|juillet|aout|sept|septembre|octobre|novembre|decembre|januar|februar|marz|juni|juli|okt|oktober|dez|dezember|ene|enero|febrero|abr|abril|mayo|junio|julio|agosto|septiembre|setiembre|octubre|noviembre|diciembre|janeiro|fevereiro|marco|maio|junho|julho|setembro|outubro|novembro|dezembro|januari|februari|maart|mei|augustus|oktober';
const PRESENT = 'presente|present|current|currently|now|today|oggi|attuale|in corso|ad oggi|a oggi|aujourd.hui|actuel|actuellement|heute|aktuell|derzeit|actualidad|actual|hoy|atual|atualmente|heden|nu|obecnie|teraz|prezent|pagina|pagaende';
const DATE = `(?:(?:${MONTHS})\\.?\\s+\\d{4}|\\d{1,2}[/.\\-]\\d{4}|\\d{4}[/.\\-]\\d{1,2}|\\d{4})`;
const RANGE_RE = new RegExp(`(${DATE})\\s*(?:-|–|—|−|to|a|al|until|bis|hasta|ate|tot|do|pana|till|>|→)\\s*(${DATE}|${PRESENT})`, 'i');
const SINGLE_RE = new RegExp(`(?:^|\\s|\\()(${DATE})(?:\\)|\\s|$)`, 'i');
const EMAIL_RE = /[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/;
const PHONE_RE = /(?:\+|00)?\d[\d\s().\-/]{7,}\d/;
const URL_RE = /\b((?:https?:\/\/)?(?:www\.)?(?:linkedin\.com|github\.com|gitlab\.com|behance\.net|dribbble\.com|[a-z0-9\-]+\.(?:com|it|dev|io|net|org|me|eu|fr|de|es))(?:\/[^\s,;)]*)?)/i;
const BULLET_RE = /^[•▪◦●■□➢➤►▸‣∙·*\-–]\s*/;

function norm(s) {
    return s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/[^a-z ]+/g, ' ').replace(/\s+/g, ' ').trim();
}

// ---------------------------------------------------------------- pdf.js → lines

async function readPages(file) {
    const base = new URL('../vendor/', import.meta.url).href;
    const pdfjs = await import(base + 'pdf.min.mjs');
    pdfjs.GlobalWorkerOptions.workerSrc = base + 'pdf.worker.min.mjs';
    const doc = await pdfjs.getDocument({data: await file.arrayBuffer(), isEvalSupported: false}).promise;
    const pages = [];
    for (let n = 1; n <= Math.min(doc.numPages, 8); n++) {
        const page = await doc.getPage(n);
        const vp = page.getViewport({scale: 1});
        const tc = await page.getTextContent();
        const items = tc.items.filter((i) => i.str && i.str.trim()).map((i) => ({
            // letter-spaced text comes as "W O R K": the spaces between single letters are not real
            str: /^(?:\S ){2,}\S$/.test(i.str) ? i.str.replace(/ /g, '') : i.str, x: i.transform[4], y: vp.height - i.transform[5], w: i.width, size: Math.round(Math.hypot(i.transform[2], i.transform[3]) * 10) / 10 || i.height,
        }));
        pages.push({items, width: vp.width});
    }
    return pages;
}

/** Pieces on the same baseline become segments; a wide horizontal gap starts a new segment. */
function segments(items) {
    const rows = [];
    for (const it of [...items].sort((a, b) => a.y - b.y || a.x - b.x)) {
        const row = rows.find((r) => Math.abs(r.y - it.y) < Math.max(2.5, it.size * 0.35));
        if (row) row.items.push(it);
        else rows.push({y: it.y, items: [it]});
    }
    const segs = [];
    rows.forEach((r, ri) => r.row = ri);
    for (const r of rows) {
        r.items.sort((a, b) => a.x - b.x);
        let cur = null;
        for (const it of r.items) {
            const gap = cur ? it.x - (cur.x2) : 0;
            if (cur && gap < Math.max(10, it.size * 1.2)) {
                // letter-spaced text (headings) comes one character at a time: real spaces come as items of their own
                const spaced = cur.lastLen === 1 && it.str.length === 1;
                cur.text += (gap > it.size * 0.15 && !spaced && !cur.text.endsWith(' ') && !it.str.startsWith(' ') ? ' ' : '') + it.str;
                cur.lastLen = it.str.trim().length;
                cur.x2 = it.x + it.w;
                cur.size = Math.max(cur.size, it.size);
            } else {
                cur = {text: it.str, x: it.x, x2: it.x + it.w, y: r.y, size: it.size, row: r.row, lastLen: it.str.trim().length};
                segs.push(cur);
            }
        }
    }
    return segs.map((s) => ({...s, text: s.text.replace(/\s+/g, ' ').trim()})).filter((s) => s.text);
}

/** Finds an empty vertical band (a gutter between two columns) and reads the left column, then the right one. */
function orderSegments(segs, width) {
    if (segs.length < 12) return segs.map((s) => ({...s, col: 0}));
    let best = null;
    for (let x = width * 0.22; x <= width * 0.72; x += 4) {
        const crossing = segs.filter((s) => s.x < x - 2 && s.x2 > x + 2).length;
        const left = segs.filter((s) => s.x2 <= x + 2).length;
        const right = segs.filter((s) => s.x >= x - 2).length;
        // a real second column has headings of its own (right-aligned dates or a two-up skill grid do not)
        const headed = (side) => segs.some((s) => side(s) && headingKind(s));
        if (crossing <= segs.length * 0.04 && left >= 4 && right >= 4 && headed((s) => s.x2 <= x + 2) && headed((s) => s.x >= x - 2)) {
            const score = Math.min(left, right) - crossing * 6;
            if (!best || score > best.score) best = {x, score};
        }
    }
    if (!best) return segs.map((s) => ({...s, col: 0}));
    // a full-width header above the columns (e.g. the name) stays first
    const top = [];
    const rest = [];
    const crossers = segs.filter((s) => s.x < best.x - 2 && s.x2 > best.x + 2);
    const headerEnd = crossers.length ? Math.max(...crossers.map((s) => s.y)) : -1;
    for (const s of segs) (s.y <= headerEnd ? top : rest).push(s);
    const left = rest.filter((s) => s.x < best.x).sort((a, b) => a.y - b.y || a.x - b.x).map((s) => ({...s, col: 1}));
    const right = rest.filter((s) => s.x >= best.x).sort((a, b) => a.y - b.y || a.x - b.x).map((s) => ({...s, col: 2}));
    return [...top.sort((a, b) => a.y - b.y || a.x - b.x).map((s) => ({...s, col: 0})), ...left, ...right];
}

// ---------------------------------------------------------------- lines → CV

function headingKind(line) {
    const n = norm(line.text.replace(/:$/, ''));
    if (!n || n.length > 42) return null;
    // compared without spaces too: letter-spaced headings may come as "E d u c at i o n"
    const flat = n.replace(/ /g, '');
    for (const [kind, words] of Object.entries(HEADINGS)) if (words.includes(n) || words.some((w) => w.replace(/ /g, '') === flat)) return kind;
    // short, all capitals, no digits: a heading we do not know
    const letters = line.text.replace(/[^A-Za-zÀ-ÿ]/g, '');
    if (letters.length >= 4 && line.text.length <= 32 && line.text === line.text.toUpperCase() && !/\d/.test(line.text) && !EMAIL_RE.test(line.text)) return 'other';
    return null;
}

function normDate(s) {
    s = s.trim();
    let m;
    if ((m = s.match(/^(\d{1,2})[/.\-](\d{4})$/))) return m[2] + '-' + m[1].padStart(2, '0');
    if ((m = s.match(/^(\d{4})[/.\-](\d{1,2})$/))) return m[1] + '-' + m[2].padStart(2, '0');
    const words = s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9 ]+/g, ' ').trim().split(/\s+/);
    if (words.length === 2 && /^\d{4}$/.test(words[1])) {
        const idx = monthIndex(words[0]);
        if (idx) return words[1] + '-' + String(idx).padStart(2, '0');
    }
    return s;
}

const MONTH_NAMES = [
    ['gen', 'jan', 'ene', 'janv', 'sty'], ['feb', 'fev', 'fevr', 'lut'], ['mar', 'marz', 'maart'], ['apr', 'avr', 'abr', 'kwi'], ['mag', 'may', 'mai', 'mei', 'maio', 'mayo', 'maj'], ['giu', 'jun', 'juin', 'junio', 'junho', 'cze'],
    ['lug', 'jul', 'juil', 'julio', 'julho', 'lip'], ['ago', 'aug', 'aout', 'agosto', 'sie'], ['set', 'sep', 'sept', 'wrz'], ['ott', 'oct', 'okt', 'out', 'paz'], ['nov', 'lis'], ['dic', 'dec', 'dez', 'dici', 'gru'],
];

function monthIndex(word) {
    for (let i = 0; i < 12; i++) if (MONTH_NAMES[i].some((p) => word.startsWith(p))) return i + 1;
    return 0;
}

/** Date range in a line: {start, end, current, rest} or null. */
function findDates(text) {
    let m = text.match(RANGE_RE);
    if (m) {
        const isNow = new RegExp(`^(${PRESENT})$`, 'i').test(m[2].trim());
        return {start: normDate(m[1]), end: isNow ? '' : normDate(m[2]), current: isNow, rest: cleanRest(text.replace(m[0], ''))};
    }
    m = text.match(SINGLE_RE);
    if (m && text.replace(m[1], '').replace(/[^A-Za-zÀ-ÿ]/g, '').length < 40) return {start: '', end: normDate(m[1]), current: false, rest: cleanRest(text.replace(m[1], ''))};
    return null;
}

function cleanRest(s) {
    return s.replace(/^[\s|,·•:–—\-()]+|[\s|,·•:–—\-()]+$/g, '').replace(/\(\s*\)/g, '').trim();
}

/** The usual size of body text in a list of lines. */
function bodySize(lines) {
    const sizes = lines.map((l) => l.size).sort((a, b) => a - b);
    return sizes[Math.floor(sizes.length / 2)] || 10;
}

function parseTimeline(lines) {
    const items = [];
    let cur = null;
    const body = bodySize(lines);
    const push = () => cur && (cur.title || cur.org || cur.text) && items.push(cur);
    for (const l of lines) {
        const d = findDates(l.text);
        if (d && cur && cur.text && cur.lastBig) {
            // "Title" then "dates": the title printed above the dates was taken as text of the entry before
            const title = cur.lastBig;
            cur.text = cur.text.split('\n').slice(0, -1).join('\n');
            push();
            cur = {title, org: '', place: '', start: d.start, end: d.end, current: d.current, text: '', hasDate: true, lines: 1};
            if (d.rest) cur.org = d.rest;
            continue;
        }
        if (d) {
            // a date that follows a title on its own line belongs to the same entry
            if (cur && !cur.hasDate && !cur.text && cur.lines <= 2) {
                Object.assign(cur, {start: d.start, end: d.end, current: d.current, hasDate: true});
                if (d.rest) cur.title ? (cur.org ? (cur.place = cur.place || d.rest) : (cur.org = d.rest)) : (cur.title = d.rest);
                continue;
            }
            push();
            cur = {title: d.rest, org: '', place: '', start: d.start, end: d.end, current: d.current, text: '', hasDate: true, lines: d.rest ? 1 : 0};
            continue;
        }
        if (!cur) cur = {title: '', org: '', place: '', start: '', end: '', current: false, text: '', hasDate: false, lines: 0};
        const bullet = BULLET_RE.test(l.text);
        const t = l.text.replace(BULLET_RE, '').trim();
        if (!bullet && !cur.title) cur.title = t;
        else if (!bullet && !cur.org && t.length < 90 && !cur.text) cur.org = t;
        else cur.text += (cur.text ? '\n' : '') + (bullet ? '- ' + t : t);
        cur.lastBig = !bullet && l.size > body + 0.4 && cur.text.endsWith(t) ? t : '';
        cur.lines++;
    }
    push();
    return items.map(({hasDate, lines, lastBig, ...i}) => ({...i, text: joinWrapped(i.text)}));
}

/** Lines broken by the PDF layout are joined again; bullets stay on their own line. */
function joinWrapped(text) {
    const out = [];
    for (const line of text.split('\n')) {
        const last = out[out.length - 1];
        if (last !== undefined && !line.startsWith('- ') && !/[.:;!?]$/.test(last) && /^[a-zà-ÿ(]/.test(line)) out[out.length - 1] = last + ' ' + line;
        else out.push(line);
    }
    return out.join('\n');
}

function splitTags(lines) {
    return lines.flatMap((l) => l.text.replace(BULLET_RE, '').split(/\s*[,;•|·]\s*/)).map((s) => s.trim()).filter((s) => s && s.length < 70);
}

const LEVEL_RE = /^(?:[ABC][12]\b|native|madrelingua|mother tongue|bilingual|bilingue|fluent|fluente|basic|base|scolastico|elementare|intermediate|intermedio|advanced|avanzato|buono|ottimo|discreto|professional|proficient|courant|natif|langue maternelle|muttersprache|fließend|fliessend|grundkenntnisse|nativo|lengua materna|língua materna|moedertaal|vloeiend|ojczysty|biegły|limba maternă|modersmål|flytande)/i;

/** "Inglese: C1", "English (fluent)", or the name and the level side by side on one row. */
function parseLangs(lines) {
    const out = [];
    for (const piece of lines.flatMap((l) => l.text.replace(BULLET_RE, '').split(/\s*[,;•|·]\s*/).map((text) => ({text, row: l.row})))) {
        const s = piece.text.trim();
        if (!s) continue;
        const m = s.match(/^([^:–\-()]+?)\s*(?:[:–\-]|\()\s*(.+?)\)?$/);
        const last = out[out.length - 1];
        if (m) out.push({name: m[1].trim(), level: m[2].trim(), row: piece.row});
        else if (LEVEL_RE.test(s) && last && !last.level && last.row === piece.row) last.level = s;
        else if (LEVEL_RE.test(s) && last && !last.level) last.level = s;
        else out.push({name: s, level: '', row: piece.row});
    }
    return out.filter((x) => x.name.length < 40).map(({row, ...x}) => x);
}

function parseList(lines) {
    const items = [];
    const big = Math.max(...lines.map((l) => l.size));
    const mixed = big > Math.min(...lines.map((l) => l.size)) + 0.4; // titles bigger than the rest: smaller lines describe the title above
    for (const l of lines) {
        const bullet = BULLET_RE.test(l.text);
        const t = l.text.replace(BULLET_RE, '').trim();
        const last = items[items.length - 1];
        if (last && mixed && l.size < big - 0.4) {
            if (!last.subtitle && !last.text && !bullet) last.subtitle = t;
            else last.text += (last.text ? '\n' : '') + (bullet ? '- ' : '') + t;
            continue;
        }
        if (last && !bullet && /^[a-zà-ÿ(]/.test(t)) {
            last.text += (last.text ? ' ' : '') + t;
            continue;
        }
        const d = findDates(t);
        items.push({title: d ? d.rest || t : t, subtitle: '', date: d ? [d.start, d.end].filter(Boolean).join(' – ') : '', url: '', text: ''});
    }
    return items;
}

/**
 * Reads a PDF and returns {profile, content, stats} for one CV language.
 * profile: what was found among the contacts (only filled keys). content: headline, summary, sections.
 */
export async function importPdf(file, lang) {
    const pages = await readPages(file);
    let lines = [];
    pages.forEach((p, pi) => lines.push(...orderSegments(segments(p.items), p.width).map((l) => ({...l, page: pi, row: pi * 10000 + l.row}))));
    lines = lines.filter((l) => !/^\d+\s*(\/\s*\d+)?$/.test(l.text) && !/^(page|pagina|seite|página|strona)\s*\d+/i.test(l.text));
    if (!lines.length) throw new Error('scanned');

    const profile = {};
    const links = [];
    const all = lines.map((l) => l.text).join('\n');
    const email = all.match(EMAIL_RE);
    if (email) profile.email = email[0].toLowerCase();
    for (const l of lines) {
        if (!profile.phone) {
            const ph = l.text.replace(EMAIL_RE, '').match(PHONE_RE);
            if (ph && ph[0].replace(/\D/g, '').length >= 8 && ph[0].replace(/\D/g, '').length <= 15 && !RANGE_RE.test(ph[0])) profile.phone = ph[0].trim();
        }
        const u = l.text.replace(EMAIL_RE, '').match(URL_RE);
        if (u && !/@/.test(u[0])) {
            const url = /^https?:/i.test(u[1]) ? u[1] : 'https://' + u[1];
            const host = url.replace(/^https?:\/\/(www\.)?/i, '').split('/')[0].toLowerCase();
            const label = host.includes('linkedin') ? 'LinkedIn' : host.includes('github') ? 'GitHub' : host.includes('gitlab') ? 'GitLab' : host.includes('behance') ? 'Behance' : host.includes('dribbble') ? 'Dribbble' : '';
            if (label) {
                if (!links.some((x) => x.label === label)) links.push({label, url});
            } else if (!profile.website) profile.website = url;
        }
        const b = l.text.match(/(?:nat[oa] il|data di nascita|date of birth|born|birth date|geboren|né[e]? le|fecha de nacimiento|data de nascimento)\s*:?\s*(\d{1,2})[/.\-](\d{1,2})[/.\-](\d{4})/i);
        if (b && !profile.birthDate) profile.birthDate = `${b[3]}-${b[2].padStart(2, '0')}-${b[1].padStart(2, '0')}`;
    }
    if (links.length) profile.links = links;

    // The name: the biggest text of the first page (whatever column it is in), without digits or @
    const isContact = (t) => EMAIL_RE.test(t) || (PHONE_RE.test(t) && t.replace(/\D/g, '').length >= 8) || URL_RE.test(t);
    const nameLine = lines.filter((l) => l.page === 0 && !/\d|@/.test(l.text) && l.text.length <= 50 && l.text.split(' ').length <= 5 && ['other', null].includes(headingKind(l))).sort((a, b) => b.size - a.size || a.y - b.y)[0];
    const used = new Set();
    let headline = '';
    if (nameLine) {
        const next = lines[lines.indexOf(nameLine) + 1];
        if (next && next.col === nameLine.col && next.text.length < 80 && !isContact(next.text) && !headingKind(next) && !findDates(next.text)) {
            headline = next.text;
            used.add(next);
        }
        used.add(nameLine);
        const words = nameLine.text.split(/\s+/).map((w) => (w === w.toUpperCase() ? w.charAt(0) + w.slice(1).toLowerCase() : w));
        profile.firstName = words.slice(0, -1).join(' ') || words[0];
        profile.lastName = words.length > 1 ? words[words.length - 1] : '';
    }

    // Sections
    const content = {headline: '', location: '', summary: '', fields: [], sections: [], footer: ''};
    const buckets = [];
    let current = {kind: 'top', title: '', lines: []};
    let prev = null;
    for (const l of lines) {
        // moving to the other column ends the section (the sidebar does not continue into the main column)
        if (prev && l.page === prev.page && l.col !== prev.col && current.kind !== 'top') {
            buckets.push(current);
            current = {kind: 'top', title: '', lines: []};
        }
        prev = l;
        if (used.has(l)) continue;
        const k = headingKind(l);
        if (k) {
            buckets.push(current);
            current = {kind: k, title: l.text.replace(/:$/, ''), lines: []};
        } else current.lines.push(l);
    }
    buckets.push(current);

    // Lines outside any section: headline, maybe the summary
    const topText = buckets.filter((b) => b.kind === 'top').flatMap((b) => b.lines).filter((l) => !isContact(l.text) && !findDates(l.text));
    const firstTop = headline ? null : topText.find((l) => l.text.length < 80);
    content.headline = headline || (firstTop ? firstTop.text : '');
    const longTop = topText.filter((l) => l !== firstTop && l.text.length >= 60);
    const consent = /(autorizzo|trattamento dei (miei )?dati|d\.?\s?lgs|196\/2003|gdpr|2016\/679|rodo|dsgvo|rgpd)/i;
    const footerLines = lines.filter((l) => consent.test(l.text));
    if (footerLines.length) {
        const start = lines.indexOf(footerLines[0]);
        content.footer = lines.slice(start, start + 4).filter((l) => !headingKind(l)).map((l) => l.text).join(' ').trim().slice(0, 900);
    }
    const notFooter = (l) => !footerLines.includes(l) && !(content.footer && content.footer.includes(l.text));

    let id = 0;
    for (const b of buckets) {
        const ls = b.lines.filter(notFooter).filter((l) => !(b.kind !== 'contacts' && isContact(l.text) && l.text.length < 60));
        if (b.kind === 'top' || b.kind === 'contacts' || b.kind === 'references') continue;
        if (b.kind === 'summary') {
            content.summary = joinWrapped(ls.map((l) => (BULLET_RE.test(l.text) ? '- ' + l.text.replace(BULLET_RE, '') : l.text)).join('\n'));
            continue;
        }
        if (!ls.length) continue;
        const kind = b.kind === 'other' ? 'list' : b.kind;
        const type = SECTION_KINDS[kind]?.type || 'list';
        const title = b.title.charAt(0) + b.title.slice(1).toLowerCase();
        const sec = {id: 'imp' + (++id), type, kind, title: b.kind === 'other' ? title : SECTION_KINDS[kind][lang] || title, show: true, text: '', items: []};
        if (type === 'timeline') sec.items = parseTimeline(ls);
        else if (type === 'skills') sec.items = splitTags(ls).map((name) => ({name, level: 0}));
        else if (type === 'langs') sec.items = parseLangs(ls);
        else if (type === 'text') sec.text = joinWrapped(ls.map((l) => l.text).join('\n'));
        else {
            // an unknown heading with dates inside is probably a timeline
            if (b.kind === 'other' && ls.filter((l) => findDates(l.text)).length >= 2) {
                sec.type = 'timeline';
                sec.kind = 'list';
                sec.items = parseTimeline(ls);
            } else sec.items = parseList(ls);
        }
        if (sec.items.length || sec.text) content.sections.push(sec);
    }
    if (!content.summary && longTop.length) content.summary = joinWrapped(longTop.map((l) => l.text).join('\n'));
    return {profile, content, stats: {sections: content.sections.length}};
}
