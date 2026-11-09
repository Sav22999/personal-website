/* Fucina — profile pictures: original characters, accessories and frames drawn here as SVG, on a background colour.
 * The artwork lives in this file; which items exist for sale, their price in Sparks and whether they are on sale
 * live in the database (fc_avatar_items, edited from the admin portal).
 * A profile picture is "character:background:accessory:frame"; an empty character shows the initial of the username. */
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
        duck: {
            name: {it: 'Paperella Debug', en: 'Debug the duck'},
            svg: `<path d="M16 64c2-8 7-12 16-12s14 4 16 12z" fill="#F5B400"/>${line('M29 14c-1-4 2-6 5-5M33 14c1-5 5-5 6-2', '#FFD43B', 3)}
<ellipse cx="32" cy="35" rx="19" ry="18" fill="#FFD43B"/><circle cx="23" cy="39" r="3" fill="#FF922B" opacity=".55"/><circle cx="41" cy="39" r="3" fill="#FF922B" opacity=".55"/>
${eyes(25, 39, 32, 2.8)}<circle cx="24" cy="31" r="1" fill="#fff"/><circle cx="38" cy="31" r="1" fill="#fff"/>
<path d="M22 39c4-3 16-3 20 0 1 5-4 8-10 8s-11-3-10-8z" fill="#FF7A1C"/>${line('M25 42q7 3 14 0', '#C84B00', 1.5)}`,
        },
        hamster: {
            name: {it: 'Loop, il criceto', en: 'Loop the hamster'},
            svg: `<circle cx="16" cy="21" r="6" fill="#E8964A"/><circle cx="48" cy="21" r="6" fill="#E8964A"/><circle cx="16" cy="21" r="3.5" fill="#FFD1DC"/><circle cx="48" cy="21" r="3.5" fill="#FFD1DC"/>
<path d="M16 64c2-8 8-11 16-11s14 3 16 11z" fill="#F2A65A"/><ellipse cx="32" cy="37" rx="20" ry="17" fill="#F2A65A"/><ellipse cx="32" cy="42" rx="15" ry="11" fill="#FFF5EA"/>
<circle cx="17" cy="40" r="6" fill="#FFD8A8"/><circle cx="47" cy="40" r="6" fill="#FFD8A8"/>${eyes(25, 39, 33, 2.6)}
<circle cx="32" cy="38" r="2" fill="#E86A8A"/><rect x="30.5" y="42" width="3" height="4" rx="1" fill="#fff" stroke="${INK}" stroke-width="1"/>`,
        },
        frog: {
            name: {it: 'Hopper, la rana', en: 'Hopper the frog'},
            svg: `<path d="M16 64c2-7 8-10 16-10s14 3 16 10z" fill="#37B24D"/><circle cx="22" cy="22" r="8" fill="#51CF66"/><circle cx="42" cy="22" r="8" fill="#51CF66"/>
<ellipse cx="32" cy="38" rx="21" ry="16" fill="#51CF66"/><ellipse cx="32" cy="45" rx="14" ry="8" fill="#B2F2BB"/>
<circle cx="22" cy="22" r="5" fill="#fff"/><circle cx="42" cy="22" r="5" fill="#fff"/>${eyes(22, 42, 22, 2.6)}
<circle cx="18" cy="39" r="3" fill="#FF8787" opacity=".6"/><circle cx="46" cy="39" r="3" fill="#FF8787" opacity=".6"/>${line('M22 40q10 7 20 0', INK, 2.2)}`,
        },
        owl: {
            name: {it: 'Gufo Debug', en: 'Debug the owl'},
            svg: `<path d="M13 24 16 10l9 9zM51 24l-3-14-9 9z" fill="#8B6B4E"/><ellipse cx="32" cy="38" rx="20" ry="22" fill="#8B6B4E"/><ellipse cx="32" cy="47" rx="12" ry="13" fill="#E8D5B5"/>
${line('M27 47l2 2 2-2M33 47l2 2 2-2M30 53l2 2 2-2', '#B89A72', 1.6)}<circle cx="24" cy="30" r="8" fill="#F5EBDD"/><circle cx="40" cy="30" r="8" fill="#F5EBDD"/>${eyes(24, 40, 30, 3.5)}
<circle cx="24" cy="30" r="9" fill="none" stroke="${INK}" stroke-width="2"/><circle cx="40" cy="30" r="9" fill="none" stroke="${INK}" stroke-width="2"/>${line('M31 29h2', INK, 2)}<path d="M29 37h6l-3 5z" fill="#F5A623"/>`,
        },
        hedgehog: {
            name: {it: 'Spike, il riccio', en: 'Spike the hedgehog'},
            svg: `<path d="M10 42 8 30l7-2-3-9 9 1 1-9 8 5 6-6 4 7 9-3-1 9 7 3-4 10z" fill="#6C4E37"/>
<path d="M16 64c2-8 8-11 16-11s14 3 16 11z" fill="#8B6B4E"/><ellipse cx="32" cy="39" rx="16" ry="14" fill="#F5DEC4"/>
<circle cx="19" cy="28" r="4" fill="#D9B99B"/><circle cx="45" cy="28" r="4" fill="#D9B99B"/>${eyes(25, 39, 36, 2.5)}
<circle cx="32" cy="41" r="2.8" fill="${INK}"/>${line('M28 46q4 3 8 0', INK, 1.8)}`,
        },
        turtle: {
            name: {it: 'Shell, la tartaruga', en: 'Shell the turtle'},
            svg: `<ellipse cx="32" cy="54" rx="20" ry="12" fill="#2B8A3E" stroke="#1B5E20" stroke-width="1.5"/>${line('M20 52h24M26 44l-4 14M38 44l4 14', '#1B5E20', 1.6)}
<ellipse cx="32" cy="33" rx="16" ry="15" fill="#69DB7C"/><circle cx="24" cy="30" r="4.5" fill="#fff"/><circle cx="40" cy="30" r="4.5" fill="#fff"/>${eyes(25, 39, 30, 2.3)}
<circle cx="20" cy="36" r="2.4" fill="#FF8787" opacity=".55"/><circle cx="44" cy="36" r="2.4" fill="#FF8787" opacity=".55"/>${line('M26 39q6 4 12 0', '#2B8A3E', 2)}`,
        },
        fox: {
            name: {it: 'Volpe Fullstack', en: 'Fullstack the fox'},
            svg: `<path d="M18 64c2-6 7-9 14-9s12 3 14 9z" fill="#D9601E"/><path d="M10 8 25 26l-13 5z" fill="#F07A2E"/><path d="M54 8 39 26l13 5z" fill="#F07A2E"/>
<path d="M13 13l7 12-6 2z" fill="#4A2A1A"/><path d="M51 13l-7 12 6 2z" fill="#4A2A1A"/><path d="M8 24q12 4 24 2t24-2q-2 18-10 26-6 6-14 6t-14-6Q10 42 8 24z" fill="#F07A2E"/>
<path d="M16 38q8 2 16 8 8-6 16-8-2 10-8 15-4 3-8 3t-8-3q-6-5-8-15z" fill="#FFF5EA"/>${eyes(24, 40, 35, 2.8)}<circle cx="32" cy="48" r="2.6" fill="${INK}"/>`,
        },
        crab: {
            name: {it: 'Ferris, il granchio', en: 'Ferris the crab'},
            svg: `${line('M16 34c-6-4-7-12-3-16M48 34c6-4 7-12 3-16', '#D9482F', 3.5)}<path d="M9 14c3-5 9-3 8 3l-4-2-2 4c-4-1-4-3-2-5zM55 14c-3-5-9-3-8 3l4-2 2 4c4-1 4-3 2-5z" fill="#FF6B4A"/>
${line('M25 24v-6M39 24v-6', '#D9482F', 3)}<ellipse cx="32" cy="40" rx="20" ry="15" fill="#FF6B4A"/><path d="M14 38q4-5 9 0t9 0 9 0 9 0" fill="none" stroke="#E04F33" stroke-width="2"/>
<circle cx="25" cy="17" r="4" fill="#fff"/><circle cx="39" cy="17" r="4" fill="#fff"/>${eyes(25, 39, 17, 2.2)}${line('M27 43q5 4 10 0', INK, 2)}`,
        },
        bee: {
            name: {it: 'Thread, l’ape', en: 'Thread the bee'},
            svg: `<ellipse cx="14" cy="34" rx="8" ry="5" transform="rotate(-20 14 34)" fill="#A5D8FF" opacity=".85"/><ellipse cx="50" cy="34" rx="8" ry="5" transform="rotate(20 50 34)" fill="#A5D8FF" opacity=".85"/>
${line('M26 17l-4-8M38 17l4-8', INK, 2.2)}<circle cx="22" cy="8" r="2.6" fill="#FFD43B"/><circle cx="42" cy="8" r="2.6" fill="#FFD43B"/>
<ellipse cx="32" cy="37" rx="18" ry="19" fill="#FFD43B"/><path d="M15 43h34v5H15zM17 51h30v4H17z" fill="${INK}"/>
${eyes(25, 39, 32, 2.8)}<circle cx="20" cy="36" r="2.5" fill="#FF922B" opacity=".6"/><circle cx="44" cy="36" r="2.5" fill="#FF922B" opacity=".6"/>${line('M28 37q4 3.5 8 0', INK, 2)}`,
        },
        axolotl: {
            name: {it: 'Pinky, l’axolotl', en: 'Pinky the axolotl'},
            svg: `${line('M14 26l-8-4M13 33l-9-1M14 40l-7 3M50 26l8-4M51 33l9-1M50 40l7 3', '#F06595', 3.5)}
<path d="M17 64c2-8 7-12 15-12s13 4 15 12z" fill="#FFADCB"/><ellipse cx="32" cy="36" rx="19" ry="16" fill="#FFC2D9"/>
${eyes(24, 40, 34, 2.7)}<circle cx="20" cy="39" r="3" fill="#F06595" opacity=".45"/><circle cx="44" cy="39" r="3" fill="#F06595" opacity=".45"/>${line('M27 40q5 4 10 0', INK, 2)}`,
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
        snake: {
            name: {it: 'Monty, il pitone', en: 'Monty the python'},
            svg: `<path d="M14 64c0-10 8-14 18-14s18 4 18 14z" fill="#2B8A3E"/><path d="M22 50v14h20V50z" fill="#FFD43B"/>
<ellipse cx="32" cy="34" rx="18" ry="16" fill="#40C057"/><ellipse cx="32" cy="39" rx="13" ry="9" fill="#B2F2BB"/>
<circle cx="24" cy="30" r="4" fill="#FFD43B"/><circle cx="40" cy="30" r="4" fill="#FFD43B"/><ellipse cx="24" cy="30" rx="1.4" ry="3" fill="${INK}"/><ellipse cx="40" cy="30" rx="1.4" ry="3" fill="${INK}"/>
${line('M32 45v7l-2.5 3M32 52l2.5 3', '#F03E82', 1.8)}${line('M25 42q7 4 14 0', '#2B8A3E', 2)}`,
        },
        racoon: {
            name: {it: 'Bandit, il procione', en: 'Bandit the raccoon'},
            svg: `<path d="M14 26 17 10l10 10zM50 26 47 10 37 20z" fill="#868E96"/><path d="M16 64c2-8 8-11 16-11s14 3 16 11z" fill="#868E96"/>
<ellipse cx="32" cy="37" rx="20" ry="17" fill="#ADB5BD"/><ellipse cx="23" cy="34" rx="8" ry="6" fill="#343A40"/><ellipse cx="41" cy="34" rx="8" ry="6" fill="#343A40"/>
${eyes(24, 40, 34, 2.3, '#fff')}<ellipse cx="32" cy="43" rx="9" ry="7" fill="#F8F9FA"/><circle cx="32" cy="41" r="2.6" fill="${INK}"/>`,
        },
        sloth: {
            name: {it: 'Lazy, il bradipo', en: 'Lazy the sloth'},
            svg: `<path d="M15 64c2-8 8-12 17-12s15 4 17 12z" fill="#8B6B4E"/><ellipse cx="32" cy="36" rx="19" ry="17" fill="#9C7A5B"/>
<ellipse cx="32" cy="38" rx="15" ry="12" fill="#F5EBDD"/><ellipse cx="23" cy="35" rx="5.5" ry="4" transform="rotate(-12 23 35)" fill="#6C4E37"/><ellipse cx="41" cy="35" rx="5.5" ry="4" transform="rotate(12 41 35)" fill="#6C4E37"/>
${eyes(23, 41, 35, 2, '#fff')}<ellipse cx="32" cy="39" rx="3" ry="2" fill="${INK}"/>${line('M27 44q5 3 10 0', INK, 1.8)}`,
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
        gopher: {
            name: {it: 'Go, la marmotta', en: 'Gopher the groundhog'},
            svg: `<circle cx="17" cy="20" r="6" fill="#4DABF7"/><circle cx="47" cy="20" r="6" fill="#4DABF7"/><circle cx="17" cy="20" r="3" fill="${INK}"/><circle cx="47" cy="20" r="3" fill="${INK}"/>
<path d="M14 64V34a18 18 0 0 1 36 0v30z" fill="#74C0FC"/><circle cx="25" cy="31" r="6.5" fill="#fff"/><circle cx="39" cy="31" r="6.5" fill="#fff"/>${eyes(26, 38, 31, 2.5)}
<rect x="28" y="42" width="4" height="6" rx="1" fill="#fff"/><rect x="32" y="42" width="4" height="6" rx="1" fill="#fff"/>
<ellipse cx="32" cy="40" rx="9" ry="5" fill="#FFE8CC"/><ellipse cx="32" cy="38" rx="4" ry="2.5" fill="${INK}"/>`,
        },
        corgi: {
            name: {it: 'Fetch, il corgi', en: 'Fetch the corgi'},
            svg: `<path d="M12 28 14 7l13 13zM52 28 50 7 37 20z" fill="#E8964A"/><path d="M15 24l1-11 8 8zM49 24l-1-11-8 8z" fill="#FFD1DC"/>
<path d="M16 64c2-8 8-11 16-11s14 3 16 11z" fill="#E8964A"/><ellipse cx="32" cy="37" rx="19" ry="17" fill="#F2A65A"/>
<path d="M29 21h6l2 16a5 5 0 0 1-10 0z" fill="#FFF5EA"/><ellipse cx="32" cy="44" rx="12" ry="9" fill="#FFF5EA"/>
${eyes(24, 40, 33, 2.7)}<circle cx="32" cy="40" r="2.8" fill="${INK}"/><path d="M29 45h6v4a3 3 0 0 1-6 0z" fill="#FF8787"/>`,
        },
        panda: {
            name: {it: 'Byte, il panda', en: 'Byte the panda'},
            svg: `<path d="M15 64c2-8 8-11 17-11s15 3 17 11z" fill="${INK}"/><circle cx="16" cy="18" r="7" fill="${INK}"/><circle cx="48" cy="18" r="7" fill="${INK}"/>
<ellipse cx="32" cy="36" rx="20" ry="18" fill="#F8F9FA"/><ellipse cx="24" cy="34" rx="6" ry="7" transform="rotate(-12 24 34)" fill="${INK}"/><ellipse cx="40" cy="34" rx="6" ry="7" transform="rotate(12 40 34)" fill="${INK}"/>
${eyes(24, 40, 33, 2.2, '#fff')}<ellipse cx="32" cy="41" rx="3.2" ry="2.2" fill="${INK}"/>${line('M28 45q4 3 8 0', INK, 1.8)}`,
        },
        chameleon: {
            name: {it: 'Style, il camaleonte', en: 'Style the chameleon'},
            svg: `<path d="M18 26c2-11 12-14 22-8 4 3 6 8 6 14z" fill="#12B886"/><path d="M16 64c2-8 8-11 16-11s14 3 16 11z" fill="#51CF66"/>
<ellipse cx="32" cy="37" rx="19" ry="16" fill="#69DB7C"/><circle cx="24" cy="33" r="6" fill="#FFD43B"/><circle cx="40" cy="33" r="6" fill="#FFD43B"/>
<circle cx="24" cy="33" r="3.5" fill="#fff"/><circle cx="40" cy="33" r="3.5" fill="#fff"/>${eyes(24, 40, 33, 2)}${line('M24 44q8 4 16 0', '#2B8A3E', 2.2)}`,
        },
        koala: {
            name: {it: 'Nimbus, il koala', en: 'Nimbus the koala'},
            svg: `<circle cx="12" cy="28" r="10" fill="#868E96"/><circle cx="52" cy="28" r="10" fill="#868E96"/><circle cx="12" cy="28" r="6" fill="#F1F3F5"/><circle cx="52" cy="28" r="6" fill="#F1F3F5"/>
<path d="M16 64c2-7 8-11 16-11s14 4 16 11z" fill="#868E96"/><ellipse cx="32" cy="36" rx="18" ry="16" fill="#ADB5BD"/>
${eyes(24, 40, 33, 2.5)}<ellipse cx="32" cy="38" rx="4.5" ry="6.5" fill="#343A40"/>${line('M28 47q4 2.5 8 0', INK, 1.8)}`,
        },
        alien: {
            name: {it: 'Zorp, l’alieno', en: 'Zorp the alien'},
            svg: `<path d="M20 64q0-12 12-12t12 12z" fill="#5CC35C"/>${line('M24 18l-4-10M40 18l4-10', '#7BE07B', 2.5)}<circle cx="20" cy="7" r="3" fill="#FF5BA0"/><circle cx="44" cy="7" r="3" fill="#FF5BA0"/>
<ellipse cx="32" cy="34" rx="20" ry="18" fill="#7BE07B"/><circle cx="23" cy="33" r="4.5" fill="#fff"/><circle cx="32" cy="27" r="5" fill="#fff"/><circle cx="41" cy="33" r="4.5" fill="#fff"/>
<circle cx="23" cy="33" r="2" fill="${INK}"/><circle cx="32" cy="27" r="2.3" fill="${INK}"/><circle cx="41" cy="33" r="2" fill="${INK}"/>${line('M26 42q6 5 12 0')}`,
        },
        redpanda: {
            name: {it: 'Firefox, il panda rosso', en: 'Firefox the red panda'},
            svg: `<path d="M12 26 15 9l12 11zM52 26 49 9 37 20z" fill="#343A40"/><path d="M15 23l1-9 7 7zM49 23l-1-9-7 7z" fill="#FFF5EA"/>
<path d="M15 64c2-8 8-11 17-11s15 3 17 11z" fill="#343A40"/><ellipse cx="32" cy="37" rx="20" ry="17" fill="#D9480F"/>
<circle cx="20" cy="40" r="6" fill="#FFF5EA"/><circle cx="44" cy="40" r="6" fill="#FFF5EA"/><ellipse cx="32" cy="43" rx="8" ry="6" fill="#FFF5EA"/>
<circle cx="25" cy="29" r="2.5" fill="#FFF5EA"/><circle cx="39" cy="29" r="2.5" fill="#FFF5EA"/>${eyes(25, 39, 34, 2.5)}<circle cx="32" cy="41" r="2.5" fill="${INK}"/>`,
        },
        narwhal: {
            name: {it: 'Docker, il narvalo', en: 'Docker the narwhal'},
            svg: `<path d="M29 20 32 3l3 17z" fill="#FFD43B"/>${line('M30.5 14h3M31 9h2', '#D99A0F', 1.4)}
<path d="M13 64V36a19 19 0 0 1 38 0v28z" fill="#3BC9DB"/><path d="M19 64V44c0-6 6-10 13-10s13 4 13 10v20z" fill="#E3FAFC"/>
${eyes(23, 41, 32, 2.6)}<circle cx="20" cy="36" r="2.5" fill="#FF8787" opacity=".5"/><circle cx="44" cy="36" r="2.5" fill="#FF8787" opacity=".5"/>${line('M27 40q5 4 10 0', INK, 2)}`,
        },
        shark: {
            name: {it: 'Packet, lo squalo', en: 'Packet the shark'},
            svg: `<path d="M28 18 33 6l6 13z" fill="#339AF0"/><path d="M12 64V36c0-13 9-20 20-20s20 7 20 20v28z" fill="#339AF0"/>
<path d="M18 64V42c0-7 6-11 14-11s14 4 14 11v22z" fill="#F8F9FA"/>${eyes(23, 41, 29, 2.6)}
<path d="M22 42h20c-1 6-5 9-10 9s-9-3-10-9z" fill="#C92A2A"/><path d="M23 42l3 3 3-3 3 3 3-3 3 3 3-3z" fill="#fff"/>`,
        },
        dragon: {
            name: {it: 'Brace, il draghetto', en: 'Ember the little dragon'},
            svg: `<path d="M20 26 16 12l10 8zM44 26l4-14-10 8z" fill="#FFC53D"/><path d="M28 22l4-7 4 7z" fill="#FFC53D"/><path d="M14 41q0-21 18-21t18 21q0 14-18 14T14 41z" fill="#E5383B"/>
<ellipse cx="25" cy="36" rx="3" ry="4" fill="${INK}"/><ellipse cx="39" cy="36" rx="3" ry="4" fill="${INK}"/><circle cx="26" cy="34.5" r="1" fill="#fff"/><circle cx="40" cy="34.5" r="1" fill="#fff"/>
<ellipse cx="32" cy="47" rx="9" ry="6" fill="#F07070"/><circle cx="29" cy="46" r="1.4" fill="#7A1A1A"/><circle cx="35" cy="46" r="1.4" fill="#7A1A1A"/><path d="M50 46q6-3 9 1-5 0-8 3z" fill="#FFC53D"/><circle cx="56" cy="40" r="2" fill="#FFC53D" opacity=".7"/>`,
        },
        viking: {
            name: {it: 'Harald, il vichingo', en: 'Harald the viking'},
            svg: `<path d="M14 23c-6-2-8-9-5-14 2 5 5 8 9 9zM50 23c6-2 8-9 5-14-2 5-5 8-9 9z" fill="#F8F9FA"/>
<path d="M14 64c2-8 8-12 18-12s16 4 18 12z" fill="#5C4033"/><circle cx="32" cy="35" r="16" fill="#FFD8A8"/>
<path d="M15 37q6-3 17-3t17 3q-2 17-17 21-15-4-17-21z" fill="#E8590C"/>${eyes(25, 39, 32, 2.4)}
<path d="M15 25a17 17 0 0 1 34 0z" fill="#868E96"/><rect x="14" y="23" width="36" height="4" rx="2" fill="#ADB5BD"/>`,
        },
        pirate: {
            name: {it: 'Hook, il pirata', en: 'Hook the pirate'},
            svg: `<path d="M14 64c2-8 8-12 18-12s16 4 18 12z" fill="#C92A2A"/><circle cx="32" cy="35" r="16" fill="#FFD8A8"/>
<path d="M16 39q5-2 16-2t16 2q-2 14-16 17-14-3-16-17z" fill="#343A40"/><path d="M15 26a17 17 0 0 1 34 0z" fill="#E03131"/>
<circle cx="25" cy="32" r="3.5" fill="${INK}"/><circle cx="39" cy="32" r="2.5" fill="${INK}"/>${line('M18 27l12 7', INK, 2)}<circle cx="15" cy="38" r="2.5" fill="none" stroke="#FFD43B" stroke-width="1.8"/>`,
        },
        ninja: {
            name: {it: 'Sudo, il ninja', en: 'Sudo the ninja'},
            svg: `<path d="M50 26l9-4-3 6 5 3-9 1z" fill="#E03131"/><path d="M14 64c2-8 8-12 18-12s16 4 18 12z" fill="#212529"/>
<circle cx="32" cy="35" r="19" fill="#2B303A"/><rect x="16" y="27" width="32" height="11" rx="5.5" fill="#FFE8CC"/>
<path d="M13 24h38v5H13z" fill="#E03131"/>${eyes(25, 39, 33, 2.5)}${line('M22 29l5 1.5M42 29l-5 1.5', INK, 1.8)}`,
        },
        anvil: {
            name: {it: 'Incudine, il golem', en: 'Anvil the golem'},
            svg: `<path d="M32 6l2 6 6 2-6 2-2 6-2-6-6-2 6-2z" fill="#FFC53D"/><path d="M10 26h44q-1 9-12 10v10h6v10H16V46h6V36Q11 35 10 26z" fill="#5A6275"/>
<rect x="9" y="23" width="46" height="5" rx="2" fill="#7A8399"/><path d="M55 23h5q0 5-5 5z" fill="#7A8399"/>${eyes(27, 37, 33, 2.6, '#FFC53D')}${line('M28 41h8', '#FFC53D', 2)}`,
        },
        wizard: {
            name: {it: 'Malloc, il mago', en: 'Malloc the wizard'},
            svg: `<path d="M14 64c2-9 8-13 18-13s16 4 18 13z" fill="#5F3DC4"/><circle cx="32" cy="34" r="16" fill="#FFE8CC"/>
<path d="M15 38q4-3 17-3t17 3q-1 18-17 22-16-4-17-22z" fill="#F1F3F5"/><ellipse cx="32" cy="40" rx="5" ry="2.5" fill="#DEE2E6"/>
${eyes(25, 39, 32, 2.4)}${line('M21 28l6-1M43 28l-6-1', '#F1F3F5', 3)}<circle cx="32" cy="59" r="3" fill="#FFD43B"/>`,
        },
        cyborg: {
            name: {it: 'Kernel, il cyborg', en: 'Kernel the cyborg'},
            svg: `<path d="M14 64c2-9 8-13 18-13s16 4 18 13z" fill="#343A40"/><circle cx="32" cy="34" r="17" fill="#FFD8A8"/>
<path d="M32 17a17 17 0 0 1 0 34z" fill="#ADB5BD"/><circle cx="25" cy="32" r="2.6" fill="${INK}"/><circle cx="39" cy="32" r="4" fill="#E03131"/><circle cx="39" cy="32" r="1.6" fill="#FFF5F5"/>
${line('M32 17v34M41 24h6M42 40h5', '#495057', 1.6)}${line('M26 42h12', INK, 2)}`,
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
        griffin: {
            name: {it: 'Grifone reale', en: 'Royal griffin'},
            svg: `<path d="M13 24 14 9l12 10zM51 24 50 9 38 19z" fill="#D99A0F"/><path d="M15 64c2-9 8-13 17-13s15 4 17 13z" fill="#B47B0E"/>
<circle cx="32" cy="36" r="18" fill="#F8F9FA"/><path d="M16 32q8-6 16 0 8-6 16 0v-6a16 16 0 0 0-32 0z" fill="#FFD43B"/>
${eyes(24, 40, 33, 2.7)}<path d="M27 36h10l-5 9z" fill="#F59F00"/>`,
        },
        phoenix: {
            name: {it: 'Fenice della forgia', en: 'Forge phoenix'},
            svg: `<path d="M25 20 22 7l8 8 2-9 2 9 8-8-3 13z" fill="#FFD43B"/><path d="M15 64c2-9 8-13 17-13s15 4 17 13z" fill="#D9480F"/>
<circle cx="32" cy="36" r="18" fill="#F76707"/><path d="M18 34q7-8 14 0 7-8 14 0-3 14-14 16-11-2-14-16z" fill="#FF922B"/>
${eyes(25, 39, 33, 2.8, '#FFF9DB')}<path d="M28 37h8l-4 7z" fill="#FFD43B"/>`,
        },
        unicorn: {
            name: {it: 'Unicode, l’unicorno', en: 'Unicode the unicorn'},
            svg: `<path d="M30 22l4-18 4 18z" fill="#FFC53D"/>${line('M31.5 16h5M32.5 11h3', '#D99A0F', 1.5)}<path d="M19 27l1-9 7 6z" fill="#fff" stroke="#E6E1F5" stroke-width="1"/>
<circle cx="45" cy="26" r="5.5" fill="#FF7AB6"/><circle cx="49" cy="35" r="5.5" fill="#C77DFF"/><circle cx="48" cy="45" r="5" fill="#7FB7FF"/>
<path d="M16 42q0-18 16-20t16 20q0 14-16 14T16 42z" fill="#fff" stroke="#E6E1F5" stroke-width="1"/><ellipse cx="32" cy="48" rx="8" ry="5" fill="#FFE3EE"/>
<ellipse cx="25" cy="37" rx="2.6" ry="3.4" fill="${INK}"/><ellipse cx="39" cy="37" rx="2.6" ry="3.4" fill="${INK}"/><circle cx="29" cy="48" r="1.2" fill="#C26A8E"/><circle cx="35" cy="48" r="1.2" fill="#C26A8E"/>`,
        },
    };

    /** Accessories: drawn on top of the character (or initial) in the same 64×64 box. */
    const ACCESSORIES = {
        glasses: {
            name: {it: 'Occhiali nerd', en: 'Nerd glasses'},
            svg: `<rect x="15" y="27" width="14" height="11" rx="3" fill="#fff" fill-opacity=".22" stroke="${INK}" stroke-width="2.6"/>
<rect x="35" y="27" width="14" height="11" rx="3" fill="#fff" fill-opacity=".22" stroke="${INK}" stroke-width="2.6"/>
${line('M29 32h6M12 31l3 1M52 31l-3 1', INK, 2.4)}${line('M18 30l3 3M38 30l3 3', '#fff', 1.6)}`,
        },
        bandage: {
            name: {it: 'Cerotto', en: 'Little plaster'},
            svg: `<g transform="rotate(-18 42 23)"><rect x="35" y="20" width="14" height="6" rx="3" fill="#FFD8A8" stroke="#D9803A" stroke-width="1.2"/><rect x="40" y="20" width="4" height="6" fill="#FFE8CC"/></g>`,
        },
        bowtie: {
            name: {it: 'Papillon', en: 'Bow tie'},
            svg: `<path d="M21 49l11 5-11 5zM43 49l-11 5 11 5z" fill="#F03E82" stroke="${INK}" stroke-width="1.2"/><circle cx="32" cy="54" r="3" fill="#C2255C" stroke="${INK}" stroke-width="1.2"/>`,
        },
        flower: {
            name: {it: 'Fiorellino', en: 'Daisy flower'},
            svg: `<g transform="translate(46 20)"><circle cx="0" cy="-5" r="3.5" fill="#fff"/><circle cx="5" cy="-1" r="3.5" fill="#fff"/><circle cx="3" cy="4" r="3.5" fill="#fff"/><circle cx="-3" cy="4" r="3.5" fill="#fff"/><circle cx="-5" cy="-1" r="3.5" fill="#fff"/><circle cx="0" cy="0" r="3" fill="#FFD43B"/></g>`,
        },
        sprout: {
            name: {it: 'Germoglio', en: 'Little sprout'},
            svg: `${line('M32 19V9', '#2B8A3E', 2.4)}<path d="M32 10c-2-5-8-6-10-2 2 4 7 5 10 2zM32 10c2-5 8-6 10-2-2 4-7 5-10 2z" fill="#51CF66" stroke="#2B8A3E" stroke-width="1"/>`,
        },
        mustache: {
            name: {it: 'Baffi', en: 'Moustache'},
            svg: `<path d="M32 42c-3-3-9-3-13 1 3 3 9 3 13-1zm0 0c3-3 9-3 13 1-3 3-9 3-13-1z" fill="#343A40" stroke="${INK}" stroke-width="1.2"/>`,
        },
        bandana: {
            name: {it: 'Fascia da coder', en: 'Coder headband'},
            svg: `<path d="M13 20q19 4 38 0v5q-19 4-38 0z" fill="#FF5B2E"/><path d="M50 22l8-3-2 5 5 3-9-1z" fill="#E03131"/><circle cx="32" cy="23.5" r="2" fill="#FFD43B"/>`,
        },
        beanie: {
            name: {it: 'Berretto di lana', en: 'Wool beanie'},
            svg: `<circle cx="32" cy="6" r="4" fill="#FFD43B"/><path d="M15 20c1-9 8-12 17-12s16 3 17 12z" fill="#E03131"/><rect x="13" y="18" width="38" height="5" rx="2.5" fill="#C92A2A"/>`,
        },
        cap: {
            name: {it: 'Cappellino', en: 'Baseball cap'},
            svg: `<path d="M15 20c1-9 8-13 17-13s16 4 17 13z" fill="#2F8CF0"/><path d="M13 19h42c2 0 3 3 0 3H13z" fill="#1864AB"/><circle cx="32" cy="7" r="2" fill="#FFD43B"/>`,
        },
        beret: {
            name: {it: 'Basco da artista', en: 'Artist beret'},
            svg: `${line('M31 8l2-4', '#C92A2A', 2.2)}<ellipse cx="31" cy="14" rx="19" ry="6" transform="rotate(-8 31 14)" fill="#E03131"/><rect x="16" y="17" width="32" height="3.5" rx="1.5" fill="${INK}"/>`,
        },
        eyepatch: {
            name: {it: 'Benda da pirata', en: 'Pirate eyepatch'},
            svg: `${line('M13 24l38 14', INK, 2.2)}<path d="M19 28h11v6a5.5 5.5 0 0 1-11 0z" fill="${INK}"/>`,
        },
        scarf: {
            name: {it: 'Sciarpa', en: 'Cosy scarf'},
            svg: `<path d="M15 47q17 6 34 0v6q-17 6-34 0z" fill="#FF5B2E"/><path d="M22 49v6M32 50v6M42 49v6" stroke="#FFD43B" stroke-width="3"/><path d="M38 52l3 10h6l-2-10z" fill="#FF5B2E"/>`,
        },
        heartglasses: {
            name: {it: 'Occhiali a cuore', en: 'Heart glasses'},
            svg: `<path d="M23 30c-2-4-8-3-8 2 0 4 8 8 8 8s8-4 8-8c0-5-6-6-8-2zM41 30c-2-4-8-3-8 2 0 4 8 8 8 8s8-4 8-8c0-5-6-6-8-2z" fill="#F03E82" fill-opacity=".8" stroke="#fff" stroke-width="1.6"/>${line('M31 32h2', '#fff', 2)}`,
        },
        shades: {
            name: {it: 'Occhiali da sole', en: 'Cool shades'},
            svg: `<path d="M13 28h38v3h-3v7H35v-5h-6v5H16v-7h-3z" fill="${INK}"/><path d="M18 30h4v3h-4zM37 30h4v3h-4z" fill="#5CF2D0"/>`,
        },
        antlers: {
            name: {it: 'Corna da renna', en: 'Reindeer antlers'},
            svg: `${line('M22 19l-5-12M19 12l-5 1M20 15l4-4M42 19l5-12M45 12l5 1M44 15l-4-4', '#8B5A2B', 3)}`,
        },
        monocle: {
            name: {it: 'Monocolo', en: 'Monocle'},
            svg: `<circle cx="39" cy="33" r="7.5" fill="#fff" fill-opacity=".2" stroke="#FFD43B" stroke-width="2.4"/>${line('M45 37q3 7 1 15', '#FFD43B', 1.5)}${line('M36 30l3 3', '#fff', 1.6)}`,
        },
        chef: {
            name: {it: 'Cappello da chef', en: 'Chef toque'},
            svg: `<circle cx="23" cy="11" r="6" fill="#fff"/><circle cx="32" cy="9" r="7" fill="#fff"/><circle cx="41" cy="11" r="6" fill="#fff"/><rect x="19" y="13" width="26" height="7" rx="1.5" fill="#F1F3F5" stroke="#DEE2E6" stroke-width="1"/>`,
        },
        coffee: {
            name: {it: 'Tazza di caffè', en: 'Coffee mug'},
            svg: `${line('M47 41q-1-3 1-5M52 41q-1-3 1-5', '#fff', 1.6)}<path d="M55 46h3a3 3 0 0 1 0 6h-3" fill="none" stroke="#F8F9FA" stroke-width="2.2"/>
<rect x="43" y="44" width="13" height="14" rx="2.5" fill="#F8F9FA" stroke="${INK}" stroke-width="1.4"/><rect x="45" y="46" width="9" height="3" rx="1" fill="#7C3A16"/>`,
        },
        gradcap: {
            name: {it: 'Tocco di laurea', en: 'Graduation cap'},
            svg: `<path d="M32 5 10 13l22 8 22-8z" fill="${INK}"/><path d="M19 16v5c0 3 26 3 26 0v-5z" fill="#2B303A"/>${line('M48 15v9', '#FFD43B', 2)}<circle cx="48" cy="25" r="2" fill="#FFD43B"/>`,
        },
        headphones: {
            name: {it: 'Cuffie', en: 'Headphones'},
            svg: `<path d="M11 33a21 21 0 0 1 42 0" fill="none" stroke="#2B303A" stroke-width="4" stroke-linecap="round"/>
<rect x="7" y="26" width="7" height="14" rx="3.5" fill="#FF5B2E" stroke="${INK}" stroke-width="1.4"/><rect x="50" y="26" width="7" height="14" rx="3.5" fill="#FF5B2E" stroke="${INK}" stroke-width="1.4"/>`,
        },
        tophat: {
            name: {it: 'Cilindro', en: 'Top hat'},
            svg: `<rect x="20" y="4" width="24" height="15" rx="2" fill="#212529"/><rect x="20" y="15" width="24" height="3" fill="#E03131"/><rect x="13" y="18" width="38" height="3.5" rx="1.7" fill="#212529"/>`,
        },
        halo: {
            name: {it: 'Aureola', en: 'Golden halo'},
            svg: `<ellipse cx="32" cy="9" rx="14" ry="4" fill="none" stroke="#FFD43B" stroke-width="3.2"/><ellipse cx="32" cy="9" rx="14" ry="4" fill="none" stroke="#FFF9DB" stroke-width="1"/>`,
        },
        vikinghelm: {
            name: {it: 'Elmo cornuto', en: 'Horned helmet'},
            svg: `<path d="M16 18c-6-2-8-8-5-13 2 5 5 7 9 8zM48 18c6-2 8-8 5-13-2 5-5 7-9 8z" fill="#FFF5EA"/>
<path d="M15 20a17 17 0 0 1 34 0z" fill="#868E96"/><rect x="14" y="18" width="36" height="4" rx="2" fill="#ADB5BD"/><circle cx="32" cy="20" r="2" fill="#FFD43B"/>`,
        },
        wizardhat: {
            name: {it: 'Cappello magico', en: 'Wizard hat'},
            svg: `<path d="M18 19 33 2l13 17z" fill="#5F3DC4"/><path d="M12 18h40c2 0 2 3 0 3H12c-2 0-2-3 0-3z" fill="#4C2E9E"/>
<path d="M19 16h26v2.5H19z" fill="#FFD43B"/><circle cx="32" cy="11" r="2" fill="#FFD43B"/>`,
        },
        vr: {
            name: {it: 'Visore VR', en: 'VR headset'},
            svg: `<rect x="13" y="25" width="38" height="14" rx="5" fill="#212529" stroke="#ADB5BD" stroke-width="1.6"/><rect x="17" y="29" width="30" height="6" rx="3" fill="#00F5D4"/>${line('M22 32h20', '#fff', 1.6)}`,
        },
        tiara: {
            name: {it: 'Diadema stellare', en: 'Star tiara'},
            svg: `<path d="M16 19q16-7 32 0l-4-9-12 5-12-5z" fill="#E9ECEF" stroke="#ADB5BD" stroke-width="1.2"/><path d="M32 6l2 4 4 1-3 3 1 4-4-2-4 2 1-4-3-3 4-1z" fill="#3BC9DB"/>`,
        },
        crown: {
            name: {it: 'Corona d’oro', en: 'Golden crown'},
            svg: `<path d="M17 19 15 8l9 6 8-8 8 8 9-6-2 11z" fill="#FFD43B" stroke="#D99A0F" stroke-width="1.2"/>
<circle cx="32" cy="14" r="2" fill="#E03131"/><circle cx="23" cy="15" r="1.6" fill="#12B886"/><circle cx="41" cy="15" r="1.6" fill="#12B886"/>`,
        },
    };

    /** Frames: a decorative ring around the edge of the 64×64 circle. */
    const FRAMES = {
        ring: {
            name: {it: 'Anello d’argento', en: 'Silver ring'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#DEE2E6" stroke-width="3.5"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#fff" stroke-width="1" opacity=".7"/>`,
        },
        dashed: {
            name: {it: 'Tratteggio', en: 'Dashed code'},
            svg: `<circle cx="32" cy="32" r="29.5" fill="none" stroke="#fff" stroke-width="3" stroke-dasharray="6 4" stroke-linecap="round"/>`,
        },
        wood: {
            name: {it: 'Legno intagliato', en: 'Carved wood'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#8B5A2B" stroke-width="4.2"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#D99A6C" stroke-width="1.2"/>
<circle cx="10" cy="12" r="3" fill="#51CF66"/><circle cx="54" cy="52" r="3" fill="#51CF66"/>`,
        },
        copper_ring: {
            name: {it: 'Ghirlanda di rame', en: 'Copper rim'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#D97736" stroke-width="4"/>
<circle cx="32" cy="3" r="2" fill="#FFE8CC"/><circle cx="32" cy="61" r="2" fill="#FFE8CC"/><circle cx="3" cy="32" r="2" fill="#FFE8CC"/><circle cx="61" cy="32" r="2" fill="#FFE8CC"/>`,
        },
        pixel_frame: {
            name: {it: 'Arcade 8-bit', en: '8-bit arcade'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#FFD43B" stroke-width="4" stroke-dasharray="4 4"/><circle cx="32" cy="32" r="27" fill="none" stroke="#00F5D4" stroke-width="2" stroke-dasharray="4 4"/>`,
        },
        neon: {
            name: {it: 'Neon cyber', en: 'Cyber neon'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#00F5D4" stroke-width="3.5" stroke-dasharray="24 8"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#FF2A85" stroke-width="2"/>`,
        },
        ice: {
            name: {it: 'Cristallo di ghiaccio', en: 'Ice crystal'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#74C0FC" stroke-width="4"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#E7F5FF" stroke-width="1.5"/>
<path d="M32 0l3 5-3 3-3-3zM32 56l3 3-3 5-3-5zM0 32l5-3 3 3-3 3zM56 32l3-3 5 3-5 3z" fill="#E7F5FF"/>`,
        },
        emerald: {
            name: {it: 'Smeraldo', en: 'Emerald'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#12B886" stroke-width="4"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#96F2D7" stroke-width="1.2"/>
<path d="M32 1l3 3-3 3-3-3zM32 57l3 3-3 3-3-3z" fill="#63E6BE"/>`,
        },
        gear: {
            name: {it: 'Ingranaggio', en: 'Forge gear'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#868E96" stroke-width="5" stroke-dasharray="5 4"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#DEE2E6" stroke-width="2"/>`,
        },
        sakura: {
            name: {it: 'Fiori di ciliegio', en: 'Cherry blossom'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#FFADCB" stroke-width="3.5"/>
<circle cx="12" cy="12" r="3.5" fill="#FFD1DC" stroke="#F06595" stroke-width="1"/><circle cx="52" cy="12" r="3.5" fill="#FFD1DC" stroke="#F06595" stroke-width="1"/><circle cx="52" cy="52" r="3.5" fill="#FFD1DC" stroke="#F06595" stroke-width="1"/><circle cx="12" cy="52" r="3.5" fill="#FFD1DC" stroke="#F06595" stroke-width="1"/>`,
        },
        gold_ring: {
            name: {it: 'Cornice d’oro', en: 'Golden frame'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#FFD43B" stroke-width="4"/><circle cx="32" cy="32" r="27.2" fill="none" stroke="#F59F00" stroke-width="1.4"/>
<path d="M32 1l3 3.5L32 8l-3-3.5z" fill="#FFF9DB"/>`,
        },
        circuit: {
            name: {it: 'Circuito stampato', en: 'Circuit board'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#12B886" stroke-width="3.5"/><circle cx="32" cy="32" r="27" fill="none" stroke="#FFD43B" stroke-width="1.4" stroke-dasharray="12 6"/>
<circle cx="32" cy="3" r="2.5" fill="#FFD43B"/><circle cx="61" cy="32" r="2.5" fill="#FFD43B"/><circle cx="32" cy="61" r="2.5" fill="#FFD43B"/><circle cx="3" cy="32" r="2.5" fill="#FFD43B"/>`,
        },
        lightning_ring: {
            name: {it: 'Tempesta elettrica', en: 'Lightning storm'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#339AF0" stroke-width="3.5"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#FFD43B" stroke-width="2" stroke-dasharray="8 6"/>
<path d="M33 0l-4 6h3l-2 5 6-6h-3z" fill="#FFD43B"/>`,
        },
        laurel: {
            name: {it: 'Corona d’alloro', en: 'Laurel wreath'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#FFD43B" stroke-width="2.5"/>
<path d="M18 57c-9-6-14-16-14-27 0-9 4-17 10-23M46 57c9-6 14-16 14-27 0-9-4-17-10-23" fill="none" stroke="#51CF66" stroke-width="3.5" stroke-dasharray="5 3" stroke-linecap="round"/>
<circle cx="32" cy="60" r="3" fill="#FF5B2E"/>`,
        },
        rainbow_ring: {
            name: {it: 'Arcobaleno', en: 'Rainbow ring'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#FF5B2E" stroke-width="4" stroke-dasharray="31 157"/>
<circle cx="32" cy="32" r="30" fill="none" stroke="#FFD43B" stroke-width="4" stroke-dasharray="31 157" stroke-dashoffset="-31"/>
<circle cx="32" cy="32" r="30" fill="none" stroke="#51CF66" stroke-width="4" stroke-dasharray="31 157" stroke-dashoffset="-62"/>
<circle cx="32" cy="32" r="30" fill="none" stroke="#2F8CF0" stroke-width="4" stroke-dasharray="31 157" stroke-dashoffset="-93"/>
<circle cx="32" cy="32" r="30" fill="none" stroke="#7048E8" stroke-width="4" stroke-dasharray="31 157" stroke-dashoffset="-124"/>
<circle cx="32" cy="32" r="30" fill="none" stroke="#F03E82" stroke-width="4" stroke-dasharray="34 157" stroke-dashoffset="-155"/>`,
        },
        fire_ring: {
            name: {it: 'Cerchio di fuoco', en: 'Ring of fire'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#FF5B2E" stroke-width="4"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#FFD43B" stroke-width="2" stroke-dasharray="10 5"/>
<path d="M28 4l4-4 4 4-2 2h-4z" fill="#FFD43B"/>`,
        },
        galaxy_ring: {
            name: {it: 'Orbita stellare', en: 'Star orbit'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#9D4EDD" stroke-width="4"/><circle cx="32" cy="32" r="27.5" fill="none" stroke="#FFD43B" stroke-width="1.2" stroke-dasharray="4 6"/>
<path d="M32 0l1.5 3.5L37 5l-3.5 1.5L32 10l-1.5-3.5L27 5l3.5-1.5zM54 50l1 2.5 2.5 1-2.5 1-1 2.5-1-2.5-2.5-1 2.5-1zM10 50l1 2.5 2.5 1-2.5 1-1 2.5-1-2.5-2.5-1 2.5-1z" fill="#FFD43B"/>`,
        },
        royal: {
            name: {it: 'Imperiale', en: 'Imperial'},
            svg: `<circle cx="32" cy="32" r="30" fill="none" stroke="#FFD43B" stroke-width="4.2"/><circle cx="32" cy="32" r="27" fill="none" stroke="#FFF3BF" stroke-width="1.2"/>
<circle cx="32" cy="3.5" r="3" fill="#E03131" stroke="#FFD43B" stroke-width="1.2"/><circle cx="3.5" cy="32" r="2.4" fill="#2F8CF0"/><circle cx="60.5" cy="32" r="2.4" fill="#2F8CF0"/><circle cx="32" cy="60.5" r="2.4" fill="#12B886"/>`,
        },
    };

    /** Backgrounds: a colour or a gradient behind the character (or behind the initial). */
    const BACKGROUNDS = {
        violet: {name: {it: 'Viola', en: 'Violet'}, css: '#7048E8'},
        night: {name: {it: 'Notte', en: 'Night'}, css: '#1B1B4B'},
        ember: {name: {it: 'Brace', en: 'Ember'}, css: '#FF5B2E'},
        mint: {name: {it: 'Menta', en: 'Mint'}, css: '#12B886'},
        sky: {name: {it: 'Cielo', en: 'Sky'}, css: '#2F8CF0'},
        ocean: {name: {it: 'Oceano', en: 'Ocean'}, css: '#0B7285'},
        forest: {name: {it: 'Foresta', en: 'Forest'}, css: '#2B8A3E'},
        lavender: {name: {it: 'Lavanda', en: 'Lavender'}, css: '#9775FA'},
        coral: {name: {it: 'Corallo', en: 'Coral'}, css: '#FF6B6B'},
        teal: {name: {it: 'Ottanio', en: 'Teal'}, css: '#0CA678'},
        crimson: {name: {it: 'Cremisi', en: 'Crimson'}, css: '#C92A2A'},
        indigo: {name: {it: 'Indaco', en: 'Indigo'}, css: '#364FC7'},
        amber: {name: {it: 'Ambra', en: 'Amber'}, css: '#F59F00'},
        slate: {name: {it: 'Ardesia', en: 'Slate'}, css: '#495057'},
        gold: {name: {it: 'Oro', en: 'Gold'}, css: '#FFC53D'},
        pink: {name: {it: 'Rosa', en: 'Pink'}, css: '#F03E82'},
        coal: {name: {it: 'Carbone', en: 'Coal'}, css: '#2B2D33'},
        cherry: {name: {it: 'Ciliegia', en: 'Cherry'}, css: 'linear-gradient(135deg, #FF758F, #C9184A)'},
        copper: {name: {it: 'Rame', en: 'Copper'}, css: 'linear-gradient(135deg, #E88D4F, #7C3A16)'},
        arctic: {name: {it: 'Artico', en: 'Arctic'}, css: 'linear-gradient(145deg, #74C0FC, #1098AD 65%, #0B7285)'},
        jungle: {name: {it: 'Giungla', en: 'Jungle'}, css: 'linear-gradient(145deg, #69DB7C, #2B8A3E 55%, #083D18)'},
        aurora: {name: {it: 'Aurora', en: 'Aurora'}, css: 'linear-gradient(135deg, #7048E8, #12B886)'},
        terminal: {name: {it: 'Terminale', en: 'Terminal'}, css: 'linear-gradient(160deg, #06281E, #12B886)'},
        cyber: {name: {it: 'Cyber', en: 'Cyber'}, css: 'linear-gradient(135deg, #00F5D4, #3A86FF 55%, #8338EC)'},
        magma: {name: {it: 'Magma', en: 'Magma'}, css: 'linear-gradient(160deg, #FF922B, #E03131 55%, #2B0909)'},
        synthwave: {name: {it: 'Synthwave', en: 'Synthwave'}, css: 'linear-gradient(155deg, #240046, #9D4EDD 55%, #FF2A85)'},
        sunset: {name: {it: 'Tramonto', en: 'Sunset'}, css: 'linear-gradient(135deg, #FFC53D, #FF5B2E 45%, #F03E82 75%, #7048E8)'},
        nebula: {name: {it: 'Nebulosa', en: 'Nebula'}, css: 'radial-gradient(circle at 30% 30%, #F72585, #7209B7 55%, #10002B)'},
        forge: {name: {it: 'Fucina ardente', en: 'Blazing forge'}, css: 'radial-gradient(circle at 50% 80%, #FFC53D, #FF5B2E 55%, #3B0D11)'},
        matrix: {name: {it: 'Matrice', en: 'Matrix'}, css: 'repeating-linear-gradient(90deg, #041B11 0 6px, #0B4F30 6px 12px)'},
        blueprint: {name: {it: 'Cianografia', en: 'Blueprint'}, css: 'repeating-linear-gradient(0deg, #1864AB 0 7px, #228BE6 7px 8px)'},
        prism: {name: {it: 'Prisma', en: 'Prism'}, css: 'linear-gradient(135deg, #FF5B2E, #FFC53D 25%, #12B886 50%, #2F8CF0 75%, #7048E8)'},
    };

    const name = item => item ? (item.name[I18N.lang] || item.name.en) : '';
    const item = id => CHARACTERS[id] || ACCESSORIES[id] || FRAMES[id] || BACKGROUNDS[id] || null;
    const parse = code => {
        const [c, b, a, f] = String(code || '').split(':');
        return {
            character: CHARACTERS[c] ? c : '',
            background: BACKGROUNDS[b] ? b : '',
            accessory: ACCESSORIES[a] ? a : '',
            frame: FRAMES[f] ? f : '',
        };
    };
    const encode = p => [p.character || '', p.background || '', p.accessory || '', p.frame || ''].join(':').replace(/:+$/, '');

    /** The picture of a person: their character (or initial), accessory and frame on their background. */
    function render(code, label, cls) {
        const {character, background, accessory, frame} = parse(code);
        const drawn = !!(character || accessory || frame);
        const el = UI.h('span.avatar' + (cls ? '.' + cls : '') + (drawn ? '.drawn' : ''), {'aria-hidden': 'true'});
        if (background) el.style.background = BACKGROUNDS[background].css;
        const ch = String(label || '?').trim().charAt(0).toUpperCase().replace(/[<>&"]/g, '');
        if (drawn) {
            const base = character ? CHARACTERS[character].svg
                : ch ? `<text x="32" y="42" text-anchor="middle" fill="#fff" font-family="var(--display, sans-serif)" font-weight="600" font-size="28">${ch}</text>` : '';
            el.innerHTML = `<svg viewBox="0 0 64 64">${base}${accessory ? ACCESSORIES[accessory].svg : ''}${frame ? FRAMES[frame].svg : ''}</svg>`;
        } else {
            el.textContent = ch || '?';
        }
        return el;
    }

    window.Avatars = {CHARACTERS, ACCESSORIES, FRAMES, BACKGROUNDS, render, parse, encode, item, name};
})();
