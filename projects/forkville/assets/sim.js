/*
 * Forkville — live service, Sims style.
 *
 * Customers come in from the street, sit, order; a waiter takes the order, a chef cooks it at a stove and
 * puts it on a counter; the waiter serves it; the customer eats, pays and rates the visit by mood.
 * Mood depends on the wait, the decor around the seat, cleanliness, music, fun, light at night and the
 * staff's traits. Tables get dirty and litter appears: cleaners (or waiters) tidy up, and the plates go to the
 * sink. Staff get tired and rest on a sofa or at the coffee machine. A game clock runs a day/night cycle with
 * speed controls (pause, 1×, 2×, 3×) and busy hours.
 *
 * Everything happens in the browser: the app sends the served dishes to the server in batches (game.report),
 * and the server caps them by time and seats. The same simulation runs, without reporting, when visiting.
 */
import * as THREE from '../vendor/three.module.min.js';
import {personModel, animatePerson, dishModel, mat} from './models.js';
import {icon} from './icons.js';
import {sfx} from './audio.js';
import {FH, FLOOR_STRIDE as S} from './scene.js';

/**
 * Floors: the simulation keeps each storey on the same flat grid, FLOOR_STRIDE tiles further along z
 * (tile (x, z) of floor f is (x, z + f·S) here), so paths, seats and spots work as on one floor; stairs and lifts
 * add links between floors. The scene's floor layers put everything back on top of each other.
 */
const floorOf = (z) => Math.max(0, Math.floor((z + 100) / S));
/** A point of the simulation → the real world (for floating texts). */
const realPos = (v) => {
    const f = floorOf(v.z);
    return new THREE.Vector3(v.x, v.y + f * FH, v.z - f * S);
};

const OUT = 3; // depth of the terrace in front (OUTDOOR in lib/catalog.php)

/** SVG pictures (never emoji): dishes, moods, stars. */
export const dishIcon = (d) => icon('dish', d);
const mood = (m) => icon('mood', m);
const starsHtml = (n) => '<span class="stars-row">' + Array.from({length: n}, () => mood('star')).join('') + '</span>';

const DIRS = [[0, 1], [1, 0], [0, -1], [-1, 0]]; // facing for rotation r = 0..3
const SPEED = {customer: 1.5, waiter: 2.3, chef: 1.9, cleaner: 2.1, manager: 1.8};
const GAME_MINUTES_PER_SECOND = 2; // a full day lasts 12 minutes at 1×

/** Seconds between customers (same formula as arrival_seconds in lib/economy.php). */
export function arrivalSeconds(popularity) {
    return Math.max(3, Math.min(16, 16 - popularity / 12));
}

/**
 * How many people go out to eat at each hour (1 = normal): almost nobody in the middle of the night, a few early
 * in the morning, a lunch peak and a dinner peak. Each kind of restaurant has its own day: a café lives on
 * breakfast, a fast food also works late, a luxury restaurant comes alive at dinner.
 * Points [hour, factor], linearly blended (the last one joins midnight).
 */
const HOUR_CURVES = {
    generic: [[0, 0.06], [2, 0.03], [5, 0.03], [7, 0.25], [9, 0.5], [11, 0.9], [12.5, 1.6], [14, 1.4], [15, 0.6], [17.5, 0.7], [19, 1.4], [20.5, 1.7], [22, 0.9], [23.5, 0.2]],
    fast_food: [[0, 0.3], [3, 0.12], [5, 0.08], [7, 0.4], [10, 0.7], [12, 1.6], [14, 1.3], [16, 0.9], [18, 1.1], [20, 1.6], [22, 1.1], [23.5, 0.5]],
    cafe: [[0, 0.04], [5, 0.04], [6.5, 0.6], [8, 1.7], [10, 1.2], [12, 1.0], [14, 0.9], [16, 1.3], [18, 0.7], [20, 0.3], [22, 0.1]],
    inn: [[0, 0.1], [3, 0.04], [6, 0.06], [8, 0.5], [11, 0.8], [12.5, 1.3], [14.5, 0.8], [17, 0.9], [19, 1.6], [21, 1.5], [22.5, 0.8], [23.5, 0.3]],
    luxury: [[0, 0.04], [6, 0.02], [11, 0.3], [12.5, 0.9], [14.5, 0.5], [17, 0.3], [19, 1.4], [20.5, 2.0], [22, 1.2], [23.5, 0.3]],
};

export function hourFactor(h, kind = 'generic') {
    const pts = HOUR_CURVES[kind] || HOUR_CURVES.generic;
    const all = [...pts, [24, pts[0][1]]];
    for (let i = 0; i < all.length - 1; i++) {
        const [h1, f1] = all[i], [h2, f2] = all[i + 1];
        if (h >= h1 && h <= h2) return f1 + (f2 - f1) * ((h - h1) / (h2 - h1));
    }
    return pts[0][1];
}

/** 'rush' at peak times, 'quiet' when hardly anyone goes out, else null (for the HUD). */
export const hourMood = (h, kind) => {
    const f = hourFactor(h, kind);
    return f >= 1.35 ? 'rush' : f <= 0.15 ? 'quiet' : null;
};

export const isNight = (h) => h < 6.5 || h >= 20;

/** Dirt on the floor: crumpled paper, fruit peels, crumbs, a rain puddle, snow brought in from outside. */
/** A soft round stain on the floor (drawn once, shared). */
const STAIN_TEX = (() => {
    const c = document.createElement('canvas');
    c.width = c.height = 64;
    const g = c.getContext('2d');
    for (let i = 0; i < 6; i++) {
        const gr = g.createRadialGradient(32 + (i % 3 - 1) * 8, 32 + (i % 2 ? 6 : -6), 2, 32, 32, 18 + i * 2);
        gr.addColorStop(0, 'rgba(255,255,255,.55)');
        gr.addColorStop(1, 'rgba(255,255,255,0)');
        g.fillStyle = gr;
        g.fillRect(0, 0, 64, 64);
    }
    return new THREE.CanvasTexture(c);
})();
const stain = (color, r, x = 0, z = 0, opacity = 0.6) => {
    const m = new THREE.Mesh(new THREE.CircleGeometry(r, 20), new THREE.MeshBasicMaterial({color, map: STAIN_TEX, transparent: true, opacity, depthWrite: false}));
    m.rotation.x = -Math.PI / 2;
    m.position.set(x, 0.032, z);
    m.renderOrder = 1;
    return m;
};
/** Shoe prints (wet or snowy) walking in from the door. */
const prints = (g, color, opacity) => {
    for (let i = 0; i < 4; i++) {
        const p = new THREE.Mesh(new THREE.CircleGeometry(0.05, 10), new THREE.MeshBasicMaterial({color, transparent: true, opacity, depthWrite: false}));
        p.rotation.x = -Math.PI / 2;
        p.scale.set(0.7, 1.4, 1);
        p.position.set((i % 2 ? 0.09 : -0.09), 0.033, 0.4 - i * 0.22);
        g.add(p);
    }
};

/** What gets dropped on the floor: crumpled paper, peels, crumbs, spilled drinks, puddles, snow, leaves. */
export function litterMesh(kind) {
    const g = new THREE.Group();
    const add = (geo, color, x, y, z, opts) => {
        const m = new THREE.Mesh(geo, mat(color, opts));
        m.position.set(x, y, z);
        m.castShadow = true;
        g.add(m);
        return m;
    };
    if (kind === 'paper') {
        // Two crumpled balls, a folded napkin and a long receipt curling up
        for (const [x, z, r, c] of [[0, 0, 0.07, '#F4EFE2'], [0.15, 0.06, 0.05, '#FFFFFF']]) {
            const b = add(new THREE.DodecahedronGeometry(r, 0), c, x, r * 0.8, z);
            b.scale.set(1, 0.85, 1.1);
            b.rotation.set(x * 10, z * 20, 0.4);
            add(new THREE.DodecahedronGeometry(r * 0.6, 0), '#E3DCCB', x + r * 0.4, r * 1.1, z - r * 0.2);
        }
        const nap = add(new THREE.CircleGeometry(0.1, 3), '#F25F5C', -0.12, 0.034, -0.06, {flat: false});
        nap.rotation.set(-Math.PI / 2, 0, 0.6);
        nap.material = nap.material.clone();
        nap.material.side = THREE.DoubleSide;
        const receipt = add(new THREE.CylinderGeometry(0.035, 0.035, 0.07, 10, 1, true, 0, Math.PI * 1.5), '#FFFFFF', -0.02, 0.035, 0.14);
        receipt.rotation.z = Math.PI / 2;
        receipt.material = receipt.material.clone();
        receipt.material.side = THREE.DoubleSide;
        g.add(stain('#C9BFA9', 0.12, 0.05, 0.02, 0.25));
    } else if (kind === 'peel') {
        // A banana peel (four floppy strips with brown tips) and a curl of orange peel
        add(new THREE.CylinderGeometry(0.018, 0.022, 0.05, 6), '#B8862B', 0, 0.035, 0).rotation.z = 0.4;
        for (let i = 0; i < 4; i++) {
            const a = i * Math.PI / 2 + 0.3;
            const strip = add(new THREE.CapsuleGeometry(0.025, 0.11, 3, 6), '#F6C945', Math.cos(a) * 0.08, 0.018, Math.sin(a) * 0.08, {flat: false});
            strip.rotation.set(Math.PI / 2, 0, -a + Math.PI / 2);
            strip.scale.set(1.2, 1, 0.35);
            add(new THREE.SphereGeometry(0.018, 6, 4), '#7B5A2C', Math.cos(a) * 0.15, 0.018, Math.sin(a) * 0.15);
        }
        const orange = add(new THREE.TorusGeometry(0.06, 0.016, 5, 16, Math.PI * 1.6), '#F08A24', 0.2, 0.02, 0.1, {flat: false});
        orange.rotation.x = Math.PI / 2;
        orange.scale.z = 0.5;
        g.add(stain('#E9C46A', 0.1, 0.02, -0.02, 0.25));
    } else if (kind === 'crumbs') {
        // Bread crumbs around a broken biscuit and a greasy spot
        g.add(stain('#B08B5A', 0.16, 0, 0, 0.35));
        const bisc = add(new THREE.CylinderGeometry(0.05, 0.05, 0.015, 12, 1, false, 0, Math.PI * 1.3), '#D9A45C', 0.03, 0.04, -0.02);
        bisc.rotation.y = 0.8;
        for (let i = 0; i < 10; i++) add(new THREE.IcosahedronGeometry(0.012 + (i % 3) * 0.008, 0), ['#C98F5A', '#E6B26B', '#A87444'][i % 3], Math.cos(i * 2.4) * (0.06 + (i % 4) * 0.03), 0.035, Math.sin(i * 2.4) * (0.06 + (i % 4) * 0.03));
    } else if (kind === 'spill') {
        // A paper cup on its side and the drink running out of it
        g.add(stain('#B8772E', 0.22, 0.06, 0, 0.95), stain('#B8772E', 0.1, 0.25, 0.08, 0.9));
        const cup = add(new THREE.CylinderGeometry(0.045, 0.035, 0.11, 12), '#FFFFFF', -0.06, 0.045, 0);
        cup.rotation.z = Math.PI / 2;
        add(new THREE.CylinderGeometry(0.047, 0.047, 0.02, 12), '#D9653B', -0.11, 0.045, 0).rotation.z = Math.PI / 2;
        const straw = add(new THREE.CylinderGeometry(0.006, 0.006, 0.16, 5), '#3E7CB4', 0.05, 0.036, 0.09);
        straw.rotation.set(Math.PI / 2, 0, 0.7);
        for (let i = 0; i < 3; i++) add(new THREE.BoxGeometry(0.025, 0.025, 0.025), '#E8F6FF', 0.08 + i * 0.05, 0.045, -0.05 + i * 0.03, {opacity: 0.7, roughness: 0.05});
    } else if (kind === 'puddle') {
        // An uneven puddle of rainwater with a ripple, and wet shoe prints from the door
        g.add(stain('#6FB4E0', 0.42, 0, 0, 0.95), stain('#BFE3F5', 0.2, -0.08, -0.06, 0.6));
        const ripple = add(new THREE.RingGeometry(0.08, 0.095, 20), '#E8F6FF', 0.04, 0.037, 0.02, {opacity: 0.6});
        ripple.rotation.x = -Math.PI / 2;
        g.userData.ripple = ripple;
        prints(g, '#5A7A92', 0.35);
    } else if (kind === 'snow') {
        // Clumps of snow brought in on boots, a melting wet ring and snowy prints
        g.add(stain('#9FB6C8', 0.36, 0, 0, 0.35));
        for (let i = 0; i < 6; i++) {
            const m = add(new THREE.SphereGeometry(0.06 + (i % 3) * 0.04, 8, 5, 0, Math.PI * 2, 0, Math.PI / 2), '#F7FAFC', Math.cos(i * 1.3) * 0.2, 0.0, Math.sin(i * 1.3) * 0.16, {flat: false});
            m.scale.y = 0.45;
        }
        prints(g, '#FFFFFF', 0.7);
    } else if (kind === 'leaves') {
        // Autumn leaves blown in through the door
        for (let i = 0; i < 7; i++) {
            const l = add(new THREE.CircleGeometry(0.05, 5), ['#E07B39', '#C8452C', '#E9B44C', '#B5562E'][i % 4], Math.cos(i * 2.2) * 0.2, 0.035 + i * 0.001, Math.sin(i * 2.2) * 0.18, {flat: false});
            l.rotation.set(-Math.PI / 2, 0, i);
            l.scale.set(1, 1.5, 1);
            l.material = l.material.clone();
            l.material.side = THREE.DoubleSide;
        }
    }
    g.traverse(o => o.isMesh && (o.receiveShadow = true));
    return g;
}

export const starsForMood = (m) => m >= 82 ? 5 : m >= 66 ? 4 : m >= 48 ? 3 : m >= 30 ? 2 : 1;

/** Plants (inside and in the garden) that need watering once a day. */
const PLANT_RX = /plant|cactus|bonsai|palm|sunflower|fern|orchid|lavender|terrarium|bamboo|tree_|bush|hedge|flower|topiary|ivy|monstera|ficus|xmas_tree|lemon/;
export const isPlant = (k) => PLANT_RX.test(k) && !/flower_cart|rug_|wall_flowers/.test(k);

/** Height of the top of tables (where dishes go) and of seats (how high a guest sits), when not the usual. */
const TABLE_H = {table_marble: 0.72, table_bar: 1.03, table_marble_round: 0.74, table_gold: 0.74, table_rustic_long: 0.76};
const SEAT_Y = {stool_bar: 0.12, stool_wood: 0.12, stool_cushion: 0.12, chair_velvet: -0.12, chair_armchair: -0.12, chair_leather: -0.12, chair_throne: -0.1, chair_egg: -0.1, chair_booth: 0.03, chair_wicker: -0.02};

export class Sim {
    constructor(world, catalog, opts = {}) {
        this.world = world;
        this.catalog = catalog;
        this.opts = opts;
        this.agents = [];
        this.bubbles = new Map();
        this.batch = {dishes: {}, tips: 0, ratings: [], reviews: []};
        this.running = false;
        this.speed = 1;
        this.clock = 9 * 60; // game minutes since the restaurant opened (year 0, 1 March); the app sets it from the server
        this.effects = {music: 0, fun: 0, cozy: 0};
        world.on('frame', (dt, t) => this.running && this.update(dt, t));
    }

    /** r: the restaurant ({size, layout, staff, menu: [{dish, level}], popularity}). */
    start(r) {
        this.stop();
        this.r = r;
        this.W = r.size; // width (x)
        this.D = r.depth || r.size; // depth (z): the door is in the front row z = D − 1
        this.size = this.D; // (z positions of the front wall and the street below use the depth)
        this.door = r.door ?? Math.floor(this.W / 2); // the player may move it along the front wall
        this.rects = [{x0: 0, w: this.W, d: this.D}, ...(r.floors || []).map(([w, d, x0]) => ({x0, w, d}))];
        this.layout = r.layout.map(it => it.f ? {...it, z: it.z + it.f * S} : it);
        this.analyse();
        this.orders = [];
        this.litter = [];
        this.spawnIn = 2;
        this.nextId = 1;
        this.t = 0;
        for (const s of r.staff) this.addStaff(s);
        this.assignTables();
        if (!this.spots.toilet.length && this.seats.length) this.warnings.push({key: 'need_toilet'});
        if ((r.status || 'open') === 'closed') for (const a of this.agents) if (a.staff) this.sendHome(a, true);
        this.running = true;
    }

    stop() {
        this.running = false;
        for (const a of this.agents) {
            a.mesh.parent?.remove(a.mesh);
            if (a.served) a.served.parent?.remove(a.served);
        }
        for (const c of this.counters || []) c.slots.forEach(s => {
            if (s.dish?.mesh) s.dish.mesh.parent?.remove(s.dish.mesh); // a reserved slot has no dish yet
            s.dish = null;
        });
        for (const seat of this.seats || []) if (seat.dirtyMesh) seat.dirtyMesh.parent?.remove(seat.dirtyMesh);
        for (const l of this.litter || []) l.mesh.parent?.remove(l.mesh);
        this.litter = [];
        this.world.traffic?.reset();
        this.agents = [];
        for (const b of this.bubbles.values()) b.remove();
        this.bubbles.clear();
        this.selected = null;
    }

    takeBatch() {
        const b = this.batch;
        this.batch = {dishes: {}, tips: 0, cars: 0, ratings: [], reviews: []};
        return b;
    }

    get hour() {
        return (this.clock / 60) % 24;
    }

    // ------------------------------------------------------------ the room as a grid

    analyse() {
        const items = this.catalog.items, tags = this.catalog.tags || {}, W = this.W;
        this.blocked = new Uint8Array(W * ((this.rects.length - 1) * S + this.D + 1));
        const inside = (x, z) => {
            const f = floorOf(z), rc = this.rects[f], rz = z - f * S;
            return !!rc && x >= rc.x0 && x < rc.x0 + rc.w && rz >= 0 && rz < rc.d;
        };
        this.inside = inside;
        const at = new Map();
        for (const it of this.layout) {
            if (items[it.k].cat === 'rug') continue; // people walk on rugs
            for (const [x, z] of this.world.footprint(it, items).tiles) {
                if (!inside(x, z)) continue;
                this.blocked[z * W + x] = 1;
                at.set(x + ',' + z, it);
            }
        }
        // The way up: above stairs and lifts the tiles stay empty (nobody stands in a stairwell)
        for (let f = 1; f < this.rects.length; f++) for (const key of this.world.shaftTiles(f, this.r.layout, items)) {
            const [x, z] = key.split(',').map(Number);
            if (inside(x, z + f * S)) this.blocked[(z + f * S) * W + x] = 1;
        }
        const free = (x, z) => inside(x, z) && !this.blocked[z * W + x];
        this.free = free;
        const center = (it) => {
            const {w, d} = this.world.footprint(it, items);
            return {x: it.x + w / 2, z: it.z + d / 2};
        };
        const front = (it) => {
            const {tiles} = this.world.footprint(it, items);
            const [dx, dz] = DIRS[it.r];
            for (const [x, z] of tiles) if (free(x + dx, z + dz)) return {x: x + dx, z: z + dz};
            for (const [x, z] of tiles) for (const [ax, az] of DIRS) if (free(x + ax, z + az)) return {x: x + ax, z: z + az};
            return null;
        };
        this.seats = [];
        this.stoves = [];
        this.counters = [];
        this.spots = {sink: [], rest: [], coffee: [], music: [], fun: [], cozy: [], light: [], janitor: [], toilet: [], washbasin: [], dryer: [], fridge: 0, register: 0, trash: 0, snack: 0, attract: 0, truck: [], queue: []};
        this.kind = (this.catalog.kinds || {})[this.r.kind || 'generic'] || {price: 1, customers: 1, eat: 1};
        this.kindKey = this.r.kind || 'generic';
        this.benchSeats = [];
        this.decor = []; // {x, z, pop}: beauty around a seat
        this.tiers = {fridge: 0, sink: 0, counter: 0};
        const tierOf = (k) => items[k].tier || 1;
        this.links = new Map(); // tile key → tiles on other floors (stairs and lifts)
        const link = (a, b) => {
            const ka = a.z * W + a.x, kb = b.z * W + b.x;
            (this.links.get(ka) || this.links.set(ka, []).get(ka)).push(kb);
            (this.links.get(kb) || this.links.set(kb, []).get(kb)).push(ka);
        };
        for (const it of this.layout) {
            const def = items[it.k];
            const tag = tags[it.k];
            if (def.cat === 'stairs') {
                const entry = front(it);
                if (!entry) continue;
                const f = floorOf(it.z), tiles = this.world.footprint(it, items).tiles;
                if (tag === 'lift') { // the same spot in front of the doors, on every floor the lift reaches
                    const stops = [entry];
                    for (let g = f + 1; g < this.rects.length; g++) {
                        if (!this.world.shaftTiles(g, this.r.layout, items).has(it.x + ',' + (it.z - f * S))) break;
                        const st = {x: entry.x, z: entry.z + (g - f) * S};
                        if (free(st.x, st.z)) stops.push(st);
                    }
                    for (let i = 0; i < stops.length; i++) for (let j = i + 1; j < stops.length; j++) link(stops[i], stops[j]);
                } else { // stairs: up at the front, out on the floor above just past the top end
                    const [dx, dz] = DIRS[it.r], own = new Set(tiles.map(([x, z]) => x + ',' + z));
                    const out = tiles.map(([x, z]) => ({x: x - dx, z: z - dz + S})).find(p => !own.has(p.x + ',' + (p.z - S)) && free(p.x, p.z));
                    if (out) link(entry, out);
                }
                continue;
            }
            if (def.pop > 0) this.decor.push({...center(it), pop: def.pop, id: it.id});
            if (tag === 'fridge' || tag === 'sink' || tag === 'janitor') this.tiers[tag] = Math.max(this.tiers[tag] || 0, tierOf(it.k));
            if (def.cat === 'counter') this.tiers.counter = Math.max(this.tiers.counter, tierOf(it.k));
            if (tag === 'queue') { // outdoor bench: two places to wait
                const {w, d} = this.world.footprint(it, items);
                const c = {x: it.x + w / 2, z: it.z + d / 2}, [fx, fz] = DIRS[it.r];
                for (const o of [-0.45, 0.45]) this.benchSeats.push({x: c.x + fz * o, z: c.z + fx * o, r: it.r, busy: null});
                continue;
            }
            if (tag) {
                if (typeof this.spots[tag] === 'number') this.spots[tag]++;
                else {
                    const f = front(it);
                    this.spots[tag].push({id: it.id, key: it.k, ...center(it), front: f, busy: null});
                }
            }
            if (def.cat === 'chair') {
                const [dx, dz] = DIRS[it.r];
                const table = at.get((it.x + dx) + ',' + (it.z + dz));
                if (!table || items[table.k].cat !== 'table') continue;
                const service = DIRS.map(([a, b]) => ({x: it.x + a, z: it.z + b})).filter(p => free(p.x, p.z));
                const tableNb = this.world.footprint(table, items).tiles.flatMap(([tx, tz]) => DIRS.map(([a, b]) => ({x: tx + a, z: tz + b}))).filter(p => free(p.x, p.z));
                const all = [...service, ...tableNb];
                if (!all.length) continue;
                const tableH = TABLE_H[table.k] ?? 0.77;
                this.seats.push({
                    id: it.id, x: it.x, z: it.z, r: it.r, service: all, busy: null, dirty: false, tableId: table.id,
                    tableTiles: this.world.footprint(table, items).tiles,
                    dishPos: new THREE.Vector3(it.x + 0.5 + dx * 0.72, tableH, it.z + 0.5 + dz * 0.72),
                    seatY: SEAT_Y[it.k] ?? -0.03,
                    beauty: 0, comfort: tierOf(it.k) + tierOf(table.k),
                });
            } else if (def.cat === 'stove') {
                const f = front(it);
                if (f) this.stoves.push({id: it.id, front: f, r: it.r, speed: def.speed || 1, tier: tierOf(it.k), chef: null});
            } else if (def.cat === 'counter') {
                const f = front(it);
                if (!f) continue;
                const cx = it.x + 0.5, cz = it.z + 0.5, [dx, dz] = DIRS[it.r];
                const side = [dz, dx];
                this.counters.push({id: it.id, front: f, slots: [-0.22, 0.22].map(o => ({pos: new THREE.Vector3(cx + side[0] * o + dx * 0.1, 0.98, cz + side[1] * o + dz * 0.1), dish: null}))});
            }
        }
        // Plants: the cleaner waters them every day (a thirsty plant droops and is only half as pretty)
        const old = new Map((this.plants || []).map(p => [p.id, p]));
        this.plants = [];
        for (const it of this.layout) {
            if (!isPlant(it.k)) continue;
            const outdoor = items[it.k].cat === 'outdoor';
            const prev = old.get(it.id);
            this.plants.push({id: it.id, k: it.k, ...center(it), outdoor, front: outdoor ? null : front(it), lastWater: prev?.lastWater ?? null, thirsty: prev?.thirsty || false, claimed: null,
                decor: this.decor.find(d => d.id === it.id)});
        }
        this.computeBeauty();
        // Seats that cannot be reached from the door are useless
        const doorTile = {x: this.door, z: this.D - 1};
        this.seats = this.seats.filter(s => this.path(doorTile, [{x: s.x, z: s.z}]) !== null);
        // The tables that have at least one usable seat, numbered for the player (1, 2, 3…)
        const byTable = new Map();
        for (const seat of this.seats) {
            if (!byTable.has(seat.tableId)) byTable.set(seat.tableId, {id: seat.tableId, tiles: seat.tableTiles, seats: []});
            byTable.get(seat.tableId).seats.push(seat);
        }
        this.tables = [...byTable.values()].sort((a, b) => a.id - b.id);
        this.tables.forEach((tb, i) => (tb.num = i + 1));
        this.problems = [];
        if (!this.stoves.length) this.problems.push('need_stove');
        if (!this.counters.length) this.problems.push('need_counter');
        if (!this.seats.length) this.problems.push('need_seat');
        if (!this.r.staff.some(s => s.role === 'chef')) this.problems.push('need_chef');
        if (!this.r.staff.some(s => s.role === 'waiter')) this.problems.push('need_waiter');
        if (!this.r.menu.length) this.problems.push('need_menu');
    }

    /** Beauty around each seat: decorations within 3 tiles (thirsty plants count half). */
    computeBeauty() {
        for (const s of this.seats) s.beauty = Math.min(25, this.decor.reduce((sum, d) => {
            if (Math.hypot(d.x - s.x - 0.5, d.z - s.z - 0.5) > 3) return sum;
            const thirsty = this.plants?.some(p => p.decor === d && p.thirsty);
            return sum + (thirsty ? d.pop / 2 : d.pop);
        }, 0));
    }

    /** The game day number (the calendar's), and the hour. */
    get day() {
        return Math.floor((this.clock || 0) / 1440);
    }

    /** A plant gets water: it perks up. */
    water(p, by = null) {
        p.lastWater = this.day;
        p.claimed = null;
        if (p.thirsty) {
            p.thirsty = false;
            this.computeBeauty();
        }
        const obj = this.world.items.get(p.id);
        if (obj) obj.scale.set(1, 1, 1);
        this.floater(new THREE.Vector3(p.x, 1.2, p.z), mood('water'), 'floater-fx');
        if (by) by.energy -= 1;
    }

    /** Evening and still not watered today: the plant droops. */
    checkPlants() {
        const day = this.day, hour = this.hour;
        let changed = false;
        for (const p of this.plants || []) {
            if (p.lastWater == null) p.lastWater = day; // at the start everything has just been watered
            if (!p.thirsty && p.lastWater < day && hour >= 18) {
                p.thirsty = true;
                changed = true;
                const obj = this.world.items.get(p.id);
                if (obj) obj.scale.set(1, 0.86, 1);
            }
        }
        if (changed) this.computeBeauty();
    }

    /** The cleaner's daily round: one plant at a time, inside or out in the garden. True if they went. */
    waterRound(a) {
        if (this.hour < 7) return false;
        const p = this.plants?.find(x => x.lastWater != null && x.lastWater < this.day && !x.claimed && (x.outdoor || x.front));
        if (!p) return false;
        p.claimed = a;
        a.task = 'water';
        a.activity = 'waterPlants';
        const pour = (then) => {
            this.face(a, p.x, p.z);
            this.busy(a, 1.8 * this.skillTime(a), 'water', () => {
                this.water(p, a);
                then();
            });
        };
        if (!p.outdoor) {
            const ok = this.walkTo(a, [p.front], () => pour(() => this.goHome(a)));
            if (!ok) {
                p.claimed = null;
                a.task = null;
            }
            return ok;
        }
        // Outside: through the door, around the house if the plant is in the yard or behind, and back again
        const n = this.D, dx = this.door + 0.5, W = this.W;
        const route = [[dx, n + 0.6]];
        if (p.x >= W || p.z < 0) route.push([W + 0.5, n + 0.6]);
        if (p.z < 0) route.push([W + 0.5, -0.5], [p.x + 0.8, -0.5]);
        const spot = [p.x + 0.8, p.z];
        const back = [...route].reverse();
        const ok = this.walkTo(a, [{x: this.door, z: n - 1}], () => this.walkPoints(a, [[dx, n + 0.4], ...route, spot], () => pour(() => this.walkPoints(a, [...back, [dx, n + 0.4], [dx, n - 0.5]], () => {
            a.tile = {x: this.door, z: n - 1};
            this.goHome(a);
        }))));
        if (!ok) {
            p.claimed = null;
            a.task = null;
        }
        return ok;
    }

    /** A* on the tile grid (4 directions). Goals may be blocked tiles (a chair). Returns tiles after start, or null. */
    path(start, goals) {
        const n = this.W, key = (x, z) => z * n + x;
        goals = goals.filter(Boolean);
        if (!goals.length) return null;
        const goalSet = new Set(goals.map(g => key(g.x, g.z)));
        if (goalSet.has(key(start.x, start.z))) return [];
        // Distance estimate: tiles on the floor, plus a few steps per storey to climb
        const h = (x, z) => Math.min(...goals.map(g => {
            const fa = floorOf(z), fb = floorOf(g.z);
            return Math.abs(g.x - x) + Math.abs((g.z - fb * S) - (z - fa * S)) + Math.abs(fb - fa) * 4;
        }));
        const open = [[h(start.x, start.z), 0, start.x, start.z]];
        const came = new Map([[key(start.x, start.z), -1]]);
        const cost = new Map([[key(start.x, start.z), 0]]);
        while (open.length) {
            let bi = 0;
            for (let i = 1; i < open.length; i++) if (open[i][0] < open[bi][0]) bi = i;
            const [, g, x, z] = open.splice(bi, 1)[0];
            const k = key(x, z);
            if (goalSet.has(k)) {
                const out = [];
                for (let c = k; c !== key(start.x, start.z); c = came.get(c)) out.unshift({x: c % n, z: Math.floor(c / n)});
                return out;
            }
            for (const [dx, dz] of DIRS) {
                const nx = x + dx, nz = z + dz, nk = key(nx, nz);
                if (!this.inside(nx, nz)) continue;
                if (this.blocked[nk] && !goalSet.has(nk)) continue;
                const ng = g + 1;
                if (ng < (cost.get(nk) ?? Infinity)) {
                    cost.set(nk, ng);
                    came.set(nk, k);
                    open.push([ng + h(nx, nz), ng, nx, nz]);
                }
            }
            for (const nk of this.links.get(k) || []) { // up or down the stairs / the lift
                const nx = nk % n, nz = Math.floor(nk / n), ng = g + 4;
                if (ng < (cost.get(nk) ?? Infinity)) {
                    cost.set(nk, ng);
                    came.set(nk, k);
                    open.push([ng + h(nx, nz), ng, nx, nz]);
                }
            }
        }
        return null;
    }

    // ------------------------------------------------------------ agents

    addAgent(role, color, seed, look) {
        const mesh = personModel(role, color, seed, look);
        this.world.agentGroup.add(mesh);
        const a = {id: this.nextId++, role, mesh, pos: new THREE.Vector3(), way: [], state: 'idle', anim: 'idle', timer: 0, task: null, activity: 'idle'};
        mesh.traverse(o => (o.userData.agentId = a.id));
        this.agents.push(a);
        return a;
    }

    addStaff(s) {
        const uniform = s.outfit && s.outfit !== 'default' ? this.catalog.uniforms?.[s.outfit] : null;
        const a = this.addAgent(s.role, s.color, s.id * 7.3, {hair: s.hair, hairColor: s.hairColor, uniform, glasses: s.glasses, facial: s.facial, headwear: s.headwear});
        a.staff = s;
        a.trait = s.trait || null;
        a.energy = 70 + Math.random() * 30;
        const same = this.agents.filter(x => x.role === s.role).length - 1;
        if (s.role === 'chef') {
            const stove = this.stoves.find(x => !x.chef);
            a.stove = stove || null;
            if (stove) stove.chef = a;
            a.home = stove ? stove.front : this.nearFree({x: 1, z: 1});
        } else if (s.role === 'waiter') {
            const c = this.counters[same % Math.max(1, this.counters.length)];
            a.home = this.nearFree(c ? c.front : {x: this.door, z: this.size - 2}, same);
        } else if (s.role === 'manager') {
            a.home = this.nearFree({x: this.door, z: this.size - 2}, same + 2);
        } else {
            // Cleaners wait by their cart or station (else near the sink)
            const base = this.spots.janitor[0]?.front || this.spots.sink[0]?.front;
            a.home = this.nearFree(base || {x: this.W - 2, z: 1}, same + 1);
            a.supplies = 4;
        }
        this.place(a, a.home);
    }

    /**
     * Each waiter looks after their own tables: the tables are shared out in turn (a waiter may get several).
     * With more waiters than tables, the extra waiters get none and cannot serve.
     */
    assignTables() {
        const waiters = this.agents.filter(a => a.role === 'waiter').sort((a, b) => a.staff.id - b.staff.id);
        this.warnings = [];
        waiters.forEach(w => (w.tables = []));
        for (const tb of this.tables) tb.waiter = null;
        if (!waiters.length) return;
        this.tables.forEach((tb, i) => {
            const w = waiters[i % waiters.length];
            w.tables.push(tb.id);
            tb.waiter = w;
        });
        const idle = waiters.filter(w => !w.tables.length).length;
        if (idle) this.warnings.push({key: 'waiter_no_table', n: idle});
    }

    /** Is this seat at one of this waiter's tables? */
    servesSeat(w, seat) {
        return !!seat && (w.tables || []).includes(seat.tableId);
    }

    /** A free tile near p (the k-th one found), so people don't stand on each other. */
    nearFree(p, k = 0) {
        const seen = [];
        for (let rad = 0; rad < 5; rad++) for (let dx = -rad; dx <= rad; dx++) for (let dz = -rad; dz <= rad; dz++) {
            if (Math.max(Math.abs(dx), Math.abs(dz)) !== rad) continue;
            const x = p.x + dx, z = p.z + dz;
            if (this.free(x, z) && !(x === this.door && z === this.size - 1)) seen.push({x, z});
            if (seen.length > k) return seen[k];
        }
        return seen[seen.length - 1] || p;
    }

    place(a, tile) {
        a.tile = {...tile};
        a.pos.set(tile.x + 0.5, 0, tile.z + 0.5);
        this.toFloor(a, floorOf(tile.z));
        a.mesh.position.copy(a.pos);
    }

    /** Puts a person's model in the layer of floor f (the simulation keeps floors apart, the layers stack them). */
    toFloor(a, f) {
        const layer = this.world.layer('agent', f);
        if (a.mesh.parent !== layer) layer.add(a.mesh);
        a.floor = f;
    }

    /** Adds a thing (dish, plate, litter) to the floor its position is on. */
    fxAdd(obj) {
        this.world.layer('fx', floorOf(obj.position.z)).add(obj);
    }

    /** Walk to the nearest of the goal tiles; then() runs on arrival. Returns false when unreachable. */
    walkTo(a, goals, then) {
        const p = this.path(a.tile, goals);
        if (p === null) return false;
        a.way = p.map(t => ({x: t.x + 0.5, z: t.z + 0.5, tile: t}));
        a.then = then;
        a.state = 'walk';
        if (!a.way.length) {
            a.state = 'idle';
            then && then();
        }
        return true;
    }

    /** Walk through world points (outside the room, no grid). */
    walkPoints(a, pts, then) {
        a.way = pts.map(([x, z]) => ({x, z, tile: {x: Math.floor(x), z: Math.floor(z)}}));
        a.then = then;
        a.state = 'walk';
    }

    /** A worker's skill in their current role (0–10, from their CV). */
    skill(a) {
        return a.staff?.skills?.[a.role] ?? 5;
    }

    /** Time factor for a task: skilled people are quicker (0.85× at 10, 1.3× at 0). */
    skillTime(a) {
        let k = 1.3 - this.skill(a) * 0.045;
        // Cleaning goes faster with proper supplies: a cart, better still a station (slower without either)
        if (a.role === 'cleaner') k *= this.tiers.janitor ? 1 - this.tiers.janitor * 0.08 : 1.2;
        return k;
    }

    /** After a few jobs a cleaner goes back to the cart or station to restock. Returns true if they went. */
    restock(a) {
        const st = this.spots.janitor.find(x => x.front);
        if (a.role !== 'cleaner' || !st || (a.supplies ?? 4) > 0) return false;
        a.task = 'restock';
        a.activity = 'restock';
        const ok = this.walkTo(a, [st.front], () => {
            this.face(a, st.x, st.z);
            this.busy(a, 1.6, 'wipe', () => {
                a.supplies = 3 + this.tiers.janitor * 2; // a station holds more supplies than a cart
                this.goHome(a);
            });
        });
        if (!ok) a.task = null;
        return ok;
    }

    walkSpeed(a) {
        let v = SPEED[a.role];
        if (a.staff) v *= 0.88 + this.skill(a) * 0.025;
        if (a.trait === 'speedy') v *= 1.25;
        if (a.staff && a.energy < 25) v *= 0.65; // tired
        return v;
    }

    move(a, dt) {
        if (a.state !== 'walk' || !a.way.length) return;
        const w = a.way[0];
        // Changing floor (stairs or lift): a short climb in real space, then on with the walk on the other floor
        const fw = floorOf(w.z), fa = a.floor ?? floorOf(a.pos.z);
        if (fw !== fa || a.climb) {
            if (!a.climb) {
                const from = realPos(a.pos), to = realPos(new THREE.Vector3(w.x, 0, w.z));
                a.climb = {from, to, t: 0, dur: Math.max(1.2, from.distanceTo(to) / this.walkSpeed(a)) * 1.3};
                a.yaw = Math.atan2(to.x - from.x, to.z - from.z);
            }
            const c = a.climb;
            c.t += dt;
            const k = Math.min(1, c.t / c.dur), p = c.from.clone().lerp(c.to, k);
            a.mesh.position.set(p.x, p.y - fa * FH, p.z + fa * S); // in the layer of the floor it started from
            if (k >= 1) {
                a.climb = null;
                a.pos.set(w.x, 0, w.z);
                a.tile = w.tile;
                a.way.shift();
                this.toFloor(a, fw);
                a.mesh.position.copy(a.pos);
                if (!a.way.length) {
                    a.state = 'idle';
                    const fn = a.then;
                    a.then = null;
                    fn && fn();
                }
            }
            return;
        }
        const dx = w.x - a.pos.x, dz = w.z - a.pos.z, d = Math.hypot(dx, dz);
        const step = this.walkSpeed(a) * dt;
        if (d > 0.001) a.yaw = Math.atan2(dx, dz);
        if (d <= step) {
            a.pos.set(w.x, 0, w.z);
            a.tile = w.tile;
            a.way.shift();
            if (!a.way.length) {
                a.state = 'idle';
                const fn = a.then;
                a.then = null;
                fn && fn();
            }
        } else a.pos.set(a.pos.x + dx / d * step, 0, a.pos.z + dz / d * step);
        a.mesh.position.x = a.pos.x;
        a.mesh.position.z = a.pos.z;
    }

    face(a, x, z) {
        a.yaw = Math.atan2(x - a.pos.x, z - a.pos.z);
    }

    /** People turn smoothly towards where they are going. */
    turn(a, dt) {
        if (a.yaw == null) return;
        let diff = a.yaw - a.mesh.rotation.y;
        diff = Math.atan2(Math.sin(diff), Math.cos(diff));
        a.mesh.rotation.y += diff * Math.min(1, dt * 12);
    }

    /** Wait `secs` (scaled by traits and tiredness), then run fn. */
    busy(a, secs, anim, fn, activity) {
        a.state = 'busy';
        a.timer = secs * (a.staff && a.energy < 25 ? 1.4 : 1);
        a.timerTotal = a.timer;
        a.anim = anim;
        a.after = fn;
        if (activity) a.activity = activity;
    }

    // ------------------------------------------------------------ cleanliness

    get cleanliness() {
        const dirty = this.seats.filter(s => s.dirty).length;
        return Math.max(0, 100 - dirty * 12 - this.litter.length * 9);
    }

    makeDirty(seat) {
        seat.dirty = true;
        const g = new THREE.Group();
        const part = (geo, color, x, y, z, o = {}) => {
            const m = new THREE.Mesh(geo, mat(color, {flat: false, roughness: 0.4, ...o}));
            m.position.set(x, y, z);
            g.add(m);
            return m;
        };
        // An empty plate with leftovers and sauce, the cutlery across it, a crumpled napkin and an empty glass
        part(new THREE.CylinderGeometry(0.16, 0.13, 0.025, 18), '#F2EEE6', 0, 0.012, 0);
        part(new THREE.TorusGeometry(0.145, 0.01, 5, 20), '#E3DDD2', 0, 0.026, 0).rotation.x = Math.PI / 2;
        const sauce = part(new THREE.CircleGeometry(0.06, 12), ['#C8301E', '#B08B5A', '#E9C46A'][seat.id % 3], 0.03, 0.027, -0.02, {opacity: 0.85});
        sauce.rotation.x = -Math.PI / 2;
        sauce.scale.set(1.4, 0.8, 1);
        for (let i = 0; i < 4; i++) part(new THREE.IcosahedronGeometry(0.015, 0), '#B08B5A', -0.05 + i * 0.03, 0.035, 0.05 - (i % 2) * 0.03, {flat: true});
        const fork = part(new THREE.BoxGeometry(0.012, 0.006, 0.2), '#C9CED4', 0.02, 0.032, 0, {metalness: 0.6, roughness: 0.3});
        fork.rotation.y = 0.6;
        const napkin = part(new THREE.DodecahedronGeometry(0.04, 0), seat.id % 2 ? '#F25F5C' : '#FFFFFF', -0.17, 0.03, 0.06, {flat: true});
        napkin.scale.set(1.2, 0.6, 1);
        part(new THREE.CylinderGeometry(0.03, 0.025, 0.09, 12, 1, true), '#FFFFFF', 0.15, 0.045, -0.1, {opacity: 0.35, roughness: 0.05});
        g.position.copy(seat.dishPos);
        g.traverse(o => (o.userData.litterAt = 'seat:' + seat.id)); // the player can tap it to clear the table
        this.fxAdd(g);
        seat.dirtyMesh = g;
    }

    cleanSeat(seat) {
        seat.dirty = false;
        seat.cleaning = null;
        if (seat.dirtyMesh) seat.dirtyMesh.parent?.remove(seat.dirtyMesh);
        seat.dirtyMesh = null;
    }

    /** Paper, fruit peels and crumbs appear around the tables now and then (fewer with trash bins). */
    dropLitter(near, kind = null) {
        const chance = this.spots.trash ? 0.14 / (1 + this.spots.trash) : 0.28;
        if (!kind && Math.random() > chance) return;
        if (this.litter.length > 16) return;
        const t = kind === 'puddle' || kind === 'snow' || kind === 'leaves' ? near : this.nearFree(near, Math.floor(Math.random() * 4));
        if (this.litter.some(l => l.x === t.x && l.z === t.z)) return;
        kind ||= ['paper', 'peel', 'crumbs', 'paper', 'peel', 'spill'][Math.floor(Math.random() * 6)];
        const mesh = litterMesh(kind);
        mesh.position.set(t.x + 0.5, 0, t.z + 0.5);
        mesh.rotation.y = Math.random() * Math.PI * 2;
        mesh.traverse(o => (o.userData.litterAt = t.x + ',' + t.z));
        this.fxAdd(mesh);
        this.litter.push({x: t.x, z: t.z, mesh, kind, claimed: null});
    }

    /** Rain and snow come in with the customers: puddles or snow by the door. */
    weatherDirt() {
        const rainy = ['drizzle', 'rain', 'hail'].includes(this.weather), snowy = ['light_snow', 'snow'].includes(this.weather);
        const leaves = !rainy && !snowy && this.season === 'autumn' && Math.random() < 0.3;
        if (!rainy && !snowy && !leaves) return;
        if ((this.weather === 'drizzle' || this.weather === 'light_snow') && Math.random() < 0.5) return; // light weather: less mess
        if (Math.random() > 0.45) return;
        const n = this.size, spots = [{x: this.door, z: n - 1}, {x: this.door - 1, z: n - 1}, {x: this.door + 1, z: n - 1}, {x: this.door, z: n - 2}].filter(p => p.x >= 0 && p.x < this.W && (this.free(p.x, p.z) || (p.x === this.door && p.z === n - 1)));
        const p = spots[Math.floor(Math.random() * spots.length)];
        if (p) this.dropLitter(p, leaves ? 'leaves' : rainy ? 'puddle' : 'snow');
    }

    removeLitter(l) {
        l.mesh.parent?.remove(l.mesh);
        this.litter = this.litter.filter(x => x !== l);
    }

    // ------------------------------------------------------------ customers

    /** People waiting outside: on benches if there are free places, otherwise standing in a line on the sidewalk. */
    get queue() {
        return this.agents.filter(a => a.role === 'customer' && a.state === 'queue');
    }

    joinQueue(c) {
        const n = this.size;
        const bench = this.benchSeats.find(b => !b.busy);
        const dx = this.door + 0.5;
        c.state = 'walk';
        c.activity = 'queue';
        c.queuedAt = this.t;
        if (bench) {
            bench.busy = c;
            c.bench = bench;
            this.walkPoints(c, [[bench.x, n + OUT + 2.6], [bench.x, bench.z + 0.3], [bench.x, bench.z]], () => {
                c.state = 'queue';
                c.anim = 'sit';
                c.yaw = c.mesh.rotation.y = bench.r * Math.PI / 2;
                c.mesh.position.y = -0.04;
                this.bubble(c, mood('meh'), 'bubble-thought', 2);
            });
        } else {
            const k = this.queue.length + this.agents.filter(a => a.activity === 'queue' && a.state === 'walk').length;
            const x = dx + 1.2 + k * 0.75;
            this.walkPoints(c, [[x, n + OUT + 0.6]], () => {
                c.state = 'queue';
                c.anim = 'idle';
                c.yaw = -Math.PI / 2;
            });
        }
    }

    /** The first in line comes in when a seat is free and new guests are welcome. */
    callFromQueue() {
        const free = this.seats.filter(s => !s.busy && !s.dirty);
        if (!free.length) return;
        const c = this.queue.sort((a, b) => a.queuedAt - b.queuedAt)[0];
        if (!c) return;
        if (c.bench) c.bench.busy = null;
        c.bench = null;
        c.mesh.position.y = 0;
        c.anim = 'walk';
        this.seatCustomer(c, free.sort((a, b) => b.beauty - a.beauty)[0]);
    }

    seatCustomer(c, seat) {
        const n = this.size, dx = this.door + 0.5;
        c.seat = seat;
        seat.busy = c;
        c.activity = 'arriving';
        const fromStreet = c.pos.z > n + OUT + 1.5;
        this.walkPoints(c, [...(fromStreet ? [[dx, n + OUT + 2.6]] : [[c.pos.x, c.pos.z + 0.01]]), [dx, n + 0.4], [dx, n - 0.5]], () => {
            c.tile = {x: this.door, z: n - 1};
            sfx.bell(); // the door bell
            this.weatherDirt();
            const ok = this.walkTo(c, [{x: seat.x, z: seat.z}], () => this.sitDown(c));
            if (!ok) this.leave(c);
        });
    }

    spawnCustomer() {
        const status = this.r.status || 'open';
        const seats = this.seats.filter(s => !s.busy && !s.dirty);
        // Full, or not letting new guests in: they queue outside (a few at most)
        if ((!seats.length || status === 'paused') && this.queue.length + this.agents.filter(a => a.activity === 'queue' && a.state === 'walk').length >= 8) return;
        if (!seats.length || status === 'paused') {
            const c = this.newCustomer();
            this.joinQueue(c);
            return;
        }
        // Some guests come by car when there is a car park: they park, walk to the sidewalk and come in from there
        const stalls = this.world.parkingStalls || [];
        if (!this.opts.visit && stalls.some(s => !s.busy) && Math.random() < 0.18 + 0.07 * stalls.length / 4) {
            const c = this.newCustomer();
            c.mesh.visible = false;
            c.state = 'driving';
            c.activity = 'arriving';
            c.byCar = true;
            const stall = this.world.traffic?.driveIn(c, (st) => {
                if (!this.agents.includes(c)) return this.world.traffic.driveOut(st);
                c.car = st;
                c.pos.set(st.x + 0.6, 0, st.z + 0.4);
                c.mesh.position.copy(c.pos);
                c.mesh.visible = true;
                this.walkPoints(c, [[st.x + 0.6, this.size + OUT + 1.0], [st.x + 0.6, this.size + OUT + 2.6]], () => this.arriveOnFoot(c));
            });
            if (stall) return;
            this.remove(c);
        }
        // People prefer the nicest seats
        seats.sort((a, b) => b.beauty - a.beauty + (Math.random() - 0.5) * 10);
        this.seatCustomer(this.newCustomer(), seats[0]);
    }

    /** A guest from the car park reaches the sidewalk: a seat if there is one (and guests are welcome), else the line. */
    arriveOnFoot(c) {
        const seats = this.seats.filter(s => !s.busy && !s.dirty);
        if (!seats.length || (this.r.status || 'open') !== 'open') return this.joinQueue(c);
        seats.sort((a, b) => b.beauty - a.beauty + (Math.random() - 0.5) * 10);
        this.seatCustomer(c, seats[0]);
    }

    newCustomer() {
        const c = this.addAgent('customer', null, Math.random() * 1000);
        const menu = this.r.menu;
        c.order = menu[Math.floor(Math.random() * menu.length)];
        c.mood = 62;
        c.name = ['Alba', 'Bea', 'Ciro', 'Dina', 'Eli', 'Furio', 'Gaia', 'Ivo', 'Luz', 'Milo', 'Nina', 'Otto', 'Pia', 'Rino', 'Sara', 'Teo', 'Vito', 'Zara'][Math.floor(Math.random() * 18)];
        const n = this.size;
        c.pos.set(Math.random() < 0.5 ? -7 - (this.world.parkingW || 0) : this.W + OUT + 7, 0, n + OUT + 2.6 + Math.random() * 0.6);
        c.mesh.position.copy(c.pos);
        c.tile = {x: Math.floor(c.pos.x), z: Math.floor(c.pos.z)};
        c.activity = 'arriving';
        return c;
    }

    sitDown(c) {
        const s = c.seat;
        const [fx, fz] = DIRS[s.r];
        c.pos.set(s.x + 0.5 - fx * 0.05, 0, s.z + 0.5 - fz * 0.05);
        c.mesh.position.set(c.pos.x, s.seatY, c.pos.z);
        c.mesh.rotation.y = c.yaw = s.r * Math.PI / 2;
        c.state = 'wantOrder';
        c.anim = 'sit';
        c.activity = 'wantOrder';
        c.seatedAt = this.t;
        // First impressions: the decor around the seat, how clean the room is, light at night
        c.mood += s.beauty * 0.8 - (100 - this.cleanliness) * 0.3 + (s.comfort - 3) * 3; // nicer chairs and tables are more comfortable
        // What each kind of restaurant's guests expect
        if (this.kindKey === 'luxury') c.mood += (s.comfort >= 6 ? 6 : -12) + (s.beauty >= 10 ? 6 : -10);
        if (this.kindKey === 'inn' && (this.spots.cozy.length || isNight(this.hour) && this.spots.light.length >= 2)) c.mood += 8;
        if (this.kindKey === 'fast_food') c.mood += 4;
        if (isNight(this.hour)) c.mood += Math.min(8, this.spots.light.length * 2) - 4;
        c.mood = Math.max(5, Math.min(100, c.mood));
        this.bubble(c, mood('alert'), 'bubble-alert');
        // A thought about the place, now and then
        setTimeout(() => {
            if (!this.agents.includes(c) || c.state === 'leaving') return;
            const th = this.thoughtFor(c);
            if (th && c.state === 'waitFood') this.bubble(c, th, 'bubble-thought', 2.5);
        }, 4000 + Math.random() * 4000);
    }

    thoughtFor(c) {
        if (this.cleanliness < 60) return mood('sick');
        if (this.effects.music > 0) return mood('music');
        if (this.effects.fun > 0) return mood('tv');
        if (c.seat.beauty >= 12) return mood('love');
        if (isNight(this.hour) && this.effects.cozy > 0) return mood('fire');
        return Math.random() < 0.5 ? mood('yummy') : null;
    }

    leave(c, angry = false) {
        if (c.seat) c.seat.busy = null;
        if (c.served) c.served.parent?.remove(c.served);
        c.served = null;
        this.orders = this.orders.filter(o => o.customer !== c);
        c.state = 'leaving';
        c.anim = 'walk';
        c.activity = 'leaving';
        c.mesh.position.y = 0;
        if (angry) {
            sfx.angry();
            this.bubble(c, mood('angry'), 'bubble-angry', 2.5);
            c.anim = 'angry';
        }
        const n = this.size, dx = this.door + 0.5;
        c.tile = c.seat ? {x: c.seat.x, z: c.seat.z} : c.tile;
        const outside = c.pos.z >= n - 0.1; // waiting outside: straight back to the street
        // Back to the street, then away on foot, or back to the car park and off by car
        const away = c.car ? [[c.car.x + 0.6, n + OUT + 2.6], [c.car.x + 0.6, c.car.z + 0.4]] : [[Math.random() < 0.5 ? -8 - (this.world.parkingW || 0) : this.W + OUT + 8, n + OUT + 2.8]];
        const out = () => this.walkPoints(c, [...(outside ? [] : [[dx, n + 0.4]]), [outside ? c.pos.x : dx, n + OUT + 2.6], ...away], () => this.remove(c));
        if (outside || !this.walkTo(c, [{x: this.door, z: n - 1}], out)) out();
    }

    remove(a) {
        if (a.bench) a.bench.busy = null;
        if (a.car) {
            this.world.traffic?.driveOut(a.car);
            a.car = null;
        }
        a.mesh.parent?.remove(a.mesh);
        this.clearBubble(a);
        this.agents = this.agents.filter(x => x !== a);
        if (this.selected === a) this.selected = null;
    }

    // ------------------------------------------------------------ staff behaviour

    /** Tired staff take a break: a sofa or the coffee machine if there is one, else a pause where they stand. */
    maybeRest(a) {
        if (a.energy > 15 && !a.forceBreak) return false;
        a.forceBreak = false;
        const spot = [...this.spots.coffee, ...this.spots.rest].find(s => s.front && !s.busy);
        a.task = 'rest';
        a.activity = 'rest';
        this.bubble(a, mood('sleep'), 'bubble-thought', 2);
        if (spot) {
            spot.busy = a;
            const ok = this.walkTo(a, [spot.front], () => {
                this.face(a, spot.x, spot.z);
                const coffee = this.spots.coffee.includes(spot);
                this.busy(a, coffee ? 4 : 7, coffee ? 'drink' : 'rest', () => {
                    spot.busy = null;
                    a.energy = 100;
                    this.bubble(a, mood(coffee ? 'coffee' : 'happy'), 'bubble-thought', 1.5);
                    this.goHome(a);
                });
            });
            if (ok) return true;
            spot.busy = null;
        }
        this.busy(a, 12, 'idle', () => {
            a.energy = 80;
            a.task = null;
            a.activity = 'idle';
        });
        return true;
    }

    thinkWaiter(w) {
        if (w.state !== 'idle' || w.task) return;
        // Closing time: clear every table left dirty (anyone's), then home
        if (w.closing) {
            if (!this.clearTable(w, true) && !this.seats.some(s => s.dirty && s.cleaning)) this.sendHome(w);
            return;
        }
        if (this.maybeRest(w)) return;
        if (!w.tables?.length) { // no table of their own: nothing they may do
            w.activity = 'noTable';
            if (!this.bubbles.has(w) && Math.random() < 0.004) this.bubble(w, mood('meh'), 'bubble-thought', 1.6);
            return;
        }
        // 1. A dish waiting on a counter
        for (const c of this.counters) for (const slot of c.slots) {
            if (!slot.dish || !slot.dish.mesh || slot.dish.claimed) continue;
            if (!this.servesSeat(w, slot.dish.customer?.seat)) continue; // another waiter's table
            slot.dish.claimed = w;
            w.task = 'serve';
            w.activity = 'serve';
            const ok = this.walkTo(w, [c.front], () => {
                const d = slot.dish;
                slot.dish = null;
                if (!d || d.customer.state === 'leaving' || !this.agents.includes(d.customer)) {
                    d && d.mesh.parent?.remove(d.mesh);
                    return this.goHome(w);
                }
                w.dish = d.mesh;
                w.mesh.add(d.mesh);
                d.mesh.position.set(0, 0.95, 0.32);
                w.anim = 'carry';
                const cust = d.customer;
                const ok2 = this.walkTo(w, cust.seat.service, () => {
                    this.face(w, cust.seat.dishPos.x, cust.seat.dishPos.z);
                    w.mesh.remove(d.mesh);
                    w.dish = null;
                    w.anim = 'idle';
                    if (cust.state !== 'waitFood') return this.goHome(w);
                    d.mesh.position.copy(cust.seat.dishPos);
                    this.fxAdd(d.mesh);
                    sfx.clink();
                    cust.served = d.mesh;
                    cust.state = 'eating';
                    cust.activity = 'eating';
                    cust.anim = 'eat';
                    cust.mood = Math.max(0, Math.min(100, cust.mood + ((cust.quality ?? 5) - 5) * 2.4));
                    if (this.skill(w) >= 7) cust.mood = Math.min(100, cust.mood + 3);
                    cust.timer = this.catalog.dishes[cust.order.dish].eat * (this.kind.eat || 1);
                    cust.wait = this.t - cust.seatedAt;
                    if (w.trait === 'cheerful') cust.mood = Math.min(100, cust.mood + 6);
                    this.clearBubble(cust);
                    w.energy -= 3;
                    this.goHome(w);
                });
                if (!ok2) {
                    w.mesh.remove(d.mesh);
                    w.dish = null;
                    this.goHome(w);
                }
            });
            if (!ok) {
                slot.dish.claimed = null;
                w.task = null;
            }
            return;
        }
        // 2. A customer waiting to order
        const cust = this.agents.find(a => a.role === 'customer' && a.state === 'wantOrder' && !a.claimed && this.servesSeat(w, a.seat));
        if (cust) {
            cust.claimed = w;
            w.task = 'order';
            w.activity = 'order';
            const ok = this.walkTo(w, cust.seat.service, () => {
                this.face(w, cust.pos.x, cust.pos.z);
                this.busy(w, (w.trait === 'chatty' ? 1.8 : 1.2) * this.skillTime(w), 'order', () => {
                    if (cust.state === 'wantOrder') {
                        cust.state = 'waitFood';
                        cust.activity = 'waitFood';
                        if (w.trait === 'chatty') cust.mood = Math.min(100, cust.mood + 6);
                        this.bubble(cust, dishIcon(cust.order.dish), 'bubble-dish');
                        this.orders.push({customer: cust, dish: cust.order.dish});
                    }
                    w.energy -= 2;
                    this.goHome(w);
                });
            });
            if (!ok) {
                cust.claimed = null;
                w.task = null;
            }
            return;
        }
        // 3. Clearing their own tables once the guests have gone
        this.clearTable(w);
    }

    /** Waiters clear their own tables: plates to the sink to be washed (quick), or to the counter without one (slow). */
    clearTable(w, any = false) {
        const seat = this.seats.find(s => s.dirty && !s.cleaning && !s.busy && (any || this.servesSeat(w, s)));
        if (!seat) return false;
        seat.cleaning = w;
        w.task = 'clean';
        w.activity = 'clean';
        const ok = this.walkTo(w, seat.service, () => {
            this.face(w, seat.dishPos.x, seat.dishPos.z);
            this.busy(w, 1.2 * this.skillTime(w), 'wipe', () => {
                if (!seat.dirty) return this.goHome(w);
                const plate = seat.dirtyMesh;
                seat.dirtyMesh = null;
                seat.dirty = false;
                seat.cleaning = null;
                if (plate) {
                    w.mesh.add(plate);
                    plate.position.set(0, 0.95, 0.32);
                    w.carrying = plate;
                }
                w.anim = 'carry';
                const sink = this.spots.sink.find(s => s.front);
                const target = sink ? sink.front : this.counters[0]?.front;
                const done = () => {
                    if (w.carrying) w.mesh.remove(w.carrying);
                    w.carrying = null;
                    w.energy -= 3;
                    this.goHome(w);
                };
                if (!target || !this.walkTo(w, [target], () => this.busy(w, (sink ? 2.2 / this.tiers.sink : 3.5) * this.skillTime(w), 'wash', done, 'wash'))) done();
            });
        });
        if (!ok) {
            seat.cleaning = null;
            w.task = null;
        }
        return ok;
    }

    /** Cleaners clean the floor: paper, peels, crumbs, puddles and snow at the entrance (never the tables). */
    thinkCleaner(a) {
        if (a.state !== 'idle' || a.task) return;
        if (!a.closing && this.maybeRest(a)) return;
        if (!a.closing && this.restock(a)) return;
        const wc = this.spots.toilet.find(x => x.dirty && !x.cleaning && !x.busy && x.front);
        if (wc) {
            wc.cleaning = a;
            a.task = 'toilet';
            a.activity = 'toiletClean';
            const ok = this.walkTo(a, [wc.front], () => {
                this.face(a, wc.x, wc.z);
                this.busy(a, 2.6 * this.skillTime(a), 'mop', () => {
                    wc.dirty = false;
                    wc.uses = 0;
                    wc.cleaning = null;
                    a.supplies = (a.supplies ?? 4) - 1;
                    this.goHome(a);
                });
            });
            if (!ok) {
                wc.cleaning = null;
                a.task = null;
            }
            return;
        }
        const l = this.litter.find(x => !x.claimed);
        if (!l) {
            if (a.closing && !this.litter.length && !this.spots.toilet.some(x => x.dirty)) this.sendHome(a); // all clean: home
            else if (!a.closing) this.waterRound(a); // nothing to clean: the plants' daily water
            return;
        }
        l.claimed = a;
        a.task = 'mop';
        a.activity = 'mop';
        const ok = this.walkTo(a, [{x: l.x, z: l.z}], () => this.busy(a, (l.kind === 'puddle' || l.kind === 'snow' || l.kind === 'spill' ? 2.6 : 1.6) * this.skillTime(a), 'mop', () => {
            this.removeLitter(l);
            a.energy -= 2;
            a.supplies = (a.supplies ?? 4) - 1;
            this.goHome(a);
        }));
        if (!ok) {
            l.claimed = null;
            a.task = null;
        }
    }

    /** Closed: the staff go home (and are not paid for it); open again: they come back to work. */
    setStatus(status) {
        const was = this.r.status || 'open';
        this.r.status = status;
        if (status === 'closed' && was !== 'closed') {
            for (const c of this.agents.filter(a => a.role === 'customer' && a.seat && a.state !== 'leaving')) this.leave(c); // guests inside finish up and go
            this.orders = [];
            // The kitchen and the manager leave at once; waiters and cleaners first tidy up (tables, floor, restrooms)
            for (const a of this.agents) if (a.staff) {
                if (a.role === 'waiter' || a.role === 'cleaner') a.closing = true;
                else this.sendHome(a);
            }
        }
        if (status !== 'closed' && was === 'closed') for (const a of this.agents) if (a.staff) {
            a.closing = false;
            if (a.away) this.backToWork(a);
        }
    }

    sendHome(a, instant = false) {
        a.away = true;
        a.closing = false;
        a.task = 'home';
        a.activity = 'home';
        if (a.dish) a.mesh.remove(a.dish);
        if (a.carrying) a.mesh.remove(a.carrying);
        a.dish = a.carrying = null;
        a.after = null;
        const n = this.size, dx = this.door + 0.5;
        const hide = () => {
            a.mesh.visible = false;
            a.state = 'idle';
        };
        if (instant) return hide();
        a.anim = 'walk';
        const out = () => this.walkPoints(a, [[dx, n + 0.4], [dx, n + OUT + 2.6], [this.W + OUT + 9, n + OUT + 2.8]], hide);
        if (!this.walkTo(a, [{x: this.door, z: n - 1}], out)) out();
    }

    backToWork(a) {
        const n = this.size, dx = this.door + 0.5;
        a.away = false;
        a.mesh.visible = true;
        a.pos.set(-7, 0, n + OUT + 2.7);
        a.mesh.position.copy(a.pos);
        a.task = 'arrive';
        a.activity = 'arriving';
        a.anim = 'walk';
        this.walkPoints(a, [[dx, n + OUT + 2.6], [dx, n + 0.4], [dx, n - 0.5]], () => {
            a.tile = {x: this.door, z: n - 1};
            a.task = null;
            this.goHome(a);
        });
    }

    /** The manager walks the room and welcomes guests (a little mood boost); the rest of the time they watch. */
    thinkManager(m) {
        if (m.state !== 'idle' || m.task) return;
        const guest = this.agents.find(a => a.role === 'customer' && (a.state === 'wantOrder' || a.state === 'waitFood') && !a.welcomed);
        if (guest && Math.random() < 0.02) {
            guest.welcomed = true;
            m.task = 'greet';
            m.activity = 'order';
            if (!this.walkTo(m, guest.seat.service, () => {
                this.face(m, guest.pos.x, guest.pos.z);
                this.busy(m, 1.4, 'talk', () => {
                    guest.mood = Math.min(100, guest.mood + 6);
                    this.bubble(guest, mood('happy'), 'bubble-thought', 1.6);
                    this.goHome(m);
                });
            })) m.task = null;
        }
    }

    goHome(a) {
        a.task = null;
        a.anim = 'walk';
        a.activity = 'idle';
        if (!this.walkTo(a, [a.home], () => (a.anim = 'idle'))) a.state = 'idle';
    }

    thinkChef(c) {
        if (c.state !== 'idle' || c.task || !c.stove) return;
        if (!this.orders.length) {
            this.maybeRest(c);
            return;
        }
        const order = this.orders.shift();
        c.task = 'cook';
        c.activity = 'cook';
        const reached = this.walkTo(c, [c.stove.front], () => {
            const st = c.stove;
            c.cooking = order.dish;
            const f = DIRS[st.r];
            this.face(c, st.front.x + 0.5 - f[0], st.front.z + 0.5 - f[1]);
            const fridge = Math.pow(0.9, Math.min(2, this.spots.fridge));
            const time = this.catalog.dishes[order.dish].cook / st.speed * fridge * (c.trait === 'careful' ? 1.1 : 1) * this.skillTime(c);
            // How good the dish comes out: the cook's skill and the equipment (stove, fridge, counter)
            order.quality = Math.max(0, Math.min(10, this.skill(c) * 0.55 + st.tier * 0.8 + this.tiers.fridge * 0.45 + this.tiers.counter * 0.3 + (c.trait === 'careful' ? 0.8 : 0)));
            this.bubble(c, dishIcon(order.dish), 'bubble-cook', 0, () => 1 - c.timer / Math.max(0.01, c.timerTotal));
            sfx.sizzle();
            this.busy(c, time, 'cook', () => {
                this.clearBubble(c);
                c.cooking = null;
                c.energy -= 4;
                const free = this.counters.flatMap(k => k.slots.map(s => ({k, s}))).filter(o => !o.s.dish);
                if (!free.length) { // counters full: put the order back and try again soon
                    this.orders.unshift(order);
                    this.busy(c, 1.5, 'idle', () => (c.task = null));
                    return;
                }
                const target = free[0];
                target.s.dish = {reserved: true};
                const mesh = dishModel(order.dish);
                mesh.traverse(o => o.isMesh && (o.castShadow = true));
                c.mesh.add(mesh);
                mesh.position.set(0, 0.95, 0.32);
                c.anim = 'carry';
                const ok = this.walkTo(c, [target.k.front], () => {
                    c.mesh.remove(mesh);
                    mesh.position.copy(target.s.pos);
                    this.fxAdd(mesh);
                    target.s.dish = {mesh, customer: order.customer, claimed: null};
                    order.customer.quality = order.quality;
                    this.goHome(c);
                });
                if (!ok) {
                    c.mesh.remove(mesh);
                    target.s.dish = null;
                    this.goHome(c);
                }
            });
        });
        if (!reached) {
            this.orders.unshift(order);
            c.task = null;
        }
    }

    // ------------------------------------------------------------ the player's interactions (pie menu)

    agentById(id) {
        return this.agents.find(a => a.id === id) || null;
    }

    /** What the player can do with a person. */
    actionsFor(a) {
        if (a.role === 'customer') {
            if (a.state === 'leaving' || a.state === 'walk') return [];
            return [{id: 'greet', icon: 'greet', done: a.greeted}, {id: 'joke', icon: 'laugh', done: a.joked}, {id: 'dessert', icon: 'candy', done: a.treated}];
        }
        return [{id: 'cheer', icon: 'strong', done: a.cheeredAt && this.t - a.cheeredAt < 60}, {id: 'break', icon: 'coffee'}, {id: 'praise', icon: 'star', done: a.praisedAt && this.t - a.praisedAt < 60}];
    }

    /** Runs an interaction; returns a message key for the toast (or null). */
    interact(a, action) {
        const say = (text, cls = 'bubble-thought') => this.bubble(a, text, cls, 2.2);
        switch (action) {
            case 'greet':
                a.greeted = true;
                a.mood = Math.min(100, a.mood + 12);
                say(mood('happy'));
                a.anim2 = {name: 'wave', until: this.t + 1.5};
                return 'act_greet';
            case 'joke':
                a.joked = true;
                if (Math.random() < 0.7) {
                    a.mood = Math.min(100, a.mood + 18);
                    say(mood('laugh'));
                    a.anim2 = {name: 'laugh', until: this.t + 2};
                    return 'act_joke_ok';
                }
                a.mood = Math.max(0, a.mood - 8);
                say(mood('meh'));
                return 'act_joke_bad';
            case 'dessert':
                a.treated = true;
                a.mood = Math.min(100, a.mood + 25);
                say(mood('candy'));
                a.anim2 = {name: 'cheer', until: this.t + 1.5};
                return 'act_dessert';
            case 'cheer':
                a.cheeredAt = this.t;
                a.energy = Math.min(100, a.energy + 30);
                say(mood('strong'));
                a.anim2 = {name: 'cheer', until: this.t + 1.2};
                return 'act_cheer';
            case 'praise':
                a.praisedAt = this.t;
                a.energy = Math.min(100, a.energy + 15);
                say(mood('love'));
                a.anim2 = {name: 'cheer', until: this.t + 1.2};
                return 'act_praise';
            case 'break':
                a.forceBreak = true;
                return 'act_break';
        }
        return null;
    }

    /** What the player can do with a piece of furniture (by id), or with litter / a dirty table. */
    itemActions(id) {
        const it = this.layout.find(x => x.id === id);
        if (!it) return [];
        const tag = (this.catalog.tags || {})[it.k];
        const out = [];
        const seat = this.seats.find(s => s.dirty && this.isTableOf(s, it));
        if (seat || (this.catalog.items[it.k].cat === 'table' && this.seats.some(s => s.dirty && this.isTableOf(s, it)))) out.push({id: 'clean', icon: 'clean'});
        if (tag === 'toilet' && this.spots.toilet.find(x => x.id === id)?.dirty) out.push({id: 'clean', icon: 'clean'});
        if (tag === 'music') out.push({id: 'music', icon: 'music', done: this.effects.music > 0});
        if (tag === 'fun') out.push({id: 'fun', icon: 'tv', done: this.effects.fun > 0});
        if (tag === 'cozy') out.push({id: 'cozy', icon: 'fire', done: this.effects.cozy > 0});
        const plant = this.plants?.find(p => p.id === id);
        if (plant) out.push({id: 'water', icon: 'water', done: plant.lastWater === this.day && !plant.thirsty});
        return out;
    }

    isTableOf(seat, table) {
        const [dx, dz] = DIRS[seat.r];
        return this.world.footprint(table, this.catalog.items).tiles.some(([x, z]) => x === seat.x + dx && z === seat.z + dz);
    }

    interactItem(id, action) {
        const it = this.layout.find(x => x.id === id);
        if (!it) return null;
        const pos = new THREE.Vector3(it.x + 0.5, 1.2, it.z + 0.5);
        switch (action) {
            case 'clean':
                this.seats.filter(s => s.dirty && this.isTableOf(s, it)).forEach(s => this.cleanSeat(s));
                const wcItem = this.spots.toilet.find(x => x.id === id);
                if (wcItem) {
                    wcItem.dirty = false;
                    wcItem.uses = 0;
                }
                sfx.sparkle();
                this.floater(pos, mood('sparkle'), 'floater-fx');
                return 'act_clean';
            case 'music':
                this.effects.music = 90;
                this.notes(it);
                return 'act_music';
            case 'fun':
                this.effects.fun = 90;
                this.floater(pos, mood('tv'), 'floater-fx');
                return 'act_fun';
            case 'cozy':
                this.effects.cozy = 180;
                this.floater(pos, mood('fire'), 'floater-fx');
                return 'act_cozy';
            case 'water': {
                const plant = this.plants?.find(p => p.id === id);
                if (plant) this.water(plant);
                else this.floater(pos, mood('water'), 'floater-fx');
                for (const a of this.agents) if (a.role === 'customer' && a.mood != null) a.mood = Math.min(100, a.mood + 2);
                return 'act_water';
            }
        }
        return null;
    }

    /** Clicking litter cleans it at once. */
    cleanLitterAt(key) {
        if (String(key).startsWith('seat:')) { // the player clears a table
            const seat = this.seats.find(x => 'seat:' + x.id === key);
            if (!seat?.dirty) return false;
            this.floater(seat.dishPos.clone(), mood('sparkle'), 'floater-fx');
            sfx.sparkle();
            this.cleanSeat(seat);
            return true;
        }
        const l = this.litter.find(x => x.x + ',' + x.z === key);
        if (!l) return false;
        this.floater(l.mesh.position.clone(), mood('sparkle'), 'floater-fx');
        sfx.sparkle();
        this.removeLitter(l);
        return true;
    }

    notes(it) {
        let n = 0;
        const timer = setInterval(() => {
            if (!this.running || this.effects.music <= 0 || n++ > 40) return clearInterval(timer);
            this.floater(new THREE.Vector3(it.x + 0.3 + Math.random() * 0.6, 1.4, it.z + 0.5), mood(n % 2 ? 'note' : 'music'), 'floater-note');
        }, 900);
    }

    // ------------------------------------------------------------ bubbles over heads (HTML overlay)

    /** A bubble over a person: speech (orders, alerts), thought clouds (moods), stars, cooking with a progress ring. */
    bubble(a, html, cls, ttl = 0, progress = null) {
        let el = this.bubbles.get(a);
        if (!el) {
            el = document.createElement('div');
            el.innerHTML = '<div class="bubble-body"><div class="bubble-ring"></div><div class="bubble-pic"></div></div><i class="trail t1"></i><i class="trail t2"></i>';
            this.world.overlay.appendChild(el);
            this.bubbles.set(a, el);
        }
        el.className = 'bubble ' + cls + ' pop';
        void el.offsetWidth; // restart the pop-in animation
        el.querySelector('.bubble-pic').innerHTML = html;
        el.ttl = ttl;
        el.progress = progress;
        el.classList.toggle('has-ring', !!progress);
    }

    clearBubble(a) {
        const el = this.bubbles.get(a);
        if (!el) return;
        this.bubbles.delete(a);
        el.classList.add('out'); // shrink and fade, then go
        setTimeout(() => el.remove(), 260);
    }

    floater(pos, text, cls = 'floater-coin') {
        const el = document.createElement('div');
        el.className = 'floater ' + cls;
        if (text.startsWith('<svg')) el.innerHTML = text;
        else el.innerHTML = '<span class="coin-mini"></span>' + text;
        this.world.overlay.appendChild(el);
        const rp = realPos(pos);
        const p = this.world.project(rp.setY(Math.max(rp.y, 2 + floorOf(pos.z) * FH)));
        el.style.transform = `translate(${p.x}px, ${p.y}px)`;
        setTimeout(() => el.remove(), 1600);
    }

    // ------------------------------------------------------------ the loop

    update(realDt, t) {
        const dt = realDt * this.speed;
        this.t += dt;
        this.clock += dt * GAME_MINUTES_PER_SECOND; // total game minutes since the restaurant opened (the calendar)
        for (const k in this.effects) this.effects[k] = Math.max(0, this.effects[k] - dt);
        const status = this.r.status || 'open';
        if (dt > 0 && !this.problems.length && status !== 'closed') {
            // Kind of restaurant, menu signs outside, the hour and the weather change how many people come
            this.spawnIn -= dt * hourFactor(this.hour, this.kindKey) * ({drizzle: 0.9, rain: 0.65, hail: 0.6, light_snow: 0.85, snow: 0.6, sunny: 1.1}[this.weather] || 1) * (this.kind.customers || 1) * (1 + 0.08 * Math.min(2, this.spots.attract));
            if (this.spawnIn <= 0) {
                this.spawnIn = arrivalSeconds(this.r.popularity) * (0.7 + Math.random() * 0.6);
                this.spawnCustomer();
            }
        }
        if (dt > 0) {
            if (status === 'open') this.callFromQueue();
            for (const q of this.queue) {
                if (status === 'closed') { // closed: nobody waits
                    if (q.bench) q.bench.busy = null;
                    q.bench = null;
                    q.mesh.position.y = 0;
                    this.leave(q);
                    continue;
                }
                q.mood -= (q.bench ? 0.12 : 0.3) * dt;
                // The food truck sells a snack to whoever is waiting
                const truck = this.spots.truck[0];
                if (truck && !q.snacked && this.t - q.queuedAt > 3) {
                    q.snacked = true;
                    q.mood = Math.min(100, q.mood + 14);
                    if (!this.opts.visit) this.batch.tips += 5;
                    this.floater(q.mesh.position.clone(), '+5');
                    this.bubble(q, dishIcon('fries'), 'bubble-thought', 1.8);
                }
                if (q.mood <= 15) {
                    if (q.bench) q.bench.busy = null;
                    q.bench = null;
                    q.mesh.position.y = 0;
                    this.bubble(q, mood('angry'), 'bubble-angry', 2);
                    this.leave(q);
                }
            }
        }
        if (dt > 0) this.checkPlants();
        for (const a of [...this.agents]) {
            if (dt > 0) {
                this.move(a, dt);
                if (a.state === 'busy') {
                    a.timer -= dt;
                    if (a.timer <= 0) {
                        a.state = 'idle';
                        const fn = a.after;
                        a.after = null;
                        fn && fn();
                    }
                }
                if (a.staff) {
                    // Energy: work tires, a 'tireless' worker less so
                    const drain = (a.task ? 0.35 : 0.08) * (a.trait === 'tireless' ? 0.5 : 1) * (a.trait === 'calm' ? 0.85 : 1);
                    if (a.away) a.energy = Math.min(100, a.energy + 2 * dt); // resting at home
                    else if (a.task !== 'rest') a.energy = Math.max(0, a.energy - drain * dt);
                    if (a.energy < 25 && !this.bubbles.has(a) && Math.random() < dt * 0.05) this.bubble(a, mood('tired'), 'bubble-thought', 1.8);
                }
                if (a.away) { /* at home */ } else if (a.role === 'waiter') this.thinkWaiter(a);
                else if (a.role === 'chef') this.thinkChef(a);
                else if (a.role === 'cleaner') this.thinkCleaner(a);
                else if (a.role === 'manager') this.thinkManager(a);
                else if (a.role === 'customer') this.updateCustomer(a, dt);
            }
            if (dt > 0) this.turn(a, realDt * Math.max(1, this.speed));
            let anim = a.state === 'walk' ? (a.anim === 'carry' ? 'carry' : 'walk') : a.anim === 'walk' ? 'idle' : a.anim;
            if (a.anim2 && this.t < a.anim2.until && a.state !== 'walk') anim = a.anim2.name;
            a.clock = (a.clock || a.id * 3.1) + realDt * this.speed;
            animatePerson(a.mesh, anim, a.clock, {seated: a.anim === 'sit' || a.anim === 'eat', mood: a.mood, energy: a.energy, reading: a.state === 'wantOrder'});
        }
        for (const l of this.litter) { // rain drips from coats into the puddles
            const rp = l.mesh.userData.ripple;
            if (rp) rp.scale.setScalar(1 + ((this.world.time * 0.7 + l.x * 0.37) % 1) * 2.5);
        }
        for (const [a, el] of this.bubbles) {
            const wp = a.mesh.getWorldPosition(new THREE.Vector3());
            const p = this.world.project(wp.setY(wp.y + 1.9));
            if (!a.mesh.parent?.visible || !a.mesh.visible) p.visible = false; // on a floor that is hidden
            el.style.transform = `translate(${p.x}px, ${p.y}px)`;
            el.style.display = p.visible ? '' : 'none';
            if (el.progress) el.style.setProperty('--p', Math.max(0, Math.min(1, el.progress())).toFixed(3));
            if (el.ttl) {
                el.ttl -= realDt;
                if (el.ttl <= 0) this.clearBubble(a);
            }
        }
        if (this.onTick) this.onTick();
    }

    updateCustomer(c, dt) {
        if (c.state === 'wantOrder' || c.state === 'waitFood') {
            // Waiting wears the mood down; music, fun and a cozy fire soften it
            let decay = 0.55 - (this.effects.music > 0 ? 0.2 : 0) - (this.effects.fun > 0 ? 0.12 : 0) - (this.effects.cozy > 0 && isNight(this.hour) ? 0.1 : 0) - (this.spots.snack ? 0.05 : 0);
            c.mood = Math.max(0, c.mood - Math.max(0.12, decay) * dt);
            if (c.mood <= 0) {
                if (!this.opts.visit) {
                    this.batch.ratings.push(1);
                    this.batch.reviews.push({stars: 1, dish: c.order.dish, name: c.name, good: [], bad: ['slow', ...(this.cleanliness < 60 ? ['dirty'] : [])]});
                }
                if (c.seat) this.dropLitter({x: c.seat.x, z: c.seat.z});
                this.leave(c, true);
            }
        } else if (c.state === 'eating') {
            if (this.effects.music > 0) c.mood = Math.min(100, c.mood + 0.3 * dt);
            c.timer -= dt;
            if (c.timer <= 0) this.pay(c);
        }
    }

    pay(c) {
        const lvl = c.order.level || 1;
        let price = Math.round(this.catalog.dishes[c.order.dish].price * (1 + 0.25 * (lvl - 1)) * (this.kind.price || 1));
        if (c.byCar) price = Math.round(price * (1 + (this.catalog.carBonus ?? 0.8))); // guests from the car park order more
        // About a third of the guests need the restroom before leaving
        const toilet = this.toiletFor(c);
        const rating = starsForMood(c.mood);
        const review = this.reviewFor(c, rating);
        let tip = rating >= 4 ? Math.max(1, Math.round(price * (rating === 5 ? 0.15 : 0.06))) : 0;
        if (rating === 3 && Math.random() < 0.25) tip = Math.max(1, Math.round(price * 0.03)); // a few coins left on the table
        if (tip && this.spots.register) tip = Math.round(tip * 1.25);
        if (tip && c.byCar) tip = Math.round(tip * 1.5);
        if (!this.opts.visit) {
            this.batch.dishes[c.order.dish] = (this.batch.dishes[c.order.dish] || 0) + 1;
            if (c.byCar) this.batch.cars = (this.batch.cars || 0) + 1;
            this.batch.tips += tip;
            this.batch.ratings.push(rating);
            this.batch.reviews.push(review);
            this.opts.onServe && this.opts.onServe(price + tip, rating, review);
        }
        this.floater(c.pos, '+' + price + (tip ? ` <small>+${tip}</small>` : ''), tip ? 'floater-coin floater-tip' : 'floater-coin');
        if (!this.opts.visit) sfx.coin();
        if (c.served) c.served.parent?.remove(c.served);
        c.served = null;
        const seat = c.seat;
        this.makeDirty(seat);
        this.dropLitter({x: seat.x, z: seat.z});
        if (toilet) this.useToilet(c, toilet);
        else this.leave(c);
        this.bubble(c, starsHtml(rating), 'bubble-stars', 2.4);
        if (rating === 5) {
            c.anim2 = {name: 'cheer', until: this.t + 1.2};
            sfx.cheer();
        }
    }

    /**
     * A review: stars from the mood, plus what the customer liked and disliked (factor keys the app turns into
     * sentences: wait, food, decor, cleanliness, ambience, staff, light).
     */
    reviewFor(c, stars) {
        const good = [], bad = [];
        const wait = c.wait ?? 60;
        if (wait < 22) good.push('fast'); else if (wait > 50) bad.push('slow');
        const q = c.quality ?? 5, lvl = c.order.level || 1;
        if (q >= 7.5 || lvl >= 3) good.push('tasty'); else if (q <= 3.5 || (lvl === 1 && Math.random() < 0.3)) bad.push('plain');
        if ((c.seat.comfort || 2) >= 6) good.push('comfy');
        if (c.toiletIssue) bad.unshift(c.toiletIssue);
        if (c.toiletGood) good.push('toilet_clean');
        if (c.seat.beauty >= 10) good.push('pretty'); else if (c.seat.beauty <= 1) bad.push('bare');
        const clean = this.cleanliness;
        if (clean >= 92) good.push('clean'); else if (clean < 65) bad.push('dirty');
        if (this.effects.music > 0 || this.effects.fun > 0) good.push('ambience');
        if (c.greeted || c.treated || (c.joked && c.mood > 60)) good.push('friendly');
        if (isNight(this.hour)) (this.spots.light.length >= 2 ? (this.effects.cozy > 0 ? good.push('cozy') : null) : bad.push('dark'));
        return {stars, dish: c.order.dish, name: c.name, good: good.slice(0, 3), bad: bad.slice(0, 3)};
    }

    /** Does this guest need the restroom? Returns the toilet to use (null if not needed or there is none). */
    toiletFor(c) {
        if (Math.random() > 0.35) return null;
        const toilets = this.spots.toilet.filter(x => x.front);
        if (!toilets.length) {
            c.mood = Math.max(0, c.mood - 16);
            c.toiletIssue = 'no_toilet';
            return null;
        }
        const t = toilets.find(x => !x.busy && !x.dirty) || toilets.find(x => !x.busy) || toilets[0];
        if (t.dirty) {
            c.mood = Math.max(0, c.mood - 10);
            c.toiletIssue = 'toilet_dirty';
        } else if (this.spots.washbasin.length && Math.random() < 0.5) c.toiletGood = true;
        return t;
    }

    /** To the toilet, then the washbasin and the dryer if there are any, then home. Toilets get dirty with use. */
    useToilet(c, t) {
        const seat = c.seat;
        seat.busy = null;
        c.tile = {x: seat.x, z: seat.z};
        c.seat = null;
        c.mesh.position.y = 0;
        c.anim = 'walk';
        c.activity = 'toilet';
        const after = () => {
            const basin = this.spots.washbasin.find(x => x.front);
            const dryer = this.spots.dryer.find(x => x.front);
            const toDryer = () => dryer && this.walkTo(c, [dryer.front], () => this.busy(c, 1.2, 'wipe', () => this.leave(c))) || this.leave(c);
            if (basin && this.walkTo(c, [basin.front], () => {
                this.face(c, basin.x, basin.z);
                this.busy(c, 1.4, 'wash', toDryer);
            })) return;
            toDryer();
        };
        const ok = this.walkTo(c, [t.front], () => {
            this.face(c, t.x, t.z);
            t.busy = c;
            this.busy(c, 3, 'idle', () => {
                t.busy = null;
                t.uses = (t.uses || 0) + 1;
                if (t.uses >= 6) t.dirty = true; // needs a clean
                after();
            });
        });
        if (!ok) this.leave(c);
    }

    /** For the info card: what this person is doing, in a key the app translates. */
    describe(a) {
        return {
            id: a.id, role: a.role, name: a.staff ? a.staff.name : a.name, trait: a.trait, activity: a.activity,
            mood: a.mood != null ? Math.round(a.mood) : null, energy: a.energy != null ? Math.round(a.energy) : null,
            order: a.order ? a.order.dish : null,
            tables: a.role === 'waiter' ? (a.tables || []).map(id => this.tables.find(t => t.id === id)?.num).filter(Boolean) : null,
            color: a.staff?.color,
        };
    }
}
