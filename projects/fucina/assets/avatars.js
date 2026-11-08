/* Fucina — profile pictures: original characters drawn here as SVG, on a background colour.
 * The artwork lives in this file; which items exist for sale, their price in Sparks and whether they are on sale
 * live in the database (fc_avatar_items, edited from the admin portal). A profile picture is "character:background";
 * an empty character shows the initial of the username. */
(function () {
    'use strict';

    const INK = '#1B1B4B';
    const eyes = (x1, x2, y, r, fill) => `<circle cx="${x1}" cy="${y}" r="${r}" fill="${fill || INK}"/><circle cx="${x2}" cy="${y}" r="${r}" fill="${fill || INK}"/>`;
    const line = (d, colour, w) => `<path d="${d}" fill="none" stroke="${colour || INK}" stroke-width="${w || 2}" stroke-linecap="round" stroke-linejoin="round"/>`;

    /** Characters: name in each language and the drawing, in a 64×64 box. */
    const CHARACTERS = {
        bit: {
            name: {it: 'Bit, il robot', en: 'Bit the robot'},
            svg: `<rect x="20" y="50" width="24" height="16" rx="5" fill="#AEB8D2"/>${line('M32 20V12', '#8E98B8', 2.5)}<circle cx="32" cy="10" r="3.5" fill="#FF5B2E"/>
<rect x="11" y="28" width="5" height="11" rx="2" fill="#8E98B8"/><rect x="48" y="28" width="5" height="11" rx="2" fill="#8E98B8"/>
<rect x="16" y="20" width="32" height="28" rx="7" fill="#C9D2E8"/><rect x="20" y="25" width="24" height="14" rx="4" fill="${INK}"/>${eyes(27, 37, 32, 3, '#5CF2D0')}${line('M27 44h10', '#8E98B8')}`,
        },
        pixel: {
            name: {it: 'Pixel, la gatta', en: 'Pixel the cat'},
            svg: `<path d="M16 64c2-8 8-11 16-11s14 3 16 11z" fill="#E8964A"/><path d="M14 28 18 8l11 12z" fill="#F2A65A"/><path d="M50 28 46 8 35 20z" fill="#F2A65A"/>
<path d="M17 22l2-9 5 6z" fill="#FFD1DC"/><path d="M47 22l-2-9-5 6z" fill="#FFD1DC"/><ellipse cx="32" cy="36" rx="20" ry="18" fill="#F2A65A"/>
${line('M32 19v6M26 20l1 5M38 20l-1 5', '#D9803A', 2.5)}<ellipse cx="25" cy="35" rx="3" ry="4" fill="${INK}"/><ellipse cx="39" cy="35" rx="3" ry="4" fill="${INK}"/>
<path d="M30 41h4l-2 2.5z" fill="#E86A8A"/>${line('M32 43.5c-1 2.5-4 2.5-5 1M32 43.5c1 2.5 4 2.5 5 1', INK, 1.6)}${line('M11 40l8 1M11 45l8-1M53 40l-8 1M53 45l-8-1', '#7A4A2A', 1.2)}`,
        },
        owl: {
            name: {it: 'Gufo Debug', en: 'Debug the owl'},
            svg: `<path d="M13 24 16 10l9 9zM51 24l-3-14-9 9z" fill="#8B6B4E"/><ellipse cx="32" cy="38" rx="20" ry="22" fill="#8B6B4E"/><ellipse cx="32" cy="47" rx="12" ry="13" fill="#E8D5B5"/>
${line('M27 47l2 2 2-2M33 47l2 2 2-2M30 53l2 2 2-2', '#B89A72', 1.6)}<circle cx="24" cy="30" r="8" fill="#F5EBDD"/><circle cx="40" cy="30" r="8" fill="#F5EBDD"/>${eyes(24, 40, 30, 3.5)}
<circle cx="24" cy="30" r="9" fill="none" stroke="${INK}" stroke-width="2"/><circle cx="40" cy="30" r="9" fill="none" stroke="${INK}" stroke-width="2"/>${line('M31 29h2', INK, 2)}<path d="M29 37h6l-3 5z" fill="#F5A623"/>`,
        },
        fox: {
            name: {it: 'Volpe Fullstack', en: 'Fullstack the fox'},
            svg: `<path d="M18 64c2-6 7-9 14-9s12 3 14 9z" fill="#D9601E"/><path d="M10 8 25 26l-13 5z" fill="#F07A2E"/><path d="M54 8 39 26l13 5z" fill="#F07A2E"/>
<path d="M13 13l7 12-6 2z" fill="#4A2A1A"/><path d="M51 13l-7 12 6 2z" fill="#4A2A1A"/><path d="M8 24q12 4 24 2t24-2q-2 18-10 26-6 6-14 6t-14-6Q10 42 8 24z" fill="#F07A2E"/>
<path d="M16 38q8 2 16 8 8-6 16-8-2 10-8 15-4 3-8 3t-8-3q-6-5-8-15z" fill="#FFF5EA"/>${eyes(24, 40, 35, 2.8)}<circle cx="32" cy="48" r="2.6" fill="${INK}"/>`,
        },
        octopus: {
            name: {it: 'Otto, il polpo', en: 'Otto the octopus'},
            svg: `<path d="M14 38q3 16-4 22 8 2 12-10 2 12 6 14 3-10 4-14 1 4 4 14 4-2 6-14 4 12 12 10-7-6-4-22z" fill="#8A4FD8"/><path d="M14 36a18 18 0 0 1 36 0v4H14z" fill="#9B5DE5"/>
<circle cx="22" cy="21" r="2" fill="#C29BF2"/><circle cx="42" cy="20" r="3" fill="#C29BF2"/><circle cx="25" cy="32" r="5" fill="#fff"/><circle cx="39" cy="32" r="5" fill="#fff"/>${eyes(26, 40, 33, 2.5)}${line('M28 41q4 3 8 0')}`,
        },
        ghost: {
            name: {it: 'Bug, il fantasmino', en: 'Bug the little ghost'},
            svg: `${line('M27 14q-3-6-7-6M37 14q3-6 7-6', '#E4E0F7', 2)}<path d="M14 60V30a18 18 0 0 1 36 0v30l-6-5-6 5-6-5-6 5-6-5z" fill="#F4F2FF" stroke="#D9D4F2" stroke-width="1"/>
<ellipse cx="26" cy="30" rx="3.2" ry="4.5" fill="${INK}"/><ellipse cx="38" cy="30" rx="3.2" ry="4.5" fill="${INK}"/><ellipse cx="32" cy="40" rx="3" ry="3.5" fill="${INK}"/>
<circle cx="21" cy="37" r="2.5" fill="#FFB3C7"/><circle cx="43" cy="37" r="2.5" fill="#FFB3C7"/>`,
        },
        snail: {
            name: {it: 'Graffa, la lumaca', en: 'Brace the snail'},
            svg: `<path d="M6 56h46q6 0 6-6v-2H14q-8 0-8 8z" fill="#6CC47C"/><path d="M44 54V34q0-6 6-6t6 6v14z" fill="#7BD389"/>${line('M47 29l-3-9M53 29l3-9', '#7BD389', 2.5)}
<circle cx="44" cy="19" r="3" fill="${INK}"/><circle cx="56" cy="19" r="3" fill="${INK}"/><circle cx="26" cy="38" r="16" fill="#FFC53D"/><circle cx="26" cy="38" r="16" fill="none" stroke="#E0A020" stroke-width="2"/>
<text x="26" y="46" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-weight="700" font-size="22" fill="#B5651D">{}</text>${line('M48 44q2 2 4 0')}`,
        },
        penguin: {
            name: {it: 'Brina, la pinguina', en: 'Frost the penguin'},
            svg: `<ellipse cx="32" cy="40" rx="19" ry="24" fill="#26324F"/><ellipse cx="32" cy="46" rx="13" ry="17" fill="#F4F6FB"/><path d="M20 31q12-10 24 0 0 8-12 8t-12-8z" fill="#F4F6FB"/>
${eyes(27, 37, 30, 2.5)}<path d="M28 35h8l-4 4z" fill="#F5A623"/><path d="M15 46q17 8 34 0v5q-17 8-34 0z" fill="#E5383B"/><path d="M42 51l3 9-6-1z" fill="#C42A2D"/>`,
        },
        alien: {
            name: {it: 'Zorp, l’alieno', en: 'Zorp the alien'},
            svg: `<path d="M20 64q0-12 12-12t12 12z" fill="#5CC35C"/>${line('M24 18l-4-10M40 18l4-10', '#7BE07B', 2.5)}<circle cx="20" cy="7" r="3" fill="#FF5BA0"/><circle cx="44" cy="7" r="3" fill="#FF5BA0"/>
<ellipse cx="32" cy="34" rx="20" ry="18" fill="#7BE07B"/><circle cx="23" cy="33" r="4.5" fill="#fff"/><circle cx="32" cy="27" r="5" fill="#fff"/><circle cx="41" cy="33" r="4.5" fill="#fff"/>
<circle cx="23" cy="33" r="2" fill="${INK}"/><circle cx="32" cy="27" r="2.3" fill="${INK}"/><circle cx="41" cy="33" r="2" fill="${INK}"/>${line('M26 42q6 5 12 0')}`,
        },
        dragon: {
            name: {it: 'Brace, il draghetto', en: 'Ember the little dragon'},
            svg: `<path d="M20 26 16 12l10 8zM44 26l4-14-10 8z" fill="#FFC53D"/><path d="M28 22l4-7 4 7z" fill="#FFC53D"/><path d="M14 41q0-21 18-21t18 21q0 14-18 14T14 41z" fill="#E5383B"/>
<ellipse cx="25" cy="36" rx="3" ry="4" fill="${INK}"/><ellipse cx="39" cy="36" rx="3" ry="4" fill="${INK}"/><circle cx="26" cy="34.5" r="1" fill="#fff"/><circle cx="40" cy="34.5" r="1" fill="#fff"/>
<ellipse cx="32" cy="47" rx="9" ry="6" fill="#F07070"/><circle cx="29" cy="46" r="1.4" fill="#7A1A1A"/><circle cx="35" cy="46" r="1.4" fill="#7A1A1A"/><path d="M50 46q6-3 9 1-5 0-8 3z" fill="#FFC53D"/><circle cx="56" cy="40" r="2" fill="#FFC53D" opacity=".7"/>`,
        },
        anvil: {
            name: {it: 'Incudine, il golem', en: 'Anvil the golem'},
            svg: `<path d="M32 6l2 6 6 2-6 2-2 6-2-6-6-2 6-2z" fill="#FFC53D"/><path d="M10 26h44q-1 9-12 10v10h6v10H16V46h6V36Q11 35 10 26z" fill="#5A6275"/>
<rect x="9" y="23" width="46" height="5" rx="2" fill="#7A8399"/><path d="M55 23h5q0 5-5 5z" fill="#7A8399"/>${eyes(27, 37, 33, 2.6, '#FFC53D')}${line('M28 41h8', '#FFC53D', 2)}`,
        },
        favilla: {
            name: {it: 'Favilla, la mascotte', en: 'Favilla, our mascot'},
            svg: `<g transform="translate(9 4) scale(.46)"><path fill="#FF5B2E" d="M52 3c6 24 36 36 36 74 0 25-17 40-38 40S12 102 12 77c0-17 10-28 19-36 2 12 8 17 13 13 7-11-1-31 8-51Z"/><path fill="#FFC53D" d="M51 36c4 15 24 24 24 46 0 16-11 26-25 26S25 98 25 82c0-11 7-18 13-22 2 7 5 9 8 7 5-7 0-20 5-31Z"/>
<circle cx="37" cy="68" r="5.5" fill="${INK}"/><circle cx="63" cy="68" r="5.5" fill="${INK}"/><circle cx="39" cy="66" r="1.8" fill="#fff"/><circle cx="65" cy="66" r="1.8" fill="#fff"/><circle cx="28" cy="79" r="5" fill="#FF8A5C" opacity=".75"/><circle cx="72" cy="79" r="5" fill="#FF8A5C" opacity=".75"/>
<path d="M38 80c4 7 20 7 24 0" fill="none" stroke="${INK}" stroke-width="4.5" stroke-linecap="round"/></g>`,
        },
        astronaut: {
            name: {it: 'Cosmo, l’astronauta', en: 'Cosmo the astronaut'},
            svg: `<path d="M14 64q0-14 18-14t18 14z" fill="#E6E9F2"/><circle cx="40" cy="58" r="3" fill="#FF5B2E"/><rect x="49" y="24" width="4" height="10" rx="2" fill="#C9D2E8"/>
<circle cx="32" cy="30" r="20" fill="#F4F6FB" stroke="#D5DAE8" stroke-width="1"/><rect x="18" y="20" width="28" height="20" rx="10" fill="${INK}"/>${line('M22 26q4-4 10-4', '#7FB7FF', 2.5)}${eyes(27, 37, 31, 2, '#5CF2D0')}${line('M29 35q3 2 6 0', '#5CF2D0', 1.5)}`,
        },
        unicorn: {
            name: {it: 'Unicode, l’unicorno', en: 'Unicode the unicorn'},
            svg: `<path d="M30 22l4-18 4 18z" fill="#FFC53D"/>${line('M31.5 16h5M32.5 11h3', '#D99A0F', 1.5)}<path d="M19 27l1-9 7 6z" fill="#fff" stroke="#E6E1F5" stroke-width="1"/>
<circle cx="45" cy="26" r="5.5" fill="#FF7AB6"/><circle cx="49" cy="35" r="5.5" fill="#C77DFF"/><circle cx="48" cy="45" r="5" fill="#7FB7FF"/>
<path d="M16 42q0-18 16-20t16 20q0 14-16 14T16 42z" fill="#fff" stroke="#E6E1F5" stroke-width="1"/><ellipse cx="32" cy="48" rx="8" ry="5" fill="#FFE3EE"/>
<ellipse cx="25" cy="37" rx="2.6" ry="3.4" fill="${INK}"/><ellipse cx="39" cy="37" rx="2.6" ry="3.4" fill="${INK}"/><circle cx="29" cy="48" r="1.2" fill="#C26A8E"/><circle cx="35" cy="48" r="1.2" fill="#C26A8E"/>`,
        },
    };

    /** Backgrounds: a colour or a gradient behind the character (or behind the initial). */
    const BACKGROUNDS = {
        violet: {name: {it: 'Viola', en: 'Violet'}, css: '#7048E8'},
        night: {name: {it: 'Notte', en: 'Night'}, css: '#1B1B4B'},
        ember: {name: {it: 'Brace', en: 'Ember'}, css: '#FF5B2E'},
        mint: {name: {it: 'Menta', en: 'Mint'}, css: '#12B886'},
        sky: {name: {it: 'Cielo', en: 'Sky'}, css: '#2F8CF0'},
        gold: {name: {it: 'Oro', en: 'Gold'}, css: '#FFC53D'},
        pink: {name: {it: 'Rosa', en: 'Pink'}, css: '#F03E82'},
        coal: {name: {it: 'Carbone', en: 'Coal'}, css: '#2B2D33'},
        aurora: {name: {it: 'Aurora', en: 'Aurora'}, css: 'linear-gradient(135deg, #7048E8, #12B886)'},
        terminal: {name: {it: 'Terminale', en: 'Terminal'}, css: 'linear-gradient(160deg, #06281E, #12B886)'},
        sunset: {name: {it: 'Tramonto', en: 'Sunset'}, css: 'linear-gradient(135deg, #FFC53D, #FF5B2E 45%, #F03E82 75%, #7048E8)'},
    };

    const name = item => item ? (item.name[I18N.lang] || item.name.en) : '';
    const parse = code => {
        const [c, b] = String(code || '').split(':');
        return {character: CHARACTERS[c] ? c : '', background: BACKGROUNDS[b] ? b : ''};
    };

    /** The picture of a person: their character on their background, or the initial of their name. */
    function render(code, label, cls) {
        const {character, background} = parse(code);
        const el = UI.h('span.avatar' + (cls ? '.' + cls : '') + (character ? '.drawn' : ''), {'aria-hidden': 'true'});
        if (background) el.style.background = BACKGROUNDS[background].css;
        if (character) el.innerHTML = `<svg viewBox="0 0 64 64">${CHARACTERS[character].svg}</svg>`;
        else el.textContent = String(label || '?').trim().charAt(0).toUpperCase();
        return el;
    }

    window.Avatars = {CHARACTERS, BACKGROUNDS, render, parse, name};
})();
