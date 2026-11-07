/*
 * ThinkHop — the logic puzzles: they are generated, so they never end. No DOM here and no language inside: texts are keys
 * of i18n-puzzles.js, and a value of a text can be {k: 'key'} itself.
 *
 * Whatever can be solved by trying every case IS solved that way (liars, boxes, who owns what, if-then, grids), so
 * a question is only asked when exactly one answer survives.
 */
(function (root) {
    'use strict';

    const E = root.ThinkHopEngine;
    const {pattern, withOptions, numberOptions, orderings, NAMES} = E;
    const YES = 'yes', NO = 'no', UNKNOWN = 'unknown';
    const names = (r, n) => r.shuffle(NAMES).slice(0, n);
    /** Three different wrong options out of a list. */
    const others = (r, list, right, n) => r.shuffle([...new Set(list)].filter(x => x !== right)).slice(0, n === undefined ? 3 : n);

    // ---------- what comes next, with pictures ----------
    const SETS = [
        ['🐸', '🦊', '🐼', '🐙', '🦉', '🐢'], ['🍕', '🍩', '🌮', '🍔', '🍟', '🧁'], ['⚽', '🏀', '🎾', '🎱', '🏐', '🏈'],
        ['🌵', '🌻', '🍄', '🌴', '🌷', '🍀'], ['🚗', '🚲', '🚀', '⛵', '🚁', '🛴'], ['🎩', '👑', '🧢', '🎓', '⛑️', '👒'],
    ];
    const ARROWS = ['⬆️', '↗️', '➡️', '↘️', '⬇️', '↙️', '⬅️', '↖️'];
    const MOONS = ['🌑', '🌒', '🌓', '🌔', '🌕', '🌖', '🌗', '🌘'];
    const CLOCKS = ['🕐', '🕑', '🕒', '🕓', '🕔', '🕕', '🕖', '🕗', '🕘', '🕙', '🕚', '🕛'];

    function emojiQuestion(r, build, why) {
        const set = r.shuffle(r.pick(SETS));
        const seq = build(...set);
        const right = seq[seq.length - 1];
        const q = {view: 'list', items: seq.slice(0, -1), why: [{k: why}]};
        return withOptions(r, q, right, others(r, set, right));
    }

    const repeated = (unit, length) => Array.from({length}, (_, i) => unit[i % unit.length]);
    pattern('pic-repeat3', 'pattern', .06, r => emojiQuestion(r, (a, b, c) => repeated([a, b, c], 8), 'why_pt_repeat'));
    pattern('pic-repeat4', 'pattern', .13, r => emojiQuestion(r, (a, b, c) => repeated([a, b, a, c], 8), 'why_pt_repeat'));
    pattern('pic-repeat5', 'pattern', .22, r => emojiQuestion(r, (a, b, c) => repeated([a, b, b, c, a], 13), 'why_pt_repeat'));
    pattern('pic-mirror', 'pattern', .27, r => emojiQuestion(r, (a, b, c, d) => [a, b, c, d, c, b, a], 'why_pt_mirror'));
    pattern('pic-grow', 'pattern', .34, r => emojiQuestion(r, (a, b) => [a, b, a, b, b, a, b, b, b, a], 'why_pt_grow'));
    pattern('pic-braid', 'pattern', .56, r => emojiQuestion(r, (a, b, c, d, e) => [a, d, b, e, c, d, a, e, b, d], 'why_pt_braid'));

    function cycle(r, set, steps, why) {
        const seq = [r.int(0, set.length - 1)];
        steps.forEach(s => seq.push((seq[seq.length - 1] + s) % set.length));
        const icons = seq.map(i => set[i]);
        const right = icons[icons.length - 1];
        const last = seq[seq.length - 1], step = steps[steps.length - 1];
        const near = [last + 1, last + set.length - 1, last + step, last + set.length - step, seq[seq.length - 2]].map(i => set[i % set.length]);
        const wrong = others(r, near, right);
        const q = {view: 'list', items: icons.slice(0, -1), why: [{k: why}]};
        return withOptions(r, q, right, wrong.length === 3 ? wrong : [...wrong, ...others(r, set, right)].filter((x, i, l) => l.indexOf(x) === i).slice(0, 3));
    }

    pattern('turn-1', 'pattern', .1, r => cycle(r, ARROWS, [1, 1, 1, 1], 'why_pt_step'));
    pattern('moon-2', 'pattern', .17, r => cycle(r, MOONS, [2, 2, 2, 2], 'why_pt_step'));
    pattern('clock-2', 'pattern', .15, r => cycle(r, CLOCKS, [2, 2, 2, 2], 'why_pt_step'));
    pattern('turn-3', 'pattern', .44, r => cycle(r, ARROWS, [3, 3, 3, 3, 3], 'why_pt_step'));
    pattern('clock-grow', 'pattern', .64, r => cycle(r, CLOCKS, [1, 2, 3, 4, 5], 'why_pt_grow_step'));
    pattern('clock-double', 'pattern', .88, r => cycle(r, CLOCKS, [1, 2, 4, 8], 'why_pt_double_step'));
    pattern('moon-alt', 'pattern', .74, r => cycle(r, MOONS, [1, 3, 1, 3, 1], 'why_pt_alt_step'));

    pattern('pic-rotate', 'pattern', .47, r => {
        const [a, b, c, d] = r.shuffle(r.pick(SETS));
        const q = {view: 'list', items: [a + b + c + d, b + c + d + a, c + d + a + b], why: [{k: 'why_pt_rotate'}]};
        return withOptions(r, q, d + a + b + c, [d + c + b + a, a + c + d + b, b + a + d + c]);
    });

    // ---------- letters ----------
    const ABC = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CONSONANTS = 'BCDFGHJKLMNPQRSTVWXZ';

    function lettersQuestion(r, items, right, why, pool) {
        const at = (pool || ABC).indexOf(right[0]);
        const near = [-2, -1, 1, 2, 3].map(k => (pool || ABC)[at + k]).filter(Boolean).map(x => right.length > 1 ? x + right.slice(1) : x);
        return withOptions(r, {view: 'list', items, why: [{k: why}]}, right, others(r, near, right));
    }

    const walk = (start, steps) => {
        const out = [start];
        steps.forEach(s => out.push(out[out.length - 1] + s));
        return out.map(i => ABC[i]);
    };
    const stepLetters = (id, d, step, count, why) => pattern(id, 'letters', d, r => {
        const steps = Array.from({length: count}, (_, i) => step(i));
        const total = steps.reduce((a, b) => a + b, 0);
        const low = Math.min(0, ...steps.map((_, i) => steps.slice(0, i + 1).reduce((a, b) => a + b, 0)));
        const seq = walk(r.int(-low, 25 - Math.max(total, 0) - 3), steps);
        return lettersQuestion(r, seq.slice(0, -1), seq[seq.length - 1], why);
    });
    stepLetters('abc-back', .09, () => -1, 5, 'why_lt_back');
    stepLetters('abc-2', .16, () => 2, 5, 'why_lt_skip');
    stepLetters('abc-3', .3, () => 3, 5, 'why_lt_skip');
    stepLetters('abc-alt', .54, i => (i % 2 ? 2 : 1), 6, 'why_lt_alt');
    stepLetters('abc-grow', .7, i => i + 1, 5, 'why_lt_grow');
    stepLetters('abc-zig', .78, i => (i % 2 ? -1 : 3), 7, 'why_lt_zig');

    pattern('abc-vowels', 'letters', .2, r => {
        const v = 'AEIOU', i = r.int(0, 1);
        const seq = r.next() < .5 ? [...v] : [...v].reverse();
        return withOptions(r, {view: 'list', items: seq.slice(i, 4), why: [{k: 'why_lt_vowels'}]}, seq[4], others(r, ['B', 'Y', 'E', 'A', 'I', 'O', 'U', 'Z'], seq[4]));
    });
    pattern('abc-ends', 'letters', .46, r => {
        const s = r.int(0, 6);
        const seq = [ABC[s], ABC[25 - s], ABC[s + 1], ABC[24 - s], ABC[s + 2], ABC[23 - s]];
        return lettersQuestion(r, seq.slice(0, -1), seq[5], 'why_lt_ends');
    });
    pattern('abc-pairs', 'letters', .42, r => {
        const s = r.int(0, 8), pair = i => ABC[s + i * 3] + ABC[s + i * 3 + 1];
        const right = pair(3);
        return withOptions(r, {view: 'list', items: [pair(0), pair(1), pair(2)], why: [{k: 'why_lt_pairs'}]}, right, [ABC[s + 8] + ABC[s + 9], ABC[s + 10] + ABC[s + 11], ABC[s + 9] + ABC[s + 11]]);
    });
    pattern('abc-mirror', 'letters', .62, r => {
        const s = r.int(0, 8), pair = i => ABC[s + i] + ABC[25 - s - i];
        const right = pair(3);
        return withOptions(r, {view: 'list', items: [pair(0), pair(1), pair(2)], why: [{k: 'why_lt_ends'}]}, right, [ABC[s + 3] + ABC[25 - s - 2], ABC[s + 4] + ABC[25 - s - 3], ABC[s + 2] + ABC[25 - s - 3]]);
    });
    pattern('abc-consonants', 'letters', 1.02, r => {
        const s = r.int(0, 10), step = r.pick([1, 2]);
        const seq = [0, 1, 2, 3, 4].map(i => CONSONANTS[s + i * step]);
        return lettersQuestion(r, seq.slice(0, -1), seq[4], 'why_lt_consonants', CONSONANTS);
    });
    pattern('abc-numbers', 'letters', 1.12, r => {
        const list = [1, 3, 6, 10, 15, 21].slice(0, 5).map(n => ABC[n - 1] + n);
        const right = list.pop();
        return withOptions(r, {view: 'list', items: list, why: [{k: 'why_lt_numbers'}]}, right, ['N14', 'P16', 'O14'].map(String));
    });

    // ---------- the odd picture out ----------
    const INTRUDERS = [
        ['sea', .1, ['🐙', '🐟', '🐬', '🦈', '🐳', '🦑'], ['🐫', '🦒', '🐇']],
        ['head', .12, ['🎩', '👑', '🧢', '🎓', '👒'], ['🧤', '👟', '🧦']],
        ['fly', .17, ['🦅', '🦉', '🦆', '🐝', '🦋', '🦇'], ['🐧', '🐢', '🐘']],
        ['hot', .22, ['🔥', '☀️', '🌋', '☕', '🌶️'], ['❄️', '🧊', '🍦']],
        ['wheels', .19, ['🚗', '🚲', '🛴', '🚌', '🚜', '🛵'], ['⛵', '🛶', '🚀']],
        ['round', .28, ['⚽', '🏀', '🌕', '🍊', '🎱', '🌍'], ['📦', '🎲', '📘']],
        ['cut', .4, ['✂️', '🔪', '🪓', '🪚'], ['🔨', '🧲', '🪛']],
        ['strings', .38, ['🎸', '🎻', '🪕'], ['🥁', '🎺', '🎷']],
        ['tree', .47, ['🍎', '🍐', '🍑', '🍊', '🥥', '🍒'], ['🍉', '🍓', '🥕']],
        ['eggs', .52, ['🐔', '🐢', '🐍', '🐧', '🐊', '🦆'], ['🐈', '🐕', '🐄']],
        ['ground', .58, ['🥕', '🥔', '🧅', '🧄'], ['🍅', '🍎', '🌽']],
        ['mammals', .66, ['🐬', '🦇', '🐋', '🐘', '🐕'], ['🦈', '🐧', '🐍']],
        ['insects', .8, ['🐝', '🐜', '🦋', '🐞', '🦗'], ['🕷️', '🦂', '🦀']],
    ];
    INTRUDERS.forEach(([key, d, members, outsiders]) => pattern('pic-' + key, 'intruder', d, r => {
        const odd = r.pick(outsiders);
        const q = {view: 'riddle', pk: 'q_intruder', pv: {set: key, odd}, why: [{k: 'why_in', v: {x: odd, y: {k: 'in_' + key}}}]};
        return withOptions(r, q, odd, r.shuffle(members).slice(0, 3));
    }));

    // ---------- small sudoku ----------
    /** A full grid: n symbols, each once per row, column and box (br x bc; no boxes when br is 0). */
    function fits(g, n, br, bc, at, v) {
        const row = Math.floor(at / n), col = at % n;
        for (let i = 0; i < n; i++) if (g[row * n + i] === v || g[i * n + col] === v) return false;
        if (br) {
            const r0 = row - row % br, c0 = col - col % bc;
            for (let i = 0; i < br; i++) for (let j = 0; j < bc; j++) if (g[(r0 + i) * n + c0 + j] === v) return false;
        }
        return true;
    }

    function fill(g, n, br, bc, r) {
        const at = g.indexOf(-1);
        if (at < 0) return true;
        for (const v of r.shuffle(Array.from({length: n}, (_, i) => i))) {
            if (!fits(g, n, br, bc, at, v)) continue;
            g[at] = v;
            if (fill(g, n, br, bc, r)) return true;
            g[at] = -1;
        }
        return false;
    }

    function solutions(g, n, br, bc, limit) {
        const at = g.indexOf(-1);
        if (at < 0) return 1;
        let found = 0;
        for (let v = 0; v < n && found < limit; v++) {
            if (!fits(g, n, br, bc, at, v)) continue;
            g[at] = v;
            found += solutions(g, n, br, bc, limit - found);
            g[at] = -1;
        }
        return found;
    }

    function makeGrid(r, n, br, bc, blanks, symbols) {
        const g = Array(n * n).fill(-1);
        fill(g, n, br, bc, r);
        const solved = g.slice();
        const holes = [];
        for (const at of r.shuffle(g.map((_, i) => i))) {
            if (holes.length === blanks) break;
            const keep = g[at];
            g[at] = -1;
            if (solutions(g.slice(), n, br, bc, 2) === 1) holes.push(at);
            else g[at] = keep;
        }
        const ask = holes[holes.length - 1]; // the last hole is the one that needs all the others
        const right = symbols[solved[ask]];
        const grid = Array.from({length: n}, (_, row) => Array.from({length: n}, (_, col) => {
            const at = row * n + col;
            return at === ask ? '?' : (g[at] < 0 ? '' : symbols[g[at]]);
        }));
        const q = {view: 'grid', grid, boxes: br ? [br, bc] : null, pk: br ? 'q_grid' : 'q_grid3', why: [{k: 'why_grid', v: {x: right}}]};
        return withOptions(r, q, right, others(r, symbols, right));
    }

    const gridSymbols = (r, n) => r.shuffle(r.pick(SETS)).slice(0, n);
    pattern('grid-3', 'grid', .15, r => makeGrid(r, 3, 0, 0, 4, gridSymbols(r, 4).slice(0, 3)));
    pattern('grid-4-easy', 'grid', .32, r => makeGrid(r, 4, 2, 2, 5, gridSymbols(r, 4)));
    pattern('grid-4', 'grid', .6, r => makeGrid(r, 4, 2, 2, 8, gridSymbols(r, 4)));
    pattern('grid-4-hard', 'grid', .95, r => makeGrid(r, 4, 2, 2, 10, gridSymbols(r, 4)));
    pattern('grid-4-max', 'grid', 1.2, r => makeGrid(r, 4, 2, 2, 12, gridSymbols(r, 4)));
    pattern('grid-6', 'grid', 1.5, r => makeGrid(r, 6, 2, 3, 16, ['1', '2', '3', '4', '5', '6']));
    pattern('grid-6-easy', 'grid', 1.34, r => makeGrid(r, 6, 2, 3, 12, ['1', '2', '3', '4', '5', '6']));
    pattern('grid-6-mid', 'grid', 1.68, r => makeGrid(r, 6, 2, 3, 19, ['1', '2', '3', '4', '5', '6']));
    pattern('grid-6-hard', 'grid', 1.85, r => makeGrid(r, 6, 2, 3, 22, ['1', '2', '3', '4', '5', '6']));
    pattern('grid-6-max', 'grid', 1.97, r => makeGrid(r, 6, 2, 3, 24, ['1', '2', '3', '4', '5', '6']));

    // ---------- turns and walks ----------
    const FOUR = ['⬆️', '➡️', '⬇️', '⬅️'];
    const TURNS = [['↻ 90°', 1], ['↺ 90°', 3], ['↻ 180°', 2], ['↺ 270°', 1], ['↻ 270°', 3], ['↻ 360°', 0]];

    function makeTurns(r, count, kinds) {
        const start = r.int(0, 3);
        const steps = Array.from({length: count}, () => r.pick(TURNS.slice(0, kinds)));
        const end = steps.reduce((s, x) => s + x[1], start) % 4;
        const q = {view: 'lines', lines: ['🐢 ' + FOUR[start], steps.map(x => x[0]).join('  ·  ')], ask: '🐢 = ?', pk: 'q_compass', why: [{lit: FOUR[start] + ' → ' + FOUR[end]}, {k: 'why_turns'}]};
        return withOptions(r, q, FOUR[end], FOUR.filter(x => x !== FOUR[end]));
    }

    pattern('turn-easy', 'compass', .2, r => makeTurns(r, 3, 2));
    pattern('turn-medium', 'compass', .5, r => makeTurns(r, 5, 3));
    pattern('turn-hard', 'compass', .84, r => makeTurns(r, 7, 5));
    pattern('turn-expert', 'compass', 1.16, r => makeTurns(r, 10, 6));
    pattern('turn-long', 'compass', 1.54, r => makeTurns(r, 14, 6));
    pattern('turn-marathon', 'compass', 1.82, r => makeTurns(r, 20, 6));

    const MOVES = {'⬆️': [-1, 0], '➡️': [0, 1], '⬇️': [1, 0], '⬅️': [0, -1]};

    /** `bump`: at the edge the turtle stays where it is; without it, the path never leaves the board. */
    function makeWalk(r, n, count, bump) {
        const label = (y, x) => ABC[y * n + x];
        let y = r.int(0, n - 1), x = r.int(0, n - 1);
        const start = [y, x];
        const path = [];
        let hits = 0;
        while (path.length < count) {
            const step = r.pick(FOUR);
            const [dy, dx] = MOVES[step];
            const inside = y + dy >= 0 && y + dy < n && x + dx >= 0 && x + dx < n;
            if (!inside && !bump) continue;
            path.push(step);
            if (inside) {
                y += dy;
                x += dx;
            } else hits++;
        }
        if (bump && !hits) return makeWalk(r, n, count, bump); // the rule must matter
        const right = label(y, x);
        const grid = Array.from({length: n}, (_, a) => Array.from({length: n}, (_, b) => a === start[0] && b === start[1] ? '🐢' : label(a, b)));
        const near = [];
        Object.values(MOVES).forEach(([dy, dx]) => {
            if (y + dy >= 0 && y + dy < n && x + dx >= 0 && x + dx < n) near.push(label(y + dy, x + dx));
        });
        const q = {view: 'grid', grid, lines: [path.join(' ')], pk: bump ? 'q_walk_bump' : 'q_walk', why: [{k: bump ? 'why_walk_bump' : 'why_walk', v: {x: right}}]};
        const wrong = others(r, near, right);
        const all = grid.flat().filter(c => c !== '🐢' && c !== right);
        return withOptions(r, q, right, [...wrong, ...others(r, all.filter(c => !wrong.includes(c)), right)].slice(0, 3));
    }

    pattern('walk-3', 'compass', .36, r => makeWalk(r, 3, 4, false));
    pattern('walk-4', 'compass', .68, r => makeWalk(r, 4, 7, false));
    pattern('walk-bump', 'compass', 1, r => makeWalk(r, 3, 8, true));
    pattern('walk-bump-long', 'compass', 1.28, r => makeWalk(r, 4, 12, true));
    pattern('walk-5', 'compass', 1.46, r => makeWalk(r, 5, 14, false));
    pattern('walk-bump-5', 'compass', 1.66, r => makeWalk(r, 5, 16, true));
    pattern('walk-bump-5-long', 'compass', 1.9, r => makeWalk(r, 5, 22, true));

    // ---------- lamps and buttons ----------
    const lampRow = s => s.map(on => on ? '💡' : '⚫').join('');

    function board(r, lamps, buttons) {
        for (; ;) {
            const list = Array.from({length: buttons}, () => Array.from({length: lamps}, () => r.next() < .5));
            const key = list.map(b => b.join());
            if (new Set(key).size === buttons && list.every(b => b.filter(Boolean).length >= 1 && b.filter(Boolean).length < lamps)) return list;
        }
    }

    const buttonLine = (b, i) => `${ABC[i]} → ${b.map((on, n) => on ? n + 1 : '').filter(Boolean).join(' ')}`;
    const press = (state, b) => state.map((on, i) => b[i] ? !on : on);

    function makeLamps(r, lamps, buttons, presses) {
        const list = board(r, lamps, buttons);
        const start = Array.from({length: lamps}, () => r.next() < .4);
        const order = Array.from({length: presses}, () => r.int(0, buttons - 1));
        const end = order.reduce((s, b) => press(s, list[b]), start);
        const right = lampRow(end);
        const wrong = new Set();
        while (wrong.size < 3) {
            const x = end.slice();
            const flips = r.int(1, 2);
            for (let i = 0; i < flips; i++) x[r.int(0, lamps - 1)] ^= true;
            const row = lampRow(x.map(Boolean));
            if (row !== right) wrong.add(row);
        }
        const q = {view: 'lines', lines: [lampRow(start), ...list.map(buttonLine)], ask: order.map(i => ABC[i]).join(' ') + ' = ?', pk: 'q_lamps', why: [{lit: right}, {k: 'why_lamps'}]};
        return withOptions(r, q, right, [...wrong]);
    }

    function makeLampsBack(r, lamps) {
        for (; ;) {
            const list = board(r, lamps, 4);
            const start = Array.from({length: lamps}, () => r.next() < .5);
            const at = r.int(0, 3), other = (at + 1 + r.int(0, 2)) % 4;
            // Two buttons pressed in any order: the pair must be the only one that gives this result
            const end = press(press(start, list[at]), list[other]);
            const pairs = [];
            for (let a = 0; a < 4; a++) for (let b = a + 1; b < 4; b++) pairs.push([a, b]);
            const good = pairs.filter(([a, b]) => lampRow(press(press(start, list[a]), list[b])) === lampRow(end));
            if (good.length !== 1) continue;
            const text = ([a, b]) => ABC[a] + ' + ' + ABC[b];
            const q = {view: 'lines', lines: [lampRow(start) + ' → ' + lampRow(end), ...list.map(buttonLine)], ask: '? + ? ', pk: 'q_lamps_back', why: [{lit: text(good[0])}, {k: 'why_lamps'}]};
            return withOptions(r, q, text(good[0]), r.shuffle(pairs.filter(p => p !== good[0])).slice(0, 3).map(text));
        }
    }

    pattern('lamps-3', 'lamps', .44, r => makeLamps(r, 3, 2, 2));
    pattern('lamps-4', 'lamps', .72, r => makeLamps(r, 4, 3, 3));
    pattern('lamps-5', 'lamps', 1.04, r => makeLamps(r, 5, 3, 5));
    pattern('lamps-6', 'lamps', 1.32, r => makeLamps(r, 6, 4, 6));
    pattern('lamps-back-4', 'lamps', 1.42, r => makeLampsBack(r, 4));
    pattern('lamps-7', 'lamps', 1.56, r => makeLamps(r, 7, 4, 7));
    pattern('lamps-back-5', 'lamps', 1.6, r => makeLampsBack(r, 5));
    pattern('lamps-back-6', 'lamps', 1.72, r => makeLampsBack(r, 6));
    pattern('lamps-8', 'lamps', 1.8, r => makeLamps(r, 8, 5, 9));
    pattern('lamps-back-8', 'lamps', 1.93, r => makeLampsBack(r, 8));

    // ---------- secret codes on invented words ----------
    const CODE_WORDS = ['BLIZ', 'GLOP', 'MURF', 'TROX', 'PLIM', 'DRAK', 'FLORB', 'QUIB', 'VONK', 'GRUB', 'NERP', 'TUNK', 'SNIB', 'WOMP', 'ZORK', 'FRAM', 'KLUD', 'BRONK'];
    const shift = (c, n) => ABC[(ABC.indexOf(c) + n + 26) % 26];
    const VOWELS = 'AEIOU';
    const CODES = {
        reverse: w => [...w].reverse().join(''),
        ends: w => w[w.length - 1] + w.slice(1, -1) + w[0],
        next: w => [...w].map(c => shift(c, 1)).join(''),
        prev: w => [...w].map(c => shift(c, -1)).join(''),
        two: w => [...w].map(c => shift(c, 2)).join(''),
        pairs: w => w.replace(/(.)(.)/g, '$2$1'),
        roll: w => w.slice(1) + w[0],
        vowels: w => [...w].map(c => VOWELS.includes(c) ? VOWELS[(VOWELS.indexOf(c) + 1) % 5] : c).join(''),
        backnext: w => [...w].reverse().map(c => shift(c, 1)).join(''),
        zigzag: w => [...w].map((c, i) => shift(c, i % 2 ? -1 : 1)).join(''),
        ramp: w => [...w].map((c, i) => shift(c, i + 1)).join(''),
        rollnext: w => [...(w.slice(1) + w[0])].map(c => shift(c, 1)).join(''),
        backtwo: w => [...w].reverse().map(c => shift(c, 2)).join(''),
        pairsnext: w => [...w.replace(/(.)(.)/g, '$2$1')].map(c => shift(c, 1)).join(''),
        backramp: w => [...w].reverse().map((c, i) => shift(c, i + 1)).join(''),
    };

    const codePattern = (rule, d) => pattern('code-' + rule, 'code', d, r => {
        for (; ;) {
            const [a, b, c] = r.shuffle(CODE_WORDS);
            const f = CODES[rule];
            // Two examples must leave one rule standing
            const twins = Object.values(CODES).filter(g => g(a) === f(a) && g(b) === f(b));
            if (twins.some(g => g(c) !== f(c))) continue;
            const right = f(c);
            const wrong = others(r, Object.values(CODES).map(g => g(c)), right);
            if (wrong.length < 3) continue;
            const q = {view: 'lines', lines: [`${a} → ${f(a)}`, `${b} → ${f(b)}`], ask: `${c} → ?`, pk: 'q_code', why: [{k: 'why_code_' + rule}]};
            return withOptions(r, q, right, wrong);
        }
    });
    [['reverse', .28], ['ends', .34], ['roll', .4], ['next', .48], ['prev', .52], ['pairs', .6], ['two', .64], ['vowels', .9], ['backnext', 1.06], ['zigzag', 1.22], ['rollnext', 1.26], ['ramp', 1.46], ['backtwo', 1.58], ['pairsnext', 1.7], ['backramp', 1.86]].forEach(([k, d]) => codePattern(k, d));

    // ---------- if-then with absurd events ----------
    const EVENTS = 16;
    // A statement is [kind, a, b]: its truth is checked on every possible world
    const TRUTH = {
        is: (w, a) => w[a], not: (w, a) => !w[a],
        imp: (w, a, b) => !w[a] || w[b], impn: (w, a, b) => !w[a] || !w[b],
        or: (w, a, b) => w[a] || w[b], nand: (w, a, b) => !(w[a] && w[b]),
        iff: (w, a, b) => w[a] === w[b], xor: (w, a, b) => w[a] !== w[b],
    };

    /** Yes when every world allowed by the statements says so, no when none does, otherwise it cannot be known. */
    function verdict(statements, ask) {
        const vars = 'abcd';
        const worlds = [];
        for (let m = 0; m < 16; m++) {
            const w = {};
            [...vars].forEach((v, i) => {
                w[v] = !!(m >> i & 1);
            });
            if (statements.every(([k, a, b]) => TRUTH[k](w, a, b))) worlds.push(w);
        }
        if (!worlds.length) return null;
        const yes = worlds.filter(w => w[ask]).length;
        return yes === worlds.length ? YES : (yes === 0 ? NO : UNKNOWN);
    }

    const IF_FORMS = [
        [.3, [['imp', 'a', 'b'], ['is', 'a']], 'b'],
        [.42, [['imp', 'a', 'b'], ['not', 'b']], 'a'],
        [.5, [['imp', 'a', 'b'], ['is', 'b']], 'a'],
        [.54, [['imp', 'a', 'b'], ['not', 'a']], 'b'],
        [.58, [['or', 'a', 'b'], ['not', 'a']], 'b'],
        [.68, [['imp', 'a', 'b'], ['imp', 'b', 'c'], ['is', 'a']], 'c'],
        [.76, [['nand', 'a', 'b'], ['is', 'a']], 'b'],
        [.8, [['or', 'a', 'b'], ['is', 'a']], 'b'],
        [.86, [['imp', 'a', 'b'], ['imp', 'b', 'c'], ['not', 'c']], 'a'],
        [.98, [['or', 'a', 'b'], ['imp', 'a', 'c'], ['imp', 'b', 'c']], 'c'],
        [1.08, [['imp', 'a', 'b'], ['impn', 'c', 'b'], ['is', 'c']], 'a'],
        [1.18, [['iff', 'a', 'b'], ['or', 'b', 'c'], ['not', 'c']], 'a'],
        [1.34, [['xor', 'a', 'b'], ['imp', 'a', 'c'], ['not', 'c']], 'b'],
        [1.48, [['imp', 'a', 'b'], ['impn', 'a', 'b']], 'a'],
        [1.6, [['or', 'a', 'b'], ['imp', 'a', 'c'], ['impn', 'b', 'c'], ['not', 'c']], 'a'],
        [1.72, [['xor', 'a', 'b'], ['iff', 'b', 'c'], ['imp', 'c', 'd'], ['not', 'd']], 'a'],
        [1.88, [['or', 'a', 'b'], ['imp', 'b', 'c'], ['imp', 'a', 'd'], ['impn', 'd', 'c'], ['is', 'c']], 'b'],
        [1.42, [['iff', 'a', 'b'], ['xor', 'b', 'c'], ['is', 'c']], 'a'],
        [1.52, [['imp', 'a', 'b'], ['imp', 'c', 'b'], ['or', 'a', 'c']], 'b'],
        [1.56, [['nand', 'a', 'b'], ['or', 'b', 'c'], ['not', 'c']], 'a'],
        [1.64, [['xor', 'a', 'b'], ['xor', 'b', 'c'], ['is', 'a']], 'c'],
        [1.76, [['iff', 'a', 'b'], ['impn', 'b', 'c'], ['or', 'c', 'd'], ['not', 'd']], 'a'],
        [1.8, [['imp', 'a', 'b'], ['imp', 'b', 'c'], ['or', 'c', 'd'], ['not', 'd']], 'a'],
        [1.84, [['xor', 'a', 'b'], ['iff', 'a', 'c'], ['nand', 'b', 'd'], ['is', 'd']], 'c'],
        [1.92, [['or', 'a', 'b'], ['or', 'c', 'd'], ['iff', 'a', 'c'], ['imp', 'b', 'd'], ['not', 'd']], 'a'],
        [1.96, [['xor', 'a', 'b'], ['xor', 'c', 'd'], ['iff', 'a', 'c'], ['imp', 'b', 'd'], ['is', 'a']], 'd'],
    ];
    IF_FORMS.forEach(([d, statements, ask], n) => pattern('if-' + n, 'ifthen', d, r => {
        const ev = {};
        const ids = r.shuffle(Array.from({length: EVENTS}, (_, i) => i + 1));
        'abcd'.split('').forEach((v, i) => {
            ev[v] = {k: 'ev_' + ids[i]};
        });
        const answer = verdict(statements, ask);
        const q = {
            view: 'facts', pk: 'q_ifthen',
            facts: r.shuffle(statements.map(([k, a, b]) => ({k: 'if_' + k, v: {a: ev[a], b: ev[b]}}))),
            ask: {k: 'if_ask', v: {a: ev[ask]}},
            why: [{k: 'why_nz_' + answer}, {k: answer === UNKNOWN ? 'why_if_unknown' : 'why_if_sure'}],
        };
        q.options = [YES, NO, UNKNOWN].map(k => ({k}));
        q.answer = [YES, NO, UNKNOWN].indexOf(answer);
        return q;
    }));

    // ---------- who always lies? ----------
    const SAYS = {
        liar: (w, s, x) => !w[x], honest: (w, s, x) => w[x],
        same: (w, s, x) => w[s] === w[x], differ: (w, s, x) => w[s] !== w[x],
        count: (w, s, x) => w.filter(h => !h).length === x,
        some: w => w.some(h => !h), none: w => w.every(Boolean), both: (w, s, x, y) => !w[x] && !w[y],
    };

    function makeLiars(r, n, kinds) {
        const people = names(r, n);
        for (; ;) {
            const claims = people.map((_, s) => {
                const kind = r.pick(kinds);
                const rest = r.shuffle(people.map((_, i) => i).filter(i => i !== s));
                return {kind, s, x: kind === 'count' ? r.int(1, n) : rest[0], y: rest[1]};
            });
            const worlds = [];
            for (let m = 0; m < 1 << n; m++) {
                const w = people.map((_, i) => !!(m >> i & 1));
                if (claims.every(c => SAYS[c.kind](w, c.s, c.x, c.y) === w[c.s])) worlds.push(w);
            }
            if (worlds.length !== 1) continue;
            const combo = w => people.map((p, i) => `${p} ${w[i] ? '😇' : '😈'}`).join(' · ');
            const right = combo(worlds[0]);
            const wrong = new Set();
            while (wrong.size < 3) {
                const w = people.map(() => r.next() < .5);
                if (combo(w) !== right) wrong.add(combo(w));
            }
            const line = c => ({
                k: 'li_' + c.kind,
                v: {a: people[c.s], b: c.kind === 'count' ? c.x : people[c.x], c: c.y === undefined ? '' : people[c.y]},
            });
            const q = {view: 'facts', pk: 'q_liars', facts: claims.map(line), ask: {k: 'li_ask'}, why: [{lit: right}, {k: 'why_li'}]};
            return withOptions(r, q, right, [...wrong]);
        }
    }

    pattern('liars-2', 'liars', .78, r => makeLiars(r, 2, ['liar', 'same', 'differ', 'honest']));
    pattern('liars-2-count', 'liars', .96, r => makeLiars(r, 2, ['liar', 'differ', 'count', 'some', 'none']));
    pattern('liars-3', 'liars', 1.24, r => makeLiars(r, 3, ['liar', 'honest', 'same', 'differ']));
    pattern('liars-3-count', 'liars', 1.44, r => makeLiars(r, 3, ['liar', 'same', 'count', 'some', 'both']));
    pattern('liars-4-plain', 'liars', 1.52, r => makeLiars(r, 4, ['liar', 'honest', 'same', 'differ']));
    pattern('liars-4', 'liars', 1.68, r => makeLiars(r, 4, ['liar', 'same', 'differ', 'count', 'both']));
    pattern('liars-5-plain', 'liars', 1.78, r => makeLiars(r, 5, ['liar', 'honest', 'same', 'differ']));
    pattern('liars-5', 'liars', 1.92, r => makeLiars(r, 5, ['liar', 'same', 'differ', 'count', 'both', 'some']));

    // ---------- the treasure chests ----------
    const CHESTS = ['🟥', '🟦', '🟩', '🟨'];
    const LABELS = {
        here: (at, own) => at === own, away: (at, own) => at !== own,
        in: (at, own, x) => at === x, out: (at, own, x) => at !== x,
    };

    function makeBoxes(r, n, truths, key) {
        const chests = CHESTS.slice(0, n);
        for (; ;) {
            const labels = chests.map((_, own) => {
                const kind = r.pick(Object.keys(LABELS));
                return {kind, own, x: (own + 1 + r.int(0, n - 2)) % n};
            });
            const count = at => labels.filter(l => LABELS[l.kind](at, l.own, l.x)).length;
            const places = chests.map((_, at) => at).filter(at => count(at) === truths);
            if (places.length !== 1) continue;
            const right = chests[places[0]];
            const fact = l => ({k: 'bx_line', v: {c: chests[l.own], s: {k: 'bx_' + l.kind, v: {x: chests[l.x]}}}});
            const q = {view: 'facts', pk: key, facts: labels.map(fact), ask: {k: 'bx_ask'}, why: [{k: 'why_boxes', v: {x: right}}]};
            return withOptions(r, q, right, chests.filter(c => c !== right));
        }
    }

    pattern('chests-3', 'boxes', .74, r => makeBoxes(r, 3, 1, 'q_boxes_t1'));
    pattern('chests-3-false', 'boxes', .92, r => makeBoxes(r, 3, 2, 'q_boxes_f1'));
    pattern('chests-4', 'boxes', 1.3, r => makeBoxes(r, 4, 1, 'q_boxes_t1'));
    pattern('chests-4-false', 'boxes', 1.5, r => makeBoxes(r, 4, 3, 'q_boxes_f1'));
    pattern('chests-4-two', 'boxes', 1.64, r => makeBoxes(r, 4, 2, 'q_boxes_t2'));
    pattern('chests-3-two', 'boxes', 1.12, r => makeBoxes(r, 3, 2, 'q_boxes_t2'));

    // ---------- who owns the octopus? ----------
    const PETS = ['🦜', '🐢', '🦔', '🐙', '🦎'];

    /** A case is {house: person at each position, pet: pet of each person}. */
    function makeOwners(r, n, houses) {
        const people = names(r, n), pets = r.shuffle(PETS).slice(0, n);
        const ids = people.map((_, i) => i);
        const perms = orderings(ids);
        const cases = [];
        (houses ? perms : [ids]).forEach(house => perms.forEach(pet => cases.push({house, pet})));
        const truth = r.pick(cases);
        const at = (c, p) => c.house.indexOf(p);
        const owner = (c, k) => c.pet.indexOf(k);
        const clues = [];
        ids.forEach(a => ids.forEach(k => {
            clues.push({k: 'wo_not', v: {a: people[a], p: pets[k]}, test: c => c.pet[a] !== k});
            if (houses && k !== a) {
                clues.push({k: 'wo_left', v: {a: people[a], b: people[k]}, test: c => at(c, a) < at(c, k)});
                clues.push({k: 'wo_next', v: {a: people[a], b: people[k]}, test: c => Math.abs(at(c, a) - at(c, k)) === 1});
                clues.push({k: 'wo_pet_left', v: {p: pets[a], q: pets[k]}, test: c => at(c, owner(c, a)) < at(c, owner(c, k))});
                clues.push({k: 'wo_pet_next', v: {a: people[a], p: pets[k]}, test: c => c.pet[a] !== k && Math.abs(at(c, a) - at(c, owner(c, k))) === 1});
            }
            if (houses && k < n) clues.push({k: 'wo_house_not', v: {a: people[a], n: k + 1}, test: c => at(c, a) !== k});
        }));
        let left = cases, facts = [];
        for (const clue of r.shuffle(clues.filter(c => c.test(truth)))) {
            if (left.length === 1) break;
            const narrowed = left.filter(clue.test);
            if (narrowed.length < left.length) {
                left = narrowed;
                facts.push(clue);
            }
        }
        facts.slice().forEach(f => {
            const rest = facts.filter(o => o !== f);
            if (cases.filter(c => rest.every(o => o.test(c))).length === 1) facts = rest;
        });
        const k = r.int(0, n - 1);
        const right = people[owner(truth, k)];
        const q = {
            view: 'facts', pk: houses ? 'q_whoowns_houses' : 'q_whoowns', pv: {n},
            facts: facts.map(({k: key, v}) => ({k: key, v})), ask: {k: 'wo_ask', v: {p: pets[k]}},
            why: [{lit: ids.map(i => `${people[i]} ${pets[truth.pet[i]]}`).join(' · ')}],
        };
        return withOptions(r, q, right, others(r, people, right));
    }

    pattern('pets-3', 'whoowns', .4, r => makeOwners(r, 3, false));
    pattern('pets-4', 'whoowns', .7, r => makeOwners(r, 4, false));
    pattern('pets-3-houses', 'whoowns', 1.1, r => makeOwners(r, 3, true));
    pattern('pets-5', 'whoowns', 1.02, r => makeOwners(r, 5, false));
    pattern('pets-4-houses', 'whoowns', 1.58, r => makeOwners(r, 4, true));
    pattern('pets-5-houses', 'whoowns', 1.95, r => makeOwners(r, 5, true));

    // ---------- days of the week ----------
    const dayOption = i => ({k: 'day_' + ((i % 7 + 7) % 7)});
    const SIMPLE_DAYS = [['today', 0], ['tomorrow', 1], ['yesterday', -1], ['after', 2], ['before', -2]];
    const TWISTED_DAYS = [...SIMPLE_DAYS, ['tom_yes', 0], ['yes_after', 1], ['tom_tom', 2], ['yes_before', -3], ['after_before', 0], ['after_after', 4]];

    function makeDays(r, list) {
        for (; ;) {
            const [[a, x], [b, y]] = r.shuffle(list);
            if (x === y) continue;
            const day = r.int(0, 6), right = day + y - x;
            const wrong = [right + 1, right - 1, day, right + 2, right - 2].filter(i => (i - right) % 7 !== 0);
            const seen = [...new Set(wrong.map(i => (i % 7 + 7) % 7))].slice(0, 3);
            const q = {
                view: 'riddle', pk: 'dy_ask', pv: {a: {k: 'dx_' + a}, b: {k: 'dx_' + b}, day: dayOption(day)},
                why: [{k: 'why_days', v: {a: {k: 'dx_' + b}, day: dayOption(right)}}],
            };
            const options = r.shuffle([dayOption(right), ...seen.map(dayOption)]);
            return Object.assign(q, {options, answer: options.findIndex(o => o.k === dayOption(right).k)});
        }
    }

    pattern('days-simple', 'days', .24, r => makeDays(r, SIMPLE_DAYS));
    pattern('days-twisted', 'days', .74, r => makeDays(r, TWISTED_DAYS));
    pattern('days-far', 'days', 1.0, r => {
        const day = r.int(0, 6), n = r.int(16, 300);
        const options = r.shuffle([0, 1, 2, 3].map(i => dayOption(day + n + i * 2)));
        const right = dayOption(day + n);
        return {
            view: 'riddle', pk: 'dy_far', pv: {day: dayOption(day), n}, why: [{k: 'why_days_far', v: {n, m: n % 7}}],
            options, answer: options.findIndex(o => o.k === right.k),
        };
    });

    // ---------- traps: the quick answer is the wrong one ----------
    const trap = (id, d, make) => pattern('trap-' + id, 'trap', d, r => {
        const {v, right, near} = make(r);
        return numberOptions(r, {view: 'riddle', pk: 'tr_' + id, pv: v, why: [{k: 'tr_' + id + '_w', v: Object.assign({x: right}, v)}]}, right, near);
    });

    trap('overtake', .3, r => {
        const n = r.int(2, 9);
        return {v: {n}, right: n, near: [n - 1, n + 1, 1]};
    });
    trap('pills', .46, r => {
        const n = r.int(3, 8), m = r.pick([10, 15, 20, 30]);
        return {v: {n, m}, right: (n - 1) * m, near: [n * m, (n + 1) * m, (n - 2) * m]};
    });
    trap('log', .5, r => {
        const n = r.int(3, 9), m = r.int(2, 6);
        return {v: {n, m}, right: (n - 1) * m, near: [n * m, (n + 1) * m, (n - 2) * m]};
    });
    trap('sister', .52, r => {
        const a = r.pick([4, 6, 8, 10, 12]), now = r.int(25, 80);
        return {v: {a, now}, right: now - a / 2, near: [now / 2 | 0, now - a, now + a / 2]};
    });
    trap('lily', .56, r => {
        const n = r.int(12, 60);
        return {v: {n}, right: n - 1, near: [n / 2 | 0, n - 2, n + 1]};
    });
    trap('stairs', .6, r => {
        const m = r.pick([10, 20, 30]), f = r.int(5, 9);
        return {v: {m, f}, right: (f - 1) * m / 2, near: [(f * m / 3) | 0, f * m, (f - 1) * m]};
    });
    trap('socks', .62, r => {
        const a = r.int(5, 14), b = r.int(5, 14);
        return {v: {a, b}, right: 3, near: [2, a + 1, b + 1, a + b]};
    });
    trap('brick', .7, r => {
        const a = r.int(1, 9);
        return {v: {a}, right: 2 * a, near: [a, a + (a >> 1), 3 * a]};
    });
    trap('ball', .76, r => {
        const d = r.pick([100, 200, 300]), b = r.pick([5, 10, 15, 20, 25]);
        return {v: {t: d + 2 * b, d}, right: b, near: [2 * b, b + 5, d / 10]};
    });
    trap('venn', .8, r => {
        const both = r.int(3, 9), a = both + r.int(3, 9), b = both + r.int(3, 9), none = r.int(2, 9);
        return {v: {n: a + b - both + none, a, b, both}, right: none, near: [none + both, a + b - both, Math.abs(a + b - both - none)]};
    });
    trap('snail', .84, r => {
        const a = r.int(3, 6), b = r.int(1, a - 1), h = a + (a - b) * r.int(3, 8);
        return {v: {h, a, b}, right: (h - a) / (a - b) + 1, near: [(h / (a - b)) | 0, ((h - a) / (a - b)), ((h - a) / (a - b)) + 2]};
    });
    trap('clock', .9, r => {
        const n = r.pick([3, 4, 5, 6, 7]), s = (n - 1) * r.int(1, 3);
        const m = r.pick([9, 10, 12]);
        return {v: {n, s, m}, right: s / (n - 1) * (m - 1), near: [s / (n - 1) * m, (s / n * m) | 0, s * 2]};
    });
    trap('socks-sure', .94, r => {
        const a = r.int(4, 12), b = r.int(4, 12);
        return {v: {a, b}, right: b + 2, near: [3, 2, a + 2, a + b]};
    });
    trap('cats', .98, r => {
        const n = r.int(3, 8), m = r.pick([20, 30, 40, 50, 60, 100]);
        return {v: {n, m}, right: n, near: [m, n * m, 1]};
    });
    trap('handshake', 1.03, r => {
        const n = r.int(5, 12);
        return {v: {n}, right: n * (n - 1) / 2, near: [n * (n - 1), n * n, n - 1]};
    });
    trap('socks-all', 1.09, r => {
        const a = r.int(4, 12), b = r.int(4, 12), c = r.int(4, 12);
        return {v: {a, b, c}, right: a + b + c - Math.min(a, b, c) + 1, near: [4, 3, a + b + c, a + b + c - Math.max(a, b, c) + 1]};
    });
    trap('fence', 1.26, r => {
        const n = r.int(6, 14), m = r.pick([2, 3, 4, 5]);
        return {v: {n, m}, right: n * m, near: [(n - 1) * m, (n + 1) * m, n + m]};
    });
    trap('frogs', 1.38, r => {
        const n = r.int(3, 7);
        return {v: {n}, right: Math.pow(2, n) - 1, near: [Math.pow(2, n), n * 2, n * n]};
    });
    trap('cards', 1.52, r => {
        const k = r.int(2, 6), n = 4;
        return {v: {k, n}, right: (k - 1) * n + 1, near: [k * n, k + n, (k - 1) * n]};
    });
})(typeof window !== 'undefined' ? window : globalThis);
