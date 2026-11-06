/**
 * Rendicontazione Ore: the application. Views: month, year, history, settings.
 * Server rules are the real protection: the interface only hides what the API would refuse.
 */
'use strict';

const ROLE_ADMIN = 9;
const ROLE_MASTER = 10;

const KINDS = {
    lavoro: 'Lavoro',
    pioggia: 'Pioggia',
    permesso: 'Permesso',
    ferie: 'Ferie',
    malattia: 'Malattia',
};

const state = {
    user: null,
    workers: [],
    pending: 0,
    googleClientId: '',
    devLogin: false,
    targetId: 0,       // whose hours are on screen
    year: new Date().getFullYear(),
    month: new Date().getMonth() + 1,
    data: null,        // the month on screen
    presets: [],
    usual: null,
};

const isAdmin = () => state.user && state.user.role >= ROLE_ADMIN;
const targetName = () => (state.workers.find(w => w.id === state.targetId) || state.user).name;
const viewingOther = () => state.targetId !== state.user.id;

/* ---- API ---- */

class ApiError extends Error {
    constructor(message, code, extra) {
        super(message);
        this.code = code;
        this.extra = extra || {};
    }
}

async function api(action, { params = {}, body, form } = {}) {
    const url = 'api/index.php?' + new URLSearchParams({ action, ...params });
    const options = { credentials: 'same-origin', headers: {} };
    if (body !== undefined || form) {
        options.method = 'POST';
        options.headers['X-Requested-With'] = 'rendicontazione';
        if (form) {
            options.body = form;
        } else {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }
    }

    let response;
    try {
        response = await fetch(url, options);
    } catch (e) {
        throw new ApiError('Connessione assente. Controlla internet e riprova: i dati non sono stati salvati.', 'network');
    }
    let json;
    try {
        json = await response.json();
    } catch (e) {
        throw new ApiError('Il server non risponde come dovrebbe. Riprova tra poco.', 'bad_response');
    }
    if (!response.ok || !json.ok) {
        if (json.code === 'unauthorized' || json.code === 'inactive') {
            await start();
        }
        throw new ApiError(json.error || 'Si è verificato un errore.', json.code, json);
    }
    return json;
}

const withUser = (extra = {}) => ({ user: state.targetId, ...extra });

function showError(error) {
    console.error(error);
    toast(error.message || 'Si è verificato un errore.', { error: true });
}

/* ---- start and sign-in ---- */

async function start() {
    let boot;
    try {
        boot = await api('boot');
    } catch (e) {
        render(h('main', { class: 'center' }, h('div', { class: 'card sign' }, h('h1', null, 'Rendicontazione Ore'), h('p', null, e.message),
            h('button', { class: 'btn btn-primary btn-big', type: 'button', onclick: start }, 'Riprova'))));
        return;
    }
    Object.assign(state, { user: boot.user, workers: boot.workers, pending: boot.pending, googleClientId: boot.googleClientId, devLogin: boot.devLogin });

    if (!state.user) return renderSignIn();
    if (state.user.status !== 'active') return renderWaiting();

    state.targetId = pickTarget();
    renderShell();
    route();
}

/** An admin lands on the last person viewed, otherwise on whoever actually has hours. */
function pickTarget() {
    if (!isAdmin()) return state.user.id;
    const saved = Number(localStorage.getItem('rend.target'));
    if (state.workers.some(w => w.id === saved)) return saved;
    const worker = state.workers.find(w => w.role < ROLE_ADMIN && w.days > 0)
        || state.workers.find(w => w.role < ROLE_ADMIN);
    return worker ? worker.id : state.user.id;
}

function render(node) {
    document.getElementById('app').replaceChildren(node);
}

function renderSignIn() {
    const slot = h('div', { class: 'google-slot' });
    const dev = state.devLogin ? h('div', { class: 'dev' },
        h('p', null, 'Solo su questo computer (server di sviluppo):'),
        [[1, 'Lavoratore'], [ROLE_ADMIN, 'Admin'], [ROLE_MASTER, 'Master']].map(([role, label]) =>
            h('button', { class: 'btn', type: 'button', onclick: () => api('auth.dev', { body: { role } }).then(start).catch(showError) }, label))) : null;

    render(h('main', { class: 'center' }, h('div', { class: 'card sign' },
        h('img', { src: 'assets/icons/icon.svg', alt: '', width: 88, height: 88 }),
        h('h1', null, 'Rendicontazione Ore'),
        h('p', null, 'Il registro delle ore di lavoro.'),
        slot,
        state.googleClientId ? null : h('p', { class: 'muted' }, "L'accesso con Google non è ancora configurato."),
        dev)));

    if (!state.googleClientId) return;
    const script = h('script', { src: 'https://accounts.google.com/gsi/client', async: true });
    script.onerror = () => slot.replaceChildren(h('p', { class: 'muted' }, 'Non riesco a caricare il pulsante di Google. Controlla la connessione.'));
    script.onload = () => {
        google.accounts.id.initialize({
            client_id: state.googleClientId,
            callback: r => api('auth.google', { body: { credential: r.credential } }).then(start).catch(showError),
        });
        google.accounts.id.renderButton(slot, { theme: 'filled_blue', size: 'large', shape: 'pill', text: 'continue_with', locale: 'it', width: 280 });
    };
    document.head.append(script);
}

function renderWaiting() {
    const waiting = state.user.status === 'pending';
    render(h('main', { class: 'center' }, h('div', { class: 'card sign' },
        h('img', { src: 'assets/icons/icon.svg', alt: '', width: 72, height: 72 }),
        h('h1', null, waiting ? 'Quasi fatto' : 'Accesso non attivo'),
        h('p', null, waiting
            ? `Ciao ${state.user.name}, il tuo account è in attesa di approvazione. Appena verrà approvato potrai entrare da qui.`
            : 'Il tuo account non è attivo. Chiedi a un amministratore.'),
        h('p', { class: 'muted' }, state.user.email),
        waiting ? h('button', { class: 'btn btn-primary btn-big', type: 'button', onclick: start }, 'Controlla di nuovo') : null,
        h('button', { class: 'btn', type: 'button', onclick: logout }, 'Esci'))));
}

async function logout() {
    try {
        await api('auth.logout', { body: {} });
        if (window.google && google.accounts) google.accounts.id.disableAutoSelect();
        await start();
    } catch (e) {
        showError(e);
    }
}

/* ---- shell and routes ---- */

const TABS = [
    ['mese', 'Mese', 'calendar'],
    ['anno', 'Anno', 'chart'],
    ['cronologia', 'Cronologia', 'history'],
    ['altro', 'Altro', 'settings'],
];

function renderShell() {
    const tabs = TABS.map(([id, label, name]) => h('a', { href: '#/' + id, class: 'tab', dataset: { tab: id } },
        icon(name), h('span', null, label),
        id === 'altro' && state.pending ? h('span', { class: 'dot', title: 'Richieste di accesso' }, String(state.pending)) : null));

    const switcher = isAdmin() && state.workers.length > 1
        ? h('label', { class: 'switcher' }, h('span', null, 'Ore di'),
            h('select', { onchange: e => setTarget(Number(e.target.value)) },
                state.workers.map(w => h('option', { value: w.id, selected: w.id === state.targetId }, w.name))))
        : null;

    render(h('div', { class: 'shell' },
        h('header', { class: 'top' },
            h('a', { class: 'brand', href: '#/mese' }, h('img', { src: 'assets/icons/icon.svg', alt: '', width: 34, height: 34 }), h('span', null, 'Rendicontazione')),
            h('nav', { class: 'tabs', 'aria-label': 'Sezioni' }, tabs),
            switcher),
        h('main', { id: 'view', class: 'view', tabIndex: -1 })));
}

function setTarget(id) {
    state.targetId = id;
    localStorage.setItem('rend.target', String(id));
    route();
}

const view = () => document.getElementById('view');

function route() {
    if (!state.user || state.user.status !== 'active' || !view()) return;
    const name = (location.hash.replace(/^#\//, '') || 'mese').split('/')[0];
    const current = TABS.some(t => t[0] === name) ? name : 'mese';
    document.querySelectorAll('.tab').forEach(tab => {
        if (tab.dataset.tab === current) tab.setAttribute('aria-current', 'page');
        else tab.removeAttribute('aria-current');
    });
    ({ mese: showMonth, anno: showYear, cronologia: showHistory, altro: showSettings })[current]();
}

window.addEventListener('hashchange', route);

function viewError(error, retry) {
    if (error.code === 'unauthorized' || error.code === 'inactive') return;
    view().replaceChildren(h('div', { class: 'card empty' }, h('p', null, error.message),
        h('button', { class: 'btn btn-primary', type: 'button', onclick: retry }, 'Riprova')));
}

/* ---- month ---- */

async function showMonth() {
    try {
        const data = await api('month', { params: withUser({ year: state.year, month: state.month }) });
        state.presets = data.presets;
        applyMonth(data);
    } catch (e) {
        viewError(e, showMonth);
    }
}

function applyMonth(data) {
    state.data = data;
    state.usual = data.usual;
    if (data.year === state.year && data.month === state.month) renderMonth();
}

function moveMonth(step) {
    const date = new Date(state.year, state.month - 1 + step, 1);
    state.year = date.getFullYear();
    state.month = date.getMonth() + 1;
    showMonth();
}

function goToToday() {
    const now = new Date();
    state.year = now.getFullYear();
    state.month = now.getMonth() + 1;
    if (location.hash.startsWith('#/mese') || !location.hash) showMonth(); else location.hash = '#/mese';
}

/** The hours a date normally has; on a holiday every hour worked is overtime, as in the old sheet. */
function usualFor(iso) {
    const weekday = parseDate(iso).getDay();
    const slots = (weekday === 6 || weekday === 0 ? state.usual.saturday : state.usual.weekday).map(s => ({ ...s }));
    if (holidayName(iso) || weekday === 0) slots.forEach(s => { s.overtime = true; });
    return slots;
}

function dayByDate(iso) {
    return state.data.days.find(d => d.date === iso) || null;
}

/** Past working days of the current month with nothing recorded. */
function missingDays() {
    const today = todayIso();
    const days = [];
    for (let iso = isoDate(state.year, state.month, 1); iso < today && iso.startsWith(today.slice(0, 7)); iso = shiftDate(iso, 1)) {
        const weekday = parseDate(iso).getDay();
        if (weekday !== 0 && weekday !== 6 && !holidayName(iso) && !dayByDate(iso)) days.push(iso);
    }
    return days;
}

async function saveQuick(iso, slots) {
    try {
        const data = await api('day.save', { body: withUser({ date: iso, kind: 'lavoro', note: '', slots }) });
        applyMonth(data);
        toast(`Salvato: ${dateLabel(iso)}, ${fmtHours(slotsMinutes(slots).total)}`, { action: { label: 'Modifica', run: () => openDay(iso) } });
    } catch (e) {
        showError(e);
    }
}

function todayCard() {
    const today = todayIso();
    if (!today.startsWith(isoDate(state.year, state.month, 1).slice(0, 7))) return null;
    const record = dayByDate(today);
    const weekday = parseDate(today).getDay();
    const holiday = holidayName(today);

    if (record) {
        return h('section', { class: 'card today done' },
            h('div', null,
                h('p', { class: 'eyebrow' }, 'Oggi, ' + dateLabel(today)),
                h('p', { class: 'today-line' }, icon('check'), fmtHours(record.ordinary + record.overtime), record.slots.length ? h('span', { class: 'today-slots' }, slotsNodes(record.slots)) : null)),
            h('button', { class: 'btn', type: 'button', onclick: () => openDay(today) }, 'Modifica'));
    }
    if (weekday === 0 || holiday) {
        return h('section', { class: 'card today rest' },
            h('p', { class: 'eyebrow' }, 'Oggi, ' + dateLabel(today)),
            h('p', null, holiday ? `${holiday}: giornata di festa.` : 'Domenica: giornata di riposo.'),
            h('button', { class: 'btn', type: 'button', onclick: () => openDay(today) }, 'Ho lavorato: segna le ore'));
    }
    const slots = usualFor(today);
    const minutes = slotsMinutes(slots);
    return h('section', { class: 'card today' },
        h('p', { class: 'eyebrow' }, 'Oggi, ' + dateLabel(today)),
        h('h2', null, viewingOther() ? `Ore di ${targetName()}` : 'Com’è andata oggi?'),
        h('button', { class: 'btn btn-primary btn-big btn-today', type: 'button', onclick: () => saveQuick(today, slots) },
            icon('check'),
            h('span', { class: 'btn-today-text' },
                h('strong', null, `Giornata normale: ${fmtHours(minutes.total)}`),
                h('small', null, slotsText(slots)))),
        h('button', { class: 'btn btn-big', type: 'button', onclick: () => openDay(today) }, 'Orario diverso, pioggia, ferie…'));
}

function missingCard() {
    const today = todayIso();
    if (!today.startsWith(isoDate(state.year, state.month, 1).slice(0, 7))) return null;
    const missing = missingDays();
    if (!missing.length) return null;
    return h('section', { class: 'card missing' },
        h('h2', null, missing.length === 1 ? 'Manca un giorno' : `Mancano ${missing.length} giorni`),
        h('div', { class: 'chips' }, missing.slice(-6).map(iso => {
            const d = parseDate(iso);
            return h('button', { class: 'chip', type: 'button', onclick: () => openDay(iso) }, `${WEEKDAYS[d.getDay()].slice(0, 3)} ${d.getDate()}`);
        })));
}

function totalsCard(totals) {
    const extras = [
        [totals.worked, totals.worked === 1 ? 'giorno lavorato' : 'giorni lavorati'],
        [totals.pioggia, 'pioggia'], [totals.permesso, 'permesso'], [totals.ferie, 'ferie'], [totals.malattia, 'malattia'],
    ].filter(([n]) => n > 0);
    return h('section', { class: 'card totals' },
        h('div', { class: 'stats' },
            h('div', { class: 'stat' }, h('span', null, 'Ordinarie'), h('strong', null, fmtHours(totals.ordinary))),
            h('div', { class: 'stat stat-ot' }, h('span', null, 'Straordinarie'), h('strong', null, fmtHours(totals.overtime))),
            h('div', { class: 'stat stat-total' }, h('span', null, 'Totale'), h('strong', null, fmtHours(totals.total)))),
        extras.length ? h('p', { class: 'muted' }, extras.map(([n, label]) => `${n} ${label}`).join(' · ')) : null);
}

function dayRow(iso, today) {
    const d = parseDate(iso);
    const weekday = d.getDay();
    const record = dayByDate(iso);
    const holiday = holidayName(iso);
    const missing = !record && iso < today && iso.startsWith(today.slice(0, 7)) && weekday !== 0 && weekday !== 6 && !holiday;

    const classes = ['day'];
    if (iso === today) classes.push('day-today');
    if (weekday === 0 || holiday) classes.push('day-rest');
    if (weekday === 6) classes.push('day-sat');
    if (!record) classes.push('day-empty');

    let body;
    if (record) {
        body = [
            h('span', { class: 'day-main' },
                record.kind !== 'lavoro' ? h('span', { class: 'kind kind-' + record.kind }, KINDS[record.kind]) : null,
                record.slots.length ? h('span', { class: 'day-slots' }, slotsNodes(record.slots)) : null),
            record.note ? h('span', { class: 'day-note' }, record.note) : null,
        ];
    } else {
        body = h('span', { class: 'day-main muted' }, holiday || (missing ? 'Da segnare' : ''));
    }

    return h('li', null, h('button', { class: classes.join(' '), type: 'button', onclick: () => openDay(iso), 'aria-label': capitalize(dateLabel(iso)) + (record ? ', ' + fmtHours(record.ordinary + record.overtime) : ', vuoto') },
        h('span', { class: 'day-date' }, h('small', null, WEEKDAYS[weekday].slice(0, 3)), h('b', null, String(d.getDate()))),
        h('span', { class: 'day-body' }, body),
        record && record.ordinary + record.overtime > 0
            ? h('span', { class: 'day-hours' }, h('b', null, fmtHours(record.ordinary + record.overtime)), h('small', { title: 'Progressivo del mese' }, 'tot. ' + fmtHours(record.running)))
            : null));
}

function renderMonth() {
    const { totals } = state.data;
    const today = todayIso();
    const daysInMonth = new Date(state.year, state.month, 0).getDate();
    const isCurrent = today.startsWith(isoDate(state.year, state.month, 1).slice(0, 7));
    const rows = [];
    for (let d = 1; d <= daysInMonth; d++) rows.push(dayRow(isoDate(state.year, state.month, d), today));

    view().replaceChildren(
        otherBanner(),
        h('div', { class: 'month-grid' },
            h('div', { class: 'month-side' },
                h('div', { class: 'pager' },
                    h('button', { class: 'btn btn-icon', type: 'button', 'aria-label': 'Mese precedente', onclick: () => moveMonth(-1) }, icon('left')),
                    h('h1', null, `${capitalize(MONTHS[state.month - 1])} ${state.year}`),
                    h('button', { class: 'btn btn-icon', type: 'button', 'aria-label': 'Mese successivo', onclick: () => moveMonth(1) }, icon('right'))),
                isCurrent ? null : h('button', { class: 'btn btn-link', type: 'button', onclick: goToToday }, 'Torna a oggi'),
                todayCard(),
                missingCard(),
                totalsCard(totals)),
            h('ol', { class: 'card days' }, rows)));
}

function otherBanner() {
    return viewingOther() ? h('p', { class: 'banner' }, icon('user'), `Stai vedendo le ore di ${targetName()}.`) : null;
}

/* ---- slot editor, shared by the day and by the quick schedules ---- */

function slotEditor(slots, onChange) {
    const box = h('div', { class: 'slots' });
    const paint = () => {
        box.replaceChildren(...slots.map((slot, index) => {
            const minutes = slotsMinutes([slot]).total;
            const set = (key, value) => {
                slot[key] = value;
                duration.textContent = slotsMinutes([slot]).total ? fmtHours(slotsMinutes([slot]).total) : '';
                onChange();
            };
            const duration = h('span', { class: 'slot-duration' }, minutes ? fmtHours(minutes) : '');
            return h('div', { class: 'slot' + (slot.overtime ? ' slot-ot' : '') },
                h('label', null, h('span', null, 'Dalle'), h('input', { type: 'time', value: slot.start, required: true, oninput: e => set('start', e.target.value) })),
                h('label', null, h('span', null, 'Alle'), h('input', { type: 'time', value: slot.end, required: true, oninput: e => set('end', e.target.value) })),
                h('button', { class: 'btn btn-icon btn-plain', type: 'button', 'aria-label': 'Togli questa fascia oraria', onclick: () => { slots.splice(index, 1); paint(); onChange(); } }, icon('close')),
                h('label', { class: 'switch' },
                    h('input', { type: 'checkbox', checked: slot.overtime, onchange: e => { slot.overtime = e.target.checked; paint(); onChange(); } }),
                    h('span', { class: 'switch-track' }),
                    h('span', null, 'Straordinario')),
                duration);
        }));
    };
    paint();
    box.repaint = paint;
    return box;
}

function nextSlot(slots) {
    const last = slots[slots.length - 1];
    return { start: last ? last.end : '07:00', end: '', overtime: false };
}

/* ---- day editor ---- */

function openDay(iso) {
    const record = dayByDate(iso);
    const draft = record
        ? { kind: record.kind, note: record.note, slots: record.slots.map(s => ({ ...s })) }
        : { kind: 'lavoro', note: '', slots: [] };
    const original = JSON.stringify(draft);
    const dirty = () => JSON.stringify(draft) !== original;

    const dialog = h('dialog', { class: 'sheet' });
    const totalLine = h('p', { class: 'sheet-total', 'aria-live': 'polite' });
    const paintTotal = () => {
        const m = slotsMinutes(draft.slots);
        totalLine.replaceChildren(h('strong', null, fmtHours(m.total)),
            m.overtime ? h('span', null, ` di cui ${fmtHours(m.overtime)} straord.`) : null);
    };

    const slots = slotEditor(draft.slots, paintTotal);
    const kinds = h('div', { class: 'kinds', role: 'radiogroup', 'aria-label': 'Tipo di giornata' });
    const paintKinds = () => kinds.replaceChildren(...Object.entries(KINDS).map(([kind, label]) =>
        h('button', {
            class: `kind-choice kind-${kind}`, type: 'button', role: 'radio', 'aria-checked': String(draft.kind === kind),
            onclick: () => { draft.kind = kind; paintKinds(); },
        }, label)));
    paintKinds();

    const applySlots = (list, kind) => {
        draft.slots.splice(0, draft.slots.length, ...list.map(s => ({ ...s })));
        if (kind) { draft.kind = kind; paintKinds(); }
        slots.repaint();
        paintTotal();
    };

    const usual = usualFor(iso);
    const quick = [{ name: 'Solito orario', kind: 'lavoro', slots: usual }]
        .concat(state.presets.filter(p => JSON.stringify(p.slots) !== JSON.stringify(usual)));

    const note = h('textarea', { rows: 3, maxLength: 1000, placeholder: 'Per esempio: pioggia, timbrato alle 10', value: draft.note, oninput: e => { draft.note = e.target.value; } });

    const save = async () => {
        try {
            const data = await api('day.save', { body: withUser({ date: iso, kind: draft.kind, note: draft.note, slots: draft.slots }) });
            applyMonth(data);
            closeDialog(dialog, true);
            toast('Salvato');
        } catch (e) {
            showError(e);
        }
    };
    const remove = async () => {
        const yes = await ask({ title: 'Cancellare questa giornata?', text: `Tutti i dati di ${dateLabel(iso)} verranno tolti.`, yes: 'Cancella', danger: true });
        if (!yes) return;
        try {
            applyMonth(await api('day.delete', { body: withUser({ date: iso }) }));
            closeDialog(dialog, true);
            toast('Giornata cancellata');
        } catch (e) {
            showError(e);
        }
    };

    const step = async delta => {
        if (dirty() && !(await ask({ title: 'Lasciare senza salvare?', text: 'Le modifiche a questo giorno andranno perse.', yes: 'Lascia', no: 'Resta' }))) return;
        const next = shiftDate(iso, delta);
        await closeDialog(dialog, true);
        const [y, m] = next.split('-').map(Number);
        if (y !== state.year || m !== state.month) {
            state.year = y;
            state.month = m;
            await showMonth();
        }
        setTimeout(() => openDay(next), 60);
    };

    const holiday = holidayName(iso);
    dialog.append(
        h('form', { method: 'dialog', class: 'sheet-form', onsubmit: e => { e.preventDefault(); save(); } },
            h('header', { class: 'sheet-head' },
                h('button', { class: 'btn btn-icon btn-plain', type: 'button', 'aria-label': 'Giorno precedente', onclick: () => step(-1) }, icon('left')),
                h('div', { class: 'sheet-title' },
                    h('h2', null, capitalize(dateLabel(iso))),
                    h('p', { class: 'muted' }, [holiday, viewingOther() ? `ore di ${targetName()}` : null].filter(Boolean).join(' · '))),
                h('button', { class: 'btn btn-icon btn-plain', type: 'button', 'aria-label': 'Giorno successivo', onclick: () => step(1) }, icon('right')),
                h('button', { class: 'btn btn-icon btn-plain', type: 'button', 'aria-label': 'Chiudi', onclick: () => closeDialog(dialog) }, icon('close'))),
            h('div', { class: 'sheet-body' },
                h('section', null, h('h3', null, 'Che giornata è stata?'), kinds),
                h('section', null,
                    h('h3', null, 'Orari'),
                    h('div', { class: 'chips' }, quick.map(p => h('button', {
                        class: 'chip', type: 'button', title: slotsText(p.slots), onclick: () => applySlots(p.slots, p.kind),
                    }, h('span', null, p.name), h('small', null, slotsText(p.slots))))),
                    slots,
                    h('button', { class: 'btn btn-add', type: 'button', onclick: () => { draft.slots.push(nextSlot(draft.slots)); slots.repaint(); paintTotal(); } }, icon('plus'), 'Aggiungi fascia oraria')),
                h('section', null, h('h3', null, h('label', null, 'Note')), note),
                record ? h('button', { class: 'btn btn-link btn-delete', type: 'button', onclick: remove }, icon('trash'), 'Cancella questa giornata') : null),
            h('footer', { class: 'sheet-foot' },
                totalLine,
                h('button', { class: 'btn btn-primary btn-big', type: 'submit' }, 'Salva'))));

    paintTotal();
    openDialog(dialog, async () => !dirty() || ask({ title: 'Chiudere senza salvare?', text: 'Le modifiche a questo giorno andranno perse.', yes: 'Chiudi', no: 'Resta' }));
}

/* ---- year ---- */

async function showYear() {
    try {
        renderYear(await api('year', { params: withUser({ year: state.year }) }));
    } catch (e) {
        viewError(e, showYear);
    }
}

function renderYear(data) {
    const max = Math.max(1, ...data.months.map(m => m.total));
    const t = data.totals;
    const rows = data.months.map(m => h('tr', {
        class: m.total ? '' : 'row-empty',
        onclick: () => { state.month = m.month; location.hash = '#/mese'; },
    },
    h('th', { scope: 'row' }, h('a', { href: '#/mese', onclick: () => { state.month = m.month; } }, capitalize(MONTHS[m.month - 1]))),
    h('td', { class: 'num' }, m.total ? fmtHours(m.ordinary) : '–'),
    h('td', { class: 'num' }, m.total ? fmtHours(m.overtime) : '–'),
    h('td', { class: 'num strong' }, m.total ? fmtHours(m.total) : '–'),
    h('td', { class: 'bar-cell', 'aria-hidden': 'true' }, m.total ? h('div', { class: 'bar', title: `${capitalize(MONTHS[m.month - 1])}: ${fmtHours(m.ordinary)} ordinarie, ${fmtHours(m.overtime)} straordinarie` },
        h('span', { class: 'bar-ord', style: `width:${m.ordinary / max * 100}%` }),
        m.overtime ? h('span', { class: 'bar-ot', style: `width:${m.overtime / max * 100}%` }) : null) : null)));

    const counts = [
        [t.worked, 'Giorni lavorati'], [t.pioggia, 'Pioggia'], [t.permesso, 'Permesso'], [t.ferie, 'Ferie'], [t.malattia, 'Malattia'],
    ];
    const move = step => { state.year += step; showYear(); };

    view().replaceChildren(
        otherBanner(),
        h('div', { class: 'pager' },
            h('button', { class: 'btn btn-icon', type: 'button', 'aria-label': 'Anno precedente', onclick: () => move(-1) }, icon('left')),
            h('h1', null, String(data.year)),
            h('button', { class: 'btn btn-icon', type: 'button', 'aria-label': 'Anno successivo', onclick: () => move(1) }, icon('right'))),
        totalsCard(t),
        h('section', { class: 'card counts' }, counts.map(([n, label]) => h('div', { class: 'stat' }, h('span', null, label), h('strong', null, String(n))))),
        h('section', { class: 'card table-card' },
            h('table', { class: 'year' },
                h('thead', null, h('tr', null,
                    h('th', { scope: 'col' }, 'Mese'),
                    h('th', { scope: 'col', class: 'num' }, 'Ordinarie'),
                    h('th', { scope: 'col', class: 'num' }, 'Straord.'),
                    h('th', { scope: 'col', class: 'num' }, 'Totale'),
                    h('th', { scope: 'col', class: 'bar-cell' }, h('span', { class: 'legend' }, h('i', { class: 'bar-ord' }), 'Ordinarie', h('i', { class: 'bar-ot' }), 'Straordinarie')))),
                h('tbody', null, rows))));
}

/* ---- history ---- */

let historyEntries = [];

async function showHistory() {
    historyEntries = [];
    await loadHistory(0);
}

async function loadHistory(before) {
    try {
        const data = await api('history', { params: withUser({ before }) });
        historyEntries = historyEntries.concat(data.entries);
        renderHistory(data.more);
    } catch (e) {
        viewError(e, showHistory);
    }
}

const HISTORY_VERBS = { 'day.add': 'ha aggiunto', 'day.edit': 'ha modificato', 'day.delete': 'ha cancellato' };

function daySummary(day) {
    if (!day) return 'nessun dato';
    const parts = [];
    if (day.kind !== 'lavoro') parts.push(KINDS[day.kind]);
    if (day.slots.length) parts.push(`${slotsText(day.slots)} = ${fmtHours(slotsMinutes(day.slots).total)}`);
    if (day.note) parts.push(`«${day.note}»`);
    return parts.join(' · ') || 'nessun dato';
}

function historyTitle(entry) {
    if (HISTORY_VERBS[entry.action]) return `${entry.actor} ${HISTORY_VERBS[entry.action]} ${dateLabel(entry.dates[0], true)}`;
    if (entry.action === 'undo') {
        return entry.dates.length === 1
            ? `${entry.actor} ha annullato una modifica di ${dateLabel(entry.dates[0], true)}`
            : `${entry.actor} ha annullato una modifica (${entry.dates.length} giorni)`;
    }
    return `${entry.actor}: ${entry.summary}`;
}

function historyDetail(entry) {
    const change = entry.change;
    if (!change) return null;
    if (entry.action === 'day.add') return h('p', { class: 'change' }, daySummary(change.after));
    if (entry.action === 'day.delete') return h('p', { class: 'change' }, h('span', { class: 'change-old' }, daySummary(change.before)));
    return h('dl', { class: 'change' },
        h('dt', null, 'Prima'), h('dd', null, daySummary(change.before)),
        h('dt', null, 'Dopo'), h('dd', null, daySummary(change.after)));
}

async function undoEntry(entry) {
    const what = entry.dates.length === 1 ? dateLabel(entry.dates[0], true) : `${entry.dates.length} giorni`;
    const yes = await ask({ title: 'Annullare questa modifica?', text: `${capitalize(what)} tornerà com'era prima. L'annullamento resterà in cronologia.`, yes: 'Annulla la modifica', no: 'Lascia così' });
    if (!yes) return;
    try {
        await sendUndo(entry.id, false);
    } catch (e) {
        if (e.code !== 'conflict') return showError(e);
        const list = e.extra.conflicts.slice(0, 5).map(d => dateLabel(d)).join(', ');
        const force = await ask({
            title: 'Ci sono modifiche più recenti',
            text: `Dopo questa modifica è stato cambiato ancora: ${list}. Annullando si perdono anche i cambiamenti più recenti.`,
            yes: 'Annulla comunque', no: 'Lascia così', danger: true,
        });
        if (force) await sendUndo(entry.id, true).catch(showError);
    }
}

async function sendUndo(id, force) {
    await api('history.undo', { body: { id, force } });
    toast('Modifica annullata');
    await showHistory();
}

function renderHistory(more) {
    const list = historyEntries.map(entry => h('li', { class: 'entry' + (entry.undone ? ' entry-undone' : '') },
        h('p', { class: 'entry-title' }, historyTitle(entry)),
        h('p', { class: 'muted' }, timeAgoLabel(entry.at)),
        historyDetail(entry),
        entry.undone ? h('p', { class: 'entry-flag' }, `Annullata da ${entry.undone.by}, ${timeAgoLabel(entry.undone.at)}`) : null,
        entry.canUndo ? h('button', { class: 'btn', type: 'button', onclick: () => undoEntry(entry) }, icon('undo'), entry.action === 'undo' ? 'Ripristina la modifica' : 'Annulla questa modifica') : null));

    view().replaceChildren(
        otherBanner(),
        h('h1', { class: 'page-title' }, 'Cronologia'),
        h('p', { class: 'lead' }, isAdmin()
            ? 'Tutte le modifiche, dalla più recente. Da amministratore puoi annullare quelle alle ore.'
            : 'Tutte le modifiche alle tue ore, dalla più recente. Se hai sbagliato qualcosa, correggi il giorno dal mese. Solo un amministratore può annullare una modifica da qui.'),
        historyEntries.length
            ? h('ol', { class: 'card entries' }, list)
            : h('div', { class: 'card empty' }, h('p', null, 'Ancora nessuna modifica.')),
        more ? h('button', { class: 'btn btn-wide', type: 'button', onclick: () => loadHistory(historyEntries[historyEntries.length - 1].id) }, 'Mostra le precedenti') : null);
}

/* ---- settings ---- */

function choice(label, key, options, apply) {
    const current = localStorage.getItem(key) || options[0][0];
    const box = h('div', { class: 'segmented', role: 'radiogroup', 'aria-label': label });
    const paint = value => box.replaceChildren(...options.map(([id, text]) => h('button', {
        type: 'button', role: 'radio', 'aria-checked': String(id === value),
        onclick: () => { localStorage.setItem(key, id); apply(id); paint(id); },
    }, text)));
    paint(current);
    return h('div', { class: 'setting' }, h('span', null, label), box);
}

async function showSettings() {
    const u = state.user;
    const exportUrl = 'api/index.php?' + new URLSearchParams({ action: 'export', user: state.targetId, year: state.year });
    if (!state.presets.length) {
        try { state.presets = (await api('month', { params: withUser({ year: state.year, month: state.month }) })).presets; } catch (e) { viewError(e, showSettings); return; }
    }

    const body = [
        otherBanner(),
        h('h1', { class: 'page-title' }, 'Altro'),
        h('section', { class: 'card panel' },
            h('h2', null, 'Scarica e stampa'),
            h('a', { class: 'btn btn-wide', href: exportUrl, download: '' }, icon('download'), `Scarica Excel del ${state.year}`),
            h('button', { class: 'btn btn-wide', type: 'button', onclick: printMonth }, icon('print'), `Stampa ${MONTHS[state.month - 1]} ${state.year}`)),
        h('section', { class: 'card panel' },
            h('h2', null, 'Orari rapidi'),
            h('p', { class: 'muted' }, viewingOther() ? `Gli orari proposti a ${targetName()}.` : 'Gli orari che vuoi avere a portata di mano.'),
            h('ul', { class: 'presets' }, state.presets.map((p, i) => h('li', null,
                h('button', { class: 'row-button', type: 'button', onclick: () => editPreset(i) },
                    h('span', null, h('b', null, p.name), h('small', null, slotsText(p.slots))), icon('pencil'))))),
            state.presets.length < 12 ? h('button', { class: 'btn btn-wide', type: 'button', onclick: () => editPreset(-1) }, icon('plus'), 'Nuovo orario rapido') : null),
        h('section', { class: 'card panel' },
            h('h2', null, 'Aspetto'),
            choice('Dimensione del testo', 'rend.text', [['normal', 'Normale'], ['large', 'Grande']], v => { document.documentElement.dataset.text = v; }),
            choice('Colori', 'rend.theme', [['auto', 'Automatici'], ['light', 'Chiari'], ['dark', 'Scuri']], v => { document.documentElement.dataset.theme = v; })),
    ];

    if (isAdmin()) body.push(usersPanel(), importPanel());

    body.push(h('section', { class: 'card panel' },
        h('h2', null, 'Il tuo account'),
        h('p', null, h('b', null, u.name), h('br'), h('span', { class: 'muted' }, u.email)),
        h('p', { class: 'muted' }, u.role >= ROLE_MASTER ? 'Master Admin' : (u.role >= ROLE_ADMIN ? 'Amministratore' : 'Utente')),
        h('button', { class: 'btn btn-wide', type: 'button', onclick: logout }, icon('logout'), 'Esci')));

    view().replaceChildren(...body.filter(Boolean));
}

function editPreset(index) {
    const isNew = index < 0;
    const source = isNew ? { name: '', kind: 'lavoro', slots: [{ start: '07:00', end: '12:00', overtime: false }] } : state.presets[index];
    const draft = { name: source.name, kind: source.kind, slots: source.slots.map(s => ({ ...s })) };
    const dialog = h('dialog', { class: 'sheet' });
    const slots = slotEditor(draft.slots, () => {});

    const persist = async list => {
        try {
            const data = await api('presets.save', { body: withUser({ presets: list }) });
            state.presets = data.presets;
            closeDialog(dialog, true);
            toast('Salvato');
            showSettings();
        } catch (e) {
            showError(e);
        }
    };
    const submit = () => {
        const list = state.presets.slice();
        if (isNew) list.push(draft); else list[index] = draft;
        persist(list);
    };
    const remove = async () => {
        if (await ask({ title: 'Togliere questo orario rapido?', text: source.name, yes: 'Togli', danger: true })) {
            persist(state.presets.filter((_, i) => i !== index));
        }
    };

    dialog.append(h('form', { method: 'dialog', class: 'sheet-form', onsubmit: e => { e.preventDefault(); submit(); } },
        h('header', { class: 'sheet-head' },
            h('div', { class: 'sheet-title' }, h('h2', null, isNew ? 'Nuovo orario rapido' : 'Modifica orario rapido')),
            h('button', { class: 'btn btn-icon btn-plain', type: 'button', 'aria-label': 'Chiudi', onclick: () => closeDialog(dialog) }, icon('close'))),
        h('div', { class: 'sheet-body' },
            h('section', null, h('h3', null, h('label', { htmlFor: 'preset-name' }, 'Nome')),
                h('input', { id: 'preset-name', type: 'text', maxLength: 40, value: draft.name, required: true, oninput: e => { draft.name = e.target.value; } })),
            h('section', null, h('h3', null, 'Orari'), slots,
                h('button', { class: 'btn btn-add', type: 'button', onclick: () => { draft.slots.push(nextSlot(draft.slots)); slots.repaint(); } }, icon('plus'), 'Aggiungi fascia oraria')),
            isNew ? null : h('button', { class: 'btn btn-link btn-delete', type: 'button', onclick: remove }, icon('trash'), 'Togli questo orario')),
        h('footer', { class: 'sheet-foot' }, h('button', { class: 'btn btn-primary btn-big', type: 'submit' }, 'Salva'))));
    openDialog(dialog);
}

/* ---- admin: users ---- */

const STATUS_LABELS = { pending: 'In attesa', active: 'Attivo', rejected: 'Rifiutato', disabled: 'Disattivato' };

function usersPanel() {
    const box = h('div', { class: 'users' }, h('p', { class: 'muted' }, 'Caricamento…'));
    const applyUsers = data => {
        state.pending = data.pending;
        if (data.workers) state.workers = data.workers;
        paint(data.users);
    };
    const update = fields => api('users.update', { body: fields }).then(data => { applyUsers(data); renderShell(); route(); }).catch(showError);

    const paint = users => {
        box.replaceChildren(...users.map(u => {
            const locked = u.id === state.user.id || u.role >= ROLE_MASTER || (u.role >= ROLE_ADMIN && state.user.role < ROLE_MASTER);
            const actions = [];
            if (!locked) {
                if (u.status === 'pending') {
                    actions.push(h('button', { class: 'btn btn-primary', type: 'button', onclick: () => update({ id: u.id, status: 'active' }) }, 'Approva'));
                    actions.push(h('button', { class: 'btn', type: 'button', onclick: () => update({ id: u.id, status: 'rejected' }) }, 'Rifiuta'));
                } else if (u.status === 'active') {
                    actions.push(h('button', { class: 'btn', type: 'button', onclick: () => update({ id: u.id, status: 'disabled' }) }, 'Disattiva'));
                } else {
                    actions.push(h('button', { class: 'btn', type: 'button', onclick: () => update({ id: u.id, status: 'active' }) }, 'Riattiva'));
                }
                if (state.user.role >= ROLE_MASTER && u.status === 'active') {
                    actions.push(u.role >= ROLE_ADMIN
                        ? h('button', { class: 'btn', type: 'button', onclick: () => update({ id: u.id, role: 1 }) }, 'Togli admin')
                        : h('button', { class: 'btn', type: 'button', onclick: () => update({ id: u.id, role: ROLE_ADMIN }) }, 'Rendi admin'));
                }
            }
            return h('div', { class: 'user' + (u.status === 'pending' ? ' user-pending' : '') },
                h('p', null, h('b', null, u.name), h('br'), h('span', { class: 'muted' }, u.email)),
                h('p', { class: 'muted' }, [u.role >= ROLE_MASTER ? 'Master Admin' : (u.role >= ROLE_ADMIN ? 'Amministratore' : 'Utente'), STATUS_LABELS[u.status], u.lastLogin ? null : 'non ha ancora fatto accesso'].filter(Boolean).join(' · ')),
                actions.length ? h('div', { class: 'user-actions' }, actions) : null);
        }));
    };
    api('users').then(applyUsers).catch(showError);

    const name = h('input', { type: 'text', placeholder: 'Nome', required: true, maxLength: 100, 'aria-label': 'Nome' });
    const email = h('input', { type: 'email', placeholder: 'Email Google', required: true, 'aria-label': 'Email Google' });
    const add = h('form', {
        class: 'add-user',
        onsubmit: e => {
            e.preventDefault();
            api('users.add', { body: { name: name.value, email: email.value } }).then(data => {
                name.value = '';
                email.value = '';
                applyUsers(data);
                toast('Account creato');
                renderShell();
                route();
            }).catch(showError);
        },
    }, h('h3', null, 'Aggiungi una persona'),
    h('p', { class: 'muted' }, 'Potrà entrare con questo account Google senza aspettare l’approvazione.'),
    name, email, h('button', { class: 'btn', type: 'submit' }, icon('plus'), 'Aggiungi'));

    return h('section', { class: 'card panel' }, h('h2', null, 'Utenti'), box, add);
}

/* ---- admin: import ---- */

function importPanel() {
    const result = h('div', { class: 'import-result' });
    const input = h('input', {
        type: 'file', accept: '.xlsx', class: 'file',
        onchange: async () => {
            const file = input.files[0];
            if (!file) return;
            result.replaceChildren(h('p', { class: 'muted' }, 'Lettura del file…'));
            const form = new FormData();
            form.append('file', file);
            form.append('user', String(state.targetId));
            try {
                paintPreview(await api('import.preview', { form }));
            } catch (e) {
                result.replaceChildren();
                showError(e);
            }
            input.value = '';
        },
    });

    const paintPreview = preview => {
        const total = preview.months.reduce((sum, m) => sum + m.ordinary + m.overtime, 0);
        const count = Object.keys(preview.days).length;
        result.replaceChildren(
            h('h3', null, `Anno ${preview.year}: ${count} giorni, ${fmtHours(total)}`),
            h('table', { class: 'year' },
                h('thead', null, h('tr', null, h('th', { scope: 'col' }, 'Mese'), h('th', { scope: 'col', class: 'num' }, 'Giorni'), h('th', { scope: 'col', class: 'num' }, 'Ordinarie'), h('th', { scope: 'col', class: 'num' }, 'Straord.'))),
                h('tbody', null, preview.months.map(m => h('tr', null,
                    h('th', { scope: 'row' }, capitalize(MONTHS[m.month - 1])),
                    h('td', { class: 'num' }, String(m.days)),
                    h('td', { class: 'num' }, fmtHours(m.ordinary)),
                    h('td', { class: 'num' }, fmtHours(m.overtime)))))),
            preview.warnings.length ? h('div', { class: 'warnings' },
                h('h3', null, 'Da controllare nel foglio'),
                h('ul', null, preview.warnings.map(w => h('li', null, w))),
                h('p', { class: 'muted' }, 'I giorni vengono importati così come sono scritti: potrai correggerli dopo.')) : null,
            preview.overwrites ? h('p', { class: 'warn-text' }, `Attenzione: ${preview.overwrites} giorni già presenti verranno sostituiti.`) : null,
            h('button', {
                class: 'btn btn-primary btn-wide', type: 'button',
                onclick: async () => {
                    try {
                        const data = await api('import.apply', { body: withUser({ days: preview.days }) });
                        state.year = preview.year;
                        result.replaceChildren(h('p', null, data.changed ? `Fatto: ${data.changed} giorni importati.` : 'Niente da importare: i dati sono già uguali.'));
                        const boot = await api('boot');
                        state.workers = boot.workers;
                    } catch (e) {
                        showError(e);
                    }
                },
            }, `Importa per ${targetName()}`));
    };

    return h('section', { class: 'card panel' },
        h('h2', null, 'Importa da Excel'),
        h('p', { class: 'muted' }, `Carica il foglio annuale: vedrai il riepilogo prima di importare nelle ore di ${targetName()}. L'importazione si può annullare dalla cronologia.`),
        h('label', { class: 'btn btn-wide' }, icon('upload'), 'Scegli il file Excel', input),
        result);
}

/* ---- print ---- */

async function printMonth() {
    try {
        const data = await api('month', { params: withUser({ year: state.year, month: state.month }) });
        const rows = data.days.flatMap(day => {
            const list = day.slots.length ? day.slots : [null];
            let running = day.running - day.ordinary - day.overtime;
            return list.map((slot, i) => {
                const duration = slot ? timeToMinutes(slot.end) - timeToMinutes(slot.start) : 0;
                running += duration;
                return h('tr', null,
                    h('td', null, i === 0 ? capitalize(dateLabel(day.date).replace(/ [a-z]+$/, '')) : ''),
                    h('td', null, slot ? slot.start : ''),
                    h('td', null, slot ? slot.end : ''),
                    h('td', { class: 'num' }, slot && !slot.overtime ? fmtHours(duration) : ''),
                    h('td', { class: 'num' }, slot && slot.overtime ? fmtHours(duration) : ''),
                    h('td', { class: 'num' }, fmtHours(running)),
                    h('td', null, i === 0 ? [day.kind !== 'lavoro' ? KINDS[day.kind] : '', day.note].filter(Boolean).join(': ') : ''));
            });
        });
        document.getElementById('print').replaceChildren(
            h('h1', null, `Rendicontazione ore: ${MONTHS[state.month - 1]} ${state.year}`),
            h('p', null, targetName()),
            h('table', null,
                h('thead', null, h('tr', null, ['Data', 'Inizio', 'Fine', 'Ordinarie', 'Straord.', 'Progressivo', 'Note'].map(t => h('th', null, t)))),
                h('tbody', null, rows),
                h('tfoot', null, h('tr', null,
                    h('th', { colSpan: 3 }, 'Totale del mese'),
                    h('th', { class: 'num' }, fmtHours(data.totals.ordinary)),
                    h('th', { class: 'num' }, fmtHours(data.totals.overtime)),
                    h('th', { class: 'num' }, fmtHours(data.totals.total)),
                    h('th', null, '')))));
        window.print();
    } catch (e) {
        showError(e);
    }
}

start();
