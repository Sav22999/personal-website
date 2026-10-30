// Piece artwork drawn for this project. Each piece is a <symbol> filled with CSS variables,
// so the same shapes serve both colours: --pf (fill), --ps (outline), --pd (details), --ph (highlight).
// A style picks a shape set and a palette (palettes live in style.css under [data-pieces]).
// The classic fills are lathe-like gradients (light band on the left, shade on the right) for a turned-wood 3D look.
const PIECE_SVG = (() => {
    const pd = 'style="fill:none;stroke:var(--pd);stroke-width:3"';
    const pdFill = 'style="fill:var(--pd);stroke:none"';
    const hl = 'style="fill:var(--ph);stroke:none"';

    // Staunton-like proportions: every piece stands on the same two-step base.
    const base = '<path d="M26 78.5h48a3.5 3.5 0 013.5 3.5v3h-55v-3a3.5 3.5 0 013.5-3.5z"/>'
        + '<path d="M20 85h60a4.5 4.5 0 014.5 4.5V93h-69v-3.5A4.5 4.5 0 0120 85z"/>';
    // A soft gloss along the left of a body; fades with --ph, so flat pieces have none.
    const gloss = (d) => `<path style="fill:none;stroke:var(--ph);stroke-width:2.6;stroke-linecap:round;opacity:0.55" d="${d}"/>`;
    const classic = {
        p: `<path d="M41.5 45h17c0 13 4.5 24.5 13.5 33.5H28c9-9 13.5-20.5 13.5-33.5z"/>${base}
            <path d="M34.5 39h31a3.5 3.5 0 010 7h-31a3.5 3.5 0 010-7z"/>
            <circle cx="50" cy="26" r="13"/>
            <ellipse ${hl} cx="44.8" cy="20.8" rx="4.6" ry="3.2" transform="rotate(-35 44.8 20.8)"/>
            ${gloss('M46 51.5c0 8-1.5 15-6 21.5')}`,
        r: `<path d="M34 39h32l3.5 35h-39z"/>
            <path d="M26 13h10.5v7h6.5v-7h14v7h6.5v-7H74v19.5l-5 6.5H31l-5-6.5z"/>
            <path d="M28.5 73.5h43a2.5 2.5 0 012.5 2.5v2.5H26V76a2.5 2.5 0 012.5-2.5z"/>${base}
            <path ${pd} d="M31 33h38"/>
            ${gloss('M40 44.5l-1.8 24')}`,
        n: `<path d="M29 79c0-13 6-21 15-27-6 1.5-12 3.5-17.5 1.5-6-2-8.5-7.5-5-13l15.5-16c2.5-6 6.5-10 10.5-12l1.5-7 6.5 7.5c15 3.5 25 18 23 39-1 12-5.5 19-8 27z"/>${base}
            <circle ${pdFill} cx="46" cy="29.5" r="3.2"/>
            <circle ${pdFill} cx="24.5" cy="45.5" r="2"/>
            <path ${pd} d="M58.5 16.5c10 7 15.5 19.5 13.5 36"/>
            <path ${pd} d="M33 52c4-1.5 7-1 11 0"/>`,
        b: `<path d="M40.5 61c0 7.5-4.5 13.5-10.5 17.5h40c-6-4-10.5-10-10.5-17.5z"/>${base}
            <path d="M50 19c-16 9-21.5 23.5-14.5 36.5h29c7-13 1.5-27.5-14.5-36.5z"/>
            <path d="M33.5 55h33a3.5 3.5 0 010 7h-33a3.5 3.5 0 010-7z"/>
            <circle cx="50" cy="12.5" r="6.5"/>
            <ellipse ${hl} cx="47.8" cy="10.3" rx="2.4" ry="1.7"/>
            <path ${pd} d="M57.5 29.5l-10 13.5"/>
            ${gloss('M46 28.5c-4 4.5-5.5 10-4.5 16.5')}`,
        q: `<path d="M34.5 63c0 6.5-4.5 11.5-10 15.5h51c-5.5-4-10-9-10-15.5z"/>${base}
            <path d="M19.5 26.5l11 30h39l11-30-14.5 15-2.5-23.5-10 21.5-3.5-25-3.5 25-10-21.5-2.5 23.5z"/>
            <path d="M29 56.5h42a3.5 3.5 0 010 7H29a3.5 3.5 0 010-7z"/>
            <circle cx="19" cy="23" r="5"/><circle cx="34" cy="15" r="5"/><circle cx="50" cy="10.5" r="5.5"/><circle cx="66" cy="15" r="5"/><circle cx="81" cy="23" r="5"/>
            <ellipse ${hl} cx="48.3" cy="8.8" rx="2" ry="1.5"/>
            <path ${pd} d="M32 49.5c12 3 24 3 36 0"/>`,
        k: `<path d="M35.5 63c0 6.5-4.5 11.5-10 15.5h49c-5.5-4-10-9-10-15.5z"/>${base}
            <path d="M50 24c15.5 0 26 9 24 21.5l-6 11.5H32l-6-11.5C24 33 34.5 24 50 24z"/>
            <path d="M30.5 56.5h39a3.5 3.5 0 010 7h-39a3.5 3.5 0 010-7z"/>
            <path d="M46.5 3.5h7v6h6v7h-6v8h-7v-8h-6v-7h6z"/>
            <path ${pd} d="M29.5 44c13.5 4 27.5 4 41 0"/>
            ${gloss('M40 32c-3.5 2.5-5 6-5 10')}`,
    };

    // Pixel: 16x16 sprites. '#' body, 'h' light, 'd' shade, 'x' outline colour inside the piece,
    // 'k' outline colour that gets no outline of its own (thin details); the outline around the rest is automatic.
    const pixelArt = (rows) => {
        const S = 5.5, OX = 6, OY = 11.5;
        const at = (r, c) => (rows[r] && rows[r][c]) || '.';
        const solid = (r, c) => !'.k'.includes(at(r, c));
        const sq = (r, c) => `M${OX + c * S} ${OY + r * S}h${S}v${S}h-${S}z`;
        const paths = { o: '', '#': '', h: '', d: '', x: '', k: '' };
        for (let r = -1; r <= rows.length; r++) {
            for (let c = -1; c <= 16; c++) {
                const ch = at(r, c);
                if (ch !== '.') paths[ch] += sq(r, c);
                else if (solid(r - 1, c) || solid(r + 1, c) || solid(r, c - 1) || solid(r, c + 1)) paths.o += sq(r, c);
            }
        }
        const fill = { o: '--ps', '#': '--pf', h: '--ph', d: '--pd', x: '--ps', k: '--ps' };
        return `<g shape-rendering="crispEdges">${Object.entries(paths).filter(([, d]) => d).map(([k, d]) => `<path style="fill:var(${fill[k]});stroke:none" d="${d}"/>`).join('')}</g>`;
    };
    const pixel = {
        p: pixelArt([
            '................', '................', '................',
            '......h##d......', '.....h####d.....', '.....h####d.....', '......h##d......',
            '.....h####d.....', '......h##d......', '......h##d......', '.....h####d.....',
            '....h######d....', '...h########d...', '...h########d...']),
        r: pixelArt([
            '................', '................',
            '...h#..h#..#d...', '...h########d...', '....xxxxxxxx....', '....h######d....',
            '....h######d....', '....h######d....', '....h######d....', '....h######d....',
            '...h########d...', '...xxxxxxxxxx...', '..h##########d..', '..h##########d..']),
        n: pixelArt([
            '................', '................',
            '......##........', '.....h###.......', '....h#####......', '...##x#####.....',
            '..h########d....', '.h#########d....', '.###d..#####d...', '......h#####d...',
            '.....h######d...', '....h#######d...', '..h##########d..', '..h##########d..']),
        b: pixelArt([
            '................',
            '.......hd.......', '......h##d......', '.....h##x#d.....', '.....h#x##d.....',
            '.....h####d.....', '......h##d......', '.....xxxxxx.....', '......h##d......',
            '......h##d......', '.....h####d.....', '....h######d....', '...h########d...', '...h########d...']),
        q: pixelArt([
            '..#....##....#..', '..#....##....#..', '..##..h##d..##..', '..h##########d..',
            '...h########d...', '....h######d....', '.....h####d.....', '....xxxxxxxx....',
            '.....h####d.....', '......h##d......', '.....h####d.....', '....h######d....',
            '...h########d...', '...h########d...']),
        k: pixelArt([
            '.......kk.......', '.....kkkkkk.....', '.......kk.......', '.......kk.......',
            '....h######d....', '...h########d...', '...h########d...', '....h######d....',
            '.....xxxxxx.....', '......h##d......', '.....h####d.....', '....h######d....',
            '...h########d...', '...h########d...']),
    };

    const SHAPES = { classic, pixel };
    // Style name -> shape set, in the order the picker shows them.
    const STYLES = { classic: 'classic', wood: 'classic', flat: 'classic', marble: 'classic', glass: 'classic', pixel: 'pixel' };
    let current = 'classic';

    // stops: [offset, colour, opacity?]
    const stopList = (stops) => stops.map(([o, c, a = 1]) => `<stop offset="${o}" stop-color="${c}"${a < 1 ? ` stop-opacity="${a}"` : ''}/>`).join('');
    const gradient = (id, stops) => `<linearGradient id="${id}" x1="0" y1="0" x2="1" y2="0.18">${stopList(stops)}</linearGradient>`;
    // Marble: one shading across the whole piece, with veins on top.
    const marble = (id, stops, veins) => `<linearGradient id="${id}-g" gradientUnits="userSpaceOnUse" x1="15" y1="0" x2="85" y2="12">${stopList(stops)}</linearGradient>`
        + `<pattern id="${id}" patternUnits="userSpaceOnUse" width="100" height="100"><rect width="100" height="100" fill="url(#${id}-g)"/>`
        + veins.map(([d, c, w, a]) => `<path d="${d}" fill="none" stroke="${c}" stroke-width="${w}" stroke-opacity="${a}" stroke-linecap="round"/>`).join('')
        + '</pattern>';
    const VEINS = ['M4 18c16 6 22-4 36 10s24 6 34 22 14 18 24 22', 'M10 60c12-8 20 2 30-4s14-18 26-16 18 10 30 6', 'M30 4c4 12-6 22 2 34s4 26 14 36 6 20 10 26', 'M60 30c8 6 4 16 14 22'];
    const defs = '<defs>'
        + gradient('pg-w', [[0, '#D3CAB2'], [0.22, '#FFFFFF'], [0.5, '#F2ECDD'], [0.82, '#C8BC9F'], [1, '#9A8E70']])
        + gradient('pg-b', [[0, '#3A3846'], [0.22, '#7A7787'], [0.46, '#33313E'], [0.8, '#191820'], [1, '#09080D']])
        + gradient('wd-w', [[0, '#DDB57A'], [0.22, '#FBE5BD'], [0.5, '#EBC88F'], [0.82, '#CB9A5B'], [1, '#A2703A']])
        + gradient('wd-b', [[0, '#6A4126'], [0.22, '#A26B41'], [0.46, '#6B4126'], [0.8, '#482914'], [1, '#2A1709']])
        + gradient('gl-w', [[0, '#DDEBF5', 0.7], [0.22, '#FFFFFF', 0.92], [0.5, '#E4F0F8', 0.6], [0.82, '#B4CCDD', 0.72], [1, '#86A6BD', 0.85]])
        + gradient('gl-b', [[0, '#26303F', 0.8], [0.22, '#62748C', 0.75], [0.46, '#1F2836', 0.78], [0.8, '#111722', 0.86], [1, '#06090E', 0.92]])
        + marble('mb-w', [[0, '#D9D5CC'], [0.25, '#FFFFFF'], [0.5, '#F2EFE9'], [0.8, '#D5D0C5'], [1, '#B1AA9D']],
            VEINS.map((d, i) => [d, '#8F887C', i % 2 ? 0.7 : 1.2, i % 2 ? 0.35 : 0.5]))
        + marble('mb-b', [[0, '#2B2C32'], [0.25, '#4C4D56'], [0.5, '#25262C'], [0.8, '#15161A'], [1, '#0A0A0D']],
            VEINS.map((d, i) => [d, i % 2 ? '#E8E4DA' : '#D2AE5E', i % 2 ? 0.6 : 1.1, i % 2 ? 0.25 : 0.6]))
        + '</defs>';
    const symbols = (set) => Object.entries(SHAPES[set]).map(([t, d]) => `<symbol id="pc-${t}" viewBox="0 0 100 100">${d}</symbol>`).join('');
    const colorClass = (p) => (p === p.toUpperCase() ? 'pc-w' : 'pc-b');

    return {
        STYLES: Object.keys(STYLES),
        get style() { return current; },
        // Not display:none: gradients inside a hidden SVG don't paint in every browser.
        sprite: `<svg xmlns="http://www.w3.org/2000/svg" id="piece-sprite" width="0" height="0" style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true">${defs}<g id="piece-symbols">${symbols('classic')}</g></svg>`,
        // Swapping the symbols redraws every <use> on the page, so nothing needs re-rendering.
        setStyle(name) {
            if (!STYLES[name]) name = 'classic';
            const set = STYLES[name];
            if (set !== STYLES[current]) {
                const g = document.getElementById('piece-symbols');
                if (g) g.innerHTML = symbols(set);
            }
            current = name;
            document.documentElement.dataset.pieces = name;
        },
        // p: piece letter (uppercase = white).
        html: (p) => `<svg class="pc ${colorClass(p)}" viewBox="0 0 100 100" aria-hidden="true"><use href="#pc-${p.toLowerCase()}"/></svg>`,
        // Drawn inline in a given style, for the style picker previews.
        preview: (p, name) => `<svg class="pc ${colorClass(p)}" viewBox="0 0 100 100" aria-hidden="true">${SHAPES[STYLES[name] || 'classic'][p.toLowerCase()]}</svg>`,
    };
})();
