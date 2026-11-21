/**
 * OpenNewsletter — small UI toolkit shared by the app and the editor: DOM builder, SVG icons, API calls, toasts,
 * dialogs, dates in a group's timezone.
 */

import {t, getLang} from './i18n.js?v=18';

export const S = {config: {}, user: null, csrf: ''};

/** h('div.a.b#id', {attrs}, ...children) */
export function h(tag, attrs, ...kids) {
    const [, name, rest] = tag.match(/^([a-z0-9-]+)(.*)$/i);
    const el = document.createElement(name);
    for (const part of rest.match(/[.#][^.#]+/g) || []) part[0] === '.' ? el.classList.add(part.slice(1)) : (el.id = part.slice(1));
    if (attrs && (typeof attrs !== 'object' || attrs instanceof Node || Array.isArray(attrs))) {
        kids.unshift(attrs);
        attrs = null;
    }
    for (const [k, v] of Object.entries(attrs || {})) {
        if (v === undefined || v === null || v === false) continue;
        if (k.startsWith('on')) el.addEventListener(k.slice(2), v);
        else if (k === 'html') el.innerHTML = v;
        else if (k === 'value') el.value = v;
        else if (k === 'checked') el.checked = !!v;
        else if (k === 'style' && typeof v === 'object') Object.assign(el.style, v);
        else el.setAttribute(k, v === true ? '' : v);
    }
    for (const kid of kids.flat(Infinity)) if (kid !== null && kid !== undefined && kid !== false) el.append(kid instanceof Node ? kid : String(kid));
    return el;
}

export function put(parent, ...kids) {
    parent.replaceChildren(...kids.flat(Infinity).filter((k) => k !== null && k !== undefined && k !== false));
}

const ICON = {
    plus: '<path d="M12 5v14M5 12h14"/>', trash: '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>', up: '<path d="m6 15 6-6 6 6"/>', down: '<path d="m6 9 6 6 6-6"/>',
    back: '<path d="M15 5l-7 7 7 7"/>', x: '<path d="M6 6l12 12M18 6 6 18"/>', check: '<path d="m5 12 5 5 9-10"/>', copy: '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>',
    eye: '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>', send: '<path d="M21 3 10 14M21 3l-7 18-4-7-7-4z"/>',
    mail: '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m3.5 7 8.5 6 8.5-6"/>', users: '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.3c2.2.7 3.5 2.8 3.5 5.7"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/>', settings: '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
    layout: '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>', link: '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
    code: '<path d="m8 8-5 4 5 4M16 8l5 4-5 4M14 4l-4 16"/>', clock: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>', calendar: '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    shield: '<path d="M12 3 4 6v6c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V6z"/>', logout: '<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4M10 17l-5-5 5-5M5 12h11"/>', globe: '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/>',
    heading: '<path d="M6 4v16M18 4v16M6 12h12"/>', text: '<path d="M4 6h16M4 12h16M4 18h10"/>', image: '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 17-5-5-9 8"/>',
    button: '<rect x="3" y="8" width="18" height="8" rx="4"/><path d="M8 12h8"/>', divider: '<path d="M3 12h18"/><path d="M7 7h10M7 17h10" opacity=".35"/>', spacer: '<path d="M12 4v16M8 8l4-4 4 4M8 16l4 4 4-4"/>',
    quote: '<path d="M7 7h4v6a4 4 0 0 1-4 4M15 7h4v6a4 4 0 0 1-4 4"/>', columns: '<rect x="3" y="4" width="7.5" height="16" rx="1.5"/><rect x="13.5" y="4" width="7.5" height="16" rx="1.5"/>', star: '<path d="m12 3.5 2.6 5.3 5.8.8-4.2 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8-4.2-4.1 5.8-.8z"/>', media: '<rect x="3" y="5" width="8" height="14" rx="2"/><path d="M14 8h7M14 12h7M14 16h4"/>', card: '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 11h18M7 15h6M7 18h4"/>', social: '<circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="m8.2 10.8 7.6-3.6M8.2 13.2l7.6 3.6"/>',
    grip: '<circle cx="9" cy="6" r="1.4"/><circle cx="15" cy="6" r="1.4"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/><circle cx="9" cy="18" r="1.4"/><circle cx="15" cy="18" r="1.4"/>',
    bold: '<path d="M7 5h6a3.5 3.5 0 0 1 0 7H7zM7 12h7a3.5 3.5 0 0 1 0 7H7z"/>', italic: '<path d="M10 5h8M6 19h8M14 5l-4 14"/>', underline: '<path d="M7 4v7a5 5 0 0 0 10 0V4M5 20h14"/>', list: '<path d="M9 6h11M9 12h11M9 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01"/>',
    olist: '<path d="M10 6h10M10 12h10M10 18h10M4 5h1v4M4 9h2M4 14.5a1 1 0 0 1 2 0c0 1-2 1.5-2 3h2"/>', clear: '<path d="M5 5h11M10 5 7 19M4 19h7M15 14l5 5M20 14l-5 5"/>',
    alignL: '<path d="M4 6h16M4 10h10M4 14h16M4 18h10"/>', alignC: '<path d="M4 6h16M7 10h10M4 14h16M7 18h10"/>', alignR: '<path d="M4 6h16M10 10h10M4 14h16M10 18h10"/>',
    dup: '<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/>', upload: '<path d="M12 16V4M7 9l5-5 5 5M5 20h14"/>', download: '<path d="M12 4v12M7 11l5 5 5-5M5 20h14"/>',
    save: '<path d="M5 3h11l3 3v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M7 3v5h8V3M7 21v-7h10v7"/>', search: '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>', phone: '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>', desktop: '<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>',
    review: '<path d="M9 11l3 3 8-8"/><path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h9"/>', ban: '<circle cx="12" cy="12" r="9"/><path d="m5.7 5.7 12.6 12.6"/>', undo: '<path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/>',
    menu: '<path d="M4 6h16M4 12h16M4 18h16"/>', chev: '<path d="m9 6 6 6-6 6"/>', home: '<path d="M3 11 12 4l9 7M5 10v10h14V10"/>', palette: '<path d="M12 3a9 9 0 1 0 0 18c1 0 1.5-.7 1.5-1.5 0-.4-.2-.8-.4-1.1-.3-.3-.4-.7-.4-1.1 0-.8.7-1.5 1.5-1.5H16a5 5 0 0 0 5-5c0-4.4-4-7.8-9-7.8z"/><circle cx="7.5" cy="11" r="1"/><circle cx="10" cy="7" r="1"/><circle cx="15" cy="7" r="1"/>',
};

export function svg(name, cls = 'ico') {
    const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    s.setAttribute('viewBox', '0 0 24 24');
    s.setAttribute('class', cls);
    s.setAttribute('aria-hidden', 'true');
    s.innerHTML = ICON[name] || '';
    return s;
}

export function iconBtn(name, title, onclick, extra = '') {
    return h('button.btn.btn-ghost.btn-icon' + extra, {type: 'button', title, 'aria-label': title, onclick}, svg(name));
}

/** The OpenNewsletter mark (same drawing as icons/favicon.svg): an O ring and an N with airmail stripes. */
export function logo(size = 32) {
    const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    s.setAttribute('viewBox', '0 0 64 64');
    s.setAttribute('width', size);
    s.setAttribute('height', size);
    s.setAttribute('aria-hidden', 'true');
    s.innerHTML = LOGO_SVG;
    return s;
}

export const LOGO_SVG = '<defs><pattern id="onair" width="16" height="16" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><rect width="16" height="16" fill="#fff"/><rect width="4" height="16" fill="#D7262E"/><rect x="8" width="4" height="16" fill="#1F3A8A"/></pattern><clipPath id="onin"><circle cx="32" cy="32" r="23"/></clipPath></defs><rect width="64" height="64" rx="16" fill="#FFFDF8"/><path clip-path="url(#onin)" d="M15 58V6h8l19 34V6h7v52h-8L22 24v34z" fill="url(#onair)" stroke="#16264F" stroke-width="1.6" stroke-linejoin="round"/><circle cx="32" cy="32" r="25.5" fill="none" stroke="#16264F" stroke-width="5"/>';

// ---------------------------------------------------------------- API

export class ApiError extends Error {
    constructor(code, status, detail) {
        super(code);
        this.code = code;
        this.status = status;
        this.detail = detail;
    }
}

export async function api(route, body, query = '') {
    const opts = {method: body ? 'POST' : 'GET', credentials: 'same-origin', headers: {}};
    if (body) {
        opts.headers['Content-Type'] = 'application/json';
        if (S.csrf) opts.headers['X-CSRF-Token'] = S.csrf;
        opts.body = JSON.stringify(body);
    }
    let res, data;
    try {
        res = await fetch('api/?r=' + route + (query ? '&' + query : ''), opts);
        data = await res.json();
    } catch (e) {
        throw new ApiError(res ? 'server' : 'network', res ? res.status : 0);
    }
    if (!data.ok && data.error !== 'needs_signup') throw new ApiError(data.error || 'server', res.status, data.detail);
    return data;
}

export function errText(e) {
    const code = e && e.code ? e.code : 'server';
    const s = t('err_' + code);
    return s === 'err_' + code ? t('err_server') : s;
}

// ---------------------------------------------------------------- toasts and dialogs

export function toast(msg, kind = 'ok') {
    let box = document.querySelector('.toasts');
    if (!box) document.body.append(box = h('div.toasts', {role: 'status', 'aria-live': 'polite'}));
    const el = h('div.toast.toast-' + kind, svg(kind === 'ok' ? 'check' : 'x'), h('span', msg));
    box.append(el);
    setTimeout(() => el.classList.add('out'), 3200);
    setTimeout(() => el.remove(), 3600);
}

export function failToast(e) {
    toast(errText(e), 'err');
}

/** A modal dialog. content: nodes; actions: [{label, primary, danger, onclick(close) -> false keeps it open}] */
export function modal(title, content, actions = [], opts = {}) {
    const close = () => {
        overlay.classList.add('out');
        setTimeout(() => overlay.remove(), 160);
        document.removeEventListener('keydown', onKey);
        if (opts.onclose) opts.onclose();
    };
    const onKey = (ev) => { if (ev.key === 'Escape') close(); };
    const foot = actions.length ? h('div.modal-foot', actions.map((a) => {
        const b = h('button.btn' + (a.primary ? '.btn-primary' : a.danger ? '.btn-danger' : '.btn-soft'), {type: 'button'}, a.icon ? svg(a.icon) : null, a.label);
        b.addEventListener('click', async () => {
            if (!a.onclick) return close();
            b.disabled = true;
            try {
                const r = await a.onclick(close);
                if (r !== false) close();
            } finally {
                b.disabled = false;
            }
        });
        return b;
    })) : null;
    const dialog = h('div.modal' + (opts.wide ? '.modal-wide' : '') + (opts.full ? '.modal-full' : ''), {role: 'dialog', 'aria-modal': 'true', 'aria-label': title},
        h('div.modal-head', h('h2', title), iconBtn('x', t('close'), close)), h('div.modal-body', content), foot);
    const overlay = h('div.overlay', {onclick: (ev) => { if (ev.target === overlay && !opts.sticky) close(); }}, dialog);
    document.body.append(overlay);
    document.addEventListener('keydown', onKey);
    setTimeout(() => (dialog.querySelector('[autofocus], input, textarea, select') || dialog.querySelector('button')).focus(), 30);
    return {close, dialog};
}

export function confirmDialog(title, text, label, danger = false) {
    return new Promise((resolve) => {
        let done = false;
        modal(title, [h('p', text)], [
            {label: t('cancel')},
            {label, danger, primary: !danger, onclick: () => { done = true; resolve(true); }},
        ], {onclose: () => { if (!done) resolve(false); }});
    });
}

// ---------------------------------------------------------------- forms

export function field(label, input, hint) {
    return h('label.field', h('span.field-label', label), input, hint ? h('span.field-hint', hint) : null);
}

export function input(attrs = {}) {
    return h('input.input', attrs);
}

export function select(options, value, attrs = {}) {
    return h('select.input', attrs, options.map(([v, label]) => h('option', {value: v, selected: v === value ? true : null}, label)));
}

/** A read-only field with Copy (and, with openUrl, a button that opens the page in a new tab). */
export function copyBox(text, multiline = false, openUrl = '') {
    const box = multiline ? h('textarea.input.mono', {readonly: true, rows: 4}, text) : h('input.input.mono', {readonly: true, value: text});
    box.addEventListener('focus', () => box.select());
    const b = h('button.btn.btn-soft', {type: 'button', onclick: async () => {
        try {
            await navigator.clipboard.writeText(text);
        } catch (e) {
            box.select();
            document.execCommand('copy');
        }
        toast(t('copied'));
    }}, svg('copy'), t('copy'));
    const open = openUrl ? h('a.btn.btn-soft', {href: openUrl, target: '_blank', rel: 'noopener', title: t('open_page')}, svg('eye'), h('span.hide-sm', t('open_page'))) : null;
    return h('div.copy-box' + (multiline ? '.copy-multi' : ''), box, h('div.copy-btns', b, open));
}

// ---------------------------------------------------------------- dates

/** "16 Nov 2026, 18:30" in a timezone (the group's). */
export function fmtDate(iso, tz, withTime = true) {
    if (!iso) return '';
    const o = {day: 'numeric', month: 'short', year: 'numeric', timeZone: tz || undefined};
    if (withTime) Object.assign(o, {hour: '2-digit', minute: '2-digit', hour12: false});
    try {
        return new Intl.DateTimeFormat(getLang(), o).format(new Date(iso));
    } catch (e) {
        return new Date(iso).toLocaleString();
    }
}

/** Date ("2026-11-20") and time ("18:30") of a moment in a timezone. */
export function partsIn(iso, tz) {
    const f = new Intl.DateTimeFormat('en-CA', {timeZone: tz, year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false});
    const p = Object.fromEntries(f.formatToParts(new Date(iso)).map((x) => [x.type, x.value]));
    return {date: `${p.year}-${p.month}-${p.day}`, time: `${p.hour === '24' ? '00' : p.hour}:${p.minute}`};
}

export function relTime(iso) {
    const d = (new Date(iso).getTime() - Date.now()) / 1000;
    const rtf = new Intl.RelativeTimeFormat(getLang(), {numeric: 'auto'});
    const a = Math.abs(d);
    if (a < 3600) return rtf.format(Math.round(d / 60), 'minute');
    if (a < 86400) return rtf.format(Math.round(d / 3600), 'hour');
    return rtf.format(Math.round(d / 86400), 'day');
}

export function uid() {
    return Math.random().toString(36).slice(2, 10);
}

export function clone(o) {
    return JSON.parse(JSON.stringify(o));
}

export const ROLES = ['user', 'moderator', 'publisher', 'admin', 'owner'];
export const rank = (r) => ROLES.indexOf(r);
export const atLeast = (role, min) => rank(role) >= rank(min);

export const LANG_NAMES = {it: 'Italiano', en: 'English', fr: 'Français', de: 'Deutsch', es: 'Español', pt: 'Português', nl: 'Nederlands', pl: 'Polski', ro: 'Română', sv: 'Svenska',
    el: 'Ελληνικά', cs: 'Čeština', hu: 'Magyar', da: 'Dansk', fi: 'Suomi', hr: 'Hrvatski', sl: 'Slovenščina', sk: 'Slovenčina', bg: 'Български', ca: 'Català'};
