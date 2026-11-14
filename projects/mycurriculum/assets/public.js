/**
 * MyCurriculum — the public page of a CV: the CV drawn as a web page, a language menu (a custom drop-down that
 * works with mouse, touch and keyboard) and, when the owner allows it, the PDF of the language on screen.
 * The data is the public copy embedded by lib/public-page.php: the same things the PDF shows, nothing more.
 */

import {t, getLang, flagSrc} from './i18n.js';
import {buildModel, renderCV, esc, langLabel, fixContactSeparators} from './cv.js';
import {downloadPdf, pdfFileName} from './pdf.js';

const root = document.getElementById('pub');
const {cv, lang: firstLang, preview} = JSON.parse(document.getElementById('cv-data').textContent);
let lang = firstLang;
const BASE = new URL('./', location.href).href.replace(/[?#].*$/, '');
document.documentElement.dataset.ui = getLang();

const SVG = (p) => `<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">${p}</svg>`;
const CHEV = '<path d="m6 9 6 6 6-6"/>';
const DL = '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>';
const CHECK = '<path d="m5 12 5 5 9-10"/>';
const GLOBE = '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/>';
// a language added by the owner has no flag: a globe instead
const flag = (l) => (flagSrc(l) ? `<img class="flag" src="${flagSrc(l)}" alt="" width="20" height="15">` : `<span class="flag flag-globe">${SVG(GLOBE)}</span>`);

function brand() {
    return `<a class="brand" href="./"><img src="icons/favicon.svg" alt=""><span><b>My</b>Curriculum</span></a>`;
}

function foot() {
    return `<footer class="site-foot"><a href="./">${esc(t('go_home'))}</a> · <a class="credit" href="https://www.saveriomorelli.com/" target="_blank" rel="noopener"><span>${esc(t('made_by'))}</span><span class="credit-name">Saverio Morelli</span></a></footer>`;
}

if (!cv) {
    root.innerHTML = `<header class="pub-bar">${brand()}</header><div class="pub-empty"><h1 style="font-size:24px">${esc(t('not_found'))}</h1><p><a class="btn btn-primary" href="./">${esc(t('go_home'))}</a></p></div>${foot()}`;
} else {
    draw();
}

function draw() {
    const m = buildModel(cv, lang, cv.photo);
    const langs = cv.settings.langs;
    root.innerHTML = `
        <header class="pub-bar">${brand()}<span class="spacer"></span>
            ${langs.length > 1 ? `<div class="lang-menu">
                <button class="lang-btn" type="button" aria-haspopup="listbox" aria-expanded="false" aria-label="${esc(langLabel(cv, lang))}">
                    ${flag(lang)}<span class="lang-name">${esc(langLabel(cv, lang))}</span>${SVG(CHEV).replace('class="ico"', 'class="ico chev"')}
                </button>
                <ul class="lang-list" role="listbox" tabindex="-1" hidden>
                    ${langs.map((l) => `<li role="option" data-lang="${l}" aria-selected="${l === lang}" lang="${l}">${flag(l)}<span>${esc(langLabel(cv, l))}</span>${SVG(CHECK).replace('class="ico"', 'class="ico tick"')}</li>`).join('')}
                </ul></div>` : ''}
            ${cv.settings.allowPdf ? `<button class="btn btn-primary pdf-btn" type="button">${SVG(DL)}<span class="hide-sm">${esc(t('pdf'))}</span></button>` : ''}
        </header>
        <main class="pub-main">
            ${preview ? `<div class="pub-banner">${esc(t('preview_private'))}</div>` : ''}
            ${renderCV(m, 'web')}
        </main>${foot()}`;
    document.title = (m.name || 'CV') + ' — CV';
    fixContactSeparators(root);
    document.fonts?.ready.then(() => fixContactSeparators(root));
    root.querySelector('.pdf-btn')?.addEventListener('click', async (e) => {
        const b = e.currentTarget;
        b.disabled = true;
        try {
            await downloadPdf(m, BASE, pdfFileName(m));
        } catch (err) {
            console.error(err);
            alert(t('err_generic'));
        }
        b.disabled = false;
    });
    setupMenu();
}

function choose(l) {
    if (l === lang) return;
    lang = l;
    const u = new URL(location.href);
    u.searchParams.set('lang', l);
    history.replaceState(null, '', u);
    draw();
    root.querySelector('.lang-btn')?.focus();
}

function setupMenu() {
    const btn = root.querySelector('.lang-btn');
    const list = root.querySelector('.lang-list');
    if (!btn) return;
    const items = [...list.querySelectorAll('li')];
    let active = items.findIndex((li) => li.dataset.lang === lang);
    const mark = () => items.forEach((li, i) => li.classList.toggle('active', i === active));
    const open = () => {
        list.hidden = false;
        btn.setAttribute('aria-expanded', 'true');
        active = items.findIndex((li) => li.dataset.lang === lang);
        mark();
        list.focus();
        setTimeout(() => document.addEventListener('click', outside));
    };
    const close = (focus = true) => {
        list.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
        document.removeEventListener('click', outside);
        if (focus) btn.focus();
    };
    const outside = (e) => !list.contains(e.target) && close(false);
    btn.addEventListener('click', () => (list.hidden ? open() : close()));
    btn.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            open();
        }
    });
    list.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') active = (active + 1) % items.length;
        else if (e.key === 'ArrowUp') active = (active - 1 + items.length) % items.length;
        else if (e.key === 'Home') active = 0;
        else if (e.key === 'End') active = items.length - 1;
        else if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            return choose(items[active].dataset.lang);
        } else if (e.key === 'Escape' || e.key === 'Tab') return close(e.key === 'Escape');
        else return;
        e.preventDefault();
        mark();
    });
    for (const li of items) li.addEventListener('click', () => choose(li.dataset.lang));
}

// the contacts wrap differently at another width: put the separators right again
let resizeTimer = 0;
addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => fixContactSeparators(root), 120);
});
