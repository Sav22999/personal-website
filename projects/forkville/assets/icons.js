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

const svg = (body) => `<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" stroke-linejoin="round" stroke-linecap="round">${body}</svg>`;
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
    w_rain: `<path d="M8 19a5 5 0 0 1 0-10 7 7 0 0 1 13-2 5 5 0 0 1 3 12z" fill="${C.grey}" ${o()}/><path d="M10 23l-2 5M16 23l-2 5M22 23l-2 5" stroke="${C.blue}" stroke-width="2.2"/>`,
    w_snow: `<path d="M8 18a5 5 0 0 1 0-10 7 7 0 0 1 13-2 5 5 0 0 1 3 12z" fill="${C.white}" ${o()}/>${[[10, 24], [16, 27], [22, 24]].map(([x, y]) => `<path d="M${x - 2.5} ${y}h5M${x} ${y - 2.5}v5M${x - 1.8} ${y - 1.8}l3.6 3.6M${x + 1.8} ${y - 1.8}l-3.6 3.6" stroke="${C.lblue}" stroke-width="1.5"/>`).join('')}`,
    manager: `<circle cx="16" cy="11" r="6" fill="${C.skin}" ${o()}/><path d="M5 29c0-7 5-11 11-11s11 4 11 11z" fill="${C.ink}" ${o()}/><path d="M16 18l-2.5 4 2.5 7 2.5-7z" fill="${C.red}"/><path d="M10 7c2-3 10-3 12 0" stroke="${C.dbrown}" stroke-width="3" fill="none"/>`,
    pause: `<rect x="8" y="6" width="5.5" height="20" rx="1.5" fill="currentColor"/><rect x="18.5" y="6" width="5.5" height="20" rx="1.5" fill="currentColor"/>`,
    play1: `<path d="M10 6l15 10-15 10z" fill="currentColor"/>`,
    play2: `<path d="M5 7l11 9-11 9zM16 7l11 9-11 9z" fill="currentColor"/>`,
    play3: `<path d="M2 8l9 8-9 8zM11.5 8l9 8-9 8zM21 8l9 8-9 8z" fill="currentColor"/>`,
    broom: `<path d="M22 3l-8 13" stroke="${C.dbrown}" stroke-width="2.6"/><path d="M12 14l6 4-3 10c-4 0-9-3-10-6z" fill="${C.yellow}" ${o()}/><path d="M9 22l3-5M12 24l2-5" stroke="${C.dbrown}" stroke-width="1.2"/>`,
    waiter: `<ellipse cx="16" cy="20" rx="12" ry="3" fill="${C.grey}" ${o()}/><path d="M6 19a10 9 0 0 1 20 0z" fill="${C.grey}" ${o()}/><circle cx="16" cy="9" r="2" fill="${C.ink}"/>`,
};

const GROUPS = {ing: ING, dish: DISH, mood: MOOD, ui: UI};

export function icon(group, name) {
    const body = GROUPS[group]?.[name];
    return svg(body || MOOD.plate);
}

/** Same as icon(), as a data: URL (for <img> and CSS). */
export const iconUrl = (group, name) => 'data:image/svg+xml,' + encodeURIComponent(icon(group, name));
