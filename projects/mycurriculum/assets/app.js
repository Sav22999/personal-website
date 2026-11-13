/**
 * MyCurriculum — the app: accounts (email + password with email confirmation, or Google) and the CV editor.
 *
 * The editor keeps the whole CV in S.cv and saves it a moment after each change (cv.save). The form on the left
 * edits one CV language at a time; the preview on the right is the same HTML the public page shows, on an A4 sheet.
 */

import {t, getLang, setLang, UI_LANGS, CV_LANGS, LANG_NAMES, flagSrc, SECTION_KINDS, CONSENT, LANG_LEVELS, CV_WORDS} from './i18n.js';
import {buildModel, renderCV, TEMPLATES, ACCENTS, emptyLang} from './cv.js';
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
    lang: '<path d="M4 5h9M8.5 3v2M6 5c1 4 4 7 7 8M11 5c-1 4-4 7-7 8M13 21l4-9 4 9M14.5 18h5"/>', shared: '<path d="M4 8h14l-3.5-3.5M20 16H6l3.5 3.5"/>', x: '<path d="M6 6l12 12M18 6 6 18"/>', drag: '<path d="M9 6h.01M15 6h.01M9 12h.01M15 12h.01M9 18h.01M15 18h.01"/>',
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

function flag(l) {
    return h('img.flag', {src: flagSrc(l), alt: '', width: 20, height: 15, loading: 'lazy'});
}

/** A row of language buttons with flags (a <select> cannot show images). */
function langPicker(langs, selected, onPick) {
    const box = h('div.lang-pick', {role: 'radiogroup'});
    const draw = () => put(box, langs.map((l) => h('button.chip' + (l === selected ? '.on' : ''), {type: 'button', role: 'radio', 'aria-checked': l === selected ? 'true' : 'false', onclick: () => {
        selected = l;
        draw();
        onPick(l);
    }}, flag(l), LANG_NAMES[l])));
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
    R.viewBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
    if (on) drawPreview();
    if (matchMedia('(max-width: 960px)').matches) window.scrollTo(0, 0);
}

function previewBtnContent() {
    return [svg(S.preview ? 'eyeOff' : 'eye'), h('span.hide-sm', S.preview ? t('preview_hide') : t('preview_show')), h('span.show-sm', S.preview ? t('tab_edit') : t('tab_preview'))];
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

function mountGoogle(slot) {
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
    S.lang = S.cv.settings.langs.includes(S.lang) ? S.lang : S.cv.settings.defaultLang;
    drawEditor();
}

const R = {}; // the parts of the editor that get redrawn

function drawEditor() {
    R.save = h('span.save-state', h('i'), h('em', ''));
    R.langbar = h('div.langbar');
    R.panel = h('div.panel');
    R.preview = h('div.preview');
    R.editor = h('div.editor', {'data-preview': S.preview ? 'on' : 'off'}, R.panel, R.preview);
    R.viewBtn = h('button.btn.btn-sm', {type: 'button', 'aria-pressed': S.preview ? 'true' : 'false', onclick: () => setPreview(!S.preview)}, previewBtnContent());
    put(app, h('header.topbar', brand(), h('span.spacer'), R.save, R.viewBtn, langSwitch(drawEditor), accountMenu()), R.langbar, R.editor);
    setSaveState(S.saveState);
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
            }}, flag(l), LANG_NAMES[l]),
            langs.length > 1 && h('button.chip-x', {type: 'button', title: t('remove_lang') + ' (' + LANG_NAMES[l] + ')', 'aria-label': t('remove_lang') + ' (' + LANG_NAMES[l] + ')', onclick: () => removeLanguage(l)}, svg('x')))),
        langs.length < CV_LANGS.length && h('button.chip.chip-add', {type: 'button', onclick: addLanguage, title: t('add_lang'), 'aria-label': t('add_lang')}, svg('plus'), h('span.hide-sm', t('add_lang')))),
        h('button.btn.btn-sm', {type: 'button', onclick: importDialog, title: t('import_pdf'), 'aria-label': t('import_pdf')}, svg('upload'), h('span.hide-sm', t('import_pdf'))),
        h('button.btn.btn-sm.btn-primary', {type: 'button', onclick: () => makePdf(S.lang), 'aria-label': t('pdf')}, svg('download'), h('span.hide-sm', t('pdf')), h('span.show-sm', 'PDF')));
}

function addLanguage() {
    const free = CV_LANGS.filter((l) => !S.cv.settings.langs.includes(l));
    let picked = free[0];
    const sel = langPicker(free, picked, (l) => (picked = l));
    const from = h('select', S.cv.settings.langs.map((l) => h('option', {value: l, selected: l === S.lang}, LANG_NAMES[l])), h('option', {value: ''}, t('add_lang_empty')));
    modal(t('add_lang'), h('div.stack', h('div.field', h('span', t('cv_lang')), sel), h('label.field', h('span', t('add_lang_from')), from), h('p.note', t('lang_copy_hint'))), [
        {label: t('cancel')},
        {label: t('add_lang'), primary: true, run: (close) => {
            const l = picked;
            S.cv.content[l] = from.value ? translateCopy(S.cv.content[from.value], from.value, l) : freshLang(l);
            S.cv.settings.langs.push(l);
            S.lang = l;
            close();
            changed(true);
            drawLangbar();
        }},
    ]);
}

/** A copy of one language for another: titles and the consent that were never changed are translated. */
function translateCopy(src, from, to) {
    const c = clone(src || emptyLang());
    for (const s of c.sections) {
        const kind = SECTION_KINDS[s.kind];
        if (kind && (!s.title || s.title === kind[from])) s.title = kind[to];
        s.id = s.id || uid();
    }
    if (c.footer && c.footer === CONSENT[from]) c.footer = CONSENT[to];
    if (from !== to) {
        const native = CV_WORDS[from]?.native;
        for (const s of c.sections) if (s.type === 'langs') for (const i of s.items) if (i.level === native) i.level = CV_WORDS[to].native;
    }
    return c;
}

function freshLang(l) {
    const c = emptyLang();
    c.sections = ['experience', 'education', 'skills', 'languages'].map((k) => ({id: uid(), type: SECTION_KINDS[k].type, kind: k, title: SECTION_KINDS[k][l], show: true, text: '', items: []}));
    return c;
}

function removeLanguage(l) {
    confirmBox(t('remove_lang_confirm', {lang: LANG_NAMES[l]}), t('delete'), () => {
        delete S.cv.content[l];
        S.cv.settings.langs = S.cv.settings.langs.filter((x) => x !== l);
        if (S.cv.settings.defaultLang === l) S.cv.settings.defaultLang = S.cv.settings.langs[0];
        if (!S.cv.settings.langs.includes(S.lang)) S.lang = S.cv.settings.langs[0];
        changed(true);
        drawLangbar();
    });
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

/** Something changed: the preview follows at once, the server a moment later. redraw: rebuild the form too. */
function changed(redraw = false) {
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
    dirty = false;
    const payload = {settings: S.cv.settings, profile: S.cv.profile, content: S.cv.content};
    saving = api('cv.save', payload).then(() => setSaveState(dirty ? 'saving' : 'saved')).catch((e) => {
        dirty = true;
        setSaveState('failed');
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
    fetch('api/?r=cv.save', {method: 'POST', keepalive: true, credentials: 'same-origin', headers: {'Content-Type': 'application/json', 'X-CSRF-Token': S.csrf}, body: JSON.stringify({settings: S.cv.settings, profile: S.cv.profile, content: S.cv.content})});
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

function drawPreview() {
    if (!R.preview || !S.preview) return;
    const scroll = R.preview.scrollTop;
    const paper = h('div.paper-scale', {html: renderCV(model(), 'paper')});
    const wrap = h('div.paper-wrap', paper);
    put(R.preview, wrap);
    const fit = () => {
        const avail = R.preview.clientWidth - 48;
        const scale = Math.min(1.1, Math.max(0.3, avail / 794));
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
    if (S.tab === 'content') contentTab(body);
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
        changed();
        opts.onInput?.(el.value);
    });
    if (label) el.setAttribute('aria-label', label);
    // a div, not a label: the show/hide switch next to the title is a label of its own
    return h('div.field', h('span', label, opts.extra || null), el, opts.hint ? h('small', opts.hint) : null);
}

function showSwitch(obj, key, onChange) {
    const input = h('input', {type: 'checkbox', checked: obj[key] !== false});
    input.addEventListener('change', () => {
        obj[key] = input.checked;
        changed();
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

    const links = h('div.stack', (p.links || []).map((l, i) => h('div.kv-row',
        h('input', {value: l.label, placeholder: t('link_label') + ' (LinkedIn…)', oninput: (e) => {
            l.label = e.target.value;
            changed();
        }}),
        h('input', {type: 'url', value: l.url, placeholder: 'https://', oninput: (e) => {
            l.url = e.target.value;
            changed();
        }}),
        h('span'),
        iconBtn('trash', t('delete'), () => {
            p.links.splice(i, 1);
            changed(true);
        }))),
    h('div.row', h('button.btn.btn-sm', {type: 'button', onclick: () => {
        (p.links ||= []).push({label: '', url: ''});
        changed(true);
    }}, svg('plus'), t('add_link')), h('span.grow'), showSwitch(show, 'links')));

    const fields = h('div.stack', c.fields.map((f, i) => h('div.kv-row',
        h('input', {value: f.label, placeholder: t('field_label'), oninput: (e) => {
            f.label = e.target.value;
            changed();
        }}),
        h('input', {value: f.value, placeholder: t('field_value'), oninput: (e) => {
            f.value = e.target.value;
            changed();
        }}),
        h('label.switch', {title: t('show_on_cv')}, h('input', {type: 'checkbox', checked: f.show !== false, onchange: (e) => {
            f.show = e.target.checked;
            changed();
        }})),
        iconBtn('trash', t('delete'), () => {
            c.fields.splice(i, 1);
            changed(true);
        }))),
    h('div', h('button.btn.btn-sm', {type: 'button', onclick: () => {
        c.fields.push({label: '', value: '', show: true});
        changed(true);
    }}, svg('plus'), t('add_field'))));

    const shared = h('span.badge', {title: t('shared_all_langs')}, svg('shared'), t('shared_all_langs'));
    body.append(h('section.card',
        h('div.card-head', h('h3', t('profile'))),
        photoRow,
        h('div.grid2',
            textField(t('first_name'), p, 'firstName', {max: 60}),
            textField(t('last_name'), p, 'lastName', {max: 60}),
            textField(t('headline'), c, 'headline', {max: 120}),
            textField(t('location'), c, 'location', {max: 120, extra: showSwitch(show, 'location')}),
            textField(t('email'), p, 'email', {type: 'email', max: 190, extra: showSwitch(show, 'email')}),
            textField(t('phone'), p, 'phone', {type: 'tel', max: 40, extra: showSwitch(show, 'phone')}),
            textField(t('website'), p, 'website', {type: 'url', max: 300, placeholder: 'https://', extra: showSwitch(show, 'website')}),
            textField(t('birth'), p, 'birthDate', {type: 'date', extra: showSwitch(show, 'birth')})),
        h('div.field', {style: 'margin-top:14px'}, h('span', t('links')), links),
        h('div.field', {style: 'margin-top:14px'}, h('span', t('custom_fields')), h('small', t('custom_fields_hint')), fields),
        h('p.note', {style: 'margin-top:12px'}, shared, ' ', t('first_name') + ', ' + t('last_name').toLowerCase() + ', ' + t('email').toLowerCase() + ', ' + t('phone').toLowerCase() + '…')));

    body.append(h('section.card', h('div.card-head', h('h3', t('summary'))), textField('', c, 'summary', {area: true, rows: 4, max: 4000, hint: t('summary_hint')})));

    c.sections.forEach((s, idx) => body.append(sectionCard(c, s, idx)));

    body.append(h('section.card', h('div.card-head', h('h3', t('add_section'))),
        h('div.add-sec', Object.keys(SECTION_KINDS).map((k) => h('button.btn.btn-sm', {type: 'button', onclick: () => {
            const sec = {id: uid(), type: SECTION_KINDS[k].type, kind: k, title: SECTION_KINDS[k][S.lang] || '', show: true, text: '', items: []};
            if (sec.type !== 'text') sec.items.push(newItem(sec.type));
            c.sections.push(sec);
            changed(true);
            requestAnimationFrame(() => R.panel.querySelector(`[data-sec="${sec.id}"]`)?.scrollIntoView({behavior: 'smooth', block: 'center'}));
        }}, svg('plus'), t('k_' + k))))));

    const footer = textField('', c, 'footer', {area: true, rows: 3, max: 1000, hint: t('footer_hint')});
    body.append(h('section.card', h('div.card-head', h('h3', t('footer')),
        h('button.btn.btn-sm', {type: 'button', onclick: () => {
            c.footer = CONSENT[S.lang] || CONSENT.en;
            changed(true);
        }}, t('footer_consent'))), footer));

    if (S.cv.settings.langs.length > 1) body.append(h('div', h('button.btn.btn-sm.btn-ghost.btn-danger', {type: 'button', onclick: () => removeLanguage(S.lang)}, svg('trash'), t('remove_lang') + ' (' + LANG_NAMES[S.lang] + ')')));
}

function newItem(type) {
    if (type === 'timeline') return {title: '', org: '', place: '', start: '', end: '', current: false, text: ''};
    if (type === 'skills') return {name: '', level: 0};
    if (type === 'langs') return {name: '', level: ''};
    return {title: '', subtitle: '', date: '', url: '', text: ''};
}

function move(arr, i, d) {
    const j = i + d;
    if (j < 0 || j >= arr.length) return;
    [arr[i], arr[j]] = [arr[j], arr[i]];
    changed(true);
}

function sectionCard(c, s, idx) {
    const titleIn = h('input', {value: s.title, placeholder: t('section_title'), 'aria-label': t('section_title'), maxlength: 80, oninput: (e) => {
        s.title = e.target.value;
        changed();
    }});
    const eye = iconBtn(s.show !== false ? 'eye' : 'eyeOff', s.show !== false ? t('show_on_cv') : t('section_hidden'), () => {
        s.show = s.show === false;
        changed(true);
    });
    const card = h('section.card.sec-card' + (s.show === false ? '.is-hidden' : ''), {'data-sec': s.id},
        h('div.card-head', h('div.grow', {style: 'flex:1;min-width:0'}, titleIn), eye,
            iconBtn('up', t('move_up'), () => move(c.sections, idx, -1)),
            iconBtn('down', t('move_down'), () => move(c.sections, idx, 1)),
            iconBtn('trash', t('delete'), () => confirmBox((s.title || t('k_' + s.kind)) + ' — ' + t('delete') + '?', t('delete'), () => {
                c.sections.splice(idx, 1);
                changed(true);
            }))));
    if (s.type === 'text') {
        card.append(textField('', s, 'text', {area: true, rows: 4, max: 4000}));
        return card;
    }
    const items = h('div.items');
    if (s.type === 'skills' || s.type === 'langs') {
        const dl = s.type === 'langs' ? h('datalist', {id: 'lv-' + s.id}, [CV_WORDS[S.lang]?.native, ...LANG_LEVELS].map((v) => h('option', {value: v}))) : null;
        s.items.forEach((it, i) => items.append(h('div.tag-row',
            h('input', {value: it.name, placeholder: s.type === 'skills' ? t('t_skill') : t('t_lang'), maxlength: 80, oninput: (e) => {
                it.name = e.target.value;
                changed();
            }}),
            s.type === 'skills' ? levelPicker(it) : h('input', {value: it.level, placeholder: t('t_level'), list: 'lv-' + s.id, maxlength: 60, oninput: (e) => {
                it.level = e.target.value;
                changed();
            }}),
            h('div.row', {style: 'gap:0'}, iconBtn('up', t('move_up'), () => move(s.items, i, -1)), iconBtn('trash', t('delete'), () => {
                s.items.splice(i, 1);
                changed(true);
            })))));
        if (dl) card.append(dl);
    } else {
        s.items.forEach((it, i) => items.append(itemCard(s, it, i)));
    }
    card.append(items, h('div', {style: 'margin-top:10px'}, h('button.btn.btn-sm', {type: 'button', onclick: () => {
        s.items.push(newItem(s.type));
        changed(true);
        requestAnimationFrame(() => {
            const inputs = R.panel.querySelectorAll(`[data-sec="${s.id}"] .items input`);
            inputs[inputs.length - (s.type === 'skills' || s.type === 'langs' ? (s.type === 'langs' ? 2 : 1) : 1)]?.focus();
        });
    }}, svg('plus'), t('add_item'))));
    return card;
}

function levelPicker(it) {
    const box = h('div.level', {role: 'radiogroup', 'aria-label': t('t_level')});
    const draw = () => put(box, [1, 2, 3, 4, 5].map((n) => h('button' + (n <= it.level ? '.on' : ''), {type: 'button', 'aria-label': n + '/5', 'aria-checked': n === it.level ? 'true' : 'false', role: 'radio', onclick: () => {
        it.level = it.level === n ? 0 : n;
        draw();
        changed();
    }})));
    draw();
    return box;
}

function itemCard(s, it, i) {
    const key = s.id + ':' + i;
    const label = () => (s.type === 'timeline' ? [it.title, it.org].filter(Boolean).join(' · ') : it.title) || '…';
    const lab = h('b', label());
    const card = h('div.item' + (S.collapsed.has(key) ? '.collapsed' : ''));
    const head = h('div.item-head', iconBtn(S.collapsed.has(key) ? 'down' : 'up', '', () => {
        S.collapsed.has(key) ? S.collapsed.delete(key) : S.collapsed.add(key);
        card.classList.toggle('collapsed');
    }), lab,
    iconBtn('up', t('move_up'), () => move(s.items, i, -1)),
    iconBtn('down', t('move_down'), () => move(s.items, i, 1)),
    iconBtn('trash', t('delete'), () => {
        s.items.splice(i, 1);
        changed(true);
    }));
    const relabel = () => (lab.textContent = label());
    card.append(head);
    if (s.type === 'timeline') {
        const endField = textField(t('t_end'), it, 'end', {max: 20, placeholder: t('date_hint')});
        endField.querySelector('input').disabled = !!it.current;
        const cur = h('label.check', {style: 'font-size:13px'}, h('input', {type: 'checkbox', checked: !!it.current, onchange: (e) => {
            it.current = e.target.checked;
            endField.querySelector('input').disabled = it.current;
            changed();
        }}), h('span', t('t_current')));
        card.append(
            textField(t('t_role'), it, 'title', {max: 120, onInput: relabel}),
            h('div.grid2', textField(t('t_org'), it, 'org', {max: 120, onInput: relabel}), textField(t('t_place'), it, 'place', {max: 80})),
            h('div.grid2', textField(t('t_start'), it, 'start', {max: 20, placeholder: t('date_hint')}), endField),
            cur,
            textField(t('t_text'), it, 'text', {area: true, rows: 3, max: 3000, hint: '- …'}));
    } else {
        card.append(
            h('div.grid2', textField(t('t_title'), it, 'title', {max: 160, onInput: relabel}), textField(t('t_subtitle'), it, 'subtitle', {max: 160})),
            h('div.grid2', textField(t('t_date'), it, 'date', {max: 40}), textField(t('t_url'), it, 'url', {type: 'url', max: 300, placeholder: 'https://'})),
            textField(t('t_text'), it, 'text', {area: true, rows: 2, max: 2000}));
    }
    return card;
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
            changed();
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
                changed(true);
            }}, thumb, t('tpl_' + tp));
        }))));
    const custom = h('input', {type: 'color', value: st.accent, 'aria-label': t('design_color'), oninput: (e) => {
        st.accent = e.target.value.toUpperCase();
        changed();
        body.querySelectorAll('.swatch').forEach((s) => s.classList.toggle('on', s.dataset.c === st.accent));
    }});
    body.append(h('section.card', h('div.card-head', h('h3', t('design_color'))),
        h('div.swatches', ACCENTS.map((c) => h('button.swatch' + (c === st.accent ? '.on' : ''), {type: 'button', 'data-c': c, style: `background:${c}`, 'aria-label': c, onclick: () => {
            st.accent = c;
            changed(true);
        }})), h('label.swatch.swatch-custom' + (ACCENTS.includes(st.accent) ? '' : '.on'), {title: t('design_color')}, custom))));
    body.append(h('section.card', h('div.card-head', h('h3', t('design_font'))),
        h('div.seg', [['sans', t('font_sans')], ['serif', t('font_serif')]].map(([k, l]) => h('button' + (st.font === k ? '.on' : ''), {type: 'button', style: `font-family:${k === 'serif' ? '"CV Serif",Georgia,serif' : 'inherit'}`, onclick: () => {
            st.font = k;
            changed(true);
        }}, l)))));
}

function drawThumbs() {
    if (!R.thumbs) return;
    for (const [tp, el] of Object.entries(R.thumbs)) {
        if (!el.isConnected) continue;
        const cv = {...S.cv, settings: {...S.cv.settings, template: tp}};
        el.innerHTML = renderCV(model(S.lang, cv), 'paper');
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
        changed(true);
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
            h('a.btn.btn-ghost.btn-icon', {href: url, target: '_blank', rel: 'noopener', title: t('share_open'), 'aria-label': t('share_open')}, svg('ext'))),
        h('div.stack',
            usernameEditor(),
            h('div.field', h('span', t('share_default_lang')), langPicker(st.langs, st.defaultLang, (l) => {
                st.defaultLang = l;
                changed();
            })),
            h('label.check', h('input', {type: 'checkbox', checked: st.allowPdf, onchange: (e) => {
                st.allowPdf = e.target.checked;
                changed();
            }}), h('span', t('share_allow_pdf'))),
            h('p.note', t('share_views', {n: S.cv.views || 0}) + ' · ' + t('share_note')))));

    body.append(h('section.card', h('div.card-head', h('h3', t('pdf'))),
        h('div.pdf-list', st.langs.map((l) => h('button.btn', {type: 'button', onclick: () => makePdf(l)}, svg('download'), flag(l), LANG_NAMES[l])))));

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
            changed(true);
            toast(t('import_done', {n: res.stats.sections}), {ms: 12000, action: {label: t('import_undo'), run: () => {
                S.cv.profile = before.profile;
                S.cv.content[lang] = before.content;
                changed(true);
            }}});
        } catch (e) {
            console.error(e);
            msg.textContent = e.message === 'scanned' ? t('import_scanned') : t('import_failed');
            btn.disabled = false;
            btn.replaceChildren(svg('file'), t('import_choose'));
        }
    });
    close = modal(t('import_title'), h('div.stack', h('p', t('import_body')), h('p.note', h('b', t('import_into', {lang: LANG_NAMES[lang]})), ' — ', t('import_replace')), h('div', btn, fileIn), msg), [{label: t('cancel')}]);
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
