// Connect Four AI engine with Easy, Medium, and Hard difficulty levels.
// Uses Minimax with Alpha-Beta pruning, heuristic positional evaluation, and move ordering.

const ConnectFourAI = (() => {
    const C4 = typeof ConnectFour !== 'undefined' ? ConnectFour : require('./engine.js');
    const ROWS = C4.ROWS;
    const COLS = C4.COLS;

    // Positional value weights: center column gives the most tactical control
    const COL_WEIGHTS = [1, 3, 5, 8, 5, 3, 1];

    function evaluateWindow(window, player, opponent) {
        let score = 0;
        let countPlayer = 0;
        let countOpponent = 0;
        let countEmpty = 0;

        for (let i = 0; i < 4; i++) {
            if (window[i] === player) countPlayer++;
            else if (window[i] === opponent) countOpponent++;
            else countEmpty++;
        }

        if (countPlayer === 4) return 100000;
        if (countPlayer === 3 && countEmpty === 1) score += 60;
        else if (countPlayer === 2 && countEmpty === 2) score += 8;

        if (countOpponent === 4) return -100000;
        if (countOpponent === 3 && countEmpty === 1) score -= 75;
        else if (countOpponent === 2 && countEmpty === 2) score -= 10;

        return score;
    }

    function evaluateBoard(board, player) {
        const opponent = player === 1 ? 2 : 1;
        let score = 0;

        // Center column control preference
        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS; c++) {
                if (board[r][c] === player) score += COL_WEIGHTS[c];
                else if (board[r][c] === opponent) score -= COL_WEIGHTS[c];
            }
        }

        // Horizontal windows
        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS - 3; c++) {
                const w = [board[r][c], board[r][c + 1], board[r][c + 2], board[r][c + 3]];
                score += evaluateWindow(w, player, opponent);
            }
        }

        // Vertical windows
        for (let c = 0; c < COLS; c++) {
            for (let r = 0; r < ROWS - 3; r++) {
                const w = [board[r][c], board[r + 1][c], board[r + 2][c], board[r + 3][c]];
                score += evaluateWindow(w, player, opponent);
            }
        }

        // Diagonal \ windows
        for (let r = 0; r < ROWS - 3; r++) {
            for (let c = 0; c < COLS - 3; c++) {
                const w = [board[r][c], board[r + 1][c + 1], board[r + 2][c + 2], board[r + 3][c + 3]];
                score += evaluateWindow(w, player, opponent);
            }
        }

        // Diagonal / windows
        for (let r = 3; r < ROWS; r++) {
            for (let c = 0; c < COLS - 3; c++) {
                const w = [board[r][c], board[r - 1][c + 1], board[r - 2][c + 2], board[r - 3][c + 3]];
                score += evaluateWindow(w, player, opponent);
            }
        }

        return score;
    }

    function isImmediateWin(board, col, player) {
        const r = C4.getLowestEmptyRow(board, col);
        if (r === -1) return false;
        board[r][col] = player;
        const win = C4.checkWinAt(board, r, col);
        board[r][col] = 0;
        return !!win;
    }

    function findImmediateWin(board, player) {
        const legal = C4.getLegalColumns(board);
        for (const col of legal) {
            if (isImmediateWin(board, col, player)) return col;
        }
        return -1;
    }

    function minimax(board, depth, alpha, beta, isMaximizing, player, opponent) {
        const legalCols = C4.getLegalColumns(board);

        // Terminal states
        if (depth === 0 || legalCols.length === 0) {
            return { score: evaluateBoard(board, player), col: null };
        }

        if (isMaximizing) {
            let maxScore = -Infinity;
            let bestCol = legalCols[0];

            for (const col of legalCols) {
                const r = C4.getLowestEmptyRow(board, col);
                board[r][col] = player;

                if (C4.checkWinAt(board, r, col)) {
                    board[r][col] = 0;
                    return { score: 100000 + depth * 1000, col };
                }

                const result = minimax(board, depth - 1, alpha, beta, false, player, opponent);
                board[r][col] = 0;

                if (result.score > maxScore) {
                    maxScore = result.score;
                    bestCol = col;
                }
                alpha = Math.max(alpha, maxScore);
                if (alpha >= beta) break; // Beta prune
            }
            return { score: maxScore, col: bestCol };
        } else {
            let minScore = Infinity;
            let bestCol = legalCols[0];

            for (const col of legalCols) {
                const r = C4.getLowestEmptyRow(board, col);
                board[r][col] = opponent;

                if (C4.checkWinAt(board, r, col)) {
                    board[r][col] = 0;
                    return { score: -100000 - depth * 1000, col };
                }

                const result = minimax(board, depth - 1, alpha, beta, true, player, opponent);
                board[r][col] = 0;

                if (result.score < minScore) {
                    minScore = result.score;
                    bestCol = col;
                }
                beta = Math.min(beta, minScore);
                if (alpha >= beta) break; // Alpha prune
            }
            return { score: minScore, col: bestCol };
        }
    }

    function bestMove(board, player, level = 'medium') {
        const legal = C4.getLegalColumns(board);
        if (legal.length === 0) return -1;
        if (legal.length === 1) return legal[0];

        const opponent = player === 1 ? 2 : 1;

        // 1. Easy Mode
        if (level === 'easy') {
            // 60% chance to take win, 50% chance to block
            if (Math.random() < 0.6) {
                const winCol = findImmediateWin(board, player);
                if (winCol !== -1) return winCol;
            }
            if (Math.random() < 0.5) {
                const blockCol = findImmediateWin(board, opponent);
                if (blockCol !== -1) return blockCol;
            }
            return legal[Math.floor(Math.random() * legal.length)];
        }

        // Always take an immediate win if available (Medium & Hard)
        const myWin = findImmediateWin(board, player);
        if (myWin !== -1) return myWin;

        // Always block immediate opponent win (Medium & Hard)
        const oppWin = findImmediateWin(board, opponent);
        if (oppWin !== -1) return oppWin;

        // 2. Medium Mode: Depth 4 search
        if (level === 'medium') {
            const depth = 4;
            const res = minimax(board, depth, -Infinity, Infinity, true, player, opponent);
            return res.col !== null ? res.col : legal[0];
        }

        // 3. Hard Mode: searches deeper as the board fills up and there are fewer moves to explore
        const emptyCells = legal.reduce((acc, c) => acc + (C4.getLowestEmptyRow(board, c) + 1), 0);
        const depth = emptyCells <= 16 ? 12 : (emptyCells <= 26 ? 10 : 8);

        const res = minimax(board, depth, -Infinity, Infinity, true, player, opponent);
        return res.col !== null ? res.col : legal[0];
    }

    return {
        bestMove,
        findImmediateWin,
    };
})();

if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConnectFourAI;
}
