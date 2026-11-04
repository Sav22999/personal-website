/* Giftwise — UI components (DOM helper, icons, sheets, dialogs, toasts, dates). Same design system as FoodTrack. */
(function () {
  'use strict';

  const t = (s, v) => (window.I18N ? I18N.t(s, v) : s);

  // ---------- DOM ----------

  /** h('div.card#id', {onclick, ...attrs}, ...children) */
  function h(tag, attrs, ...children) {
    const m = /^([a-z0-9-]+)?((?:[.#][\w-]+)*)$/i.exec(tag) || [];
    const node = document.createElement(m[1] || 'div');
    (m[2] || '').replace(/([.#])([\w-]+)/g, (_, k, v) => { if (k === '.') node.classList.add(v); else node.id = v; });
    if (attrs && (typeof attrs !== 'object' || attrs instanceof Node || Array.isArray(attrs))) { children.unshift(attrs); attrs = null; }
    if (attrs) {
      for (const [k, v] of Object.entries(attrs)) {
        if (v === undefined || v === null || v === false) continue;
        if (k.startsWith('on') && typeof v === 'function') node.addEventListener(k.slice(2), v);
        else if (k === 'class') node.className += (node.className ? ' ' : '') + v;
        else if (k === 'style' && typeof v === 'object') Object.assign(node.style, v);
        else if (k === 'html') node.innerHTML = v;
        else if (k === 'value' && node.tagName === 'TEXTAREA') node.value = String(v); // a textarea ignores the value attribute
        else if (k in node && typeof v !== 'string') node[k] = v;
        else node.setAttribute(k, v === true ? '' : v);
      }
    }
    append(node, children);
    return node;
  }

  function append(node, children) {
    for (const c of children.flat(Infinity)) {
      if (c === null || c === undefined || c === false) continue;
      node.appendChild(c instanceof Node ? c : document.createTextNode(String(c)));
    }
    return node;
  }

  function clear(node) { while (node.firstChild) node.removeChild(node.firstChild); return node; }

  // ---------- Icons (Material Icons, 24px viewBox) ----------

  const PATHS = {
    gift: 'M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z',
    cake: 'M12 6c1.11 0 2-.9 2-2 0-.38-.1-.73-.29-1.03L12 0l-1.71 2.97c-.19.3-.29.65-.29 1.03 0 1.1.9 2 2 2zm4.6 9.99-1.07-1.07-1.08 1.07c-1.3 1.3-3.58 1.31-4.89 0l-1.07-1.07-1.09 1.07C6.75 16.64 5.88 17 4.96 17c-.73 0-1.4-.23-1.96-.61V21c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-4.61c-.56.38-1.23.61-1.96.61-.92 0-1.79-.36-2.44-1.01zM18 9h-5V7h-2v2H6c-1.66 0-3 1.34-3 3v1.54c0 1.08.88 1.96 1.96 1.96.52 0 1.02-.2 1.38-.57l2.14-2.13 2.13 2.13c.74.74 2.03.74 2.77 0l2.14-2.13 2.13 2.13c.37.37.86.57 1.38.57 1.08 0 1.96-.88 1.96-1.96V12C21 10.34 19.66 9 18 9z',
    tree: 'M17 12h2L12 2 5.05 12H7l-3.9 6h6.92v4h3.95v-4H21z',
    rings: 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
    baby: 'M14.5 10.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-5 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM12 17.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5zM22.94 12.66c.04-.21.06-.43.06-.66s-.02-.45-.06-.66a4.008 4.008 0 0 0-2.81-3.17 9.114 9.114 0 0 0-2.19-2.91C16.36 3.85 14.28 3 12 3s-4.36.85-5.94 2.26c-.92.81-1.67 1.8-2.19 2.91a3.994 3.994 0 0 0-2.81 3.17c-.04.21-.06.43-.06.66s.02.45.06.66a4.008 4.008 0 0 0 2.81 3.17 8.977 8.977 0 0 0 2.17 2.89C7.62 20.14 9.71 21 12 21s4.38-.86 5.97-2.28c.9-.8 1.65-1.79 2.17-2.89a3.998 3.998 0 0 0 2.8-3.17zM19 14c-.1 0-.19-.02-.29-.03-.2.67-.49 1.29-.86 1.86C16.6 17.74 14.45 19 12 19s-4.6-1.26-5.85-3.17c-.37-.57-.66-1.19-.86-1.86-.1.01-.19.03-.29.03-1.1 0-2-.9-2-2s.9-2 2-2c.1 0 .19.02.29.03.2-.67.49-1.29.86-1.86C7.4 6.26 9.55 5 12 5s4.6 1.26 5.85 3.17c.37.57.66 1.19.86 1.86.1-.01.19-.03.29-.03 1.1 0 2 .9 2 2s-.9 2-2 2z',
    grad: 'M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3 1 9l11 6 9-4.91V17h2V9L12 3z',
    house: 'M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z',
    party: 'M2 22l14-5-9-9-5 14zm12.53-9.47 5.59-5.59c.49-.49 1.28-.49 1.77 0l.59.59 1.06-1.06-.59-.59c-1.07-1.07-2.82-1.07-3.89 0l-5.59 5.59 1.06 1.06zm-4.47-5.65-.59.59 1.06 1.06.59-.59c1.07-1.07 1.07-2.82 0-3.89l-.59-.59-1.06 1.07.59.59c.48.48.48 1.28 0 1.76zm7 5-1.59 1.59 1.06 1.06 1.59-1.59c.49-.49 1.28-.49 1.77 0l1.61 1.61 1.06-1.06-1.61-1.61c-1.08-1.07-2.82-1.07-3.89 0zm-2-6-3.59 3.59 1.06 1.06 3.59-3.59c1.07-1.07 1.07-2.82 0-3.89l-1.59-1.59-1.06 1.06 1.59 1.59c.48.49.48 1.29 0 1.77z',
    chat: 'M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z',
    send: 'M2.01 21 23 12 2.01 3 2 10l15 2-15 2z',
    personAdd: 'M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z',
    star: 'M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z',
    starOutline: 'M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z',
    visibilityOff: 'M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78 3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z',
    archive: 'M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5 6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z',
    bag: 'M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z',
    think: 'M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7z',
    help: 'M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z',
    more: 'M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z',
    add: 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z',
    addCircle: 'M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z',
    back: 'M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z',
    search: 'M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z',
    filter: 'M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z',
    sort: 'M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z',
    cartAdd: 'M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z',
    cartRemove: 'M22.73 22.73 2.77 2.77 2 2l-.73-.73L0 2.54l4.39 4.39 2.21 4.66-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h7.46l1.38 1.38c-.5.36-.83.95-.83 1.62 0 1.1.89 2 1.99 2 .67 0 1.26-.33 1.62-.84L21.46 24l1.27-1.27zM7.42 15c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h2.36l2 2H7.42zm8.13-2c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H6.54l9.01 9zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2z',
    heart: 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
    heartOutline: 'M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z',
    edit: 'M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z',
    delete: 'M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z',
    warning: 'M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z',
    list: 'M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5 5.5 6.83 5.5 6 4.83 4.5 4 4.5zm0 12c-.83 0-1.5.68-1.5 1.5s.68 1.5 1.5 1.5 1.5-.68 1.5-1.5-.67-1.5-1.5-1.5zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z',
    spaces: 'M9 21H5c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2zm6 0h4c1.1 0 2-.9 2-2v-5c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v5c0 1.1.9 2 2 2zm6-13V5c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h4c1.1 0 2-.9 2-2z',
    restaurant: 'M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z',
    person: 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z',
    share: 'M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z',
    calendar: 'M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z',
    scan: 'M9.5 6.5v3h-3v-3h3M11 5H5v6h6V5zm-1.5 9.5v3h-3v-3h3M11 13H5v6h6v-6zm6.5-6.5v3h-3v-3h3M19 5h-6v6h6V5zm-6 8h1.5v1.5H13V13zm1.5 1.5H16V16h-1.5v-1.5zM16 13h1.5v1.5H16V13zm-3 3h1.5v1.5H13V16zm1.5 1.5H16V19h-1.5v-1.5zM16 16h1.5v1.5H16V16zm1.5-1.5H19V16h-1.5v-1.5zm0 3H19V19h-1.5v-1.5zM22 7h-2V4h-3V2h5v5zm0 15v-5h-2v3h-3v2h5zM2 22h5v-2H4v-3H2v5zM2 2v5h2V4h3V2H2z',
    move: 'M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6 12v-3h-4v-4h4V8l5 5-5 5z',
    info: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z',
    logout: 'M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z',
    check: 'M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z',
    close: 'M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z',
    save: 'M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z',
    left: 'M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z',
    right: 'M10 6 8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z',
    home: 'M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z',
    timer: 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z',
    group: 'M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z',
    shield: 'M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z',
    copy: 'M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z',
    bell: 'M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z',
    swap: 'M6.99 11 3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z',
    lock: 'M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z',
    eye: 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z',
    upload: 'M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z',
    dashboard: 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z',
    category: 'M12 2l-5.5 9h11L12 2zm5.5 11c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zM3 21.5h8v-8H3v8z',
    open: 'M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z',
    globe: 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95a15.65 15.65 0 0 0-1.38-3.56A8.03 8.03 0 0 1 18.92 8zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2s.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56A7.987 7.987 0 0 1 5.08 16zm2.95-8H5.08a7.987 7.987 0 0 1 4.33-3.56A15.65 15.65 0 0 0 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2s.07-1.35.16-2h4.68c.09.65.16 1.32.16 2s-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95a8.03 8.03 0 0 1-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2s-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z',
    school: 'M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3 1 9l11 6 9-4.91V17h2V9L12 3z',
    bulb: 'M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7z',
    error: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z',
    menu: 'M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z',
    chevronDown: 'M16.59 8.59 12 13.17 7.41 8.59 6 10l6 6 6-6z',
    up: 'M7.41 15.41 12 10.83l4.59 4.58L18 14l-6-6-6 6z',
    down: 'M7.41 8.59 12 13.17l4.59-4.58L18 10l-6 6-6-6z',
    image: 'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z',
    theme: 'M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z',
    link: 'M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z',
  };

  function icon(name, cls) {
    const ns = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(ns, 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('class', 'ic' + (cls ? ' ' + cls : ''));
    const p = document.createElementNS(ns, 'path');
    p.setAttribute('d', PATHS[name] || PATHS.info);
    svg.appendChild(p);
    return svg;
  }

  // ---------- Sign in with Google (Google Identity Services) ----------

  let gisPromise = null;
  function loadGis() {
    if (!gisPromise) {
      gisPromise = new Promise((resolve, reject) => {
        const sc = document.createElement('script');
        sc.src = 'https://accounts.google.com/gsi/client';
        sc.async = true;
        sc.onload = () => resolve(window.google);
        sc.onerror = () => { gisPromise = null; reject(new Error('Google unreachable')); };
        document.head.appendChild(sc);
      });
    }
    return gisPromise;
  }
  /** Renders the official "Continue with Google" button; onCredential receives the ID token. */
  // ---------- Android app bridge ----------
  // Inside the FoodTrack Android app (native WebView shell) window.GiftwiseAndroid is injected:
  // Google sign-in goes through the system account picker (Credential Manager) and notifications are native.
  const native = window.GiftwiseAndroid || null;
  const nativeWait = {};
  window.GiftwiseNative = {
    // Called by the app: (kind, value, error)
    result(kind, value, error) { const w = nativeWait[kind]; delete nativeWait[kind]; if (w) (error ? w.reject(new Error(error)) : w.resolve(value)); },
  };
  function nativeCall(kind, fn) {
    return new Promise((resolve, reject) => { nativeWait[kind] = { resolve, reject }; try { fn(); } catch (e) { delete nativeWait[kind]; reject(e); } });
  }
  const app = {
    isAndroidApp: !!native,
    hasNativeGoogle: !!(native && native.googleSignIn),
    googleSignIn: clientId => nativeCall('google', () => native.googleSignIn(clientId)),
    notificationsSupported: () => !!native || 'Notification' in window,
    notificationPermission: () => native ? String(native.notificationPermission()) : ('Notification' in window ? Notification.permission : 'denied'),
    requestNotifications: () => native ? nativeCall('notify', () => native.requestNotificationPermission()) : Notification.requestPermission(),
    notify: (title, body) => { if (native) { native.notify(title, body); return true; } return false; },
  };

  const G_LOGO = '<svg viewBox="0 0 48 48" width="20" height="20" aria-hidden="true"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.6l6.8-6.8C35.8 2.4 30.3 0 24 0 14.6 0 6.6 5.4 2.6 13.3l7.9 6.1C12.4 13.7 17.7 9.5 24 9.5z"/><path fill="#4285F4" d="M46.1 24.5c0-1.6-.1-3.1-.4-4.5H24v9h12.4c-.5 2.9-2.2 5.3-4.6 6.9l7.4 5.8c4.3-4 6.9-9.9 6.9-17.2z"/><path fill="#FBBC05" d="M10.5 28.6c-.5-1.4-.8-2.9-.8-4.6s.3-3.2.8-4.6l-7.9-6.1C1 16.6 0 20.2 0 24s1 7.4 2.6 10.7l7.9-6.1z"/><path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.9-5.8l-7.4-5.8c-2.1 1.4-4.8 2.3-8.5 2.3-6.3 0-11.6-4.2-13.5-10l-7.9 6.1C6.6 42.6 14.6 48 24 48z"/></svg>';

  async function googleButton(slot, clientId, onCredential, text) {
    if (app.hasNativeGoogle) {
      // Android app: Google Identity Services does not run in a WebView, the system account picker does
      const label = { signup_with: t('Sign up with Google'), signin_with: t('Sign in with Google') }[text] || t('Continue with Google');
      const b = h('button.btn.btn-google', { type: 'button', onclick: async () => {
        b.disabled = true;
        try { const token = await app.googleSignIn(clientId); if (token) onCredential(token); }
        catch (e) { if (e.message && e.message !== 'cancelled') toast(t('Google sign-in is not available right now.') + ' (' + e.message + ')', 'error'); }
        finally { b.disabled = false; }
      } }, h('span.g', { html: G_LOGO }), h('span', label));
      slot.replaceChildren(b);
      return;
    }
    try {
      const g = await loadGis();
      g.accounts.id.initialize({ client_id: clientId, callback: r => onCredential(r.credential), ux_mode: 'popup', auto_select: false, cancel_on_tap_outside: true, itp_support: true, use_fedcm_for_button: true });
      const dark = document.documentElement.getAttribute('data-theme') === 'dark' || (!document.documentElement.hasAttribute('data-theme') && matchMedia('(prefers-color-scheme: dark)').matches);
      clear(slot);
      g.accounts.id.renderButton(slot, { type: 'standard', theme: dark ? 'filled_black' : 'outline', size: 'large', text: text || 'continue_with', shape: 'pill', logo_alignment: 'center', locale: (window.I18N ? I18N.lang : 'it'), width: Math.min(Math.max(slot.clientWidth, 220), 400) });
    } catch (e) {
      slot.replaceChildren(h('p.helper.center', t('Google sign-in is not available right now.')));
    }
  }

  // ---------- Shared components ----------

  /** Leading icon tile: a pictogram (products/spaces/lists) or a Material icon, always in the same rounded container. */
  function tile(kind, name, size) {
    const t = h('span.tile' + (size ? '.tile-' + size : ''));
    t.appendChild(icon(name));
    return t;
  }

  /** Status chip: tone = danger | warn | ok | neutral */
  function chip(text, tone, ic) {
    return h('span.status.' + (tone || 'neutral'), ic ? icon(ic) : null, text);
  }

  /** Extended floating action button (main "add" action of a screen). */
  function fab(label, ic, onclick) {
    return h('button.fab', { type: 'button', onclick }, icon(ic || 'add'), h('span', label));
  }

  // ---------- Buttons ----------

  function btn(label, opts) {
    opts = opts || {};
    const b = h('button.btn' + (opts.kind ? '.btn-' + opts.kind : '.btn-filled'), { type: opts.type || 'button', onclick: opts.onclick, disabled: opts.disabled, title: opts.title });
    if (opts.icon) b.appendChild(icon(opts.icon));
    if (label) b.appendChild(h('span', label));
    if (opts.cls) b.classList.add(...opts.cls.split(' '));
    return b;
  }

  function iconBtn(name, label, onclick, cls) {
    return h('button.icon-btn' + (cls ? '.' + cls : ''), { type: 'button', 'aria-label': label, title: label, onclick: e => { e.stopPropagation(); onclick(e); } }, icon(name));
  }

  /** Disables a button while an async action runs and reports its error. */
  async function busy(button, fn) {
    if (button) { button.disabled = true; button.classList.add('loading'); }
    try { return await fn(); }
    catch (e) { toast(e.message || t('Something went wrong.'), 'error'); throw e; }
    finally { if (button) { button.disabled = false; button.classList.remove('loading'); } }
  }

  // ---------- Form fields (Material "outlined" text fields) ----------

  function field(label, input, opts) {
    opts = opts || {};
    input.classList.add('input');
    if (!input.id) input.id = 'f' + Math.random().toString(36).slice(2, 9);
    if (!input.placeholder) input.placeholder = ' ';
    return h('div.field' + (opts.cls ? '.' + opts.cls : ''), input, h('label', { for: input.id }, label), input.tagName === 'SELECT' ? icon('chevronDown', 'chev') : null, opts.hint ? h('p.hint', opts.hint) : null);
  }

  function input(attrs) { return h('input', Object.assign({ type: 'text', autocomplete: 'off' }, attrs || {})); }
  function textarea(attrs) { return h('textarea', Object.assign({ rows: 3 }, attrs || {})); }
  function select(options, value, attrs) {
    const s = h('select', attrs || {});
    for (const o of options) {
      const opt = h('option', { value: String(o.value) }, o.label);
      if (String(o.value) === String(value ?? '')) opt.selected = true;
      s.appendChild(opt);
    }
    return s;
  }

  // ---------- Bottom sheets (with Android back button support) ----------

  const stack = [];
  let ignorePop = 0;
  let lastPointer = null;
  document.addEventListener('pointerdown', e => { if (e.pointerType === 'mouse') lastPointer = { x: e.clientX, y: e.clientY, at: Date.now() }; }, true);

  function sheet(opts) {
    const handle = h('div.sheet-handle');
    const panel = h('div.sheet' + (opts.cls ? '.' + opts.cls : ''), { role: 'dialog', 'aria-modal': 'true', 'aria-label': opts.title || '' },
      handle,
      opts.title ? h('h2.sheet-title', opts.title) : null,
      h('div.sheet-body', opts.body || []),
      opts.actions && opts.actions.length ? h('div.sheet-actions', opts.actions) : null,
    );
    const backdrop = h('div.sheet-backdrop', { onclick: e => { if (e.target === backdrop && !opts.persistent) close(); } }, panel);
    // Desktop: a menu opened with the mouse becomes a popover next to the pointer
    const pop = opts.popover && lastPointer && Date.now() - lastPointer.at < 800 && matchMedia('(min-width: 960px) and (pointer: fine)').matches;
    if (pop) backdrop.classList.add('popover');
    document.body.appendChild(backdrop);
    if (pop) {
      const r = panel.getBoundingClientRect(), m = 12;
      panel.style.left = Math.max(m, Math.min(lastPointer.x - r.width + 24, innerWidth - r.width - m)) + 'px';
      panel.style.top = (lastPointer.y + 12 + r.height > innerHeight - m ? Math.max(m, lastPointer.y - r.height - 12) : lastPointer.y + 12) + 'px';
    }
    document.body.classList.add('no-scroll');
    requestAnimationFrame(() => backdrop.classList.add('open'));

    let closed = false;
    const entry = { close: () => doClose(false), persistent: !!opts.persistent };
    stack.push(entry);
    history.pushState({ ftSheet: stack.length }, '');

    // Swipe down on the handle to close
    let startY = null;
    handle.addEventListener('pointerdown', e => { startY = e.clientY; handle.setPointerCapture(e.pointerId); });
    handle.addEventListener('pointermove', e => { if (startY !== null) panel.style.transform = `translateY(${Math.max(0, e.clientY - startY)}px)`; });
    handle.addEventListener('pointerup', e => { const d = e.clientY - (startY || 0); startY = null; panel.style.transform = ''; if (d > 80) close(); });

    function doClose(fromHistory) {
      if (closed) return;
      closed = true;
      const i = stack.indexOf(entry);
      if (i >= 0) stack.splice(i, 1);
      backdrop.classList.remove('open');
      setTimeout(() => { backdrop.remove(); if (!stack.length) document.body.classList.remove('no-scroll'); }, 220);
      if (!fromHistory) { ignorePop++; history.back(); }
      if (opts.onclose) opts.onclose();
    }
    function close() { doClose(false); }
    entry.fromHistory = () => doClose(true);
    const first = panel.querySelector('input:not([type=checkbox]):not([readonly]), textarea, select');
    if (first && opts.autofocus !== false && !matchMedia('(pointer: coarse)').matches) setTimeout(() => first.focus(), 250);
    return { close, panel, body: panel.querySelector('.sheet-body') };
  }

  window.addEventListener('popstate', e => {
    if (ignorePop > 0) { ignorePop--; e.stopImmediatePropagation(); return; }
    if (stack.length) { e.stopImmediatePropagation(); stack[stack.length - 1].fromHistory(); }
  });

  // Esc closes the topmost sheet (dialogs on desktop)
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && stack.length && !stack[stack.length - 1].persistent && !document.querySelector('.tour-ovl')) { e.preventDefault(); closeTop(); } });

  function closeTop() { if (stack.length) stack[stack.length - 1].close(); }
  function closeAllSheets() { while (stack.length) stack[stack.length - 1].close(); }

  /** Menu of actions as a bottom sheet (the "Altre azioni" pattern of the app). */
  function actionSheet(items) {
    const s = sheet({
      title: arguments[1] || null, cls: 'menu-sheet', popover: true,
      body: [h('div.menu', items.filter(Boolean).map(it => h('button.menu-item' + (it.danger ? '.danger' : '') + (it.selected ? '.selected' : ''), { type: 'button', onclick: () => { s.close(); setTimeout(it.onclick, 230); } }, icon(it.icon), h('span', it.label), it.selected ? icon('check', 'trail') : null)))],
    });
    return s;
  }

  function confirm(title, text, okLabel, danger) {
    return new Promise(resolve => {
      let answered = false;
      const s = sheet({
        title, body: [h('p.sheet-text', text)],
        onclose: () => { if (!answered) resolve(false); },
        actions: [
          btn(t('Cancel'), { kind: 'text', onclick: () => s.close() }),
          btn(okLabel || t('Confirm'), { kind: danger ? 'danger' : 'filled', icon: danger ? 'delete' : 'check', onclick: () => { answered = true; s.close(); resolve(true); } }),
        ],
      });
    });
  }

  // ---------- Snackbar ----------

  let toastTimer = null;
  function toast(text, kind, action) {
    let t = document.getElementById('toast');
    if (!t) { t = h('div#toast', { role: 'status', 'aria-live': 'polite' }); document.body.appendChild(t); }
    clear(t);
    t.className = 'show' + (kind ? ' ' + kind : '');
    t.appendChild(h('span', text));
    if (action) t.appendChild(h('button', { type: 'button', onclick: () => { t.className = ''; action.onclick(); } }, action.label));
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { t.className = ''; }, action ? 6000 : 3200);
  }

  // ---------- Dates ----------

  function todayStr() {
    const d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }
  function parseDate(s) { const [y, m, d] = s.split('-').map(Number); return new Date(y, m - 1, d); }
  function dateStr(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }
  function addDays(s, n) { const d = parseDate(s); d.setDate(d.getDate() + n); return dateStr(d); }
  function daysUntil(s) { return Math.round((parseDate(s) - parseDate(todayStr())) / 86400000); }
  function fmtDate(s) { if (!s) return ''; const [y, m, d] = s.split('-'); return `${d}/${m}/${y}`; }
  /** "today", "tomorrow", "in 12 days", "3 days ago". */
  function relDays(n) {
    if (n < -1) return t('{n} days ago', { n: -n });
    if (n === -1) return t('yesterday');
    if (n === 0) return t('today');
    if (n === 1) return t('tomorrow');
    return t('in {n} days', { n });
  }
  /** "5 min ago", "2 h ago", or the date. */
  function ago(s) {
    if (!s) return '';
    const d = new Date(s.replace(' ', 'T') + 'Z'), m = Math.round((Date.now() - d) / 60000);
    if (m < 1) return t('just now');
    if (m < 60) return t('{n} min ago', { n: m });
    if (m < 60 * 24) return t('{n} h ago', { n: Math.round(m / 60) });
    return d.toLocaleDateString(locale(), { day: 'numeric', month: 'short' });
  }
  /** Price in the user's locale ("€ 89,90"). */
  function money(v, cur) {
    if (v === null || v === undefined || v === '') return '';
    try { return Number(v).toLocaleString(locale(), { style: 'currency', currency: cur || 'EUR' }); } catch (e) { return Number(v).toFixed(2) + ' ' + (cur || ''); }
  }
  const locale = () => (window.I18N ? I18N.locale : 'it-IT');
  /** "Monday 3 November" in the user's language. */
  function longDate(s) { return parseDate(s).toLocaleDateString(locale(), { weekday: 'long', day: 'numeric', month: 'long' }); }
  function fmtDateTime(s) {
    if (!s) return '—';
    const d = new Date(s.replace(' ', 'T') + 'Z');
    return d.toLocaleDateString(locale()) + ' ' + d.toLocaleTimeString(locale(), { hour: '2-digit', minute: '2-digit' });
  }

  function norm(s) { return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[’']/g, "'").trim(); }

  // ---------- Storage (per device preferences) ----------

  const store = {
    get(k, def) { try { const v = localStorage.getItem('gw.' + k); return v === null ? def : JSON.parse(v); } catch (e) { return def; } },
    set(k, v) { try { localStorage.setItem('gw.' + k, JSON.stringify(v)); } catch (e) { /* private mode */ } },
  };

  function applyTheme() {
    const t = store.get('theme', 'auto');
    if (t === 'auto') document.documentElement.removeAttribute('data-theme');
    else document.documentElement.setAttribute('data-theme', t);
    const dark = t === 'dark' || (t === 'auto' && matchMedia('(prefers-color-scheme: dark)').matches);
    document.querySelectorAll('meta[name=theme-color]').forEach(m => { m.removeAttribute('media'); m.setAttribute('content', dark ? '#150F11' : '#FBF6F7'); });
  }

  window.UI = {
    app, h, append, clear, icon, googleButton, tile, chip, fab, btn, iconBtn, busy, field, input, textarea, select,
    sheet, actionSheet, confirm, closeTop, closeAllSheets, toast,
    todayStr, parseDate, dateStr, addDays, daysUntil, fmtDate, relDays, ago, money, longDate, fmtDateTime, norm, store, applyTheme,
    sheetsOpen: () => stack.length,
  };
})();
