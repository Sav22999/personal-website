/* Fischietto · Match simulation: lightweight 2.5D renderer (canvas, no libraries).
 *
 * World coordinates in metres: x along the court (-9 .. 9, net at x = 0; team on the left at x < 0),
 * y across the court (-4.5 .. 4.5; the 1st referee's stand is at y < 0, the scorer's table at y > 0),
 * z up. Players and the ball are drawn as billboards; flat parts (floor, lines) as projected polygons.
 */
(function () {
    'use strict';

    const V = {
        sub: (a, b) => [a[0] - b[0], a[1] - b[1], a[2] - b[2]],
        dot: (a, b) => a[0] * b[0] + a[1] * b[1] + a[2] * b[2],
        cross: (a, b) => [a[1] * b[2] - a[2] * b[1], a[2] * b[0] - a[0] * b[2], a[0] * b[1] - a[1] * b[0]],
        norm: a => {
            const l = Math.hypot(a[0], a[1], a[2]) || 1;
            return [a[0] / l, a[1] / l, a[2] / l];
        },
        lerp: (a, b, t) => [a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t, a[2] + (b[2] - a[2]) * t],
    };

    // ---------- Camera ----------
    const NEAR = 0.25;

    function Camera(pos, target, fovDeg, upHint) {
        this.set(pos, target, fovDeg, upHint);
    }

    Camera.prototype.set = function (pos, target, fovDeg, upHint) {
        this.pos = pos;
        this.target = target;
        this.fov = fovDeg;
        this.fwd = V.norm(V.sub(target, pos));
        let r = V.cross(this.fwd, upHint || [0, 0, 1]);
        if (Math.hypot(r[0], r[1], r[2]) < 1e-6) r = [1, 0, 0];
        this.right = V.norm(r);
        this.up = V.cross(this.right, this.fwd);
    };
    Camera.prototype.resize = function (w, h) {
        this.w = w;
        this.h = h;
        this.f = (h / 2) / Math.tan((this.fov * Math.PI / 180) / 2);
    };
    /** Camera-space coordinates of a world point. */
    Camera.prototype.toCam = function (p) {
        const d = V.sub(p, this.pos);
        return [V.dot(d, this.right), V.dot(d, this.up), V.dot(d, this.fwd)];
    };
    Camera.prototype.camToScreen = function (c) {
        const s = this.f / c[2];
        return {x: this.w / 2 + c[0] * s, y: this.h / 2 - c[1] * s, z: c[2], s};
    };
    /** Screen position of a world point, or null when it is behind the camera. */
    Camera.prototype.project = function (p) {
        const c = this.toCam(p);
        if (c[2] < NEAR) return null;
        return this.camToScreen(c);
    };
    /** Projects a polygon, clipping it against the near plane. */
    Camera.prototype.projectPoly = function (pts) {
        const cam = pts.map(p => this.toCam(p));
        const out = [];
        for (let i = 0; i < cam.length; i++) {
            const a = cam[i], b = cam[(i + 1) % cam.length];
            const ain = a[2] >= NEAR, bin = b[2] >= NEAR;
            if (ain) out.push(a);
            if (ain !== bin) {
                const t = (NEAR - a[2]) / (b[2] - a[2]);
                out.push([a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t, NEAR]);
            }
        }
        if (out.length < 3) return null;
        return out.map(c => this.camToScreen(c));
    };

    // ---------- Camera presets ----------
    const VIEWS = {
        // 1st referee: on the stand at the net post, opposite the scorer's table
        first: {label: 'Dal seggiolone', pos: [0, -8.4, 4.3], target: [0, 1.6, -0.9], fov: 60},
        // 2nd referee: on the floor by the other post, scorer's side (shifts towards the receiving team)
        second: {label: 'Sotto la rete', pos: [0, 6.6, 1.75], target: [0, -1.5, 0.2], fov: 70},
        top: {label: 'Dall’alto', pos: [0, -0.001, 24], target: [0, 0, 0], fov: 50, up: [0, 1, 0]},
    };

    // ---------- Colours ----------
    const C = {
        wall1: '#1b2740', wall2: '#0f1728',
        floor: '#2c6d8f', court: '#e0843f', line: '#f7f7f2',
        netMesh: 'rgba(20,24,30,0.55)', netBand: '#f5f5f5', post: '#c8ccd4',
        antennaA: '#e53935', antennaB: '#ffffff',
        table: '#3a4a66', bench: '#58657d', stand: '#7d8597',
        skin: ['#f1c7a3', '#d9a27a', '#a8714d', '#7b4a2e'],
        shadow: 'rgba(0,0,0,0.28)',
    };

    // ---------- Scene drawing ----------
    function Renderer(canvas) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.cam = new Camera([0, -8, 4], [0, 0, 0], 60);
        this.view = 'first';
        this.refX = 0;  // the referee's own position along the court (x, metres): 1st leans on the stand, 2nd walks along the side line
        this.look = 0; // where the referee is looking along the court (x, metres): the view turns towards the play
        this.quality = 'high'; // level of detail: 'high' (whole hall), 'medium' (empty stands), 'low' (court and people only)
        this.resize();
    }

    /** Level of detail of the drawing; the lower ones also draw fewer pixels on high-density screens. */
    const MAX_DPR = {high: 2, medium: 1.5, low: 1};
    Renderer.prototype.setQuality = function (q) {
        if (!MAX_DPR[q] || q === this.quality) return;
        this.quality = q;
        this.resize();
    };

    Renderer.prototype.resize = function () {
        const dpr = Math.min(window.devicePixelRatio || 1, MAX_DPR[this.quality] || 2);
        const r = this.canvas.getBoundingClientRect();
        this.cssW = Math.max(1, r.width);
        this.cssH = Math.max(1, r.height);
        this.canvas.width = Math.round(this.cssW * dpr);
        this.canvas.height = Math.round(this.cssH * dpr);
        this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        this.applyView();
    };

    Renderer.prototype.setView = function (view) {
        this.view = view;
        this.applyView();
    };

    /** Moves the referee (and so the camera) along the court. */
    Renderer.prototype.setRefX = function (x) {
        this.refX = x;
        if (this.view !== 'top') this.applyView();
    };

    Renderer.prototype.applyView = function () {
        const v = VIEWS[this.view];
        let pos = v.pos.slice(), target = v.target.slice();
        if (this.view === 'second') {
            pos[0] = this.refX;
            target[0] = this.refX * 1.6;
        }
        if (this.view === 'first') pos[0] += this.refX;
        // Portrait screens: a slightly higher eye looking more downwards, so the court fills the tall frame
        if (this.view !== 'top' && aspectOf(this) < 1) {
            const k = 1 - aspectOf(this);
            pos[2] += (this.view === 'first' ? 3.2 : 1.6) * k;
            target[2] -= (this.view === 'first' ? 2.6 : 1.2) * k;
        }
        if (this.view !== 'top') {
            // Turning the head towards the play: the target moves along the court, the eye barely moves
            target[0] += this.look;
            pos[0] += this.look * 0.06;
        }
        // The 2nd referee turning away from the court (towards the scorer, or towards a team): the yaw turns
        if (this.view === 'second' && (this.gazeK || 0) > 0.002 && this.gazeLast) {
            const g = this.gazeLast;
            const gt = g.scorer ? [pos[0], pos[1] + 10, pos[2] - 0.6] : [g.x, pos[1] - 5.5, 0.6];
            const y0 = Math.atan2(target[1] - pos[1], target[0] - pos[0]),
                y1 = Math.atan2(gt[1] - pos[1], gt[0] - pos[0]);
            let d = y1 - y0;
            while (d > Math.PI) d -= 2 * Math.PI;
            while (d < -Math.PI) d += 2 * Math.PI;
            const yaw = y0 + d * this.gazeK;
            const h = Math.hypot(target[0] - pos[0], target[1] - pos[1]);
            const z = target[2] + (gt[2] - target[2]) * this.gazeK;
            target = [pos[0] + Math.cos(yaw) * h, pos[1] + Math.sin(yaw) * h, z];
        }
        // Narrow screens (portrait phones): widen the field of view so both half-courts stay visible
        const aspect = this.cssW / this.cssH;
        let fov = v.fov;
        if (this.view !== 'top' && aspect < 1.3) fov = Math.min(100, v.fov * (1.3 / Math.max(aspect, 0.5)) * 0.82);
        if (this.view === 'top' && aspect < 1.4) fov = Math.min(105, 2 * Math.atan(Math.tan(v.fov * Math.PI / 360) * 1.4 / Math.max(aspect, 0.45)) * 180 / Math.PI);
        this.cam.set(pos, target, fov, v.up);
        this.cam.resize(this.cssW, this.cssH);
    };

    /**
     * Where the 2nd referee is facing when not following the play: { scorer: true } (back to the net post,
     * facing the scorer) or { x } (towards a team's half). null goes back to the court. Smooth turn.
     */
    Renderer.prototype.setGaze = function (g, dt) {
        if (g) this.gazeLast = g;
        const k = 1 - Math.exp(-(dt || 0.016) * 2.6);
        this.gazeK = (this.gazeK || 0) + ((g ? 1 : 0) - (this.gazeK || 0)) * k;
        if (this.view === 'second') this.applyView();
    };

    /** Smoothly turns the view towards x (metres along the court). */
    Renderer.prototype.lookAt = function (x, dt) {
        const k = 1 - Math.exp(-(dt || 0.016) * 3.2);
        if (!Number.isFinite(this.look)) this.look = 0;
        if (Number.isFinite(x)) this.look += (x - this.look) * k;
        if (this.view !== 'top') this.applyView();
    };

    Renderer.prototype.poly = function (pts, fill, stroke, lw) {
        const p = this.cam.projectPoly(pts);
        if (!p) return;
        const ctx = this.ctx;
        ctx.beginPath();
        ctx.moveTo(p[0].x, p[0].y);
        for (let i = 1; i < p.length; i++) ctx.lineTo(p[i].x, p[i].y);
        ctx.closePath();
        if (fill) {
            ctx.fillStyle = fill;
            ctx.fill();
        }
        if (stroke) {
            ctx.strokeStyle = stroke;
            ctx.lineWidth = lw || 1;
            ctx.stroke();
        }
    };

    Renderer.prototype.rect = function (x0, y0, x1, y1, z, fill) {
        this.poly([[x0, y0, z], [x1, y0, z], [x1, y1, z], [x0, y1, z]], fill);
    };

    /** A painted line on the floor, `w` metres wide. */
    Renderer.prototype.floorLine = function (x0, y0, x1, y1, w, color) {
        const dx = x1 - x0, dy = y1 - y0, l = Math.hypot(dx, dy) || 1;
        const nx = -dy / l * w / 2, ny = dx / l * w / 2;
        this.poly([[x0 + nx, y0 + ny, 0.002], [x1 + nx, y1 + ny, 0.002], [x1 - nx, y1 - ny, 0.002], [x0 - nx, y0 - ny, 0.002]], color || C.line);
    };

    /** Box (tables, stands, benches): drawn face by face, far faces first. */
    Renderer.prototype.box = function (x0, y0, z0, x1, y1, z1, color, shade) {
        const faces = [
            {pts: [[x0, y0, z1], [x1, y0, z1], [x1, y1, z1], [x0, y1, z1]], n: [0, 0, 1], k: 1},
            {pts: [[x0, y0, z0], [x1, y0, z0], [x1, y0, z1], [x0, y0, z1]], n: [0, -1, 0], k: 0.8},
            {pts: [[x0, y1, z0], [x1, y1, z0], [x1, y1, z1], [x0, y1, z1]], n: [0, 1, 0], k: 0.8},
            {pts: [[x0, y0, z0], [x0, y1, z0], [x0, y1, z1], [x0, y0, z1]], n: [-1, 0, 0], k: 0.65},
            {pts: [[x1, y0, z0], [x1, y1, z0], [x1, y1, z1], [x1, y0, z1]], n: [1, 0, 0], k: 0.65},
        ];
        const c = [(x0 + x1) / 2, (y0 + y1) / 2, (z0 + z1) / 2];
        faces.forEach(f => {
            const fc = f.pts.reduce((a, p) => [a[0] + p[0] / 4, a[1] + p[1] / 4, a[2] + p[2] / 4], [0, 0, 0]);
            const toCam = V.sub(this.cam.pos, fc);
            if (V.dot(toCam, f.n) <= 0) return; // back face
            this.poly(f.pts, shadeColor(color, f.k * (shade || 1)));
        });
        return c;
    };

    function aspectOf(r) {
        return r.cssW / Math.max(1, r.cssH);
    }

    function shadeColor(hex, k) {
        const n = parseInt(hex.slice(1), 16);
        const r = Math.min(255, Math.round(((n >> 16) & 255) * k));
        const g = Math.min(255, Math.round(((n >> 8) & 255) * k));
        const b = Math.min(255, Math.round((n & 255) * k));
        return 'rgb(' + r + ',' + g + ',' + b + ')';
    }

    /** Lighter (amt > 0) or darker (amt < 0) version of a #rrggbb colour. */
    function tone(hex, amt) {
        if (!hex || hex[0] !== '#' || hex.length !== 7) return hex;
        const n = parseInt(hex.slice(1), 16);
        const f = c => Math.round(amt >= 0 ? c + (255 - c) * amt : c * (1 + amt));
        return 'rgb(' + f((n >> 16) & 255) + ',' + f((n >> 8) & 255) + ',' + f(n & 255) + ')';
    }

    /**
     * Screen-space layers that only depend on the canvas size and the view (background, vignette): painted once
     * on an offscreen canvas and then copied every frame, since full-screen gradients are slow to fill (mostly on
     * phones) and a slow frame would slow down the whole game.
     */
    Renderer.prototype.cachedLayer = function (name, paint) {
        const W = this.canvas.width, H = this.canvas.height,
            key = W + 'x' + H + ':' + this.cssW + 'x' + this.cssH + ':' + this.view + ':' + this.quality;
        const layers = this.layers || (this.layers = {});
        let L = layers[name];
        if (!L || L.key !== key) {
            const c = L ? L.canvas : document.createElement('canvas');
            c.width = W;
            c.height = H;
            const lctx = c.getContext('2d');
            lctx.setTransform(W / this.cssW, 0, 0, H / this.cssH, 0, 0);
            paint(lctx, this.cssW, this.cssH);
            L = layers[name] = {canvas: c, key};
        }
        this.ctx.drawImage(L.canvas, 0, 0, this.cssW, this.cssH);
    };

    Renderer.prototype.drawBackground = function () {
        this.cachedLayer('background', (ctx, W, H) => {
            const g = ctx.createLinearGradient(0, 0, 0, H);
            g.addColorStop(0, '#0b1222');
            g.addColorStop(0.45, C.wall1);
            g.addColorStop(1, C.wall2);
            ctx.fillStyle = g;
            ctx.fillRect(0, 0, W, H);
            // Hall lights: soft glows high up (not in the top view, nor in the simple graphics)
            if (this.view !== 'top' && this.quality !== 'low') {
                [0.18, 0.5, 0.82].forEach(fx => {
                    const lg = ctx.createRadialGradient(W * fx, -H * 0.05, 0, W * fx, -H * 0.05, H * 0.55);
                    lg.addColorStop(0, 'rgba(255,244,214,0.22)');
                    lg.addColorStop(0.4, 'rgba(255,244,214,0.06)');
                    lg.addColorStop(1, 'rgba(255,244,214,0)');
                    ctx.fillStyle = lg;
                    ctx.fillRect(0, 0, W, H);
                });
            }
        });
    };

    /**
     * Stands with spectators on both long sides of the hall, and advertising boards around the free zone.
     * Everything is generic (no real brands); the crowd is drawn as small figures with a fixed pattern.
     */
    Renderer.prototype.drawHall = function () {
        const ctx = this.ctx;
        const seat = ['#26324a', '#2c3a55'];
        const shirts = ['#2f6fd0', '#e2552b', '#f2c14e', '#ffffff', '#2e9e5b', '#8e44ad', '#1b2433', '#d64a7a', '#5fb3d9'];
        [1, -1].forEach(side => {
            const y0 = side * 10.4, tiers = 7;
            // Back wall of the stand
            this.poly([[-17, side * (y0 * side + tiers * 0.9), 0], [17, side * (y0 * side + tiers * 0.9), 0], [17, side * (y0 * side + tiers * 0.9), 6.5], [-17, side * (y0 * side + tiers * 0.9), 6.5]], '#141c2e');
            for (let i = tiers - 1; i >= 0; i--) {
                const ya = y0 + side * i * 0.9, yb = ya + side * 0.9, z = 0.5 + i * 0.48;
                // Riser and tread of the tier
                this.poly([[-16, ya, z - 0.48], [16, ya, z - 0.48], [16, ya, z], [-16, ya, z]], shadeColor(seat[i % 2], 0.8));
                this.poly([[-16, ya, z], [16, ya, z], [16, yb, z], [-16, yb, z]], seat[i % 2]);
                // Spectators (not every seat taken), one path per colour for the whole tier (hundreds of small shapes);
                // the stands stay empty below the detailed graphics
                if (this.quality !== 'high') continue;
                const body = shirts.map(() => new Path2D()), heads = C.skin.map(() => new Path2D());
                for (let j = 0; j < 46; j++) {
                    const hsh = (j * 73 + i * 151 + (side > 0 ? 7 : 31)) % 997;
                    if (hsh % 5 === 0) continue;
                    const x = -15.4 + j * 0.68 + ((hsh % 7) - 3) * 0.03;
                    const p = this.cam.project([x, ya + side * 0.45, z + 0.62]);
                    if (!p || p.s < 1.2) continue;
                    const r = Math.max(0.6, 0.11 * p.s);
                    body[hsh % shirts.length].rect(p.x - r * 1.5, p.y + r * 0.6, r * 3, r * 3.2);
                    const hd = heads[hsh % 4];
                    hd.moveTo(p.x + r, p.y);
                    hd.arc(p.x, p.y, r, 0, Math.PI * 2);
                }
                body.forEach((path, k) => {
                    ctx.fillStyle = shirts[k];
                    ctx.fill(path);
                });
                heads.forEach((path, k) => {
                    ctx.fillStyle = C.skin[k];
                    ctx.fill(path);
                });
            }
        });
    };

    /** Advertising boards along the long sides, beyond the benches / the referee's stand (drawn after the floor). */
    Renderer.prototype.drawBoards = function () {
        [1, -1].forEach(side => {
            const yb = side * 9.4;
            const cols = ['#1b5fa8', '#e0843f', '#2e9e5b', '#1b2433', '#f2c14e', '#c0392b'];
            for (let k = 0; k < 8; k++) {
                const x0 = -12 + k * 3, x1 = x0 + 2.95, col = cols[(k + (side > 0 ? 0 : 3)) % cols.length];
                this.poly([[x0, yb, 0], [x1, yb, 0], [x1, yb, 0.9], [x0, yb, 0.9]], col);
                this.poly([[x0, yb, 0.62], [x1, yb, 0.62], [x1, yb, 0.7], [x0, yb, 0.7]], 'rgba(255,255,255,0.35)');
                if (k % 2 === 0) this.boardText(x0, x1, yb, 'FISCHIETTO', col === '#f2c14e' ? '#1b2433' : '#ffffff');
            }
        });
    };

    /** Text painted on a board (vertical, facing the court): mapped with the board's projected corners. */
    Renderer.prototype.boardText = function (x0, x1, y, text, color) {
        let a = this.cam.project([x0, y, 0.55]), b = this.cam.project([x1, y, 0.55]),
            c = this.cam.project([x0, y, 0.15]);
        if (!a || !b || !c) return;
        // Seen from the other side of the hall: the text still reads left to right
        if (b.x < a.x) {
            const t = a;
            a = b;
            b = t;
            c = this.cam.project([x1, y, 0.15]);
            if (!c) return;
        }
        const w = Math.hypot(b.x - a.x, b.y - a.y);
        if (w < 30) return;
        const ctx = this.ctx;
        ctx.save();
        // Affine map: unit square of the board -> its projected corners
        ctx.transform((b.x - a.x) / 100, (b.y - a.y) / 100, (c.x - a.x) / 40, (c.y - a.y) / 40, a.x, a.y);
        ctx.fillStyle = color;
        ctx.font = '800 26px "Inter", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(text, 50, 20, 92);
        ctx.restore();
    };

    /** Light on the court (brighter in the middle) and a soft vignette on the whole picture. */
    Renderer.prototype.courtLight = function () {
        const pts = this.cam.projectPoly([[-9, -4.5, 0], [9, -4.5, 0], [9, 4.5, 0], [-9, 4.5, 0]]);
        const c = this.cam.project([0, 0, 0]);
        if (!pts || !c) return;
        const ctx = this.ctx;
        // The court itself filled with the gradient (cheaper than clipping a full-screen fill)
        const R = Math.max(this.cssW, this.cssH) * 0.75;
        const g = ctx.createRadialGradient(c.x, c.y, 0, c.x, c.y, R);
        g.addColorStop(0, 'rgba(255,236,200,0.16)');
        g.addColorStop(0.6, 'rgba(255,236,200,0.03)');
        g.addColorStop(1, 'rgba(60,20,0,0.18)');
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.moveTo(pts[0].x, pts[0].y);
        pts.slice(1).forEach(q => ctx.lineTo(q.x, q.y));
        ctx.closePath();
        ctx.fill();
    };
    Renderer.prototype.vignette = function () {
        this.cachedLayer('vignette', (ctx, W, H) => {
            const g = ctx.createRadialGradient(W / 2, H * 0.55, Math.min(W, H) * 0.35, W / 2, H * 0.55, Math.max(W, H) * 0.8);
            g.addColorStop(0, 'rgba(0,0,0,0)');
            g.addColorStop(1, 'rgba(0,0,0,0.32)');
            ctx.fillStyle = g;
            ctx.fillRect(0, 0, W, H);
        });
    };

    Renderer.prototype.drawFloor = function () {
        // Hall floor, free zone and court
        this.rect(-17, -10.4, 17, 10.4, 0, '#1a4257');
        this.rect(-13, -7.5, 13, 9.5, 0, C.floor);
        this.rect(-9, -4.5, 9, 4.5, 0, C.court);
        if (this.quality === 'high') this.courtLight();
        const w = 0.05;
        // Perimeter lines (inside the court), centre line, attack lines and their dashed extensions
        this.floorLine(-9, -4.5 + w / 2, 9, -4.5 + w / 2, w);
        this.floorLine(-9, 4.5 - w / 2, 9, 4.5 - w / 2, w);
        this.floorLine(-9 + w / 2, -4.5, -9 + w / 2, 4.5, w);
        this.floorLine(9 - w / 2, -4.5, 9 - w / 2, 4.5, w);
        this.floorLine(0, -4.5, 0, 4.5, w);
        [-3, 3].forEach(x => {
            this.floorLine(x, -4.5, x, 4.5, w);
            for (let i = 0; i < 5; i++) {
                this.floorLine(x, -4.5 - 0.2 - i * 0.35, x, -4.5 - 0.35 - i * 0.35, w);
                this.floorLine(x, 4.5 + 0.2 + i * 0.35, x, 4.5 + 0.35 + i * 0.35, w);
            }
        });
        // Service zone marks behind the end lines
        [-9, 9].forEach(x => {
            const d = x < 0 ? -1 : 1;
            [-4.5, 4.5].forEach(y => this.floorLine(x + d * 0.2, y, x + d * 0.35, y, w));
        });
    };

    /** Pushes everything that needs depth sorting (net, furniture, people, ball) and draws it. */
    Renderer.prototype.render = function (scene) {
        const ctx = this.ctx;
        this.hits = [];   // screen boxes of the people drawn, in drawing order (for picking someone on the court)
        ctx.save();
        this.drawBackground();
        const hall = this.view !== 'top' && this.quality !== 'low';
        if (hall) this.drawHall();
        this.drawFloor();
        if (hall) this.drawBoards();

        this.ballNow = scene.ball || null; // players' hands reach for it (detailed graphics)
        const items = [];
        const push = (p, fn) => {
            const c = this.cam.toCam(p);
            if (c[2] > NEAR) items.push({z: c[2], fn});
        };

        // Scorer's table and team benches (scorer's side, y > 0); 1st referee's stand (y < 0)
        push([0, 7.0, 0.4], () => this.box(-1.2, 6.6, 0, 1.2, 7.3, 0.75, C.table));
        push([-4.5, 7.6, 0.2], () => this.box(-6.5, 7.4, 0, -2.5, 7.9, 0.45, C.bench));
        push([4.5, 7.6, 0.2], () => this.box(2.5, 7.4, 0, 6.5, 7.9, 0.45, C.bench));
        if (this.view !== 'first') push([0, -5.6, 0.8], () => this.box(-0.45, -5.95, 0, 0.45, -5.25, 1.55, C.stand));

        // Net in slices, so players on either side are sorted correctly against it
        const netH = scene.netHeight || 2.24, slices = 10;
        for (let i = 0; i < slices; i++) {
            const y0 = -5 + i, y1 = y0 + 1;
            push([0, y0 + 0.5, netH - 0.5], () => this.drawNetSlice(y0, y1, netH, scene.netShake));
        }
        [-5, 5].forEach(y => push([0, y, 1.2], () => this.drawPost(y, netH)));
        [-4.5, 4.5].forEach(y => push([0, y, netH + 0.4], () => this.drawAntenna(y, netH, scene.netShake)));

        (scene.people || []).forEach(p => push([p.x, p.y, 1.0 + (p.z || 0)], () => this.drawPerson(p)));
        if (scene.ball) {
            const b = scene.ball;
            push([b.x, b.y, 0.001], () => this.drawBallShadow(b));
            push([b.x, b.y, b.z], () => this.drawBall(b));
        }
        (scene.markers || []).forEach(m => push([m.x, m.y, 0.01], () => this.drawMarker(m)));

        items.sort((a, b) => b.z - a.z);
        items.forEach(it => it.fn());
        if (hall) this.vignette();
        ctx.restore();
    };

    Renderer.prototype.drawNetSlice = function (y0, y1, h, shake) {
        const top = h, bottom = h - 1.0;
        // A touched net wobbles near the contact point across the whole height (top band, mesh and bottom band)
        const off = (y, zFrac) => {
            if (!shake) return 0;
            const dist = Math.abs(y - shake.y);
            const falloff = Math.max(0.2, 1 - dist / 4.8);
            const zScale = 0.58 + 0.42 * (zFrac !== undefined ? zFrac : 1);
            return shake.amp * falloff * zScale * Math.sin(shake.phase + 0.35);
        };

        const b0 = off(y0, 0), b1 = off(y1, 0);
        const t0 = off(y0, 1), t1 = off(y1, 1);

        this.poly([[b0, y0, bottom], [b1, y1, bottom], [t1, y1, top], [t0, y0, top]], C.netMesh);
        // Mesh grid lines: both horizontal and vertical lines follow the wave
        const ctx = this.ctx;
        ctx.strokeStyle = shake ? 'rgba(255,255,255,0.22)' : 'rgba(255,255,255,0.12)';
        ctx.lineWidth = 1;
        for (let k = 1; k < 4; k++) {
            const u = k / 4;
            const mk0 = off(y0, u), mk1 = off(y1, u);
            const a = this.cam.project([mk0, y0, bottom + k * 0.25]),
                b = this.cam.project([mk1, y1, bottom + k * 0.25]);
            if (a && b) {
                ctx.beginPath();
                ctx.moveTo(a.x, a.y);
                ctx.lineTo(b.x, b.y);
                ctx.stroke();
            }
        }
        for (let v = 0; v <= 4; v++) {
            const yv = y0 + (y1 - y0) * (v / 4);
            const bv = off(yv, 0), tv = off(yv, 1);
            const a = this.cam.project([bv, yv, bottom]), b = this.cam.project([tv, yv, top]);
            if (a && b) {
                ctx.beginPath();
                ctx.moveTo(a.x, a.y);
                ctx.lineTo(b.x, b.y);
                ctx.stroke();
            }
        }

        const topBandColor = shake ? '#fff9c4' : C.netBand;
        this.poly([[t0, y0, top - 0.07], [t1, y1, top - 0.07], [t1, y1, top], [t0, y0, top]], topBandColor, shake ? 'rgba(255,213,79,0.75)' : null, 1.5);
        this.poly([[b0, y0, bottom], [b1, y1, bottom], [b1, y1, bottom + 0.05], [b0, y0, bottom + 0.05]], '#dcdcdc');
    };

    Renderer.prototype.drawPost = function (y, h) {
        const a = this.cam.project([0, y, 0]), b = this.cam.project([0, y, h + 0.15]);
        if (!a || !b) return;
        const ctx = this.ctx;
        ctx.strokeStyle = C.post;
        ctx.lineWidth = Math.max(2, 0.1 * a.s);
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.moveTo(a.x, a.y);
        ctx.lineTo(b.x, b.y);
        ctx.stroke();
    };

    Renderer.prototype.drawAntenna = function (y, h, shake) {
        const ctx = this.ctx;
        const segs = 8, z0 = h - 1.0, z1 = h + 0.8;
        // A hit on the net (or on the antenna itself) makes the antenna sway too, more at the top
        const dist = shake ? Math.abs(y - shake.y) : 0;
        const sway = shake ? shake.amp * 2.5 * Math.max(0.35, 1 - dist / 5.5) * Math.sin(shake.phase + 0.35) : 0;
        const xAt = k => sway * Math.max(0, k - 0.15) * 1.5;
        for (let i = 0; i < segs; i++) {
            const a = this.cam.project([xAt(i / segs), y, z0 + (z1 - z0) * i / segs]),
                b = this.cam.project([xAt((i + 1) / segs), y, z0 + (z1 - z0) * (i + 1) / segs]);
            if (!a || !b) continue;
            ctx.strokeStyle = i % 2 ? C.antennaB : C.antennaA;
            ctx.lineWidth = Math.max(1.5, 0.025 * a.s);
            ctx.beginPath();
            ctx.moveTo(a.x, a.y);
            ctx.lineTo(b.x, b.y);
            ctx.stroke();
        }
    };

    Renderer.prototype.drawMarker = function (m) {
        const p = this.cam.project([m.x, m.y, 0.01]);
        if (!p) return;
        const ctx = this.ctx;
        ctx.save();
        const r = (m.r || 0.35) * p.s;
        ctx.beginPath();
        ctx.ellipse(p.x, p.y, Math.max(3, r), Math.max(2, r * this.flatten(m)), 0, 0, Math.PI * 2);
        if (m.fill) {
            ctx.fillStyle = m.fill;
            ctx.fill();
        }
        ctx.strokeStyle = m.color || '#ffd54f';
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.restore();
    };

    /** How much a horizontal circle is squashed on screen at that point (1 = seen from above). */
    Renderer.prototype.flatten = function (p) {
        const c = this.cam.toCam([p.x, p.y, 0]);
        const toCam = V.norm(V.sub(this.cam.pos, [p.x, p.y, 0]));
        return Math.max(0.15, Math.min(1, Math.abs(toCam[2]) + 0.05 * (c[2] < 0 ? 0 : 0)));
    };

    // ---------- Ball ----------
    Renderer.prototype.drawBallShadow = function (b) {
        const p = this.cam.project([b.x, b.y, 0.002]);
        if (!p) return;
        const ctx = this.ctx;
        const r = 0.13 * p.s * Math.max(0.4, 1 - b.z / 8);
        ctx.fillStyle = 'rgba(0,0,0,' + (0.32 * Math.max(0.25, 1 - b.z / 6)) + ')';
        ctx.beginPath();
        ctx.ellipse(p.x, p.y, r, r * this.flatten(b), 0, 0, Math.PI * 2);
        ctx.fill();
    };

    /** Volleyball in the style of the official match balls: yellow and blue curved panels, shading, highlight. */
    Renderer.prototype.drawBall = function (b) {
        const p = this.cam.project([b.x, b.y, b.z]);
        if (!p) return;
        const ctx = this.ctx;
        const r = Math.max(2.5, 0.105 * p.s * 1.15);
        const x = p.x, y = p.y;
        ctx.save();
        ctx.beginPath();
        ctx.arc(x, y, r, 0, Math.PI * 2);
        ctx.fillStyle = '#f7d21a';
        ctx.fill();
        ctx.clip();
        if (r > 3.5) {
            // Curved panels turning with the spin: blue bands and thin seams, like the 8-panel match balls
            const a = (b.spin || 0) * 0.6;
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(a);
            const band = (k, color, wid) => {
                const off = k * r * 0.62;
                ctx.strokeStyle = color;
                ctx.lineWidth = wid;
                ctx.lineCap = 'butt';
                ctx.beginPath();
                ctx.moveTo(-r * 1.2, off - r * 0.35);
                ctx.bezierCurveTo(-r * 0.4, off + r * 0.55, r * 0.4, off - r * 0.55, r * 1.2, off + r * 0.35);
                ctx.stroke();
            };
            band(-1, '#1d4fa8', r * 0.42);
            band(1, '#1d4fa8', r * 0.42);
            if (r > 6) {
                band(-1.42, 'rgba(255,255,255,.55)', Math.max(0.6, r * 0.05));
                band(-0.58, 'rgba(255,255,255,.55)', Math.max(0.6, r * 0.05));
                band(0.58, 'rgba(255,255,255,.55)', Math.max(0.6, r * 0.05));
                band(1.42, 'rgba(255,255,255,.55)', Math.max(0.6, r * 0.05));
                band(0, 'rgba(120,90,0,.35)', Math.max(0.5, r * 0.04));
            }
            ctx.restore();
        }
        // Shading (light from the top left) and a highlight
        const g = ctx.createRadialGradient(x - r * 0.35, y - r * 0.4, r * 0.1, x, y, r * 1.05);
        g.addColorStop(0, 'rgba(255,255,255,.45)');
        g.addColorStop(0.45, 'rgba(255,255,255,0)');
        g.addColorStop(1, 'rgba(0,0,0,.38)');
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(x, y, r, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
        ctx.strokeStyle = 'rgba(0,0,0,.35)';
        ctx.lineWidth = Math.max(0.6, r * 0.06);
        ctx.beginPath();
        ctx.arc(x, y, r, 0, Math.PI * 2);
        ctx.stroke();
        if (b.highlight) {
            ctx.strokeStyle = b.highlight;
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(x, y, r + 4, 0, Math.PI * 2);
            ctx.stroke();
        }
    };


    // ---------- People ----------
    const clamp01 = u => Math.max(0, Math.min(1, u));
    const ease = u => {
        u = clamp01(u);
        return u * u * (3 - 2 * u);
    };
    const mix = (a, b, k) => [a[0] + (b[0] - a[0]) * k, a[1] + (b[1] - a[1]) * k];
    const pulse = (dt, wid) => Math.exp(-(dt / wid) * (dt / wid));

    /**
     * Steps of a person (detailed graphics), from how they move between frames: smoothed speed (m/s), stride phase
     * (radians, one stride every ~1.5 m) and direction. A jump (replay scrubbing, a new rally) starts again from rest.
     */
    Renderer.prototype.gaitOf = function (p) {
        const key = p.id || p.label;
        if (!key) return null;
        const now = performance.now() / 1000, G = this.gaits || (this.gaits = new Map());
        let g = G.get(key);
        if (!g || now - g.t > 1 || Math.hypot(p.x - g.x, p.y - g.y) > 2) {
            g = {x: p.x, y: p.y, t: now, v: 0, ph: 0, ux: null, uy: null};
            G.set(key, g);
            return g;
        }
        const dt = now - g.t;
        if (dt < 0.008) return g;
        const dx = p.x - g.x, dy = p.y - g.y, d = Math.hypot(dx, dy), k = 1 - Math.exp(-dt * 9);
        g.v += (Math.min(8, d / dt) - g.v) * k;
        g.ph += d * 4.2;
        if (d > 1e-4) {
            g.ux = g.ux == null ? dx / d : g.ux + (dx / d - g.ux) * k;
            g.uy = g.uy == null ? dy / d : g.uy + (dy / d - g.uy) * k;
        }
        g.x = p.x;
        g.y = p.y;
        g.t = now;
        return g;
    };

    /**
     * Body of a player for the detailed graphics, from the pose and from what they are doing (p.act: contacts close
     * in time, the jump around now, the held ball): crouch (0..0.25, knees bent), tuck (legs folded in the air),
     * feet spread, how much they are running, and the phase of the action used by the arms.
     */
    Renderer.prototype.poseRig = function (p, g) {
        const a = p.act || {}, pose = p.pose || 'stand', j = a.jump;
        const run = g ? clamp01((g.v - 0.25) / 3) : 0;
        const bob = 0.025 * run * Math.abs(Math.cos(g ? g.ph : 0));
        const r = {
            g,
            kind: pose,
            crouch: (pose === 'ready' || pose === 'pass' || pose === 'set' ? 0.12 * (1 - run * 0.5) : 0) + bob,
            tuck: 0,
            run,
            ph: g ? g.ph : 0,
            spread: null
        };
        if (p.outfit && p.outfit !== 'player') {
            r.kind = 'other';
            return r;
        }
        const touch = list => (a.touches || []).filter(q => list.includes(q.what)).sort((x, y) => Math.abs(x.dt) - Math.abs(y.dt))[0];
        // Landing: the knees take the weight, then straighten
        const land = (u, k) => k * Math.min(1, u * 5) * (1 - clamp01(u));
        if (j && (j.kind === 'attack' || j.kind === 'serve')) {
            const s = j.dt, rise = j.rise, fall = j.fall, sc = j.kind === 'serve' ? 0.6 : 1;
            Object.assign(r, {kind: j.kind === 'serve' ? 'serveJ' : 'attackJ', s, rise, fall});
            if (s < -rise) r.crouch = Math.max(r.crouch, (0.04 + 0.18 * ease((s + rise + 0.35) / 0.35)) * sc); // last steps: lower for the take-off
            else if (s < -rise * 0.55) {
                r.crouch = 0.22 * sc * (1 - ease((s + rise) / (rise * 0.45)));
                r.run = 0;
            } // take-off: legs push
            else if (s < fall) {
                r.crouch = 0;
                r.run = 0;
                r.tuck = 0.45 * sc * (s < 0 ? ease((s + rise * 0.55) / (rise * 0.55)) : 1 - ease(s / fall));
            } else {
                r.crouch = land((s - fall) / 0.45, 0.2 * sc);
                r.run = 0;
            }
            if (s > -rise && s < fall) r.spread = 0.16;
            return r;
        }
        if (j && j.kind === 'block') {
            const s = j.dt, rise = j.rise, fall = j.fall;
            Object.assign(r, {kind: 'blockJ', s, rise, fall, c: touch(['block'])});
            if (s < -rise - 0.2) r.crouch = 0.08 + bob;
            else if (s < -rise) r.crouch = 0.08 + 0.14 * ease((s + rise + 0.2) / 0.2); // squat before the jump
            else if (s < -rise * 0.5) {
                r.crouch = 0.22 * (1 - ease((s + rise) / (rise * 0.5)));
                r.run = 0;
            } else if (s < fall) {
                r.crouch = 0;
                r.tuck = 0.12;
                r.run = 0;
            } else {
                r.crouch = land((s - fall) / 0.45, 0.16);
                r.run = 0;
            }
            r.spread = s > -rise && s < fall ? 0.14 : 0.34;
            return r;
        }
        // Ready just before or after a touch: the hands already go to (or come back from) the set or the pass
        const SETS = ['set', 'libero-set', 'second-touch', 'fourth'],
            PASSES = ['reception', 'dig', 'cover', 'third', 'double-first'];
        if (pose === 'ready') {
            const cs = touch(SETS), cp = touch(PASSES);
            if ((cs && cs.dt > -0.7 && cs.dt < 0.8) || a.hold != null || a.released != null) r.kind = 'set';
            else if (cp && cp.dt > -0.6 && cp.dt < 0.55) r.kind = 'pass';
        }
        if (r.kind === 'set') {
            r.c = touch(SETS);
            // Legs push with the set: almost straight at the contact
            if (r.c && r.c.dt > -0.6) r.crouch = r.c.dt < 0 ? 0.12 - 0.1 * ease((r.c.dt + 0.3) / 0.3) : 0.02 * (1 - ease(r.c.dt / 0.3));
            if (a.hold != null) r.crouch = 0.02 + 0.1 * Math.sin(Math.PI * a.hold); // the held ball sinks with the body
            else if (a.released != null) r.crouch = 0.02 * (1 - ease(a.released / 0.3));
        } else if (r.kind === 'pass') {
            r.c = touch(PASSES);
            r.crouch = r.c && Math.abs(r.c.dt) < 0.6 ? (r.c.dt < 0 ? 0.2 : 0.2 - 0.08 * ease(r.c.dt / 0.3)) : 0.12;
        }
        return r;
    };

    /**
     * Arms of a player for the detailed graphics: hand positions on screen for the action (run-up and spike, serve,
     * block over the net, set and pass with the hands on the ball, held ball, double contact, dribble), as entries
     * [side, x from the middle, y, hand style, elbow, in front of the body]; null keeps the plain pose.
     */
    Renderer.prototype.rigArms = function (p, r, G) {
        const {cx, top, hpx, w, sh0, hipY, zf} = G;
        const a = p.act || {}, ball = this.ballNow, k = r.kind;
        if (!['stand', 'walk', 'ready', 'set', 'pass', 'attackJ', 'serveJ', 'blockJ'].includes(k) && !(a.dribble && k === 'serve')) return null;
        const pt = (x, y) => [x, y];
        const proj = q => {
            const s = this.cam.project(q);
            return s ? G.toBody([s.x, s.y]) : null;
        };
        const near = ball && Math.hypot(ball.x - p.x, ball.y - p.y) < 1.3;
        const R = this.cam.right;
        const atBall = (lat, dz) => (near ? proj([ball.x + R[0] * lat, ball.y + R[1] * lat, ball.z + dz]) : null);
        const out = [];
        const put = (side, P, hand, front, elb) => out.push([side, P[0] - cx, P[1], hand, elb || null, !!front]);
        // Relaxed arms swinging with the steps (opposite to the leg on the same side), and the ready position
        const relaxed = sd => {
            const sw = Math.sin(r.ph + (sd > 0 ? 0 : Math.PI)) * r.run;
            return pt(cx + sd * w * (0.58 - 0.12 * r.run - Math.max(0, sw) * 0.12), sh0 + hpx * (0.29 - 0.07 * r.run - Math.max(0, sw) * 0.08 - Math.max(0, -sw) * 0.03));
        };
        const readyH = sd => mix(pt(cx + sd * w * 0.4, sh0 + hpx * 0.21), relaxed(sd), r.run);
        // Ready position: elbows bent outwards, forearms forwards (for the plain ready arms)
        const readyE = sd => [cx + sd * w * 0.66, sh0 + hpx * 0.16];
        const netP = proj([0, p.y, zf + 1.6]);
        // Hitting arm: the one towards the net on screen; serving, the one on the side of the tossed ball
        let hs = netP && netP[0] < cx ? -1 : 1;
        // (the ball goes straight up from the tossing hand: its side is kept until the end of the follow-through)
        if (k === 'serveJ' && r.g) {
            if (r.s < 0 && ball && near) {
                const bs = this.cam.project([ball.x, ball.y, ball.z]);
                if (bs && Math.abs(bs.x - cx) > w * 0.08) r.g.serveSide = bs.x < cx ? -1 : 1;
            }
            if (r.g.serveSide) hs = r.g.serveSide;
        }
        const ls = -hs;

        if (k === 'attackJ' || (k === 'serveJ' && r.s >= -0.75)) {
            const s = r.s, rise = r.rise, fall = r.fall, serve = k === 'serveJ';
            const backLow = sd => pt(cx + sd * w * 0.8, hipY + hpx * 0.03);
            const over = sd => pt(cx + sd * w * 0.28, top - hpx * 0.24);
            const cock = pt(cx + hs * w * 0.6, top + hpx * 0.05), cockE = [cx + hs * w * 0.88, sh0 - hpx * 0.1];
            const leadUp = pt(cx + hs * w * 0.1, top - hpx * 0.27);
            const hitP = pt(cx + hs * w * 0.22, top - hpx * 0.3);
            const chest = pt(cx - hs * w * 0.22, sh0 + hpx * 0.1);
            const follow = pt(cx - hs * w * 0.32, hipY + hpx * 0.02);
            const leadDown = pt(cx + ls * w * 0.55, sh0 + hpx * 0.24);
            let H, L, hF = true, lF = true, hE = null, hHand = 'reach', lHand = 'reach', u;
            if (serve && s < -0.45) {
                // Toss: the hand rises with the ball and lets it go, the hitting arm draws back
                u = ease((s + 0.75) / 0.3);
                const bp = atBall(0, -0.13);
                L = bp && u < 0.5 ? mix(bp, leadUp, u * 0.6) : mix(relaxed(ls), leadUp, u);
                H = mix(relaxed(hs), cock, u);
                hE = u > 0.5 ? cockE : null;
                hF = u < 0.5;
                lHand = u < 0.4 ? 'grip' : 'reach';
            } else if (serve && s < -0.07) {
                H = cock;
                hE = cockE;
                hF = false;
                L = leadUp;
            } else if (s < -rise - 0.35) {
                H = relaxed(hs);
                L = relaxed(ls);
                hF = lF = false;
                hHand = lHand = 'relax';
            } else if (s < -rise) {
                // Last steps of the run-up: both arms swing down and back
                u = ease((s + rise + 0.35) / 0.35);
                H = mix(relaxed(hs), backLow(hs), u);
                L = mix(relaxed(ls), backLow(ls), u);
                hF = lF = false;
                hHand = lHand = 'relax';
            } else if (s < -rise * 0.45) {
                // Take-off: both arms swing forwards and up
                u = ease((s + rise) / (rise * 0.55));
                H = mix(backLow(hs), over(hs), u);
                L = mix(backLow(ls), over(ls), u);
                hF = lF = u > 0.35;
            } else if (s < -0.07) {
                // Rising: the hitting arm draws back like a bow (elbow high, hand behind the head), the other points at the ball
                u = ease((s + rise * 0.45) / Math.max(0.05, rise * 0.45 - 0.07));
                H = mix(over(hs), cock, u);
                hE = u > 0.5 ? cockE : null;
                hF = u < 0.6;
                L = mix(over(ls), leadUp, u);
            } else if (s < 0.05) {
                // The hit: the arm whips up to the ball, the other one comes down to the chest
                u = ease((s + 0.07) / 0.12);
                H = mix(cock, hitP, u);
                hE = u < 0.3 ? cockE : null;
                L = mix(leadUp, chest, u);
            } else if (s < fall) {
                // Follow-through across the body
                u = ease((s - 0.05) / Math.max(0.05, fall - 0.05));
                H = mix(hitP, follow, u);
                L = mix(chest, leadDown, u);
            } else {
                u = ease((s - fall) / 0.4);
                H = mix(follow, readyH(hs), u);
                L = mix(leadDown, readyH(ls), u);
            }
            // At the contact the palm is on the ball (it leaves fast: only for an instant)
            const bp = atBall(0, -0.04);
            if (bp && s > -0.12 && s < 0.08) H = mix(H, bp, pulse(s, 0.035));
            put(hs, H, hHand, hF, hE);
            put(ls, L, lHand, lF);
            return out;
        }

        if (k === 'blockJ') {
            const s = r.s, rise = r.rise, fall = r.fall, nx = p.x > 0 ? -1 : 1;
            // Hands up in front of the shoulders, then over the net: reaching into the opponents' space, then pulled back
            const rb = sd => pt(cx + sd * w * 0.52, sh0 - hpx * 0.03);
            const sq = sd => pt(cx + sd * w * 0.48, sh0 + hpx * 0.02);
            const ext = sd => proj([p.x + nx * 0.42 + R[0] * sd * 0.2, p.y + R[1] * sd * 0.2, zf + G.bodyH + 0.3]) || pt(cx + sd * w * 0.3, top - hpx * 0.28);
            const back = sd => pt(cx + sd * w * 0.3, top - hpx * 0.2);
            [-1, 1].forEach(sd => {
                let P;
                if (s < -rise - 0.2) P = rb(sd);
                else if (s < -rise) P = mix(rb(sd), sq(sd), ease((s + rise + 0.2) / 0.2));
                else if (s < -rise * 0.15) P = mix(sq(sd), ext(sd), ease((s + rise) / (rise * 0.85)));
                else if (s < fall * 0.5) P = ext(sd);
                else if (s < fall) P = mix(ext(sd), back(sd), ease((s - fall * 0.5) / (fall * 0.5)));
                else P = mix(back(sd), readyH(sd), ease((s - fall) / 0.4));
                // Touching the ball: the hands close on it
                const bp = r.c && Math.abs(r.c.dt) < 0.2 && atBall(sd * 0.1, 0);
                if (bp) P = mix(P, bp, 0.7 * pulse(r.c.dt, 0.08));
                put(sd, P, 'spread', true);
            });
            return out;
        }

        if (k === 'set' && r.c && r.c.dt > -0.7 && (r.c.dt < 0.8 || a.released != null)) {
            // Set: the hands go above the forehead in a window, fingers spread, meet the ball and push it up
            const ts = (a.touches || []).filter(q => ['set', 'libero-set', 'second-touch', 'fourth'].includes(q.what));
            const dbl = ts.find(q => q.what === 'second-touch'), first = dbl && ts.find(q => q !== dbl);
            // The push follows the last touch (for a held ball: when it is let go)
            const ref = a.released != null ? {dt: a.released} : a.hold != null ? {dt: -0.01} : dbl || r.c;
            const base0 = sd => pt(cx + sd * w * 0.18, top - hpx * 0.16);
            const ext = sd => pt(cx + sd * w * 0.15, top - hpx * 0.32);
            // Wrists under the sides of the ball, so the fingers are on it
            const bl = atBall(-0.1, -0.16), br = atBall(0.1, -0.16);
            const pair = bl && br && bl[0] > br[0] ? [br, bl] : [bl, br];
            [-1, 1].forEach(sd => {
                const d0 = (first || r.c).dt;
                let B;
                if (d0 < -0.35) B = mix(readyH(sd), base0(sd), ease((d0 + 0.7) / 0.35));
                else if (ref.dt < 0) B = base0(sd);
                else B = mix(mix(base0(sd), ext(sd), ease(ref.dt / 0.25)), readyH(sd), ease((ref.dt - 0.32) / 0.35));
                let wgt;
                if (a.hold != null) wgt = 1; // held ball: the hands stay on it, fingers closed around
                else if (a.released != null) wgt = pulse(a.released, 0.1);
                else if (dbl) {
                    // Double contact: two separate touches, the first mostly with one hand, the second with the other;
                    // in between the hands come off the ball
                    // (the ball slips away from the net: the second hand is the one on that side)
                    const p1 = first ? pulse(first.dt, 0.075) : 0, p2 = pulse(dbl.dt, 0.075);
                    wgt = sd === hs ? Math.max(p1, p2 * 0.4) : Math.max(p2, p1 * 0.4);
                    if (first && first.dt > 0 && dbl.dt < 0) B = [B[0] + sd * w * 0.06, B[1] + hpx * 0.05 * Math.sin(Math.PI * first.dt / (first.dt - dbl.dt))];
                } else wgt = pulse(r.c.dt, r.c.dt < 0 ? 0.14 : 0.1);
                const T = pair[sd < 0 ? 0 : 1];
                put(sd, T ? mix(B, T, wgt) : B, a.hold != null || (a.released != null && a.released < 0.06) ? 'grip' : 'set', true);
            });
            return out;
        }

        if (k === 'pass' && r.c && r.c.dt > -0.6 && r.c.dt < 0.55) {
            // Pass: forearms joined in a platform under the ball, legs pushing a little at the contact
            const dt = r.c.dt;
            const plat = pt(cx, hipY + hpx * (dt > 0 ? 0.06 - 0.06 * ease(dt / 0.25) : 0.06));
            const bp = atBall(0, -0.13);
            [-1, 1].forEach(sd => {
                let P = pt(plat[0] + sd * w * 0.035, plat[1]);
                if (dt < -0.35) P = mix(readyH(sd), P, ease((dt + 0.6) / 0.25));
                if (dt > 0.3) P = mix(P, readyH(sd), ease((dt - 0.3) / 0.25));
                if (bp) P = mix(P, [bp[0] + sd * w * 0.035, bp[1]], pulse(dt, 0.13));
                put(sd, P, dt > -0.4 && dt < 0.35 ? 'clasp' : 'reach', true);
            });
            return out;
        }

        if (a.dribble && near && ball.z < zf + 1.3) {
            // Bouncing the ball before the service: the hand on its side pats it down
            const bs = this.cam.project([ball.x, ball.y, ball.z]);
            const sd = bs && bs.x < cx ? -1 : 1;
            const hp = proj([ball.x, ball.y, Math.max(ball.z + 0.1, zf + 0.78)]);
            if (hp) put(sd, hp, 'spread', true);
            put(-sd, relaxed(-sd), 'relax', false);
            return out;
        }
        if (k === 'serve') return null;
        const rd = k === 'ready' || k === 'set' || k === 'pass';
        [-1, 1].forEach(sd => put(sd, rd ? readyH(sd) : relaxed(sd), rd ? 'reach' : 'relax', rd && r.run < 0.5, rd && r.run < 0.3 ? readyE(sd) : null));
        return out;
    };

    /**
     * p: { x, y, z (jump height), h (height), jersey, shorts, number, pose, skin, role, label, bubble, highlight, facing }
     * pose: stand | ready | pass | set | attack | block | serve | sit | gesture-t | gesture-sub | ref | walk
     */
    Renderer.prototype.drawPerson = function (p) {
        if (p.hiddenIn === this.view) return; // the user's own referee is not drawn in their own view
        if (this.view === 'top') return this.drawToken(p);
        const ctx = this.ctx;
        const H = p.h || 1.9;
        const sitting = p.pose === 'sit';
        const pose = p.pose || 'stand';
        // Detailed graphics: the body follows what the player is doing (steps, run-up, jumps, touches), with fingers
        const anim = this.quality === 'high' && !sitting;
        const gait = anim ? this.gaitOf(p) : null;
        const rig = anim ? this.poseRig(p, gait) : null;
        const crouch = rig ? rig.crouch : pose === 'ready' || pose === 'pass' ? 0.12 : 0;
        const bodyH = H * (1 - crouch);
        const zf = p.z || 0;
        // Sitting on a bench: hips at seat height, knees and feet towards the court
        const SEAT = 0.47, toCourt = -Math.sign(p.y || 1);
        const seat = sitting ? this.cam.project([p.x, p.y, zf + SEAT]) : null;
        const foot = sitting ? this.cam.project([p.x, p.y + toCourt * 0.48, zf]) : this.cam.project([p.x, p.y, zf]);
        const head = this.cam.project([p.x, p.y, zf + (sitting ? SEAT + H * 0.47 : bodyH)]);
        if (!foot || !head || (sitting && !seat)) return;
        // Shadow on the floor (smaller and lighter while jumping)
        const sh = this.cam.project([p.x, p.y, 0.003]);
        if (sh) {
            // Soft contact shadow (darker under the feet, fading outwards)
            const k = 1 / (1 + zf * 1.2), r = 0.4 * sh.s * k;
            if (this.quality !== 'high') {
                // Lighter graphics: a flat shadow
                ctx.fillStyle = 'rgba(0,0,0,' + (0.3 * k).toFixed(3) + ')';
                ctx.beginPath();
                ctx.ellipse(sh.x, sh.y, 0.34 * sh.s * k, 0.34 * sh.s * k * this.flatten(p), 0, 0, Math.PI * 2);
                ctx.fill();
            } else if (r > 0.5) {
                ctx.save();
                ctx.translate(sh.x, sh.y);
                ctx.scale(1, this.flatten(p));
                const g = ctx.createRadialGradient(0, 0, 0, 0, 0, r);
                g.addColorStop(0, 'rgba(0,0,0,' + (0.42 * k).toFixed(3) + ')');
                g.addColorStop(0.55, 'rgba(0,0,0,' + (0.22 * k).toFixed(3) + ')');
                g.addColorStop(1, 'rgba(0,0,0,0)');
                ctx.fillStyle = g;
                ctx.beginPath();
                ctx.arc(0, 0, r, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            }
        }
        let hpx = sitting ? (seat.y - head.y) / 0.53 : foot.y - head.y;
        if (hpx < 3) return;
        // Crouching (detailed graphics) bends the knees: the torso keeps most of its size, the legs get shorter
        if (rig && crouch > 0) hpx *= 1 + 0.6 * crouch / (1 - crouch);
        const w = Math.max(3, hpx * 0.26);                 // shoulder width
        const cx = sitting ? seat.x : foot.x, top = head.y;
        if (p.id) this.hits.push({id: p.id, x0: cx - w * 0.75, x1: cx + w * 0.75, y0: top - 4, y1: foot.y + 3});
        const outfit = p.outfit || 'player';
        const skin = p.skinColor || C.skin[(p.skin || 0) % C.skin.length];
        const skinDark = tone(skin, -0.18);
        const jersey = p.jersey || '#2f6fd0';
        const shorts = p.shorts || '#1d2533';
        const longTrousers = outfit !== 'player';
        const headR = hpx * 0.068;
        const neckY = top + headR * 2.05;
        const shY = top + hpx * 0.19;                       // shoulder line
        const hipY = top + hpx * 0.53;
        const kneeY = hipY + (foot.y - hipY) * 0.5;
        const detail = hpx > 26 && this.quality !== 'low';  // small figures (and simple graphics): fewer details
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        if (p.highlight) {
            ctx.fillStyle = p.highlight;
            ctx.beginPath();
            ctx.ellipse(cx, foot.y, w * 1.0, w * 0.36, 0, 0, Math.PI * 2);
            ctx.fill();
            // A soft halo around the whole figure (hover / selection / people involved)
            ctx.save();
            ctx.globalAlpha = 0.55;
            roundRect(ctx, cx - w * 0.85, top - hpx * 0.06, w * 1.7, foot.y - top + hpx * 0.08, w * 0.6);
            ctx.fill();
            ctx.restore();
        }

        const line = (pts, color, width) => {
            ctx.strokeStyle = color;
            ctx.lineWidth = width;
            ctx.beginPath();
            ctx.moveTo(pts[0][0], pts[0][1]);
            for (let i = 1; i < pts.length; i++) ctx.lineTo(pts[i][0], pts[i][1]);
            ctx.stroke();
        };
        // A limb with a thin darker contour and a light stripe on the lit side (volume), on bigger figures
        const limb = (pts, color, width) => {
            if (detail && width > 3) line(pts, tone(color, -0.38), width + Math.max(1, width * 0.16));
            line(pts, color, width);
            if (detail && width > 4) {
                const hl = pts.map(q => [q[0] - width * 0.18, q[1]]);
                ctx.globalAlpha = 0.22;
                line(hl, tone(color, 0.45), width * 0.28);
                ctx.globalAlpha = 1;
            }
        };

        // ---- Legs: thigh, knee (bent when ready), calf, sock, shoe ----
        let spread = w * (pose === 'ready' || pose === 'pass' || pose === 'block' ? 0.34 : pose === 'walk' ? 0.26 : 0.2);
        if (rig) spread = w * Math.max(rig.spread != null ? rig.spread : spread / w, 0.24 * rig.run);
        const legW = Math.max(1.6, w * 0.25);
        [-1, 1].forEach(sd => {
            const hip = [cx + sd * w * 0.17, hipY];
            let ft = [cx + sd * spread, foot.y - legW * 0.25];
            let knee = [(hip[0] + ft[0]) / 2 + sd * (crouch ? w * 0.16 : w * 0.03), kneeY];
            if (rig) {
                // Knees out as much as the crouch; steps: the feet lift in turn; in the air the legs fold
                const LL = foot.y - hipY, tk = rig.tuck;
                const lift = rig.run > 0.05 ? Math.max(0, Math.sin(rig.ph + (sd > 0 ? Math.PI : 0))) * rig.run : 0;
                ft[0] += (cx - ft[0]) * tk * 0.35;
                ft[1] -= LL * (lift * 0.2 + tk * 0.24);
                knee = [(hip[0] + ft[0]) / 2 + sd * w * (0.03 + crouch * 1.1 + lift * 0.05 + tk * 0.1), kneeY - LL * (lift * 0.07 + tk * 0.06)];
            }
            if (sitting) {
                const kp = this.cam.project([p.x, p.y + toCourt * 0.45, zf + SEAT + 0.04]);
                knee = kp ? [kp.x + sd * w * 0.18, kp.y] : knee;
                ft = [foot.x + sd * w * 0.2, foot.y - legW * 0.25];
            }
            const lower = longTrousers ? shorts : skin;
            // Thigh (shorts or trousers colour from the hip down a bit)
            limb([hip, knee], longTrousers ? shorts : skin, legW);
            limb([knee, ft], lower, legW * 0.88);
            if (!longTrousers) {
                // Shorts cover the top of the thigh, with a stripe in the trim colour on the outer side
                const sEnd = [hip[0] + (knee[0] - hip[0]) * 0.42, hip[1] + (knee[1] - hip[1]) * 0.42];
                limb([hip, sEnd], shorts, legW * 1.18);
                if (detail && p.trim && outfit === 'player') line([[hip[0] + sd * legW * 0.45, hip[1]], [sEnd[0] + sd * legW * 0.45, sEnd[1]]], p.trim, Math.max(0.8, legW * 0.14));
                if (detail && outfit === 'player') {
                    // Knee pads (rounded, with a highlight) and socks with a band in the team colour
                    const kp0 = [knee[0], knee[1] - legW * 0.25], kp1 = [knee[0], knee[1] + legW * 0.35];
                    line([kp0, kp1], '#1c2029', legW * 1.1);
                    line([kp0, kp1], '#2e3442', legW * 0.95);
                    ctx.globalAlpha = 0.35;
                    line([[kp0[0] - legW * 0.2, kp0[1] + legW * 0.05], [kp1[0] - legW * 0.2, kp1[1] - legW * 0.05]], '#8a93a6', legW * 0.2);
                    ctx.globalAlpha = 1;
                    const sk = [ft[0] + (knee[0] - ft[0]) * 0.3, ft[1] + (knee[1] - ft[1]) * 0.3];
                    limb([sk, ft], '#f4f4f4', legW * 0.86);
                    ctx.lineCap = 'butt';
                    line([[sk[0] + (ft[0] - sk[0]) * 0.08, sk[1] + (ft[1] - sk[1]) * 0.08], [sk[0] + (ft[0] - sk[0]) * 0.24, sk[1] + (ft[1] - sk[1]) * 0.24]], jersey, legW * 0.86);
                    ctx.lineCap = 'round';
                }
            }
            // Shoe: upper, sole and (players) a stripe in the team colour
            const shoe = outfit === 'player' ? '#f4f5f7' : '#15171c';
            const sx0 = ft[0] + sd * legW * 0.15, sy0 = ft[1] + legW * 0.11;
            if (detail) {
                ctx.fillStyle = outfit === 'player' ? '#b9bec8' : '#0a0b0e';
                ctx.beginPath();
                ctx.ellipse(sx0, sy0 + legW * 0.16, legW * 0.78, legW * 0.3, 0, 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.fillStyle = shoe;
            ctx.beginPath();
            ctx.ellipse(sx0, sy0, legW * 0.72, legW * 0.42, 0, 0, Math.PI * 2);
            ctx.fill();
            if (outfit === 'player' && detail) {
                ctx.strokeStyle = jersey;
                ctx.lineWidth = Math.max(0.8, legW * 0.14);
                ctx.beginPath();
                ctx.moveTo(sx0 - legW * 0.4, sy0 + legW * 0.08);
                ctx.quadraticCurveTo(sx0, sy0 - legW * 0.12, sx0 + legW * 0.45, sy0 - legW * 0.02);
                ctx.stroke();
            }
        });

        // ---- Arms: from the shoulder to the hand position of the pose, bent at the elbow ----
        const armW = Math.max(1.4, w * 0.16);
        const sleeve = outfit === 'player' || outfit === 'ref' ? 0.38 : 1; // short sleeves; staff in long sleeves
        const arms = [];
        const arm = (side, ex, ey, hand, elb) => arms.push([side, ex, ey, hand, elb]);
        const up = top - hpx * 0.26;
        const sh0 = shY + hpx * 0.02;
        // Running: the upper body leans a little towards where the person is going (around the hips)
        let lean = 0;
        if (rig && rig.run > 0.1 && gait && gait.ux != null) {
            const q = this.cam.project([p.x + gait.ux * 0.6, p.y + gait.uy * 0.6, zf]);
            if (q) lean = Math.max(-0.13, Math.min(0.13, (q.x - foot.x) / hpx * 0.5)) * rig.run;
        }
        const cl = Math.cos(lean), sl = Math.sin(lean);
        const toBody = q => (lean ? [cx + (q[0] - cx) * cl + (q[1] - hipY) * sl, hipY - (q[0] - cx) * sl + (q[1] - hipY) * cl] : q);
        const rigged = rig && !p.live && this.rigArms(p, rig, {cx, top, hpx, w, sh0, hipY, zf, bodyH, toBody});
        if (rigged) rigged.forEach(a => arms.push(a));
        else if (p.live) {
            // A referee making an official signal: hands placed from the signal drawings (figure units, 202 tall)
            const u = hpx / 202, f = p.live.flip || 1;
            ['L', 'R'].forEach(k => {
                const a = p.live[k];
                // (7th value: where the hand points, as in the drawing: e.g. the index towards the chest)
                arms.push([(k === 'L' ? -1 : 1) * f, (a.x - 130) * u * f, top + (a.y - 30) * u, a.hand, [cx + (a.ex - 130) * u * f, top + (a.ey - 30) * u], undefined, a.hd ? [a.hd[0] * f, a.hd[1]] : null]);
            });
        } else if (pose === 'block') {
            arm(-1, -w * 0.42, up);
            arm(1, w * 0.42, up);
        } else if (pose === 'set') {
            arm(-1, -w * 0.22, top - hpx * 0.08);
            arm(1, w * 0.22, top - hpx * 0.08);
        } else if (pose === 'attack' || pose === 'serve') {
            // The hitting arm moves: elbow high and forward, hand cocked behind the head -> straight up at the hit ->
            // down across the body; the other arm leads up and then drops
            const k = p.swing == null ? 0.5 : p.swing;
            const lerp = (a, b, u) => a + (b - a) * u;
            let hx, hy, ox, oy;
            if (k < 0.5) {
                const u = k / 0.5;
                hx = lerp(w * 0.62, w * 0.3, u);
                hy = lerp(top + hpx * 0.02, up, u);
                ox = -w * 0.35;
                oy = lerp(up + hpx * 0.06, sh0 - hpx * 0.05, u);
            } else {
                const u = (k - 0.5) / 0.5;
                hx = lerp(w * 0.3, -w * 0.2, u);
                hy = lerp(up, sh0 + hpx * 0.24, u);
                ox = lerp(-w * 0.35, -w * 0.55, u);
                oy = lerp(sh0 - hpx * 0.05, sh0 + hpx * 0.26, u);
            }
            arm(1, hx, hy, null, k < 0.4 ? [cx + w * 0.75, sh0 - hpx * 0.1] : null);
            arm(-1, ox, oy);
        } else if (pose === 'pass') {
            arm(-1, -w * 0.04, sh0 + hpx * 0.27);
            arm(1, w * 0.04, sh0 + hpx * 0.27);
        } else if (pose === 'gesture-t') {
            arm(-1, -w * 0.9, sh0 - hpx * 0.02);
            arm(1, w * 0.9, sh0 - hpx * 0.02);
        } else if (pose === 'gesture-sub') {
            arm(-1, -w * 0.5, sh0 + hpx * 0.02);
            arm(1, w * 0.5, sh0 - hpx * 0.06);
        } else if (pose === 'wave') {
            arm(1, w * 0.4, up);
            arm(-1, -w * 0.5, sh0 + hpx * 0.25);
        } else if (pose === 'point') {
            arm(1, w * 1.1, sh0 - hpx * 0.06);
            arm(-1, -w * 0.5, sh0 + hpx * 0.25);
        } else if (pose === 'sit') {
            arm(-1, -w * 0.32, sh0 + hpx * 0.27);
            arm(1, w * 0.32, sh0 + hpx * 0.27);
        } else {
            arm(-1, -w * 0.58, sh0 + hpx * 0.29);
            arm(1, w * 0.58, sh0 + hpx * 0.29);
        }
        const fine = anim && hpx * 0.1 > 4.5; // hands with fingers
        const drawArm = ([side, ex, ey, handType, elb, rigFront, hdir]) => {
            const s0 = [cx + side * w * 0.44, sh0];
            let hand = [cx + ex, ey];
            if (rigFront != null) {
                // Animated arms: the shoulder rises with a hand above the head; they never stretch beyond their length
                if (hand[1] < top) s0[1] -= hpx * 0.035 * Math.min(1, (top - hand[1]) / (hpx * 0.1));
                const dx = hand[0] - s0[0], dy = hand[1] - s0[1], l = Math.hypot(dx, dy), mx = hpx * 0.46;
                if (l > mx) hand = [s0[0] + dx / l * mx, s0[1] + dy / l * mx];
            }
            const len = Math.hypot(hand[0] - s0[0], hand[1] - s0[1]);
            const reach = hpx * 0.4;
            // Elbow: halfway, pushed outwards when the arm is bent (or given, for the live signals)
            const bend = Math.max(0, (reach - len) / reach) * hpx * 0.12;
            const mid = elb || [(s0[0] + hand[0]) / 2 + side * bend, (s0[1] + hand[1]) / 2 + bend * 0.3];
            const sl = [s0[0] + (mid[0] - s0[0]) * sleeve * 2, s0[1] + (mid[1] - s0[1]) * sleeve * 2];
            limb([s0, mid, hand], skin, armW);
            if (sleeve >= 1) limb([s0, mid, [mid[0] + (hand[0] - mid[0]) * 0.85, mid[1] + (hand[1] - mid[1]) * 0.85]], jersey, armW * 1.12);
            else {
                limb([s0, sl], jersey, armW * 1.28);
                // Sleeve cuff in the trim colour
                if (detail && p.trim) {
                    const c0 = [s0[0] + (sl[0] - s0[0]) * 0.86, s0[1] + (sl[1] - s0[1]) * 0.86];
                    ctx.lineCap = 'butt';
                    line([c0, sl], p.trim, armW * 1.28);
                    ctx.lineCap = 'round';
                }
            }
            // Hand: along the forearm (or where the signal points it), with the thumb on the inner side
            // (the fist of the thumbs up is held upright, as in the drawing)
            const ang = hdir ? (handType === 'thumb' ? -Math.PI / 2 : Math.atan2(hdir[1], hdir[0])) : Math.atan2(hand[1] - mid[1], hand[0] - mid[0]);
            if (fine) {
                // The thumb on the side of the body's middle
                const inn = Math.sign(-Math.sin(ang) * (Math.abs(cx - hand[0]) > w * 0.05 ? cx - hand[0] : -side)) || 1;
                drawHand(ctx, hand, ang, HAND_STYLE[handType] || 'relax', hpx * 0.1, inn, skin);
            } else {
                ctx.fillStyle = skin;
                ctx.beginPath();
                ctx.ellipse(hand[0] + Math.cos(ang) * armW * 0.35, hand[1] + Math.sin(ang) * armW * 0.35, armW * 0.78, armW * 0.55, ang, 0, Math.PI * 2);
                ctx.fill();
            }
            if (handType === 'f2' || handType === 'f4' || handType === 'point' || handType === 'thumb') {
                // Fingers held out from the fist, where the hand points (the index on the chest for the game captain,
                // two / four fingers up for double contact and four hits...); the thumb of the double fault stays up
                const n = handType === 'f4' ? 4 : handType === 'f2' ? 2 : 1;
                const fa = handType === 'thumb' ? -Math.PI / 2 : ang, ux = Math.cos(fa), uy = Math.sin(fa);
                // Base, length and spacing of the fingers: from the detailed fist, or from the small mitten
                const hs = fine ? hpx * 0.1 : armW * 2.1, fb = [hand[0] + ux * hs * 0.15, hand[1] + uy * hs * 0.15];
                const fw = fine ? Math.max(1, hs * 0.13) : Math.max(1, armW * 0.28);
                (fine ? [[tone(skin, -0.38), fw + Math.max(0.8, fw * 0.35)], [skin, fw]] : [[skin, fw]]).forEach(([col, lw]) => {
                    ctx.strokeStyle = col;
                    ctx.lineWidth = lw;
                    ctx.lineCap = 'round';
                    for (let i = 0; i < n; i++) {
                        const o = (i - (n - 1) / 2) * hs * (fine ? 0.17 : 0.2),
                            b = [fb[0] + ux * hs * 0.15, fb[1] + uy * hs * 0.15];
                        ctx.beginPath();
                        ctx.moveTo(b[0] - uy * o, b[1] + ux * o);
                        ctx.lineTo(b[0] - uy * o * 1.5 + ux * hs * 0.5, b[1] + ux * o * 1.5 + uy * hs * 0.5);
                        ctx.stroke();
                    }
                });
            }
            if (handType && /^card/.test(handType)) {
                // Cards held up
                const cw = armW * 1.1, ch = armW * 1.6;
                const cards = handType === 'cardY' ? ['#ffd400'] : handType === 'cardR' ? ['#e53935'] : ['#ffd400', '#e53935'];
                cards.forEach((c, i) => {
                    ctx.fillStyle = c;
                    ctx.fillRect(hand[0] - cw / 2 + (i - (cards.length - 1) / 2) * cw * 0.8, hand[1] - ch - armW * 0.4, cw, ch);
                });
                ctx.fillStyle = skin;
            }
            if (detail && !fine) {
                ctx.fillStyle = skin;
                ctx.beginPath();
                ctx.ellipse(hand[0] - side * armW * 0.35, hand[1], armW * 0.3, armW * 0.2, ang + side * 0.8, 0, Math.PI * 2);
                ctx.fill();
                ctx.strokeStyle = 'rgba(0,0,0,.18)';
                ctx.lineWidth = Math.max(0.6, armW * 0.1);
                ctx.beginPath();
                ctx.ellipse(hand[0] + Math.cos(ang) * armW * 0.35, hand[1] + Math.sin(ang) * armW * 0.35, armW * 0.78, armW * 0.55, ang, 0, Math.PI * 2);
                ctx.stroke();
            }
        };
        // Raised arms go in front of the body, lowered ones behind it (except while passing)
        const front = arms.filter(a => (a[5] != null ? a[5] : a[2] < sh0 || pose === 'pass' || pose === 'sit' || (p.live && Math.abs(a[1]) < w * 0.6)));
        if (lean) {
            ctx.save();
            ctx.translate(cx, hipY);
            ctx.rotate(lean);
            ctx.translate(-cx, -hipY);
        }
        arms.filter(a => !front.includes(a)).forEach(drawArm);

        // ---- Shorts / trousers top ----
        ctx.fillStyle = shorts;
        roundRect(ctx, cx - w * 0.36, hipY - hpx * 0.06, w * 0.72, hpx * (sitting ? 0.09 : 0.1), w * 0.12);
        ctx.fill();

        // ---- Torso: shoulders wider than the waist, light from the left ----
        const g = ctx.createLinearGradient(cx - w / 2, 0, cx + w / 2, 0);
        g.addColorStop(0, tone(jersey, 0.12));
        g.addColorStop(0.55, jersey);
        g.addColorStop(1, tone(jersey, -0.22));
        ctx.fillStyle = g;
        const waist = w * 0.36, sw = w * 0.5, r = w * 0.18;
        ctx.beginPath();
        ctx.moveTo(cx - sw + r, shY);
        ctx.lineTo(cx + sw - r, shY);
        ctx.quadraticCurveTo(cx + sw, shY, cx + sw, shY + r);
        ctx.lineTo(cx + waist + w * 0.02, hipY - hpx * 0.02);
        ctx.quadraticCurveTo(cx + waist, hipY + hpx * 0.01, cx + waist - r * 0.6, hipY + hpx * 0.01);
        ctx.lineTo(cx - waist + r * 0.6, hipY + hpx * 0.01);
        ctx.quadraticCurveTo(cx - waist, hipY + hpx * 0.01, cx - waist - w * 0.02, hipY - hpx * 0.02);
        ctx.lineTo(cx - sw, shY + r);
        ctx.quadraticCurveTo(cx - sw, shY, cx - sw + r, shY);
        ctx.fill();
        const torsoH = hipY - shY;
        if (detail) {
            // Light from above (chest brighter, waist darker) and a thin contour
            const gv = ctx.createLinearGradient(0, shY, 0, hipY);
            gv.addColorStop(0, 'rgba(255,255,255,0.12)');
            gv.addColorStop(0.45, 'rgba(255,255,255,0)');
            gv.addColorStop(1, 'rgba(0,0,0,0.16)');
            ctx.fillStyle = gv;
            ctx.fill();
            ctx.strokeStyle = tone(jersey, -0.42);
            ctx.lineWidth = Math.max(0.8, w * 0.03);
            ctx.stroke();
        }
        if (detail) {
            // Collar: a V for players, a polo collar for referees and staff
            if (outfit === 'player') {
                line([[cx - w * 0.13, shY + hpx * 0.002], [cx, shY + torsoH * 0.13], [cx + w * 0.13, shY + hpx * 0.002]], p.trim || tone(jersey, -0.3), Math.max(1, w * 0.05));
            } else {
                ctx.fillStyle = p.collar || '#ffffff';
                ctx.beginPath();
                ctx.moveTo(cx - w * 0.2, shY - hpx * 0.005);
                ctx.lineTo(cx, shY + torsoH * 0.14);
                ctx.lineTo(cx + w * 0.2, shY - hpx * 0.005);
                ctx.lineTo(cx + w * 0.1, shY - hpx * 0.005);
                ctx.lineTo(cx, shY + torsoH * 0.06);
                ctx.lineTo(cx - w * 0.1, shY - hpx * 0.005);
                ctx.closePath();
                ctx.fill();
                if (outfit === 'ref' && p.badge !== false) {
                    // Federation badge on the chest
                    ctx.fillStyle = '#ffd54f';
                    ctx.beginPath();
                    ctx.arc(cx - w * 0.22, shY + torsoH * 0.28, Math.max(1, w * 0.055), 0, Math.PI * 2);
                    ctx.fill();
                }
            }
        }
        // Side stripes in the trim colour (no band across the jersey: the only stripe under the number is the captain's)
        if (p.trim && detail) {
            ctx.fillStyle = p.trim;
            ctx.fillRect(cx - sw + w * 0.04, shY + r, Math.max(1, w * 0.05), torsoH * 0.55);
            ctx.fillRect(cx + sw - w * 0.09, shY + r, Math.max(1, w * 0.05), torsoH * 0.55);
        }
        // Number (and captain's stripe under it)
        if (p.number !== undefined && w > 7) {
            ctx.fillStyle = p.numberColor || '#ffffff';
            ctx.font = '700 ' + Math.round(Math.max(8, w * 0.6)) + 'px "Barlow Semi Condensed", "Inter", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            if (detail) {
                ctx.strokeStyle = tone(jersey, -0.5);
                ctx.lineWidth = Math.max(1, w * 0.06);
                ctx.lineJoin = 'round';
                ctx.strokeText(String(p.number), cx, shY + torsoH * 0.47);
            }
            ctx.fillText(String(p.number), cx, shY + torsoH * 0.47);
            // Small number on the shorts (front, right leg)
            if (detail && w > 16 && outfit === 'player') {
                ctx.font = '700 ' + Math.round(w * 0.2) + 'px "Barlow Semi Condensed", "Inter", sans-serif';
                ctx.fillText(String(p.number), cx + w * 0.2, hipY + hpx * 0.02);
                ctx.font = '700 ' + Math.round(Math.max(8, w * 0.6)) + 'px "Barlow Semi Condensed", "Inter", sans-serif';
            }
            // Captain: a stripe under the number (Rule 19.1.4 / 5.1)
            if (p.captain) ctx.fillRect(cx - w * 0.2, shY + torsoH * 0.72, w * 0.4, Math.max(1.8, w * 0.07));
        }
        front.forEach(drawArm);

        // ---- Neck and head ----
        line([[cx, neckY - headR * 0.3], [cx, shY + hpx * 0.01]], skinDark, Math.max(1.5, w * 0.2));
        const hy = top + headR;
        // Head turned towards what the person is watching (p.lookX: a point along the court, e.g. the referees at the
        // service: the 1st watches the serving team, the 2nd the receiving team): -1 .. 1, to the left / right on screen
        let turn = 0;
        if (p.lookX != null && !p.back) {
            const q = this.cam.project([p.lookX, p.y * 0.3, 1.2]);
            if (q) turn = Math.max(-1, Math.min(1, (q.x - cx) / (hpx * 1.4)));
            if (gait) {
                gait.turn = gait.turn == null ? turn : gait.turn + (turn - gait.turn) * 0.2;
                turn = gait.turn;
            }
        }
        const fcx = cx + turn * headR * 0.34; // middle of the face
        ctx.fillStyle = skin;
        ctx.beginPath();
        ctx.ellipse(cx, hy, headR * 0.9, headR, 0, 0, Math.PI * 2);
        ctx.fill();
        if (detail) {
            // Ears (the one on the side the head turns to goes out of sight) and a soft shadow on the right
            ctx.beginPath();
            ctx.ellipse(cx - headR * 0.9, hy + headR * 0.1, headR * 0.2 * (1 + Math.min(0, turn) * 0.8), headR * 0.2, 0, 0, Math.PI * 2);
            ctx.ellipse(cx + headR * 0.9, hy + headR * 0.1, headR * 0.2 * (1 - Math.max(0, turn) * 0.8), headR * 0.2, 0, 0, Math.PI * 2);
            ctx.fill();
            ctx.fillStyle = 'rgba(0,0,0,.12)';
            ctx.beginPath();
            ctx.ellipse(cx + headR * 0.35, hy + headR * 0.1, headR * 0.5, headR * 0.85, 0, -Math.PI / 2, Math.PI / 2);
            ctx.fill();
        }
        const hair = p.hair || (outfit === 'player' ? null : '#2b2420');
        // Face (on bigger figures, when seen from the front): eyes, brows, nose and mouth
        if (detail && !p.back && headR > 3.2) {
            // (turned: the features move to that side and get closer together)
            const ey = hy + headR * 0.08, ex = headR * 0.36 * (1 - 0.22 * Math.abs(turn)), nd = turn >= 0 ? 1 : -1;
            ctx.fillStyle = '#1d1a18';
            ctx.beginPath();
            ctx.ellipse(fcx - ex, ey, headR * 0.1, headR * 0.13, 0, 0, Math.PI * 2);
            ctx.ellipse(fcx + ex, ey, headR * 0.1, headR * 0.13, 0, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = hair || '#2b2420';
            ctx.lineWidth = Math.max(0.8, headR * 0.1);
            ctx.beginPath();
            ctx.moveTo(fcx - ex - headR * 0.16, ey - headR * 0.24);
            ctx.lineTo(fcx - ex + headR * 0.14, ey - headR * 0.28);
            ctx.moveTo(fcx + ex - headR * 0.14, ey - headR * 0.28);
            ctx.lineTo(fcx + ex + headR * 0.16, ey - headR * 0.24);
            ctx.stroke();
            ctx.strokeStyle = tone(skin, -0.28);
            ctx.lineWidth = Math.max(0.7, headR * 0.08);
            const nt = Math.abs(turn) > 0.25 ? nd : 1; // the nose tip points where the head turns
            ctx.beginPath();
            ctx.moveTo(fcx + nt * headR * 0.04, ey + headR * 0.08);
            ctx.lineTo(fcx + nt * headR * (0.1 + 0.08 * Math.abs(turn)), ey + headR * 0.36);
            ctx.lineTo(fcx - nt * headR * 0.04, ey + headR * 0.38);
            ctx.stroke();
            ctx.strokeStyle = tone(skin, -0.4);
            ctx.lineWidth = Math.max(0.7, headR * 0.09);
            const mw = headR * 0.2 * (1 - 0.2 * Math.abs(turn));
            ctx.beginPath();
            ctx.moveTo(fcx - mw, hy + headR * 0.62);
            ctx.quadraticCurveTo(fcx, hy + headR * 0.7, fcx + mw, hy + headR * 0.62);
            ctx.stroke();
        }
        if (hair && p.back) {
            // Seen from behind (facing away): the hair covers the back of the head
            ctx.fillStyle = hair;
            ctx.beginPath();
            ctx.ellipse(cx, hy - headR * 0.05, headR * 0.93, headR * 0.98, 0, 0, Math.PI * 2);
            ctx.fill();
            if ((p.hairStyle || 0) === 1) {
                ctx.beginPath();
                ctx.ellipse(cx, hy + headR * 0.9, headR * 0.3, headR * 0.55, 0, 0, Math.PI * 2);
                ctx.fill();
            }
        } else if (hair) {
            ctx.fillStyle = hair;
            const st = p.hairStyle || 0;
            ctx.beginPath();
            ctx.ellipse(cx, hy - headR * 0.18, headR * 0.95, headR * (st === 2 ? 0.95 : 0.85), 0, Math.PI, Math.PI * 2);
            ctx.fill();
            if (detail && headR > 3.2) {
                // Fringe over the forehead and a soft shine
                ctx.beginPath();
                ctx.ellipse(cx - headR * 0.25 + (fcx - cx) * 0.6, hy - headR * 0.2, headR * 0.55, headR * 0.22, -0.25, 0, Math.PI * 2);
                ctx.fill();
                ctx.strokeStyle = 'rgba(255,255,255,0.18)';
                ctx.lineWidth = Math.max(0.8, headR * 0.12);
                ctx.beginPath();
                ctx.arc(cx - headR * 0.1, hy - headR * 0.2, headR * 0.62, Math.PI * 1.15, Math.PI * 1.45);
                ctx.stroke();
            }
            if (st === 1) {
                // Ponytail
                ctx.beginPath();
                ctx.ellipse(cx + headR * 0.95, hy + headR * 0.15, headR * 0.28, headR * 0.6, -0.4, 0, Math.PI * 2);
                ctx.fill();
            } else if (st === 2) {
                // Longer hair at the sides
                ctx.fillRect(cx - headR * 0.95, hy - headR * 0.2, headR * 0.3, headR * 0.9);
                ctx.fillRect(cx + headR * 0.65, hy - headR * 0.2, headR * 0.3, headR * 0.9);
            }
        }

        if (lean) ctx.restore();

        // Speech bubble (requests, protests) and label
        if (p.bubble) this.drawBubble(cx, top - 4, p.bubble, p.bubbleColor);
        if (p.label && w > 5) {
            ctx.font = '600 ' + Math.round(Math.max(9, Math.min(13, w * 0.5))) + 'px "Inter", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.fillStyle = 'rgba(0,0,0,0.55)';
            const tw = ctx.measureText(p.label).width + 8;
            ctx.fillRect(cx - tw / 2, foot.y + 3, tw, 15);
            ctx.fillStyle = '#fff';
            ctx.fillText(p.label, cx, foot.y + 5);
        }
    };

    /** Top view: a disc in the jersey colour with the number; jumps make it slightly bigger. */
    /** The person under a point of the canvas (CSS pixels), or null: the last drawn (nearest) wins. */
    Renderer.prototype.hitTest = function (x, y) {
        const h = this.hits || [];
        for (let i = h.length - 1; i >= 0; i--) if (x >= h[i].x0 && x <= h[i].x1 && y >= h[i].y0 && y <= h[i].y1) return h[i].id;
        return null;
    };

    Renderer.prototype.drawToken = function (p) {
        const ctx = this.ctx;
        const c = this.cam.project([p.x, p.y, 0]);
        if (!c) return;
        const r = Math.max(6, 0.36 * c.s * (1 + (p.z || 0) * 0.25));
        if (p.id) this.hits.push({
            id: p.id,
            x0: c.x - r * 1.2,
            x1: c.x + r * 1.2,
            y0: c.y - r * 1.2,
            y1: c.y + r * 1.2
        });
        if (p.highlight) {
            ctx.fillStyle = p.highlight;
            ctx.beginPath();
            ctx.arc(c.x, c.y, r * 1.45, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.fillStyle = 'rgba(0,0,0,0.3)';
        ctx.beginPath();
        ctx.arc(c.x + 1.5, c.y + 2, r, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = p.jersey || '#2f6fd0';
        ctx.beginPath();
        ctx.arc(c.x, c.y, r, 0, Math.PI * 2);
        ctx.fill();
        ctx.lineWidth = Math.max(1.5, r * 0.14);
        ctx.strokeStyle = p.trim || 'rgba(255,255,255,0.85)';
        ctx.stroke();
        if (p.number !== undefined && p.number !== '') {
            ctx.fillStyle = p.numberColor || '#fff';
            ctx.font = '700 ' + Math.round(r * 1.05) + 'px "Barlow Semi Condensed", "Inter", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(String(p.number), c.x, c.y + 0.5);
        }
        if (p.captain) {
            ctx.fillStyle = p.numberColor || '#fff';
            ctx.fillRect(c.x - r * 0.35, c.y + r * 0.55, r * 0.7, Math.max(1.5, r * 0.12));
        }
        if (p.bubble) this.drawBubble(c.x, c.y - r - 2, p.bubble, p.bubbleColor);
        if (p.label && !p.number) {
            ctx.font = '600 10px "Inter", sans-serif';
            ctx.fillStyle = 'rgba(255,255,255,0.75)';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.fillText(p.label, c.x, c.y + r + 3);
        }
    };

    Renderer.prototype.drawBubble = function (x, y, text, color) {
        const ctx = this.ctx;
        ctx.font = '700 13px "Inter", sans-serif';
        const w = ctx.measureText(text).width + 14, h = 22;
        ctx.fillStyle = color || '#ffffff';
        roundRect(ctx, x - w / 2, y - h - 6, w, h, 8);
        ctx.fill();
        ctx.beginPath();
        ctx.moveTo(x - 5, y - 7);
        ctx.lineTo(x + 5, y - 7);
        ctx.lineTo(x, y);
        ctx.closePath();
        ctx.fill();
        ctx.fillStyle = '#14264a';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(text, x, y - h / 2 - 6);
    };

    /** Hand shapes of the referees' signals (and of the players' actions) for the detailed hands. */
    const HAND_STYLE = {
        open: 'flat',
        palm: 'flat',
        palmBody: 'flat',
        back: 'flat',
        palmBack: 'flat',
        flat: 'flat',
        blade: 'flat',
        palmDown: 'flat',
        palmDownBack: 'flat',
        tips: 'reach',
        tipsV: 'flat',
        palmUp: 'reach',
        fist: 'fist',
        cardY: 'fist',
        cardR: 'fist',
        cardYR: 'fist',
        point: 'fist',
        f2: 'fist',
        f4: 'fist',
        thumb: 'fist',
        relax: 'relax',
        reach: 'reach',
        set: 'set',
        spread: 'spread',
        grip: 'grip',
        clasp: 'clasp',
    };
    /** Fingers of each hand shape: spread (radians, index to little finger), length, curl (shorter, bent tips), thumb angle. */
    const FINGERS = {
        relax: {sp: 0.1, len: 0.82, curl: 0.55, th: 0.55},
        reach: {sp: 0.18, len: 1, curl: 0.12, th: 0.9},
        set: {sp: 0.34, len: 1, curl: 0.3, th: 1.45},
        spread: {sp: 0.44, len: 1.04, curl: 0, th: 1.15},
        grip: {sp: 0.36, len: 0.9, curl: 1, th: 1.3},
        flat: {sp: 0.04, len: 1, curl: 0, th: 0.35},
    };

    /**
     * A hand with fingers (detailed graphics). wr: the wrist; ang: direction of the forearm (the hand continues it);
     * s: length of the hand in pixels; inn: the side of the thumb (+1 / -1 across the hand).
     */
    function drawHand(ctx, wr, ang, style, s, inn, skin) {
        const dark = tone(skin, -0.38), pl = s * 0.5, pw = s * 0.44;
        const fw = Math.max(0.9, s * 0.12), ow = fw + Math.max(0.7, fw * 0.35);
        ctx.save();
        ctx.translate(wr[0], wr[1]);
        ctx.rotate(ang);
        ctx.lineCap = 'round';
        const palm = (rx, ry) => {
            ctx.fillStyle = skin;
            ctx.strokeStyle = dark;
            ctx.lineWidth = Math.max(0.6, s * 0.035);
            ctx.beginPath();
            ctx.ellipse(pl * 0.5, 0, rx, ry, 0, 0, Math.PI * 2);
            ctx.fill();
            ctx.stroke();
        };
        const strokes = (segs) => {
            [[dark, ow], [skin, fw]].forEach(([c, lw]) => {
                ctx.strokeStyle = c;
                ctx.lineWidth = lw;
                segs.forEach(q => {
                    ctx.beginPath();
                    ctx.moveTo(q[0], q[1]);
                    ctx.quadraticCurveTo(q[2], q[3], q[4], q[5]);
                    ctx.stroke();
                });
            });
        };
        if (style === 'fist' || style === 'clasp') {
            // Fist: knuckles at the end; clasped hands (pass): the thumbs side by side along the forearms
            palm(pl * 0.52, pw * 0.52);
            ctx.fillStyle = skin;
            ctx.strokeStyle = dark;
            ctx.lineWidth = Math.max(0.5, s * 0.03);
            for (let i = 0; i < 4; i++) {
                ctx.beginPath();
                ctx.arc(pl * 0.92, inn * pw * (0.3 - i * 0.2), s * 0.07, 0, Math.PI * 2);
                ctx.fill();
                ctx.stroke();
            }
            if (style === 'clasp') strokes([[pl * 0.15, inn * pw * 0.12, pl * 0.6, inn * pw * 0.14, pl * 1.0, inn * pw * 0.1], [pl * 0.15, -inn * pw * 0.12, pl * 0.6, -inn * pw * 0.14, pl * 1.0, -inn * pw * 0.1]]);
            else strokes([[pl * 0.3, inn * pw * 0.45, pl * 0.55, inn * pw * 0.35, pl * 0.75, inn * pw * 0.08]]);
            ctx.restore();
            return;
        }
        const F = FINGERS[style] || FINGERS.relax;
        const lens = [0.47, 0.52, 0.49, 0.39];
        const segs = lens.map((l0, i) => {
            const y0 = inn * pw * (0.33 - i * 0.22), a = inn * F.sp * (1.5 - i) / 1.5;
            const L = s * l0 * F.len * (1 - 0.4 * F.curl), kx = pl * 0.9;
            // Curled fingers are shorter and bent
            const bend = F.curl * L * 0.14;
            return [kx, y0, kx + Math.cos(a) * L * 0.55 - Math.sin(a) * bend, y0 + Math.sin(a) * L * 0.55 + Math.cos(a) * bend, kx + Math.cos(a) * L, y0 + Math.sin(a) * L];
        });
        // Thumb from the base of the palm, on the inner side
        const tb = [pl * 0.25, inn * pw * 0.42], ta = inn * F.th, tl = s * 0.36 * (1 - 0.25 * F.curl);
        segs.push([tb[0], tb[1], tb[0] + Math.cos(ta) * tl * 0.55, tb[1] + Math.sin(ta) * tl * 0.55, tb[0] + Math.cos(ta * 0.8) * tl, tb[1] + Math.sin(ta * 0.8) * tl]);
        strokes(segs);
        palm(pl * 0.58, pw * 0.52);
        if (style === 'flat' && s > 9) {
            // Fingers together: thin lines between them
            ctx.strokeStyle = dark;
            ctx.lineWidth = Math.max(0.4, s * 0.02);
            for (let i = 0; i < 3; i++) {
                const y = inn * pw * (0.22 - i * 0.22);
                ctx.beginPath();
                ctx.moveTo(pl * 0.98, y);
                ctx.lineTo(pl * 0.98 + s * 0.3, y);
                ctx.stroke();
            }
        }
        ctx.restore();
    }

    function roundRect(ctx, x, y, w, h, r) {
        r = Math.min(r, w / 2, h / 2);
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r);
        ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r);
        ctx.arcTo(x, y, x + w, y, r);
        ctx.closePath();
    }

    window.SimRender = {Renderer, Camera, VIEWS, V};
})();
