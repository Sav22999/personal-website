/* Sudoku engine: puzzle generator (unique solution), solver, candidates and logic hints.
 * The grid is an array of 81 numbers (0 = empty), index = row * 9 + col.
 */
(function () {
    'use strict';

    const ALL = 0x3FE; // bits 1..9
    const ROW = i => (i / 9) | 0;
    const COL = i => i % 9;
    const BOX = i => ((ROW(i) / 3) | 0) * 3 + ((COL(i) / 3) | 0);

    // Peers and units, computed once
    const UNITS = {row: [], col: [], box: []};
    for (let k = 0; k < 9; k++) {
        UNITS.row.push(Array.from({length: 9}, (_, c) => k * 9 + c));
        UNITS.col.push(Array.from({length: 9}, (_, r) => r * 9 + k));
        const br = ((k / 3) | 0) * 3, bc = (k % 3) * 3;
        UNITS.box.push(Array.from({length: 9}, (_, j) => (br + ((j / 3) | 0)) * 9 + bc + (j % 3)));
    }
    const PEERS = Array.from({length: 81}, (_, i) => {
        const s = new Set([...UNITS.row[ROW(i)], ...UNITS.col[COL(i)], ...UNITS.box[BOX(i)]]);
        s.delete(i);
        return [...s];
    });

    const bitCount = m => {
        let n = 0;
        while (m) {
            m &= m - 1;
            n++;
        }
        return n;
    };
    const digitsOf = m => {
        const out = [];
        for (let d = 1; d <= 9; d++) if (m & (1 << d)) out.push(d);
        return out;
    };

    function shuffle(a) {
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    /** Bitmask of the digits that can go in cell i. */
    function candidates(grid, i) {
        if (grid[i]) return 0;
        let used = 0;
        for (const p of PEERS[i]) used |= 1 << grid[p];
        return ALL & ~used;
    }

    /** Counts the solutions up to `limit` (backtracking, most constrained cell first). */
    function countSolutions(grid, limit) {
        const g = grid.slice();
        let count = 0;
        (function solve() {
            if (count >= limit) return;
            let best = -1, bestMask = 0, bestN = 10;
            for (let i = 0; i < 81; i++) {
                if (g[i]) continue;
                const m = candidates(g, i);
                const n = bitCount(m);
                if (n === 0) return;
                if (n < bestN) {
                    best = i;
                    bestMask = m;
                    bestN = n;
                    if (n === 1) break;
                }
            }
            if (best < 0) {
                count++;
                return;
            }
            for (const d of digitsOf(bestMask)) {
                g[best] = d;
                solve();
                if (count >= limit) return;
            }
            g[best] = 0;
        })();
        return count;
    }

    function solve(grid) {
        const g = grid.slice();
        const ok = (function rec() {
            let best = -1, bestMask = 0, bestN = 10;
            for (let i = 0; i < 81; i++) {
                if (g[i]) continue;
                const m = candidates(g, i);
                const n = bitCount(m);
                if (n === 0) return false;
                if (n < bestN) {
                    best = i;
                    bestMask = m;
                    bestN = n;
                    if (n === 1) break;
                }
            }
            if (best < 0) return true;
            for (const d of digitsOf(bestMask)) {
                g[best] = d;
                if (rec()) return true;
            }
            g[best] = 0;
            return false;
        })();
        return ok ? g : null;
    }

    function solvedGrid() {
        const g = new Array(81).fill(0);
        (function fill(i) {
            if (i === 81) return true;
            for (const d of shuffle(digitsOf(candidates(g, i)))) {
                g[i] = d;
                if (fill(i + 1)) return true;
            }
            g[i] = 0;
            return false;
        })(0);
        return g;
    }

    /** Can the puzzle be solved with singles only (naked + hidden)? Used to keep easy puzzles easy. */
    function solvableWithSingles(grid) {
        const g = grid.slice();
        for (; ;) {
            const step = findSingle(g);
            if (!step) return g.every(Boolean);
            g[step.index] = step.digit;
        }
    }

    const LEVELS = {
        easy: {clues: 40, singles: true},
        medium: {clues: 32, singles: true},
        hard: {clues: 27, singles: false},
        expert: {clues: 23, singles: false},
    };

    /** New puzzle: { puzzle, solution } with a unique solution. */
    function generate(level) {
        const cfg = LEVELS[level] || LEVELS.medium;
        for (let attempt = 0; attempt < 6; attempt++) {
            const solution = solvedGrid();
            const puzzle = solution.slice();
            let clues = 81;
            // Remove cells in symmetric pairs, keeping the solution unique
            for (const i of shuffle(Array.from({length: 41}, (_, k) => k))) {
                if (clues <= cfg.clues) break;
                const j = 80 - i;
                const a = puzzle[i], b = puzzle[j];
                puzzle[i] = 0;
                puzzle[j] = 0;
                const removed = i === j ? 1 : 2;
                if (countSolutions(puzzle, 2) !== 1 || (cfg.singles && !solvableWithSingles(puzzle))) {
                    puzzle[i] = a;
                    puzzle[j] = b;
                } else clues -= removed;
            }
            if (clues <= cfg.clues + 3 || attempt === 5) return {puzzle, solution};
        }
    }

    // ---------- Logic hints ----------

    /** Next "single" deduction: naked single (one candidate in a cell) or hidden single (one place in a unit). */
    function findSingle(grid) {
        for (let i = 0; i < 81; i++) {
            if (grid[i]) continue;
            const m = candidates(grid, i);
            if (bitCount(m) === 1) return {type: 'naked', index: i, digit: digitsOf(m)[0]};
        }
        for (const kind of ['box', 'row', 'col']) {
            for (let u = 0; u < 9; u++) {
                const cells = UNITS[kind][u];
                for (let d = 1; d <= 9; d++) {
                    if (cells.some(i => grid[i] === d)) continue;
                    const spots = cells.filter(i => !grid[i] && (candidates(grid, i) & (1 << d)));
                    if (spots.length === 1) return {
                        type: 'hidden',
                        unit: kind,
                        unitIndex: u,
                        index: spots[0],
                        digit: d,
                        cells
                    };
                }
            }
        }
        return null;
    }

    /** Cells whose value breaks the rules (same digit twice in a row, column or box). */
    function conflicts(grid) {
        const bad = new Set();
        for (let i = 0; i < 81; i++) {
            if (!grid[i]) continue;
            for (const p of PEERS[i]) if (grid[p] === grid[i]) {
                bad.add(i);
                bad.add(p);
            }
        }
        return bad;
    }

    window.SudokuEngine = {
        generate,
        solve,
        countSolutions,
        candidates,
        findSingle,
        conflicts,
        digitsOf,
        bitCount,
        PEERS,
        UNITS,
        ROW,
        COL,
        BOX,
        LEVELS
    };
})();
