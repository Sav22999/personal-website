/* FoodTrack — admin portal (administrators and master admin only). */
(function () {
  'use strict';

  const { h, icon, pic, btn, iconBtn, busy, field, input, textarea, select, sheet, actionSheet, confirm, toast, norm } = UI;
  const root = document.getElementById('admin');
  let me = null;

  const TABS = [
    ['panoramica', 'Panoramica', 'dashboard'],
    ['ricette', 'Ricette', 'restaurant'],
    ['prodotti', 'Prodotti', 'spaces'],
    ['categorie', 'Categorie', 'category'],
    ['utenti', 'Utenti', 'person'],
    ['case', 'Case', 'home'],
  ];
  const UNITS = ['pz', 'g', 'kg', 'ml', 'cl', 'l', 'conf', 'cucchiai', 'cucchiaini', 'q.b.'];

  const tabName = () => (location.hash.replace(/^#\/?/, '') || 'panoramica').split('/')[0];

  function shell(content) {
    const t = tabName();
    root.replaceChildren(
      h('header.adm-top', h('div.adm-top-inner',
        h('div.adm-head', h('img', { src: 'assets/icons/icon-mono.png', alt: '' }), h('h1', 'FoodTrack · Portale admin'),
          h('div.who', me.name, h('br'), h('span', { style: { opacity: .85 } }, me.roleLabel), ' · ', h('a', { href: './' }, 'Apri l’app'))),
        h('nav.adm-tabs', TABS.map(([k, label, ic]) => h('a' + (k === t ? '.active' : ''), { href: '#/' + k }, icon(ic), label))))),
      h('main.adm-main', content));
  }

  async function render() {
    shell(h('div.loader'));
    const views = { panoramica: vOverview, ricette: vRecipes, prodotti: vProducts, categorie: vCategories, utenti: vUsers, case: vHouseholds };
    try {
      const node = await (views[tabName()] || vOverview)();
      shell(node);
    } catch (e) {
      shell(h('div.err-box', e.message));
    }
  }

  const tbl = (head, rows) => h('div.tbl-wrap', h('table.tbl', h('thead', h('tr', head.map(x => h('th', { class: x[1] || '' }, x[0])))), rows instanceof Node ? rows : h('tbody', rows)));

  // ---------------------------------------------------------------- overview

  async function vOverview() {
    const d = await Api.get('admin.overview');
    const s = d.stats;
    const stat = (n, l, sub) => h('div.stat', h('div.n', String(n)), h('div.l', l), sub ? h('div.s', sub) : null);
    return h('div',
      h('div.adm-toolbar', h('h2', 'Panoramica')),
      h('div.stats',
        stat(s.users, 'Utenti registrati', `+${s.usersNew7} negli ultimi 7 giorni`),
        stat(s.usersActive30, 'Utenti attivi (30 giorni)'),
        stat(s.admins, 'Amministratori', 'incluso il master admin'),
        stat(s.households, 'Case', `${s.sharedHouseholds} condivise tra più utenti`),
        stat(s.items, 'Prodotti negli spazi', `${s.expired} scaduti`),
        stat(s.lists, 'Liste della spesa'),
        stat(s.recipes, 'Ricette', `${s.recipesPublished} pubblicate`),
        stat(s.products, 'Prodotti nel catalogo'),
        stat(s.favorites, 'Ricette salvate tra le preferite')),
      h('div.two-col',
        h('div.panel', h('h3', 'Ricette più amate'), d.topRecipes.length ? h('ol', d.topRecipes.map(r => h('li', `${r.name} — ${r.n} ❤`))) : h('p.muted.small', 'Ancora nessuna preferita.')),
        h('div.panel', h('h3', 'Prodotti più presenti negli spazi'), d.topProducts.length ? h('ol', d.topProducts.map(r => h('li', `${r.name} (${r.n})`))) : h('p.muted.small', 'Ancora nessun prodotto.'))),
      h('p.small.muted', { style: { marginTop: '16px' } }, 'I dati sono aggregati: il portale non mostra il contenuto degli spazi e delle liste dei singoli utenti.'));
  }

  // ---------------------------------------------------------------- recipes

  let recipeQuery = '';
  async function vRecipes() {
    const d = await Api.get('admin.recipes');
    const q = input({ type: 'search', value: recipeQuery, placeholder: 'Cerca ricetta' });
    const body = h('tbody');
    function paint() {
      UI.clear(body);
      const n = norm(recipeQuery);
      d.recipes.filter(r => !n || norm(r.name).includes(n)).forEach(r => body.appendChild(h('tr.click', { onclick: () => openRecipe(r, d) },
        h('td', h('div.thumb', { style: { backgroundImage: r.image ? `url("${r.image}")` : 'none' } })),
        h('td', h('strong', r.name), h('div.small.muted', r.description)),
        h('td.hide-sm', `${r.ingredients.length} ingr. · ${r.steps.length} passaggi`),
        h('td.hide-sm', ((r.prepMinutes || 0) + (r.cookMinutes || 0)) + ' min'),
        h('td.num', String(d.favorites[r.id] || 0) + ' ❤'),
        h('td', r.published ? h('span.tag.green', 'Pubblicata') : h('span.tag', 'Bozza')))));
      if (!body.children.length) body.appendChild(h('tr', h('td', { colspan: 6 }, h('p.muted.center', 'Nessuna ricetta'))));
    }
    q.addEventListener('input', () => { recipeQuery = q.value; paint(); });
    paint();
    return h('div',
      h('div.adm-toolbar', h('h2', `Ricette (${d.recipes.length})`), h('div.search-box', icon('search'), q), btn('Nuova ricetta', { icon: 'add', onclick: () => openRecipe(null, d) })),
      h('div.tbl-wrap', h('table.tbl', h('thead', h('tr', h('th', ''), h('th', 'Ricetta'), h('th.hide-sm', 'Contenuto'), h('th.hide-sm', 'Tempo'), h('th', 'Preferite'), h('th', 'Stato'))), body)));
  }

  function openRecipe(r, d) {
    r = r ? JSON.parse(JSON.stringify(r)) : { id: 0, name: '', description: '', image: '', prepMinutes: 15, cookMinutes: null, cookNotes: '', servings: 2, difficulty: 'facile', published: true, ingredients: [], steps: [] };
    const name = input({ value: r.name, maxlength: 120 });
    const desc = textarea({ value: r.description, maxlength: 500, rows: 2 });
    const prep = input({ type: 'number', min: 0, value: r.prepMinutes ?? '' });
    const cook = input({ type: 'number', min: 0, value: r.cookMinutes ?? '' });
    const cookNotes = input({ value: r.cookNotes, maxlength: 255 });
    const servings = input({ type: 'number', min: 1, max: 50, value: r.servings });
    const diff = select([['facile', 'Facile'], ['media', 'Media'], ['difficile', 'Difficile']].map(([v, l]) => ({ value: v, label: l })), r.difficulty);
    const pub = h('input', { type: 'checkbox', checked: r.published });
    let image = r.image;
    const preview = h('div.img-preview');
    const paintImg = () => { preview.style.backgroundImage = image ? `url("${image}")` : 'none'; preview.textContent = image ? '' : 'Nessuna immagine'; };
    paintImg();
    const file = h('input', { type: 'file', accept: 'image/jpeg,image/png,image/webp', hidden: true });
    const upBtn = btn('Carica immagine', { kind: 'tonal', icon: 'upload', onclick: () => file.click() });
    file.addEventListener('change', async () => {
      if (!file.files[0]) return;
      const fd = new FormData(); fd.append('image', file.files[0]);
      try { const res = await busy(upBtn, () => Api.upload('admin.recipe.image', fd)); image = res.image; paintImg(); toast('Immagine caricata: ricordati di salvare la ricetta'); }
      catch (e) { /* toast shown */ }
      file.value = '';
    });

    const prodNames = h('datalist#ft-prod-list', d.products.map(p => h('option', { value: p.name })));
    const ingWrap = h('div');
    const ingRows = [];
    function addIng(ing) {
      ing = ing || { name: '', quantity: null, unit: 'g', notes: '' };
      const nm = input({ value: ing.name, list: 'ft-prod-list', maxlength: 80 });
      const qt = input({ type: 'number', min: 0, step: 'any', value: ing.quantity ?? '' });
      const un = select(UNITS.concat(UNITS.includes(ing.unit) ? [] : [ing.unit]).map(u => ({ value: u, label: u })), ing.unit);
      const nt = input({ value: ing.notes || '', maxlength: 255 });
      const row = { nm, qt, un, nt };
      const el = h('div.ed-row', field('Ingrediente', nm), field('Q.tà', qt), field('Unità', un), field('Note', nt),
        h('div.acts', iconBtn('delete', 'Rimuovi', () => { ingRows.splice(ingRows.indexOf(row), 1); el.remove(); }, 'sm')));
      nm.addEventListener('change', () => { const p = d.products.find(x => norm(x.name) === norm(nm.value)); if (p && !qt.value) un.value = p.unit; });
      row.el = el; ingRows.push(row); ingWrap.appendChild(el);
    }
    r.ingredients.forEach(addIng);

    const stepWrap = h('div');
    const stepRows = [];
    function renumber() { stepRows.forEach((s, i) => { s.n.textContent = (i + 1) + '.'; }); }
    function addStep(text) {
      const ta = textarea({ value: text || '', rows: 2, maxlength: 1000 });
      const n = h('span.n');
      const row = { ta, n };
      const el = h('div.step-row', n, field('Passaggio', ta),
        h('div', { style: { paddingTop: '8px' } },
          iconBtn('left', 'Sposta su', () => { const i = stepRows.indexOf(row); if (i > 0) { stepRows.splice(i, 1); stepRows.splice(i - 1, 0, row); stepWrap.insertBefore(el, el.previousSibling); renumber(); } }, 'sm'),
          iconBtn('delete', 'Rimuovi', () => { stepRows.splice(stepRows.indexOf(row), 1); el.remove(); renumber(); }, 'sm')));
      row.el = el; stepRows.push(row); stepWrap.appendChild(el); renumber();
    }
    r.steps.forEach(addStep);
    if (!r.id) { addIng(); addStep(''); }

    const save = btn('Salva ricetta', { icon: 'save', onclick: async () => {
      if (!name.value.trim()) { toast('Inserisci il nome della ricetta.', 'error'); return; }
      const ingredients = ingRows.filter(x => x.nm.value.trim()).map(x => {
        const p = d.products.find(pp => norm(pp.name) === norm(x.nm.value));
        return { productId: p ? p.id : null, name: x.nm.value.trim(), quantity: x.qt.value, unit: x.un.value, notes: x.nt.value };
      });
      const unlinked = ingredients.filter(i => !i.productId).map(i => i.name);
      await busy(save, () => Api.post('admin.recipe.save', {
        id: r.id, name: name.value, description: desc.value, image, prepMinutes: prep.value, cookMinutes: cook.value, cookNotes: cookNotes.value,
        servings: servings.value, difficulty: diff.value, published: pub.checked, ingredients, steps: stepRows.map(s => s.ta.value),
      }));
      s.close();
      toast(unlinked.length ? `Salvata. Non collegati al catalogo: ${unlinked.join(', ')} (la disponibilità verrà calcolata solo per nome)` : 'Ricetta salvata');
      setTimeout(render, 250);
    } });

    const s = sheet({
      title: r.id ? 'Modifica ricetta' : 'Nuova ricetta', persistent: true,
      body: [
        prodNames,
        field('Nome', name), field('Descrizione breve', desc),
        preview, h('div.row', { style: { gap: '8px' } }, upBtn, image ? btn('Rimuovi immagine', { kind: 'text', onclick: () => { image = ''; paintImg(); } }) : null, file),
        h('div.field-row', field('Preparazione (min)', prep), field('Cottura (min)', cook)),
        field('Note di cottura', cookNotes, { hint: 'Ad esempio: «Forno statico, 180 °C per 45 minuti»' }),
        h('div.field-row', field('Porzioni', servings), field('Difficoltà', diff)),
        h('label.check', pub, h('span', 'Pubblicata (visibile a tutti gli utenti nell’app)')),
        h('h3.sub', 'Ingredienti'),
        h('p.small.muted', 'Scegli il nome dal catalogo dei prodotti (suggerito mentre scrivi): così l’app può calcolare cosa l’utente ha già in casa.'),
        ingWrap, btn('Aggiungi ingrediente', { kind: 'text', icon: 'add', onclick: () => addIng() }),
        h('h3.sub', 'Procedimento'),
        stepWrap, btn('Aggiungi passaggio', { kind: 'text', icon: 'add', onclick: () => addStep('') }),
      ],
      actions: [
        r.id ? btn('Elimina', { kind: 'danger-text', icon: 'delete', onclick: async () => {
          if (!await confirm('Eliminare la ricetta?', `«${r.name}» verrà eliminata, anche dalle preferite e dai piani degli utenti.`, 'Elimina', true)) return;
          await Api.post('admin.recipe.delete', { id: r.id }); s.close(); toast('Ricetta eliminata'); setTimeout(render, 250);
        } }) : null,
        btn('Annulla', { kind: 'text', onclick: () => s.close() }), save,
      ].filter(Boolean),
    });
    s.panel.classList.add('wide');
  }

  // ---------------------------------------------------------------- products

  let prodQuery = '', prodCat = '';
  async function vProducts() {
    const d = await Api.get('admin.catalog');
    const catName = id => (d.categories.find(c => c.id === id) || {}).name || '—';
    const q = input({ type: 'search', value: prodQuery, placeholder: 'Cerca prodotto' });
    const cat = select([{ value: '', label: 'Tutte le categorie' }].concat(d.categories.map(c => ({ value: c.id, label: c.name }))), prodCat);
    const body = h('tbody');
    function paint() {
      UI.clear(body);
      const n = norm(prodQuery);
      d.products.filter(p => (!n || norm(p.name).includes(n)) && (!prodCat || String(p.categoryId) === String(prodCat))).forEach(p => body.appendChild(h('tr.click', { onclick: () => openProduct(p, d) },
        h('td', pic('products', p.icon, 'sm')), h('td', h('strong', p.name)), h('td', catName(p.categoryId)), h('td.hide-sm', p.unit),
        h('td.hide-sm', p.expiryTip ? h('span.tag.green', 'Sì') : h('span.tag', 'Categoria')),
        h('td.num.hide-sm', String(d.usage[p.id] || 0)),
        h('td', p.active ? h('span.tag.green', 'Attivo') : h('span.tag', 'Nascosto')))));
    }
    q.addEventListener('input', () => { prodQuery = q.value; paint(); });
    cat.addEventListener('change', () => { prodCat = cat.value; paint(); });
    paint();
    return h('div',
      h('div.adm-toolbar', h('h2', `Catalogo prodotti (${d.products.length})`), h('div.search-box', icon('search'), q), field('Categoria', cat), btn('Nuovo prodotto', { icon: 'add', onclick: () => openProduct(null, d) })),
      h('div.notice', 'Il catalogo serve a suggerire i prodotti quando gli utenti li aggiungono (con icona, categoria e unità), a collegare gli ingredienti delle ricette e a mostrare le informazioni sulla scadenza.'),
      tbl([['', ''], ['Nome'], ['Categoria'], ['Unità', 'hide-sm'], ['Info scadenza', 'hide-sm'], ['Nelle ricette', 'hide-sm num'], ['Stato']], body));
  }

  function openProduct(p, d) {
    p = p || { id: 0, name: '', icon: 'generic', categoryId: null, unit: 'pz', expiryTip: '', active: true };
    let ic = p.icon;
    const name = input({ value: p.name, maxlength: 80 });
    const cat = select([{ value: '', label: '—' }].concat(d.categories.map(c => ({ value: c.id, label: c.name }))), p.categoryId ?? '');
    const unit = select(d.units.map(u => ({ value: u, label: u })), p.unit);
    const tip = textarea({ value: p.expiryTip || '', rows: 4, maxlength: 2000 });
    const active = h('input', { type: 'checkbox', checked: p.active });
    const save = btn('Salva', { icon: 'save', onclick: async () => {
      await busy(save, () => Api.post('admin.product.save', { id: p.id, name: name.value, icon: ic, categoryId: cat.value || null, unit: unit.value, expiryTip: tip.value, active: active.checked }));
      s.close(); toast('Prodotto salvato'); setTimeout(render, 250);
    } });
    const s = sheet({
      title: p.id ? 'Modifica prodotto' : 'Nuovo prodotto',
      body: [field('Nome', name), h('div.field-row', field('Categoria', cat), field('Unità predefinita', unit)),
        h('p.small.muted', { style: { margin: '6px 2px' } }, 'Icona'),
        (() => { const w = h('div.icon-picker.products'); d.icons.forEach(x => { const b = h('button' + (x === ic ? '.sel' : ''), { type: 'button', title: x, onclick: () => { ic = x; w.querySelectorAll('button').forEach(y => y.classList.toggle('sel', y === b)); } }, pic('products', x)); w.appendChild(b); }); return w; })(),
        field('Informazioni sulla scadenza', tip, { hint: 'Consigli mostrati quando il prodotto è in scadenza o scaduto. Se vuoto, si usano quelli della categoria.' }),
        h('label.check', active, h('span', 'Attivo (suggerito agli utenti)'))],
      actions: [
        p.id ? btn('Elimina', { kind: 'danger-text', icon: 'delete', onclick: async () => {
          if (!await confirm('Eliminare il prodotto?', 'I prodotti già presenti negli spazi degli utenti restano, ma non saranno più collegati al catalogo. Per nasconderlo soltanto, togli la spunta «Attivo».', 'Elimina', true)) return;
          await Api.post('admin.product.delete', { id: p.id }); s.close(); toast('Prodotto eliminato'); setTimeout(render, 250);
        } }) : null,
        btn('Annulla', { kind: 'text', onclick: () => s.close() }), save].filter(Boolean),
    });
  }

  // ---------------------------------------------------------------- categories

  async function vCategories() {
    const d = await Api.get('admin.catalog');
    const count = id => d.products.filter(p => p.categoryId === id).length;
    return h('div',
      h('div.adm-toolbar', h('h2', `Categorie (${d.categories.length})`), btn('Nuova categoria', { icon: 'add', onclick: () => openCategory(null) })),
      tbl([['Ordine', 'num'], ['Nome'], ['Prodotti', 'num'], ['Informazioni sulla scadenza']],
        d.categories.map(c => h('tr.click', { onclick: () => openCategory(c) }, h('td.num', String(c.sort)), h('td', h('strong', c.name)), h('td.num', String(count(c.id))), h('td.small', (c.expiryTip || '—').slice(0, 140) + ((c.expiryTip || '').length > 140 ? '…' : ''))))));
  }

  function openCategory(c) {
    c = c || { id: 0, name: '', sort: 100, expiryTip: '' };
    const name = input({ value: c.name, maxlength: 60 });
    const sort = input({ type: 'number', value: c.sort });
    const tip = textarea({ value: c.expiryTip || '', rows: 5, maxlength: 2000 });
    const save = btn('Salva', { icon: 'save', onclick: async () => {
      await busy(save, () => Api.post('admin.category.save', { id: c.id, name: name.value, sort: sort.value, expiryTip: tip.value }));
      s.close(); toast('Categoria salvata'); setTimeout(render, 250);
    } });
    const s = sheet({
      title: c.id ? 'Modifica categoria' : 'Nuova categoria',
      body: [h('div.field-row', field('Nome', name), field('Ordine', sort)), field('Informazioni generiche sulla scadenza', tip)],
      actions: [
        c.id ? btn('Elimina', { kind: 'danger-text', icon: 'delete', onclick: async () => {
          if (!await confirm('Eliminare la categoria?', 'I prodotti di questa categoria resteranno senza categoria.', 'Elimina', true)) return;
          await Api.post('admin.category.delete', { id: c.id }); s.close(); setTimeout(render, 250);
        } }) : null,
        btn('Annulla', { kind: 'text', onclick: () => s.close() }), save].filter(Boolean),
    });
  }

  // ---------------------------------------------------------------- users

  let userQuery = '', userRole = '';
  async function vUsers() {
    const d = await Api.get('admin.users');
    const q = input({ type: 'search', value: userQuery, placeholder: 'Cerca nome o email' });
    const role = select([{ value: '', label: 'Tutti' }, { value: '1', label: 'Utenti' }, { value: '9', label: 'Amministratori' }, { value: 'disabled', label: 'Disabilitati' }], userRole);
    const body = h('tbody');
    const roleTag = u => u.role >= 10 ? h('span.tag.gold', 'Master admin') : u.role >= 9 ? h('span.tag.green', 'Amministratore') : h('span.tag', 'Utente');
    function paint() {
      UI.clear(body);
      const n = norm(userQuery);
      d.users.filter(u => (!n || norm(u.name + ' ' + u.email).includes(n))
        && (!userRole || (userRole === 'disabled' ? u.status === 'disabled' : userRole === '9' ? u.role >= 9 : u.role < 9)))
        .forEach(u => body.appendChild(h('tr.click', { onclick: () => userActions(u) },
          h('td', h('strong', u.name + (u.isMe ? ' (tu)' : '')), h('div.small.muted', u.email)),
          h('td', roleTag(u)),
          h('td', u.status === 'active' ? h('span.tag.green', 'Attivo') : h('span.tag.red', 'Disabilitato'), u.mustChangePassword ? h('div.small.muted', 'deve cambiare password') : null),
          h('td.hide-sm', UI.fmtDateTime(u.createdAt)),
          h('td.hide-sm', UI.fmtDateTime(u.lastSeenAt || u.lastLoginAt)),
          h('td.num.hide-sm', String(u.households)))));
      if (!body.children.length) body.appendChild(h('tr', h('td', { colspan: 6 }, h('p.muted.center', 'Nessun utente'))));
    }
    q.addEventListener('input', () => { userQuery = q.value; paint(); });
    role.addEventListener('change', () => { userRole = role.value; paint(); });
    paint();
    return h('div',
      h('div.adm-toolbar', h('h2', `Utenti (${d.users.length})`), h('div.search-box', icon('search'), q), field('Ruolo', role), btn('Nuovo utente', { icon: 'add', onclick: createUser })),
      h('div.notice', me.isMaster
        ? 'Sei il master admin: puoi gestire utenti e amministratori, e cedere il ruolo di master admin a un altro account (ce n’è sempre uno solo).'
        : 'Come amministratore puoi gestire gli utenti. Solo il master admin può nominare, modificare o rimuovere gli amministratori.'),
      tbl([['Utente'], ['Ruolo'], ['Stato'], ['Registrato', 'hide-sm'], ['Ultimo accesso', 'hide-sm'], ['Case', 'num hide-sm']], body));
  }

  function showPassword(title, pw, who) {
    sheet({
      title,
      body: [h('p.sheet-text', `Comunica a ${who} questa password temporanea: al primo accesso dovrà sceglierne una nuova. Non verrà mostrata di nuovo.`),
        h('div.code-box', { style: { fontSize: '1.2rem', letterSpacing: '.08em' } }, pw),
        h('div.center-btn', btn('Copia', { kind: 'tonal', icon: 'copy', onclick: () => navigator.clipboard.writeText(pw).then(() => toast('Copiata')).catch(() => {}) }))],
    });
  }

  function userActions(u) {
    if (!u.canManage && !(me.isMaster && !u.isMe && u.role < 10)) {
      toast(u.isMe ? 'Il tuo account si gestisce dal Profilo nell’app.' : u.role >= 10 ? 'Il master admin non può essere modificato da qui.' : 'Solo il master admin può gestire gli amministratori.');
      return;
    }
    actionSheet([
      u.canManage ? { icon: 'edit', label: 'Modifica', onclick: () => editUser(u) } : null,
      u.canManage ? { icon: 'lock', label: 'Reimposta la password', onclick: async () => {
        if (!await confirm('Reimpostare la password?', `${u.name} verrà disconnesso da tutti i dispositivi e dovrà accedere con una password temporanea.`, 'Reimposta')) return;
        try { const d = await Api.post('admin.user.reset_password', { id: u.id }); showPassword('Password temporanea', d.password, u.name); } catch (e) { toast(e.message, 'error'); }
      } } : null,
      me.isMaster && u.status === 'active' && !u.isMe ? { icon: 'shield', label: 'Rendi master admin', onclick: () => transferMaster(u) } : null,
      u.canManage ? { icon: 'delete', label: 'Elimina account', danger: true, onclick: async () => {
        if (!await confirm('Eliminare l’account?', `L’account di ${u.name} (${u.email}) verrà eliminato definitivamente, insieme alle case in cui è l’unico membro.`, 'Elimina', true)) return;
        try { await Api.post('admin.user.delete', { id: u.id }); toast('Account eliminato'); render(); } catch (e) { toast(e.message, 'error'); }
      } } : null,
    ]);
  }

  function editUser(u) {
    const name = input({ value: u.name, maxlength: 80 });
    const email = input({ type: 'email', value: u.email, maxlength: 190 });
    const status = select([{ value: 'active', label: 'Attivo' }, { value: 'disabled', label: 'Disabilitato (non può accedere)' }], u.status);
    const role = select([{ value: 1, label: 'Utente' }, { value: 9, label: 'Amministratore' }], u.role);
    const save = btn('Salva', { icon: 'save', onclick: async () => {
      await busy(save, () => Api.post('admin.user.update', { id: u.id, name: name.value, email: email.value, status: status.value, role: Number(role.value) }));
      s.close(); toast('Utente aggiornato'); setTimeout(render, 250);
    } });
    const s = sheet({
      title: 'Modifica utente',
      body: [field('Nome', name), field('Email', email), field('Stato', status), me.isMaster ? field('Ruolo', role, { hint: 'Gli amministratori possono gestire ricette, catalogo e utenti.' }) : null],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), save],
    });
  }

  function createUser() {
    const name = input({ maxlength: 80 });
    const email = input({ type: 'email', maxlength: 190 });
    const role = select([{ value: 1, label: 'Utente' }, { value: 9, label: 'Amministratore' }], 1);
    const save = btn('Crea', { icon: 'add', onclick: async () => {
      const d = await busy(save, () => Api.post('admin.user.create', { name: name.value, email: email.value, role: Number(role.value) }));
      s.close(); setTimeout(() => { showPassword('Account creato', d.password, name.value); render(); }, 250);
    } });
    const s = sheet({
      title: 'Nuovo utente',
      body: [h('p.sheet-text', 'Gli utenti di solito si registrano da soli dall’app. Qui puoi creare un account con una password temporanea.'), field('Nome', name), field('Email', email), me.isMaster ? field('Ruolo', role) : null],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), save],
    });
  }

  function transferMaster(u) {
    const pw = input({ type: 'password', autocomplete: 'current-password' });
    const ok = btn('Cedi il ruolo', { kind: 'danger', icon: 'shield', onclick: async () => {
      await busy(ok, () => Api.post('admin.user.transfer_master', { id: u.id, password: pw.value }));
      s.close(); toast(`${u.name} è il nuovo master admin`);
      const m = await Api.get('auth.me'); me = m.user; setTimeout(render, 250);
    } });
    const s = sheet({
      title: 'Cedere il ruolo di master admin?',
      body: [h('p.sheet-text', `${u.name} diventerà l’unico master admin e tu diventerai un amministratore. Non potrai annullare l’operazione da solo.`), field('La tua password', pw)],
      actions: [btn('Annulla', { kind: 'text', onclick: () => s.close() }), ok],
    });
  }

  // ---------------------------------------------------------------- households

  async function vHouseholds() {
    const d = await Api.get('admin.households');
    return h('div',
      h('div.adm-toolbar', h('h2', `Case (${d.households.length})`)),
      h('div.notice', 'Le case sono gli spazi condivisi: ogni utente ne ha almeno una e può invitare altre persone. Qui vedi solo i numeri, non il contenuto.'),
      tbl([['Casa'], ['Proprietario'], ['Membri', 'num'], ['Spazi', 'num hide-sm'], ['Prodotti', 'num hide-sm'], ['Liste', 'num hide-sm'], ['Creata', 'hide-sm']],
        d.households.map(x => h('tr', h('td', h('strong', x.name)), h('td', x.owner || '—', h('div.small.muted', x.ownerEmail || '')), h('td.num', x.members > 1 ? h('span.tag.green', String(x.members)) : '1'),
          h('td.num.hide-sm', String(x.spaces)), h('td.num.hide-sm', String(x.items)), h('td.num.hide-sm', String(x.lists)), h('td.hide-sm', UI.fmtDateTime(x.createdAt))))));
  }

  // ---------------------------------------------------------------- start

  function loginForm(msg) {
    const email = input({ type: 'email', autocomplete: 'email' });
    const pw = input({ type: 'password', autocomplete: 'current-password' });
    const err = h('div.err-box', { hidden: !msg }, msg || '');
    const sub = btn('Accedi', { type: 'submit', cls: 'btn-block' });
    root.replaceChildren(h('div.auth', h('div.auth-top', h('img', { src: 'assets/icons/icon-mono.png', alt: '' }), h('h1', 'Portale admin'), h('p', 'FoodTrack')),
      h('div.auth-body', h('form', { onsubmit: async e => {
        e.preventDefault(); err.hidden = true;
        try { await busy(sub, () => Api.post('auth.login', { email: email.value, password: pw.value }, { silentAuth: true })); start(); }
        catch (ex) { err.textContent = ex.message; err.hidden = false; }
      } }, field('Email', email), field('Password', pw), err, h('div.spacer'), sub), h('p.center', h('a', { href: './' }, 'Torna all’app')))));
  }

  async function start() {
    UI.applyTheme();
    try {
      const d = await Api.get('auth.me', { silentAuth: true });
      me = d.user;
    } catch (e) { root.replaceChildren(h('div.adm-main', h('div.err-box', e.message))); return; }
    if (!me) return loginForm();
    if (me.mustChangePassword) return loginForm('Devi prima cambiare la password temporanea dall’app.');
    if (!me.isAdmin) {
      root.replaceChildren(h('div.auth', h('div.auth-top', h('h1', 'Accesso negato')), h('div.auth-body', h('p', 'Il portale è riservato agli amministratori di FoodTrack.'), h('p', h('a', { href: './' }, 'Torna all’app')))));
      return;
    }
    render();
  }

  Api.onUnauthenticated(() => loginForm('Sessione scaduta: accedi di nuovo.'));
  window.addEventListener('hashchange', () => { if (!UI.sheetsOpen()) render(); });
  start();
})();
