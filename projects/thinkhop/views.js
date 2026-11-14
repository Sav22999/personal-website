/*
 * ThinkHop — how a question is drawn: the prompt, the options and the picture of every kind of puzzle, plus the replay
 * that shows how the answer comes about. Shared by the game (game.js) and by "try it" in the admin panel (admin.js),
 * so the two always look the same.
 */
(function () {
    'use strict';

    const E = window.ThinkHopEngine;
    const I = window.ThinkHopI18n;
    const {h, icon, bulbs, isBulbRow} = window.ThinkHopUI;
    const t = I.t;
    const RIDDLES = Object.fromEntries((window.ThinkHopRiddles || []).map(r => [r.id, r]));

    // ---------- texts of a question ----------
    /** The invented words are nouns: German writes them with a capital letter. */
    const word = w => I.getLang() === 'de' && E.WORDS.includes(w) ? w[0].toUpperCase() + w.slice(1) : w;
    const line = l => {
        const v = {};
        Object.entries(l.v || {}).forEach(([k, x]) => {
            v[k] = x && typeof x === 'object' ? line(x) : word(x);
        });
        return t(l.k, v);
    };
    const riddleText = q => {
        const r = q.t ? q : RIDDLES[q.id];
        return r.t[I.getLang()] || r.t.en;
    };

    function hasOrigamiGuidelines(q) {
        if (q && q.origamiData && q.origamiData.noGuides !== undefined) {
            return !q.origamiData.noGuides;
        }
        if (q && q.level && E.TIERS) {
            const idx = E.TIERS.indexOf(q.level);
            const expIdx = E.TIERS.indexOf('expert');
            if (idx >= 0 && expIdx >= 0) return idx <= expIdx;
        }
        if (q && q.d !== undefined) return q.d < 7.5;
        return true;
    }

    function optionText(q, o) {
        if (q.view === 'origami' && q.origamiData) {
            const sheet = q.origamiData.candidateSheets.find(s => s.letter === o);
            if (sheet) {
                const noGuides = !hasOrigamiGuidelines(q);
                const size = q.origamiData.size || 4;
                const sizeClass = size === 8 ? '.size-8' : (size === 6 ? '.size-6' : '.size-4');
                const cells = sheet.pattern.split('').map(ch => h('span.origami-mini-cell' + (ch === '1' ? '.hole' : '')));
                return h('div.origami-opt-box',
                    h('span.origami-opt-letter', String(o)),
                    h('div.origami-mini-grid' + sizeClass + (noGuides ? '.no-guides' : ''), cells));
            }
        }
        if (q.cat === 'lamps' && isBulbRow(o)) return bulbs(o);
        if (typeof o === 'string') return o;
        if (o.k) return t(o.k);
        return riddleText(q)[2][o.r];
    }

    function whyLines(q) {
        return q.why.map(w => {
            if (w.lit) return h('p.why-lit', w.lit);
            if (w.riddle) return h('p', riddleText(q)[1]);
            const v = Object.assign({}, w.v);
            if (w.prop) v.prop = t(w.prop.k, w.prop.v);
            return h('p', line({k: w.k, v}));
        });
    }

    function promptText(q) {
        if (q.pk) return line({k: q.pk, v: q.pv});
        if (q.cat === 'sign') return t(q.nums.length > 2 ? 'q_sign2' : 'q_sign');
        if (q.cat === 'riddle') return riddleText(q)[0];
        return t('q_' + q.cat);
    }

    /** The equation with the gaps: once answered, the gaps show the right signs. */
    function equationView(q, answered) {
        const signs = answered ? String(optionText(q, q.options[q.answer])).split(/\s+/) : [];
        const parts = [];
        q.nums.forEach((n, i) => {
            if (i) parts.push(h('span.gap' + (answered ? '.filled' : ''), signs[i - 1] || '?'));
            parts.push(h('span', String(n)));
        });
        return h('div.equation', parts, h('span', '='), h('span', String(q.result)));
    }

    /** A board of cells: once answered, the “?” shows the right symbol. */
    function gridView(q, answered) {
        const right = optionText(q, q.options[q.answer]);
        const n = q.grid.length;
        const edge = (i, size) => size && (i + 1) % size === 0 && i + 1 < n;
        return h('div.grid-wrap',
            h('div.board', {style: `--n:${n}`}, q.grid.flatMap((row, y) => row.map((cell, x) => {
                const ask = cell === '?';
                const diag = q.isDiag && (x === y || x + y === n - 1);
                return h('span.cell' + (ask ? '.ask' : '') + (diag ? '.diag' : '') + (q.boxes && edge(x, q.boxes[1]) ? '.edge-r' : '') + (q.boxes && edge(y, q.boxes[0]) ? '.edge-b' : ''), ask && answered ? right : cell);
            }))),
            q.lines ? h('div.path', q.lines) : null);
    }

    const GEMS_LIST = ['💎', '🔮', '🪙', '⭐'];

    function cubesView(q, answered) {
        const d = q.cubesData;
        if (!d) return gridView(q, answered);

        const rows = d.netGrid.map(row => h('div.cube-net-row', row.map(cell => {
            if (!cell) return h('span.cube-net-empty');
            const isTarget = (cell === d.targetIcon);
            const isOk = answered && (cell === d.rightIcon);
            const cls = (isOk ? '.ok.pop' : (isTarget ? '.target' : ''));
            return h('span.cube-net-cell' + cls, cell);
        })));

        return h('div.cubes-view',
            h('div.cube-net-board', rows),
            q.lines && !d.isViewMode ? h('div.path', q.lines) : null);
    }

    function scalesView(q, answered) {
        const right = optionText(q, q.options[q.answer]);
        const parseScale = str => {
            if (str.includes('⚖️')) {
                const [l, r] = str.split('⚖️').map(s => s.trim());
                return {l, r, op: '⚖️', tilt: ''};
            }
            if (str.includes('>')) {
                const [l, r] = str.split('>').map(s => s.trim());
                return {l, r, op: '>', tilt: '.tilt-left'};
            }
            if (str.includes('<')) {
                const [l, r] = str.split('<').map(s => s.trim());
                return {l, r, op: '<', tilt: '.tilt-right'};
            }
            return null;
        };

        const scaleRows = (q.lines || []).map(lineStr => {
            const parsed = parseScale(lineStr);
            if (!parsed) return h('div.h-chip', lineStr);
            return h('div.scale-card',
                h('div.scale-visual',
                    h('div.scale-beam-bar' + parsed.tilt,
                        h('div.scale-pan-wrap.left',
                            h('div.scale-pan-string'),
                            h('div.scale-pan-dish', h('span.scale-items', parsed.l))),
                        h('div.scale-pan-wrap.right',
                            h('div.scale-pan-string'),
                            h('div.scale-pan-dish', h('span.scale-items', parsed.r)))),
                    h('div.scale-pivot')));
        });

        if (q.ask) {
            const parsedAsk = parseScale(q.ask);
            if (parsedAsk) {
                const rightSide = answered ? right : '?';
                scaleRows.push(h('div.scale-card.focus',
                    h('div.scale-visual',
                        h('div.scale-beam-bar',
                            h('div.scale-pan-wrap.left',
                                h('div.scale-pan-string'),
                                h('div.scale-pan-dish', h('span.scale-items', parsedAsk.l))),
                            h('div.scale-pan-wrap.right',
                                h('div.scale-pan-string'),
                                h('div.scale-pan-dish' + (answered ? '.ok.pop' : '.focus'), h('span.scale-items', rightSide)))),
                        h('div.scale-pivot'))));
            }
        }

        return h('div.scales-view', scaleRows);
    }

    function cupsView(q, answered) {
        const right = optionText(q, q.options[q.answer]);
        const startItems = (q.lines && q.lines[0]) ? q.lines[0].split(/\s+/).filter(Boolean) : [];
        const moves = (q.lines && q.lines[1]) ? q.lines[1].split(/\s*·\s*/).filter(Boolean) : [];
        const endItems = (q.lines && q.lines[2]) ? q.lines[2].split(/\s+/).filter(Boolean) : [];

        const isBack = (q.pk === 'q_cups_back');
        const targetPos = (q.pv && q.pv.p !== undefined) ? parseInt(q.pv.p, 10) : null;
        const targetItem = (q.pv && q.pv.x !== undefined) ? q.pv.x : null;

        const revealedStart = (isBack && answered && right) ? right.split(/\s+/) : null;

        const startCards = startItems.map((item, i) => {
            const displayItem = revealedStart ? (revealedStart[i] || item) : item;
            const isTarget = (targetPos !== null && i + 1 === targetPos) || (targetItem !== null && item === targetItem);
            const isOk = answered && isTarget;
            return h('div.cup-card' + (isOk ? '.ok.pop' : (isTarget ? '.target-cup' : '')),
                h('span.cup-idx', '#' + (i + 1)),
                h('span.cup-item', displayItem));
        });

        const moveChips = moves.map(m => {
            const isMissingSlot = m.includes('?');
            const displayText = (isMissingSlot && answered) ? right : m;
            return h('span.h-chip' + (isMissingSlot ? (answered ? '.ok.pop' : '.target-chip') : '.focus'), displayText);
        });

        const rows = [
            h('div.cups-row', startCards),
            moveChips.length ? h('div.cup-moves-path', moveChips) : null,
        ];

        if (endItems.length > 0) {
            const endCards = endItems.map((item, i) => {
                return h('div.cup-card',
                    h('span.cup-idx', '#' + (i + 1)),
                    h('span.cup-item', item));
            });
            rows.push(h('div.cups-row.end-row', endCards));
        } else if (answered && q.pk === 'q_cups_row' && right) {
            const rightItems = right.split(/\s+/);
            const resCards = rightItems.map((item, i) => {
                return h('div.cup-card.ok',
                    h('span.cup-idx', '#' + (i + 1)),
                    h('span.cup-item', item));
            });
            rows.push(h('div.cups-row.end-row', resCards));
        }

        return h('div.cups-view', rows.filter(Boolean));
    }

    function locksView(q, answered) {
        const right = optionText(q, q.options[q.answer]);
        let dialCount = 3;
        const rows = (q.lines || []).map(lineStr => {
            const parts = lineStr.split(/\s*(?:→|➔|·)\s*/);
            const rawDials = parts[0] ? parts[0].trim() : '';
            const dials = rawDials.includes(' ')
                ? rawDials.split(/\s+/).filter(Boolean)
                : Array.from(rawDials);
            if (dials.length) dialCount = dials.length;
            const feedback = parts[1] || lineStr;
            const matchTarget = feedback.match(/(\d+)\s*🎯/);
            const matchSpin = feedback.match(/(\d+)\s*🔄/);
            const targets = matchTarget ? matchTarget[1] : '0';
            const spins = matchSpin ? matchSpin[1] : '0';

            return h('div.lock-attempt-row',
                h('div.lock-dials', dials.map(d => h('span.lock-dial', d))),
                h('div.lock-feedback-badges',
                    h('span.lock-badge.bullseye', `${targets} 🎯`),
                    h('span.lock-badge.spin', `${spins} 🔄`)));
        });

        const rightStr = right ? String(right).trim() : '';
        const secretDials = (answered && rightStr)
            ? (rightStr.includes(' ') ? rightStr.split(/\s+/).filter(Boolean) : Array.from(rightStr))
            : Array.from({length: dialCount}, () => '?');

        rows.push(h('div.lock-attempt-row.focus',
            h('div.lock-dials', secretDials.map(d => h('span.lock-dial' + (answered ? '.ok.pop' : ''), d))),
            h('span.lock-badge' + (answered ? '.bullseye' : ''), answered ? '🔓 OK' : '🔒')));

        return h('div.locks-view', rows);
    }

    function seatsView(q, answered) {
        const right = optionText(q, q.options[q.answer]);
        const numSeats = q.pv && q.pv.n ? q.pv.n : 4;
        const rad = 83;
        const center = 120;

        let targetSeat = (q.pv && q.pv.targetSeat) || (q.ask && q.ask.v && (q.ask.v.targetSeat || q.ask.v.n)) || null;
        if (!targetSeat && q.ask && q.ask.k === 'st_ask_opp' && q.ask.v && q.ask.v.a && q.why && q.why[0] && q.why[0].v && q.why[0].v.x) {
            const pairs = q.why[0].v.x.split(/\s*·\s*/).map(s => s.split(':'));
            const oppPair = pairs.find(p => p[1] === q.ask.v.a);
            if (oppPair) {
                const oppSeat = parseInt(oppPair[0], 10) - 1;
                targetSeat = ((oppSeat + numSeats / 2) % numSeats) + 1;
            }
        }

        const isBench = q.pk === 'q_seats_bench';

        const renderChair = (i, x, y) => {
            const isAsk = targetSeat !== null ? (i + 1 === targetSeat) : false;
            const isOk = isAsk && answered;
            const attrs = (x !== undefined && y !== undefined) ? {style: `left:${x}px;top:${y}px`} : {};
            return h('div.chair-spot' + (isOk ? '.ok.pop' : (isAsk ? '.ask-chair' : '')), attrs,
                h('span.chair-num', String(i + 1)),
                h('span.chair-who', isOk ? right : (isAsk ? '?' : '🪑')));
        };

        let seatingGraphic = null;
        if (isBench) {
            const benchSpots = Array.from({length: numSeats}, (_, i) => renderChair(i));
            seatingGraphic = h('div.bench-wrap', benchSpots);
        } else {
            const chairs = Array.from({length: numSeats}, (_, i) => {
                const ang = (i / numSeats) * Math.PI * 2 - Math.PI / 2;
                const x = Math.round(center + Math.cos(ang) * rad - 25);
                const y = Math.round(center + Math.sin(ang) * rad - 25);
                return renderChair(i, x, y);
            });
            seatingGraphic = h('div.round-table-wrap',
                h('div.round-table-center', '🍽️'),
                chairs);
        }

        return h('div.seats-view',
            seatingGraphic,
            q.facts ? h('div.facts',
                h('ul', q.facts.map(f => h('li', line(f)))),
                q.ask ? h('p.ask', line(q.ask)) : null) : null);
    }

    function boxesView(q, answered) {
        const right = optionText(q, q.options[q.answer]);
        const chests = (q.lines || []).map(lineStr => {
            const isWinner = lineStr.includes(right);
            const isOpened = answered && isWinner;
            return h('div.chest-card' + (isOpened ? '.winner-open' : ''),
                h('span.chest-icon', isOpened ? '💎' : '🧰'),
                h('span.chest-label', lineStr));
        });

        return h('div.boxes-view', chests);
    }

    function lampsView(q, answered) {
        if (!q.lines || !q.lines.length) return null;
        const startLine = q.lines[0];
        const buttonLines = q.lines.slice(1);
        const isBack = startLine.includes('→');
        const [from, to] = startLine.split('→');
        // Forward: the buttons pressed, one after another, in this order; back: two buttons to find
        const order = !isBack && q.ask ? q.ask.split('=')[0].trim().split(/\s+/).filter(Boolean) : [];
        return h('div.lamps-view',
            h('div.lamps-start-card',
                h('span.lamps-badge', isBack ? t('lamps_trans') : t('lamps_start')),
                h('div.lamps-state-row', bulbs(from, true), isBack ? h('span.lamp-step-arrow', '➔') : null, isBack ? bulbs(to, true) : null)),
            buttonLines.length ? h('div.lamps-buttons-wrap',
                h('span.lamps-press-label', t('lamps_buttons_lbl')),
                h('div.lamps-buttons-grid', buttonLines.map(b => {
                    const parts = b.split(/\s*→\s*/);
                    return h('div.lamp-btn-chip',
                        h('span.lamp-btn-letter', parts[0] || ''),
                        h('span.lamp-btn-arrow', '➔'),
                        h('span.lamp-btn-nums', parts[1] || ''));
                }))) : null,
            order.length ? h('div.lamps-ask-card',
                h('span.lamps-press-label', t('lamps_pressed')),
                h('div.lamps-press-seq', order.flatMap((b, i) => [
                    i ? h('span.lamp-step-arrow', '➔') : null,
                    h('span.lamp-step', h('small', String(i + 1)), b),
                ]).filter(Boolean), h('span.lamp-step-arrow', '= ?'))) : null);
    }

    function mobilesView(q, answered) {
        const d = q.mobilesData;
        if (!d) return null;
        const right = optionText(q, q.options[q.answer]);

        const width = 310;
        const height = d.beams.length > 1 ? 165 : 125;
        const svgParts = [];

        const topX = d.beams[0] ? d.beams[0].x : 155;
        const topY = d.beams[0] ? d.beams[0].y : 32;
        svgParts.push(`<circle cx="${topX}" cy="5" r="4" fill="var(--text)"/>`);
        svgParts.push(`<line x1="${topX}" y1="5" x2="${topX}" y2="${topY}" stroke="var(--text)" stroke-width="2.5" stroke-linecap="round"/>`);

        const renderWeight = (x, y, w) => {
            const wy = y + 26;
            const isTarget = w.isTarget;
            const isOk = isTarget && answered;
            const valStr = isOk ? right : (isTarget ? '?' : String(w.val));
            const bgFill = isOk ? 'var(--ok-bg)' : (isTarget ? '#FFD23F' : 'var(--primary-soft)');
            const strokeCol = isOk ? 'var(--ok)' : (isTarget ? '#141127' : 'var(--line)');
            const textCol = isOk ? 'var(--ok)' : (isTarget ? '#141127' : 'var(--on-primary-soft)');

            return `<g class="mobile-weight-group${isTarget ? ' target-node' : ''}${isOk ? ' ok' : ''}">
                <line x1="${x}" y1="${y}" x2="${x}" y2="${wy}" stroke="var(--text)" stroke-width="1.8"/>
                <rect class="mobile-weight-node" x="${x - 16}" y="${wy}" width="32" height="26" rx="7" fill="${bgFill}" stroke="${strokeCol}" stroke-width="2"/>
                <text x="${x}" y="${wy + 18}" font-size="13" font-weight="900" text-anchor="middle" fill="${textCol}">${valStr}</text>
            </g>`;
        };

        d.beams.forEach(b => {
            const armScale = 32;
            const xL = b.x - b.dL * armScale;
            const xR = b.x + b.dR * armScale;
            const y = b.y;

            svgParts.push(`<circle cx="${b.x}" cy="${y}" r="4" fill="var(--text)"/>`);
            svgParts.push(`<line x1="${xL}" y1="${y}" x2="${xR}" y2="${y}" stroke="var(--text)" stroke-width="4" stroke-linecap="round"/>`);

            for (let i = 1; i <= b.dL; i++) {
                const tx = b.x - i * armScale;
                svgParts.push(`<line x1="${tx}" y1="${y - 4}" x2="${tx}" y2="${y + 4}" stroke="var(--muted)" stroke-width="1.5"/>`);
            }
            for (let i = 1; i <= b.dR; i++) {
                const tx = b.x + i * armScale;
                svgParts.push(`<line x1="${tx}" y1="${y - 4}" x2="${tx}" y2="${y + 4}" stroke="var(--muted)" stroke-width="1.5"/>`);
            }
            svgParts.push(`<text x="${(b.x + xL) / 2}" y="${y - 6}" font-size="10" font-weight="900" text-anchor="middle" fill="var(--muted)">×${b.dL}</text>`);
            svgParts.push(`<text x="${(b.x + xR) / 2}" y="${y - 6}" font-size="10" font-weight="900" text-anchor="middle" fill="var(--muted)">×${b.dR}</text>`);

            if (b.left.type === 'weight') {
                svgParts.push(renderWeight(xL, y, b.left));
            } else if (b.left.type === 'beam') {
                const subB = d.beams[b.left.subBeamIdx];
                if (subB) {
                    svgParts.push(`<line x1="${xL}" y1="${y}" x2="${subB.x}" y2="${subB.y}" stroke="var(--text)" stroke-width="2" stroke-dasharray="3 2"/>`);
                }
            }

            if (b.right.type === 'weight') {
                svgParts.push(renderWeight(xR, y, b.right));
            } else if (b.right.type === 'beam') {
                const subB = d.beams[b.right.subBeamIdx];
                if (subB) {
                    svgParts.push(`<line x1="${xR}" y1="${y}" x2="${subB.x}" y2="${subB.y}" stroke="var(--text)" stroke-width="2" stroke-dasharray="3 2"/>`);
                }
            }
        });

        const svgEl = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svgEl.setAttribute('viewBox', `0 0 ${width} ${height}`);
        svgEl.setAttribute('class', 'mobiles-diagram' + (answered ? ' balanced' : ''));
        svgEl.innerHTML = svgParts.join('');

        return h('div.mobiles-view',
            svgEl,
            answered ? h('div.h-row',
                h('span.h-chip.ok', '✓ ? = ' + right)) : null);
    }

    function computeOrigamiBounds(folds, size = 4) {
        let b = { xMin: 0, xMax: size - 1, yMin: 0, yMax: size - 1 };
        const steps = [];
        for (const f of folds) {
            const bBefore = { xMin: b.xMin, xMax: b.xMax, yMin: b.yMin, yMax: b.yMax, cols: b.xMax - b.xMin + 1, rows: b.yMax - b.yMin + 1 };
            const sx = b.xMin + b.xMax;
            const sy = b.yMin + b.yMax;
            if (f === 'L_TO_R') b.xMin = Math.floor((b.xMin + b.xMax) / 2) + 1;
            else if (f === 'R_TO_L') b.xMax = Math.floor((b.xMin + b.xMax) / 2);
            else if (f === 'T_TO_B') b.yMin = Math.floor((b.yMin + b.yMax) / 2) + 1;
            else if (f === 'B_TO_T') b.yMax = Math.floor((b.yMin + b.yMax) / 2);
            const bAfter = { xMin: b.xMin, xMax: b.xMax, yMin: b.yMin, yMax: b.yMax, cols: b.xMax - b.xMin + 1, rows: b.yMax - b.yMin + 1 };
            steps.push({ fold: f, bBefore, bAfter, sx, sy });
        }
        const finalBounds = { xMin: b.xMin, xMax: b.xMax, yMin: b.yMin, yMax: b.yMax, cols: b.xMax - b.xMin + 1, rows: b.yMax - b.yMin + 1 };
        return { steps, finalBounds };
    }

    function makeOrigamiSheet(cols, rows, u, extraClass, children, showGuides) {
        const kids = [];
        if (showGuides) {
            for (let c = 1; c < cols; c++) {
                kids.push(h('span.origami-guideline.vert', {
                    style: `left:${(c / cols) * 100}%;`
                }));
            }
            for (let r = 1; r < rows; r++) {
                kids.push(h('span.origami-guideline.horiz', {
                    style: `top:${(r / rows) * 100}%;`
                }));
            }
        }
        if (children) {
            if (Array.isArray(children)) kids.push(...children);
            else kids.push(children);
        }
        const wPx = cols * u;
        const hPx = rows * u;
        return h('div.origami-sheet' + (extraClass ? '.' + extraClass : '') + (showGuides ? '' : '.no-guides'), {
            style: `width:${wPx}px;height:${hPx}px;position:relative;`
        }, kids);
    }

    function origamiView(q, answered) {
        const d = q.origamiData;
        if (!d) return null;
        const rawRight = q.options && q.answer !== undefined ? q.options[q.answer] : '';
        const rightLetter = typeof rawRight === 'string' ? rawRight : (rawRight && rawRight.letter ? rawRight.letter : '');

        const size = d.size || 4;
        const { steps, finalBounds } = (d.stepBounds && d.finalBounds)
            ? { steps: d.stepBounds, finalBounds: d.finalBounds }
            : computeOrigamiBounds(d.folds || [], size);

        const u = size === 8 ? 8 : (size === 6 ? 10 : 16);
        const dotD = size === 8 ? 6 : (size === 6 ? 7 : 9);
        const showGuides = hasOrigamiGuidelines(q);

        // Card 0: Starting sheet (blank white paper)
        const startSheet = makeOrigamiSheet(size, size, u, 'full.blank',
            h('span.origami-blank-lbl', t('origami_blank')),
            showGuides);
        const startCard = h('div.origami-sheet-card',
            h('span.origami-sheet-step', t('origami_start')),
            h('div.origami-sheet-stage', startSheet));

        // Cards 1..N: Each fold step with exact sheet shape before that fold
        const foldCards = steps.map((step, idx) => {
            const f = step.fold;
            const isVert = (f === 'L_TO_R' || f === 'R_TO_L');
            const arrow = isVert
                ? (f === 'L_TO_R' ? '➡️' : '⬅️')
                : (f === 'T_TO_B' ? '⬇️' : '⬆️');

            const foldSheet = makeOrigamiSheet(step.bBefore.cols, step.bBefore.rows, u, '', [
                h('div.origami-fold-line' + (isVert ? '.vert' : '.horiz')),
                h('span.origami-arrow', arrow)
            ], showGuides);

            return h('div.origami-sheet-card',
                h('span.origami-sheet-step', String(idx + 1)),
                h('div.origami-sheet-stage', foldSheet));
        });

        // Final Card: Hole punch on folded sheet (exact shape and hole coordinates)
        const punchDots = (d.punch || []).map(([px, py]) => {
            const colIdx = px - finalBounds.xMin;
            const rowIdx = py - finalBounds.yMin;
            const cx = (colIdx + 0.5) * u;
            const cy = (rowIdx + 0.5) * u;
            return h('span.origami-punch-dot', {
                style: `left:${cx}px;top:${cy}px;width:${dotD}px;height:${dotD}px;`
            });
        });

        const punchSheet = makeOrigamiSheet(finalBounds.cols, finalBounds.rows, u, 'punched', punchDots, showGuides);
        const punchStepNum = String(steps.length + 1) + ' ✂️';
        const punchCard = h('div.origami-sheet-card.punch-card',
            h('span.origami-sheet-step', punchStepNum),
            h('div.origami-sheet-stage', punchSheet));

        const stepCards = [startCard, ...foldCards, punchCard];

        return h('div.origami-view',
            h('div.origami-steps-row', stepCards),
            answered ? origamiAnim(q, steps, finalBounds) : null);
    }

    // ---------- origami: punch, then unfold one fold at a time ----------
    /**
     * The folded sheet is punched, then every fold opens again, last one first: the flap turns over the fold line and
     * carries its holes, which lay exactly on the holes under it. At the end the whole sheet matches the right answer.
     */
    function origamiAnim(q, steps, finalBounds) {
        const d = q.origamiData;
        const N = d.size || 4;
        const n = steps.length;
        const C = Math.round(200 / N), PAD = 16;
        const px = v => PAD + v * C;
        const key = (x, y) => x + ',' + y;
        // Holes after each unfolding: holes[m] are those on the sheet still folded m times
        const holes = Array(n + 1);
        holes[n] = new Set((d.punch || []).map(([x, y]) => key(x, y)));
        for (let m = n; m > 0; m--) {
            const s = steps[m - 1];
            const next = new Set(holes[m]);
            holes[m].forEach(k => {
                const [x, y] = k.split(',').map(Number);
                const vert = s.fold === 'L_TO_R' || s.fold === 'R_TO_L';
                next.add(vert ? key(s.sx - x, y) : key(x, s.sy - y));
            });
            holes[m - 1] = next;
        }
        const region = m => (m === 0 ? {xMin: 0, xMax: N - 1, yMin: 0, yMax: N - 1} : steps[m - 1].bAfter);
        const FRONT = '#FFFDF5', BACK = '#E9DCC0', EDGE = '#8E86B5', HOLE = '#2A2450';
        const rect = (b, fill, extra) => `<rect x="${px(b.xMin)}" y="${px(b.yMin)}" width="${(b.xMax - b.xMin + 1) * C}" height="${(b.yMax - b.yMin + 1) * C}" fill="${fill}" stroke="${EDGE}" stroke-width="1.5" ${extra || ''}/>`;
        const dots = (set, b, r) => [...set].map(k => k.split(',').map(Number))
            .filter(([x, y]) => x >= b.xMin && x <= b.xMax && y >= b.yMin && y <= b.yMax)
            .map(([x, y]) => `<circle cx="${px(x) + C / 2}" cy="${px(y) + C / 2}" r="${(r * C * .3).toFixed(1)}" fill="${HOLE}"/>`).join('');
        const PUNCH = 1, OPEN = 1.6, HOLD = .5;
        const total = PUNCH + n * OPEN + HOLD;
        const unfoldArrow = f => ({L_TO_R: '⬅️', R_TO_L: '➡️', T_TO_B: '⬆️', B_TO_T: '⬇️'})[f];
        const right = String(q.options[q.answer]); // the letter of the right sheet
        const draw = tt => {
            let u = tt * total;
            const parts = [`<rect x="${PAD}" y="${PAD}" width="${N * C}" height="${N * C}" fill="none" stroke="${EDGE}" stroke-width="1.2" stroke-dasharray="4 4" opacity=".55"/>`];
            let phase;
            if (u < PUNCH) {
                // The punch goes through every layer of the folded sheet
                const k = ease(u / PUNCH);
                const b = region(n);
                parts.push(rect(b, FRONT), dots(holes[n], b, k));
                phase = 0;
            } else if (u < PUNCH + n * OPEN) {
                const idx = Math.floor((u - PUNCH) / OPEN); // 0 = the last fold opens first
                const v = ease(clamp01(((u - PUNCH) - idx * OPEN) / (OPEN * .85)));
                const m = n - idx;
                const s = steps[m - 1];
                const base = region(m), whole = region(m - 1);
                parts.push(rect(base, FRONT), dots(holes[m], base, 1));
                let flap, line, transform;
                const sc = -1 + 2 * v; // -1: lying on the sheet, 0: on its edge, 1: open
                if (s.fold === 'L_TO_R') { flap = {xMin: whole.xMin, xMax: base.xMin - 1, yMin: base.yMin, yMax: base.yMax}; line = px(base.xMin); transform = `translate(${line} 0) scale(${sc.toFixed(3)} 1) translate(${-line} 0)`; }
                if (s.fold === 'R_TO_L') { flap = {xMin: base.xMax + 1, xMax: whole.xMax, yMin: base.yMin, yMax: base.yMax}; line = px(base.xMax + 1); transform = `translate(${line} 0) scale(${sc.toFixed(3)} 1) translate(${-line} 0)`; }
                if (s.fold === 'T_TO_B') { flap = {xMin: base.xMin, xMax: base.xMax, yMin: whole.yMin, yMax: base.yMin - 1}; line = px(base.yMin); transform = `translate(0 ${line}) scale(1 ${sc.toFixed(3)}) translate(0 ${-line})`; }
                if (s.fold === 'B_TO_T') { flap = {xMin: base.xMin, xMax: base.xMax, yMin: base.yMax + 1, yMax: whole.yMax}; line = px(base.yMax + 1); transform = `translate(0 ${line}) scale(1 ${sc.toFixed(3)}) translate(0 ${-line})`; }
                parts.push(`<g transform="${transform}">${rect(flap, sc < 0 ? BACK : FRONT)}${dots(holes[m - 1], flap, 1)}</g>`);
                const vert = s.fold === 'L_TO_R' || s.fold === 'R_TO_L';
                parts.push(vert
                    ? `<line x1="${line}" y1="${px(base.yMin) - 6}" x2="${line}" y2="${px(base.yMax + 1) + 6}" stroke="var(--primary)" stroke-width="2" stroke-dasharray="5 4"/>`
                    : `<line x1="${px(base.xMin) - 6}" y1="${line}" x2="${px(base.xMax + 1) + 6}" y2="${line}" stroke="var(--primary)" stroke-width="2" stroke-dasharray="5 4"/>`);
                phase = idx + 1;
            } else {
                const b = region(0);
                parts.push(rect(b, FRONT, 'stroke-width="2.5"'), dots(holes[0], b, 1));
                phase = n + 1;
            }
            const svg = `<svg class="origami-anim-svg" viewBox="0 0 ${N * C + PAD * 2} ${N * C + PAD * 2}" aria-hidden="true">${parts.join('')}</svg>`;
            const end = tt >= 1;
            const chips = ['✂️', ...steps.slice().reverse().map(s => unfoldArrow(s.fold))].map((label, i) =>
                h('span.h-chip' + (i === phase && !end ? '.focus.now' : (i < phase || end ? '.ok' : '')), label));
            return h('div.origami-anim',
                h('div.origami-anim-stage', {html: svg}),
                h('div.cup-moves-path', chips, end ? h('span.h-chip.ok', '= ' + right) : null));
        };
        return player(replayKey(q, 'origami'), 1000 * total + 400, draw);
    }


    function questionBody(q, answered) {
        if (q.view === 'mobiles' || q.cat === 'mobiles') return mobilesView(q, answered);
        if (q.view === 'origami' || q.cat === 'origami') return origamiView(q, answered);
        if (q.view === 'gears') return gearsView(q, answered);
        if (q.view === 'lasers') return lasersView(q, answered);
        if (q.view === 'cubes') return cubesView(q, answered);
        if (q.view === 'pipes') return pipesView(q, answered);
        if (q.cat === 'scales') return scalesView(q, answered);
        if (answered && q.cat === 'cups' && q.lines && q.lines.length >= 2) return cupsAnim(q);
        if (q.cat === 'cups') return cupsView(q, answered);
        if (q.cat === 'locks') return locksView(q, answered);
        if (q.cat === 'seats' && q.pv && q.pv.n) return seatsView(q, answered);
        if (q.cat === 'boxes' && q.lines && q.lines.length) return boxesView(q, answered);
        if (answered && q.cat === 'lamps' && q.lines && q.lines.length >= 2) return lampsAnim(q);
        if (q.cat === 'lamps' || q.view === 'lamps') return lampsView(q, answered);
        if (answered && q.cat === 'compass' && q.lines && q.lines.length) return compassAnim(q);
        if (q.view === 'grid') return gridView(q, answered);
        if (q.view === 'equation') return equationView(q, answered);
        if (q.view === 'list') return h('div.sequence', q.items.map(n => h('span', String(n))), h('span.gap' + (answered ? '.filled' : ''), answered ? optionText(q, q.options[q.answer]) : '?'));
        if (q.view === 'lines') return h('div.lines', q.lines.map(l => h('div', l)), h('div.ask', q.ask));
        if (q.view === 'facts') return h('div.facts', h('ul', q.facts.map(f => h('li', line(f)))), h('p.ask', line(q.ask)));
        return null;
    }

    // ---------- replay of the answer ----------
    // Once answered, the drawn puzzles show how the answer comes about: it plays by itself, and can be paused, dragged
    // and played again. Each puzzle only says how to draw a moment t, from 0 (start) to 1 (end).
    const replays = {};
    const reduceMotion = () => !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    const ease = u => (u <= 0 ? 0 : u >= 1 ? 1 : u * u * (3 - 2 * u));
    const clamp01 = u => Math.max(0, Math.min(1, u));

    function player(key, duration, draw) {
        const still = reduceMotion();
        const st = replays[key] || (replays[key] = {t: still ? 1 : 0, playing: !still});
        const stage = h('div.anim-stage');
        const btn = h('button.anim-btn', {type: 'button'});
        const again = h('button.anim-btn', {type: 'button', title: t('anim_replay'), 'aria-label': t('anim_replay')}, icon('replay'));
        const range = h('input.anim-range', {type: 'range', min: '0', max: '1000', step: '1', 'aria-label': t('anim_progress')});
        const wrap = h('div.anim-player', stage, h('div.anim-bar', btn, again, range));
        let raf = 0, last = null, waits = 0, shown = null;
        const paint = () => {
            const out = draw(st.t);
            if (typeof out === 'string') stage.innerHTML = out; else stage.replaceChildren(out);
            range.value = String(Math.round(st.t * 1000));
            range.style.setProperty('--p', (st.t * 100).toFixed(1) + '%');
            if (shown !== st.playing) {
                shown = st.playing;
                const label = t(st.playing ? 'anim_pause' : 'anim_play');
                btn.replaceChildren(icon(st.playing ? 'pause' : 'play'));
                btn.title = label;
                btn.setAttribute('aria-label', label);
            }
        };
        const frame = now => {
            raf = 0;
            if (!wrap.isConnected) {
                if (++waits < 90) raf = requestAnimationFrame(frame); // not on the page yet: wait for it
                return;
            }
            waits = 0;
            if (!st.playing) { last = null; return; }
            if (last !== null) st.t = Math.min(1, st.t + (now - last) / duration);
            last = now;
            if (st.t >= 1) st.playing = false;
            paint();
            if (st.playing) raf = requestAnimationFrame(frame); else last = null;
        };
        const run = () => { if (!raf) raf = requestAnimationFrame(frame); };
        btn.addEventListener('click', () => {
            if (!st.playing && st.t >= 1) st.t = 0;
            st.playing = !st.playing;
            last = null;
            paint();
            run();
        });
        again.addEventListener('click', () => { st.t = 0; st.playing = true; last = null; paint(); run(); });
        range.addEventListener('input', () => { st.t = +range.value / 1000; st.playing = false; paint(); });
        paint();
        run();
        return wrap;
    }
    const replayKey = (q, kind) => kind + '|' + (q.sig || '') + '|' + q.answer + '|' + JSON.stringify(q.options).length;

    // ---------- gears ----------
    const GEAR_NAMES = ['A', 'B', 'C', 'D', 'E', 'F'];

    /** Where the gears go and how they are turned so that the teeth of two touching gears really mesh. */
    function gearLayout(d) {
        const n = d.count;
        const MOD = d.speed ? Math.min(4, 92 / Math.max(...d.teeth)) : 4;
        const gears = Array.from({length: n}, (_, i) => {
            const teeth = d.speed ? d.teeth[i] : 10;
            return {t: teeth, rp: teeth * MOD / 2, x: 0, y: 0, rot: 0};
        });
        const mesh = (a, b) => {
            const theta = Math.atan2(b.y - a.y, b.x - a.x);
            const pa = 2 * Math.PI / a.t, pb = 2 * Math.PI / b.t;
            const f = (((theta - a.rot) / pa) % 1 + 1) % 1;
            b.rot = theta + Math.PI + pb / 2 - f * pb;
        };
        if (d.isRing) {
            const ringR = gears[0].rp / Math.sin(Math.PI / n);
            gears.forEach((g, i) => {
                const ang = (i / n) * Math.PI * 2 - Math.PI / 2;
                g.x = Math.cos(ang) * ringR;
                g.y = Math.sin(ang) * ringR;
            });
            for (let i = 1; i < n; i++) mesh(gears[i - 1], gears[i]);
        } else {
            const branch = d.links.some(([a, b]) => a === 0 && b === 2);
            d.links.forEach(([a, b]) => {
                const from = gears[a], to = gears[b];
                const ang = branch && a === 0 ? (b === 1 ? -50 : 50) * Math.PI / 180 : 0;
                to.x = from.x + Math.cos(ang) * (from.rp + to.rp);
                to.y = from.y + Math.sin(ang) * (from.rp + to.rp);
                mesh(from, to);
            });
        }
        const outer = g => g.rp + MOD * .9;
        const minX = Math.min(...gears.map(g => g.x - outer(g))) - 6, maxX = Math.max(...gears.map(g => g.x + outer(g))) + 6;
        const minY = Math.min(...gears.map(g => g.y - outer(g))) - 24, maxY = Math.max(...gears.map(g => g.y + outer(g))) + 24;
        gears.forEach(g => { g.x -= minX; g.y -= minY; g.outer = outer(g); g.mod = MOD; });
        // Contacts from the start gear, for the wave that carries the movement along
        const start = d.startIdx || 0;
        const dist = Array(n).fill(-1);
        dist[start] = 0;
        const order = [start];
        for (let k = 0; k < order.length; k++) {
            d.links.forEach(([a, b]) => {
                [[a, b], [b, a]].forEach(([x, y]) => {
                    if (x === order[k] && dist[y] < 0) { dist[y] = dist[x] + 1; order.push(y); }
                });
            });
        }
        return {gears, width: Math.round(maxX - minX), height: Math.round(maxY - minY), dist};
    }

    function gearSvg(d, lay, state) {
        const {gears} = lay;
        const toothPath = g => {
            const ro = g.rp + g.mod * .9, rr = g.rp - g.mod * .95, p = 2 * Math.PI / g.t;
            const pt = (r, a) => (g.x + Math.cos(a) * r).toFixed(1) + ' ' + (g.y + Math.sin(a) * r).toFixed(1);
            const out = [];
            for (let k = 0; k < g.t; k++) {
                const a = g.rot + k * p;
                out.push((k ? 'L' : 'M') + pt(rr, a - p * .5), 'L' + pt(rr, a - p * .3), 'L' + pt(ro, a - p * .17), 'L' + pt(ro, a + p * .17), 'L' + pt(rr, a + p * .3));
            }
            return out.join(' ') + ' Z';
        };
        const parts = gears.map((g, i) => {
            const isStart = i === d.startIdx, isTarget = i === d.targetIdx;
            const isOk = isTarget && state.reveal[i];
            const bad = state.conflict && state.conflict.includes(i);
            const glow = state.pulse === i;
            const fill = bad ? 'var(--wrong-bg)' : (isOk ? 'var(--ok-bg)' : (isTarget ? 'var(--primary-soft)' : (isStart ? '#FFD23F' : 'var(--surface-3)')));
            const stroke = bad ? 'var(--wrong)' : (isOk ? 'var(--ok)' : (isTarget || glow ? 'var(--primary)' : (isStart ? '#141127' : 'var(--muted)')));
            const labelFill = isStart && !isOk && !bad ? '#141127' : 'var(--text)';
            const dir = d.dirs[i];
            const sym = state.reveal[i] || isStart ? (dir === 'cw' ? '↻' : '↺') : (isTarget ? '?' : '');
            const crowded = gears.some(o => o !== g && o.y < g.y - 4 && Math.abs(o.x - g.x) < g.outer + o.outer);
            const symY = crowded ? g.y + g.outer + 17 : g.y - g.outer - 5;
            const hub = Math.max(9, g.rp * .45);
            const angle = state.angle ? state.angle(i) : 0;
            return `<g class="gear-cog">
                <g transform="rotate(${angle.toFixed(2)} ${g.x.toFixed(1)} ${g.y.toFixed(1)})">
                    <path d="${toothPath(g)}" fill="${fill}" stroke="${stroke}" stroke-width="${glow || bad ? 2.4 : 1.6}" stroke-linejoin="round"/>
                    <circle cx="${g.x.toFixed(1)}" cy="${g.y.toFixed(1)}" r="${(g.rp * .72).toFixed(1)}" fill="none" stroke="${stroke}" stroke-width="1" opacity=".35"/>
                    <line x1="${g.x.toFixed(1)}" y1="${(g.y - hub).toFixed(1)}" x2="${g.x.toFixed(1)}" y2="${(g.y - g.rp * .72).toFixed(1)}" stroke="${stroke}" stroke-width="1.6" opacity=".5"/>
                </g>
                <circle cx="${g.x.toFixed(1)}" cy="${g.y.toFixed(1)}" r="${hub.toFixed(1)}" fill="${isStart && !isOk && !bad ? '#FFE680' : 'var(--surface)'}" stroke="${stroke}" stroke-width="1.6"/>
                <text x="${g.x.toFixed(1)}" y="${(g.y + 4.5).toFixed(1)}" font-size="13" font-weight="900" text-anchor="middle" style="fill:${labelFill}">${GEAR_NAMES[i]}</text>
                ${sym ? `<text x="${g.x.toFixed(1)}" y="${symY.toFixed(1)}" font-size="17" font-weight="900" text-anchor="middle" style="fill:${isOk ? 'var(--ok)' : (isTarget ? 'var(--primary)' : 'var(--text)')}">${sym}</text>` : ''}
            </g>`;
        });
        if (state.spark) {
            const [a, b] = state.spark.map(i => gears[i]);
            const mx = (a.x + b.x) / 2, my = (a.y + b.y) / 2;
            parts.push(`<g><circle cx="${mx.toFixed(1)}" cy="${my.toFixed(1)}" r="11" fill="#E5484D" stroke="#141127" stroke-width="1.5"/><text x="${mx.toFixed(1)}" y="${(my + 5).toFixed(1)}" font-size="14" text-anchor="middle" style="fill:#fff">⚡</text></g>`);
        }
        return `<svg class="gears-svg" viewBox="0 0 ${lay.width} ${lay.height}" style="width:${Math.min(340, Math.round(lay.width * 1.45))}px" aria-hidden="true">${parts.join('')}</svg>`;
    }

    function gearsView(q, answered) {
        const d = q.gearsData;
        if (!d) return q.lines ? h('div.lines', q.lines.map(l => h('div', l)), h('div.ask', q.ask)) : null;
        const lay = gearLayout(d);
        const n = d.count;
        const chips = reveal => h('div.h-row', lay.gears.map((g, i) => {
            const isStart = i === d.startIdx, isTarget = i === d.targetIdx;
            const cls = (isTarget && reveal[i]) ? '.ok' : (isTarget || isStart ? '.focus' : '');
            if (d.speed) return h('span.h-chip' + cls, `${GEAR_NAMES[i]}: ${d.teeth[i]}` + (isStart ? ` · ${d.turns} ↻` : ''));
            const sym = reveal[i] || isStart ? (d.dirs[i] === 'cw' ? '↻' : '↺') : '?';
            return h('span.h-chip' + cls, `${GEAR_NAMES[i]}: ${sym}`);
        }));
        const none = Array(n).fill(false);
        if (!answered) return h('div.gears-view', h('div.gears-svg-wrap', {html: gearSvg(d, lay, {reveal: none})}), chips(none));
        const sign = i => (d.dirs[i] === 'cw' ? 1 : -1);
        let duration, stateAt;
        if (d.speed) {
            // Every gear turns at once: the first makes its turns, the others as many as their teeth allow
            duration = Math.min(9000, 1600 * d.turns + 800);
            stateAt = tt => {
                const turnsA = d.turns * ease(tt);
                return {reveal: Array(n).fill(tt >= 1), angle: i => sign(i) * 360 * turnsA * d.teeth[0] / d.teeth[i], turnsA};
            };
        } else {
            // First the movement travels from gear to gear, one contact at a time; then the whole train turns
            const steps = Math.max(...lay.dist) + 1;
            const wave = .55;
            duration = 1100 * steps + 2600;
            stateAt = tt => {
                const reached = Math.floor(clamp01(tt / wave) * steps + 1e-9);
                const reveal = lay.dist.map(x => x >= 0 && x < reached);
                const pulse = tt < wave ? lay.dist.indexOf(Math.min(steps - 1, reached)) : -1;
                if (d.isRing && d.isJammed) {
                    // The last gear meets the first turning the same way: they block each other
                    const ring = Array.from({length: n}, (_, k) => (d.startIdx + k) % n);
                    const lastIdx = ring[n - 1];
                    const show = ring.map((_, k) => k < Math.floor(clamp01(tt / wave) * n + 1e-9));
                    const done = tt >= wave;
                    const shake = done ? Math.sin(tt * 90) * 2.5 * (1 - tt) : 0;
                    return {
                        reveal: ring.reduce((acc, gi, k) => (acc[gi] = show[k], acc), Array(n).fill(false)),
                        pulse: done ? -1 : ring[Math.min(n - 1, show.filter(Boolean).length)],
                        conflict: done ? [lastIdx, d.startIdx] : null, spark: done ? [lastIdx, d.startIdx] : null,
                        angle: () => shake,
                    };
                }
                if (d.isRing) {
                    const ring = Array.from({length: n}, (_, k) => (d.startIdx + k) % n);
                    const count = Math.floor(clamp01(tt / wave) * n + 1e-9);
                    const r2 = Array(n).fill(false);
                    ring.forEach((gi, k) => { r2[gi] = k < count; });
                    const turn = tt > wave ? (tt - wave) / (1 - wave) : 0;
                    return {reveal: r2, pulse: tt < wave ? ring[Math.min(n - 1, count)] : -1, angle: i => sign(i) * 200 * ease(turn) * 10 / lay.gears[i].t};
                }
                const turn = tt > wave ? (tt - wave) / (1 - wave) : 0;
                return {reveal, pulse, angle: i => sign(i) * 220 * ease(turn) * 10 / lay.gears[i].t};
            };
        }
        const draw = tt => {
            const s = stateAt(tt);
            const kids = [h('div.gears-svg-wrap', {html: gearSvg(d, lay, s)})];
            if (d.speed) {
                kids.push(h('div.h-row', lay.gears.map((g, i) => h('span.h-chip' + (i === n - 1 && tt >= 1 ? '.ok' : (i === 0 ? '.focus' : '')),
                    `${GEAR_NAMES[i]}: ${(s.turnsA * d.teeth[0] / d.teeth[i]).toFixed(1).replace(/\.0$/, '')} ${d.dirs[i] === 'cw' ? '↻' : '↺'}`))));
            } else kids.push(chips(s.reveal));
            return h('div.gears-view', kids);
        };
        return player(replayKey(q, 'gears'), duration, draw);
    }

    // ---------- lasers ----------
    function lasersView(q, answered) {
        const d = q.lasersData;
        if (!d) return gridView(q, answered);
        if (!answered) return laserBoard(q, 0, false);
        const segs = Math.max(1, (d.path || []).length - 1);
        return player(replayKey(q, 'lasers'), 500 * segs + 700, tt => laserBoard(q, ease(tt), true));
    }

    function laserBoard(q, frac, answered) {
        const d = q.lasersData;
        const n = d.size;
        const right = optionText(q, q.options[q.answer]);
        const gap = 4;
        const cellSize = Math.max(38, Math.min(46, Math.floor((270 - (n - 1) * gap) / n)));
        const gridDim = n * cellSize + (n - 1) * gap;
        const centre = ([x, y]) => [x * (cellSize + gap) + cellSize / 2, y * (cellSize + gap) + cellSize / 2];
        // The beam grows along its path: how far it has got, and which cells it has already passed
        const pts = (d.path || []).map(centre);
        const lens = pts.slice(1).map((p, i) => Math.hypot(p[0] - pts[i][0], p[1] - pts[i][1]));
        const total = lens.reduce((a, b) => a + b, 0);
        let left = frac * total, reachedIdx = 0;
        const shown = pts.length ? [pts[0]] : [];
        for (let i = 0; i < lens.length && left > 0; i++) {
            const k = Math.min(1, left / lens[i]);
            shown.push([pts[i][0] + (pts[i + 1][0] - pts[i][0]) * k, pts[i][1] + (pts[i + 1][1] - pts[i][1]) * k]);
            if (k >= 1) reachedIdx = i + 1;
            left -= lens[i];
        }
        const done = answered && frac >= 1;
        let beamOverlay = null;
        if (answered && shown.length > 1) {
            const head = shown[shown.length - 1];
            beamOverlay = h('div.laser-beam-svg', {
                style: `width:${gridDim}px;height:${gridDim}px`,
                html: `<svg viewBox="0 0 ${gridDim} ${gridDim}" width="${gridDim}" height="${gridDim}"><polyline class="laser-beam-line" points="${shown.map(p => p[0].toFixed(1) + ',' + p[1].toFixed(1)).join(' ')}"/>${done ? '' : `<circle cx="${head[0].toFixed(1)}" cy="${head[1].toFixed(1)}" r="5" fill="#FF4D6D"/>`}</svg>`,
            });
        }
        const passed = new Set((d.path || []).slice(0, reachedIdx + 1).map(([x, y]) => x + ',' + y));
        const mirrorSvg = (window.ThinkHopUI && window.ThinkHopUI.mirrorSvg) || ((tp) => `<svg class="mirror-svg" viewBox="0 0 36 36"><line x1="6" y1="${tp === '/' ? 30 : 6}" x2="30" y2="${tp === '/' ? 6 : 30}" stroke="#38BDF8" stroke-width="4.5"/></svg>`);
        const dirX = (d.path && d.path.length > 1) ? (d.path[1][0] - d.startX) : 1;
        const dirY = (d.path && d.path.length > 1) ? (d.path[1][1] - d.startY) : 0;
        const dirCls = (dirX === 1) ? '.dir-right' : ((dirX === -1) ? '.dir-left' : ((dirY === 1) ? '.dir-down' : '.dir-up'));
        const gridCells = d.grid.flatMap((row, y) => row.map((cell, x) => {
            const isEmitter = (x === d.startX && y === d.startY);
            const isSlash = (cell === '/'), isBslash = (cell === '\\');
            const isTarget = GEMS_LIST.includes(cell);
            const isHit = isTarget && done && cell === right;
            const isReflecting = answered && (isSlash || isBslash) && passed.has(x + ',' + y);
            const cls = (isEmitter ? ('.emitter' + dirCls) : '') + (isSlash ? '.mirror-slash' : '') + (isBslash ? '.mirror-bslash' : '') +
                (isReflecting ? '.reflecting' : '') + (isTarget ? '.target-gem' : '') + (isHit ? '.hit' : '');
            if (isSlash || isBslash) return h('div.laser-cell' + cls, {html: mirrorSvg(cell, isReflecting)});
            if (isEmitter) return h('div.laser-cell' + cls, h('span.laser-torch', cell));
            return h('div.laser-cell' + cls, cell);
        }));
        return h('div.lasers-view',
            h('div.laser-grid-wrap',
                beamOverlay,
                h('div.laser-grid', {
                    style: `width:${gridDim}px;height:${gridDim}px;grid-template-columns:repeat(${n},${cellSize}px);grid-template-rows:repeat(${n},${cellSize}px);gap:${gap}px`,
                }, gridCells)));
    }

    // ---------- tanks and pipes ----------
    /** The water's way: out of each tank through its lowest open pipe, until a tank has no way out. */
    function pipesSteps(d) {
        const cons = d.connections || [];
        const lowestOpen = tk => cons.filter(c => c.from === tk && !c.blocked).sort((a, b) => a.h - b.h)[0];
        const steps = [];
        let at = d.source || 'A';
        for (let guard = 0; guard < 6; guard++) {
            const out = lowestOpen(at);
            steps.push({tank: at, level: out ? (out.h === 2 ? .62 : .26) : 1, pipe: out || null});
            if (!out) break;
            at = out.to;
        }
        return steps;
    }

    function pipesStateAt(d, tt) {
        const steps = pipesSteps(d);
        const PIPE = .6;
        let u = tt * (steps.length + (steps.length - 1) * PIPE);
        const level = {}, flowing = new Set();
        for (const s of steps) {
            level[s.tank] = s.level * ease(clamp01(u));
            u -= 1;
            if (u <= 0 || !s.pipe) break;
            flowing.add(s.pipe);
            u -= PIPE;
            if (u <= 0) break;
        }
        return {level, flowing, done: tt >= 1};
    }

    function pipesSvg(q, state) {
        const d = q.pipesData;
        const source = d.source || 'A';
        const cons = d.connections || [];
        const W = 54, H = 64, ROW = 114;
        const pos = {[source]: {x: 160, y: 30}};
        cons.filter(c => c.from === source).forEach(c => {
            const other = cons.filter(x => x.from === source).find(x => x !== c);
            pos[c.to] = {x: !other || c.to < other.to ? 70 : 250, y: 30 + ROW};
        });
        cons.filter(c => c.from !== source).forEach(c => {
            pos[c.to] = {x: pos[c.from].x, y: pos[c.from].y + ROW}; // straight below the tank that feeds it
        });
        const pipeY = (tk, hgt) => pos[tk].y + H - (hgt === 2 ? .62 : .26) * H;
        const parts = [];
        cons.forEach(c => {
            const a = pos[c.from], b = pos[c.to];
            const y = pipeY(c.from, c.h);
            const side = b.x < a.x ? -1 : (b.x > a.x ? 1 : (a.x < 160 ? -1 : 1));
            const x0 = a.x + side * W / 2;
            const xTurn = b.x === a.x ? a.x + side * (W / 2 + 16) : b.x;
            const pts = b.x === a.x
                ? [[x0, y], [xTurn, y], [xTurn, b.y - 14], [b.x, b.y - 14], [b.x, b.y + 4]]
                : [[x0, y], [xTurn, y], [xTurn, b.y + 4]];
            const flowing = state.flowing.has(c);
            const path = pts.map(([x, yy], i) => (i ? 'L' : 'M') + x.toFixed(1) + ' ' + yy.toFixed(1)).join(' ');
            parts.push(`<path d="${path}" fill="none" stroke="var(--muted)" stroke-width="10" stroke-linejoin="round" stroke-linecap="round"/>`);
            parts.push(`<path d="${path}" fill="none" stroke="${flowing ? '#3AA8F7' : 'var(--surface-2)'}" stroke-width="5.5" stroke-linejoin="round" stroke-linecap="round"${flowing ? ' class="pipe-flow"' : ''}/>`);
            if (flowing) {
                // A stream falls from the end of the pipe into the tank below
                const [ex, ey] = pts[pts.length - 1];
                const bottom = b.y + H - (state.level[c.to] || 0) * (H - 4);
                parts.push(`<line x1="${ex}" y1="${ey}" x2="${ex}" y2="${bottom.toFixed(1)}" stroke="#3AA8F7" stroke-width="3" stroke-linecap="round" class="pipe-flow"/>`);
            }
            if (c.blocked) {
                const vx = (x0 + xTurn) / 2, vy = y;
                parts.push(`<g><circle cx="${vx.toFixed(1)}" cy="${vy.toFixed(1)}" r="8" fill="#E5484D" stroke="#141127" stroke-width="1.5"/><path d="M${(vx - 3.5).toFixed(1)} ${(vy - 3.5).toFixed(1)} L${(vx + 3.5).toFixed(1)} ${(vy + 3.5).toFixed(1)} M${(vx + 3.5).toFixed(1)} ${(vy - 3.5).toFixed(1)} L${(vx - 3.5).toFixed(1)} ${(vy + 3.5).toFixed(1)}" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/></g>`);
            }
        });
        Object.keys(pos).sort().forEach(tk => {
            const {x, y} = pos[tk];
            const fill = state.level[tk] || 0;
            const isWin = state.done && tk === d.winner;
            const wy = y + H - fill * (H - 4);
            if (fill > 0) parts.push(`<rect x="${x - W / 2 + 2}" y="${wy.toFixed(1)}" width="${W - 4}" height="${(y + H - wy).toFixed(1)}" fill="url(#pipeWater)" rx="2"/>`);
            parts.push(`<path d="M${x - W / 2} ${y} L${x - W / 2} ${y + H - 6} Q${x - W / 2} ${y + H} ${x - W / 2 + 6} ${y + H} L${x + W / 2 - 6} ${y + H} Q${x + W / 2} ${y + H} ${x + W / 2} ${y + H - 6} L${x + W / 2} ${y}" fill="none" stroke="${isWin ? 'var(--ok)' : 'var(--text)'}" stroke-width="${isWin ? 3.5 : 2.5}" stroke-linecap="round"/>`);
            parts.push(`<text x="${x}" y="${y + 22}" font-size="17" font-weight="900" text-anchor="middle" style="fill:var(--text)" paint-order="stroke" stroke="var(--surface)" stroke-width="4">${tk}</text>`);
            if (tk === source) {
                // The tap is drawn with the theme colours: an emoji was grey and almost invisible on the dark blackboard
                const sp = `M${x - 18} ${y - 15} H${x} V${y - 4}`;
                parts.push(`<g fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="${sp}" stroke="var(--text)" stroke-width="8"/><path d="${sp}" stroke="var(--surface-2)" stroke-width="3.5"/><path d="M${x - 9} ${y - 19} V${y - 25} M${x - 14} ${y - 25} H${x - 4}" stroke="var(--text)" stroke-width="3"/></g>`);
                if (state.pouring) parts.push(`<line x1="${x}" y1="${y - 2}" x2="${x}" y2="${wy.toFixed(1)}" stroke="#3AA8F7" stroke-width="3" stroke-linecap="round" class="pipe-flow"/>`);
            }
        });
        const maxY = Math.max(...Object.values(pos).map(p => p.y)) + H + 10;
        return `<svg class="pipes-svg" viewBox="0 0 320 ${maxY}" aria-hidden="true"><defs><linearGradient id="pipeWater" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7FD0FF"/><stop offset="1" stop-color="#3AA8F7"/></linearGradient></defs>${parts.join('')}</svg>`;
    }

    function pipesView(q, answered) {
        const d = q.pipesData;
        if (!d) return q.lines ? h('div.lines', q.lines.map(l => h('div', l)), h('div.ask', q.ask)) : null;
        if (!answered) {
            return h('div.pipes-view', h('div.pipes-stage.svg', {html: pipesSvg(q, {level: {[d.source || 'A']: .12}, flowing: new Set(), done: false})}));
        }
        const steps = pipesSteps(d).length;
        return player(replayKey(q, 'pipes'), 1500 * steps + 900 * (steps - 1), tt => {
            const s = pipesStateAt(d, tt);
            s.pouring = tt < 1;
            return h('div.pipes-view', h('div.pipes-stage.svg', {html: pipesSvg(q, s)}));
        });
    }

    // ---------- cups: the swaps, one at a time ----------
    function cupsAnim(q) {
        const right = optionText(q, q.options[q.answer]);
        const items = l => l.split(/\s+/).filter(Boolean);
        const moves = q.lines[1].split('·').map(s => s.trim()).filter(Boolean).map(m => (m.includes('?') ? right : m));
        const start = q.pk === 'q_cups_back' ? items(right) : items(q.lines[0]);
        const n = start.length;
        // Where every item is before each move
        const rows = [start.map((_, i) => i)]; // rows[k][slot] = index of the item in `start`
        const apply = (row, move) => {
            const out = row.slice();
            const m = move.match(/(\d+)\s*↔\s*(\d+)/);
            if (m) { const a = +m[1] - 1, b = +m[2] - 1; [out[a], out[b]] = [out[b], out[a]]; }
            else if (move.includes('⇄')) out.reverse();
            else if (move.includes('⏪')) out.push(out.shift());
            return out;
        };
        moves.forEach(mv => rows.push(apply(rows[rows.length - 1], mv)));
        const slotOf = (row, item) => row.indexOf(item);
        const SLOT = 58;
        const targetPos = q.pv && q.pv.p !== undefined ? +q.pv.p - 1 : -1;
        const targetItem = q.pv && q.pv.x !== undefined ? q.pv.x : null;
        return player(replayKey(q, 'cups'), 1100 * moves.length + 900, tt => {
            const span = tt * moves.length;
            const k = Math.min(moves.length - 1, Math.floor(span));
            const u = tt >= 1 ? 1 : ease(clamp01((span - k) * 1.25));
            const from = rows[k], to = rows[k + 1];
            const done = tt >= 1;
            const cards = start.map((item, idx) => {
                const a = slotOf(from, idx), b = slotOf(to, idx);
                const x = (a + (b - a) * u) * SLOT;
                const lift = a !== b ? Math.sin(Math.PI * u) * (b > a ? -22 : 22) : 0;
                const finalSlot = slotOf(rows[rows.length - 1], idx);
                const isTarget = done && ((targetPos >= 0 && finalSlot === targetPos) || item === targetItem);
                return h('div.cup-card.cup-moving' + (isTarget ? '.ok' : (a !== b ? '.focus' : '')), {style: `transform:translate(${x.toFixed(1)}px, ${lift.toFixed(1)}px)`}, h('span.cup-item', item));
            });
            const slots = start.map((_, i) => h('span.cup-slot', '#' + (i + 1)));
            const chips = moves.map((m, i) => h('span.h-chip' + (i === k && !done ? '.focus.now' : (i < k || done ? '.ok' : '')), m));
            return h('div.cups-view',
                h('div.cups-anim', {style: `width:${n * SLOT}px`}, h('div.cups-anim-track', cards), h('div.cups-anim-slots', slots)),
                h('div.cup-moves-path', chips));
        });
    }

    // ---------- the turtle: every turn, or every step ----------
    function compassAnim(q) {
        const FOUR = ['⬆️', '➡️', '⬇️', '⬅️'];
        if (q.grid) {
            const n = q.grid.length;
            let y = 0, x = 0;
            q.grid.forEach((row, r) => row.forEach((c, k) => { if (c === '🐢') { y = r; x = k; } }));
            const label = (r, k) => String.fromCharCode(65 + r * n + k);
            const MOVES = {'⬆️': [-1, 0], '➡️': [0, 1], '⬇️': [1, 0], '⬅️': [0, -1]};
            const steps = q.lines[0].split(/\s+/).filter(Boolean);
            const cells = [[y, x, false]];
            steps.forEach(s => {
                const [dy, dx] = MOVES[s] || [0, 0];
                const [cy, cx] = cells[cells.length - 1];
                const inside = cy + dy >= 0 && cy + dy < n && cx + dx >= 0 && cx + dx < n;
                cells.push(inside ? [cy + dy, cx + dx, false, dy, dx] : [cy, cx, true, dy, dx]);
            });
            const C = 44;
            return player(replayKey(q, 'walk'), 650 * steps.length + 700, tt => {
                const span = tt * steps.length;
                const k = Math.min(steps.length - 1, Math.floor(span));
                const u = tt >= 1 ? 1 : ease(clamp01((span - k) * 1.3));
                const [y0, x0] = cells[k];
                const [y1, x1, bump, dy, dx] = cells[k + 1];
                // Against the edge the turtle tries, bumps and comes back
                const nudge = bump ? Math.sin(Math.PI * u) * .3 : 0;
                const ty = y0 + (y1 - y0) * u + (bump ? dy * nudge : 0), tx = x0 + (x1 - x0) * u + (bump ? dx * nudge : 0);
                const end = tt >= 1;
                const board = [];
                for (let r = 0; r < n; r++) for (let c = 0; c < n; c++) {
                    const visited = cells.slice(0, k + 2).some(([cy, cx]) => cy === r && cx === c);
                    board.push(h('span.walk-cell' + (end && r === cells[cells.length - 1][0] && c === cells[cells.length - 1][1] ? '.ok' : (visited ? '.seen' : '')), label(r, c)));
                }
                const chips = steps.map((s, i) => h('span.h-chip' + (i === k && !end ? '.focus.now' : (i < k || end ? (cells[i + 1][2] ? '.bad' : '.ok') : '')), (i + 1) + '. ' + s + (cells[i + 1][2] && (i <= k || end) ? ' 🚫' : '')));
                return h('div.walk-anim',
                    h('div.walk-board', {style: `grid-template-columns:repeat(${n},${C}px);width:${n * C}px`}, board,
                        h('span.walk-turtle', {style: `transform:translate(${(tx * C).toFixed(1)}px, ${(ty * C).toFixed(1)}px)${bump && u > 0 && u < 1 ? ' rotate(' + (Math.sin(u * 30) * 8).toFixed(1) + 'deg)' : ''}`}, '🐢')),
                    h('div.cup-moves-path', chips));
            });
        }
        // Turns on the spot: the arrow turns by each step, and the running direction is shown under it
        const startDir = q.lines[0].replace('🐢', '').trim();
        const startIdx = Math.max(0, FOUR.indexOf(startDir));
        const turns = (q.lines[1] || '').split('·').map(s => s.trim()).filter(Boolean);
        const deltas = turns.map(tn => (parseInt(tn.replace(/[^\d]/g, ''), 10) || 0) * (tn.includes('↺') ? -1 : 1));
        const acc = [0];
        deltas.forEach(dl => acc.push(acc[acc.length - 1] + dl));
        return player(replayKey(q, 'turns'), 900 * turns.length + 700, tt => {
            const span = tt * turns.length;
            const k = Math.min(turns.length - 1, Math.floor(span));
            const u = tt >= 1 ? 1 : ease(clamp01((span - k) * 1.3));
            const angle = acc[k] + (acc[k + 1] - acc[k]) * u;
            const facing = FOUR[(((startIdx + Math.round(angle / 90)) % 4) + 4) % 4];
            const end = tt >= 1;
            const chips = turns.map((tn, i) => h('span.h-chip' + (i === k && !end ? '.focus.now' : (i < k || end ? '.ok' : '')), tn));
            return h('div.turn-anim',
                h('div.turn-dial', h('span.turn-arrow', {style: `transform:rotate(${(startIdx * 90 + angle).toFixed(1)}deg)`}, '⬆️'), h('span.turn-turtle', '🐢')),
                h('div.h-row', h('span.h-chip' + (end ? '.ok' : '.focus'), '🐢 ' + facing)),
                h('div.cup-moves-path', chips));
        });
    }

    // ---------- lamps: the buttons pressed one by one ----------
    function lampsAnim(q) {
        const lampsOf = s => [...s.trim()].filter(c => c === '💡' || c === '⚫');
        const buttons = {};
        q.lines.slice(1).forEach(l => {
            const m = l.match(/^\s*([A-Z])\s*→\s*(.*)$/);
            if (m) buttons[m[1]] = m[2].split(/\s+/).filter(Boolean).map(Number);
        });
        const isBack = q.pk === 'q_lamps_back';
        const right = optionText(q, q.options[q.answer]);
        const first = lampsOf(isBack ? q.lines[0].split('→')[0] : q.lines[0]);
        const order = isBack ? right.split('+').map(s => s.trim()) : (q.ask || '').split('=')[0].trim().split(/\s+/).filter(b => buttons[b]);
        const states = [first];
        order.forEach(b => states.push(states[states.length - 1].map((c, i) => ((buttons[b] || []).includes(i + 1) ? (c === '💡' ? '⚫' : '💡') : c))));
        return player(replayKey(q, 'lamps'), 1000 * order.length + 700, tt => {
            const span = tt * order.length;
            const k = Math.min(order.length - 1, Math.floor(span));
            const u = tt >= 1 ? 1 : clamp01(span - k);
            const flipped = u >= .45;
            const row = flipped ? states[k + 1] : states[k];
            const touched = buttons[order[k]] || [];
            const end = tt >= 1;
            return h('div.lamps-anim',
                h('div.lamps-anim-row', row.map((c, i) => h('span.lamp-cell' + (touched.includes(i + 1) && !end ? (flipped ? '.flash' : '.armed') : ''), bulbs(c), h('small', String(i + 1))))),
                h('div.lamps-buttons-grid', Object.keys(buttons).map(b => h('div.lamp-btn-chip' + (b === order[k] && !end ? '.pressed' : ''),
                    h('span.lamp-btn-letter', b), h('span.lamp-btn-arrow', '➔'), h('span.lamp-btn-nums', buttons[b].join(' '))))),
                h('div.cup-moves-path', order.map((b, i) => h('span.h-chip' + (i === k && !end ? '.focus.now' : (i < k || end ? '.ok' : '')), b))));
        });
    }

    window.ThinkHopViews = {line, riddleText, hasOrigamiGuidelines, optionText, whyLines, promptText, questionBody};
})();
