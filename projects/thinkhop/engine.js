/*
 * ThinkHop — questions, games and points. No DOM here: it also runs in Node (tools/check_questions.js).
 *
 * A question is plain data, without any language inside, so a game can be saved and resumed in another language:
 *   {pid, sig, cat, d, level, limit, view, ...data, options: [...], answer: index, why: [...]}
 * An option is a string shown as it is, or {k: 'key'} (translated) or {r: n} (option n of the riddle).
 *
 * Every kind of exercise is a "pattern" with its own difficulty d, from 0.00 (super easy) to 1.99 (super hard).
 * Players only see the tier that d falls in. Levels ask for a d that keeps growing, and a question that was already
 * seen is not asked again (see `sig` and pick()). More patterns are registered by puzzles.js.
 * Every generator guarantees one right answer: see the comments and tools/check_questions.js.
 */
(function (root) {
    'use strict';

    const TIERS = ['novice', 'easy', 'medium', 'hard', 'expert', 'master', 'legend'];
    const CATS = ['sign', 'sequence', 'symbols', 'oddone', 'order', 'weird', 'nonsense', 'riddle',
        'pattern', 'letters', 'intruder', 'grid', 'compass', 'lamps', 'code', 'ifthen', 'liars', 'boxes', 'whoowns', 'days', 'trap',
        'scales', 'cups', 'locks', 'suspects', 'alchemy', 'seats', 'mirror', 'gears', 'lasers', 'cubes', 'pipes', 'mobiles', 'origami'];
    const MATH = ['sign', 'sequence', 'symbols', 'oddone', 'weird']; // this is a game of logic: few of these in each game
    const MAX_D = 9.99;
    const STAGES = 1000;        // levels of the game, in 100 worlds of 10
    const WORLD = 10;
    const DAILY_LENGTH = 10;
    const LIVES = 3;
    const START_POINTS = 100;   // a gift, so that the first hint can be tried

    const tierOf = d => TIERS[Math.min(6, Math.max(0, Math.floor((+d || 0) / 1.5)))];

    const DIFFICULTY_MODES = {
        beginner: {key: 'beginner', name: 'Principiante', targets: [.6, 1.1, 1.6, 2.1, 2.6], tiers: ['novice', 'easy']},
        intermediate: {key: 'intermediate', name: 'Intermedio', targets: [3.3, 3.9, 4.5, 5.1, 5.7], tiers: ['medium', 'hard']},
        hero: {key: 'hero', name: 'Eroe', targets: [6.3, 6.9, 7.5, 8.1, 8.7], tiers: ['expert', 'master']},
        impossible: {key: 'impossible', name: 'Impossibile', targets: [9.1, 9.3, 9.5, 9.7, 9.95], tiers: ['legend']},
    };

    // What the admin panel can change (see api/index.php): the defaults work without any server
    const settings = {
        levelLength: 5,
        hintCost: {novice: 40, easy: 60, medium: 90, hard: 130, expert: 170, master: 220, legend: 280, /* legacy compatibility */ easy_legacy: 50},
        disabledCats: [], disabledRiddles: [], difficulty: {}, catWeights: {}
    };
    let customQuestions = [];

    function configure(c) {
        if (!c) return;
        ['levelLength', 'hintCost', 'disabledCats', 'disabledRiddles', 'difficulty', 'catWeights'].forEach(k => {
            if (c[k] !== undefined) settings[k] = Array.isArray(c[k]) && (k === 'difficulty' || k === 'catWeights') ? {} : c[k]; // PHP sends an empty object as []
        });
        if (c.questions) customQuestions = c.questions;
        catalogue = null;
    }

    // ---------- random numbers (seeded: the daily challenge is the same for everyone) ----------
    function rng(seed) {
        let a = seed >>> 0;
        const next = () => {
            a = (a + 0x6D2B79F5) >>> 0;
            let t = a;
            t = Math.imul(t ^ (t >>> 15), t | 1);
            t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
            return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
        };
        const int = (lo, hi) => lo + Math.floor(next() * (hi - lo + 1));
        const pick = list => list[int(0, list.length - 1)];
        const shuffle = list => {
            const out = list.slice();
            for (let i = out.length - 1; i > 0; i--) {
                const j = int(0, i);
                [out[i], out[j]] = [out[j], out[i]];
            }
            return out;
        };
        return {next, int, pick, shuffle};
    }

    function hashSeed(text) {
        let h = 2166136261;
        for (let i = 0; i < text.length; i++) h = Math.imul(h ^ text.charCodeAt(i), 16777619);
        return h >>> 0;
    }

    /** Puts the right option among the others, in random order. */
    function withOptions(r, q, right, wrong) {
        const options = r.shuffle([right, ...wrong]);
        return Object.assign(q, {options, answer: options.indexOf(right)});
    }

    /** Three wrong numbers close to the answer; `near` are the mistakes people really make. */
    function numberOptions(r, q, right, near) {
        const wrong = [];
        const add = n => {
            if (Number.isInteger(n) && n >= 0 && n !== right && !wrong.includes(n) && wrong.length < 3) wrong.push(n);
        };
        (near || []).forEach(add);
        const spread = Math.max(2, Math.round(Math.abs(right) * .15));
        for (let i = 0; wrong.length < 3; i++) add(right + r.int(-spread - i, spread + i));
        return withOptions(r, q, String(right), wrong.map(String));
    }

    // ---------- the missing sign: 2 ? 3 = 8 ----------
    const OPS = ['+', '−', '×', '÷', '^'];

    /** Value of a0 op0 a1 op1 a2… with the usual precedence; null when it is not a whole number. */
    function evaluate(nums, ops) {
        const n = nums.slice(), o = ops.slice();
        const reduce = (set, fn) => {
            for (let i = 0; i < o.length;) {
                if (!set.includes(o[i])) {
                    i++;
                    continue;
                }
                n.splice(i, 2, fn(n[i], o[i], n[i + 1]));
                o.splice(i, 1);
            }
        };
        const apply = (a, op, b) => {
            if (a === null || b === null) return null;
            if (op === '+') return a + b;
            if (op === '−') return a - b;
            if (op === '×') return a * b;
            if (op === '÷') return b !== 0 && a % b === 0 ? a / b : null;
            return b >= 0 && b <= 12 ? Math.pow(a, b) : null;
        };
        reduce(['^'], apply);
        reduce(['×', '÷'], apply);
        reduce(['+', '−'], apply);
        return n[0];
    }

    const showExpr = (nums, ops) => nums.reduce((s, n, i) => s + (i ? ` ${ops[i - 1]} ` : '') + n, '');

    function signOne(r, level) {
        const ops = level === 'easy' ? OPS.slice(0, 4) : OPS;
        for (; ;) {
            const op = level === 'easy' ? r.pick(ops) : r.pick(['^', '^', '+', '−', '×', '÷']);
            const a = level === 'easy' ? r.int(2, 12) : r.int(2, 15);
            const b = op === '^' ? r.int(2, 4) : (level === 'easy' ? r.int(2, 9) : r.int(2, 15));
            const res = evaluate([a, b], [op]);
            if (res === null || res < 0 || res > 2000) continue;
            // Only one of the signs on screen may work: 2 ? 2 = 4 is not a fair question
            if (ops.filter(o => evaluate([a, b], [o]) === res).length !== 1) continue;
            const q = {cat: 'sign', level, view: 'equation', nums: [a, b], result: res};
            q.why = [{lit: `${a} ${op} ${b} = ${res}`}];
            if (op === '^') q.why.push({k: 'why_power', v: {a, b, chain: Array(b).fill(a).join(' × ')}});
            return withOptions(r, q, op, ops.filter(o => o !== op));
        }
    }

    function signTwo(r) {
        for (; ;) {
            const nums = [r.int(2, 9), r.int(2, 9), r.int(2, 9)];
            const pair = [r.pick(OPS), r.pick(OPS)];
            const res = evaluate(nums, pair);
            if (res === null || res < 0 || res > 500) continue;
            const all = [];
            OPS.forEach(a => OPS.forEach(b => all.push([a, b])));
            const fits = all.filter(p => evaluate(nums, p) === res);
            if (fits.length !== 1) continue; // one pair of signs in the whole game, not only among the options
            const wrong = r.shuffle(all.filter(p => p !== fits[0] && (p[0] === pair[0] || p[1] === pair[1]))).slice(0, 3);
            const label = p => `${p[0]}  ${p[1]}`;
            const q = {cat: 'sign', level: 'hard', view: 'equation', nums, result: res};
            q.why = [{lit: `${showExpr(nums, pair)} = ${res}`}];
            const strong = o => ['×', '÷', '^'].includes(o);
            if (strong(pair[1]) && !strong(pair[0]) || pair[1] === '^' && pair[0] !== '^') q.why.push({k: 'why_precedence'});
            return withOptions(r, q, label(pair), wrong.map(label));
        }
    }

    function signThree(r) {
        const all = [];
        OPS.forEach(a => OPS.forEach(b => OPS.forEach(c => all.push([a, b, c]))));
        for (; ;) {
            const nums = [r.int(2, 9), r.int(2, 9), r.int(2, 9), r.int(2, 9)];
            const signs = r.pick(all);
            const res = evaluate(nums, signs);
            if (res === null || res < 0 || res > 1000) continue;
            const fits = all.filter(p => evaluate(nums, p) === res);
            if (fits.length !== 1) continue;
            const wrong = r.shuffle(all.filter(p => p !== fits[0] && p.filter((o, i) => o === signs[i]).length === 2)).slice(0, 3);
            const label = p => p.join('  ');
            const q = {cat: 'sign', level: 'expert', view: 'equation', nums, result: res};
            q.why = [{lit: `${showExpr(nums, signs)} = ${res}`}, {k: 'why_precedence'}];
            return withOptions(r, q, label(signs), wrong.map(label));
        }
    }

    function makeSign(r, level) {
        if (level === 'expert') return signThree(r);
        return level === 'hard' && r.next() < .7 ? signTwo(r) : signOne(r, level);
    }

    // ---------- what comes next? ----------
    const PRIMES = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61];

    function iterate(start, count, step) {
        const out = [start];
        while (out.length < count) out.push(step(out[out.length - 1], out.length));
        return out;
    }

    const SEQUENCES = {
        easy: [
            r => {
                const d = r.int(2, 9);
                return {t: iterate(r.int(1, 20), 6, x => x + d), why: {k: 'seq_add', v: {d}}};
            },
            r => {
                const d = r.int(2, 9);
                return {t: iterate(d * 6 + r.int(1, 20), 6, x => x - d), why: {k: 'seq_sub', v: {d}}};
            },
            r => ({t: iterate(r.int(1, 5), 6, x => x * 2), why: {k: 'seq_mul', v: {m: 2}}}),
            r => {
                const m = r.pick([3, 4]);
                return {t: iterate(r.int(1, 6), 5, x => x * m), why: {k: 'seq_mul', v: {m}}};
            },
        ],
        medium: [
            r => {
                const s = r.int(1, 9);
                return {t: [0, 1, 2, 3, 4, 5].map(i => (i + s) * (i + s)), why: {k: 'seq_square'}};
            },
            r => ({t: iterate(r.int(1, 9), 6, (x, i) => x + i), why: {k: 'seq_grow'}}),
            r => {
                const a = r.int(1, 5), b = r.int(1, 5);
                const t = [a, b];
                while (t.length < 7) t.push(t[t.length - 1] + t[t.length - 2]);
                return {t, why: {k: 'seq_fib'}};
            },
            r => {
                const a = r.int(4, 9), b = r.int(1, 3);
                return {t: iterate(r.int(1, 12), 7, (x, i) => i % 2 ? x + a : x - b), why: {k: 'seq_alt', v: {a, b}}};
            },
            r => {
                const a = r.int(1, 3);
                return {t: iterate(r.int(1, 4), 6, x => x * 2 + a), why: {k: 'seq_muladd', v: {m: 2, a}}};
            },
        ],
        hard: [
            r => {
                const s = r.int(1, 8);
                return {t: [0, 1, 2, 3, 4].map(i => Math.pow(i + s, 3)), why: {k: 'seq_cube'}};
            },
            r => {
                const s = r.int(0, 9);
                return {t: PRIMES.slice(s, s + 6), why: {k: 'seq_prime'}};
            },
            r => {
                const a = r.int(2, 6), b = r.int(2, 6), x = r.int(1, 9), y = r.int(20, 40);
                return {t: [x, y, x + a, y - b, x + 2 * a, y - 2 * b, x + 3 * a], why: {k: 'seq_mix', v: {a, b}}};
            },
            r => ({t: iterate(r.int(1, 6), 6, (x, i) => x * (i + 1)), why: {k: 'seq_factorial'}}),
            r => {
                const a = r.int(1, 4);
                return {t: iterate(r.int(1, 5), 7, (x, i) => i % 2 ? x * 2 : x + a), why: {k: 'seq_altmul', v: {a}}};
            },
            r => {
                const k = r.int(1, 5);
                return {t: [1, 2, 3, 4, 5, 6].map(n => n * n + k), why: {k: 'seq_squareadd', v: {k}}};
            },
        ],
        expert: [
            r => {
                const k = r.int(1, 9);
                return {t: [1, 2, 3, 4, 5, 6].map(n => n * n * n + k), why: {k: 'seq_cubeadd', v: {k}}};
            },
            r => ({t: iterate(r.int(1, 20), 7, (x, i) => x + PRIMES[i - 1]), why: {k: 'seq_primestep'}}),
            r => {
                const t = [r.int(1, 3), r.int(1, 3), r.int(2, 4)];
                while (t.length < 8) t.push(t[t.length - 1] + t[t.length - 2] + t[t.length - 3]);
                return {t, why: {k: 'seq_trib'}};
            },
            r => {
                const d = r.int(1, 4);
                return {t: iterate(r.int(1, 9), 7, (x, i) => x + d * Math.pow(2, i - 1)), why: {k: 'seq_doublestep', v: {a: d, b: d * 2, c: d * 4}}};
            },
            r => {
                const s = r.int(1, 6);
                return {t: [0, 1, 2, 3, 4, 5].map(i => (i + s) * (i + s + 1)), why: {k: 'seq_pronic'}};
            },
            r => {
                const s = r.int(1, 6), flip = r.int(0, 1);
                return {t: [0, 1, 2, 3, 4, 5, 6].map(i => Math.pow(i + s, (i + flip) % 2 ? 3 : 2)), why: {k: 'seq_sqcube'}};
            },
        ],
    };

    const SEQUENCE_D = {easy: [.05, .08, .1, .14], medium: [.42, .38, .5, .55, .6], hard: [.8, .85, .92, .78, 1, .88], expert: [1.2, 1.3, 1.42, 1.25, 1.35, 1.55]};

    function makeSequence(r, level, make) {
        const s = make(r);
        const shown = s.t.slice(0, -1), right = s.t[s.t.length - 1];
        const last = shown[shown.length - 1], step = last - shown[shown.length - 2];
        const q = {cat: 'sequence', level, view: 'list', items: shown, why: [s.why]};
        return numberOptions(r, q, right, [last + step, right + 1, right - 1, right + 2]);
    }

    // ---------- fruit algebra: 🍎 + 🍎 = 6 ----------
    const FRUIT = ['🍎', '🍌', '🍇', '🍒', '🍋', '🍓', '🥝', '🍑', '🍐', '🍍', '🍉', '🍊', '🥭', '🫐'];

    function makeSymbols(r, level, alt) {
        const [x, y, z, w] = r.shuffle(FRUIT);
        const a = r.int(2, 9), b = r.int(2, 9), c = r.int(2, 6);
        const q = {cat: 'symbols', level, view: 'lines'};
        let right, near;
        // Every line adds one unknown only, so the values follow one after the other
        if (level === 'easy') {
            if (alt) {
                q.lines = [`${x} + ${x} + ${x} = ${3 * a}`, `${x} + ${y} + ${y} = ${a + 2 * b}`];
                q.ask = `${x} + ${y} = ?`;
                right = a + b;
                near = [a + 2 * b, 2 * a + b, b];
            } else {
                q.lines = [`${x} + ${x} = ${2 * a}`, `${x} + ${y} = ${a + b}`];
                q.ask = `${y} = ?`;
                right = b;
                near = [a, a + b, 2 * a - b];
            }
            q.why = [{lit: `${x} = ${a}, ${y} = ${b}`}];
        } else if (level === 'medium') {
            if (alt) {
                q.lines = [`${x} × ${x} = ${a * a}`, `${x} + ${y} = ${a + b}`, `${y} − ${z} = ${b - c}`];
                q.ask = `${x} + ${y} + ${z} = ?`;
                right = a + b + c;
                near = [a + b - c, a * b + c, a + c];
            } else {
                q.lines = [`${x} + ${x} + ${x} = ${3 * a}`, `${x} + ${y} = ${a + b}`, `${y} + ${z} = ${b + c}`];
                q.ask = `${x} + ${z} = ?`;
                right = a + c;
                near = [a + b, b + c, a * c];
            }
            q.why = [{lit: `${x} = ${a}, ${y} = ${b}, ${z} = ${c}`}];
        } else if (level === 'expert') {
            const d = r.int(1, c - 1);
            if (alt) {
                q.lines = [`${x} + ${x} = ${2 * a}`, `${x} × ${y} − ${x} = ${a * b - a}`, `${y} + ${z} × ${z} = ${b + c * c}`, `${z} + ${w} = ${c + d}`];
                q.ask = `${w} × ${x} + ${y} = ?`;
                right = d * a + b;
                near = [d * (a + b), a * b + d, c * a + b];
                q.why = [{lit: `${x} = ${a}, ${y} = ${b}, ${z} = ${c}, ${w} = ${d}`}, {lit: `${d} × ${a} + ${b} = ${right}`}];
            } else {
                q.lines = [`${x} × ${x} = ${a * a}`, `${x} + ${y} = ${a + b}`, `${y} × ${z} = ${b * c}`, `${z} − ${w} = ${c - d}`];
                q.ask = `${x} × ${y} + ${z} − ${w} = ?`;
                right = a * b + c - d;
                near = [a * (b + c) - d, a * b + c + d, a + b + c + d];
                q.why = [{lit: `${x} = ${a}, ${y} = ${b}, ${z} = ${c}, ${w} = ${d}`}, {lit: `${a} × ${b} + ${c} − ${d} = ${right}`}];
            }
        } else {
            if (alt) {
                q.lines = [`${x} × ${x} = ${a * a}`, `${x} + ${y} + ${y} = ${a + 2 * b}`, `${y} + ${z} = ${b + c}`];
                q.ask = `${z} × ${x} − ${y} = ?`;
                right = Math.max(1, c * a - b);
                if (c * a - b < 1) {
                    q.ask = `${x} × ${y} − ${z} = ?`;
                    right = a * b - c;
                    near = [a * (b - c), a * b + c, a + b - c];
                    q.why = [{lit: `${x} = ${a}, ${y} = ${b}, ${z} = ${c}`}, {lit: `${a} × ${b} − ${c} = ${right}`}, {k: 'why_precedence'}];
                } else {
                    near = [c * (a - b), c * a + b, a + b + c];
                    q.why = [{lit: `${x} = ${a}, ${y} = ${b}, ${z} = ${c}`}, {lit: `${c} × ${a} − ${b} = ${right}`}, {k: 'why_precedence'}];
                }
            } else {
                q.lines = [`${x} + ${x} = ${2 * a}`, `${x} × ${y} = ${a * b}`, `${y} − ${z} = ${b - c}`];
                q.ask = `${x} + ${y} × ${z} = ?`;
                right = a + b * c;
                near = [(a + b) * c, a * b + c, a + b + c];
                q.why = [{lit: `${x} = ${a}, ${y} = ${b}, ${z} = ${c}`}, {lit: `${a} + ${b} × ${c} = ${right}`}, {k: 'why_precedence'}];
            }
        }
        return numberOptions(r, q, right, near);
    }

    // ---------- the odd one out ----------
    const digitSum = n => String(n).split('').reduce((s, d) => s + +d, 0);
    const isPrime = n => {
        if (n < 2) return false;
        for (let i = 2; i * i <= n; i++) if (n % i === 0) return false;
        return true;
    };
    const isSquare = n => Number.isInteger(Math.sqrt(n));
    const isPalindrome = n => String(n) === String(n).split('').reverse().join('');
    const isTriangular = n => Number.isInteger((Math.sqrt(8 * n + 1) - 1) / 2);
    const FIBONACCI = [1, 2, 3, 5, 8, 13, 21, 34, 55, 89, 144, 233, 377, 610];
    const between = (lo, hi, test) => {
        const out = [];
        for (let n = lo; n <= hi; n++) if (test(n)) out.push(n);
        return out;
    };

    const PROPERTIES = {
        easy: [
            () => ({p: 'even', test: n => n % 2 === 0, range: [2, 60]}),
            () => ({p: 'odd', test: n => n % 2 === 1, range: [2, 60]}),
            () => ({p: 'multiple', v: {m: 5}, test: n => n % 5 === 0, range: [5, 80]}),
            () => ({p: 'multiple', v: {m: 10}, test: n => n % 10 === 0, range: [10, 120]}),
        ],
        medium: [
            r => {
                const m = r.pick([3, 4, 6, 7, 9]);
                return {p: 'multiple', v: {m}, test: n => n % m === 0, range: [m * 2, m * 14]};
            },
            () => ({p: 'square', test: isSquare, range: [4, 144]}),
        ],
        hard: [
            () => ({p: 'prime', test: isPrime, range: [11, 97], decoys: [21, 27, 33, 39, 49, 51, 57, 63, 69, 77, 81, 87, 91, 93]}),
            () => ({p: 'cube', test: n => Number.isInteger(Math.round(Math.cbrt(n))) && Math.pow(Math.round(Math.cbrt(n)), 3) === n, range: [8, 343], decoys: [16, 25, 36, 49, 81, 100, 121, 144, 200, 256]}),
            r => {
                const s = r.int(6, 12);
                return {p: 'digits', v: {s}, test: n => digitSum(n) === s, range: [12, 99]};
            },
            () => ({p: 'palindrome', test: isPalindrome, range: [101, 999]}),
            r => {
                const m = r.pick([11, 13]);
                return {p: 'multiple', v: {m}, test: n => n % m === 0, range: [m * 2, m * 12]};
            },
            () => ({p: 'power', test: n => (n & (n - 1)) === 0, range: [4, 1024], decoys: [6, 12, 24, 48, 96, 100, 200, 500, 1000]}),
        ],
        expert: [
            () => ({p: 'triangular', test: isTriangular, range: [10, 210], decoys: [12, 16, 20, 25, 30, 40, 50, 60, 70, 80, 100, 110, 130, 150, 200]}),
            () => ({p: 'fibonacci', test: n => FIBONACCI.includes(n), range: [13, 610], decoys: [14, 22, 33, 35, 54, 56, 88, 90, 143, 145, 234, 376, 600]}),
            () => ({p: 'prime', test: isPrime, range: [101, 199], decoys: [111, 117, 119, 121, 123, 129, 133, 141, 143, 147, 153, 159, 161, 171, 177, 187, 189]}),
            r => {
                const m = r.pick([17, 19, 23]);
                return {p: 'multiple', v: {m}, test: n => n % m === 0, range: [m * 3, m * 15]};
            },
        ],
    };

    const ODD_D = {easy: [.1, .1, .16, .12], medium: [.45, .5], hard: [.85, .95, .9, .8, 1, .9], expert: [1.3, 1.4, 1.3, 1.45]};

    function makeOddOne(r, level, make) {
        const prop = make(r);
        const [lo, hi] = prop.range;
        const members = r.shuffle(between(lo, hi, prop.test)).slice(0, 3);
        const outsiders = prop.decoys || between(lo, hi, n => !prop.test(n));
        const odd = r.pick(outsiders.filter(n => !prop.test(n)));
        const q = {cat: 'oddone', level, view: 'odd', why: [{k: 'why_odd', v: {x: odd, prop: {k: 'prop_' + prop.p, v: prop.v || {}}}, prop: {k: 'prop_' + prop.p, v: prop.v || {}}}]};
        return withOptions(r, q, String(odd), members.map(String));
    }

    // ---------- who finishes first? ----------
    const NAMES = ['Ada', 'Bruno', 'Carla', 'Dino', 'Elsa', 'Fabio', 'Gina', 'Hugo', 'Irene', 'Leo', 'Marta', 'Nico', 'Olga', 'Pablo', 'Rita', 'Sara', 'Teo', 'Vera'];

    const factHolds = (f, order) => {
        const a = order.indexOf(f.v.a), b = order.indexOf(f.v.b);
        if (f.k === 'ord_before') return a < b;
        if (f.k === 'ord_after') return a > b;
        if (f.k === 'ord_next') return a === b + 1;
        return Math.abs(a - b) === 2; // ord_gap
    };

    function orderings(list) {
        if (list.length < 2) return [list];
        return list.flatMap((p, i) => orderings(list.filter((_, j) => j !== i)).map(rest => [p, ...rest]));
    }

    /** Clues that do not say the order directly: they are added until one order is left, then the useless ones go. */
    function makeOrderExpert(r) {
        const people = r.shuffle(NAMES).slice(0, 5);
        const all = orderings(people);
        const clues = [];
        people.forEach((a, i) => people.forEach((b, j) => {
            if (j === i + 1) clues.push({k: 'ord_next', v: {a: b, b: a}});
            if (j === i + 2) clues.push({k: 'ord_gap', v: {a, b}});
            if (j > i + 1) clues.push(r.next() < .5 ? {k: 'ord_before', v: {a, b}} : {k: 'ord_after', v: {a: b, b: a}});
        }));
        const left = list => all.filter(o => list.every(f => factHolds(f, o))).length;
        let facts = [];
        for (const clue of r.shuffle(clues)) {
            if (left(facts) === 1) break;
            if (left([...facts, clue]) < left(facts)) facts.push(clue);
        }
        facts.slice().forEach(f => {
            const others = facts.filter(o => o !== f);
            if (left(others) === 1) facts = others;
        });
        const [ask, at] = r.pick([['ord_q_first', 0], ['ord_q_last', 4], ['ord_q_second', 1], ['ord_q_penultimate', 3]]);
        const right = people[at];
        const q = {cat: 'order', level: 'expert', view: 'facts', facts: r.shuffle(facts), ask: {k: ask}, why: [{lit: people.join(' → ')}]};
        return withOptions(r, q, right, r.shuffle(people.filter(p => p !== right)).slice(0, 3));
    }

    function makeOrder(r, level) {
        if (level === 'expert') return makeOrderExpert(r);
        const n = {easy: 3, medium: 4, hard: 5}[level];
        const people = r.shuffle(NAMES).slice(0, n); // the true order, first to last
        // Each pair of neighbours appears once, so the whole order is fixed
        const facts = [];
        for (let i = 0; i + 1 < n; i++) {
            const after = level !== 'easy' && r.next() < .5;
            facts.push(after ? {k: 'ord_after', v: {a: people[i + 1], b: people[i]}} : {k: 'ord_before', v: {a: people[i], b: people[i + 1]}});
        }
        const asks = level === 'hard' ? [['ord_q_second', 1], ['ord_q_penultimate', n - 2]] : [['ord_q_first', 0], ['ord_q_last', n - 1]];
        const [ask, at] = r.pick(asks);
        const right = people[at];
        const q = {cat: 'order', level, view: 'facts', facts: r.shuffle(facts), ask: {k: ask}, why: [{lit: people.join(' → ')}]};
        return withOptions(r, q, right, r.shuffle(people.filter(p => p !== right)).slice(0, 3));
    }

    // ---------- arithmetic from another world: 2 + 3 = 7 ----------
    const WEIRD = {
        easy: [
            k => ({f: (a, b) => a + b + k, s: `a + b + ${k}`}),
            k => ({f: (a, b) => a + b + a, s: 'a + b + a', fixed: true}),
            k => ({f: (a, b) => (a + b) * 2, s: '(a + b) × 2', fixed: true}),
        ],
        medium: [
            k => ({f: (a, b) => a * b + k, s: `a × b + ${k}`}),
            k => ({f: (a, b) => a * b - k, s: `a × b − ${k}`}),
            k => ({f: (a, b) => a * a + b, s: 'a × a + b', fixed: true}),
            k => ({f: (a, b) => (a + b) * 3, s: '(a + b) × 3', fixed: true}),
        ],
        hard: [
            k => ({f: (a, b) => a * a + b * b, s: 'a × a + b × b', fixed: true}),
            k => ({f: (a, b) => a * b + a + b, s: 'a × b + a + b', fixed: true}),
            k => ({f: (a, b) => a * b - a, s: 'a × b − a', fixed: true}),
            k => ({f: (a, b) => a * b + b, s: 'a × b + b', fixed: true}),
            k => ({f: (a, b) => a * a - b, s: 'a × a − b', fixed: true}),
            k => ({f: (a, b) => (a + b) * (a + b), s: '(a + b) × (a + b)', fixed: true}),
        ],
        expert: [
            k => ({f: (a, b) => a * a * b, s: 'a × a × b', fixed: true}),
            k => ({f: (a, b) => (a + b) * a, s: '(a + b) × a', fixed: true}),
            k => ({f: (a, b) => (a - b) * (a + b), s: '(a − b) × (a + b)', fixed: true}),
            k => ({f: (a, b) => a * a * a - b, s: 'a × a × a − b', fixed: true}),
            k => ({f: (a, b) => a * b * 2 - a, s: 'a × b × 2 − a', fixed: true}),
            k => ({f: (a, b) => a * a + b * b + a, s: 'a × a + b × b + a', fixed: true}),
        ],
    };
    // Every rule the player could reasonably suspect: the examples must leave only one of them standing
    const WEIRD_ALL = [...WEIRD.easy, ...WEIRD.medium, ...WEIRD.hard, ...WEIRD.expert].reduce((all, make) => {
        const one = make(1);
        if (one.fixed) all.push(one); else for (let k = 1; k <= 6; k++) all.push(make(k));
        return all;
    }, [(a, b) => a + b, (a, b) => a * b].map(f => ({f})));

    const WEIRD_D = {easy: [.3, .36, .4], medium: [.62, .66, .72, .7], hard: [1.05, 1.1, 1, 1, 1.05, 1.15], expert: [1.5, 1.45, 1.6, 1.55, 1.6, 1.7]};

    function makeWeird(r, level, make) {
        for (; ;) {
            const rule = make(r.int(1, 5));
            const pairs = [];
            while (pairs.length < 4) {
                const p = [r.int(2, 9), r.int(1, 9)];
                if (!pairs.some(o => o[0] === p[0] && o[1] === p[1])) pairs.push(p);
            }
            const examples = pairs.slice(0, level === 'easy' ? 2 : 3);
            const [a, b] = pairs[3];
            const right = rule.f(a, b);
            const twins = WEIRD_ALL.filter(o => examples.every(([x, y]) => o.f(x, y) === rule.f(x, y)));
            if (twins.some(o => o.f(a, b) !== right)) continue;
            if (right < 0 || examples.some(([x, y]) => rule.f(x, y) < 0)) continue;
            const sign = level === 'easy' ? '+' : '★';
            const q = {
                cat: 'weird', level, view: 'lines',
                lines: examples.map(([x, y]) => `${x} ${sign} ${y} = ${rule.f(x, y)}`),
                ask: `${a} ${sign} ${b} = ?`,
                why: [{k: 'why_weird', v: {rule: `a ${sign} b = ${rule.s}`}}],
            };
            return numberOptions(r, q, right, [a + b, a * b, right + 1, right - 1]);
        }
    }

    // ---------- nonsense: only the reasoning counts ----------
    const WORDS = ['bliz', 'glop', 'murf', 'trox', 'plim', 'drak', 'florb', 'quib', 'vonk', 'grub', 'nerp', 'tunk'];
    const SUBJECTS = ['Zog', 'Pim', 'Nub', 'Kex', 'Mop', 'Tiz'];
    const YES = 'yes', NO = 'no', UNKNOWN = 'unknown';

    // [statements, question, answer]: a, b, c are invented words and x an invented name.
    const FORMS = {
        easy: [
            [[['all', 'a', 'b'], ['is', 'x', 'a']], ['q_is', 'x', 'b'], YES],
            [[['none', 'a', 'b'], ['is', 'x', 'a']], ['q_is', 'x', 'b'], NO],
            [[['all', 'a', 'b'], ['is', 'x', 'b']], ['q_is', 'x', 'a'], UNKNOWN],
        ],
        medium: [
            [[['all', 'a', 'b'], ['all', 'b', 'c']], ['q_all', 'a', 'c'], YES],
            [[['all', 'a', 'b'], ['none', 'b', 'c']], ['q_can', 'a', 'c'], NO],
            [[['all', 'a', 'b'], ['not', 'x', 'b']], ['q_is', 'x', 'a'], NO],
            [[['all', 'a', 'b'], ['all', 'c', 'b']], ['q_all', 'a', 'c'], UNKNOWN],
        ],
        hard: [
            [[['some', 'a', 'b'], ['all', 'b', 'c']], ['q_some', 'a', 'c'], YES],
            [[['some', 'a', 'b'], ['some', 'b', 'c']], ['q_some', 'a', 'c'], UNKNOWN],
            [[['all', 'a', 'b'], ['all', 'b', 'c'], ['not', 'x', 'c']], ['q_is', 'x', 'a'], NO],
            [[['none', 'a', 'b'], ['all', 'c', 'a']], ['q_can', 'c', 'b'], NO],
            [[['all', 'a', 'b'], ['some', 'c', 'a']], ['q_some', 'c', 'b'], YES],
            [[['some', 'a', 'b'], ['none', 'b', 'c']], ['q_all', 'a', 'c'], NO],
        ],
        expert: [
            [[['all', 'a', 'b'], ['all', 'b', 'c'], ['all', 'c', 'd'], ['not', 'x', 'd']], ['q_is', 'x', 'a'], NO],
            [[['all', 'a', 'b'], ['none', 'b', 'c'], ['all', 'd', 'c']], ['q_can', 'a', 'd'], NO],
            [[['some', 'a', 'b'], ['all', 'b', 'c'], ['none', 'c', 'd']], ['q_all', 'a', 'd'], NO],
            [[['all', 'a', 'b'], ['some', 'b', 'c'], ['all', 'c', 'd']], ['q_some', 'a', 'd'], UNKNOWN],
            [[['none', 'a', 'b'], ['some', 'c', 'a'], ['all', 'b', 'd']], ['q_all', 'c', 'b'], NO],
            [[['all', 'a', 'b'], ['all', 'c', 'b'], ['none', 'a', 'd'], ['is', 'x', 'b']], ['q_is', 'x', 'a'], UNKNOWN],
            [[['some', 'a', 'b'], ['all', 'a', 'c'], ['all', 'b', 'd']], ['q_some', 'c', 'd'], YES],
        ],
    };

    const FORM_D = {easy: [.26, .3, .46], medium: [.58, .62, .66, .72], hard: [1.02, 1.1, 1, .95, 1, 1.15], expert: [1.4, 1.45, 1.6, 1.7, 1.55, 1.75, 1.8]};

    function makeNonsense(r, level, form) {
        const [statements, ask, answer] = form;
        const [a, b, c, d] = r.shuffle(WORDS);
        const names = {a, b, c, d, x: r.pick(SUBJECTS)};
        const line = ([k, p, q]) => ({k: 'nz_' + k, v: {p: names[p], q: names[q]}});
        const q = {
            cat: 'nonsense', level, view: 'facts', words: true,
            facts: r.shuffle(statements.map(line)), ask: line(ask),
            why: [{k: 'why_nz_' + answer}],
        };
        const list = [YES, NO, UNKNOWN].map(k => ({k}));
        q.options = list;
        q.answer = [YES, NO, UNKNOWN].indexOf(answer);
        return q;
    }

    // ---------- riddles (riddles.js) ----------
    function riddleQuestion(r, riddle) {
        const order = r.shuffle([0, 1, 2, 3]);
        return {
            view: 'riddle', id: riddle.id,
            t: riddle.custom ? riddle.t : undefined, // a saved game must survive the deletion of a question from the admin panel
            options: order.map(i => riddle.o ? riddle.o[i] : {r: i}),
            answer: order.indexOf(0), // in riddles.js the right option always comes first
            why: [{riddle: riddle.id}],
        };
    }

    // ---------- the patterns ----------
    const PATTERNS = [];
    const pattern = (id, cat, d, make) => {
        PATTERNS.push({id, cat, d, make});
    };

    pattern('sign-easy', 'sign', .07, r => signOne(r, 'easy'));
    pattern('sign-power', 'sign', .4, r => signOne(r, 'medium'));
    pattern('sign-two', 'sign', .9, signTwo);
    pattern('sign-three', 'sign', 1.55, signThree);
    ['easy', 'medium', 'hard', 'expert'].forEach((tier, n) => {
        SEQUENCES[tier].forEach((make, i) => pattern(`seq-${tier}-${i}`, 'sequence', SEQUENCE_D[tier][i], r => makeSequence(r, tier, make)));
        PROPERTIES[tier].forEach((make, i) => pattern(`odd-${tier}-${i}`, 'oddone', ODD_D[tier][i], r => makeOddOne(r, tier, make)));
        WEIRD[tier].forEach((make, i) => pattern(`weird-${tier}-${i}`, 'weird', WEIRD_D[tier][i], r => makeWeird(r, tier, make)));
        FORMS[tier].forEach((form, i) => pattern(`nz-${tier}-${i}`, 'nonsense', FORM_D[tier][i], r => makeNonsense(r, tier, form)));
        pattern('fruit-' + tier, 'symbols', [.2, .55, .92, 1.35][n], r => makeSymbols(r, tier, false));
        pattern('fruit2-' + tier, 'symbols', [.32, .72, 1.12, 1.68][n], r => makeSymbols(r, tier, true));
        pattern('order-' + tier, 'order', [.18, .48, .82, 1.3][n], r => makeOrder(r, tier));
        pattern('order2-' + tier, 'order', [.3, .66, 1.06, 1.62][n], r => makeOrder(r, tier));
    });

    /** Riddles without their own d get one from the tier they were written for, spread over its range. */
    function riddlePatterns() {
        const count = {}, seen = {};
        const list = (root.ThinkHopRiddles || []);
        list.forEach(x => {
            const lvl = x.level === 'expert' ? 'hard' : (x.level || 'easy');
            count[lvl] = (count[lvl] || 0) + 1;
        });
        const tierBase = lvl => {
            const i = TIERS.indexOf(lvl);
            return i >= 0 ? Math.min(3, i) : 1;
        };
        const spread = x => {
            const lvl = x.level === 'expert' ? 'hard' : (x.level || 'easy');
            seen[lvl] = (seen[lvl] || 0) + 1;
            const b = tierBase(lvl);
            return Math.round((b * .30 + .03 + .24 * seen[lvl] / count[lvl]) * 100) / 100;
        };
        const riddles = list.map(x => ({riddle: x, d: x.d === undefined ? spread(x) : x.d}));
        return [...riddles, ...customQuestions.map(x => ({riddle: x, d: x.d === undefined ? tierBase(x.level) * .30 + .15 : +x.d}))]
            .map(({riddle, d}) => ({id: riddle.id, cat: 'riddle', d, fixed: true, make: r => riddleQuestion(r, riddle)}));
    }

    let catalogue = null;

    /** Every pattern, with the difficulties changed in the admin panel and auto-scaled from legacy <= 2.0 to 0.00 – 9.99. */
    function allPatterns() {
        if (!catalogue) {
            catalogue = [...PATTERNS, ...riddlePatterns()].map(p => {
                let d = settings.difficulty[p.id] !== undefined ? +settings.difficulty[p.id] : p.d;
                if (d <= 2.0) d = Math.round(d * 500) / 100;
                d = Math.max(0, Math.min(MAX_D, d));
                return Object.assign({}, p, {d});
            });
        }
        return catalogue;
    }

    const catWeight = cat => {
        if (settings.catWeights && settings.catWeights[cat] !== undefined) {
            const w = +settings.catWeights[cat];
            return isNaN(w) ? 1.0 : Math.max(0, Math.min(1, w));
        }
        return 1.0;
    };

    const usable = () => allPatterns().filter(p => !settings.disabledCats.includes(p.cat) && catWeight(p.cat) > 0 && !settings.disabledRiddles.includes(p.id));

    /** Seconds to answer: more for the harder questions and for those with more to read. */
    function timeFor(q) {
        const reading = q.view === 'riddle' || q.pk ? 12 : (q.view === 'grid' ? 15 : 0);
        return Math.min(120, Math.round(28 + q.d * 4.8 + reading + (q.facts ? q.facts.length * 5 : 0) + (q.lines ? q.lines.length * 2 : 0)));
    }

    /** Same content, whatever the order of the options: that is what must not come back. */
    const contentOf = q => JSON.stringify([q.nums, q.result, q.items, q.lines, q.ask, q.grid, q.pv, q.gearsData, q.lasersData, q.cubesData, q.pipesData, q.facts && q.facts.map(f => JSON.stringify(f)).sort(), q.options.map(o => JSON.stringify(o)).sort()]);
    const sigOf = (id, q) => id + '|' + (q ? hashSeed(contentOf(q)).toString(36) : '0');

    function build(p, seed) {
        const q = p.make(rng(seed));
        q.pid = p.id;
        q.cat = p.cat;
        q.d = p.d;
        q.level = tierOf(p.d);
        q.limit = timeFor(q);
        q.pics = q.options.every(o => typeof o === 'string' && !/[A-Za-z0-9]/.test(o)); // pictures: shown bigger
        q.sig = sigOf(p.id, p.fixed ? null : q);
        return q;
    }

    /** One question of a pattern, for the tests and the admin panel. */
    const make = (id, seed) => build(allPatterns().find(p => p.id === id), seed);

    /** Types are drawn before patterns, with admin-configured probability (0.0 – 1.0) compared to peers. */
    const weight = cat => (cat === 'riddle' ? 3 : (MATH.includes(cat) ? .6 : 1.4)) * catWeight(cat);

    function drawCat(r, cats) {
        const eligible = cats.filter(c => weight(c) > 0);
        if (!eligible.length) return cats[cats.length - 1];
        const total = eligible.reduce((s, c) => s + weight(c), 0);
        if (total <= 0) return eligible[0];
        let x = r.next() * total;
        for (const c of eligible) {
            x -= weight(c);
            if (x <= 0) return c;
        }
        return eligible[eligible.length - 1];
    }

    /**
     * The question closest to `target` that is not in ctx: {seen, recent, pids, cats, math, mathMax}.
     * The window opens only as far as needed; riddles stay near their difficulty, the generated patterns never end.
     */
    function pick(r, target, ctx) {
        const all = usable();
        const list = all.length ? all : [PATTERNS[0]];
        for (const w of [.15, .30, .50, .75, 1.25, 2.25, 10.0]) {
            const pool = list.filter(p => Math.abs(p.d - target) <= w && !ctx.pids.has(p.id)
                && !(p.fixed && (target >= 6.0 || w > 1.25 || ctx.seen.has(sigOf(p.id))))
                && !(p.cat === 'trap' && target >= 6.0)
                && !(ctx.math >= ctx.mathMax && MATH.includes(p.cat)));
            const fresh = pool.filter(p => !ctx.recent.has(p.id));
            // Do not reuse recently played patterns when widening the window slightly finds a fresh one
            const usableNow = fresh.length ? fresh : (w >= 2.25 ? pool : []);
            const cats = [...new Set(usableNow.map(p => p.cat))];
            const newCats = cats.filter(c => !ctx.cats.has(c));
            const veryFreshCats = newCats.filter(c => !ctx.recentCats || !ctx.recentCats.has(c));
            // A type already used in this game comes back only when a wider window has nothing else to offer
            const choice = veryFreshCats.length ? veryFreshCats : (newCats.length ? (w <= .50 ? [] : newCats) : (w < 2.25 ? [] : cats));
            for (let tries = 0; choice.length && tries < 16; tries++) {
                const cat = drawCat(r, choice);
                const p = r.pick(usableNow.filter(x => x.cat === cat));
                const q = build(p, r.int(0, 2147483647));
                if (ctx.seen.has(q.sig)) continue;
                ctx.pids.add(p.id);
                ctx.cats.add(cat);
                ctx.seen.add(q.sig);
                if (MATH.includes(cat)) ctx.math++;
                return q;
            }
        }
        return build(r.pick(list), r.int(0, 2147483647)); // everything was seen: better an old question than none
    }

    function context(seen, mathMax) {
        const list = seen || [];
        const byId = Object.fromEntries(allPatterns().map(p => [p.id, p.cat]));
        return {
            seen: new Set(list),
            recent: new Set(list.slice(-90).map(s => s.split('|')[0])),
            recentCats: new Set(list.slice(-6).map(s => byId[s.split('|')[0]]).filter(Boolean)),
            pids: new Set(), cats: new Set(), math: 0, mathMax,
        };
    }

    const ascending = list => list.sort((a, b) => a.d - b.d);

    const dayText = (d) => {
        const x = d || new Date();
        return x.getFullYear() + '-' + String(x.getMonth() + 1).padStart(2, '0') + '-' + String(x.getDate()).padStart(2, '0');
    };

    /** The difficulty asked for by each question of a level: from 0.40 to 9.95 across 1000 levels. */
    function stageTargets(stage) {
        const n = settings.levelLength;
        return Array.from({length: n}, (_, i) => Math.round((.40 + 9.55 * Math.pow(((stage - 1) * n + i) / (STAGES * n - 1), .85)) * 100) / 100);
    }

    function stageQuestions(stage, seed, seen) {
        const r = rng(seed);
        const ctx = context(seen, Math.max(1, Math.floor(settings.levelLength / 4)));
        return ascending(stageTargets(stage).map(target => pick(r, target, ctx)));
    }

    function dailyQuestions(day) {
        const r = rng(hashSeed('thinkhop-' + day));
        const ctx = context([], 2);
        return ascending(Array.from({length: DAILY_LENGTH}, (_, i) => pick(r, Math.round((.75 + i * .95) * 100) / 100, ctx)));
    }

    function difficultyQuestions(tierKey, seed, seen) {
        const conf = DIFFICULTY_MODES[tierKey] || DIFFICULTY_MODES.beginner;
        const r = rng(seed);
        const ctx = context(seen, 2);
        return ascending(conf.targets.map(target => pick(r, target, ctx)));
    }

    /** New game: `stage` is the level for mode 'level' or diffKey for 'difficulty'; `seen` are the questions asked so far. */
    function newGame(mode, stage, seed, seen, day) {
        const isDiff = mode === 'difficulty';
        const diffKey = isDiff ? (typeof stage === 'string' ? stage : 'beginner') : null;
        const game = {
            v: 2, uid: uid(), mode, stage: mode === 'level' ? stage : 0, diffKey, day: day || dayText(),
            index: 0, score: 0, spent: 0, correct: 0, streak: 0, bestStreak: 0, lives: mode === 'survival' ? LIVES : 0,
            seconds: 0, clock: 0, cats: {}, answered: false, picked: -1, removed: [],
            seed, questions: [],
        };
        if (mode === 'level') game.questions = stageQuestions(stage, seed, seen);
        else if (mode === 'daily') game.questions = dailyQuestions(game.day);
        else if (isDiff) game.questions = difficultyQuestions(diffKey, seed, seen);
        else game.questions = [survivalQuestion(game, seen)];
        return game;
    }

    function survivalQuestion(game, seen) {
        const n = game.questions.length;
        const r = rng((game.seed + n * 7919) >>> 0);
        const ctx = context([...(seen || []), ...game.questions.map(q => q.sig)], 1);
        const last = game.questions.slice(-8);
        last.forEach(q => ctx.pids.add(q.pid));
        last.slice(-3).forEach(q => ctx.cats.add(q.cat));
        ctx.math = last.slice(-3).filter(q => MATH.includes(q.cat)).length;
        return pick(r, Math.min(MAX_D, Math.round((.60 + n * .35) * 100) / 100), ctx);
    }

    function uid() {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let out = '';
        for (let i = 0; i < 16; i++) out += chars[Math.floor(Math.random() * chars.length)];
        return out;
    }

    /**
     * Points of a right answer: from 100 to about 400 according to d, up to half more for speed and 10% more for each
     * right answer in a row (at most 50%).
     */
    function points(q, seconds, streak) {
        const base = 100 + q.d * 30;
        const speed = Math.max(0, 1 - seconds / q.limit) * base / 2;
        const bonus = base * Math.min(.5, .1 * Math.max(0, streak - 1));
        return Math.round((base + speed + bonus) / 5) * 5;
    }

    /** `picked` is -1 when the time ran out: it counts as a wrong answer. */
    function answer(game, picked, seconds) {
        const q = game.questions[game.index];
        const right = picked === q.answer;
        const cat = game.cats[q.cat] || (game.cats[q.cat] = [0, 0]);
        cat[1]++;
        game.answered = true;
        game.picked = picked;
        game.seconds += seconds;
        game.gained = 0;
        if (right) {
            cat[0]++;
            game.correct++;
            game.streak++;
            game.bestStreak = Math.max(game.bestStreak, game.streak);
            game.gained = points(q, seconds, game.streak);
            game.score += game.gained;
        } else {
            game.streak = 0;
            if (game.mode === 'survival') game.lives--;
        }
        return right;
    }

    const isOver = game => game.mode === 'survival' ? game.lives <= 0 : game.index >= game.questions.length - 1;

    function next(game, seen) {
        if (game.mode === 'survival') game.questions.push(survivalQuestion(game, seen));
        game.index++;
        game.answered = false;
        game.picked = -1;
        game.removed = [];
        game.clock = 0;
    }

    const hintCost = game => settings.hintCost[game.questions[game.index].level] || 100;

    /** Spends points to hide wrong options until two are left (one for the questions with three options). */
    function hint(game, balance) {
        const q = game.questions[game.index];
        if (game.answered || game.removed.length || balance < hintCost(game)) return false;
        const wrong = q.options.map((o, i) => i).filter(i => i !== q.answer);
        game.removed = rng(game.seed + game.index).shuffle(wrong).slice(0, Math.max(1, q.options.length - 2));
        game.spent += hintCost(game);
        return true;
    }

    /** 3 stars from 90% of right answers, 2 from 70%, 1 from 50%: at least 1 star passes a level. */
    const stars = game => {
        const share = game.correct / (game.index + 1);
        return share >= .9 ? 3 : (share >= .7 ? 2 : (share >= .5 ? 1 : 0));
    };

    /** The summary kept for the statistics, on the device and on the account. */
    const summary = game => ({
        uid: game.uid, mode: game.mode, stage: game.stage, diffKey: game.diffKey || null, stars: stars(game), score: game.score, spent: game.spent, correct: game.correct,
        total: game.index + 1, seconds: Math.round(game.seconds), cats: game.cats, day: game.day,
    });

    function addDays(text, n) {
        const [y, m, d] = text.split('-').map(Number);
        return dayText(new Date(y, m - 1, d + n));
    }

    /** Statistics from the list of finished games. */
    function stats(games, today) {
        const out = {games: games.length, points: 0, balance: START_POINTS, correct: 0, total: 0, seconds: 0, best: {}, cats: {}, stages: {}, unlocked: 1, streak: 0};
        const days = new Set();
        games.forEach(g => {
            out.points += g.score;
            out.balance += g.score - (g.spent || 0);
            out.correct += g.correct;
            out.total += g.total;
            out.seconds += g.seconds;
            out.best[g.mode] = Math.max(out.best[g.mode] || 0, g.score);
            if (g.mode === 'difficulty' && g.diffKey) {
                out.best['difficulty_' + g.diffKey] = Math.max(out.best['difficulty_' + g.diffKey] || 0, g.score);
            }
            if (g.mode === 'level' && g.stars > 0) {
                out.stages[g.stage] = Math.max(out.stages[g.stage] || 0, g.stars);
                out.unlocked = Math.max(out.unlocked, Math.min(STAGES, g.stage + 1));
            }
            Object.entries(g.cats || {}).forEach(([c, v]) => {
                const s = out.cats[c] || (out.cats[c] = [0, 0]);
                s[0] += v[0];
                s[1] += v[1];
            });
            if (g.mode === 'daily') days.add(g.day);
        });
        const now = today || dayText();
        // The streak is still alive until today's challenge is missed
        let day = days.has(now) ? now : addDays(now, -1);
        while (days.has(day)) {
            out.streak++;
            day = addDays(day, -1);
        }
        out.dailyDone = days.has(now);
        return out;
    }

    const worldTier = w => tierOf(stageTargets(Math.max(1, Math.min(STAGES, (w - 1) * WORLD + 5)))[2]);

    root.ThinkHopEngine = {
        TIERS, CATS, MATH, MAX_D, STAGES, WORLD, LIVES, START_POINTS, WORDS, NAMES, settings, DIFFICULTY_MODES,
        configure, rng, evaluate, tierOf, worldTier, pattern, allPatterns, make, withOptions, numberOptions, orderings, stageTargets, newGame, answer, next, hint, hintCost, isOver, stars, summary, stats, points, dayText, addDays, difficultyQuestions,
    };
})(typeof window !== 'undefined' ? window : globalThis);
