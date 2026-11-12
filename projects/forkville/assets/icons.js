/*
 * Forkville — every small picture in the interface is an SVG drawn here (never emoji):
 * ingredients, dishes, moods and thoughts, actions, medals and interface symbols.
 *
 *   icon('ing', 'tomato')   icon('dish', 'pizza')   icon('mood', 'happy')   icon('ui', 'gear')
 * Each returns an SVG string (viewBox 32×32) to put in innerHTML.
 */

const C = {
    ink: '#3B2418', red: '#E0453A', dred: '#B5302A', green: '#5E9A3E', dgreen: '#3F7A2E', leaf: '#7CB342', yellow: '#F6C945', orange: '#F08A24',
    brown: '#9C6B3F', dbrown: '#6B4426', cream: '#FFF6E9', white: '#FFFFFF', grey: '#C9CED4', blue: '#3E7CB4', lblue: '#8CC8EC', pink: '#F7A1C4',
    purple: '#8E5BA8', gold: '#E2B23A', skin: '#F6D0A8', bread: '#E6B26B', choc: '#5A3218', beige: '#F1E3B5', plate: '#F4F1EA',
};

let uid = 0;
const svg = (body) => `<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" stroke-linejoin="round" stroke-linecap="round">${body}</svg>`;

/**
 * Food pictures get volume: a soft light from the top left (specular lighting on the shape) and a light shadow
 * underneath. Each picture has its own filter id, so removing one never breaks another.
 */
const shaded = (body) => {
    const id = 'fvl' + (++uid);
    return svg(`<defs><filter id="${id}" x="-15%" y="-15%" width="130%" height="135%">
        <feGaussianBlur in="SourceAlpha" stdDeviation="1.1" result="blur"/>
        <feSpecularLighting in="blur" surfaceScale="2.6" specularConstant="0.75" specularExponent="16" lighting-color="#ffffff" result="spec"><fePointLight x="6" y="2" z="22"/></feSpecularLighting>
        <feComposite in="spec" in2="SourceAlpha" operator="in" result="shine"/>
        <feComposite in="SourceGraphic" in2="shine" operator="arithmetic" k1="0" k2="1" k3="0.55" k4="0" result="lit"/>
        <feDropShadow in="lit" dx="0" dy="1.2" stdDeviation="0.8" flood-color="#3B2418" flood-opacity="0.28"/>
    </filter></defs><g filter="url(#${id})">${body}</g>`);
};
const o = (extra = '') => `stroke="${C.ink}" stroke-width="1.6" ${extra}`;
const plate = () => `<ellipse cx="16" cy="22" rx="13" ry="5.5" fill="${C.plate}" ${o()}/><ellipse cx="16" cy="21.5" rx="9" ry="3.6" fill="${C.white}"/>`;
const face = (fill) => `<circle cx="16" cy="16" r="12.5" fill="${fill}" ${o()}/>`;
const eyes = (y = 13.5) => `<circle cx="12" cy="${y}" r="1.6" fill="${C.ink}"/><circle cx="20" cy="${y}" r="1.6" fill="${C.ink}"/>`;

const ING = {
    tomato: `<circle cx="16" cy="18" r="10" fill="${C.red}" ${o()}/><path d="M16 9l-3 -3M16 9l3 -3M16 9l-5 1M16 9l5 1M16 9v-4" stroke="${C.dgreen}" stroke-width="2.2" fill="none"/><ellipse cx="12" cy="15" rx="2" ry="1.2" fill="#fff" opacity=".5"/>`,
    basil: `<path d="M16 28V12" stroke="${C.dgreen}" stroke-width="2" fill="none"/><path d="M16 14c-8 0-10-6-9-9 5 0 9 3 9 9z" fill="${C.green}" ${o()}/><path d="M16 18c8 0 10-6 9-9-5 0-9 3-9 9z" fill="${C.leaf}" ${o()}/><path d="M16 24c-6 0-8-4-7-7 4 0 7 2 7 7z" fill="${C.green}" ${o()}/>`,
    potato: `<path d="M6 17c0-6 5-9 11-9s10 4 9 9-5 8-11 8-9-3-9-8z" fill="#C9A066" ${o()}/><circle cx="12" cy="15" r="1" fill="${C.dbrown}"/><circle cx="19" cy="13" r="1" fill="${C.dbrown}"/><circle cx="20" cy="20" r="1" fill="${C.dbrown}"/>`,
    onion: `<path d="M16 5c-2 3-10 7-10 14a10 8 0 0 0 20 0c0-7-8-11-10-14z" fill="#C77DBB" ${o()}/><path d="M16 8c-2 4-4 8-4 13M16 8c2 4 4 8 4 13" stroke="#A35B98" stroke-width="1.3" fill="none"/><path d="M14 4h4" stroke="${C.dgreen}" stroke-width="2"/>`,
    garlic: `<path d="M16 6c-1 3-9 6-9 13a9 7 0 0 0 18 0c0-7-8-10-9-13z" fill="#F7F2E6" ${o()}/><path d="M16 9v16M11 13c-1 4 0 9 2 12M21 13c1 4 0 9-2 12" stroke="#D8CDB8" stroke-width="1.2" fill="none"/>`,
    carrot: `<path d="M9 13l14-3-11 19z" fill="${C.orange}" ${o()}/><path d="M13 17h4M12 21h3" stroke="#C96A12" stroke-width="1.3"/><path d="M19 10c0-4 2-6 4-7M21 11c2-3 5-4 7-3M20 10c-1-3-3-5-6-5" stroke="${C.green}" stroke-width="2.2" fill="none"/>`,
    lemon: `<ellipse cx="16" cy="17" rx="11" ry="8" fill="${C.yellow}" ${o()}/><path d="M4 17h2M26 17h2" stroke="${C.ink}" stroke-width="1.6"/><ellipse cx="12" cy="14" rx="3" ry="1.4" fill="#fff" opacity=".55"/><path d="M17 9c1-2 4-3 6-2-1 2-3 3-6 2z" fill="${C.green}"/>`,
    strawberry: `<path d="M16 28c-6-4-10-9-10-14 0-3 3-5 10-5s10 2 10 5c0 5-4 10-10 14z" fill="${C.red}" ${o()}/><path d="M10 9l3 3 3-4 3 4 3-3" fill="${C.green}" ${o('stroke-width="1.2"')}/>${[[12, 15], [17, 14], [20, 18], [14, 20], [16, 24], [10, 18]].map(([x, y]) => `<ellipse cx="${x}" cy="${y}" rx=".7" ry="1" fill="${C.yellow}"/>`).join('')}`,
    pepper: `<path d="M9 13c0-3 3-4 7-4s7 1 7 4c0 7-2 14-7 14s-7-7-7-14z" fill="#E2711D" ${o()}/><path d="M16 9V5c0-1 2-2 3-1" stroke="${C.dgreen}" stroke-width="2.2" fill="none"/><path d="M13 13c-1 4-1 8 0 11" stroke="#fff" stroke-width="1.4" opacity=".5" fill="none"/>`,
    mushroom: `<path d="M4 17c0-7 5-11 12-11s12 4 12 11z" fill="#B5651D" ${o()}/><circle cx="11" cy="12" r="1.6" fill="${C.cream}"/><circle cx="19" cy="10" r="1.3" fill="${C.cream}"/><circle cx="22" cy="14" r="1.2" fill="${C.cream}"/><path d="M12 17h8l1 10h-10z" fill="#F3EEE2" ${o()}/>`,
    flour: `<path d="M8 10c0-2 2-3 8-3s8 1 8 3l2 16c0 2-3 3-10 3s-10-1-10-3z" fill="#F4EFE2" ${o()}/><path d="M9 10c3 1 11 1 14 0" stroke="${C.ink}" stroke-width="1.2" fill="none"/><path d="M14 16l2-3 2 3-2 6z" fill="${C.yellow}" ${o('stroke-width="1"')}/>`,
    cheese: `<path d="M4 22l20-12 4 5v9H4z" fill="${C.yellow}" ${o()}/><path d="M4 22h24" stroke="${C.ink}" stroke-width="1.6"/><circle cx="12" cy="22" r="1.8" fill="#E0A526"/><circle cx="20" cy="18" r="1.6" fill="#E0A526"/><circle cx="23" cy="24" r="1.3" fill="#E0A526"/>`,
    egg: `<path d="M16 4c5 0 9 8 9 14a9 9 0 0 1-18 0c0-6 4-14 9-14z" fill="${C.white}" ${o()}/><ellipse cx="13" cy="12" rx="2" ry="3" fill="${C.cream}"/>`,
    milk: `<path d="M10 11l3-5h6l3 5v16H10z" fill="${C.white}" ${o()}/><path d="M10 15h12v7H10z" fill="${C.lblue}"/><path d="M13 6l3 5 3-5" fill="none" ${o('stroke-width="1.2"')}/>`,
    rice: `<path d="M5 15h22a11 9 0 0 1-22 0z" fill="${C.blue}" ${o()}/><path d="M6 15c0-5 4-7 10-7s10 2 10 7z" fill="${C.white}" ${o()}/>${[[11, 12], [15, 10], [19, 12], [14, 13], [21, 13.5]].map(([x, y]) => `<ellipse cx="${x}" cy="${y}" rx="1.4" ry=".7" fill="${C.grey}"/>`).join('')}`,
    fish: `<path d="M4 16c4-6 12-8 18-4l6-5v18l-6-5c-6 4-14 2-18-4z" fill="${C.lblue}" ${o()}/><circle cx="10" cy="15" r="1.4" fill="${C.ink}"/><path d="M14 13c1 2 1 4 0 6" stroke="${C.blue}" stroke-width="1.4" fill="none"/>`,
    beef: `<path d="M6 12c2-4 10-6 16-3 5 2 6 8 3 12-4 5-14 6-18 2-3-3-3-8-1-11z" fill="#C84B3E" ${o()}/><path d="M10 13c3 2 6 1 8-1M12 19c3-1 6 0 9-2" stroke="#F4C9C0" stroke-width="1.5" fill="none"/><circle cx="21" cy="14" r="2.2" fill="#F4E1D6" ${o('stroke-width="1"')}/>`,
    chicken: `<path d="M18 5c5 0 9 4 9 9 0 4-3 7-7 8l-7 4" fill="none"/><path d="M14 7c5-4 13 0 12 7-1 6-7 8-11 7z" fill="#D98A3D" ${o()}/><path d="M15 20l-6 5" stroke="${C.ink}" stroke-width="5.5"/><path d="M15 20l-6 5" stroke="${C.cream}" stroke-width="3"/><circle cx="7.5" cy="26.5" r="2.3" fill="${C.cream}" ${o('stroke-width="1.2"')}/>`,
    chocolate: `<rect x="7" y="5" width="18" height="22" rx="2" fill="${C.choc}" ${o()}/><path d="M7 12h18M7 19h18M16 5v22" stroke="#3B1F0E" stroke-width="1.4"/><path d="M7 22l8 5H7z" fill="#C0392B"/>`,
    coffee: `<path d="M7 12h15v8a6 6 0 0 1-6 6h-3a6 6 0 0 1-6-6z" fill="${C.white}" ${o()}/><path d="M22 14h2a3 3 0 0 1 0 6h-2" fill="none" ${o()}/><ellipse cx="14.5" cy="12" rx="7.5" ry="1.6" fill="#8B5A2B"/><path d="M12 8c0-2 2-2 2-4M17 8c0-2 2-2 2-4" stroke="${C.grey}" stroke-width="1.5" fill="none"/>`,
    olive: `<path d="M13 4h6v3l2 3v16a2 2 0 0 1-2 2h-6a2 2 0 0 1-2-2V10l2-3z" fill="#C9D86B" ${o()}/><rect x="13" y="2.5" width="6" height="2.5" fill="${C.dgreen}" ${o('stroke-width="1"')}/><ellipse cx="16" cy="19" rx="3" ry="4" fill="${C.dgreen}" ${o('stroke-width="1"')}/>`,
    sugar: `<path d="M6 14l8-4 8 4-8 4z" fill="${C.white}" ${o()}/><path d="M6 14v8l8 4v-8z" fill="#EDEAE3" ${o()}/><path d="M22 14v8l-8 4v-8z" fill="#DCD7CD" ${o()}/><path d="M18 9l5-2 5 2-5 2z" fill="${C.white}" ${o('stroke-width="1.2"')}/>`,
};

const DISH = {
    bruschetta: `${plate()}<path d="M6 19c0-4 4-6 10-6s10 2 10 6c-3 2-17 2-20 0z" fill="${C.bread}" ${o()}/>${[[11, 16], [16, 15], [21, 16], [13.5, 18], [19, 18]].map(([x, y]) => `<circle cx="${x}" cy="${y}" r="1.6" fill="${C.red}"/>`).join('')}<path d="M15 14l2-1" stroke="${C.green}" stroke-width="1.8"/>`,
    fries: `<path d="M9 14h14l-2 14H11z" fill="${C.red}" ${o()}/>${[10, 13, 16, 19, 22].map((x, i) => `<rect x="${x - 1}" y="${4 + (i % 2) * 3}" width="2.6" height="12" rx=".8" fill="${C.yellow}" ${o('stroke-width="1"')}/>`).join('')}<path d="M9 14h14l-2 14H11z" fill="${C.red}" ${o()}/><path d="M13 19c2 2 4 2 6 0" stroke="${C.white}" stroke-width="1.6" fill="none"/>`,
    tomato_soup: `<path d="M4 15h24a12 10 0 0 1-24 0z" fill="${C.white}" ${o()}/><ellipse cx="16" cy="15" rx="12" ry="3" fill="${C.red}" ${o()}/><circle cx="19" cy="14.5" r="1.4" fill="${C.green}"/><path d="M12 10c0-2 2-2 2-4M18 10c0-2 2-2 2-4" stroke="${C.grey}" stroke-width="1.5" fill="none"/>`,
    lemonade: `<path d="M9 7h14l-2 20H11z" fill="#FFF2A6" ${o()}/><path d="M10 12h12" stroke="${C.ink}" stroke-width="1"/><circle cx="21" cy="8" r="4" fill="${C.yellow}" ${o()}/><path d="M18 4l-3 10" stroke="${C.pink}" stroke-width="2"/>`,
    cappuccino: `<ellipse cx="15" cy="25" rx="11" ry="3" fill="${C.white}" ${o()}/><path d="M7 12h16v6a7 7 0 0 1-7 7h-2a7 7 0 0 1-7-7z" fill="${C.white}" ${o()}/><path d="M23 14h2a3 3 0 0 1 0 6h-2" fill="none" ${o()}/><ellipse cx="15" cy="12" rx="8" ry="2" fill="#C69C6D"/><path d="M13 12c1-1 3-1 4 0" stroke="${C.white}" stroke-width="1.4" fill="none"/>`,
    omelette: `${plate()}<path d="M6 20c0-6 6-9 12-8 6 1 8 5 8 8z" fill="${C.yellow}" ${o()}/><circle cx="14" cy="17" r="1" fill="${C.green}"/><circle cx="19" cy="16" r="1" fill="${C.green}"/>`,
    pasta_tomato: `${plate()}<path d="M8 20c0-4 4-7 8-7s8 3 8 7z" fill="${C.beige}" ${o()}/><path d="M10 18c2-2 4 0 6-2s4 0 6-2" stroke="#E2C48A" stroke-width="1.3" fill="none"/><ellipse cx="16" cy="15.5" rx="4" ry="2" fill="${C.red}"/><path d="M15 13l2-1" stroke="${C.green}" stroke-width="1.8"/>`,
    pizza: `<path d="M16 28L5 7c7-3 15-3 22 0z" fill="${C.yellow}" ${o()}/><path d="M5 7c7-3 15-3 22 0l-1.5 3c-6-2.5-13-2.5-19 0z" fill="${C.bread}" ${o()}/><circle cx="14" cy="13" r="2" fill="${C.red}"/><circle cx="19" cy="15" r="2" fill="${C.red}"/><circle cx="16" cy="20" r="1.8" fill="${C.red}"/><path d="M12 17l2-1" stroke="${C.green}" stroke-width="1.6"/>`,
    carrot_cake: `<path d="M5 14l18-6 4 6v12H5z" fill="#D98A3D" ${o()}/><path d="M5 14l18-6 4 6z" fill="${C.cream}" ${o()}/><path d="M5 20h22" stroke="${C.cream}" stroke-width="2.4"/><path d="M19 9l3-1 1 3" fill="${C.orange}"/>`,
    burger: `<path d="M5 15c0-6 5-9 11-9s11 3 11 9z" fill="${C.bread}" ${o()}/><path d="M4 17h24" stroke="${C.green}" stroke-width="3"/><rect x="5" y="18" width="22" height="4" rx="2" fill="${C.dbrown}" ${o()}/><path d="M5 23h22v1a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3z" fill="${C.bread}" ${o()}/><circle cx="12" cy="10" r=".8" fill="${C.cream}"/><circle cx="17" cy="9" r=".8" fill="${C.cream}"/><circle cx="21" cy="11" r=".8" fill="${C.cream}"/>`,
    risotto: `${plate()}<path d="M8 20c0-4 4-6 8-6s8 2 8 6z" fill="${C.beige}" ${o()}/>${[[12, 17], [17, 16], [20, 18]].map(([x, y]) => `<path d="M${x - 2} ${y}a2 1.6 0 0 1 4 0z" fill="${C.brown}"/>`).join('')}`,
    strawberry_tart: `<path d="M4 18h24l-3 7H7z" fill="${C.bread}" ${o()}/><path d="M5 18c0-3 5-5 11-5s11 2 11 5z" fill="${C.cream}" ${o()}/>${[9, 14, 19, 24].map(x => `<path d="M${x} 16c-2-1-2-4 0-5 2 1 2 4 0 5z" fill="${C.red}" ${o('stroke-width="1"')}/>`).join('')}`,
    grilled_fish: `${plate()}<path d="M6 19c3-4 9-5 14-3l5-3v10l-5-3c-5 2-11 1-14-1z" fill="${C.grey}" ${o()}/><path d="M11 17l2 3M15 16l2 4" stroke="${C.dbrown}" stroke-width="1.4"/><circle cx="9" cy="18" r=".9" fill="${C.ink}"/><path d="M22 12a3 3 0 0 1 4 3z" fill="${C.yellow}" ${o('stroke-width="1"')}/>`,
    stuffed_peppers: `${plate()}<path d="M8 21c-1-5 1-9 4-9s4 4 3 9z" fill="${C.red}" ${o()}/><path d="M17 21c-1-5 1-9 4-9s4 4 3 9z" fill="#E2711D" ${o()}/><ellipse cx="11.5" cy="12" rx="3" ry="1.3" fill="${C.beige}"/><ellipse cx="20.5" cy="12" rx="3" ry="1.3" fill="${C.beige}"/><path d="M11.5 11V8M20.5 11V8" stroke="${C.dgreen}" stroke-width="1.8"/>`,
    roast_chicken: `${plate()}<path d="M7 19c0-6 5-9 9-9s9 3 9 9z" fill="#C9792F" ${o()}/><path d="M11 13c1-3 4-4 6-3" stroke="#E8A65A" stroke-width="1.6" fill="none"/><path d="M23 12l3-3" stroke="${C.ink}" stroke-width="4"/><path d="M23 12l3-3" stroke="${C.cream}" stroke-width="2"/>`,
    chocolate_cake: `<path d="M5 14l18-6 4 6v12H5z" fill="${C.choc}" ${o()}/><path d="M5 14l18-6 4 6z" fill="#3B1F0E" ${o()}/><path d="M5 20h22" stroke="#8B5A2B" stroke-width="2"/><circle cx="19" cy="8" r="2.2" fill="${C.red}" ${o('stroke-width="1"')}/>`,
};

const MOOD = {
    happy: `${face(C.yellow)}${eyes()}<path d="M10.5 18.5c3 4 8 4 11 0" stroke="${C.ink}" stroke-width="1.8" fill="none"/>`,
    love: `${face(C.yellow)}<path d="M12 15l-3-3a2 2 0 0 1 3-2 2 2 0 0 1 3 2z M20 15l-3-3a2 2 0 0 1 3-2 2 2 0 0 1 3 2z" fill="${C.red}"/><path d="M11 19c3 3 7 3 10 0" stroke="${C.ink}" stroke-width="1.8" fill="none"/>`,
    sick: `${face('#B7D77A')}${eyes()}<path d="M11 21c2-2 4 0 5-1 2-2 4 0 5 0" stroke="${C.ink}" stroke-width="1.6" fill="none"/>`,
    angry: `${face('#F08060')}<path d="M9 11l5 2M23 11l-5 2" stroke="${C.ink}" stroke-width="1.8"/>${eyes(15)}<path d="M11 22c3-3 7-3 10 0" stroke="${C.ink}" stroke-width="1.8" fill="none"/>`,
    tired: `${face(C.yellow)}<path d="M10 14h4M18 14h4" stroke="${C.ink}" stroke-width="1.8"/><ellipse cx="16" cy="20.5" rx="2" ry="2.4" fill="${C.ink}"/><text x="22" y="9" font-size="8" font-weight="900" fill="${C.blue}" font-family="sans-serif">z</text>`,
    sleep: `<text x="4" y="24" font-size="16" font-weight="900" fill="${C.blue}" font-family="sans-serif">Z</text><text x="16" y="16" font-size="11" font-weight="900" fill="${C.lblue}" font-family="sans-serif">z</text><text x="24" y="9" font-size="8" font-weight="900" fill="${C.lblue}" font-family="sans-serif">z</text>`,
    yummy: `${face(C.yellow)}${eyes()}<path d="M10.5 18.5c3 4 8 4 11 0z" fill="${C.ink}"/><path d="M17 20c2 0 3 2 2 3-2 0-3-1-2-3z" fill="${C.red}"/>`,
    laugh: `${face(C.yellow)}<path d="M10 13l3 1-3 1M22 13l-3 1 3 1" stroke="${C.ink}" stroke-width="1.6" fill="none"/><path d="M9.5 18h13a6.5 6 0 0 1-13 0z" fill="${C.ink}"/><path d="M12 21h8" stroke="${C.red}" stroke-width="2"/>`,
    meh: `${face(C.yellow)}<path d="M10 13h4M18 13h4" stroke="${C.ink}" stroke-width="1.8"/><path d="M12 21h8" stroke="${C.ink}" stroke-width="1.8"/>`,
    music: `<path d="M12 23V8l14-3v15" fill="none" stroke="${C.purple}" stroke-width="2.6"/><ellipse cx="9" cy="23" rx="4" ry="3" fill="${C.purple}"/><ellipse cx="23" cy="20" rx="4" ry="3" fill="${C.purple}"/>`,
    tv: `<rect x="4" y="8" width="24" height="16" rx="3" fill="${C.ink}"/><rect x="6.5" y="10.5" width="19" height="11" rx="1.5" fill="${C.lblue}"/><path d="M12 4l4 4 4-4M10 27h12" stroke="${C.ink}" stroke-width="2" fill="none"/>`,
    fire: `<path d="M16 3c2 6 9 8 9 16a9 9 0 0 1-18 0c0-5 3-7 4-10 1 3 2 4 3 4 0-4 0-7 2-10z" fill="${C.orange}" ${o()}/><path d="M16 15c1 3 4 4 4 7a4 4 0 0 1-8 0c0-2 2-3 2-5 1 1 1 2 2 2z" fill="${C.yellow}"/>`,
    strong: `<path d="M18 4l-9 14h7l-2 10 9-14h-7z" fill="${C.yellow}" ${o()}/>`,
    star: `<path d="M16 3l3.9 8 8.7 1.2-6.3 6.1 1.5 8.7L16 22.9 8.2 27l1.5-8.7-6.3-6.1 8.7-1.2z" fill="${C.gold}" ${o()}/>`,
    greet: `<path d="M11 27c-4-2-6-6-6-10V9a2 2 0 0 1 4 0v6V6a2 2 0 0 1 4 0v8V5a2 2 0 0 1 4 0v9V7a2 2 0 0 1 4 0v12c0 5-4 9-9 9-0.4 0-0.7 0-1-.1z" fill="${C.skin}" ${o()}/><path d="M24 4c2 1 3 3 3 5M27 2c2 2 3 4 3 7" stroke="${C.orange}" stroke-width="1.8" fill="none"/>`,
    candy: `<path d="M3 12l5 4-5 4zM29 12l-5 4 5 4z" fill="${C.pink}" ${o()}/><circle cx="16" cy="16" r="8" fill="${C.pink}" ${o()}/><path d="M11 12c3 1 6 5 10 8M10 17c3 0 6 2 8 6" stroke="${C.white}" stroke-width="1.6" fill="none"/>`,
    coffee: ING.coffee,
    clean: `<path d="M6 14l14-8 6 10-14 8z" fill="${C.yellow}" ${o()}/><path d="M6 14l6 10 2-1-6-10z" fill="${C.green}" ${o()}/>${[[24, 6], [28, 11], [21, 3]].map(([x, y]) => `<circle cx="${x}" cy="${y}" r="1.6" fill="${C.lblue}" ${o('stroke-width="1"')}/>`).join('')}`,
    water: `<path d="M16 3c4 6 9 11 9 16a9 9 0 0 1-18 0c0-5 5-10 9-16z" fill="${C.lblue}" ${o()}/><path d="M11 19a5 5 0 0 0 4 5" stroke="${C.white}" stroke-width="1.8" fill="none"/>`,
    sparkle: `<path d="M16 3l3 10 10 3-10 3-3 10-3-10-10-3 10-3z" fill="${C.yellow}" ${o()}/><path d="M26 4l1 3 3 1-3 1-1 3-1-3-3-1 3-1z" fill="${C.white}" ${o('stroke-width="1"')}/>`,
    alert: `<circle cx="16" cy="16" r="12.5" fill="${C.gold}" ${o()}/><path d="M16 8v10" stroke="${C.ink}" stroke-width="3.4"/><circle cx="16" cy="23" r="2" fill="${C.ink}"/>`,
    note: `<path d="M13 23V7l10 3" fill="none" stroke="${C.purple}" stroke-width="2.6"/><ellipse cx="10" cy="23" rx="4" ry="3" fill="${C.purple}"/>`,
    heart: `<path d="M16 27C6 20 3 15 3 10.5A6 6 0 0 1 16 8a6 6 0 0 1 13 2.5C29 15 26 20 16 27z" fill="${C.red}" ${o()}/>`,
    plate: `${plate()}<path d="M5 8v7M8 8v7M6.5 15v10M24 7c3 0 3 8 0 8v10" stroke="${C.grey}" stroke-width="1.8" fill="none"/>`,
};

const UI = {
    register: `<rect x="5" y="16" width="22" height="11" rx="2" fill="${C.brown}" ${o()}/><path d="M9 16l2-7h10l2 7z" fill="#3B3B40" ${o()}/><rect x="12" y="10.5" width="8" height="3" rx=".5" fill="#9FE0B0"/><rect x="9" y="19" width="14" height="3" rx="1" fill="${C.gold}" ${o('stroke-width="1"')}/><circle cx="25" cy="8" r="3.4" fill="${C.gold}" ${o()}/>`,
    car: `<path d="M4 20v-6l3-6h18l3 6v6z" fill="${C.berry || '#B43E5C'}" ${o()}/><path d="M9 9h14l2 5H7z" fill="#BFE3F0" ${o()}/><circle cx="10" cy="21" r="3" fill="${C.ink}"/><circle cx="22" cy="21" r="3" fill="${C.ink}"/><rect x="5" y="15" width="3" height="2" fill="#FFE7A8"/><rect x="24" y="15" width="3" height="2" fill="#FFE7A8"/>`,
    gear: `<path d="M16 4l2.4.4.8 3 2.6 1.1 2.7-1.6 1.7 1.7-1.6 2.7 1.1 2.6 3 .8L29 16l-.4 2.4-3 .8-1.1 2.6 1.6 2.7-1.7 1.7-2.7-1.6-2.6 1.1-.8 3L16 28l-2.4-.4-.8-3-2.6-1.1-2.7 1.6-1.7-1.7 1.6-2.7-1.1-2.6-3-.8L3 16l.4-2.4 3-.8 1.1-2.6-1.6-2.7 1.7-1.7 2.7 1.6 2.6-1.1.8-3z" fill="${C.grey}" ${o()}/><circle cx="16" cy="16" r="4.5" fill="${C.cream}" ${o()}/>`,
    warning: `<path d="M16 3l14 25H2z" fill="${C.gold}" ${o()}/><path d="M16 11v9" stroke="${C.ink}" stroke-width="3"/><circle cx="16" cy="24" r="1.8" fill="${C.ink}"/>`,
    close: `<path d="M8 8l16 16M24 8L8 24" stroke="${C.ink}" stroke-width="3.2"/>`,
    medal1: `<path d="M10 2h5l3 9h-5zM22 2h-5l-3 9h5z" fill="${C.blue}" ${o('stroke-width="1.2"')}/><circle cx="16" cy="20" r="9" fill="${C.gold}" ${o()}/><text x="16" y="24.5" text-anchor="middle" font-size="12" font-weight="900" fill="${C.ink}" font-family="sans-serif">1</text>`,
    medal2: `<path d="M10 2h5l3 9h-5zM22 2h-5l-3 9h5z" fill="${C.red}" ${o('stroke-width="1.2"')}/><circle cx="16" cy="20" r="9" fill="#C9CED4" ${o()}/><text x="16" y="24.5" text-anchor="middle" font-size="12" font-weight="900" fill="${C.ink}" font-family="sans-serif">2</text>`,
    medal3: `<path d="M10 2h5l3 9h-5zM22 2h-5l-3 9h5z" fill="${C.green}" ${o('stroke-width="1.2"')}/><circle cx="16" cy="20" r="9" fill="#D08A4C" ${o()}/><text x="16" y="24.5" text-anchor="middle" font-size="12" font-weight="900" fill="${C.ink}" font-family="sans-serif">3</text>`,
    chef: `<path d="M9 17c-4 0-6-3-5-6s5-4 7-2c1-4 9-4 10 0 2-2 6-1 7 2s-1 6-5 6z" fill="${C.white}" ${o()}/><rect x="9" y="16" width="14" height="6" fill="${C.white}" ${o()}/><circle cx="16" cy="26" r="0" /><path d="M11 25h10" stroke="${C.ink}" stroke-width="1.6"/>`,
    coin: `<circle cx="16" cy="16" r="12" fill="${C.gold}" ${o()}/><circle cx="16" cy="16" r="8" fill="none" stroke="#B98A1E" stroke-width="1.6"/><text x="16" y="20.5" text-anchor="middle" font-size="11" font-weight="900" fill="#8C6612" font-family="sans-serif">F</text>`,
    sun: `<circle cx="16" cy="16" r="6.5" fill="${C.yellow}" ${o()}/>${Array.from({length: 8}, (_, i) => {
        const a = i * Math.PI / 4;
        return `<path d="M${16 + Math.cos(a) * 9.5} ${16 + Math.sin(a) * 9.5}L${16 + Math.cos(a) * 13} ${16 + Math.sin(a) * 13}" stroke="${C.orange}" stroke-width="2.2"/>`;
    }).join('')}`,
    moon: `<path d="M20 4a12 12 0 1 0 8 18A10 10 0 0 1 20 4z" fill="#F4E3A1" ${o()}/><circle cx="12" cy="14" r="1.5" fill="#D9C27A"/><circle cx="16" cy="21" r="1.2" fill="#D9C27A"/>`,
    w_clear: `<circle cx="16" cy="16" r="6.5" fill="${C.yellow}" ${o()}/>${Array.from({length: 8}, (_, i) => {
        const a = i * Math.PI / 4;
        return `<path d="M${16 + Math.cos(a) * 9.5} ${16 + Math.sin(a) * 9.5}L${16 + Math.cos(a) * 13} ${16 + Math.sin(a) * 13}" stroke="${C.orange}" stroke-width="2.2"/>`;
    }).join('')}`,
    w_cloudy: `<circle cx="21" cy="11" r="5" fill="${C.yellow}" ${o()}/><path d="M8 25a5 5 0 0 1 0-10 7 7 0 0 1 13-2 5 5 0 0 1 3 12z" fill="${C.white}" ${o()}/>`,
    w_rain: `<path d="M8 19a5 5 0 0 1 0-10 7 7 0 0 1 13-2 5 5 0 0 1 3 12z" fill="#7D8792" ${o()}/><path d="M8 22l-2 6M13 23l-2 6M24 22l-2 6" stroke="${C.blue}" stroke-width="2.4"/><path d="M19 18l-3 6h3l-2 6 6-8h-3l2-4z" fill="${C.yellow}" ${o('stroke-width="1.1"')}/>`,
    w_snow: `<path d="M8 18a5 5 0 0 1 0-10 7 7 0 0 1 13-2 5 5 0 0 1 3 12z" fill="${C.white}" ${o()}/>${[[10, 24], [16, 27], [22, 24]].map(([x, y]) => `<path d="M${x - 2.5} ${y}h5M${x} ${y - 2.5}v5M${x - 1.8} ${y - 1.8}l3.6 3.6M${x + 1.8} ${y - 1.8}l-3.6 3.6" stroke="${C.lblue}" stroke-width="1.5"/>`).join('')}`,
    w_sunny: `<circle cx="16" cy="16" r="7.5" fill="#FFC93C" ${o()}/>${Array.from({length: 12}, (_, i) => {
        const a = i * Math.PI / 6, r1 = i % 2 ? 10 : 10.5, r2 = i % 2 ? 13 : 15;
        return `<path d="M${16 + Math.cos(a) * r1} ${16 + Math.sin(a) * r1}L${16 + Math.cos(a) * r2} ${16 + Math.sin(a) * r2}" stroke="${C.orange}" stroke-width="2.4"/>`;
    }).join('')}<circle cx="13.5" cy="14" r="1" fill="${C.ink}"/><circle cx="18.5" cy="14" r="1" fill="${C.ink}"/><path d="M13 18c1.5 1.5 4.5 1.5 6 0" stroke="${C.ink}" stroke-width="1.4" fill="none"/>`,
    w_drizzle: `<circle cx="22" cy="10" r="4.5" fill="${C.yellow}" ${o()}/><path d="M8 19a5 5 0 0 1 0-10 7 7 0 0 1 13-2 5 5 0 0 1 3 12z" fill="${C.white}" ${o()}/><path d="M11 23l-1 3M18 24l-1 3" stroke="${C.blue}" stroke-width="2"/>`,
    w_hail: `<path d="M8 18a5 5 0 0 1 0-10 7 7 0 0 1 13-2 5 5 0 0 1 3 12z" fill="#9BA6B0" ${o()}/>${[[10, 23], [16, 26], [22, 23], [13, 28], [19, 29]].map(([x, y]) => `<circle cx="${x}" cy="${y}" r="1.8" fill="#E8F1F6" ${o('stroke-width="1"')}/>`).join('')}`,
    w_light_snow: `<path d="M8 19a5 5 0 0 1 0-10 7 7 0 0 1 13-2 5 5 0 0 1 3 12z" fill="${C.white}" ${o()}/>${[[12, 25], [20, 26]].map(([x, y]) => `<path d="M${x - 2.5} ${y}h5M${x} ${y - 2.5}v5" stroke="${C.lblue}" stroke-width="1.5"/>`).join('')}`,
    s_spring: `<path d="M16 28V16" stroke="${C.dgreen}" stroke-width="2"/>${[0, 72, 144, 216, 288].map(a => `<ellipse cx="${16 + Math.cos(a * Math.PI / 180) * 6}" cy="${12 + Math.sin(a * Math.PI / 180) * 6}" rx="4.5" ry="4.5" fill="${C.pink}" ${o('stroke-width="1.2"')}/>`).join('')}<circle cx="16" cy="12" r="3" fill="${C.yellow}" ${o('stroke-width="1"')}/>`,
    s_summer: `<circle cx="16" cy="16" r="7" fill="${C.yellow}" ${o()}/>${Array.from({length: 8}, (_, i) => {
        const a = i * Math.PI / 4;
        return `<path d="M${16 + Math.cos(a) * 10} ${16 + Math.sin(a) * 10}L${16 + Math.cos(a) * 14} ${16 + Math.sin(a) * 14}" stroke="${C.orange}" stroke-width="2.4"/>`;
    }).join('')}`,
    s_autumn: `<path d="M16 3c3 4 9 4 11 3-1 4 0 8 2 10-4 1-6 4-6 7-3-2-6-2-7 1-1-3-4-3-7-1 0-3-2-6-6-7 2-2 3-6 2-10 2 1 8 1 11-3z" fill="${C.orange}" ${o()}/><path d="M16 9v20" stroke="${C.dbrown}" stroke-width="1.8"/>`,
    s_winter: `${[0, 60, 120].map(a => `<path d="M${16 + Math.cos(a * Math.PI / 180) * 12} ${16 + Math.sin(a * Math.PI / 180) * 12}L${16 - Math.cos(a * Math.PI / 180) * 12} ${16 - Math.sin(a * Math.PI / 180) * 12}" stroke="${C.blue}" stroke-width="2.6"/>`).join('')}<circle cx="16" cy="16" r="3" fill="${C.white}" ${o()}/>`,
    manager: `<circle cx="16" cy="11" r="6" fill="${C.skin}" ${o()}/><path d="M5 29c0-7 5-11 11-11s11 4 11 11z" fill="${C.ink}" ${o()}/><path d="M16 18l-2.5 4 2.5 7 2.5-7z" fill="${C.red}"/><path d="M10 7c2-3 10-3 12 0" stroke="${C.dbrown}" stroke-width="3" fill="none"/>`,
    pause: `<rect x="8" y="6" width="5.5" height="20" rx="1.5" fill="currentColor"/><rect x="18.5" y="6" width="5.5" height="20" rx="1.5" fill="currentColor"/>`,
    play1: `<path d="M10 6l15 10-15 10z" fill="currentColor"/>`,
    play2: `<path d="M5 7l11 9-11 9zM16 7l11 9-11 9z" fill="currentColor"/>`,
    play3: `<path d="M2 8l9 8-9 8zM11.5 8l9 8-9 8zM21 8l9 8-9 8z" fill="currentColor"/>`,
    broom: `<path d="M22 3l-8 13" stroke="${C.dbrown}" stroke-width="2.6"/><path d="M12 14l6 4-3 10c-4 0-9-3-10-6z" fill="${C.yellow}" ${o()}/><path d="M9 22l3-5M12 24l2-5" stroke="${C.dbrown}" stroke-width="1.2"/>`,
    waiter: `<ellipse cx="16" cy="20" rx="12" ry="3" fill="${C.grey}" ${o()}/><path d="M6 19a10 9 0 0 1 20 0z" fill="${C.grey}" ${o()}/><circle cx="16" cy="9" r="2" fill="${C.ink}"/>`,
};

// New ingredients
Object.assign(ING, {
    lettuce: `<path d="M16 27c-7 0-11-4-11-9 0-3 2-5 4-6-1-3 2-6 5-5 1-2 4-2 5 0 3-1 6 2 5 5 2 1 4 3 4 6 0 5-4 9-12 9z" fill="${C.leaf}" ${o()}/><path d="M16 26c-4 0-7-3-7-7 0-3 3-6 7-6s7 3 7 6c0 4-3 7-7 7z" fill="#C5E384" ${o('stroke-width="1"')}/><path d="M16 14v11M12 18l4 3 4-3" stroke="${C.green}" stroke-width="1.1" fill="none"/>`,
    corn: `<path d="M16 4c4 0 6 6 6 12s-2 11-6 11-6-5-6-11 2-12 6-12z" fill="${C.yellow}" ${o()}/>${[0, 1, 2, 3, 4, 5].map(i => `<path d="M11 ${8 + i * 3}h10" stroke="#D9A21D" stroke-width="1"/>`).join('')}<path d="M16 5v21" stroke="#D9A21D" stroke-width="1"/><path d="M10 14c-4 4-4 10 2 14 0-6 0-10-2-14zM22 14c4 4 4 10-2 14 0-6 0-10 2-14z" fill="${C.leaf}" ${o()}/>`,
    eggplant: `<path d="M19 9c5 2 7 9 3 14s-11 5-13 1 0-8 3-11c2-3 4-5 7-4z" fill="#6B2F80" ${o()}/><path d="M17 9c0-3 2-5 4-6M15 8c2-2 6-2 8 1-2 2-6 2-8-1z" fill="${C.green}" ${o('stroke-width="1.2"')}/><ellipse cx="13" cy="17" rx="2" ry="3.5" fill="#fff" opacity=".25"/>`,
    pumpkin: `<path d="M16 9c-7 0-12 4-12 9s5 9 12 9 12-4 12-9-5-9-12-9z" fill="${C.orange}" ${o()}/><path d="M16 9c-3 0-5 4-5 9s2 9 5 9M16 9c3 0 5 4 5 9s-2 9-5 9" stroke="#C96A12" stroke-width="1.3" fill="none"/><path d="M16 9c0-3 1-5 3-6" stroke="${C.dbrown}" stroke-width="2.2" fill="none"/>`,
    shrimp: `<path d="M24 9c-6-3-15 0-16 7-1 6 5 10 10 8 3-1 3-4 1-5-3-1-5 0-6-2s2-5 6-5c3 0 5 1 5 3z" fill="#F58A6E" ${o()}/>${[0, 1, 2].map(i => `<path d="M${13 + i * 3} ${11 - i * 0.5}c-1 2-1 4 0 6" stroke="#D9654A" stroke-width="1" fill="none"/>`).join('')}<path d="M24 9l4-4M24 10l5-1" stroke="${C.dred}" stroke-width="1.2"/><circle cx="21" cy="10.5" r="1" fill="${C.ink}"/>`,
    pork: `<path d="M6 13c0-4 5-6 10-6s10 2 10 6v6c0 4-5 6-10 6S6 23 6 19z" fill="#F4A9A0" ${o()}/><ellipse cx="16" cy="13" rx="10" ry="5" fill="#F8C5BE" ${o()}/><path d="M9 13c2-2 5-3 7-3s5 1 7 3" stroke="#FFFFFF" stroke-width="2" fill="none"/>`,
    cream: `<path d="M9 10h14l-1 16a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2z" fill="${C.white}" ${o()}/><path d="M10 15h12" stroke="${C.lblue}" stroke-width="2.4"/><path d="M11 10c0-3 2-5 5-6 3 1 5 3 5 6z" fill="${C.lblue}" ${o()}/><path d="M13 20h6" stroke="${C.grey}" stroke-width="1.4"/>`,
    butter: `<path d="M5 16l12-5 10 4-12 5z" fill="#FFF0A6" ${o()}/><path d="M5 16v5l10 4v-5z" fill="#F6DD7A" ${o()}/><path d="M15 20v5l12-5v-5z" fill="#EBCB5C" ${o()}/><path d="M8 13l5-2" stroke="#fff" stroke-width="1.4" opacity=".7"/>`,
    honey: `<path d="M9 10h14v14a4 4 0 0 1-4 4h-6a4 4 0 0 1-4-4z" fill="#F2A93B" ${o()}/><rect x="8" y="6" width="16" height="5" rx="1.5" fill="${C.brown}" ${o()}/><path d="M9 14c3 2 5-1 7 1s4-1 7 1" stroke="#D88A1C" stroke-width="1.6" fill="none"/><path d="M12 18h8" stroke="#FFFFFF" stroke-width="1.2" opacity=".6"/>`,
    banana: `<path d="M5 12c3 8 13 13 22 8 1-1 0-3-1-2-7 3-14 0-17-7-1-2-4-1-4 1z" fill="${C.yellow}" ${o()}/><path d="M7 11c4 6 10 9 17 8" stroke="#D9A21D" stroke-width="1.1" fill="none"/><path d="M5 12l-1-3 2-1" stroke="${C.dbrown}" stroke-width="1.8" fill="none"/>`,
    beans: `${[[11, 13], [19, 12], [14, 20], [22, 19], [8, 21]].map(([x, y], i) => `<ellipse cx="${x}" cy="${y}" rx="4.4" ry="3" transform="rotate(${i * 35} ${x} ${y})" fill="${i % 2 ? '#9C3A2A' : '#B5462F'}" ${o('stroke-width="1.2"')}/>`).join('')}`,
    tea: `<path d="M7 12h15v8a6 6 0 0 1-6 6h-3a6 6 0 0 1-6-6z" fill="${C.white}" ${o()}/><ellipse cx="14.5" cy="12" rx="7.5" ry="1.6" fill="#C98A3D"/><path d="M22 14h2a3 3 0 0 1 0 6h-2" fill="none" ${o()}/><path d="M18 12l4-8" stroke="${C.ink}" stroke-width="1"/><rect x="20.5" y="2" width="4" height="4" fill="${C.green}" ${o('stroke-width="1"')}/>`,
});
// New dishes
Object.assign(DISH, {
    caprese: `${plate()}${[9, 14, 19, 24].map((x, i) => `<circle cx="${x}" cy="${20 - (i % 2)}" r="3.4" fill="${i % 2 ? C.white : C.red}" ${o('stroke-width="1.2"')}/>`).join('')}<path d="M12 17l2-2M21 17l2-2" stroke="${C.green}" stroke-width="2.4"/>`,
    garden_salad: `<path d="M4 15h24a12 10 0 0 1-24 0z" fill="#F2EEE6" ${o()}/>${[[9, 14, C.leaf], [14, 12, C.green], [19, 13, C.leaf], [24, 14, C.green]].map(([x, y, c]) => `<ellipse cx="${x}" cy="${y}" rx="4" ry="2.8" fill="${c}" ${o('stroke-width="1"')}/>`).join('')}<circle cx="13" cy="15" r="2" fill="${C.red}"/><circle cx="20" cy="15" r="1.8" fill="${C.red}"/><circle cx="17" cy="13" r="1.2" fill="#3B3F2A"/>`,
    iced_tea: `<path d="M9 6h14l-2 21H11z" fill="#E6A65A" ${o()}/><rect x="12" y="10" width="4" height="4" fill="#F4FBFF" opacity=".8"/><rect x="16" y="15" width="4" height="4" fill="#F4FBFF" opacity=".8"/><circle cx="22" cy="7" r="3.6" fill="${C.yellow}" ${o()}/><path d="M17 3l-1 12" stroke="${C.green}" stroke-width="2"/>`,
    pancakes: `${plate()}${[19, 16, 13].map(y => `<ellipse cx="16" cy="${y}" rx="10" ry="3.2" fill="${C.bread}" ${o()}/>`).join('')}<path d="M10 12c2 3 3 6 2 9M20 12c1 2 1 4 0 6" stroke="#E08A1C" stroke-width="2.2" fill="none"/><rect x="13.5" y="9" width="5" height="3" rx="1" fill="#FFF0A6" ${o('stroke-width="1"')}/>`,
    milkshake: `<path d="M10 11h12l-2 17h-8z" fill="${C.pink}" ${o()}/><path d="M9 11c0-4 3-6 7-6s7 2 7 6z" fill="${C.white}" ${o()}/><circle cx="16" cy="4.5" r="2.2" fill="${C.red}" ${o('stroke-width="1"')}/><path d="M19 8l4-7" stroke="${C.blue}" stroke-width="2"/><path d="M11 18h10" stroke="#fff" stroke-width="1.2" opacity=".6"/>`,
    corn_soup: `<path d="M4 15h24a12 10 0 0 1-24 0z" fill="${C.white}" ${o()}/><ellipse cx="16" cy="15" rx="12" ry="3" fill="${C.yellow}" ${o()}/>${[[11, 14.5], [15, 15.5], [19, 14.3], [22, 15.4]].map(([x, y]) => `<circle cx="${x}" cy="${y}" r="1.1" fill="#E9A21D"/>`).join('')}<path d="M12 10c0-2 2-2 2-4M18 10c0-2 2-2 2-4" stroke="${C.grey}" stroke-width="1.5" fill="none"/>`,
    banana_split: `<path d="M3 19c4 5 22 5 26 0z" fill="#DDF1F7" ${o()}/><path d="M4 18c4 4 20 4 24 0-1-2-23-2-24 0z" fill="${C.yellow}" ${o()}/>${[[10, '#F7A1C4'], [16, '#FFF3C4'], [22, '#7B4E2C']].map(([x, c]) => `<circle cx="${x}" cy="13" r="4" fill="${c}" ${o()}/>`).join('')}<circle cx="16" cy="8" r="1.8" fill="${C.red}"/><path d="M8 11c2-1 3 1 5 0s3 1 5 0 3 1 5 0" stroke="${C.choc}" stroke-width="1.4" fill="none"/>`,
    tacos: `${plate()}${[10, 21].map(x => `<path d="M${x - 7} 20a7 7 0 0 1 14 0z" fill="#F2C76B" ${o()}/><path d="M${x - 5} 18c2-2 8-2 10 0" stroke="${C.leaf}" stroke-width="2.4" fill="none"/><circle cx="${x - 2}" cy="17" r="1.2" fill="${C.red}"/><circle cx="${x + 2}" cy="17.5" r="1.2" fill="#9C3A2A"/>`).join('')}`,
    pumpkin_soup: `<path d="M4 15h24a12 10 0 0 1-24 0z" fill="#3E7CB4" ${o()}/><ellipse cx="16" cy="15" rx="12" ry="3" fill="${C.orange}" ${o()}/><path d="M12 15c2-1.5 6-1.5 8 0" stroke="${C.white}" stroke-width="1.6" fill="none"/>${[[14, 14.2], [18, 14.4]].map(([x, y]) => `<circle cx="${x}" cy="${y}" r=".8" fill="${C.dgreen}"/>`).join('')}<path d="M12 10c0-2 2-2 2-4M18 10c0-2 2-2 2-4" stroke="${C.grey}" stroke-width="1.5" fill="none"/>`,
    veggie_lasagna: `${plate()}<path d="M7 11l14-3 5 3v8l-14 3-5-3z" fill="${C.beige}" ${o()}/><path d="M7 14l14-3 5 3M7 17l14-3 5 3" stroke="${C.red}" stroke-width="1.8" fill="none"/><path d="M7 11l14-3 5 3-14 3z" fill="#F2D06B" ${o()}/><circle cx="13" cy="11" r="1.2" fill="#6B2F80"/><circle cx="18" cy="10.5" r="1" fill="${C.green}"/>`,
    cream_puffs: `${plate()}${[[10, 18], [22, 18], [16, 13]].map(([x, y]) => `<circle cx="${x}" cy="${y}" r="5" fill="${C.bread}" ${o()}/><path d="M${x - 4} ${y}h8" stroke="${C.white}" stroke-width="2.4"/>`).join('')}<path d="M8 10l1 1M24 9l1 1M16 6l1 1" stroke="${C.white}" stroke-width="1.6"/>`,
    shrimp_pasta: `${plate()}<path d="M7 18c2-4 16-4 18 0-4 3-14 3-18 0z" fill="#F2D49B" ${o()}/><path d="M9 17c3-2 6 1 9-1s5 1 6 0" stroke="#D9B46B" stroke-width="1.2" fill="none"/>${[[11, 15], [19, 14.5]].map(([x, y]) => `<path d="M${x + 4} ${y}c-3-2-7 0-6 3 1 2 4 2 4 0" fill="#F58A6E" ${o('stroke-width="1.1"')}/>`).join('')}<circle cx="16" cy="16" r="1" fill="${C.green}"/>`,
    parmigiana: `${plate()}<rect x="7" y="11" width="18" height="9" rx="2" fill="${C.red}" ${o()}/><path d="M7 14h18M7 17h18" stroke="#6B2F80" stroke-width="1.6"/><path d="M8 11c3 2 5-1 8 1s5-1 8 1" stroke="${C.beige}" stroke-width="2.4" fill="none"/><path d="M14 9l2-1" stroke="${C.green}" stroke-width="2.2"/>`,
    pork_ribs: `${plate()}${[0, 1, 2].map(i => `<rect x="${8 + i * 5}" y="${11 + (i % 2)}" width="5" height="10" rx="2" fill="#A0522D" ${o('stroke-width="1.2"')}/><rect x="${9.5 + i * 5}" y="${8 + (i % 2)}" width="2" height="4" fill="${C.cream}" ${o('stroke-width="1"')}/>`).join('')}<path d="M8 15c4-1 10-1 15 0" stroke="#E08A1C" stroke-width="1.6" fill="none"/>`,
    tiramisu: `${plate()}<path d="M8 12l16 0v8H8z" fill="#F4E6D0" ${o()}/><path d="M8 15h16M8 18h16" stroke="#8A5A35" stroke-width="1.8"/><rect x="8" y="10" width="16" height="3" fill="#7B4E2C" ${o()}/>${[11, 15, 19, 22].map(x => `<circle cx="${x}" cy="11.5" r=".6" fill="#3B1F0E"/>`).join('')}`,
    sushi: `<rect x="3" y="17" width="26" height="7" rx="2" fill="${C.brown}" ${o()}/>${[8, 16, 24].map((x, i) => `<rect x="${x - 4}" y="12" width="8" height="5" rx="2" fill="${C.white}" ${o('stroke-width="1.2"')}/><path d="M${x - 4} 13c2-2 6-2 8 0v1H${x - 4}z" fill="${i === 1 ? '#F58A6E' : '#F38B5A'}" ${o('stroke-width="1"')}/><rect x="${x - 1}" y="12" width="2" height="5" fill="#2C3A2C"/>`).join('')}`,
    paella: `<ellipse cx="16" cy="19" rx="13" ry="6.5" fill="#2C2C30" ${o()}/><ellipse cx="16" cy="18" rx="11" ry="5" fill="#F2C24A"/>${[[10, 17, C.red], [16, 20, C.leaf], [21, 17, C.red], [14, 16, '#F58A6E'], [19, 19.5, '#F58A6E']].map(([x, y, c]) => `<circle cx="${x}" cy="${y}" r="1.6" fill="${c}"/>`).join('')}<path d="M3 19H0M29 19h3" stroke="${C.ink}" stroke-width="2"/><path d="M23 14l3-2" stroke="${C.yellow}" stroke-width="2.4"/>`,
    steak_fries: `${plate()}<path d="M7 17c0-4 4-6 8-6s6 2 6 5-3 5-7 5-7-1-7-4z" fill="#8A3A22" ${o()}/><path d="M10 15l6 2M12 13l5 2" stroke="#5A2412" stroke-width="1.2"/>${[0, 1, 2, 3].map(i => `<rect x="${19 + i * 2}" y="${11 + (i % 2) * 2}" width="1.8" height="8" rx=".6" fill="${C.yellow}" ${o('stroke-width=".9"')}/>`).join('')}<rect x="12" y="10" width="3" height="2" rx=".6" fill="#FFF0A6"/>`,
});

Object.assign(DISH, {
    gelato: `<path d="M10 18h12l-2 10h-8z" fill="${C.pink}" ${o()}/><circle cx="12.5" cy="15" r="4.2" fill="${C.pink}" ${o()}/><circle cx="19.5" cy="15" r="4.2" fill="#7B4E2C" ${o()}/><circle cx="16" cy="10" r="4.5" fill="#FFF3C4" ${o()}/><path d="M19 7l4-4" stroke="#E3A55B" stroke-width="3"/><circle cx="16" cy="5.5" r="1.6" fill="${C.red}"/>`,
    sorbet: `<path d="M8 14h16a8 6 0 0 1-16 0z" fill="#E8F6FF" ${o()}/><path d="M16 20v6M11 27h10" stroke="${C.ink}" stroke-width="1.6"/><circle cx="13" cy="12" r="4" fill="${C.yellow}" ${o()}/><circle cx="19.5" cy="12" r="4" fill="${C.red}" ${o()}/><path d="M16 8c1-2 3-2 4-1" stroke="${C.green}" stroke-width="2" fill="none"/>`,
    cold_brew: `<path d="M9 6h14l-2 21H11z" fill="#6B3E20" ${o()}/><rect x="12" y="11" width="4" height="4" fill="#F4FBFF" opacity=".8"/><rect x="16" y="16" width="4" height="4" fill="#F4FBFF" opacity=".8"/><path d="M11 9c3 2 7-1 10 1" stroke="#F4E6D0" stroke-width="2" fill="none"/><path d="M18 2l-1 12" stroke="${C.ink}" stroke-width="2"/>`,
});

const GROUPS = {ing: ING, dish: DISH, mood: MOOD, ui: UI};

export function icon(group, name) {
    const body = GROUPS[group]?.[name];
    // Food and moods look round and glossy; interface symbols stay flat and crisp
    return (group === 'ui' ? svg : shaded)(body || MOOD.plate);
}

/** Same as icon(), as a data: URL (for <img> and CSS). */
export const iconUrl = (group, name) => 'data:image/svg+xml,' + encodeURIComponent(icon(group, name));
