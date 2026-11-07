/* LetterHop — game logic: dictionary, one-letter moves, shortest routes, puzzles and points. */
(function () {
    'use strict';

    const LEVELS = {easy: 4, medium: 6, hard: 8};   // minimum number of words between start and end
    const LENGTHS = [5, 6];
    const BASE = {easy: 100, medium: 200, hard: 300};
    const WORD_COST = 10, TIP_COST = 15, MIN_POINTS = 10;
    const KEYBOARDS = {
        qwerty: ['QWERTYUIOP', 'ASDFGHJKL', 'ZXCVBNM'],
        azerty: ['AZERTYUIOP', 'QSDFGHJKLM', 'WXCVBN'],
        qwertz: ['QWERTZUIOP', 'ASDFGHJKL', 'YXCVBNM'],
    };
    const keyboardFor = lang => KEYBOARDS[{fr: 'azerty', de: 'qwertz'}[lang] || 'qwerty'];

    const dictionaries = {};

    /** Loads the words of a language: common words come first, so the first is always the most familiar. */
    function load(lang, version) {
        if (!dictionaries[lang]) {
            dictionaries[lang] = fetch('words/' + lang + '.json?v=' + version)
                .then(r => {
                    if (!r.ok) throw new Error('words');
                    return r.json();
                })
                .then(data => {
                    const dict = {lang, puzzles: data.puzzles, byLength: {}};
                    for (const len of LENGTHS) {
                        const words = data.words[len].split(' ');
                        dict.byLength[len] = {
                            words,
                            rank: new Map(words.map((w, i) => [w, i])),
                            buckets: null,
                            routes: new Map()
                        };
                    }
                    return dict;
                })
                .catch(e => {
                    delete dictionaries[lang];
                    throw e;
                });
        }
        return dictionaries[lang];
    }

    const isWord = (dict, word) => dict.byLength[word.length].rank.has(word);

    function differences(a, b) {
        let n = 0;
        for (let i = 0; i < a.length; i++) if (a[i] !== b[i]) n++;
        return n;
    }

    const changedAt = (a, b) => {
        for (let i = 0; i < a.length; i++) if (a[i] !== b[i]) return i;
        return -1;
    };

    function buckets(group) {
        if (group.buckets) return group.buckets;
        const map = new Map();
        for (const w of group.words) {
            for (let i = 0; i < w.length; i++) {
                const key = w.slice(0, i) + '_' + w.slice(i + 1);
                const list = map.get(key);
                if (list) list.push(w); else map.set(key, [w]);
            }
        }
        group.buckets = map;
        return map;
    }

    function neighbours(group, word) {
        const map = buckets(group), out = [];
        for (let i = 0; i < word.length; i++) {
            for (const w of map.get(word.slice(0, i) + '_' + word.slice(i + 1))) if (w !== word) out.push(w);
        }
        return out;
    }

    /** How many changes separate every word from the target. */
    function distances(group, target) {
        let dist = group.routes.get(target);
        if (dist) return dist;
        dist = new Map([[target, 0]]);
        let front = [target];
        while (front.length) {
            const next = [];
            for (const u of front) {
                const d = dist.get(u) + 1;
                for (const v of neighbours(group, u)) if (!dist.has(v)) {
                    dist.set(v, d);
                    next.push(v);
                }
            }
            front = next;
        }
        group.routes.clear();
        group.routes.set(target, dist);
        return dist;
    }

    /** The most familiar word that gets one change closer to the target, avoiding the words in `used`. */
    function nextStep(dict, from, target, used) {
        const group = dict.byLength[from.length];
        const dist = distances(group, target);
        const here = dist.get(from);
        if (!here) return null;
        const best = list => list.sort((a, b) => group.rank.get(a) - group.rank.get(b))[0] || null;
        const all = neighbours(group, from);
        const closer = all.filter(w => dist.get(w) === here - 1);
        return best(closer.filter(w => !used || !used.includes(w))) || best(closer);
    }

    function route(dict, from, target) {
        const path = [from];
        while (path[path.length - 1] !== target) path.push(nextStep(dict, path[path.length - 1], target));
        return path;
    }

    /** A puzzle not played recently: `recent` holds "start end" keys. */
    function pickPuzzle(dict, len, level, recent) {
        const list = dict.puzzles[len][LEVELS[level]];
        const seen = new Set(recent || []);
        const fresh = list.filter(p => !seen.has(p));
        const pool = fresh.length ? fresh : list;
        const key = pool[Math.floor(Math.random() * pool.length)];
        const [start, target] = key.split(' ');
        // Either direction is the same puzzle: half of the games are played backwards
        return Math.random() < 0.5 ? {key, start, target} : {key, start: target, target: start};
    }

    /** Every word over the minimum and every tip costs points: the shortest chain is worth the most. */
    function score(level, words, tips) {
        const extra = Math.max(0, words - LEVELS[level]);
        const wordPenalty = extra * WORD_COST, tipPenalty = tips * TIP_COST;
        const points = Math.max(MIN_POINTS, BASE[level] - wordPenalty - tipPenalty);
        const stars = extra === 0 && tips === 0 ? 3 : extra <= 2 ? 2 : 1;
        return {base: BASE[level], extra, wordPenalty, tipPenalty, points, stars};
    }

    /** A tip must be paid for: it is available only if the points left stay at the minimum or above. */
    function canTip(level, words, tips) {
        const s = score(level, words, tips);
        return s.base - s.wordPenalty - s.tipPenalty - TIP_COST >= MIN_POINTS;
    }

    window.LetterHopEngine = {
        LEVELS,
        LENGTHS,
        BASE,
        WORD_COST,
        TIP_COST,
        keyboardFor,
        load,
        isWord,
        differences,
        changedAt,
        nextStep,
        route,
        pickPuzzle,
        score,
        canTip
    };
})();
