/*
 * Forkville — every 3D model is built here from simple shapes: furniture, people, dishes, crops,
 * floor and wall textures (drawn on a canvas). No external art.
 *
 * Conventions: 1 tile = 1 unit. A model is centred on its footprint at y = 0 and "faces" +z
 * (a chair's sitter looks towards +z, a stove's door is on +z). Width runs along x before rotation.
 */
import * as THREE from '../vendor/three.module.min.js';
import {n as nameOf} from './i18n.js';

export const PALETTE = {
    cream: '#FFF3E0', terracotta: '#D9653B', olive: '#7A8B3A', berry: '#B43E5C', wood: '#B07A4A', woodDark: '#7B4E2C',
    steel: '#B8BEC6', ink: '#3B2418', gold: '#E2B23A', leaf: '#5E9A3E', leafDark: '#3F7A2E', water: '#5CB8D6', skin: ['#F1C7A5', '#D9A47E', '#A8714E', '#7A4E33', '#F6D7BE'],
};

const matCache = new Map();

/** Shared materials by colour (flat shading gives the low-poly look). */
export function mat(color, opts = {}) {
    const key = color + JSON.stringify(opts);
    if (!matCache.has(key)) {
        matCache.set(key, new THREE.MeshStandardMaterial({color, roughness: opts.roughness ?? 0.78, metalness: opts.metalness ?? 0, flatShading: opts.flat ?? true,
            transparent: !!opts.opacity, opacity: opts.opacity ?? 1, emissive: opts.emissive ?? '#000000', emissiveIntensity: opts.emissive ? (opts.glow ?? 0.8) : 0}));
    }
    return matCache.get(key);
}

function mesh(geo, material, x = 0, y = 0, z = 0) {
    const m = new THREE.Mesh(geo, material);
    m.position.set(x, y, z);
    m.castShadow = true;
    m.receiveShadow = true;
    return m;
}

const box = (w, h, d, color, x, y, z, o) => mesh(new THREE.BoxGeometry(w, h, d), mat(color, o), x, y + h / 2, z);
const cyl = (rt, rb, h, color, x, y, z, seg = 14, o) => mesh(new THREE.CylinderGeometry(rt, rb, h, seg), mat(color, o), x, y + h / 2, z);
const ball = (r, color, x, y, z, o, seg = 10) => mesh(new THREE.SphereGeometry(r, seg, Math.max(6, seg - 3)), mat(color, o), x, y, z);

function group(...children) {
    const g = new THREE.Group();
    children.flat().forEach(c => c && g.add(c));
    return g;
}

function legs(w, d, h, color, r = 0.035) {
    const out = [];
    for (const sx of [-1, 1]) for (const sz of [-1, 1]) out.push(cyl(r, r, h, color, sx * w / 2, 0, sz * d / 2, 6));
    return out;
}

// ---------------------------------------------------------------- furniture

const FURNITURE = {
    table_small: () => group(box(0.78, 0.06, 0.78, PALETTE.wood, 0, 0.7, 0), legs(0.62, 0.62, 0.7, PALETTE.woodDark),
        box(0.5, 0.012, 0.5, '#F4E2C6', 0, 0.76, 0), cyl(0.035, 0.045, 0.12, '#BFE3F0', 0, 0.77, 0, 8, {opacity: 0.7}),
        ball(0.04, '#F25F5C', 0, 0.93, 0, {}, 6), ball(0.03, '#FFD23F', 0.04, 0.9, 0.02, {}, 6), cyl(0.006, 0.006, 0.1, PALETTE.leafDark, 0, 0.84, 0, 4)),
    table_round: () => group(cyl(0.42, 0.42, 0.06, PALETTE.cream, 0, 0.7, 0, 20), cyl(0.44, 0.44, 0.12, PALETTE.cream, 0, 0.62, 0, 20),
        cyl(0.05, 0.05, 0.62, PALETTE.ink, 0, 0, 0, 8), cyl(0.22, 0.25, 0.04, PALETTE.ink, 0, 0, 0, 12),
        cyl(0.04, 0.05, 0.12, PALETTE.berry, 0.12, 0.76, -0.1, 8), ball(0.05, '#F2A0B4', 0.12, 0.92, -0.1)),
    table_marble: () => group(box(0.82, 0.07, 0.82, '#ECE8E2', 0, 0.68, 0, {roughness: 0.3}), box(0.84, 0.02, 0.84, PALETTE.gold, 0, 0.66, 0, {metalness: 0.6, roughness: 0.3}),
        legs(0.6, 0.6, 0.66, '#2C2522', 0.03), cyl(0.06, 0.04, 0.16, '#FFFFFF', 0, 0.75, 0, 10, {opacity: 0.55})),
    table_long: () => group(box(1.78, 0.06, 0.8, PALETTE.wood, 0, 0.7, 0), legs(1.6, 0.62, 0.7, PALETTE.woodDark),
        box(1.6, 0.012, 0.28, PALETTE.terracotta, 0, 0.76, 0), cyl(0.04, 0.04, 0.2, PALETTE.cream, -0.3, 0.76, 0, 8), cyl(0.04, 0.04, 0.2, PALETTE.cream, 0.3, 0.76, 0, 8)),

    chair_wood: () => group(box(0.42, 0.05, 0.42, PALETTE.wood, 0, 0.42, 0.02), legs(0.34, 0.34, 0.42, PALETTE.woodDark, 0.025),
        box(0.42, 0.42, 0.05, PALETTE.wood, 0, 0.47, -0.19), box(0.3, 0.06, 0.06, PALETTE.woodDark, 0, 0.78, -0.19)),
    chair_cushion: () => group(box(0.42, 0.05, 0.42, PALETTE.wood, 0, 0.42, 0.02), legs(0.34, 0.34, 0.42, PALETTE.woodDark, 0.025),
        box(0.38, 0.06, 0.38, PALETTE.terracotta, 0, 0.47, 0.03, {flat: false}), box(0.42, 0.46, 0.05, PALETTE.wood, 0, 0.47, -0.19),
        box(0.34, 0.3, 0.04, PALETTE.terracotta, 0, 0.55, -0.16)),
    stool_bar: () => group(cyl(0.2, 0.2, 0.07, PALETTE.berry, 0, 0.6, 0, 16, {flat: false}), cyl(0.035, 0.035, 0.6, PALETTE.steel, 0, 0, 0, 8, {metalness: 0.6, roughness: 0.3}),
        cyl(0.2, 0.22, 0.03, PALETTE.steel, 0, 0, 0, 14, {metalness: 0.6}), cyl(0.16, 0.16, 0.02, PALETTE.steel, 0, 0.28, 0, 14, {metalness: 0.6})),
    chair_velvet: () => group(box(0.5, 0.18, 0.48, PALETTE.berry, 0, 0.24, 0.01, {flat: false}), box(0.5, 0.5, 0.12, PALETTE.berry, 0, 0.42, -0.19, {flat: false}),
        box(0.08, 0.22, 0.44, PALETTE.berry, -0.23, 0.42, 0, {flat: false}), box(0.08, 0.22, 0.44, PALETTE.berry, 0.23, 0.42, 0, {flat: false}),
        legs(0.4, 0.38, 0.24, PALETTE.gold, 0.025)),

    stove_basic: () => group(box(0.88, 0.82, 0.8, PALETTE.cream, 0, 0, -0.02), box(0.9, 0.05, 0.82, '#2E2724', 0, 0.82, -0.02),
        cyl(0.13, 0.13, 0.025, '#151110', -0.2, 0.87, -0.05, 16), cyl(0.13, 0.13, 0.025, '#151110', 0.2, 0.87, -0.05, 16),
        box(0.6, 0.38, 0.02, '#3A3230', 0, 0.2, 0.39), box(0.4, 0.03, 0.03, PALETTE.steel, 0, 0.62, 0.4, {metalness: 0.7}),
        ...[-0.3, -0.1, 0.1, 0.3].map(x => cyl(0.03, 0.03, 0.03, PALETTE.ink, x, 0.7, 0.39, 8))),
    stove_steel: () => group(box(0.9, 0.84, 0.82, PALETTE.steel, 0, 0, -0.02, {metalness: 0.65, roughness: 0.35}), box(0.92, 0.05, 0.84, '#222', 0, 0.84, -0.02),
        ...[[-0.2, -0.2], [0.2, -0.2], [-0.2, 0.15], [0.2, 0.15]].map(([x, z]) => cyl(0.11, 0.11, 0.025, '#111', x, 0.89, z, 16)),
        box(0.66, 0.42, 0.02, '#1E1E22', 0, 0.18, 0.4, {roughness: 0.2}), box(0.92, 0.5, 0.3, PALETTE.steel, 0, 1.75, -0.28, {metalness: 0.65, roughness: 0.35})),
    oven_brick: () => group(box(0.92, 0.5, 0.9, '#9C4A2E', 0, 0, 0), mesh(new THREE.SphereGeometry(0.44, 14, 8, 0, Math.PI * 2, 0, Math.PI / 2), mat('#C2572B'), 0, 0.5, 0),
        box(0.3, 0.22, 0.1, '#FF9A3C', 0, 0.52, 0.38, {emissive: '#FF7A1A', glow: 1.2}), box(0.38, 0.05, 0.12, '#7B3A22', 0, 0.74, 0.38),
        cyl(0.07, 0.07, 0.45, '#7B3A22', 0.12, 0.85, -0.15, 8)),
    counter_wood: () => group(box(0.94, 0.92, 0.7, PALETTE.wood, 0, 0, -0.1), box(0.98, 0.06, 0.82, '#E8D3B0', 0, 0.92, -0.06),
        box(0.94, 0.06, 0.04, PALETTE.woodDark, 0, 0.3, 0.26), box(0.94, 0.06, 0.04, PALETTE.woodDark, 0, 0.62, 0.26)),
    counter_tile: () => group(box(0.94, 0.92, 0.7, '#3E7CB4', 0, 0, -0.1), box(0.98, 0.06, 0.82, '#F2EEE6', 0, 0.92, -0.06, {roughness: 0.3}),
        ...[-0.32, 0, 0.32].map(x => box(0.28, 0.82, 0.02, '#5B95C7', x, 0.05, 0.26))),

    plant_pot: () => group(cyl(0.2, 0.15, 0.32, PALETTE.terracotta, 0, 0, 0, 12), ball(0.24, PALETTE.leaf, 0, 0.5, 0), ball(0.17, PALETTE.leafDark, 0.12, 0.62, 0.05), ball(0.15, PALETTE.leaf, -0.1, 0.66, -0.06)),
    plant_tall: () => group(cyl(0.22, 0.18, 0.4, PALETTE.cream, 0, 0, 0, 12), cyl(0.03, 0.03, 0.9, PALETTE.woodDark, 0, 0.4, 0, 6),
        ...[0, 1, 2, 3, 4].map(i => {
            const leaf = mesh(new THREE.ConeGeometry(0.14, 0.6, 5), mat(i % 2 ? PALETTE.leaf : PALETTE.leafDark), 0, 0, 0);
            leaf.position.set(Math.cos(i * 1.3) * 0.18, 1.0 + i * 0.12, Math.sin(i * 1.3) * 0.18);
            leaf.rotation.set(Math.sin(i * 1.3) * 0.9, 0, -Math.cos(i * 1.3) * 0.9);
            return leaf;
        })),
    lamp_floor: () => group(cyl(0.16, 0.18, 0.04, PALETTE.ink, 0, 0, 0, 14), cyl(0.02, 0.02, 1.35, PALETTE.ink, 0, 0.04, 0, 6),
        cyl(0.12, 0.24, 0.3, '#F6D58E', 0, 1.3, 0, 14, {emissive: '#F6C35E', glow: 0.6}), lightBulb()),
    bookshelf: () => group(box(0.92, 1.7, 0.36, PALETTE.woodDark, 0, 0, -0.3), ...[0.05, 0.45, 0.85, 1.25].map(y => box(0.84, 0.03, 0.32, PALETTE.wood, 0, y, -0.28)),
        ...booksRow(0.08), ...booksRow(0.48), ...booksRow(0.88), ...booksRow(1.28)),
    jukebox: () => group(box(0.7, 0.9, 0.5, PALETTE.berry, 0, 0, -0.15), mesh(new THREE.CylinderGeometry(0.35, 0.35, 0.5, 16, 1, false, 0, Math.PI), mat(PALETTE.gold, {metalness: 0.4}), 0, 0.9, -0.15),
        box(0.5, 0.3, 0.02, '#FFD27A', 0, 0.45, 0.11, {emissive: '#FFB347', glow: 0.7}), box(0.6, 0.06, 0.02, '#5CB8D6', 0, 0.82, 0.11, {emissive: '#5CB8D6', glow: 0.6})),
    piano: () => group(box(1.7, 0.95, 0.5, '#1B1615', 0, 0.1, -0.2, {roughness: 0.25}), box(1.6, 0.05, 0.22, '#FFFFFF', 0, 0.7, 0.13), box(1.6, 0.02, 0.08, '#111', 0, 0.75, 0.08),
        legs(1.5, 0.4, 0.1, '#1B1615', 0.04), box(0.9, 0.06, 0.32, '#1B1615', 0, 0.45, 0.55), legs(0.8, 0.25, 0.45, '#1B1615', 0.025).map(l => (l.position.z += 0.55, l))),
    aquarium: () => {
        const g = group(box(1.8, 0.6, 0.55, PALETTE.woodDark, 0, 0, -0.15), box(1.76, 0.7, 0.5, PALETTE.water, 0, 0.6, -0.15, {opacity: 0.45, roughness: 0.1}),
            box(1.76, 0.06, 0.52, '#E8D9A8', 0, 0.6, -0.15), box(1.8, 0.05, 0.55, PALETTE.ink, 0, 1.3, -0.15));
        g.userData.fish = [];
        ['#FF8C42', '#FFD23F', '#F25F5C'].forEach((c, i) => {
            const fish = group(ball(0.06, c, 0, 0, 0), mesh(new THREE.ConeGeometry(0.04, 0.07, 4), mat(c), -0.07, 0, 0));
            fish.children[1].rotation.z = Math.PI / 2;
            fish.position.set(-0.5 + i * 0.5, 0.9 + i * 0.08, -0.15);
            g.add(fish);
            g.userData.fish.push(fish);
        });
        return g;
    },
    fountain: () => group(cyl(0.46, 0.48, 0.25, '#E6E0D6', 0, 0, 0, 18), cyl(0.4, 0.4, 0.02, PALETTE.water, 0, 0.24, 0, 18, {opacity: 0.7, roughness: 0.1}),
        cyl(0.08, 0.1, 0.55, '#E6E0D6', 0, 0.25, 0, 10), cyl(0.24, 0.12, 0.1, '#E6E0D6', 0, 0.8, 0, 14), cyl(0.2, 0.2, 0.02, PALETTE.water, 0, 0.89, 0, 14, {opacity: 0.7}),
        ball(0.07, PALETTE.water, 0, 1.0, 0, {opacity: 0.8})),
    statue_fork: () => group(box(0.6, 0.5, 0.6, '#E6E0D6', 0, 0, 0), box(0.66, 0.06, 0.66, '#D6CFC2', 0, 0.5, 0), forkMesh(PALETTE.gold, 1.2).translateY(0.56)),
    clock_tall: () => group(box(0.5, 1.9, 0.36, PALETTE.woodDark, 0, 0, -0.2), cyl(0.18, 0.18, 0.03, PALETTE.cream, 0, 1.45, -0.01, 18).rotateX(Math.PI / 2),
        box(0.02, 0.12, 0.01, PALETTE.ink, 0, 1.49, 0.01), box(0.09, 0.02, 0.01, PALETTE.ink, 0.04, 1.45, 0.01), box(0.26, 0.5, 0.02, '#E9C46A', 0, 0.55, -0.01, {opacity: 0.6})),
    cake_stand: () => group(cyl(0.3, 0.3, 0.04, PALETTE.cream, 0, 0.7, 0, 18), legs(0.36, 0.36, 0.7, PALETTE.woodDark, 0.025),
        cyl(0.2, 0.2, 0.12, '#F7C6D0', 0, 0.74, 0, 16, {flat: false}), cyl(0.14, 0.14, 0.1, '#FFF1E6', 0, 0.86, 0, 16, {flat: false}), ball(0.04, '#D7263D', 0, 0.98, 0),
        mesh(new THREE.SphereGeometry(0.3, 14, 8, 0, Math.PI * 2, 0, Math.PI / 2), mat('#FFFFFF', {opacity: 0.35, roughness: 0.1}), 0, 0.74, 0)),
};

function lightBulb() {
    const l = new THREE.PointLight('#FFD58A', 0.6, 3, 2);
    l.position.set(0, 1.35, 0);
    return l;
}

function booksRow(y) {
    const colours = [PALETTE.terracotta, PALETTE.olive, PALETTE.berry, '#3E7CB4', PALETTE.gold, PALETTE.cream];
    const out = [];
    let x = -0.38;
    let i = Math.round(y * 10);
    while (x < 0.36) {
        const w = 0.05 + ((i * 7) % 4) * 0.012;
        const h = 0.24 + ((i * 5) % 3) * 0.04;
        out.push(box(w, h, 0.22, colours[i % colours.length], x + w / 2, y, -0.27));
        x += w + 0.008;
        i++;
    }
    return out;
}

/** The Forkville fork: a handle and four tines (also the logo and the statue). */
export function forkMesh(color, height = 1) {
    const s = height;
    const m = mat(color, {metalness: 0.55, roughness: 0.35});
    const g = new THREE.Group();
    const handle = new THREE.Mesh(new THREE.CylinderGeometry(0.045 * s, 0.06 * s, 0.55 * s, 10), m);
    handle.position.y = 0.275 * s;
    const neck = new THREE.Mesh(new THREE.BoxGeometry(0.3 * s, 0.08 * s, 0.05 * s), m);
    neck.position.y = 0.6 * s;
    g.add(handle, neck);
    for (let i = 0; i < 4; i++) {
        const tine = new THREE.Mesh(new THREE.BoxGeometry(0.045 * s, 0.34 * s, 0.045 * s), m);
        tine.position.set((-0.12 + i * 0.08) * s, 0.81 * s, 0);
        const roof = new THREE.Mesh(new THREE.ConeGeometry(0.045 * s, 0.07 * s, 4), m);
        roof.position.set((-0.12 + i * 0.08) * s, 1.01 * s, 0);
        roof.rotation.y = Math.PI / 4;
        g.add(tine, roof);
    }
    g.traverse(o => o.isMesh && (o.castShadow = true));
    return g;
}


// ---------------------------------------------------------------- night lights

/** Materials that glow at night (lamps, lanterns, neon, screens): the scene raises their glow after sunset. */
const GLOW_MATS = new Map();

export function glowMat(color) {
    if (!GLOW_MATS.has(color)) GLOW_MATS.set(color, new THREE.MeshStandardMaterial({color, emissive: color, emissiveIntensity: 0.25, roughness: 0.5}));
    return GLOW_MATS.get(color);
}

/** The warm pool of light a lamp throws on the ground: invisible by day, glowing at night. */
export const POOL_MAT = new THREE.MeshBasicMaterial({color: '#FFD98A', transparent: true, opacity: 0, depthWrite: false, blending: THREE.AdditiveBlending, fog: false});
const POOL_TEX = (() => {
    const c = document.createElement('canvas');
    c.width = c.height = 64;
    const g = c.getContext('2d');
    const gr = g.createRadialGradient(32, 32, 0, 32, 32, 32);
    gr.addColorStop(0, 'rgba(255,255,255,1)');
    gr.addColorStop(0.5, 'rgba(255,255,255,0.45)');
    gr.addColorStop(1, 'rgba(255,255,255,0)');
    g.fillStyle = gr;
    g.fillRect(0, 0, 64, 64);
    const t = new THREE.CanvasTexture(c);
    return t;
})();
POOL_MAT.map = POOL_TEX;

export function lightPool(r = 1.6, x = 0, z = 0) {
    const m = new THREE.Mesh(new THREE.CircleGeometry(r, 24), POOL_MAT);
    m.rotation.x = -Math.PI / 2;
    m.position.set(x, 0.05, z);
    m.renderOrder = 2;
    return m;
}

/** 0 = full day, 1 = deep night. */
export function setNightGlow(k) {
    for (const m of GLOW_MATS.values()) m.emissiveIntensity = 0.25 + k * 1.6;
    POOL_MAT.opacity = Math.max(0, k - 0.15) * 0.55;
}

// ---------------------------------------------------------------- cars

const CAR_COLORS = ['#D7263D', '#3E7CB4', '#F6C945', '#2F9E8F', '#FFFFFF', '#2C2C30', '#E07B39', '#9ED2C0', '#B43E5C', '#8E8E8E', '#6B4FA0', '#7A8B3A'];

/** A little cartoon car facing +z (about 2 long, 1 wide). kind: 0 hatchback, 1 sedan, 2 van, 3 pickup, 4 luxury. */
export function carModel(seed = 0, kind = null) {
    const rnd = (n) => Math.abs(Math.sin(seed * 12.9898 + n * 78.233) * 43758.5453) % 1;
    const k = kind ?? Math.floor(rnd(1) * 5);
    const color = k === 4 ? ['#1B1B1B', '#2F4B7C', '#E9E4DA', '#5A1E2A'][Math.floor(rnd(2) * 4)] : CAR_COLORS[Math.floor(rnd(2) * CAR_COLORS.length)];
    const shiny = {flat: false, roughness: 0.3, metalness: 0.25};
    const g = new THREE.Group();
    const len = [1.7, 2.05, 2.1, 2.1, 2.3][k], w = k === 2 ? 1.05 : 0.98;
    g.add(mesh(new THREE.BoxGeometry(w, 0.38, len), mat(color, shiny), 0, 0.36, 0));
    // The cabin with its windows
    const cabLen = [0.95, 1.0, 1.55, 0.8, 1.15][k], cabZ = [-0.1, -0.1, -0.2, 0.25, -0.15][k], cabH = k === 2 ? 0.62 : 0.42;
    g.add(mesh(new THREE.BoxGeometry(w * 0.9, cabH, cabLen), mat(color, shiny), 0, 0.55 + cabH / 2, cabZ));
    const glass = mat('#9CC7DA', {flat: false, roughness: 0.1, metalness: 0.3});
    g.add(mesh(new THREE.BoxGeometry(w * 0.84, cabH * 0.7, cabLen + 0.02), glass, 0, 0.58 + cabH / 2, cabZ));
    g.add(mesh(new THREE.BoxGeometry(w * 0.92, cabH * 0.68, cabLen * 0.86), glass, 0, 0.58 + cabH / 2, cabZ));
    g.add(mesh(new THREE.BoxGeometry(w * 0.9, 0.04, cabLen), mat(color, shiny), 0, 0.56 + cabH, cabZ)); // roof
    if (k === 3) g.add(mesh(new THREE.BoxGeometry(w * 0.9, 0.18, 0.75), mat('#3A3F45'), 0, 0.6, -0.6)); // the open back of a pickup
    if (k === 4) g.add(mesh(new THREE.BoxGeometry(0.06, 0.06, 0.06), mat('#E2B23A', {metalness: 0.8, roughness: 0.2}), 0, 0.58, len / 2 - 0.02));
    // Wheels, lights, bumpers
    for (const sx of [-1, 1]) for (const sz of [-1, 1]) {
        const wh = mesh(new THREE.CylinderGeometry(0.17, 0.17, 0.14, 14), mat('#1E1E22'), sx * (w / 2 - 0.02), 0.17, sz * (len / 2 - 0.36));
        wh.rotation.z = Math.PI / 2;
        const hub = mesh(new THREE.CylinderGeometry(0.08, 0.08, 0.15, 10), mat('#C9CED4', {metalness: 0.6}), sx * (w / 2 - 0.01), 0.17, sz * (len / 2 - 0.36));
        hub.rotation.z = Math.PI / 2;
        g.add(wh, hub);
        g.add(mesh(new THREE.BoxGeometry(0.2, 0.1, 0.04), sz > 0 ? glowMat('#FFF4C2') : glowMat('#E63946'), sx * (w / 2 - 0.16), 0.44, sz * (len / 2 + 0.01)));
    }
    for (const sz of [-1, 1]) g.add(mesh(new THREE.BoxGeometry(w + 0.02, 0.1, 0.08), mat('#3A3F45'), 0, 0.2, sz * len / 2));
    g.userData.kind = k;
    return g;
}

const glow = (geo, color, x, y, z) => mesh(geo, glowMat(color), x, y, z);

// ---------------------------------------------------------------- more furniture

Object.assign(FURNITURE, {
    table_picnic: () => group(box(1.8, 0.06, 0.7, '#C98F5A', 0, 0.7, 0), ...[-0.7, 0.7].map(x => box(0.08, 0.7, 0.6, PALETTE.woodDark, x, 0, 0)),
        box(1.6, 0.012, 0.7, '#F25F5C', 0, 0.76, 0, {opacity: 0.0}), ...[-0.6, -0.2, 0.2, 0.6].map(x => box(0.18, 0.012, 0.7, '#F25F5C', x, 0.765, 0))),
    table_bar: () => group(cyl(0.34, 0.34, 0.05, '#2C2522', 0, 1.0, 0, 18, {roughness: 0.3}), cyl(0.04, 0.04, 1.0, PALETTE.steel, 0, 0, 0, 8, {metalness: 0.6}),
        cyl(0.22, 0.24, 0.03, PALETTE.steel, 0, 0, 0, 14, {metalness: 0.6})),
    table_glass: () => group(box(0.84, 0.03, 0.84, '#CFEAF2', 0, 0.7, 0, {opacity: 0.55, roughness: 0.05}), legs(0.7, 0.7, 0.7, PALETTE.gold, 0.02),
        box(0.7, 0.02, 0.7, PALETTE.gold, 0, 0.3, 0, {metalness: 0.6, roughness: 0.3, opacity: 0.0}), cyl(0.05, 0.05, 0.14, '#FFFFFF', 0, 0.72, 0, 10), ball(0.05, '#F7A1C4', 0, 0.9, 0)),
    chair_rattan: () => group(cyl(0.22, 0.2, 0.08, '#D9B47A', 0, 0.4, 0.02, 16), legs(0.3, 0.3, 0.4, '#B98A50', 0.022),
        mesh(new THREE.TorusGeometry(0.22, 0.04, 6, 16, Math.PI), mat('#D9B47A'), 0, 0.6, -0.12), box(0.36, 0.05, 0.36, '#FFF3E0', 0, 0.48, 0.02, {flat: false})),
    bench_wood: () => group(box(0.9, 0.06, 0.42, PALETTE.wood, 0, 0.42, 0.02), box(0.9, 0.4, 0.06, PALETTE.wood, 0, 0.5, -0.2),
        ...[-0.4, 0.4].map(x => box(0.06, 0.42, 0.4, PALETTE.woodDark, x, 0, 0))),
    chair_modern: () => group(box(0.42, 0.05, 0.42, '#2F9E8F', 0, 0.44, 0.02, {flat: false}), box(0.42, 0.4, 0.05, '#2F9E8F', 0, 0.5, -0.2, {flat: false}),
        ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => cyl(0.015, 0.015, 0.44, '#222', a * 0.17, 0, b * 0.17, 6))),
    chair_armchair: () => group(box(0.56, 0.22, 0.52, '#3E7CB4', 0, 0.18, 0.01, {flat: false}), box(0.56, 0.55, 0.14, '#3E7CB4', 0, 0.36, -0.19, {flat: false}),
        box(0.1, 0.26, 0.48, '#3E7CB4', -0.26, 0.36, 0, {flat: false}), box(0.1, 0.26, 0.48, '#3E7CB4', 0.26, 0.36, 0, {flat: false}),
        box(0.4, 0.06, 0.38, '#FFF3E0', 0, 0.4, 0.04, {flat: false}), legs(0.46, 0.42, 0.18, PALETTE.woodDark, 0.025)),

    stove_grill: () => group(box(0.9, 0.8, 0.8, '#2C2C30', 0, 0, -0.02, {metalness: 0.5, roughness: 0.4}),
        ...[-0.3, -0.15, 0, 0.15, 0.3].map(x => box(0.04, 0.02, 0.6, '#555', x, 0.82, -0.05)), glow(new THREE.BoxGeometry(0.7, 0.02, 0.55), '#FF6A2A', 0, 0.8, -0.05),
        box(0.92, 0.6, 0.25, '#B8BEC6', 0, 1.6, -0.3, {metalness: 0.6, roughness: 0.35})),
    fridge: () => group(box(0.8, 1.8, 0.7, '#F4F6F7', 0, 0, -0.1, {roughness: 0.35}), box(0.78, 0.01, 0.02, '#C8CDD2', 0, 1.15, 0.26),
        box(0.04, 0.4, 0.04, PALETTE.steel, 0.3, 1.3, 0.27, {metalness: 0.7}), box(0.04, 0.3, 0.04, PALETTE.steel, 0.3, 0.75, 0.27, {metalness: 0.7}),
        box(0.12, 0.12, 0.01, '#F25F5C', -0.2, 1.4, 0.26), box(0.1, 0.1, 0.01, '#FFD23F', -0.05, 1.5, 0.26)),
    sink: () => group(box(0.92, 0.9, 0.7, '#E8D3B0', 0, 0, -0.1), box(0.96, 0.05, 0.8, '#F2EEE6', 0, 0.9, -0.06), box(0.6, 0.04, 0.45, '#9FB4BF', 0, 0.92, -0.06, {metalness: 0.5, roughness: 0.2}),
        cyl(0.02, 0.02, 0.3, PALETTE.steel, 0, 0.95, -0.32, 8, {metalness: 0.7}), box(0.04, 0.03, 0.18, PALETTE.steel, 0, 1.24, -0.25, {metalness: 0.7})),
    cash_register: () => group(box(0.8, 0.9, 0.6, PALETTE.woodDark, 0, 0, -0.1), box(0.4, 0.2, 0.35, '#3B3B40', 0, 0.9, -0.1), box(0.3, 0.12, 0.02, '#9FE0B0', 0, 1.15, -0.03, {emissive: '#5CCB7A', glow: 0.4}),
        box(0.34, 0.06, 0.2, '#C8CDD2', 0, 1.1, 0.0)),
    trash_bin: () => group(cyl(0.18, 0.15, 0.5, '#7A8B3A', 0, 0, 0, 12), cyl(0.2, 0.2, 0.05, '#5D6B2C', 0, 0.5, 0, 12), box(0.12, 0.03, 0.02, '#FFF', 0, 0.3, 0.17)),
    menu_board: () => {
        const c = document.createElement('canvas');
        c.width = 128; c.height = 160;
        const g = c.getContext('2d');
        g.fillStyle = '#26302B'; g.fillRect(0, 0, 128, 160);
        g.fillStyle = '#F4EFE2'; g.font = 'bold 18px sans-serif'; g.textAlign = 'center'; g.fillText(document.documentElement.lang === 'it' ? 'MENÙ' : 'MENU', 64, 26);
        g.font = '12px sans-serif';
        ['bruschetta', 'tomato_soup', 'pizza', 'pasta_tomato', 'carrot_cake'].forEach((k, i) => g.fillText(nameOf(k).slice(0, 16), 64, 52 + i * 21)); // in the player's language
        const t = new THREE.CanvasTexture(c);
        t.colorSpace = THREE.SRGBColorSpace;
        const board = new THREE.Mesh(new THREE.PlaneGeometry(0.5, 0.62), new THREE.MeshStandardMaterial({map: t, roughness: 0.9}));
        board.position.set(0, 0.85, 0.04);
        board.rotation.x = -0.18;
        return group(box(0.58, 0.7, 0.04, PALETTE.wood, 0, 0.5, 0, {}).rotateX(-0.18), board, box(0.04, 0.6, 0.04, PALETTE.woodDark, -0.24, 0, -0.18).rotateX(0.3), box(0.04, 0.6, 0.04, PALETTE.woodDark, 0.24, 0, -0.18).rotateX(0.3));
    },

    coffee_machine: () => group(box(0.85, 0.9, 0.6, PALETTE.wood, 0, 0, -0.15), box(0.5, 0.45, 0.4, '#B43E5C', 0, 0.9, -0.15, {metalness: 0.3, roughness: 0.3}),
        box(0.3, 0.06, 0.1, PALETTE.steel, 0, 1.05, 0.08, {metalness: 0.7}), cyl(0.05, 0.04, 0.08, '#FFFFFF', -0.1, 0.92, 0.1, 10), cyl(0.05, 0.04, 0.08, '#FFFFFF', 0.1, 0.92, 0.1, 10),
        cyl(0.1, 0.1, 0.06, PALETTE.gold, 0, 1.38, -0.15, 12, {metalness: 0.6})),
    sofa: () => group(box(1.8, 0.24, 0.8, '#7A8B3A', 0, 0.16, 0.0, {flat: false}), box(1.8, 0.55, 0.2, '#7A8B3A', 0, 0.3, -0.32, {flat: false}),
        box(0.18, 0.32, 0.8, '#6C7B31', -0.86, 0.3, 0, {flat: false}), box(0.18, 0.32, 0.8, '#6C7B31', 0.86, 0.3, 0, {flat: false}),
        box(0.74, 0.1, 0.58, '#A4B85A', -0.38, 0.4, 0.06, {flat: false}), box(0.74, 0.1, 0.58, '#A4B85A', 0.38, 0.4, 0.06, {flat: false}),
        box(0.3, 0.26, 0.1, '#F7A1C4', -0.55, 0.6, -0.18, {flat: false}), legs(1.6, 0.6, 0.16, PALETTE.woodDark, 0.03)),
    tv: () => group(box(0.9, 0.5, 0.45, PALETTE.woodDark, 0, 0, -0.2), box(0.82, 0.5, 0.05, '#111', 0, 0.55, -0.2, {roughness: 0.2}),
        glow(new THREE.PlaneGeometry(0.74, 0.42), '#6FC3E8', 0, 0.8, -0.17)),
    arcade: () => group(box(0.7, 1.7, 0.7, '#6B4FA0', 0, 0, -0.1), glow(new THREE.PlaneGeometry(0.5, 0.4), '#FF5DA2', 0, 1.25, 0.26),
        box(0.66, 0.14, 0.3, '#2C2C30', 0, 0.95, 0.2), ball(0.04, '#F25F5C', -0.1, 1.05, 0.25), ball(0.03, '#FFD23F', 0.08, 1.04, 0.26), ball(0.03, '#5CB8D6', 0.16, 1.04, 0.26),
        glow(new THREE.BoxGeometry(0.66, 0.12, 0.02), '#FFD23F', 0, 1.6, 0.26)),
    gramophone: () => group(box(0.5, 0.7, 0.5, PALETTE.woodDark, 0, 0, 0), box(0.44, 0.1, 0.44, PALETTE.wood, 0, 0.7, 0), cyl(0.18, 0.18, 0.01, '#111', 0, 0.8, 0, 18),
        mesh(new THREE.ConeGeometry(0.22, 0.45, 14, 1, true), mat(PALETTE.gold, {metalness: 0.6, roughness: 0.3}), 0.08, 1.15, -0.05).rotateX(-0.6)),
    fireplace: () => group(box(1.8, 1.3, 0.5, '#B5532E', 0, 0, -0.25), box(1.1, 0.75, 0.1, '#2A1A14', 0, 0.12, 0.0), box(1.95, 0.1, 0.6, '#E6E0D6', 0, 1.3, -0.25),
        glow(new THREE.ConeGeometry(0.16, 0.4, 6), '#FF8A2A', -0.12, 0.33, 0.0), glow(new THREE.ConeGeometry(0.12, 0.3, 6), '#FFC24A', 0.12, 0.28, 0.0),
        box(0.7, 0.08, 0.12, PALETTE.woodDark, 0, 0.12, 0.02), cyl(0.06, 0.06, 0.2, PALETTE.gold, -0.6, 1.35, -0.25, 8), ball(0.08, '#7A8B3A', 0.6, 1.45, -0.25)),
    vending: () => group(box(0.8, 1.8, 0.7, '#D7263D', 0, 0, -0.1), glow(new THREE.PlaneGeometry(0.5, 1.0), '#CFEFFF', -0.08, 1.05, 0.26),
        ...[0, 1, 2, 3].map(i => box(0.4, 0.03, 0.1, ['#FFD23F', '#5CB8D6', '#7A8B3A', '#F7A1C4'][i], -0.08, 0.7 + i * 0.22, 0.22)), box(0.14, 0.4, 0.02, '#2C2C30', 0.28, 1.0, 0.26)),

    cactus: () => group(cyl(0.16, 0.13, 0.25, PALETTE.terracotta, 0, 0, 0, 10), cyl(0.07, 0.08, 0.5, '#4E8A3A', 0, 0.25, 0, 8), cyl(0.04, 0.04, 0.2, '#4E8A3A', 0.1, 0.45, 0, 6), ball(0.04, '#F7A1C4', 0, 0.77, 0)),
    sunflowers: () => group(cyl(0.13, 0.1, 0.35, '#3E7CB4', 0, 0, 0, 12), ...[0, 1, 2].map(i => {
        const g = group(cyl(0.012, 0.012, 0.5, PALETTE.leafDark, 0, 0, 0, 4), cyl(0.09, 0.09, 0.02, '#FFC21A', 0, 0.5, 0, 12), cyl(0.04, 0.04, 0.03, '#6B3B22', 0, 0.51, 0, 10));
        g.position.set((i - 1) * 0.08, 0.3, (i % 2) * 0.05);
        g.rotation.z = (i - 1) * 0.3;
        g.children[1].rotation.x = g.children[2].rotation.x = 1.1;
        return g;
    })),
    coat_rack: () => group(cyl(0.18, 0.2, 0.04, PALETTE.woodDark, 0, 0, 0, 12), cyl(0.025, 0.025, 1.7, PALETTE.woodDark, 0, 0, 0, 8), box(0.3, 0.4, 0.1, '#B43E5C', 0.12, 1.15, 0),
        ball(0.12, '#3E7CB4', -0.08, 1.7, 0)),
    umbrella_stand: () => group(cyl(0.12, 0.12, 0.45, PALETTE.steel, 0, 0, 0, 12, {metalness: 0.5}), cyl(0.012, 0.012, 0.6, '#222', 0.03, 0.3, 0, 4),
        mesh(new THREE.ConeGeometry(0.06, 0.4, 8), mat('#F25F5C'), 0.03, 0.75, 0), cyl(0.012, 0.012, 0.6, '#222', -0.04, 0.3, 0.02, 4), mesh(new THREE.ConeGeometry(0.06, 0.4, 8), mat('#FFD23F'), -0.04, 0.72, 0.02)),
    candles: () => group(cyl(0.2, 0.22, 0.05, PALETTE.gold, 0, 0, 0, 14, {metalness: 0.6}), cyl(0.03, 0.03, 1.0, PALETTE.gold, 0, 0, 0, 8, {metalness: 0.6}),
        ...[-0.15, 0, 0.15].map(x => group(cyl(0.03, 0.03, 0.18, '#FFF8E7', x, 1.0, 0, 8), glow(new THREE.ConeGeometry(0.025, 0.07, 6), '#FFB23A', x, 1.22, 0))),
        box(0.36, 0.03, 0.03, PALETTE.gold, 0, 1.0, 0, {metalness: 0.6})),
    globe: () => group(cyl(0.16, 0.2, 0.05, PALETTE.woodDark, 0, 0, 0, 12), cyl(0.02, 0.02, 0.7, PALETTE.woodDark, 0, 0, 0, 6), ball(0.24, '#5CB8D6', 0, 0.95, 0, {flat: false}, 16),
        ball(0.1, '#7A8B3A', 0.12, 1.02, 0.13, {}, 6), ball(0.08, '#7A8B3A', -0.14, 0.9, 0.12, {}, 6), mesh(new THREE.TorusGeometry(0.27, 0.012, 6, 24), mat(PALETTE.gold, {metalness: 0.6}), 0, 0.95, 0)),
    lantern: () => group(cyl(0.12, 0.14, 0.04, '#2F3A3A', 0, 0, 0, 8), cyl(0.02, 0.02, 0.9, '#2F3A3A', 0, 0, 0, 6), box(0.22, 0.28, 0.22, '#2F3A3A', 0, 0.9, 0, {opacity: 0.5}),
        glow(new THREE.BoxGeometry(0.16, 0.22, 0.16), '#FFC861', 0, 0.93, 0), mesh(new THREE.ConeGeometry(0.17, 0.12, 4), mat('#2F3A3A'), 0, 1.24, 0).rotateY(Math.PI / 4)),
    palm: () => group(cyl(0.2, 0.16, 0.36, '#E6E0D6', 0, 0, 0, 12), cyl(0.05, 0.07, 1.1, '#9C7454', 0, 0.36, 0, 7),
        ...[0, 1, 2, 3, 4, 5].map(i => {
            const leaf = mesh(new THREE.BoxGeometry(0.12, 0.02, 0.7), mat(i % 2 ? PALETTE.leaf : PALETTE.leafDark), 0, 0, 0);
            leaf.position.set(Math.cos(i * 1.05) * 0.3, 1.45, Math.sin(i * 1.05) * 0.3);
            leaf.rotation.set(0.5, -i * 1.05 + Math.PI / 2, 0);
            return leaf;
        })),
    easel: () => group(box(0.04, 1.3, 0.04, PALETTE.wood, -0.22, 0, 0.05).rotateZ(0.12), box(0.04, 1.3, 0.04, PALETTE.wood, 0.22, 0, 0.05).rotateZ(-0.12), box(0.04, 1.2, 0.04, PALETTE.wood, 0, 0, -0.25).rotateX(-0.3),
        box(0.6, 0.45, 0.03, '#FFFFFF', 0, 0.75, 0.1).rotateX(-0.1), box(0.2, 0.12, 0.01, '#F25F5C', -0.1, 0.95, 0.13), box(0.15, 0.1, 0.01, '#5CB8D6', 0.12, 0.85, 0.13)),
    bonsai: () => group(box(0.42, 0.14, 0.3, '#3B5A6B', 0, 0, 0), cyl(0.03, 0.05, 0.35, '#7B4E2C', 0, 0.14, 0, 6).rotateZ(0.2),
        ball(0.17, PALETTE.leafDark, -0.05, 0.55, 0, {}, 7), ball(0.12, PALETTE.leaf, 0.14, 0.5, 0.04, {}, 7), ball(0.1, PALETTE.leaf, -0.18, 0.45, -0.03, {}, 7)),
    neon_sign: () => group(box(0.7, 0.5, 0.08, '#1E1E24', 0, 0.9, -0.3), box(0.04, 0.9, 0.04, '#1E1E24', 0, 0, -0.3),
        glow(new THREE.TorusGeometry(0.15, 0.025, 6, 20), '#FF5DA2', -0.12, 0.95, -0.24), glow(new THREE.BoxGeometry(0.03, 0.3, 0.03), '#5CF2E0', 0.15, 0.95, -0.24),
        glow(new THREE.BoxGeometry(0.16, 0.03, 0.03), '#5CF2E0', 0.15, 1.08, -0.24)),
    trophy_case: () => group(box(0.9, 1.7, 0.45, PALETTE.woodDark, 0, 0, -0.25), box(0.84, 1.5, 0.02, '#CFEAF2', 0, 0.1, -0.02, {opacity: 0.35, roughness: 0.05}),
        ...[0.3, 0.75, 1.2].map(y => box(0.8, 0.03, 0.38, PALETTE.wood, 0, y, -0.25)), ...[[-0.2, 0.33], [0.15, 0.33], [0, 0.78], [-0.2, 1.23], [0.2, 1.23]].map(([x, y]) =>
            group(cyl(0.05, 0.07, 0.04, PALETTE.gold, x, y, -0.25, 8, {metalness: 0.7}), cyl(0.07, 0.03, 0.14, PALETTE.gold, x, y + 0.04, -0.25, 10, {metalness: 0.7, roughness: 0.25})))),
    flower_arch: () => group(...[-0.85, 0.85].map(x => box(0.1, 1.9, 0.1, '#FFFFFF', x, 0, 0)), mesh(new THREE.TorusGeometry(0.85, 0.05, 6, 20, Math.PI), mat('#FFFFFF'), 0, 1.9, 0),
        ...Array.from({length: 22}, (_, i) => {
            const a = Math.PI * i / 21;
            return ball(0.09, ['#F7A1C4', '#FFFFFF', '#F25F5C', PALETTE.leaf][i % 4], Math.cos(a) * 0.85, 1.9 + Math.sin(a) * 0.85, 0.04, {}, 6);
        }), ...Array.from({length: 10}, (_, i) => ball(0.08, i % 2 ? PALETTE.leaf : '#F7A1C4', i % 2 ? 0.85 : -0.85, 0.2 + i * 0.17, 0.05, {}, 6))),

    rug_round: () => group(mesh(new THREE.CylinderGeometry(0.95, 0.95, 0.037, 28), mat('#E9C46A', {flat: false}), 0, 0.034, 0), mesh(new THREE.CylinderGeometry(0.7, 0.7, 0.039, 28), mat('#F4A261', {flat: false}), 0, 0.035, 0),
        mesh(new THREE.CylinderGeometry(0.35, 0.35, 0.041, 24), mat('#E76F51', {flat: false}), 0, 0.036, 0)),
    rug_stripes: () => group(...[0, 1, 2, 3, 4].map(i => box(1.9, 0.037, 0.18, ['#3E7CB4', '#FFF3E0', '#7A8B3A', '#FFF3E0', '#3E7CB4'][i], 0, 0.027, -0.36 + i * 0.18))),
    rug_red: () => group(box(1.9, 0.037, 1.9, '#B43E5C', 0, 0.027, 0), box(1.6, 0.039, 1.6, '#D46A82', 0, 0.028, 0), box(0.9, 0.041, 0.9, '#B43E5C', 0, 0.029, 0)),
    rug_checker: () => group(...Array.from({length: 16}, (_, i) => box(0.47, 0.037, 0.47, (Math.floor(i / 4) + i) % 2 ? '#2F2A28' : '#F4EEE4', -0.71 + (i % 4) * 0.475, 0.027, -0.71 + Math.floor(i / 4) * 0.475))),
    rug_persian: () => group(box(2.9, 0.037, 1.9, '#7B2D26', 0, 0.027, 0), box(2.6, 0.039, 1.6, '#1F3A5F', 0, 0.028, 0), box(2.3, 0.041, 1.3, '#7B2D26', 0, 0.029, 0),
        mesh(new THREE.CylinderGeometry(0.45, 0.45, 0.021, 6), mat('#E2B23A'), 0, 0.030, 0), ...[-1, 1].map(x => mesh(new THREE.CylinderGeometry(0.2, 0.2, 0.021, 6), mat('#E2B23A'), x * 0.85, 0.030, 0))),
});


// ---------------------------------------------------------------- equipment tiers (budget → luxury) and alternative looks

Object.assign(FURNITURE, {
    stove_retro: () => group(box(0.88, 0.82, 0.8, '#9ED2C0', 0, 0, -0.02, {flat: false}), box(0.9, 0.05, 0.82, '#F4F6F7', 0, 0.82, -0.02),
        cyl(0.12, 0.12, 0.025, '#2A2A2A', -0.2, 0.87, -0.05, 16), cyl(0.12, 0.12, 0.025, '#2A2A2A', 0.2, 0.87, -0.05, 16),
        box(0.56, 0.36, 0.02, '#FFFFFF', 0, 0.22, 0.39, {opacity: 0.5}), box(0.6, 0.06, 0.03, PALETTE.steel, 0, 0.62, 0.4, {metalness: 0.7}),
        ...[-0.3, -0.1, 0.1, 0.3].map(x => cyl(0.035, 0.035, 0.03, '#FFFFFF', x, 0.71, 0.39, 10)), legs(0.76, 0.66, 0.06, PALETTE.steel, 0.03)),
    stove_pro: () => group(box(0.92, 0.86, 0.84, '#1E1E22', 0, 0, -0.02, {roughness: 0.3}), box(0.94, 0.05, 0.86, '#2E2E33', 0, 0.86, -0.02),
        ...[[-0.25, -0.22], [0, -0.22], [0.25, -0.22], [-0.25, 0.12], [0, 0.12], [0.25, 0.12]].map(([x, z]) => cyl(0.09, 0.09, 0.025, '#111', x, 0.91, z, 16)),
        box(0.92, 0.04, 0.04, PALETTE.gold, 0, 0.66, 0.41, {metalness: 0.8, roughness: 0.25}), box(0.7, 0.4, 0.02, '#333', 0, 0.18, 0.41, {roughness: 0.2}),
        ...[-0.32, -0.16, 0, 0.16, 0.32].map(x => cyl(0.03, 0.03, 0.03, PALETTE.gold, x, 0.74, 0.41, 10, {metalness: 0.8})),
        box(0.94, 0.5, 0.3, '#1E1E22', 0, 1.75, -0.28, {roughness: 0.3}), box(0.94, 0.03, 0.31, PALETTE.gold, 0, 1.75, -0.28, {metalness: 0.8})),
    stove_gold: () => group(box(0.92, 0.86, 0.84, '#F4EFE6', 0, 0, -0.02, {roughness: 0.25}), box(0.94, 0.06, 0.86, '#1B1B1B', 0, 0.86, -0.02, {roughness: 0.2}),
        ...[[-0.22, -0.2], [0.22, -0.2], [-0.22, 0.14], [0.22, 0.14]].map(([x, z]) => cyl(0.1, 0.1, 0.03, PALETTE.gold, x, 0.92, z, 18, {metalness: 0.85, roughness: 0.2})),
        box(0.8, 0.46, 0.02, '#2A2A2A', 0, 0.14, 0.41, {roughness: 0.15}), box(0.84, 0.05, 0.05, PALETTE.gold, 0, 0.66, 0.42, {metalness: 0.85, roughness: 0.2}),
        ...[-0.3, -0.1, 0.1, 0.3].map(x => ball(0.04, PALETTE.gold, x, 0.76, 0.42, {metalness: 0.85, roughness: 0.2})), legs(0.8, 0.7, 0.05, PALETTE.gold, 0.035),
        mesh(new THREE.SphereGeometry(0.3, 16, 8, 0, Math.PI * 2, 0, Math.PI / 2), mat('#F4EFE6', {roughness: 0.25}), 0, 1.6, -0.25)),
    counter_marble: () => group(box(0.94, 0.92, 0.7, '#2C2522', 0, 0, -0.1), box(0.98, 0.07, 0.84, '#ECE8E2', 0, 0.92, -0.06, {roughness: 0.2}),
        box(0.98, 0.03, 0.03, PALETTE.gold, 0, 0.9, 0.36, {metalness: 0.7, roughness: 0.3})),
    counter_gold: () => group(box(0.94, 0.92, 0.7, PALETTE.berry, 0, 0, -0.1, {roughness: 0.4}), box(0.98, 0.07, 0.84, '#F7F2E8', 0, 0.92, -0.06, {roughness: 0.15}),
        ...[-0.3, 0, 0.3].map(x => box(0.04, 0.8, 0.03, PALETTE.gold, x, 0.06, 0.26, {metalness: 0.85, roughness: 0.2})), box(0.98, 0.04, 0.05, PALETTE.gold, 0, 0.88, 0.36, {metalness: 0.85})),
    fridge_retro: () => group(box(0.78, 1.7, 0.7, '#9ED2C0', 0, 0.05, -0.1, {flat: false}), mesh(new THREE.CylinderGeometry(0.39, 0.39, 0.7, 16, 1, false, -Math.PI / 2, Math.PI), mat('#9ED2C0', {flat: false}), 0, 1.75, -0.1).rotateX(Math.PI / 2).rotateZ(Math.PI / 2),
        box(0.78, 0.01, 0.02, '#7DB8A5', 0, 1.1, 0.26), box(0.06, 0.3, 0.05, PALETTE.steel, 0.3, 1.3, 0.27, {metalness: 0.8}), legs(0.6, 0.5, 0.05, PALETTE.steel, 0.03)),
    fridge_pro: () => group(box(0.9, 1.9, 0.72, PALETTE.steel, 0, 0, -0.1, {metalness: 0.65, roughness: 0.3}), box(0.01, 1.84, 0.02, '#8A9097', 0, 0.03, 0.27),
        box(0.04, 0.8, 0.04, '#E6E9EC', -0.06, 0.6, 0.28, {metalness: 0.8}), box(0.04, 0.8, 0.04, '#E6E9EC', 0.06, 0.6, 0.28, {metalness: 0.8}),
        glow(new THREE.PlaneGeometry(0.16, 0.08), '#5CF2E0', 0.25, 1.5, 0.27)),
    sink_steel: () => group(box(0.92, 0.9, 0.7, PALETTE.steel, 0, 0, -0.1, {metalness: 0.6, roughness: 0.3}), box(0.96, 0.05, 0.8, '#D8DDE2', 0, 0.9, -0.06, {metalness: 0.7, roughness: 0.2}),
        box(0.34, 0.04, 0.42, '#9FB4BF', -0.2, 0.92, -0.06, {metalness: 0.6, roughness: 0.15}), box(0.34, 0.04, 0.42, '#9FB4BF', 0.2, 0.92, -0.06, {metalness: 0.6, roughness: 0.15}),
        cyl(0.02, 0.02, 0.45, PALETTE.steel, 0, 0.95, -0.34, 8, {metalness: 0.8}), mesh(new THREE.TorusGeometry(0.12, 0.018, 6, 12, Math.PI), mat(PALETTE.steel, {metalness: 0.8}), 0, 1.4, -0.22).rotateY(Math.PI / 2)),
    coffee_luxe: () => group(box(0.85, 0.9, 0.6, '#2C2522', 0, 0, -0.15), box(0.62, 0.5, 0.42, '#C9CED4', 0, 0.9, -0.15, {metalness: 0.8, roughness: 0.2}),
        ...[-0.18, 0.18].map(x => box(0.12, 0.06, 0.12, '#222', x, 1.05, 0.1)), cyl(0.1, 0.1, 0.25, PALETTE.gold, 0, 1.4, -0.15, 14, {metalness: 0.85, roughness: 0.2}),
        ball(0.08, PALETTE.gold, 0, 1.68, -0.15, {metalness: 0.85}), cyl(0.05, 0.04, 0.08, '#FFFFFF', -0.18, 0.92, 0.1, 10), cyl(0.05, 0.04, 0.08, '#FFFFFF', 0.18, 0.92, 0.1, 10)),
    table_rustic: () => group(box(0.8, 0.08, 0.8, '#8A5A35', 0, 0.68, 0), ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => box(0.08, 0.68, 0.08, '#6B4426', a * 0.32, 0, b * 0.32)),
        box(0.64, 0.05, 0.05, '#6B4426', 0, 0.2, 0.32), box(0.64, 0.05, 0.05, '#6B4426', 0, 0.2, -0.32)),
    chair_folding: () => group(box(0.4, 0.03, 0.38, '#8E99A3', 0, 0.44, 0.02), box(0.4, 0.3, 0.03, '#8E99A3', 0, 0.55, -0.18),
        box(0.025, 0.85, 0.025, '#5B6670', -0.18, 0, 0).rotateX(0.18), box(0.025, 0.85, 0.025, '#5B6670', 0.18, 0, 0).rotateX(0.18),
        box(0.025, 0.5, 0.025, '#5B6670', -0.18, 0, 0.1).rotateX(-0.3), box(0.025, 0.5, 0.025, '#5B6670', 0.18, 0, 0.1).rotateX(-0.3)),
    chair_bistro: () => group(cyl(0.2, 0.2, 0.04, '#2C2C30', 0, 0.44, 0.02, 16), ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => cyl(0.014, 0.014, 0.44, '#2C2C30', a * 0.14, 0, b * 0.14, 6)),
        mesh(new THREE.TorusGeometry(0.17, 0.018, 6, 16, Math.PI), mat('#2C2C30'), 0, 0.68, -0.16), mesh(new THREE.TorusGeometry(0.1, 0.014, 6, 14, Math.PI), mat('#2C2C30'), 0, 0.62, -0.16),
        box(0.024, 0.26, 0.024, '#2C2C30', -0.17, 0.44, -0.16), box(0.024, 0.26, 0.024, '#2C2C30', 0.17, 0.44, -0.16)),
});

// ---------------------------------------------------------------- outdoors: terrace and yard

const treeTop = (colors, s = 1) => [[0, 1.6, 0, 0.75], [0.33, 1.35, 0.2, 0.5], [-0.3, 1.4, -0.2, 0.55], [0, 2.05, 0, 0.5]].map(([x, y, z, r], i) => {
    const m = mesh(new THREE.IcosahedronGeometry(r * s, 0), mat(colors[i % colors.length]), x * s, y * s, z * s);
    return m;
});

Object.assign(FURNITURE, {
    bench_park: () => group(box(1.7, 0.07, 0.42, '#9C6B3F', 0, 0.42, 0.02), ...[-0.12, 0.02].map(z => box(1.7, 0.05, 0.12, '#B07A4A', 0, 0.44, z * 2)),
        box(1.7, 0.12, 0.05, '#9C6B3F', 0, 0.62, -0.19), box(1.7, 0.12, 0.05, '#9C6B3F', 0, 0.8, -0.19),
        ...[-0.75, 0.75].map(x => group(box(0.06, 0.45, 0.4, '#2F3A3A', x, 0, 0), box(0.06, 0.5, 0.05, '#2F3A3A', x, 0.42, -0.19)))),
    tree_oak: () => group(cyl(0.11, 0.15, 1.2, PALETTE.woodDark, 0, 0, 0, 7), ...treeTop([PALETTE.leaf, PALETTE.leafDark])),
    tree_pine: () => group(cyl(0.08, 0.11, 0.5, PALETTE.woodDark, 0, 0, 0, 6), ...[0, 1, 2].map(i => mesh(new THREE.ConeGeometry(0.55 - i * 0.13, 0.75, 7), mat(i % 2 ? '#2E6B3A' : '#3F7A44'), 0, 0.85 + i * 0.5, 0))),
    tree_cherry: () => group(cyl(0.1, 0.14, 1.2, '#6B4426', 0, 0, 0, 7), ...treeTop(['#F7B6CF', '#F49AC1', '#FFFFFF'])),
    bush_round: () => group(ball(0.38, PALETTE.leaf, 0, 0.33, 0), ball(0.24, PALETTE.leafDark, 0.2, 0.42, 0.1), ball(0.2, '#F7A1C4', -0.12, 0.6, 0.18, {}, 6)),
    hedge: () => group(box(1.8, 0.7, 0.55, '#4E8A3A', 0, 0, 0, {flat: false}), box(1.84, 0.12, 0.58, '#5E9A3E', 0, 0.62, 0, {flat: false})),
    planter_flowers: () => group(box(0.8, 0.36, 0.6, PALETTE.terracotta, 0, 0, 0), box(0.76, 0.04, 0.56, '#6B4A33', 0, 0.36, 0),
        ...Array.from({length: 8}, (_, i) => ball(0.07, ['#F25F5C', '#FFD23F', '#F7A1C4', '#FFFFFF'][i % 4], -0.28 + (i % 4) * 0.19, 0.48, i < 4 ? -0.12 : 0.12, {}, 6))),
    street_lamp: () => group(cyl(0.12, 0.16, 0.08, '#2F3A3A', 0, 0, 0, 10), cyl(0.04, 0.05, 2.3, '#2F3A3A', 0, 0.08, 0, 8),
        mesh(new THREE.SphereGeometry(0.18, 12, 8), glowMat('#FFE7A8'), 0, 2.5, 0), mesh(new THREE.ConeGeometry(0.22, 0.14, 8), mat('#2F3A3A'), 0, 2.7, 0)),
    parasol: () => group(cyl(0.3, 0.3, 0.04, '#E6E0D6', 0, 0.7, 0, 16), legs(0.4, 0.4, 0.7, PALETTE.steel, 0.02), cyl(0.025, 0.025, 2.0, PALETTE.ink, 0, 0, 0, 6),
        ...Array.from({length: 8}, (_, i) => {
            const seg = mesh(new THREE.ConeGeometry(0.85, 0.35, 8, 1, true, i * Math.PI / 4, Math.PI / 4), mat(i % 2 ? PALETTE.cream : PALETTE.terracotta), 0, 2.05, 0);
            seg.material = seg.material.clone();
            seg.material.side = THREE.DoubleSide;
            return seg;
        })),
    menu_sign: () => {
        const g = group(box(0.5, 0.75, 0.04, '#26302B', 0, 0, 0.12).rotateX(-0.25), box(0.5, 0.75, 0.04, '#26302B', 0, 0, -0.12).rotateX(0.25),
            box(0.56, 0.05, 0.05, PALETTE.wood, 0, 0.78, 0));
        const c = document.createElement('canvas');
        c.width = 96; c.height = 128;
        const x = c.getContext('2d');
        x.fillStyle = '#26302B'; x.fillRect(0, 0, 96, 128);
        x.strokeStyle = '#F4EFE2'; x.lineWidth = 3;
        for (let i = 0; i < 5; i++) { x.beginPath(); x.moveTo(14, 30 + i * 20); x.lineTo(82, 30 + i * 20); x.stroke(); }
        x.fillStyle = '#F6C945'; x.beginPath(); x.arc(48, 14, 8, 0, Math.PI * 2); x.fill();
        const t = new THREE.CanvasTexture(c);
        t.colorSpace = THREE.SRGBColorSpace;
        const face = new THREE.Mesh(new THREE.PlaneGeometry(0.44, 0.66), new THREE.MeshStandardMaterial({map: t}));
        face.position.set(0, 0.36, 0.165);
        face.rotation.x = -0.25;
        g.add(face);
        return g;
    },
    bike_rack: () => group(box(0.9, 0.04, 0.3, '#5B6670', 0, 0, 0), ...[-0.3, 0, 0.3].map(x => mesh(new THREE.TorusGeometry(0.22, 0.025, 6, 16, Math.PI), mat('#5B6670', {metalness: 0.5}), x, 0.02, 0).rotateY(Math.PI / 2)),
        mesh(new THREE.TorusGeometry(0.2, 0.035, 6, 16), mat('#2C2C30'), 0.15, 0.25, 0.12), mesh(new THREE.TorusGeometry(0.2, 0.035, 6, 16), mat('#2C2C30'), -0.25, 0.25, 0.12),
        box(0.45, 0.04, 0.04, '#D7263D', -0.05, 0.42, 0.12)),
    gnome: () => group(cyl(0.12, 0.15, 0.3, '#3E7CB4', 0, 0, 0, 10), ball(0.11, PALETTE.skin[0], 0, 0.38, 0, {flat: false}, 10), mesh(new THREE.ConeGeometry(0.12, 0.3, 10), mat('#D7263D'), 0, 0.58, 0),
        mesh(new THREE.ConeGeometry(0.09, 0.16, 8), mat('#FFFFFF'), 0, 0.27, 0.08).rotateX(Math.PI)),
    garden_fountain: () => group(cyl(0.46, 0.5, 0.35, '#B8B0A2', 0, 0, 0, 18), cyl(0.4, 0.4, 0.02, PALETTE.water, 0, 0.34, 0, 18, {opacity: 0.75, roughness: 0.05}),
        cyl(0.07, 0.09, 0.6, '#B8B0A2', 0, 0.35, 0, 10), cyl(0.22, 0.1, 0.12, '#B8B0A2', 0, 0.95, 0, 14), ball(0.12, '#B8B0A2', 0, 1.15, 0, {flat: false}),
        ball(0.05, PALETTE.water, 0.12, 1.0, 0, {opacity: 0.8}), ball(0.05, PALETTE.water, -0.12, 1.0, 0, {opacity: 0.8})),
    food_truck: () => {
        const g = group(box(2.7, 1.3, 1.6, '#F6C945', 0, 0.4, 0, {flat: false}), box(1.0, 0.9, 1.6, '#F6C945', 1.25, 0.4, 0),
            box(1.6, 0.7, 0.02, '#2A2A2A', -0.25, 0.95, 0.81), box(1.8, 0.06, 0.4, PALETTE.steel, -0.25, 0.92, 0.98, {metalness: 0.6}),
            ...[-0.8, 1.0].flatMap(x => [-0.82, 0.82].map(z => cyl(0.26, 0.26, 0.2, '#1B1B1B', x, 0.26, z, 16).rotateX(Math.PI / 2))),
            box(2.0, 0.08, 0.7, '#D9653B', -0.25, 1.62, 1.15).rotateX(0.35), box(0.9, 0.5, 0.02, '#BFE3F0', 1.4, 1.0, 0.81, {opacity: 0.6}));
        // A little sign on top with the Forkville fork
        const f = forkMesh(PALETTE.terracotta, 0.6);
        f.position.set(0, 1.7, 0);
        g.add(f, glow(new THREE.BoxGeometry(1.6, 0.05, 0.03), '#FFE29A', -0.25, 1.32, 0.82));
        return g;
    },
});

// ---------------------------------------------------------------- fireplaces (flames flicker, see World.frame)

/** A small fire: three flame cones and a warm light; the group remembers its flames for the flicker. */
function fire(x, y, z, w = 1) {
    const g = new THREE.Group();
    g.position.set(x, y, z);
    const flames = [];
    [[-0.1 * w, 0.4, '#FF7A1A'], [0.1 * w, 0.32, '#FF9A3C'], [0, 0.26, '#FFD23F']].forEach(([fx, h, c]) => {
        const f = new THREE.Mesh(new THREE.ConeGeometry(0.09 * (0.8 + w * 0.3), h, 7), glowMat(c));
        f.position.set(fx, h / 2, 0);
        g.add(f);
        flames.push(f);
    });
    const logs = [-0.12, 0.12].map(lx => {
        const l = new THREE.Mesh(new THREE.CylinderGeometry(0.04, 0.04, 0.32 * w, 6), mat('#5A3218'));
        l.rotation.z = Math.PI / 2;
        l.rotation.y = lx * 3;
        l.position.y = 0.03;
        return l;
    });
    g.add(...logs);
    const light = new THREE.PointLight('#FF9A4A', 0.8, 4, 1.6);
    light.position.y = 0.35;
    g.add(light);
    g.userData.flames = flames;
    return g;
}

Object.assign(FURNITURE, {
    woodstove: () => {
        const g = group(cyl(0.32, 0.34, 0.75, '#2C2C30', 0, 0.12, -0.05, 16, {metalness: 0.5, roughness: 0.5}), legs(0.38, 0.38, 0.12, '#2C2C30', 0.03),
            mesh(new THREE.SphereGeometry(0.32, 16, 8, 0, Math.PI * 2, 0, Math.PI / 2), mat('#2C2C30', {metalness: 0.5, roughness: 0.5}), 0, 0.87, -0.05),
            cyl(0.07, 0.07, 1.2, '#2C2C30', 0, 1.1, -0.05, 10, {metalness: 0.5}), box(0.34, 0.3, 0.03, '#FFB25A', 0, 0.3, 0.27, {opacity: 0.6, emissive: '#FF8A2A', glow: 0.6}),
            box(0.4, 0.04, 0.05, PALETTE.gold, 0, 0.64, 0.27, {metalness: 0.7}));
        const f = fire(0, 0.22, 0.12, 0.7);
        g.add(f);
        g.userData.flames = f.userData.flames;
        return g;
    },
    fireplace_stone: () => {
        const stones = [];
        for (let r = 0; r < 6; r++) for (let c = 0; c < 5; c++) {
            if (r < 3 && c > 0 && c < 4) continue; // the opening
            stones.push(box(0.34, 0.2, 0.42, ['#9C9184', '#B2A796', '#8A7F72', '#A89C8B'][(r * 3 + c) % 4], -0.72 + c * 0.36, r * 0.21, -0.28));
        }
        const g = group(...stones, box(1.9, 0.12, 0.6, '#7B4E2C', 0, 1.26, -0.25), box(1.0, 0.62, 0.1, '#2A1A14', 0, 0.02, -0.08),
            box(0.3, 0.1, 0.1, '#5A3218', -0.6, 1.38, -0.25), ball(0.07, PALETTE.leaf, 0.6, 1.45, -0.25));
        const f = fire(0, 0.03, -0.02, 1);
        g.add(f);
        g.userData.flames = f.userData.flames;
        return g;
    },
    fireplace_modern: () => {
        const g = group(box(1.9, 1.0, 0.45, '#1E1E24', 0, 0, -0.27, {roughness: 0.3}), box(1.5, 0.42, 0.02, '#CFE8F2', 0, 0.3, -0.04, {opacity: 0.25, roughness: 0.05}),
            box(1.5, 0.05, 0.3, '#3A3A40', 0, 0.28, -0.2), box(1.95, 0.05, 0.5, '#B8BEC6', 0, 1.0, -0.27, {metalness: 0.7, roughness: 0.25}),
            ...[-0.5, 0, 0.5].map(x => mesh(new THREE.IcosahedronGeometry(0.06, 0), mat('#DCD7CD'), x, 0.33, -0.16)));
        // A long line of low blue-orange flames
        const flames = [];
        for (let i = 0; i < 7; i++) {
            const f = mesh(new THREE.ConeGeometry(0.07, 0.26 + (i % 3) * 0.05, 6), glowMat(i % 2 ? '#FF9A3C' : '#FFD23F'), -0.6 + i * 0.2, 0.44, -0.18);
            g.add(f);
            flames.push(f);
        }
        const light = new THREE.PointLight('#FF9A4A', 0.8, 4, 1.6);
        light.position.set(0, 0.6, 0.1);
        g.add(light);
        g.userData.flames = flames;
        return g;
    },
    fireplace_marble: () => {
        const g = group(box(1.9, 1.35, 0.5, '#F3F1EC', 0, 0, -0.25, {roughness: 0.2}), box(1.1, 0.78, 0.1, '#2A1A14', 0, 0.02, -0.04),
            box(2.05, 0.1, 0.62, '#ECE8E2', 0, 1.35, -0.25, {roughness: 0.15}), box(2.05, 0.03, 0.64, PALETTE.gold, 0, 1.33, -0.25, {metalness: 0.85, roughness: 0.2}),
            ...[-0.72, 0.72].map(x => cyl(0.09, 0.1, 1.3, '#ECE8E2', x, 0, 0.02, 14, {roughness: 0.2})),
            ...[-0.72, 0.72].map(x => box(0.26, 0.08, 0.26, PALETTE.gold, x, 1.27, 0.02, {metalness: 0.85})),
            mesh(new THREE.TorusGeometry(0.2, 0.03, 8, 20), mat(PALETTE.gold, {metalness: 0.85, roughness: 0.2}), 0, 1.75, -0.45),
            cyl(0.05, 0.07, 0.25, PALETTE.gold, -0.6, 1.4, -0.25, 10, {metalness: 0.8}), ...[-0.62, -0.58].map(x => cyl(0.012, 0.012, 0.1, '#FFF8E7', x, 1.65, -0.25, 6)),
            box(1.15, 0.05, 0.02, PALETTE.gold, 0, 0.42, 0.02, {metalness: 0.85}), ...[-0.4, -0.2, 0, 0.2, 0.4].map(x => box(0.02, 0.42, 0.02, PALETTE.gold, x, 0.0, 0.02, {metalness: 0.85})));
        const f = fire(0, 0.03, -0.06, 1.1);
        g.add(f);
        g.userData.flames = f.userData.flames;
        return g;
    },
});

// The first fireplace gets real flames too
FURNITURE.fireplace = () => {
    const g = group(box(1.8, 1.3, 0.5, '#B5532E', 0, 0, -0.25), box(1.1, 0.75, 0.1, '#2A1A14', 0, 0.12, 0.0), box(1.95, 0.1, 0.6, '#E6E0D6', 0, 1.3, -0.25),
        cyl(0.06, 0.06, 0.2, PALETTE.gold, -0.6, 1.35, -0.25, 8), ball(0.08, '#7A8B3A', 0.6, 1.45, -0.25));
    const f = fire(0, 0.12, 0.02, 1);
    g.add(f);
    g.userData.flames = f.userData.flames;
    return g;
};

// ---------------------------------------------------------------- the cleaners' corner

Object.assign(FURNITURE, {
    cleaning_cart: () => group(
        box(0.7, 0.05, 0.45, '#F6C945', 0, 0.12, 0), box(0.7, 0.05, 0.45, '#F6C945', 0, 0.6, 0), ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => box(0.04, 0.6, 0.04, '#E0A526', a * 0.32, 0.12, b * 0.2)),
        ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => ball(0.05, '#2C2C30', a * 0.3, 0.05, b * 0.18, {}, 8)),
        cyl(0.13, 0.11, 0.24, '#3E7CB4', -0.15, 0.17, 0, 14), cyl(0.12, 0.12, 0.02, '#8CC8EC', -0.15, 0.4, 0, 14, {opacity: 0.7}),
        cyl(0.012, 0.012, 1.2, '#9C6B3F', 0.2, 0.17, -0.1, 6).rotateZ(-0.12), cyl(0.09, 0.06, 0.12, '#E9E4DA', 0.27, 0.1, -0.1, 8),
        ...[['#2F9E8F', -0.2], ['#F7A1C4', -0.05], ['#F6C945', 0.1]].map(([c, x]) => cyl(0.04, 0.045, 0.16, c, x, 0.65, 0.05, 8)),
        ball(0.15, '#2C2C30', 0.2, 0.78, 0.05, {}, 7)),
    cleaning_station: () => group(
        box(0.9, 1.7, 0.45, '#5D7A8C', 0, 0, -0.25), box(0.84, 1.6, 0.02, '#3F5866', 0, 0.05, -0.02),
        ...[0.35, 0.8, 1.25].map(y => box(0.8, 0.03, 0.38, '#E6E0D6', 0, y, -0.24)),
        ...[0, 1, 2, 3].map(i => cyl(0.045, 0.05, 0.2, ['#2F9E8F', '#F7A1C4', '#F6C945', '#8CC8EC'][i], -0.28 + i * 0.18, 0.83, -0.22, 8)),
        ...[0, 1, 2].map(i => box(0.18, 0.12, 0.25, ['#FFFFFF', '#F4EFE2', '#E9C46A'][i], -0.22 + i * 0.22, 1.28, -0.22)),
        cyl(0.16, 0.13, 0.28, '#3E7CB4', -0.2, 0.0, -0.18, 14), cyl(0.015, 0.015, 1.4, '#9C6B3F', 0.28, 0.05, 0.02, 6).rotateZ(-0.08),
        box(0.28, 0.06, 0.08, '#C0392B', 0.33, 0.05, 0.02), cyl(0.015, 0.015, 1.3, '#9C6B3F', 0.12, 0.05, 0.04, 6).rotateZ(0.06),
        mesh(new THREE.ConeGeometry(0.1, 0.25, 8), mat('#F6C945'), 0.12, 0.14, 0.04).rotateX(Math.PI),
        box(0.5, 0.08, 0.02, '#F6C945', 0, 1.55, -0.01)),
});

// ---------------------------------------------------------------- restroom

const porcelain = '#F7F8FA';
Object.assign(FURNITURE, {
    toilet: () => group(box(0.42, 0.42, 0.22, porcelain, 0, 0.42, -0.32, {flat: false}), box(0.44, 0.06, 0.24, '#E6E9EC', 0, 0.84, -0.32),
        cyl(0.17, 0.13, 0.38, porcelain, 0, 0, -0.02, 16, {flat: false}), cyl(0.2, 0.2, 0.04, porcelain, 0, 0.38, 0.02, 18, {flat: false}),
        cyl(0.2, 0.2, 0.03, '#E6E9EC', 0, 0.42, 0.02, 18), box(0.06, 0.03, 0.03, PALETTE.steel, 0.14, 0.76, -0.2, {metalness: 0.7})),
    toilet_luxe: () => group(box(0.46, 0.5, 0.2, '#2C2522', 0, 0.4, -0.33, {roughness: 0.3}), box(0.48, 0.04, 0.22, PALETTE.gold, 0, 0.9, -0.33, {metalness: 0.8}),
        mesh(new THREE.SphereGeometry(0.21, 18, 10, 0, Math.PI * 2, Math.PI / 2, Math.PI / 2), mat(porcelain, {flat: false}), 0, 0.42, 0.0),
        cyl(0.21, 0.21, 0.04, '#2C2522', 0, 0.42, 0.0, 18, {roughness: 0.3}), cyl(0.1, 0.14, 0.22, porcelain, 0, 0, 0, 14, {flat: false}),
        box(0.08, 0.05, 0.03, PALETTE.gold, 0.16, 0.8, -0.22, {metalness: 0.8})),
    washbasin: () => group(cyl(0.08, 0.11, 0.7, porcelain, 0, 0, -0.1, 12, {flat: false}), mesh(new THREE.SphereGeometry(0.24, 16, 8, 0, Math.PI * 2, Math.PI / 2, Math.PI / 2), mat(porcelain, {flat: false}), 0, 0.86, -0.1).rotateX(0),
        cyl(0.24, 0.24, 0.03, '#E6E9EC', 0, 0.84, -0.1, 18), cyl(0.015, 0.015, 0.2, PALETTE.steel, 0, 0.86, -0.3, 8, {metalness: 0.8}), box(0.03, 0.03, 0.12, PALETTE.steel, 0, 1.05, -0.25, {metalness: 0.8}),
        box(0.5, 0.6, 0.03, '#CFE8F2', 0, 1.15, -0.44, {roughness: 0.05, metalness: 0.3}), box(0.54, 0.64, 0.02, '#B8BEC6', 0, 1.13, -0.46)),
    washbasin_marble: () => group(box(0.9, 0.8, 0.5, '#2C2522', 0, 0, -0.22), box(0.94, 0.08, 0.54, '#F3F1EC', 0, 0.8, -0.22, {roughness: 0.15}),
        cyl(0.18, 0.15, 0.06, porcelain, 0, 0.86, -0.18, 18, {flat: false}), cyl(0.015, 0.015, 0.22, PALETTE.gold, 0, 0.88, -0.4, 8, {metalness: 0.85}), box(0.03, 0.03, 0.12, PALETTE.gold, 0, 1.08, -0.35, {metalness: 0.85}),
        box(0.7, 0.7, 0.03, '#CFE8F2', 0, 1.2, -0.47, {roughness: 0.05, metalness: 0.3}), box(0.76, 0.76, 0.02, PALETTE.gold, 0, 1.17, -0.49, {metalness: 0.8}),
        cyl(0.04, 0.04, 0.12, '#F7A1C4', 0.32, 0.88, -0.3, 8), ball(0.05, PALETTE.leaf, 0.32, 1.05, -0.3, {}, 6)),
    hand_dryer: () => group(box(0.06, 1.0, 0.06, '#B8BEC6', 0, 0, -0.4, {metalness: 0.6}), box(0.34, 0.28, 0.2, '#E6E9EC', 0, 1.0, -0.32, {flat: false}),
        box(0.24, 0.03, 0.12, '#5B6670', 0, 0.99, -0.24), glow(new THREE.BoxGeometry(0.04, 0.04, 0.01), '#5CF2E0', 0.11, 1.18, -0.21)),
    toilet_cubicle: () => group(box(0.06, 1.7, 0.96, '#8CC8EC', -0.45, 0.1, 0), box(0.06, 1.7, 0.96, '#8CC8EC', 0.45, 0.1, 0), box(0.96, 1.7, 0.06, '#8CC8EC', 0, 0.1, -0.45),
        box(0.84, 1.55, 0.04, '#6FB0D9', 0.06, 0.15, 0.45).rotateY(0.5), ...[-0.45, 0.45].map(x => box(0.07, 0.1, 0.07, PALETTE.steel, x, 0, 0.45, {metalness: 0.7})),
        box(0.08, 0.04, 0.02, PALETTE.steel, 0.3, 0.9, 0.6, {metalness: 0.8})),
    wc_sign: () => {
        const c = document.createElement('canvas');
        c.width = 128; c.height = 128;
        const g = c.getContext('2d');
        g.fillStyle = '#3E7CB4'; g.beginPath(); g.roundRect(4, 4, 120, 120, 20); g.fill();
        g.fillStyle = '#FFFFFF';
        g.beginPath(); g.arc(40, 34, 10, 0, Math.PI * 2); g.fill(); g.fillRect(31, 48, 18, 40); // man
        g.beginPath(); g.arc(88, 34, 10, 0, Math.PI * 2); g.fill(); g.beginPath(); g.moveTo(88, 46); g.lineTo(104, 88); g.lineTo(72, 88); g.fill(); // woman
        g.fillRect(62, 22, 4, 84);
        const t = new THREE.CanvasTexture(c);
        t.colorSpace = THREE.SRGBColorSpace;
        const plate = new THREE.Mesh(new THREE.PlaneGeometry(0.4, 0.4), new THREE.MeshStandardMaterial({map: t}));
        plate.position.set(0, 1.5, 0.03);
        return group(cyl(0.12, 0.15, 0.04, PALETTE.ink, 0, 0, 0, 10), cyl(0.025, 0.025, 1.3, PALETTE.ink, 0, 0, 0, 6), box(0.44, 0.44, 0.03, PALETTE.ink, 0, 1.28, 0.0), plate);
    },
});

// ---------------------------------------------------------------- partitions and wall blocks (people walk around them)

const wallBlock = (depth, color, trim = PALETTE.woodDark, len = 0.98) => group(box(len, 2.0, depth, color, 0, 0, 0), box(len + 0.02, 0.1, depth + 0.04, trim, 0, 0, 0), box(len + 0.02, 0.05, depth + 0.04, trim, 0, 2.0, 0));
Object.assign(FURNITURE, {
    wall_thin: () => wallBlock(0.12, '#F3E6D2'),
    wall_medium: () => wallBlock(0.3, '#EFE0C8'),
    wall_thick: () => wallBlock(0.6, '#E8D7BC'),
    wall_long: () => wallBlock(0.3, '#EFE0C8', PALETTE.woodDark, 1.98),
    wall_brick_block: () => {
        const g = group(box(0.98, 2.0, 0.3, '#E3D2C0', 0, 0, 0));
        for (let r = 0; r < 10; r++) for (let c = 0; c < 4; c++) {
            const off = (r % 2) * 0.12;
            const x = -0.37 + c * 0.245 + off;
            if (x > 0.42) continue;
            g.add(box(0.22, 0.17, 0.32, ['#B5532E', '#A84B2A', '#BF5C36'][(r + c) % 3], x, r * 0.2 + 0.015, 0));
        }
        return g;
    },
    half_wall_planter: () => group(box(1.96, 0.85, 0.4, '#EFE0C8', 0, 0, 0), box(1.98, 0.06, 0.44, PALETTE.woodDark, 0, 0.85, 0), box(1.86, 0.12, 0.3, '#6B4A33', 0, 0.91, 0),
        ...Array.from({length: 7}, (_, i) => ball(0.13 + (i % 2) * 0.04, i % 3 ? PALETTE.leaf : PALETTE.leafDark, -0.78 + i * 0.26, 1.1, (i % 2 ? 0.05 : -0.05), {}, 6)),
        ...[-0.6, 0, 0.6].map(x => ball(0.05, '#F7A1C4', x, 1.28, 0.08, {}, 6))),
    column: () => group(cyl(0.3, 0.3, 0.12, '#E6E0D6', 0, 0, 0, 16), cyl(0.2, 0.22, 2.1, '#F3EFE6', 0, 0.12, 0, 16, {flat: false}), cyl(0.3, 0.26, 0.14, '#E6E0D6', 0, 2.22, 0, 16),
        ...Array.from({length: 8}, (_, i) => box(0.03, 2.05, 0.03, '#E6E0D6', Math.cos(i * Math.PI / 4) * 0.21, 0.14, Math.sin(i * Math.PI / 4) * 0.21))),
    screen_folding: () => group(...[-0.33, 0, 0.33].map((x, i) => {
        const p = group(box(0.32, 1.6, 0.03, '#E9D5B5', 0, 0.08, 0), box(0.34, 0.04, 0.05, PALETTE.woodDark, 0, 1.68, 0), box(0.04, 1.68, 0.05, PALETTE.woodDark, -0.16, 0, 0),
            ...[0.5, 1.1].map(y => box(0.3, 0.02, 0.04, PALETTE.woodDark, 0, y, 0)), ball(0.04, '#D7263D', 0.05, 1.3, 0.02, {}, 6), ball(0.03, '#F7A1C4', -0.06, 0.8, 0.02, {}, 6));
        p.position.x = x;
        p.rotation.y = i === 1 ? 0 : (i === 0 ? 0.35 : -0.35);
        return p;
    })),
    screen_bamboo: () => group(...Array.from({length: 9}, (_, i) => cyl(0.04, 0.04, 1.8, i % 2 ? '#C9B06A' : '#B89A4E', -0.4 + i * 0.1, 0, 0, 7)),
        ...[0.4, 1.4].map(y => box(0.92, 0.04, 0.1, '#8A7240', 0, y, 0)), box(0.92, 0.12, 0.25, '#6B5530', 0, 0, 0)),
    glass_partition: () => group(box(1.96, 0.12, 0.14, '#2C2C30', 0, 0, 0), box(1.9, 1.7, 0.04, '#CFE8F2', 0, 0.12, 0, {opacity: 0.35, roughness: 0.05}),
        ...[-0.97, 0, 0.97].map(x => box(0.05, 1.86, 0.08, '#2C2C30', x, 0, 0)), box(1.96, 0.05, 0.08, '#2C2C30', 0, 1.82, 0)),
});

// ---------------------------------------------------------------- more plants and rugs

Object.assign(FURNITURE, {
    plant_monstera: () => group(cyl(0.22, 0.17, 0.36, '#F3EFE6', 0, 0, 0, 14), ...Array.from({length: 7}, (_, i) => {
        const leaf = mesh(new THREE.CircleGeometry(0.2, 9, 0, Math.PI * 1.6), mat(i % 2 ? '#2E7D4F' : '#3D8F5C', {flat: false}), 0, 0, 0);
        leaf.material = leaf.material.clone();
        leaf.material.side = THREE.DoubleSide;
        const a = i * 0.9;
        leaf.position.set(Math.cos(a) * 0.18, 0.62 + (i % 3) * 0.14, Math.sin(a) * 0.18);
        leaf.rotation.set(-0.9, a, 0.3);
        return leaf;
    }), cyl(0.012, 0.012, 0.4, PALETTE.leafDark, 0, 0.36, 0, 5)),
    plant_ficus: () => group(cyl(0.2, 0.16, 0.34, PALETTE.terracotta, 0, 0, 0, 12), cyl(0.03, 0.05, 1.0, PALETTE.woodDark, 0, 0.34, 0, 6),
        ...[[0, 1.45, 0, 0.36], [0.2, 1.25, 0.1, 0.26], [-0.2, 1.3, -0.08, 0.28], [0.05, 1.7, 0.05, 0.24]].map(([x, y, z, r], i) => mesh(new THREE.IcosahedronGeometry(r, 1), mat(i % 2 ? PALETTE.leafDark : '#5E9A3E'), x, y, z))),
    plant_stand: () => group(...[0.3, 0.75, 1.2].map(y => box(0.5, 0.03, 0.3, PALETTE.wood, 0, y, 0)), ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => box(0.03, 1.25, 0.03, PALETTE.woodDark, a * 0.23, 0, b * 0.13)),
        ...[[-0.12, 0.33], [0.12, 0.78], [-0.1, 1.23]].map(([x, y]) => group(cyl(0.08, 0.06, 0.12, PALETTE.terracotta, x, y, 0, 10), ball(0.11, PALETTE.leaf, x, y + 0.2, 0, {}, 6))),
        ...Array.from({length: 4}, (_, i) => cyl(0.008, 0.008, 0.3, PALETTE.leafDark, -0.12 + i * 0.02, 1.0, 0.08, 4))),
    plant_trough: () => group(box(1.9, 0.45, 0.4, '#5D7A8C', 0, 0, 0), box(1.86, 0.05, 0.36, '#6B4A33', 0, 0.45, 0),
        ...Array.from({length: 9}, (_, i) => mesh(new THREE.ConeGeometry(0.08, 0.6 + (i % 3) * 0.15, 5), mat(i % 2 ? PALETTE.leaf : PALETTE.leafDark), -0.8 + i * 0.2, 0.75 + (i % 3) * 0.07, (i % 2 ? 0.06 : -0.06)))),
    rug_jute: () => group(mesh(new THREE.CylinderGeometry(0.95, 0.95, 0.03, 28), mat('#C9B07A', {flat: false}), 0, 0.034, 0),
        ...[0.8, 0.6, 0.4, 0.2].map((r, i) => mesh(new THREE.TorusGeometry(r, 0.012, 4, 32), mat(i % 2 ? '#B0955E' : '#D8C190'), 0, 0.05, 0).rotateX(Math.PI / 2))),
    rug_runner: () => group(box(2.9, 0.03, 0.8, '#3E7CB4', 0, 0.028, 0), box(2.7, 0.032, 0.6, '#E9C46A', 0, 0.029, 0), box(2.5, 0.034, 0.4, '#3E7CB4', 0, 0.03, 0),
        ...Array.from({length: 6}, (_, i) => box(0.12, 0.036, 0.12, '#B43E5C', -1.0 + i * 0.4, 0.031, 0).rotateY(Math.PI / 4))),
    rug_large: () => group(box(2.9, 0.03, 2.9, '#7A8B3A', 0, 0.028, 0), box(2.6, 0.032, 2.6, '#F4E6D0', 0, 0.029, 0), box(2.2, 0.034, 2.2, '#7A8B3A', 0, 0.03, 0),
        mesh(new THREE.CylinderGeometry(0.7, 0.7, 0.036, 8), mat('#E9C46A'), 0, 0.031, 0), ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => mesh(new THREE.CylinderGeometry(0.22, 0.22, 0.036, 8), mat('#B43E5C'), a * 0.85, 0.031, b * 0.85))),
});

// ---------------------------------------------------------------- the big catalogue: many more pieces

const shinyO = {flat: false, roughness: 0.3};
const metalO = {metalness: 0.6, roughness: 0.3};
const vase = (x, y, z, c = PALETTE.berry, f = '#F7A1C4') => [cyl(0.05, 0.035, 0.12, c, x, y, z, 10, shinyO), ball(0.05, f, x, y + 0.16, z, {}, 7), ball(0.035, '#FFD23F', x + 0.04, y + 0.14, z + 0.02, {}, 6)];
const pot = (x, z, r = 0.18, h = 0.3, c = PALETTE.terracotta) => [cyl(r, r * 0.78, h, c, x, 0, z, 12), cyl(r * 0.95, r * 0.95, 0.03, '#5A3A22', x, h - 0.04, z, 12)];
const roundTable = (topC, legC, r = 0.42, h = 0.7, o = {}) => [cyl(r, r, 0.05, topC, 0, h, 0, 22, o), cyl(0.045, 0.045, h, legC, 0, 0, 0, 8, metalO), cyl(0.22, 0.25, 0.04, legC, 0, 0, 0, 14, metalO)];
const squareTable = (topC, legC, w = 0.8, d = 0.8, h = 0.7, o = {}) => [box(w, 0.06, d, topC, 0, h, 0, o), ...legs(w - 0.14, d - 0.14, h, legC)];
const chairBase = (seatC, legC, backC = seatC, h = 0.42) => [box(0.42, 0.05, 0.42, seatC, 0, h, 0.02), ...legs(0.34, 0.34, h, legC, 0.022)];
const leafy = (x, y, z, r, c = PALETTE.leaf) => mesh(new THREE.IcosahedronGeometry(r, 0), mat(c), x, y, z);
const lampHead = (x, y, z, r = 0.16, c = '#FFE7A8') => mesh(new THREE.SphereGeometry(r, 12, 8), glowMat(c), x, y, z);
/** An open cabinet against the back of the tile: back, sides, top and bottom (what is inside stays visible). */
const cabinet = (w, h, d, color, z = -0.3) => [box(w, h, 0.04, color, 0, 0, z - d / 2 + 0.02), box(0.04, h, d, color, -w / 2 + 0.02, 0, z), box(0.04, h, d, color, w / 2 - 0.02, 0, z),
    box(w, 0.04, d, color, 0, h - 0.04, z), box(w, 0.06, d, color, 0, 0, z)];

Object.assign(FURNITURE, {
    // ---- tables
    table_white: () => group(...squareTable('#F7F4EE', '#E3DDD2'), cyl(0.04, 0.05, 0.1, '#9ED2C0', 0, 0.76, 0, 8), ball(0.04, '#FFD23F', 0, 0.9, 0, {}, 6)),
    table_iron: () => group(cyl(0.38, 0.38, 0.04, '#D9CBB0', 0, 0.72, 0, 22, shinyO), cyl(0.03, 0.03, 0.72, '#2B2B2E', 0, 0, 0, 6), ...[0, 1, 2].map(i => {
        const l = box(0.04, 0.03, 0.4, '#2B2B2E', Math.cos(i * 2.1) * 0.16, 0, Math.sin(i * 2.1) * 0.16);
        l.rotation.y = -i * 2.1 + Math.PI / 2;
        return l;
    }), mesh(new THREE.TorusGeometry(0.2, 0.012, 5, 18), mat('#2B2B2E'), 0, 0.35, 0).rotateX(Math.PI / 2), ...vase(0.1, 0.76, -0.05)),
    table_oak: () => group(...squareTable('#C08A55', '#8A5A35', 0.84, 0.84, 0.7, {}), box(0.84, 0.02, 0.84, '#A87444', 0, 0.68, 0), box(0.3, 0.012, 0.6, '#FFF3E0', 0, 0.76, 0)),
    table_hex: () => group(mesh(new THREE.CylinderGeometry(0.44, 0.44, 0.06, 6), mat('#6B4A33'), 0, 0.73, 0), cyl(0.06, 0.08, 0.7, '#3B2418', 0, 0, 0, 6), cyl(0.26, 0.28, 0.04, '#3B2418', 0, 0, 0, 6),
        mesh(new THREE.CylinderGeometry(0.34, 0.34, 0.012, 6), mat('#E9C46A'), 0, 0.765, 0)),
    table_mosaic: () => {
        const g = group(cyl(0.42, 0.42, 0.05, '#F2EEE6', 0, 0.7, 0, 22), cyl(0.04, 0.05, 0.7, '#2B2B2E', 0, 0, 0, 8), cyl(0.22, 0.25, 0.04, '#2B2B2E', 0, 0, 0, 14));
        for (let i = 0; i < 18; i++) {
            const a = i * 2.39, r = Math.sqrt(i / 18) * 0.36;
            g.add(box(0.07, 0.012, 0.07, ['#3E7CB4', '#2F9E8F', '#E2B23A', '#D9653B', '#FFFFFF'][i % 5], Math.cos(a) * r, 0.75, Math.sin(a) * r));
        }
        return g;
    },
    table_cafe: () => group(...roundTable('#D7263D', '#C9CED4', 0.36, 0.72, shinyO), cyl(0.38, 0.38, 0.015, '#FFFFFF', 0, 0.715, 0, 22), ...vase(0, 0.77, 0, '#FFFFFF', '#D7263D')),
    table_marble_round: () => group(cyl(0.44, 0.44, 0.06, '#F1EEEA', 0, 0.68, 0, 24, {roughness: 0.25}), cyl(0.46, 0.46, 0.02, PALETTE.gold, 0, 0.66, 0, 24, metalO),
        cyl(0.07, 0.12, 0.66, '#2C2522', 0, 0, 0, 12, shinyO), cyl(0.28, 0.3, 0.04, PALETTE.gold, 0, 0, 0, 16, metalO), cyl(0.04, 0.04, 0.2, '#FFFFFF', 0.12, 0.74, 0.05, 10, {opacity: 0.6}), ball(0.04, '#F2E4C8', 0.12, 0.98, 0.05, {emissive: '#FFB347', glow: 1})),
    table_gold: () => group(box(0.86, 0.06, 0.86, '#1E1E24', 0, 0.68, 0, {roughness: 0.15}), box(0.9, 0.03, 0.9, PALETTE.gold, 0, 0.65, 0, metalO), ...legs(0.7, 0.7, 0.65, PALETTE.gold, 0.035).map(l => (l.material = mat(PALETTE.gold, metalO), l)),
        box(0.7, 0.012, 0.18, '#B43E5C', 0, 0.745, 0), cyl(0.05, 0.03, 0.18, '#FFFFFF', -0.2, 0.74, 0, 10, {opacity: 0.5}), cyl(0.05, 0.03, 0.18, '#FFFFFF', 0.2, 0.74, 0, 10, {opacity: 0.5}), ...vase(0, 0.75, 0, PALETTE.gold, '#FFFFFF')),
    table_rustic_long: () => group(box(1.8, 0.08, 0.82, '#9C6B3F', 0, 0.68, 0), ...[-0.4, -0.13, 0.13, 0.4].map(z => box(1.8, 0.005, 0.012, '#6B4A33', 0, 0.762, z)),
        ...[-0.75, 0.75].map(x => group(box(0.08, 0.68, 0.08, '#6B4A33', x, 0, -0.3), box(0.08, 0.68, 0.08, '#6B4A33', x, 0, 0.3), box(0.08, 0.06, 0.6, '#6B4A33', x, 0.2, 0))),
        box(1.4, 0.06, 0.06, '#6B4A33', 0, 0.2, 0), ...[-0.4, 0.4].map(x => cyl(0.05, 0.05, 0.14, '#F4E6D0', x, 0.76, 0, 10)), ...[-0.4, 0.4].map(x => ball(0.03, '#FFB347', x, 0.93, 0, {emissive: '#FF9A3C', glow: 1.2}, 6))),
    table_banquet: () => group(box(2.8, 0.06, 0.86, '#FFFFFF', 0, 0.7, 0, {flat: false}), box(2.84, 0.4, 0.9, '#FFFFFF', 0, 0.36, 0, {flat: false}),
        box(2.6, 0.012, 0.3, PALETTE.gold, 0, 0.76, 0), ...[-1, 0, 1].map(x => group(...vase(x, 0.77, 0, '#FFFFFF', '#F7A1C4'))), ...[-0.5, 0.5].map(x => cyl(0.04, 0.05, 0.25, PALETTE.gold, x, 0.77, 0, 10, metalO))),
    table_booth: () => group(box(1.7, 0.06, 0.7, '#7B4E2C', 0, 0.72, 0), cyl(0.06, 0.06, 0.72, '#2B2B2E', -0.5, 0, 0, 8), cyl(0.06, 0.06, 0.72, '#2B2B2E', 0.5, 0, 0, 8),
        box(1.7, 0.02, 0.7, '#D9653B', 0, 0.7, 0), cyl(0.05, 0.04, 0.14, '#C9CED4', 0, 0.78, 0, 8, metalO), box(0.25, 0.012, 0.18, '#FFF3E0', -0.5, 0.78, 0), box(0.25, 0.012, 0.18, '#FFF3E0', 0.5, 0.78, 0)),
    table_picnic_blue: () => group(box(1.8, 0.06, 0.72, '#5B8DB8', 0, 0.7, 0), ...[-0.7, 0.7].map(x => box(0.08, 0.7, 0.6, '#3E6F9A', x, 0, 0)),
        ...[-0.6, -0.2, 0.2, 0.6].map(x => box(0.18, 0.012, 0.72, '#FFFFFF', x, 0.762, 0)), cyl(0.12, 0.12, 0.05, '#C98F5A', 0, 0.77, 0, 12)),

    // ---- chairs
    chair_white: () => group(...chairBase('#F7F4EE', '#E3DDD2'), box(0.42, 0.44, 0.04, '#F7F4EE', 0, 0.47, -0.19), ...[-0.1, 0.1].map(x => box(0.05, 0.4, 0.045, '#E3DDD2', x, 0.47, -0.17))),
    chair_bistro_red: () => group(cyl(0.21, 0.21, 0.05, '#D7263D', 0, 0.44, 0.02, 16, shinyO), ...legs(0.3, 0.3, 0.44, '#2B2B2E', 0.018),
        mesh(new THREE.TorusGeometry(0.2, 0.025, 6, 16, Math.PI), mat('#2B2B2E'), 0, 0.68, -0.15), box(0.3, 0.18, 0.03, '#D7263D', 0, 0.56, -0.16)),
    chair_ladder: () => group(...chairBase('#C9A16E', '#8A5A35'), ...[-0.18, 0.18].map(x => box(0.04, 0.55, 0.04, '#8A5A35', x, 0.44, -0.19)), ...[0.6, 0.75, 0.9].map(y => box(0.36, 0.05, 0.03, '#8A5A35', 0, y, -0.19)),
        box(0.4, 0.03, 0.4, '#E8D3B0', 0, 0.465, 0.02)),
    chair_wicker: () => group(cyl(0.24, 0.2, 0.1, '#D9B47A', 0, 0.38, 0.02, 14), ...legs(0.28, 0.28, 0.38, '#B98A50', 0.02), mesh(new THREE.SphereGeometry(0.28, 14, 8, 0, Math.PI * 2, 0, Math.PI / 2.2), mat('#D9B47A'), 0, 0.42, -0.05).rotateX(-0.25),
        cyl(0.2, 0.2, 0.06, '#F4E6D0', 0, 0.48, 0.04, 14, {flat: false})),
    chair_tolix: () => group(box(0.42, 0.03, 0.42, '#4A8C7E', 0, 0.45, 0.02, metalO), ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => {
        const l = cyl(0.015, 0.02, 0.46, '#4A8C7E', a * 0.18, 0, b * 0.18, 6, metalO);
        l.rotation.set(-b * 0.08, 0, a * 0.08);
        return l;
    }), box(0.38, 0.3, 0.03, '#4A8C7E', 0, 0.5, -0.19, metalO), box(0.22, 0.04, 0.035, '#2E6B5E', 0, 0.68, -0.19)),
    chair_ghost: () => group(box(0.42, 0.04, 0.42, '#DDF1F7', 0, 0.44, 0.02, {opacity: 0.45, roughness: 0.05}), ...legs(0.34, 0.34, 0.44, '#DDF1F7', 0.022).map(l => (l.material = mat('#DDF1F7', {opacity: 0.45}), l)),
        mesh(new THREE.TorusGeometry(0.19, 0.03, 6, 16, Math.PI), mat('#DDF1F7', {opacity: 0.45, roughness: 0.05}), 0, 0.62, -0.18)),
    chair_leather: () => group(box(0.5, 0.16, 0.48, '#7B3F22', 0, 0.26, 0.01, shinyO), box(0.5, 0.5, 0.12, '#7B3F22', 0, 0.42, -0.19, shinyO), ...[-1, 1].map(s => box(0.08, 0.2, 0.44, '#6B3519', s * 0.23, 0.42, 0, shinyO)),
        ...Array.from({length: 6}, (_, i) => ball(0.012, PALETTE.gold, -0.15 + (i % 3) * 0.15, 0.6 + Math.floor(i / 3) * 0.15, -0.125, metalO, 5)), ...legs(0.4, 0.38, 0.26, '#3B2418', 0.025)),
    chair_throne: () => group(box(0.56, 0.2, 0.52, '#6B1E3A', 0, 0.26, 0, shinyO), box(0.6, 0.85, 0.12, PALETTE.gold, 0, 0.3, -0.22, metalO), box(0.46, 0.72, 0.04, '#6B1E3A', 0, 0.4, -0.155, shinyO),
        ball(0.06, PALETTE.gold, -0.27, 1.2, -0.22, metalO, 8), ball(0.06, PALETTE.gold, 0.27, 1.2, -0.22, metalO, 8), ball(0.07, '#B43E5C', 0, 1.12, -0.2, shinyO, 8),
        ...[-1, 1].map(s => box(0.08, 0.26, 0.46, PALETTE.gold, s * 0.26, 0.44, 0, metalO)), ...legs(0.46, 0.42, 0.26, PALETTE.gold, 0.03).map(l => (l.material = mat(PALETTE.gold, metalO), l))),
    chair_bamboo: () => group(...chairBase('#C9B06A', '#A08A48'), ...Array.from({length: 5}, (_, i) => cyl(0.018, 0.018, 0.45, i % 2 ? '#B89A4E' : '#C9B06A', -0.16 + i * 0.08, 0.46, -0.19, 6)), box(0.42, 0.04, 0.04, '#A08A48', 0, 0.88, -0.19)),
    chair_egg: () => group(cyl(0.22, 0.26, 0.04, '#2C2C30', 0, 0, 0, 16, metalO), cyl(0.03, 0.03, 0.3, '#2C2C30', 0, 0, 0, 8, metalO),
        mesh(new THREE.SphereGeometry(0.36, 18, 12, Math.PI * 0.15, Math.PI * 1.7, 0, Math.PI * 0.7), mat('#F6F1E9', {flat: false, roughness: 0.4}), 0, 0.62, 0).rotateY(Math.PI / 2 + 0.6).rotateZ(0.2),
        cyl(0.26, 0.26, 0.12, '#E07B39', 0, 0.3, 0.03, 18, {flat: false})),
    stool_wood: () => group(cyl(0.18, 0.18, 0.05, '#B07A4A', 0, 0.62, 0, 14), ...[0, 1, 2, 3].map(i => {
        const l = cyl(0.02, 0.025, 0.64, '#7B4E2C', Math.cos(i * Math.PI / 2 + 0.78) * 0.12, 0, Math.sin(i * Math.PI / 2 + 0.78) * 0.12, 6);
        l.rotation.set(Math.sin(i * Math.PI / 2 + 0.78) * 0.12, 0, -Math.cos(i * Math.PI / 2 + 0.78) * 0.12);
        return l;
    }), mesh(new THREE.TorusGeometry(0.13, 0.012, 5, 16), mat('#7B4E2C'), 0, 0.25, 0).rotateX(Math.PI / 2)),
    stool_cushion: () => group(cyl(0.2, 0.2, 0.1, '#2F9E8F', 0, 0.6, 0, 16, {flat: false}), cyl(0.035, 0.035, 0.6, PALETTE.gold, 0, 0, 0, 8, metalO), cyl(0.2, 0.22, 0.03, PALETTE.gold, 0, 0, 0, 14, metalO),
        mesh(new THREE.TorusGeometry(0.15, 0.012, 5, 16), mat(PALETTE.gold, metalO), 0, 0.28, 0).rotateX(Math.PI / 2)),
    chair_booth: () => group(box(0.92, 0.42, 0.5, '#B43E5C', 0, 0, 0.05, shinyO), box(0.92, 0.7, 0.16, '#B43E5C', 0, 0.42, -0.2, shinyO), box(0.86, 0.1, 0.44, '#C9506C', 0, 0.42, 0.06, shinyO),
        ...[-0.3, 0, 0.3].map(x => box(0.02, 0.6, 0.02, '#8E2E46', x, 0.48, -0.115)), box(0.96, 0.06, 0.2, '#7B4E2C', 0, 1.12, -0.2)),
    bench_cushion: () => group(box(0.9, 0.06, 0.42, PALETTE.wood, 0, 0.4, 0.02), box(0.84, 0.08, 0.38, '#7A8B3A', 0, 0.46, 0.02, {flat: false}), box(0.9, 0.42, 0.06, PALETTE.wood, 0, 0.5, -0.2),
        ...[-0.25, 0.2].map((x, i) => box(0.28, 0.24, 0.08, i ? '#F7A1C4' : '#E9C46A', x, 0.56, -0.13, {flat: false})), ...[-0.4, 0.4].map(x => box(0.06, 0.4, 0.4, PALETTE.woodDark, x, 0, 0))),

    // ---- kitchen
    stove_rustic: () => group(box(0.9, 0.82, 0.8, '#5A4A3E', 0, 0, -0.02), box(0.92, 0.06, 0.82, '#2E2724', 0, 0.82, -0.02), ...[-0.22, 0.22].map(x => cyl(0.14, 0.14, 0.03, '#151110', x, 0.88, -0.05, 14)),
        box(0.5, 0.3, 0.02, '#FF9A3C', 0, 0.18, 0.39, {emissive: '#FF7A1A', glow: 0.8}), box(0.6, 0.04, 0.04, PALETTE.gold, 0, 0.56, 0.4, metalO), cyl(0.12, 0.1, 0.18, '#7B3A22', 0.22, 0.91, -0.05, 12)),
    stove_induction: () => group(box(0.9, 0.84, 0.8, '#FFFFFF', 0, 0, -0.02, shinyO), box(0.92, 0.04, 0.82, '#151515', 0, 0.84, -0.02, {roughness: 0.1}),
        ...[[-0.2, -0.2], [0.2, -0.2], [-0.2, 0.15], [0.2, 0.15]].map(([x, z]) => mesh(new THREE.TorusGeometry(0.1, 0.006, 4, 20), glowMat('#FF5A3C'), x, 0.885, z).rotateX(Math.PI / 2)),
        box(0.7, 0.4, 0.02, '#2C2C30', 0, 0.2, 0.39, {roughness: 0.15}), box(0.3, 0.05, 0.02, '#5CB8D6', 0.2, 0.7, 0.39, {emissive: '#5CB8D6', glow: 0.6})),
    wok_station: () => group(box(0.92, 0.82, 0.82, '#B8BEC6', 0, 0, -0.02, metalO), box(0.94, 0.05, 0.84, '#222', 0, 0.82, -0.02),
        glow(new THREE.CylinderGeometry(0.18, 0.18, 0.02, 14), '#FF6A2A', -0.18, 0.87, -0.05), mesh(new THREE.SphereGeometry(0.22, 14, 8, 0, Math.PI * 2, Math.PI / 2, Math.PI / 2), mat('#2C2C30', metalO), -0.18, 1.08, -0.05),
        cyl(0.012, 0.012, 0.3, '#5A3218', 0.08, 1.0, -0.05, 6).rotateZ(1.3), cyl(0.12, 0.1, 0.16, '#B8BEC6', 0.24, 0.87, -0.1, 12, metalO), box(0.94, 0.5, 0.3, '#B8BEC6', 0, 1.65, -0.28, metalO)),
    oven_pizza_steel: () => group(box(0.94, 0.7, 0.9, '#2C2C30', 0, 0, 0, metalO), mesh(new THREE.SphereGeometry(0.45, 16, 8, 0, Math.PI * 2, 0, Math.PI / 2), mat('#B8BEC6', metalO), 0, 0.7, 0),
        box(0.36, 0.2, 0.08, '#FF9A3C', 0, 0.72, 0.4, {emissive: '#FF7A1A', glow: 1.2}), cyl(0.08, 0.08, 0.5, '#2C2C30', 0, 1.1, -0.1, 10, metalO), box(0.5, 0.06, 0.2, '#C98F5A', 0, 0.62, 0.48)),
    counter_steel: () => group(box(0.94, 0.9, 0.7, '#B8BEC6', 0, 0, -0.1, metalO), box(0.98, 0.05, 0.82, '#D5DAE0', 0, 0.9, -0.06, metalO), ...[-0.22, 0.22].map(x => box(0.42, 0.7, 0.02, '#A7AEB6', x, 0.1, 0.26, metalO)),
        ...[-0.22, 0.22].map(x => box(0.12, 0.03, 0.03, '#2C2C30', x, 0.72, 0.28))),
    counter_butcher: () => group(box(0.94, 0.86, 0.7, '#7B4E2C', 0, 0, -0.1), box(0.98, 0.1, 0.82, '#D9A45C', 0, 0.86, -0.06), ...Array.from({length: 6}, (_, i) => box(0.98, 0.1, 0.004, '#B98448', 0, 0.86, -0.4 + i * 0.14)),
        box(0.3, 0.02, 0.2, '#C9CED4', -0.2, 0.96, -0.1, metalO), ball(0.06, '#D7263D', 0.2, 1.0, -0.1, {}, 8), ball(0.05, '#7CB342', 0.3, 0.99, 0.0, {}, 8)),
    counter_pastry: () => group(box(0.94, 0.6, 0.7, '#F4E6D0', 0, 0, -0.1), box(0.94, 0.42, 0.66, '#DDF1F7', 0, 0.6, -0.1, {opacity: 0.35, roughness: 0.05}), box(0.98, 0.04, 0.72, '#F7F4EE', 0, 1.02, -0.1),
        box(0.9, 0.02, 0.6, '#FFFFFF', 0, 0.6, -0.1), cyl(0.1, 0.1, 0.08, '#F7C6D0', -0.25, 0.62, -0.1, 14, {flat: false}), cyl(0.09, 0.09, 0.1, '#5A3218', 0.05, 0.62, -0.1, 14, {flat: false}), cyl(0.08, 0.08, 0.07, '#F6D55C', 0.3, 0.62, -0.1, 14, {flat: false}),
        ...[-0.25, 0.05, 0.3].map(x => ball(0.025, '#D7263D', x, 0.73, -0.1, {}, 6))),
    counter_bar: () => group(box(0.96, 1.0, 0.6, '#5A3218', 0, 0, -0.15), box(1.0, 0.06, 0.8, '#2C2522', 0, 1.0, -0.05, {roughness: 0.2}), ...Array.from({length: 4}, (_, i) => box(0.2, 0.9, 0.02, '#6B4026', -0.36 + i * 0.24, 0.05, 0.16)),
        box(0.96, 0.04, 0.04, PALETTE.gold, 0, 0.12, 0.2, metalO), ...[-0.3, -0.1, 0.25].map((x, i) => cyl(0.03, 0.03, 0.2, ['#2F6F3E', '#7B1E2A', '#E9C46A'][i], x, 1.06, -0.2, 8, shinyO))),
    fridge_double: () => group(box(1.0, 1.9, 0.7, '#D5DAE0', 0, 0, -0.1, metalO), box(0.01, 1.84, 0.02, '#8A9097', 0, 0.03, 0.26), ...[-0.08, 0.08].map(x => box(0.03, 0.6, 0.04, '#2C2C30', x, 0.8, 0.27)),
        box(0.25, 0.2, 0.01, '#5CB8D6', -0.25, 1.4, 0.26, {emissive: '#5CB8D6', glow: 0.4})),
    freezer_chest: () => group(box(0.94, 0.8, 0.66, '#F4F6F7', 0, 0, -0.1, shinyO), box(0.96, 0.06, 0.68, '#DDE2E6', 0, 0.8, -0.1), box(0.4, 0.04, 0.04, '#B8BEC6', 0, 0.7, 0.24, metalO), box(0.12, 0.06, 0.01, '#5CB8D6', 0.3, 0.6, 0.24, {emissive: '#5CB8D6', glow: 0.4})),
    dishwasher: () => group(box(0.9, 0.88, 0.7, '#B8BEC6', 0, 0, -0.1, metalO), box(0.94, 0.05, 0.8, '#F2EEE6', 0, 0.88, -0.06), box(0.6, 0.04, 0.03, '#2C2C30', 0, 0.75, 0.26),
        box(0.12, 0.04, 0.01, '#6BBF59', 0.3, 0.82, 0.26, {emissive: '#6BBF59', glow: 0.6}), ...[-0.2, 0, 0.2].map(x => cyl(0.1, 0.1, 0.012, '#FFFFFF', x, 0.95, -0.1, 14).rotateX(Math.PI / 2))),
    spice_rack: () => group(...cabinet(0.8, 1.2, 0.25, '#B07A4A'), ...[0.25, 0.6, 0.95].map(y => box(0.76, 0.03, 0.22, '#8A5A35', 0, y, -0.28)),
        ...Array.from({length: 15}, (_, i) => cyl(0.035, 0.035, 0.12, ['#D7263D', '#E9A21D', '#7CB342', '#5A3218', '#F4E6D0'][i % 5], -0.3 + (i % 5) * 0.15, 0.28 + Math.floor(i / 5) * 0.35, -0.28, 8))),
    shelf_pots: () => group(...[0, 0.5, 1.0, 1.5].map(y => box(0.9, 0.03, 0.4, '#B8BEC6', 0, y, -0.25, metalO)), ...[[-1, -1], [1, -1], [-1, 1], [1, 1]].map(([a, b]) => box(0.03, 1.55, 0.03, '#8A9097', a * 0.43, 0, -0.25 + b * 0.18)),
        cyl(0.15, 0.14, 0.22, '#C9CED4', -0.2, 0.53, -0.25, 14, metalO), cyl(0.12, 0.12, 0.16, '#D7263D', 0.2, 0.53, -0.25, 14, shinyO), cyl(0.1, 0.1, 0.25, '#2C2C30', -0.15, 1.03, -0.25, 12), ...[-0.25, 0, 0.25].map(x => cyl(0.1, 0.1, 0.03, '#FFFFFF', x, 1.53, -0.25, 14))),
    mixer_station: () => group(box(0.9, 0.86, 0.7, '#9ED2C0', 0, 0, -0.1), box(0.94, 0.05, 0.8, '#F2EEE6', 0, 0.86, -0.06), box(0.2, 0.06, 0.25, '#D7263D', -0.15, 0.91, -0.1, shinyO), box(0.08, 0.35, 0.08, '#D7263D', -0.15, 0.97, -0.2, shinyO),
        box(0.22, 0.1, 0.24, '#D7263D', -0.15, 1.28, -0.12, shinyO), cyl(0.09, 0.07, 0.14, '#C9CED4', -0.15, 0.97, -0.05, 12, metalO), ...[0.15, 0.3].map((x, i) => cyl(0.06, 0.06, 0.18, i ? '#F4E6D0' : '#FFFFFF', x, 0.91, -0.15, 10))),
    host_stand: () => group(box(0.6, 1.0, 0.45, '#7B4E2C', 0, 0, -0.05), box(0.66, 0.05, 0.5, '#5A3218', 0, 1.0, -0.05), box(0.3, 0.02, 0.22, '#FFF3E0', 0, 1.05, -0.05).rotateX(-0.25), ...vase(0.2, 1.05, -0.15), lampHead(-0.2, 1.15, -0.15, 0.06)),
    register_modern: () => group(box(0.8, 0.9, 0.6, '#F4F6F7', 0, 0, -0.1, shinyO), box(0.84, 0.04, 0.64, '#2C2C30', 0, 0.9, -0.1), box(0.04, 0.3, 0.04, '#2C2C30', 0, 0.94, -0.15),
        glow(new THREE.BoxGeometry(0.36, 0.24, 0.02), '#9FE0F7', 0, 1.3, -0.15), box(0.2, 0.02, 0.14, '#2C2C30', 0.2, 0.94, 0.05)),
    trash_recycle: () => group(...['#3E7CB4', '#F6C945', '#7A8B3A'].map((c, i) => group(box(0.26, 0.6, 0.36, c, -0.3 + i * 0.3, 0, 0), box(0.28, 0.04, 0.38, c, -0.3 + i * 0.3, 0.6, 0), box(0.1, 0.02, 0.02, '#FFFFFF', -0.3 + i * 0.3, 0.45, 0.185)))),
    water_station: () => group(box(0.6, 0.85, 0.5, '#B07A4A', 0, 0, -0.1), box(0.64, 0.04, 0.54, '#F2EEE6', 0, 0.85, -0.1), cyl(0.12, 0.12, 0.35, '#CFEAF2', -0.1, 0.89, -0.15, 14, {opacity: 0.55}),
        ...[0, 0.12, 0.24].map(x => cyl(0.035, 0.03, 0.09, '#DDF1F7', 0.1 + x * 0.5, 0.89, 0.05, 8, {opacity: 0.6})), ...Array.from({length: 4}, (_, i) => ball(0.025, '#F6C945', -0.1 + (i % 2) * 0.05, 1.0 + i * 0.05, -0.15, {}, 6))),

    // ---- lounge, music, fun, cosy, snacks
    bean_bags: () => group(...[[-0.25, '#E07B39'], [0.25, '#3E7CB4']].map(([x, c]) => {
        const b = mesh(new THREE.SphereGeometry(0.28, 14, 10), mat(c, {flat: false, roughness: 0.9}), x, 0.2, 0);
        b.scale.set(1, 0.7, 1);
        return b;
    })),
    sofa_leather: () => group(box(1.8, 0.24, 0.8, '#5A3218', 0, 0.16, 0, shinyO), box(1.8, 0.55, 0.2, '#5A3218', 0, 0.3, -0.32, shinyO), ...[-1, 1].map(s => box(0.18, 0.32, 0.8, '#4A2814', s * 0.86, 0.3, 0, shinyO)),
        ...[-0.4, 0.4].map(x => box(0.76, 0.1, 0.58, '#6B3E20', x, 0.4, 0.06, shinyO)), ...Array.from({length: 8}, (_, i) => ball(0.015, PALETTE.gold, -0.7 + (i % 4) * 0.46, 0.55 + Math.floor(i / 4) * 0.2, -0.215, metalO, 5)), ...legs(1.6, 0.6, 0.16, '#2C2522', 0.03)),
    sofa_corner: () => group(box(1.9, 0.24, 0.8, '#7FB3C9', 0, 0.16, -0.55, {flat: false}), box(0.8, 0.24, 1.1, '#7FB3C9', -0.55, 0.16, 0.4, {flat: false}), box(1.9, 0.55, 0.2, '#6A9DB3', 0, 0.3, -0.87, {flat: false}),
        box(0.2, 0.55, 1.9, '#6A9DB3', -0.87, 0.3, 0, {flat: false}), ...[[-0.5, -0.5, '#F7A1C4'], [0.4, -0.55, '#E9C46A'], [-0.55, 0.5, '#FFFFFF']].map(([x, z, c]) => box(0.3, 0.28, 0.1, c, x, 0.4, z - 0.15, {flat: false}).rotateY(z > 0 ? Math.PI / 2 : 0)),
        box(0.7, 0.3, 0.7, '#B07A4A', 0.45, 0, 0.45), box(0.6, 0.02, 0.4, '#FFF3E0', 0.45, 0.3, 0.45)),
    espresso_bar: () => group(box(0.92, 0.95, 0.6, '#2C2522', 0, 0, -0.15), box(0.96, 0.05, 0.64, '#F1EEEA', 0, 0.95, -0.15, {roughness: 0.2}), box(0.6, 0.4, 0.35, '#C9CED4', 0, 1.0, -0.2, metalO),
        ...[-0.15, 0.15].map(x => cyl(0.04, 0.04, 0.08, '#2C2C30', x, 1.08, 0.0, 8)), ball(0.06, PALETTE.gold, 0, 1.46, -0.2, metalO, 10), ...[-0.3, 0.3].map(x => cyl(0.04, 0.035, 0.06, '#FFFFFF', x, 1.0, 0.05, 10))),
    water_cooler: () => group(box(0.4, 0.95, 0.4, '#F4F6F7', 0, 0, -0.1), cyl(0.15, 0.15, 0.42, '#7FC4D9', 0, 0.95, -0.1, 16, {opacity: 0.6, roughness: 0.05}), box(0.06, 0.04, 0.06, '#3E7CB4', -0.08, 0.6, 0.11), box(0.06, 0.04, 0.06, '#D7263D', 0.08, 0.6, 0.11)),
    radio_retro: () => group(box(0.5, 0.75, 0.4, '#7B4E2C', 0, 0, -0.1), box(0.56, 0.4, 0.36, '#C98F5A', 0, 0.75, -0.1), mesh(new THREE.CylinderGeometry(0.28, 0.28, 0.36, 16, 1, false, 0, Math.PI), mat('#C98F5A'), 0, 1.15, -0.1).rotateZ(Math.PI / 2).rotateY(Math.PI / 2),
        box(0.36, 0.2, 0.02, '#F4E6D0', 0, 0.88, 0.09), ...[-0.12, 0.12].map(x => cyl(0.04, 0.04, 0.03, '#3B2418', x, 0.8, 0.09, 10).rotateX(Math.PI / 2))),
    guitar_stand: () => {
        const body = mesh(new THREE.SphereGeometry(0.2, 14, 10), mat('#D9653B', shinyO), 0, 0.35, 0);
        body.scale.set(1, 1.2, 0.35);
        const upper = mesh(new THREE.SphereGeometry(0.15, 14, 10), mat('#D9653B', shinyO), 0, 0.62, 0);
        upper.scale.set(1, 1, 0.35);
        return group(body, upper, cyl(0.05, 0.05, 0.02, '#2A1D17', 0, 0.45, 0.07, 12).rotateX(Math.PI / 2), box(0.06, 0.6, 0.04, '#5A3218', 0, 0.7, 0), box(0.1, 0.14, 0.05, '#3B2418', 0, 1.3, 0),
            box(0.4, 0.04, 0.04, '#2B2B2E', 0, 0, -0.1), box(0.04, 0.5, 0.04, '#2B2B2E', 0, 0, -0.15));
    },
    drum_kit: () => group(cyl(0.3, 0.3, 0.35, '#B43E5C', 0, 0.2, -0.1, 18, shinyO).rotateX(Math.PI / 2), cyl(0.28, 0.28, 0.02, '#FFFFFF', 0, 0.2, 0.08, 18).rotateX(Math.PI / 2).translateY(0),
        ...[[-0.38, 0.55, 0.1, 0.14], [0.38, 0.55, 0.1, 0.14], [0, 0.72, -0.05, 0.12]].map(([x, y, z, r]) => cyl(r, r, 0.16, '#B43E5C', x, y, z, 14, shinyO)),
        ...[-0.55, 0.55].map(x => group(cyl(0.012, 0.012, 1.0, '#C9CED4', x, 0, 0, 6, metalO), cyl(0.2, 0.2, 0.01, PALETTE.gold, x, 1.0, 0, 16, metalO))), box(0.3, 0.45, 0.3, '#2C2C30', 0, 0, -0.55)),
    harp: () => group(box(0.5, 0.12, 0.35, PALETTE.gold, 0, 0, 0, metalO), mesh(new THREE.TorusGeometry(0.55, 0.04, 6, 20, Math.PI * 0.6), mat(PALETTE.gold, metalO), 0.05, 1.0, 0).rotateZ(0.4),
        cyl(0.04, 0.06, 1.5, PALETTE.gold, -0.18, 0.1, 0, 8, metalO), box(0.06, 1.2, 0.08, PALETTE.gold, 0.22, 0.12, 0, metalO), ...Array.from({length: 7}, (_, i) => cyl(0.004, 0.004, 1.0 - i * 0.08, '#FFFFFF', -0.12 + i * 0.05, 0.2, 0, 4))),
    dj_booth: () => group(box(1.8, 0.95, 0.6, '#2C2C30', 0, 0, -0.1), box(1.84, 0.04, 0.64, '#151515', 0, 0.95, -0.1), ...[-0.5, 0.5].map(x => cyl(0.16, 0.16, 0.03, '#3A3A40', x, 0.99, -0.1, 18)),
        box(0.4, 0.04, 0.3, '#3A3A40', 0, 0.99, -0.1), glow(new THREE.BoxGeometry(1.76, 0.08, 0.02), '#FF5DA2', 0, 0.6, 0.21), glow(new THREE.BoxGeometry(1.76, 0.08, 0.02), '#5CB8D6', 0, 0.35, 0.21),
        ...[-0.85, 0.85].map(x => box(0.3, 0.6, 0.3, '#151515', x, 0.99, -0.2)), ...[-0.85, 0.85].map(x => cyl(0.1, 0.1, 0.02, '#555', x, 1.3, -0.04, 14).rotateX(Math.PI / 2))),
    pinball: () => group(box(0.6, 0.12, 1.0, '#2F5DA8', 0, 0.75, 0.05).rotateX(-0.08), ...legs(0.5, 0.86, 0.75, '#2C2C30', 0.03), glow(new THREE.PlaneGeometry(0.5, 0.85), '#7FD1F0', 0, 0.9, 0.05).rotateX(-Math.PI / 2 - 0.08),
        box(0.6, 0.65, 0.12, '#2F5DA8', 0, 0.85, -0.45), glow(new THREE.PlaneGeometry(0.5, 0.45), '#FF5DA2', 0, 1.2, -0.388), ball(0.03, '#C9CED4', 0.1, 0.92, 0.2, metalO, 8)),
    foosball: () => group(box(1.6, 0.25, 0.8, '#2F7D3A', 0, 0.62, 0), box(1.5, 0.02, 0.7, '#4CAF50', 0, 0.86, 0), box(1.66, 0.3, 0.06, '#7B4E2C', 0, 0.62, -0.42), box(1.66, 0.3, 0.06, '#7B4E2C', 0, 0.62, 0.42),
        ...legs(1.4, 0.6, 0.62, '#7B4E2C', 0.05), ...[-0.55, -0.2, 0.2, 0.55].map((x, i) => group(cyl(0.012, 0.012, 1.1, '#C9CED4', x, 0.86, 0, 6, metalO).rotateX(Math.PI / 2), ...[-0.2, 0, 0.2].map(z => box(0.05, 0.12, 0.04, i % 2 ? '#D7263D' : '#3E7CB4', x, 0.8, z))))),
    dartboard: () => group(box(0.06, 1.6, 0.06, '#5A3218', 0, 0, -0.3), cyl(0.28, 0.28, 0.06, '#2C2C30', 0, 1.45, -0.25, 24).rotateX(Math.PI / 2),
        ...[0.22, 0.15, 0.08].map((r, i) => cyl(r, r, 0.07, ['#F4E6D0', '#2F7D3A', '#D7263D'][i], 0, 1.45, -0.245 + i * 0.002, 24).rotateX(Math.PI / 2)), box(0.5, 0.04, 0.2, '#5A3218', 0, 0.9, -0.2)),
    pool_table: () => group(box(1.9, 0.18, 1.0, '#7B4E2C', 0, 0.62, 0), box(1.78, 0.02, 0.88, '#2F7D3A', 0, 0.8, 0), ...[[-0.8, -0.4], [0.8, -0.4], [-0.8, 0.4], [0.8, 0.4]].map(([x, z]) => cyl(0.1, 0.12, 0.62, '#5A3218', x, 0, z, 8)),
        ...[[-0.3, 0, '#FFFFFF'], [0.3, 0, '#D7263D'], [0.38, 0.06, '#F6C945'], [0.38, -0.06, '#3E7CB4'], [0.46, 0, '#2C2C30']].map(([x, z, c]) => ball(0.04, c, x, 0.85, z, shinyO, 8)),
        cyl(0.012, 0.02, 1.3, '#C98F5A', -0.4, 0.86, 0.2, 6).rotateZ(Math.PI / 2 - 0.05)),
    board_games: () => group(...cabinet(0.9, 1.4, 0.36, '#B07A4A'), ...[0.05, 0.5, 0.95].map(y => box(0.84, 0.03, 0.32, '#8A5A35', 0, y, -0.28)),
        ...Array.from({length: 9}, (_, i) => box(0.24, 0.06 + (i % 3) * 0.02, 0.26, ['#D7263D', '#3E7CB4', '#F6C945', '#2F9E8F', '#6B4FA0', '#E07B39'][i % 6], -0.28 + (i % 3) * 0.28, 0.08 + Math.floor(i / 3) * 0.45, -0.28))),
    claw_machine: () => group(box(0.8, 0.9, 0.8, '#F7A1C4', 0, 0, 0), box(0.76, 0.8, 0.76, '#DDF1F7', 0, 0.9, 0, {opacity: 0.3, roughness: 0.05}), box(0.8, 0.2, 0.8, '#F7A1C4', 0, 1.7, 0),
        glow(new THREE.BoxGeometry(0.7, 0.1, 0.02), '#FFD23F', 0, 1.75, 0.41), ...Array.from({length: 6}, (_, i) => ball(0.08, ['#F6C945', '#7FC4D9', '#E07B39', '#9B5DE5', '#6BBF59', '#FFFFFF'][i], -0.2 + (i % 3) * 0.2, 1.0, -0.15 + Math.floor(i / 3) * 0.25, {flat: false}, 8)),
        cyl(0.01, 0.01, 0.3, '#C9CED4', 0, 1.4, 0, 6, metalO), box(0.12, 0.06, 0.12, '#C9CED4', 0, 1.35, 0, metalO)),
    stove_tile: () => {
        const g = group(box(0.8, 1.6, 0.7, '#9ED2C0', 0, 0, -0.1, shinyO), box(0.86, 0.1, 0.76, '#7FB8A6', 0, 1.6, -0.1), box(0.4, 0.3, 0.04, '#2A1A14', 0, 0.3, 0.26));
        for (let r = 0; r < 6; r++) for (let c = 0; c < 3; c++) g.add(box(0.24, 0.22, 0.01, (r + c) % 2 ? '#B5E0D2' : '#8CC9B6', -0.26 + c * 0.26, 0.66 + r * 0.155, 0.255));
        const f = fire(0, 0.32, 0.22, 0.6);
        g.add(f);
        g.userData.flames = f.userData.flames;
        return g;
    },
    electric_fire: () => group(box(0.9, 0.7, 0.3, '#2C2C30', 0, 0, -0.3), glow(new THREE.BoxGeometry(0.7, 0.35, 0.02), '#FF8A2A', 0, 0.15, -0.14), box(0.96, 0.06, 0.36, '#F2EEE6', 0, 0.7, -0.3),
        ...[-0.2, 0, 0.2].map(x => glow(new THREE.ConeGeometry(0.06, 0.22, 6), '#FFD23F', x, 0.26, -0.13))),
    popcorn_machine: () => group(box(0.6, 0.7, 0.5, '#D7263D', 0, 0, -0.05, shinyO), ...legs(0.5, 0.4, 0.15, PALETTE.gold, 0.02), box(0.56, 0.6, 0.46, '#FFF7DD', 0, 0.7, -0.05, {opacity: 0.45, roughness: 0.05}),
        box(0.62, 0.15, 0.52, '#D7263D', 0, 1.3, -0.05, shinyO), ...Array.from({length: 16}, (_, i) => ball(0.04, '#FFF3C4', -0.2 + (i % 4) * 0.13, 0.76 + Math.floor(i / 4) * 0.05, -0.15 + (i % 3) * 0.1, {}, 5)), glow(new THREE.BoxGeometry(0.4, 0.08, 0.02), '#FFD23F', 0, 1.33, 0.21)),
    ice_cream_cart: () => group(box(1.0, 0.7, 0.6, '#FFFFFF', 0, 0.2, 0, shinyO), box(1.0, 0.12, 0.6, '#F7A1C4', 0, 0.2, 0), ...[-0.4, 0.4].map(x => cyl(0.18, 0.18, 0.06, '#2C2C30', x, 0.18, 0.32, 14).rotateX(Math.PI / 2)),
        cyl(0.02, 0.02, 1.0, '#C9CED4', 0, 0.9, 0, 6), mesh(new THREE.ConeGeometry(0.7, 0.3, 8), mat('#F7A1C4'), 0, 2.0, 0), ...['#F7A1C4', '#FFF3C4', '#7B4E2C', '#9ED2C0'].map((c, i) => ball(0.07, c, -0.3 + i * 0.2, 0.95, 0, {flat: false}, 8))),
    candy_jars: () => group(box(0.7, 0.85, 0.45, '#F4E6D0', 0, 0, -0.1), box(0.74, 0.04, 0.49, '#B07A4A', 0, 0.85, -0.1),
        ...[[-0.2, '#F7A1C4'], [0, '#9ED2C0'], [0.2, '#FFD23F']].map(([x, c]) => group(cyl(0.08, 0.08, 0.26, '#DDF1F7', x, 0.89, -0.1, 12, {opacity: 0.35}), ...Array.from({length: 5}, (_, i) => ball(0.025, c, x + (i % 2 - 0.5) * 0.06, 0.93 + i * 0.035, -0.1, {}, 5)), cyl(0.085, 0.085, 0.03, '#D7263D', x, 1.15, -0.1, 12)))),

    // ---- decorations: plants
    plant_snake: () => group(...pot(0, 0, 0.17, 0.32, '#F2EEE6'), ...Array.from({length: 7}, (_, i) => {
        const l = mesh(new THREE.ConeGeometry(0.05, 0.7 + (i % 3) * 0.15, 4), mat(i % 2 ? '#3F7A2E' : '#7CB342'), Math.cos(i * 0.9) * 0.07, 0.62, Math.sin(i * 0.9) * 0.07);
        l.rotation.set(Math.sin(i) * 0.15, i, Math.cos(i) * 0.15);
        l.scale.z = 0.3;
        return l;
    })),
    plant_olive: () => group(...pot(0, 0, 0.22, 0.38, '#B9A58C'), cyl(0.04, 0.06, 0.9, '#7B6A55', 0, 0.38, 0, 6),
        ...[[0, 1.45, 0, 0.32], [0.22, 1.3, 0.05, 0.22], [-0.2, 1.32, -0.05, 0.24], [0.05, 1.65, 0.05, 0.2]].map(([x, y, z, r], i) => leafy(x, y, z, r, i % 2 ? '#8FA06A' : '#A4B57E')),
        ...Array.from({length: 6}, (_, i) => ball(0.03, '#3B3F2A', Math.cos(i) * 0.25, 1.3 + (i % 3) * 0.12, Math.sin(i) * 0.25, {}, 5))),
    plant_fern: () => group(...pot(0, 0, 0.2, 0.42, '#E8D3B0'), ...Array.from({length: 9}, (_, i) => {
        const l = mesh(new THREE.ConeGeometry(0.09, 0.6, 4), mat(i % 2 ? '#4E8A3A' : '#6FAF45'), Math.cos(i * 0.7) * 0.18, 0.6, Math.sin(i * 0.7) * 0.18);
        l.rotation.set(Math.sin(i * 0.7) * 1.1, 0, -Math.cos(i * 0.7) * 1.1);
        l.scale.z = 0.25;
        return l;
    })),
    plant_lemon: () => group(...pot(0, 0, 0.22, 0.36, '#3E7CB4'), cyl(0.035, 0.05, 0.7, '#7B6A55', 0, 0.36, 0, 6), leafy(0, 1.25, 0, 0.36, '#3F7A2E'), leafy(0.15, 1.45, 0.05, 0.22, '#4E8A3A'),
        ...Array.from({length: 7}, (_, i) => ball(0.045, '#F6C945', Math.cos(i * 0.9) * 0.3, 1.15 + (i % 3) * 0.15, Math.sin(i * 0.9) * 0.3, {flat: false}, 7))),
    plant_bamboo: () => group(box(0.5, 0.35, 0.5, '#2C2C30', 0, 0, 0), ...Array.from({length: 7}, (_, i) => cyl(0.025, 0.025, 1.2 + (i % 3) * 0.3, i % 2 ? '#8FB84A' : '#A4C95A', -0.15 + (i % 4) * 0.1, 0.35, -0.12 + Math.floor(i / 4) * 0.24, 6)),
        ...Array.from({length: 10}, (_, i) => {
            const l = mesh(new THREE.ConeGeometry(0.04, 0.3, 4), mat('#6FAF45'), -0.18 + (i % 5) * 0.09, 1.2 + (i % 4) * 0.2, (i % 2 ? 0.1 : -0.1));
            l.rotation.z = (i % 2 ? 1 : -1) * 1.1;
            return l;
        })),
    plant_orchid: () => group(cyl(0.1, 0.08, 0.18, '#FFFFFF', 0, 0.7, 0, 12, shinyO), box(0.4, 0.04, 0.4, '#B07A4A', 0, 0.66, 0), ...legs(0.32, 0.32, 0.66, '#7B4E2C', 0.02), cyl(0.008, 0.008, 0.45, '#3F7A2E', 0, 0.86, 0, 4),
        ...Array.from({length: 5}, (_, i) => ball(0.04, i % 2 ? '#F7A1C4' : '#E75A9A', 0.04 * Math.sin(i), 1.0 + i * 0.06, 0.06 + i * 0.01, {}, 6)), ...[-1, 1].map(s => box(0.18, 0.01, 0.06, '#3F7A2E', s * 0.08, 0.88, 0).rotateZ(s * 0.3))),
    plant_hanging: () => group(box(0.04, 1.8, 0.04, '#5A3218', 0, 0, 0), box(0.5, 0.04, 0.04, '#5A3218', 0.2, 1.78, 0), cyl(0.005, 0.005, 0.5, '#C9B07A', 0.4, 1.3, 0, 4), cyl(0.15, 0.1, 0.16, '#D9653B', 0.4, 1.15, 0, 12),
        ...Array.from({length: 8}, (_, i) => leafy(0.4 + Math.cos(i) * 0.12, 1.1 - i * 0.07, Math.sin(i) * 0.12, 0.07, i % 2 ? '#4E8A3A' : '#6FAF45')), box(0.3, 0.04, 0.3, '#5A3218', 0, 0, 0)),
    plant_lavender: () => group(box(0.8, 0.3, 0.32, '#F2EEE6', 0, 0, 0), ...Array.from({length: 12}, (_, i) => group(cyl(0.008, 0.008, 0.3, '#7A8B5A', -0.33 + (i % 6) * 0.13, 0.3, -0.07 + Math.floor(i / 6) * 0.14, 4),
        cyl(0.025, 0.015, 0.12, '#9B7BC8', -0.33 + (i % 6) * 0.13, 0.56, -0.07 + Math.floor(i / 6) * 0.14, 6)))),
    terrarium: () => group(box(0.5, 0.75, 0.4, '#5A3218', 0, 0, 0), mesh(new THREE.SphereGeometry(0.22, 14, 10), mat('#DDF1F7', {opacity: 0.3, roughness: 0.05}), 0, 0.97, 0), cyl(0.18, 0.18, 0.05, '#7B5A3A', 0, 0.8, 0, 14),
        leafy(-0.05, 0.92, 0, 0.08, '#6FAF45'), cyl(0.03, 0.04, 0.15, '#4E8A3A', 0.08, 0.82, 0.02, 6), ball(0.04, '#C9CED4', 0.02, 0.86, -0.08, {}, 5)),
    cactus_trio: () => group(...[[-0.2, 0.3, '#4E8A3A'], [0.05, 0.55, '#5E9A3E'], [0.25, 0.4, '#3F7A2E']].map(([x, h, c], i) => group(cyl(0.11, 0.09, 0.18, ['#D9653B', '#F2EEE6', '#3E7CB4'][i], x, 0, 0, 10),
        cyl(0.06, 0.07, h, c, x, 0.18, 0, 8), ball(0.03, '#F7A1C4', x, 0.2 + h, 0, {}, 5)))),
    // ---- decorations: objects and art
    statue_bust: () => group(box(0.4, 0.9, 0.4, '#E6E0D6', 0, 0, 0), box(0.46, 0.06, 0.46, '#D6CFC2', 0, 0.9, 0), box(0.3, 0.2, 0.2, '#F2EEE6', 0, 0.96, 0, {flat: false}), ball(0.13, '#F2EEE6', 0, 1.32, 0, {flat: false}, 12), cyl(0.06, 0.07, 0.12, '#F2EEE6', 0, 1.14, 0, 10)),
    vase_tall: () => group(mesh(new THREE.LatheGeometry([[0.0, 0], [0.16, 0.02], [0.22, 0.3], [0.12, 0.7], [0.15, 0.9], [0.0, 0.9]].map(([x, y]) => new THREE.Vector2(x, y)), 18), mat('#2F5DA8', shinyO), 0, 0, 0),
        mesh(new THREE.TorusGeometry(0.2, 0.012, 5, 18), mat('#FFFFFF'), 0, 0.3, 0).rotateX(Math.PI / 2), ...Array.from({length: 5}, (_, i) => cyl(0.006, 0.006, 0.5, '#3F7A2E', Math.cos(i) * 0.05, 0.9, Math.sin(i) * 0.05, 4)),
        ...Array.from({length: 5}, (_, i) => ball(0.05, ['#FFFFFF', '#F7A1C4', '#FFD23F'][i % 3], Math.cos(i) * 0.12, 1.4 + (i % 2) * 0.06, Math.sin(i) * 0.12, {}, 6))),
    mirror_standing: () => group(box(0.6, 1.7, 0.06, PALETTE.gold, 0, 0.05, -0.1, metalO), box(0.5, 1.5, 0.02, '#CFE3EC', 0, 0.15, -0.06, {roughness: 0.05, metalness: 0.4}), box(0.5, 0.05, 0.4, PALETTE.gold, 0, 0, -0.1, metalO)),
    telescope: () => group(...[0, 1, 2].map(i => {
        const l = cyl(0.015, 0.02, 1.0, '#7B4E2C', Math.cos(i * 2.1) * 0.15, 0, Math.sin(i * 2.1) * 0.15, 6);
        l.rotation.set(Math.sin(i * 2.1) * 0.25, 0, -Math.cos(i * 2.1) * 0.25);
        return l;
    }), cyl(0.06, 0.09, 0.8, PALETTE.gold, 0, 1.05, 0, 12, metalO).rotateZ(-1.0), cyl(0.04, 0.04, 0.1, '#2C2C30', -0.35, 0.85, 0, 8).rotateZ(-1.0)),
    armor_suit: () => group(box(0.5, 0.1, 0.4, '#5A3218', 0, 0, 0), ...[-0.08, 0.08].map(x => cyl(0.06, 0.07, 0.75, '#B8BEC6', x, 0.1, 0, 8, metalO)), box(0.36, 0.5, 0.24, '#B8BEC6', 0, 0.85, 0, metalO),
        ...[-0.24, 0.24].map(x => cyl(0.05, 0.05, 0.5, '#B8BEC6', x, 0.85, 0, 8, metalO)), ball(0.14, '#B8BEC6', 0, 1.48, 0, metalO, 10), box(0.16, 0.03, 0.02, '#2C2C30', 0, 1.48, 0.13), mesh(new THREE.ConeGeometry(0.03, 0.2, 4), mat('#B43E5C'), 0, 1.68, 0)),
    bird_cage: () => group(cyl(0.03, 0.03, 1.2, PALETTE.gold, 0, 0, 0, 6, metalO), cyl(0.2, 0.25, 0.04, PALETTE.gold, 0, 0, 0, 12, metalO), cyl(0.22, 0.22, 0.03, PALETTE.gold, 0, 1.2, 0, 14, metalO),
        ...Array.from({length: 10}, (_, i) => cyl(0.004, 0.004, 0.45, PALETTE.gold, Math.cos(i * 0.628) * 0.21, 1.22, Math.sin(i * 0.628) * 0.21, 4, metalO)), mesh(new THREE.SphereGeometry(0.22, 12, 6, 0, Math.PI * 2, 0, Math.PI / 2), mat(PALETTE.gold, metalO), 0, 1.65, 0),
        ball(0.06, '#F6C945', 0, 1.38, 0, {}, 8), mesh(new THREE.ConeGeometry(0.015, 0.04, 4), mat('#E07B39'), 0, 1.4, 0.07).rotateX(Math.PI / 2)),
    china_cabinet: () => group(...cabinet(0.92, 1.8, 0.4, '#FFFFFF', -0.28), box(0.84, 0.75, 0.38, '#F2EEE6', 0, 0, -0.28), box(0.84, 1.0, 0.02, '#DDF1F7', 0, 0.75, -0.075, {opacity: 0.3}), ...[0.85, 1.2, 1.55].map(y => box(0.84, 0.02, 0.32, '#F2EEE6', 0, y, -0.28)),
        ...Array.from({length: 9}, (_, i) => cyl(0.08, 0.08, 0.02, ['#3E7CB4', '#FFFFFF', '#E2B23A'][i % 3], -0.28 + (i % 3) * 0.28, 0.98 + Math.floor(i / 3) * 0.35, -0.38, 14).rotateX(Math.PI / 2 - 0.2)), box(0.94, 0.06, 0.42, '#E6E0D6', 0, 1.8, -0.28)),
    wine_rack: () => {
        const g = group(box(0.9, 1.6, 0.36, '#6B4026', 0, 0, -0.3));
        for (let r = 0; r < 5; r++) for (let c = 0; c < 5; c++) g.add(cyl(0.04, 0.04, 0.3, ['#2F6F3E', '#7B1E2A', '#5A1E2A', '#E9C46A'][(r + c * 2) % 4], -0.32 + c * 0.16, 0.18 + r * 0.29, -0.18, 8, shinyO).rotateX(Math.PI / 2));
        return g;
    },
    wine_barrels: () => group(...[[-0.22, 0, 0], [0.22, 0, 0], [0, 0.42, 0]].map(([x, y, z]) => group(cyl(0.22, 0.22, 0.6, '#8A5A35', x, y + 0.22, z, 14).rotateX(Math.PI / 2),
        ...[-0.2, 0.2].map(dz => mesh(new THREE.TorusGeometry(0.225, 0.015, 4, 18), mat('#2C2C30'), x, y + 0.22, z + dz))))),
    magazine_rack: () => group(box(0.6, 0.9, 0.3, '#B07A4A', 0, 0, -0.2), ...[0.2, 0.5, 0.8].map((y, i) => box(0.56, 0.22, 0.02, ['#D7263D', '#3E7CB4', '#F6C945'][i], 0, y, -0.03).rotateX(-0.2))),
    record_shelf: () => group(...cabinet(0.92, 0.8, 0.4, '#2C2C30', -0.28), box(0.88, 0.03, 0.38, '#2C2C30', 0, 0.38, -0.28), ...Array.from({length: 18}, (_, i) => box(0.02, 0.3, 0.3, ['#D7263D', '#F6C945', '#3E7CB4', '#FFFFFF', '#2F9E8F', '#E07B39'][i % 6], -0.4 + (i % 9) * 0.1, 0.05 + Math.floor(i / 9) * 0.38, -0.27)),
        cyl(0.15, 0.15, 0.02, '#151515', -0.15, 0.82, -0.28, 18), box(0.3, 0.06, 0.3, '#7B4E2C', 0.25, 0.8, -0.28)),
    flower_cart: () => group(box(1.4, 0.4, 0.7, '#B07A4A', 0, 0.35, 0), ...[-0.5, 0.5].map(x => cyl(0.3, 0.3, 0.06, '#5A3218', x, 0.3, 0.38, 14).rotateX(Math.PI / 2)),
        ...Array.from({length: 14}, (_, i) => ball(0.08, ['#F25F5C', '#FFD23F', '#F7A1C4', '#FFFFFF', '#9B5DE5'][i % 5], -0.55 + (i % 7) * 0.18, 0.85 + (i % 2) * 0.05, -0.15 + Math.floor(i / 7) * 0.3, {}, 6)),
        ...Array.from({length: 14}, (_, i) => leafy(-0.55 + (i % 7) * 0.18, 0.78, -0.15 + Math.floor(i / 7) * 0.3, 0.08, '#4E8A3A')), cyl(0.02, 0.02, 0.8, '#5A3218', 0.75, 0.4, 0, 6).rotateZ(-0.6)),
    xmas_tree: () => group(cyl(0.2, 0.18, 0.25, '#B43E5C', 0, 0, 0, 10), cyl(0.05, 0.05, 0.25, '#5A3218', 0, 0.25, 0, 6), ...[[0.5, 0.6, 0.4], [0.4, 0.55, 0.85], [0.3, 0.45, 1.25]].map(([r, h, y]) => mesh(new THREE.ConeGeometry(r, h, 8), mat('#2F7D3A'), 0, y, 0)),
        mesh(new THREE.OctahedronGeometry(0.1), glowMat('#FFD23F'), 0, 1.6, 0), ...Array.from({length: 12}, (_, i) => ball(0.04, ['#D7263D', '#FFD23F', '#3E7CB4', '#FFFFFF'][i % 4], Math.cos(i * 1.7) * (0.42 - (i % 3) * 0.1), 0.4 + (i % 3) * 0.4, Math.sin(i * 1.7) * (0.42 - (i % 3) * 0.1), shinyO, 6))),
    pumpkins: () => group(...[[-0.15, 0, 0.2], [0.18, 0.05, 0.16], [0, 0.22, 0.12]].map(([x, y, r]) => {
        const p = mesh(new THREE.SphereGeometry(r, 12, 8), mat('#E07B39', {flat: false}), x, y + r * 0.8, 0);
        p.scale.y = 0.75;
        return group(p, cyl(0.015, 0.02, 0.06, '#5A3218', x, y + r * 1.4, 0, 5));
    }), box(0.6, 0.02, 0.4, '#C9A16E', 0, 0, 0)),
    lamp_arc: () => group(box(0.3, 0.06, 0.3, '#2C2C30', 0, 0, 0), mesh(new THREE.TorusGeometry(0.6, 0.02, 5, 20, Math.PI / 1.4), mat('#C9CED4', metalO), 0.25, 1.2, 0).rotateZ(-0.2),
        cyl(0.02, 0.02, 1.2, '#C9CED4', -0.2, 0, 0, 6, metalO), mesh(new THREE.SphereGeometry(0.2, 14, 8, 0, Math.PI * 2, 0, Math.PI / 2), mat('#2C2C30', shinyO), 0.55, 1.55, 0).rotateX(Math.PI), lampHead(0.55, 1.5, 0, 0.08)),
    lamp_tiffany: () => group(box(0.5, 0.75, 0.5, '#7B4E2C', 0, 0, 0), cyl(0.03, 0.06, 0.35, PALETTE.gold, 0, 0.75, 0, 8, metalO), mesh(new THREE.ConeGeometry(0.28, 0.2, 10, 1, true), glowMat('#E9A21D'), 0, 1.22, 0),
        mesh(new THREE.TorusGeometry(0.28, 0.01, 4, 20), mat(PALETTE.gold, metalO), 0, 1.12, 0).rotateX(Math.PI / 2), lampHead(0, 1.15, 0, 0.06)),
    lamp_paper: () => group(cyl(0.12, 0.14, 0.04, '#2C2C30', 0, 0, 0, 10), cyl(0.012, 0.012, 1.0, '#2C2C30', 0, 0, 0, 6), mesh(new THREE.SphereGeometry(0.3, 14, 10), glowMat('#FFF1D6'), 0, 1.25, 0),
        ...[-0.15, 0, 0.15].map(y => mesh(new THREE.TorusGeometry(Math.sqrt(0.09 - y * y), 0.005, 4, 20), mat('#E9DCC4'), 0, 1.25 + y, 0).rotateX(Math.PI / 2))),
    lamp_tripod: () => group(...[0, 1, 2].map(i => {
        const l = cyl(0.015, 0.02, 1.4, '#B07A4A', Math.cos(i * 2.1) * 0.15, 0, Math.sin(i * 2.1) * 0.15, 6);
        l.rotation.set(Math.sin(i * 2.1) * 0.18, 0, -Math.cos(i * 2.1) * 0.18);
        return l;
    }), cyl(0.18, 0.24, 0.32, '#F4E6D0', 0, 1.35, 0, 14, {flat: false}), lampHead(0, 1.38, 0, 0.07)),
    candelabra_floor: () => group(cyl(0.18, 0.2, 0.05, PALETTE.gold, 0, 0, 0, 12, metalO), cyl(0.025, 0.03, 1.3, PALETTE.gold, 0, 0, 0, 8, metalO), ...[-1, 0, 1].map(s => group(box(0.25, 0.02, 0.02, PALETTE.gold, s * 0.12, 1.3, 0, metalO),
        cyl(0.025, 0.025, 0.14, '#FFF7E6', s * 0.24, 1.3, 0, 8), mesh(new THREE.ConeGeometry(0.02, 0.06, 6), glowMat('#FFB347'), s * 0.24, 1.47, 0)))),
    fishbowl: () => group(box(0.4, 0.8, 0.4, '#B07A4A', 0, 0, 0), mesh(new THREE.SphereGeometry(0.22, 16, 12), mat('#7FC4D9', {opacity: 0.4, roughness: 0.05}), 0, 1.0, 0), ball(0.04, '#FF8C42', 0.04, 1.0, 0.02, {}, 6),
        cyl(0.17, 0.2, 0.04, '#E8D9A8', 0, 0.8, 0, 14), leafy(-0.06, 0.88, 0, 0.05, '#4E8A3A')),
    sculpture: () => group(box(0.4, 0.8, 0.4, '#2C2C30', 0, 0, 0), mesh(new THREE.TorusKnotGeometry(0.16, 0.05, 60, 8), mat(PALETTE.gold, metalO), 0, 1.05, 0)),
    piano_grand: () => {
        const top = new THREE.Shape();
        top.moveTo(-0.7, -0.7); top.lineTo(0.7, -0.7); top.lineTo(0.7, 0.3); top.quadraticCurveTo(0.6, 0.9, -0.1, 0.9); top.quadraticCurveTo(-0.7, 0.9, -0.7, 0.2); top.lineTo(-0.7, -0.7);
        const body = mesh(new THREE.ExtrudeGeometry(top, {depth: 0.32, bevelEnabled: false}), mat('#1B1615', {roughness: 0.2}), 0, 0.98, 0);
        body.rotation.x = Math.PI / 2;
        const lid = mesh(new THREE.ExtrudeGeometry(top, {depth: 0.02, bevelEnabled: false}), mat('#1B1615', {roughness: 0.2}), 0, 1.0, -0.2);
        lid.rotation.x = Math.PI / 2 - 0.6;
        return group(body, lid, box(1.36, 0.05, 0.2, '#FFFFFF', 0, 0.72, -0.75), box(1.36, 0.02, 0.08, '#111', 0, 0.77, -0.78), ...[[-0.6, -0.6], [0.6, -0.6], [0, 0.7]].map(([x, z]) => cyl(0.05, 0.04, 0.66, '#1B1615', x, 0, z, 8)),
            box(0.8, 0.06, 0.32, '#1B1615', 0, 0.45, -1.15));
    },

    // ---- rugs
    rug_oval: () => group(mesh(new THREE.CylinderGeometry(0.95, 0.95, 0.03, 32).scale(1, 1, 0.5), mat('#3E7CB4', {flat: false}), 0, 0.034, 0), mesh(new THREE.CylinderGeometry(0.78, 0.78, 0.032, 32).scale(1, 1, 0.42), mat('#E9F1F8', {flat: false}), 0, 0.036, 0),
        ...Array.from({length: 8}, (_, i) => ball(0.05, '#F6C945', Math.cos(i * 0.785) * 0.6, 0.04, Math.sin(i * 0.785) * 0.28, {}, 5))),
    rug_zigzag: () => {
        const g = group(box(1.9, 0.03, 1.9, '#F4E6D0', 0, 0.028, 0));
        for (let i = 0; i < 5; i++) for (let j = 0; j < 8; j++) g.add(box(0.26, 0.032, 0.08, ['#D9653B', '#2F9E8F', '#3B2418'][i % 3], -0.82 + j * 0.24, 0.03, -0.7 + i * 0.35 + (j % 2) * 0.08).rotateY(j % 2 ? 0.5 : -0.5));
        return g;
    },
    rug_sun: () => group(mesh(new THREE.CylinderGeometry(0.95, 0.95, 0.03, 32), mat('#E9C46A', {flat: false}), 0, 0.034, 0), mesh(new THREE.CylinderGeometry(0.5, 0.5, 0.032, 32), mat('#E07B39', {flat: false}), 0, 0.036, 0),
        ...Array.from({length: 12}, (_, i) => box(0.08, 0.034, 0.28, '#E07B39', Math.cos(i * 0.52) * 0.72, 0.037, Math.sin(i * 0.52) * 0.72).rotateY(-i * 0.52 + Math.PI / 2))),
    rug_kilim: () => {
        const g = group(box(1.9, 0.03, 0.9, '#B5302A', 0, 0.028, 0), box(1.7, 0.032, 0.7, '#E9C46A', 0, 0.029, 0));
        for (let i = 0; i < 6; i++) g.add(box(0.16, 0.035, 0.16, i % 2 ? '#2F4B7C' : '#B5302A', -0.7 + i * 0.28, 0.031, 0).rotateY(Math.PI / 4));
        return g;
    },
    rug_green: () => group(mesh(new THREE.CylinderGeometry(0.95, 0.95, 0.04, 28), mat('#7CB342', {flat: false, roughness: 1}), 0, 0.034, 0), ...Array.from({length: 18}, (_, i) => ball(0.05, '#8FC75A', Math.cos(i * 2.4) * Math.sqrt(i / 18) * 0.8, 0.05, Math.sin(i * 2.4) * Math.sqrt(i / 18) * 0.8, {}, 5))),
    rug_star: () => {
        const s = new THREE.Shape();
        for (let i = 0; i < 10; i++) {
            const r = i % 2 ? 0.42 : 0.95, a = i * Math.PI / 5 - Math.PI / 2;
            i ? s.lineTo(Math.cos(a) * r, Math.sin(a) * r) : s.moveTo(Math.cos(a) * r, Math.sin(a) * r);
        }
        const m = mesh(new THREE.ExtrudeGeometry(s, {depth: 0.03, bevelEnabled: false}), mat('#F6C945'), 0, 0.06, 0);
        m.rotation.x = Math.PI / 2;
        return group(m);
    },
    rug_hall: () => group(box(3.9, 0.03, 0.8, '#6B1E3A', 0, 0.028, 0), box(3.7, 0.032, 0.6, '#B43E5C', 0, 0.029, 0), ...Array.from({length: 9}, (_, i) => box(0.12, 0.035, 0.12, PALETTE.gold, -1.6 + i * 0.4, 0.031, 0).rotateY(Math.PI / 4))),
    rug_shag: () => {
        const g = group(box(1.9, 0.04, 1.9, '#F4F1EA', 0, 0.03, 0, {roughness: 1}));
        for (let i = 0; i < 40; i++) g.add(ball(0.06, '#FFFFFF', -0.85 + (i % 8) * 0.24, 0.06, -0.85 + Math.floor(i / 8) * 0.42, {roughness: 1}, 4));
        return g;
    },

    // ---- restroom
    toilet_gold: () => group(box(0.46, 0.5, 0.2, '#F4EAD5', 0, 0.4, -0.33, shinyO), box(0.48, 0.04, 0.22, PALETTE.gold, 0, 0.9, -0.33, metalO), cyl(0.18, 0.14, 0.4, '#F4EAD5', 0, 0, -0.02, 16, shinyO),
        cyl(0.21, 0.21, 0.05, PALETTE.gold, 0, 0.4, 0.02, 18, metalO), box(0.08, 0.03, 0.03, PALETTE.gold, 0.15, 0.8, -0.2, metalO)),
    washbasin_bowl: () => group(box(0.7, 0.8, 0.45, '#7B4E2C', 0, 0, -0.2), box(0.74, 0.04, 0.5, '#F2EEE6', 0, 0.8, -0.2), mesh(new THREE.SphereGeometry(0.17, 16, 8, 0, Math.PI * 2, Math.PI / 2, Math.PI / 2), mat('#FFFFFF', shinyO), 0, 0.98, -0.18),
        cyl(0.015, 0.015, 0.25, '#2C2C30', 0, 0.84, -0.4, 8), box(0.02, 0.02, 0.12, '#2C2C30', 0, 1.08, -0.35), cyl(0.25, 0.25, 0.02, '#CFE3EC', 0, 1.5, -0.42, 18, {metalness: 0.4, roughness: 0.05}).rotateX(Math.PI / 2)),
    towel_rack: () => group(box(0.04, 1.2, 0.04, PALETTE.steel, -0.25, 0, -0.35, metalO), box(0.04, 1.2, 0.04, PALETTE.steel, 0.25, 0, -0.35, metalO), ...[0.7, 1.0].map(y => cyl(0.015, 0.015, 0.5, PALETTE.steel, 0, y, -0.35, 6, metalO).rotateZ(Math.PI / 2)),
        box(0.36, 0.5, 0.03, '#9ED2C0', 0, 0.5, -0.33), box(0.3, 0.4, 0.03, '#F7A1C4', 0, 0.62, -0.37)),
    baby_change: () => group(box(0.8, 0.85, 0.5, '#FFFFFF', 0, 0, -0.15, shinyO), box(0.76, 0.06, 0.46, '#9ED2C0', 0, 0.85, -0.15, {flat: false}), box(0.8, 0.12, 0.04, '#FFFFFF', 0, 0.9, -0.38), ...[-0.2, 0.2].map(x => box(0.3, 0.3, 0.02, '#F6C945', x, 0.3, 0.1))),
    mirror_led: () => group(box(0.7, 0.9, 0.4, '#F2EEE6', 0, 0, -0.25), box(0.74, 0.04, 0.44, '#2C2C30', 0, 0.9, -0.25), mesh(new THREE.TorusGeometry(0.3, 0.02, 6, 28), glowMat('#FFF4DB'), 0, 1.5, -0.44),
        cyl(0.29, 0.29, 0.01, '#CFE3EC', 0, 1.5, -0.45, 24, {metalness: 0.4, roughness: 0.05}).rotateX(Math.PI / 2), cyl(0.03, 0.03, 0.12, '#FFFFFF', 0.25, 0.94, -0.3, 8)),
    soap_station: () => group(box(0.04, 1.2, 0.04, PALETTE.steel, 0, 0, -0.35, metalO), box(0.18, 0.3, 0.12, '#FFFFFF', 0, 1.0, -0.3, shinyO), box(0.1, 0.12, 0.02, '#9ED2C0', 0, 1.1, -0.23), box(0.3, 0.06, 0.3, PALETTE.steel, 0, 0, -0.35, metalO)),

    // ---- walls and screens
    wall_stone_block: () => {
        const g = group();
        for (let r = 0; r < 7; r++) for (let c = 0; c < 3; c++) g.add(box(0.31, 0.25, 0.3, ['#9A9488', '#B5AFA3', '#A8A296'][(r + c) % 3], -0.33 + c * 0.33 + (r % 2) * 0.05 - 0.02, r * 0.26, 0));
        return g;
    },
    wall_glass_brick: () => {
        const g = group(box(0.98, 0.08, 0.2, '#E6E0D6', 0, 0, 0));
        for (let r = 0; r < 8; r++) for (let c = 0; c < 4; c++) g.add(box(0.23, 0.2, 0.16, '#BFE3F0', -0.36 + c * 0.24, 0.1 + r * 0.21, 0, {opacity: 0.55, roughness: 0.05}));
        return g;
    },
    lattice_screen: () => {
        const g = group(box(0.98, 0.1, 0.14, '#7B4E2C', 0, 0, 0), box(0.98, 0.06, 0.1, '#7B4E2C', 0, 1.8, 0), box(0.06, 1.86, 0.1, '#7B4E2C', -0.46, 0, 0), box(0.06, 1.86, 0.1, '#7B4E2C', 0.46, 0, 0));
        for (let i = 1; i < 6; i++) g.add(box(0.03, 1.7, 0.03, '#B07A4A', -0.46 + i * 0.153, 0.1, 0));
        for (let i = 1; i < 10; i++) g.add(box(0.9, 0.03, 0.03, '#B07A4A', 0, 0.1 + i * 0.17, 0.01));
        g.add(...Array.from({length: 14}, (_, i) => leafy(-0.38 + (i % 5) * 0.19, 0.25 + Math.floor(i / 5) * 0.55 + (i % 2) * 0.15, 0.05, 0.09, i % 2 ? '#3F7A2E' : '#5E9A3E')));
        return g;
    },
    curtain_red: () => group(box(1.0, 0.06, 0.06, PALETTE.gold, 0, 2.0, 0, metalO), ...Array.from({length: 7}, (_, i) => cyl(0.08, 0.1, 2.0, '#B5302A', -0.42 + i * 0.14, 0, (i % 2) * 0.04, 8, {flat: false})),
        mesh(new THREE.TorusGeometry(0.15, 0.03, 5, 12), mat(PALETTE.gold, metalO), -0.42, 1.0, 0.08)),
    bookshelf_divider: () => group(box(0.06, 1.6, 0.4, '#8A5A35', -0.92, 0, 0), box(0.06, 1.6, 0.4, '#8A5A35', 0.92, 0, 0), box(1.9, 0.05, 0.4, '#8A5A35', 0, 1.55, 0), ...[0.05, 0.42, 0.8, 1.18].map(y => box(1.84, 0.03, 0.38, '#B07A4A', 0, y, 0)),
        ...[0.08, 0.45, 0.83, 1.21].flatMap(y => booksRow(y).map(b => (b.position.x *= 2.2, b.position.z = 0, b.scale.x = 2.2, b))), box(0.04, 1.6, 0.4, '#6B4026', 0, 0, 0)),
    screen_shoji: () => group(...[-0.32, 0, 0.32].map((x, i) => {
        const p = group(box(0.3, 1.75, 0.04, '#5A3218', 0, 0, 0), box(0.25, 1.6, 0.02, '#FFF7E6', 0, 0.08, 0.01));
        for (let k = 0; k < 4; k++) p.add(box(0.26, 0.015, 0.03, '#5A3218', 0, 0.4 + k * 0.38, 0.01));
        p.add(box(0.015, 1.6, 0.03, '#5A3218', 0, 0.08, 0.01));
        p.position.x = x;
        p.rotation.y = i === 1 ? 0 : (i === 0 ? 0.3 : -0.3);
        return p;
    })),
    wall_ivy: () => {
        const g = group(box(0.98, 1.9, 0.2, '#7B6A55', 0, 0, 0));
        for (let i = 0; i < 40; i++) g.add(leafy(-0.44 + (i % 8) * 0.125, 0.15 + Math.floor(i / 8) * 0.38 + (i % 2) * 0.1, (i % 2 ? 0.1 : -0.1), 0.1, i % 3 ? '#3F7A2E' : '#5E9A3E'));
        for (let i = 0; i < 6; i++) g.add(ball(0.04, ['#FFFFFF', '#F7A1C4', '#FFD23F'][i % 3], -0.35 + i * 0.14, 0.4 + (i % 3) * 0.5, 0.13, {}, 5));
        return g;
    },
    half_wall_wood: () => group(box(1.96, 1.0, 0.18, '#B07A4A', 0, 0, 0), box(2.0, 0.06, 0.24, '#7B4E2C', 0, 1.0, 0), ...Array.from({length: 10}, (_, i) => box(0.01, 0.96, 0.19, '#8A5A35', -0.9 + i * 0.2, 0.02, 0))),
    rope_barrier: () => group(...[-0.42, 0.42].map(x => group(cyl(0.12, 0.14, 0.04, PALETTE.gold, x, 0, 0, 12, metalO), cyl(0.025, 0.025, 0.9, PALETTE.gold, x, 0, 0, 8, metalO), ball(0.04, PALETTE.gold, x, 0.92, 0, metalO, 8))),
        mesh(new THREE.TorusGeometry(0.42, 0.025, 6, 16, Math.PI), mat('#B5302A', {flat: false}), 0, 0.85, 0).rotateZ(Math.PI)),
    arch_flowers: () => group(...[-0.45, 0.45].map(x => box(0.08, 1.8, 0.08, '#FFFFFF', x, 0, 0)), mesh(new THREE.TorusGeometry(0.45, 0.04, 6, 16, Math.PI), mat('#FFFFFF'), 0, 1.8, 0),
        ...Array.from({length: 16}, (_, i) => ball(0.08, ['#F7A1C4', '#FFFFFF', '#F25F5C'][i % 3], Math.cos(i / 15 * Math.PI) * 0.45, 1.8 + Math.sin(i / 15 * Math.PI) * 0.45, 0.04, {}, 6)),
        ...Array.from({length: 10}, (_, i) => leafy((i % 2 ? 0.45 : -0.45), 0.3 + i * 0.15, 0.05, 0.09, '#4E8A3A'))),

    // ---- outdoor: lamps (they light the ground at night)
    lamp_lantern_post: () => group(box(0.12, 2.2, 0.12, '#2F3A3A', 0, 0, 0), box(0.3, 0.1, 0.3, '#2F3A3A', 0, 0, 0), box(0.26, 0.36, 0.26, '#2F3A3A', 0, 2.2, 0), glow(new THREE.BoxGeometry(0.2, 0.28, 0.2), '#FFE7A8', 0, 2.38, 0),
        mesh(new THREE.ConeGeometry(0.24, 0.18, 4), mat('#2F3A3A'), 0, 2.65, 0).rotateY(Math.PI / 4), lightPool(1.8)),
    lamp_double: () => group(box(0.1, 2.6, 0.1, '#2F3A3A', 0, 0, 0), box(0.3, 0.12, 0.3, '#2F3A3A', 0, 0, 0), box(0.9, 0.05, 0.05, '#2F3A3A', 0, 2.5, 0), ...[-0.45, 0.45].map(x => group(lampHead(x, 2.35, 0, 0.14), mesh(new THREE.ConeGeometry(0.18, 0.12, 8), mat('#2F3A3A'), x, 2.52, 0))),
        lightPool(2.2)),
    lamp_bollard: () => group(cyl(0.1, 0.12, 0.6, '#2C2C30', 0, 0, 0, 10, metalO), glow(new THREE.CylinderGeometry(0.09, 0.09, 0.12, 10), '#FFE7A8', 0, 0.45, 0), cyl(0.12, 0.12, 0.04, '#2C2C30', 0, 0.6, 0, 10), lightPool(0.9)),
    lamp_string: () => group(...[-0.95, 0.95].map(x => box(0.08, 2.2, 0.08, '#7B4E2C', x, 0, 0)), ...Array.from({length: 9}, (_, i) => {
        const x = -0.85 + i * 0.21, y = 2.05 - Math.sin(i / 8 * Math.PI) * 0.25;
        return lampHead(x, y, 0, 0.05, ['#FFE7A8', '#F7A1C4', '#9FE0F7'][i % 3]);
    }), ...Array.from({length: 8}, (_, i) => cyl(0.005, 0.005, 0.22, '#2C2C30', -0.75 + i * 0.21, 2.0 - Math.sin((i + 0.5) / 8 * Math.PI) * 0.25, 0, 3).rotateZ(Math.PI / 2)), lightPool(1.6, -0.5), lightPool(1.6, 0.5)),
    lamp_torch: () => group(cyl(0.03, 0.04, 1.5, '#9C6B3F', 0, 0, 0, 6), cyl(0.07, 0.05, 0.18, '#5A3218', 0, 1.5, 0, 8), ...(() => {
        const f = fire(0, 1.68, 0, 0.35);
        f.children.filter(c => c.isPointLight).forEach(l => f.remove(l));
        return [f];
    })(), lightPool(1.2)),
    lamp_modern: () => group(box(0.1, 2.8, 0.1, '#5D6D7E', 0, 0, 0, metalO), box(0.7, 0.08, 0.2, '#5D6D7E', 0.3, 2.8, 0, metalO), glow(new THREE.BoxGeometry(0.5, 0.02, 0.14), '#F4FBFF', 0.35, 2.79, 0), lightPool(2.0, 0.35)),
    lamp_victorian: () => group(cyl(0.18, 0.22, 0.3, '#1E2B2B', 0, 0, 0, 8), cyl(0.05, 0.07, 2.4, '#1E2B2B', 0, 0.3, 0, 8), ...[-1, 1].map(s => mesh(new THREE.TorusGeometry(0.2, 0.02, 4, 10, Math.PI), mat('#1E2B2B'), s * 0.2, 2.6, 0)),
        ...[-0.4, 0.4].map(x => group(glow(new THREE.CylinderGeometry(0.1, 0.08, 0.26, 6), '#FFE7A8', x, 2.38, 0), mesh(new THREE.ConeGeometry(0.15, 0.14, 6), mat('#1E2B2B'), x, 2.58, 0))), lightPool(2.3)),
    heater_patio: () => group(cyl(0.22, 0.25, 0.06, '#5D6D7E', 0, 0, 0, 12, metalO), cyl(0.04, 0.06, 2.0, '#5D6D7E', 0, 0, 0, 8, metalO), glow(new THREE.CylinderGeometry(0.1, 0.12, 0.3, 10), '#FF7A3C', 0, 2.0, 0),
        mesh(new THREE.ConeGeometry(0.45, 0.2, 14), mat('#5D6D7E', metalO), 0, 2.4, 0), lightPool(1.4)),
    // ---- outdoor: garden
    flower_bed: () => group(box(1.9, 0.25, 0.9, '#9C6B3F', 0, 0, 0), box(1.82, 0.05, 0.82, '#6B4A33', 0, 0.22, 0), ...Array.from({length: 24}, (_, i) => group(cyl(0.008, 0.008, 0.2, '#3F7A2E', -0.8 + (i % 8) * 0.23, 0.25, -0.3 + Math.floor(i / 8) * 0.3, 4),
        ball(0.06, ['#F25F5C', '#FFD23F', '#F7A1C4', '#FFFFFF', '#9B5DE5'][i % 5], -0.8 + (i % 8) * 0.23, 0.48, -0.3 + Math.floor(i / 8) * 0.3, {}, 5)))),
    pond: () => group(mesh(new THREE.CylinderGeometry(0.9, 0.95, 0.12, 20), mat('#9A9488'), 0, 0, 0).translateY(0.06), mesh(new THREE.CylinderGeometry(0.8, 0.8, 0.02, 20), mat(PALETTE.water, {opacity: 0.8, roughness: 0.05}), 0, 0.11, 0),
        ...[[0.3, 0.2], [-0.35, -0.1], [0.05, -0.4]].map(([x, z]) => cyl(0.12, 0.12, 0.01, '#5E9A3E', x, 0.125, z, 10)), ball(0.05, '#F7A1C4', 0.3, 0.16, 0.2, {}, 6), ...[[0.2, -0.1, '#FF8C42'], [-0.2, 0.25, '#FFFFFF']].map(([x, z, c]) => ball(0.05, c, x, 0.1, z, {}, 6)),
        ...Array.from({length: 10}, (_, i) => ball(0.1, '#B5AFA3', Math.cos(i * 0.628) * 0.92, 0.1, Math.sin(i * 0.628) * 0.92, {}, 5))),
    swing_garden: () => group(...[-0.9, 0.9].map(x => group(box(0.08, 2.0, 0.08, '#9C6B3F', x, 0, -0.3).rotateX(0.15), box(0.08, 2.0, 0.08, '#9C6B3F', x, 0, 0.3).rotateX(-0.15))), box(1.9, 0.1, 0.1, '#9C6B3F', 0, 1.95, 0),
        ...[-0.4, 0.4].map(x => cyl(0.008, 0.008, 1.3, '#C9B07A', x, 0.65, 0, 4)), box(1.0, 0.06, 0.4, '#B07A4A', 0, 0.6, 0), box(1.0, 0.4, 0.05, '#B07A4A', 0, 0.66, -0.18), box(0.9, 0.08, 0.34, '#F7A1C4', 0, 0.66, 0.02, {flat: false})),
    bird_bath: () => group(cyl(0.15, 0.2, 0.08, '#E6E0D6', 0, 0, 0, 12), cyl(0.06, 0.08, 0.7, '#E6E0D6', 0, 0.08, 0, 10), cyl(0.3, 0.12, 0.12, '#E6E0D6', 0, 0.78, 0, 14), cyl(0.26, 0.26, 0.02, PALETTE.water, 0, 0.88, 0, 14, {opacity: 0.8}),
        ball(0.05, '#3E7CB4', 0.18, 0.95, 0, {}, 6), mesh(new THREE.ConeGeometry(0.012, 0.04, 4), mat('#F6C945'), 0.23, 0.96, 0).rotateZ(-Math.PI / 2)),
    statue_garden: () => group(box(0.5, 0.4, 0.5, '#B5AFA3', 0, 0, 0), cyl(0.12, 0.16, 0.6, '#D6CFC2', 0, 0.4, 0, 10), ball(0.12, '#D6CFC2', 0, 1.12, 0, {}, 10), ...[-1, 1].map(s => box(0.06, 0.35, 0.06, '#D6CFC2', s * 0.15, 0.75, 0).rotateZ(s * -0.5)),
        cyl(0.1, 0.06, 0.12, '#D6CFC2', 0.28, 0.98, 0, 8)),
    wheelbarrow: () => group(box(0.7, 0.3, 0.5, '#D7263D', 0.05, 0.3, 0), cyl(0.15, 0.15, 0.06, '#2C2C30', 0.45, 0.15, 0, 12).rotateX(Math.PI / 2), ...[-0.2, 0.2].map(z => box(0.6, 0.04, 0.04, '#5A3218', -0.45, 0.45, z)),
        ...Array.from({length: 10}, (_, i) => ball(0.08, ['#F25F5C', '#FFD23F', '#F7A1C4', '#FFFFFF'][i % 4], -0.15 + (i % 5) * 0.1, 0.65, -0.12 + Math.floor(i / 5) * 0.24, {}, 5)), ...Array.from({length: 6}, (_, i) => leafy(-0.15 + i * 0.08, 0.6, 0, 0.09, '#4E8A3A'))),
    pergola: () => group(...[[-0.9, -0.9], [0.9, -0.9], [-0.9, 0.9], [0.9, 0.9]].map(([x, z]) => box(0.12, 2.3, 0.12, '#F4F1E8', x, 0, z)), ...[-0.9, 0.9].map(z => box(2.0, 0.1, 0.1, '#F4F1E8', 0, 2.3, z)),
        ...Array.from({length: 7}, (_, i) => box(0.06, 0.08, 2.1, '#F4F1E8', -0.9 + i * 0.3, 2.4, 0)), ...Array.from({length: 14}, (_, i) => leafy(-0.9 + (i % 7) * 0.3, 2.45, -0.6 + Math.floor(i / 7) * 1.2, 0.18, i % 2 ? '#4E8A3A' : '#6FAF45')),
        ...Array.from({length: 8}, (_, i) => ball(0.08, '#9B7BC8', -0.8 + i * 0.23, 2.2, (i % 2 ? 0.7 : -0.7), {}, 5))),
    rock_garden: () => group(box(1.9, 0.04, 0.9, '#E6DDCB', 0, 0, 0), ...[[-0.5, 0.15, 0.22], [0.35, -0.1, 0.18], [0.7, 0.2, 0.12]].map(([x, z, r]) => mesh(new THREE.DodecahedronGeometry(r, 0), mat('#8E8A82'), x, r * 0.6, z)),
        ...Array.from({length: 5}, (_, i) => mesh(new THREE.TorusGeometry(0.3 + i * 0.12, 0.008, 3, 24, Math.PI), mat('#C9BFA9'), -0.5, 0.045, 0.15).rotateX(-Math.PI / 2)), leafy(0.1, 0.2, 0.25, 0.15, '#3F7A2E')),
    bamboo_clump: () => group(...Array.from({length: 9}, (_, i) => cyl(0.04, 0.04, 2.0 + (i % 3) * 0.4, i % 2 ? '#8FB84A' : '#A4C95A', Math.cos(i * 2.4) * 0.2, 0, Math.sin(i * 2.4) * 0.2, 6)),
        ...Array.from({length: 14}, (_, i) => leafy(Math.cos(i * 1.3) * 0.3, 1.6 + (i % 5) * 0.2, Math.sin(i * 1.3) * 0.3, 0.14, '#6FAF45'))),
    tree_maple: () => group(cyl(0.12, 0.16, 1.3, PALETTE.woodDark, 0, 0, 0, 7), ...[[0, 1.8, 0, 0.75], [0.4, 1.5, 0.2, 0.5], [-0.35, 1.55, -0.2, 0.55], [0, 2.3, 0, 0.5]].map(([x, y, z, r], i) => leafy(x, y, z, r, i % 2 ? '#C8452C' : '#E07B39'))),
    tree_birch: () => group(cyl(0.08, 0.1, 2.0, '#F4F1EA', 0, 0, 0, 7), ...[0.5, 0.9, 1.3, 1.7].map(y => box(0.14, 0.03, 0.17, '#2C2C30', 0, y, 0)), ...[[0, 2.1, 0, 0.5], [0.25, 1.8, 0.1, 0.35], [-0.2, 1.9, -0.1, 0.38]].map(([x, y, z, r], i) => leafy(x, y, z, r, i % 2 ? '#9CCB5A' : '#B5D86E'))),
    tree_palm: () => group(...Array.from({length: 6}, (_, i) => cyl(0.1 - i * 0.008, 0.11 - i * 0.008, 0.42, i % 2 ? '#9C6B3F' : '#B07A4A', i * 0.03, i * 0.42, 0, 8)),
        ...Array.from({length: 7}, (_, i) => {
            const l = mesh(new THREE.ConeGeometry(0.16, 1.2, 4), mat(i % 2 ? '#3F7A2E' : '#5E9A3E'), 0.18 + Math.cos(i * 0.9) * 0.45, 2.45, Math.sin(i * 0.9) * 0.45);
            l.rotation.set(Math.sin(i * 0.9) * 1.3, 0, -Math.cos(i * 0.9) * 1.3);
            l.scale.z = 0.25;
            return l;
        }), ...[0, 1, 2].map(i => ball(0.08, '#7B5A2C', 0.18 + Math.cos(i * 2) * 0.1, 2.38, Math.sin(i * 2) * 0.1, {}, 6))),
    bush_hydrangea: () => group(leafy(0, 0.4, 0, 0.45, '#4E8A3A'), ...Array.from({length: 9}, (_, i) => ball(0.13, i % 2 ? '#9CB8F0' : '#C7A6E8', Math.cos(i * 0.7) * 0.32, 0.45 + (i % 3) * 0.15, Math.sin(i * 0.7) * 0.32, {}, 6))),
    topiary: () => group(...pot(0, 0, 0.24, 0.4, '#B9A58C'), cyl(0.03, 0.03, 0.4, '#5A3218', 0, 0.4, 0, 6), ball(0.3, '#3F7A2E', 0, 0.95, 0, {}, 10), ball(0.22, '#3F7A2E', 0, 1.42, 0, {}, 10), ball(0.14, '#3F7A2E', 0, 1.78, 0, {}, 8)),
    mailbox: () => group(box(0.06, 1.0, 0.06, '#5A3218', 0, 0, 0), box(0.3, 0.22, 0.45, '#D7263D', 0, 1.0, 0, shinyO), mesh(new THREE.CylinderGeometry(0.15, 0.15, 0.45, 12, 1, false, 0, Math.PI), mat('#D7263D', shinyO), 0, 1.22, 0).rotateX(Math.PI / 2).rotateZ(Math.PI / 2),
        box(0.02, 0.2, 0.06, '#F6C945', 0.16, 1.15, 0.1)),
    bicycle: () => group(...[-0.45, 0.45].map(z => mesh(new THREE.TorusGeometry(0.3, 0.025, 6, 20), mat('#2C2C30'), 0, 0.32, z).rotateY(Math.PI / 2)), box(0.04, 0.04, 0.7, '#2F9E8F', 0, 0.55, 0, metalO), box(0.04, 0.4, 0.04, '#2F9E8F', 0, 0.35, -0.1, metalO),
        box(0.12, 0.04, 0.2, '#3B2418', 0, 0.78, -0.12), box(0.4, 0.03, 0.03, '#2C2C30', 0, 0.85, 0.4), cyl(0.12, 0.1, 0.12, '#B07A4A', 0, 0.75, 0.52, 10)),
    slide_kids: () => group(box(0.6, 1.4, 0.6, '#F6C945', 0, 0, -0.6), box(0.5, 0.05, 1.5, '#D7263D', 0, 0.75, 0.3, shinyO).rotateX(0.75), ...[-0.25, 0.25].map(x => box(0.04, 1.6, 0.04, '#3E7CB4', x, 0, -0.95)),
        ...[0.4, 0.8, 1.2].map(y => box(0.5, 0.04, 0.04, '#3E7CB4', 0, y, -0.95)), mesh(new THREE.ConeGeometry(0.5, 0.4, 4), mat('#3E7CB4'), 0, 1.6, -0.6).rotateY(Math.PI / 4)),
    sandbox: () => group(box(1.9, 0.25, 0.9, '#B07A4A', 0, 0, 0), box(1.8, 0.04, 0.8, '#F2DDAA', 0, 0.2, 0), mesh(new THREE.ConeGeometry(0.2, 0.2, 10), mat('#E9D08F'), -0.4, 0.32, 0), cyl(0.08, 0.06, 0.12, '#D7263D', 0.4, 0.24, 0.1, 10), box(0.06, 0.02, 0.2, '#3E7CB4', 0.2, 0.26, -0.15)),
    bench_stone: () => group(box(1.7, 0.12, 0.45, '#B5AFA3', 0, 0.38, 0), ...[-0.65, 0.65].map(x => box(0.25, 0.38, 0.4, '#9A9488', x, 0, 0)), leafy(-0.95, 0.15, 0.1, 0.12, '#4E8A3A')),
    parasol_big: () => group(cyl(0.3, 0.32, 0.06, '#2C2C30', 0, 0, 0, 12), cyl(0.03, 0.03, 2.2, '#F4F1E8', 0, 0, 0, 8), mesh(new THREE.ConeGeometry(1.0, 0.35, 8), mat('#2F9E8F'), 0, 2.25, 0),
        ...Array.from({length: 8}, (_, i) => box(0.04, 0.05, 0.6, '#FFFFFF', Math.cos(i * 0.785) * 0.7, 2.08, Math.sin(i * 0.785) * 0.7).rotateY(-i * 0.785 + Math.PI / 2)), ...roundTable('#FFFFFF', '#2C2C30', 0.45, 0.7)),
    fountain_wall: () => group(box(1.0, 1.6, 0.3, '#B5AFA3', 0, 0, -0.3), box(0.9, 0.3, 0.5, '#9A9488', 0, 0, -0.1), box(0.8, 0.02, 0.4, PALETTE.water, 0, 0.28, -0.1, {opacity: 0.8}),
        mesh(new THREE.SphereGeometry(0.12, 10, 8), mat('#9A9488'), 0, 1.1, -0.12), box(0.06, 0.8, 0.02, PALETTE.water, 0, 0.3, -0.06, {opacity: 0.6})),
    beehive: () => group(box(0.5, 0.5, 0.5, '#F2EEE6', 0, 0.3, 0), ...[0.35, 0.55, 0.75].map(y => box(0.52, 0.02, 0.52, '#E9C46A', 0, y, 0)), mesh(new THREE.ConeGeometry(0.42, 0.25, 4), mat('#B07A4A'), 0, 0.92, 0).rotateY(Math.PI / 4),
        ...legs(0.4, 0.4, 0.3, '#5A3218', 0.03), ...Array.from({length: 4}, (_, i) => ball(0.025, '#F6C945', 0.3 + i * 0.05, 0.7 + (i % 2) * 0.1, 0.3, {}, 4))),
});

// ---------------------------------------------------------------- stairs and lifts (one storey is 2.6 high)

const STOREY = 2.6;
/** Straight stairs, 1 × 2 tiles: you step on at the front (+z) and come out on the floor above, behind the back end. */
function straightStairs(step, side, rail) {
    const g = group();
    const n = 11, run = 2 / n;
    for (let i = 0; i < n; i++) g.add(box(0.86, 0.06, run + 0.02, step, 0, (i + 1) * STOREY / n - 0.06, 1 - (i + 0.5) * run));
    for (let i = 0; i < n; i++) g.add(box(0.84, (i + 1) * STOREY / n - 0.06, 0.03, side, 0, 0, 1 - (i + 1) * run + 0.015)); // risers
    for (const sx of [-1, 1]) {
        const s = box(0.06, 0.25, 2.5, side, sx * 0.46, 0, 0);
        s.position.y = STOREY / 2;
        s.rotation.x = Math.atan2(STOREY, 2);
        g.add(s);
        const r = box(0.04, 0.04, 2.55, rail, sx * 0.46, 0, 0);
        r.position.y = STOREY / 2 + 0.9;
        r.rotation.x = Math.atan2(STOREY, 2);
        g.add(r);
        for (let i = 0; i < 5; i++) g.add(box(0.025, 0.9, 0.025, rail, sx * 0.46, (i + 0.5) * STOREY / 5, 1 - (i + 0.5) * 0.4));
    }
    return g;
}
Object.assign(FURNITURE, {
    stairs_wood: () => straightStairs('#B07A4A', '#8A5A35', '#5A3218'),
    stairs_stone: () => straightStairs('#D6CFC2', '#B5AFA3', '#2C2C30'),
    stairs_spiral: () => {
        const g = group(cyl(0.05, 0.05, STOREY + 0.9, '#2C2C30', 0, 0, 0, 10, {metalness: 0.6, roughness: 0.3}));
        const n = 12;
        for (let i = 0; i < n; i++) {
            const st = mesh(new THREE.CylinderGeometry(0.48, 0.48, 0.05, 12, 1, false, 0, Math.PI / 3), mat('#B8BEC6', {metalness: 0.5, roughness: 0.35}), 0, (i + 1) * STOREY / n, 0);
            st.rotation.y = Math.PI / 2 + i * (Math.PI * 2 / n) * 0.75;
            g.add(st, cyl(0.012, 0.012, 0.9, '#2C2C30', Math.cos(-st.rotation.y - Math.PI / 6) * 0.45, (i + 1) * STOREY / n, Math.sin(-st.rotation.y - Math.PI / 6) * 0.45, 5));
        }
        return g;
    },
    lift_glass: () => {
        const glassM = {opacity: 0.35, roughness: 0.05};
        return group(box(0.94, 0.06, 0.94, '#5D6D7E', 0, 0, 0, {metalness: 0.5}), box(0.94, 0.08, 0.94, '#5D6D7E', 0, 2.38, 0, {metalness: 0.5}),
            ...[[-0.45, -0.45], [0.45, -0.45], [-0.45, 0.45], [0.45, 0.45]].map(([x, z]) => box(0.06, 2.4, 0.06, '#5D6D7E', x, 0, z, {metalness: 0.5})),
            box(0.88, 2.3, 0.02, '#CFE8F2', 0, 0.06, -0.45, glassM), box(0.02, 2.3, 0.88, '#CFE8F2', -0.45, 0.06, 0, glassM), box(0.02, 2.3, 0.88, '#CFE8F2', 0.45, 0.06, 0, glassM),
            box(0.42, 2.2, 0.03, '#B8BEC6', -0.21, 0.08, 0.46, {metalness: 0.6, roughness: 0.25}), box(0.42, 2.2, 0.03, '#B8BEC6', 0.21, 0.08, 0.46, {metalness: 0.6, roughness: 0.25}),
            glow(new THREE.BoxGeometry(0.16, 0.08, 0.02), '#7FD1F0', 0, 2.22, 0.48), box(0.06, 0.14, 0.02, '#2C2C30', 0.5, 1.1, 0.46), ball(0.015, '#FFD23F', 0.5, 1.13, 0.475, {emissive: '#FFD23F', glow: 1}, 6));
    },
});

// ---------------------------------------------------------------- seasonal pieces (Valentine, Easter, summer, autumn, Christmas)

/** A flat heart (extruded), facing +z, standing on its tip at y = 0. */
function heart(size, color, depth = 0.05, o = {flat: false}) {
    const s = new THREE.Shape();
    s.moveTo(0, 0);
    s.bezierCurveTo(-0.5, 0.35, -0.55, 0.85, -0.25, 0.95);
    s.bezierCurveTo(-0.08, 1.0, 0, 0.88, 0, 0.78);
    s.bezierCurveTo(0, 0.88, 0.08, 1.0, 0.25, 0.95);
    s.bezierCurveTo(0.55, 0.85, 0.5, 0.35, 0, 0);
    const g = new THREE.ExtrudeGeometry(s, {depth, bevelEnabled: true, bevelSize: depth * 0.4, bevelThickness: depth * 0.4, bevelSegments: 2});
    g.translate(0, 0, -depth / 2);
    g.scale(size, size, size);
    return mesh(g, mat(color, o));
}
const egg = (x, y, z, r, c, stripe = '#FFFFFF') => {
    const e = ball(r, c, x, y, z, {flat: false}, 12);
    e.scale.y = 1.3;
    const band = mesh(new THREE.TorusGeometry(r * 0.98, r * 0.14, 5, 18), mat(stripe), x, y, z);
    band.rotation.x = Math.PI / 2;
    return [e, band];
};
const softO = {flat: false};
const capsuleMesh = (r, len, color, x, y, z) => mesh(new THREE.CapsuleGeometry(r, len, 4, 10), mat(color, softO), x, y, z);
Object.assign(FURNITURE, {
    heart_arch: () => group(...[-0.42, 0.42].map(x => cyl(0.04, 0.04, 1.6, '#FFFFFF', x, 0, 0, 8)), mesh(new THREE.TorusGeometry(0.42, 0.04, 6, 18, Math.PI), mat('#F7A1C4'), 0, 1.6, 0),
        ...Array.from({length: 9}, (_, i) => heart(0.16, i % 2 ? '#E63946' : '#F7A1C4').translateX(Math.cos(i / 8 * Math.PI) * 0.42).translateY(1.55 + Math.sin(i / 8 * Math.PI) * 0.42)),
        heart(0.45, '#E63946', 0.08).translateY(0.9), box(0.5, 0.06, 0.3, '#F2EEE6', 0, 0, 0)),
    rose_vase: () => group(cyl(0.18, 0.2, 0.7, '#F2EEE6', 0, 0, 0, 14), cyl(0.1, 0.08, 0.3, '#DDF1F7', 0, 0.7, 0, 12, {opacity: 0.5}),
        ...Array.from({length: 9}, (_, i) => group(cyl(0.006, 0.006, 0.35, '#3F7A2E', Math.cos(i * 0.7) * 0.06, 0.85, Math.sin(i * 0.7) * 0.06, 4), ball(0.045, i % 3 ? '#D7263D' : '#F7A1C4', Math.cos(i * 0.7) * 0.09, 1.22 + (i % 2) * 0.04, Math.sin(i * 0.7) * 0.09, softO, 8)))),
    heart_balloons: () => group(cyl(0.08, 0.1, 0.1, '#B43E5C', 0, 0, 0, 10), ...[[-0.15, 1.5, '#E63946'], [0.12, 1.65, '#F7A1C4'], [0.02, 1.85, '#D7263D']].flatMap(([x, y, c]) => [
        heart(0.28, c, 0.12).translateX(x).translateY(y - 0.14), cyl(0.004, 0.004, y - 0.1, '#FFFFFF', x / 2, 0.1, 0, 3)])),
    cupid_statue: () => group(box(0.42, 0.7, 0.42, '#F2EEE6', 0, 0, 0), box(0.48, 0.06, 0.48, '#E6E0D6', 0, 0.7, 0), ball(0.14, '#F6D7BE', 0, 0.92, 0, softO, 12), ball(0.1, '#F6D7BE', 0, 1.13, 0.02, softO, 10),
        ball(0.06, '#E8C07D', 0, 1.2, -0.01, softO, 8), ...[-1, 1].map(sx => {
            const w = ball(0.1, '#FFFFFF', sx * 0.13, 1.0, -0.08, softO, 8);
            w.scale.set(0.5, 1.2, 0.3);
            return w;
        }), mesh(new THREE.TorusGeometry(0.12, 0.012, 5, 12, Math.PI), mat(PALETTE.gold), 0.16, 0.95, 0.08).rotateZ(Math.PI / 2), heart(0.08, '#E63946').translateY(0.92).translateZ(0.15)),
    rug_heart: () => {
        const m = heart(1.9, '#F7A1C4', 0.02);
        m.rotation.x = -Math.PI / 2;
        m.position.set(0, 0.04, 0.9);
        const inner = heart(1.4, '#E63946', 0.022);
        inner.rotation.x = -Math.PI / 2;
        inner.position.set(0, 0.045, 0.65);
        return group(m, inner);
    },
    easter_egg_big: () => group(cyl(0.22, 0.26, 0.12, PALETTE.gold, 0, 0, 0, 14, {metalness: 0.5}), ...egg(0, 0.62, 0, 0.36, '#9ED2C0', '#F7A1C4'),
        mesh(new THREE.TorusGeometry(0.3, 0.035, 5, 18), mat('#FFD23F'), 0, 0.82, 0).rotateX(Math.PI / 2), ...Array.from({length: 6}, (_, i) => ball(0.04, ['#E63946', '#3E7CB4', '#FFFFFF'][i % 3], Math.cos(i) * 0.36, 0.45 + (i % 2) * 0.05, Math.sin(i) * 0.36, {}, 6))),
    bunny_statue: () => {
        const body = ball(0.22, '#F4F1EA', 0, 0.25, 0, softO, 14);
        body.scale.set(1, 1.1, 1);
        return group(body, ball(0.15, '#F4F1EA', 0, 0.58, 0.05, softO, 12), ...[-1, 1].map(sx => {
            const ear = capsuleMesh(0.045, 0.22, '#F4F1EA', sx * 0.06, 0.82, 0.02);
            ear.rotation.z = sx * -0.15;
            return group(ear, capsuleMesh(0.025, 0.16, '#F7A1C4', sx * 0.06, 0.82, 0.05).rotateZ(sx * -0.15));
        }), ball(0.06, '#FFFFFF', 0, 0.2, -0.22, softO, 8), ball(0.022, '#F7A1C4', 0, 0.58, 0.2, {}, 6), ball(0.018, '#2A1D17', -0.05, 0.62, 0.18, {}, 5), ball(0.018, '#2A1D17', 0.05, 0.62, 0.18, {}, 5),
            ...egg(0.18, 0.08, 0.15, 0.07, '#FFD23F', '#3E7CB4'));
    },
    egg_basket: () => group(cyl(0.26, 0.2, 0.22, '#C9A16E', 0, 0, 0, 14), mesh(new THREE.TorusGeometry(0.24, 0.02, 5, 16, Math.PI), mat('#B98A50'), 0, 0.22, 0),
        cyl(0.24, 0.24, 0.03, '#9CCB5A', 0, 0.2, 0, 14), ...[[-0.1, 0.05, '#F7A1C4'], [0.1, 0.04, '#9ED2C0'], [0, -0.1, '#FFD23F'], [0.02, 0.12, '#B9A3E0'], [-0.14, -0.06, '#F08A24']].flatMap(([x, z, c]) => egg(x, 0.28, z, 0.06, c))),
    easter_tree: () => group(cyl(0.18, 0.15, 0.3, '#F2EEE6', 0, 0, 0, 12), ...[[0.5, 0], [-0.4, 0.4], [0.2, -0.6], [-0.3, -0.3], [0, 0.6]].map(([rx, rz], i) => {
        const b = cyl(0.015, 0.025, 0.9, '#8A5A35', 0, 0.3, 0, 5);
        b.rotation.set(rx * 0.6, 0, rz * 0.6);
        return b;
    }), ...Array.from({length: 9}, (_, i) => egg(Math.cos(i * 1.3) * 0.25, 0.85 + (i % 3) * 0.15, Math.sin(i * 1.3) * 0.25, 0.045, ['#F7A1C4', '#9ED2C0', '#FFD23F', '#B9A3E0'][i % 4])).flat(),
        ...Array.from({length: 5}, (_, i) => ball(0.04, '#F7B6CF', Math.cos(i * 2) * 0.2, 1.1 + i * 0.05, Math.sin(i * 2) * 0.2, {}, 5))),
    chick_planter: () => group(box(0.8, 0.3, 0.5, '#B07A4A', 0, 0, 0), box(0.76, 0.04, 0.46, '#9CCB5A', 0, 0.28, 0), ...[-0.22, 0.05, 0.25].map((x, i) => group(ball(0.08, '#FFD23F', x, 0.38, (i % 2) * 0.08, softO, 10),
        ball(0.055, '#FFD23F', x, 0.5, (i % 2) * 0.08 + 0.03, softO, 8), mesh(new THREE.ConeGeometry(0.018, 0.04, 4), mat('#F08A24'), x, 0.5, (i % 2) * 0.08 + 0.09).rotateX(Math.PI / 2))),
        ...Array.from({length: 6}, (_, i) => ball(0.04, ['#FFFFFF', '#F7A1C4'][i % 2], -0.32 + i * 0.13, 0.33, -0.15, {}, 5))),
    lemonade_stand: () => group(box(0.9, 0.85, 0.5, '#FFF3C4', 0, 0, -0.1), box(0.94, 0.06, 0.56, '#F6C945', 0, 0.85, -0.1), ...[-0.42, 0.42].map(x => box(0.05, 0.9, 0.05, '#FFFFFF', x, 0.9, -0.3)),
        ...Array.from({length: 6}, (_, i) => box(0.16, 0.04, 0.6, i % 2 ? '#FFFFFF' : '#F6C945', -0.4 + i * 0.16, 1.8, -0.05).rotateX(0.25)), cyl(0.07, 0.06, 0.22, '#DDF1F7', -0.2, 0.91, -0.1, 12, {opacity: 0.6}),
        cyl(0.06, 0.05, 0.17, '#F6E27A', -0.2, 0.92, -0.1, 12), ...[0.05, 0.18, 0.31].map(x => cyl(0.03, 0.025, 0.09, '#F6E27A', x, 0.91, 0.05, 8)), ball(0.05, '#F6C945', 0.25, 0.95, -0.2, softO, 8)),
    surfboard: () => {
        const b = ball(0.25, '#2F9E8F', 0, 0.95, -0.1, softO, 14);
        b.scale.set(1, 3.6, 0.15);
        return group(b, box(0.04, 1.6, 0.02, '#FFFFFF', 0, 0.12, -0.06), box(0.5, 0.05, 0.3, '#B07A4A', 0, 0, -0.1), mesh(new THREE.ConeGeometry(0.06, 0.12, 3), mat('#F25F5C'), 0, 0.35, -0.08));
    },
    watermelon_cart: () => group(box(0.85, 0.35, 0.55, '#7CB342', 0, 0.35, 0), ...[-0.32, 0.32].map(x => cyl(0.16, 0.16, 0.05, '#5A3218', x, 0.16, 0.3, 12).rotateX(Math.PI / 2)), cyl(0.02, 0.02, 0.6, '#5A3218', 0.6, 0.45, 0, 6).rotateZ(-1.0),
        ...[[-0.2, 0], [0.15, 0.05]].map(([x, z]) => {
            const m = ball(0.18, '#3F7A2E', x, 0.82, z, softO, 12);
            m.scale.set(1.2, 0.9, 1);
            return m;
        }), ...[-0.25, 0, 0.25].map(x => mesh(new THREE.CylinderGeometry(0.12, 0.12, 0.03, 12, 1, false, 0, Math.PI), mat('#E63946'), x, 0.74, -0.2).rotateZ(Math.PI / 2))),
    hammock: () => {
        const g = group(...[-0.92, 0.92].map(x => group(box(0.08, 1.3, 0.08, '#8A5A35', x, 0, -0.2), box(0.08, 1.3, 0.08, '#8A5A35', x, 0, 0.2), box(0.08, 0.06, 0.5, '#8A5A35', x, 1.25, 0))));
        // The cloth sags between the posts: short striped strips along a curve
        const n = 10;
        for (let i = 0; i < n; i++) {
            const x0 = -0.8 + i * 1.6 / n, x1 = x0 + 1.6 / n, y = (x) => 1.15 - 0.45 * (1 - (x / 0.8) ** 2);
            const seg = box(Math.hypot(x1 - x0, y(x1) - y(x0)) + 0.01, 0.02, 0.5, i % 2 ? '#FFFFFF' : '#F25F5C', (x0 + x1) / 2, (y(x0) + y(x1)) / 2, 0);
            seg.rotation.z = Math.atan2(y(x1) - y(x0), x1 - x0);
            g.add(seg);
        }
        g.add(cyl(0.01, 0.01, 0.2, '#C9B07A', -0.86, 1.12, 0, 4).rotateZ(0.8), cyl(0.01, 0.01, 0.2, '#C9B07A', 0.86, 1.12, 0, 4).rotateZ(-0.8), box(0.3, 0.08, 0.2, '#F7A1C4', -0.45, 0.85, 0).rotateZ(-0.3));
        return g;
    },
    deck_chair: () => {
        const seat = group(...Array.from({length: 4}, (_, i) => box(0.12, 0.02, 0.5, i % 2 ? '#FFFFFF' : '#3E7CB4', -0.18 + i * 0.12, 0, 0)));
        seat.rotation.y = Math.PI / 2;
        seat.position.set(0, 0.32, 0.1);
        seat.rotation.x = -0.15;
        const back = group(...Array.from({length: 4}, (_, i) => box(0.12, 0.55, 0.02, i % 2 ? '#FFFFFF' : '#3E7CB4', -0.18 + i * 0.12, 0, 0)));
        back.position.set(0, 0.3, -0.22);
        back.rotation.x = -0.55;
        return group(seat, back, ...[-0.26, 0.26].flatMap(x => [box(0.04, 0.04, 0.75, '#C98F5A', x, 0.28, 0.05), box(0.04, 0.3, 0.04, '#C98F5A', x, 0, 0.38), box(0.04, 0.75, 0.04, '#C98F5A', x, 0, -0.3).rotateX(-0.45)]));
    },
    flamingo: () => {
        const body = ball(0.15, '#F7A1C4', 0, 0.75, 0, softO, 12);
        body.scale.set(0.8, 0.7, 1.3);
        const neck = mesh(new THREE.TorusGeometry(0.12, 0.025, 6, 12, Math.PI * 1.2), mat('#F7A1C4', softO), 0, 0.98, 0.15);
        neck.rotation.y = Math.PI / 2;
        return group(body, neck, ball(0.05, '#F7A1C4', 0, 1.08, 0.3, softO, 8), mesh(new THREE.ConeGeometry(0.02, 0.07, 5), mat('#2A1D17'), 0, 1.06, 0.36).rotateX(Math.PI / 2 + 0.4),
            cyl(0.01, 0.01, 0.65, '#E07B9A', 0.03, 0.0, 0, 4), cyl(0.01, 0.01, 0.65, '#E07B9A', -0.03, 0.0, 0, 4));
    },
    jack_lantern: () => {
        const p = mesh(new THREE.SphereGeometry(0.28, 14, 10), mat('#E07B39', softO), 0, 0.25, 0);
        p.scale.y = 0.8;
        return group(p, cyl(0.02, 0.03, 0.1, '#5A3218', 0, 0.45, 0, 6), ...[-0.1, 0.1].map(x => mesh(new THREE.ConeGeometry(0.05, 0.07, 3), glowMat('#FFC24A'), x, 0.32, 0.25).rotateX(Math.PI / 2)),
            mesh(new THREE.BoxGeometry(0.2, 0.04, 0.02), glowMat('#FFC24A'), 0, 0.17, 0.26), lightPool(0.8));
    },
    scarecrow: () => group(cyl(0.025, 0.025, 1.6, '#8A5A35', 0, 0, 0, 6), box(1.0, 0.05, 0.05, '#8A5A35', 0, 1.1, 0), box(0.4, 0.45, 0.2, '#5B8DB8', 0, 0.75, 0), box(0.36, 0.06, 0.21, '#B43E5C', 0, 0.98, 0),
        ...[-0.32, 0.32].map(x => box(0.3, 0.12, 0.12, '#5B8DB8', x, 1.05, 0)), ball(0.14, '#E9D08F', 0, 1.35, 0, softO, 10), cyl(0.24, 0.24, 0.02, '#C98F5A', 0, 1.45, 0, 14), cyl(0.12, 0.14, 0.16, '#C98F5A', 0, 1.46, 0, 12),
        ...[-0.05, 0.05].map(x => ball(0.015, '#2A1D17', x, 1.38, 0.13, {}, 5)), ...Array.from({length: 6}, (_, i) => cyl(0.006, 0.006, 0.12, '#E9D08F', -0.5 + (i % 2), 1.06 + i * 0.01, 0.05, 3).rotateZ(1.4))),
    hay_bales: () => group(...[[-0.45, 0], [0.45, 0], [0, 0.42]].map(([x, y]) => group(box(0.85, 0.42, 0.55, '#E9C46A', x, y, 0), ...[-0.2, 0.2].map(dx => box(0.03, 0.43, 0.56, '#B98A50', x + dx, y, 0)))),
        mesh(new THREE.SphereGeometry(0.16, 12, 8).scale(1, 0.8, 1), mat('#E07B39', softO), -0.15, 0.97, 0.05), cyl(0.015, 0.02, 0.06, '#5A3218', -0.15, 1.1, 0.05, 5)),
    autumn_wreath: () => group(...[0, 1, 2].map(i => {
        const l = cyl(0.015, 0.02, 1.2, '#7B4E2C', Math.cos(i * 2.1) * 0.15, 0, Math.sin(i * 2.1) * 0.15 - 0.1, 6);
        l.rotation.set(Math.sin(i * 2.1) * 0.15, 0, -Math.cos(i * 2.1) * 0.15);
        return l;
    }), ...Array.from({length: 16}, (_, i) => {
        const a = i / 16 * Math.PI * 2;
        return leafy(Math.cos(a) * 0.25, 1.15 + Math.sin(a) * 0.25, 0, 0.07, ['#E07B39', '#C8452C', '#E9B44C', '#B5562E'][i % 4]);
    }), ...[0.8, 2.5, 4.4].map(a => ball(0.04, '#7B4E2C', Math.cos(a) * 0.25, 1.15 + Math.sin(a) * 0.25, 0.05, {}, 6)), box(0.12, 0.08, 0.02, '#D7263D', 0, 0.88, 0.05)),
    friendly_ghost: () => {
        const g = group(cyl(0.18, 0.2, 0.06, '#5A3A6B', 0, 0, 0, 12));
        const body = mesh(new THREE.CapsuleGeometry(0.2, 0.3, 6, 12), glowMat('#F4F7FF'), 0, 0.75, 0);
        g.add(body, ...[-0.07, 0.07].map(x => ball(0.03, '#2A1D17', x, 0.85, 0.18, {}, 6)), ball(0.025, '#F7A1C4', 0, 0.76, 0.19, {}, 5));
        for (let i = 0; i < 5; i++) g.add(mesh(new THREE.ConeGeometry(0.06, 0.12, 6), glowMat('#F4F7FF'), Math.cos(i * 1.26) * 0.15, 0.42, Math.sin(i * 1.26) * 0.15).rotateX(Math.PI));
        g.userData.bob = body;
        return g;
    },
    snowman: () => group(ball(0.32, '#F7FAFC', 0, 0.3, 0, softO, 14), ball(0.24, '#F7FAFC', 0, 0.78, 0, softO, 14), ball(0.17, '#F7FAFC', 0, 1.15, 0, softO, 12),
        mesh(new THREE.ConeGeometry(0.03, 0.15, 6), mat('#F08A24'), 0, 1.15, 0.22).rotateX(Math.PI / 2), ...[-0.06, 0.06].map(x => ball(0.02, '#2A1D17', x, 1.2, 0.15, {}, 5)),
        ...[0.7, 0.82, 0.94].map(y => ball(0.025, '#2A1D17', 0, y, 0.24, {}, 5)), mesh(new THREE.TorusGeometry(0.15, 0.04, 6, 14), mat('#D7263D'), 0, 1.0, 0).rotateX(Math.PI / 2),
        box(0.06, 0.25, 0.03, '#D7263D', 0.1, 0.78, 0.17), cyl(0.15, 0.15, 0.02, '#2A1D17', 0, 1.3, 0, 14), cyl(0.1, 0.1, 0.18, '#2A1D17', 0, 1.3, 0, 12),
        ...[-1, 1].map(sx => cyl(0.012, 0.012, 0.4, '#5A3218', sx * 0.35, 0.85, 0, 4).rotateZ(sx * 1.0))),
    gift_pile: () => group(...[[-0.18, 0, 0, 0.35, 0.3, '#D7263D', '#FFD23F'], [0.2, 0, 0.05, 0.3, 0.25, '#2F7D3A', '#FFFFFF'], [0, 0.3, 0, 0.26, 0.22, '#3E7CB4', '#F7A1C4'], [0.05, 0, -0.25, 0.22, 0.18, '#FFD23F', '#D7263D']].flatMap(([x, y, z, w, h, c, r]) => [
        box(w, h, w, c, x, y, z), box(w + 0.01, h + 0.01, 0.04, r, x, y, z), box(0.04, h + 0.01, w + 0.01, r, x, y, z), ball(0.05, r, x, y + h + 0.03, z, {}, 6)])),
    nutcracker: () => group(box(0.32, 0.1, 0.32, '#2F7D3A', 0, 0, 0), ...[-0.06, 0.06].map(x => cyl(0.05, 0.05, 0.5, '#1E1E24', x, 0.1, 0, 8)), box(0.28, 0.45, 0.2, '#D7263D', 0, 0.6, 0),
        box(0.3, 0.05, 0.22, PALETTE.gold, 0, 0.75, 0, {metalness: 0.5}), ...[-0.18, 0.18].map(x => cyl(0.04, 0.04, 0.38, '#D7263D', x, 0.68, 0, 8)), ball(0.12, '#F6D7BE', 0, 1.18, 0, softO, 10),
        cyl(0.12, 0.13, 0.28, '#1E1E24', 0, 1.27, 0, 12), box(0.14, 0.05, 0.05, '#FFFFFF', 0, 1.1, 0.1), ball(0.025, PALETTE.gold, 0, 1.45, 0.08, {metalness: 0.6}, 6)),
    reindeer: () => {
        const body = ball(0.25, '#9C6B3F', 0, 0.75, 0, softO, 12);
        body.scale.set(0.8, 0.8, 1.5);
        return group(body, ...[[-0.12, -0.25], [0.12, -0.25], [-0.12, 0.25], [0.12, 0.25]].map(([x, z]) => cyl(0.04, 0.035, 0.6, '#7B4E2C', x, 0, z, 6)),
            cyl(0.07, 0.08, 0.35, '#9C6B3F', 0, 0.95, 0.32, 8).rotateX(-0.5), ball(0.12, '#9C6B3F', 0, 1.2, 0.45, softO, 10), ball(0.04, '#E63946', 0, 1.18, 0.57, {emissive: '#E63946', glow: 0.8}, 8),
            ...[-1, 1].flatMap(sx => [cyl(0.015, 0.015, 0.3, '#5A3218', sx * 0.08, 1.3, 0.42, 4).rotateZ(sx * -0.5), cyl(0.012, 0.012, 0.15, '#5A3218', sx * 0.18, 1.45, 0.42, 4).rotateZ(sx * 0.6)]),
            mesh(new THREE.TorusGeometry(0.1, 0.02, 5, 12), mat(PALETTE.gold, {metalness: 0.6}), 0, 1.02, 0.36).rotateX(Math.PI / 3));
    },
    xmas_arch: () => group(...[-0.45, 0.45].map(x => cyl(0.04, 0.04, 1.8, '#2F7D3A', x, 0, 0, 8)), mesh(new THREE.TorusGeometry(0.45, 0.05, 6, 18, Math.PI), mat('#2F7D3A'), 0, 1.8, 0),
        ...Array.from({length: 14}, (_, i) => {
            const a = i / 13 * Math.PI;
            return lampHead(Math.cos(a) * 0.45, 1.8 + Math.sin(a) * 0.45, 0.05, 0.035, ['#FFE7A8', '#E63946', '#9FE0F7', '#7CD992'][i % 4]);
        }), ...Array.from({length: 8}, (_, i) => lampHead(i % 2 ? 0.45 : -0.45, 0.3 + Math.floor(i / 2) * 0.4, 0.05, 0.035, ['#FFE7A8', '#E63946'][i % 2])), lightPool(1.4)),
    advent_candles: () => group(box(0.5, 0.7, 0.5, '#7B4E2C', 0, 0, 0), mesh(new THREE.TorusGeometry(0.18, 0.06, 6, 18), mat('#2F7D3A'), 0, 0.74, 0).rotateX(Math.PI / 2),
        ...[0, 1, 2, 3].map(i => group(cyl(0.025, 0.025, 0.18, i % 2 ? '#B43E5C' : '#F4EAD5', Math.cos(i * Math.PI / 2) * 0.18, 0.74, Math.sin(i * Math.PI / 2) * 0.18, 8),
            mesh(new THREE.ConeGeometry(0.015, 0.05, 6), glowMat('#FFB347'), Math.cos(i * Math.PI / 2) * 0.18, 0.96, Math.sin(i * Math.PI / 2) * 0.18))),
        ...Array.from({length: 6}, (_, i) => ball(0.025, '#D7263D', Math.cos(i + 0.4) * 0.2, 0.78, Math.sin(i + 0.4) * 0.2, {}, 5))),
});

// The floor lamp's shade glows at night like the others
FURNITURE.lamp_floor = () => group(cyl(0.16, 0.18, 0.04, PALETTE.ink, 0, 0, 0, 14), cyl(0.02, 0.02, 1.35, PALETTE.ink, 0, 0.04, 0, 6),
    mesh(new THREE.CylinderGeometry(0.12, 0.24, 0.3, 14), glowMat('#F6D58E'), 0, 1.45, 0), lightBulb());

export function furnitureModel(key) {
    const make = FURNITURE[key];
    const g = make ? make() : group(box(0.8, 0.8, 0.8, '#FF00FF', 0, 0, 0));
    g.userData.key = key;
    return g;
}

// ---------------------------------------------------------------- people

const SHIRTS = ['#3E7CB4', '#7A8B3A', '#B43E5C', '#E0A526', '#6B4FA0', '#2F9E8F', '#D9653B', '#5D6D7E', '#C06C84', '#F08A5D'];
const HAIR = ['#2B1B12', '#5A3825', '#A0522D', '#E8C07D', '#1C1C1C', '#B23A48', '#8E8E8E'];
const STYLES = ['short', 'bob', 'bun', 'ponytail', 'curly', 'spiky', 'long', 'afro', 'braids', 'pigtails', 'pixie', 'topknot', 'wavy', 'sidepart', 'short', 'bob'];

// ---------------------------------------------------------------- cartoon shading for people

/** Three flat tones per colour (cel shading) instead of smooth light: the cartoon look. */
const TOON_RAMP = (() => {
    const t = new THREE.DataTexture(new Uint8Array([90, 90, 90, 255, 175, 175, 175, 255, 255, 255, 255, 255]), 3, 1);
    t.minFilter = t.magFilter = THREE.NearestFilter;
    t.needsUpdate = true;
    return t;
})();
const toonCache = new Map();
const toon = (color) => {
    if (!toonCache.has(color)) toonCache.set(color, new THREE.MeshToonMaterial({color, gradientMap: TOON_RAMP}));
    return toonCache.get(color);
};
const OUTLINE = new THREE.MeshBasicMaterial({color: '#2A1A12', side: THREE.BackSide});

/** A cartoon part: toon material, shadows, and (optionally) a dark outline drawn behind it. */
function part(geo, color, x = 0, y = 0, z = 0, outline = 0) {
    const g = new THREE.Group();
    const m = new THREE.Mesh(geo, toon(color));
    m.castShadow = true;
    g.add(m);
    if (outline) {
        const o = new THREE.Mesh(geo, OUTLINE);
        o.scale.setScalar(1 + outline);
        g.add(o);
    }
    g.position.set(x, y, z);
    return g;
}

const capsule = (r, len, color, x, y, z, outline = 0.08) => part(new THREE.CapsuleGeometry(r, len, 6, 12), color, x, y, z, outline);
const sphere = (r, color, x, y, z, outline = 0, seg = 16) => part(new THREE.SphereGeometry(r, seg, Math.max(8, seg - 4)), color, x, y, z, outline);

/** Hairstyles (cartoon volumes on top of the head, radius 0.23). */
function hairMesh(style, color) {
    const g = new THREE.Group();
    const cap = (scaleY = 1) => {
        const c = part(new THREE.SphereGeometry(0.245, 18, 10, 0, Math.PI * 2, 0, Math.PI / 1.9), color, 0, 0.02, -0.02, 0.06);
        c.rotation.x = -0.32;
        c.scale.y = scaleY;
        g.add(c);
    };
    switch (style) {
        case 'bald':
            break;
        case 'short':
            cap(0.8);
            break;
        case 'bob':
            cap();
            for (const sx of [-1, 1]) g.add(part(new THREE.SphereGeometry(0.13, 12, 10), color, sx * 0.18, -0.08, -0.04, 0.06).translateY(0));
            g.add(part(new THREE.SphereGeometry(0.2, 14, 10), color, 0, -0.06, -0.12, 0.06));
            break;
        case 'bun':
            cap();
            g.add(sphere(0.1, color, 0, 0.24, -0.12, 0.08, 12));
            break;
        case 'ponytail':
            cap();
            g.add(capsule(0.06, 0.2, color, 0, -0.08, -0.26, 0.08).rotateX(0.5));
            g.add(sphere(0.04, '#E63946', 0, 0.06, -0.22, 0, 8));
            break;
        case 'curly':
            for (let i = 0; i < 11; i++) {
                const a = i / 11 * Math.PI * 2;
                g.add(sphere(0.085, color, Math.cos(a) * 0.17, 0.12 + Math.sin(i * 1.7) * 0.03, Math.sin(a) * 0.15 - 0.03, 0.08, 10));
            }
            g.add(sphere(0.12, color, 0, 0.2, -0.02, 0.06, 12));
            break;
        case 'spiky':
            cap(0.7);
            for (let i = 0; i < 7; i++) {
                const spike = part(new THREE.ConeGeometry(0.05, 0.16, 6), color, (i - 3) * 0.055, 0.24, -0.04 + Math.abs(i - 3) * 0.02, 0.08);
                spike.rotation.z = -(i - 3) * 0.18;
                g.add(spike);
            }
            break;
        case 'long':
            cap();
            g.add(part(new THREE.CapsuleGeometry(0.17, 0.25, 6, 12), color, 0, -0.18, -0.12, 0.05));
            break;
        case 'afro':
            g.add(sphere(0.34, color, 0, 0.1, -0.04, 0.05, 16));
            for (let i = 0; i < 8; i++) g.add(sphere(0.1, color, Math.cos(i * 0.8) * 0.3, 0.1 + Math.sin(i * 1.3) * 0.12, Math.sin(i * 0.8) * 0.26 - 0.04, 0.06, 8));
            break;
        case 'mohawk':
            cap(0.45);
            for (let i = 0; i < 6; i++) {
                const sp = part(new THREE.ConeGeometry(0.05, 0.2, 6), color, 0, 0.27 - Math.abs(i - 2.5) * 0.02, 0.15 - i * 0.07, 0.08);
                sp.rotation.x = -0.3 + i * 0.12;
                g.add(sp);
            }
            break;
        case 'braids':
            cap();
            for (const sx of [-1, 1]) {
                for (let k = 0; k < 4; k++) g.add(sphere(0.05, color, sx * 0.2, -0.1 - k * 0.08, -0.02, 0.08, 8));
                g.add(sphere(0.03, PALETTE.berry, sx * 0.2, -0.43, -0.02, 0, 6));
            }
            break;
        case 'pigtails':
            cap();
            for (const sx of [-1, 1]) {
                g.add(sphere(0.11, color, sx * 0.27, 0.1, -0.06, 0.07, 10), sphere(0.035, '#F7A1C4', sx * 0.2, 0.12, -0.05, 0, 6));
            }
            break;
        case 'pixie':
            cap(0.75);
            g.add(sphere(0.09, color, 0.08, 0.15, 0.17, 0.08, 10), sphere(0.07, color, -0.04, 0.17, 0.18, 0.08, 8));
            break;
        case 'topknot':
            cap(0.8);
            g.add(sphere(0.09, color, 0, 0.28, -0.04, 0.08, 10), part(new THREE.TorusGeometry(0.06, 0.015, 5, 12), PALETTE.gold, 0, 0.22, -0.04).rotateX(Math.PI / 2));
            break;
        case 'wavy':
            cap();
            for (let i = 0; i < 7; i++) g.add(sphere(0.085, color, Math.cos(Math.PI + i * 0.52) * 0.22, -0.12 - (i % 2) * 0.05, Math.sin(Math.PI + i * 0.52) * 0.18 - 0.02, 0.07, 8));
            break;
        case 'sidepart':
            cap(0.9);
            g.add(part(new THREE.CapsuleGeometry(0.06, 0.18, 4, 8), color, -0.08, 0.18, 0.15, 0.08).rotateZ(1.2));
            break;
    }
    return g;
}

/** A small prop part (no outline: they are tiny). */
function pp(geo, color, x = 0, y = 0, z = 0) {
    const m = new THREE.Mesh(geo, toon(color));
    m.position.set(x, y, z);
    m.castShadow = true;
    return m;
}

/**
 * Hand-held tools, hidden until a pose needs them. Hand props hang from the hand (0, -0.3, 0 in the arm) inside a
 * "level" group that the animation counter-rotates, so a pan, a notepad or a tray stays flat while the arm moves.
 */
function makeProps(role, armL, armR, p) {
    const props = {};
    const hand = (arm, name, build) => {
        const g = new THREE.Group();
        g.position.set(0, -0.3, 0);
        g.visible = false;
        build(g);
        arm.add(g);
        props[name] = g;
    };
    if (role === 'chef') {
        hand(armL, 'pan', g => {
            const h = pp(new THREE.CylinderGeometry(0.012, 0.014, 0.16, 6), '#3B2418', 0, 0.0, 0.08);
            h.rotation.x = Math.PI / 2;
            g.add(h, pp(new THREE.CylinderGeometry(0.1, 0.085, 0.035, 18), '#3A3F45', 0, 0.01, 0.24), pp(new THREE.CylinderGeometry(0.088, 0.088, 0.006, 18), '#5C636B', 0, 0.026, 0.24));
            const food = new THREE.Group();
            food.position.set(0, 0.04, 0.24);
            for (let i = 0; i < 4; i++) food.add(pp(new THREE.SphereGeometry(0.022, 8, 6), ['#F4C542', '#D7442B', '#7CB342', '#F4C542'][i], Math.cos(i * 1.6) * 0.04, 0, Math.sin(i * 1.6) * 0.04));
            g.add(food);
            g.userData.food = food;
        });
        hand(armR, 'spatula', g => {
            const h = pp(new THREE.CylinderGeometry(0.012, 0.012, 0.26, 6), '#8B5A2B', 0, -0.06, 0.115);
            h.rotation.x = 2.04; // forward and down, into the pan
            const blade = pp(new THREE.BoxGeometry(0.08, 0.01, 0.1), '#C9CED4', 0, -0.125, 0.245);
            blade.rotation.x = 0.45;
            g.add(h, blade);
        });
        hand(armR, 'shaker', g => g.add(pp(new THREE.CylinderGeometry(0.022, 0.025, 0.07, 10), '#FFFFFF', 0, 0.03, 0.03), pp(new THREE.CylinderGeometry(0.023, 0.023, 0.02, 10), '#C9CED4', 0, -0.01, 0.03)));
    }
    if (role === 'waiter' || role === 'manager') {
        hand(armL, 'pad', g => {
            const pad = new THREE.Group();
            pad.position.set(0.04, 0.06, 0.05);
            pad.rotation.x = -0.9;
            pad.add(pp(new THREE.BoxGeometry(0.11, 0.15, 0.012), '#FFFFFF'), pp(new THREE.BoxGeometry(0.11, 0.025, 0.016), PALETTE.terracotta, 0, 0.065, 0));
            for (let i = 0; i < 3; i++) pad.add(pp(new THREE.BoxGeometry(0.07, 0.006, 0.014), '#9AA3AD', -0.005, 0.02 - i * 0.03, 0.001));
            g.add(pad);
        });
        hand(armR, 'pen', g => {
            const pen = pp(new THREE.CylinderGeometry(0.006, 0.004, 0.09, 6), '#2A4F7A', 0, 0.02, 0.02);
            pen.rotation.x = 0.6;
            g.add(pen);
        });
    }
    if (role === 'waiter' || role === 'cleaner' || role === 'chef') hand(armR, 'cloth', g => {
        const cl = pp(new THREE.BoxGeometry(0.1, 0.018, 0.09), role === 'cleaner' ? '#F6C945' : '#7FC4D9', 0, -0.05, 0.02);
        cl.rotation.z = 0.2;
        g.add(cl);
    });
    if (role === 'waiter' || role === 'chef') { // a round tray under whatever is carried (it lives on the body, not the hand)
        const tray = new THREE.Group();
        tray.add(pp(new THREE.CylinderGeometry(0.22, 0.21, 0.015, 24), '#C9CED4', 0, 0, 0));
        const rim = pp(new THREE.TorusGeometry(0.215, 0.008, 6, 28), '#AAB2BA', 0, 0.008, 0);
        rim.rotation.x = Math.PI / 2;
        tray.add(rim);
        tray.position.set(0, 0.94, 0.32);
        tray.visible = false;
        p.add(tray);
        props.tray = tray;
    }
    if (role === 'cleaner') hand(armR, 'can', g => { // a green watering can, with water coming out of the spout
        const can = new THREE.Group();
        can.position.set(0, -0.08, 0.06);
        can.add(pp(new THREE.CylinderGeometry(0.07, 0.08, 0.14, 12), '#4CAF50'), pp(new THREE.TorusGeometry(0.05, 0.012, 5, 10, Math.PI), '#3E8E41', 0, 0.08, 0));
        const spout = pp(new THREE.CylinderGeometry(0.012, 0.02, 0.2, 6), '#4CAF50', 0, 0.02, 0.13);
        spout.rotation.x = 1.0;
        const stream = new THREE.Mesh(new THREE.CylinderGeometry(0.008, 0.02, 0.35, 6), new THREE.MeshBasicMaterial({color: '#8CC8EC', transparent: true, opacity: 0.7}));
        stream.position.set(0, -0.12, 0.27);
        can.add(spout, stream);
        g.add(can);
        g.userData.stream = stream;
    });
    if (role === 'cleaner') { // the mop, held in both hands and pivoting at the hands
        const mop = new THREE.Group();
        mop.add(pp(new THREE.CylinderGeometry(0.014, 0.014, 0.9, 6), '#C98F5A', 0, -0.45, 0));
        const head = new THREE.Group();
        head.position.y = -0.88;
        head.add(pp(new THREE.BoxGeometry(0.2, 0.03, 0.06), '#2F6F8F'));
        for (let i = 0; i < 7; i++) {
            const str = pp(new THREE.CylinderGeometry(0.012, 0.016, 0.06, 5), '#F2EDE4', -0.09 + i * 0.03, -0.03, (i % 2) * 0.02 - 0.01);
            str.rotation.z = (i - 3) * 0.12;
            head.add(str);
        }
        mop.add(head);
        mop.position.set(0, 0.74, 0.24);
        mop.visible = false;
        p.add(mop);
        props.mop = mop;
    }
    if (role === 'customer') {
        hand(armR, 'fork', g => {
            const f = new THREE.Group();
            f.position.set(0, 0.0, 0.04);
            f.rotation.x = -0.9;
            f.add(pp(new THREE.BoxGeometry(0.012, 0.1, 0.006), '#C9CED4', 0, 0.03, 0));
            for (let i = 0; i < 3; i++) f.add(pp(new THREE.BoxGeometry(0.004, 0.03, 0.004), '#C9CED4', (i - 1) * 0.008, 0.09, 0));
            g.add(f);
        });
        hand(armL, 'knife', g => {
            const k = pp(new THREE.BoxGeometry(0.014, 0.12, 0.004), '#C9CED4', 0, 0.02, 0.04);
            k.rotation.x = -1.2;
            g.add(k);
        });
        hand(armL, 'menu', g => {
            const m = new THREE.Group();
            m.position.set(0.12, 0.07, 0.05);
            m.rotation.x = -0.8;
            m.add(pp(new THREE.BoxGeometry(0.2, 0.26, 0.012), PALETTE.berry), pp(new THREE.BoxGeometry(0.17, 0.23, 0.014), '#FFF4E6'));
            for (let i = 0; i < 4; i++) m.add(pp(new THREE.BoxGeometry(0.12, 0.008, 0.016), '#B9A58C', 0, 0.07 - i * 0.045, 0));
            g.add(m);
        });
    }
    if (role !== 'customer') hand(armR, 'cup', g => {
        g.add(pp(new THREE.CylinderGeometry(0.03, 0.025, 0.07, 12), '#FFFFFF', 0, 0.02, 0.04));
        const handle = pp(new THREE.TorusGeometry(0.016, 0.005, 5, 10), '#FFFFFF', 0.035, 0.02, 0.04);
        handle.rotation.y = Math.PI / 2;
        g.add(handle);
    });
    return props;
}

/**
 * A cartoon character. role: chef | waiter | cleaner | manager | customer.
 * look (staff): {outfit, hair, hairColor, uniform: {top, bottom, accent, cut}} — the uniform comes from the catalog.
 */
export function personModel(role, color, seed = 0, look = {}) {
    const rnd = (n) => Math.abs(Math.sin(seed * 12.9898 + n * 78.233) * 43758.5453) % 1;
    const skin = PALETTE.skin[Math.floor(rnd(1) * PALETTE.skin.length)];
    const uni = look.uniform && look.uniform.top ? look.uniform : null;
    const roleTop = {chef: '#FFFFFF', waiter: '#FAFAFA', cleaner: '#7FC4D9', manager: '#3D5A8C'}[role];
    const roleBottom = {chef: '#3B3B3B', waiter: '#22201F', cleaner: '#2F6F8F', manager: '#34405E'}[role];
    const shirt = uni?.top || roleTop || (color || SHIRTS[Math.floor(rnd(2) * SHIRTS.length)]);
    const pants = uni?.bottom || roleBottom || ['#2F3B52', '#4A3B2F', '#2E2E2E', '#5B5F4A'][Math.floor(rnd(3) * 4)];
    const accent = uni?.accent || color || PALETTE.terracotta;
    const cut = uni?.cut || {chef: 'apron', waiter: 'vest', cleaner: 'overalls', manager: 'tie'}[role] || '';
    const p = new THREE.Group();
    const body = new THREE.Group();
    body.position.y = 0.5;
    p.add(body);

    // A rounded, slightly pear-shaped body
    const torso = capsule(0.16, 0.2, shirt, 0, 0.27, 0, 0.07);
    const customer = role === 'customer';
    const wide = customer ? 0.9 + rnd(11) * 0.28 : 1;
    torso.scale.set(wide, 1, 0.85 * wide);
    body.add(torso);
    if (customer) { // a collar and (often) a belt: everyday clothes
        for (const sx of [-1, 1]) {
            const col = part(new THREE.BoxGeometry(0.08, 0.02, 0.06), rnd(14) > 0.5 ? '#FFFFFF' : shirt, sx * 0.05, 0.44, 0.1, 0.12);
            col.rotation.set(0.3, sx * 0.5, sx * -0.35);
            body.add(col);
        }
        if (rnd(15) > 0.35) {
            body.add(part(new THREE.TorusGeometry(0.158 * wide, 0.02, 6, 22), '#3B2418', 0, 0.075, 0).rotateX(Math.PI / 2));
            body.add(part(new THREE.BoxGeometry(0.05, 0.04, 0.02), '#E2B23A', 0, 0.075, 0.15 * wide));
        }
    }
    if (cut === 'stripes') for (let i = 0; i < 3; i++) body.add(part(new THREE.TorusGeometry(0.162, 0.018, 6, 20), accent, 0, 0.18 + i * 0.1, 0).rotateX(Math.PI / 2));
    if (cut === 'apron' || cut === 'stripes') body.add(part(new THREE.BoxGeometry(0.24, 0.3, 0.02), cut === 'stripes' ? '#FFFFFF' : accent, 0, 0.14, 0.145));
    if (cut === 'vest') body.add(part(new THREE.CylinderGeometry(0.165, 0.19, 0.3, 14, 1, true, -Math.PI * 0.72, Math.PI * 1.44), accent === '#FFFFFF' ? '#2B2B2B' : (role === 'waiter' && !uni ? (color || '#2B2B2B') : accent), 0, 0.24, 0));
    if (cut === 'vest' || cut === 'tux') body.add(part(new THREE.BoxGeometry(0.09, 0.035, 0.03), role === 'waiter' && !uni ? PALETTE.berry : (cut === 'tux' ? '#1B1B1B' : accent), 0, 0.42, 0.13)); // bow tie
    if (cut === 'tux') body.add(part(new THREE.BoxGeometry(0.1, 0.24, 0.02), accent, 0, 0.3, 0.138));
    if (cut === 'tie') {
        body.add(part(new THREE.BoxGeometry(0.12, 0.08, 0.02), '#FFFFFF', 0, 0.39, 0.135));
        body.add(part(new THREE.BoxGeometry(0.045, 0.2, 0.02), uni ? accent : (color || PALETTE.berry), 0, 0.27, 0.145));
    }
    if (cut === 'jacket') { // a double-breasted chef's jacket with a stand-up collar
        for (const sx of [-1, 1]) for (let k = 0; k < 3; k++) body.add(sphere(0.016, accent, sx * 0.06, 0.18 + k * 0.08, 0.15, 0, 6));
        body.add(part(new THREE.CylinderGeometry(0.11, 0.12, 0.05, 14, 1, true), shirt, 0, 0.44, 0, 0.1));
        body.add(part(new THREE.BoxGeometry(0.2, 0.03, 0.02), accent, 0, 0.47, 0.11));
    }
    if (cut === 'overalls') {
        body.add(part(new THREE.BoxGeometry(0.26, 0.2, 0.02), uni ? accent : (color || '#2F6F8F'), 0, 0.12, 0.14));
        for (const sx of [-1, 1]) body.add(part(new THREE.BoxGeometry(0.035, 0.2, 0.02), uni ? accent : (color || '#2F6F8F'), sx * 0.08, 0.3, 0.14));
    }

    // A big round head with big eyes (the cartoon look)
    const head = new THREE.Group();
    head.position.y = 0.68;
    body.add(head);
    const skull = sphere(0.23, skin, 0, 0, 0, 0.05, 22);
    skull.scale.set(1, 0.95, 0.95);
    head.add(skull);
    const eyes = [], brows = [];
    for (const sx of [-1, 1]) {
        const eye = new THREE.Group(); // one group per eye, so it can blink (scale.y)
        eye.position.set(sx * 0.08, 0.02, 0.19);
        const white = sphere(0.055, '#FFFFFF', 0, 0, 0, 0, 12);
        white.scale.set(0.85, 1.1, 0.5);
        eye.add(white, sphere(0.034, '#2A1D17', 0, -0.005, 0.025, 0, 10), sphere(0.012, '#FFFFFF', 0.012, 0.015, 0.045, 0, 6));
        head.add(eye);
        eyes.push(eye);
        const brow = part(new THREE.BoxGeometry(0.07, 0.015, 0.015), '#3B2418', sx * 0.085, 0.1, 0.205);
        brow.rotation.z = sx * -0.12;
        brow.userData.sx = sx;
        head.add(brow);
        brows.push(brow);
        const cheek = sphere(0.035, '#F29A9A', sx * 0.14, -0.06, 0.17, 0, 8);
        cheek.scale.z = 0.35;
        head.add(cheek);
    }
    head.add(sphere(0.025, '#E9967A', 0, -0.02, 0.225, 0, 8)); // nose
    const smile = part(new THREE.TorusGeometry(0.04, 0.009, 6, 14, Math.PI), '#7A3B2E', 0, -0.085, 0.205);
    smile.rotation.z = Math.PI;
    const mouthO = sphere(0.032, '#6B2A22', 0, -0.09, 0.2, 0, 10); // an open mouth (chewing, laughing)
    mouthO.scale.set(1.1, 0.8, 0.4);
    mouthO.visible = false;
    head.add(smile, mouthO);
    // Accessories: glasses for some, and (customers) earrings, a moustache or a beard
    const hairC = look.hairColor || HAIR[Math.floor(rnd(4) * HAIR.length)];
    // Staff wear what the player chose; customers come as they are
    const acc = customer ? {glasses: rnd(8) > 0.78 ? (rnd(16) > 0.5 ? 'round' : 'square') : 'none', facial: rnd(10) > 0.8 ? 'beard' : rnd(10) > 0.66 ? 'moustache' : 'none', headwear: 'none'}
        : {glasses: look.glasses || 'none', facial: look.facial || 'none', headwear: look.headwear || 'none'};
    const frame = customer && rnd(16) > 0.5 ? PALETTE.berry : '#2A1D17';
    if (acc.glasses === 'round' || acc.glasses === 'square') {
        for (const sx of [-1, 1]) head.add(acc.glasses === 'round' ? part(new THREE.TorusGeometry(0.048, 0.008, 6, 16), frame, sx * 0.08, 0.02, 0.228)
            : part(new THREE.TorusGeometry(0.055, 0.009, 4, 4), frame, sx * 0.08, 0.02, 0.228).rotateZ(Math.PI / 4));
        head.add(part(new THREE.BoxGeometry(0.06, 0.01, 0.01), frame, 0, 0.03, 0.235));
    } else if (acc.glasses === 'sun' || acc.glasses === 'heart') {
        for (const sx of [-1, 1]) {
            const lens = sphere(0.055, acc.glasses === 'sun' ? '#1E1E24' : '#F25F5C', sx * 0.08, 0.02, 0.23, 0, 12);
            lens.scale.set(1, acc.glasses === 'sun' ? 0.75 : 0.9, 0.25);
            head.add(lens);
        }
        head.add(part(new THREE.BoxGeometry(0.06, 0.012, 0.01), '#1E1E24', 0, 0.035, 0.24));
    }
    if (customer && rnd(9) > 0.7) for (const sx of [-1, 1]) head.add(sphere(0.018, '#E2B23A', sx * 0.23, -0.06, 0.01, 0, 8));
    if (acc.facial === 'beard') {
        const beard = sphere(0.1, hairC, 0, -0.16, 0.1, 0.06, 12);
        beard.scale.set(1.35, 0.75, 0.8);
        head.add(beard);
    } else if (acc.facial === 'moustache') {
        for (const sx of [-1, 1]) head.add(capsule(0.014, 0.04, hairC, sx * 0.03, -0.05, 0.22, 0.1).rotateZ(sx * 1.2));
    } else if (acc.facial === 'goatee') {
        const g2 = sphere(0.04, hairC, 0, -0.19, 0.17, 0.08, 10);
        g2.scale.set(1, 1.3, 0.8);
        head.add(g2);
    }
    const headwear = (kind, c) => {
        if (kind === 'cap') {
            head.add(part(new THREE.SphereGeometry(0.25, 16, 8, 0, Math.PI * 2, 0, Math.PI / 2), c, 0, 0.04, -0.01, 0.05));
            head.add(part(new THREE.CylinderGeometry(0.16, 0.16, 0.02, 16, 1, false, -Math.PI / 2, Math.PI), c, 0, 0.06, 0.16));
        } else if (kind === 'beret') {
            const b = sphere(0.24, c, -0.03, 0.17, -0.02, 0.05, 14);
            b.scale.set(1.1, 0.35, 1.1);
            b.rotation.z = 0.25;
            head.add(b, sphere(0.02, c, -0.03, 0.27, -0.02, 0, 6));
        } else if (kind === 'bandana') {
            head.add(part(new THREE.TorusGeometry(0.225, 0.03, 6, 20), c, 0, 0.1, 0, 0.06).rotateX(Math.PI / 2 - 0.15));
            head.add(sphere(0.045, c, 0, 0.08, -0.25, 0.06, 8), part(new THREE.ConeGeometry(0.03, 0.12, 4), c, 0.03, 0.0, -0.26));
        } else if (kind === 'beanie') {
            head.add(part(new THREE.SphereGeometry(0.255, 16, 8, 0, Math.PI * 2, 0, Math.PI / 1.9), c, 0, 0.03, -0.01, 0.05));
            head.add(part(new THREE.TorusGeometry(0.235, 0.035, 6, 20), c, 0, 0.04, -0.01, 0.06).rotateX(Math.PI / 2), sphere(0.06, '#FFFFFF', 0, 0.32, -0.02, 0.06, 8));
        } else if (kind === 'flower') {
            for (let i = 0; i < 5; i++) head.add(sphere(0.035, '#F7A1C4', 0.19 + Math.cos(i * 1.26) * 0.04, 0.12 + Math.sin(i * 1.26) * 0.04, 0.08, 0.08, 6));
            head.add(sphere(0.025, '#FFD23F', 0.19, 0.12, 0.1, 0, 6));
        } else if (kind === 'bow') {
            for (const sx of [-1, 1]) {
                const w = part(new THREE.ConeGeometry(0.06, 0.12, 4), c, sx * 0.07, 0.26, -0.02, 0.08);
                w.rotation.z = sx * Math.PI / 2;
                head.add(w);
            }
            head.add(sphere(0.03, c, 0, 0.26, -0.02, 0.06, 6));
        }
    };
    head.add(sphere(0.045, skin, -0.225, 0, 0, 0.08, 8), sphere(0.045, skin, 0.225, 0, 0, 0.08, 8)); // ears

    // Hair: the staff choose theirs; customers get a random one (sometimes a hat)
    const hairStyle = look.hair || STYLES[Math.floor(rnd(5) * STYLES.length)];
    const hairColor = look.hairColor || HAIR[Math.floor(rnd(4) * HAIR.length)];
    if (acc.headwear !== 'none') {
        // A hat of the player's choosing replaces the chef's toque and the cleaner's cap
        head.add(hairMesh(['afro', 'mohawk', 'topknot', 'spiky'].includes(hairStyle) && ['cap', 'beanie', 'beret'].includes(acc.headwear) ? 'short' : hairStyle, hairColor));
        headwear(acc.headwear, accent);
    } else if (role === 'chef') {
        if (hairStyle !== 'bald') head.add(hairMesh('short', hairColor));
        const hat = new THREE.Group();
        hat.add(part(new THREE.CylinderGeometry(0.19, 0.17, 0.2, 18), '#FFFFFF', 0, 0.2, 0, 0.05));
        hat.add(sphere(0.2, '#FFFFFF', 0, 0.38, 0, 0.05, 14), sphere(0.13, '#FFFFFF', 0.12, 0.36, 0.04, 0.05, 12), sphere(0.13, '#FFFFFF', -0.12, 0.36, -0.03, 0.05, 12));
        head.add(hat);
    } else {
        head.add(hairMesh(hairStyle, hairColor));
        if (role === 'cleaner') { // a cap
            head.add(part(new THREE.SphereGeometry(0.25, 16, 8, 0, Math.PI * 2, 0, Math.PI / 2), uni ? accent : (color || '#2F6F8F'), 0, 0.04, -0.01, 0.05));
            head.add(part(new THREE.CylinderGeometry(0.16, 0.16, 0.02, 16, 1, false, -Math.PI / 2, Math.PI), uni ? accent : (color || '#2F6F8F'), 0, 0.06, 0.16));
        } else if (role === 'customer' && rnd(6) > 0.78) { // a hat
            const c = ['#3E7CB4', '#E2B23A', '#B43E5C', '#2F9E8F'][Math.floor(rnd(7) * 4)];
            head.add(part(new THREE.CylinderGeometry(0.3, 0.3, 0.02, 20), c, 0, 0.15, 0, 0.06), part(new THREE.CylinderGeometry(0.18, 0.2, 0.15, 18), c, 0, 0.22, 0, 0.05));
        }
    }

    const arm = (side) => {
        const a = new THREE.Group();
        a.position.set(side * 0.2, 0.4, 0);
        a.add(capsule(0.05, 0.2, shirt, 0, -0.14, 0, 0.1));
        a.add(sphere(0.055, skin, 0, -0.3, 0, 0.08, 10));
        body.add(a);
        return a;
    };
    const leg = (side) => {
        const l = new THREE.Group();
        l.position.set(side * 0.085, 0.5, 0);
        l.add(capsule(0.06, 0.3, pants, 0, -0.22, 0, 0.08));
        const shoe = sphere(0.075, '#2A1D17', 0, -0.46, 0.035, 0.06, 12);
        shoe.scale.set(0.9, 0.55, 1.35);
        l.add(shoe);
        p.add(l);
        return l;
    };
    const armL = arm(-1), armR = arm(1);
    p.userData = {body, head, armL, armR, legL: leg(-1), legR: leg(1), role, eyes, brows, smile, mouthO, props: makeProps(role, armL, armR, p),
        seed: rnd(17) * 10, gait: 0.9 + rnd(13) * 0.2, base: customer ? 0.93 + rnd(12) * 0.14 : 1};
    return p;
}

/**
 * Poses and motions (Sims style): walk, carry, cook, order, sit, eat, talk, wipe, wash, mop, rest, drink, wave,
 * laugh, cheer, angry, idle. t = this person's own clock in seconds; ctx = {seated, mood, energy, reading}.
 * Each pose also picks its hand props, the face (blinks, mouth, brows) follows mood and action.
 */
export function animatePerson(p, state, t, ctx = {}) {
    const u = p.userData;
    const s = Math.sin, c = Math.cos;
    const tired = ctx.energy != null && ctx.energy < 25;
    const seed = u.seed || 0;
    // Reset to a neutral pose, then add the motion of the current state
    let bodyY = 0.5, lean = 0, sway = 0, twist = 0, headX = 0, headY = 0, headZ = 0;
    let legL = 0, legR = 0, liftL = 0, liftR = 0, armL = 0, armR = 0, armLz = 0, armRz = 0, scale = 1;
    let mouth = null; // null = by mood, 'open', 'chew'
    let angry = false, eyesK = 1;
    const show = {};
    const breathe = s(t * 2.2) * 0.012;
    const sitLegs = () => (legL = legR = -1.45);
    switch (state) {
        case 'walk':
        case 'carry': {
            const g = u.gait || 1;
            const happy = ctx.mood != null && ctx.mood > 80, grumpy = ctx.mood != null && ctx.mood < 25;
            const k = (tired ? 7 : grumpy ? 11 : 9.5) * g;
            const ph = t * k, w = s(ph);
            legL = w * 0.6;
            legR = -w * 0.6;
            liftL = Math.max(0, -c(ph)) * 0.035; // the swinging foot lifts off the floor
            liftR = Math.max(0, c(ph)) * 0.035;
            bodyY += Math.abs(c(ph)) * (state === 'carry' ? 0.02 : happy ? 0.06 : 0.04);
            sway = w * 0.035; // weight shifts from foot to foot
            twist = w * 0.09; // shoulders turn against the hips
            lean = tired ? 0.16 : grumpy ? 0.12 : 0.06;
            headX = -lean * 0.7 + s(ph * 2) * 0.02;
            headY = -twist * 0.7; // the head keeps looking ahead
            if (state === 'carry') {
                armL = armR = -1.5; // both hands under the tray
                armLz = 0.12;
                armRz = -0.12;
                twist *= 0.3;
                show.tray = true;
            } else {
                armL = -w * 0.65;
                armR = w * 0.65;
                armLz = -0.06;
                armRz = 0.06;
                if (grumpy) angry = true;
            }
            break;
        }
        case 'cook': {
            // Three phases in a loop: stirring, tossing the pan, seasoning
            const cyc = (t + seed) % 9;
            bodyY += breathe;
            headX = 0.3;
            armL = -1.15;
            armLz = 0.3;
            show.pan = true;
            if (cyc < 3.5) {
                show.spatula = true;
                armR = -1.2 + s(t * 7) * 0.15;
                armRz = -0.3 + c(t * 7) * 0.22; // in circles
                sway = s(t * 3.5) * 0.03;
                twist = s(t * 7) * 0.04;
            } else if (cyc < 6.5) {
                const ph = (cyc - 3.5) % 1;
                const jerk = ph < 0.22 ? s(ph / 0.22 * Math.PI) : 0;
                armL -= jerk * 0.4;
                armR = -0.4;
                const food = u.props.pan?.userData.food;
                if (food) { // the food flies up and lands back in the pan
                    const fly = ph > 0.1 && ph < 0.75 ? (ph - 0.1) / 0.65 : 0;
                    food.position.y = 0.04 + 4 * 0.14 * fly * (1 - fly);
                    food.rotation.x = fly * Math.PI * 2;
                }
                headX = 0.15 - jerk * 0.1;
                bodyY += jerk * 0.012;
                mouth = air(ph);
            } else {
                show.shaker = true;
                armR = -1.5;
                armRz = -0.32 + s(t * 24) * 0.12; // shake, shake over the pan
                headX = 0.2;
                headZ = 0.08;
            }
            break;
        }
        case 'order': {
            // Notepad up, writing; now and then a look up at the guest and a nod
            bodyY += breathe;
            show.pad = show.pen = true;
            armL = -1.25;
            armLz = 0.5;
            const writing = s(t * 2.1 + seed) > -0.3;
            if (writing) {
                headX = 0.32;
                armR = -1.15 + s(t * 17) * 0.04;
                armRz = -0.42 + s(t * 8.5) * 0.06;
            } else {
                headX = -0.08 + s(t * 9) * 0.06;
                headZ = 0.06;
                armR = -0.95;
                armRz = -0.32;
                mouth = 'open';
            }
            break;
        }
        case 'sit':
            sitLegs();
            bodyY = 0.5 + breathe;
            if (ctx.reading) { // reading the menu
                show.menu = true;
                armL = -1.25;
                armLz = 0.35;
                armR = -1.1;
                armRz = -0.45;
                headX = 0.25;
                headY = s(t * 0.8) * 0.12;
            } else {
                armL = armR = -0.55;
                headY = s(t * 0.6) * 0.45; // looking around the room
                headX = s(t * 0.9) * 0.06;
                if (ctx.mood != null && ctx.mood < 35) { // impatient: drumming fingers, head down
                    armR = -0.9 + Math.abs(s(t * 12)) * 0.15;
                    headX = 0.25;
                    headY = 0;
                }
            }
            break;
        case 'eat': {
            sitLegs();
            bodyY = 0.5 + breathe;
            show.fork = show.knife = true;
            const cyc = (t + seed) % 6;
            armL = -0.75;
            armLz = 0.28;
            if (cyc < 1.6) { // cutting
                armR = -0.75 + s(t * 14) * 0.07;
                armRz = -0.28 + s(t * 14) * 0.05;
                armL += s(t * 14 + 1) * 0.02;
                headX = 0.32;
            } else if (cyc < 2.7) { // a bite
                const k = s((cyc - 1.6) / 1.1 * Math.PI);
                armR = -0.75 - k * 1.45;
                armRz = -0.28 - k * 0.2;
                headX = 0.25 - k * 0.35;
                if (k > 0.55) mouth = 'open';
            } else { // chewing, enjoying it
                armR = -0.72;
                armRz = -0.28;
                headX = 0.06 + s(t * 9) * 0.025;
                headY = s(t * 0.7) * 0.25;
                mouth = cyc < 4.4 ? 'chew' : null;
                if (cyc > 4.6 && cyc < 5.4 && (ctx.mood ?? 60) > 60) { // "mmh!"
                    headX += s(t * 10) * 0.07;
                    eyesK = 0.35;
                }
            }
            break;
        }
        case 'talk':
            armR = -0.8 + s(t * 6) * 0.35;
            armRz = -0.3 + s(t * 4) * 0.2;
            armL = -0.5 + s(t * 5 + 2) * 0.25;
            armLz = 0.25;
            headZ = s(t * 3) * 0.08;
            headX = s(t * 6) * 0.05;
            bodyY += breathe;
            mouth = s(t * 11) > 0 ? 'open' : null;
            break;
        case 'wipe':
            show.cloth = true;
            armR = -1.15 + c(t * 7) * 0.12; // circles on the surface
            armRz = -0.35 + s(t * 7) * 0.3;
            armL = -1.0;
            armLz = 0.12;
            lean = 0.3;
            twist = s(t * 7) * 0.08;
            headX = 0.35;
            bodyY += Math.abs(s(t * 7)) * 0.008;
            break;
        case 'wash':
            show.cloth = true;
            armL = -1.2 + s(t * 10) * 0.12; // scrubbing, hands taking turns
            armR = -1.2 - s(t * 10) * 0.12;
            armLz = 0.32;
            armRz = -0.32;
            lean = 0.22;
            headX = 0.4;
            sway = s(t * 5) * 0.02;
            break;
        case 'mop': {
            show.mop = true;
            const ph = t * 5;
            armL = armR = -0.9;
            armLz = 0.38 + s(ph) * 0.12;
            armRz = -0.38 + s(ph) * 0.12;
            twist = s(ph) * 0.25;
            lean = 0.18;
            headX = 0.35;
            legL = s(ph) * 0.08;
            legR = -legL;
            if (u.props.mop) u.props.mop.rotation.set(-0.62 + c(ph * 2) * 0.06, twist, s(ph) * 0.35);
            break;
        }
        case 'water': { // pouring from the watering can, a little lean, and a happy look at the plant
            show.can = true;
            armR = -1.0 + s(t * 2) * 0.06;
            armRz = -0.15;
            armL = -0.3;
            lean = 0.15;
            headX = 0.35;
            bodyY += breathe;
            const st = u.props.can?.userData.stream;
            if (st) st.scale.y = 0.8 + Math.abs(s(t * 9)) * 0.3;
            break;
        }
        case 'rest':
            // A long stretch now and then, otherwise slow breathing with drooping head
            if (s(t * 0.7) > 0.85) {
                armL = armR = -3.0;
                armLz = 0.3;
                armRz = -0.3;
                headX = -0.3;
                mouth = 'open'; // a yawn
                eyesK = 0.2;
            } else {
                headX = 0.2 + s(t * 1.2) * 0.05;
                armL = armR = 0.1;
                eyesK = 0.6;
            }
            bodyY += s(t * 1.2) * 0.02;
            break;
        case 'drink': {
            show.cup = true;
            const sip = Math.max(0, s(t * 1.6));
            armR = -1.1 - sip * 0.85;
            armRz = -0.4;
            headX = -sip * 0.2;
            bodyY += breathe;
            if (sip > 0.8) eyesK = 0.3;
            break;
        }
        case 'wave':
            if (ctx.seated) sitLegs();
            armR = -2.8;
            armRz = -0.4 + s(t * 14) * 0.45;
            headZ = 0.12;
            mouth = 'open';
            break;
        case 'laugh':
            if (ctx.seated) sitLegs();
            bodyY += Math.abs(s(t * 16)) * 0.03;
            headX = -0.25 + s(t * 16) * 0.08;
            armL = armR = -0.5;
            armLz = 0.3;
            armRz = -0.3;
            mouth = 'open';
            eyesK = 0.3;
            break;
        case 'cheer':
            armL = armR = -2.9;
            armLz = 0.35 + s(t * 10) * 0.1;
            armRz = -0.35 - s(t * 10) * 0.1;
            if (!ctx.seated) bodyY += Math.abs(s(t * 8)) * 0.18; // little jumps
            else sitLegs();
            headX = -0.2;
            mouth = 'open';
            break;
        case 'angry':
            armL = armR = -0.3 + Math.abs(s(t * 18)) * 0.4;
            armLz = 0.5;
            armRz = -0.5;
            bodyY += Math.abs(s(t * 9)) * 0.03;
            headZ = s(t * 18) * 0.1;
            angry = true;
            break;
        default: // idle
            bodyY += breathe;
            headY = s(t * 0.5) * 0.35;
            armL = s(t * 1.1) * 0.05;
            armR = -s(t * 1.1) * 0.05;
            if (tired) {
                headX = 0.3;
                lean = 0.1;
            }
            // A happy little bounce when in a very good mood
            if (ctx.mood != null && ctx.mood > 85) bodyY += Math.abs(s(t * 4)) * 0.02;
    }
    u.body.position.y = bodyY;
    u.body.rotation.set(lean, twist, sway);
    u.head.rotation.set(headX, headY, headZ);
    u.legL.position.y = 0.5 + liftL;
    u.legR.position.y = 0.5 + liftR;
    u.legL.rotation.x = legL;
    u.legR.rotation.x = legR;
    u.armL.rotation.set(armL, 0, armLz);
    u.armR.rotation.set(armR, 0, armRz);
    p.scale.setScalar(scale * (u.base || 1));

    // Props: only the ones this pose uses; hand props are kept level against the arm's swing
    if (u.props) for (const [name, g] of Object.entries(u.props)) {
        g.visible = !!show[name];
        if (!g.visible || name === 'tray' || name === 'mop') continue;
        const left = g.parent === u.armL;
        g.rotation.set(-(left ? armL : armR) - lean, 0, -(left ? armLz : armRz));
        if (name === 'cup') g.rotation.x += Math.max(0, s(t * 1.6)) * 0.9; // tipped to drink
        if (name === 'can') g.rotation.x += 0.6; // tipped to pour
    }

    // The face: blinking, then mouth and brows from the action or the mood
    if (u.eyes) {
        const blink = (t * 0.27 + seed) % 1 < 0.035 ? 0.1 : 1;
        const k = Math.min(blink, eyesK, tired ? 0.55 : 1);
        for (const e of u.eyes) e.scale.y = k;
        const mood = ctx.mood ?? (ctx.energy != null ? 45 + ctx.energy * 0.5 : 75);
        const open = mouth === 'open' || (mouth === 'chew' && s(t * 9) > 0);
        u.mouthO.visible = open;
        u.smile.visible = !open;
        if (open) u.mouthO.scale.set(1.1, mouth === 'chew' ? 0.45 : 0.85, 0.4);
        else if (angry || mood < 30) { // a frown
            u.smile.rotation.z = 0;
            u.smile.position.y = -0.115;
            u.smile.scale.set(0.9, 0.8, 1);
        } else {
            u.smile.rotation.z = Math.PI;
            u.smile.position.y = -0.085;
            u.smile.scale.set(1, mood < 55 ? 0.3 : 1, 1); // flat when so-so, a smile when happy
        }
        const cross = angry || mood < 25;
        for (const b of u.brows) {
            b.rotation.z = b.userData.sx * (cross ? 0.38 : -0.12);
            b.position.y = cross ? 0.085 : 0.1;
        }
    }
}

const air = (ph) => (ph > 0.1 && ph < 0.75 ? 'open' : null); // the cook's "hop!" while tossing

// ---------------------------------------------------------------- dishes and crops

/** Smooth, slightly glossy food materials. */
const food = (color, opts = {}) => mat(color, {flat: false, roughness: 0.55, ...opts});

function plateMesh(g, r = 0.17, color = '#FFFFFF') {
    g.add(mesh(new THREE.CylinderGeometry(r, r * 0.8, 0.025, 24), food(color, {roughness: 0.3}), 0, 0.012, 0));
    const rim = mesh(new THREE.TorusGeometry(r - 0.012, 0.012, 6, 28), food('#EEF0F2', {roughness: 0.3}), 0, 0.027, 0);
    rim.rotation.x = Math.PI / 2;
    g.add(rim);
}

const leafBasil = (x, y, z, rot = 0) => {
    const l = mesh(new THREE.SphereGeometry(0.022, 8, 6), food('#3F8F3A'), x, y, z);
    l.scale.set(1, 0.3, 1.8);
    l.rotation.y = rot;
    return l;
};

/** A slice of a round cake: a wedge with layers. */
function cakeSlice(g, base, cream, top, y = 0.03) {
    const wedge = (h, color, yy, r = 0.12) => {
        const m = mesh(new THREE.CylinderGeometry(r, r, h, 16, 1, false, 0, Math.PI / 3), food(color), 0, yy + h / 2, 0);
        m.position.x = -0.05;
        m.position.z = -0.05;
        return m;
    };
    g.add(wedge(0.04, base, y), wedge(0.012, cream, y + 0.04), wedge(0.04, base, y + 0.052), wedge(0.014, top, y + 0.092, 0.122));
}

const DISHES_3D = {
    bruschetta: (g) => {
        plateMesh(g);
        for (const [x, rot] of [[-0.06, 0.3], [0.06, -0.3]]) {
            const bread = mesh(new THREE.BoxGeometry(0.1, 0.03, 0.16), food('#D9A45C'), x, 0.045, 0);
            bread.rotation.y = rot;
            g.add(bread);
            for (let i = 0; i < 5; i++) g.add(mesh(new THREE.BoxGeometry(0.022, 0.02, 0.022), food('#D7263D'), x + (i % 2 ? 0.018 : -0.018), 0.07, -0.05 + i * 0.025));
            g.add(leafBasil(x, 0.078, 0.02, rot));
        }
    },
    fries: (g) => {
        const box_ = mesh(new THREE.CylinderGeometry(0.075, 0.055, 0.1, 4, 1, true), food('#D7263D'), 0, 0.05, 0);
        box_.rotation.y = Math.PI / 4;
        box_.material = box_.material.clone();
        box_.material.side = THREE.DoubleSide;
        g.add(box_);
        for (let i = 0; i < 12; i++) {
            const f = mesh(new THREE.BoxGeometry(0.015, 0.11, 0.015), food('#F4C542'), Math.cos(i * 2.4) * 0.03, 0.1 + (i % 3) * 0.012, Math.sin(i * 2.4) * 0.03);
            f.rotation.set((i % 3 - 1) * 0.2, 0, (i % 2 ? 0.2 : -0.15));
            g.add(f);
        }
        g.add(mesh(new THREE.CylinderGeometry(0.03, 0.03, 0.02, 12), food('#E63946'), 0.1, 0.01, 0.06));
    },
    tomato_soup: (g) => {
        plateMesh(g, 0.16);
        g.add(mesh(new THREE.SphereGeometry(0.12, 20, 10, 0, Math.PI * 2, Math.PI / 2, Math.PI / 2), food('#FFFFFF', {roughness: 0.3}), 0, 0.14, 0));
        g.add(mesh(new THREE.CylinderGeometry(0.11, 0.11, 0.01, 22), food('#D7442B', {roughness: 0.25}), 0, 0.125, 0));
        const swirl = mesh(new THREE.TorusGeometry(0.035, 0.006, 6, 18), food('#FFF3E0'), 0.02, 0.132, 0.01);
        swirl.rotation.x = Math.PI / 2;
        g.add(swirl, leafBasil(-0.03, 0.135, -0.02), mesh(new THREE.BoxGeometry(0.12, 0.02, 0.05), food('#D9A45C'), 0.16, 0.03, 0.02));
    },
    lemonade: (g) => {
        g.add(mesh(new THREE.CylinderGeometry(0.06, 0.05, 0.19, 16, 1, true), food('#FFFFFF', {opacity: 0.35, roughness: 0.05}), 0, 0.095, 0));
        g.add(mesh(new THREE.CylinderGeometry(0.055, 0.046, 0.15, 16), food('#F6E27A', {opacity: 0.8, roughness: 0.1}), 0, 0.077, 0));
        for (let i = 0; i < 3; i++) g.add(mesh(new THREE.BoxGeometry(0.025, 0.025, 0.025), food('#E8F6FF', {opacity: 0.7, roughness: 0.05}), (i - 1) * 0.022, 0.14, (i % 2) * 0.015));
        const lemon = mesh(new THREE.CylinderGeometry(0.035, 0.035, 0.008, 16), food('#F6C945'), 0.05, 0.18, 0);
        lemon.rotation.z = Math.PI / 2.4;
        const straw = mesh(new THREE.CylinderGeometry(0.006, 0.006, 0.25, 6), food('#F7A1C4'), -0.02, 0.17, 0.01);
        straw.rotation.z = 0.25;
        g.add(lemon, straw);
    },
    cappuccino: (g) => {
        g.add(mesh(new THREE.CylinderGeometry(0.1, 0.08, 0.015, 20), food('#FFFFFF', {roughness: 0.3}), 0, 0.008, 0));
        g.add(mesh(new THREE.CylinderGeometry(0.062, 0.045, 0.085, 18), food('#FFFFFF', {roughness: 0.3}), 0, 0.058, 0));
        g.add(mesh(new THREE.CylinderGeometry(0.058, 0.058, 0.006, 18), food('#E9D3B4', {roughness: 0.4}), 0, 0.1, 0));
        const heart = mesh(new THREE.SphereGeometry(0.018, 8, 6), food('#B98A5E'), 0, 0.104, 0);
        heart.scale.set(1.2, 0.2, 1);
        const handle = mesh(new THREE.TorusGeometry(0.022, 0.007, 6, 12), food('#FFFFFF'), 0.07, 0.06, 0);
        g.add(heart, handle, mesh(new THREE.BoxGeometry(0.035, 0.008, 0.02), food('#5A3218'), -0.07, 0.02, 0.05));
    },
    omelette: (g) => {
        plateMesh(g);
        const om = mesh(new THREE.SphereGeometry(0.1, 16, 8, 0, Math.PI), food('#F6D55C'), 0, 0.03, 0);
        om.scale.set(1, 0.35, 0.7);
        om.rotation.y = Math.PI / 2;
        g.add(om);
        for (let i = 0; i < 4; i++) g.add(mesh(new THREE.SphereGeometry(0.01, 6, 4), food(i % 2 ? '#3F8F3A' : '#D7263D'), -0.05 + i * 0.03, 0.065, 0.02));
        g.add(leafBasil(0.09, 0.035, 0.07));
    },
    pasta_tomato: (g) => {
        plateMesh(g, 0.18);
        for (let i = 0; i < 9; i++) {
            const n = mesh(new THREE.TorusGeometry(0.04 + (i % 3) * 0.015, 0.006, 6, 20), food('#F2D49B'), Math.cos(i * 2.2) * 0.03, 0.04 + (i % 4) * 0.008, Math.sin(i * 2.2) * 0.03);
            n.rotation.set(Math.PI / 2 + (i % 3 - 1) * 0.4, i * 0.6, 0);
            g.add(n);
        }
        const sauce = mesh(new THREE.SphereGeometry(0.05, 12, 8), food('#C8301E', {roughness: 0.3}), 0, 0.07, 0);
        sauce.scale.y = 0.4;
        g.add(sauce, leafBasil(0.01, 0.09, 0), leafBasil(-0.02, 0.088, 0.02, 1.2));
        for (let i = 0; i < 5; i++) g.add(mesh(new THREE.BoxGeometry(0.008, 0.004, 0.008), food('#FFF8E7'), Math.cos(i) * 0.04, 0.085, Math.sin(i * 2) * 0.04));
    },
    pizza: (g) => {
        plateMesh(g, 0.2, '#C98F5A');
        const crust = mesh(new THREE.TorusGeometry(0.155, 0.022, 8, 28), food('#E3A55B'), 0, 0.04, 0);
        crust.rotation.x = Math.PI / 2;
        g.add(crust, mesh(new THREE.CylinderGeometry(0.16, 0.16, 0.012, 28), food('#E8B26B'), 0, 0.032, 0),
            mesh(new THREE.CylinderGeometry(0.145, 0.145, 0.008, 28), food('#D7442B', {roughness: 0.35}), 0, 0.04, 0));
        for (let i = 0; i < 6; i++) { // mozzarella
            const m = mesh(new THREE.SphereGeometry(0.025, 10, 6), food('#FFF8E7'), Math.cos(i * 1.05) * 0.08, 0.046, Math.sin(i * 1.05) * 0.08);
            m.scale.y = 0.3;
            g.add(m);
        }
        for (let i = 0; i < 4; i++) g.add(leafBasil(Math.cos(i * 1.6 + 0.4) * 0.05, 0.05, Math.sin(i * 1.6 + 0.4) * 0.05, i));
    },
    carrot_cake: (g) => {
        plateMesh(g, 0.14);
        cakeSlice(g, '#C98A3D', '#FFF4E6', '#FFF4E6');
        const carrot = mesh(new THREE.ConeGeometry(0.012, 0.04, 6), food('#F08A24'), -0.02, 0.13, -0.02);
        carrot.rotation.z = Math.PI / 2;
        g.add(carrot, mesh(new THREE.ConeGeometry(0.006, 0.015, 4), food('#5E9A3E'), 0.002, 0.13, -0.02).rotateZ(-Math.PI / 2));
    },
    burger: (g) => {
        plateMesh(g);
        g.add(mesh(new THREE.CylinderGeometry(0.075, 0.07, 0.03, 18), food('#D9A45C'), 0, 0.04, 0));
        const lettuce = mesh(new THREE.CylinderGeometry(0.085, 0.085, 0.01, 12), food('#7CB342'), 0, 0.06, 0);
        g.add(lettuce, mesh(new THREE.CylinderGeometry(0.075, 0.075, 0.03, 18), food('#6B3B22', {roughness: 0.7}), 0, 0.078, 0));
        const cheese = mesh(new THREE.BoxGeometry(0.12, 0.008, 0.12), food('#F6C945'), 0, 0.097, 0);
        cheese.rotation.y = Math.PI / 4;
        g.add(cheese, mesh(new THREE.CylinderGeometry(0.07, 0.07, 0.012, 16), food('#D7263D'), 0, 0.107, 0));
        const top = mesh(new THREE.SphereGeometry(0.08, 18, 10, 0, Math.PI * 2, 0, Math.PI / 2), food('#D9A45C'), 0, 0.112, 0);
        top.scale.y = 0.75;
        g.add(top);
        for (let i = 0; i < 9; i++) {
            const seed = mesh(new THREE.SphereGeometry(0.006, 5, 4), food('#FFF3E0'), Math.cos(i * 2.4) * 0.04 * (i % 3 ? 1 : 0.5), 0.165 - (i % 3 ? 0.008 : 0), Math.sin(i * 2.4) * 0.04);
            seed.scale.set(1, 0.5, 1.5);
            g.add(seed);
        }
    },
    risotto: (g) => {
        plateMesh(g, 0.17);
        const mound = mesh(new THREE.SphereGeometry(0.09, 16, 10, 0, Math.PI * 2, 0, Math.PI / 2), food('#F1E3B5', {roughness: 0.7}), 0, 0.025, 0);
        mound.scale.y = 0.45;
        g.add(mound);
        for (let i = 0; i < 4; i++) {
            const cap = mesh(new THREE.SphereGeometry(0.02, 8, 5, 0, Math.PI * 2, 0, Math.PI / 2), food('#8B6B4A'), Math.cos(i * 1.6) * 0.035, 0.06, Math.sin(i * 1.6) * 0.035);
            g.add(cap);
        }
        g.add(leafBasil(0, 0.07, 0));
    },
    strawberry_tart: (g) => {
        plateMesh(g, 0.15);
        g.add(mesh(new THREE.CylinderGeometry(0.11, 0.1, 0.03, 20), food('#D9A45C'), 0, 0.04, 0), mesh(new THREE.CylinderGeometry(0.095, 0.095, 0.01, 20), food('#FFF4E6'), 0, 0.058, 0));
        for (let i = 0; i < 8; i++) {
            const st = mesh(new THREE.ConeGeometry(0.018, 0.035, 8), food('#E63946', {roughness: 0.3}), Math.cos(i * 0.785) * 0.065, 0.078, Math.sin(i * 0.785) * 0.065);
            st.rotation.x = Math.PI;
            g.add(st);
        }
        g.add(mesh(new THREE.ConeGeometry(0.02, 0.04, 8), food('#E63946', {roughness: 0.3}), 0, 0.08, 0).rotateX(Math.PI), leafBasil(0.01, 0.1, 0));
    },
    grilled_fish: (g) => {
        plateMesh(g, 0.2);
        const body = mesh(new THREE.SphereGeometry(0.06, 14, 10), food('#C9CED4', {roughness: 0.4}), -0.01, 0.045, 0);
        body.scale.set(2.2, 0.55, 0.9);
        const tail = mesh(new THREE.ConeGeometry(0.04, 0.06, 4), food('#AAB2BA'), 0.14, 0.045, 0);
        tail.rotation.z = -Math.PI / 2;
        tail.scale.z = 0.3;
        g.add(body, tail);
        for (let i = 0; i < 3; i++) g.add(mesh(new THREE.BoxGeometry(0.008, 0.005, 0.07), food('#5A3218'), -0.06 + i * 0.045, 0.078, 0).rotateY(0.4));
        const lemon = mesh(new THREE.SphereGeometry(0.03, 10, 6, 0, Math.PI), food('#F6C945'), -0.12, 0.035, 0.07);
        g.add(lemon, mesh(new THREE.SphereGeometry(0.005, 4, 3), food('#2A1D17'), -0.11, 0.055, 0.035));
    },
    stuffed_peppers: (g) => {
        plateMesh(g, 0.18);
        for (const [x, c] of [[-0.05, '#D7442B'], [0.06, '#E9A21D']]) {
            const p = mesh(new THREE.SphereGeometry(0.05, 12, 10), food(c, {roughness: 0.35}), x, 0.06, 0);
            p.scale.set(1, 1.15, 1);
            g.add(p, mesh(new THREE.CylinderGeometry(0.035, 0.035, 0.012, 12), food('#F1E3B5'), x, 0.115, 0), mesh(new THREE.CylinderGeometry(0.006, 0.008, 0.03, 6), food('#3F7A2E'), x, 0.13, 0));
        }
    },
    roast_chicken: (g) => {
        plateMesh(g, 0.19);
        for (const [x, rot] of [[-0.04, 0.4], [0.05, -0.6]]) {
            const leg = mesh(new THREE.SphereGeometry(0.045, 12, 10), food('#B8682A', {roughness: 0.45}), x, 0.05, 0);
            leg.scale.set(1.4, 0.9, 1);
            leg.rotation.y = rot;
            const bone = mesh(new THREE.CylinderGeometry(0.008, 0.008, 0.06, 6), food('#FFF3E0'), x + Math.cos(rot) * 0.07, 0.06, -Math.sin(rot) * 0.07);
            bone.rotation.z = Math.PI / 2;
            bone.rotation.y = rot;
            g.add(leg, bone);
        }
        for (let i = 0; i < 6; i++) g.add(mesh(new THREE.BoxGeometry(0.025, 0.02, 0.025), food('#F4C542'), -0.1 + (i % 3) * 0.03, 0.035, 0.08 + Math.floor(i / 3) * 0.025));
        g.add(leafBasil(0.08, 0.04, 0.08));
    },
    chocolate_cake: (g) => {
        plateMesh(g, 0.14);
        cakeSlice(g, '#5A3218', '#B07A4A', '#3B1F0E');
        g.add(mesh(new THREE.SphereGeometry(0.018, 10, 8), food('#D7263D', {roughness: 0.3}), -0.02, 0.138, -0.02),
            mesh(new THREE.SphereGeometry(0.02, 8, 6), food('#FFFFFF'), 0.07, 0.035, 0.06));
    },
};

const bowl = (g, inner, r = 0.12, outer = '#FFFFFF') => {
    g.add(mesh(new THREE.SphereGeometry(r, 20, 10, 0, Math.PI * 2, Math.PI / 2, Math.PI / 2), food(outer, {roughness: 0.3}), 0, r + 0.02, 0));
    g.add(mesh(new THREE.CylinderGeometry(r * 0.92, r * 0.92, 0.01, 22), food(inner, {roughness: 0.3}), 0, r + 0.005, 0));
};
const glassCup = (g, drink, h = 0.18) => {
    g.add(mesh(new THREE.CylinderGeometry(0.06, 0.05, h, 16, 1, true), food('#FFFFFF', {opacity: 0.35, roughness: 0.05}), 0, h / 2, 0));
    g.add(mesh(new THREE.CylinderGeometry(0.055, 0.046, h * 0.8, 16), food(drink, {opacity: 0.85, roughness: 0.1}), 0, h * 0.4, 0));
};
Object.assign(DISHES_3D, {
    caprese: (g) => {
        plateMesh(g);
        for (let i = 0; i < 6; i++) {
            const a = i * Math.PI / 3, sl = mesh(new THREE.CylinderGeometry(0.045, 0.045, 0.02, 14), food(i % 2 ? '#FFF8E7' : '#D7263D'), Math.cos(a) * 0.08, 0.04, Math.sin(a) * 0.08);
            sl.rotation.z = 0.25;
            sl.rotation.y = a;
            g.add(sl, leafBasil(Math.cos(a + 0.5) * 0.08, 0.06, Math.sin(a + 0.5) * 0.08, a));
        }
    },
    garden_salad: (g) => {
        bowl(g, '#7CB342', 0.13, '#F2EEE6');
        for (let i = 0; i < 9; i++) {
            const l = mesh(new THREE.SphereGeometry(0.04, 8, 6), food(i % 2 ? '#9CCB5A' : '#6FAF45'), Math.cos(i * 2.3) * 0.07, 0.16, Math.sin(i * 2.3) * 0.07);
            l.scale.set(1.3, 0.4, 1);
            l.rotation.y = i;
            g.add(l);
        }
        for (let i = 0; i < 4; i++) g.add(mesh(new THREE.SphereGeometry(0.022, 8, 6), food('#D7263D', {roughness: 0.3}), Math.cos(i * 1.6 + 0.4) * 0.05, 0.18, Math.sin(i * 1.6 + 0.4) * 0.05));
        g.add(mesh(new THREE.SphereGeometry(0.015, 6, 4), food('#3B3F2A'), 0.02, 0.19, -0.02));
    },
    iced_tea: (g) => {
        glassCup(g, '#C98A3D');
        for (let i = 0; i < 3; i++) g.add(mesh(new THREE.BoxGeometry(0.025, 0.025, 0.025), food('#E8F6FF', {opacity: 0.7, roughness: 0.05}), (i - 1) * 0.022, 0.13, (i % 2) * 0.015));
        const lemon = mesh(new THREE.CylinderGeometry(0.035, 0.035, 0.008, 16), food('#F6C945'), 0.05, 0.17, 0);
        lemon.rotation.z = Math.PI / 2.4;
        g.add(lemon, mesh(new THREE.SphereGeometry(0.018, 6, 4), food('#4E8A3A'), -0.03, 0.18, 0.02));
    },
    pancakes: (g) => {
        plateMesh(g);
        for (let i = 0; i < 4; i++) g.add(mesh(new THREE.CylinderGeometry(0.09, 0.09, 0.022, 20), food(i % 2 ? '#E3A55B' : '#D9984C'), 0, 0.04 + i * 0.024, 0));
        const honey = mesh(new THREE.CylinderGeometry(0.075, 0.08, 0.008, 18), food('#F2A93B', {roughness: 0.15}), 0, 0.137, 0);
        g.add(honey, mesh(new THREE.BoxGeometry(0.04, 0.02, 0.04), food('#FFF0A6'), 0, 0.15, 0));
        for (let i = 0; i < 3; i++) g.add(mesh(new THREE.SphereGeometry(0.012, 6, 4), food('#F2A93B', {roughness: 0.15}), 0.085 * Math.cos(i * 2), 0.1 - i * 0.02, 0.085 * Math.sin(i * 2)));
    },
    milkshake: (g) => {
        glassCup(g, '#F7A1C4', 0.2);
        const cream = mesh(new THREE.SphereGeometry(0.06, 12, 8), food('#FFFFFF'), 0, 0.2, 0);
        cream.scale.y = 0.7;
        const straw = mesh(new THREE.CylinderGeometry(0.006, 0.006, 0.22, 6), food('#3E7CB4'), 0.02, 0.24, 0);
        straw.rotation.z = -0.25;
        g.add(cream, mesh(new THREE.SphereGeometry(0.02, 8, 6), food('#D7263D', {roughness: 0.3}), 0, 0.25, 0), straw);
    },
    corn_soup: (g) => {
        plateMesh(g, 0.16);
        bowl(g, '#F6C945');
        for (let i = 0; i < 8; i++) g.add(mesh(new THREE.SphereGeometry(0.01, 6, 4), food('#E9A21D'), Math.cos(i * 2.4) * 0.06, 0.135, Math.sin(i * 2.4) * 0.06));
        g.add(leafBasil(0.02, 0.135, 0.01));
    },
    banana_split: (g) => {
        const dish = mesh(new THREE.BoxGeometry(0.3, 0.04, 0.12), food('#DDF1F7', {opacity: 0.6, roughness: 0.05}), 0, 0.02, 0);
        g.add(dish);
        for (const sz of [-0.03, 0.03]) {
            const b = mesh(new THREE.CapsuleGeometry(0.02, 0.22, 4, 8), food('#F6D55C'), 0, 0.05, sz);
            b.rotation.z = Math.PI / 2;
            g.add(b);
        }
        ['#F7A1C4', '#FFF3C4', '#7B4E2C'].forEach((c, i) => g.add(mesh(new THREE.SphereGeometry(0.035, 10, 8), food(c), -0.08 + i * 0.08, 0.08, 0)));
        g.add(mesh(new THREE.SphereGeometry(0.014, 6, 4), food('#D7263D', {roughness: 0.3}), 0, 0.125, 0));
    },
    tacos: (g) => {
        plateMesh(g);
        for (const x of [-0.06, 0.06]) {
            const shell = mesh(new THREE.CylinderGeometry(0.07, 0.07, 0.1, 16, 1, true, 0, Math.PI), food('#F2C76B'), x, 0.08, 0);
            shell.rotation.z = Math.PI / 2;
            shell.rotation.y = Math.PI / 2;
            shell.material = shell.material.clone();
            shell.material.side = THREE.DoubleSide;
            g.add(shell, mesh(new THREE.BoxGeometry(0.03, 0.03, 0.09), food('#7B3F22'), x, 0.07, 0), mesh(new THREE.BoxGeometry(0.04, 0.012, 0.09), food('#7CB342'), x, 0.095, 0),
                mesh(new THREE.SphereGeometry(0.012, 6, 4), food('#D7263D'), x, 0.1, 0.02), mesh(new THREE.SphereGeometry(0.012, 6, 4), food('#9C3A2A'), x, 0.1, -0.02));
        }
    },
    pumpkin_soup: (g) => {
        plateMesh(g, 0.16);
        bowl(g, '#E07B39', 0.12, '#3E7CB4');
        const swirl = mesh(new THREE.TorusGeometry(0.035, 0.006, 6, 18), food('#FFF8E7'), 0, 0.132, 0);
        swirl.rotation.x = Math.PI / 2;
        g.add(swirl);
        for (let i = 0; i < 5; i++) g.add(mesh(new THREE.SphereGeometry(0.008, 5, 4), food('#3F7A2E'), Math.cos(i) * 0.05, 0.134, Math.sin(i * 2) * 0.05));
    },
    veggie_lasagna: (g) => {
        plateMesh(g);
        for (let i = 0; i < 4; i++) {
            g.add(mesh(new THREE.BoxGeometry(0.16, 0.018, 0.12), food('#F2D49B'), 0, 0.035 + i * 0.03, 0));
            g.add(mesh(new THREE.BoxGeometry(0.155, 0.012, 0.115), food(i % 2 ? '#6B2F80' : '#C8301E'), 0, 0.05 + i * 0.03, 0));
        }
        g.add(mesh(new THREE.BoxGeometry(0.16, 0.012, 0.12), food('#F6D55C', {roughness: 0.3}), 0, 0.155, 0), leafBasil(0.02, 0.165, 0));
    },
    cream_puffs: (g) => {
        plateMesh(g, 0.15);
        for (const [x, z, y] of [[-0.05, -0.03, 0], [0.05, -0.03, 0], [0, 0.05, 0], [0, 0, 0.06]]) {
            const puff = mesh(new THREE.SphereGeometry(0.045, 12, 8), food('#E3A55B'), x, 0.06 + y, z);
            puff.scale.y = 0.85;
            g.add(puff, mesh(new THREE.TorusGeometry(0.04, 0.012, 6, 14), food('#FFF8E7'), x, 0.06 + y, z).rotateX(Math.PI / 2));
        }
        for (let i = 0; i < 12; i++) g.add(mesh(new THREE.BoxGeometry(0.008, 0.004, 0.008), food('#FFFFFF'), Math.cos(i) * 0.06, 0.11 + (i % 3) * 0.01, Math.sin(i * 1.7) * 0.06));
    },
    shrimp_pasta: (g) => {
        plateMesh(g, 0.18);
        for (let i = 0; i < 9; i++) {
            const n = mesh(new THREE.TorusGeometry(0.04 + (i % 3) * 0.015, 0.006, 6, 20), food('#F2D49B'), Math.cos(i * 2.2) * 0.03, 0.04 + (i % 4) * 0.008, Math.sin(i * 2.2) * 0.03);
            n.rotation.set(Math.PI / 2 + (i % 3 - 1) * 0.4, i * 0.6, 0);
            g.add(n);
        }
        for (let i = 0; i < 4; i++) {
            const sh = mesh(new THREE.TorusGeometry(0.022, 0.01, 6, 12, Math.PI * 1.3), food('#F58A6E', {roughness: 0.4}), Math.cos(i * 1.6) * 0.06, 0.08, Math.sin(i * 1.6) * 0.06);
            sh.rotation.set(Math.PI / 2, 0, i);
            g.add(sh);
        }
        g.add(leafBasil(0, 0.09, 0));
    },
    parmigiana: (g) => {
        plateMesh(g);
        g.add(mesh(new THREE.BoxGeometry(0.17, 0.1, 0.13), food('#C8301E'), 0, 0.075, 0));
        for (let i = 0; i < 3; i++) g.add(mesh(new THREE.BoxGeometry(0.172, 0.012, 0.132), food('#6B2F80'), 0, 0.045 + i * 0.03, 0));
        g.add(mesh(new THREE.BoxGeometry(0.17, 0.014, 0.13), food('#F6D55C', {roughness: 0.35}), 0, 0.128, 0), leafBasil(0.03, 0.14, 0.01), leafBasil(-0.03, 0.14, -0.02, 1));
    },
    pork_ribs: (g) => {
        plateMesh(g, 0.19);
        for (let i = 0; i < 4; i++) {
            const rib = mesh(new THREE.BoxGeometry(0.035, 0.035, 0.16), food('#8A3A22', {roughness: 0.35}), -0.06 + i * 0.04, 0.045, 0);
            const bone = mesh(new THREE.CylinderGeometry(0.008, 0.008, 0.03, 6), food('#FFF3E0'), -0.06 + i * 0.04, 0.05, 0.095);
            bone.rotation.x = Math.PI / 2;
            g.add(rib, bone);
        }
        for (let i = 0; i < 5; i++) g.add(mesh(new THREE.BoxGeometry(0.02, 0.02, 0.06), food('#F4C542'), 0.11, 0.035 + (i % 2) * 0.02, -0.06 + i * 0.03));
    },
    tiramisu: (g) => {
        plateMesh(g, 0.14);
        for (let i = 0; i < 4; i++) g.add(mesh(new THREE.BoxGeometry(0.13, 0.025, 0.13), food(i % 2 ? '#F4E6D0' : '#9C6B3F'), 0, 0.035 + i * 0.027, 0));
        g.add(mesh(new THREE.BoxGeometry(0.13, 0.008, 0.13), food('#5A3218', {roughness: 0.9}), 0, 0.142, 0), mesh(new THREE.BoxGeometry(0.03, 0.01, 0.015), food('#2A1D17'), 0.03, 0.15, 0.02));
    },
    sushi: (g) => {
        g.add(mesh(new THREE.BoxGeometry(0.32, 0.03, 0.14), food('#8A5A35'), 0, 0.015, 0));
        for (let i = 0; i < 4; i++) {
            const x = -0.11 + i * 0.075;
            if (i % 2) {
                g.add(mesh(new THREE.CylinderGeometry(0.03, 0.03, 0.04, 14), food('#1E2B22'), x, 0.05, 0), mesh(new THREE.CylinderGeometry(0.024, 0.024, 0.042, 14), food('#FFFFFF'), x, 0.05, 0),
                    mesh(new THREE.CylinderGeometry(0.01, 0.01, 0.044, 8), food('#F38B5A'), x, 0.05, 0));
            } else {
                g.add(mesh(new THREE.BoxGeometry(0.05, 0.025, 0.07), food('#FFFFFF'), x, 0.043, 0));
                const top = mesh(new THREE.BoxGeometry(0.055, 0.015, 0.075), food(i === 0 ? '#F58A6E' : '#F38B5A', {roughness: 0.35}), x, 0.063, 0);
                g.add(top, mesh(new THREE.BoxGeometry(0.012, 0.03, 0.078), food('#1E2B22'), x, 0.05, 0));
            }
        }
        g.add(mesh(new THREE.SphereGeometry(0.015, 6, 4), food('#7CB342'), 0.14, 0.04, 0.04), mesh(new THREE.BoxGeometry(0.04, 0.006, 0.02), food('#F7A1C4'), 0.14, 0.035, -0.04));
    },
    paella: (g) => {
        g.add(mesh(new THREE.CylinderGeometry(0.17, 0.16, 0.03, 24), food('#2C2C30', {metalness: 0.4}), 0, 0.015, 0));
        for (const sx of [-1, 1]) g.add(mesh(new THREE.TorusGeometry(0.025, 0.008, 6, 10), food('#2C2C30'), sx * 0.19, 0.025, 0).rotateX(Math.PI / 2));
        g.add(mesh(new THREE.CylinderGeometry(0.155, 0.155, 0.012, 24), food('#F2C24A', {roughness: 0.8}), 0, 0.036, 0));
        for (let i = 0; i < 6; i++) {
            const sh = mesh(new THREE.TorusGeometry(0.02, 0.009, 6, 12, Math.PI * 1.3), food('#F58A6E'), Math.cos(i) * 0.09, 0.05, Math.sin(i) * 0.09);
            sh.rotation.set(Math.PI / 2, 0, i);
            g.add(sh, mesh(new THREE.BoxGeometry(0.02, 0.01, 0.015), food(i % 2 ? '#D7442B' : '#7CB342'), Math.cos(i + 0.5) * 0.05, 0.048, Math.sin(i + 0.5) * 0.05));
        }
        g.add(mesh(new THREE.SphereGeometry(0.03, 10, 6, 0, Math.PI), food('#F6C945'), 0.03, 0.045, -0.02));
    },
    steak_fries: (g) => {
        plateMesh(g, 0.19);
        const steak = mesh(new THREE.CylinderGeometry(0.08, 0.075, 0.035, 14), food('#7B3A22', {roughness: 0.45}), -0.04, 0.045, 0);
        steak.scale.z = 0.7;
        g.add(steak);
        for (let i = 0; i < 3; i++) g.add(mesh(new THREE.BoxGeometry(0.008, 0.004, 0.08), food('#3B1A0E'), -0.08 + i * 0.04, 0.064, 0).rotateY(0.5));
        g.add(mesh(new THREE.BoxGeometry(0.03, 0.015, 0.03), food('#FFF0A6'), -0.04, 0.07, 0), leafBasil(-0.02, 0.075, 0.01));
        for (let i = 0; i < 7; i++) {
            const f = mesh(new THREE.BoxGeometry(0.014, 0.014, 0.08), food('#F4C542'), 0.08 + (i % 3) * 0.015, 0.035 + Math.floor(i / 3) * 0.015, -0.04 + (i % 4) * 0.02);
            f.rotation.y = (i % 2 ? 0.3 : -0.2);
            g.add(f);
        }
    },
});

export function dishModel(dish) {
    const g = new THREE.Group();
    (DISHES_3D[dish] || ((gg) => {
        plateMesh(gg);
        gg.add(mesh(new THREE.SphereGeometry(0.07, 12, 8), food('#E0A526'), 0, 0.05, 0));
    }))(g);
    g.traverse(o => o.isMesh && (o.castShadow = true));
    return g;
}

const CROP_LOOK = {
    tomato: '#D7263D', basil: '#4C9A2A', potato: '#C9A66B', onion: '#C77DBB', carrot: '#F08A24', garlic: '#F3EEE2',
    lemon: '#F4D35E', strawberry: '#E63946', pepper: '#E2711D', mushroom: '#D9C3A5', eggplant: '#5B2A6E',
};

/** A crop in a garden bed. stage: 0 seedling, 1 growing, 2 ripe. */
export function cropModel(crop, stage) {
    const g = new THREE.Group();
    const leaf = crop === 'basil' ? '#4C9A2A' : PALETTE.leaf;
    if (crop === 'mushroom') {
        const s = [0.4, 0.7, 1][stage];
        g.add(cyl(0.04 * s, 0.05 * s, 0.12 * s, '#F3EEE2', 0, 0, 0, 8), mesh(new THREE.SphereGeometry(0.1 * s, 10, 6, 0, Math.PI * 2, 0, Math.PI / 2), mat('#B5651D'), 0, 0.12 * s, 0));
        return g;
    }
    if (crop === 'lettuce') { // a round head of leaves
        const s = [0.4, 0.7, 1][stage];
        for (let i = 0; i < 7; i++) {
            const l = ball(0.08 * s, i % 2 ? '#9CCB5A' : '#7CB342', Math.cos(i * 0.9) * 0.06 * s, 0.06 * s, Math.sin(i * 0.9) * 0.06 * s, {}, 7);
            l.scale.set(1.2, 0.8, 1.2);
            g.add(l);
        }
        g.add(ball(0.06 * s, '#C5E384', 0, 0.1 * s, 0, {}, 7));
        return g;
    }
    if (crop === 'corn') { // a tall stalk with long leaves and (ripe) a cob
        const h = [0.18, 0.45, 0.62][stage];
        g.add(cyl(0.015, 0.025, h, '#7CB342', 0, 0, 0, 5));
        for (let i = 0; i < 2 + stage; i++) {
            const l = mesh(new THREE.ConeGeometry(0.03, 0.3, 4), mat('#6FAF45'), Math.cos(i * 2.4) * 0.06, h * (0.3 + i * 0.2), Math.sin(i * 2.4) * 0.06);
            l.rotation.set(Math.sin(i * 2.4) * 1.0, 0, -Math.cos(i * 2.4) * 1.0);
            l.scale.z = 0.3;
            g.add(l);
        }
        if (stage === 2) g.add(cyl(0.035, 0.03, 0.14, '#F6C945', 0.04, h * 0.5, 0, 8), mesh(new THREE.ConeGeometry(0.04, 0.16, 5), mat('#9CCB5A'), 0.045, h * 0.5 + 0.06, 0));
        return g;
    }
    if (crop === 'pumpkin') { // a creeping vine and, when ripe, a big pumpkin
        const s = [0.4, 0.7, 1][stage];
        for (let i = 0; i < 4; i++) {
            const l = ball(0.07 * s, PALETTE.leaf, Math.cos(i * 1.6) * 0.12, 0.04, Math.sin(i * 1.6) * 0.12, {}, 6);
            l.scale.set(1.3, 0.4, 1.3);
            g.add(l);
        }
        if (stage >= 1) {
            const p = mesh(new THREE.SphereGeometry(stage === 2 ? 0.13 : 0.06, 12, 8), mat(stage === 2 ? '#E07B39' : '#9CCB5A', {flat: false}), 0, stage === 2 ? 0.09 : 0.05, 0);
            p.scale.y = 0.75;
            g.add(p, cyl(0.012, 0.015, 0.05, '#5A3218', 0, stage === 2 ? 0.18 : 0.09, 0, 5));
        }
        return g;
    }
    const h = [0.12, 0.25, 0.32][stage];
    g.add(cyl(0.015, 0.02, h, PALETTE.leafDark, 0, 0, 0, 5));
    for (let i = 0; i < (stage + 1) * 2; i++) {
        const l = ball(0.06 + stage * 0.015, leaf, Math.cos(i * 2.4) * 0.07, h * (0.5 + (i % 3) * 0.2), Math.sin(i * 2.4) * 0.07, {}, 6);
        l.scale.set(1, 0.5, 1.4);
        l.rotation.y = i * 2.4;
        g.add(l);
    }
    if (stage === 2) {
        const c = CROP_LOOK[crop] || '#D7263D';
        const underground = ['potato', 'onion', 'carrot', 'garlic'].includes(crop);
        for (let i = 0; i < 3; i++) {
            const fruit = ball(underground ? 0.06 : 0.05, c, Math.cos(i * 2.1) * 0.09, underground ? 0.03 : h * 0.7, Math.sin(i * 2.1) * 0.09, {}, 8);
            if (crop === 'carrot') fruit.scale.set(0.8, 0.5, 0.8);
            if (crop === 'eggplant') fruit.scale.set(0.8, 1.6, 0.8);
            g.add(fruit);
        }
    }
    return g;
}

// ---------------------------------------------------------------- floors and walls (canvas textures)

const texCache = new Map();

function canvasTexture(key, draw, repeat = 1) {
    if (texCache.has(key)) return texCache.get(key);
    const c = document.createElement('canvas');
    c.width = c.height = 256;
    draw(c.getContext('2d'), 256);
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    t.wrapS = t.wrapT = THREE.RepeatWrapping;
    t.anisotropy = 4;
    t.repeat.set(repeat, repeat);
    texCache.set(key, t);
    return t;
}

const FLOOR_DRAW = {

    floor_blue: (g, s) => {
        g.fillStyle = '#EEF3F7';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 4; i++) for (let j = 0; j < 4; j++) {
            g.fillStyle = (i + j) % 2 ? '#3E7CB4' : '#9CC3E4';
            g.beginPath();
            const x = i * s / 4 + s / 8, y = j * s / 4 + s / 8;
            g.moveTo(x, y - s / 9); g.lineTo(x + s / 9, y); g.lineTo(x, y + s / 9); g.lineTo(x - s / 9, y); g.fill();
        }
    },
    floor_herringbone: (g, s) => {
        g.fillStyle = '#8A5A35';
        g.fillRect(0, 0, s, s);
        const w = s / 8, l = s / 4;
        for (let y = -s; y < s * 2; y += w * 2) for (let x = -s; x < s * 2; x += l) {
            g.save();
            g.translate(x + (Math.floor(y / (w * 2)) % 2) * l / 2, y);
            g.fillStyle = ['#C48A55', '#B67C48', '#D29A63'][Math.abs(Math.floor(x / l + y / w)) % 3];
            g.rotate(Math.PI / 4); g.fillRect(0, 0, l - 2, w - 2);
            g.rotate(-Math.PI / 2); g.fillRect(0, 0, l - 2, w - 2);
            g.restore();
        }
    },
    floor_hex: (g, s) => {
        g.fillStyle = '#2F2A28';
        g.fillRect(0, 0, s, s);
        const r = s / 8;
        for (let row = 0; row < 6; row++) for (let col = 0; col < 6; col++) {
            const x = col * r * 1.5, y = row * r * Math.sqrt(3) + (col % 2) * r * Math.sqrt(3) / 2;
            g.fillStyle = (row + col) % 5 === 0 ? '#E2B23A' : '#F4EEE4';
            g.beginPath();
            for (let k = 0; k < 6; k++) g.lineTo(x + Math.cos(k * Math.PI / 3) * (r - 2), y + Math.sin(k * Math.PI / 3) * (r - 2));
            g.fill();
        }
    },
    floor_oak: (g, s) => {
        for (let i = 0; i < 4; i++) {
            g.fillStyle = ['#C98F5A', '#BF8550', '#D29A63', '#C48A55'][i];
            g.fillRect(0, i * s / 4, s, s / 4);
            g.fillStyle = 'rgba(80,45,20,.35)';
            g.fillRect(0, i * s / 4, s, 2);
            g.fillRect(((i * 97) % 200) + 20, i * s / 4, 2, s / 4);
            g.strokeStyle = 'rgba(90,50,25,.12)';
            for (let k = 0; k < 3; k++) {
                g.beginPath();
                g.moveTo(0, i * s / 4 + 14 + k * 16);
                g.bezierCurveTo(s / 3, i * s / 4 + 8 + k * 16, s / 1.5, i * s / 4 + 22 + k * 16, s, i * s / 4 + 12 + k * 16);
                g.stroke();
            }
        }
    },
    floor_checker: (g, s) => {
        for (let x = 0; x < 2; x++) for (let y = 0; y < 2; y++) {
            g.fillStyle = (x + y) % 2 ? '#2F2A28' : '#F4EEE4';
            g.fillRect(x * s / 2, y * s / 2, s / 2, s / 2);
        }
    },
    floor_terracotta: (g, s) => {
        g.fillStyle = '#E9DCC8';
        g.fillRect(0, 0, s, s);
        for (let x = 0; x < 2; x++) for (let y = 0; y < 2; y++) {
            g.fillStyle = ['#C9663F', '#BF5C37', '#D06D45', '#C4613A'][x * 2 + y];
            g.fillRect(x * s / 2 + 4, y * s / 2 + 4, s / 2 - 8, s / 2 - 8);
        }
    },
    floor_mint: (g, s) => {
        g.fillStyle = '#F7F4EC';
        g.fillRect(0, 0, s, s);
        g.fillStyle = '#9ED2C0';
        for (let i = 0; i < 4; i++) for (let j = 0; j < 4; j++) if ((i + j) % 2 === 0) g.fillRect(i * s / 4 + 3, j * s / 4 + 3, s / 4 - 6, s / 4 - 6);
        g.fillStyle = '#E2B23A';
        for (let i = 0; i <= 4; i++) for (let j = 0; j <= 4; j++) g.fillRect(i * s / 4 - 4, j * s / 4 - 4, 8, 8);
    },
    floor_marble: (g, s) => {
        g.fillStyle = '#F3F1EC';
        g.fillRect(0, 0, s, s);
        g.strokeStyle = 'rgba(120,120,130,.25)';
        g.lineWidth = 2;
        for (let i = 0; i < 6; i++) {
            g.beginPath();
            g.moveTo(Math.sin(i * 3) * s, 0);
            g.bezierCurveTo(s * 0.3, s * (0.2 + i * 0.1), s * 0.7, s * (0.6 - i * 0.05), s, s * (0.3 + i * 0.1));
            g.stroke();
        }
        g.strokeStyle = 'rgba(200,170,90,.6)';
        g.strokeRect(1, 1, s - 2, s - 2);
    },
};

const WALL_DRAW = {

    wall_tile: (g, s) => {
        g.fillStyle = '#FFFFFF';
        g.fillRect(0, 0, s, s);
        g.fillStyle = '#DCEFF0';
        for (let r = 0; r < 12; r++) for (let c = 0; c < 6; c++) g.fillRect(c * s / 6 + (r % 2) * s / 12 + 2, r * s / 12 + 2, s / 6 - 4, s / 12 - 4);
        g.fillStyle = '#2F9E8F';
        g.fillRect(0, s * 0.6, s, 8);
    },
    wall_wood: (g, s) => {
        for (let i = 0; i < 10; i++) {
            g.fillStyle = ['#A9744A', '#9C6A42', '#B47E52'][i % 3];
            g.fillRect(i * s / 10, 0, s / 10, s);
            g.fillStyle = 'rgba(60,30,10,.35)';
            g.fillRect(i * s / 10, 0, 2, s);
        }
    },
    wall_flowers: (g, s) => {
        g.fillStyle = '#F6EFE1';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 5; i++) for (let j = 0; j < 5; j++) {
            const x = i * s / 5 + (j % 2) * s / 10 + s / 10, y = j * s / 5 + s / 10;
            g.fillStyle = ['#F25F5C', '#F7A1C4', '#E2B23A'][(i + j) % 3];
            for (let k = 0; k < 5; k++) { g.beginPath(); g.arc(x + Math.cos(k * 1.26) * 7, y + Math.sin(k * 1.26) * 7, 5, 0, Math.PI * 2); g.fill(); }
            g.fillStyle = '#7A8B3A';
            g.beginPath(); g.arc(x, y, 4, 0, Math.PI * 2); g.fill();
        }
        g.fillStyle = '#7A8B3A';
        g.fillRect(0, s * 0.62, s, 6);
    },
    wall_cream: (g, s) => {
        g.fillStyle = '#FFF0D9';
        g.fillRect(0, 0, s, s);
        g.fillStyle = '#E8D2B0';
        g.fillRect(0, s * 0.62, s, s * 0.38);
        g.fillStyle = '#C9A77E';
        g.fillRect(0, s * 0.6, s, 6);
    },
    wall_stripes: (g, s) => {
        for (let i = 0; i < 8; i++) {
            g.fillStyle = i % 2 ? '#F7E7CE' : '#EFC7A7';
            g.fillRect(i * s / 8, 0, s / 8, s);
        }
        g.fillStyle = PALETTE.woodDark;
        g.fillRect(0, s * 0.62, s, s * 0.38);
        g.fillStyle = PALETTE.wood;
        g.fillRect(0, s * 0.6, s, 6);
    },
    wall_brick: (g, s) => {
        g.fillStyle = '#E3D2C0';
        g.fillRect(0, 0, s, s);
        const bh = s / 8;
        for (let r = 0; r < 8; r++) for (let c = -1; c < 4; c++) {
            g.fillStyle = ['#B5532E', '#A84B2A', '#BF5C36', '#9E4527'][(r * 3 + c + 4) % 4];
            g.fillRect(c * s / 4 + (r % 2) * s / 8 + 3, r * bh + 3, s / 4 - 6, bh - 6);
        }
    },
    wall_sage: (g, s) => {
        g.fillStyle = '#A9BC92';
        g.fillRect(0, 0, s, s);
        g.strokeStyle = '#93A87C';
        g.lineWidth = 6;
        g.strokeRect(20, 20, s - 40, s * 0.45);
        g.strokeRect(20, s * 0.6, s - 40, s * 0.32);
        g.fillStyle = '#FFF5E6';
        g.fillRect(0, s * 0.53, s, 5);
    },
    wall_berry: (g, s) => {
        g.fillStyle = '#8E2F48';
        g.fillRect(0, 0, s, s);
        g.fillStyle = 'rgba(226,178,58,.5)';
        for (let i = 0; i < 4; i++) for (let j = 0; j < 4; j++) {
            g.beginPath();
            const x = i * s / 4 + (j % 2) * s / 8 + s / 8, y = j * s / 4 + s / 8;
            g.moveTo(x, y - 14);
            g.lineTo(x + 9, y);
            g.lineTo(x, y + 14);
            g.lineTo(x - 9, y);
            g.fill();
        }
        g.fillStyle = '#E2B23A';
        g.fillRect(0, s * 0.62, s, 5);
    },
};

/** More floors and wallpapers (one canvas tile = one floor tile, or one wall unit). */
const planks = (g, s, cols, h = 4) => {
    for (let r = 0; r < h; r++) {
        const off = (r % 2) * s / 2;
        for (let c = -1; c < 2; c++) {
            g.fillStyle = cols[(r + c + 3) % cols.length];
            g.fillRect(c * s + off + 2, r * s / h + 2, s - 4, s / h - 4);
        }
    }
};
Object.assign(FLOOR_DRAW, {
    floor_walnut: (g, s) => {
        g.fillStyle = '#3E2416';
        g.fillRect(0, 0, s, s);
        planks(g, s, ['#6B4026', '#5A3520', '#74472B', '#62391F']);
        g.strokeStyle = 'rgba(0,0,0,.18)';
        for (let i = 0; i < 12; i++) {
            g.beginPath();
            g.moveTo(Math.random() * s, (i % 4) * s / 4 + 12);
            g.bezierCurveTo(s * 0.3, (i % 4) * s / 4 + 20, s * 0.6, (i % 4) * s / 4 + 8, s, (i % 4) * s / 4 + 16);
            g.stroke();
        }
    },
    floor_slate: (g, s) => {
        g.fillStyle = '#3A3F45';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 2; i++) for (let j = 0; j < 2; j++) {
            g.fillStyle = ['#4E555C', '#5A6168', '#474D53', '#535A61'][i * 2 + j];
            g.fillRect(i * s / 2 + 3, j * s / 2 + 3, s / 2 - 6, s / 2 - 6);
        }
        g.fillStyle = 'rgba(255,255,255,.05)';
        for (let i = 0; i < 60; i++) g.fillRect(Math.random() * s, Math.random() * s, 6, 2);
    },
    floor_terrazzo: (g, s) => {
        g.fillStyle = '#F2ECE2';
        g.fillRect(0, 0, s, s);
        const cols = ['#D9653B', '#7A8B3A', '#3E7CB4', '#E2B23A', '#B9AFA3', '#8A6E5C'];
        for (let i = 0; i < 160; i++) {
            g.fillStyle = cols[i % cols.length];
            g.beginPath();
            g.arc(Math.random() * s, Math.random() * s, 1.5 + Math.random() * 4, 0, Math.PI * 2);
            g.fill();
        }
    },
    floor_diagonal: (g, s) => {
        g.fillStyle = '#F4F1EA';
        g.fillRect(0, 0, s, s);
        g.fillStyle = '#26262A';
        g.beginPath();
        g.moveTo(s / 2, 0); g.lineTo(s, s / 2); g.lineTo(s / 2, s); g.lineTo(0, s / 2);
        g.fill();
    },
    floor_cork: (g, s) => {
        g.fillStyle = '#C49A6C';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 900; i++) {
            g.fillStyle = Math.random() < 0.5 ? '#A97E52' : '#D8B285';
            g.fillRect(Math.random() * s, Math.random() * s, 3, 3);
        }
        g.strokeStyle = 'rgba(80,50,20,.35)';
        g.strokeRect(1, 1, s - 2, s - 2);
    },
    floor_mosaic: (g, s) => {
        const n = 16, cols = ['#2F5DA8', '#3E7CB4', '#5B95C7', '#9CC3E4', '#FFFFFF', '#2F9E8F'];
        g.fillStyle = '#E8EEF3';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < n; i++) for (let j = 0; j < n; j++) {
            const d = Math.hypot(i - n / 2 + 0.5, j - n / 2 + 0.5);
            g.fillStyle = cols[(Math.floor(d) + (i * j) % 2) % cols.length];
            g.fillRect(i * s / n + 1, j * s / n + 1, s / n - 2, s / n - 2);
        }
    },
    floor_gold_parquet: (g, s) => {
        g.fillStyle = '#7B4E2C';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 2; i++) for (let j = 0; j < 2; j++) for (let k = 0; k < 4; k++) {
            g.fillStyle = ['#C9954F', '#B8843F', '#D6A35C', '#C08C47'][(k + i + j) % 4];
            const x = i * s / 2, y = j * s / 2, w = s / 8;
            if ((i + j) % 2) g.fillRect(x + 2, y + k * w + 2, s / 2 - 4, w - 3);
            else g.fillRect(x + k * w + 2, y + 2, w - 3, s / 2 - 4);
        }
        g.fillStyle = '#E2B23A';
        g.fillRect(s / 2 - 3, s / 2 - 3, 6, 6);
    },
    floor_concrete: (g, s) => {
        g.fillStyle = '#B9B6B0';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 500; i++) {
            g.fillStyle = `rgba(${Math.random() < 0.5 ? '255,255,255' : '0,0,0'},.05)`;
            g.fillRect(Math.random() * s, Math.random() * s, 4, 4);
        }
        g.strokeStyle = 'rgba(0,0,0,.12)';
        g.strokeRect(0, 0, s, s);
    },
    floor_red_tiles: (g, s) => {
        g.fillStyle = '#8A3A22';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 4; i++) for (let j = 0; j < 4; j++) {
            g.fillStyle = ['#C2572B', '#B24E25', '#CC6235'][(i + j * 2) % 3];
            g.fillRect(i * s / 4 + 3, j * s / 4 + 3, s / 4 - 6, s / 4 - 6);
        }
    },
});
const stripes = (g, s, a, b, n) => {
    for (let i = 0; i < n; i++) {
        g.fillStyle = i % 2 ? a : b;
        g.fillRect(i * s / n, 0, s / n, s);
    }
};
const wainscot = (g, s, c) => {
    g.fillStyle = c;
    g.fillRect(0, s * 0.68, s, s * 0.32);
    g.fillStyle = 'rgba(255,255,255,.35)';
    g.fillRect(0, s * 0.68, s, 4);
};
Object.assign(WALL_DRAW, {
    wall_navy: (g, s) => {
        g.fillStyle = '#22315A';
        g.fillRect(0, 0, s, s);
        g.fillStyle = 'rgba(255,255,255,.06)';
        for (let i = 0; i < 8; i++) g.fillRect(i * s / 8, 0, 2, s);
        wainscot(g, s, '#F4F1EA');
    },
    wall_mint_stripes: (g, s) => {
        stripes(g, s, '#BFE6D8', '#F4FBF8', 8);
        wainscot(g, s, '#7FB8A6');
    },
    wall_stone: (g, s) => {
        g.fillStyle = '#8E8A82';
        g.fillRect(0, 0, s, s);
        for (let r = 0; r < 8; r++) for (let c = -1; c < 4; c++) {
            g.fillStyle = ['#B5AFA3', '#A8A296', '#C2BCB0', '#9A9488'][(r + c + 4) % 4];
            g.beginPath();
            g.roundRect(c * s / 3 + (r % 2) * s / 6 + 3, r * s / 8 + 3, s / 3 - 6, s / 8 - 6, 8);
            g.fill();
        }
    },
    wall_gold_damask: (g, s) => {
        g.fillStyle = '#4A1E2A';
        g.fillRect(0, 0, s, s);
        g.fillStyle = '#C9A04A';
        for (let i = 0; i < 2; i++) for (let j = 0; j < 3; j++) {
            const x = i * s / 2 + s / 4 + (j % 2) * s / 4 - s / 8, y = j * s / 3 + s / 6;
            g.beginPath();
            g.ellipse(x, y, 16, 28, 0, 0, Math.PI * 2);
            g.fill();
            g.beginPath();
            g.ellipse(x, y, 30, 8, 0, 0, Math.PI * 2);
            g.fill();
        }
        g.fillStyle = '#E2B23A';
        g.fillRect(0, s * 0.68, s, 6);
    },
    wall_greenery: (g, s) => {
        g.fillStyle = '#F2F5EC';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 28; i++) {
            g.fillStyle = ['#5E9A3E', '#3F7A2E', '#7CB342'][i % 3];
            g.save();
            g.translate((i * 37) % s, (i * 53) % s);
            g.rotate(i);
            g.beginPath();
            g.ellipse(0, 0, 9, 22, 0, 0, Math.PI * 2);
            g.fill();
            g.restore();
        }
    },
    wall_chalk: (g, s) => {
        g.fillStyle = '#2A3530';
        g.fillRect(0, 0, s, s);
        g.strokeStyle = 'rgba(255,255,255,.55)';
        g.lineWidth = 3;
        g.font = 'bold 26px sans-serif';
        g.fillStyle = 'rgba(255,255,255,.6)';
        g.fillText('☆', s * 0.15, s * 0.25);
        g.beginPath();
        g.arc(s * 0.65, s * 0.3, 22, 0, Math.PI * 2);
        g.stroke();
        g.beginPath();
        g.moveTo(s * 0.2, s * 0.5); g.lineTo(s * 0.8, s * 0.5);
        g.stroke();
        wainscot(g, s, '#7B4E2C');
    },
    wall_tropical: (g, s) => {
        g.fillStyle = '#FCEFD9';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 9; i++) {
            g.save();
            g.translate((i * 71) % s, (i * 97) % s);
            g.rotate(i * 0.8);
            g.fillStyle = i % 3 ? '#2E7D4F' : '#3D8F5C';
            for (let k = -3; k <= 3; k++) {
                g.beginPath();
                g.ellipse(k * 6, -k * k * 1.5, 5, 26, k * 0.25, 0, Math.PI * 2);
                g.fill();
            }
            g.restore();
            g.fillStyle = '#F25F5C';
            g.beginPath();
            g.arc((i * 113) % s, (i * 41) % s, 7, 0, Math.PI * 2);
            g.fill();
        }
    },
    wall_concrete: (g, s) => {
        g.fillStyle = '#C4C1BB';
        g.fillRect(0, 0, s, s);
        for (let i = 0; i < 400; i++) {
            g.fillStyle = `rgba(${Math.random() < 0.5 ? '255,255,255' : '0,0,0'},.05)`;
            g.fillRect(Math.random() * s, Math.random() * s, 3, 3);
        }
        g.fillStyle = 'rgba(0,0,0,.18)';
        for (const [x, y] of [[0.25, 0.25], [0.75, 0.25], [0.25, 0.75], [0.75, 0.75]]) {
            g.beginPath();
            g.arc(x * s, y * s, 3, 0, Math.PI * 2);
            g.fill();
        }
    },
    wall_pastel: (g, s) => {
        g.fillStyle = '#CFE6F5';
        g.fillRect(0, 0, s, s);
        wainscot(g, s, '#FFFFFF');
        g.fillStyle = '#B5D5EA';
        for (let i = 0; i < 6; i++) g.fillRect(i * s / 6 + s / 12 - 1, s * 0.72, 2, s * 0.26);
    },
});

export function floorMaterial(key, w, d = w) {
    const t = canvasTexture(key + w, FLOOR_DRAW[key] || FLOOR_DRAW.floor_oak, w).clone();
    t.needsUpdate = true;
    t.repeat.set(w, d);
    return new THREE.MeshStandardMaterial({map: t, roughness: 0.85});
}

export function wallMaterial(key) {
    return new THREE.MeshStandardMaterial({map: canvasTexture(key, WALL_DRAW[key] || WALL_DRAW.wall_cream, 1), roughness: 0.9});
}

/** A flat swatch (data URL) for the shop buttons. */
export function swatch(key) {
    const c = document.createElement('canvas');
    c.width = c.height = 96;
    const g = c.getContext('2d');
    g.scale(96 / 256, 96 / 256);
    (FLOOR_DRAW[key] || WALL_DRAW[key])(g, 256);
    return c.toDataURL();
}
