/* ThinkHop — game UI: home, questions, hints, results, statistics. How a game works is in engine.js. */
(function () {
    'use strict';

    const E = window.ThinkHopEngine;
    const I = window.ThinkHopI18n;
    const A = window.ThinkHopAccount;
    const S = window.ThinkHopSocial;
    const M = window.ThinkHopMascots;
    const {store, h, icon, toast, modal, confirmBox} = window.ThinkHopUI;
    const t = I.t;
    const app = document.getElementById('app');
    const VERSION = (window.ThinkHopReleases && window.ThinkHopReleases[0].version) || '1.0.0';
    const NAME = 'ThinkHop';
    const RIDDLES = Object.fromEntries((window.ThinkHopRiddles || []).map(r => [r.id, r]));

    const fmtTime = s => {
        s = Math.floor(s);
        return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
    };
    const fmtNumber = n => n.toLocaleString(I.getLang());

    // ---------- theme ----------
    function applyTheme() {
        const th = store.get('theme', 'auto');
        if (th === 'auto') document.documentElement.removeAttribute('data-theme'); else document.documentElement.setAttribute('data-theme', th);
        const dark = th === 'dark' || (th === 'auto' && matchMedia('(prefers-color-scheme: dark)').matches);
        document.querySelectorAll('meta[name=theme-color]').forEach(m => {
            m.removeAttribute('media');
            m.content = dark ? '#141127' : '#FFFBF2';
        });
        document.querySelectorAll('.theme-tabs button').forEach(b => {
            const on = b.dataset.mode === th;
            b.classList.toggle('sel', on);
            b.setAttribute('aria-checked', String(on));
        });
    }

    function themeTabs() {
        const th = store.get('theme', 'auto');
        return h('div.seg.theme-tabs', {role: 'radiogroup', 'aria-label': t('theme')}, ['auto', 'light', 'dark'].map(k => h('button' + (k === th ? '.sel' : ''), {
            type: 'button', role: 'radio', 'aria-checked': String(k === th), 'data-mode': k,
            onclick: () => {
                store.set('theme', k);
                applyTheme();
            }
        }, k === 'auto' ? 'Auto' : t('theme_' + k))));
    }

    function cycleTheme() {
        const next = {auto: 'light', light: 'dark', dark: 'auto'}[store.get('theme', 'auto')];
        store.set('theme', next);
        applyTheme();
        toast(t('theme') + ': ' + t('theme_' + next));
    }

    // ---------- state ----------
    let G = null;           // current game
    let screen = 'home';
    let startedAt = 0;      // when the current question appeared

    let ticking = null;     // the clock of the current question
    let worried = false;    // the mascot already warned about the time on this question

    // ---------- mascots ----------
    const face = (id, mood, extra) => h('span.mascot' + (extra || ''), {html: M.svg(id, mood), title: id[0].toUpperCase() + id.slice(1)});
    const guide = (id, mood, text, extra) => h('div.guide' + (extra || ''), face(id, mood), h('p.bubble', h('b', id[0].toUpperCase() + id.slice(1)), text));

    /** Tips only while a type is new; then a word now and then, so that the mascots don't become routine. */
    function questionGuide(q) {
        const id = M.hostOf(q.cat);
        const met = store.get('met', {});
        if (q.guided === undefined) {
            const n = met[q.cat] || 0;
            q.guided = n < 3 ? 'tip' : ((G.index + q.sig.length) % 3 === 0 ? 'idle' : 'none');
            store.set('met', Object.assign(met, {[q.cat]: n + 1}));
        }
        if (q.guided === 'tip') return guide(id, 'think', M.tip(q.cat));
        return q.guided === 'idle' ? guide(id, 'happy', M.pick(id, 'idle', G.index)) : null;
    }

    function reaction(q, right) {
        const id = M.hostOf(q.cat);
        if (G.picked < 0) return guide(id, 'sad', M.say(id, 'time'), '.small');
        if (right && [3, 5, 8, 10, 15, 20].includes(G.streak)) return guide(id, 'happy', M.say(id, 'streak'), '.small.hop');
        return guide(id, right ? 'happy' : 'sad', M.pick(id, right ? 'right' : 'wrong', G.index + G.correct), '.small' + (right ? '.hop' : ''));
    }

    const worldHost = world => M.IDS[world % M.IDS.length];

    /** A new world opens with its mascot: the clock starts only after this. */
    function worldIntro(stage, go) {
        const world = Math.floor((stage - 1) / E.WORLD);
        const seenWorlds = store.get('worlds', []);
        if ((stage - 1) % E.WORLD !== 0 || seenWorlds.includes(world)) return go();
        store.set('worlds', [...seenWorlds, world]);
        const id = worldHost(world);
        const m = modal([h('div.intro', face(id, 'happy', '.big.hop'), h('h3', t('world_n', {n: world + 1})), h('p', M.say(id, 'world'))),
            h('div.actions', h('button.btn.btn-primary.btn-block', {type: 'button', onclick: () => m.close()}, t('start'), icon('arrow')))], {persistent: true, onclose: go});
    }

    const elapsed = () => G.clock + (performance.now() - startedAt) / 1000;

    /** A saved game keeps the time already used: leaving a question doesn't give it back. */
    const save = () => {
        if (!G) return;
        if (!G.answered && screen === 'game') {
            G.clock = elapsed();
            startedAt = performance.now();
        }
        store.set('saved', G);
        if (G.mode === 'daily') store.set('daily_saved', G);
    };

    function stopClock() {
        clearInterval(ticking);
        ticking = null;
    }

    function runClock(q) {
        stopClock();
        if (G.answered) return;
        const show = () => {
            const left = Math.max(0, Math.ceil(q.limit - elapsed()));
            if (left <= 10 && left > 0 && !worried) {
                worried = true;
                const id = M.hostOf(q.cat);
                const slot = app.querySelector('.timer-face');
                if (slot) {
                    slot.replaceChildren(face(id, 'worried', '.shake'));
                    toast(M.say(id, 'worry'));
                }
            }
            const el = app.querySelector('.clock');
            if (el) {
                el.textContent = String(left);
                el.classList.toggle('low', left <= 10);
                el.setAttribute('aria-label', t('seconds_left', {n: left}));
            }
            if (left <= 0) timeUp();
        };
        ticking = setInterval(show, 250);
        show();
    }

    /** Out of time: it counts as a wrong answer, and the right one is shown. */
    function timeUp() {
        stopClock();
        if (!G || G.answered) return;
        E.answer(G, -1, G.questions[G.index].limit);
        save();
        renderGame();
    }
    const seen = () => store.get('seen', []);

    /** What was asked is not asked again: the engine skips it in every new game. */
    function rememberQuestion(q) {
        if (q.shown) return;
        q.shown = true;
        store.set('seen', [...seen(), q.sig].slice(-8000));
    }

    /** The wallet also counts what an administrator changed on the account and what was spent in the avatar shop. */
    const stats = () => {
        const s = E.stats(A.games());
        const u = A.user();
        s.balance = Math.max(0, s.balance + (u && u.pointsAdjust ? u.pointsAdjust : 0) - (A.avatarSpent ? A.avatarSpent() : 0));
        return s;
    };
    /** Points that can be spent now: the wallet plus what the current game has earned so far. */
    const balance = () => stats().balance + (G ? G.score - G.spent : 0);

    function begin(mode, stage) {
        const today = E.dayText();
        if (mode === 'daily') {
            const dailySaved = store.get('daily_saved');
            const saved = store.get('saved');
            const activeDaily = (dailySaved && dailySaved.day === today) ? dailySaved : (saved && saved.mode === 'daily' && saved.day === today ? saved : null);
            if (activeDaily) {
                G = activeDaily;
                return renderGame();
            }
            if (stats().dailyDone) {
                toast(t('mode_daily_done', {n: ''}), 'bad');
                return showHome();
            }
        }
        G = E.newGame(mode, stage, Math.floor(Math.random() * 2147483647), seen(), today);
        store.set('saved', G);
        if (mode === 'daily') store.set('daily_saved', G);
        if (mode === 'level') worldIntro(stage, renderGame);
        else renderGame();
    }

    async function start(mode, stage) {
        const today = E.dayText();
        if (mode === 'daily') {
            const dailySaved = store.get('daily_saved');
            const saved = store.get('saved');
            const activeDaily = (dailySaved && dailySaved.day === today) ? dailySaved : (saved && saved.mode === 'daily' && saved.day === today ? saved : null);
            if (activeDaily) {
                G = activeDaily;
                return renderGame();
            }
        }
        if (store.get('saved') && !await confirmBox(t('confirm_new'), t('confirm_new_text'), t('start'), t('cancel'))) return;
        begin(mode, stage || 0);
    }

    // ---------- texts of a question ----------
    /** The invented words are nouns: German writes them with a capital letter. */
    const word = w => I.getLang() === 'de' && E.WORDS.includes(w) ? w[0].toUpperCase() + w.slice(1) : w;
    const line = l => {
        const v = {};
        Object.entries(l.v || {}).forEach(([k, x]) => {
            v[k] = x && typeof x === 'object' ? line(x) : word(x);
        });
        return t(l.k, v);
    };
    const riddleText = q => {
        const r = q.t ? q : RIDDLES[q.id];
        return r.t[I.getLang()] || r.t.en;
    };

    function hasOrigamiGuidelines(q) {
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
    }

    function optionText(q, o) {
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
        if (o.k) return t(o.k);
        return riddleText(q)[2][o.r];
    }

    function whyLines(q) {
        return q.why.map(w => {
            if (w.lit) return h('p.why-lit', w.lit);
            if (w.riddle) return h('p', riddleText(q)[1]);
            const v = Object.assign({}, w.v);
            if (w.prop) v.prop = t(w.prop.k, w.prop.v);
            return h('p', line({k: w.k, v}));
        });
    }

    function promptText(q) {
        if (q.pk) return line({k: q.pk, v: q.pv});
        if (q.cat === 'sign') return t(q.nums.length > 2 ? 'q_sign2' : 'q_sign');
        if (q.cat === 'riddle') return riddleText(q)[0];
        return t('q_' + q.cat);
    }

    /** The equation with the gaps: once answered, the gaps show the right signs. */
    function equationView(q) {
        const signs = G.answered ? String(optionText(q, q.options[q.answer])).split(/\s+/) : [];
        const parts = [];
        q.nums.forEach((n, i) => {
            if (i) parts.push(h('span.gap' + (G.answered ? '.filled' : ''), signs[i - 1] || '?'));
            parts.push(h('span', String(n)));
        });
        return h('div.equation', parts, h('span', '='), h('span', String(q.result)));
    }

    /** A board of cells: once answered, the “?” shows the right symbol. */
    function gridView(q) {
        const right = optionText(q, q.options[q.answer]);
        const n = q.grid.length;
        const edge = (i, size) => size && (i + 1) % size === 0 && i + 1 < n;
        return h('div.grid-wrap',
            h('div.board', {style: `--n:${n}`}, q.grid.flatMap((row, y) => row.map((cell, x) => {
                const ask = cell === '?';
                return h('span.cell' + (ask ? '.ask' : '') + (q.boxes && edge(x, q.boxes[1]) ? '.edge-r' : '') + (q.boxes && edge(y, q.boxes[0]) ? '.edge-b' : ''), ask && G.answered ? right : cell);
            }))),
            q.lines ? h('div.path', q.lines) : null);
    }

    const GEMS_LIST = ['💎', '🔮', '🪙', '⭐'];

    function gearsView(q, answered) {
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
    }

    function lasersView(q, answered) {
        const d = q.lasersData;
        if (!d) return gridView(q);
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
    }

    function cubesView(q, answered) {
        const d = q.cubesData;
        if (!d) return gridView(q);

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
    }

    function pipesView(q, answered) {
        const d = q.pipesData;
        if (!d) return q.lines ? h('div.lines', q.lines.map(l => h('div', l)), h('div.ask', q.ask)) : null;
        const right = optionText(q, q.options[q.answer]);

        const tankCards = (d.tanks || []).map((name, i) => {
            const isWinner = (name === right);
            const isFilled = answered && isWinner;
            const isSource = (i === 0);
            return h('div.pipe-tank-card' + (isFilled ? '.winner-fill' : '') + (isSource ? '.source' : ''),
                isSource ? h('span.pipe-tank-faucet', '🚰') : null,
                h('span.water-level'),
                h('span.pipe-tank-lbl', name),
                h('span.tank-water-icon', isFilled ? '💧💧' : (isSource ? '💧' : '')));
        });

        const valveChips = (d.connections || []).map(c => {
            return h('span.valve-chip' + (c.blocked ? '.blocked' : '.ok'),
                `${c.from} ➔ ${c.to}`,
                h('b', c.blocked ? ' 🚫' : ' ✓'));
        });

        return h('div.pipes-view',
            h('div.pipes-stage', tankCards),
            h('div.h-row.pipes-chips', valveChips));
    }

    function scalesView(q, answered) {
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
    }

    function cupsView(q, answered) {
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
    }

    function locksView(q, answered) {
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
    }

    function seatsView(q, answered) {
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
    }

    function boxesView(q, answered) {
        const right = optionText(q, q.options[q.answer]);
        const chests = (q.lines || []).map(lineStr => {
            const isWinner = lineStr.includes(right);
            const isOpened = answered && isWinner;
            return h('div.chest-card' + (isOpened ? '.winner-open' : ''),
                h('span.chest-icon', isOpened ? '💎' : '🧰'),
                h('span.chest-label', lineStr));
        });

        return h('div.boxes-view', chests);
    }

    function lampsView(q, answered) {
        if (!q.lines || !q.lines.length) return null;
        const startLine = q.lines[0];
        const buttonLines = q.lines.slice(1);
        const isBack = startLine.includes('→');

        return h('div.lamps-view',
            h('div.lamps-start-card',
                h('span.lamps-badge', isBack ? t('lamps_trans') : t('lamps_start')),
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
    }

    function mobilesView(q, answered) {
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
    }

    function computeOrigamiBounds(folds, size = 4) {
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
    }

    function makeOrigamiSheet(cols, rows, u, extraClass, children, showGuides) {
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
    }

    function origamiView(q, answered) {
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
            h('span.origami-blank-lbl', t('origami_blank')),
            showGuides);
        const startCard = h('div.origami-sheet-card',
            h('span.origami-sheet-step', t('origami_start')),
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
                h('span.h-chip.ok', '✓ ' + rightLetter + ' (' + t('origami_unfolded') + ')')) : null);
    }

    function questionBody(q) {
        if (q.view === 'mobiles' || q.cat === 'mobiles') return mobilesView(q, G.answered);
        if (q.view === 'origami' || q.cat === 'origami') return origamiView(q, G.answered);
        if (q.view === 'gears') return gearsView(q, G.answered);
        if (q.view === 'lasers') return lasersView(q, G.answered);
        if (q.view === 'cubes') return cubesView(q, G.answered);
        if (q.view === 'pipes') return pipesView(q, G.answered);
        if (q.cat === 'scales') return scalesView(q, G.answered);
        if (q.cat === 'cups') return cupsView(q, G.answered);
        if (q.cat === 'locks') return locksView(q, G.answered);
        if (q.cat === 'seats' && q.pv && q.pv.n) return seatsView(q, G.answered);
        if (q.cat === 'boxes' && q.lines && q.lines.length) return boxesView(q, G.answered);
        if (q.cat === 'lamps' || q.view === 'lamps') return lampsView(q, G.answered);
        if (q.view === 'grid') return gridView(q);
        if (q.view === 'equation') return equationView(q);
        if (q.view === 'list') return h('div.sequence', q.items.map(n => h('span', String(n))), h('span.gap' + (G.answered ? '.filled' : ''), G.answered ? optionText(q, q.options[q.answer]) : '?'));
        if (q.view === 'lines') return h('div.lines', q.lines.map(l => h('div', l)), h('div.ask', q.ask));
        if (q.view === 'facts') return h('div.facts', h('ul', q.facts.map(f => h('li', line(f)))), h('p.ask', line(q.ask)));
        return null;
    }

    // ---------- game screen ----------
    function renderGame() {
        screen = 'game';
        greeting = null;
        const q = G.questions[G.index];
        const mode = G.mode === 'level' ? t('level_n', {n: G.stage}) : (G.mode === 'difficulty' ? t('diff_mode_' + (G.diffKey || 'beginner')) : t('mode_' + G.mode));
        const counter = G.mode === 'survival' ? t('question_n', {n: G.index + 1}) : t('question_of', {n: G.index + 1, m: G.questions.length});
        const lives = G.mode === 'survival'
            ? h('span.lives', {role: 'img', 'aria-label': t('lives_left', {n: G.lives})}, Array.from({length: E.LIVES}, (_, i) => h('span' + (i < G.lives ? '.on' : ''), icon('heart')))) : null;
        const isLampsRow = q.cat === 'lamps' && typeof q.options[0] === 'string' && q.options[0].includes('💡');
        const options = h('div.options' + (isLampsRow ? '.lamps-opts' : '') + (q.options.length === 5 ? '.five' : '') + (q.view === 'riddle' || q.view === 'facts' ? '.long' : '') + (q.pics ? '.pics' : ''), q.options.map((o, i) => {
            const gone = G.removed.includes(i);
            const state = !G.answered ? '' : (i === q.answer ? '.right' : (i === G.picked ? '.wrong' : ''));
            return h('button.option' + state + (gone ? '.gone' : ''), {
                type: 'button', disabled: G.answered || gone || undefined, onclick: () => choose(i),
            }, optionText(q, o));
        }));
        const right = G.answered && G.picked === q.answer;
        app.replaceChildren(h('div.game',
            h('header.game-bar',
                h('button.icon-btn', {type: 'button', title: t('exit_title'), 'aria-label': t('exit_title'), onclick: leave}, icon('back')),
                h('div.title', h('strong', mode), h('span', counter)),
                lives,
                h('div.score-pill', {title: t('balance')}, icon('star'), h('b', fmtNumber(balance())))),
            G.mode === 'survival' ? null : h('div.progress', {'aria-hidden': 'true'}, h('i', {style: `width:${(G.index + (G.answered ? 1 : 0)) / G.questions.length * 100}%`})),
            h('main.question',
                h('div.tags', h('span.tag', t('cat_' + q.cat)), h('span.tag.level-' + q.level, t(q.level)),
                    G.streak > 1 ? h('span.tag.hot', icon('flame'), String(G.streak)) : null,
                    h('button.help-btn', {
                        type: 'button', title: t('how_to_play'), 'aria-label': t('how_to_play'), onclick: () => showHelp(q),
                    }, '?')),
                G.answered ? null : questionGuide(q),
                h('h1.prompt', promptText(q)),
                questionBody(q),
                G.answered ? null : h('div.timer', h('span.timer-face', face(M.hostOf(q.cat), 'think')), h('div.speed', {'aria-hidden': 'true'}, h('i', {style: `animation-duration:${q.limit}s;animation-delay:-${G.clock}s`})), h('span.clock', {role: 'timer'})),
                options,
                G.answered ? h('section.feedback' + (right ? '.good' : '.bad'), {role: 'status'},
                        h('h2', icon(right ? 'tick' : 'close'), G.picked < 0 ? t('time_up') : t(right ? 'right' : 'wrong'), right ? h('span.gain', t('plus_points', {n: G.gained})) : null),
                        reaction(q, right),
                        (window.ThinkHopSolution && window.ThinkHopSolution.render(q, right)) || whyLines(q),
                        h('button.btn.btn-primary.btn-block', {type: 'button', onclick: advance}, E.isOver(G) ? t('see_result') : t('next'), icon('arrow')))
                    : h('button.btn.btn-soft.hint-btn', {
                        type: 'button', title: t('hint_title'), disabled: G.removed.length > 0 || undefined, onclick: useHint,
                    }, icon('bulb'), t('hint_cost', {n: E.hintCost(G)})))));
        document.title = `${NAME} – ${counter}`;
        if (!G.answered) {
            startedAt = performance.now();
            worried = false;
            rememberQuestion(q);
            runClock(q);
        } else {
            stopClock();
        }
        window.scrollTo(0, 0);
        if (G.answered) app.querySelector('.feedback .btn').focus({preventScroll: true});
    }

    function choose(i) {
        if (G.answered || G.removed.includes(i)) return;
        stopClock();
        E.answer(G, i, elapsed());
        save();
        renderGame();
        const box = app.querySelector('.feedback');
        if (box) box.scrollIntoView({block: 'nearest', behavior: 'smooth'});
    }

    function showHelp(q) {
        if (!window.ThinkHopHelp) return;
        const active = G && !G.answered;
        window.ThinkHopHelp.open(q.cat, {
            paused: active,
            onopen: () => {
                if (!active) return;
                G.clock = elapsed();
                stopClock();
                const bar = app.querySelector('.speed i');
                if (bar) bar.style.animationPlayState = 'paused';
            },
            onclose: () => {
                if (!G || G.answered || screen !== 'game') return;
                startedAt = performance.now();
                const bar = app.querySelector('.speed i');
                if (bar) bar.style.animationPlayState = 'running';
                runClock(G.questions[G.index]);
            },
        });
    }

    function useHint() {
        if (!E.hint(G, balance())) {
            if (!G.removed.length) toast(t('not_enough', {n: E.hintCost(G)}), 'bad');
            return;
        }
        save();
        renderGame();
    }

    function advance() {
        if (E.isOver(G)) return finish();
        E.next(G, seen());
        startedAt = performance.now();
        save();
        renderGame();
    }

    function leave() {
        save();
        stopClock();
        showHome();
    }

    // ---------- end of a game ----------
    function finish() {
        stopClock();
        const g = E.summary(G);
        const before = stats().best[G.mode] || 0;
        const done = G;
        const passed = done.mode !== 'level' || g.stars > 0;
        A.addGame(g);
        G = null;
        store.del('saved');
        if (done.mode === 'daily') store.del('daily_saved');
        showHome();
        const title = done.mode === 'level'
            ? t(passed ? 'level_passed' : 'level_failed', {n: done.stage})
            : (done.mode === 'difficulty' ? t('over_difficulty') : t('over_' + done.mode));
        const needed = Math.ceil(done.questions.length / 2);
        const last = done.stage >= E.STAGES;
        const host = M.IDS[(done.stage + done.correct) % M.IDS.length];
        const again = (label, stage, primary) => h('button.btn' + (primary ? '.btn-primary' : '.btn-soft'), {
            type: 'button', onclick: () => {
                m.close();
                begin(done.mode, stage);
            }
        }, label);
        const m = modal([
            h('div.win-box',
                face(host, passed ? 'happy' : 'sad', '.big' + (passed ? '.hop' : '')),
                h('h3', title),
                h('div.stars', {role: 'img', 'aria-label': `${g.stars}/3`}, [0, 1, 2].map(i => h('span' + (i < g.stars ? '.on' : ''), icon('star')))),
                h('div.score', h('b', '+' + fmtNumber(done.score)), h('span', t('stat_points'))),
                done.mode !== 'level' && done.score > before && before > 0 ? h('p.record', '🏆 ' + t('new_record')) : null,
                h('p.bubble.result-line', M.say(host, passed ? 'pass' : 'fail')),
                h('p.muted', t('result_right', {n: done.correct, m: g.total})),
                passed ? h('p.muted', [t('result_streak', {n: done.bestStreak}), t('result_time', {t: fmtTime(done.seconds)})].join(' · ')) : h('p.muted', t('level_failed_text', {n: needed})),
                done.mode === 'level' && passed && last ? h('p.muted', t('all_done')) : null),
            h('div.actions.win-actions',
                done.mode === 'daily' ? h('button.btn.btn-soft', {type: 'button', onclick: () => share(g)}, icon('share'), t('share')) : null,
                h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('home')),
                done.mode === 'survival' ? again(t('play_again'), 0, true) : null,
                done.mode === 'difficulty' ? again(t('play_again'), done.diffKey, true) : null,
                done.mode === 'level' && !passed ? again(t('retry'), done.stage, true) : null,
                done.mode === 'level' && passed && g.stars < 3 ? again(t('retry'), done.stage, last) : null,
                done.mode === 'level' && passed && !last ? again(t('next_level'), done.stage + 1, true) : null),
        ]);
    }

    async function share(g) {
        const day = new Date(g.day + 'T12:00:00').toLocaleDateString(I.getLang(), {day: 'numeric', month: 'long'});
        const text = t('share_text', {day, right: g.correct, total: g.total, score: fmtNumber(g.score)});
        const url = location.origin + location.pathname;
        try {
            if (navigator.share) await navigator.share({text, url});
            else {
                await navigator.clipboard.writeText(text + ' ' + url);
                toast(t('copied'), 'ok');
            }
        } catch (e) { /* closed by the player */
        }
    }

    // ---------- home ----------
    function langButton() {
        return h('button.lang-pill', {type: 'button', title: t('language'), 'aria-label': t('language'), onclick: showLanguages}, icon('globe'), I.getLang().toUpperCase());
    }

    function showLanguages() {
        const m = modal([h('h3', t('language')), h('div.menu-list.lang-list', Object.entries(I.LANGS).map(([k, label]) =>
            h('button', {
                type: 'button', onclick: () => {
                    I.setLang(k);
                    m.close();
                    showHome();
                }
            }, h('span', label), k === I.getLang() ? icon('tick') : null)))]);
    }

    function accountButton() {
        const u = A.user();
        const av = A.avatar ? A.avatar() : '';
        const Av = window.ThinkHopAvatars;
        const pic = u || av
            ? (Av ? Av.render(av, u ? u.name : 'T', 'small') : h('span.avatar.small', (u ? u.name : 'T').slice(0, 1).toUpperCase()))
            : icon('user');
        return h('button.account-btn', {type: 'button', onclick: A.open, title: t('account')},
            pic, h('span', u ? (u.username || u.name.split(' ')[0]) : t('sign_in')));
    }

    /** The next level to play: the last one unlocked. */
    const nextStage = st => st.unlocked;

    function levelCard(st) {
        const passed = Object.keys(st.stages).length;
        const stage = nextStage(st);
        const currentWorld = Math.min(E.STAGES / E.WORLD, Math.floor((stage - 1) / E.WORLD) + 1);
        const currentTier = E.worldTier ? E.worldTier(currentWorld) : 'novice';
        return h('div.level-card',
            h('div.level-head',
                h('div.level-head-title',
                    h('strong', t('mode_worlds')),
                    h('span.tag.world-tier-tag.tier-' + currentTier, t(currentTier))),
                h('span', t('worlds_done', {n: passed, m: E.STAGES}))),
            h('div.bar', {'aria-hidden': 'true'}, h('i', {style: `width:${passed / E.STAGES * 100}%`})),
            h('button.btn.btn-primary.btn-block.play', {type: 'button', onclick: () => start('level', stage)}, icon('play'), t('continue_level', {n: stage})),
            h('button.btn.btn-soft.btn-block', {type: 'button', onclick: showMap}, icon('grid'), t('all_worlds')));
    }

    function difficultyCard(st) {
        const diffs = [
            {key: 'beginner', icon: '🌱', title: t('diff_mode_beginner'), sub: t('diff_mode_beginner_desc')},
            {key: 'intermediate', icon: '⚡', title: t('diff_mode_intermediate'), sub: t('diff_mode_intermediate_desc')},
            {key: 'hero', icon: '🔥', title: t('diff_mode_hero'), sub: t('diff_mode_hero_desc')},
            {key: 'impossible', icon: '👑', title: t('diff_mode_impossible'), sub: t('diff_mode_impossible_desc')},
        ];
        return h('section.panel.diff-panel',
            h('div.diff-panel-head',
                h('strong', t('mode_difficulty')),
                h('span.muted', t('mode_difficulty_text'))),
            h('div.diff-modes-grid', diffs.map(d => {
                const best = st.best['difficulty_' + d.key] || 0;
                return h('button.diff-card.diff-' + d.key, {
                    type: 'button',
                    onclick: () => start('difficulty', d.key),
                },
                    h('span.diff-card-badge', d.icon),
                    h('div.diff-card-body',
                        h('strong', d.title),
                        h('span.diff-card-desc', d.sub)),
                    best ? h('span.diff-best', t('best', {n: fmtNumber(best)})) : h('span.diff-play', icon('play')));
            })));
    }

    function dailyCard(st) {
        const today = E.dayText();
        const done = st.dailyDone;
        const played = done ? A.games().filter(g => g.mode === 'daily' && g.day === today).pop() : null;
        const dailySaved = store.get('daily_saved');
        const saved = store.get('saved');
        const inProgress = !done && ((dailySaved && dailySaved.day === today) || (saved && saved.mode === 'daily' && saved.day === today));
        const activeDaily = (dailySaved && dailySaved.day === today) ? dailySaved : (saved && saved.mode === 'daily' && saved.day === today ? saved : null);

        let subtext = t('mode_daily_text');
        if (done && played) {
            subtext = t('mode_daily_done', {n: fmtNumber(played.score)});
        } else if (inProgress && activeDaily) {
            subtext = t('mode_daily_in_progress', {n: activeDaily.index + 1, total: activeDaily.questions.length || 10});
        }

        const action = inProgress && activeDaily
            ? () => {
                G = activeDaily;
                renderGame();
            }
            : () => start('daily');

        return h('button.daily' + (inProgress ? '.in-progress' : ''), {
            type: 'button',
            disabled: done || undefined,
            onclick: action,
        },
            h('span.daily-icon', icon(inProgress ? 'play' : 'calendar')),
            h('span.daily-text', h('strong', t('mode_daily')), h('span', subtext)),
            st.streak ? h('span.tag.hot', icon('flame'), String(st.streak)) : null);
    }

    function resumeCard(saved) {
        const isDaily = saved.mode === 'daily';
        let label = `${t('mode_' + saved.mode)} · ${t('question_n', {n: saved.index + 1})}`;
        if (saved.mode === 'level') label = `${t('level_n', {n: saved.stage})} · ${t('question_n', {n: saved.index + 1})}`;
        else if (saved.mode === 'difficulty') label = `${t('diff_mode_' + (saved.diffKey || 'beginner'))} · ${t('question_n', {n: saved.index + 1})}`;
        return h('div.resume-wrap',
            h('button.resume-card', {
                    type: 'button', onclick: () => {
                        G = saved;
                        renderGame();
                    }
                }, h('img.mini', {src: 'icons/favicon.svg', alt: ''}),
                h('div', h('strong', t('continue_game')), h('span', label))),
            isDaily ? null : h('button.resume-discard', {
                type: 'button', title: t('discard_title'), 'aria-label': t('discard_title'), onclick: async () => {
                    if (!await confirmBox(t('discard_title'), t('confirm_new_text'), t('discard_title'), t('cancel'))) return;
                    store.del('saved');
                    showHome();
                }
            }, icon('close')));
    }

    /** The home is drawn again when the server answers: the greeting must stay the same until the next game. */
    let greeting = null;

    function homeGreeting() {
        if (!greeting) greeting = {id: M.IDS[Math.floor(Math.random() * M.IDS.length)], n: Math.floor(Math.random() * 2)};
        return guide(greeting.id, 'happy', M.pick(greeting.id, 'hello', greeting.n), '.home-guide');
    }

    // ---------- PWA Install & Notifications Home Tips ----------
    let deferredInstallPrompt = null;
    window.addEventListener('beforeinstallprompt', e => {
        e.preventDefault();
        deferredInstallPrompt = e;
        if (screen === 'home') showHome();
    });
    window.addEventListener('appinstalled', () => {
        deferredInstallPrompt = null;
        store.set('tip_dismiss_install', true);
        if (screen === 'home') showHome();
    });

    function isAppInstalled() {
        if (window.matchMedia && (
            window.matchMedia('(display-mode: standalone)').matches ||
            window.matchMedia('(display-mode: fullscreen)').matches ||
            window.matchMedia('(display-mode: minimal-ui)').matches
        )) return true;
        if (window.navigator && window.navigator.standalone === true) return true;
        if (document.referrer && document.referrer.includes('android-app://')) return true;
        if (store.get('app_installed') === true) return true;
        return false;
    }

    if (typeof navigator !== 'undefined' && navigator.getInstalledRelatedApps) {
        navigator.getInstalledRelatedApps().then(apps => {
            if (apps && apps.length > 0) {
                store.set('app_installed', true);
                if (screen === 'home') showHome();
            }
        }).catch(() => {});
    }

    function areNotificationsActive() {
        if (!('Notification' in window)) return false;
        if (Notification.permission !== 'granted') return false;
        if (store.get('notify_device') === false) return false;
        return true;
    }

    if (typeof navigator !== 'undefined' && 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window && Notification.permission === 'granted') {
        navigator.serviceWorker.getRegistration().then(reg => {
            if (!reg) return;
            reg.pushManager.getSubscription().then(sub => {
                const hasSub = !!sub;
                if (store.get('notify_device') !== hasSub) {
                    store.set('notify_device', hasSub);
                    if (screen === 'home') showHome();
                }
            }).catch(() => {});
        }).catch(() => {});
    }

    function showInstallGuide() {
        const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent || '');
        const m = modal([
            h('h3', t('install_modal_title')),
            h('p.muted', t('tip_install_text')),
            h('div.install-steps',
                isIOS ? [
                    h('div.install-step', h('span.install-step-num', '1'), h('span', t('install_ios_step1'))),
                    h('div.install-step', h('span.install-step-num', '2'), h('span', t('install_ios_step2'))),
                    h('div.install-step', h('span.install-step-num', '3'), h('span', t('install_ios_step3'))),
                ] : [
                    h('div.install-step', h('span.install-step-num', '1'), h('span', t('install_generic_step'))),
                    h('div.install-step', h('span.install-step-num', '2'), h('span', t('install_ios_step3'))),
                ]
            ),
            h('div.actions',
                h('button.btn.btn-primary.btn-block', {type: 'button', onclick: () => m.close()}, t('got_it')))
        ]);
    }

    async function onInstallAction(e) {
        if (e) e.stopPropagation();
        if (deferredInstallPrompt) {
            try {
                deferredInstallPrompt.prompt();
                const choice = await deferredInstallPrompt.userChoice;
                if (choice && choice.outcome === 'accepted') {
                    deferredInstallPrompt = null;
                    store.set('tip_dismiss_install', true);
                    if (screen === 'home') showHome();
                }
            } catch (err) {
                showInstallGuide();
            }
        } else {
            showInstallGuide();
        }
    }

    function onNotifyAction(e) {
        if (e) e.stopPropagation();
        if (A.user && A.user()) {
            if (S && S.showNotifications) {
                S.showNotifications();
            }
        } else {
            const m = modal([
                h('h3', t('tip_notify_title')),
                h('p.muted', t('tip_notify_signin_hint')),
                h('div.actions',
                    h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('cancel')),
                    h('button.btn.btn-primary', {type: 'button', onclick: () => { m.close(); A.open(); }}, t('sign_in')))
            ]);
        }
    }

    function makeHomeTip(opts) {
        let card;
        const closeBtn = h('button.home-tip-close', {
            type: 'button',
            title: t('tip_hide'),
            'aria-label': t('tip_hide'),
            onclick: e => {
                e.stopPropagation();
                store.set(opts.dismissKey, true);
                card.classList.add('dismissing');
                setTimeout(() => {
                    const parent = card.parentElement;
                    card.remove();
                    if (parent && !parent.children.length) parent.remove();
                }, 180);
            }
        }, icon('close'));

        card = h('div.home-tip', {
            role: 'region',
            'aria-label': opts.title,
            onclick: () => opts.onAction()
        },
            h('div.home-tip-icon', icon(opts.iconName)),
            h('div.home-tip-body',
                h('div.home-tip-head',
                    h('span.home-tip-badge', icon('bulb'), t('tip_label')),
                    h('strong.home-tip-title', opts.title)),
                h('p.home-tip-text', opts.text),
                h('div.home-tip-actions',
                    h('button.home-tip-btn', {
                        type: 'button',
                        onclick: e => opts.onAction(e)
                    }, icon(opts.iconName), h('span', opts.btnLabel)))),
            closeBtn);

        return card;
    }

    function homeTips() {
        const tips = [];
        const isInstalled = isAppInstalled();
        const notifyActive = areNotificationsActive();

        if (!isInstalled && !store.get('tip_dismiss_install')) {
            tips.push(makeHomeTip({
                id: 'install',
                iconName: 'download',
                title: t('tip_install_title'),
                text: t('tip_install_text'),
                btnLabel: t('tip_install_btn'),
                dismissKey: 'tip_dismiss_install',
                onAction: onInstallAction,
            }));
        }

        if (!notifyActive && !store.get('tip_dismiss_notify')) {
            tips.push(makeHomeTip({
                id: 'notify',
                iconName: 'bell',
                title: t('tip_notify_title'),
                text: t('tip_notify_text'),
                btnLabel: t('tip_notify_btn'),
                dismissKey: 'tip_dismiss_notify',
                onAction: onNotifyAction,
            }));
        }

        if (!tips.length) return null;
        return h('div.home-tips', tips);
    }

    const quickLink = (name, label, open) => h('button.quick-link', {type: 'button', onclick: open}, icon(name), h('span', label));

    function showHome() {
        screen = 'home';
        G = null;
        const st = stats();
        const today = E.dayText();
        let saved = store.get('saved');
        if (saved && (saved.v !== 2 || (saved.mode === 'daily' && saved.day !== today))) {
            store.del('saved'); // a game of the first version or an expired daily game
            saved = null;
        }
        const dailySaved = store.get('daily_saved');
        if (dailySaved && dailySaved.day !== today) {
            store.del('daily_saved');
        }
        app.replaceChildren(h('div.home',
            h('div.topbar', themeTabs(), accountButton(), langButton(), h('button.icon-btn.theme-cycle', {
                type: 'button', title: t('theme'), 'aria-label': t('theme'), onclick: cycleTheme,
            }, icon('moon'))),
            h('section.hero', h('img.logo', {src: 'icons/favicon.svg', alt: ''}), h('h1', NAME), h('p', t('subtitle')),
                h('button.total', {type: 'button', title: t('avatar_title'), onclick: () => A.openAvatarShop()}, icon('star'), t('total_points', {n: fmtNumber(st.balance)}))),
            homeGreeting(),
            h('main.home-main',
                saved ? resumeCard(saved) : null,
                homeTips(),
                h('section.panel', levelCard(st)),
                difficultyCard(st),
                h('div.modes',
                    dailyCard(st),
                    h('button.survival', {type: 'button', onclick: () => start('survival')},
                        h('span.survival-icon', icon('heart')),
                        h('span.daily-text', h('strong', t('mode_survival')), h('span', st.best.survival ? t('best', {n: fmtNumber(st.best.survival)}) : t('mode_survival_text'))))),
                h('nav.quick', {'aria-label': t('home')},
                    quickLink('trophy', t('board'), S.showBoard),
                    quickLink('user', t('friends'), S.showFriends),
                    quickLink('chart', t('stats_btn'), showStats))),
            h('footer.footer',
                // Same credit block as the other games
                h('div.credit-block',
                    h('a.credit', {href: 'https://www.saveriomorelli.com/', target: '_blank', rel: 'noopener'}, h('span', t('credit_by')), h('span.credit-name', 'Saverio Morelli')),
                    h('span.credit-ai', {html: '<svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.2 6.6 6.8 2.4-6.8 2.4L12 20.5l-2.2-6.6L3 11.5l6.8-2.4z"/></svg>'}, h('span', t('credit_ai')))),
                h('div.footer-actions',
                    h('a.version-btn.privacy-link', {href: 'privacy.html'}, 'Privacy'),
                    h('a.version-btn.privacy-link', {href: 'admin.html'}, t('admin_link')),
                    h('button.donate-btn', {type: 'button', onclick: A.showDonate, title: t('donate_title')}, icon('heart'), h('span', t('donate'))),
                    h('button.version-btn', {type: 'button', onclick: showReleases}, `${t('version')} ${VERSION}`)))));
        document.title = 'ThinkHop – Saverio Morelli';
    }

    // ---------- all the levels / worlds ----------
    function showMap() {
        screen = 'map';
        const st = stats();
        const totalWorlds = Math.round(E.STAGES / E.WORLD);
        const worlds = Array.from({length: totalWorlds}, (_, w) => {
            const first = w * E.WORLD + 1;
            const tierKey = E.worldTier ? E.worldTier(w + 1) : 'novice';
            return h('section.world',
                h('div.world-header',
                    h('h2', face(worldHost(w), 'happy', '.tiny'), t('world_n', {n: w + 1})),
                    h('span.tag.world-tier-tag.tier-' + tierKey, t(tierKey))),
                h('div.stage-grid', Array.from({length: E.WORLD}, (_, i) => {
                    const n = first + i, stars = st.stages[n] || 0, open = n <= st.unlocked;
                    return h('button.stage' + (open ? '' : '.locked') + (n === st.unlocked && !stars ? '.current' : ''), {
                            type: 'button', title: open ? t('level_n', {n}) : t('locked'), 'aria-label': open ? t('level_n', {n}) : t('locked'),
                            onclick: () => (open ? start('level', n) : toast(t('locked'))),
                        }, open ? h('b', String(n)) : icon('lock'),
                        open ? h('span.mini-stars', [0, 1, 2].map(k => h('i' + (k < stars ? '.on' : '')))) : null);
                })));
        });
        app.replaceChildren(h('div.map',
            h('header.game-bar',
                h('button.icon-btn', {type: 'button', title: t('home'), 'aria-label': t('home'), onclick: showHome}, icon('back')),
                h('div.title', h('strong', t('mode_worlds')), h('span', t('worlds_done', {n: Object.keys(st.stages).length, m: E.STAGES}))),
                h('div.score-pill', {title: t('balance')}, icon('star'), h('b', fmtNumber(st.balance)))),
            worlds));
        window.scrollTo(0, 0);
    }

    function showStats() {
        const st = stats();
        const m = modal([h('h3', t('stats_title')),
            st.games ? [
                h('div.stat-grid',
                    statTile(t('stat_games'), fmtNumber(st.games)),
                    statTile(t('balance'), fmtNumber(st.balance)),
                    statTile(t('stat_level'), String(st.unlocked)),
                    statTile(t('stat_right'), Math.round(st.correct / Math.max(1, st.total) * 100) + '%'),
                    statTile(t('stat_streak'), String(st.streak))),
                h('h4.stat-title', t('stat_best')),
                h('div.best-list', ['level', 'daily', 'survival', 'difficulty_beginner', 'difficulty_intermediate', 'difficulty_hero', 'difficulty_impossible'].filter(k => st.best[k]).map(k => {
                    const label = k.startsWith('difficulty_') ? `${t('mode_difficulty')}: ${t('diff_mode_' + k.replace('difficulty_', ''))}` : t('mode_' + k);
                    return h('div', h('span', label), h('b', fmtNumber(st.best[k])));
                })),
                h('h4.stat-title', t('stat_cats')),
                h('div.cat-list', E.CATS.filter(c => st.cats[c] && st.cats[c][1]).map(c => {
                    const [right, total] = st.cats[c];
                    const share = Math.round(right / total * 100);
                    return h('div.cat-row', h('div.cat-label', h('span', t('cat_' + c)), h('b', share + '%')), h('div.bar', h('i', {style: `width:${share}%`})));
                })),
            ] : h('p.muted', t('stats_empty')),
            h('div.actions', h('button.btn.btn-primary', {type: 'button', onclick: () => m.close()}, t('close')))]);
    }

    const statTile = (label, value) => h('div.stat', h('b', value), h('span', label));

    function showReleases() {
        const L = I.getLang();
        modal([h('h3', t('release_notes')), h('div.release', (window.ThinkHopReleases || []).map(r => [h('h4', `${r.version} · ${new Date(r.date).toLocaleDateString(L, {
            year: 'numeric', month: 'short', day: 'numeric',
        })}`), h('ul', (r.notes[L] || r.notes.en).map(n => h('li', n)))]))]);
    }

    // ---------- keyboard ----------
    document.addEventListener('keydown', e => {
        if (screen !== 'game' || !G || document.querySelector('.modal') || e.metaKey || e.ctrlKey || e.altKey) return;
        const n = Number(e.key);
        if (!G.answered && n >= 1 && n <= G.questions[G.index].options.length) choose(n - 1);
        else if (!G.answered && e.key.toLowerCase() === 'h') useHint();
        else if (e.key === '?') showHelp(G.questions[G.index]);
    });

    // Android back key (see MainActivity): true means the page used it
    window.onAndroidBack = function () {
        const open = document.querySelectorAll('.modal');
        if (open.length) {
            open[open.length - 1].dispatchEvent(new MouseEvent('click'));
            return true;
        }
        if (screen === 'game') {
            leave();
            return true;
        }
        if (screen === 'map') {
            showHome();
            return true;
        }
        return false;
    };

    matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
    window.addEventListener('beforeunload', save);
    window.addEventListener('pagehide', save);
    applyTheme();
    showHome();
    A.start(() => {
        if (screen === 'home') showHome();
    });
})();

// Lets the game open without a connection (see sw.js)
if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => {
}));
