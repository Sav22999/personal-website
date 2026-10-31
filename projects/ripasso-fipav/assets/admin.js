/* Management panel: overview, users, questions, references, reports, documents. */
(function () {
  'use strict';

  const $ = id => document.getElementById(id);
  const esc = window.escapeHtml;

  const SOURCE_CLASS = { 'Regole di gioco': 'rg', 'Casistica': 'cas', 'Indicazioni tecniche': 'it', 'Normative federali': 'nf', 'Vademecum': 'vad' };
  const SOURCE_SHORT = { 'Regole di gioco': 'RG', 'Casistica': 'CAS', 'Indicazioni tecniche': 'IT', 'Normative federali': 'NF', 'Vademecum': 'VAD' };
  const TYPE_LABEL = { 'multiple-choice': 'Scelta multipla', 'yes-no': 'Vero / Falso', 'multi-select': 'Più risposte' };
  const DIFF_LABEL = { e: 'Facile', m: 'Media', d: 'Difficile', dd: 'Molto difficile' };
  const STATUS_LABEL = { pending: 'In attesa', active: 'Attivo', disabled: 'Disabilitato' };
  const LEVELS = [[1, 'Utente'], [5, 'Moderatore contenuti'], [9, 'Amministratore'], [10, 'Master admin']];
  const PAGE = 60;

  const S = {
    me: null,
    sources: [],
    refs: [],
    refMap: {},
    questions: [],
    flags: [],
    users: [],
    qLimit: PAGE,
    rLimit: PAGE,
    rSources: new Set(),
    userFilter: 'all',
    openMenu: null,
    docs: [],
    docMax: 0,
    overview: null,
    overviewAt: 0,
    overviewLoading: false,
  };

  // ---------- Utilities ----------

  let toastTimer;
  function toast(msg) {
    const t = $('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), Math.max(2600, msg.length * 55));
  }

  let modalResolve = null;
  function confirmModal(title, text, confirmLabel) {
    return new Promise(resolve => {
      modalResolve = resolve;
      $('modal-title').textContent = title;
      $('modal-text').textContent = text;
      $('modal-confirm').textContent = confirmLabel || 'Conferma';
      $('modal').hidden = false;
      $('modal-cancel').focus();
    });
  }
  $('modal-cancel').addEventListener('click', () => { $('modal').hidden = true; if (modalResolve) modalResolve(false); modalResolve = null; });
  $('modal-confirm').addEventListener('click', () => { $('modal').hidden = true; if (modalResolve) modalResolve(true); modalResolve = null; });

  function fmtDate(utc) {
    if (!utc) return '—';
    const d = new Date(String(utc).replace(' ', 'T') + (String(utc).endsWith('Z') ? '' : 'Z'));
    if (isNaN(d)) return '—';
    return d.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' +
           d.toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
  }
  function fmtIso(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    return isNaN(d) ? '' : d.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit', year: 'numeric' });
  }
  function norm(s) { return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, ''); }
  function sourceOfRef(code) {
    const r = S.refMap[code];
    if (r) return r['ref-from'];
    const p = String(code).split('-')[0];
    return Object.keys(SOURCE_SHORT).find(k => SOURCE_SHORT[k] === p) || '';
  }
  function roleLabel(p) { let l = 'Nessun accesso'; LEVELS.forEach(([n, name]) => { if (p >= n) l = name; }); return l; }
  // FIPAV season: starts on 1 July (July 2026 - June 2027 = 2026/27)
  function seasonFromYear(y) { return y + '/' + String((y + 1) % 100).padStart(2, '0'); }
  const NOW = new Date();
  const CURRENT_START = NOW.getMonth() >= 6 ? NOW.getFullYear() : NOW.getFullYear() - 1;
  const CURRENT_SEASON = seasonFromYear(CURRENT_START);
  function changeBadge(c) {
    if (!c) return '';
    return '<span class="change-badge ' + (c.season === CURRENT_SEASON ? 'current' : 'past') + '">' + (c.type === 'new' ? 'Nuova' : 'Modificata') + ' ' + esc(c.season) + '</span>';
  }
  function roleClass(p) { return p >= 10 ? 'master' : p >= 9 ? 'admin' : p >= 5 ? 'editor' : ''; }
  function initials(name) { return String(name || '?').split(/\s+/).filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase(); }

  async function withBusy(btn, fn) {
    btn.disabled = true;
    try { return await fn(); } finally { btn.disabled = false; }
  }

  function closeOnBackdrop(overlay, close) {
    overlay.addEventListener('mousedown', e => { if (e.target === overlay) close(); });
    overlay.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
    overlay.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', close));
  }

  // ---------- Tabs and navigation ----------

  function showTab(tab) {
    const allowed = ['panoramica', 'domande', 'riferimenti', 'segnalazioni'].concat(S.me.canManageUsers ? ['utenti', 'documenti'] : []);
    if (!allowed.includes(tab)) tab = 'panoramica';
    closeUserMenu();
    document.querySelectorAll('.admin-tabs .tab-btn').forEach(b => {
      const on = b.dataset.tab === tab;
      b.classList.toggle('active', on);
      b.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    document.querySelectorAll('.admin-section').forEach(s => { s.hidden = s.id !== 'sec-' + tab; });
    window.scrollTo(0, 0);
    if (location.hash !== '#' + tab) history.replaceState(null, '', '#' + tab);
    if (tab === 'panoramica') renderDashboard();
    if (tab === 'utenti') renderUsers();
    if (tab === 'domande') renderQuestions();
    if (tab === 'riferimenti') renderRefs();
    if (tab === 'segnalazioni') renderFlags();
    if (tab === 'documenti') renderDocs();
  }
  document.querySelectorAll('.admin-tabs .tab-btn').forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));
  window.addEventListener('hashchange', () => showTab(location.hash.slice(1)));

  // ---------- Data loading ----------

  async function loadContent() {
    const d = await Api.get('admin.content');
    S.sources = d.sources;
    S.refs = d.refs;
    S.refMap = {};
    S.refs.forEach(r => { S.refMap[r['ref-code']] = r; });
    S.questions = d.questions;
  }
  async function loadFlags() {
    const d = await Api.get('admin.flags');
    S.flags = d.flags;
    const open = new Set(S.flags.filter(f => !f.resolved).map(f => f.code));
    const el = $('flags-count');
    el.textContent = open.size;
    el.hidden = open.size === 0;
  }
  async function loadUsers() {
    if (!S.me.canManageUsers) return;
    const d = await Api.get('admin.users');
    S.users = d.users;
    S.mailEnabled = !!d.mailEnabled;
    const pending = S.users.filter(u => u.status === 'pending').length;
    $('pending-count').textContent = pending;
    $('pending-count').hidden = pending === 0;
  }

  // ====================================================================
  // USERS
  // ====================================================================

  function closeUserMenu() {
    if (S.openMenu) { S.openMenu.parentElement.classList.remove('open'); S.openMenu.remove(); S.openMenu = null; }
  }
  document.addEventListener('click', e => {
    if (!S.openMenu) return;
    // Outside the menu, or on the dark backdrop (which belongs to .u-more) on mobile
    if (!e.target.closest('.u-more') || e.target.classList.contains('u-more')) closeUserMenu();
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeUserMenu(); });

  function renderUsers() {
    closeUserMenu();
    const q = norm($('users-search').value.trim());
    const role = parseInt($('users-role').value || '0', 10);
    const list = S.users.filter(u => (S.userFilter === 'all' || u.status === S.userFilter) &&
      (!role || (role === 9 ? u.permissions >= 9 : role === 5 ? u.permissions >= 5 && u.permissions < 9 : u.permissions >= 1 && u.permissions < 5)) &&
      (!q || norm(u.name + ' ' + u.email).includes(q)));
    renderMailBanner();
    $('users-count').textContent = list.length + (list.length === 1 ? ' utente' : ' utenti');
    const box = $('users-list');
    if (!list.length) { box.innerHTML = '<div class="admin-empty">Nessun utente corrisponde ai filtri.</div>'; return; }
    box.innerHTML = '';
    list.forEach(u => {
      const isMe = u.id === S.me.id;
      const card = document.createElement('div');
      card.className = 'a-card u-card clickable' + (u.status === 'pending' ? ' pending' : '') + (u.permissions >= 10 ? ' is-master' : '');
      card.tabIndex = 0;
      card.title = 'Apri la panoramica di ' + u.name;
      const statusPill = u.status === 'active' ? 'green' : u.status === 'pending' ? 'gold' : 'red';

      // Assignable levels: 9 only by the master admin, 10 only through the transfer
      const levelOptions = LEVELS.filter(([n]) => n < 9 || (n === 9 && S.me.isMaster));
      if (!levelOptions.some(([n]) => n === u.permissions) && u.permissions > 0) levelOptions.push([u.permissions, roleLabel(u.permissions) + ' (' + u.permissions + ')']);
      levelOptions.sort((a, b) => a[0] - b[0]);

      let actions = '';
      if (isMe) {
        actions = '<span class="u-locked">Il tuo account</span>';
      } else if (!u.canManage) {
        actions = '<span class="u-locked"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>' +
          (u.permissions >= 10 ? 'Master admin' : 'Gestito dal master admin') + '</span>';
      } else if (u.status === 'pending') {
        actions = '<button class="btn btn-primary btn-sm" data-act="approve">Abilita</button>' +
                  '<button class="btn btn-ghost btn-sm" data-act="delete">Rifiuta</button>';
      } else {
        actions =
          '<select class="admin-select" data-act="perm" aria-label="Ruolo di ' + esc(u.name) + '">' +
            levelOptions.map(([n, name]) => '<option value="' + n + '"' + (n === u.permissions ? ' selected' : '') + '>' + esc(name) + '</option>').join('') +
          '</select>' +
          (u.status === 'disabled' ? '<button class="btn btn-primary btn-sm" data-act="enable">Riabilita</button>' : '') +
          '<span class="u-more"><button class="icon-btn" data-act="menu" aria-label="Altre azioni" aria-haspopup="true">' +
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg></button></span>';
      }

      card.innerHTML =
        '<div class="u-avatar" aria-hidden="true"></div>' +
        '<div>' +
          '<div class="a-card-top"><span class="u-name">' + esc(u.name) + '</span>' + (isMe ? '<span class="pill blue">tu</span>' : '') +
            '<span class="pill ' + statusPill + '">' + STATUS_LABEL[u.status] + '</span>' +
            (u.status !== 'pending' ? '<span class="role-badge ' + roleClass(u.permissions) + '">' + esc(u.role) + '</span>' : '') +
            (u.must_change_password ? '<span class="pill gold">Deve cambiare password</span>' : '') +
            (needsActivationMail(u) ? '<span class="pill gold" title="Non ha ricevuto l’email con le indicazioni per accedere">Email di attivazione non inviata</span>' : '') +
          '</div>' +
          '<div class="u-email">' + esc(u.email) + '</div>' +
          '<div class="a-meta"><span>Registrato il ' + fmtDate(u.created_at) + '</span>' +
            '<span>Ultimo accesso: ' + fmtDate(u.last_seen_at || u.last_login_at) + '</span>' +
            '<span>' + u.answers + ' risposte date</span>' +
            (u.activation_mail_at ? '<span>Email di attivazione inviata il ' + fmtDate(u.activation_mail_at) + '</span>' : '') + '</div>' +
        '</div>' +
        '<div class="u-actions">' + actions + '</div>';

      Avatar.apply(card.querySelector('.u-avatar'), u.name, u.gravatar, { size: 40 });
      // Clicking the card (outside the action controls) opens the read-only overview
      // (a menu item removes itself from the page when clicked: a detached target is not a click on the card)
      card.addEventListener('click', e => { if (e.target.isConnected && !e.target.closest('.u-actions')) openUserStats(u); });
      card.addEventListener('keydown', e => { if (e.key === 'Enter' && e.target === card) openUserStats(u); });
      card.querySelectorAll('[data-act]').forEach(el => {
        const act = el.dataset.act;
        if (act === 'perm') el.addEventListener('change', () => {
          const v = parseInt(el.value, 10);
          const go = () => updateUser(u, { permissions: v }, 'Ruolo aggiornato: ' + roleLabel(v), () => { el.value = u.permissions; });
          if (v >= 9) {
            confirmModal('Nominare ' + u.name + ' amministratore?', 'Potrà abilitare utenti e gestire utenti e moderatori. Solo tu, come master admin, potrai modificarlo.', 'Nomina amministratore')
              .then(ok => { if (ok) go(); else el.value = u.permissions; });
          } else go();
        });
        if (act === 'approve') el.addEventListener('click', () => updateUser(u, { status: 'active' }, u.name + ' abilitato'));
        if (act === 'enable') el.addEventListener('click', () => updateUser(u, { status: 'active' }, 'Account riabilitato'));
        if (act === 'delete') el.addEventListener('click', () => deleteUser(u));
        if (act === 'menu') el.addEventListener('click', e => { e.stopPropagation(); openUserMenu(el.parentElement, u); });
      });
      box.appendChild(card);
    });
  }

  // ---------- "Account activated" email ----------
  function needsActivationMail(u) {
    return u.status === 'active' && !u.activation_mail_at && u.canManage && u.permissions >= 1;
  }
  // Text appended to a toast after an action that tried to send an email
  function mailOutcome(mail, what) {
    if (!mail) return '';
    return mail.sent ? ' · inviata ' + what : '. Attenzione: ' + what + ' non è partita (' + mail.error + ')';
  }

  function renderMailBanner() {
    const missing = S.users.filter(needsActivationMail).length;
    const banner = $('mail-banner');
    if (!S.mailEnabled) {
      banner.hidden = false;
      banner.classList.add('warn');
      $('mail-banner-text').innerHTML = '<strong>Invio email non configurato.</strong> Manca il file <code>lib/mail-config.php</code> sul server: gli utenti abilitati o rifiutati non ricevono avvisi.';
      $('mail-send-all').hidden = true;
      return;
    }
    banner.classList.remove('warn');
    banner.hidden = missing === 0;
    if (!missing) return;
    $('mail-banner-text').innerHTML = '<strong>' + (missing === 1 ? '1 utente attivo non ha' : missing + ' utenti attivi non hanno') + ' ricevuto l’email di attivazione</strong> con le indicazioni per accedere.';
    $('mail-send-all').hidden = false;
    $('mail-send-all').textContent = missing === 1 ? 'Invia' : 'Invia a tutti (' + missing + ')';
  }

  async function sendActivation(u) {
    const again = !!u.activation_mail_at;
    if (again && !(await confirmModal('Inviare di nuovo l’email?', u.name + ' ha già ricevuto l’email di attivazione il ' + fmtDate(u.activation_mail_at) + '. Vuoi inviarla di nuovo a ' + u.email + '?', 'Invia di nuovo'))) return;
    try {
      await Api.post('admin.user.send_activation', { id: u.id });
      await loadUsers();
      renderUsers();
      toast('Email di attivazione inviata a ' + u.name);
    } catch (e) { toast(e.message); }
  }

  $('mail-send-all').addEventListener('click', async () => {
    const targets = S.users.filter(needsActivationMail);
    if (!targets.length) return;
    const names = targets.slice(0, 5).map(u => u.name).join(', ') + (targets.length > 5 ? ' e altri ' + (targets.length - 5) : '');
    if (!(await confirmModal('Inviare l’email di attivazione?', 'Riceveranno le indicazioni per accedere: ' + names + '.', targets.length === 1 ? 'Invia' : 'Invia ' + targets.length + ' email'))) return;
    const btn = $('mail-send-all');
    btn.disabled = true;
    btn.textContent = 'Invio in corso…';
    try {
      const d = await Api.post('admin.user.send_activation_all', {});
      await loadUsers();
      renderUsers();
      toast(d.failed ? 'Inviate ' + d.sent + ' email, ' + d.failed + ' non partite: ' + d.errors.join('; ') : (d.sent === 1 ? 'Email inviata' : 'Inviate ' + d.sent + ' email'));
    } catch (e) { toast(e.message); renderMailBanner(); }
    finally { btn.disabled = false; }
  });

  function openUserMenu(anchor, u) {
    const wasOpen = S.openMenu && S.openMenu.parentElement === anchor;
    closeUserMenu();
    if (wasOpen) return;
    const menu = document.createElement('div');
    menu.className = 'u-menu';
    menu.setAttribute('role', 'menu');
    const items = [];
    if (u.status === 'active') items.push(['mail', u.activation_mail_at ? 'Invia di nuovo l’email di attivazione' : 'Invia email di attivazione']);
    items.push(['reset', 'Reimposta password']);
    if (u.status === 'active') items.push(['disable', 'Disabilita account']);
    if (S.me.isMaster && u.status === 'active') items.push(['transfer', 'Passa il ruolo di master admin']);
    items.push(['sep'], ['delete', 'Elimina account', 'danger']);
    menu.innerHTML = items.map(([a, label, cls]) => a === 'sep' ? '<hr>' : '<button type="button" role="menuitem" data-m="' + a + '" class="' + (cls || '') + '">' + label + '</button>').join('');
    menu.querySelectorAll('[data-m]').forEach(b => b.addEventListener('click', async () => {
      closeUserMenu();
      const m = b.dataset.m;
      if (m === 'reset') resetPassword(u);
      if (m === 'mail') sendActivation(u);
      if (m === 'delete') deleteUser(u);
      if (m === 'transfer') openTransfer(u);
      if (m === 'disable' && await confirmModal('Disabilitare ' + u.name + '?', 'Verrà disconnesso subito e non potrà accedere finché non lo riabiliti. I suoi progressi restano salvati.', 'Disabilita')) {
        updateUser(u, { status: 'disabled' }, 'Account disabilitato');
      }
    }));
    anchor.appendChild(menu);
    anchor.classList.add('open');
    S.openMenu = menu;
    const first = menu.querySelector('button');
    if (first) first.focus();
  }

  // ---------- Read-only user overview ----------
  async function openUserStats(u) {
    const body = $('us-body');
    $('us-title').textContent = u.name;
    body.innerHTML = '<div class="admin-empty">Carico la panoramica…</div>';
    $('us-modal').hidden = false;
    let d;
    try { d = (await Api.get('admin.user.stats&id=' + u.id)).stats; }
    catch (e) { body.innerHTML = '<div class="form-msg error">' + esc(e.message) + '</div>'; return; }
    const a = d.answers;
    const pct = n => a.total ? Math.round(n / a.total * 100) : 0;
    const tile = (v, label, cls, extra) => '<div class="us-tile"><div class="us-tile-value ' + (cls || '') + '">' + v + (extra ? '<small>' + extra + '</small>' : '') + '</div><div class="us-tile-label">' + label + '</div></div>';
    const sources = Object.keys(d.by_source).map(src => {
      const x = d.by_source[src]; const t = x.correct + x.wrong; const p = t ? Math.round(x.correct / t * 100) : 0;
      return '<div class="us-src ' + (SOURCE_CLASS[src] || '') + '"><span class="us-src-name"><span class="ref-source-tag ' + (SOURCE_CLASS[src] || '') + '">' + (SOURCE_SHORT[src] || '') + '</span>' + esc(src) + '</span>' +
        '<span class="us-src-bar' + (t ? '' : ' empty') + '"><span style="width:' + p + '%"></span></span>' +
        '<span class="us-src-num">' + (t ? '<strong>' + p + '%</strong> ' + x.correct + '/' + t : 'Nessun dato') + '</span></div>';
    }).join('');
    const weak = d.weak_refs.length
      ? d.weak_refs.map(w => '<li><span class="a-code">' + esc(w.code) + '</span> <span class="us-weak-title">' + esc(w.title) + '</span><span class="us-weak-num">' + w.pct + '% · ' + w.wrong + (w.wrong === 1 ? ' errore' : ' errori') + '</span></li>').join('')
      : '<li class="us-none">Nessun errore registrato.</li>';
    const facts = [
      ['Riferimenti esercitati', d.refs_practiced],
      ['Domande salvate', d.saved.questions],
      ['Riferimenti salvati', d.saved.refs],
      ['Segnalazioni aperte', d.flags.open],
      ['Segnalazioni chiuse', d.flags.closed],
      ['Quiz in sospeso', d.suspended_quizzes + (d.quiz_in_progress ? ' (+1 in corso)' : '')],
    ];
    body.innerHTML =
      '<p class="us-readonly">Sola lettura: mostra i progressi di studio, non i dati personali.</p>' +
      '<div class="us-meta"><span class="role-badge ' + roleClass(u.permissions) + '">' + esc(d.role) + '</span>' +
        '<span>Iscritto il ' + fmtDate(d.created_at).split(' ')[0] + '</span>' +
        '<span>Ultimo accesso: ' + fmtDate(d.last_seen_at) + '</span>' +
        (d.last_saved_at ? '<span>Ultimi progressi: ' + fmtDate(d.last_saved_at) + '</span>' : '') + '</div>' +
      '<div class="us-tiles">' + tile(a.total, 'Risposte date') + tile(a.correct, 'Corrette', 'green', a.total ? pct(a.correct) + '%' : '') + tile(a.wrong, 'Errate', 'red', a.total ? pct(a.wrong) + '%' : '') + '</div>' +
      '<h4 class="us-h">Per fonte</h4><div class="us-sources">' + sources + '</div>' +
      '<h4 class="us-h">Argomenti più deboli</h4><ul class="us-weak">' + weak + '</ul>' +
      '<h4 class="us-h">Attività</h4><dl class="us-facts">' + facts.map(([k, v]) => '<div><dt>' + k + '</dt><dd>' + v + '</dd></div>').join('') + '</dl>';
  }
  closeOnBackdrop($('us-modal'), () => { $('us-modal').hidden = true; });

  // ---------- Master admin role transfer ----------
  let transferTarget = null;
  function openTransfer(u) {
    transferTarget = u;
    $('transfer-text').textContent = u.name + ' diventerà master admin e tu passerai ad amministratore (9). Non potrai più gestire gli altri amministratori, a meno che il nuovo master non ti restituisca il ruolo.';
    $('transfer-error').hidden = true;
    $('transfer-password').value = '';
    $('transfer-modal').hidden = false;
    setTimeout(() => $('transfer-password').focus(), 0);
  }
  function closeTransfer() { $('transfer-modal').hidden = true; transferTarget = null; }
  $('transfer-cancel').addEventListener('click', closeTransfer);
  closeOnBackdrop($('transfer-modal'), closeTransfer);
  $('transfer-form').addEventListener('submit', async e => {
    e.preventDefault();
    if (!transferTarget) return;
    await withBusy($('transfer-confirm'), async () => {
      try {
        await Api.post('admin.user.transfer_master', { id: transferTarget.id, password: $('transfer-password').value });
        closeTransfer();
        toast('Ruolo di master admin passato');
        setTimeout(() => window.location.reload(), 900);
      } catch (ex) {
        $('transfer-error').textContent = ex.message;
        $('transfer-error').hidden = false;
      }
    });
  });

  async function updateUser(u, changes, okMsg, onError) {
    try {
      const d = await Api.post('admin.user.update', Object.assign({ id: u.id }, changes));
      await loadUsers();
      renderUsers();
      toast(okMsg + mailOutcome(d.mail, 'l’email con le indicazioni per accedere'));
    } catch (e) {
      toast(e.message);
      if (onError) onError();
    }
  }

  async function resetPassword(u) {
    if (!(await confirmModal('Reimpostare la password?', 'Verrà generata una password temporanea per ' + u.name + ' e le sue sessioni aperte verranno chiuse.' +
      (S.mailEnabled ? ' La riceverà anche via email, a ' + u.email + '.' : '') + ' Al primo accesso dovrà sceglierne una nuova.', 'Genera password'))) return;
    try {
      const d = await Api.post('admin.user.reset_password', { id: u.id });
      $('pw-modal-text').textContent = 'Nuova password temporanea per ' + u.name + ' (' + u.email + '):';
      $('pw-value').textContent = d.password;
      // Tell the admin whether the user already received it by email
      const pm = $('pw-mail');
      pm.hidden = false;
      pm.className = 'pw-mail ' + (d.mail && d.mail.sent ? 'ok' : 'warn');
      pm.textContent = d.mail && d.mail.sent ? 'Inviata via email a ' + u.email + '.'
        : d.mail ? 'Email non inviata (' + d.mail.error + '): comunicala tu all’utente in modo riservato.'
        : 'Invio email non configurato: comunicala tu all’utente in modo riservato.';
      $('pw-modal').hidden = false;
      await loadUsers();
      renderUsers();
    } catch (e) { toast(e.message); }
  }
  $('pw-copy').addEventListener('click', () => {
    navigator.clipboard.writeText($('pw-value').textContent).then(() => toast('Password copiata')).catch(() => toast('Copia non riuscita'));
  });
  $('pw-close').addEventListener('click', () => { $('pw-modal').hidden = true; $('pw-value').textContent = ''; });

  async function deleteUser(u) {
    const pending = u.status === 'pending';
    const ok = await confirmModal(pending ? 'Rifiutare la registrazione?' : 'Eliminare ' + u.name + '?',
      pending ? 'La richiesta di ' + u.name + ' verrà eliminata' + (S.mailEnabled ? ' e riceverà un’email che lo avvisa.' : '.') : 'L\'account e tutti i suoi progressi (statistiche, segnalibri, quiz sospesi) verranno eliminati definitivamente.',
      pending ? 'Rifiuta' : 'Elimina');
    if (!ok) return;
    try {
      const d = await Api.post('admin.user.delete', { id: u.id });
      await loadUsers();
      renderUsers();
      toast(pending ? 'Registrazione rifiutata' + mailOutcome(d.mail, 'l’email che lo avvisa') : 'Utente eliminato');
    } catch (e) { toast(e.message); }
  }

  $('users-search').addEventListener('input', renderUsers);
  $('users-role').addEventListener('change', renderUsers);
  document.querySelectorAll('#users-filters .filter-chip').forEach(c => c.addEventListener('click', () => {
    S.userFilter = c.dataset.status;
    document.querySelectorAll('#users-filters .filter-chip').forEach(x => x.classList.toggle('active', x === c));
    renderUsers();
  }));

  // ====================================================================
  // QUESTIONS
  // ====================================================================

  function flagCountByCode() {
    const m = {};
    S.flags.forEach(f => { if (!f.resolved) m[f.code] = (m[f.code] || 0) + 1; });
    return m;
  }

  function filteredQuestions() {
    const q = norm($('q-search').value.trim());
    const src = $('q-filter-source').value;
    const type = $('q-filter-type').value;
    const diff = $('q-filter-diff').value;
    const act = $('q-filter-active').value;
    const flags = flagCountByCode();
    return S.questions.filter(x => {
      if (type && x['question-type'] !== type) return false;
      if (diff && x['question-difficulty'] !== diff) return false;
      if (act === '1' && !x.active) return false;
      if (act === '0' && x.active) return false;
      if (act === 'flag' && !flags[x['question-code']]) return false;
      if (act === 'noref' && x['question-ref'].length && x['question-ref'].every(rc => S.refMap[rc])) return false;
      if (src && !x['question-ref'].some(rc => sourceOfRef(rc) === src)) return false;
      if (q) {
        const hay = norm(x['question-code'] + ' ' + x['question-text'] + ' ' + x['question-ref'].join(' ') + ' ' + x['question-answers'].map(a => a.text).join(' '));
        if (!q.split(/\s+/).every(w => hay.includes(w))) return false;
      }
      return true;
    });
  }

  function renderQuestions() {
    const list = filteredQuestions();
    const flags = flagCountByCode();
    const active = S.questions.filter(q => q.active).length;
    $('q-count').textContent = list.length + ' domande' + (list.length !== S.questions.length ? ' su ' + S.questions.length : '') + ' · ' + active + ' attive';
    const box = $('q-list');
    box.innerHTML = '';
    S.qList = list; S.qShown = 0; S.qFlags = flags;
    if (!list.length) { box.innerHTML = '<div class="admin-empty">Nessuna domanda corrisponde ai filtri.</div>'; $('q-more').hidden = true; return; }
    appendQuestions(S.qLimit);
    checkMoreQuestions();
  }

  // Appends the next `count` questions of the current list
  function appendQuestions(count) {
    const box = $('q-list');
    const flags = S.qFlags;
    const next = S.qList.slice(S.qShown, S.qShown + count);
    next.forEach(q => {
      const code = q['question-code'];
      const card = document.createElement('div');
      card.className = 'a-card clickable' + (q.active ? '' : ' inactive');
      card.tabIndex = 0;
      const correct = q['question-answers'].filter(a => a.correct).map(a => a.text).join(' · ');
      card.innerHTML =
        '<div class="a-card-top">' +
          '<span class="a-code">' + esc(code) + '</span>' +
          '<span class="pill">' + TYPE_LABEL[q['question-type']] + '</span>' +
          '<span class="quiz-diff-badge diff-' + esc(q['question-difficulty']) + '">' + DIFF_LABEL[q['question-difficulty']] + '</span>' +
          (q.active ? '' : '<span class="pill red">Disattivata</span>') +
          (flags[code] ? '<span class="pill gold">⚑ ' + flags[code] + (flags[code] === 1 ? ' segnalazione' : ' segnalazioni') + '</span>' : '') +
        '</div>' +
        '<div class="a-text">' + esc(q['question-text']) + '</div>' +
        '<div class="a-meta"><span>✓ ' + esc(correct) + '</span></div>' +
        '<div class="a-tags">' + q['question-ref'].map(rc => '<span class="a-tag">' + esc(rc) + '</span>').join('') + '</div>';
      card.addEventListener('click', () => openQuestion(code));
      card.addEventListener('keydown', e => { if (e.key === 'Enter') openQuestion(code); });
      box.appendChild(card);
    });
    S.qShown += next.length;
    S.qLimit = Math.max(S.qLimit, S.qShown);
    $('q-more').hidden = S.qShown >= S.qList.length;
    return next.length > 0 && S.qShown < S.qList.length;
  }
  const checkMoreQuestions = InfiniteList.watch($('q-more'), () => appendQuestions(PAGE));

  ['q-search'].forEach(id => $(id).addEventListener('input', () => { S.qLimit = PAGE; renderQuestions(); }));
  ['q-filter-source', 'q-filter-type', 'q-filter-diff', 'q-filter-active'].forEach(id => $(id).addEventListener('change', () => { S.qLimit = PAGE; renderQuestions(); }));

  // ---------- Question editor ----------

  const QE = { code: null, type: 'multiple-choice', answers: [], refs: [], ynTrue: true, suggestIdx: -1 };

  function openQuestion(code) {
    const q = code ? S.questions.find(x => x['question-code'] === code) : null;
    QE.code = q ? code : null;
    QE.type = q ? q['question-type'] : 'multiple-choice';
    QE.answers = q && q['question-type'] !== 'yes-no'
      ? q['question-answers'].map(a => ({ text: a.text, correct: !!a.correct }))
      : [{ text: '', correct: true }, { text: '', correct: false }, { text: '', correct: false }, { text: '', correct: false }];
    QE.ynTrue = q && q['question-type'] === 'yes-no' ? !!(q['question-answers'].find(a => a.value === 'V') || {}).correct : true;
    QE.refs = q ? q['question-ref'].slice() : [];
    $('q-editor-title').textContent = q ? 'Domanda ' + code : 'Nuova domanda';
    $('q-text').value = q ? q['question-text'] : '';
    $('q-difficulty').value = q ? q['question-difficulty'] : 'm';
    $('q-active').checked = q ? !!q.active : true;
    $('q-delete').hidden = !q;
    $('q-error').hidden = true;
    $('q-ref-input').value = '';
    $('q-ref-suggest').hidden = true;

    const fl = q ? S.flags.filter(f => f.code === code && !f.resolved) : [];
    const flBox = $('q-editor-flags');
    flBox.hidden = !fl.length;
    flBox.innerHTML = fl.length ? '<strong>Segnalazioni aperte</strong>' + fl.map(f =>
      '<div class="f-item"><div class="f-item-head"><strong>' + esc(f.user.name) + '</strong><span>' + fmtIso(f.date) + '</span></div>' +
      (f.text ? '<div class="f-note">' + esc(f.text) + '</div>' : '<div class="f-note"><em>Nessuna descrizione</em></div>') + '</div>').join('') : '';

    renderQEType();
    renderQERefs();
    $('q-editor').hidden = false;
    setTimeout(() => $('q-text').focus(), 0);
  }

  function closeQuestion() { $('q-editor').hidden = true; }
  closeOnBackdrop($('q-editor'), closeQuestion);

  function renderQEType() {
    document.querySelectorAll('#q-type button').forEach(b => b.setAttribute('aria-checked', b.dataset.type === QE.type ? 'true' : 'false'));
    const hint = $('q-answers-hint');
    const box = $('q-answers');
    box.innerHTML = '';
    if (QE.type === 'yes-no') {
      hint.textContent = 'Scegli quale delle due è la risposta corretta.';
      $('q-add-answer').hidden = true;
      box.innerHTML =
        '<div class="yn-choice">' +
          '<label><input type="radio" name="yn" value="V"' + (QE.ynTrue ? ' checked' : '') + '> Vero</label>' +
          '<label><input type="radio" name="yn" value="F"' + (!QE.ynTrue ? ' checked' : '') + '> Falso</label>' +
        '</div>';
      box.querySelectorAll('input[name="yn"]').forEach(r => r.addEventListener('change', () => { QE.ynTrue = r.value === 'V'; }));
      return;
    }
    const multi = QE.type === 'multi-select';
    hint.textContent = multi ? 'Spunta tutte le risposte corrette (almeno una).' : 'Spunta l\'unica risposta corretta.';
    $('q-add-answer').hidden = QE.answers.length >= 10;
    QE.answers.forEach((a, i) => {
      const row = document.createElement('div');
      row.className = 'ans-row' + (a.correct ? ' correct' : '');
      row.innerHTML =
        '<span class="ans-letter">' + String.fromCharCode(65 + i) + '</span>' +
        '<input class="form-input" maxlength="1000" placeholder="Risposta ' + String.fromCharCode(65 + i) + '">' +
        '<label class="ans-correct"><input type="' + (multi ? 'checkbox' : 'radio') + '" name="ans-correct"' + (a.correct ? ' checked' : '') + '> Corretta</label>' +
        '<button type="button" class="icon-btn ans-remove" aria-label="Rimuovi risposta"' + (QE.answers.length <= 2 ? ' disabled' : '') + '>✕</button>';
      const input = row.querySelector('.form-input');
      input.value = a.text;
      input.addEventListener('input', () => { a.text = input.value; });
      row.querySelector('.ans-correct input').addEventListener('change', e => {
        if (multi) a.correct = e.target.checked;
        else QE.answers.forEach((x, j) => { x.correct = j === i; });
        renderQEType();
      });
      row.querySelector('.ans-remove').addEventListener('click', () => {
        if (QE.answers.length <= 2) return;
        QE.answers.splice(i, 1);
        if (!multi && !QE.answers.some(x => x.correct)) QE.answers[0].correct = true;
        renderQEType();
      });
      box.appendChild(row);
    });
  }

  document.querySelectorAll('#q-type button').forEach(b => b.addEventListener('click', () => {
    const prev = QE.type;
    QE.type = b.dataset.type;
    if (prev !== QE.type && QE.type === 'multiple-choice') {
      const first = QE.answers.findIndex(a => a.correct);
      QE.answers.forEach((a, j) => { a.correct = j === (first < 0 ? 0 : first); });
    }
    if (QE.type !== 'yes-no' && QE.answers.length < 2) QE.answers.push({ text: '', correct: false });
    renderQEType();
  }));
  $('q-add-answer').addEventListener('click', () => {
    QE.answers.push({ text: '', correct: false });
    renderQEType();
    const inputs = $('q-answers').querySelectorAll('.form-input');
    inputs[inputs.length - 1].focus();
  });

  function renderQERefs() {
    const box = $('q-refs');
    box.innerHTML = '';
    QE.refs.forEach((code, i) => {
      const r = S.refMap[code];
      const src = sourceOfRef(code);
      const chip = document.createElement('span');
      chip.className = 'ref-chip';
      chip.innerHTML = '<span class="ref-source-tag ' + (SOURCE_CLASS[src] || 'rg') + '">' + esc(SOURCE_SHORT[src] || '?') + '</span>' +
        '<strong>' + esc(code) + '</strong>' +
        (r ? '<span class="ref-chip-title">' + esc(r['ref-title']) + '</span>' : '<span class="ref-chip-title" style="color:var(--red)">non trovato</span>') +
        '<button type="button" aria-label="Rimuovi ' + esc(code) + '">✕</button>';
      chip.querySelector('button').addEventListener('click', () => { QE.refs.splice(i, 1); renderQERefs(); });
      box.appendChild(chip);
    });
  }

  function refSuggestions(term) {
    const t = norm(term.trim());
    if (!t) return [];
    const words = t.split(/\s+/);
    const scored = [];
    S.refs.forEach(r => {
      if (QE.refs.includes(r['ref-code'])) return;
      const code = norm(r['ref-code']);
      const hay = code + ' ' + norm(r['ref-title']) + ' ' + norm(r['ref-text']);
      if (!words.every(w => hay.includes(w))) return;
      const score = code === t ? 0 : code.startsWith(t) ? 1 : code.includes(t) ? 2 : norm(r['ref-title']).includes(t) ? 3 : 4;
      scored.push([score, r]);
    });
    scored.sort((a, b) => a[0] - b[0] || strnat(a[1]['ref-code'], b[1]['ref-code']));
    return scored.slice(0, 12).map(x => x[1]);
  }
  function strnat(a, b) { return a.localeCompare(b, 'it', { numeric: true }); }

  function renderSuggest() {
    const input = $('q-ref-input');
    const box = $('q-ref-suggest');
    const items = refSuggestions(input.value);
    if (!input.value.trim()) { box.hidden = true; return; }
    box.hidden = false;
    QE.suggestIdx = Math.min(QE.suggestIdx, items.length - 1);
    if (!items.length) { box.innerHTML = '<div class="ref-suggest-empty">Nessun riferimento trovato.</div>'; return; }
    box.innerHTML = '';
    items.forEach((r, i) => {
      const el = document.createElement('div');
      el.className = 'ref-suggest-item' + (i === QE.suggestIdx ? ' active' : '');
      el.innerHTML = '<span class="ref-source-tag ' + (SOURCE_CLASS[r['ref-from']] || 'rg') + '">' + esc(SOURCE_SHORT[r['ref-from']]) + '</span>' +
        '<strong>' + esc(r['ref-code']) + '</strong><span class="t">' + esc(r['ref-title']) + '</span>';
      el.addEventListener('mousedown', e => { e.preventDefault(); pickRef(r['ref-code']); });
      box.appendChild(el);
    });
    box._items = items;
  }
  function pickRef(code) {
    if (!QE.refs.includes(code)) QE.refs.push(code);
    $('q-ref-input').value = '';
    QE.suggestIdx = -1;
    $('q-ref-suggest').hidden = true;
    renderQERefs();
    $('q-ref-input').focus();
  }
  $('q-ref-input').addEventListener('input', () => { QE.suggestIdx = 0; renderSuggest(); });
  $('q-ref-input').addEventListener('blur', () => setTimeout(() => { $('q-ref-suggest').hidden = true; }, 120));
  $('q-ref-input').addEventListener('keydown', e => {
    const box = $('q-ref-suggest');
    const items = box._items || [];
    if (e.key === 'ArrowDown') { e.preventDefault(); QE.suggestIdx = Math.min(QE.suggestIdx + 1, items.length - 1); renderSuggest(); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); QE.suggestIdx = Math.max(QE.suggestIdx - 1, 0); renderSuggest(); }
    else if (e.key === 'Enter') {
      e.preventDefault();
      if (!box.hidden && items[QE.suggestIdx]) pickRef(items[QE.suggestIdx]['ref-code']);
    } else if (e.key === 'Escape' && !box.hidden) { e.stopPropagation(); box.hidden = true; }
  });

  $('q-form').addEventListener('submit', async e => {
    e.preventDefault();
    const err = $('q-error');
    err.hidden = true;
    const body = {
      code: QE.code || '',
      text: $('q-text').value.trim(),
      type: QE.type,
      difficulty: $('q-difficulty').value,
      active: $('q-active').checked,
      refs: QE.refs,
      answers: QE.type === 'yes-no'
        ? [{ value: 'V', text: 'Vero', correct: QE.ynTrue }, { value: 'F', text: 'Falso', correct: !QE.ynTrue }]
        : QE.answers.map(a => ({ text: a.text.trim(), correct: a.correct })).filter(a => a.text),
    };
    const problem = !body.text ? 'Scrivi il testo della domanda.'
      : body.type !== 'yes-no' && body.answers.length < 2 ? 'Servono almeno 2 risposte compilate.'
      : body.type === 'multiple-choice' && body.answers.filter(a => a.correct).length !== 1 ? 'Indica una risposta corretta (tra quelle compilate).'
      : body.type === 'multi-select' && !body.answers.some(a => a.correct) ? 'Indica almeno una risposta corretta.'
      : !body.refs.length ? 'Collega almeno un riferimento normativo.' : '';
    if (problem) { err.textContent = problem; err.hidden = false; err.scrollIntoView({ block: 'nearest' }); return; }
    await withBusy($('q-save'), async () => {
      try {
        const d = await Api.post('admin.question.save', body);
        await loadContent();
        closeQuestion();
        renderQuestions();
        toast(QE.code ? 'Domanda ' + d.code + ' aggiornata' : 'Domanda ' + d.code + ' creata');
      } catch (ex) {
        err.textContent = ex.message;
        err.hidden = false;
        err.scrollIntoView({ block: 'nearest' });
      }
    });
  });

  $('q-delete').addEventListener('click', async () => {
    const code = QE.code;
    const ok = await confirmModal('Eliminare la domanda ' + code + '?',
      'L\'eliminazione è definitiva. Se vuoi solo toglierla dai quiz, disattivala con l\'interruttore «Attiva».', 'Elimina');
    if (!ok) return;
    try {
      await Api.post('admin.question.delete', { code });
      await loadContent();
      closeQuestion();
      renderQuestions();
      toast('Domanda ' + code + ' eliminata');
    } catch (ex) { toast(ex.message); }
  });

  $('q-new').addEventListener('click', () => openQuestion(null));

  // ---------- Export (JSON file) ----------
  function downloadJson(data, filename) {
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = filename;
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
  }
  const today = () => new Date().toISOString().slice(0, 10);
  $('q-export').addEventListener('click', () => { downloadJson({ questions: S.questions }, 'fipav-domande-' + today() + '.json'); toast(S.questions.length + ' domande esportate'); });
  $('r-export').addEventListener('click', () => { downloadJson({ refs: S.refs }, 'fipav-riferimenti-' + today() + '.json'); toast(S.refs.length + ' riferimenti esportati'); });

  // ====================================================================
  // REFERENCES
  // ====================================================================

  function initRefFilters() {
    const box = $('r-filters');
    box.innerHTML = '';
    S.sources.forEach(src => {
      const chip = document.createElement('button');
      chip.className = 'filter-chip ' + (SOURCE_CLASS[src] || '');
      chip.innerHTML = '<span class="dot" style="background:var(--src)"></span>' + esc(src);
      chip.addEventListener('click', () => {
        if (S.rSources.has(src)) S.rSources.delete(src); else S.rSources.add(src);
        chip.classList.toggle('active', S.rSources.has(src));
        S.rLimit = PAGE;
        renderRefs();
      });
      box.appendChild(chip);
    });
    const sel = $('q-filter-source');
    S.sources.forEach(src => { const o = document.createElement('option'); o.value = src; o.textContent = src; sel.appendChild(o); });
    const rs = $('r-source');
    S.sources.forEach(src => { const o = document.createElement('option'); o.value = src; o.textContent = src + ' (' + SOURCE_SHORT[src] + ')'; rs.appendChild(o); });
  }

  function renderRefs() {
    const q = norm($('r-search').value.trim());
    const list = S.refs.filter(r => {
      if (S.rSources.size && !S.rSources.has(r['ref-from'])) return false;
      if ($('r-unused').checked && r.usage) return false;
      const chf = $('r-filter-change').value;
      if (chf === 'current' && !(r['ref-change'] && r['ref-change'].season === CURRENT_SEASON)) return false;
      if (chf === 'any' && !r['ref-change']) return false;
      if (q) {
        const hay = norm(r['ref-code'] + ' ' + r['ref-title'] + ' ' + r['ref-text'] + ' ' + r['ref-full-text']);
        if (!q.split(/\s+/).every(w => hay.includes(w))) return false;
      }
      return true;
    });
    $('r-count').textContent = list.length + ' riferimenti' + (list.length !== S.refs.length ? ' su ' + S.refs.length : '');
    const box = $('r-list');
    box.innerHTML = '';
    S.rList = list; S.rShown = 0;
    if (!list.length) { box.innerHTML = '<div class="admin-empty">Nessun riferimento trovato.</div>'; $('r-more').hidden = true; return; }
    appendRefs(S.rLimit);
    checkMoreRefs();
  }

  // Appends the next `count` references of the current list
  function appendRefs(count) {
    const box = $('r-list');
    const next = S.rList.slice(S.rShown, S.rShown + count);
    next.forEach(r => {
      const card = document.createElement('div');
      card.className = 'a-card clickable';
      card.tabIndex = 0;
      card.innerHTML =
        '<div class="a-card-top">' +
          '<span class="ref-source-tag ' + (SOURCE_CLASS[r['ref-from']] || 'rg') + '">' + esc(SOURCE_SHORT[r['ref-from']]) + '</span>' +
          '<span class="a-code">' + esc(r['ref-code']) + '</span>' +
          '<span style="font-weight:600">' + esc(r['ref-title']) + '</span>' + changeBadge(r['ref-change']) +
          '<span class="spacer"></span>' +
          '<span class="pill' + (r.usage ? '' : ' gold') + '">' + (r.usage ? r.usage + (r.usage === 1 ? ' domanda' : ' domande') : 'Nessuna domanda') + '</span>' +
        '</div>' +
        '<div class="a-text">' + esc(r['ref-text']) + '</div>' +
        (r['ref-change'] && r['ref-change'].note ? '<div class="a-meta"><span>Cosa cambia: ' + esc(r['ref-change'].note) + '</span></div>' : '');
      card.addEventListener('click', () => openRef(r['ref-code']));
      card.addEventListener('keydown', e => { if (e.key === 'Enter') openRef(r['ref-code']); });
      box.appendChild(card);
    });
    S.rShown += next.length;
    S.rLimit = Math.max(S.rLimit, S.rShown);
    $('r-more').hidden = S.rShown >= S.rList.length;
    return next.length > 0 && S.rShown < S.rList.length;
  }
  const checkMoreRefs = InfiniteList.watch($('r-more'), () => appendRefs(PAGE));
  $('r-search').addEventListener('input', () => { S.rLimit = PAGE; renderRefs(); });
  $('r-unused').addEventListener('change', () => { S.rLimit = PAGE; renderRefs(); });
  $('r-filter-change').addEventListener('change', () => { S.rLimit = PAGE; renderRefs(); });

  // ---------- Reference editor ----------

  let RE = { original: null };

  function updateCodeHint() {
    const src = $('r-source').value;
    const prefix = SOURCE_SHORT[src] + '-';
    $('r-code-hint').textContent = 'Deve iniziare con «' + prefix + '», es. ' + prefix + '1.2';
    $('r-rename-warn').hidden = !RE.original || $('r-code').value.trim() === RE.original;
  }

  function openRef(code) {
    const r = code ? S.refMap[code] : null;
    RE.original = r ? code : null;
    $('r-editor-title').textContent = r ? 'Riferimento ' + code : 'Nuovo riferimento';
    $('r-source').value = r ? r['ref-from'] : (S.rSources.size === 1 ? Array.from(S.rSources)[0] : S.sources[0]);
    $('r-code').value = r ? code : SOURCE_SHORT[$('r-source').value] + '-';
    $('r-title').value = r ? r['ref-title'] : '';
    $('r-summary').value = r ? r['ref-text'] : '';
    $('r-full').value = r ? r['ref-full-text'] : '';
    const ch = r ? r['ref-change'] : null;
    fillSeasonOptions(ch ? ch.season : CURRENT_SEASON);
    $('r-change-type').value = ch ? ch.type : '';
    $('r-change-note').value = ch ? ch.note : '';
    updateChangeFields();
    $('r-error').hidden = true;
    $('r-delete').hidden = !r;
    const used = r ? S.questions.filter(q => q['question-ref'].includes(code)) : [];
    const usage = $('r-usage');
    usage.hidden = !r;
    if (r) {
      usage.innerHTML = used.length
        ? '<strong>Usato da ' + used.length + (used.length === 1 ? ' domanda' : ' domande') + ':</strong> ' +
          used.map(q => '<a data-q="' + esc(q['question-code']) + '">' + esc(q['question-code']) + '</a>').join(', ') +
          '<br><span class="form-hint">Per eliminarlo, prima toglilo da queste domande.</span>'
        : 'Non è collegato a nessuna domanda.';
      usage.querySelectorAll('a[data-q]').forEach(a => a.addEventListener('click', () => { closeRef(); openQuestion(a.dataset.q); }));
    }
    updateCodeHint();
    $('r-editor').hidden = false;
    setTimeout(() => (r ? $('r-title') : $('r-code')).focus(), 0);
  }
  function closeRef() { $('r-editor').hidden = true; }

  // Offered seasons: from 2020/21 to the one after the current season (plus the saved one, if different)
  function fillSeasonOptions(selected) {
    const sel = $('r-change-season');
    const seasons = [];
    for (let y = CURRENT_START + 1; y >= 2020; y--) seasons.push(seasonFromYear(y));
    if (selected && !seasons.includes(selected)) seasons.push(selected);
    sel.innerHTML = seasons.map(se => '<option value="' + se + '"' + (se === selected ? ' selected' : '') + '>' + se + (se === CURRENT_SEASON ? ' (in corso)' : '') + '</option>').join('');
  }
  function updateChangeFields() {
    const none = !$('r-change-type').value;
    $('r-change-season-field').hidden = none;
    $('r-change-note-field').hidden = none;
  }
  $('r-change-type').addEventListener('change', updateChangeFields);
  closeOnBackdrop($('r-editor'), closeRef);

  $('r-source').addEventListener('change', () => {
    const code = $('r-code').value.trim();
    const m = code.match(/^[A-Z]+-(.*)$/);
    $('r-code').value = SOURCE_SHORT[$('r-source').value] + '-' + (m ? m[1] : '');
    updateCodeHint();
  });
  $('r-code').addEventListener('input', updateCodeHint);

  $('r-form').addEventListener('submit', async e => {
    e.preventDefault();
    const err = $('r-error');
    err.hidden = true;
    const body = {
      original_code: RE.original || '',
      code: $('r-code').value.trim(),
      source: $('r-source').value,
      title: $('r-title').value.trim(),
      summary: $('r-summary').value.trim(),
      full_text: $('r-full').value.trim(),
      change_type: $('r-change-type').value,
      change_season: $('r-change-type').value ? $('r-change-season').value : '',
      change_note: $('r-change-type').value ? $('r-change-note').value.trim() : '',
    };
    const prefix = SOURCE_SHORT[body.source] + '-';
    const problem = !body.code.startsWith(prefix) || body.code.length <= prefix.length ? 'Il codice deve iniziare con «' + prefix + '» seguito dal numero.'
      : !body.title ? 'Inserisci un titolo.' : !body.summary ? 'Inserisci il riassunto.' : '';
    if (problem) { err.textContent = problem; err.hidden = false; return; }
    await withBusy($('r-save'), async () => {
      try {
        const d = await Api.post('admin.ref.save', body);
        await loadContent();
        closeRef();
        renderRefs();
        toast(RE.original ? 'Riferimento ' + d.code + ' aggiornato' : 'Riferimento ' + d.code + ' creato');
      } catch (ex) { err.textContent = ex.message; err.hidden = false; }
    });
  });

  $('r-delete').addEventListener('click', async () => {
    const code = RE.original;
    if (!(await confirmModal('Eliminare ' + code + '?', 'Il riferimento verrà eliminato definitivamente.', 'Elimina'))) return;
    try {
      await Api.post('admin.ref.delete', { code });
      await loadContent();
      closeRef();
      renderRefs();
      toast('Riferimento ' + code + ' eliminato');
    } catch (ex) { $('r-error').textContent = ex.message; $('r-error').hidden = false; }
  });
  $('r-new').addEventListener('click', () => openRef(null));

  // ====================================================================
  // REPORTS
  // ====================================================================

  function renderFlags() {
    const openOnly = $('flags-open-only').checked;
    const groups = {};
    S.flags.forEach(f => {
      if (openOnly && f.resolved) return;
      (groups[f.code] = groups[f.code] || []).push(f);
    });
    const codes = Object.keys(groups).sort((a, b) => groups[b].length - groups[a].length || strnat(a, b));
    const box = $('flags-list');
    box.innerHTML = '';
    if (!codes.length) { box.innerHTML = '<div class="admin-empty">' + (openOnly ? 'Nessuna segnalazione aperta.' : 'Nessuna segnalazione.') + '</div>'; return; }
    codes.forEach(code => {
      const q = S.questions.find(x => x['question-code'] === code);
      const card = document.createElement('div');
      card.className = 'a-card';
      card.innerHTML =
        '<div class="a-card-top">' +
          '<span class="a-code">' + esc(code) + '</span>' +
          (q ? '<span class="pill">' + TYPE_LABEL[q['question-type']] + '</span>' + (q.active ? '' : '<span class="pill red">Disattivata</span>') : '<span class="pill red">Domanda eliminata</span>') +
          '<span class="pill gold">⚑ ' + groups[code].length + '</span>' +
          '<span class="spacer"></span>' +
          (q ? '<button class="btn btn-secondary btn-sm" data-edit>Modifica domanda</button>' : '') +
        '</div>' +
        (q ? '<div class="a-text" style="-webkit-line-clamp:3">' + esc(q['question-text']) + '</div>' : '') +
        groups[code].map(f =>
          '<div class="f-item"><div class="f-item-head"><strong>' + esc(f.user.name) + '</strong><span>' + esc(f.user.email) + '</span><span>' + fmtIso(f.date) + '</span>' +
            (f.resolved ? '<span class="flag-status-badge resolved">Chiusa</span>' : '<span class="flag-status-badge open">Aperta</span>') + '</div>' +
          (f.text ? '<div class="f-note">' + esc(f.text) + '</div>' : '<div class="f-note"><em>Nessuna descrizione</em></div>') + '</div>'
        ).join('');
      const edit = card.querySelector('[data-edit]');
      if (edit) edit.addEventListener('click', () => openQuestion(code));
      box.appendChild(card);
    });
  }
  $('flags-open-only').addEventListener('change', renderFlags);


  // ====================================================================
  // DOCUMENTS ("Other useful documents", administrators only)
  // ====================================================================

  async function loadDocs() {
    if (!S.me.canManageUsers) return;
    const d = await Api.get('admin.documents');
    S.docs = d.documents;
    S.docMax = d.maxBytes;
  }
  function fmtBytes(b) {
    if (!b) return '';
    return b >= 1048576 ? (b / 1048576).toFixed(1).replace('.', ',') + ' MB' : Math.max(1, Math.round(b / 1024)) + ' kB';
  }

  function renderDocs() {
    const box = $('d-list');
    $('d-count').textContent = S.docs.length === 1 ? '1 documento' : S.docs.length + ' documenti';
    if (!S.docs.length) { box.innerHTML = '<div class="admin-empty">Nessun documento. Con «Nuovo documento» carichi il primo PDF.</div>'; return; }
    box.innerHTML = '';
    S.docs.forEach((d, i) => {
      const card = document.createElement('div');
      card.className = 'a-card d-card';
      const url = d.file + '?v=' + d.version;
      card.innerHTML =
        '<div class="d-icon" aria-hidden="true">PDF</div>' +
        '<div><div class="a-card-top"><span class="d-title">' + esc(d.title) + '</span>' +
          (d.exists ? '' : '<span class="pill red">File mancante sul server</span>') + '</div>' +
          (d.edition ? '<div class="u-email">' + esc(d.edition) + '</div>' : '') +
          '<div class="a-meta">' + (d.size ? '<span>' + fmtBytes(d.size) + '</span>' : '') + '<span>Aggiornato il ' + fmtDate(d.updated_at) + '</span></div></div>' +
        '<div class="d-actions">' +
          '<span class="d-order">' +
            '<button class="icon-btn" data-move="-1" aria-label="Sposta su"' + (i === 0 ? ' disabled' : '') + '><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg></button>' +
            '<button class="icon-btn" data-move="1" aria-label="Sposta giù"' + (i === S.docs.length - 1 ? ' disabled' : '') + '><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></button>' +
          '</span>' +
          (d.exists ? '<a class="btn btn-ghost btn-sm" href="' + esc(url) + '" target="_blank" rel="noopener">Apri</a>' : '') +
          '<button class="btn btn-secondary btn-sm" data-edit>Modifica</button>' +
        '</div>';
      card.querySelector('[data-edit]').addEventListener('click', () => openDoc(d.id));
      card.querySelectorAll('[data-move]').forEach(b => b.addEventListener('click', async () => {
        try {
          await Api.post('admin.document.move', { id: d.id, dir: parseInt(b.dataset.move, 10) });
          await loadDocs(); renderDocs();
        } catch (e) { toast(e.message); }
      }));
      box.appendChild(card);
    });
  }

  // ---------- Document editor ----------
  let DE = { id: null, file: null };
  function setDocFile(f) {
    const drop = $('d-drop');
    $('d-error').hidden = true;
    if (f) {
      const isPdf = /\.pdf$/i.test(f.name) || f.type === 'application/pdf';
      if (!isPdf) { showDocError('Scegli un file PDF.'); f = null; }
      else if (S.docMax && f.size > S.docMax) { showDocError('Il PDF pesa ' + fmtBytes(f.size) + ': il limite è ' + fmtBytes(S.docMax) + '.'); f = null; }
    }
    DE.file = f || null;
    drop.classList.toggle('has-file', !!DE.file);
    $('d-drop-text').textContent = DE.file ? DE.file.name + ' (' + fmtBytes(DE.file.size) + ')' : (DE.id ? 'Trascina qui un nuovo PDF o tocca per sceglierlo' : 'Trascina qui il PDF o tocca per sceglierlo');
    if (!DE.file) $('d-file').value = '';
  }
  function showDocError(msg) { const e = $('d-error'); e.textContent = msg; e.hidden = false; e.scrollIntoView({ block: 'nearest' }); }

  function openDoc(id) {
    const d = id ? S.docs.find(x => x.id === id) : null;
    DE = { id: d ? d.id : null, file: null };
    $('d-editor-title').textContent = d ? 'Modifica documento' : 'Nuovo documento';
    $('d-title').value = d ? d.title : '';
    $('d-edition').value = d ? d.edition : '';
    $('d-description').value = d ? d.description : '';
    $('d-file-label').textContent = d ? 'Sostituisci il PDF (facoltativo)' : 'File PDF';
    $('d-current').hidden = !d;
    if (d) $('d-current').innerHTML = 'File attuale: <a href="' + esc(d.file + '?v=' + d.version) + '" target="_blank" rel="noopener">' + esc(d.file.split('/').pop()) + '</a>' + (d.size ? ' · ' + fmtBytes(d.size) : '') + (d.exists ? '' : ' · <strong style="color:var(--red)">mancante sul server: caricalo di nuovo</strong>');
    $('d-limit').textContent = S.docMax ? 'Solo PDF, massimo ' + fmtBytes(S.docMax) + '.' : 'Solo PDF.';
    $('d-delete').hidden = !d;
    $('d-error').hidden = true;
    $('d-progress').hidden = true;
    setDocFile(null);
    $('d-editor').hidden = false;
    setTimeout(() => $('d-title').focus(), 0);
  }
  function closeDoc() { $('d-editor').hidden = true; }
  closeOnBackdrop($('d-editor'), closeDoc);
  $('d-new').addEventListener('click', () => openDoc(null));
  $('d-file').addEventListener('change', e => setDocFile(e.target.files[0]));
  ['dragenter', 'dragover'].forEach(ev => $('d-drop').addEventListener(ev, e => { e.preventDefault(); $('d-drop').classList.add('drag'); }));
  ['dragleave', 'drop'].forEach(ev => $('d-drop').addEventListener(ev, e => { e.preventDefault(); $('d-drop').classList.remove('drag'); }));
  $('d-drop').addEventListener('drop', e => { if (e.dataTransfer.files[0]) setDocFile(e.dataTransfer.files[0]); });

  $('d-form').addEventListener('submit', async e => {
    e.preventDefault();
    $('d-error').hidden = true;
    const title = $('d-title').value.trim();
    if (!title) { showDocError('Inserisci un titolo.'); return; }
    if (!DE.id && !DE.file) { showDocError('Scegli il file PDF da caricare.'); return; }
    const fd = new FormData();
    if (DE.id) fd.append('id', DE.id);
    fd.append('title', title);
    fd.append('edition', $('d-edition').value.trim());
    fd.append('description', $('d-description').value.trim());
    if (DE.file) fd.append('file', DE.file, DE.file.name);
    const bar = $('d-progress');
    bar.hidden = !DE.file;
    bar.firstElementChild.style.width = '0';
    await withBusy($('d-save'), async () => {
      try {
        await Api.upload('admin.document.save', fd, p => { bar.firstElementChild.style.width = Math.round(p * 100) + '%'; });
        await loadDocs();
        closeDoc();
        renderDocs();
        toast(DE.id ? 'Documento aggiornato' : 'Documento aggiunto');
      } catch (ex) {
        bar.hidden = true;
        showDocError(ex.message);
      }
    });
  });

  $('d-delete').addEventListener('click', async () => {
    const d = S.docs.find(x => x.id === DE.id);
    if (!d) return;
    if (!(await confirmModal('Eliminare «' + d.title + '»?', 'Il documento sparisce dal portale e il PDF viene cancellato dal server. Non si può annullare.', 'Elimina'))) return;
    try {
      await Api.post('admin.document.delete', { id: d.id });
      await loadDocs();
      closeDoc();
      renderDocs();
      toast('Documento eliminato');
    } catch (ex) { showDocError(ex.message); }
  });

  // ====================================================================
  // OVERVIEW
  // ====================================================================

  async function loadOverview() {
    try {
      S.overview = (await Api.get('admin.overview')).overview;
      S.overviewAt = Date.now();
    } catch (e) {
      S.overview = null;
    }
  }

  const pct = (part, total) => total ? Math.round(part / total * 100) : 0;
  const plural = (n, one, many) => n + ' ' + (n === 1 ? one : many);
  const fmtNum = n => Number(n || 0).toLocaleString('it-IT');

  function renderDashboard() {
    const ov = S.overview;
    const pending = S.users.filter(u => u.status === 'pending');
    const openFlags = S.flags.filter(f => !f.resolved);
    const activeQ = S.questions.filter(q => q.active).length;
    const unusedRefs = S.refs.filter(r => !r.usage).length;
    const isAdmin = S.me.canManageUsers;

    // ---------- Tiles ----------
    // [goto tab or '', value, label, sub-line, needs attention, preset]
    const tiles = [];
    if (isAdmin) tiles.push(['utenti', pending.length, pending.length === 1 ? 'Registrazione in attesa' : 'Registrazioni in attesa', plural(S.users.filter(u => u.status === 'active').length, 'utente attivo', 'utenti attivi'), pending.length > 0]);
    tiles.push(['segnalazioni', new Set(openFlags.map(f => f.code)).size, 'Domande segnalate', plural(openFlags.length, 'segnalazione aperta', 'segnalazioni aperte'), openFlags.length > 0]);
    tiles.push(['domande', activeQ, 'Domande attive', (S.questions.length - activeQ) + ' disattivate', false]);
    tiles.push(['riferimenti', S.refs.length, 'Riferimenti', unusedRefs + ' senza domande', false]);
    if (isAdmin) {
      const missing = S.docs.filter(d => !d.exists).length;
      const official = ov ? ov.official_documents.count : 0;
      tiles.push(['documenti', official + S.docs.length, 'Documenti', missing ? plural(missing, 'file mancante', 'file mancanti') : plural(S.docs.length, 'altro documento utile', 'altri documenti utili'), missing > 0]);
    }
    $('dash-tiles').innerHTML = tiles.map(([tab, v, label, sub, attn]) =>
      (tab ? '<button class="dash-tile' + (attn ? ' attention' : '') + '" data-goto="' + tab + '">' : '<div class="dash-tile static">') +
        '<div class="dash-tile-value">' + v + '</div><div class="dash-tile-label">' + label + '</div><div class="dash-tile-sub">' + sub + '</div>' +
      (tab ? '</button>' : '</div>')).join('');

    // ---------- Registrations to approve ----------
    $('dash-pending-panel').hidden = !isAdmin || !pending.length;
    const pBox = $('dash-pending');
    pBox.innerHTML = '';
    pending.slice(0, 6).forEach(u => {
      const row = document.createElement('div');
      row.className = 'dash-row';
      row.innerHTML = '<div class="dash-row-main"><strong>' + esc(u.name) + '</strong><small>' + esc(u.email) + ' · ' + fmtDate(u.created_at) + '</small></div>' +
        (u.canManage ? '<button class="btn btn-primary btn-sm">Abilita</button>' : '');
      const b = row.querySelector('button');
      if (b) b.addEventListener('click', async () => { await updateUser(u, { status: 'active' }, u.name + ' abilitato'); renderDashboard(); });
      pBox.appendChild(row);
    });

    // ---------- Latest open reports ----------
    const fBox = $('dash-flags');
    fBox.innerHTML = openFlags.length ? '' : '<div class="dash-empty">Nessuna segnalazione aperta.</div>';
    openFlags.slice(0, 5).forEach(f => {
      const q = S.questions.find(x => x['question-code'] === f.code);
      const row = document.createElement('div');
      row.className = 'dash-row';
      row.innerHTML = '<div class="dash-row-main"><strong>' + esc(f.code) + '</strong> ' + (f.text ? esc(f.text) : '<em>Nessuna descrizione</em>') +
        '<small>' + esc(f.user.name) + ' · ' + fmtIso(f.date) + (q ? ' · ' + esc(q['question-text']) : '') + '</small></div>' +
        (q ? '<button class="btn btn-secondary btn-sm">Apri</button>' : '');
      const b = row.querySelector('button');
      if (b) b.addEventListener('click', () => openQuestion(f.code));
      fBox.appendChild(row);
    });

    renderDashActivity(ov);
    renderDashChanges();
    renderDashHealth();

    // ---------- Questions by source ----------
    const counts = {};
    S.questions.forEach(q => { new Set(q['question-ref'].map(sourceOfRef)).forEach(src => { if (src) counts[src] = (counts[src] || 0) + 1; }); });
    const max = Math.max(1, ...Object.values(counts));
    $('dash-sources').innerHTML = S.sources.map(src =>
      '<div class="dash-bar ' + (SOURCE_CLASS[src] || '') + '"><span>' + esc(src) + '</span><span class="dash-bar-track"><span style="width:' + Math.round((counts[src] || 0) / max * 100) + '%"></span></span><span class="dash-bar-num">' + (counts[src] || 0) + '</span></div>').join('');

    renderDashDocs(ov);

    // Refresh the aggregated data when it is older than a minute
    if (!S.overviewLoading && (!S.overviewAt || Date.now() - S.overviewAt > 60000)) {
      S.overviewLoading = true;
      loadOverview().then(() => {
        S.overviewLoading = false;
        if (!$('sec-panoramica').hidden && S.overview) renderDashboard();
      });
    }
  }

  // Study activity, accuracy by source and hardest references (aggregated, from admin.overview)
  function renderDashActivity(ov) {
    if (!ov) {
      const msg = S.overviewAt ? '<div class="dash-empty">Dati non disponibili.</div>' : '<div class="dash-empty">Caricamento…</div>';
      ['dash-activity', 'dash-accuracy', 'dash-hard'].forEach(id => { $(id).innerHTML = msg; });
      return;
    }
    const a = ov.activity;
    const cells = [
      [ov.users.seen7, 'attivi in 7 giorni', ov.users.seen30 + ' in 30 giorni'],
      [ov.users.new30, 'nuovi iscritti', 'ultimi 30 giorni'],
      [a.learners, a.learners === 1 ? 'utente ha risposto' : 'utenti hanno risposto', 'su ' + ov.users.active + ' attivi'],
      [fmtNum(a.answers), 'risposte date', a.answers ? pct(a.correct, a.answers) + '% corrette' : '—'],
      [pct(a.refs_practiced, S.refs.length) + '%', 'riferimenti esercitati', a.refs_practiced + ' su ' + S.refs.length],
      [a.quiz_in_progress + a.suspended_quizzes, 'quiz da finire', a.quiz_in_progress + ' in corso · ' + a.suspended_quizzes + ' sospesi'],
      // saved questions and references are shown in the panel footer
    ];
    $('dash-activity').innerHTML = '<div class="dash-stats">' + cells.map(([v, l, sub]) =>
      '<div class="dash-stat"><div class="dash-stat-value">' + v + '</div><div class="dash-stat-label">' + l + '</div><div class="dash-stat-sub">' + esc(sub) + '</div></div>').join('') + '</div>' +
      '<div class="dash-foot">Salvate dagli utenti: ' + plural(a.saved.questions, 'domanda', 'domande') + ' e ' + plural(a.saved.refs, 'riferimento', 'riferimenti') + '.</div>';

    const srcs = S.sources.filter(src => ov.by_source[src]);
    const anyAnswers = srcs.some(src => ov.by_source[src].correct + ov.by_source[src].wrong > 0);
    $('dash-accuracy').innerHTML = !anyAnswers ? '<div class="dash-empty">Nessuna risposta ancora.</div>' :
      srcs.map(src => {
        const b = ov.by_source[src], n = b.correct + b.wrong, p = pct(b.correct, n);
        return '<div class="dash-bar ' + (SOURCE_CLASS[src] || '') + '" title="' + b.correct + ' corrette su ' + n + '"><span>' + esc(src) + '</span>' +
          '<span class="dash-bar-track"><span style="width:' + (n ? p : 0) + '%"></span></span>' +
          '<span class="dash-bar-num">' + (n ? p + '%' : '—') + '</span></div>';
      }).join('') + '<div class="dash-foot">Percentuale di risposte corrette sui riferimenti di ogni fonte.</div>';

    const hBox = $('dash-hard');
    hBox.innerHTML = ov.hard_refs.length ? '' : '<div class="dash-empty">Ancora pochi dati: servono almeno 3 risposte su un riferimento.</div>';
    ov.hard_refs.forEach(r => {
      const row = document.createElement('button');
      row.type = 'button';
      row.className = 'dash-row dash-row-btn';
      row.innerHTML = '<span class="ref-source-tag ' + (SOURCE_CLASS[r.source] || 'rg') + '">' + esc(SOURCE_SHORT[r.source] || '') + '</span>' +
        '<div class="dash-row-main"><strong>' + esc(r.code) + '</strong> ' + esc(r.title) +
        '<small>' + plural(r.answers, 'risposta', 'risposte') + ' · ' + plural(r.users, 'utente ha sbagliato', 'utenti hanno sbagliato') + '</small></div>' +
        '<span class="dash-pct ' + (r.pct < 50 ? 'low' : r.pct < 75 ? 'mid' : 'ok') + '">' + r.pct + '%</span>';
      row.addEventListener('click', () => openRef(r.code));
      hBox.appendChild(row);
    });
  }

  // Rulebook changes of the current season
  function renderDashChanges() {
    const cur = S.refs.filter(r => r['ref-change'] && r['ref-change'].season === CURRENT_SEASON);
    $('dash-changes-panel').hidden = !cur.length;
    if (!cur.length) return;
    $('dash-changes-title').textContent = 'Novità della stagione ' + CURRENT_SEASON;
    const codes = new Set(cur.map(r => r['ref-code']));
    const nNew = cur.filter(r => r['ref-change'].type === 'new').length;
    const linkedQ = S.questions.filter(q => q.active && q['question-ref'].some(rc => codes.has(rc))).length;
    const noQ = cur.filter(r => !r.usage).length;
    let html = '<div class="dash-chips">' +
      '<span class="dash-chip"><strong>' + nNew + '</strong> ' + (nNew === 1 ? 'nuova' : 'nuove') + '</span>' +
      '<span class="dash-chip"><strong>' + (cur.length - nNew) + '</strong> ' + (cur.length - nNew === 1 ? 'modificata' : 'modificate') + '</span>' +
      '<span class="dash-chip"><strong>' + linkedQ + '</strong> ' + (linkedQ === 1 ? 'domanda collegata' : 'domande collegate') + '</span>' +
      (noQ ? '<span class="dash-chip warn"><strong>' + noQ + '</strong> senza domande</span>' : '') +
      '</div>';
    $('dash-changes').innerHTML = html;
    cur.slice(0, 5).forEach(r => {
      const row = document.createElement('button');
      row.type = 'button';
      row.className = 'dash-row dash-row-btn';
      row.innerHTML = '<div class="dash-row-main"><strong>' + esc(r['ref-code']) + '</strong> ' + esc(r['ref-title']) +
        (r['ref-change'].note ? '<small>' + esc(r['ref-change'].note) + '</small>' : '') + '</div>' + changeBadge(r['ref-change']);
      row.addEventListener('click', () => openRef(r['ref-code']));
      $('dash-changes').appendChild(row);
    });
  }

  // Content health: things worth a look, plus how questions are spread by type and difficulty
  function renderDashHealth() {
    const unused = S.refs.filter(r => !r.usage).length;
    const inactive = S.questions.filter(q => !q.active).length;
    const broken = S.questions.filter(q => q['question-ref'].some(rc => !S.refMap[rc])).length;
    const noRef = S.questions.filter(q => !q['question-ref'].length).length;
    const items = [
      ['riferimenti', 'unused', unused, 'Riferimenti senza domande', 'info'],
      ['domande', 'inactive', inactive, 'Domande disattivate', 'info'],
      ['domande', 'noref', broken + noRef, 'Domande con riferimenti mancanti', 'warn'],
    ];
    let html = items.map(([tab, preset, n, label, kind]) =>
      '<button type="button" class="dash-row dash-row-btn" data-goto="' + tab + '" data-preset="' + preset + '"' + (n ? '' : ' disabled') + '>' +
        '<div class="dash-row-main">' + label + '</div>' +
        '<span class="dash-count ' + (n ? kind : 'ok') + '">' + (n ? n : '✓') + '</span></button>').join('');

    const total = S.questions.length || 1;
    const byDiff = ['e', 'm', 'd', 'dd'].map(k => [k, S.questions.filter(q => q['question-difficulty'] === k).length]);
    html += '<div class="dash-sub">Per difficoltà</div><div class="dash-stack">' +
      byDiff.map(([k, n]) => n ? '<span class="diff-' + k + '" style="flex:' + n + '" title="' + DIFF_LABEL[k] + ': ' + n + '"></span>' : '').join('') + '</div>' +
      '<div class="dash-legend">' + byDiff.map(([k, n]) => '<span><i class="diff-' + k + '"></i>' + DIFF_LABEL[k] + ' ' + pct(n, total) + '%</span>').join('') + '</div>';
    const byType = Object.keys(TYPE_LABEL).map(k => [k, S.questions.filter(q => q['question-type'] === k).length]);
    html += '<div class="dash-sub">Per tipo</div><div class="dash-chips">' +
      byType.map(([k, n]) => '<span class="dash-chip"><strong>' + n + '</strong> ' + TYPE_LABEL[k] + '</span>').join('') + '</div>';
    $('dash-health').innerHTML = html;
  }

  // Documents (administrators only)
  function renderDashDocs(ov) {
    $('dash-docs-panel').hidden = !S.me.canManageUsers;
    if (!S.me.canManageUsers) return;
    let html = '';
    if (ov) {
      html += '<div class="dash-row"><span class="d-icon sm" aria-hidden="true">PDF</span><div class="dash-row-main"><strong>Documenti ufficiali</strong>' +
        '<small>' + plural(ov.official_documents.count, 'file', 'file') + ' · ' + fmtBytes(ov.official_documents.size) + ' · fissi, da cui sono tratte le domande</small></div></div>';
    }
    if (!S.docs.length) html += '<div class="dash-empty">Nessun «altro documento utile» caricato.</div>';
    S.docs.forEach(d => {
      html += '<div class="dash-row"><span class="d-icon sm" aria-hidden="true">PDF</span><div class="dash-row-main"><strong>' + esc(d.title) + '</strong>' +
        '<small>' + (d.size ? fmtBytes(d.size) + ' · ' : '') + 'aggiornato il ' + fmtDate(d.updated_at) + '</small></div>' +
        (d.exists ? '' : '<span class="pill red">File mancante</span>') + '</div>';
    });
    $('dash-docs').innerHTML = html;
  }

  // Shortcuts from the Overview that open a section with a filter already set
  function applyPreset(tab, preset) {
    if (tab === 'riferimenti') {
      $('r-search').value = '';
      S.rSources.clear();
      document.querySelectorAll('#r-filters .filter-chip').forEach(c => c.classList.remove('active'));
      $('r-unused').checked = preset === 'unused';
      $('r-filter-change').value = preset === 'changes' ? 'current' : '';
      S.rLimit = PAGE;
    }
    if (tab === 'domande') {
      $('q-search').value = '';
      ['q-filter-source', 'q-filter-type', 'q-filter-diff'].forEach(id => { $(id).value = ''; });
      $('q-filter-active').value = preset === 'inactive' ? '0' : preset === 'noref' ? 'noref' : '';
      S.qLimit = PAGE;
    }
  }

  document.addEventListener('click', e => {
    const g = e.target.closest('[data-goto]');
    if (!g || g.disabled) return;
    if (g.dataset.preset) applyPreset(g.dataset.goto, g.dataset.preset);
    showTab(g.dataset.goto);
  });

  // ====================================================================
  // STARTUP
  // ====================================================================

  Api.onUnauthenticated(() => { window.location.href = './'; });
  // The "Fischietto Gestione" title leads to the panel home (Overview)
  $('admin-home').addEventListener('click', e => { e.preventDefault(); showTab('panoramica'); });

  // Back: return to the portal page you came from (e.g. #profilo), otherwise to the portal home
  $('admin-back').addEventListener('click', e => {
    try {
      const ref = new URL(document.referrer);
      const portal = new URL('./', location.href);
      if (ref.origin === location.origin && ref.pathname === portal.pathname && history.length > 1) { e.preventDefault(); history.back(); }
    } catch (err) { /* no referrer: follow the link */ }
  });
  if (window.APP_VERSION_LABEL) $('admin-version').textContent = window.APP_VERSION_LABEL;

  (async () => {
    try {
      const me = await Api.me();
      if (!me.user || !me.user.canEdit || me.user.mustChangePassword) { window.location.replace('./'); return; }
      S.me = me.user;
      $('admin-user-name').textContent = S.me.name;
      Avatar.apply($('admin-avatar'), S.me.name, S.me.gravatar, { size: 36 });
      const role = $('admin-user-role');
      role.textContent = S.me.role;
      role.classList.add(roleClass(S.me.permissions));
      $('tab-utenti').hidden = !S.me.canManageUsers;
      $('tab-documenti').hidden = !S.me.canManageUsers;
      if (S.me.isMaster) $('users-intro').textContent = 'Abilita le nuove registrazioni, assegna i ruoli (anche amministratore) e reimposta le password.';
      else if (S.me.canManageUsers) $('users-intro').textContent = 'Abilita le nuove registrazioni e gestisci utenti e moderatori. Gli amministratori sono gestiti dal master admin.';
      $('boot-message').textContent = 'Carico domande, riferimenti e utenti…';
      await Promise.all([loadContent(), loadFlags(), loadUsers(), loadDocs(), loadOverview()]);
      initRefFilters();
      $('boot-screen').hidden = true;
      $('admin-root').hidden = false;
      showTab(location.hash.slice(1));
    } catch (e) {
      $('boot-message').textContent = e.message || 'Errore durante il caricamento.';
    }
  })();
})();
