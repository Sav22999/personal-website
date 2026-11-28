// Prism Mine rules engine, shared by the page and (mirrored) api/engine.php.
//
// The mine is a square grid: 6 x 6, 8 x 8 or 10 x 10 (size goes with the level; 8 is the default).
// Gems hide in it; every cell of a gem is either a full square ('S')
// or a right triangle named by its filled corner ('NE', 'SE', 'SW', 'NW').
// A beam of light enters from one of the 4 x size ports around the edge (numbered clockwise from the top left)
// and travels straight. It bounces back off a flat side, turns 90 degrees off a slanted side,
// and takes the colour of every gem it bounces off. A black gem swallows it, a clear gem bounces it without colour.
// The answer is where the beam comes out and with which colours.
const PrismMine = (() => {
    const SIZE = 8; // default size (daily mine, example, diagrams)
    const PORTS = SIZE * 4;
    const DIRS = [[-1, 0], [0, 1], [1, 0], [0, -1]]; // up, right, down, left

    // The gem shapes, in their base orientation: [row, column, cell type]
    const SHAPES = {
        shard: [[0, 0, 'SE']],
        cube: [[0, 0, 'S']],
        wedge: [[0, 0, 'S'], [0, 1, 'SW']],
        slab: [[0, 0, 'SE'], [0, 1, 'NW']],
        pyramid: [[0, 1, 'SE'], [1, 0, 'SE'], [1, 1, 'S']],
        diamond: [[0, 0, 'SE'], [0, 1, 'SW'], [1, 0, 'NE'], [1, 1, 'NW']],
        rod: [[0, 0, 'S'], [0, 1, 'S']],
        peak: [[0, 0, 'SE'], [0, 1, 'SW']],
        corner: [[0, 0, 'S'], [1, 0, 'S'], [1, 1, 'S']],
        bridge: [[0, 0, 'SE'], [0, 1, 'S'], [0, 2, 'SW']],
    };
    const SHAPE_KEYS = Object.keys(SHAPES);
    // R = red, Y = yellow, B = blue, W = white, C = clear (no colour), K = black (swallows the beam)
    const COLORS = ['R', 'Y', 'B', 'W', 'C', 'K'];

    // Filled sides of each triangle, as directions (0 up, 1 right, 2 down, 3 left)
    const FILLED = {NE: [0, 1], SE: [1, 2], SW: [2, 3], NW: [3, 0]};
    const SLASH = {NE: '\\', SW: '\\', SE: '/', NW: '/'};
    const TURN = {
        '/': {1: 0, 0: 1, 3: 2, 2: 3},
        '\\': {1: 2, 2: 1, 3: 0, 0: 3},
    };
    const ROTATE_TYPE = {S: 'S', NE: 'SE', SE: 'SW', SW: 'NW', NW: 'NE'};

    function rotateCells(cells) {
        const h = Math.max(...cells.map(c => c[0])) + 1;
        return normalize(cells.map(([r, c, t]) => [c, h - 1 - r, ROTATE_TYPE[t]]));
    }

    function normalize(cells) {
        const minR = Math.min(...cells.map(c => c[0]));
        const minC = Math.min(...cells.map(c => c[1]));
        return cells.map(([r, c, t]) => [r - minR, c - minC, t]).sort((a, b) => a[0] - b[0] || a[1] - b[1]);
    }

    // The distinct orientations of every shape (a diamond looks the same all round, a shard has four)
    const ORIENTATIONS = {};
    SHAPE_KEYS.forEach(key => {
        const list = [];
        let cells = normalize(SHAPES[key]);
        for (let i = 0; i < 4; i++) {
            const sig = JSON.stringify(cells);
            if (!list.some(o => JSON.stringify(o) === sig)) list.push(cells);
            cells = rotateCells(cells);
        }
        ORIENTATIONS[key] = list;
    });

    const orientation = (shape, rot) => ORIENTATIONS[shape][((rot % ORIENTATIONS[shape].length) + ORIENTATIONS[shape].length) % ORIENTATIONS[shape].length];

    // Cells covered by a placed gem {shape, color, r, c, rot}, or null when it sticks out of the mine
    function gemCells(gem, size = SIZE) {
        const out = [];
        for (const [dr, dc, t] of orientation(gem.shape, gem.rot)) {
            const r = gem.r + dr, c = gem.c + dc;
            if (r < 0 || r >= size || c < 0 || c >= size) return null;
            out.push({r, c, t});
        }
        return out;
    }

    // Grid of {t, g} (cell type, gem index), or null when gems overlap or stick out. Missing gems are skipped.
    function buildMap(gems, size = SIZE) {
        const map = Array.from({length: size}, () => new Array(size).fill(null));
        for (let g = 0; g < gems.length; g++) {
            if (!gems[g]) continue;
            const cells = gemCells(gems[g], size);
            if (!cells) return null;
            for (const {r, c, t} of cells) {
                if (map[r][c]) return null;
                map[r][c] = {t, g};
            }
        }
        return map;
    }

    // Where port p sits and which way its beam goes in
    function portStart(p, size = SIZE) {
        const side = Math.floor(p / size), k = p % size;
        if (side === 0) return {r: 0, c: k, d: 2};
        if (side === 1) return {r: k, c: size - 1, d: 3};
        if (side === 2) return {r: size - 1, c: size - 1 - k, d: 0};
        return {r: size - 1 - k, c: 0, d: 1};
    }

    // The port a beam reaches when it leaves cell (r, c) going in direction d
    function exitPort(r, c, d, size = SIZE) {
        if (d === 0) return c;
        if (d === 1) return size + r;
        if (d === 2) return 2 * size + (size - 1 - c);
        return 3 * size + (size - 1 - r);
    }

    // Follows a beam (the size of the mine is the size of the map).
    // Returns {exit: port or null when swallowed, colors: 'BRWY' subset, path: [[r, c, d]...]}
    function trace(map, gems, port) {
        const size = map.length;
        let {r, c, d} = portStart(port, size);
        const colors = new Set();
        const path = [];
        for (let step = 0; step < 600; step++) {
            if (r < 0 || r >= size || c < 0 || c >= size) {
                return {exit: exitPort(r - DIRS[d][0], c - DIRS[d][1], d, size), colors: mixKey(colors), path};
            }
            path.push([r, c, d]);
            const cell = map[r][c];
            if (cell) {
                const color = gems[cell.g].color;
                if (color === 'K') return {exit: null, colors: '', path};
                const entering = (d + 2) % 4; // the side the beam comes in through
                if (cell.t === 'S' || FILLED[cell.t].includes(entering)) d = (d + 2) % 4;
                else d = TURN[SLASH[cell.t]][d];
                if (color !== 'C') colors.add(color);
            }
            r += DIRS[d][0];
            c += DIRS[d][1];
        }
        return {exit: null, colors: '', path};
    }

    const mixKey = (set) => [...set].sort().join('');

    // Every port's answer: two layouts that give the same answers everywhere are equally right
    function signature(gems, size = SIZE) {
        const map = buildMap(gems, size);
        if (!map) return null;
        const out = [];
        for (let p = 0; p < size * 4; p++) {
            const res = trace(map, gems, p);
            out.push((res.exit === null ? 'x' : res.exit) + ':' + res.colors);
        }
        return out.join('|');
    }

    // The gems of a guess must be the hidden ones (same shapes and colours), placed anywhere that answers the same
    function sameGemSet(a, b) {
        const key = (list) => list.map(g => g.shape + g.color).sort().join(',');
        return a.length === b.length && key(a) === key(b);
    }

    function isCorrect(guess, hidden, size = SIZE) {
        if (!guess.every(Boolean) || !sameGemSet(guess, hidden)) return false;
        const sig = signature(guess, size);
        return sig !== null && sig === signature(hidden, size);
    }

    // Small seeded random generator (mulberry32), so the daily mine is the same for everybody
    function seededRandom(seed) {
        let h = 1779033703 ^ seed.length;
        for (let i = 0; i < seed.length; i++) {
            h = Math.imul(h ^ seed.charCodeAt(i), 3432918353);
            h = (h << 13) | (h >>> 19);
        }
        let a = h >>> 0;
        return () => {
            a = (a + 0x6D2B79F5) | 0;
            let t = Math.imul(a ^ (a >>> 15), 1 | a);
            t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
            return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
        };
    }

    // More gems need more room: each level has its own size of mine
    const LEVELS = {
        easy: {gems: 3, size: 6, colors: ['R', 'Y', 'B']},
        medium: {gems: 4, size: 8, colors: ['R', 'Y', 'B', 'W']},
        hard: {gems: 5, size: 10, colors: ['R', 'Y', 'B', 'W'], extra: ['C', 'K']},
    };
    const sizeOf = (level) => (LEVELS[level] || LEVELS.medium).size;

    function shuffle(list, rand) {
        const a = list.slice();
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(rand() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    // A new mine: distinct shapes, distinct colours, no overlaps, and every gem changes at least one answer
    function generate(level = 'medium', rand = Math.random, size = sizeOf(level)) {
        const cfg = LEVELS[level] || LEVELS.medium;
        for (let attempt = 0; attempt < 500; attempt++) {
            const shapes = shuffle(SHAPE_KEYS, rand).slice(0, cfg.gems);
            let colors = shuffle(cfg.colors, rand);
            if (cfg.extra) colors = colors.slice(0, cfg.gems - 1).concat(cfg.extra[Math.floor(rand() * cfg.extra.length)]);
            colors = colors.slice(0, cfg.gems);
            const gems = [];
            let ok = true;
            for (let g = 0; g < shapes.length && ok; g++) {
                ok = false;
                for (let tries = 0; tries < 200; tries++) {
                    const gem = {
                        shape: shapes[g], color: colors[g],
                        r: Math.floor(rand() * size), c: Math.floor(rand() * size),
                        rot: Math.floor(rand() * 4) % ORIENTATIONS[shapes[g]].length,
                    };
                    gems[g] = gem;
                    if (buildMap(gems, size)) {
                        ok = true;
                        break;
                    }
                }
            }
            if (!ok) continue;
            const full = signature(gems, size);
            const allSeen = gems.every((_, g) => signature(gems.filter((__, k) => k !== g), size) !== full);
            if (allSeen) return gems;
        }
        return null;
    }

    // The given gems (shape and colour) placed at random, without overlaps
    function randomLayout(set, size = SIZE, rand = Math.random) {
        for (let attempt = 0; attempt < 500; attempt++) {
            const gems = [];
            let ok = true;
            for (let g = 0; g < set.length && ok; g++) {
                ok = false;
                for (let tries = 0; tries < 200; tries++) {
                    gems[g] = {shape: set[g].shape, color: set[g].color, r: Math.floor(rand() * size), c: Math.floor(rand() * size), rot: Math.floor(rand() * ORIENTATIONS[set[g].shape].length)};
                    if (buildMap(gems, size)) {
                        ok = true;
                        break;
                    }
                }
            }
            if (ok) return gems;
        }
        return null;
    }

    const encode = (gems) => gems.map(g => `${g.shape},${g.color},${g.r},${g.c},${g.rot}`).join(';');

    function decode(text, size = SIZE) {
        if (typeof text !== 'string' || text === '') return null;
        const gems = text.split(';').map(part => {
            const [shape, color, r, c, rot] = part.split(',');
            return {shape, color, r: +r, c: +c, rot: +rot};
        });
        if (gems.some(g => !SHAPES[g.shape] || !COLORS.includes(g.color))) return null;
        return buildMap(gems, size) ? gems : null;
    }

    const portLabel = (p) => String(p + 1);

    return {
        SIZE, PORTS, SHAPES, SHAPE_KEYS, COLORS, ORIENTATIONS, LEVELS, sizeOf,
        orientation, gemCells, buildMap, portStart, exitPort, trace, signature, sameGemSet, isCorrect,
        seededRandom, generate, randomLayout, encode, decode, portLabel,
    };
})();

if (typeof module !== 'undefined') module.exports = PrismMine;
