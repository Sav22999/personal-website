// Computer opponent: alpha-beta search over Chess engine moves, with material + piece-square evaluation.
const ChessAI = (() => {
    const VALUE = { p: 100, n: 320, b: 330, r: 500, q: 900, k: 0 };

    // Piece-square tables from White's point of view, row 0 = rank 8 (simplified evaluation function).
    const PST = {
        p: [0, 0, 0, 0, 0, 0, 0, 0, 50, 50, 50, 50, 50, 50, 50, 50, 10, 10, 20, 30, 30, 20, 10, 10, 5, 5, 10, 25, 25, 10, 5, 5, 0, 0, 0, 20, 20, 0, 0, 0, 5, -5, -10, 0, 0, -10, -5, 5, 5, 10, 10, -20, -20, 10, 10, 5, 0, 0, 0, 0, 0, 0, 0, 0],
        n: [-50, -40, -30, -30, -30, -30, -40, -50, -40, -20, 0, 0, 0, 0, -20, -40, -30, 0, 10, 15, 15, 10, 0, -30, -30, 5, 15, 20, 20, 15, 5, -30, -30, 0, 15, 20, 20, 15, 0, -30, -30, 5, 10, 15, 15, 10, 5, -30, -40, -20, 0, 5, 5, 0, -20, -40, -50, -40, -30, -30, -30, -30, -40, -50],
        b: [-20, -10, -10, -10, -10, -10, -10, -20, -10, 0, 0, 0, 0, 0, 0, -10, -10, 0, 5, 10, 10, 5, 0, -10, -10, 5, 5, 10, 10, 5, 5, -10, -10, 0, 10, 10, 10, 10, 0, -10, -10, 10, 10, 10, 10, 10, 10, -10, -10, 5, 0, 0, 0, 0, 5, -10, -20, -10, -10, -10, -10, -10, -10, -20],
        r: [0, 0, 0, 0, 0, 0, 0, 0, 5, 10, 10, 10, 10, 10, 10, 5, -5, 0, 0, 0, 0, 0, 0, -5, -5, 0, 0, 0, 0, 0, 0, -5, -5, 0, 0, 0, 0, 0, 0, -5, -5, 0, 0, 0, 0, 0, 0, -5, -5, 0, 0, 0, 0, 0, 0, -5, 0, 0, 0, 5, 5, 0, 0, 0],
        q: [-20, -10, -10, -5, -5, -10, -10, -20, -10, 0, 0, 0, 0, 0, 0, -10, -10, 0, 5, 5, 5, 5, 0, -10, -5, 0, 5, 5, 5, 5, 0, -5, 0, 0, 5, 5, 5, 5, 0, -5, -10, 5, 5, 5, 5, 5, 0, -10, -10, 0, 5, 0, 0, 0, 0, -10, -20, -10, -10, -5, -5, -10, -10, -20],
        k: [-30, -40, -40, -50, -50, -40, -40, -30, -30, -40, -40, -50, -50, -40, -40, -30, -30, -40, -40, -50, -50, -40, -40, -30, -30, -40, -40, -50, -50, -40, -40, -30, -20, -30, -30, -40, -40, -30, -30, -20, -10, -20, -20, -20, -20, -20, -20, -10, 20, 20, 0, 0, 0, 0, 20, 20, 20, 30, 10, 0, 0, 10, 30, 20],
        kEnd: [-50, -40, -30, -20, -20, -30, -40, -50, -30, -20, -10, 0, 0, -10, -20, -30, -30, -10, 20, 30, 30, 20, -10, -30, -30, -10, 30, 40, 40, 30, -10, -30, -30, -10, 30, 40, 40, 30, -10, -30, -30, -10, 20, 30, 30, 20, -10, -30, -30, -30, 0, 0, 0, 0, -30, -30, -50, -30, -30, -30, -30, -30, -30, -50],
    };

    const LEVELS = {
        easy: { depth: 1, noise: 140, timeMs: 300, blunder: 0.2 },
        medium: { depth: 2, noise: 25, timeMs: 900, blunder: 0 },
        hard: { depth: 4, noise: 0, timeMs: 1800, blunder: 0 },
    };

    const MATE = 100000;

    function evaluate(s) {
        let score = 0, queens = 0, minors = 0;
        for (const p of s.board) if (p) { const t = p.toLowerCase(); if (t === 'q') queens++; if (t === 'n' || t === 'b' || t === 'r') minors++; }
        const endgame = queens === 0 || (queens <= 2 && minors <= 2);
        for (let sq = 0; sq < 64; sq++) {
            const p = s.board[sq];
            if (!p) continue;
            const t = p.toLowerCase();
            const white = p !== t;
            const idx = white ? sq : (7 - (sq >> 3)) * 8 + (sq & 7);
            const table = t === 'k' && endgame ? PST.kEnd : PST[t];
            const v = VALUE[t] + table[idx];
            score += white ? v : -v;
        }
        return s.turn === 'w' ? score : -score;
    }

    // Captures first, most valuable victim / least valuable attacker.
    function ordered(moves) {
        return moves.map(m => ({ m, k: (m.captured ? 10 * VALUE[m.captured.toLowerCase()] - VALUE[m.piece.toLowerCase()] : 0) + (m.promo ? 800 : 0) }))
            .sort((a, b) => b.k - a.k).map(x => x.m);
    }

    function quiesce(s, alpha, beta, ctx, depth) {
        const stand = evaluate(s);
        if (stand >= beta) return beta;
        if (stand > alpha) alpha = stand;
        if (depth <= 0 || ctx.stop()) return alpha;
        for (const m of ordered(Chess.legalMoves(s).filter(x => x.captured || x.promo))) {
            const score = -quiesce(Chess.applyMove(s, m), -beta, -alpha, ctx, depth - 1);
            if (score >= beta) return beta;
            if (score > alpha) alpha = score;
        }
        return alpha;
    }

    function negamax(s, depth, alpha, beta, ply, ctx) {
        const moves = Chess.legalMoves(s);
        if (!moves.length) return Chess.inCheck(s, s.turn) ? -MATE + ply : 0;
        if (s.half >= 100) return 0;
        if (depth === 0) return ctx.quiescence ? quiesce(s, alpha, beta, ctx, 4) : evaluate(s);
        for (const m of ordered(moves)) {
            if (ctx.stop()) break;
            const score = -negamax(Chess.applyMove(s, m), depth - 1, -beta, -alpha, ply + 1, ctx);
            if (score >= beta) return beta;
            if (score > alpha) alpha = score;
        }
        return alpha;
    }

    // Iterative deepening; returns the best move (UCI) found within the level's time budget.
    function bestMove(state, level = 'medium') {
        const cfg = LEVELS[level] || LEVELS.medium;
        const start = performance.now();
        const ctx = { stop: () => performance.now() - start > cfg.timeMs, quiescence: level !== 'easy' };
        let root = ordered(Chess.legalMoves(state));
        if (!root.length) return null;
        // Easy level occasionally plays a random move, like a beginner would.
        if (cfg.blunder && Math.random() < cfg.blunder) return Chess.toUci(root[Math.floor(Math.random() * root.length)]);
        let best = root[0];

        for (let depth = 1; depth <= cfg.depth; depth++) {
            const scored = [];
            let alpha = -Infinity;
            for (const m of root) {
                // With noise every root move needs its exact score (a bounded one could be
                // lifted above the real best move by the noise); without noise a bound is enough.
                const bound = cfg.noise ? Infinity : -alpha;
                const score = -negamax(Chess.applyMove(state, m), depth - 1, -Infinity, bound, 1, ctx);
                scored.push({ m, score: score + (cfg.noise ? (Math.random() * 2 - 1) * cfg.noise : 0) });
                if (score > alpha) alpha = score;
                if (ctx.stop() && depth > 1) break;
            }
            if (ctx.stop() && depth > 1 && scored.length < root.length) break;
            scored.sort((a, b) => b.score - a.score);
            best = scored[0].m;
            root = scored.map(x => x.m).concat(root.filter(m => !scored.some(x => x.m === m)));
            if (ctx.stop()) break;
        }
        return Chess.toUci(best);
    }

    return { bestMove, evaluate, LEVELS };
})();
