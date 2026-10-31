/* Startup: checks the session, shows sign-in/registration and the mandatory password change, loads data and the portal. */
(function () {
  'use strict';

  const $ = id => document.getElementById(id);
  const bootScreen = $('boot-screen');
  const authScreen = $('auth-screen');
  const appRoot = $('app-root');
  let appStarted = false;
  let tempPassword = null;     // temporary password just used to sign in (kept in memory only)
  let pendingUser = null;      // user who must change the password before entering

  // ---------- Installable web app ----------
  // The browser's install prompt is captured as early as possible and kept for the "Install" buttons.
  const Install = {
    deferred: null,
    listeners: [],
    isStandalone() {
      return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    },
    isIOS() {
      return /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    },
    /** 'installed' | 'prompt' (native dialog available) | 'ios' (manual steps) | 'manual' (browser menu) */
    mode() {
      if (this.isStandalone()) return 'installed';
      if (this.deferred) return 'prompt';
      if (this.isIOS()) return 'ios';
      return 'manual';
    },
    async prompt() {
      if (!this.deferred) return false;
      const ev = this.deferred;
      this.deferred = null;
      ev.prompt();
      const choice = await ev.userChoice.catch(() => ({}));
      this.notify();
      return choice && choice.outcome === 'accepted';
    },
    onChange(fn) { this.listeners.push(fn); fn(this.mode()); },
    notify() { this.listeners.forEach(fn => { try { fn(this.mode()); } catch (e) {} }); },
  };
  window.Install = Install;
  window.addEventListener('beforeinstallprompt', e => { e.preventDefault(); Install.deferred = e; Install.notify(); });
  window.addEventListener('appinstalled', () => { Install.deferred = null; Install.notify(); });
  if ('serviceWorker' in navigator && window.isSecureContext) {
    window.addEventListener('load', () => { navigator.serviceWorker.register('sw.js').catch(() => {}); });
  }

  // Version shown on the sign-in screen too
  if (window.APP_VERSION_LABEL && $('auth-version')) $('auth-version').textContent = window.APP_VERSION_LABEL;

  // ---------- Password rules and strength meter ----------

  const PwStrength = {
    MIN: 10,
    checks(pw) {
      return {
        length: pw.length >= this.MIN,
        kinds: [/\p{Ll}/u, /\p{Lu}/u, /\d/, /[^\p{L}\d]/u].filter(r => r.test(pw)).length >= 3,
      };
    },
    problem(pw, user) {
      const c = this.checks(pw);
      if (!c.length) return 'La password deve avere almeno ' + this.MIN + ' caratteri.';
      if (!c.kinds) return 'Usa almeno tre tipi di caratteri tra minuscole, maiuscole, numeri e simboli.';
      const low = pw.toLowerCase();
      if (user && user.email) {
        const local = user.email.split('@')[0].toLowerCase();
        if (local.length >= 4 && low.includes(local)) return 'La password non può contenere il tuo indirizzo email.';
      }
      if (user && user.name) {
        const part = user.name.toLowerCase().split(/\s+/).find(p => p.length >= 4 && low.includes(p));
        if (part) return 'La password non può contenere il tuo nome.';
      }
      if (/(password|qwerty|123456|fipav|arbitro|pallavolo)/i.test(pw) || /^(.)\1+$/.test(pw)) return 'Password troppo facile da indovinare: scegline una meno prevedibile.';
      return '';
    },
    level(pw) {
      if (!pw) return 0;
      const kinds = [/\p{Ll}/u, /\p{Lu}/u, /\d/, /[^\p{L}\d]/u].filter(r => r.test(pw)).length;
      let score = 0;
      if (pw.length >= 10) score++;
      if (pw.length >= 14) score++;
      if (kinds >= 3) score++;
      if (kinds === 4 || pw.length >= 18) score++;
      return Math.max(1, Math.min(4, score));
    },
    render(input) {
      const box = $(input.dataset.strength);
      if (!box) return;
      const pw = input.value;
      box.classList.toggle('show', pw.length > 0);
      const c = this.checks(pw);
      box.innerHTML = '<div class="pw-meter" data-level="' + this.level(pw) + '"><span></span><span></span><span></span><span></span></div>' +
        '<ul class="pw-rules"><li class="' + (c.length ? 'ok' : '') + '">Almeno ' + this.MIN + ' caratteri</li>' +
        '<li class="' + (c.kinds ? 'ok' : '') + '">3 tipi tra minuscole, maiuscole, numeri, simboli</li></ul>';
    },
    reset(id) { const el = $(id); if (el) { el.value = ''; this.render(el); } },
  };
  window.PwStrength = PwStrength;

  document.querySelectorAll('input[data-strength]').forEach(inp => inp.addEventListener('input', () => PwStrength.render(inp)));
  document.querySelectorAll('[data-toggle-pw]').forEach(btn => btn.addEventListener('click', () => {
    const inp = $(btn.dataset.togglePw);
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    btn.classList.toggle('on', show);
    btn.setAttribute('aria-label', show ? 'Nascondi password' : 'Mostra password');
  }));

  // ---------- Sign-in screen ----------

  function setAuthMessage(text, kind) {
    const el = $('auth-msg');
    el.hidden = !text;
    el.textContent = text || '';
    el.className = 'form-msg ' + (kind || 'error');
  }

  function showPanel(id) {
    ['auth-forms', 'auth-forgot', 'auth-force', 'auth-pending'].forEach(p => { $(p).hidden = p !== id; });
  }

  function switchAuthTab(tab) {
    showPanel('auth-forms');
    document.querySelectorAll('.auth-tab').forEach(b => {
      const on = b.dataset.authTab === tab;
      b.classList.toggle('active', on);
      b.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    $('login-form').hidden = tab !== 'login';
    $('register-form').hidden = tab !== 'register';
    setAuthMessage('');
    const first = tab === 'login' ? $('login-email') : $('reg-name');
    setTimeout(() => first.focus(), 0);
  }

  function showPending(title, text) {
    showPanel('auth-pending');
    $('auth-pending-title').textContent = title;
    $('auth-pending-text').textContent = text;
  }

  function showAuth(asOverlay) {
    bootScreen.hidden = true;
    authScreen.classList.toggle('overlay', !!asOverlay);
    authScreen.hidden = false;
    switchAuthTab('login');
    if (asOverlay) {
      setAuthMessage('La sessione è scaduta: accedi di nuovo per continuare. I progressi non ancora salvati verranno inviati dopo l\'accesso.', 'info');
      if (window.APP_USER) $('login-email').value = window.APP_USER.email;
      $('login-password').focus();
    } else {
      appRoot.hidden = true;
    }
  }

  function showForceChange(user) {
    pendingUser = user;
    bootScreen.hidden = true;
    appRoot.hidden = true;
    authScreen.classList.remove('overlay');
    authScreen.hidden = false;
    showPanel('auth-force');
    $('force-msg').hidden = true;
    $('force-current-field').hidden = !!tempPassword;
    PwStrength.reset('force-new');
    $('force-new2').value = '';
    setTimeout(() => (tempPassword ? $('force-new') : $('force-current')).focus(), 0);
  }

  function setBusy(form, busy) {
    const btn = form.querySelector('.auth-submit');
    btn.disabled = busy;
    if (busy) { btn.dataset.label = btn.textContent; btn.textContent = 'Attendi…'; }
    else if (btn.dataset.label) btn.textContent = btn.dataset.label;
  }

  document.querySelectorAll('.auth-tab').forEach(b => b.addEventListener('click', () => switchAuthTab(b.dataset.authTab)));
  document.querySelectorAll('[data-auth-back]').forEach(b => b.addEventListener('click', () => switchAuthTab('login')));
  $('btn-forgot').addEventListener('click', () => showPanel('auth-forgot'));

  $('login-form').addEventListener('submit', async e => {
    e.preventDefault();
    const email = $('login-email').value.trim();
    const password = $('login-password').value;
    if (!email || !password) { setAuthMessage('Inserisci email e password.'); return; }
    setBusy(e.target, true);
    setAuthMessage('');
    try {
      const res = await Api.login(email, password);
      $('login-password').value = '';
      if (res.user.mustChangePassword) {
        tempPassword = password;
        showForceChange(res.user);
        return;
      }
      if (appStarted) {
        if (window.APP_USER && res.user.id === window.APP_USER.id) {
          authScreen.hidden = true;
          authScreen.classList.remove('overlay');
          window.Store.flush();
        } else {
          window.location.reload();
        }
      } else {
        await startApp(res.user);
      }
    } catch (err) {
      if (!appStarted) { authScreen.hidden = false; bootScreen.hidden = true; }
      if (err.code === 'pending') showPending('Account in attesa', err.message);
      else if (err.code === 'disabled') showPending('Account disabilitato', err.message);
      else setAuthMessage(err.message);
    } finally {
      setBusy(e.target, false);
    }
  });

  $('register-form').addEventListener('submit', async e => {
    e.preventDefault();
    const name = $('reg-name').value.trim();
    const email = $('reg-email').value.trim();
    const pw = $('reg-password').value;
    const pw2 = $('reg-password2').value;
    if (name.length < 2) { setAuthMessage('Inserisci nome e cognome.'); return; }
    if (!/^\S+@\S+\.\S+$/.test(email)) { setAuthMessage('Inserisci un indirizzo email valido.'); return; }
    const problem = PwStrength.problem(pw, { name, email });
    if (problem) { setAuthMessage(problem); return; }
    if (pw !== pw2) { setAuthMessage('Le due password non coincidono.'); return; }
    setBusy(e.target, true);
    setAuthMessage('');
    try {
      const res = await Api.register(name, email, pw);
      e.target.reset();
      PwStrength.reset('reg-password');
      $('login-email').value = email;
      showPending('Registrazione completata', res.message);
    } catch (err) {
      setAuthMessage(err.message);
    } finally {
      setBusy(e.target, false);
    }
  });

  // ---------- Mandatory password change ----------

  $('auth-force').addEventListener('submit', async e => {
    e.preventDefault();
    const msg = $('force-msg');
    const show = t => { msg.textContent = t; msg.hidden = false; };
    msg.hidden = true;
    const current = tempPassword || $('force-current').value;
    const next = $('force-new').value;
    if (!current) { show('Inserisci la password temporanea che hai ricevuto.'); return; }
    const problem = PwStrength.problem(next, pendingUser);
    if (problem) { show(problem); return; }
    if (next !== $('force-new2').value) { show('Le due password non coincidono.'); return; }
    if (next === current) { show('La nuova password deve essere diversa da quella temporanea.'); return; }
    setBusy(e.target, true);
    try {
      const res = await Api.post('auth.password', { current, new: next });
      tempPassword = null;
      $('force-current').value = '';
      await startApp(res.user);
      setTimeout(() => { if (window.showToast) window.showToast('Password aggiornata'); }, 300);
    } catch (err) {
      if (err.code === 'bad_current') { tempPassword = null; $('force-current-field').hidden = false; }
      show(err.message);
    } finally {
      setBusy(e.target, false);
    }
  });

  $('force-logout').addEventListener('click', async () => {
    try { await Api.logout(); } catch (e) {}
    window.location.reload();
  });

  // ---------- Portal startup ----------

  function loadScript(src) {
    return new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = () => reject(new Error('Impossibile caricare ' + src));
      document.body.appendChild(s);
    });
  }

  function showBootError(message) {
    bootScreen.hidden = false;
    authScreen.hidden = true;
    bootScreen.innerHTML = '';
    const p = document.createElement('p');
    p.textContent = message;
    p.style.maxWidth = '360px';
    const btn = document.createElement('button');
    btn.className = 'btn btn-secondary';
    btn.textContent = 'Riprova';
    btn.addEventListener('click', () => window.location.reload());
    bootScreen.append(p, btn);
  }

  async function startApp(user) {
    authScreen.hidden = true;
    bootScreen.hidden = false;
    $('boot-message').textContent = 'Carico domande e progressi…';
    const [content, state] = await Promise.all([Api.get('content'), Api.get('state.get')]);
    window.APP_USER = user;
    window.APP_CONTENT = content;
    window.Store.load(state.state || {});
    appRoot.hidden = false;
    await loadScript('assets/app.js?v=3.3.2');
    appStarted = true;
    bootScreen.hidden = true;
  }

  Api.onUnauthenticated(() => { if (appStarted) showAuth(true); });

  (async () => {
    try {
      const me = await Api.me();
      if (me.user && me.user.canUse && me.user.mustChangePassword) showForceChange(me.user);
      else if (me.user && me.user.canUse) await startApp(me.user);
      else showAuth(false);
    } catch (err) {
      if (err.code === 'must_change_password') showForceChange(window.APP_USER || null);
      else if (err.status === 401 || err.status === 403) showAuth(false);
      else showBootError(err.message || 'Errore durante il caricamento.');
    }
  })();
})();
