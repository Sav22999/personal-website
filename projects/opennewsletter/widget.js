/**
 * OpenNewsletter — subscription widget for other websites.
 *
 *   <div data-opennewsletter="SLUG"></div>
 *   <script src="https://www.saveriomorelli.com/projects/opennewsletter/widget.js" async></script>
 *
 * Optional attributes on the div: data-lang="it" (language of the form), data-max-width="480" (pixels),
 * data-style="card|minimal|outline|bold|compact" (another look than the one saved in the group's settings).
 * The form lives in an iframe (subscribe.php?embed=1): the host page never sees the address typed in it, and the
 * iframe grows with its content.
 */
(function () {
    'use strict';
    var me = document.currentScript || document.querySelector('script[src*="opennewsletter/widget.js"]');
    if (!me) return;
    var base = me.src.replace(/widget\.js(\?.*)?$/, '');
    var origin = new URL(base).origin;
    var frames = [];

    function mount(el) {
        if (el.getAttribute('data-on-mounted')) return;
        el.setAttribute('data-on-mounted', '1');
        var slug = el.getAttribute('data-opennewsletter');
        if (!/^[a-z0-9-]{3,40}$/.test(slug || '')) return;
        var url = base + 'subscribe.php?embed=1&g=' + encodeURIComponent(slug);
        var lang = el.getAttribute('data-lang');
        if (lang && /^[a-z]{2}$/.test(lang)) url += '&lang=' + lang;
        var style = el.getAttribute('data-style');
        if (style && /^(card|minimal|outline|bold|compact)$/.test(style)) url += '&style=' + style;
        var f = document.createElement('iframe');
        f.src = url;
        f.title = 'Newsletter';
        f.loading = 'lazy';
        f.setAttribute('scrolling', 'no');
        f.style.cssText = 'display:block;width:100%;border:0;height:420px;background:transparent;color-scheme:normal;max-width:' + (parseInt(el.getAttribute('data-max-width'), 10) || 520) + 'px';
        el.appendChild(f);
        frames.push(f);
    }

    window.addEventListener('message', function (ev) {
        if (ev.origin !== origin || !ev.data || ev.data.opennewsletter !== 'height') return;
        for (var i = 0; i < frames.length; i++) {
            if (frames[i].contentWindow === ev.source) frames[i].style.height = Math.max(120, Math.min(2000, ev.data.height | 0)) + 'px';
        }
    });

    function scan() {
        document.querySelectorAll('[data-opennewsletter]').forEach(mount);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scan);
    else scan();
})();
