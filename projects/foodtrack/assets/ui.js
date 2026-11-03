/* FoodTrack — UI helpers: DOM builder, icons, bottom sheets, snackbar, dates and units. */
(function () {
  'use strict';

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

  /** Picture icon of a product / space / list (black outline PNG, inverted in dark mode). */
  function pic(folder, name, cls) {
    return h('img.pic' + (cls ? '.' + cls : ''), { src: `assets/img/${folder}/${name || (folder === 'products' ? 'generic' : 'home')}.png`, alt: '', loading: 'lazy', onerror: e => { e.target.style.visibility = 'hidden'; } });
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
    catch (e) { toast(e.message || 'Si è verificato un errore.', 'error'); throw e; }
    finally { if (button) { button.disabled = false; button.classList.remove('loading'); } }
  }

  // ---------- Form fields (Material "outlined" text fields) ----------

  function field(label, input, opts) {
    opts = opts || {};
    input.classList.add('input');
    if (!input.id) input.id = 'f' + Math.random().toString(36).slice(2, 9);
    if (!input.placeholder) input.placeholder = ' ';
    return h('div.field' + (opts.cls ? '.' + opts.cls : ''), input, h('label', { for: input.id }, label), opts.hint ? h('p.hint', opts.hint) : null);
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

  function sheet(opts) {
    const handle = h('div.sheet-handle');
    const panel = h('div.sheet', { role: 'dialog', 'aria-modal': 'true', 'aria-label': opts.title || '' },
      handle,
      opts.title ? h('h2.sheet-title', opts.title) : null,
      h('div.sheet-body', opts.body || []),
      opts.actions && opts.actions.length ? h('div.sheet-actions', opts.actions) : null,
    );
    const backdrop = h('div.sheet-backdrop', { onclick: e => { if (e.target === backdrop && !opts.persistent) close(); } }, panel);
    document.body.appendChild(backdrop);
    document.body.classList.add('no-scroll');
    requestAnimationFrame(() => backdrop.classList.add('open'));

    let closed = false;
    const entry = { close: () => doClose(false) };
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

  function closeAllSheets() { while (stack.length) stack[stack.length - 1].close(); }

  /** Menu of actions as a bottom sheet (the "Altre azioni" pattern of the app). */
  function actionSheet(items) {
    const s = sheet({
      body: items.filter(Boolean).map(it => h('button.menu-item' + (it.danger ? '.danger' : ''), { type: 'button', onclick: () => { s.close(); setTimeout(it.onclick, 230); } }, icon(it.icon), h('span', it.label))),
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
          btn('Annulla', { kind: 'text', onclick: () => s.close() }),
          btn(okLabel || 'Conferma', { kind: danger ? 'danger' : 'filled', icon: danger ? 'delete' : 'check', onclick: () => { answered = true; s.close(); resolve(true); } }),
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
  function relDays(n) {
    if (n < -1) return `scaduto da ${-n} giorni`;
    if (n === -1) return 'scaduto ieri';
    if (n === 0) return 'scade oggi';
    if (n === 1) return 'domani';
    return `tra ${n} giorni`;
  }
  const WEEKDAYS = ['domenica', 'lunedì', 'martedì', 'mercoledì', 'giovedì', 'venerdì', 'sabato'];
  const MONTHS = ['gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'];
  function longDate(s) { const d = parseDate(s); return `${WEEKDAYS[d.getDay()]} ${d.getDate()} ${MONTHS[d.getMonth()]}`; }
  function fmtDateTime(s) {
    if (!s) return '—';
    const d = new Date(s.replace(' ', 'T') + 'Z');
    return d.toLocaleDateString('it-IT') + ' ' + d.toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
  }

  // ---------- Quantities ----------

  const UNIT_BASE = { g: ['g', 1], kg: ['g', 1000], ml: ['ml', 1], cl: ['ml', 10], l: ['ml', 1000] };
  function toBase(qty, unit) {
    const b = UNIT_BASE[unit];
    return b ? { base: b[0], value: qty * b[1] } : { base: unit, value: qty };
  }
  function fmtNum(n) { return (Math.round(n * 1000) / 1000).toLocaleString('it-IT', { maximumFractionDigits: 3 }); }
  function fmtQty(qty, unit) {
    if (unit === 'q.b.') return 'q.b.';
    if (qty === null || qty === undefined) return unit && unit !== 'pz' ? unit : '';
    return fmtNum(qty) + (unit ? ' ' + unit : '');
  }

  function norm(s) { return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[’']/g, "'").trim(); }

  // ---------- Storage (per device preferences) ----------

  const store = {
    get(k, def) { try { const v = localStorage.getItem('ft.' + k); return v === null ? def : JSON.parse(v); } catch (e) { return def; } },
    set(k, v) { try { localStorage.setItem('ft.' + k, JSON.stringify(v)); } catch (e) { /* private mode */ } },
  };

  function applyTheme() {
    const t = store.get('theme', 'auto');
    if (t === 'auto') document.documentElement.removeAttribute('data-theme');
    else document.documentElement.setAttribute('data-theme', t);
    const dark = t === 'dark' || (t === 'auto' && matchMedia('(prefers-color-scheme: dark)').matches);
    const meta = document.querySelector('meta[name=theme-color]');
    if (meta) meta.setAttribute('content', dark ? '#1B5E20' : '#4CAF50');
  }

  window.UI = {
    h, append, clear, icon, pic, btn, iconBtn, busy, field, input, textarea, select,
    sheet, actionSheet, confirm, closeAllSheets, toast,
    todayStr, parseDate, dateStr, addDays, daysUntil, fmtDate, relDays, longDate, fmtDateTime, WEEKDAYS, MONTHS,
    toBase, fmtNum, fmtQty, norm, store, applyTheme,
    sheetsOpen: () => stack.length,
  };
})();
