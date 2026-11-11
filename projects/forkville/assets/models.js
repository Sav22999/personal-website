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

/** 0 = full day, 1 = deep night. */
export function setNightGlow(k) {
    for (const m of GLOW_MATS.values()) m.emissiveIntensity = 0.25 + k * 1.6;
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
const STYLES = ['short', 'bob', 'bun', 'ponytail', 'curly', 'spiky', 'long'];

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
    }
    return g;
}

/**
 * A cartoon character. role: chef | waiter | cleaner | manager | customer.
 * look (staff): {outfit, hair, hairColor, uniform: {top, bottom, accent, cut}} — the uniform comes from the catalog.
 */
export function personModel(role, color, seed = 0, look = {}) {
    const rnd = (n) => Math.abs(Math.sin(seed * 12.9898 + n * 78.233) * 43758.5453) % 1;
    const skin = PALETTE.skin[Math.floor(rnd(1) * PALETTE.skin.length)];
    const uni = look.uniform && look.uniform.top ? look.uniform : null;
    const roleTop = {chef: '#FFFFFF', waiter: '#FAFAFA', cleaner: '#7FC4D9', manager: '#2C2C3A'}[role];
    const roleBottom = {chef: '#3B3B3B', waiter: '#22201F', cleaner: '#2F6F8F', manager: '#2C2C3A'}[role];
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
    torso.scale.set(1, 1, 0.85);
    body.add(torso);
    if (cut === 'stripes') for (let i = 0; i < 3; i++) body.add(part(new THREE.TorusGeometry(0.162, 0.018, 6, 20), accent, 0, 0.18 + i * 0.1, 0).rotateX(Math.PI / 2));
    if (cut === 'apron' || cut === 'stripes') body.add(part(new THREE.BoxGeometry(0.24, 0.3, 0.02), cut === 'stripes' ? '#FFFFFF' : accent, 0, 0.14, 0.145));
    if (cut === 'vest') body.add(part(new THREE.CylinderGeometry(0.165, 0.19, 0.3, 14, 1, true, -Math.PI * 0.72, Math.PI * 1.44), accent === '#FFFFFF' ? '#2B2B2B' : (role === 'waiter' && !uni ? (color || '#2B2B2B') : accent), 0, 0.24, 0));
    if (cut === 'vest' || cut === 'tux') body.add(part(new THREE.BoxGeometry(0.09, 0.035, 0.03), role === 'waiter' && !uni ? PALETTE.berry : (cut === 'tux' ? '#1B1B1B' : accent), 0, 0.42, 0.13)); // bow tie
    if (cut === 'tux') body.add(part(new THREE.BoxGeometry(0.1, 0.24, 0.02), accent, 0, 0.3, 0.138));
    if (cut === 'tie') {
        body.add(part(new THREE.BoxGeometry(0.12, 0.08, 0.02), '#FFFFFF', 0, 0.39, 0.135));
        body.add(part(new THREE.BoxGeometry(0.045, 0.2, 0.02), uni ? accent : (color || PALETTE.berry), 0, 0.27, 0.145));
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
    for (const sx of [-1, 1]) {
        const white = sphere(0.055, '#FFFFFF', sx * 0.08, 0.02, 0.19, 0, 12);
        white.scale.set(0.85, 1.1, 0.5);
        head.add(white);
        head.add(sphere(0.034, '#2A1D17', sx * 0.08, 0.015, 0.215, 0, 10));
        head.add(sphere(0.012, '#FFFFFF', sx * 0.08 + 0.012, 0.035, 0.235, 0, 6));
        const brow = part(new THREE.BoxGeometry(0.07, 0.015, 0.015), '#3B2418', sx * 0.085, 0.1, 0.205);
        brow.rotation.z = sx * -0.12;
        head.add(brow);
        const cheek = sphere(0.035, '#F29A9A', sx * 0.14, -0.06, 0.17, 0, 8);
        cheek.scale.z = 0.35;
        head.add(cheek);
    }
    head.add(sphere(0.025, '#E9967A', 0, -0.02, 0.225, 0, 8)); // nose
    const smile = part(new THREE.TorusGeometry(0.04, 0.009, 6, 14, Math.PI), '#7A3B2E', 0, -0.085, 0.205);
    smile.rotation.z = Math.PI;
    head.add(smile);
    head.add(sphere(0.045, skin, -0.225, 0, 0, 0.08, 8), sphere(0.045, skin, 0.225, 0, 0, 0.08, 8)); // ears

    // Hair: the staff choose theirs; customers get a random one (sometimes a hat)
    const hairStyle = look.hair || STYLES[Math.floor(rnd(5) * STYLES.length)];
    const hairColor = look.hairColor || HAIR[Math.floor(rnd(4) * HAIR.length)];
    if (role === 'chef') {
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
    p.userData = {body, head, armL: arm(-1), armR: arm(1), legL: leg(-1), legR: leg(1), role};
    return p;
}

/**
 * Poses and motions (Sims style): walk, carry, cook, sit, eat, talk, wipe, mop, rest, drink, wave, laugh, cheer,
 * angry, idle. t = this person's own clock in seconds; ctx = {seated, mood, energy}.
 */
export function animatePerson(p, state, t, ctx = {}) {
    const u = p.userData;
    const s = Math.sin, c = Math.cos;
    const tired = ctx.energy != null && ctx.energy < 25;
    // Reset to a neutral pose, then add the motion of the current state
    let bodyY = 0.5, lean = 0, sway = 0, twist = 0, headX = 0, headY = 0, headZ = 0;
    let legL = 0, legR = 0, armL = 0, armR = 0, armLz = 0, armRz = 0, scale = 1;
    const breathe = s(t * 2.2) * 0.012;
    switch (state) {
        case 'walk':
        case 'carry': {
            const k = tired ? 7 : 9.5;
            const w = s(t * k);
            legL = w * 0.65;
            legR = -w * 0.65;
            bodyY += Math.abs(c(t * k)) * 0.045;
            sway = w * 0.05;
            lean = tired ? 0.16 : 0.07;
            headX = -lean * 0.6 + s(t * k * 2) * 0.03;
            if (state === 'carry') {
                armL = armR = -1.35; // both hands under the tray
                armLz = 0.15;
                armRz = -0.15;
            } else {
                armL = -w * 0.7;
                armR = w * 0.7;
            }
            break;
        }
        case 'cook':
            armR = -1.25 + s(t * 7) * 0.2;
            armRz = -0.25 + c(t * 7) * 0.25; // stirring in circles
            armL = -0.9;
            sway = s(t * 3.5) * 0.04;
            headX = 0.25;
            bodyY += breathe;
            break;
        case 'sit':
        case 'eat':
            legL = legR = -1.45;
            bodyY = 0.5 + breathe;
            if (state === 'eat') {
                const bite = Math.max(0, s(t * 3.2));
                armR = -0.6 - bite * 1.6; // fork to mouth
                armRz = -bite * 0.45;
                armL = -0.6;
                headX = 0.15 - bite * 0.2;
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
        case 'talk':
            armR = -0.8 + s(t * 6) * 0.35;
            armRz = -0.3 + s(t * 4) * 0.2;
            headZ = s(t * 3) * 0.08;
            headX = s(t * 6) * 0.05;
            bodyY += breathe;
            break;
        case 'wipe':
            armR = -1.2;
            armRz = -0.4 + s(t * 9) * 0.5; // wiping side to side
            armL = -0.5;
            lean = 0.25;
            twist = s(t * 9) * 0.1;
            headX = 0.3;
            break;
        case 'mop':
            armL = armR = -1.0;
            armLz = 0.2 + s(t * 6) * 0.25;
            armRz = -0.2 + s(t * 6) * 0.25;
            twist = s(t * 6) * 0.3;
            lean = 0.2;
            break;
        case 'rest':
            // A long stretch now and then, otherwise slow breathing with drooping head
            if (s(t * 0.7) > 0.85) {
                armL = armR = -3.0;
                armLz = 0.3;
                armRz = -0.3;
                headX = -0.3;
            } else {
                headX = 0.2 + s(t * 1.2) * 0.05;
                armL = armR = 0.1;
            }
            bodyY += s(t * 1.2) * 0.02;
            break;
        case 'drink':
            armR = -1.8 + s(t * 2) * 0.25;
            armRz = -0.5;
            headX = -0.2;
            bodyY += breathe;
            break;
        case 'wave':
            if (ctx.seated) legL = legR = -1.45;
            armR = -2.8;
            armRz = -0.4 + s(t * 14) * 0.45;
            headZ = 0.12;
            break;
        case 'laugh':
            if (ctx.seated) legL = legR = -1.45;
            bodyY += Math.abs(s(t * 16)) * 0.03;
            headX = -0.25 + s(t * 16) * 0.08;
            armL = armR = -0.5;
            armLz = 0.3;
            armRz = -0.3;
            break;
        case 'cheer':
            armL = armR = -2.9;
            armLz = 0.35 + s(t * 10) * 0.1;
            armRz = -0.35 - s(t * 10) * 0.1;
            if (!ctx.seated) bodyY += Math.abs(s(t * 8)) * 0.18; // little jumps
            else legL = legR = -1.45;
            headX = -0.2;
            break;
        case 'angry':
            armL = armR = -0.3 + Math.abs(s(t * 18)) * 0.4;
            armLz = 0.5;
            armRz = -0.5;
            bodyY += Math.abs(s(t * 9)) * 0.03;
            headZ = s(t * 18) * 0.1;
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
    u.legL.position.y = u.legR.position.y = 0.5;
    u.legL.rotation.x = legL;
    u.legR.rotation.x = legR;
    u.armL.rotation.set(armL, 0, armLz);
    u.armR.rotation.set(armR, 0, armRz);
    p.scale.setScalar(scale);
}

// ---------------------------------------------------------------- dishes and crops

const DISH_LOOK = {
    bruschetta: ['plate', '#E6B26B', '#D7263D', '#5E9A3E'], fries: ['plate', '#F4C542', '#F4C542', '#D7263D'], tomato_soup: ['bowl', '#D7442B', '#F2E8CF'],
    lemonade: ['glass', '#F6E27A'], cappuccino: ['cup', '#C69C6D', '#FFFFFF'], omelette: ['plate', '#F6D55C', '#5E9A3E'], pasta_tomato: ['plate', '#F2D49B', '#D7263D', '#5E9A3E'],
    pizza: ['pizza', '#E8B26B', '#D7263D', '#FFF4D6'], carrot_cake: ['cake', '#D98A3D', '#FFF4E6'], burger: ['burger', '#C98A3E', '#6B3B22', '#5E9A3E'],
    risotto: ['plate', '#F1E3B5', '#8B6B4A'], strawberry_tart: ['cake', '#E8B26B', '#D7263D'], grilled_fish: ['plate', '#C9CED4', '#F4D35E'],
    stuffed_peppers: ['plate', '#D7442B', '#7A8B3A'], roast_chicken: ['plate', '#C9792F', '#F4C542'], chocolate_cake: ['cake', '#5A3218', '#3B1F0E'],
};

export function dishModel(dish) {
    const [type, a, b = a, c = b] = DISH_LOOK[dish] || ['plate', '#E0A526'];
    const g = new THREE.Group();
    const plate = () => g.add(cyl(0.17, 0.14, 0.03, '#FFFFFF', 0, 0, 0, 16, {flat: false}));
    if (type === 'glass') {
        g.add(cyl(0.06, 0.05, 0.18, '#FFFFFF', 0, 0, 0, 12, {opacity: 0.4}), cyl(0.055, 0.045, 0.14, a, 0, 0.01, 0, 12, {opacity: 0.85}), ball(0.03, '#F4D35E', 0.05, 0.17, 0));
    } else if (type === 'cup') {
        g.add(cyl(0.1, 0.08, 0.02, '#FFFFFF', 0, 0, 0, 14), cyl(0.065, 0.05, 0.1, '#FFFFFF', 0, 0.02, 0, 12), cyl(0.06, 0.06, 0.01, a, 0, 0.115, 0, 12), ball(0.025, b, 0, 0.125, 0));
    } else if (type === 'bowl') {
        g.add(mesh(new THREE.SphereGeometry(0.13, 14, 6, 0, Math.PI * 2, Math.PI / 2, Math.PI / 2), mat('#FFFFFF', {flat: false}), 0, 0.12, 0), cyl(0.12, 0.12, 0.01, a, 0, 0.1, 0, 14), ball(0.025, b, 0.03, 0.12, 0.02));
    } else if (type === 'pizza') {
        plate();
        g.add(cyl(0.16, 0.16, 0.02, a, 0, 0.03, 0, 18), cyl(0.14, 0.14, 0.01, b, 0, 0.05, 0, 18));
        for (let i = 0; i < 4; i++) g.add(cyl(0.025, 0.025, 0.01, c, Math.cos(i * 1.6) * 0.07, 0.055, Math.sin(i * 1.6) * 0.07, 8));
    } else if (type === 'cake') {
        plate();
        g.add(cyl(0.1, 0.1, 0.1, a, 0, 0.03, 0, 14, {flat: false}), cyl(0.1, 0.1, 0.015, b, 0, 0.13, 0, 14), ball(0.025, '#D7263D', 0, 0.16, 0));
    } else if (type === 'burger') {
        plate();
        g.add(mesh(new THREE.SphereGeometry(0.09, 12, 6, 0, Math.PI * 2, 0, Math.PI / 2), mat(a), 0, 0.12, 0), cyl(0.095, 0.095, 0.03, b, 0, 0.07, 0, 12), cyl(0.1, 0.1, 0.01, c, 0, 0.1, 0, 12), cyl(0.09, 0.09, 0.03, a, 0, 0.035, 0, 12));
    } else {
        plate();
        g.add(ball(0.09, a, 0, 0.05, 0, {}, 8), ball(0.04, b, 0.05, 0.1, 0.02, {}, 6), ball(0.03, c, -0.05, 0.09, -0.03, {}, 6));
        g.children[1].scale.y = 0.5;
    }
    return g;
}

const CROP_LOOK = {
    tomato: '#D7263D', basil: '#4C9A2A', potato: '#C9A66B', onion: '#C77DBB', carrot: '#F08A24', garlic: '#F3EEE2',
    lemon: '#F4D35E', strawberry: '#E63946', pepper: '#E2711D', mushroom: '#D9C3A5',
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

export function floorMaterial(key, size) {
    const t = canvasTexture(key + size, FLOOR_DRAW[key] || FLOOR_DRAW.floor_oak, size).clone();
    t.needsUpdate = true;
    t.repeat.set(size, size);
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
