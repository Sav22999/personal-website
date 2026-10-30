// Piece artwork drawn for this project. Each piece is a <symbol> filled with CSS variables,
// so the same shapes serve both colours: --pf (fill), --ps (outline), --pd (details), --ph (highlight).
// A style picks a shape set and a palette (palettes live in style.css under [data-pieces]).
// The classic fills are lathe-like gradients (light band on the left, shade on the right) for a turned-wood 3D look.
const PIECE_SVG = (() => {
    const pd = 'style="fill:none;stroke:var(--pd);stroke-width:3"';
    const pdFill = 'style="fill:var(--pd);stroke:none"';
    const hl = 'style="fill:var(--ph);stroke:none"';

    const base = '<path d="M22 83h56a4 4 0 014 4v5H18v-5a4 4 0 014-4z"/>';
    const classic = {
        p: `<path d="M41 47h18c0 12 4 22 13 30H28c9-8 13-18 13-30z"/>
            <path d="M25 77h50a3 3 0 013 3v3H22v-3a3 3 0 013-3z"/>${base}
            <path d="M34 41h32a3.25 3.25 0 010 6.5H34a3.25 3.25 0 010-6.5z"/>
            <circle cx="50" cy="28" r="13.5"/>
            <ellipse ${hl} cx="45" cy="22.5" rx="4.5" ry="3.2" transform="rotate(-30 45 22.5)"/>`,
        r: `<path d="M27 15h10v8h8v-8h10v8h8v-8h10v21l-6 6H33l-6-6z"/>
            <path d="M34 42h32l3 33H31z"/>
            <path d="M27 75h46v8H27z"/>${base}
            <path ${pd} d="M33 36h34M35 58h30"/>`,
        n: `<path d="M30 83c0-14 7-22 16-28-7 1-14 4-20 2-6-2-9-8-5-14l17-17c2-6 6-10 10-12l2-7 6 8c14 3 24 17 22 38-1 12-6 20-8 30z"/>${base}
            <circle ${pdFill} cx="47" cy="30" r="3"/>
            <circle ${pdFill} cx="25.5" cy="47.5" r="2"/>
            <path ${pd} d="M59 17c10 7 15 20 13 37"/>`,
        b: `<path d="M40 63c0 8-5 14-11 20h42c-6-6-11-12-11-20z"/>${base}
            <path d="M50 20c-16 9-21 24-14 36h28c7-12 2-27-14-36z"/>
            <path d="M33 56h34a3.5 3.5 0 010 7H33a3.5 3.5 0 010-7z"/>
            <circle cx="50" cy="13.5" r="6.5"/>
            <ellipse ${hl} cx="48" cy="11.5" rx="2.2" ry="1.6"/>
            <path ${pd} d="M57 30l-10 14"/>`,
        q: `<circle cx="18" cy="23" r="5"/><circle cx="34" cy="15" r="5"/><circle cx="50" cy="11" r="5"/><circle cx="66" cy="15" r="5"/><circle cx="82" cy="23" r="5"/>
            <path d="M19 27l11 33h40l11-33-15 18-1-25-10 24-5-27-5 27-10-24-1 25z"/>
            <path d="M30 60h40l-2 7H32z"/>
            <path d="M33 67c0 7-5 11-9 16h52c-4-5-9-9-9-16z"/>${base}`,
        k: `<path d="M46.5 4h7v6h6v7h-6v8h-7v-8h-6v-7h6z"/>
            <path d="M50 25c15 0 25 9 23 21l-6 14H33l-6-14c-2-12 8-21 23-21z"/>
            <path d="M32 60h36v7H32z"/>
            <path d="M35 67c0 7-5 11-9 16h48c-4-5-9-9-9-16z"/>${base}
            <path ${pd} d="M50 31v22M40 41h20"/>`,
    };

    // Modern: flat geometric shapes on a shared plinth, with a coloured accent line.
    const plinth = '<rect x="22" y="80" width="56" height="11" rx="4"/>';
    const modern = {
        p: `<path d="M38 56h24l8 24H30z"/>
            <circle cx="50" cy="40" r="14"/>${plinth}`,
        r: `<path d="M27 16h12v8h5v-8h12v8h5v-8h12v22H27z"/>
            <path d="M33 38h34v42H33z"/>${plinth}
            <path ${pd} d="M33 50h34"/>`,
        n: `<path d="M31 80l3-22-12-6 4-13 22-22 5-9 6 9c13 9 17 29 13 63z"/>${plinth}
            <circle ${pdFill} cx="45" cy="32" r="3.5"/>
            <path ${pd} d="M58 20l9 14"/>`,
        b: `<path d="M50 20c15 9 18 26 9 38H41c-9-12-6-29 9-38z"/>
            <path d="M41 58h18l9 22H32z"/>
            <circle cx="50" cy="13" r="6"/>${plinth}
            <path ${pd} d="M57 32l-9 11"/>`,
        q: `<path d="M24 26l13 34h26l13-34-15 15-11-24-11 24z"/>
            <circle cx="24" cy="24" r="5"/><circle cx="50" cy="13" r="5"/><circle cx="76" cy="24" r="5"/>
            <path d="M36 60h28l8 20H28z"/>${plinth}
            <path ${pd} d="M36 66h28"/>`,
        k: `<path d="M46 4h8v7h7v8h-7v9h-8v-9h-7v-8h7z"/>
            <path d="M30 30h40l-6 30H36z"/>
            <path d="M36 60h28l8 20H28z"/>${plinth}
            <path ${pd} d="M36 66h28"/>`,
    };

    const SHAPES = { classic, modern };
    const STYLES = { classic: 'classic', wood: 'classic', flat: 'classic', modern: 'modern' };
    let current = 'classic';

    const gradient = (id, stops) => `<linearGradient id="${id}" x1="0" y1="0" x2="1" y2="0.18">${stops.map(([o, c]) => `<stop offset="${o}" stop-color="${c}"/>`).join('')}</linearGradient>`;
    const defs = '<defs>'
        + gradient('pg-w', [[0, '#D3CAB2'], [0.22, '#FFFFFF'], [0.5, '#F2ECDD'], [0.82, '#C8BC9F'], [1, '#9A8E70']])
        + gradient('pg-b', [[0, '#3A3846'], [0.22, '#7A7787'], [0.46, '#33313E'], [0.8, '#191820'], [1, '#09080D']])
        + gradient('wd-w', [[0, '#DDB57A'], [0.22, '#FBE5BD'], [0.5, '#EBC88F'], [0.82, '#CB9A5B'], [1, '#A2703A']])
        + gradient('wd-b', [[0, '#6A4126'], [0.22, '#A26B41'], [0.46, '#6B4126'], [0.8, '#482914'], [1, '#2A1709']])
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
