// Connect Four client-side rules engine.
// Grid: 6 rows (0 = top, 5 = bottom), 7 columns (0 = left, 6 = right).
// Cell values: 0 = empty, 1 = player 1, 2 = player 2.

const ConnectFour = (() => {
    const ROWS = 6;
    const COLS = 7;

    function createBoard() {
        const board = [];
        for (let r = 0; r < ROWS; r++) {
            board.push(new Array(COLS).fill(0));
        }
        return board;
    }

    function cloneBoard(b) {
        return b.map(row => row.slice());
    }

    function getLowestEmptyRow(b, col) {
        if (col < 0 || col >= COLS) return -1;
        for (let r = ROWS - 1; r >= 0; r--) {
            if (b[r][col] === 0) return r;
        }
        return -1;
    }

    function canDrop(b, col) {
        return col >= 0 && col < COLS && b[0][col] === 0;
    }

    function getLegalColumns(b) {
        const cols = [];
        // Center columns preferred for ordering
        const order = [3, 2, 4, 1, 5, 0, 6];
        for (const c of order) {
            if (b[0][c] === 0) cols.push(c);
        }
        return cols;
    }

    function dropPiece(b, col, player) {
        const row = getLowestEmptyRow(b, col);
        if (row === -1) return -1;
        b[row][col] = player;
        return row;
    }

    // Directions: horizontal, vertical, diagonal-up (/), diagonal-down (\)
    const DIRS = [
        [[0, 1], [0, -1]],
        [[1, 0], [-1, 0]],
        [[-1, 1], [1, -1]],
        [[1, 1], [-1, -1]],
    ];

    function checkWinAt(b, row, col) {
        const player = b[row][col];
        if (player === 0) return null;

        for (const [[dr1, dc1], [dr2, dc2]] of DIRS) {
            const line = [{r: row, c: col}];

            // Positive direction
            let r = row + dr1, c = col + dc1;
            while (r >= 0 && r < ROWS && c >= 0 && c < COLS && b[r][c] === player) {
                line.push({r, c});
                r += dr1;
                c += dc1;
            }

            // Negative direction
            r = row + dr2;
            c = col + dc2;
            while (r >= 0 && r < ROWS && c >= 0 && c < COLS && b[r][c] === player) {
                line.unshift({r, c});
                r += dr2;
                c += dc2;
            }

            if (line.length >= 4) {
                return line.slice(0, 4);
            }
        }
        return null;
    }

    function checkWin(b) {
        for (let r = 0; r < ROWS; r++) {
            for (let c = 0; c < COLS; c++) {
                if (b[r][c] !== 0) {
                    const line = checkWinAt(b, r, c);
                    if (line) {
                        return {winner: b[r][c], line};
                    }
                }
            }
        }
        return null;
    }

    function isBoardFull(b) {
        for (let c = 0; c < COLS; c++) {
            if (b[0][c] === 0) return false;
        }
        return true;
    }

    function replay(moves, firstPlayer = 1) {
        const b = createBoard();
        let winner = 0;
        let winningLine = null;
        let lastMove = null;

        for (let ply = 0; ply < moves.length; ply++) {
            const col = Number(moves[ply]);
            if (col < 0 || col >= COLS) return null;
            if (winner !== 0) return null;

            const player = (ply % 2 === 0) ? firstPlayer : (firstPlayer === 1 ? 2 : 1);
            const row = dropPiece(b, col, player);
            if (row === -1) return null;

            lastMove = {r: row, c: col, player};
            const line = checkWinAt(b, row, col);
            if (line) {
                winner = player;
                winningLine = line;
            }
        }

        const isFull = isBoardFull(b);
        return {
            board: b,
            winner,
            winningLine,
            isFull,
            totalMoves: moves.length,
            lastMove,
        };
    }

    return {
        ROWS,
        COLS,
        createBoard,
        cloneBoard,
        getLowestEmptyRow,
        canDrop,
        getLegalColumns,
        dropPiece,
        checkWinAt,
        checkWin,
        isBoardFull,
        replay,
    };
})();

if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConnectFour;
}
