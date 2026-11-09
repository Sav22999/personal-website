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
        'Adjust probability (0.0 – 1.0) compared to other exercises of the same difficulty. Switch off a type to exclude it completely.': 'Regola la probabilità di comparsa (0.0 – 1.0) comparata agli altri esercizi della stessa difficoltà. Spegni un tipo per escluderlo del tutto.',
        'Probability (0.0 – 1.0)': 'Probabilità (0.0 – 1.0)',
        'Probability (0.0 – 1.0) compared to other exercises of the same difficulty': 'Probabilità (0.0 – 1.0) comparata agli altri esercizi della stessa difficoltà',
        'Probability': 'Probabilità',
        '{n} kinds of exercise': '{n} tipi di esercizio', 'Settings': 'Impostazioni', 'Questions per level': 'Domande per livello',
        'Hint cost (easy)': 'Costo suggerimento (facile)', 'Hint cost (medium)': 'Costo suggerimento (media)', 'Hint cost (hard)': 'Costo suggerimento (difficile)', 'Hint cost (expert)': 'Costo suggerimento (esperto)',
        'Hint cost': 'Costo suggerimento',
        'Save': 'Salva', 'Saved': 'Salvato', 'Cancel': 'Annulla', 'Delete': 'Elimina', 'Edit': 'Modifica',
        'Recent games': 'Partite recenti', 'Player': 'Giocatore', 'Game': 'Partita', 'Result': 'Risultato', 'Points': 'Punti', 'Spent': 'Spesi', 'Date': 'Data',
        'Delete this game?': 'Eliminare questa partita?', 'Its points and stars are removed from the player. This cannot be undone.': 'Punti e stelle vengono tolti al giocatore. Non si può annullare.',
        'New question': 'Nuova domanda', 'Edit question': 'Modifica domanda', 'Written by you': 'Scritte da te', 'Built in': 'Incluse nel gioco',
        'No questions yet: the game uses the built-in ones.': 'Ancora nessuna domanda: il gioco usa quelle incluse.',
        'Difficulty': 'Difficoltà', 'Active': 'Attiva', 'Off': 'Spenta', 'Question': 'Domanda', 'Explanation': 'Spiegazione', 'Right answer': 'Risposta giusta', 'Wrong answer {n}': 'Risposta sbagliata {n}',
        'required': 'obbligatoria', 'optional': 'facoltativa', 'They appear among the riddles, when the level reaches their difficulty.': 'Compaiono tra gli indovinelli, quando il livello arriva alla loro difficoltà.',
        'From 0.00 (super easy) to 9.99 (super hard): players only see the tier.': 'Da 0.00 (facilissima) a 9.99 (difficilissima): i giocatori vedono solo la fascia.',
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
        'Details': 'Dettagli', 'Activity': 'Attività', 'Read only: what this player did.': 'Sola lettura: ciò che ha fatto questo giocatore.',
        'Wallet': 'Punti disponibili', 'Earned': 'Guadagnati', 'Changed by admins': 'Modificati dagli admin', 'Levels passed': 'Livelli superati', 'Highest level': 'Livello più alto',
        'Daily challenges': 'Sfide del giorno', 'Play time': 'Tempo di gioco', 'Best daily': 'Migliore sfida', 'Best survival': 'Migliore sopravvivenza',
        'Following': 'Segue', 'Followers': 'Lo seguono', 'Notification devices': 'Dispositivi con notifiche',
        'Signed up': 'Iscritto', 'Last sign-in': 'Ultimo accesso', 'First game': 'Prima partita', 'Last game': 'Ultima partita',
        'Email confirmed': 'Email confermata', 'Email not confirmed': 'Email da confermare', 'Google': 'Google', 'Password': 'Password', 'In the ranking': 'In classifica', 'Hidden from the ranking': 'Nascosto dalla classifica',
        'Change points': 'Modifica punti', 'New balance': 'Nuovo saldo', 'Note (optional)': 'Nota (facoltativa)', 'The games stay as they were played: only the balance changes.': 'Le partite restano come sono state giocate: cambia solo il saldo.',
        'Points saved': 'Punti salvati', 'Last 100 games': 'Ultime 100 partite', 'Admin changes': 'Modifiche degli admin', 'No changes.': 'Nessuna modifica.', 'Mode': 'Modalità', 'Time': 'Tempo', 'Admin': 'Admin', 'Change': 'Modifica', 'Note': 'Nota',
        'You cannot change your own points.': 'Non puoi modificare i tuoi punti.', 'Never': 'Mai',
        'Profile pictures': 'Immagini profilo', 'Characters': 'Personaggi', 'Accessories': 'Accessori', 'Frames': 'Cornici', 'Backgrounds': 'Sfondi',
        'Name': 'Nome', 'Price (points)': 'Prezzo (punti)', 'On sale': 'In vendita', 'Sold': 'Venduti',
        'Players buy these with their points from their profile. At 0 an item is free for everyone. An item taken off sale stays with whoever already bought it.': 'I giocatori li comprano con i punti dal profilo. A 0 un elemento è gratis per tutti. Un elemento tolto dalla vendita resta a chi lo aveva già comprato.',
        'Spent on profile': 'Spesi nel profilo',
        'Try': 'Prova', 'Tutorial': 'Tutorial', 'Try exercise': 'Prova esercizio', 'New example': 'Nuovo esempio',
        'All variants': 'Tutte le varianti', 'Test hint': 'Prova suggerimento', 'Show answer': 'Mostra soluzione', 'Try another': 'Altro esercizio',
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

    const TABS = [['overview', 'Overview', 'chart'], ['games', 'Games', 'grid'], ['questions', 'Questions', 'book'], ['players', 'Players', 'user'], ['avatars', 'Profile pictures', 'image']];
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
            const detail = (e.data && e.data.detail) ? (e.data.detail + (e.data.file ? ' (' + e.data.file + ')' : '')) : adminError(e);
            toast(detail, 'bad');
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

    /** Every cell knows its column: on a phone each row becomes a card with labels (admin.css, "Phones"). */
    function table(heads, rows) {
        const body = h('tbody', rows);
        [...body.rows].forEach(tr => [...tr.cells].forEach((td, i) => {
            if (heads[i]) td.dataset.label = heads[i];
        }));
        return h('div.table-wrap', h('table', h('thead', h('tr', heads.map(x => h('th', x)))), body));
    }
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

    // ---------- tutorial & interactive exercise preview ----------
    const RIDDLES = Object.fromEntries((window.ThinkHopRiddles || []).map(r => [r.id, r]));
    const word = w => I.getLang() === 'de' && E.WORDS.includes(w) ? w[0].toUpperCase() + w.slice(1) : w;
    const line = l => {
        const v = {};
        Object.entries(l.v || {}).forEach(([k, x]) => {
            v[k] = x && typeof x === 'object' ? line(x) : word(x);
        });
        return gt(l.k, v);
    };
    const riddleText = q => {
        const r = q.t ? q : RIDDLES[q.id];
        return (r && r.t && (r.t[I.getLang()] || r.t.en)) || ['', '', ['', '', '', '']];
    };
    const hasOrigamiGuidelines = q => {
        if (q && q.origamiData && q.origamiData.noGuides !== undefined) {
            return !q.origamiData.noGuides;
        }
        if (q && q.level && E.TIERS) {
            const idx = E.TIERS.indexOf(q.level);
            const expIdx = E.TIERS.indexOf('expert');
            if (idx >= 0 && expIdx >= 0) return idx <= expIdx;
        }
        if (q && q.d !== undefined) return q.d < 7.5;
        return true;
    };

    const optionText = (q, o) => {
        if (q.view === 'origami' && q.origamiData) {
            const sheet = q.origamiData.candidateSheets.find(s => s.letter === o);
            if (sheet) {
                const noGuides = !hasOrigamiGuidelines(q);
                const size = q.origamiData.size || 4;
                const sizeClass = size === 8 ? '.size-8' : (size === 6 ? '.size-6' : '.size-4');
                const cells = sheet.pattern.split('').map(ch => h('span.origami-mini-cell' + (ch === '1' ? '.hole' : '')));
                return h('div.origami-opt-box',
                    h('span.origami-opt-letter', String(o)),
                    h('div.origami-mini-grid' + sizeClass + (noGuides ? '.no-guides' : ''), cells));
            }
        }
        if (typeof o === 'string') return o;
        if (o.k) return gt(o.k);
        return riddleText(q)[2][o.r];
    };
    const whyLines = q => q.why.map(w => {
        if (w.lit) return h('p.why-lit', w.lit);
        if (w.riddle) return h('p', riddleText(q)[1]);
        const v = Object.assign({}, w.v);
        if (w.prop) v.prop = gt(w.prop.k, w.prop.v);
        return h('p', line({k: w.k, v}));
    });
    const promptText = q => {
        if (q.pk) return line({k: q.pk, v: q.pv});
        if (q.cat === 'sign') return gt(q.nums.length > 2 ? 'q_sign2' : 'q_sign');
        if (q.cat === 'riddle') return riddleText(q)[0];
        return gt('q_' + q.cat);
    };
    const equationView = (q, answered) => {
        const signs = answered ? String(optionText(q, q.options[q.answer])).split(/\s+/) : [];
        const parts = [];
        q.nums.forEach((n, i) => {
            if (i) parts.push(h('span.gap' + (answered ? '.filled' : ''), signs[i - 1] || '?'));
            parts.push(h('span', String(n)));
        });
        return h('div.equation', parts, h('span', '='), h('span', String(q.result)));
    };
    const gridView = (q, answered) => {
        const right = optionText(q, q.options[q.answer]);
        const n = q.grid.length;
        const edge = (i, size) => size && (i + 1) % size === 0 && i + 1 < n;
        return h('div.grid-wrap',
            h('div.board', {style: `--n:${n}`}, q.grid.flatMap((row, y) => row.map((cell, x) => {
                const ask = cell === '?';
                return h('span.cell' + (ask ? '.ask' : '') + (q.boxes && edge(x, q.boxes[1]) ? '.edge-r' : '') + (q.boxes && edge(y, q.boxes[0]) ? '.edge-b' : ''), ask && answered ? right : cell);
            }))),
            q.lines ? h('div.path', q.lines) : null);
    };
    const GEMS_LIST = ['💎', '🔮', '🪙', '⭐'];

    const gearsView = (q, answered) => {
        const d = q.gearsData;
        if (!d) return q.lines ? h('div.lines', q.lines.map(l => h('div', l)), h('div.ask', q.ask)) : null;
        const n = d.count;
        const width = Math.min(340, Math.max(220, n * 55));
        const height = d.links.some(([a, b]) => a === 0 && (b === 1 || b === 2) && n > 3) ? 140 : 100;
        const coords = [];
        if (d.isRing) {
            const rad = 36, cx = width / 2, cy = height / 2;
            for (let i = 0; i < n; i++) {
                const ang = (i / n) * Math.PI * 2 - Math.PI / 2;
                coords.push({x: cx + Math.cos(ang) * rad, y: cy + Math.sin(ang) * rad, r: 24, teeth: 10});
            }
        } else if (n === 2) {
            coords.push({x: 80, y: 50, r: 34, teeth: 14}, {x: 160, y: 50, r: 44, teeth: 18});
        } else if (d.links.some(([a, b]) => a === 0 && b === 2)) {
            coords.push({x: 55, y: 65, r: 26, teeth: 10});
            coords.push({x: 110, y: 35, r: 24, teeth: 10});
            coords.push({x: 110, y: 95, r: 24, teeth: 10});
            for (let i = 3; i < n; i++) coords.push({x: 110 + (i - 2) * 50, y: 65, r: 24, teeth: 10});
        } else {
            const step = (width - 60) / (n - 1);
            for (let i = 0; i < n; i++) coords.push({x: 30 + i * step, y: 50, r: 24, teeth: 10});
        }
        const names = ['A', 'B', 'C', 'D', 'E', 'F'];
        const cogSvg = coords.map((c, i) => {
            const isStart = (i === d.startIdx);
            const isTarget = (i === d.targetIdx);
            const dir = d.dirs[i];
            let spin = '';
            if (!d.isJammed && dir) {
                if (answered) {
                    spin = dir === 'cw' ? 'spin-cw' : 'spin-ccw';
                } else if (isStart) {
                    spin = dir === 'cw' ? 'spin-loop-cw' : 'spin-loop-ccw';
                }
            }
            const isOk = isTarget && answered;
            const pts = [];
            const steps = c.teeth * 2;
            for (let s = 0; s < steps; s++) {
                const ang = (s / steps) * Math.PI * 2;
                const rad = (s % 2 === 0) ? c.r : c.r - 5;
                pts.push((s === 0 ? 'M' : 'L') + (c.x + Math.cos(ang) * rad).toFixed(1) + ' ' + (c.y + Math.sin(ang) * rad).toFixed(1));
            }
            pts.push('Z');
            const gearFill = isOk ? 'var(--ok-bg)' : (isTarget ? 'var(--primary-soft)' : (isStart ? '#FFD23F' : 'var(--surface)'));
            const gearStroke = isOk ? 'var(--ok)' : (isTarget ? 'var(--primary)' : 'var(--line)');
            const label = names[i];
            const dirSym = isStart ? (d.startDir === 'cw' ? '↻' : '↺') : (isOk ? (dir === 'cw' ? '↻' : '↺') : (isTarget ? '?' : ''));
            return `<g class="gear-cog${isTarget ? ' target-gear' : ''}${isOk ? ' ok' : ''}">
                <path class="gear-teeth ${spin}" d="${pts.join(' ')}" fill="${gearFill}" stroke="${gearStroke}" stroke-width="2" style="transform-origin:${c.x}px ${c.y}px"/>
                <circle class="gear-hub" cx="${c.x}" cy="${c.y}" r="${c.r * 0.42}" fill="var(--surface-2)" stroke="${gearStroke}" stroke-width="1.5"/>
                <text class="gear-label" x="${c.x}" y="${c.y + 4}" font-size="12" font-weight="900" text-anchor="middle" fill="var(--text)">${label}</text>
                ${dirSym ? `<text class="gear-dir-sym" x="${c.x}" y="${c.y - c.r - 4}" font-size="13" font-weight="900" text-anchor="middle" fill="${gearStroke}">${dirSym}</text>` : ''}
            </g>`;
        }).join('');
        const statusChips = coords.map((c, i) => {
            const isStart = (i === d.startIdx);
            const isTarget = (i === d.targetIdx);
            const dir = d.dirs[i];
            const cls = (isTarget && answered) ? '.ok.pop' : (isTarget ? '.focus' : (isStart ? '.focus' : ''));
            const sym = isStart ? (d.startDir === 'cw' ? '↻' : '↺') : (answered ? (dir === 'cw' ? '↻' : '↺') : (isTarget ? '?' : ''));
            return h('span.h-chip' + cls, `${names[i]}: ${sym || '?'}`);
        });
        return h('div.gears-view',
            h('div.gears-svg-wrap', {html: `<svg class="gears-svg" viewBox="0 0 ${width} ${height}" aria-hidden="true">${cogSvg}</svg>`}),
            h('div.h-row', statusChips));
    };

    const lasersView = (q, answered) => {
        const d = q.lasersData;
        if (!d) return gridView(q, answered);
        const n = d.size;
        const right = optionText(q, q.options[q.answer]);
        const gap = 4;
        const cellSize = Math.max(38, Math.min(46, Math.floor((270 - (n - 1) * gap) / n)));
        const gridDim = n * cellSize + (n - 1) * gap;
        let beamOverlay = null;
        if (answered && d.path && d.path.length > 1) {
            const pts = d.path.map(([x, y]) => {
                const px = x * (cellSize + gap) + cellSize / 2;
                const py = y * (cellSize + gap) + cellSize / 2;
                return `${px.toFixed(1)},${py.toFixed(1)}`;
            }).join(' ');
            beamOverlay = h('svg.laser-beam-svg', {
                viewBox: `0 0 ${gridDim} ${gridDim}`,
                style: `width:${gridDim}px;height:${gridDim}px`
            }, h('polyline.laser-beam-line', {points: pts}));
        }
        const mirrorSvg = (window.ThinkHopUI && window.ThinkHopUI.mirrorSvg) || ((t, r) => `<svg class="mirror-svg" viewBox="0 0 36 36"><line x1="6" y1="${t==='/'?30:6}" x2="30" y2="${t==='/'?6:30}" stroke="#38BDF8" stroke-width="4.5"/></svg>`);

        const dirX = (d.path && d.path.length > 1) ? (d.path[1][0] - d.startX) : 1;
        const dirY = (d.path && d.path.length > 1) ? (d.path[1][1] - d.startY) : 0;
        const dirCls = (dirX === 1) ? '.dir-right' : ((dirX === -1) ? '.dir-left' : ((dirY === 1) ? '.dir-down' : '.dir-up'));

        const gridCells = d.grid.flatMap((row, y) => row.map((cell, x) => {
            const isEmitter = (x === d.startX && y === d.startY);
            const isSlash = (cell === '/');
            const isBslash = (cell === '\\');
            const isTarget = GEMS_LIST.includes(cell);
            const isHit = isTarget && answered && cell === right;
            const isReflecting = answered && (isSlash || isBslash) && d.path && d.path.some(([px, py]) => px === x && py === y);
            const cls = (isEmitter ? ('.emitter' + dirCls) : '') +
                (isSlash ? '.mirror-slash' : '') +
                (isBslash ? '.mirror-bslash' : '') +
                (isReflecting ? '.reflecting' : '') +
                (isTarget ? '.target-gem' : '') +
                (isHit ? '.hit' : '');
            if (isSlash || isBslash) {
                return h('div.laser-cell' + cls, {html: mirrorSvg(cell, isReflecting)});
            }
            if (isEmitter) {
                return h('div.laser-cell' + cls, h('span.laser-torch', cell));
            }
            return h('div.laser-cell' + cls, cell);
        }));
        return h('div.lasers-view',
            h('div.laser-grid-wrap',
                beamOverlay,
                h('div.laser-grid', {
                    style: `width:${gridDim}px;height:${gridDim}px;grid-template-columns:repeat(${n},${cellSize}px);grid-template-rows:repeat(${n},${cellSize}px);gap:${gap}px`
                }, gridCells)));
    };

    const cubesView = (q, answered) => {
        const d = q.cubesData;
        if (!d) return gridView(q, answered);
        const rows = d.netGrid.map(row => h('div.cube-net-row', row.map(cell => {
            if (!cell) return h('span.cube-net-empty');
            const isTarget = (cell === d.targetIcon);
            const isOk = answered && (cell === d.rightIcon);
            const cls = (isOk ? '.ok.pop' : (isTarget ? '.target' : ''));
            return h('span.cube-net-cell' + cls, cell);
        })));
        return h('div.cubes-view',
            h('div.cube-net-board', rows),
            q.lines && !d.isViewMode ? h('div.path', q.lines) : null);
    };

    const pipesView = (q, answered) => {
        const d = q.pipesData;
        if (!d) return q.lines ? h('div.lines', q.lines.map(l => h('div', l)), h('div.ask', q.ask)) : null;
        const right = optionText(q, q.options[q.answer]);
        const tankCards = d.tanks.map((name, i) => {
            const isWinner = (name === right);
            const isFilled = answered && isWinner;
            const isSource = (i === 0);
            return h('div.pipe-tank-card' + (isFilled ? '.winner-fill' : '') + (isSource ? '.source' : ''),
                isSource ? h('span.pipe-tank-faucet', '🚰') : null,
                h('span.water-level'),
                h('span.pipe-tank-lbl', name),
                h('span.tank-water-icon', isFilled ? '💧💧' : (isSource ? '💧' : '')));
        });
        const valveChips = d.connections.map(c => {
            return h('span.valve-chip' + (c.blocked ? '.blocked' : '.ok'),
                `${c.from} ➔ ${c.to}`,
                h('b', c.blocked ? ' 🚫' : ' ✓'));
        });
        return h('div.pipes-view',
            h('div.pipes-stage', tankCards),
            h('div.h-row', valveChips));
    };

    const scalesView = (q, answered) => {
        const right = optionText(q, q.options[q.answer]);
        const parseScale = str => {
            if (str.includes('⚖️')) {
                const [l, r] = str.split('⚖️').map(s => s.trim());
                return {l, r, op: '⚖️', tilt: ''};
            }
            if (str.includes('>')) {
                const [l, r] = str.split('>').map(s => s.trim());
                return {l, r, op: '>', tilt: '.tilt-left'};
            }
            if (str.includes('<')) {
                const [l, r] = str.split('<').map(s => s.trim());
                return {l, r, op: '<', tilt: '.tilt-right'};
            }
            return null;
        };
        const scaleRows = (q.lines || []).map(lineStr => {
            const parsed = parseScale(lineStr);
            if (!parsed) return h('div.h-chip', lineStr);
            return h('div.scale-card',
                h('div.scale-visual',
                    h('div.scale-beam-bar' + parsed.tilt,
                        h('div.scale-pan-wrap.left',
                            h('div.scale-pan-string'),
                            h('div.scale-pan-dish', h('span.scale-items', parsed.l))),
                        h('div.scale-pan-wrap.right',
                            h('div.scale-pan-string'),
                            h('div.scale-pan-dish', h('span.scale-items', parsed.r)))),
                    h('div.scale-pivot')));
        });
        if (q.ask) {
            const parsedAsk = parseScale(q.ask);
            if (parsedAsk) {
                const rightSide = answered ? right : '?';
                scaleRows.push(h('div.scale-card.focus',
                    h('div.scale-visual',
                        h('div.scale-beam-bar',
                            h('div.scale-pan-wrap.left',
                                h('div.scale-pan-string'),
                                h('div.scale-pan-dish', h('span.scale-items', parsedAsk.l))),
                            h('div.scale-pan-wrap.right',
                                h('div.scale-pan-string'),
                                h('div.scale-pan-dish' + (answered ? '.ok.pop' : '.focus'), h('span.scale-items', rightSide)))),
                        h('div.scale-pivot'))));
            }
        }
        return h('div.scales-view', scaleRows);
    };

    const cupsView = (q, answered) => {
        const right = optionText(q, q.options[q.answer]);
        const startItems = (q.lines && q.lines[0]) ? q.lines[0].split(/\s+/).filter(Boolean) : [];
        const moves = (q.lines && q.lines[1]) ? q.lines[1].split(/\s*·\s*/).filter(Boolean) : [];
        const endItems = (q.lines && q.lines[2]) ? q.lines[2].split(/\s+/).filter(Boolean) : [];

        const isBack = (q.pk === 'q_cups_back');
        const targetPos = (q.pv && q.pv.p !== undefined) ? parseInt(q.pv.p, 10) : null;
        const targetItem = (q.pv && q.pv.x !== undefined) ? q.pv.x : null;

        const revealedStart = (isBack && answered && right) ? right.split(/\s+/) : null;

        const startCards = startItems.map((item, i) => {
            const displayItem = revealedStart ? (revealedStart[i] || item) : item;
            const isTarget = (targetPos !== null && i + 1 === targetPos) || (targetItem !== null && item === targetItem);
            const isOk = answered && isTarget;
            return h('div.cup-card' + (isOk ? '.ok.pop' : (isTarget ? '.target-cup' : '')),
                h('span.cup-idx', '#' + (i + 1)),
                h('span.cup-item', displayItem));
        });

        const moveChips = moves.map(m => {
            const isMissingSlot = m.includes('?');
            const displayText = (isMissingSlot && answered) ? right : m;
            return h('span.h-chip' + (isMissingSlot ? (answered ? '.ok.pop' : '.target-chip') : '.focus'), displayText);
        });

        const rows = [
            h('div.cups-row', startCards),
            moveChips.length ? h('div.cup-moves-path', moveChips) : null,
        ];

        if (endItems.length > 0) {
            const endCards = endItems.map((item, i) => {
                return h('div.cup-card',
                    h('span.cup-idx', '#' + (i + 1)),
                    h('span.cup-item', item));
            });
            rows.push(h('div.cups-row.end-row', endCards));
        } else if (answered && q.pk === 'q_cups_row' && right) {
            const rightItems = right.split(/\s+/);
            const resCards = rightItems.map((item, i) => {
                return h('div.cup-card.ok',
                    h('span.cup-idx', '#' + (i + 1)),
                    h('span.cup-item', item));
            });
            rows.push(h('div.cups-row.end-row', resCards));
        }

        return h('div.cups-view', rows.filter(Boolean));
    };

    const locksView = (q, answered) => {
        const right = optionText(q, q.options[q.answer]);
        let dialCount = 3;
        const rows = (q.lines || []).map(lineStr => {
            const parts = lineStr.split(/\s*(?:→|➔|·)\s*/);
            const rawDials = parts[0] ? parts[0].trim() : '';
            const dials = rawDials.includes(' ')
                ? rawDials.split(/\s+/).filter(Boolean)
                : Array.from(rawDials);
            if (dials.length) dialCount = dials.length;
            const feedback = parts[1] || lineStr;
            const matchTarget = feedback.match(/(\d+)\s*🎯/);
            const matchSpin = feedback.match(/(\d+)\s*🔄/);
            const targets = matchTarget ? matchTarget[1] : '0';
            const spins = matchSpin ? matchSpin[1] : '0';
            return h('div.lock-attempt-row',
                h('div.lock-dials', dials.map(d => h('span.lock-dial', d))),
                h('div.lock-feedback-badges',
                    h('span.lock-badge.bullseye', `${targets} 🎯`),
                    h('span.lock-badge.spin', `${spins} 🔄`)));
        });
        const rightStr = right ? String(right).trim() : '';
        const secretDials = (answered && rightStr)
            ? (rightStr.includes(' ') ? rightStr.split(/\s+/).filter(Boolean) : Array.from(rightStr))
            : Array.from({length: dialCount}, () => '?');
        rows.push(h('div.lock-attempt-row.focus',
            h('div.lock-dials', secretDials.map(d => h('span.lock-dial' + (answered ? '.ok.pop' : ''), d))),
            h('span.lock-badge' + (answered ? '.bullseye' : ''), answered ? '🔓 OK' : '🔒')));
        return h('div.locks-view', rows);
    };

    const seatsView = (q, answered) => {
        const right = optionText(q, q.options[q.answer]);
        const numSeats = q.pv && q.pv.n ? q.pv.n : 4;
        const rad = 83;
        const center = 120;

        let targetSeat = (q.pv && q.pv.targetSeat) || (q.ask && q.ask.v && (q.ask.v.targetSeat || q.ask.v.n)) || null;
        if (!targetSeat && q.ask && q.ask.k === 'st_ask_opp' && q.ask.v && q.ask.v.a && q.why && q.why[0] && q.why[0].v && q.why[0].v.x) {
            const pairs = q.why[0].v.x.split(/\s*·\s*/).map(s => s.split(':'));
            const oppPair = pairs.find(p => p[1] === q.ask.v.a);
            if (oppPair) {
                const oppSeat = parseInt(oppPair[0], 10) - 1;
                targetSeat = ((oppSeat + numSeats / 2) % numSeats) + 1;
            }
        }

        const isBench = q.pk === 'q_seats_bench';

        const renderChair = (i, x, y) => {
            const isAsk = targetSeat !== null ? (i + 1 === targetSeat) : false;
            const isOk = isAsk && answered;
            const attrs = (x !== undefined && y !== undefined) ? {style: `left:${x}px;top:${y}px`} : {};
            return h('div.chair-spot' + (isOk ? '.ok.pop' : (isAsk ? '.ask-chair' : '')), attrs,
                h('span.chair-num', String(i + 1)),
                h('span.chair-who', isOk ? right : (isAsk ? '?' : '🪑')));
        };

        let seatingGraphic = null;
        if (isBench) {
            const benchSpots = Array.from({length: numSeats}, (_, i) => renderChair(i));
            seatingGraphic = h('div.bench-wrap', benchSpots);
        } else {
            const chairs = Array.from({length: numSeats}, (_, i) => {
                const ang = (i / numSeats) * Math.PI * 2 - Math.PI / 2;
                const x = Math.round(center + Math.cos(ang) * rad - 25);
                const y = Math.round(center + Math.sin(ang) * rad - 25);
                return renderChair(i, x, y);
            });
            seatingGraphic = h('div.round-table-wrap',
                h('div.round-table-center', '🍽️'),
                chairs);
        }

        return h('div.seats-view',
            seatingGraphic,
            q.facts ? h('div.facts',
                h('ul', q.facts.map(f => h('li', line(f)))),
                q.ask ? h('p.ask', line(q.ask)) : null) : null);
    };

    const boxesView = (q, answered) => {
        const right = optionText(q, q.options[q.answer]);
        const chests = (q.lines || []).map(lineStr => {
            const isWinner = lineStr.includes(right);
            const isOpened = answered && isWinner;
            return h('div.chest-card' + (isOpened ? '.winner-open' : ''),
                h('span.chest-icon', isOpened ? '💎' : '🧰'),
                h('span.chest-label', lineStr));
        });
        return h('div.boxes-view', chests);
    };

    const lampsView = (q, answered) => {
        if (!q.lines || !q.lines.length) return null;
        const startLine = q.lines[0];
        const buttonLines = q.lines.slice(1);
        const isBack = startLine.includes('→');

        return h('div.lamps-view',
            h('div.lamps-start-card',
                h('span.lamps-badge', isBack ? gt('lamps_trans') : gt('lamps_start')),
                h('div.lamps-state-row', startLine)),
            buttonLines.length ? h('div.lamps-buttons-wrap',
                h('div.lamps-buttons-grid', buttonLines.map(b => {
                    const parts = b.split(/\s*→\s*/);
                    const letter = parts[0] || '';
                    const nums = parts[1] || '';
                    return h('div.lamp-btn-chip',
                        h('span.lamp-btn-letter', letter),
                        h('span.lamp-btn-arrow', '➔'),
                        h('span.lamp-btn-nums', nums));
                }))) : null,
            q.ask ? h('div.lamps-ask-card',
                h('span.lamps-ask-seq', q.ask)) : null);
    };

    const mobilesView = (q, answered) => {
        const d = q.mobilesData;
        if (!d) return null;
        const right = optionText(q, q.options[q.answer]);
        const width = 310;
        const height = d.beams.length > 1 ? 165 : 125;
        const svgParts = [];
        const topX = d.beams[0] ? d.beams[0].x : 155;
        const topY = d.beams[0] ? d.beams[0].y : 32;
        svgParts.push(`<circle cx="${topX}" cy="5" r="4" fill="var(--text)"/>`);
        svgParts.push(`<line x1="${topX}" y1="5" x2="${topX}" y2="${topY}" stroke="var(--text)" stroke-width="2.5" stroke-linecap="round"/>`);

        const renderWeight = (x, y, w) => {
            const wy = y + 26;
            const isTarget = w.isTarget;
            const isOk = isTarget && answered;
            const valStr = isOk ? right : (isTarget ? '?' : String(w.val));
            const bgFill = isOk ? 'var(--ok-bg)' : (isTarget ? '#FFD23F' : 'var(--primary-soft)');
            const strokeCol = isOk ? 'var(--ok)' : (isTarget ? '#141127' : 'var(--line)');
            const textCol = isOk ? 'var(--ok)' : (isTarget ? '#141127' : 'var(--on-primary-soft)');

            return `<g class="mobile-weight-group${isTarget ? ' target-node' : ''}${isOk ? ' ok' : ''}">
                <line x1="${x}" y1="${y}" x2="${x}" y2="${wy}" stroke="var(--text)" stroke-width="1.8"/>
                <rect class="mobile-weight-node" x="${x - 16}" y="${wy}" width="32" height="26" rx="7" fill="${bgFill}" stroke="${strokeCol}" stroke-width="2"/>
                <text x="${x}" y="${wy + 18}" font-size="13" font-weight="900" text-anchor="middle" fill="${textCol}">${valStr}</text>
            </g>`;
        };

        d.beams.forEach(b => {
            const armScale = 32;
            const xL = b.x - b.dL * armScale;
            const xR = b.x + b.dR * armScale;
            const y = b.y;
            svgParts.push(`<circle cx="${b.x}" cy="${y}" r="4" fill="var(--text)"/>`);
            svgParts.push(`<line x1="${xL}" y1="${y}" x2="${xR}" y2="${y}" stroke="var(--text)" stroke-width="4" stroke-linecap="round"/>`);
            for (let i = 1; i <= b.dL; i++) {
                const tx = b.x - i * armScale;
                svgParts.push(`<line x1="${tx}" y1="${y - 4}" x2="${tx}" y2="${y + 4}" stroke="var(--muted)" stroke-width="1.5"/>`);
            }
            for (let i = 1; i <= b.dR; i++) {
                const tx = b.x + i * armScale;
                svgParts.push(`<line x1="${tx}" y1="${y - 4}" x2="${tx}" y2="${y + 4}" stroke="var(--muted)" stroke-width="1.5"/>`);
            }
            svgParts.push(`<text x="${(b.x + xL) / 2}" y="${y - 6}" font-size="10" font-weight="900" text-anchor="middle" fill="var(--muted)">×${b.dL}</text>`);
            svgParts.push(`<text x="${(b.x + xR) / 2}" y="${y - 6}" font-size="10" font-weight="900" text-anchor="middle" fill="var(--muted)">×${b.dR}</text>`);

            if (b.left.type === 'weight') {
                svgParts.push(renderWeight(xL, y, b.left));
            } else if (b.left.type === 'beam') {
                const subB = d.beams[b.left.subBeamIdx];
                if (subB) {
                    svgParts.push(`<line x1="${xL}" y1="${y}" x2="${subB.x}" y2="${subB.y}" stroke="var(--text)" stroke-width="2" stroke-dasharray="3 2"/>`);
                }
            }

            if (b.right.type === 'weight') {
                svgParts.push(renderWeight(xR, y, b.right));
            } else if (b.right.type === 'beam') {
                const subB = d.beams[b.right.subBeamIdx];
                if (subB) {
                    svgParts.push(`<line x1="${xR}" y1="${y}" x2="${subB.x}" y2="${subB.y}" stroke="var(--text)" stroke-width="2" stroke-dasharray="3 2"/>`);
                }
            }
        });

        const svgEl = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svgEl.setAttribute('viewBox', `0 0 ${width} ${height}`);
        svgEl.setAttribute('class', 'mobiles-diagram' + (answered ? ' balanced' : ''));
        svgEl.innerHTML = svgParts.join('');

        return h('div.mobiles-view',
            svgEl,
            answered ? h('div.h-row',
                h('span.h-chip.ok', '✓ ? = ' + right)) : null);
    };

    const computeOrigamiBounds = (folds, size = 4) => {
        let b = { xMin: 0, xMax: size - 1, yMin: 0, yMax: size - 1 };
        const steps = [];
        for (const f of folds) {
            const bBefore = { xMin: b.xMin, xMax: b.xMax, yMin: b.yMin, yMax: b.yMax, cols: b.xMax - b.xMin + 1, rows: b.yMax - b.yMin + 1 };
            const sx = b.xMin + b.xMax;
            const sy = b.yMin + b.yMax;
            if (f === 'L_TO_R') b.xMin = Math.floor((b.xMin + b.xMax) / 2) + 1;
            else if (f === 'R_TO_L') b.xMax = Math.floor((b.xMin + b.xMax) / 2);
            else if (f === 'T_TO_B') b.yMin = Math.floor((b.yMin + b.yMax) / 2) + 1;
            else if (f === 'B_TO_T') b.yMax = Math.floor((b.yMin + b.yMax) / 2);
            const bAfter = { xMin: b.xMin, xMax: b.xMax, yMin: b.yMin, yMax: b.yMax, cols: b.xMax - b.xMin + 1, rows: b.yMax - b.yMin + 1 };
            steps.push({ fold: f, bBefore, bAfter, sx, sy });
        }
        const finalBounds = { xMin: b.xMin, xMax: b.xMax, yMin: b.yMin, yMax: b.yMax, cols: b.xMax - b.xMin + 1, rows: b.yMax - b.yMin + 1 };
        return { steps, finalBounds };
    };

    const makeOrigamiSheet = (cols, rows, u, extraClass, children, showGuides) => {
        const kids = [];
        if (showGuides) {
            for (let c = 1; c < cols; c++) {
                kids.push(h('span.origami-guideline.vert', {
                    style: `left:${(c / cols) * 100}%;`
                }));
            }
            for (let r = 1; r < rows; r++) {
                kids.push(h('span.origami-guideline.horiz', {
                    style: `top:${(r / rows) * 100}%;`
                }));
            }
        }
        if (children) {
            if (Array.isArray(children)) kids.push(...children);
            else kids.push(children);
        }
        const wPx = cols * u;
        const hPx = rows * u;
        return h('div.origami-sheet' + (extraClass ? '.' + extraClass : '') + (showGuides ? '' : '.no-guides'), {
            style: `width:${wPx}px;height:${hPx}px;position:relative;`
        }, kids);
    };

    const origamiView = (q, answered) => {
        const d = q.origamiData;
        if (!d) return null;
        const rawRight = q.options && q.answer !== undefined ? q.options[q.answer] : '';
        const rightLetter = typeof rawRight === 'string' ? rawRight : (rawRight && rawRight.letter ? rawRight.letter : '');

        const size = d.size || 4;
        const { steps, finalBounds } = (d.stepBounds && d.finalBounds)
            ? { steps: d.stepBounds, finalBounds: d.finalBounds }
            : computeOrigamiBounds(d.folds || [], size);

        const u = size === 8 ? 8 : (size === 6 ? 10 : 16);
        const dotD = size === 8 ? 6 : (size === 6 ? 7 : 9);
        const showGuides = hasOrigamiGuidelines(q);

        // Card 0: Starting sheet (blank white paper)
        const startSheet = makeOrigamiSheet(size, size, u, 'full.blank',
            h('span.origami-blank-lbl', gt('origami_blank')),
            showGuides);
        const startCard = h('div.origami-sheet-card',
            h('span.origami-sheet-step', gt('origami_start')),
            h('div.origami-sheet-stage', startSheet));

        // Cards 1..N: Each fold step with exact sheet shape before that fold
        const foldCards = steps.map((step, idx) => {
            const f = step.fold;
            const isVert = (f === 'L_TO_R' || f === 'R_TO_L');
            const arrow = isVert
                ? (f === 'L_TO_R' ? '➡️' : '⬅️')
                : (f === 'T_TO_B' ? '⬇️' : '⬆️');

            const foldSheet = makeOrigamiSheet(step.bBefore.cols, step.bBefore.rows, u, '', [
                h('div.origami-fold-line' + (isVert ? '.vert' : '.horiz')),
                h('span.origami-arrow', arrow)
            ], showGuides);

            return h('div.origami-sheet-card',
                h('span.origami-sheet-step', String(idx + 1)),
                h('div.origami-sheet-stage', foldSheet));
        });

        // Final Card: Hole punch on folded sheet (exact shape and hole coordinates)
        const punchDots = (d.punch || []).map(([px, py]) => {
            const colIdx = px - finalBounds.xMin;
            const rowIdx = py - finalBounds.yMin;
            const cx = (colIdx + 0.5) * u;
            const cy = (rowIdx + 0.5) * u;
            return h('span.origami-punch-dot', {
                style: `left:${cx}px;top:${cy}px;width:${dotD}px;height:${dotD}px;`
            });
        });

        const punchSheet = makeOrigamiSheet(finalBounds.cols, finalBounds.rows, u, 'punched', punchDots, showGuides);
        const punchStepNum = String(steps.length + 1) + ' ✂️';
        const punchCard = h('div.origami-sheet-card.punch-card',
            h('span.origami-sheet-step', punchStepNum),
            h('div.origami-sheet-stage', punchSheet));

        const stepCards = [startCard, ...foldCards, punchCard];

        return h('div.origami-view',
            h('div.origami-steps-row', stepCards),
            answered ? h('div.h-row',
                h('span.h-chip.ok', '✓ ' + rightLetter + ' (' + gt('origami_unfolded') + ')')) : null);
    };

    const questionBody = (q, answered) => {
        if (q.view === 'mobiles' || q.cat === 'mobiles') return mobilesView(q, answered);
        if (q.view === 'origami' || q.cat === 'origami') return origamiView(q, answered);
        if (q.view === 'gears') return gearsView(q, answered);
        if (q.view === 'lasers') return lasersView(q, answered);
        if (q.view === 'cubes') return cubesView(q, answered);
        if (q.view === 'pipes') return pipesView(q, answered);
        if (q.cat === 'scales') return scalesView(q, answered);
        if (q.cat === 'cups') return cupsView(q, answered);
        if (q.cat === 'locks') return locksView(q, answered);
        if (q.cat === 'seats' && q.pv && q.pv.n) return seatsView(q, answered);
        if (q.cat === 'boxes' && q.lines && q.lines.length) return boxesView(q, answered);
        if (q.cat === 'lamps' || q.view === 'lamps') return lampsView(q, answered);
        if (q.view === 'grid') return gridView(q, answered);
        if (q.view === 'equation') return equationView(q, answered);
        if (q.view === 'list') return h('div.sequence', q.items.map(n => h('span', String(n))), h('span.gap' + (answered ? '.filled' : ''), answered ? optionText(q, q.options[q.answer]) : '?'));
        if (q.view === 'lines') return h('div.lines', q.lines.map(l => h('div', l)), h('div.ask', q.ask));
        if (q.view === 'facts') return h('div.facts', h('ul', q.facts.map(f => h('li', line(f)))), h('p.ask', line(q.ask)));
        return null;
    };

    function openTutorial(cat) {
        if (window.ThinkHopHelp) window.ThinkHopHelp.open(cat);
    }

    function tryExercise(opts) {
        opts = opts || {};
        const cat = opts.custom ? 'riddle' : (opts.cat || 'sign');
        const catPatterns = E.allPatterns().filter(p => p.cat === cat && !p.id.match(/^c\d+$/)).sort((a, b) => a.d - b.d);
        let selectedPid = opts.pid || '';
        let q = null, answered = false, picked = -1, removed = [];
        let seedCounter = (Date.now() & 0x7fffffff) || 12345;
        const nextSeed = () => {
            seedCounter = (seedCounter + 0x6D2B79F5) >>> 0;
            return seedCounter;
        };

        function generate() {
            answered = false;
            picked = -1;
            removed = [];
            const seed = nextSeed();
            if (opts.custom) {
                const r = E.rng(seed);
                const order = r.shuffle([0, 1, 2, 3]);
                const d = +opts.custom.difficulty || .25;
                q = {
                    pid: 'c' + opts.custom.id, cat: 'riddle', d, level: E.tierOf(d), limit: 45,
                    view: 'riddle', id: 'c' + opts.custom.id, t: opts.custom.data,
                    options: order.map(i => ({r: i})), answer: order.indexOf(0),
                    why: [{riddle: 'c' + opts.custom.id}],
                };
                return;
            }
            const p = (selectedPid && catPatterns.find(x => x.id === selectedPid))
                || catPatterns[seed % catPatterns.length]
                || E.allPatterns()[0];
            q = E.make(p.id, seed);
        }

        const stage = h('div.question.try-stage');
        const lang = I.getLang() === 'it' ? 'it' : 'en';
        const patLabel = p => RIDDLES[p.id] ? (RIDDLES[p.id].t[lang] || RIDDLES[p.id].t.en)[0].slice(0, 48) : p.id;
        const variantSelect = !opts.custom && catPatterns.length > 1
            ? h('select.ctl', {
                'aria-label': t('Exercise'),
                onchange: e => {
                    selectedPid = e.target.value;
                    generate();
                    draw();
                },
            },
            h('option', {value: '', selected: !selectedPid || undefined}, `${t('All variants')} (${catPatterns.length})`),
            catPatterns.map(p => h('option', {value: p.id, selected: p.id === selectedPid || undefined}, `${patLabel(p)} · ${tierName(E.tierOf(p.d))} (${p.d.toFixed(2)})`)))
            : null;

        function draw() {
            const right = answered && picked === q.answer;
            const M = window.ThinkHopMascots;
            const host = M ? M.hostOf(q.cat) : null;
            const isLampsRow = q.cat === 'lamps' && typeof q.options[0] === 'string' && q.options[0].includes('💡');
            const options = h('div.options' + (isLampsRow ? '.lamps-opts' : '') + (q.options.length === 5 ? '.five' : '') + (q.view === 'riddle' || q.view === 'facts' ? '.long' : '') + (q.pics ? '.pics' : ''), q.options.map((o, i) => {
                const gone = removed.includes(i);
                const state = !answered ? '' : (i === q.answer ? '.right' : (i === picked ? '.wrong' : ''));
                return h('button.option' + state + (gone ? '.gone' : ''), {
                    type: 'button', disabled: answered || gone || undefined, onclick: () => {
                        answered = true;
                        picked = i;
                        draw();
                    },
                }, optionText(q, o));
            }));
            stage.replaceChildren(
                ...[
                    h('div.tags',
                        h('span.tag', gt('cat_' + q.cat)),
                        h('span.tag.level-' + q.level, `${tierName(q.level)} · ${q.d.toFixed(2)}`),
                        h('span.tag', q.pid),
                        h('span.tag', icon('clock'), `${q.limit}s`)),
                    !answered && M ? h('div.guide',
                        h('span.mascot', {html: M.svg(host, 'happy')}),
                        h('p.bubble', h('b', host[0].toUpperCase() + host.slice(1), ' · '), M.tip(q.cat))) : null,
                    h('h1.prompt', promptText(q)),
                    questionBody(q, answered),
                    options,
                    answered
                        ? h('section.feedback' + (right ? '.good' : '.bad'), {role: 'status'},
                            h('h2', icon(right ? 'tick' : 'close'), gt(right ? 'right' : 'wrong')),
                            (window.ThinkHopSolution && window.ThinkHopSolution.render(q, right)) || whyLines(q),
                            h('button.btn.btn-primary.btn-block', {
                                type: 'button', onclick: () => {
                                    generate();
                                    draw();
                                }
                            }, t('Try another'), icon('arrow')))
                        : h('div.try-hint-row',
                            h('button.btn.btn-soft.small', {
                                type: 'button', disabled: removed.length > 0 || undefined, onclick: () => {
                                    const wrong = q.options.map((_, i) => i).filter(i => i !== q.answer);
                                    removed = E.rng(nextSeed()).shuffle(wrong).slice(0, Math.max(1, q.options.length - 2));
                                    draw();
                                }
                            }, icon('bulb'), h('span', t('Test hint'))),
                            h('button.btn.btn-ghost.small', {
                                type: 'button', onclick: () => {
                                    answered = true;
                                    picked = q.answer;
                                    draw();
                                }
                            }, t('Show answer')))
                ].filter(Boolean)
            );
        }

        generate();
        draw();
        const m = modal([
            h('div.try-head',
                h('h3', `${t('Try exercise')} · ${gt('cat_' + cat)}`),
                h('div.try-head-actions',
                    h('button.help-btn', {
                        type: 'button', title: t('Tutorial'), 'aria-label': t('Tutorial'), onclick: () => openTutorial(cat),
                    }, '?'),
                    h('button.btn.btn-soft.small', {
                        type: 'button', onclick: () => {
                            generate();
                            draw();
                        }
                    }, icon('replay'), h('span', t('New example'))))),
            variantSelect ? h('div.try-bar', variantSelect) : null,
            stage,
            h('div.actions', h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, gt('close'))),
        ]);
        m.box.classList.add('wide', 'try-modal');
    }

    // ---------- games: types, settings, recent games ----------
    async function gamesTab(view) {
        const [o, g] = await Promise.all([call('admin.overview'), call('admin.games')]);
        if (!o || !g) return;
        const s = o.settings;
        s.catWeights = s.catWeights || {};
        const saveSettings = () => call('admin.settings.save', s, d => {
            Object.assign(s, d.settings);
            s.catWeights = s.catWeights || {};
            toast(t('Saved'), 'ok');
        });
        const length = h('input.ctl', {type: 'number', min: 3, max: 10, value: s.levelLength});
        const costs = E.TIERS.map(k => [k, h('input.ctl', {type: 'number', min: 0, max: 5000, step: 10, value: s.hintCost[k]})]);
        const label = {easy: 'Hint cost (easy)', medium: 'Hint cost (medium)', hard: 'Hint cost (hard)', expert: 'Hint cost (expert)'};
        const modeName = x => x.mode === 'level' ? gt('level_n', {n: x.stage}) : t(x.mode === 'daily' ? 'Daily' : 'Survival');
        view.replaceChildren(
            h('div.admin-cols',
                card(t('Types of question'), h('p.muted', t('Adjust probability (0.0 – 1.0) compared to other exercises of the same difficulty. Switch off a type to exclude it completely.')),
                    h('div.type-list', E.CATS.map(k => {
                        let isDisabled = s.disabledCats.includes(k);
                        const curWeight = (s.catWeights && s.catWeights[k] !== undefined) ? +s.catWeights[k] : 1.0;
                        const badge = h('span.prob-badge', `${Math.round(curWeight * 100)}%`);
                        const updateVal = val => {
                            val = Math.max(0, Math.min(1, Math.round((isNaN(val) ? 1.0 : val) * 100) / 100));
                            probRange.value = val;
                            probNum.value = val.toFixed(2);
                            s.catWeights[k] = val;
                            badge.textContent = `${Math.round(val * 100)}%`;
                            saveSettings();
                        };
                        const probRange = h('input.prob-range', {
                            type: 'range',
                            min: 0,
                            max: 1,
                            step: 0.05,
                            value: curWeight,
                            disabled: isDisabled || undefined,
                            'aria-label': `${gt('cat_' + k)}: ${t('Probability')}`,
                            oninput: () => {
                                const val = +probRange.value;
                                probNum.value = val.toFixed(2);
                                badge.textContent = `${Math.round(val * 100)}%`;
                            },
                            onchange: () => updateVal(+probRange.value)
                        });
                        const probNum = h('input.prob-num', {
                            type: 'number',
                            min: 0,
                            max: 1,
                            step: 0.05,
                            value: curWeight.toFixed(2),
                            disabled: isDisabled || undefined,
                            'aria-label': `${gt('cat_' + k)}: ${t('Probability')}`,
                            onchange: () => updateVal(+probNum.value)
                        });

                        const row = h('div.type-row' + (isDisabled ? '.type-row-off' : ''),
                            h('div.type-main',
                                h('div.type-info',
                                    h('strong', gt('cat_' + k)),
                                    h('small', t('{n} kinds of exercise', {n: E.allPatterns().filter(p => p.cat === k).length}))),
                                h('div.type-actions',
                                    h('button.help-btn', {
                                        type: 'button', title: t('Tutorial'), 'aria-label': t('Tutorial'), onclick: () => openTutorial(k),
                                    }, '?'),
                                    h('button.btn.btn-soft.small', {
                                        type: 'button', title: t('Try exercise'), onclick: () => tryExercise({cat: k}),
                                    }, icon('play'), h('span', t('Try'))),
                                    toggle(!isDisabled, gt('cat_' + k), on => {
                                        isDisabled = !on;
                                        row.classList.toggle('type-row-off', isDisabled);
                                        probRange.disabled = isDisabled;
                                        probNum.disabled = isDisabled;
                                        s.disabledCats = on ? s.disabledCats.filter(x => x !== k) : [...s.disabledCats, k];
                                        saveSettings();
                                    }))),
                            h('div.type-prob',
                                h('div.type-prob-meta',
                                    h('span.prob-title', t('Probability')),
                                    badge),
                                h('div.type-prob-controls',
                                    probRange,
                                    probNum)));

                        return row;
                    }))),
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
                    costs.map(([k, el]) => h('label.field', h('span', `${t('Hint cost')}: ${tierName(k)}`), el)),
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
                h('button.btn.btn-soft.small', {type: 'button', title: t('Try exercise'), onclick: () => tryExercise({custom: q})}, icon('play'), h('span', t('Try'))),
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
            wrap.replaceChildren(table([t('Exercise'), t('Type'), t('Difficulty'), t('Tier'), t('Active'), ''], list.map(p => {
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
                    })),
                    h('td.row-actions',
                        h('button.help-btn', {type: 'button', title: t('Tutorial'), 'aria-label': t('Tutorial'), onclick: () => openTutorial(p.cat)}, '?'),
                        h('button.btn.btn-soft.small', {type: 'button', title: t('Try exercise'), onclick: () => tryExercise({pid: p.id, cat: p.cat})}, icon('play'), h('span', t('Try')))));
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
                const av = window.ThinkHopAvatars ? window.ThinkHopAvatars.render(p.avatar, p.name, 'avatar md') : null;
                return h('tr',
                    h('td', h('div.player-cell', av, h('div', h('strong', p.username ? '@' + p.username : p.name), h('small.block', `${p.name} · ${p.email}`)))), h('td', String(p.stage)), h('td', num(p.games)), h('td', num(p.points)), h('td', when(p.last_login_at || p.created_at)),
                    h('td', self ? t('You') : (off ? t('Disabled') : (+p.verified ? (admin ? t('Administrator') : t('Active')) : t('Email not confirmed')))),
                    h('td.row-actions', h('button.btn.btn-soft.small', {type: 'button', onclick: () => showPlayer(p.id, +d.me, refresh)}, t('Details')), self ? null : [
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

    // ---------- one player: everything they did, read only; only the points can be changed ----------
    const clock = s => {
        const m = Math.round((+s || 0) / 60);
        return m < 60 ? `${m} min` : `${Math.floor(m / 60)} h ${m % 60} min`;
    };

    async function showPlayer(id, me, refresh) {
        const body = h('div', skeleton('tiles', 8), skeleton('table', 6));
        const m = modal([h('h3', t('Details')), h('p.muted', t('Read only: what this player did.')), body,
            h('div.actions', h('button.btn.btn-primary', {type: 'button', onclick: () => m.close()}, gt('close')))]);
        m.box.classList.add('wide');
        const d = await call('admin.player', {id});
        if (!d) return body.replaceChildren(h('p.muted', '–'));
        const p = d.player, s = d.stats;
        const badge = (on, yes, no) => h('span.tag' + (on ? '.on' : ''), t(on ? yes : no));
        const share = s.total > 0 ? Math.round(s.correct / s.total * 100) + '%' : '–';
        const modeName = x => x.mode === 'level' ? gt('level_n', {n: x.stage}) : t(x.mode === 'daily' ? 'Daily' : 'Survival');
        const self = p.id === me;
        const balance = h('input.ctl', {type: 'number', min: 0, max: 10000000, step: 1, value: p.balance, disabled: self || undefined});
        const note = h('input.ctl', {type: 'text', maxlength: 200, placeholder: t('Note (optional)'), disabled: self || undefined});
        const save = h('button.btn.btn-primary', {type: 'submit', disabled: self || undefined}, t('Save'));
        const av = window.ThinkHopAvatars ? window.ThinkHopAvatars.render(p.avatar, p.name, 'avatar lg') : null;
        body.replaceChildren(
            h('div.player-head', h('div.player-head-row', av, h('div', h('strong', p.username ? '@' + p.username : p.name), h('span.muted.block', `${p.name} · ${p.email}`))),
                h('div.tags', badge(!!p.verified, 'Email confirmed', 'Email not confirmed'), p.google ? h('span.tag', 'Google') : null, p.password ? h('span.tag', t('Password')) : null,
                    badge(p.ranking, 'In the ranking', 'Hidden from the ranking'), p.admin ? h('span.tag.on', t('Administrator')) : null, p.status === 'disabled' ? h('span.tag.off', t('Disabled')) : null)),
            h('div.stat-grid.wide',
                tile(t('Wallet'), num(p.balance)), tile(t('Earned'), num(s.earned)), tile(t('Spent'), num(s.spent)), tile(t('Spent on profile'), num(p.avatarSpent || 0)), tile(t('Changed by admins'), (p.adjust > 0 ? '+' : '') + num(p.adjust)),
                tile(t('Games played'), num(s.games)), tile(t('Levels passed'), num(s.levels)), tile(t('Highest level'), num(s.top_level)), tile(t('Daily challenges'), num(s.dailies)),
                tile(t('Right answers'), share), tile(t('Play time'), clock(s.seconds)), tile(t('Best daily'), num(s.best_daily)), tile(t('Best survival'), num(s.best_survival)),
                tile(t('Following'), num(p.following)), tile(t('Followers'), num(p.followers)), tile(t('Notification devices'), num(p.devices))),
            h('dl.player-dates', [['Signed up', p.createdAt], ['Last sign-in', p.lastLogin], ['First game', s.first_game], ['Last game', s.last_game]].map(([k, v]) => [h('dt', t(k)), h('dd', v ? when(v) : t('Never'))])),
            h('form.form.points-form', {
                    onsubmit: async e => {
                        e.preventDefault();
                        save.disabled = true;
                        await call('admin.player.points', {id: p.id, balance: +balance.value, note: note.value}, () => {
                            toast(t('Points saved'), 'ok');
                            m.close();
                            showPlayer(id, me, refresh);
                            if (refresh) refresh();
                        });
                        save.disabled = false;
                    }
                }, h('h4', t('Change points')), h('p.muted', self ? t('You cannot change your own points.') : t('The games stay as they were played: only the balance changes.')),
                h('div.form-row', h('label.field', h('span', t('New balance')), balance), h('label.field', h('span', t('Note')), note), save)),
            h('h4', t('Last 100 games')),
            d.games.length ? table([t('Date'), t('Mode'), t('Result'), t('Points'), t('Spent'), t('Time')], d.games.map(x => h('tr',
                h('td', when(x.played_at)), h('td', modeName(x)), h('td', `${x.correct}/${x.total} ${'★'.repeat(+x.stars)}`), h('td', num(x.score)), h('td', num(x.spent)), h('td', clock(x.seconds))))) : h('p.muted', t('No data yet.')),
            h('h4', t('Admin changes')),
            d.log.length ? table([t('Date'), t('Admin'), t('Change'), t('Note')], d.log.map(x => {
                const v = JSON.parse(x.detail || '{}');
                return h('tr', h('td', when(x.created_at)), h('td', x.admin || '–'), h('td', `${num(v.from)} → ${num(v.to)}`), h('td', v.note || ''));
            })) : h('p.muted', t('No changes.')));
    }

    // ---------- profile pictures: prices and availability ----------
    async function avatarsTab(view) {
        const d = await call('admin.avatars');
        if (!d) return;
        const Av = window.ThinkHopAvatars;
        const previewCode = (kind, id) => kind === 'character' ? id + ':violet' : kind === 'accessory' ? ':violet:' + id : kind === 'frame' ? ':violet::' + id : ':' + id;
        const section = (kind, title) => card(t(title),
            table(['', t('Name'), t('Price (points)'), t('On sale'), t('Sold'), ''], d.items.filter(x => x.kind === kind).map(x => {
                const art = Av.item(x.id);
                const label = art ? Av.name(art) : x.id;
                let enabled = +x.enabled === 1;
                const price = h('input.ctl.adm-price', {type: 'number', min: 0, max: 1000000, step: 50, value: +x.price, 'aria-label': t('Price (points)')});
                const save = () => call('admin.avatar.save', {id: x.id, price: +price.value, enabled}, () => toast(t('Saved'), 'ok'));
                return h('tr',
                    h('td', Av.render(previewCode(kind, x.id), kind === 'accessory' ? '' : 'A', 'md')),
                    h('td', h('strong', label), h('small.block', x.id)),
                    h('td', price),
                    h('td', toggle(enabled, label, on => {
                        enabled = on;
                        save();
                    })),
                    h('td', num(x.sold || 0)),
                    h('td.row-actions', h('button.btn.btn-soft.small', {type: 'button', onclick: save}, icon('save'), h('span', t('Save')))));
            })));
        view.replaceChildren(
            card(t('Profile pictures'), h('p.muted', t('Players buy these with their points from their profile. At 0 an item is free for everyone. An item taken off sale stays with whoever already bought it.'))),
            section('character', 'Characters'),
            section('accessory', 'Accessories'),
            section('frame', 'Frames'),
            section('background', 'Backgrounds'));
    }

    // ---------- shell ----------
    const VIEWS = {overview, games: gamesTab, questions: questionsTab, players: playersTab, avatars: avatarsTab};

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
                h('button.icon-btn', {type: 'button', title: u.name, 'aria-label': gt('account'), onclick: A.open}, window.ThinkHopAvatars ? window.ThinkHopAvatars.render(u.avatar, u.name, 'avatar sm') : icon('user'))),
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
