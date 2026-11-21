/**
 * MyCurriculum — reading an existing CV from a PDF, in the browser (the file is never uploaded).
 *
 * pdf.js gives positioned pieces of text. They are joined into lines, two-column layouts are read one column at a
 * time, then simple rules find contacts (email, phone, links), the name (the biggest text at the top), the
 * section headings (known words in ten languages, or short lines in capitals) and, inside timeline sections,
 * one entry per date range. The result always needs a human check: the editor says so.
 */

import {SECTION_KINDS, CV_WORDS} from './i18n.js';

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
const PRESENT = 'presente|present|current|currently|now|today|oggi|attuale|in corso|ad oggi|a oggi|aujourd.hui|actuel|actuellement|heute|aktuell|derzeit|actualidad|actual|hoy|atual|atualmente|heden|nu|obecnie|teraz|prezent|pagaende';
const DATE = `(?:\\d{1,2}[/.\\-]\\d{1,2}[/.\\-]\\d{4}|(?:${MONTHS})\\.?\\s+\\d{4}|\\d{1,2}[/.\\-]\\d{4}|\\d{4}[/.\\-]\\d{1,2}|\\d{4})`;
const RANGE_RE = new RegExp(`(${DATE})\\s*(?:-|–|—|−|to|a|al|until|bis|hasta|ate|tot|do|pana|till|>|→)\\s*(${DATE}|${PRESENT})`, 'i');
const SINGLE_RE = new RegExp(`(?:^|\\s|\\()(${DATE})(?:\\)|\\s|$)`, 'i');
const EMAIL_RE = /[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/;
const PHONE_RE = /(?:\(?\+\d{1,3}\)?|00\d{1,3})?[\s.\-]*\d[\d\s().\-/]{6,}\d/;
const URL_RE = /\b((?:https?:\/\/)?(?:www\.)?(?:linkedin\.com|github\.com|gitlab\.com|behance\.net|dribbble\.com|[a-z0-9\-]+\.(?:com|it|dev|io|net|org|me|eu|fr|de|es))(?:\/[^\s,;)]*)?)/i;
/** A phone number in a line: 8–15 digits, never a date or a date range ("31/08/2024 – 16/04/2026"). */
function findPhone(text) {
    const m = text.replace(EMAIL_RE, '').replace(new RegExp(RANGE_RE.source, 'gi'), ' ').replace(/\b\d{1,2}[/.]\d{1,2}[/.]\d{2,4}\b/g, ' ').match(PHONE_RE);
    if (!m) return '';
    const digits = m[0].replace(/\D/g, '').length;
    return digits >= 8 && digits <= 15 ? m[0].trim() : '';
}

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
        // which text is bold: the operator list loads the fonts, whose names say it ("OpenSans-Bold")
        await page.getOperatorList();
        const isBold = (fontName) => {
            try {
                const f = page.commonObjs.get(fontName);
                return !!f && (f.bold || /bold|black|heavy|semibold|demi|medium/i.test(f.name || ''));
            } catch (e) {
                return false;
            }
        };
        // links of the PDF (the address behind "LinkedIn: saveriomorelli")
        const links = (await page.getAnnotations()).map((a) => a.url).filter(Boolean);
        const items = tc.items.filter((i) => i.str && i.str.trim()).map((i) => ({
            bold: isBold(i.fontName),
            // letter-spaced text comes as "W O R K": the spaces between single letters are not real
            str: /^(?:\S ){2,}\S$/.test(i.str) ? i.str.replace(/ /g, '') : i.str, x: i.transform[4], y: vp.height - i.transform[5], w: i.width, size: Math.round(Math.hypot(i.transform[2], i.transform[3]) * 10) / 10 || i.height,
        }));
        pages.push({items, width: vp.width, links});
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
                if (cur.bold && !it.bold && !cur.boldText) cur.boldText = cur.text.slice(0, cur.text.length - it.str.length).trim(); // "TITLE University of X": bold title, then the school
                cur.bold = cur.bold && it.bold;
                cur.x2 = it.x + it.w;
                cur.size = Math.max(cur.size, it.size);
            } else {
                cur = {text: it.str, x: it.x, x2: it.x + it.w, y: r.y, size: it.size, row: r.row, lastLen: it.str.trim().length, bold: it.bold, boldText: ''};
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
        // only known section headings count: "HTML & CSS" in capitals is a skill, not the title of a second column
        const headed = (side) => segs.some((s) => side(s) && !['other', null].includes(headingKind(s)));
        if (crossing <= segs.length * 0.04 && left >= 4 && right >= 4 && headed((s) => s.x2 <= x + 2) && headed((s) => s.x >= x - 2)) {
            const score = Math.min(left, right) - crossing * 6;
            if (!best || score > best.score) best = {x, score};
        }
    }
    if (!best) return segs.map((s) => ({...s, col: 0}));
    // a narrow column made of section titles (Swiss: the title on the left, the content beside it) is not a second
    // column of content: the page is read row by row, each title before its content
    const leftSegs = segs.filter((s) => s.x2 <= best.x + 2);
    if (leftSegs.filter((s) => headingKind(s)).length >= leftSegs.length * 0.6) return segs.map((s) => ({...s, col: 0}));
    // a full-width header above the columns (e.g. the name) stays first
    // full-width lines above the first heading are a header (read first), below it a footer (read last)
    const top = [];
    const rest = [];
    const bottom = [];
    const firstHeading = Math.min(...segs.filter((s) => !['other', null].includes(headingKind(s))).map((s) => s.y), Infinity);
    const crossers = segs.filter((s) => s.x < best.x - 2 && s.x2 > best.x + 2);
    const headerCrossers = crossers.filter((s) => s.y < firstHeading);
    const headerEnd = headerCrossers.length ? Math.max(...headerCrossers.map((s) => s.y)) : -1;
    for (const s of segs) {
        if (s.y <= headerEnd) top.push(s);
        else if (crossers.includes(s)) bottom.push(s);
        else rest.push(s);
    }
    const left = rest.filter((s) => s.x < best.x).sort((a, b) => a.y - b.y || a.x - b.x).map((s) => ({...s, col: 1}));
    const right = rest.filter((s) => s.x >= best.x).sort((a, b) => a.y - b.y || a.x - b.x).map((s) => ({...s, col: 2}));
    return [...top.sort((a, b) => a.y - b.y || a.x - b.x).map((s) => ({...s, col: 0})), ...left, ...right, ...bottom.sort((a, b) => a.y - b.y).map((s) => ({...s, col: 0}))];
}

// ---------------------------------------------------------------- lines → CV

function headingKind(line) {
    const n = norm(line.text.replace(/:$/, ''));
    if (!n || n.length > 42) return null;
    if (GRID_RE.test(line.text.trim())) return null; // "UNDERSTANDING", "SPEAKING" of the Europass language grid
    // compared without spaces too: letter-spaced headings may come as "E d u c at i o n"
    const flat = n.replace(/ /g, '');
    for (const [kind, words] of Object.entries(HEADINGS)) if (words.includes(n) || words.some((w) => w.replace(/ /g, '') === flat)) return kind;
    // short, all capitals, no digits: a heading we do not know
    const letters = line.text.replace(/[^A-Za-zÀ-ÿ]/g, '');
    // ...but not an acronym like "A.V.I.S.", which is the title of an entry
    if (letters.length >= 4 && line.text.length <= 32 && line.text === line.text.toUpperCase() && !/\d/.test(line.text) && !EMAIL_RE.test(line.text) && !/^(?:\p{L}\.){2,}/u.test(line.text.trim())) return 'other';
    return null;
}

function normDate(s) {
    s = s.trim();
    let m;
    if ((m = s.match(/^\d{1,2}[/.\-](\d{1,2})[/.\-](\d{4})$/))) return m[2] + '-' + m[1].padStart(2, '0');
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

const SMALL_WORDS = new Set(['of', 'and', 'the', 'for', 'at', 'in', 'on', 'to', 'a', 'an', 'di', 'e', 'del', 'della', 'dei', 'per', 'il', 'la', 'le', 'lo', 'da', 'de', 'des', 'du', 'et', 'und', 'der', 'die', 'das', 'y', 'el', 'en']);

/** "UX DESIGNER AND FRONTEND DEVELOPER" -> "UX Designer and Frontend Developer" (short words in capitals stay, e.g. UX, IT). */
function smartCase(s) {
    if (!s || s !== s.toUpperCase() || !/\p{L}{2}/u.test(s)) return s;
    return s.toLowerCase().split(/(\s+|[-–/(])/).map((w, i) => {
        if (!/\p{L}/u.test(w)) return w;
        if (i > 0 && SMALL_WORDS.has(w)) return w;
        if (w.replace(/\P{L}/gu, '').length <= 3 && !SMALL_WORDS.has(w)) return w.toUpperCase(); // UX, IT, (BCN)
        return w.charAt(0).toUpperCase() + w.slice(1);
    }).join('');
}

/** "TITLE – ORGANIZATION – – PLACE" (what remains of a Europass header without its dates). */
function splitHeader(rest) {
    // en/em dashes separate the parts; a plain hyphen only when there is no dash ("IT ASSISTANT - "150 ORE" – UNIVERSITY")
    const sep = /[–—|]/.test(rest) ? /\s+[–—]\s+|\s*\|\s*/ : /\s+-\s+/;
    return rest.split(sep).map((x) => x.replace(/^[–—\-\s]+|[–—\-\s]+$/g, '').trim()).filter(Boolean);
}

const EQF_RE = /\b(?:EQF|CEC|EQR|ERK|QEQ|MEC|NQF)\b\D{0,15}([1-8])\b|\blivello\s+EQF\s*([1-8])/i;
const GRADE_RE = /^(?:final\s+grade|grade|gpa|voto(?:\s+di\s+laurea)?|votazione(?:\s+finale)?|note|calificaci[oó]n|nota\s+final|cijfer|ocena|not[aă]|betyg)\s*:?\s+(.{1,40})$/i;
const LINK_RE = /^(?:link|url|website|web\s*site|sito(?:\s+web)?|site|enlace|lien)\s*:?\s+((?:https?:\/\/|www\.)\S+)$/i;

/**
 * One entry per date range, or per bold title when an entry has no dates (projects). Europass headers are split
 * ("TITLE – ORGANIZATION – dates – PLACE"), "EQF level 7", "Grade: …" and "Link https://…" fill their own fields.
 */
function parseTimeline(lines) {
    const items = [];
    let cur = null;
    const body = bodySize(lines);
    const fresh = (extra = {}) => ({title: '', org: '', place: '', start: '', end: '', current: false, text: '', url: '', eqf: 0, grade: '', hasDate: false, lines: 0, ...extra});
    const push = () => cur && (cur.title || cur.org || cur.text) && items.push(cur);
    // when the PDF has bold text, titles are the bold lines; otherwise the bigger ones
    const hasBold = lines.some((l) => l.bold);
    const looksTitle = (l) => !BULLET_RE.test(l.text) && (hasBold ? l.bold : l.size > body + 0.4) && l.text.length < 140;
    const fillHeader = (rest, dateFirst) => {
        if (!rest) return;
        const parts = splitHeader(rest);
        cur.placeOpen = lineOpen; // "– ROVERETO," and the country on the next line
        if (parts.length >= 2) {
            // "TITLE – ORGANIZATION – PLACE"
            cur.title ||= smartCase(parts[0]);
            cur.org ||= smartCase(parts[1]);
            if (parts.length > 2) cur.place ||= smartCase(parts.slice(2).join(', ').replace(/,\s*$/, ''));
        } else if (dateFirst && rest.length < 40) cur.place ||= smartCase(rest); // "11/2023 – CURRENT Trento"
        else if (!cur.title) cur.title = smartCase(rest);
        else if (!cur.org) cur.org = smartCase(rest);
        else cur.place ||= rest;
    };
    let lineOpen = false;
    for (const l of lines) {
        const text = l.text.trim();
        lineOpen = /,\s*$/.test(text);
        const eqf = text.match(EQF_RE);
        if (eqf && cur && text.length < 60) {
            cur.eqf = +(eqf[1] || eqf[2]);
            // "EQF level 7   Grade: 110/110" on one line
            const g = text.match(/\b(?:final\s+grade|grade|gpa|voto(?:\s+di\s+laurea)?|votazione|note|calificaci[oó]n|nota|cijfer|ocena|not[aă]|betyg)\s*:\s*(.{1,40})$/i);
            if (g) cur.grade = g[1].trim();
            continue;
        }
        const grade = text.match(GRADE_RE);
        if (grade && cur) {
            cur.grade = grade[1].trim();
            continue;
        }
        const bare = text.match(/^((?:https?:\/\/)?(?:www\.)?[a-z0-9-]+(?:\.[a-z0-9-]+)*\.[a-z]{2,}(?:\/\S*)?)$/i);
        if (bare && cur && !l.bold && !EMAIL_RE.test(text)) {
            cur.url ||= /^https?:/i.test(bare[1]) ? bare[1] : 'https://' + bare[1]; // "notefox.eu" under a project
            continue;
        }
        const link = text.match(LINK_RE);
        if (link && cur) {
            cur.url = /^https?:/i.test(link[1]) ? link[1] : 'https://' + link[1];
            continue;
        }
        const d = findDates(text);
        if (d) {
            const dateFirst = text.search(RANGE_RE) === 0 || text.search(SINGLE_RE) === 0;
            if (d && cur && cur.text && cur.lastBig) {
                // "Title" then "dates": the title printed above the dates was taken as text of the entry before
                const title = cur.lastBig;
                cur.text = cur.text.split('\n').slice(0, -1).join('\n');
                push();
                cur = fresh({title: smartCase(title), start: d.start, end: d.end, current: d.current, hasDate: true, lines: 1});
                fillHeader(d.rest, false);
                continue;
            }
            if (cur && !cur.hasDate && !cur.text && cur.lines <= 2) {
                // a date that follows a title on its own line belongs to the same entry
                Object.assign(cur, {start: d.start, end: d.end, current: d.current, hasDate: true});
                fillHeader(d.rest, dateFirst);
                continue;
            }
            push();
            cur = fresh({start: d.start, end: d.end, current: d.current, hasDate: true});
            fillHeader(d.rest, dateFirst);
            cur.lines = cur.title ? 1 : 0;
            continue;
        }
        const bullet = BULLET_RE.test(text);
        const t = text.replace(BULLET_RE, '').trim();
        if (cur?.placeOpen && !cur.text && t.length < 30 && t === t.toUpperCase()) {
            cur.place = [cur.place, smartCase(t)].filter(Boolean).join(', ');
            cur.placeOpen = false;
            continue;
        }
        if (cur) cur.placeOpen = false;
        // a bold title after some text: a new entry (projects often have no dates)
        if (cur && (cur.text || cur.url) && looksTitle(l) && !cur.lastBig) {
            push();
            cur = fresh();
        }
        if (!cur) cur = fresh();
        if (!bullet && !cur.title) {
            if (l.boldText && l.boldText.length < t.length) {
                cur.title = smartCase(l.boldText); // "COMPUTER SCIENCE (L-31) University of Trento"
                cur.org ||= t.slice(l.boldText.length).trim();
            } else cur.title = smartCase(t);
        } else if (!bullet && !cur.org && t.length < 90 && !cur.text && !looksTitle(l) && !/[.!?]$/.test(t) && (cur.hasDate || l.size > body + 0.2)) cur.org = smartCase(t); // undated entries: a body-size line is the description
        else cur.text += (cur.text ? '\n' : '') + (bullet ? '- ' + t : t);
        cur.lastBig = !bullet && l.size > body + 0.4 && cur.text.endsWith(t) ? t : '';
        cur.lines++;
    }
    push();
    return items.map(({hasDate, lines, lastBig, placeOpen, ...i}) => ({...i, text: joinWrapped(i.text)}));
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

/** Labels of the Europass language block: the mother tongue(s) and the heading of the other languages. */
const NATIVE_LABEL_RE = /^(?:mother\s*tongues?|native\s*languages?|first\s*language|madrelingu[ae]|lingu[ae]\s*madr[ei]|langues?\s*maternelles?|muttersprachen?|lenguas?\s*maternas?|línguas?\s*maternas?|moedertalen|moedertaal|języki?\s*ojczyste?|j[ęe]zyk\s*ojczysty|limb[ai]\s*matern[ăe]|modersmål)\s*:?\s*(.*)$/i;
const OTHER_LABEL_RE = /^(?:other\s*languages?|foreign\s*languages?|altr[ae]\s*lingu[ae]|lingue\s*straniere|autres?\s*langues?|weitere\s*sprachen|fremdsprachen|otr[oa]s?\s*(?:idiomas|lenguas)|outr[oa]s?\s*(?:idiomas|línguas)|andere\s*talen|inne\s*języki|języki\s*obce|alte\s*limbi|andra\s*språk)\s*:?\s*(.*)$/i;
/** Headers and legend of the Europass level grid: never a language. */
const GRID_RE = /^(?:understanding|speaking|writing|listening|reading|spoken(?:\s*(?:production|interaction))?|production|interaction|comprensione|parlato|produzione\s*(?:scritta|orale)|interazione|ascolto|lettura|scritta|comprendre|parler|écrire|verstehen|sprechen|schreiben|comprensión|hablar|escribir|levels?\s*:|livelli\s*:|niveaux?\s*:|common\s*european|quadro\s*comune|cadre\s*européen|gemeinsamer\s*europäischer|marco\s*común|basic\s*user|independent\s*user|proficient\s*user|utente\s*(?:base|autonomo|avanzato))/i;
const CEFR_ROW_RE = /^(?:[ABC][12]\s*)+$/i;

function titleCase(s) {
    return s === s.toUpperCase() ? s.toLowerCase().replace(/(^|[\s\-])\p{L}/gu, (c) => c.toUpperCase()) : s;
}

/** "B2 B2 B1 B1 B2" (a Europass grid row): the level that appears most, the higher one on a tie. */
function cefrSummary(row) {
    const counts = {};
    for (const l of row.toUpperCase().match(/[ABC][12]/g)) counts[l] = (counts[l] || 0) + 1;
    return Object.keys(counts).sort((a, b) => counts[b] - counts[a] || b.localeCompare(a))[0];
}

/**
 * "Inglese: C1", "English (fluent)", the name and the level side by side on one row, and the Europass block
 * ("Mother tongue(s): ITALIAN", "Other language(s):", then each language with its grid of levels).
 */
function parseLangs(lines, lang) {
    const native = CV_WORDS[lang]?.native || 'Native';
    const out = [];
    let nativeMode = false; // after a mother-tongue label without a value: the next names are mother tongues
    // the levels of a grid row ("English  B2 B2 B1 B1 B2") come as separate pieces: one line again
    const rows = [];
    for (const l of lines) {
        const prev = rows[rows.length - 1];
        if (prev && prev.row === l.row && CEFR_ROW_RE.test(l.text.trim())) prev.text += ' ' + l.text.trim();
        else rows.push({...l});
    }
    for (const l of rows) {
        // "(s)" of "tongue(s)" / "language(s)" is not a level in brackets
        let text = l.text.replace(BULLET_RE, '').replace(/\((?:s|e|i|n|es)\)/gi, '').trim();
        // the grid and certificate lines of the Europass block are not languages ("Certificate: IELTS 7.5" is not a heading either)
        if (!text || GRID_RE.test(text) || /^(certificat|diploma)/i.test(text)) continue;
        const nat = text.match(NATIVE_LABEL_RE);
        if (nat) {
            const prev = out[out.length - 1];
            if (!nat[1] && prev && prev.row === l.row && !prev.level) {
                prev.level = native; // "Italiano   Madrelingua": the level of the language on its left, not a label
                continue;
            }
            nativeMode = !nat[1];
            for (const name of nat[1].split(/\s*[,;/]\s*|\s+(?:and|e|et|und|y|en|i|och)\s+/i).filter(Boolean)) out.push({name: titleCase(name.trim()), level: native, row: l.row});
            continue;
        }
        const other = text.match(OTHER_LABEL_RE);
        if (other) {
            nativeMode = false;
            if (!other[1]) continue;
            text = other[1];
        }
        const last = out[out.length - 1];
        if (CEFR_ROW_RE.test(text)) {
            if (last && (!last.level || last.level === '')) last.level = cefrSummary(text);
            continue;
        }
        for (const piece of text.split(/\s*[,;•|·]\s*/)) {
            const s = piece.trim();
            if (!s) continue;
            const prev = out[out.length - 1];
            const cefr = s.match(/^(\p{L}[\p{L}\s]*?)\s+((?:[ABC][12]\s*)+)$/iu); // "ENGLISH B2", "English B2 B2 B1"
            if (cefr) {
                out.push({name: titleCase(cefr[1].trim()), level: cefrSummary(cefr[2]), row: l.row});
                continue;
            }
            const m = s.match(/^([^:–\-()]+?)\s*(?:[:–\-]|\()\s*(.+?)\)?$/);
            if (m) out.push({name: titleCase(m[1].trim()), level: m[2].trim(), row: l.row});
            else if (LEVEL_RE.test(s) && prev && !prev.level) prev.level = s;
            else out.push({name: titleCase(s), level: nativeMode ? native : '', row: l.row});
        }
    }
    return out.filter((x) => x.name.length < 40 && /\p{L}{2}/u.test(x.name)).map(({row, ...x}) => x);
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
export async function importPdf(file, lang, debug = false) {
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
        if (!profile.phone) profile.phone = findPhone(l.text) || undefined;
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
    // the addresses behind the links of the PDF ("LinkedIn: saveriomorelli" points to linkedin.com/in/…)
    for (let url of pages.flatMap((p) => p.links)) {
        if (!/^https?:/i.test(url)) continue;
        try {
            // redirects (Europass: …/redirect-external?url=http://www.linkedin.com/in/…) point to the real address
            const inner = new URL(url).searchParams.get('url') || new URL(url).searchParams.get('u') || new URL(url).searchParams.get('q');
            if (inner && /^https?:\/\//i.test(inner)) url = inner;
        } catch (e) { /* not a valid URL */ }
        const host = url.replace(/^https?:\/\/(www\.)?/i, '').split('/')[0].toLowerCase();
        const label = host.includes('linkedin') ? 'LinkedIn' : host.includes('github') ? 'GitHub' : host.includes('gitlab') ? 'GitLab' : host.includes('behance') ? 'Behance' : host.includes('dribbble') ? 'Dribbble' : '';
        if (label && !links.some((x) => x.label === label)) links.push({label, url});
    }
    if (links.length) profile.links = links;

    // The name: the biggest text of the first page (whatever column it is in), without digits or @
    const isContact = (t) => EMAIL_RE.test(t) || !!findPhone(t) || URL_RE.test(t);
    const nameLine = lines.filter((l) => l.page === 0 && !/\d|@/.test(l.text) && l.text.length <= 50 && l.text.split(' ').length <= 5 && ['other', null].includes(headingKind(l))).sort((a, b) => b.size - a.size || a.y - b.y)[0];
    const used = new Set();
    let headline = '';
    if (nameLine) {
        const next = lines[lines.indexOf(nameLine) + 1];
        if (next && next.col === nameLine.col && next.text.length < 80 && !isContact(next.text) && !headingKind(next) && !findDates(next.text) && !/^\p{L}[\p{L} ]{1,30}:(\s|$)/u.test(next.text)) {
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
    // "Nationality: Italian", "Address: …" in the header (Europass writes the personal data as "Label: value")
    const headText = lines.filter((l) => l.page === 0).slice(0, 25).map((l) => l.text).join('  |  ');
    const LABELS = /(?<![\p{L}]\s?)(nationality|nazionalit[àa]|nationalit[ée]|nationalit[äa]t|nacionalidad|nacionalidade|nationaliteit|narodowo[śs][ćc]|na[țt]ionalitate|nationalitet|driving licen[cs]e|patente(?: di guida)?|permis de conduire|f[üu]hrerschein|address|indirizzo|residenza|adresse|direcci[óo]n|morada|adres|adres[ăa])\s*:\s*([^|]+?)(?=\s{2,}|\s*\||\s+\p{Lu}[\p{L} ]{2,25}:|$)/giu;
    for (const m of headText.matchAll(LABELS)) {
        const value = m[2].trim().replace(/[|;,]$/, '');
        if (!value || value.length > 120) continue;
        if (/^(address|indirizzo|residenza|adresse|direcci|morada|adres)/i.test(m[1])) content.location ||= value;
        else if (!content.fields.some((f) => f.label.toLowerCase() === m[1].toLowerCase())) content.fields.push({label: m[1].charAt(0).toUpperCase() + m[1].slice(1), value, show: true});
    }
    const labelled = (t) => /^\p{L}[\p{L} ]{1,30}:(\s|$)/u.test(t); // "Date of birth: …": personal data, not a headline
    const buckets = [];
    let current = {kind: 'top', title: '', lines: []};
    let prev = null;
    let headLine = null; // the line of the current heading
    // an unknown heading in capitals stands alone on its row ("ITALIANO" next to "Madrelingua" is a value)
    const perRow = {};
    for (const l of lines) perRow[l.row] = (perRow[l.row] || 0) + 1;
    // ...and looks like the headings we do recognise: bold or bigger than the text, not smaller than them
    const knownSizes = lines.filter((l) => !['other', null].includes(headingKind(l))).map((l) => l.size);
    const minKnown = knownSizes.length ? Math.min(...knownSizes) : 0;
    const bodyAll = bodySize(lines);
    const plausibleHeading = (l) => (l.bold || l.size > bodyAll + 0.4) && l.size >= minKnown - 0.3;
    for (const l of lines) {
        // moving to the other column ends the section (the sidebar does not continue into the main column)
        if (prev && l.page === prev.page && l.col !== prev.col && current.kind !== 'top') {
            buckets.push(current);
            current = {kind: 'top', title: '', lines: []};
        }
        prev = l;
        if (used.has(l)) continue;
        // a heading wrapped onto two lines ("ESPERIENZA" / "PROFESSIONALE" in a narrow title column): joined when
        // the second piece sits right under the first and together they make a known heading
        if (headLine && current.kind !== 'top' && Math.abs(l.x - headLine.x) < 3 && l.row - headLine.row <= 2 && !/\d/.test(l.text) && l.text.length < 30) {
            const joined = {...l, text: current.title + ' ' + l.text};
            const jk = headingKind(joined);
            if (jk && jk !== 'other') {
                current = {kind: jk, title: joined.text, lines: []};
                headLine = l;
                continue;
            }
        }
        let k = headingKind(l);
        if (k === 'other' && (perRow[l.row] > 1 || !plausibleHeading(l))) k = null;
        if (k) {
            buckets.push(current);
            current = {kind: k, title: l.text.replace(/:$/, ''), lines: []};
            headLine = l;
        } else current.lines.push(l);
    }
    buckets.push(current);

    // Lines outside any section: headline, maybe the summary
    const topText = buckets.filter((b) => b.kind === 'top').flatMap((b) => b.lines).filter((l) => !isContact(l.text) && !findDates(l.text) && !labelled(l.text));
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
        // contact lines are dropped from sections, but "Link https://…" belongs to the entry above it
        const ls = b.lines.filter(notFooter).filter((l) => !(b.kind !== 'contacts' && isContact(l.text) && l.text.length < 60 && !LINK_RE.test(l.text.trim()) && !l.bold && !(b.kind === 'projects' || b.kind === 'volunteering' || b.kind === 'other' || b.kind === 'certifications' || b.kind === 'publications') ));
        if (b.kind === 'top' || b.kind === 'contacts' || b.kind === 'references') continue;
        if (b.kind === 'summary') {
            content.summary = joinWrapped(ls.map((l) => (BULLET_RE.test(l.text) ? '- ' + l.text.replace(BULLET_RE, '') : l.text)).join('\n'));
            continue;
        }
        if (!ls.length) continue;
        const kind = b.kind === 'other' ? 'list' : b.kind;
        const type = SECTION_KINDS[kind]?.type || 'list';
        const title = b.title.charAt(0) + b.title.slice(1).toLowerCase();
        const sec = {id: 'imp' + (++id), type, kind, title: b.kind === 'other' ? title : SECTION_KINDS[kind][lang] || title, show: true, text: '', items: []}; // a custom language keeps the title as written
        if (type === 'timeline') sec.items = parseTimeline(ls);
        else if (type === 'skills') sec.items = splitTags(ls).map((name) => ({name, level: 0}));
        else if (type === 'langs') sec.items = parseLangs(ls, lang);
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
    return {profile, content, stats: {sections: content.sections.length}, lines: debug ? lines : undefined};
}
