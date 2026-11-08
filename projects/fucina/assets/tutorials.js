/* Fucina — the first game of each kind starts with a short guided round, made with the pieces of the game itself.
 * Each step shows something and, when it has a task, waits for the learner to do it before going on. */
(function () {
    'use strict';

    const {h, btn, icon} = UI;
    const t = I18N.t;
    const wait = ms => new Promise(r => setTimeout(r, ms));

    /** Marks what to tap with a pulsing ring; clear() removes every mark. */
    const hint = el => el && el.classList.add('tut-hint');
    const clear = box => box.querySelectorAll('.tut-hint').forEach(x => x.classList.remove('tut-hint'));

    function shake(el) {
        el.classList.remove('shake');
        void el.offsetWidth;
        el.classList.add('shake');
    }

    // ---------------------------------------------------------------- Who's the tag?

    function whotag() {
        const tags = ['h1', 'p', 'a', 'img', 'ul', 'span'];
        let out = new Set();
        const cards = box => {
            const wrap = h('div.tag-cards.tut-cards');
            tags.forEach((tag, i) => wrap.appendChild(h('button.tag-card.tut-pop' + (out.has(tag) ? '.out' : ''), {type: 'button', style: {animationDelay: i * 70 + 'ms'}, 'data-tag': tag}, `<${tag}>`)));
            box.appendChild(wrap);
            return wrap;
        };
        const question = (box, label, drop, done) => {
            const chip = h('button.chip-btn', {type: 'button'}, label);
            const answer = h('ul.qa-log');
            const wrap = cards(box);
            box.append(h('div.qa', chip), answer);
            hint(chip);
            chip.onclick = async () => {
                clear(box);
                chip.disabled = true;
                answer.prepend(h('li.tut-pop', h('span', label), h('b', t('Yes'))));
                await wait(350);
                drop.forEach(tag => {
                    out.add(tag);
                    const c = wrap.querySelector(`[data-tag="${tag}"]`);
                    c.classList.add('out');
                    c.disabled = true;
                });
                done();
            };
        };
        return [
            {text: t('A tag is hidden among these cards. You have to find out which one.'), build: box => cards(box)},
            {text: t('Ask a question: tap it.'), ok: t('The answer knocks down the cards that do not match.'), build: (box, done) => question(box, t('Is it a block element?'), ['a', 'img', 'span'], done)},
            {text: t('One more question.'), ok: t('Only one card is left standing.'), build: (box, done) => question(box, t('Is it used in lists?'), ['h1', 'p'], done)},
            {
                text: t('Now you are sure: tap the hidden tag.'), ok: t('Found it! The fewer questions you use, the more stars you win.'), build: (box, done) => {
                    const wrap = cards(box);
                    const ul = wrap.querySelector('[data-tag="ul"]');
                    hint(ul);
                    ul.onclick = () => {
                        clear(box);
                        ul.classList.add('tut-win');
                        Fun.confetti();
                        done();
                    };
                }
            },
        ];
    }

    // ---------------------------------------------------------------- Tagdoku

    function tagdoku() {
        const grid = [['h1', 'p', 'a', 'ul'], ['a', 'ul', 'h1', 'p'], ['p', 'h1', 'ul', 'a'], ['ul', 'a', 'p', 'h1']];
        const holes = [[0, 2], [3, 0]];
        let filled = new Set();
        const draw = box => {
            const g = h('div.doku.tut-doku', {style: {gridTemplateColumns: 'repeat(4, 1fr)'}});
            grid.forEach((row, r) => row.forEach((v, c) => {
                const hole = holes.some(([a, b]) => a === r && b === c) && !filled.has(r + ',' + c);
                g.appendChild(h('button.dk' + (hole ? '' : '.given') + (c === 1 ? '.edge-r' : '') + (r === 1 ? '.edge-b' : ''), {type: 'button', 'data-rc': r + ',' + c}, hole ? '' : v));
            }));
            box.appendChild(g);
            return g;
        };
        const flash = async (g, cells) => {
            cells.forEach(([r, c]) => g.querySelector(`[data-rc="${r},${c}"]`).classList.add('tut-glow'));
            await wait(900);
            g.querySelectorAll('.tut-glow').forEach(x => x.classList.remove('tut-glow'));
        };
        return [
            {
                text: t('Each tag goes once in every row, once in every column and once in every box.'), build: async box => {
                    const g = draw(box);
                    await wait(400);
                    await flash(g, [0, 1, 2, 3].map(c => [1, c]));
                    await flash(g, [0, 1, 2, 3].map(r => [r, 2]));
                    await flash(g, [[0, 0], [0, 1], [1, 0], [1, 1]]);
                }
            },
            {
                text: t('Tap the empty square of the first row.'), ok: t('Selected: now choose what goes in it.'), build: (box, done) => {
                    const g = draw(box);
                    const cell = g.querySelector('[data-rc="0,2"]');
                    hint(cell);
                    cell.onclick = () => {
                        clear(box);
                        cell.classList.add('sel');
                        done();
                    };
                }
            },
            {
                text: t('The first row already has h1, p and ul: tap the tag that is missing.'), ok: t('Right! A tag in the wrong place turns red and counts as a mistake.'), build: (box, done) => {
                    const g = draw(box);
                    const cell = g.querySelector('[data-rc="0,2"]');
                    cell.classList.add('sel');
                    const pad = h('div.doku-pad', ['h1', 'p', 'a', 'ul'].map(tag => h('button.chip-btn', {
                        type: 'button', onclick: e => {
                            if (cell.textContent) return;
                            if (tag !== 'a') {
                                cell.classList.add('bad');
                                shake(e.currentTarget);
                                setTimeout(() => cell.classList.remove('bad'), 600);
                                return;
                            }
                            cell.textContent = tag;
                            cell.classList.remove('sel');
                            cell.classList.add('tut-win');
                            filled.add('0,2');
                            done();
                        }
                    }, tag)));
                    box.append(pad);
                }
            },
            {
                text: t('Every two levels the grid grows: 4×4, then 9×9 with 3×3 boxes, then 16×16 with 4×4 boxes.'), build: box => growing(box, [4, 9, 16], dokuOf),
            },
        ];
    }

    // ---------------------------------------------------------------- Four in a line

    function fourline() {
        // Columns from the bottom: 0 empty, 1 you (green), 2 the computer (red)
        const cols = [[2], [1, 2], [1], [], [2, 2], [], [2]];
        let chosen = 3;
        const draw = box => {
            const view = h('div.four.tut-four');
            cols.forEach((col, c) => view.appendChild(h('button.four-col', {type: 'button', 'data-c': c}, ...Array.from({length: 6}, (_, k) => {
                const v = col[5 - k];
                return h('i.disc-' + (['e', 'me', 'ai'][v || 0]));
            }))));
            box.appendChild(view);
            return view;
        };
        return [
            {
                text: t('Your discs are green, the computer’s are red. Tap the column where you want to drop one.'), ok: t('Good choice: before it drops, you have to answer.'), build: (box, done) => {
                    const view = draw(box);
                    const col = view.querySelector('[data-c="3"]');
                    hint(col);
                    view.querySelectorAll('.four-col').forEach(b => b.onclick = () => {
                        chosen = +b.dataset.c;
                        clear(box);
                        b.classList.add('tut-pick');
                        done();
                    });
                }
            },
            {
                text: t('Answer right and your green disc drops. Answer wrong and the computer drops a red one wherever it likes.'), ok: t('Right: your disc drops!'), build: (box, done) => {
                    let view = draw(box);
                    view.querySelector(`[data-c="${chosen}"]`).classList.add('tut-pick');
                    // Both answers really drop a disc: yours in your column when right, the computer's red one when wrong
                    const dropIn = (who, col) => {
                        cols[col].push(who);
                        const fresh = draw(h('div'));
                        view.replaceWith(fresh);
                        view = fresh;
                        fresh.querySelector(`[data-c="${chosen}"]`).classList.add('tut-pick');
                        const slots = fresh.querySelector(`[data-c="${col}"]`).querySelectorAll('i');
                        slots[6 - cols[col].length].classList.add('tut-drop');
                    };
                    const opts = ['<p>', '<a>', '<img>'].map((o, i) => h('button.option', {
                        type: 'button', onclick: e => {
                            if (i !== 1) {
                                e.currentTarget.classList.add('wrong');
                                e.currentTarget.disabled = true;
                                shake(e.currentTarget);
                                dropIn(2, cols[5].length < 5 ? 5 : 2);
                                return;
                            }
                            opts.forEach(x => x.disabled = true);
                            e.currentTarget.classList.add('right');
                            dropIn(1, chosen);
                            done();
                        }
                    }, h('b.opt-key', 'ABC'[i]), h('span', o)));
                    box.append(h('div.tut-question', h('strong', t('Which tag makes a link?')), h('div.options', opts)));
                }
            },
            {
                text: t('The computer plays only when you get it wrong. Line up four green discs in a row, column or diagonal to win; four red ones and the computer wins.'), build: async box => {
                    const four = [[1], [2, 1], [2, 2, 1], [2, 2, 2, 1], [2], [], []];
                    const view = h('div.four.tut-four');
                    four.forEach((col, c) => view.appendChild(h('button.four-col', {type: 'button'}, ...Array.from({length: 6}, (_, k) => {
                        const v = col[5 - k];
                        return h('i.disc-' + ['e', 'me', 'ai'][v || 0] + (v === 1 && c < 4 && k === 5 - c ? '.tut-glow' : ''));
                    }))));
                    box.append(view);
                }
            },
        ];
    }

    // ---------------------------------------------------------------- CSS board

    function selectors() {
        const board = () => {
            const b = h('div.chess.tut-chess');
            for (let r = 1; r <= 4; r++) for (let c = 1; c <= 4; c++) {
                b.appendChild(h('button.sq.r' + r + '.c' + c + ((r + c) % 2 ? '.black' : '.white'), {type: 'button', 'data-rc': r + ',' + c}, h('small', `.r${r} .c${c}`)));
            }
            return b;
        };
        return [
            {
                text: t('Every square is an element with classes: its row (.r1, .r2…), its column (.c1, .c2…) and its colour (.white or .black).'), build: async box => {
                    const b = board();
                    box.append(b);
                    for (const r of [1, 2, 3, 4]) {
                        b.querySelectorAll('.r' + r).forEach(x => x.classList.add('tut-glow'));
                        await wait(500);
                        b.querySelectorAll('.tut-glow').forEach(x => x.classList.remove('tut-glow'));
                    }
                }
            },
            {
                text: t('Tap every square the selector picks, then press Check.'), ok: t('Exactly! Each wrong check costs one of your 3 lives: the lives left are your stars.'), build: (box, done) => {
                    const sel = h('code.selector', '.c3');
                    const b = board();
                    const picked = new Set();
                    b.querySelectorAll('.sq').forEach(sq => sq.onclick = () => {
                        sq.classList.toggle('on');
                        if (picked.has(sq)) picked.delete(sq); else picked.add(sq);
                    });
                    hint(b.querySelector('.c3'));
                    const check = btn(t('Check'), {
                        icon: 'check', onclick: () => {
                            clear(box);
                            const ok = picked.size === 4 && [...picked].every(x => x.classList.contains('c3'));
                            if (!ok) return shake(b);
                            picked.forEach(x => x.classList.add('tut-win'));
                            check.disabled = true;
                            done();
                        }
                    });
                    box.append(sel, b, h('div.tut-row', check));
                }
            },
        ];
    }

    /** Small empty boards that appear one after the other: how a game grows with the levels. */
    async function growing(box, sizes, make) {
        const row = h('div.tut-sizes');
        box.append(row);
        for (const n of sizes) {
            row.append(h('figure.tut-pop', make(n), h('figcaption', `${n}×${n}`)));
            await wait(350);
        }
    }

    const dokuOf = n => {
        const b = Math.sqrt(n);
        return h('div.doku.tut-mini', {style: {gridTemplateColumns: `repeat(${n}, 1fr)`}}, Array.from({length: n * n}, (_, i) => {
            const r = Math.floor(i / n), c = i % n;
            return h('i.dk' + ((c + 1) % b === 0 && c < n - 1 ? '.edge-r' : '') + ((r + 1) % b === 0 && r < n - 1 ? '.edge-b' : ''));
        }));
    };
    const chessOf = n => h('div.chess.tut-mini', {style: {gridTemplateColumns: `repeat(${n}, 1fr)`}}, Array.from({length: n * n}, (_, i) => h('i.sq.' + ((Math.floor(i / n) + i % n) % 2 ? 'black' : 'white'))));

    // ---------------------------------------------------------------- Line hop

    function linehop() {
        const target = ['<ul>', '  <li>Uno</li>', '  <li>Due</li>', '</ul>'];
        return [
            {
                text: t('These lines of code are mixed up. Drag a line by its handle to move it: put <ul> at the top.'), ok: t('Green lines are in the right place. The fewer moves you use, the more stars you win.'), build: (box, done) => {
                    let lines = ['  <li>Uno</li>', '<ul>', '  <li>Due</li>', '</ul>'];
                    let selected = null;
                    const list = h('div.lines.tut-lines');
                    const draw = () => {
                        list.replaceChildren(...lines.map((l, i) => {
                            const b = h('button.line' + (selected === i ? '.sel' : '') + (l === target[i] ? '.fit' : ''), {type: 'button', 'data-i': i}, h('span.grip', {'aria-hidden': 'true'}, icon('menu')), h('code', l));
                            b.onclick = () => {
                                if (list.dataset.dragged) return;
                                if (selected === null) selected = i;
                                else {
                                    if (selected !== i) [lines[selected], lines[i]] = [lines[i], lines[selected]];
                                    selected = null;
                                }
                                settle();
                            };
                            b.addEventListener('pointerdown', e => Games.dragLine(e, b, list, order => {
                                selected = null;
                                lines = order.map(k => lines[k]);
                                settle();
                            }));
                            return b;
                        }));
                        if (lines.join() !== target.join()) hint(list.children[lines.indexOf('<ul>')].querySelector('.grip'));
                    };
                    const settle = () => {
                        draw();
                        if (lines.join() === target.join()) {
                            list.classList.add('tut-done');
                            done();
                        }
                    };
                    draw();
                    box.append(list);
                }
            },
            {
                text: t('From level 4 the indentation disappears: the order is up to you.'), build: box => {
                    box.append(h('div.lines.tut-lines', target.map(l => h('button.line', {type: 'button'}, h('span.grip', {'aria-hidden': 'true'}, icon('menu')), h('code', l.trim())))));
                }
            },
        ];
    }


    // ---------------------------------------------------------------- Quick rounds: one question, the options as buttons

    /** Options to tap; the right one ends the step, a wrong one shakes. */
    function pickStep(box, prompt, options, done, swatches) {
        const keys = 'ABCD';
        const buttons = options.map((o, i) => h('button.option' + (o.swatch ? '.swatch-opt' : ''), {
            type: 'button', onclick: e => {
                if (!o.ok) {
                    e.currentTarget.classList.add('wrong');
                    shake(e.currentTarget);
                    return;
                }
                clear(box);
                buttons.forEach(b => b.disabled = true);
                e.currentTarget.classList.add('right');
                done();
            }
        }, h('b.opt-key', keys[i]), o.swatch ? h('i.swatch', {style: {background: o.swatch}}) : h('span', o.label)));
        box.append(h('div.qr.tut-qr', prompt, h('div.options' + (swatches ? '.swatch-grid' : ''), buttons)));
    }

    function shade() {
        return [
            {
                text: t('Every round shows a colour written in CSS. Tap the matching colour.'), ok: t('Right: #f00 is pure red, the short form of #ff0000.'), build: (box, done) => {
                    pickStep(box, h('div.qr-prompt', h('code.selector', '#f00')), [
                        {swatch: '#00f', ok: false}, {swatch: '#f00', ok: true}, {swatch: '#0a0', ok: false}, {swatch: '#fc0', ok: false},
                    ], done, true);
                }
            },
            {
                text: t('From level 2 a round in two goes the other way: you see the colour and pick its code. Then come rgb() and hsl(), with closer colours.'), build: box => {
                    box.append(h('div.qr.tut-qr', h('div.qr-prompt', h('i.swatch.big', {style: {background: 'hsl(200, 80%, 50%)'}})),
                        h('div.options', ['hsl(200, 80%, 50%)', 'rgb(26, 166, 230)', '#1aa6e6'].map(c => h('div.option', h('span', h('code', c)))))));
                }
            },
        ];
    }

    function duel() {
        return [
            {
                text: t('Two selectors point at the same element: the more specific one wins. Which one is it?'), ok: t('Yes: an id counts more than any number of classes and elements.'), build: (box, done) => {
                    pickStep(box, h('div.qr-prompt', h('p', t('Which selector is more specific?'))), [
                        {label: h('code', '.menu a'), ok: false}, {label: h('code', '#nav'), ok: true}, {label: t('Same specificity'), ok: false},
                    ], done);
                }
            },
            {
                text: t('Count in three columns: ids, then classes, attributes and pseudo-classes, then elements. The first column that differs decides.'), build: async box => {
                    const rows = [['#nav', '(1, 0, 0)'], ['.menu a', '(0, 1, 1)'], ['a:hover', '(0, 1, 1)'], ['ul li a', '(0, 0, 3)']];
                    const list = h('div.options');
                    box.append(h('div.qr.tut-qr', list));
                    for (const [sel, spec] of rows) {
                        list.append(h('div.option.tut-pop', h('span.grow', h('code', sel)), h('b.mono', spec)));
                        await wait(300);
                    }
                }
            },
        ];
    }

    function output() {
        return [
            {
                text: t('Read the code and guess what appears in the console.'), ok: t('Right: a string plus a number joins them, so you get 22 and not 4.'), build: (box, done) => {
                    pickStep(box, h('div.qr-prompt', h('pre.out-code', h('code', "console.log('2' + 2);"))), [
                        {label: h('code', '4'), ok: false}, {label: h('code', '22'), ok: true}, {label: h('code', 'NaN'), ok: false},
                    ], done);
                }
            },
            {
                text: t('Each level adds something: types, arrays, loops, array methods, scope. Three mistakes and the level is lost.'), build: async box => {
                    const lines = ['a + b', "typeof 'ciao'", 'a.length', 'for (…)', 'a.map(x => x * 2)', '{ let x = 2; }'];
                    const list = h('div.tut-row.tut-chips');
                    box.append(list);
                    for (const l of lines) {
                        list.append(h('code.chip-btn.tut-pop', l));
                        await wait(200);
                    }
                }
            },
        ];
    }

    function flexlab() {
        return [
            {
                text: t('The dashed boxes show where the items must go. Choose the value that puts them there.'), ok: t('They line up! Each level adds a property: align-items, flex-direction, flex-wrap and gap.'), build: (box, done) => {
                    const items = () => [1, 2, 3].map(n => h('i.fi.fi' + (n - 1), {style: {width: '40px', height: '40px'}}, String(n)));
                    const ghost = h('div.flex-box.ghost', {style: {justifyContent: 'center'}}, items());
                    const mine = h('div.flex-box.mine', items());
                    const values = ['flex-start', 'center', 'flex-end', 'space-between'];
                    const chips = h('div.chips', values.map(v => h('button.chip-btn' + (v === 'flex-start' ? '.on' : ''), {
                        type: 'button', onclick: e => {
                            chips.querySelectorAll('.on').forEach(x => x.classList.remove('on'));
                            e.currentTarget.classList.add('on');
                            mine.style.justifyContent = v;
                            if (v !== 'center') return;
                            clear(box);
                            mine.classList.add('matched');
                            done();
                        }
                    }, v)));
                    box.append(h('div.flex-stage.tut-flex', ghost, mine), h('div.flex-prop', h('code', 'justify-content:'), chips));
                    hint(chips.children[1]);
                }
            },
        ];
    }

    const TUTORIALS = {whotag, tagdoku, fourline, selectors, linehop, shade, duel, output, flexlab};

    /** Opens the guided round of a game. */
    function open(id, title, cost) {
        if (!TUTORIALS[id]) return;
        const steps = TUTORIALS[id]();
        let i = 0;
        const stage = h('div.tut-stage');
        const say = h('p');
        const dots = h('div.tut-dots', steps.map(() => h('i')));
        const next = btn(t('Next'), {icon: 'right'});
        const skip = btn(t('Skip'), {kind: 'text'});

        function show() {
            const step = steps[i];
            [...dots.children].forEach((d, k) => d.classList.toggle('on', k <= i));
            say.textContent = step.text;
            say.parentNode && say.parentNode.classList.remove('ok');
            stage.replaceChildren();
            const last = i === steps.length - 1;
            next.lastChild.textContent = last ? t('Let’s play!') : t('Next');
            next.disabled = !!step.ok && step.build.length > 1;
            step.build(stage, () => {
                if (step.ok) {
                    say.textContent = step.ok;
                    say.parentNode.classList.add('ok');
                }
                next.disabled = false;
                next.classList.add('tut-ready');
                setTimeout(() => next.classList.remove('tut-ready'), 900);
            });
        }

        const s = UI.sheet({
            title: t('How to play') + ' · ' + title,
            body: [h('div.tut', h('div.tut-say', Fun.mascot('happy', 'pet-sm'), say), stage, dots, h('p.muted.small', t('Stuck during a game? The Help button gives you a hand for {n} Sparks.', {n: cost})))],
            actions: [skip, next]
        });
        skip.onclick = () => s.close();
        next.onclick = () => {
            if (++i >= steps.length) return s.close();
            show();
        };
        show();
    }

    window.Tutorials = {open};
})();
