/* FoodTrack — API client shared by the app and the admin portal. */
(function () {
    'use strict';

    const t = (s, v) => (window.I18N ? I18N.t(s, v) : s);
    const lang = () => (window.I18N ? I18N.lang : 'it');

    const API_URL = 'api/index.php';
    let csrfToken = null;
    let onUnauthenticated = null;

    class ApiError extends Error {
        constructor(status, data) {
            super((data && data.message) || t('Error {code}', {code: status}));
            this.status = status;
            this.code = (data && data.error) || 'error';
            this.data = data || {};
        }
    }

    function apiUrl(route) {
        const [name, ...rest] = String(route).split('&');
        return API_URL + '?r=' + encodeURIComponent(name) + (rest.length ? '&' + rest.join('&') : '');
    }

    function handle(status, data, opts) {
        if (status >= 200 && status < 300 && data && data.ok !== false) {
            if (data.csrf) csrfToken = data.csrf;
            return data;
        }
        const err = new ApiError(status, data);
        if (status === 401 && onUnauthenticated && !(opts && opts.silentAuth)) onUnauthenticated(err);
        throw err;
    }

    async function request(route, body, opts) {
        const headers = {'Accept': 'application/json', 'X-Lang': lang()};
        const init = {
            method: body === undefined ? 'GET' : 'POST',
            headers,
            credentials: 'same-origin',
            cache: 'no-store'
        };
        if (body !== undefined) {
            headers['Content-Type'] = 'application/json';
            if (csrfToken) headers['X-CSRF-Token'] = csrfToken;
            init.body = JSON.stringify(body);
        }
        let res;
        try {
            res = await fetch(apiUrl(route), init);
        } catch (e) {
            throw new ApiError(0, {
                error: 'network',
                message: t('Connection unavailable. Check your network and try again.')
            });
        }
        let data = null;
        try {
            data = await res.json();
        } catch (e) {
            data = null;
        }
        return handle(res.status, data, opts);
    }

    window.Api = {
        ApiError,
        get: (route, opts) => request(route, undefined, opts),
        post: (route, body, opts) => request(route, body || {}, opts),
        setCsrf: t => {
            csrfToken = t;
        },
        onUnauthenticated: fn => {
            onUnauthenticated = fn;
        },
        async upload(route, formData) {
            let res;
            try {
                res = await fetch(apiUrl(route), {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {'Accept': 'application/json', 'X-Lang': lang(), 'X-CSRF-Token': csrfToken || ''}
                });
            } catch (e) {
                throw new ApiError(0, {
                    error: 'network',
                    message: t('Connection unavailable. Check your network and try again.')
                });
            }
            let data = null;
            try {
                data = await res.json();
            } catch (e) {
                data = res.status === 413 ? {
                    error: 'too_large',
                    message: t('The file is too large for the server.')
                } : null;
            }
            return handle(res.status, data);
        },
    };
})();
