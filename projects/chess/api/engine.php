<?php
// Server-side chess rules: a direct port of engine.js, so client and server always agree on legal moves.
// Squares are 0..63, index = row * 8 + col, row 0 = rank 8. Uppercase pieces are white.
final class ChessEngine
{
    const START_FEN = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
    const FILES = 'abcdefgh';
    const KNIGHT = [[-2, -1], [-2, 1], [-1, -2], [-1, 2], [1, -2], [1, 2], [2, -1], [2, 1]];
    const KING = [[-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]];
    const BISHOP_DIRS = [[-1, -1], [-1, 1], [1, -1], [1, 1]];
    const ROOK_DIRS = [[-1, 0], [1, 0], [0, -1], [0, 1]];

    private static function colorOf($p) { return $p === null ? null : (ctype_upper($p) ? 'w' : 'b'); }
    private static function onBoard($r, $c) { return $r >= 0 && $r < 8 && $c >= 0 && $c < 8; }
    public static function sqName($sq) { return self::FILES[$sq & 7] . (8 - ($sq >> 3)); }
    public static function sqIndex($name) { return (8 - (int)$name[1]) * 8 + strpos(self::FILES, $name[0]); }

    public static function parseFEN($fen)
    {
        $parts = preg_split('/\s+/', trim($fen));
        $board = array_fill(0, 64, null);
        $sq = 0;
        foreach (str_split($parts[0]) as $ch) {
            if ($ch === '/') continue;
            if (ctype_digit($ch)) $sq += (int)$ch;
            else $board[$sq++] = $ch;
        }
        return [
            'board' => $board,
            'turn' => $parts[1] ?? 'w',
            'castling' => isset($parts[2]) && $parts[2] !== '-' ? $parts[2] : '',
            'ep' => isset($parts[3]) && $parts[3] !== '-' ? self::sqIndex($parts[3]) : -1,
            'half' => (int)($parts[4] ?? 0),
            'full' => (int)($parts[5] ?? 1),
        ];
    }

    public static function toFEN($s)
    {
        $out = '';
        for ($r = 0; $r < 8; $r++) {
            $empty = 0;
            for ($c = 0; $c < 8; $c++) {
                $p = $s['board'][$r * 8 + $c];
                if ($p === null) { $empty++; continue; }
                if ($empty) { $out .= $empty; $empty = 0; }
                $out .= $p;
            }
            if ($empty) $out .= $empty;
            if ($r < 7) $out .= '/';
        }
        return $out . ' ' . $s['turn'] . ' ' . ($s['castling'] !== '' ? $s['castling'] : '-') . ' '
            . ($s['ep'] >= 0 ? self::sqName($s['ep']) : '-') . ' ' . $s['half'] . ' ' . $s['full'];
    }

    public static function positionKey($s)
    {
        return implode(' ', array_slice(explode(' ', self::toFEN($s)), 0, 4));
    }

    private static function findKing($s, $color)
    {
        return array_search($color === 'w' ? 'K' : 'k', $s['board'], true);
    }

    public static function isAttacked($s, $sq, $by)
    {
        $r = $sq >> 3; $c = $sq & 7; $b = $s['board'];
        $pr = $by === 'w' ? $r + 1 : $r - 1;
        $pawn = $by === 'w' ? 'P' : 'p';
        foreach ([-1, 1] as $dc) if (self::onBoard($pr, $c + $dc) && $b[$pr * 8 + $c + $dc] === $pawn) return true;
        $knight = $by === 'w' ? 'N' : 'n';
        foreach (self::KNIGHT as [$dr, $dc]) if (self::onBoard($r + $dr, $c + $dc) && $b[($r + $dr) * 8 + $c + $dc] === $knight) return true;
        $king = $by === 'w' ? 'K' : 'k';
        foreach (self::KING as [$dr, $dc]) if (self::onBoard($r + $dr, $c + $dc) && $b[($r + $dr) * 8 + $c + $dc] === $king) return true;
        $sets = [
            [self::BISHOP_DIRS, $by === 'w' ? ['B', 'Q'] : ['b', 'q']],
            [self::ROOK_DIRS, $by === 'w' ? ['R', 'Q'] : ['r', 'q']],
        ];
        foreach ($sets as [$dirs, $set]) {
            foreach ($dirs as [$dr, $dc]) {
                $rr = $r + $dr; $cc = $c + $dc;
                while (self::onBoard($rr, $cc)) {
                    $p = $b[$rr * 8 + $cc];
                    if ($p !== null) { if (in_array($p, $set, true)) return true; break; }
                    $rr += $dr; $cc += $dc;
                }
            }
        }
        return false;
    }

    public static function inCheck($s, $color)
    {
        return self::isAttacked($s, self::findKing($s, $color), $color === 'w' ? 'b' : 'w');
    }

    private static function pseudoMoves($s)
    {
        $moves = [];
        $b = $s['board']; $me = $s['turn']; $them = $me === 'w' ? 'b' : 'w';
        $add = function ($from, $to, $extra = []) use (&$moves, $b) {
            $moves[] = array_merge(['from' => $from, 'to' => $to, 'piece' => $b[$from], 'captured' => $b[$to], 'promo' => null, 'ep' => false, 'double' => false, 'castle' => null], $extra);
        };

        for ($from = 0; $from < 64; $from++) {
            $p = $b[$from];
            if ($p === null || self::colorOf($p) !== $me) continue;
            $r = $from >> 3; $c = $from & 7; $type = strtolower($p);

            if ($type === 'p') {
                $dir = $me === 'w' ? -1 : 1;
                $startRow = $me === 'w' ? 6 : 1;
                $lastRow = $me === 'w' ? 0 : 7;
                $pushPromo = function ($to, $extra = []) use ($add, $from, $lastRow) {
                    if (($to >> 3) === $lastRow) foreach (['q', 'r', 'b', 'n'] as $promo) $add($from, $to, array_merge($extra, ['promo' => $promo]));
                    else $add($from, $to, $extra);
                };
                $one = ($r + $dir) * 8 + $c;
                if (self::onBoard($r + $dir, $c) && $b[$one] === null) {
                    $pushPromo($one);
                    $two = ($r + 2 * $dir) * 8 + $c;
                    if ($r === $startRow && $b[$two] === null) $add($from, $two, ['double' => true]);
                }
                foreach ([-1, 1] as $dc) {
                    if (!self::onBoard($r + $dir, $c + $dc)) continue;
                    $to = ($r + $dir) * 8 + $c + $dc;
                    if ($b[$to] !== null && self::colorOf($b[$to]) === $them) $pushPromo($to);
                    elseif ($to === $s['ep']) $add($from, $to, ['ep' => true, 'captured' => $me === 'w' ? 'p' : 'P']);
                }
            } elseif ($type === 'n' || $type === 'k') {
                foreach ($type === 'n' ? self::KNIGHT : self::KING as [$dr, $dc]) {
                    if (!self::onBoard($r + $dr, $c + $dc)) continue;
                    $to = ($r + $dr) * 8 + $c + $dc;
                    if ($b[$to] === null || self::colorOf($b[$to]) === $them) $add($from, $to);
                }
                if ($type === 'k') self::addCastling($s, $from, $add);
            } else {
                $dirs = $type === 'b' ? self::BISHOP_DIRS : ($type === 'r' ? self::ROOK_DIRS : array_merge(self::BISHOP_DIRS, self::ROOK_DIRS));
                foreach ($dirs as [$dr, $dc]) {
                    $rr = $r + $dr; $cc = $c + $dc;
                    while (self::onBoard($rr, $cc)) {
                        $to = $rr * 8 + $cc;
                        if ($b[$to] === null) $add($from, $to);
                        else { if (self::colorOf($b[$to]) === $them) $add($from, $to); break; }
                        $rr += $dr; $cc += $dc;
                    }
                }
            }
        }
        return $moves;
    }

    private static function addCastling($s, $from, $add)
    {
        $me = $s['turn']; $them = $me === 'w' ? 'b' : 'w'; $b = $s['board'];
        $home = $me === 'w' ? 60 : 4;
        if ($from !== $home || self::isAttacked($s, $home, $them)) return;
        [$kSide, $qSide] = $me === 'w' ? ['K', 'Q'] : ['k', 'q'];
        $rook = $me === 'w' ? 'R' : 'r';
        if (strpos($s['castling'], $kSide) !== false && $b[$home + 1] === null && $b[$home + 2] === null && $b[$home + 3] === $rook
            && !self::isAttacked($s, $home + 1, $them) && !self::isAttacked($s, $home + 2, $them)) {
            $add($home, $home + 2, ['castle' => 'k']);
        }
        if (strpos($s['castling'], $qSide) !== false && $b[$home - 1] === null && $b[$home - 2] === null && $b[$home - 3] === null && $b[$home - 4] === $rook
            && !self::isAttacked($s, $home - 1, $them) && !self::isAttacked($s, $home - 2, $them)) {
            $add($home, $home - 2, ['castle' => 'q']);
        }
    }

    public static function applyMove($s, $m)
    {
        $b = $s['board'];
        $me = $s['turn'];
        $piece = $b[$m['from']];
        $b[$m['from']] = null;
        if ($m['ep']) $b[$m['to'] + ($me === 'w' ? 8 : -8)] = null;
        if ($m['promo']) $piece = $me === 'w' ? strtoupper($m['promo']) : $m['promo'];
        $b[$m['to']] = $piece;
        if ($m['castle'] === 'k') { $b[$m['from'] + 1] = $b[$m['from'] + 3]; $b[$m['from'] + 3] = null; }
        if ($m['castle'] === 'q') { $b[$m['from'] - 1] = $b[$m['from'] - 4]; $b[$m['from'] - 4] = null; }

        $castling = $s['castling'];
        $strip = function ($chars) use (&$castling) { $castling = str_replace(str_split($chars), '', $castling); };
        if ($m['piece'] === 'K') $strip('KQ');
        if ($m['piece'] === 'k') $strip('kq');
        foreach ([$m['from'], $m['to']] as $sq) {
            if ($sq === 63) $strip('K');
            if ($sq === 56) $strip('Q');
            if ($sq === 7) $strip('k');
            if ($sq === 0) $strip('q');
        }

        $isPawn = strtolower($m['piece']) === 'p';
        return [
            'board' => $b,
            'turn' => $me === 'w' ? 'b' : 'w',
            'castling' => $castling,
            'ep' => $m['double'] ? intdiv($m['from'] + $m['to'], 2) : -1,
            'half' => $isPawn || $m['captured'] !== null ? 0 : $s['half'] + 1,
            'full' => $me === 'b' ? $s['full'] + 1 : $s['full'],
        ];
    }

    public static function legalMoves($s)
    {
        return array_values(array_filter(self::pseudoMoves($s), fn($m) => !self::inCheck(self::applyMove($s, $m), $s['turn'])));
    }

    public static function toUci($m) { return self::sqName($m['from']) . self::sqName($m['to']) . ($m['promo'] ?? ''); }

    public static function toSAN($s, $m, $legal)
    {
        if ($m['castle']) {
            $san = $m['castle'] === 'k' ? 'O-O' : 'O-O-O';
        } else {
            $type = strtoupper($m['piece']);
            $capture = $m['captured'] !== null;
            if ($type === 'P') {
                $san = ($capture ? self::FILES[$m['from'] & 7] . 'x' : '') . self::sqName($m['to']) . ($m['promo'] ? '=' . strtoupper($m['promo']) : '');
            } else {
                $rivals = array_filter($legal, fn($o) => $o['to'] === $m['to'] && $o['from'] !== $m['from'] && $o['piece'] === $m['piece']);
                $dis = '';
                if ($rivals) {
                    $sameFile = (bool)array_filter($rivals, fn($o) => ($o['from'] & 7) === ($m['from'] & 7));
                    $sameRank = (bool)array_filter($rivals, fn($o) => ($o['from'] >> 3) === ($m['from'] >> 3));
                    if (!$sameFile) $dis = self::FILES[$m['from'] & 7];
                    elseif (!$sameRank) $dis = (string)(8 - ($m['from'] >> 3));
                    else $dis = self::sqName($m['from']);
                }
                $san = $type . $dis . ($capture ? 'x' : '') . self::sqName($m['to']);
            }
        }
        $next = self::applyMove($s, $m);
        if (self::inCheck($next, $next['turn'])) $san .= self::legalMoves($next) ? '+' : '#';
        return $san;
    }

    private static function insufficientMaterial($s)
    {
        $pieces = [];
        foreach ($s['board'] as $sq => $p) if ($p !== null && strtolower($p) !== 'k') $pieces[] = ['p' => strtolower($p), 'sq' => $sq];
        if (!$pieces) return true;
        if (count($pieces) === 1 && in_array($pieces[0]['p'], ['b', 'n'], true)) return true;
        $shade = fn($sq) => (($sq >> 3) + ($sq & 7)) % 2;
        foreach ($pieces as $x) if ($x['p'] !== 'b' || $shade($x['sq']) !== $shade($pieces[0]['sq'])) return false;
        return true;
    }

    // $keys: position keys of every position reached so far, including the current one.
    public static function outcome($s, $keys = [])
    {
        if (!self::legalMoves($s)) {
            if (self::inCheck($s, $s['turn'])) return ['over' => true, 'result' => $s['turn'] === 'w' ? '0-1' : '1-0', 'reason' => 'checkmate'];
            return ['over' => true, 'result' => '1/2-1/2', 'reason' => 'stalemate'];
        }
        if (self::insufficientMaterial($s)) return ['over' => true, 'result' => '1/2-1/2', 'reason' => 'insufficient'];
        if ($s['half'] >= 100) return ['over' => true, 'result' => '1/2-1/2', 'reason' => 'fifty'];
        $key = self::positionKey($s);
        if (count(array_filter($keys, fn($k) => $k === $key)) >= 3) return ['over' => true, 'result' => '1/2-1/2', 'reason' => 'repetition'];
        return ['over' => false];
    }

    public static function perft($s, $depth)
    {
        if ($depth === 0) return 1;
        $n = 0;
        foreach (self::legalMoves($s) as $m) $n += $depth === 1 ? 1 : self::perft(self::applyMove($s, $m), $depth - 1);
        return $n;
    }

    // Replays a list of UCI moves from the start; returns [state, keys, sanList] or null on an illegal move.
    public static function replay(array $uciMoves)
    {
        $s = self::parseFEN(self::START_FEN);
        $keys = [self::positionKey($s)];
        $sans = [];
        foreach ($uciMoves as $uci) {
            $legal = self::legalMoves($s);
            $m = null;
            foreach ($legal as $x) if (self::toUci($x) === $uci) { $m = $x; break; }
            if (!$m) return null;
            $sans[] = self::toSAN($s, $m, $legal);
            $s = self::applyMove($s, $m);
            $keys[] = self::positionKey($s);
        }
        return [$s, $keys, $sans];
    }
}
