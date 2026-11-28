/**
 * OpenNewsletter — the app: accounts, groups and their tabs, the review of proposals, the master admin.
 *
 * Routes (hash):  #/                     my groups
 *                 #/g/ID[/TAB]           a group: newsletters, subscribers, templates, members, share, settings
 *                 #/g/ID/n/NID           a newsletter: the editor while it is a draft, otherwise its page (preview, review)
 *                 #/g/ID/t/TID           a template of the group in the editor
 *                 #/account              profile, password, delete the account
 *                 #/master[/g/ID]        master admin of the whole platform
 * The newsletter editor is assets/editor.js; texts are in assets/i18n.js.
 */

import {t, getLang, setLang, UI_LANGS} from './i18n.js?v=18';
import {S, h, put, svg, iconBtn, logo, api, errText, toast, failToast, modal, confirmDialog, field, input, select, copyBox, fmtDate, partsIn, relTime, atLeast, rank, ROLES, LANG_NAMES} from './ui.js?v=18';
import {editorView, slotDialog, previewDialog} from './editor.js?v=18';

const app = document.getElementById('app');
const STATUS_ORDER = ['pending', 'scheduled', 'sending', 'draft', 'rejected', 'expired', 'sent', 'failed', 'cancelled'];
let cache = {group: null};

// ---------------------------------------------------------------- boot

async function boot() {
    try {
        S.config = await api('auth.config');
    } catch (e) {
        return fatal(errText(e));
    }
    if (!S.config.installed) return fatal(t('not_installed'));
    const q = new URLSearchParams(location.search);
    const verify = q.get('verify');
    if (verify) {
        history.replaceState(null, '', location.pathname + location.hash);
        return verifyScreen(verify);
    }
    try {
        const me = await api('auth.me');
        setUser(me.user, me.csrf);
    } catch (e) {
        return fatal(errText(e));
    }
    route();
}

function setUser(user, csrf) {
    S.user = user;
    if (csrf) S.csrf = csrf;
    if (user && user.lang && user.lang !== getLang()) setLang(user.lang);
}

function fatal(msg) {
    put(app, h('div.center-page', logo(56), h('h1', 'OpenNewsletter'), h('p', msg)));
}

window.addEventListener('hashchange', route);

function route() {
    document.title = 'OpenNewsletter';
    if (!S.user) return authScreen();
    if (!S.user.verified) return pendingScreen();
    const parts = location.hash.replace(/^#\/?/, '').split('/').filter(Boolean);
    if (parts[0] === 'g' && parts[1]) {
        if (parts[2] === 'n' && parts[3]) return newsletterPage(Number(parts[1]), Number(parts[3]));
        if (parts[2] === 't' && parts[3]) return templatePage(Number(parts[1]), Number(parts[3]));
        return groupPage(Number(parts[1]), parts[2] || 'newsletters');
    }
    if (parts[0] === 'account') return accountPage();
    if (parts[0] === 'master' && S.user.master) return parts[1] === 'g' && parts[2] ? masterGroup(Number(parts[2])) : masterPage();
    return dashboard();
}

function go(hash) {
    if (location.hash === hash) route();
    else location.hash = hash;
}

// ---------------------------------------------------------------- frame

function langSwitch() {
    return h('div.lang-switch', UI_LANGS.map((l) => h('button' + (l === getLang() ? '.on' : ''), {type: 'button', onclick: async () => {
        setLang(l);
        if (S.user) {
            try {
                setUser((await api('auth.profile', {lang: l})).user);
            } catch (e) { /* only the app language */ }
        }
        route();
    }}, l.toUpperCase())));
}

function credit() {
    return h('footer.app-foot', h('a', {href: 'terms.php?lang=' + getLang(), target: '_blank'}, t('terms')),
        h('a.credit', {href: 'https://www.saveriomorelli.com/', target: '_blank', rel: 'noopener'}, h('span', t('made_by')), h('span.credit-name', 'Saverio Morelli')));
}

function brand() {
    return h('a.brand', {href: '#/'}, logo(30), h('span', 'Open', h('b', h('span.brand-n', 'N'), 'ewsletter')));
}

function userMenu() {
    const menu = h('div.menu', {hidden: true},
        h('div.menu-who', h('strong', S.user.name), h('span.muted', S.user.email)),
        h('a.menu-item', {href: '#/account'}, svg('user'), t('account')),
        S.user.master ? h('a.menu-item', {href: '#/master'}, svg('shield'), t('master')) : null,
        h('div.menu-item.menu-lang', svg('globe'), langSwitch()),
        h('button.menu-item', {type: 'button', onclick: logout}, svg('logout'), t('logout')));
    const btn = h('button.avatar', {type: 'button', 'aria-label': t('account'), onclick: (ev) => {
        ev.stopPropagation();
        menu.hidden = !menu.hidden;
    }}, initials(S.user.name));
    document.addEventListener('click', (ev) => { if (!menu.contains(ev.target)) menu.hidden = true; });
    return h('div.user-menu', btn, menu);
}

function initials(name) {
    return (name || '?').split(/\s+/).filter(Boolean).slice(0, 2).map((w) => w[0].toUpperCase()).join('');
}

function shell(...content) {
    put(app, h('header.topbar', brand(), h('div.topbar-mid'), userMenu()), h('main.page', content), credit());
    window.scrollTo(0, 0);
}

function loading() {
    return h('div.loading', h('span.spin'), t('loading'));
}

async function logout() {
    try {
        await api('auth.logout', {});
    } catch (e) { /* signed out anyway */ }
    S.user = null;
    S.csrf = '';
    go('#/');
}

// ---------------------------------------------------------------- sign in / sign up

let gisLoaded = null;

function loadGoogle() {
    if (!gisLoaded) gisLoaded = new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = 'https://accounts.google.com/gsi/client';
        s.async = true;
        s.onload = resolve;
        s.onerror = reject;
        document.head.append(s);
    });
    return gisLoaded;
}

function googleButton(box, onCredential) {
    if (!S.config.googleClientId) return;
    loadGoogle().then(() => {
        google.accounts.id.initialize({client_id: S.config.googleClientId, callback: (r) => onCredential(r.credential), ux_mode: 'popup'});
        google.accounts.id.renderButton(box, {theme: 'outline', size: 'large', shape: 'pill', text: 'continue_with', width: 300, locale: getLang()});
    }).catch(() => {});
}

function authScreen(mode = 'login') {
    const card = h('div.auth-card');
    const draw = () => {
        const err = h('p.form-error', {role: 'alert', hidden: true});
        const showErr = (e) => { err.textContent = errText(e); err.hidden = false; };
        const email = input({type: 'email', autocomplete: 'email', required: true, maxlength: 190});
        const pw = input({type: 'password', autocomplete: mode === 'login' ? 'current-password' : 'new-password', required: true, minlength: 8});
        const name = input({autocomplete: 'name', maxlength: 80, required: true});
        const terms = h('input', {type: 'checkbox'});
        const submit = h('button.btn.btn-primary.btn-lg.btn-block', {type: 'submit'}, t(mode === 'login' ? 'sign_in' : 'create_account'));
        const form = h('form.stack', {onsubmit: async (ev) => {
            ev.preventDefault();
            err.hidden = true;
            submit.disabled = true;
            try {
                if (mode === 'login') {
                    const r = await api('auth.login', {email: email.value, password: pw.value});
                    setUser(r.user, r.csrf);
                    route();
                } else {
                    if (!terms.checked) throw {code: 'terms_required'};
                    await api('auth.register', {name: name.value, email: email.value, password: pw.value, acceptTerms: true, lang: getLang()});
                    put(card, h('div.center', h('div.big-ico', svg('mail', 'ico ico-xl')), h('h2', t('check_inbox')), h('p', t('check_inbox_text', {email: email.value})),
                        h('button.btn.btn-soft', {type: 'button', onclick: () => { mode = 'login'; draw(); }}, t('back_to_login'))));
                }
            } catch (e) {
                showErr(e);
            } finally {
                submit.disabled = false;
            }
        }},
            mode === 'register' ? field(t('your_name'), name) : null,
            field(t('email'), email, mode === 'register' ? t('email_fixed') : null),
            field(t('password'), pw, mode === 'register' ? t('password_hint') : null),
            mode === 'register' ? h('label.check', terms, h('span', t('accept_terms_pre'), ' ', h('a', {href: 'terms.php?lang=' + getLang(), target: '_blank'}, t('terms_link')))) : null,
            err, submit);
        const g = h('div.google-box');
        const gTerms = h('p.muted.small.center', t('google_terms'), ' ', h('a', {href: 'terms.php?lang=' + getLang(), target: '_blank'}, t('terms_link')));
        googleButton(g, async (credential) => {
            try {
                let r = await api('auth.google', {credential, lang: getLang()});
                if (r.error === 'needs_signup') {
                    const ok = await confirmDialog(t('google_new_title'), t('google_new_text'), t('create_account'));
                    if (!ok) return;
                    r = await api('auth.google', {credential, acceptTerms: true, lang: getLang()});
                }
                setUser(r.user, r.csrf);
                route();
            } catch (e) {
                showErr(e);
            }
        });
        put(card,
            h('div.seg.seg-block', h('button' + (mode === 'login' ? '.on' : ''), {type: 'button', onclick: () => { mode = 'login'; draw(); }}, t('sign_in')),
                h('button' + (mode === 'register' ? '.on' : ''), {type: 'button', onclick: () => { mode = 'register'; draw(); }}, t('create_account'))),
            form, S.config.googleClientId ? [h('div.or', h('span', t('or'))), g, gTerms] : null,
            !S.config.mailReady && mode === 'register' ? h('p.muted.small', t('mail_not_ready')) : null);
    };
    draw();
    const features = [['layout', 'feat_editor'], ['review', 'feat_review'], ['users', 'feat_roles'], ['code', 'feat_widget']];
    put(app, h('div.landing',
        h('header.landing-top', brand(), langSwitch()),
        h('section.hero',
            h('div.hero-text',
                h('span.kicker', t('kicker')),
                h('h1', t('hero_title')),
                h('p.lead', t('hero_lead')),
                h('ul.features', features.map(([icon, key]) => h('li', h('span.feat-ico', svg(icon)), h('div', h('strong', t(key)), h('span', t(key + '_text')))))),
            ),
            h('div.hero-card', card)),
        h('div.hero-art', {'aria-hidden': 'true'}, h('div.art-mail.a1'), h('div.art-mail.a2'), h('div.art-mail.a3')),
        credit()));
}

function verifyScreen(token) {
    const btn = h('button.btn.btn-primary.btn-lg', {type: 'button', onclick: async () => {
        btn.disabled = true;
        try {
            const r = await api('auth.verify', {token});
            setUser(r.user, r.csrf);
            toast(t('verified_ok'));
            go('#/');
        } catch (e) {
            put(box, h('div.big-ico', svg('x', 'ico ico-xl')), h('h1', t('link_expired')), h('p', errText(e)), h('a.btn.btn-soft', {href: './'}, t('back_to_login')));
        }
    }}, svg('check'), t('confirm_email'));
    const box = h('div.center-card', h('div.big-ico', svg('mail', 'ico ico-xl')), h('h1', t('confirm_email_title')), h('p', t('confirm_email_text')), btn);
    put(app, h('div.landing', h('header.landing-top', brand(), langSwitch()), box, credit()));
}

function pendingScreen() {
    const resend = h('button.btn.btn-primary', {type: 'button', onclick: async () => {
        resend.disabled = true;
        try {
            await api('auth.resend', {lang: getLang()});
            toast(t('link_sent'));
        } catch (e) {
            failToast(e);
        } finally {
            resend.disabled = false;
        }
    }}, svg('send'), t('resend_link'));
    put(app, h('div.landing', h('header.landing-top', brand(), langSwitch()),
        h('div.center-card', h('div.big-ico', svg('mail', 'ico ico-xl')), h('h1', t('check_inbox')), h('p', t('pending_text', {email: S.user.email})),
            !S.user.mailReady ? h('p.muted.small', t('mail_not_ready')) : null,
            h('div.row-btns', resend, h('button.btn.btn-soft', {type: 'button', onclick: logout}, t('logout')))), credit()));
}

// ---------------------------------------------------------------- dashboard

async function dashboard() {
    shell(loading());
    let groups;
    try {
        groups = (await api('group.list')).groups;
    } catch (e) {
        return shell(h('p.form-error', errText(e)));
    }
    const cards = groups.map((g) => h('a.group-card', {href: '#/g/' + g.id, style: {'--g': g.accent}},
        h('div.group-card-top', h('span.group-dot', g.name[0].toUpperCase()), h('span.chip.role-' + g.role, t('role_' + g.role))),
        h('h3', g.name), g.description ? h('p.muted', g.description) : null,
        h('div.group-stats',
            h('span', svg('users', 'ico ico-sm'), t('n_subscribers', {n: g.subscribers})),
            g.pending ? h('span.hl', svg('review', 'ico ico-sm'), t('n_to_review', {n: g.pending})) : null,
            g.scheduled ? h('span', svg('calendar', 'ico ico-sm'), t('n_scheduled', {n: g.scheduled})) : null),
        g.status === 'disabled' ? h('p.warn.small', svg('ban', 'ico ico-sm'), t('group_disabled')) : !g.sendingReady && atLeast(g.role, 'admin') ? h('p.warn.small', svg('mail', 'ico ico-sm'), t('setup_sending')) : null));
    shell(
        h('div.page-head', h('div', h('h1', t('hello', {name: S.user.name.split(' ')[0]})), h('p.muted', t(groups.length ? 'dash_lead' : 'dash_empty'))),
            h('button.btn.btn-primary', {type: 'button', onclick: createGroup}, svg('plus'), t('new_group'))),
        h('div.group-grid', cards, h('button.group-card.group-new', {type: 'button', onclick: createGroup}, svg('plus', 'ico ico-lg'), h('strong', t('new_group')), h('span.muted', t('new_group_text')))),
        !groups.length ? h('p.muted.small.center', t('invite_note')) : null);
}

function langChips(selected, onChange) {
    const box = h('div.chips');
    const draw = () => put(box, S.config.langs.map((l) => h('button.chip-toggle' + (selected.includes(l) ? '.on' : ''), {type: 'button', onclick: () => {
        if (selected.includes(l)) {
            if (selected.length > 1) selected.splice(selected.indexOf(l), 1);
        } else selected.push(l);
        draw();
        onChange && onChange(selected);
    }}, LANG_NAMES[l] || l)));
    draw();
    return box;
}

function timezones() {
    try {
        return Intl.supportedValuesOf('timeZone');
    } catch (e) {
        return ['Europe/Rome', 'Europe/London', 'Europe/Paris', 'Europe/Berlin', 'Europe/Madrid', 'Europe/Lisbon', 'America/New_York', 'America/Los_Angeles', 'UTC'];
    }
}

function createGroup() {
    const name = input({maxlength: 80, placeholder: t('group_name_ph'), autofocus: true});
    const slug = input({maxlength: 40, placeholder: 'my-newsletter'});
    const desc = input({maxlength: 300, placeholder: t('group_desc_ph')});
    const langs = [S.config.langs.includes(getLang()) ? getLang() : 'en'];
    const tz = select(timezones().map((z) => [z, z]), Intl.DateTimeFormat().resolvedOptions().timeZone || S.config.timezone);
    let touched = false;
    let timer;
    name.addEventListener('input', () => {
        if (touched) return;
        clearTimeout(timer);
        timer = setTimeout(async () => {
            if (!name.value.trim()) return;
            try {
                slug.value = (await api('group.slug', null, 'name=' + encodeURIComponent(name.value))).slug;
            } catch (e) { /* typed by hand */ }
        }, 300);
    });
    slug.addEventListener('input', () => { touched = true; });
    modal(t('new_group'), [
        field(t('group_name'), name), field(t('group_slug'), slug, t('group_slug_hint')), field(t('group_desc'), desc),
        field(t('sub_langs'), langChips(langs), t('sub_langs_hint')), field(t('timezone'), tz, t('timezone_hint')),
    ], [
        {label: t('cancel')},
        {label: t('create'), primary: true, icon: 'plus', onclick: async () => {
            try {
                const r = await api('group.create', {name: name.value, slug: slug.value, description: desc.value, langs, timezone: tz.value});
                go('#/g/' + r.group.id + '/settings');
                toast(t('group_created'));
            } catch (e) {
                failToast(e);
                return false;
            }
        }},
    ]);
}

// ---------------------------------------------------------------- group page

const TABS = [
    ['newsletters', 'mail', 'user'], ['subscribers', 'users', 'publisher'], ['templates', 'layout', 'moderator'],
    ['members', 'user', 'user'], ['share', 'code', 'user'], ['settings', 'settings', 'admin'],
];

async function loadGroup(id) {
    const g = (await api('group.get', null, 'group=' + id)).group;
    cache.group = g;
    return g;
}

async function groupPage(id, tab) {
    shell(loading());
    let g;
    try {
        g = await loadGroup(id);
    } catch (e) {
        return shell(h('p.form-error', errText(e)), h('a.btn.btn-soft', {href: '#/'}, svg('back'), t('back')));
    }
    document.title = g.name + ' · OpenNewsletter';
    const tabs = TABS.filter(([, , min]) => atLeast(g.role, min));
    if (!tabs.some(([k]) => k === tab)) tab = 'newsletters';
    const body = h('div.tab-body', loading());
    shell(
        h('div.group-head', {style: {'--g': g.page.accent}},
            h('a.crumb', {href: '#/'}, svg('back', 'ico ico-sm'), t('my_groups')),
            h('div.group-title', h('span.group-dot.big', g.name[0].toUpperCase()), h('div', h('h1', g.name), h('p.muted', h('span.chip.role-' + g.role, t('role_' + g.role)), ' ', t('n_subscribers', {n: g.subscribers.active})))),
            g.status === 'disabled' ? h('p.banner.banner-warn', svg('ban'), t('group_disabled_long')) : null,
            !g.sendingReady && g.status === 'active' ? h('p.banner', svg('mail'), atLeast(g.role, 'admin') ? h('span', t('setup_sending_long'), ' ', h('a', {href: '#/g/' + g.id + '/settings'}, t('open_settings'))) : t('sending_not_ready')) : null,
            h('nav.tabs', tabs.map(([k, icon]) => h('a.tab' + (k === tab ? '.on' : ''), {href: '#/g/' + g.id + '/' + k}, svg(icon), h('span', t('tab_' + k)))))),
        body);
    const views = {newsletters: tabNewsletters, subscribers: tabSubscribers, templates: tabTemplates, members: tabMembers, share: tabShare, settings: tabSettings};
    try {
        await views[tab](g, body);
    } catch (e) {
        put(body, h('p.form-error', errText(e)));
    }
}

function statusChip(st) {
    return h('span.status.st-' + st, t('st_' + st));
}

/**
 * What can be done to a newsletter without opening it: delete a draft, move or unschedule a scheduled one, change the
 * time of a proposal. Sent ones have no actions: subscribers already received them.
 */
function nlActions(g, n, refresh) {
    const mine = n.authorId === S.user.id;
    const pub = atLeast(g.role, 'publisher');
    const mod = atLeast(g.role, 'moderator') && g.status === 'active';
    const out = [];
    const run = async (route, extra, msg) => {
        try {
            await api(route, {id: n.id, ...extra});
            toast(msg);
            refresh();
        } catch (e) {
            failToast(e);
        }
    };
    if (mod && ['draft', 'rejected', 'expired', 'cancelled'].includes(n.status) && (mine || pub)) {
        out.push(['trash', t('delete'), async () => {
            if (await confirmDialog(t('delete_nl_title'), t('delete_nl_text_named', {subject: n.subject || t('untitled')}), t('delete'), true)) run('nl.delete', {}, t('deleted_ok'));
        }, true]);
    }
    if (mod && ((n.status === 'scheduled' && pub) || (n.status === 'pending' && (mine || pub)))) {
        out.push(['calendar', t(n.status === 'scheduled' ? 'reschedule' : 'change_time'), async () => {
            const when = n.scheduledAt || n.proposedAt;
            const pick = await slotDialog(g, {title: t('reschedule_title'), action: t('save'), initial: when ? partsIn(when, g.timezone) : null});
            if (pick) run('nl.reschedule', {date: pick.date, time: pick.time}, t('rescheduled_ok'));
        }]);
    }
    if (mod && n.status === 'scheduled' && pub) out.push(['undo', t('unschedule'), () => run('nl.withdraw', {}, t('withdrawn_ok'))]);
    return out;
}

function nlRow(g, n, refresh) {
    const when = n.status === 'sent' ? n.sentAt : n.scheduledAt || n.proposedAt;
    const open = () => go('#/g/' + g.id + '/n/' + n.id);
    const acts = nlActions(g, n, refresh).map(([icon, label, fn, danger]) => iconBtn(icon, label, (ev) => { ev.stopPropagation(); fn(); }, danger ? '.danger-ico' : ''));
    return h('div.nl-row', {role: 'link', tabindex: '0', onclick: open, onkeydown: (ev) => { if (ev.key === 'Enter' && ev.target === ev.currentTarget) open(); }},
        h('div.nl-main', h('strong', n.subject || t('untitled')), h('span.muted.small', [n.author ? t('by', {name: n.author}) : null, n.lang ? ' · ' + (LANG_NAMES[n.lang] || n.lang) : ' · ' + t('all_langs')])),
        h('div.nl-when', when ? [h('span', fmtDate(when, g.timezone)), ['pending', 'scheduled'].includes(n.status) ? h('span.muted.small', relTime(when)) : null] : h('span.muted.small', t('updated', {when: fmtDate(n.updatedAt, g.timezone)}))),
        n.status === 'sent' || n.status === 'sending' ? h('div.nl-num', h('strong', String(n.sent)), h('span.muted.small', ' / ' + n.total)) : h('div.nl-num'),
        statusChip(n.status),
        h('div.nl-acts', acts));
}

async function tabNewsletters(g, body) {
    const list = (await api('nl.list', null, 'group=' + g.id)).newsletters;
    const by = {};
    for (const n of list) (by[n.status] = by[n.status] || []).push(n);
    const sections = [
        ['pending', atLeast(g.role, 'publisher') ? 'sec_review' : 'sec_pending'], ['scheduled', 'sec_scheduled'], ['sending', 'sec_sending'],
        ['draft', 'sec_drafts'], ['rejected', 'sec_rejected'], ['expired', 'sec_expired'], ['sent', 'sec_sent'], ['failed', 'sec_failed'], ['cancelled', 'sec_cancelled'],
    ];
    const c = g.subscribers;
    put(body,
        h('div.stat-row',
            stat(t('stat_active'), c.active, 'users'), stat(t('stat_pending'), c.pending, 'clock'),
            stat(t('stat_scheduled'), (by.scheduled || []).length + (by.sending || []).length, 'calendar'), stat(t('stat_sent'), (by.sent || []).length, 'send')),
        h('div.section-head', h('h2', t('tab_newsletters')), atLeast(g.role, 'moderator') && g.status === 'active' ? h('button.btn.btn-primary', {type: 'button', onclick: () => newNewsletter(g)}, svg('plus'), t('new_newsletter')) : null),
        !list.length ? h('div.empty', svg('mail', 'ico ico-xl'), h('h3', t('no_newsletters')), h('p.muted', t(atLeast(g.role, 'moderator') ? 'no_newsletters_text' : 'no_newsletters_user'))) : null,
        sections.filter(([st]) => by[st]).map(([st, title]) => h('section.nl-section' + (st === 'pending' && atLeast(g.role, 'publisher') ? '.hl' : ''),
            h('h3', t(title), h('span.count', String(by[st].length))), h('div.nl-list', by[st].map((n) => nlRow(g, n, () => groupPage(g.id, 'newsletters')))))));
}

function stat(label, value, icon) {
    return h('div.stat', h('span.stat-ico', svg(icon)), h('div', h('strong', String(value)), h('span', label)));
}

/** Built-in templates with this user's favourites first (in the order they were starred). */
function sortedBuiltin(builtin, favs) {
    const rank = (id) => (favs.includes(id) ? favs.indexOf(id) : 1000);
    return [...builtin].sort((a, b) => rank(a.id) - rank(b.id) || builtin.indexOf(a) - builtin.indexOf(b));
}

/** The star that marks a built-in template as a favourite of the signed-in user. */
function favStar(id, favs, onChange) {
    const on = favs.includes(id);
    return h('button.fav-star' + (on ? '.on' : ''), {type: 'button', 'aria-pressed': on ? 'true' : 'false', title: t(on ? 'fav_remove' : 'fav_add'), 'aria-label': t(on ? 'fav_remove' : 'fav_add'), onclick: async (ev) => {
        ev.stopPropagation();
        try {
            const r = await api('tpl.fav', {id, on: !on});
            favs.splice(0, favs.length, ...r.favorites);
            onChange();
        } catch (e) {
            failToast(e);
        }
    }}, svg('star'));
}

async function newNewsletter(g) {
    const r = await api('tpl.list', null, 'group=' + g.id);
    const favs = r.favorites || [];
    let chosen = r.own.length ? String(r.own[0].id) : (favs[0] || 'b:blank');
    const lang = g.langs.length > 1 ? select([['', t('all_langs')], ...g.langs.map((l) => [l, LANG_NAMES[l] || l])], '') : null;
    const subject = input({maxlength: 200, placeholder: t('subject_ph'), autofocus: true});
    const box = h('div.tpl-picker');
    const pick = (id) => {
        chosen = id;
        box.querySelectorAll('.tpl-card').forEach((c) => {
            c.classList.toggle('on', c.dataset.id === id);
            c.setAttribute('aria-checked', c.dataset.id === id ? 'true' : 'false');
        });
    };
    const card = (id, name, desc, doc, star) => h('div.tpl-card' + (id === chosen ? '.on' : ''), {role: 'radio', tabindex: '0', 'aria-checked': id === chosen ? 'true' : 'false', 'data-id': id,
        onclick: () => pick(id), onkeydown: (ev) => { if ((ev.key === 'Enter' || ev.key === ' ') && ev.target === ev.currentTarget) { ev.preventDefault(); pick(id); } }},
        tplThumb(doc), h('div.tpl-card-title', h('strong', name), star), desc ? h('span.muted.small', desc) : null);
    const draw = () => put(box,
        h('h4', svg('users', 'ico ico-sm'), t('own_templates')),
        r.own.length ? h('div.tpl-grid', {role: 'radiogroup'}, r.own.map((o) => card(String(o.id), o.name, o.author ? t('by', {name: o.author}) : '', o.doc, null)))
            : h('p.muted.small', t('no_templates_short')),
        h('h4', svg('layout', 'ico ico-sm'), t('builtin_templates')),
        h('div.tpl-grid', {role: 'radiogroup'}, sortedBuiltin(r.builtin, favs).map((b) => card(b.id, b.name, b.description, b.doc, favStar(b.id, favs, draw)))));
    draw();
    modal(t('new_newsletter'), [field(t('subject'), subject), lang ? field(t('audience'), lang, t('audience_hint')) : null, box], [
        {label: t('cancel')},
        {label: t('start_writing'), primary: true, icon: 'chev', onclick: async () => {
            try {
                const n = (await api('nl.create', {group: g.id, template: chosen, subject: subject.value, lang: lang ? lang.value : null})).newsletter;
                go('#/g/' + g.id + '/n/' + n.id);
            } catch (e) {
                failToast(e);
                return false;
            }
        }},
    ], {wide: true});
}

/** A tiny drawing of a template: its colours and the shape of its blocks. */
function tplThumb(doc) {
    const th = doc.theme || {};
    const rows = (doc.blocks || []).slice(0, 7).map((b) => h('i.tb-' + (b.type === 'social' ? 'netlinks' : b.type), {style: {background: ['button'].includes(b.type) ? th.accent : null}}));
    return h('div.tpl-thumb', {style: {background: th.bg}}, h('div', {style: {background: th.card, '--a': th.accent}}, rows));
}

// ---------------------------------------------------------------- newsletter page (not editable: preview and review)

async function newsletterPage(gid, nid) {
    shell(loading());
    let g, r;
    try {
        [g, r] = await Promise.all([cache.group && cache.group.id === gid ? cache.group : loadGroup(gid), api('nl.get', null, 'id=' + nid)]);
    } catch (e) {
        return shell(h('p.form-error', errText(e)), h('a.btn.btn-soft', {href: '#/g/' + gid}, svg('back'), t('back')));
    }
    const n = r.newsletter;
    document.title = (n.subject || t('untitled')) + ' · OpenNewsletter';
    if (r.canEdit && g.status === 'active') {
        put(app, h('div.editor-root'));
        editorView(app.firstChild, {group: g, nl: n, role: g.role, onBack: () => go('#/g/' + g.id), onChange: () => newsletterPage(gid, nid)});
        return;
    }
    const frame = h('iframe.preview-frame.inline', {title: n.subject, sandbox: 'allow-same-origin allow-popups allow-popups-to-escape-sandbox', referrerpolicy: 'no-referrer'});
    api('nl.preview', null, 'id=' + n.id).then((p) => { frame.srcdoc = p.html; }).catch(failToast);
    const mine = n.authorId === S.user.id;
    const pub = atLeast(g.role, 'publisher');
    const act = async (route, extra = {}, msg = '') => {
        try {
            await api(route, {id: n.id, ...extra});
            if (msg) toast(msg);
            newsletterPage(gid, nid);
        } catch (e) {
            failToast(e);
        }
    };
    const buttons = [];
    if (n.status === 'pending' && pub) {
        buttons.push(h('button.btn.btn-primary', {type: 'button', onclick: async () => {
            const ok = await confirmDialog(t('approve_title'), t('approve_text', {when: fmtDate(n.proposedAt, g.timezone)}), t('approve'));
            if (ok) act('nl.approve', {}, t('approved_ok'));
        }}, svg('check'), t('approve')));
        buttons.push(h('button.btn.btn-soft', {type: 'button', onclick: async () => {
            const pick = await slotDialog(g, {title: t('approve_other_title'), action: t('approve'), initial: null});
            if (pick) act('nl.approve', {date: pick.date, time: pick.time}, t('approved_ok'));
        }}, svg('calendar'), t('approve_other')));
        buttons.push(h('button.btn.btn-danger-soft', {type: 'button', onclick: () => {
            const note = h('textarea.input', {rows: 3, maxlength: 500, placeholder: t('reject_note_ph')});
            modal(t('reject_title'), [h('p', t('reject_text')), field(t('reject_note'), note)], [{label: t('cancel')}, {label: t('reject'), danger: true, onclick: () => act('nl.reject', {note: note.value}, t('rejected_ok'))}]);
        }}, svg('x'), t('reject')));
    }
    if (n.status === 'pending' && (mine || pub) && atLeast(g.role, 'moderator')) buttons.push(h('button.btn.btn-soft', {type: 'button', onclick: () => act('nl.withdraw', {}, t('withdrawn_ok'))}, svg('undo'), t('withdraw')));
    const reschedule = async () => {
        const pick = await slotDialog(g, {title: t('reschedule_title'), action: t('save'), initial: partsIn(n.scheduledAt || n.proposedAt, g.timezone)});
        if (pick) act('nl.reschedule', {date: pick.date, time: pick.time}, t('rescheduled_ok'));
    };
    if (n.status === 'pending' && (mine || pub) && atLeast(g.role, 'moderator')) buttons.push(h('button.btn.btn-soft', {type: 'button', onclick: reschedule}, svg('calendar'), t('change_time')));
    if (n.status === 'scheduled' && pub) {
        buttons.push(h('button.btn.btn-soft', {type: 'button', onclick: reschedule}, svg('calendar'), t('reschedule')));
        buttons.push(h('button.btn.btn-soft', {type: 'button', onclick: () => act('nl.withdraw', {}, t('withdrawn_ok'))}, svg('undo'), t('unschedule')));
        buttons.push(h('button.btn.btn-danger-soft', {type: 'button', onclick: async () => {
            if (await confirmDialog(t('cancel_nl_title'), t('cancel_nl_text'), t('cancel_nl'), true)) act('nl.cancel', {}, t('cancelled_ok'));
        }}, svg('ban'), t('cancel_nl')));
    }
    if (atLeast(g.role, 'moderator') && g.status === 'active' && !['sent', 'sending'].includes(n.status)) buttons.push(h('button.btn.btn-ghost', {type: 'button', onclick: async () => {
        try {
            const d = (await api('nl.duplicate', {id: n.id})).newsletter;
            go('#/g/' + g.id + '/n/' + d.id);
        } catch (e) {
            failToast(e);
        }
    }}, svg('dup'), t('duplicate')));
    if (n.status === 'cancelled' && (mine || pub)) buttons.push(h('button.btn.btn-ghost', {type: 'button', onclick: async () => {
        if (await confirmDialog(t('delete_nl_title'), t('delete_nl_text'), t('delete'), true)) {
            await api('nl.delete', {id: n.id}).catch(failToast);
            go('#/g/' + g.id);
        }
    }}, svg('trash'), t('delete')));
    const facts = [
        [t('status'), statusChip(n.status)],
        [t('author'), n.author || '—'],
        [t('audience'), n.lang ? LANG_NAMES[n.lang] || n.lang : t('all_langs')],
        n.proposedAt ? [t('proposed_for'), fmtDate(n.proposedAt, g.timezone)] : null,
        n.scheduledAt ? [t('scheduled_for'), fmtDate(n.scheduledAt, g.timezone)] : null,
        n.reviewer ? [t('reviewed_by'), n.reviewer + (n.reviewedAt ? ' · ' + fmtDate(n.reviewedAt, g.timezone) : '')] : null,
        n.reviewNote ? [t('review_note'), h('em', n.reviewNote)] : null,
        n.sentAt ? [t('sent_on'), fmtDate(n.sentAt, g.timezone)] : null,
        ['sending', 'sent', 'failed'].includes(n.status) ? [t('delivered'), t('delivered_n', {sent: n.sent, total: n.total, failed: n.failed})] : null,
    ].filter(Boolean);
    shell(
        h('a.crumb', {href: '#/g/' + g.id}, svg('back', 'ico ico-sm'), g.name),
        h('div.page-head', h('div', h('h1', n.subject || t('untitled')), n.preheader ? h('p.muted', n.preheader) : null)),
        h('div.nl-detail',
            h('div.nl-preview', frame),
            h('aside.nl-side',
                n.status === 'pending' && pub ? h('p.banner.banner-hl', svg('review'), t('review_banner')) : null,
                n.status === 'pending' && !pub ? h('p.banner', svg('clock'), t('waiting_banner')) : null,
                n.status === 'sending' ? h('p.banner', svg('clock'), t('sending_banner', {rate: g.ratePerHour})) : null,
                n.status === 'sent' ? h('p.banner', svg('check'), t('sent_banner')) : null,
                h('dl.facts', facts.map(([k, v]) => [h('dt', k), h('dd', v)])),
                h('div.stack', buttons),
                ['sending', 'sent'].includes(n.status) && n.total ? h('div.progress', h('div', {style: {width: Math.round(100 * (n.sent + n.failed) / n.total) + '%'}})) : null)));
}

// ---------------------------------------------------------------- subscribers

async function tabSubscribers(g, body) {
    const state = {status: 'active', lang: '', q: '', page: 0};
    const table = h('div.table-wrap');
    const pager = h('div.pager');
    const counts = g.subscribers;
    const statusSel = select([['active', t('st_sub_active')], ['pending', t('st_sub_pending')], ['unsubscribed', t('st_sub_unsubscribed')], ['', t('all')]], 'active', {onchange: (ev) => { state.status = ev.target.value; state.page = 0; load(); }});
    const langSel = g.langs.length > 1 ? select([['', t('all_langs')], ...g.langs.map((l) => [l, LANG_NAMES[l] || l])], '', {onchange: (ev) => { state.lang = ev.target.value; state.page = 0; load(); }}) : null;
    let timer;
    const search = input({type: 'search', placeholder: t('search_email'), oninput: (ev) => { clearTimeout(timer); timer = setTimeout(() => { state.q = ev.target.value; state.page = 0; load(); }, 300); }});
    const admin = atLeast(g.role, 'admin');
    async function load() {
        put(table, loading());
        try {
            const r = await api('sub.list', null, new URLSearchParams({group: g.id, ...state}).toString());
            if (!r.subscribers.length) return put(table, h('div.empty.small', svg('users', 'ico ico-lg'), h('p.muted', t('no_subscribers'))), put(pager));
            put(table, h('table.table',
                h('thead', h('tr', [t('email'), t('lang'), t('browser'), t('source'), t('since'), t('status'), admin ? '' : null].filter((x) => x !== null).map((c) => h('th', c)))),
                h('tbody', r.subscribers.map((s) => h('tr',
                    h('td', h('strong', s.email), s.reason && s.status === 'unsubscribed' ? h('div.muted.small', t('reason') + ': ' + reasonText(s.reason)) : null),
                    h('td', LANG_NAMES[s.lang] || s.lang, s.browserLang && s.browserLang !== s.lang ? h('div.muted.small', t('browser_lang') + ': ' + s.browserLang) : null),
                    h('td', s.browser || '—'), h('td', t('src_' + s.source)),
                    h('td', fmtDate(s.confirmedAt || s.createdAt, g.timezone, false)),
                    h('td', h('span.status.st-sub-' + s.status, t('st_sub_' + s.status)), s.unsubscribedAt ? h('div.muted.small', fmtDate(s.unsubscribedAt, g.timezone, false)) : null),
                    admin ? h('td', iconBtn('trash', t('erase'), async () => {
                        if (!(await confirmDialog(t('erase_title'), t('erase_text', {email: s.email}), t('erase'), true))) return;
                        await api('sub.remove', {group: g.id, id: s.id}).catch(failToast);
                        load();
                    })) : null)))));
            const pages = Math.ceil(r.total / r.pageSize);
            put(pager, h('span.muted.small', t('n_results', {n: r.total})), pages > 1 ? [
                h('button.btn.btn-soft.btn-sm', {type: 'button', disabled: state.page === 0, onclick: () => { state.page--; load(); }}, svg('back')),
                h('span.small', `${state.page + 1} / ${pages}`),
                h('button.btn.btn-soft.btn-sm', {type: 'button', disabled: state.page >= pages - 1, onclick: () => { state.page++; load(); }}, svg('chev'))] : null);
        } catch (e) {
            put(table, h('p.form-error', errText(e)));
        }
    }
    put(body,
        h('div.stat-row', stat(t('stat_active'), counts.active, 'users'), stat(t('stat_pending'), counts.pending, 'clock'), stat(t('stat_unsub'), counts.unsubscribed, 'ban')),
        Object.keys(counts.byLang).length > 1 ? h('div.lang-bars', Object.entries(counts.byLang).sort((a, b) => b[1] - a[1]).map(([l, n]) => h('div.lang-bar', h('span', LANG_NAMES[l] || l), h('div', h('i', {style: {width: Math.max(3, Math.round(100 * n / counts.active)) + '%'}})), h('strong', String(n))))) : null,
        h('div.section-head', h('h2', t('tab_subscribers')), admin ? h('div.row-btns', g.status === 'active' ? h('button.btn.btn-soft', {type: 'button', onclick: () => importDialog(g)}, svg('upload'), t('import_csv')) : null, h('a.btn.btn-soft', {href: 'api/?r=sub.export&group=' + g.id, download: ''}, svg('download'), t('export_csv'))) : null),
        h('div.filters', statusSel, langSel, search),
        table, pager,
        h('p.muted.small', t('subscribers_note')));
    load();
}

/** CSV text -> rows of cells. Comma, semicolon or tab (the most frequent in the first line), quotes as in RFC 4180. */
function parseCsv(text) {
    text = text.replace(/^﻿/, '');
    const first = text.split(/\r?\n/, 1)[0] || '';
    const delim = [',', ';', '\t'].map((d) => [d, first.split(d).length]).sort((a, b) => b[1] - a[1])[0][0];
    const rows = [];
    let row = [], cell = '', quoted = false;
    for (let i = 0; i < text.length; i++) {
        const c = text[i];
        if (quoted) {
            if (c === '"' && text[i + 1] === '"') { cell += '"'; i++; } else if (c === '"') quoted = false; else cell += c;
        } else if (c === '"') quoted = true;
        else if (c === delim) { row.push(cell); cell = ''; }
        else if (c === '\n' || c === '\r') {
            if (c === '\r' && text[i + 1] === '\n') i++;
            row.push(cell); rows.push(row); row = []; cell = '';
        } else cell += c;
    }
    if (cell !== '' || row.length) { row.push(cell); rows.push(row); }
    return rows.map((r) => r.map((x) => x.trim())).filter((r) => r.some((x) => x !== ''));
}

/** Rows of a CSV -> [{email, lang}]: uses the "email" and "lang/language/lingua" columns of a header, else any cell with an @. */
function csvSubscribers(rows) {
    if (!rows.length) return [];
    const head = rows[0].map((x) => x.toLowerCase());
    const ei = head.findIndex((x) => /^e-?mail/.test(x) || x === 'indirizzo email');
    const li = head.findIndex((x) => ['lang', 'language', 'lingua', 'locale'].includes(x));
    const data = ei >= 0 ? rows.slice(1) : rows;
    return data.map((r) => ({email: ei >= 0 ? r[ei] || '' : r.find((x) => x.includes('@')) || '', lang: li >= 0 ? r[li] || '' : ''})).filter((x) => x.email !== '');
}

/** Import subscribers from a CSV file: they skip the confirmation email, so the admin declares they agreed. */
function importDialog(g) {
    let rows = [];
    const file = h('input', {type: 'file', accept: '.csv,text/csv,text/plain', hidden: true});
    const pickBtn = h('button.btn.btn-soft', {type: 'button', onclick: () => file.click()}, svg('upload'), t('import_choose'));
    const info = h('p.muted.small', t('import_format'));
    const consent = h('input', {type: 'checkbox'});
    const lang = g.langs.length > 1 ? select(g.langs.map((l) => [l, LANG_NAMES[l] || l]), g.langs[0]) : null;
    const result = h('div.test-result', {hidden: true});
    file.addEventListener('change', async () => {
        const f = file.files[0];
        if (!f) return;
        if (f.size > 2_000_000) return toast(t('err_import_too_big'), 'err');
        rows = csvSubscribers(parseCsv(await f.text()));
        put(info, h('strong', f.name), ' · ', t('import_found', {n: rows.length}),
            rows.length ? h('span', ' · ', rows.slice(0, 3).map((r) => r.email).join(', '), rows.length > 3 ? '…' : '') : null);
    });
    modal(t('import_title'), [
        h('p', t('import_text')),
        h('div.row-btns', pickBtn, file), info,
        lang ? field(t('import_lang'), lang, t('import_lang_hint')) : null,
        h('label.check', consent, h('span', h('strong', t('import_consent')), h('br'), h('span.muted.small', t('import_consent_hint')))),
        result,
    ], [
        {label: t('close'), onclick: () => groupPage(g.id, 'subscribers')},
        {label: t('import_go'), primary: true, icon: 'upload', onclick: async () => {
            result.hidden = false;
            if (!rows.length) {
                result.className = 'test-result err';
                put(result, svg('x'), h('span', t('err_import_empty')));
                return false;
            }
            if (!consent.checked) {
                result.className = 'test-result err';
                put(result, svg('x'), h('span', t('err_consent_required')));
                return false;
            }
            try {
                const r = await api('sub.import', {group: g.id, rows, consent: true, lang: lang ? lang.value : null});
                const x = r.report;
                result.className = 'test-result ok';
                put(result, svg('check'), h('span', h('strong', t('import_done', {n: x.added + x.confirmed})),
                    h('br'), t('import_details', {added: x.added, confirmed: x.confirmed, already: x.already, unsub: x.unsubscribed, invalid: x.invalid})));
                rows = [];
            } catch (e) {
                result.className = 'test-result err';
                put(result, svg('x'), h('span', errText(e)));
            }
            return false;
        }},
    ]);
}

function reasonText(r) {
    const [code, ...rest] = r.split(': ');
    const known = ['too_many', 'not_relevant', 'never', 'other', 'one-click'];
    return (known.includes(code) ? t('reason_' + code.replace('-', '_')) : code) + (rest.length ? ': ' + rest.join(': ') : '');
}

// ---------------------------------------------------------------- templates

async function tabTemplates(g, body) {
    const r = await api('tpl.list', null, 'group=' + g.id);
    const favs = r.favorites || [];
    const builtinGrid = h('div.tpl-grid');
    const pub = atLeast(g.role, 'publisher');
    const writer = atLeast(g.role, 'moderator') && g.status === 'active';
    const useTpl = async (id) => {
        try {
            const n = (await api('nl.create', {group: g.id, template: String(id)})).newsletter;
            go('#/g/' + g.id + '/n/' + n.id);
        } catch (e) {
            failToast(e);
        }
    };
    /** A group template made from a built-in one (or another group template), opened in the editor. */
    const newTemplate = async (name, doc) => {
        try {
            const res = await api('tpl.save', {group: g.id, name, doc});
            go('#/g/' + g.id + '/t/' + res.id);
        } catch (e) {
            failToast(e);
        }
    };
    const pickBase = () => {
        let chosen = r.builtin[0].id;
        const all = [...r.builtin.map((b) => ({id: b.id, name: b.name, desc: b.description, doc: b.doc})), ...r.own.map((o) => ({id: String(o.id), name: o.name, desc: o.author ? t('by', {name: o.author}) : '', doc: o.doc}))];
        const name = input({maxlength: 80, placeholder: t('template_name'), autofocus: true});
        const grid = h('div.tpl-grid', all.map((x) => h('button.tpl-card' + (x.id === chosen ? '.on' : ''), {type: 'button', 'data-id': x.id, onclick: () => {
            chosen = x.id;
            grid.querySelectorAll('.tpl-card').forEach((c) => c.classList.toggle('on', c.dataset.id === x.id));
        }}, tplThumb(x.doc), h('strong', x.name), x.desc ? h('span.muted.small', x.desc) : null)));
        modal(t('new_template'), [field(t('template_name'), name), h('h4', t('start_from')), grid], [
            {label: t('cancel')},
            {label: t('create'), primary: true, icon: 'plus', onclick: () => {
                const base = all.find((x) => x.id === chosen);
                newTemplate(name.value.trim() || base.name, base.doc);
            }},
        ], {wide: true});
    };
    put(body,
        h('div.section-head', h('h2', t('tab_templates')), writer ? h('button.btn.btn-primary', {type: 'button', onclick: pickBase}, svg('plus'), t('new_template')) : null),
        h('p.muted', t('templates_text')),
        h('h3.sub-title', t('own_templates')),
        r.own.length ? h('div.tpl-grid', r.own.map((o) => h('div.tpl-card.static', tplThumb(o.doc), h('strong', o.name),
            h('span.muted.small', (o.author ? t('by', {name: o.author}) + ' · ' : '') + fmtDate(o.updatedAt, g.timezone, false)),
            writer ? h('div.row-btns',
                h('button.btn.btn-soft.btn-sm', {type: 'button', onclick: () => useTpl(o.id)}, svg('plus'), t('use')),
                h('a.btn.btn-ghost.btn-sm', {href: '#/g/' + g.id + '/t/' + o.id}, svg('layout'), t('edit')),
                (pub || o.author === S.user.name) ? iconBtn('trash', t('delete'), async () => {
                    if (!(await confirmDialog(t('delete_tpl_title'), t('delete_tpl_text', {name: o.name}), t('delete'), true))) return;
                    await api('tpl.delete', {group: g.id, id: o.id}).catch(failToast);
                    groupPage(g.id, 'templates');
                }) : null) : null))) : h('div.empty.small', svg('layout', 'ico ico-lg'), h('p.muted', t('no_templates')),
            writer ? h('button.btn.btn-soft', {type: 'button', onclick: pickBase}, svg('plus'), t('new_template')) : null),
        h('h3.sub-title', t('builtin_templates')),
        builtinGrid);
    drawBuiltin();
    return;
    function drawBuiltin() {
        put(builtinGrid, sortedBuiltin(r.builtin, favs).map((b) => h('div.tpl-card.static', tplThumb(b.doc), h('div.tpl-card-title', h('strong', b.name), favStar(b.id, favs, drawBuiltin)), h('span.muted.small', b.description),
            writer ? h('div.row-btns',
                h('button.btn.btn-soft.btn-sm', {type: 'button', onclick: () => useTpl(b.id)}, svg('plus'), t('use')),
                h('button.btn.btn-ghost.btn-sm', {type: 'button', onclick: () => newTemplate(b.name, b.doc)}, svg('palette'), t('customize'))) : null)));
    }
}

/** A group template in the editor (template mode: no subject, no sending). */
async function templatePage(gid, tid) {
    shell(loading());
    let g, r;
    try {
        [g, r] = await Promise.all([cache.group && cache.group.id === gid ? cache.group : loadGroup(gid), api('tpl.list', null, 'group=' + gid)]);
    } catch (e) {
        return shell(h('p.form-error', errText(e)));
    }
    const tpl = r.own.find((o) => o.id === tid);
    if (!tpl || !atLeast(g.role, 'moderator') || g.status !== 'active') return shell(h('p.form-error', t('err_not_found')), h('a.btn.btn-soft', {href: '#/g/' + gid + '/templates'}, svg('back'), t('back')));
    document.title = tpl.name + ' · OpenNewsletter';
    put(app, h('div.editor-root'));
    editorView(app.firstChild, {group: g, tpl: {id: tpl.id, name: tpl.name, doc: tpl.doc}, role: g.role, onBack: () => go('#/g/' + gid + '/templates')});
}

// ---------------------------------------------------------------- members

async function tabMembers(g, body) {
    const r = await api('member.list', null, 'group=' + g.id);
    const admin = atLeast(g.role, 'admin');
    const owner = g.role === 'owner';
    const assignable = owner ? ['user', 'moderator', 'publisher', 'admin'] : ['user', 'moderator', 'publisher'];
    const rows = r.members.map((m) => {
        const self = m.id === S.user.id;
        const editable = admin && !self && m.role !== 'owner' && (owner || rank(m.role) < rank('admin'));
        const roleCell = editable ? select([...assignable.map((x) => [x, t('role_' + x)]), ...(owner ? [['owner', t('make_owner')]] : [])], m.role, {onchange: async (ev) => {
            const v = ev.target.value;
            if (v === 'owner' && !(await confirmDialog(t('transfer_title'), t('transfer_text', {name: m.name}), t('transfer'), true))) {
                ev.target.value = m.role;
                return;
            }
            try {
                await api('member.role', {group: g.id, user: m.id, role: v});
                toast(t('saved'));
                if (v === 'owner') groupPage(g.id, 'members');
            } catch (e) {
                failToast(e);
                ev.target.value = m.role;
            }
        }}) : h('span.chip.role-' + m.role, t('role_' + m.role));
        return h('tr', h('td', h('div.who', h('span.avatar.sm', initials(m.name)), h('div', h('strong', m.name + (self ? ' (' + t('you') + ')' : '')), h('div.muted.small', m.email)))),
            h('td', roleCell), h('td.muted.small', fmtDate(m.since, g.timezone, false)),
            h('td', editable ? iconBtn('trash', t('remove'), async () => {
                if (!(await confirmDialog(t('remove_member_title'), t('remove_member_text', {name: m.name}), t('remove'), true))) return;
                await api('member.remove', {group: g.id, user: m.id}).catch(failToast);
                groupPage(g.id, 'members');
            }) : null));
    });
    const email = input({type: 'email', placeholder: 'name@example.com'});
    const role = select(assignable.map((x) => [x, t('role_' + x)]), 'moderator');
    put(body,
        h('div.section-head', h('h2', t('tab_members'))),
        admin && g.status === 'active' ? h('form.add-member', {onsubmit: async (ev) => {
            ev.preventDefault();
            try {
                const res = await api('member.add', {group: g.id, email: email.value, role: role.value});
                toast(t(res.joined ? 'member_added' : 'invite_sent'));
                groupPage(g.id, 'members');
            } catch (e) {
                failToast(e);
            }
        }}, field(t('add_member'), email), field(t('role'), role), h('button.btn.btn-primary', {type: 'submit'}, svg('plus'), t('add'))) : null,
        h('div.table-wrap', h('table.table', h('tbody', rows))),
        r.invites.length ? [h('h3.sub-title', t('invites')), h('p.muted.small', t('invites_text')), h('div.table-wrap', h('table.table', h('tbody', r.invites.map((i) => h('tr',
            h('td', h('strong', i.email)), h('td', h('span.chip.role-' + i.role, t('role_' + i.role))), h('td.muted.small', fmtDate(i.since, g.timezone, false)),
            h('td', iconBtn('x', t('cancel_invite'), async () => { await api('invite.cancel', {group: g.id, id: i.id}).catch(failToast); groupPage(g.id, 'members'); })))))))] : null,
        h('h3.sub-title', t('roles_title')),
        h('div.roles-grid', ROLES.map((x) => h('div.role-card' + (x === g.role ? '.on' : ''), h('span.chip.role-' + x, t('role_' + x)), h('p.small', t('role_' + x + '_text'))))),
        g.role !== 'owner' ? h('p', h('button.btn.btn-ghost', {type: 'button', onclick: async () => {
            if (!(await confirmDialog(t('leave_title'), t('leave_text', {group: g.name}), t('leave'), true))) return;
            try {
                await api('group.leave', {group: g.id});
                go('#/');
            } catch (e) {
                failToast(e);
            }
        }}, svg('logout'), t('leave'))) : null);
}

// ---------------------------------------------------------------- share (links and widget)

function tabShare(g, body) {
    const embed = `<div data-opennewsletter="${g.slug}"></div>\n<script src="${g.links.widget}" async></script>`;
    const frame = h('iframe.widget-preview', {src: 'subscribe.php?embed=1&g=' + encodeURIComponent(g.slug), title: t('widget_preview')});
    window.addEventListener('message', function onMsg(ev) {
        if (!document.body.contains(frame)) return window.removeEventListener('message', onMsg);
        if (ev.source === frame.contentWindow && ev.data && ev.data.opennewsletter === 'height') frame.style.height = ev.data.height + 'px';
    });
    put(body,
        h('div.section-head', h('h2', t('tab_share'))),
        !g.sendingReady ? h('p.banner', svg('mail'), t('share_not_ready')) : null,
        h('div.share-grid',
            h('div.card.share-card', h('h3', svg('link'), t('subscribe_link')), h('p.muted.small', t('subscribe_link_text')), copyBox(g.links.subscribe, false, g.links.subscribe),
                g.langs.length > 1 ? h('p.muted.small', t('lang_param'), ' ', h('code', '&lang=' + g.langs[1])) : null),
            h('div.card.share-card', h('h3', svg('ban'), t('unsubscribe_link')), h('p.muted.small', t('unsubscribe_link_text')), copyBox(g.links.unsubscribe, false, g.links.unsubscribe)),
            h('div.card.span2', h('h3', svg('code'), t('widget')), h('p.muted.small', t('widget_text')), copyBox(embed, true),
                h('p.muted.small', t('widget_attrs')),
                h('div.widget-stage', frame))));
}

// ---------------------------------------------------------------- settings (admins)

function tabSettings(g, body) {
    const admin = atLeast(g.role, 'admin');
    const sec = (icon, title, text, ...kids) => h('section.card.settings-sec', h('h3', svg(icon), title), text ? h('p.muted.small', text) : null, kids);
    const saveBtn = (fn, label = t('save')) => {
        const b = h('button.btn.btn-primary', {type: 'button', onclick: async () => {
            b.disabled = true;
            try {
                await fn();
                toast(t('saved'));
            } catch (e) {
                failToast(e);
            } finally {
                b.disabled = false;
            }
        }}, svg('save'), label);
        return b;
    };
    const refresh = (r) => { cache.group = r.group; Object.assign(g, r.group); };

    // general
    const name = input({value: g.name, maxlength: 80});
    const slug = input({value: g.slug, maxlength: 40});
    const desc = input({value: g.description, maxlength: 300});
    const langs = [...g.langs];
    const tz = select(timezones().map((z) => [z, z]), g.timezone);
    const general = sec('settings', t('general'), null, field(t('group_name'), name), field(t('group_slug'), slug, t('group_slug_change')), field(t('group_desc'), desc),
        field(t('sub_langs'), langChips(langs), t('sub_langs_hint')), field(t('timezone'), tz, t('timezone_hint')),
        h('div.row-btns', saveBtn(async () => { refresh(await api('group.update', {group: g.id, name: name.value, slug: slug.value, description: desc.value, langs, timezone: tz.value})); groupPage(g.id, 'settings'); })));

    // sending (SMTP): the password never comes back from the server
    const s = g.smtp || {};
    const host = input({value: s.host || '', placeholder: 'smtp.example.com'});
    const enc = select([['ssl', 'SSL/TLS (465)'], ['tls', 'STARTTLS (587)'], ['none', t('enc_none')]], s.encryption || 'ssl', {onchange: () => { if (!port.value || ['465', '587', '25'].includes(port.value)) port.value = {ssl: 465, tls: 587, none: 25}[enc.value]; }});
    const port = input({type: 'number', value: s.port || 465, min: 1, max: 65535});
    const user = input({value: s.user || '', autocomplete: 'off'});
    const pass = input({type: 'password', autocomplete: 'new-password', placeholder: s.hasPassword ? t('password_kept') : ''});
    const from = input({type: 'email', value: g.fromEmail, placeholder: 'news@example.com'});
    const fromName = input({value: g.fromName || g.name, maxlength: 80});
    const reply = input({type: 'email', value: g.replyTo || '', placeholder: t('optional')});
    const rate = input({type: 'number', min: 10, max: 5000, step: 10, value: g.ratePerHour || 200});
    const sending = sec('mail', t('sending'), t('sending_text'),
        g.sendingReady ? h('p.ok-line', svg('check', 'ico ico-sm'), t('sending_from', {email: g.fromEmail}), g.smtpCheckedAt ? h('span.muted.small', ' · ' + t('tested_on', {when: fmtDate(g.smtpCheckedAt, g.timezone)})) : null) : null,
        h('div.row2', field(t('from_email'), from), field(t('from_name'), fromName)), field(t('reply_to'), reply),
        h('div.row3', field(t('smtp_host'), host), field(t('smtp_enc'), enc), field(t('smtp_port'), port)),
        h('div.row2', field(t('smtp_user'), user), field(t('smtp_password'), pass, t('smtp_password_hint'))),
        field(t('rate_per_hour'), rate, t('rate_hint')),
        h('div.row-btns',
            saveBtn(async () => { refresh(await api('group.smtp', {group: g.id, host: host.value, encryption: enc.value, port: Number(port.value), user: user.value, password: pass.value, fromEmail: from.value, fromName: fromName.value, replyTo: reply.value, ratePerHour: Number(rate.value)})); groupPage(g.id, 'settings'); }),
            g.sendingReady ? h('button.btn.btn-soft', {type: 'button', onclick: () => smtpTestDialog(g, refresh)}, svg('send'), t('smtp_test')) : null,
            g.sendingReady ? h('button.btn.btn-ghost', {type: 'button', onclick: async () => {
                if (!(await confirmDialog(t('smtp_clear_title'), t('smtp_clear_text'), t('smtp_clear'), true))) return;
                refresh(await api('group.smtpClear', {group: g.id}));
                groupPage(g.id, 'settings');
            }}, svg('trash'), t('smtp_clear')) : null),
        h('p.muted.small', svg('shield', 'ico ico-sm'), ' ', t('smtp_privacy')));

    // subscription page and widget
    const p = {...g.page};
    const pv = h('iframe.widget-preview', {title: t('page_preview')});
    // live preview: the unsaved settings go to the page in the URL (it accepts them only from the group's admins)
    let pvTimer;
    const reloadPv = () => {
        clearTimeout(pvTimer);
        pvTimer = setTimeout(() => {
            const data = btoa(unescape(encodeURIComponent(JSON.stringify(p))));
            pv.src = 'subscribe.php?embed=1&g=' + encodeURIComponent(g.slug) + '&lang=' + getLang() + '&preview=' + encodeURIComponent(data);
        }, 350);
    };
    window.addEventListener('message', function onMsg(ev) {
        if (!document.body.contains(pv)) return window.removeEventListener('message', onMsg);
        if (ev.source === pv.contentWindow && ev.data && ev.data.opennewsletter === 'height') pv.style.height = ev.data.height + 'px';
    });
    const bind = (k, el, prop = 'value') => {
        const ev = prop === 'checked' || el.tagName === 'SELECT' ? 'change' : 'input';
        el.addEventListener(ev, () => { p[k] = prop === 'checked' ? el.checked : el.value; reloadPv(); });
        return el;
    };
    const logoBox = h('div.logo-box');
    const drawLogo = () => put(logoBox, p.logo ? h('img', {src: p.logo.startsWith('img:') ? 'api/?r=img&id=' + p.logo.slice(4) : p.logo, alt: ''}) : h('span.muted.small', t('no_logo')),
        logoUpload(g, (src) => { p.logo = src; drawLogo(); reloadPv(); }), p.logo ? h('button.btn.btn-ghost.btn-sm', {type: 'button', onclick: () => { p.logo = ''; drawLogo(); reloadPv(); }}, svg('trash')) : null);
    drawLogo();
    const accent = bind('accent', h('input.color', {type: 'color', value: p.accent}));
    const pageSec = sec('palette', t('page_custom'), t('page_custom_text'),
        h('div.split',
            h('div.stack',
                field(t('page_title'), bind('title', input({value: p.title, maxlength: 120, placeholder: t('sub_title_default', {group: g.name})}))),
                field(t('page_intro'), bind('intro', h('textarea.input', {rows: 3, maxlength: 600, placeholder: g.description || t('sub_intro_default')}, p.intro))),
                h('div.row2', field(t('page_button'), bind('button', input({value: p.button, maxlength: 40, placeholder: t('subscribe')}))), field(t('accent'), h('div.color-row', accent))),
                field(t('page_success'), bind('success', input({value: p.success, maxlength: 400, placeholder: t('page_success_ph')}))),
                field(t('page_style'), stylePicker(p.style, (v) => { p.style = v; reloadPv(); })),
                field(t('logo'), logoBox),
                field(t('page_font'), fontPicker(p.font || 'inter', (v) => { p.font = v; reloadPv(); })),
                h('div.row2', field(t('page_theme'), bind('theme', select([['light', t('theme_light')], ['dark', t('theme_dark')], ['auto', t('theme_auto')]], p.theme))),
                    field(t('page_shape'), bind('shape', select([['round', t('shape_round')], ['soft', t('shape_soft')], ['square', t('shape_square')]], p.shape)))),
                h('label.check', bind('showLang', h('input', {type: 'checkbox', checked: p.showLang}), 'checked'), h('span', t('show_lang'))),
                h('label.check', bind('showCount', h('input', {type: 'checkbox', checked: p.showCount}), 'checked'), h('span', t('show_count'))),
                h('div.row-btns', saveBtn(async () => { refresh(await api('group.page', {group: g.id, page: p})); reloadPv(); }))),
            h('div.preview-col', h('span.muted.small', t('page_preview')), h('div.widget-stage', pv))));
    reloadPv();

    // terms
    const terms = h('textarea.input', {rows: 8, maxlength: 20000, placeholder: t('terms_ph')}, g.terms);
    const termsSec = sec('shield', t('group_terms'), t('group_terms_text'), terms, h('div.row-btns', saveBtn(async () => refresh(await api('group.terms', {group: g.id, terms: terms.value})))));

    // danger zone
    const danger = g.role === 'owner' ? sec('trash', t('danger'), t('danger_text'), h('button.btn.btn-danger', {type: 'button', onclick: () => {
        const c = input({placeholder: g.name});
        modal(t('delete_group'), [h('p', t('delete_group_text', {name: g.name})), field(t('type_name'), c)], [{label: t('cancel')}, {label: t('delete_group'), danger: true, onclick: async () => {
            try {
                await api('group.delete', {group: g.id, confirm: c.value});
                go('#/');
                toast(t('group_deleted'));
            } catch (e) {
                failToast(e);
                return false;
            }
        }}]);
    }}, svg('trash'), t('delete_group'))) : null;

    put(body, h('div.section-head', h('h2', t('tab_settings'))), admin ? [sending, general, pageSec, termsSec, danger] : null);
}

/**
 * Typefaces of the subscription page and widget (PAGE_FONTS in lib/groups.php): key, name, CSS family used to draw
 * the card (the "ON …" families are declared in app.css and downloaded only when a card shows them).
 */
const PAGE_FONTS = [
    ['inter', 'Inter + Poppins', 'Poppins, Inter, sans-serif'], ['poppins', 'Poppins', 'Poppins, sans-serif'],
    ['montserrat', 'Montserrat', '"ON Montserrat", sans-serif'], ['opensans', 'Open Sans', '"ON Open Sans", sans-serif'],
    ['sourcesans', 'Source Sans 3', '"ON Source Sans", sans-serif'], ['lora', 'Lora', '"ON Lora", Georgia, serif'],
    ['playfair', 'Playfair Display', '"ON Playfair", Georgia, serif'],
    ['system', 'System', '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif'],
    ['georgia', 'Georgia', 'Georgia, "Times New Roman", serif'], ['mono', 'Monospace', 'ui-monospace, "SF Mono", Menlo, Consolas, "Courier New", monospace'],
];

/** Typeface cards: each one is written in its own font, so it is seen before choosing. */
function fontPicker(value, onPick) {
    const box = h('div.font-pick', {role: 'radiogroup'});
    PAGE_FONTS.forEach(([k, name, family]) => box.append(h('button.font-opt' + (k === value ? '.on' : ''), {type: 'button', role: 'radio', 'aria-checked': k === value ? 'true' : 'false', 'data-v': k, title: name, onclick: () => {
        box.querySelectorAll('.font-opt').forEach((b) => { b.classList.toggle('on', b.dataset.v === k); b.setAttribute('aria-checked', b.dataset.v === k ? 'true' : 'false'); });
        onPick(k);
    }}, h('span.font-sample', {style: {fontFamily: family}}, 'Aa'), h('span.font-name', {style: {fontFamily: family}}, name))));
    return box;
}

/** The five looks of the subscription page and widget, as small drawings. */
function stylePicker(value, onPick) {
    const box = h('div.style-pick', {role: 'radiogroup'});
    ['card', 'minimal', 'outline', 'bold', 'compact'].forEach((st) => box.append(h('button.style-opt' + (st === value ? '.on' : ''), {type: 'button', role: 'radio', 'aria-checked': st === value ? 'true' : 'false', 'data-v': st, onclick: () => {
        box.querySelectorAll('.style-opt').forEach((b) => { b.classList.toggle('on', b.dataset.v === st); b.setAttribute('aria-checked', b.dataset.v === st ? 'true' : 'false'); });
        onPick(st);
    }}, h('span.style-art.art-' + st, h('i'), h('i'), h('b')), h('span', t('style_' + st)))));
    return box;
}

/** "Verify it works": a real email from the group's outgoing address to an address of the admin's choice. */
function smtpTestDialog(g, refresh) {
    const to = input({type: 'email', value: S.user.email, autocomplete: 'email', autofocus: true});
    const result = h('div.test-result', {hidden: true});
    modal(t('smtp_test_title'), [h('p.muted', t('smtp_test_text', {from: g.fromEmail})), field(t('smtp_test_to'), to), result], [
        {label: t('close')},
        {label: t('smtp_test_send'), primary: true, icon: 'send', onclick: async () => {
            result.hidden = false;
            put(result, h('span.spin'), t('smtp_test_sending'));
            result.className = 'test-result';
            try {
                const r = await api('group.smtpTest', {group: g.id, to: to.value.trim()});
                refresh(r);
                result.className = 'test-result ok';
                put(result, svg('check'), h('span', t('smtp_test_ok', {email: r.to}), h('br'), h('span.small', t('smtp_test_ok_hint'))));
            } catch (e) {
                result.className = 'test-result err';
                put(result, svg('x'), h('span', h('strong', t('smtp_test_failed')), h('br'), errText(e), e.detail ? h('code.small', ' ' + e.detail) : null));
            }
            return false; // stays open: the result is shown here
        }},
    ]);
}

function logoUpload(g, onDone) {
    const file = h('input', {type: 'file', accept: 'image/png,image/jpeg,image/webp,image/gif', hidden: true});
    const btn = h('button.btn.btn-soft.btn-sm', {type: 'button', onclick: () => file.click()}, svg('upload'), t('upload'));
    file.addEventListener('change', async () => {
        const f = file.files[0];
        if (!f) return;
        btn.disabled = true;
        try {
            const data = await new Promise((res, rej) => {
                const img = new Image();
                img.onload = () => {
                    const scale = Math.min(1, 600 / img.width);
                    const c = document.createElement('canvas');
                    c.width = Math.round(img.width * scale);
                    c.height = Math.round(img.height * scale);
                    c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
                    res(c.toDataURL('image/png'));
                };
                img.onerror = rej;
                img.src = URL.createObjectURL(f);
            });
            onDone((await api('img.upload', {group: g.id, data})).src);
        } catch (e) {
            failToast(e.code ? e : {code: 'image_invalid'});
        } finally {
            btn.disabled = false;
        }
    });
    return h('span', btn, file);
}

// ---------------------------------------------------------------- account

function accountPage() {
    const u = S.user;
    const name = input({value: u.name, maxlength: 80});
    const cur = input({type: 'password', autocomplete: 'current-password'});
    const pw = input({type: 'password', autocomplete: 'new-password', minlength: 8});
    shell(
        h('a.crumb', {href: '#/'}, svg('back', 'ico ico-sm'), t('my_groups')),
        h('div.page-head', h('h1', t('account'))),
        h('div.account-grid',
            h('section.card', h('h3', svg('user'), t('profile')),
                field(t('email'), input({value: u.email, disabled: true}), t('email_fixed')),
                field(t('your_name'), name),
                field(t('app_lang'), langSwitch()),
                h('div.row-btns', h('button.btn.btn-primary', {type: 'button', onclick: async () => {
                    try {
                        setUser((await api('auth.profile', {name: name.value, lang: getLang()})).user);
                        toast(t('saved'));
                    } catch (e) {
                        failToast(e);
                    }
                }}, svg('save'), t('save'))),
                h('p.muted.small', u.googleLinked ? t('google_linked') : '')),
            h('section.card', h('h3', svg('shield'), t(u.hasPassword ? 'change_password' : 'set_password')),
                u.hasPassword ? field(t('current_password'), cur) : h('p.muted.small', t('set_password_text')),
                field(t('new_password'), pw, t('password_hint')),
                h('div.row-btns', h('button.btn.btn-primary', {type: 'button', onclick: async () => {
                    try {
                        setUser((await api('auth.password', {current: cur.value, password: pw.value})).user);
                        cur.value = pw.value = '';
                        toast(t('saved'));
                    } catch (e) {
                        failToast(e);
                    }
                }}, svg('save'), t('save')))),
            h('section.card.danger-card', h('h3', svg('trash'), t('delete_account')), h('p.muted.small', t('delete_account_text')),
                h('button.btn.btn-danger', {type: 'button', onclick: () => {
                    const p = input({type: 'password'});
                    modal(t('delete_account'), [h('p', t('delete_account_confirm')), u.hasPassword ? field(t('password'), p) : null], [{label: t('cancel')}, {label: t('delete_account'), danger: true, onclick: async () => {
                        try {
                            await api('auth.delete', {password: p.value});
                            S.user = null;
                            go('#/');
                        } catch (e) {
                            failToast(e);
                            return false;
                        }
                    }}]);
                }}, svg('trash'), t('delete_account')))));
}

// ---------------------------------------------------------------- master admin

async function masterPage() {
    shell(loading());
    let r;
    try {
        r = await api('master.overview');
    } catch (e) {
        return shell(h('p.form-error', errText(e)));
    }
    const q = input({type: 'search', placeholder: t('search_groups')});
    const tbody = h('tbody');
    const draw = () => {
        const rows = r.groups.filter((g) => !q.value || (g.name + ' ' + g.slug + ' ' + (g.ownerEmail || '')).toLowerCase().includes(q.value.toLowerCase()));
        if (!rows.length) return put(tbody, emptyRow(8, q.value ? t('no_results') : t('no_groups')));
        put(tbody, rows.map((g) => h('tr', {class: 'click', onclick: () => go('#/master/g/' + g.id)},
        h('td', h('strong', g.name), h('div.muted.small', g.slug)), h('td', g.owner || '—', h('div.muted.small', g.ownerEmail || '')),
        h('td', g.fromEmail ? h('span.mono.small', g.fromEmail) : h('span.muted.small', t('not_configured'))),
        h('td.num', String(g.members)), h('td.num', String(g.subscribers)), h('td.num', String(g.sent)), h('td.num', String(g.upcoming)),
        h('td', h('span.status.st-' + (g.status === 'active' ? 'sent' : 'cancelled'), t('gst_' + g.status))))));
    };
    q.addEventListener('input', draw);
    draw();
    shell(
        h('div.page-head', h('div', h('span.kicker', svg('shield', 'ico ico-sm'), ' ', t('master')), h('h1', t('master_title')), h('p.muted', t('master_text')))),
        h('div.stat-row', stat(t('m_users'), r.totals.users, 'user'), stat(t('m_groups'), r.totals.groups, 'layout'), stat(t('stat_active'), r.totals.subscribers, 'users'), stat(t('m_sent'), r.totals.sent, 'send')),
        h('div.filters', q),
        h('div.table-wrap', h('table.table', h('thead', h('tr', [t('group'), t('owner'), t('from_email'), t('m_members'), t('m_subs'), t('m_nl_sent'), t('m_upcoming'), t('status')].map((c) => h('th', c)))), tbody)));
}

async function masterGroup(id) {
    shell(loading());
    let r;
    try {
        r = await api('master.group', null, 'group=' + id);
    } catch (e) {
        return shell(h('p.form-error', errText(e)));
    }
    const g = r.group;
    const toggle = h('button.btn' + (g.status === 'active' ? '.btn-danger-soft' : '.btn-primary'), {type: 'button', onclick: async () => {
        const to = g.status === 'active' ? 'disabled' : 'active';
        if (to === 'disabled' && !(await confirmDialog(t('suspend_title'), t('suspend_text', {name: g.name}), t('suspend'), true))) return;
        await api('master.status', {group: g.id, status: to}).catch(failToast);
        masterGroup(id);
    }}, svg(g.status === 'active' ? 'ban' : 'check'), t(g.status === 'active' ? 'suspend' : 'reactivate'));
    const facts = [[t('group_slug'), g.slug], [t('status'), t('gst_' + g.status)], [t('from_email'), g.fromEmail ? h('span.mono', g.fromEmail) : t('not_configured')],
        [t('from_name'), g.fromName || '—'], [t('sub_langs'), g.langs.map((l) => LANG_NAMES[l] || l).join(', ')], [t('timezone'), g.timezone],
        [t('created'), fmtDate(g.createdAt, g.timezone, false)], [t('subscribe_link'), h('a', {href: g.links.subscribe, target: '_blank'}, g.links.subscribe)]];
    shell(
        h('a.crumb', {href: '#/master'}, svg('back', 'ico ico-sm'), t('master')),
        h('div.page-head', h('div', h('h1', g.name), h('p.muted', g.description)), toggle),
        h('p.banner', svg('shield'), t('master_smtp_note')),
        h('div.stat-row', stat(t('stat_active'), g.subscribers.active, 'users'), stat(t('stat_pending'), g.subscribers.pending, 'clock'), stat(t('stat_unsub'), g.subscribers.unsubscribed, 'ban'), stat(t('m_members'), r.members.length, 'user')),
        h('div.account-grid',
            h('section.card', h('h3', svg('settings'), t('general')), h('dl.facts', facts.map(([k, v]) => [h('dt', k), h('dd', v)]))),
            h('section.card', h('h3', svg('user'), t('tab_members')), r.members.length ? h('div.table-wrap', h('table.table', h('tbody', r.members.map((m) => h('tr', h('td', h('strong', m.name), h('div.muted.small', m.email)), h('td', h('span.chip.role-' + m.role, t('role_' + m.role)))))))) : h('p.muted.small', t('no_members'))),
            h('section.card', h('h3', svg('ban'), t('unsub_reasons')), r.reasons.length ? h('ul.reasons', r.reasons.map((x) => h('li', h('span', reasonText(x.unsub_reason)), h('strong', String(x.n))))) : h('p.muted.small', t('no_reasons')))),
        h('h2', t('tab_newsletters')),
        r.newsletters.length ? h('div.nl-list', r.newsletters.map((n) => h('button.nl-row', {type: 'button', onclick: async () => {
            try {
                previewDialog((await api('master.preview', null, 'id=' + n.id)).html, n.subject);
            } catch (e) {
                failToast(e);
            }
        }}, h('div.nl-main', h('strong', n.subject || t('untitled')), h('span.muted.small', n.author ? t('by', {name: n.author}) : '')),
            h('div.nl-when', h('span', fmtDate(n.sentAt || n.scheduledAt || n.proposedAt || n.updatedAt, g.timezone))), h('div.nl-num', n.total ? h('strong', n.sent + '/' + n.total) : null), statusChip(n.status)))) : h('div.empty.small', svg('mail', 'ico ico-lg'), h('p.muted', t('no_newsletters'))),
        h('h2', t('tab_subscribers')),
        r.subscribers.length ? h('div.table-wrap', h('table.table', h('thead', h('tr', [t('email'), t('lang'), t('browser'), t('source'), t('since'), t('status')].map((c) => h('th', c)))),
            h('tbody', r.subscribers.map((s) => h('tr', h('td', s.email, s.reason && s.status === 'unsubscribed' ? h('div.muted.small', reasonText(s.reason)) : null), h('td', LANG_NAMES[s.lang] || s.lang), h('td', s.browser || '—'),
                h('td', t('src_' + s.source)), h('td', fmtDate(s.createdAt, g.timezone, false)), h('td', h('span.status.st-sub-' + s.status, t('st_sub_' + s.status))))))))
            : h('div.empty.small', svg('users', 'ico ico-lg'), h('p.muted', t('no_subscribers'))));
}

/** A table row that says the list is empty (instead of a bare header that looks like it is still loading). */
function emptyRow(cols, text) {
    return h('tr', h('td.empty-cell', {colspan: cols}, text));
}

boot();
