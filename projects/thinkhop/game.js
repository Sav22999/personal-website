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

    /** The wallet also counts what an administrator changed on the account (see admin.player.points). */
    const stats = () => {
        const s = E.stats(A.games());
        const u = A.user();
        if (u && u.pointsAdjust) s.balance = Math.max(0, s.balance + u.pointsAdjust);
        return s;
    };
    /** Points that can be spent now: the wallet plus what the current game has earned so far. */
    const balance = () => stats().balance + (G ? G.score - G.spent : 0);

    function begin(mode, stage) {
        G = E.newGame(mode, stage, Math.floor(Math.random() * 2147483647), seen());
        store.set('saved', G);
        if (mode === 'level') worldIntro(stage, renderGame);
        else renderGame();
    }

    async function start(mode, stage) {
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

    function optionText(q, o) {
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

    function questionBody(q) {
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
        const mode = G.mode === 'level' ? t('level_n', {n: G.stage}) : t('mode_' + G.mode);
        const counter = G.mode === 'survival' ? t('question_n', {n: G.index + 1}) : t('question_of', {n: G.index + 1, m: G.questions.length});
        const lives = G.mode === 'survival'
            ? h('span.lives', {role: 'img', 'aria-label': t('lives_left', {n: G.lives})}, Array.from({length: E.LIVES}, (_, i) => h('span' + (i < G.lives ? '.on' : ''), icon('heart')))) : null;
        const options = h('div.options' + (q.options.length === 5 ? '.five' : '') + (q.view === 'riddle' || q.view === 'facts' ? '.long' : '') + (q.pics ? '.pics' : ''), q.options.map((o, i) => {
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
                    G.streak > 1 ? h('span.tag.hot', icon('flame'), String(G.streak)) : null),
                G.answered ? null : questionGuide(q),
                h('h1.prompt', promptText(q)),
                questionBody(q),
                G.answered ? null : h('div.timer', h('span.timer-face', face(M.hostOf(q.cat), 'think')), h('div.speed', {'aria-hidden': 'true'}, h('i', {style: `animation-duration:${q.limit}s;animation-delay:-${G.clock}s`})), h('span.clock', {role: 'timer'})),
                options,
                G.answered ? h('section.feedback' + (right ? '.good' : '.bad'), {role: 'status'},
                        h('h2', icon(right ? 'tick' : 'close'), G.picked < 0 ? t('time_up') : t(right ? 'right' : 'wrong'), right ? h('span.gain', t('plus_points', {n: G.gained})) : null),
                        reaction(q, right),
                        whyLines(q),
                        h('button.btn.btn-primary.btn-block', {type: 'button', onclick: advance}, E.isOver(G) ? t('see_result') : t('next'), icon('arrow')))
                    : h('button.btn.btn-soft.hint-btn', {
                        type: 'button', title: t('hint_title'), disabled: G.removed.length > 0 || undefined, onclick: useHint,
                    }, icon('bulb'), t('hint_cost', {n: E.hintCost(G)})))));
        document.title = `${NAME} – ${counter}`;
        startedAt = performance.now();
        worried = false;
        rememberQuestion(q);
        runClock(q);
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
        showHome();
        const title = done.mode === 'level' ? t(passed ? 'level_passed' : 'level_failed', {n: done.stage}) : t('over_' + done.mode);
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
                done.mode !== 'level' && done.score > before && before > 0 ? h('p.record', t('new_record')) : null,
                h('p.bubble.result-line', M.say(host, passed ? 'pass' : 'fail')),
                h('p.muted', t('result_right', {n: done.correct, m: g.total})),
                passed ? h('p.muted', [t('result_streak', {n: done.bestStreak}), t('result_time', {t: fmtTime(done.seconds)})].join(' · ')) : h('p.muted', t('level_failed_text', {n: needed})),
                done.mode === 'level' && passed && last ? h('p.muted', t('all_done')) : null),
            h('div.actions',
                done.mode === 'daily' ? h('button.btn.btn-soft', {type: 'button', onclick: () => share(g)}, icon('share'), t('share')) : null,
                h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('home')),
                done.mode === 'survival' ? again(t('play_again'), 0, true) : null,
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
        return h('button.account-btn', {type: 'button', onclick: A.open, title: t('account')},
            u ? h('span.avatar.small', u.name.slice(0, 1).toUpperCase()) : icon('user'), h('span', u ? (u.username || u.name.split(' ')[0]) : t('sign_in')));
    }

    /** The next level to play: the last one unlocked. */
    const nextStage = st => st.unlocked;

    function levelCard(st) {
        const passed = Object.keys(st.stages).length;
        const stage = nextStage(st);
        return h('div.level-card',
            h('div.level-head', h('strong', t('mode_level')), h('span', t('levels_done', {n: passed, m: E.STAGES}))),
            h('div.bar', {'aria-hidden': 'true'}, h('i', {style: `width:${passed / E.STAGES * 100}%`})),
            h('button.btn.btn-primary.btn-block.play', {type: 'button', onclick: () => start('level', stage)}, icon('play'), t('continue_level', {n: stage})),
            h('button.btn.btn-soft.btn-block', {type: 'button', onclick: showMap}, icon('grid'), t('all_levels')));
    }

    function dailyCard(st) {
        const today = E.dayText();
        const done = st.dailyDone;
        const played = done ? A.games().filter(g => g.mode === 'daily' && g.day === today).pop() : null;
        return h('button.daily', {type: 'button', disabled: done || undefined, onclick: () => start('daily')},
            h('span.daily-icon', icon('calendar')),
            h('span.daily-text', h('strong', t('mode_daily')), h('span', done ? t('mode_daily_done', {n: fmtNumber(played.score)}) : t('mode_daily_text'))),
            st.streak ? h('span.tag.hot', icon('flame'), String(st.streak)) : null);
    }

    function resumeCard(saved) {
        return h('div.resume-wrap',
            h('button.resume-card', {
                    type: 'button', onclick: () => {
                        G = saved;
                        renderGame();
                    }
                }, h('img.mini', {src: 'icons/favicon.svg', alt: ''}),
                h('div', h('strong', t('continue_game')), h('span', `${saved.mode === 'level' ? t('level_n', {n: saved.stage}) : t('mode_' + saved.mode)} · ${t('question_n', {n: saved.index + 1})}`))),
            h('button.resume-discard', {
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

    const quickLink = (name, label, open) => h('button.quick-link', {type: 'button', onclick: open}, icon(name), h('span', label));

    function showHome() {
        screen = 'home';
        G = null;
        const st = stats();
        let saved = store.get('saved');
        if (saved && saved.v !== 2) {
            store.del('saved'); // a game of the first version: its questions have no difficulty and no clock
            saved = null;
        }
        app.replaceChildren(h('div.home',
            h('div.topbar', themeTabs(), accountButton(), langButton(), h('button.icon-btn.theme-cycle', {
                type: 'button', title: t('theme'), 'aria-label': t('theme'), onclick: cycleTheme,
            }, icon('moon'))),
            h('section.hero', h('img.logo', {src: 'icons/favicon.svg', alt: ''}), h('h1', NAME), h('p', t('subtitle')),
                h('div.total', {title: t('balance')}, icon('star'), t('total_points', {n: fmtNumber(st.balance)}))),
            homeGreeting(),
            h('main.home-main',
                saved ? resumeCard(saved) : null,
                h('section.panel', levelCard(st)),
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

    // ---------- all the levels ----------
    function showMap() {
        screen = 'map';
        const st = stats();
        const worlds = Array.from({length: E.STAGES / E.WORLD}, (_, w) => {
            const first = w * E.WORLD + 1;
            return h('section.world',
                h('h2', face(worldHost(w), 'happy', '.tiny'), t('world_n', {n: w + 1})),
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
                h('div.title', h('strong', t('mode_level')), h('span', t('levels_done', {n: Object.keys(st.stages).length, m: E.STAGES}))),
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
                h('div.best-list', ['level', 'daily', 'survival'].filter(k => st.best[k]).map(k =>
                    h('div', h('span', t('mode_' + k)), h('b', fmtNumber(st.best[k]))))),
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
