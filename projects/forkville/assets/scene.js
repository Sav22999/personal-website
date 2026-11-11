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

const WALL_H = 2.4;
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

/** Stone paving for the terrace. */
function pavingTexture(w, d) {
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
export function weatherFor(season, dayKey) {
    let h = 0;
    for (const ch of String(dayKey)) h = (h * 31 + ch.charCodeAt(0)) >>> 0;
    const r = (h % 1000) / 1000;
    const table = {
        winter: [['snow', 0.4], ['cloudy', 0.3], ['clear', 0.3]],
        spring: [['rain', 0.25], ['cloudy', 0.25], ['clear', 0.5]],
        summer: [['clear', 0.7], ['cloudy', 0.15], ['rain', 0.15]],
        autumn: [['rain', 0.35], ['cloudy', 0.35], ['clear', 0.3]],
    }[season];
    let acc = 0;
    for (const [w, p] of table) if (r < (acc += p)) return w;
    return 'clear';
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

export class World {
    constructor(canvas, overlay) {
        this.canvas = canvas;
        this.overlay = overlay;
        this.listeners = {};
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

    /** Street, sidewalk, trees and lamp posts, placed around a room of `size`. */
    buildStreet(size) {
        this.outdoor.clear();
        this.trees = [];
        this.bushes = [];
        this.snowCaps = [];
        this.flowers = [];
        const side = new THREE.Mesh(new THREE.BoxGeometry(size + 30, 0.06, 2.2), mat('#E4D6C1'));
        side.position.set(size / 2, 0.0, size + 1.4 + OUT);
        side.receiveShadow = true;
        const road = new THREE.Mesh(new THREE.BoxGeometry(size + 30, 0.04, 4), mat('#5B5754'));
        road.position.set(size / 2, -0.01, size + 4.5 + OUT);
        road.receiveShadow = true;
        this.outdoor.add(side, road);
        for (let x = -12; x < size + 14; x += 3) {
            const stripe = new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.05, 0.14), mat('#F4E9C8'));
            stripe.position.set(x, 0.0, size + 4.5 + OUT);
            this.outdoor.add(stripe);
        }
        // Path from the door to the sidewalk
        // The terrace in front (stone paving) and the path to the door across it
        const patio = new THREE.Mesh(new THREE.PlaneGeometry(size + OUT, OUT), new THREE.MeshStandardMaterial({map: pavingTexture(size + OUT, OUT), roughness: 0.95}));
        patio.rotation.x = -Math.PI / 2;
        patio.position.set((size + OUT) / 2, -0.035, size + OUT / 2);
        patio.receiveShadow = true;
        const path = new THREE.Mesh(new THREE.BoxGeometry(1.0, 0.05, OUT + 0.4), mat('#D9C7AC'));
        path.position.set(Math.floor(size / 2) + 0.5, -0.02, size + OUT / 2 + 0.1);
        path.receiveShadow = true;
        this.outdoor.add(patio, path);
        const tree = (x, z, s = 1) => {
            const g = new THREE.Group();
            const trunk = new THREE.Mesh(new THREE.CylinderGeometry(0.12 * s, 0.16 * s, 1.2 * s, 7), mat(PALETTE.woodDark));
            trunk.position.y = 0.6 * s;
            g.add(trunk);
            const foliage = [];
            [[0, 1.6, 0, 0.8], [0.35, 1.35, 0.2, 0.55], [-0.3, 1.4, -0.2, 0.6], [0, 2.1, 0, 0.55]].forEach(([dx, y, dz, r], i) => {
                const b = new THREE.Mesh(new THREE.IcosahedronGeometry(r * s, 0), mat(i % 2 ? PALETTE.leafDark : PALETTE.leaf));
                b.position.set(dx * s, y * s, dz * s);
                g.add(b);
                foliage.push(b);
            });
            // Bare branches, shown when the tree has lost its leaves
            const branches = new THREE.Group();
            for (const [rx, rz, len] of [[0.6, 0, 0.9], [-0.5, 0.4, 0.8], [0.2, -0.7, 0.7], [-0.2, 0.2, 1.0]]) {
                const br = new THREE.Mesh(new THREE.CylinderGeometry(0.03 * s, 0.06 * s, len * s, 5), mat(PALETTE.woodDark));
                br.position.y = 1.2 * s + len * s * 0.4;
                br.rotation.set(rx, 0, rz);
                branches.add(br);
            }
            branches.visible = false;
            g.add(branches);
            this.trees.push({g, foliage, branches, i: this.trees.length});
            g.position.set(x, 0, z);
            g.traverse(o => o.isMesh && (o.castShadow = true));
            this.outdoor.add(g);
        };
        const lamp = (x, z) => {
            const g = new THREE.Group();
            const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.07, 2.6, 8), mat('#2F3A3A'));
            pole.position.y = 1.3;
            const head = new THREE.Mesh(new THREE.SphereGeometry(0.18, 10, 8), glowMat('#FFE7A8'));
            head.position.y = 2.7;
            g.add(pole, head);
            g.position.set(x, 0, z);
            g.traverse(o => o.isMesh && (o.castShadow = true));
            this.outdoor.add(g);
        };
        for (let x = -2; x <= size + 2; x += 6) tree(x, size + 7.8 + OUT, 1 + ((x * 7) % 3) * 0.12);
        [[size + OUT + 1.6, 2, 1.2], [size + OUT + 2.5, size * 0.6, 1], [size + OUT + 1.8, size - 1, 0.9], [-6.5, size + 0.5, 1.1], [-7, 1.5, 1.2], [size / 2, -3.5, 1.3], [2, -4, 1]].forEach(([x, z, s]) => tree(x, z, s));
        for (let x = 1; x < size; x += 5) lamp(x, size + 2.4 + OUT);
        this.buildTown(size);
        if (this.env) this.applySeason(this.env.season, true);
    }

    /** The neighbourhood of Forkville: little houses across the street and behind, fences, flowers, benches. */
    buildTown(size) {
        const rnd = (n) => Math.abs(Math.sin(n * 91.7 + size) * 4371.3) % 1;
        const walls = ['#FFF3E0', '#F6D9B8', '#E8EFD9', '#F4E1E6', '#E3ECF4', '#FCEBC5'];
        const roofs = ['#B43E5C', '#7A8B3A', '#E2B23A', '#3E7CB4', '#C2572B', '#6B4FA0'];
        const glow = glowMat('#FFD27A');
        const house = (x, z, rot, i) => {
            const g = new THREE.Group();
            const w = 3.2 + rnd(i) * 1.6, d = 3 + rnd(i + 3) * 1.2, h = 2.2 + rnd(i + 7) * 1.4;
            const body = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), mat(walls[i % walls.length]));
            body.position.y = h / 2;
            const roof = new THREE.Mesh(new THREE.ConeGeometry(Math.max(w, d) * 0.78, 1.6 + rnd(i + 1), 4), mat(roofs[(i * 5) % roofs.length]));
            roof.position.y = h + (1.6 + rnd(i + 1)) / 2;
            roof.rotation.y = Math.PI / 4;
            roof.scale.set(w / Math.max(w, d), 1, d / Math.max(w, d));
            const door = new THREE.Mesh(new THREE.BoxGeometry(0.7, 1.3, 0.08), mat('#7B4E2C'));
            door.position.set(0, 0.65, d / 2 + 0.03);
            g.add(body, roof, door);
            const cap = new THREE.Mesh(new THREE.ConeGeometry(Math.max(w, d) * 0.8, 0.5, 4), mat('#F7FAFC'));
            cap.position.y = h + (1.6 + rnd(i + 1)) * 0.78;
            cap.rotation.y = Math.PI / 4;
            cap.scale.set(w / Math.max(w, d), 1, d / Math.max(w, d));
            cap.visible = false;
            g.add(cap);
            this.snowCaps.push(cap);
            for (const sx of [-1, 1]) {
                const win = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.6, 0.06), glow);
                win.position.set(sx * w * 0.3, h * 0.62, d / 2 + 0.03);
                const frame = new THREE.Mesh(new THREE.BoxGeometry(0.74, 0.08, 0.1), mat('#FFFFFF'));
                frame.position.set(sx * w * 0.3, h * 0.62 - 0.34, d / 2 + 0.05);
                g.add(win, frame);
            }
            if (rnd(i + 11) > 0.4) {
                const chim = new THREE.Mesh(new THREE.BoxGeometry(0.4, 1, 0.4), mat('#9C4A2E'));
                chim.position.set(w * 0.25, h + 0.9, -d * 0.15);
                g.add(chim);
            }
            g.position.set(x, 0, z);
            g.rotation.y = rot;
            g.traverse(o => o.isMesh && (o.castShadow = o.receiveShadow = true));
            this.outdoor.add(g);
        };
        // Across the street, facing the restaurant
        let i = 0;
        for (let x = -10; x <= size + 10; x += 5.6) house(x + rnd(i) * 0.8, size + 11.5 + OUT, Math.PI, i++);
        // Behind the restaurant and on the far right
        for (let x = -2; x <= size + 6; x += 6.2) house(x, -9 - rnd(i) * 1.5, 0, i++);
        for (let z = 1; z < size; z += 6) house(size + OUT + 8 + rnd(i), z, -Math.PI / 2, i++);
        // A white picket fence around the plot (open at the front)
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
            g.traverse(o => o.isMesh && (o.castShadow = true));
            this.outdoor.add(g);
        };
        fence(-5, -2.5, size + OUT + 0.5, -2.5);
        fence(-5, -2.5, -5, size + OUT);
        fence(size + OUT + 0.5, -2.5, size + OUT + 0.5, size + OUT);
        // Flower beds and bushes along the front
        const petals = ['#F25F5C', '#FFD23F', '#F7A1C4', '#FFFFFF', '#9B5DE5'];
        for (let k = 0; k < 46; k++) {
            const x = -4.5 + rnd(k + 50) * (size + 6.5), z = size + OUT + 0.2 + rnd(k + 80) * 0.15;
            if (Math.abs(x - (Math.floor(size / 2) + 0.5)) < 1.2) continue; // keep the path clear
            const f = new THREE.Mesh(new THREE.IcosahedronGeometry(0.09, 0), mat(petals[k % petals.length]));
            f.position.set(x, 0.18, z);
            const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.015, 0.015, 0.18, 4), mat(PALETTE.leafDark));
            stem.position.set(x, 0.09, z);
            this.outdoor.add(f, stem);
            this.flowers.push(f, stem);
        }
        for (const [x, z, r] of [[-4.3, size - 0.6, 0.5], [-4.4, size + OUT - 0.5, 0.55], [size + OUT + 1.2, -1.6, 0.6], [-4.4, 9.5, 0.45]]) {
            const b = new THREE.Mesh(new THREE.IcosahedronGeometry(r, 0), mat(PALETTE.leaf));
            this.bushes.push(b);
            b.position.set(x, r * 0.8, z);
            b.castShadow = true;
            this.outdoor.add(b);
        }
        // Two benches on the sidewalk
        for (const x of [2, size - 3]) {
            const g = new THREE.Group();
            const seat = new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.08, 0.4), mat(PALETTE.wood));
            seat.position.y = 0.42;
            const back = new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.35, 0.06), mat(PALETTE.wood));
            back.position.set(0, 0.68, -0.18);
            g.add(seat, back);
            for (const sx of [-0.6, 0.6]) {
                const leg = new THREE.Mesh(new THREE.BoxGeometry(0.06, 0.42, 0.36), mat('#2F3A3A'));
                leg.position.set(sx, 0.21, 0);
                g.add(leg);
            }
            g.position.set(x, 0, size + 2.15 + OUT);
            g.rotation.y = Math.PI;
            g.traverse(o => o.isMesh && (o.castShadow = true));
            this.outdoor.add(g);
        }
    }

    // ------------------------------------------------------------ the restaurant

    /** Builds (or rebuilds) the whole restaurant from the state sent by the server. */
    setRestaurant(r, opts = {}) {
        this.root.clear();
        this.items.clear();
        this.walls = [];
        this.size = r.size;
        this.door = Math.floor(r.size / 2);
        this.buildStreet(r.size);
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
        // Warm room lights over the dining room: they come on after sunset (see setEnvironment)
        this.roomLights = [];
        const n = r.size <= 10 ? 2 : 3;
        for (let i = 0; i < n; i++) for (let j = 0; j < 2; j++) {
            const l = new THREE.PointLight('#FFD9A0', 0, r.size * 0.9, 1.2);
            l.position.set(r.size * (i + 0.5) / n, 2.5, r.size * (j + 0.5) / 2);
            this.root.add(l);
            this.roomLights.push(l);
        }
        for (const it of r.layout) this.addItem(it);
        if (opts.plots != null) this.setGarden(opts.plots, opts.garden || [], opts.now || Date.now() / 1000);
        this.fitSun();
        if (!opts.keepCamera) this.recenter(true);
    }

    fitSun() {
        const s = this.size;
        const c = new THREE.Vector3(s / 2 - 1.5, 0, s / 2 + 1);
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
        const s = this.size;
        const floor = new THREE.Mesh(new THREE.PlaneGeometry(s, s), floorMaterial(key, s));
        floor.rotation.x = -Math.PI / 2;
        floor.position.set(s / 2, 0.01, s / 2);
        floor.receiveShadow = true;
        this.floor = floor;
        this.root.add(floor);
        const mat_ = new THREE.Mesh(new THREE.BoxGeometry(0.9, 0.02, 0.7), mat(PALETTE.terracotta));
        mat_.position.set(Math.floor(s / 2) + 0.5, 0.03, s - 0.45);
        mat_.receiveShadow = true;
        const matIn = new THREE.Mesh(new THREE.BoxGeometry(0.7, 0.025, 0.5), mat('#A8472A'));
        matIn.position.copy(mat_.position);
        this.root.add(mat_, matIn);
        const base = new THREE.Mesh(new THREE.BoxGeometry(s + 0.6, 0.12, s + 0.6), mat('#CDBBA0'));
        base.position.set(s / 2, -0.085, s / 2); // top 2.5 cm under the floor: no flicker from far away
        base.receiveShadow = true;
        this.root.add(base);
    }

    setFloor(key) {
        this.floor.material = floorMaterial(key, this.size);
    }

    setWall(key) {
        const m = wallMaterial(key);
        this.walls.forEach(w => w.segments.forEach(seg => seg.userData.paint && (seg.material = m)));
    }

    buildWalls(key, name) {
        const s = this.size, T = 0.16;
        const paint = wallMaterial(key);
        const outside = mat('#F3E6D2');
        const trim = mat(PALETTE.woodDark);
        const glass = mat('#BFE3F0', {opacity: 0.45, roughness: 0.05});
        // Each side: where it is, which way it faces (outward normal), and which units are door or windows
        const sides = [
            {n: [0, -1], len: s, at: (i) => [i + 0.5, -T / 2], rotY: 0, windows: [2, s - 3]},
            {n: [0, 1], len: s, at: (i) => [i + 0.5, s + T / 2], rotY: 0, door: this.door, windows: [1, s - 2]},
            {n: [-1, 0], len: s, at: (i) => [-T / 2, i + 0.5], rotY: Math.PI / 2, windows: [2, Math.floor(s / 2), s - 3]},
            {n: [1, 0], len: s, at: (i) => [s + T / 2, i + 0.5], rotY: Math.PI / 2, windows: [3, s - 4]},
        ];
        for (const side of sides) {
            const g = new THREE.Group();
            const segments = [];
            const decor = [];
            for (let i = 0; i < side.len; i++) {
                const [x, z] = side.at(i);
                const isDoor = side.door === i;
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
                if (!isDoor && !isWin && !side.door && i % 3 === 1 && i > 0 && i < side.len - 1) {
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
            const cap = new THREE.Mesh(new THREE.BoxGeometry(side.rotY ? T + 0.04 : s + T * 2, 0.06, side.rotY ? s + T * 2 : T + 0.04), trim);
            cap.position.set(side.rotY ? side.at(0)[0] : s / 2, WALL_H, side.rotY ? s / 2 : side.at(0)[1]);
            g.add(cap);
            this.root.add(g);
            this.walls.push({group: g, n: side.n, segments, cap, decor, h: null, isFront: !!side.door}); // h: set at once on the first frame
        }
        // Awning and sign over the door, on the street side
        const frontWall = this.walls.find(w => w.isFront);
        const awning = new THREE.Group();
        for (let i = 0; i < 6; i++) {
            const stripe = new THREE.Mesh(new THREE.BoxGeometry(0.28, 0.05, 0.9), mat(i % 2 ? PALETTE.cream : PALETTE.terracotta));
            stripe.position.set(this.door + 0.5 - 0.7 + i * 0.28, WALL_H - 0.1, s + 0.55);
            stripe.rotation.x = 0.35;
            stripe.castShadow = true;
            awning.add(stripe);
        }
        this.sign = this.makeSign(name);
        this.sign.position.set(this.door + 0.5, WALL_H + 0.45, s + 0.2);
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
        obj.position.set(it.x + w / 2, 0, it.z + d / 2);
        obj.rotation.y = it.r * Math.PI / 2;
    }

    addItem(it) {
        const obj = furnitureModel(it.k);
        obj.userData.itemId = it.id;
        obj.traverse(o => (o.userData.itemId = it.id));
        this.placeObject(obj, it, this.catalogItems);
        this.itemGroup.add(obj);
        this.items.set(it.id, obj);
        return obj;
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
        const over = {clear: 0, cloudy: 0.45, rain: 0.7, snow: 0.55}[weather] || 0;
        // Sky colours through the day, greyer under clouds
        const [top, hor] = skyAt(hour);
        const grey = new THREE.Color(weather === 'snow' ? '#C9D3DC' : '#8F99A3');
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
        this.renderer.toneMappingExposure = 1.05 - night * 0.15;
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
        let kind = weather === 'rain' ? 'rain' : weather === 'snow' ? 'snow' : season === 'autumn' ? 'leaves' : season === 'spring' ? 'petals' : null;
        if (!kind) return;
        const n = {rain: 1400, snow: 1300, leaves: 140, petals: 160}[kind];
        const pos = new Float32Array(n * (kind === 'rain' ? 6 : 3));
        const col = new Float32Array(n * 3);
        const leafCols = ['#E07B39', '#C8452C', '#E9B44C', '#B5562E'].map(c => new THREE.Color(c));
        const petalCols = ['#F7B6CF', '#FFFFFF', '#F49AC1'].map(c => new THREE.Color(c));
        for (let i = 0; i < n; i++) {
            const x = (Math.random() - 0.5) * 50, y = Math.random() * 22, z = (Math.random() - 0.5) * 50;
            if (kind === 'rain') pos.set([x, y, z, x + 0.05, y - 0.55, z], i * 6);
            else pos.set([x, y, z], i * 3);
            const cc = kind === 'leaves' ? leafCols[i % 4] : kind === 'petals' ? petalCols[i % 3] : new THREE.Color('#FFFFFF');
            col.set([cc.r, cc.g, cc.b], i * 3);
        }
        const g = new THREE.BufferGeometry();
        g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        if (kind === 'rain') {
            this.particles = new THREE.LineSegments(g, new THREE.LineBasicMaterial({color: '#B7CBDD', transparent: true, opacity: 0.55, fog: false}));
        } else {
            g.setAttribute('color', new THREE.BufferAttribute(col, 3));
            this.particles = new THREE.Points(g, new THREE.PointsMaterial({size: kind === 'snow' ? 0.14 : 0.22, vertexColors: true, transparent: true, opacity: 0.95, depthWrite: false}));
        }
        this.particles.userData = {kind, n, seeds: Array.from({length: n}, () => Math.random() * 10)};
        this.particles.frustumCulled = false;
        this.scene.add(this.particles);
    }

    updateParticles(dt) {
        const p = this.particles;
        if (!p) return;
        const {kind, n, seeds} = p.userData;
        const a = p.geometry.attributes.position.array;
        const c = this.cam.target;
        const fall = {rain: 16, snow: 1.3, leaves: 0.9, petals: 0.7}[kind];
        const stride = kind === 'rain' ? 6 : 3;
        for (let i = 0; i < n; i++) {
            const k = i * stride;
            const sd = seeds[i];
            a[k + 1] -= fall * dt * (0.7 + (sd % 1) * 0.6);
            if (kind !== 'rain') { // snow drifts, leaves and petals flutter
                const sway = kind === 'snow' ? 0.4 : 1.4;
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
        this.goal.target.set(s / 2, 0, s / 2 + 0.4);
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
        const s = this.size || 10;
        this.goal.target.x = THREE.MathUtils.clamp(this.goal.target.x, -6, s + 4 + OUT);
        this.goal.target.z = THREE.MathUtils.clamp(this.goal.target.z, -3, s + 6 + OUT);
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
            const target = facing || this.wallsDown ? WALL_LOW : 1;
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
        el.addEventListener('pointerdown', e => {
            el.setPointerCapture(e.pointerId);
            pts.set(e.pointerId, {x: e.clientX, y: e.clientY});
            if (pts.size === 1) drag = {x0: e.clientX, y0: e.clientY, t0: performance.now(), moved: false, pan: e.button === 2 || e.shiftKey, button: e.button};
            else if (drag) drag.moved = true;
            if (pts.size === 2) {
                const [a, b] = [...pts.values()];
                this.pinch = {d: Math.hypot(a.x - b.x, a.y - b.y), mx: (a.x + b.x) / 2, my: (a.y + b.y) / 2};
            }
        });
        el.addEventListener('pointermove', e => {
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
            pts.delete(e.pointerId);
            if (pts.size < 2) this.pinch = null;
            if (drag && pts.size === 0) {
                if (!drag.moved && performance.now() - drag.t0 < 600 && drag.button !== 2) this.emit('tap', e);
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
        const hit = this.ray(e).intersectObjects(this.itemGroup.children, true)[0];
        return hit ? hit.object.userData.itemId ?? null : null;
    }

    /** The person under the pointer (their agent id), or null. */
    agentAt(e) {
        if (!this.agentGroup) return null;
        const hit = this.ray(e).intersectObjects(this.agentGroup.children, true)[0];
        return hit ? hit.object.userData.agentId ?? null : null;
    }

    /** Litter under the pointer ("x,z"), or null. */
    litterAt(e) {
        if (!this.fxGroup) return null;
        const hit = this.ray(e).intersectObjects(this.fxGroup.children, true).find(h => h.object.userData.litterAt);
        return hit ? hit.object.userData.litterAt : null;
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
        m.position.set(this.door + 1.15, 1.6, this.size + 0.12);
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
        if (this.selRing && this.selTarget) {
            this.selRing.position.set(this.selTarget.position.x, 0.04, this.selTarget.position.z);
            this.selRing.scale.setScalar(1 + Math.sin(this.time * 4) * 0.06);
        }
        this.emit('frame', dt, this.time);
        this.renderer.render(this.scene, this.camera);
    }
}
