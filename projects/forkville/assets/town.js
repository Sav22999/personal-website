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

/** The middle of the doorway (a wide door spans two tiles). */
const doorMidOf = (world, W) => (world.door ?? Math.floor(W / 2)) + (world.doorW || 1) / 2;

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
        if (Math.abs(x - doorMidOf(world, W)) < 2) continue;
        if (world.junctions.some(j => Math.abs(x - j) < 4.4)) continue; // no centre line across a junction
        add(world, box(1.4, 0.05, 0.14, mat('#F4E9C8'), x, -0.02, roadZ));
    }
    for (let i = 0; i < 6; i++) add(world, box(0.34, 0.05, 3.4, mat('#F4F1E8'), doorMidOf(world, W) - 1.1 + i * 0.44, -0.02, roadZ));

    // The terrace in front (stone paving) and the path to the door across it
    const patio = new THREE.Mesh(new THREE.PlaneGeometry(W + OUT, OUT), new THREE.MeshStandardMaterial({map: pavingTexture(W + OUT, OUT), roughness: 0.95}));
    patio.rotation.x = -Math.PI / 2;
    patio.position.set((W + OUT) / 2, -0.035, D + OUT / 2);
    patio.receiveShadow = true;
    world.outdoor.add(patio);
    add(world, box(world.doorW || 1, 0.05, OUT + 0.4, mat('#D9C7AC'), doorMidOf(world, W), -0.045, D + OUT / 2 + 0.1)); // the path, as wide as the door

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
        if (Math.abs(x - doorMidOf(world, W)) < 4.5 && z > roadZ + 3 && z < roadZ + 11) return; // the square across from the door
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

    // Graphics quality (Account): low = the restaurant only (no quarter around it), medium = the streets and the
    // buildings near the restaurant, high = the whole quarter
    world.roundabouts = null;
    world.secondRoad = null;
    if ((world.quality || 'high') !== 'low') buildHouses(world, W, D, level, lotW, rnd); // lotW: street car park and garage ramp
    else {
        world.junctions = [];
        world.trafficLights = [];
    }
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
    // The centre line, kept off the last few metres at each end (junctions and their zebra crossings are there)
    for (let t = a + 3.5; t < b - 3.5; t += 3) g.add(along === 'x' ? box(1.3, 0.05, 0.12, line, t, -0.03, at) : box(0.12, 0.05, 1.3, line, at, -0.03, t));
    mergeStatic(g, new Set());
    g.traverse(m => m.isMesh && (m.receiveShadow = true));
    world.outdoor.add(g);
}

/** Zebra stripes across a road: 'x' = the road runs along x (stripes at x), 'z' = it runs along z. */
function zebra(world, x, z, along, len = 0.9) {
    const g = new THREE.Group(), white = mat('#F4F1E8');
    for (let i = 0; i < 7; i++) {
        const o = -1.5 + i * 0.5;
        g.add(along === 'x' ? box(len, 0.05, 0.3, white, x, -0.005, z + o) : box(0.3, 0.05, len, white, x + o, -0.005, z));
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
    const roadZ = D + OUT + 4.5, door = doorMidOf(world, W);
    const xL = -lotW - 9.5, xR = W + OUT + 5.5, zB = -OUT - 5, zF = roadZ + 20;
    const cx = W / 2, cz = D / 2;
    world.holiHosts = [];
    world.plaza = null;
    world.roundabouts = [{x: xL, z: zF}, {x: xR, z: zF}];
    world.secondRoad = {z: zF, xL, xR};
    let i = 0;
    // Far away buildings do not cast shadows (they would only cost time)
    const near30 = (x, z) => Math.hypot(x - W / 2, z - D / 2) < 30;
    const place = (g, x, z, rot) => {
        if (world.quality === 'medium' && !near30(x, z)) return; // medium: only the buildings close to the restaurant
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
        // Across the side street where the main road's sidewalks run on (the walkers cross here, on their green)
        zebra(world, x, roadZ - 2.6, 'z', 1.5);
        zebra(world, x, roadZ + 3.1, 'z', 1.5);
        for (const [sx, sz, rot, axis] of [[-2.7, -2.7, 0, 'main'], [2.7, 2.7, Math.PI, 'main'], [2.7, -2.7, -Math.PI / 2, 'side'], [-2.7, 2.7, Math.PI / 2, 'side']]) {
            const tl = trafficLight(axis);
            tl.group.position.set(x + sx, 0, roadZ + sz);
            tl.group.rotation.y = rot;
            add(world, tl.group);
            world.trafficLights.push(tl);
        }
        roundabout(world, x, zF);
        // A zebra crossing on each arm of the roundabout, just outside the ring (cars give way to people on them)
        zebra(world, x - 4.8, zF, 'x');
        zebra(world, x + 4.8, zF, 'x');
        zebra(world, x, zF - 4.8, 'z');
        zebra(world, x, zF + 4.8, 'z');
        // The T where the back street meets the side street: the side street's sidewalk stops for the back street's mouth
        const inward = x === xL ? 1 : -1;
        const mouth = box(1.7, 0.07, 3.6, mat('#5B5754'), x + inward * 2.55, -0.035, zB);
        mouth.receiveShadow = true;
        world.outdoor.add(mouth);
        zebra(world, x, zB + 3.1, 'z');
        zebra(world, x + inward * 4.4, zB, 'x');
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
        // The water stands a little above the stone (never level with it: two surfaces at the same height flicker), inside a rim
        const waterMat = new THREE.MeshStandardMaterial({color: '#7EC8E3', roughness: 0.15, metalness: 0.05, emissive: '#2C6E8A', emissiveIntensity: 0.15, polygonOffset: true, polygonOffsetFactor: -2});
        const water = new THREE.Mesh(new THREE.CylinderGeometry(1.12, 1.12, 0.04, 28), waterMat);
        water.position.y = 0.455;
        const rim = new THREE.Mesh(new THREE.TorusGeometry(1.2, 0.1, 6, 32), mat('#D8D0C2'));
        rim.rotation.x = Math.PI / 2;
        rim.position.y = 0.47;
        const bowlWater = new THREE.Mesh(new THREE.CylinderGeometry(0.42, 0.42, 0.03, 18), waterMat);
        bowlWater.position.y = 1.465;
        // Jets: a spout from the top and thin streams falling from the bowl into the basin
        const jetMat = new THREE.MeshStandardMaterial({color: '#DFF6FF', transparent: true, opacity: 0.55, roughness: 0.1, emissive: '#9FD8EC', emissiveIntensity: 0.2, depthWrite: false});
        const spout = new THREE.Mesh(new THREE.CylinderGeometry(0.03, 0.06, 0.5, 8), jetMat);
        spout.position.y = 1.7;
        g.add(rim, bowlWater, spout);
        for (let k = 0; k < 6; k++) {
            const a = k / 6 * Math.PI * 2, st = new THREE.Mesh(new THREE.CylinderGeometry(0.018, 0.03, 0.95, 5), jetMat);
            st.position.set(Math.cos(a) * 0.48, 0.98, Math.sin(a) * 0.48);
            g.add(st);
        }
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
        if (Math.abs(x - doorMidOf(world, W)) < 1.2) continue; // keep the path clear
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
        if (points[1]) mesh.rotation.y = Math.atan2(points[1].x - points[0].x, points[1].z - points[0].z);
        // Indicators: an amber light at each corner, blinking on the side of the next turn
        const blink = {L: [], R: []};
        for (const [side, sx] of [['L', 0.47], ['R', -0.47]]) for (const sz of [0.98, -0.98]) {
            const m = new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.08, 0.06), new THREE.MeshStandardMaterial({color: '#FF9F1C', emissive: '#FF9F1C', emissiveIntensity: 0}));
            m.position.set(sx, 0.48, sz);
            mesh.add(m);
            blink[side].push(m);
        }
        mesh.userData.blink = blink;
        this.group.add(mesh);
        const car = {mesh, points, i: 1, speed, onDone, stuck: 0};
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
            const pts = [new THREE.Vector3(st.x0, 0, st.laneIn), new THREE.Vector3(gi.rx - 2.2, 0, st.laneIn), new THREE.Vector3(gi.rx, 0, st.laneIn - 1.6),
                new THREE.Vector3(gi.rx, 0, gi.zFront + 0.4), ...rampPoints(gi, false), new THREE.Vector3(gi.rx, -gi.drop, gi.zFront - gi.len - 1.2)];
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
        car.guest = true;
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
            car.points = [car.mesh.position.clone(), ...rampPoints(gi, true), new THREE.Vector3(gi.rx, 0, gi.zFront + 0.4), new THREE.Vector3(gi.rx, 0, st.laneIn + 0.2),
                new THREE.Vector3(gi.rx - 1.4, 0, st.laneOut), new THREE.Vector3(st.x0, 0, st.laneOut)];
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

    /**
     * Someone walking by, always on a sidewalk: along the far one or the near one (right past the restaurant's door),
     * or coming down a side street from the back of the quarter and turning onto the main road's sidewalk.
     */
    addWalker(far, fromSide = false) {
        const st = this.world.street;
        if (!st) return;
        const east = Math.random() < 0.5;
        const z = far ? st.farWalk + (Math.random() - 0.5) * 0.6 : st.D + OUT + 1.75 + Math.random() * 0.3;
        const xEnd = east ? st.x1 - 4 : st.x0 + 4;
        let pts;
        const js = this.world.junctions || [];
        if (fromSide && js.length && !far) { // down a side street's sidewalk (the side towards where they are going)
            const j = js[Math.floor(Math.random() * js.length)], x = j + (east ? 2.55 : -2.55);
            pts = [new THREE.Vector3(x, 0, -OUT - 34), new THREE.Vector3(x, 0, z), new THREE.Vector3(xEnd, 0, z)];
        } else pts = [new THREE.Vector3(east ? st.x0 + 4 : st.x1 - 4, 0, z), new THREE.Vector3(xEnd, 0, z)];
        const mesh = personModel('customer', null, Math.random() * 1000);
        mesh.position.copy(pts[0]);
        this.group.add(mesh);
        const dog = Math.random() < 0.12 ? dogModel() : null;
        if (dog) {
            dog.position.copy(mesh.position);
            this.group.add(dog);
        }
        this.walkers.push({mesh, dog, pts, i: 1, speed: 1.0 + Math.random() * 0.6, clock: Math.random() * 10, mood: 50 + Math.random() * 50, far});
    }

    /** Someone who might have come in, but the restaurant is closed (or not taking guests): they walk on by. */
    passBy() {
        if (this.walkers.length < 22) this.addWalker(false);
    }

    update(dt) {
        const st = this.world.street;
        if (!st) return;
        const q = this.world.quality || 'high';
        const h = this.hour(), busy = q === 'low' ? 0 : streetBusy(h) * (q === 'medium' ? 0.5 : 1);
        const wet = ['drizzle', 'rain', 'hail', 'light_snow', 'snow'].includes(this.world.env?.weather);
        // Traffic lights: the main road has green most of the time, then the side streets (a 22 s cycle)
        this.lightClock = ((this.lightClock || 0) + dt) % 22;
        const lc = this.lightClock, main = lc < 11 ? 'green' : lc < 13.5 ? 'yellow' : 'red';
        const side = lc >= 14 && lc < 20 ? 'green' : lc >= 20 && lc < 22 ? 'yellow' : 'red';
        for (const tl of this.world.trafficLights || []) {
            const s2 = tl.axis === 'main' ? main : side;
            if (s2 === tl.state) continue;
            tl.state = s2;
            for (const c of ['red', 'yellow', 'green']) tl[c].material.emissiveIntensity = c === s2 ? 2.2 : 0.05;
        }
        this.light = {main, side};
        // Passing cars: along the main road, down a side street onto it, or off it into a side street
        this.carIn -= dt;
        const passing = this.cars.filter(c => c.passing).length;
        if (this.carIn <= 0) {
            this.carIn = (2 + Math.random() * 4) / Math.max(0.05, busy);
            if (q !== 'low' && passing < 2 + Math.round(busy * 6)) this.spawnPassing(st, q === 'medium');
        }
        // The second road, with its roundabouts: its own flow of cars and of people
        this.carIn2 = (this.carIn2 ?? 3) - dt;
        if (this.carIn2 <= 0 && this.world.roundabouts && q === 'high') {
            this.carIn2 = (3 + Math.random() * 5) / Math.max(0.05, busy);
            if (this.cars.filter(c => c.second).length < 2 + Math.round(busy * 4)) this.spawnSecond(st);
        }
        this.walkIn2 = (this.walkIn2 ?? 2) - dt;
        if (this.walkIn2 <= 0 && this.world.roundabouts && q === 'high') {
            this.walkIn2 = (2.5 + Math.random() * 4) / Math.max(0.1, busy);
            if (this.walkers.filter(w => w.second).length < Math.round((1 + busy * 5) * (wet ? 0.45 : 1))) this.addSecondWalker();
        }
        this.blinkClock = (this.blinkClock || 0) + dt;
        const blinkOn = this.blinkClock % 0.7 < 0.38;
        for (const car of [...this.cars]) {
            if (car.parked) continue;
            const target = car.points[car.i];
            if (!target) continue;
            const pos = car.mesh.position;
            const dx = target.x - pos.x, dz = target.z - pos.z, dist = Math.hypot(dx, dz);
            const hx = dist > 0.001 ? dx / dist : 0, hz = dist > 0.001 ? dz / dist : 0;
            let speed = car.speed;
            const flat = Math.abs(target.y - pos.y) < 0.01 && pos.y > -0.05;
            if (car.passing || car.guest) speed = Math.min(speed, this.lightLimit(car, pos, hx, hz));
            // Never into the car ahead (any car, any direction: what matters is what is in front of the bonnet)
            let blocked = false;
            car.blockedBy = null;
            // On a roundabout's ring only the cars on the ring count (those waiting at its edge give way, see below)
            const onRing = (p) => (this.world.roundabouts || []).some(rb => Math.hypot(p.x - rb.x, p.z - rb.z) < 3.05);
            const ringMe = onRing(pos);
            // In the middle of a crossing, likewise: the cars waiting at its edges wait for this one, not the other way
            const inCross = (p) => !!st && (this.world.junctions || []).some(j => Math.abs(p.x - j) < 2.3 && Math.abs(p.z - st.roadZ) < 2.3);
            const crossMe = inCross(pos);
            if (flat) for (const o of this.cars) {
                if (o === car || (o.parked && !o.mesh.visible) || o.mesh.position.y < -0.3) continue;
                if (ringMe && !onRing(o.mesh.position)) continue;
                if (crossMe && !inCross(o.mesh.position)) continue;
                const ox = o.mesh.position.x - pos.x, oz = o.mesh.position.z - pos.z;
                const ahead = ox * hx + oz * hz, lateral = Math.abs(ox * hz - oz * hx);
                if (ahead > 0.4 && ahead < 3.4 && lateral < 1.05) {
                    speed = Math.min(speed, Math.max(0, ahead - 2.4) * 2);
                    blocked = true;
                    car.blockedBy = o;
                }
            }
            // The middle of a crossing takes one direction at a time: don't drive in while a car going another way is in it
            if (flat && st) for (const j of this.world.junctions || []) {
                const inBox = (p) => Math.abs(p.x - j) < 2.3 && Math.abs(p.z - st.roadZ) < 2.3;
                const nx = pos.x + hx * 1.6, nz = pos.z + hz * 1.6;
                if (inBox(pos) || !inBox({x: nx, z: nz})) continue;
                for (const o of this.cars) {
                    if (o === car || !inBox(o.mesh.position)) continue;
                    const oy = o.mesh.rotation.y, same = Math.abs(Math.sin(oy) - hx) < 0.35 && Math.abs(Math.cos(oy) - hz) < 0.35;
                    if (!same) speed = 0;
                }
            }
            // A roundabout: wait at its edge while a car already going round is about to pass the way in
            if (flat) for (const rb of this.world.roundabouts || []) {
                const dIn = Math.hypot(pos.x - rb.x, pos.z - rb.z), dNext = Math.hypot(target.x - rb.x, target.z - rb.z);
                if (dIn < 3.3 || dIn > 5.2 || dNext > 3.1) continue;
                for (const o of this.cars) {
                    if (o === car) continue;
                    const op = o.mesh.position;
                    if (Math.hypot(op.x - rb.x, op.z - rb.z) < 3.0 && Math.hypot(op.x - target.x, op.z - target.z) < 3.2) speed = 0;
                }
            }
            // Joining a lane (out of the car park, out of a side street): wait for a gap in the traffic on it
            if (flat && !car.passing && st && Math.abs(pos.z - target.z) > 0.3) for (const laneZ of [st.laneIn, st.laneOut]) {
                if (Math.abs(target.z - laneZ) > 0.15) continue;
                for (const o of this.cars) {
                    if (o === car || !o.passing || Math.abs(o.mesh.position.z - laneZ) > 0.6) continue;
                    const towards = (target.x - o.mesh.position.x) * Math.sign((o.points[o.i]?.x ?? target.x) - o.mesh.position.x);
                    if (towards > -1.5 && towards < 8 && Math.abs(pos.z - laneZ) < 2.6) speed = 0;
                }
            }
            // Nor into anyone walking across in front
            if (flat) for (const w of this.walkers) {
                if (!this.onRoad(w)) continue; // people on the sidewalk are no obstacle
                const ox = w.mesh.position.x - pos.x, oz = w.mesh.position.z - pos.z;
                const ahead = ox * hx + oz * hz, lateral = Math.abs(ox * hz - oz * hx);
                if (ahead > 0 && ahead < 3.6 && lateral < 1.2) {
                    speed = 0;
                    blocked = true;
                }
            }
            // Two cars waiting for each other in a crossing would wait for ever: after a while the blocked one creeps on
            car.stuck = blocked && speed < 0.05 ? car.stuck + dt : 0;
            const o = car.blockedBy;
            if (car.stuck > 4 && o?.blockedBy === car && this.cars.indexOf(car) < this.cars.indexOf(o)) speed = Math.max(speed, 0.8); // mutual: one goes first
            if (car.i === car.points.length - 1 && !car.passing && car.onDone && car.reverse == null) speed = Math.min(speed, 1.2 + dist * 2); // slow down to park
            if (!flat) speed = Math.min(speed, 2.2); // the garage ramp: slowly, nose down (or up)
            const step = Math.min(dist, speed * dt);
            if (dist > 0.001) {
                const dy = target.y - pos.y;
                pos.y += dy * step / dist;
                pos.x += hx * step;
                pos.z += hz * step;
                const yaw = Math.atan2(dx, dz) + (car.reverse && car.i === 1 ? Math.PI : 0);
                let d = yaw - car.mesh.rotation.y;
                d = Math.atan2(Math.sin(d), Math.cos(d));
                car.mesh.rotation.y += d * Math.min(1, dt * (car.passing ? 5 : 8));
                // Tilted on the ramp: the pitch follows the slope (smoothly, so it noses over at the top)
                const pitch = Math.abs(dy) > 0.01 ? -Math.atan2(dy, dist) * (car.reverse && car.i === 1 ? -1 : 1) : 0;
                car.mesh.rotation.x += (pitch - car.mesh.rotation.x) * Math.min(1, dt * 5);
                car.mesh.rotation.order = 'YXZ';
            }
            // Indicators: when the road turns at the next point (or the one after), blink on that side
            const next = car.points[car.i + 1];
            let turn = 0;
            if (next && dist < 9) {
                const nx = next.x - target.x, nz = next.z - target.z, nl = Math.hypot(nx, nz);
                if (nl > 0.3) {
                    const cross = hx * (nz / nl) - hz * (nx / nl), dot = hx * nx / nl + hz * nz / nl;
                    if (dot < 0.9) turn = cross > 0 ? -1 : 1; // +1 left, −1 right (seen from the driver)
                }
            }
            if (car.reverse && car.i === 1) turn = 0;
            const bl = car.mesh.userData.blink;
            if (bl) for (const [k, sgn] of [['L', 1], ['R', -1]]) for (const m of bl[k]) m.material.emissiveIntensity = turn === sgn && blinkOn ? 2.4 : 0;
            if (dist < 0.05) {
                car.i++;
                if (car.i >= car.points.length) {
                    const fn = car.onDone;
                    car.onDone = null;
                    fn && fn();
                }
            }
        }
        // People walking by (fewer in the rain), only on the sidewalks; they cross side streets on the zebras when green
        this.walkIn -= dt;
        const want = Math.round((1 + busy * 9) * (wet ? 0.45 : 1));
        if (this.walkIn <= 0 && q !== 'low') {
            this.walkIn = (1.5 + Math.random() * 3) / Math.max(0.1, busy);
            if (this.walkers.length < want) this.addWalker(Math.random() < 0.55, q === 'high' && Math.random() < 0.3);
        }
        for (const w of [...this.walkers]) {
            const target = w.pts[w.i];
            const pos = w.mesh.position;
            const dx = target.x - pos.x, dz = target.z - pos.z, dist = Math.hypot(dx, dz);
            let go = w.speed;
            // Along the main road, a side street crossing is ahead: wait at the kerb unless the walkers' light is green
            if (Math.abs(dx) > Math.abs(dz) && Math.abs(pos.z - st.roadZ) < 4) for (const j of this.world.junctions || []) {
                const kerb = j - Math.sign(dx) * 2.35, before = (kerb - pos.x) * Math.sign(dx);
                if (before > -0.05 && before < 0.6 && main !== 'green') go = 0;
            }
            const step = Math.min(dist, go * dt);
            if (dist > 0.001 && step > 0) {
                pos.x += dx / dist * step;
                pos.z += dz / dist * step;
                w.mesh.rotation.y = Math.atan2(dx, dz);
            }
            if (go > 0) w.clock += dt;
            animatePerson(w.mesh, go > 0 ? 'walk' : 'idle', w.clock * w.speed, {mood: w.mood});
            if (w.dog) {
                const side = new THREE.Vector3(Math.cos(w.mesh.rotation.y), 0, -Math.sin(w.mesh.rotation.y)).multiplyScalar(0.45);
                w.dog.position.copy(pos).add(side);
                w.dog.rotation.y = w.mesh.rotation.y;
                const legs = w.dog.userData.legs;
                if (go > 0) legs.forEach((l, i) => (l.rotation.x = Math.sin(w.clock * 14 + i * Math.PI) * 0.5));
                w.dog.userData.tail.rotation.y = Math.sin(this.blinkClock * 12) * 0.6;
            }
            if (dist < 0.05 && ++w.i >= w.pts.length) {
                this.group.remove(w.mesh);
                if (w.dog) this.group.remove(w.dog);
                this.walkers = this.walkers.filter(x => x !== w);
            }
        }
    }

    /**
     * How fast a car may go given the traffic lights: before a junction's stop line it slows to a stop on yellow and
     * red. Along the main road the main light counts; coming down a side street, the side light.
     */
    lightLimit(car, pos, hx, hz) {
        const st = this.world.street, L = this.light || {main: 'green', side: 'green'};
        let lim = Infinity;
        for (const j of this.world.junctions || []) {
            if (Math.abs(hx) > 0.7 && Math.abs(pos.z - st.roadZ) < 1.6 && L.main !== 'green') { // along the main road
                const stopX = j - Math.sign(hx) * 4.0, before = (stopX - pos.x) * Math.sign(hx);
                if (before > -0.2 && before < 5) lim = Math.min(lim, Math.max(0, before - 0.1) * 1.8);
            }
            if (Math.abs(hz) > 0.7 && Math.abs(pos.x - j) < 1.6 && L.side !== 'green') { // down (or up) a side street
                const stopZ = st.roadZ - Math.sign(hz) * 3.9, before = (stopZ - pos.z) * Math.sign(hz);
                if (before > -0.2 && before < 5) lim = Math.min(lim, Math.max(0, before - 0.1) * 1.8);
            }
        }
        return lim;
    }

    /** Is this person on the road (crossing it), not on a sidewalk? Only they make cars stop. */
    onRoad(w) {
        if (w.pts[w.i]?.cross) return true;
        const st = this.world.street, p = w.mesh.position;
        return Math.abs(p.z - st.roadZ) < 4 && (this.world.junctions || []).some(j => Math.abs(p.x - j) < 2.3);
    }

    /**
     * Points round a roundabout from angle a0 to a1 (radians, always increasing: every car turns the same way round,
     * keeping to the left as on these roads), on the ring between the island and the outer edge.
     */
    ring(rb, a0, a1) {
        const pts = [];
        if (a1 < a0) a1 += Math.PI * 2;
        const n = Math.max(2, Math.ceil((a1 - a0) / 0.35));
        for (let k = 0; k <= n; k++) {
            const a = a0 + (a1 - a0) * k / n;
            pts.push(new THREE.Vector3(rb.x + Math.cos(a) * 2.5, 0, rb.z + Math.sin(a) * 2.5));
        }
        return pts;
    }

    /**
     * A car on the second road: across the quarter through both roundabouts (either way), up a south side street and
     * round the roundabout to the main road, or from the main road down a side street and round onto the second road.
     */
    spawnSecond(st) {
        const V = (x, z) => new THREE.Vector3(x, 0, z);
        const [A, B] = this.world.roundabouts, zF = A.z, q = Math.asin(1 / 2.5), PI = Math.PI;
        const r = Math.random();
        let pts;
        if (r < 0.35) { // west → east, lane on the north side (zF − 1)
            pts = [V(st.x0, zF - 1), ...this.ring(A, PI + q, 2 * PI - q), ...this.ring(B, PI + q, 2 * PI - q), V(st.x1, zF - 1)];
        } else if (r < 0.7) { // east → west, lane on the south side (zF + 1)
            pts = [V(st.x1, zF + 1), ...this.ring(B, q, PI - q), ...this.ring(A, q, PI - q), V(st.x0, zF + 1)];
        } else if (r < 0.85) { // up a south side street (lane j − 1), round, north to the main road, left onto it (west)
            const rb = Math.random() < 0.5 ? A : B, j = rb.x;
            pts = [V(j - 1, zF + 14), ...this.ring(rb, PI / 2 + q, 3 * PI / 2 - q), V(j - 1, st.roadZ + 4.4), V(j - 1.4, st.roadZ + 2.2), V(j - 3.4, st.laneOut), V(st.x0, st.laneOut)];
        } else { // along the main road westwards, left down a side street (lane j + 1), round, east along the second road
            const rb = Math.random() < 0.5 ? A : B, j = rb.x;
            pts = [V(st.x1, st.laneOut), V(j + 3.4, st.laneOut), V(j + 1.5, st.roadZ + 1.6), V(j + 1, st.roadZ + 3.4), ...this.ring(rb, 3 * PI / 2 + q, 2 * PI - q)];
            pts.push(...(rb === A ? this.ring(B, PI + q, 2 * PI - q) : []), V(st.x1, zF - 1));
        }
        if (this.startTaken(pts[0])) return;
        const car = this.addCar(pts, 4 + Math.random() * 2.5, null);
        car.passing = true;
        car.second = true;
        car.onDone = () => this.removeCar(car);
    }

    /**
     * Someone walking along the second road's sidewalk (or coming up a south side street to it): at each roundabout they
     * go round by the side street's sidewalk and cross on its zebra (marked .cross: on the road, cars stop for them).
     */
    addSecondWalker() {
        const st = this.world.street, rbs = this.world.roundabouts, zF = rbs[0].z;
        const V = (x, z, cross = false) => Object.assign(new THREE.Vector3(x, 0, z), {cross});
        const east = Math.random() < 0.5, north = Math.random() < 0.5;
        const s = north ? -1 : 1, z = zF + s * (2.55 + (Math.random() - 0.5) * 0.3), zc = zF + s * 4.8;
        const dir = east ? 1 : -1, list = east ? rbs : [...rbs].reverse();
        const pts = [V(east ? st.x0 + 4 : st.x1 - 4, z)];
        for (const rb of list) pts.push(V(rb.x - dir * 2.55, z), V(rb.x - dir * 2.55, zc), V(rb.x + dir * 2.55, zc, true), V(rb.x + dir * 2.55, z));
        pts.push(V(east ? st.x1 - 4 : st.x0 + 4, z));
        const mesh = personModel('customer', null, Math.random() * 1000);
        mesh.position.copy(pts[0]);
        this.group.add(mesh);
        this.walkers.push({mesh, dog: null, pts, i: 1, speed: 1.0 + Math.random() * 0.6, clock: Math.random() * 10, mood: 50 + Math.random() * 50, second: true});
    }

    /** A car passing through: along the main road, from a side street onto it, or from it into a side street. */
    spawnPassing(st, mainOnly = false) {
        const V = (x, z) => new THREE.Vector3(x, 0, z);
        const js = mainOnly ? [] : this.world.junctions || [];
        const r = Math.random(), east = Math.random() < 0.5;
        const zE = st.laneIn, zW = st.laneOut, far = -OUT - 36;
        let pts;
        if (!js.length || r < 0.55) pts = east ? [V(st.x0, zE), V(st.x1, zE)] : [V(st.x1, zW), V(st.x0, zW)];
        else {
            const j = js[Math.floor(Math.random() * js.length)];
            if (r < 0.8) { // down a side street (its lane x = j + 1), then left (east) or right (west) onto the main road
                const top = [V(j + 1, far), V(j + 1, st.roadZ - 4.4)];
                pts = east ? [...top, V(j + 1.4, st.roadZ - 2.2), V(j + 3.4, zE), V(st.x1, zE)]
                    : [...top, V(j + 0.8, st.roadZ - 1.6), V(j - 0.2, zW - 0.2), V(j - 2.6, zW), V(st.x0, zW)];
            } else { // along the main road, then off it up a side street (lane x = j − 1)
                pts = east ? [V(st.x0, zE), V(j - 3.6, zE), V(j - 1.5, zE - 0.6), V(j - 1, st.roadZ - 3.4), V(j - 1, far)]
                    : [V(st.x1, zW), V(j + 3.4, zW), V(j + 0.4, zW - 0.6), V(j - 0.6, st.roadZ - 1.4), V(j - 1, st.roadZ - 3.6), V(j - 1, far)];
            }
        }
        if (this.startTaken(pts[0])) return;
        const car = this.addCar(pts, 4.5 + Math.random() * 3, null);
        car.passing = true;
        car.onDone = () => this.removeCar(car);
    }

    /** Is a car already near where a new one would start? (Then it waits for the next turn.) */
    startTaken(p) {
        return this.cars.some(c => c.mesh.position.distanceTo(p) < 3.5);
    }
}

/**
 * Points down the garage ramp (or up it, reversed): an S curve, so the car noses over gently at the top and levels out
 * at the bottom (its pitch follows the slope, see Traffic.update).
 */
function rampPoints(gi, up) {
    const pts = [];
    for (let k = 0; k <= 8; k++) {
        const t = k / 8, y = -gi.drop * (t * t * (3 - 2 * t));
        pts.push(new THREE.Vector3(gi.rx, y, gi.zFront - gi.len * t));
    }
    return up ? pts.reverse() : pts;
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
