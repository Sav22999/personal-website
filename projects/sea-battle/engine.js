// Sea Battle rules engine, shared by the page, the computer player and (mirrored) api/engine.php.
// Grid: 10 x 10, a cell is an index 0-99 (row * 10 + column), row 0 = "1", column 0 = "A".
// A ship is {r, c, d} with d = 'h' (to the right) or 'v' (down); its length comes from FLEET.
// Ships never overlap and never touch, not even at a corner.
const SeaBattle = (() => {
    const SIZE = 10;
    const FLEET = [5, 4, 3, 3, 2];
    const SHIP_KEYS = ['carrier', 'battleship', 'cruiser', 'submarine', 'destroyer'];
    const TOTAL_CELLS = FLEET.reduce((a, b) => a + b, 0);

    const idx = (r, c) => r * SIZE + c;
    const inside = (r, c) => r >= 0 && r < SIZE && c >= 0 && c < SIZE;
    const label = (i) => 'ABCDEFGHIJ'[i % SIZE] + (Math.floor(i / SIZE) + 1);

    function shipCells(ship, len) {
        const out = [];
        for (let k = 0; k < len; k++) {
            const r = ship.r + (ship.d === 'v' ? k : 0);
            const c = ship.c + (ship.d === 'h' ? k : 0);
            if (!inside(r, c)) return null;
            out.push(idx(r, c));
        }
        return out;
    }

    // The ring of cells around a set of cells (the cells themselves excluded)
    function around(cells) {
        const own = new Set(cells);
        const ring = new Set();
        cells.forEach(i => {
            const r = Math.floor(i / SIZE), c = i % SIZE;
            for (let dr = -1; dr <= 1; dr++) for (let dc = -1; dc <= 1; dc++) {
                if (inside(r + dr, c + dc) && !own.has(idx(r + dr, c + dc))) ring.add(idx(r + dr, c + dc));
            }
        });
        return [...ring];
    }

    // Cell -> ship index map, or null when the fleet breaks a rule. Missing ships are allowed with partial = true.
    function fleetMap(ships, partial = false) {
        if (!Array.isArray(ships) || (!partial && ships.length !== FLEET.length)) return null;
        const map = new Array(SIZE * SIZE).fill(-1);
        for (let s = 0; s < FLEET.length; s++) {
            const ship = ships[s];
            if (!ship) {
                if (partial) continue;
                return null;
            }
            const cells = shipCells(ship, FLEET[s]);
            if (!cells) return null;
            for (const i of cells) if (map[i] !== -1) return null;
            for (const i of around(cells)) if (map[i] !== -1) return null;
            cells.forEach(i => map[i] = s);
        }
        return map;
    }

    const isValidFleet = (ships) => fleetMap(ships) !== null;

    // Can ship s go there, given the other ships already placed?
    function canPlace(ships, s, ship) {
        const copy = ships.slice();
        copy[s] = ship;
        return fleetMap(copy, true) !== null;
    }

    function randomFleet(rand = Math.random) {
        for (let attempt = 0; attempt < 200; attempt++) {
            const ships = [];
            let ok = true;
            for (let s = 0; s < FLEET.length && ok; s++) {
                ok = false;
                for (let tries = 0; tries < 300; tries++) {
                    const d = rand() < 0.5 ? 'h' : 'v';
                    const ship = {
                        r: Math.floor(rand() * (d === 'v' ? SIZE - FLEET[s] + 1 : SIZE)),
                        c: Math.floor(rand() * (d === 'h' ? SIZE - FLEET[s] + 1 : SIZE)),
                        d,
                    };
                    if (canPlace(ships, s, ship)) {
                        ships[s] = ship;
                        ok = true;
                        break;
                    }
                }
            }
            if (ok) return ships;
        }
        return null;
    }

    // Applies a list of shots at one fleet. Returns what the shooter knows and what is left.
    // shots: array of cell indexes, in order. Result per shot: 'miss', 'hit' or 'sunk'.
    function resolve(ships, shots) {
        const map = fleetMap(ships);
        const hitsOnShip = FLEET.map(() => 0);
        const seen = new Set();
        const results = [];
        const sunk = [];
        for (const i of shots) {
            if (seen.has(i)) {
                results.push(null);
                continue;
            }
            seen.add(i);
            const s = map[i];
            if (s === -1) {
                results.push('miss');
                continue;
            }
            hitsOnShip[s]++;
            if (hitsOnShip[s] === FLEET[s]) {
                sunk.push(s);
                results.push('sunk');
            } else results.push('hit');
        }
        return {map, results, sunk, allSunk: sunk.length === FLEET.length};
    }

    // Cells the shooter can tell are empty: the ring around every sunk ship (ships never touch)
    function clearedAround(ships, sunk) {
        const out = new Set();
        sunk.forEach(s => around(shipCells(ships[s], FLEET[s])).forEach(i => out.add(i)));
        return out;
    }

    const encodeFleet = (ships) => ships.map(s => `${s.r},${s.c},${s.d}`).join(';');

    function decodeFleet(text) {
        if (typeof text !== 'string' || text === '') return null;
        const ships = text.split(';').map(part => {
            const [r, c, d] = part.split(',');
            return {r: parseInt(r, 10), c: parseInt(c, 10), d};
        });
        return isValidFleet(ships) ? ships : null;
    }

    return {
        SIZE, FLEET, SHIP_KEYS, TOTAL_CELLS,
        idx, inside, label, shipCells, around, fleetMap, isValidFleet, canPlace, randomFleet,
        resolve, clearedAround, encodeFleet, decodeFleet,
    };
})();

if (typeof module !== 'undefined') module.exports = SeaBattle;
