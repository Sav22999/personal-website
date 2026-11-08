/* Fucina — the games. Each one receives a context from the app (see viewPlay) and reports its result with ctx.finish().
 * Levels: the content of selectors and linehop comes from the admin portal; the others scale with the level number. */
(function () {
    'use strict';

    const {h, btn, icon, toast} = UI;

    const shuffle = list => {
        const a = [...list];
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    };
    const rand = n => Math.floor(Math.random() * n);

    // ---------------------------------------------------------------- Who's the tag?

    const TRAITS = {
        block: 'Is it a block element?', inline: 'Does it flow inside the text?', text: 'Is it made for text?', semantic: 'Does it describe a part of the page?',
        attr: 'Does it need attributes to work?', interactive: 'Can the user interact with it?', void: 'Is it an empty element, with no closing tag?',
        media: 'Does it show media?', form: 'Is it used in forms?', list: 'Is it used in lists?', table: 'Is it used in tables?',
    };

    function whotag(ctx) {
        const {t, level} = ctx;
        const size = Math.min(ctx.items.length, 6 + level * 2);
        const cards = shuffle(ctx.items).slice(0, size).map(c => ({...c, out: false}));
        const hidden = cards[rand(cards.length)];
        const allowed = Math.max(3, 7 - Math.floor(level / 2));
        let asked = 0;
        const log = h('ul.qa-log');
        const board = h('div.tag-cards');
        const questions = h('div.qa');
        const left = h('p.game-status');

        const lengthQ = {id: 'short', label: t('Does its name have at most 2 letters?'), test: c => c.name.length <= 2};
        const firstQ = {id: 'first', label: t('Does its name start with a letter from a to h?'), test: c => c.name[0] <= 'h'};
        const all = [...Object.keys(TRAITS).map(k => ({id: k, label: t(TRAITS[k]), test: c => c.traits.includes(k)})), lengthQ, firstQ];

        function draw() {
            left.textContent = t('{n} questions left: then tap the tag you suspect.', {n: allowed - asked});
            board.replaceChildren(...cards.map(c => h('button.tag-card' + (c.out ? '.out' : ''), {type: 'button', disabled: c.out, onclick: () => guess(c)}, `<${c.name}>`)));
            const useful = all.filter(q => {
                if (q.done) return false;
                const yes = cards.filter(c => !c.out && q.test(c)).length;
                return yes > 0 && yes < cards.filter(c => !c.out).length;
            });
            questions.replaceChildren(...(asked < allowed ? useful.map(q => h('button.chip-btn', {type: 'button', onclick: () => ask(q)}, q.label)) : []));
        }

        function ask(q) {
            const yes = q.test(hidden);
            q.done = true;
            asked++;
            cards.forEach(c => {
                if (q.test(c) !== yes) c.out = true;
            });
            log.prepend(h('li', h('span', q.label), h('b', yes ? t('Yes') : t('No'))));
            draw();
        }

        function guess(c) {
            const won = c === hidden;
            ctx.finish({won, stars: won ? (asked <= allowed - 2 ? 3 : asked < allowed ? 2 : 1) : 0, score: won ? (allowed - asked + 1) * 10 * level : 0, note: won ? null : t('It was <{name}>.', {name: hidden.name})});
        }

        ctx.onHint(() => {
            const wrong = shuffle(cards.filter(c => !c.out && c !== hidden));
            wrong.slice(0, Math.ceil(wrong.length / 3)).forEach(c => c.out = true);
            draw();
        });
        ctx.root.append(left, board, questions, log);
        draw();
    }

    // ---------------------------------------------------------------- Tagdoku

    function tagdoku(ctx) {
        const {t, level} = ctx;
        // Two levels for each size: 4×4 with 2×2 boxes, then 9×9 with 3×3 boxes, then 16×16 with 4×4 boxes (never larger).
        // The second level of each size leaves more squares empty.
        const box = level <= 2 ? 2 : level <= 4 ? 3 : 4;
        const n = box * box, boxH = box, boxW = box;
        const hard = level % 2 === 0 || level > 6;
        const pool = {
            2: ['h1', 'p', 'a', 'ul', 'img', 'div'],
            3: ['p', 'a', 'b', 'i', 'em', 'ul', 'ol', 'li', 'h1'],
            4: ['p', 'a', 'b', 'i', 'u', 's', 'em', 'ul', 'ol', 'li', 'h1', 'h2', 'br', 'hr', 'td', 'tr'],
        }[box];
        const symbols = shuffle(pool).slice(0, n);
        const blanks = Math.round(n * n * {2: [.4, .56], 3: [.45, .56], 4: [.38, .48]}[box][hard ? 1 : 0]);

        // A valid grid from a pattern, then shuffled inside its bands and stacks
        const rowsOrder = [], colsOrder = [];
        for (let b = 0; b < n / boxH; b++) rowsOrder.push(...shuffle(Array.from({length: boxH}, (_, i) => b * boxH + i)));
        for (let b = 0; b < n / boxW; b++) colsOrder.push(...shuffle(Array.from({length: boxW}, (_, i) => b * boxW + i)));
        const solution = rowsOrder.map(r => colsOrder.map(c => (r % boxH * boxW + Math.floor(r / boxH) + c) % n));
        const cells = solution.map(row => row.map(v => ({v, given: true, value: v})));
        shuffle(Array.from({length: n * n}, (_, i) => i)).slice(0, blanks).forEach(i => {
            const c = cells[Math.floor(i / n)][i % n];
            c.given = false;
            c.value = null;
        });

        let selected = null, mistakes = 0;
        const grid = h('div.doku.n' + n, {style: {gridTemplateColumns: `repeat(${n}, 1fr)`}});
        const pad = h('div.doku-pad');
        const status = h('p.game-status');

        function clash(r, c) {
            const v = cells[r][c].value;
            if (v === null) return false;
            for (let i = 0; i < n; i++) {
                if (i !== c && cells[r][i].value === v) return true;
                if (i !== r && cells[i][c].value === v) return true;
            }
            const br = r - r % boxH, bc = c - c % boxW;
            for (let i = br; i < br + boxH; i++) for (let j = bc; j < bc + boxW; j++) if ((i !== r || j !== c) && cells[i][j].value === v) return true;
            return false;
        }

        function draw() {
            status.textContent = t('Mistakes: {n}', {n: mistakes});
            // On the big grids the row, column and box of the selected square light up, and so do the squares with its tag
            const [sr, sc] = selected || [-1, -1];
            const sv = selected ? cells[sr][sc].value : null;
            const peer = (r, c) => selected && (r === sr || c === sc || (Math.floor(r / boxH) === Math.floor(sr / boxH) && Math.floor(c / boxW) === Math.floor(sc / boxW)));
            grid.replaceChildren(...cells.flatMap((row, r) => row.map((cell, c) => h('button.dk' + (cell.given ? '.given' : '') + (r === sr && c === sc ? '.sel' : peer(r, c) ? '.peer' : '') + (sv !== null && cell.value === sv && (r !== sr || c !== sc) ? '.same' : '') + (clash(r, c) ? '.bad' : '') + ((c + 1) % boxW === 0 && c < n - 1 ? '.edge-r' : '') + ((r + 1) % boxH === 0 && r < n - 1 ? '.edge-b' : ''), {
                type: 'button', 'aria-label': t('Row {r}, column {c}', {r: r + 1, c: c + 1}), onclick: () => {
                    selected = [r, c];
                    draw();
                }
            }, cell.value === null ? '' : symbols[cell.value]))));
            pad.replaceChildren(...symbols.map((s, i) => h('button.chip-btn', {type: 'button', onclick: () => put(i)}, s)), h('button.chip-btn', {type: 'button', 'aria-label': t('Clear'), onclick: () => put(null)}, '✕'));
        }

        function put(v) {
            if (!selected || cells[selected[0]][selected[1]].given) return toast(t('Tap an empty square first.'));
            const [r, c] = selected;
            cells[r][c].value = v;
            if (v !== null && clash(r, c)) mistakes++;
            draw();
            const full = cells.every(row => row.every(x => x.value !== null));
            if (full && !cells.some((row, i) => row.some((_, j) => clash(i, j)))) ctx.finish({won: true, stars: mistakes === 0 ? 3 : mistakes <= 2 ? 2 : 1, score: Math.max(10, 100 - mistakes * 10) * level});
        }

        ctx.onHint(() => {
            const open = [];
            cells.forEach((row, r) => row.forEach((x, c) => {
                if (!x.given && x.value !== x.v) open.push([r, c]);
            }));
            if (!open.length) return;
            const [r, c] = open[rand(open.length)];
            selected = [r, c];
            put(cells[r][c].v);
        });
        ctx.root.append(status, grid, pad);
        draw();
    }

    // ---------------------------------------------------------------- Four in a line

    function fourline(ctx) {
        const {t, level, rich} = ctx;
        const W = 7, H = 6, ME = 1, AI = 2;
        const board = Array.from({length: H}, () => Array(W).fill(0));
        const pool = shuffle(ctx.questions);
        let wrongAnswers = 0, busy = false, current = null, picked = -1, last = null, line = [];
        const view = h('div.four');
        const status = h('p.game-status', t('Choose a column.'));
        const panel = h('div.four-q');

        const drop = (b, c, who) => {
            for (let r = H - 1; r >= 0; r--) if (!b[r][c]) {
                b[r][c] = who;
                return r;
            }
            return -1;
        };
        const free = b => Array.from({length: W}, (_, c) => c).filter(c => !b[0][c]);
        /** The four squares of a line of `who`, or null. */
        const four = (b, who) => {
            for (let r = 0; r < H; r++) for (let c = 0; c < W; c++) for (const [dr, dc] of [[0, 1], [1, 0], [1, 1], [1, -1]]) {
                let k = 0;
                while (k < 4 && b[r + dr * k] && b[r + dr * k][c + dc * k] === who) k++;
                if (k === 4) return [0, 1, 2, 3].map(i => [r + dr * i, c + dc * i]);
            }
            return null;
        };
        const wins = (b, who) => !!four(b, who);

        /** Plain minimax with alpha-beta: higher levels look further ahead. */
        function score(b) {
            let s = 0;
            for (let r = 0; r < H; r++) if (b[r][3] === AI) s += 3; else if (b[r][3] === ME) s -= 3;
            return s;
        }

        function search(b, depth, alpha, beta, maxing) {
            if (wins(b, AI)) return 1000 + depth;
            if (wins(b, ME)) return -1000 - depth;
            const moves = free(b);
            if (!depth || !moves.length) return score(b);
            let best = maxing ? -Infinity : Infinity;
            for (const c of moves) {
                const copy = b.map(row => [...row]);
                drop(copy, c, maxing ? AI : ME);
                const v = search(copy, depth - 1, alpha, beta, !maxing);
                if (maxing) {
                    best = Math.max(best, v);
                    alpha = Math.max(alpha, v);
                } else {
                    best = Math.min(best, v);
                    beta = Math.min(beta, v);
                }
                if (beta <= alpha) break;
            }
            return best;
        }

        function aiMove() {
            const moves = shuffle(free(board));
            if (!moves.length) return;
            // The first levels sometimes play at random, to leave room for learning
            if (level <= 2 && Math.random() < .4 / level) return last = [drop(board, moves[0], AI), moves[0]];
            let best = moves[0], top = -Infinity;
            for (const c of moves) {
                const copy = board.map(row => [...row]);
                drop(copy, c, AI);
                const v = search(copy, Math.min(level, 5), -Infinity, Infinity, false);
                if (v > top) {
                    top = v;
                    best = c;
                }
            }
            last = [drop(board, best, AI), best];
        }

        /**
         * The last disc falls from the top of its column (--fall: how many rows), the column being answered for glows,
         * and the four of a winning line pulse.
         */
        function draw() {
            const inLine = (r, c) => line.some(([a, b]) => a === r && b === c);
            view.replaceChildren(...Array.from({length: W}, (_, c) => h('button.four-col' + (c === picked ? '.picked' : ''), {type: 'button', disabled: !!board[0][c] || busy, 'aria-label': t('Column {n}', {n: c + 1}), onclick: () => choose(c)},
                ...board.map((row, r) => {
                    const fall = last && last[0] === r && last[1] === c;
                    return h('i.disc-' + ['e', 'me', 'ai'][row[c]] + (fall ? '.fall' : '') + (inLine(r, c) ? '.win' : ''), fall ? {style: {'--fall': r + 1}} : {});
                }))));
            last = null;
        }

        function end() {
            const mine = four(board, ME), theirs = mine ? null : four(board, AI);
            const over = mine || theirs || !free(board).length;
            if (!over) return false;
            busy = true;
            if (mine || theirs) {
                line = mine || theirs;
                draw();
            }
            // A moment to see the line before the result
            setTimeout(() => {
                if (mine) ctx.finish({won: true, stars: wrongAnswers === 0 ? 3 : wrongAnswers <= 2 ? 2 : 1, score: Math.max(10, 100 - wrongAnswers * 15) * level});
                else if (theirs) ctx.finish({won: false, stars: 0});
                else ctx.finish({won: false, stars: 0, note: t('It’s a draw.')});
            }, mine || theirs ? 1300 : 500);
            return true;
        }

        function choose(c) {
            busy = true;
            picked = c;
            draw();
            current = pool.shift() || shuffle(ctx.questions)[0];
            const q = current;
            const buttons = q.options.map((o, i) => h('button.option', {
                type: 'button', onclick: () => {
                    buttons.forEach(b => b.disabled = true);
                    buttons[q.a].classList.add('right');
                    const ok = i === q.a;
                    if (!ok) {
                        buttons[i].classList.add('wrong');
                        wrongAnswers++;
                    }
                    status.textContent = ok ? t('Correct: your disc drops.') : t('Wrong: the computer drops a red disc.');
                    // A modified four in a row: a right answer drops your green disc in your column,
                    // a wrong one lets the computer drop its red disc wherever it likes. Nobody else plays.
                    setTimeout(() => {
                        panel.replaceChildren();
                        current = null;
                        picked = -1;
                        if (ok) last = [drop(board, c, ME), c];
                        else aiMove();
                        busy = false;
                        draw();
                        if (!end()) status.textContent = t('Choose a column.');
                    }, ok ? 500 : 1400);
                }
            }, h('span', rich(o))));
            current.buttons = buttons;
            panel.replaceChildren(h('h3', rich(q.q)), h('div.options', buttons));
            panel.scrollIntoView({behavior: 'smooth', block: 'nearest'});
        }

        ctx.onHint(() => {
            if (!current) return toast(t('Choose a column first.'));
            const wrong = shuffle(current.options.map((_, i) => i).filter(i => i !== current.a && !current.buttons[i].disabled));
            wrong.slice(0, Math.ceil(wrong.length / 2)).forEach(i => {
                current.buttons[i].disabled = true;
                current.buttons[i].classList.add('gone');
            });
        });
        ctx.root.append(status, view, panel);
        draw();
    }

    // ---------------------------------------------------------------- CSS board

    function selectors(ctx) {
        const {t, level} = ctx;
        // The board grows with the level: 3×3, 5×5, 7×7, 9×9, then 10×10
        const N = [3, 5, 7, 9, 10][level - 1] || 10;
        const board = h('div.chess.n' + N, {style: {gridTemplateColumns: `repeat(${N}, 1fr)`}});
        const picked = new Set();
        for (let i = 0; i < N * N; i++) {
            const r = Math.floor(i / N) + 1, c = i % N + 1;
            const sq = h('button.sq.r' + r + '.c' + c + ((r + c) % 2 ? '.black' : '.white'), {
                type: 'button', 'aria-label': t('Row {r}, column {c}', {r, c}), onclick: () => {
                    picked.has(sq) ? picked.delete(sq) : picked.add(sq);
                    sq.classList.toggle('on');
                }
            });
            board.appendChild(sq);
        }
        const picks = selector => {
            try {
                const n = board.querySelectorAll(selector).length;
                return n > 0 && n < N * N;
            } catch (e) {
                return false;
            }
        };
        const rounds = shuffle(ctx.items.filter(x => picks(x.selector))).slice(0, 4);
        let round = 0, lives = 3;
        const sel = h('code.selector');
        const status = h('p.game-status');
        const target = () => {
            try {
                return new Set(board.querySelectorAll(rounds[round].selector));
            } catch (e) {
                return new Set();
            }
        };

        function draw() {
            sel.textContent = rounds[round].selector;
            status.textContent = t('Selector {a} of {b} · lives: {n}', {a: round + 1, b: rounds.length, n: lives});
        }

        function check() {
            const want = target();
            const same = want.size === picked.size && [...want].every(x => picked.has(x));
            if (same) {
                picked.forEach(x => x.classList.remove('on'));
                picked.clear();
                round++;
                if (round >= rounds.length) return ctx.finish({won: true, stars: lives, score: lives * 30 * level});
                toast(t('Right!'), 'ok');
                return draw();
            }
            lives--;
            board.classList.add('shake');
            setTimeout(() => board.classList.remove('shake'), 400);
            if (lives <= 0) return ctx.finish({won: false, stars: 0, note: t('Selector: {s}', {s: rounds[round].selector})});
            toast(t('Not the right squares.'), 'error');
            draw();
        }

        ctx.onHint(() => {
            const missing = [...target()].filter(x => !picked.has(x));
            const extra = [...picked].filter(x => !target().has(x));
            const fix = extra[0] || missing[0];
            if (fix) fix.click();
        });
        if (!rounds.length) {
            ctx.root.append(h('p', t('This level has no content yet.')));
            return;
        }
        ctx.root.append(status, sel, board, h('div.step-actions', btn(t('Check'), {icon: 'check', onclick: check})));
        draw();
    }

    // ---------------------------------------------------------------- Line hop

    /**
     * Drag a row of a list to its place: the others make room while it moves.
     * The row changes position in the page, which drops pointer capture, so the listeners stay on window.
     * By touch only the grip drags, so the rest of the row still scrolls the page.
     * Rows carry data-i; onDrop gets the new order of those indexes. While list.dataset.dragged is set,
     * the click that follows the drop must not count as a tap.
     */
    function dragLine(e, row, list, onDrop) {
        if (e.button !== 0) return;
        if (e.pointerType !== 'mouse' && !e.target.closest('.grip')) return;
        e.preventDefault();
        const y0 = e.clientY;
        let moved = false;
        const move = ev => {
            if (!moved && Math.abs(ev.clientY - y0) < 6) return;
            if (!moved) {
                moved = true;
                list.dataset.dragged = '1';
                list.querySelectorAll('.sel').forEach(x => x.classList.remove('sel'));
                row.classList.add('dragging');
            }
            const before = [...list.children].filter(x => x !== row).find(x => {
                const r = x.getBoundingClientRect();
                return ev.clientY < r.top + r.height / 2;
            }) || null;
            if (row.nextElementSibling !== before) list.insertBefore(row, before);
        };
        const stop = () => {
            window.removeEventListener('pointermove', move);
            window.removeEventListener('pointerup', stop);
            window.removeEventListener('pointercancel', stop);
            if (!moved) return;
            row.classList.remove('dragging');
            setTimeout(() => delete list.dataset.dragged, 0);
            onDrop([...list.children].map(x => +x.dataset.i));
        };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', stop);
        window.addEventListener('pointercancel', stop);
    }

    function linehop(ctx) {
        const {t, level} = ctx;
        const rounds = shuffle(ctx.items).slice(0, 2);
        let round = 0, moves = 0, extra = 0;
        const list = h('div.lines');
        const title = h('p.game-status');
        let lines = [], target = [], selected = null;

        /** From the fourth level the indentation is gone: it no longer gives the order away. */
        const shown = s => level >= 4 ? s.trim() : s;

        function load() {
            target = rounds[round].lines.map(shown);
            do lines = shuffle(target); while (lines.join('\n') === target.join('\n') && target.length > 1);
            selected = null;
            moves = 0;
            draw();
        }

        function draw() {
            title.textContent = `${rounds[round].title} · ${t('Moves: {n}', {n: moves})}`;
            list.replaceChildren(...lines.map((l, i) => {
                const row = h('button.line' + (selected === i ? '.sel' : '') + (l === target[i] ? '.fit' : ''), {type: 'button', 'data-i': i, onclick: () => {
                    if (!list.dataset.dragged) tap(i);
                }}, h('span.grip', {'aria-hidden': 'true'}, icon('menu')), h('code', l));
                row.addEventListener('pointerdown', e => dragLine(e, row, list, order => {
                    selected = null;
                    if (order.some((k, n) => k !== n)) moves++;
                    lines = order.map(k => lines[k]);
                    settle();
                }));
                return row;
            }));
        }

        function tap(i) {
            if (selected === null) selected = i;
            else {
                if (selected !== i) {
                    [lines[selected], lines[i]] = [lines[i], lines[selected]];
                    moves++;
                }
                selected = null;
            }
            settle();
        }

        function settle() {
            draw();
            if (lines.join('\n') === target.join('\n')) {
                extra += Math.max(0, moves - (target.length - 1));
                round++;
                if (round >= rounds.length) return ctx.finish({won: true, stars: extra === 0 ? 3 : extra <= 3 ? 2 : 1, score: Math.max(10, 100 - extra * 5) * level});
                toast(t('Right!'), 'ok');
                load();
            }
        }

        ctx.onHint(() => {
            const i = lines.findIndex((l, k) => l !== target[k]);
            if (i < 0) return;
            const j = lines.findIndex((l, k) => k > i && l === target[i]);
            selected = i;
            tap(j);
        });
        if (!rounds.length) {
            ctx.root.append(h('p', t('This level has no content yet.')));
            return;
        }
        ctx.root.append(h('p.lead', t('Drag the lines into the right order, or tap two lines to swap them: green lines are in the right place.')), title, list);
        load();
    }

    // ---------------------------------------------------------------- Rounds of choices (shared by the quick games)

    /**
     * A run of rounds with one right option each: three mistakes and the level is lost.
     * make(round) returns {prompt: node, options: [{label, ok, swatch?}], explain?: string, swatches?: true}.
     */
    function quizRounds(ctx, total, make) {
        const {t, level} = ctx;
        const MAX = 3;
        let round = 0, mistakes = 0, current = null, buttons = [];
        const status = h('p.game-status');
        const stage = h('div.qr');
        const feedback = h('p.qr-why');

        function next() {
            if (round >= total) return ctx.finish({won: true, stars: mistakes === 0 ? 3 : mistakes === 1 ? 2 : 1, score: (total - mistakes) * 10 * level});
            current = make(round);
            status.textContent = t('Round {a} of {b} · mistakes: {n} of {m}', {a: round + 1, b: total, n: mistakes, m: MAX});
            feedback.textContent = '';
            const keys = 'ABCD';
            buttons = shuffle(current.options).map((o, i) => {
                const b = h('button.option' + (o.swatch ? '.swatch-opt' : ''), {type: 'button', onclick: () => answer(b, o)},
                    h('b.opt-key', keys[i]), o.swatch ? h('i.swatch', {style: {background: o.swatch}, 'aria-label': t('Option {k}', {k: keys[i]})}) : h('span', o.label));
                b.ok = o.ok;
                return b;
            });
            stage.replaceChildren(current.prompt, h('div.options' + (current.swatches ? '.swatch-grid' : ''), buttons));
            stage.classList.remove('qr-in');
            void stage.offsetWidth;
            stage.classList.add('qr-in');
        }

        function answer(b, o) {
            buttons.forEach(x => x.disabled = true);
            buttons.find(x => x.ok).classList.add('right');
            if (!o.ok) {
                b.classList.add('wrong');
                mistakes++;
            }
            if (current.explain) feedback.textContent = current.explain;
            round++;
            if (mistakes >= MAX) return setTimeout(() => ctx.finish({won: false, stars: 0, note: current.explain || null}), 1400);
            setTimeout(next, o.ok ? 900 : 2000);
        }

        ctx.onHint(() => {
            const wrong = shuffle(buttons.filter(b => !b.ok && !b.disabled));
            wrong.slice(0, Math.max(1, Math.floor(wrong.length / 2))).forEach(b => b.disabled = true);
        });
        ctx.root.append(status, stage, feedback);
        next();
    }

    // ---------------------------------------------------------------- Right shade

    const hsl2rgb = (hue, s, l) => {
        s /= 100;
        l /= 100;
        const k = n => (n + hue / 30) % 12, a = s * Math.min(l, 1 - l);
        return [0, 8, 4].map(n => Math.round(255 * (l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1))))));
    };
    const hex2 = v => v.toString(16).padStart(2, '0');
    const NAMED = {red: '#ff0000', orange: '#ffa500', gold: '#ffd700', yellow: '#ffff00', green: '#008000', lime: '#00ff00', teal: '#008080', cyan: '#00ffff',
        blue: '#0000ff', navy: '#000080', purple: '#800080', magenta: '#ff00ff', pink: '#ffc0cb', brown: '#a52a2a', gray: '#808080', black: '#000000', white: '#ffffff', tomato: '#ff6347'};

    function shade(ctx) {
        const {t, level} = ctx;
        // A colour in the format of the level; the others are more and more alike as the level grows
        const formats = ['named', 'hex3', 'hex6', 'rgb', 'hsl'];
        const write = (format, [hh, s, l]) => {
            const [r, g, b] = hsl2rgb(hh, s, l);
            if (format === 'hex6') return '#' + hex2(r) + hex2(g) + hex2(b);
            if (format === 'rgb') return `rgb(${r}, ${g}, ${b})`;
            return `hsl(${hh}, ${s}%, ${l}%)`;
        };
        const near = [null, 120, 90, 60, 40, 28, 20][Math.min(level, 6)];

        function hslRound() {
            const format = level >= 6 ? formats[1 + rand(4)] : formats[Math.min(level, 5) - 1];
            const base = [rand(360), 55 + rand(35), 38 + rand(25)];
            // Two neighbours of the hue on either side, and a third further away or (from level 4) lighter or darker
            const others = [near, -near, 2 * near].map((o, k) => k === 2 && level >= 4
                ? [base[0], base[1], base[2] + (base[2] > 50 ? -1 : 1) * (14 + rand(8))]
                : [(base[0] + o + rand(8) + 720) % 360, base[1], base[2]]);
            if (format === 'hex3') {
                const pick = () => [0, 0, 0].map(() => '0369cf'[rand(6)]);
                const hex3 = d => '#' + d.join('');
                const right = pick();
                const wrong = [];
                while (wrong.length < 3) {
                    const d = [...right];
                    const i = rand(3);
                    d[i] = '0369cf'[(('0369cf'.indexOf(d[i]) + 2 + rand(3)) % 6)];
                    if (!wrong.some(w => hex3(w) === hex3(d)) && hex3(d) !== hex3(right)) wrong.push(d);
                }
                return {right: hex3(right), wrong: wrong.map(hex3)};
            }
            return {right: write(format, base), wrong: others.map(o => write(format, o))};
        }

        function namedRound() {
            const names = shuffle(Object.keys(NAMED));
            return {right: names[0], wrong: names.slice(1, 4), css: n => NAMED[n]};
        }

        quizRounds(ctx, 8, i => {
            const c = level === 1 ? namedRound() : hslRound();
            const css = c.css || (x => x);
            // From level 2 one round in two goes the other way: the colour is shown, the code is chosen
            const reverse = level >= 2 && i % 2 === 1;
            if (reverse) return {
                prompt: h('div.qr-prompt', h('p', t('Which code makes this colour?')), h('i.swatch.big', {style: {background: css(c.right)}})),
                options: [{label: c.right, ok: true}, ...c.wrong.map(w => ({label: w, ok: false}))], explain: c.right,
            };
            return {
                prompt: h('div.qr-prompt', h('p', t('Tap the colour of this code.')), h('code.selector', c.right)),
                options: [{label: c.right, swatch: css(c.right), ok: true}, ...c.wrong.map(w => ({label: w, swatch: css(w), ok: false}))], swatches: true, explain: null,
            };
        });
    }

    // ---------------------------------------------------------------- Selector duel

    const SPEC = {
        el: ['a', 'p', 'li', 'nav', 'ul', 'div', 'button', 'h1', 'span', 'input', 'section'],
        cls: ['.menu', '.btn', '.card', '.active', '.title', '.item', '.big', '.dark'],
        id: ['#main', '#nav', '#app', '#logo', '#cart'],
        attr: ['[type="text"]', '[href]', '[disabled]', '[data-open]'],
        pc: [':hover', ':first-child', ':focus', ':checked', ':last-child', ':nth-child(2)'],
        pe: ['::before', '::after', '::first-line'],
    };

    /** A random selector for the level and its specificity [ids, classes, elements]. */
    function makeSelector(level) {
        const one = list => list[rand(list.length)];
        const usedIds = new Set(); // an id appears once in a page, so once in a selector
        const compound = () => {
            const s = [0, 0, 0];
            let text = '';
            const add = (part, k) => {
                text += part;
                s[k]++;
            };
            if (Math.random() < .65) add(one(SPEC.el), 2);
            if (Math.random() < (level <= 1 ? .3 : .2 + level * .03)) {
                const id = shuffle(SPEC.id).find(x => !usedIds.has(x));
                usedIds.add(id);
                add(id, 0);
            }
            for (const c of shuffle(SPEC.cls).slice(0, rand(level <= 1 ? 2 : 3))) add(c, 1);
            if (level >= 3 && Math.random() < .35) add(one(SPEC.attr), 1);
            if (level >= 3 && Math.random() < .35) add(one(SPEC.pc), 1);
            if (level >= 5 && Math.random() < .4) {
                // :not() and :is() count as their argument, :where() counts nothing
                const fn = one([':not', ':is', ':where']);
                const arg = Math.random() < .5 ? [one(SPEC.cls), [0, 1, 0]] : Math.random() < .5 ? [one(SPEC.id), [1, 0, 0]] : [one(SPEC.el), [0, 0, 1]];
                text += `${fn}(${arg[0]})`;
                if (fn !== ':where') arg[1].forEach((v, k) => s[k] += v);
            }
            if (!text) add(one(SPEC.el), 2);
            return [text, s];
        };
        const parts = level <= 1 ? 1 : 1 + rand(level <= 3 ? 2 : 3);
        let text = '';
        const spec = [0, 0, 0];
        for (let i = 0; i < parts; i++) {
            const [c, s] = compound();
            text += (i ? (Math.random() < .3 ? ' > ' : ' ') : '') + c;
            s.forEach((v, k) => spec[k] += v);
        }
        if (level >= 4 && Math.random() < .25) {
            text += one(SPEC.pe);
            spec[2]++;
        }
        return [text, spec];
    }

    function duel(ctx) {
        const {t, level} = ctx;
        const cmp = (a, b) => a[0] - b[0] || a[1] - b[1] || a[2] - b[2];
        const show = s => `(${s.join(', ')})`;
        quizRounds(ctx, 10, () => {
            let a, b, tries = 0;
            do {
                a = makeSelector(level);
                b = makeSelector(level);
                tries++;
            } while ((a[0] === b[0] || (level <= 2 && cmp(a[1], b[1]) === 0)) && tries < 50);
            const c = cmp(a[1], b[1]);
            return {
                prompt: h('div.qr-prompt', h('p', t('Which selector is more specific?'))),
                options: [
                    {label: h('code', a[0]), ok: c > 0},
                    {label: h('code', b[0]), ok: c < 0},
                    {label: t('Same specificity'), ok: c === 0},
                ],
                explain: `${a[0]} ${show(a[1])} · ${b[0]} ${show(b[1])}`,
            };
        });
    }

    // ---------------------------------------------------------------- What does it print?

    const pickOne = list => list[rand(list.length)];
    const fmt = v => Array.isArray(v) ? '[' + v.map(x => typeof x === 'string' ? `'${x}'` : fmt(x)).join(', ') + ']' : String(v);

    /** Snippets by level: each returns the code, what it prints and some likely mistakes. */
    const OUTPUTS = [
        // 1: numbers and strings
        [
            () => {
                const a = 2 + rand(9), b = 2 + rand(9);
                return {code: `const a = ${a};\nconst b = ${b};\nconsole.log(a + b);`, out: a + b, wrong: [`${a}${b}`, a * b, a - b]};
            },
            () => {
                const name = pickOne(['Luca', 'Sara', 'Anna', 'Marco']);
                return {code: `const name = '${name}';\nconsole.log('Ciao, ' + name);`, out: `Ciao, ${name}`, wrong: ['Ciao, name', `Ciao,${name}`, `'Ciao, ' + name`]};
            },
            () => {
                const w = pickOne(['fucina', 'codice', 'tag', 'scintilla', 'css']);
                return {code: `console.log('${w}'.length);`, out: w.length, wrong: [w.length - 1, w.length + 1, w]};
            },
            () => {
                const a = 2 + rand(8);
                return {code: `let x = ${a};\nx = x * 2;\nconsole.log(x);`, out: a * 2, wrong: [a, a + 2, 'x * 2']};
            },
        ],
        // 2: types and coercion
        [
            () => {
                const a = 1 + rand(9), b = 1 + rand(9);
                return {code: `console.log('${a}' + ${b});`, out: `${a}${b}`, wrong: [a + b, 'NaN', `'${a}'${b}`]};
            },
            () => {
                const a = 5 + rand(9), b = 1 + rand(4);
                return {code: `console.log('${a}' - ${b});`, out: a - b, wrong: [`${a}${b}`, 'NaN', `${a}-${b}`]};
            },
            () => {
                const [v, type] = pickOne([['42', 'number'], ["'ciao'", 'string'], ['true', 'boolean'], ['null', 'object'], ['undefined', 'undefined'], ['[1, 2]', 'object'], ['3.5', 'number']]);
                return {code: `console.log(typeof ${v});`, out: type, wrong: ['number', 'string', 'boolean', 'object', 'undefined', 'array', 'null'].filter(x => x !== type)};
            },
            () => {
                const a = 1 + rand(8);
                return {code: `console.log(true + ${a});`, out: a + 1, wrong: [`true${a}`, 'NaN', a]};
            },
        ],
        // 3: arrays
        [
            () => {
                const a = shuffle([3, 5, 7, 9, 11, 2]).slice(0, 3 + rand(3));
                return {code: `const a = [${a.join(', ')}];\nconsole.log(a.length);`, out: a.length, wrong: [a.length - 1, a.length + 1, a[a.length - 1]]};
            },
            () => {
                const a = shuffle(['rosso', 'verde', 'blu', 'giallo']), i = rand(3);
                return {code: `const a = ${fmt(a)};\nconsole.log(a[${i + 1}]);`, out: a[i + 1], wrong: [a[i], a[(i + 2) % 4], 'undefined']};
            },
            () => {
                const a = shuffle([1, 2, 3, 4, 5]).slice(0, 3);
                return {code: `const a = [${a.join(', ')}];\nconsole.log(a.push(8));`, out: 4, wrong: [fmt([...a, 8]), 8, 3]};
            },
            () => {
                const a = shuffle(['a', 'b', 'c', 'd']).slice(0, 3), miss = Math.random() < .4;
                const v = miss ? 'z' : pickOne(a);
                return {code: `const a = ${fmt(a)};\nconsole.log(a.indexOf('${v}'));`, out: a.indexOf(v), wrong: miss ? [0, 3, 'undefined'] : [a.indexOf(v) + 1, -1, 'undefined'].filter(x => x !== a.indexOf(v))};
            },
        ],
        // 4: loops and conditions
        [
            () => {
                const n = 3 + rand(5);
                return {code: `let s = 0;\nfor (let i = 1; i <= ${n}; i++) {\n  s += i;\n}\nconsole.log(s);`, out: n * (n + 1) / 2, wrong: [n * (n - 1) / 2, n, n * n]};
            },
            () => {
                const a = 10 + rand(20), b = 3 + rand(4);
                return {code: `console.log(${a} % ${b});`, out: a % b, wrong: [Math.floor(a / b), (a / b).toFixed(1), b - (a % b) || b]};
            },
            () => {
                const n = 1 + rand(20), lim = 10;
                return {code: `const n = ${n};\nconsole.log(n > ${lim} ? 'grande' : 'piccolo');`, out: n > lim ? 'grande' : 'piccolo', wrong: [n > lim ? 'piccolo' : 'grande', 'true', 'undefined']};
            },
            () => {
                const n = 10 + rand(40);
                let x = 1;
                while (x < n) x *= 2;
                return {code: `let x = 1;\nwhile (x < ${n}) {\n  x *= 2;\n}\nconsole.log(x);`, out: x, wrong: [x / 2, n, x * 2]};
            },
        ],
        // 5: array methods
        [
            () => {
                const a = shuffle([1, 2, 3, 4, 5]).slice(0, 3);
                return {code: `const a = [${a.join(', ')}];\nconsole.log(a.map(x => x * 2));`, out: fmt(a.map(x => x * 2)), wrong: [fmt(a), fmt(a.map(x => x + 2)), a.reduce((s, x) => s + x * 2, 0)]};
            },
            () => {
                const a = shuffle([1, 2, 3, 4, 5, 6]).slice(0, 5);
                return {code: `const a = [${a.join(', ')}];\nconsole.log(a.filter(x => x % 2 === 0));`, out: fmt(a.filter(x => x % 2 === 0)), wrong: [fmt(a.filter(x => x % 2)), fmt(a.map(x => x % 2 === 0)), a.filter(x => x % 2 === 0).length]};
            },
            () => {
                const a = shuffle([1, 2, 3, 4, 5, 10]).slice(0, 4);
                const sum = a.reduce((s, x) => s + x, 0);
                return {code: `const a = [${a.join(', ')}];\nconsole.log(a.reduce((s, x) => s + x, 0));`, out: sum, wrong: [sum - a[0], a.join(''), fmt(a)]};
            },
            () => {
                const a = shuffle(['html', 'css', 'js', 'php']).slice(0, 3);
                return {code: `const a = ${fmt(a)};\nconsole.log(a.join('-'));`, out: a.join('-'), wrong: [a.join(','), fmt(a), a.join(' - ')]};
            },
        ],
        // 6: scope, logic and objects
        [
            () => ({code: `let x = 1;\n{\n  let x = 2;\n}\nconsole.log(x);`, out: 1, wrong: [2, 'undefined', 'ReferenceError']}),
            () => {
                const [code, out, wrong] = pickOne([
                    ["console.log(0 || 'b');", 'b', [0, 'false', 'true']],
                    ["console.log(0 ?? 'b');", 0, ['b', 'null', 'undefined']],
                    ["console.log(null ?? 'b');", 'b', ['null', 'undefined', 0]],
                    ["console.log('' && 'b');", '', ['b', 'false', 'undefined']],
                ]);
                return {code, out: out === '' ? t('(an empty line)') : out, wrong};
            },
            () => ({code: 'console.log(NaN === NaN);', out: 'false', wrong: ['true', 'NaN', 'undefined']}),
            () => {
                const a = 1 + rand(5), b = 1 + rand(5);
                return {code: `const a = ${a}, b = ${b};\nconsole.log(\`\${a}\${b} e \${a + b}\`);`, out: `${a}${b} e ${a + b}`, wrong: [`${a + b} e ${a + b}`, `${a}${b} e ${a}${b}`, '${a}${b} e ${a + b}']};
            },
            () => ({code: 'const o = {a: 1};\no.a = 2;\nconsole.log(o.a);', out: 2, wrong: [1, 'TypeError', 'undefined']}),
        ],
    ];

    function output(ctx) {
        const {t, level} = ctx;
        const seen = new Set();
        quizRounds(ctx, 8, () => {
            // Mostly snippets of this level, sometimes of the ones before; the same snippet never twice in a game
            let s, tries = 0;
            do {
                const lv = Math.random() < .7 || level === 1 ? level : 1 + rand(level - 1);
                s = pickOne(OUTPUTS[Math.min(lv, OUTPUTS.length) - 1])();
            } while (seen.has(s.code) && ++tries < 20);
            seen.add(s.code);
            const right = String(s.out);
            const wrong = [...new Set(s.wrong.map(String))].filter(w => w !== right).slice(0, 3);
            return {
                prompt: h('div.qr-prompt', h('p', t('What does it print?')), h('pre.out-code', h('code', s.code))),
                options: [{label: h('code', right), ok: true}, ...wrong.map(w => ({label: h('code', w), ok: false}))],
                explain: t('It prints: {v}', {v: right}),
            };
        });
    }

    // ---------------------------------------------------------------- Flex lab

    const FLEX = {
        'justify-content': ['flex-start', 'center', 'flex-end', 'space-between', 'space-around', 'space-evenly'],
        'align-items': ['stretch', 'flex-start', 'center', 'flex-end'],
        'flex-direction': ['row', 'column', 'row-reverse', 'column-reverse'],
        'flex-wrap': ['nowrap', 'wrap', 'wrap-reverse'],
        'gap': ['0', '8px', '20px'],
    };

    function flexlab(ctx) {
        const {t, level} = ctx;
        const props = ['justify-content', 'align-items', 'flex-direction', 'flex-wrap', 'gap'].slice(0, Math.min(level, 5));
        const total = level >= 6 ? 4 : 3;
        const count = level >= 4 ? 7 : level >= 3 ? 3 : 4;
        let round = 0, changes = 0, needed = 0, mine = {}, goal = {};
        const status = h('p.game-status');
        const ghost = h('div.flex-box.ghost');
        const box = h('div.flex-box.mine');
        const stage = h('div.flex-stage', ghost, box);
        const controls = h('div.flex-controls');
        const css = h('pre.out-code.flex-css');

        const items = (wrap, sizes) => wrap.replaceChildren(...sizes.map((sz, i) => h('i.fi.fi' + (i % 5), {style: {width: sz[0] + 'px', height: sz[1] + 'px'}}, String(i + 1))));
        const apply = (el, v) => props.forEach(p => el.style.setProperty(p, v[p]));
        // Same layout = every item at the same place in the two boxes (two values can give the same picture)
        const where = el => [...el.children].map(x => [x.offsetLeft, x.offsetTop, x.offsetWidth, x.offsetHeight]);
        const same = () => {
            const a = where(ghost), b = where(box);
            return a.every((p, i) => p.every((v, k) => Math.abs(v - b[i][k]) < 2));
        };

        function load() {
            // Item sizes: the last level mixes them; align-items needs no fixed cross size to show stretch
            const sizes = Array.from({length: count}, () => level >= 6 ? [34 + rand(4) * 10, 34 + rand(3) * 10] : [count > 4 ? 64 : 44, 44]);
            items(ghost, sizes);
            items(box, sizes);
            mine = Object.fromEntries(props.map(p => [p, FLEX[p][0]]));
            apply(box, mine);
            // A goal that looks different from the start; a few tries at most, so the page can never hang
            let tries = 0;
            do {
                goal = Object.fromEntries(props.map(p => [p, Math.random() < .8 ? FLEX[p][1 + rand(FLEX[p].length - 1)] : FLEX[p][0]]));
                apply(ghost, goal);
            } while (same() && ++tries < 40);
            needed += props.filter(p => goal[p] !== mine[p]).length;
            draw();
        }

        function draw() {
            status.textContent = t('Layout {a} of {b} · changes: {n}', {a: round + 1, b: total, n: changes});
            controls.replaceChildren(...props.map(p => h('div.flex-prop', h('code', p + ':'), h('div.chips', FLEX[p].map(v => h('button.chip-btn' + (mine[p] === v ? '.on' : ''), {
                type: 'button', onclick: () => set(p, v)
            }, v))))));
            css.textContent = '.box {\n' + props.map(p => `  ${p}: ${mine[p]};`).join('\n') + '\n}';
        }

        function set(p, v) {
            if (mine[p] === v) return;
            mine[p] = v;
            changes++;
            apply(box, mine);
            draw();
            if (!same()) return;
            box.classList.add('matched');
            round++;
            setTimeout(() => {
                box.classList.remove('matched');
                if (round >= total) {
                    const extra = Math.max(0, changes - needed);
                    return ctx.finish({won: true, stars: extra <= 2 ? 3 : extra <= 7 ? 2 : 1, score: Math.max(10, 100 - extra * 5) * level});
                }
                toast(t('Right!'), 'ok');
                load();
            }, 900);
        }

        ctx.onHint(() => {
            const p = props.find(x => goal[x] !== mine[x]);
            if (p) set(p, goal[p]);
        });
        ctx.root.append(h('p.lead', t('Change the properties until the coloured boxes sit on the dashed ones.')), status, stage, css, controls);
        // The positions can be measured only once the board is on screen
        const start = () => stage.isConnected && stage.offsetWidth ? load() : setTimeout(start, 50);
        start();
    }

    window.Games = {whotag, tagdoku, fourline, selectors, linehop, shade, duel, flexlab, output, dragLine};
})();
