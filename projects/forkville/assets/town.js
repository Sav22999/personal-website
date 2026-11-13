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

const OUT = 5; // the outdoor strip around the room (OUTDOOR in lib/catalog.php)
const PARK_SPACES = [0, 4, 8, 12]; // spaces per car park level (PARKING in lib/catalog.php)
const STALL_W = 1.45;
const GARAGE_SPACES = [0, 16, 28, 42, 60, 80]; // underground spaces per level (GARAGE in lib/catalog.php)
const GARAGE_W = 5.2; // the ramp and the stairs kiosk beside it

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
    g.userData.lamp = style; // the holidays hang ribbons and garlands on street lamps
    return g;
}

/** The whole neighbourhood, rebuilt with the room (it depends on the room's size, the level and the car park). */
export function buildNeighbourhood(world, r) {
    const W = r.size, D = r.depth || r.size, level = r.level || 1, parking = Math.min(3, r.parking || 0);
    const rnd = (n) => Math.abs(Math.sin(n * 91.7 + W * 3 + D) * 4371.3) % 1;
    const roadZ = D + OUT + 4.5;
    const spaces = PARK_SPACES[parking];
    const streetLotW = spaces ? spaces * STALL_W + 0.6 : 0;
    const lotX1 = -5.6, lotX0 = lotX1 - streetLotW;
    const garage = Math.min(GARAGE_SPACES.length - 1, r.garage || 0), garW = garage ? GARAGE_W + 0.4 : 0;
    const lotW = streetLotW + garW; // everything left of the plot: street car park, then the ramp down
    world.parkingW = lotW;
    world.street = {roadZ, nearWalk: D + OUT + 2.6, farWalk: roadZ + 3.1, laneIn: roadZ - 1, laneOut: roadZ + 1, x0: -36 - lotW, x1: W + 40, D, W};

    // Sidewalks, road, the stripes and a zebra crossing at the door
    const len = W + 90 + lotW, cx = (W - lotW) / 2;
    add(world, box(len, 0.06, 2.2, mat('#E4D6C1'), cx, -0.03, D + 1.4 + OUT));
    add(world, box(len, 0.04, 4, mat('#5B5754'), cx, -0.05, roadZ));
    add(world, box(len, 0.06, 2.2, mat('#E4D6C1'), cx, -0.03, roadZ + 3.1));
    for (const z of [roadZ - 2.05, roadZ + 2.05]) add(world, box(len, 0.1, 0.12, mat('#BDB2A1'), cx, -0.04, z)); // kerbs
    // Where the side streets cross the main road (same places as in buildHouses): junctions with traffic lights
    world.junctions = [-lotW - 9.5, W + OUT + 5.5];
    world.trafficLights = [];
    for (let x = cx - len / 2 + 1; x < cx + len / 2; x += 3) {
        if (Math.abs(x - ((world.door ?? Math.floor(W / 2)) + 0.5)) < 2) continue;
        if (world.junctions.some(j => Math.abs(x - j) < 4.4)) continue; // no centre line across a junction
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
    world.garageStalls = [];
    if (garage) buildGarage(world, lotX0 - 0.4 - GARAGE_W, D + OUT + 0.3, GARAGE_SPACES[garage]);
    else groundHole(world, null);
    if (spaces) {
        const lotW = streetLotW;
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
        if (lotW && x > lotX1 - lotW - 1 && x < lotX1 + 0.5 && z > D + OUT - 6 && z < D + OUT + 1) return;
        if (Math.abs(x - ((world.door ?? Math.floor(W / 2)) + 0.5)) < 4.5 && z > roadZ + 3 && z < roadZ + 11) return; // the square across from the door
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
        const holi = new THREE.Group();
        g.add(holi);
        world.trees.push({g, foliage, branches, i: world.trees.length, holi, sc: s});
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

    buildHouses(world, W, D, level, lotW, rnd); // lotW: street car park and garage ramp
    buildPlot(world, W, D, rnd);
    // Every street lamp gets an empty group for the holiday ribbons
    world.holiLamps = [];
    for (const o of world.outdoor.children) if (o.userData.lamp !== undefined) {
        const holi = new THREE.Group();
        o.add(holi);
        world.holiLamps.push({holi, style: o.userData.lamp});
    }
    world.holiStreet = new THREE.Group(); // garlands across the main road, the big tree in the square
    world.outdoor.add(world.holiStreet);
}

/**
 * A hole in the grass (for the garage ramp), or none: the grass is one big plane, rebuilt as a shape with a hole.
 * Its texture coordinates stay those of the plain plane, so the grass looks the same around it.
 */
function groundHole(world, h) {
    const grass = world.grass;
    if (!grass) return;
    const size = 420, half = size / 2, px = grass.position.x, pz = grass.position.z;
    if (!h) {
        if (grass.userData.holed) {
            grass.geometry.dispose();
            grass.geometry = new THREE.PlaneGeometry(size, size);
            grass.userData.holed = false;
        }
        return;
    }
    // The plane lies flat (rotated −90° about x): local x = world x − px, local y = −(world z − pz)
    const shape = new THREE.Shape([new THREE.Vector2(-half, -half), new THREE.Vector2(half, -half), new THREE.Vector2(half, half), new THREE.Vector2(-half, half)]);
    const lx0 = h.x0 - px, lx1 = h.x1 - px, ly0 = -(h.z1 - pz), ly1 = -(h.z0 - pz);
    shape.holes.push(new THREE.Path([new THREE.Vector2(lx0, ly0), new THREE.Vector2(lx0, ly1), new THREE.Vector2(lx1, ly1), new THREE.Vector2(lx1, ly0)]));
    const geo = new THREE.ShapeGeometry(shape);
    const pos = geo.attributes.position, uv = geo.attributes.uv;
    for (let i = 0; i < pos.count; i++) uv.setXY(i, (pos.getX(i) + half) / size, (pos.getY(i) + half) / size);
    geo.computeVertexNormals();
    grass.geometry.dispose();
    grass.geometry = geo;
    grass.userData.holed = true;
}

/**
 * The underground car park: a ramp that goes down under the ground beside the plot, a concrete portal with a big
 * "P −1", and a small glass kiosk with the stairs for people on foot. The spaces themselves are underground (not
 * drawn): a guest's car drives down the ramp and vanishes; the guest comes up the kiosk stairs.
 */
function buildGarage(world, x0, zFront, spaces) {
    const concrete = mat('#B9B2A8'), dark = mat('#26221F'), asphalt = mat('#4F4B48');
    const rampW = 3.2, len = 4.2, drop = 1.5, rx = x0 + rampW / 2;
    const g = new THREE.Group();
    // The ramp: a tilted slab from the sidewalk down to the portal, with yellow-black edges
    const slope = Math.atan2(drop, len);
    const slab = new THREE.Mesh(new THREE.BoxGeometry(rampW, 0.1, Math.hypot(len, drop)), asphalt);
    slab.rotation.x = -slope;
    slab.position.set(rx, -drop / 2 - 0.05, zFront - len / 2);
    slab.receiveShadow = true;
    g.add(slab);
    for (let i = 0; i < 5; i++) { // painted arrows down the ramp
        const a = new THREE.Mesh(new THREE.BoxGeometry(0.16, 0.02, 0.5), mat('#F4F1E8'));
        a.rotation.x = -slope;
        const t = (i + 0.5) / 5;
        a.position.set(rx, -drop * t + 0.01, zFront - len * t);
        g.add(a);
    }
    // Low concrete walls along both sides (they rise as the ramp goes down) with a hazard stripe on top
    for (const side of [-1, 1]) {
        const wx = rx + side * (rampW / 2 + 0.1);
        const wall = box(0.2, drop + 0.55, len, concrete, wx, -drop - 0.05, zFront - len / 2);
        g.add(wall);
        for (let k = 0; k < 8; k++) g.add(box(0.22, 0.06, len / 8, k % 2 ? mat('#2B2B2B') : mat('#F2C230'), wx, 0.56, zFront - len / 16 - k * len / 8));
    }
    // The portal at the bottom: a concrete frame around a dark mouth, and the sign above
    g.add(box(rampW + 0.6, 0.5, 0.4, concrete, rx, 0.3, zFront - len - 0.2));
    g.add(box(rampW + 0.4, drop + 0.3, 0.3, dark, rx, -drop - 0.05, zFront - len - 0.25));
    g.add(box(rampW + 0.6, 0.3, 2.2, concrete, rx, 0.45, zFront - len - 1.3)); // the slab over the entrance
    const c = document.createElement('canvas');
    c.width = 128;
    c.height = 64;
    const x = c.getContext('2d');
    x.fillStyle = '#2F5DA8';
    x.fillRect(0, 0, 128, 64);
    x.fillStyle = '#FFFFFF';
    x.font = 'bold 44px sans-serif';
    x.textAlign = 'center';
    x.textBaseline = 'middle';
    x.fillText('P −1', 64, 35);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    const sign = new THREE.Mesh(new THREE.BoxGeometry(1.6, 0.8, 0.06), [mat('#2F5DA8'), mat('#2F5DA8'), mat('#2F5DA8'), mat('#2F5DA8'), new THREE.MeshStandardMaterial({map: tex}), mat('#2F5DA8')]);
    sign.position.set(rx, 1.05, zFront - len - 0.15);
    g.add(sign);
    // A light over the mouth (on at night with the street lamps)
    const lamp = new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.08, 0.12), glowMat('#FFE6A8'));
    lamp.position.set(rx, 0.6, zFront - len + 0.05);
    g.add(lamp);
    // The stairs kiosk for people on foot: glass walls, a flat roof and a little P
    const kx = x0 + rampW + 1.25, kz = zFront - 0.9; // clear of the ramp's side wall
    const glass = new THREE.MeshStandardMaterial({color: '#BFE3F0', transparent: true, opacity: 0.45, roughness: 0.1});
    g.add(box(1.5, 2.1, 1.3, glass, kx, 1.05, kz));
    g.add(box(1.7, 0.14, 1.5, concrete, kx, 2.17, kz));
    for (const [dx, dz] of [[-0.72, -0.62], [0.72, -0.62], [-0.72, 0.62], [0.72, 0.62]]) g.add(box(0.07, 2.1, 0.07, mat('#3B3B40'), kx + dx, 1.05, kz + dz));
    // The stairwell inside: a dark opening with the edges of the steps going down towards the back, and a handrail
    g.add(box(1.2, 0.02, 1.0, dark, kx, 0.0, kz));
    for (let s = 0; s < 5; s++) g.add(box(1.1, 0.025, 0.05, mat('#8E877E'), kx, 0.01, kz + 0.42 - s * 0.2));
    g.add(box(0.05, 0.05, 1.0, mat('#3B3B40'), kx + 0.62, 0.9, kz));
    for (const dz of [-0.45, 0.45]) g.add(box(0.05, 0.9, 0.05, mat('#3B3B40'), kx + 0.62, 0, kz + dz));
    const ksign = sign.clone();
    ksign.scale.setScalar(0.45);
    ksign.position.set(kx, 2.5, kz + 0.6);
    g.add(ksign);
    g.traverse(o => { if (o.isMesh) o.castShadow = o.receiveShadow = true; });
    world.outdoor.add(g);
    world.garageInfo = {rx, zFront, len, drop, kx};
    groundHole(world, {x0: rx - rampW / 2 - 0.2, x1: rx + rampW / 2 + 0.2, z0: zFront - len - 0.1, z1: zFront});
    // Underground spaces: the guest steps out of the kiosk (stall x + 0.6 is where the simulation puts them)
    for (let i = 0; i < spaces; i++) world.garageStalls.push({x: kx - 0.6, z: zFront, busy: null, car: null, garage: true});
}

/**
 * Fewer draw calls: the static pieces of a building that share a material become one mesh. Pieces that change at
 * run time (snow caps, hedges that change colour with the season, the holiday group) and multi-material meshes stay.
 */
function mergeStatic(g, skip) {
    g.updateMatrixWorld(true);
    const inv = new THREE.Matrix4().copy(g.matrixWorld).invert();
    const byMat = new Map();
    const inHoli = (o) => { for (let p = o.parent; p && p !== g; p = p.parent) if (p.userData.holi) return true; return false; };
    g.traverse(o => {
        if (!o.isMesh || Array.isArray(o.material) || skip.has(o) || inHoli(o)) return;
        (byMat.get(o.material) || byMat.set(o.material, []).get(o.material)).push(o);
    });
    for (const [material, list] of byMat) {
        if (list.length < 2) continue;
        const geos = list.map(o => {
            const geo = o.geometry.index ? o.geometry.toNonIndexed() : o.geometry.clone();
            geo.applyMatrix4(new THREE.Matrix4().multiplyMatrices(inv, o.matrixWorld));
            return geo;
        });
        const withUv = geos.every(x => x.attributes.uv);
        const total = geos.reduce((n, x) => n + x.attributes.position.count, 0);
        const pos = new Float32Array(total * 3), nor = new Float32Array(total * 3), uv = withUv ? new Float32Array(total * 2) : null;
        let at = 0;
        for (const x of geos) {
            pos.set(x.attributes.position.array, at * 3);
            nor.set(x.attributes.normal.array, at * 3);
            if (uv) uv.set(x.attributes.uv.array, at * 2);
            at += x.attributes.position.count;
            x.dispose();
        }
        const merged = new THREE.BufferGeometry();
        merged.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        merged.setAttribute('normal', new THREE.BufferAttribute(nor, 3));
        if (uv) merged.setAttribute('uv', new THREE.BufferAttribute(uv, 2));
        merged.computeBoundingSphere();
        for (const o of list) o.parent.remove(o);
        g.add(new THREE.Mesh(merged, material));
    }
}

/** Shop signs: a pictogram (no words, so it works in every language) on a coloured board. Cached per kind. */
const SIGNS = new Map();
function signTexture(kind, bg) {
    const key = kind + bg;
    if (SIGNS.has(key)) return SIGNS.get(key);
    const c = document.createElement('canvas');
    c.width = 128;
    c.height = 48;
    const g = c.getContext('2d');
    g.fillStyle = bg;
    g.fillRect(0, 0, 128, 48);
    g.strokeStyle = g.fillStyle = '#FFFFFF';
    g.lineWidth = 4;
    g.lineCap = g.lineJoin = 'round';
    const cx = 64, cy = 24;
    g.beginPath();
    if (kind === 'bakery') { // a loaf with three cuts
        g.ellipse(cx, cy, 26, 13, 0, 0, Math.PI * 2);
        g.fill();
        g.strokeStyle = bg;
        for (const dx of [-12, 0, 12]) { g.beginPath(); g.moveTo(cx + dx - 4, cy + 7); g.lineTo(cx + dx + 4, cy - 7); g.stroke(); }
    } else if (kind === 'florist') { // a flower
        for (let k = 0; k < 5; k++) { g.beginPath(); g.arc(cx + Math.cos(k * 1.257) * 10, cy + Math.sin(k * 1.257) * 10, 7, 0, Math.PI * 2); g.fill(); }
        g.fillStyle = '#FFD23F';
        g.beginPath(); g.arc(cx, cy, 6, 0, Math.PI * 2); g.fill();
    } else if (kind === 'cafe') { // a cup with steam
        g.fillRect(cx - 16, cy - 6, 26, 18);
        g.beginPath(); g.arc(cx + 13, cy + 3, 6, -Math.PI / 2, Math.PI / 2); g.stroke();
        for (const dx of [-9, 0]) { g.beginPath(); g.moveTo(cx + dx - 3, cy - 10); g.quadraticCurveTo(cx + dx + 4, cy - 15, cx + dx - 1, cy - 21); g.stroke(); }
    } else if (kind === 'grocer') { // an apple with a leaf
        g.arc(cx, cy + 3, 14, 0, Math.PI * 2);
        g.fill();
        g.fillStyle = '#9CD86B';
        g.beginPath(); g.ellipse(cx + 7, cy - 13, 7, 3.5, -0.5, 0, Math.PI * 2); g.fill();
    } else if (kind === 'books') { // an open book
        g.moveTo(cx, cy - 10); g.lineTo(cx - 24, cy - 14); g.lineTo(cx - 24, cy + 12); g.lineTo(cx, cy + 16); g.lineTo(cx + 24, cy + 12); g.lineTo(cx + 24, cy - 14); g.closePath();
        g.fill();
        g.strokeStyle = bg;
        g.beginPath(); g.moveTo(cx, cy - 10); g.lineTo(cx, cy + 16); g.stroke();
    } else if (kind === 'pharmacy') { // a cross
        g.fillRect(cx - 6, cy - 18, 12, 36);
        g.fillRect(cx - 18, cy - 6, 36, 12);
    } else { // ice cream: a cone and two scoops
        g.moveTo(cx - 9, cy); g.lineTo(cx, cy + 20); g.lineTo(cx + 9, cy); g.closePath();
        g.fill();
        g.fillStyle = '#F7A1C4';
        g.beginPath(); g.arc(cx - 4, cy - 4, 8, 0, Math.PI * 2); g.fill();
        g.fillStyle = '#FFF6E9';
        g.beginPath(); g.arc(cx + 5, cy - 9, 7, 0, Math.PI * 2); g.fill();
    }
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    SIGNS.set(key, tex);
    return tex;
}
const SHOP_KINDS = ['bakery', 'florist', 'cafe', 'grocer', 'books', 'pharmacy', 'icecream'];
const SHOP_COLORS = {bakery: '#C2572B', florist: '#B43E5C', cafe: '#5A3218', grocer: '#4E8A3A', books: '#2F5DA8', pharmacy: '#3E9A5A', icecream: '#E07BA8'};

/** A street: asphalt with a dashed middle line and a sidewalk each side, along x (horizontal) or along z. */
function street(world, a, b, at, along) {
    const len = b - a, mid = (a + b) / 2;
    const asphalt = mat('#5B5754'), walk = mat('#E4D6C1'), line = mat('#F4E9C8');
    const g = new THREE.Group();
    const put = (w, h, d, m, x, y, z) => g.add(along === 'x' ? box(len * w, h, d, m, mid + x, y, at + z) : box(d, h, len * w, m, at + z, y, mid + x));
    put(1, 0.04, 3.6, asphalt, 0, -0.055, 0);
    put(1, 0.06, 1.5, walk, 0, -0.035, -2.55);
    put(1, 0.06, 1.5, walk, 0, -0.035, 2.55);
    for (let t = a + 1; t < b - 1; t += 3) g.add(along === 'x' ? box(1.3, 0.05, 0.12, line, t, -0.03, at) : box(0.12, 0.05, 1.3, line, at, -0.03, t));
    mergeStatic(g, new Set());
    g.traverse(m => m.isMesh && (m.receiveShadow = true));
    world.outdoor.add(g);
}

/** Zebra stripes across a road: 'x' = the road runs along x (stripes at x), 'z' = it runs along z. */
function zebra(world, x, z, along) {
    const g = new THREE.Group(), white = mat('#F4F1E8');
    for (let i = 0; i < 7; i++) {
        const o = -1.5 + i * 0.5;
        g.add(along === 'x' ? box(0.9, 0.05, 0.3, white, x, -0.005, z + o) : box(0.3, 0.05, 0.9, white, x + o, -0.005, z));
    }
    mergeStatic(g, new Set());
    g.traverse(m => m.isMesh && (m.receiveShadow = true));
    world.outdoor.add(g);
}

/** Clean asphalt over the middle of a crossroads (no kerbs or sidewalks running through it). */
function junctionPatch(world, x, z) {
    // The middle, and the side street going on across both sidewalks of the main road
    for (const [w, d, zz] of [[4.1, 4.1, z], [3.6, 2.4, z - 3.1], [3.6, 2.4, z + 3.1]]) {
        const m = box(w, 0.07, d, mat('#5B5754'), x, -0.035, zz);
        m.receiveShadow = true;
        world.outdoor.add(m);
    }
}

/** A traffic light on a pole, facing +z before rotation. Its three lamps are switched by Traffic.update. */
function trafficLight(axis) {
    const g = new THREE.Group(), dark = mat('#2C2C30');
    g.add(box(0.1, 2.3, 0.1, dark, 0, 0, 0), box(0.3, 0.8, 0.22, dark, 0, 2.2, 0.08), box(0.36, 0.06, 0.26, dark, 0, 3.0, 0.08));
    const lamp = (c, y) => {
        const m = new THREE.Mesh(new THREE.CylinderGeometry(0.085, 0.085, 0.05, 12), new THREE.MeshStandardMaterial({color: c, emissive: c, emissiveIntensity: 0.05}));
        m.rotation.x = Math.PI / 2;
        m.position.set(0, y, 0.2);
        g.add(m);
        return m;
    };
    return {group: g, axis, red: lamp('#E63946', 2.84), yellow: lamp('#F6C945', 2.6), green: lamp('#4CAF50', 2.36), state: null};
}

/** A roundabout: asphalt disc, a grassy island with a kerb and a little tree, a dashed ring and give-way lines. */
function roundabout(world, x, z) {
    const g = new THREE.Group();
    const disc = new THREE.Mesh(new THREE.CylinderGeometry(3.5, 3.5, 0.06, 40), mat('#5B5754'));
    disc.position.set(x, 0.0, z);
    const kerb = new THREE.Mesh(new THREE.CylinderGeometry(1.45, 1.5, 0.16, 32), mat('#D9D2C5'));
    kerb.position.set(x, 0.05, z);
    const grass = new THREE.Mesh(new THREE.CylinderGeometry(1.32, 1.32, 0.18, 32), mat('#7DB04B'));
    grass.position.set(x, 0.06, z);
    g.add(disc, kerb, grass);
    for (let i = 0; i < 18; i++) { // the dashed ring between the two lanes
        const a = i / 18 * Math.PI * 2;
        const d = box(0.45, 0.04, 0.1, mat('#F4E9C8'), x + Math.cos(a) * 2.45, 0.0, z + Math.sin(a) * 2.45);
        d.rotation.y = -a + Math.PI / 2;
        g.add(d);
    }
    for (const [dx, dz] of [[1, 0], [-1, 0], [0, 1], [0, -1]]) { // give-way lines where each road comes in
        for (let k = -3; k <= 3; k++) {
            const t = box(dz ? 0.22 : 0.1, 0.04, dx ? 0.22 : 0.1, mat('#F4F1E8'), x + dx * 3.75 + (dz ? k * 0.26 : 0), -0.03, z + dz * 3.75 + (dx ? k * 0.26 : 0));
            g.add(t);
        }
    }
    const flowers = ['#F25F5C', '#FFD23F', '#FFFFFF', '#C06C84'];
    for (let i = 0; i < 10; i++) {
        const a = i / 10 * Math.PI * 2;
        const f = new THREE.Mesh(new THREE.SphereGeometry(0.12, 6, 5), mat(flowers[i % 4]));
        f.position.set(x + Math.cos(a) * 0.95, 0.2, z + Math.sin(a) * 0.95);
        g.add(f);
    }
    const trunk = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.12, 1.2, 7), mat(PALETTE.woodDark));
    trunk.position.set(x, 0.75, z);
    const crown = new THREE.Mesh(new THREE.SphereGeometry(0.75, 10, 8), mat(PALETTE.leaf));
    crown.position.set(x, 1.65, z);
    g.add(trunk, crown);
    g.traverse(m => m.isMesh && (m.receiveShadow = true, m.castShadow = m === crown || m === trunk));
    world.outdoor.add(g);
}

/**
 * The neighbourhood around the plot, a real one from the start: the main road in front, a street on each side, a
 * back street, and a second road across the main one. Houses, little shops (bakery, florist, café, grocer,
 * bookshop, pharmacy, ice cream) and, as the restaurant levels up, blocks of flats; a little square with a
 * fountain right across from the door. Every building and tree is registered for the holiday decorations.
 */
function buildHouses(world, W, D, level, lotW, rnd) {
    const walls = ['#FFF3E0', '#F6D9B8', '#E8EFD9', '#F4E1E6', '#E3ECF4', '#FCEBC5', '#E9DCCB', '#D8E6E0'];
    const roofs = ['#B43E5C', '#7A8B3A', '#E2B23A', '#3E7CB4', '#C2572B', '#6B4FA0', '#8A5A3C', '#2F6F8F'];
    const lit = glowMat('#FFD27A'), dark = mat('#58697A');
    const roadZ = D + OUT + 4.5, door = (world.door ?? Math.floor(W / 2)) + 0.5;
    const xL = -lotW - 9.5, xR = W + OUT + 5.5, zB = -OUT - 5, zF = roadZ + 20;
    const cx = W / 2, cz = D / 2;
    world.holiHosts = [];
    world.plaza = null;
    let i = 0;
    // Far away buildings do not cast shadows (they would only cost time)
    const place = (g, x, z, rot) => {
        mergeStatic(g, new Set([...world.bushes, ...world.snowCaps]));
        g.position.set(x, 0, z);
        g.rotation.y = rot;
        const near = Math.hypot(x - cx, z - cz) < 30;
        g.traverse(m => m.isMesh && (m.castShadow = near, m.receiveShadow = true));
        world.outdoor.add(g);
    };
    const host = (g, info) => {
        const holi = new THREE.Group();
        holi.userData.holi = true;
        g.add(holi);
        world.holiHosts.push({holi, ...info});
    };
    const WIN = new THREE.BoxGeometry(0.5, 0.55, 0.06);
    // Windows on the front and (fewer, never lit… or lit) on the back: from the restaurant we often see the backs
    const windows = (g, w, h, d, floors, k, y0 = 0, sides = [1, -1]) => {
        const cols = Math.max(2, Math.round(w / 1.3));
        for (const side of sides) for (let f = 0; f < floors; f++) for (let c = 0; c < cols; c++) {
            if (side < 0 && (c + f) % 2) continue;
            const x = -w / 2 + (c + 0.5) * w / cols, y = y0 + 0.9 + f * (h - y0 - 0.4) / floors + 0.25;
            const win = new THREE.Mesh(WIN, rnd(k * 7 + f * 3 + c + (side < 0 ? 5 : 0)) > 0.35 ? lit : dark);
            win.position.set(x, Math.min(y, h - 0.45), side * (d / 2 + 0.03));
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
    // A small house with a pitched roof, a chimney, maybe a hedge
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
            const hedge = box(w * 0.9, 0.45, 0.35, mat(PALETTE.leafDark), 0, 0, d / 2 + 0.9);
            g.add(hedge);
            world.bushes.push(hedge);
        }
        host(g, {kind: 'house', w, h, d, doorX: 0});
        place(g, x, z, rot);
    };
    // A little shop: flat roof, a striped awning, a sign with a pictogram, a lit window and something outside
    const shop = (x, z, rot, kind) => {
        const k = i++;
        kind ||= SHOP_KINDS[Math.floor(rnd(k + 90) * SHOP_KINDS.length)];
        const col = SHOP_COLORS[kind];
        const g = new THREE.Group();
        const w = 4 + rnd(k) * 1.4, d = 3.4, h = 3.2 + rnd(k + 2) * 0.9;
        g.add(box(w, h, d, mat(walls[(k + 3) % walls.length]), 0, 0, 0), box(w + 0.2, 0.25, d + 0.2, mat('#8A7A6A'), 0, h, 0));
        g.add(box(w * 0.62, 1.4, 0.06, lit, -w * 0.08, 0.45, d / 2 + 0.03), box(0.8, 1.7, 0.08, mat('#3B2418'), w * 0.36, 0, d / 2 + 0.04));
        windows(g, w, h, d, 2, k + 3, 0, [-1]); // the back of the shop (the front has the shop window and the sign)
        if (kind !== 'pharmacy') for (let st = 0; st < 8; st++) { // the awning
            const strip = box(w / 8, 0.05, 1.0, mat(st % 2 ? '#FFFFFF' : col), -w / 2 + (st + 0.5) * w / 8, 2.2, d / 2 + 0.45);
            strip.rotation.x = 0.35;
            g.add(strip);
        }
        const sign = new THREE.Mesh(new THREE.BoxGeometry(Math.min(2.6, w * 0.6), 0.75, 0.07),
            [mat(col), mat(col), mat(col), mat(col), new THREE.MeshStandardMaterial({map: signTexture(kind, kind === 'pharmacy' ? '#FFFFFF' : col), emissive: '#FFFFFF', emissiveIntensity: 0.08}), mat(col)]);
        sign.position.set(0, h - 0.55, d / 2 + 0.06);
        g.add(sign);
        if (kind === 'pharmacy') { // the green cross that glows, sticking out from the wall
            const cross = new THREE.Group();
            cross.add(box(0.14, 0.5, 0.14, glowMat('#3EBF6A'), 0, -0.25, 0), box(0.5, 0.14, 0.14, glowMat('#3EBF6A'), 0, -0.07, 0));
            cross.position.set(-w / 2 + 0.4, 2.6, d / 2 + 0.35);
            g.add(cross, box(0.05, 0.05, 0.35, mat('#3B3B40'), -w / 2 + 0.4, 2.55, d / 2 + 0.15));
        }
        // Outside the door
        const fz = d / 2 + 0.9;
        if (kind === 'cafe' || kind === 'icecream') for (const tx of [-w * 0.3, w * 0.05]) {
            const top = new THREE.Mesh(new THREE.CylinderGeometry(0.32, 0.32, 0.05, 12), mat('#FFFFFF'));
            top.position.set(tx, 0.72, fz);
            g.add(top, box(0.06, 0.7, 0.06, mat('#3B3B40'), tx, 0, fz));
            for (const sx of [-0.45, 0.45]) g.add(box(0.28, 0.45, 0.28, mat(col), tx + sx, 0, fz));
        } else if (kind === 'florist' || kind === 'grocer') for (let c = 0; c < 3; c++) {
            const crate = box(0.7, 0.35, 0.45, mat('#B98A57'), -w * 0.35 + c * 0.8, 0.25, fz - 0.2);
            g.add(crate, box(0.08, 0.25, 0.4, mat('#8A6A45'), -w * 0.35 + c * 0.8 - 0.3, 0, fz - 0.2), box(0.08, 0.25, 0.4, mat('#8A6A45'), -w * 0.35 + c * 0.8 + 0.3, 0, fz - 0.2));
            const fruit = kind === 'grocer' ? ['#F08A24', '#E0453A', '#9CD86B'][c] : ['#F25F5C', '#FFD23F', '#9B5DE5'][c];
            for (let f = 0; f < 6; f++) {
                const b = new THREE.Mesh(new THREE.IcosahedronGeometry(0.08, 0), mat(fruit));
                b.position.set(-w * 0.35 + c * 0.8 - 0.22 + (f % 3) * 0.22, 0.66, fz - 0.3 + Math.floor(f / 3) * 0.2);
                g.add(b);
            }
        } else if (kind === 'books') {
            g.add(box(1.0, 0.7, 0.4, mat('#7B4E2C'), -w * 0.3, 0, fz - 0.2));
            for (let b = 0; b < 6; b++) g.add(box(0.12, 0.3, 0.25, mat(roofs[b]), -w * 0.3 - 0.4 + b * 0.16, 0.7, fz - 0.2));
        } else if (kind === 'bakery') {
            g.add(box(1.3, 0.08, 0.38, mat(PALETTE.wood), -w * 0.28, 0.4, fz), box(1.3, 0.35, 0.06, mat(PALETTE.wood), -w * 0.28, 0.5, fz - 0.17));
        }
        host(g, {kind: 'shop', w, h, d, doorX: w * 0.36, awning: kind !== 'pharmacy'});
        place(g, x, z, rot);
    };
    // A block of flats: several floors of windows, a flat roof with a water tank, sometimes a shop on the ground floor
    const flats = (x, z, rot, floors) => {
        const k = i++;
        const g = new THREE.Group();
        const w = 4.5 + rnd(k) * 1.5, d = 4, h = floors * 1.25 + 0.6;
        g.add(box(w, h, d, mat(walls[(k + 5) % walls.length]), 0, 0, 0), box(w + 0.15, 0.2, d + 0.15, mat('#8A7A6A'), 0, h, 0));
        windows(g, w, h, d, floors, k);
        for (let f = 1; f < floors; f++) if (rnd(k + f * 17) > 0.5) g.add(box(w * 0.5, 0.08, 0.5, mat('#C9CED4'), 0, 0.9 + f * 1.25 - 0.3, d / 2 + 0.25)); // balconies
        g.add(box(0.9, 1.3, 0.08, mat('#5A3218'), 0, 0, d / 2 + 0.04));
        const tank = new THREE.Mesh(new THREE.CylinderGeometry(0.35, 0.35, 0.7, 10), mat('#9AA3AD'));
        tank.position.set(w * 0.25, h + 0.55, -d * 0.2);
        g.add(tank);
        const cap = box(w + 0.15, 0.08, d + 0.15, mat('#F7FAFC'), 0, h + 0.2, 0);
        cap.visible = false;
        g.add(cap);
        world.snowCaps.push(cap);
        host(g, {kind: 'flats', w, h, d, doorX: 0});
        place(g, x, z, rot);
    };
    // A street tree (registered for the seasons and the holidays)
    const tree = (x, z, sc = 1) => {
        const g = new THREE.Group();
        g.add(box(0.24 * sc, 1.2 * sc, 0.24 * sc, mat(PALETTE.woodDark), 0, 0, 0));
        const foliage = [];
        [[0, 1.6, 0, 0.8], [0.35, 1.35, 0.2, 0.55], [-0.3, 1.4, -0.2, 0.6], [0, 2.1, 0, 0.55]].forEach(([dx, y, dz, rr], k) => {
            const b = new THREE.Mesh(new THREE.IcosahedronGeometry(rr * sc, 0), mat(k % 2 ? PALETTE.leafDark : PALETTE.leaf));
            b.position.set(dx * sc, y * sc, dz * sc);
            g.add(b);
            foliage.push(b);
        });
        const branches = new THREE.Group();
        for (const [rx, rz, l] of [[0.6, 0, 0.9], [-0.5, 0.4, 0.8], [0.2, -0.7, 0.7], [-0.2, 0.2, 1.0]]) {
            const br = new THREE.Mesh(new THREE.CylinderGeometry(0.03 * sc, 0.06 * sc, l * sc, 5), mat(PALETTE.woodDark));
            br.position.y = 1.2 * sc + l * sc * 0.4;
            br.rotation.set(rx, 0, rz);
            branches.add(br);
        }
        branches.visible = false;
        g.add(branches);
        const holi = new THREE.Group();
        g.add(holi);
        world.trees.push({g, foliage, branches, i: world.trees.length, holi, sc});
        place(g, x, z, 0);
    };
    const lamp = (x, z, style = 0) => {
        const l = streetLamp(style);
        l.position.set(x, 0, z);
        add(world, l);
    };
    const pick = (x, z, rot, k, shopChance) => {
        if (level >= 8 && rnd(k + 40) > (level >= 18 ? 0.45 : 0.68)) flats(x, z, rot, 3 + Math.floor(rnd(k + 41) * (level >= 20 ? 4 : 2)));
        else if (rnd(k + 50) < shopChance) shop(x, z, rot);
        else house(x, z, rot);
    };
    const offStreet = (x) => Math.abs(x - xL) > 4.6 && Math.abs(x - xR) > 4.6;

    // Junctions. Main road × side streets: zebra crossings on every arm and traffic lights (the passing cars obey them,
    // see Traffic.update). Second road × side streets: roundabouts. Back street × side streets (a T): zebra crossings.
    for (const x of [xL, xR]) {
        junctionPatch(world, x, roadZ);
        for (const dx of [-3.1, 3.1]) zebra(world, x + dx, roadZ, 'x');
        for (const dz of [-4.9, 4.9]) zebra(world, x, roadZ + dz, 'z');
        for (const [sx, sz, rot, axis] of [[-2.7, -2.7, 0, 'main'], [2.7, 2.7, Math.PI, 'main'], [2.7, -2.7, -Math.PI / 2, 'side'], [-2.7, 2.7, Math.PI / 2, 'side']]) {
            const tl = trafficLight(axis);
            tl.group.position.set(x + sx, 0, roadZ + sz);
            tl.group.rotation.y = rot;
            add(world, tl.group);
            world.trafficLights.push(tl);
        }
        roundabout(world, x, zF);
        zebra(world, x, zB + 3.1, 'z');
        zebra(world, x + (x === xL ? 4.4 : -4.4), zB, 'x');
    }

    // The streets: one on each side of the plot, one behind, one more road across the main one
    for (const x of [xL, xR]) { // in pieces: the main road and the second road cross them
        street(world, -OUT - 40, roadZ - 2, x, 'z');
        street(world, roadZ + 2, zF - 1.8, x, 'z');
        street(world, zF + 1.8, zF + 14, x, 'z');
    }
    street(world, xL + 3.4, xR - 3.4, zB, 'x');
    street(world, xL - 40, xR + 40, zF, 'x');
    for (let z = zB - 2; z < zF + 12; z += 7) if (Math.abs(z - roadZ) > 4 && Math.abs(z - zF) > 4) { lamp(xL + 2.5, z, 0); lamp(xR - 2.5, z + 3, 0); }
    for (let x = xL - 34; x < xR + 34; x += 8) if (offStreet(x)) lamp(x, zF - 2.5, 1);

    // Across the main road: the shops street, with a little square and a fountain right in front of the door
    let k = 0;
    const gap = level < 5 ? 6.6 : 5.8;
    for (let x = xL - 26; x <= xR + 26; x += gap) {
        const xx = x + rnd(k) * 0.6;
        if (!offStreet(xx) || Math.abs(xx - door) < 4.4) { k++; continue; }
        if (rnd(k + 50) < (Math.abs(xx - door) < 16 ? 0.75 : 0.3)) shop(xx, roadZ + 7.4, Math.PI); // low buildings only: nothing tall in front of the restaurant
        else house(xx, roadZ + 7.4, Math.PI);
        k++;
    }
    {   // the square: paving, a fountain, two benches and a tree each side (the Christmas tree goes in the middle)
        const g = new THREE.Group();
        const pave = new THREE.Mesh(new THREE.PlaneGeometry(7.6, 6), new THREE.MeshStandardMaterial({map: pavingTexture(7.6, 6), roughness: 0.95}));
        pave.rotation.x = -Math.PI / 2;
        pave.position.set(0, -0.025, 0);
        g.add(pave);
        const basin = new THREE.Mesh(new THREE.CylinderGeometry(1.2, 1.3, 0.45, 20), mat('#CFC6B8'));
        basin.position.y = 0.22;
        const water = new THREE.Mesh(new THREE.CylinderGeometry(1.05, 1.05, 0.05, 20), new THREE.MeshStandardMaterial({color: '#7EC8E3', roughness: 0.1, metalness: 0.2}));
        water.position.y = 0.42;
        const column = new THREE.Mesh(new THREE.CylinderGeometry(0.15, 0.2, 1.1, 10), mat('#CFC6B8'));
        column.position.y = 0.8;
        const bowl = new THREE.Mesh(new THREE.CylinderGeometry(0.5, 0.25, 0.2, 14), mat('#CFC6B8'));
        bowl.position.y = 1.35;
        g.add(basin, water, column, bowl);
        for (const sx of [-2.8, 2.8]) {
            const bench = new THREE.Group();
            bench.add(box(1.4, 0.08, 0.4, mat(PALETTE.wood), 0, 0.38, 0), box(1.4, 0.35, 0.06, mat(PALETTE.wood), 0, 0.5, 0.18));
            for (const bx of [-0.6, 0.6]) bench.add(box(0.06, 0.42, 0.36, mat('#2F3A3A'), bx, 0, 0));
            bench.position.set(sx, 0, 1.6);
            g.add(bench);
        }
        g.position.set(door, 0, roadZ + 7.2);
        place(g, door, roadZ + 7.2, 0);
        world.plaza = {x: door, z: roadZ + 6.6};
        tree(door - 3.2, roadZ + 9.2, 1.1);
        tree(door + 3.2, roadZ + 9.2, 1.1);
    }
    // Behind the shops: houses facing the second road; beyond it, another row (more flats as the town grows)
    for (let x = xL - 24; x <= xR + 24; x += 6.4) if (offStreet(x)) pick(x + rnd(k) * 0.8, zF - 5, 0, k++, 0.15);
    for (let x = xL - 26; x <= xR + 26; x += 6.2) if (offStreet(x)) pick(x + rnd(k) * 0.8, zF + 5.4, Math.PI, k++, 0.35);
    if (level >= 12) for (let x = xL - 20; x <= xR + 20; x += 6.6) if (offStreet(x)) flats(x + rnd(k++), zF + 13, Math.PI, 3 + Math.floor(rnd(k) * (level >= 22 ? 5 : 3)));
    // Behind the restaurant, along the back street, and a row further back
    for (let x = xL + 5.5; x <= xR - 5.5; x += 6.3) pick(x + rnd(k) * 0.6, zB - 5, 0, k++, 0.2);
    for (let x = xL + 5; x <= xR - 5; x += 6.8) pick(x + rnd(k) * 0.6, zB - 13, Math.PI, k++, 0.1);
    // Left of the left street and right of the right one, all the way from the back to the main road
    for (let z = zB - 12; z < roadZ - 5; z += 6.2) {
        pick(xL - 6, z + rnd(k) * 0.6, Math.PI / 2, k++, 0.2);
        pick(xR + 6, z + rnd(k) * 0.6, -Math.PI / 2, k++, 0.2);
    }
    // Trees along the side streets and the second road
    for (let z = zB - 8; z < zF + 12; z += 9) if (Math.abs(z - roadZ) > 4 && Math.abs(z - zF) > 4) { tree(xL - 2.6, z + 4, 0.9); tree(xR + 2.6, z, 0.9); }
    for (let x = xL - 30; x < xR + 30; x += 9) if (offStreet(x)) tree(x + 4, zF + 2.6, 0.9);
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
        for (const s of [...(this.world.parkingStalls || []), ...(this.world.garageStalls || [])]) s.busy = s.car = null;
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
        const st = this.world.street, stall = [...(this.world.parkingStalls || []), ...(this.world.garageStalls || [])].find(s => !s.busy);
        if (!st || !stall) return null;
        stall.busy = guest;
        const gi = this.world.garageInfo;
        if (stall.garage && gi) { // down the ramp and out of sight
            const pts = [new THREE.Vector3(st.x0, 0, st.laneIn), new THREE.Vector3(gi.rx, 0, st.laneIn), new THREE.Vector3(gi.rx, 0, gi.zFront + 0.4),
                new THREE.Vector3(gi.rx, -gi.drop, gi.zFront - gi.len), new THREE.Vector3(gi.rx, -gi.drop, gi.zFront - gi.len - 1.2)];
            const car = this.addCar(pts, 5, () => {
                car.parked = true;
                car.mesh.visible = false;
                onParked && onParked(stall);
            });
            stall.car = car;
            return stall;
        }
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
        const gi = this.world.garageInfo;
        if (stall.garage && gi) { // back up the ramp, then off along the road
            car.mesh.visible = true;
            car.mesh.position.set(gi.rx, -gi.drop, gi.zFront - gi.len - 1.2);
            car.mesh.rotation.y = 0;
            car.points = [car.mesh.position.clone(), new THREE.Vector3(gi.rx, -gi.drop, gi.zFront - gi.len), new THREE.Vector3(gi.rx, 0, gi.zFront + 0.4), new THREE.Vector3(gi.rx + 0.8, 0, st.laneOut), new THREE.Vector3(st.x0, 0, st.laneOut)];
            car.i = 1;
            car.reverse = null;
            car.onDone = () => this.removeCar(car);
            return;
        }
        car.points = [car.mesh.position.clone(), new THREE.Vector3(stall.x, 0, st.D + OUT + 1.6), new THREE.Vector3(stall.x + 0.8, 0, st.laneOut), new THREE.Vector3(st.x0, 0, st.laneOut)];
        car.i = 1;
        car.reverse = 1; // the first leg is backwards
        car.onDone = () => this.removeCar(car);
    }

    removeCar(car) {
        this.group.remove(car.mesh);
        this.cars = this.cars.filter(c => c !== car);
    }

    /** Someone walking by: on the far sidewalk, or on the near one, right past the restaurant's door. */
    addWalker(far) {
        const st = this.world.street;
        if (!st) return;
        const east = Math.random() < 0.5;
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

    /** Someone who might have come in, but the restaurant is closed (or not taking guests): they walk on by. */
    passBy() {
        if (this.walkers.length < 22) this.addWalker(false);
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
        // Traffic lights: the main road has green most of the time, then the side streets (a 22 s cycle)
        this.lightClock = ((this.lightClock || 0) + dt) % 22;
        const lc = this.lightClock, main = lc < 11 ? 'green' : lc < 13.5 ? 'yellow' : 'red';
        const side = lc >= 14 && lc < 20 ? 'green' : lc >= 20 && lc < 22 ? 'yellow' : 'red';
        for (const tl of this.world.trafficLights || []) {
            const st = tl.axis === 'main' ? main : side;
            if (st === tl.state) continue;
            tl.state = st;
            for (const c of ['red', 'yellow', 'green']) tl[c].material.emissiveIntensity = c === st ? 2.2 : 0.05;
        }
        for (const car of [...this.cars]) {
            if (car.parked) continue;
            const target = car.points[car.i];
            if (!target) continue;
            const pos = car.mesh.position;
            const dx = target.x - pos.x, dz = target.z - pos.z, dist = Math.hypot(dx, dz);
            // Keep a distance from the car ahead in the same lane
            let speed = car.speed;
            // Stop at the line when the light is not green (passing cars, along the main road)
            if (car.passing && main !== 'green' && Math.abs(dx) > Math.abs(dz)) for (const j of this.world.junctions || []) {
                const stopX = j - Math.sign(dx) * 3.6, before = (stopX - pos.x) * Math.sign(dx);
                if (before > -0.2 && before < 4) speed = Math.min(speed, Math.max(0, before - 0.1) * 1.8);
            }
            if (car.passing) for (const o of this.cars) {
                if (o === car || Math.abs(o.mesh.position.z - pos.z) > 0.5) continue;
                const ahead = (o.mesh.position.x - pos.x) * Math.sign(dx);
                if (ahead > 0 && ahead < 3.2) speed = Math.min(speed, o.passing ? o.speed * 0.9 : 0);
            }
            if (car.i === car.points.length - 1 && !car.passing && car.onDone && car.reverse == null) speed = Math.min(speed, 1.2 + dist * 2); // slow down to park
            const step = Math.min(dist, speed * dt);
            if (dist > 0.001) {
                pos.y += (target.y - pos.y) * step / dist; // the garage ramp goes down
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
            if (this.walkers.length < want) this.addWalker(Math.random() < 0.55);
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


// ---------------------------------------------------------------- holidays

/**
 * The festivity going on in the restaurant's own calendar (months of 10 days, as gameDate() in app.js), or null:
 * Christmas (all December and the first days of January), Halloween (end of October), Easter (early April),
 * Carnival and Valentine's (February), the summer festival (mid August).
 */
export function holidayAt(month, day) {
    if (month === 11 || (month === 0 && day <= 3)) return 'christmas';
    if ((month === 9 && day >= 6) || (month === 10 && day === 1)) return 'halloween';
    if (month === 3 && day <= 7) return 'easter';
    if (month === 1 && day <= 4) return 'carnival';
    if (month === 1 && day >= 6 && day <= 8) return 'valentine';
    if (month === 7 && day >= 4 && day <= 8) return 'summer';
    return null;
}

const HOLI_COLORS = {
    christmas: ['#E0453A', '#3FAE5A', '#F6C945', '#3E7CB4', '#FFF4D6'],
    halloween: ['#F08A24', '#8E5BA8', '#F08A24', '#9CD86B'],
    easter: ['#F7A1C4', '#FFE27A', '#9FD8F0', '#B9E6A6', '#D9B8F2'],
    carnival: ['#E0453A', '#F6C945', '#3FAE5A', '#3E7CB4', '#8E5BA8', '#F08A24'],
    valentine: ['#E0453A', '#F7A1C4', '#FFFFFF'],
    summer: ['#F08A24', '#F6C945', '#E0453A', '#FFF4D6'],
};
const BULB_GEO = new THREE.SphereGeometry(0.06, 6, 5);
const bulb = (color, x, y, z, r = 1) => {
    const m = new THREE.Mesh(BULB_GEO, glowMat(color));
    m.position.set(x, y, z);
    if (r !== 1) m.scale.setScalar(r);
    return m;
};
/** A string of things between two points, sagging a little in the middle. */
function hangLine(g, a, b, n, make, sag = 0.25) {
    for (let k = 0; k <= n; k++) {
        const t = k / n;
        const p = a.clone().lerp(b, t);
        p.y -= Math.sin(t * Math.PI) * sag;
        g.add(make(p, k));
    }
}
const flag = (color, p) => { // a little bunting triangle
    const m = new THREE.Mesh(new THREE.ConeGeometry(0.13, 0.28, 3), mat(color));
    m.rotation.x = Math.PI;
    m.position.set(p.x, p.y - 0.14, p.z);
    return m;
};
const lantern = (color, p) => {
    const m = new THREE.Mesh(new THREE.SphereGeometry(0.16, 8, 6), glowMat(color));
    m.scale.y = 1.25;
    m.position.set(p.x, p.y - 0.2, p.z);
    return m;
};
const heart = (color, s = 1) => {
    const g = new THREE.Group();
    const m = mat(color);
    for (const sx of [-1, 1]) {
        const lobe = new THREE.Mesh(new THREE.SphereGeometry(0.09 * s, 8, 6), m);
        lobe.position.set(sx * 0.07 * s, 0.05 * s, 0);
        g.add(lobe);
    }
    const tip = new THREE.Mesh(new THREE.ConeGeometry(0.13 * s, 0.2 * s, 8), m);
    tip.rotation.x = Math.PI;
    tip.position.y = -0.06 * s;
    g.add(tip);
    return g;
};
const egg = (color, s = 1) => {
    const m = new THREE.Mesh(new THREE.SphereGeometry(0.09 * s, 10, 8), mat(color));
    m.scale.set(0.8, 1.1, 0.8);
    return m;
};
const pumpkin = (s = 1) => {
    const g = new THREE.Group();
    const body = new THREE.Mesh(new THREE.SphereGeometry(0.22 * s, 10, 8), mat('#F08A24'));
    body.scale.y = 0.75;
    body.position.y = 0.17 * s;
    const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.025 * s, 0.035 * s, 0.1 * s, 5), mat('#5E7A2E'));
    stem.position.y = 0.36 * s;
    g.add(body, stem);
    for (const [x, y, w] of [[-0.08, 0.22, 0.06], [0.08, 0.22, 0.06], [0, 0.11, 0.14]]) { // a glowing face
        const f = new THREE.Mesh(new THREE.BoxGeometry(w * s, 0.04 * s, 0.02), glowMat('#FFD23F'));
        f.position.set(x * s, y * s, 0.2 * s);
        g.add(f);
    }
    return g;
};
const ghost = () => {
    const g = new THREE.Group();
    const body = new THREE.Mesh(new THREE.ConeGeometry(0.14, 0.32, 8, 1, true), mat('#FFFFFF'));
    body.position.y = -0.1;
    const head = new THREE.Mesh(new THREE.SphereGeometry(0.1, 8, 6), mat('#FFFFFF'));
    head.position.y = 0.06;
    g.add(body, head);
    for (const sx of [-0.035, 0.035]) g.add(box(0.025, 0.035, 0.02, mat('#222222'), sx, 0.06, 0.09));
    return g;
};
const wreath = () => {
    const g = new THREE.Group();
    g.add(new THREE.Mesh(new THREE.TorusGeometry(0.2, 0.07, 6, 14), mat('#2E7D44')));
    const bow = new THREE.Mesh(new THREE.SphereGeometry(0.06, 6, 5), mat('#E0453A'));
    bow.position.y = -0.2;
    g.add(bow);
    for (let k = 0; k < 6; k++) g.add(bulb(['#E0453A', '#F6C945'][k % 2], Math.cos(k) * 0.2, Math.sin(k) * 0.2, 0.06, 0.6));
    return g;
};

/** Christmas tree for the square: stacked cones, lights all round, a star on top. */
function bigTree(colors) {
    const g = new THREE.Group();
    g.add(box(0.3, 0.6, 0.3, mat(PALETTE.woodDark), 0, 0, 0));
    [[1.5, 1.6, 0.5], [1.15, 1.4, 1.5], [0.8, 1.2, 2.4]].forEach(([r, h, y], k) => {
        const c = new THREE.Mesh(new THREE.ConeGeometry(r, h, 10), mat(k % 2 ? '#2E7D44' : '#256B39'));
        c.position.y = y + h / 2;
        g.add(c);
        for (let b = 0; b < 14; b++) {
            const a = b / 14 * Math.PI * 2 + k, yy = y + 0.2 + (b % 3) * 0.3, rr = r * (1 - (yy - y) / h) + 0.04;
            g.add(bulb(colors[(b + k) % colors.length], Math.cos(a) * rr, yy, Math.sin(a) * rr, 1.3));
        }
    });
    const star = new THREE.Mesh(new THREE.OctahedronGeometry(0.25, 0), glowMat('#F6C945'));
    star.position.y = 3.85;
    g.add(star);
    for (let k = 0; k < 4; k++) { // presents under the tree
        const p = box(0.35, 0.3, 0.35, mat(colors[k % colors.length]), Math.cos(k * 1.6) * 1.2, 0, Math.sin(k * 1.6) * 1.2);
        g.add(p, box(0.37, 0.05, 0.08, mat('#FFFFFF'), Math.cos(k * 1.6) * 1.2, 0.3, Math.sin(k * 1.6) * 1.2));
    }
    return g;
}

/**
 * Decorate the neighbourhood for a holiday (or take everything down with null): lights along the eaves of every
 * house and shop, wreaths or pumpkins or eggs by the doors, lights and ornaments in the trees, ribbons on the lamps,
 * strings of lights or bunting across the main road, and something special in the square.
 */
export function applyHoliday(world, name) {
    const clear = (g) => { while (g.children.length) { const c = g.children[0]; c.traverse(o => o.isMesh && o.geometry !== BULB_GEO && o.geometry.dispose()); g.remove(c); } };
    for (const h of world.holiHosts || []) clear(h.holi);
    for (const t of world.trees || []) t.holi && clear(t.holi);
    for (const l of world.holiLamps || []) clear(l.holi);
    if (world.holiStreet) clear(world.holiStreet);
    world.holidayShown = name;
    if (!name) return;
    const cols = HOLI_COLORS[name], C = (k) => cols[k % cols.length];
    const V = (x, y, z) => new THREE.Vector3(x, y, z);
    // Houses, shops and flats
    for (const h of world.holiHosts || []) {
        const g = h.holi, top = h.kind === 'shop' ? h.h - 0.05 : h.h - 0.08, fz = h.d / 2 + 0.08;
        const a = V(-h.w / 2, top, fz), b = V(h.w / 2, top, fz), n = Math.round(h.w / 0.32);
        if (name === 'christmas' || name === 'halloween' || name === 'valentine') hangLine(g, a, b, n, (p, k) => name === 'valentine' && k % 2 ? (() => { const hh = heart(C(k), 0.6); hh.position.copy(p); return hh; })() : bulb(C(k), p.x, p.y, p.z), 0.12);
        else if (name === 'summer') hangLine(g, a, b, Math.round(h.w / 0.7), (p, k) => lantern(C(k), p), 0.15);
        else hangLine(g, a, b, Math.round(h.w / 0.3), (p, k) => flag(C(k), p), 0.1); // easter, carnival: bunting
        const dx = h.doorX;
        if (name === 'christmas') {
            const w = wreath();
            w.position.set(dx, 1.55, h.d / 2 + 0.1);
            g.add(w);
            if (h.kind === 'house') { // lights down the corners too
                for (const sx of [-1, 1]) hangLine(g, V(sx * h.w / 2 + sx * 0.03, top, fz - 0.02), V(sx * h.w / 2 + sx * 0.03, 0.3, fz - 0.02), Math.round(top / 0.35), (p, k) => bulb(C(k + 2), p.x, p.y, p.z), 0);
            }
        } else if (name === 'halloween') {
            for (const sx of [-0.75, 0.75]) {
                const pk = pumpkin(0.9 + (sx > 0 ? 0.2 : 0));
                pk.position.set(dx + sx, 0, h.d / 2 + 0.45);
                g.add(pk);
            }
        } else if (name === 'easter') {
            for (let k = 0; k < 5; k++) {
                const e = egg(C(k));
                e.position.set(dx - 1 + k * 0.18 + (k % 2) * 0.05, 0.09, h.d / 2 + 0.55 + (k % 2) * 0.12);
                g.add(e);
            }
        } else if (name === 'valentine') {
            const hh = heart('#E0453A', 1.4);
            hh.position.set(dx, 1.55, h.d / 2 + 0.12);
            g.add(hh);
        }
    }
    // Trees: lights or ornaments among the leaves (bare trees get them on the branches)
    for (const t of world.trees || []) {
        if (!t.holi) continue;
        const sc = t.sc || 1;
        for (let k = 0; k < 12; k++) {
            const a = k * 2.399 + t.i, y = (1.15 + (k % 4) * 0.3) * sc, r = (0.75 - (k % 4) * 0.1) * sc;
            const x = Math.cos(a) * r, z = Math.sin(a) * r;
            if (name === 'christmas' || name === 'halloween') t.holi.add(bulb(C(k), x, y, z, 1.2));
            else if (name === 'easter') { const e = egg(C(k), 0.8); e.position.set(x, y - 0.1, z); t.holi.add(e); }
            else if (name === 'valentine' && k % 2) { const hh = heart(C(k), 0.7); hh.position.set(x, y, z); t.holi.add(hh); }
            else if (name === 'summer' && k % 3 === 0) t.holi.add(lantern(C(k), V(x, y + 0.1, z)));
            else if (name === 'carnival' && k % 2) t.holi.add(bulb(C(k), x, y, z, 1.6));
        }
        if (name === 'christmas') {
            const star = new THREE.Mesh(new THREE.OctahedronGeometry(0.14 * sc, 0), glowMat('#F6C945'));
            star.position.y = 2.75 * sc;
            t.holi.add(star);
        }
        if (name === 'halloween' && t.i % 3 === 0) {
            const gh = ghost();
            gh.position.set(0.6 * sc, 1.0 * sc, 0.3 * sc);
            t.holi.add(gh);
        }
    }
    // Street lamps: a ribbon and a garland round the pole
    for (const l of world.holiLamps || []) {
        const ring = new THREE.Mesh(new THREE.TorusGeometry(0.12, 0.05, 5, 10), mat(name === 'christmas' ? '#2E7D44' : C(1)));
        ring.rotation.x = Math.PI / 2;
        ring.position.y = 1.9;
        const bow = new THREE.Mesh(new THREE.SphereGeometry(0.09, 6, 5), mat(name === 'christmas' ? '#E0453A' : C(0)));
        bow.scale.set(1.6, 1, 0.6);
        bow.position.set(0, 2.05, 0.1);
        l.holi.add(ring, bow);
        if (name === 'halloween') { const pk = pumpkin(0.7); pk.position.set(0.25, 0, 0.1); l.holi.add(pk); }
    }
    // Across the main road: lights or bunting from one sidewalk to the other, and the square
    const st = world.street;
    if (st && world.holiStreet) {
        const z1 = st.D + OUT + 2.4, z2 = st.roadZ + 2.4;
        for (let x = -8; x <= st.W + 8; x += 6) {
            const a = V(x, 3.3, z1), b = V(x + 1.5, 3.3, z2);
            if (name === 'christmas' || name === 'halloween') hangLine(world.holiStreet, a, b, 18, (p, k) => bulb(C(k + x), p.x, p.y, p.z, 1.4), 0.5);
            else if (name === 'summer') hangLine(world.holiStreet, a, b, 9, (p, k) => lantern(C(k), p), 0.5);
            else if (name === 'valentine') hangLine(world.holiStreet, a, b, 9, (p, k) => { const hh = heart(C(k), 1.1); hh.position.copy(p); return hh; }, 0.5);
            else hangLine(world.holiStreet, a, b, 16, (p, k) => flag(C(k), p), 0.5);
        }
        const pl = world.plaza;
        if (pl) {
            let piece = null;
            if (name === 'christmas') piece = bigTree(cols);
            else if (name === 'halloween') {
                piece = new THREE.Group();
                [[0, 0, 2.2], [0.6, 0.3, 1.4], [-0.6, 0.2, 1.6], [0.1, -0.6, 1.2]].forEach(([x, z, s]) => { const p = pumpkin(s); p.position.set(x, 0, z); piece.add(p); });
            } else if (name === 'easter') {
                piece = new THREE.Group();
                const e = egg('#F7A1C4', 9);
                e.position.y = 0.95;
                piece.add(e);
                for (let k = 0; k < 3; k++) piece.add(box(1.0, 0.08, 0.06, mat(C(k + 1)), 0, 0.6 + k * 0.4, 0.62));
            } else if (name === 'valentine') {
                piece = heart('#E0453A', 6);
                piece.position.y = 1.4;
            }
            if (piece) {
                piece.position.x += pl.x;
                piece.position.z += pl.z - 1.8;
                world.holiStreet.add(piece);
            }
        }
    }
    // One mesh per colour per house, tree, lamp: thousands of little bulbs would cost too much to draw
    const none = new Set();
    for (const h of world.holiHosts || []) mergeStatic(h.holi, none);
    for (const t of world.trees || []) t.holi && mergeStatic(t.holi, none);
    for (const l of world.holiLamps || []) mergeStatic(l.holi, none);
    if (world.holiStreet) mergeStatic(world.holiStreet, none);
}
