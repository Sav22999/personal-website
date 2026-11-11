/*
 * Forkville — the 3D world: renderer, lights, the room (floor, walls with door and windows), the street,
 * the garden beds, and the camera.
 *
 * Camera: drag to orbit, right-drag / Shift-drag / two fingers to pan, wheel or pinch to zoom.
 * "Top view" glides to a near-orthographic overhead camera (a build view). The walls facing the camera
 * are lowered automatically so the room is always visible.
 */
import * as THREE from '../vendor/three.module.min.js';
import {furnitureModel, floorMaterial, wallMaterial, cropModel, mat, forkMesh, PALETTE, glowMat, setNightGlow} from './models.js';
import {buildNeighbourhood, Traffic} from './town.js';

const WALL_H = 2.4;
/** Height of one storey (walls + floor slab), and the z stride the simulation uses to keep floors apart (see sim.js). */
export const FH = 2.6;
export const FLOOR_STRIDE = 1000;
/** Is an object (and every parent) visible? Hidden floors must not be picked by taps. */
const shown = (o) => {
    for (let x = o; x; x = x.parent) if (!x.visible) return false;
    return true;
};
/** Depth of the outdoor area (terrace in front, yard at the side): the same as OUTDOOR in lib/catalog.php. */
export const OUT = 3;
const WALL_LOW = 0.14;
const FOV = 40;
const FOV_TOP = 14;


const smooth = (a, b, x) => {
    const t = Math.max(0, Math.min(1, (x - a) / (b - a)));
    return t * t * (3 - 2 * t);
};

/** Sky colours (top, horizon) through the day, blended between key hours. */
const SKY_KEYS = [
    [0, '#0B1530', '#1E2A4A'], [5, '#18244A', '#3A3F66'], [6, '#5A6FA8', '#F2A27A'], [7.5, '#8CC8EC', '#F4EBD3'],
    [12, '#6FB8EE', '#EAF2F4'], [17, '#7FB5DE', '#F6D8B0'], [19, '#4A5D9A', '#F08A5D'], [20.5, '#1A2550', '#3B3F6A'], [24, '#0B1530', '#1E2A4A'],
].map(([h, a, b]) => [h, new THREE.Color(a), new THREE.Color(b)]);

function skyAt(h) {
    for (let i = 0; i < SKY_KEYS.length - 1; i++) {
        const [h1, t1, b1] = SKY_KEYS[i], [h2, t2, b2] = SKY_KEYS[i + 1];
        if (h >= h1 && h <= h2) {
            const k = (h - h1) / (h2 - h1);
            return [t1.clone().lerp(t2, k), b1.clone().lerp(b2, k)];
        }
    }
    return [SKY_KEYS[0][1].clone(), SKY_KEYS[0][2].clone()];
}

const groundCache = {};

/** The ground for each season: fresh green with flowers, summer green, autumn gold with leaves, snow. */
function groundTexture(season) {
    if (groundCache[season]) return groundCache[season];
    const c = document.createElement('canvas');
    c.width = c.height = 128;
    const g = c.getContext('2d');
    const base = {spring: '#A4CF68', summer: '#9CBF6A', autumn: '#B8B062', winter: '#EEF2F5'}[season];
    g.fillStyle = base;
    g.fillRect(0, 0, 128, 128);
    for (let i = 0; i < 500; i++) {
        g.fillStyle = season === 'winter' ? (Math.random() > 0.5 ? 'rgba(170,190,210,.25)' : 'rgba(255,255,255,.6)')
            : Math.random() > 0.5 ? 'rgba(80,130,60,.25)' : 'rgba(200,220,140,.25)';
        g.fillRect(Math.random() * 128, Math.random() * 128, 2, 3);
    }
    const dots = {spring: ['#FFFFFF', '#F7B6CF', '#FFD23F'], autumn: ['#E07B39', '#C8452C', '#E9B44C']}[season];
    if (dots) for (let i = 0; i < 40; i++) {
        g.fillStyle = dots[i % dots.length];
        g.beginPath();
        g.arc(Math.random() * 128, Math.random() * 128, 1.6, 0, Math.PI * 2);
        g.fill();
    }
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    t.wrapS = t.wrapT = THREE.RepeatWrapping;
    t.repeat.set(105, 105);
    return (groundCache[season] = t);
}

/** The season of a real date (northern hemisphere). */
export function seasonOf(date = new Date()) {
    const m = date.getMonth();
    return m < 2 || m === 11 ? 'winter' : m < 5 ? 'spring' : m < 8 ? 'summer' : 'autumn';
}

/** The weather of a game day: random but the same for everyone that day, and fitting the season. */
/** Weather kinds. Rainy ones bring puddles in, snowy ones bring snow in (see sim.js), and both keep people indoors. */
export const RAINY = ['drizzle', 'rain', 'hail'];
export const SNOWY = ['light_snow', 'snow'];
export const isWet = (w) => RAINY.includes(w) || SNOWY.includes(w);
/** How much each kind of weather covers the sky (0 clear … 1 storm). */
const OVERCAST = {sunny: 0, clear: 0.08, cloudy: 0.45, drizzle: 0.55, rain: 0.85, hail: 0.75, light_snow: 0.5, snow: 0.8};

/**
 * The weather of one game day: it changes from day to day (a good hash of the day number, so consecutive days are
 * unrelated), with odds by season, and it may turn in the afternoon. The same day always has the same weather.
 */
export function weatherFor(season, day, hour = 12) {
    const rnd = (n) => {
        let h = (Math.imul(day + 1, 2654435761) ^ Math.imul(n + 7, 40503)) >>> 0;
        h = Math.imul(h ^ (h >>> 16), 2246822507) >>> 0;
        h = Math.imul(h ^ (h >>> 13), 3266489909) >>> 0;
        return ((h ^ (h >>> 16)) >>> 0) / 4294967296;
    };
    const table = {
        winter: [['snow', 0.12], ['light_snow', 0.18], ['hail', 0.04], ['rain', 0.08], ['drizzle', 0.1], ['cloudy', 0.2], ['clear', 0.16], ['sunny', 0.12]],
        spring: [['drizzle', 0.15], ['rain', 0.1], ['hail', 0.03], ['cloudy', 0.2], ['clear', 0.27], ['sunny', 0.25]],
        summer: [['sunny', 0.45], ['clear', 0.24], ['cloudy', 0.12], ['drizzle', 0.06], ['rain', 0.09], ['hail', 0.04]],
        autumn: [['drizzle', 0.2], ['rain', 0.16], ['hail', 0.03], ['cloudy', 0.25], ['clear', 0.2], ['sunny', 0.12], ['light_snow', 0.04]],
    }[season] || [['clear', 1]];
    const pick = (r) => {
        let acc = 0;
        for (const [w, p] of table) if (r < (acc += p)) return w;
        return 'clear';
    };
    // About one day in three the weather turns after lunch
    return hour >= 14 && rnd(2) < 0.35 ? pick(rnd(3)) : pick(rnd(1));
}

const paintings = [];

/** Four little paintings drawn on a canvas: hills at sunset, a still life, the sea, a fork. */
function paintingTexture(k) {
    if (paintings[k]) return paintings[k];
    const c = document.createElement('canvas');
    c.width = 160;
    c.height = 112;
    const g = c.getContext('2d');
    const sky = [['#FFB677', '#FFE3B3'], ['#3B2418', '#7B4E2C'], ['#8CC8EC', '#E3F4FB'], ['#FFF3E0', '#F6D9B8']][k];
    const grad = g.createLinearGradient(0, 0, 0, 112);
    grad.addColorStop(0, sky[0]);
    grad.addColorStop(1, sky[1]);
    g.fillStyle = grad;
    g.fillRect(0, 0, 160, 112);
    if (k === 0) {
        g.fillStyle = '#F25F5C';
        g.beginPath(); g.arc(110, 50, 18, 0, Math.PI * 2); g.fill();
        g.fillStyle = '#7A8B3A';
        g.beginPath(); g.ellipse(40, 120, 90, 50, 0, 0, Math.PI * 2); g.fill();
        g.fillStyle = '#5E9A3E';
        g.beginPath(); g.ellipse(140, 125, 80, 45, 0, 0, Math.PI * 2); g.fill();
    } else if (k === 1) {
        g.fillStyle = '#E8D3B0'; g.fillRect(0, 80, 160, 32);
        [['#D7263D', 55, 70, 16], ['#F4D35E', 85, 74, 13], ['#7A8B3A', 108, 72, 14]].forEach(([col, x, y, r]) => {
            g.fillStyle = col; g.beginPath(); g.arc(x, y, r, 0, Math.PI * 2); g.fill();
        });
    } else if (k === 2) {
        g.fillStyle = '#3E7CB4'; g.fillRect(0, 62, 160, 50);
        g.fillStyle = '#FFFFFF'; g.beginPath(); g.moveTo(70, 60); g.lineTo(90, 20); g.lineTo(92, 60); g.fill();
        g.fillStyle = '#7B4E2C'; g.fillRect(62, 60, 36, 6);
    } else {
        g.fillStyle = '#D9653B';
        g.fillRect(74, 50, 12, 50); g.fillRect(56, 44, 48, 10);
        for (let i = 0; i < 4; i++) { g.fillRect(58 + i * 12, 16, 6, 32); }
    }
    g.strokeStyle = 'rgba(0,0,0,.25)';
    g.lineWidth = 4;
    g.strokeRect(2, 2, 156, 108);
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    return (paintings[k] = t);
}

/** The front door, open towards the room, in one of the styles of DOORS (lib/catalog.php). Unit space: +z = street. */
export function doorModel(style, open = true) {
    const g = new THREE.Group();
    const m = (c, o) => mat(c, o);
    const b = (w, h, d, c, x, y, z, o, parent = g) => {
        const k = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), m(c, o));
        k.position.set(x, y + h / 2, z);
        k.castShadow = true;
        parent.add(k);
        return k;
    };
    const H = WALL_H - 0.38;
    const leaf = (w, hinge, amount, build) => { // a door leaf hinged at x = hinge, swung open into the room
        const p = new THREE.Group();
        p.position.set(hinge, 0, -0.03);
        const l = new THREE.Group();
        l.position.x = hinge < 0 ? w / 2 : -w / 2;
        build(l, w);
        p.add(l);
        p.rotation.y = (hinge < 0 ? 1 : -1) * (open ? amount : 0);
        g.add(p);
    };
    const glassO = {opacity: 0.4, roughness: 0.05};
    const knob = (l, w, c = PALETTE.gold) => {
        const k = new THREE.Mesh(new THREE.SphereGeometry(0.035, 8, 6), m(c, {metalness: 0.7, roughness: 0.3}));
        k.position.set(0, 1.0, 0);
        k.position.x = w / 2 - 0.1;
        l.add(k);
    };
    const panels = (l, w, c, frame) => {
        b(w, H, 0.05, c, 0, 0, 0, {}, l);
        for (const y of [0.25, 1.15]) b(w * 0.7, 0.7, 0.02, frame, 0, y, 0.03, {}, l);
    };
    switch (style) {
        case 'rustic':
            leaf(0.9, -0.45, 1.6, (l, w) => {
                for (let i = 0; i < 5; i++) b(w / 5 - 0.01, H, 0.06, ['#5A3A22', '#6B4426'][i % 2], -w / 2 + (i + 0.5) * w / 5, 0, 0, {}, l);
                for (const y of [0.35, 1.5]) b(w, 0.07, 0.07, '#2C2C30', 0, y, 0.02, {metalness: 0.5}, l);
                knob(l, w, '#2C2C30');
            });
            break;
        case 'glass':
            leaf(0.9, -0.45, 1.5, (l, w) => {
                b(w, H, 0.03, '#BFE3F0', 0, 0, 0, glassO, l);
                for (const [x, ww] of [[-w / 2 + 0.03, 0.06], [w / 2 - 0.03, 0.06]]) b(ww, H, 0.05, '#9AA3AD', x, 0, 0, {metalness: 0.6}, l);
                b(w, 0.06, 0.05, '#9AA3AD', 0, 0, 0, {metalness: 0.6}, l);
                b(w, 0.06, 0.05, '#9AA3AD', 0, H - 0.06, 0, {metalness: 0.6}, l);
                b(0.03, 0.6, 0.05, '#C9CED4', w / 2 - 0.12, 0.7, 0.04, {metalness: 0.7}, l);
            });
            break;
        case 'red':
            leaf(0.9, -0.45, 1.6, (l, w) => {
                b(w, H, 0.05, '#C0392B', 0, 0, 0, {flat: false, roughness: 0.4}, l);
                for (let i = 0; i < 4; i++) b(0.2, 0.2, 0.02, '#BFE3F0', -0.12 + (i % 2) * 0.24, 1.35 + Math.floor(i / 2) * 0.25, 0.03, glassO, l);
                b(w * 0.7, 0.6, 0.02, '#A93226', 0, 0.3, 0.03, {}, l);
                knob(l, w);
            });
            break;
        case 'double':
            for (const hinge of [-0.45, 0.45]) leaf(0.45, hinge, 1.7, (l, w) => {
                b(w, H, 0.05, '#7B4E2C', 0, 0, 0, {}, l);
                b(w * 0.7, 1.0, 0.02, '#BFE3F0', 0, 0.9, 0.03, glassO, l);
                b(0.03, 0.3, 0.04, PALETTE.gold, hinge < 0 ? w / 2 - 0.06 : -w / 2 + 0.06, 0.9, 0.04, {metalness: 0.7}, l);
            });
            break;
        case 'arched': {
            leaf(0.9, -0.45, 1.6, (l, w) => panels(l, w, '#6B4426', '#5A3218'));
            const arch = new THREE.Mesh(new THREE.CircleGeometry(0.42, 18, 0, Math.PI), m('#F6D58E', {emissive: '#F6C35E', glow: 0.4}));
            arch.position.set(0, H + 0.01, 0.06);
            g.add(arch);
            break;
        }
        case 'french':
            leaf(0.9, -0.45, 1.5, (l, w) => {
                b(w, H, 0.04, '#FFFFFF', 0, 0, 0, {flat: false}, l);
                for (let r = 0; r < 4; r++) for (let c = 0; c < 2; c++) b(w / 2 - 0.08, H / 4 - 0.08, 0.02, '#BFE3F0', -w / 4 + c * w / 2, 0.04 + r * H / 4, 0.025, glassO, l);
                knob(l, w);
            });
            break;
        case 'gold':
            for (const hinge of [-0.45, 0.45]) leaf(0.45, hinge, 1.6, (l, w) => {
                b(w, H, 0.05, '#1E1E24', 0, 0, 0, {roughness: 0.2}, l);
                for (const y of [0.15, 1.05]) {
                    b(w * 0.75, 0.025, 0.02, PALETTE.gold, 0, y, 0.03, {metalness: 0.8, roughness: 0.25}, l);
                    b(w * 0.75, 0.025, 0.02, PALETTE.gold, 0, y + 0.7, 0.03, {metalness: 0.8, roughness: 0.25}, l);
                }
                b(0.03, 0.4, 0.04, PALETTE.gold, hinge < 0 ? w / 2 - 0.06 : -w / 2 + 0.06, 0.85, 0.04, {metalness: 0.8}, l);
            });
            b(1.0, 0.08, 0.2, PALETTE.gold, 0, H, 0.02, {metalness: 0.7});
            break;
        default: // wood
            leaf(0.9, -0.45, 1.6, (l, w) => {
                panels(l, w, '#9C6B3F', '#8A5A35');
                knob(l, w);
            });
    }
    return g;
}

export class World {
    constructor(canvas, overlay) {
        this.canvas = canvas;
        this.overlay = overlay;
        this.listeners = {};
        try {
            this.wallMode = localStorage.getItem('forkville-walls') || 'adaptive';
        } catch (e) {
            this.wallMode = 'adaptive';
        }
        this.items = new Map();
        this.clock = new THREE.Clock();
        this.time = 0;

        const r = this.renderer = new THREE.WebGLRenderer({canvas, antialias: true, powerPreference: 'high-performance'});
        r.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
        r.shadowMap.enabled = true;
        r.shadowMap.type = THREE.PCFSoftShadowMap;
        r.outputColorSpace = THREE.SRGBColorSpace;
        r.toneMapping = THREE.ACESFilmicToneMapping;
        r.toneMappingExposure = 1.05;

        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color('#CFE7F2');
        this.scene.fog = new THREE.Fog('#F4EBD3', 38, 80);
        this.camera = new THREE.PerspectiveCamera(FOV, 1, 0.1, 200);

        this.hemi = new THREE.HemisphereLight('#EAF4FF', '#8A9A62', 1.1);
        this.scene.add(this.hemi);
        const sun = this.sun = new THREE.DirectionalLight('#FFE2B8', 2.1);
        sun.castShadow = true;
        sun.shadow.mapSize.set(2048, 2048);
        sun.shadow.bias = -0.0006;
        sun.shadow.normalBias = 0.02;
        this.scene.add(sun, sun.target);

        this.root = new THREE.Group();
        this.scene.add(this.root);
        this.buildOutdoors();

        // Camera state (yaw around the target, pitch above the horizon, distance)
        this.cam = {target: new THREE.Vector3(5, 0, 5), yaw: Math.PI / 4, pitch: 0.82, dist: 17, fov: FOV};
        this.goal = {...this.cam, target: this.cam.target.clone()};
        this.top = false;
        this.raycaster = new THREE.Raycaster();
        this.ground = new THREE.Plane(new THREE.Vector3(0, 1, 0), 0);

        this.bindInput();
        this.resize();
        window.addEventListener('resize', () => this.resize());
        this.renderer.setAnimationLoop(() => this.frame());
    }

    /** Walls: 'adaptive' (low on the camera side), 'low' (always), 'high' (always). Remembered on this device. */
    setWallMode(mode) {
        this.wallMode = mode;
        try {
            localStorage.setItem('forkville-walls', mode);
        } catch (e) { /* storage blocked */ }
    }

    on(name, fn) {
        (this.listeners[name] ||= []).push(fn);
    }

    emit(name, ...args) {
        (this.listeners[name] || []).forEach(fn => fn(...args));
    }

    resize() {
        const w = this.canvas.clientWidth || window.innerWidth, h = this.canvas.clientHeight || window.innerHeight;
        this.renderer.setSize(w, h, false);
        this.camera.aspect = w / h;
        this.applyInsets();
    }

    /** Panels covering the canvas (px): the view shifts so the room stays in the visible part. */
    setInsets(right = 0, bottom = 0) {
        this.insets = {right, bottom};
        this.applyInsets();
    }

    applyInsets() {
        const w = this.canvas.clientWidth || window.innerWidth, h = this.canvas.clientHeight || window.innerHeight;
        const {right = 0, bottom = 0} = this.insets || {};
        if (right || bottom) this.camera.setViewOffset(w, h, right / 2, bottom / 2, w, h);
        else this.camera.clearViewOffset();
        this.camera.updateProjectionMatrix();
    }

    // ------------------------------------------------------------ outdoors (built once)

    buildOutdoors() {
        const grass = new THREE.Mesh(new THREE.PlaneGeometry(420, 420), new THREE.MeshStandardMaterial({map: groundTexture('summer'), roughness: 1}));
        this.grass = grass;
        grass.rotation.x = -Math.PI / 2;
        grass.position.set(8, -0.06, 8);
        grass.receiveShadow = true;
        this.scene.add(grass);
        this.outdoor = new THREE.Group();
        this.scene.add(this.outdoor);
        this.buildSky();
        this.buildCelestial();
        this.env = {hour: 12, weather: 'clear', season: 'summer'};
    }

    /** A gradient sky dome (blue overhead, warm at the horizon) and a few slow low-poly clouds. */
    buildSky() {
        const sky = new THREE.Mesh(new THREE.SphereGeometry(120, 24, 12), new THREE.ShaderMaterial({
            side: THREE.BackSide, depthWrite: false, fog: false,
            uniforms: {top: {value: new THREE.Color('#8CC8EC')}, horizon: {value: new THREE.Color('#F4EBD3')}, ground: {value: new THREE.Color('#F4EBD3')}},
            vertexShader: 'varying vec3 vP; void main(){ vP = normalize(position); gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }',
            fragmentShader: 'uniform vec3 top; uniform vec3 horizon; uniform vec3 ground; varying vec3 vP; void main(){ float h = vP.y; vec3 c = h > 0.0 ? mix(horizon, top, pow(min(1.0, h * 1.6), 0.7)) : mix(horizon, ground, min(1.0, -h * 4.0)); gl_FragColor = vec4(c, 1.0); }',
        }));
        sky.renderOrder = -1;
        this.sky = sky;
        this.scene.add(sky);
        this.clouds = new THREE.Group();
        const white = new THREE.MeshStandardMaterial({color: '#FFFFFF', roughness: 1, flatShading: true, fog: false, emissive: '#FFF4E6', emissiveIntensity: 0.25});
        for (let i = 0; i < 9; i++) {
            const c = new THREE.Group();
            const n = 3 + (i % 3);
            for (let j = 0; j < n; j++) {
                const puff = new THREE.Mesh(new THREE.IcosahedronGeometry(1.2 + ((i + j) % 3) * 0.5, 0), white);
                puff.position.set(j * 1.5 - n * 0.7, ((j * 7) % 3) * 0.35, ((j * 5) % 3) * 0.5);
                c.add(puff);
            }
            // A wide ring far from the restaurant: clouds frame the horizon, never the view of the room
            const a = i * (Math.PI * 2 / 9) + (i % 2) * 0.3, r = 72 + (i % 3) * 12;
            c.position.set(Math.cos(a) * r, 20 + (i % 4) * 3, Math.sin(a) * r);
            c.scale.setScalar(2.2);
            this.clouds.add(c);
        }
        this.clouds.position.set(8, 0, 8);
        this.scene.add(this.clouds);
    }

    /** Street, sidewalks, the neighbourhood (grows with the level), the car park, trees and street lamps. */
    buildStreet(r) {
        this.outdoor.clear();
        this.trees = [];
        this.bushes = [];
        this.snowCaps = [];
        this.flowers = [];
        buildNeighbourhood(this, r);
        if (this.env) this.applySeason(this.env.season, true);
        if (!this.traffic) this.traffic = new Traffic(this);
        this.traffic.reset();
    }

    // ------------------------------------------------------------ the restaurant

    /** Builds (or rebuilds) the whole restaurant from the state sent by the server. */
    setRestaurant(r, opts = {}) {
        this.root.clear();
        this.items.clear();
        this.walls = [];
        this.W = r.size; // width (x)
        this.D = r.depth || r.size; // depth (z): the front wall with the door is at z = D
        this.size = Math.max(this.W, this.D);
        this.door = r.door ?? Math.floor(this.W / 2);
        this.doorStyle = r.doorStyle || 'wood';
        this.floors = r.floors || [];
        this.layout = r.layout;
        this.viewFloor = Math.min(this.viewFloor || 0, this.floors.length);
        this.buildStreet(r);
        this.buildFloor(r.floor);
        this.buildWalls(r.wall, r.name);
        this.gardenGroup = new THREE.Group();
        this.root.add(this.gardenGroup);
        this.itemGroup = new THREE.Group();
        this.root.add(this.itemGroup);
        this.agentGroup = new THREE.Group();
        this.root.add(this.agentGroup);
        this.fxGroup = new THREE.Group();
        this.root.add(this.fxGroup);
        this.layers = {agent: [this.agentGroup], fx: [this.fxGroup]};
        // Warm room lights over the dining room: they come on after sunset (see setEnvironment)
        this.roomLights = [];
        const n = this.W <= 10 ? 2 : 3, m = this.D <= 12 ? 2 : 3;
        for (let i = 0; i < n; i++) for (let j = 0; j < m; j++) {
            const l = new THREE.PointLight('#FFD9A0', 0, this.size * 0.9, 1.2);
            l.position.set(this.W * (i + 0.5) / n, 2.5, this.D * (j + 0.5) / m);
            this.root.add(l);
            this.roomLights.push(l);
        }
        this.upper = [];
        for (let f = 1; f <= this.floors.length; f++) this.buildUpperFloor(f, r.floor, r.wall);
        for (const it of r.layout) this.addItem(it);
        this.applyViewFloor();
        if (opts.plots != null) this.setGarden(opts.plots, opts.garden || [], opts.now || Date.now() / 1000);
        this.fitSun();
        if (!opts.keepCamera) this.recenter(true);
    }

    fitSun() {
        const s = this.size + (this.parkingW || 0) * 0.5;
        const c = new THREE.Vector3(this.W / 2 - 1.5 - (this.parkingW || 0) * 0.4, 0, this.D / 2 + 1);
        this.sun.position.set(c.x + 9, 16, c.z - 6);
        this.sun.target.position.copy(c);
        const cam = this.sun.shadow.camera;
        const e = s * 0.95 + 6;
        cam.left = -e;
        cam.right = e;
        cam.top = e;
        cam.bottom = -e;
        cam.near = 1;
        cam.far = 60;
        cam.updateProjectionMatrix();
    }

    buildFloor(key) {
        const W = this.W, D = this.D;
        const floor = new THREE.Mesh(new THREE.PlaneGeometry(W, D), floorMaterial(key, W, D));
        floor.rotation.x = -Math.PI / 2;
        floor.position.set(W / 2, 0.01, D / 2);
        floor.receiveShadow = true;
        this.floor = floor;
        this.root.add(floor);
        const mat_ = new THREE.Mesh(new THREE.BoxGeometry(0.9, 0.02, 0.7), mat(PALETTE.terracotta));
        mat_.position.set(this.door + 0.5, 0.03, D - 0.45);
        mat_.receiveShadow = true;
        const matIn = new THREE.Mesh(new THREE.BoxGeometry(0.7, 0.025, 0.5), mat('#A8472A'));
        matIn.position.copy(mat_.position);
        this.root.add(mat_, matIn);
        const base = new THREE.Mesh(new THREE.BoxGeometry(W + 0.6, 0.12, D + 0.6), mat('#CDBBA0'));
        base.position.set(W / 2, -0.085, D / 2); // top 2.5 cm under the floor: no flicker from far away
        base.receiveShadow = true;
        this.root.add(base);
    }

    setFloor(key) {
        this.floor.material = floorMaterial(key, this.W, this.D);
        for (const u of this.upper || []) if (u) u.group.children[0].material = floorMaterial(key, 1, 1);
    }

    setWall(key) {
        const m = wallMaterial(key);
        this.walls.forEach(w => w.segments.forEach(seg => seg.userData.paint && (seg.material = m)));
    }

    /** The rectangle of a floor: {x0, w, d} (0 = ground floor). */
    floorRect(f) {
        if (!f) return {x0: 0, w: this.W, d: this.D};
        const fl = this.floors[f - 1];
        return fl ? {x0: fl[2], w: fl[0], d: fl[1]} : null;
    }

    /** Tiles of floor f kept free for the way up (above stairs on the floor below, above a lift). "x,z" set. */
    shaftTiles(f, layout = this.layout, items = this.catalogItems) {
        const out = new Set();
        if (!f || !items) return out;
        for (const it of layout || []) {
            const def = items[it.k];
            if (!def || def.cat !== 'stairs') continue;
            const from = it.f || 0, lift = it.k.startsWith('lift');
            if (lift ? f <= from : f !== from + 1) continue;
            const rect = this.floorRect(f), tiles = this.footprint(it, items).tiles;
            if (!rect || !tiles.every(([x, z]) => x >= rect.x0 && x < rect.x0 + rect.w && z >= 0 && z < rect.d)) continue;
            if (lift) { // a lift only reaches floors it fits on all the way up
                let ok = true;
                for (let g = from + 1; g < f; g++) {
                    const rr = this.floorRect(g);
                    if (!rr || !tiles.every(([x, z]) => x >= rr.x0 && x < rr.x0 + rr.w && z >= 0 && z < rr.d)) ok = false;
                }
                if (!ok) continue;
            }
            tiles.forEach(([x, z]) => out.add(x + ',' + z));
        }
        return out;
    }

    /** An upper floor: its slab (with the stairwell left open), a ceiling for the room below, its walls and a light. */
    buildUpperFloor(f, floorKey, wallKey) {
        const rect = this.floorRect(f), y = f * FH;
        const g = new THREE.Group();
        g.userData.floor = f;
        const holes = this.shaftTiles(f);
        const pos = [], uv = [], under = [];
        for (let x = rect.x0; x < rect.x0 + rect.w; x++) for (let z = 0; z < rect.d; z++) {
            if (holes.has(x + ',' + z)) continue;
            pos.push(x, 0, z, x, 0, z + 1, x + 1, 0, z, x + 1, 0, z, x, 0, z + 1, x + 1, 0, z + 1);
            uv.push(0, 1, 0, 0, 1, 1, 1, 1, 0, 0, 1, 0);
            under.push(x, 0, z, x + 1, 0, z, x, 0, z + 1, x + 1, 0, z, x + 1, 0, z + 1, x, 0, z + 1);
        }
        const geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
        geo.setAttribute('uv', new THREE.Float32BufferAttribute(uv, 2));
        geo.computeVertexNormals();
        const slab = new THREE.Mesh(geo, floorMaterial(floorKey, 1, 1));
        slab.position.y = y + 0.01;
        slab.receiveShadow = true;
        const ug = new THREE.BufferGeometry();
        ug.setAttribute('position', new THREE.Float32BufferAttribute(under, 3));
        ug.computeVertexNormals();
        const ceiling = new THREE.Mesh(ug, mat('#F4EEE4'));
        ceiling.position.y = y - 0.18;
        g.add(slab, ceiling);
        // The edge of the slab, between the walls below and the walls above
        const edge = mat('#E2D3BC');
        for (const [w, d, x, z] of [[rect.w + 0.32, 0.16, rect.x0 + rect.w / 2, -0.08], [rect.w + 0.32, 0.16, rect.x0 + rect.w / 2, rect.d + 0.08], [0.16, rect.d, rect.x0 - 0.08, rect.d / 2], [0.16, rect.d, rect.x0 + rect.w + 0.08, rect.d / 2]]) {
            const m = new THREE.Mesh(new THREE.BoxGeometry(w, 0.2, d), edge);
            m.position.set(x, y - 0.09, z);
            m.castShadow = m.receiveShadow = true;
            g.add(m);
        }
        const light = new THREE.PointLight('#FFD9A0', 0, Math.max(rect.w, rect.d) * 1.2, 1.2);
        light.position.set(rect.x0 + rect.w / 2, y + 2.3, rect.d / 2);
        g.add(light);
        this.roomLights.push(light);
        this.root.add(g);
        const walls = this.buildWalls(wallKey, null, f);
        this.upper[f] = {group: g, walls};
    }

    buildWalls(key, name, f = 0) {
        const rect = this.floorRect(f), x0 = rect.x0, W = rect.w, D = rect.d, T = 0.16;
        const paint = wallMaterial(key);
        const outside = mat('#F3E6D2');
        const trim = mat(PALETTE.woodDark);
        const glass = mat('#BFE3F0', {opacity: 0.45, roughness: 0.05});
        // Each side: where it is, which way it faces (outward normal), and which units are door or windows
        const sides = [
            {n: [0, -1], len: W, at: (i) => [x0 + i + 0.5, -T / 2], rotY: 0, windows: [2, W - 3]},
            {n: [0, 1], len: W, at: (i) => [x0 + i + 0.5, D + T / 2], rotY: 0, door: f ? undefined : this.door - x0, windows: f ? [1, Math.floor(W / 2), W - 2] : [1, W - 2]},
            {n: [-1, 0], len: D, at: (i) => [x0 - T / 2, i + 0.5], rotY: Math.PI / 2, windows: [2, Math.floor(D / 2), D - 3]},
            {n: [1, 0], len: D, at: (i) => [x0 + W + T / 2, i + 0.5], rotY: Math.PI / 2, windows: [3, D - 4]},
        ];
        const built = [];
        for (const side of sides) {
            const g = new THREE.Group();
            const segments = [];
            const decor = [];
            for (let i = 0; i < side.len; i++) {
                const [x, z] = side.at(i);
                const isDoor = side.door != null && side.door === i;
                const isWin = (side.windows || []).includes(i);
                const unit = new THREE.Group();
                unit.position.set(x, 0, z);
                unit.rotation.y = side.rotY;
                // Two thin slabs: the painted one on the room side, a plain one on the street side
                const parts = isDoor ? [[WALL_H - 0.35, 0.35]] : isWin ? [[0, 0.9], [1.9, WALL_H - 1.9]] : [[0, WALL_H]];
                const inward = side.rotY ? -side.n[0] : -side.n[1]; // the room side, in the unit's own z
                for (const [y0, h] of parts) {
                    const inner = new THREE.Mesh(new THREE.BoxGeometry(1, h, T / 2), paint);
                    inner.position.set(0, y0 + h / 2, inward * T / 4);
                    inner.userData.paint = true;
                    const outer = new THREE.Mesh(new THREE.BoxGeometry(1, h, T / 2), outside);
                    outer.position.set(0, y0 + h / 2, -inward * T / 4);
                    for (const m of [inner, outer]) {
                        m.castShadow = true;
                        m.receiveShadow = true;
                        unit.add(m);
                    }
                    segments.push(inner, outer);
                }
                if (isWin) {
                    const pane = new THREE.Mesh(new THREE.BoxGeometry(0.96, 1.0, 0.03), glass);
                    pane.position.y = 1.4;
                    const sill = new THREE.Mesh(new THREE.BoxGeometry(1, 0.06, T + 0.08), trim);
                    sill.position.y = 0.9;
                    const bar = new THREE.Mesh(new THREE.BoxGeometry(0.04, 1.0, T), trim);
                    bar.position.y = 1.4;
                    unit.add(pane, sill, bar);
                    segments.push(pane, sill, bar);
                }
                if (!isDoor && !isWin && side.door == null && i % 3 === 1 && i > 0 && i < side.len - 1) {
                    // A framed painting on the room side (hidden when the wall is lowered)
                    const art = new THREE.Group();
                    const frame = new THREE.Mesh(new THREE.BoxGeometry(0.7, 0.52, 0.04), mat(PALETTE.woodDark));
                    const pic = new THREE.Mesh(new THREE.PlaneGeometry(0.6, 0.42), new THREE.MeshStandardMaterial({map: paintingTexture((i + side.n[0] * 3 + side.n[1] * 5 + 9) % 4), roughness: 0.7}));
                    pic.position.z = 0.025;
                    art.add(frame, pic);
                    art.position.set(0, 1.55, inward * (T / 2 + 0.03));
                    if (inward < 0) art.rotation.y = Math.PI;
                    unit.add(art);
                    decor.push(art);
                }
                if (isDoor) {
                    const leaf = doorModel(this.doorStyle || 'wood');
                    leaf.position.z = inward * 0.02;
                    if (inward > 0) leaf.rotation.y = Math.PI;
                    unit.add(leaf);
                    segments.push(...leaf.children.filter(c => c.isMesh));
                    for (const dx of [-0.48, 0.48]) {
                        const post = new THREE.Mesh(new THREE.BoxGeometry(0.06, WALL_H - 0.35, T + 0.04), trim);
                        post.position.set(dx, (WALL_H - 0.35) / 2, 0);
                        post.castShadow = true;
                        unit.add(post);
                        segments.push(post);
                    }
                }
                g.add(unit);
            }
            // The cap on top of the wall
            const cap = new THREE.Mesh(new THREE.BoxGeometry(side.rotY ? T + 0.04 : W + T * 2, 0.06, side.rotY ? D + T * 2 : T + 0.04), trim);
            cap.position.set(side.rotY ? side.at(0)[0] : x0 + W / 2, WALL_H, side.rotY ? D / 2 : side.at(0)[1]);
            g.add(cap);
            g.position.y = f * FH;
            this.root.add(g);
            const wall = {group: g, n: side.n, segments, cap, decor, h: null, isFront: side.door != null, floor: f}; // h: set at once on the first frame
            this.walls.push(wall);
            built.push(wall);
        }
        if (f) return built;
        // Awning and sign over the door, on the street side
        const frontWall = this.walls.find(w => w.isFront);
        const awning = new THREE.Group();
        for (let i = 0; i < 6; i++) {
            const stripe = new THREE.Mesh(new THREE.BoxGeometry(0.28, 0.05, 0.9), mat(i % 2 ? PALETTE.cream : PALETTE.terracotta));
            stripe.position.set(this.door + 0.5 - 0.7 + i * 0.28, WALL_H - 0.1, D + 0.55);
            stripe.rotation.x = 0.35;
            stripe.castShadow = true;
            awning.add(stripe);
        }
        this.sign = this.makeSign(name);
        this.sign.position.set(this.door + 0.5, WALL_H + 0.45, D + 0.2);
        this.root.add(awning, this.sign);
        frontWall.extras = [awning, this.sign]; // hidden (not squashed) while the front wall is lowered
    }

    makeSign(name) {
        const c = document.createElement('canvas');
        c.width = 512;
        c.height = 128;
        const g = c.getContext('2d');
        g.fillStyle = '#3B2418';
        g.beginPath();
        g.roundRect(4, 4, 504, 120, 26);
        g.fill();
        g.strokeStyle = '#E2B23A';
        g.lineWidth = 6;
        g.stroke();
        g.fillStyle = '#FFF3E0';
        g.font = '700 54px ui-rounded, "SF Pro Rounded", system-ui, sans-serif';
        g.textAlign = 'center';
        g.textBaseline = 'middle';
        let txt = name || 'Forkville';
        while (g.measureText(txt).width > 460 && txt.length > 3) txt = txt.slice(0, -2) + '…';
        g.fillText(txt, 256, 68);
        const t = new THREE.CanvasTexture(c);
        t.colorSpace = THREE.SRGBColorSpace;
        const m = new THREE.Mesh(new THREE.BoxGeometry(2.4, 0.6, 0.06), [mat('#3B2418'), mat('#3B2418'), mat('#3B2418'), mat('#3B2418'), new THREE.MeshStandardMaterial({map: t, roughness: 0.6}), mat('#3B2418')]);
        m.castShadow = true;
        return m;
    }

    setSign(name) {
        const parent = this.sign.parent;
        const pos = this.sign.position.clone();
        parent.remove(this.sign);
        this.sign = this.makeSign(name);
        this.sign.position.copy(pos);
        parent.add(this.sign);
        const w = this.walls.find(x => x.isFront);
        w.extras[1] = this.sign;
    }

    /** Tiles covered by an item (width and depth swap on quarter turns). */
    footprint(it, items) {
        const def = items[it.k];
        const [w, d] = it.r % 2 ? [def.d, def.w] : [def.w, def.d];
        const out = [];
        for (let dx = 0; dx < w; dx++) for (let dz = 0; dz < d; dz++) out.push([it.x + dx, it.z + dz]);
        return {tiles: out, w, d};
    }

    placeObject(obj, it, items) {
        const {w, d} = this.footprint(it, items);
        obj.position.set(it.x + w / 2, (it.f || 0) * FH, it.z + d / 2);
        obj.rotation.y = it.r * Math.PI / 2;
    }

    addItem(it) {
        const obj = furnitureModel(it.k);
        obj.userData.itemId = it.id;
        obj.userData.floor = it.f || 0;
        obj.traverse(o => (o.userData.itemId = it.id));
        this.placeObject(obj, it, this.catalogItems);
        // A lift shows its doors on every floor it reaches
        if (it.k.startsWith('lift')) for (let f = (it.f || 0) + 1; f <= this.floors.length; f++) {
            if (!this.shaftTiles(f).has(it.x + ',' + it.z)) break;
            const stop = furnitureModel(it.k);
            stop.position.y = (f - (it.f || 0)) * FH;
            stop.userData.floor = f;
            stop.traverse(o => (o.userData.itemId = it.id));
            obj.add(stop);
        }
        this.itemGroup.add(obj);
        this.items.set(it.id, obj);
        obj.visible = (it.f || 0) <= (this.viewFloor || 0);
        return obj;
    }

    /** Which floor the player looks at: the floors above it are hidden; building happens on it. */
    setViewFloor(f) {
        this.viewFloor = Math.max(0, Math.min(f, this.floors?.length || 0));
        this.applyViewFloor();
        this.emit('floor', this.viewFloor);
    }

    applyViewFloor() {
        const k = this.viewFloor || 0;
        this.ground.constant = -k * FH;
        for (let f = 1; f < (this.upper || []).length; f++) {
            const u = this.upper[f];
            if (!u) continue;
            u.group.visible = f <= k;
            u.walls.forEach(w => (w.group.visible = f <= k));
        }
        // One floor at a time (Sims style): what stands on other floors is hidden, the garden outside stays
        for (const obj of this.items.values()) {
            const f = obj.userData.floor || 0, outdoor = this.catalogItems?.[obj.userData.key]?.cat === 'outdoor';
            obj.visible = outdoor || f === k || (k > 0 && obj.children.some(c => c.userData.floor === k));
            obj.children.forEach(c => {
                if (c.userData.floor != null && c.userData.itemId != null && c.userData.floor > f) c.visible = c.userData.floor === k;
                else if (c.userData.floor == null && f !== k && k > 0 && !outdoor) c.visible = false; // a lift seen from above: only its stop on this floor
                else if (c.userData.floor == null) c.visible = true;
            });
        }
        for (const kind of ['agent', 'fx']) (this.layers?.[kind] || []).forEach((l, f) => l && (l.visible = f === k));
        // The floors below are shaded, so the one being looked at stands out
        if (this.shades) this.root.remove(this.shades);
        this.shades = new THREE.Group();
        const shade = new THREE.MeshBasicMaterial({color: '#1E1410', transparent: true, opacity: 0.45, depthWrite: false});
        for (let f = 0; f < k; f++) {
            const rc = this.floorRect(f);
            const m = new THREE.Mesh(new THREE.PlaneGeometry(rc.w, rc.d), shade);
            m.rotation.x = -Math.PI / 2;
            m.position.set(rc.x0 + rc.w / 2, f * FH + 0.06, rc.d / 2);
            this.shades.add(m);
        }
        this.root.add(this.shades);
        // The ground floor's sign and awning stay; the camera rises to the floor
        this.goal.target.y = k * FH;
    }

    /** A group for people (kind 'agent') or things on the floor (kind 'fx') of floor f: the simulation's floors are kept
     *  FLOOR_STRIDE apart along z, the group puts them back on top of each other. */
    layer(kind, f) {
        const list = this.layers[kind];
        if (!list[f]) {
            const g = new THREE.Group();
            g.position.set(0, f * FH, -f * FLOOR_STRIDE);
            g.visible = f === (this.viewFloor || 0);
            this.root.add(g);
            list[f] = g;
        }
        return list[f];
    }

    /** Rebuilds the models of some items in place (e.g. menu boards after a language change). */
    refreshItems(filter) {
        for (const [id, obj] of [...this.items]) {
            if (!filter(obj.userData.key)) continue;
            const it = {id, k: obj.userData.key, x: 0, z: 0, r: 0};
            const fresh = furnitureModel(it.k);
            fresh.userData.itemId = id;
            fresh.traverse(o => (o.userData.itemId = id));
            fresh.position.copy(obj.position);
            fresh.rotation.copy(obj.rotation);
            this.itemGroup.remove(obj);
            this.itemGroup.add(fresh);
            this.items.set(id, fresh);
        }
    }

    removeItem(id) {
        const o = this.items.get(id);
        if (o) this.itemGroup.remove(o);
        this.items.delete(id);
    }

    // ------------------------------------------------------------ garden beds (west of the restaurant)

    plotPosition(i) {
        const col = i % 2, row = Math.floor(i / 2);
        return new THREE.Vector3(-3.7 + col * 1.5, 0, 0.9 + row * 1.5);
    }

    setGarden(plots, garden, now) {
        this.gardenGroup.clear();
        this.plotMeshes = [];
        const byPlot = new Map(garden.map(g => [g.plot, g]));
        const fence = mat(PALETTE.wood);
        for (let i = 0; i < plots; i++) {
            const p = this.plotPosition(i);
            const bed = new THREE.Group();
            bed.position.copy(p);
            const soil = new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.18, 1.2), mat('#6B4A33'));
            soil.position.y = 0.09;
            soil.receiveShadow = true;
            bed.add(soil);
            for (const [w, d, x, z] of [[1.3, 0.08, 0, 0.62], [1.3, 0.08, 0, -0.62], [0.08, 1.3, 0.62, 0], [0.08, 1.3, -0.62, 0]]) {
                const b = new THREE.Mesh(new THREE.BoxGeometry(w, 0.24, d), fence);
                b.position.set(x, 0.12, z);
                b.castShadow = true;
                bed.add(b);
            }
            const g = byPlot.get(i);
            if (g) {
                const stage = now >= g.readyAt ? 2 : (now >= g.readyAt - (g.secs || 300) / 2 ? 1 : 0);
                for (const [x, z] of [[-0.3, -0.3], [0.3, -0.3], [-0.3, 0.3], [0.3, 0.3]]) {
                    const c = cropModel(g.crop, stage);
                    c.position.set(x, 0.18, z);
                    c.traverse(o => o.isMesh && (o.castShadow = true));
                    bed.add(c);
                }
                if (stage === 2) bed.userData.ripe = true;
            }
            bed.traverse(o => (o.userData.plot = i));
            this.gardenGroup.add(bed);
            this.plotMeshes.push(bed);
        }
        // A little sign with a fork, at the head of the garden
        const post = forkMesh(PALETTE.gold, 0.9);
        post.position.set(-3, 0, -0.4);
        this.gardenGroup.add(post);
    }


    // ------------------------------------------------------------ time of day, weather, seasons

    /** Sun, moon and stars live on the sky dome (they follow the camera, like the sky). */
    buildCelestial() {
        const g = new THREE.Group();
        const sunMat = new THREE.MeshBasicMaterial({color: '#FFE9A8', fog: false});
        this.sunDisc = new THREE.Mesh(new THREE.SphereGeometry(5, 20, 12), sunMat);
        const halo = new THREE.Mesh(new THREE.SphereGeometry(8, 20, 12), new THREE.MeshBasicMaterial({color: '#FFD27A', transparent: true, opacity: 0.25, fog: false, depthWrite: false}));
        this.sunDisc.add(halo);
        this.moonDisc = new THREE.Mesh(new THREE.SphereGeometry(3.4, 20, 12), new THREE.MeshBasicMaterial({color: '#F4EED8', fog: false}));
        for (const [x, y, z, r] of [[1, 1, 3, 0.7], [-1.2, -0.6, 3, 0.5], [0.4, -1.4, 3, 0.4]]) {
            const crater = new THREE.Mesh(new THREE.CircleGeometry(r, 12), new THREE.MeshBasicMaterial({color: '#DCD3B4', fog: false}));
            crater.position.set(x, y, z);
            this.moonDisc.add(crater);
        }
        const starPos = [];
        for (let i = 0; i < 500; i++) {
            const a = Math.random() * Math.PI * 2, e = 0.15 + Math.random() * 1.35;
            starPos.push(Math.cos(a) * Math.cos(e) * 110, Math.sin(e) * 110, Math.sin(a) * Math.cos(e) * 110);
        }
        const sg = new THREE.BufferGeometry();
        sg.setAttribute('position', new THREE.Float32BufferAttribute(starPos, 3));
        this.stars = new THREE.Points(sg, new THREE.PointsMaterial({color: '#FFFFFF', size: 1.3, sizeAttenuation: false, transparent: true, opacity: 0, fog: false, depthWrite: false}));
        g.add(this.sunDisc, this.moonDisc, this.stars);
        this.celestial = g;
        this.scene.add(g);
    }

    /**
     * hour 0–24, weather (clear | cloudy | rain | snow), season (spring | summer | autumn | winter).
     * Called every frame by the app; heavy work happens only when something actually changes.
     */
    setEnvironment({hour, weather, season}) {
        const env = this.env;
        if (season !== env.season) this.applySeason(season);
        if (weather !== env.weather) this.setParticles(weather, season);
        env.hour = hour;
        env.weather = weather;
        env.season = season;
        const day = smooth(5.3, 7.3, hour) * (1 - smooth(18.6, 20.6, hour));
        const night = 1 - day;
        const over = OVERCAST[weather] ?? 0;
        // Sky colours through the day, greyer under clouds
        const [top, hor] = skyAt(hour);
        const grey = new THREE.Color(SNOWY.includes(weather) ? '#C9D3DC' : weather === 'hail' ? '#9BA6B0' : '#8F99A3');
        const topC = top.clone().lerp(grey.clone().multiplyScalar(0.35 + day * 0.65), over * 0.8);
        const horC = hor.clone().lerp(grey.clone().multiplyScalar(0.45 + day * 0.55), over * 0.7);
        const u = this.sky.material.uniforms;
        u.top.value.copy(topC);
        u.horizon.value.copy(horC);
        u.ground.value.copy(horC);
        this.scene.fog.color.copy(horC);
        this.scene.background.copy(horC);
        // Sun and moon arcs: rise in the east (+x), set in the west
        const sa = (hour - 6) / 12 * Math.PI;
        const sunDir = new THREE.Vector3(Math.cos(sa), Math.sin(sa) * 0.85, -0.35).normalize();
        const moonDir = new THREE.Vector3(-Math.cos(sa), -Math.sin(sa) * 0.85 + 0.15, 0.3).normalize();
        this.sunDisc.position.copy(sunDir).multiplyScalar(100);
        this.moonDisc.position.copy(moonDir).multiplyScalar(100);
        this.moonDisc.lookAt(0, 0, 0);
        this.sunDisc.visible = sunDir.y > -0.1 && over < 0.6;
        this.moonDisc.visible = moonDir.y > -0.1 && over < 0.6;
        this.stars.material.opacity = night * (1 - over) * 0.95;
        // Lights: warm sun by day, a cool moonlight at night
        const lightDir = (day > 0.5 ? sunDir : moonDir).clone();
        lightDir.y = Math.max(0.35, lightDir.y);
        const c = this.sun.target.position;
        this.sun.position.copy(c).addScaledVector(lightDir.normalize(), 22);
        const low = 1 - Math.min(1, Math.max(0, sunDir.y * 2.5)); // dawn and dusk are golden
        this.sun.color.set(day > 0.5 ? '#FFE2B8' : '#9FB6E8').lerp(new THREE.Color('#FF9E5E'), day > 0.5 ? low * 0.6 : 0);
        this.sun.intensity = (day * 2.1 + night * 0.45) * (1 - over * 0.55);
        this.hemi.intensity = 0.42 + day * 0.73 - over * 0.15;
        const roomK = Math.max(smooth(17.5, 20, hour) + (1 - smooth(5.5, 7.5, hour)), over * 0.4);
        for (const l of this.roomLights || []) l.intensity = Math.min(1, roomK) * 7;
        this.hemi.color.set(night > 0.5 ? '#6E7FB0' : '#EAF4FF');
        // Lamps, windows and neon glow after sunset (only touched when it changes noticeably)
        const glowK = Math.max(night, over * 0.5);
        if (Math.abs((this.lastGlow ?? -1) - glowK) > 0.02) {
            this.lastGlow = glowK;
            setNightGlow(glowK);
            this.itemGroup?.traverse(o => o.isPointLight && (o.intensity = 0.15 + glowK * 1.4));
        }
        this.renderer.toneMappingExposure = 1.05 - night * 0.15 + (weather === 'sunny' ? day * 0.08 : 0);
        // A thunderstorm: now and then a flash lights everything up
        if (weather === 'rain') {
            if (!this.flash && Math.random() < 0.0025) this.flash = {t: 0};
            if (this.flash) {
                this.flash.t += 1 / 60;
                const k = this.flash.t < 0.08 ? 1 : this.flash.t < 0.16 ? 0.2 : this.flash.t < 0.24 ? 0.8 : 0;
                this.hemi.intensity += k * 2.5;
                if (this.flash.t > 0.3) {
                    this.flash = null;
                    this.emit('thunder');
                }
            }
        } else this.flash = null;
        this.clouds.children.forEach(cl => cl.children.forEach(p => p.material.color.set(over > 0.5 ? '#C9CED4' : '#FFFFFF')));
        this.clouds.scale.setScalar(1 + over * 0.6);
        this.clouds.position.y = -over * 6;
    }

    /** Trees, bushes, ground and roofs for the season. */
    applySeason(season, force = false) {
        if (!force && this.env && season === this.env.season && this.seasonApplied) return;
        this.seasonApplied = true;
        this.grass.material.map = groundTexture(season);
        this.grass.material.needsUpdate = true;
        const pal = {
            spring: ['#F7B6CF', '#7CB342', '#FFFFFF', '#5E9A3E'],
            summer: [PALETTE.leaf, PALETTE.leafDark, '#6FAF45', PALETTE.leafDark],
            autumn: ['#E07B39', '#C8452C', '#E9B44C', '#B5562E'],
            winter: ['#EEF3F6', '#D9E4EA', '#F7FAFC', '#CAD6DD'],
        }[season];
        for (const t of this.trees || []) {
            // In winter some trees stand bare ("dry"), the others carry snow; late autumn thins a few out
            const bare = (season === 'winter' && t.i % 3 === 1) || (season === 'autumn' && t.i % 5 === 2);
            t.branches.visible = bare;
            t.foliage.forEach((f, k) => {
                f.visible = !bare;
                f.material = mat(pal[(k + t.i) % pal.length]);
            });
        }
        for (const b of this.bushes || []) b.material = mat({spring: '#8CC152', summer: PALETTE.leaf, autumn: '#C98A3A', winter: '#E8EEF2'}[season]);
        for (const cap of this.snowCaps || []) cap.visible = season === 'winter';
        for (const f of this.flowers || []) f.visible = season !== 'winter';
        if (this.env) this.setParticles(this.env.weather, season);
    }

    /** Rain, snow, falling autumn leaves or spring petals around the camera. */
    setParticles(weather, season) {
        if (this.particles) {
            this.scene.remove(this.particles);
            this.particles.geometry.dispose();
            this.particles = null;
        }
        let kind = RAINY.includes(weather) && weather !== 'hail' ? 'rain' : weather === 'hail' ? 'hail' : SNOWY.includes(weather) ? 'snow' : season === 'autumn' ? 'leaves' : season === 'spring' && weather !== 'cloudy' ? 'petals' : null;
        if (!kind) return;
        // How hard it falls: drizzle and light snow are a few drops and flakes, a storm or a blizzard many
        const n = {rain: weather === 'drizzle' ? 450 : 2200, snow: weather === 'light_snow' ? 500 : 2400, hail: 900, leaves: 140, petals: 160}[kind];
        const pos = new Float32Array(n * (kind === 'rain' ? 6 : 3));
        const col = new Float32Array(n * 3);
        const leafCols = ['#E07B39', '#C8452C', '#E9B44C', '#B5562E'].map(c => new THREE.Color(c));
        const petalCols = ['#F7B6CF', '#FFFFFF', '#F49AC1'].map(c => new THREE.Color(c));
        for (let i = 0; i < n; i++) {
            const x = (Math.random() - 0.5) * 50, y = Math.random() * 22, z = (Math.random() - 0.5) * 50;
            if (kind === 'rain') pos.set([x, y, z, x + 0.05, y - 0.55, z], i * 6);
            else pos.set([x, y, z], i * 3);
            const cc = kind === 'leaves' ? leafCols[i % 4] : kind === 'petals' ? petalCols[i % 3] : kind === 'hail' ? new THREE.Color('#E8F1F6') : new THREE.Color('#FFFFFF');
            col.set([cc.r, cc.g, cc.b], i * 3);
        }
        const g = new THREE.BufferGeometry();
        g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        if (kind === 'rain') {
            this.particles = new THREE.LineSegments(g, new THREE.LineBasicMaterial({color: '#B7CBDD', transparent: true, opacity: 0.55, fog: false}));
        } else {
            g.setAttribute('color', new THREE.BufferAttribute(col, 3));
            this.particles = new THREE.Points(g, new THREE.PointsMaterial({size: kind === 'snow' ? (weather === 'snow' ? 0.18 : 0.12) : kind === 'hail' ? 0.1 : 0.22, vertexColors: true, transparent: true, opacity: 0.95, depthWrite: false}));
        }
        this.particles.userData = {kind, n, heavy: weather === 'rain' || weather === 'snow', seeds: Array.from({length: n}, () => Math.random() * 10)};
        this.particles.frustumCulled = false;
        this.scene.add(this.particles);
    }

    updateParticles(dt) {
        const p = this.particles;
        if (!p) return;
        const {kind, n, seeds, heavy} = p.userData;
        const a = p.geometry.attributes.position.array;
        const c = this.cam.target;
        const fall = {rain: heavy ? 20 : 11, snow: heavy ? 1.8 : 1.0, hail: 13, leaves: 0.9, petals: 0.7}[kind];
        const stride = kind === 'rain' ? 6 : 3;
        for (let i = 0; i < n; i++) {
            const k = i * stride;
            const sd = seeds[i];
            a[k + 1] -= fall * dt * (0.7 + (sd % 1) * 0.6);
            if (kind !== 'rain' && kind !== 'hail') { // snow drifts, leaves and petals flutter
                const sway = kind === 'snow' ? (heavy ? 1.2 : 0.4) : 1.4;
                a[k] += Math.sin(this.time * 1.3 + sd * 6) * sway * dt;
                a[k + 2] += Math.cos(this.time * 1.1 + sd * 4) * sway * dt;
            } else a[k + 4] = a[k + 1] - 0.55;
            // Recycle around the camera target
            if (a[k + 1] < 0) {
                const x = c.x + (Math.random() - 0.5) * 50, z = c.z + (Math.random() - 0.5) * 50, y = 18 + Math.random() * 4;
                a[k] = x; a[k + 1] = y; a[k + 2] = z;
                if (kind === 'rain') { a[k + 3] = x + 0.05; a[k + 4] = y - 0.55; a[k + 5] = z; }
            }
        }
        p.geometry.attributes.position.needsUpdate = true;
    }

    // ------------------------------------------------------------ camera

    recenter(instant = false) {
        const s = this.size || 10;
        this.goal.target.set((this.W || s) / 2, (this.viewFloor || 0) * FH, (this.D || s) / 2 + 0.4);
        // A portrait screen needs the camera further back for the room to fit across
        const k = Math.max(1, 0.8 / Math.max(0.3, this.camera.aspect));
        this.goal.dist = (s * 1.55 + 3) * k;
        if (this.top) this.goal.dist = this.topDist((s * 2.3 + 3) * k);
        if (instant) {
            this.cam.target.copy(this.goal.target);
            this.cam.dist = this.goal.dist;
        }
    }

    topDist(d) {
        return d * Math.tan(THREE.MathUtils.degToRad(FOV / 2)) / Math.tan(THREE.MathUtils.degToRad(FOV_TOP / 2));
    }

    setTopView(on) {
        if (on === this.top) return;
        this.top = on;
        this.wallsDown = on; // a build view: every wall is cut down, nothing hides the floor
        if (on) {
            this.saved = {yaw: this.goal.yaw, pitch: this.goal.pitch, dist: this.goal.dist};
            this.goal.pitch = 1.53;
            // Snap to the nearest quarter turn, preferring the entrance at the bottom of the screen (yaw 0)
            this.goal.yaw = Math.floor((this.goal.yaw + Math.PI / 4 - 0.001) / (Math.PI / 2)) * (Math.PI / 2);
            this.goal.fov = FOV_TOP;
            this.goal.dist = this.topDist(Math.max(this.goal.dist, ((this.size || 10) * 2.3 + 3) * Math.max(1, 0.8 / Math.max(0.3, this.camera.aspect))));
        } else {
            this.goal.pitch = this.saved?.pitch ?? 0.82;
            this.goal.yaw = this.saved?.yaw ?? Math.PI / 4;
            this.goal.fov = FOV;
            this.goal.dist = this.saved?.dist ?? 17;
        }
        this.emit('topview', on);
    }

    /** Build mode, outdoor tab: the camera pulls back to show the whole plot (garden, terrace, yards). */
    frameOutdoor(on) {
        if (!!on === !!this.outdoorFramed) return;
        this.outdoorFramed = !!on;
        const s = this.size || 10;
        if (on) {
            this.savedFrame = {target: this.goal.target.clone(), dist: this.goal.dist};
            this.goal.target.set((this.W || s) / 2 + 0.5 - (this.parkingW || 0) * 0.3, 0, (this.D || s) / 2 + 0.8);
            this.goal.dist = this.top ? this.topDist((s + 2 * OUT) * 2.1) : (s + 2 * OUT) * 1.55 + 3;
        } else if (this.savedFrame) {
            this.goal.target.copy(this.savedFrame.target);
            this.goal.dist = this.savedFrame.dist;
        }
    }

    rotate(delta) {
        this.goal.yaw += delta;
    }

    zoom(factor) {
        const [min, max] = this.top ? [this.topDist(5), this.topDist(70)] : [5, 70];
        this.goal.dist = THREE.MathUtils.clamp(this.goal.dist * factor, min, max);
    }

    pan(dx, dy) {
        // dx, dy in pixels: move the target along the ground, relative to the view
        const k = this.cam.dist * Math.tan(THREE.MathUtils.degToRad(this.cam.fov / 2)) * 2 / this.canvas.clientHeight;
        const right = new THREE.Vector3(Math.cos(this.cam.yaw), 0, -Math.sin(this.cam.yaw));
        const fwd = new THREE.Vector3(-Math.sin(this.cam.yaw), 0, -Math.cos(this.cam.yaw));
        const pitchK = this.top ? 1 : 1 / Math.max(0.5, Math.sin(this.cam.pitch));
        this.goal.target.addScaledVector(right, -dx * k).addScaledVector(fwd, dy * k * pitchK);
        const W = this.W || 10, D = this.D || 10;
        this.goal.target.x = THREE.MathUtils.clamp(this.goal.target.x, -8 - (this.parkingW || 0), W + 6 + OUT);
        this.goal.target.z = THREE.MathUtils.clamp(this.goal.target.z, -5 - OUT, D + 12 + OUT);
        this.cam.target.copy(this.goal.target); // panning follows the finger without lag
    }

    updateCamera(dt) {
        const k = 1 - Math.pow(0.0005, dt);
        const c = this.cam, g = this.goal;
        c.yaw += (g.yaw - c.yaw) * k;
        c.pitch += (g.pitch - c.pitch) * k;
        c.dist += (g.dist - c.dist) * k;
        c.fov += (g.fov - c.fov) * k;
        c.target.lerp(g.target, k);
        const cp = Math.cos(c.pitch);
        this.camera.position.set(c.target.x + c.dist * cp * Math.sin(c.yaw), c.target.y + c.dist * Math.sin(c.pitch), c.target.z + c.dist * cp * Math.cos(c.yaw));
        this.camera.lookAt(c.target);
        // The fog only softens the far horizon, wherever the camera is
        this.scene.fog.near = c.dist + 14;
        this.scene.fog.far = c.dist + 60;
        // Depth precision: the near plane follows the distance, so far and narrow views (top view) don't flicker
        const near = Math.min(12, Math.max(0.1, c.dist * 0.12)), far = c.dist + 170;
        if (Math.abs(this.camera.fov - c.fov) > 0.01 || Math.abs(this.camera.near - near) > 0.05 || Math.abs(this.camera.far - far) > 1) {
            this.camera.fov = c.fov;
            this.camera.near = near;
            this.camera.far = far;
            this.camera.updateProjectionMatrix();
        }
        // Walls whose outside faces the camera go down, the others come back up
        const vx = Math.sin(c.yaw), vz = Math.cos(c.yaw);
        for (const w of this.walls || []) {
            const facing = w.n[0] * vx + w.n[1] * vz > 0.25;
            // Only the floor being looked at opens up; the floors below keep their walls (they are under it)
            const mine = (w.floor || 0) === (this.viewFloor || 0);
            // The player's choice: walls that open towards the camera (Sims style), always low, or always up
            const mode = this.wallMode || 'adaptive';
            const target = !mine ? 1 : mode === 'low' ? WALL_LOW : mode === 'high' ? 1 : (facing || this.wallsDown ? WALL_LOW : 1);
            w.h = w.h == null ? target : w.h + (target - w.h) * Math.min(1, dt * 8);
            w.group.scale.y = w.h;
            w.cap.visible = w.h > 0.5;
            if (w.extras) w.extras.forEach(o => (o.visible = w.h > 0.5));
            w.decor.forEach(o => (o.visible = w.h > 0.6));
        }
    }

    bindInput() {
        const el = this.canvas;
        const pts = new Map();
        let drag = null;
        el.addEventListener('contextmenu', e => e.preventDefault());
        let grab = null; // a press taken by build mode (dragging a piece): no orbit while it lasts
        el.addEventListener('pointerdown', e => {
            el.setPointerCapture(e.pointerId);
            if (!pts.size && e.button === 0 && this.onPress?.(e)) {
                grab = {id: e.pointerId, x0: e.clientX, y0: e.clientY, moved: false};
                el.style.cursor = 'grabbing';
                return;
            }
            pts.set(e.pointerId, {x: e.clientX, y: e.clientY});
            if (pts.size === 1) drag = {x0: e.clientX, y0: e.clientY, t0: performance.now(), moved: false, pan: e.button === 2 || e.shiftKey, button: e.button};
            else if (drag) drag.moved = true;
            if (pts.size === 2) {
                const [a, b] = [...pts.values()];
                this.pinch = {d: Math.hypot(a.x - b.x, a.y - b.y), mx: (a.x + b.x) / 2, my: (a.y + b.y) / 2};
            }
        });
        el.addEventListener('pointermove', e => {
            if (grab && e.pointerId === grab.id) {
                if (Math.hypot(e.clientX - grab.x0, e.clientY - grab.y0) > 4) grab.moved = true;
                this.onDragMove?.(e);
                return;
            }
            const prev = pts.get(e.pointerId);
            if (!prev) {
                this.emit('hover', e);
                return;
            }
            const dx = e.clientX - prev.x, dy = e.clientY - prev.y;
            prev.x = e.clientX;
            prev.y = e.clientY;
            if (pts.size === 2 && this.pinch) {
                const [a, b] = [...pts.values()];
                const d = Math.hypot(a.x - b.x, a.y - b.y), mx = (a.x + b.x) / 2, my = (a.y + b.y) / 2;
                if (d > 0) this.zoom(this.pinch.d / d);
                this.pan(mx - this.pinch.mx, my - this.pinch.my);
                this.pinch = {d, mx, my};
                return;
            }
            if (!drag) return;
            if (!drag.moved && Math.hypot(e.clientX - drag.x0, e.clientY - drag.y0) > 7) drag.moved = true;
            if (!drag.moved) {
                this.emit('hover', e);
                return;
            }
            if (drag.pan) this.pan(dx, dy);
            else {
                this.goal.yaw -= dx * 0.008;
                if (!this.top) this.goal.pitch = THREE.MathUtils.clamp(this.goal.pitch + dy * 0.005, 0.32, 1.45);
            }
        });
        const end = e => {
            if (grab && e.pointerId === grab.id) {
                const moved = grab.moved;
                grab = null;
                el.style.cursor = '';
                this.onDragEnd?.(e, moved);
                return;
            }
            pts.delete(e.pointerId);
            if (pts.size < 2) this.pinch = null;
            if (drag && pts.size === 0) {
                if (!drag.moved && performance.now() - drag.t0 < 600) this.emit(drag.button === 2 ? 'rtap' : 'tap', e);
                drag = null;
            }
        };
        el.addEventListener('pointerup', end);
        el.addEventListener('pointercancel', end);
        el.addEventListener('wheel', e => {
            e.preventDefault();
            this.zoom(Math.exp(e.deltaY * 0.0012));
        }, {passive: false});
    }

    ray(e) {
        const rect = this.canvas.getBoundingClientRect();
        const ndc = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect.top) / rect.height) * 2 + 1);
        this.raycaster.setFromCamera(ndc, this.camera);
        return this.raycaster;
    }

    /** The tile under the pointer (may be outside the room), or null. */
    tileAt(e) {
        const p = new THREE.Vector3();
        if (!this.ray(e).ray.intersectPlane(this.ground, p)) return null;
        return {x: Math.floor(p.x), z: Math.floor(p.z), px: p.x, pz: p.z};
    }

    /** The furniture id under the pointer, or null. */
    itemAt(e) {
        const hit = this.ray(e).intersectObjects(this.itemGroup.children, true).find(h => shown(h.object));
        return hit ? hit.object.userData.itemId ?? null : null;
    }

    /** The person under the pointer (their agent id), or null. */
    agentAt(e) {
        if (!this.agentGroup) return null;
        const hit = this.ray(e).intersectObjects(this.layers.agent.filter(Boolean), true).find(h => shown(h.object));
        return hit ? hit.object.userData.agentId ?? null : null;
    }

    /** Litter under the pointer ("x,z"), or null. */
    litterAt(e) {
        if (!this.fxGroup) return null;
        const hit = this.ray(e).intersectObjects(this.layers.fx.filter(Boolean), true).find(h => h.object.userData.litterAt && shown(h.object));
        return hit ? hit.object.userData.litterAt : null;
    }

    /** Coloured squares under some tiles (a waiter's tables while they are selected), with their table numbers. */
    highlightTables(tables, color) {
        if (this.tableMarks) this.root.remove(this.tableMarks);
        this.tableMarks = null;
        (this.tableLabels || []).forEach(l => l.remove());
        this.tableLabels = [];
        if (!tables?.length) return;
        const g = new THREE.Group();
        const m = new THREE.MeshBasicMaterial({color, transparent: true, opacity: 0.45, depthWrite: false});
        for (const tb of tables) {
            for (const [x, vz] of tb.tiles) { // tiles of the simulation: floors are FLOOR_STRIDE apart along z
                const f = Math.max(0, Math.floor((vz + 100) / FLOOR_STRIDE)), z = vz - f * FLOOR_STRIDE;
                const p = new THREE.Mesh(new THREE.PlaneGeometry(1.1, 1.1), m);
                p.rotation.x = -Math.PI / 2;
                p.position.set(x + 0.5, f * FH + 0.05, z + 0.5);
                g.add(p);
            }
            const f0 = Math.max(0, Math.floor((tb.tiles[0][1] + 100) / FLOOR_STRIDE));
            const label = document.createElement('div');
            label.className = 'table-num';
            label.style.background = color;
            label.textContent = tb.num;
            label.dataset.x = tb.tiles[0][0] + 0.5;
            label.dataset.z = tb.tiles[0][1] - f0 * FLOOR_STRIDE + 0.5;
            label.dataset.y = f0 * FH + 1.3;
            this.overlay.appendChild(label);
            this.tableLabels.push(label);
        }
        this.tableMarks = g;
        this.root.add(g);
    }

    /** A golden ring on the floor under the selected person (follows them every frame). */
    select(mesh) {
        if (!this.selRing) {
            this.selRing = new THREE.Mesh(new THREE.RingGeometry(0.36, 0.46, 32), new THREE.MeshBasicMaterial({color: '#E2B23A', transparent: true, opacity: 0.9, depthWrite: false}));
            this.selRing.rotation.x = -Math.PI / 2;
            this.scene.add(this.selRing);
        }
        this.selTarget = mesh;
        this.selRing.visible = !!mesh;
    }

    /** The little sign hanging by the door: open, no new guests, closed. */
    setDoorStatus(status, labels) {
        if (this.doorSign) this.doorSign.parent?.remove(this.doorSign);
        const c = document.createElement('canvas');
        c.width = 256;
        c.height = 96;
        const g = c.getContext('2d');
        const col = {open: '#4E8A3A', paused: '#E0A526', closed: '#B5302A'}[status] || '#4E8A3A';
        g.fillStyle = '#FFF6E9';
        g.beginPath();
        g.roundRect(4, 4, 248, 88, 18);
        g.fill();
        g.lineWidth = 8;
        g.strokeStyle = col;
        g.stroke();
        g.fillStyle = col;
        g.font = '800 34px ui-rounded, system-ui, sans-serif';
        g.textAlign = 'center';
        g.textBaseline = 'middle';
        g.fillText((labels[status] || '').toUpperCase().slice(0, 14), 128, 50);
        const t = new THREE.CanvasTexture(c);
        t.colorSpace = THREE.SRGBColorSpace;
        const m = new THREE.Mesh(new THREE.PlaneGeometry(0.9, 0.34), new THREE.MeshBasicMaterial({map: t, transparent: true}));
        m.position.set(this.door + 1.15, 1.6, this.D + 0.12);
        this.doorSign = m;
        this.root.add(m);
        const front = this.walls.find(w => w.isFront);
        if (front) front.extras = [...front.extras.filter(o => o !== this.oldDoorSign && o.parent), m]; // hidden with the front wall
        this.oldDoorSign = m;
    }

    plotAt(e) {
        if (!this.gardenGroup) return null;
        const hit = this.ray(e).intersectObjects(this.gardenGroup.children, true).find(h => h.object.userData.plot != null);
        return hit ? hit.object.userData.plot : null;
    }

    /** Screen position (CSS pixels) of a world point, for the bubbles over people. */
    project(v) {
        const p = v.clone().project(this.camera);
        const r = this.canvas.getBoundingClientRect();
        return {x: (p.x + 1) / 2 * r.width, y: (1 - p.y) / 2 * r.height, visible: p.z < 1 && p.z > -1};
    }

    frame() {
        const dt = Math.min(0.05, this.clock.getDelta());
        this.time += dt;
        this.updateCamera(dt);
        // Gentle life: fish swim
        for (const o of this.items.values()) {
            // Fireplaces: flames dance
            if (o.userData.flames) o.userData.flames.forEach((f, i) => {
                const k = Math.sin(this.time * (9 + i * 2.3) + i * 1.7) * 0.5 + Math.sin(this.time * (17 + i) + i) * 0.25;
                f.scale.set(1 + k * 0.12, 1 + k * 0.3, 1 + k * 0.12);
            });
            if (o.userData.bob) o.userData.bob.position.y = 0.75 + Math.sin(this.time * 2) * 0.07; // the little ghost floats
            if (o.userData.fish) o.userData.fish.forEach((f, i) => {
                f.position.x = Math.sin(this.time * 0.6 + i * 2) * 0.6;
                f.rotation.y = Math.cos(this.time * 0.6 + i * 2) > 0 ? 0 : Math.PI;
            });
        }
        // The ring of clouds turns very slowly
        if (this.clouds) this.clouds.rotation.y += dt * 0.004;
        if (this.sky) this.sky.position.copy(this.camera.position);
        if (this.celestial) this.celestial.position.copy(this.camera.position);
        this.updateParticles(dt);
        for (const l of this.tableLabels || []) {
            const p = this.project(new THREE.Vector3(+l.dataset.x, +(l.dataset.y || 1.3), +l.dataset.z));
            l.style.transform = `translate(${p.x}px, ${p.y}px)`;
        }
                if (this.selRing && this.selTarget) {
            const wp = this.selTarget.getWorldPosition(new THREE.Vector3()); // people on upper floors live in floor layers
            this.selRing.position.set(wp.x, wp.y + 0.04 - this.selTarget.position.y, wp.z);
            this.selRing.scale.setScalar(1 + Math.sin(this.time * 4) * 0.06);
        }
        this.traffic?.update(dt);
        this.emit('frame', dt, this.time);
        this.renderer.render(this.scene, this.camera);
    }
}
