/*
 * Forkville — the app: accounts (email + password with mandatory email confirmation, or Google),
 * the HUD over the 3D world, build mode, menu and recipes, garden, market, staff, friends, visits,
 * rankings and the account panel.
 */
import {World} from './scene.js';
import {Sim, dishIcon, isNight, hourMood} from './sim.js';
import {Editor} from './editor.js';
import {swatch} from './models.js';
import {t, n, errText, getLang, setLang} from './i18n.js';
import {icon as svgIcon} from './icons.js';
import {thumbFor, portrait, doorThumb} from './thumbs.js';
import {weatherFor} from './scene.js';
import {holidayAt} from './town.js';
import {start as startAudio, sfx, setAmbience, audioPrefs, setAudioPrefs} from './audio.js';

/** An SVG picture as an element (never emoji). */
const ic = (group, key, cls = 'pic') => h('span.' + cls, {html: svgIcon(group, key)});

const S = {user: null, csrf: '', config: null, catalog: null, game: null, mode: 'home', visit: null, world: null, sim: null, editor: null, sheet: null};
const app = document.getElementById('app');

// ---------------------------------------------------------------- helpers

/** Tiny element builder: h('div.card', {onclick}, child, …). Strings become text; {html} sets innerHTML. */
function h(tag, attrs, ...kids) {
    let id = null;
    const hash = tag.indexOf('#');
    if (hash >= 0) { // 'div.a#b.c' → id b, classes a c
        const rest = tag.slice(hash + 1), dot = rest.indexOf('.');
        id = dot < 0 ? rest : rest.slice(0, dot);
        tag = tag.slice(0, hash) + (dot < 0 ? '' : rest.slice(dot));
    }
    const [name, ...cls] = tag.split('.');
    const el = document.createElement(name || 'div');
    if (id) el.id = id;
    if (cls.length) el.className = cls.join(' ');
    if (attrs && (typeof attrs !== 'object' || attrs instanceof Node || Array.isArray(attrs))) {
        kids.unshift(attrs);
        attrs = null;
    }
    for (const [k, v] of Object.entries(attrs || {})) {
        if (v == null || v === false) continue;
        if (k === 'html') el.innerHTML = v;
        else if (k.startsWith('on')) el.addEventListener(k.slice(2), v);
        else if (k === 'class') el.className += ' ' + v;
        else el.setAttribute(k, v === true ? '' : v);
    }
    for (const kid of kids.flat()) if (kid != null && kid !== false) el.append(kid instanceof Node ? kid : document.createTextNode(String(kid)));
    return el;
}

/** replaceChildren that skips null/false (conditional children), which the DOM would print as text. */
function put(el, ...kids) {
    el.replaceChildren(...kids.flat().filter(k => k != null && k !== false));
}

/** This copy of the game: only the last one opened may play (see client_instance in api/index.php). */
const INSTANCE = Array.from(crypto.getRandomValues(new Uint8Array(8)), b => (b % 36).toString(36)).join('') + Date.now().toString(36).slice(-6);
let channel = null;
try {
    channel = new BroadcastChannel('forkville');
    channel.onmessage = (e) => e.data?.type === 'claim' && e.data.id !== INSTANCE && S.game && elsewhere();
} catch (e) { /* old browser: the server check is enough */ }

/** The game was opened in another tab or device: this copy stops and offers to take over. */
function elsewhere() {
    if (S.elsewhere) return;
    S.elsewhere = true;
    try {
        S.sim?.stop();
        S.editor?.active && S.editor.end();
    } catch (e) { /* stopping anyway */ }
    document.querySelector('.modal-bg')?.remove();
    const el = h('div.elsewhere', h('div.modal', h('h2', t('elsewhere_title')), h('p', t('elsewhere_body')),
        h('button.btn.btn-primary', {style: 'width:100%', onclick: () => location.reload()}, t('play_here'))));
    document.body.append(el);
}

async function api(route, body) {
    const opts = {method: body === undefined ? 'GET' : 'POST', credentials: 'same-origin', headers: {'X-Instance': INSTANCE}};
    if (body !== undefined) {
        opts.headers['Content-Type'] = 'application/json';
        if (S.csrf) opts.headers['X-CSRF-Token'] = S.csrf;
        opts.body = JSON.stringify(body);
    }
    let res;
    try {
        res = await fetch('api/index.php?r=' + route, opts);
    } catch (e) {
        throw new ApiError('network');
    }
    const data = await res.json().catch(() => ({ok: false, error: 'server'}));
    if (data.csrf) S.csrf = data.csrf;
    if (data.error === 'other_instance') elsewhere();
    if (!data.ok && data.error !== 'needs_signup') throw new ApiError(data.error || 'server', data);
    return data;
}

class ApiError extends Error {
    constructor(code, data) {
        super(code);
        this.code = code;
        this.data = data;
    }
}

function toast(msg, kind = '') {
    const el = h('div.toast' + (kind ? '.' + kind : ''), msg);
    document.getElementById('toasts').append(el);
    setTimeout(() => el.classList.add('out'), 2600);
    setTimeout(() => el.remove(), 3100);
}

const fail = (e) => toast(errText(e.code || 'server'), 'bad');

function fmtTime(sec) {
    sec = Math.max(0, Math.round(sec));
    if (sec < 60) return sec + 's';
    if (sec < 3600) return Math.floor(sec / 60) + 'm ' + (sec % 60 ? (sec % 60) + 's' : '');
    return Math.floor(sec / 3600) + 'h ' + Math.floor((sec % 3600) / 60) + 'm';
}

/** Stars drawn as SVG (filled, then empty up to max). */
const starsEl = (filled, max = 0) => h('span.stars-svg', Array.from({length: Math.max(filled, max)}, (_, i) => h('span.star-pic' + (i < filled ? '' : '.empty'), {html: svgIcon('mood', 'star')})));
const levelTitle = (lvl) => {
    let k = 'kiosk';
    for (const [from, key] of S.catalog.titles || []) if (lvl >= from) k = key;
    return t('title_' + k);
};
const coin = (c) => h('span.coin-amount', h('i.coin'), String(c));
const ingList = (obj) => Object.entries(obj).map(([k, q]) => `${n(k)} ×${q}`).join(', ');
const nowSec = () => Date.now() / 1000 + (S.clockSkew || 0);

const LOGO = `<svg aria-hidden="true" viewBox="0 0 64 64"><defs><linearGradient id="fvbg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#33264F"/><stop offset="1" stop-color="#171230"/></linearGradient><radialGradient id="fvglow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#FF9A4A" stop-opacity=".55"/><stop offset="1" stop-color="#FF9A4A" stop-opacity="0"/></radialGradient><linearGradient id="fvroof" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FF8A4C"/><stop offset="1" stop-color="#E0483C"/></linearGradient><linearGradient id="fvwall" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFF6E6"/><stop offset="1" stop-color="#F2D7B4"/></linearGradient><linearGradient id="fvgold" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#FFE08A"/><stop offset="1" stop-color="#F2B33D"/></linearGradient><filter id="fvblur" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="1.6"/></filter></defs><rect width="64" height="64" rx="15" fill="url(#fvbg)"/><circle cx="32" cy="36" r="27" fill="url(#fvglow)"/><ellipse cx="32" cy="53.5" rx="20" ry="2.4" fill="#0C0918" opacity=".55"/><g fill="url(#fvgold)"><rect x="30.7" y="14" width="2.6" height="9" rx="1.3"/><path d="M25.3 10.2h13.4v1.2c0 2.9-3 4.9-6.7 4.9s-6.7-2-6.7-4.9z"/><rect x="25.3" y="3.6" width="2.3" height="8.4" rx="1.15"/><rect x="29.15" y="3.6" width="2.3" height="8.4" rx="1.15"/><rect x="32.55" y="3.6" width="2.3" height="8.4" rx="1.15"/><rect x="36.4" y="3.6" width="2.3" height="8.4" rx="1.15"/></g><path d="M12.6 30.4L32 18.6l19.4 11.8a1.6 1.6 0 0 1-.83 2.97H13.43a1.6 1.6 0 0 1-.83-2.97z" fill="url(#fvroof)"/><rect x="16" y="32" width="32" height="21" rx="1.6" fill="url(#fvwall)"/><g><path d="M15 33.4h34v3.6H15z" fill="#E0483C"/><path d="M15 37h4.25a2.13 2.13 0 0 1-4.25 0zM23.5 37h4.25a2.13 2.13 0 0 1-4.25 0zM32 37h4.25a2.13 2.13 0 0 1-4.25 0zM40.5 37h4.25a2.13 2.13 0 0 1-4.25 0z" fill="#E0483C"/><path d="M19.25 33.4h4.25V37h-4.25zM27.75 33.4H32V37h-4.25zM36.25 33.4h4.25V37h-4.25zM44.75 33.4H49V37h-4.25z" fill="#FFF6E6"/><path d="M19.25 37h4.25a2.13 2.13 0 0 1-4.25 0zM27.75 37H32a2.13 2.13 0 0 1-4.25 0zM36.25 37h4.25a2.13 2.13 0 0 1-4.25 0zM44.75 37H49a2.13 2.13 0 0 1-4.25 0z" fill="#FFF6E6"/></g><rect x="18.6" y="42" width="7.6" height="6.6" rx="1.2" fill="#FFC857" filter="url(#fvblur)" opacity=".9"/><rect x="18.6" y="42" width="7.6" height="6.6" rx="1.2" fill="#FFD77A"/><rect x="37.8" y="42" width="7.6" height="6.6" rx="1.2" fill="#FFC857" filter="url(#fvblur)" opacity=".9"/><rect x="37.8" y="42" width="7.6" height="6.6" rx="1.2" fill="#FFD77A"/><path d="M22.4 42v6.6M18.6 45.3h7.6M41.6 42v6.6M37.8 45.3h7.6" stroke="#E9A93A" stroke-width=".8"/><path d="M28.4 53V44.4a3.6 3.6 0 0 1 7.2 0V53z" fill="#7A3B2A"/><circle cx="34" cy="48.8" r=".7" fill="#FFD77A"/><path d="M51.5 10.5l.9 2.4 2.4.9-2.4.9-.9 2.4-.9-2.4-2.4-.9 2.4-.9z" fill="#FFE08A"/><circle cx="11.5" cy="15" r="1.1" fill="#FFE08A" opacity=".85"/><circle cx="54" cy="24" r=".8" fill="#FFE08A" opacity=".6"/></svg>`;

function credit() {
    return h('div.credit-block',
        h('a.credit', {href: 'https://www.saveriomorelli.com/', target: '_blank', rel: 'noopener'}, h('span', t('credit_by')), h('span.credit-name', 'Saverio Morelli')),
        h('span.credit-ai', t('credit_ai')));
}

// ---------------------------------------------------------------- boot

async function boot() {
    setLang(getLang());
    put(app, h('div.splash', h('div.logo-big', {html: LOGO}), h('p', t('loading'))));
    const verify = new URLSearchParams(location.search).get('verify');
    try {
        S.config = await api('auth.config');
    } catch (e) {
        return put(app, h('div.splash', h('div.logo-big', {html: LOGO}), h('p', errText(e.code))));
    }
    if (!S.config.installed) return put(app, h('div.splash', h('div.logo-big', {html: LOGO}), h('p', errText('not_installed'))));
    if (verify && /^[a-f0-9]{64}$/.test(verify)) return verifyLinkScreen(verify);
    const me = await api('auth.me').catch(() => ({user: null}));
    S.user = me.user;
    route();
}

function route() {
    if (!S.user) return authScreen('signup');
    if (S.user.mustVerify) return verifyScreen();
    startGame();
}

// ---------------------------------------------------------------- accounts

function authScreen(mode) {
    const err = h('p.form-error');
    const field = (name, label, type = 'text', extra = {}) => h('label.field', h('span', label), h('input', {name, type, required: true, ...extra}));
    const form = h('form.auth-form', {novalidate: true},
        mode === 'signup' && field('name', t('name'), 'text', {autocomplete: 'name', maxlength: 80}),
        mode === 'signup' && h('label.field', h('span', t('username')), h('input', {name: 'username', required: true, autocomplete: 'username', maxlength: 20, pattern: '[A-Za-z0-9_]{3,20}'}), h('small', t('username_hint'))),
        field('email', t('email'), 'email', {autocomplete: 'email'}),
        h('label.field', h('span', t('password')), h('input', {name: 'password', type: 'password', required: true, autocomplete: mode === 'signup' ? 'new-password' : 'current-password'}), mode === 'signup' && h('small', t('password_hint'))),
        mode === 'signup' && h('label.check', h('input', {type: 'checkbox', name: 'privacy'}), h('span', {html: t('privacy_accept')})),
        err,
        h('button.btn.btn-primary.btn-wide', {type: 'submit'}, mode === 'signup' ? t('signup') : t('signin')));
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = new FormData(form);
        const btn = form.querySelector('button[type=submit]');
        btn.disabled = true;
        err.textContent = '';
        try {
            const body = {email: f.get('email'), password: f.get('password'), lang: getLang()};
            if (mode === 'signup') Object.assign(body, {name: f.get('name'), username: f.get('username'), acceptPrivacy: !!f.get('privacy')});
            const d = await api(mode === 'signup' ? 'auth.register' : 'auth.login', body);
            // Signing up never signs in: the link in the email does (the answer is the same for a known address)
            if (d.checkEmail) return checkEmailScreen(body.email);
            S.user = d.user;
            route();
        } catch (ex) {
            err.textContent = errText(ex.code);
            btn.disabled = false;
        }
    });
    const google = h('div.google-slot');
    put(app, h('main.auth',
        h('div.auth-hero', h('div.logo-big', {html: LOGO}), h('h1', 'Forkville'), h('p.tagline', t('tagline'))),
        h('div.auth-card',
            h('div.tabs', h('button.tab' + (mode === 'signup' ? '.on' : ''), {type: 'button', onclick: () => authScreen('signup')}, t('signup')),
                h('button.tab' + (mode === 'signin' ? '.on' : ''), {type: 'button', onclick: () => authScreen('signin')}, t('signin'))),
            google, S.config.googleClientId ? h('div.or', h('span', t('or'))) : null, form),
        langSwitch(() => authScreen(mode)), credit()));
    if (S.config.googleClientId) mountGoogle(google);
}

function langSwitch(rerender) {
    return h('div.lang-switch', ['it', 'en'].map(l => h('button' + (getLang() === l ? '.on' : ''), {type: 'button', onclick: () => (setLang(l), rerender())}, l.toUpperCase())));
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

async function googleSignIn(credential, extra = {}) {
    try {
        const d = await api('auth.google', {credential, lang: getLang(), ...extra});
        if (d.error === 'needs_signup') return googleFinish(credential, d.name);
        S.user = d.user;
        closeModal();
        route();
    } catch (e) {
        fail(e);
    }
}

function googleFinish(credential) {
    const err = h('p.form-error');
    const form = h('form.auth-form',
        h('label.field', h('span', t('username')), h('input', {name: 'username', required: true, maxlength: 20}), h('small', t('username_hint'))),
        h('label.check', h('input', {type: 'checkbox', name: 'privacy'}), h('span', {html: t('privacy_accept')})), err,
        h('div.row', h('button.btn', {type: 'button', onclick: closeModal}, t('cancel')), h('button.btn.btn-primary', {type: 'submit'}, t('continue'))));
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = new FormData(form);
        try {
            const d = await api('auth.google', {credential, username: f.get('username'), acceptPrivacy: !!f.get('privacy'), lang: getLang()});
            S.user = d.user;
            closeModal();
            route();
        } catch (ex) {
            err.textContent = errText(ex.code);
        }
    });
    modal(t('google_finish'), [h('p', t('google_finish_body')), form]);
}

/** Right after signing up: no session yet, the link in the email confirms the address and signs in. */
function checkEmailScreen(email) {
    put(app, h('main.auth', h('div.auth-card.verify',
        h('div.mail-art', {html: MAIL_ART}),
        h('h1', t('verify_title')), h('p', {html: t('verify_body', {email: escapeHtml(email)})}),
        !S.config.mailReady && h('p.note.bad', t('verify_nomail')),
        h('p.note', t('verify_signin_hint')),
        h('button.btn.btn-primary.btn-wide', {onclick: () => authScreen('signin')}, t('signin'))), credit()));
}

const MAIL_ART = '<svg viewBox="0 0 120 90"><rect x="8" y="14" width="104" height="68" rx="10" fill="#FFF3E0" stroke="#3B2418" stroke-width="4"/><path d="M12 20l48 34 48-34" fill="none" stroke="#D9653B" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="100" cy="18" r="13" fill="#7A8B3A"/><path d="M94 18l4 4 8-8" stroke="#fff" stroke-width="3.5" fill="none" stroke-linecap="round"/></svg>';

/** The gate: an account made with email and password cannot play before confirming the address. */
function verifyScreen() {
    const msg = h('p.note');
    const resend = h('button.btn', {
        onclick: async () => {
            resend.disabled = true;
            try {
                await api('auth.resend', {lang: getLang()});
                msg.textContent = t('verify_sent');
            } catch (e) {
                msg.textContent = errText(e.code);
            }
            setTimeout(() => (resend.disabled = false), 30000);
        },
    }, t('verify_resend'));
    put(app, h('main.auth', h('div.auth-card.verify',
        h('div.mail-art', {html: MAIL_ART}),
        h('h1', t('verify_title')), h('p', {html: t('verify_body', {email: escapeHtml(S.user.email)})}),
        !S.user.mailReady && h('p.note.bad', t('verify_nomail')),
        h('button.btn.btn-primary.btn-wide', {
            onclick: async () => {
                const me = await api('auth.me').catch(() => null);
                if (me?.user && !me.user.mustVerify) {
                    S.user = me.user;
                    return route();
                }
                msg.textContent = t('verify_still');
            },
        }, t('verify_check')),
        resend, msg,
        h('button.btn.btn-ghost', {onclick: logout}, t('logout'))), credit()));
}

function verifyLinkScreen(token) {
    const msg = h('p.form-error');
    const btn = h('button.btn.btn-primary.btn-wide', {
        onclick: async () => {
            btn.disabled = true;
            try {
                const d = await api('auth.verify', {token});
                S.user = d.user;
                history.replaceState(null, '', location.pathname);
                toast(t('verify_done'), 'good');
                route();
            } catch (e) {
                msg.textContent = errText(e.code);
                btn.disabled = false;
            }
        },
    }, t('verify_link_btn'));
    put(app, h('main.auth', h('div.auth-card.verify', h('div.logo-big', {html: LOGO}), h('h1', t('verify_link_title')), h('p', t('verify_link_body')), btn, msg)));
}

const escapeHtml = (s) => String(s).replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));

async function logout() {
    try {
        await api('auth.logout', {});
    } catch (e) { /* signed out anyway */ }
    location.href = location.pathname;
}

// ---------------------------------------------------------------- the game

async function startGame() {
    put(app, h('div.splash', h('div.logo-big', {html: LOGO}), h('p', t('loading'))));
    try {
        const [cat, st] = await Promise.all([api('game.catalog'), api('game.state', {})]);
        S.catalog = cat.catalog;
        applyState(st);
        channel?.postMessage({type: 'claim', id: INSTANCE}); // other tabs of this browser step aside at once
    } catch (e) {
        if (e.code === 'email_unverified') {
            S.user.mustVerify = true;
            return verifyScreen();
        }
        return put(app, h('div.splash', h('div.logo-big', {html: LOGO}), h('p', errText(e.code)), h('button.btn', {onclick: () => location.reload()}, 'OK')));
    }
    const canvas = h('canvas#world');
    const overlay = h('div.overlay');
    put(app, h('div.game', canvas, overlay, h('div#hud')));
    S.world = new World(canvas, overlay);
    S.world.catalogItems = S.catalog.items;
    S.world.on('thunder', () => sfx.thunder());
    S.sim = new Sim(S.world, S.catalog, {onServe: (coins) => bumpCoins(coins)});
    S.sim.clock = S.game.restaurant.gameMinutes || 540; // the restaurant's own calendar, saved on the server
    S.season = gameDate(S.sim.clock).season;
    S.world.on('frame', envTick);
    S.sim.onTick = () => {
        const now = performance.now();
        if (now - (S.lastHudTick || 0) < 250) return;
        S.lastHudTick = now;
        renderClock();
        renderInfo();
    };
    S.editor = new Editor(S.world, S.catalog, {changed: renderBuild, selected: renderSelection, toast: (k) => toast(t(k), 'bad'), coins: () => S.game.restaurant.coins, name: n, text: t, placed: () => sfx.pop?.(), moveDoor: (x) => act('game.door', {x}, {done: () => {
        S.editor.end();
        enterBuild(buildTab);
        toast(t('door_moved'), 'good');
    }})});
    S.world.on('tap', onWorldTap);
    loadHome();
    renderHud();
    if (S.game.away) showAway(S.game.away);
    // Levels earned while away are celebrated too (after the summary)
    if (S.game.away?.levelTo > S.game.away?.levelFrom) queueModal(() => showLevelUp(S.game.away.levelFrom, S.game.away.levelTo));
    if (S.game.wages?.paid) setTimeout(() => toast(t('wages_paid', {c: S.game.wages.paid})), 800);
    setInterval(sendReport, 25000);
    setInterval(tick, 1000);
    document.addEventListener('visibilitychange', () => document.visibilityState === 'hidden' && sendReport(true));
}

function applyState(st) {
    const prevLevel = S.game?.restaurant.level;
    S.game = st;
    S.clockSkew = st.now - Date.now() / 1000;
    S.liveCoins = 0;
    if (S.sim?.r && S.mode === 'home') {
        S.sim.r.popularity = st.restaurant.popularity;
        if (S.sim.r.status !== st.restaurant.status) S.sim.setStatus(st.restaurant.status);
    }
    if (prevLevel && st.restaurant.level > prevLevel) setTimeout(() => queueModal(() => showLevelUp(prevLevel, st.restaurant.level)), 400);
}

function simRestaurant(r) {
    return {size: r.size, depth: r.depth || r.size, floors: r.floors || [], door: r.door, kind: r.kind, layout: r.layout, staff: r.staff, menu: r.menu, popularity: r.popularity, status: r.status || 'open'};
}

/** Every frame: the game clock drives day and night, the weather changes each game day, the season follows the calendar. */
function envTick() {
    if (!S.sim) return;
    // The game calendar (not the real one): seasons and weather follow the restaurant's own days
    const date = gameDate(S.sim.clock);
    S.season = date.season;
    S.weather = weatherFor(S.season, date.dayIndex, date.hour);
    S.sim.weather = S.weather;
    const ev = currentEvent();
    if (S.lastEvent !== undefined && ev && ev !== S.lastEvent) toast(t('ev_started', {e: t('ev_' + ev)}), 'good');
    S.lastEvent = ev;
    S.sim.season = S.season;
    // Holidays of the game calendar dress up the neighbourhood
    const hol = holidayAt(date.month, date.day);
    if (hol !== S.holiday) {
        if (S.holiday !== undefined && hol) toast(t('hol_started', {h: t('hol_' + hol)}), 'good');
        S.holiday = hol;
        S.world.setHoliday(hol);
    }
    S.world.setEnvironment({hour: S.sim.hour, weather: S.weather, season: S.season});
    const now = performance.now();
    if (now - (S.lastAmb || 0) > 500) { // rain, voices in the room, day or night music
        S.lastAmb = now;
        setAmbience({rain: ['drizzle', 'rain', 'hail'].includes(S.weather), snow: ['light_snow', 'snow'].includes(S.weather), guests: S.sim.agents.filter(a => a.role === 'customer' && a.seat).length, isNight: isNight(S.sim.hour)});
    }
}

/**
 * The restaurant's calendar from its game minutes: year 0 starts on 1 March at the opening;
 * months of DAYS_PER_MONTH days, the usual month names, seasons of three months.
 */
function gameDate(total) {
    const per = S.catalog?.calendar?.daysPerMonth || 10;
    const dayIndex = Math.floor(total / 1440);
    const year = Math.floor(dayIndex / (per * 12));
    const inYear = dayIndex % (per * 12);
    const month = (2 + Math.floor(inYear / per)) % 12; // 0 = January; the first month is March
    const day = inYear % per + 1;
    const season = [11, 0, 1].includes(month) ? 'winter' : month <= 4 ? 'spring' : month <= 7 ? 'summer' : 'autumn';
    const hour = Math.floor((total % 1440) / 60), minute = Math.floor(total % 60);
    return {dayIndex, year, month, day, season, hour, minute, per};
}

const monthName = (m) => new Date(2001, m, 1).toLocaleString(getLang() === 'it' ? 'it-IT' : 'en-GB', {month: 'long'});

/** A calendar page: time, day, month, year, season, the days of the month and how far the season has gone. */
function openCalendar() {
    const d = gameDate(S.sim.clock);
    const seasonStart = {spring: 2, summer: 5, autumn: 8, winter: 11}[d.season];
    const monthInSeason = (d.month - seasonStart + 12) % 12;
    const dayInSeason = monthInSeason * d.per + d.day;
    const next = {spring: 'summer', summer: 'autumn', autumn: 'winter', winter: 'spring'}[d.season];
    const pad = (x) => String(x).padStart(2, '0');
    modal(t('calendar_title'), [
        h('div.calendar',
            h('div.cal-page',
                h('div.cal-month', monthName(d.month) + ' · ' + t('year_n', {n: d.year})),
                h('div.cal-day', String(d.day)),
                h('div.cal-time', `${pad(d.hour)}:${pad(d.minute)}`)),
            h('div.cal-info',
                h('div.cal-season', h('span.pic', {html: svgIcon('ui', 's_' + d.season)}), h('b', t('season_' + d.season)),
                    h('span.pic', {html: svgIcon('ui', 'w_' + (S.weather || 'clear'))}), h('span.muted', t('weather_' + (S.weather || 'clear')))),
                h('div.cal-grid', Array.from({length: d.per}, (_, i) => h('span.cal-cell' + (i + 1 === d.day ? '.today' : i + 1 < d.day ? '.past' : ''), String(i + 1)))),
                h('div.cal-progress', h('small', t('season_progress', {d: dayInSeason, n: d.per * 3})), h('span.track', h('i', {style: `width:${dayInSeason / (d.per * 3) * 100}%`})),
                    h('small.muted', t('next_season', {n: d.per * 3 - dayInSeason + 1, s: t('season_' + next)}))),
                h('p.note', t('calendar_note')))),
        h('button.btn.btn-primary.btn-wide', {onclick: closeModal}, 'OK')]);
}

const statusLabels = () => ({open: t('status_open'), paused: t('status_paused'), closed: t('status_closed')});

function gardenForScene() {
    return S.game.garden.map(g => ({...g, secs: S.catalog.crops[g.crop]?.secs || 300}));
}

function loadHome(keepCamera = false) {
    S.mode = 'home';
    S.visit = null;
    const r = S.game.restaurant;
    S.world.setRestaurant(r, {plots: r.plots, garden: gardenForScene(), now: nowSec(), keepCamera});
    S.world.setDoorStatus(r.status || 'open', statusLabels());
    S.sim.opts.visit = false;
    S.sim.start(simRestaurant(r));
    closePie();
}

/** After a change that touches the room (layout, staff, menu, size), rebuild scene and service. */
function refreshHome(st, keepCamera = true) {
    applyState(st);
    if (S.mode === 'home') loadHome(keepCamera);
    renderHud();
    S.sheet?.render();
}

/** The menu or the staff changed: the service goes on (guests stay), the simulation updates itself. */
function liveState(st) {
    applyState(st);
    if (S.mode === 'home' && S.sim?.running) S.sim.refresh(simRestaurant(S.game.restaurant));
    else if (S.mode === 'home') loadHome(true);
    renderHud();
    S.sheet?.render();
}

/** Only the numbers changed (coins, ingredients…): no need to rebuild the room. */
function softState(st) {
    applyState(st);
    renderHud();
    S.sheet?.render();
}

function bumpCoins(c) {
    S.liveCoins = (S.liveCoins || 0) + c;
    renderTop();
}

let reporting = false;

async function sendReport(beacon = false) {
    if (S.mode !== 'home' || reporting || !S.sim) return;
    const b = S.sim.takeBatch();
    b.clock = Math.floor(S.sim.clock);
    const any = Object.keys(b.dishes).length || b.ratings.length || b.clock - (S.sentClock || 0) >= 10;
    if (!any) return;
    S.sentClock = b.clock;
    reporting = true;
    try {
        const body = JSON.stringify(b);
        if (beacon) {
            fetch('api/index.php?r=game.report', {method: 'POST', keepalive: true, credentials: 'same-origin', headers: {'Content-Type': 'application/json', 'X-CSRF-Token': S.csrf, 'X-Instance': INSTANCE}, body});
        } else {
            const st = await api('game.report', b);
            softState(st);
            if (st.result?.wages?.paid) toast(t('wages_paid', {c: st.result.wages.paid}));
            S.sim.r.popularity = st.restaurant.popularity;
        }
    } catch (e) { /* the next report will try again with fresh dishes */
    } finally {
        reporting = false;
    }
}

let lastGardenStages = '';

function tick() {
    if (!S.game) return;
    // Garden: refresh the beds when a crop changes stage, and the open garden panel every second
    const now = nowSec();
    const stages = S.game.garden.map(g => g.plot + ':' + (now >= g.readyAt ? 2 : 1)).join(',');
    if (stages !== lastGardenStages && S.mode === 'home') {
        lastGardenStages = stages;
        S.world.setGarden(S.game.restaurant.plots, gardenForScene(), now);
    }
    if (S.sheet?.live) S.sheet.render();
    renderStatus();
}

/** Sims-style clicks: a person opens their info card and a round action menu; litter is cleaned; furniture shows its actions. */
function onWorldTap(e) {
    if (S.mode === 'build') return;
    closePie();
    const agentId = S.world.agentAt(e);
    if (agentId != null) {
        const a = S.sim.agentById(agentId);
        if (!a) return;
        S.sim.selected = a;
        S.world.select(a.mesh);
        renderInfo();
        if (S.mode === 'home') openPie(e, S.sim.actionsFor(a).map(x => ({...x, label: t('pie_' + x.id), run: () => toast(t(S.sim.interact(a, x.id) || 'saved_ok'))})));
        return;
    }
    S.sim.selected = null;
    S.world.select(null);
    renderInfo();
    if (S.mode !== 'home') return;
    // Helping out: tapping dirt or a dirty table cleans it at once
    const litter = S.world.litterAt(e);
    if (litter && S.sim.cleanLitterAt(litter)) return;
    const itemId = S.world.itemAt(e);
    if (itemId != null) {
        const acts = S.sim.itemActions(itemId);
        if (acts.some(a => a.id === 'clean')) return toast(t(S.sim.interactItem(itemId, 'clean')));
        if (acts.length) openPie(e, acts.map(x => ({...x, label: t('pie_' + x.id), run: () => toast(t(S.sim.interactItem(itemId, x.id) || 'saved_ok'))})));
        return;
    }
    const plot = S.world.plotAt(e);
    if (plot != null) openSheet('garden');
}

/** The round action menu around the click (like The Sims). */
function openPie(e, actions) {
    closePie();
    if (!actions.length) return;
    const box = document.querySelector('.game');
    const rect = box.getBoundingClientRect();
    const x = Math.min(rect.width - 90, Math.max(90, e.clientX - rect.left)), y = Math.min(rect.height - 110, Math.max(90, e.clientY - rect.top));
    const pie = h('div.pie', {style: `left:${x}px;top:${y}px`});
    const r = 72;
    actions.forEach((a, i) => {
        const ang = -Math.PI / 2 + (i - (actions.length - 1) / 2) * 0.95;
        const b = h('button.pie-item' + (a.done ? '.done' : ''), {
            style: `--x:${Math.cos(ang) * r}px;--y:${Math.sin(ang) * r}px;--d:${i * 40}ms`, disabled: a.done, title: a.label,
            onclick: (ev) => {
                ev.stopPropagation();
                closePie();
                a.run();
            },
        }, h('span.pie-pic', {html: svgIcon('mood', a.icon)}), h('span.pie-label', a.label));
        pie.append(b);
    });
    pie.append(h('button.pie-close', {onclick: closePie, title: t('close')}, icon('x')));
    box.append(pie);
    S.pie = pie;
}

function closePie() {
    S.pie?.remove();
    S.pie = null;
}

// ---------------------------------------------------------------- HUD

const ICONS = {
    build: '<path d="M4 20h16M6 20V10l6-5 6 5v10M10 20v-5h4v5"/>',
    menu: '<path d="M7 3v8a2 2 0 0 0 4 0V3M9 11v10M17 3c-2 0-3 3-3 6s1 4 3 4v8"/>',
    garden: '<path d="M12 21V11M12 11c0-4 3-7 7-7 0 4-3 7-7 7zM12 14c0-3-2-5-6-5 0 3 2 5 6 5zM5 21h14"/>',
    market: '<path d="M3 9l2-5h14l2 5M4 9h16v11H4zM9 20v-6h6v6M3 9c0 2 2 3 3 3s3-1 3-3c0 2 2 3 3 3s3-1 3-3c0 2 2 3 3 3s3-1 3-3"/>',
    staff: '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-4 3-6 6.5-6s6.5 2 6.5 6M16 4.5a3.5 3.5 0 0 1 0 7M18 14c2.5.6 4 2.6 4 6"/>',
    friends: '<path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11z"/>',
    board: '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0zM7 6H4v2a3 3 0 0 0 3 3M17 6h3v2a3 3 0 0 1-3 3"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/>',
    gift: '<rect x="3" y="8" width="18" height="5" rx="1"/><path d="M5 13v8h14v-8M12 8v13M12 8c-2-4-6-4-6-1s6 1 6 1zm0 0c2-4 6-4 6-1s-6 1-6 1z"/>',
    left: '<path d="M4 12a8 8 0 1 0 2.3-5.7M4 4v4h4"/>', right: '<path d="M20 12a8 8 0 1 1-2.3-5.7M20 4v4h-4"/>',
    top: '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 12h16M12 4v16"/>', cube: '<path d="M12 3l8 4.5v9L12 21l-8-4.5v-9zM12 12l8-4.5M12 12v9M12 12L4 7.5"/>',
    plus: '<path d="M12 5v14M5 12h14"/>', minus: '<path d="M5 12h14"/>', center: '<circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/>',
    x: '<path d="M6 6l12 12M18 6L6 18"/>', home: '<path d="M3 11l9-8 9 8M5 10v10h14V10"/>',
    star: '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
    expand: '<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5M9 12h6M12 9v6"/>',
    car: '<path d="M3 16v-4l2-5h14l2 5v4zM3 16v2h3v-2M18 16v2h3v-2M5 12h14"/><circle cx="7.5" cy="14" r="1"/><circle cx="16.5" cy="14" r="1"/>',
    stairs: '<path d="M3 20h5v-5h5v-5h5V5h3"/>',
    box: '<path d="M3 8l9-4 9 4v9l-9 4-9-4z"/><path d="M3 8l9 4 9-4M12 12v9"/>',
    up: '<path d="M6 15l6-6 6 6"/>', down: '<path d="M6 9l6 6 6-6"/>',
    door: '<path d="M5 21V4h11v17M3 21h18M13 12h.01M16 6l3 1v13l-3 1"/>',
    walls: '<path d="M3 20h18M4 20V9h6v11M14 20v-6h6v6"/>',
    coin: '<circle cx="12" cy="12" r="8"/><path d="M12 8v8M9.5 10c0-1 1-2 2.5-2s2.5.8 2.5 2-1 1.6-2.5 2-2.5 1-2.5 2 1 2 2.5 2 2.5-1 2.5-2"/>',
};
const icon = (name) => h('span.ic', {html: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${ICONS[name]}</svg>`});

function renderHud() {
    const hud = document.getElementById('hud');
    if (!hud) return;
    put(hud, 
        h('div.topbar#topbar'),
        h('div.status#status'),
        h('div.clock#clock'),
        h('div.infocard#infocard'),
        h('div.camctl',
            h('button.cam', {title: t('rotate_left'), onclick: () => S.world.rotate(Math.PI / 4)}, icon('left')),
            h('button.cam', {title: t('rotate_right'), onclick: () => S.world.rotate(-Math.PI / 4)}, icon('right')),
            h('button.cam#topbtn', {title: t('top_view'), onclick: () => (S.world.setTopView(!S.world.top), renderHud())}, icon(S.world.top ? 'cube' : 'top')),
            h('button.cam', {title: t('zoom_in'), onclick: () => S.world.zoom(0.8)}, icon('plus')),
            h('button.cam', {title: t('zoom_out'), onclick: () => S.world.zoom(1.25)}, icon('minus')),
            h('button.cam', {title: t('recenter'), onclick: () => S.world.recenter()}, icon('center')),
            h('div.wallmode-wrap',
                h('button.cam.wallmode' + (S.wallMenu ? '.on' : ''), {title: t('walls_' + S.world.wallMode), onclick: (e) => (e.stopPropagation(), (S.wallMenu = !S.wallMenu), renderHud())}, icon('walls'), h('i.wm-' + S.world.wallMode)),
                // A small panel like the restaurant's status: the three ways of showing the walls, the current one marked
                S.wallMenu && h('div.status-menu.wall-menu', {onclick: (e) => e.stopPropagation()}, ['adaptive', 'low', 'high'].map(m => h('button.status-opt' + (m === S.world.wallMode ? '.on' : ''), {
                    onclick: () => {
                        S.world.setWallMode(m);
                        S.wallMenu = false;
                        renderHud();
                    },
                }, h('i.wm.wm-' + m), t('walls_' + m)))))),
        floorPicker(),
        S.mode === 'visit' ? visitBar() : S.mode === 'build' ? h('div.buildpanel#buildpanel') : dock(),
        h('div.selbar#selbar'));
    renderTop();
    renderStatus();
    renderClock(true);
    renderInfo();
    if (S.mode === 'build') renderBuild();
    updateInsets(); // the panel is already in the page, so its height is known now
}

function renderTop() {
    const bar = document.getElementById('topbar');
    if (!bar || !S.game) return;
    const r = S.game.restaurant;
    const levels = S.catalog.levels;
    const lo = levels[r.level - 1] || 0, hi = levels[r.level] || lo + 1;
    const pct = Math.min(100, Math.max(0, (r.xp - lo) / (hi - lo) * 100));
    put(bar, 
        h('div.brand', h('div.logo-small', {html: LOGO}), h('div',
            h('div.rname', S.mode === 'visit' ? S.visit.restaurant.name : r.name),
            S.mode === 'visit' ? h('div.rsub', '@' + S.visit.username) : h('button.xp', {title: t('levels_title'), onclick: () => openSheet('levels')},
                h('span.lvl', t('level_title', {n: r.level, t: levelTitle(r.level)})), h('span.xpbar', h('i', {style: `width:${pct}%`}))))),
        h('div.pills',
            S.mode !== 'visit' && h('button.pill.coins', {title: t('ledger_title'), onclick: () => openSheet('ledger')}, coin(r.coins + (S.liveCoins || 0))),
            h('button.pill.stars', {title: t('reputation'), onclick: () => S.mode === 'visit' ? visitReviews() : openSheet('reviews')}, starsEl(S.mode === 'visit' ? S.visit.restaurant.stars : r.stars), h('small', String(S.mode === 'visit' ? S.visit.restaurant.popularity : r.popularity))),
            S.mode !== 'visit' && h('button.pill.iconpill', {title: t('inbox'), onclick: () => openSheet('friends', {tab: 'inbox'})}, icon('gift'), S.game.inbox ? h('b.badge', String(S.game.inbox)) : null),
            S.mode !== 'visit' && h('button.pill.iconpill', {title: t('account'), onclick: () => openSheet('account')}, icon('user'))));
}

/** Italian / English at any moment: texts, the door sign and the menu board in the room follow. */
function switchLang(l) {
    setLang(l);
    // No restart: the service goes on, only the words change (texts, door sign, menu boards in the room)
    const status = S.mode === 'visit' ? S.visit?.restaurant.status : S.game.restaurant.status;
    S.world.setDoorStatus(status || 'open', statusLabels());
    S.world.refreshItems?.(k => k === 'menu_board');
    S.clockKey = null;
    renderHud();
    S.sheet?.render();
}

function renderStatus() {
    const el = document.getElementById('status');
    if (!el || !S.sim) return;
    if (S.mode !== 'home') return put(el, );
    const problems = S.sim.problems || [];
    const guests = S.sim.guestsInside; // whoever walked out of the door (or waits outside) is not in the room
    const status = S.game.restaurant.status || 'open';
    const label = status === 'closed' ? t('closed_status') : status === 'paused' ? t('paused_status', {n: guests}) : t('open_status', {n: guests});
    // Redraw only when something changed (a redraw every second made the open menu flicker)
    const todo = todoCount();
    const key = [status, label, problems.join(), (S.sim.warnings || []).map(w => w.key + w.n).join(), !!S.statusMenu, getLang(), todo].join('|');
    if (key === S.statusKey && el.childElementCount) return;
    const menuWasOpen = S.statusKey?.includes('|true|');
    S.statusKey = key;
    put(el,
        h('button.chip.status-' + status, {onclick: (e) => (e.stopPropagation(), toggleStatusMenu()), title: t('status_title')}, h('i.dot'), label, h('span.caret', S.statusMenu ? '▴' : '▾')),
        S.statusMenu && statusMenu(status, menuWasOpen),
        todo > 0 && h('button.chip.todo-chip', {onclick: () => openSheet('todo'), title: t('todo_title')}, ic('ui', 'todo'), t('todo_chip', {n: todo})),
        problems.length > 0 && h('button.chip.warn', {onclick: () => fixProblem(problems[0])}, ic('ui', 'warning'), t(problems[0])),
        !problems.length && (S.sim.warnings || []).map(w => h('button.chip.warn', {onclick: () => enterBuild(w.key === 'need_toilet' ? 'restroom' : 'table')}, ic('ui', 'warning'), t(w.key, {n: w.n}))));
}

/** Open / no new guests / closed (saved on the server: closed restaurants earn nothing while away). It stays open across redraws. */
function toggleStatusMenu(force) {
    S.statusMenu = force ?? !S.statusMenu;
    S.statusKey = null;
    renderStatus();
}

function statusMenu(cur, still = false) {
    return h('div.status-menu' + (still ? '.still' : ''), {onclick: (e) => e.stopPropagation()}, ['open', 'paused', 'closed'].map(st => h('button.status-opt.status-' + st + (st === cur ? '.on' : ''), {
        onclick: () => {
            S.statusMenu = false;
            act('game.status', {status: st}, {
                done: () => {
                    S.sim.setStatus(st);
                    S.world.setDoorStatus(st, statusLabels());
                    renderStatus();
                    if (st === 'closed') toast(t('closed_staff_home'));
                },
            });
        },
    }, h('i.dot'), statusLabels()[st])), h('button.status-opt.status-todo', {onclick: () => (S.statusMenu = false, openSheet('todo'))}, ic('ui', 'todo'), t('todo_title')));
}

// A click anywhere else closes the status menu
document.addEventListener('click', () => S.statusMenu && toggleStatusMenu(false));
document.addEventListener('click', () => S.wallMenu && ((S.wallMenu = false), renderHud()));

/** The game clock with the Sims-style speed buttons, plus weather and season. */
function renderClock(force = false) {
    const el = document.getElementById('clock');
    if (!el || !S.sim) return;
    const hr = S.sim.hour;
    const hh = String(Math.floor(hr)).padStart(2, '0'), mm = String(Math.floor((hr % 1) * 60)).padStart(2, '0'); // every game minute
    const busy = S.mode === 'home' ? hourMood(hr, S.game.restaurant.kind || 'generic') : null;
    const key = hh + mm + S.sim.speed + S.weather + S.mode + getLang() + busy;
    if (!force && key === S.clockKey) return;
    S.clockKey = key;
    const night = isNight(hr);
    const sp = (v, name, title) => h('button.speed' + (S.sim.speed === v ? '.on' : ''), {
        title, onclick: () => {
            S.sim.speed = v;
            renderClock(true);
        },
    }, h('span.speed-pic', {html: svgIcon('ui', name)}));
    const gd = gameDate(S.sim.clock);
    put(el,
        h('button.clock-time', {title: t('calendar_title'), onclick: openCalendar}, h('span.clock-pic', {html: svgIcon('ui', night ? 'moon' : 'sun')}), h('b', `${hh}:${mm}`),
            h('small.clock-date', `${gd.day} ${monthName(gd.month).slice(0, 3)} · ${t('year_short', {n: gd.year})}`),
            h('span.clock-pic', {title: t('weather_' + (S.weather || 'clear')), html: svgIcon('ui', 'w_' + (S.weather || 'clear'))}),
            h('span.clock-pic', {title: t('season_' + S.season), html: svgIcon('ui', 's_' + S.season)})),
        busy && h('span.busy.busy-' + busy, {title: t('busy_' + busy + '_d')}, t('busy_' + busy)),
        S.mode !== 'build' && h('div.speeds', sp(0, 'pause', t('speed_pause')), sp(1, 'play1', t('speed_1')), sp(2, 'play2', t('speed_2')), sp(5, 'play3', t('speed_3'))));
}

/** The card of the selected person: who they are, what they are doing, their mood or energy. */
function renderInfo() {
    const el = document.getElementById('infocard');
    if (!el) return;
    const a = S.sim?.selected;
    if (!a || !S.sim.agents.includes(a)) {
        if (S.sim?.selected) {
            S.sim.selected = null;
            S.world.select(null);
        }
        if (S.highlighted) {
            S.world.highlightTables(null);
            S.highlighted = null;
        }
        return el.childElementCount && put(el);
    }
    const d = S.sim.describe(a);
    // A selected waiter shows their tables in their colour
    const hl = d.role === 'waiter' ? a.id + ':' + (d.tables || []).join(',') : null;
    if (hl !== S.highlighted) {
        S.highlighted = hl;
        S.world.highlightTables(hl ? S.sim.tables.filter(tb => (a.tables || []).includes(tb.id)) : null, d.color || '#E2B23A');
    }
    const bar = (label, v, cls) => h('div.need-bar', h('span', label), h('span.track', h('i.' + cls, {style: `width:${v}%`})), h('b', v + '%'));
    const moodIcon = d.mood == null ? null : d.mood >= 80 ? 'love' : d.mood >= 60 ? 'happy' : d.mood >= 40 ? 'meh' : d.mood >= 20 ? 'sick' : 'angry';
    put(el, h('div.info-head', h('span.info-pic', {html: svgIcon(d.role === 'customer' ? 'mood' : 'ui', d.role === 'customer' ? moodIcon : d.role === 'chef' ? 'chef' : d.role === 'cleaner' ? 'broom' : 'waiter')}),
            h('div', h('b', d.name), h('small', d.role === 'customer' ? t('customer') : n(d.role)))),
        h('button.icon-btn.small', {onclick: () => (S.sim.selected = null, S.world.select(null), renderInfo())}, icon('x')),
        d.trait && h('div.info-trait', {title: t('trait_' + d.trait + '_d')}, t('trait') + ': ', h('b', t('trait_' + d.trait))),
        h('div.info-doing', t('doing') + ': ', t('act_' + d.activity)),
        d.tables && h('div.info-tables', t('my_tables') + ': ', d.tables.length ? d.tables.map(nm => h('b.tnum', {style: `background:${d.color || '#E2B23A'}`}, String(nm))) : h('b.warn-text', t('no_table'))),
        d.order && h('div.info-order', t('ordered') + ': ', h('span.pic', {html: svgIcon('dish', d.order)}), n(d.order)),
        d.mood != null && bar(t('mood'), d.mood, d.mood >= 60 ? 'good' : d.mood >= 35 ? 'mid' : 'bad'),
        d.energy != null && bar(t('energy'), d.energy, d.energy >= 50 ? 'good' : d.energy >= 25 ? 'mid' : 'bad'));
}

/**
 * The restaurant's to-do list: what guests strictly need (to open at all), what keeps them happy, and a station
 * for every worker already hired. Each line knows whether it is done and where to go to fix it.
 */
function requirements() {
    const sim = S.sim, r = S.game.restaurant, staff = sim.agents.filter(a => a.staff && !a.dismissed);
    const hired = (role) => staff.filter(a => a.role === role);
    const fix = {build: (tab) => () => (closeSheet(), enterBuild(tab)), sheet: (k) => () => openSheet(k)};
    const line = (key, done, go, extra = {}) => ({key, done: !!done, go, ...extra});
    const groups = [
        ['req_open', [
            line('req_seat', sim.seats.length, fix.build('table'), {n: sim.seats.length}),
            line('req_stove', sim.stoves.length, fix.build('kitchen'), {n: sim.stoves.length}),
            line('req_counter', sim.counters.length, fix.build('kitchen'), {n: sim.counters.length}),
            line('req_chef', hired('chef').some(a => a.stove), fix.sheet('staff')),
            line('req_waiter', hired('waiter').some(a => a.tables?.length), fix.sheet('staff')),
            line('req_menu', r.menu.length, fix.sheet('menu'), {n: r.menu.length}),
        ]],
        ['req_happy', [
            line('req_toilet', sim.spots.toilet.length, fix.build('restroom')),
            line('req_washbasin', sim.spots.washbasin.length, fix.build('restroom')),
            line('req_sink', sim.spots.sink.length, fix.build('kitchen')),
            line('req_cleaner', hired('cleaner').some(a => a.station), hired('cleaner').length ? fix.build('kitchen') : fix.sheet('staff')),
            line('req_trash', sim.spots.trash, fix.build('kitchen')),
            line('req_light', sim.spots.light.length >= 2, fix.build('deco')),
        ]],
    ];
    // Every worker already hired needs their own station
    const st = [];
    const per = (role, key, ok, tab) => {
        const list = hired(role);
        if (list.length) st.push(line(key, list.every(ok), fix.build(tab), {n: list.filter(ok).length, m: list.length}));
    };
    per('chef', 'req_st_chef', a => a.stove, 'kitchen');
    per('waiter', 'req_st_waiter', a => a.tables?.length, 'table');
    per('cleaner', 'req_st_cleaner', a => a.station, 'kitchen');
    per('manager', 'req_st_manager', a => a.station, 'kitchen');
    per('cashier', 'req_st_cashier', a => a.till, 'kitchen');
    if (st.length) groups.push(['req_staff', st]);
    return groups;
}

/** How many lines of the to-do list are still open (the chip in the status bar shows it). */
const todoCount = () => requirements().reduce((k, [, lines]) => k + lines.filter(l => !l.done).length, 0);

function fixProblem(p) {
    if (['need_stove', 'need_counter', 'need_seat'].includes(p)) enterBuild(p === 'need_seat' ? 'chair' : 'kitchen');
    else if (p === 'need_menu') openSheet('menu');
    else openSheet('staff');
}

function dock() {
    const b = (key, ic, fn) => h('button.dockbtn', {onclick: fn}, icon(ic), h('span', t('dock_' + key)));
    return h('nav.dock',
        b('build', 'build', () => enterBuild()), b('menu', 'menu', () => openSheet('menu')), b('garden', 'garden', () => openSheet('garden')),
        b('market', 'market', () => openSheet('market')), b('staff', 'staff', () => openSheet('staff')), b('friends', 'friends', () => openSheet('friends')),
        b('reviews', 'star', () => openSheet('reviews')), b('board', 'board', () => openSheet('board')));
}

// ---------------------------------------------------------------- sheets (panels)

function openSheet(kind, opts = {}) {
    closeSheet();
    const body = h('div.sheet-body');
    const title = h('h2');
    const el = h('section.sheet', h('header', title, h('button.icon-btn', {onclick: closeSheet, title: t('close')}, icon('x'))), body);
    document.querySelector('.game').append(el); // outside #hud: redrawing the HUD keeps the panel
    S.sheet = {kind, opts, el, live: kind === 'garden' || kind === 'todo', render: () => SHEETS[kind](body, title, S.sheet.opts)};
    updateInsets();
    S.sheet.render();
    requestAnimationFrame(() => el.classList.add('in'));
}

function closeSheet() {
    if (!S.sheet) return;
    const el = S.sheet.el;
    el.classList.remove('in');
    setTimeout(() => el.remove(), 200);
    S.sheet = null;
    updateInsets();
}

/** Keep the room in the part of the screen not covered by a panel. */
function updateInsets() {
    if (!S.world) return;
    const narrow = window.innerWidth <= 640;
    const panel = document.getElementById('buildpanel');
    let right = 0, bottom = 0;
    if (S.sheet) narrow ? (bottom = window.innerHeight * 0.82) : (right = Math.min(420, window.innerWidth));
    if (panel) bottom = Math.max(bottom, panel.offsetHeight);
    document.body.classList.toggle('sheet-open', !!S.sheet && !narrow);
    // On a phone the camera and floor buttons sit in a row just above the build panel
    document.body.classList.toggle('building', !!panel);
    if (panel && !panel._ro && window.ResizeObserver) (panel._ro = new ResizeObserver(() => updateInsets())).observe(panel);
    document.documentElement.style.setProperty('--panel-h', (panel ? panel.offsetHeight : 0) + 'px');
    S.world.setInsets(right, narrow && S.sheet ? 0 : bottom);
}

async function act(route, body, {rebuild = false, live = false, done} = {}) {
    try {
        const before = S.game?.restaurant.coins;
        const st = await api(route, body);
        if (before != null && st.restaurant && st.restaurant.coins < before) sfx.cash();
        (rebuild ? refreshHome : live ? liveState : softState)(st);
        done && done(st);
        return st;
    } catch (e) {
        fail(e);
    }
}

const SHEETS = {
    todo(body, title) {
        title.textContent = t('todo_title');
        const groups = requirements();
        put(body, h('p.muted', t('todo_intro')),
            groups.map(([gk, lines]) => h('div.todo-group',
                h('h3', t(gk), h('small.muted', ` ${lines.filter(l => l.done).length}/${lines.length}`)),
                lines.map(l => h('div.todo-line' + (l.done ? '.done' : ''),
                    h('span.todo-check', {html: l.done ? '<svg viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>' : ''}),
                    h('div.todo-text', h('b', t(l.key, {n: l.n ?? '', m: l.m ?? ''})), h('small.muted', t(l.key + '_d'))),
                    !l.done && h('button.btn.btn-small', {onclick: l.go}, t('todo_go')))))));
    },

    menu(body, title) {
        title.textContent = t('menu_title');
        const r = S.game.restaurant, rec = S.game.recipes, ing = S.game.ingredients;
        const menu = r.menuKeys;
        const needs = (dish, lvl) => S.catalog.dishes[dish].ings.map(i => {
            const need = lvl <= 1 ? 1 : lvl - 1, have = ing[i] || 0;
            return h('span.need' + (have >= need ? '.ok' : '.miss'), {title: n(i)}, ic('ing', i), `${have}/${need}`);
        });
        // Cooking time: the dish's base, and how long it takes on the best stove of this kitchen (before the chef's skill)
        const best = (S.sim?.stoves || []).reduce((a, b) => (!a || b.speed > a.speed ? b : a), null);
        const cold = (k) => S.catalog.dishKinds?.[k] && ((S.catalog.dishKinds[k] === 'drink' && S.sim?.spots.drinks.length) || (S.catalog.dishKinds[k] === 'icecream' && S.sim?.spots.icecream.length));
        const cookEl = (k, d) => {
            if (cold(k)) return h('span.cook', {title: t('cook_cold')}, ic('ui', 'timer'), t('cook_cold_short'));
            const here = S.sim && best ? Math.round(S.sim.cookTime(k, null, best)) : null;
            return h('span.cook', {title: t('cook_hint')}, ic('ui', 'timer'), here != null && here < d.cook ? t('cook_secs_here', {b: d.cook, n: here}) : t('cook_secs', {b: d.cook}));
        };
        const rows = Object.entries(S.catalog.dishes).sort((a, b) => a[1].level - b[1].level).map(([k, d]) => {
            const lvl = rec[k] || 0;
            const locked = d.level > r.level;
            const price = Math.round(d.price * (1 + 0.25 * (Math.max(1, lvl) - 1)));
            const on = menu.includes(k);
            return h('div.dish' + (locked ? '.locked' : ''),
                h('div.dish-icon', {html: dishIcon(k)}),
                h('div.dish-main', h('b', n(k)), h('div.dish-meta', lvl ? h('span.lv', starsEl(lvl, S.catalog.dishMax)) : h('span.muted', locked ? t('locked_level', {n: d.level}) : t('not_learned')),
                    h('span', coin(price)), cookEl(k, d)),
                    !locked && lvl < S.catalog.dishMax && h('div.needs', needs(k, lvl + 1))),
                h('div.dish-act',
                    lvl > 0 && h('label.switch', {title: t('on_menu')}, h('input', {
                        type: 'checkbox', checked: on, onchange: (e) => {
                            const next = e.target.checked ? [...menu, k] : menu.filter(x => x !== k);
                            act('game.menu', {dishes: next}, {live: true});
                        },
                    }), h('i')),
                    !locked && !lvl && d.enabled !== false && h('button.btn.btn-small.btn-primary', {onclick: () => act('game.learn', {dish: k}, {live: true, done: () => toast(t('learned', {d: n(k)}), 'good')})}, t('learn')),
                    !locked && lvl > 0 && lvl < S.catalog.dishMax && h('button.btn.btn-small', {onclick: () => act('game.upgrade', {dish: k}, {live: true, done: () => toast(t('improved', {d: n(k), n: lvl + 1}), 'good')})}, t('upgrade'))));
        });
        put(body, 
            h('p.muted', t('menu_slots', {n: menu.length, m: r.maxMenu})),
            h('div.list', rows),
            pantry());
    },

    garden(body, title) {
        title.textContent = t('garden_title');
        const r = S.game.restaurant, now = nowSec();
        const byPlot = new Map(S.game.garden.map(g => [g.plot, g]));
        const anyRipe = S.game.garden.some(g => now >= g.readyAt);
        if (S.sheet.opts.plot != null) return cropPicker(body, S.sheet.opts.plot);
        const plots = [];
        for (let i = 0; i < r.plots; i++) {
            const g = byPlot.get(i);
            if (!g) plots.push(h('button.plot.empty', {onclick: () => ((S.sheet.opts.plot = i), S.sheet.render())}, h('span.plot-ic', '+'), h('span', t('plant'))));
            else {
                const crop = S.catalog.crops[g.crop];
                const left = g.readyAt - now;
                const pct = Math.min(100, (1 - left / crop.secs) * 100);
                plots.push(left <= 0
                    ? h('button.plot.ripe', {onclick: () => act('game.harvest', {plot: i}, {done: (st) => toast(t('harvested', {list: ingList(st.result.got)}), 'good')})}, h('span.plot-ic', {html: svgIcon('ing', g.crop)}), h('b', t('ready')), h('span', t('harvest')))
                    : h('div.plot', h('span.plot-ic', {html: svgIcon('ing', g.crop)}), h('b', n(g.crop)), h('span.bar', h('i', {style: `width:${pct}%`})), h('small', t('grows_in', {t: fmtTime(left)}))));
            }
        }
        put(body, 
            anyRipe && h('button.btn.btn-primary.btn-wide', {onclick: () => act('game.harvest', {plot: -1}, {done: (st) => toast(t('harvested', {list: ingList(st.result.got)}), 'good')})}, t('harvest_all')),
            h('div.plots', plots));
    },

    market(body, title) {
        title.textContent = t('market_title');
        put(body, h('p.muted', t('market_hint', {n: S.catalog.rules.marketLimit})),
            h('div.list', S.game.market.map(m => h('div.dish',
                h('div.dish-icon', {html: svgIcon('ing', m.ing)}), h('div.dish-main', h('b', n(m.ing)), h('div.dish-meta', coin(m.price), h('span.muted', m.left ? t('left', {n: m.left}) : t('sold_out')))),
                h('div.dish-act',
                    h('button.btn.btn-small', {disabled: !m.left, onclick: () => act('game.market', {ing: m.ing, qty: 1})}, t('buy')),
                    m.left > 1 && h('button.btn.btn-small.btn-primary', {onclick: () => act('game.market', {ing: m.ing, qty: m.left})}, t('buy_n', {n: m.left})))))),
            pantry());
    },

    staff(body, title) {
        title.textContent = t('staff_title');
        const r = S.game.restaurant, mg = S.catalog.manager, roles = S.catalog.roles;
        const roleIcon = (role) => role === 'chef' ? 'chef' : role === 'cleaner' ? 'broom' : role === 'manager' ? 'manager' : role === 'cashier' ? 'register' : 'waiter';
        const stoves = r.layout.filter(it => S.catalog.items[it.k]?.cat === 'stove').length;
        const limits = {chef: r.maxChefs ?? stoves, waiter: r.maxWaiters, cleaner: r.maxCleaners, manager: r.maxManagers, cashier: r.maxCashiers};
        const people = r.staff.map(s => {
            const rank = s.rank || 1, next = mg.promote[rank + 1];
            const select = h('select.role-select', {
                onchange: (e) => act('game.assign', {id: s.id, role: e.target.value}, {live: true, done: () => toast(t('role_changed', {n: s.name, r: n(e.target.value)}), 'good')}),
            }, roles.map(role => h('option', {value: role, selected: role === s.role, disabled: role === 'manager' && r.level < mg.level}, n(role))));
            return h('div.person',
                h('div.person-head', h('img.portrait', {src: portrait(s.role, s.color, s.id * 7.3, lookOf(s)), alt: ''}), h('span.pic', {html: svgIcon('ui', roleIcon(s.role))}),
                    h('div.staff-main', h('b', s.name), h('span.muted', s.trait ? t('trait_' + s.trait) + ' · ' + t('trait_' + s.trait + '_d') : '')), select),
                cv(s.skills, s.role),
                h('div.wage', ic('ui', 'coin'), t('wage_day', {c: (S.game.restaurant.wages || []).find(w => w.id === s.id)?.wage ?? '—'})),
                h('div.seniority', t('seniority', {t: seniorityText(s.years || 0)}), ' · ', t('next_point', {p: Math.floor(((s.skills?.[s.role] ?? 5) % 1) * 100)})),
                s.role === 'manager' && h('div.rank', starsEl(rank, 5), ' ', t('manager_rank', {h: Math.round(mg.hours[rank] * (0.6 + 0.06 * (s.skills?.manager ?? 5)))})),
                h('div.row.wrap',
                    s.role === 'manager' && next != null && h('button.btn.btn-small.btn-primary', {onclick: () => act('game.promote', {id: s.id}, {live: true, done: () => toast(t('promoted', {n: s.name}), 'good')})}, t('promote'), ' · ', coin(next)),
                    h('button.btn.btn-small', {onclick: () => openLook(s)}, t('look_title')),
                    h('button.btn.btn-small.btn-ghost', {onclick: () => confirm(t('fire_confirm', {n: s.name})) && act('game.fire', {id: s.id}, {live: true})}, t('fire'))));
        });
        put(body,
            h('p.muted', t('staff_intro')),
            h('div.payroll', t('payroll') + ': ', coin(r.payroll || 0)),
            h('div.role-limits', roles.map(role => h('span.limit', ic('ui', roleIcon(role)), n(role) + ': ', h('b', String(r.staff.filter(x => x.role === role).length)), ' / ', String(limits[role] ?? 0), role === 'chef' ? h('small.muted', ' · ' + t('chef_stoves', {n: stoves})) : null))),
            h('button.btn.btn-primary.btn-wide', {onclick: () => openCandidates()}, t('find_staff')),
            h('div.list', people),
            r.level < mg.level ? h('p.muted', t('locked_level', {n: mg.level}) + ' · ' + t('manager_what')) : h('p.muted', t('manager_hint', {n: r.maxManagers, h: r.managerHours})));
    },

    ledger(body, title, opts) {
        title.textContent = t('ledger_title');
        put(body, h('p.muted', t('loading')));
        api('game.ledger', {day: opts.day}).then(d => {
            const days = d.days || {};
            const totals = (day) => {
                let inc = 0, out = 0;
                for (const v of Object.values(days[day] || {})) v.sum > 0 ? (inc += v.sum) : (out -= v.sum);
                return {inc, out};
            };
            const dayKeys = Array.from({length: 7}, (_, i) => new Date(Date.parse(d.day + 'T00:00:00Z') - (6 - i) * 86400000).toISOString().slice(0, 10));
            const maxBar = Math.max(1, ...dayKeys.map(k => Math.max(totals(k).inc, totals(k).out)));
            const fmtDay = (k) => new Date(k + 'T12:00:00Z').toLocaleDateString(getLang() === 'it' ? 'it-IT' : 'en-GB', {weekday: 'short', day: 'numeric'});
            const today = totals(d.day);
            const kinds = Object.entries(days[d.day] || {}).sort((a, b) => b[1].sum - a[1].sum);
            const hm = (ts) => new Date(ts * 1000).toLocaleTimeString(getLang() === 'it' ? 'it-IT' : 'en-GB', {hour: '2-digit', minute: '2-digit'});
            put(body,
                h('p.note', t('ledger_readonly')),
                h('div.ledger-chart', dayKeys.map(k => {
                    const tt = totals(k);
                    return h('button.ledger-day' + (k === d.day ? '.on' : ''), {onclick: () => ((opts.day = k), S.sheet.render())},
                        h('div.bars', h('i.inc', {style: `height:${tt.inc / maxBar * 100}%`}), h('i.out', {style: `height:${tt.out / maxBar * 100}%`})), h('small', fmtDay(k)));
                })),
                h('div.ledger-sum', h('div.inc', h('small', t('ledger_in')), coin(today.inc)), h('div.out', h('small', t('ledger_out')), coin(today.out)),
                    h('div.net' + (today.inc - today.out < 0 ? '.neg' : ''), h('small', t('ledger_net')), coin(today.inc - today.out))),
                kinds.length ? h('div.ledger-kinds', kinds.map(([k, v]) => h('div.ledger-kind' + (v.sum < 0 ? '.neg' : ''), h('span', t('lk_' + k)), h('small.muted', '×' + v.n), h('b', (v.sum > 0 ? '+' : '') + v.sum)))) : h('p.muted', t('ledger_empty')),
                d.entries.length ? h('h3', t('ledger_moves')) : null,
                h('div.ledger-list', d.entries.map(e => h('div.ledger-row' + (e.amount < 0 ? '.neg' : ''), h('small.muted', hm(e.at)), h('span', ledgerText(e)), h('b', (e.amount > 0 ? '+' : '') + e.amount)))));
        }).catch(fail);
    },

    reviews(body, title) {
        title.textContent = t('reviews_title');
        put(body, h('p.muted', t('loading')));
        api('game.reviews', {}).then(d => {
            if (!d.count) return put(body, h('div.empty-art', {html: svgIcon('mood', 'greet')}), h('p.muted', t('reviews_none')));
            const max = Math.max(...Object.values(d.distribution));
            const factors = (obj, cls) => h('div.factors', Object.entries(obj).slice(0, 6).map(([k, v]) => h('span.factor.' + cls, t('f_' + k), h('b', '×' + v))));
            const tips = Object.keys(d.bad).map(k => ({slow: 'tip_slow', plain: 'tip_plain', bare: 'tip_bare', dirty: 'tip_dirty', dark: 'tip_dark', no_toilet: 'tip_no_toilet', toilet_dirty: 'tip_toilet_dirty'})[k]).filter(Boolean).slice(0, 3);
            put(body,
                h('div.rep-head',
                    h('div.rep-score', h('b', d.average.toFixed(1)), starsEl(Math.round(d.average), 5), h('small', t('reviews_window', {n: Math.min(d.count, d.window)}))),
                    h('div.rep-dist', [5, 4, 3, 2, 1].map(st => h('div.dist-row', h('span', String(st)), h('span.star-pic', {html: svgIcon('mood', 'star')}),
                        h('span.track', h('i', {style: `width:${max ? d.distribution[st] / max * 100 : 0}%`})), h('small', String(d.distribution[st])))))),
                Object.keys(d.good).length ? h('div', h('h3', t('praised')), factors(d.good, 'good')) : null,
                Object.keys(d.bad).length ? h('div', h('h3', t('blamed')), factors(d.bad, 'bad')) : null,
                h('div.tips', h('h3', t('how_improve')), (tips.length ? tips : ['tip_good']).map(k => h('p.tip', ic('mood', k === 'tip_good' ? 'star' : 'sparkle'), t(k)))),
                h('h3', t('reviews_title')),
                h('div.list', d.reviews.map(reviewCard)));
        }).catch(fail);
    },

    levels(body, title) {
        title.textContent = t('levels_title');
        const r = S.game.restaurant;
        const rows = [];
        for (let l = 1; l <= S.catalog.levels.length; l++) {
            const u = unlocksAt(l);
            rows.push(h('div.level-row' + (l === r.level ? '.current' : l < r.level ? '.done' : ''),
                h('div.level-num', String(l)),
                h('div.level-main', h('b', levelTitle(l)), l === r.level && h('span.tag-now', t('current')),
                    h('small.muted', t('reward') + ': ', coin(l * (S.catalog.levelReward || 0)), ' · XP ' + S.catalog.levels[l - 1]),
                    u.length ? h('div.unlocks', u.map(x => h('span.unlock', x))) : null)));
        }
        put(body, h('div.levels', rows));
        requestAnimationFrame(() => body.querySelector('.level-row.current')?.scrollIntoView({block: 'center'}));
    },
};

/** The look of a worker for the 3D model: hairstyle, hair colour, uniform from the catalog. */
const lookOf = (s, over = {}) => {
    const outfit = over.outfit ?? s.outfit ?? 'default';
    return {hair: over.hair ?? s.hair, hairColor: over.hairColor ?? s.hairColor, uniform: outfit !== 'default' ? S.catalog.uniforms[outfit] : null,
        glasses: over.glasses ?? s.glasses, facial: over.facial ?? s.facial, headwear: over.headwear ?? s.headwear};
};

/** Uniforms and the hairdresser, with a live 3D preview. You pay only for what changes. */
function openLook(s) {
    const cur = {outfit: s.outfit || 'default', hair: s.hair || 'short', hairColor: s.hairColor || S.catalog.hairColors[0], glasses: s.glasses || 'none', facial: s.facial || 'none', headwear: s.headwear || 'none'};
    const ACC = S.catalog.accessories || {};
    const pick = {...cur};
    const img = h('img.look-preview', {alt: ''});
    const total = h('b');
    const lvl = S.game.restaurant.level;
    const cost = () => (pick.outfit !== cur.outfit ? S.catalog.uniforms[pick.outfit].price : 0)
        + (pick.hair !== cur.hair || pick.hairColor !== cur.hairColor ? S.catalog.hairPrice : 0)
        + Object.keys(ACC).filter(k => pick[k] !== cur[k]).length * (S.catalog.accessoryPrice || 60);
    const draw = () => {
        img.src = portrait(s.role, s.color, s.id * 7.3, lookOf(s, pick));
        put(total, coin(cost()));
        body.querySelectorAll('[data-k]').forEach(b => b.classList.toggle('on', pick[b.dataset.k] === b.dataset.v));
    };
    const opt = (k, v, label, extra = {}) => h('button.look-opt', {'data-k': k, 'data-v': v, ...extra, onclick: () => ((pick[k] = v), draw())}, label);
    const body = h('div.look',
        h('div.look-left', img, h('p.muted', t('look_cost')), total),
        h('div.look-right',
            h('h3', t('uniform')),
            h('div.look-grid', Object.entries(S.catalog.uniforms).map(([k, u]) => opt('outfit', k, [
                h('i.uni-swatch', {style: k === 'default' ? 'background:repeating-linear-gradient(45deg,#fff 0 4px,#ddd 4px 8px)' : `background:linear-gradient(135deg, ${u.top} 50%, ${u.accent} 50%)`}),
                h('span', t('uni_' + k)), h('small', u.level > lvl ? t('locked_level', {n: u.level}) : u.price ? String(u.price) : t('free')),
            ], {disabled: u.level > lvl}))),
            h('h3', t('hairstyle'), h('small.muted', ' · ' + S.catalog.hairPrice + ' ' + t('coins'))),
            h('div.look-grid', S.catalog.hairstyles.map(k => opt('hair', k, h('span', t('hair_' + k))))),
            h('div.look-colors', S.catalog.hairColors.map(c => opt('hairColor', c, '', {style: `background:${c}`, title: c}))),
            ...Object.entries(ACC).flatMap(([slot, options]) => [
                h('h3', t('acc_' + slot), h('small.muted', ' · ' + (S.catalog.accessoryPrice || 60) + ' ' + t('coins'))),
                h('div.look-grid', options.map(k => opt(slot, k, h('span', t('acc_' + slot + '_' + k))))),
            ])));
    modal(t('look_of', {n: s.name}), [body, h('div.row', h('button.btn', {onclick: closeModal}, t('cancel')), h('button.btn.btn-primary', {
        style: 'flex:1', onclick: async () => {
            if (!cost()) return closeModal();
            const done = await act('game.style', {id: s.id, ...pick}, {live: true});
            if (done) {
                toast(t('look_done', {n: s.name}), 'good');
                closeModal();
            }
        },
    }, t('save')))]);
    draw();
}

/** Seniority in game time: days (120 a year) or years and days. */
function seniorityText(years) {
    const days = Math.floor(years * 120);
    if (days < 120) return t('days_n', {n: days});
    return t('years_days', {y: Math.floor(days / 120), d: days % 120});
}

/** A CV: the four skills from 0 to 10, the current role highlighted. */
function cv(skills = {}, current = null) {
    return h('div.cv', S.catalog.roles.map(role => {
        const v = skills[role] ?? 0;
        return h('div.cv-row' + (role === current ? '.current' : ''), {title: v.toFixed(2)}, h('span', n(role)), h('span.track', h('i', {style: `width:${v * 10}%`})), h('b', String(Math.floor(v))));
    }));
}

/** People looking for a job today: their CV, their price, and the role you hire them for. */
async function openCandidates(refresh = false) {
    let st;
    try {
        st = await api('game.candidates', {refresh});
    } catch (e) {
        return fail(e);
    }
    softState(st);
    const list = st.result.candidates;
    const cards = list.map((c, idx) => {
        const best = S.catalog.roles.reduce((a, b) => (c.skills[b] > c.skills[a] ? b : a), 'chef');
        const select = h('select.role-select', S.catalog.roles.map(role => h('option', {value: role, selected: role === best, disabled: role === 'manager' && S.game.restaurant.level < S.catalog.manager.level}, n(role))));
        return h('div.candidate',
            h('div.person-head', h('i.swatch', {style: `background:${c.color}`}), h('div.staff-main', h('b', c.name), h('span.muted', t('trait_' + c.trait) + ' · ' + t('trait_' + c.trait + '_d')))),
            cv(c.skills, best),
            h('div.row', select, h('button.btn.btn-small.btn-primary', {
                disabled: c.cost > S.game.restaurant.coins,
                onclick: async () => {
                    const done = await act('game.hire', {idx, role: select.value}, {live: true});
                    if (done) {
                        toast(t('hired', {n: c.name, r: n(select.value)}), 'good');
                        closeModal();
                    }
                },
            }, t('hire'), ' · ', coin(c.cost))));
    });
    modal(t('candidates_title'), [h('p.muted', t('candidates_intro')), list.length ? h('div.candidates', cards) : h('p.muted', t('candidates_none')),
        h('div.row', h('button.btn', {onclick: closeModal}, t('close')), h('button.btn.btn-ghost', {onclick: () => openCandidates(true)}, t('candidates_refresh'), ' · ', coin(S.catalog.refresh)))]);
}

/** One line of the statement, in words. */
function ledgerText(e) {
    const [a, b] = String(e.detail || '').split('|');
    switch (e.kind) {
        case 'customers': return t('lt_customers', {n: a || 0});
        case 'away': return t('lt_away', {n: a || 0});
        case 'level': return t('lt_level', {n: a});
        case 'hire': return t('lt_hire', {n: a, r: b ? n(b) : ''});
        case 'market': return t('lt_market', {i: a ? n(a) : '', q: b || 1});
        case 'plant': return t('lt_plant', {i: a ? n(a) : ''});
        case 'expand': return t('lt_expand', {n: a});
        case 'promote': return t('lt_promote', {n: a});
        case 'visit': return t('lt_visit', {n: a || ''});
        case 'admin': return t('lt_admin') + (a ? ' · ' + a : '');
        case 'wages': return t('lt_wages', {n: a || 0, d: b || 1});
        case 'parking': return t('lt_parking', {n: a || 1});
        case 'garage': return t('lt_garage', {n: a || 1});
        case 'floor': return t('lt_floor', {n: a || 1});
        case 'tip_given': return t('lt_tip_given', {n: a || ''});
        case 'tip_friend': return t('lt_tip_friend', {n: a ? '@' + a : ''});
        default: return t('lk_' + e.kind);
    }
}

/** Music and sounds: on/off and volume (remembered on this device). */
function soundSettings() {
    const p = audioPrefs();
    const toggle = (key, label) => h('label.check', h('input', {type: 'checkbox', checked: p[key], onchange: (e) => setAudioPrefs({[key]: e.target.checked})}), h('span', label));
    return h('div.field', h('span', t('sound_title')),
        h('div.row.wrap', toggle('music', t('sound_music')), toggle('sfx', t('sound_effects'))),
        h('label.row', h('small', t('sound_volume')), h('input', {type: 'range', min: 0, max: 1, step: 0.05, value: p.volume, oninput: (e) => setAudioPrefs({volume: +e.target.value})})));
}

/** What a level unlocks, as short labels. */
function unlocksAt(l) {
    const out = [];
    const items = Object.entries(S.catalog.items).filter(([, d]) => d.level === l && d.enabled !== false).map(([k]) => n(k));
    const dishes = Object.entries(S.catalog.dishes).filter(([, d]) => d.level === l).map(([k]) => n(k));
    const crops = Object.entries(S.catalog.crops).filter(([, d]) => d.level === l).map(([k]) => n(k));
    if (items.length) out.push(t('unlock_items') + ': ' + items.join(', '));
    if (dishes.length) out.push(t('unlock_dishes') + ': ' + dishes.join(', '));
    if (crops.length) out.push(t('unlock_crops') + ': ' + crops.join(', '));
    if (l > 1 && maxSide(l) > maxSide(l - 1)) out.push(t('unlock_side', {n: maxSide(l)}));
    for (const [pl, p] of Object.entries(S.catalog.parking || {})) if (p.level === l) out.push(t('unlock_parking', {n: p.spaces, l: pl}));
    for (const [gl, p] of Object.entries(S.catalog.garage || {})) if (p.level === l) out.push(t('unlock_garage', {n: p.spaces, l: gl}));
    for (const [fl, p] of Object.entries(S.catalog.floors || {})) if (p.level === l) out.push(t('unlock_floor', {n: fl}));
    const f = (fn) => fn(l) > fn(l - 1) ? fn(l) : null;
    const w = f(x => Math.min(6, 1 + Math.floor(x / 3))), c = f(x => Math.min(4, 1 + Math.floor(x / 5))), m = f(x => Math.min(8, 3 + Math.floor(x / 4))), p = f(x => Math.min(12, 4 + Math.floor(x / 2)));
    const mg = f(x => x < S.catalog.manager.level ? 0 : Math.min(3, 1 + Math.floor((x - S.catalog.manager.level) / 6)));
    if (l > 1 && w) out.push(t('unlock_waiters', {n: w}));
    if (l === (S.catalog.cashier?.level || 9)) out.push(t('unlock_cashier'));
    if (l > 1 && c) out.push(t('unlock_cleaners', {n: c}));
    if (l > 1 && m) out.push(t('unlock_menu', {n: m}));
    if (l > 1 && p) out.push(t('unlock_plots', {n: p}));
    if (mg) out.push(t('unlock_managers', {n: mg}));
    return out;
}

/** A review as the customer would write it, built from what they liked and disliked. */
function reviewSentence(rv) {
    const cap = (x) => x.charAt(0).toUpperCase() + x.slice(1);
    const good = rv.good.map(k => t('f_' + k)), bad = rv.bad.map(k => t('f_' + k));
    const parts = [];
    if (good.length) parts.push(t('review_good', {a: cap(good.join(', '))}));
    if (bad.length) parts.push(t(good.length ? 'review_but' : 'review_only_bad', {b: bad.join(', ')}));
    return parts.join(' ') || t('review_neutral');
}

function reviewCard(rv) {
    const face = rv.stars >= 5 ? 'love' : rv.stars === 4 ? 'happy' : rv.stars === 3 ? 'meh' : rv.stars === 2 ? 'sick' : 'angry';
    return h('div.review',
        h('span.review-face', {html: svgIcon('mood', face)}),
        h('div.review-main', h('div.review-top', h('b', rv.name), starsEl(rv.stars, 5), h('span.pic', {html: svgIcon('dish', rv.dish)})), h('p', reviewSentence(rv))));
}

/** The level-up celebration: confetti, the new level and title, the coins won, what is unlocked. */
function showLevelUp(from, to) {
    sfx.levelUp();
    const blocks = [];
    let reward = 0;
    for (let l = from + 1; l <= to; l++) {
        reward += l * (S.catalog.levelReward || 0);
        const u = unlocksAt(l);
        if (u.length) blocks.push(h('div.unlocks', u.map(x => h('span.unlock', x))));
    }
    const colors = ['#D9653B', '#7A8B3A', '#B43E5C', '#E2B23A', '#3E7CB4', '#F7A1C4'];
    const confetti = h('div.confetti', Array.from({length: 46}, (_, i) => h('i', {
        style: `--x:${(Math.random() * 2 - 1) * 240}px;--y:${-120 - Math.random() * 180}px;--r:${Math.random() * 720 - 360}deg;--d:${Math.random() * 0.25}s;background:${colors[i % colors.length]};` +
            (i % 3 ? '' : 'border-radius:50%;'),
    })));
    modal(t('level_up_title', {n: to}), [
        confetti,
        h('div.lvl-badge', h('span.lvl-star', {html: svgIcon('mood', 'star')}), h('b.lvl-num', String(to))),
        h('p.lvl-title', levelTitle(to)),
        h('div.lvl-reward', coin('+' + reward)),
        blocks.length ? h('h3', t('unlocks')) : null, ...blocks,
        h('div.row', h('button.btn', {onclick: () => (closeModal(), openSheet('levels'))}, t('levels_title')), h('button.btn.btn-primary', {style: 'flex:1', onclick: closeModal}, t('level_up_ok')))],
    'levelup-modal');
}

function visitReviews() {
    const list = S.visit?.reviews || [];
    modal(t('reviews_title') + ' · @' + (S.visit?.username || ''), [list.length ? h('div.list', list.map(reviewCard)) : h('p.muted', t('reviews_none')),
        h('button.btn.btn-wide', {onclick: closeModal}, t('close'))]);
}

const SHEETS_MORE = {
    friends(body, title, opts) {
        title.textContent = t('friends_title');
        const tab = opts.tab || 'list';
        const tabs = h('div.tabs', [['list', t('friends_title')], ['inbox', t('inbox') + (S.game.inbox ? ` (${S.game.inbox})` : '')]].map(([k, label]) =>
            h('button.tab' + (tab === k ? '.on' : ''), {onclick: () => ((opts.tab = k), S.sheet.render())}, label)));
        const content = h('div');
        put(body, tabs, content);
        if (tab === 'inbox') return renderInbox(content);
        renderFriends(content);
    },

    board(body, title, opts) {
        title.textContent = t('board_title');
        opts.scope ||= 'friends';
        opts.period ||= 'week';
        const seg = (key, vals) => h('div.seg', vals.map(([v, label]) => h('button' + (opts[key] === v ? '.on' : ''), {onclick: () => ((opts[key] = v), S.sheet.render())}, label)));
        const list = h('div.list', h('p.muted', t('loading')));
        put(body, seg('scope', [['friends', t('scope_friends')], ['global', t('scope_global')]]), seg('period', [['week', t('period_week')], ['all', t('period_all')]]), list);
        api('social.board', {scope: opts.scope, period: opts.period}).then(d => {
            put(list, ...(d.rows.length ? d.rows.map(r => h('div.rank' + (r.username === d.me ? '.me' : ''),
                h('b.pos', r.rank <= 3 ? ic('ui', 'medal' + r.rank) : String(r.rank)), h('span.who', '@' + r.username), h('span.muted', t('level') + ' ' + r.level),
                h('b', t(opts.period === 'week' ? 'week_value' : 'all_value', {n: r.value})))) : [h('p.muted', t('board_empty'))]));
        }).catch(fail);
    },

    account(body, title) {
        title.textContent = t('account_title');
        const r = S.game.restaurant;
        const nameInput = h('input', {value: r.name, maxlength: 40});
        const pwForm = h('form.auth-form',
            S.user.hasPassword && h('label.field', h('span', t('current_password')), h('input', {type: 'password', name: 'current', autocomplete: 'current-password'})),
            h('label.field', h('span', t('new_password')), h('input', {type: 'password', name: 'password', autocomplete: 'new-password'})),
            h('button.btn', {type: 'submit'}, t('change_password')));
        pwForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const f = new FormData(pwForm);
            try {
                const d = await api('auth.password', {current: f.get('current') || '', password: f.get('password')});
                S.user = d.user;
                toast(t('saved_ok'), 'good');
                pwForm.reset();
            } catch (ex) {
                fail(ex);
            }
        });
        put(body, 
            h('div.acct', h('b', '@' + S.user.username), h('span.muted', S.user.email)),
            S.user.isAdmin && h('a.btn.btn-wide', {href: 'admin.html', target: '_blank'}, ic('ui', 'gear'), t('admin_panel')),
            soundSettings(),
            h('div.field', h('span', t('kind_title')), h('div.kinds', Object.entries(S.catalog.kinds).map(([k, v]) => {
                const cur = (r.kind || 'generic') === k, locked = v.level > r.level;
                return h('div.kind' + (cur ? '.on' : '') + (locked ? '.locked' : ''),
                    h('b', t('kind_' + k)), h('small', t('kind_' + k + '_d')), h('small.muted', t('kind_effects', {p: v.price, c: v.customers})),
                    cur ? h('span.tag-now', t('kind_current')) : locked ? h('small', t('locked_level', {n: v.level}))
                        : h('button.btn.btn-small', {onclick: () => confirm(t('kind_title') + ': ' + t('kind_' + k) + '?') && act('game.kind', {kind: k}, {live: true, done: () => toast(t('kind_' + k), 'good')})}, t('kind_switch', {c: v.cost})));
            }))),
            h('label.field', h('span', t('restaurant_name')), h('div.row', nameInput, h('button.btn', {
                onclick: () => act('game.rename', {name: nameInput.value}, {done: () => S.world.setSign(S.game.restaurant.name)}),
            }, t('save')))),
            h('div.field', h('span', t('language')), h('div.lang-switch', ['it', 'en'].map(l => h('button' + (getLang() === l ? '.on' : ''), {type: 'button', onclick: () => switchLang(l)}, l.toUpperCase())))),
            pwForm,
            h('div.row.wrap', h('button.btn', {onclick: logout}, t('logout')), h('a.btn.btn-ghost', {href: 'privacy.html', target: '_blank'}, t('privacy')),
                h('button.btn.btn-danger', {onclick: deleteAccount}, t('delete_account'))),
            credit());
    },
};

Object.assign(SHEETS, SHEETS_MORE);

function pantry() {
    const ing = S.game.ingredients;
    const keys = Object.keys(ing).filter(k => ing[k] > 0);
    const wish = S.game.restaurant.wish || '';
    return h('div.pantry', h('h3', t('pantry')),
        keys.length ? h('div.chips', keys.map(k => h('span.ichip', ic('ing', k), n(k) + ' ', h('b', '×' + ing[k])))) : h('p.muted', t('pantry_empty')),
        h('label.field.inline', h('span', t('wish')), h('select', {onchange: (e) => act('game.wish', {ing: e.target.value})},
            h('option', {value: ''}, t('wish_none')), Object.keys(S.catalog.ingredients).map(k => h('option', {value: k, selected: k === wish}, n(k))))));
}

function cropPicker(body, plot) {
    const lvl = S.game.restaurant.level;
    put(body, h('button.btn.btn-ghost', {onclick: () => ((S.sheet.opts.plot = null), S.sheet.render())}, '← ' + t('back')), h('h3', t('choose_crop')),
        h('div.list', Object.entries(S.catalog.crops).filter(([, c]) => c.enabled !== false).map(([k, c]) => h('div.dish' + (c.level > lvl ? '.locked' : ''),
            h('div.dish-icon', {html: svgIcon('ing', k)}), h('div.dish-main', h('b', n(k)), h('div.dish-meta', c.level > lvl ? t('locked_level', {n: c.level}) : t('seed', {c: c.cost, t: fmtTime(c.secs), y: c.yield}))),
            c.level <= lvl && h('button.btn.btn-small.btn-primary', {onclick: () => act('game.plant', {plot, crop: k}, {done: () => ((S.sheet.opts.plot = null), S.sheet.render())})}, t('plant'))))));
}

async function renderFriends(el) {
    const search = h('input', {placeholder: t('search_ph'), maxlength: 20, autocomplete: 'off'});
    const results = h('div.list');
    const listEl = h('div', h('p.muted', t('loading')));
    let timer;
    search.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(async () => {
            const q = search.value.trim();
            if (q.length < 2) return put(results, );
            const d = await api('social.search', {q}).catch(() => ({players: []}));
            put(results, ...d.players.map(p => h('div.friend', h('span.who', '@' + p.username),
                p.status === 'none' ? h('button.btn.btn-small.btn-primary', {onclick: () => api('social.request', {username: p.username}).then(draw).then(() => search.dispatchEvent(new Event('input'))).catch(fail)}, t('add'))
                    : h('span.muted', t(p.status === 'friend' ? 'friend' : 'requested')))));
        }, 250);
    });
    put(el, h('div.search', search), results, listEl);
    const draw = (d) => {
        const bonus = (f) => S.catalog.rules.visitBase + 5 * f.level;
        put(listEl, 
            d.incoming.length ? h('div', h('h3', t('requests')), h('div.list', d.incoming.map(u => h('div.friend', h('span.who', '@' + u),
                h('button.btn.btn-small.btn-primary', {onclick: () => api('social.accept', {username: u}).then(draw).catch(fail)}, t('accept')),
                h('button.btn.btn-small.btn-ghost', {onclick: () => api('social.decline', {username: u}).then(draw).catch(fail)}, t('decline')))))) : null,
            d.outgoing.length ? h('div.list.muted', d.outgoing.map(u => h('div.friend', h('span.who', '@' + u), h('span', t('requested'))))) : null,
            h('h3', t('friends_title')),
            d.friends.length ? h('div.list', d.friends.map(f => h('div.friend.card',
                h('div.friend-main', h('b.who', '@' + f.username), h('span.muted', `${t('level')} ${f.level} `, starsEl(f.stars)), f.wish && h('span.wish', ic('ing', f.wish), t('wants', {i: n(f.wish)}))),
                h('div.friend-act',
                    h('button.btn.btn-small.btn-primary', {onclick: () => startVisit(f.username)}, f.visited ? t('visit') : t('visit_bonus', {c: bonus(f)})),
                    h('button.btn.btn-small', {disabled: f.gifted, onclick: () => giftPicker(f)}, f.gifted ? t('gifted') : t('gift')),
                    h('button.btn.btn-small.btn-ghost', {title: t('remove'), onclick: () => confirm(t('remove_confirm', {u: f.username})) && api('social.remove', {username: f.username}).then(draw).catch(fail)}, icon('x')))))) : h('p.muted', t('no_friends')));
    };
    api('social.friends', {}).then(draw).catch(fail);
}

function giftPicker(f) {
    const keys = Object.keys(S.catalog.ingredients);
    const list = h('div.chips.pick', keys.map(k => h('button.ichip' + (k === f.wish ? '.wished' : ''), {
        onclick: async () => {
            try {
                await api('social.gift', {username: f.username, ing: k});
                closeModal();
                toast(t('gift_sent', {u: f.username}), 'good');
                S.sheet?.render();
            } catch (e) {
                fail(e);
            }
        },
    }, ic('ing', k), n(k))));
    modal(t('gift_title', {u: f.username}), [list, h('div.row', h('button.btn', {onclick: closeModal}, t('cancel')))]);
}

async function renderInbox(el) {
    put(el, h('p.muted', t('loading')));
    const d = await api('social.inbox', {}).catch(e => (fail(e), null));
    if (!d) return;
    put(el, d.gifts.length
        ? h('div', h('button.btn.btn-primary.btn-wide', {onclick: () => act('social.claim', {}, {done: (st) => toast(t('got', {list: ingList(st.result.got)}), 'good')})}, t('claim_all')),
            h('div.list', d.gifts.map(g => h('div.friend', h('span', ic('ing', g.ing), n(g.ing)), h('span.muted', t('gift_from', {u: g.from}))))))
        : h('p.muted', t('inbox_empty')),
        d.incoming.length ? h('div', h('h3', t('requests')), h('div.list', d.incoming.map(u => h('div.friend', h('span.who', '@' + u),
            h('button.btn.btn-small.btn-primary', {onclick: () => api('social.accept', {username: u}).then(() => S.sheet.render()).catch(fail)}, t('accept')))))) : null);
}

async function deleteAccount() {
    const pw = S.user.hasPassword ? prompt(t('delete_confirm')) : (confirm(t('delete_confirm')) ? '' : null);
    if (pw === null) return;
    try {
        await api('auth.delete', {password: pw});
        location.href = location.pathname;
    } catch (e) {
        fail(e);
    }
}

// ---------------------------------------------------------------- visits

async function startVisit(username) {
    await sendReport();
    try {
        const d = await api('social.visit', {username});
        closeSheet();
        S.mode = 'visit';
        S.visit = d;
        S.sim.stop();
        S.world.setRestaurant(d.restaurant, {plots: 0});
        S.sim.opts.visit = true;
        S.sim.start(simRestaurant(d.restaurant));
        renderHud();
        if (d.reward) {
            toast(t('visit_reward', {c: d.reward}), 'good');
            S.game.restaurant.coins += d.reward;
        }
    } catch (e) {
        fail(e);
    }
}

function visitBar() {
    const v = S.visit;
    const stars = h('div.rate', v.rated ? h('span', t('rated'), ' ', starsEl(v.rated)) : [h('span', t('rate_it')), ...[1, 2, 3, 4, 5].map(i => h('button.star', {
        onclick: async () => {
            try {
                await api('social.rate', {username: v.username, stars: i});
                v.rated = i;
                renderHud();
            } catch (e) {
                fail(e);
            }
        },
    }, h('span.star-pic', {html: svgIcon('mood', 'star')})))]);
    // A tip for the friend: coins from my restaurant to theirs (a daily limit per friend)
    const daily = S.catalog.friendTipDaily || 200;
    const tips = h('div.tipbar', h('span', icon('coin'), t('tip_friend')), (S.catalog.friendTips || [10, 25, 50, 100]).map(c => h('button.btn.btn-small', {
        disabled: (v.tipped || 0) + c > daily || S.game.restaurant.coins < c,
        onclick: async () => {
            try {
                const d = await api('social.tip', {username: v.username, coins: c});
                v.tipped = d.given;
                S.game.restaurant.coins = d.coins;
                toast(t('tip_sent', {c, u: '@' + v.username}), 'good');
                sfx.coin?.();
                renderHud();
            } catch (e) {
                fail(e);
            }
        },
    }, coin(c))), v.tipped ? h('small.muted', t('tip_given_today', {c: v.tipped})) : null);
    return h('div.visitbar', h('b', t('visiting', {u: '@' + v.username})), stars, tips,
        v.reviews?.length ? h('button.btn.btn-small', {onclick: visitReviews}, icon('star'), t('reviews_title')) : null,
        h('button.btn.btn-primary', {onclick: goHome}, icon('home'), t('back_home')));
}

async function goHome() {
    S.sim.stop();
    try {
        const st = await api('game.state', {});
        applyState(st);
    } catch (e) { /* keep the last state */
    }
    loadHome();
    renderHud();
}

// ---------------------------------------------------------------- build mode

const BUILD_TABS = [['storage', []], ['seasonal', []], ['table', ['table']], ['chair', ['chair']], ['stairs', ['stairs']], ['kitchen', ['stove', 'counter', 'kitchen', 'service']], ['outdoor', ['outdoor']], ['ground', ['ground']], ['restroom', ['restroom']], ['partition', ['partition']], ['window', ['window']],
    ['lounge', ['lounge']], ['deco', ['deco']], ['rug', ['rug']], ['floor', ['floor']], ['wall', ['wall']]];
/** Decorations that entertain go in the lounge tab. */
const LOUNGE_TAGS = ['music', 'fun', 'cozy', 'snack'];
const buildTabOf = (k, d) => S.catalog.seasonal?.[k] ? 'seasonal' : d.cat === 'deco' && LOUNGE_TAGS.includes((S.catalog.tags || {})[k]) ? 'lounge' : (BUILD_TABS.find(([, cats]) => cats.includes(d.cat)) || ['deco'])[0];
let buildTab = 'table';
/** How the shop is sorted (remembered on this device): level (default), price, popularity, quality, name. */
const SORTS = {
    level: (a, b) => a[1].level - b[1].level || a[1].price - b[1].price,
    price_asc: (a, b) => a[1].price - b[1].price || a[1].level - b[1].level,
    price_desc: (a, b) => b[1].price - a[1].price || a[1].level - b[1].level,
    pop: (a, b) => b[1].pop - a[1].pop || a[1].price - b[1].price,
    tier: (a, b) => (b[1].tier || 0) - (a[1].tier || 0) || a[1].price - b[1].price,
    name: (a, b) => n(a[0]).localeCompare(n(b[0]), getLang()),
};
let buildSort = 'level';
try {
    buildSort = SORTS[localStorage.getItem('forkville-sort')] ? localStorage.getItem('forkville-sort') : 'level';
} catch (e) { /* storage blocked */ }

function enterBuild(tab) {
    closeSheet();
    sendReport();
    if (tab) buildTab = tab;
    S.mode = 'build';
    S.sim.stop();
    S.world.setRestaurant(S.game.restaurant, {plots: S.game.restaurant.plots, garden: gardenForScene(), now: nowSec(), keepCamera: true});
    S.editor.begin(S.game.restaurant);
    renderHud();
}

function exitBuild() {
    S.world.frameOutdoor(false);
    S.editor.end();
    loadHome(true);
    renderHud();
}

async function saveBuild() {
    try {
        const st = await api('game.layout', S.editor.payload());
        S.editor.end();
        applyState(st);
        loadHome(true);
        renderHud();
        toast(t('saved'), 'good');
    } catch (e) {
        fail(e);
    }
}

/**
 * Who uses a piece (badges in the store): a list, since some pieces serve two jobs — the counter (the chef puts the
 * dish there, the waiter picks it up), the sink (waiters and cleaners wash the plates). Breaks are for all the staff.
 */
function rolesOfItem(k, d) {
    const tag = S.catalog.tags?.[k];
    if (d.cat === 'stove' || tag === 'fridge') return ['chef'];
    if (d.cat === 'counter') return ['chef', 'waiter'];
    if (tag === 'sink') return ['waiter', 'cleaner'];
    if (tag === 'drinks' || tag === 'icecream') return ['waiter'];
    if (tag === 'janitor' || tag === 'toilet') return ['cleaner'];
    if (tag === 'register') return ['cashier'];
    if (tag === 'host') return ['manager'];
    if (tag === 'coffee' || tag === 'rest') return ['staff'];
    return [];
}
const ROLE_ICON = {chef: ['ui', 'chef'], waiter: ['ui', 'waiter'], cleaner: ['ui', 'broom'], cashier: ['ui', 'register'], manager: ['ui', 'manager'], staff: ['mood', 'sleep']};

/** The event going on now on the real calendar (valentine, easter, summer, autumn, christmas), or null: the server says. */
const eventsInfo = () => S.game?.restaurant?.events || S.catalog.events || {};
const currentEvent = () => eventsInfo().now || null;
const inSeason = (k) => !S.catalog.seasonal?.[k] || S.catalog.seasonal[k] === currentEvent();
/** "12 December": a date of an event window, in the player's language. */
const eventDate = (ev, edge) => {
    const d = eventsInfo().dates?.[ev]?.[edge];
    return d ? new Date(d + 'T12:00:00Z').toLocaleDateString(getLang() === 'it' ? 'it-IT' : 'en-GB', {day: 'numeric', month: 'long'}) : '';
};

function renderBuild() {
    const el = document.getElementById('buildpanel');
    if (!el || !S.editor.active) return;
    const r = S.game.restaurant, cat = S.catalog.items, ed = S.editor;
    const cats = BUILD_TABS.find(x => x[0] === buildTab)[1];
    const ev = currentEvent();
    const items = Object.entries(cat).filter(([k, d]) => buildTabOf(k, d) === buildTab && cats && (d.enabled !== false || r.finishes.includes(k)))
        .sort((a, b) => (buildTab === 'seasonal' ? inSeason(b[0]) - inSeason(a[0]) : 0) || SORTS[buildSort](a, b)); // in-season pieces first
    const c = ed.cost();
    // The storage room: pieces put away, placed again for free (or sold, if they were already stored before this session)
    const stock = ed.storageList();
    const storeTile = ([k, count]) => {
        const d = cat[k], sellable = (ed.storage[k] || 0) - ed.draft.filter(x => x.st && x.k === k).length > 0;
        const refund = Math.floor(d.price * S.catalog.rules.sellBack);
        return h('div.shopitem.stored' + (ed.placing?.it.k === k && ed.placing.it.st ? '.on' : ''), {onclick: () => ed.startPlacing(k, null, 0, true)},
            h('span.stock-count', '×' + count), h('img.thumb3d', {src: thumbFor(k), alt: '', loading: 'lazy'}), h('b', n(k)), h('small.free', t('storage_free')),
            sellable && h('button.btn.btn-small.btn-danger.stock-sell', {
                title: t('storage_sell_hint'),
                onclick: (e) => {
                    e.stopPropagation();
                    api('game.storage_sell', {k}).then(st => {
                        r.storage = st.restaurant?.storage || {};
                        r.coins = st.restaurant?.coins ?? r.coins;
                        ed.storage = {...r.storage};
                        toast(t('storage_sold', {c: st.result?.refund ?? refund}), 'good');
                        renderTop();
                        renderBuild();
                    }).catch(fail);
                },
            }, t('sell'), ' +', String(refund)));
    };
    const tile = ([k, d]) => {
        const sev = S.catalog.seasonal?.[k], away = sev && !inSeason(k);
        const roles = rolesOfItem(k, d);
        const locked = d.level > r.level || away;
        const finish = d.cat === 'floor' || d.cat === 'wall';
        const owned = finish && r.finishes.includes(k);
        const current = finish && ed[d.cat] === k;
        return h('button.shopitem' + (locked ? '.locked' : '') + (current || ed.placing?.it.k === k ? '.on' : ''), {
            disabled: locked,
            onclick: () => finish ? ed.setFinish(k) : ed.startPlacing(k),
        }, roles.length ? h('span.role-badges', {title: roles.map(x => t('for_role_' + x)).join(' · ')}, roles.map(x => h('span.role-badge', {html: svgIcon(...ROLE_ICON[x])}))) : null, finish ? h('img', {src: swatch(k), alt: ''}) : h('img.thumb3d', {src: thumbFor(k), alt: '', loading: 'lazy'}),
            h('b', n(k)), sev ? h('span.tier.seasonal', t('ev_' + sev)) : d.tier ? h('span.tier.tier-' + d.tier, t('tier_' + d.tier)) : null,
            h('small', away ? t('ev_back', {m: eventDate(sev, 'from')}) : locked ? t('locked_level', {n: d.level}) : owned ? t('owned') : coin(d.price)),
            d.pop > 0 && h('small.pop', t('pop', {n: d.pop})), d.speed > 1 && h('small.pop', t('speed', {n: d.speed})));
    };
    put(el, 
        h('div.build-head',
            h('div.tabs.scroll', BUILD_TABS.map(([k]) => h('button.tab' + (buildTab === k ? '.on' : ''), {onclick: () => ((buildTab = k), S.world.frameOutdoor(k === 'outdoor'), renderBuild())},
                t('cat_' + k), k === 'storage' && stock.length ? ` (${stock.reduce((a, [, x]) => a + x, 0)})` : ''))),
            ed.doorMove ? h('div.placing', h('span', t('door_pick')), h('button.btn.btn-small.btn-ghost', {onclick: () => ed.stopDoorMove()}, t('cancel'))) :
            ed.placing ? h('div.placing',
                    h('span', t(ed.placing.moving ? 'moving_piece' : 'placing')),
                    h('button.icon-btn', {title: t('rotate_left') + ' (Q)', onclick: () => ed.rotate(-1)}, icon('left')),
                    h('button.icon-btn', {title: t('rotate_right') + ' (R)', onclick: () => ed.rotate(1)}, icon('right')),
                    h('button.btn.btn-small.btn-primary', {disabled: !ed.placing.ok, onclick: () => ed.confirm()}, t('place_here')),
                    !ed.placing.moving ? h('label.repeat', {title: t('repeat_hint')}, h('input', {type: 'checkbox', checked: ed.repeatFor(cat[ed.placing.it.k].cat), onchange: (e) => ed.setRepeat(cat[ed.placing.it.k].cat, e.target.checked)}), h('span', t('repeat_place'))) : null,
                    h('button.btn.btn-small.btn-ghost', {onclick: () => ed.cancelPlacing()}, t('cancel')))
                : h('div.build-tools', h('small.muted', t('build_hint')),
                    h('label.sortby', h('span', t('sort_by')), h('select', {
                        onchange: (e) => {
                            buildSort = e.target.value;
                            try {
                                localStorage.setItem('forkville-sort', buildSort);
                            } catch (err) { /* storage blocked */ }
                            renderBuild();
                        },
                    }, Object.keys(SORTS).map(k => h('option', {value: k, selected: k === buildSort}, t('sort_' + k))))))),
        buildTab === 'seasonal' ? h('div.event-banner' + (ev ? '.on' : ''), ic('ui', ev === 'christmas' || ev === 'autumn' ? 's_winter' : 's_spring'),
            h('div', h('b', ev ? t('ev_now', {e: t('ev_' + ev)}) : t('ev_none')), h('small', [ev ? t('ev_until', {m: eventDate(ev, 'to')}) + ' · ' : '', t('ev_kept')].join('')))) : null,
        buildTab === 'storage' ? (stock.length ? h('div.shop', stock.map(storeTile)) : h('p.muted.storage-empty', t('storage_empty'))) : h('div.shop', items.map(tile)),
        h('div.build-foot',
            h('div.cost', h('span', t('spend') + ' '), coin(c.spend), c.refund ? [h('span', ' · ' + t('refund') + ' '), coin(c.refund)] : null),
            h('button.btn.btn-small.btn-ghost', {onclick: openDoor}, icon('door'), t('door_title')),
            h('button.btn.btn-small.btn-ghost', {onclick: openExpand}, icon('expand'), t('expand_title')),
            h('button.btn', {onclick: exitBuild}, t('cancel')),
            h('button.btn.btn-primary', {disabled: c.net > r.coins, onclick: saveBuild}, t('save'))));
}

/** The front door: its style (with a picture of each) and where it stands on the front wall. */
function openDoor() {
    const r = S.game.restaurant, cur = r.doorStyle || 'wood';
    const styles = Object.entries(S.catalog.doors || {});
    const move = () => {
        if (S.editor.dirty()) return toast(t('door_save_first'), 'bad');
        if (r.coins < (S.catalog.doorMove || 250)) return toast(errText('no_coins'), 'bad');
        closeModal();
        S.editor.startDoorMove();
    };
    modal(t('door_title'), h('div.doorpick',
        h('div.door-grid', styles.map(([k, d]) => h('button.door-opt' + (k === cur ? '.on' : ''), {
            disabled: d.level > r.level || k === cur,
            onclick: () => {
                if (S.editor.dirty()) return toast(t('door_save_first'), 'bad');
                closeModal();
                act('game.door', {style: k}, {done: () => (S.editor.end(), enterBuild(buildTab), toast(t('door_changed'), 'good'))});
            },
        }, h('img', {src: doorThumb(k), alt: ''}), h('b', t('door_' + k)), h('small', k === cur ? t('door_current') : d.level > r.level ? t('locked_level', {n: d.level}) : d.price ? coin(d.price) : t('free'))))),
        h('div.expand-opt', h('div.expand-pic', icon('door')), h('div', h('b', t('door_move')), h('small.muted', t('door_move_d'))),
            h('button.btn.btn-primary.btn-small', {onclick: move}, coin(S.catalog.doorMove || 250)))), 'door-modal');
}

/** Floor buttons (when the restaurant has upper floors): the floors above the chosen one are hidden. */
function floorPicker() {
    const fl = (S.mode === 'visit' ? S.visit?.restaurant?.floors : S.game?.restaurant?.floors) || [];
    if (!fl.length) return null;
    const cur = S.world.viewFloor || 0;
    return h('div.floorpick',
        h('button.cam', {title: t('floor_up') + ' (PgUp)', disabled: cur >= fl.length, onclick: () => goFloor(cur + 1)}, icon('up')),
        h('div.floor-now', h('small', t('floor_view')), h('b', cur ? String(cur) : t('floor_ground_short'))),
        h('button.cam', {title: t('floor_down') + ' (PgDn)', disabled: cur <= 0, onclick: () => goFloor(cur - 1)}, icon('down')));
}

/** Look at (and build on) another floor, one at a time. */
function goFloor(f) {
    const max = (S.mode === 'visit' ? S.visit?.restaurant?.floors : S.game?.restaurant?.floors)?.length || 0;
    if (f < 0 || f > max) return;
    S.world.setViewFloor(f);
    if (S.editor?.active) S.editor.setLevel(f);
    renderHud();
}
// On touch screens a long press or a double tap would select text or open the callout: only fields may
document.addEventListener('selectstart', (e) => {
    if (!e.target.closest?.('input, textarea, [contenteditable]')) e.preventDefault();
});
document.addEventListener('contextmenu', (e) => {
    if (!e.target.closest?.('input, textarea, a')) e.preventDefault();
});
window.addEventListener('keydown', (e) => {
    if (!S.world || e.target.closest?.('input, textarea')) return;
    if (e.key === 'PageUp') goFloor((S.world.viewFloor || 0) + 1);
    else if (e.key === 'PageDown') goFloor((S.world.viewFloor || 0) - 1);
});

/** How long a side of the room can be at a level (max_side in lib/catalog.php). */
const maxSide = (l) => Math.min(S.catalog.expand?.max || 22, (S.catalog.expand?.min || 10) + Math.floor(l / 2));

/** Coins to add a row or a column (expand_cost in lib/catalog.php). */
function expandCost(w, d, dir) {
    const area = dir === 'col' ? (w + 1) * d : w * (d + 1);
    return Math.round((S.catalog.expand?.rate ?? 18) * area * (1 + Math.max(0, area - 100) / 200));
}

/** Enlarge the room one row or one column at a time, and buy (or enlarge) the car park. */
function openExpand() {
    const r = S.game.restaurant, W = r.size, D = r.depth || r.size, max = maxSide(r.level), top = S.catalog.expand?.max || 22;
    const grow = (dir) => {
        const side = dir === 'col' ? W : D, cost = expandCost(W, D, dir);
        const locked = side >= max, full = side >= top;
        const nextLevel = Array.from({length: 40}, (_, i) => i + 1).find(l => maxSide(l) > side);
        return h('div.expand-opt',
            h('div.expand-pic', h('div.room-mini', {style: `--w:${dir === 'col' ? W + 1 : W};--d:${dir === 'row' ? D + 1 : D}`}, h('i.add-' + dir))),
            h('div', h('b', t(dir === 'col' ? 'add_col' : 'add_row')), h('small.muted', t(dir === 'col' ? 'add_col_d' : 'add_row_d', {w: dir === 'col' ? W + 1 : W, d: dir === 'row' ? D + 1 : D})),
                full ? h('small', t('max_size')) : locked ? h('small', t('locked_level', {n: nextLevel})) : null),
            h('button.btn.btn-primary.btn-small', {
                disabled: locked || cost > r.coins,
                onclick: () => {
                    closeModal();
                    act('game.expand', {dir}, {
                        done: () => {
                            sfx.levelUp?.();
                            if (S.editor.active) {
                                S.editor.end();
                                enterBuild(buildTab);
                            }
                        },
                    });
                },
            }, coin(cost)));
    };
    const pl = r.parking || 0, park = S.catalog.parking || {}, next = park[pl + 1];
    const gl = r.garage || 0, gar = S.catalog.garage || {}, gnext = gar[gl + 1];
    // Upper floors: each grows inside the one below; the next one can be built from its level
    const floors = r.floors || [], rectOf = (f) => f ? {x0: floors[f - 1][2], w: floors[f - 1][0], d: floors[f - 1][1]} : {x0: 0, w: W, d: D};
    const growUp = (f, dir) => {
        const me = rectOf(f), below = rectOf(f - 1), cost = Math.round(expandCost(me.w, me.d, dir) * 0.8);
        const room = dir === 'row' ? me.d + 1 <= below.d : me.x0 + me.w + 1 <= below.x0 + below.w || me.x0 > below.x0;
        const locked = (dir === 'col' ? me.w : me.d) >= max;
        return h('div.expand-opt',
            h('div.expand-pic', h('div.room-mini', {style: `--w:${dir === 'col' ? me.w + 1 : me.w};--d:${dir === 'row' ? me.d + 1 : me.d}`}, h('i.add-' + dir))),
            h('div', h('b', t(dir === 'col' ? 'add_col' : 'add_row')), h('small.muted', `${dir === 'col' ? me.w + 1 : me.w}×${dir === 'row' ? me.d + 1 : me.d}`),
                !room ? h('small', t('floor_below_full')) : locked ? h('small', t('locked_level', {n: Array.from({length: 40}, (_, i) => i + 1).find(l => maxSide(l) > (dir === 'col' ? me.w : me.d))})) : null),
            h('button.btn.btn-primary.btn-small', {
                disabled: !room || locked || cost > r.coins,
                onclick: () => {
                    closeModal();
                    act('game.expand', {dir, floor: f}, {done: () => S.editor.active && (S.editor.end(), enterBuild(buildTab))});
                },
            }, coin(cost)));
    };
    const nextFloor = (S.catalog.floors || {})[floors.length + 1];
    const floorSections = [
        ...floors.flatMap((fl, i) => [h('h3', icon('stairs'), t('floor_n', {n: i + 1}), h('small.muted', ` · ${fl[0]}×${fl[1]}`)), growUp(i + 1, 'col'), growUp(i + 1, 'row')]),
        nextFloor ? h('div.expand-opt', h('div.expand-pic', icon('stairs')),
            h('div', h('b', t('floor_build', {n: floors.length + 1})), h('small.muted', t('floor_build_d', {s: S.catalog.floorStart || 6})), nextFloor.level > r.level ? h('small', t('locked_level', {n: nextFloor.level})) : null),
            h('button.btn.btn-primary.btn-small', {
                disabled: nextFloor.level > r.level || nextFloor.cost > r.coins,
                onclick: () => {
                    closeModal();
                    act('game.floor', {}, {rebuild: true, done: () => {
                        toast(t('floor_built', {n: floors.length + 1}), 'good');
                        S.world.setViewFloor(floors.length + 1);
                        if (S.editor.active) (S.editor.end(), enterBuild('stairs'));
                        renderHud();
                    }});
                },
            }, coin(nextFloor.cost))) : null,
    ];
    modal(t('expand_title'), h('div.expand',
        h('p.muted', t('expand_now', {w: W, d: D, m: max})),
        h('h3', icon('home'), t('floor_ground')),
        grow('col'), grow('row'),
        ...floorSections,
        h('h3', icon('car'), t('parking_title')),
        h('p.muted', t('parking_d', {p: Math.round((S.catalog.carBonus || 0.8) * 100)})),
        pl ? h('p', t('parking_have', {n: park[pl].spaces})) : null,
        next ? h('div.expand-opt', h('div.expand-pic', ic('ui', 'car', 'pic big')),
            h('div', h('b', t(pl ? 'parking_more' : 'parking_buy', {n: next.spaces})), next.level > r.level ? h('small', t('locked_level', {n: next.level})) : null),
            h('button.btn.btn-primary.btn-small', {
                disabled: next.level > r.level || next.cost > r.coins,
                onclick: () => {
                    closeModal();
                    act('game.parking', {}, {rebuild: true, done: () => S.editor.active && (S.editor.end(), enterBuild(buildTab))});
                },
            }, coin(next.cost))) : h('p', t('parking_max')),
        // The underground car park (floor −1): later levels, many more spaces
        h('h3', icon('car'), t('garage_title')),
        h('p.muted', t('garage_d')),
        gl ? h('p', t('garage_have', {n: gar[gl].spaces})) : null,
        gnext ? h('div.expand-opt', h('div.expand-pic', ic('ui', 'garage', 'pic big')),
            h('div', h('b', t(gl ? 'garage_more' : 'garage_buy', {n: gnext.spaces})), gnext.level > r.level ? h('small', t('locked_level', {n: gnext.level})) : null),
            h('button.btn.btn-primary.btn-small', {
                disabled: gnext.level > r.level || gnext.cost > r.coins,
                onclick: () => {
                    closeModal();
                    act('game.garage', {}, {rebuild: true, done: () => S.editor.active && (S.editor.end(), enterBuild(buildTab))});
                },
            }, coin(gnext.cost))) : h('p', t('garage_max'))), 'expand-modal');
}

function renderSelection(it) {
    const el = document.getElementById('selbar');
    if (!el) return;
    if (!it) return put(el, );
    const refund = it.id > 0 ? Math.floor(S.catalog.items[it.k].price * S.catalog.rules.sellBack) : S.catalog.items[it.k].price;
    put(el, h('div.selcard', h('b', n(it.k)),
        h('button.icon-btn', {title: t('rotate_left') + ' (Q)', onclick: () => S.editor.rotate(-1)}, icon('left')),
        h('button.icon-btn', {title: t('rotate_right') + ' (R)', onclick: () => S.editor.rotate(1)}, icon('right')),
        h('button.btn.btn-small', {title: 'M', onclick: () => S.editor.moveSelected()}, t('move')),
        h('button.btn.btn-small', {title: 'C', disabled: !inSeason(it.k), onclick: () => S.editor.copySelected()}, t('copy_piece'), ' ', coin(S.catalog.items[it.k].price)),
        h('button.btn.btn-small', {title: t('store_hint'), onclick: () => S.editor.storeSelected()}, icon('box'), ' ', t('store_piece')),
        !it.st && h('button.btn.btn-small.btn-danger', {title: t('key_delete'), onclick: () => S.editor.sellSelected()}, t('sell'), ' +', String(refund)),
        h('button.icon-btn', {onclick: () => S.editor.select(null)}, icon('x')),
        h('small.muted.selhint', t('drag_hint'))));
}


// ---------------------------------------------------------------- modals

function modal(title, content, cls = '') {
    document.querySelector('.modal-bg')?.remove();
    const el = h('div.modal-bg', {onclick: (e) => e.target === el && closeModal()}, h('div.modal' + (cls ? '.' + cls : ''), h('h2', title), content));
    document.body.append(el);
}

/** Windows that must not be lost (a level-up) wait for the open one to close. */
const modalQueue = [];

function queueModal(show) {
    if (document.querySelector('.modal-bg')) modalQueue.push(show);
    else show();
}

function closeModal() {
    document.querySelector('.modal-bg')?.remove();
    if (modalQueue.length) setTimeout(() => !document.querySelector('.modal-bg') && modalQueue.shift()?.(), 250);
}

/** While you were away: who ran the restaurant, from when to when, and how to make it run longer. */
function showAway(a) {
    const hm = (ts) => new Date(ts * 1000).toLocaleTimeString(getLang() === 'it' ? 'it-IT' : 'en-GB', {hour: '2-digit', minute: '2-digit'});
    const lines = [];
    if (a.managed > 0) lines.push(h('p.away-line.ok', ic('ui', 'manager'), h('span', {html: t('away_slot_manager', {a: hm(a.from), b: hm(a.managedTo), p: a.managerPct})})));
    if (a.aloneTo > a.managedTo) lines.push(h('p.away-line', ic('ui', 'chef'), h('span', {html: t('away_slot_alone', {a: hm(a.managedTo), b: hm(a.aloneTo), p: a.alonePct})})));
    if (a.now - a.aloneTo > 120) lines.push(h('p.away-line.off', ic('mood', 'sleep'), h('span', {html: t('away_slot_closed', {a: hm(a.aloneTo)})})));
    modal(t('away_title'), [h('div.away-art', {html: svgIcon('ui', a.managed ? 'manager' : 'chef')}), h('p', {html: t('away_body', {n: a.served, m: a.minutes, c: a.coins})}),
        ...lines,
        h('p.tip', ic('mood', 'sparkle'), t(a.hasManager ? 'away_tip_promote' : 'away_tip_hire')),
        h('div.row', h('button.btn', {onclick: () => (closeModal(), openSheet('staff'))}, t('dock_staff')), h('button.btn.btn-primary', {onclick: closeModal, style: 'flex:1'}, 'OK'))]);
}

// Browsers allow sound only after a tap: the first one starts the music
document.addEventListener('pointerdown', () => startAudio(), {passive: true});
document.addEventListener('click', (e) => {
    if (e.target.closest?.('button, .btn, a.btn')) sfx.click();
}, true);

if ('serviceWorker' in navigator && location.protocol === 'https:') navigator.serviceWorker.register('sw.js').catch(() => {});
boot();
