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
        const big = level > 3;
        const n = big ? 6 : 4, boxH = 2, boxW = big ? 3 : 2;
        const symbols = shuffle(['h1', 'p', 'a', 'ul', 'img', 'div']).slice(0, n);
        const blanks = big ? [14, 18, 22][level - 4] || 24 : [6, 8, 10][level - 1];

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
        const grid = h('div.doku', {style: {gridTemplateColumns: `repeat(${n}, 1fr)`}});
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
            grid.replaceChildren(...cells.flatMap((row, r) => row.map((cell, c) => h('button.dk' + (cell.given ? '.given' : '') + (selected && selected[0] === r && selected[1] === c ? '.sel' : '') + (clash(r, c) ? '.bad' : '') + ((c + 1) % boxW === 0 && c < n - 1 ? '.edge-r' : '') + ((r + 1) % boxH === 0 && r < n - 1 ? '.edge-b' : ''), {
                type: 'button', disabled: cell.given, 'aria-label': t('Row {r}, column {c}', {r: r + 1, c: c + 1}), onclick: () => {
                    selected = [r, c];
                    draw();
                }
            }, cell.value === null ? '' : symbols[cell.value]))));
            pad.replaceChildren(...symbols.map((s, i) => h('button.chip-btn', {type: 'button', onclick: () => put(i)}, s)), h('button.chip-btn', {type: 'button', 'aria-label': t('Clear'), onclick: () => put(null)}, '✕'));
        }

        function put(v) {
            if (!selected) return toast(t('Tap an empty square first.'));
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
        const W = 7, H = 6, ME = 1, AI = 2, MISS = 3;
        const board = Array.from({length: H}, () => Array(W).fill(0));
        const pool = shuffle(ctx.questions);
        let wrongAnswers = 0, busy = false, current = null;
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
        const wins = (b, who) => {
            for (let r = 0; r < H; r++) for (let c = 0; c < W; c++) for (const [dr, dc] of [[0, 1], [1, 0], [1, 1], [1, -1]]) {
                let k = 0;
                while (k < 4 && b[r + dr * k] && b[r + dr * k][c + dc * k] === who) k++;
                if (k === 4) return true;
            }
            return false;
        };

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
            if (level <= 2 && Math.random() < .4 / level) return drop(board, moves[0], AI);
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
            drop(board, best, AI);
        }

        function draw() {
            view.replaceChildren(...Array.from({length: W}, (_, c) => h('button.four-col', {type: 'button', disabled: !!board[0][c] || busy, 'aria-label': t('Column {n}', {n: c + 1}), onclick: () => choose(c)},
                ...board.map(row => h('i.disc-' + ['e', 'me', 'ai', 'x'][row[c]])))));
        }

        function end() {
            if (wins(board, ME)) return ctx.finish({won: true, stars: wrongAnswers === 0 ? 3 : wrongAnswers <= 2 ? 2 : 1, score: Math.max(10, 100 - wrongAnswers * 15) * level}), true;
            if (wins(board, AI)) return ctx.finish({won: false, stars: 0}), true;
            if (!free(board).length) return ctx.finish({won: false, stars: 0, note: t('It’s a draw.')}), true;
            return false;
        }

        function choose(c) {
            busy = true;
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
                    status.textContent = ok ? t('Correct: your disc drops.') : t('Wrong: a red disc takes the square.');
                    setTimeout(() => {
                        panel.replaceChildren();
                        current = null;
                        drop(board, c, ok ? ME : MISS);
                        draw();
                        if (end()) return;
                        setTimeout(() => {
                            aiMove();
                            busy = false;
                            draw();
                            if (!end()) status.textContent = t('Choose a column.');
                        }, 350);
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
        const rounds = shuffle(ctx.items).slice(0, 4);
        let round = 0, lives = 3;
        const board = h('div.chess');
        const picked = new Set();
        for (let i = 0; i < 64; i++) {
            const r = Math.floor(i / 8) + 1, c = i % 8 + 1;
            const sq = h('button.sq.r' + r + '.c' + c + ((r + c) % 2 ? '.black' : '.white'), {
                type: 'button', 'aria-label': t('Row {r}, column {c}', {r, c}), onclick: () => {
                    picked.has(sq) ? picked.delete(sq) : picked.add(sq);
                    sq.classList.toggle('on');
                }
            });
            board.appendChild(sq);
        }
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

    window.Games = {whotag, tagdoku, fourline, selectors, linehop, dragLine};
})();
