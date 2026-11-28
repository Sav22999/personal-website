/*
 * Prism Mine theme: automatic, light or dark. It runs in <head>, before the page is drawn,
 * so the wrong colours never flash. The stylesheet only reads <html data-theme>.
 */
(function () {
    'use strict';

    const KEY = 'pm_theme';
    const COLORS = {light: '#F3ECE2', dark: '#15100D'};
    const MODES = ['auto', 'light', 'dark'];
    const root = document.documentElement;
    const system = matchMedia('(prefers-color-scheme: light)');

    let mode = 'auto';
    try {
        const saved = localStorage.getItem(KEY);
        if (MODES.includes(saved)) mode = saved;
    } catch (e) { /* storage unavailable */
    }

    function apply() {
        const theme = mode === 'auto' ? (system.matches ? 'light' : 'dark') : mode;
        root.setAttribute('data-theme', theme);
        const meta = document.querySelector('meta[name=theme-color]');
        if (meta) meta.content = COLORS[theme];
        if (window.AndroidTheme) window.AndroidTheme.setDark(theme === 'dark');
        document.querySelectorAll('[data-theme-mode]').forEach(b => {
            const on = b.dataset.themeMode === mode;
            b.classList.toggle('active', on);
            b.setAttribute('aria-checked', String(on));
        });
        document.querySelectorAll('[data-theme-label]').forEach(s => {
            s.hidden = s.dataset.themeLabel !== mode;
        });
    }

    function choose(next) {
        mode = next;
        try {
            localStorage.setItem(KEY, mode);
        } catch (e) { /* storage unavailable */
        }
        apply();
    }

    system.addEventListener('change', apply);
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-theme-mode]').forEach(b => b.addEventListener('click', () => choose(b.dataset.themeMode)));
        const toggle = document.getElementById('btn-theme');
        if (toggle) toggle.addEventListener('click', () => choose(MODES[(MODES.indexOf(mode) + 1) % MODES.length]));
        apply();
    });
    apply();
})();
