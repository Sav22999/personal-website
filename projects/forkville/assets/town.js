/*
 * Forkville — the neighbourhood around the restaurant: the street with its sidewalks and lamps, houses that
 * multiply (and grow taller) as the restaurant levels up, the restaurant car park, and the life of the street:
 * cars and people passing by, more at rush hours, few at night.
 *
 * Coordinates: the room is W × D tiles from (0, 0); the front wall (with the door) is at z = D and the street
 * runs along x in front of it. The garden is west of the room (x < 0); the car park, when bought, is west of it.
 */
import * as THREE from '../vendor/three.module.min.js';
import {mat, glowMat, PALETTE, personModel, animatePerson, carModel, lightPool} from './models.js';

const OUT = 3; // the outdoor strip around the room (OUTDOOR in lib/catalog.php)
const PARK_SPACES = [0, 4, 8, 12]; // spaces per car park level (PARKING in lib/catalog.php)
const STALL_W = 1.45;

/** Stone paving for the terrace. */
export function pavingTexture(w, d) {
    const c = document.createElement('canvas');
    c.width = c.height = 128;
    const g = c.getContext('2d');
    g.fillStyle = '#C9BBA4';
    g.fillRect(0, 0, 128, 128);
    for (let y = 0; y < 4; y++) for (let x = -1; x < 4; x++) {
        g.fillStyle = ['#E3D6C1', '#D8C9B0', '#EADFCB', '#DCCDB5'][(x + y * 3 + 8) % 4];
        g.fillRect(x * 32 + (y % 2) * 16 + 2, y * 32 + 2, 28, 28);
    }
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    t.wrapS = t.wrapT = THREE.RepeatWrapping;
    t.repeat.set(w / 2, d / 2);
    return t;
}

/** Asphalt with painted parking lines. */
function lotTexture(spaces) {
    const c = document.createElement('canvas');
    c.width = 64 * spaces;
    c.height = 128;
    const g = c.getContext('2d');
    g.fillStyle = '#55524F';
    g.fillRect(0, 0, c.width, 128);
    for (let i = 0; i < 400; i++) { // a little grain
        g.fillStyle = Math.random() < 0.5 ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.08)';
        g.fillRect(Math.random() * c.width, Math.random() * 128, 2, 2);
    }
    g.fillStyle = '#F4F1E8';
    for (let i = 0; i <= spaces; i++) g.fillRect(i * 64 - 2, 0, 4, 104);
    g.fillRect(0, 0, c.width, 4);
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    return t;
}

const add = (world, o) => {
    o.traverse(m => m.isMesh && (m.castShadow = true, m.receiveShadow = true));
    world.outdoor.add(o);
    return o;
};

function box(w, h, d, material, x, y, z) {
    const m = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), material);
    m.position.set(x, y + h / 2, z);
    return m;
}

/** A street lamp: a pole, a lantern that glows at night and its pool of light on the ground. */
export function streetLamp(style = 0) {
    const g = new THREE.Group();
    const iron = mat('#2F3A3A');
    g.add(box(0.1, 2.6, 0.1, iron, 0, 0, 0), box(0.3, 0.12, 0.3, iron, 0, 0, 0));
    if (style === 1) { // double arm
        for (const sx of [-1, 1]) {
            g.add(box(0.5, 0.05, 0.05, iron, sx * 0.25, 2.5, 0));
            const head = new THREE.Mesh(new THREE.SphereGeometry(0.15, 10, 8), glowMat('#FFE7A8'));
            head.position.set(sx * 0.5, 2.4, 0);
            g.add(head);
        }
    } else {
        const head = new THREE.Mesh(new THREE.CylinderGeometry(0.16, 0.12, 0.32, 6), glowMat('#FFE7A8'));
        head.position.y = 2.75;
        const roof = new THREE.Mesh(new THREE.ConeGeometry(0.24, 0.18, 6), iron);
        roof.position.y = 3.0;
        g.add(head, roof);
    }
    g.add(lightPool(1.9));
    return g;
}

/** The whole neighbourhood, rebuilt with the room (it depends on the room's size, the level and the car park). */
export function buildNeighbourhood(world, r) {
    const W = r.size, D = r.depth || r.size, level = r.level || 1, parking = Math.min(3, r.parking || 0);
    const rnd = (n) => Math.abs(Math.sin(n * 91.7 + W * 3 + D) * 4371.3) % 1;
    const roadZ = D + OUT + 4.5;
    const spaces = PARK_SPACES[parking];
    const lotW = spaces ? spaces * STALL_W + 0.6 : 0;
    const lotX1 = -5.6, lotX0 = lotX1 - lotW;
    world.parkingW = lotW;
    world.street = {roadZ, nearWalk: D + OUT + 2.6, farWalk: roadZ + 3.1, laneIn: roadZ - 1, laneOut: roadZ + 1, x0: -36 - lotW, x1: W + 40, D, W};

    // Sidewalks, road, the stripes and a zebra crossing at the door
    const len = W + 90 + lotW, cx = (W - lotW) / 2;
    add(world, box(len, 0.06, 2.2, mat('#E4D6C1'), cx, -0.03, D + 1.4 + OUT));
    add(world, box(len, 0.04, 4, mat('#5B5754'), cx, -0.05, roadZ));
    add(world, box(len, 0.06, 2.2, mat('#E4D6C1'), cx, -0.03, roadZ + 3.1));
    for (const z of [roadZ - 2.05, roadZ + 2.05]) add(world, box(len, 0.1, 0.12, mat('#BDB2A1'), cx, -0.04, z)); // kerbs
    for (let x = cx - len / 2 + 1; x < cx + len / 2; x += 3) {
        if (Math.abs(x - ((world.door ?? Math.floor(W / 2)) + 0.5)) < 2) continue;
        add(world, box(1.4, 0.05, 0.14, mat('#F4E9C8'), x, -0.02, roadZ));
    }
    for (let i = 0; i < 6; i++) add(world, box(0.34, 0.05, 3.4, mat('#F4F1E8'), (world.door ?? Math.floor(W / 2)) + 0.5 - 1.1 + i * 0.44, -0.02, roadZ));

    // The terrace in front (stone paving) and the path to the door across it
    const patio = new THREE.Mesh(new THREE.PlaneGeometry(W + OUT, OUT), new THREE.MeshStandardMaterial({map: pavingTexture(W + OUT, OUT), roughness: 0.95}));
    patio.rotation.x = -Math.PI / 2;
    patio.position.set((W + OUT) / 2, -0.035, D + OUT / 2);
    patio.receiveShadow = true;
    world.outdoor.add(patio);
    add(world, box(1.0, 0.05, OUT + 0.4, mat('#D9C7AC'), (world.door ?? Math.floor(W / 2)) + 0.5, -0.045, D + OUT / 2 + 0.1));

    // The car park: one row of spaces, open to the street, with a sign and its own lamps
    world.parkingStalls = [];
    if (spaces) {
        const zFront = D + OUT + 0.3, depth = 3.0;
        const lot = new THREE.Mesh(new THREE.PlaneGeometry(lotW, depth), new THREE.MeshStandardMaterial({map: lotTexture(spaces), roughness: 0.95}));
        lot.rotation.x = -Math.PI / 2;
        lot.position.set((lotX0 + lotX1) / 2, -0.03, zFront - depth / 2);
        lot.receiveShadow = true;
        world.outdoor.add(lot);
        add(world, box(lotW + 0.2, 0.14, 0.14, mat('#BDB2A1'), (lotX0 + lotX1) / 2, -0.03, zFront - depth - 0.05));
        for (let i = 0; i < spaces; i++) world.parkingStalls.push({x: lotX0 + 0.3 + STALL_W * (i + 0.5), z: zFront - 1.55, busy: null, car: null});
        // A sign with the fork and a big P
        const c = document.createElement('canvas');
        c.width = c.height = 64;
        const g = c.getContext('2d');
        g.fillStyle = '#2F5DA8';
        g.fillRect(0, 0, 64, 64);
        g.fillStyle = '#FFFFFF';
        g.font = 'bold 48px sans-serif';
        g.textAlign = 'center';
        g.textBaseline = 'middle';
        g.fillText('P', 32, 35);
        const tex = new THREE.CanvasTexture(c);
        tex.colorSpace = THREE.SRGBColorSpace;
        const sign = new THREE.Group();
        sign.add(box(0.08, 1.9, 0.08, mat('#2F3A3A'), 0, 0, 0));
        const face = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.6, 0.05), [mat('#2F5DA8'), mat('#2F5DA8'), mat('#2F5DA8'), mat('#2F5DA8'), new THREE.MeshStandardMaterial({map: tex}), mat('#2F5DA8')]);
        face.position.y = 2.1;
        sign.add(face);
        sign.position.set(lotX1 - 0.3, 0, zFront + 0.3);
        add(world, sign);
        for (let x = lotX0 + 0.4; x < lotX1; x += 6) {
            const l = streetLamp(1);
            l.position.set(x, 0, zFront - depth - 0.25);
            add(world, l);
        }
    }

    // Trees: along the far sidewalk and around the plot (not where the car park is)
    const tree = (x, z, s = 1) => {
        if (spaces && x > lotX0 - 1 && x < lotX1 + 0.5 && z > D + OUT - 4 && z < D + OUT + 1) return;
        const g = new THREE.Group();
        const trunk = new THREE.Mesh(new THREE.CylinderGeometry(0.12 * s, 0.16 * s, 1.2 * s, 7), mat(PALETTE.woodDark));
        trunk.position.y = 0.6 * s;
        g.add(trunk);
        const foliage = [];
        [[0, 1.6, 0, 0.8], [0.35, 1.35, 0.2, 0.55], [-0.3, 1.4, -0.2, 0.6], [0, 2.1, 0, 0.55]].forEach(([dx, y, dz, rr], i) => {
            const b = new THREE.Mesh(new THREE.IcosahedronGeometry(rr * s, 0), mat(i % 2 ? PALETTE.leafDark : PALETTE.leaf));
            b.position.set(dx * s, y * s, dz * s);
            g.add(b);
            foliage.push(b);
        });
        const branches = new THREE.Group();
        for (const [rx, rz, l] of [[0.6, 0, 0.9], [-0.5, 0.4, 0.8], [0.2, -0.7, 0.7], [-0.2, 0.2, 1.0]]) {
            const br = new THREE.Mesh(new THREE.CylinderGeometry(0.03 * s, 0.06 * s, l * s, 5), mat(PALETTE.woodDark));
            br.position.y = 1.2 * s + l * s * 0.4;
            br.rotation.set(rx, 0, rz);
            branches.add(br);
        }
        branches.visible = false;
        g.add(branches);
        world.trees.push({g, foliage, branches, i: world.trees.length});
        g.position.set(x, 0, z);
        add(world, g);
    };
    for (let x = -2 - lotW; x <= W + 4; x += 6) tree(x, roadZ + 4.6, 1 + ((Math.abs(x) * 7) % 3) * 0.12);
    [[W + OUT + 1.6, 2, 1.2], [W + OUT + 2.5, D * 0.6, 1], [W + OUT + 1.8, D - 1, 0.9], [-6.5, D + 0.5, 1.1], [-7, 1.5, 1.2], [W / 2, -OUT - 2.5, 1.3], [2, -OUT - 3, 1]].forEach(([x, z, s]) => tree(x, z, s));

    // Street lamps on both sidewalks
    for (let x = 1; x < W + 6; x += 5) {
        const l = streetLamp(0);
        l.position.set(x, 0, D + 2.4 + OUT);
        add(world, l);
    }
    for (let x = -lotW - 8; x < W + 14; x += 7) {
        const l = streetLamp(x % 2 ? 1 : 0);
        l.position.set(x + 2.5, 0, roadZ + 2.4);
        add(world, l);
    }

    buildHouses(world, W, D, level, lotW, rnd);
    buildPlot(world, W, D, rnd);
}

/**
 * The houses. A young restaurant stands in a quiet street; as it levels up (and grows), the neighbourhood fills in:
 * more houses across the street, then behind and at the sides, then a second row of taller buildings and shops.
 */
function buildHouses(world, W, D, level, lotW, rnd) {
    const walls = ['#FFF3E0', '#F6D9B8', '#E8EFD9', '#F4E1E6', '#E3ECF4', '#FCEBC5', '#E9DCCB', '#D8E6E0'];
    const roofs = ['#B43E5C', '#7A8B3A', '#E2B23A', '#3E7CB4', '#C2572B', '#6B4FA0', '#8A5A3C', '#2F6F8F'];
    const lit = glowMat('#FFD27A'), dark = mat('#58697A');
    const roadZ = D + OUT + 4.5;
    let i = 0;
    const windows = (g, w, h, d, floors, k) => {
        for (let f = 0; f < floors; f++) for (let c = 0; c < Math.max(2, Math.round(w / 1.3)); c++) {
            const cols = Math.max(2, Math.round(w / 1.3));
            const x = -w / 2 + (c + 0.5) * w / cols, y = 0.9 + f * (h - 0.4) / floors + 0.25;
            const win = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.55, 0.06), rnd(k * 7 + f * 3 + c) > 0.35 ? lit : dark);
            win.position.set(x, Math.min(y, h - 0.45), d / 2 + 0.03);
            g.add(win);
        }
    };
    const snowCap = (g, w, d, y, coneR, h) => {
        const cap = new THREE.Mesh(new THREE.ConeGeometry(coneR * 1.02, h * 0.32, 4), mat('#F7FAFC'));
        cap.position.y = y;
        cap.rotation.y = Math.PI / 4;
        cap.scale.set(w / Math.max(w, d), 1, d / Math.max(w, d));
        cap.visible = false;
        g.add(cap);
        world.snowCaps.push(cap);
    };
    // A small house with a pitched roof
    const house = (x, z, rot) => {
        const k = i++;
        const g = new THREE.Group();
        const w = 3.2 + rnd(k) * 1.6, d = 3 + rnd(k + 3) * 1.2, h = 2.2 + rnd(k + 7) * 1.4;
        g.add(box(w, h, d, mat(walls[k % walls.length]), 0, 0, 0));
        const rh = 1.6 + rnd(k + 1), coneR = Math.max(w, d) * 0.78;
        const roof = new THREE.Mesh(new THREE.ConeGeometry(coneR, rh, 4), mat(roofs[(k * 5) % roofs.length]));
        roof.position.y = h + rh / 2;
        roof.rotation.y = Math.PI / 4;
        roof.scale.set(w / Math.max(w, d), 1, d / Math.max(w, d));
        g.add(roof, box(0.7, 1.3, 0.08, mat('#7B4E2C'), 0, 0, d / 2 + 0.03));
        snowCap(g, w, d, h + rh * 0.78, coneR, rh * 2);
        windows(g, w, h, d, 1, k);
        if (rnd(k + 11) > 0.4) g.add(box(0.4, 1, 0.4, mat('#9C4A2E'), w * 0.25, h + 0.4, -d * 0.15));
        if (rnd(k + 13) > 0.6) { // a little front garden hedge
            g.add(box(w * 0.9, 0.45, 0.35, mat(PALETTE.leafDark), 0, 0, d / 2 + 0.9));
            world.bushes.push(g.children[g.children.length - 1]);
        }
        g.position.set(x, 0, z);
        g.rotation.y = rot;
        add(world, g);
    };
    // A shop: flat roof, a striped awning and a big glowing window
    const shop = (x, z, rot) => {
        const k = i++;
        const g = new THREE.Group();
        const w = 4 + rnd(k) * 1.5, d = 3.4, h = 3 + rnd(k + 2) * 0.8;
        g.add(box(w, h, d, mat(walls[(k + 3) % walls.length]), 0, 0, 0), box(w + 0.2, 0.25, d + 0.2, mat('#8A7A6A'), 0, h, 0));
        g.add(box(w * 0.7, 1.4, 0.06, lit, 0, 0.5, d / 2 + 0.03), box(0.8, 1.6, 0.08, mat('#3B2418'), w * 0.38, 0, d / 2 + 0.04));
        const c1 = roofs[k % roofs.length];
        for (let s = 0; s < 8; s++) {
            const st = box(w / 8, 0.05, 1.0, mat(s % 2 ? '#FFFFFF' : c1), -w / 2 + (s + 0.5) * w / 8, 2.2, d / 2 + 0.45);
            st.rotation.x = 0.35;
            g.add(st);
        }
        g.add(box(w * 0.6, 0.45, 0.08, mat(c1), 0, h - 0.7, d / 2 + 0.05)); // the shop sign
        g.position.set(x, 0, z);
        g.rotation.y = rot;
        add(world, g);
    };
    // A block of flats: several floors of windows, a flat roof with a water tank
    const flats = (x, z, rot, floors) => {
        const k = i++;
        const g = new THREE.Group();
        const w = 4.5 + rnd(k) * 1.5, d = 4, h = floors * 1.25 + 0.6;
        g.add(box(w, h, d, mat(walls[(k + 5) % walls.length]), 0, 0, 0), box(w + 0.15, 0.2, d + 0.15, mat('#8A7A6A'), 0, h, 0));
        windows(g, w, h, d, floors, k);
        g.add(box(0.9, 1.3, 0.08, mat('#5A3218'), 0, 0, d / 2 + 0.04));
        const tank = new THREE.Mesh(new THREE.CylinderGeometry(0.35, 0.35, 0.7, 10), mat('#9AA3AD'));
        tank.position.set(w * 0.25, h + 0.55, -d * 0.2);
        g.add(tank);
        const cap = box(w + 0.15, 0.08, d + 0.15, mat('#F7FAFC'), 0, h + 0.2, 0);
        cap.visible = false;
        g.add(cap);
        world.snowCaps.push(cap);
        g.position.set(x, 0, z);
        g.rotation.y = rot;
        add(world, g);
    };
    const pick = (x, z, rot, k) => {
        if (level >= 14 && rnd(k + 40) > 0.6) flats(x, z, rot, 3 + Math.floor(rnd(k + 41) * (level >= 20 ? 4 : 2)));
        else if (level >= 6 && rnd(k + 50) > 0.65) shop(x, z, rot);
        else house(x, z, rot);
    };
    // Across the street: a few houses at first, then filling the gaps
    const gap = level < 4 ? 9 : level < 8 ? 7.2 : 5.8;
    let k = 0;
    for (let x = -10 - lotW; x <= W + 12; x += gap) pick(x + rnd(k) * 0.8, roadZ + 7, Math.PI, k++);
    // A second row further back, taller, from level 8
    if (level >= 8) for (let x = -14 - lotW; x <= W + 16; x += 6.4) flats(x + rnd(k++) * 1.2, roadZ + 15, Math.PI, 2 + Math.floor(rnd(k) * (level >= 16 ? 5 : 3)));
    // Behind the restaurant, from level 3; on the far right, from level 5; on the far left, from level 10
    if (level >= 3) for (let x = -2; x <= W + 6; x += level < 9 ? 8.5 : 6.2) pick(x, -OUT - 8 - rnd(k) * 1.5, 0, k++);
    if (level >= 5) for (let z = 1; z < D + 2; z += level < 11 ? 8 : 6) pick(W + OUT + 8 + rnd(k), z, -Math.PI / 2, k++);
    if (level >= 10) for (let z = -2; z < D; z += 6.5) pick(-lotW - 13 - rnd(k), z, Math.PI / 2, k++);
    if (level >= 18) for (let x = -2; x <= W + 6; x += 6.5) flats(x + rnd(k++), -OUT - 16, 0, 3 + Math.floor(rnd(k) * 4));
}

/** The plot itself: the picket fence, flower beds along the front, bushes and two benches on the sidewalk. */
function buildPlot(world, W, D, rnd) {
    const pale = mat('#FFFFFF');
    const fence = (x1, z1, x2, z2) => {
        const len = Math.hypot(x2 - x1, z2 - z1), n = Math.floor(len / 0.5);
        const g = new THREE.Group();
        for (let k = 0; k <= n; k++) {
            const p = new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.55, 0.06), pale);
            p.position.set(x1 + (x2 - x1) * k / n, 0.27, z1 + (z2 - z1) * k / n);
            g.add(p);
        }
        const rail = new THREE.Mesh(new THREE.BoxGeometry(len, 0.06, 0.04), pale);
        rail.position.set((x1 + x2) / 2, 0.38, (z1 + z2) / 2);
        rail.rotation.y = -Math.atan2(z2 - z1, x2 - x1);
        g.add(rail);
        add(world, g);
    };
    fence(-5, -OUT - 0.5, W + OUT + 0.5, -OUT - 0.5);
    fence(-5, -OUT - 0.5, -5, D + OUT);
    fence(W + OUT + 0.5, -OUT - 0.5, W + OUT + 0.5, D + OUT);
    const petals = ['#F25F5C', '#FFD23F', '#F7A1C4', '#FFFFFF', '#9B5DE5'];
    for (let k = 0; k < 46; k++) {
        const x = -4.5 + rnd(k + 50) * (W + 6.5), z = D + OUT + 0.2 + rnd(k + 80) * 0.15;
        if (Math.abs(x - ((world.door ?? Math.floor(W / 2)) + 0.5)) < 1.2) continue; // keep the path clear
        const f = new THREE.Mesh(new THREE.IcosahedronGeometry(0.09, 0), mat(petals[k % petals.length]));
        f.position.set(x, 0.18, z);
        const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.015, 0.015, 0.18, 4), mat(PALETTE.leafDark));
        stem.position.set(x, 0.09, z);
        world.outdoor.add(f, stem);
        world.flowers.push(f, stem);
    }
    for (const [x, z, r] of [[-4.3, D - 0.6, 0.5], [-4.4, D + OUT - 0.5, 0.55], [W + OUT + 1.2, -OUT - 1.4, 0.6], [-4.4, Math.min(9.5, D - 1.5), 0.45]]) {
        const b = new THREE.Mesh(new THREE.IcosahedronGeometry(r, 0), mat(PALETTE.leaf));
        world.bushes.push(b);
        b.position.set(x, r * 0.8, z);
        b.castShadow = true;
        world.outdoor.add(b);
    }
    for (const x of [2, W - 3]) {
        const g = new THREE.Group();
        g.add(box(1.4, 0.08, 0.4, mat(PALETTE.wood), 0, 0.38, 0), box(1.4, 0.35, 0.06, mat(PALETTE.wood), 0, 0.5, -0.18));
        for (const sx of [-0.6, 0.6]) g.add(box(0.06, 0.42, 0.36, mat('#2F3A3A'), sx, 0, 0));
        g.position.set(x, 0, D + 2.15 + OUT);
        g.rotation.y = Math.PI;
        add(world, g);
    }
}

// ---------------------------------------------------------------- street life

/** How busy the street is at an hour (0..1): morning, lunch and evening rush, almost empty at night. */
export function streetBusy(h) {
    const bump = (c, w, k) => k * Math.exp(-((h - c) ** 2) / (2 * w * w));
    const night = h < 5.5 || h > 23 ? 0.04 : 0.18;
    return Math.min(1, night + bump(8.5, 1.1, 0.75) + bump(13, 1.3, 0.55) + bump(18.5, 1.5, 0.85) + bump(21, 1, 0.25));
}

/**
 * Cars on the road and people on the sidewalks, plus the cars of guests using the car park (asked for by the
 * simulation through driveIn / driveOut). Purely for the eye: none of it touches the economy.
 */
export class Traffic {
    constructor(world) {
        this.world = world;
        this.cars = [];
        this.walkers = [];
        this.group = new THREE.Group();
        world.scene.add(this.group);
        this.carIn = 1;
        this.walkIn = 0.5;
        this.seed = 1;
    }

    /** The room was rebuilt: clear the street (parked guest cars are put back by the simulation). */
    reset() {
        this.group.clear();
        this.cars = [];
        this.walkers = [];
        for (const s of this.world.parkingStalls || []) s.busy = s.car = null;
    }

    hour() {
        return this.world.env?.hour ?? 12;
    }

    /** A car following a list of points; onDone when it reaches the last one. */
    addCar(points, speed, onDone, model) {
        const mesh = model || carModel(this.seed++ * 3.7);
        mesh.position.copy(points[0]);
        this.group.add(mesh);
        const car = {mesh, points, i: 1, speed, onDone};
        this.cars.push(car);
        return car;
    }

    /** A guest's car: from the road into a free space. Returns the stall, or null when the car park is full. */
    driveIn(guest, onParked) {
        const st = this.world.street, stall = (this.world.parkingStalls || []).find(s => !s.busy);
        if (!st || !stall) return null;
        stall.busy = guest;
        const fromLeft = true;
        const x0 = fromLeft ? st.x0 : st.x1;
        const pts = [new THREE.Vector3(x0, 0, st.laneIn), new THREE.Vector3(stall.x, 0, st.laneIn), new THREE.Vector3(stall.x, 0, st.D + OUT + 1.2), new THREE.Vector3(stall.x, 0, stall.z)];
        const car = this.addCar(pts, 6, () => {
            car.parked = true;
            onParked && onParked(stall);
        });
        stall.car = car;
        return stall;
    }

    /** The guest drives away: out of the space backwards, then along the road. */
    driveOut(stall) {
        const car = stall?.car, st = this.world.street;
        if (!car) return;
        stall.car = null;
        stall.busy = null;
        car.parked = false;
        car.points = [car.mesh.position.clone(), new THREE.Vector3(stall.x, 0, st.D + OUT + 1.6), new THREE.Vector3(stall.x + 0.8, 0, st.laneOut), new THREE.Vector3(st.x0, 0, st.laneOut)];
        car.i = 1;
        car.reverse = 1; // the first leg is backwards
        car.onDone = () => this.removeCar(car);
    }

    removeCar(car) {
        this.group.remove(car.mesh);
        this.cars = this.cars.filter(c => c !== car);
    }

    update(dt) {
        const st = this.world.street;
        if (!st) return;
        const h = this.hour(), busy = streetBusy(h);
        const wet = ['drizzle', 'rain', 'hail', 'light_snow', 'snow'].includes(this.world.env?.weather);
        // Passing cars
        this.carIn -= dt;
        const passing = this.cars.filter(c => c.passing).length;
        if (this.carIn <= 0) {
            this.carIn = (2 + Math.random() * 4) / Math.max(0.05, busy);
            if (passing < 2 + Math.round(busy * 6)) {
                const east = Math.random() < 0.5;
                const z = east ? st.laneIn : st.laneOut;
                const car = this.addCar([new THREE.Vector3(east ? st.x0 : st.x1, 0, z), new THREE.Vector3(east ? st.x1 : st.x0, 0, z)], 4.5 + Math.random() * 3, null);
                car.passing = true;
                car.onDone = () => this.removeCar(car);
            }
        }
        for (const car of [...this.cars]) {
            if (car.parked) continue;
            const target = car.points[car.i];
            if (!target) continue;
            const pos = car.mesh.position;
            const dx = target.x - pos.x, dz = target.z - pos.z, dist = Math.hypot(dx, dz);
            // Keep a distance from the car ahead in the same lane
            let speed = car.speed;
            if (car.passing) for (const o of this.cars) {
                if (o === car || Math.abs(o.mesh.position.z - pos.z) > 0.5) continue;
                const ahead = (o.mesh.position.x - pos.x) * Math.sign(dx);
                if (ahead > 0 && ahead < 3.2) speed = Math.min(speed, o.passing ? o.speed * 0.9 : 0);
            }
            if (car.i === car.points.length - 1 && !car.passing && car.onDone && car.reverse == null) speed = Math.min(speed, 1.2 + dist * 2); // slow down to park
            const step = Math.min(dist, speed * dt);
            if (dist > 0.001) {
                pos.x += dx / dist * step;
                pos.z += dz / dist * step;
                const yaw = Math.atan2(dx, dz) + (car.reverse && car.i === 1 ? Math.PI : 0);
                let d = yaw - car.mesh.rotation.y;
                d = Math.atan2(Math.sin(d), Math.cos(d));
                car.mesh.rotation.y += d * Math.min(1, dt * 8);
            }
            if (dist < 0.05) {
                car.i++;
                if (car.i >= car.points.length) {
                    const fn = car.onDone;
                    car.onDone = null;
                    fn && fn();
                }
            }
        }
        // People walking by on both sidewalks (fewer in the rain)
        this.walkIn -= dt;
        const want = Math.round((1 + busy * 9) * (wet ? 0.45 : 1));
        if (this.walkIn <= 0) {
            this.walkIn = (1.5 + Math.random() * 3) / Math.max(0.1, busy);
            if (this.walkers.length < want) {
                const east = Math.random() < 0.5, far = Math.random() < 0.55;
                const z = far ? st.farWalk + (Math.random() - 0.5) * 0.8 : st.D + OUT + 1.8 + Math.random() * 0.3;
                const mesh = personModel('customer', null, Math.random() * 1000);
                mesh.position.set(east ? st.x0 + 4 : st.x1 - 4, 0, z);
                mesh.rotation.y = east ? Math.PI / 2 : -Math.PI / 2;
                this.group.add(mesh);
                const dog = Math.random() < 0.12 ? dogModel() : null;
                if (dog) {
                    dog.position.copy(mesh.position).add(new THREE.Vector3(east ? 0.7 : -0.7, 0, 0.35));
                    dog.rotation.y = mesh.rotation.y;
                    this.group.add(dog);
                }
                this.walkers.push({mesh, dog, dir: east ? 1 : -1, speed: 1.0 + Math.random() * 0.6, clock: Math.random() * 10, end: east ? st.x1 - 4 : st.x0 + 4, mood: 50 + Math.random() * 50});
            }
        }
        for (const w of [...this.walkers]) {
            w.mesh.position.x += w.dir * w.speed * dt;
            w.clock += dt;
            animatePerson(w.mesh, 'walk', w.clock * w.speed, {mood: w.mood});
            if (w.dog) {
                w.dog.position.x += w.dir * w.speed * dt;
                const legs = w.dog.userData.legs;
                legs.forEach((l, i) => (l.rotation.x = Math.sin(w.clock * 14 + i * Math.PI) * 0.5));
                w.dog.userData.tail.rotation.y = Math.sin(w.clock * 12) * 0.6;
            }
            if ((w.end - w.mesh.position.x) * w.dir <= 0) {
                this.group.remove(w.mesh);
                if (w.dog) this.group.remove(w.dog);
                this.walkers = this.walkers.filter(x => x !== w);
            }
        }
    }
}

/** A small dog on a walk with its owner. */
function dogModel() {
    const g = new THREE.Group();
    const fur = mat(['#C98F5A', '#F4EAD5', '#3B2418', '#8E8E8E'][Math.floor(Math.random() * 4)], {flat: false});
    const body = new THREE.Mesh(new THREE.CapsuleGeometry(0.1, 0.25, 4, 8), fur);
    body.rotation.x = Math.PI / 2;
    body.position.y = 0.24;
    const head = new THREE.Mesh(new THREE.SphereGeometry(0.1, 10, 8), fur);
    head.position.set(0, 0.36, 0.22);
    const nose = new THREE.Mesh(new THREE.SphereGeometry(0.025, 6, 4), mat('#2A1D17'));
    nose.position.set(0, 0.35, 0.32);
    g.add(body, head, nose);
    for (const sx of [-1, 1]) {
        const ear = new THREE.Mesh(new THREE.SphereGeometry(0.045, 6, 4), mat('#3B2418'));
        ear.scale.set(0.6, 1.2, 0.5);
        ear.position.set(sx * 0.07, 0.42, 0.2);
        g.add(ear);
    }
    const legs = [];
    for (const [x, z] of [[-0.06, 0.13], [0.06, 0.13], [-0.06, -0.13], [0.06, -0.13]]) {
        const leg = new THREE.Group();
        leg.position.set(x, 0.18, z);
        const m = new THREE.Mesh(new THREE.CylinderGeometry(0.025, 0.025, 0.18, 6), fur);
        m.position.y = -0.09;
        leg.add(m);
        g.add(leg);
        legs.push(leg);
    }
    const tail = new THREE.Group();
    tail.position.set(0, 0.3, -0.2);
    const tm = new THREE.Mesh(new THREE.CylinderGeometry(0.015, 0.025, 0.15, 5), fur);
    tm.position.set(0, 0.05, -0.04);
    tm.rotation.x = -0.8;
    tail.add(tm);
    g.add(tail);
    g.userData = {legs, tail};
    g.traverse(o => o.isMesh && (o.castShadow = true));
    return g;
}

