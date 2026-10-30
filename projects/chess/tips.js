// Analysis behind the Tips feature: what a piece can do from its square, as plain facts the UI can phrase.
const Tips = (() => {
    const VALUE = { p: 1, n: 3, b: 3, r: 5, q: 9, k: 100 };
    const other = (c) => (c === 'w' ? 'b' : 'w');

    // Lets players inspect either side: the piece's side is treated as the one to move.
    function asMover(state, color) {
        return color === state.turn ? state : { ...state, turn: color, ep: -1 };
    }

    function isPinned(state, sq, color) {
        if (state.board[sq].toLowerCase() === 'k' || Chess.inCheck(state, color)) return false;
        const board = state.board.slice();
        board[sq] = null;
        return Chess.inCheck({ ...state, board }, color);
    }

    function analyze(state, sq) {
        const piece = state.board[sq];
        if (!piece) return null;
        const color = Chess.colorOf(piece);
        const enemy = other(color);
        const type = piece.toLowerCase();
        const s = asMover(state, color);
        const legal = Chess.legalMoves(s);

        // One entry per target square (a promotion is four moves to the same square).
        const bySquare = new Map();
        for (const m of legal) {
            if (m.from !== sq) continue;
            if (bySquare.has(m.to)) continue;
            const pick = m.promo ? legal.find(x => x.from === sq && x.to === m.to && (x.promo || '').toLowerCase() === 'q') || m : m;
            const next = Chess.applyMove(s, pick);
            const replies = Chess.legalMoves(next);
            const check = Chess.inCheck(next, next.turn);
            bySquare.set(m.to, {
                to: m.to,
                uci: Chess.toUci(pick),
                san: Chess.toSAN(s, pick, legal),
                captured: pick.captured || null,
                check,
                mate: check && replies.length === 0,
                // Unsafe only if the opponent really has a legal capture there (a pinned or checked defender doesn't count).
                unsafe: replies.some(r => r.to === m.to),
                special: pick.castle ? 'castle' : pick.ep ? 'ep' : pick.promo ? 'promo' : null,
            });
        }
        const targets = [...bySquare.values()];
        return {
            piece, type, color, sq, targets,
            inspectOnly: color !== state.turn,
            attacked: type === 'k' ? Chess.inCheck(state, color) : Chess.isAttacked(state, sq, enemy),
            defended: Chess.isAttacked(state, sq, color),
            pinned: isPinned(state, sq, color),
        };
    }

    // The side's pieces that are attacked and not defended, most valuable first.
    function hanging(state, color) {
        const out = [];
        for (let sq = 0; sq < 64; sq++) {
            const p = state.board[sq];
            if (!p || Chess.colorOf(p) !== color || p.toLowerCase() === 'k') continue;
            if (Chess.isAttacked(state, sq, other(color)) && !Chess.isAttacked(state, sq, color)) out.push({ sq, piece: p });
        }
        return out.sort((a, b) => VALUE[b.piece.toLowerCase()] - VALUE[a.piece.toLowerCase()]);
    }

    return { analyze, hanging, VALUE };
})();
