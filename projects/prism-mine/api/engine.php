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
    ];
    public const COLORS = ['R', 'Y', 'B', 'W', 'C', 'K'];
    private const FILLED = ['NE' => [0, 1], 'SE' => [1, 2], 'SW' => [2, 3], 'NW' => [3, 0]];
    private const SLASH = ['NE' => '\\', 'SW' => '\\', 'SE' => '/', 'NW' => '/'];
    private const TURN = ['/' => [1 => 0, 0 => 1, 3 => 2, 2 => 3], '\\' => [1 => 2, 2 => 1, 3 => 0, 0 => 3]];
    private const ROTATE_TYPE = ['S' => 'S', 'NE' => 'SE', 'SE' => 'SW', 'SW' => 'NW', 'NW' => 'NE'];
    public const LEVELS = [
        'easy' => ['gems' => 3, 'colors' => ['R', 'Y', 'B'], 'extra' => null],
        'medium' => ['gems' => 4, 'colors' => ['R', 'Y', 'B', 'W'], 'extra' => null],
        'hard' => ['gems' => 5, 'colors' => ['R', 'Y', 'B', 'W'], 'extra' => ['C', 'K']],
    ];

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

    public static function gemCells(array $gem): ?array
    {
        $list = self::orientations()[$gem['shape']];
        $n = count($list);
        $out = [];
        foreach ($list[(($gem['rot'] % $n) + $n) % $n] as [$dr, $dc, $t]) {
            $r = $gem['r'] + $dr;
            $c = $gem['c'] + $dc;
            if ($r < 0 || $r >= self::SIZE || $c < 0 || $c >= self::SIZE) return null;
            $out[] = [$r, $c, $t];
        }
        return $out;
    }

    public static function buildMap(array $gems): ?array
    {
        $map = array_fill(0, self::SIZE, array_fill(0, self::SIZE, null));
        foreach ($gems as $g => $gem) {
            $cells = self::gemCells($gem);
            if ($cells === null) return null;
            foreach ($cells as [$r, $c, $t]) {
                if ($map[$r][$c] !== null) return null;
                $map[$r][$c] = ['t' => $t, 'g' => $g];
            }
        }
        return $map;
    }

    private static function portStart(int $p): array
    {
        $side = intdiv($p, self::SIZE);
        $k = $p % self::SIZE;
        if ($side === 0) return [0, $k, 2];
        if ($side === 1) return [$k, self::SIZE - 1, 3];
        if ($side === 2) return [self::SIZE - 1, self::SIZE - 1 - $k, 0];
        return [self::SIZE - 1 - $k, 0, 1];
    }

    private static function exitPort(int $r, int $c, int $d): int
    {
        if ($d === 0) return $c;
        if ($d === 1) return self::SIZE + $r;
        if ($d === 2) return 2 * self::SIZE + (self::SIZE - 1 - $c);
        return 3 * self::SIZE + (self::SIZE - 1 - $r);
    }

    // ['exit' => port or null, 'colors' => sorted letters]
    public static function trace(array $map, array $gems, int $port): array
    {
        [$r, $c, $d] = self::portStart($port);
        $colors = [];
        for ($step = 0; $step < 400; $step++) {
            if ($r < 0 || $r >= self::SIZE || $c < 0 || $c >= self::SIZE) {
                ksort($colors);
                return ['exit' => self::exitPort($r - self::DIRS[$d][0], $c - self::DIRS[$d][1], $d), 'colors' => implode('', array_keys($colors))];
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

    public static function signature(array $gems): ?string
    {
        $map = self::buildMap($gems);
        if ($map === null) return null;
        $out = [];
        for ($p = 0; $p < self::PORTS; $p++) {
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

    public static function isCorrect(array $guess, array $hidden): bool
    {
        if (count($guess) !== count($hidden) || self::gemSetKey($guess) !== self::gemSetKey($hidden)) return false;
        $sig = self::signature($guess);
        return $sig !== null && $sig === self::signature($hidden);
    }

    public static function generate(string $level): array
    {
        $cfg = self::LEVELS[$level] ?? self::LEVELS['medium'];
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
                    $try[$g] = ['shape' => $shape, 'color' => $colors[$g], 'r' => random_int(0, self::SIZE - 1), 'c' => random_int(0, self::SIZE - 1), 'rot' => random_int(0, count($orient[$shape]) - 1)];
                    if (self::buildMap($try) !== null) {
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
            $full = self::signature($gems);
            $allSeen = true;
            foreach (array_keys($gems) as $g) {
                $others = $gems;
                unset($others[$g]);
                if (self::signature(array_values($others)) === $full) {
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

    public static function decode(?string $text, int $expected = 0): ?array
    {
        if (!is_string($text) || $text === '') return null;
        $parts = explode(';', $text);
        if (count($parts) > 6 || ($expected && count($parts) !== $expected)) return null;
        $gems = [];
        foreach ($parts as $part) {
            if (!preg_match('/^([a-z]+),([RYBWCK]),([0-7]),([0-7]),([0-3])$/', $part, $m) || !isset(self::SHAPES[$m[1]])) return null;
            $gems[] = ['shape' => $m[1], 'color' => $m[2], 'r' => (int)$m[3], 'c' => (int)$m[4], 'rot' => (int)$m[5]];
        }
        return self::buildMap($gems) === null ? null : $gems;
    }
}
