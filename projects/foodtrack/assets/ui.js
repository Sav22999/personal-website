/* FoodTrack — UI helpers: DOM builder, icons, bottom sheets, snackbar, dates and units. */
(function () {
    'use strict';

    const t = (s, v) => (window.I18N ? I18N.t(s, v) : s);

    // ---------- DOM ----------

    /** h('div.card#id', {onclick, ...attrs}, ...children) */
    function h(tag, attrs, ...children) {
        const m = /^([a-z0-9-]+)?((?:[.#][\w-]+)*)$/i.exec(tag) || [];
        const node = document.createElement(m[1] || 'div');
        (m[2] || '').replace(/([.#])([\w-]+)/g, (_, k, v) => {
            if (k === '.') node.classList.add(v); else node.id = v;
        });
        if (attrs && (typeof attrs !== 'object' || attrs instanceof Node || Array.isArray(attrs))) {
            children.unshift(attrs);
            attrs = null;
        }
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

    function clear(node) {
        while (node.firstChild) node.removeChild(node.firstChild);
        return node;
    }

    // ---------- Icons (Material Icons, 24px viewBox) ----------

    const PATHS = {
        more: 'M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z',
        add: 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z',
        minus: 'M19 13H5v-2h14v2z',
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
        history: 'M13 3a9 9 0 0 0-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.954 8.954 0 0 0 13 21a9 9 0 0 0 0-18zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z',
        undo: 'M12.5 8c-2.65 0-5.05.99-6.9 2.6L2 7v9h9l-3.62-3.62c1.39-1.16 3.16-1.88 5.12-1.88 3.54 0 6.55 2.31 7.6 5.5l2.37-.78C21.08 11.03 17.15 8 12.5 8z',
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

    /** Pictogram of a product / space / list from the SVG sprite (follows currentColor, any size). */
    const SPRITE = 'assets/img/icons.svg?v=4';

    function pic(folder, name, cls) {
        const ns = 'http://www.w3.org/2000/svg';
        const known = iconNames(folder);
        if (!known.includes(name)) name = folder === 'products' ? 'generic' : folder === 'spaces' ? 'pantry' : 'cart';
        const svg = document.createElementNS(ns, 'svg');
        svg.setAttribute('class', 'pic' + (cls ? ' ' + cls : ''));
        svg.setAttribute('aria-hidden', 'true');
        const use = document.createElementNS(ns, 'use');
        use.setAttribute('href', SPRITE + '#' + folder + '-' + name);
        svg.appendChild(use);
        return svg;
    }

    function iconNames(folder) {
        return ((window.FT_ICONS || {})[folder] || []).map(x => x[0]);
    }

    function iconLabel(folder, name) {
        const hit = ((window.FT_ICONS || {})[folder] || []).find(x => x[0] === name);
        if (window.I18N && I18N.lang !== 'it') return name.replace(/_/g, ' ').replace(/\d+$/, '');
        return hit ? hit[1] : name;
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
                sc.onerror = () => {
                    gisPromise = null;
                    reject(new Error('Google unreachable'));
                };
                document.head.appendChild(sc);
            });
        }
        return gisPromise;
    }

    /** Renders the official "Continue with Google" button; onCredential receives the ID token. */
        // ---------- Android app bridge ----------
        // Inside the FoodTrack Android app (native WebView shell) window.FoodTrackAndroid is injected:
        // Google sign-in goes through the system account picker (Credential Manager) and notifications are native.
    const native = window.FoodTrackAndroid || null;
    const nativeWait = {};
    window.FoodTrackNative = {
        // Called by the app: (kind, value, error)
        result(kind, value, error) {
            const w = nativeWait[kind];
            delete nativeWait[kind];
            if (w) (error ? w.reject(new Error(error)) : w.resolve(value));
        },
    };

    function nativeCall(kind, fn) {
        return new Promise((resolve, reject) => {
            nativeWait[kind] = {resolve, reject};
            try {
                fn();
            } catch (e) {
                delete nativeWait[kind];
                reject(e);
            }
        });
    }

    const app = {
        isAndroidApp: !!native,
        hasNativeGoogle: !!(native && native.googleSignIn),
        googleSignIn: clientId => nativeCall('google', () => native.googleSignIn(clientId)),
        notificationsSupported: () => !!native || 'Notification' in window,
        notificationPermission: () => native ? String(native.notificationPermission()) : ('Notification' in window ? Notification.permission : 'denied'),
        requestNotifications: () => native ? nativeCall('notify', () => native.requestNotificationPermission()) : Notification.requestPermission(),
        backgroundActivity: (on, after, lang) => {
            if (native && native.setBackgroundActivity) native.setBackgroundActivity(on, String(after), lang); // Older app versions don't have it
        },
        notify: (title, body) => {
            if (native) {
                native.notify(title, body);
                return true;
            }
            return false;
        },
    };

    const G_LOGO = '<svg viewBox="0 0 48 48" width="20" height="20" aria-hidden="true"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.6l6.8-6.8C35.8 2.4 30.3 0 24 0 14.6 0 6.6 5.4 2.6 13.3l7.9 6.1C12.4 13.7 17.7 9.5 24 9.5z"/><path fill="#4285F4" d="M46.1 24.5c0-1.6-.1-3.1-.4-4.5H24v9h12.4c-.5 2.9-2.2 5.3-4.6 6.9l7.4 5.8c4.3-4 6.9-9.9 6.9-17.2z"/><path fill="#FBBC05" d="M10.5 28.6c-.5-1.4-.8-2.9-.8-4.6s.3-3.2.8-4.6l-7.9-6.1C1 16.6 0 20.2 0 24s1 7.4 2.6 10.7l7.9-6.1z"/><path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.9-5.8l-7.4-5.8c-2.1 1.4-4.8 2.3-8.5 2.3-6.3 0-11.6-4.2-13.5-10l-7.9 6.1C6.6 42.6 14.6 48 24 48z"/></svg>';

    async function googleButton(slot, clientId, onCredential, text) {
        if (app.hasNativeGoogle) {
            // Android app: Google Identity Services does not run in a WebView, the system account picker does
            const label = {
                signup_with: t('Sign up with Google'),
                signin_with: t('Sign in with Google')
            }[text] || t('Continue with Google');
            const b = h('button.btn.btn-google', {
                type: 'button', onclick: async () => {
                    b.disabled = true;
                    try {
                        const token = await app.googleSignIn(clientId);
                        if (token) onCredential(token);
                    } catch (e) {
                        if (e.message && e.message !== 'cancelled') toast(t('Google sign-in is not available right now.') + ' (' + e.message + ')', 'error');
                    } finally {
                        b.disabled = false;
                    }
                }
            }, h('span.g', {html: G_LOGO}), h('span', label));
            slot.replaceChildren(b);
            return;
        }
        try {
            const g = await loadGis();
            g.accounts.id.initialize({
                client_id: clientId,
                callback: r => onCredential(r.credential),
                ux_mode: 'popup',
                auto_select: false,
                cancel_on_tap_outside: true,
                itp_support: true,
                use_fedcm_for_button: true
            });
            const dark = document.documentElement.getAttribute('data-theme') === 'dark' || (!document.documentElement.hasAttribute('data-theme') && matchMedia('(prefers-color-scheme: dark)').matches);
            clear(slot);
            g.accounts.id.renderButton(slot, {
                type: 'standard',
                theme: dark ? 'filled_black' : 'outline',
                size: 'large',
                text: text || 'continue_with',
                shape: 'pill',
                logo_alignment: 'center',
                locale: (window.I18N ? I18N.lang : 'it'),
                width: Math.min(Math.max(slot.clientWidth, 220), 400)
            });
        } catch (e) {
            slot.replaceChildren(h('p.helper.center', t('Google sign-in is not available right now.')));
        }
    }

    // ---------- Shared components ----------

    /** Leading icon tile: a pictogram (products/spaces/lists) or a Material icon, always in the same rounded container. */
    function tile(kind, name, size) {
        const t = h('span.tile' + (size ? '.tile-' + size : ''));
        t.appendChild(kind === 'icon' ? icon(name) : pic(kind, name));
        return t;
    }

    /** Status chip: tone = danger | warn | ok | neutral */
    function chip(text, tone, ic) {
        return h('span.status.' + (tone || 'neutral'), ic ? icon(ic) : null, text);
    }

    /** Extended floating action button (main "add" action of a screen). */
    function fab(label, ic, onclick) {
        return h('button.fab', {type: 'button', onclick}, icon(ic || 'add'), h('span', label));
    }

    // ---------- Buttons ----------

    function btn(label, opts) {
        opts = opts || {};
        const b = h('button.btn' + (opts.kind ? '.btn-' + opts.kind : '.btn-filled'), {
            type: opts.type || 'button',
            onclick: opts.onclick,
            disabled: opts.disabled,
            title: opts.title
        });
        if (opts.icon) b.appendChild(icon(opts.icon));
        if (label) b.appendChild(h('span', label));
        if (opts.cls) b.classList.add(...opts.cls.split(' '));
        return b;
    }

    function iconBtn(name, label, onclick, cls) {
        return h('button.icon-btn' + (cls ? '.' + cls : ''), {
            type: 'button',
            'aria-label': label,
            title: label,
            onclick: e => {
                e.stopPropagation();
                onclick(e);
            }
        }, icon(name));
    }

    /** Disables a button while an async action runs and reports its error. */
    async function busy(button, fn) {
        if (button) {
            button.disabled = true;
            button.classList.add('loading');
        }
        try {
            return await fn();
        } catch (e) {
            toast(e.message || t('Something went wrong.'), 'error');
            throw e;
        } finally {
            if (button) {
                button.disabled = false;
                button.classList.remove('loading');
            }
        }
    }

    // ---------- Form fields (Material "outlined" text fields) ----------

    function field(label, input, opts) {
        opts = opts || {};
        input.classList.add('input');
        if (!input.id) input.id = 'f' + Math.random().toString(36).slice(2, 9);
        if (!input.placeholder) input.placeholder = ' ';
        return h('div.field' + (opts.cls ? '.' + opts.cls : ''), input, h('label', {for: input.id}, label), input.tagName === 'SELECT' ? icon('chevronDown', 'chev') : null, opts.hint ? h('p.hint', opts.hint) : null);
    }

    function input(attrs) {
        return h('input', Object.assign({type: 'text', autocomplete: 'off'}, attrs || {}));
    }

    function textarea(attrs) {
        return h('textarea', Object.assign({rows: 3}, attrs || {}));
    }

    /**
     * Fills a list little by little while the user scrolls: render(item) gives the node of an item.
     * Gives back the element to put right after the list, which brings in the next items as it gets near the screen.
     * opts: {step, shown (items to show at once), onmore(shown)}.
     */
    function infiniteList(parent, items, render, opts) {
        const step = opts.step || 24;
        const end = h('div.infinite-end', {'aria-hidden': 'true'});
        let shown = 0, observer = null;

        function more(count) {
            const next = Math.min(items.length, shown + count);
            for (; shown < next; shown++) parent.appendChild(render(items[shown]));
            if (opts.onmore) opts.onmore(shown);
            if (shown >= items.length && observer) observer.disconnect();
        }

        end._stop = () => observer && observer.disconnect();
        if (!('IntersectionObserver' in window)) {
            more(items.length);
            return end;
        }
        more(Math.max(step, opts.shown || 0));
        if (shown < items.length) {
            observer = new IntersectionObserver(entries => {
                if (!entries.some(e => e.isIntersecting)) return;
                more(step);
                observer.unobserve(end); // Observing again checks if the end is still near the screen
                if (shown < items.length) observer.observe(end);
            }, {rootMargin: '600px'});
            observer.observe(end);
        }
        return end;
    }

    /** Fills a <select>. An option can have a flag (country code) or a pic ([folder, icon]), shown in the custom list. */
    function fillSelect(s, options, value) {
        for (const o of options) {
            const opt = h('option', {value: String(o.value)}, o.label);
            if (o.flag) opt.dataset.flag = o.flag;
            if (o.pic) opt.dataset.pic = o.pic.join('/');
            if (String(o.value) === String(value ?? '')) opt.selected = true;
            s.appendChild(opt);
        }
        refreshSelectFlag(s);
        return s;
    }

    function select(options, value, attrs) {
        return fillSelect(h('select', attrs || {}), options, value);
    }

    /** Shows the flag of the chosen option inside the closed <select>. */
    function refreshSelectFlag(s) {
        const code = s.selectedOptions[0] && s.selectedOptions[0].dataset.flag;
        if (code) {
            s.dataset.lead = '1';
            s.style.setProperty('--lead', `url("${new URL(`assets/flags/${code.toLowerCase()}.svg`, document.baseURI).href}")`); // Absolute: a url() in a variable would be relative to the stylesheet
        } else {
            delete s.dataset.lead;
            s.style.removeProperty('--lead');
        }
    }

    // ---------- Dropdowns ----------
    // With a mouse every <select> of the app opens this list instead of the one of the operating system: same look
    // everywhere, flags and icons, and a search box in long lists. The real <select> stays in the page and keeps the
    // value, so the code using it does not change. On touch screens the native picker is better and stays.

    const DROPDOWN_SEARCH_FROM = 10;
    let dropdown = null;

    function usesDropdown(s) {
        return !!s && !s.disabled && !s.multiple && s.size <= 1 && matchMedia('(hover: hover) and (pointer: fine)').matches;
    }

    function closeDropdown(refocus) {
        if (!dropdown) return;
        const {panel, target, timer} = dropdown;
        dropdown = null;
        clearInterval(timer);
        panel.remove();
        target.removeAttribute('aria-expanded');
        if (refocus && target.isConnected) target.focus();
    }

    function chooseOption(opt) {
        const s = dropdown.target;
        const changed = s.value !== opt.value;
        closeDropdown(true);
        if (!changed) return;
        s.value = opt.value;
        s.dispatchEvent(new Event('input', {bubbles: true}));
        s.dispatchEvent(new Event('change', {bubbles: true}));
    }

    function setActive(i) {
        const d = dropdown, rows = d.rows;
        if (!rows.length) return;
        d.active = Math.max(0, Math.min(rows.length - 1, i));
        rows.forEach((r, n) => r.el.classList.toggle('active', n === d.active));
        rows[d.active].el.scrollIntoView({block: 'nearest'});
    }

    function paintDropdown(query) {
        const d = dropdown, s = d.target, q = norm(query);
        const opts = [...s.options].filter(o => !o.hidden && !o.disabled && (!q || norm(o.textContent).includes(q)));
        d.rows = opts.map(o => {
            const code = o.dataset.flag, picture = o.dataset.pic && o.dataset.pic.split('/');
            const el = h('div.dd-item' + (o.selected ? '.selected' : ''), {role: 'option', 'aria-selected': String(o.selected)},
                code ? flag(code) : picture ? pic(picture[0], picture[1]) : null,
                h('span.dd-label', o.textContent),
                o.selected ? icon('check') : null);
            return {el, option: o};
        });
        d.rows.forEach((r, i) => {
            r.el.addEventListener('mousemove', () => {
                if (d.active !== i) setActive(i);
            });
            r.el.addEventListener('click', () => chooseOption(r.option));
        });
        d.list.replaceChildren(...(d.rows.length ? d.rows.map(r => r.el) : [h('div.dd-empty', t('No results'))]));
        const chosen = d.rows.findIndex(r => r.option.selected);
        setActive(chosen >= 0 ? chosen : 0);
    }

    /** Puts a floating list under its field, or above it when there is more room there. */
    function placePanel(panel, target) {
        const r = target.getBoundingClientRect(), gap = 6, margin = 8;
        const below = innerHeight - r.bottom - gap - margin, above = r.top - gap - margin;
        const up = below < 220 && above > below;
        panel.style.minWidth = r.width + 'px';
        panel.style.maxHeight = Math.min(340, up ? above : below) + 'px';
        const w = panel.offsetWidth;
        panel.style.left = Math.max(margin, Math.min(r.left, innerWidth - w - margin)) + 'px';
        panel.style.top = up ? 'auto' : r.bottom + gap + 'px';
        panel.style.bottom = up ? innerHeight - r.top + gap + 'px' : 'auto';
    }

    function openDropdown(s) {
        closeDropdown();
        const list = h('div.dd-list', {role: 'listbox', 'aria-label': s.getAttribute('aria-label') || ''});
        const search = s.options.length >= DROPDOWN_SEARCH_FROM ? h('input.dd-search', {
            type: 'search',
            placeholder: t('Search'),
            'aria-label': t('Search'),
            autocomplete: 'off',
            oninput: e => paintDropdown(e.target.value)
        }) : null;
        const panel = h('div.dd', search, list);
        dropdown = {
            target: s, panel, list, search, rows: [], active: 0, typed: '', typedAt: 0,
            timer: setInterval(() => {
                if (!s.isConnected) closeDropdown(); // The page was redrawn under the list
            }, 400)
        };
        s.setAttribute('aria-expanded', 'true');
        document.body.appendChild(panel);
        paintDropdown('');
        placePanel(panel, s);
        if (dropdown.rows[dropdown.active]) dropdown.rows[dropdown.active].el.scrollIntoView({block: 'nearest'});
        if (search) search.focus();
    }

    /** Without a search box, typing jumps to the first option starting with those letters. */
    function typeAhead(ch) {
        const d = dropdown;
        d.typed = (Date.now() - d.typedAt < 700 ? d.typed : '') + ch;
        d.typedAt = Date.now();
        const i = d.rows.findIndex(r => norm(r.option.textContent).startsWith(norm(d.typed)));
        if (i >= 0) setActive(i);
    }

    document.addEventListener('mousedown', e => {
        const s = e.target.closest ? e.target.closest('select') : null;
        if (dropdown && !dropdown.panel.contains(e.target)) {
            const same = dropdown.target === s;
            closeDropdown();
            if (same) {
                e.preventDefault();
                return;
            }
        }
        if (e.button !== 0 || !usesDropdown(s)) return;
        e.preventDefault();
        s.focus();
        openDropdown(s);
    }, true);

    document.addEventListener('keydown', e => {
        if (!dropdown) {
            const s = e.target;
            if (s.tagName === 'SELECT' && usesDropdown(s) && ['Enter', ' ', 'ArrowDown', 'ArrowUp'].includes(e.key)) {
                e.preventDefault();
                openDropdown(s);
            }
            return;
        }
        const d = dropdown, page = 8;
        const moves = {ArrowDown: d.active + 1, ArrowUp: d.active - 1, PageDown: d.active + page, PageUp: d.active - page};
        if (!d.search) Object.assign(moves, {Home: 0, End: d.rows.length - 1});
        if (e.key in moves) setActive(moves[e.key]);
        else if (e.key === 'Enter') {
            if (d.rows[d.active]) chooseOption(d.rows[d.active].option);
        } else if (e.key === 'Escape') closeDropdown(true);
        else if (e.key === 'Tab') {
            closeDropdown(true);
            return;
        } else {
            if (!d.search && e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
                typeAhead(e.key);
                e.preventDefault();
                e.stopPropagation();
            }
            return; // Typing in the search box
        }
        e.preventDefault();
        e.stopPropagation(); // Esc must close the list, not the sheet under it
    }, true);

    document.addEventListener('change', e => {
        if (e.target.tagName === 'SELECT') refreshSelectFlag(e.target);
    });
    window.addEventListener('scroll', e => {
        if (dropdown && !dropdown.panel.contains(e.target)) closeDropdown();
    }, true);
    window.addEventListener('resize', () => closeDropdown());
    window.addEventListener('popstate', () => closeDropdown());
    window.addEventListener('hashchange', () => closeDropdown());

    /** Storage environments of products and spaces, coldest first. */
    function environments() {
        return [
            {value: 'cold', label: t('Cold (freezer)'), pic: ['spaces', 'freezer']},
            {value: 'cool', label: t('Cool (fridge)'), pic: ['spaces', 'fridge']},
            {value: 'ambient', label: t('Room temperature'), pic: ['spaces', 'pantry']},
            {value: 'warm', label: t('Warm'), pic: ['spaces', 'microwave']},
        ];
    }

    function environmentLabel(env) {
        const e = environments().find(x => x.value === env);
        return e ? e.label : '';
    }

    // ---------- Bottom sheets (with Android back button support) ----------

    const stack = [];
    let ignorePop = 0;
    let lastPointer = null;
    document.addEventListener('pointerdown', e => {
        if (e.pointerType === 'mouse') lastPointer = {x: e.clientX, y: e.clientY, at: Date.now()};
    }, true);

    function sheet(opts) {
        const handle = h('div.sheet-handle');
        const panel = h('div.sheet' + (opts.cls ? '.' + opts.cls : ''), {
                role: 'dialog',
                'aria-modal': 'true',
                'aria-label': opts.title || ''
            },
            handle,
            opts.title ? h('h2.sheet-title', opts.title) : null,
            h('div.sheet-body', opts.body || []),
            opts.actions && opts.actions.length ? h('div.sheet-actions', opts.actions) : null,
        );
        const backdrop = h('div.sheet-backdrop', {
            onclick: e => {
                if (e.target === backdrop && !opts.persistent) close();
            }
        }, panel);
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
        const entry = {close: () => doClose(false), persistent: !!opts.persistent};
        stack.push(entry);
        history.pushState({ftSheet: stack.length}, '');

        // Swipe down on the handle to close
        let startY = null;
        handle.addEventListener('pointerdown', e => {
            startY = e.clientY;
            handle.setPointerCapture(e.pointerId);
        });
        handle.addEventListener('pointermove', e => {
            if (startY !== null) panel.style.transform = `translateY(${Math.max(0, e.clientY - startY)}px)`;
        });
        handle.addEventListener('pointerup', e => {
            const d = e.clientY - (startY || 0);
            startY = null;
            panel.style.transform = '';
            if (d > 80) close();
        });

        function doClose(fromHistory) {
            if (closed) return;
            closed = true;
            const i = stack.indexOf(entry);
            if (i >= 0) stack.splice(i, 1);
            backdrop.classList.remove('open');
            setTimeout(() => {
                backdrop.remove();
                if (!stack.length) document.body.classList.remove('no-scroll');
            }, 220);
            if (!fromHistory) {
                ignorePop++;
                history.back();
            }
            if (opts.onclose) opts.onclose();
        }

        function close() {
            doClose(false);
        }

        entry.fromHistory = () => doClose(true);
        const first = panel.querySelector('input:not([type=checkbox]):not([readonly]), textarea, select');
        if (first && opts.autofocus !== false && !matchMedia('(pointer: coarse)').matches) setTimeout(() => first.focus(), 250);
        return {close, panel, body: panel.querySelector('.sheet-body')};
    }

    window.addEventListener('popstate', e => {
        if (ignorePop > 0) {
            ignorePop--;
            e.stopImmediatePropagation();
            return;
        }
        if (stack.length) {
            e.stopImmediatePropagation();
            stack[stack.length - 1].fromHistory();
        }
    });

    // Esc closes the topmost sheet (dialogs on desktop)
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && stack.length && !stack[stack.length - 1].persistent && !document.querySelector('.tour-ovl')) {
            e.preventDefault();
            closeTop();
        }
    });

    function closeTop() {
        if (stack.length) stack[stack.length - 1].close();
    }

    function closeAllSheets() {
        while (stack.length) stack[stack.length - 1].close();
    }

    /** Menu of actions as a bottom sheet (the "Altre azioni" pattern of the app). */
    function actionSheet(items) {
        const s = sheet({
            title: arguments[1] || null, cls: 'menu-sheet', popover: true,
            body: [h('div.menu', items.filter(Boolean).map(it => h('button.menu-item' + (it.danger ? '.danger' : '') + (it.selected ? '.selected' : ''), {
                type: 'button',
                onclick: () => {
                    s.close();
                    setTimeout(it.onclick, 230);
                }
            }, icon(it.icon), h('span', it.label), it.selected ? icon('check', 'trail') : null)))],
        });
        return s;
    }

    function confirm(title, text, okLabel, danger) {
        return new Promise(resolve => {
            let answered = false;
            const s = sheet({
                title, body: [h('p.sheet-text', text)],
                onclose: () => {
                    if (!answered) resolve(false);
                },
                actions: [
                    btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}),
                    btn(okLabel || t('Confirm'), {
                        kind: danger ? 'danger' : 'filled',
                        icon: danger ? 'delete' : 'check',
                        onclick: () => {
                            answered = true;
                            s.close();
                            resolve(true);
                        }
                    }),
                ],
            });
        });
    }

    // ---------- Snackbar ----------

    let toastTimer = null;

    function toast(text, kind, action) {
        let t = document.getElementById('toast');
        if (!t) {
            t = h('div#toast', {role: 'status', 'aria-live': 'polite'});
            document.body.appendChild(t);
        }
        clear(t);
        t.className = 'show' + (kind ? ' ' + kind : '');
        t.appendChild(h('span', text));
        if (action) t.appendChild(h('button', {
            type: 'button', onclick: () => {
                t.className = '';
                action.onclick();
            }
        }, action.label));
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            t.className = '';
        }, action ? 6000 : 3200);
    }

    // ---------- Dates ----------

    function todayStr() {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function parseDate(s) {
        const [y, m, d] = s.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function dateStr(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function addDays(s, n) {
        const d = parseDate(s);
        d.setDate(d.getDate() + n);
        return dateStr(d);
    }

    function daysUntil(s) {
        return Math.round((parseDate(s) - parseDate(todayStr())) / 86400000);
    }

    function fmtDate(s) {
        if (!s) return '';
        const [y, m, d] = s.split('-');
        return `${d}/${m}/${y}`;
    }

    function relDays(n) {
        if (n < -1) return t('expired {n} days ago', {n: -n});
        if (n === -1) return t('expired yesterday');
        if (n === 0) return t('expires today');
        if (n === 1) return t('tomorrow');
        return t('in {n} days', {n});
    }

    const locale = () => (window.I18N ? I18N.locale : 'it-IT');

    /** "Monday 3 November" in the user's language. */
    function longDate(s) {
        return parseDate(s).toLocaleDateString(locale(), {weekday: 'long', day: 'numeric', month: 'long'});
    }

    function fmtDateTime(s) {
        if (!s) return '—';
        const d = new Date(s.replace(' ', 'T') + 'Z');
        return d.toLocaleDateString(locale()) + ' ' + d.toLocaleTimeString(locale(), {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // ---------- Quantities ----------

    const UNIT_BASE = {g: ['g', 1], kg: ['g', 1000], ml: ['ml', 1], cl: ['ml', 10], dl: ['ml', 100], l: ['ml', 1000]};

    function toBase(qty, unit) {
        const b = UNIT_BASE[unit];
        return b ? {base: b[0], value: qty * b[1]} : {base: unit, value: qty};
    }

    function fmtNum(n) {
        return (Math.round(n * 1000) / 1000).toLocaleString(locale(), {maximumFractionDigits: 3});
    }

    /** Display label of a stored unit (stored values are language independent). */
    const UNIT_EN = {pz: 'pcs', conf: 'pack', cucchiai: 'tbsp', cucchiaini: 'tsp', 'q.b.': 'to taste'};

    const UNIT_ONE = {cucchiai: 'cucchiaio', cucchiaini: 'cucchiaino'};

    function unitLabel(u) {
        return window.I18N && I18N.lang !== 'it' && UNIT_EN[u] ? UNIT_EN[u] : u;
    }

    /**
     * Quantity in the unit chosen in the settings. Only for display: the data keeps the unit it was saved with.
     * Preference: '' = as entered, 'auto' = the unit that reads best, or a unit (g, kg, ml, cl, dl, l).
     */
    function preferredQty(qty, unit) {
        const b = UNIT_BASE[unit];
        const pref = b ? store.get(b[0] === 'g' ? 'units.weight' : 'units.volume', '') : '';
        if (!pref) return {qty, unit};
        const value = qty * b[1];
        const to = pref !== 'auto' ? pref : value >= 1000 ? (b[0] === 'g' ? 'kg' : 'l') : b[0];
        return {qty: value / UNIT_BASE[to][1], unit: to};
    }

    function fmtQty(qty, unit) {
        if (unit === 'q.b.') return unitLabel('q.b.');
        if (qty === null || qty === undefined) return unit && unit !== 'pz' ? unitLabel(unit) : '';
        ({qty, unit} = preferredQty(qty, unit));
        if (qty === 1 && UNIT_ONE[unitLabel(unit)]) return '1 ' + UNIT_ONE[unitLabel(unit)];
        return fmtNum(qty) + (unit ? ' ' + unitLabel(unit) : '');
    }

    function norm(s) {
        return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[’']/g, "'").trim();
    }

    // ---------- Countries (origin of the recipes) ----------

    const COUNTRY_CODES = 'AD AE AF AG AL AM AO AR AT AU AZ BA BB BD BE BF BG BH BI BJ BN BO BR BS BT BW BY BZ CA CD CF CG CH CI CL CM CN CO CR CU CV CY CZ DE DJ DK DM DO DZ EC EE EG ER ES ET FI FJ FR GA GB GD GE GH GM GN GQ GR GT GW GY HK HN HR HT HU ID IE IL IN IQ IR IS IT JM JO JP KE KG KH KM KN KP KR KW KZ LA LB LC LI LK LR LS LT LU LV LY MA MC MD ME MG MK ML MM MN MR MT MU MV MW MX MY MZ NA NE NG NI NL NO NP NZ OM PA PE PG PH PK PL PR PS PT PY QA RO RS RU RW SA SC SD SE SG SI SK SL SM SN SO SR SS SV SY SZ TD TG TH TJ TL TM TN TO TR TT TW TZ UA UG US UY UZ VA VE VN YE ZA ZM ZW'.split(' ');
    let regionNames = null;
    // UK nations (ISO 3166-2 codes): Intl has no names for them
    const NATIONS = {'GB-ENG': 'England', 'GB-SCT': 'Scotland', 'GB-WLS': 'Wales', 'GB-NIR': 'Northern Ireland'};

    function countryName(code) {
        if (NATIONS[code]) return t(NATIONS[code]);
        try {
            regionNames = regionNames || new Intl.DisplayNames([locale()], {type: 'region'});
            return regionNames.of(code) || code;
        } catch (e) {
            return code;
        }
    }

    /**
     * Flag of an ISO 3166 code as an SVG image (assets/flags, from the flag-icons project, MIT):
     * the same drawing on every device, unlike emoji flags (Windows shows only the letters).
     */
    function flag(code, cls) {
        if (!/^[A-Z]{2}(-[A-Z]{3})?$/.test(code || '')) return null;
        return h('img.flag' + (cls ? '.' + cls : ''), {
            src: 'assets/flags/' + code.toLowerCase() + '.svg',
            alt: '',
            width: 20,
            height: 15,
            loading: 'lazy',
            title: countryName(code)
        });
    }

    /** All countries sorted by name in the user's language. */
    function countryList() {
        return COUNTRY_CODES.concat(Object.keys(NATIONS)).map(c => ({
            code: c,
            name: countryName(c)
        })).sort((a, b) => a.name.localeCompare(b.name, locale()));
    }

    /** Cover of a recipe: its photo, or an illustration with the flags of its countries. */
    function recipeCover(r, cls) {
        if (r.image) return h('div.' + (cls || 'cover'), {style: {backgroundImage: `url("${r.image}")`}});
        const flags = (r.origin || []).slice(0, 3).map(c => flag(c, 'big'));
        const ill = h('div.' + (cls || 'cover') + '.cover-ill', h('span.ci-icon', icon('restaurant')), flags.length ? h('span.ci-flags', flags) : null);
        let hash = 0;
        for (const ch of String(r.name || '')) hash = (hash * 31 + ch.charCodeAt(0)) % 360;
        ill.style.setProperty('--hue', String(hash));
        return ill;
    }

    /** Flag + name of each country (only the flags when short and more than two), or "Not defined". */
    function originLabel(list, short) {
        if (!list || !list.length) return h('span.origin', t('Not defined'));
        if (short && list.length > 2) return h('span.origin', list.map(c => flag(c)));
        return h('span.origin', list.map((c, i) => h('span.oc', flag(c), countryName(c) + (i < list.length - 1 ? ',' : ''))));
    }

    /** Plain-text version (sorting, search). */
    function originText(list) {
        return (list || []).map(countryName).join(', ');
    }

    /** Generates a skeleton screen placeholder for views. */
    function skeletonScreen(kind = 'list') {
        const card = () => h('div.sk-card',
            h('span.skeleton.sk-tile'),
            h('div.sk-body', h('span.skeleton.sk-title'), h('span.skeleton.sk-line.w-60')),
            h('span.skeleton.sk-chip')
        );
        return h('div.sk-view',
            h('div.sk-appbar',
                h('div', {style: {display: 'flex', flexDirection: 'column', gap: '6px'}},
                    h('span.skeleton', {style: {width: '80px', height: '14px', borderRadius: '4px'}}),
                    h('span.skeleton', {style: {width: '140px', height: '24px', borderRadius: '6px'}})
                ),
                h('span.skeleton.sk-avatar')
            ),
            h('div.sk-banner',
                h('span.skeleton.sk-tile'),
                h('div.sk-body', h('span.skeleton.sk-line.w-80'), h('span.skeleton.sk-line.w-40'))
            ),
            h('div.sk-stack', card(), card(), card(), card())
        );
    }

    // ---------- Storage (per device preferences) ----------

    const store = {
        get(k, def) {
            try {
                const v = localStorage.getItem('ft.' + k);
                return v === null ? def : JSON.parse(v);
            } catch (e) {
                return def;
            }
        },
        set(k, v) {
            try {
                localStorage.setItem('ft.' + k, JSON.stringify(v));
            } catch (e) { /* private mode */
            }
        },
    };

    function applyTheme() {
        const t = store.get('theme', 'auto');
        if (t === 'auto') document.documentElement.removeAttribute('data-theme');
        else document.documentElement.setAttribute('data-theme', t);
        const dark = t === 'dark' || (t === 'auto' && matchMedia('(prefers-color-scheme: dark)').matches);
        document.querySelectorAll('meta[name=theme-color]').forEach(m => {
            m.removeAttribute('media');
            m.setAttribute('content', dark ? '#0F120F' : '#F5F7F4');
        });
    }

    window.UI = {
        app,
        h,
        append,
        clear,
        icon,
        pic,
        iconNames,
        iconLabel,
        googleButton,
        tile,
        chip,
        fab,
        btn,
        iconBtn,
        busy,
        field,
        input,
        textarea,
        select,
        fillSelect,
        infiniteList,
        placePanel,
        environments,
        environmentLabel,
        sheet,
        actionSheet,
        confirm,
        closeTop,
        closeAllSheets,
        toast,
        todayStr,
        parseDate,
        dateStr,
        addDays,
        daysUntil,
        fmtDate,
        relDays,
        longDate,
        fmtDateTime,
        toBase,
        fmtNum,
        fmtQty,
        unitLabel,
        countryName,
        flag,
        countryList,
        originLabel,
        originText,
        recipeCover,
        norm,
        store,
        applyTheme,
        skeletonScreen,
        sheetsOpen: () => stack.length,
    };
})();
