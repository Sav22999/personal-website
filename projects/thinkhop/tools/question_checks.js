/* The checks of check_questions.js, without Node: they also run in any other JavaScript shell. */
(function () {
    'use strict';

    function run(log, quit) {
        const E = globalThis.ThinkHopEngine, I = globalThis.ThinkHopI18n;
        const LANGS = ['en', 'it', 'es', 'fr', 'de'];
        const RUNS = 300;
        const errors = [];
        const bad = (q, why) => errors.push(why + ': ' + JSON.stringify(q).slice(0, 300));

        // Every key used by a question must have a text in every language
        const texts = (x, q) => {
            if (!x || typeof x !== 'object') return;
            if (Array.isArray(x)) return x.forEach(y => texts(y, q));
            if (typeof x.k === 'string') LANGS.forEach(l => {
                I.setLang(l);
                if (I.t(x.k) === x.k) bad(q, `no text for ${x.k} in ${l}`);
            });
            Object.values(x).forEach(y => texts(y, q));
        };

        function checkSign(q) {
            const right = q.options[q.answer].split(/\s+/);
            if (E.evaluate(q.nums, right) !== q.result) bad(q, 'the sign does not give the result');
            q.options.forEach((o, i) => {
                if (i !== q.answer && E.evaluate(q.nums, o.split(/\s+/)) === q.result) bad(q, 'two signs give the result');
            });
        }

        function check(q) {
            const keys = q.options.map(o => JSON.stringify(o));
            if (new Set(keys).size !== keys.length) bad(q, 'two equal options');
            if (q.options.length < 3 || q.options.length > 5) bad(q, 'wrong number of options');
            if (!(q.answer >= 0 && q.answer < q.options.length)) bad(q, 'no right answer');
            if (!(q.d >= 0 && q.d <= E.MAX_D)) bad(q, 'difficulty out of range');
            if (!(q.limit >= 25 && q.limit <= 150)) bad(q, 'odd time limit ' + q.limit);
            if (q.options.some(o => o === undefined || o === 'undefined' || o === 'NaN')) bad(q, 'empty option');
            if (JSON.stringify(q).includes('undefined') || JSON.stringify(q).includes('NaN')) bad(q, 'undefined in the data');
            if (q.pk) texts({k: q.pk}, q);
            texts([q.pv, q.facts, q.ask, q.why, q.options], q);
            if (q.cat === 'sign') checkSign(q);
            if (q.pk && q.pk.startsWith('tr_') && !Number.isInteger(+q.options[q.answer])) bad(q, 'a trap with an odd answer');
            if (q.why.some(w => w.k === 'why_nz_null')) bad(q, 'contradictory statements');
            if (globalThis.ThinkHopTwemoji) {
                const dump = JSON.stringify(q);
                const re = globalThis.ThinkHopTwemoji.EMOJI_RE;
                re.lastIndex = 0;
                let m;
                while ((m = re.exec(dump)) !== null) {
                    if (m[0] !== '★' && !globalThis.ThinkHopTwemoji.has(m[0])) bad(q, 'missing Twemoji SVG for ' + m[0]);
                }
            }
        }

        const patterns = E.allPatterns();
        const cats = {};
        patterns.forEach(p => {
            cats[p.cat] = (cats[p.cat] || 0) + 1;
            const seen = new Set();
            const runs = p.fixed ? 8 : RUNS;
            for (let i = 0; i < runs; i++) {
                const q = E.make(p.id, i * 2654435 + 7);
                check(q);
                seen.add(q.sig);
            }
            if (!p.fixed) p.variety = seen.size;
            if (p.fixed && p.cat === 'riddle') {
                const r = globalThis.ThinkHopRiddles.find(x => x.id === p.id);
                LANGS.forEach(l => {
                    const t = r.t[l];
                    if (!t || !t[0] || !t[1]) bad(r.id, 'riddle incomplete in ' + l);
                    if (!r.o && (!t[2] || t[2].length !== 4 || new Set(t[2]).size !== 4)) bad(r.id, 'riddle options in ' + l);
                });
                if (r.o && new Set(r.o).size !== 4) bad(r.id, 'riddle options');
            }
        });
        const mockH = (tag, ...kids) => ({tag, kids: kids.flat(Infinity).filter(Boolean)});
        for (const c of E.CATS) {
            if (!cats[c]) bad(c, 'a type without patterns');
            if (globalThis.ThinkHopHelp && !globalThis.ThinkHopHelp.has(c)) bad(c, 'missing help scene');
            LANGS.forEach(l => {
                I.setLang(l);
                if (I.t('cat_' + c) === 'cat_' + c) bad(c, 'no name in ' + l);
                if (globalThis.ThinkHopHelp) {
                    ['goal', 's1', 's2', 's3'].forEach(k => {
                        const key = `help_${c}_${k}`;
                        const val = I.t(key);
                        if (val === key) bad(c, `no help text ${key} in ${l}`);
                        if (globalThis.ThinkHopTwemoji) {
                            const re = globalThis.ThinkHopTwemoji.EMOJI_RE;
                            re.lastIndex = 0;
                            let m;
                            while ((m = re.exec(val)) !== null) {
                                if (!globalThis.ThinkHopTwemoji.has(m[0])) bad(c, `missing Twemoji SVG in ${key} for ${m[0]}`);
                            }
                        }
                    });
                    for (let s = 0; s < 3; s++) {
                        const tree = JSON.stringify(globalThis.ThinkHopHelp.renderStep(c, s, mockH));
                        if (tree.includes('undefined') || tree.includes('NaN')) bad(c, `undefined in help scene step ${s}`);
                        if (globalThis.ThinkHopTwemoji) {
                            const re = globalThis.ThinkHopTwemoji.EMOJI_RE;
                            re.lastIndex = 0;
                            let m;
                            while ((m = re.exec(tree)) !== null) {
                                if (!globalThis.ThinkHopTwemoji.has(m[0])) bad(c, `missing Twemoji SVG in help scene for ${m[0]}`);
                            }
                        }
                    }
                }
            });
        }

        // A player going through all the levels, keeping the memory of what was seen
        let seen = [], repeats = 0, drops = 0, worst = 0, math = 0, total = 0, last = 0;
        const shares = {}, firstD = [], lastD = [];
        for (let s = 1; s <= E.STAGES; s++) {
            const g = E.newGame('level', s, s * 7919, seen);
            if (g.questions.length !== E.settings.levelLength) bad(s, 'wrong level length');
            const avg = g.questions.reduce((a, q) => a + q.d, 0) / g.questions.length;
            if (avg < last) worst = Math.max(worst, last - avg), drops++;
            last = avg;
            if (s === 1) firstD.push(...g.questions.map(q => q.d));
            if (s === E.STAGES) lastD.push(...g.questions.map(q => q.d));
            for (let i = 1; i < g.questions.length; i++) if (g.questions[i].d < g.questions[i - 1].d) bad(g.questions, 'a level goes down');
            g.questions.forEach(q => {
                if (seen.includes(q.sig)) repeats++;
                seen.push(q.sig);
                shares[q.cat] = (shares[q.cat] || 0) + 1;
                total++;
                if (E.MATH.includes(q.cat)) math++;
            });
            if (new Set(g.questions.map(q => q.cat)).size < g.questions.length - 1) bad(g.questions.map(q => q.cat), 'a level with little variety');
        }
        if (repeats) bad(repeats, 'repeated questions over all the levels');

        // Survival and daily
        const sv = E.newGame('survival', 0, 12345, []);
        for (let i = 0; i < 40; i++) E.next(sv, sv.questions.map(q => q.sig));
        if (new Set(sv.questions.map(q => q.sig)).size !== sv.questions.length) bad(0, 'survival repeats');
        const d1 = E.newGame('daily', 0, 1, [], '2026-11-07'), d2 = E.newGame('daily', 0, 2, ['x|1'], '2026-11-07');
        if (JSON.stringify(d1.questions.map(q => q.sig)) !== JSON.stringify(d2.questions.map(q => q.sig))) bad(0, 'the daily challenge differs between players');

        const low = patterns.filter(p => p.variety && p.variety < 5).map(p => `${p.id}:${p.variety}`);
        log(`patterns: ${patterns.length}, riddles: ${cats.riddle}, questions checked: ${patterns.reduce((a, p) => a + (p.fixed ? 8 : RUNS), 0)}`);
        log(`levels: first ${firstD.join(' ')} · last ${lastD.join(' ')} · averages going down ${drops} times, by ${worst.toFixed(2)} at most`);
        log(`maths share ${(math / total * 100).toFixed(0)}% · ${Object.entries(shares).sort((a, b) => b[1] - a[1]).map(([c, n]) => c + ' ' + n).join(', ')}`);
        if (low.length) log('few variants: ' + low.join(' '));
        log(errors.length ? `${errors.length} ERRORS\n` + [...new Set(errors)].slice(0, 25).join('\n') : 'all fair');
        if (errors.length) quit(1);
    }

    if (typeof module !== 'undefined') module.exports = run;
    globalThis.ThinkHopChecks = run;
})();
