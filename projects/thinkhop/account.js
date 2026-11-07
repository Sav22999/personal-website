/*
 * ThinkHop — accounts: email and password or Google, like FoodTrack and Giftwise.
 * An account is optional. When there is one, finished games are also kept on the server, so the statistics
 * are the same on every device. The questions never leave the device.
 */
(function () {
    'use strict';

    const {store, h, icon, toast, modal, confirmBox} = window.ThinkHopUI;
    const t = window.ThinkHopI18n.t;
    const lang = window.ThinkHopI18n.getLang;
    const CONTACT_URL = 'https://www.saveriomorelli.com/contact-me/email/';

    let csrf = null;
    let user = null;
    let config = store.get('config', {accounts: false, googleClientId: '', game: null});
    window.ThinkHopEngine.configure(config.game);
    let onChange = () => {
    };

    // ---------- API ----------
    class ApiError extends Error {
        constructor(code, data) {
            super(t('err_' + code));
            this.code = code;
            this.data = data || {};
        }
    }

    async function api(route, body) {
        const headers = {Accept: 'application/json', 'X-Lang': lang()};
        const init = {method: body === undefined ? 'GET' : 'POST', headers, credentials: 'same-origin', cache: 'no-store'};
        if (body !== undefined) {
            headers['Content-Type'] = 'application/json';
            if (csrf) headers['X-CSRF-Token'] = csrf;
            init.body = JSON.stringify(body);
        }
        let res, data = null;
        try {
            res = await fetch('api/index.php?r=' + encodeURIComponent(route), init);
        } catch (e) {
            throw new ApiError('network');
        }
        try {
            data = await res.json();
        } catch (e) { /* not JSON: an error page */
        }
        if (!res.ok || !data || data.ok === false) throw new ApiError((data && data.error) || 'server', data);
        if (data.csrf) csrf = data.csrf;
        return data;
    }

    // ---------- games on the device and on the account ----------
    const games = () => store.get('games', []);

    /** Keeps a finished game; it stays in `pending` until the server has it. */
    function addGame(g) {
        store.set('games', [...games(), g].slice(-8000)); // points and stars come from this list
        store.set('pending', [...store.get('pending', []), g.uid]);
        if (user) sync();
    }

    let syncing = null;

    async function sync() {
        if (!user) return false;
        if (syncing) return syncing;
        syncing = (async () => {
            try {
                const waiting = store.get('pending', []);
                const send = games().filter(g => waiting.includes(g.uid));
                const d = await api('sync', {games: send});
                // Games finished while the request was running are still only here
                const late = store.get('pending', []).filter(u => !waiting.includes(u));
                const known = new Set(d.games.map(g => g.uid));
                store.set('games', [...d.games, ...games().filter(g => late.includes(g.uid) && !known.has(g.uid))]);
                store.set('pending', late);
                return true;
            } catch (e) {
                if (e.code === 'unauthenticated') signedOut();
                return false;
            } finally {
                syncing = null;
            }
        })();
        return syncing;
    }

    function signedOut() {
        user = null;
        csrf = null;
    }

    /** Sign-out and deletion leave nothing of the account on the device. */
    function forgetDevice() {
        signedOut();
        store.del('games');
        store.del('pending');
        store.del('saved');
    }

    async function start(changed) {
        onChange = changed;
        try {
            const c = await api('auth.config');
            config = {accounts: !!c.accounts, googleClientId: c.googleClientId || '', game: c.game || null, installNeeded: !!c.installNeeded, needsAdmin: !!c.needsAdmin};
            store.set('config', config);
            window.ThinkHopEngine.configure(config.game);
            if (config.accounts) {
                const me = await api('auth.me');
                user = me.user;
                if (user) await sync();
            }
        } catch (e) { /* offline or no server: the game does not need it */
        }
        onChange();
        // An account made before usernames existed: the game asks, the admin panel doesn't get in the way
        if (user && !user.username && document.getElementById('app')) showUsernameForm();
    }

    async function afterSignIn(d) {
        user = d.user;
        const ok = await sync();
        toast(ok ? t('synced') : t('sync_later'), ok ? 'ok' : undefined);
        onChange();
        if (!user.username) showUsernameForm();
    }

    /** The only public data of an account: it appears in the rankings and in the search for friends. */
    function showUsernameForm(parent, done) {
        const username = usernameInput(user.username);
        const err = h('p.form-error', {role: 'alert', hidden: true});
        const save = h('button.btn.btn-primary', {type: 'submit'}, t('save'));
        const m = modal([h('h3', t(user.username ? 'username_change' : 'username_title')), h('p.muted', t('username_text')), h('form.form', {
            novalidate: true, onsubmit: async e => {
                e.preventDefault();
                err.hidden = true;
                try {
                    const d = await busy(save, () => api('auth.username', {username: username.value}));
                    user = d.user;
                    m.close();
                    if (parent) parent.close();
                    toast(t('saved'), 'ok');
                    onChange();
                    if (parent) showProfile();
                    if (done) done();
                } catch (ex) {
                    err.textContent = ex.message;
                    err.hidden = false;
                }
            }
        }, usernameField(username), err, h('div.actions', h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('cancel')), save))]);
    }

    function showAccountsOff() {
        const m = modal([h('h3', t('account')), h('p.muted', t('sign_in_off')), h('div.actions', h('button.btn.btn-primary', {type: 'button', onclick: () => m.close()}, t('close')))]);
    }

    // ---------- Sign in with Google ----------
    const native = window.ThinkHopAndroid || null;
    const nativeWait = {};
    window.ThinkHopNative = {
        // Called by the Android app: (kind, value, error)
        result(kind, value, error) {
            const w = nativeWait[kind];
            delete nativeWait[kind];
            if (w) (error ? w.reject(new Error(error)) : w.resolve(value));
        },
    };

    let gisPromise = null;

    function loadGis() {
        if (!gisPromise) {
            gisPromise = new Promise((resolve, reject) => {
                const sc = document.createElement('script');
                sc.src = 'https://accounts.google.com/gsi/client';
                sc.async = true;
                sc.onload = () => resolve(window.google);
                sc.onerror = () => {
                    gisPromise = null;
                    reject(new Error('Google unreachable'));
                };
                document.head.appendChild(sc);
            });
        }
        return gisPromise;
    }

    const G_LOGO = '<svg viewBox="0 0 48 48" width="20" height="20" aria-hidden="true"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.6l6.8-6.8C35.8 2.4 30.3 0 24 0 14.6 0 6.6 5.4 2.6 13.3l7.9 6.1C12.4 13.7 17.7 9.5 24 9.5z"/><path fill="#4285F4" d="M46.1 24.5c0-1.6-.1-3.1-.4-4.5H24v9h12.4c-.5 2.9-2.2 5.3-4.6 6.9l7.4 5.8c4.3-4 6.9-9.9 6.9-17.2z"/><path fill="#FBBC05" d="M10.5 28.6c-.5-1.4-.8-2.9-.8-4.6s.3-3.2.8-4.6l-7.9-6.1C1 16.6 0 20.2 0 24s1 7.4 2.6 10.7l7.9-6.1z"/><path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.9-5.8l-7.4-5.8c-2.1 1.4-4.8 2.3-8.5 2.3-6.3 0-11.6-4.2-13.5-10l-7.9 6.1C6.6 42.6 14.6 48 24 48z"/></svg>';

    async function googleButton(slot, onCredential, text) {
        if (native && native.googleSignIn) {
            // Android app: Google Identity Services does not run in a WebView, the system account picker does
            const b = h('button.btn.btn-google.btn-block', {
                type: 'button', onclick: async () => {
                    b.disabled = true;
                    try {
                        const token = await new Promise((resolve, reject) => {
                            nativeWait.google = {resolve, reject};
                            native.googleSignIn(config.googleClientId);
                        });
                        if (token) onCredential(token);
                    } catch (e) {
                        if (e.message !== 'cancelled') toast(t('google_off'), 'bad');
                    } finally {
                        b.disabled = false;
                    }
                }
            }, h('span.g', {html: G_LOGO}), h('span', t(text === 'signup_with' ? 'google_signup' : 'google_continue')));
            slot.replaceChildren(b);
            return;
        }
        try {
            const g = await loadGis();
            g.accounts.id.initialize({
                client_id: config.googleClientId,
                callback: r => onCredential(r.credential),
                ux_mode: 'popup',
                auto_select: false,
                cancel_on_tap_outside: true,
                itp_support: true,
                use_fedcm_for_button: true,
            });
            const root = document.documentElement;
            const dark = root.getAttribute('data-theme') === 'dark' || (!root.hasAttribute('data-theme') && matchMedia('(prefers-color-scheme: dark)').matches);
            slot.replaceChildren();
            g.accounts.id.renderButton(slot, {
                type: 'standard', theme: dark ? 'filled_black' : 'outline', size: 'large', text: text || 'continue_with', shape: 'pill',
                logo_alignment: 'center', locale: lang(), width: Math.min(Math.max(slot.clientWidth, 220), 400),
            });
        } catch (e) {
            slot.replaceChildren(h('p.muted.center', t('google_off')));
        }
    }

    // ---------- forms ----------
    const field = (label, input) => h('label.field', h('span', label), input);
    const input = (type, value, auto) => h('input', {type, value: value || '', autocomplete: auto, required: true});

    const usernameInput = value => h('input', {type: 'text', value: value || '', maxlength: 20, autocomplete: 'username', autocapitalize: 'none', spellcheck: 'false', required: true});
    const usernameField = el => h('label.field', h('span', t('username')), el, h('small.muted', t('username_hint')));

    const privacyCheck = () => {
        const box = h('input', {type: 'checkbox'});
        return {
            box, el: h('label.check', box, h('span', t('privacy_accept'), h('a', {href: 'privacy.html', target: '_blank', rel: 'noopener'}, t('privacy_policy')))),
        };
    };

    async function busy(button, work) {
        button.disabled = true;
        try {
            return await work();
        } finally {
            button.disabled = false;
        }
    }

    function showForgot() {
        const m = modal([h('h3', t('forgot')), h('p.muted', t('forgot_text')), h('div.actions',
            h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('close')),
            h('a.btn.btn-primary', {href: CONTACT_URL, target: '_blank', rel: 'noopener'}, t('forgot_open')))]);
    }

    function showSignIn(mode) {
        mode = mode || store.get('authMode', 'login');
        const name = input('text', '', 'name');
        const username = usernameInput();
        const email = input('email', '', 'email');
        const pw = input('password', '', mode === 'login' ? 'current-password' : 'new-password');
        const privacy = privacyCheck();
        const err = h('p.form-error', {role: 'alert', hidden: true});
        const gslot = h('div.google-slot');
        const submit = h('button.btn.btn-primary.btn-block', {type: 'submit'}, t(mode === 'login' ? 'sign_in' : 'sign_up'));
        const m = modal([
            h('h3', t('account')), h('p.muted', t('account_pitch')),
            h('div.seg.auth-seg', {role: 'tablist'}, [['login', 'sign_in'], ['register', 'sign_up']].map(([k, label]) => h('button' + (k === mode ? '.sel' : ''), {
                type: 'button', role: 'tab', 'aria-selected': String(k === mode), onclick: () => {
                    if (k === mode) return;
                    store.set('authMode', k);
                    m.close();
                    showSignIn(k);
                }
            }, t(label)))),
            config.googleClientId ? [gslot, h('div.divider', h('span', t('or_email')))] : null,
            h('form.form', {
                    novalidate: true, onsubmit: async e => {
                        e.preventDefault();
                        err.hidden = true;
                        try {
                            const d = await busy(submit, () => mode === 'login'
                                ? api('auth.login', {email: email.value, password: pw.value})
                                : api('auth.register', {name: name.value, username: username.value, email: email.value, password: pw.value, acceptPrivacy: privacy.box.checked}));
                            m.close();
                            await afterSignIn(d);
                        } catch (ex) {
                            err.textContent = ex.message;
                            err.hidden = false;
                        }
                    }
                },
                mode === 'register' ? field(t('name'), name) : null,
                mode === 'register' ? usernameField(username) : null,
                field(t('email'), email),
                field(t(mode === 'login' ? 'password' : 'password_new'), pw),
                mode === 'register' ? privacy.el : null,
                err, submit,
                mode === 'login' ? h('button.btn.btn-ghost.btn-block', {type: 'button', onclick: showForgot}, t('forgot')) : null),
        ]);
        if (config.googleClientId) googleButton(gslot, cred => googleSignIn(cred, m), mode === 'login' ? 'continue_with' : 'signup_with');
    }

    async function googleSignIn(credential, parent) {
        try {
            const d = await api('auth.google', {credential});
            parent.close();
            await afterSignIn(d);
        } catch (e) {
            if (e.code !== 'needs_signup') {
                toast(e.message, 'bad');
                return;
            }
            // A new account: the privacy consent comes first
            const privacy = privacyCheck();
            const username = usernameInput();
            const ok = h('button.btn.btn-primary', {
                type: 'button', onclick: async () => {
                    if (!privacy.box.checked) {
                        toast(t('privacy_needed'), 'bad');
                        return;
                    }
                    try {
                        const d = await busy(ok, () => api('auth.google', {credential, acceptPrivacy: true, username: username.value}));
                        s.close();
                        parent.close();
                        await afterSignIn(d);
                    } catch (ex) {
                        toast(ex.message, 'bad');
                    }
                }
            }, t('sign_up'));
            const s = modal([h('h3', t('create_title')), h('p.muted', [e.data.name, e.data.email].filter(Boolean).join(' · ')), usernameField(username), privacy.el,
                h('div.actions', h('button.btn.btn-ghost', {type: 'button', onclick: () => s.close()}, t('cancel')), ok)]);
        }
    }

    function showProfileForm(parent) {
        const name = input('text', user.name, 'name');
        const email = input('email', user.email, 'email');
        const err = h('p.form-error', {role: 'alert', hidden: true});
        const save = h('button.btn.btn-primary', {type: 'submit'}, t('save'));
        const m = modal([h('h3', t('profile_edit')), h('form.form', {
            novalidate: true, onsubmit: async e => {
                e.preventDefault();
                err.hidden = true;
                try {
                    const d = await busy(save, () => api('auth.profile', {name: name.value, email: email.value}));
                    user = d.user;
                    m.close();
                    parent.close();
                    toast(t('saved'), 'ok');
                    onChange();
                    showProfile();
                } catch (ex) {
                    err.textContent = ex.message;
                    err.hidden = false;
                }
            }
        }, field(t('name'), name), field(t('email'), email), err, h('div.actions', h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('cancel')), save))]);
    }

    function showPasswordForm(parent) {
        const current = input('password', '', 'current-password');
        const pw = input('password', '', 'new-password');
        const err = h('p.form-error', {role: 'alert', hidden: true});
        const save = h('button.btn.btn-primary', {type: 'submit'}, t('save'));
        const m = modal([h('h3', t(user.hasPassword ? 'password_change' : 'password_set')), h('form.form', {
            novalidate: true, onsubmit: async e => {
                e.preventDefault();
                err.hidden = true;
                try {
                    const d = await busy(save, () => api('auth.password', {current: current.value, password: pw.value}));
                    user = d.user;
                    m.close();
                    parent.close();
                    toast(t('password_saved'), 'ok');
                    showProfile();
                } catch (ex) {
                    err.textContent = ex.message;
                    err.hidden = false;
                }
            }
        }, user.hasPassword ? field(t('password_current'), current) : null, field(t('password_new'), pw), err,
            h('div.actions', h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('cancel')), save))]);
    }

    async function signOut(parent) {
        if (!await confirmBox(t('sign_out_title'), t('sign_out_text'), t('sign_out'), t('cancel'))) return;
        await sync(); // what is still only here must reach the account before it leaves the device
        try {
            await api('auth.logout', {});
        } catch (e) { /* the cookie expires by itself */
        }
        forgetDevice();
        parent.close();
        onChange();
    }

    function showDelete(parent) {
        const pw = input('password', '', 'current-password');
        const err = h('p.form-error', {role: 'alert', hidden: true});
        const ok = h('button.btn.btn-danger', {type: 'submit'}, t('delete_ok'));
        const m = modal([h('h3', t('delete_title')), h('p.muted', t('delete_text')), h('form.form', {
            novalidate: true, onsubmit: async e => {
                e.preventDefault();
                err.hidden = true;
                try {
                    await busy(ok, () => api('auth.delete', {password: pw.value}));
                    forgetDevice();
                    m.close();
                    parent.close();
                    toast(t('deleted'), 'ok');
                    onChange();
                } catch (ex) {
                    err.textContent = ex.message;
                    err.hidden = false;
                }
            }
        }, user.hasPassword ? field(t('delete_password'), pw) : null, err,
            h('div.actions', h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('cancel')), ok))]);
    }

    /** Everything is free and open source: a donation is only a way to support the project. */
    function showDonate() {
        modal([
            h('div.donate-modal-icon', icon('heart')),
            h('h3', t('donate_modal_title')),
            h('p.muted', t('donate_modal_intro')),
            h('div.donate-reasons',
                h('div.donate-reason', h('div.donate-reason-icon', icon('check')), h('div', h('strong', t('donate_reason_ads_title')), h('p', t('donate_reason_ads_desc')))),
                h('div.donate-reason', h('div.donate-reason-icon', icon('magic')), h('div', h('strong', t('donate_reason_oss_title')), h('p', t('donate_reason_oss_desc')))),
                h('div.donate-reason', h('div.donate-reason-icon', icon('globe')), h('div', h('strong', t('donate_reason_server_title')), h('p', t('donate_reason_server_desc'))))),
            h('div.actions', h('a.btn.btn-primary.btn-block.donate-cta-btn', {href: 'https://www.saveriomorelli.com/donate/', target: '_blank', rel: 'noopener'}, icon('heart'), h('span', t('donate_cta')))),
        ]);
    }

    /** Only the player's choice: staying out of the ranking changes nothing else in the game. */
    function rankingToggle() {
        const state = h('b.menu-state');
        const draw = () => {
            state.textContent = t(user.ranking ? 'yes' : 'no');
            b.setAttribute('aria-pressed', String(!!user.ranking));
        };
        const b = h('button', {
            type: 'button', title: t('ranking_hint'), onclick: async () => {
                try {
                    const d = await busy(b, () => api('auth.ranking', {visible: !user.ranking}));
                    user = d.user;
                    draw();
                    toast(t(user.ranking ? 'ranking_on' : 'ranking_off'), 'ok');
                } catch (e) {
                    toast(e.message, 'bad');
                }
            }
        }, icon('trophy'), h('span', t('ranking_show')), state);
        draw();
        return b;
    }

    function showProfile() {
        const m = modal([
            h('div.profile-head', h('span.avatar', (user.name || '?').slice(0, 1).toUpperCase()),
                h('div', h('h3', t('hello', {name: user.name.split(' ')[0]})), h('p.muted', user.username ? `@${user.username} · ${user.email}` : user.email))),
            h('div.menu-list',
                h('button', {type: 'button', onclick: () => showProfileForm(m)}, icon('pen'), h('span', t('profile_edit'))),
                h('button', {type: 'button', onclick: () => showUsernameForm(m)}, icon('user'), h('span', t(user.username ? 'username_change' : 'username_title'))),
                h('button', {type: 'button', onclick: () => showPasswordForm(m)}, icon('key'), h('span', t(user.hasPassword ? 'password_change' : 'password_set'))),
                rankingToggle(),
                h('button', {type: 'button', onclick: showDonate}, icon('heart'), h('span', t('donate'))),
                user.isAdmin ? h('a.menu-link', {href: 'admin.html'}, icon('shield'), h('span', t('admin_panel'))) : null,
                h('button', {type: 'button', onclick: () => signOut(m)}, icon('exit'), h('span', t('sign_out'))),
                h('button.danger', {type: 'button', onclick: () => showDelete(m)}, icon('trash'), h('span', t('delete_account')))),
        ]);
    }

    window.ThinkHopAccount = {
        start, addGame, games, api,
        enabled: () => config.accounts,
        installNeeded: () => !!config.installNeeded,
        needsAdmin: () => !!config.needsAdmin,
        user: () => user,
        open: () => (user ? showProfile() : (config.accounts ? showSignIn() : showAccountsOff())),
        askUsername: done => showUsernameForm(null, done),
        showDonate,
    };
})();
