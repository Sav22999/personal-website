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

    // ---------- texts and pictures of a question: views.js, shared with the admin panel ----------
    const {line, riddleText, optionText, whyLines, promptText} = window.ThinkHopViews;
    const questionBody = q => window.ThinkHopViews.questionBody(q, G.answered);

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
                    A.enabled() && window.ThinkHopReports ? h('button.report-btn', {
                        type: 'button', title: t('report_btn'), 'aria-label': t('report_btn'), onclick: () => reportQuestion(q),
                    }, icon('flag')) : null,
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

    /** Reporting a question stops the clock while the form is open, like the "?" guide. */
    function reportQuestion(q) {
        const active = G && !G.answered;
        window.ThinkHopReports.open(q, G ? G.picked : -1, {
            onopen: () => {
                if (!active) return;
                G.clock = elapsed();
                stopClock();
                const bar = app.querySelector('.speed i');
                if (bar) bar.style.animationPlayState = 'paused';
            },
            onclose: () => {
                if (!active || !G || G.answered || screen !== 'game') return;
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
    let reportsChecked = false;
    A.start(() => {
        if (screen === 'home') showHome();
        if (!reportsChecked && A.user() && window.ThinkHopReports) {
            reportsChecked = true;
            window.ThinkHopReports.checkClosed();
        }
    });
})();

// Lets the game open without a connection (see sw.js)
if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => {
}));
