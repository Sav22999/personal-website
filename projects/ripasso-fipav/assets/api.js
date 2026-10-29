/* API client shared by the portal and the management panel. */
(function () {
  'use strict';

  const API_URL = 'api/index.php';
  let csrfToken = null;
  let onUnauthenticated = null;

  class ApiError extends Error {
    constructor(status, data) {
      super((data && data.message) || ('Errore ' + status));
      this.status = status;
      this.code = (data && data.error) || 'error';
      this.data = data || {};
    }
  }

  /** Builds the API URL: "name" or "name&param=value" (only the action name is encoded). */
  function apiUrl(route) {
    const [name, ...rest] = String(route).split('&');
    return API_URL + '?r=' + encodeURIComponent(name) + (rest.length ? '&' + rest.join('&') : '');
  }

  async function request(route, body, opts) {
    opts = opts || {};
    const headers = { 'Accept': 'application/json' };
    const init = { method: body === undefined ? 'GET' : 'POST', headers, credentials: 'same-origin', cache: 'no-cache' };
    if (body !== undefined) {
      headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(body);
      if (csrfToken) headers['X-CSRF-Token'] = csrfToken;
      if (opts.keepalive) init.keepalive = true;
    }
    let res;
    try {
      res = await fetch(apiUrl(route), init);
    } catch (e) {
      throw new ApiError(0, { error: 'network', message: 'Connessione non disponibile. Controlla la rete e riprova.' });
    }
    let data = null;
    try { data = await res.json(); } catch (e) { data = null; }
    if (!res.ok || !data || data.ok === false) {
      const err = new ApiError(res.status, data);
      if (res.status === 401 && onUnauthenticated && !opts.silentAuth) onUnauthenticated(err);
      throw err;
    }
    if (data.csrf) csrfToken = data.csrf;
    return data;
  }

  const Api = {
    ApiError,
    get: (route, opts) => request(route, undefined, opts),
    post: (route, body, opts) => request(route, body || {}, opts),
    setCsrf: t => { csrfToken = t; },
    onUnauthenticated: fn => { onUnauthenticated = fn; },
    me: () => request('auth.me', undefined, { silentAuth: true }),
    login: (email, password) => request('auth.login', { email, password }, { silentAuth: true }),
    register: (name, email, password) => request('auth.register', { name, email, password }, { silentAuth: true }),
    logout: () => request('auth.logout', {}, { silentAuth: true }),
    /** File upload (multipart) with progress: onProgress receives a value from 0 to 1. */
    upload(route, formData, onProgress) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', apiUrl(route));
        xhr.withCredentials = true;
        xhr.setRequestHeader('Accept', 'application/json');
        if (csrfToken) xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        if (onProgress) xhr.upload.onprogress = e => { if (e.lengthComputable) onProgress(e.loaded / e.total); };
        xhr.onload = () => {
          let data = null;
          try { data = JSON.parse(xhr.responseText); } catch (e) { data = null; }
          if (xhr.status >= 200 && xhr.status < 300 && data && data.ok !== false) { resolve(data); return; }
          if (!data && xhr.status === 413) data = { error: 'too_large', message: 'Il PDF è troppo grande per il server.' };
          const err = new ApiError(xhr.status, data);
          if (xhr.status === 401 && onUnauthenticated) onUnauthenticated(err);
          reject(err);
        };
        xhr.onerror = () => reject(new ApiError(0, { error: 'network', message: 'Connessione non disponibile. Controlla la rete e riprova.' }));
        xhr.send(formData);
      });
    },
  };

  /**
   * Store: replaces localStorage for personal data.
   * Synchronous reads from a cache loaded at startup, debounced writes to the server.
   */
  const KEY_MAP = { 'fipav-data': 'data', 'fipav-suspended': 'suspended', 'fipav-autosave': 'autosave' };
  const cache = {};
  const dirty = new Set();
  let timer = null;
  let inFlight = null;
  let retryDelay = 2000;
  let listeners = [];

  function mapKey(k) { return KEY_MAP[k] || k; }
  function notify(state) { listeners.forEach(fn => { try { fn(state); } catch (e) {} }); }

  const Store = {
    load(state) {
      Object.keys(cache).forEach(k => delete cache[k]);
      Object.keys(state || {}).forEach(k => { cache[k] = state[k]; });
    },
    getItem(key) { const k = mapKey(key); return Object.prototype.hasOwnProperty.call(cache, k) ? cache[k] : null; },
    setItem(key, value) { const k = mapKey(key); cache[k] = String(value); dirty.add(k); this.schedule(); },
    removeItem(key) { const k = mapKey(key); delete cache[k]; dirty.add(k); this.schedule(); },
    hasPending() { return dirty.size > 0 || !!inFlight; },
    onStatus(fn) { listeners.push(fn); },
    schedule(delay) {
      clearTimeout(timer);
      timer = setTimeout(() => this.flush(), delay === undefined ? 800 : delay);
    },
    async flush(keepalive) {
      if (dirty.size === 0) return;
      if (inFlight && !keepalive) { await inFlight.catch(() => {}); if (dirty.size === 0) return; }
      clearTimeout(timer);
      const keys = Array.from(dirty);
      const items = {};
      keys.forEach(k => { items[k] = Object.prototype.hasOwnProperty.call(cache, k) ? cache[k] : null; });
      keys.forEach(k => dirty.delete(k));
      notify('saving');
      const payloadSize = JSON.stringify(items).length;
      const p = Api.post('state.set', { items }, { keepalive: !!keepalive && payloadSize < 60000 });
      inFlight = p;
      try {
        await p;
        retryDelay = 2000;
        notify(dirty.size ? 'saving' : 'saved');
      } catch (e) {
        // Re-queue the keys that were not saved (unless they were rewritten in the meantime)
        keys.forEach(k => dirty.add(k));
        notify(e.status === 401 ? 'auth' : 'error');
        if (e.status !== 401) {
          this.schedule(retryDelay);
          retryDelay = Math.min(retryDelay * 2, 60000);
        }
      } finally {
        if (inFlight === p) inFlight = null;
      }
    },
  };

  // Save immediately when the page is hidden or closed
  document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'hidden') Store.flush(true); });
  window.addEventListener('pagehide', () => Store.flush(true));
  window.addEventListener('beforeunload', e => {
    if (dirty.size > 0) { Store.flush(true); e.preventDefault(); e.returnValue = ''; }
  });

  function escapeHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }

  /**
   * Avatar: shows the Gravatar image when there is one, otherwise the initials.
   * d=404 makes Gravatar answer "not found" when no image is set.
   */
  const gravatarCache = {};
  const Avatar = {
    initials(name) {
      return String(name || '?').split(/\s+/).filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase() || '?';
    },
    apply(el, name, hash, opts) {
      if (!el) return;
      opts = opts || {};
      const size = opts.size || 64;
      el.classList.remove('has-img');
      el.textContent = Avatar.initials(name);
      if (!hash) return;
      const src = 'https://gravatar.com/avatar/' + hash + '?s=' + (size * 2) + '&d=404' + (opts.refresh ? '&r=g&_=' + Date.now() : '&r=g');
      const key = hash + ':' + size;
      if (gravatarCache[key] === false && !opts.refresh) return;
      const img = new Image();
      img.alt = '';
      img.decoding = 'async';
      img.referrerPolicy = 'no-referrer';
      img.onload = () => {
        gravatarCache[key] = true;
        el.textContent = '';
        el.appendChild(img);
        el.classList.add('has-img');
        if (opts.onResult) opts.onResult(true);
      };
      img.onerror = () => { gravatarCache[key] = false; if (opts.onResult) opts.onResult(false); };
      img.src = src;
    },
  };

  /**
   * Infinite scroll: calls loadMore() while the sentinel element is near the viewport.
   * loadMore() returns false when there is nothing left to load.
   * watch() returns a check function: call it after re-rendering a list, because an
   * IntersectionObserver does not fire again if the sentinel simply stays visible.
   */
  const InfiniteList = {
    watch(sentinel, loadMore, margin = 600) {
      let busy = false;
      const near = () => !sentinel.hidden && sentinel.offsetParent !== null &&
        sentinel.getBoundingClientRect().top < window.innerHeight + margin;
      const check = () => {
        if (busy) return;
        busy = true;
        requestAnimationFrame(() => {
          busy = false;
          if (near() && loadMore() !== false) check();
        });
      };
      if ('IntersectionObserver' in window) {
        new IntersectionObserver(entries => { if (entries.some(e => e.isIntersecting)) check(); },
          { rootMargin: '0px 0px ' + margin + 'px 0px' }).observe(sentinel);
      } else {
        window.addEventListener('scroll', check, { passive: true });
      }
      window.addEventListener('resize', check, { passive: true });
      return check;
    },
  };

  window.Api = Api;
  window.InfiniteList = InfiniteList;
  window.Avatar = Avatar;
  window.Store = Store;
  window.escapeHtml = escapeHtml;
})();
