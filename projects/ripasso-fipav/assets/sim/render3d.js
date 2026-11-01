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
    norm: a => { const l = Math.hypot(a[0], a[1], a[2]) || 1; return [a[0] / l, a[1] / l, a[2] / l]; },
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
    return { x: this.w / 2 + c[0] * s, y: this.h / 2 - c[1] * s, z: c[2], s };
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
    first: { label: 'Dal seggiolone', pos: [0, -8.4, 4.3], target: [0, 1.6, -0.9], fov: 60 },
    // 2nd referee: on the floor by the other post, scorer's side (shifts towards the receiving team)
    second: { label: 'Sotto la rete', pos: [0, 6.6, 1.75], target: [0, -1.5, 0.2], fov: 70 },
    top: { label: 'Dall’alto', pos: [0, -0.001, 24], target: [0, 0, 0], fov: 50, up: [0, 1, 0] },
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
    this.resize();
  }

  Renderer.prototype.resize = function () {
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
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
      const y0 = Math.atan2(target[1] - pos[1], target[0] - pos[0]), y1 = Math.atan2(gt[1] - pos[1], gt[0] - pos[0]);
      let d = y1 - y0; while (d > Math.PI) d -= 2 * Math.PI; while (d < -Math.PI) d += 2 * Math.PI;
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
    if (fill) { ctx.fillStyle = fill; ctx.fill(); }
    if (stroke) { ctx.strokeStyle = stroke; ctx.lineWidth = lw || 1; ctx.stroke(); }
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
      { pts: [[x0, y0, z1], [x1, y0, z1], [x1, y1, z1], [x0, y1, z1]], n: [0, 0, 1], k: 1 },
      { pts: [[x0, y0, z0], [x1, y0, z0], [x1, y0, z1], [x0, y0, z1]], n: [0, -1, 0], k: 0.8 },
      { pts: [[x0, y1, z0], [x1, y1, z0], [x1, y1, z1], [x0, y1, z1]], n: [0, 1, 0], k: 0.8 },
      { pts: [[x0, y0, z0], [x0, y1, z0], [x0, y1, z1], [x0, y0, z1]], n: [-1, 0, 0], k: 0.65 },
      { pts: [[x1, y0, z0], [x1, y1, z0], [x1, y1, z1], [x1, y0, z1]], n: [1, 0, 0], k: 0.65 },
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

  function aspectOf(r) { return r.cssW / Math.max(1, r.cssH); }

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

  Renderer.prototype.drawBackground = function () {
    const ctx = this.ctx;
    const g = ctx.createLinearGradient(0, 0, 0, this.cssH);
    g.addColorStop(0, C.wall1);
    g.addColorStop(1, C.wall2);
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, this.cssW, this.cssH);
  };

  Renderer.prototype.drawFloor = function () {
    // Hall floor, free zone and court
    this.rect(-16, -11, 16, 11, 0, '#1f4d66');
    this.rect(-13, -7.5, 13, 9.5, 0, C.floor);
    this.rect(-9, -4.5, 9, 4.5, 0, C.court);
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
    this.drawFloor();

    const items = [];
    const push = (p, fn) => { const c = this.cam.toCam(p); if (c[2] > NEAR) items.push({ z: c[2], fn }); };

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
    ctx.restore();
  };

  Renderer.prototype.drawNetSlice = function (y0, y1, h, shake) {
    const top = h, bottom = h - 1.0;
    // A touched net wobbles near the contact point (strongest on the top band)
    const off = y => (shake ? shake.amp * Math.max(0, 1 - Math.abs(y - shake.y) / 2.2) * Math.sin(shake.phase) : 0);
    const o0 = off(y0), o1 = off(y1);
    if (shake) {
      this.poly([[0, y0, bottom], [0, y1, bottom], [o1, y1, top], [o0, y0, top]], C.netMesh);
      this.poly([[o0, y0, top - 0.07], [o1, y1, top - 0.07], [o1, y1, top], [o0, y0, top]], '#fff8d6');
      return;
    }
    this.poly([[0, y0, bottom], [0, y1, bottom], [0, y1, top], [0, y0, top]], C.netMesh);
    // Mesh lines
    const ctx = this.ctx;
    ctx.strokeStyle = 'rgba(255,255,255,0.12)';
    ctx.lineWidth = 1;
    for (let k = 1; k < 4; k++) {
      const a = this.cam.project([0, y0, bottom + k * 0.25]), b = this.cam.project([0, y1, bottom + k * 0.25]);
      if (a && b) { ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke(); }
    }
    this.poly([[0, y0, top - 0.07], [0, y1, top - 0.07], [0, y1, top], [0, y0, top]], C.netBand);
    this.poly([[0, y0, bottom], [0, y1, bottom], [0, y1, bottom + 0.05], [0, y0, bottom + 0.05]], '#dcdcdc');
  };

  Renderer.prototype.drawPost = function (y, h) {
    const a = this.cam.project([0, y, 0]), b = this.cam.project([0, y, h + 0.15]);
    if (!a || !b) return;
    const ctx = this.ctx;
    ctx.strokeStyle = C.post;
    ctx.lineWidth = Math.max(2, 0.1 * a.s);
    ctx.lineCap = 'round';
    ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
  };

  Renderer.prototype.drawAntenna = function (y, h, shake) {
    const ctx = this.ctx;
    const segs = 8, z0 = h - 1.0, z1 = h + 0.8;
    // A hit on the net (or on the antenna itself) makes the antenna sway too, more at the top
    const sway = shake ? shake.amp * 1.6 * Math.max(0, 1 - Math.abs(y - shake.y) / 3) * Math.sin(shake.phase) : 0;
    const xAt = k => sway * Math.max(0, k - 0.25) * 1.3;
    for (let i = 0; i < segs; i++) {
      const a = this.cam.project([xAt(i / segs), y, z0 + (z1 - z0) * i / segs]), b = this.cam.project([xAt((i + 1) / segs), y, z0 + (z1 - z0) * (i + 1) / segs]);
      if (!a || !b) continue;
      ctx.strokeStyle = i % 2 ? C.antennaB : C.antennaA;
      ctx.lineWidth = Math.max(1.5, 0.025 * a.s);
      ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
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
    if (m.fill) { ctx.fillStyle = m.fill; ctx.fill(); }
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
    ctx.beginPath(); ctx.arc(x, y, r, 0, Math.PI * 2);
    ctx.fillStyle = '#f7d21a';
    ctx.fill();
    ctx.clip();
    if (r > 3.5) {
      // Curved panels turning with the spin: blue bands and thin seams, like the 8-panel match balls
      const a = (b.spin || 0) * 0.6;
      ctx.save();
      ctx.translate(x, y); ctx.rotate(a);
      const band = (k, color, wid) => {
        const off = k * r * 0.62;
        ctx.strokeStyle = color; ctx.lineWidth = wid; ctx.lineCap = 'butt';
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
    ctx.beginPath(); ctx.arc(x, y, r, 0, Math.PI * 2); ctx.fill();
    ctx.restore();
    ctx.strokeStyle = 'rgba(0,0,0,.35)'; ctx.lineWidth = Math.max(0.6, r * 0.06);
    ctx.beginPath(); ctx.arc(x, y, r, 0, Math.PI * 2); ctx.stroke();
    if (b.highlight) {
      ctx.strokeStyle = b.highlight;
      ctx.lineWidth = 2;
      ctx.beginPath(); ctx.arc(x, y, r + 4, 0, Math.PI * 2); ctx.stroke();
    }
  };


  // ---------- People ----------
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
    const crouch = pose === 'ready' || pose === 'pass' ? 0.12 : 0;
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
      const k = 1 / (1 + zf * 1.2);
      ctx.fillStyle = 'rgba(0,0,0,' + (0.3 * k).toFixed(3) + ')';
      ctx.beginPath();
      ctx.ellipse(sh.x, sh.y, 0.34 * sh.s * k, 0.34 * sh.s * k * this.flatten(p), 0, 0, Math.PI * 2);
      ctx.fill();
    }
    const hpx = sitting ? (seat.y - head.y) / 0.53 : foot.y - head.y;
    if (hpx < 3) return;
    const w = Math.max(3, hpx * 0.26);                 // shoulder width
    const cx = sitting ? seat.x : foot.x, top = head.y;
    if (p.id) this.hits.push({ id: p.id, x0: cx - w * 0.75, x1: cx + w * 0.75, y0: top - 4, y1: foot.y + 3 });
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
    const detail = hpx > 26;                            // small figures: fewer details
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
      ctx.strokeStyle = color; ctx.lineWidth = width;
      ctx.beginPath(); ctx.moveTo(pts[0][0], pts[0][1]);
      for (let i = 1; i < pts.length; i++) ctx.lineTo(pts[i][0], pts[i][1]);
      ctx.stroke();
    };

    // ---- Legs: thigh, knee (bent when ready), calf, sock, shoe ----
    const spread = w * (pose === 'ready' || pose === 'pass' || pose === 'block' ? 0.34 : pose === 'walk' ? 0.26 : 0.2);
    const legW = Math.max(1.6, w * 0.25);
    [-1, 1].forEach(sd => {
      const hip = [cx + sd * w * 0.17, hipY];
      let ft = [cx + sd * spread, foot.y - legW * 0.25];
      let knee = [(hip[0] + ft[0]) / 2 + sd * (crouch ? w * 0.16 : w * 0.03), kneeY];
      if (sitting) {
        const kp = this.cam.project([p.x, p.y + toCourt * 0.45, zf + SEAT + 0.04]);
        knee = kp ? [kp.x + sd * w * 0.18, kp.y] : knee;
        ft = [foot.x + sd * w * 0.2, foot.y - legW * 0.25];
      }
      const lower = longTrousers ? shorts : skin;
      // Thigh (shorts or trousers colour from the hip down a bit)
      line([hip, knee], longTrousers ? shorts : skin, legW);
      line([knee, ft], lower, legW * 0.88);
      if (!longTrousers) {
        // Shorts cover the top of the thigh
        line([hip, [hip[0] + (knee[0] - hip[0]) * 0.42, hip[1] + (knee[1] - hip[1]) * 0.42]], shorts, legW * 1.18);
        if (detail && outfit === 'player') {
          // Knee pads and socks
          line([[knee[0], knee[1] - legW * 0.25], [knee[0], knee[1] + legW * 0.35]], '#2a2f3a', legW * 1.02);
          line([[ft[0] + (knee[0] - ft[0]) * 0.28, ft[1] + (knee[1] - ft[1]) * 0.28], ft], '#f4f4f4', legW * 0.86);
        }
      }
      // Shoe
      const shoe = outfit === 'player' ? '#f2f2f2' : '#15171c';
      ctx.fillStyle = shoe;
      ctx.beginPath();
      ctx.ellipse(ft[0] + sd * legW * 0.15, foot.y - legW * 0.12, legW * 0.72, legW * 0.42, 0, 0, Math.PI * 2);
      ctx.fill();
      if (outfit === 'player' && detail) {
        ctx.fillStyle = 'rgba(0,0,0,.18)';
        ctx.fillRect(ft[0] + sd * legW * 0.15 - legW * 0.7, foot.y - legW * 0.05, legW * 1.4, legW * 0.18);
      }
    });

    // ---- Arms: from the shoulder to the hand position of the pose, bent at the elbow ----
    const armW = Math.max(1.4, w * 0.16);
    const sleeve = outfit === 'player' || outfit === 'ref' ? 0.38 : 1; // short sleeves; staff in long sleeves
    const arms = [];
    const arm = (side, ex, ey, hand, elb) => arms.push([side, ex, ey, hand, elb]);
    const up = top - hpx * 0.26;
    const sh0 = shY + hpx * 0.02;
    if (p.live) {
      // A referee making an official signal: hands placed from the signal drawings (figure units, 202 tall)
      const u = hpx / 202, f = p.live.flip || 1;
      ['L', 'R'].forEach(k => {
        const a = p.live[k];
        arm((k === 'L' ? -1 : 1) * f, (a.x - 130) * u * f, top + (a.y - 30) * u, a.hand, [cx + (a.ex - 130) * u * f, top + (a.ey - 30) * u]);
      });
    } else if (pose === 'block') { arm(-1, -w * 0.42, up); arm(1, w * 0.42, up); }
    else if (pose === 'set') { arm(-1, -w * 0.22, top - hpx * 0.08); arm(1, w * 0.22, top - hpx * 0.08); }
    else if (pose === 'attack' || pose === 'serve') {
      // The hitting arm moves: elbow high and forward, hand cocked behind the head -> straight up at the hit ->
      // down across the body; the other arm leads up and then drops
      const k = p.swing == null ? 0.5 : p.swing;
      const lerp = (a, b, u) => a + (b - a) * u;
      let hx, hy, ox, oy;
      if (k < 0.5) { const u = k / 0.5; hx = lerp(w * 0.62, w * 0.3, u); hy = lerp(top + hpx * 0.02, up, u); ox = -w * 0.35; oy = lerp(up + hpx * 0.06, sh0 - hpx * 0.05, u); }
      else { const u = (k - 0.5) / 0.5; hx = lerp(w * 0.3, -w * 0.2, u); hy = lerp(up, sh0 + hpx * 0.24, u); ox = lerp(-w * 0.35, -w * 0.55, u); oy = lerp(sh0 - hpx * 0.05, sh0 + hpx * 0.26, u); }
      arm(1, hx, hy, null, k < 0.4 ? [cx + w * 0.75, sh0 - hpx * 0.1] : null);
      arm(-1, ox, oy);
    }
    else if (pose === 'pass') { arm(-1, -w * 0.04, sh0 + hpx * 0.27); arm(1, w * 0.04, sh0 + hpx * 0.27); }
    else if (pose === 'gesture-t') { arm(-1, -w * 0.9, sh0 - hpx * 0.02); arm(1, w * 0.9, sh0 - hpx * 0.02); }
    else if (pose === 'gesture-sub') { arm(-1, -w * 0.5, sh0 + hpx * 0.02); arm(1, w * 0.5, sh0 - hpx * 0.06); }
    else if (pose === 'wave') { arm(1, w * 0.4, up); arm(-1, -w * 0.5, sh0 + hpx * 0.25); }
    else if (pose === 'point') { arm(1, w * 1.1, sh0 - hpx * 0.06); arm(-1, -w * 0.5, sh0 + hpx * 0.25); }
    else if (pose === 'sit') { arm(-1, -w * 0.32, sh0 + hpx * 0.27); arm(1, w * 0.32, sh0 + hpx * 0.27); }
    else { arm(-1, -w * 0.58, sh0 + hpx * 0.29); arm(1, w * 0.58, sh0 + hpx * 0.29); }
    const drawArm = ([side, ex, ey, handType, elb]) => {
      const s0 = [cx + side * w * 0.44, sh0];
      const hand = [cx + ex, ey];
      const len = Math.hypot(hand[0] - s0[0], hand[1] - s0[1]);
      const reach = hpx * 0.4;
      // Elbow: halfway, pushed outwards when the arm is bent (or given, for the live signals)
      const bend = Math.max(0, (reach - len) / reach) * hpx * 0.12;
      const mid = elb || [(s0[0] + hand[0]) / 2 + side * bend, (s0[1] + hand[1]) / 2 + bend * 0.3];
      const sl = [s0[0] + (mid[0] - s0[0]) * sleeve * 2, s0[1] + (mid[1] - s0[1]) * sleeve * 2];
      line([s0, mid, hand], skin, armW);
      if (sleeve >= 1) line([s0, mid, [mid[0] + (hand[0] - mid[0]) * 0.85, mid[1] + (hand[1] - mid[1]) * 0.85]], jersey, armW * 1.12);
      else line([s0, sl], jersey, armW * 1.28);
      // Hand: a small mitten along the forearm, with the thumb on the inner side
      const ang = Math.atan2(hand[1] - mid[1], hand[0] - mid[0]);
      ctx.fillStyle = skin;
      ctx.beginPath(); ctx.ellipse(hand[0] + Math.cos(ang) * armW * 0.35, hand[1] + Math.sin(ang) * armW * 0.35, armW * 0.78, armW * 0.55, ang, 0, Math.PI * 2); ctx.fill();
      if (handType === 'f2' || handType === 'f4' || handType === 'point' || handType === 'thumb') {
        // Fingers held up (collaboration signals at the chest, double contact, four hits, eight seconds...)
        const n = handType === 'f4' ? 4 : handType === 'f2' ? 2 : 1;
        ctx.strokeStyle = skin; ctx.lineWidth = Math.max(1, armW * 0.28); ctx.lineCap = 'round';
        for (let i = 0; i < n; i++) {
          const dx = (i - (n - 1) / 2) * armW * 0.42;
          ctx.beginPath(); ctx.moveTo(hand[0] + dx, hand[1] - armW * 0.3); ctx.lineTo(hand[0] + dx * 1.35, hand[1] - armW * 1.35); ctx.stroke();
        }
      }
      if (handType && /^card/.test(handType)) {
        // Cards held up
        const cw = armW * 1.1, ch = armW * 1.6;
        const cards = handType === 'cardY' ? ['#ffd400'] : handType === 'cardR' ? ['#e53935'] : ['#ffd400', '#e53935'];
        cards.forEach((c, i) => { ctx.fillStyle = c; ctx.fillRect(hand[0] - cw / 2 + (i - (cards.length - 1) / 2) * cw * 0.8, hand[1] - ch - armW * 0.4, cw, ch); });
        ctx.fillStyle = skin;
      }
      if (detail) {
        ctx.beginPath(); ctx.ellipse(hand[0] - side * armW * 0.35, hand[1], armW * 0.3, armW * 0.2, ang + side * 0.8, 0, Math.PI * 2); ctx.fill();
        ctx.strokeStyle = 'rgba(0,0,0,.18)'; ctx.lineWidth = Math.max(0.6, armW * 0.1);
        ctx.beginPath(); ctx.ellipse(hand[0] + Math.cos(ang) * armW * 0.35, hand[1] + Math.sin(ang) * armW * 0.35, armW * 0.78, armW * 0.55, ang, 0, Math.PI * 2); ctx.stroke();
      }
    };
    // Raised arms go in front of the body, lowered ones behind it (except while passing)
    const front = arms.filter(a => a[2] < sh0 || pose === 'pass' || pose === 'sit' || (p.live && Math.abs(a[1]) < w * 0.6));
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
      // Collar: a V for players, a polo collar for referees and staff
      if (outfit === 'player') {
        line([[cx - w * 0.13, shY + hpx * 0.002], [cx, shY + torsoH * 0.13], [cx + w * 0.13, shY + hpx * 0.002]], p.trim || tone(jersey, -0.3), Math.max(1, w * 0.05));
      } else {
        ctx.fillStyle = p.collar || '#ffffff';
        ctx.beginPath();
        ctx.moveTo(cx - w * 0.2, shY - hpx * 0.005); ctx.lineTo(cx, shY + torsoH * 0.14); ctx.lineTo(cx + w * 0.2, shY - hpx * 0.005);
        ctx.lineTo(cx + w * 0.1, shY - hpx * 0.005); ctx.lineTo(cx, shY + torsoH * 0.06); ctx.lineTo(cx - w * 0.1, shY - hpx * 0.005);
        ctx.closePath(); ctx.fill();
        if (outfit === 'ref' && p.badge !== false) {
          // Federation badge on the chest
          ctx.fillStyle = '#ffd54f';
          ctx.beginPath(); ctx.arc(cx - w * 0.22, shY + torsoH * 0.28, Math.max(1, w * 0.055), 0, Math.PI * 2); ctx.fill();
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
      ctx.fillText(String(p.number), cx, shY + torsoH * 0.47);
      // Captain: a stripe under the number (Rule 19.1.4 / 5.1)
      if (p.captain) ctx.fillRect(cx - w * 0.2, shY + torsoH * 0.72, w * 0.4, Math.max(1.8, w * 0.07));
    }
    front.forEach(drawArm);

    // ---- Neck and head ----
    line([[cx, neckY - headR * 0.3], [cx, shY + hpx * 0.01]], skinDark, Math.max(1.5, w * 0.2));
    const hy = top + headR;
    ctx.fillStyle = skin;
    ctx.beginPath(); ctx.ellipse(cx, hy, headR * 0.9, headR, 0, 0, Math.PI * 2); ctx.fill();
    if (detail) {
      // Ears and a soft shadow on the right
      ctx.beginPath(); ctx.arc(cx - headR * 0.9, hy + headR * 0.1, headR * 0.2, 0, Math.PI * 2); ctx.arc(cx + headR * 0.9, hy + headR * 0.1, headR * 0.2, 0, Math.PI * 2); ctx.fill();
      ctx.fillStyle = 'rgba(0,0,0,.12)';
      ctx.beginPath(); ctx.ellipse(cx + headR * 0.35, hy + headR * 0.1, headR * 0.5, headR * 0.85, 0, -Math.PI / 2, Math.PI / 2); ctx.fill();
    }
    const hair = p.hair || (outfit === 'player' ? null : '#2b2420');
    if (hair && p.back) {
      // Seen from behind (facing away): the hair covers the back of the head
      ctx.fillStyle = hair;
      ctx.beginPath(); ctx.ellipse(cx, hy - headR * 0.05, headR * 0.93, headR * 0.98, 0, 0, Math.PI * 2); ctx.fill();
      if ((p.hairStyle || 0) === 1) { ctx.beginPath(); ctx.ellipse(cx, hy + headR * 0.9, headR * 0.3, headR * 0.55, 0, 0, Math.PI * 2); ctx.fill(); }
    } else if (hair) {
      ctx.fillStyle = hair;
      const st = p.hairStyle || 0;
      ctx.beginPath();
      ctx.ellipse(cx, hy - headR * 0.18, headR * 0.95, headR * (st === 2 ? 0.95 : 0.85), 0, Math.PI, Math.PI * 2);
      ctx.fill();
      if (st === 1) {
        // Ponytail
        ctx.beginPath(); ctx.ellipse(cx + headR * 0.95, hy + headR * 0.15, headR * 0.28, headR * 0.6, -0.4, 0, Math.PI * 2); ctx.fill();
      } else if (st === 2) {
        // Longer hair at the sides
        ctx.fillRect(cx - headR * 0.95, hy - headR * 0.2, headR * 0.3, headR * 0.9);
        ctx.fillRect(cx + headR * 0.65, hy - headR * 0.2, headR * 0.3, headR * 0.9);
      }
    }

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
    if (p.id) this.hits.push({ id: p.id, x0: c.x - r * 1.2, x1: c.x + r * 1.2, y0: c.y - r * 1.2, y1: c.y + r * 1.2 });
    if (p.highlight) {
      ctx.fillStyle = p.highlight;
      ctx.beginPath(); ctx.arc(c.x, c.y, r * 1.45, 0, Math.PI * 2); ctx.fill();
    }
    ctx.fillStyle = 'rgba(0,0,0,0.3)';
    ctx.beginPath(); ctx.arc(c.x + 1.5, c.y + 2, r, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = p.jersey || '#2f6fd0';
    ctx.beginPath(); ctx.arc(c.x, c.y, r, 0, Math.PI * 2); ctx.fill();
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
    ctx.beginPath(); ctx.moveTo(x - 5, y - 7); ctx.lineTo(x + 5, y - 7); ctx.lineTo(x, y); ctx.closePath(); ctx.fill();
    ctx.fillStyle = '#14264a';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(text, x, y - h / 2 - 6);
  };

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

  window.SimRender = { Renderer, Camera, VIEWS, V };
})();
