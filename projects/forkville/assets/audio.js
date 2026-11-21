/*
 * Forkville — music and sounds, all synthesized live with Web Audio (no audio files).
 *
 * Music: a gentle loop (electric piano chords, bass, soft drums, a pentatonic melody) that turns calmer at night,
 * with its own feel for each kind of restaurant; it fades right down while the restaurant is closed or the game paused.
 * Ambience: rain when it rains, a murmur of voices that grows with the guests seated (none when nobody is there), and
 * the work you can hear only while it is being done: frying and chopping while a chef is cooking, running water while
 * someone washes up, a broom while someone sweeps. Nobody at work, no sound.
 * Effects: coins, the door bell, frying, plates, sparkles, pops, clicks, the level-up fanfare.
 * Browsers start audio only after a tap: start() is called on the first pointer press.
 */

const prefs = (() => {
    try {
        return {music: true, sfx: true, volume: 0.6, ...JSON.parse(localStorage.getItem('forkville-audio') || '{}')};
    } catch (e) {
        return {music: true, sfx: true, volume: 0.6};
    }
})();

let ctx = null, master, musicBus, sfxBus, ambBus, noiseBuf;
let rainGain, crowdGain, crowdFilter, fryGain, waterGain;
let nextBar = 0, bar = 0, timer = null;
let night = false, cooking = 0, sweeping = 0, kind = 'generic', quiet = false;

export const audioPrefs = () => ({...prefs});

export function setAudioPrefs(p) {
    Object.assign(prefs, p);
    try {
        localStorage.setItem('forkville-audio', JSON.stringify(prefs));
    } catch (e) { /* storage blocked */ }
    if (!ctx) return;
    master.gain.setTargetAtTime(prefs.volume, ctx.currentTime, 0.1);
    musicBus.gain.setTargetAtTime(prefs.music ? 0.32 : 0, ctx.currentTime, 0.3);
    sfxBus.gain.setTargetAtTime(prefs.sfx ? 0.8 : 0, ctx.currentTime, 0.05);
    ambBus.gain.setTargetAtTime(prefs.sfx ? 0.5 : 0, ctx.currentTime, 0.3);
}

/** Creates the audio graph (on the first tap). */
export function start() {
    if (ctx) {
        if (ctx.state === 'suspended') ctx.resume();
        return;
    }
    const AC = window.AudioContext || window.webkitAudioContext;
    if (!AC) return;
    ctx = new AC();
    master = ctx.createGain();
    master.gain.value = prefs.volume;
    const comp = ctx.createDynamicsCompressor();
    master.connect(comp).connect(ctx.destination);
    musicBus = gainTo(master, prefs.music ? 0.32 : 0);
    sfxBus = gainTo(master, prefs.sfx ? 0.8 : 0);
    ambBus = gainTo(master, prefs.sfx ? 0.5 : 0);
    // White noise, reused by drums, rain, frying and the crowd
    noiseBuf = ctx.createBuffer(1, ctx.sampleRate * 2, ctx.sampleRate);
    const d = noiseBuf.getChannelData(0);
    for (let i = 0; i < d.length; i++) d[i] = Math.random() * 2 - 1;
    // Rain: high filtered noise
    rainGain = loopNoise(ambBus, 'highpass', 1800, 0);
    // Crowd: band-passed noise that wobbles like voices
    crowdGain = ctx.createGain();
    crowdGain.gain.value = 0;
    crowdFilter = ctx.createBiquadFilter();
    crowdFilter.type = 'bandpass';
    crowdFilter.frequency.value = 600;
    crowdFilter.Q.value = 1.4;
    const src = ctx.createBufferSource();
    src.buffer = noiseBuf;
    src.loop = true;
    src.connect(crowdFilter).connect(crowdGain).connect(ambBus);
    src.start();
    const lfo = ctx.createOscillator();
    const lfoGain = ctx.createGain();
    lfo.frequency.value = 0.7;
    lfoGain.gain.value = 250;
    lfo.connect(lfoGain).connect(crowdFilter.frequency);
    lfo.start();
    // Frying (crackling high noise) and running water (low rushing noise): their level follows the work being done
    fryGain = loopNoise(ambBus, 'bandpass', 4200, 0);
    waterGain = loopNoise(ambBus, 'lowpass', 900, 0);
    nextBar = ctx.currentTime + 0.2;
    timer = setInterval(schedule, 120);
}

function gainTo(dest, v) {
    const g = ctx.createGain();
    g.gain.value = v;
    g.connect(dest);
    return g;
}

function loopNoise(dest, type, freq, gain) {
    const src = ctx.createBufferSource();
    src.buffer = noiseBuf;
    src.loop = true;
    const f = ctx.createBiquadFilter();
    f.type = type;
    f.frequency.value = freq;
    const g = ctx.createGain();
    g.gain.value = gain;
    src.connect(f).connect(g).connect(dest);
    src.start();
    return g;
}

/**
 * Weather, time of day, how busy the room is and who is working right now (called twice a second): cooks = chefs at
 * the stove this moment, washers = people at a sink, sweepers = people with a broom or a mop; open = guests welcome;
 * paused = the game clock stopped.
 */
export function setAmbience({rain = false, snow = false, guests = 0, isNight = false, cooks = 0, washers = 0, sweepers = 0, open = true, paused = false, restaurantKind = 'generic'}) {
    if (!ctx) return;
    night = isNight;
    cooking = paused ? 0 : cooks;
    sweeping = paused ? 0 : sweepers;
    kind = SONGS[restaurantKind] ? restaurantKind : 'generic';
    const t = ctx.currentTime;
    rainGain.gain.setTargetAtTime(rain ? 0.22 : snow ? 0.03 : 0, t, 1.2);
    crowdGain.gain.setTargetAtTime(paused ? 0 : Math.min(0.18, guests * 0.025), t, 1.5);
    fryGain.gain.setTargetAtTime(cooking ? Math.min(0.09, 0.035 + cooking * 0.02) : 0, t, cooking ? 0.25 : 0.4);
    waterGain.gain.setTargetAtTime(!paused && washers ? Math.min(0.12, 0.06 + washers * 0.03) : 0, t, 0.3);
    // Closed, or paused: the music almost stops (a few soft notes), so the quiet is clear
    quiet = !open || paused;
    if (prefs.music) musicBus.gain.setTargetAtTime(quiet ? 0.07 : 0.32, t, 1.5);
}

// ---------------------------------------------------------------- music

const DAY = {tempo: 98, chords: [[60, 64, 67, 71], [57, 60, 64, 67], [53, 57, 60, 64], [55, 59, 62, 65]]};   // Cmaj7 Am7 Fmaj7 G7
const NIGHT = {tempo: 74, chords: [[57, 60, 64, 67], [53, 57, 60, 64], [48, 52, 55, 59], [52, 55, 59, 62]]};  // Am7 Fmaj7 Cmaj7 Em7
/** Each kind of restaurant has its own day song: a bistro swings, fast food bounces, an inn is folky, luxury is slow. */
const SONGS = {
    generic: DAY,
    fast_food: {tempo: 118, chords: [[60, 64, 67, 72], [65, 69, 72, 77], [67, 71, 74, 79], [65, 69, 72, 77]], drums: 'busy'},   // C F G F
    cafe: {tempo: 88, chords: [[62, 65, 69, 72], [55, 59, 62, 65], [60, 64, 67, 71], [57, 60, 64, 67]], drums: 'brush'},      // Dm7 G7 Cmaj7 Am7
    inn: {tempo: 92, chords: [[55, 59, 62, 67], [60, 64, 67, 72], [62, 66, 69, 74], [55, 59, 62, 67]], drums: 'folk'},       // G C D G
    luxury: {tempo: 70, chords: [[60, 64, 67, 71], [62, 65, 69, 72], [64, 67, 71, 74], [57, 60, 64, 67]], drums: 'none'},      // Cmaj7 Dm7 Em7 Am7
};
const PENTA = [0, 2, 4, 7, 9];
const hz = (m) => 440 * Math.pow(2, (m - 69) / 12);

function schedule() {
    if (!ctx || ctx.state !== 'running') return;
    workSounds();
    while (nextBar < ctx.currentTime + 0.6) {
        playBar(nextBar);
        const song = night ? NIGHT : SONGS[kind];
        nextBar += 4 * 60 / song.tempo;
        bar++;
    }
}

/** Short sounds of the work going on (every 120 ms): knife taps on the board while cooking, broom swishes while sweeping. */
function workSounds() {
    if (!prefs.sfx) return;
    const t = ctx.currentTime + 0.02;
    if (cooking && Math.random() < 0.22 * Math.min(2, cooking)) // chopping: two or three quick taps
        for (let i = 0, n = 2 + Math.floor(Math.random() * 2); i < n; i++) noise(t + i * 0.11, 0.025, 'bandpass', 2400 + Math.random() * 600, 0.05, ambBus, 3);
    if (cooking && Math.random() < 0.08) noise(t, 0.18, 'highpass', 5200, 0.03, ambBus, 1); // a pop of hot oil
    if (sweeping && Math.random() < 0.18 * Math.min(2, sweeping)) noise(t, 0.28, 'bandpass', 1500 + Math.random() * 500, 0.045, ambBus, 0.8);
}

function playBar(t0) {
    const song = night ? NIGHT : SONGS[kind];
    const beat = 60 / song.tempo;
    const chord = song.chords[bar % 4];
    if (quiet) { // closed or paused: one soft chord, nothing else
        for (const m of chord) epiano(hz(m), t0, beat * 3.5, 0.03);
        return;
    }
    // Electric piano: chord on beats 1 and 3 (softly offbeat at night)
    for (const b of night ? [0, 2.5] : [0, 1.5, 2, 3.5]) for (const m of chord) epiano(hz(m), t0 + b * beat, beat * (night ? 1.6 : 0.9), 0.05);
    // Bass: root and fifth
    const root = chord[0] - 24;
    [[0, root], [2, root + 7], [3, root + (bar % 2 ? 5 : 7)]].forEach(([b, m]) => bass(hz(m), t0 + b * beat, beat * 0.9));
    // Drums: soft kick, brushed hats (only by day); each kind of restaurant has its own groove
    const groove = night ? 'night' : song.drums || 'soft';
    if (groove === 'soft' || groove === 'busy' || groove === 'folk') {
        kick(t0);
        kick(t0 + 2 * beat);
        if (groove === 'busy') kick(t0 + 2.5 * beat);
        for (let i = 0; i < 8; i++) hat(t0 + i * beat / 2, i % 2 ? (groove === 'busy' ? 0.035 : 0.025) : 0.04);
        if (groove === 'folk') for (const b of [1, 3]) noise(t0 + b * beat, 0.08, 'bandpass', 1800, 0.05, musicBus, 1.2); // a clap
    } else if (groove === 'brush') for (let i = 0; i < 4; i++) noise(t0 + i * beat, 0.18, 'bandpass', 5000, 0.018, musicBus, 0.7);
    else if (groove === 'night') hat(t0 + 1 * beat, 0.02);
    // A little melody: notes of the pentatonic scale, seeded by the bar so it repeats every 8 bars
    const seed = bar % 8;
    for (let i = 0; i < (night ? 3 : 5); i++) {
        const r = Math.abs(Math.sin(seed * 9.1 + i * 3.7));
        if (r < 0.35) continue;
        const note = chord[0] + 12 + PENTA[Math.floor(r * 10) % 5];
        bell(hz(note), t0 + (i * (night ? 1.3 : 0.75) + 0.25) * beat, beat * 0.8, 0.045);
    }
}

function env(g, t, a, peak, d) {
    g.gain.setValueAtTime(0.0001, t);
    g.gain.exponentialRampToValueAtTime(peak, t + a);
    g.gain.exponentialRampToValueAtTime(0.0001, t + a + d);
}

function osc(type, f, t, dur, peak, dest, attack = 0.01, detune = 0) {
    const o = ctx.createOscillator();
    const g = ctx.createGain();
    o.type = type;
    o.frequency.value = f;
    o.detune.value = detune;
    env(g, t, attack, peak, dur);
    o.connect(g).connect(dest);
    o.start(t);
    o.stop(t + attack + dur + 0.05);
    return o;
}

const epiano = (f, t, d, v) => {
    osc('sine', f, t, d, v, musicBus, 0.01);
    osc('triangle', f * 2, t, d * 0.4, v * 0.25, musicBus, 0.005, 4);
};
const bass = (f, t, d) => osc('triangle', f, t, d, 0.12, musicBus, 0.01);
const bell = (f, t, d, v) => {
    osc('sine', f, t, d, v, musicBus, 0.005);
    osc('sine', f * 3, t, d * 0.3, v * 0.2, musicBus, 0.005);
};

function kick(t) {
    const o = ctx.createOscillator();
    const g = ctx.createGain();
    o.frequency.setValueAtTime(120, t);
    o.frequency.exponentialRampToValueAtTime(45, t + 0.15);
    env(g, t, 0.005, 0.18, 0.18);
    o.connect(g).connect(musicBus);
    o.start(t);
    o.stop(t + 0.3);
}

function noise(t, dur, type, freq, peak, dest, q = 1) {
    const src = ctx.createBufferSource();
    src.buffer = noiseBuf;
    const f = ctx.createBiquadFilter();
    f.type = type;
    f.frequency.value = freq;
    f.Q.value = q;
    const g = ctx.createGain();
    env(g, t, 0.003, peak, dur);
    src.connect(f).connect(g).connect(dest);
    src.start(t, Math.random());
    src.stop(t + dur + 0.05);
}

const hat = (t, v) => noise(t, 0.05, 'highpass', 7000, v, musicBus);

// ---------------------------------------------------------------- sound effects

const now = () => ctx.currentTime + 0.01;
const ready = () => ctx && ctx.state === 'running' && prefs.sfx;

export const sfx = {
    thunder() { // a distant rumble after a lightning flash
        if (!ready()) return;
        const t = now() + 0.4 + Math.random() * 0.8;
        noise(t, 1.6, 'lowpass', 180, 0.5, sfxBus, 0.7);
        noise(t + 0.05, 0.5, 'lowpass', 600, 0.18, sfxBus, 0.5);
        noise(t + 0.6, 1.2, 'lowpass', 120, 0.3, sfxBus, 0.7);
    },
    coin() {
        if (!ready()) return;
        const t = now();
        osc('square', 988, t, 0.08, 0.06, sfxBus, 0.003);
        osc('square', 1319, t + 0.07, 0.22, 0.06, sfxBus, 0.003);
    },
    bell() { // the door bell when someone comes in
        if (!ready()) return;
        const t = now();
        [1568, 2093].forEach((f, i) => {
            osc('sine', f, t + i * 0.09, 0.6, 0.07, sfxBus, 0.002);
            osc('sine', f * 2.76, t + i * 0.09, 0.25, 0.015, sfxBus, 0.002);
        });
    },
    sizzle() { // frying on the stove
        if (!ready()) return;
        const t = now();
        for (let i = 0; i < 6; i++) noise(t + i * 0.09 + Math.random() * 0.05, 0.12, 'bandpass', 3500 + Math.random() * 2000, 0.05, sfxBus, 2);
    },
    ding() { // the dish lift sets off
        if (!ready()) return;
        const t = now();
        osc('sine', 1318, t, 0.5, 0.05, sfxBus, 0.002);
        osc('sine', 1046, t + 0.12, 0.6, 0.045, sfxBus, 0.002);
    },
    clink() { // a plate on the table
        if (!ready()) return;
        const t = now();
        osc('triangle', 2600, t, 0.12, 0.05, sfxBus, 0.002);
        osc('sine', 3900, t + 0.01, 0.1, 0.03, sfxBus, 0.002);
    },
    sparkle() {
        if (!ready()) return;
        const t = now();
        [1760, 2217, 2637, 3520].forEach((f, i) => osc('sine', f, t + i * 0.05, 0.18, 0.04, sfxBus, 0.003));
    },
    pop() {
        if (!ready()) return;
        const t = now();
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.frequency.setValueAtTime(500, t);
        o.frequency.exponentialRampToValueAtTime(900, t + 0.06);
        env(g, t, 0.003, 0.05, 0.07);
        o.connect(g).connect(sfxBus);
        o.start(t);
        o.stop(t + 0.12);
    },
    click() {
        if (!ready()) return;
        osc('triangle', 660, now(), 0.04, 0.035, sfxBus, 0.002);
    },
    angry() {
        if (!ready()) return;
        const t = now();
        osc('sawtooth', 220, t, 0.18, 0.04, sfxBus, 0.01);
        osc('sawtooth', 185, t + 0.16, 0.25, 0.04, sfxBus, 0.01);
    },
    cheer() {
        if (!ready()) return;
        const t = now();
        [784, 988, 1175].forEach((f, i) => osc('triangle', f, t + i * 0.06, 0.2, 0.05, sfxBus, 0.004));
    },
    levelUp() { // a short fanfare
        if (!ready()) return;
        const t = now();
        [523, 659, 784, 1047, 784, 1047].forEach((f, i) => {
            osc('square', f, t + i * 0.11, i === 5 ? 0.6 : 0.12, 0.05, sfxBus, 0.004);
            osc('triangle', f / 2, t + i * 0.11, i === 5 ? 0.6 : 0.12, 0.05, sfxBus, 0.004);
        });
    },
    cash() { // buying something
        if (!ready()) return;
        const t = now();
        noise(t, 0.06, 'bandpass', 2500, 0.06, sfxBus, 3);
        osc('square', 1175, t + 0.06, 0.15, 0.05, sfxBus, 0.003);
        osc('square', 1568, t + 0.12, 0.25, 0.05, sfxBus, 0.003);
    },
};
