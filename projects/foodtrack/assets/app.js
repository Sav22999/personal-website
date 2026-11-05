/* FoodTrack — web app (single page, hash routing). */
(function () {
  'use strict';

  const t = I18N.t;
  const { h, icon, tile, chip, fab, btn, iconBtn, busy, field, input, textarea, select, sheet, actionSheet, confirm, toast, store, norm } = UI;

  const VERSION = '1.6.0';
  const CONTACT_URL = 'https://www.saveriomorelli.com/contact-me/email/';
  const DONATE_URL = 'https://www.saveriomorelli.com/donate/';
  const UNITS = ['pz', 'g', 'kg', 'ml', 'cl', 'l', 'conf', 'cucchiai', 'cucchiaini', 'q.b.'];
  const SLOTS = [['colazione', t('Breakfast')], ['pranzo', t('Lunch')], ['merenda', t('Snack')], ['cena', t('Dinner')]];
  const SORTS = [
    ['exp-asc', t('Expiry: soonest first')],
    ['exp-desc', t('Expiry: latest first')],
    ['qty-asc', t('Quantity: smallest first')],
    ['qty-desc', t('Quantity: largest first')],
    ['name-asc', t('Name: A to Z')],
    ['name-desc', t('Name: Z to A')],
  ];
  const DISCLAIMER = t('Indicative information based on common practice: it does not replace your judgement and FoodTrack does not guarantee that the product is safe to eat.');

  const S = {
    user: null, loaded: false, config: { googleClientId: '' },
    catalog: { categories: [], products: [] }, recipes: [],
    household: null, households: [], spaces: [], items: [], lists: [], listItems: [], plan: [], favorites: new Set(),
    signature: '',
  };
  const root = document.getElementById('app');
  let weekOffset = 0;
  let recipeQuery = '';
  let recipeFilters = { fav: false, inHome: false, cookable: false, expiring: false, expired: false };
  let recipeIngredients = new Set();
  const searchState = { q: '', spaceId: '', categoryId: '', exp: 'all' };

  // ===================================================== state

  function applyState(d) {
    if (!d) return;
    for (const k of ['household', 'households', 'spaces', 'items', 'lists', 'listItems', 'plan']) if (d[k] !== undefined) S[k] = d[k];
    if (d.favorites) S.favorites = new Set(d.favorites);
    if (d.catalog) S.catalog = d.catalog;
    if (d.recipes) S.recipes = d.recipes;
    S.signature = JSON.stringify([S.household && S.household.id, S.household && S.household.members, S.spaces, S.items, S.lists, S.listItems, S.plan]);
  }

  /** Runs an action on the API, applies the returned state and re-renders the current view. */
  async function act(route, body, button) {
    const d = await busy(button, () => Api.post(route, body));
    applyState(d);
    render(true);
    return d;
  }

  const productById = id => S.catalog.products.find(p => p.id === id) || null;
  const categoryById = id => S.catalog.categories.find(c => c.id === id) || null;
  const spaceById = id => S.spaces.find(s => s.id === id) || null;
  const listById = id => S.lists.find(l => l.id === id) || null;
  const recipeById = id => S.recipes.find(r => r.id === id) || null;
  const noticeDays = () => store.get('noticeDays', 3);
  /** Name of an item/list item: the catalog name in the user's language when it was not renamed. */
  function itemName(x) {
    const p = productById(x.productId);
    return p && p.nameBase && x.name === p.nameBase ? p.name : x.name;
  }
  const plural = (n, one, many) => `${n} ${t(n === 1 ? one : many)}`;

  function expiryState(item) {
    if (!item.expiryDate) return null;
    const d = UI.daysUntil(item.expiryDate);
    if (d < 0) return 'expired';
    if (d <= noticeDays()) return 'expiring';
    return 'ok';
  }
  /** Chip with the expiry status of a product. */
  function expiryChip(item) {
    if (!item.expiryDate) return chip(t('No expiry'), 'neutral');
    const d = UI.daysUntil(item.expiryDate);
    if (d < 0) return chip(d === -1 ? t('Expired yesterday') : t('Expired {n} days ago', { n: -d }), 'danger', 'error');
    if (d === 0) return chip(t('Expires today'), 'warn', 'warning');
    if (d <= noticeDays()) return chip(d === 1 ? t('Expires tomorrow') : t('Expires in {n} days', { n: d }), 'warn', 'warning');
    return chip(t('In {n} days', { n: d }), 'ok');
  }
  function expiryLine(item) {
    if (!item.expiryDate) return t('No expiry date');
    return (item.expiryType === 'best_before' ? t('Best before ') : t('Expires on ')) + UI.fmtDate(item.expiryDate);
  }
  const expiredItems = () => S.items.filter(i => expiryState(i) === 'expired');
  const expiringItems = () => S.items.filter(i => expiryState(i) === 'expiring');

  function expiryTip(item) {
    const p = productById(item.productId);
    if (p && p.expiryTip) return p.expiryTip;
    const c = categoryById(item.categoryId != null ? item.categoryId : (p && p.categoryId));
    if (c && c.expiryTip) return c.expiryTip;
    return item.expiryType === 'best_before'
      ? t('“Best before” is the date until which the product keeps its quality. If not much time has passed and the package is intact, check the product yourself: it may still be fine if there is no mould and no change in colour, smell or taste.')
      : t('“Use by” is a safety limit: after this date you should not eat the product, especially if fresh (meat, fish, fresh dairy).');
  }

  // ---------- Recipes: ingredient availability ----------

  function matchItems(ing) {
    const n = norm(ing.name);
    return S.items.filter(i => (ing.productId && i.productId === ing.productId) || norm(i.name) === n);
  }

  function availability(ing) {
    const matches = matchItems(ing);
    if (!matches.length) return { status: 'missing', matches };
    if (ing.quantity == null || ing.unit === 'q.b.') return { status: 'ok', matches };
    const need = UI.toBase(ing.quantity, ing.unit);
    let have = 0, comparable = false;
    for (const m of matches) {
      if (m.quantity == null) continue;
      const b = UI.toBase(m.quantity, m.unit);
      if (b.base === need.base) { have += b.value; comparable = true; }
    }
    if (!comparable || have >= need.value) return { status: 'ok', matches };
    return { status: 'partial', matches, missing: need.value - have, unit: need.base };
  }

  function recipeStats(r) {
    let ok = 0, usesExpiring = false, usesExpired = false, usesAvailable = false;
    for (const ing of r.ingredients) {
      const a = availability(ing);
      if (a.status === 'ok') { ok++; usesAvailable = true; }
      else if (a.matches && a.matches.length > 0) { usesAvailable = true; }
      for (const m of a.matches) { const st = expiryState(m); if (st === 'expiring') usesExpiring = true; if (st === 'expired') usesExpired = true; }
    }
    return { ok, total: r.ingredients.length, usesExpiring, usesExpired, usesAvailable };
  }

  /** Missing ingredients of some recipes, merged by product and unit. */
  function missingFor(recipes) {
    const map = new Map();
    function add(ing, qty, unit) {
      const key = (ing.productId || norm(ing.name)) + '|' + (unit ? UI.toBase(1, unit).base : '');
      const cur = map.get(key);
      if (cur && qty != null && cur.quantity != null) {
        const a = UI.toBase(cur.quantity, cur.unit), b = UI.toBase(qty, unit);
        if (a.base === b.base) { cur.quantity = a.value + b.value; cur.unit = a.base; return; }
      }
      if (!cur) map.set(key, { productId: ing.productId, name: ing.name, quantity: qty, unit: unit || 'pz' });
    }
    for (const r of recipes) {
      for (const ing of r.ingredients) {
        const a = availability(ing);
        if (ing.unit === 'q.b.') { if (a.status === 'missing') add(ing, null, 'q.b.'); continue; }
        if (a.status === 'missing') add(ing, ing.quantity, ing.unit);
        else if (a.status === 'partial') add(ing, a.missing, a.unit);
      }
    }
    return [...map.values()];
  }

  // ===================================================== components

  /** List row: leading tile, title, subtitle, chips, trailing actions. */
  function listRow(o) {
    return h('div.list-row' + (o.big ? '.big' : '') + (o.cls ? '.' + o.cls : ''),
      o.lead || null,
      h('div.body', h('p.title', o.title), o.sub ? h('div.sub', o.sub) : null, o.meta && o.meta.filter(Boolean).length ? h('div.meta', o.meta) : null),
      o.trail ? h('div.trail', o.trail) : null);
  }
  /** Card with a list row inside; clickable when onclick is given. */
  function cardRow(o, onclick) {
    return h('div.card' + (onclick ? '.clickable' : ''), onclick ? { role: 'button', tabindex: 0, onclick, onkeydown: e => { if (e.key === 'Enter' && e.target === e.currentTarget) onclick(); } } : null, listRow(o));
  }
  /** Row of a settings group (profile). */
  function groupRow(ic, title, sub, onclick, opts) {
    opts = opts || {};
    const tag = opts.href ? 'a' : onclick ? 'button' : 'div';
    return h(tag + '.list-row' + (opts.danger ? '.danger' : ''), { type: tag === 'button' ? 'button' : undefined, href: opts.href, target: opts.external ? '_blank' : undefined, rel: opts.external ? 'noopener' : undefined, onclick },
      opts.lead || tile('icon', ic, 'sm'), h('div.body', h('p.title', title), sub ? h('div.sub', sub) : null),
      opts.trail !== undefined ? opts.trail : (onclick || opts.href ? h('div.trail', icon(opts.href && opts.external ? 'open' : 'right')) : null));
  }
  function sectionTitle(text, count, action) {
    return h('h2.section-title', h('span', text), count != null ? h('span.count', String(count)) : null, action ? h('span.grow') : null, action || null);
  }
  function banner(tone, ic, title, text, actions) {
    return h('div.banner.' + tone, icon(ic), h('div.b-body', h('h3', title), h('p', text), actions && actions.length ? h('div.b-actions', actions) : null));
  }
  function empty(kind, name, title, text) {
    return h('div.empty', tile(kind, name, 'xl'), h('strong', title), text ? h('p', text) : null);
  }
  function moreBtn(items, title) { return iconBtn('more', t('More actions'), () => actionSheet(items, title)); }
  function searchbar(inp, trailing) { return h('div.searchbar', icon('search'), inp, trailing || null); }

  function iconPicker(folder, icons, value, onchange, grid) {
    let cur = value;
    const wrap = h('div.icon-picker' + (grid ? '.grid' : ''), { role: 'radiogroup' });
    for (const ic of icons) {
      const b = h('button' + (ic === cur ? '.sel' : ''), { type: 'button', role: 'radio', 'aria-checked': String(ic === cur), title: UI.iconLabel(folder, ic), onclick: () => {
        cur = ic; wrap.querySelectorAll('button').forEach(x => { x.classList.toggle('sel', x === b); x.setAttribute('aria-checked', String(x === b)); }); onchange(ic);
      } }, UI.pic(folder, ic));
      wrap.appendChild(b);
    }
    return wrap;
  }

  function nutriPicker(value, onchange, readonly) {
    let cur = value || null;
    const wrap = h('div.nutri' + (cur ? '' : '.none'), { role: 'radiogroup', 'aria-label': 'Nutri-Score' });
    for (const g of ['A', 'B', 'C', 'D', 'E']) {
      wrap.appendChild(h('button.' + g + (g === cur ? '.sel' : ''), { type: 'button', disabled: !!readonly, role: 'radio', 'aria-checked': String(g === cur), onclick: () => {
        cur = cur === g ? null : g;
        wrap.classList.toggle('none', !cur);
        wrap.querySelectorAll('button').forEach(x => x.classList.toggle('sel', x.textContent === cur));
        onchange(cur);
      } }, g));
    }
    return wrap;
  }

  function seg(options, value, onchange) {
    const wrap = h('div.seg', { role: 'tablist' });
    for (const [v, label] of options) {
      const b = h('button' + (v === value ? '.sel' : ''), { type: 'button', role: 'tab', onclick: () => { wrap.querySelectorAll('button').forEach(x => x.classList.toggle('sel', x === b)); onchange(v); } }, label);
      wrap.appendChild(b);
    }
    return wrap;
  }

  function unitSelect(value) {
    const opts = UNITS.slice();
    if (value && !opts.includes(value)) opts.push(value);
    return select(opts.map(u => ({ value: u, label: UI.unitLabel(u) })), value || 'pz');
  }

  function tipBanner(key, text) {
    if (store.get('tip.' + key, false)) return null;
    const b = banner('info', 'bulb', t('Tip'), text, [btn(t('Got it'), { kind: 'text', onclick: () => { store.set('tip.' + key, true); b.remove(); } })]);
    return b;
  }

  const cancelBtn = () => btn(t('Cancel'), { kind: 'text', onclick: UI.closeTop });

  // ===================================================== routing

  /** Routes used before 1.5.0 (Italian): old links and invitations keep working. */
  const LEGACY_ROUTES = { liste: 'lists', lista: 'list', spazi: 'spaces', spazio: 'space', ricette: 'recipes', ricetta: 'recipe', piano: 'plan', cerca: 'search', profilo: 'profile', unisciti: 'join' };
  (function upgradeLegacyHash() {
    const m = location.hash.match(/^#\/?([a-z]+)(\/.*)?$/);
    if (m && LEGACY_ROUTES[m[1]]) history.replaceState(history.state, '', '#/' + LEGACY_ROUTES[m[1]] + (m[2] || ''));
  })();
  window.addEventListener('hashchange', () => {
    const m = location.hash.match(/^#\/?([a-z]+)(\/.*)?$/);
    if (m && LEGACY_ROUTES[m[1]]) location.replace('#/' + LEGACY_ROUTES[m[1]] + (m[2] || ''));
  }, true);
  function route() {
    const parts = (location.hash.replace(/^#\/?/, '') || 'spaces').split('/');
    return { name: parts[0], id: parts[1] ? decodeURIComponent(parts[1]) : null };
  }
  function go(path) {
    const target = '#/' + path;
    if (UI.sheetsOpen()) { UI.closeAllSheets(); setTimeout(() => { location.hash = target; }, 120); }
    else location.hash = target;
  }

  let lastRouteKey = '';
  let curDepth = -1;
  /** Remembers how deep we are in the app history, so "back" never leaves the app. */
  function trackDepth() {
    const st = history.state;
    if (st && typeof st.ftDepth === 'number') { curDepth = st.ftDepth; return; }
    curDepth = curDepth < 0 ? 0 : curDepth + 1;
    history.replaceState({ ftDepth: curDepth }, '');
  }

  function render(keepScroll) {
    if (!S.user) return renderAuth();
    if (S.user.mustChangePassword) return renderForcePassword();
    if (!S.loaded) {
      root.replaceChildren(DESK.matches ? h('div.content', UI.skeletonScreen()) : UI.skeletonScreen());
      return;
    }

    const r = route();
    const key = r.name + '/' + (r.id || '');
    const y = window.scrollY;
    const views = { lists: viewLists, list: viewList, spaces: viewSpaces, space: viewSpace, recipes: viewRecipes, recipe: viewRecipe, plan: viewPlan, search: viewSearch, profile: viewProfile, join: viewJoin, history: viewHistory };
    const v = (views[r.name] || viewSpaces)(r.id);
    if (!v) return;
    // On desktop, screens listed in the side navigation are top-level: no back arrow
    const showBack = v.back && !(DESK.matches && ['profile', 'plan', 'search'].includes(r.name));
    const bar = showBack
      ? h('header.appbar.small', iconBtn('back', t('Back'), () => { if (curDepth > 0) history.back(); else location.replace('#/' + v.back); }), h('div.titles', h('h1', v.title)), v.actions || [])
      : h('header.appbar',
          h('div.titles', h('div.brand', h('img', { src: 'assets/icons/icon-color.png', alt: '' }), 'FoodTrack', householdChip()), h('h1', v.title)),
          v.actions || [],
          h('button.avatar', { type: 'button', title: t('Profile'), 'aria-label': t('Profile and settings'), onclick: () => go('profile') }, initials(S.user.name)));
    const nav = v.nav !== false;
    const main = h('main.view' + (!nav ? '.no-nav' : '') + (nav && !v.fab ? '.no-fab' : '') + (v.cls ? '.' + v.cls : ''), v.body);
    const content = h('div.content', bar, main);
    const nodes = [sideNav(NAV_OF[r.name] || r.name), content];
    if (nav) content.appendChild(bottomNav(v.tab));
    if (v.fab) {
      // Desktop: the main action is a button in the app bar; mobile: a floating action button
      if (DESK.matches) bar.appendChild(v.fab);
      else { if (!nav) v.fab.classList.add('low'); content.appendChild(v.fab); }
    }
    root.replaceChildren(...nodes);
    document.title = v.title + ' · FoodTrack';
    if (keepScroll && key === lastRouteKey) window.scrollTo(0, y); else window.scrollTo(0, 0);
    onScroll();
    lastRouteKey = key;
    if (v.after) v.after();
  }

  function onScroll() { document.body.classList.toggle('scrolled', window.scrollY > 4); }
  window.addEventListener('scroll', onScroll, { passive: true });

  function householdChip() {
    if (!S.household) return null;
    return h('button.household-chip', { type: 'button', title: t('Switch household'), onclick: e => { e.stopPropagation(); openHouseholdSwitcher(); } },
      icon(S.household.members.length > 1 ? 'group' : 'home'), h('span', S.household.name), icon('chevronDown'));
  }

  /** Which side-navigation entry is highlighted for each route. */
  const NAV_OF = { list: 'lists', space: 'spaces', recipe: 'recipes', join: 'profile', history: 'history' };
  /** Wide screens (desktop, landscape tablets): side navigation, grids, dialogs instead of bottom sheets. */
  const DESK = matchMedia('(min-width: 960px)');
  DESK.addEventListener('change', () => { if (S.user && S.loaded) { UI.closeAllSheets(); render(true); } });

  /** Navigation drawer shown on wide screens (hidden on phones, where the bottom bar is used). */
  function sideNav(active) {
    const nExp = expiredItems().length + expiringItems().length;
    const nBuy = S.listItems.filter(x => !x.inCart).length;
    const link = (name, label, ic, badge, tone) => h('a.sn-link' + (active === name ? '.active' : ''), { href: '#/' + name, 'aria-current': active === name ? 'page' : null },
      icon(ic), h('span', label), badge ? h('span.sn-badge' + (tone ? '.' + tone : ''), String(badge)) : null);
    const hh = S.household;
    return h('nav.sidenav', { 'aria-label': t('Sections') },
      h('a.sn-brand', { href: '#/spaces' }, h('img', { src: 'assets/icons/icon-color.png', alt: '' }), h('strong', 'FoodTrack')),
      hh ? h('button.sn-household', { type: 'button', title: t('Switch household'), onclick: () => openHouseholdSwitcher() },
        icon(hh.members.length > 1 ? 'group' : 'home'), h('span.grow', h('small', t('Household')), h('span', hh.name)), icon('chevronDown')) : null,
      h('div.sn-links',
        link('spaces', t('Spaces'), 'spaces', nExp, 'warn'),
        link('lists', t('Lists'), 'list', nBuy),
        link('recipes', t('Recipes'), 'restaurant'),
        link('plan', t('Weekly plan'), 'calendar'),
        link('search', t('Search'), 'search'),
        link('history', t('Activity history'), 'history')),
      h('div.grow'),
      // Admins and content moderators: shortcut to the management portal
      S.user.isEditor ? h('a.sn-link.sn-admin', { href: 'admin.html', target: '_blank', rel: 'noopener', title: S.user.isAdmin ? t('Recipes, products, categories and users') : t('Recipes and products') },
        icon('shield'), h('span', t('Management portal')), icon('open')) : null,
      h('a.sn-user' + (active === 'profile' ? '.active' : ''), { href: '#/profile', title: t('Profile and settings') },
        h('span.avatar', initials(S.user.name)), h('span.grow', h('strong', S.user.name), h('small', S.user.email)), icon('right')));
  }

  function bottomNav(active) {
    const tab = (name, label, ic) => h('a' + (active === name ? '.active' : ''), { href: '#/' + name, 'aria-current': active === name ? 'page' : null }, h('span.pill', icon(ic)), label);
    return h('nav.bottomnav', { 'aria-label': t('Sections') }, tab('lists', t('Lists'), 'list'), tab('spaces', t('Spaces'), 'spaces'), tab('recipes', t('Recipes'), 'restaurant'));
  }

  function initials(name) { return String(name || '?').trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase(); }

  // ===================================================== SPACES

  function viewSpaces() {
    const body = [];
    const exp = expiredItems(), soon = expiringItems();
    const sig = a => a.map(i => i.id).sort().join(',');
    if (exp.length && store.get('hide.expired', '') !== sig(exp)) {
      body.push(banner('danger', 'error', plural(exp.length, 'expired product', 'expired products'),
        t('Some products can still be eaten for a short time after they expire. Find out which ones.'),
        [btn(t('Hide'), { kind: 'text', onclick: () => { store.set('hide.expired', sig(exp)); render(true); } }), btn(t('Learn more'), { onclick: () => openExpirySheet('expired') })]));
    }
    if (soon.length && store.get('hide.expiring', '') !== sig(soon)) {
      body.push(banner('warn', 'warning', plural(soon.length, 'expiring product', 'expiring products'),
        t('Don’t let them expire: find out how to use them in great recipes!'),
        [btn(t('Hide'), { kind: 'text', onclick: () => { store.set('hide.expiring', sig(soon)); render(true); } }), btn(t('Learn more'), { onclick: () => openExpirySheet('expiring') })]));
    }

    if (!S.spaces.length) body.push(empty('spaces', 'fridge', t('No spaces'), t('Create a space (fridge, pantry, cellar…) to start adding products.')));
    body.push(h('div.stack', S.spaces.map(sp => {
      const items = S.items.filter(i => i.spaceId === sp.id);
      const nExp = items.filter(i => expiryState(i) === 'expired').length;
      const nSoon = items.filter(i => expiryState(i) === 'expiring').length;
      return cardRow({
        big: true, lead: tile('spaces', sp.icon, 'lg'), title: sp.name, sub: sp.notes || null,
        meta: [chip(items.length ? plural(items.length, 'product', 'products') : t('Empty'), 'neutral'),
          nExp ? chip(nExp === 1 ? t('1 expired') : t('{n} expired', { n: nExp }), 'danger') : null, nSoon ? chip(t('{n} expiring', { n: nSoon }), 'warn') : null],
        trail: moreBtn([
          { icon: 'edit', label: t('Edit'), onclick: () => openSpaceForm(sp) },
          { icon: 'delete', label: t('Delete'), danger: true, onclick: () => deleteSpace(sp) },
        ], sp.name),
      }, () => go('space/' + sp.id));
    })));
    return {
      tab: 'spaces', title: t('Home spaces'), body,
      actions: [
        iconBtn('history', t('Activity history'), () => go('history')),
        iconBtn('search', t('Find a food'), () => go('search'))
      ],
      fab: fab(t('New space'), 'add', () => openSpaceForm(null)),
    };
  }

  function openExpirySheet(kind) {
    const items = kind === 'expired' ? expiredItems() : expiringItems();
    const text = kind === 'expired'
      ? t('If they say “best before”, check the products yourself: they may still be fine if there is no mould and no change in colour, smell or taste.')
      : t('These products will expire soon. Discover recipes so you don’t throw them away!');
    const s = sheet({
      title: kind === 'expired' ? t('Expired products') : t('Expiring products'),
      body: [h('p.sheet-text', text), h('div.stack', items.map(i => {
        const sp = spaceById(i.spaceId);
        return cardRow({ lead: tile('products', i.icon), title: itemName(i), sub: `${expiryLine(i)} · ${sp ? sp.name : '—'}`, meta: [expiryChip(i)], trail: icon('right') }, () => openItemDetail(i));
      }))],
      actions: [cancelBtn(), btn(t('Discover recipes'), { icon: 'restaurant', onclick: () => { recipeFilters = { fav: false, inHome: false, expiring: kind === 'expiring', expired: kind === 'expired', cookable: false }; recipeIngredients.clear(); recipeQuery = ''; go('recipes'); } })],
    });
  }

  function openSpaceForm(sp) {
    let iconSel = sp ? sp.icon : 'fridge';
    const name = input({ value: sp ? sp.name : '', maxlength: 60 });
    const notes = textarea({ value: sp ? sp.notes : '', maxlength: 500, rows: 2 });
    const save = btn(sp ? t('Save') : t('Create space'), { icon: 'check', onclick: async () => {
      if (!name.value.trim()) { toast(t('Enter the space name.'), 'error'); name.focus(); return; }
      await act('space.save', { id: sp ? sp.id : 0, name: name.value, icon: iconSel, notes: notes.value }, save);
      s.close();
    } });
    const s = sheet({
      title: sp ? t('Edit space') : t('New space'),
      body: [field(t('Name'), name), h('div.label', t('Icon')), iconPicker('spaces', UI.iconNames('spaces'), iconSel, v => { iconSel = v; }), field(t('Notes (optional)'), notes)],
      actions: [cancelBtn(), save],
    });
  }

  async function deleteSpace(sp) {
    const n = S.items.filter(i => i.spaceId === sp.id).length;
    if (!await confirm(t('Delete the space?'), n ? t('“{name}” contains {what}: they will be deleted too, for all household members.', { name: sp.name, what: plural(n, 'product', 'products') }) : t('The space “{name}” will be deleted.', { name: sp.name }), t('Delete'), true)) return;
    await act('space.delete', { id: sp.id });
    toast(t('Space deleted'));
  }

  function sortItems(items, mode) {
    const by = {
      'exp-asc': (a, b) => (a.expiryDate || '9999').localeCompare(b.expiryDate || '9999'),
      'exp-desc': (a, b) => (b.expiryDate || '0000').localeCompare(a.expiryDate || '0000'),
      'qty-asc': (a, b) => (a.quantity ?? Infinity) - (b.quantity ?? Infinity),
      'qty-desc': (a, b) => (b.quantity ?? -Infinity) - (a.quantity ?? -Infinity),
      'name-asc': (a, b) => a.name.localeCompare(b.name, 'it'),
      'name-desc': (a, b) => b.name.localeCompare(a.name, 'it'),
    }[mode] || (() => 0);
    return items.slice().sort(by);
  }

  function itemCard(i, opts) {
    opts = opts || {};
    const sp = spaceById(i.spaceId);
    const qty = UI.fmtQty(i.quantity, i.unit);
    return cardRow({
      lead: tile('products', i.icon), title: itemName(i) + (i.brand ? ' · ' + i.brand : ''),
      sub: [expiryLine(i), qty, opts.showSpace && sp ? sp.name : null].filter(Boolean).join(' · '),
      meta: [expiryChip(i)],
      trail: moreBtn([
        { icon: 'edit', label: t('Edit'), onclick: () => openItemForm({ item: i }) },
        { icon: 'check', label: t('Mark as used'), onclick: () => consumeItem(i) },
        { icon: 'cartAdd', label: t('Add to shopping list'), onclick: () => addToListSheet([{ productId: i.productId, name: i.name, quantity: i.quantity, unit: i.unit }]) },
        { icon: 'delete', label: t('Delete'), danger: true, onclick: () => deleteItem(i) },
      ], i.name),
    }, () => openItemDetail(i));
  }

  function viewSpace(id) {
    const sp = spaceById(Number(id));
    if (!sp) { go('spaces'); return null; }
    const mode = store.get('sort', 'exp-asc');
    const items = sortItems(S.items.filter(i => i.spaceId === sp.id), mode);
    const body = [];
    if (!items.length) body.push(empty('spaces', sp.icon, t('No products'), t('Add the products you keep here: we will alert you before they expire.')));
    else body.push(sectionTitle(t('Products'), items.length, btn(SORTS.find(x => x[0] === mode)[1].split(':')[0], { kind: 'text', icon: 'sort', onclick: () => openSort(mode) })));
    body.push(h('div.stack', items.map(i => itemCard(i))));
    return {
      tab: 'spaces', title: sp.name, back: 'spaces', body,
      actions: [moreBtn([
        { icon: 'sort', label: t('Sort products'), onclick: () => openSort(mode) },
        { icon: 'edit', label: t('Edit space'), onclick: () => openSpaceForm(sp) },
        { icon: 'delete', label: t('Delete space'), danger: true, onclick: () => deleteSpace(sp).then(() => { if (!spaceById(sp.id)) go('spaces'); }) },
      ], sp.name)],
      fab: fab(t('Add product'), 'add', () => openProductSearch({ spaceId: sp.id })),
    };
  }

  function openSort(mode) {
    actionSheet(SORTS.map(([k, label]) => ({ icon: k.startsWith('exp') ? 'timer' : k.startsWith('qty') ? 'filter' : 'sort', label, selected: k === mode, onclick: () => { store.set('sort', k); render(true); } })), t('Sort by'));
  }

  // ---------- Item detail / forms ----------

  function openItemDetail(i) {
    const st = expiryState(i);
    const sp = spaceById(i.spaceId);
    const cat = categoryById(i.categoryId);
    const d = (label, value) => value ? h('div.d', h('span', label), h('span', value)) : null;
    const body = [
      h('div.row', { style: { marginBottom: '14px' } }, tile('products', i.icon, 'lg'), h('div.grow', h('div.meta-row', expiryChip(i), i.nutriscore ? chip('Nutri-Score ' + i.nutriscore, 'neutral') : null))),
    ];
    if (st === 'expired' || st === 'expiring') body.push(banner('info', 'info', t('About the expiry'), expiryTip(i)), h('p.helper', { style: { margin: '-6px 4px 14px' } }, DISCLAIMER));
    body.push(h('div.details',
      d(i.expiryType === 'best_before' ? t('Best before') : t('Expiry'), i.expiryDate ? UI.fmtDate(i.expiryDate) : t('None')),
      d(t('Quantity'), UI.fmtQty(i.quantity, i.unit) || '—'),
      d(t('Space'), sp && sp.name), d(t('Category'), cat && cat.name), d(t('Brand'), i.brand), d(t('Barcode'), i.barcode),
      d(t('Added'), (i.createdBy ? i.createdBy + ', ' : '') + UI.fmtDateTime(i.createdAt).split(' ')[0])));
    if (i.notes) body.push(h('div.details', h('div.d', h('span', t('Notes')), h('span', i.notes))));
    body.push(h('div.btn-row', { style: { marginTop: '10px' } },
      btn(t('Find recipes with this product'), {
        kind: 'tonal', icon: 'restaurant',
        onclick: () => {
          s.close();
          recipeFilters = { fav: false, inHome: false, cookable: false, expiring: false, expired: false };
          recipeIngredients = new Set([norm(itemName(i))]);
          recipeQuery = '';
          go('recipes');
        }
      })
    ));
    const s = sheet({
      title: itemName(i), body,
      actions: [
        btn(t('Edit'), { kind: 'text', icon: 'edit', onclick: () => { s.close(); setTimeout(() => openItemForm({ item: i }), 240); } }),
        btn(t('Mark as used'), { icon: 'check', onclick: () => { s.close(); setTimeout(() => consumeItem(i), 240); } }),
      ],
    });
  }

  function consumeItem(i) {
    if (i.quantity == null || (i.quantity <= 1 && i.unit === 'pz')) {
      return act('item.consume', { id: i.id }).then(() => toast(t('“{name}” marked as used', { name: itemName(i) })));
    }
    let mode = 'all';
    const qty = input({ type: 'number', inputmode: 'decimal', min: 0, step: 'any', value: '' });
    const qtyField = field(t('Quantity used') + ` (${UI.unitLabel(i.unit)})`, qty);
    qtyField.hidden = true;
    const ok = btn(t('Confirm'), { icon: 'check', onclick: async () => {
      if (mode === 'part' && !(Number(qty.value) > 0)) { toast(t('Enter how much you used.'), 'error'); return; }
      await act('item.consume', mode === 'all' ? { id: i.id } : { id: i.id, quantity: qty.value }, ok);
      s.close(); toast(mode === 'all' ? t('“{name}” used', { name: itemName(i) }) : t('Quantity updated'));
    } });
    const s = sheet({
      title: t('Mark as used'),
      body: [h('p.sheet-text', t('Available: {qty} of “{name}”.', { qty: UI.fmtQty(i.quantity, i.unit), name: itemName(i) })),
        seg([['all', t('All of it')], ['part', t('Only some')]], mode, v => { mode = v; qtyField.hidden = v !== 'part'; if (v === 'part') qty.focus(); }), qtyField],
      actions: [cancelBtn(), ok],
    });
  }

  async function deleteItem(i) {
    if (!await confirm(t('Delete the product?'), t('“{name}” will be removed from the space.', { name: itemName(i) }), t('Delete'), true)) return;
    await act('item.delete', { id: i.id });
    toast(t('Product deleted'));
  }

  /** Product search sheet ("Aggiungi un prodotto"), used both for spaces and shopping lists. */
  function openProductSearch(ctx) {
    const q = input({ type: 'search', placeholder: t('Search the catalogue'), enterkeyhint: 'search', 'aria-label': t('Search product') });
    const results = h('div.stack', { style: { marginTop: '12px' } });
    const createBtn = btn(t('Create new product'), { kind: 'tonal', icon: 'add', onclick: () => choose({ name: q.value.trim() }) });
    function choose(prefill) {
      s.close();
      setTimeout(() => ctx.listId ? openListItemForm({ listId: ctx.listId, prefill }) : openItemForm({ spaceId: ctx.spaceId, prefill }), 240);
    }
    function refresh() {
      const n = norm(q.value);
      createBtn.querySelector('span').textContent = n ? t('Create “{name}”', { name: q.value.trim() }) : t('Create new product');
      let prods = S.catalog.products;
      if (n) prods = prods.filter(p => norm(p.name).includes(n)).sort((a, b) => norm(a.name).indexOf(n) - norm(b.name).indexOf(n));
      UI.clear(results);
      for (const p of prods.slice(0, 60)) {
        const cat = categoryById(p.categoryId);
        results.appendChild(cardRow({ lead: tile('products', p.icon, 'sm'), title: p.name, sub: cat ? cat.name : null, trail: icon('add') },
          () => choose({ productId: p.id, name: p.name, icon: p.icon, unit: p.unit, categoryId: p.categoryId })));
      }
      if (n && !prods.length) results.appendChild(h('p.helper.center', t('Not in the catalogue: you can create a new product.')));
    }
    q.addEventListener('input', refresh);
    q.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); const first = results.querySelector('.card'); if (first) first.click(); else if (q.value.trim()) createBtn.click(); } });
    const s = sheet({
      title: t('Add a product'),
      body: [searchbar(q), h('div.btn-row', createBtn, ctx.listId ? null : btn(t('Scan'), { kind: 'outline', icon: 'scan', onclick: () => { s.close(); setTimeout(() => openScanner(ctx), 240); } })), results],
    });
    refresh();
  }

  /** New / edit item in a space. opts: { item } | { spaceId, prefill } | { fromListItem } */
  function openItemForm(opts) {
    const it = opts.item || null;
    const pre = Object.assign({}, opts.prefill || {});
    const li = opts.fromListItem || null;
    if (!S.spaces.length) { toast(t('Create a space first.'), 'error'); return; }
    const v = it ? Object.assign({}, it) : {
      productId: pre.productId || (li && li.productId) || null,
      name: pre.name || (li && li.name) || '', icon: pre.icon || (li && li.icon) || null,
      quantity: pre.quantity ?? (li ? li.quantity : null), unit: pre.unit || (li && li.unit) || 'pz',
      categoryId: pre.categoryId ?? null, brand: pre.brand || '', nutriscore: pre.nutriscore || null, barcode: pre.barcode || '',
      expiryDate: '', expiryType: pre.expiryType || 'use_by', notes: pre.notes || (li && li.notes) || '',
      spaceId: opts.spaceId || (S.spaces[0] && S.spaces[0].id),
    };
    const p0 = productById(v.productId);
    if (!v.icon) v.icon = p0 ? p0.icon : 'generic';
    if (v.categoryId == null && p0) v.categoryId = p0.categoryId;

    const space = select(S.spaces.map(sp => ({ value: sp.id, label: sp.name })), v.spaceId);
    const name = input({ value: v.name, maxlength: 80 });
    const qty = input({ type: 'number', inputmode: 'decimal', min: 0, step: 'any', value: v.quantity != null ? String(v.quantity) : '' });
    const unit = unitSelect(v.unit);
    const date = input({ type: 'date', value: v.expiryDate || '' });
    let expType = v.expiryType || 'use_by';
    let ns = v.nutriscore;
    let iconSel = v.icon;
    const brand = input({ value: v.brand || '', maxlength: 80 });
    const cat = select([{ value: '', label: '—' }].concat(S.catalog.categories.map(c => ({ value: c.id, label: c.name }))), v.categoryId ?? '');
    const notes = textarea({ value: v.notes || '', maxlength: 500, rows: 2 });
    const barcode = input({ value: v.barcode || '', inputmode: 'numeric', maxlength: 32 });
    const open = !!(it || v.brand || v.barcode);
    const more = h('div', { hidden: !open },
      h('div.field-row', field(t('Brand'), brand), field(t('Category'), cat)),
      h('div.label', t('Icon')), iconPicker('products', UI.iconNames('products'), iconSel, x => { iconSel = x; }, true),
      field(t('Barcode'), barcode), field(t('Notes'), notes));
    const moreToggle = btn(t('More details'), { kind: 'text', icon: 'chevronDown', onclick: () => { more.hidden = false; moreToggle.remove(); } });
    if (open) moreToggle.hidden = true;

    const title = it ? t('Edit product') : li ? t('Move to a space') : t('New product');
    const save = btn(it ? t('Save') : li ? t('Move') : t('Add'), { icon: li ? 'move' : 'check', onclick: async () => {
      if (!name.value.trim()) { toast(t('Enter the product name.'), 'error'); name.focus(); return; }
      const payload = {
        id: it ? it.id : 0, spaceId: Number(space.value), productId: matchProduct(name.value, v.productId, v.name),
        name: name.value, quantity: qty.value, unit: unit.value, expiryDate: date.value, expiryType: expType,
        nutriscore: ns, brand: brand.value, categoryId: cat.value || null, icon: iconSel, barcode: barcode.value, notes: notes.value,
      };
      if (li) { payload.id = li.id; await act('listitem.tospace', payload, save); toast(t('“{name}” moved to {space}', { name: payload.name, space: spaceById(payload.spaceId).name })); }
      else { await act('item.save', payload, save); toast(it ? t('Product updated') : t('“{name}” added', { name: payload.name })); }
      s.close();
    } });
    const s = sheet({
      title,
      body: [
        field(t('Name'), name), field(t('Space'), space),
        h('div.field-row', field(t('Quantity'), qty), field(t('Unit'), unit)),
        field(t('Expiry date'), date),
        seg([['use_by', t('Use by')], ['best_before', t('Best before')]], expType, x => { expType = x; }),
        h('div.label', t('Nutri-Score (optional)')), nutriPicker(ns, x => { ns = x; }),
        moreToggle, more,
      ],
      actions: [cancelBtn(), save],
    });
  }

  /** Keeps the catalog link when the name matches a catalog product or was pre-matched by barcode scan. */
  function matchProduct(name, productId, initialName) {
    const n = norm(name);
    const p = productById(productId);
    if (p && (norm(p.name) === n || norm(p.nameBase) === n || (initialName && norm(initialName) === n))) return p.id;
    const q = S.catalog.products.find(x => norm(x.name) === n || norm(x.nameBase) === n);
    return q ? q.id : null;
  }

  // ---------- Barcode scanner (Multi-engine: Native BarcodeDetector + Sub-pixel 1D + ZXing + Open Food Facts) ----------

  let zxingPromise = null;
  /** Loads the ZXing barcode reader (self-hosted) for browsers without the native BarcodeDetector. */
  function loadZxing() {
    if (!zxingPromise) {
      zxingPromise = new Promise((resolve, reject) => {
        if (window.ZXing) return resolve(window.ZXing);
        const sc = document.createElement('script');
        sc.src = 'assets/vendor/zxing-0.21.3.min.js';
        sc.onload = () => (window.ZXing ? resolve(window.ZXing) : reject(new Error('ZXing')));
        sc.onerror = () => {
          // Fallback to CDN if self-hosted file cannot be loaded
          const cdn = document.createElement('script');
          cdn.src = 'https://unpkg.com/@zxing/library@0.21.3/umd/index.min.js';
          cdn.onload = () => (window.ZXing ? resolve(window.ZXing) : reject(new Error('ZXing')));
          cdn.onerror = () => { zxingPromise = null; reject(new Error('ZXing')); };
          document.head.appendChild(cdn);
        };
        document.head.appendChild(sc);
      });
    }
    return zxingPromise;
  }

  async function nativeDetector() {
    if (!('BarcodeDetector' in window)) return null;
    try {
      const formats = await window.BarcodeDetector.getSupportedFormats();
      const wanted = ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128'].filter(f => formats.includes(f));
      return wanted.length ? new window.BarcodeDetector({ formats: wanted }) : null;
    } catch (e) { return null; }
  }

  // --- Sub-pixel 1D EAN-13 / UPC-A / EAN-8 / UPC-E tables & decoders ---
  const EAN_L = [
    [3, 2, 1, 1], [2, 2, 2, 1], [2, 1, 2, 2], [1, 4, 1, 1], [1, 1, 3, 2],
    [1, 2, 3, 1], [1, 1, 1, 4], [1, 3, 1, 2], [1, 2, 1, 3], [3, 1, 1, 2],
  ];
  const EAN_G = EAN_L.map(p => [p[3], p[2], p[1], p[0]]);
  const EAN_LG = EAN_L.concat(EAN_G);
  const EAN_T = EAN_LG.map(p => [p[0] + p[1], p[1] + p[2], p[2] + p[3]]);
  const EAN13_FIRST = [0, 11, 13, 14, 19, 25, 28, 21, 22, 26];
  const UPCE_P0 = [56, 52, 50, 49, 44, 38, 35, 42, 41, 37];
  const UPCE_P1 = [7, 11, 13, 14, 19, 25, 28, 21, 22, 26];
  const SHARP_SCALES = [[1.5, 2, 3], [2.5, 3, 5], [3.2, 6, 10]];
  const SCAN_Y_FRACS = [0.50, 0.45, 0.55, 0.40, 0.60, 0.34, 0.66, 0.28, 0.72, 0.22, 0.78, 0.16, 0.84, 0.48, 0.52];

  /** Verifies the standard GS1 modulo-10 check digit for EAN-8, UPC-A, EAN-13 and GTIN-14 codes. */
  function checkEanChecksum(code) {
    if (!code || !/^\d+$/.test(code)) return false;
    const n = code.length;
    if (n !== 8 && n !== 12 && n !== 13 && n !== 14) return false;
    let sum = 0;
    for (let i = n - 2; i >= 0; i--) {
      const d = code.charCodeAt(i) - 48;
      sum += (((n - 2 - i) & 1) === 0 ? 3 : 1) * d;
    }
    return ((10 - (sum % 10)) % 10) === (code.charCodeAt(n - 1) - 48);
  }

  /** Expands an 8-digit UPC-E string (ns + 6 digits + check) into its 12-digit UPC-A equivalent. */
  function expandUpceToUpca(upce) {
    if (!upce || upce.length !== 8) return '';
    const ns = upce[0], d = upce.slice(1, 7), chk = upce[7], last = d.charCodeAt(5) - 48;
    let body;
    if (last <= 2) body = d.slice(0, 2) + d[5] + '0000' + d.slice(2, 5);
    else if (last === 3) body = d.slice(0, 3) + '00000' + d.slice(3, 5);
    else if (last === 4) body = d.slice(0, 4) + '00000' + d[4];
    else body = d.slice(0, 5) + '0000' + d[5];
    return ns + body + chk;
  }

  /** Validates a raw barcode string before accepting it from any engine. */
  function isValidBarcode(raw) {
    const c = String(raw || '').replace(/\D/g, '');
    if (c.length < 6 || c.length > 14) return false;
    if (c.length === 13 || c.length === 12 || c.length === 14) return checkEanChecksum(c);
    if (c.length === 8) return checkEanChecksum(c) || checkEanChecksum(expandUpceToUpca(c));
    return true;
  }

  /** Decodes EAN-13 / UPC-A candidates across all black-run positions of a sub-pixel run-length array. */
  function decodeRunsEAN13(runs, firstBlack, onHit) {
    const limit = runs.length - 58;
    for (let i = firstBlack; i < limit; i += 2) {
      const modS = (runs[i] + runs[i + 1] + runs[i + 2]) / 3;
      const modM = (runs[i + 27] + runs[i + 28] + runs[i + 29] + runs[i + 30] + runs[i + 31]) / 5;
      const modE = (runs[i + 56] + runs[i + 57] + runs[i + 58]) / 3;
      if (modS < 0.85 || modM < 0.85 || modE < 0.85) continue;
      if (Math.abs(modS - modM) > 0.45 * modM || Math.abs(modE - modM) > 0.45 * modM) continue;
      const loG = 0.22 * modM, hiG = 2.25 * modM;
      if (runs[i] < loG || runs[i] > hiG || runs[i + 1] < loG || runs[i + 1] > hiG || runs[i + 2] < loG || runs[i + 2] > hiG ||
          runs[i + 27] < loG || runs[i + 27] > hiG || runs[i + 28] < loG || runs[i + 28] > hiG || runs[i + 29] < loG || runs[i + 29] > hiG ||
          runs[i + 30] < loG || runs[i + 30] > hiG || runs[i + 31] < loG || runs[i + 31] > hiG ||
          runs[i + 56] < loG || runs[i + 56] > hiG || runs[i + 57] < loG || runs[i + 57] > hiG || runs[i + 58] < loG || runs[i + 58] > hiG) continue;

      const bAvg = (runs[i] + runs[i + 2] + runs[i + 28] + runs[i + 30] + runs[i + 56] + runs[i + 58]) / 6;
      const wAvg = (runs[i + 1] + runs[i + 27] + runs[i + 29] + runs[i + 31] + runs[i + 57]) / 5;
      const modG = (bAvg + wAvg) * 0.5;
      const bias = modG > 0 ? Math.max(-0.42, Math.min(0.42, (bAvg - wAvg) / (2 * modG))) : 0;

      let parity = 0, totalErr = 0, ok = true, code = '';
      for (let d = 0; d < 6; d++) {
        const idx = i + 3 + (d << 2);
        const r0 = runs[idx], r1 = runs[idx + 1], r2 = runs[idx + 2], r3 = runs[idx + 3];
        const tot = r0 + r1 + r2 + r3;
        const expMod = modS + (modM - modS) * ((d + 0.5) / 6);
        if (Math.abs(tot - 7 * expMod) > 1.85 * expMod) { ok = false; break; }
        const sc = 7 / tot;
        const n0 = r0 * sc + bias, n1 = r1 * sc - bias, n2 = r2 * sc + bias, n3 = r3 * sc - bias;
        const t1 = (r0 + r1) * sc, t2 = (r1 + r2) * sc, t3 = (r2 + r3) * sc;
        let bestK = -1, bestErr = 1e9;
        for (let k = 0; k < 20; k++) {
          const p = EAN_LG[k], pt = EAN_T[k];
          const dt1 = t1 - pt[0], dt2 = t2 - pt[1], dt3 = t3 - pt[2];
          const dr0 = n0 - p[0], dr1 = n1 - p[1], dr2 = n2 - p[2], dr3 = n3 - p[3];
          const err = 0.65 * (dt1 * dt1 + dt2 * dt2 + dt3 * dt3) + 0.35 * (dr0 * dr0 + dr1 * dr1 + dr2 * dr2 + dr3 * dr3);
          if (err < bestErr) { bestErr = err; bestK = k; }
        }
        if (bestErr > 1.35) { ok = false; break; }
        totalErr += bestErr;
        code += String(bestK % 10);
        if (bestK >= 10) parity |= (1 << (5 - d));
      }
      if (!ok) continue;
      const firstDigit = EAN13_FIRST.indexOf(parity);
      if (firstDigit < 0) continue;

      for (let d = 0; d < 6; d++) {
        const idx = i + 32 + (d << 2);
        const r0 = runs[idx], r1 = runs[idx + 1], r2 = runs[idx + 2], r3 = runs[idx + 3];
        const tot = r0 + r1 + r2 + r3;
        const expMod = modM + (modE - modM) * ((d + 0.5) / 6);
        if (Math.abs(tot - 7 * expMod) > 1.85 * expMod) { ok = false; break; }
        const sc = 7 / tot;
        const n0 = r0 * sc - bias, n1 = r1 * sc + bias, n2 = r2 * sc - bias, n3 = r3 * sc + bias;
        const t1 = (r0 + r1) * sc, t2 = (r1 + r2) * sc, t3 = (r2 + r3) * sc;
        let bestK = -1, bestErr = 1e9;
        for (let k = 0; k < 10; k++) {
          const p = EAN_L[k], pt = EAN_T[k];
          const dt1 = t1 - pt[0], dt2 = t2 - pt[1], dt3 = t3 - pt[2];
          const dr0 = n0 - p[0], dr1 = n1 - p[1], dr2 = n2 - p[2], dr3 = n3 - p[3];
          const err = 0.65 * (dt1 * dt1 + dt2 * dt2 + dt3 * dt3) + 0.35 * (dr0 * dr0 + dr1 * dr1 + dr2 * dr2 + dr3 * dr3);
          if (err < bestErr) { bestErr = err; bestK = k; }
        }
        if (bestErr > 1.35) { ok = false; break; }
        totalErr += bestErr;
        code += String(bestK);
      }
      if (!ok) continue;
      const avgErr = totalErr / 12;
      if (avgErr > 0.48) continue;
      const qLeft = i === 0 || runs[i - 1] >= 2.2 * modS;
      const qRight = i + 59 >= runs.length || runs[i + 59] >= 2.2 * modE;
      if (!qLeft && !qRight) continue;
      const full = String(firstDigit) + code;
      if (checkEanChecksum(full)) {
        onHit(full, qLeft && qRight ? avgErr * 0.75 : avgErr + 0.08);
      }
    }
  }

  /** Decodes EAN-8 candidates across all black-run positions of a sub-pixel run-length array. */
  function decodeRunsEAN8(runs, firstBlack, onHit) {
    const limit = runs.length - 42;
    for (let i = firstBlack; i < limit; i += 2) {
      const modS = (runs[i] + runs[i + 1] + runs[i + 2]) / 3;
      const modM = (runs[i + 19] + runs[i + 20] + runs[i + 21] + runs[i + 22] + runs[i + 23]) / 5;
      const modE = (runs[i + 40] + runs[i + 41] + runs[i + 42]) / 3;
      if (modS < 0.9 || modM < 0.9 || modE < 0.9) continue;
      if (Math.abs(modS - modM) > 0.42 * modM || Math.abs(modE - modM) > 0.42 * modM) continue;
      // Require left & right quiet zones so an inner sub-window of a 13-digit EAN-13 is never misread as EAN-8
      const qLeft = i === 0 || runs[i - 1] >= 2.8 * modS;
      const qRight = i + 43 >= runs.length || runs[i + 43] >= 2.8 * modE;
      if (!qLeft || !qRight) continue;
      const loG = 0.25 * modM, hiG = 2.15 * modM;
      if (runs[i] < loG || runs[i] > hiG || runs[i + 1] < loG || runs[i + 1] > hiG || runs[i + 2] < loG || runs[i + 2] > hiG ||
          runs[i + 19] < loG || runs[i + 19] > hiG || runs[i + 20] < loG || runs[i + 20] > hiG || runs[i + 21] < loG || runs[i + 21] > hiG ||
          runs[i + 22] < loG || runs[i + 22] > hiG || runs[i + 23] < loG || runs[i + 23] > hiG ||
          runs[i + 40] < loG || runs[i + 40] > hiG || runs[i + 41] < loG || runs[i + 41] > hiG || runs[i + 42] < loG || runs[i + 42] > hiG) continue;

      const bAvg = (runs[i] + runs[i + 2] + runs[i + 20] + runs[i + 22] + runs[i + 40] + runs[i + 42]) / 6;
      const wAvg = (runs[i + 1] + runs[i + 19] + runs[i + 21] + runs[i + 23] + runs[i + 41]) / 5;
      const modG = (bAvg + wAvg) * 0.5;
      const bias = modG > 0 ? Math.max(-0.4, Math.min(0.4, (bAvg - wAvg) / (2 * modG))) : 0;

      let totalErr = 0, ok = true, code = '';
      for (let d = 0; d < 4; d++) {
        const idx = i + 3 + (d << 2);
        const r0 = runs[idx], r1 = runs[idx + 1], r2 = runs[idx + 2], r3 = runs[idx + 3];
        const tot = r0 + r1 + r2 + r3;
        const expMod = modS + (modM - modS) * ((d + 0.5) / 4);
        if (Math.abs(tot - 7 * expMod) > 1.75 * expMod) { ok = false; break; }
        const sc = 7 / tot;
        const n0 = r0 * sc + bias, n1 = r1 * sc - bias, n2 = r2 * sc + bias, n3 = r3 * sc - bias;
        const t1 = (r0 + r1) * sc, t2 = (r1 + r2) * sc, t3 = (r2 + r3) * sc;
        let bestK = -1, bestErr = 1e9;
        for (let k = 0; k < 10; k++) {
          const p = EAN_L[k], pt = EAN_T[k];
          const dt1 = t1 - pt[0], dt2 = t2 - pt[1], dt3 = t3 - pt[2];
          const dr0 = n0 - p[0], dr1 = n1 - p[1], dr2 = n2 - p[2], dr3 = n3 - p[3];
          const err = 0.65 * (dt1 * dt1 + dt2 * dt2 + dt3 * dt3) + 0.35 * (dr0 * dr0 + dr1 * dr1 + dr2 * dr2 + dr3 * dr3);
          if (err < bestErr) { bestErr = err; bestK = k; }
        }
        if (bestErr > 1.2) { ok = false; break; }
        totalErr += bestErr;
        code += String(bestK);
      }
      if (!ok) continue;
      for (let d = 0; d < 4; d++) {
        const idx = i + 24 + (d << 2);
        const r0 = runs[idx], r1 = runs[idx + 1], r2 = runs[idx + 2], r3 = runs[idx + 3];
        const tot = r0 + r1 + r2 + r3;
        const expMod = modM + (modE - modM) * ((d + 0.5) / 4);
        if (Math.abs(tot - 7 * expMod) > 1.75 * expMod) { ok = false; break; }
        const sc = 7 / tot;
        const n0 = r0 * sc - bias, n1 = r1 * sc + bias, n2 = r2 * sc - bias, n3 = r3 * sc + bias;
        const t1 = (r0 + r1) * sc, t2 = (r1 + r2) * sc, t3 = (r2 + r3) * sc;
        let bestK = -1, bestErr = 1e9;
        for (let k = 0; k < 10; k++) {
          const p = EAN_L[k], pt = EAN_T[k];
          const dt1 = t1 - pt[0], dt2 = t2 - pt[1], dt3 = t3 - pt[2];
          const dr0 = n0 - p[0], dr1 = n1 - p[1], dr2 = n2 - p[2], dr3 = n3 - p[3];
          const err = 0.65 * (dt1 * dt1 + dt2 * dt2 + dt3 * dt3) + 0.35 * (dr0 * dr0 + dr1 * dr1 + dr2 * dr2 + dr3 * dr3);
          if (err < bestErr) { bestErr = err; bestK = k; }
        }
        if (bestErr > 1.2) { ok = false; break; }
        totalErr += bestErr;
        code += String(bestK);
      }
      if (!ok) continue;
      const avgErr = totalErr / 8;
      if (avgErr > 0.38) continue;
      if (checkEanChecksum(code)) {
        onHit(code, avgErr * 0.85);
      }
    }
  }

  /** Decodes UPC-E candidates across all black-run positions of a sub-pixel run-length array. */
  function decodeRunsUPCE(runs, firstBlack, onHit) {
    const limit = runs.length - 32;
    for (let i = firstBlack; i < limit; i += 2) {
      const modS = (runs[i] + runs[i + 1] + runs[i + 2]) / 3;
      const modE = (runs[i + 27] + runs[i + 28] + runs[i + 29] + runs[i + 30] + runs[i + 31] + runs[i + 32]) / 6;
      if (modS < 0.9 || modE < 0.9 || Math.abs(modS - modE) > 0.4 * modE) continue;
      const qLeft = i === 0 || runs[i - 1] >= 2.8 * modS;
      const qRight = i + 33 >= runs.length || runs[i + 33] >= 2.8 * modE;
      if (!qLeft || !qRight) continue;
      const modM = 0.5 * (modS + modE), loG = 0.25 * modM, hiG = 2.1 * modM;
      if (runs[i] < loG || runs[i] > hiG || runs[i + 1] < loG || runs[i + 1] > hiG || runs[i + 2] < loG || runs[i + 2] > hiG ||
          runs[i + 27] < loG || runs[i + 27] > hiG || runs[i + 28] < loG || runs[i + 28] > hiG || runs[i + 29] < loG || runs[i + 29] > hiG ||
          runs[i + 30] < loG || runs[i + 30] > hiG || runs[i + 31] < loG || runs[i + 31] > hiG || runs[i + 32] < loG || runs[i + 32] > hiG) continue;

      const bAvg = (runs[i] + runs[i + 2] + runs[i + 28] + runs[i + 30] + runs[i + 32]) / 5;
      const wAvg = (runs[i + 1] + runs[i + 27] + runs[i + 29] + runs[i + 31]) / 4;
      const modG = (bAvg + wAvg) * 0.5;
      const bias = modG > 0 ? Math.max(-0.4, Math.min(0.4, (bAvg - wAvg) / (2 * modG))) : 0;

      let parity = 0, totalErr = 0, ok = true, d6 = '';
      for (let d = 0; d < 6; d++) {
        const idx = i + 3 + (d << 2);
        const r0 = runs[idx], r1 = runs[idx + 1], r2 = runs[idx + 2], r3 = runs[idx + 3];
        const tot = r0 + r1 + r2 + r3;
        const expMod = modS + (modE - modS) * ((d + 0.5) / 6);
        if (Math.abs(tot - 7 * expMod) > 1.7 * expMod) { ok = false; break; }
        const sc = 7 / tot;
        const n0 = r0 * sc + bias, n1 = r1 * sc - bias, n2 = r2 * sc + bias, n3 = r3 * sc - bias;
        const t1 = (r0 + r1) * sc, t2 = (r1 + r2) * sc, t3 = (r2 + r3) * sc;
        let bestK = -1, bestErr = 1e9;
        for (let k = 0; k < 20; k++) {
          const p = EAN_LG[k], pt = EAN_T[k];
          const dt1 = t1 - pt[0], dt2 = t2 - pt[1], dt3 = t3 - pt[2];
          const dr0 = n0 - p[0], dr1 = n1 - p[1], dr2 = n2 - p[2], dr3 = n3 - p[3];
          const err = 0.65 * (dt1 * dt1 + dt2 * dt2 + dt3 * dt3) + 0.35 * (dr0 * dr0 + dr1 * dr1 + dr2 * dr2 + dr3 * dr3);
          if (err < bestErr) { bestErr = err; bestK = k; }
        }
        if (bestErr > 1.15) { ok = false; break; }
        totalErr += bestErr;
        d6 += String(bestK % 10);
        if (bestK >= 10) parity |= (1 << (5 - d));
      }
      if (!ok) continue;
      const avgErr = totalErr / 6;
      if (avgErr > 0.34) continue;
      let ns = -1, chk = UPCE_P0.indexOf(parity);
      if (chk >= 0) ns = 0;
      else { chk = UPCE_P1.indexOf(parity); if (chk >= 0) ns = 1; }
      if (ns < 0) continue;
      const upce = String(ns) + d6 + String(chk);
      if (checkEanChecksum(expandUpceToUpca(upce))) {
        onHit(upce, avgErr * 0.9);
      }
    }
  }

  /** Extracts sub-pixel run lengths from a 1D luminance scanline across multiple sharpening scales and decodes 1D barcodes. */
  function scanLine1D(row, n0, onHit) {
    if (n0 < 80) return;
    const n = n0 << 1;
    const up = new Float32Array(n);
    for (let x = 0; x < n0; x++) {
      const c = row[x];
      up[x << 1] = c;
      const xm1 = row[x > 0 ? x - 1 : 0];
      const xp1 = row[x + 1 < n0 ? x + 1 : n0 - 1];
      const xp2 = row[x + 2 < n0 ? x + 2 : n0 - 1];
      up[(x << 1) + 1] = (-xm1 + 9 * c + 9 * xp1 - xp2) * 0.0625;
    }

    // O(1) symmetric block min/max envelope to cancel illumination gradients & glare
    const B = Math.max(6, n >> 6);
    const nb = Math.ceil(n / B);
    const bmin = new Float32Array(nb).fill(255);
    const bmax = new Float32Array(nb);
    for (let i = 0; i < n; i++) {
      const bi = (i / B) | 0, v = up[i];
      if (v < bmin[bi]) bmin[bi] = v;
      if (v > bmax[bi]) bmax[bi] = v;
    }
    const midArr = new Float32Array(n);
    const rngArr = new Float32Array(n);
    for (let x = 0; x < n; x++) {
      const bi = (x / B) | 0;
      const l0 = bi > 4 ? bi - 4 : 0;
      const r0 = bi + 4 < nb ? bi + 4 : nb - 1;
      let loL = up[x], hiL = up[x], loR = up[x], hiR = up[x];
      for (let k = l0; k <= bi; k++) { if (bmin[k] < loL) loL = bmin[k]; if (bmax[k] > hiL) hiL = bmax[k]; }
      for (let k = bi; k <= r0; k++) { if (bmin[k] < loR) loR = bmin[k]; if (bmax[k] > hiR) hiR = bmax[k]; }
      const lo = 0.5 * (loL + loR), hi = 0.5 * (hiL + hiR);
      midArr[x] = 0.5 * (lo + hi);
      rngArr[x] = hi - lo;
    }

    // Deduplicate hits within the same scanline across sharpening scales so one noisy line cannot self-confirm
    let bestLineCode = null, bestLineErr = 1e9;
    const lineHit = (code, err) => {
      const rankErr = code.length >= 12 ? err : err + 0.08;
      if (rankErr < bestLineErr) { bestLineCode = code; bestLineErr = rankErr; }
    };

    const diff = new Float32Array(n);
    for (let sIdx = 0; sIdx < SHARP_SCALES.length; sIdx++) {
      const [gain, r1, r2] = SHARP_SCALES[sIdx];
      for (let x = 0; x < n; x++) {
        if (rngArr[x] <= 8) { diff[x] = -10; continue; }
        const c = up[x];
        const blur = (up[x >= r2 ? x - r2 : 0] + 2 * up[x >= r1 ? x - r1 : 0] + 2 * c + 2 * up[x + r1 < n ? x + r1 : n - 1] + up[x + r2 < n ? x + r2 : n - 1]) * 0.125;
        const sharp = c + gain * (c - blur);
        diff[x] = midArr[x] - sharp;
      }

      const runs = [];
      let prevEdge = 0;
      const firstState = diff[0] > 0 ? 1 : 0;
      let cur = firstState;
      for (let x = 1; x < n; x++) {
        const s = diff[x] > 0 ? 1 : 0;
        if (s !== cur) {
          const d0 = diff[x - 1], d1 = diff[x];
          let frac = d0 !== d1 ? d0 / (d0 - d1) : 0.5;
          if (frac < 0) frac = 0; else if (frac > 1) frac = 1;
          const edge = (x - 1) + frac;
          runs.push(edge - prevEdge);
          prevEdge = edge;
          cur = s;
        }
      }
      runs.push(n - prevEdge);
      if (runs.length < 33) continue;

      const fb = firstState === 1 ? 0 : 1;
      decodeRunsEAN13(runs, fb, lineHit);
      decodeRunsEAN8(runs, fb, lineHit);
      decodeRunsUPCE(runs, fb, lineHit);

      // Upside-down (180°) pass by reversing the run-length array
      const rev = runs.slice().reverse();
      const fbRev = (fb + runs.length - 1) & 1;
      decodeRunsEAN13(rev, fbRev, lineHit);
      decodeRunsEAN8(rev, fbRev, lineHit);
      decodeRunsUPCE(rev, fbRev, lineHit);
    }
    if (bestLineCode) onHit(bestLineCode, bestLineErr);
  }

  /** Runs multi-band horizontal, diagonal and vertical sub-pixel 1D decoding on a grayscale buffer. */
  function decode1DFromGray(gray, w, h, onHit) {
    const row = new Float32Array(w);
    // 1. Horizontal 5-row vertically-averaged bands
    for (let i = 0; i < SCAN_Y_FRACS.length; i++) {
      const yc = Math.max(2, Math.min(h - 3, (h * SCAN_Y_FRACS[i]) | 0));
      const r0 = (yc - 2) * w, r1 = (yc - 1) * w, r2 = yc * w, r3 = (yc + 1) * w, r4 = (yc + 2) * w;
      for (let x = 0; x < w; x++) {
        row[x] = (gray[r0 + x] + 2 * gray[r1 + x] + 2 * gray[r2 + x] + 2 * gray[r3 + x] + gray[r4 + x]) * 0.125;
      }
      scanLine1D(row, w, onHit);
    }
    // 2. Diagonal bands (±10° and ±18° tilt across the center) for angled packages
    const slopes = [-0.18, 0.18, -0.32, 0.32];
    const yc = h >> 1;
    for (let s = 0; s < slopes.length; s++) {
      const k = slopes[s];
      for (let x = 0; x < w; x++) {
        const y = Math.max(1, Math.min(h - 2, Math.round(yc + (x - (w >> 1)) * k)));
        row[x] = (gray[(y - 1) * w + x] + 2 * gray[y * w + x] + gray[(y + 1) * w + x]) * 0.25;
      }
      scanLine1D(row, w, onHit);
    }
    // 3. Vertical 5-column averaged bands for vertically held products
    if (h >= 100) {
      const col = new Float32Array(h);
      const xFracs = [0.50, 0.40, 0.60, 0.30, 0.70];
      for (let i = 0; i < xFracs.length; i++) {
        const xc = Math.max(2, Math.min(w - 3, (w * xFracs[i]) | 0));
        for (let y = 0; y < h; y++) {
          const base = y * w + xc;
          col[y] = (gray[base - 2] + 2 * gray[base - 1] + 2 * gray[base] + 2 * gray[base + 1] + gray[base + 2]) * 0.125;
        }
        scanLine1D(col, h, onHit);
      }
    }
  }

  /** Builds a locally contrast-normalized luminance buffer and runs ZXing's MultiFormatOneDReader (adds Code-128 & extra 1D pass). */
  function decodeWithZxing(zxOneD, ZX, gray, w, h, hints, onHit) {
    if (!zxOneD || !ZX) return;
    try {
      const normBuf = new Uint8ClampedArray(w * h);
      const B = Math.max(8, w >> 5);
      const nb = Math.ceil(w / B);
      const bmin = new Uint8Array(nb);
      const bmax = new Uint8Array(nb);
      for (let y = 0; y < h; y++) {
        const y0 = (y > 0 ? y - 1 : 0) * w, y1 = y * w, y2 = (y + 1 < h ? y + 1 : h - 1) * w;
        bmin.fill(255);
        bmax.fill(0);
        for (let x = 0; x < w; x++) {
          const v = (gray[y0 + x] + 2 * gray[y1 + x] + gray[y2 + x]) >> 2;
          normBuf[y1 + x] = v;
          const bi = (x / B) | 0;
          if (v < bmin[bi]) bmin[bi] = v;
          if (v > bmax[bi]) bmax[bi] = v;
        }
        for (let x = 0; x < w; x++) {
          const bi = (x / B) | 0;
          const l0 = bi > 2 ? bi - 2 : 0, r0 = bi + 2 < nb ? bi + 2 : nb - 1;
          let lo = 255, hi = 0;
          for (let k = l0; k <= r0; k++) { if (bmin[k] < lo) lo = bmin[k]; if (bmax[k] > hi) hi = bmax[k]; }
          const span = hi - lo;
          const v = normBuf[y1 + x];
          normBuf[y1 + x] = span > 10 ? ((v - lo) * 255 / span) : 255;
        }
      }
      const src = new ZX.RGBLuminanceSource(normBuf, w, h);
      const bmp = new ZX.BinaryBitmap(new ZX.GlobalHistogramBinarizer(src));
      const res = zxOneD.decode(bmp, hints);
      if (res && res.getText()) {
        const txt = res.getText();
        if (isValidBarcode(txt)) onHit(txt.replace(/\D/g, ''), 0.25);
      }
    } catch (e) { /* NotFoundException on this frame */ }
  }

  /** Returns normalized barcode variants (EAN-13, UPC-A, and expanded UPC-E) to maximize database/Open Food Facts hits. */
  function barcodeVariants(code) {
    const c = String(code || '').replace(/\D/g, '');
    const list = [c];
    if (c.length === 12) list.push('0' + c);
    else if (c.length === 13 && c[0] === '0') list.push(c.slice(1));
    else if (c.length === 8 && (c[0] === '0' || c[0] === '1')) {
      const upca = expandUpceToUpca(c);
      if (upca.length === 12 && checkEanChecksum(upca)) {
        list.push(upca, '0' + upca);
      }
    }
    return [...new Set(list)];
  }

  /** Extracts structured product info from a raw Open Food Facts v2 `product` payload. */
  function parseOffProduct(p, barcode) {
    if (!p || typeof p !== 'object') return null;
    const brand = String((p.brands || '').split(',')[0] || '').trim().slice(0, 80);
    const nameCands = [p.product_name_it, p.product_name, p.product_name_en, p.abbreviated_product_name, p.generic_name_it, p.generic_name];
    let name = '';
    for (const nc of nameCands) {
      const s = String(nc || '').trim();
      if (s) { name = s.slice(0, 80); break; }
    }
    const genericName = String(p.generic_name_it || p.generic_name || '').trim().slice(0, 120);
    if (!name && brand) name = brand;
    if (brand && norm(name) === 'bueno') name = brand + ' Bueno';
    if (!name) return null;

    const nsRaw = String(p.nutriscore_grade || '').trim().toUpperCase();
    const nutriscore = ['A', 'B', 'C', 'D', 'E'].includes(nsRaw) ? nsRaw : null;

    let quantity = null, unit = null;
    const qStr = String(p.quantity || '');
    const m = qStr.match(/(?:^\s*\d+\s*[x×*]\s*)?([\d.,]+)\s*(kg|g|ml|cl|l)\b/i);
    if (m) {
      quantity = parseFloat(m[1].replace(',', '.'));
      unit = m[2].toLowerCase();
    } else if (p.product_quantity) {
      const pq = parseFloat(p.product_quantity);
      if (pq > 0) {
        quantity = pq;
        const uRaw = String(p.product_quantity_unit || 'g').trim().toLowerCase();
        unit = UNITS.includes(uRaw) ? uRaw : 'g';
      }
    }

    const categories = Array.isArray(p.categories_tags) ? p.categories_tags.map(x => String(x || '').toLowerCase()) : [];
    return { name, genericName, brand, nutriscore, quantity, unit, categories, barcode };
  }

  /** Direct browser fallback to Open Food Facts API v2 (CORS-enabled) when server-side lookup is unavailable or returns not found. */
  async function fetchOpenFoodFactsBrowser(code) {
    const fields = 'product_name,product_name_it,product_name_en,generic_name,generic_name_it,abbreviated_product_name,brands,nutriscore_grade,quantity,product_quantity,product_quantity_unit,categories_tags';
    for (const cand of barcodeVariants(code)) {
      const ctrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
      const timer = ctrl ? setTimeout(() => ctrl.abort(), 5500) : null;
      try {
        const res = await fetch(`https://world.openfoodfacts.org/api/v2/product/${cand}.json?fields=${fields}`, {
          method: 'GET',
          headers: { Accept: 'application/json' },
          signal: ctrl ? ctrl.signal : undefined,
        });
        if (timer) clearTimeout(timer);
        if (!res.ok) continue;
        const data = await res.json();
        if (data && Number(data.status) === 1 && data.product) {
          const parsed = parseOffProduct(data.product, code);
          if (parsed) return parsed;
        }
      } catch (e) {
        if (timer) clearTimeout(timer);
      }
    }
    return null;
  }

  function wordMatch(text, word) {
    if (!text || !word) return false;
    const esc = norm(word).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const rx = new RegExp('(?:^|[^a-z0-9])' + esc + '(?:$|[^a-z0-9])', 'i');
    return rx.test(text);
  }

  /** Keyword/tag rules mapping scanned products to canonical FoodTrack catalog products. */
  const CATALOG_RULES = [
    ['Pasta di semola', ['spaghetti', 'penne', 'fusilli', 'rigatoni', 'farfalle', 'maccheroni', 'tortiglioni', 'bucatini', 'linguine', 'mezze maniche', 'ditalini', 'lasagne', 'tagliatelle', 'paccheri', 'pasta di semola', 'grano duro', 'en:pastas', 'en:spaghetti', 'en:durum-wheat-pasta']],
    ['Pasta all’uovo', ['pasta all uovo', 'tagliatelle all uovo', 'pappardelle', 'tortellini', 'ravioli', 'agnolotti', 'cappelletti']],
    ['Passata di pomodoro', ['passata', 'polpa di pomodoro', 'pomodori pelati', 'pelati', 'concentrato di pomodoro', 'sugo di pomodoro', 'en:passata', 'en:tomato-sauces', 'en:canned-tomatoes']],
    ['Tonno in scatola', ['tonno', 'filetti di tonno', 'rio mare', 'en:tunas', 'en:canned-tunas']],
    ['Biscotti integrali', ['biscotti', 'frollini', 'gocciole', 'macine', 'pan di stelle', 'tarallucci', 'abbracci', 'molinetti', 'campagnole', 'ringo', 'oreo', 'savoiardi', 'wafer', 'baiocchi', 'pavesini', 'biscotto', 'en:biscuits', 'en:cookies']],
    ['Caffè macinato', ['caffe', 'espresso', 'moka', 'cialde', 'capsule', 'qualita rossa', 'lavazza', 'illy', 'kimbo', 'en:coffees']],
    ['Yogurt greco', ['yogurt greco', 'fage', 'en:greek-yogurts']],
    ['Yogurt bianco', ['yogurt', 'kefir', 'skyr', 'actimel', 'en:yogurts']],
    ['Mozzarella di bufala', ['mozzarella', 'burrata', 'stracciatella', 'fiordilatte', 'en:mozzarella']],
    ['Formaggio spalmabile', ['formaggio spalmabile', 'philadelphia', 'stracchino', 'crescenza', 'robiola', 'ricotta', 'mascarpone', 'en:cream-cheeses', 'en:ricotta']],
    ['Parmigiano', ['parmigiano', 'grana padano', 'grattugiato', 'parmigiano reggiano', 'en:parmigiano-reggiano', 'en:grana-padano']],
    ['Latte UHT', ['latte uht', 'lunga conservazione', 'en:uht-milks']],
    ['Latte intero', ['latte intero', 'latte fresco', 'latte parzialmente scremato', 'latte scremato', 'en:milks']],
    ['Olio extravergine d’oliva', ['olio extravergine', 'olio extra vergine', 'olio evo', 'olio di oliva', 'olio d oliva', 'en:olive-oils', 'en:extra-virgin-olive-oils']],
    ['Olio di semi', ['olio di semi', 'olio di girasole', 'olio di mais', 'olio di arachidi']],
    ['Aceto balsamico', ['aceto balsamico', 'aceto di mele', 'aceto di vino', 'aceto', 'glassa', 'en:vinegars']],
    ['Patatine', ['patatine', 'chips', 'croccantelle', 'salatini', 'popcorn', 'taralli', 'cracker', 'gallette', 'en:crisps']],
    ['Pane integrale', ['pane', 'pan bauletto', 'pancarre', 'focaccia', 'baguette', 'ciabatta', 'panini', 'en:breads']],
    ['Piadina', ['piadina', 'piada', 'tortilla', 'en:flatbreads']],
    ['Gelato', ['gelato', 'sorbetto', 'ghiacciolo', 'cornetto', 'magnum', 'coppa del nonno', 'en:ice-creams']],
    ['Prosciutto cotto', ['prosciutto cotto', 'fesa di tacchino', 'mortadella', 'wurstel', 'en:cooked-hams']],
    ['Prosciutto crudo', ['prosciutto crudo', 'speck', 'bresaola', 'culatello', 'en:cured-hams']],
    ['Salame', ['salame', 'salamino', 'soppressata', 'nduja', 'en:salami']],
    ['Carne di pollo', ['pollo', 'petto di pollo', 'tacchino', 'cotoletta', 'nuggets', 'macinato', 'manzo', 'hamburger', 'svizzera', 'salsiccia', 'en:chicken', 'en:poultry', 'en:beef', 'en:meats']],
    ['Pesce fresco', ['merluzzo', 'orata', 'branzino', 'spigola', 'salmone', 'gamberi', 'calamari', 'polpo', 'bastoncini', 'en:fishes', 'en:seafood']],
    ['Salmone affumicato', ['salmone affumicato', 'en:smoked-salmons']],
    ['Birra', ['birra', 'lager', 'ipa', 'pils', 'radler', 'heineken', 'peroni', 'moretti', 'ichnusa', 'en:beers']],
    ['Vino bianco', ['vino bianco', 'prosecco', 'spumante', 'champagne', 'en:white-wines']],
    ['Vino rosso', ['vino rosso', 'chianti', 'barolo', 'primitivo', 'en:red-wines']],
    ['Acqua', ['acqua', 'acqua minerale', 'acqua naturale', 'acqua frizzante', 'en:waters']],
    ['Riso integrale', ['riso', 'carnaroli', 'arborio', 'basmati', 'vialone', 'risotto', 'en:rices']],
    ['Farina', ['farina', 'manitoba', 'semola rimacinata', 'en:flours']]
  ];

  /** Matches a scanned product to FoodTrack's catalog (`productId`, `icon`, `categoryId`, `expiryType`). */
  function matchScannedProduct(p) {
    const out = { productId: p.productId || null, icon: p.icon || null, categoryId: p.categoryId ?? null, expiryType: null };
    const text = norm([p.name || '', p.genericName || '', p.brand || ''].join(' '));
    const cats = Array.isArray(p.categories) ? p.categories : [];

    const catByBase = base => {
      const c = S.catalog.categories.find(x => norm(x.nameBase || x.name) === norm(base));
      return c ? c.id : null;
    };

    // 1. Direct match with a catalog product
    let match = null;
    if (out.productId) match = productById(out.productId);
    if (!match && p.name) {
      const nName = norm(p.name);
      match = S.catalog.products.find(x => norm(x.name) === nName || norm(x.nameBase) === nName);
    }

    // 2. Specific catalog rules
    if (!match) {
      for (const [baseName, keys] of CATALOG_RULES) {
        const hit = keys.some(k => {
          if (k.startsWith('en:') || k.startsWith('fr:')) return cats.includes(k);
          if (k === 'riso' && (wordMatch(text, 'farina di riso') || wordMatch(text, 'latte di riso'))) return false;
          if (k === 'farina' && wordMatch(text, 'biscotti')) return false;
          return wordMatch(text, k);
        });
        if (hit) {
          match = S.catalog.products.find(x => norm(x.nameBase || x.name) === norm(baseName)) || null;
          if (match) break;
        }
      }
    }

    if (match) {
      out.productId = match.id;
      out.icon = out.icon || match.icon;
      if (out.categoryId == null) out.categoryId = match.categoryId;
      if (!p.unit && match.unit) p.unit = match.unit;
    }

    // 3. Fallback category & icon for branded products not in catalog
    if (out.categoryId == null) {
      if (cats.some(c => /sweet-spread|chocolate|biscuit|snack|cookie|confection/i.test(c)) || wordMatch(text, 'nutella') || wordMatch(text, 'cioccolato') || wordMatch(text, 'bueno') || wordMatch(text, 'kinder') || wordMatch(text, 'biscotti')) {
        out.categoryId = catByBase('Snack e dolci');
      } else if (cats.some(c => /sauce|pesto|condiment/i.test(c)) || wordMatch(text, 'pesto') || wordMatch(text, 'salsa') || wordMatch(text, 'maionese') || wordMatch(text, 'ketchup')) {
        out.categoryId = catByBase('Condimenti e spezie');
      } else if (cats.some(c => /beverage|drink|soda|water|juice|coffee|tea/i.test(c)) || wordMatch(text, 'coca cola') || wordMatch(text, 'cola') || wordMatch(text, 'fanta') || wordMatch(text, 'sprite') || wordMatch(text, 'succo') || wordMatch(text, 'bevanda')) {
        out.categoryId = catByBase('Bevande');
      } else if (cats.some(c => /milk|dairy|cheese|yogurt/i.test(c)) || wordMatch(text, 'formaggio') || wordMatch(text, 'latte')) {
        out.categoryId = catByBase('Latticini e uova');
      } else if (cats.some(c => /frozen/i.test(c)) || wordMatch(text, 'surgelato') || wordMatch(text, 'surgelati')) {
        out.categoryId = catByBase('Surgelati');
      } else if (cats.some(c => /meat|pork|beef|poultry/i.test(c)) || wordMatch(text, 'carne')) {
        out.categoryId = catByBase('Carne e salumi');
      } else if (cats.some(c => /fish|seafood/i.test(c)) || wordMatch(text, 'pesce')) {
        out.categoryId = catByBase('Pesce');
      } else if (cats.some(c => /pasta|cereal|bread/i.test(c))) {
        out.categoryId = catByBase('Pasta, riso e cereali');
      }
    }

    // 4. Fallback product icon if still generic
    if (!out.icon) {
      if (wordMatch(text, 'nutella') || wordMatch(text, 'cioccolato') || wordMatch(text, 'kinder') || wordMatch(text, 'cacao') || cats.some(c => /cocoa|chocolate/i.test(c))) out.icon = 'chocolate';
      else if (wordMatch(text, 'pesto') || wordMatch(text, 'salsa') || wordMatch(text, 'sugo') || cats.some(c => /sauce|pesto/i.test(c))) out.icon = 'sauce';
      else if (wordMatch(text, 'cola') || wordMatch(text, 'aranciata') || wordMatch(text, 'limonata') || wordMatch(text, 'succo') || cats.some(c => /juice|soda|carbonated/i.test(c))) out.icon = 'juice';
      else if (wordMatch(text, 'biscotti') || wordMatch(text, 'frollini') || cats.some(c => /biscuit|cookie/i.test(c))) out.icon = 'biscuit';
      else if (wordMatch(text, 'patatine') || wordMatch(text, 'chips') || cats.some(c => /crisps/i.test(c))) out.icon = 'crisps';
      else if (wordMatch(text, 'caffe') || cats.some(c => /coffee/i.test(c))) out.icon = 'coffee';
      else if (wordMatch(text, 'formaggio') || cats.some(c => /cheese/i.test(c))) out.icon = 'cheese';
    }

    // 5. Expiry type
    const catObj = categoryById(out.categoryId);
    const catBase = norm((catObj && (catObj.nameBase || catObj.name)) || '');
    if (['pasta, riso e cereali', 'conserve e scatolame', 'condimenti e spezie', 'bevande', 'snack e dolci', 'surgelati'].includes(catBase)) {
      out.expiryType = 'best_before';
    } else if (['latticini e uova', 'carne e salumi', 'pesce'].includes(catBase)) {
      out.expiryType = 'use_by';
    }
    return out;
  }

  function openScanner(ctx) {
    const code = input({ inputmode: 'numeric', maxlength: 14 });
    const status = h('p.helper.center', t('Starting the camera…'));
    const video = h('video', { playsinline: true, muted: true, autoplay: true });
    const ctrls = h('div.scanner-ctrls');
    const cam = h('div.scanner', video, ctrls);
    let stream = null, stopped = false, busyLookup = false, scanTimer = null, zoomLevel = 1, torchOn = false;

    async function lookup(c) {
      c = String(c || '').replace(/\D/g, '');
      if (c.length < 6) { toast(t('Invalid code.'), 'error'); return; }
      if (busyLookup) return;
      busyLookup = true;
      stop();
      status.textContent = t('Looking up the product…');
      try {
        const variants = barcodeVariants(c);
        let p = null;
        try {
          const d = await Api.get('barcode.lookup&code=' + c);
          if (d && d.found && d.product && d.product.name) p = d.product;
        } catch (e) { /* fall back to direct browser lookup */ }

        if (!p) {
          p = await fetchOpenFoodFactsBrowser(c);
        }

        s.close();
        const prefill = { barcode: c };
        const prev = S.items.find(x => x.barcode && variants.includes(String(x.barcode).replace(/\D/g, '')));
        if (p && p.name) {
          Object.assign(prefill, { name: p.name, brand: p.brand || '', nutriscore: p.nutriscore || null });
          if (p.quantity) { prefill.quantity = p.quantity; prefill.unit = p.unit || 'pz'; }
          const m = matchScannedProduct(p);
          if (m.productId) prefill.productId = m.productId;
          if (m.icon) prefill.icon = m.icon;
          if (m.categoryId != null) prefill.categoryId = m.categoryId;
          if (m.expiryType) prefill.expiryType = m.expiryType;
          if (p.unit && !prefill.unit) prefill.unit = p.unit;
          toast(t('Product found: set the expiry date'));
        } else if (prev) {
          Object.assign(prefill, { productId: prev.productId, name: prev.name, brand: prev.brand, nutriscore: prev.nutriscore, icon: prev.icon, categoryId: prev.categoryId, quantity: prev.quantity, unit: prev.unit, expiryType: prev.expiryType });
          toast(t('Product found: set the expiry date'));
        } else {
          toast(t('Product not found: enter the details manually'));
        }
        setTimeout(() => openItemForm({ spaceId: ctx.spaceId, prefill }), 240);
      } catch (e) { status.textContent = e.message; busyLookup = false; }
    }
    function stop() {
      stopped = true;
      if (scanTimer) { clearTimeout(scanTimer); scanTimer = null; }
      if (stream) { stream.getTracks().forEach(tr => tr.stop()); stream = null; }
      try { video.srcObject = null; } catch (e) { /* ignore */ }
    }
    const retryBox = h('div.btn-row', { hidden: true, style: { justifyContent: 'center' } });
    function noCamera(msg, detail) {
      cam.hidden = true;
      UI.clear(status);
      status.append(msg, detail ? h('small.scan-detail', detail) : '');
      UI.clear(retryBox);
      retryBox.hidden = false;
      retryBox.appendChild(btn(t('Try again'), { kind: 'tonal', icon: 'scan', onclick: () => { retryBox.hidden = true; cam.hidden = false; status.textContent = t('Starting the camera…'); startCamera(); } }));
    }

    const s = sheet({
      title: t('Scan barcode'),
      body: [cam, status, retryBox, field(t('Barcode'), code, { hint: t('Or type the number under the bars') })],
      onclose: stop,
      actions: [cancelBtn(), btn(t('Search'), { icon: 'search', onclick: () => lookup(code.value) })],
    });
    code.addEventListener('keydown', e => { if (e.key === 'Enter') lookup(code.value); });

    if (!window.isSecureContext || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      noCamera(t('The camera needs a secure (HTTPS) connection: type the code manually.'));
      return;
    }
    startCamera();

    /** Opens the rear camera, configures continuous autofocus/zoom/torch, and runs the multi-engine decoder loop. */
    async function startCamera() {
      stopped = false;
      zoomLevel = 1;
      torchOn = false;
      video.style.transform = '';
      UI.clear(ctrls);
      try {
        stream = await getCameraStream();
        if (stopped) return stop();
        video.muted = true;
        video.playsInline = true;
        video.setAttribute('playsinline', '');
        video.srcObject = stream;
        await video.play().catch(() => { /* muted autoplay fallback */ });
        if (stopped) return stop();

        const track = stream.getVideoTracks()[0] || null;
        let caps = {};
        if (track && track.getCapabilities) {
          try { caps = track.getCapabilities() || {}; } catch (e) { caps = {}; }
        }
        if (track && track.applyConstraints) {
          const adv = {};
          if (Array.isArray(caps.focusMode) && caps.focusMode.includes('continuous')) adv.focusMode = 'continuous';
          if (Array.isArray(caps.exposureMode) && caps.exposureMode.includes('continuous')) adv.exposureMode = 'continuous';
          if (Array.isArray(caps.whiteBalanceMode) && caps.whiteBalanceMode.includes('continuous')) adv.whiteBalanceMode = 'continuous';
          if (Object.keys(adv).length) track.applyConstraints({ advanced: [adv] }).catch(() => {});
        }

        // Camera overlay controls: 1x/2x zoom and (when supported) flashlight/torch
        const zoomBtn = h('button.scan-ctrl', {
          type: 'button', title: t('Zoom'), 'aria-label': t('Zoom'),
          onclick: e => {
            e.stopPropagation();
            zoomLevel = zoomLevel === 1 ? 2 : 1;
            zoomBtn.textContent = zoomLevel + '×';
            zoomBtn.classList.toggle('active', zoomLevel > 1);
            if (track && track.applyConstraints && caps.zoom && caps.zoom.max > caps.zoom.min) {
              const targetZ = Math.min(caps.zoom.max, Math.max(caps.zoom.min, zoomLevel * (caps.zoom.min || 1)));
              track.applyConstraints({ advanced: [{ zoom: targetZ }] }).catch(() => {
                video.style.transform = zoomLevel > 1 ? `scale(${zoomLevel})` : '';
              });
            } else {
              video.style.transform = zoomLevel > 1 ? `scale(${zoomLevel})` : '';
            }
          },
        }, '1×');
        ctrls.appendChild(zoomBtn);

        if (caps.torch && track && track.applyConstraints) {
          const torchBtn = h('button.scan-ctrl', {
            type: 'button', title: t('Flash'), 'aria-label': t('Flash'),
            onclick: e => {
              e.stopPropagation();
              torchOn = !torchOn;
              torchBtn.classList.toggle('active', torchOn);
              track.applyConstraints({ advanced: [{ torch: torchOn }] }).catch(() => {});
            },
          }, icon('bulb'));
          ctrls.appendChild(torchBtn);
        }

        // Tap video preview to re-trigger autofocus
        video.onclick = () => {
          if (!track || !track.applyConstraints || !Array.isArray(caps.focusMode)) return;
          if (caps.focusMode.includes('single-shot')) {
            track.applyConstraints({ advanced: [{ focusMode: 'single-shot' }] }).then(() => {
              if (caps.focusMode.includes('continuous')) setTimeout(() => { if (!stopped) track.applyConstraints({ advanced: [{ focusMode: 'continuous' }] }).catch(() => {}); }, 600);
            }).catch(() => {});
          }
        };

        status.textContent = t('Point the camera at the product barcode');

        // Prepare both native BarcodeDetector and ZXing reader in parallel (neither blocks the other)
        const detectorPromise = nativeDetector();
        let zxModule = null, zxOneD = null, zxHints = null;
        loadZxing().then(ZX => {
          if (stopped || !ZX) return;
          zxModule = ZX;
          zxHints = new Map();
          zxHints.set(ZX.DecodeHintType.POSSIBLE_FORMATS, [ZX.BarcodeFormat.EAN_13, ZX.BarcodeFormat.EAN_8, ZX.BarcodeFormat.UPC_A, ZX.BarcodeFormat.UPC_E, ZX.BarcodeFormat.CODE_128]);
          zxHints.set(ZX.DecodeHintType.TRY_HARDER, true);
          zxOneD = new ZX.MultiFormatOneDReader(zxHints);
        }).catch(() => { /* custom sub-pixel 1D engine still runs */ });

        const detector = await detectorPromise;
        if (stopped) return;

        const canvas = document.createElement('canvas');
        let cctx = null;
        try { cctx = canvas.getContext('2d', { willReadFrequently: true }); } catch (e) { cctx = canvas.getContext('2d'); }

        const votes = new Map();
        let frameCount = 0;

        function acceptCode(winner) {
          if (stopped || busyLookup) return true;
          if (navigator.vibrate) navigator.vibrate(60);
          code.value = winner;
          lookup(winner);
          return true;
        }

        function recordVote(candidate, avgErr) {
          if (!candidate || stopped || busyLookup) return false;
          const now = Date.now();
          for (const [k, v] of votes) if (now - v.at > 1600) votes.delete(k);
          const weight = avgErr < 0.22 ? 1.15 : avgErr < 0.38 ? 0.95 : 0.65;
          const cur = votes.get(candidate) || { score: 0, hits: 0, at: now };
          cur.score += weight;
          cur.hits += 1;
          cur.at = now;
          votes.set(candidate, cur);
          if (cur.hits >= 2 && cur.score >= 1.8) return acceptCode(candidate);
          return false;
        }

        const tick = async () => {
          if (stopped || busyLookup) return;
          const vw = video.videoWidth || 0, vh = video.videoHeight || 0;
          if (video.readyState < 2 || vw < 64 || vh < 64) {
            scanTimer = setTimeout(tick, 80);
            return;
          }
          frameCount++;

          // 1. Native BarcodeDetector pass (full video frame)
          if (detector) {
            try {
              const codes = await detector.detect(video);
              for (const hit of codes) {
                if (hit && isValidBarcode(hit.rawValue)) {
                  const cleaned = String(hit.rawValue).replace(/\D/g, '');
                  if (acceptCode(cleaned)) return;
                }
              }
            } catch (e) { /* fall through to ROI + JS engines */ }
          }
          if (stopped || busyLookup) return;

          // 2. Extract high-resolution ROI around the target area (alternating standard & tight zoom crop)
          if (cctx) {
            const tight = zoomLevel > 1 || (frameCount & 1) === 0;
            const cropWFrac = zoomLevel > 1 ? 0.52 : (tight ? 0.68 : 0.88);
            const cropHFrac = zoomLevel > 1 ? 0.36 : (tight ? 0.44 : 0.58);
            const sx = Math.round(vw * (1 - cropWFrac) * 0.5);
            const sy = Math.round(vh * (1 - cropHFrac) * 0.5);
            const sw = Math.max(64, Math.round(vw * cropWFrac));
            const sh = Math.max(48, Math.round(vh * cropHFrac));
            const targetW = Math.min(920, Math.max(640, sw));
            const targetH = Math.max(48, Math.round(sh * (targetW / sw)));

            if (canvas.width !== targetW || canvas.height !== targetH) {
              canvas.width = targetW;
              canvas.height = targetH;
            }
            cctx.drawImage(video, sx, sy, sw, sh, 0, 0, targetW, targetH);

            // 2a. Native BarcodeDetector on cropped/upscaled ROI (acts as 2x digital zoom for native detector)
            if (detector) {
              try {
                const roiCodes = await detector.detect(canvas);
                for (const hit of roiCodes) {
                  if (hit && isValidBarcode(hit.rawValue)) {
                    const cleaned = String(hit.rawValue).replace(/\D/g, '');
                    if (acceptCode(cleaned)) return;
                  }
                }
              } catch (e) { /* ignore */ }
            }
            if (stopped || busyLookup) return;

            // 2b. Convert ROI to perceptual grayscale
            const imgData = cctx.getImageData(0, 0, targetW, targetH).data;
            const pixelCount = targetW * targetH;
            const gray = new Uint8Array(pixelCount);
            for (let p = 0, j = 0; j < pixelCount; p += 4, j++) {
              gray[j] = (imgData[p] * 77 + imgData[p + 1] * 150 + imgData[p + 2] * 29) >> 8;
            }

            // 2c. Multi-scale sub-pixel T-sequence 1D decoder (EAN-13, UPC-A, EAN-8, UPC-E)
            decode1DFromGray(gray, targetW, targetH, recordVote);
            if (stopped || busyLookup) return;

            // 2d. Locally contrast-normalized ZXing MultiFormatOneDReader pass (Code-128 + extra 1D fallback)
            if (zxOneD && zxModule) {
              decodeWithZxing(zxOneD, zxModule, gray, targetW, targetH, zxHints, recordVote);
            }
          }

          if (!stopped && !busyLookup) scanTimer = setTimeout(tick, 90);
        };

        tick();
      } catch (e) {
        if (stream) { stream.getTracks().forEach(tr => tr.stop()); stream = null; }
        if (stopped) return;
        const [msg, detail] = await cameraErrorMessage(e);
        noCamera(msg, detail);
      }
    }
  }

  /** Asks for the rear camera in Full HD with continuous autofocus; falls back gracefully to any available camera. */
  async function getCameraStream() {
    const attempts = [
      { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1920 }, height: { ideal: 1080 }, focusMode: { ideal: 'continuous' } }, audio: false },
      { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false },
      { video: true, audio: false },
    ];
    let lastErr = null;
    for (const constraints of attempts) {
      try {
        return await navigator.mediaDevices.getUserMedia(constraints);
      } catch (e) {
        lastErr = e;
        if (e && (e.name === 'NotAllowedError' || e.name === 'SecurityError')) throw e;
      }
    }
    throw lastErr;
  }

  /** Explains why the camera could not start, telling a real refusal apart from a camera that is busy, missing or blocked. */
  async function cameraErrorMessage(e) {
    const name = (e && e.name) || 'Error';
    const msg = (e && e.message) || '';
    const detail = name + (msg ? ': ' + msg : '');
    if (msg === 'ZXing' || name === 'ZXing') {
      return [t('Barcode reader could not be loaded. Type the code manually or try again.'), detail];
    }
    if (name === 'NotAllowedError' || name === 'SecurityError') {
      let state = '';
      try { state = (await navigator.permissions.query({ name: 'camera' })).state; } catch (x) { /* not supported */ }
      if (state === 'denied') return [t('Camera permission denied: allow it in the browser settings, or type the code manually.'), detail];
      return [t('The camera is blocked by the system: check that the browser is allowed to use the camera in the phone settings (Settings → Apps → browser → Permissions), then try again.'), detail];
    }
    if (name === 'NotReadableError' || name === 'AbortError' || name === 'TrackStartError') return [t('The camera is in use by another app or could not start: close the other apps using it and try again.'), detail];
    if (name === 'NotFoundError' || name === 'OverconstrainedError' || name === 'DevicesNotFoundError') return [t('No camera found on this device: type the code manually.'), detail];
    return [t('Could not start the camera. Type the code manually or try again.'), detail];
  }

  // ===================================================== SHOPPING LISTS

  function viewLists() {
    const body = [];
    if (!S.lists.length) body.push(empty('lists', 'cart', t('No lists'), t('Create a shopping list: you can share it with the people you live with.')));
    body.push(h('div.stack', S.lists.map(l => {
      const items = S.listItems.filter(x => x.listId === l.id);
      const inCart = items.filter(x => x.inCart).length;
      const toBuy = items.length - inCart;
      return cardRow({
        big: true, lead: tile('lists', l.icon, 'lg'), title: l.name, sub: l.notes || null,
        meta: !items.length ? [chip(t('Empty list'), 'neutral')] : [chip(t('{n} to buy', { n: toBuy }), toBuy ? 'ok' : 'neutral'), inCart ? chip(t('{n} in the cart', { n: inCart }), 'neutral', 'cartAdd') : null],
        trail: moreBtn([
          { icon: 'edit', label: t('Edit'), onclick: () => openListForm(l) },
          { icon: 'delete', label: t('Delete'), danger: true, onclick: () => deleteList(l) },
        ], l.name),
      }, () => go('list/' + l.id));
    })));
    return { tab: 'lists', title: t('Shopping lists'), body, fab: fab(t('New list'), 'add', () => openListForm(null)) };
  }

  function openListForm(l) {
    let iconSel = l ? l.icon : 'home';
    const name = input({ value: l ? l.name : '', maxlength: 60 });
    const notes = textarea({ value: l ? l.notes : '', maxlength: 500, rows: 2 });
    const save = btn(l ? t('Save') : t('Create list'), { icon: 'check', onclick: async () => {
      if (!name.value.trim()) { toast(t('Enter the list name.'), 'error'); name.focus(); return; }
      await act('list.save', { id: l ? l.id : 0, name: name.value, icon: iconSel, notes: notes.value }, save);
      s.close();
    } });
    const s = sheet({
      title: l ? t('Edit list') : t('New shopping list'),
      body: [field(t('Name'), name), h('div.label', t('Icon')), iconPicker('lists', UI.iconNames('lists'), iconSel, v => { iconSel = v; }), field(t('Notes (optional)'), notes)],
      actions: [cancelBtn(), save],
    });
  }

  async function deleteList(l) {
    if (!await confirm(t('Delete the list?'), t('The list “{name}” and its products will be deleted for all household members.', { name: l.name }), t('Delete'), true)) return;
    await act('list.delete', { id: l.id });
    if (route().name === 'list') go('lists');
    toast(t('List deleted'));
  }

  function listItemCard(x) {
    const qty = UI.fmtQty(x.quantity, x.unit);
    return cardRow({
      cls: x.inCart ? 'done' : '', lead: tile('products', x.icon), title: itemName(x),
      sub: [qty || null, x.notes || null].filter(Boolean).join(' · ') || null,
      trail: [
        moreBtn([
          { icon: 'edit', label: t('Edit'), onclick: () => openListItemForm({ listId: x.listId, item: x }) },
          x.inCart ? { icon: 'move', label: t('Move to a space'), onclick: () => openItemForm({ fromListItem: x }) } : null,
          { icon: 'delete', label: t('Remove from list'), danger: true, onclick: () => act('listitem.delete', { id: x.id }).then(() => toast(t('Product removed from the list'))) },
        ], x.name),
        iconBtn(x.inCart ? 'cartRemove' : 'cartAdd', x.inCart ? t('Take out of cart') : t('Put in cart'), () => act('listitem.cart', { id: x.id, inCart: !x.inCart }), x.inCart ? '' : 'tonal'),
      ],
    }, () => openListItemDetail(x));
  }

  function viewList(id) {
    const l = listById(Number(id));
    if (!l) { go('lists'); return null; }
    const items = S.listItems.filter(x => x.listId === l.id);
    const toBuy = items.filter(x => !x.inCart), cart = items.filter(x => x.inCart);
    const body = [];
    if (!items.length) {
      body.push(empty('lists', l.icon, t('The list is empty'), t('Add the products to buy, then put them in the cart while shopping.')));
    } else {
      body.push(sectionTitle(t('To buy'), toBuy.length));
      if (toBuy.length) body.push(tipBanner('cart', t('Tap the cart icon to mark a product as picked up.')));
      else body.push(h('p.helper', { style: { margin: '0 4px 8px' } }, t('You got everything! 🎉')));
      body.push(h('div.stack', toBuy.map(listItemCard)));
      if (cart.length) {
        body.push(sectionTitle(t('In the cart'), cart.length));
        body.push(tipBanner('spaces', t('Once home, move the products you bought into your spaces to track their expiry.')));
        body.push(h('div.stack', cart.map(listItemCard)));
        body.push(h('div.btn-row', { style: { justifyContent: 'flex-end' } },
          btn(t('Empty cart'), { kind: 'text', onclick: async e => {
            const b = e.currentTarget;
            if (await confirm(t('Empty the cart?'), t('The products in the cart will be removed from the list without being moved to a space.'), t('Empty it'), true)) act('list.clearcart', { id: l.id }, b);
          } }),
          btn(t('Move all to a space'), { kind: 'tonal', icon: 'move', onclick: () => moveAllToSpace(cart) })));
      }
    }
    return {
      tab: 'lists', title: l.name, back: 'lists', body,
      actions: [moreBtn([
        { icon: 'edit', label: t('Edit list'), onclick: () => openListForm(l) },
        { icon: 'share', label: t('Share as text'), onclick: () => shareListText(l, toBuy) },
        { icon: 'delete', label: t('Delete list'), danger: true, onclick: () => deleteList(l) },
      ], l.name)],
      fab: fab(t('Add product'), 'add', () => openProductSearch({ listId: l.id })),
    };
  }

  function moveAllToSpace(cart) {
    const space = select(S.spaces.map(sp => ({ value: sp.id, label: sp.name })), S.spaces[0] && S.spaces[0].id);
    const ok = btn(t('Move all'), { icon: 'move', onclick: async () => {
      await busy(ok, async () => {
        for (const x of cart) {
          applyState(await Api.post('listitem.tospace', { id: x.id, spaceId: Number(space.value), productId: x.productId, name: x.name, icon: x.icon, quantity: x.quantity, unit: x.unit, notes: x.notes }));
        }
      });
      render(true); s.close();
      toast(t('{what}: remember the expiry dates!', { what: plural(cart.length, 'product moved', 'products moved') }));
    } });
    const s = sheet({
      title: t('Move all to a space'),
      body: [h('p.sheet-text', t('The {n} products in the cart will be moved to the same space. You can set each expiry date later.', { n: cart.length })), field(t('Space'), space)],
      actions: [cancelBtn(), ok],
    });
  }

  async function shareListText(l, items) {
    const text = `${l.name}\n` + items.map(x => `• ${x.name}${UI.fmtQty(x.quantity, x.unit) ? ' — ' + UI.fmtQty(x.quantity, x.unit) : ''}`).join('\n');
    try {
      if (navigator.share) await navigator.share({ title: l.name, text });
      else { await navigator.clipboard.writeText(text); toast(t('List copied to the clipboard')); }
    } catch (e) { /* cancelled */ }
  }

  function openListItemDetail(x) {
    const d = (label, value) => value ? h('div.d', h('span', label), h('span', value)) : null;
    const s = sheet({
      title: itemName(x),
      body: [h('div.row', { style: { marginBottom: '14px' } }, tile('products', x.icon, 'lg'), h('div.grow', x.inCart ? chip(t('In the cart'), 'ok', 'cartAdd') : chip(t('To buy'), 'neutral'))),
        h('div.details', d(t('Quantity'), UI.fmtQty(x.quantity, x.unit) || '—'), d(t('Notes'), x.notes))],
      actions: [
        btn(t('Edit'), { kind: 'text', icon: 'edit', onclick: () => { s.close(); setTimeout(() => openListItemForm({ listId: x.listId, item: x }), 240); } }),
        x.inCart
          ? btn(t('Move to a space'), { icon: 'move', onclick: () => { s.close(); setTimeout(() => openItemForm({ fromListItem: x }), 240); } })
          : btn(t('Put in cart'), { icon: 'cartAdd', onclick: async e => { await act('listitem.cart', { id: x.id, inCart: true }, e.currentTarget); s.close(); } }),
      ],
    });
  }

  function openListItemForm(opts) {
    const x = opts.item || null;
    const pre = opts.prefill || {};
    const p = productById(x ? x.productId : pre.productId);
    const name = input({ value: x ? x.name : (pre.name || ''), maxlength: 80 });
    const qty = input({ type: 'number', inputmode: 'decimal', min: 0, step: 'any', value: x && x.quantity != null ? String(x.quantity) : '' });
    const unit = unitSelect(x ? x.unit : (pre.unit || (p && p.unit) || 'pz'));
    const notes = textarea({ value: x ? x.notes : '', maxlength: 500, rows: 2 });
    const save = btn(x ? t('Save') : t('Add'), { icon: 'check', onclick: async () => {
      if (!name.value.trim()) { toast(t('Enter the product name.'), 'error'); name.focus(); return; }
      const pid = matchProduct(name.value, x ? x.productId : pre.productId);
      const pp = productById(pid);
      await act('listitem.save', { id: x ? x.id : 0, listId: opts.listId, productId: pid, name: name.value, icon: pp ? pp.icon : (x ? x.icon : 'generic'), quantity: qty.value, unit: unit.value, notes: notes.value }, save);
      s.close();
    } });
    const s = sheet({
      title: x ? t('Edit product') : t('Add to list'),
      body: [field(t('Name'), name), h('div.field-row', field(t('Quantity'), qty), field(t('Unit'), unit)), field(t('Notes (optional)'), notes)],
      actions: [cancelBtn(), save],
    });
  }

  /** Adds some products to a shopping list (existing or new). rows: [{productId, name, quantity, unit}] */
  function addToListSheet(rows, title) {
    if (!rows.length) { toast(t('You already have everything!')); return; }
    const checks = rows.map(r => ({ r, cb: h('input', { type: 'checkbox', checked: true }) }));
    const NEW = '__new__';
    const listSel = select(S.lists.map(l => ({ value: l.id, label: l.name })).concat([{ value: NEW, label: t('+ New list…') }]), S.lists[0] ? S.lists[0].id : NEW);
    const newName = input({ value: title || t('Shopping'), maxlength: 60 });
    const newField = field(t('Name of the new list'), newName);
    newField.hidden = listSel.value !== NEW;
    listSel.addEventListener('change', () => { newField.hidden = listSel.value !== NEW; });
    const ok = btn(t('Add'), { icon: 'cartAdd', onclick: async () => {
      const chosen = checks.filter(c => c.cb.checked).map(c => c.r);
      if (!chosen.length) { toast(t('Select at least one product.'), 'error'); return; }
      await busy(ok, async () => {
        let listId = listSel.value;
        if (listId === NEW) {
          if (!newName.value.trim()) throw new Error(t('Enter the list name.'));
          const d = await Api.post('list.save', { name: newName.value, icon: 'cart' });
          applyState(d); listId = d.id;
        }
        const d = await Api.post('listitem.addmany', { listId: Number(listId), items: chosen });
        applyState(d);
        s.close(); render(true);
        toast(t('{what} to the list', { what: plural(d.added, 'product added', 'products added') }), null, { label: t('Open'), onclick: () => go('list/' + listId) });
      });
    } });
    const s = sheet({
      title: t('Add to shopping list'),
      body: [
        h('div.group', checks.map(({ r, cb }) => {
          const p = productById(r.productId);
          return h('label.list-row', { style: { cursor: 'pointer' } }, tile('products', p ? p.icon : 'generic', 'sm'), h('div.body', h('p.title', r.name), UI.fmtQty(r.quantity, r.unit) ? h('div.sub', UI.fmtQty(r.quantity, r.unit)) : null), h('div.trail', { style: { paddingRight: '10px' } }, cb));
        })),
        field(t('Shopping list'), listSel), newField,
      ],
      actions: [cancelBtn(), ok],
    });
  }

  // ===================================================== RECIPES

  function recipeHasIngredient(r, key) {
    return r.ingredients.some(ing => norm(ing.name).includes(key) || matchItems(ing).some(m => norm(itemName(m)) === key));
  }

  function recipeMatches(r) {
    const q = norm(recipeQuery);
    if (q && !norm(r.name).includes(q) && !r.ingredients.some(i => norm(i.name).includes(q)) && !(r.origin || []).some(c => norm(UI.countryName(c)).includes(q))) return false;
    if (recipeIngredients.size) {
      for (const k of recipeIngredients) {
        if (!recipeHasIngredient(r, k)) return false;
      }
    }
    if (recipeFilters.fav && !S.favorites.has(r.id)) return false;
    if (recipeFilters.inHome || recipeFilters.expiring || recipeFilters.expired || recipeFilters.cookable) {
      const st = recipeStats(r);
      if (recipeFilters.inHome && !st.usesAvailable) return false;
      if (recipeFilters.expiring && !st.usesExpiring) return false;
      if (recipeFilters.expired && !st.usesExpired) return false;
      if (recipeFilters.cookable && st.ok < st.total) return false;
    }
    return true;
  }

  function favBtn(r) {
    const on = S.favorites.has(r.id);
    return iconBtn(on ? 'heart' : 'heartOutline', on ? t('Remove from favourites') : t('Add to favourites'), async () => {
      try {
        const d = await Api.post('favorite.toggle', { recipeId: r.id });
        if (d.favorite) S.favorites.add(r.id); else S.favorites.delete(r.id);
        render(true);
      } catch (e) { toast(e.message, 'error'); }
    }, on ? 'fav-on' : '');
  }

  const DIFFICULTY = { facile: 'Easy', media: 'Medium', difficile: 'Hard' };
  const diffLabel = d => t(DIFFICULTY[d] || d);
  const totalTime = r => (r.prepMinutes || 0) + (r.cookMinutes || 0);

  function recipeCard(r) {
    const st = recipeStats(r);
    return h('div.card.clickable.recipe-card', { role: 'button', tabindex: 0, onclick: () => go('recipe/' + r.id), onkeydown: e => { if (e.key === 'Enter') go('recipe/' + r.id); } },
      UI.recipeCover(r),
      listRow({
        title: r.name, sub: [r.origin.length ? [UI.originLabel(r.origin, true), ' · '] : null, [totalTime(r) ? totalTime(r) + ' min' : null, diffLabel(r.difficulty), plural(r.servings, 'serving', 'servings')].filter(Boolean).join(' · ')],
        meta: [st.ok === st.total ? chip(t('You have all the ingredients'), 'ok', 'check') : chip(t('{a} of {b} ingredients', { a: st.ok, b: st.total }), 'neutral'),
          st.usesExpired ? chip(t('Uses expired products'), 'danger') : st.usesExpiring ? chip(t('Uses expiring products'), 'warn') : null],
        trail: favBtn(r),
      }));
  }

  function viewRecipes() {
    const q = input({ type: 'search', value: recipeQuery, placeholder: t('Search by name, ingredient or country'), enterkeyhint: 'search', 'aria-label': t('Search recipe') });
    const results = h('div');
    const chips = h('div.chips.scroll');
    const inHomePills = h('div.chips.scroll');
    const labels = {
      fav: t('Favourites'),
      inHome: t('With products at home'),
      cookable: t('I can cook them now'),
      expiring: t('With expiring products'),
      expired: t('With expired products'),
    };
    function paintInHomePills() {
      UI.clear(inHomePills);
      const seen = new Set();
      const uniqueInHome = [];
      for (const it of S.items) {
        const name = itemName(it);
        const key = norm(name);
        if (!name || seen.has(key)) continue;
        seen.add(key);
        const p = productById(it.productId);
        uniqueInHome.push({ name, key, icon: it.icon || (p && p.icon) || 'generic' });
      }
      for (const k of recipeIngredients) {
        if (!seen.has(k)) recipeIngredients.delete(k);
      }
      if (!uniqueInHome.length) { inHomePills.hidden = true; return; }
      inHomePills.hidden = false;
      for (const { name, key, icon: ic } of uniqueInHome) {
        const isSel = recipeIngredients.has(key);
        inHomePills.appendChild(h('button.chip' + (isSel ? '.sel' : ''), {
          type: 'button',
          'aria-pressed': String(isSel),
          onclick: () => {
            if (isSel) recipeIngredients.delete(key);
            else recipeIngredients.add(key);
            paint();
          }
        }, isSel ? icon('check') : UI.pic('products', ic), name));
      }
    }
    function paint() {
      UI.clear(results); UI.clear(chips);
      for (const k of Object.keys(labels)) {
        chips.appendChild(h('button.chip' + (recipeFilters[k] ? '.sel' : ''), { type: 'button', onclick: () => { recipeFilters[k] = !recipeFilters[k]; paint(); } }, recipeFilters[k] ? icon('check') : null, labels[k]));
      }
      paintInHomePills();
      const list = S.recipes.filter(recipeMatches);
      const filtering = recipeQuery || recipeIngredients.size > 0 || Object.values(recipeFilters).some(Boolean);
      if (!list.length) { results.appendChild(empty('icon', 'restaurant', t('No recipes found'), t('Try another ingredient or remove some filters.'))); return; }
      if (!filtering) {
        const fav = list.filter(r => S.favorites.has(r.id));
        if (fav.length) { results.appendChild(sectionTitle(t('Favourites'), fav.length)); results.appendChild(h('div.stack', fav.map(recipeCard))); }
        list.sort((a, b) => {
          const A = recipeStats(a), B = recipeStats(b);
          return (B.usesExpiring - A.usesExpiring) || (B.ok / (B.total || 1) - A.ok / (A.total || 1));
        });
        results.appendChild(sectionTitle(fav.length ? t('All recipes') : t('Recommended for you'), list.length));
      } else {
        results.appendChild(sectionTitle(t('Results'), list.length));
      }
      results.appendChild(h('div.stack', list.map(recipeCard)));
    }
    q.addEventListener('input', () => { recipeQuery = q.value; paint(); });
    paint();
    return {
      tab: 'recipes', title: t('Recipes'),
      body: [
        searchbar(q), chips, inHomePills,
        h('div', { style: { marginTop: '12px' } }, cardRow({ lead: tile('icon', 'calendar'), title: t('Weekly plan'), sub: planSummary(), trail: icon('right') }, () => go('plan'))),
        results,
      ],
    };
  }

  function planSummary() {
    const today = UI.todayStr();
    const n = S.plan.filter(p => p.date >= today && p.date <= UI.addDays(today, 6)).length;
    return n ? t('{what} in the next 7 days', { what: plural(n, 'meal planned', 'meals planned') }) : t('Plan the week’s meals and shopping');
  }

  function viewRecipe(id) {
    const r = recipeById(Number(id));
    if (!r) { go('recipes'); return null; }
    const body = [];
    body.push(UI.recipeCover(r, 'hero-img'));
    body.push(h('h2.page-title', r.name));
    if (r.description) body.push(h('p.lead.muted', r.description));
    body.push(h('div.meta-row', ...(r.origin || []).map(c => h('span.status.neutral', UI.flag(c), UI.countryName(c))), chip(totalTime(r) + ' min', 'neutral', 'timer'), chip(t('Difficulty: {d}', { d: diffLabel(r.difficulty) }), 'neutral'), chip(plural(r.servings, 'serving', 'servings'), 'neutral', 'group')));

    const missing = missingFor([r]);
    const st = recipeStats(r);
    body.push(sectionTitle(t('Ingredients'), r.ingredients.length));
    body.push(h('div.group', r.ingredients.map(ing => {
      const a = availability(ing);
      const p = productById(ing.productId);
      const where = [...new Set(a.matches.map(m => (spaceById(m.spaceId) || {}).name).filter(Boolean))].join(', ');
      const av = a.status === 'ok' ? chip(where ? 'In ' + where : t('Available'), 'ok', 'check')
        : a.status === 'partial' ? chip(t('{qty} missing', { qty: UI.fmtQty(a.missing, a.unit) }), 'warn')
        : chip(t('To get'), 'danger');
      return listRow({ lead: tile('products', p ? p.icon : 'generic', 'sm'), title: ing.name, sub: [UI.fmtQty(ing.quantity, ing.unit), ing.notes].filter(Boolean).join(' · ') || null, meta: [av] });
    })));
    body.push(h('div.btn-row', { style: { justifyContent: 'flex-end' } }, missing.length
      ? btn(t('Add {what} to the list', { what: plural(missing.length, 'missing item', 'missing items') }), { kind: 'tonal', icon: 'cartAdd', onclick: () => addToListSheet(missing, r.name) })
      : chip(t('You have all {n} ingredients', { n: st.total }), 'ok', 'check')));

    const colA = body.splice(0);
    body.push(sectionTitle(t('Time needed')));
    body.push(h('div.details',
      h('div.d', h('span', t('Preparation')), h('span', r.prepMinutes != null ? r.prepMinutes + ' min' : '—')),
      h('div.d', h('span', t('Cooking')), h('span', r.cookMinutes != null ? r.cookMinutes + ' min' : t('None'))),
      r.cookNotes ? h('div.d', h('span', t('Notes')), h('span', r.cookNotes)) : null));
    body.push(sectionTitle(t('Method'), r.steps.length));
    body.push(h('div.group', r.steps.map((t, i) => h('div.step', h('span.num', String(i + 1)), h('p', t)))));
    const layout = [h('div.recipe-layout', h('div.col-a', colA), h('div.col-b', body))];
    return {
      tab: 'recipes', title: r.name, back: 'recipes', body: layout, actions: [favBtn(r)],
      fab: fab(t('Add to plan'), 'calendar', () => openPlanForm({ recipeId: r.id, date: UI.todayStr() })),
    };
  }

  // ===================================================== MEAL PLAN

  function weekStart(offset) {
    const d = UI.parseDate(UI.todayStr());
    d.setDate(d.getDate() - ((d.getDay() + 6) % 7) + offset * 7);
    return UI.dateStr(d);
  }

  function viewPlan() {
    const start = weekStart(weekOffset);
    const days = Array.from({ length: 7 }, (_, i) => UI.addDays(start, i));
    const today = UI.todayStr();
    const label = weekOffset === 0 ? t('This week') : weekOffset === 1 ? t('Next week') : weekOffset === -1 ? t('Last week') : t('In {n} weeks', { n: weekOffset });
    const body = [h('div.week-nav',
      iconBtn('left', t('Previous week'), () => { if (weekOffset > -1) { weekOffset--; render(); } }),
      h('div.center', h('strong', label), h('span.small.muted', `${UI.fmtDate(days[0]).slice(0, 5)} – ${UI.fmtDate(days[6])}`)),
      iconBtn('right', t('Following week'), () => { if (weekOffset < 4) { weekOffset++; render(); } }))];
    const week = h('div.week-grid');
    body.push(week);
    for (const d of days) {
      const entries = S.plan.filter(p => p.date === d);
      week.appendChild(h('div.card.day' + (d === today ? '.today' : ''),
        h('div.day-head', h('strong', (d === today ? t('Today') + ', ' : '') + UI.longDate(d)), iconBtn('add', t('Add a meal'), () => openPlanForm({ date: d }), 'sm')),
        entries.length ? entries.map(p => {
          const r = recipeById(p.recipeId);
          const slot = (SLOTS.find(x => x[0] === p.slot) || [, p.slot])[1];
          return h('div.list-row', { role: 'button', tabindex: 0, style: { cursor: 'pointer' }, onclick: () => actionSheet([
            r ? { icon: 'restaurant', label: t('Open recipe'), onclick: () => go('recipe/' + r.id) } : null,
            { icon: 'edit', label: t('Edit'), onclick: () => openPlanForm(p) },
            { icon: 'delete', label: t('Remove from plan'), danger: true, onclick: () => act('plan.delete', { id: p.id }) },
          ], r ? r.name : p.title) },
          r && r.image ? h('span.tile.tile-sm.photo', { style: { backgroundImage: `url("${r.image}")` } }) : r && r.origin.length ? h('span.tile.tile-sm.flag-tile', UI.flag(r.origin[0], 'big')) : tile('icon', r ? 'restaurant' : 'edit', 'sm'),
          h('div.body', h('p.title', r ? r.name : (p.title || t('Recipe not available in your language'))), h('div.sub', slot)), h('div.trail', icon('more')));
        }) : h('div.none', t('No meals planned'))));
    }
    const weekRecipes = S.plan.filter(p => days.includes(p.date) && p.recipeId).map(p => recipeById(p.recipeId)).filter(Boolean);
    body.push(h('div.btn-row', { style: { justifyContent: 'flex-end' } }, btn(t('Missing ingredients to list'), { kind: 'tonal', icon: 'cartAdd', disabled: !weekRecipes.length, onclick: () => addToListSheet(missingFor(weekRecipes), t('Weekly shopping')) })));
    if (!weekRecipes.length) body.push(h('p.helper', { style: { textAlign: 'right' } }, t('Add recipes to the plan to build the week’s shopping list automatically.')));
    return { tab: 'recipes', title: t('Weekly plan'), back: 'recipes', body, fab: fab(t('Add meal'), 'add', () => openPlanForm({ date: weekOffset === 0 ? today : days[0] })) };
  }

  function openPlanForm(p) {
    const isEdit = !!p.id;
    const date = input({ type: 'date', value: p.date || UI.todayStr() });
    let slot = p.slot || (new Date().getHours() < 15 ? 'pranzo' : 'cena');
    let recipeId = p.recipeId && recipeById(p.recipeId) ? p.recipeId : null;
    let mode = recipeId || !p.title ? 'recipe' : 'other';
    const title = input({ value: p.title || '', maxlength: 120 });
    const titleField = field(t('What are you eating?'), title, { hint: t('For example: “Takeaway pizza” or “Yesterday’s leftovers”') });
    const picked = h('div.recipe-picked');
    function paintPicked() {
      UI.clear(picked);
      picked.hidden = mode !== 'recipe';
      titleField.hidden = mode !== 'other';
      const r = recipeId ? recipeById(recipeId) : null;
      if (!r) {
        picked.appendChild(h('button.pick-empty', { type: 'button', onclick: choose }, tile('icon', 'restaurant'), h('span.grow', h('strong', t('Choose a recipe')), h('small', t('Search by name, ingredient or country'))), icon('right')));
        return;
      }
      picked.appendChild(h('div.card.clickable', { role: 'button', tabindex: 0, onclick: choose, onkeydown: e => { if (e.key === 'Enter') choose(); } },
        recipePickRow(r, { trail: h('span.btn.btn-text', h('span', t('Change'))) })));
    }
    function choose() { openRecipePicker(recipeId, id => { recipeId = id; paintPicked(); }); }
    const modeSeg = seg([['recipe', t('Recipe')], ['other', t('Other (write it yourself)')]], mode, v => { mode = v; paintPicked(); if (v === 'other') setTimeout(() => title.focus(), 50); });
    paintPicked();
    const save = btn(isEdit ? t('Save') : t('Add'), { icon: 'check', onclick: async () => {
      if (mode === 'recipe' && !recipeId) { toast(t('Choose a recipe'), 'error'); choose(); return; }
      const useRecipe = mode === 'recipe';
      await act('plan.save', { id: p.id || 0, date: date.value, slot, recipeId: useRecipe ? recipeId : null, title: useRecipe ? '' : title.value }, save);
      s.close();
      toast(isEdit ? t('Meal updated') : t('Added to the weekly plan'), null, route().name !== 'plan' ? { label: t('Open'), onclick: () => go('plan') } : null);
    } });
    const s = sheet({
      title: isEdit ? t('Edit meal') : t('Add a meal'),
      body: [field(t('Day'), date), seg(SLOTS, slot, v => { slot = v; }), h('div.label', t('What are you eating?')), modeSeg, picked, titleField],
      actions: [cancelBtn(), save],
      autofocus: false,
    });
    // Opened from the plan without a recipe: go straight to the choice
    if (!isEdit && !recipeId && mode === 'recipe') setTimeout(choose, 260);
  }

  /** Compact recipe row: cover thumbnail, name, countries · time · difficulty, ingredient availability. */
  function recipePickRow(r, o) {
    o = o || {};
    const st = recipeStats(r);
    return listRow({
      lead: UI.recipeCover(r, 'recipe-thumb'), title: r.name, cls: o.cls,
      sub: [r.origin.length ? [UI.originLabel(r.origin, true), ' · '] : null, [totalTime(r) ? totalTime(r) + ' min' : null, diffLabel(r.difficulty)].filter(Boolean).join(' · ')],
      meta: [st.ok === st.total ? chip(t('You have all the ingredients'), 'ok', 'check') : chip(t('{a} of {b} ingredients', { a: st.ok, b: st.total }), 'neutral'),
        st.usesExpired ? chip(t('Uses expired products'), 'danger') : st.usesExpiring ? chip(t('Uses expiring products'), 'warn') : null,
        S.favorites.has(r.id) ? chip(t('Favourite'), 'neutral', 'heart') : null],
      trail: o.trail,
    });
  }

  /** Searchable recipe chooser (stacked over the meal form): suggestions, recently planned, filters. */
  function openRecipePicker(current, onpick) {
    let query = '';
    const filters = { fav: false, cookable: false, expiring: false };
    const labels = { fav: t('Favourites'), cookable: t('I can cook them now'), expiring: t('With expiring products') };
    const q = input({ type: 'search', placeholder: t('Search by name, ingredient or country'), enterkeyhint: 'search', 'aria-label': t('Search recipe') });
    const chips = h('div.chips');
    const results = h('div.recipe-pick-list');
    const matches = r => {
      const n = norm(query);
      if (n && !norm(r.name).includes(n) && !r.ingredients.some(i => norm(i.name).includes(n)) && !(r.origin || []).some(c => norm(UI.countryName(c)).includes(n))) return false;
      if (filters.fav && !S.favorites.has(r.id)) return false;
      if (filters.cookable || filters.expiring) {
        const st = recipeStats(r);
        if (filters.cookable && st.ok < st.total) return false;
        if (filters.expiring && !(st.usesExpiring || st.usesExpired)) return false;
      }
      return true;
    };
    const row = r => h('button.pick-row' + (r.id === current ? '.sel' : ''), { type: 'button', onclick: () => { s.close(); onpick(r.id); } },
      recipePickRow(r, { trail: r.id === current ? icon('check') : null }));
    const score = r => { const st = recipeStats(r); return (st.usesExpired || st.usesExpiring ? 2 : 0) + st.ok / (st.total || 1) + (S.favorites.has(r.id) ? .5 : 0); };
    function paint() {
      UI.clear(chips); UI.clear(results);
      for (const k of Object.keys(labels)) chips.appendChild(h('button.chip' + (filters[k] ? '.sel' : ''), { type: 'button', onclick: () => { filters[k] = !filters[k]; paint(); } }, filters[k] ? icon('check') : null, labels[k]));
      const list = S.recipes.filter(matches);
      if (!list.length) { results.appendChild(empty('icon', 'restaurant', t('No recipes found'), t('Try another ingredient or remove some filters.'))); return; }
      const section = (label, items) => { if (items.length) results.append(h('div.label', label), h('div.group', items.map(row))); };
      if (!query && !Object.values(filters).some(Boolean)) {
        const recentIds = [...new Set(S.plan.slice().sort((a, b) => (b.date > a.date ? 1 : -1)).map(x => x.recipeId).filter(Boolean))].slice(0, 4);
        const recent = recentIds.map(recipeById).filter(Boolean);
        const sugg = list.filter(r => !recentIds.includes(r.id)).sort((a, b) => score(b) - score(a)).slice(0, 5);
        section(t('Recommended for you'), sugg);
        section(t('Recently planned'), recent);
        section(t('All recipes'), list.slice().sort((a, b) => a.name.localeCompare(b.name)));
      } else {
        section(t('Results') + ' · ' + list.length, list.sort((a, b) => score(b) - score(a)));
      }
    }
    q.addEventListener('input', () => { query = q.value; paint(); });
    paint();
    const s = sheet({ title: t('Choose a recipe'), cls: 'tall', body: [searchbar(q), chips, results], actions: [cancelBtn()] });
  }

  // ===================================================== SEARCH

  function viewSearch() {
    const q = input({ type: 'search', value: searchState.q, placeholder: t('Name, brand, category, space…'), enterkeyhint: 'search', 'aria-label': t('Find a food') });
    const results = h('div');
    const spaceSel = select([{ value: '', label: t('All spaces') }].concat(S.spaces.map(s => ({ value: s.id, label: s.name }))), searchState.spaceId);
    const catSel = select([{ value: '', label: t('All categories') }].concat(S.catalog.categories.map(c => ({ value: c.id, label: c.name }))), searchState.categoryId);
    const expSeg = seg([['all', t('All')], ['expiring', t('Expiring')], ['expired', t('Expired')], ['none', t('No date')]], searchState.exp, v => { searchState.exp = v; paint(); });

    function saveRecent(text) {
      text = text.trim();
      if (text.length < 2) return;
      const rec = store.get('recentSearches', []).filter(x => norm(x) !== norm(text));
      rec.unshift(text);
      store.set('recentSearches', rec.slice(0, 8));
    }

    function paint() {
      UI.clear(results);
      const n = norm(searchState.q);
      const filtering = n || searchState.spaceId || searchState.categoryId || searchState.exp !== 'all';
      if (!filtering) {
        const rec = store.get('recentSearches', []);
        if (rec.length) {
          results.appendChild(sectionTitle(t('Recent searches')));
          results.appendChild(h('div.chips', rec.map(t => h('button.chip', { type: 'button', onclick: () => { q.value = t; searchState.q = t; paint(); } }, icon('timer'), t,
            h('span.x', { role: 'button', 'aria-label': t('Remove'), onclick: e => { e.stopPropagation(); store.set('recentSearches', store.get('recentSearches', []).filter(x => x !== t)); paint(); } }, icon('close'))))));
        }
        const recent = S.items.slice().sort((a, b) => b.createdAt.localeCompare(a.createdAt)).slice(0, 10);
        results.appendChild(sectionTitle(t('Recently added')));
        if (!recent.length) results.appendChild(empty('icon', 'search', t('No products yet'), t('Products you add to your spaces will appear here.')));
        results.appendChild(h('div.stack', recent.map(i => itemCard(i, { showSpace: true }))));
        return;
      }
      const found = S.items.filter(i => {
        if (searchState.spaceId && i.spaceId !== Number(searchState.spaceId)) return false;
        if (searchState.categoryId && i.categoryId !== Number(searchState.categoryId)) return false;
        const st = expiryState(i);
        if (searchState.exp === 'expiring' && st !== 'expiring') return false;
        if (searchState.exp === 'expired' && st !== 'expired') return false;
        if (searchState.exp === 'none' && i.expiryDate) return false;
        if (!n) return true;
        const sp = spaceById(i.spaceId), cat = categoryById(i.categoryId);
        return [i.name, i.brand, i.notes, sp && sp.name, cat && cat.name, i.expiryDate && UI.fmtDate(i.expiryDate)].some(x => x && norm(x).includes(n));
      });
      results.appendChild(sectionTitle(t('Results'), found.length));
      if (!found.length) results.appendChild(empty('icon', 'search', t('No products found'), t('Try another word or change the filters.')));
      results.appendChild(h('div.stack', sortItems(found, 'exp-asc').map(i => h('div', { onclick: () => saveRecent(searchState.q) }, itemCard(i, { showSpace: true })))));
    }

    q.addEventListener('input', () => { searchState.q = q.value; paint(); });
    q.addEventListener('keydown', e => { if (e.key === 'Enter') { saveRecent(q.value); q.blur(); } });
    q.addEventListener('change', () => saveRecent(q.value));
    spaceSel.addEventListener('change', () => { searchState.spaceId = spaceSel.value; paint(); });
    catSel.addEventListener('change', () => { searchState.categoryId = catSel.value; paint(); });
    paint();
    return {
      tab: 'spaces', title: t('Find a food'), back: 'spaces',
      body: [searchbar(q), h('div.field-row', field(t('Space'), spaceSel), field(t('Category'), catSel)), expSeg, results],
      after: () => { if (!searchState.q && !matchMedia('(pointer: coarse)').matches) q.focus(); },
    };
  }

  // ===================================================== ACTIVITY HISTORY & UNDO

  function viewHistory() {
    let list = null;
    const body = [h('div.history-box', UI.skeletonScreen())];

    async function load() {
      try {
        const d = await Api.get('history.list');
        list = d.history || [];
        paint();
      } catch (e) {
        list = [];
        paint();
        toast(e.message, 'error');
      }
    }

    function paint() {
      const box = body[0];
      UI.clear(box);
      if (!list || !list.length) {
        box.appendChild(empty('icon', 'history', t('No actions recorded yet'), t('Actions such as consuming, adding or deleting products will appear here.')));
        return;
      }

      box.appendChild(h('div.stack', list.map(hItem => {
        let title = '';
        let ic = 'history';
        if (hItem.action === 'consume_part') {
          title = t('Consumed {qty} of “{name}”', { qty: UI.fmtQty(hItem.quantity, hItem.unit), name: hItem.itemName });
          ic = 'check';
        } else if (hItem.action === 'consume') {
          title = t('Consumed “{name}” entirely', { name: hItem.itemName });
          ic = 'check';
        } else if (hItem.action === 'add') {
          title = t('Added “{name}”', { name: hItem.itemName });
          ic = 'add';
        } else if (hItem.action === 'delete') {
          title = t('Deleted “{name}”', { name: hItem.itemName });
          ic = 'delete';
        } else if (hItem.action === 'edit') {
          title = t('Edited “{name}”', { name: hItem.itemName });
          ic = 'edit';
        } else if (hItem.action === 'move_from_list') {
          title = t('Purchased “{name}”', { name: hItem.itemName });
          ic = 'cartAdd';
        } else {
          title = `${hItem.action}: ${hItem.itemName}`;
        }

        const timeStr = UI.fmtDateTime(hItem.createdAt);
        const subParts = [hItem.userName || '', timeStr, hItem.spaceName || ''].filter(Boolean);
        const sub = subParts.join(' · ');

        const meta = [];
        if (hItem.undone) {
          meta.push(chip(t('Undone'), 'neutral'));
        }

        const trail = hItem.undone ? null : btn(t('Undo'), {
          kind: 'tonal', icon: 'undo',
          onclick: async () => {
            const ok = await confirm(t('Undo this action?'), title, t('Undo'), false);
            if (!ok) return;
            try {
              const res = await Api.post('history.undo', { id: hItem.id });
              applyState(res);
              toast(t('Action undone: {name} restored', { name: hItem.itemName }));
              load();
            } catch (err) {
              toast(err.message, 'error');
            }
          }
        });

        return cardRow({
          lead: tile('icon', ic),
          title,
          sub,
          meta,
          trail,
        });
      })));
    }

    load();
    return {
      tab: 'spaces',
      title: t('Activity history'),
      back: 'spaces',
      body,
    };
  }

  // ===================================================== PROFILE & HOUSEHOLD

  function openHouseholdSwitcher() {
    const s = sheet({
      title: t('Your households'),
      body: [
        h('p.sheet-text', t('Each household has its own spaces, products, lists and meal plan, shared with the members you invite.')),
        h('div.stack', S.households.map(hh => cardRow({
          lead: tile('icon', hh.members > 1 ? 'group' : 'home'), title: hh.name,
          sub: `${plural(hh.members, 'member', 'members')} · ${hh.role === 'owner' ? t('owner') : t('member')}`,
          trail: hh.id === S.household.id ? chip(t('Active'), 'ok', 'check') : icon('right'),
        }, async () => {
          s.close();
          if (hh.id === S.household.id) return;
          await act('household.switch', { id: hh.id }); toast(t('You are now in “{home}”', { home: hh.name }));
        }))),
      ],
      actions: [btn(t('Manage and invite'), { kind: 'tonal', icon: 'group', onclick: () => go('profile') })],
    });
  }

  function inviteLink() { return location.origin + location.pathname.replace(/[^/]*$/, '') + '#/join/' + S.household.inviteCode; }

  function openInvite() {
    const link = inviteLink();
    const isOwner = S.household.role === 'owner';
    const s = sheet({
      title: t('Invite to the household'),
      body: [
        h('p.sheet-text', t('Whoever joins “{home}” will see and edit spaces, products, shopping lists and meal plan. Share the code or the link.', { home: S.household.name })),
        h('div.code-box', S.household.inviteCode),
        isOwner ? h('div.btn-row', { style: { justifyContent: 'center' } }, btn(t('Generate a new code'), { kind: 'text', icon: 'swap', onclick: async e => { await act('household.code', {}, e.currentTarget); s.close(); openInvite(); } })) : null,
      ],
      actions: [
        btn(t('Copy link'), { kind: 'text', icon: 'copy', onclick: async () => { try { await navigator.clipboard.writeText(link); toast(t('Link copied')); } catch (e) { toast(link); } } }),
        navigator.share ? btn(t('Share'), { icon: 'share', onclick: () => navigator.share({ title: 'FoodTrack', text: t('Join my household “{home}” on FoodTrack with the code {code}', { home: S.household.name, code: S.household.inviteCode }), url: link }).catch(() => {}) }) : null,
      ].filter(Boolean),
    });
  }

  function promptSheet(title, label, value, okLabel, onok, extra) {
    const inp = input({ value: value || '', maxlength: 80 });
    const ok = btn(okLabel, { icon: 'check', onclick: async () => { if (!inp.value.trim()) { inp.focus(); return; } await busy(ok, () => onok(inp.value.trim())); s.close(); } });
    inp.addEventListener('keydown', e => { if (e.key === 'Enter') ok.click(); });
    const s = sheet({ title, body: [extra ? h('p.sheet-text', extra) : null, field(label, inp)], actions: [cancelBtn(), ok] });
  }

  /** "Developed with ♥ by Saverio Morelli" at the bottom of the profile, in the font of the website (heart as SVG). */
  function devCredit() {
    return h('a.dev-credit', { href: 'https://www.saveriomorelli.com/', target: '_blank', rel: 'noopener', 'aria-label': t('Developed with love by Saverio Morelli') },
      h('span', t('Developed with')), h('span.dev-heart', { html: '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="#E5484D" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>' }), h('span', t('by')), h('span.dev-name', 'Saverio Morelli'));
  }

  function viewProfile() {
    const hh = S.household;
    const isOwner = hh.role === 'owner';
    const theme = store.get('theme', 'auto');
    const notif = store.get('notify', false) && UI.app.notificationsSupported() && UI.app.notificationPermission() === 'granted';
    const u = S.user;

    const body = [
      h('div.card', listRow({ big: true, lead: h('span.avatar.lg', initials(u.name)), title: u.name, sub: u.email, meta: [u.isEditor ? chip(u.roleLabel, 'ok', 'shield') : null, u.googleLinked ? chip('Google', 'neutral', 'link') : null] })),

      sectionTitle(t('Shared household')),
      h('div.group',
        groupRow(hh.members.length > 1 ? 'group' : 'home', hh.name, `${plural(hh.members.length, 'member', 'members')} · ${isOwner ? t('you are the owner') : t('you are a member')}`, null, {
          trail: isOwner ? h('div.trail', iconBtn('edit', t('Rename household'), () => promptSheet(t('Rename household'), t('Name'), hh.name, t('Save'), name => act('household.rename', { name })))) : null,
        }),
        ...hh.members.map(m => groupRow(null, m.name + (m.id === u.id ? ' ' + t('(you)') : ''), m.role === 'owner' ? t('Owner') : t('Member since {d}', { d: UI.fmtDateTime(m.joinedAt).split(' ')[0] }), null, {
          lead: h('span.avatar', initials(m.name)),
          trail: isOwner && m.id !== u.id ? h('div.trail', moreBtn([
            { icon: 'swap', label: t('Make owner'), onclick: async () => { if (await confirm(t('Hand over the household?'), t('{name} will become the owner of “{home}” and you will stay a member.', { name: m.name, home: hh.name }), t('Hand over'))) act('household.transfer', { userId: m.id }); } },
            { icon: 'delete', label: t('Remove from household'), danger: true, onclick: async () => { if (await confirm(t('Remove the member?'), t('{name} will no longer see or edit the data of “{home}”.', { name: m.name, home: hh.name }), t('Remove'), true)) act('household.member.remove', { userId: m.id }); } },
          ], m.name)) : null,
        })),
        groupRow('share', t('Invite someone'), t('Family, partner or flatmates'), openInvite),
      ),
      h('div.spacer'),
      h('div.group',
        S.households.length > 1 ? groupRow('swap', t('Switch household'), t('You belong to {n} households', { n: S.households.length }), openHouseholdSwitcher) : null,
        groupRow('link', t('Join a household'), t('With the invite code you received'), () => promptSheet(t('Join a household'), t('Invite code'), '', t('Join'), async code => { await act('household.join', { code }); toast(t('Welcome to “{home}”!', { home: S.household.name })); })),
        groupRow('add', t('Create a new household'), t('For example a holiday home or the office'), () => promptSheet(t('New household'), t('Name'), '', t('Create'), async name => { await act('household.create', { name }); toast(t('Household created')); })),
        hh.members.length > 1 || S.households.length > 1 ? groupRow('logout', t('Leave this household'), isOwner ? t('Ownership will pass to another member') : null, async () => {
          if (await confirm(t('Leave the household?'), t('You will no longer see the spaces, products and lists of “{home}”.', { home: hh.name }) + (hh.members.length === 1 ? ' Sei l’unico membro: la casa e tutti i suoi dati verranno eliminati.' : ''), t('Sign out'), true)) { await act('household.leave', {}); toast(t('You left the household')); }
        }, { danger: true }) : null,
        isOwner ? groupRow('delete', t('Delete this household'), t('For all members, with all the data'), async () => {
          if (await confirm(t('Delete the household?'), t('“{home}” and all its data will be permanently deleted for {what}.', { home: hh.name, what: plural(hh.members.length, 'member', 'members') }), t('Delete'), true)) { await act('household.delete', {}); toast(t('Household deleted')); }
        }, { danger: true }) : null,
      ),

      h('div.spacer'),
      h('div.group',
        groupRow('history', t('Household activity history'), t('View actions and undo accidental changes'), () => go('history'))
      ),

      sectionTitle(t('Preferences')),
      h('div.group',
        groupRow('globe', t('Language'), (I18N.langs.find(l => l[0] === I18N.lang) || [, ''])[1], () => actionSheet(I18N.langs.map(([k, label]) => ({ icon: 'globe', label, selected: k === I18N.lang, onclick: () => I18N.set(k) })), t('Language'))),
        groupRow('theme', t('Theme'), { auto: t('Automatic'), light: t('Light'), dark: t('Dark') }[theme], () => actionSheet([['auto', t('Automatic (like the system)')], ['light', t('Light')], ['dark', t('Dark')]].map(([k, label]) => ({ icon: 'theme', label, selected: k === theme, onclick: () => { store.set('theme', k); UI.applyTheme(); render(true); } })), t('Theme'))),
        groupRow('warning', t('“Expiring soon” alert'), t('{what} before expiry', { what: plural(noticeDays(), 'day', 'days') }), () => actionSheet([1, 2, 3, 5, 7].map(n => ({ icon: 'timer', label: t('{what} before', { what: plural(n, 'day', 'days') }), selected: n === noticeDays(), onclick: () => { store.set('noticeDays', n); render(true); } })), t('Remind me'))),
        UI.app.notificationsSupported() ? groupRow('bell', t('Notifications'), notif ? t('On for this device') : t('Off'), async () => {
          if (notif) { store.set('notify', false); render(true); return; }
          let p = 'denied';
          try { p = await UI.app.requestNotifications(); } catch (e) { /* not allowed */ }
          store.set('notify', p === 'granted');
          if (p !== 'granted') toast(t('Permission denied: you can turn them on in the browser settings.'), 'error');
          else { store.set('notifiedOn', ''); notifyExpiry(); }
          render(true);
        }, { trail: h('div.trail', h('span.switch' + (notif ? '.on' : ''))) }) : null,
        groupRow('school', t('App tutorial'), t('A quick tour of how FoodTrack works'), () => openTutorial()),
        groupRow('bulb', t('Show tips again'), null, () => { ['tip.cart', 'tip.spaces'].forEach(k => store.set(k, false)); store.set('hide.expired', ''); store.set('hide.expiring', ''); toast(t('Tips and alerts restored')); }),
      ),

      sectionTitle(t('Account and security')),
      h('div.group',
        groupRow('edit', t('Edit name'), null, () => promptSheet(t('Edit name'), t('Full name'), u.name, t('Save'), async name => { const d = await Api.post('auth.profile', { name }); S.user = d.user; render(true); })),
        groupRow('lock', u.hasPassword ? t('Change password') : t('Set a password'), u.hasPassword ? null : t('To sign in with email and password too'), openChangePassword),
        S.config.googleClientId ? groupRow('link', t('Google account'), u.googleLinked ? t('Linked: you can sign in with “Continue with Google”') : t('Not linked'), u.googleLinked ? (u.hasPassword ? async () => {
          if (!await confirm(t('Unlink Google?'), t('You will only be able to sign in with email and password.'), t('Unlink'), true)) return;
          try { const d = await Api.post('auth.google.unlink', {}); S.user = d.user; render(true); toast(t('Google account unlinked')); } catch (e) { toast(e.message, 'error'); }
        } : null) : openLinkGoogle, { trail: u.googleLinked && !u.hasPassword ? h('div.trail', { style: { paddingRight: '12px' } }, chip(t('Linked'), 'ok', 'check')) : undefined }) : null,
        u.isEditor ? groupRow('shield', t('Admin portal'), u.isAdmin ? t('Recipes, products, categories and users') : t('Recipes and products'), null, { href: 'admin.html', external: true }) : null,
        groupRow('info', t('Privacy and info'), 'FoodTrack ' + VERSION, null, { href: 'privacy.html', external: true }),
        groupRow('heart', t('Donation'), t('Free and open-source project, with no ads: a small donation helps cover server and maintenance costs.'), null, { href: DONATE_URL, external: true }),
      ),
      h('div.spacer'),
      h('div.group',
        groupRow('logout', t('Sign out'), null, logout),
        groupRow('delete', t('Delete my account'), t('Permanently delete account and data'), deleteAccount, { danger: true }),
      ),
    ];
    body.push(devCredit());
    return { tab: null, title: t('Profile'), back: 'spaces', body, cls: 'narrow' };
  }

  function openChangePassword(forced) {
    forced = forced === true;
    const needsCurrent = S.user.hasPassword || forced;
    const cur = input({ type: 'password', autocomplete: 'current-password' });
    const p1 = input({ type: 'password', autocomplete: 'new-password', minlength: 8 });
    const p2 = input({ type: 'password', autocomplete: 'new-password', minlength: 8 });
    const ok = btn(t('Save password'), { icon: 'lock', onclick: async () => {
      if (p1.value.length < 8) { toast(t('The new password must be at least 8 characters long.'), 'error'); return; }
      if (p1.value !== p2.value) { toast(t('The two passwords do not match.'), 'error'); return; }
      const d = await busy(ok, () => Api.post('auth.password', { current: cur.value, password: p1.value }));
      S.user = d.user; s.close(); toast(t('Password updated'));
      if (forced) start(); else render(true);
    } });
    const s = sheet({
      title: forced ? t('Choose a new password') : S.user.hasPassword ? t('Change password') : t('Set a password'), persistent: forced,
      body: [forced ? h('p.sheet-text', t('An administrator reset your password: choose a new one to continue.')) : null,
        needsCurrent ? field(forced ? t('Temporary password') : t('Current password'), cur) : null, field(t('New password (min. 8 characters)'), p1), field(t('Repeat the new password'), p2)],
      actions: [forced ? btn(t('Sign out'), { kind: 'text', onclick: () => { s.close(); logout(); } }) : cancelBtn(), ok],
    });
  }

  async function deleteAccount() {
    const hasPw = S.user.hasPassword;
    const pw = input({ type: hasPw ? 'password' : 'text', autocomplete: hasPw ? 'current-password' : 'off' });
    const ok = btn(t('Delete permanently'), { kind: 'danger', icon: 'delete', onclick: async () => {
      await busy(ok, () => Api.post('auth.delete', hasPw ? { password: pw.value } : { confirm: pw.value }));
      s.close(); S.user = null; S.loaded = false; toast(t('Account deleted')); render();
    } });
    const s = sheet({
      title: t('Delete the account?'),
      body: [h('p.sheet-text', t('Your account will be deleted permanently. Households where you are the only member will be deleted with all their data; in shared ones ownership will pass to another member.')),
        field(hasPw ? 'Password' : t('Type DELETE to confirm'), pw)],
      actions: [cancelBtn(), ok],
    });
  }

  async function logout() {
    try { await Api.post('auth.logout', {}); } catch (e) { /* already signed out */ }
    try { if (window.google && google.accounts) google.accounts.id.disableAutoSelect(); } catch (e) { /* not loaded */ }
    S.user = null; S.loaded = false;
    location.hash = '#/spaces';
    render();
  }

  function viewJoin(code) {
    code = String(code || '').toUpperCase();
    setTimeout(async () => {
      if (!code) return go('spaces');
      if (S.household.inviteCode === code) { toast(t('You are already in this household')); return go('spaces'); }
      if (await confirm(t('Join the household?'), t('You are about to join the household with code {code}: you will see and edit its spaces, products and shopping lists.', { code }), t('Join'))) {
        try { applyState(await Api.post('household.join', { code })); toast(t('Welcome to “{home}”!', { home: S.household.name })); }
        catch (e) { toast(e.message, 'error'); }
      }
      go('spaces');
    }, 50);
    return { tab: 'spaces', title: t('Invitation'), back: 'spaces', body: [h('div.loader')] };
  }

  // ===================================================== Google Identity Services

  async function googleButton(slot, onCredential, text) {
    if (!S.config.googleClientId) { slot.hidden = true; return; }
    await UI.googleButton(slot, S.config.googleClientId, onCredential, text);
  }

  async function googleSignIn(credential, pending) {
    try {
      const d = await Api.post('auth.google', { credential, inviteCode: pending || '' }, { silentAuth: true });
      S.user = d.user; afterLogin(pending, true);
    } catch (e) {
      if (e.code !== 'needs_signup') { toast(e.message, 'error'); return; }
      // New account: privacy consent (and optional invite code) before creating it
      const privacy = h('input', { type: 'checkbox' });
      const code = input({ value: pending || '', maxlength: 12, autocapitalize: 'characters' });
      const ok = btn(t('Create account'), { icon: 'check', onclick: async () => {
        if (!privacy.checked) { toast(t('To continue, accept the privacy policy.'), 'error'); return; }
        const d = await busy(ok, () => Api.post('auth.google', { credential, acceptPrivacy: true, inviteCode: code.value }, { silentAuth: true }));
        s.close(); S.user = d.user; setTimeout(() => afterLogin(code.value, true), 150);
      } });
      const s = sheet({
        title: t('Create your account'),
        body: [
          h('div.card', { style: { marginBottom: '8px' } }, listRow({ lead: h('span.avatar', initials(e.data.name || e.data.email)), title: e.data.name || t('New account'), sub: e.data.email })),
          field(t('Invite code (optional)'), code, { hint: t('If someone invited you to their household') }),
          h('label.check', privacy, h('span', t('I have read and accept the '), h('a', { href: 'privacy.html', target: '_blank', rel: 'noopener' }, t('privacy policy')))),
        ],
        actions: [cancelBtn(), ok],
      });
    }
  }

  function openLinkGoogle() {
    const slot = h('div.google-slot');
    const s = sheet({
      title: t('Link your Google account'),
      body: [h('p.sheet-text', t('Once linked, you can sign in to FoodTrack with one tap, without a password.')), slot],
      actions: [cancelBtn()],
    });
    googleButton(slot, async credential => {
      try { const d = await Api.post('auth.google.link', { credential }); S.user = d.user; s.close(); render(true); toast(t('Google account linked')); }
      catch (e) { toast(e.message, 'error'); }
    }, 'signin_with');
  }

  // ===================================================== AUTH

  function afterLogin(pending, viaGoogle) {
    if (pending && !viaGoogle) location.hash = '#/join/' + pending;
    else if (pending) location.hash = '#/spaces';
    start();
  }

  function features(cls) {
    return h('ul.features.' + cls,
      h('li', tile('icon', 'spaces', 'sm'), t('Keep track of what is in your fridge, freezer and pantry')),
      h('li', tile('icon', 'bell', 'sm'), t('Get an alert before products expire')),
      h('li', tile('icon', 'group', 'sm'), t('Shopping lists shared with the people you live with')),
      h('li', tile('icon', 'restaurant', 'sm'), t('Recipes with the ingredients you already have')));
  }

  function renderAuth(mode) {
    const pending = (location.hash.match(/^#\/(?:join|unisciti)\/([A-Za-z0-9]+)/) || [])[1] || '';
    mode = mode || (pending ? 'register' : store.get('authMode', 'login'));
    const err = h('div.err-box', { hidden: true });
    const showErr = m => { UI.clear(err); err.append(icon('error'), h('span', m)); err.hidden = false; };
    const email = input({ type: 'email', autocomplete: 'email', inputmode: 'email', maxlength: 190 });
    const pw = input({ type: 'password', autocomplete: mode === 'login' ? 'current-password' : 'new-password' });
    const name = input({ autocomplete: 'name', maxlength: 80 });
    const code = input({ value: pending, maxlength: 12, autocapitalize: 'characters' });
    const privacy = h('input', { type: 'checkbox' });
    const gslot = h('div.google-slot');

    const submit = btn(mode === 'login' ? t('Sign in') : t('Create account'), { type: 'submit', cls: 'btn-block' });
    const form = h('form', { novalidate: true, onsubmit: async e => {
      e.preventDefault(); err.hidden = true;
      try {
        const d = await busy(submit, () => mode === 'login'
          ? Api.post('auth.login', { email: email.value, password: pw.value }, { silentAuth: true })
          : Api.post('auth.register', { name: name.value, email: email.value, password: pw.value, inviteCode: code.value, acceptPrivacy: privacy.checked }, { silentAuth: true }));
        S.user = d.user;
        afterLogin(pending && mode === 'login' ? pending : '', false);
      } catch (ex) { showErr(ex.message); }
    } },
      mode === 'register' ? field(t('Name'), name) : null,
      field('Email', email),
      field(mode === 'login' ? 'Password' : t('Password (min. 8 characters)'), pw),
      mode === 'register' ? field(t('Invite code (optional)'), code, { hint: t('If someone invited you to their household') }) : null,
      mode === 'register' ? h('label.check', privacy, h('span', t('I have read and accept the '), h('a', { href: 'privacy.html', target: '_blank', rel: 'noopener' }, t('privacy policy')))) : null,
      err, h('div.spacer'), submit,
      mode === 'login' ? h('div.center', { style: { marginTop: '6px' } }, btn(t('Forgot your password?'), { kind: 'text', onclick: () => {
        const s = sheet({ title: t('Forgot password'), body: [h('p.sheet-text', t('Write to us through the contact form, from the email address of your account: an administrator will send you a temporary password, to be changed at the first sign-in.'), S.config.googleClientId ? ' ' + t('If it is a Google account email, you can also sign in with “Continue with Google”.') : ''), h('div.btn-row', h('a.btn.btn-tonal', { href: CONTACT_URL, target: '_blank', rel: 'noopener' }, icon('open'), h('span', t('Open the contact form'))))], actions: [btn(t('Got it'), { onclick: () => s.close() })] });
      } })) : null,
    );

    root.replaceChildren(h('div.auth',
      h('div.auth-top', h('div.logo', h('img', { src: 'assets/icons/icon-color.png', alt: '' })), h('h1', 'FoodTrack'), h('p', t('The food at home, under control')), features('desk-only')),
      h('div.auth-body',
        pending ? banner('info', 'group', t('You have been invited!'), t('Create an account (or sign in) to join the shared household.')) : null,
        seg([['login', t('Sign in')], ['register', t('Create account')]], mode, v => { store.set('authMode', v); renderAuth(v); }),
        S.config.googleClientId ? [gslot, h('div.divider-text', t('or with your email'))] : null,
        form,
        features('mob-only'))));
    document.title = 'FoodTrack';
    if (S.config.googleClientId) googleButton(gslot, cred => googleSignIn(cred, pending), mode === 'login' ? 'continue_with' : 'signup_with');
  }

  function renderForcePassword() {
    root.replaceChildren(h('div.auth', h('div.auth-top', h('div.logo', h('img', { src: 'assets/icons/icon-color.png', alt: '' })), h('h1', 'FoodTrack'), h('p', t('Choose a new password')))));
    if (!UI.sheetsOpen()) openChangePassword(true);
  }

  // ===================================================== tutorial

  const MAJOR = VERSION.split('.')[0];
  /** Shown at the first sign-in on a device and the first time a new major version is opened. */
  function maybeShowTutorial() {
    const key = 'tutorial.' + S.user.id;
    // After a language change inside the tutorial the page reloads: reopen it on the same slide.
    let resume = null;
    try { resume = sessionStorage.getItem('ft.tourResume'); sessionStorage.removeItem('ft.tourResume'); } catch (e) { /* no storage */ }
    if (resume === null && store.get(key, '') === MAJOR) return;
    setTimeout(() => openTutorial(() => store.set(key, MAJOR), +resume || 0), resume === null ? 400 : 0);
  }

  /** Small illustrations built with the app's own components (tiles, chips, icons). */
  function illo(kind) {
    const el = illoContent(kind);
    el.classList.add('ill-' + kind); // per-slide animations (app.css, "Tutorial animations")
    return el;
  }
  function illoContent(kind) {
    const T = (f, n, size) => tile(f, n, size);
    const mini = (lead, title, sub, meta) => h('div.tour-card', lead, h('div', h('strong', title), sub ? h('small', sub) : null, meta ? h('div.meta', meta) : null));
    switch (kind) {
      case 'welcome': return h('div.illo', h('div.tour-logo', h('img', { src: 'assets/icons/icon-color.png', alt: '' })),
        h('div.float.f1', T('products', 'apple', 'sm')), h('div.float.f2', T('products', 'milk', 'sm')), h('div.float.f3', T('products', 'carrot', 'sm')), h('div.float.f4', T('products', 'bread', 'sm')));
      case 'spaces': return h('div.illo.col', mini(T('spaces', 'fridge'), t('Fridge'), null, [chip(plural(5, 'product', 'products'), 'neutral')]), mini(T('spaces', 'pantry'), t('Pantry'), null, [chip(plural(8, 'product', 'products'), 'neutral')]), mini(T('spaces', 'freezer'), t('Freezer'), null, [chip(t('Empty'), 'neutral')]));
      case 'products': return h('div.illo.col', mini(T('products', 'milk'), t('Whole milk'), t('Expires on ') + UI.fmtDate(UI.addDays(UI.todayStr(), 1)), [chip(t('Expires tomorrow'), 'warn', 'warning')]),
        mini(T('products', 'egg'), t('Eggs'), t('Expires on ') + UI.fmtDate(UI.addDays(UI.todayStr(), 9)), [chip(t('In {n} days', { n: 9 }), 'ok')]), h('div.tour-pill', icon('scan'), t('Scan barcode')));
      case 'alerts': return h('div.illo.col', h('div.banner.warn.tour-banner', icon('warning'), h('div.b-body', h('h3', t('2 expiring products')), h('p', t('Don’t let them expire: find out how to use them in great recipes!')))),
        h('div.banner.info.tour-banner', icon('info'), h('div.b-body', h('h3', t('About the expiry')), h('p', t('Some people test freshness by putting the egg in a glass of water…')))));
      case 'lists': return h('div.illo.col', mini(T('products', 'banana'), t('Bananas'), '4 ' + UI.unitLabel('pz'), null).appendChild(icon('cartAdd')).parentNode,
        h('div.tour-card.done', T('products', 'butter'), h('div', h('strong', t('Butter')), h('small', '250 g')), icon('cartRemove')), h('div.tour-pill', icon('move'), t('Move to a space')));
      case 'recipes': return h('div.illo.col', h('div.tour-recipe', h('div.cover', { style: { backgroundImage: 'url("assets/img/recipes/pancakes.jpg")' } }), h('div.meta', chip(t('You have all the ingredients'), 'ok', 'check'), chip(t('Uses expiring products'), 'warn'))),
        h('div.tour-pill', icon('calendar'), t('Weekly plan')));
      case 'share': return h('div.illo.col', h('div.tour-avatars', ['LV', 'MR', 'PB'].map(x => h('span.avatar', x))), h('div.code-box.tour-code', 'K7M2Q9XA'), h('div.tour-pill', icon('share'), t('Invite someone')));
      case 'donate': return h('div.illo.col',
        mini(T('icon', 'heart'), t('100% free & open-source'), t('No ads, no subscriptions'), [chip(t('Open-source'), 'ok', 'check')]),
        mini(T('icon', 'shield'), t('Server and maintenance'), t('Kept alive thanks to your support'), null),
        h('a.tour-pill', { href: DONATE_URL, target: '_blank', rel: 'noopener' }, icon('heart'), t('Donation')));
      default: return h('div.illo');
    }
  }

  function openTutorial(onDone, start) {
    const steps = [
      ['welcome', t('Welcome to FoodTrack'), t('Keep track of the food you have at home, avoid waste and find ideas for what to cook.')],
      ['spaces', t('1 · Create your spaces'), t('Fridge, freezer and pantry are ready. Add your own (cellar, cupboard…) with the “New space” button.')],
      ['products', t('2 · Add your products'), t('Open a space and tap “Add product”: pick it from the catalogue or scan the barcode, then set the expiry date.')],
      ['alerts', t('3 · Get alerts on expiry'), t('Expiring and expired products are highlighted, with tips to tell whether they are still good.')],
      ['lists', t('4 · Make shopping lists'), t('Add what you need, put it in the cart while shopping, then move it into a space once home.')],
      ['recipes', t('5 · Cook with what you have'), t('Recipes show which ingredients you already have; add the missing ones to a list or plan your week.')],
      ['share', t('6 · Share your household'), t('Invite family or flatmates from the Profile: you will all see and edit the same spaces and lists.')],
      ['donate', t('Support the project'), t('FoodTrack is a free and open-source project. If you find it useful, consider making a small donation in the Profile to help cover server and maintenance costs.')],
    ];
    let i = Math.min(Math.max(0, start || 0), steps.length - 1);
    const stage = h('div.tour-stage');
    // Language choice, available on every slide (the page reloads in the new language and resumes here)
    const langSeg = h('div.seg.tour-lang', { role: 'group', 'aria-label': t('Language') }, I18N.langs.map(([k, label]) =>
      h('button' + (k === I18N.lang ? '.sel' : ''), { type: 'button', lang: k, 'aria-pressed': String(k === I18N.lang), onclick: () => {
        if (k === I18N.lang) return;
        try { sessionStorage.setItem('ft.tourResume', String(i)); } catch (e) { /* no storage */ }
        I18N.set(k);
      } }, icon('globe'), label)));
    const dots = h('div.tour-dots');
    const next = btn('', { onclick: () => go(i + 1) });
    const back = btn(t('Back'), { kind: 'text', onclick: () => go(i - 1) });
    const skip = btn(t('Skip'), { kind: 'text', onclick: () => finish() });
    const box = h('div.tour', { role: 'dialog', 'aria-modal': 'true', 'aria-label': t('App tutorial') }, h('div.tour-top', langSeg, skip), stage, dots, h('div.tour-actions', back, next));
    const ovl = h('div.tour-ovl', box);
    function paintT() {
      const [kind, title, text] = steps[i];
      stage.replaceChildren(h('div.tour-slide', illo(kind), h('h2', title), h('p', text)));
      dots.replaceChildren(...steps.map((_, k) => h('button' + (k === i ? '.on' : ''), { type: 'button', 'aria-label': String(k + 1), onclick: () => go(k) })));
      back.style.visibility = i ? 'visible' : 'hidden';
      skip.style.visibility = i < steps.length - 1 ? 'visible' : 'hidden';
      next.querySelector('span') ? next.querySelector('span').textContent = (i === steps.length - 1 ? t('Get started') : t('Next')) : next.appendChild(h('span', i === steps.length - 1 ? t('Get started') : t('Next')));
    }
    function go(k) { if (k >= steps.length) return finish(); i = Math.max(0, k); paintT(); }
    function finish() { document.removeEventListener('keydown', key); ovl.classList.remove('open'); setTimeout(() => ovl.remove(), 200); if (onDone) onDone(); }
    const key = e => { if (e.key === 'ArrowRight') go(i + 1); else if (e.key === 'ArrowLeft') go(i - 1); else if (e.key === 'Escape') finish(); };
    document.addEventListener('keydown', key);
    let x0 = null;
    stage.addEventListener('pointerdown', e => { x0 = e.clientX; });
    stage.addEventListener('pointerup', e => { if (x0 === null) return; const dx = e.clientX - x0; x0 = null; if (Math.abs(dx) > 50) go(i + (dx < 0 ? 1 : -1)); });
    document.body.appendChild(ovl);
    paintT();
    requestAnimationFrame(() => ovl.classList.add('open'));
  }

  // ===================================================== notifications & sync

  function notifyExpiry() {
    if (!store.get('notify', false) || !UI.app.notificationsSupported() || UI.app.notificationPermission() !== 'granted') return;
    const today = UI.todayStr();
    if (store.get('notifiedOn', '') === today) return;
    const a = expiredItems().length, b = expiringItems().length;
    if (!a && !b) return;
    store.set('notifiedOn', today);
    const parts = [];
    if (b) parts.push(plural(b, 'expiring product', 'expiring products'));
    if (a) parts.push(plural(a, 'expired product', 'expired products'));
    const opts = { body: t('You have {what}. Find out how to use them before throwing them away!', { what: parts.join(t(' and ')) }), icon: 'assets/icons/icon-192.png', badge: 'assets/icons/icon-mono.png', tag: 'ft-expiry' };
    if (UI.app.notify('FoodTrack', opts.body)) return; // Android app: native notification
    if (navigator.serviceWorker && navigator.serviceWorker.controller) navigator.serviceWorker.ready.then(r => r.showNotification('FoodTrack', opts)).catch(() => {});
    else try { new Notification('FoodTrack', opts); } catch (e) { /* not allowed */ }
  }

  async function sync() {
    if (!S.user || !S.loaded || document.visibilityState !== 'visible') return;
    try {
      const d = await Api.get('state', { silentAuth: true });
      const before = S.signature;
      applyState(d);
      const active = document.activeElement;
      const typing = active && root.contains(active) && /INPUT|TEXTAREA|SELECT/.test(active.tagName);
      if (S.signature !== before && !typing) render(true);
    } catch (e) {
      if (e.status === 401) { S.user = null; S.loaded = false; render(); }
    }
  }

  function fatal(title, text, action) {
    root.replaceChildren(h('div.auth', h('div.auth-top', h('div.logo', h('img', { src: 'assets/icons/icon-color.png', alt: '' })), h('h1', 'FoodTrack'), h('p', title)),
      h('div.auth-body.center', h('p', text), action || btn(t('Try again'), { icon: 'swap', onclick: start }))));
  }

  async function start() {
    try {
      const [me, cfg] = await Promise.all([Api.get('auth.me', { silentAuth: true }), Api.get('auth.config', { silentAuth: true }).catch(() => null)]);
      S.user = me.user;
      if (cfg) S.config = cfg;
    } catch (e) {
      if (e.code === 'not_installed') return fatal(t('Installation not completed'), t('The database is not configured yet.'), h('a.btn.btn-filled', { href: 'install-db.php' }, t('Open install-db.php')));
      return fatal(t('Could not connect'), e.message);
    }
    if (!S.user) { S.loaded = false; render(); return; }
    if (S.user.mustChangePassword) { render(); return; }
    render();
    try {
      applyState(await Api.get('bootstrap'));
      S.loaded = true;
      render();
      notifyExpiry();
      maybeShowTutorial();
    } catch (e) {
      fatal(t('Could not load the data'), e.message);
    }
  }

  document.addEventListener('keydown', e => {
    if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey || UI.sheetsOpen() || /^(INPUT|TEXTAREA|SELECT)$/.test((e.target || {}).tagName || '')) return;
    const q = document.querySelector('main.view input[type=search]');
    if (q) { e.preventDefault(); q.focus(); } else if (S.user && S.loaded) { e.preventDefault(); go('search'); }
  });

  Api.onUnauthenticated(() => { S.user = null; S.loaded = false; UI.closeAllSheets(); render(); });
  window.addEventListener('hashchange', () => { trackDepth(); if (!UI.sheetsOpen()) render(); else { UI.closeAllSheets(); setTimeout(render, 130); } });
  document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'visible') sync(); });
  setInterval(sync, 30000);
  matchMedia('(prefers-color-scheme: dark)').addEventListener('change', UI.applyTheme);

  UI.applyTheme();
  trackDepth();
  if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => {}));
  start();
})();
