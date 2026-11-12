<?php
// Sea Battle server-side rules: the same as engine.js.
// Grid 10 x 10, a cell is row * 10 + column. A fleet is stored as "r,c,d;r,c,d;..." in the FLEET order.
// Shots are stored as "slot:cell slot:cell ..." in the order they were fired; "slot:x" is a turn whose time ran out.

class SeaBattleEngine
{
    public const SIZE = 10;
    public const FLEET = [5, 4, 3, 3, 2];

    public static function inside(int $r, int $c): bool
    {
        return $r >= 0 && $r < self::SIZE && $c >= 0 && $c < self::SIZE;
    }

    public static function shipCells(array $ship, int $len): ?array
    {
        $out = [];
        for ($k = 0; $k < $len; $k++) {
            $r = $ship['r'] + ($ship['d'] === 'v' ? $k : 0);
            $c = $ship['c'] + ($ship['d'] === 'h' ? $k : 0);
            if (!self::inside($r, $c)) return null;
            $out[] = $r * self::SIZE + $c;
        }
        return $out;
    }

    public static function around(array $cells): array
    {
        $own = array_flip($cells);
        $ring = [];
        foreach ($cells as $i) {
            $r = intdiv($i, self::SIZE);
            $c = $i % self::SIZE;
            for ($dr = -1; $dr <= 1; $dr++) {
                for ($dc = -1; $dc <= 1; $dc++) {
                    $j = ($r + $dr) * self::SIZE + $c + $dc;
                    if (self::inside($r + $dr, $c + $dc) && !isset($own[$j])) $ring[$j] = true;
                }
            }
        }
        return array_keys($ring);
    }

    // Cell -> ship index (-1 = water), or null when the fleet breaks a rule
    public static function fleetMap(array $ships): ?array
    {
        if (count($ships) !== count(self::FLEET)) return null;
        $map = array_fill(0, self::SIZE * self::SIZE, -1);
        foreach (self::FLEET as $s => $len) {
            $cells = self::shipCells($ships[$s], $len);
            if ($cells === null) return null;
            foreach ($cells as $i) if ($map[$i] !== -1) return null;
            foreach (self::around($cells) as $i) if ($map[$i] !== -1) return null;
            foreach ($cells as $i) $map[$i] = $s;
        }
        return $map;
    }

    public static function decodeFleet(?string $text): ?array
    {
        if (!is_string($text) || $text === '') return null;
        $parts = explode(';', $text);
        if (count($parts) !== count(self::FLEET)) return null;
        $ships = [];
        foreach ($parts as $part) {
            if (!preg_match('/^(\d),(\d),([hv])$/', $part, $m)) return null;
            $ships[] = ['r' => (int)$m[1], 'c' => (int)$m[2], 'd' => $m[3]];
        }
        return self::fleetMap($ships) === null ? null : $ships;
    }

    public static function encodeFleet(array $ships): string
    {
        return implode(';', array_map(fn($s) => $s['r'] . ',' . $s['c'] . ',' . $s['d'], $ships));
    }

    public static function randomFleet(): array
    {
        while (true) {
            $ships = [];
            $ok = true;
            foreach (self::FLEET as $s => $len) {
                $placed = false;
                for ($tries = 0; $tries < 300 && !$placed; $tries++) {
                    $d = random_int(0, 1) ? 'h' : 'v';
                    $ship = [
                        'r' => random_int(0, $d === 'v' ? self::SIZE - $len : self::SIZE - 1),
                        'c' => random_int(0, $d === 'h' ? self::SIZE - $len : self::SIZE - 1),
                        'd' => $d,
                    ];
                    $try = $ships;
                    $try[$s] = $ship;
                    // Check only the ships placed so far
                    if (self::partialOk($try)) {
                        $ships = $try;
                        $placed = true;
                    }
                }
                if (!$placed) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) return $ships;
        }
    }

    private static function partialOk(array $ships): bool
    {
        $map = array_fill(0, self::SIZE * self::SIZE, -1);
        foreach ($ships as $s => $ship) {
            $cells = self::shipCells($ship, self::FLEET[$s]);
            if ($cells === null) return false;
            foreach ($cells as $i) if ($map[$i] !== -1) return false;
            foreach (self::around($cells) as $i) if ($map[$i] !== -1) return false;
            foreach ($cells as $i) $map[$i] = $s;
        }
        return true;
    }

    public static function parseShots(?string $text): array
    {
        $text = trim((string)$text);
        if ($text === '') return [];
        return array_map(function ($part) {
            [$slot, $cell] = explode(':', $part);
            return $cell === 'x' ? ['slot' => (int)$slot, 'cell' => -1, 'skip' => true] : ['slot' => (int)$slot, 'cell' => (int)$cell];
        }, explode(' ', $text));
    }

    public static function formatShots(array $shots): string
    {
        return implode(' ', array_map(fn($s) => $s['slot'] . ':' . (empty($s['skip']) ? $s['cell'] : 'x'), $shots));
    }

    /**
     * Replays the battle. $fleets[1] and $fleets[2] are the two fleets; a shot by slot 1 hits fleet 2.
     * With $bonus a hit gives the shooter another shot; otherwise turns always alternate.
     * Returns null on an illegal sequence (wrong turn, repeated cell, shot after the end).
     */
    public static function replay(array $fleets, array $shots, int $firstSlot, bool $bonus): ?array
    {
        $maps = [1 => self::fleetMap($fleets[1]), 2 => self::fleetMap($fleets[2])];
        if ($maps[1] === null || $maps[2] === null) return null;
        $hitsOn = [1 => array_fill(0, count(self::FLEET), 0), 2 => array_fill(0, count(self::FLEET), 0)];
        $sunk = [1 => [], 2 => []]; // ships sunk in each fleet
        $fired = [1 => [], 2 => []];
        $turn = $firstSlot;
        $winner = 0;
        $results = [];

        foreach ($shots as $shot) {
            $slot = $shot['slot'];
            $cell = $shot['cell'];
            // The time of the turn ran out: the turn passes without a shot
            if (!empty($shot['skip'])) {
                if ($winner !== 0 || $slot !== $turn) return null;
                $results[] = ['slot' => $slot, 'cell' => -1, 'result' => 'skip', 'ship' => null];
                $turn = $slot === 1 ? 2 : 1;
                continue;
            }
            if ($winner !== 0 || $slot !== $turn || $cell < 0 || $cell >= self::SIZE * self::SIZE) return null;
            if (isset($fired[$slot][$cell])) return null;
            $fired[$slot][$cell] = true;
            $target = $slot === 1 ? 2 : 1;
            $s = $maps[$target][$cell];
            if ($s === -1) {
                $results[] = ['slot' => $slot, 'cell' => $cell, 'result' => 'miss', 'ship' => null];
                $turn = $target;
                continue;
            }
            $hitsOn[$target][$s]++;
            $isSunk = $hitsOn[$target][$s] === self::FLEET[$s];
            if ($isSunk) $sunk[$target][] = $s;
            $results[] = ['slot' => $slot, 'cell' => $cell, 'result' => $isSunk ? 'sunk' : 'hit', 'ship' => $isSunk ? $s : null];
            if (count($sunk[$target]) === count(self::FLEET)) $winner = $slot;
            if (!$bonus) $turn = $target;
        }

        return ['results' => $results, 'turn' => $turn, 'winner' => $winner, 'sunk' => $sunk];
    }
}
