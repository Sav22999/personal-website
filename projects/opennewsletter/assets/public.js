/**
 * OpenNewsletter — the forms of the public pages (subscribe.php, unsubscribe.php). No framework, no modules: it also
 * runs inside the widget's iframe on other sites, where it tells the parent page its height (postMessage).
 */
(function () {
    'use strict';
    var root = document.getElementById('pub');
    if (!root) return;
    var S = {};
    try { S = JSON.parse(root.getAttribute('data-strings') || '{}'); } catch (e) { /* defaults below */ }
    var embedded = document.documentElement.getAttribute('data-embed') === '1';

    function api(route, body) {
        return fetch('api/?r=' + route, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(body), credentials: 'omit'})
            .then(function (r) { return r.json().catch(function () { return {ok: false, error: 'server'}; }); })
            .catch(function () { return {ok: false, error: 'network'}; });
    }

    function message(code) {
        return {email_invalid: S.err_email, accept_required: S.err_accept, rate_limited: S.err_rate, token_invalid: S.link_invalid, not_ready: S.not_ready}[code] || S.err_generic || 'Error';
    }

    function step(name) {
        root.querySelectorAll('section[data-step]').forEach(function (s) { s.hidden = s.getAttribute('data-step') !== name; });
        resize();
    }

    function showError(box, code) {
        var p = box.querySelector('.pub-error');
        if (!p) return;
        p.textContent = code ? message(code) : '';
        p.hidden = !code;
        resize();
    }

    function busy(form, on) {
        var b = form.querySelector('button[type=submit], button[data-confirm]');
        if (b) { b.disabled = on; b.classList.toggle('is-busy', on); }
    }

    // ---- subscribe
    var sub = root.querySelector('form[data-group]');
    var preview = document.documentElement.getAttribute('data-preview') === '1';
    if (sub) {
        sub.addEventListener('submit', function (ev) {
            ev.preventDefault();
            if (preview) return; // live preview in the settings: nothing is sent
            var f = sub.elements;
            var email = f.email.value.trim();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError(sub, 'email_invalid'); f.email.focus(); return; }
            if (!f.acceptPlatform.checked || (f.acceptGroup && !f.acceptGroup.checked)) { showError(sub, 'accept_required'); return; }
            showError(sub, null);
            busy(sub, true);
            api('pub.subscribe', {
                group: sub.getAttribute('data-group'), email: email, lang: f.lang.value, website: f.website.value,
                acceptPlatform: true, acceptGroup: !!(f.acceptGroup && f.acceptGroup.checked),
                browserLang: (navigator.languages && navigator.languages[0]) || navigator.language || '', source: sub.getAttribute('data-source'),
            }).then(function (r) {
                busy(sub, false);
                if (r.ok) step('sent'); else showError(sub, r.error);
            });
        });
        var toggle = root.querySelector('[data-toggle-terms]');
        if (toggle) toggle.addEventListener('click', function (ev) {
            ev.preventDefault();
            var t = root.querySelector('.pub-terms');
            t.hidden = !t.hidden;
            resize();
        });
    }

    // ---- confirm the subscription
    var conf = root.querySelector('button[data-confirm]');
    if (conf) {
        conf.addEventListener('click', function () {
            var box = conf.closest('section');
            busy(box, true);
            api('pub.confirm', {token: conf.getAttribute('data-confirm')}).then(function (r) {
                busy(box, false);
                if (!r.ok) { showError(box, r.error); return; }
                var p = root.querySelector('[data-done-text]');
                p.textContent = p.getAttribute('data-done-text').replace('{email}', r.email || '');
                step('done');
            });
        });
    }

    // ---- unsubscribe (with the link's token, or by typing the address)
    var un = root.querySelector('form[data-unsub-token], form[data-unsub-group]');
    if (un) {
        un.addEventListener('submit', function (ev) {
            ev.preventDefault();
            var f = un.elements;
            var reason = un.querySelector('input[name=reason]:checked');
            var body = {reason: reason ? reason.value : '', note: f.note.value};
            var route = 'pub.unsubscribe';
            if (un.hasAttribute('data-unsub-token')) body.token = un.getAttribute('data-unsub-token');
            else {
                var email = f.email.value.trim();
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError(un, 'email_invalid'); return; }
                body.email = email;
                body.group = un.getAttribute('data-unsub-group');
                route = 'pub.unsubRequest';
            }
            showError(un, null);
            busy(un, true);
            api(route, body).then(function (r) {
                busy(un, false);
                if (!r.ok) showError(un, r.error);
                else step(route === 'pub.unsubscribe' ? 'done' : 'sent');
            });
        });
    }

    // ---- inside the widget: the parent page sizes the iframe to the content
    function resize() {
        if (!embedded || window.parent === window) return;
        window.parent.postMessage({opennewsletter: 'height', height: Math.ceil(document.documentElement.scrollHeight)}, '*');
    }
    if (embedded) {
        window.addEventListener('load', resize);
        window.addEventListener('resize', resize);
        if (window.ResizeObserver) new ResizeObserver(resize).observe(document.body);
        resize();
    }
})();
