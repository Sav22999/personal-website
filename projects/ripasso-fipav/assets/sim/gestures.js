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
    // Referees always keep the whistle in their mouth: whistling needs no gesture
    whistle: () => rest(),
    // Team to serve: arm stretched horizontally towards that side, palm towards the court; the other arm still.
    // Authorization to serve starts from the same position (then folds, see MOTIONS.fold).
    serve: s => one(s, [90, 0, 'palm']),
    authorize: s => one(s, [90, 0, 'palm']),
    in: s => one(s, [24, 0, 'open']),
    // Ball out: both arms bent at 90° at the elbow, forearms up, palms towards the court, fingers together
    out: () => ({ L: [90, 90, 'flat'], R: [90, 90, 'flat'] }),
    // Ball touched: like the time-out but higher, the vertical hand with its back towards the courts; the other
    // hand brushes its fingertips once, from close to the body to away from it (forwards)
    // The vertical hand is on the side of the team that touched the ball (s); the other hand brushes it
    touched: s => (s === 'L' ? { L: [35, 147, 'flat', 1, 1.25, 180], R: [20, -156, 'palmDown', 0.9, 2.69, -90] }
      : { R: [35, 147, 'flat', 1, 1.25, 180], L: [20, -156, 'palmDown', 0.9, 2.69, -90] }),
    four: s => one(s, [26, 150, 'f4']),
    double: s => one(s, [26, 150, 'f2']),
    catch: s => one(s, [14, 70, 'palmUp']),
    eight: () => ({ L: [26, 150, 'f4'], R: [26, 150, 'f4'] }),
    // Net: arm straight at shoulder height towards the scorer (the viewer), open hand continuing the arm:
    // the arm is seen end-on, with the fingertips pointing at the viewer
    net: s => one(s, [76, 0, 'tipsV', 0.34, 0.3]),
    // Informal help of the 2nd referee to the 1st: small signals at mid-chest
    chest4: s => one(s, [10, -136, 'f4', 1, 0.7, 180]),
    // Request for the game captain: the index finger touches the chest at the height of the number (the
    // captain's stripe), in a single movement (see MOTIONS.tapChest)
    askCap: s => one(s, [12, -136, 'point', 1, 0.35, -100]),
    chest2: s => one(s, [10, -136, 'f2', 1, 0.7, 180]),
    chestCatch: s => one(s, [10, -136, 'palmUp', 1, 0.7, 180]),
    // Ball touched (collaboration): the hands touch lightly at the fingertips like a roof, palms towards the chest
    chestTouched: () => ({ L: [15, -140.6, 'flat', 0.9, 0.56, -132], R: [15, -140.6, 'flat', 0.9, 0.56, -132] }),
    line: s => one(s, [30, 0, 'point']),
    // Positional / rotation fault: index finger pointing to the floor, turning in a circle
    position: s => one(s, [40, -60, 'point', 0.9, 0.7, 0]),
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
    // Delay sanction (Indicazioni tecniche 4.5): the arm on the side of the sanctioned team raised, slightly bent
    // towards the middle, back of the hand towards the scorer; the other hand brings the card onto its wrist
    delayY: s => one(s, [165, 50, 'back'], [-160, 16.3, 'cardY', 1, 0.56]),
    delayR: s => one(s, [165, 50, 'back'], [-160, 16.3, 'cardR', 1, 0.56]),
    yellow: s => one(s, [24, 156, 'cardY']),
    red: s => one(s, [24, 156, 'cardR']),
    expulsion: s => one(s, [24, 156, 'cardYR']),
    disqualification: () => ({ L: [24, 156, 'cardY'], R: [24, 156, 'cardR'] }),
  };

  /** Movements on top of a pose: return angle changes for time u (0..1, repeating) */
  const MOTIONS = {
    circle: (p, side, u) => { const q = p[side]; p[side] = [q[0] + Math.sin(u * Math.PI * 4) * 6, q[1] + Math.cos(u * Math.PI * 4) * 12, q[2], q[3], q[4], q[5]]; },
    // Faulty attack hit: in a single movement the raised arm bends and "falls" towards the other side of the
    // body, ending over the head, horizontal and roughly centred, palm down
    spike: (p, side, u) => {
      const k0 = Math.max(0, Math.min(1, (u - 0.1) / 0.6)), k = k0 * k0 * (3 - 2 * k0);
      p[side] = [175, 70 * k, k < 0.55 ? 'open' : 'palmDown', 1, 1 - 0.25 * k];
    },
    down: (p, side, u) => { const k = Math.min(1, u * 1.6); p[side] = [p[side][0], 158 - 118 * k, p[side][2]]; },
    lift: (p, side, u) => { const k = Math.min(1, u * 1.3); p[side] = [p[side][0] + 10 * k, 60 + 80 * k, p[side][2]]; },
    // One pass of the flat hand over the fingertips, from behind (smaller, higher) to the front (bigger, lower)
    tapChest: (p, side, u) => {
      const k0 = Math.max(0, Math.min(1, (u - 0.1) / 0.45)), k = k0 * k0 * (3 - 2 * k0);
      const a = p[side];
      p[side] = [a[0], a[1], 'point', a[3], 0.35 + 0.4 * k, -100];
    },
    brush: (p, side, u) => {
      const k0 = Math.max(0, Math.min(1, (u - 0.1) / 0.55)), k = k0 * k0 * (3 - 2 * k0);
      // The brushing hand is the one opposite to the side of the signal
      const b = side === 'L' ? 'R' : 'L';
      const a = p[b];
      // Corrected by a referee: the hand goes from close to the body to away from it (forwards), passing over
      // the fingertips of the vertical hand. Seen from the front this is depth, not a sideways slide: the hand
      // stays over the fingertips, starts small and behind them, and grows as it comes towards the viewer.
      // 7th value: scale of the hand (depth)
      p[b] = [a[0], a[1], k < 0.45 ? 'palmDownBack' : 'palmDown', a[3], a[4] * (1.0 - 0.04 * k), a[5], 0.72 + 0.5 * k];
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
    s += handSvg(wr, dh, hand, side, arm[6]);
    return s;
  }

  // ---------- Hands ----------
  // Drawn in a local frame: wrist at (0, 0), fingers pointing up (-y); then rotated along the forearm.
  const PALM = '#f3c6a5', OUT = 'rgba(120,70,40,.45)';
  const f1 = v => v.toFixed(1);
  /** A finger (or thumb) as a capsule from (x, y), `len` long, at `deg` degrees from straight up. */
  function capsule(x, y, len, wid, deg, fill) {
    return '<rect x="' + f1(x - wid / 2) + '" y="' + f1(y - len) + '" width="' + f1(wid) + '" height="' + f1(len + wid / 2) + '" rx="' + f1(wid / 2) +
      '" fill="' + fill + '" stroke="' + OUT + '" stroke-width=".7" transform="rotate(' + f1(deg) + ' ' + f1(x) + ' ' + f1(y) + ')"/>';
  }
  const palmPath = fill => '<path d="M-6.2 -1.5 Q-6.8 -9 -5.8 -13.2 Q0 -15 5.8 -13.2 Q6.8 -9 6.2 -1.5 Q0 2 -6.2 -1.5Z" fill="' + fill + '" stroke="' + OUT + '" stroke-width=".7"/>';
  const LENS = [8.6, 10.4, 9.8, 7.8], XS = [-4.6, -1.55, 1.55, 4.6];
  function fourFingers(spread, fill, lens) {
    return XS.map((x, i) => capsule(x, -12.5, (lens || LENS)[i], 3.3, (i - 1.5) * spread, fill)).join('');
  }
  /** in = +1 when the thumb is on the right of the local frame, -1 on the left. */
  const thumbOut = (inn, fill) => capsule(inn * 5.6, -4.5, 7.5, 3.6, inn * 52, fill);
  const fist = fill => '<rect x="-6.5" y="-13" width="13" height="13.5" rx="5" fill="' + fill + '" stroke="' + OUT + '" stroke-width=".7"/>' +
    '<path d="M-5 -9.6h10M-5 -6.4h10M-5 -3.2h10" stroke="' + OUT + '" stroke-width=".6"/>';
  const card = (color, dx) => '<rect x="' + f1(-7 + (dx || 0)) + '" y="-34" width="14" height="20" rx="2" fill="' + color + '" stroke="rgba(0,0,0,.25)"/>';

  /**
   * side: the arm ('L' / 'R' of the drawing). The figure faces the viewer, so with the palm visible the thumb
   * points towards the middle of the body, with the back visible it points outwards.
   */
  function handSvg(c, d, hand, side, scale) {
    const out = side === 'L' ? -1 : 1;     // outwards, in drawing x
    let deg = Math.atan2(d[1], d[0]) * 180 / Math.PI + 90;
    // Thumb side in the local frame for a palm facing the viewer (towards the body's middle)
    const rad = deg * Math.PI / 180;
    const localX = [Math.cos(rad), Math.sin(rad)];  // local +x in drawing space
    const inn = (localX[0] * -out) >= 0 ? 1 : -1;
    let g = '';
    switch (hand) {
      case 'open': g = fourFingers(9, SKIN) + thumbOut(-inn, SKIN) + palmPath(SKIN); break;
      case 'palm': case 'palmBody':
        g = fourFingers(7, SKIN) + thumbOut(inn, SKIN) + palmPath(PALM) + '<path d="M-4 -6 Q0 -3.5 4 -7" stroke="' + OUT + '" fill="none" stroke-width=".7"/>'; break;
      case 'back': case 'palmBack':
        g = fourFingers(6, SKIN) + thumbOut(-inn, SKIN) + palmPath(SKIN) + XS.map(x => '<circle cx="' + x + '" cy="-12.3" r=".9" fill="' + OUT + '"/>').join(''); break;
      // Five fingers together
      case 'flat':
        g = '<rect x="-6" y="-24" width="12" height="13" rx="4.5" fill="' + SKIN + '" stroke="' + OUT + '" stroke-width=".7"/>' +
          '<path d="M-3 -23v10M0 -23.6v10.6M3 -23v10" stroke="' + OUT + '" stroke-width=".5"/>' + palmPath(SKIN) +
          capsule(-inn * 6, -4, 7, 3.4, -inn * 18, SKIN); break;
      case 'point': g = fist(SKIN) + capsule(-inn * 3.2, -12, 11, 3.4, 0, SKIN); break;
      case 'f2': g = fist(SKIN) + capsule(-2, -12, 10.5, 3.4, -14, SKIN) + capsule(2, -12, 10.5, 3.4, 14, SKIN); break;
      case 'f4': g = fourFingers(13, SKIN, [9.5, 11, 10.5, 8.5]) + palmPath(SKIN) + '<path d="M' + (inn * 5) + ' -3 Q0 -6 ' + (-inn * 3) + ' -8" stroke="' + SKIN + '" stroke-width="3.4" stroke-linecap="round" fill="none"/>'; break;
      case 'thumb':
        // Fist held sideways, thumb up: drawn upright whatever the forearm
        deg = 0;
        g = '<rect x="-7" y="-11" width="14" height="12" rx="5" fill="' + SKIN + '" stroke="' + OUT + '" stroke-width=".7"/>' +
          '<path d="M-5.5 -7.5h11M-5.5 -4.5h11M-5.5 -1.5h11" stroke="' + OUT + '" stroke-width=".6"/>' + capsule(0, -10, 9, 4, 0, SKIN); break;
      case 'fist': g = fist(SKIN) + capsule(inn * 4.5, -6, 6, 3.6, -inn * 70, SKIN); break;
      // Hand seen edge-on (palm facing sideways)
      case 'blade':
        g = '<rect x="-2.8" y="-22" width="5.6" height="23" rx="2.8" fill="' + SKIN + '" stroke="' + OUT + '" stroke-width=".7"/>' + capsule(0, -5, 6, 3.4, -inn * 30, SKIN); break;
      case 'cardY': g = fist(SKIN) + card('#ffd400'); break;
      case 'cardR': g = fist(SKIN) + card('#e53935'); break;
      case 'cardYR': g = fist(SKIN) + card('#ffd400', -6) + card('#e53935', 6); break;
      // Horizontal hand, palm down, seen from the front: a plate with fingertips
      case 'palmDown': case 'palmDownBack': {
        deg = 0;
        const cx = d[0] * 10, cy = d[1] * 10, dark = hand === 'palmDownBack';
        const fill = dark ? '#d79c78' : SKIN, sc = dark ? 0.8 : 1;
        g = '<ellipse cx="' + f1(cx) + '" cy="' + f1(cy) + '" rx="' + f1(14 * sc) + '" ry="' + f1(4.6 * sc) + '" fill="' + fill + '" stroke="' + OUT + '" stroke-width=".7"/>' +
          (dark ? '' : [-1, 0, 1].map(k => '<path d="M' + f1(cx + Math.sign(d[0] || 1) * 7 + k * 0) + ' ' + f1(cy - 1.6 + k * 1.6) + 'h' + f1(Math.sign(d[0] || 1) * 6) + '" stroke="' + OUT + '" stroke-width=".5"/>').join(''));
        break;
      }
      // Open hand pointing at the viewer: fingertips seen end-on
      case 'tips':
        deg = 0;
        g = '<ellipse cx="0" cy="0" rx="9" ry="10" fill="' + SKIN + '" stroke="' + OUT + '" stroke-width=".7"/>' +
          [-5.4, -1.8, 1.8, 5.4].map(dx => '<circle cx="' + dx + '" cy="-3" r="1.9" fill="' + PALM + '" stroke="' + OUT + '" stroke-width=".6"/>').join('') +
          '<circle cx="' + (inn * 7.5) + '" cy="3" r="2.2" fill="' + PALM + '" stroke="' + OUT + '" stroke-width=".6"/>'; break;
      // Open hand pointing at the viewer, palm facing the net (inwards): seen end-on, vertical
      case 'tipsV':
        deg = 0;
        g = '<ellipse cx="0" cy="0" rx="4.8" ry="10.5" fill="' + SKIN + '" stroke="' + OUT + '" stroke-width=".7"/>' +
          '<path d="M' + f1(inn * 1.2) + ' -8 Q' + f1(inn * 4.6) + ' 0 ' + f1(inn * 1.2) + ' 8" stroke="' + PALM + '" stroke-width="2.4" fill="none" stroke-linecap="round"/>' +
          [-6, -2, 2, 6].map(dy => '<circle cx="' + f1(-inn * 0.8) + '" cy="' + dy + '" r="1.7" fill="' + PALM + '" stroke="' + OUT + '" stroke-width=".5"/>').join('') +
          '<circle cx="' + f1(-inn * 1) + '" cy="-11" r="2" fill="' + PALM + '" stroke="' + OUT + '" stroke-width=".5"/>'; break;
      case 'palmUp':
        deg = 0;
        g = '<ellipse cx="0" cy="0" rx="11" ry="5.5" fill="' + PALM + '" stroke="' + OUT + '" stroke-width=".7"/><path d="M-6 -1 Q0 2 6 -1" stroke="' + OUT + '" fill="none" stroke-width=".6"/>'; break;
      default: g = fist(SKIN);
    }
    return '<g transform="translate(' + f1(c[0]) + ' ' + f1(c[1]) + ') rotate(' + f1(deg) + ') scale(' + (1.25 * (scale || 1)).toFixed(3) + ')">' + g + '</g>';
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
    // Whistle on a lanyard, always in the mouth
    svg += '<path d="M122 70q8 16 16 0" fill="none" stroke="#3a4558" stroke-width="1.5"/><rect x="125" y="58" width="10" height="6" rx="2.5" fill="#c9ced8"/>';
    // Arms in front of the body when raised, the body covering them when down
    // A hand coming towards the viewer (palm down, in front) is drawn over the other arm
    svg += pose.L && pose.L[2] === 'palmDown' ? armSvg('R', pose.R) + armSvg('L', pose.L) : armSvg('L', pose.L) + armSvg('R', pose.R);
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

  // ---------- Live signals on the referees in the 3D scene ----------
  const numOr = (v, d) => v == null ? d : v;
  function lerpArmL(a, b, k) {
    const r = [a[0] + (b[0] - a[0]) * k, a[1] + (b[1] - a[1]) * k, k < 0.5 ? a[2] : b[2],
      numOr(a[3], 1) + (numOr(b[3], 1) - numOr(a[3], 1)) * k, numOr(a[4], 1) + (numOr(b[4], 1) - numOr(a[4], 1)) * k];
    if (a[5] != null || b[5] != null) r[5] = numOr(a[5], a[0] + a[1]) + (numOr(b[5], b[0] + b[1]) - numOr(a[5], a[0] + a[1])) * k;
    return r;
  }
  /** Arms of a sequence of steps `el` seconds after its start (same timing as play()). */
  function sampleArms(steps, el) {
    let from = rest(), t = el;
    for (let i = 0; i < steps.length; i++) {
      const st = steps[i], d = st.dur || 1.6;
      const target = POSES[st.pose] ? POSES[st.pose](st.side) : rest();
      if (t < d || i === steps.length - 1) {
        const lt = Math.min(t, d), k = Math.min(1, lt / 0.35), e = k * k * (3 - 2 * k);
        const p = { L: lerpArmL(from.L, target.L, e), R: lerpArmL(from.R, target.R, e) };
        if (st.motion && k >= 1 && MOTIONS[st.motion]) {
          const u = (lt - 0.35) / Math.max(0.5, d - 0.35);
          MOTIONS[st.motion](p, st.side || 'R', st.once ? Math.min(1, u) : u % 1);
        }
        return { arms: p, done: t >= d, whistle: !!(st.whistle || st.blow) && lt < 0.6 };
      }
      const p = { L: target.L.slice(), R: target.R.slice() };
      if (st.motion && st.once && MOTIONS[st.motion]) MOTIONS[st.motion](p, st.side || 'R', 1);
      from = p; t -= d;
    }
    return { arms: rest(), done: true, whistle: false };
  }
  /**
   * Wrist position (drawing units, figure 260 wide, head top at y = 30, feet at y = 232), hand shape and the
   * direction the hand points to (hd: unit vector in drawing units, as in the signal drawings).
   */
  function wristOf(side, arm) {
    const [a, b, hand] = arm;
    const sh = SH[side];
    const su = arm[3] == null ? 1 : arm[3], sf = arm[4] == null ? 1 : arm[4];
    const d1 = dir(side, a), d2 = dir(side, a + b);
    const el = [sh[0] + d1[0] * UPPER * su, sh[1] + d1[1] * UPPER * su];
    return { x: el[0] + d2[0] * FORE * sf, y: el[1] + d2[1] * FORE * sf, ex: el[0], ey: el[1], hand, hd: arm[5] == null ? d2 : dir(side, arm[5]) };
  }
  /** For the 3D renderer: where the two hands of a referee are while making the signal. */
  function liveArms(steps, el) {
    const s = sampleArms(steps, el);
    return { L: wristOf('L', s.arms.L), R: wristOf('R', s.arms.R), done: s.done, whistle: s.whistle };
  }

  /** Draws the signal as it is `el` seconds after its start (replay); returns the caption of that step. */
  function frame(box, steps, el, sides) {
    const s = sampleArms(steps, Math.max(0, el));
    box.innerHTML = figureSvg(s.arms, { sides, whistleOn: s.whistle });
    let t = Math.max(0, el);
    for (let i = 0; i < steps.length; i++) { const d = steps[i].dur || 1.6; if (t < d || i === steps.length - 1) return steps[i].caption || ''; t -= d; }
    return '';
  }

  /** Steps of the authorization to serve towards the team on side s ('L' / 'R'). */
  function authorizeSteps(s, prefix) {
    return [
      { pose: 'serve', side: s, caption: (prefix || '') + 'Braccio teso orizzontalmente verso la squadra al servizio, palmo verso il campo; l’altro braccio fermo lungo il fianco', dur: 1.6 },
      { pose: 'serve', side: s, blow: true, caption: 'Fischio, con il braccio ancora teso', dur: 1.1 },
      { pose: 'authorize', side: s, motion: 'fold', once: true, caption: 'Piega il braccio portando la mano sulla spalla opposta', dur: 1.7 },
    ];
  }

  window.SimGestures = { play, stop, POSES, authorizeSteps, liveArms, frame };
})();
