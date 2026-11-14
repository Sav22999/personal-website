/**
 * MyCurriculum — the app: accounts (email + password with email confirmation, or Google) and the CV editor.
 *
 * The editor keeps the whole CV in S.cv and saves it a moment after each change (cv.save). The form on the left
 * edits one CV language at a time; the preview on the right is the same HTML the public page shows, on an A4 sheet.
 */

import {t, getLang, setLang, UI_LANGS, CV_LANGS, LANG_NAMES, flagSrc, SECTION_KINDS, CONSENT, LANG_LEVELS, CV_WORDS} from './i18n.js';
import {buildModel, renderCV, TEMPLATES, ACCENTS, FONTS, emptyLang, langDots, langLabel, cvWords, fixContactSeparators} from './cv.js';
import {downloadPdf, pdfFileName} from './pdf.js';

const S = {config: {}, user: null, csrf: '', cv: null, lang: '', tab: 'content', preview: wantsPreview(), saveState: 'saved', collapsed: new Set()};
const app = document.getElementById('app');
const BASE = new URL('./', location.href).href.replace(/[?#].*$/, '');

// ---------------------------------------------------------------- tiny DOM helpers

/** h('div.a.b#id', {attrs}, ...children) */
function h(tag, attrs, ...kids) {
    const [, name, rest] = tag.match(/^([a-z0-9]+)(.*)$/i);
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
        else el.setAttribute(k, v === true ? '' : v);
    }
    for (const kid of kids.flat(Infinity)) if (kid !== null && kid !== undefined && kid !== false) el.append(kid instanceof Node ? kid : String(kid));
    return el;
}

/** Like parent.append(), but skips false, null and undefined (DOM append would write them as text). */
function add(parent, ...kids) {
    parent.append(...kids.flat().filter((k) => k !== false && k !== null && k !== undefined));
}

function put(parent, ...kids) {
    parent.replaceChildren(...kids.flat().filter(Boolean));
}

const ICON = {
    plus: '<path d="M12 5v14M5 12h14"/>', trash: '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>', up: '<path d="m6 15 6-6 6 6"/>', down: '<path d="m6 9 6 6 6-6"/>',
    eye: '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>', eyeOff: '<path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4M6.6 6.6C3.8 8.4 2 12 2 12s3.6 7 10 7a9.6 9.6 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2"/>',
    download: '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>', upload: '<path d="M12 20V9M7 14l5-5 5 5M5 4h14"/>', file: '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/>', link: '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
    copy: '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>', ext: '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
    globe: '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/>',
    layout: '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M9 9h12"/>', mail: '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    logout: '<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4M10 17l-5-5 5-5M5 12h11"/>', chev: '<path d="m6 9 6 6 6-6"/>', check: '<path d="m5 12 5 5 9-10"/>',
    lang: '<path d="M4 5h9M8.5 3v2M6 5c1 4 4 7 7 8M11 5c-1 4-4 7-7 8M13 21l4-9 4 9M14.5 18h5"/>', shared: '<path d="M4 8h14l-3.5-3.5M20 16H6l3.5 3.5"/>', x: '<path d="M6 6l12 12M18 6 6 18"/>',
    undo: '<path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/>', redo: '<path d="m15 14 5-5-5-5"/><path d="M20 9H9.5a5.5 5.5 0 0 0 0 11H13"/>',
    shareArrow: '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
    fold: '<path d="m7 20 5-5 5 5M7 4l5 5 5-5"/>', unfold: '<path d="m7 15 5 5 5-5M7 9l5-5 5 5"/>',
    clock: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>', grip: '<circle cx="9" cy="6" r="1.4"/><circle cx="15" cy="6" r="1.4"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/><circle cx="9" cy="18" r="1.4"/><circle cx="15" cy="18" r="1.4"/>', drag: '<path d="M9 6h.01M15 6h.01M9 12h.01M15 12h.01M9 18h.01M15 18h.01"/>',
};

function svg(name, cls = 'ico') {
    const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    s.setAttribute('viewBox', '0 0 24 24');
    s.setAttribute('class', cls);
    s.setAttribute('aria-hidden', 'true');
    s.innerHTML = ICON[name] || '';
    return s;
}

function iconBtn(name, title, onclick, extra = '') {
    return h('button.btn.btn-ghost.btn-icon' + extra, {type: 'button', title, 'aria-label': title, onclick}, svg(name));
}

/** The flag of a built-in language; a globe for a language added by the user. */
function flag(l) {
    const src = flagSrc(l);
    return src ? h('img.flag', {src, alt: '', width: 20, height: 15, loading: 'lazy'}) : h('span.flag.flag-globe', svg('globe'));
}

/** A row of language buttons with flags (a <select> cannot show images). */
function langPicker(langs, selected, onPick, label = (l) => [flag(l), langLabel(S.cv, l)]) {
    const box = h('div.lang-pick', {role: 'radiogroup'});
    const draw = () => put(box, langs.map((l) => h('button.chip' + (l === selected ? '.on' : ''), {type: 'button', role: 'radio', 'aria-checked': l === selected ? 'true' : 'false', onclick: () => {
        selected = l;
        draw();
        onPick(l);
    }}, label(l))));
    draw();
    return box;
}

/**
 * The CV preview is hidden by default, so the form gets the whole width; the choice is remembered on this device.
 * On a narrow screen the preview takes the place of the form, so it always starts hidden there.
 */
function wantsPreview() {
    if (matchMedia('(max-width: 960px)').matches) return false;
    try {
        return localStorage.getItem('mc_preview') === '1';
    } catch (e) {
        return false;
    }
}

function setPreview(on) {
    S.preview = on;
    try {
        if (!matchMedia('(max-width: 960px)').matches) localStorage.setItem('mc_preview', on ? '1' : '0');
    } catch (e) { /* storage blocked */ }
    R.editor.dataset.preview = on ? 'on' : 'off';
    put(R.viewBtn, ...previewBtnContent());
    R.viewBtn.setAttribute('aria-label', on ? t('preview_hide') : t('preview_show'));
    R.viewBtn.title = on ? t('preview_hide') : t('preview_show');
    R.viewBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
    if (on) drawPreview();
    if (matchMedia('(max-width: 960px)').matches) window.scrollTo(0, 0);
}

function previewBtnContent() {
    // on a phone only the icon (the bar also holds undo, redo and history); the name stays for screen readers
    return [svg(S.preview ? 'eyeOff' : 'eye'), h('span.hide-sm', S.preview ? t('preview_hide') : t('preview_show'))];
}

function clone(o) {
    return JSON.parse(JSON.stringify(o));
}

function uid() {
    return Math.random().toString(36).slice(2, 10);
}

// ---------------------------------------------------------------- API

class ApiError extends Error {}

async function api(route, body, query = '') {
    const opts = {method: body ? 'POST' : 'GET', credentials: 'same-origin', headers: {}};
    if (body) {
        opts.headers['Content-Type'] = 'application/json';
        if (S.csrf) opts.headers['X-CSRF-Token'] = S.csrf;
        opts.body = JSON.stringify(body);
    }
    let res;
    try {
        res = await fetch('api/?r=' + route + query, opts);
    } catch (e) {
        throw new ApiError('network');
    }
    const d = await res.json().catch(() => ({ok: false, error: 'server'}));
    if (d.ok === false && d.error !== 'needs_signup') throw new ApiError(d.error || 'server');
    return d;
}

function errText(e) {
    const code = e instanceof ApiError ? e.message : 'generic';
    const s = t('err_' + code);
    return s === 'err_' + code ? t('err_generic') : s;
}

// ---------------------------------------------------------------- toasts and modals

function toast(text, {bad = false, action = null, ms = 4500} = {}) {
    let box = document.querySelector('.toasts');
    if (!box) document.body.append(box = h('div.toasts', {'aria-live': 'polite'}));
    const el = h('div.toast' + (bad ? '.bad' : ''), h('span', text), action && h('button', {type: 'button', onclick: () => {
        action.run();
        el.remove();
    }}, action.label));
    box.append(el);
    setTimeout(() => el.remove(), action ? Math.max(ms, 9000) : ms);
}

function modal(title, body, actions = []) {
    const back = h('div.modal-back', {role: 'dialog', 'aria-modal': 'true', 'aria-label': title});
    const close = () => {
        back.remove();
        document.removeEventListener('keydown', onKey);
    };
    const onKey = (e) => e.key === 'Escape' && close();
    document.addEventListener('keydown', onKey);
    back.addEventListener('mousedown', (e) => e.target === back && close());
    const box = h('div.modal', h('h2', title), body, actions.length ? h('div.modal-actions', actions.map((a) => h('button.btn' + (a.primary ? '.btn-primary' : '') + (a.danger ? '.btn-danger' : ''), {type: a.submit ? 'submit' : 'button', form: a.form, onclick: a.submit ? undefined : () => (a.run ? a.run(close) : close())}, a.label))) : null);
    back.append(box);
    document.body.append(back);
    setTimeout(() => box.querySelector('input:not([type=checkbox]), select, textarea, button.btn-primary')?.focus(), 30);
    return close;
}

function confirmBox(text, label, run, danger = true) {
    modal(t('confirm'), h('p', text), [{label: t('cancel')}, {label, primary: !danger, danger, run: (close) => {
        close();
        run();
    }}]);
}

// ---------------------------------------------------------------- start

// ---------------------------------------------------------------- web app and Android app

/** Works offline as an installed web app (sw.js caches the shell; the CV always comes from the network). */
if ('serviceWorker' in navigator && window.isSecureContext) navigator.serviceWorker.register('sw.js').catch(() => null);

/**
 * Inside the Android app (android/, a WebView) window.MyCurriculumAndroid is injected: Google sign-in goes through the
 * system account picker (Google does not allow it inside a WebView), PDFs are saved and links shared natively.
 * The app answers through window.MyCurriculumNative.result(kind, value, error).
 */
const native = window.MyCurriculumAndroid || null;
const nativeWait = {};
window.MyCurriculumNative = {
    result(kind, value, error) {
        const w = nativeWait[kind];
        delete nativeWait[kind];
        if (w) (error ? w.reject(new Error(error)) : w.resolve(value));
    },
};

function nativeCall(kind, fn) {
    return new Promise((resolve, reject) => {
        nativeWait[kind] = {resolve, reject};
        try {
            fn();
        } catch (e) {
            delete nativeWait[kind];
            reject(e);
        }
    });
}

/** Shares the link of the public CV: the Android share sheet, the browser's share sheet, or nothing (copy instead). */
function canShare() {
    return !!(native?.share || navigator.share);
}

async function shareLink(url, title) {
    if (native?.share) return native.share(title, url);
    try {
        await navigator.share({title, url});
    } catch (e) { /* closed by the user */ }
}

async function boot() {
    document.documentElement.lang = getLang();
    try {
        S.config = await api('auth.config');
    } catch (e) {
        S.config = {installed: false};
    }
    if (S.config.installed === false) return put(app, h('div.center-card', h('p', t('err_not_installed'))));
    const verify = new URLSearchParams(location.search).get('verify');
    try {
        const d = await api('auth.me');
        S.user = d.user;
        S.csrf = d.csrf || '';
    } catch (e) {
        S.user = null;
    }
    if (verify && /^[a-f0-9]{64}$/.test(verify)) return verifyLinkScreen(verify);
    route();
}

function route() {
    if (!S.user) return landing();
    if (S.user.mustVerify) return verifyScreen();
    return openEditor();
}

function langSwitch(onChange) {
    return h('div.ui-lang', {role: 'group', 'aria-label': 'Language'}, UI_LANGS.map((l) => h('button' + (l === getLang() ? '.on' : ''), {type: 'button', 'aria-pressed': l === getLang() ? 'true' : 'false', onclick: () => {
        setLang(l);
        onChange();
    }}, l.toUpperCase())));
}

function brand() {
    return h('a.brand', {href: './'}, h('img', {src: 'icons/favicon.svg', alt: ''}), h('span', h('b', 'My'), 'Curriculum'));
}

function credit() {
    return h('footer.site-foot', h('a', {href: 'privacy.html'}, t('privacy')), ' · ',
        h('a.credit', {href: 'https://www.saveriomorelli.com/', target: '_blank', rel: 'noopener'}, h('span', t('made_by')), h('span.credit-name', 'Saverio Morelli')));
}

// ---------------------------------------------------------------- landing and accounts

function landing(mode = 'signin') {
    const feat = (ico, title, body) => h('div.feature', h('div.feature-ico', svg(ico)), h('div', h('b', title), h('span', body)));
    const hero = h('section.hero',
        h('h1', {html: getLang() === 'it' ? 'Il tuo curriculum, <em>in ogni lingua</em> che parli' : 'Your CV, <em>in every language</em> you speak'}),
        h('p', t('hero_body')),
        h('div.features', feat('lang', t('feat_langs'), t('feat_langs_b')), feat('layout', t('feat_tpl'), t('feat_tpl_b')), feat('globe', t('feat_web'), t('feat_web_b')), feat('file', t('feat_import'), t('feat_import_b'))));
    put(app, h('div.landing',
        h('header.landing-top', brand(), langSwitch(() => landing(mode))),
        h('main.landing-main', hero, authCard(mode)),
        credit()));
}

function authCard(mode) {
    const google = h('div.google-slot');
    const msg = h('p.msg', {role: 'alert'});
    const card = h('div.auth-card');
    let form;
    if (mode === 'signin') {
        form = h('form', {onsubmit: async (e) => {
            e.preventDefault();
            const f = new FormData(form);
            busy(form, true);
            try {
                const d = await api('auth.login', {email: f.get('email'), password: f.get('password')});
                signedIn(d);
            } catch (err) {
                msg.textContent = errText(err);
                busy(form, false);
            }
        }},
        h('label.field', h('span', t('email')), h('input', {type: 'email', name: 'email', required: true, autocomplete: 'email'})),
        h('label.field', h('span', t('password')), h('input', {type: 'password', name: 'password', required: true, autocomplete: 'current-password'})),
        msg, h('button.btn.btn-primary.btn-block', {type: 'submit'}, t('signin')));
    } else {
        const nameIn = h('input', {name: 'name', required: true, autocomplete: 'name', maxlength: 80});
        const userIn = h('input', {name: 'username', required: true, pattern: '[a-z0-9][a-z0-9\\-]{1,28}[a-z0-9]', maxlength: 30, autocapitalize: 'off', spellcheck: 'false'});
        let touched = false;
        userIn.addEventListener('input', () => {
            touched = true;
            userIn.value = userIn.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
        });
        nameIn.addEventListener('input', () => {
            if (!touched) userIn.value = slugify(nameIn.value);
        });
        form = h('form', {onsubmit: async (e) => {
            e.preventDefault();
            const f = new FormData(form);
            busy(form, true);
            try {
                await api('auth.register', {name: f.get('name'), email: f.get('email'), password: f.get('password'), username: f.get('username'), acceptPrivacy: !!f.get('privacy'), lang: getLang(), cvLang: getLang()});
                checkEmailScreen(String(f.get('email')));
            } catch (err) {
                msg.textContent = errText(err);
                busy(form, false);
            }
        }},
        h('label.field', h('span', t('name')), nameIn),
        h('label.field', h('span', t('email')), h('input', {type: 'email', name: 'email', required: true, autocomplete: 'email'})),
        h('label.field', h('span', t('password')), h('input', {type: 'password', name: 'password', required: true, minlength: 8, autocomplete: 'new-password'})),
        h('label.field', h('span', t('username')), h('div.username-field', h('span', '?user='), userIn), h('small', t('username_hint'))),
        h('label.check', h('input', {type: 'checkbox', name: 'privacy', required: true}), h('span', {html: t('accept_privacy')})),
        msg, h('button.btn.btn-primary.btn-block', {type: 'submit'}, t('signup')));
    }
    put(card,
        h('h2', mode === 'signin' ? t('signin') : t('signup')),
        h('p.note', t('tagline')),
        S.config.googleClientId ? [google, h('div.or', t('or'))] : h('div', {style: 'height:14px'}),
        form,
        h('p.auth-switch', mode === 'signin' ? t('no_account') : t('have_account'), ' ', h('button', {type: 'button', onclick: () => landing(mode === 'signin' ? 'signup' : 'signin')}, mode === 'signin' ? t('signup') : t('signin'))));
    if (S.config.googleClientId) mountGoogle(google);
    return card;
}

function slugify(s) {
    return s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 30);
}

function busy(form, on) {
    for (const b of form.querySelectorAll('button[type=submit]')) {
        b.disabled = on;
        if (on) {
            b.dataset.label = b.textContent;
            b.replaceChildren(h('span.spinner'));
        } else if (b.dataset.label) b.textContent = b.dataset.label;
    }
}

const G_LOGO = '<svg viewBox="0 0 48 48" width="20" height="20" aria-hidden="true"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.6l6.8-6.8C35.8 2.4 30.3 0 24 0 14.6 0 6.6 5.4 2.6 13.3l7.9 6.1C12.4 13.7 17.7 9.5 24 9.5z"/><path fill="#4285F4" d="M46.1 24.5c0-1.6-.1-3.1-.4-4.5H24v9h12.4c-.5 2.9-2.2 5.3-4.6 6.9l7.4 5.8c4.3-4 6.9-9.9 6.9-17.2z"/><path fill="#FBBC05" d="M10.5 28.6c-.5-1.4-.8-2.9-.8-4.6s.3-3.2.8-4.6l-7.9-6.1C1 16.6 0 20.2 0 24s1 7.4 2.6 10.7l7.9-6.1z"/><path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.9-5.8l-7.4-5.8c-2.1 1.4-4.8 2.3-8.5 2.3-6.3 0-11.6-4.2-13.5-10l-7.9 6.1C6.6 42.6 14.6 48 24 48z"/></svg>';

function mountGoogle(slot) {
    if (native?.googleSignIn) {
        // Android app: the system account picker gives the same ID token the website gets
        put(slot, h('button.btn.btn-block.google-native', {type: 'button', onclick: async () => {
            try {
                googleSignIn(await nativeCall('google', () => native.googleSignIn(S.config.googleClientId)));
            } catch (e) {
                if (e.message !== 'cancelled') toast(t('err_google_token'), {bad: true});
            }
        }}, h('span', {html: G_LOGO}), t('google_continue')));
        return;
    }
    const init = () => {
        window.google.accounts.id.initialize({client_id: S.config.googleClientId, callback: (r) => googleSignIn(r.credential)});
        window.google.accounts.id.renderButton(slot, {theme: 'outline', size: 'large', shape: 'pill', text: 'continue_with', width: 300, locale: getLang()});
    };
    if (window.google?.accounts?.id) return init();
    const s = document.createElement('script');
    s.src = 'https://accounts.google.com/gsi/client';
    s.async = true;
    s.onload = init;
    document.head.append(s);
}

async function googleSignIn(credential) {
    try {
        const d = await api('auth.google', {credential, lang: getLang(), cvLang: getLang()});
        if (d.error === 'needs_signup') return googleFinish(credential, d.suggest || '');
        signedIn(d);
    } catch (e) {
        toast(errText(e), {bad: true});
    }
}

function googleFinish(credential, suggest) {
    const msg = h('p.msg');
    const userIn = h('input', {name: 'username', required: true, value: suggest, maxlength: 30, autocapitalize: 'off'});
    userIn.addEventListener('input', () => (userIn.value = userIn.value.toLowerCase().replace(/[^a-z0-9-]/g, '')));
    const form = h('form.stack', {id: 'gform', onsubmit: async (e) => {
        e.preventDefault();
        const f = new FormData(form);
        try {
            const d = await api('auth.google', {credential, username: f.get('username'), acceptPrivacy: !!f.get('privacy'), lang: getLang(), cvLang: getLang()});
            close();
            signedIn(d);
        } catch (err) {
            msg.textContent = errText(err);
        }
    }},
    h('p.note', t('google_finish_body')),
    h('label.field', h('span', t('username')), h('div.username-field', h('span', '?user='), userIn)),
    h('label.check', h('input', {type: 'checkbox', name: 'privacy', required: true}), h('span', {html: t('accept_privacy')})), msg);
    const close = modal(t('google_finish'), form, [{label: t('cancel')}, {label: t('continue'), primary: true, submit: true, form: 'gform'}]);
}

function signedIn(d) {
    S.user = d.user;
    S.csrf = d.csrf || S.csrf;
    history.replaceState(null, '', location.pathname);
    route();
}

function centerCard(ico, title, ...body) {
    put(app, h('div', h('header.landing-top', brand(), langSwitch(route)), h('div.center-card', h('div.big-ico', svg(ico)), h('h1', {style: 'font-size:26px;margin:0 0 10px'}, title), ...body)), credit());
}

function checkEmailScreen(email) {
    centerCard('mail', t('check_email_title'), h('p', t('check_email_body', {email})), !S.config.mailReady && h('p.note.bad', t('verify_nomail')),
        h('p', h('button.btn', {type: 'button', onclick: () => landing('signin')}, t('signin'))));
}

function verifyScreen() {
    const msg = h('p.msg');
    centerCard('mail', t('verify_title'),
        h('p', {html: t('verify_body', {email: S.user.email.replace(/[<>&"]/g, '')})}),
        !S.config.mailReady && h('p.note.bad', t('verify_nomail')),
        h('p.row', {style: 'justify-content:center'},
            h('button.btn.btn-primary', {type: 'button', onclick: async () => {
                try {
                    await api('auth.resend', {lang: getLang()});
                    msg.className = 'msg ok';
                    msg.textContent = t('verify_sent');
                } catch (e) {
                    msg.className = 'msg';
                    msg.textContent = errText(e);
                }
            }}, t('verify_resend')),
            h('button.btn', {type: 'button', onclick: logout}, t('logout'))), msg);
}

function verifyLinkScreen(token) {
    const msg = h('p.msg');
    const btn = h('button.btn.btn-primary', {type: 'button', onclick: async () => {
        btn.disabled = true;
        try {
            signedIn(await api('auth.verify', {token}));
        } catch (e) {
            msg.textContent = errText(e);
            btn.disabled = false;
        }
    }}, t('verify_link_btn'));
    centerCard('check', t('verify_link_title'), h('p', t('verify_link_body')), h('p', btn), msg);
}

async function logout() {
    await flushSave();
    try {
        await api('auth.logout', {});
    } catch (e) { /* signed out anyway */ }
    S.user = null;
    S.cv = null;
    S.csrf = '';
    landing();
}

// ---------------------------------------------------------------- editor

async function openEditor() {
    put(app, h('div.center-card', h('span.spinner')));
    try {
        const d = await api('cv.get');
        S.cv = d.cv;
    } catch (e) {
        if (e.message === 'unauthenticated') {
            S.user = null;
            return landing();
        }
        return put(app, h('div.center-card', h('p', errText(e)), h('button.btn', {onclick: openEditor}, 'Retry')));
    }
    for (const l of S.cv.settings.langs) if (!S.cv.content[l]) S.cv.content[l] = emptyLang();
    if (Array.isArray(S.cv.settings.customLangs) || !S.cv.settings.customLangs) S.cv.settings.customLangs = {}; // PHP sends [] when empty
    S.migrated = upgradeProjects(S.cv);
    S.lang = S.cv.settings.langs.includes(S.lang) ? S.lang : S.cv.settings.defaultLang;
    drawEditor();
    resetHistory();
    if (S.migrated) changed(true, t('h_upgrade')); // saved once in the new form (a one-off update, not an undoable step)
}

/**
 * Projects and volunteering used to be simple lists with one "date": they are timelines now, with from, to and
 * ongoing. Old sections are converted when the CV opens ("2020 – 2022" is split, "2021 – present" becomes ongoing,
 * the subtitle becomes the second line). Returns true when something changed (then the CV is saved once).
 */
function upgradeProjects(cv) {
    const PRESENT = /^(present|presente|current|ongoing|in corso|oggi|today|now|heute|actualidad|aujourd.hui|heden|obecnie|prezent|nu)$/i;
    let changed = false;
    for (const c of Object.values(cv.content || {})) {
        for (const s of c.sections || []) {
            if (s.type !== 'list' || (s.kind !== 'projects' && s.kind !== 'volunteering')) continue;
            s.type = 'timeline';
            s.items = (s.items || []).map((i) => {
                const parts = String(i.date || '').split(/\s+[–—-]\s+|\s*[–—]\s*/).map((x) => x.trim()).filter(Boolean);
                const current = parts.length > 1 && PRESENT.test(parts[1]);
                return {
                    title: i.title || '', org: i.subtitle || '', place: '', text: i.text || '', url: i.url || '', eqf: 0, grade: '',
                    start: parts.length > 1 ? parts[0] : '', end: parts.length > 1 ? (current ? '' : parts[1]) : parts[0] || '', current,
                };
            });
            changed = true;
        }
    }
    return changed;
}

const R = {}; // the parts of the editor that get redrawn

function drawEditor() {
    R.save = h('span.save-state', h('i'), h('em', ''));
    R.langbar = h('div.langbar');
    R.panel = h('div.panel');
    R.preview = h('div.preview');
    R.editor = h('div.editor', {'data-preview': S.preview ? 'on' : 'off'}, R.panel, R.preview);
    // the outline follows the reading position; a scroll by the user ends the lock set by a click on the outline
    // (scroll events come at most once per frame; the update only reads a few positions)
    R.panel.addEventListener('scroll', updateOutline, {passive: true});
    for (const ev of ['wheel', 'touchstart', 'keydown', 'pointerdown']) R.panel.addEventListener(ev, (e) => {
        if (R.outlineLock && !e.target.closest?.('.outline')) R.outlineLock = null;
    }, {passive: true});
    R.viewBtn = h('button.btn.btn-sm', {type: 'button', 'aria-pressed': S.preview ? 'true' : 'false', 'aria-label': S.preview ? t('preview_hide') : t('preview_show'), title: S.preview ? t('preview_hide') : t('preview_show'), onclick: () => setPreview(!S.preview)}, previewBtnContent());
    R.undoBtn = h('button.btn.btn-sm.btn-icon', {type: 'button', 'aria-label': t('undo'), onclick: undo}, svg('undo'));
    R.redoBtn = h('button.btn.btn-sm.btn-icon', {type: 'button', 'aria-label': t('redo'), onclick: redo}, svg('redo'));
    put(app, h('header.topbar', brand(), h('span.spacer'), R.save, h('div.history-tools', R.undoBtn, R.redoBtn, historyMenu()), R.viewBtn, langSwitch(drawEditor), accountMenu()), R.langbar, R.editor);
    setSaveState(S.saveState);
    drawHistoryButtons();
    drawLangbar();
    drawPanel();
    drawPreview();
}

function accountMenu() {
    const wrap = h('div.menu-wrap');
    const btn = h('button.btn.btn-sm', {type: 'button', 'aria-haspopup': 'true', 'aria-expanded': 'false', onclick: (e) => {
        e.stopPropagation();
        const open = wrap.querySelector('.menu');
        if (open) return closeMenu();
        btn.setAttribute('aria-expanded', 'true');
        wrap.append(h('div.menu', {role: 'menu'},
            h('div.menu-info', S.user.email),
            h('button', {type: 'button', role: 'menuitem', onclick: () => {
                closeMenu();
                S.tab = 'share';
                drawPanel();
                if (S.preview && matchMedia('(max-width: 960px)').matches) setPreview(false);
            }}, svg('user'), t('account')),
            h('button', {type: 'button', role: 'menuitem', onclick: logout}, svg('logout'), t('logout'))));
        setTimeout(() => document.addEventListener('click', closeMenu, {once: true}));
    }}, svg('user'), h('span.hide-sm', S.user.name.split(' ')[0]));
    const closeMenu = () => {
        wrap.querySelector('.menu')?.remove();
        btn.setAttribute('aria-expanded', 'false');
    };
    wrap.append(btn);
    return wrap;
}

function drawLangbar() {
    const langs = S.cv.settings.langs;
    put(R.langbar,
        h('span.langbar-label.hide-sm', t('cv_lang')),
        // each language: a button that opens it and, when the CV has more than one, a small × that removes it
        h('div.lang-chips', langs.map((l) => h('span.chip-group' + (l === S.lang ? '.on' : ''),
            h('button.chip', {type: 'button', 'aria-pressed': l === S.lang ? 'true' : 'false', onclick: () => {
                S.lang = l;
                drawLangbar();
                drawPanel();
                drawPreview();
            }}, flag(l), langLabel(S.cv, l)),
            langs.length > 1 && h('button.chip-x', {type: 'button', title: t('remove_lang') + ' (' + langLabel(S.cv, l) + ')', 'aria-label': t('remove_lang') + ' (' + langLabel(S.cv, l) + ')', onclick: () => removeLanguage(l)}, svg('x')))),
        langs.length < CV_LANGS.length && h('button.chip.chip-add', {type: 'button', onclick: addLanguage, title: t('add_lang'), 'aria-label': t('add_lang')}, svg('plus'), h('span.hide-sm', t('add_lang')))),
        h('button.btn.btn-sm', {type: 'button', onclick: importDialog, title: t('import_pdf'), 'aria-label': t('import_pdf')}, svg('upload'), h('span.hide-sm', t('import_pdf'))),
        h('button.btn.btn-sm.btn-primary', {type: 'button', onclick: () => makePdf(S.lang), 'aria-label': t('pdf')}, svg('download'), h('span.hide-sm', t('pdf')), h('span.show-sm', 'PDF')));
}

function addLanguage() {
    const free = CV_LANGS.filter((l) => !S.cv.settings.langs.includes(l));
    const OTHER = '__other';
    let picked = free[0] || OTHER;
    // a language that is not in the list: its name, its ISO code (it formats the dates) and, later, its CV words
    const nameIn = h('input', {maxlength: 40, placeholder: 'Hrvatski', autocomplete: 'off'});
    const codeIn = h('input', {maxlength: 10, placeholder: 'hr', autocapitalize: 'off', spellcheck: 'false', autocomplete: 'off'});
    codeIn.addEventListener('input', () => (codeIn.value = codeIn.value.toLowerCase().replace(/[^a-z-]/g, '')));
    const msg = h('p.msg');
    const custom = h('div.stack.custom-lang', {hidden: picked !== OTHER},
        h('div.grid2', h('label.field', h('span', t('custom_lang_name')), nameIn), h('label.field', h('span', t('custom_lang_code')), codeIn)),
        h('p.note', {html: t('custom_lang_hint')}), h('p.note', t('custom_lang_pdf')));
    const sel = langPicker([...free, OTHER], picked, (l) => {
        picked = l;
        custom.hidden = l !== OTHER;
        if (l === OTHER) nameIn.focus();
    }, (l) => (l === OTHER ? [h('span.flag.flag-globe', svg('globe')), t('custom_lang')] : [flag(l), langLabel(S.cv, l)]));
    const from = h('select', S.cv.settings.langs.map((l) => h('option', {value: l, selected: l === S.lang}, langLabel(S.cv, l))), h('option', {value: ''}, t('add_lang_empty')));
    modal(t('add_lang'), h('div.stack', h('div.field', h('span', t('cv_lang')), sel), custom, h('label.field', h('span', t('add_lang_from')), from), h('p.note', t('lang_copy_hint')), msg), [
        {label: t('cancel')},
        {label: t('add_lang'), primary: true, run: (close) => {
            let l = picked;
            if (l === OTHER) {
                l = codeIn.value.trim();
                const name = nameIn.value.trim();
                if (!name) return (msg.textContent = t('custom_lang_need_name'));
                if (!/^[a-z]{2,3}(-[a-z0-9]{2,8})?$/.test(l)) return (msg.textContent = t('custom_lang_bad_code'));
                if (S.cv.settings.langs.includes(l) || CV_LANGS.includes(l)) return (msg.textContent = t('custom_lang_exists'));
                if (Array.isArray(S.cv.settings.customLangs) || !S.cv.settings.customLangs) S.cv.settings.customLangs = {};
                S.cv.settings.customLangs[l] = {name, words: {}};
            }
            S.cv.content[l] = from.value ? translateCopy(S.cv.content[from.value], from.value, l) : freshLang(l);
            S.cv.settings.langs.push(l);
            S.lang = l;
            close();
            changed(true, t('h_add_lang', {l: langLabel(S.cv, l)}));
            drawLangbar();
        }},
    ]);
}

/** A copy of one language for another: titles and the consent that were never changed are translated. */
function translateCopy(src, from, to) {
    const c = clone(src || emptyLang());
    for (const s of c.sections) {
        const kind = SECTION_KINDS[s.kind];
        if (kind && kind[to] && (!s.title || s.title === kind[from])) s.title = kind[to];
        s.id = s.id || uid();
    }
    if (c.footer && c.footer === CONSENT[from] && CONSENT[to]) c.footer = CONSENT[to];
    if (from !== to && CV_WORDS[to]) {
        const native = CV_WORDS[from]?.native;
        for (const s of c.sections) if (s.type === 'langs') for (const i of s.items) if (i.level === native) i.level = CV_WORDS[to].native;
    }
    return c;
}

function freshLang(l) {
    const c = emptyLang();
    c.sections = ['experience', 'education', 'skills', 'languages'].map((k) => ({id: uid(), type: SECTION_KINDS[k].type, kind: k, title: SECTION_KINDS[k][l] || SECTION_KINDS[k].en, show: true, text: '', items: []}));
    return c;
}

function removeLanguage(l) {
    confirmBox(t('remove_lang_confirm', {lang: langLabel(S.cv, l)}), t('delete'), () => {
        delete S.cv.content[l];
        S.cv.settings.langs = S.cv.settings.langs.filter((x) => x !== l);
        if (S.cv.settings.defaultLang === l) S.cv.settings.defaultLang = S.cv.settings.langs[0];
        if (!S.cv.settings.langs.includes(S.lang)) S.lang = S.cv.settings.langs[0];
        changed(true, t('h_del_lang', {l: langLabel(S.cv, l)}));
        drawLangbar();
    });
}

// ---------------------------------------------------------------- activity history, undo and redo

/**
 * Each step keeps a snapshot of the whole document (settings, profile, content): small JSON, so undoing is just
 * putting a snapshot back. The photo lives on the server and is not part of it.
 */
const H = {steps: [], pos: -1, lastKey: '', lastAt: 0};
const HISTORY_MAX = 150;
const objIds = new WeakMap();
let objSeq = 0;

/** A stable id for an object of the document: the fields of one entry share it, so typing in them groups well. */
function objKey(o) {
    if (!objIds.has(o)) objIds.set(o, ++objSeq);
    return objIds.get(o);
}

function snapshot() {
    return JSON.stringify({settings: S.cv.settings, profile: S.cv.profile, content: S.cv.content});
}

function resetHistory() {
    H.steps = [{label: t('h_open'), snap: snapshot(), at: Date.now()}];
    H.pos = 0;
    H.lastKey = '';
    drawHistoryButtons();
}

function record(label, key) {
    if (!S.cv || H.pos < 0) return;
    const snap = snapshot();
    if (H.steps[H.pos].snap === snap) return;
    const now = Date.now();
    if (key && key === H.lastKey && now - H.lastAt < 2000 && H.pos === H.steps.length - 1 && H.pos > 0) {
        Object.assign(H.steps[H.pos], {snap, at: now}); // still typing in the same field: one step
    } else {
        H.steps = H.steps.slice(0, H.pos + 1);
        H.steps.push({label, snap, at: now});
        if (H.steps.length > HISTORY_MAX) H.steps.splice(1, 1); // the first step (CV opened) always stays
        H.pos = H.steps.length - 1;
    }
    H.lastKey = key;
    H.lastAt = now;
    drawHistoryButtons();
}

function goToStep(pos) {
    if (pos < 0 || pos >= H.steps.length || pos === H.pos) return;
    H.pos = pos;
    H.lastKey = '';
    const d = JSON.parse(H.steps[pos].snap);
    Object.assign(S.cv, d);
    if (!S.cv.settings.langs.includes(S.lang)) S.lang = S.cv.settings.defaultLang;
    dirty = true;
    setSaveState('saving');
    clearTimeout(saveTimer);
    saveTimer = setTimeout(save, 800);
    drawLangbar();
    drawPanel();
    schedulePreview();
    drawHistoryButtons();
}

const undo = () => goToStep(H.pos - 1);
const redo = () => goToStep(H.pos + 1);

function drawHistoryButtons() {
    if (!R.undoBtn) return;
    R.undoBtn.disabled = H.pos <= 0;
    R.redoBtn.disabled = H.pos >= H.steps.length - 1;
    R.undoBtn.title = H.pos > 0 ? t('undo') + ': ' + H.steps[H.pos].label : t('undo');
    R.redoBtn.title = H.pos < H.steps.length - 1 ? t('redo') + ': ' + H.steps[H.pos + 1].label : t('redo');
    if (R.historyList?.isConnected) drawHistoryList();
}

function historyMenu() {
    const wrap = h('div.menu-wrap');
    const btn = h('button.btn.btn-sm.btn-icon', {type: 'button', title: t('history'), 'aria-label': t('history'), 'aria-haspopup': 'true', 'aria-expanded': 'false'}, svg('clock'));
    const close = () => {
        wrap.querySelector('.menu')?.remove();
        btn.setAttribute('aria-expanded', 'false');
        document.removeEventListener('mousedown', outside);
    };
    const outside = (e) => !wrap.contains(e.target) && close();
    btn.addEventListener('click', () => {
        if (wrap.querySelector('.menu')) return close();
        btn.setAttribute('aria-expanded', 'true');
        R.historyList = h('ol.history-list');
        wrap.append(h('div.menu.history-menu', {role: 'dialog', 'aria-label': t('history')}, h('div.menu-info', h('b', t('history')), h('br'), /Mac|iPhone|iPad/.test(navigator.platform) ? t('history_hint').replace(/Ctrl\+/g, '⌘').replace(/(Maiusc|Shift)\+/g, '⇧') : t('history_hint')), R.historyList));
        drawHistoryList();
        setTimeout(() => document.addEventListener('mousedown', outside));
    });
    wrap.append(btn);
    return wrap;
}

function drawHistoryList() {
    const fmt = new Intl.DateTimeFormat(getLang(), {hour: '2-digit', minute: '2-digit', second: '2-digit'});
    const rows = H.steps.map((step, i) => h('li' + (i === H.pos ? '.now' : i > H.pos ? '.ahead' : ''),
        h('button', {type: 'button', 'aria-current': i === H.pos ? 'step' : undefined, onclick: () => goToStep(i)},
            h('time', fmt.format(step.at)), h('span', step.label), i === H.pos ? h('em', t('history_now')) : null)));
    put(R.historyList, rows.reverse());
}

document.addEventListener('keydown', (e) => {
    if (!S.cv || !R.editor?.isConnected || document.querySelector('.modal-back')) return;
    const mod = e.ctrlKey || e.metaKey;
    if (!mod || e.altKey) return;
    const k = e.key.toLowerCase();
    if (k === 'z' && !e.shiftKey) {
        e.preventDefault();
        undo();
    } else if ((k === 'z' && e.shiftKey) || k === 'y') {
        e.preventDefault();
        redo();
    }
});

// ---------------------------------------------------------------- drag and drop

function moveTo(arr, from, to) {
    const [x] = arr.splice(from, 1);
    arr.splice(to, 0, x);
}

/** ↑ and ↓: the same moves as drag and drop, one step at a time; the focus follows the button. */
function moveButtons(arr, i, label, listKey) {
    const go = (d, cls) => {
        moveTo(arr, i, i + d);
        changed(true, label());
        requestAnimationFrame(() => R.panel.querySelector(`[data-list="${CSS.escape(listKey)}"]`)?.querySelectorAll(':scope > [data-sort]')[i + d]?.querySelector('.' + cls)?.focus());
    };
    const up = iconBtn('up', t('move_up'), () => go(-1, 'mv-up'), '.mv-up');
    const down = iconBtn('down', t('move_down'), () => go(1, 'mv-down'), '.mv-down');
    up.disabled = i === 0;
    down.disabled = i === arr.length - 1;
    return h('span.move-btns', up, down);
}

/** The handle that drags a row: also a keyboard control (↑/↓ move the row). */
function dragHandle() {
    return h('button.drag', {type: 'button', title: t('drag'), 'aria-label': t('drag') + ' (↑ ↓)'}, svg('grip'));
}

/** Animates the rows that change place (FLIP): fn moves the DOM, the rows glide to their new spot. */
function flip(list, fn) {
    const els = [...list.children];
    const before = new Map(els.map((el) => [el, el.getBoundingClientRect().top]));
    fn();
    for (const el of els) {
        if (el.classList.contains('dragging') || !el.isConnected) continue;
        const dy = before.get(el) - el.getBoundingClientRect().top;
        if (!dy) continue;
        el.style.transition = 'none';
        el.style.transform = `translateY(${dy}px)`;
        requestAnimationFrame(() => {
            el.style.transition = 'transform .16s ease';
            el.style.transform = '';
        });
    }
}

/**
 * Drag to reorder the [data-sort] children of list, with mouse, finger or pen: the row follows the pointer, the
 * others make room, the panel scrolls near its edges. onMove(from, to) changes the data (and redraws).
 * key: the data-list name, to give the focus back to the handle after a keyboard move.
 */
function sortable(list, key, onMove) {
    list.dataset.list = key;
    const rows = () => [...list.children].filter((el) => el.hasAttribute('data-sort'));
    const refocus = (to) => requestAnimationFrame(() => R.panel.querySelector(`[data-list="${CSS.escape(key)}"]`)?.querySelectorAll(':scope > [data-sort]')[to]?.querySelector('.drag')?.focus());
    list.addEventListener('keydown', (e) => {
        const handle = e.target.closest?.('.drag');
        if (!handle || (e.key !== 'ArrowUp' && e.key !== 'ArrowDown')) return;
        const row = handle.closest('[data-sort]');
        if (row?.parentElement !== list) return;
        e.preventDefault();
        const from = rows().indexOf(row);
        const to = from + (e.key === 'ArrowUp' ? -1 : 1);
        if (to < 0 || to >= rows().length) return;
        onMove(from, to);
        refocus(to);
    });
    list.addEventListener('pointerdown', (e) => {
        const handle = e.target.closest('.drag');
        const row = handle?.closest('[data-sort]');
        if (!handle || row?.parentElement !== list || e.button > 0) return;
        e.preventDefault();
        handle.focus({preventScroll: true});
        const from = rows().indexOf(row);
        // long cards (sections) shrink to their title bar while dragging: the list becomes short and easy to sort
        if (list.classList.contains('fold-on-drag')) {
            const before = row.getBoundingClientRect().top;
            list.classList.add('folded');
            const scroller0 = R.panel.scrollHeight > R.panel.clientHeight ? R.panel : document.scrollingElement;
            scroller0.scrollTop += row.getBoundingClientRect().top - before; // keep the grabbed card under the pointer
        }
        const rect = row.getBoundingClientRect();
        const dy = Math.min(e.clientY - rect.top, rect.height - 8);
        const slot = h('div.drop-slot', {style: `height:${rect.height}px`});
        row.after(slot);
        row.classList.add('dragging');
        Object.assign(row.style, {position: 'fixed', left: rect.left + 'px', top: rect.top + 'px', width: rect.width + 'px', zIndex: 60});
        document.body.classList.add('is-dragging');
        try {
            handle.setPointerCapture(e.pointerId); // keeps the moves coming even when the pointer leaves the handle
        } catch (err) { /* synthetic events have no capturable pointer */ }
        const panelScrolls = R.panel.scrollHeight > R.panel.clientHeight && getComputedStyle(R.panel).overflowY !== 'visible';
        const scroller = panelScrolls ? R.panel : document.scrollingElement;
        let y = e.clientY;
        let raf = 0;
        // the row follows the pointer and the slot moves at every pointer move...
        const update = () => {
            row.style.top = y - dy + 'px';
            let before = null;
            for (const el of rows()) {
                if (el === row) continue;
                const r = el.getBoundingClientRect();
                if (y < r.top + r.height / 2) {
                    before = el;
                    break;
                }
            }
            if (before ? slot.nextElementSibling !== before && before !== slot : list.lastElementChild !== slot) {
                flip(list, () => (before ? list.insertBefore(slot, before) : list.append(slot)));
            }
        };
        // ...and, at every frame, the panel (or the window on a phone) scrolls when the pointer is near its edges
        const tick = () => {
            const box = panelScrolls ? R.panel.getBoundingClientRect() : {top: 0, bottom: innerHeight};
            const edge = 70;
            const before = scroller.scrollTop;
            if (y < box.top + edge) scroller.scrollTop -= Math.ceil((box.top + edge - y) / 6);
            else if (y > box.bottom - edge) scroller.scrollTop += Math.ceil((y - box.bottom + edge) / 6);
            if (scroller.scrollTop !== before) update();
            raf = requestAnimationFrame(tick);
        };
        raf = requestAnimationFrame(tick);
        const onMovePointer = (ev) => {
            y = ev.clientY;
            update();
        };
        const end = () => {
            cancelAnimationFrame(raf);
            handle.removeEventListener('pointermove', onMovePointer);
            handle.removeEventListener('pointerup', end);
            handle.removeEventListener('pointercancel', end);
            document.body.classList.remove('is-dragging');
            list.classList.remove('folded');
            row.classList.remove('dragging');
            row.removeAttribute('style');
            list.insertBefore(row, slot);
            slot.remove();
            const to = rows().indexOf(row);
            if (to !== from) {
                onMove(from, to);
                refocus(to);
            }
        };
        handle.addEventListener('pointermove', onMovePointer);
        handle.addEventListener('pointerup', end);
        handle.addEventListener('pointercancel', end);
    });
    return list;
}

// ---------------------------------------------------------------- saving

let saveTimer = null;
let saving = null;
let dirty = false;

function setSaveState(state) {
    S.saveState = state;
    if (!R.save) return;
    R.save.className = 'save-state' + (state === 'saving' ? ' busy' : state === 'failed' ? ' bad' : '');
    R.save.querySelector('em').textContent = state === 'saving' ? t('saving') : state === 'failed' ? t('save_failed') : t('saved');
}

/**
 * Something changed: the preview follows at once, the server a moment later. redraw: rebuild the form too.
 * label: what the activity history shows; key: changes with the same key within 2 s are one step (typing).
 */
function changed(redraw = false, label = '', key = '') {
    record(label || t('h_generic'), key);
    dirty = true;
    setSaveState('saving');
    clearTimeout(saveTimer);
    saveTimer = setTimeout(save, 1200);
    if (redraw) drawPanel();
    schedulePreview();
}

async function save() {
    if (saving) {
        saveTimer = setTimeout(save, 400);
        return;
    }
    if (S.conflict) return; // another window saved a newer version: only a reload can continue
    dirty = false;
    const payload = {settings: S.cv.settings, profile: S.cv.profile, content: S.cv.content, base: S.cv.rev};
    saving = api('cv.save', payload).then((d) => {
        if (typeof d.rev === 'number') S.cv.rev = d.rev;
        setSaveState(dirty ? 'saving' : 'saved');
    }).catch((e) => {
        dirty = true;
        setSaveState('failed');
        if (e.message === 'cv_conflict') {
            // the CV was saved by another window or device: never overwrite it, offer to reload
            S.conflict = true;
            clearTimeout(saveTimer);
            return toast(errText(e), {bad: true, ms: 60000, action: {label: t('reload'), run: () => {
                dirty = false;
                location.reload();
            }}});
        }
        if (e.message === 'csrf' || e.message === 'unauthenticated') return toast(errText(e), {bad: true});
        if (e.message !== 'network' && e.message !== 'server') toast(errText(e), {bad: true});
        clearTimeout(saveTimer);
        saveTimer = setTimeout(save, 5000);
    }).finally(() => (saving = null));
    await saving;
}

async function flushSave() {
    if (dirty) {
        clearTimeout(saveTimer);
        await save();
    } else if (saving) await saving;
}

window.addEventListener('beforeunload', (e) => {
    if (!dirty || !S.cv) return;
    // last try: keepalive survives the closing page
    fetch('api/?r=cv.save', {method: 'POST', keepalive: true, credentials: 'same-origin', headers: {'Content-Type': 'application/json', 'X-CSRF-Token': S.csrf}, body: JSON.stringify({settings: S.cv.settings, profile: S.cv.profile, content: S.cv.content, base: S.cv.rev})});
    e.preventDefault();
});

// ---------------------------------------------------------------- preview

let previewQueued = false;

function schedulePreview() {
    if (previewQueued) return;
    previewQueued = true;
    requestAnimationFrame(() => {
        previewQueued = false;
        drawPreview();
        if (S.tab === 'design') drawThumbs();
    });
}

function model(lang = S.lang, cv = S.cv) {
    return buildModel(cv, lang, cv.photo || null);
}

/** Top and bottom margin of the PDF pages of each template, in preview pixels (see pageMargins in pdf.js). */
const PAGE_PAD = {classic: 48, modern: 40, minimal: 56, elegant: 52, banner: 48, timeline: 48, compact: 36, studio: 48, monogram: 48, contrast: 48, swiss: 52};
const PAGE_H = 1123; // an A4 page at 96 dpi, the width of the preview being 794

/**
 * Paginates the preview the way the PDF does: an entry (or a heading with its first entry) that would cross the end of
 * a page moves to the top of the next one. Only what fits on a page is moved; long free text flows on, as in the PDF.
 */
function paginate(root, template) {
    root.querySelectorAll('[data-push]').forEach((el) => {
        el.style.marginTop = el.dataset.push;
        delete el.dataset.push;
    });
    const pad = PAGE_PAD[template] ?? 48;
    const blocks = [];
    for (const sec of root.querySelectorAll('.cv-sec')) {
        const units = [...sec.querySelectorAll(':scope > .cv-item, :scope > ul > li, :scope > .cv-text > p, :scope > .cv-text > ul > li, :scope > .cv-fields > div, :scope > .cv-contacts > li')];
        const head = sec.querySelector(':scope > h3');
        // the heading travels with the first entry of its section
        if (head || units.length) blocks.push({el: sec, last: units[0] || head});
        for (const u of units.slice(1)) blocks.push({el: u, last: u});
    }
    const footer = root.querySelector('.cv-footer');
    if (footer) blocks.push({el: footer, last: footer});
    const origin = root.getBoundingClientRect().top;
    for (const b of blocks) {
        const top = b.el.getBoundingClientRect().top - origin;
        const bottom = b.last.getBoundingClientRect().bottom - origin;
        const page = Math.floor(top / PAGE_H);
        const end = (page + 1) * PAGE_H - pad;
        if (bottom <= end || top <= page * PAGE_H + pad + 1 || bottom - top > PAGE_H - 2 * pad) continue;
        b.el.dataset.push = b.el.style.marginTop || '';
        const margin = parseFloat(getComputedStyle(b.el).marginTop) || 0;
        b.el.style.marginTop = margin + ((page + 1) * PAGE_H + pad - top) + 'px';
    }
}

function drawPreview() {
    if (!R.preview || !S.preview) return;
    const scroll = R.preview.scrollTop;
    const paper = h('div.paper-scale', {html: renderCV(model(), 'paper')});
    const wrap = h('div.paper-wrap', paper);
    put(R.preview, wrap);
    const fit = () => {
        const avail = R.preview.clientWidth - 48;
        const scale = Math.min(1.1, Math.max(0.3, avail / 794));
        paper.style.transform = '';
        fixContactSeparators(paper);
        paginate(paper.firstElementChild, S.cv.settings.template);
        const height = paper.firstElementChild.offsetHeight;
        paper.style.transform = `scale(${scale})`;
        wrap.style.width = 794 * scale + 'px';
        wrap.style.height = height * scale + 'px';
        wrap.querySelectorAll('.page-break').forEach((b) => b.remove());
        for (let p = 1; p * 1123 < height - 40; p++) wrap.append(h('div.page-break', {style: `top:${p * 1123 * scale}px`}, h('span', (getLang() === 'it' ? 'Pagina ' : 'Page ') + (p + 1))));
    };
    fit();
    R.preview.scrollTop = scroll;
    document.fonts?.ready.then(fit);
}

window.addEventListener('resize', () => R.preview && drawPreview());

// ---------------------------------------------------------------- the form

function drawPanel() {
    if (!R.panel) return;
    const scroll = R.panel.scrollTop;
    const tabs = h('div.tabs', {role: 'tablist'}, [['content', t('tab_content')], ['design', t('tab_design')], ['share', t('tab_share')]].map(([k, label]) => h('button.tab' + (S.tab === k ? '.on' : ''), {type: 'button', role: 'tab', 'aria-selected': S.tab === k ? 'true' : 'false', onclick: () => {
        S.tab = k;
        drawPanel();
        R.panel.scrollTop = 0;
    }}, label)));
    const body = h('div.panel-body');
    if (S.tab === 'content') {
        contentTab(body);
        body.prepend(outline());
    }
    else if (S.tab === 'design') designTab(body);
    else shareTab(body);
    put(R.panel, tabs, body);
    if (S.tab === 'design') drawThumbs(); // after the panel is on the page
    R.panel.scrollTop = scroll;
}

/** A text field bound to obj[key]. */
function textField(label, obj, key, opts = {}) {
    const tag = opts.area ? 'textarea' : 'input';
    const el = h(tag, {type: opts.area ? undefined : opts.type || 'text', value: obj[key] ?? '', placeholder: opts.placeholder, maxlength: opts.max, rows: opts.rows, list: opts.list, autocomplete: 'off'});
    el.addEventListener('input', () => {
        obj[key] = el.value;
        // typing in one field is one step of the history (see record)
        changed(false, t('h_edit', {f: opts.histLabel || label || '…'}), (opts.histKey || 'o' + objKey(obj)) + ':' + key);
        opts.onInput?.(el.value);
    });
    if (label) el.setAttribute('aria-label', label);
    // a div, not a label: the show/hide switch next to the title is a label of its own
    return h('div.field', opts.noLabel ? null : h('span', label, opts.extra || null), el, opts.hint ? h('small', opts.hint) : null);
}

function showSwitch(obj, key, what = '') {
    const input = h('input', {type: 'checkbox', checked: obj[key] !== false, 'aria-label': t('show_on_cv') + (what ? ' — ' + what : '')});
    const onChange = typeof what === 'function' ? what : null;
    input.addEventListener('change', () => {
        obj[key] = input.checked;
        changed(false, t('h_show', {f: typeof what === 'string' && what ? what : t(key === 'photo' ? 'photo' : 'show_on_cv')}));
        onChange?.(input.checked);
    });
    return h('label.switch', {title: t('show_on_cv')}, input, h('span', t('show_on_cv')));
}

function contentTab(body) {
    const c = S.cv.content[S.lang];
    const p = S.cv.profile;
    const show = S.cv.settings.show;

    // Personal details
    const photoBox = h('div.photo-box', S.cv.photo ? h('img', {src: S.cv.photo, alt: ''}) : svg('user'));
    const fileIn = h('input', {type: 'file', accept: 'image/jpeg,image/png,image/webp', hidden: true, onchange: () => fileIn.files[0] && uploadPhoto(fileIn.files[0])});
    const photoRow = h('div.photo-row', photoBox, h('div.photo-actions',
        h('div.row', h('button.btn.btn-sm', {type: 'button', onclick: () => fileIn.click()}, svg('upload'), S.cv.photo ? t('photo_change') : t('photo_upload')),
            S.cv.photo && h('button.btn.btn-sm.btn-ghost.btn-danger', {type: 'button', onclick: removePhoto}, t('photo_remove'))),
        showSwitch(show, 'photo')), fileIn);

    const links = sortable(h('div.stack.sort-list', (p.links || []).map((l, i) => h('div.kv-row', {'data-sort': ''},
        dragHandle(),
        h('div.kv-inputs',
            h('input', {value: l.label, placeholder: t('link_label') + ' (LinkedIn…)', 'aria-label': t('link_label'), oninput: (e) => {
                l.label = e.target.value;
                changed(false, t('h_edit', {f: t('links')}), 'link' + objKey(l));
            }}),
            h('input', {type: 'url', value: l.url, placeholder: 'https://', 'aria-label': t('link_url'), oninput: (e) => {
                l.url = e.target.value;
                changed(false, t('h_edit', {f: t('links')}), 'link' + objKey(l));
            }})),
        h('span'),
        iconBtn('trash', t('delete'), () => {
            p.links.splice(i, 1);
            changed(true, t('h_del_link'));
        })))), 'links', (from, to) => {
        moveTo(p.links, from, to);
        changed(true, t('h_move_item', {s: t('links')}));
    });
    const linksBox = h('div.stack', links, h('div.row', h('button.btn.btn-sm', {type: 'button', onclick: () => {
        (p.links ||= []).push({label: '', url: ''});
        changed(true, t('h_add_link'));
    }}, svg('plus'), t('add_link')), h('span.grow'), showSwitch(show, 'links', t('links'))));

    const fields = sortable(h('div.stack.sort-list', c.fields.map((f, i) => h('div.kv-row', {'data-sort': ''},
        dragHandle(),
        h('div.kv-inputs',
            h('input', {value: f.label, placeholder: t('field_label'), 'aria-label': t('field_label'), oninput: (e) => {
                f.label = e.target.value;
                changed(false, t('h_edit', {f: f.label || t('custom_fields')}), 'field' + objKey(f));
            }}),
            h('input', {value: f.value, placeholder: t('field_value'), 'aria-label': t('field_value'), oninput: (e) => {
                f.value = e.target.value;
                changed(false, t('h_edit', {f: f.label || t('custom_fields')}), 'field' + objKey(f));
            }})),
        h('label.switch', {title: t('show_on_cv')}, h('input', {type: 'checkbox', checked: f.show !== false, 'aria-label': t('show_on_cv'), onchange: (e) => {
            f.show = e.target.checked;
            changed(false, t('h_show', {f: f.label || t('custom_fields')}));
        }})),
        iconBtn('trash', t('delete'), () => {
            c.fields.splice(i, 1);
            changed(true, t('h_del_field'));
        })))), 'fields', (from, to) => {
        moveTo(c.fields, from, to);
        changed(true, t('h_move_item', {s: t('custom_fields')}));
    });
    const fieldsBox = h('div.stack', fields, h('div', h('button.btn.btn-sm', {type: 'button', onclick: () => {
        c.fields.push({label: '', value: '', show: true});
        changed(true, t('h_add_field'));
    }}, svg('plus'), t('add_field'))));

    const shared = h('span.badge', {title: t('shared_all_langs')}, svg('shared'), t('shared_all_langs'));
    body.append(h('section.card', {'data-anchor': 'profile'},
        h('div.card-head', h('h3', t('profile'))),
        photoRow,
        h('div.grid2',
            textField(t('first_name'), p, 'firstName', {max: 60}),
            textField(t('last_name'), p, 'lastName', {max: 60}),
            textField(t('headline'), c, 'headline', {max: 120}),
            textField(t('location'), c, 'location', {max: 120, extra: showSwitch(show, 'location', t('location'))}),
            textField(t('email'), p, 'email', {type: 'email', max: 190, extra: showSwitch(show, 'email', t('email'))}),
            textField(t('phone'), p, 'phone', {type: 'tel', max: 40, extra: showSwitch(show, 'phone', t('phone'))}),
            textField(t('website'), p, 'website', {type: 'url', max: 300, placeholder: 'https://', extra: showSwitch(show, 'website', t('website'))}),
            textField(t('birth'), p, 'birthDate', {type: 'date', extra: showSwitch(show, 'birth', t('birth'))})),
        h('div.field', {style: 'margin-top:14px'}, h('span', t('links')), linksBox),
        h('div.field', {style: 'margin-top:14px'}, h('span', t('custom_fields')), h('small', t('custom_fields_hint')), fieldsBox),
        h('p.note', {style: 'margin-top:12px'}, shared, ' ', t('first_name') + ', ' + t('last_name').toLowerCase() + ', ' + t('email').toLowerCase() + ', ' + t('phone').toLowerCase() + '…')));

    body.append(h('section.card', {'data-anchor': 'summary'}, h('div.card-head', h('h3', t('summary'))), textField(t('summary'), c, 'summary', {area: true, rows: 4, max: 4000, hint: t('summary_hint'), noLabel: true})));

    body.append(sortable(h('div.sections.sort-list.fold-on-drag', c.sections.map((s, idx) => sectionCard(c, s, idx))), 'sections', (from, to) => {
        const s = c.sections[from];
        moveTo(c.sections, from, to);
        changed(true, t('h_move_section', {s: secName(s)}));
    }));

    body.append(h('section.card', {'data-anchor': 'add'}, h('div.card-head', h('h3', t('add_section'))),
        h('div.add-sec', Object.keys(SECTION_KINDS).map((k) => h('button.btn.btn-sm', {type: 'button', onclick: () => {
            const sec = {id: uid(), type: SECTION_KINDS[k].type, kind: k, title: SECTION_KINDS[k][S.lang] || SECTION_KINDS[k].en, show: true, text: '', items: []};
            if (sec.type !== 'text') sec.items.push(newItem(sec.type));
            c.sections.push(sec);
            changed(true, t('h_add_section', {s: secName(sec)}));
            requestAnimationFrame(() => R.panel.querySelector(`[data-sec="${sec.id}"]`)?.scrollIntoView({behavior: 'smooth', block: 'center'}));
        }}, svg('plus'), t('k_' + k))))));

    const footer = textField(t('footer'), c, 'footer', {area: true, rows: 3, max: 1000, hint: t('footer_hint'), noLabel: true});
    body.append(h('section.card', {'data-anchor': 'footer'}, h('div.card-head', h('h3', t('footer')),
        h('button.btn.btn-sm', {type: 'button', onclick: () => {
            c.footer = CONSENT[S.lang] || CONSENT.en;
            changed(true, t('h_footer'));
        }}, t('footer_consent'))), footer));

    const customLang = S.cv.settings.customLangs?.[S.lang];
    if (customLang) body.append(customWordsCard(customLang));

    if (S.cv.settings.langs.length > 1) body.append(h('div', h('button.btn.btn-sm.btn-ghost.btn-danger', {type: 'button', onclick: () => removeLanguage(S.lang)}, svg('trash'), t('remove_lang') + ' (' + langLabel(S.cv, S.lang) + ')')));
}

/**
 * A language added by the user: its name and the small words printed on the CV, which the built-in languages
 * already have. Empty words fall back to English (shown as placeholders).
 */
function customWordsCard(lang) {
    lang.words = Array.isArray(lang.words) || !lang.words ? {} : lang.words; // PHP sends [] when empty
    const en = CV_WORDS.en;
    const word = (key, label) => textField(label, lang.words, key, {max: 60, placeholder: en[key], histLabel: t('custom_words')});
    const nameField = textField(t('custom_lang_name'), lang, 'name', {max: 40, histLabel: t('custom_lang_name'), onInput: () => drawLangbar()});
    return h('section.card', {'data-anchor': 'words'}, h('div.card-head', h('h3', t('custom_words')), h('span.badge', S.lang)),
        h('p.note', {style: 'margin-bottom:12px'}, t('custom_words_hint')),
        h('div.grid2', nameField, word('present', t('w_present')), word('born', t('birth')), word('profile', t('summary')), word('contacts', t('w_contacts')),
            word('native', t('w_native')), word('eqf', t('t_eqf')), word('grade', t('t_grade')), word('link', t('t_link')),
            word('pageOf', t('w_page_of'))));
}

/**
 * The outline of the content (wide screens with the preview hidden): every card of the form, the current one
 * highlighted while scrolling; a click scrolls to it. Sections show how many entries they have and whether hidden.
 */
function outline() {
    const c = S.cv.content[S.lang];
    const nav = h('nav.outline', {'aria-label': t('outline')});
    const go = (anchor) => () => {
        const el = R.panel.querySelector(`[data-anchor="${anchor}"]`);
        if (!el) return;
        // the clicked item stays selected until the user scrolls by themselves (a card near the end cannot
        // reach the top, and the smooth scroll passes over the others)
        R.outlineLock = anchor;
        markOutline(anchor);
        el.scrollIntoView({behavior: 'smooth', block: 'start'});
        el.classList.add('flash');
        setTimeout(() => el.classList.remove('flash'), 900);
    };
    const link = (anchor, label, extra = null, cls = '') => h('button.outline-link' + cls, {type: 'button', 'data-to': anchor, onclick: go(anchor)}, h('span', label), extra);
    const allFolded = c.sections.length && c.sections.every((s) => S.collapsed.has('sec:' + s.id));
    nav.append(...[
        h('p.outline-title', t('outline')),
        link('profile', t('profile')),
        link('summary', t('summary')),
        h('div.outline-secs', c.sections.map((s) => link('sec-' + s.id, secName(s), h('em', s.type === 'text' ? '' : String(s.items.length)), s.show === false ? '.is-hidden' : ''))),
        link('add', '+ ' + t('add_section'), null, '.muted'),
        link('footer', t('footer')),
        S.cv.settings.customLangs?.[S.lang] ? link('words', t('custom_words')) : null,
        c.sections.length ? h('button.btn.btn-sm.btn-ghost.outline-fold', {type: 'button', onclick: () => {
            // the page changes height: keep the selected section in view and selected
            const keep = R.outlineLock || R.panel.querySelector('.outline-link.on')?.dataset.to;
            for (const s of c.sections) allFolded ? S.collapsed.delete('sec:' + s.id) : S.collapsed.add('sec:' + s.id);
            drawPanel();
            const el = keep && R.panel.querySelector(`[data-anchor="${keep}"]`);
            if (el) {
                R.outlineLock = keep;
                el.scrollIntoView({block: 'start'});
                markOutline(keep);
            }
        }}, svg(allFolded ? 'unfold' : 'fold'), allFolded ? t('expand_all') : t('collapse_all')) : null,
    ].filter(Boolean)); // DOM append would write null as text
    setTimeout(updateOutline); // once the panel is on the page
    return nav;
}

/** Highlights one item of the outline. */
function markOutline(anchor) {
    R.panel?.querySelectorAll('.outline-link').forEach((b) => b.classList.toggle('on', b.dataset.to === anchor));
}

/**
 * The card being read: the last one whose top has gone past the line just under the sticky tabs. At the very end of
 * the page, the last card. Collapsed cards are short, so a band in the middle of the panel would pick the next one.
 */
function updateOutline() {
    if (!R.panel?.querySelector('.outline') || R.outlineLock) return;
    const cards = [...R.panel.querySelectorAll('.panel-body > [data-anchor], .panel-body [data-anchor].sec-card')];
    if (!cards.length) return;
    const line = R.panel.getBoundingClientRect().top + 80;
    let current = cards[0];
    for (const c of cards) if (c.getBoundingClientRect().top <= line) current = c;
    // scrolled to the very end: the last cards can never reach the line, the last one is the one being read
    const scrolls = R.panel.scrollHeight > R.panel.clientHeight + 2;
    if (scrolls && R.panel.scrollTop > 0 && R.panel.scrollTop + R.panel.clientHeight >= R.panel.scrollHeight - 2) current = cards[cards.length - 1];
    markOutline(current.dataset.anchor);
}

function secName(s) {
    return s.title || t('k_' + s.kind);
}

function newItem(type) {
    if (type === 'timeline') return {title: '', org: '', place: '', start: '', end: '', current: false, text: '', url: '', eqf: 0, grade: ''};
    if (type === 'skills') return {name: '', level: 0};
    if (type === 'langs') return {name: '', level: ''};
    return {title: '', subtitle: '', date: '', url: '', text: ''};
}

/** One section: drag handle, title, collapse, show/hide, delete; then its entries (also draggable). */
function sectionCard(c, s, idx) {
    const name = secName(s);
    const colKey = 'sec:' + s.id;
    const titleIn = h('input.sec-title', {value: s.title, placeholder: t('section_title'), 'aria-label': t('section_title'), maxlength: 80, oninput: (e) => {
        s.title = e.target.value;
        changed(false, t('h_edit', {f: t('section_title')}), 'title' + objKey(s));
    }});
    const eye = iconBtn(s.show !== false ? 'eye' : 'eyeOff', s.show !== false ? t('show_on_cv') : t('section_hidden'), () => {
        s.show = s.show === false;
        changed(true, t('h_toggle_section', {s: name}));
    });
    const card = h('section.card.sec-card' + (s.show === false ? '.is-hidden' : '') + (S.collapsed.has(colKey) ? '.collapsed' : ''), {'data-sec': s.id, 'data-sort': '', 'data-anchor': 'sec-' + s.id});
    const fold = iconBtn(S.collapsed.has(colKey) ? 'unfold' : 'fold', t('fold'), () => {
        S.collapsed.has(colKey) ? S.collapsed.delete(colKey) : S.collapsed.add(colKey);
        card.classList.toggle('collapsed');
        put(fold, svg(card.classList.contains('collapsed') ? 'unfold' : 'fold'));
        fold.setAttribute('aria-expanded', card.classList.contains('collapsed') ? 'false' : 'true');
    });
    fold.setAttribute('aria-expanded', S.collapsed.has(colKey) ? 'false' : 'true');
    fold.setAttribute('aria-label', name);
    card.append(h('div.card-head', dragHandle(), h('div', {style: 'flex:1;min-width:0'}, titleIn), eye, fold,
        moveButtons(c.sections, idx, () => t('h_move_section', {s: name}), 'sections'),
        iconBtn('trash', t('delete'), () => confirmBox(name + ' — ' + t('delete') + '?', t('delete'), () => {
            c.sections.splice(idx, 1);
            changed(true, t('h_del_section', {s: name}));
        }))));
    const body = h('div.sec-body');
    card.append(body);
    if (s.type === 'text') {
        body.append(textField(name, s, 'text', {area: true, rows: 4, max: 4000, noLabel: true}));
        return card;
    }
    let items;
    if (s.type === 'skills' || s.type === 'langs') {
        if (s.type === 'langs') body.append(h('datalist', {id: 'lv-' + s.id}, [cvWords(S.cv, S.lang).native, ...LANG_LEVELS].map((v) => h('option', {value: v}))));
        items = h('div.items.sort-list', s.items.map((it, i) => {
            const dots = s.type === 'langs' ? langDotsPicker(it, name) : null;
            return h('div.tag-row' + (s.type === 'langs' ? '.lang-row' : ''), {'data-sort': ''},
            dragHandle(),
            h('input', {value: it.name, placeholder: s.type === 'skills' ? t('t_skill') : t('t_lang'), 'aria-label': s.type === 'skills' ? t('t_skill') : t('t_lang'), maxlength: 80, oninput: (e) => {
                it.name = e.target.value;
                changed(false, t('h_edit', {f: name}), 'tag' + objKey(it));
            }}),
            s.type === 'skills' ? levelPicker(it, name) : h('input', {value: it.level, placeholder: t('t_level'), 'aria-label': t('t_level'), list: 'lv-' + s.id, maxlength: 60, oninput: (e) => {
                it.level = e.target.value;
                dots.redraw(); // automatic dots follow the written level
                changed(false, t('h_edit', {f: name}), 'tag' + objKey(it));
            }}),
            dots,
            iconBtn('trash', t('delete'), () => {
                s.items.splice(i, 1);
                changed(true, t('h_del_item', {s: name}));
            }));
        }));
    } else {
        items = h('div.items.sort-list', s.items.map((it, i) => itemCard(s, it, i)));
    }
    sortable(items, 'items:' + s.id, (from, to) => {
        moveTo(s.items, from, to);
        changed(true, t('h_move_item', {s: name}));
    });
    body.append(items, h('div', {style: 'margin-top:10px'}, h('button.btn.btn-sm', {type: 'button', onclick: () => {
        s.items.push(newItem(s.type));
        changed(true, t('h_add_item', {s: name}));
        requestAnimationFrame(() => {
            const rows = R.panel.querySelectorAll(`[data-sec="${s.id}"] .items > [data-sort]`);
            rows[rows.length - 1]?.querySelector('input')?.focus();
        });
    }}, svg('plus'), t('add_item'))));
    return card;
}

function levelPicker(it, name) {
    const box = h('div.level', {role: 'radiogroup', 'aria-label': t('t_level')});
    const draw = () => put(box, [1, 2, 3, 4, 5].map((n) => h('button' + (n <= it.level ? '.on' : ''), {type: 'button', 'aria-label': n + '/5', 'aria-checked': n === it.level ? 'true' : 'false', role: 'radio', onclick: () => {
        it.level = it.level === n ? 0 : n;
        draw();
        changed(false, t('h_edit', {f: name}));
    }})));
    draw();
    return box;
}

/**
 * Dots of a language: lighter while they follow the written level (C1 → 5), solid once chosen by hand.
 * Clicking the dot already chosen removes the dots.
 */
function langDotsPicker(it, name) {
    const box = h('div.level', {role: 'radiogroup', 'aria-label': t('t_level')});
    box.redraw = () => {
        const n = langDots(it);
        const auto = it.dots === null || it.dots === undefined;
        box.classList.toggle('auto', auto);
        box.title = auto ? t('dots_auto') : '';
        put(box, [1, 2, 3, 4, 5].map((k) => h('button' + (k <= n ? '.on' : ''), {type: 'button', 'aria-label': k + '/5', 'aria-checked': k === n ? 'true' : 'false', role: 'radio', onclick: () => {
            it.dots = n === k && !auto ? 0 : k;
            box.redraw();
            changed(false, t('h_edit', {f: name}));
        }})));
    };
    box.redraw();
    return box;
}

/** One entry of a timeline or list section, collapsible. Education adds EQF level and grade; projects a link. */
function itemCard(s, it, i) {
    const name = secName(s);
    const key = 'item:' + objKey(it);
    const label = () => (s.type === 'timeline' ? [it.title, it.org].filter(Boolean).join(' · ') : it.title) || '…';
    const lab = h('b', label());
    const card = h('div.item' + (S.collapsed.has(key) ? '.collapsed' : ''), {'data-sort': ''});
    const toggle = () => {
        S.collapsed.has(key) ? S.collapsed.delete(key) : S.collapsed.add(key);
        card.classList.toggle('collapsed');
        put(fold, svg(card.classList.contains('collapsed') ? 'unfold' : 'fold'));
    };
    const fold = iconBtn(S.collapsed.has(key) ? 'unfold' : 'fold', t('fold'), toggle);
    lab.addEventListener('click', toggle);
    card.append(h('div.item-head', dragHandle(), fold, lab, moveButtons(s.items, i, () => t('h_move_item', {s: name}), 'items:' + s.id), iconBtn('trash', t('delete'), () => {
        s.items.splice(i, 1);
        changed(true, t('h_del_item', {s: name}));
    })));
    const relabel = () => (lab.textContent = label());
    const f = (lbl, k, opts = {}) => textField(lbl, it, k, {...opts, histLabel: name, histKey: key});
    if (s.type === 'timeline') {
        const endField = f(t('t_end'), 'end', {max: 20, placeholder: t('date_hint')});
        endField.querySelector('input').disabled = !!it.current;
        const cur = h('label.check', {style: 'font-size:13px'}, h('input', {type: 'checkbox', checked: !!it.current, onchange: (e) => {
            it.current = e.target.checked;
            endField.querySelector('input').disabled = it.current;
            changed(false, t('h_edit', {f: name}));
        }}), h('span', t('t_current')));
        const isEdu = s.kind === 'education';
        const isProject = s.kind === 'projects' || s.kind === 'volunteering' || s.kind === 'list';
        add(card,
            f(isProject ? t('t_title') : t('t_role'), 'title', {max: 120, onInput: relabel}),
            h('div.grid2', f(isProject ? t('t_subtitle') : t('t_org'), 'org', {max: 120, onInput: relabel}), f(t('t_place'), 'place', {max: 80})),
            h('div.grid2', f(t('t_start'), 'start', {max: 20, placeholder: t('date_hint')}), endField),
            cur,
            isEdu && h('div.grid2', eqfSelect(it, name), f(t('t_grade'), 'grade', {max: 40, placeholder: t('t_grade_hint')})),
            f(t('t_text'), 'text', {area: true, rows: 3, max: 3000, hint: t('t_text_hint')}),
            !isEdu && s.kind !== 'experience' && f(t('t_link'), 'url', {type: 'url', max: 300, placeholder: 'https://'}));
    } else {
        add(card,
            h('div.grid2', f(t('t_title'), 'title', {max: 160, onInput: relabel}), f(t('t_subtitle'), 'subtitle', {max: 160})),
            h('div.grid2', f(t('t_date'), 'date', {max: 40}), f(t('t_url'), 'url', {type: 'url', max: 300, placeholder: 'https://'})),
            f(t('t_text'), 'text', {area: true, rows: 2, max: 2000}));
    }
    return card;
}

/** EQF level 1–8 (European Qualifications Framework), optional. */
function eqfSelect(it, name) {
    const sel = h('select', {'aria-label': t('t_eqf'), onchange: (e) => {
        it.eqf = +e.target.value;
        changed(false, t('h_edit', {f: t('t_eqf')}));
    }}, h('option', {value: 0}, '— ' + t('t_eqf_none')), [1, 2, 3, 4, 5, 6, 7, 8].map((n) => h('option', {value: n, selected: (it.eqf || 0) === n}, 'EQF ' + n)));
    return h('div.field', h('span', t('t_eqf')), sel);
}

// ---------------------------------------------------------------- photo

async function uploadPhoto(file) {
    try {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        await img.decode();
        const S2 = 480;
        const side = Math.min(img.naturalWidth, img.naturalHeight);
        const cnv = document.createElement('canvas');
        cnv.width = cnv.height = S2;
        const g = cnv.getContext('2d');
        g.fillStyle = '#fff';
        g.fillRect(0, 0, S2, S2);
        g.drawImage(img, (img.naturalWidth - side) / 2, (img.naturalHeight - side) / 2, side, side, 0, 0, S2, S2);
        URL.revokeObjectURL(img.src);
        const d = await api('cv.photoSet', {data: cnv.toDataURL('image/jpeg', 0.86)});
        S.cv.photo = d.photo;
        if (!S.cv.settings.show.photo) {
            S.cv.settings.show.photo = true;
            changed(false, t('h_show', {f: t('photo')}));
        }
        drawPanel();
        drawPreview();
    } catch (e) {
        toast(e instanceof ApiError ? errText(e) : t('err_photo_invalid'), {bad: true});
    }
}

function removePhoto() {
    confirmBox(t('photo_remove') + '?', t('photo_remove'), async () => {
        try {
            await api('cv.photoDelete', {});
            S.cv.photo = null;
            drawPanel();
            drawPreview();
        } catch (e) {
            toast(errText(e), {bad: true});
        }
    });
}

// ---------------------------------------------------------------- design

function designTab(body) {
    const st = S.cv.settings;
    R.thumbs = {};
    body.append(h('section.card', h('div.card-head', h('h3', t('design_template'))),
        h('div.tpl-grid', TEMPLATES.map((tp) => {
            const thumb = h('div.tpl-thumb');
            R.thumbs[tp] = thumb;
            return h('button.tpl' + (st.template === tp ? '.on' : ''), {type: 'button', 'aria-pressed': st.template === tp ? 'true' : 'false', onclick: () => {
                st.template = tp;
                changed(true, t('h_template', {x: t('tpl_' + tp)}));
            }}, thumb, t('tpl_' + tp));
        }))));
    const custom = h('input', {type: 'color', value: st.accent, 'aria-label': t('design_color'), oninput: (e) => {
        st.accent = e.target.value.toUpperCase();
        changed(false, t('h_color'), 'accent');
        body.querySelectorAll('.swatch').forEach((s) => s.classList.toggle('on', s.dataset.c === st.accent));
    }});
    body.append(h('section.card', h('div.card-head', h('h3', t('design_color'))),
        h('div.swatches', ACCENTS.map((c) => h('button.swatch' + (c === st.accent ? '.on' : ''), {type: 'button', 'data-c': c, style: `background:${c}`, 'aria-label': c, onclick: () => {
            st.accent = c;
            changed(true, t('h_color'));
        }})), h('label.swatch.swatch-custom' + (ACCENTS.includes(st.accent) ? '' : '.on'), {title: t('design_color')}, custom))));
    // typefaces in two groups; each name is written in its own typeface (the browser loads only the regular weight here)
    const fontGroup = (kind, title) => h('div.font-group', h('h4', title), h('div.font-grid', {role: 'radiogroup', 'aria-label': title},
        Object.entries(FONTS).filter(([, f]) => f.kind === kind).map(([id, f]) => h('button.font-opt' + (st.font === id ? '.on' : ''), {
            type: 'button', role: 'radio', 'aria-checked': st.font === id ? 'true' : 'false', style: `font-family:${f.css}, ${kind === 'serif' ? 'Georgia, serif' : 'sans-serif'}`,
            onclick: () => {
                st.font = id;
                changed(true, t('h_font') + ': ' + f.name);
            },
        }, h('span.font-sample', 'Aa'), h('span.font-name', f.name)))));
    body.append(h('section.card', h('div.card-head', h('h3', t('design_font'))), fontGroup('sans', t('font_sans')), fontGroup('serif', t('font_serif'))));
    body.append(pdfOptionsCard());
}

/** PDF options: "Page X of Y" in the footer (on by default). */
function pdfOptionsCard() {
    const st = S.cv.settings;
    const input = h('input', {type: 'checkbox', checked: st.pageNumbers !== false, onchange: (e) => {
        st.pageNumbers = e.target.checked;
        changed(false, t('h_page_numbers'));
    }});
    return h('section.card', h('div.card-head', h('h3', t('design_pdf'))),
        h('label.check', input, h('span', t('page_numbers'))), h('p.note', {style: 'margin-top:6px'}, t('page_numbers_hint')));
}

const thumbSizer = 'ResizeObserver' in window ? new ResizeObserver((entries) => {
    for (const e of entries) e.target.style.setProperty('--thumb-scale', String(e.contentRect.width / 794));
}) : null;

/** The template thumbnails: the real CV, rendered on an A4 page scaled to the width of each box. */
function drawThumbs() {
    if (!R.thumbs) return;
    for (const [tp, el] of Object.entries(R.thumbs)) {
        if (!el.isConnected) continue;
        const cv = {...S.cv, settings: {...S.cv.settings, template: tp}};
        el.innerHTML = renderCV(model(S.lang, cv), 'paper');
        el.style.setProperty('--thumb-scale', String((el.clientWidth || 175) / 794));
        fixContactSeparators(el);
        thumbSizer?.observe(el);
    }
}

// ---------------------------------------------------------------- share and account

function publicUrl() {
    return BASE + '?user=' + encodeURIComponent(S.user.username);
}

function shareTab(body) {
    const st = S.cv.settings;
    const url = publicUrl();
    const pub = h('input', {type: 'checkbox', checked: st.public});
    pub.addEventListener('change', () => {
        st.public = pub.checked;
        changed(true, t('h_public'));
    });
    body.append(h('section.card',
        h('div.card-head', h('h3', t('share_public')), h('label.switch', pub, h('span', st.public ? 'ON' : 'OFF'))),
        h('p.note', st.public ? t('share_public_on') : t('share_public_off')),
        h('div.share-url', {style: 'margin:10px 0'}, h('code', url),
            iconBtn('copy', t('share_copy'), async () => {
                try {
                    await navigator.clipboard.writeText(url);
                    toast(t('share_copied'));
                } catch (e) {
                    prompt('', url);
                }
            }),
            canShare() && iconBtn('shareArrow', t('share_share'), () => shareLink(url, [S.cv.profile.firstName, S.cv.profile.lastName].filter(Boolean).join(' ') + ' — CV')),
            h('a.btn.btn-ghost.btn-icon', {href: url, target: '_blank', rel: 'noopener', title: t('share_open'), 'aria-label': t('share_open')}, svg('ext'))),
        h('div.stack',
            usernameEditor(),
            h('div.field', h('span', t('share_default_lang')), langPicker(st.langs, st.defaultLang, (l) => {
                st.defaultLang = l;
                changed(false, t('h_settings'));
            })),
            h('label.check', h('input', {type: 'checkbox', checked: st.allowPdf, onchange: (e) => {
                st.allowPdf = e.target.checked;
                changed(false, t('h_settings'));
            }}), h('span', t('share_allow_pdf'))),
            h('p.note', t('share_views', {n: S.cv.views || 0}) + ' · ' + t('share_note')))));

    body.append(h('section.card', h('div.card-head', h('h3', t('pdf'))),
        h('div.pdf-list', st.langs.map((l) => h('button.btn', {type: 'button', onclick: () => makePdf(l)}, svg('download'), flag(l), langLabel(S.cv, l))))));

    body.append(accountCard());
}

function usernameEditor() {
    const input = h('input', {value: S.user.username, maxlength: 30, autocapitalize: 'off'});
    input.addEventListener('input', () => (input.value = input.value.toLowerCase().replace(/[^a-z0-9-]/g, '')));
    const msg = h('p.msg');
    return h('div.field', h('span', t('share_address')),
        h('div.row', h('div.username-field.grow', h('span', '?user='), input),
            h('button.btn', {type: 'button', onclick: async () => {
                if (input.value === S.user.username) return;
                try {
                    const d = await api('auth.username', {username: input.value});
                    S.user = d.user;
                    S.cv.username = d.user.username;
                    if (S.cv.photo) S.cv.photo = S.cv.photo.replace(/user=[^&]+/, 'user=' + encodeURIComponent(d.user.username));
                    drawPanel();
                    toast(t('saved'));
                } catch (e) {
                    msg.textContent = errText(e);
                }
            }}, t('save'))), msg);
}

function accountCard() {
    const u = S.user;
    const msg = h('p.msg');
    const cur = h('input', {type: 'password', autocomplete: 'current-password'});
    const nw = h('input', {type: 'password', autocomplete: 'new-password', minlength: 8});
    return h('section.card', h('div.card-head', h('h3', t('account'))),
        h('div.stack',
            h('label.field', h('span', t('acc_email')), h('input', {value: u.email, readonly: true}), h('small', t('acc_email_note'))),
            u.googleLinked && h('p.note.with-ico', svg('check'), t('acc_google')),
            h('div.field', h('span', u.hasPassword ? t('acc_password') : t('acc_set_password')),
                u.hasPassword && h('label.field', h('small', t('acc_current')), cur),
                h('label.field', h('small', t('acc_new')), nw),
                h('div', h('button.btn.btn-sm', {type: 'button', onclick: async () => {
                    try {
                        const d = await api('auth.password', {current: cur.value, password: nw.value});
                        S.user = d.user;
                        cur.value = nw.value = '';
                        msg.className = 'msg ok';
                        msg.textContent = t('saved');
                    } catch (e) {
                        msg.className = 'msg';
                        msg.textContent = errText(e);
                    }
                }}, t('save'))), msg),
            h('div.row', {style: 'flex-wrap:wrap'},
                h('button.btn.btn-sm', {type: 'button', onclick: logout}, svg('logout'), t('logout')),
                h('button.btn.btn-sm.btn-ghost.btn-danger', {type: 'button', onclick: deleteAccount}, svg('trash'), t('acc_delete')))));
}

function deleteAccount() {
    const pw = h('input', {type: 'password', autocomplete: 'current-password'});
    const msg = h('p.msg');
    modal(t('acc_delete'), h('div.stack', h('p', t('acc_delete_body')), S.user.hasPassword && h('label.field', h('span', t('password')), pw), msg), [
        {label: t('cancel')},
        {label: t('acc_delete'), danger: true, run: async (close) => {
            try {
                await api('auth.delete', {password: pw.value});
                close();
                dirty = false;
                S.user = null;
                S.cv = null;
                landing();
            } catch (e) {
                msg.textContent = errText(e);
            }
        }},
    ]);
}

// ---------------------------------------------------------------- PDF export and import

async function makePdf(lang) {
    const m = model(lang);
    toast(t('pdf_making'), {ms: 2500});
    try {
        await downloadPdf(m, BASE, pdfFileName(m));
    } catch (e) {
        console.error(e);
        toast(t('err_generic'), {bad: true});
    }
}

function importDialog() {
    const lang = S.lang;
    const msg = h('p.msg');
    const fileIn = h('input', {type: 'file', accept: 'application/pdf,.pdf', hidden: true});
    const btn = h('button.btn.btn-primary', {type: 'button', onclick: () => fileIn.click()}, svg('file'), t('import_choose'));
    let close;
    fileIn.addEventListener('change', async () => {
        const file = fileIn.files[0];
        if (!file) return;
        btn.disabled = true;
        btn.replaceChildren(h('span.spinner'), t('import_reading'));
        try {
            const {importPdf} = await import('./import.js');
            const res = await importPdf(file, lang);
            const before = clone({profile: S.cv.profile, content: S.cv.content[lang]});
            applyImport(res, lang);
            close();
            changed(true, t('h_import'));
            toast(t('import_done', {n: res.stats.sections}), {ms: 12000, action: {label: t('import_undo'), run: () => {
                S.cv.profile = before.profile;
                S.cv.content[lang] = before.content;
                changed(true, t('import_undo') + ': ' + t('h_import'));
            }}});
        } catch (e) {
            console.error(e);
            msg.textContent = e.message === 'scanned' ? t('import_scanned') : t('import_failed');
            btn.disabled = false;
            btn.replaceChildren(svg('file'), t('import_choose'));
        }
    });
    close = modal(t('import_title'), h('div.stack', h('p', t('import_body')), h('p.note', h('b', t('import_into', {lang: langLabel(S.cv, lang)})), ' — ', t('import_replace')), h('div', btn, fileIn), msg), [{label: t('cancel')}]);
}

function applyImport(res, lang) {
    const p = S.cv.profile;
    for (const [k, v] of Object.entries(res.profile)) {
        if (k === 'links') {
            if (!p.links?.length) p.links = v;
        } else if (!p[k]) p[k] = v;
    }
    const c = S.cv.content[lang];
    if (res.content.headline) c.headline = res.content.headline;
    if (res.content.summary) c.summary = res.content.summary;
    if (res.content.footer) c.footer = res.content.footer;
    if (res.content.sections.length) c.sections = res.content.sections.map((s) => ({...s, id: uid()}));
}

boot();
