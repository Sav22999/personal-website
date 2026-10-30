// Chess rules engine. Squares are 0..63, index = row * 8 + col, row 0 = rank 8 (top, black side).
// Pieces: uppercase = white (P N B R Q K), lowercase = black. Mirrored on the server in api/engine.php.
const Chess = (() => {
    const START_FEN = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
    const FILES = 'abcdefgh';

    const KNIGHT = [[-2, -1], [-2, 1], [-1, -2], [-1, 2], [1, -2], [1, 2], [2, -1], [2, 1]];
    const KING = [[-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]];
    const BISHOP_DIRS = [[-1, -1], [-1, 1], [1, -1], [1, 1]];
    const ROOK_DIRS = [[-1, 0], [1, 0], [0, -1], [0, 1]];

    const isWhite = (p) => p !== null && p === p.toUpperCase();
    const colorOf = (p) => (p === null ? null : isWhite(p) ? 'w' : 'b');
    const row = (sq) => sq >> 3;
    const col = (sq) => sq & 7;
    const sqName = (sq) => FILES[col(sq)] + (8 - row(sq));
    const sqIndex = (name) => (8 - parseInt(name[1], 10)) * 8 + FILES.indexOf(name[0]);
    const onBoard = (r, c) => r >= 0 && r < 8 && c >= 0 && c < 8;

    function parseFEN(fen) {
        const [placement, turn, castling, ep, half, full] = fen.trim().split(/\s+/);
        const board = new Array(64).fill(null);
        let sq = 0;
        for (const ch of placement) {
            if (ch === '/') continue;
            if (/\d/.test(ch)) sq += parseInt(ch, 10);
            else board[sq++] = ch;
        }
        return {
            board,
            turn: turn || 'w',
            castling: castling && castling !== '-' ? castling : '',
            ep: ep && ep !== '-' ? sqIndex(ep) : -1,
            half: parseInt(half || '0', 10),
            full: parseInt(full || '1', 10),
        };
    }

    function toFEN(s) {
        let out = '';
        for (let r = 0; r < 8; r++) {
            let empty = 0;
            for (let c = 0; c < 8; c++) {
                const p = s.board[r * 8 + c];
                if (p === null) empty++;
                else { if (empty) { out += empty; empty = 0; } out += p; }
            }
            if (empty) out += empty;
            if (r < 7) out += '/';
        }
        return `${out} ${s.turn} ${s.castling || '-'} ${s.ep >= 0 ? sqName(s.ep) : '-'} ${s.half} ${s.full}`;
    }

    // Position identity for repetition: placement, side to move, castling rights and en passant square.
    const positionKey = (s) => toFEN(s).split(' ').slice(0, 4).join(' ');

    function findKing(s, color) {
        const k = color === 'w' ? 'K' : 'k';
        return s.board.indexOf(k);
    }

    function isAttacked(s, sq, by) {
        const r = row(sq), c = col(sq), b = s.board;
        // Pawns
        const pr = by === 'w' ? r + 1 : r - 1;
        const pawn = by === 'w' ? 'P' : 'p';
        for (const dc of [-1, 1]) if (onBoard(pr, c + dc) && b[pr * 8 + c + dc] === pawn) return true;
        // Knights
        const knight = by === 'w' ? 'N' : 'n';
        for (const [dr, dc] of KNIGHT) if (onBoard(r + dr, c + dc) && b[(r + dr) * 8 + c + dc] === knight) return true;
        // King
        const king = by === 'w' ? 'K' : 'k';
        for (const [dr, dc] of KING) if (onBoard(r + dr, c + dc) && b[(r + dr) * 8 + c + dc] === king) return true;
        // Sliders
        const bq = by === 'w' ? ['B', 'Q'] : ['b', 'q'];
        const rq = by === 'w' ? ['R', 'Q'] : ['r', 'q'];
        for (const [dirs, set] of [[BISHOP_DIRS, bq], [ROOK_DIRS, rq]]) {
            for (const [dr, dc] of dirs) {
                let rr = r + dr, cc = c + dc;
                while (onBoard(rr, cc)) {
                    const p = b[rr * 8 + cc];
                    if (p !== null) { if (set.includes(p)) return true; break; }
                    rr += dr; cc += dc;
                }
            }
        }
        return false;
    }

    const inCheck = (s, color) => isAttacked(s, findKing(s, color), color === 'w' ? 'b' : 'w');

    function pseudoMoves(s) {
        const moves = [];
        const b = s.board, me = s.turn, them = me === 'w' ? 'b' : 'w';
        const add = (from, to, extra = {}) => moves.push({ from, to, piece: b[from], captured: b[to], ...extra });

        for (let from = 0; from < 64; from++) {
            const p = b[from];
            if (p === null || colorOf(p) !== me) continue;
            const r = row(from), c = col(from), type = p.toLowerCase();

            if (type === 'p') {
                const dir = me === 'w' ? -1 : 1;
                const startRow = me === 'w' ? 6 : 1;
                const lastRow = me === 'w' ? 0 : 7;
                const pushPromo = (to, extra = {}) => {
                    if (row(to) === lastRow) for (const promo of 'qrbn') add(from, to, { ...extra, promo });
                    else add(from, to, extra);
                };
                const one = (r + dir) * 8 + c;
                if (onBoard(r + dir, c) && b[one] === null) {
                    pushPromo(one);
                    const two = (r + 2 * dir) * 8 + c;
                    if (r === startRow && b[two] === null) add(from, two, { double: true });
                }
                for (const dc of [-1, 1]) {
                    if (!onBoard(r + dir, c + dc)) continue;
                    const to = (r + dir) * 8 + c + dc;
                    if (b[to] !== null && colorOf(b[to]) === them) pushPromo(to);
                    else if (to === s.ep) add(from, to, { ep: true, captured: me === 'w' ? 'p' : 'P' });
                }
            } else if (type === 'n' || type === 'k') {
                for (const [dr, dc] of type === 'n' ? KNIGHT : KING) {
                    if (!onBoard(r + dr, c + dc)) continue;
                    const to = (r + dr) * 8 + c + dc;
                    if (b[to] === null || colorOf(b[to]) === them) add(from, to);
                }
                if (type === 'k') addCastling(s, from, add);
            } else {
                const dirs = type === 'b' ? BISHOP_DIRS : type === 'r' ? ROOK_DIRS : BISHOP_DIRS.concat(ROOK_DIRS);
                for (const [dr, dc] of dirs) {
                    let rr = r + dr, cc = c + dc;
                    while (onBoard(rr, cc)) {
                        const to = rr * 8 + cc;
                        if (b[to] === null) add(from, to);
                        else { if (colorOf(b[to]) === them) add(from, to); break; }
                        rr += dr; cc += dc;
                    }
                }
            }
        }
        return moves;
    }

    function addCastling(s, from, add) {
        const me = s.turn, them = me === 'w' ? 'b' : 'w', b = s.board;
        const home = me === 'w' ? 60 : 4;
        if (from !== home || isAttacked(s, home, them)) return;
        const [kSide, qSide] = me === 'w' ? ['K', 'Q'] : ['k', 'q'];
        const rook = me === 'w' ? 'R' : 'r';
        if (s.castling.includes(kSide) && b[home + 1] === null && b[home + 2] === null && b[home + 3] === rook
            && !isAttacked(s, home + 1, them) && !isAttacked(s, home + 2, them)) {
            add(home, home + 2, { castle: 'k' });
        }
        if (s.castling.includes(qSide) && b[home - 1] === null && b[home - 2] === null && b[home - 3] === null && b[home - 4] === rook
            && !isAttacked(s, home - 1, them) && !isAttacked(s, home - 2, them)) {
            add(home, home - 2, { castle: 'q' });
        }
    }

    function applyMove(s, m) {
        const b = s.board.slice();
        const me = s.turn;
        let piece = b[m.from];
        b[m.from] = null;
        if (m.ep) b[m.to + (me === 'w' ? 8 : -8)] = null;
        if (m.promo) piece = me === 'w' ? m.promo.toUpperCase() : m.promo;
        b[m.to] = piece;
        if (m.castle === 'k') { b[m.from + 1] = b[m.from + 3]; b[m.from + 3] = null; }
        if (m.castle === 'q') { b[m.from - 1] = b[m.from - 4]; b[m.from - 4] = null; }

        let castling = s.castling;
        const strip = (chars) => { for (const ch of chars) castling = castling.replace(ch, ''); };
        if (m.piece === 'K') strip('KQ');
        if (m.piece === 'k') strip('kq');
        for (const sq of [m.from, m.to]) {
            if (sq === 63) strip('K');
            if (sq === 56) strip('Q');
            if (sq === 7) strip('k');
            if (sq === 0) strip('q');
        }

        const isPawn = m.piece.toLowerCase() === 'p';
        return {
            board: b,
            turn: me === 'w' ? 'b' : 'w',
            castling,
            ep: m.double ? (m.from + m.to) / 2 : -1,
            half: isPawn || m.captured ? 0 : s.half + 1,
            full: me === 'b' ? s.full + 1 : s.full,
        };
    }

    function legalMoves(s) {
        return pseudoMoves(s).filter(m => !inCheck(applyMove(s, m), s.turn));
    }

    const toUci = (m) => sqName(m.from) + sqName(m.to) + (m.promo || '');

    function toSAN(s, m, legal = legalMoves(s)) {
        let san;
        if (m.castle) san = m.castle === 'k' ? 'O-O' : 'O-O-O';
        else {
            const type = m.piece.toUpperCase();
            const capture = m.captured !== null && m.captured !== undefined;
            if (type === 'P') {
                san = (capture ? FILES[col(m.from)] + 'x' : '') + sqName(m.to) + (m.promo ? '=' + m.promo.toUpperCase() : '');
            } else {
                const rivals = legal.filter(o => o.to === m.to && o.from !== m.from && o.piece === m.piece);
                let dis = '';
                if (rivals.length) {
                    const sameFile = rivals.some(o => col(o.from) === col(m.from));
                    const sameRank = rivals.some(o => row(o.from) === row(m.from));
                    if (!sameFile) dis = FILES[col(m.from)];
                    else if (!sameRank) dis = String(8 - row(m.from));
                    else dis = sqName(m.from);
                }
                san = type + dis + (capture ? 'x' : '') + sqName(m.to);
            }
        }
        const next = applyMove(s, m);
        if (inCheck(next, next.turn)) san += legalMoves(next).length ? '+' : '#';
        return san;
    }

    function insufficientMaterial(s) {
        const pieces = [];
        for (let sq = 0; sq < 64; sq++) {
            const p = s.board[sq];
            if (p && p.toLowerCase() !== 'k') pieces.push({ p: p.toLowerCase(), sq });
        }
        if (pieces.length === 0) return true;
        if (pieces.length === 1 && (pieces[0].p === 'b' || pieces[0].p === 'n')) return true;
        // Only bishops, all on squares of the same colour.
        if (pieces.every(x => x.p === 'b')) {
            const shade = (sq) => (row(sq) + col(sq)) % 2;
            return pieces.every(x => shade(x.sq) === shade(pieces[0].sq));
        }
        return false;
    }

    // keys: position keys of every position reached so far, including the current one.
    function outcome(s, keys = []) {
        const legal = legalMoves(s);
        if (!legal.length) {
            if (inCheck(s, s.turn)) return { over: true, result: s.turn === 'w' ? '0-1' : '1-0', reason: 'checkmate' };
            return { over: true, result: '1/2-1/2', reason: 'stalemate' };
        }
        if (insufficientMaterial(s)) return { over: true, result: '1/2-1/2', reason: 'insufficient' };
        if (s.half >= 100) return { over: true, result: '1/2-1/2', reason: 'fifty' };
        const key = positionKey(s);
        if (keys.filter(k => k === key).length >= 3) return { over: true, result: '1/2-1/2', reason: 'repetition' };
        return { over: false };
    }

    function perft(s, depth) {
        if (depth === 0) return 1;
        let n = 0;
        for (const m of legalMoves(s)) n += depth === 1 ? 1 : perft(applyMove(s, m), depth - 1);
        return n;
    }

    // A playable game: current state, SAN history and the positions needed for repetition draws.
    class Game {
        constructor(fen = START_FEN) {
            this.state = parseFEN(fen);
            this.history = [];
            this.keys = [positionKey(this.state)];
        }
        get turn() { return this.state.turn; }
        get fen() { return toFEN(this.state); }
        legal() { return legalMoves(this.state); }
        movesFrom(sq) { return this.legal().filter(m => m.from === sq); }
        inCheck() { return inCheck(this.state, this.state.turn); }
        outcome() { return outcome(this.state, this.keys); }
        move(uci) {
            const legal = this.legal();
            const m = legal.find(x => toUci(x) === uci);
            if (!m) return null;
            const san = toSAN(this.state, m, legal);
            this.state = applyMove(this.state, m);
            this.keys.push(positionKey(this.state));
            const entry = { uci, san, from: m.from, to: m.to, captured: m.captured || null, piece: m.piece, ep: !!m.ep };
            this.history.push(entry);
            return entry;
        }
        // Rebuild from a list of UCI moves (used when the server sends the game).
        static fromMoves(uciList, fen = START_FEN) {
            const g = new Game(fen);
            for (const u of uciList) if (!g.move(u)) break;
            return g;
        }
    }

    return { START_FEN, parseFEN, toFEN, positionKey, legalMoves, applyMove, toUci, toSAN, inCheck, isAttacked, insufficientMaterial, outcome, perft, sqName, sqIndex, colorOf, isWhite, row, col, Game };
})();
