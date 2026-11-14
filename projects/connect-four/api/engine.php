<?php
// Connect Four server-side validation and state engine.
// Grid: 6 rows (0 = top, 5 = bottom), 7 columns (0 = left, 6 = right).
// Cell values: 0 = empty, 1 = player 1, 2 = player 2.

class ConnectFourEngine
{
    public const ROWS = 6;
    public const COLS = 7;

    public static function replay(array $moves, int $firstSlot = 1): ?array
    {
        $board = self::createBoard();
        $winner = 0;
        $winningLine = null;
        $totalMoves = count($moves);
        $lastMove = null;

        foreach ($moves as $ply => $col) {
            $col = (int)$col;
            if ($col < 0 || $col >= self::COLS) {
                return null; // Illegal column index
            }

            if ($winner !== 0) {
                return null; // Game already had a winner, cannot play further moves
            }

            // Find lowest available row
            $landedRow = -1;
            for ($r = self::ROWS - 1; $r >= 0; $r--) {
                if ($board[$r][$col] === 0) {
                    $landedRow = $r;
                    break;
                }
            }

            if ($landedRow === -1) {
                return null; // Column is already full
            }

            $currentSlot = ($ply % 2 === 0) ? $firstSlot : ($firstSlot === 1 ? 2 : 1);
            $board[$landedRow][$col] = $currentSlot;
            $lastMove = ['row' => $landedRow, 'col' => $col, 'slot' => $currentSlot];

            // Check if this move made a 4-in-a-row
            $line = self::checkWinAt($board, $landedRow, $col);
            if ($line !== null) {
                $winner = $currentSlot;
                $winningLine = $line;
            }
        }

        $isFull = ($totalMoves >= self::ROWS * self::COLS);

        return [
            'board' => $board,
            'winner' => $winner,
            'winning_line' => $winningLine,
            'is_full' => $isFull,
            'total_moves' => $totalMoves,
            'last_move' => $lastMove,
        ];
    }

    public static function createBoard(): array
    {
        return array_fill(0, self::ROWS, array_fill(0, self::COLS, 0));
    }

    public static function checkWinAt(array $board, int $row, int $col): ?array
    {
        $target = $board[$row][$col];
        if ($target === 0) return null;

        // 4 directions: [delta_r, delta_c]
        // Horizontal: [0, 1]
        // Vertical:   [1, 0]
        // Diagonal /: [-1, 1]
        // Diagonal \: [1, 1]
        $directions = [
            [[0, 1], [0, -1]],
            [[1, 0], [-1, 0]],
            [[-1, 1], [1, -1]],
            [[1, 1], [-1, -1]],
        ];

        foreach ($directions as $dirPair) {
            $line = [[$row, $col]];

            // Positive direction
            $dr = $dirPair[0][0];
            $dc = $dirPair[0][1];
            $r = $row + $dr;
            $c = $col + $dc;
            while ($r >= 0 && $r < self::ROWS && $c >= 0 && $c < self::COLS && $board[$r][$c] === $target) {
                $line[] = [$r, $c];
                $r += $dr;
                $c += $dc;
            }

            // Negative direction
            $dr = $dirPair[1][0];
            $dc = $dirPair[1][1];
            $r = $row + $dr;
            $c = $col + $dc;
            while ($r >= 0 && $r < self::ROWS && $c >= 0 && $c < self::COLS && $board[$r][$c] === $target) {
                array_unshift($line, [$r, $c]);
                $r += $dr;
                $c += $dc;
            }

            if (count($line) >= 4) {
                // Return exactly the 4 consecutive coordinates that connect
                return array_slice($line, 0, 4);
            }
        }

        return null;
    }

    public static function formatWinningLine(?array $line): ?string
    {
        if ($line === null) return null;
        $parts = [];
        foreach ($line as $pt) {
            $parts[] = $pt[0] . ',' . $pt[1];
        }
        return implode('-', $parts);
    }
}
