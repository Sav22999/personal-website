/* Fucina — quick drills inside the levels (see lib/drills.php), plus the small joys of the app: Favilla the mascot,
 * confetti and code colouring. A drill is played here, for immediate feedback, and reports its mistakes to the server. */
(function () {
    'use strict';

    const {h, btn, icon, iconBtn} = UI;
    const t = I18N.t;

    const shuffle = list => {
        const a = [...list];
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    };
    const calm = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ---------------------------------------------------------------- Favilla

    const FACES = {
        happy: '<path d="M38 80c4 7 20 7 24 0" fill="none" stroke="#1B1B4B" stroke-width="4.5" stroke-linecap="round"/>',
        wow: '<ellipse cx="50" cy="84" rx="6" ry="7.5" fill="#1B1B4B"/>',
        sad: '<path d="M39 88c4-7 18-7 22 0" fill="none" stroke="#1B1B4B" stroke-width="4.5" stroke-linecap="round"/>',
    };

    function mascot(mood, cls) {
        const face = FACES[mood] || FACES.happy;
        return h('span.mascot' + (cls ? '.' + cls : ''), {
            'aria-hidden': 'true',
            html: `<svg viewBox="0 0 100 120"><path fill="#FF5B2E" d="M52 3c6 24 36 36 36 74 0 25-17 40-38 40S12 102 12 77c0-17 10-28 19-36 2 12 8 17 13 13 7-11-1-31 8-51Z"/><path fill="#FFC53D" d="M51 36c4 15 24 24 24 46 0 16-11 26-25 26S25 98 25 82c0-11 7-18 13-22 2 7 5 9 8 7 5-7 0-20 5-31Z"/><circle cx="37" cy="68" r="5.5" fill="#1B1B4B"/><circle cx="63" cy="68" r="5.5" fill="#1B1B4B"/><circle cx="39" cy="66" r="1.8" fill="#fff"/><circle cx="65" cy="66" r="1.8" fill="#fff"/><circle cx="28" cy="79" r="5" fill="#FF8A5C" opacity=".75"/><circle cx="72" cy="79" r="5" fill="#FF8A5C" opacity=".75"/>${face}</svg>`
        });
    }

    function confetti() {
        if (calm()) return;
        const colours = ['#FF5B2E', '#FFC53D', '#12B886', '#2F8CF0', '#F03E82'];
        const box = h('div.confetti', {'aria-hidden': 'true'});
        for (let i = 0; i < 44; i++) {
            const bit = h('i', {style: {left: Math.random() * 100 + '%', background: colours[i % colours.length], animationDelay: Math.random() * .25 + 's', animationDuration: 1 + Math.random() * .9 + 's'}});
            bit.style.setProperty('--drift', (Math.random() * 160 - 80) + 'px');
            bit.style.setProperty('--spin', (Math.random() * 900 - 450) + 'deg');
            box.appendChild(bit);
        }
        document.body.appendChild(box);
        setTimeout(() => box.remove(), 2300);
    }

    // ---------------------------------------------------------------- code colouring

    const TOKENS = /(<!--[\s\S]*?-->|\/\*[\s\S]*?\*\/)|("[^"\n]*"|'[^'\n]*')|(<\/?[a-zA-Z!][\w-]*|\/?>)|(#[0-9a-fA-F]{3,8}\b|\b\d*\.?\d+(?:px|rem|em|%|s|ms|fr|deg|vw|vh)?\b)|([$@][\w-]+|--[\w-]+|&)|([a-z-]+)(?=\s*:\s)/g;
    const KINDS = ['t-note', 't-str', 't-tag', 't-num', 't-var', 't-prop'];

    /** Coloured code, as nodes: the text is never read as HTML. */
    function colour(code) {
        const out = [];
        let last = 0, m;
        TOKENS.lastIndex = 0;
        while ((m = TOKENS.exec(code))) {
            if (m.index > last) out.push(code.slice(last, m.index));
            out.push(h('span.' + KINDS[m.slice(1).findIndex(x => x !== undefined)], m[0]));
            last = TOKENS.lastIndex;
        }
        if (last < code.length) out.push(code.slice(last));
        return out;
    }

    const LANG_NAMES = {html: 'HTML', css: 'CSS', scss: 'SCSS', sass: 'Sass', tailwind: 'Tailwind CSS', js: 'JavaScript', ts: 'TypeScript', angular: 'Angular', vue: 'Vue',
        php: 'PHP', python: 'Python', java: 'Java', sql: 'SQL', mysql: 'MySQL', mariadb: 'MariaDB', postgresql: 'PostgreSQL', sqlite: 'SQLite', mssql: 'SQL Server', mongodb: 'MongoDB', ai: 'AI fundamentals', ml: 'Machine learning', dl: 'Deep learning', prompt: 'Prompt engineering', llm: 'LLM', assistants: 'AI assistants', react: 'React', next: 'Next.js', node: 'Node.js', git: 'Git', bash: 'Bash', go: 'Go', csharp: 'C#', c: 'C', cpp: 'C++', docker: 'Docker', kubernetes: 'Kubernetes', cloud: 'Cloud', testing: 'Testing', patterns: 'Design patterns', regex: 'Regex', security: 'Security', a11y: 'Accessibility', api: 'API', redis: 'Redis', algorithms: 'Algorithms', excel: 'Excel', powerbi: 'Power BI', r: 'R', flutter: 'Flutter', swift: 'Swift', kotlin: 'Kotlin', rust: 'Rust', vala: 'Vala'};

    /** The language of an exercise, in the colour of its course family. */
    function langChip(family) {
        return family ? h('span.lang-chip.fam-' + family, LANG_NAMES[family] ? t(LANG_NAMES[family]) : family.toUpperCase()) : null;
    }

    // ---------------------------------------------------------------- the frame of a drill

    const NAMES = {blanks: ['Tiles', 'grid'], order: ['Sort it', 'sort'], bug: ['Bug hunt', 'bug'], match: ['Pairs', 'link'], flash: ['Lightning', 'spark'], tune: ['Tune it', 'tune']};

    /**
     * ctx: rich (text to nodes), cost (of a hint), buyHint() → true once paid, onDone(mistakes), next(), skip().
     * Every kind fills the body and returns {hint()}; it calls api.miss() on a mistake and api.win() at the end.
     */
    function render(d, ctx) {
        const {rich} = ctx;
        let mistakes = 0, won = false;
        const lives = h('span.lives', {'aria-hidden': 'true'}, [0, 1, 2].map(() => icon('spark')));
        const body = h('div.drill-body');
        const end = h('div.drill-end');
        const actions = h('div.step-actions');

        const api = {
            rich,
            body,
            /** A mistake: the drill shakes and the stars at stake go down. */
            miss(target) {
                mistakes++;
                const stars = mistakes === 0 ? 3 : mistakes <= 2 ? 2 : 1;
                [...lives.children].forEach((x, i) => x.classList.toggle('off', i >= stars));
                const el = target || body;
                el.classList.remove('shake');
                void el.offsetWidth;
                el.classList.add('shake');
            },
            win() {
                if (won) return;
                won = true;
                body.classList.add('finished'); // what was only needed to play goes away: see fucina.css
                actions.replaceChildren();
                if (!mistakes) confetti();
                end.replaceChildren(h('div.win', mascot(mistakes ? 'happy' : 'wow'), h('div.grow', h('strong', mistakes ? t('Solved!') : t('Flawless!')), h('p', rich(d.why))), btn(t('Continue'), {icon: 'right', onclick: ctx.next})));
                end.scrollIntoView({behavior: calm() ? 'auto' : 'smooth', block: 'nearest'});
                ctx.onDone(mistakes);
            },
            get won() {
                return won;
            }
        };

        const game = KINDS_OF[d.kind](d, api);
        const hintBtn = btn(t('Hint · {n}', {n: ctx.cost}), {
            kind: 'text', icon: 'bulb', onclick: async () => {
                if (!won && await ctx.buyHint()) game.hint();
            }
        });
        actions.append(hintBtn, btn(t('Skip'), {kind: 'text', onclick: ctx.skip}));
        if (game.action) actions.append(game.action);
        const [name, ic] = NAMES[d.kind];
        return h('section.drill.k-' + d.kind, h('header.drill-head', h('span.drill-kind', icon(ic), t(name)), langChip(d.family), d.from ? h('span.muted.small.drill-from', d.from) : null, lives), h('h2', rich(d.prompt)), body, end, actions);
    }

    // ---------------------------------------------------------------- tiles in the gaps

    function blanks(d, api) {
        const parts = d.code.split('___');
        const tiles = shuffle([...d.gaps, ...d.extra]).map(text => {
            const b = h('button.tile-btn', {type: 'button', onclick: () => place(text, b)}, text);
            return b;
        });
        const slots = d.gaps.map((_, k) => h('button.slot', {type: 'button', 'aria-label': t('Gap {n}', {n: k + 1}), onclick: () => clear(k)}));
        const filled = d.gaps.map(() => null);

        function clear(k) {
            if (!filled[k] || slots[k].classList.contains('fit') || api.won) return;
            filled[k].button.classList.remove('used');
            filled[k] = null;
            slots[k].textContent = '';
        }

        function place(text, button) {
            if (api.won || button.classList.contains('used')) return;
            const k = filled.findIndex(x => !x);
            if (k < 0) return;
            filled[k] = {text, button};
            slots[k].textContent = text;
            button.classList.add('used');
            if (filled.every(Boolean)) check();
        }

        function check() {
            const wrong = d.gaps.map((g, k) => filled[k].text !== g ? k : -1).filter(k => k >= 0);
            d.gaps.forEach((g, k) => {
                if (filled[k].text === g) slots[k].classList.add('fit');
            });
            if (!wrong.length) return api.win();
            api.miss(code);
            setTimeout(() => wrong.forEach(clear), 420);
        }

        const code = h('pre.gap-code', parts.flatMap((p, k) => k < slots.length ? [...colour(p), slots[k]] : colour(p)));
        api.body.append(code, h('div.tile-tray', tiles));
        return {
            hint() {
                const k = filled.findIndex((x, n) => !x || x.text !== d.gaps[n]);
                if (k < 0) return;
                clear(k);
                const free = tiles.find(b => b.textContent === d.gaps[k] && !b.classList.contains('used'));
                if (!free) return;
                filled[k] = {text: d.gaps[k], button: free};
                slots[k].textContent = d.gaps[k];
                free.classList.add('used');
                slots[k].classList.add('fit');
                if (filled.every(Boolean)) check();
            }
        };
    }

    // ---------------------------------------------------------------- lines to sort

    function order(d, api) {
        let lines = shuffle(d.lines);
        while (lines.every((x, i) => x === d.lines[i])) lines = shuffle(d.lines);
        const list = h('ol.sort-list');
        const step = (li, dir) => {
            const other = dir < 0 ? li.previousElementSibling : li.nextElementSibling;
            if (other) dir < 0 ? list.insertBefore(li, other) : list.insertBefore(other, li);
            list.querySelectorAll('.ko').forEach(x => x.classList.remove('ko'));
        };
        for (const line of lines) {
            const grip = h('span.grip', icon('menu'));
            const li = h('li.sort-item', grip, h('code', colour(line)), iconBtn('up', t('Move up'), () => step(li, -1)), iconBtn('down', t('Move down'), () => step(li, 1)));
            li.dataset.line = line;
            // The row moves in the page while it is dragged, and that drops any pointer capture: so the listeners stay on window
            li.addEventListener('pointerdown', e => {
                if (e.button !== 0 || e.target.closest('button')) return;
                if (e.pointerType !== 'mouse' && !e.target.closest('.grip')) return; // by touch the rest of the row scrolls the page
                e.preventDefault();
                li.classList.add('dragging');
                list.querySelectorAll('.ko').forEach(x => x.classList.remove('ko'));
                const move = ev => {
                    const rest = [...list.children].filter(x => x !== li);
                    const before = rest.find(x => {
                        const r = x.getBoundingClientRect();
                        return ev.clientY < r.top + r.height / 2;
                    }) || null;
                    if (li.nextElementSibling !== before) list.insertBefore(li, before);
                };
                const stop = () => {
                    li.classList.remove('dragging');
                    window.removeEventListener('pointermove', move);
                    window.removeEventListener('pointerup', stop);
                    window.removeEventListener('pointercancel', stop);
                };
                window.addEventListener('pointermove', move);
                window.addEventListener('pointerup', stop);
                window.addEventListener('pointercancel', stop);
            });
            list.appendChild(li);
        }
        api.body.append(list);
        const verify = btn(t('Check'), {
            icon: 'check', onclick: () => {
                const items = [...list.children];
                items.forEach((li, i) => {
                    li.classList.toggle('fit', li.dataset.line === d.lines[i]);
                    li.classList.toggle('ko', li.dataset.line !== d.lines[i]);
                });
                if (items.every((li, i) => li.dataset.line === d.lines[i])) api.win(); else api.miss(list);
            }
        });
        return {
            action: verify,
            hint() {
                const items = [...list.children];
                const i = items.findIndex((li, n) => li.dataset.line !== d.lines[n]);
                if (i < 0) return;
                const right = items.slice(i).find(li => li.dataset.line === d.lines[i]);
                list.insertBefore(right, items[i]);
                right.classList.add('fit');
            }
        };
    }

    // ---------------------------------------------------------------- bug hunt

    function bug(d, api) {
        const rows = d.lines.map((line, i) => h('button.bug-line', {
            type: 'button', onclick: () => {
                if (api.won) return;
                if (i === d.bug) {
                    rows[i].classList.add('found');
                    rows[i].after(h('div.bug-fix', icon('check'), h('code', colour(d.fix))));
                    return api.win();
                }
                rows[i].disabled = true;
                rows[i].classList.add('clean');
                api.miss(rows[i]);
            }
        }, h('span.ln', String(i + 1)), h('code', colour(line))));
        api.body.append(h('div.bug-code', rows));
        return {
            hint() {
                const clean = rows.filter((r, i) => i !== d.bug && !r.disabled);
                shuffle(clean).slice(0, Math.ceil(clean.length / 2)).forEach(r => {
                    r.disabled = true;
                    r.classList.add('clean');
                });
            }
        };
    }

    // ---------------------------------------------------------------- pairs

    function match(d, api) {
        const started = Date.now();
        const clock = h('span.clock', icon('timer'), h('b', '0s'));
        const timer = setInterval(() => {
            if (!clock.isConnected || api.won) return clearInterval(timer);
            clock.lastChild.textContent = Math.round((Date.now() - started) / 1000) + 's';
        }, 500);
        let picked = null, left = d.pairs.length;
        const card = (pair, side, text) => {
            const b = h('button.pair.' + side, {type: 'button', onclick: () => choose(b)}, side === 'l' ? h('code', String(text)) : h('span', api.rich(String(text))));
            b.dataset.pair = pair;
            b.dataset.side = side;
            return b;
        };
        const lefts = shuffle(d.pairs.map((p, i) => card(i, 'l', p[0])));
        const rights = shuffle(d.pairs.map((p, i) => card(i, 'r', p[1])));

        function solve(a, b) {
            [a, b].forEach(x => {
                x.classList.remove('sel');
                x.classList.add('gone');
                x.disabled = true;
            });
            picked = null;
            if (--left === 0) api.win();
        }

        function choose(b) {
            if (b === picked) {
                b.classList.remove('sel');
                picked = null;
                return;
            }
            if (!picked || picked.dataset.side === b.dataset.side) {
                if (picked) picked.classList.remove('sel');
                picked = b;
                b.classList.add('sel');
                return;
            }
            if (picked.dataset.pair === b.dataset.pair) return solve(picked, b);
            picked.classList.remove('sel');
            const other = picked;
            picked = null;
            api.miss(b);
            other.classList.add('shake');
            setTimeout(() => other.classList.remove('shake'), 400);
        }

        api.body.append(h('div.pair-top', clock), h('div.pairs', h('div.pair-col', lefts), h('div.pair-col', rights)));
        return {
            hint() {
                const a = lefts.find(x => !x.disabled);
                if (a) solve(a, rights.find(x => x.dataset.pair === a.dataset.pair));
            }
        };
    }

    // ---------------------------------------------------------------- true or false against the clock

    function flash(d, api) {
        const SECONDS = 9;
        const queue = shuffle(d.cards);
        const total = queue.length;
        let solved = 0, combo = 0, best = 0, deadline = 0, frozen = false, card = null, frame = 0;
        const text = h('p.flash-text');
        const fuse = h('i');
        const count = h('span.muted.small');
        const comboTag = h('b.combo');
        const yes = btn(t('True'), {kind: 'tonal', icon: 'check', cls: 'flash-yes', onclick: () => answer(true)});
        const no = btn(t('False'), {kind: 'tonal', icon: 'close', cls: 'flash-no', onclick: () => answer(false)});
        const box = h('div.flash-card', h('div.fuse', fuse), text);

        function deal() {
            card = queue[0];
            frozen = false;
            yes.classList.remove('glow');
            no.classList.remove('glow');
            text.replaceChildren(...api.rich(String(card[0])));
            count.textContent = t('{a} of {b}', {a: solved + 1, b: total});
            box.classList.remove('deal');
            void box.offsetWidth;
            box.classList.add('deal');
            deadline = Date.now() + SECONDS * 1000;
        }

        function tick() {
            if (!box.isConnected && frame) return stop();
            if (api.won) return stop();
            if (card && !frozen) {
                const left = deadline - Date.now();
                fuse.style.width = Math.max(0, left / (SECONDS * 10)) + '%';
                if (left <= 0) answer(!card[1]);
            }
            frame = requestAnimationFrame(tick);
        }

        function key(e) {
            if (e.key === 'ArrowLeft') answer(false);
            if (e.key === 'ArrowRight') answer(true);
        }

        function stop() {
            cancelAnimationFrame(frame);
            document.removeEventListener('keydown', key);
        }

        function answer(value) {
            if (!card || api.won) return;
            const right = value === card[1];
            queue.shift();
            if (right) {
                solved++;
                combo++;
                best = Math.max(best, combo);
                comboTag.textContent = combo > 1 ? t('Combo ×{n}', {n: combo}) : '';
                comboTag.classList.remove('pop');
                void comboTag.offsetWidth;
                comboTag.classList.add('pop');
            } else {
                combo = 0;
                comboTag.textContent = '';
                queue.push(card); // a wrong card comes back
                api.miss(box);
            }
            if (!queue.length) {
                card = null;
                api.body.append(h('div.flash-done', icon('check'), h('strong', t('{a} of {b}', {a: total, b: total})), best > 1 ? h('span', t('Combo ×{n}', {n: best})) : null));
                return api.win();
            }
            deal();
        }

        // Swipe: right for true, left for false
        let sx = null;
        box.addEventListener('pointerdown', e => sx = e.clientX);
        box.addEventListener('pointerup', e => {
            if (sx !== null && Math.abs(e.clientX - sx) > 60) answer(e.clientX > sx);
            sx = null;
        });

        api.body.append(h('div.flash-top', count, comboTag), box, h('div.flash-btns', no, yes));
        document.addEventListener('keydown', key);
        deal();
        frame = requestAnimationFrame(tick);
        return {
            hint() {
                if (!card) return;
                frozen = true;
                (card[1] ? yes : no).classList.add('glow');
            }
        };
    }

    // ---------------------------------------------------------------- copy the target

    function tune(d, api) {
        const onItems = d.layout === 'box';
        const value = p => (p.choices ? p.mine : p.mine + (p.unit || ''));
        const props = d.props.map(p => ({...p, mine: p.start ?? (p.choices ? p.choices[0] : p.min)}));
        const figure = cls => {
            const stage = h('div.tune-stage.' + cls);
            if (d.layout !== 'box') stage.style.display = d.layout;
            for (const [k, v] of Object.entries(Array.isArray(d.base) ? {} : d.base || {})) stage.style.setProperty(k, v);
            const items = Array.from({length: d.items || 1}, (_, i) => {
                const it = h('i.tune-item.c' + (i % 4));
                for (const [k, v] of Object.entries(Array.isArray(d.itemBase) ? {} : d.itemBase || {})) it.style.setProperty(k, v);
                stage.appendChild(it);
                return it;
            });
            return {stage, items};
        };
        const apply = (f, p, v) => (onItems ? f.items : [f.stage]).forEach(el => el.style.setProperty(p.prop, v));
        const target = figure('goal');
        const mine = figure('mine');
        d.props.forEach(p => apply(target, p, p.choices ? p.target : p.target + (p.unit || '')));

        const lines = h('code');
        const meter = h('i');
        const label = h('b', '0%');

        function refresh() {
            props.forEach(p => apply(mine, p, value(p)));
            lines.replaceChildren(...colour(`${onItems ? '.box' : '.stage'} {\n${props.map(p => `  ${p.prop}: ${value(p)};`).join('\n')}\n}`));
            const score = props.reduce((n, p) => n + (p.choices ? (p.mine === p.target ? 1 : 0) : 1 - Math.abs(p.mine - p.target) / (p.max - p.min)), 0) / props.length;
            const pct = Math.round(score * 100);
            meter.style.width = pct + '%';
            label.textContent = pct + '%';
            if (props.every(p => p.choices ? p.mine === p.target : Number(p.mine) === Number(p.target))) {
                mine.stage.classList.add('fit');
                api.win();
            }
        }

        const controls = props.map(p => {
            if (p.choices) {
                const chips = p.choices.map(c => {
                    const b = h('button.tune-chip' + (c === p.mine ? '.sel' : ''), {
                        type: 'button', onclick: () => {
                            if (api.won) return;
                            p.mine = c;
                            chips.forEach(x => x.classList.toggle('sel', x === b));
                            refresh();
                        }
                    }, /^#/.test(c) ? h('span.swatch', {style: {background: c}, title: c}) : c);
                    return b;
                });
                p.sync = () => chips.forEach((x, i) => x.classList.toggle('sel', p.choices[i] === p.mine));
                return h('div.tune-row', h('label', p.prop), h('div.tune-chips', chips));
            }
            const shown = h('b', value(p));
            const range = h('input', {
                type: 'range', min: p.min, max: p.max, step: p.step, value: p.mine, 'aria-label': p.prop, oninput: () => {
                    if (api.won) return;
                    p.mine = Number(range.value);
                    shown.textContent = value(p);
                    refresh();
                }
            });
            p.sync = () => {
                range.value = p.mine;
                shown.textContent = value(p);
            };
            return h('div.tune-row', h('label', p.prop, shown), range);
        });

        api.body.append(h('div.tune-pair', h('figure', mine.stage, h('figcaption', t('Yours'))), h('figure', target.stage, h('figcaption', t('Target')))),
            h('div.tune-meter', h('div.bar', meter), label), h('div.tune-controls', controls), h('figure.code', h('figcaption', 'css'), h('pre', lines)));
        refresh();
        return {
            hint() {
                const p = props.find(x => String(x.mine) !== String(x.target));
                if (!p) return;
                p.mine = p.target;
                p.sync();
                refresh();
            }
        };
    }

    const KINDS_OF = {blanks, order, bug, match, flash, tune};

    window.Fun = {mascot, confetti, colour, langChip};
    window.Drills = {render};
})();
