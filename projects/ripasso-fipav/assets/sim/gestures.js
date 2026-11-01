/* Fischietto · Match simulation: animated official signals (Rules 22.2.3 and 30.1, Indicazioni tecniche 4).
 *
 * Mirror view: the referee faces the user, like their reflection in a mirror, so the hand on the left of the
 * drawing is the one the user would raise on their left, towards the team on that side of the court (labelled
 * under the figure). A gesture made with one hand uses the hand on the side of the team concerned (Rule 30.1).
 * "L" / "R" are sides of the drawing.
 * Arms are described by two angles in degrees: the upper arm from straight down (positive = away from the body)
 * and the elbow bend added to it (positive = further outwards and up).
 */
(function () {
  'use strict';

  const SKIN = '#e9b48f', SHIRT = '#1d2533', SLEEVE = '#141a24';
  const SH = { L: [104, 86], R: [156, 86] };
  const UPPER = 44, FORE = 42;

  // ---------- Poses ----------
  const rest = () => ({ L: [8, 0, 'fist'], R: [8, 0, 'fist'] });
  /** side: 'L' or 'R' (the referee's left / right hand). */
  const one = (side, arm, other) => { const p = rest(); p[side] = arm; if (other) p[side === 'L' ? 'R' : 'L'] = other; return p; };

  const POSES = {
    rest: () => rest(),
    whistle: () => one('R', [30, -168, 'fist']),
    // Team to serve: arm stretched horizontally towards that side, palm towards the court; the other arm still.
    // Authorization to serve starts from the same position (then folds, see MOTIONS.fold).
    serve: s => one(s, [90, 0, 'palm']),
    authorize: s => one(s, [90, 0, 'palm']),
    in: s => one(s, [24, 0, 'open']),
    // Ball out: both arms bent at 90° at the elbow, forearms up, palms towards the court, fingers together
    out: () => ({ L: [90, 90, 'flat'], R: [90, 90, 'flat'] }),
    // Ball touched: like the time-out but higher, the vertical hand with its back towards the courts; the other
    // hand brushes its fingertips once, from back to front
    touched: s => (s === 'L' ? { R: [35, 147, 'flat', 1, 1.25, 180], L: [20, -152.5, 'palmDown', 0.9, 2.54, -90] }
      : { L: [35, 147, 'flat', 1, 1.25, 180], R: [20, -152.5, 'palmDown', 0.9, 2.54, -90] }),
    four: s => one(s, [26, 150, 'f4']),
    double: s => one(s, [26, 150, 'f2']),
    catch: s => one(s, [14, 70, 'palmUp']),
    eight: () => ({ L: [26, 150, 'f4'], R: [26, 150, 'f4'] }),
    // Net: arm straight at shoulder height towards the scorer (the viewer), open hand continuing the arm:
    // the arm is seen end-on, with the fingertips pointing at the viewer
    net: s => one(s, [76, 0, 'tips', 0.34, 0.3]),
    line: s => one(s, [30, 0, 'point']),
    position: s => one(s, [30, 125, 'point']),
    // Faulty attack hit: starts from the arm stretched straight up, open hand (see MOTIONS.spike)
    attack: s => one(s, [175, 0, 'open']),
    // Double fault: arms straight towards the scorer (towards the viewer), thumbs up at shoulder height
    doubleFault: () => ({ L: [62, 14, 'thumb', 0.42, 0.4], R: [62, 14, 'thumb', 0.42, 0.4] }),
    // Time-out: one hand vertical, palm facing inwards (seen edge-on); the other one palm down on top of it (T)
    timeout: () => ({ R: [-15, 195, 'blade', 0.85, 0.7, 180], L: [40, -165.5, 'palmDown', 0.6, 1.43, -90] }),
    sub: () => ({ L: [26, -100, 'fist'], R: [26, -100, 'fist'] }),
    // Improper request: arm stretched towards the team like the service signal, but lower than the shoulder,
    // with the hand turned the opposite way (palm outwards, back towards the court)
    improper: s => one(s, [52, 0, 'back']),
    // Reaching beyond the net: forearm horizontal in front of the chest, the hand about in the middle, palm down
    reach: s => one(s, [30, -139, 'palmDown', 0.9, 0.9, -90]),
    // Blocking fault: both arms raised vertically, palms forward
    blockFault: () => ({ L: [174, 0, 'palm', 1, 0.8], R: [174, 0, 'palm', 1, 0.8] }),
    delayY: s => one(s, [26, 150, 'open'], [34, -118, 'cardY']),
    delayR: s => one(s, [26, 150, 'open'], [34, -118, 'cardR']),
    yellow: s => one(s, [24, 156, 'cardY']),
    red: s => one(s, [24, 156, 'cardR']),
    expulsion: s => one(s, [24, 156, 'cardYR']),
    disqualification: () => ({ L: [24, 156, 'cardY'], R: [24, 156, 'cardR'] }),
  };

  /** Movements on top of a pose: return angle changes for time u (0..1, repeating) */
  const MOTIONS = {
    circle: (p, side, u) => { p[side] = [p[side][0] + Math.sin(u * Math.PI * 4) * 6, p[side][1] + Math.cos(u * Math.PI * 4) * 12, p[side][2]]; },
    // Faulty attack hit: in a single movement the raised arm bends and "falls" towards the other side of the
    // body, ending over the head, horizontal and roughly centred, palm down
    spike: (p, side, u) => {
      const k0 = Math.max(0, Math.min(1, (u - 0.1) / 0.6)), k = k0 * k0 * (3 - 2 * k0);
      p[side] = [175, 70 * k, k < 0.55 ? 'open' : 'palmDown', 1, 1 - 0.25 * k];
    },
    down: (p, side, u) => { const k = Math.min(1, u * 1.6); p[side] = [p[side][0], 158 - 118 * k, p[side][2]]; },
    lift: (p, side, u) => { const k = Math.min(1, u * 1.3); p[side] = [p[side][0] + 10 * k, 60 + 80 * k, p[side][2]]; },
    // One pass of the flat hand over the fingertips, from behind (smaller, higher) to the front (bigger, lower)
    brush: (p, side, u) => {
      const k0 = Math.max(0, Math.min(1, (u - 0.1) / 0.55)), k = k0 * k0 * (3 - 2 * k0);
      const a = p[side];
      p[side] = [a[0], a[1], k < 0.5 ? 'palmDownBack' : 'palmDown', a[3], a[4] * (0.9 + 0.16 * k), a[5]];
    },
    roll: (p, side, u) => { const a = Math.min(1, u) * Math.PI * 2; p.L = [26 + Math.sin(a) * 10, -100 + Math.cos(a) * 18, 'fist']; p.R = [26 - Math.sin(a) * 10, -100 - Math.cos(a) * 18, 'fist']; },
    // Authorization to serve: after the whistle the extended arm bends and the hand goes to the opposite shoulder
    // (a single rotation of the arm, no other movement)
    fold: (p, side, u) => {
      const k0 = Math.max(0, Math.min(1, (u - 0.12) / 0.6)), k = k0 * k0 * (3 - 2 * k0);
      // The upper arm swings forward (shorter on screen), the forearm turns in the horizontal plane: first
      // towards the viewer (shortest), then across the chest to the opposite shoulder, back of the hand outwards
      const f = Math.cos(Math.PI * k);
      p[side] = [90, f >= 0 ? 0 : -180, k < 0.5 ? 'palm' : 'flat', 1 - 0.74 * k, Math.max(0.05, Math.abs(f) * (f >= 0 ? 1 : 1.02)), f >= 0 ? 90 : -90];
    },
    swing: (p, side, u) => { const k = Math.min(1, u * 2); p[side] = [40 + 48 * k, 0, 'open']; },
  };

  // ---------- Drawing ----------
  function dir(side, deg) {
    const r = deg * Math.PI / 180, s = side === 'L' ? -1 : 1;
    return [s * Math.sin(r), Math.cos(r)];
  }

  function armSvg(side, arm) {
    const [a, b, hand] = arm;
    const sh = SH[side];
    // Optional 4th / 5th values: visible length of upper arm and forearm (foreshortening when they point
    // towards the viewer); 6th: hand direction in degrees, when it differs from the forearm's
    const su = arm[3] == null ? 1 : arm[3], sf = arm[4] == null ? 1 : arm[4];
    const d1 = dir(side, a), d2 = dir(side, a + b);
    const el = [sh[0] + d1[0] * UPPER * su, sh[1] + d1[1] * UPPER * su];
    const wr = [el[0] + d2[0] * FORE * sf, el[1] + d2[1] * FORE * sf];
    const dh = arm[5] == null ? d2 : dir(side, arm[5]);
    const mid = [sh[0] + d1[0] * UPPER * 0.42, sh[1] + d1[1] * UPPER * 0.42];
    let s = '<line x1="' + sh[0] + '" y1="' + sh[1] + '" x2="' + el[0].toFixed(1) + '" y2="' + el[1].toFixed(1) + '" stroke="' + SKIN + '" stroke-width="13" stroke-linecap="round"/>' +
      '<line x1="' + sh[0] + '" y1="' + sh[1] + '" x2="' + mid[0].toFixed(1) + '" y2="' + mid[1].toFixed(1) + '" stroke="' + SLEEVE + '" stroke-width="17" stroke-linecap="round"/>' +
      '<line x1="' + el[0].toFixed(1) + '" y1="' + el[1].toFixed(1) + '" x2="' + wr[0].toFixed(1) + '" y2="' + wr[1].toFixed(1) + '" stroke="' + SKIN + '" stroke-width="11" stroke-linecap="round"/>';
    s += handSvg(wr, dh, hand);
    return s;
  }

  function fingers(c, d, n, spread) {
    let s = '';
    const base = Math.atan2(d[1], d[0]);
    for (let i = 0; i < n; i++) {
      const ang = base + (i - (n - 1) / 2) * spread;
      const x = c[0] + Math.cos(ang) * 15, y = c[1] + Math.sin(ang) * 15;
      s += '<line x1="' + c[0].toFixed(1) + '" y1="' + c[1].toFixed(1) + '" x2="' + x.toFixed(1) + '" y2="' + y.toFixed(1) + '" stroke="' + SKIN + '" stroke-width="3.6" stroke-linecap="round"/>';
    }
    return s;
  }

  function card(c, d, color, dx) {
    const x = c[0] + d[0] * 10 + (dx || 0), y = c[1] + d[1] * 10;
    return '<rect x="' + (x - 7).toFixed(1) + '" y="' + (y - 11).toFixed(1) + '" width="14" height="20" rx="2" fill="' + color + '" stroke="rgba(0,0,0,.25)"/>';
  }

  function handSvg(c, d, hand) {
    const dot = r => '<circle cx="' + c[0].toFixed(1) + '" cy="' + c[1].toFixed(1) + '" r="' + r + '" fill="' + SKIN + '"/>';
    switch (hand) {
      case 'open': return dot(8) + fingers(c, d, 4, 0.28);
      case 'palmBody': return dot(9) + fingers(c, d, 4, 0.18);
      // Hand seen edge-on (palm facing sideways)
      case 'blade': return '<line x1="' + c[0].toFixed(1) + '" y1="' + c[1].toFixed(1) + '" x2="' + (c[0] + d[0] * 20).toFixed(1) + '" y2="' + (c[1] + d[1] * 20).toFixed(1) + '" stroke="' + SKIN + '" stroke-width="9" stroke="rgba(0,0,0,0)" stroke-linecap="round"/>' + dot(6);
      // Flat hand, palm down, seen from the front: a thin horizontal plate
      case 'palmDown': return '<ellipse cx="' + (c[0] + d[0] * 10).toFixed(1) + '" cy="' + (c[1] + d[1] * 10).toFixed(1) + '" rx="14" ry="4.5" fill="' + SKIN + '" stroke="rgba(120,70,40,.35)" stroke-width=".8"/>';
      case 'palmDownBack': return '<ellipse cx="' + (c[0] + d[0] * 10).toFixed(1) + '" cy="' + (c[1] + d[1] * 10).toFixed(1) + '" rx="11" ry="3.5" fill="#d79c78"/>';
      // Open hand pointing at the viewer: fingertips seen end-on
      case 'tips': return '<ellipse cx="' + c[0].toFixed(1) + '" cy="' + c[1].toFixed(1) + '" rx="9" ry="10" fill="' + SKIN + '" stroke="rgba(120,70,40,.35)" stroke-width=".8"/>' +
        [-5.4, -1.8, 1.8, 5.4].map(dx => '<circle cx="' + (c[0] + dx).toFixed(1) + '" cy="' + (c[1] - 3).toFixed(1) + '" r="1.9" fill="#f5cfb2" stroke="rgba(120,70,40,.4)" stroke-width=".6"/>').join('');
      // Open hand showing its back (palm turned away), knuckles marked
      case 'back': return dot(9) + fingers(c, d, 4, 0.2) + '<path d="M' + (c[0] - 5).toFixed(1) + ' ' + (c[1] + 1).toFixed(1) + 'h10" stroke="rgba(120,70,40,.45)" stroke-width="1.4" stroke-linecap="round" transform="rotate(' + (Math.atan2(d[1], d[0]) * 180 / Math.PI - 90).toFixed(1) + ' ' + c[0].toFixed(1) + ' ' + c[1].toFixed(1) + ')"/>';
      case 'palm': return dot(9) + fingers(c, d, 4, 0.2) + '<circle cx="' + c[0].toFixed(1) + '" cy="' + c[1].toFixed(1) + '" r="4" fill="#f5cfb2"/>';
      // Five fingers together: a single flat blade
      case 'flat': return dot(9) + '<line x1="' + c[0].toFixed(1) + '" y1="' + c[1].toFixed(1) + '" x2="' + (c[0] + d[0] * 17).toFixed(1) + '" y2="' + (c[1] + d[1] * 17).toFixed(1) + '" stroke="' + SKIN + '" stroke-width="11" stroke-linecap="round"/>';
      case 'palmUp': return '<ellipse cx="' + c[0].toFixed(1) + '" cy="' + c[1].toFixed(1) + '" rx="11" ry="6" fill="' + SKIN + '"/>';
      case 'palmBack': return dot(9) + fingers(c, d, 4, 0.22);
      case 'point': return dot(7) + fingers(c, d, 1, 0);
      case 'f2': return dot(7) + fingers(c, d, 2, 0.42);
      case 'f4': return dot(8) + fingers(c, d, 4, 0.34);
      case 'thumb': return dot(8) + fingers(c, [0, -1], 1, 0);
      case 'cardY': return dot(7) + card(c, d, '#ffd400');
      case 'cardR': return dot(7) + card(c, d, '#e53935');
      case 'cardYR': return dot(7) + card(c, d, '#ffd400', -6) + card(c, d, '#e53935', 6);
      default: return dot(8);
    }
  }

  function figureSvg(pose, opts) {
    const o = opts || {};
    const sides = o.sides || { left: 'A', right: 'B' };
    const tc = { A: '#2f6fd0', B: '#e2552b' };
    let svg = '<svg viewBox="-24 -14 308 266" width="100%" height="100%" aria-hidden="true" preserveAspectRatio="xMidYMid meet">';
    svg += '<ellipse cx="130" cy="238" rx="62" ry="8" fill="rgba(0,0,0,.18)"/>';
    // Team on each side of the user (the referee faces them, like a mirror)
    svg += '<text x="-18" y="246" font-family="Inter,sans-serif" font-size="12" font-weight="700" fill="' + tc[sides.left] + '">← Squadra ' + sides.left + '</text>' +
      '<text x="278" y="246" text-anchor="end" font-family="Inter,sans-serif" font-size="12" font-weight="700" fill="' + tc[sides.right] + '">Squadra ' + sides.right + ' →</text>';
    // Legs and body, facing the user
    svg += '<rect x="108" y="168" width="18" height="64" rx="8" fill="#14181f"/><rect x="134" y="168" width="18" height="64" rx="8" fill="#14181f"/>';
    svg += '<rect x="98" y="76" width="64" height="100" rx="20" fill="' + SHIRT + '"/>';
    svg += '<path d="M118 77l12 14 12-14" fill="none" stroke="#3a4558" stroke-width="3" stroke-linejoin="round"/>';
    svg += '<rect x="122" y="66" width="16" height="14" rx="5" fill="' + SKIN + '"/>';
    svg += '<circle cx="130" cy="50" r="20" fill="' + SKIN + '"/><path d="M110 46a20 20 0 0 1 40 0q-6-9-20-9t-20 9z" fill="#2b211b"/>';
    svg += '<circle cx="123" cy="50" r="2" fill="#2b211b"/><circle cx="137" cy="50" r="2" fill="#2b211b"/>';
    // Whistle on a lanyard
    svg += '<path d="M122 70q8 22 16 0" fill="none" stroke="#3a4558" stroke-width="1.5"/><rect x="126" y="86" width="9" height="6" rx="2.5" fill="#c9ced8"/>';
    // Arms in front of the body when raised, the body covering them when down
    svg += armSvg('L', pose.L) + armSvg('R', pose.R);
    if (o.whistleOn) svg += '<rect x="125" y="58" width="10" height="6" rx="2.5" fill="#c9ced8"/>';
    if (o.whistleOn) svg += '<g fill="none" stroke="#ffd54f" stroke-width="3" stroke-linecap="round"><path d="M164 30l12-8M168 42l15 0M164 54l12 8"/></g>';
    svg += '</svg>';
    return svg;
  }

  // ---------- Player ----------
  /**
   * Plays a sequence of steps in `box`: [{ pose, side, motion, caption, dur, whistle }].
   * The caption of the current step is written in `captionEl`; the step list (if any) is highlighted.
   */
  function play(box, steps, captionEl, listEl, sides) {
    if (box._raf) cancelAnimationFrame(box._raf);
    let i = 0, t0 = performance.now(), from = rest(), cur = rest();
    const num = (v, d) => v == null ? d : v;
    const lerpArm = (a, b, k) => {
      const r = [a[0] + (b[0] - a[0]) * k, a[1] + (b[1] - a[1]) * k, k < 0.5 ? a[2] : b[2],
        num(a[3], 1) + (num(b[3], 1) - num(a[3], 1)) * k, num(a[4], 1) + (num(b[4], 1) - num(a[4], 1)) * k];
      if (a[5] != null || b[5] != null) r[5] = num(a[5], a[0] + a[1]) + (num(b[5], b[0] + b[1]) - num(a[5], a[0] + a[1])) * k;
      return r;
    };
    const show = () => {
      if (captionEl) captionEl.textContent = steps[i] ? steps[i].caption : '';
      if (listEl && steps[i]) listEl.querySelectorAll('li').forEach(li => li.classList.toggle('on', li.textContent === steps[i].caption));
    };
    show();
    const tick = now => {
      const st = steps[i];
      if (!st) return;
      const el = (now - t0) / 1000;
      const target = POSES[st.pose] ? POSES[st.pose](st.side) : rest();
      const k = Math.min(1, el / 0.35);
      const e = k * k * (3 - 2 * k);
      const p = { L: lerpArm(from.L, target.L, e), R: lerpArm(from.R, target.R, e) };
      if (st.motion && k >= 1 && MOTIONS[st.motion]) {
        const u = (el - 0.35) / Math.max(0.5, (st.dur || 1.6) - 0.35);
        MOTIONS[st.motion](p, st.side || 'R', st.once ? Math.min(1, u) : u % 1);
      }
      cur = p;
      box.innerHTML = figureSvg(p, { sides, whistleOn: (st.whistle || st.blow) && el < 0.6 });
      if (el >= (st.dur || 1.6)) {
        from = cur; i++; t0 = now; show();
        if (i >= steps.length) { box.innerHTML = figureSvg(cur, { sides }); return; }
        if ((steps[i].whistle || steps[i].blow) && window.SimSound) window.SimSound();
      }
      box._raf = requestAnimationFrame(tick);
    };
    if (steps[0] && (steps[0].whistle || steps[0].blow) && window.SimSound) window.SimSound();
    box._raf = requestAnimationFrame(tick);
  }

  function stop(box) { if (box && box._raf) cancelAnimationFrame(box._raf); }

  /** Steps of the authorization to serve towards the team on side s ('L' / 'R'). */
  function authorizeSteps(s, prefix) {
    return [
      { pose: 'serve', side: s, caption: (prefix || '') + 'Braccio teso orizzontalmente verso la squadra al servizio, palmo verso il campo; l’altro braccio fermo lungo il fianco', dur: 1.6 },
      { pose: 'serve', side: s, blow: true, caption: 'Fischio, con il braccio ancora teso', dur: 1.1 },
      { pose: 'authorize', side: s, motion: 'fold', once: true, caption: 'Piega il braccio portando la mano sulla spalla opposta', dur: 1.7 },
    ];
  }

  window.SimGestures = { play, stop, POSES, authorizeSteps };
})();
