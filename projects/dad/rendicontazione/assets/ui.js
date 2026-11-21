/**
 * Rendicontazione Ore: small toolkit. DOM building, dates and hours, dialogs, toasts.
 * Text always goes through text nodes, so what people type can never turn into markup.
 */
'use strict';

/** h('div', {class: 'x', onclick: fn}, child, 'text', [children]) */
function h(tag, props, ...children) {
    const node = document.createElement(tag);
    for (const [key, value] of Object.entries(props || {})) {
        if (value === null || value === undefined || value === false) continue;
        if (key.startsWith('on')) node.addEventListener(key.slice(2), value);
        else if (key === 'class') node.className = value;
        else if (key === 'style') node.style.cssText = value;
        else if (key === 'dataset') Object.assign(node.dataset, value);
        else if (key in node && key !== 'list') node[key] = value;
        else node.setAttribute(key, value === true ? '' : value);
    }
    node.append(...children.flat(Infinity).filter(c => c !== null && c !== undefined && c !== false));
    return node;
}

/** Replaces the content of a node; optional parts may be null. */
function fill(node, ...children) {
    node.replaceChildren(...children.flat(Infinity).filter(c => c !== null && c !== undefined && c !== false));
}

const ICONS = {
    week: '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4M7 15.5h10"/>',
    calendar: '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    chart: '<path d="M5 20v-7M12 20V5M19 20v-10"/>',
    history: '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3.5 2"/>',
    settings: '<path d="M4 7h10M18 7h2M4 17h2M10 17h10"/><circle cx="16" cy="7" r="2.4"/><circle cx="8" cy="17" r="2.4"/>',
    left: '<path d="m15 5-7 7 7 7"/>',
    right: '<path d="m9 5 7 7-7 7"/>',
    close: '<path d="M6 6l12 12M18 6 6 18"/>',
    check: '<path d="m5 12.5 4.5 4.5L19 7"/>',
    plus: '<path d="M12 5v14M5 12h14"/>',
    trash: '<path d="M4 7h16M9 7V4h6v3M6.5 7l1 13h9l1-13"/>',
    download: '<path d="M12 4v11M7 11l5 5 5-5M5 20h14"/>',
    upload: '<path d="M12 16V5M7 9l5-5 5 5M5 20h14"/>',
    print: '<path d="M7 9V4h10v5M7 17H4v-8h16v8h-3"/><rect x="7" y="14" width="10" height="6"/>',
    undo: '<path d="M9 7 4 12l5 5"/><path d="M4 12h11a5 5 0 0 1 0 10h-2"/>',
    pencil: '<path d="m4 20 1-4L16 5l3 3L8 19l-4 1Z"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21c1-5 4-7 8-7s7 2 8 7"/>',
    logout: '<path d="M10 4H5v16h5M15 8l4 4-4 4M19 12H9"/>',
    lavoro: '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V4h6v3M3 13h18"/>',
    pioggia: '<path d="M17 14H7a4 4 0 0 1-.6-7.95A5.5 5.5 0 0 1 17 5.6a4.2 4.2 0 0 1 0 8.4Z"/><path d="m8 17-1 4M13 17l-1 4M18 17l-1 4"/>',
    permesso: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
    ferie: '<circle cx="12" cy="12" r="4"/><path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M4.9 19.1l1.8-1.8M17.3 6.7l1.8-1.8"/>',
    malattia: '<path d="M9 3h6v6h6v6h-6v6H9v-6H3V9h6Z"/>',
};

function icon(name) {
    const span = h('span', { class: 'icon', 'aria-hidden': 'true' });
    span.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${ICONS[name]}</svg>`;
    return span;
}

/* ---- dates ---- */

const MONTHS = ['gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'];
const WEEKDAYS = ['domenica', 'lunedì', 'martedì', 'mercoledì', 'giovedì', 'venerdì', 'sabato'];

const pad = n => String(n).padStart(2, '0');
const isoDate = (y, m, d) => `${y}-${pad(m)}-${pad(d)}`;
const parseDate = iso => { const [y, m, d] = iso.split('-').map(Number); return new Date(y, m - 1, d, 12); };
const capitalize = s => s.charAt(0).toUpperCase() + s.slice(1);

function todayIso() {
    const now = new Date();
    return isoDate(now.getFullYear(), now.getMonth() + 1, now.getDate());
}

function shiftDate(iso, days) {
    const date = parseDate(iso);
    date.setDate(date.getDate() + days);
    return isoDate(date.getFullYear(), date.getMonth() + 1, date.getDate());
}

/** "venerdì 6 novembre" (with the year when asked) */
function dateLabel(iso, withYear) {
    const d = parseDate(iso);
    return `${WEEKDAYS[d.getDay()]} ${d.getDate()} ${MONTHS[d.getMonth()]}${withYear ? ' ' + d.getFullYear() : ''}`;
}

/** The Monday of the week that contains a date. */
const mondayOf = iso => shiftDate(iso, -((parseDate(iso).getDay() + 6) % 7));

/** "2 – 8 novembre 2026", "30 novembre – 6 dicembre 2026", "28 dicembre 2026 – 3 gennaio 2027" */
function weekLabel(start) {
    const a = parseDate(start), b = parseDate(shiftDate(start, 6));
    const end = `${b.getDate()} ${MONTHS[b.getMonth()]} ${b.getFullYear()}`;
    if (a.getFullYear() !== b.getFullYear()) return `${a.getDate()} ${MONTHS[a.getMonth()]} ${a.getFullYear()} – ${end}`;
    return a.getMonth() === b.getMonth() ? `${a.getDate()} – ${end}` : `${a.getDate()} ${MONTHS[a.getMonth()]} – ${end}`;
}

function timeAgoLabel(utc) {
    const when = new Date(utc.replace(' ', 'T') + 'Z');
    const time = `${pad(when.getHours())}:${pad(when.getMinutes())}`;
    const iso = isoDate(when.getFullYear(), when.getMonth() + 1, when.getDate());
    if (iso === todayIso()) return `oggi alle ${time}`;
    if (iso === shiftDate(todayIso(), -1)) return `ieri alle ${time}`;
    return `${when.getDate()} ${MONTHS[when.getMonth()]} ${when.getFullYear()} alle ${time}`;
}

/** Italian national holidays: their name, or null. */
function holidayName(iso) {
    const [y, m, d] = iso.split('-').map(Number);
    const fixed = {
        '1-1': 'Capodanno', '1-6': 'Epifania', '4-25': 'Liberazione', '5-1': 'Festa del lavoro', '6-2': 'Festa della Repubblica',
        '8-15': 'Ferragosto', '11-1': 'Ognissanti', '12-8': 'Immacolata', '12-25': 'Natale', '12-26': 'Santo Stefano',
    };
    if (fixed[`${m}-${d}`]) return fixed[`${m}-${d}`];

    // Easter Sunday, anonymous Gregorian algorithm
    const a = y % 19, b = Math.floor(y / 100), c = y % 100, e = b % 4, f = Math.floor((b + 8) / 25);
    const g = Math.floor((b - f + 1) / 3), hh = (19 * a + b - Math.floor(b / 4) - g + 15) % 30;
    const i = Math.floor(c / 4), k = c % 4, l = (32 + 2 * e + 2 * i - hh - k) % 7, n = Math.floor((a + 11 * hh + 22 * l) / 451);
    const month = Math.floor((hh + l - 7 * n + 114) / 31), day = ((hh + l - 7 * n + 114) % 31) + 1;
    const easter = isoDate(y, month, day);
    if (iso === easter) return 'Pasqua';
    if (iso === shiftDate(easter, 1)) return 'Pasquetta';
    return null;
}

/* ---- hours ---- */

/** 480 -> "8 h", 510 -> "8 h 30", 104700 -> "1.745 h" */
function fmtHours(minutes) {
    // Not toLocaleString: for Italian it can leave four-digit numbers without the dot
    const hours = String(Math.floor(minutes / 60)).replace(/\B(?=(\d{3})+$)/g, '.'), rest = minutes % 60;
    return rest ? `${hours} h ${pad(rest)}` : `${hours} h`;
}

const timeToMinutes = t => { const [hh, mm] = t.split(':').map(Number); return hh * 60 + mm; };

/** "07:00" -> "7", "11:30" -> "11:30" */
const shortTime = t => { const [hh, mm] = t.split(':'); return mm === '00' ? String(Number(hh)) : `${Number(hh)}:${mm}`; };

function slotsMinutes(slots) {
    let ordinary = 0, overtime = 0;
    for (const s of slots) {
        if (!s.start || !s.end) continue;
        const duration = timeToMinutes(s.end) - timeToMinutes(s.start);
        if (duration <= 0) continue;
        if (s.overtime) overtime += duration; else ordinary += duration;
    }
    return { ordinary, overtime, total: ordinary + overtime };
}

/** "7–12, 13–16" as text. */
const slotsText = slots => slots.map(s => `${shortTime(s.start)}–${shortTime(s.end)}${s.overtime ? ' (straord.)' : ''}`).join(', ');

/** The same with the overtime slots highlighted. */
function slotsNodes(slots) {
    const nodes = [];
    slots.forEach((s, i) => {
        if (i) nodes.push(', ');
        const text = `${shortTime(s.start)}–${shortTime(s.end)}`;
        nodes.push(s.overtime ? h('span', { class: 'ot', title: 'Straordinario' }, text + ' str.') : text);
    });
    return nodes;
}

/* ---- toasts ---- */

function toast(message, opts = {}) {
    const box = document.getElementById('toasts');
    const node = h('div', { class: 'toast' + (opts.error ? ' toast-error' : '') },
        h('span', null, message),
        opts.action ? h('button', { class: 'toast-action', type: 'button', onclick: () => { node.remove(); opts.action.run(); } }, opts.action.label) : null);
    box.append(node);
    // A modal covers everything outside it, so the messages must live inside the one on top
    const modals = document.querySelectorAll('dialog[open]');
    (modals.length ? modals[modals.length - 1] : document.body).append(box);
    setTimeout(() => node.remove(), opts.error ? 7000 : (opts.action ? 6000 : 3500));
}

/* ---- dialogs ---- */

const dialogStack = [];

/** To call before removing a modal: the messages on screen must survive it. */
function rescueToasts(dialog) {
    const box = document.getElementById('toasts');
    if (dialog.contains(box)) document.body.append(box);
}

/**
 * Opens a modal. The phone's back button closes it instead of leaving the page, which is why
 * every dialog owns one browser history entry. `guard` can stop the closing (unsaved changes).
 */
function openDialog(node, guard) {
    document.body.append(node);
    node.tabIndex = -1;
    node.showModal();
    node.focus();   // no control looks selected until the person chooses one
    history.pushState({ dialog: true }, '');
    const entry = { node, guard };
    dialogStack.push(entry);
    node.addEventListener('cancel', event => { event.preventDefault(); closeDialog(node); });
    return entry;
}

async function closeDialog(node, force) {
    const entry = dialogStack[dialogStack.length - 1];
    if (!entry || entry.node !== node) return;
    if (!force && entry.guard && !(await entry.guard())) return;
    entry.guard = null;
    return new Promise(resolve => {
        entry.onClosed = resolve;
        history.back();
    });
}

window.addEventListener('popstate', async () => {
    const entry = dialogStack[dialogStack.length - 1];
    if (!entry) return;
    if (entry.guard) {
        history.pushState({ dialog: true }, '');   // back pressed with unsaved changes: ask first
        if (await entry.guard()) { entry.guard = null; history.back(); }
        return;
    }
    dialogStack.pop();
    rescueToasts(entry.node);
    entry.node.close();
    entry.node.remove();
    if (entry.onClosed) entry.onClosed();
});

/** Yes/no question. Resolves to true or false. */
function ask({ title, text, yes, no = 'Annulla', danger }) {
    return new Promise(resolve => {
        const dialog = h('dialog', { class: 'ask' });
        const finish = value => {
            const modals = [...document.querySelectorAll('dialog[open]')].filter(d => d !== dialog);
            if (dialog.contains(document.getElementById('toasts'))) (modals.pop() || document.body).append(document.getElementById('toasts'));
            dialog.close();
            dialog.remove();
            resolve(value);
        };
        fill(dialog,
            h('h2', null, title),
            text ? h('p', null, text) : null,
            h('div', { class: 'ask-actions' },
                h('button', { class: 'btn', type: 'button', onclick: () => finish(false) }, no),
                h('button', { class: 'btn ' + (danger ? 'btn-danger' : 'btn-primary'), type: 'button', onclick: () => finish(true) }, yes)));
        dialog.addEventListener('cancel', event => { event.preventDefault(); finish(false); });
        document.body.append(dialog);
        dialog.showModal();
    });
}
