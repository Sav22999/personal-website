/*
 * ThinkHop — admin panel: statistics, types of question and settings, questions, games and players.
 * The server checks the role on every call (require_admin in lib/bootstrap.php). Italian and English.
 */
(function () {
    'use strict';

    const E = window.ThinkHopEngine;
    const I = window.ThinkHopI18n;
    const A = window.ThinkHopAccount;
    const {h, icon, toast, modal, confirmBox, skeleton} = window.ThinkHopUI;
    const root = document.getElementById('admin');
    const LANGS = ['it', 'en', 'es', 'fr', 'de'];
    const REQUIRED = ['it', 'en'];

    const IT = {
        'Overview': 'Panoramica', 'Games': 'Giochi', 'Questions': 'Domande', 'Players': 'Giocatori', 'Back to the game': 'Torna al gioco',
        'Sign in with an administrator account.': 'Accedi con un account amministratore.', 'Sign in': 'Accedi',
        'This account is not an administrator.': 'Questo account non è amministratore.',
        'Accounts are not installed yet: open install-db.php.': 'Gli account non sono ancora installati: apri install-db.php.',
        'Games played': 'Partite giocate', 'Last 7 days': 'Ultimi 7 giorni', 'Right answers': 'Risposte giuste', 'Your questions': 'Domande tue',
        'Right answers by type': 'Risposte giuste per tipo', 'Levels: passed out of played': 'Livelli: superati su giocati', 'No data yet.': 'Ancora nessun dato.',
        'Types of question': 'Tipi di domanda', 'Switch off a type and it no longer appears in any game.': 'Un tipo spento non compare più in nessuna partita.',
        '{n} kinds of exercise': '{n} tipi di esercizio', 'Settings': 'Impostazioni', 'Questions per level': 'Domande per livello',
        'Hint cost (easy)': 'Costo suggerimento (facile)', 'Hint cost (medium)': 'Costo suggerimento (media)', 'Hint cost (hard)': 'Costo suggerimento (difficile)', 'Hint cost (expert)': 'Costo suggerimento (esperto)',
        'Save': 'Salva', 'Saved': 'Salvato', 'Cancel': 'Annulla', 'Delete': 'Elimina', 'Edit': 'Modifica',
        'Recent games': 'Partite recenti', 'Player': 'Giocatore', 'Game': 'Partita', 'Result': 'Risultato', 'Points': 'Punti', 'Spent': 'Spesi', 'Date': 'Data',
        'Delete this game?': 'Eliminare questa partita?', 'Its points and stars are removed from the player. This cannot be undone.': 'Punti e stelle vengono tolti al giocatore. Non si può annullare.',
        'New question': 'Nuova domanda', 'Edit question': 'Modifica domanda', 'Written by you': 'Scritte da te', 'Built in': 'Incluse nel gioco',
        'No questions yet: the game uses the built-in ones.': 'Ancora nessuna domanda: il gioco usa quelle incluse.',
        'Difficulty': 'Difficoltà', 'Active': 'Attiva', 'Off': 'Spenta', 'Question': 'Domanda', 'Explanation': 'Spiegazione', 'Right answer': 'Risposta giusta', 'Wrong answer {n}': 'Risposta sbagliata {n}',
        'required': 'obbligatoria', 'optional': 'facoltativa', 'They appear among the riddles, when the level reaches their difficulty.': 'Compaiono tra gli indovinelli, quando il livello arriva alla loro difficoltà.',
        'From 0.00 (super easy) to 1.99 (super hard): players only see the tier.': 'Da 0.00 (facilissima) a 1.99 (difficilissima): i giocatori vedono solo la fascia.',
        'All the exercises': 'Tutti gli esercizi', 'Search': 'Cerca', 'All types': 'Tutti i tipi', 'Type': 'Tipo', 'Exercise': 'Esercizio', 'Tier': 'Fascia', 'Username': 'Username',
        'Tables are missing: open install-db.php to create them and the first administrator.': 'Mancano le tabelle: apri install-db.php per crearle insieme al primo amministratore.', 'Open the installation': 'Apri l’installazione', 'There is no administrator yet: create the first one.': 'Non c’è ancora un amministratore: crea il primo.',
        'Delete this question?': 'Eliminare questa domanda?', 'This cannot be undone.': 'Non si può annullare.',
        'Search by name or email': 'Cerca per nome o email', 'Level': 'Livello', 'Status': 'Stato', 'Administrator': 'Amministratore', 'Disabled': 'Disattivato', 'You': 'Tu',
        'Make administrator': 'Rendi amministratore', 'Remove administrator': 'Togli amministratore', 'Disable': 'Disattiva', 'Enable': 'Riattiva',
        'Delete this player?': 'Eliminare questo giocatore?', 'The account and all its games are permanently deleted. This cannot be undone.': 'L’account e tutte le sue partite vengono eliminati per sempre. Non si può annullare.',
        'Every language needs the question, the explanation and four answers: {lang} is incomplete.': 'Ogni lingua richiede domanda, spiegazione e quattro risposte: {lang} è incompleta.',
        'The four answers must be different: check {lang}.': 'Le quattro risposte devono essere diverse: controlla {lang}.',
        'At least one type of question must stay on.': 'Almeno un tipo di domanda deve restare acceso.', 'You cannot change your own account here.': 'Non puoi modificare il tuo account da qui.',
        'You do not have access to this.': 'Non hai accesso a questa funzione.',
        'Levels': 'Livelli', 'Survival': 'Sopravvivenza', 'Daily': 'Sfida del giorno',
    };
    const t = (s, v) => {
        let out = I.getLang() === 'it' ? (IT[s] || s) : s;
        Object.entries(v || {}).forEach(([k, x]) => {
            out = out.replace('{' + k + '}', x);
        });
        return out;
    };
    const gt = I.t; // the translations of the game
    const tierName = k => gt(k);
    const when = s => s ? new Date(s.replace(' ', 'T') + 'Z').toLocaleString(I.getLang(), {dateStyle: 'short', timeStyle: 'short'}) : '–';
    const num = n => Number(n).toLocaleString(I.getLang());

    const TABS = [['overview', 'Overview', 'chart'], ['games', 'Games', 'grid'], ['questions', 'Questions', 'book'], ['players', 'Players', 'user']];
    const tabName = () => {
        const n = location.hash.replace(/^#\/?/, '');
        return TABS.some(x => x[0] === n) ? n : 'overview';
    };

    const adminError = e => {
        if (e.code === 'question_incomplete') return t('Every language needs the question, the explanation and four answers: {lang} is incomplete.', {lang: e.data.lang.toUpperCase()});
        if (e.code === 'question_repeated') return t('The four answers must be different: check {lang}.', {lang: e.data.lang.toUpperCase()});
        if (e.code === 'no_types_left') return t('At least one type of question must stay on.');
        if (e.code === 'not_yourself') return t('You cannot change your own account here.');
        if (e.code === 'forbidden') return t('You do not have access to this.');
        return e.message;
    };

    async function call(route, body, done) {
        try {
            const d = await A.api(route, body);
            if (done) await done(d);
            return d;
        } catch (e) {
            toast(adminError(e), 'bad');
            return null;
        }
    }

    const toggle = (on, label, change) => {
        const b = h('button.switch' + (on ? '.on' : ''), {
            type: 'button', role: 'switch', 'aria-checked': String(on), 'aria-label': label, onclick: () => {
                const now = !b.classList.contains('on');
                b.classList.toggle('on', now);
                b.setAttribute('aria-checked', String(now));
                change(now);
            }
        });
        return b;
    };

    const table = (heads, rows) => h('div.table-wrap', h('table', h('thead', h('tr', heads.map(x => h('th', x)))), h('tbody', rows)));
    const card = (title, ...kids) => h('section.panel', title ? h('h2', title) : null, kids);

    // ---------- overview ----------
    async function overview(view) {
        const d = await call('admin.overview');
        if (!d) return;
        const c = d.counts;
        const share = c.answers ? Math.round(c.right / c.answers * 100) + '%' : '–';
        const cats = E.CATS.filter(k => d.cats[k] && d.cats[k][1]);
        view.replaceChildren(
            h('div.stat-grid.wide',
                tile(t('Players'), num(c.players)), tile(t('Games played'), num(c.games)), tile(t('Last 7 days'), num(c.week)),
                tile(t('Right answers'), share), tile(t('Your questions'), num(c.questions))),
            h('div.admin-cols',
                card(t('Right answers by type'), cats.length ? h('div.cat-list', cats.map(k => bar(gt('cat_' + k), d.cats[k][0], d.cats[k][1]))) : h('p.muted', t('No data yet.'))),
                card(t('Levels: passed out of played'), d.stages.length ? h('div.cat-list', d.stages.map(s => bar(gt('level_n', {n: s.stage}), +s.passed, +s.played))) : h('p.muted', t('No data yet.')))));
    }

    const tile = (label, value) => h('div.stat', h('b', value), h('span', label));
    const bar = (label, a, b) => {
        const p = Math.round(a / Math.max(1, b) * 100);
        return h('div.cat-row', h('div.cat-label', h('span', label), h('b', `${p}% · ${num(a)}/${num(b)}`)), h('div.bar', h('i', {style: `width:${p}%`})));
    };

    // ---------- games: types, settings, recent games ----------
    async function gamesTab(view) {
        const [o, g] = await Promise.all([call('admin.overview'), call('admin.games')]);
        if (!o || !g) return;
        const s = o.settings;
        const saveSettings = () => call('admin.settings.save', s, d => {
            Object.assign(s, d.settings);
            toast(t('Saved'), 'ok');
        });
        const length = h('input.ctl', {type: 'number', min: 3, max: 10, value: s.levelLength});
        const costs = E.TIERS.map(k => [k, h('input.ctl', {type: 'number', min: 0, max: 5000, step: 10, value: s.hintCost[k]})]);
        const label = {easy: 'Hint cost (easy)', medium: 'Hint cost (medium)', hard: 'Hint cost (hard)', expert: 'Hint cost (expert)'};
        const modeName = x => x.mode === 'level' ? gt('level_n', {n: x.stage}) : t(x.mode === 'daily' ? 'Daily' : 'Survival');
        view.replaceChildren(
            h('div.admin-cols',
                card(t('Types of question'), h('p.muted', t('Switch off a type and it no longer appears in any game.')),
                    h('div.type-list', E.CATS.map(k => h('div.type-row',
                        h('div', h('strong', gt('cat_' + k)), h('small', t('{n} kinds of exercise', {n: E.allPatterns().filter(p => p.cat === k).length}))),
                        toggle(!s.disabledCats.includes(k), gt('cat_' + k), on => {
                            s.disabledCats = on ? s.disabledCats.filter(x => x !== k) : [...s.disabledCats, k];
                            saveSettings();
                        }))))),
                card(t('Settings'), h('form.form', {
                        onsubmit: e => {
                            e.preventDefault();
                            s.levelLength = +length.value;
                            costs.forEach(([k, el]) => {
                                s.hintCost[k] = +el.value;
                            });
                            saveSettings();
                        }
                    }, h('label.field', h('span', t('Questions per level')), length),
                    costs.map(([k, el]) => h('label.field', h('span', t(label[k])), el)),
                    h('button.btn.btn-primary', {type: 'submit'}, t('Save'))))),
            card(t('Recent games'), g.games.length ? table([t('Player'), t('Game'), t('Result'), t('Points'), t('Spent'), t('Date'), ''], g.games.map(x => {
                const row = h('tr', h('td', x.name), h('td', modeName(x)), h('td', `${x.correct}/${x.total} ${'★'.repeat(+x.stars)}`), h('td', num(x.score)), h('td', num(x.spent)), h('td', when(x.played_at)),
                    h('td', h('button.icon-btn', {
                        type: 'button', title: t('Delete'), 'aria-label': t('Delete'), onclick: async () => {
                            if (!await confirmBox(t('Delete this game?'), t('Its points and stars are removed from the player. This cannot be undone.'), t('Delete'), t('Cancel'), true)) return;
                            call('admin.game.delete', {userId: x.user_id, uid: x.uid}, () => row.remove());
                        }
                    }, icon('trash'))));
                return row;
            })) : h('p.muted', t('No data yet.'))));
    }

    // ---------- questions ----------
    const blank = () => ({id: 0, difficulty: .25, active: true, data: {}});

    function editQuestion(q, refresh) {
        const difficulty = h('input.ctl', {type: 'number', min: 0, max: E.MAX_D, step: .01, value: q.difficulty});
        let active = q.active;
        const forms = {};
        const panels = LANGS.map(l => {
            const d = q.data[l] || ['', '', ['', '', '', '']];
            const f = forms[l] = {q: h('textarea.ctl', {rows: 3, maxlength: 400}, d[0]), why: h('textarea.ctl', {rows: 2, maxlength: 400}, d[1]), options: d[2].map(v => h('input.ctl', {type: 'text', maxlength: 80, value: v}))};
            return h('fieldset.lang-set', h('legend', `${I.LANGS[l]} (${t(REQUIRED.includes(l) ? 'required' : 'optional')})`),
                h('label.field', h('span', t('Question')), f.q),
                f.options.map((el, i) => h('label.field', h('span', i ? t('Wrong answer {n}', {n: i}) : t('Right answer')), el)),
                h('label.field', h('span', t('Explanation')), f.why));
        });
        const save = h('button.btn.btn-primary', {type: 'submit'}, t('Save'));
        const m = modal([h('h3', t(q.id ? 'Edit question' : 'New question')), h('p.muted', t('They appear among the riddles, when the level reaches their difficulty.')),
            h('form.form', {
                    novalidate: true, onsubmit: async e => {
                        e.preventDefault();
                        const texts = {};
                        LANGS.forEach(l => {
                            texts[l] = {q: forms[l].q.value, why: forms[l].why.value, options: forms[l].options.map(o => o.value)};
                        });
                        save.disabled = true;
                        await call('admin.question.save', {id: q.id, difficulty: +difficulty.value, active, texts}, () => {
                            m.close();
                            toast(t('Saved'), 'ok');
                            refresh();
                        });
                        save.disabled = false;
                    }
                }, h('div.form-row', h('label.field', h('span', t('Difficulty')), difficulty, h('small.muted', t('From 0.00 (super easy) to 1.99 (super hard): players only see the tier.'))),
                    h('div.type-row', h('strong', t('Active')), toggle(active, t('Active'), on => {
                        active = on;
                    }))),
                panels, h('div.actions', h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('Cancel')), save))]);
        m.box.classList.add('wide');
    }

    async function questionsTab(view) {
        const d = await call('admin.questions');
        if (!d) return;
        const s = d.settings;
        const refresh = () => questionsTab(view);
        const lang = I.getLang() === 'it' ? 'it' : 'en';
        const custom = d.questions.map(q => h('tr',
            h('td', (q.data[lang] || q.data.en)[0]), h('td', `${(+q.difficulty).toFixed(2)} · ${tierName(q.tier)}`), h('td', Object.keys(q.data).join(' ').toUpperCase()), h('td', t(q.active ? 'Active' : 'Off')),
            h('td.row-actions',
                h('button.icon-btn', {type: 'button', title: t('Edit'), 'aria-label': t('Edit'), onclick: () => editQuestion(q, refresh)}, icon('pen')),
                h('button.icon-btn', {
                    type: 'button', title: t('Delete'), 'aria-label': t('Delete'), onclick: async () => {
                        if (await confirmBox(t('Delete this question?'), t('This cannot be undone.'), t('Delete'), t('Cancel'), true)) call('admin.question.delete', {id: q.id}, refresh);
                    }
                }, icon('trash')))));
        // Riddles show their text; a generated exercise changes every time, so it shows its name
        const riddles = Object.fromEntries((window.ThinkHopRiddles || []).map(r => [r.id, r]));
        const label = p => riddles[p.id] ? (riddles[p.id].t[lang] || riddles[p.id].t.en)[0] : p.id;
        const saveAll = () => call('admin.settings.save', s, () => toast(t('Saved'), 'ok'));
        const all = E.allPatterns().filter(p => !p.id.match(/^c\d+$/)).sort((a, b) => a.d - b.d);
        const search = h('input.ctl', {type: 'search', placeholder: t('Search'), 'aria-label': t('Search')});
        const type = h('select.ctl', {'aria-label': t('Type')}, h('option', {value: ''}, t('All types')), E.CATS.map(k => h('option', {value: k}, gt('cat_' + k))));
        const wrap = h('div');
        const draw = () => {
            const text = search.value.trim().toLowerCase();
            const list = all.filter(p => (!type.value || p.cat === type.value) && (!text || label(p).toLowerCase().includes(text)));
            wrap.replaceChildren(table([t('Exercise'), t('Type'), t('Difficulty'), t('Tier'), t('Active')], list.map(p => {
                const tierCell = h('td', tierName(E.tierOf(p.d)));
                return h('tr', h('td', label(p)), h('td', gt('cat_' + p.cat)),
                    h('td', h('input.ctl.tiny', {
                        type: 'number', min: 0, max: E.MAX_D, step: .01, value: p.d.toFixed(2), 'aria-label': t('Difficulty'), onchange: e => {
                            const v = Math.min(E.MAX_D, Math.max(0, +e.target.value || 0));
                            p.d = v;
                            s.difficulty = Object.assign({}, s.difficulty, {[p.id]: v});
                            tierCell.textContent = tierName(E.tierOf(v));
                            saveAll();
                        }
                    })), tierCell,
                    h('td', toggle(!s.disabledRiddles.includes(p.id), p.id, on => {
                        s.disabledRiddles = on ? s.disabledRiddles.filter(x => x !== p.id) : [...s.disabledRiddles, p.id];
                        saveAll();
                    })));
            })));
        };
        search.addEventListener('input', draw);
        type.addEventListener('change', draw);
        draw();
        view.replaceChildren(
            card(null, h('div.card-head', h('h2', t('Written by you')), h('button.btn.btn-primary', {type: 'button', onclick: () => editQuestion(blank(), refresh)}, t('New question'))),
                custom.length ? table([t('Question'), t('Difficulty'), 'Lang', t('Status'), ''], custom) : h('p.muted', t('No questions yet: the game uses the built-in ones.'))),
            card(null, h('div.card-head', h('h2', `${t('All the exercises')} (${all.length})`), h('div.filters', search, type)), wrap));
    }

    // ---------- players ----------
    async function playersTab(view) {
        const d = await call('admin.players');
        if (!d) return;
        const refresh = () => playersTab(view);
        const search = h('input.ctl', {type: 'search', placeholder: t('Search by name or email'), 'aria-label': t('Search by name or email')});
        const wrap = h('div');
        const draw = () => {
            const q = search.value.trim().toLowerCase();
            const list = d.players.filter(p => !q || (p.name + ' ' + p.email + ' ' + (p.username || '')).toLowerCase().includes(q));
            wrap.replaceChildren(table([t('Player'), t('Level'), t('Games'), t('Points'), t('Date'), t('Status'), ''], list.map(p => {
                const self = +p.id === +d.me, admin = +p.role >= 9, off = p.status === 'disabled';
                return h('tr',
                    h('td', h('strong', p.username ? '@' + p.username : p.name), h('small.block', `${p.name} · ${p.email}`)), h('td', String(p.stage)), h('td', num(p.games)), h('td', num(p.points)), h('td', when(p.last_login_at || p.created_at)),
                    h('td', self ? t('You') : (off ? t('Disabled') : (admin ? t('Administrator') : t('Active')))),
                    h('td.row-actions', self ? null : [
                        h('button.btn.btn-ghost.small', {type: 'button', onclick: () => call('admin.player.update', {id: p.id, admin: !admin, status: p.status}, refresh)}, t(admin ? 'Remove administrator' : 'Make administrator')),
                        h('button.btn.btn-ghost.small', {type: 'button', onclick: () => call('admin.player.update', {id: p.id, admin, status: off ? 'active' : 'disabled'}, refresh)}, t(off ? 'Enable' : 'Disable')),
                        h('button.icon-btn', {
                            type: 'button', title: t('Delete'), 'aria-label': t('Delete'), onclick: async () => {
                                if (await confirmBox(t('Delete this player?'), t('The account and all its games are permanently deleted. This cannot be undone.'), t('Delete'), t('Cancel'), true)) call('admin.player.delete', {id: p.id}, refresh);
                            }
                        }, icon('trash')),
                    ]));
            })));
        };
        search.addEventListener('input', draw);
        draw();
        view.replaceChildren(card(null, h('div.card-head', h('h2', t('Players')), search), wrap));
    }

    // ---------- shell ----------
    const VIEWS = {overview, games: gamesTab, questions: questionsTab, players: playersTab};

    function gate(text, action) {
        root.replaceChildren(h('div.gate', h('img.logo', {src: 'icons/favicon.svg', alt: ''}), h('h1', 'ThinkHop Admin'), h('p.muted', text), action,
            h('a.btn.btn-ghost', {href: './'}, t('Back to the game'))));
    }

    function render() {
        const u = A.user();
        if (A.installNeeded()) return gate(t('Tables are missing: open install-db.php to create them and the first administrator.'), h('a.btn.btn-primary', {href: 'install-db.php'}, t('Open the installation')));
        if (!A.enabled()) return gate(t('Accounts are not installed yet: open install-db.php.'), h('a.btn.btn-primary', {href: 'install-db.php'}, t('Open the installation')));
        if (A.needsAdmin()) return gate(t('There is no administrator yet: create the first one.'), h('a.btn.btn-primary', {href: 'install-db.php'}, t('Open the installation')));
        if (!u) return gate(t('Sign in with an administrator account.'), h('button.btn.btn-primary', {type: 'button', onclick: A.open}, t('Sign in')));
        if (!u.isAdmin) return gate(t('This account is not an administrator.'), h('button.btn.btn-soft', {type: 'button', onclick: A.open}, u.name));
        const tab = tabName();
        const view = h('main.admin-view');
        root.replaceChildren(h('div.admin',
            h('header.admin-bar', h('a.brand', {href: './'}, h('img', {src: 'icons/favicon.svg', alt: ''}), h('strong', 'ThinkHop Admin')),
                h('nav', TABS.map(([k, label, ic]) => h('a' + (k === tab ? '.sel' : ''), {href: '#/' + k, 'aria-current': k === tab ? 'page' : null}, icon(ic), h('span', t(label))))),
                h('div.seg.lang-switch', ['it', 'en'].map(l => h('button' + (I.getLang() === l ? '.sel' : ''), {
                    type: 'button', onclick: () => {
                        I.setLang(l);
                        render();
                    }
                }, l.toUpperCase()))),
                h('button.icon-btn', {type: 'button', title: u.name, 'aria-label': gt('account'), onclick: A.open}, icon('user'))),
            view));
        document.title = `${t(TABS.find(x => x[0] === tab)[1])} · ThinkHop Admin`;
        const shape = {overview: [skeleton('tiles', 5), h('div.admin-cols', h('section.panel', skeleton('rows', 6)), h('section.panel', skeleton('rows', 6)))], players: [h('section.panel', skeleton('table', 8))]}[tab]
            || [h('section.panel', skeleton('table', 8))];
        view.replaceChildren(...shape);
        // If the call fails the error is already in a toast: the shapes must not stay forever
        Promise.resolve(VIEWS[tab](view)).then(() => {
            if (view.querySelector('.skeleton') && !view.querySelector('.skeleton').closest('.modal')) view.replaceChildren(h('p.muted.center', '–'));
        });
    }

    window.addEventListener('hashchange', render);
    if (!['it', 'en'].includes(I.getLang())) I.setLang('en');
    root.replaceChildren(h('div.admin-loading', skeleton('table', 6)));
    A.start(render);
})();
