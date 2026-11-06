/* LetterHop — game UI: home, word ladder, keyboard, tips, undo, timer, saved games and points. */
(function () {
    'use strict';

    const E = window.LetterHopEngine;
    const I = window.LetterHopI18n;
    const t = I.t;
    const app = document.getElementById('app');
    const LEVELS = Object.keys(E.LEVELS);
    const VERSION = (window.LetterHopReleases && window.LetterHopReleases[0].version) || '1.0.0';
    const NAME = 'LetterHop';

    // ---------- storage ----------
    const store = {
        get(k, d) {
            try {
                const v = localStorage.getItem('letterhop.' + k);
                return v === null ? d : JSON.parse(v);
            } catch (e) {
                return d;
            }
        },
        set(k, v) {
            try {
                localStorage.setItem('letterhop.' + k, JSON.stringify(v));
            } catch (e) { /* private mode */
            }
        },
        del(k) {
            try {
                localStorage.removeItem('letterhop.' + k);
            } catch (e) { /* private mode */
            }
        },
    };
    const settings = Object.assign({match: true, timer: true}, store.get('settings', {}));
    const saveSettings = () => store.set('settings', settings);

    // ---------- DOM helpers ----------
    function h(tag, attrs, ...kids) {
        const m = /^([a-z0-9]+)?((?:[.#][\w-]+)*)$/i.exec(tag);
        const el = document.createElement(m[1] || 'div');
        m[2].replace(/([.#])([\w-]+)/g, (_, k, v) => {
            if (k === '.') el.classList.add(v); else el.id = v;
        });
        if (attrs && (attrs instanceof Node || Array.isArray(attrs) || typeof attrs !== 'object')) {
            kids.unshift(attrs);
            attrs = null;
        }
        for (const [k, v] of Object.entries(attrs || {})) {
            if (v == null || v === false) continue;
            if (k.startsWith('on')) el.addEventListener(k.slice(2), v);
            else if (k === 'html') el.innerHTML = v;
            else el.setAttribute(k, v === true ? '' : v);
        }
        for (const c of kids.flat(Infinity)) if (c != null && c !== false) el.append(c instanceof Node ? c : document.createTextNode(String(c)));
        return el;
    }

    const P = {
        back: '<path d="M15 18l-6-6 6-6"/>',
        more: '<circle cx="12" cy="5" r="1.4"/><circle cx="12" cy="12" r="1.4"/><circle cx="12" cy="19" r="1.4"/>',
        undo: '<path d="M9 14L4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 010 11H11"/>',
        erase: '<path d="M20 20H9L4 15a2 2 0 010-2.8L13.2 3a2 2 0 012.8 0L21 8a2 2 0 010 2.8L12 20"/><path d="M8.5 9.5l6 6"/>',
        pen: '<path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.6 7.6"/><circle cx="11" cy="11" r="2"/>',
        check: '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
        bulb: '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7c.6.5 1 1.3 1 2.1V17h6v-.2c0-.8.4-1.6 1-2.1A7 7 0 0012 2z"/>',
        pause: '<rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/>',
        play: '<path d="M6 4l14 8-14 8z"/>',
        plus: '<path d="M12 5v14M5 12h14"/>',
        restart: '<path d="M3 12a9 9 0 109-9 9.7 9.7 0 00-6.7 2.7L3 8"/><path d="M3 3v5h5"/>',
        grid: '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/>',
        magic: '<path d="M15 4V2M15 16v-2M8 9h2M20 9h2M17.8 11.8L19 13M15 9h0M17.8 6.2L19 5M3 21l9-9M12.2 6.2L11 5"/>',
        gear: '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1.1-1.5 1.7 1.7 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1.1 1.7 1.7 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.8.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.8V9a1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z"/>',
        book: '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/>',
        home: '<path d="M3 11l9-8 9 8v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1z"/>',
        globe: '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>',
        moon: '<path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/>',
        trophy: '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0z"/><path d="M17 5h3v2a3 3 0 01-3 3M7 5H4v2a3 3 0 003 3"/>',
        help: '<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/>',
        tick: '<path d="M5 12l5 5L20 7"/>',
        close: '<path d="M18 6L6 18M6 6l12 12"/>',
        target: '<path d="M12.8 4.2A9 9 0 1 0 19.8 11.2"/><path d="M11.9 8.6A4.5 4.5 0 1 0 15.4 12.1"/><path d="M11 13l8.5-8.5"/><path d="M16.5 4.5v3h3L23 4h-3V1z"/>',
        star: '<path d="M12 2.8l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.6l-5.8 3.1 1.1-6.5L2.6 9.6l6.5-.9z"/>',
        flag: '<path d="M5 21V4m0 0h12l-2.5 4L17 12H5"/>',
        backspace: '<path d="M21 5H9l-6 7 6 7h12a1 1 0 001-1V6a1 1 0 00-1-1z"/><path d="M16 9.5l-5 5M11 9.5l5 5"/>',
        info: '<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>',
        heart: '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>',
    };
    const icon = name => {
        const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        s.setAttribute('viewBox', '0 0 24 24');
        s.setAttribute('class', 'i');
        s.setAttribute('aria-hidden', 'true');
        s.innerHTML = P[name];
        return s;
    };

    let toastTimer;

    function toast(msg, kind) {
        const el = document.getElementById('toast');
        el.textContent = msg;
        el.className = 'toast show' + (kind ? ' ' + kind : '');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            el.className = 'toast';
        }, 2200);
    }

    function modal(content, opts) {
        opts = opts || {};
        const box = h('div.box', {role: 'dialog', 'aria-modal': 'true'}, content);
        const m = h('div.modal', {
            onclick: e => {
                if (e.target === m && !opts.persistent) close();
            }
        }, box);
        document.body.appendChild(m);
        const key = e => {
            if (e.key === 'Escape' && !opts.persistent) close();
        };
        document.addEventListener('keydown', key, true);

        function close() {
            document.removeEventListener('keydown', key, true);
            m.remove();
            if (opts.onclose) opts.onclose();
        }

        return {close, box};
    }

    function confirmBox(title, text, okLabel) {
        return new Promise(res => {
            const m = modal([h('h3', title), h('p.muted', text), h('div.actions',
                h('button.btn.btn-ghost', {
                    onclick: () => {
                        m.close();
                        res(false);
                    }
                }, t('cancel')),
                h('button.btn.btn-primary', {
                    onclick: () => {
                        res(true);
                        m.close();
                    }
                }, okLabel))], {onclose: () => res(false)});
        });
    }

    const fmtTime = s => {
        s = Math.floor(s);
        const hh = Math.floor(s / 3600), mm = Math.floor(s / 60) % 60, ss = s % 60;
        return (hh ? hh + ':' + String(mm).padStart(2, '0') : mm) + ':' + String(ss).padStart(2, '0');
    };

    // ---------- theme ----------
    function applyTheme() {
        const th = store.get('theme', 'auto');
        if (th === 'auto') document.documentElement.removeAttribute('data-theme'); else document.documentElement.setAttribute('data-theme', th);
        const dark = th === 'dark' || (th === 'auto' && matchMedia('(prefers-color-scheme: dark)').matches);
        document.querySelectorAll('meta[name=theme-color]').forEach(m => {
            m.removeAttribute('media');
            m.content = dark ? '#16100B' : '#FFF8F1';
        });
        document.querySelectorAll('.theme-tabs button').forEach(b => {
            const on = b.dataset.mode === th;
            b.classList.toggle('sel', on);
            b.setAttribute('aria-checked', String(on));
        });
    }

    // =================================================================== state
    let G = null;          // current game
    let dict = null;       // dictionary of the current game
    let sel = -1;          // letter of the current word chosen for the change
    let tip = null;        // { pos, word, shown }
    let refused = null;    // word just refused, shown for a moment in the current row
    let timer = null;
    let screen = 'home';

    const current = () => G.chain[G.chain.length - 1];
    /** Words added so far: the start does not count, and neither does the final word once reached. */
    const wordsUsed = () => G.chain.length - 1 - (current() === G.target ? 1 : 0);
    const up = w => w.toUpperCase();

    function save() {
        if (G && !G.done) store.set('game', G);
    }

    function loadSaved() {
        const g = store.get('game', null);
        return g && g.chain && g.chain.length && !g.done ? g : null;
    }

    function newGame(level, len) {
        const key = `recent.${dict.lang}.${len}.${level}`;
        const recent = store.get(key, []);
        const p = E.pickPuzzle(dict, len, level, recent);
        recent.push(p.key);
        store.set(key, recent.slice(-150));
        G = {
            lang: dict.lang,
            len,
            level,
            min: E.LEVELS[level],
            start: p.start,
            target: p.target,
            chain: [p.start],
            tips: 0,
            elapsed: 0,
            paused: false,
            done: false
        };
        sel = -1;
        tip = null;
        refused = null;
        store.set('level', level);
        store.set('len', len);
        save();
    }

    // ---------- moves ----------
    function refuse(word, message) {
        refused = word;
        toast(message, 'bad');
        paint();
        setTimeout(() => {
            refused = null;
            paint();
        }, 650);
    }

    function letter(ch) {
        if (!G || G.done || G.paused) return;
        if (sel < 0) {
            toast(t('pick_tile'));
            pulse();
            return;
        }
        const word = current();
        const c = ch.toLowerCase();
        if (word[sel] === c) {
            toast(t('same_letter'));
            return;
        }
        const next = word.slice(0, sel) + c + word.slice(sel + 1);
        if (!E.isWord(dict, next)) {
            refuse(next, t('not_word', {w: up(next)}));
            return;
        }
        if (G.chain.includes(next)) {
            refuse(next, t('used_word', {w: up(next)}));
            return;
        }
        hop(next);
    }

    function hop(next) {
        G.chain.push(next);
        sel = -1;
        tip = null;
        refused = null;
        save();
        if (next === G.target) return win();
        paint();
    }

    function undo() {
        if (!G || G.done || G.paused || G.chain.length < 2) return;
        G.chain.pop();
        sel = -1;
        tip = null;
        save();
        paint();
    }

    function pulse() {
        const row = document.querySelector('.row.current');
        if (!row) return;
        row.classList.remove('nudge');
        void row.offsetWidth;
        row.classList.add('nudge');
    }

    const tipAffordable = () => E.canTip(G.level, wordsUsed(), G.tips);

    /** First tip: which letter. Second tip: the whole word. */
    function showTip() {
        if (!G || G.done || G.paused) return;
        if (tip) {
            tip = null;
            paint();
            return;
        }
        if (!tipAffordable()) {
            toast(t('no_points'), 'bad');
            return;
        }
        const word = E.nextStep(dict, current(), G.target, G.chain);
        tip = {word, pos: E.changedAt(current(), word), shown: false};
        sel = tip.pos;
        G.tips++;
        save();
        paint();
    }

    function revealTip() {
        if (!tipAffordable()) {
            toast(t('no_points'), 'bad');
            return;
        }
        tip.shown = true;
        G.tips++;
        save();
        paint();
    }

    async function restart() {
        if (!await confirmBox(t('confirm_restart'), t('confirm_restart_text'), t('restart_btn'))) return;
        Object.assign(G, {chain: [G.start], tips: 0, elapsed: 0});
        sel = -1;
        tip = null;
        save();
        paint();
    }

    const chainView = words => h('div.chain', words.map((w, i) => [i ? h('span.chain-arrow', '→') : null, h('span' + (i === 0 || i === words.length - 1 ? '.end' : ''), up(w))]));

    function win() {
        G.done = true;
        stopTimer();
        const used = wordsUsed();
        const s = E.score(G.level, used, G.tips);
        const stats = store.get('stats', {});
        const key = G.len + '.' + G.level;
        const st = stats[key] || {solved: 0, perfect: 0};
        st.solved++;
        if (s.stars === 3) st.perfect++;
        stats[key] = st;
        stats.total = (stats.total || 0) + s.points;
        store.set('stats', stats);
        store.del('game');
        const shortest = s.extra ? E.route(dict, G.start, G.target) : null;
        sel = -1;
        paint();
        setTimeout(() => {
            const m = modal(h('div.win-box',
                h('div.stars', {
                    role: 'img',
                    'aria-label': `${s.stars}/3`
                }, [1, 2, 3].map(n => h('span' + (n <= s.stars ? '.on' : ''), icon('star')))),
                h('h3', t(s.stars === 3 ? 'win_perfect' : 'win_t')),
                h('div.score', h('b', String(s.points)), h('span', t('earned'))),
                h('p.muted', t('win_words', {w: used, m: G.min})),
                s.wordPenalty ? h('p.penalty', t('pen_words', {p: s.wordPenalty})) : null,
                s.tipPenalty ? h('p.penalty', t('pen_tips', {p: s.tipPenalty})) : null,
                h('h4', t('your_chain')), chainView(G.chain),
                shortest ? [h('h4', t('best_chain')), chainView(shortest)] : null,
                h('div.stack', {style: 'margin-top:18px'},
                    h('button.btn.btn-primary.btn-block', {
                        type: 'button', onclick: () => {
                            m.close();
                            startGame(G.level, G.len);
                        }
                    }, icon('plus'), t('play_again')),
                    h('button.btn.btn-ghost.btn-block', {
                        type: 'button', onclick: () => {
                            m.close();
                            showHome();
                        }
                    }, t('back_home')))), {persistent: true});
        }, 450);
    }

    async function giveUp() {
        if (!await confirmBox(t('giveup_t'), t('giveup_text'), t('giveup_btn'))) return;
        G.done = true;
        stopTimer();
        store.del('game');
        const path = E.route(dict, G.start, G.target);
        const m = modal(h('div.win-box', h('h3', t('solution_t')), chainView(path),
            h('div.stack', {style: 'margin-top:18px'},
                h('button.btn.btn-primary.btn-block', {
                    type: 'button', onclick: () => {
                        m.close();
                        startGame(G.level, G.len);
                    }
                }, icon('plus'), t('play_again')),
                h('button.btn.btn-ghost.btn-block', {
                    type: 'button', onclick: () => {
                        m.close();
                        showHome();
                    }
                }, t('back_home')))), {persistent: true});
    }

    // ---------- timer ----------
    function startTimer() {
        stopTimer();
        let last = Date.now();
        timer = setInterval(() => {
            const now = Date.now();
            if (G && !G.paused && !G.done && document.visibilityState === 'visible') {
                G.elapsed += (now - last) / 1000;
                const el = document.getElementById('time');
                if (el) el.textContent = fmtTime(G.elapsed);
                if (Math.floor(G.elapsed) % 5 === 0) save();
            }
            last = now;
        }, 500);
    }

    function stopTimer() {
        clearInterval(timer);
        timer = null;
    }

    function setPaused(p) {
        if (!G || G.done) return;
        G.paused = p;
        save();
        paint();
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden' && G && !G.done && screen === 'game') {
            G.paused = true;
            save();
            paint();
        }
    });

    // =================================================================== screens
    async function withWords(lang, then) {
        app.replaceChildren(h('div.loading', t('generating')));
        try {
            dict = await E.load(lang, VERSION);
        } catch (e) {
            showHome();
            toast(t('load_error'), 'bad');
            return;
        }
        then();
        screen = 'game';
        renderGame();
        startTimer();
    }

    function startGame(level, len) {
        withWords(I.getLang(), () => newGame(level, len));
    }

    function resume(saved) {
        withWords(saved.lang, () => {
            G = saved;
            G.paused = false;
            sel = -1;
            tip = null;
            refused = null;
        });
    }

    function exampleView() {
        const words = t('example');
        return h('div.example', {'aria-hidden': 'true'}, words.map((w, i) => {
            const at = i ? E.changedAt(words[i - 1], w) : -1;
            return h('div.example-row', [...w].map((c, k) => h('span' + (k === at ? '.changed' : ''), c)));
        }));
    }

    function showHome() {
        screen = 'home';
        stopTimer();
        const saved = loadSaved();
        const stats = store.get('stats', {});
        let level = store.get('level', 'easy');
        let len = store.get('len', 5);
        const levels = h('div.levels', {role: 'radiogroup', 'aria-label': t('level')});
        const levelNote = lv => {
            const st = stats[len + '.' + lv];
            return st ? `${t('solved')} ${st.solved} · ${t('perfect')} ${st.perfect}` : t('min_words', {n: E.LEVELS[lv]});
        };

        function drawLevels() {
            levels.replaceChildren(...LEVELS.map((lv, k) => h('button.level' + (lv === level ? '.sel' : ''), {
                    type: 'button',
                    role: 'radio',
                    'aria-checked': String(lv === level),
                    onclick: () => {
                        level = lv;
                        store.set('level', lv);
                        drawLevels();
                    }
                },
                h('strong', t(lv)), h('div.dots', [0, 1, 2].map(j => h('i' + (j <= k ? '.on' : '')))), h('small', t('min_words', {n: E.LEVELS[lv]})), stats[len + '.' + lv] ? h('small', levelNote(lv)) : null)));
        }

        drawLevels();
        const lengths = h('div.seg', {
            role: 'radiogroup',
            'aria-label': t('length')
        }, E.LENGTHS.map(n => h('button' + (n === len ? '.sel' : ''), {
            type: 'button', role: 'radio', 'aria-checked': String(n === len), onclick: e => {
                len = n;
                store.set('len', n);
                lengths.querySelectorAll('button').forEach(b => {
                    b.classList.toggle('sel', b === e.currentTarget);
                    b.setAttribute('aria-checked', String(b === e.currentTarget));
                });
                drawLevels();
            }
        }, t('letters', {n}))));
        app.replaceChildren(h('div.home',
            h('div.topbar', themeTabs(), langButton(), h('button.icon-btn.theme-cycle', {
                type: 'button',
                title: t('theme'),
                'aria-label': t('theme'),
                onclick: cycleTheme
            }, icon('moon'))),
            h('section.hero', h('img.logo', {src: 'icons/favicon.svg', alt: ''}), h('h1', NAME), h('p', t('subtitle')),
                stats.total ? h('div.total', icon('star'), t('total_points', {n: stats.total})) : null),
            h('div.home-grid',
                h('section.panel',
                    saved ? h('div.resume-wrap', h('button.resume-card', {
                                type: 'button',
                                onclick: () => resume(saved)
                            }, h('img.mini', {src: 'icons/favicon.svg', alt: ''}),
                            h('div', h('strong', t('continue_game')), h('span', `${up(saved.start)} → ${up(saved.target)} · ${t(saved.level)}`))),
                        h('button.resume-discard', {
                            type: 'button',
                            title: t('discard_title'),
                            'aria-label': t('discard_title'),
                            onclick: discardGame
                        }, icon('close'))) : null,
                    h('h2', t('level')), levels,
                    h('h2', t('length')), lengths,
                    h('button.btn.btn-primary.btn-block.play', {
                        type: 'button', onclick: async () => {
                            if (saved && !await confirmBox(t('confirm_new'), t('confirm_new_text'), t('start'))) return;
                            startGame(level, len);
                        }
                    }, icon('play'), saved ? t('new_game') : t('play'))),
                h('section.panel.howto', h('h2', t('howto_title')), exampleView(),
                    h('ol', [1, 2, 3].map(n => h('li', h('span.n', String(n)), h('div', h('strong', t('howto' + n + '_t')), h('p', t('howto' + n)))))),
                    h('div.links', h('button.btn.btn-soft', {
                        type: 'button',
                        onclick: showRules
                    }, icon('book'), t('rules_title'))))),
            h('footer.footer',
                // Same credit block as the other games
                h('div.credit-block',
                    h('a.credit', {
                        href: 'https://www.saveriomorelli.com/',
                        target: '_blank',
                        rel: 'noopener'
                    }, h('span', t('credit_by')), h('span.credit-name', 'Saverio Morelli')),
                    h('span.credit-ai', {html: '<svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.2 6.6 6.8 2.4-6.8 2.4L12 20.5l-2.2-6.6L3 11.5l6.8-2.4z"/></svg>'}, h('span', t('credit_ai')))),
                h('div.footer-actions',
                    h('a.version-btn.privacy-link', {href: 'privacy.html'}, 'Privacy'),
                    h('button.donate-btn', {
                        type: 'button',
                        onclick: showDonate,
                        title: t('donate_title')
                    }, icon('heart'), h('span', t('donate'))),
                    h('button.version-btn', {type: 'button', onclick: showReleases}, `${t('version')} ${VERSION}`)))));
        document.title = 'LetterHop – Saverio Morelli';
    }

    async function discardGame() {
        if (!await confirmBox(t('confirm_discard'), t('confirm_discard_text'), t('discard_btn'))) return;
        store.del('game');
        showHome();
    }

    function langButton() {
        return h('button.lang-pill', {
            type: 'button',
            onclick: showLanguages
        }, icon('globe'), I.getLang().toUpperCase());
    }

    function showLanguages() {
        const m = modal([h('h3', t('language')), h('div.menu-list.lang-list', Object.entries(I.LANGS).map(([k, label]) =>
            h('button', {
                type: 'button', onclick: () => {
                    I.setLang(k);
                    m.close();
                    screen === 'game' ? renderGame() : showHome();
                }
            }, h('span', label), k === I.getLang() ? icon('tick') : null)))]);
    }

    function themeTabs() {
        const th = store.get('theme', 'auto');
        return h('div.seg.theme-tabs', {
            role: 'radiogroup',
            'aria-label': t('theme')
        }, ['auto', 'light', 'dark'].map(k => h('button' + (k === th ? '.sel' : ''), {
            type: 'button',
            role: 'radio',
            'aria-checked': String(k === th),
            'data-mode': k,
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

    function showRules() {
        const m = modal([h('h3', t('rules_title')), h('div.rules', t('rules').map(([a, b]) => [h('h4', a), h('p', b)])),
            h('div.actions', h('button.btn.btn-primary', {type: 'button', onclick: () => m.close()}, t('got_it')))]);
    }

    function showReleases() {
        const L = I.getLang();
        modal([h('h3', t('release_notes')), h('div.release', (window.LetterHopReleases || []).map(r => [h('h4', `${r.version} · ${new Date(r.date).toLocaleDateString(L, {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })}`), h('ul', (r.notes[L] || r.notes.en).map(n => h('li', n)))]))]);
    }

    function showDonate() {
        modal([
            h('div.donate-modal-icon', icon('heart')),
            h('h3', t('donate_modal_title')),
            h('p.muted', t('donate_modal_intro')),
            h('div.donate-reasons',
                h('div.donate-reason',
                    h('div.donate-reason-icon', icon('check')),
                    h('div', h('strong', t('donate_reason_ads_title')), h('p', t('donate_reason_ads_desc')))),
                h('div.donate-reason',
                    h('div.donate-reason-icon', icon('magic')),
                    h('div', h('strong', t('donate_reason_oss_title')), h('p', t('donate_reason_oss_desc')))),
                h('div.donate-reason',
                    h('div.donate-reason-icon', icon('globe')),
                    h('div', h('strong', t('donate_reason_server_title')), h('p', t('donate_reason_server_desc'))))),
            h('div.actions',
                h('a.btn.btn-primary.btn-block.donate-cta-btn', {
                        href: 'https://www.saveriomorelli.com/donate/',
                        target: '_blank',
                        rel: 'noopener'
                    },
                    icon('heart'), h('span', t('donate_cta'))))
        ]);
    }

    // =================================================================== game screen
    let ladderEl, endEl, sideEl;

    function renderGame() {
        ladderEl = h('div.ladder');
        endEl = h('div.ladder-end');
        sideEl = h('div.side');
        app.replaceChildren(h('div.game',
            h('header.game-bar',
                h('button.icon-btn', {
                    type: 'button', title: t('leave'), 'aria-label': t('leave'), onclick: () => {
                        save();
                        showHome();
                    }
                }, icon('back')),
                h('div.title', h('img', {
                    src: 'icons/favicon.svg',
                    alt: ''
                }), h('strong', NAME), h('span.badge', t(G.level))),
                h('button.icon-btn', {
                    type: 'button',
                    id: 'pause-btn',
                    title: t('pause'),
                    'aria-label': t('pause'),
                    onclick: () => setPaused(!G.paused)
                }, icon('pause')),
                h('button.icon-btn', {
                    type: 'button',
                    title: t('settings'),
                    'aria-label': t('settings'),
                    onclick: showMenu
                }, icon('more'))),
            h('div.stats',
                h('span', `${t('words')}: `, h('b#used', '0')),
                h('span', `${t('minimum')}: `, h('b', String(G.min))),
                h('span', `${t('points')}: `, h('b#points', '0')),
                settings.timer ? h('span', h('b#time', fmtTime(G.elapsed))) : null),
            h('div.board', {style: `--n:${G.len};--rows:${G.min + 2}`}, ladderEl, endEl),
            sideEl));
        paint();
    }

    function tile(ch, cls, attrs) {
        return h((attrs ? 'button' : 'span') + '.tile' + (cls || ''), attrs ? Object.assign({type: 'button'}, attrs) : null, ch.toUpperCase());
    }

    const tileClass = (word, i, prev) => (prev && word[i] !== prev[i] ? '.changed' : '') + (settings.match && word[i] === G.target[i] ? '.match' : '');

    function wordRow(word, prev, label, extra) {
        return h('div.row' + (extra || ''), h('span.row-n', label), h('div.tiles', [...word].map((c, i) => tile(c, tileClass(word, i, prev)))));
    }

    function currentRow(word, prev, label) {
        const shown = refused || word;
        const live = !G.done && !G.paused;
        const row = h('div.row.current' + (refused ? '.shake' : ''), h('span.row-n', label), h('div.tiles', [...shown].map((c, i) => {
            const cls = (refused ? (c !== word[i] ? '.wrong' : '') : tileClass(word, i, prev)) + (i === sel ? '.sel' : '') + (tip && tip.pos === i ? '.tip' : '');
            return tile(c, cls, {
                'aria-label': `${i + 1}: ${c.toUpperCase()}`,
                'aria-pressed': String(i === sel),
                disabled: !live,
                onclick: () => {
                    sel = sel === i ? -1 : i;
                    paint();
                }
            });
        })));
        return row;
    }

    function paint() {
        if (screen !== 'game' || !ladderEl) return;
        const rows = [];
        const reached = current() === G.target;
        const last = reached ? G.chain.length - 2 : G.chain.length - 1;
        for (let i = 0; i <= last; i++) {
            const word = G.chain[i], prev = G.chain[i - 1];
            const label = i === 0 ? '' : String(i);
            if (i === last && !G.done) rows.push(currentRow(word, prev, label));
            else rows.push(wordRow(word, prev, label, i === 0 ? '.first' : ''));
        }
        const missing = Math.max(0, G.min - wordsUsed());
        for (let i = 0; i < missing; i++) rows.push(h('div.row.slot', h('span.row-n', String(wordsUsed() + i + 1)), h('div.tiles', Array.from({length: G.len}, () => h('span.tile.empty')))));
        ladderEl.replaceChildren(...rows);
        ladderEl.classList.toggle('paused', !!G.paused);
        endEl.replaceChildren(h('div.row.last' + (reached ? '.won' : ''), h('span.row-n', {
            title: t('target_word'),
            'aria-label': t('target_word'),
            role: 'img'
        }, icon('target')), h('div.tiles', [...G.target].map(c => tile(c)))));
        endEl.classList.toggle('paused', !!G.paused);
        const now = ladderEl.querySelector('.current');
        if (now) ladderEl.scrollTop = Math.max(0, now.offsetTop - ladderEl.offsetTop - ladderEl.clientHeight / 2 + now.offsetHeight);

        const pauseBtn = document.getElementById('pause-btn');
        if (pauseBtn) pauseBtn.replaceChildren(icon(G.paused ? 'play' : 'pause'));
        const board = ladderEl.parentElement;
        const cover = board.querySelector('.pause-cover');
        if (G.paused && !cover) board.appendChild(h('div.pause-cover', h('div', h('h3', t('paused')), h('p', t('paused_text')), h('button.btn.btn-primary', {
            type: 'button',
            onclick: () => setPaused(false)
        }, icon('play'), t('resume')))));
        if (!G.paused && cover) cover.remove();

        const used = wordsUsed();
        const usedEl = document.getElementById('used'), pointsEl = document.getElementById('points');
        if (usedEl) {
            usedEl.textContent = used;
            usedEl.classList.toggle('over', used > G.min);
        }
        if (pointsEl) pointsEl.textContent = E.score(G.level, used, G.tips).points;
        paintSide();
    }

    function paintSide() {
        const parts = [];
        if (tip) parts.push(tipCard());
        else parts.push(h('p.prompt' + (sel >= 0 ? '.go' : ''), G.done ? '' : t(sel >= 0 ? 'pick_key' : 'tap_letter')));
        parts.push(h('div.keyboard', {role: 'group'}, E.keyboardFor(G.lang).map((line, r, all) => h('div.key-row',
            [...line].map(c => h('button.key', {type: 'button', onclick: () => letter(c)}, c)),
            r === all.length - 1 ? h('button.key.wide', {
                type: 'button',
                title: t('undo'),
                'aria-label': t('undo'),
                disabled: G.chain.length < 2 || G.done,
                onclick: undo
            }, icon('backspace')) : null))));
        parts.push(h('div.tools',
            h('button.tool', {
                type: 'button',
                onclick: undo,
                disabled: G.chain.length < 2 || G.done
            }, icon('undo'), t('undo')),
            h('button.tool' + (tip ? '.on' : '') + (!tip && !tipAffordable() ? '.off' : ''), {
                type: 'button',
                onclick: showTip,
                disabled: G.done
            }, icon('bulb'), t('tip'), h('span.pill', `−${E.TIP_COST}`))));
        sideEl.replaceChildren(...parts);
    }

    function tipCard() {
        return h('div.tip-card',
            h('div', h('h3', icon('bulb'), t(tip.shown ? 'tip_word_t' : 'tip_pos_t')), h('p', tip.shown ? t('tip_word', {w: up(tip.word)}) : t('tip_pos', {n: tip.pos + 1}))),
            h('div.row-actions',
                h('button.btn.btn-ghost', {
                    type: 'button', onclick: () => {
                        tip = null;
                        paint();
                    }
                }, t('close')),
                tip.shown ? h('button.btn.btn-primary', {type: 'button', onclick: () => hop(tip.word)}, t('use_word'))
                    : h('button.btn.btn-primary' + (tipAffordable() ? '' : '.off'), {
                        type: 'button',
                        onclick: revealTip
                    }, `${t('reveal_word')} −${E.TIP_COST}`)));
    }

    function showMenu() {
        const item = (ic, label, fn) => h('button', {
            type: 'button', onclick: () => {
                m.close();
                fn();
            }
        }, icon(ic), label);
        const m = modal([h('h3', NAME), h('div.menu-list',
            item('plus', t('new_game'), async () => {
                if (await confirmBox(t('confirm_new'), t('confirm_new_text'), t('start'))) startGame(G.level, G.len);
            }),
            item('restart', t('restart'), restart),
            item('flag', t('solution'), giveUp),
            h('hr'),
            item('gear', t('settings'), showSettings),
            item('book', t('rules_title'), showRules),
            item('heart', t('donate'), showDonate),
            item('home', t('leave'), () => {
                save();
                showHome();
            }))]);
    }

    function showSettings() {
        const row = (key, label) => {
            const sw = h('span.switch' + (settings[key] ? '.on' : ''));
            return h('div.setting', {
                role: 'switch', tabindex: 0, 'aria-checked': String(settings[key]), onclick: e => {
                    settings[key] = !settings[key];
                    saveSettings();
                    sw.classList.toggle('on', settings[key]);
                    e.currentTarget.setAttribute('aria-checked', String(settings[key]));
                    if (key === 'timer') renderGame(); else paint();
                }
            }, h('span', label), sw);
        };
        const th = store.get('theme', 'auto');
        const seg = h('div.seg', ['auto', 'light', 'dark'].map(k => h('button' + (k === th ? '.sel' : ''), {
            type: 'button', onclick: e => {
                store.set('theme', k);
                applyTheme();
                seg.querySelectorAll('button').forEach(b => b.classList.toggle('sel', b === e.currentTarget));
            }
        }, t('theme_' + k))));
        modal([h('h3', t('settings')), row('match', t('set_match')), row('timer', t('set_timer')),
            h('div.setting', {style: 'display:block'}, h('div', {style: 'margin-bottom:8px'}, t('theme')), seg),
            h('div.setting', {onclick: showLanguages}, h('span', t('language')), h('span', {style: 'color:var(--muted)'}, I.LANGS[I.getLang()]))]);
    }

    // ---------- keyboard ----------
    document.addEventListener('keydown', e => {
        if (screen !== 'game' || !G || document.querySelector('.modal')) return;
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
            e.preventDefault();
            undo();
            return;
        }
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        const k = e.key;
        // Every key the game uses is consumed, so the browser does not react to it too
        if (/^[a-zA-Z]$/.test(k)) {
            e.preventDefault();
            letter(k);
            return;
        }
        if (k === 'Backspace') {
            e.preventDefault();
            undo();
            return;
        }
        if (k === '?') {
            e.preventDefault();
            showTip();
            return;
        }
        if (k === 'ArrowLeft' || k === 'ArrowRight') {
            e.preventDefault();
            if (G.done || G.paused) return;
            sel = sel < 0 ? (k === 'ArrowLeft' ? G.len - 1 : 0) : (sel + (k === 'ArrowLeft' ? G.len - 1 : 1)) % G.len;
            paint();
            return;
        }
        if (k === ' ') {
            if (e.target.closest && e.target.closest('button')) e.target.blur();
            e.preventDefault();
            setPaused(!G.paused);
            return;
        }
        if (k === 'Escape' && (tip || sel >= 0)) {
            tip = null;
            sel = -1;
            paint();
        }
    });

    // Android back key (see MainActivity): true means the page used it
    window.onAndroidBack = function () {
        const open = document.querySelectorAll('.modal');
        if (open.length) {
            const top = open[open.length - 1];
            if (top.querySelector('.win-box')) {
                top.remove();
                showHome();
            } else top.dispatchEvent(new MouseEvent('click'));
            return true;
        }
        if (screen === 'game') {
            save();
            showHome();
            return true;
        }
        return false;
    };

    matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
    window.addEventListener('beforeunload', save);
    applyTheme();
    showHome();
    E.load(I.getLang(), VERSION).catch(() => { /* retried when a game starts */
    });
})();

// Lets the game open without a connection (see sw.js)
if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => {
}));
