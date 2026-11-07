/* ThinkHop — small UI toolkit shared by the game and the account screens. */
(function () {
    'use strict';

    const store = {
        get(k, d) {
            try {
                const v = localStorage.getItem('thinkhop.' + k);
                return v === null ? d : JSON.parse(v);
            } catch (e) {
                return d;
            }
        },
        set(k, v) {
            try {
                localStorage.setItem('thinkhop.' + k, JSON.stringify(v));
            } catch (e) { /* private mode */
            }
        },
        del(k) {
            try {
                localStorage.removeItem('thinkhop.' + k);
            } catch (e) { /* private mode */
            }
        },
    };

    function h(tag, attrs, ...kids) {
        const m = /^([a-z0-9]+)?((?:[.#][\w-]+)*)$/i.exec(tag);
        const el = document.createElement(m[1] || 'div');
        m[2].replace(/([.#])([\w-]+)/g, (_, k, v) => {
            if (k === '.') el.classList.add(v); else el.id = v;
        });
        if (attrs && (attrs instanceof Node || Array.isArray(attrs) || typeof attrs !== 'object')) {
            kids.unshift(attrs);
            attrs = null;
        }
        for (const [k, v] of Object.entries(attrs || {})) {
            if (v == null || v === false) continue;
            if (k.startsWith('on')) el.addEventListener(k.slice(2), v);
            else if (k === 'html') el.innerHTML = v;
            else el.setAttribute(k, v === true ? '' : v);
        }
        for (const c of kids.flat(Infinity)) if (c != null && c !== false) el.append(c instanceof Node ? c : document.createTextNode(String(c)));
        return el;
    }

    const P = {
        back: '<path d="M15 18l-6-6 6-6"/>',
        lock: '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>',
        grid: '<rect x="3" y="3" width="7" height="7" rx="1.500"/><rect x="14" y="3" width="7" height="7" rx="1.500"/><rect x="3" y="14" width="7" height="7" rx="1.500"/><rect x="14" y="14" width="7" height="7" rx="1.500"/>',
        shield: '<path d="M12 2l8 3v6c0 5-3.400 9-8 11-4.600-2-8-6-8-11V5z"/>',
        play: '<path d="M6 4l14 8-14 8z"/>',
        bulb: '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7c.6.5 1 1.3 1 2.1V17h6v-.2c0-.8.4-1.6 1-2.1A7 7 0 0012 2z"/>',
        book: '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5v14zM20 17v4H6.5A2.5 2.5 0 014 18.5"/>',
        globe: '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>',
        moon: '<path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/>',
        trophy: '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0z"/><path d="M17 5h3v2a3 3 0 01-3 3M7 5H4v2a3 3 0 003 3"/>',
        tick: '<path d="M5 12l5 5L20 7"/>',
        close: '<path d="M18 6L6 18M6 6l12 12"/>',
        star: '<path d="M12 2.8l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.6l-5.8 3.1 1.1-6.5L2.6 9.6l6.5-.9z"/>',
        heart: '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>',
        bell: '<path d="M6 8a6 6 0 0112 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a2 2 0 003.4 0"/>',
        user: '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        chart: '<path d="M4 20V10M10 20V4M16 20v-8M22 20H2"/>',
        calendar: '<rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        flame: '<path d="M12 2c1 4 6 6.5 6 12a6 6 0 01-12 0c0-2.5 1.2-4 2.5-5 .2 1.8 1 2.8 2 3C10.500 8.500 10.500 5 12 2z"/>',
        arrow: '<path d="M5 12h14M13 6l6 6-6 6"/>',
        share: '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.500l6.800 4M15.400 6.500l-6.800 4"/>',
        check: '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
        magic: '<path d="M15 4V2M15 16v-2M8 9h2M20 9h2M17.8 11.8L19 13M15 9h0M17.8 6.2L19 5M3 21l9-9M12.2 6.2L11 5"/>',
        key: '<circle cx="8" cy="15" r="5"/><path d="M12 11l9-9M17 6l3 3"/>',
        exit: '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>',
        trash: '<path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>',
        pen: '<path d="M12 20h9"/><path d="M16.500 3.500a2.100 2.100 0 013 3L7 19l-4 1 1-4z"/>',
    };

    const icon = name => {
        const s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        s.setAttribute('viewBox', '0 0 24 24');
        s.setAttribute('class', 'i');
        s.setAttribute('aria-hidden', 'true');
        s.innerHTML = P[name];
        return s;
    };

    let toastTimer;

    function toast(msg, kind) {
        const el = document.getElementById('toast');
        el.textContent = msg;
        el.className = 'toast show' + (kind ? ' ' + kind : '');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            el.className = 'toast';
        }, 2600);
    }

    function modal(content, opts) {
        opts = opts || {};
        const box = h('div.box', {role: 'dialog', 'aria-modal': 'true'}, content);
        const m = h('div.modal', {
            onclick: e => {
                if (e.target === m && !opts.persistent) close();
            }
        }, box);
        document.body.appendChild(m);
        const key = e => {
            if (e.key === 'Escape' && !opts.persistent) close();
        };
        document.addEventListener('keydown', key, true);

        function close() {
            document.removeEventListener('keydown', key, true);
            m.remove();
            if (opts.onclose) opts.onclose();
        }

        return {close, box};
    }

    function confirmBox(title, text, okLabel, cancelLabel, danger) {
        return new Promise(res => {
            const m = modal([h('h3', title), h('p.muted', text), h('div.actions',
                h('button.btn.btn-ghost', {
                    type: 'button', onclick: () => {
                        m.close();
                        res(false);
                    }
                }, cancelLabel),
                h('button.btn' + (danger ? '.btn-danger' : '.btn-primary'), {
                    type: 'button', onclick: () => {
                        res(true); // before closing: closing the box answers "no"
                        m.close();
                    }
                }, okLabel))], {onclose: () => res(false)});
        });
    }

    window.ThinkHopUI = {store, h, icon, toast, modal, confirmBox};
})();
