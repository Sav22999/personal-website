/* Fucina — the app: home, level paths, lessons, exams, games, statistics and profile. */
(function () {
    'use strict';

    const VERSION = '1.15.3';
    /** What changed, newest first: add an entry at every release, and keep VERSION equal to the first one. */
    const RELEASES = [
        {v: '1.15.3', date: '2026-11-07', it: ['Salti di riga: ora sposti le righe trascinandole (da telefono, dalla maniglia a sinistra); il tocco per scambiarle resta'], en: ['Line hop: you now move lines by dragging them (on a phone, by the handle on the left); tapping to swap still works']},
        {v: '1.15.2', date: '2026-11-07', it: ['Tagdoku: i riquadri sono separati da linee più marcate e i tag che inserisci sono blu, così non sembrano errori'], en: ['Tagdoku: the boxes have stronger dividing lines and the tags you enter are blue, so they no longer look like mistakes']},
        {v: '1.15.1', date: '2026-11-07', it: ['Quattro in fila: il tuo gettone è verde e una risposta sbagliata fa cadere un gettone rosso, che non è di nessuno', 'Corretto un errore grafico che allargava la colonna scelta'], en: ['Four in a line: your disc is green and a wrong answer drops a red disc that belongs to nobody', 'Fixed a display bug that stretched the chosen column']},
        {v: '1.15.0', date: '2026-11-07', it: ['Alla prima partita fai un giro di prova guidato, con i pezzi veri del gioco'], en: ['Your first game starts with a guided practice round, using the real pieces of the game']},
        {v: '1.14.0', date: '2026-11-07', it: ['Alla prima partita di ogni gioco una guida spiega come si gioca; poi la ritrovi nel pulsante ?'], en: ['The first time you play a game a guide explains how; then you find it under the ? button']},
        {v: '1.13.0', date: '2026-11-07', it: ['Mentre una pagina si carica ne vedi già la forma', 'Il pulsante di Google non ha più lo sfondo bianco nel tema scuro'], en: ['While a page loads you already see its shape', 'The Google button no longer has a white background in the dark theme']},
        {v: '1.12.1', date: '2026-11-07', it: ['I livelli superati con l’esame mostrano da 1 a 3 fuochi, in base al voto', 'Sfida del giorno e Palestra indicano il linguaggio di ogni esercizio', 'Su telefono il corso si apre sull’ultimo livello sbloccato', 'L’email dell’account non si può più modificare'], en: ['Levels passed with the exam show 1 to 3 fires, based on the score', 'Daily challenge and Gym show the language of each exercise', 'On phones a course opens on the last unlocked level', 'The email of the account can no longer be changed']},
        {v: '1.11.0', date: '2026-11-07', it: ['Notifiche: sfida del giorno, serie a rischio, premi e amici, ognuna attivabile dal profilo'], en: ['Notifications: daily challenge, streak at risk, prizes and friends, each one set from your profile']},
        {v: '1.10.0', date: '2026-11-07', it: ['Chiunque può verificare un certificato con il suo numero, senza account'], en: ['Anyone can verify a certificate with its number, without an account']},
        {v: '1.9.0', date: '2026-11-07', it: ['L’esame ha un tempo massimo di 15 minuti', 'Sfida del giorno cronometrata: a parità di risposte vince chi è più veloce'], en: ['The exam has a time limit of 15 minutes', 'Timed daily challenge: with equal answers the fastest wins']},
        {v: '1.8.0', date: '2026-11-07', it: ['Nuovo certificato: un’immagine in inglese, da salvare in PNG o PDF'], en: ['New certificate: an image in English, to save as PNG or PDF']},
        {v: '1.7.0', date: '2026-11-07', it: ['Superando l’esame i livelli non giocati risultano risolti: 2 stelle dal 90%, altrimenti 1', 'La terza stella si guadagna giocando il livello'], en: ['Passing the exam solves the levels not played: 2 stars from 90%, otherwise 1', 'The third star is earned by playing the level']},
        {v: '1.6.2', date: '2026-11-07', it: ['Testi dell’esame più chiari'], en: ['Clearer exam texts']},
        {v: '1.6.1', date: '2026-11-07', it: ['Un esame superato con il massimo non si ripete più'], en: ['An exam passed with full marks can no longer be repeated']},
        {v: '1.6.0', date: '2026-11-07', it: ['Favilla ti accompagna di più: in home, nei consigli delle lezioni e prima degli esami'], en: ['Favilla keeps you company more: at home, in lesson tips and before exams']},
        {v: '1.5.0', date: '2026-11-07', it: ['Sai già la materia? Fai subito l’esame finale di qualsiasi corso: se lo superi, il corso è finito e si aprono i successivi', 'Nuovo carattere per il codice, uguale su tutti i dispositivi'], en: ['Already know the subject? Take the final exam of any course right away: pass it and the course is finished and the next ones open', 'New font for code, the same on every device']},
        {v: '1.4.1', date: '2026-11-07', it: ['La firma nel profilo usa il carattere del sito dell’autore'], en: ['The credit in the profile uses the font of the author’s website']},
        {v: '1.4.0', date: '2026-11-07', it: ['Note di rilascio: tocca il numero di versione nel profilo', 'Nel profilo trovi chi ha creato Fucina e come sostenerla', 'A fine sfida restano solo il risultato e il pulsante per continuare', 'Il trascinamento delle righe ora funziona, con mouse e dito'], en: ['Release notes: tap the version number in your profile', 'Your profile shows who made Fucina and how to support it', 'At the end of a drill only the result and the continue button remain', 'Dragging lines now works, with mouse and finger']},
        {v: '1.3.0', date: '2026-11-07', it: ['Username: l’unico dato pubblico del tuo account', 'Amici: cerca, aggiungi e rimuovi', 'Classifiche della sfida del giorno, tra tutti o tra amici', 'I premi di fine giornata vanno alla sfida del giorno', 'Nuova icona'], en: ['Username: the only public data of your account', 'Friends: search, add and remove', 'Daily challenge rankings, among everyone or among friends', 'End-of-day prizes go to the daily challenge', 'New icon']},
        {v: '1.2.0', date: '2026-11-07', it: ['Nove nuovi corsi: JavaScript, TypeScript, PHP, HTML e CSS intermedi, Animazioni', 'Più livelli nei corsi esistenti', 'Il certificato si sblocca con le Scintille', 'Classifica giornaliera con premi', 'Grado sempre in vista e molti nuovi badge'], en: ['Nine new courses: JavaScript, TypeScript, PHP, intermediate HTML and CSS, Animations', 'More levels in the existing courses', 'Certificates are unlocked with Sparks', 'Daily leaderboard with prizes', 'Grade always in sight and many new badges']},
        {v: '1.1.0', date: '2026-11-07', it: ['Grafica tutta nuova con Favilla, la mascotte', 'Mappa dei livelli', 'Sfide lampo: tessere, riordina, caccia al bug, coppie, vero o falso, regola', 'Palestra: sei sfide quando vuoi'], en: ['A brand new look with Favilla, the mascot', 'Level map', 'Quick drills: tiles, sort it, bug hunt, pairs, true or false, tune it', 'Gym: six drills whenever you want']},
        {v: '1.0.0', date: '2026-11-07', it: ['Prima versione: corsi a livelli, quiz, esercizi, giochi, certificati e pannello admin'], en: ['First release: level-based courses, quizzes, exercises, games, certificates and admin portal']},
    ];
    const t = I18N.t;
    const {h, icon, tile, chip, btn, iconBtn, busy, toast, sheet, confirm} = UI;
    const {mascot, confetti, colour, langChip} = Fun;
    const root = document.getElementById('app');
    const LOGO = 'assets/icons/icon-192.png?v=2';
    const AUTHOR_URL = 'https://www.saveriomorelli.com/';
    const DONATE_URL = 'https://www.saveriomorelli.com/donate/';
    const CONTACT_URL = '../../contact-me/';
    const DESK = matchMedia('(min-width: 960px)');

    const S = {user: null, config: {googleClientId: ''}, loaded: false, state: null, course: {}, board: {type: 'daily', scope: 'all'}};

    // ---------------------------------------------------------------- small components

    function avatar(name, cls) {
        return h('span.avatar' + (cls ? '.' + cls : ''), String(name || '?').trim().charAt(0).toUpperCase());
    }

    function banner(tone, ic, title, text) {
        return h('div.banner.' + tone, icon(ic), h('div.b-body', h('h3', title), text ? h('p', text) : null));
    }

    function empty(title, text, action) {
        return h('div.empty', mascot('happy', 'lg'), h('strong', title), text ? h('p', text) : null, action || null);
    }

    function seg(options, value, onchange) {
        const wrap = h('div.seg', {role: 'tablist'});
        for (const [v, label] of options) {
            const b = h('button' + (v === value ? '.sel' : ''), {
                type: 'button', role: 'tab', onclick: () => {
                    wrap.querySelectorAll('button').forEach(x => x.classList.toggle('sel', x === b));
                    onchange(v);
                }
            }, label);
            wrap.appendChild(b);
        }
        return wrap;
    }

    /** Levels passed with the exam: fires instead of stars, from its score. */
    function fires(n) {
        return h('span.stars.fires', {'aria-label': t('{n} fires out of 3: passed with the exam', {n}), title: t('Passed with the exam')}, [0, 1, 2].map(i => icon('fire', i < n ? 'on' : 'off')));
    }

    const progressMark = p => p.skipped ? fires(p.stars) : stars(p.stars);

    function stars(n, total = 3) {
        return h('span.stars', {'aria-label': t('{n} stars out of {total}', {n, total})}, Array.from({length: total}, (_, i) => icon('star', i < n ? 'on' : 'off')));
    }

    function difficultyDots(d) {
        const names = {1: t('Easy'), 2: t('Medium'), 3: t('Hard')};
        return h('span.diff.d' + d, {title: names[d]}, h('i'), h('i'), h('i'), h('span', names[d]));
    }

    function sparksChip() {
        return h('span.sparks', {title: t('Sparks: spend them on hints')}, icon('spark'), String(S.state ? S.state.user.points : 0));
    }

    /** The grade, always in sight: number, rank and how close the next one is. */
    function gradeChip() {
        const l = S.state.level;
        return h('a.grade.r-' + l.rankId, {href: '#/stats', title: t('Grade {n}', {n: l.level}) + ' · ' + l.rank}, h('b.grade-num', String(l.level)),
            h('span.grade-text', h('strong', l.rank), h('span.grade-bar', h('i', {style: {width: Math.round(l.into * 100 / l.need) + '%'}}))));
    }

    /** Text with `code`, **bold** and *italics*: built as nodes, so that tags in the lessons stay plain text. */
    function rich(text) {
        const out = [];
        const re = /`([^`]+)`|\*\*([^*]+)\*\*|\*([^*]+)\*/g;
        let last = 0, m;
        while ((m = re.exec(text))) {
            if (m.index > last) out.push(text.slice(last, m.index));
            if (m[1] !== undefined) out.push(h('code', m[1]));
            else if (m[2] !== undefined) out.push(h('strong', m[2]));
            else out.push(h('em', m[3]));
            last = re.lastIndex;
        }
        if (last < text.length) out.push(text.slice(last));
        return out;
    }

    function codeBlock(code, lang) {
        return h('figure.code', lang ? h('figcaption', lang) : null, h('pre', h('code', colour(code))));
    }

    const certUrl = code => `certificate.php?c=${code}&lang=${I18N.lang}`;

    function applyState(d) {
        S.state = d;
        S.user = d.user;
        showSparks();
    }

    /** The counter in the app bar stays on screen while the content below it changes. */
    function showSparks() {
        document.querySelectorAll('.sparks').forEach(x => x.lastChild.textContent = S.state.user.points);
        document.querySelectorAll('.grade').forEach(x => x.replaceWith(gradeChip()));
    }

    const go = path => {
        location.hash = '#/' + path;
    };

    /** What a server response has earned: XP, a new rank and badges. */
    function celebrate(d) {
        if (d.xp > 0) toast(t('+{n} XP and Sparks', {n: d.xp}), 'ok');
        if ((d.newBadges || []).length) {
            const s = sheet({
                title: d.newBadges.length > 1 ? t('{n} new badges!', {n: d.newBadges.length}) : t('New badge!'),
                body: [h('div.badge-won', d.newBadges.map(b => h('div.badge.on', tile('icon', b.icon), h('strong', b.title), h('span', b.desc))))],
                actions: [btn(t('Great!'), {onclick: () => s.close()})]
            });
        }
        if (d.levelUp) {
            confetti();
            const s = sheet({
                title: t('Grade {n}!', {n: d.levelUp.level}),
                body: [h('div.levelup', mascot('wow', 'lg'), h('p', t('You are now: {rank}', {rank: d.levelUp.rank})))],
                actions: [btn(t('Keep forging'), {onclick: () => s.close()})]
            });
        }
    }

    // ---------------------------------------------------------------- routing and shell

    function route() {
        const parts = (location.hash.replace(/^#\/?/, '') || 'home').split('/').map(decodeURIComponent);
        return {name: parts[0], a: parts[1], b: parts[2]};
    }

    function bottomNav(active) {
        const tab = (name, label, ic) => h('a' + (active === name ? '.active' : ''), {href: '#/' + name, 'aria-current': active === name ? 'page' : null}, h('span.pill', icon(ic)), label);
        return h('nav.bottomnav', {'aria-label': t('Sections')}, tab('home', t('Home'), 'home'), tab('path', t('Path'), 'book'), tab('games', t('Games'), 'game'), tab('board', t('Ranking'), 'trophy'), tab('stats', t('Stats'), 'stats'));
    }

    function sideNav(active) {
        const link = (name, label, ic) => h('a.sn-link' + (active === name ? '.active' : ''), {href: '#/' + name, 'aria-current': active === name ? 'page' : null}, icon(ic), h('span', label));
        return h('nav.sidenav', {'aria-label': t('Sections')},
            h('a.sn-brand', {href: '#/home'}, h('img', {src: LOGO, alt: ''}), h('strong', 'Fucina')),
            S.state ? gradeChip() : null,
            h('div.sn-links', link('home', t('Home'), 'home'), link('path', t('Path'), 'book'), link('games', t('Games'), 'game'), link('board', t('Ranking'), 'trophy'), link('stats', t('Stats'), 'stats')),
            h('div.grow'),
            S.user.role >= 5 ? h('a.sn-link', {href: 'admin.html'}, icon('shield'), h('span', t('Admin portal'))) : null,
            h('a.sn-user' + (active === 'profile' ? '.active' : ''), {href: '#/profile'}, avatar(S.user.name, 'sm'), h('span', S.user.name)));
    }

    const TAB_OF = {course: 'path', lesson: 'path', exam: 'path', daily: 'home', gym: 'home', game: 'games', play: 'games', friends: 'board'};
    const FULL_SCREEN = ['lesson', 'exam', 'daily', 'gym', 'play'];

    /** The shape of the page that is on its way, shown only when the data take a moment to arrive. */
    function skeleton(name) {
        const sk = (cls, style) => h('span.skeleton.' + cls, style ? {style} : null);
        const cards = n => Array.from({length: n}, () => h('div.sk-block', sk('sk-tile'), h('div.sk-body', sk('sk-title'), sk('sk-line.w-60'))));
        let body;
        if (name === 'course' && !DESK.matches) body = [sk('sk-line.w-80'), h('div.sk-trail', [50, 74, 62, 36, 28].map(x => sk('sk-node', {left: x + '%'})))];
        else if (name === 'course') body = [h('div.sk-row4', [1, 2, 3, 4].map(() => sk('sk-stat'))), cards(6)];
        else if (name === 'board' || name === 'friends') body = [sk('sk-hero'), sk('sk-seg'), Array.from({length: 5}, () => sk('sk-row'))];
        else if (FULL_SCREEN.includes(name)) body = [sk('sk-progress'), sk('sk-title.w-80'), Array.from({length: 4}, () => sk('sk-option'))];
        else body = [sk('sk-hero'), cards(3)];
        return h('div.sk-page', {'aria-busy': 'true', 'aria-label': t('Loading')}, body);
    }

    function showSkeleton(name) {
        const tab = TAB_OF[name] || name;
        const content = h('div.content', h('header.appbar' + (FULL_SCREEN.includes(name) ? '.small' : ''), h('div.titles', h('span.skeleton.sk-heading'))), h('main.view', skeleton(name)));
        if (!FULL_SCREEN.includes(name)) content.appendChild(bottomNav(tab));
        root.replaceChildren(sideNav(tab), content);
    }

    async function render() {
        if (!S.user) return renderAuth();
        if (!S.loaded) {
            root.replaceChildren(UI.skeletonScreen());
            return;
        }
        if (!S.state.user.username) return renderUsername();
        const r = route();
        const waiting = setTimeout(() => {
            if (route().name === r.name) showSkeleton(r.name);
        }, 180);
        const views = {home: viewHome, path: viewPath, course: viewCourse, lesson: viewLesson, exam: viewExam, daily: viewDaily, gym: viewGym, board: viewBoard, friends: viewFriends, games: viewGames, game: viewGame, play: viewPlay, stats: viewStats, profile: viewProfile};
        let v;
        try {
            v = await (views[r.name] || viewHome)(r.a, r.b);
        } catch (e) {
            v = {title: t('Something went wrong'), body: banner('error', 'warning', e.message), back: 'home'};
        }
        clearTimeout(waiting);
        if (route().name !== r.name) return; // the user has moved on while this was loading
        const bar = v.back
            ? h('header.appbar.small', iconBtn('back', t('Back'), () => go(v.back)), h('div.titles', h('h1', v.title)), S.state ? gradeChip() : null, S.state ? sparksChip() : null, v.actions || [])
            : h('header.appbar', h('div.titles', h('h1', v.title)), gradeChip(), sparksChip(), v.actions || [],
                h('button.avatar-btn', {type: 'button', 'aria-label': t('Profile and settings'), onclick: () => go('profile')}, avatar(S.user.name)));
        const nav = v.nav !== false;
        const main = h('main.view' + (!nav ? '.no-nav' : '.no-fab'), v.body);
        const content = h('div.content', bar, main);
        if (nav) content.appendChild(bottomNav(v.tab));
        root.replaceChildren(sideNav(v.tab), content);
        document.title = (v.title ? v.title + ' · ' : '') + 'Fucina';
        window.scrollTo(0, 0);
        if (v.after) setTimeout(v.after, 60);
    }

    async function refresh() {
        applyState(await Api.get('state'));
    }

    // ---------------------------------------------------------------- home

    function rankCard() {
        const l = S.state.level;
        return h('section.rank.r-' + l.rankId, h('div.medal', h('small', t('Grade')), h('b', String(l.level))),
            h('div.rank-body',
                h('div.rank-top', h('div.grow', h('span.rank-label', t('Your grade')), h('strong', l.rank)),
                    h('div.streak' + (S.state.practicedToday ? '.lit' : ''), {title: t('Days in a row')}, icon('fire'), h('b', String(S.state.user.streak)))),
                h('div.bar', {role: 'progressbar', 'aria-valuenow': l.into, 'aria-valuemax': l.need}, h('i', {style: {width: Math.round(l.into * 100 / l.need) + '%'}})),
                h('p.rank-xp', t('{n} XP to the next grade', {n: l.need - l.into}))));
    }

    /** The next thing to do: the first open course with lessons left. */
    function nextStep() {
        return S.state.courses.find(c => !c.locked && !c.examScore && c.done < c.lessons) || S.state.courses.find(c => !c.locked && !c.certificate);
    }

    /** Favilla says one thing at a time, about what matters now. */
    function favillaSays() {
        const st = S.state;
        const line = !st.totals.lessons && !st.daily ? t('Hi, I’m Favilla! Shall we light the forge with the first level?')
            : !st.practicedToday && st.user.streak > 0 ? t('{n} days in a row: don’t let the fire go out!', {n: st.user.streak})
                : !st.daily ? t('The daily challenge is waiting: the podium is up for grabs!')
                    : t('Nice work today. See you tomorrow at the forge!');
        return h('div.pet-say', mascot(st.practicedToday ? 'happy' : 'wow', 'pet-sm'), h('p', line));
    }

    function viewHome() {
        const n = nextStep();
        const d = S.state.daily;
        return {
            title: t('Hi {name}', {name: S.user.name.split(' ')[0]}), tab: 'home', body: [
                favillaSays(),
                rankCard(),
                n ? h('a.cta.fam-' + n.family, {href: '#/course/' + n.id}, h('span.kicker', n.done ? t('Pick up where you left off') : t('Start here')), h('strong', n.title),
                    h('div.bar', h('i', {style: {width: Math.round(n.done * 100 / Math.max(1, n.lessons)) + '%'}})),
                    h('div.cta-foot', h('span', n.done < n.lessons ? t('Level {a} of {b}', {a: n.done + 1, b: n.lessons}) : t('Ready for the exam')), h('span.cta-go', icon('play')))) : null,
                h('h2.section-title', t('Today')),
                h('div.quick',
                    quick('#/daily', 'calendar', 'sun', t('Daily challenge'), d ? t('Done: {a} out of {b}', {a: d.correct, b: d.total}) : t('Five questions from all the courses'), d ? 'check' : null),
                    quick('#/gym', 'bolt', 'mint', t('Gym'), t('Six quick drills, right now')),
                    quick('#/games', 'game', 'sky', t('Play a game'), t('Extra Sparks, on top of the courses')),
                    quick('#/board', 'trophy', 'pink', t('Ranking'), S.state.board.place ? t('You are number {a} of {b}', {a: S.state.board.place, b: S.state.board.participants}) : t('Do the daily challenge to enter'))),
                h('h2.section-title', t('Your results')),
                h('div.tiles',
                    statTile('book', S.state.totals.lessons, t('Levels done')), statTile('star', S.state.totals.stars, t('Stars')),
                    statTile('cert', S.state.totals.certificates, t('Certificates')), statTile('trophy', S.state.badges.filter(b => b.earnedAt).length, t('Badges'))),
            ]
        };
    }

    function quick(href, ic, tone, title, text, mark) {
        return h('a.quick-card.' + tone, {href}, h('span.quick-ic', icon(ic)), h('strong', title), h('span', text), mark ? icon(mark, 'quick-mark') : null);
    }

    function statTile(ic, n, label) {
        return h('div.stat', icon(ic), h('strong', String(n)), h('span', label));
    }

    // ---------------------------------------------------------------- path of courses

    function viewPath() {
        return {
            title: t('Path'), tab: 'path', body: [
                h('p.lead', t('Every course is a series of levels, each harder than the last. Finish a course to open the ones that follow.')),
                h('div.course-list', S.state.courses.map(courseCard)),
            ]
        };
    }

    function courseCard(c) {
        const after = c.locked ? S.state.courses.find(x => x.id === c.after) : null;
        const inner = [h('span.fam', c.family.toUpperCase()), h('div.grow', h('strong', c.title), h('p.muted', c.desc),
            c.locked ? h('p.lock-note', icon('lock'), t('Finish “{name}” or pass the exam', {name: after ? after.title : ''})) : h('div.bar', h('i', {style: {width: Math.round(c.done * 100 / Math.max(1, c.lessons)) + '%'}})),
            h('div.meta', chip(c.level), chip(t('{n} levels', {n: c.lessons})), c.certificate ? chip(t('Certified'), 'ok', 'cert') : null))];
        return h('a.course.fam-' + c.family + (c.locked ? '.locked' : ''), {href: '#/course/' + c.id}, inner);
    }

    async function loadCourse(id) {
        const d = await Api.get('course.get&id=' + encodeURIComponent(id));
        S.course[id] = d.course;
        return d.course;
    }

    /** How a course is going, in numbers: on a large screen the levels are a table of results. */
    function courseStats(c) {
        const drills = c.items.flatMap(l => l.drills);
        const tasks = c.items.filter(l => l.task);
        const stat = (ic, a, b, label) => h('div.stat', icon(ic), h('strong', `${a}/${b}`), h('span', label));
        return h('div.tiles.course-stats',
            stat('book', c.items.filter(l => l.progress).length, c.items.length, t('Levels done')),
            stat('star', c.items.reduce((n, l) => n + (l.progress && !l.progress.skipped ? l.progress.stars : 0), 0), c.items.length * 3, t('Stars')),
            stat('bolt', drills.filter(d => d.stars > 0).length, drills.length, t('Drills')),
            stat('code', tasks.filter(l => l.progress && l.progress.taskDone).length, tasks.length, t('Exercises')));
    }

    function levelList(c, firstOpen) {
        return h('ol.levels.fam-' + c.family, c.items.map((l, i) => {
            const open = !c.locked && (l.progress || i === firstOpen || c.examScore > 0);
            const solved = l.drills.filter(d => d.stars > 0).length;
            const body = [h('span.lvl-num' + (l.progress ? '.cleared' : ''), {title: l.progress && l.progress.skipped ? t('Passed with the exam') : null}, l.progress ? icon(l.progress.skipped ? 'bolt' : 'check') : open ? String(i + 1) : icon('lock')),
                h('div.grow', h('span.kicker', t('Level {n}', {n: i + 1})), h('strong', l.title)),
                difficultyDots(l.difficulty),
                l.drills.length ? h('span.lvl-stat', {title: t('Drills')}, icon('bolt'), `${solved}/${l.drills.length}`) : null,
                l.task ? h('span.lvl-stat' + (l.progress && l.progress.taskDone ? '.yes' : ''), {title: t('Exercises')}, icon('code')) : null,
                l.progress ? progressMark(l.progress) : stars(0)];
            return h('li', open ? h('a.level' + (i === firstOpen ? '.current' : ''), {href: '#/lesson/' + c.id + '/' + l.id}, body) : h('div.level.locked', body));
        }));
    }

    /** The levels as a winding trail: the path is drawn behind nodes placed at fixed heights. */
    function levelMap(c, firstOpen) {
        const ROW = 158;
        const lanes = [50, 72, 78, 62, 38, 22, 28, 44];
        const spots = c.items.map((_, i) => ({x: lanes[i % lanes.length], y: ROW * i + 58}));
        const height = ROW * c.items.length + 8;
        const line = list => list.map((p, i) => {
            if (!i) return `M${p.x} ${p.y}`;
            const q = list[i - 1], mid = (q.y + p.y) / 2;
            return `C${q.x} ${mid} ${p.x} ${mid} ${p.x} ${p.y}`;
        }).join(' ');
        const reached = firstOpen === -1 ? spots : spots.slice(0, firstOpen + 1);
        return h('ol.map.fam-' + c.family, {style: {height: height + 'px'}},
            h('div.map-line', {
                'aria-hidden': 'true',
                html: `<svg viewBox="0 0 100 ${height}" preserveAspectRatio="none"><path class="way" d="${line(spots)}"/><path class="walked" d="${line(reached)}"/></svg>`
            }),
            c.items.map((l, i) => {
                const open = !c.locked && (l.progress || i === firstOpen || c.examScore > 0);
                const current = !c.locked && i === firstOpen;
                const face = l.progress ? icon(l.progress.skipped ? 'bolt' : 'check') : open ? String(i + 1) : icon('lock');
                const inner = [current ? h('span.node-go', i ? t('Next') : t('Start!')) : null, h('span.node-dot', face),
                    h('span.node-name', l.title), l.progress ? progressMark(l.progress) : difficultyDots(l.difficulty)];
                const cls = current ? '.current' : l.progress ? '.cleared' : '';
                return h('li.node-spot', {style: {left: spots[i].x + '%', top: spots[i].y + 'px'}}, open ? h('a.node' + cls, {href: '#/lesson/' + c.id + '/' + l.id}, inner) : h('div.node.locked', inner));
            }));
    }

    async function viewCourse(id) {
        const c = await loadCourse(id);
        const firstOpen = c.items.findIndex(l => !l.progress);
        const allDone = firstOpen === -1;
        return {
            // On a phone the trail is long: it opens on the level to play, or on the last one reached
            after: () => {
                if (DESK.matches) return;
                const spot = document.querySelector('.node.current') || [...document.querySelectorAll('.node.cleared')].pop();
                if (spot) spot.scrollIntoView({block: 'center'});
            },
            title: c.title, back: 'path', tab: 'path', body: [
                h('p.lead', c.desc),
                c.locked ? banner('warn', 'lock', t('This course is still locked'), t('Finish the previous course, or pass the final exam below to skip ahead.')) : null,
                DESK.matches ? [courseStats(c), levelList(c, firstOpen)] : levelMap(c, firstOpen),
                examCard(c, allDone),
            ]
        };
    }

    function examCard(c, allDone) {
        const passed = c.examScore > 0 && !c.certificate;
        const perfect = c.examScore >= 100;
        const text = perfect && c.certificate ? t('Passed with full marks: there is nothing left to improve.') : passed ? t('Exam passed with {n}%: unlock your certificate with {c} Sparks.', {n: c.examScore, c: S.state.costs.certificate})
            : (allDone || c.examScore ? '' : t('Already know this? Take it now: if you pass, the course counts as finished and the next ones open.') + ' ')
            + t('{n} questions, pass with {p}%. Then unlock the certificate with {c} Sparks.', {n: c.examQuestions, p: c.examPass, c: S.state.costs.certificate});
        return h('section.card.exam-card', tile('icon', 'cert', 'lg'), h('div.grow', h('strong', t('Final exam')), h('p.muted', text),
            c.certificate ? h('div.btn-row', h('a.btn.btn-tonal', {href: certUrl(c.certificate.code), target: '_blank', rel: 'noopener'}, icon('cert'), h('span', t('Open the certificate')))) : null),
            passed ? btn(t('Unlock · {n}', {n: S.state.costs.certificate}), {icon: 'spark', onclick: () => claimCertificate(c.id, render)}) : null,
            perfect ? null : btn(c.certificate || passed ? t('Try again') : t('Start'), {kind: c.certificate || passed ? 'text' : 'filled', disabled: !c.items.length, onclick: () => go('exam/' + c.id)}));
    }

    /** The certificate is the prize you buy with what you earned: the price is shown first. */
    async function claimCertificate(courseId, done) {
        const cost = S.state.costs.certificate, have = S.state.user.points;
        if (have < cost) {
            const s = sheet({
                title: t('{n} Sparks to go', {n: cost - have}),
                body: [h('div.levelup', mascot('sad', 'lg'), h('p', t('The certificate costs {c} Sparks and you have {m}. Games, drills and the daily challenge earn more.', {c: cost, m: have})))],
                actions: [btn(t('Later'), {kind: 'text', onclick: () => s.close()}), btn(t('Play a game'), {
                    onclick: () => {
                        s.close();
                        setTimeout(() => go('games'), 150);
                    }
                })]
            });
            return;
        }
        if (!await confirm(t('Unlock the certificate?'), t('It costs {n} Sparks. You have {m}.', {n: cost, m: have}), t('Use {n} Sparks', {n: cost}))) return;
        try {
            const d = await Api.post('certificate.claim', {course: courseId});
            applyState(d);
            delete S.course[courseId];
            confetti();
            toast(t('Certificate unlocked!'), 'ok');
            celebrate({...d, xp: 0});
            done(d);
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    // ---------------------------------------------------------------- a lesson

    /** A hint is paid in Sparks: the price is shown before spending them. */
    async function askHint(cost, body) {
        if (S.state.user.points < cost) {
            toast(t('You need {n} Sparks: you have {m}.', {n: cost, m: S.state.user.points}), 'error');
            return null;
        }
        if (!await confirm(t('Use a hint?'), t('It costs {n} Sparks. You have {m}.', {n: cost, m: S.state.user.points}), t('Use {n} Sparks', {n: cost}))) return null;
        const d = await Api.post('hint.buy', body);
        S.state.user.points = d.points;
        showSparks();
        return d.hint;
    }

    async function viewLesson(courseId, slug) {
        const c = S.course[courseId] || await loadCourse(courseId);
        const lesson = c.items.find(l => l.id === slug);
        if (!lesson) throw new Error(t('Lesson not found.'));
        const stage = h('div.stage');
        const firstAnswers = {};
        let taskDone = false;

        const steps = [{kind: 'theory'}, ...lesson.drills.map(d => ({kind: 'drill', d})), ...lesson.quiz.map(q => ({kind: 'quiz', q})), ...(lesson.task ? [{kind: 'task'}] : [])];
        const bar = h('i');
        let step = 0;

        function show() {
            bar.style.width = Math.round(step * 100 / steps.length) + '%';
            const s = steps[step];
            if (!s) return finish();
            stage.replaceChildren(s.kind === 'theory' ? theory() : s.kind === 'drill' ? drillView(s.d, next) : s.kind === 'quiz' ? quiz(s.q) : taskView());
            window.scrollTo(0, 0);
        }

        const next = () => {
            step++;
            show();
        };

        function theory() {
            return h('article.theory', lesson.body.map(b => b.t === 'code' ? codeBlock(b.code, b.lang) : b.t === 'tip' ? h('aside.tip', mascot('happy', 'still'), h('p', rich(b.text))) : h('p', rich(b.text))),
                h('div.step-actions', btn(lesson.drills.length ? t('Let’s play') : t('Go to the questions'), {icon: 'right', onclick: next})));
        }

        function quiz(q) {
            const buttons = q.options.map((o, i) => h('button.option', {type: 'button', onclick: () => answer(i)}, h('b.opt-key', 'ABCDEFGH'[i]), h('span', rich(o))));
            const feedback = h('div.feedback');
            const actions = h('div.step-actions');
            const hintBtn = btn(t('Hint · {n}', {n: S.state.costs.question}), {
                kind: 'text', icon: 'bulb', onclick: async () => {
                    const hint = await askHint(S.state.costs.question, {kind: 'question', course: c.id, question: q.id}).catch(e => toast(e.message, 'error'));
                    if (!hint) return;
                    hint.remove.forEach(i => {
                        buttons[i].disabled = true;
                        buttons[i].classList.add('gone');
                    });
                    if (hint.text) feedback.replaceChildren(h('aside.tip', icon('bulb'), h('p', rich(hint.text))));
                    hintBtn.disabled = true;
                }
            });

            async function answer(i) {
                buttons.forEach(b => b.disabled = true);
                hintBtn.disabled = true;
                try {
                    const d = await Api.post('quiz.check', {course: c.id, question: q.id, answer: i});
                    firstAnswers[q.id] = i;
                    buttons[d.answer].classList.add('right');
                    if (!d.correct) buttons[i].classList.add('wrong');
                    feedback.replaceChildren(h('aside.result.' + (d.correct ? 'ok' : 'ko'), mascot(d.correct ? 'happy' : 'sad'), h('div.grow', h('strong', d.correct ? t('Correct!') : t('Not quite')), h('p', rich(d.why)))));
                    actions.replaceChildren(btn(t('Continue'), {icon: 'right', onclick: next}));
                } catch (e) {
                    toast(e.message, 'error');
                    buttons.forEach(b => b.disabled = false);
                }
            }

            actions.append(hintBtn);
            return h('section.quiz', h('h2', rich(q.q)), h('div.options', buttons), feedback, actions);
        }

        function taskView() {
            const task = lesson.task;
            const ta = h('textarea.editor', {spellcheck: false, autocapitalize: 'off', autocomplete: 'off', value: task.start, 'aria-label': t('Your code')});
            const live = task.lang === 'html' || task.lang === 'css';
            const frame = live ? h('iframe.preview', {sandbox: 'allow-same-origin', title: t('Preview')}) : null;
            const list = h('ul.checks', task.checks.map(ch => h('li', icon('check'), h('span', rich(ch.label)))));
            const hintBox = h('div');
            const page = () => task.lang === 'css' ? `<!doctype html><style>${ta.value}</style>${task.html}` : ta.value;
            let timer = null;

            ta.addEventListener('keydown', e => {
                if (e.key !== 'Tab') return;
                e.preventDefault();
                ta.setRangeText('  ', ta.selectionStart, ta.selectionEnd, 'end');
                ta.dispatchEvent(new Event('input'));
            });
            const refreshFrame = () => new Promise(resolve => {
                if (!frame) return resolve();
                frame.addEventListener('load', () => resolve(), {once: true});
                frame.srcdoc = page();
            });
            ta.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(refreshFrame, 300);
            });

            async function verify() {
                await refreshFrame();
                const doc = frame ? frame.contentDocument : null;
                const results = task.checks.map(ch => runCheck(ch, ta.value, doc));
                [...list.children].forEach((li, i) => li.className = results[i] ? 'ok' : 'ko');
                if (results.every(Boolean)) {
                    taskDone = true;
                    actions.replaceChildren(btn(t('Continue'), {icon: 'right', onclick: next}));
                    toast(t('Exercise solved!'), 'ok');
                } else toast(t('Not yet: check the list.'), 'error');
            }

            const hintBtn = btn(t('Hint · {n}', {n: S.state.costs.task}), {
                kind: 'text', icon: 'bulb', onclick: async () => {
                    const hint = await askHint(S.state.costs.task, {kind: 'task', course: c.id, lesson: lesson.id}).catch(e => toast(e.message, 'error'));
                    if (hint) hintBox.replaceChildren(hint.code ? codeBlock(hint.code, t('Hint')) : h('p.muted', t('No hint for this exercise.')));
                }
            });
            const actions = h('div.step-actions', hintBtn, btn(t('Skip'), {kind: 'text', onclick: next}), btn(t('Check'), {icon: 'check', onclick: verify}));
            setTimeout(refreshFrame);
            return h('section.task', h('h2', t('Now you try')), h('p', rich(task.prompt)),
                task.lang === 'css' ? h('details.page-src', h('summary', t('The page your CSS applies to')), codeBlock(task.html, 'html')) : null,
                h('div.workbench' + (live ? '' : '.single'), ta, frame), list, hintBox, actions);
        }

        async function finish() {
            stage.replaceChildren(h('div.loader'));
            try {
                const d = await Api.post('lesson.complete', {course: c.id, lesson: lesson.id, answers: firstAnswers, taskDone});
                applyState(d);
                delete S.course[c.id];
                bar.style.width = '100%';
                const index = c.items.indexOf(lesson);
                const following = c.items[index + 1];
                if (d.stars === 3) confetti();
                stage.replaceChildren(h('section.done', mascot(d.stars === 3 ? 'wow' : 'happy', 'lg'), stars(d.stars), h('h2', d.stars === 3 ? t('Perfect level!') : t('Level complete')),
                    h('p', t('{a} correct answers out of {b}', {a: d.correct, b: d.total})), h('p.gain', icon('spark'), t('+{n} XP and Sparks', {n: d.xp})),
                    h('div.step-actions', btn(t('Back to the course'), {kind: 'tonal', onclick: () => go('course/' + c.id)}),
                        following ? btn(t('Next level'), {icon: 'right', onclick: () => go('lesson/' + c.id + '/' + following.id)}) : btn(t('Go to the exam'), {icon: 'cert', onclick: () => go('exam/' + c.id)}))));
                celebrate({...d, xp: 0});
            } catch (e) {
                stage.replaceChildren(banner('error', 'warning', e.message));
            }
        }

        show();
        return {title: lesson.title, back: 'course/' + c.id, tab: 'path', nav: false, body: [h('div.bar.top', bar), stage]};
    }

    /** A drill, wherever it is played. The result is saved in the background: the learner goes on at once. */
    function drillView(d, next) {
        return Drills.render(d, {
            rich, cost: S.state.costs.drill, next, skip: next,
            buyHint: async () => !!await askHint(S.state.costs.drill, {kind: 'drill', drill: d.id}).catch(e => toast(e.message, 'error')),
            onDone(mistakes) {
                Api.post('drill.finish', {drill: d.id, mistakes}).then(r => {
                    applyState(r);
                    if (r.xp) toast(t('+{n} XP and Sparks', {n: r.xp}), 'ok');
                    celebrate({...r, xp: 0});
                }).catch(e => toast(e.message, 'error'));
            }
        });
    }

    /** One requirement of an exercise, against the written source and the rendered page. */
    function runCheck(ch, source, doc) {
        try {
            if (ch.re) return new RegExp(ch.re, 'im').test(source);
            if (!doc) return false;
            const el = doc.querySelector(ch.sel);
            if (!el) return false;
            if (ch.prop) return doc.defaultView.getComputedStyle(el).getPropertyValue(ch.prop).trim() === ch.value;
            return !ch.text || el.textContent.includes(ch.text);
        } catch (e) {
            return false;
        }
    }

    // ---------------------------------------------------------------- questions in a row: exam and daily challenge

    /** A clock that counts down to zero and calls onEnd, or counts up without end. It stops when it leaves the page. */
    function clock(seconds, down, onEnd) {
        const el = h('div.exam-clock', icon('timer'), h('b'));
        const started = Date.now();
        const show = s => `${Math.floor(s / 60)}:${String(s % 60).padStart(2, '0')}`;
        let id = 0;
        const tick = () => {
            const gone = Math.floor((Date.now() - started) / 1000);
            const value = down ? Math.max(0, seconds - gone) : seconds + gone;
            el.lastChild.textContent = show(value);
            el.classList.toggle('low', down && value <= 60);
            if (down && value === 0) {
                clearInterval(id);
                onEnd();
            } else if (gone > 1 && !el.isConnected) clearInterval(id);
        };
        tick();
        id = setInterval(tick, 500);
        el.stop = () => clearInterval(id);
        return el;
    }

    /** Asks the questions one by one and resolves with the answers, keyed by keyOf(question). The result ends it early. */
    function askAll(stage, bar, questions, keyOf, onDone) {
        const answers = {};
        let i = 0, closed = false;
        const finish = () => {
            if (closed) return;
            closed = true;
            onDone(answers);
        };
        const next = () => {
            bar.style.width = Math.round(i * 100 / questions.length) + '%';
            if (i >= questions.length) return finish();
            const q = questions[i];
            stage.replaceChildren(h('section.quiz', h('div.quiz-head', h('span.kicker', t('Question {a} of {b}', {a: i + 1, b: questions.length})), langChip(q.family)), h('h2', rich(q.q)),
                h('div.options', q.options.map((o, k) => h('button.option', {
                    type: 'button', onclick: () => {
                        answers[keyOf(q)] = k;
                        i++;
                        next();
                    }
                }, h('b.opt-key', 'ABCDEFGH'[k]), h('span', rich(o)))))));
            window.scrollTo(0, 0);
        };
        next();
        return finish;
    }

    function reviewList(items) {
        return items.length ? h('div.review', items.map(r => h('div.card.review-item', h('p', h('strong', rich(r.q))), h('p.ok', icon('check'), h('span', rich(r.answer))), h('p.muted', rich(r.why))))) : null;
    }

    async function viewExam(courseId) {
        const c = S.state.courses.find(x => x.id === courseId);
        const stage = h('div.stage');
        const bar = h('i');
        const slot = h('div.clock-slot');

        function intro() {
            if (c.examScore >= 100) {
                stage.replaceChildren(h('section.done', mascot('wow', 'lg'), h('h2', c.title), h('p', t('Passed with full marks: there is nothing left to improve.')), h('div.step-actions', btn(t('Back to the course'), {onclick: () => go('course/' + courseId)}))));
                return;
            }
            stage.replaceChildren(h('section.done', mascot('wow', 'lg'), h('h2', c.title), h('p', t('No hints here: the exam is all yours. The questions are drawn at random from the course, and you have {n} minutes.', {n: S.state.examMinutes})),
                h('div.step-actions', btn(t('Start the exam'), {
                    icon: 'play', onclick: async e => {
                        const d = await busy(e.currentTarget, () => Api.post('exam.start', {course: courseId})).catch(err => toast(err.message, 'error'));
                        if (!d) return;
                        let finish = () => null;
                        const time = d.seconds ? clock(d.seconds, true, () => {
                            toast(t('Time is up!'), 'error');
                            finish();
                        }) : null;
                        slot.replaceChildren(...(time ? [time] : []));
                        finish = askAll(stage, bar, d.questions, q => q.id, a => {
                            if (time) time.stop();
                            slot.replaceChildren();
                            submit(d.exam, a);
                        });
                    }
                }))));
        }

        async function submit(exam, answers) {
            stage.replaceChildren(h('div.loader'));
            try {
                const d = await Api.post('exam.submit', {exam, answers});
                applyState(d);
                delete S.course[courseId];
                if (d.passed) confetti();
                stage.replaceChildren(h('section.done', mascot(d.passed ? 'wow' : 'sad', 'lg'), h('h2', d.passed ? t('You passed!') : t('Not this time')),
                    d.expired ? h('p', t('Time ran out before the answers arrived.')) : null, h('p.score', d.score + '%'), h('p', t('{a} correct answers out of {b}', {a: d.correct, b: d.total})),
                    d.passed && d.xp ? h('p.gain', icon('spark'), t('+{n} XP and Sparks', {n: d.xp})) : null,
                    d.passed ? h('p', t('The course counts as finished: its levels are solved and the next courses are open.')) : null,
                    d.passed && d.certificate ? h('div.step-actions', h('a.btn.btn-filled', {href: certUrl(d.certificate.code), target: '_blank', rel: 'noopener'}, icon('cert'), h('span', t('Open the certificate')))) : null,
                    d.passed && !d.certificate ? h('div.step-actions', btn(t('Unlock the certificate · {n}', {n: S.state.costs.certificate}), {icon: 'spark', onclick: () => claimCertificate(courseId, () => go('course/' + courseId))})) : null,
                    h('div.step-actions', btn(t('Back to the course'), {kind: 'tonal', onclick: () => go('course/' + courseId)}), !d.passed ? btn(t('Try again'), {onclick: intro}) : null),
                    d.review.length ? h('h3', t('To review')) : null, reviewList(d.review)));
                celebrate({...d, xp: 0});
            } catch (e) {
                stage.replaceChildren(banner('error', 'warning', e.message));
            }
        }

        if (!c) throw new Error(t('Course not found.'));
        intro();
        return {title: t('Final exam'), back: 'course/' + courseId, tab: 'path', nav: false, body: [h('div.bar.top', bar), slot, stage]};
    }

    async function viewDaily() {
        const stage = h('div.stage');
        const bar = h('i');
        const slot = h('div.clock-slot');
        if (S.state.daily) {
            stage.replaceChildren(h('section.done', mascot('happy', 'lg'), h('h2', t('See you tomorrow')), h('p', t('Today you got {a} out of {b}.', {a: S.state.daily.correct, b: S.state.daily.total})),
                h('div.step-actions', btn(t('Play a game'), {onclick: () => go('games')}))));
        } else {
            const d = await Api.get('daily.get');
            const time = clock(d.elapsed || 0, false);
            slot.replaceChildren(time);
            askAll(stage, bar, d.questions, q => q.key, async answers => {
                time.stop();
                slot.replaceChildren();
                stage.replaceChildren(h('div.loader'));
                try {
                    const r = await Api.post('daily.submit', {answers});
                    applyState(r);
                    if (r.correct === r.total) confetti();
                    stage.replaceChildren(h('section.done', mascot(r.correct === r.total ? 'wow' : 'happy', 'lg'), stars(r.correct === r.total ? 3 : r.correct >= 3 ? 2 : 1), h('h2', t('{a} out of {b}', {a: r.correct, b: r.total})), r.seconds ? h('p', t('Time: {n} seconds', {n: r.seconds})) : null, h('p.gain', icon('spark'), t('+{n} XP and Sparks', {n: r.xp})),
                        h('div.step-actions', btn(t('Back home'), {onclick: () => go('home')})), reviewList(r.review.filter(x => !x.ok))));
                    celebrate({...r, xp: 0});
                } catch (e) {
                    stage.replaceChildren(banner('error', 'warning', e.message));
                }
            });
        }
        return {title: t('Daily challenge'), back: 'home', tab: 'home', nav: false, body: [h('div.bar.top', bar), slot, stage]};
    }

    // ---------------------------------------------------------------- gym

    async function viewGym() {
        const d = await Api.get('drill.mix');
        const stage = h('div.stage');
        const bar = h('i');
        let i = 0;

        function next() {
            bar.style.width = Math.round(i * 100 / Math.max(1, d.drills.length)) + '%';
            if (i >= d.drills.length) {
                confetti();
                stage.replaceChildren(h('section.done', mascot('wow', 'lg'), h('h2', t('Workout complete!')), h('p', t('Your hands are getting faster.')),
                    h('div.step-actions', btn(t('Back home'), {kind: 'tonal', onclick: () => go('home')}), btn(t('Again'), {icon: 'bolt', onclick: render}))));
            } else stage.replaceChildren(drillView(d.drills[i++], next));
            window.scrollTo(0, 0);
        }

        if (d.drills.length) next();
        else stage.replaceChildren(empty(t('The gym opens soon'), t('Start the first level of a course to unlock the drills.'), btn(t('Open the path'), {onclick: () => go('path')})));
        return {title: t('Gym'), back: 'home', tab: 'home', nav: false, body: [d.drills.length ? h('div.bar.top', bar) : null, stage]};
    }

    // ---------------------------------------------------------------- games

    function viewGames() {
        return {
            title: t('Games'), tab: 'games', body: [
                h('p.lead', t('Every game has levels that get harder. Win one to open the next.')),
                h('div.game-sparks', icon('spark'), h('div.grow', h('strong', t('Extra Sparks, on top of the courses')), h('div.bar', h('i', {style: {width: Math.round(S.state.gameSparks.today * 100 / Math.max(1, S.state.gameSparks.cap)) + '%'}}))), h('b', `${S.state.gameSparks.today}/${S.state.gameSparks.cap}`)),
                quick('#/gym', 'bolt', 'mint.wide', t('Gym'), t('Six quick drills, right now')),
                h('div.game-list', S.state.games.map(g => {
                    const won = Object.keys(g.won).length;
                    return h('a.card.game-card', {href: '#/game/' + g.id}, tile('icon', g.icon, 'lg'), h('div.grow', h('strong', g.title), h('p.muted', g.desc),
                        h('div.bar', h('i', {style: {width: Math.round(won * 100 / g.levels) + '%'}})), h('span.muted.small', t('{a} of {b} levels', {a: won, b: g.levels}))));
                })),
            ]
        };
    }

    function viewGame(id) {
        const g = S.state.games.find(x => x.id === id);
        if (!g) throw new Error(t('Game not found.'));
        return {
            title: g.title, back: 'games', tab: 'games', body: [
                h('p.lead', g.desc),
                h('div.game-levels', Array.from({length: g.levels}, (_, i) => {
                    const n = i + 1;
                    const open = n <= g.unlocked;
                    const inner = [h('b', open ? String(n) : icon('lock')), g.won[n] ? stars(g.won[n]) : h('span.muted.small', open ? t('Play') : '')];
                    return open ? h('a.glevel', {href: `#/play/${id}/${n}`}, inner) : h('div.glevel.locked', inner);
                })),
            ]
        };
    }

    /** The guided round of a game: by itself the first time, then from the ? button (see assets/tutorials.js). */
    const gameGuide = (id, title) => Tutorials.open(id, title, S.state.costs.game);

    async function viewPlay(id, levelStr) {
        const level = parseInt(levelStr, 10) || 1;
        const d = await Api.get(`game.get&id=${encodeURIComponent(id)}&level=${level}`);
        const stage = h('div.game-stage');
        const started = Date.now();
        let finished = false;
        const hintBtn = btn(t('Help · {n}', {n: S.state.costs.game}), {kind: 'tonal', icon: 'bulb'});
        Games[id]({
            root: stage, level, levels: d.game.levels, items: d.items, questions: d.questions, t, rich,
            /** Registers what the help button does; it is paid first. */
            onHint(fn) {
                hintBtn.onclick = async () => {
                    const paid = await askHint(S.state.costs.game, {kind: 'game', game: id}).catch(e => toast(e.message, 'error'));
                    if (paid) fn();
                };
            },
            async finish(result) {
                if (finished) return;
                finished = true;
                try {
                    const r = await Api.post('game.finish', {game: id, level, won: result.won, stars: result.stars, score: result.score || 0, seconds: Math.round((Date.now() - started) / 1000)});
                    applyState(r);
                    const nextLevel = result.won && level < d.game.levels;
                    const s = sheet({
                        title: result.won ? t('Level {n} won!', {n: level}) : t('Level lost'), persistent: true,
                        body: [h('div.levelup', mascot(result.won ? 'wow' : 'sad', 'lg'), result.won ? stars(result.stars) : null, result.note ? h('p', result.note) : null, r.xp ? h('p.gain', icon('spark'), t('+{n} XP and Sparks', {n: r.xp})) : h('p.muted', r.capped ? t('Daily XP limit for games reached: you can still play.') : t('No XP this time.')))],
                        actions: [btn(t('Levels'), {
                            kind: 'text', onclick: () => {
                                s.close();
                                setTimeout(() => go('game/' + id), 150);
                            }
                        }), btn(nextLevel ? t('Next level') : t('Play again'), {
                            onclick: () => {
                                s.close();
                                setTimeout(() => {
                                    const target = `play/${id}/${nextLevel ? level + 1 : level}`;
                                    if (location.hash === '#/' + target) render(); else go(target);
                                }, 150);
                            }
                        })]
                    });
                    if (result.won) confetti();
                    celebrate({...r, xp: 0});
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        if (!UI.store.get('guide.' + id, false)) {
            UI.store.set('guide.' + id, true);
            setTimeout(() => gameGuide(id, d.game.title), 300);
        }
        return {title: t('{name} · level {n}', {name: d.game.title, n: level}), back: 'game/' + id, tab: 'games', nav: false, actions: [iconBtn('help', t('How to play'), () => gameGuide(id, d.game.title))], body: [stage, h('div.step-actions', hintBtn)]};
    }

    // ---------------------------------------------------------------- daily leaderboard

    async function viewBoard() {
        const {type, scope} = S.board;
        const d = await Api.get(`board.get&type=${type}&scope=${scope}`);
        const hours = Math.floor(d.secondsLeft / 3600), minutes = Math.floor(d.secondsLeft % 3600 / 60);
        const row = r => h('li.board-row' + (r.me ? '.you' : ''), h('b.place.p' + Math.min(r.place, 4), String(r.place)), avatar(r.name, 'sm'), h('span.grow', r.name), h('strong', r.score));
        const prize = (n, label) => n ? h('div.prize', h('b', label), h('span', icon('spark'), String(n))) : null;
        const pick = key => v => {
            S.board[key] = v;
            render();
        };
        const daily = type === 'daily';
        return {
            title: t('Ranking'), tab: 'board', body: [
                h('section.board-head', mascot(d.me && d.me.place === 1 ? 'wow' : 'happy'), h('div.grow', h('strong', daily ? t('Daily challenge') : t('Today’s forge')), h('p', d.participants === 1 ? t('1 participant') : t('{n} participants', {n: d.participants})), h('p.small', t('Closes in {h} h {m} min', {h: hours, m: minutes})))),
                seg([['daily', t('Daily challenge')], ['xp', t('Today’s XP')]], type, pick('type')),
                seg([['all', t('Everyone')], ['friends', t('Friends')]], scope, pick('scope')),
                h('p.lead', daily ? t('Most right answers first; at a tie, the fastest. The top three win Sparks: the more people take part, the bigger the prize.') : t('Who earned the most XP today. This one is just for fun: the prizes go to the daily challenge.')),
                daily && scope === 'all' && d.prizes[0] ? h('div.prizes', prize(d.prizes[0], '1°'), prize(d.prizes[1], '2°'), prize(d.prizes[2], '3°')) : null,
                d.rows.length ? h('ol.board', d.rows.map(row)) : empty(t('Nobody yet today'), daily ? t('Do the daily challenge and take the lead.') : t('Finish a level, a drill or a game and take the lead.'), daily && !S.state.daily ? btn(t('Daily challenge'), {onclick: () => go('daily')}) : null),
                d.me && d.me.place > 20 ? h('ol.board', {start: d.me.place}, row(d.me)) : null,
                !d.onBoard ? banner('info', 'info', t('You are not in the ranking'), t('You can join again from your profile.')) : null,
                h('a.btn.btn-tonal.btn-block', {href: '#/friends'}, icon('group'), h('span', t('Find and manage friends'))),
                daily && scope === 'all' && d.yesterday.length ? [h('h2.section-title', t('Yesterday’s podium')), h('ol.board', d.yesterday.map(y => h('li.board-row', h('b.place.p' + y.place, String(y.place)), avatar(y.name, 'sm'), h('span.grow', y.name), h('strong.won', icon('spark'), '+' + y.points))))] : null,
            ]
        };
    }

    // ---------------------------------------------------------------- friends

    async function viewFriends() {
        const list = h('div.friend-list');
        const found = h('div.friend-list');
        const line = (name, action) => h('div.friend', avatar(name, 'sm'), h('span.grow', name), action);

        async function load() {
            const d = await Api.get('friends.list');
            list.replaceChildren(...(d.friends.length ? d.friends.map(f => line(f.name, btn(t('Remove'), {
                kind: 'text', onclick: async () => {
                    if (!await confirm(t('Remove {name}?', {name: f.name}), t('You can add them again at any time.'), t('Remove'), true)) return;
                    await Api.post('friends.remove', {username: f.name}).catch(e => toast(e.message, 'error'));
                    load();
                    search();
                }
            }))) : [h('p.muted', t('No friends yet: search for a username.'))]));
        }

        const input = UI.input({autocomplete: 'off', autocapitalize: 'off', spellcheck: false, maxlength: 20, 'aria-label': t('Search by username')});
        let timer = null;

        async function search() {
            const q = input.value.trim();
            if (q.length < 2) return found.replaceChildren();
            const d = await Api.get('friends.search&q=' + encodeURIComponent(q)).catch(() => ({results: []}));
            found.replaceChildren(...(d.results.length ? d.results.map(r => line(r.name, r.added ? chip(t('Friend'), 'ok', 'check') : btn(t('Add'), {
                icon: 'add', onclick: async () => {
                    try {
                        const a = await Api.post('friends.add', {username: r.name});
                        applyState(a);
                        toast(t('{name} added', {name: r.name}), 'ok');
                        celebrate({...a, xp: 0});
                        load();
                        search();
                    } catch (e) {
                        toast(e.message, 'error');
                    }
                }
            }))) : [h('p.muted', t('Nobody with this username.'))]));
        }

        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(search, 250);
        });
        await load();
        return {
            title: t('Friends'), back: 'board', tab: 'board', body: [
                h('p.lead', t('Your username is @{name}. Friends only see your username and your results in the rankings.', {name: S.state.user.username})),
                UI.field(t('Search by username'), input), found,
                h('h2.section-title', t('Your friends')), list,
            ]
        };
    }

    /** Prizes won while away, shown once. */
    function showPrizes() {
        const list = S.state.prizes || [];
        if (!list.length) return;
        const total = list.reduce((n, p) => n + p.points, 0);
        const best = Math.min(...list.map(p => p.place));
        confetti();
        const s = sheet({
            title: t('You made the podium!'),
            body: [h('div.levelup', mascot('wow', 'lg'), list.map(p => h('p', t('{day}: number {place} out of {n}', {day: UI.fmtDate(p.day), place: p.place, n: p.participants}))), h('p.gain', icon('spark'), t('+{n} Sparks', {n: total})))],
            actions: [btn(best === 1 ? t('Champion!') : t('Great!'), {onclick: () => s.close()})]
        });
        S.state.prizes = [];
        Api.post('board.seen', {}).catch(() => null);
    }

    // ---------------------------------------------------------------- statistics

    /** The last 16 weeks, one square per day: its colour grows with the XP of that day. */
    function calendar() {
        const byDay = Object.fromEntries(S.state.activity.map(a => [a.day, a]));
        const end = UI.parseDate(S.state.today);
        const start = new Date(end);
        start.setDate(start.getDate() - 111 - ((start.getDay() + 6) % 7) + 6 - ((end.getDay() + 6) % 7) * 0);
        const days = [];
        for (let i = 111; i >= 0; i--) {
            const d = new Date(end);
            d.setDate(end.getDate() - i);
            days.push(UI.dateStr(d));
        }
        return h('div.cal', {role: 'img', 'aria-label': t('Activity of the last 16 weeks')}, days.map(day => {
            const a = byDay[day];
            const xp = a ? a.xp : 0;
            return h('i.l' + (xp === 0 ? 0 : xp < 30 ? 1 : xp < 80 ? 2 : 3), {title: `${UI.fmtDate(day)} · ${xp} XP`});
        }));
    }

    function weekBars() {
        const byDay = Object.fromEntries(S.state.activity.map(a => [a.day, a.xp]));
        const days = Array.from({length: 7}, (_, i) => UI.addDays(S.state.today, i - 6));
        const max = Math.max(1, ...days.map(d => byDay[d] || 0));
        return h('div.week', days.map(d => h('div.wk', {title: `${byDay[d] || 0} XP`}, h('span', String(byDay[d] || '')), h('i', {style: {height: Math.round((byDay[d] || 0) * 100 / max) + '%'}}), h('small', UI.parseDate(d).toLocaleDateString(I18N.locale, {weekday: 'narrow'})))));
    }

    function viewStats() {
        const st = S.state;
        const n = st.totals;
        return {
            title: t('Stats'), tab: 'stats', body: [
                rankCard(),
                h('div.tiles', statTile('fire', st.user.bestStreak, t('Best streak')), statTile('spark', st.user.points, t('Sparks')), statTile('code', n.tasks, t('Exercises')), statTile('bolt', n.drills, t('Drills')), statTile('game', n.games, t('Games played')), statTile('calendar', n.daily, t('Daily challenges')), statTile('trophy', n.wins, t('Wins'))),
                h('h2.section-title', t('This week')), h('div.card.pad', weekBars()),
                h('h2.section-title', t('Activity')), h('div.card.pad', calendar()),
                h('h2.section-title', t('Certificates')),
                st.courses.some(c => c.certificate) ? h('div.group', st.courses.filter(c => c.certificate).map(c => certRow(c))) : h('p.muted', t('Finish a course and pass its exam to earn the first one.')),
                h('h2.section-title', t('Badges') + ` · ${st.badges.filter(b => b.earnedAt).length}/${st.badges.length}`),
                h('div.badges', [...st.badges].sort((a, b) => !!b.earnedAt - !!a.earnedAt).map(b => h('div.badge' + (b.earnedAt ? '.on' : ''), {title: b.desc}, tile('icon', b.icon), h('strong', b.title), h('span', b.desc)))),
            ]
        };
    }

    function certRow(c) {
        return h('div.cert-row', tile('icon', 'cert'), h('div.grow', h('strong', c.title), h('p.muted', `${c.certificate.score}% · ${UI.fmtDate(c.certificate.issuedAt.slice(0, 10))} · ${c.certificate.name}`)),
            iconBtn('edit', t('Name on the certificate'), () => renameCertificate(c)),
            h('a.icon-btn', {href: certUrl(c.certificate.code), target: '_blank', rel: 'noopener', 'aria-label': t('Open the certificate')}, icon('right')));
    }

    function renameCertificate(c) {
        const name = UI.input({value: c.certificate.name, maxlength: 80});
        const save = btn(t('Save'), {
            onclick: async () => {
                try {
                    applyState(await busy(save, () => Api.post('certificate.rename', {code: c.certificate.code, name: name.value})));
                    s.close();
                    setTimeout(render, 150);
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = sheet({title: t('Name on the certificate'), body: [UI.field(t('Full name'), name)], actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), save]});
    }

    // ---------------------------------------------------------------- profile

    function viewProfile() {
        const row = (ic, label, onclick, danger) => h('button.setting' + (danger ? '.danger' : ''), {type: 'button', onclick}, icon(ic), h('span', label), icon('right'));
        const u = S.state.user;
        return {
            title: t('Profile'), back: 'home', tab: 'profile', body: [
                h('div.card.pad.profile-head', avatar(u.name, 'lg'), h('div.grow', h('strong', u.name), h('p.muted', '@' + u.username), h('p.muted', u.email))),
                rankCard(),
                h('h2.section-title', t('Preferences')),
                seg(I18N.langs, I18N.lang, I18N.set),
                seg([['auto', t('Automatic')], ['light', t('Light')], ['dark', t('Dark')]], UI.store.get('theme', 'auto'), v => {
                    UI.store.set('theme', v);
                    UI.applyTheme();
                }),
                h('h2.section-title', t('Ranking')),
                seg([['on', t('Show me in the ranking')], ['off', t('Keep me out')]], u.onBoard ? 'on' : 'off', async v => {
                    try {
                        applyState(await Api.post('board.join', {on: v === 'on'}));
                    } catch (e) {
                        toast(e.message, 'error');
                    }
                }),
                h('p.muted.small', t('Your username is the only thing others see, next to your score.')),
                h('h2.section-title', t('Account')),
                h('div.group', row('person', t('Change username'), () => renderUsername(true)), row('group', t('Friends'), () => go('friends')), row('bell', t('Notifications'), notificationSettings), row('edit', t('Name'), editProfile), row('lock', u.hasPassword ? t('Change password') : t('Set a password'), editPassword),
                    u.role >= 5 ? h('a.setting', {href: 'admin.html'}, icon('shield'), h('span', t('Admin portal')), icon('right')) : null,
                    h('a.setting', {href: 'privacy.html'}, icon('info'), h('span', t('Privacy')), icon('right')),
                    row('logout', t('Sign out'), async () => {
                        await Api.post('auth.logout', {}).catch(() => null);
                        S.user = null;
                        S.loaded = false;
                        render();
                    }),
                    row('delete', t('Delete account'), deleteAccount, true)),
                h('h2.section-title', t('Support the project')),
                h('a.donate', {href: DONATE_URL, target: '_blank', rel: 'noopener'}, h('span.quick-ic', icon('heart')), h('span.grow', h('strong', t('Donate')), h('span', t('Fucina is open source and free. A donation supports the project.'))), icon('right')),
                h('div.center', h('a.credit', {href: AUTHOR_URL, target: '_blank', rel: 'noopener'}, h('span', t('A project by')), h('span.credit-name', 'Saverio Morelli'))),
                h('div.center', h('button.version-btn', {type: 'button', onclick: releaseNotes}, 'Fucina ' + VERSION)),
            ]
        };
    }

    /** Everyone needs a username: it is the only public data of an account, and the app stops here until it is chosen. */
    function renderUsername(changing) {
        const current = S.state.user.username || '';
        const name = UI.input({value: current, autocomplete: 'off', autocapitalize: 'off', spellcheck: false, maxlength: 20});
        const err = h('div.err-box', {hidden: true});
        const save = btn(changing ? t('Save') : t('Continue'), {type: 'submit', cls: 'btn-block'});
        const form = h('form', {
            novalidate: true, onsubmit: async e => {
                e.preventDefault();
                err.hidden = true;
                try {
                    applyState(await busy(save, () => Api.post('auth.username', {username: name.value})));
                    render();
                } catch (ex) {
                    err.textContent = ex.message;
                    err.hidden = false;
                }
            }
        }, UI.field(t('Username'), name), h('p.muted.small', t('3 to 20 letters, numbers, dots or underscores. It is the only thing others can see about you: avoid your real name if you prefer.')), err, h('div.spacer'), save,
            changing ? h('div.center', btn(t('Cancel'), {kind: 'text', onclick: render})) : null);
        root.replaceChildren(h('div.auth', h('div.auth-top', mascot('happy', 'auth-pet'), h('h1', changing ? t('Change username') : t('Choose your username')), h('p', t('This is how others find you'))), h('div.auth-body', form)));
    }

    const pushReady = () => 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;

    function keyBytes(text) {
        const raw = atob(text.replace(/-/g, '+').replace(/_/g, '/') + '='.repeat((4 - text.length % 4) % 4));
        return Uint8Array.from(raw, c => c.charCodeAt(0));
    }

    /** This device receives notifications when the browser allows them and it is subscribed. */
    async function deviceSubscription() {
        if (!pushReady() || Notification.permission !== 'granted') return null;
        return (await navigator.serviceWorker.ready).pushManager.getSubscription();
    }

    async function setDevice(on) {
        const reg = await navigator.serviceWorker.ready;
        let sub = await reg.pushManager.getSubscription();
        if (!on) {
            if (sub) {
                await Api.post('push.unsubscribe', {endpoint: sub.endpoint});
                await sub.unsubscribe();
            }
            return;
        }
        if (await Notification.requestPermission() !== 'granted') throw new Error(t('Permission denied: you can turn them on in the browser settings.'));
        const {publicKey} = await Api.get('push.key');
        if (!publicKey) throw new Error(t('Notifications are not available on this device.'));
        if (!sub) sub = await reg.pushManager.subscribe({userVisibleOnly: true, applicationServerKey: keyBytes(publicKey)});
        await Api.post('push.subscribe', {endpoint: sub.endpoint});
    }

    /** The device is a choice of this browser; the kinds follow the account, on every device. */
    function notificationSettings() {
        const kinds = [['daily', 'calendar', t('Daily challenge'), t('An evening reminder if you have not done it yet')], ['streak', 'fire', t('Streak at risk'), t('In the evening, if your streak is about to end')], ['prizes', 'trophy', t('Prizes'), t('When you make the podium')], ['friends', 'group', t('Friends'), t('When someone adds you')]];
        const box = h('div');
        const toggle = (ic, title, text, on, onclick, disabled) => h('button.setting.toggle', {type: 'button', role: 'switch', 'aria-checked': on ? 'true' : 'false', disabled: !!disabled, onclick}, icon(ic), h('span', h('strong', title), text ? h('small', text) : null), h('i.switch' + (on ? '.on' : '')));

        async function paint() {
            const sub = await deviceSubscription().catch(() => null);
            const off = new Set(S.state.user.notifyOff || []);
            box.replaceChildren(
                pushReady() ? h('div.group', toggle('bell', t('On this device'), sub ? t('On') : t('Off'), !!sub, async e => {
                    e.currentTarget.disabled = true;
                    await setDevice(!sub).catch(err => toast(err.message, 'error'));
                    paint();
                })) : banner('info', 'info', t('Not available here'), t('This browser cannot receive notifications. On iPhone, add Fucina to the home screen first.')),
                h('h2.section-title', t('Notify me about')),
                h('div.group', kinds.map(([key, ic, title, text]) => toggle(ic, title, text, !off.has(key), async () => {
                    if (off.has(key)) off.delete(key); else off.add(key);
                    try {
                        S.state.user.notifyOff = (await Api.post('notify.save', {off: [...off]})).notifyOff;
                    } catch (err) {
                        toast(err.message, 'error');
                    }
                    paint();
                }))),
                h('p.muted.small', t('Your choices follow your account. The content never passes through the push services.')));
        }

        paint();
        const s = sheet({title: t('Notifications'), body: [box], actions: [btn(t('Close'), {onclick: () => s.close()})]});
    }

    function releaseNotes() {
        const s = sheet({
            title: t('Release notes'),
            body: [h('div.releases', RELEASES.map(r => h('section.release', h('h3', r.v, h('small', UI.fmtDate(r.date))), h('ul', (r[I18N.lang] || r.en).map(n => h('li', n))))))],
            actions: [btn(t('Close'), {onclick: () => s.close()})]
        });
    }

    function editProfile() {
        const name = UI.input({value: S.state.user.name, maxlength: 80});
        const save = btn(t('Save'), {
            onclick: async () => {
                try {
                    applyState(await busy(save, () => Api.post('auth.profile', {name: name.value})));
                    s.close();
                    setTimeout(render, 150);
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = sheet({
            title: t('Name'), body: [UI.field(t('Name'), name), h('p.sheet-text', t('Email: {email}', {email: S.state.user.email})), h('p.muted.small', t('The email identifies your account and cannot be changed.'))],
            actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), save]
        });
    }

    function editPassword() {
        const cur = UI.input({type: 'password', autocomplete: 'current-password'});
        const pw = UI.input({type: 'password', autocomplete: 'new-password'});
        const save = btn(t('Save'), {
            onclick: async () => {
                try {
                    await busy(save, () => Api.post('auth.password', {current: cur.value, password: pw.value}));
                    await refresh();
                    s.close();
                    toast(t('Password updated'), 'ok');
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = sheet({title: t('Password'), body: [S.state.user.hasPassword ? UI.field(t('Current password'), cur) : null, UI.field(t('New password (min. 8 characters)'), pw)], actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), save]});
    }

    function deleteAccount() {
        const pw = UI.input({type: 'password', autocomplete: 'current-password'});
        const del = btn(t('Delete everything'), {
            kind: 'danger', onclick: async () => {
                try {
                    await busy(del, () => Api.post('auth.delete', {password: pw.value}));
                    s.close();
                    S.user = null;
                    S.loaded = false;
                    setTimeout(render, 150);
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = sheet({
            title: t('Delete account'),
            body: [h('p.sheet-text', t('Progress, badges and certificates are deleted for good, and your certificate links stop working.')), S.state.user.hasPassword ? UI.field(t('Password'), pw) : null],
            actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), del]
        });
    }

    // ---------------------------------------------------------------- sign in

    function renderAuth(mode) {
        mode = mode || UI.store.get('authMode', 'register');
        const err = h('div.err-box', {hidden: true});
        const name = UI.input({autocomplete: 'name', maxlength: 80});
        const email = UI.input({type: 'email', autocomplete: 'email', inputmode: 'email'});
        const pw = UI.input({type: 'password', autocomplete: mode === 'login' ? 'current-password' : 'new-password'});
        const privacy = h('input', {type: 'checkbox'});
        const gslot = h('div.google-slot');
        const submit = btn(mode === 'login' ? t('Sign in') : t('Create account'), {type: 'submit', cls: 'btn-block'});
        const form = h('form', {
                novalidate: true, onsubmit: async e => {
                    e.preventDefault();
                    err.hidden = true;
                    try {
                        const d = await busy(submit, () => mode === 'login'
                            ? Api.post('auth.login', {email: email.value, password: pw.value}, {silentAuth: true})
                            : Api.post('auth.register', {name: name.value, email: email.value, password: pw.value, acceptPrivacy: privacy.checked}, {silentAuth: true}));
                        S.user = d.user;
                        start();
                    } catch (ex) {
                        err.textContent = ex.message;
                        err.hidden = false;
                    }
                }
            },
            mode === 'register' ? UI.field(t('Name'), name) : null, UI.field('Email', email), UI.field(mode === 'login' ? t('Password') : t('Password (min. 8 characters)'), pw),
            mode === 'register' ? h('label.check', privacy, h('span', t('I have read and accept the '), h('a', {href: 'privacy.html', target: '_blank', rel: 'noopener'}, t('privacy policy')))) : null,
            err, h('div.spacer'), submit,
            mode === 'login' ? h('div.center', {style: {marginTop: '6px'}}, btn(t('Forgot your password?'), {
                kind: 'text', onclick: () => {
                    const s = sheet({
                        title: t('Forgot password'),
                        body: [h('p.sheet-text', t('Write to us through the contact form, from the email address of your account: we will help you get back in.')), h('div.btn-row', h('a.btn.btn-tonal', {href: CONTACT_URL, target: '_blank', rel: 'noopener'}, h('span', t('Open the contact form'))))],
                        actions: [btn(t('Got it'), {onclick: () => s.close()})]
                    });
                }
            })) : null);
        const features = cls => h('ul.features.' + cls,
            h('li', tile('icon', 'book', 'sm'), t('HTML, CSS, SCSS and Sass, level after level')),
            h('li', tile('icon', 'game', 'sm'), t('Games, drills and daily challenges')),
            h('li', tile('icon', 'spark', 'sm'), t('Earn Sparks and spend them on hints')),
            h('li', tile('icon', 'cert', 'sm'), t('A certificate for every course you complete')));
        root.replaceChildren(h('div.auth',
            h('div.auth-top', h('div.auth-brand', h('img', {src: LOGO, alt: ''}), h('h1', 'Fucina')), h('p', t('Where the code is forged')), mascot('happy', 'auth-pet'), features('desk-only')),
            h('div.auth-body',
                seg([['login', t('Sign in')], ['register', t('Create account')]], mode, v => {
                    UI.store.set('authMode', v);
                    renderAuth(v);
                }),
                S.config.googleClientId ? [gslot, h('div.divider-text', t('or with your email'))] : null,
                form, features('mob-only'), h('p.center.small', h('a.verify-link', {href: 'verify.php'}, t('Verify a certificate'))))));
        document.title = 'Fucina';
        if (S.config.googleClientId) UI.googleButton(gslot, S.config.googleClientId, googleSignIn, mode === 'login' ? 'continue_with' : 'signup_with');
    }

    async function googleSignIn(credential) {
        try {
            const d = await Api.post('auth.google', {credential}, {silentAuth: true});
            S.user = d.user;
            start();
        } catch (e) {
            if (e.code !== 'needs_signup') return toast(e.message, 'error');
            const privacy = h('input', {type: 'checkbox'});
            const ok = btn(t('Create account'), {
                onclick: async () => {
                    if (!privacy.checked) return toast(t('To continue, accept the privacy policy.'), 'error');
                    const d = await busy(ok, () => Api.post('auth.google', {credential, acceptPrivacy: true}, {silentAuth: true}));
                    s.close();
                    S.user = d.user;
                    setTimeout(start, 150);
                }
            });
            const s = sheet({
                title: t('Create your account'),
                body: [h('p.sheet-text', e.data.email), h('label.check', privacy, h('span', t('I have read and accept the '), h('a', {href: 'privacy.html', target: '_blank', rel: 'noopener'}, t('privacy policy'))))],
                actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), ok]
            });
        }
    }

    function fatal(title, text, action) {
        root.replaceChildren(h('div.auth', h('div.auth-top', h('div.auth-brand', h('img', {src: LOGO, alt: ''}), h('h1', 'Fucina')), h('p', title), mascot('sad', 'auth-pet')),
            h('div.auth-body.center', h('p', text), action || btn(t('Try again'), {onclick: start}))));
    }

    async function start() {
        try {
            const [me, cfg] = await Promise.all([Api.get('auth.me', {silentAuth: true}), Api.get('auth.config', {silentAuth: true}).catch(() => null)]);
            S.user = me.user;
            if (cfg) S.config = cfg;
        } catch (e) {
            if (e.code === 'not_installed') return fatal(t('Installation not completed'), t('The database is not configured yet.'), h('a.btn.btn-filled', {href: 'install-db.php'}, t('Open install-db.php')));
            return fatal(t('Could not connect'), e.message);
        }
        if (!S.user) {
            S.loaded = false;
            return render();
        }
        render();
        try {
            await refresh();
            S.loaded = true;
            render();
            showPrizes();
        } catch (e) {
            fatal(t('Could not load the data'), e.message);
        }
    }

    Api.onUnauthenticated(() => {
        S.user = null;
        S.loaded = false;
        UI.closeAllSheets();
        render();
    });
    window.addEventListener('hashchange', () => {
        if (UI.sheetsOpen()) UI.closeAllSheets();
        render();
    });
    DESK.addEventListener('change', () => S.loaded && render());
    matchMedia('(prefers-color-scheme: dark)').addEventListener('change', UI.applyTheme);

    UI.applyTheme();
    if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('sw.js').catch(() => null));
    start();
})();
