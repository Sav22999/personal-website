/* FoodTrack — web app (single page, hash routing). */
(function () {
  'use strict';

  const { h, icon, pic, btn, iconBtn, busy, field, input, textarea, select, sheet, actionSheet, confirm, toast, store, norm } = UI;

  const VERSION = '1.0.0';
  const SPACE_ICONS = ['fridge', 'freezer', 'pantry', 'kitchen', 'cellar', 'shelf', 'garage', 'laundry', 'snow'];
  const LIST_ICONS = ['home', 'cake', 'restaurant', 'book', 'pizza', 'box', 'car', 'tree'];
  const UNITS = ['pz', 'g', 'kg', 'ml', 'cl', 'l', 'conf', 'cucchiai', 'cucchiaini', 'q.b.'];
  const SLOTS = [['colazione', 'Colazione'], ['pranzo', 'Pranzo'], ['merenda', 'Merenda'], ['cena', 'Cena']];
  const SORTS = [
    ['exp-asc', 'Data scadenza: dalla più vicina alla più distante'],
    ['exp-desc', 'Data scadenza: dalla più distante alla più vicina'],
    ['qty-asc', 'Quantità: dalla minore alla maggiore'],
    ['qty-desc', 'Quantità: dalla maggiore alla minore'],
    ['name-asc', 'Nome: A a Z'],
    ['name-desc', 'Nome: Z a A'],
  ];
  const DISCLAIMER = 'Queste informazioni sono indicative e basate su pratiche comuni: non sostituiscono il tuo giudizio e FoodTrack non garantisce che il prodotto sia sicuro da consumare.';

  const S = {
    user: null, loaded: false,
    catalog: { categories: [], products: [] }, recipes: [],
    household: null, households: [], spaces: [], items: [], lists: [], listItems: [], plan: [], favorites: new Set(),
    signature: '',
  };
  const root = document.getElementById('app');
  let weekOffset = 0;
  let recipeQuery = '';
  let recipeFilters = { fav: false, expiring: false, expired: false, cookable: false };
  let searchState = { q: '', spaceId: '', categoryId: '', exp: 'all' };

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

  function expiryState(item) {
    if (!item.expiryDate) return null;
    const d = UI.daysUntil(item.expiryDate);
    if (d < 0) return 'expired';
    if (d <= noticeDays()) return 'expiring';
    return 'ok';
  }
  function expiryText(item) {
    if (!item.expiryDate) return 'Nessuna data di scadenza';
    const d = UI.daysUntil(item.expiryDate);
    const pre = item.expiryType === 'best_before' ? 'Preferibilmente entro il ' : 'Scade il ';
    return `${pre}${UI.fmtDate(item.expiryDate)}  (${d < 0 ? 'scaduto' : UI.relDays(d)})`;
  }
  const expiredItems = () => S.items.filter(i => expiryState(i) === 'expired');
  const expiringItems = () => S.items.filter(i => expiryState(i) === 'expiring');

  function expiryTip(item) {
    const p = productById(item.productId);
    if (p && p.expiryTip) return p.expiryTip;
    const c = categoryById(item.categoryId != null ? item.categoryId : (p && p.categoryId));
    if (c && c.expiryTip) return c.expiryTip;
    return item.expiryType === 'best_before'
      ? '«Da consumarsi preferibilmente entro» indica la data entro cui il prodotto mantiene le sue qualità. Se non è passato troppo tempo e la confezione è integra, verifica direttamente il prodotto: potrebbe essere ancora consumabile se non presenta muffe o alterazioni di colore, odore e sapore.'
      : '«Da consumarsi entro» indica un limite di sicurezza: dopo questa data è sconsigliato consumare il prodotto, soprattutto se fresco (carne, pesce, latticini freschi).';
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
    if (!comparable || have >= need.value) return { status: 'ok', matches, uncertain: !comparable };
    return { status: 'partial', matches, missing: need.value - have, unit: need.base };
  }

  function recipeStats(r) {
    let ok = 0;
    const expiring = new Set(expiringItems().map(i => i.id));
    const expired = new Set(expiredItems().map(i => i.id));
    let usesExpiring = false, usesExpired = false;
    for (const ing of r.ingredients) {
      const a = availability(ing);
      if (a.status === 'ok') ok++;
      for (const m of a.matches) { if (expiring.has(m.id)) usesExpiring = true; if (expired.has(m.id)) usesExpired = true; }
    }
    return { ok, total: r.ingredients.length, usesExpiring, usesExpired };
  }

  /** Missing ingredients of some recipes, merged by product and unit. */
  function missingFor(recipes) {
    const map = new Map();
    for (const r of recipes) {
      for (const ing of r.ingredients) {
        if (ing.unit === 'q.b.') { if (availability(ing).status === 'missing') add(ing, null, 'q.b.'); continue; }
        const a = availability(ing);
        if (a.status === 'missing') add(ing, ing.quantity, ing.unit);
        else if (a.status === 'partial') add(ing, a.missing, a.unit);
      }
    }
    function add(ing, qty, unit) {
      const key = (ing.productId || norm(ing.name)) + '|' + (unit ? UI.toBase(1, unit).base : '');
      const cur = map.get(key);
      if (cur && qty != null && cur.quantity != null) {
        const a = UI.toBase(cur.quantity, cur.unit), b = UI.toBase(qty, unit);
        if (a.base === b.base) { cur.quantity = a.value + b.value; cur.unit = a.base; return; }
      }
      if (!cur) map.set(key, { productId: ing.productId, name: ing.name, quantity: qty, unit: unit || 'pz' });
    }
    return [...map.values()];
  }

  // ===================================================== routing

  function route() {
    const parts = (location.hash.replace(/^#\/?/, '') || 'spazi').split('/');
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
    if (!S.loaded) { root.replaceChildren(h('div.loader')); return; }

    const r = route();
    const key = r.name + '/' + (r.id || '');
    const y = window.scrollY;
    const views = { liste: viewLists, lista: viewList, spazi: viewSpaces, spazio: viewSpace, ricette: viewRecipes, ricetta: viewRecipe, piano: viewPlan, cerca: viewSearch, profilo: viewProfile, unisciti: viewJoin };
    const v = (views[r.name] || viewSpaces)(r.id);
    if (!v) return;
    const tab = v.tab;
    const bar = v.back
      ? h('header.appbar.detail', iconBtn('back', 'Indietro', () => { if (curDepth > 0) history.back(); else location.replace('#/' + v.back); }), h('div.titles', h('h1', v.title)), v.actions || [])
      : h('header.appbar',
          h('div.titles', h('div.brand', h('img', { src: 'assets/icons/icon-mono.png', alt: '' }), 'FoodTrack', householdChip()), h('h1', v.title)),
          v.actions || [],
          h('button.avatar', { type: 'button', title: 'Profilo', 'aria-label': 'Profilo e impostazioni', onclick: () => go('profilo') }, initials(S.user.name)));
    const main = h('main.view' + (v.nav === false ? '.no-nav' : ''), v.body);
    const nodes = [bar, main];
    if (v.nav !== false) nodes.push(bottomNav(tab));
    root.replaceChildren(...nodes);
    document.title = v.title === 'FoodTrack' ? 'FoodTrack' : v.title + ' · FoodTrack';
    if (keepScroll && key === lastRouteKey) window.scrollTo(0, y); else window.scrollTo(0, 0);
    lastRouteKey = key;
    if (v.after) v.after();
  }

  function householdChip() {
    if (!S.household) return null;
    const shared = S.household.members.length > 1;
    return h('button.household-chip', { type: 'button', title: 'Cambia casa', onclick: openHouseholdSwitcher }, icon(shared ? 'group' : 'home'), h('span', S.household.name));
  }

  function bottomNav(active) {
    const tab = (name, label, ic) => h('a' + (active === name ? '.active' : ''), { href: '#/' + name }, h('span.pill', icon(ic)), label);
    return h('nav.bottomnav', { 'aria-label': 'Sezioni' }, tab('liste', 'Liste', 'list'), tab('spazi', 'Spazi', 'spaces'), tab('ricette', 'Ricette', 'restaurant'));
  }

  function initials(name) {
    return String(name || '?').trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
  }

  // ===================================================== reusable pieces

  function moreBtn(items) { return iconBtn('more', 'Altre azioni', () => actionSheet(items)); }

  function iconPicker(folder, icons, value, onchange) {
    let cur = value;
    const wrap = h('div.icon-picker' + (folder === 'products' ? '.products' : ''), { role: 'radiogroup' });
    for (const ic of icons) {
      const b = h('button' + (ic === cur ? '.sel' : ''), { type: 'button', role: 'radio', 'aria-checked': String(ic === cur), title: ic, onclick: () => {
        cur = ic; wrap.querySelectorAll('button').forEach(x => { x.classList.toggle('sel', x === b); x.setAttribute('aria-checked', String(x === b)); }); onchange(ic);
      } }, pic(folder, ic));
      wrap.appendChild(b);
    }
    return wrap;
  }

  function nutriPicker(value, onchange, readonly) {
    let cur = value || null;
    const wrap = h('div.nutri' + (readonly ? '.readonly' : ''), { role: 'radiogroup', 'aria-label': 'Nutri-Score' });
    for (const g of ['A', 'B', 'C', 'D', 'E']) {
      const b = h('button.' + g + (g === cur ? '.sel' : ''), { type: 'button', disabled: !!readonly, role: 'radio', 'aria-checked': String(g === cur), onclick: () => {
        cur = cur === g ? null : g;
        wrap.querySelectorAll('button').forEach(x => x.classList.toggle('sel', x.textContent === cur));
        onchange(cur);
      } }, g);
      wrap.appendChild(b);
    }
    return wrap;
  }

  function seg(options, value, onchange) {
    const wrap = h('div.seg');
    for (const [v, label] of options) {
      const b = h('button' + (v === value ? '.sel' : ''), { type: 'button', onclick: () => { wrap.querySelectorAll('button').forEach(x => x.classList.toggle('sel', x === b)); onchange(v); } }, label);
      wrap.appendChild(b);
    }
    return wrap;
  }

  function unitSelect(value) {
    const opts = UNITS.slice();
    if (value && !opts.includes(value)) opts.push(value);
    return select(opts.map(u => ({ value: u, label: u })), value || 'pz');
  }

  function empty(text, img) {
    return h('div.empty', img ? h('img', { src: img, alt: '' }) : null, h('p', text));
  }

  function tipBanner(key, text) {
    if (store.get('tip.' + key, false)) return null;
    const b = h('div.banner.tip', h('h3', 'Suggerimento'), h('p', text),
      h('div.banner-actions', btn('OK, nascondi', { onclick: () => { store.set('tip.' + key, true); b.remove(); } })));
    return b;
  }

  // ===================================================== SPACES

  function viewSpaces() {
    const body = [];
    const exp = expiredItems(), soon = expiringItems();
    const sig = a => a.map(i => i.id).sort().join(',');
    if (exp.length && store.get('hide.expired', '') !== sig(exp)) {
      body.push(h('div.banner.danger',
        h('h3', icon('warning'), 'Prodotti scaduti'),
        h('p', 'Hai dei prodotti che sono scaduti. Alcuni prodotti anche se scaduti possono essere ugualmente consumati, nel breve periodo successivo alla scadenza. Scopri di più!'),
        h('div.banner-actions',
          btn('OK, nascondi', { kind: 'text', onclick: () => { store.set('hide.expired', sig(exp)); render(true); } }),
          btn('Scopri di più', { onclick: () => openExpirySheet('expired') }))));
    }
    if (soon.length && store.get('hide.expiring', '') !== sig(soon)) {
      body.push(h('div.banner.warn',
        h('h3', icon('warning'), 'Prodotti in scadenza'),
        h('p', 'Hai dei prodotti che scadranno a breve. Non lasciare che scadano, scopri subito come utilizzarli per cucinare delle fantastiche ricette!'),
        h('div.banner-actions',
          btn('OK, nascondi', { kind: 'text', onclick: () => { store.set('hide.expiring', sig(soon)); render(true); } }),
          btn('Scopri di più', { onclick: () => openExpirySheet('expiring') }))));
    }

    if (!S.spaces.length) body.push(empty('Nessuno spazio creato', 'assets/img/spaces/fridge.png'));
    const list = h('div.stack');
    for (const sp of S.spaces) {
      const items = S.items.filter(i => i.spaceId === sp.id);
      const nExp = items.filter(i => expiryState(i) === 'expired').length;
      const nSoon = items.filter(i => expiryState(i) === 'expiring').length;
      list.appendChild(h('div.card', { role: 'button', tabindex: 0, onclick: () => go('spazio/' + sp.id), onkeydown: e => { if (e.key === 'Enter') go('spazio/' + sp.id); } },
        h('div.card-head', h('h3.card-title', sp.name), moreBtn([
          { icon: 'edit', label: 'Modifica', onclick: () => openSpaceForm(sp) },
          { icon: 'delete', label: 'Elimina', danger: true, onclick: () => deleteSpace(sp) },
        ])),
        sp.notes ? h('p.card-notes', sp.notes) : null,
        h('div.card-foot',
          h('span', items.length ? `${items.length} ${items.length === 1 ? 'prodotto' : 'prodotti'}` : 'Nessun prodotto in questo spazio',
            nExp ? h('span.badge-danger', ` · ${nExp} scadut${nExp === 1 ? 'o' : 'i'}`) : null,
            nSoon ? h('span.badge-warn', ` · ${nSoon} in scadenza`) : null),
          pic('spaces', sp.icon))));
    }
    body.push(list);
    body.push(h('div.center-btn', btn('Crea un nuovo spazio', { icon: 'addCircle', onclick: () => openSpaceForm(null) })));
    return { tab: 'spazi', title: 'Spazi della casa', body, actions: [iconBtn('search', 'Cerca un alimento', () => go('cerca'))] };
  }

  function openExpirySheet(kind) {
    const items = kind === 'expired' ? expiredItems() : expiringItems();
    const text = kind === 'expired'
      ? 'Alcuni prodotti presenti nei tuoi spazi sono scaduti. Se riportano la dicitura «Da consumare preferibilmente entro il…» verifica direttamente i prodotti: potrebbero essere ancora consumabili se non presentano evidenti muffe, alterazioni di colore, di odore e di sapore. Scopri nuove ricette per evitare di buttarli! I prodotti scaduti sono i seguenti.'
      : 'Alcuni prodotti presenti nei tuoi spazi scadranno a breve. Scopri nuove ricette per evitare di buttarli! I prodotti in scadenza sono i seguenti.';
    const s = sheet({
      title: kind === 'expired' ? 'Prodotti scaduti' : 'Prodotti in scadenza',
      body: [h('p.sheet-text', text), h('div.stack', items.map(i => {
        const sp = spaceById(i.spaceId);
        return h('button.card.item', { type: 'button', onclick: () => openItemDetail(i) }, pic('products', i.icon),
          h('div.item-main', h('p.item-name', i.name), h('div.item-sub', 'in ' + (sp ? sp.name : '—')), h('div.item-sub.' + kind, expiryText(i))));
      }))],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }),
        btn('Scopri nuove ricette', { icon: 'restaurant', onclick: () => { recipeFilters = { fav: false, expiring: kind === 'expiring', expired: kind === 'expired', cookable: false }; recipeQuery = ''; go('ricette'); } })],
    });
  }

  function openSpaceForm(sp) {
    let iconSel = sp ? sp.icon : 'fridge';
    const name = input({ value: sp ? sp.name : '', maxlength: 60 });
    const notes = textarea({ value: sp ? sp.notes : '', maxlength: 500 });
    const save = btn(sp ? 'Salva e modifica' : 'Salva e aggiungi', { icon: 'save', onclick: async () => {
      if (!name.value.trim()) { toast('Inserisci il nome dello spazio.', 'error'); name.focus(); return; }
      await act('space.save', { id: sp ? sp.id : 0, name: name.value, icon: iconSel, notes: notes.value }, save);
      s.close();
    } });
    const s = sheet({
      title: sp ? 'Modifica spazio della casa' : 'Nuovo spazio della casa',
      body: [field('Nome', name), iconPicker('spaces', SPACE_ICONS, iconSel, v => { iconSel = v; }), field('Note', notes)],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), save],
    });
  }

  async function deleteSpace(sp) {
    const n = S.items.filter(i => i.spaceId === sp.id).length;
    if (!await confirm('Eliminare lo spazio?', n ? `«${sp.name}» contiene ${n} prodotti: verranno eliminati anche loro, per tutti i membri della casa.` : `Lo spazio «${sp.name}» verrà eliminato.`, 'Elimina', true)) return;
    await act('space.delete', { id: sp.id });
    toast('Spazio eliminato');
  }

  function sortItems(items, mode) {
    const by = {
      'exp-asc': (a, b) => (a.expiryDate || '9999').localeCompare(b.expiryDate || '9999'),
      'exp-desc': (a, b) => (b.expiryDate || '0000').localeCompare(a.expiryDate || '0000'),
      'qty-asc': (a, b) => (a.quantity ?? Infinity) - (b.quantity ?? Infinity),
      'qty-desc': (a, b) => (b.quantity ?? -Infinity) - (a.quantity ?? -Infinity),
      'name-asc': (a, b) => a.name.localeCompare(b.name, 'it'),
      'name-desc': (a, b) => b.name.localeCompare(a.name, 'it'),
    }[mode] || ((a, b) => 0);
    return items.slice().sort(by);
  }

  function itemCard(i, opts) {
    opts = opts || {};
    const st = expiryState(i);
    const sp = spaceById(i.spaceId);
    const qty = UI.fmtQty(i.quantity, i.unit);
    return h('div.card.item', { role: 'button', tabindex: 0, onclick: () => openItemDetail(i), onkeydown: e => { if (e.key === 'Enter') openItemDetail(i); } },
      pic('products', i.icon),
      h('div.item-main',
        h('p.item-name', i.name, i.brand ? h('span.muted.small', ' · ' + i.brand) : null),
        h('div.item-sub' + (st === 'expired' ? '.expired' : st === 'expiring' ? '.expiring' : ''), expiryText(i)),
        (qty || opts.showSpace) ? h('div.item-sub.muted', [qty ? 'Quantità: ' + qty : null, opts.showSpace && sp ? 'in ' + sp.name : null].filter(Boolean).join(' · ')) : null),
      moreBtn([
        { icon: 'edit', label: 'Modifica', onclick: () => openItemForm({ item: i }) },
        { icon: 'check', label: 'Segna come consumato', onclick: () => consumeItem(i) },
        { icon: 'cartAdd', label: 'Aggiungi alla lista della spesa', onclick: () => addToListSheet([{ productId: i.productId, name: i.name, quantity: i.quantity, unit: i.unit }]) },
        { icon: 'delete', label: 'Elimina', danger: true, onclick: () => deleteItem(i) },
      ]));
  }

  function viewSpace(id) {
    const sp = spaceById(Number(id));
    if (!sp) { go('spazi'); return null; }
    const mode = store.get('sort', 'exp-asc');
    const items = sortItems(S.items.filter(i => i.spaceId === sp.id), mode);
    const body = [];
    if (!items.length) body.push(empty('Non sono presenti prodotti in questo spazio', 'assets/img/spaces/' + sp.icon + '.png'));
    body.push(h('div.stack', items.map(i => itemCard(i))));
    body.push(h('div.center-btn', btn('Aggiungi un prodotto', { icon: 'addCircle', onclick: () => openProductSearch({ spaceId: sp.id }) })));
    return {
      tab: 'spazi', title: sp.name, back: 'spazi', body,
      actions: [iconBtn('sort', 'Ordina per', () => {
        const s = sheet({ body: SORTS.map(([k, label]) => h('button.sort-option' + (k === mode ? '.active' : ''), { type: 'button', onclick: () => { store.set('sort', k); s.close(); render(true); } }, label)) });
      })],
    };
  }

  // ---------- Item detail / forms ----------

  function openItemDetail(i) {
    const st = expiryState(i);
    const sp = spaceById(i.spaceId);
    const cat = categoryById(i.categoryId);
    const ro = (label, value) => field(label, input({ value: value || '—', readonly: true }));
    const body = [
      h('div.field-row', ro('Quantità', i.quantity != null ? UI.fmtNum(i.quantity) : ''), ro('Unità', i.unit)),
      ro(i.expiryType === 'best_before' ? 'Da consumarsi preferibilmente entro' : 'Data di scadenza', i.expiryDate ? UI.fmtDate(i.expiryDate) + (st ? '  (' + (st === 'expired' ? 'scaduto' : UI.relDays(UI.daysUntil(i.expiryDate))) + ')' : '') : ''),
      nutriPicker(i.nutriscore, () => {}, true),
      h('div.field-row', ro('Spazio', sp && sp.name), ro('Categoria', cat && cat.name)),
      i.brand ? ro('Marca', i.brand) : null,
      i.notes ? field('Note', textarea({ value: i.notes, readonly: true })) : null,
      h('p.small.muted', `Aggiunto ${i.createdBy ? 'da ' + i.createdBy + ' ' : ''}il ${UI.fmtDateTime(i.createdAt)}`),
    ];
    if (st === 'expired' || st === 'expiring') {
      body.unshift(h('div.banner.info', h('h3', icon('info'), 'Informazioni sulla scadenza'), h('p', expiryTip(i)), h('p.small', { style: { marginTop: '8px', opacity: .8 } }, DISCLAIMER)));
    }
    const s = sheet({
      title: i.name, body,
      actions: [
        btn('Chiudi', { kind: 'text', onclick: () => s.close() }),
        btn('Modifica', { kind: 'tonal', icon: 'edit', onclick: () => { s.close(); setTimeout(() => openItemForm({ item: i }), 240); } }),
        btn('Segna come consumato', { icon: 'check', onclick: () => { s.close(); setTimeout(() => consumeItem(i), 240); } }),
      ],
    });
  }

  function consumeItem(i) {
    if (i.quantity == null || i.quantity <= 1 && i.unit === 'pz') {
      return act('item.consume', { id: i.id }).then(() => toast(`«${i.name}» segnato come consumato`));
    }
    const qty = input({ type: 'number', inputmode: 'decimal', min: 0, step: 'any', value: String(i.quantity) });
    const s = sheet({
      title: 'Segna come consumato',
      body: [h('p.sheet-text', `Quanto hai consumato di «${i.name}»? Disponibile: ${UI.fmtQty(i.quantity, i.unit)}.`), field('Quantità consumata (' + i.unit + ')', qty)],
      actions: [
        btn('Annulla', { kind: 'text', onclick: () => s.close() }),
        btn('Tutto', { kind: 'tonal', onclick: async e => { await act('item.consume', { id: i.id }, e.currentTarget); s.close(); toast(`«${i.name}» consumato`); } }),
        btn('Conferma', { icon: 'check', onclick: async e => { await act('item.consume', { id: i.id, quantity: qty.value }, e.currentTarget); s.close(); toast('Quantità aggiornata'); } }),
      ],
    });
  }

  async function deleteItem(i) {
    if (!await confirm('Eliminare il prodotto?', `«${i.name}» verrà eliminato dallo spazio.`, 'Elimina', true)) return;
    await act('item.delete', { id: i.id });
    toast('Prodotto eliminato');
  }

  /** Product search sheet ("Aggiungi un prodotto"), used both for spaces and shopping lists. */
  function openProductSearch(ctx) {
    const q = input({ type: 'search', placeholder: 'Cerca prodotto', enterkeyhint: 'search' });
    const results = h('div.pick-list');
    const create = btn('Crea un nuovo prodotto', { icon: 'addCircle', onclick: () => choose({ name: q.value.trim() }) });
    const scanBtn = btn('Scansiona codice a barre', { kind: 'tonal', icon: 'scan', onclick: () => { s.close(); setTimeout(() => openScanner(ctx), 240); } });
    function choose(prefill) {
      s.close();
      setTimeout(() => ctx.listId ? openListItemForm({ listId: ctx.listId, prefill }) : openItemForm({ spaceId: ctx.spaceId, prefill }), 240);
    }
    function refresh() {
      const n = norm(q.value);
      let prods = S.catalog.products;
      if (n) prods = prods.filter(p => norm(p.name).includes(n)).sort((a, b) => norm(a.name).indexOf(n) - norm(b.name).indexOf(n));
      UI.clear(results);
      for (const p of prods.slice(0, 60)) {
        const cat = categoryById(p.categoryId);
        results.appendChild(h('button.pick', { type: 'button', onclick: () => choose({ productId: p.id, name: p.name, icon: p.icon, unit: p.unit, categoryId: p.categoryId }) },
          pic('products', p.icon, 'sm'), h('span', p.name, cat ? h('small', cat.name) : null)));
      }
      if (n && !prods.length) results.appendChild(h('p.center.muted', 'Nessun prodotto trovato'));
    }
    q.addEventListener('input', refresh);
    q.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); const first = results.querySelector('.pick'); if (first) first.click(); else if (q.value.trim()) create.click(); } });
    const s = sheet({
      title: 'Aggiungi un prodotto',
      body: [h('div.search-box', icon('search'), q), h('p.small.muted', { style: { margin: '6px 2px 0' } }, 'Ricerca il prodotto che vuoi aggiungere. Se non lo trovi, puoi crearne uno nuovo.'),
        h('div.row', { style: { justifyContent: 'center', gap: '8px', flexWrap: 'wrap', margin: '10px 0 0' } }, create, ctx.listId ? null : scanBtn), results],
    });
    refresh();
  }

  /** New / edit item in a space. opts: { item } | { spaceId, prefill } | { fromListItem } */
  function openItemForm(opts) {
    const it = opts.item || null;
    const pre = Object.assign({}, opts.prefill || {});
    const li = opts.fromListItem || null;
    const v = it ? Object.assign({}, it) : {
      productId: pre.productId || (li && li.productId) || null,
      name: pre.name || (li && li.name) || '', icon: pre.icon || (li && li.icon) || null,
      quantity: pre.quantity ?? (li ? li.quantity : null), unit: pre.unit || (li && li.unit) || 'pz',
      categoryId: pre.categoryId ?? null, brand: pre.brand || '', nutriscore: pre.nutriscore || null, barcode: pre.barcode || '',
      expiryDate: '', expiryType: 'use_by', notes: pre.notes || (li && li.notes) || '',
      spaceId: opts.spaceId || (S.spaces[0] && S.spaces[0].id),
    };
    if (!v.icon) { const p = productById(v.productId); v.icon = p ? p.icon : 'generic'; }
    if (v.categoryId == null && v.productId) { const p = productById(v.productId); if (p) v.categoryId = p.categoryId; }
    if (!S.spaces.length) { toast('Crea prima uno spazio della casa.', 'error'); return; }

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
    const notes = textarea({ value: v.notes || '', maxlength: 500 });
    const barcode = input({ value: v.barcode || '', inputmode: 'numeric', maxlength: 32 });
    const more = h('div', { hidden: !(v.brand || v.barcode) && !it },
      h('div.field-row', field('Marca', brand), field('Categoria', cat)),
      h('p.small.muted', { style: { margin: '4px 2px' } }, 'Icona'),
      iconPicker('products', iconList(), iconSel, x => { iconSel = x; }),
      field('Codice a barre', barcode));
    const moreToggle = btn(more.hidden ? 'Altri dettagli (marca, categoria, icona)' : '', { kind: 'text', icon: 'add', onclick: () => { more.hidden = false; moreToggle.remove(); } });
    if (!more.hidden) moreToggle.hidden = true;

    const title = it ? 'Modifica prodotto' : li ? 'Sposta prodotto in uno spazio' : 'Nuovo prodotto';
    const saveLabel = it ? 'Salva e modifica' : li ? 'Sposta in questo spazio' : 'Salva e aggiungi';
    const save = btn(saveLabel, { icon: li ? 'move' : 'save', onclick: async () => {
      if (!name.value.trim()) { toast('Inserisci il nome del prodotto.', 'error'); name.focus(); return; }
      const payload = {
        id: it ? it.id : 0, spaceId: Number(space.value), productId: matchProduct(name.value, v.productId),
        name: name.value, quantity: qty.value, unit: unit.value, expiryDate: date.value, expiryType: expType,
        nutriscore: ns, brand: brand.value, categoryId: cat.value || null, icon: iconSel, barcode: barcode.value, notes: notes.value,
      };
      if (li) { payload.id = li.id; await act('listitem.tospace', payload, save); toast(`«${payload.name}» spostato in ${spaceById(payload.spaceId).name}`); }
      else { await act('item.save', payload, save); toast(it ? 'Prodotto modificato' : `«${payload.name}» aggiunto`); }
      s.close();
    } });
    const s = sheet({
      title,
      body: [
        field(li ? 'Seleziona uno spazio' : 'Spazio', space), field('Nome', name),
        h('div.field-row', field('Quantità', qty), field('Unità', unit)),
        field('Data di scadenza', date),
        seg([['use_by', 'Da consumarsi entro'], ['best_before', 'Preferibilmente entro']], expType, x => { expType = x; }),
        nutriPicker(ns, x => { ns = x; }),
        moreToggle, more,
        field('Note', notes),
      ],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), save],
    });
  }

  function iconList() {
    const set = new Set(['generic']);
    S.catalog.products.forEach(p => set.add(p.icon));
    return [...set].sort();
  }

  /** Keeps the catalog link only if the name still matches a catalog product. */
  function matchProduct(name, productId) {
    const n = norm(name);
    const p = productById(productId);
    if (p && norm(p.name) === n) return p.id;
    const q = S.catalog.products.find(x => norm(x.name) === n);
    return q ? q.id : null;
  }

  // ---------- Barcode scanner (BarcodeDetector + Open Food Facts) ----------

  function openScanner(ctx) {
    const code = input({ inputmode: 'numeric', placeholder: ' ', maxlength: 14 });
    const status = h('p.small.muted.center', '');
    const video = h('video', { playsinline: true, muted: true, autoplay: true });
    const cam = h('div.scanner', video);
    let stream = null, stopped = false;
    const supported = 'BarcodeDetector' in window && navigator.mediaDevices && navigator.mediaDevices.getUserMedia;
    if (!supported) { cam.hidden = true; status.textContent = 'La scansione con la fotocamera non è supportata da questo browser: inserisci il codice a mano.'; }

    async function lookup(c) {
      c = String(c || '').replace(/\D/g, '');
      if (c.length < 6) { toast('Codice non valido.', 'error'); return; }
      stop();
      status.textContent = 'Cerco il prodotto…';
      try {
        const d = await Api.get('barcode.lookup&code=' + c);
        s.close();
        const prefill = { barcode: c };
        if (d.found) {
          const p = d.product;
          Object.assign(prefill, { name: p.name, brand: p.brand, nutriscore: p.nutriscore });
          if (p.quantity) { prefill.quantity = p.quantity; prefill.unit = p.unit; }
          const match = S.catalog.products.find(x => p.name && norm(p.name).includes(norm(x.name)));
          if (match) Object.assign(prefill, { productId: match.id, icon: match.icon, categoryId: match.categoryId });
          toast('Prodotto trovato: ricordati di impostare la data di scadenza');
        } else {
          toast('Prodotto non trovato: inserisci i dati a mano');
        }
        setTimeout(() => openItemForm({ spaceId: ctx.spaceId, prefill }), 240);
      } catch (e) { status.textContent = e.message; }
    }
    function stop() { stopped = true; if (stream) stream.getTracks().forEach(t => t.stop()); }

    const s = sheet({
      title: 'Scansiona codice a barre',
      body: [cam, status, field('Oppure inserisci il codice a barre', code)],
      onclose: stop,
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), btn('Cerca', { icon: 'search', onclick: () => lookup(code.value) })],
    });
    code.addEventListener('keydown', e => { if (e.key === 'Enter') lookup(code.value); });
    if (supported) {
      (async () => {
        try {
          stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
          if (stopped) return stop();
          video.srcObject = stream;
          const detector = new window.BarcodeDetector({ formats: ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128'] });
          status.textContent = 'Inquadra il codice a barre del prodotto';
          const tick = async () => {
            if (stopped) return;
            try {
              const codes = await detector.detect(video);
              if (codes.length) { if (navigator.vibrate) navigator.vibrate(60); code.value = codes[0].rawValue; return lookup(codes[0].rawValue); }
            } catch (e) { /* frame not ready */ }
            setTimeout(tick, 250);
          };
          video.addEventListener('loadeddata', tick, { once: true });
        } catch (e) {
          cam.hidden = true;
          status.textContent = 'Fotocamera non disponibile (permesso negato?). Inserisci il codice a mano.';
        }
      })();
    }
  }

  // ===================================================== SHOPPING LISTS

  function viewLists() {
    const body = [];
    if (!S.lists.length) body.push(empty('Nessuna lista creata', 'assets/img/lists/home.png'));
    body.push(h('div.stack', S.lists.map(l => {
      const items = S.listItems.filter(x => x.listId === l.id);
      const inCart = items.filter(x => x.inCart).length;
      const txt = !items.length ? 'Nessun prodotto in questa lista'
        : `${items.length} ${items.length === 1 ? 'prodotto' : 'prodotti'}, ${inCart ? 'di cui ' + inCart + ' nel carrello' : 'nessuno nel carrello'}`;
      return h('div.card', { role: 'button', tabindex: 0, onclick: () => go('lista/' + l.id), onkeydown: e => { if (e.key === 'Enter') go('lista/' + l.id); } },
        h('div.card-head', h('h3.card-title', l.name), moreBtn([
          { icon: 'edit', label: 'Modifica', onclick: () => openListForm(l) },
          { icon: 'delete', label: 'Elimina', danger: true, onclick: () => deleteList(l) },
        ])),
        l.notes ? h('p.card-notes', l.notes) : null,
        h('div.card-foot', h('span', txt), pic('lists', l.icon)));
    })));
    body.push(h('div.center-btn', btn('Crea una nuova lista', { icon: 'addCircle', onclick: () => openListForm(null) })));
    return { tab: 'liste', title: 'Liste della spesa', body };
  }

  function openListForm(l, then) {
    let iconSel = l ? l.icon : 'home';
    const name = input({ value: l ? l.name : '', maxlength: 60 });
    const notes = textarea({ value: l ? l.notes : '', maxlength: 500 });
    const save = btn(l ? 'Salva e modifica' : 'Salva e aggiungi', { icon: 'save', onclick: async () => {
      if (!name.value.trim()) { toast('Inserisci il nome della lista.', 'error'); name.focus(); return; }
      const d = await act('list.save', { id: l ? l.id : 0, name: name.value, icon: iconSel, notes: notes.value }, save);
      s.close();
      if (then) setTimeout(() => then(d.id), 240);
    } });
    const s = sheet({
      title: l ? 'Modifica lista della spesa' : 'Nuova lista della spesa',
      body: [field('Nome', name), iconPicker('lists', LIST_ICONS, iconSel, v => { iconSel = v; }), field('Note', notes)],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), save],
    });
  }

  async function deleteList(l) {
    if (!await confirm('Eliminare la lista?', `La lista «${l.name}» e i suoi prodotti verranno eliminati per tutti i membri della casa.`, 'Elimina', true)) return;
    await act('list.delete', { id: l.id });
    if (route().name === 'lista') go('liste');
    toast('Lista eliminata');
  }

  function listItemCard(x) {
    return h('div.card.item' + (x.inCart ? '.in-cart' : ''), { role: 'button', tabindex: 0, onclick: () => openListItemDetail(x) },
      pic('products', x.icon),
      h('div.item-main', h('p.item-name', x.name),
        h('div.item-sub', 'Quantità: ' + (UI.fmtQty(x.quantity, x.unit) || '—')),
        x.notes ? h('div.item-sub.muted', x.notes) : null),
      h('div.item-actions',
        moreBtn([
          { icon: 'edit', label: 'Modifica', onclick: () => openListItemForm({ listId: x.listId, item: x }) },
          x.inCart ? { icon: 'move', label: 'Sposta in uno spazio', onclick: () => openItemForm({ fromListItem: x }) } : null,
          { icon: 'delete', label: 'Elimina', danger: true, onclick: () => act('listitem.delete', { id: x.id }).then(() => toast('Prodotto rimosso dalla lista')) },
        ]),
        iconBtn(x.inCart ? 'cartRemove' : 'cartAdd', x.inCart ? 'Togli dal carrello' : 'Metti nel carrello', () => act('listitem.cart', { id: x.id, inCart: !x.inCart }), 'sm')));
  }

  function viewList(id) {
    const l = listById(Number(id));
    if (!l) { go('liste'); return null; }
    const items = S.listItems.filter(x => x.listId === l.id);
    const toBuy = items.filter(x => !x.inCart), cart = items.filter(x => x.inCart);
    const body = [h('h2.section', 'Prodotti da acquistare')];
    if (toBuy.length) body.push(tipBanner('cart', 'Fai tap sull’icona del carrello per inserire un prodotto nel carrello'));
    if (!toBuy.length) body.push(empty('Non sono presenti prodotti da acquistare'));
    body.push(h('div.stack', toBuy.map(listItemCard)));
    body.push(h('div.center-btn', btn('Aggiungi un prodotto', { icon: 'addCircle', onclick: () => openProductSearch({ listId: l.id }) })));
    body.push(h('h2.section', 'Prodotti nel carrello'));
    if (cart.length) body.push(tipBanner('spaces', 'Ricordati di spostare i prodotti acquistati negli spazi, una volta tornato a casa!'));
    if (!cart.length) body.push(empty('Non hai ancora inserito prodotti nel carrello'));
    body.push(h('div.stack', cart.map(listItemCard)));
    if (cart.length) {
      body.push(h('div.center-btn', { style: { gap: '8px', flexWrap: 'wrap' } },
        btn('Sposta tutti in uno spazio', { kind: 'tonal', icon: 'move', onclick: () => moveAllToSpace(cart) }),
        btn('Svuota carrello', { kind: 'text', icon: 'cartRemove', onclick: async e => {
          if (await confirm('Svuotare il carrello?', 'I prodotti nel carrello verranno rimossi dalla lista senza essere spostati negli spazi.', 'Svuota', true)) act('list.clearcart', { id: l.id }, e.target.closest('button'));
        } })));
    }
    return {
      tab: 'liste', title: l.name, back: 'liste', body,
      actions: [iconBtn('more', 'Altre azioni', () => actionSheet([
        { icon: 'edit', label: 'Modifica lista', onclick: () => openListForm(l) },
        { icon: 'share', label: 'Condividi come testo', onclick: () => shareListText(l, toBuy) },
        { icon: 'delete', label: 'Elimina lista', danger: true, onclick: () => deleteList(l) },
      ]))],
    };
  }

  function moveAllToSpace(cart) {
    const space = select(S.spaces.map(sp => ({ value: sp.id, label: sp.name })), S.spaces[0] && S.spaces[0].id);
    const go2 = btn('Sposta tutti', { icon: 'move', onclick: async () => {
      await busy(go2, async () => {
        for (const x of cart) {
          const d = await Api.post('listitem.tospace', { id: x.id, spaceId: Number(space.value), productId: x.productId, name: x.name, icon: x.icon, quantity: x.quantity, unit: x.unit, notes: x.notes });
          applyState(d);
        }
      });
      render(true); s.close();
      toast(`${cart.length} prodotti spostati. Ricordati di impostare le date di scadenza!`);
    } });
    const s = sheet({
      title: 'Sposta tutti in uno spazio',
      body: [h('p.sheet-text', `Sposta i ${cart.length} prodotti nel carrello nello stesso spazio. Potrai impostare la data di scadenza di ciascuno in seguito.`), field('Seleziona uno spazio', space)],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), go2],
    });
  }

  async function shareListText(l, items) {
    const text = `${l.name}\n` + items.map(x => `• ${x.name}${UI.fmtQty(x.quantity, x.unit) ? ' — ' + UI.fmtQty(x.quantity, x.unit) : ''}`).join('\n');
    try {
      if (navigator.share) await navigator.share({ title: l.name, text });
      else { await navigator.clipboard.writeText(text); toast('Lista copiata negli appunti'); }
    } catch (e) { /* cancelled */ }
  }

  function openListItemDetail(x) {
    const ro = (label, value) => field(label, input({ value: value || '—', readonly: true }));
    const s = sheet({
      title: x.name,
      body: [h('div.field-row', ro('Quantità', x.quantity != null ? UI.fmtNum(x.quantity) : ''), ro('Unità', x.unit)), field('Note', textarea({ value: x.notes || '', readonly: true, placeholder: ' ' }))],
      actions: [
        btn('Chiudi', { kind: 'text', onclick: () => s.close() }),
        x.inCart
          ? btn('Sposta in uno spazio', { icon: 'move', onclick: () => { s.close(); setTimeout(() => openItemForm({ fromListItem: x }), 240); } })
          : btn('Sposta nel carrello', { icon: 'cartAdd', onclick: async e => { await act('listitem.cart', { id: x.id, inCart: true }, e.currentTarget); s.close(); } }),
      ],
    });
  }

  function openListItemForm(opts) {
    const x = opts.item || null;
    const pre = opts.prefill || {};
    const name = input({ value: x ? x.name : (pre.name || ''), maxlength: 80 });
    const p = productById(x ? x.productId : pre.productId);
    const qty = input({ type: 'number', inputmode: 'decimal', min: 0, step: 'any', value: x && x.quantity != null ? String(x.quantity) : '' });
    const unit = unitSelect(x ? x.unit : (pre.unit || (p && p.unit) || 'pz'));
    const notes = textarea({ value: x ? x.notes : '', maxlength: 500 });
    const save = btn(x ? 'Salva e modifica' : 'Salva e aggiungi', { icon: 'save', onclick: async () => {
      if (!name.value.trim()) { toast('Inserisci il nome del prodotto.', 'error'); name.focus(); return; }
      const pid = matchProduct(name.value, x ? x.productId : pre.productId);
      const pp = productById(pid);
      await act('listitem.save', { id: x ? x.id : 0, listId: opts.listId, productId: pid, name: name.value, icon: pp ? pp.icon : (x ? x.icon : 'generic'), quantity: qty.value, unit: unit.value, notes: notes.value }, save);
      s.close();
    } });
    const s = sheet({
      title: x ? 'Modifica prodotto' : 'Nuovo prodotto',
      body: [field('Nome', name), h('div.field-row', field('Quantità', qty), field('Unità', unit)), field('Note', notes)],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), save],
    });
  }

  /** Adds some products to a shopping list (existing or new). rows: [{productId, name, quantity, unit}] */
  function addToListSheet(rows, title) {
    if (!rows.length) { toast('Hai già tutto il necessario!'); return; }
    const checks = rows.map(r => ({ r, cb: h('input', { type: 'checkbox', checked: true }) }));
    const NEW = '__new__';
    const listSel = select(S.lists.map(l => ({ value: l.id, label: l.name })).concat([{ value: NEW, label: '+ Nuova lista…' }]), S.lists[0] ? S.lists[0].id : NEW);
    const newName = input({ value: title ? title : 'Spesa', maxlength: 60 });
    const newField = field('Nome della nuova lista', newName);
    newField.hidden = listSel.value !== NEW;
    listSel.addEventListener('change', () => { newField.hidden = listSel.value !== NEW; });
    const ok = btn('Aggiungi alla lista', { icon: 'cartAdd', onclick: async () => {
      const chosen = checks.filter(c => c.cb.checked).map(c => c.r);
      if (!chosen.length) { toast('Seleziona almeno un prodotto.', 'error'); return; }
      await busy(ok, async () => {
        let listId = listSel.value;
        if (listId === NEW) {
          if (!newName.value.trim()) throw new Error('Inserisci il nome della lista.');
          const d = await Api.post('list.save', { name: newName.value, icon: 'home' });
          applyState(d); listId = d.id;
        }
        const d = await Api.post('listitem.addmany', { listId: Number(listId), items: chosen });
        applyState(d);
        s.close(); render(true);
        toast(`${d.added} prodott${d.added === 1 ? 'o aggiunto' : 'i aggiunti'} alla lista`, null, { label: 'Apri', onclick: () => go('lista/' + listId) });
      });
    } });
    const s = sheet({
      title: 'Aggiungi alla lista della spesa',
      body: [
        h('div', checks.map(({ r, cb }) => h('label.check', cb, h('span', h('strong', r.name), UI.fmtQty(r.quantity, r.unit) ? ' — ' + UI.fmtQty(r.quantity, r.unit) : '')))),
        field('Lista della spesa', listSel), newField,
      ],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), ok],
    });
  }

  // ===================================================== RECIPES

  function recipeMatches(r) {
    const q = norm(recipeQuery);
    if (q && !norm(r.name).includes(q) && !r.ingredients.some(i => norm(i.name).includes(q))) return false;
    if (recipeFilters.fav && !S.favorites.has(r.id)) return false;
    if (recipeFilters.expiring || recipeFilters.expired || recipeFilters.cookable) {
      const st = recipeStats(r);
      if (recipeFilters.expiring && !st.usesExpiring) return false;
      if (recipeFilters.expired && !st.usesExpired) return false;
      if (recipeFilters.cookable && st.ok < st.total) return false;
    }
    return true;
  }

  function favBtn(r) {
    const on = S.favorites.has(r.id);
    return iconBtn(on ? 'heart' : 'heartOutline', on ? 'Rimuovi dalle preferite' : 'Aggiungi alle preferite', async () => {
      try {
        const d = await Api.post('favorite.toggle', { recipeId: r.id });
        if (d.favorite) S.favorites.add(r.id); else S.favorites.delete(r.id);
        render(true);
      } catch (e) { toast(e.message, 'error'); }
    }, on ? 'fav-on' : '');
  }

  function recipeCard(r) {
    const st = recipeStats(r);
    const time = (r.prepMinutes || 0) + (r.cookMinutes || 0);
    return h('div.card.recipe-card', { role: 'button', tabindex: 0, onclick: () => go('ricetta/' + r.id), onkeydown: e => { if (e.key === 'Enter') go('ricetta/' + r.id); } },
      h('div.cover', { style: { backgroundImage: r.image ? `url("${r.image}")` : 'none' } }),
      h('div.recipe-info',
        h('div.grow', { style: { flex: 1, minWidth: 0 } },
          h('p.recipe-name', r.name),
          h('div.recipe-meta', [time ? time + ' min' : null, r.difficulty, r.servings ? r.servings + ' porzioni' : null].filter(Boolean).join(' · ')),
          h('span.avail' + (st.ok < st.total ? '.warn' : ''), st.ok === st.total ? 'Hai tutti gli ingredienti' : `Hai ${st.ok} ingredienti su ${st.total}`),
          st.usesExpiring || st.usesExpired ? h('span.avail', { style: { marginLeft: '6px', background: 'var(--warn-bg)', color: 'var(--warn-text)' } }, st.usesExpired ? 'Usa prodotti scaduti' : 'Usa prodotti in scadenza') : null),
        favBtn(r)));
  }

  function viewRecipes() {
    const q = input({ type: 'search', value: recipeQuery, placeholder: 'Cerca ricetta', enterkeyhint: 'search' });
    const results = h('div.stack');
    const chips = h('div.chips');
    function paint() {
      UI.clear(results); UI.clear(chips);
      const labels = { fav: 'Preferite', expiring: 'Con prodotti in scadenza', expired: 'Con prodotti scaduti', cookable: 'Posso prepararle ora' };
      for (const k of Object.keys(labels)) if (recipeFilters[k]) chips.appendChild(h('button.chip.sel', { type: 'button', onclick: () => { recipeFilters[k] = false; paint(); } }, labels[k], icon('close', 'x')));
      const list = S.recipes.filter(recipeMatches);
      const filtering = recipeQuery || Object.values(recipeFilters).some(Boolean);
      if (!list.length) { results.appendChild(empty('Nessuna ricetta trovata')); return; }
      if (!filtering) {
        const fav = list.filter(r => S.favorites.has(r.id));
        if (fav.length) { results.appendChild(h('h2.section', 'Preferite')); fav.forEach(r => results.appendChild(recipeCard(r))); results.appendChild(h('h2.section', 'Tutte le ricette')); }
        // Recipes that use expiring products first: they help avoid waste
        list.sort((a, b) => (recipeStats(b).usesExpiring - recipeStats(a).usesExpiring) || (recipeStats(b).ok / (recipeStats(b).total || 1) - recipeStats(a).ok / (recipeStats(a).total || 1)));
      }
      list.forEach(r => results.appendChild(recipeCard(r)));
    }
    q.addEventListener('input', () => { recipeQuery = q.value; paint(); });
    paint();
    const planCard = h('button.card.item', { type: 'button', onclick: () => go('piano'), style: { marginBottom: '6px' } }, h('span.member-avatar', icon('calendar')),
      h('div.item-main', h('p.item-name', 'Piano della settimana'), h('div.item-sub.muted', planSummary())), icon('right'));
    return {
      tab: 'ricette', title: 'Ricette',
      body: [
        h('div.row', h('div.search-box', icon('search'), q), iconBtn('filter', 'Filtra le ricette', openRecipeFilters)),
        h('p.small.muted', { style: { margin: '4px 2px 8px' } }, 'Puoi cercare per nome o ingredienti'),
        chips, planCard, results,
      ],
    };
  }

  function planSummary() {
    const today = UI.todayStr();
    const n = S.plan.filter(p => p.date >= today && p.date <= UI.addDays(today, 6)).length;
    return n ? `${n} past${n === 1 ? 'o pianificato' : 'i pianificati'} nei prossimi 7 giorni` : 'Organizza i pasti e la spesa della settimana';
  }

  function openRecipeFilters() {
    const f = Object.assign({}, recipeFilters);
    const row = (k, label) => { const cb = h('input', { type: 'checkbox', checked: f[k], onchange: () => { f[k] = cb.checked; } }); return h('label.check', h('span', { style: { flex: 1 } }, label), cb); };
    const s = sheet({
      title: 'Filtra le ricette',
      body: [row('fav', 'Mostra solo le ricette preferite'), row('expiring', 'Mostra solo le ricette contenenti prodotti in scadenza'), row('expired', 'Mostra solo le ricette contenenti prodotti scaduti'), row('cookable', 'Mostra solo le ricette che posso preparare con ciò che ho')],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), btn('Applica filtri', { icon: 'filter', onclick: () => { recipeFilters = f; s.close(); render(true); } })],
    });
  }

  function viewRecipe(id) {
    const r = recipeById(Number(id));
    if (!r) { go('ricette'); return null; }
    const body = [];
    if (r.image) body.push(h('div.hero-img', { style: { backgroundImage: `url("${r.image}")` } }));
    if (r.description) body.push(h('p', { style: { margin: '0 0 6px' } }, r.description));
    body.push(h('div.meta-chips',
      h('span', icon('timer'), ((r.prepMinutes || 0) + (r.cookMinutes || 0)) + ' min'),
      h('span', 'Difficoltà: ' + r.difficulty),
      h('span', icon('group'), r.servings + ' porzioni')));
    body.push(h('h2.section', 'Ingredienti'));
    const missing = missingFor([r]);
    body.push(h('div.stack', r.ingredients.map(ing => {
      const a = availability(ing);
      const p = productById(ing.productId);
      const where = a.matches.length ? ' (in ' + [...new Set(a.matches.map(m => (spaceById(m.spaceId) || {}).name).filter(Boolean))].join(', ') + ')' : '';
      const av = a.status === 'ok' ? h('span.ok', 'Già disponi di questa quantità' + where)
        : a.status === 'partial' ? h('span.part', `Hai bisogno di altri ${UI.fmtQty(a.missing, a.unit)}` + where)
        : h('span.miss', 'Non presente nei tuoi spazi');
      return h('div.card.ingredient', pic('products', p ? p.icon : 'generic', 'sm'),
        h('div', h('div.ing-name', ing.name),
          h('div.ing-sub', 'Quantità: ' + (UI.fmtQty(ing.quantity, ing.unit) || '—')),
          ing.notes ? h('div.ing-sub', 'Note: ' + ing.notes) : null,
          h('div.ing-sub', 'Disponibilità: ', av)));
    })));
    body.push(h('div.center-btn', missing.length
      ? btn('Aggiungi i mancanti alla lista della spesa', { icon: 'cartAdd', onclick: () => addToListSheet(missing, r.name) })
      : h('p.muted.center', 'Hai tutti gli ingredienti necessari!')));
    body.push(h('h2.section', 'Tempo richiesto'));
    body.push(h('div.info-card', h('strong', 'Preparazione: ' + (r.prepMinutes != null ? r.prepMinutes + ' min' : '—'))));
    body.push(h('div.info-card', h('strong', 'Cottura: ' + (r.cookMinutes != null ? r.cookMinutes + ' min' : 'nessuna')), r.cookNotes ? h('div.small', r.cookNotes) : null));
    body.push(h('h2.section', 'Procedimento'));
    body.push(h('div', r.steps.map((t, i) => h('div.step', h('span.n', String(i + 1)), h('p', t)))));
    body.push(h('div.center-btn', btn('Aggiungi al piano settimanale', { kind: 'tonal', icon: 'calendar', onclick: () => openPlanForm({ recipeId: r.id, date: UI.todayStr() }) })));
    return { tab: 'ricette', title: r.name, back: 'ricette', body, actions: [favBtn(r)] };
  }

  // ===================================================== MEAL PLAN

  function weekStart(offset) {
    const d = UI.parseDate(UI.todayStr());
    const dow = (d.getDay() + 6) % 7; // Monday = 0
    d.setDate(d.getDate() - dow + offset * 7);
    return UI.dateStr(d);
  }

  function viewPlan() {
    const start = weekStart(weekOffset);
    const days = Array.from({ length: 7 }, (_, i) => UI.addDays(start, i));
    const today = UI.todayStr();
    const label = weekOffset === 0 ? 'Questa settimana' : weekOffset === 1 ? 'Settimana prossima' : weekOffset === -1 ? 'Settimana scorsa' : `${UI.fmtDate(days[0])} – ${UI.fmtDate(days[6])}`;
    const body = [h('div.week-nav',
      iconBtn('left', 'Settimana precedente', () => { if (weekOffset > -1) { weekOffset--; render(); } }),
      h('div.center', h('strong', label), h('div.small.muted', `${UI.fmtDate(days[0])} – ${UI.fmtDate(days[6])}`)),
      iconBtn('right', 'Settimana successiva', () => { if (weekOffset < 4) { weekOffset++; render(); } }))];
    for (const d of days) {
      const entries = S.plan.filter(p => p.date === d);
      body.push(h('div.day' + (d === today ? '.today' : ''),
        h('div.day-head', h('strong', UI.longDate(d)), iconBtn('add', 'Aggiungi un pasto', () => openPlanForm({ date: d }), 'sm')),
        entries.length ? entries.map(p => {
          const r = recipeById(p.recipeId);
          const slot = (SLOTS.find(x => x[0] === p.slot) || [, p.slot])[1];
          return h('div.meal', { role: 'button', tabindex: 0, onclick: () => actionSheet([
            r ? { icon: 'restaurant', label: 'Apri la ricetta', onclick: () => go('ricetta/' + r.id) } : null,
            { icon: 'edit', label: 'Modifica', onclick: () => openPlanForm(p) },
            { icon: 'delete', label: 'Elimina', danger: true, onclick: () => act('plan.delete', { id: p.id }) },
          ]) }, h('span.slot', slot), h('span.what', r ? r.name : p.title), r ? icon('restaurant') : null);
        }) : h('div.small.muted', { style: { padding: '4px 0 2px' } }, 'Nessun pasto pianificato')));
    }
    const weekRecipes = S.plan.filter(p => days.includes(p.date) && p.recipeId).map(p => recipeById(p.recipeId)).filter(Boolean);
    body.push(h('div.center-btn', btn('Aggiungi gli ingredienti mancanti alla lista', { icon: 'cartAdd', disabled: !weekRecipes.length, onclick: () => addToListSheet(missingFor(weekRecipes), 'Spesa settimanale') })));
    if (!weekRecipes.length) body.push(h('p.small.muted.center', 'Aggiungi delle ricette al piano per creare in automatico la lista della spesa della settimana.'));
    return { tab: 'ricette', title: 'Piano della settimana', back: 'ricette', body };
  }

  function openPlanForm(p) {
    const isEdit = !!p.id;
    const date = input({ type: 'date', value: p.date || UI.todayStr() });
    let slot = p.slot || (new Date().getHours() < 15 ? 'pranzo' : 'cena');
    const OTHER = '';
    const recipe = select([{ value: OTHER, label: 'Altro (scrivi tu)' }].concat(S.recipes.map(r => ({ value: r.id, label: r.name }))), p.recipeId || OTHER);
    const title = input({ value: p.title || '', maxlength: 120, placeholder: ' ' });
    const titleField = field('Cosa mangi?', title, { hint: 'Ad esempio: «Pizza da asporto» o «Avanzi di ieri»' });
    titleField.hidden = !!recipe.value;
    recipe.addEventListener('change', () => { titleField.hidden = !!recipe.value; });
    const save = btn(isEdit ? 'Salva e modifica' : 'Aggiungi al piano', { icon: 'calendar', onclick: async () => {
      await act('plan.save', { id: p.id || 0, date: date.value, slot, recipeId: recipe.value ? Number(recipe.value) : null, title: recipe.value ? '' : title.value }, save);
      s.close();
      toast(isEdit ? 'Pasto modificato' : 'Aggiunto al piano della settimana', null, route().name !== 'piano' ? { label: 'Apri', onclick: () => go('piano') } : null);
    } });
    const s = sheet({
      title: isEdit ? 'Modifica pasto' : 'Aggiungi un pasto',
      body: [field('Giorno', date), seg(SLOTS, slot, v => { slot = v; }), field('Ricetta', recipe), titleField],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), save],
    });
  }

  // ===================================================== SEARCH

  function viewSearch() {
    const q = input({ type: 'search', value: searchState.q, placeholder: 'Cerca un alimento', enterkeyhint: 'search' });
    const results = h('div');
    const spaceSel = select([{ value: '', label: 'Tutti gli spazi' }].concat(S.spaces.map(s => ({ value: s.id, label: s.name }))), searchState.spaceId);
    const catSel = select([{ value: '', label: 'Tutte le categorie' }].concat(S.catalog.categories.map(c => ({ value: c.id, label: c.name }))), searchState.categoryId);
    const expSeg = seg([['all', 'Tutti'], ['expiring', 'In scadenza'], ['expired', 'Scaduti'], ['none', 'Senza data']], searchState.exp, v => { searchState.exp = v; paint(); });

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
          results.appendChild(h('h3.sub', 'Ricerche recenti'));
          results.appendChild(h('div.chips', rec.map(t => h('button.chip', { type: 'button', onclick: () => { q.value = t; searchState.q = t; paint(); } }, icon('search'), t,
            h('span.x', { role: 'button', 'aria-label': 'Rimuovi', onclick: e => { e.stopPropagation(); store.set('recentSearches', store.get('recentSearches', []).filter(x => x !== t)); paint(); } }, icon('close'))))));
        }
        const recent = S.items.slice().sort((a, b) => b.createdAt.localeCompare(a.createdAt)).slice(0, 10);
        results.appendChild(h('h3.sub', 'Aggiunti di recente'));
        if (!recent.length) results.appendChild(empty('Non hai ancora aggiunto prodotti'));
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
      results.appendChild(h('h3.sub', found.length ? `${found.length} risultat${found.length === 1 ? 'o' : 'i'}` : 'Nessun prodotto trovato'));
      results.appendChild(h('div.stack', sortItems(found, 'exp-asc').map(i => h('div', { onclick: () => saveRecent(searchState.q) }, itemCard(i, { showSpace: true })))));
    }

    q.addEventListener('input', () => { searchState.q = q.value; paint(); });
    q.addEventListener('keydown', e => { if (e.key === 'Enter') { saveRecent(q.value); q.blur(); } });
    q.addEventListener('change', () => saveRecent(q.value));
    spaceSel.addEventListener('change', () => { searchState.spaceId = spaceSel.value; paint(); });
    catSel.addEventListener('change', () => { searchState.categoryId = catSel.value; paint(); });
    paint();
    return {
      tab: 'spazi', title: 'Cerca un alimento', back: 'spazi',
      body: [
        h('div.search-box', icon('search'), q),
        h('p.small.muted', { style: { margin: '4px 2px 4px' } }, 'Cerca per nome, marca, categoria, spazio o data di scadenza (gg/mm/aaaa)'),
        h('div.field-row', field('Spazio', spaceSel), field('Categoria', catSel)),
        expSeg, results,
      ],
      after: () => { if (!searchState.q && !matchMedia('(pointer: coarse)').matches) q.focus(); },
    };
  }

  // ===================================================== PROFILE & HOUSEHOLD

  function openHouseholdSwitcher() {
    const s = sheet({
      title: 'Le tue case',
      body: [
        h('p.sheet-text', 'Ogni casa ha i suoi spazi, prodotti, liste della spesa e piano dei pasti, condivisi con i membri che inviti.'),
        h('div.pick-list', S.households.map(hh => h('button.pick', { type: 'button', onclick: async () => {
          if (hh.id === S.household.id) { s.close(); return; }
          s.close(); await act('household.switch', { id: hh.id }); toast('Ora sei in «' + hh.name + '»');
        } }, icon(hh.members > 1 ? 'group' : 'home'), h('span', { style: { flex: 1 } }, hh.name, h('small', `${hh.members} ${hh.members === 1 ? 'membro' : 'membri'} · ${hh.role === 'owner' ? 'proprietario' : 'membro'}`)), hh.id === S.household.id ? icon('check') : null))),
      ],
      actions: [btn('Gestisci e invita', { kind: 'tonal', icon: 'group', onclick: () => go('profilo') })],
    });
  }

  function inviteLink() {
    return location.origin + location.pathname.replace(/[^/]*$/, '') + '#/unisciti/' + S.household.inviteCode;
  }

  function openInvite() {
    const link = inviteLink();
    const isOwner = S.household.role === 'owner';
    const s = sheet({
      title: 'Invita nella casa',
      body: [
        h('p.sheet-text', `Chi si unisce a «${S.household.name}» vedrà e potrà modificare spazi, prodotti, liste della spesa e piano dei pasti. Condividi questo codice o il link:`),
        h('div.code-box', S.household.inviteCode),
        h('div.row', { style: { justifyContent: 'center', margin: '12px 0', flexWrap: 'wrap', gap: '8px' } },
          btn('Copia link', { kind: 'tonal', icon: 'copy', onclick: async () => { try { await navigator.clipboard.writeText(link); toast('Link copiato'); } catch (e) { toast(link); } } }),
          navigator.share ? btn('Condividi', { icon: 'share', onclick: () => navigator.share({ title: 'FoodTrack', text: `Unisciti alla mia casa «${S.household.name}» su FoodTrack con il codice ${S.household.inviteCode}`, url: link }).catch(() => {}) }) : null),
        isOwner ? h('p.small.muted', 'Se il codice è finito nelle mani sbagliate puoi generarne uno nuovo: quello vecchio smetterà di funzionare.') : null,
      ],
      actions: isOwner ? [btn('Genera un nuovo codice', { kind: 'text', icon: 'swap', onclick: async e => { await act('household.code', {}, e.currentTarget); s.close(); openInvite(); } })] : [],
    });
  }

  function promptSheet(title, label, value, okLabel, onok, extra) {
    const inp = input({ value: value || '', maxlength: 80 });
    const ok = btn(okLabel, { icon: 'check', onclick: async () => { if (!inp.value.trim()) { inp.focus(); return; } await busy(ok, () => onok(inp.value.trim())); s.close(); } });
    inp.addEventListener('keydown', e => { if (e.key === 'Enter') ok.click(); });
    const s = sheet({ title, body: [extra ? h('p.sheet-text', extra) : null, field(label, inp)], actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), ok] });
  }

  function viewProfile() {
    const hh = S.household;
    const isOwner = hh.role === 'owner';
    const row = (ic, label, sub, onclick, cls) => h('button.list-row' + (cls ? '.' + cls : ''), { type: 'button', onclick }, icon(ic), h('span.grow', label, sub ? h('small', sub) : null));
    const theme = store.get('theme', 'auto');
    const notif = store.get('notify', false) && 'Notification' in window && Notification.permission === 'granted';

    const body = [
      h('div.row', { style: { margin: '4px 0 16px' } }, h('span.member-avatar', { style: { width: '52px', height: '52px', fontSize: '1.3rem' } }, initials(S.user.name)),
        h('div.grow', h('strong', { style: { fontSize: '1.2rem' } }, S.user.name), h('div.small.muted', S.user.email), S.user.isAdmin ? h('span.small', { style: { color: 'var(--primary-dark)', fontWeight: 700 } }, S.user.roleLabel) : null)),

      h('h3.sub', 'Casa condivisa'),
      h('div.list-group',
        h('div.list-row', { style: { cursor: 'default' } }, icon(hh.members.length > 1 ? 'group' : 'home'), h('span.grow', h('strong', hh.name), h('small', `${hh.members.length} ${hh.members.length === 1 ? 'membro' : 'membri'} · sei ${isOwner ? 'il proprietario' : 'un membro'}`))),
        row('share', 'Invita qualcuno', 'Condividi spazi, prodotti e liste con familiari e coinquilini', openInvite),
        ...hh.members.map(m => h('div.list-row', { style: { cursor: 'default' } }, h('span.member-avatar', initials(m.name)), h('span.grow', m.name + (m.id === S.user.id ? ' (tu)' : ''), h('small', m.role === 'owner' ? 'Proprietario' : 'Membro dal ' + UI.fmtDateTime(m.joinedAt).split(' ')[0])),
          isOwner && m.id !== S.user.id ? moreBtn([
            { icon: 'swap', label: 'Rendi proprietario', onclick: async () => { if (await confirm('Cedere la casa?', `${m.name} diventerà il proprietario di «${hh.name}» e tu resterai un membro.`, 'Cedi')) act('household.transfer', { userId: m.id }); } },
            { icon: 'delete', label: 'Rimuovi dalla casa', danger: true, onclick: async () => { if (await confirm('Rimuovere il membro?', `${m.name} non potrà più vedere né modificare i dati di «${hh.name}».`, 'Rimuovi', true)) act('household.member.remove', { userId: m.id }); } },
          ]) : null)),
        isOwner ? row('edit', 'Rinomina la casa', null, () => promptSheet('Rinomina la casa', 'Nome', hh.name, 'Salva', name => act('household.rename', { name }))) : null,
      ),
      h('div.list-group',
        S.households.length > 1 ? row('swap', 'Cambia casa', `Fai parte di ${S.households.length} case`, openHouseholdSwitcher) : null,
        row('group', 'Unisciti a una casa', 'Inserisci il codice invito che ti hanno mandato', () => promptSheet('Unisciti a una casa', 'Codice invito', '', 'Unisciti', async code => { await act('household.join', { code }); toast('Benvenuto in «' + S.household.name + '»!'); })),
        row('add', 'Crea una nuova casa', 'Ad esempio per la casa al mare o l’ufficio', () => promptSheet('Nuova casa', 'Nome', '', 'Crea', async name => { await act('household.create', { name }); toast('Casa creata'); })),
        hh.members.length > 1 || S.households.length > 1 ? row('logout', 'Esci da questa casa', isOwner ? 'La proprietà passerà a un altro membro' : null, async () => {
          if (await confirm('Uscire dalla casa?', `Non vedrai più spazi, prodotti e liste di «${hh.name}».` + (hh.members.length === 1 ? ' Sei l’unico membro: la casa e tutti i suoi dati verranno eliminati.' : ''), 'Esci', true)) { await act('household.leave', {}); toast('Sei uscito dalla casa'); }
        }, 'danger') : null,
        isOwner ? row('delete', 'Elimina questa casa', 'Elimina spazi, prodotti, liste e piano per tutti i membri', async () => {
          if (await confirm('Eliminare la casa?', `«${hh.name}» e tutti i suoi dati verranno eliminati definitivamente per tutti i ${hh.members.length} membri.`, 'Elimina', true)) { await act('household.delete', {}); toast('Casa eliminata'); }
        }, 'danger') : null,
      ),

      h('h3.sub', 'Preferenze'),
      h('div.list-group',
        row('eye', 'Tema', { auto: 'Automatico (come il sistema)', light: 'Chiaro', dark: 'Scuro' }[theme], () => actionSheet([
          { icon: 'check', label: 'Automatico (come il sistema)', onclick: () => { store.set('theme', 'auto'); UI.applyTheme(); render(true); } },
          { icon: 'check', label: 'Chiaro', onclick: () => { store.set('theme', 'light'); UI.applyTheme(); render(true); } },
          { icon: 'check', label: 'Scuro', onclick: () => { store.set('theme', 'dark'); UI.applyTheme(); render(true); } },
        ])),
        row('warning', 'Avviso «in scadenza»', `${noticeDays()} giorn${noticeDays() === 1 ? 'o' : 'i'} prima della scadenza`, () => actionSheet([1, 2, 3, 5, 7].map(n => ({ icon: n === noticeDays() ? 'check' : 'timer', label: `${n} giorn${n === 1 ? 'o' : 'i'} prima`, onclick: () => { store.set('noticeDays', n); render(true); } })))),
        'Notification' in window ? row('bell', 'Notifiche sui prodotti in scadenza', notif ? 'Attive su questo dispositivo' : 'Disattivate', async () => {
          if (notif) { store.set('notify', false); render(true); return; }
          const p = await Notification.requestPermission();
          store.set('notify', p === 'granted');
          if (p !== 'granted') toast('Permesso negato: puoi attivarle dalle impostazioni del browser.', 'error');
          else { store.set('notifiedOn', ''); notifyExpiry(); }
          render(true);
        }) : null,
        row('info', 'Mostra di nuovo i suggerimenti', null, () => { store.set('tip.cart', false); store.set('tip.spaces', false); store.set('hide.expired', ''); store.set('hide.expiring', ''); toast('Suggerimenti e avvisi ripristinati'); }),
      ),

      h('h3.sub', 'Account'),
      h('div.list-group',
        row('edit', 'Modifica il nome', null, () => promptSheet('Modifica il nome', 'Nome e cognome', S.user.name, 'Salva', async name => { const d = await Api.post('auth.profile', { name }); S.user = d.user; render(true); })),
        row('lock', 'Cambia password', null, openChangePassword),
        S.user.isAdmin ? h('a.list-row', { href: 'admin.html' }, icon('shield'), h('span.grow', 'Portale di amministrazione', h('small', 'Ricette, prodotti, categorie e utenti')), icon('open')) : null,
        h('a.list-row', { href: 'privacy.html' }, icon('info'), h('span.grow', 'Privacy e informazioni', h('small', 'FoodTrack ' + VERSION))),
        row('logout', 'Esci', null, logout),
        row('delete', 'Elimina il mio account', 'Elimina definitivamente il tuo account', deleteAccount, 'danger'),
      ),
    ];
    return { tab: null, title: 'Profilo', back: 'spazi', body };
  }

  function openChangePassword(forced) {
    const cur = input({ type: 'password', autocomplete: 'current-password' });
    const p1 = input({ type: 'password', autocomplete: 'new-password', minlength: 8 });
    const p2 = input({ type: 'password', autocomplete: 'new-password', minlength: 8 });
    const ok = btn('Cambia password', { icon: 'lock', onclick: async () => {
      if (p1.value.length < 8) { toast('La nuova password deve avere almeno 8 caratteri.', 'error'); return; }
      if (p1.value !== p2.value) { toast('Le due password non coincidono.', 'error'); return; }
      const d = await busy(ok, () => Api.post('auth.password', { current: cur.value, password: p1.value }));
      S.user = d.user; s.close(); toast('Password aggiornata'); start();
    } });
    const s = sheet({
      title: 'Cambia password', persistent: forced === true,
      body: [forced === true ? h('p.sheet-text', 'Un amministratore ha reimpostato la tua password: scegline una nuova per continuare.') : null,
        field(forced === true ? 'Password temporanea' : 'Password attuale', cur), field('Nuova password (min. 8 caratteri)', p1), field('Ripeti la nuova password', p2)],
      actions: [forced === true ? btn('Esci', { kind: 'text', onclick: () => { s.close(); logout(); } }) : btn('Annulla', { kind: 'text', onclick: () => s.close() }), ok],
    });
  }

  async function deleteAccount() {
    const pw = input({ type: 'password', autocomplete: 'current-password' });
    const ok = btn('Elimina definitivamente', { kind: 'danger', icon: 'delete', onclick: async () => {
      await busy(ok, () => Api.post('auth.delete', { password: pw.value }));
      s.close(); S.user = null; S.loaded = false; toast('Account eliminato'); render();
    } });
    const s = sheet({
      title: 'Eliminare l’account?',
      body: [h('p.sheet-text', 'Il tuo account verrà eliminato definitivamente. Le case in cui sei l’unico membro verranno eliminate con tutti i loro dati; in quelle condivise la proprietà passerà a un altro membro.'), field('Password', pw)],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), ok],
    });
  }

  async function logout() {
    try { await Api.post('auth.logout', {}); } catch (e) { /* already signed out */ }
    S.user = null; S.loaded = false;
    location.hash = '#/spazi';
    render();
  }

  function viewJoin(code) {
    code = String(code || '').toUpperCase();
    setTimeout(async () => {
      if (!code) return go('spazi');
      if (S.households.some(x => x.id === S.household.id) && S.household.inviteCode === code) { toast('Fai già parte di questa casa'); return go('spazi'); }
      if (await confirm('Unirti alla casa?', `Stai per unirti alla casa con codice ${code}: vedrai e potrai modificare i suoi spazi, prodotti e liste della spesa.`, 'Unisciti')) {
        try { const d = await Api.post('household.join', { code }); applyState(d); toast('Benvenuto in «' + S.household.name + '»!'); }
        catch (e) { toast(e.message, 'error'); }
      }
      go('spazi');
    }, 50);
    return { tab: 'spazi', title: 'Invito', back: 'spazi', body: [h('div.loader')] };
  }

  // ===================================================== AUTH

  function renderAuth(mode) {
    const pending = (location.hash.match(/^#\/unisciti\/([A-Za-z0-9]+)/) || [])[1] || '';
    mode = mode || (pending ? 'register' : store.get('authMode', 'login'));
    const err = h('div.err-box', { hidden: true });
    const showErr = m => { err.textContent = m; err.hidden = false; };
    const email = input({ type: 'email', autocomplete: 'email', inputmode: 'email', maxlength: 190 });
    const pw = input({ type: 'password', autocomplete: mode === 'login' ? 'current-password' : 'new-password' });
    const name = input({ autocomplete: 'name', maxlength: 80 });
    const code = input({ value: pending, maxlength: 12, autocapitalize: 'characters' });
    const privacy = h('input', { type: 'checkbox' });

    const submit = btn(mode === 'login' ? 'Accedi' : 'Crea account', { type: 'submit', cls: 'btn-block' });
    const form = h('form', { novalidate: true, onsubmit: async e => {
      e.preventDefault(); err.hidden = true;
      try {
        const d = await busy(submit, () => mode === 'login'
          ? Api.post('auth.login', { email: email.value, password: pw.value }, { silentAuth: true })
          : Api.post('auth.register', { name: name.value, email: email.value, password: pw.value, inviteCode: code.value, acceptPrivacy: privacy.checked }, { silentAuth: true }));
        S.user = d.user;
        if (pending && mode === 'login') location.hash = '#/unisciti/' + pending; else if (pending) location.hash = '#/spazi';
        start();
      } catch (ex) { showErr(ex.message); }
    } },
      mode === 'register' ? field('Nome', name) : null,
      field('Email', email),
      field(mode === 'login' ? 'Password' : 'Password (min. 8 caratteri)', pw),
      mode === 'register' ? field('Codice invito (facoltativo)', code, { hint: 'Se qualcuno ti ha invitato nella sua casa, inserisci il suo codice' }) : null,
      mode === 'register' ? h('label.check', privacy, h('span', 'Ho letto e accetto l’', h('a', { href: 'privacy.html', target: '_blank', rel: 'noopener' }, 'informativa sulla privacy'))) : null,
      err, h('div.spacer'), submit,
      mode === 'login' ? h('div.center', { style: { marginTop: '10px' } }, btn('Password dimenticata?', { kind: 'text', onclick: () => sheet({ title: 'Password dimenticata', body: [h('p.sheet-text', 'Per motivi di sicurezza la password può essere reimpostata da un amministratore di FoodTrack: scrivi a ', h('a', { href: 'mailto:info@saveriomorelli.com' }, 'info@saveriomorelli.com'), ' dall’indirizzo email del tuo account. Riceverai una password temporanea da cambiare al primo accesso.')] }) })) : null,
    );

    const tabs = h('div.tabs',
      h('button' + (mode === 'login' ? '.sel' : ''), { type: 'button', onclick: () => { store.set('authMode', 'login'); renderAuth('login'); } }, 'Accedi'),
      h('button' + (mode === 'register' ? '.sel' : ''), { type: 'button', onclick: () => { store.set('authMode', 'register'); renderAuth('register'); } }, 'Crea account'));

    root.replaceChildren(h('div.auth',
      h('div.auth-top', h('img', { src: 'assets/icons/icon-mono.png', alt: '' }), h('h1', 'FoodTrack'), h('p', 'Il cibo di casa, sotto controllo')),
      h('div.auth-body', pending ? h('div.banner.info', h('h3', icon('group'), 'Sei stato invitato!'), h('p', 'Crea un account (o accedi) per unirti alla casa condivisa.')) : null, tabs, form,
        h('ul.features',
          h('li', icon('spaces'), 'Tieni traccia di ciò che hai in frigo, freezer e dispensa'),
          h('li', icon('warning'), 'Ricevi un avviso prima che i prodotti scadano'),
          h('li', icon('list'), 'Liste della spesa condivise con chi vive con te'),
          h('li', icon('restaurant'), 'Ricette con gli ingredienti che hai già')))));
    document.title = 'FoodTrack';
  }

  function renderForcePassword() {
    root.replaceChildren(h('div.auth', h('div.auth-top', h('img', { src: 'assets/icons/icon-mono.png', alt: '' }), h('h1', 'FoodTrack'), h('p', 'Scegli una nuova password'))));
    if (!UI.sheetsOpen()) openChangePassword(true);
  }

  // ===================================================== notifications & sync

  function notifyExpiry() {
    if (!store.get('notify', false) || !('Notification' in window) || Notification.permission !== 'granted') return;
    const today = UI.todayStr();
    if (store.get('notifiedOn', '') === today) return;
    const a = expiredItems().length, b = expiringItems().length;
    if (!a && !b) return;
    store.set('notifiedOn', today);
    const parts = [];
    if (b) parts.push(`${b} prodott${b === 1 ? 'o' : 'i'} in scadenza`);
    if (a) parts.push(`${a} prodott${a === 1 ? 'o scaduto' : 'i scaduti'}`);
    const opts = { body: 'Hai ' + parts.join(' e ') + '. Scopri come usarli prima di buttarli!', icon: 'assets/icons/icon-192.png', badge: 'assets/icons/icon-mono.png', tag: 'ft-expiry' };
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

  async function start() {
    try {
      const me = await Api.get('auth.me', { silentAuth: true });
      S.user = me.user;
    } catch (e) {
      if (e.code === 'not_installed') { root.replaceChildren(h('div.auth', h('div.auth-top', h('h1', 'FoodTrack'), h('p', 'Installazione non completata')), h('div.auth-body', h('p', 'Apri ', h('a', { href: 'install-db.php' }, 'install-db.php'), ' per configurare il database.')))); return; }
      root.replaceChildren(h('div.auth', h('div.auth-top', h('h1', 'FoodTrack'), h('p', e.message)), h('div.auth-body.center', btn('Riprova', { icon: 'swap', onclick: start }))));
      return;
    }
    if (!S.user) { S.loaded = false; render(); return; }
    if (S.user.mustChangePassword) { render(); return; }
    render();
    try {
      const d = await Api.get('bootstrap');
      applyState(d);
      S.loaded = true;
      render();
      notifyExpiry();
    } catch (e) {
      toast(e.message, 'error');
      root.replaceChildren(h('div.auth', h('div.auth-top', h('h1', 'FoodTrack'), h('p', e.message)), h('div.auth-body.center', btn('Riprova', { icon: 'swap', onclick: start }))));
    }
  }

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
