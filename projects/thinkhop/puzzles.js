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
        ['💎', '🔮', '🪙', '🧲', '🧭', '⏳'], ['🎸', '🎺', '🥁', '🎻', '🎷', '🪗'],
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
        const s = r.int(1, 12);
        const nums = [0, 1, 2, 3, 4].reduce((a, i) => [...a, (a.length ? a[a.length - 1] + i + 1 : s)], []);
        const list = nums.map(n => ABC[n - 1] + n);
        const right = list.pop();
        const rNum = nums[4], rIdx = rNum - 1;
        return withOptions(r, {view: 'list', items: list, why: [{k: 'why_lt_numbers'}]}, right, [
            ABC[(rIdx + 25) % 26] + (rNum - 1),
            ABC[(rIdx + 1) % 26] + (rNum + 1),
            ABC[rIdx] + (rNum - 1),
        ]);
    });

    // ---------- the odd picture out ----------
    const INTRUDERS = [
        ['sea', .1, ['🐙', '🐟', '🐬', '🦈', '🐳', '🦑'], ['🐫', '🦒', '🐇', '🦁']],
        ['head', .12, ['🎩', '👑', '🧢', '🎓', '👒'], ['🧤', '👟', '🧦', '🧣']],
        ['fly', .17, ['🦅', '🦉', '🦆', '🐝', '🦋', '🦇'], ['🐧', '🐢', '🐘', '🐄']],
        ['hot', .22, ['🔥', '☀️', '🌋', '☕', '🌶️'], ['❄️', '🧊', '🍦', '⛄']],
        ['cold', .25, ['❄️', '🧊', '⛄', '🍦', '🐧'], ['🔥', '☀️', '🌋', '🌶️', '☕']],
        ['wheels', .19, ['🚗', '🚲', '🛴', '🚌', '🚜', '🛵'], ['⛵', '🛶', '🚀', '🚁']],
        ['round', .28, ['⚽', '🏀', '🌕', '🍊', '🎱', '🌍'], ['📦', '🎲', '📘', '🧱']],
        ['sweet', .33, ['🍩', '🧁', '🍦', '🍫', '🍪'], ['🥦', '🧄', '🧅', '🥕', '🥔']],
        ['cut', .4, ['✂️', '🔪', '🪓', '🪚'], ['🔨', '🧲', '🪛', '🔦', '🔑']],
        ['strings', .38, ['🎸', '🎻', '🪕'], ['🥁', '🎺', '🎷', '🪗', '🔔']],
        ['write', .45, ['✏️', '🖊️', '🖍️', '🖌️'], ['🥄', '🍴', '🔪', '✂️', '🔨']],
        ['tree', .47, ['🍎', '🍐', '🍑', '🍊', '🥥', '🍒'], ['🍉', '🍓', '🥕', '🥔']],
        ['eggs', .52, ['🐔', '🐢', '🐍', '🐧', '🐊', '🦆'], ['🐈', '🐕', '🐄', '🦁']],
        ['ground', .58, ['🥕', '🥔', '🧅', '🧄'], ['🍅', '🍎', '🌽', '🍒', '🍋']],
        ['space', .62, ['🚀', '🛸', '🛰️', '☄️', '🪐'], ['🚲', '🚜', '⛵', '🚌', '🛴']],
        ['mammals', .66, ['🐬', '🦇', '🐋', '🐘', '🐕'], ['🦈', '🐧', '🐍', '🐊', '🦉']],
        ['reptiles', .76, ['🐢', '🐍', '🐊', '🦎'], ['🐸', '🐧', '🐳', '🦉', '🦈']],
        ['insects', .8, ['🐝', '🐜', '🦋', '🐞', '🦗'], ['🕷️', '🦂', '🦀', '🐙', '🐌']],
        ['feathers', 1.05, ['🐧', '🐔', '🦅', '🦉', '🦆', '🦜'], ['🦇', '🦋', '🐝', '🚁', '🚀']],
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
        const q = {cat: 'lamps', view: 'lamps', lines: [lampRow(start), ...list.map(buttonLine)], ask: order.map(i => ABC[i]).join(' ') + ' = ?', pk: 'q_lamps', why: [{lit: right}, {k: 'why_lamps'}]};
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
            const q = {cat: 'lamps', view: 'lamps', lines: [lampRow(start) + ' → ' + lampRow(end), ...list.map(buttonLine)], ask: '? + ? ', pk: 'q_lamps_back', why: [{lit: text(good[0])}, {k: 'why_lamps'}]};
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
    const CHESTS = ['🟥', '🟦', '🟩', '🟨', '🟪'];
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
    pattern('chests-3-zero', 'boxes', .85, r => makeBoxes(r, 3, 0, 'q_boxes_t0'));
    pattern('chests-3-false', 'boxes', .92, r => makeBoxes(r, 3, 2, 'q_boxes_f1'));
    pattern('chests-3-two', 'boxes', 1.12, r => makeBoxes(r, 3, 2, 'q_boxes_t2'));
    pattern('chests-4', 'boxes', 1.3, r => makeBoxes(r, 4, 1, 'q_boxes_t1'));
    pattern('chests-4-zero', 'boxes', 1.38, r => makeBoxes(r, 4, 0, 'q_boxes_t0'));
    pattern('chests-4-false', 'boxes', 1.5, r => makeBoxes(r, 4, 3, 'q_boxes_f1'));
    pattern('chests-4-two', 'boxes', 1.64, r => makeBoxes(r, 4, 2, 'q_boxes_t2'));
    pattern('chests-5-one', 'boxes', 1.76, r => makeBoxes(r, 5, 1, 'q_boxes_t1'));
    pattern('chests-5-two', 'boxes', 1.88, r => makeBoxes(r, 5, 2, 'q_boxes_t2'));

    // ---------- who owns the octopus? ----------
    const PETS = ['🦜', '🐢', '🦔', '🐙', '🦎', '🦦', '🦩', '🦙'];

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
    pattern('days-back', 'days', .52, r => {
        const day = r.int(0, 6), n = r.int(10, 120);
        const options = r.shuffle([0, 1, 2, 3].map(i => dayOption(day - n + i * 2)));
        const right = dayOption(day - n);
        return {
            view: 'riddle', pk: 'dy_ago', pv: {day: dayOption(day), n}, why: [{k: 'why_days_far', v: {n, m: n % 7}}],
            options, answer: options.findIndex(o => o.k === right.k),
        };
    });
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
    pattern('days-chain', 'days', 1.18, r => {
        const [[a, x], [b, y]] = r.shuffle(SIMPLE_DAYS);
        const day = r.int(0, 6), n = r.int(3, 18);
        const right = dayOption(day + y - x + n);
        const options = r.shuffle([0, 1, 2, 3].map(i => dayOption(day + y - x + n + i * 2)));
        return {
            view: 'riddle', pk: 'dy_chain', pv: {a: {k: 'dx_' + a}, b: {k: 'dx_' + b}, day: dayOption(day), n},
            why: [{k: 'why_days', v: {a: {k: 'dx_' + b}, day: right}}],
            options, answer: options.findIndex(o => o.k === right.k),
        };
    });
    pattern('days-meet', 'days', 1.48, r => {
        const pairs = [[2, 3, 6], [2, 5, 10], [3, 4, 12], [3, 5, 15], [4, 5, 20], [4, 6, 12], [5, 6, 30], [3, 8, 24]];
        const [a, b, m] = r.pick(pairs);
        const day = r.int(0, 6);
        const right = dayOption(day + m);
        const options = r.shuffle([0, 1, 2, 3].map(i => dayOption(day + m + i * 2)));
        return {
            view: 'riddle', pk: 'dy_meet', pv: {a, b, day: dayOption(day)},
            why: [{k: 'why_days_meet', v: {m, day: dayOption(day)}}],
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
    trap('fence', .92, r => {
        const n = r.int(6, 14), m = r.pick([2, 3, 4, 5]);
        return {v: {n, m}, right: n * m, near: [(n - 1) * m, (n + 1) * m, n + m]};
    });
    trap('frogs', 1.0, r => {
        const n = r.int(3, 7);
        return {v: {n}, right: Math.pow(2, n) - 1, near: [Math.pow(2, n), n * 2, n * n]};
    });
    trap('cards', 1.04, r => {
        const k = r.int(2, 6), n = 4;
        return {v: {k, n}, right: (k - 1) * n + 1, near: [k * n, k + n, (k - 1) * n]};
    });
    trap('candle', .42, r => {
        const n = r.int(6, 15), b = r.int(2, n - 2);
        return {v: {n, b}, right: b, near: [n - b, n, 0]};
    });
    trap('holes', .54, r => {
        const n = r.int(2, 7);
        return {v: {n}, right: 4 + 2 * n, near: [n, 2 * n, 4 + n]};
    });
    trap('bus', .66, r => {
        const a = r.int(8, 20), b = r.int(2, 5), c = r.int(2, 7), d = r.int(1, 4);
        const p = a - b + c - d;
        return {v: {a, b, c, d}, right: p + 1, near: [p, p - 1, a + c + 1]};
    });
    trap('folds', 1.15, r => {
        const n = r.int(4, 9);
        return {v: {n}, right: 1 << n, near: [n * 2, n * n, (1 << (n - 1))]};
    });
    trap('matches', .96, r => {
        const n = r.int(12, 64);
        return {v: {n}, right: n - 1, near: [n, (n / 2) | 0, n + 1]};
    });
    trap('weigh', 1.08, r => {
        const [n, w] = r.pick([[7, 2], [8, 2], [9, 2], [12, 3], [20, 3], [25, 3], [27, 3], [40, 4], [64, 4], [80, 4], [81, 4]]);
        return {v: {n}, right: w, near: [w + 1, w - 1, Math.max(4, (n / 3) | 0)]};
    });

    // ---------- crazy scales (scales) ----------
    const SCALE_ITEMS = ['🍉', '🍍', '🎃', '🥥', '🧀', '🍎', '🍐', '🍋', '🍓', '🍒', '🪨', '🧱', '💎', '🔮', '👑', '🏺', '🪙'];
    const repeatIcon = (icon, n) => n <= 4 ? Array(n).fill(icon).join(' ') : `${n} × ${icon}`;

    pattern('scales-2', 'scales', .18, r => {
        const [x, y, z] = r.shuffle(SCALE_ITEMS);
        const a = r.int(2, 4), b = r.int(2, 4), k = r.int(1, 2);
        const right = k * a * b;
        const q = {
            view: 'lines', pk: 'q_scales_eq', pv: {z},
            lines: [`${x}  ⚖️  ${repeatIcon(y, a)}`, `${y}  ⚖️  ${repeatIcon(z, b)}`],
            ask: `${repeatIcon(x, k)}  ⚖️  ? ${z}`,
            why: [{k: 'why_scales_eq', v: {x: `${right} ${z}`}}],
        };
        return numberOptions(r, q, right, [k * (a + b), a * b + k, right + a, right - b]);
    });

    pattern('scales-3', 'scales', .56, r => {
        const [x, y, z, w] = r.shuffle(SCALE_ITEMS);
        const a = r.int(2, 3), b = r.int(2, 3), c = r.int(2, 4);
        const right = a * b * c;
        const q = {
            view: 'lines', pk: 'q_scales_eq', pv: {z: w},
            lines: [`${x}  ⚖️  ${repeatIcon(y, a)}`, `${y}  ⚖️  ${repeatIcon(z, b)}`, `${z}  ⚖️  ${repeatIcon(w, c)}`],
            ask: `${x}  ⚖️  ? ${w}`,
            why: [{k: 'why_scales_eq', v: {x: `${right} ${w}`}}],
        };
        return numberOptions(r, q, right, [a + b + c, a * b + c, right - c, right + b]);
    });

    pattern('scales-mix', 'scales', .92, r => {
        const [x, y, z] = r.shuffle(SCALE_ITEMS);
        const a = r.int(1, 3), xv = r.int(3, 7), yv = r.int(2, 6);
        const right = xv + yv;
        const q = {
            view: 'lines', pk: 'q_scales_eq', pv: {z},
            lines: [`${x} + ${repeatIcon(z, a)}  ⚖️  ${repeatIcon(z, xv + a)}`, `${y}  ⚖️  ${repeatIcon(z, yv)}`],
            ask: `${x} + ${y}  ⚖️  ? ${z}`,
            why: [{k: 'why_scales_eq', v: {x: `${right} ${z}`}}],
        };
        return numberOptions(r, q, right, [xv + a + yv, xv + yv - a, xv * yv]);
    });

    pattern('scales-cross', 'scales', 1.36, r => {
        const [x, y, z, w] = r.shuffle(SCALE_ITEMS);
        const a = r.int(2, 6), b = r.int(2, 5);
        const right = 2 * a + b;
        const q = {
            view: 'lines', pk: 'q_scales_eq', pv: {z: w},
            lines: [
                `${x} + ${y}  ⚖️  ${a + b} × ${w}`,
                `${x} + ${y} + ${y}  ⚖️  ${a + 2 * b} × ${w}`,
                `${z}  ⚖️  ${x} + ${x}`,
            ],
            ask: `${z} + ${y}  ⚖️  ? ${w}`,
            why: [{k: 'why_scales_eq', v: {x: `${right} ${w}`}}],
        };
        return numberOptions(r, q, right, [a + 2 * b, 2 * (a + b), a + b + 2]);
    });

    pattern('scales-heavy-4', 'scales', .42, r => {
        const items = r.shuffle(SCALE_ITEMS).slice(0, 4);
        const askHeavy = r.next() < .5;
        const right = askHeavy ? items[0] : items[3];
        const lines = r.shuffle([
            `${items[0]} + ${items[2]}  >  ${items[1]} + ${items[2]}`,
            `${items[1]}  >  ${items[2]}`,
            `${items[2]}  >  ${items[3]}`,
        ]);
        const q = {
            view: 'lines', pk: askHeavy ? 'q_scales_heavy' : 'q_scales_light',
            lines, ask: askHeavy ? '? = max' : '? = min',
            why: [{k: 'why_scales_ord', v: {x: items.join(' > ')}}],
        };
        return withOptions(r, q, right, items.filter(i => i !== right));
    });

    pattern('scales-light-5', 'scales', 1.12, r => {
        const items = r.shuffle(SCALE_ITEMS).slice(0, 5);
        const askHeavy = r.next() < .5;
        const right = askHeavy ? items[0] : items[4];
        // items[0] > items[1] > items[2] > items[3] > items[4]
        // Note: items[1] + items[3] ⚖️ items[2] + items[?]: instead use a deduction where items[1] + items[4] ⚖️ items[2] + items[3] and items[2] > items[3] => items[1] > items[2] and items[3] > items[4]!
        const lines = r.shuffle([
            `${items[0]}  >  ${items[1]}`,
            `${items[1]} + ${items[4]}  ⚖️  ${items[2]} + ${items[3]}`,
            `${items[1]}  >  ${items[2]}`,
            `${items[2]}  >  ${items[3]}`,
        ]);
        const q = {
            view: 'lines', pk: askHeavy ? 'q_scales_heavy' : 'q_scales_light',
            lines, ask: askHeavy ? '? = max' : '? = min',
            why: [{k: 'why_scales_ord', v: {x: items.join(' > ')}}],
        };
        return withOptions(r, q, right, others(r, items, right));
    });

    function makeFakeGem(r, n, d) {
        const gems = ['🟥', '🟦', '🟩', '🟨', '🟪'].slice(0, n);
        const heavy = r.next() < .5;
        const fakeIdx = r.int(0, n - 1);
        const weight = (idx, f) => idx === f ? (heavy ? 2 : 0) : 1;
        const panSign = (left, right, f) => {
            const lw = left.reduce((s, i) => s + weight(i, f), 0);
            const rw = right.reduce((s, i) => s + weight(i, f), 0);
            return lw === rw ? '⚖️' : (lw > rw ? '>' : '<');
        };
        for (; ;) {
            const weighings = [];
            for (let t = 0; t < 2; t++) {
                const k = r.int(1, Math.floor(n / 2));
                const pick = r.shuffle(gems.map((_, i) => i));
                weighings.push([pick.slice(0, k), pick.slice(k, 2 * k)]);
            }
            const obs = weighings.map(([L, R]) => panSign(L, R, fakeIdx));
            const matches = gems.map((_, f) => f).filter(f => weighings.every(([L, R], i) => panSign(L, R, f) === obs[i]));
            if (matches.length !== 1) continue;
            const right = gems[fakeIdx];
            const kind = {k: heavy ? 'sc_heavy' : 'sc_light'};
            const lines = weighings.map(([L, R], i) => `${L.map(j => gems[j]).join(' + ')}  ${obs[i]}  ${R.map(j => gems[j]).join(' + ')}`);
            const q = {
                view: 'lines', pk: 'q_scales_fake', pv: {kind},
                lines, ask: '? = 💎',
                why: [{k: 'why_scales_fake', v: {x: right, kind}}],
            };
            return withOptions(r, q, right, others(r, gems, right));
        }
    }

    pattern('scales-fake-4', 'scales', 1.58, r => makeFakeGem(r, 4, 1.58));
    pattern('scales-fake-5', 'scales', 1.86, r => makeFakeGem(r, 5, 1.86));

    // ---------- shell game / cups (cups) ----------
    function applyCupMove(row, m) {
        const out = row.slice();
        if (m.kind === 'swap') {
            [out[m.a], out[m.b]] = [out[m.b], out[m.a]];
        } else if (m.kind === 'rev') {
            out.reverse();
        } else if (m.kind === 'left') {
            out.push(out.shift());
        }
        return out;
    }

    const cupMoveText = m => m.kind === 'swap' ? `${m.a + 1} ↔ ${m.b + 1}` : (m.kind === 'rev' ? '⇄' : '⏪');

    function makeCupsForward(r, n, steps, allowSpecial) {
        const items = r.shuffle(r.pick(SETS)).slice(0, n);
        const moves = [];
        for (let i = 0; i < steps; i++) {
            if (allowSpecial && i === Math.floor(steps / 2) && r.next() < .65) {
                moves.push({kind: r.pick(['rev', 'left'])});
            } else {
                const a = r.int(0, n - 1), b = (a + 1 + r.int(0, n - 2)) % n;
                moves.push({kind: 'swap', a: Math.min(a, b), b: Math.max(a, b)});
            }
        }
        const end = moves.reduce(applyCupMove, items);
        if (end.join('') === items.join('')) return makeCupsForward(r, n, steps, allowSpecial);
        const mode = allowSpecial ? 'row' : (r.next() < .5 ? 'pos' : 'where');
        if (mode === 'pos') {
            const p = r.int(0, n - 1);
            const right = end[p];
            const q = {
                view: 'lines', pk: 'q_cups_pos', pv: {p: p + 1},
                lines: [items.join('   '), moves.map(cupMoveText).join('  ·  ')],
                ask: `#${p + 1} = ?`,
                why: [{k: 'why_cups', v: {x: end.join(' ')}}],
            };
            return withOptions(r, q, right, others(r, items, right));
        }
        if (mode === 'where') {
            const target = r.pick(items);
            const right = String(end.indexOf(target) + 1);
            const q = {
                view: 'lines', pk: 'q_cups_where', pv: {x: target},
                lines: [items.join('   '), moves.map(cupMoveText).join('  ·  ')],
                ask: `${target} → #?`,
                why: [{k: 'why_cups', v: {x: end.join(' ')}}],
            };
            return withOptions(r, q, right, others(r, Array.from({length: n}, (_, i) => String(i + 1)), right));
        }
        const right = end.join(' ');
        const wrong = new Set();
        while (wrong.size < 3) {
            const w = r.shuffle(end).join(' ');
            if (w !== right) wrong.add(w);
        }
        const q = {
            view: 'lines', pk: 'q_cups_row',
            lines: [items.join('   '), moves.map(cupMoveText).join('  ·  ')],
            ask: '? ? ?',
            why: [{k: 'why_cups', v: {x: right}}],
        };
        return withOptions(r, q, right, [...wrong]);
    }

    function makeCupsBack(r, n, steps) {
        const start = r.shuffle(r.pick(SETS)).slice(0, n);
        const moves = Array.from({length: steps}, () => {
            const a = r.int(0, n - 1), b = (a + 1 + r.int(0, n - 2)) % n;
            return {kind: 'swap', a: Math.min(a, b), b: Math.max(a, b)};
        });
        const end = moves.reduce(applyCupMove, start);
        if (end.join('') === start.join('')) return makeCupsBack(r, n, steps);
        const right = start.join(' ');
        const wrong = new Set([end.join(' ')]);
        while (wrong.size < 3) {
            const w = r.shuffle(start).join(' ');
            if (w !== right) wrong.add(w);
        }
        const q = {
            view: 'lines', pk: 'q_cups_back',
            lines: ['?   '.repeat(n).trim(), moves.map(cupMoveText).join('  ·  '), end.join('   ')],
            ask: '? → ' + end.join(' '),
            why: [{k: 'why_cups', v: {x: right}}],
        };
        return withOptions(r, q, right, [...wrong]);
    }

    function makeCupsMissing(r, n, steps) {
        for (; ;) {
            const start = r.shuffle(r.pick(SETS)).slice(0, n);
            const allPairs = [];
            for (let a = 0; a < n; a++) for (let b = a + 1; b < n; b++) allPairs.push({kind: 'swap', a, b});
            const moves = Array.from({length: steps}, () => r.pick(allPairs));
            const missAt = Math.floor(steps / 2);
            const end = moves.reduce(applyCupMove, start);
            const valid = allPairs.filter(cand => {
                const tryMoves = moves.map((m, i) => i === missAt ? cand : m);
                return tryMoves.reduce(applyCupMove, start).join('') === end.join('');
            });
            if (valid.length !== 1) continue;
            const right = cupMoveText(valid[0]);
            const shownMoves = moves.map((m, i) => i === missAt ? '? ↔ ?' : cupMoveText(m)).join('  ·  ');
            const q = {
                view: 'lines', pk: 'q_cups_miss',
                lines: [start.join('   '), shownMoves, end.join('   ')],
                ask: '? ↔ ?',
                why: [{k: 'why_cups', v: {x: right}}],
            };
            return withOptions(r, q, right, others(r, allPairs.map(cupMoveText), right));
        }
    }

    pattern('cups-3-easy', 'cups', .16, r => makeCupsForward(r, 3, 2, false));
    pattern('cups-4-easy', 'cups', .38, r => makeCupsForward(r, 4, 3, false));
    pattern('cups-4-med', 'cups', .68, r => makeCupsForward(r, 4, 4, true));
    pattern('cups-5-hard', 'cups', .96, r => makeCupsForward(r, 5, 5, false));
    pattern('cups-back-4', 'cups', 1.18, r => makeCupsBack(r, 4, 4));
    pattern('cups-missing-4', 'cups', 1.44, r => makeCupsMissing(r, 4, 3));
    pattern('cups-5-expert', 'cups', 1.62, r => makeCupsForward(r, 5, 6, true));
    pattern('cups-missing-5', 'cups', 1.84, r => makeCupsMissing(r, 5, 5));

    // ---------- crack the lock / mastermind (locks) ----------
    function scoreLock(secret, guess) {
        let exact = 0, misplaced = 0;
        for (let i = 0; i < secret.length; i++) {
            if (secret[i] === guess[i]) exact++;
            else if (secret.includes(guess[i])) misplaced++;
        }
        return [exact, misplaced];
    }

    function allCodes(pool, len) {
        if (len === 1) return pool.map(x => [x]);
        const out = [];
        pool.forEach((x, i) => {
            const rest = pool.filter((_, j) => j !== i);
            allCodes(rest, len - 1).forEach(sub => out.push([x, ...sub]));
        });
        return out;
    }

    function makeLock(r, len, poolSize, digits, maxClues) {
        const pool = digits
            ? r.shuffle(['1', '2', '3', '4', '5', '6', '7', '8', '9']).slice(0, poolSize)
            : r.shuffle(r.pick(SETS)).slice(0, poolSize);
        const candidates = allCodes(pool, len);
        for (; ;) {
            const secret = r.pick(candidates);
            let left = candidates;
            let clues = [];
            const order = r.shuffle(candidates.filter(c => c !== secret));
            for (const g of order) {
                if (left.length === 1) break;
                const [ex, mis] = scoreLock(secret, g);
                if (ex === len) continue;
                const narrowed = left.filter(c => {
                    const [e2, m2] = scoreLock(c, g);
                    return e2 === ex && m2 === mis;
                });
                if (narrowed.length < left.length) {
                    left = narrowed;
                    clues.push({g, ex, mis});
                }
            }
            if (left.length !== 1) continue;
            clues.slice().forEach(cl => {
                const rest = clues.filter(o => o !== cl);
                const survivors = candidates.filter(c => rest.every(o => {
                    const [e2, m2] = scoreLock(c, o.g);
                    return e2 === o.ex && m2 === o.mis;
                }));
                if (survivors.length === 1) clues = rest;
            });
            if (clues.length > maxClues || clues.length < 2) continue;
            const sep = digits ? '' : ' ';
            const fmt = c => c.join(sep);
            const right = fmt(secret);
            const near = candidates
                .filter(c => c !== secret)
                .sort((a, b) => {
                    const sa = clues.filter(o => {
                        const [e2, m2] = scoreLock(a, o.g);
                        return e2 === o.ex && m2 === o.mis;
                    }).length;
                    const sb = clues.filter(o => {
                        const [e2, m2] = scoreLock(b, o.g);
                        return e2 === o.ex && m2 === o.mis;
                    }).length;
                    return sb - sa;
                })
                .slice(0, 8)
                .map(fmt);
            const q = {
                view: 'lines', pk: 'q_locks',
                lines: clues.map(o => `${fmt(o.g)}   →   ${o.ex} 🎯   ${o.mis} 🔄`),
                ask: '🔒 = ?',
                why: [{k: 'why_locks', v: {x: right}}],
            };
            return withOptions(r, q, right, others(r, near, right));
        }
    }

    pattern('locks-pic-3', 'locks', .34, r => makeLock(r, 3, 4, false, 4));
    pattern('locks-dig-3-easy', 'locks', .58, r => makeLock(r, 3, 4, true, 4));
    pattern('locks-pic-3-med', 'locks', .86, r => makeLock(r, 3, 5, false, 5));
    pattern('locks-dig-3-med', 'locks', 1.14, r => makeLock(r, 3, 5, true, 5));
    pattern('locks-pic-3-hard', 'locks', 1.38, r => makeLock(r, 3, 6, false, 5));
    pattern('locks-dig-3-hard', 'locks', 1.56, r => makeLock(r, 3, 6, true, 5));
    pattern('locks-pic-4', 'locks', 1.74, r => makeLock(r, 4, 5, false, 5));
    pattern('locks-dig-4', 'locks', 1.91, r => makeLock(r, 4, 5, true, 5));

    // ---------- detective case (suspects) ----------
    const HATS = ['🎩', '👑', '🧢', '🎓', '👒'];
    const GEAR = ['☂️', '🕶️', '🎒', '🧤', '🧣', '🔦'];
    const RIDES = ['🚲', '🛴', '🛹', '🛶', '🚀', '🚁'];

    function makeSuspectTraits(r, n, hard) {
        const people = names(r, n);
        const hats = r.shuffle(HATS).slice(0, 3);
        const gear = r.shuffle(GEAR).slice(0, 3);
        const rides = r.shuffle(RIDES).slice(0, 3);
        for (; ;) {
            const profiles = people.map(() => [r.pick(hats), r.pick(gear), r.pick(rides)]);
            if (new Set(profiles.map(p => p.join())).size !== n) continue;
            const culprit = r.int(0, n - 1);
            const allItems = [...new Set(profiles.flat())];
            const possibleClues = [];
            allItems.forEach(x => {
                if (!hard && profiles.filter(p => p.includes(x)).length >= 2) {
                    possibleClues.push({k: 'su_has', v: {x}, test: p => p.includes(x)});
                }
                possibleClues.push({k: 'su_not', v: {x}, test: p => !p.includes(x)});
                allItems.forEach(y => {
                    if (x >= y) return;
                    possibleClues.push({k: 'su_imp', v: {x, y}, test: p => !p.includes(x) || p.includes(y)});
                    possibleClues.push({k: 'su_xor', v: {x, y}, test: p => p.includes(x) !== p.includes(y)});
                    possibleClues.push({k: 'su_or', v: {x, y}, test: p => p.includes(x) || p.includes(y)});
                });
            });
            let left = people.map((_, i) => i);
            let chosen = [];
            for (const c of r.shuffle(possibleClues.filter(cl => cl.test(profiles[culprit])))) {
                if (left.length === 1) break;
                const narrowed = left.filter(i => c.test(profiles[i]));
                if (narrowed.length < left.length && narrowed.length >= 1) {
                    left = narrowed;
                    chosen.push(c);
                }
            }
            if (left.length !== 1) continue;
            chosen.slice().forEach(c => {
                const rest = chosen.filter(o => o !== c);
                if (rest.length >= 2 && people.filter((_, i) => rest.every(o => o.test(profiles[i]))).length === 1) chosen = rest;
            });
            if (chosen.length < 2 || chosen.length > 4) continue;
            const right = people[culprit];
            const roster = people.map((p, i) => ({lit: `${p}:  ${profiles[i].join(' · ')}`}));
            const q = {
                view: 'facts', pk: 'q_suspects_traits',
                facts: [...roster.map(x => ({k: 'bx_line', v: {c: x.lit.split(':')[0], s: x.lit.split(':  ')[1]}})), ...chosen.map(({k, v}) => ({k, v}))],
                ask: {k: 'su_ask'},
                why: [{k: 'why_suspects', v: {x: `${right} (${profiles[culprit].join(' · ')})`}}],
            };
            return withOptions(r, q, right, others(r, people, right));
        }
    }

    function makeSuspectAlibi(r, n, truths) {
        const people = names(r, n);
        const kinds = ['me', 'guilty', 'not', 'either'];
        for (; ;) {
            const statements = people.map((_, s) => {
                const kind = r.pick(kinds);
                const rest = r.shuffle(people.map((_, i) => i).filter(i => i !== s));
                return {kind, s, b: rest[0], c: rest[1]};
            });
            const holds = (st, guilty) => {
                if (st.kind === 'me') return guilty !== st.s;
                if (st.kind === 'guilty') return guilty === st.b;
                if (st.kind === 'not') return guilty !== st.b;
                return guilty === st.b || guilty === st.c;
            };
            const valid = people.map((_, g) => g).filter(g => statements.filter(st => holds(st, g)).length === truths);
            if (valid.length !== 1) continue;
            const right = people[valid[0]];
            const facts = statements.map(st => ({
                k: 'su_says_' + st.kind,
                v: {a: people[st.s], b: people[st.b], c: st.c !== undefined ? people[st.c] : ''},
            }));
            const q = {
                view: 'facts', pk: 'q_suspects_alibi', pv: {k: truths},
                facts, ask: {k: 'su_ask'},
                why: [{k: 'why_suspects', v: {x: right}}],
            };
            return withOptions(r, q, right, others(r, people, right));
        }
    }

    pattern('suspects-traits-3', 'suspects', .28, r => makeSuspectTraits(r, 3, false));
    pattern('suspects-traits-4', 'suspects', .54, r => makeSuspectTraits(r, 4, false));
    pattern('suspects-alibi-3', 'suspects', .82, r => makeSuspectAlibi(r, 3, 1));
    pattern('suspects-traits-4-hard', 'suspects', 1.06, r => makeSuspectTraits(r, 4, true));
    pattern('suspects-alibi-4-one', 'suspects', 1.28, r => makeSuspectAlibi(r, 4, 1));
    pattern('suspects-alibi-4-two', 'suspects', 1.48, r => makeSuspectAlibi(r, 4, 2));
    pattern('suspects-traits-5', 'suspects', 1.68, r => makeSuspectTraits(r, 5, true));
    pattern('suspects-alibi-5', 'suspects', 1.89, r => makeSuspectAlibi(r, 5, 2));

    // ---------- crazy alchemy (alchemy) ----------
    const HERBS = ['🍄', '🍀', '🌻', '🌙', '⭐', '💎', '🪶', '🐚', '🦴', '🌶️', '🧄', '🍯'];

    pattern('alchemy-recipe-easy', 'alchemy', .24, r => {
        const [a, b] = r.shuffle(HERBS);
        const needA = r.int(2, 4), needB = r.int(2, 4), batches = r.int(2, 6);
        const haveB = batches * needA * needB;
        const q = {
            view: 'lines', pk: 'q_alchemy_recipe',
            lines: [`${needB} ${b}  →  1 ${a}`, `${needA} ${a}  →  1 🧪`, `🎒:  ${haveB} ${b}`],
            ask: '🧪 = ?',
            why: [{k: 'why_alchemy_recipe', v: {x: batches}}],
        };
        return numberOptions(r, q, batches, [batches * needA, batches + 1, batches - 1, Math.floor(haveB / needB)]);
    });

    pattern('alchemy-recipe-med', 'alchemy', .62, r => {
        const [a, b] = r.shuffle(HERBS);
        const na = r.int(2, 4), nb = r.int(2, 5);
        const capA = r.int(3, 8), capB = capA + r.int(1, 4);
        const swap = r.next() < .5;
        const [ha, hb] = swap ? [capB * na + r.int(0, na - 1), capA * nb + r.int(0, nb - 1)] : [capA * na + r.int(0, na - 1), capB * nb + r.int(0, nb - 1)];
        const right = capA;
        const q = {
            view: 'lines', pk: 'q_alchemy_recipe',
            lines: [`${na} ${a} + ${nb} ${b}  →  1 🧪`, `🎒:  ${ha} ${a}  ·  ${hb} ${b}`],
            ask: '🧪 = ?',
            why: [{k: 'why_alchemy_recipe', v: {x: right}}],
        };
        return numberOptions(r, q, right, [capB, capA + capB, Math.floor((capA + capB) / 2), right + 1]);
    });

    pattern('alchemy-recipe-hard', 'alchemy', 1.04, r => {
        const [a, b, c] = r.shuffle(HERBS);
        const cPerA = r.int(2, 3), aPerP = r.int(2, 3), bPerP = r.int(2, 4);
        const fromC = r.int(3, 6), directA = r.int(2, 5);
        const totalA = fromC + directA;
        const maxByA = Math.floor(totalA / aPerP);
        const maxByB = Math.max(1, maxByA + r.pick([-1, 1, 2]));
        const right = Math.min(maxByA, maxByB);
        const q = {
            view: 'lines', pk: 'q_alchemy_recipe',
            lines: [
                `${cPerA} ${c}  →  1 ${a}`,
                `${aPerP} ${a} + ${bPerP} ${b}  →  1 🧪`,
                `🎒:  ${directA} ${a}  ·  ${maxByB * bPerP} ${b}  ·  ${fromC * cPerA} ${c}`,
            ],
            ask: '🧪 = ?',
            why: [{k: 'why_alchemy_recipe', v: {x: right}}],
        };
        return numberOptions(r, q, right, [Math.max(maxByA, maxByB), totalA, right + 1, right + 2]);
    });

    function makeRecycle(r, kRange, nRange) {
        const k = r.int(kRange[0], kRange[1]);
        const n = r.int(nRange[0], nRange[1]);
        let total = n, empty = n;
        while (empty >= k) {
            const extra = Math.floor(empty / k);
            total += extra;
            empty = (empty % k) + extra;
        }
        const firstStepOnly = n + Math.floor(n / k);
        const q = {
            view: 'riddle', pk: 'q_alchemy_recycle', pv: {n, k},
            why: [{k: 'why_alchemy_recycle', v: {x: total}}],
        };
        return numberOptions(r, q, total, [firstStepOnly, total - 1, total + 1, n + k]);
    }

    pattern('alchemy-recycle-easy', 'alchemy', .88, r => makeRecycle(r, [3, 4], [9, 16]));
    pattern('alchemy-recycle-hard', 'alchemy', 1.38, r => makeRecycle(r, [2, 3], [14, 27]));

    function makeCauldronBack(r, steps, endZero) {
        let k, start, end;
        if (endZero) {
            const m = r.int(1, 5);
            k = m * (1 << steps);
            start = m * ((1 << steps) - 1);
            end = 0;
        } else {
            k = r.int(2, 8);
            start = k + r.int(1, 5);
            end = start;
            for (let i = 0; i < steps; i++) end = end * 2 - k;
        }
        const q = {
            view: 'riddle', pk: 'q_alchemy_back', pv: {k, s: steps, e: end},
            why: [{k: 'why_alchemy_back', v: {k, s: steps, x: start}}],
        };
        return numberOptions(r, q, start, [start + 1, start - 1, start + 2, Math.max(1, Math.floor((end + k * steps) / 2))]);
    }

    pattern('alchemy-back-2', 'alchemy', .76, r => makeCauldronBack(r, 2, false));
    pattern('alchemy-back-3-zero', 'alchemy', 1.24, r => makeCauldronBack(r, 3, true));
    pattern('alchemy-back-4', 'alchemy', 1.78, r => makeCauldronBack(r, 4, false));

    // ---------- around the table & on the bench (seats) ----------
    function makeSeatsRound(r, n) {
        const people = names(r, n);
        const ids = people.map((_, i) => i);
        // Seat 0 is fixed as person 0 in the candidate search or by a clue
        const perms = orderings(ids.slice(1)).map(rest => [0, ...rest]);
        const truth = r.pick(perms);
        const seatOf = (arr, p) => arr.indexOf(p);
        const rightOf = (arr, a, b) => (seatOf(arr, b) + 1) % n === seatOf(arr, a);
        const leftOf = (arr, a, b) => (seatOf(arr, b) + n - 1) % n === seatOf(arr, a);
        const nextTo = (arr, a, b) => rightOf(arr, a, b) || leftOf(arr, a, b);
        const oppOf = (arr, a, b) => n % 2 === 0 && (seatOf(arr, b) + n / 2) % n === seatOf(arr, a);
        const clues = [];
        ids.forEach(a => ids.forEach(b => {
            if (a === b) return;
            clues.push({k: 'st_right', v: {a: people[a], b: people[b]}, test: c => rightOf(c, a, b)});
            clues.push({k: 'st_left', v: {a: people[a], b: people[b]}, test: c => leftOf(c, a, b)});
            if (a < b) {
                clues.push({k: 'st_next', v: {a: people[a], b: people[b]}, test: c => nextTo(c, a, b)});
                clues.push({k: 'st_not_next', v: {a: people[a], b: people[b]}, test: c => !nextTo(c, a, b)});
                if (n % 2 === 0) clues.push({k: 'st_opp', v: {a: people[a], b: people[b]}, test: c => oppOf(c, a, b)});
            }
        }));
        let left = perms, facts = [{k: 'st_at', v: {a: people[0], n: 1}, test: c => c[0] === 0}];
        for (const clue of r.shuffle(clues.filter(c => c.test(truth)))) {
            if (left.length === 1) break;
            const narrowed = left.filter(clue.test);
            if (narrowed.length < left.length) {
                left = narrowed;
                facts.push(clue);
            }
        }
        if (left.length !== 1) return makeSeatsRound(r, n);
        facts.slice(1).forEach(f => {
            const rest = facts.filter(o => o !== f);
            if (perms.filter(c => rest.every(o => o.test(c))).length === 1) facts = rest;
        });
        const askSeat = r.int(1, n - 1);
        const askPerson = truth[askSeat];
        const useOpp = n % 2 === 0 && r.next() < .6;
        const targetSeat = askSeat + 1;
        const ask = useOpp
            ? {k: 'st_ask_opp', v: {a: people[truth[(askSeat + n / 2) % n]], targetSeat}}
            : {k: 'st_ask_seat', v: {n: targetSeat, targetSeat}};
        const right = people[askPerson];
        const q = {
            view: 'facts', pk: 'q_seats_round', pv: {n, targetSeat},
            facts: facts.map(({k, v}) => ({k, v})),
            ask,
            why: [{k: 'why_seats', v: {x: truth.map((p, i) => `${i + 1}:${people[p]}`).join(' · ')}}],
        };
        return withOptions(r, q, right, others(r, people, right));
    }

    function makeSeatsBench(r, n) {
        const people = names(r, n);
        const ids = people.map((_, i) => i);
        const perms = orderings(ids);
        const truth = r.pick(perms);
        const at = (c, p) => c.indexOf(p);
        const clues = [];
        ids.forEach(a => {
            clues.push({k: 'st_end', v: {a: people[a]}, test: c => at(c, a) === 0 || at(c, a) === n - 1});
            ids.forEach((b) => {
                if (a === b) return;
                clues.push({k: 'st_left_of', v: {a: people[a], b: people[b]}, test: c => at(c, a) < at(c, b)});
                clues.push({k: 'st_right', v: {a: people[a], b: people[b]}, test: c => at(c, a) === at(c, b) + 1});
                if (a < b) {
                    clues.push({k: 'st_next', v: {a: people[a], b: people[b]}, test: c => Math.abs(at(c, a) - at(c, b)) === 1});
                    clues.push({k: 'st_not_next', v: {a: people[a], b: people[b]}, test: c => Math.abs(at(c, a) - at(c, b)) > 1});
                }
                ids.forEach(d => {
                    if (b < d && a !== d) {
                        clues.push({k: 'st_between', v: {a: people[a], b: people[b], c: people[d]}, test: c => Math.abs(at(c, a) - at(c, b)) === 1 && Math.abs(at(c, a) - at(c, d)) === 1});
                    }
                });
            });
        });
        let left = perms, facts = [];
        for (const clue of r.shuffle(clues.filter(c => c.test(truth)))) {
            if (left.length === 1) break;
            const narrowed = left.filter(clue.test);
            if (narrowed.length < left.length) {
                left = narrowed;
                facts.push(clue);
            }
        }
        if (left.length !== 1) return makeSeatsBench(r, n);
        facts.slice().forEach(f => {
            const rest = facts.filter(o => o !== f);
            if (perms.filter(c => rest.every(o => o.test(c))).length === 1) facts = rest;
        });
        const pos = r.int(0, n - 1);
        const right = people[truth[pos]];
        const targetSeat = pos + 1;
        const q = {
            view: 'facts', pk: 'q_seats_bench', pv: {n, targetSeat},
            facts: facts.map(({k, v}) => ({k, v})),
            ask: {k: 'st_ask_seat', v: {n: targetSeat, targetSeat}},
            why: [{k: 'why_seats', v: {x: truth.map(p => people[p]).join(' · ')}}],
        };
        return withOptions(r, q, right, others(r, people, right));
    }

    pattern('seats-round-4', 'seats', .32, r => makeSeatsRound(r, 4));
    pattern('seats-bench-4', 'seats', .64, r => makeSeatsBench(r, 4));
    pattern('seats-round-5', 'seats', .94, r => makeSeatsRound(r, 5));
    pattern('seats-bench-5', 'seats', 1.22, r => makeSeatsBench(r, 5));
    pattern('seats-round-6', 'seats', 1.52, r => makeSeatsRound(r, 6));
    pattern('seats-bench-6', 'seats', 1.84, r => makeSeatsBench(r, 6));

    // ---------- mirrors & flips (mirror) ----------
    const MIRROR_OPS = [
        ['↔️', (g, n) => g.map(row => row.slice().reverse())],
        ['↕️', (g, n) => g.slice().reverse().map(row => row.slice())],
        ['↻ 90°', (g, n) => Array.from({length: n}, (_, r) => Array.from({length: n}, (_, c) => g[n - 1 - c][r]))],
        ['↺ 90°', (g, n) => Array.from({length: n}, (_, r) => Array.from({length: n}, (_, c) => g[c][n - 1 - r]))],
        ['↻ 180°', (g, n) => g.slice().reverse().map(row => row.slice().reverse())],
    ];

    function makeMirrorMove(r, n, steps) {
        const pool = r.shuffle(SETS.flat().filter((x, i, a) => a.indexOf(x) === i)).slice(0, n * n);
        const grid = Array.from({length: n}, (_, y) => Array.from({length: n}, (_, x) => pool[y * n + x]));
        const ops = Array.from({length: steps}, () => r.pick(MIRROR_OPS));
        const end = ops.reduce((g, op) => op[1](g, n), grid);
        // Pick a non-center cell where end[ty][tx] !== grid[ty][tx]
        const cells = [];
        for (let y = 0; y < n; y++) for (let x = 0; x < n; x++) {
            if (end[y][x] !== grid[y][x]) cells.push([y, x]);
        }
        if (!cells.length) return makeMirrorMove(r, n, steps);
        const [ty, tx] = r.pick(cells);
        const target = grid[ty][tx];
        const right = end[ty][tx];
        const decoys = MIRROR_OPS.map(op => op[1](grid, n)[ty][tx]).concat([target, grid[tx][ty]]);
        const wrong = others(r, [...decoys, ...pool], right);
        const q = {
            view: 'grid', grid,
            lines: [ops.map(o => o[0]).join('  ·  ')],
            pk: 'q_mirror_move', pv: {target},
            why: [{k: 'why_mirror', v: {x: right}}],
        };
        return withOptions(r, q, right, wrong);
    }

    function makeMirrorSym(r, n, mode) {
        const icons = r.shuffle(r.pick(SETS)).slice(0, 5);
        const grid = Array.from({length: n}, () => Array(n).fill(''));
        for (let y = 0; y < n; y++) for (let x = 0; x < n; x++) {
            if (mode === 'both') {
                const sy = Math.min(y, n - 1 - y), sx = Math.min(x, n - 1 - x);
                grid[y][x] = grid[sy][sx] || r.pick(icons);
            } else if (mode === 'point') {
                const py = n - 1 - y, px = n - 1 - x;
                grid[y][x] = grid[py][px] || r.pick(icons);
            } else {
                grid[y][x] = grid[x][y] || r.pick(icons);
            }
        }
        // Ensure non-trivial board where different symmetries give different answers at the chosen cell
        const candidates = [];
        for (let y = 0; y < n; y++) for (let x = 0; x < n; x++) {
            if (mode === 'diag' && y === x) continue;
            if (mode === 'point' && y === n - 1 - y && x === n - 1 - x) continue;
            candidates.push([y, x]);
        }
        const [ay, ax] = r.pick(candidates);
        const right = grid[ay][ax];
        const mirrorCell = mode === 'both' ? [ay, n - 1 - ax] : (mode === 'point' ? [n - 1 - ay, n - 1 - ax] : [ax, ay]);
        if (mirrorCell[0] === ay && mirrorCell[1] === ax) return makeMirrorSym(r, n, mode);
        grid[ay][ax] = '?';
        const pk = mode === 'both' ? 'q_mirror_sym' : (mode === 'point' ? 'q_mirror_point' : 'q_mirror_diag');
        const q = {
            view: 'grid', grid, pk,
            why: [{k: 'why_mirror', v: {x: right}}],
        };
        return withOptions(r, q, right, others(r, icons, right));
    }

    pattern('mirror-sym-4', 'mirror', .22, r => makeMirrorSym(r, 4, 'both'));
    pattern('mirror-flip-3', 'mirror', .46, r => makeMirrorMove(r, 3, 1));
    pattern('mirror-point-4', 'mirror', .78, r => makeMirrorSym(r, 4, 'point'));
    pattern('mirror-combo-3', 'mirror', 1.08, r => makeMirrorMove(r, 3, 2));
    pattern('mirror-diag-4', 'mirror', 1.34, r => makeMirrorSym(r, 4, 'diag'));
    pattern('mirror-combo-4', 'mirror', 1.58, r => makeMirrorMove(r, 4, 2));
    pattern('mirror-triple-3', 'mirror', 1.76, r => makeMirrorMove(r, 3, 3));
    pattern('mirror-triple-4', 'mirror', 1.94, r => makeMirrorMove(r, 4, 3));

    // ---------- mini-sudoku X (grid) ----------
    function makeGridDiag(r) {
        const n = 4, br = 2, bc = 2;
        const symbols = gridSymbols(r, 4);
        function fitsDiag(g, at, v) {
            const row = Math.floor(at / n), col = at % n;
            for (let c = 0; c < n; c++) if (g[row * n + c] === v) return false;
            for (let ro = 0; ro < n; ro++) if (g[ro * n + col] === v) return false;
            const r0 = Math.floor(row / br) * br, c0 = Math.floor(col / bc) * bc;
            for (let dr = 0; dr < br; dr++) for (let dc = 0; dc < bc; dc++) if (g[(r0 + dr) * n + c0 + dc] === v) return false;
            if (row === col) {
                for (let i = 0; i < n; i++) if (g[i * n + i] === v) return false;
            }
            if (row + col === n - 1) {
                for (let i = 0; i < n; i++) if (g[i * n + (n - 1 - i)] === v) return false;
            }
            return true;
        }
        function solveDiag(g, limit) {
            const at = g.indexOf(-1);
            if (at < 0) return 1;
            let found = 0;
            for (let v = 0; v < n && found < limit; v++) {
                if (!fitsDiag(g, at, v)) continue;
                g[at] = v;
                found += solveDiag(g, limit - found);
                g[at] = -1;
            }
            return found;
        }
        function fillDiag(g) {
            const at = g.indexOf(-1);
            if (at < 0) return true;
            for (const v of r.shuffle([0, 1, 2, 3])) {
                if (!fitsDiag(g, at, v)) continue;
                g[at] = v;
                if (fillDiag(g)) return true;
                g[at] = -1;
            }
            return false;
        }
        const g = Array(16).fill(-1);
        fillDiag(g);
        const solved = g.slice();
        const holes = [];
        for (const at of r.shuffle(Array.from({length: 16}, (_, i) => i))) {
            if (holes.length === 8) break;
            const keep = g[at];
            g[at] = -1;
            if (solveDiag(g.slice(), 2) === 1) holes.push(at);
            else g[at] = keep;
        }
        const ask = holes[holes.length - 1];
        const right = symbols[solved[ask]];
        const grid = Array.from({length: n}, (_, row) => Array.from({length: n}, (_, col) => {
            const at = row * n + col;
            return at === ask ? '?' : (g[at] < 0 ? '' : symbols[g[at]]);
        }));
        const q = {
            view: 'grid', grid, boxes: [2, 2], isDiag: true,
            pk: 'q_grid_diag',
            why: [{k: 'why_grid_diag', v: {x: right}}],
        };
        return withOptions(r, q, right, others(r, symbols, right));
    }

    pattern('grid-diag-4', 'grid', 1.28, r => makeGridDiag(r));

    // ---------- gears & rotation (gears) ----------
    function makeGearsDir(r, n, branch) {
        const gearNames = ['A', 'B', 'C', 'D', 'E', 'F'];
        const links = [];
        if (!branch) {
            for (let i = 0; i < n - 1; i++) links.push([i, i + 1]);
        } else {
            links.push([0, 1], [0, 2]);
            for (let i = 2; i < n - 1; i++) links.push([i, i + 1]);
        }
        const startIdx = r.int(0, n - 1);
        const startCw = r.next() < .5;
        const dirs = Array(n).fill(null);
        dirs[startIdx] = startCw;
        let changed = true;
        while (changed) {
            changed = false;
            links.forEach(([a, b]) => {
                if (dirs[a] !== null && dirs[b] === null) { dirs[b] = !dirs[a]; changed = true; }
                if (dirs[b] !== null && dirs[a] === null) { dirs[a] = !dirs[b]; changed = true; }
            });
        }
        const candidateTargets = [];
        for (let i = 0; i < n; i++) if (i !== startIdx && dirs[i] !== null) candidateTargets.push(i);
        const targetIdx = r.pick(candidateTargets);
        const startName = gearNames[startIdx];
        const target = gearNames[targetIdx];
        const isCw = dirs[targetIdx];
        const right = isCw ? {k: 'ge_cw'} : {k: 'ge_ccw'};
        const wrong = [isCw ? {k: 'ge_ccw'} : {k: 'ge_cw'}, {k: 'ge_jammed'}];
        const startDirSym = startCw ? '↻' : '↺';
        const gearsData = {
            count: n,
            links,
            startDir: startCw ? 'cw' : 'ccw',
            startIdx,
            targetIdx,
            dirs: dirs.map(d => d ? 'cw' : 'ccw'),
        };
        const q = {
            view: 'gears', gearsData,
            lines: [`${startName} (${startDirSym}) ➔ ${target}`],
            pk: 'q_gears_dir', pv: {start: startName, dir: startDirSym, target},
            why: [{k: 'why_gears_dir', v: {target, x: isCw ? '↻' : '↺'}}],
        };
        return withOptions(r, q, right, wrong);
    }

    function makeGearsSpeed(r) {
        const ratios = [
            [12, 24, 2, 1],
            [10, 30, 3, 1],
            [12, 36, 3, 1],
            [15, 30, 4, 2],
            [20, 10, 1, 2],
            [30, 15, 2, 4],
            [16, 32, 4, 2],
            [24, 12, 2, 4],
            [18, 36, 2, 1],
            [25, 50, 4, 2],
        ];
        const [na, nb, ta, tb] = r.pick(ratios);
        const right = tb;
        const gearsData = {
            count: 2,
            links: [[0, 1]],
            teeth: [na, nb],
            turns: [ta, tb],
            startDir: 'cw',
            startIdx: 0,
            targetIdx: 1,
            dirs: ['cw', 'ccw'],
        };
        const q = {
            view: 'gears', gearsData,
            lines: [`A (${na}) · B (${nb}) · ${ta} ↻`],
            pk: 'q_gears_speed', pv: {na, nb, ta},
            why: [{k: 'why_gears_speed', v: {na, nb, ta, x: right}}],
        };
        return numberOptions(r, q, right, [ta, ta + tb, Math.max(1, Math.abs(ta - tb)), right * 2]);
    }

    function makeGearsJam(r, n) {
        const isJammed = (n % 2 !== 0);
        const right = isJammed ? {k: 'ge_jammed'} : {k: 'ge_free'};
        const wrong = [isJammed ? {k: 'ge_free'} : {k: 'ge_jammed'}, {k: 'unknown'}];
        const links = [];
        for (let i = 0; i < n; i++) links.push([i, (i + 1) % n]);
        const startName = ['A', 'B', 'C', 'D', 'E'][r.int(0, n - 1)];
        const gearsData = {
            count: n,
            links,
            isRing: true,
            isJammed,
            startDir: 'cw',
            dirs: Array(n).fill('cw'),
        };
        const q = {
            view: 'gears', gearsData,
            lines: [`Ring (${n}) · Start: ${startName}`],
            pk: 'q_gears_jam',
            why: [{k: 'why_gears_jam', v: {x: isJammed ? '🚫' : '✓'}}],
        };
        return withOptions(r, q, right, wrong);
    }

    pattern('gears-dir-3', 'gears', .18, r => makeGearsDir(r, 3, false));
    pattern('gears-dir-4', 'gears', .44, r => makeGearsDir(r, 4, false));
    pattern('gears-branch-4', 'gears', .72, r => makeGearsDir(r, 4, true));
    pattern('gears-speed-2', 'gears', .98, r => makeGearsSpeed(r));
    pattern('gears-branch-5', 'gears', 1.24, r => makeGearsDir(r, 5, true));
    pattern('gears-jam-odd', 'gears', 1.48, r => makeGearsJam(r, r.pick([3, 5])));
    pattern('gears-speed-hard', 'gears', 1.72, r => makeGearsSpeed(r));
    pattern('gears-branch-6', 'gears', 1.92, r => makeGearsDir(r, 6, true));

    // ---------- lasers & mirrors (lasers) ----------
    const GEMS = ['💎', '🔮', '🪙', '⭐'];

    function makeLasers(r, size, mirrorCount, withObstacles) {
        for (let attempt = 0; attempt < 50; attempt++) {
            const grid = Array.from({length: size}, () => Array(size).fill(''));
            const startX = 0, startY = r.int(0, size - 1);
            let dirX = 1, dirY = 0;
            const mirrorCells = [];
            for (let y = 0; y < size; y++) for (let x = 0; x < size; x++) {
                if (x === startX && y === startY) continue;
                mirrorCells.push([x, y]);
            }
            const chosenMirrors = r.shuffle(mirrorCells).slice(0, mirrorCount);
            chosenMirrors.forEach(([mx, my]) => {
                grid[my][mx] = r.next() < .5 ? '/' : String.fromCharCode(92);
            });
            if (withObstacles && size >= 4) {
                const rem = mirrorCells.filter(([x, y]) => !chosenMirrors.some(([mx, my]) => mx === x && my === y));
                if (rem.length) {
                    const [ox, oy] = r.pick(rem);
                    grid[oy][ox] = '🧱';
                }
            }
            let curX = startX, curY = startY;
            let curDx = dirX, curDy = dirY;
            const visited = new Set();
            let reflections = 0;
            let hitBorder = false;
            const path = [[curX, curY]];
            for (let step = 0; step < size * size * 2; step++) {
                curX += curDx;
                curY += curDy;
                path.push([curX, curY]);
                if (curX < 0 || curX >= size || curY < 0 || curY >= size) {
                    hitBorder = true;
                    break;
                }
                const cell = grid[curY][curX];
                if (cell === '🧱') break;
                if (cell === '/' || cell === String.fromCharCode(92)) {
                    reflections++;
                    if (cell === '/') {
                        const ndx = -curDy, ndy = -curDx;
                        curDx = ndx; curDy = ndy;
                    } else {
                        const ndx = curDy, ndy = curDx;
                        curDx = ndx; curDy = ndy;
                    }
                    const sk = `${curX},${curY},${curDx},${curDy}`;
                    if (visited.has(sk)) break;
                    visited.add(sk);
                }
            }
            if (!hitBorder || reflections < 2) continue;
            const [exitX, exitY] = path[path.length - 2];
            const targetGem = GEMS[0];
            grid[exitY][exitX] = targetGem;
            const borderCells = [];
            for (let y = 0; y < size; y++) for (let x = 0; x < size; x++) {
                if ((x === 0 || x === size - 1 || y === 0 || y === size - 1) && !grid[y][x] && (x !== startX || y !== startY)) {
                    borderCells.push([x, y]);
                }
            }
            if (borderCells.length < 3) continue;
            const decoys = r.shuffle(borderCells).slice(0, 3);
            decoys.forEach(([dx, dy], i) => {
                grid[dy][dx] = GEMS[i + 1];
            });
            grid[startY][startX] = '🔦';
            const lasersData = {
                size, grid,
                startX, startY,
                path,
                target: targetGem,
                reflections,
            };
            const q = {
                view: 'lasers', lasersData, grid,
                pk: 'q_lasers_hit',
                why: [{k: 'why_lasers_hit', v: {x: targetGem}}],
            };
            return withOptions(r, q, targetGem, GEMS.slice(1));
        }
        const grid = [['🔦', '/', '💎'], ['⭐', '', ''], ['🔮', '', '🪙']];
        const lasersData = {size: 3, grid, startX: 0, startY: 0, path: [[0, 0], [1, 0], [2, 0]], target: '💎', reflections: 1};
        const q = {view: 'lasers', lasersData, grid, pk: 'q_lasers_hit', why: [{k: 'why_lasers_hit', v: {x: '💎'}}]};
        return withOptions(r, q, '💎', ['⭐', '🔮', '🪙']);
    }

    pattern('lasers-3x3', 'lasers', .24, r => makeLasers(r, 3, 2, false));
    pattern('lasers-3x3-med', 'lasers', .54, r => makeLasers(r, 3, 2, false));
    pattern('lasers-4x4', 'lasers', .88, r => makeLasers(r, 4, 3, false));
    pattern('lasers-4x4-obs', 'lasers', 1.16, r => makeLasers(r, 4, 3, true));
    pattern('lasers-5x5', 'lasers', 1.46, r => makeLasers(r, 5, 4, false));
    pattern('lasers-5x5-obs', 'lasers', 1.74, r => makeLasers(r, 5, 5, true));
    pattern('lasers-5x5-max', 'lasers', 1.95, r => makeLasers(r, 5, 6, true));

    // ---------- 3D cube nets (cubes) ----------
    const CUBE_NETS = [
        {
            grid: [
                [-1, 0, -1, -1],
                [1,  2,  3,  4],
                [-1, 5, -1, -1],
            ],
            opp: {0: 5, 5: 0, 1: 3, 3: 1, 2: 4, 4: 2},
        },
        {
            grid: [
                [-1, 0, -1, -1],
                [1,  2,  3, -1],
                [-1, -1, 4,  5],
            ],
            opp: {0: 4, 4: 0, 1: 3, 3: 1, 2: 5, 5: 2},
        },
        {
            grid: [
                [0,  1, -1, -1],
                [-1, 2,  3, -1],
                [-1, -1, 4,  5],
            ],
            opp: {0: 3, 3: 0, 1: 4, 4: 1, 2: 5, 5: 2},
        },
    ];

    function makeCubesOpp(r, netIndex) {
        const netDef = CUBE_NETS[netIndex % CUBE_NETS.length];
        const icons = r.shuffle(r.pick(SETS)).slice(0, 6);
        const targetFace = r.int(0, 5);
        const oppFace = netDef.opp[targetFace];
        const targetIcon = icons[targetFace];
        const right = icons[oppFace];
        const netGrid = netDef.grid.map(row => row.map(idx => idx >= 0 ? icons[idx] : ''));
        const cubesData = {
            netGrid,
            targetIcon,
            rightIcon: right,
            opp: netDef.opp,
            icons,
        };
        const q = {
            view: 'cubes', cubesData, grid: netGrid,
            lines: [icons.join(' ')],
            pk: 'q_cubes_opp', pv: {target: targetIcon},
            why: [{k: 'why_cubes_opp', v: {target: targetIcon, x: right}}],
        };
        return withOptions(r, q, right, others(r, icons, right));
    }

    function makeCubesView(r, netIndex) {
        const netDef = CUBE_NETS[netIndex % CUBE_NETS.length];
        const icons = r.shuffle(r.pick(SETS)).slice(0, 6);
        const validTriplets = [];
        const invalidTriplets = [];
        for (let a = 0; a < 6; a++) {
            for (let b = a + 1; b < 6; b++) {
                for (let c = b + 1; c < 6; c++) {
                    if (netDef.opp[a] !== b && netDef.opp[a] !== c && netDef.opp[b] !== c) {
                        validTriplets.push([a, b, c]);
                    } else {
                        invalidTriplets.push([a, b, c]);
                    }
                }
            }
        }
        const rightTriplet = r.pick(validTriplets);
        const wrongTriplets = r.shuffle(invalidTriplets).slice(0, 3);
        const rightText = rightTriplet.map(i => icons[i]).join(' ');
        const wrongTexts = wrongTriplets.map(tri => tri.map(i => icons[i]).join(' '));
        const netGrid = netDef.grid.map(row => row.map(idx => idx >= 0 ? icons[idx] : ''));
        const cubesData = {
            netGrid,
            icons,
            isViewMode: true,
        };
        const q = {
            view: 'cubes', cubesData, grid: netGrid,
            lines: [icons.join(' ')],
            pk: 'q_cubes_view',
            why: [{k: 'why_cubes_view', v: {x: rightText}}],
        };
        return withOptions(r, q, rightText, wrongTexts);
    }

    pattern('cubes-opp-cross', 'cubes', .26, r => makeCubesOpp(r, 0));
    pattern('cubes-opp-t', 'cubes', .58, r => makeCubesOpp(r, 1));
    pattern('cubes-opp-step', 'cubes', .92, r => makeCubesOpp(r, 2));
    pattern('cubes-view-cross', 'cubes', 1.22, r => makeCubesView(r, 0));
    pattern('cubes-view-t', 'cubes', 1.54, r => makeCubesView(r, 1));
    pattern('cubes-view-step', 'cubes', 1.86, r => makeCubesView(r, 2));

    // ---------- pipes & fluid levels (pipes) ----------
    function makePipes(r, tankCount) {
        const tankNames = ['A', 'B', 'C', 'D', 'E'].slice(0, tankCount);
        const tA = 'A';
        let connections = [];
        let winningTank = tA;

        if (tankCount === 3) {
            // Tanks A, B, C: A branches to B and C (one low h=1, one high h=2)
            const lowTarget = r.next() < .5 ? 'B' : 'C';
            const highTarget = (lowTarget === 'B') ? 'C' : 'B';
            const scenario = r.pick(['low_open', 'low_open', 'low_blocked', 'both_blocked']);

            if (scenario === 'low_open') {
                connections = [
                    {from: tA, to: lowTarget, h: 1, blocked: false},
                    {from: tA, to: highTarget, h: 2, blocked: r.next() < .5},
                ];
                winningTank = lowTarget;
            } else if (scenario === 'low_blocked') {
                connections = [
                    {from: tA, to: lowTarget, h: 1, blocked: true},
                    {from: tA, to: highTarget, h: 2, blocked: false},
                ];
                winningTank = highTarget;
            } else {
                // Both outlets from A are blocked: tank A fills to 100% first
                connections = [
                    {from: tA, to: lowTarget, h: 1, blocked: true},
                    {from: tA, to: highTarget, h: 2, blocked: true},
                ];
                winningTank = tA;
            }
        } else if (tankCount === 4) {
            // Tanks A, B, C, D: A branches to B and C, one branch connects down to D
            const downstreamParent = r.next() < .5 ? 'B' : 'C';
            const sibling = (downstreamParent === 'B') ? 'C' : 'B';
            const parentIsLow = r.next() < .5;
            const scenario = r.pick(['to_d', 'parent_blocked_down', 'to_sibling', 'both_blocked_a']);

            if (scenario === 'to_d') {
                connections = [
                    {from: tA, to: downstreamParent, h: parentIsLow ? 1 : 2, blocked: false},
                    {from: tA, to: sibling, h: parentIsLow ? 2 : 1, blocked: parentIsLow ? r.next() < .5 : true},
                    {from: downstreamParent, to: 'D', h: 1, blocked: false},
                ];
                winningTank = 'D';
            } else if (scenario === 'parent_blocked_down') {
                connections = [
                    {from: tA, to: downstreamParent, h: parentIsLow ? 1 : 2, blocked: false},
                    {from: tA, to: sibling, h: parentIsLow ? 2 : 1, blocked: parentIsLow ? r.next() < .5 : true},
                    {from: downstreamParent, to: 'D', h: 1, blocked: true},
                ];
                winningTank = downstreamParent;
            } else if (scenario === 'to_sibling') {
                if (parentIsLow) {
                    connections = [
                        {from: tA, to: downstreamParent, h: 1, blocked: true},
                        {from: tA, to: sibling, h: 2, blocked: false},
                        {from: downstreamParent, to: 'D', h: 1, blocked: r.next() < .5},
                    ];
                } else {
                    connections = [
                        {from: tA, to: sibling, h: 1, blocked: false},
                        {from: tA, to: downstreamParent, h: 2, blocked: r.next() < .5},
                        {from: downstreamParent, to: 'D', h: 1, blocked: r.next() < .5},
                    ];
                }
                winningTank = sibling;
            } else {
                connections = [
                    {from: tA, to: downstreamParent, h: parentIsLow ? 1 : 2, blocked: true},
                    {from: tA, to: sibling, h: parentIsLow ? 2 : 1, blocked: true},
                    {from: downstreamParent, to: 'D', h: 1, blocked: r.next() < .5},
                ];
                winningTank = tA;
            }
        } else {
            // 5 tanks: A, B, C, D, E
            const lowTarget = r.next() < .5 ? 'B' : 'C';
            const highTarget = (lowTarget === 'B') ? 'C' : 'B';
            const lowDown = (lowTarget === 'B') ? 'D' : 'E';
            const highDown = (highTarget === 'C') ? 'E' : 'D';
            const outcome = r.pick(['lowDown', 'lowDown', 'lowTarget', 'highDown', 'highTarget', 'A']);

            if (outcome === 'lowDown') {
                connections = [
                    {from: tA, to: lowTarget, h: 1, blocked: false},
                    {from: tA, to: highTarget, h: 2, blocked: r.next() < .5},
                    {from: lowTarget, to: lowDown, h: 1, blocked: false},
                    {from: highTarget, to: highDown, h: 1, blocked: r.next() < .5},
                ];
                winningTank = lowDown;
            } else if (outcome === 'lowTarget') {
                connections = [
                    {from: tA, to: lowTarget, h: 1, blocked: false},
                    {from: tA, to: highTarget, h: 2, blocked: r.next() < .5},
                    {from: lowTarget, to: lowDown, h: 1, blocked: true},
                    {from: highTarget, to: highDown, h: 1, blocked: r.next() < .5},
                ];
                winningTank = lowTarget;
            } else if (outcome === 'highDown') {
                connections = [
                    {from: tA, to: lowTarget, h: 1, blocked: true},
                    {from: tA, to: highTarget, h: 2, blocked: false},
                    {from: lowTarget, to: lowDown, h: 1, blocked: r.next() < .5},
                    {from: highTarget, to: highDown, h: 1, blocked: false},
                ];
                winningTank = highDown;
            } else if (outcome === 'highTarget') {
                connections = [
                    {from: tA, to: lowTarget, h: 1, blocked: true},
                    {from: tA, to: highTarget, h: 2, blocked: false},
                    {from: lowTarget, to: lowDown, h: 1, blocked: r.next() < .5},
                    {from: highTarget, to: highDown, h: 1, blocked: true},
                ];
                winningTank = highTarget;
            } else {
                connections = [
                    {from: tA, to: lowTarget, h: 1, blocked: true},
                    {from: tA, to: highTarget, h: 2, blocked: true},
                    {from: lowTarget, to: lowDown, h: 1, blocked: r.next() < .5},
                    {from: highTarget, to: highDown, h: 1, blocked: r.next() < .5},
                ];
                winningTank = tA;
            }
        }

        const pipesData = {
            tanks: tankNames,
            connections,
            winner: winningTank,
            source: tA,
        };
        const right = winningTank;
        const q = {
            view: 'pipes', pipesData,
            lines: connections.map(c => `${c.from} ➔ ${c.to}${c.blocked ? ' (🚫)' : ''}`),
            pk: 'q_pipes_first',
            pv: {source: tA},
            why: (right === tA)
                ? [{k: 'why_pipes_blocked', v: {x: tA}}]
                : [{k: 'why_pipes_first', v: {x: right}}],
        };
        return withOptions(r, q, right, others(r, tankNames, right));
    }

    pattern('pipes-3', 'pipes', .22, r => makePipes(r, 3));
    pattern('pipes-4-a', 'pipes', .55, r => makePipes(r, 4));
    pattern('pipes-4-b', 'pipes', .90, r => makePipes(r, 4));
    pattern('pipes-5', 'pipes', 1.35, r => makePipes(r, 5));
    pattern('pipes-5-branch', 'pipes', 1.75, r => makePipes(r, 5));

    // ---------- hanging kinetic balance mobiles ----------
    function makeMobile(r, tier) {
        let beams = [];
        let rightAnswer = 0;

        if (tier === 1) {
            const pairs = [
                {dL: 1, dR: 1, wL: 4, wR: 4},
                {dL: 2, dR: 1, wL: 3, wR: 6},
                {dL: 1, dR: 2, wL: 8, wR: 4},
                {dL: 3, dR: 1, wL: 2, wR: 6},
                {dL: 2, dR: 3, wL: 6, wR: 4},
                {dL: 1, dR: 3, wL: 9, wR: 3},
                {dL: 3, dR: 2, wL: 4, wR: 6},
                {dL: 2, dR: 1, wL: 5, wR: 10},
            ];
            const p = r.pick(pairs);
            const hideRight = r.next() < .5;
            rightAnswer = hideRight ? p.wR : p.wL;
            beams = [{
                id: 'b0',
                x: 155, y: 35,
                dL: p.dL, dR: p.dR,
                left: {type: 'weight', val: hideRight ? p.wL : '?', isTarget: !hideRight, realVal: p.wL},
                right: {type: 'weight', val: hideRight ? '?' : p.wR, isTarget: hideRight, realVal: p.wR},
            }];
        } else if (tier === 2) {
            const sL = r.int(1, 2);
            const sR = r.int(1, 3);
            const k = r.int(2, 4);
            const wSL = k * sR;
            const wSR = k * sL;
            const subTotal = wSL + wSR;

            const dR = 1;
            let dL = r.int(1, 2);
            if (dL === 2 && subTotal % 2 !== 0) dL = 1;
            const wL = (subTotal * dR) / dL;

            const hideTarget = r.pick(['subRight', 'subLeft', 'mainLeft']);
            if (hideTarget === 'subRight') rightAnswer = wSR;
            else if (hideTarget === 'subLeft') rightAnswer = wSL;
            else rightAnswer = wL;

            const rel_min = Math.min(-dL, dR - sL);
            const rel_max = dR + sR;
            const b0_x = Math.round(155 - (rel_min + rel_max) * 32 / 2);
            const b1_x = b0_x + dR * 32;

            beams = [
                {
                    id: 'b0',
                    x: b0_x, y: 32,
                    dL: dL, dR: dR,
                    left: {type: 'weight', val: hideTarget === 'mainLeft' ? '?' : wL, isTarget: hideTarget === 'mainLeft', realVal: wL},
                    right: {type: 'beam', subBeamIdx: 1, totalWeight: subTotal},
                },
                {
                    id: 'b1',
                    x: b1_x, y: 92,
                    dL: sL, dR: sR,
                    left: {type: 'weight', val: hideTarget === 'subLeft' ? '?' : wSL, isTarget: hideTarget === 'subLeft', realVal: wSL},
                    right: {type: 'weight', val: hideTarget === 'subRight' ? '?' : wSR, isTarget: hideTarget === 'subRight', realVal: wSR},
                },
            ];
        } else {
            const sL = r.int(1, 3);
            const sR = r.int(1, 3);
            const k = r.int(2, 4);
            const wSubA = k * sR;
            const wSubB = k * sL;
            const sSum = wSubA + wSubB;
            const dL = r.int(1, 2);
            const dR = 1;
            const wL = (sSum * dR) / (dL === 2 && sSum % 2 !== 0 ? 1 : dL);
            const actualDL = (dL === 2 && sSum % 2 !== 0) ? 1 : dL;
            rightAnswer = wSubB;

            const rel_min = Math.min(-actualDL, dR - sL);
            const rel_max = dR + sR;
            const b0_x = Math.round(155 - (rel_min + rel_max) * 32 / 2);
            const b1_x = b0_x + dR * 32;

            beams = [
                {
                    id: 'b0',
                    x: b0_x, y: 32,
                    dL: actualDL, dR: dR,
                    left: {type: 'weight', val: wL, isTarget: false, realVal: wL},
                    right: {type: 'beam', subBeamIdx: 1, totalWeight: sSum},
                },
                {
                    id: 'b1',
                    x: b1_x, y: 92,
                    dL: sL, dR: sR,
                    left: {type: 'weight', val: wSubA, isTarget: false, realVal: wSubA},
                    right: {type: 'weight', val: '?', isTarget: true, realVal: wSubB},
                },
            ];
        }

        const mobilesData = {
            tier,
            beams,
            rightAnswer,
        };
        const right = String(rightAnswer);
        const distractors = [rightAnswer - 2, rightAnswer - 1, rightAnswer + 1, rightAnswer + 2, rightAnswer + 3, rightAnswer * 2]
            .filter(n => n > 0 && n !== rightAnswer)
            .map(String);

        const q = {
            view: 'mobiles', mobilesData,
            pk: 'q_mobile_balance',
            why: [{k: 'why_mobile', v: {x: right}}],
        };
        return withOptions(r, q, right, others(r, distractors, right));
    }

    pattern('mobiles-1-easy', 'mobiles', .25, r => makeMobile(r, 1));
    pattern('mobiles-1-med', 'mobiles', .58, r => makeMobile(r, 1));
    pattern('mobiles-2-easy', 'mobiles', .86, r => makeMobile(r, 2));
    pattern('mobiles-2-hard', 'mobiles', 1.25, r => makeMobile(r, 2));
    pattern('mobiles-3-exp', 'mobiles', 1.68, r => makeMobile(r, 3));
    pattern('mobiles-3-max', 'mobiles', 1.94, r => makeMobile(r, 3));

    // ---------- origami paper folding and hole punch ----------
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

    function unfoldOrigamiHoles(punch, steps) {
        let holes = [...punch];
        for (let i = steps.length - 1; i >= 0; i--) {
            const info = steps[i];
            const next = [];
            const seen = new Set();
            for (const p of holes) {
                const k1 = p[0] + ',' + p[1];
                if (!seen.has(k1)) { seen.add(k1); next.push(p); }
                const rx = (info.fold === 'L_TO_R' || info.fold === 'R_TO_L') ? (info.sx - p[0]) : p[0];
                const ry = (info.fold === 'T_TO_B' || info.fold === 'B_TO_T') ? (info.sy - p[1]) : p[1];
                const k2 = rx + ',' + ry;
                if (!seen.has(k2)) { seen.add(k2); next.push([rx, ry]); }
            }
            holes = next;
        }
        return holes;
    }

    function makeOrigami(r, tier, reqSize) {
        let size = reqSize || 4;
        let folds = [];
        let numFolds = 1;

        if (size === 4) {
            if (tier === 1) {
                numFolds = 1;
            } else if (tier === 2) {
                numFolds = 2;
            } else {
                numFolds = (r.next() < .5) ? 2 : 3;
            }
        } else if (size === 6) {
            numFolds = (tier <= 2 && r.next() < .35) ? 1 : 2;
        } else if (size === 8) {
            if (tier <= 3) numFolds = 2;
            else if (tier === 4) numFolds = (r.next() < .5) ? 2 : 3;
            else if (tier === 5) numFolds = (r.next() < .4) ? 3 : 4;
            else numFolds = 4;
        }

        let curB = { xMin: 0, xMax: size - 1, yMin: 0, yMax: size - 1 };
        for (let s = 0; s < numFolds; s++) {
            const w = curB.xMax - curB.xMin + 1;
            const h = curB.yMax - curB.yMin + 1;
            const valid = [];
            if (w >= 2 && w % 2 === 0) valid.push('L_TO_R', 'R_TO_L');
            if (h >= 2 && h % 2 === 0) valid.push('T_TO_B', 'B_TO_T');
            if (!valid.length) break;
            const f = r.pick(valid);
            folds.push(f);
            if (f === 'L_TO_R') curB.xMin = Math.floor((curB.xMin + curB.xMax) / 2) + 1;
            else if (f === 'R_TO_L') curB.xMax = Math.floor((curB.xMin + curB.xMax) / 2);
            else if (f === 'T_TO_B') curB.yMin = Math.floor((curB.yMin + curB.yMax) / 2) + 1;
            else if (f === 'B_TO_T') curB.yMax = Math.floor((curB.yMin + curB.yMax) / 2);
        }

        const { steps, finalBounds } = computeOrigamiBounds(folds, size);

        const validX = [];
        for (let x = finalBounds.xMin; x <= finalBounds.xMax; x++) validX.push(x);
        const validY = [];
        for (let y = finalBounds.yMin; y <= finalBounds.yMax; y++) validY.push(y);

        const allCells = [];
        for (const x of validX) {
            for (const y of validY) {
                allCells.push([x, y]);
            }
        }

        const wantTwoPunches = (tier >= 3 && (numFolds <= 3 || size <= 6) && r.next() < .6) || (tier >= 5 && r.next() < .7);
        let punch = [];
        if (!wantTwoPunches || allCells.length < 2) {
            punch = [[r.pick(validX), r.pick(validY)]];
        } else {
            punch = r.shuffle(allCells).slice(0, 2);
        }

        const rightHoles = unfoldOrigamiHoles(punch, steps);
        const serialize = pts => {
            const grid = Array.from({length: size}, () => Array(size).fill(0));
            pts.forEach(([x, y]) => { if (x >= 0 && x < size && y >= 0 && y < size) grid[y][x] = 1; });
            return grid.flat().join('');
        };

        const rightKey = serialize(rightHoles);
        const distractors = [];
        const maxCoord = size - 1;

        // Distractor 1: partial unfold (if multiple folds)
        if (steps.length > 1) {
            const partial = unfoldOrigamiHoles(punch, steps.slice(0, steps.length - 1));
            const d1 = serialize(partial);
            if (d1 !== rightKey && !distractors.includes(d1)) distractors.push(d1);
        }

        // Distractor 2: 90-degree rotated
        const rot90 = serialize(rightHoles.map(([x, y]) => [y, maxCoord - x]));
        if (rot90 !== rightKey && !distractors.includes(rot90)) distractors.push(rot90);

        // Distractor 3: 180-degree rotated
        const rot180 = serialize(rightHoles.map(([x, y]) => [maxCoord - x, maxCoord - y]));
        if (rot180 !== rightKey && !distractors.includes(rot180)) distractors.push(rot180);

        // Distractor 4: Inverted X
        const invX = serialize(rightHoles.map(([x, y]) => [maxCoord - x, y]));
        if (invX !== rightKey && !distractors.includes(invX)) distractors.push(invX);

        // Distractor 5: Inverted Y
        const invY = serialize(rightHoles.map(([x, y]) => [x, maxCoord - y]));
        if (invY !== rightKey && !distractors.includes(invY)) distractors.push(invY);

        // Fill remaining with another punch position unfolded under same steps
        let attempts = 0;
        while (distractors.length < 3 && attempts < 30) {
            attempts++;
            let altPunch = [];
            if (allCells.length >= punch.length) {
                altPunch = r.shuffle(allCells).slice(0, punch.length);
            } else {
                altPunch = [[r.int(finalBounds.xMin, finalBounds.xMax), r.int(finalBounds.yMin, finalBounds.yMax)]];
            }
            const altKey = serialize(unfoldOrigamiHoles(altPunch, steps));
            if (altKey !== rightKey && !distractors.includes(altKey)) {
                distractors.push(altKey);
            }
        }

        const allKeys = [rightKey, ...distractors].slice(0, 4);
        while (allKeys.length < 4) {
            const fallbackPunch = [];
            for (let p = 0; p < punch.length; p++) {
                fallbackPunch.push([r.int(0, size - 1), r.int(0, size - 1)]);
            }
            const fbKey = serialize(fallbackPunch);
            if (!allKeys.includes(fbKey)) allKeys.push(fbKey);
            else allKeys.push(serialize([[r.int(0, size - 1), r.int(0, size - 1)]]));
        }

        const shuffledKeys = r.shuffle(allKeys);
        const letterOptions = ['A', 'B', 'C', 'D'];
        const rightLetter = letterOptions[shuffledKeys.indexOf(rightKey)];

        const origamiData = {
            size,
            tier,
            noGuides: tier >= 4 || (tier >= 3 && r.next() < .5),
            folds,
            stepBounds: steps,
            finalBounds,
            punch,
            unfoldedHoles: rightHoles,
            candidateSheets: letterOptions.map((letter, idx) => ({
                letter,
                pattern: shuffledKeys[idx] || rightKey,
            })),
        };

        const q = {
            view: 'origami', origamiData,
            pics: true,
            pk: 'q_origami_unfold',
            why: [{k: 'why_origami', v: {x: rightLetter}}],
        };

        return withOptions(r, q, rightLetter, letterOptions.filter(l => l !== rightLetter));
    }

    pattern('origami-1-easy', 'origami', .28, r => makeOrigami(r, 1, 4));
    pattern('origami-2-med', 'origami', .68, r => makeOrigami(r, 2, 4));
    pattern('origami-2-hard', 'origami', 1.18, r => makeOrigami(r, 2, 4));
    pattern('origami-3-diag', 'origami', 1.55, r => makeOrigami(r, 3, 4));
    pattern('origami-3-max', 'origami', 1.88, r => makeOrigami(r, 3, 4));
    pattern('origami-4-med', 'origami', 3.45, r => makeOrigami(r, 3, 6));
    pattern('origami-4-hard', 'origami', 4.85, r => makeOrigami(r, 4, 6));
    pattern('origami-5-exp', 'origami', 6.40, r => makeOrigami(r, 5, 8));
    pattern('origami-6-master', 'origami', 7.80, r => makeOrigami(r, 6, 8));
    pattern('origami-7-legend', 'origami', 9.20, r => makeOrigami(r, 7, 8));
})(typeof window !== 'undefined' ? window : globalThis);
