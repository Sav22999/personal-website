/* Fischietto · Match simulation: game engine (no DOM).
 *
 * Keeps the match state (score, rotations, score sheet, sanctions), builds each rally as a timeline
 * (players' keyframes, ball flight, contacts) with situations from the rulebook injected into it,
 * and evaluates the referee's decisions against what really happened.
 *
 * Coordinates: see render3d.js. Each team works in local coordinates (d = distance from the centre line,
 * l = lateral position, positive towards the team's own left); w(team, d, l) converts to world.
 */
(function () {
  'use strict';

  const rnd = (a, b) => a + Math.random() * (b - a);
  const pick = arr => arr[Math.floor(Math.random() * arr.length)];
  const chance = p => Math.random() < p;
  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
  const other = t => (t === 'A' ? 'B' : 'A');
  const smooth = u => u * u * (3 - 2 * u);

  const NET_H = 2.24;
  const BALL_R = 0.105;

  // ---------- Teams (5-1 system: setter, opposite, two outside hitters, two middle blockers) ----------
  const TEAM_DEF = {
    A: {
      label: 'Squadra A', short: 'A',
      jersey: '#2f6fd0', shorts: '#14264a', trim: '#ffffff', hair: '#3b2a1f',
      liberoJersey: '#ffd54f', liberoNumber: '#14264a',
      roster: [1, 2, 3, 5, 7, 8, 9, 10, 11, 13, 14, 16], libero: 6, captain: 3,
      lineup: [7, 10, 3, 11, 5, 9],
      roles: { 7: 'S', 10: 'OH', 3: 'M', 11: 'O', 5: 'OH', 9: 'M', 1: 'S', 2: 'OH', 8: 'M', 13: 'O', 14: 'OH', 16: 'M' },
    },
    B: {
      label: 'Squadra B', short: 'B',
      jersey: '#e2552b', shorts: '#3b1a12', trim: '#ffd5c2', hair: '#1f1712',
      liberoJersey: '#1f8a5b', liberoNumber: '#ffffff',
      roster: [1, 2, 4, 5, 8, 10, 12, 13, 15, 17, 18, 20], libero: 7, captain: 12,
      lineup: [4, 12, 8, 15, 1, 2],
      roles: { 4: 'S', 12: 'OH', 8: 'M', 15: 'O', 1: 'OH', 2: 'M', 5: 'S', 10: 'OH', 13: 'M', 17: 'O', 18: 'OH', 20: 'M' },
    },
  };

  /** Numbers that are not on either score sheet (used for irregular requests). */
  const NOT_ON_SHEET = { A: [4, 12, 15, 19, 22], B: [3, 6, 9, 11, 14] };

  /** Substitutions per set: 8 in the FIPAV 2026/27 season (Normative federali 4.3; Rule 15.1 says 6). */
  const MAX_SUBS = 8;

  // ---------- Decisions the referee can make ----------
  const DECISIONS = {
    in: { label: 'Palla dentro', group: 'Esito', team: true },
    out: { label: 'Palla fuori', group: 'Esito', team: true },
    touched_out: { label: 'Toccata dal muro e fuori', group: 'Esito', team: true },
    serve_foot: { label: 'Fallo del giocatore al servizio', group: 'Servizio', team: true },
    serve_8s: { label: 'Servizio oltre gli 8 secondi', group: 'Servizio', team: true },
    serve_net: { label: 'Servizio in rete', group: 'Servizio', team: true },
    serve_early: { label: 'Servizio prima del fischio: si ripete', group: 'Servizio', team: false },
    pos_fault: { label: 'Fallo di posizione', group: 'Posizione', team: true },
    four_hits: { label: 'Quattro tocchi', group: 'Tocco di palla', team: true },
    catch: { label: 'Palla trattenuta', group: 'Tocco di palla', team: true },
    double_contact: { label: 'Doppio tocco', group: 'Tocco di palla', team: true },
    net_touch: { label: 'Tocco della rete', group: 'Rete e linea', team: true },
    centre_line: { label: 'Invasione oltre la linea centrale', group: 'Rete e linea', team: true },
    back_attack: { label: 'Attacco irregolare di un difensore', group: 'Attacco', team: true },
    libero_attack: { label: 'Attacco irregolare del Libero', group: 'Attacco', team: true },
    libero_set: { label: 'Attacco su alzata del Libero in zona d’attacco', group: 'Attacco', team: true },
    attack_serve: { label: 'Attacco sul servizio avversario', group: 'Attacco', team: true },
    reach_beyond: { label: 'Invasione aerea: tocco nello spazio avversario', group: 'Rete e linea', team: true },
    under_net: { label: 'Palla passata sotto la rete', group: 'Rete e linea', team: true },
    antenna: { label: 'Palla sull’antenna', group: 'Rete e linea', team: true },
    block_serve: { label: 'Muro sul servizio avversario', group: 'Muro', team: true },
    back_block: { label: 'Muro di un difensore', group: 'Muro', team: true },
    double_fault: { label: 'Doppio fallo: si rigioca', group: 'Altro', team: false },
    no_fault: { label: 'Nessun fallo: ho fischiato per errore', group: 'Altro', team: false },
  };

  /** Who decides each fault: '1' first referee, '2' second referee, 'both'. */
  const COMPETENCE = {
    in: '1', out: '1', touched_out: '1', serve_foot: '1', serve_8s: '1', serve_net: '1', serve_early: '1',
    pos_fault_serving: '1', pos_fault_receiving: '2',
    four_hits: '1', catch: '1', double_contact: '1',
    net_touch: 'both', centre_line: '2', back_attack: 'both', libero_attack: 'both', libero_set: 'both',
    attack_serve: '1', reach_beyond: '1', under_net: 'both', antenna: 'both', block_serve: '1', back_block: 'both',
  };

  const REQUEST_DECISIONS = {
    authorize: 'Autorizza',
    reject_improper: 'Respingi: richiesta impropria',
    delay_warning: 'Respingi e sanziona: avvertimento per ritardo',
    delay_penalty: 'Respingi e sanziona: penalizzazione per ritardo',
  };

  const CONDUCT_DECISIONS = {
    none: 'Nessun provvedimento',
    verbal: 'Avvertimento verbale',
    yellow: 'Avvertimento ufficiale (giallo)',
    red: 'Penalizzazione (rosso)',
    expulsion: 'Espulsione (giallo e rosso insieme)',
    disqualification: 'Squalifica (giallo e rosso separati)',
  };

  // ======================================================================================
  // Match
  // ======================================================================================

  /**
   * opts: mode ('1' | '2' | 'solo'), aids (slow motion and impact mark for touches and lines),
   * freq (0..1, how often an irregular situation appears), target (points to win the set), libero (check Libero replacements)
   */
  function Match(opts) {
    this.opts = Object.assign({ mode: '1', aids: true, freq: 0.45, target: 25, libero: true }, opts || {});
    this.score = { A: 0, B: 0 };
    this.side = { A: -1, B: 1 };
    this.serving = chance(0.5) ? 'A' : 'B';
    this.teams = { A: this.newTeam('A'), B: this.newTeam('B') };
    this.rallyNo = 0;
    this.completedRallies = 0;
    this.history = [];
    this.pos = {};
    this.lastSit = {};
    this.over = false;
    this.initPositions();
  }

  Match.prototype.newTeam = function (key) {
    const d = TEAM_DEF[key];
    const reserves = d.roster.filter(n => !d.lineup.includes(n));
    return {
      key, def: d,
      lineup: d.lineup.slice(),
      libero: { num: d.libero, on: null, lastRally: -99, log: [] },
      subs: { count: 0, list: [], starterOut: {}, starterBack: {}, reserveIn: {}, reserveDone: {} },
      reserves,
      timeouts: 0,
      delays: 0,
      improper: 0,
      conduct: { verbal: false, yellow: false, members: {} },
      sanctions: [],
    };
  };

  Match.prototype.userRole = function () { return this.opts.mode; };
  /** Is the user responsible for a decision with this competence? */
  Match.prototype.userDecides = function (comp) {
    const m = this.opts.mode;
    if (m === 'solo') return true;
    if (comp === 'both') return true;
    return comp === m;
  };

  /** World position from a team's local coordinates. */
  Match.prototype.w = function (team, d, l) {
    const s = this.side[team];
    return [s * d, -s * l];
  };
  Match.prototype.local = function (team, x, y) {
    const s = this.side[team];
    return { d: x * s, l: -y * s };
  };

  /** Number shown on court in position index k (0 = position I), with the Libero replacing someone. */
  Match.prototype.onCourt = function (team, k) {
    const t = this.teams[team];
    const n = t.lineup[k];
    return t.libero.on === n ? t.libero.num : n;
  };
  Match.prototype.isFront = k => k === 1 || k === 2 || k === 3;
  Match.prototype.roleOf = function (team, num) {
    const t = this.teams[team];
    if (num === t.libero.num) return 'L';
    return t.def.roles[num] || 'OH';
  };
  Match.prototype.indexOf = function (team, num) {
    const t = this.teams[team];
    const n = num === t.libero.num ? t.libero.on : num;
    return t.lineup.indexOf(n);
  };

  Match.prototype.initPositions = function () {
    ['A', 'B'].forEach(T => {
      const f = this.legalFormation(T, T === this.serving);
      for (let k = 0; k < 6; k++) {
        const n = this.onCourt(T, k);
        const p = this.w(T, f[k].d, f[k].l);
        this.pos[T + n] = { x: p[0], y: p[1] };
      }
    });
  };

  // ---------- Formations ----------
  /** Random legal positions for the 6 players (local coords, index = position - 1). */
  Match.prototype.legalFormation = function (team, serving) {
    const f = [];
    const fd = [rnd(0.9, 2.4), rnd(0.7, 2.0), rnd(0.9, 2.4)];
    // Front row: II (right), III (centre), IV (left)
    f[1] = { d: fd[0], l: rnd(-3.9, -2.0) };
    f[2] = { d: fd[1], l: rnd(-0.9, 0.9) };
    f[3] = { d: fd[2], l: rnd(2.0, 3.9) };
    // Back row: I (right), VI (centre), V (left), each farther from the net than its front player
    f[0] = { d: clamp(f[1].d + rnd(1.6, 5.2), 3.6, 8.3), l: rnd(-3.9, -2.2) };
    f[5] = { d: clamp(f[2].d + rnd(2.2, 5.6), 4.0, 8.4), l: rnd(-0.9, 0.9) };
    f[4] = { d: clamp(f[3].d + rnd(1.6, 5.2), 3.6, 8.3), l: rnd(2.2, 3.9) };
    if (serving) f[0] = { d: rnd(9.5, 10.2), l: rnd(-3.6, 3.0), server: true };
    return f;
  };

  const PAIRS_DEPTH = [[0, 1], [5, 2], [4, 3]];        // [back, front]
  const ROWS = [[1, 2, 3], [0, 5, 4]];                // right → left in each row

  /** Positional fault check (Rule 7.4.3), server excluded. Returns the first violation or null. */
  Match.prototype.checkFormation = function (f, serving) {
    for (const [b, fr] of PAIRS_DEPTH) {
      if (serving && b === 0) continue;
      if (f[b].d < f[fr].d) return { kind: 'depth', back: b, front: fr };
    }
    for (const row of ROWS) {
      for (let i = 0; i < 2; i++) {
        const r = row[i], c = row[i + 1];
        if (serving && (r === 0 || c === 0)) continue;
        if (f[r].l > f[c].l) return { kind: 'lateral', right: r, left: c };
      }
    }
    return null;
  };

  /** A formation with one clear positional fault (about half a metre, visible from the right angle). */
  Match.prototype.faultyFormation = function (team, serving) {
    const f = this.legalFormation(team, serving);
    const options = [];
    PAIRS_DEPTH.forEach(([b, fr]) => { if (!(serving && b === 0)) options.push({ kind: 'depth', b, fr }); });
    ROWS.forEach(row => {
      for (let i = 0; i < 2; i++) {
        if (serving && (row[i] === 0 || row[i + 1] === 0)) continue;
        options.push({ kind: 'lateral', r: row[i], c: row[i + 1] });
      }
    });
    const o = pick(options);
    if (o.kind === 'depth') {
      f[o.fr].d = rnd(2.4, 3.2);
      f[o.b].d = f[o.fr].d - rnd(0.55, 0.9);
      f[o.b].l = f[o.fr].l + rnd(-0.6, 0.6);
    } else {
      // The right-hand player ends up to the left of the centre one (or the centre one to the left of the left one)
      const mid = (f[o.r].l + f[o.c].l) / 2;
      f[o.r].l = mid + rnd(0.35, 0.6);
      f[o.c].l = mid - rnd(0.35, 0.6);
      f[o.c].d = f[o.r].d + rnd(-0.3, 0.3);
      if (f[o.c].d < 0.6) f[o.c].d = 0.6;
    }
    const v = this.checkFormation(f, serving);
    return { f, violation: v };
  };

  Match.prototype.describeViolation = function (team, v) {
    const name = k => 'n. ' + this.onCourt(team, k) + ' (posizione ' + ['I', 'II', 'III', 'IV', 'V', 'VI'][k] + ')';
    if (!v) return '';
    if (v.kind === 'depth') return name(v.back) + ' è più vicino alla rete del suo avanti corrispondente, ' + name(v.front) + '.';
    return name(v.right) + ' è più a sinistra di ' + name(v.left) + ', che deve restare alla sua sinistra.';
  };

  // ======================================================================================
  // Between rallies: requests, Libero replacements, conduct
  // ======================================================================================

  /**
   * Builds the events of the break before the next rally. Each event:
   * { kind: 'timeout' | 'sub' | 'libero' | 'conduct', team, comp ('1' | '2'), user (bool),
   *   text, expected (decision key), sit (situation id), ... }
   */
  Match.prototype.betweenEvents = function () {
    const ev = [];
    if (this.over) return ev;
    const freq = this.opts.freq;
    // Automatic (regular) Libero replacements required by the new rotation
    ['A', 'B'].forEach(T => {
      const r = this.liberoNeed(T);
      if (r) ev.push(r);
    });
    // Occasional requests and conduct
    if (this.completedRallies > 0 && chance(0.18 + freq * 0.25)) {
      const T = chance(0.5) ? 'A' : 'B';
      const roll = Math.random();
      let e = null;
      if (roll < 0.28) e = this.timeoutRequest(T);
      else if (roll < 0.66) e = this.subRequest(T);
      else if (roll < 0.82 && this.opts.libero) e = this.liberoIrregular(T);
      else e = this.conductEvent(T);
      if (e) ev.push(e);
    }
    ev.forEach(e => { e.user = this.userDecides(e.comp) && !(e.kind === 'libero' && e.regular && !this.opts.libero); });
    return ev;
  };

  /** The Libero must leave when the replaced player reaches the front row (or must serve), and comes in for a back-row middle blocker. */
  Match.prototype.liberoNeed = function (T) {
    const t = this.teams[T];
    const L = t.libero;
    const serving = this.serving === T;
    if (L.on !== null) {
      const k = t.lineup.indexOf(L.on);
      if (this.isFront(k) || (k === 0 && serving)) {
        return { kind: 'libero', team: T, comp: '2', out: L.num, inn: L.on, regular: true, exit: true, sit: 'libero_regular',
          text: 'Rimpiazzo del Libero (' + t.def.label + '): esce il Libero n. ' + L.num + ', rientra il n. ' + L.on + '.' };
      }
      return null;
    }
    if (this.completedRallies - L.lastRally < 1) return null;
    const cand = [4, 5].concat(serving ? [] : [0]).map(k => t.lineup[k]).filter(n => this.roleOf(T, n) === 'M');
    if (!cand.length) return null;
    const n = cand[0];
    return { kind: 'libero', team: T, comp: '2', out: n, inn: L.num, regular: true, sit: 'libero_regular',
      text: 'Rimpiazzo del Libero (' + t.def.label + '): entra il Libero n. ' + L.num + ' al posto del n. ' + n + '.' };
  };

  /** Irregular Libero replacement: two replacements without a completed rally in between. */
  Match.prototype.liberoIrregular = function (T) {
    const t = this.teams[T];
    const L = t.libero;
    if (L.on === null || L.lastRally !== this.completedRallies) {
      // Only possible right after a replacement in this same break: build it from the current state
      if (L.on === null) return null;
    }
    const backOthers = [4, 5, 0].map(k => t.lineup[k]).filter(n => n !== L.on && this.roleOf(T, n) !== 'S');
    if (!backOthers.length) return null;
    const target = pick(backOthers);
    return { kind: 'libero', team: T, comp: '2', out: target, inn: L.num, regular: false, swap: L.on, sit: 'libero_no_rally',
      text: 'Rimpiazzo del Libero (' + t.def.label + '): il Libero n. ' + L.num + ' esce per il n. ' + L.on + ' e rientra subito al posto del n. ' + target + ', senza un’azione di gioco completata in mezzo.',
      expected: t.delays ? 'delay_penalty' : 'delay_warning' };
  };

  Match.prototype.timeoutRequest = function (T) {
    const t = this.teams[T];
    if (t.timeouts >= 2) {
      return { kind: 'timeout', team: T, comp: '2', sit: 'timeout_third',
        text: 'L’allenatore della ' + t.def.label + ' chiede un tempo di riposo.',
        expected: t.improper === 0 ? 'reject_improper' : (t.delays ? 'delay_penalty' : 'delay_warning') };
    }
    return { kind: 'timeout', team: T, comp: '2', sit: 'timeout_ok',
      text: 'L’allenatore della ' + t.def.label + ' chiede un tempo di riposo.', expected: 'authorize' };
  };

  /** Substitution request: regular most of the time, otherwise one of the irregular cases. */
  Match.prototype.subRequest = function (T) {
    const t = this.teams[T];
    const S = t.subs;
    const onCourt = [0, 1, 2, 3, 4, 5].map(k => t.lineup[k]).filter(n => n !== t.libero.on);
    const lbl = t.def.label;
    const delayExp = t.delays ? 'delay_penalty' : 'delay_warning';
    const irregular = chance(0.3 + this.opts.freq * 0.4);
    if (S.count >= MAX_SUBS) {
      const out = pick(onCourt);
      const inn = pick(t.reserves.filter(n => !S.reserveIn[n] && !S.reserveDone[n]).concat([0])) || 0;
      if (inn) return { kind: 'sub', team: T, comp: '2', sit: 'sub_seventh', out, inn,
        text: lbl + ': il n. ' + inn + ' è nella zona di sostituzione per il n. ' + out + '.',
        expected: t.improper === 0 ? 'reject_improper' : delayExp };
    }
    if (irregular) {
      const cases = [];
      // Not on the score sheet
      cases.push(() => {
        const out = pick(onCourt.filter(n => !S.starterBack[n]));
        const inn = pick(NOT_ON_SHEET[T]);
        return { sit: 'sub_not_on_sheet', out, inn, text: lbl + ': il n. ' + inn + ' è nella zona di sostituzione per il n. ' + out + '.' };
      });
      // A starter who is out tries to come back for a different player
      const outStarters = Object.keys(S.starterOut).map(Number).filter(n => !S.starterBack[n]);
      if (outStarters.length) cases.push(() => {
        const x = pick(outStarters);
        const y = S.starterOut[x];
        const z = pick(onCourt.filter(n => n !== y));
        return { sit: 'sub_wrong_reentry', out: z, inn: x, text: lbl + ': il n. ' + x + ' è nella zona di sostituzione per il n. ' + z + '.' };
      });
      // A reserve who already entered and left tries to enter again
      const done = Object.keys(S.reserveDone).map(Number);
      if (done.length) cases.push(() => {
        const y = pick(done);
        const out = pick(onCourt.filter(n => !S.starterOut[n] && !S.starterBack[n]).concat(onCourt).slice(0, 6));
        return { sit: 'sub_reserve_twice', out, inn: y, text: lbl + ': il n. ' + y + ' è nella zona di sostituzione per il n. ' + out + '.' };
      });
      // A starter who already came back tries to leave again
      const back = Object.keys(S.starterBack).map(Number).filter(n => onCourt.includes(n));
      if (back.length) cases.push(() => {
        const x = pick(back);
        const inn = pick(t.reserves.filter(n => !S.reserveIn[n] && !S.reserveDone[n]).concat(NOT_ON_SHEET[T]).slice(0, 4));
        return { sit: 'sub_starter_twice', out: x, inn, text: lbl + ': il n. ' + inn + ' è nella zona di sostituzione per il n. ' + x + '.' };
      });
      const c = pick(cases)();
      return Object.assign({ kind: 'sub', team: T, comp: '2', expected: delayExp }, c);
    }
    // Regular: a reserve goes back out for its starter, or a fresh reserve comes in for a starter
    const reserveOnCourt = Object.keys(S.reserveIn).map(Number).filter(n => onCourt.includes(n));
    if (reserveOnCourt.length && chance(0.5)) {
      const y = pick(reserveOnCourt);
      const x = S.reserveIn[y];
      return { kind: 'sub', team: T, comp: '2', sit: 'sub_ok', out: y, inn: x, expected: 'authorize',
        text: lbl + ': il n. ' + x + ' è nella zona di sostituzione per il n. ' + y + '.' };
    }
    const starters = onCourt.filter(n => !S.starterOut[n] && !S.starterBack[n] && !S.reserveIn[n] && this.roleOf(T, n) !== 'S');
    const fresh = t.reserves.filter(n => !S.reserveIn[n] && !S.reserveDone[n]);
    if (!starters.length || !fresh.length) return null;
    const out = pick(starters), inn = pick(fresh);
    return { kind: 'sub', team: T, comp: '2', sit: 'sub_ok', out, inn, expected: 'authorize',
      text: lbl + ': il n. ' + inn + ' è nella zona di sostituzione per il n. ' + out + '.' };
  };

  /** Misconduct: the expected decision follows the scale of sanctions. */
  Match.prototype.conductEvent = function (T) {
    const t = this.teams[T];
    const onCourt = [0, 1, 2, 3, 4, 5].map(k => this.onCourt(T, k));
    const who = pick(onCourt);
    const m = t.conduct.members[who] || { rude: 0 };
    const roll = Math.random();
    if (roll < 0.5) {
      // Minor misconduct: phase 1 verbal warning, phase 2 official warning (yellow card)
      if (t.conduct.yellow) return null;
      return { kind: 'conduct', team: T, comp: '1', who, sit: t.conduct.verbal ? 'conduct_minor_second' : 'conduct_minor_first', level: 'minor',
        text: 'Il n. ' + who + ' della ' + t.def.label + ' allarga le braccia e commenta a voce alta la decisione appena presa.',
        expected: t.conduct.verbal ? 'yellow' : 'verbal' };
    }
    if (roll < 0.82) {
      return { kind: 'conduct', team: T, comp: '1', who, sit: m.rude ? 'conduct_rude_second' : 'conduct_rude', level: 'rude',
        text: 'Il n. ' + who + ' della ' + t.def.label + ' calcia con rabbia il pallone verso la tribuna dopo il fischio.',
        expected: m.rude ? 'expulsion' : 'red' };
    }
    return { kind: 'conduct', team: T, comp: '1', who, sit: 'conduct_offensive', level: 'offensive',
      text: 'Il n. ' + who + ' della ' + t.def.label + ' rivolge al 1° arbitro un gesto offensivo.',
      expected: 'expulsion' };
  };

  /** An event prepared at the start of the break may no longer make sense (e.g. after a penalty rotated the team). */
  Match.prototype.stillValid = function (e) {
    const t = this.teams[e.team];
    if (e.kind === 'libero' && e.regular && !e.exit) {
      const k = t.lineup.indexOf(e.out);
      return t.libero.on === null && k >= 0 && !this.isFront(k) && !(k === 0 && this.serving === e.team);
    }
    if (e.kind === 'libero' && e.regular && e.exit) return t.libero.on === e.inn;
    // A substitution cannot involve the player the Libero is replacing (he is not on court)
    if (e.kind === 'sub') return t.lineup.includes(e.out) && e.out !== t.libero.on;
    if (e.kind === 'libero') return t.lineup.includes(e.out) || e.out === t.libero.num;
    return true;
  };

  /** Applies a break event after the decision (always the correct one, so the match stays consistent). */
  Match.prototype.applyBetween = function (e) {
    const t = this.teams[e.team];
    const exp = e.expected || (e.regular ? 'authorize' : null);
    const res = { point: null };
    if (exp === 'delay_warning') { t.delays++; t.sanctions.push('Avvertimento per ritardo'); }
    if (exp === 'delay_penalty') { t.delays++; t.sanctions.push('Penalizzazione per ritardo'); res.point = other(e.team); }
    if (exp === 'reject_improper') { t.improper++; }
    if (e.kind === 'timeout' && exp === 'authorize') t.timeouts++;
    if (e.kind === 'sub' && exp === 'authorize') this.doSub(e.team, e.out, e.inn);
    if (e.kind === 'libero') {
      if (e.regular) {
        if (e.exit) t.libero.on = null; else t.libero.on = e.out;
        t.libero.lastRally = this.completedRallies;
        t.libero.log.push(e.exit ? 'L' + t.libero.num + ' → ' + e.inn : e.out + ' → L' + t.libero.num);
        this.swapPos(e.team, e.exit ? t.libero.num : e.out, e.exit ? e.inn : t.libero.num);
      } else {
        t.libero.log.push('Rimpiazzo irregolare respinto');
      }
    }
    if (e.kind === 'conduct') {
      const m = t.conduct.members[e.who] || (t.conduct.members[e.who] = { rude: 0 });
      if (exp === 'verbal') { t.conduct.verbal = true; t.sanctions.push('Avvertimento verbale (n. ' + e.who + ')'); }
      if (exp === 'yellow') { t.conduct.yellow = true; t.sanctions.push('Cartellino giallo (n. ' + e.who + ')'); }
      if (exp === 'red') { m.rude++; t.sanctions.push('Penalizzazione (n. ' + e.who + ')'); res.point = other(e.team); }
      if (exp === 'expulsion') { t.sanctions.push('Espulsione (n. ' + e.who + ')'); res.expelled = e.who; }
    }
    if (res.point) {
      this.addPoint(res.point);
      // A penalty can rotate a team and bring the Libero to the front row: he is replaced straight away (Rule 19.3.2)
      res.followUp = ['A', 'B'].map(T => this.liberoNeed(T)).filter(x => x && x.exit);
      res.followUp.forEach(x => { x.user = this.userDecides(x.comp) && this.opts.libero; });
    }
    return res;
  };

  Match.prototype.doSub = function (T, out, inn) {
    const t = this.teams[T];
    const S = t.subs;
    const k = t.lineup.indexOf(out);
    if (k < 0) return;
    t.lineup[k] = inn;
    S.count++;
    S.list.push(inn + ' ⇄ ' + out);
    const starter = TEAM_DEF[T].lineup.includes(out);
    if (starter && !S.starterOut[out]) { S.starterOut[out] = inn; S.reserveIn[inn] = out; }
    else if (S.reserveIn[out] !== undefined && S.reserveIn[out] === inn) { S.starterBack[inn] = true; S.reserveDone[out] = true; delete S.reserveIn[out]; }
    this.swapPos(T, out, inn);
  };

  Match.prototype.swapPos = function (T, outNum, inNum) {
    const p = this.pos[T + outNum];
    if (p) { this.pos[T + inNum] = { x: p.x, y: p.y }; delete this.pos[T + outNum]; }
  };

  // ======================================================================================
  // Points and rotation
  // ======================================================================================

  Match.prototype.addPoint = function (T) {
    this.score[T]++;
    if (this.serving !== T) {
      this.serving = T;
      const t = this.teams[T];
      t.lineup = t.lineup.slice(1).concat(t.lineup.slice(0, 1));
    }
    const a = this.score.A, b = this.score.B, tg = this.opts.target;
    if ((a >= tg || b >= tg) && Math.abs(a - b) >= 2) this.over = true;
  };

  // ======================================================================================
  // Rally construction
  // ======================================================================================

  /** Chooses what will happen in the next rally. */
  Match.prototype.planRally = function () {
    const o = this.opts;
    const sits = [];
    const add = (id, w) => sits.push([id, w]);
    add('pos_serving', 1); add('pos_receiving', 1);
    add('serve_foot', 0.7); add('serve_8s', 0.6); add('serve_net', 0.5);
    if (o.mode !== '2') add('serve_early', 0.5);
    add('four_hits', 0.8); add('block_plus_three', 0.6);
    add('net_touch', 1); add('centre_line', 0.8);
    add('back_attack', 0.7); add('libero_attack', 0.6); add('libero_set', 0.6);
    add('no_fault_close', 0.8);
    add('reach_beyond', 0.6); add('back_block', 0.5); add('under_net', 0.4); add('antenna', 0.5);
    add('attack_serve', 0.4); add('block_serve', 0.4);
    if (o.aids) { add('catch', 0.8); add('double_contact', 0.8); add('double_first', 0.6); add('touched_out', 0.9); }
    let sit = null;
    if (chance(o.freq)) {
      // Avoid repeating the same situation too often
      const total = sits.reduce((s, x) => s + x[1] / (1 + (this.lastSit[x[0]] || 0)), 0);
      let r = Math.random() * total;
      for (const [id, wt] of sits) { r -= wt / (1 + (this.lastSit[id] || 0)); if (r <= 0) { sit = id; break; } }
      if (sit) this.lastSit[sit] = (this.lastSit[sit] || 0) + 1;
    }
    return { sit };
  };

  /**
   * Builds the rally timeline. t = 0 is the service authorisation (or, for a service before the whistle,
   * the moment the server hits). Returns an object read by sample() and evaluate().
   */
  Match.prototype.buildRally = function (plan) {
    const M = this;
    const sv = this.serving, rc = other(sv);
    const R = {
      plan, serving: sv, receiving: rc, keys: {}, moves: {}, ball: [], events: [], faults: [], slow: [],
      formations: {}, natural: null, endT: 0, holder: null, label: {},
    };
    let sit = plan.sit;
    // Libero situations need the receiving team's Libero on court
    const rcTeam = this.teams[rc];
    if ((sit === 'libero_attack' || sit === 'libero_set') && rcTeam.libero.on === null) sit = plan.sit = null;
    this.rallyNo++;

    // ---- Formations at the moment of the service ----
    const forms = {};
    [sv, rc].forEach(T => {
      const serving = T === sv;
      const wantFault = (sit === 'pos_serving' && serving) || (sit === 'pos_receiving' && !serving);
      if (wantFault) {
        const ff = M.faultyFormation(T, serving);
        forms[T] = ff.f;
        R.posFault = { team: T, violation: ff.violation, text: M.describeViolation(T, ff.violation) };
      } else {
        let f, tries = 0;
        do { f = M.legalFormation(T, serving); tries++; } while (M.checkFormation(f, serving) && tries < 20);
        forms[T] = f;
      }
    });
    R.formations = forms;

    // ---- Keyframe helpers ----
    const keyOf = (T, n) => T + n;
    const track = key => (R.moves[key] = R.moves[key] || { p: [], z: [], pose: [] });
    const at = (T, n, t, d, l, pose) => {
      const k = keyOf(T, n);
      const p = M.w(T, d, l);
      const tr = track(k);
      tr.p.push({ t, x: p[0], y: p[1] });
      if (pose) tr.pose.push({ t, pose });
      R.keys[k] = { team: T, num: n };
    };
    const atW = (T, n, t, x, y, pose) => {
      const k = keyOf(T, n);
      const tr = track(k);
      tr.p.push({ t, x, y });
      if (pose) tr.pose.push({ t, pose });
      R.keys[k] = { team: T, num: n };
    };
    const jump = (T, n, t, h, dur) => {
      const tr = track(keyOf(T, n));
      dur = dur || 0.75;
      tr.z.push({ t: t - dur * 0.45, z: 0 }, { t, z: h }, { t: t + dur * 0.55, z: 0 });
    };
    const pose = (T, n, t, p) => track(keyOf(T, n)).pose.push({ t, pose: p });
    const ball = (t0, t1, p0, p1, h, under) => {
      h = h || 0;
      // A flight that crosses the net always clears it (by at least 25 cm), unless it is meant to pass under it
      if (!under && p0[0] !== 0 && p1[0] !== 0 && Math.sign(p0[0]) !== Math.sign(p1[0])) {
        const u = p0[0] / (p0[0] - p1[0]);
        const line = p0[2] + (p1[2] - p0[2]) * u;
        const need = (NET_H + 0.25 - line) / (4 * u * (1 - u));
        if (need > h) h = need;
      }
      R.ball.push({ t0, t1, p0, p1, h });
    };
    const ev = e => R.events.push(e);
    const fault = f => R.faults.push(f);

    // Initial positions: everyone in formation at t = 0
    const players = {};
    [sv, rc].forEach(T => {
      players[T] = [];
      for (let k = 0; k < 6; k++) {
        const n = M.onCourt(T, k);
        players[T].push(n);
        at(T, n, 0, forms[T][k].d, forms[T][k].l, k === 0 && T === sv ? 'stand' : 'ready');
      }
    });
    const server = M.onCourt(sv, 0);
    R.server = { team: sv, num: server };

    // Base defensive/attacking positions after the service
    const base = (T, k) => [[6.6, -2.9], [1.1, -2.9], [1.1, 0], [1.1, 2.9], [6.6, 2.9], [7.0, 0]][k];
    const nAt = (T, k) => M.onCourt(T, k);
    const kOf = (T, n) => players[T].indexOf(n);

    // ---- Service ----
    let tHit = rnd(2.0, 4.2);
    if (sit === 'serve_8s') tHit = rnd(9.2, 9.8);
    if (sit === 'serve_early') tHit = rnd(1.2, 1.8);
    R.tHit = tHit;
    const sPos = forms[sv][0];
    const sl = clamp(sPos.l, -3.6, 3.6);
    let sd = sPos.d;
    if (sit === 'serve_foot') sd = 9.0 - rnd(0.02, 0.12); // on the end line / inside the court at the hit
    // Server walks to the service spot and bounces the ball
    at(sv, server, 0, sPos.d, sl, 'stand');
    at(sv, server, tHit - 0.9, sd + 0.45, sl, 'stand');
    at(sv, server, tHit - 0.05, sd, sl, 'serve');
    jump(sv, server, tHit, 0.35, 0.6);
    const hand = M.w(sv, sd - 0.25, sl - 0.25);
    const handP = [hand[0], hand[1], 2.75];
    R.holder = { team: sv, num: server, until: tHit - 0.75 };
    // Toss
    ball(tHit - 0.75, tHit, [hand[0], hand[1], 1.3], handP, 1.3);
    ev({ t: tHit, kind: 'contact', team: sv, num: server, what: 'serve' });
    if (sit === 'serve_8s') fault({ t: 8.0, key: 'serve_8s', team: sv, comp: '1', sit, text: 'Il n. ' + server + ' ha colpito la palla dopo più di 8 secondi dal fischio.' });
    if (sit === 'serve_foot') fault({ t: tHit, key: 'serve_foot', team: sv, comp: '1', sit, text: 'Al colpo di servizio il n. ' + server + ' tocca la linea di fondo.' });
    if (sit === 'serve_early') fault({ t: tHit, key: 'serve_early', team: null, comp: '1', sit, text: 'Il n. ' + server + ' ha servito prima del fischio di autorizzazione.' });
    if (R.posFault) {
      const pf = R.posFault;
      if (pf.team === sv) {
        // FIPAV 2026/27 experiment (Normative federali 4.2): the serving team may take any position at the service hit
        R.trap = { sit: 'pos_serving', team: sv, text: 'Al colpo di servizio la ' + M.teams[sv].def.label + ' (al servizio) non rispetta l’ordine di rotazione: ' + pf.text + ' Con la sperimentazione 2026/27 la squadra al servizio è libera di posizionarsi come vuole.' };
      } else {
        fault({ t: tHit, key: 'pos_fault', team: pf.team, comp: '2', sit: 'pos_receiving', text: pf.text });
      }
    }

    // After the hit, everyone may move to the base positions
    [sv, rc].forEach(T => {
      for (let k = 0; k < 6; k++) {
        const n = nAt(T, k);
        if (T === sv && k === 0) { const b = base(T, 0); at(T, n, tHit + 0.5, sd - 0.6, sl, 'walk'); at(T, n, tHit + 1.6, b[0], b[1], 'ready'); continue; }
        const b = base(T, k);
        at(T, n, tHit + 0.25, forms[T][k].d, forms[T][k].l, 'ready');
        at(T, n, tHit + 1.4, T === rc && !M.isFront(k) ? forms[T][k].d * 0.4 + b[0] * 0.6 : b[0], T === rc && !M.isFront(k) ? forms[T][k].l * 0.5 + b[1] * 0.5 : b[1], 'ready');
      }
    });

    // ---- Service outcome ----
    const serveFaulty = sit === 'serve_net';
    const serveOut = !sit && chance(0.06);
    const ace = !sit && !serveOut && chance(0.05);
    // Receiver: a back-row non-setter (or the Libero), close to where the serve lands
    const recvCands = [0, 4, 5].map(k => nAt(rc, k)).filter(n => M.roleOf(rc, n) !== 'S' && !((sit === 'libero_attack' || sit === 'libero_set') && M.roleOf(rc, n) === 'L'));
    let receiver = pick(recvCands.length ? recvCands : [0, 4, 5].map(k => nAt(rc, k)).filter(n => M.roleOf(rc, n) !== 'S'));
    // If only the Libero can receive, the Libero situations give way to a normal rally
    if ((sit === 'libero_attack' || sit === 'libero_set') && M.roleOf(rc, receiver) === 'L') sit = plan.sit = null;
    const rk = kOf(rc, receiver);
    const rPoint = { d: clamp(forms[rc][rk].d + rnd(-0.5, 0.5), 4.5, 8.2), l: clamp(forms[rc][rk].l + rnd(-0.8, 0.8), -4.0, 4.0) };

    let t = tHit;
    const flight = rnd(1.05, 1.35);
    if (serveFaulty) {
      // Into the net, falls back on the serving side
      const netP = [0, -M.side[sv] * sl * 0.6, NET_H - 0.35];
      ball(t, t + 0.75, handP, netP, 0.6);
      const back = M.w(sv, rnd(0.6, 1.4), sl * 0.6);
      ball(t + 0.75, t + 1.3, netP, [back[0], back[1], BALL_R], 0.15);
      ev({ t: t + 0.75, kind: 'net', y: netP[1] });
      fault({ t: t + 0.75, key: 'serve_net', team: sv, comp: '1', sit, text: 'Il servizio non supera la rete.' });
      R.natural = { t: t + 1.3, key: 'serve_net', team: sv, noWhistle: true };
      R.endT = t + 1.3;
      return this.finishRally(R);
    }
    if (sit === 'attack_serve' || sit === 'block_serve') {
      // A short, high service attacked (or blocked) by a front-row player of the receiving team above the net
      const blk = sit === 'block_serve';
      const fr = [1, 2, 3].map(k => nAt(rc, k)).filter(n => n !== M.teams[rc].libero.num);
      const who = pick(fr);
      const k0 = kOf(rc, who);
      const yl = clamp(forms[rc][k0].l + rnd(-0.6, 0.6), -3.6, 3.6);
      const dC = blk ? 0.3 : 0.9;
      const hitP = (() => { const w = M.w(rc, dC, yl); return [w[0], w[1], NET_H + (blk ? 0.3 : 0.45)]; })();
      const tC = t + rnd(0.95, 1.1);
      // The player goes straight to the net: drop the generic moves after the service
      const tr = track(keyOf(rc, who));
      tr.p = tr.p.filter(q => q.t <= tHit + 0.3);
      tr.pose = tr.pose.filter(q => q.t <= tHit + 0.3);
      ball(t, tC, handP, hitP, 1.0);
      at(rc, who, tC - 0.45, dC + (blk ? 0 : 0.8), yl, blk ? 'block' : 'walk');
      at(rc, who, tC - 0.05, dC, yl, blk ? 'block' : 'attack');
      jump(rc, who, tC, 0.75, 0.8);
      at(rc, who, tC + 0.7, dC + 0.4, yl, 'ready');
      ev({ t: tC, kind: 'contact', team: rc, num: who, what: blk ? 'block' : 'attack' });
      const lp = M.w(sv, blk ? rnd(0.8, 2.5) : rnd(2, 6), rnd(-3.5, 3.5));
      const tl = tC + (blk ? 0.6 : 0.45);
      ball(tC, tl, hitP, [lp[0], lp[1], BALL_R], blk ? 0.25 : 0.05);
      ev({ t: tl, kind: 'land', x: lp[0], y: lp[1] });
      fault({ t: tC, key: sit, team: rc, comp: '1', sit, text: blk
        ? 'Il n. ' + who + ' mura il servizio della squadra avversaria.'
        : 'Il n. ' + who + ' attacca la palla di servizio avversaria nella zona d’attacco, quando è interamente sopra il bordo superiore della rete.' });
      R.slow.push(tC - 0.05);
      R.natural = { t: tl, key: 'in', team: sv, land: lp };
      R.endT = tl + 0.2;
      return this.finishRally(R);
    }
    if (serveOut || ace) {
      const land = serveOut ? this.outPoint(rc) : { d: rnd(4, 8.3), l: rnd(-4, 4) };
      const lp = M.w(rc, land.d, land.l);
      ball(t, t + flight, handP, [lp[0], lp[1], BALL_R], 1.6);
      ev({ t: t + flight, kind: 'land', x: lp[0], y: lp[1] });
      // team: for 'in' the court where the ball landed, for 'out' the team that sent it
      R.natural = { t: t + flight, key: serveOut ? 'out' : 'in', team: serveOut ? sv : rc, borderline: !!land.borderline, land: lp, noWhistle: true };
      R.endT = t + flight;
      return this.finishRally(R);
    }
    const rp = M.w(rc, rPoint.d, rPoint.l);
    t += flight;
    ball(tHit, t, handP, [rp[0], rp[1], 0.75], 1.7);
    at(rc, receiver, t - 0.15, rPoint.d, rPoint.l, 'pass');
    at(rc, receiver, t + 0.3, rPoint.d, rPoint.l, 'pass');
    ev({ t, kind: 'contact', team: rc, num: receiver, what: 'reception' });

    // ---- Possessions ----
    let T = rc;
    let touchT = t, touchP = [rp[0], rp[1], 0.75], firstBy = receiver;
    let exchanges = sit ? 1 : (chance(0.55) ? 1 : chance(0.6) ? 2 : 3);
    if (sit === 'block_plus_three' || sit === 'touched_out') exchanges = Math.max(exchanges, 1);
    let blockTouchedBefore = false;
    for (let ex = 0; ex < 6; ex++) {
      const isLast = ex >= exchanges - 1;
      const injectHere = ex === 0; // situations are placed in the first possession
      const s = sit;
      const front = [1, 2, 3].map(k => nAt(T, k));
      const back = [0, 4, 5].map(k => nAt(T, k));
      const setter = players[T].find(n => M.roleOf(T, n) === 'S');
      const L = M.teams[T].libero.num;
      // --- First touch ---
      if (injectHere && s === 'double_first' && T === rc) {
        // Two contacts in the same action at the first team hit: legal (Rule 9.2.3.2)
        ev({ t: touchT + 0.07, kind: 'contact', team: T, num: firstBy, what: 'double-first' });
        R.slow.push(touchT);
        R.trap = { sit: 'double_first', team: T, text: 'Il n. ' + firstBy + ' tocca la palla due volte in ricezione, nella stessa azione.' };
      }
      if (injectHere && s === 'under_net') {
        // The first touch sends the ball completely under the net, into the opponents' court
        const O = other(T);
        const ul = clamp(M.local(T, touchP[0], touchP[1]).l + rnd(-1, 1), -3.5, 3.5);
        const pass = M.w(O, rnd(1.5, 3), ul);
        const tU = touchT + rnd(1.0, 1.2);
        ball(touchT, tU, touchP, [pass[0], pass[1], BALL_R], 0.2, true);
        ev({ t: tU, kind: 'land', x: pass[0], y: pass[1] });
        const uC = touchP[0] / (touchP[0] - pass[0]);
        fault({ t: touchT + (tU - touchT) * uC + 0.05, key: 'under_net', team: T, comp: 'both', sit: s, text: 'Il tocco del n. ' + firstBy + ' manda la palla completamente sotto la rete, nel campo avversario.' });
        R.slow.push(touchT + (tU - touchT) * uC - 0.1);
        R.natural = { t: tU, key: 'in', team: O, land: pass };
        R.endT = tU + 0.3;
        return this.finishRally(R);
      }
      // --- Set ---
      let setBy = setter;
      if (firstBy === setter) setBy = front.find(n => n !== setter) || front[0];
      if (injectHere && s === 'libero_set' && back.includes(L) === false && players[T].includes(L) === false) { /* Libero not on court */ }
      let liberoSets = false;
      if (injectHere && s === 'libero_set' && players[T].includes(L) && firstBy !== L) { setBy = L; liberoSets = true; }
      const setD = liberoSets ? rnd(1.6, 2.6) : 0.8, setL = -1.1;
      const setP3 = (() => { const w = M.w(T, setD, setL); return [w[0], w[1], liberoSets ? 2.2 : 2.55]; })();
      const t1 = touchT + rnd(1.0, 1.25);
      ball(touchT, t1, touchP, setP3, 2.2);
      at(T, setBy, t1 - 0.2, setD + 0.15, setL, 'set');
      at(T, setBy, t1 + 0.3, setD + 0.15, setL, 'set');
      pose(T, setBy, t1 + 0.6, 'ready');
      ev({ t: t1, kind: 'contact', team: T, num: setBy, what: liberoSets ? 'libero-set' : 'set' });
      let setEnd = t1;
      if (injectHere && s === 'catch') {
        R.holdUntil = { t0: t1, t1: t1 + 0.42, p: setP3 };
        setEnd = t1 + 0.42;
        fault({ t: t1 + 0.42, key: 'catch', team: T, comp: '1', sit: s, text: 'Il palleggio del n. ' + setBy + ' ferma la palla prima di rilanciarla.' });
        R.slow.push(t1);
      }
      if (injectHere && s === 'double_contact') {
        const p2 = [setP3[0], setP3[1], setP3[2] + 0.35];
        ball(t1, t1 + 0.22, setP3, p2, 0.05);
        setEnd = t1 + 0.22;
        ev({ t: setEnd, kind: 'contact', team: T, num: setBy, what: 'second-touch' });
        fault({ t: setEnd, key: 'double_contact', team: T, comp: '1', sit: s, text: 'Il n. ' + setBy + ' tocca la palla due volte di seguito in palleggio.' });
        R.slow.push(t1);
      }
      const setStart = s === 'double_contact' && injectHere ? [setP3[0], setP3[1], setP3[2] + 0.35] : setP3;

      // --- Attack (or free ball / fourth touch) ---
      const fourHits = injectHere && s === 'four_hits';
      let attacker, aD, aL, aZ;
      const backAttack = injectHere && s === 'back_attack';
      const liberoAttack = injectHere && s === 'libero_attack' && players[T].includes(L);
      if (liberoAttack) { attacker = L; aD = rnd(1.4, 2.6); aL = rnd(-1.5, 1.5); aZ = NET_H + rnd(0.25, 0.45); }
      else if (backAttack) {
        const bk = back.filter(n => n !== L && n !== setBy && M.roleOf(T, n) !== 'S');
        attacker = bk.length ? pick(bk) : back[0];
        aD = rnd(2.0, 2.7); aL = rnd(-1.2, 1.2); aZ = NET_H + rnd(0.5, 0.8);
      } else {
        const opts = front.filter(n => n !== setBy && n !== L);
        attacker = pick(opts.length ? opts : front);
        const k = kOf(T, attacker);
        aD = 0.7; aL = k === 1 ? -3.3 : k === 3 ? 3.3 : 0.4; aZ = 3.0;
        if (k === 2) { aD = 0.6; aZ = 2.9; }
      }
      let t2 = setEnd + (Math.abs(aL - setL) > 2.5 ? rnd(1.0, 1.2) : rnd(0.55, 0.75));
      const aP = (() => { const w = M.w(T, aD, aL); return [w[0], w[1], aZ]; })();
      if (fourHits) {
        // Third touch is a high pass to a teammate, who sends the ball over with a fourth touch
        const helper = back.find(n => n !== setBy && n !== firstBy) || back[0];
        const hp = M.w(T, 3.2, rnd(-1, 1));
        const third = [hp[0], hp[1], 1.2];
        ball(setEnd, t2, setStart, third, 2.0);
        at(T, helper, t2 - 0.25, 3.2, M.local(T, hp[0], hp[1]).l, 'pass');
        ev({ t: t2, kind: 'contact', team: T, num: helper, what: 'third' });
        const t3 = t2 + rnd(0.9, 1.1);
        const fourth = front.find(n => n !== setBy) || front[0];
        const fp = M.w(T, 1.4, rnd(-1.5, 1.5));
        ball(t2, t3, third, [fp[0], fp[1], 2.4], 1.6);
        at(T, fourth, t3 - 0.2, 1.4, M.local(T, fp[0], fp[1]).l, 'set');
        ev({ t: t3, kind: 'contact', team: T, num: fourth, what: 'fourth' });
        fault({ t: t3, key: 'four_hits', team: T, comp: '1', sit: s, text: 'La ' + M.teams[T].def.label + ' tocca la palla quattro volte prima di rinviarla.' });
        // Free ball over the net and caught by the opponents' reception
        const O = other(T);
        const dp = { d: rnd(4.5, 7), l: rnd(-3, 3) };
        const dw = M.w(O, dp.d, dp.l);
        const t4 = t3 + 1.3;
        ball(t3, t4, [fp[0], fp[1], 2.4], [dw[0], dw[1], 0.75], 2.2);
        const dg = [0, 4, 5].map(k => nAt(O, k))[0];
        at(O, dg, t4 - 0.2, dp.d, dp.l, 'pass');
        R.natural = { t: t4 + 0.6, key: 'four_hits', team: T };
        R.endT = t4 + 0.6;
        return this.finishRally(R);
      }
      if (injectHere && s === 'reach_beyond') {
        // Tight set on the net: an opponent blocker touches the ball in the attackers' space before the attack hit
        const O = other(T);
        const tp = (() => { const w = M.w(T, 0.25, aL); return [w[0], w[1], NET_H + 0.3]; })();
        ball(setEnd, t2, setStart, tp, 1.4);
        at(T, attacker, t2 - 0.6, aD + 1.8, aL, 'walk');
        at(T, attacker, t2 - 0.05, 0.7, aL, 'attack');
        jump(T, attacker, t2 + 0.1, 0.8, 0.85);
        pose(T, attacker, t2 + 0.6, 'ready');
        const oF = [1, 2, 3].map(k => nAt(O, k)).filter(n => n !== M.teams[O].libero.num);
        const who = oF.slice().sort((a, b) => Math.abs((M.pos[O + a] || { y: 0 }).y - tp[1]) - Math.abs((M.pos[O + b] || { y: 0 }).y - tp[1]))[0];
        const lo = clamp(M.local(O, 0, tp[1]).l, -4.2, 4.2);
        at(O, who, t2 - 0.35, 0.3, lo, 'block');
        jump(O, who, t2, 0.75, 0.8);
        pose(O, who, t2 + 0.7, 'ready');
        ev({ t: t2, kind: 'contact', team: O, num: who, what: 'block' });
        const lp = M.w(T, rnd(1, 3), clamp(aL + rnd(-1, 1), -4, 4));
        ball(t2, t2 + 0.45, tp, [lp[0], lp[1], BALL_R], 0.05);
        ev({ t: t2 + 0.45, kind: 'land', x: lp[0], y: lp[1] });
        fault({ t: t2, key: 'reach_beyond', team: O, comp: '1', sit: s, text: 'Il n. ' + who + ' a muro tocca la palla nello spazio avversario prima che il n. ' + attacker + ' possa attaccarla.' });
        R.slow.push(t2 - 0.08);
        R.natural = { t: t2 + 0.45, key: 'in', team: T, land: lp };
        R.endT = t2 + 0.65;
        return this.finishRally(R);
      }
      ball(setEnd, t2, setStart, aP, liberoSets ? 2.4 : 1.9);
      at(T, attacker, t2 - 0.6, aD + 1.8, aL, 'walk');
      at(T, attacker, t2 - 0.05, aD, aL, 'attack');
      jump(T, attacker, t2, aZ - 2.25 > 0 ? Math.min(0.95, aZ - 2.1) : 0.6, 0.85);
      pose(T, attacker, t2 + 0.5, 'ready');
      ev({ t: t2, kind: 'contact', team: T, num: attacker, what: 'attack' });
      if (liberoAttack) fault({ t: t2 + 0.12, key: 'libero_attack', team: T, comp: 'both', sit: s, text: 'Il Libero n. ' + L + ' completa un attacco con la palla interamente sopra il bordo superiore della rete.' });
      if (backAttack) fault({ t: t2 + 0.12, key: 'back_attack', team: T, comp: 'both', sit: s, text: 'Il difensore n. ' + attacker + ' attacca dalla zona d’attacco con la palla sopra il bordo della rete.' });
      if (liberoSets) {
        if (aZ > NET_H + 0.2) fault({ t: t2 + 0.12, key: 'libero_set', team: T, comp: 'both', sit: s, text: 'Il n. ' + attacker + ' attacca sopra la rete una palla alzata in palleggio dal Libero n. ' + L + ' che si trova nella zona d’attacco.' });
      }

      if (injectHere && s === 'antenna') {
        // The attack hits the antenna, then falls outside the court
        const sideL = aL >= 0 ? 4.5 : -4.5;
        const ap = M.w(T, 0, sideL);
        const hitA = [0, ap[1], NET_H + 0.45];
        ball(t2, t2 + 0.28, aP, hitA, 0, true);
        ev({ t: t2 + 0.28, kind: 'net', y: hitA[1], strong: true });
        const fall = [rnd(-1.2, 1.2), ap[1] + Math.sign(ap[1]) * rnd(0.9, 1.6), BALL_R];
        ball(t2 + 0.28, t2 + 0.95, hitA, fall, 0.25, true);
        ev({ t: t2 + 0.95, kind: 'land', x: fall[0], y: fall[1] });
        fault({ t: t2 + 0.28, key: 'antenna', team: T, comp: 'both', sit: s, text: 'L’attacco del n. ' + attacker + ' colpisce l’antenna.' });
        R.slow.push(t2 + 0.2);
        R.natural = { t: t2 + 0.95, key: 'out', team: T, land: fall };
        R.endT = t2 + 1.1;
        return this.finishRally(R);
      }
      // --- Block ---
      const O = other(T);
      const oFront = [1, 2, 3].map(k => nAt(O, k)).filter(n => n !== M.teams[O].libero.num);
      const bY = aP[1];
      const blockers = oFront.slice().sort((a, b) => Math.abs((M.pos[O + a] || { y: 0 }).y - bY) - Math.abs((M.pos[O + b] || { y: 0 }).y - bY)).slice(0, chance(0.5) ? 2 : 1);
      blockers.forEach((n, i) => {
        const l = M.local(O, 0.35, bY + (i ? (i % 2 ? 0.55 : -0.55) : 0)).l;
        at(O, n, t2 - 0.35, 0.35, clamp(l, -4.2, 4.2), 'block');
        jump(O, n, t2 + 0.02, 0.7, 0.8);
        pose(O, n, t2 + 0.7, 'ready');
      });
      if (injectHere && s === 'back_block') {
        // A back-row player runs to the net and takes part in a completed block
        const oBack = [0, 4, 5].map(k => nAt(O, k)).filter(n => n !== M.teams[O].libero.num);
        const bb = pick(oBack);
        const bl = clamp(M.local(O, 0, bY).l + (chance(0.5) ? 0.6 : -0.6), -4.2, 4.2);
        at(O, bb, t2 - 0.9, 3.0, bl, 'walk');
        at(O, bb, t2 - 0.35, 0.35, bl, 'block');
        jump(O, bb, t2 + 0.02, 0.7, 0.8);
        pose(O, bb, t2 + 0.7, 'ready');
        const bp = [0, bY, NET_H + 0.3];
        ball(t2, t2 + 0.08, aP, bp, 0);
        ev({ t: t2 + 0.08, kind: 'block-touch', team: O, num: bb });
        const lp = M.w(T, rnd(1, 3.5), clamp(aL + rnd(-1.5, 1.5), -4, 4));
        ball(t2 + 0.08, t2 + 0.75, bp, [lp[0], lp[1], BALL_R], 0.4);
        ev({ t: t2 + 0.75, kind: 'land', x: lp[0], y: lp[1] });
        fault({ t: t2 + 0.08, key: 'back_block', team: O, comp: 'both', sit: s, text: 'Il difensore n. ' + bb + ' partecipa a un muro completato: la palla tocca il muro.' });
        R.slow.push(t2);
        R.natural = { t: t2 + 0.75, key: 'in', team: T, land: lp };
        R.endT = t2 + 0.9;
        return this.finishRally(R);
      }
      // Net touch or centre line during this action
      if (injectHere && (s === 'net_touch' || s === 'centre_line')) {
        const byBlock = chance(0.55);
        const tm = byBlock ? O : T, who = byBlock ? blockers[0] : attacker;
        const wy = byBlock ? bY : aP[1];
        if (s === 'net_touch') {
          ev({ t: t2 + 0.15, kind: 'net', y: wy, strong: true });
          fault({ t: t2 + 0.15, key: 'net_touch', team: tm, comp: 'both', sit: s, text: 'Il n. ' + who + ' tocca la rete tra le antenne durante l’azione di ' + (byBlock ? 'muro' : 'attacco') + '.' });
        } else {
          // Lands with the whole foot beyond the centre line
          // ...while the ball is still in play: landing over the line after the rally has ended is not a fault (Rule 11.2.3)
          const pIn = M.w(tm, -rnd(0.25, 0.45), M.local(tm, 0, wy).l);
          atW(tm, who, t2 + 0.38, pIn[0], pIn[1], 'stand');
          at(tm, who, t2 + 1.4, 1.0, M.local(tm, 0, wy).l, 'ready');
          fault({ t: t2 + 0.38, key: 'centre_line', team: tm, comp: '2', sit: s, text: 'Il n. ' + who + ' ricade con il piede interamente oltre la linea centrale mentre la palla è ancora in gioco.' });
          R.minLand = t2 + 0.9;
        }
      }

      // --- Outcome of the attack ---
      const tLand = Math.max(t2 + rnd(0.45, 0.7), R.minLand || 0);
      if (injectHere && s === 'block_plus_three') {
        // Block touch: the ball stays on the attackers' side, who then use three more touches (legal: Rule 14.4.1)
        const bp = [0, bY, NET_H + 0.35];
        ball(t2, t2 + 0.1, aP, bp, 0);
        ev({ t: t2 + 0.1, kind: 'block-touch', team: O, num: blockers[0] });
        const cover = M.w(T, rnd(2.5, 4), rnd(-2, 2));
        const coverBy = back.find(n => n !== setBy) || back[0];
        const tc = t2 + 0.8;
        ball(t2 + 0.1, tc, bp, [cover[0], cover[1], 0.7], 0.8);
        at(T, coverBy, tc - 0.15, M.local(T, cover[0], cover[1]).d, M.local(T, cover[0], cover[1]).l, 'pass');
        ev({ t: tc, kind: 'contact', team: T, num: coverBy, what: 'cover' });
        R.trap = { sit: 'block_plus_three', team: T, text: 'Dopo il tocco del muro, la ' + M.teams[T].def.label + ' gioca altri tre tocchi: il tocco di muro non conta.' };
        blockTouchedBefore = true;
        touchT = tc; touchP = [cover[0], cover[1], 0.7]; firstBy = coverBy;
        exchanges = ex + 2;
        continue;
      }
      if (injectHere && s === 'touched_out') {
        const bp = [0, bY + rnd(-0.2, 0.2), NET_H + 0.3];
        ball(t2, t2 + 0.1, aP, bp, 0);
        ev({ t: t2 + 0.1, kind: 'block-touch', team: O, num: blockers[0] });
        R.slow.push(t2 + 0.05);
        const land = this.outPoint(O, true);
        const lp = M.w(O, land.d, land.l);
        const tl = t2 + rnd(0.9, 1.2);
        ball(t2 + 0.1, tl, bp, [lp[0], lp[1], BALL_R], 1.2);
        ev({ t: tl, kind: 'land', x: lp[0], y: lp[1] });
        R.natural = { t: tl, key: 'touched_out', team: O, land: lp };
        R.endT = tl;
        return this.finishRally(R);
      }
      if (!isLast) {
        // Dig by the opponents: the rally goes on
        const dCands = [0, 4, 5].map(k => nAt(O, k));
        const digger = pick(dCands);
        const dp = { d: rnd(4.5, 7.2), l: rnd(-3.2, 3.2) };
        const dw = M.w(O, dp.d, dp.l);
        ball(t2, tLand, aP, [dw[0], dw[1], 0.7], 0.15);
        at(O, digger, tLand - 0.3, dp.d, dp.l, 'pass');
        at(O, digger, tLand + 0.4, dp.d, dp.l, 'pass');
        ev({ t: tLand, kind: 'contact', team: O, num: digger, what: 'dig' });
        T = O; touchT = tLand; touchP = [dw[0], dw[1], 0.7]; firstBy = digger;
        continue;
      }
      // Last attack: kill, out, or close to the line
      let key, land;
      const close = sit === 'no_fault_close' || (!sit && chance(0.2));
      if (close) land = this.closePoint(O);
      else if (chance(0.22)) land = this.outPoint(O);
      else land = { d: rnd(1.5, 8.3), l: rnd(-4.1, 4.1), in: true };
      const lp = M.w(O, land.d, land.l);
      ball(t2, tLand, aP, [lp[0], lp[1], BALL_R], 0.15);
      ev({ t: tLand, kind: 'land', x: lp[0], y: lp[1] });
      key = land.in ? 'in' : 'out';
      R.natural = { t: tLand, key, team: land.in ? O : T, borderline: !!land.borderline, land: lp, dist: land.dist };
      if (land.borderline) R.slow.push(tLand - 0.1);
      R.endT = tLand;
      return this.finishRally(R);
    }
    return this.finishRally(R);
  };

  /** A landing point outside the court of `team` (behind the end line or beyond a side line). */
  Match.prototype.outPoint = function (team, wide) {
    if (chance(0.5) || wide) {
      const side = chance(0.5) ? 1 : -1;
      return { d: rnd(2, 8.5), l: side * (4.5 + BALL_R + rnd(0.4, 2.0)), in: false };
    }
    return { d: 9 + BALL_R + rnd(0.4, 1.8), l: rnd(-4, 4), in: false };
  };

  /**
   * A landing point within a few centimetres of a line. The ball is "in" if any part of it touches the line:
   * with a radius of about 10.5 cm, the centre may be up to that far outside the line.
   */
  Match.prototype.closePoint = function (team) {
    const isIn = chance(0.55);
    // Distance of the ball's centre outside the line (negative = inside)
    const dist = isIn ? rnd(-0.12, BALL_R - 0.02) : rnd(BALL_R + 0.03, BALL_R + 0.16);
    if (chance(0.5)) {
      const side = chance(0.5) ? 1 : -1;
      return { d: rnd(2, 8.3), l: side * (4.5 + dist), in: isIn, borderline: true, dist };
    }
    return { d: 9 + dist, l: rnd(-3.8, 3.8), in: isIn, borderline: true, dist };
  };

  Match.prototype.finishRally = function (R) {
    R.faults.sort((a, b) => a.t - b.t);
    R.events.sort((a, b) => a.t - b.t);
    R.ball.sort((a, b) => a.t0 - b.t0);
    Object.values(R.moves).forEach(tr => { tr.p.sort((a, b) => a.t - b.t); tr.z.sort((a, b) => a.t - b.t); tr.pose.sort((a, b) => a.t - b.t); });
    if (!R.endT) R.endT = R.ball.length ? R.ball[R.ball.length - 1].t1 : R.tHit + 2;
    return R;
  };

  // ======================================================================================
  // Sampling the timeline (positions at time t)
  // ======================================================================================

  function interp(list, t, fx) {
    if (!list.length) return null;
    if (t <= list[0].t) return fx(list[0], list[0], 0);
    for (let i = 1; i < list.length; i++) {
      if (t <= list[i].t) {
        const a = list[i - 1], b = list[i];
        const u = b.t > a.t ? (t - a.t) / (b.t - a.t) : 1;
        return fx(a, b, smooth(u));
      }
    }
    const l = list[list.length - 1];
    return fx(l, l, 0);
  }

  /** Ball position at time t in a rally (null before the toss when the server is holding it). */
  Match.prototype.ballAt = function (R, t) {
    if (R.holdUntil && t >= R.holdUntil.t0 && t <= R.holdUntil.t1) {
      const p = R.holdUntil.p;
      return { x: p[0], y: p[1], z: p[2] - 0.05 * Math.sin((t - R.holdUntil.t0) / (R.holdUntil.t1 - R.holdUntil.t0) * Math.PI), spin: 0 };
    }
    let seg = null;
    for (const s of R.ball) { if (t >= s.t0 && t <= s.t1) seg = s; }
    if (!seg) {
      const last = R.ball[R.ball.length - 1];
      if (last && t > last.t1) {
        // After landing: a small bounce and roll
        const dt = t - last.t1;
        const dir = [last.p1[0] - last.p0[0], last.p1[1] - last.p0[1]];
        const len = Math.hypot(dir[0], dir[1]) || 1;
        const roll = Math.min(2.2, dt * 2.5);
        const bounce = last.p1[2] <= BALL_R + 0.01 ? Math.max(0, Math.sin(Math.min(dt, 0.6) / 0.6 * Math.PI)) * 0.6 * Math.exp(-dt * 2) : 0;
        return { x: last.p1[0] + dir[0] / len * roll, y: last.p1[1] + dir[1] / len * roll, z: Math.max(BALL_R, last.p1[2] + bounce - (last.p1[2] > 0.5 ? Math.min(last.p1[2] - BALL_R, dt * dt * 4.9) : 0)), spin: t * 6 };
      }
      return null;
    }
    const u = (t - seg.t0) / Math.max(1e-6, seg.t1 - seg.t0);
    return {
      x: seg.p0[0] + (seg.p1[0] - seg.p0[0]) * u,
      y: seg.p0[1] + (seg.p1[1] - seg.p0[1]) * u,
      z: seg.p0[2] + (seg.p1[2] - seg.p0[2]) * u + 4 * seg.h * u * (1 - u),
      spin: t * 9,
    };
  };

  /** Player position and pose at time t. */
  Match.prototype.playerAt = function (R, key, t) {
    const tr = R.moves[key];
    if (!tr) return null;
    const p = interp(tr.p, t, (a, b, u) => ({ x: a.x + (b.x - a.x) * u, y: a.y + (b.y - a.y) * u }));
    const z = interp(tr.z, t, (a, b, u) => a.z + (b.z - a.z) * (u < 0.5 ? Math.sin(u * Math.PI / 2) : 1 - (1 - Math.sin(u * Math.PI / 2)) * 0)) || 0;
    let pose = 'ready';
    for (const q of tr.pose) if (q.t <= t) pose = q.pose;
    return { x: p.x, y: p.y, z: Math.max(0, z), pose };
  };

  // ======================================================================================
  // Evaluation
  // ======================================================================================

  /** What really happened in the rally, given when the user whistled (or null if they did not). */
  Match.prototype.truth = function (R, whistleT) {
    const first = R.faults[0] || null;
    if (whistleT !== null && whistleT !== undefined) {
      if (first && first.t <= whistleT + 0.05) return { fault: first, late: whistleT - first.t > 2.5 };
      // Whistled with no fault yet (an 8-second fault only exists from second 8)
      return { wrongWhistle: true };
    }
    if (first) return { fault: first, missed: true };
    return { natural: R.natural };
  };

  /** The decision that should have been made, as { key, point } (point = team that wins the rally). */
  Match.prototype.expected = function (R, tr) {
    if (tr.wrongWhistle) return { key: 'no_fault', point: null };
    if (tr.fault) {
      const f = tr.fault;
      if (f.key === 'serve_early') return { key: 'serve_early', point: null };
      return { key: f.key, point: other(f.team) };
    }
    const n = tr.natural;
    if (!n) return { key: 'double_fault', point: null };
    if (n.key === 'touched_out') return { key: 'touched_out', point: other(n.team) };
    if (n.key === 'serve_net') return { key: 'serve_net', point: other(n.team) };
    if (n.key === 'four_hits') return { key: 'four_hits', point: other(n.team) };
    // in: point to the team that sent the ball (n.team is the team whose court it landed in)
    if (n.key === 'in') return { key: 'in', point: other(n.team) };
    return { key: 'out', point: other(n.team) };
  };

  /** Applies the correct outcome of a rally to the score. */
  Match.prototype.applyRally = function (exp) {
    this.completedRallies++;
    if (exp.point) this.addPoint(exp.point);
  };

  // ======================================================================================

  window.SimEngine = { Match, MAX_SUBS, TEAM_DEF, DECISIONS, REQUEST_DECISIONS, CONDUCT_DECISIONS, COMPETENCE, NET_H, BALL_R, other };
})();
