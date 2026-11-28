<?php
// Prism Mine server-side rules: the same as engine.js (see the comments there).

class PrismMineEngine
{
    public const SIZE = 8;
    public const PORTS = 32;
    private const DIRS = [[-1, 0], [0, 1], [1, 0], [0, -1]];
    public const SHAPES = [
        'shard' => [[0, 0, 'SE']],
        'cube' => [[0, 0, 'S']],
        'wedge' => [[0, 0, 'S'], [0, 1, 'SW']],
        'slab' => [[0, 0, 'SE'], [0, 1, 'NW']],
        'pyramid' => [[0, 1, 'SE'], [1, 0, 'SE'], [1, 1, 'S']],
        'diamond' => [[0, 0, 'SE'], [0, 1, 'SW'], [1, 0, 'NE'], [1, 1, 'NW']],
        'rod' => [[0, 0, 'S'], [0, 1, 'S']],
        'peak' => [[0, 0, 'SE'], [0, 1, 'SW']],
        'corner' => [[0, 0, 'S'], [1, 0, 'S'], [1, 1, 'S']],
        'bridge' => [[0, 0, 'SE'], [0, 1, 'S'], [0, 2, 'SW']],
    ];
    public const COLORS = ['R', 'Y', 'B', 'W', 'C', 'K'];
    private const FILLED = ['NE' => [0, 1], 'SE' => [1, 2], 'SW' => [2, 3], 'NW' => [3, 0]];
    private const SLASH = ['NE' => '\\', 'SW' => '\\', 'SE' => '/', 'NW' => '/'];
    private const TURN = ['/' => [1 => 0, 0 => 1, 3 => 2, 2 => 3], '\\' => [1 => 2, 2 => 1, 3 => 0, 0 => 3]];
    private const ROTATE_TYPE = ['S' => 'S', 'NE' => 'SE', 'SE' => 'SW', 'SW' => 'NW', 'NW' => 'NE'];
    // More gems need more room: each level has its own size of mine (SIZE is the default)
    public const LEVELS = [
        'easy' => ['gems' => 3, 'size' => 6, 'colors' => ['R', 'Y', 'B'], 'extra' => null],
        'medium' => ['gems' => 4, 'size' => 8, 'colors' => ['R', 'Y', 'B', 'W'], 'extra' => null],
        'hard' => ['gems' => 5, 'size' => 10, 'colors' => ['R', 'Y', 'B', 'W'], 'extra' => ['C', 'K']],
    ];

    public static function sizeOf(string $level): int
    {
        return (self::LEVELS[$level] ?? self::LEVELS['medium'])['size'];
    }

    private static ?array $orientations = null;

    private static function normalize(array $cells): array
    {
        $minR = min(array_column($cells, 0));
        $minC = min(array_column($cells, 1));
        $out = array_map(fn($x) => [$x[0] - $minR, $x[1] - $minC, $x[2]], $cells);
        usort($out, fn($a, $b) => $a[0] <=> $b[0] ?: $a[1] <=> $b[1]);
        return $out;
    }

    private static function rotateCells(array $cells): array
    {
        $h = max(array_column($cells, 0)) + 1;
        return self::normalize(array_map(fn($x) => [$x[1], $h - 1 - $x[0], self::ROTATE_TYPE[$x[2]]], $cells));
    }

    public static function orientations(): array
    {
        if (self::$orientations === null) {
            self::$orientations = [];
            foreach (self::SHAPES as $key => $base) {
                $list = [];
                $cells = self::normalize($base);
                for ($i = 0; $i < 4; $i++) {
                    if (!in_array($cells, $list, true)) $list[] = $cells;
                    $cells = self::rotateCells($cells);
                }
                self::$orientations[$key] = $list;
            }
        }
        return self::$orientations;
    }

    public static function gemCells(array $gem, int $size = self::SIZE): ?array
    {
        $list = self::orientations()[$gem['shape']];
        $n = count($list);
        $out = [];
        foreach ($list[(($gem['rot'] % $n) + $n) % $n] as [$dr, $dc, $t]) {
            $r = $gem['r'] + $dr;
            $c = $gem['c'] + $dc;
            if ($r < 0 || $r >= $size || $c < 0 || $c >= $size) return null;
            $out[] = [$r, $c, $t];
        }
        return $out;
    }

    public static function buildMap(array $gems, int $size = self::SIZE): ?array
    {
        $map = array_fill(0, $size, array_fill(0, $size, null));
        foreach ($gems as $g => $gem) {
            $cells = self::gemCells($gem, $size);
            if ($cells === null) return null;
            foreach ($cells as [$r, $c, $t]) {
                if ($map[$r][$c] !== null) return null;
                $map[$r][$c] = ['t' => $t, 'g' => $g];
            }
        }
        return $map;
    }

    private static function portStart(int $p, int $size): array
    {
        $side = intdiv($p, $size);
        $k = $p % $size;
        if ($side === 0) return [0, $k, 2];
        if ($side === 1) return [$k, $size - 1, 3];
        if ($side === 2) return [$size - 1, $size - 1 - $k, 0];
        return [$size - 1 - $k, 0, 1];
    }

    private static function exitPort(int $r, int $c, int $d, int $size): int
    {
        if ($d === 0) return $c;
        if ($d === 1) return $size + $r;
        if ($d === 2) return 2 * $size + ($size - 1 - $c);
        return 3 * $size + ($size - 1 - $r);
    }

    // ['exit' => port or null, 'colors' => sorted letters]
    public static function trace(array $map, array $gems, int $port): array
    {
        $size = count($map); // the size of the mine is the size of the map
        [$r, $c, $d] = self::portStart($port, $size);
        $colors = [];
        for ($step = 0; $step < 600; $step++) {
            if ($r < 0 || $r >= $size || $c < 0 || $c >= $size) {
                ksort($colors);
                return ['exit' => self::exitPort($r - self::DIRS[$d][0], $c - self::DIRS[$d][1], $d, $size), 'colors' => implode('', array_keys($colors))];
            }
            $cell = $map[$r][$c];
            if ($cell !== null) {
                $color = $gems[$cell['g']]['color'];
                if ($color === 'K') return ['exit' => null, 'colors' => ''];
                $entering = ($d + 2) % 4;
                if ($cell['t'] === 'S' || in_array($entering, self::FILLED[$cell['t']], true)) $d = ($d + 2) % 4;
                else $d = self::TURN[self::SLASH[$cell['t']]][$d];
                if ($color !== 'C') $colors[$color] = true;
            }
            $r += self::DIRS[$d][0];
            $c += self::DIRS[$d][1];
        }
        return ['exit' => null, 'colors' => ''];
    }

    public static function signature(array $gems, int $size = self::SIZE): ?string
    {
        $map = self::buildMap($gems, $size);
        if ($map === null) return null;
        $out = [];
        for ($p = 0; $p < $size * 4; $p++) {
            $res = self::trace($map, $gems, $p);
            $out[] = ($res['exit'] === null ? 'x' : $res['exit']) . ':' . $res['colors'];
        }
        return implode('|', $out);
    }

    private static function gemSetKey(array $gems): string
    {
        $keys = array_map(fn($g) => $g['shape'] . $g['color'], $gems);
        sort($keys);
        return implode(',', $keys);
    }

    public static function sameSet(array $a, array $b): bool
    {
        return count($a) === count($b) && self::gemSetKey($a) === self::gemSetKey($b);
    }

    // The given gems (shape and colour) placed at random, without overlaps
    public static function randomLayout(array $set, int $size = self::SIZE): array
    {
        $orient = self::orientations();
        while (true) {
            $gems = [];
            foreach ($set as $g => $gem) {
                for ($tries = 0; $tries < 200; $tries++) {
                    $try = $gems;
                    $try[$g] = ['shape' => $gem['shape'], 'color' => $gem['color'], 'r' => random_int(0, $size - 1), 'c' => random_int(0, $size - 1), 'rot' => random_int(0, count($orient[$gem['shape']]) - 1)];
                    if (self::buildMap($try, $size) !== null) {
                        $gems = $try;
                        break;
                    }
                }
                if (count($gems) !== $g + 1) continue 2;
            }
            return $gems;
        }
    }

    public static function isCorrect(array $guess, array $hidden, int $size = self::SIZE): bool
    {
        if (count($guess) !== count($hidden) || self::gemSetKey($guess) !== self::gemSetKey($hidden)) return false;
        $sig = self::signature($guess, $size);
        return $sig !== null && $sig === self::signature($hidden, $size);
    }

    public static function generate(string $level): array
    {
        $cfg = self::LEVELS[$level] ?? self::LEVELS['medium'];
        $size = $cfg['size'];
        $orient = self::orientations();
        while (true) {
            $shapes = array_keys(self::SHAPES);
            shuffle($shapes);
            $shapes = array_slice($shapes, 0, $cfg['gems']);
            $colors = $cfg['colors'];
            shuffle($colors);
            if ($cfg['extra']) {
                $colors = array_slice($colors, 0, $cfg['gems'] - 1);
                $colors[] = $cfg['extra'][random_int(0, count($cfg['extra']) - 1)];
            }
            $gems = [];
            $ok = true;
            foreach ($shapes as $g => $shape) {
                $placed = false;
                for ($tries = 0; $tries < 200 && !$placed; $tries++) {
                    $try = $gems;
                    $try[$g] = ['shape' => $shape, 'color' => $colors[$g], 'r' => random_int(0, $size - 1), 'c' => random_int(0, $size - 1), 'rot' => random_int(0, count($orient[$shape]) - 1)];
                    if (self::buildMap($try, $size) !== null) {
                        $gems = $try;
                        $placed = true;
                    }
                }
                if (!$placed) {
                    $ok = false;
                    break;
                }
            }
            if (!$ok) continue;
            // Every gem must change at least one answer
            $full = self::signature($gems, $size);
            $allSeen = true;
            foreach (array_keys($gems) as $g) {
                $others = $gems;
                unset($others[$g]);
                if (self::signature(array_values($others), $size) === $full) {
                    $allSeen = false;
                    break;
                }
            }
            if ($allSeen) return $gems;
        }
    }

    public static function encode(array $gems): string
    {
        return implode(';', array_map(fn($g) => "{$g['shape']},{$g['color']},{$g['r']},{$g['c']},{$g['rot']}", $gems));
    }

    public static function decode(?string $text, int $expected = 0, int $size = self::SIZE): ?array
    {
        if (!is_string($text) || $text === '') return null;
        $parts = explode(';', $text);
        if (count($parts) > 6 || ($expected && count($parts) !== $expected)) return null;
        $gems = [];
        foreach ($parts as $part) {
            if (!preg_match('/^([a-z]+),([RYBWCK]),(\d),(\d),([0-3])$/', $part, $m) || !isset(self::SHAPES[$m[1]])) return null;
            $gems[] = ['shape' => $m[1], 'color' => $m[2], 'r' => (int)$m[3], 'c' => (int)$m[4], 'rot' => (int)$m[5]];
        }
        return self::buildMap($gems, $size) === null ? null : $gems;
    }
}
