/* FoodTrack — admin portal (administrators and master admin only). Desktop layout. */
(function () {
  'use strict';

  const t = I18N.t;
  const { h, icon, tile, chip, btn, iconBtn, busy, toast, norm } = UI;
  const root = document.getElementById('admin');
  let me = null;
  let config = { googleClientId: '' };
  let counts = {};

  // [route, label, icon, minimum role] — content moderators (5) only see recipes and products
  const ALL_TABS = [
    ['overview', 'Overview', 'dashboard', 9],
    ['recipes', 'Recipes', 'restaurant', 5],
    ['products', 'Products', 'spaces', 5],
    ['categories', 'Categories', 'category', 9],
    ['users', 'Users', 'person', 9],
    ['households', 'Households', 'home', 9],
  ];
  let TABS = ALL_TABS;
  const canDelete = () => me && me.isAdmin;
  const DIFFICULTY = { facile: 'Easy', media: 'Medium', difficile: 'Hard' };
  const LANG_NAMES = { it: 'Italiano', en: 'English' };
  const CONTENT_BASE = 'it';                  // language of the main content columns
  const CONTENT_LANGS = ['it', 'en'];

  const tabName = () => {
    const n = (location.hash.replace(/^#\/?/, '') || '').split('/')[0];
    return TABS.some(x => x[0] === n) ? n : TABS[0][0];
  };

  // ================================================================ components

  /** Labelled form field (desktop: label above the control). */
  function f(label, control, help) {
    if (!control.id) control.id = 'c' + Math.random().toString(36).slice(2, 9);
    return h('div.f', h('label', { for: control.id }, label), control, help ? h('div.help', help) : null);
  }
  const ctl = (tag, attrs) => h(tag + '.ctl', attrs || {});
  const inp = attrs => ctl('input', Object.assign({ type: 'text', autocomplete: 'off' }, attrs || {}));
  const area = attrs => ctl('textarea', Object.assign({ rows: 3 }, attrs || {}));
  function sel(options, value, attrs) {
    const s = ctl('select', attrs);
    for (const o of options) {
      const opt = h('option', { value: String(o.value) }, o.label);
      if (String(o.value) === String(value ?? '')) opt.selected = true;
      s.appendChild(opt);
    }
    return s;
  }
  function toggle(label, checked) {
    const cb = h('input', { type: 'checkbox', checked });
    const sw = h('span.switch' + (checked ? '.on' : ''));
    cb.addEventListener('change', () => sw.classList.toggle('on', cb.checked));
    return { el: h('label.switch-row', cb, sw, h('span', label)), get checked() { return cb.checked; } };
  }

  /** Centered modal dialog (or a right-side drawer). Returns { close, body }. */
  function dialog(opts) {
    const body = h('div.dlg-body', opts.body || []);
    const box = h('div.dlg' + (opts.size ? '.' + opts.size : ''), { role: 'dialog', 'aria-modal': 'true', 'aria-label': opts.title },
      h('div.dlg-head', h('h2', opts.title), iconBtn('close', t('Close'), () => close())),
      body,
      opts.actions && opts.actions.length ? h('div.dlg-foot', opts.actions) : null);
    const ovl = h('div.ovl' + (opts.drawer ? '.drawer' : ''), { onmousedown: e => { if (e.target === ovl && !opts.persistent) close(); } }, box);
    document.body.appendChild(ovl);
    document.body.classList.add('no-scroll');
    requestAnimationFrame(() => ovl.classList.add('open'));
    const onKey = e => { if (e.key === 'Escape' && !opts.persistent) close(); };
    document.addEventListener('keydown', onKey);
    let closed = false;
    function close() {
      if (closed) return;
      closed = true;
      document.removeEventListener('keydown', onKey);
      ovl.classList.remove('open');
      setTimeout(() => { ovl.remove(); if (!document.querySelector('.ovl')) document.body.classList.remove('no-scroll'); }, 180);
      if (opts.onclose) opts.onclose();
    }
    setTimeout(() => { const first = box.querySelector('.dlg-body input:not([type=checkbox]):not([type=file]), .dlg-body textarea, .dlg-body select'); if (first) first.focus(); }, 60);
    return { close, body, box };
  }

  function confirmDlg(title, text, okLabel, danger) {
    return new Promise(resolve => {
      let done = false;
      const d = dialog({
        title, size: 'sm', body: [h('p.text', text)], onclose: () => { if (!done) resolve(false); },
        actions: [btn(t('Cancel'), { kind: 'text', onclick: () => d.close() }),
          btn(okLabel || t('Confirm'), { kind: danger ? 'danger' : 'filled', onclick: () => { done = true; d.close(); resolve(true); } })],
      });
    });
  }

  /** Dropdown menu anchored to a button. */
  function menu(anchor, items) {
    document.querySelectorAll('.menu-pop').forEach(m => m.remove());
    const pop = h('div.menu-pop', { role: 'menu' }, items.filter(Boolean).map(it => it === '-' ? h('hr') :
      h('button' + (it.danger ? '.danger' : ''), { type: 'button', role: 'menuitem', onclick: () => { pop.remove(); it.onclick(); } }, icon(it.icon), it.label)));
    document.body.appendChild(pop);
    const r = anchor.getBoundingClientRect();
    const w = pop.offsetWidth, hgt = pop.offsetHeight;
    pop.style.left = Math.max(8, Math.min(r.right - w, innerWidth - w - 8)) + 'px';
    pop.style.top = (r.bottom + 4 + hgt > innerHeight ? r.top - hgt - 4 : r.bottom + 4) + 'px';
    setTimeout(() => document.addEventListener('mousedown', function away(e) { if (!pop.contains(e.target)) { pop.remove(); document.removeEventListener('mousedown', away); } }), 0);
  }
  const menuBtn = items => iconBtn('more', t('More actions'), e => menu(e.currentTarget, items));

  /**
   * Sortable data table.
   * cols: [{ key, label, sort?: row => value, num?, cls?, render: row => node }]
   */
  function dataTable(cols, rows, opts) {
    opts = opts || {};
    const state = opts.state || { key: opts.sortKey || null, dir: opts.sortDir || 1 };
    const thead = h('thead'), tbody = h('tbody');
    function paint() {
      const list = rows.slice();
      const c = cols.find(x => x.key === state.key);
      if (c && c.sort) list.sort((a, b) => { const A = c.sort(a), B = c.sort(b); return (typeof A === 'string' ? A.localeCompare(B, I18N.locale) : (A > B) - (A < B)) * state.dir; });
      UI.clear(thead).appendChild(h('tr', cols.map(col => h('th' + (col.sort ? '.sortable' : '') + (col.num ? '.num' : '') + (col.cls ? '.' + col.cls : ''), {
        onclick: col.sort ? () => { state.dir = state.key === col.key ? -state.dir : 1; state.key = col.key; paint(); } : null,
        'aria-sort': state.key === col.key ? (state.dir > 0 ? 'ascending' : 'descending') : null,
      }, col.label, col.sort ? h('span.arrow', state.key === col.key ? (state.dir > 0 ? ' ↑' : ' ↓') : '') : null))));
      UI.clear(tbody);
      for (const r of list) tbody.appendChild(h('tr' + (opts.onrow ? '.click' : ''), { onclick: opts.onrow ? e => { if (!e.target.closest('button, a, input')) opts.onrow(r); } : null },
        cols.map(col => h('td' + (col.num ? '.num' : '') + (col.cls ? '.' + col.cls : ''), col.render(r)))));
      if (!list.length) tbody.appendChild(h('tr', h('td', { colspan: cols.length }, h('div.dt-empty', opts.empty || t('Nothing to show')))));
    }
    paint();
    return h('div.table-card', h('div.table-scroll', h('table.dt', thead, tbody)));
  }

  function searchInput(value, placeholder, oninput) {
    const i = inp({ type: 'search', value, placeholder });
    i.addEventListener('input', () => oninput(i.value));
    return h('div.search', icon('search'), i);
  }

  function pageHead(title, sub, actions) {
    return h('div.page-head', h('div.titles', h('h1', title), sub ? h('p', sub) : null), actions ? h('div.actions', actions) : null);
  }

  /** Language tabs for translatable fields: renders fields for the selected content language. */
  function langTabs(render) {
    let cur = CONTENT_BASE;
    const holder = h('div');
    const tabs = h('div.lang-tabs', CONTENT_LANGS.map(l => h('button' + (l === cur ? '.sel' : ''), { type: 'button', onclick: e => {
      cur = l; tabs.querySelectorAll('button').forEach(b => b.classList.toggle('sel', b === e.currentTarget)); paintL();
    } }, LANG_NAMES[l] + (l === CONTENT_BASE ? ' · ' + t('main') : ''))));
    function paintL() { UI.clear(holder); holder.appendChild(render(cur)); }
    paintL();
    return h('div', tabs, holder);
  }

  /** Two-way binding for a translatable text: base value + translations object ({en: {...}}). */
  function trField(model, i18n, key, field, label, multiline, help) {
    return lang => {
      const isBase = lang === CONTENT_BASE;
      const cur = isBase ? model[key] : ((i18n[lang] || {})[field] || '');
      const c = multiline ? area({ value: cur || '', rows: multiline }) : inp({ value: cur || '' });
      if (!isBase) c.placeholder = model[key] || '';
      c.addEventListener('input', () => {
        if (isBase) model[key] = c.value;
        else { i18n[lang] = i18n[lang] || {}; i18n[lang][field] = c.value; }
      });
      return f(label + (isBase ? '' : ' (' + LANG_NAMES[lang] + ')'), c, isBase ? help : t('Leave empty to use the Italian text.'));
    };
  }
  const STATUS_LABEL = { published: 'Published', draft: 'Draft', none: 'Not translated' };
  /** Per-language publication badges of a recipe: green = published, amber = draft, grey = not translated. */
  const statusFlags = r => h('span.flag', CONTENT_LANGS.map(l => { const st = (r.status || {})[l] || 'none'; return h('span.' + (st === 'published' ? 'on' : st === 'draft' ? 'draft' : 'off'), { title: LANG_NAMES[l] + ': ' + t(STATUS_LABEL[st]) }, l.toUpperCase()); }));
  const anyPublished = r => CONTENT_LANGS.some(l => (r.status || {})[l] === 'published');
  const translatedFlags = (row, fields) => h('span.flag', CONTENT_LANGS.map(l => h('span' + (l === CONTENT_BASE || fields.every(fl => ((row.i18n || {})[l] || {})[fl]) ? '.on' : ''), l.toUpperCase())));

  // ================================================================ shell

  function adminSkeleton() {
    const row = () => h('div.sk-card', { style: { height: '54px' } },
      h('span.skeleton.sk-tile', { style: { width: '32px', height: '32px' } }),
      h('div.sk-body', h('span.skeleton.sk-title', { style: { width: '35%' } }), h('span.skeleton.sk-line.w-60')),
      h('span.skeleton.sk-chip')
    );
    return h('div',
      h('div.page-head', h('div.titles', h('span.skeleton', { style: { width: '180px', height: '32px', borderRadius: '8px' } }), h('span.skeleton.sk-line.w-40', { style: { marginTop: '8px' } }))),
      h('div.toolbar', h('span.skeleton', { style: { width: '260px', height: '44px', borderRadius: '999px' } })),
      h('div.sk-stack', row(), row(), row(), row(), row())
    );
  }

  function shell(content) {
    const tab = tabName();
    const curTheme = UI.store.get('theme', 'auto');
    const setTheme = k => {
      UI.store.set('theme', k);
      UI.applyTheme();
      document.querySelectorAll('.sn-theme-tab').forEach(b => {
        const isMatch = b.dataset.themeKey === k;
        b.classList.toggle('active', isMatch);
        b.setAttribute('aria-selected', isMatch ? 'true' : 'false');
      });
      toast(t('Theme') + ': ' + t({ auto: 'Automatic', light: 'Light', dark: 'Dark' }[k]));
    };
    const cycleTheme = () => {
      const next = { auto: 'light', light: 'dark', dark: 'auto' }[UI.store.get('theme', 'auto')];
      setTheme(next);
    };

    // Desktop sidebar (same style as FoodTrack app)
    const side = h('nav.sidenav.adm-side', { 'aria-label': t('Admin portal') },
      h('a.sn-brand', { href: '#/' + TABS[0][0] },
        h('img', { src: 'assets/icons/icon-color.png', alt: '' }),
        h('div.sn-brand-titles',
          h('strong', 'FoodTrack'),
          h('small', t('Admin portal'))
        )
      ),
      h('div.sn-links',
        TABS.map(([k, label, ic]) =>
          h('a.sn-link' + (k === tab ? '.active' : ''), { href: '#/' + k, title: t(label), 'aria-current': k === tab ? 'page' : null },
            icon(ic),
            h('span', t(label)),
            counts[k] != null ? h('span.sn-badge', String(counts[k])) : null
          )
        )
      ),
      h('div.grow'),
      // Back to the FoodTrack app (mirrors .sn-admin in FoodTrack app)
      h('a.sn-link.sn-admin.sn-app-link', { href: './', title: t('Open the app') },
        icon('home'),
        h('span', t('Go to the app')),
        icon('right')
      ),
      // Theme segmented tabs: Auto | Light | Dark
      h('div.sn-theme-tabs', { role: 'tablist', 'aria-label': t('Theme') },
        [
          ['auto', t('Auto') || 'Auto'],
          ['light', t('Light')],
          ['dark', t('Dark')],
        ].map(([k, label]) =>
          h('button.sn-theme-tab' + (curTheme === k ? '.active' : ''), {
            type: 'button',
            role: 'tab',
            'aria-selected': curTheme === k ? 'true' : 'false',
            'data-theme-key': k,
            onclick: () => setTheme(k),
          }, label)
        )
      ),
      // Language selector
      h('div.sn-lang',
        sel(I18N.langs.map(([v, l]) => ({ value: v, label: l })), I18N.lang, { onchange: e => I18N.set(e.target.value), 'aria-label': t('Language') })
      ),
      h('button.sn-user.adm-user-btn', {
        type: 'button',
        title: (me ? me.name : '') + ' (' + (me ? me.roleLabel : '') + ')',
        onclick: e => menu(e.currentTarget, [
          { icon: 'home', label: t('Open the app'), onclick: () => { location.href = './'; } },
          { icon: 'theme', label: t('Theme'), onclick: cycleTheme },
          ...I18N.langs.map(([v, l]) => ({ icon: 'globe', label: l + (I18N.lang === v ? ' ✓' : ''), onclick: () => I18N.set(v) })),
          '-',
          { icon: 'logout', label: t('Sign out'), danger: true, onclick: logout },
        ]),
      },
        h('span.avatar', initials(me ? me.name : '')),
        h('span.grow',
          h('strong', me ? me.name : ''),
          h('small', me ? me.roleLabel : '')
        ),
        icon('more')
      )
    );

    // Mobile topbar
    const topbar = h('header.adm-topbar',
      h('div.adm-topbar-inner',
        h('a.adm-logo', { href: '#/' + TABS[0][0] },
          h('img', { src: 'assets/icons/icon-color.png', alt: '' }),
          h('span', 'FoodTrack', h('small', t('Admin portal')))
        ),
        h('div.adm-tools',
          h('a.adm-app-link', { href: './', title: t('Open the app') }, icon('home'), h('span', t('Go to the app'))),
          sel(I18N.langs.map(([v, l]) => ({ value: v, label: l })), I18N.lang, { onchange: e => I18N.set(e.target.value), 'aria-label': t('Language') }),
          iconBtn('theme', t('Theme'), cycleTheme),
          h('button.adm-user', {
            type: 'button',
            title: (me ? me.name : '') + ' (' + (me ? me.roleLabel : '') + ')',
            onclick: e => menu(e.currentTarget, [
              { icon: 'home', label: t('Open the app'), onclick: () => { location.href = './'; } },
              { icon: 'theme', label: t('Theme'), onclick: cycleTheme },
              ...I18N.langs.map(([v, l]) => ({ icon: 'globe', label: l + (I18N.lang === v ? ' ✓' : ''), onclick: () => I18N.set(v) })),
              '-',
              { icon: 'logout', label: t('Sign out'), danger: true, onclick: logout },
            ]),
          },
            h('span.avatar', initials(me ? me.name : '')),
            h('div.who', h('strong', me ? me.name : ''), h('span', me ? me.roleLabel : ''))
          )
        )
      )
    );

    // Mobile bottom navigation
    const bottomNav = h('nav.adm-bottomnav', { 'aria-label': t('Admin portal') },
      TABS.map(([k, label, ic]) =>
        h('a' + (k === tab ? '.active' : ''), { href: '#/' + k, title: t(label), 'aria-current': k === tab ? 'page' : null },
          h('span.pill', icon(ic)),
          h('span.lbl', t(label))
        )
      )
    );

    root.replaceChildren(h('div.adm', side, topbar, h('main.adm-content', content), bottomNav));
    document.title = t((TABS.find(x => x[0] === tab) || TABS[0])[1]) + ' · FoodTrack Admin';
  }

  function initials(name) { return String(name || '?').trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase(); }

  async function render() {
    shell(adminSkeleton());
    const views = { overview: vOverview, recipes: vRecipes, products: vProducts, categories: vCategories, users: vUsers, households: vHouseholds };
    try { shell(await (views[tabName()] || vOverview)()); }
    catch (e) { shell(h('div.err-box', icon('error'), h('span', e.message))); }
  }

  // ================================================================ overview

  async function vOverview() {
    const d = await Api.get('admin.overview');
    const s = d.stats;
    counts = { recipes: s.recipes, products: s.products, users: s.users, households: s.households };
    const kpi = (ic, n, l, sub) => h('div.kpi', tile('icon', ic), h('div', h('div.n', String(n)), h('div.l', l), sub ? h('div.s', sub) : null));
    const bars = (rows, label) => {
      const max = Math.max(1, ...rows.map(r => Number(r.n)));
      return rows.length ? h('div.bars', rows.map(r => h('div.bar', h('span', r.name), h('strong', label(r.n)), h('div.track', h('i', { style: { width: (100 * r.n / max) + '%' } }))))) : h('p.muted', t('No data yet.'));
    };
    return h('div',
      pageHead(t('Overview'), t('Aggregated numbers: the portal never shows the content of users’ spaces and lists.')),
      h('div.kpis',
        kpi('person', s.users, t('Registered users'), t('+{n} in the last 7 days', { n: s.usersNew7 })),
        kpi('timer', s.usersActive30, t('Active users (30 days)')),
        kpi('shield', s.admins, t('Administrators'), t('including the master admin') + (s.editors ? ' · ' + t('{n} content moderators', { n: s.editors }) : '')),
        kpi('home', s.households, t('Households'), t('{n} shared by several users', { n: s.sharedHouseholds })),
        kpi('spaces', s.items, t('Products in spaces'), t('{n} expired', { n: s.expired })),
        kpi('list', s.lists, t('Shopping lists')),
        kpi('restaurant', s.recipes, t('Recipes'), t('{n} published', { n: s.recipesPublished })),
        kpi('category', s.products, t('Catalogue products')),
        kpi('heart', s.favorites, t('Recipes saved as favourites'))),
      h('div.panels',
        h('div.panel', h('h3', t('Most loved recipes')), bars(d.topRecipes, n => n + ' ♥')),
        h('div.panel', h('h3', t('Most common products in spaces')), bars(d.topProducts, n => String(n)))));
  }

  // ================================================================ recipes

  const recipeState = { q: '', status: '', sort: { key: 'name', dir: 1 } };
  async function vRecipes() {
    const d = await Api.get('admin.recipes');
    counts.recipes = d.recipes.length;
    const table = h('div');
    function paint() {
      const n = norm(recipeState.q);
      const rows = d.recipes.filter(r => (!n || norm(r.name).includes(n) || norm((r.i18n.en || {}).name || '').includes(n))
        && (!recipeState.status || (recipeState.status === 'pub') === anyPublished(r)));
      table.replaceChildren(dataTable([
        { key: 'img', label: '', render: r => UI.recipeCover(r, 'thumb') },
        { key: 'name', label: t('Recipe'), sort: r => r.name, render: r => [h('div.title', r.name), h('div.sub', (r.i18n.en || {}).name ? [UI.flag('GB'), ' ', r.i18n.en.name] : r.description)] },
        { key: 'ing', label: t('Content'), sort: r => r.ingredients.length, render: r => t('{a} ingredients · {b} steps', { a: r.ingredients.length, b: r.steps.length }) },
        { key: 'origin', label: t('Origin'), sort: r => UI.originText(r.origin), render: r => r.origin && r.origin.length ? UI.originLabel(r.origin, true) : h('span.muted', '—') },
        { key: 'time', label: t('Time'), num: true, sort: r => (r.prepMinutes || 0) + (r.cookMinutes || 0), render: r => ((r.prepMinutes || 0) + (r.cookMinutes || 0)) + ' min' },
        { key: 'lang', label: t('Publication'), sort: r => CONTENT_LANGS.filter(l => (r.status || {})[l] === 'published').length, render: statusFlags },
        { key: 'fav', label: '♥', num: true, sort: r => d.favorites[r.id] || 0, render: r => String(d.favorites[r.id] || 0) },
        { key: 'act', label: '', cls: 'actions', render: r => [iconBtn('edit', t('Edit'), () => openRecipe(r, d)), menuBtn([
          { icon: 'edit', label: t('Edit'), onclick: () => openRecipe(r, d) },
          { icon: 'copy', label: t('Duplicate'), onclick: () => {
            const c = JSON.parse(JSON.stringify(r));
            c.id = 0; c.name = r.name + ' (' + t('copy') + ')'; c.published = false;
            c.status = Object.fromEntries(Object.entries(c.status || {}).map(([l, st]) => [l, st === 'none' ? 'none' : 'draft']));
            openRecipe(c, d);
          } },
          canDelete() ? '-' : null,
          canDelete() ? { icon: 'delete', label: t('Delete'), danger: true, onclick: () => deleteRecipe(r) } : null,
        ])] },
      ], rows, { state: recipeState.sort, onrow: r => openRecipe(r, d), empty: t('No recipes') }));
    }
    paint();
    return h('div',
      pageHead(t('Recipes'), t('Recipes shown to every user, with ingredients linked to the catalogue so the app can tell what they already have.'), [btn(t('New recipe'), { icon: 'add', onclick: () => openRecipe(null, d) })]),
      h('div.toolbar', searchInput(recipeState.q, t('Search recipe'), v => { recipeState.q = v; paint(); }),
        sel([{ value: '', label: t('All statuses') }, { value: 'pub', label: t('Published in at least one language') }, { value: 'draft', label: t('Not published in any language') }], recipeState.status, { onchange: e => { recipeState.status = e.target.value; paint(); } }),
        h('span.meta', t('{n} recipes', { n: d.recipes.length }))),
      table);
  }

  function saveRecipe(r) {
    return Api.post('admin.recipe.save', {
      id: r.id, name: r.name, description: r.description, image: r.image, prepMinutes: r.prepMinutes, cookMinutes: r.cookMinutes, cookNotes: r.cookNotes,
      servings: r.servings, difficulty: r.difficulty, origin: r.origin || [], published: r.published, i18n: r.i18n, steps: r.steps,
      ingredients: r.ingredients.map(i => ({ productId: i.productId, name: i.name, quantity: i.quantity, unit: i.unit, notes: i.notes, i18n: i.i18n })),
    });
  }

  async function deleteRecipe(r) {
    if (!await confirmDlg(t('Delete the recipe?'), t('“{name}” will be deleted, also from users’ favourites and meal plans.', { name: r.name }), t('Delete'), true)) return;
    try { await Api.post('admin.recipe.delete', { id: r.id }); toast(t('Recipe deleted')); render(); } catch (e) { toast(e.message, 'error'); }
  }

  function openRecipe(src, d) {
    const r = src ? JSON.parse(JSON.stringify(src)) : { id: 0, name: '', description: '', image: '', prepMinutes: 15, cookMinutes: null, cookNotes: '', servings: 2, difficulty: 'facile', origin: [], published: true, ingredients: [], steps: [], i18n: {} };
    r.i18n = (r.i18n && !Array.isArray(r.i18n)) ? r.i18n : {};
    if (!r.i18n.en) r.i18n.en = {};
    r.ingredients.forEach(i => { if (!i.i18n || Array.isArray(i.i18n)) i.i18n = {}; });
    const prodByName = name => d.products.find(p => norm(p.name) === norm(name));
    const datalist = h('datalist#ft-prod', d.products.map(p => h('option', { value: p.name })));

    // --- Details
    const prep = inp({ type: 'number', min: 0, value: r.prepMinutes ?? '' });
    const cook = inp({ type: 'number', min: 0, value: r.cookMinutes ?? '' });
    const servings = inp({ type: 'number', min: 1, max: 50, value: r.servings });
    const diff = sel(Object.entries(DIFFICULTY).map(([v, l]) => ({ value: v, label: t(l) })), r.difficulty);
    const origin = countryPicker(r.origin);
    // Publication status per language: base language published/draft, the others also "not translated"
    r.status = Object.assign({}, r.status || {});
    for (const l of CONTENT_LANGS) if (!r.status[l]) r.status[l] = l === CONTENT_BASE ? (r.published ? 'published' : 'draft') : ((r.i18n[l] || {}).name ? ((r.i18n[l] || {}).status || 'draft') : 'none');
    const statusSeg = lang => {
      const opts = ['published', 'draft'].concat(lang === CONTENT_BASE ? [] : ['none']);
      const wrap = h('div.status-seg', opts.map(st => h('button' + (r.status[lang] === st ? '.sel.' + st : ''), { type: 'button', onclick: () => { r.status[lang] = st; details.querySelector('.lang-tabs .sel').click(); } }, t(STATUS_LABEL[st]))));
      return h('div.f', h('span.lbl', t('Status in {lang}', { lang: LANG_NAMES[lang] })), wrap,
        h('div.help', r.status[lang] === 'published' ? t('Visible to users who use the app in this language.') : r.status[lang] === 'draft' ? t('Saved but hidden from users in this language.') : t('Not translated: the recipe is not shown to users in this language.')));
    };
    const prev = h('div.prev');
    const paintImg = () => { prev.style.backgroundImage = r.image ? `url("${r.image}")` : 'none'; UI.clear(prev); if (!r.image) prev.appendChild(icon('image')); };
    paintImg();
    const file = h('input', { type: 'file', accept: 'image/jpeg,image/png,image/webp', hidden: true });
    const up = btn(t('Upload image'), { kind: 'tonal', icon: 'upload', onclick: () => file.click() });
    file.addEventListener('change', async () => {
      if (!file.files[0]) return;
      const fd = new FormData(); fd.append('image', file.files[0]);
      try { const res = await busy(up, () => Api.upload('admin.recipe.image', fd)); r.image = res.image; paintImg(); toast(t('Image uploaded: remember to save the recipe')); } catch (e) { /* toast already shown */ }
      file.value = '';
    });
    const details = h('div',
      langTabs(lang => r.status[lang] === 'none' ? h('div', statusSeg(lang)) : h('div',
        statusSeg(lang),
        trField(r, r.i18n, 'name', 'name', t('Name'))(lang),
        trField(r, r.i18n, 'description', 'description', t('Short description'), 2)(lang),
        trField(r, r.i18n, 'cookNotes', 'cook_notes', t('Cooking notes'), 0, t('For example: “Conventional oven, 180 °C for 45 minutes”'))(lang))),
      h('div.f', h('span.lbl', t('Image')), h('div.img-drop', prev, h('div', h('div.row', up, btn(t('Remove'), { kind: 'text', onclick: () => { r.image = ''; paintImg(); } })), h('div.help', t('JPG, PNG or WebP, up to 6 MB. It is resized automatically.'))), file)),
      h('div.f-row', f(t('Preparation (min)'), prep), f(t('Cooking (min)'), cook), f(t('Servings'), servings), f(t('Difficulty'), diff)),
      h('div.f', h('span.lbl', t('Countries of origin')), origin.el, h('div.help', t('Add one or more countries (for example for fusion dishes). With none, the origin is “not defined”.'))));

    // --- Ingredients
    const ingBody = h('tbody');
    function ingRow(ing) {
      const name = inp({ value: ing.name, list: 'ft-prod', placeholder: t('Catalogue product or free text') });
      const qty = inp({ type: 'number', min: 0, step: 'any', value: ing.quantity ?? '' });
      const unit = sel(d.units.concat(d.units.includes(ing.unit) ? [] : [ing.unit]).map(u => ({ value: u, label: UI.unitLabel(u) })), ing.unit || 'g');
      const notes = inp({ value: ing.notes || '', placeholder: t('Notes (Italian)') });
      const notesEn = inp({ value: (ing.i18n.en || {}).notes || '', placeholder: t('Notes (English)') });
      const linked = h('span');
      const paintLink = () => { const p = prodByName(name.value); linked.replaceChildren(p ? tile('products', p.icon, 'sm') : h('span.tile.tile-sm', { title: t('Not linked to the catalogue') }, icon('link'))); };
      paintLink();
      name.addEventListener('input', paintLink);
      name.addEventListener('change', () => { const p = prodByName(name.value); if (p && !qty.value) unit.value = p.unit; });
      const tr = h('tr', h('td.drag', linked), h('td', name), h('td', { style: { width: '90px' } }, qty), h('td', { style: { width: '120px' } }, unit), h('td', notes), h('td', notesEn),
        h('td.del', iconBtn('delete', t('Remove'), () => tr.remove())));
      tr._get = () => {
        const p = prodByName(name.value);
        return name.value.trim() ? { productId: p ? p.id : null, name: name.value.trim(), quantity: qty.value, unit: unit.value, notes: notes.value, i18n: notesEn.value.trim() ? { en: { notes: notesEn.value.trim() } } : {} } : null;
      };
      ingBody.appendChild(tr);
    }
    r.ingredients.forEach(ingRow);
    if (!r.ingredients.length) ingRow({ name: '', unit: 'g', i18n: {} });
    const ingredients = h('div',
      h('div.notice', icon('info'), h('span', t('Pick names from the catalogue (suggested while typing): linked ingredients show an icon and let the app check what users already have.'))),
      datalist,
      h('table.ed-table', h('thead', h('tr', h('th', ''), h('th', t('Ingredient')), h('th', t('Qty')), h('th', t('Unit')), h('th', t('Notes') + ' (IT)'), h('th', t('Notes') + ' (EN)'), h('th', ''))), ingBody),
      h('div', { style: { marginTop: '8px' } }, btn(t('Add ingredient'), { kind: 'text', icon: 'add', onclick: () => ingRow({ name: '', unit: 'g', i18n: {} }) })));

    // --- Steps (per language)
    const stepsBy = { it: r.steps.slice(), en: (r.i18n.en.steps || []).slice() };
    const steps = langTabs(lang => {
      const wrap = h('div');
      const list = stepsBy[lang];
      function paintS() {
        UI.clear(wrap);
        if (lang !== CONTENT_BASE) wrap.appendChild(h('div.notice', icon('info'), h('span', t('Leave the steps empty to show the Italian ones.'))));
        list.forEach((text, i) => {
          const ta = area({ value: text, rows: 2, placeholder: lang !== CONTENT_BASE ? (stepsBy.it[i] || '') : '' });
          ta.addEventListener('input', () => { list[i] = ta.value; });
          wrap.appendChild(h('table.ed-table', h('tbody', h('tr', h('td.drag', String(i + 1)), h('td', ta), h('td.del',
            iconBtn('up', t('Move up'), () => { if (i > 0) { [list[i - 1], list[i]] = [list[i], list[i - 1]]; paintS(); } }),
            iconBtn('delete', t('Remove'), () => { list.splice(i, 1); paintS(); }))))));
        });
        wrap.appendChild(btn(t('Add step'), { kind: 'text', icon: 'add', onclick: () => { list.push(''); paintS(); setTimeout(() => { const all = wrap.querySelectorAll('textarea'); if (all.length) all[all.length - 1].focus(); }, 0); } }));
      }
      paintS();
      return wrap;
    });

    const sections = { details: [t('Details'), details], ingredients: [t('Ingredients'), ingredients], steps: [t('Method'), steps] };
    let cur = 'details';
    const holder = h('div');
    const tabsEl = h('div.tabs-h', Object.entries(sections).map(([k, [label]]) => h('button' + (k === cur ? '.sel' : ''), { type: 'button', onclick: e => {
      cur = k; tabsEl.querySelectorAll('button').forEach(b => b.classList.toggle('sel', b === e.currentTarget)); holder.replaceChildren(sections[k][1]);
    } }, label)));
    holder.appendChild(details);

    const save = btn(t('Save recipe'), { icon: 'save', onclick: async () => {
      if (!(r.name || '').trim()) { toast(t('Enter the recipe name.'), 'error'); return; }
      r.prepMinutes = prep.value; r.cookMinutes = cook.value; r.servings = servings.value; r.difficulty = diff.value; r.origin = origin.value; r.published = r.status[CONTENT_BASE] === 'published';
      for (const l of CONTENT_LANGS) {
        if (l === CONTENT_BASE) continue;
        if (r.status[l] === 'none') { r.i18n[l] = {}; continue; }
        r.i18n[l] = Object.assign(r.i18n[l] || {}, { status: r.status[l] });
        if (!(r.i18n[l].name || '').trim()) { toast(t('Enter the name in {lang}, or set it as “Not translated”.', { lang: LANG_NAMES[l] }), 'error'); return; }
      }
      r.ingredients = [...ingBody.children].map(tr => tr._get()).filter(Boolean);
      r.steps = stepsBy.it.filter(s => s.trim());
      if (r.status.en !== 'none') r.i18n.en.steps = stepsBy.en.filter(s => s.trim());
      const unlinked = r.ingredients.filter(i => !i.productId).map(i => i.name);
      await busy(save, () => saveRecipe(r));
      dlg.close();
      toast(unlinked.length ? t('Saved. Not linked to the catalogue: {list}', { list: unlinked.join(', ') }) : t('Recipe saved'));
      render();
    } });
    const dlg = dialog({
      title: r.id ? t('Edit recipe') : t('New recipe'), drawer: true, persistent: true,
      body: [tabsEl, holder],
      actions: [r.id && canDelete() ? btn(t('Delete'), { kind: 'danger-text', icon: 'delete', cls: 'left', onclick: () => { dlg.close(); deleteRecipe(r); } }) : null, btn(t('Cancel'), { kind: 'text', onclick: () => dlg.close() }), save].filter(Boolean),
    });
  }

  // ================================================================ products

  const prodState = { q: '', cat: '', sort: { key: 'name', dir: 1 } };
  async function vProducts() {
    const d = await Api.get('admin.catalog');
    counts.products = d.products.length;
    const catName = id => (d.categories.find(c => c.id === id) || {}).name || '—';
    const table = h('div');
    function paint() {
      const n = norm(prodState.q);
      const rows = d.products.filter(p => (!n || norm(p.name).includes(n) || norm((p.i18n.en || {}).name || '').includes(n)) && (!prodState.cat || String(p.categoryId) === prodState.cat));
      table.replaceChildren(dataTable([
        { key: 'icon', label: '', render: p => tile('products', p.icon, 'sm') },
        { key: 'name', label: t('Name'), sort: p => p.name, render: p => [h('div.title', p.name), (p.i18n.en || {}).name ? h('div.sub', p.i18n.en.name) : null] },
        { key: 'cat', label: t('Category'), sort: p => catName(p.categoryId), render: p => catName(p.categoryId) },
        { key: 'unit', label: t('Unit'), sort: p => p.unit, render: p => UI.unitLabel(p.unit) },
        { key: 'tip', label: t('Expiry info'), sort: p => p.expiryTip ? 1 : 0, render: p => p.expiryTip ? chip(t('Specific'), 'ok') : chip(t('From category'), 'neutral') },
        { key: 'lang', label: t('Languages'), render: p => translatedFlags(p, ['name']) },
        { key: 'use', label: t('In recipes'), num: true, sort: p => d.usage[p.id] || 0, render: p => String(d.usage[p.id] || 0) },
        { key: 'act', label: t('Status'), sort: p => p.active ? 1 : 0, render: p => p.active ? chip(t('Active'), 'ok') : chip(t('Hidden'), 'neutral') },
        { key: 'menu', label: '', cls: 'actions', render: p => [iconBtn('edit', t('Edit'), () => openProduct(p, d)), menuBtn([
          { icon: 'edit', label: t('Edit'), onclick: () => openProduct(p, d) },
          canDelete() ? '-' : null,
          canDelete() ? { icon: 'delete', label: t('Delete'), danger: true, onclick: () => deleteProduct(p) } : null,
        ])] },
      ], rows, { state: prodState.sort, onrow: p => openProduct(p, d), empty: t('No products') }));
    }
    paint();
    return h('div',
      pageHead(t('Product catalogue'), t('Suggested when users add products (with icon, category and unit), linked to recipe ingredients and used for the expiry information.'), [btn(t('New product'), { icon: 'add', onclick: () => openProduct(null, d) })]),
      h('div.toolbar', searchInput(prodState.q, t('Search product'), v => { prodState.q = v; paint(); }),
        sel([{ value: '', label: t('All categories') }].concat(d.categories.map(c => ({ value: c.id, label: c.name }))), prodState.cat, { onchange: e => { prodState.cat = e.target.value; paint(); } }),
        h('span.meta', t('{n} products', { n: d.products.length }))),
      table);
  }

  async function deleteProduct(p) {
    if (!await confirmDlg(t('Delete the product?'), t('Products already in users’ spaces stay, but they will no longer be linked to the catalogue. To only hide it, turn “Active” off.'), t('Delete'), true)) return;
    try { await Api.post('admin.product.delete', { id: p.id }); toast(t('Product deleted')); render(); } catch (e) { toast(e.message, 'error'); }
  }

  /** Multi-select of countries: chips of the chosen ones + a select to add more (none = not defined). */
  function countryPicker(value) {
    const chosen = (value || []).slice();
    const wrap = h('div.country-picker');
    function paintC() {
      const chips = chosen.length ? chosen.map(c => h('span.cchip', UI.flag(c), UI.countryName(c),
        h('button', { type: 'button', title: t('Remove'), 'aria-label': t('Remove'), onclick: () => { chosen.splice(chosen.indexOf(c), 1); paintC(); } }, icon('close'))))
        : [h('span.muted.small', t('Not defined'))];
      const add = sel([{ value: '', label: '+ ' + t('Add a country') }].concat(UI.countryList().filter(x => !chosen.includes(x.code)).map(x => ({ value: x.code, label: x.name }))), '', {
        onchange: e => { if (e.target.value) { chosen.push(e.target.value); paintC(); } }, 'aria-label': t('Add a country'),
      });
      wrap.replaceChildren(h('div.cchips', chips), add);
    }
    paintC();
    return { el: wrap, get value() { return chosen.slice(); } };
  }

  function iconGrid(names, value, onchange) {
    let cur = value;
    const g = h('div.icon-grid', { role: 'radiogroup' });
    for (const n of names) {
      const b = h('button' + (n === cur ? '.sel' : ''), { type: 'button', title: UI.iconLabel('products', n), onclick: () => { cur = n; g.querySelectorAll('button').forEach(x => x.classList.toggle('sel', x === b)); onchange(n); } }, UI.pic('products', n));
      g.appendChild(b);
    }
    return g;
  }

  function openProduct(src, d) {
    const p = src ? JSON.parse(JSON.stringify(src)) : { id: 0, name: '', icon: 'generic', categoryId: null, unit: 'pz', expiryTip: '', active: true, i18n: {} };
    if (!p.i18n || Array.isArray(p.i18n)) p.i18n = {};
    const cat = sel([{ value: '', label: '—' }].concat(d.categories.map(c => ({ value: c.id, label: c.name }))), p.categoryId ?? '');
    const unit = sel(d.units.map(u => ({ value: u, label: UI.unitLabel(u) })), p.unit);
    const active = toggle(t('Active (suggested to users)'), p.active);
    const save = btn(t('Save'), { icon: 'save', onclick: async () => {
      if (!(p.name || '').trim()) { toast(t('Enter the product name.'), 'error'); return; }
      await busy(save, () => Api.post('admin.product.save', { id: p.id, name: p.name, icon: p.icon, categoryId: cat.value || null, unit: unit.value, expiryTip: p.expiryTip, active: active.checked, i18n: p.i18n }));
      dlg.close(); toast(t('Product saved')); render();
    } });
    const dlg = dialog({
      title: p.id ? t('Edit product') : t('New product'), size: 'lg',
      body: [
        langTabs(lang => h('div',
          trField(p, p.i18n, 'name', 'name', t('Name'))(lang),
          trField(p, p.i18n, 'expiryTip', 'expiry_tip', t('Expiry information'), 3, t('Tips shown when the product is expiring or expired. If empty, the category ones are used.'))(lang))),
        h('div.f-row', f(t('Category'), cat), f(t('Default unit'), unit)),
        h('div.f', h('span.lbl', t('Icon')), iconGrid(d.icons, p.icon, v => { p.icon = v; })),
        active.el,
      ],
      actions: [btn(t('Cancel'), { kind: 'text', onclick: () => dlg.close() }), save],
    });
  }

  // ================================================================ categories

  async function vCategories() {
    const d = await Api.get('admin.catalog');
    const count = id => d.products.filter(p => p.categoryId === id).length;
    return h('div',
      pageHead(t('Categories'), t('Groups of the catalogue, each with general expiry tips used when a product has none.'), [btn(t('New category'), { icon: 'add', onclick: () => openCategory(null) })]),
      dataTable([
        { key: 'sort', label: t('Order'), num: true, sort: c => c.sort, render: c => String(c.sort) },
        { key: 'name', label: t('Name'), sort: c => c.name, render: c => [h('div.title', c.name), (c.i18n.en || {}).name ? h('div.sub', c.i18n.en.name) : null] },
        { key: 'n', label: t('Products'), num: true, sort: c => count(c.id), render: c => String(count(c.id)) },
        { key: 'tip', label: t('Expiry information'), render: c => h('div.sub', { style: { whiteSpace: 'normal', maxWidth: '560px' } }, c.expiryTip || '—') },
        { key: 'lang', label: t('Languages'), render: c => translatedFlags(c, ['name', 'expiry_tip']) },
        { key: 'menu', label: '', cls: 'actions', render: c => [iconBtn('edit', t('Edit'), () => openCategory(c)), menuBtn([
          { icon: 'edit', label: t('Edit'), onclick: () => openCategory(c) }, '-',
          { icon: 'delete', label: t('Delete'), danger: true, onclick: async () => {
            if (!await confirmDlg(t('Delete the category?'), t('The products of this category will have no category.'), t('Delete'), true)) return;
            try { await Api.post('admin.category.delete', { id: c.id }); render(); } catch (e) { toast(e.message, 'error'); }
          } },
        ])] },
      ], d.categories, { sortKey: 'sort', onrow: openCategory }));
  }

  function openCategory(src) {
    const c = src ? JSON.parse(JSON.stringify(src)) : { id: 0, name: '', sort: 100, expiryTip: '', i18n: {} };
    if (!c.i18n || Array.isArray(c.i18n)) c.i18n = {};
    const sort = inp({ type: 'number', value: c.sort });
    const save = btn(t('Save'), { icon: 'save', onclick: async () => {
      await busy(save, () => Api.post('admin.category.save', { id: c.id, name: c.name, sort: sort.value, expiryTip: c.expiryTip, i18n: c.i18n }));
      dlg.close(); toast(t('Category saved')); render();
    } });
    const dlg = dialog({
      title: c.id ? t('Edit category') : t('New category'), size: 'lg',
      body: [langTabs(lang => h('div', trField(c, c.i18n, 'name', 'name', t('Name'))(lang), trField(c, c.i18n, 'expiryTip', 'expiry_tip', t('General expiry information'), 5)(lang))),
        h('div.f-row', f(t('Order'), sort, t('Lower numbers come first.')))],
      actions: [btn(t('Cancel'), { kind: 'text', onclick: () => dlg.close() }), save],
    });
  }

  // ================================================================ users

  const userState = { q: '', role: '', sort: { key: 'role', dir: -1 } };
  async function vUsers() {
    const d = await Api.get('admin.users');
    counts.users = d.users.length;
    const roleChip = u => u.role >= 10 ? chip(t('Master admin'), 'warn', 'shield') : u.role >= 9 ? chip(t('Administrator'), 'ok', 'shield') : u.role >= 5 ? chip(t('Content moderator'), 'ok', 'edit') : chip(t('User'), 'neutral');
    const table = h('div');
    function paint() {
      const n = norm(userState.q);
      const rows = d.users.filter(u => (!n || norm(u.name + ' ' + u.email).includes(n))
        && (!userState.role || (userState.role === 'disabled' ? u.status === 'disabled' : userState.role === '9' ? u.role >= 9 : userState.role === '5' ? u.role === 5 : u.role < 5)));
      table.replaceChildren(dataTable([
        { key: 'name', label: t('User'), sort: u => u.name, render: u => h('div.person', h('span.avatar', initials(u.name)), h('div', h('div.title', u.name + (u.isMe ? ' (' + t('you') + ')' : '')), h('div.sub', u.email))) },
        { key: 'role', label: t('Role'), sort: u => u.role, render: roleChip },
        { key: 'status', label: t('Status'), sort: u => u.status, render: u => [u.status === 'active' ? chip(t('Active'), 'ok') : chip(t('Disabled'), 'danger'), u.mustChangePassword ? h('div.sub', t('must change password')) : null] },
        { key: 'login', label: t('Sign-in'), render: u => h('span.flag', h('span' + (u.hasPassword ? '.on' : ''), t('Password')), h('span' + (u.google ? '.on' : ''), 'Google')) },
        { key: 'created', label: t('Registered'), sort: u => u.createdAt || '', render: u => UI.fmtDateTime(u.createdAt).split(' ')[0] },
        { key: 'seen', label: t('Last seen'), sort: u => u.lastSeenAt || u.lastLoginAt || '', render: u => UI.fmtDateTime(u.lastSeenAt || u.lastLoginAt) },
        { key: 'hh', label: t('Households'), num: true, sort: u => u.households, render: u => String(u.households) },
        { key: 'menu', label: '', cls: 'actions', render: u => userMenu(u) },
      ], rows, { state: userState.sort, onrow: u => { if (u.canManage) editUser(u); } }));
    }
    paint();
    return h('div',
      pageHead(t('Users'), me.isMaster
        ? t('You are the master admin: you manage users, content moderators and administrators and can hand over the master role (there is always exactly one).')
        : t('As an administrator you manage users and content moderators. Only the master admin can appoint, edit or remove administrators.'),
        [btn(t('New user'), { icon: 'add', onclick: createUser })]),
      h('div.toolbar', searchInput(userState.q, t('Search name or email'), v => { userState.q = v; paint(); }),
        sel([{ value: '', label: t('All roles') }, { value: '1', label: t('Users') }, { value: '5', label: t('Content moderators') }, { value: '9', label: t('Administrators') }, { value: 'disabled', label: t('Disabled') }], userState.role, { onchange: e => { userState.role = e.target.value; paint(); } }),
        h('span.meta', t('{n} users', { n: d.users.length }))),
      table);
  }

  function userMenu(u) {
    const items = [
      u.canManage ? { icon: 'edit', label: t('Edit'), onclick: () => editUser(u) } : null,
      u.canManage ? { icon: 'lock', label: t('Reset password'), onclick: async () => {
        if (!await confirmDlg(t('Reset the password?'), t('{name} will be signed out of every device and will have to sign in with a temporary password.', { name: u.name }), t('Reset'))) return;
        try { const d = await Api.post('admin.user.reset_password', { id: u.id }); showPassword(t('Temporary password'), d.password, u.name); } catch (e) { toast(e.message, 'error'); }
      } } : null,
      me.isMaster && u.status === 'active' && !u.isMe ? { icon: 'shield', label: t('Make master admin'), onclick: () => transferMaster(u) } : null,
      u.canManage ? '-' : null,
      u.canManage ? { icon: 'delete', label: t('Delete account'), danger: true, onclick: async () => {
        if (!await confirmDlg(t('Delete the account?'), t('The account of {name} ({email}) will be deleted permanently, together with the households where they are the only member.', { name: u.name, email: u.email }), t('Delete'), true)) return;
        try { await Api.post('admin.user.delete', { id: u.id }); toast(t('Account deleted')); render(); } catch (e) { toast(e.message, 'error'); }
      } } : null,
    ].filter(Boolean);
    if (!items.length) return h('span.muted.small', u.isMe ? t('you') : '—');
    return menuBtn(items);
  }

  /** Roles the signed-in admin can assign: users and content moderators; administrators only by the master admin. */
  function roleOptions() {
    const o = [{ value: 1, label: t('User') }, { value: 5, label: t('Content moderator') }];
    if (me.isMaster) o.push({ value: 9, label: t('Administrator') });
    return o;
  }

  function showPassword(title, pw, who) {
    const d = dialog({
      title, size: 'sm',
      body: [h('p.text', t('Give {name} this temporary password: they will have to choose a new one at the first sign-in. It will not be shown again.', { name: who })),
        h('div.code-box', { style: { fontSize: '1.2rem', letterSpacing: '.08em' } }, pw)],
      actions: [btn(t('Copy'), { kind: 'text', icon: 'copy', onclick: () => navigator.clipboard.writeText(pw).then(() => toast(t('Copied'))).catch(() => {}) }), btn(t('Done'), { onclick: () => d.close() })],
    });
  }

  function editUser(u) {
    const name = inp({ value: u.name, maxlength: 80 });
    const email = inp({ type: 'email', value: u.email, maxlength: 190 });
    const status = sel([{ value: 'active', label: t('Active') }, { value: 'disabled', label: t('Disabled (cannot sign in)') }], u.status);
    const role = sel(roleOptions(), u.role);
    const save = btn(t('Save'), { icon: 'save', onclick: async () => {
      await busy(save, () => Api.post('admin.user.update', { id: u.id, name: name.value, email: email.value, status: status.value, role: Number(role.value) }));
      dlg.close(); toast(t('User updated')); render();
    } });
    const dlg = dialog({
      title: t('Edit user'),
      body: [f(t('Name'), name), f(t('Email'), email), h('div.f-row', f(t('Status'), status), u.role < 9 || me.isMaster ? f(t('Role'), role, t('Content moderators add and edit recipes and products; administrators also manage categories and users.')) : null)],
      actions: [btn(t('Cancel'), { kind: 'text', onclick: () => dlg.close() }), save],
    });
  }

  function createUser() {
    const name = inp({ maxlength: 80 });
    const email = inp({ type: 'email', maxlength: 190 });
    const role = sel(roleOptions(), 1);
    const save = btn(t('Create'), { icon: 'add', onclick: async () => {
      const d = await busy(save, () => Api.post('admin.user.create', { name: name.value, email: email.value, role: Number(role.value) }));
      dlg.close(); showPassword(t('Account created'), d.password, name.value); render();
    } });
    const dlg = dialog({
      title: t('New user'),
      body: [h('p.text', t('Users usually sign up by themselves from the app. Here you can create an account with a temporary password.')), f(t('Name'), name), f(t('Email'), email), f(t('Role'), role)],
      actions: [btn(t('Cancel'), { kind: 'text', onclick: () => dlg.close() }), save],
    });
  }

  function transferMaster(u) {
    const pw = inp({ type: 'password', autocomplete: 'current-password' });
    const ok = btn(t('Hand over the role'), { kind: 'danger', icon: 'shield', onclick: async () => {
      await busy(ok, () => Api.post('admin.user.transfer_master', { id: u.id, password: pw.value }));
      dlg.close(); toast(t('{name} is the new master admin', { name: u.name }));
      me = (await Api.get('auth.me')).user; render();
    } });
    const dlg = dialog({
      title: t('Hand over the master admin role?'), size: 'sm',
      body: [h('p.text', t('{name} will become the only master admin and you will become an administrator. You cannot undo this by yourself.', { name: u.name })), f(t('Your password'), pw)],
      actions: [btn(t('Cancel'), { kind: 'text', onclick: () => dlg.close() }), ok],
    });
  }

  // ================================================================ households

  async function vHouseholds() {
    const d = await Api.get('admin.households');
    counts.households = d.households.length;
    return h('div',
      pageHead(t('Households'), t('Households are the shared workspaces: every user has at least one and can invite other people. Only numbers are shown here, never the content.')),
      dataTable([
        { key: 'name', label: t('Household'), sort: x => x.name, render: x => h('div.title', x.name) },
        { key: 'owner', label: t('Owner'), sort: x => x.owner || '', render: x => [h('div', x.owner || '—'), h('div.sub', x.ownerEmail || '')] },
        { key: 'members', label: t('Members'), num: true, sort: x => x.members, render: x => x.members > 1 ? chip(String(x.members), 'ok', 'group') : '1' },
        { key: 'spaces', label: t('Spaces'), num: true, sort: x => x.spaces, render: x => String(x.spaces) },
        { key: 'items', label: t('Products'), num: true, sort: x => x.items, render: x => String(x.items) },
        { key: 'lists', label: t('Lists'), num: true, sort: x => x.lists, render: x => String(x.lists) },
        { key: 'created', label: t('Created'), sort: x => x.createdAt, render: x => UI.fmtDateTime(x.createdAt).split(' ')[0] },
      ], d.households, { sortKey: 'created', sortDir: -1 }));
  }

  // ================================================================ sign-in

  async function logout() {
    try { await Api.post('auth.logout', {}); } catch (e) { /* already signed out */ }
    me = null; loginForm();
  }

  function loginForm(msg) {
    const email = inp({ type: 'email', autocomplete: 'email' });
    const pw = inp({ type: 'password', autocomplete: 'current-password' });
    const err = h('div.err-box', { hidden: !msg }, icon('error'), h('span', msg || ''));
    const showErr = m => { err.lastChild.textContent = m; err.hidden = false; };
    const sub = btn(t('Sign in'), { type: 'submit', cls: 'btn-block' });
    const gslot = h('div.google-slot');
    root.replaceChildren(h('div.adm-login', h('div.card-l',
      h('div.logo', h('img', { src: 'assets/icons/icon-color.png', alt: '' }), 'FoodTrack'),
      h('p.lead', t('Admin portal')),
      config.googleClientId ? [gslot, h('div.divider-text', t('or with your email'))] : null,
      h('form', { onsubmit: async e => {
        e.preventDefault(); err.hidden = true;
        try { await busy(sub, () => Api.post('auth.login', { email: email.value, password: pw.value }, { silentAuth: true })); start(); }
        catch (ex) { showErr(ex.message); }
      } }, f(t('Email'), email), f(t('Password'), pw), err, h('div.spacer'), sub),
      h('p.center', h('a', { href: './' }, t('Back to the app'))))));
    if (config.googleClientId) UI.googleButton(gslot, config.googleClientId, async credential => {
      try {
        await Api.post('auth.google', { credential }, { silentAuth: true });
        start();
      } catch (e) { showErr(e.code === 'needs_signup' ? t('No FoodTrack account uses this Google account.') : e.message); }
    }, 'signin_with');
  }

  async function start() {
    UI.applyTheme();
    try {
      const [d, cfg] = await Promise.all([Api.get('auth.me', { silentAuth: true }), Api.get('auth.config', { silentAuth: true }).catch(() => null)]);
      me = d.user;
      if (cfg) config = cfg;
    } catch (e) { root.replaceChildren(h('div.adm-login', h('div.err-box', icon('error'), h('span', e.message)))); return; }
    if (!me) return loginForm();
    if (me.mustChangePassword) return loginForm(t('First change your temporary password from the app.'));
    TABS = ALL_TABS.filter(x => me.role >= x[3]);
    if (!me.isEditor) {
      root.replaceChildren(h('div.adm-login', h('div.card-l', h('div.logo', h('img', { src: 'assets/icons/icon-color.png', alt: '' }), t('Access denied')),
        h('p.lead', t('The portal is reserved to FoodTrack administrators.')), h('a.btn.btn-filled', { href: './' }, t('Back to the app')))));
      return;
    }
    render();
  }

  Api.onUnauthenticated(() => loginForm(t('Session expired: sign in again.')));
  window.addEventListener('hashchange', render);
  matchMedia('(prefers-color-scheme: dark)').addEventListener('change', UI.applyTheme);
  start();
})();
