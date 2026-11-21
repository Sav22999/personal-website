/*
 * Forkville — admin panel (admin.html). Players (coins, experience, pantry, recipes, account),
 * prices and rules of the game, and the history of every change. Talks to the admin.* routes.
 * In Italian or English, like the game.
 */
import {nameIn, getLang, setLang} from './i18n.js';

/** The panel speaks Italian or English (same choice as the game; switch in the header). */
const LANG = getLang() === 'it' ? 'it' : 'en';
const EN = {
    "Accesso riservato agli amministratori.": "Administrators only.",
    "Accedi prima al gioco.": "Sign in to the game first.",
    "Sessione scaduta: ricarica la pagina.": "Session expired: reload the page.",
    "Non trovato.": "Not found.",
    "Non puoi farlo sul tuo account.": "You cannot do that to your own account.",
    "Valore non valido.": "Invalid value.",
    "Questo giocatore non ha ancora aperto il ristorante.": "This player has not opened the restaurant yet.",
    "Il mercato deve vendere almeno un ingrediente.": "The market must sell at least one ingredient.",
    "Il nome è troppo corto.": "The name is too short.",
    "Nessuna connessione.": "No connection.",
    "Errore del server.": "Server error.",
    "Errore: ": "Error: ",
    "Accedi al gioco con il tuo account amministratore, poi torna qui.": "Sign in to the game with your administrator account, then come back here.",
    "Questo account (": "This account (",
    ") non è amministratore. Aggiungi la sua email a $admin_emails in credentials.php, oppure crea un amministratore con install-db.php.": ") is not an administrator. Add its email to $admin_emails in credentials.php, or create an administrator with install-db.php.",
    "Apri il gioco": "Open the game",
    "Panoramica": "Overview",
    "Giocatori": "Players",
    "Prezzi e regole": "Prices & rules",
    "Registro": "Log",
    "Caricamento…": "Loading…",
    "Nuovi (7 giorni)": "New (7 days)",
    "Attivi (24 ore)": "Active (24 hours)",
    "Ristoranti": "Restaurants",
    "Monete in gioco": "Coins in play",
    "Clienti serviti": "Customers served",
    "Disattivati": "Disabled",
    "Classifica della settimana": "This week’s ranking",
    " monete": " coins",
    "Ancora nessuno.": "Nobody yet.",
    "Ultime modifiche": "Latest changes",
    "Nessuna modifica.": "No changes.",
    "Quando": "When",
    "Giocatore": "Player",
    "Azione": "Action",
    "Dettagli": "Details",
    "Cerca username, nome o email": "Search username, name or email",
    "Livello": "Level",
    "Monete": "Coins",
    "Ultimo accesso": "Last sign-in",
    "Scegli un giocatore dalla lista.": "Pick a player from the list.",
    "da confermare": "unconfirmed",
    "Fatto": "Done",
    "Motivo (facoltativo)": "Reason (optional)",
    "Ricetta aggiornata": "Recipe updated",
    "livello ": "level ",
    "non nota": "unknown",
    " · nel menù": " · on the menu",
    "creato ": "created ",
    "Chiudi": "Close",
    "Email confermata": "Email confirmed",
    "Conferma email": "Confirm email",
    "Account disattivato": "Account disabled",
    "Disattiva": "Disable",
    "Account riattivato": "Account enabled again",
    "Riattiva": "Enable",
    "Ruolo aggiornato": "Role updated",
    "Togli admin": "Remove admin",
    "Rendi admin": "Make admin",
    "Account eliminato": "Account deleted",
    "Elimina": "Delete",
    "Ristorante": "Restaurant",
    "Esperienza": "Experience",
    "Popolarità": "Popularity",
    "Dimensione": "Size",
    "Arredi": "Furniture",
    "Serviti": "Served",
    "Voto clienti": "Customer rating",
    "Voto amici": "Friends’ rating",
    "Amici": "Friends",
    "Ultima visita": "Last visit",
    "Nome del ristorante": "Restaurant name",
    "Nome salvato": "Name saved",
    "Salva": "Save",
    "Un numero positivo aggiunge, uno negativo toglie (il saldo non scende sotto zero).": "A positive number adds, a negative one takes away (the balance never goes below zero).",
    "Monete aggiornate": "Coins updated",
    "Applica": "Apply",
    "Esperienza e livello": "Experience and level",
    "Esperienza aggiornata": "Experience updated",
    "Imposta": "Set",
    "Il livello segue l’esperienza: livello 2 = 100, 3 = 300, 4 = 600, 5 = 1000…": "The level follows experience: level 2 = 100, 3 = 300, 4 = 600, 5 = 1000…",
    "Dispensa": "Pantry",
    "Dispensa salvata": "Pantry saved",
    "Salva dispensa": "Save pantry",
    "Ricette": "Recipes",
    "Staff e orto": "Staff and garden",
    "pronto ": "ready ",
    "Orto vuoto.": "Empty garden.",
    "Riportare il ristorante agli arredi iniziali? Il valore degli arredi tolti viene rimborsato in monete.": "Reset the restaurant to the starter furniture? The value of the removed furniture is refunded in coins.",
    "Ristorante ripristinato": "Restaurant reset",
    "Ripristina arredi iniziali": "Reset to starter furniture",
    "Il ristorante non è ancora stato aperto (si crea al primo accesso al gioco).": "The restaurant has not been opened yet (it is created on the first visit to the game).",
    "Storico modifiche": "Change history",
    "Piatti": "Dishes",
    "Orto": "Garden",
    "Mercato": "Market",
    "Staff e ampliamenti": "Staff & enlargements",
    "Regole": "Rules",
    "Tavoli": "Tables",
    "Sedie": "Chairs",
    "Cucina": "Kitchen",
    "Servizio": "Service",
    "Relax": "Lounge",
    "Decori": "Decor",
    "Tappeti": "Rugs",
    "Pavimenti": "Floors",
    "Pareti": "Walls",
    "Monete iniziali di un nuovo ristorante": "Starting coins of a new restaurant",
    "Rimborso alla vendita di un arredo (0–1, 0,5 = metà prezzo)": "Refund when selling furniture (0–1, 0.5 = half price)",
    "Ore massime di guadagno mentre il giocatore è via": "Maximum hours of earnings while the player is away",
    "Velocità del guadagno da via (0–1 rispetto al gioco live)": "Speed of earnings while away (0–1 of live play)",
    "Monete al ristorante visitato (una volta al giorno per amico)": "Coins to the visited restaurant (once a day per friend)",
    "Monete base a chi visita (+5 per livello del ristorante visitato)": "Base coins to the visitor (+5 per level of the visited restaurant)",
    "Monete regalate a ogni livello (× il numero del livello)": "Coins given at each level (× the level number)",
    "Pezzi acquistabili al giorno per ingrediente": "Pieces per ingredient per day at the market",
    "Ingredienti in vendita ogni giorno": "Ingredients on sale each day",
    "Esperienza per cliente servito": "Experience per customer served",
    "Esperienza per raccolto": "Experience per harvest",
    "Costo degli ampliamenti (monete per casella, cresce con la sala)": "Enlargement cost (coins per tile, grows with the room)",
    "Nessuna modifica": "No changes",
    "Annulla": "Cancel",
    "Prezzi e regole salvati: valgono subito per tutti i giocatori": "Prices and rules saved: they apply to every player at once",
    "Predefinito: ": "Default: ",
    "Torna ai valori predefiniti": "Back to the defaults",
    "Oggetto": "Item",
    "In vendita": "On sale",
    "Prezzo": "Price",
    "Velocità": "Speed",
    "Prezzo base al livello 1: ogni livello aggiunge il 25%. Tempi in secondi.": "Base price at level 1: each level adds 25%. Times in seconds.",
    "Piatto": "Dish",
    "Ingredienti": "Ingredients",
    "Imparabile": "Learnable",
    "Prezzo base": "Base price",
    "Cottura": "Cooking",
    "Pasto": "Eating",
    "Crescita in secondi (3600 = un’ora). Il raccolto dà la quantità indicata di quell’ingrediente.": "Growth in seconds (3600 = one hour). The harvest gives that amount of the ingredient.",
    "Coltura": "Crop",
    "Piantabile": "Plantable",
    "Semi": "Seeds",
    "Crescita (s)": "Growth (s)",
    "Raccolto": "Harvest",
    "Ogni giorno il mercato propone a rotazione alcuni ingredienti fra quelli in vendita (vedi Regole).": "Each day the market offers some of the ingredients on sale, in rotation (see Rules).",
    "Ingrediente": "Ingredient",
    "Assunzioni": "Hiring",
    "Costo = primo + aumento × (assunti di quel ruolo − 1).": "Cost = first + increase × (hired in that role − 1).",
    "Ruolo": "Role",
    "Costo del primo": "First one costs",
    "Aumento per ogni altro": "Increase for each other",
    "Ampliamenti del ristorante": "Restaurant enlargements",
    "Costo": "Cost",
    "Regola": "Rule",
    "Valore": "Value",
    "Predefinito": "Default",
    "I valori evidenziati sono diversi da quelli predefiniti. Un oggetto tolto dalla vendita resta a chi lo possiede già.": "Highlighted values differ from the defaults. An item taken off sale stays with those who already own it.",
    "Registro delle modifiche": "Change log",
    "maturo": "ripe",
    "Esterno": "Outdoors",
    "Bagno": "Restroom",
    "Muri e separè": "Walls & screens",
    "disattivato": "disabled",
    "attivo": "active",
    "finitura": "finish",
    "admin": "admin",
    "confermati": "confirmed",
    "giocatori": "players",
    "pagina": "page",
    "monete": "coins",
    "Disattivare @{u}? Verrà disconnesso subito.": "Disable @{u}? They will be signed out at once.",
    "Eliminare per sempre @{u} e il suo ristorante? Scrivi lo username per confermare.": "Delete @{u} and their restaurant forever? Type the username to confirm."
    };
const T = (it) => (LANG === 'en' ? EN[it] ?? it : it);
const LOCALE = LANG === 'it' ? 'it-IT' : 'en-GB';
document.documentElement.lang = LANG;
import {icon} from './icons.js';

const ING_KEYS = ['tomato', 'basil', 'potato', 'onion', 'garlic', 'carrot', 'lemon', 'strawberry', 'pepper', 'mushroom', 'flour', 'cheese', 'egg', 'milk', 'rice', 'fish', 'beef', 'chicken', 'chocolate', 'coffee', 'olive', 'sugar'];
const ingPic = (k) => h('span.pic', {html: icon('ing', k)});

const nm = (k) => nameIn(k, LANG);
const S = {csrf: '', me: null, tab: 'overview', catTab: 'items', catalog: null, draft: null, users: {q: '', page: 0}};
const root = document.getElementById('admin');

function h(tag, attrs, ...kids) {
    const [name, ...cls] = tag.split('.');
    const el = document.createElement(name || 'div');
    if (cls.length) el.className = cls.join(' ');
    if (attrs && (typeof attrs !== 'object' || attrs instanceof Node || Array.isArray(attrs))) {
        kids.unshift(attrs);
        attrs = null;
    }
    for (const [k, v] of Object.entries(attrs || {})) {
        if (v == null || v === false) continue;
        if (k === 'html') el.innerHTML = v;
        else if (k.startsWith('on')) el.addEventListener(k.slice(2), v);
        else el.setAttribute(k, v === true ? '' : v);
    }
    for (const kid of kids.flat()) if (kid != null && kid !== false) el.append(kid instanceof Node ? kid : document.createTextNode(String(kid)));
    return el;
}

const put = (el, ...kids) => el.replaceChildren(...kids.flat().filter(k => k != null && k !== false));

const ERR = {
    forbidden: T('Accesso riservato agli amministratori.'), unauthenticated: T('Accedi prima al gioco.'), csrf: T('Sessione scaduta: ricarica la pagina.'), not_found: T('Non trovato.'),
    not_yourself: T('Non puoi farlo sul tuo account.'), bad_value: T('Valore non valido.'), no_restaurant: T('Questo giocatore non ha ancora aperto il ristorante.'),
    market_empty: T('Il mercato deve vendere almeno un ingrediente.'), name_required: T('Il nome è troppo corto.'), network: T('Nessuna connessione.'), server: T('Errore del server.'),
};

async function api(route, body = {}) {
    let res;
    try {
        res = await fetch('api/index.php?r=' + route, {
            method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/json', 'X-CSRF-Token': S.csrf}, body: JSON.stringify(body),
        });
    } catch (e) {
        throw Object.assign(new Error('network'), {code: 'network'});
    }
    const d = await res.json().catch(() => ({ok: false, error: 'server'}));
    if (!d.ok) throw Object.assign(new Error(d.error), {code: d.error, field: d.field});
    return d;
}

function toast(msg, kind = 'good') {
    const el = h('div.toast.' + kind, msg);
    document.getElementById('toasts').append(el);
    setTimeout(() => el.classList.add('out'), 2600);
    setTimeout(() => el.remove(), 3100);
}

const fail = (e) => toast((ERR[e.code] || T('Errore: ') + e.code) + (e.field ? ` (${e.field})` : ''), 'bad');
const fmt = (n) => Number(n ?? 0).toLocaleString(LOCALE);
const when = (s) => s ? new Date(s.replace(' ', 'T') + 'Z').toLocaleString(LOCALE, {dateStyle: 'short', timeStyle: 'short'}) : '—';

// ---------------------------------------------------------------- boot

async function boot() {
    const me = await fetch('api/index.php?r=auth.me', {credentials: 'same-origin'}).then(r => r.json()).catch(() => null);
    if (!me?.user) return gate(T('Accedi al gioco con il tuo account amministratore, poi torna qui.'), true);
    if (!me.user.isAdmin) return gate(T('Questo account (') + me.user.email + T(') non è amministratore. Aggiungi la sua email a $admin_emails in credentials.php, oppure crea un amministratore con install-db.php.'), true);
    S.csrf = me.csrf;
    S.me = me.user;
    render();
}

function gate(text, link) {
    put(root, h('main.gate', h('div.auth-card.verify', h('h1', 'Forkville · Admin'), h('p', text), link && h('a.btn.btn-primary', {href: './'}, T('Apri il gioco')))));
}

const TABS = [['overview', T('Panoramica')], ['players', T('Giocatori')], ['catalog', T('Prezzi e regole')], ['log', T('Registro')]];

function render() {
    const main = h('main.admin-main');
    put(root,
        h('header.admin-top',
            h('a.admin-brand', {href: './'}, h('img', {src: 'icons/favicon.svg', alt: ''}), h('b', 'Forkville'), h('span', 'admin')),
            h('nav.tabs', TABS.map(([k, label]) => h('button.tab' + (S.tab === k ? '.on' : ''), {onclick: () => ((S.tab = k), render())}, label))),
            h('div.lang-switch', ['it', 'en'].map(l => h('button' + (LANG === l ? '.on' : ''), {onclick: () => (setLang(l), location.reload())}, l.toUpperCase()))),
            h('span.muted', '@' + S.me.username)),
        main);
    ({overview, players, catalog, log})[S.tab](main);
}

// ---------------------------------------------------------------- overview

async function overview(main) {
    put(main, h('p.muted', T('Caricamento…')));
    const d = await api('admin.overview').catch(e => (fail(e), null));
    if (!d) return;
    const stat = (label, value, sub) => h('div.stat', h('small', label), h('b', fmt(value)), sub && h('span.muted', sub));
    put(main,
        h('div.stats',
            stat('Account', d.users, `${fmt(d.verified)} ${T('confermati')}`), stat(T('Nuovi (7 giorni)'), d.newWeek), stat(T('Attivi (24 ore)'), d.active24),
            stat(T('Ristoranti'), d.restaurants), stat(T('Monete in gioco'), d.coins), stat(T('Clienti serviti'), d.served), stat(T('Disattivati'), d.disabled)),
        h('div.cols',
            h('section.panel', h('h2', T('Classifica della settimana')), d.topWeek.length ? h('ol.top', d.topWeek.map(r => h('li', h('b', '@' + r.username), ' · ', fmt(r.coins), T(' monete')))) : h('p.muted', T('Ancora nessuno.'))),
            h('section.panel', h('h2', T('Ultime modifiche')), logTable(d.log))));
}

function logTable(rows) {
    if (!rows.length) return h('p.muted', T('Nessuna modifica.'));
    return h('table.grid', h('thead', h('tr', [T('Quando'), 'Admin', T('Giocatore'), T('Azione'), T('Dettagli')].map(x => h('th', x)))),
        h('tbody', rows.map(r => h('tr', h('td', when(r.created_at)), h('td', r.admin ? '@' + r.admin : '—'), h('td', r.player ? '@' + r.player : '—'), h('td', h('code', r.action)), h('td.wrap', r.detail)))));
}

// ---------------------------------------------------------------- players

async function players(main) {
    const search = h('input', {placeholder: T('Cerca username, nome o email'), value: S.users.q});
    const list = h('div', h('p.muted', T('Caricamento…')));
    const detail = h('aside.detail');
    let timer;
    search.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            S.users = {q: search.value.trim(), page: 0};
            load();
        }, 250);
    });
    put(main, h('div.players', h('section.panel', h('div.search', search), list), detail));
    const load = async () => {
        const d = await api('admin.users', S.users).catch(e => (fail(e), null));
        if (!d) return;
        const pages = Math.ceil(d.total / 50);
        put(list,
            h('p.muted', `${fmt(d.total)} ${T('giocatori')}`),
            h('table.grid.clickable', h('thead', h('tr', ['', 'Username', 'Email', T('Livello'), T('Monete'), T('Ultimo accesso')].map(x => h('th', x)))),
                h('tbody', d.users.map(u => h('tr', {onclick: () => openUser(u.id, detail)},
                    h('td', badge(u)), h('td', h('b', '@' + u.username), h('div.muted.small', u.name)), h('td.small', u.email),
                    h('td', u.level ?? '—'), h('td', u.coins == null ? '—' : fmt(u.coins)), h('td.small', when(u.lastLogin)))))),
            pages > 1 && h('div.row', h('button.btn.btn-small', {disabled: S.users.page === 0, onclick: () => (S.users.page--, load())}, '←'),
                h('span', `${S.users.page + 1} / ${pages}`), h('button.btn.btn-small', {disabled: S.users.page + 1 >= pages, onclick: () => (S.users.page++, load())}, '→')));
    };
    S.reloadList = load; // a change in the detail refreshes the numbers in the list
    load();
    put(detail, h('p.muted', T('Scegli un giocatore dalla lista.')));
}

function badge(u) {
    if (u.status === 'disabled') return h('span.tag.bad', T('disattivato'));
    if (!u.verified) return h('span.tag.warn', T('da confermare'));
    if (u.admin) return h('span.tag.admin', 'admin');
    return h('span.tag.ok', T('attivo'));
}

async function openUser(id, box) {
    put(box, h('p.muted', T('Caricamento…')));
    try {
        drawUser(await api('admin.user', {id}), box);
    } catch (e) {
        fail(e);
    }
}

function drawUser(d, box) {
    const u = d.user, r = d.restaurant;
    const call = async (route, body, msg) => {
        try {
            const res = await api(route, {id: u.id, ...body});
            if (res.user) drawUser(res, box);
            S.reloadList?.();
            toast(msg || T('Fatto'));
        } catch (e) {
            fail(e);
        }
    };
    const coinsIn = h('input', {type: 'number', step: 1, placeholder: 'es. 500 o -200'});
    const reasonIn = h('input', {placeholder: T('Motivo (facoltativo)'), maxlength: 200});
    const xpIn = h('input', {type: 'number', min: 0, value: r?.xp ?? 0});
    const nameIn = h('input', {value: r?.name ?? '', maxlength: 40});

    const pantry = h('div.pantry-grid', ING_KEYS.map(k => h('label.ing', h('span', ingPic(k), nm(k)),
        h('input', {type: 'number', min: 0, max: 100000, 'data-ing': k, value: d.ingredients[k] ?? 0}))));
    const recipes = h('div.recipe-grid', dishKeys().map(k => {
        const sel = h('select', {onchange: () => call('admin.user.recipe', {dish: k, level: +sel.value}, T('Ricetta aggiornata'))},
            [0, 1, 2, 3, 4, 5].map(l => h('option', {value: l, selected: (d.recipes[k] || 0) === l}, l ? T('livello ') + l : T('non nota'))));
        return h('label.ing', h('span', nm(k) + (r?.menu.includes(k) ? T(' · nel menù') : '')), sel);
    }));

    put(box,
        h('div.detail-head', h('div', h('h2', '@' + u.username), h('div.muted', `${u.name} · ${u.email}`), h('div.row.wrap.small', badge(u),
            u.google && h('span.tag', 'Google'), u.password && h('span.tag', 'password'), h('span.muted', T('creato ') + when(u.createdAt)))),
            h('button.icon-btn', {title: T('Chiudi'), html: icon('ui', 'close'), onclick: () => put(box, h('p.muted', T('Scegli un giocatore dalla lista.')))})),

        h('section.block', h('h3', 'Account'), h('div.row.wrap',
            !u.verified && h('button.btn.btn-small', {onclick: () => call('admin.user.update', {verify: true}, T('Email confermata'))}, T('Conferma email')),
            u.status === 'active'
                ? h('button.btn.btn-small', {onclick: () => confirm(T('Disattivare @{u}? Verrà disconnesso subito.').replace('{u}', u.username)) && call('admin.user.update', {status: 'disabled'}, T('Account disattivato'))}, T('Disattiva'))
                : h('button.btn.btn-small', {onclick: () => call('admin.user.update', {status: 'active'}, T('Account riattivato'))}, T('Riattiva')),
            h('button.btn.btn-small', {onclick: () => call('admin.user.update', {admin: !u.admin}, T('Ruolo aggiornato'))}, u.admin ? T('Togli admin') : T('Rendi admin')),
            h('button.btn.btn-small.btn-danger', {
                onclick: async () => {
                    if (prompt(T('Eliminare per sempre @{u} e il suo ristorante? Scrivi lo username per confermare.').replace('{u}', u.username)) !== u.username) return;
                    try {
                        await api('admin.user.delete', {id: u.id});
                        toast(T('Account eliminato'));
                        S.tab = 'players';
                        render();
                    } catch (e) {
                        fail(e);
                    }
                },
            }, T('Elimina')))),

        r ? [
            h('section.block', h('h3', T('Ristorante')),
                h('div.facts', fact(T('Monete'), fmt(r.coins)), fact(T('Livello'), r.level), fact(T('Esperienza'), fmt(r.xp)), fact(T('Popolarità'), `${r.popularity} · ${r.stars}/5`),
                    fact(T('Dimensione'), `${r.size}×${r.size}`), fact(T('Arredi'), r.items), fact(T('Serviti'), fmt(r.served)), fact(T('Voto clienti'), r.rating ?? '—'),
                    fact(T('Voto amici'), r.friendRating ?? '—'), fact(T('Amici'), d.friends), fact(T('Ultima visita'), when(r.lastSeen))),
                h('label.field', h('span', T('Nome del ristorante')), h('div.row', nameIn, h('button.btn.btn-small', {onclick: () => call('admin.user.update', {restaurantName: nameIn.value}, T('Nome salvato'))}, T('Salva'))))),
            h('section.block', h('h3', T('Monete')), h('p.muted.small', T('Un numero positivo aggiunge, uno negativo toglie (il saldo non scende sotto zero).')),
                h('div.row.wrap', coinsIn, reasonIn,
                    h('button.btn.btn-small.btn-primary', {onclick: () => +coinsIn.value && call('admin.user.coins', {delta: +coinsIn.value, reason: reasonIn.value}, T('Monete aggiornate'))}, T('Applica'))),
                h('div.row.wrap', [100, 500, 1000, 5000].map(n => h('button.btn.btn-small.btn-ghost', {onclick: () => call('admin.user.coins', {delta: n, reason: reasonIn.value}, `+${n} ${T('monete')}`)}, '+' + fmt(n))))),
            h('section.block', h('h3', T('Esperienza e livello')), h('div.row', xpIn, h('button.btn.btn-small', {onclick: () => call('admin.user.xp', {xp: +xpIn.value}, T('Esperienza aggiornata'))}, T('Imposta'))),
                h('p.muted.small', T('Il livello segue l’esperienza: livello 2 = 100, 3 = 300, 4 = 600, 5 = 1000…'))),
            h('section.block', h('h3', T('Dispensa')), pantry, h('button.btn.btn-small.btn-primary', {
                onclick: () => {
                    const set = {};
                    pantry.querySelectorAll('input').forEach(i => (set[i.dataset.ing] = +i.value));
                    call('admin.user.ingredients', {set}, T('Dispensa salvata'));
                },
            }, T('Salva dispensa'))),
            h('section.block', h('h3', T('Ricette')), recipes),
            h('section.block', h('h3', T('Staff e orto')),
                h('p', r.staff.map(s => `${s.name} (${nm(s.role)})`).join(', ')),
                h('p.muted', d.garden.length ? d.garden.map(g => `${nm(g.crop)} ${g.readyAt * 1000 <= Date.now() ? T('maturo') : T('pronto ') + new Date(g.readyAt * 1000).toLocaleTimeString(LOCALE, {timeStyle: 'short'})}`).join(' · ') : T('Orto vuoto.')),
                h('button.btn.btn-small.btn-ghost', {onclick: () => confirm(T('Riportare il ristorante agli arredi iniziali? Il valore degli arredi tolti viene rimborsato in monete.')) && call('admin.user.layout.reset', {}, T('Ristorante ripristinato'))}, T('Ripristina arredi iniziali'))),
        ] : h('p.muted', T('Il ristorante non è ancora stato aperto (si crea al primo accesso al gioco).')),

        h('section.block', h('h3', T('Storico modifiche')), logTable(d.log.map(l => ({...l, player: u.username})))));
}

const fact = (k, v) => h('div.fact', h('small', k), h('b', String(v)));
let DISH_KEYS = null;
const dishKeys = () => DISH_KEYS || [];

// ---------------------------------------------------------------- catalog (prices and rules)

const CAT_TABS = [['items', T('Arredi')], ['dishes', T('Piatti')], ['crops', T('Orto')], ['market', T('Mercato')], ['staff', T('Staff e ampliamenti')], ['rules', T('Regole')]];
const ITEM_CAT = {table: T('Tavoli'), chair: T('Sedie'), stove: T('Cucina'), counter: T('Cucina'), kitchen: T('Cucina'), service: T('Servizio'), lounge: T('Relax'), deco: T('Decori'), rug: T('Tappeti'), outdoor: T('Esterno'), restroom: T('Bagno'), partition: T('Muri e separè'), floor: T('Pavimenti'), wall: T('Pareti')};
const RULE_LABEL = {
    start_coins: T('Monete iniziali di un nuovo ristorante'), sell_back: T('Rimborso alla vendita di un arredo (0–1, 0,5 = metà prezzo)'),
    offline_hours: T('Ore massime di guadagno mentre il giocatore è via'), offline_rate: T('Velocità del guadagno da via (0–1 rispetto al gioco live)'),
    visit_host: T('Monete al ristorante visitato (una volta al giorno per amico)'), visit_base: T('Monete base a chi visita (+5 per livello del ristorante visitato)'),
    level_reward: T('Monete regalate a ogni livello (× il numero del livello)'), market_limit: T('Pezzi acquistabili al giorno per ingrediente'), market_slots: T('Ingredienti in vendita ogni giorno'), xp_customer: T('Esperienza per cliente servito'), xp_harvest: T('Esperienza per raccolto'), expand_rate: T('Costo degli ampliamenti (monete per casella, cresce con la sala)'),
};

async function catalog(main) {
    put(main, h('p.muted', T('Caricamento…')));
    try {
        S.catalog = (await api('admin.catalog')).catalog;
    } catch (e) {
        return fail(e);
    }
    S.draft = structuredClone(S.catalog);
    drawCatalog(main);
}

function changedCount() {
    let n = 0;
    for (const g of ['items', 'dishes', 'crops', 'market', 'staff']) for (const k in S.draft[g]) if (JSON.stringify(S.draft[g][k].value) !== JSON.stringify(S.catalog[g][k].value)) n++;
    for (const k in S.draft.rules) if (S.draft.rules[k].value !== S.catalog.rules[k].value) n++;
    return n;
}

function drawCatalog(main) {
    const body = h('div');
    const bar = h('div.savebar');
    const refreshBar = () => {
        const n = changedCount();
        put(bar, h('span', n ? `${n} modifiche da salvare` : T('Nessuna modifica')),
            h('button.btn.btn-small.btn-ghost', {disabled: !n, onclick: () => ((S.draft = structuredClone(S.catalog)), drawCatalog(main))}, T('Annulla')),
            h('button.btn.btn-small.btn-primary', {disabled: !n, onclick: save}, T('Salva')));
    };
    const save = async () => {
        const out = {};
        for (const g of ['items', 'dishes', 'crops', 'market', 'staff']) out[g] = Object.fromEntries(Object.entries(S.draft[g]).map(([k, v]) => [k, v.value]));
        out.rules = Object.fromEntries(Object.entries(S.draft.rules).map(([k, v]) => [k, v.value]));
        try {
            S.catalog = (await api('admin.catalog.save', {catalog: out})).catalog;
            S.draft = structuredClone(S.catalog);
            toast(T('Prezzi e regole salvati: valgono subito per tutti i giocatori'));
            drawCatalog(main);
        } catch (e) {
            fail(e);
        }
    };
    /** A number input bound to draft[group][key].value[field], highlighted when it differs from the default. */
    const num = (group, key, field, step = 1) => {
        const row = S.draft[group][key];
        const input = h('input', {type: 'number', step, value: row.value[field], title: T('Predefinito: ') + row.default[field]});
        const mark = () => input.classList.toggle('changed', +input.value !== +row.default[field]);
        input.addEventListener('input', () => {
            row.value[field] = input.value === '' ? row.default[field] : +input.value;
            mark();
            refreshBar();
        });
        mark();
        return input;
    };
    const onoff = (group, key) => {
        const row = S.draft[group][key];
        return h('label.switch', h('input', {
            type: 'checkbox', checked: row.value.enabled, onchange: (e) => {
                row.value.enabled = e.target.checked;
                refreshBar();
            },
        }), h('i'));
    };
    const reset = (group, key) => h('button.icon-btn.small', {
        title: T('Torna ai valori predefiniti'), onclick: () => {
            S.draft[group][key].value = structuredClone(S.draft[group][key].default);
            drawCatalog(main);
        },
    }, '↺');
    const table = (heads, rows) => h('table.grid', h('thead', h('tr', heads.map(x => h('th', x)))), h('tbody', rows));
    const tabs = {
        items: () => {
            const groups = {};
            for (const [k, it] of Object.entries(S.draft.items)) (groups[ITEM_CAT[it.cat]] ||= []).push([k, it]);
            return Object.entries(groups).map(([g, list]) => h('section.panel', h('h2', g), table([T('Oggetto'), T('In vendita'), T('Prezzo'), T('Livello'), T('Popolarità'), T('Velocità'), ''],
                list.map(([k, it]) => h('tr', h('td', h('b', nm(k)), h('div.muted.small', it.cat === 'floor' || it.cat === 'wall' ? T('finitura') : it.size)), h('td', onoff('items', k)),
                    h('td', num('items', k, 'price')), h('td', num('items', k, 'level')), h('td', num('items', k, 'pop')),
                    h('td', it.cat === 'stove' ? num('items', k, 'speed', 0.05) : h('span.muted', '—')), h('td', reset('items', k)))))));
        },
        dishes: () => h('section.panel', h('p.muted', T('Prezzo base al livello 1: ogni livello aggiunge il 25%. Tempi in secondi.')),
            table([T('Piatto'), T('Ingredienti'), T('Imparabile'), T('Prezzo base'), T('Livello'), T('Cottura'), T('Pasto'), ''],
                Object.entries(S.draft.dishes).map(([k, d]) => h('tr', h('td', h('b', nm(k))), h('td.small', d.ings.map(i => h('span.ing-inline', ingPic(i), nm(i)))), h('td', onoff('dishes', k)),
                    h('td', num('dishes', k, 'price')), h('td', num('dishes', k, 'level')), h('td', num('dishes', k, 'cook', 0.5)), h('td', num('dishes', k, 'eat', 0.5)), h('td', reset('dishes', k)))))),
        crops: () => h('section.panel', h('p.muted', T('Crescita in secondi (3600 = un’ora). Il raccolto dà la quantità indicata di quell’ingrediente.')),
            table([T('Coltura'), T('Piantabile'), T('Semi'), T('Crescita (s)'), T('Raccolto'), T('Livello'), ''],
                Object.entries(S.draft.crops).map(([k]) => h('tr', h('td', h('b', ingPic(k), nm(k))), h('td', onoff('crops', k)),
                    h('td', num('crops', k, 'cost')), h('td', num('crops', k, 'secs', 10)), h('td', num('crops', k, 'yield')), h('td', num('crops', k, 'level')), h('td', reset('crops', k)))))),
        market: () => h('section.panel', h('p.muted', T('Ogni giorno il mercato propone a rotazione alcuni ingredienti fra quelli in vendita (vedi Regole).')),
            table([T('Ingrediente'), T('In vendita'), T('Prezzo'), ''],
                Object.entries(S.draft.market).map(([k]) => h('tr', h('td', h('b', ingPic(k), nm(k))), h('td', onoff('market', k)), h('td', num('market', k, 'price')), h('td', reset('market', k)))))),
        staff: () => [
            h('section.panel', h('h2', T('Assunzioni')), h('p.muted', T('Costo = primo + aumento × (assunti di quel ruolo − 1).')),
                table([T('Ruolo'), T('Costo del primo'), T('Aumento per ogni altro'), ''], Object.keys(S.draft.staff).map(k => h('tr', h('td', h('b', nm(k))), h('td', num('staff', k, 'base')), h('td', num('staff', k, 'step')), h('td', reset('staff', k)))))),
        ],
        rules: () => h('section.panel', table([T('Regola'), T('Valore'), T('Predefinito')], Object.entries(S.draft.rules).map(([k, r]) => {
            const input = h('input', {type: 'number', step: Number.isInteger(r.default) ? 1 : 0.05, min: r.min, max: r.max, value: r.value});
            const mark = () => input.classList.toggle('changed', +input.value !== +r.default);
            input.addEventListener('input', () => {
                r.value = input.value === '' ? r.default : +input.value;
                mark();
                refreshBar();
            });
            mark();
            return h('tr', h('td', RULE_LABEL[k] || k), h('td', input), h('td.muted', String(r.default)));
        }))),
    };
    put(body, tabs[S.catTab]());
    put(main, h('div.subtabs.tabs', CAT_TABS.map(([k, label]) => h('button.tab' + (S.catTab === k ? '.on' : ''), {onclick: () => ((S.catTab = k), drawCatalog(main))}, label))),
        h('p.note', T('I valori evidenziati sono diversi da quelli predefiniti. Un oggetto tolto dalla vendita resta a chi lo possiede già.')), body, bar);
    refreshBar();
}

// ---------------------------------------------------------------- log

async function log(main, page = 0) {
    put(main, h('p.muted', T('Caricamento…')));
    const d = await api('admin.log', {page}).catch(e => (fail(e), null));
    if (!d) return;
    put(main, h('section.panel', h('h2', T('Registro delle modifiche')), logTable(d.log),
        h('div.row', h('button.btn.btn-small', {disabled: page === 0, onclick: () => log(main, page - 1)}, '←'), h('span', `${T('pagina')} ${page + 1}`),
            h('button.btn.btn-small', {disabled: d.log.length < 50, onclick: () => log(main, page + 1)}, '→'))));
}

// The dish list comes from the catalog (same order as the game)
fetch('api/index.php?r=game.catalog', {credentials: 'same-origin'}).then(r => r.json()).then(d => {
    DISH_KEYS = Object.keys(d.catalog?.dishes || {});
}).catch(() => {}).finally(boot);
