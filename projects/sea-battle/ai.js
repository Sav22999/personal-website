// Sea Battle computer player. It only uses what a human shooter knows.
// knowledge: array of 100 values: 0 = unknown, 1 = water (miss or cleared), 2 = hit on a ship still afloat, 3 = sunk ship.
// remaining: lengths of the ships still afloat.
const SeaBattleAI = (() => {
    const N = SeaBattle.SIZE;
    const rc = (i) => [Math.floor(i / N), i % N];

    // Ships never touch: the cells diagonal to a hit are water
    function deduce(knowledge) {
        const k = knowledge.slice();
        for (let i = 0; i < N * N; i++) {
            if (k[i] !== 2) continue;
            const [r, c] = rc(i);
            [[-1, -1], [-1, 1], [1, -1], [1, 1]].forEach(([dr, dc]) => {
                if (SeaBattle.inside(r + dr, c + dc) && k[SeaBattle.idx(r + dr, c + dc)] === 0) k[SeaBattle.idx(r + dr, c + dc)] = 1;
            });
        }
        return k;
    }

    const unknownCells = (k) => k.map((v, i) => v === 0 ? i : -1).filter(i => i >= 0);
    const pick = (list, rand) => list[Math.floor(rand() * list.length)];

    // Cells next to the hits, along the line when two or more hits are in a row
    function targetCells(k) {
        const hits = k.map((v, i) => v === 2 ? i : -1).filter(i => i >= 0);
        if (!hits.length) return [];
        const out = new Set();
        hits.forEach(i => {
            const [r, c] = rc(i);
            const rowMate = hits.some(j => j !== i && Math.floor(j / N) === r && Math.abs(j % N - c) === 1);
            const colMate = hits.some(j => j !== i && j % N === c && Math.abs(Math.floor(j / N) - r) === 1);
            const dirs = rowMate ? [[0, -1], [0, 1]] : colMate ? [[-1, 0], [1, 0]] : [[0, -1], [0, 1], [-1, 0], [1, 0]];
            dirs.forEach(([dr, dc]) => {
                let rr = r + dr, cc = c + dc;
                // Walk past the other hits of the same line
                while (SeaBattle.inside(rr, cc) && k[SeaBattle.idx(rr, cc)] === 2) {
                    rr += dr;
                    cc += dc;
                }
                if (SeaBattle.inside(rr, cc) && k[SeaBattle.idx(rr, cc)] === 0) out.add(SeaBattle.idx(rr, cc));
            });
        });
        return [...out];
    }

    // How many ways the remaining ships can lie over each cell: hits make a placement far more likely
    function density(k, remaining) {
        const score = new Array(N * N).fill(0);
        const anyHit = k.includes(2);
        remaining.forEach(len => {
            for (let r = 0; r < N; r++) for (let c = 0; c < N; c++) {
                ['h', 'v'].forEach(d => {
                    const cells = SeaBattle.shipCells({r, c, d}, len);
                    if (!cells || cells.some(i => k[i] === 1 || k[i] === 3)) return;
                    // A ship never touches a sunk one
                    if (SeaBattle.around(cells).some(i => k[i] === 3)) return;
                    const hits = cells.filter(i => k[i] === 2).length;
                    if (anyHit && hits === 0) return;
                    const w = hits ? Math.pow(8, hits) : 1;
                    cells.forEach(i => {
                        if (k[i] === 0) score[i] += w;
                    });
                });
            }
        });
        return score;
    }

    function bestFromScores(score, k, rand) {
        let best = -1, list = [];
        score.forEach((v, i) => {
            if (k[i] !== 0) return;
            if (v > best) {
                best = v;
                list = [i];
            } else if (v === best) list.push(i);
        });
        return list.length ? pick(list, rand) : null;
    }

    function bestShot(knowledge, remaining, level = 'medium', rand = Math.random) {
        const raw = knowledge;
        const k = level === 'easy' ? raw : deduce(raw);
        const free = unknownCells(k);
        if (!free.length) return pick(unknownCells(raw), rand);

        if (level === 'easy') {
            const targets = targetCells(k);
            if (targets.length && rand() < 0.35) return pick(targets, rand);
            return pick(free, rand);
        }

        if (level === 'medium') {
            const targets = targetCells(k);
            if (targets.length) return pick(targets, rand);
            const min = Math.min(...remaining);
            const parity = free.filter(i => {
                const [r, c] = rc(i);
                return (r + c) % min === 0;
            });
            return pick(parity.length ? parity : free, rand);
        }

        const shot = bestFromScores(density(k, remaining), k, rand);
        return shot === null ? pick(free, rand) : shot;
    }

    // A fleet for the computer: random, but the hard level keeps away from the edges a little less predictably
    function placeFleet(level = 'medium', rand = Math.random) {
        if (level !== 'hard') return SeaBattle.randomFleet(rand);
        let best = null, bestScore = -1;
        for (let n = 0; n < 12; n++) {
            const fleet = SeaBattle.randomFleet(rand);
            // Prefer fleets whose ships are spread out and not all on the same orientation
            const dirs = new Set(fleet.map(s => s.d)).size;
            const rows = new Set(fleet.map(s => s.r)).size + new Set(fleet.map(s => s.c)).size;
            const score = dirs * 10 + rows + rand() * 4;
            if (score > bestScore) {
                bestScore = score;
                best = fleet;
            }
        }
        return best;
    }

    return {bestShot, placeFleet, deduce};
})();

if (typeof module !== 'undefined') module.exports = SeaBattleAI;
