/* Fucina — admin portal: courses, lessons, quizzes, games, users, certificates and settings.
 * Every text is edited in all the languages side by side; an empty language is shown as "to translate". */
(function () {
    'use strict';

    const t = I18N.t;
    const {h, icon, chip, btn, iconBtn, busy, toast, confirm} = UI;
    const root = document.getElementById('admin');
    let me = null;
    let LANGS = ['it', 'en'];
    /** Languages of a code exercise (any other slug saved in the catalogue is kept as it is). */
    const TASK_LANGS = [['html', 'HTML'], ['css', 'CSS'], ['scss', 'SCSS'], ['sass', 'Sass'], ['js', 'JavaScript'], ['ts', 'TypeScript'], ['angular', 'Angular'], ['vue', 'Vue'],
        ['php', 'PHP'], ['python', 'Python'], ['java', 'Java'], ['sql', 'SQL'], ['mongodb', 'MongoDB'], ['r', 'R'], ['dart', 'Dart'], ['swift', 'Swift'], ['kotlin', 'Kotlin'],
        ['rust', 'Rust'], ['vala', 'Vala'], ['text', 'Text']];

    /** What a course is for (the same list as COURSE_CATEGORIES_ALL in lib/courses.php). */
    const CATEGORIES = [['frontend', 'Frontend'], ['backend', 'Backend'], ['mobile', 'Mobile'], ['data', 'Data'], ['systems', 'Systems'], ['desktop', 'Desktop'], ['ai', 'AI'], ['devops', 'DevOps'], ['security', 'Security'], ['productivity', 'Productivity'], ['design', 'Design']];
    let GENERAL_EXAM_MINUTES = null; // the general time of the final exams, shown as a hint in the course form

    const TABS = [['overview', 'Overview', 'dashboard'], ['courses', 'Courses', 'book'], ['games', 'Games', 'game'], ['users', 'Users', 'group'], ['certificates', 'Certificates', 'cert'], ['avatars', 'Profile pictures', 'image'], ['settings', 'Settings', 'tune']];

    // ---------------------------------------------------------------- form helpers

    const inp = attrs => h('input.ctl', Object.assign({type: 'text', autocomplete: 'off'}, attrs || {}));
    const area = attrs => h('textarea.ctl', Object.assign({rows: 3, spellcheck: false}, attrs || {}));
    const f = (label, control, help) => h('label.f', h('span', label), control, help ? h('small', help) : null);

    function sel(options, value) {
        const s = h('select.ctl');
        for (const [v, label] of options) s.appendChild(h('option', {value: v, selected: String(v) === String(value)}, label));
        return s;
    }

    /** A text in all the languages, side by side. value() gives back {it: …, en: …}. */
    function multi(label, value, opts) {
        opts = opts || {};
        const controls = {};
        const cols = LANGS.map(l => {
            const text = (value && value[l]) || '';
            const c = opts.rows ? area({rows: opts.rows, value: text}) : inp({value: text});
            controls[l] = c;
            const tag = h('b.lang-tag', l.toUpperCase());
            const mark = () => tag.classList.toggle('missing', !c.value.trim());
            c.addEventListener('input', mark);
            mark();
            return h('div.lang-col', tag, c);
        });
        const el = h('div.f', h('span', label), h('div.lang-row', cols));
        el.value = () => Object.fromEntries(LANGS.map(l => [l, controls[l].value]));
        return el;
    }

    const title = v => (v && (v[I18N.lang] || v.it || v.en)) || '';
    const difficulties = () => [[1, t('Easy')], [2, t('Medium')], [3, t('Hard')]];
    const statuses = () => [['published', t('Published')], ['draft', t('Draft')]];
    const statusChip = s => s === 'published' ? chip(t('Published'), 'ok') : chip(t('Draft'), 'warn');

    function page(heading, actions, ...body) {
        return h('div.adm-page', h('header.adm-head', h('h1', heading), h('div.adm-actions', actions)), body);
    }

    async function act(button, fn, done) {
        try {
            await busy(button, fn);
            toast(t('Saved'), 'ok');
            if (done) done();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    /** A table; on a phone each row becomes a card, and every cell shows the name of its column (data-label). */
    function table(cols, rows) {
        const body = h('tbody', rows);
        body.querySelectorAll(':scope > tr').forEach(tr => [...tr.children].forEach((td, i) => {
            if (cols[i]) td.dataset.label = cols[i];
        }));
        return h('div.tbl-wrap', h('table.tbl', h('thead', h('tr', cols.map(c => h('th', c)))), body));
    }

    /** Moves a row up or down and saves the new order. */
    function orderButtons(list, i, route) {
        const move = async d => {
            const ids = list.map(x => x.id);
            const j = i + d;
            if (j < 0 || j >= ids.length) return;
            [ids[i], ids[j]] = [ids[j], ids[i]];
            await Api.post(route, {ids}).catch(e => toast(e.message, 'error'));
            render();
        };
        return [iconBtn('up', t('Move up'), () => move(-1)), iconBtn('down', t('Move down'), () => move(1))];
    }

    // ---------------------------------------------------------------- overview

    async function viewOverview() {
        const d = await Api.get('admin.overview');
        const stat = (label, n) => h('div.adm-stat', h('strong', String(n)), h('span', label));
        const max = Math.max(1, ...d.days.map(x => +x.users));
        return page(t('Overview'), null,
            h('div.adm-stats', stat(t('Users'), d.users), stat(t('Active this week'), d.activeWeek), stat(t('Levels completed'), d.lessonsDone), stat(t('Games played'), d.gamesPlayed), stat(t('Certificates'), d.certificates), stat(t('Hints bought'), d.hintsBought), stat(t('Lessons'), d.lessons), stat(t('Questions'), d.questions)),
            h('h2', t('Active users, last 30 days')),
            d.days.length ? h('div.adm-bars', d.days.map(x => h('i', {title: `${x.day}: ${x.users}`, style: {height: Math.round(x.users * 100 / max) + '%'}}))) : h('p.muted', t('No activity yet.')),
            h('h2', t('Courses')),
            table([t('Course'), t('Status'), t('Learners'), t('Certificates')], d.courses.map(c => h('tr', h('td', c.title), h('td', statusChip(c.status)), h('td', String(c.learners)), h('td', String(c.certificates))))));
    }

    // ---------------------------------------------------------------- courses

    async function viewCourses() {
        const d = await Api.get('admin.courses');
        LANGS = d.languages;
        return page(t('Courses'), btn(t('New course'), {icon: 'add', onclick: () => go('course-new')}),
            table(['', t('Course'), t('Status'), t('Lessons'), t('Questions'), t('To translate'), ''], d.courses.map((c, i) => h('tr',
                h('td.nowrap', orderButtons(d.courses, i, 'admin.course.sort')),
                h('td', h('a', {href: '#/course/' + c.id}, h('strong', title(c.title))), h('div.muted.small', c.id)),
                h('td', statusChip(c.status)), h('td', String(c.lessons)), h('td', String(c.questions)),
                h('td', c.missing ? chip(String(c.missing), 'warn') : chip('0', 'ok')),
                h('td.nowrap', iconBtn('edit', t('Edit'), () => go('course/' + c.id)))))));
    }

    function courseForm(c, courses) {
        const isNew = !c;
        c = c || {title: {}, short: {}, desc: {}, level: {}, family: 'html', status: 'draft'};
        const id = inp({value: c.id || '', disabled: !isNew, placeholder: 'php-base'});
        const fTitle = multi(t('Title'), c.title);
        const fShort = multi(t('Short title'), c.short);
        const fDesc = multi(t('Description'), c.desc, {rows: 2});
        const fLevel = multi(t('Level label'), c.level);
        const family = inp({value: c.family});
        const after = sel([['', t('None: open from the start')], ...courses.filter(x => x.id !== c.id).map(x => [x.id, title(x.title)])], c.after || '');
        const status = sel(statuses(), c.status);
        const cats = (c.categories || []);
        const boxes = CATEGORIES.map(([v, label]) => [v, h('input', {type: 'checkbox', checked: cats.includes(v)}), label]);
        const catField = h('div.f', h('span', t('Categories')), h('div.adm-traits', boxes.map(([, box, label]) => h('label.check', box, h('span', t(label))))),
            h('small', t('Used to filter the courses in the app.')));
        const minutes = inp({type: 'number', min: 0, max: 240, value: c.examMinutes ?? '', placeholder: t('General: {n}', {n: GENERAL_EXAM_MINUTES ?? 15})});
        const save = btn(t('Save'), {
            icon: 'save', onclick: () => act(save, () => Api.post('admin.course.save', {
                existing: !isNew, id: id.value, family: family.value, after: after.value, status: status.value, examMinutes: minutes.value,
                categories: boxes.filter(([, box]) => box.checked).map(([v]) => v), title: fTitle.value(), short: fShort.value(), desc: fDesc.value(), level: fLevel.value()
            }), () => go(isNew ? 'courses' : 'course/' + c.id))
        });
        return h('div.adm-form', h('div.adm-grid', f(t('Id (cannot change later)'), id), f(t('Family'), family, 'html, css, scss, sass, js, php…'), f(t('Unlocked after'), after), f(t('Status'), status),
            f(t('Final exam: time in minutes'), minutes, t('Empty = the general time in Settings · 0 = no limit'))), catField, fTitle, fShort, fDesc, fLevel, h('div.adm-actions', save));
    }

    async function viewCourseNew() {
        const d = await Api.get('admin.courses');
        LANGS = d.languages;
        GENERAL_EXAM_MINUTES = d.examMinutes;
        return page(t('New course'), null, courseForm(null, d.courses));
    }

    async function viewCourse(id) {
        const [d, list] = await Promise.all([Api.get('admin.course&id=' + encodeURIComponent(id)), Api.get('admin.courses')]);
        LANGS = list.languages;
        GENERAL_EXAM_MINUTES = list.examMinutes;
        const c = d.course;
        const del = btn(t('Delete'), {
            kind: 'danger', icon: 'delete', onclick: async () => {
                if (!await confirm(t('Delete this course?'), t('Its lessons and questions are deleted too. Certificates already issued stay.'), t('Delete'), true)) return;
                act(del, () => Api.post('admin.course.delete', {id}), () => go('courses'));
            }
        });
        return page(title(c.title), [btn(t('New lesson'), {icon: 'add', onclick: () => go('lesson-new/' + id)}), del],
            h('h2', t('Levels')),
            table(['', t('Level'), t('Difficulty'), t('Status'), t('Questions'), t('Drills'), t('Exercise'), ''], c.items.map((l, i) => h('tr',
                h('td.nowrap', orderButtons(c.items, i, 'admin.lesson.sort')),
                h('td', h('a', {href: `#/lesson/${id}/${l.id}`}, h('strong', `${i + 1}. ${title(l.title)}`)), h('div.muted.small', l.slug)),
                h('td', difficulties().find(x => x[0] === l.difficulty)[1]), h('td', statusChip(l.status)), h('td', String(l.questions.length)), h('td', String(l.drills.length)), h('td', l.task ? t('Yes') : t('No')),
                h('td.nowrap', iconBtn('edit', t('Edit'), () => go(`lesson/${id}/${l.id}`)))))),
            h('h2', t('Course details')), courseForm(c, list.courses));
    }

    // ---------------------------------------------------------------- lesson

    function blockEditor(blocks) {
        const list = h('div.blocks');
        const items = [];

        function add(b) {
            const item = {b, el: null, read: null};
            const type = b.t;
            let body;
            if (type === 'code') {
                const code = area({rows: 6, value: b.code || '', class: 'mono'});
                const lang = inp({value: b.lang || 'html'});
                item.read = () => ({t: 'code', code: code.value, lang: lang.value});
                body = [f(t('Language'), lang), f(t('Code'), code)];
            } else {
                const text = multi(type === 'tip' ? t('Tip') : t('Paragraph'), b.text, {rows: 3});
                item.read = () => ({t: type, text: text.value()});
                body = [text];
            }
            const remove = iconBtn('delete', t('Remove'), () => {
                items.splice(items.indexOf(item), 1);
                item.el.remove();
            });
            const up = iconBtn('up', t('Move up'), () => {
                const i = items.indexOf(item);
                if (i < 1) return;
                items.splice(i, 1);
                items.splice(i - 1, 0, item);
                list.insertBefore(item.el, items[i].el);
            });
            item.el = h('div.block', h('div.block-bar', h('b', {code: t('Code'), tip: t('Tip'), p: t('Paragraph')}[type]), up, remove), body);
            items.push(item);
            list.appendChild(item.el);
        }

        (blocks || []).forEach(add);
        const el = h('div', list, h('div.adm-actions', btn(t('Paragraph'), {kind: 'tonal', icon: 'add', onclick: () => add({t: 'p'})}), btn(t('Code'), {kind: 'tonal', icon: 'add', onclick: () => add({t: 'code'})}), btn(t('Tip'), {kind: 'tonal', icon: 'add', onclick: () => add({t: 'tip'})})));
        el.value = () => items.map(i => i.read());
        return el;
    }

    function checkRow(c, onRemove) {
        c = c || {};
        const kind = sel([['sel', t('Element exists')], ['css', t('Computed CSS')], ['re', t('Regular expression')]], c.re ? 're' : c.prop ? 'css' : 'sel');
        const a = inp({value: c.re || c.sel || '', class: 'mono', placeholder: 'h1'});
        const b = inp({value: c.prop || c.text || '', class: 'mono', placeholder: t('Text or CSS property')});
        const v = inp({value: c.value || '', class: 'mono', placeholder: 'rgb(255, 0, 0)'});
        const label = multi(t('What the learner reads'), c.label);
        const el = h('div.block', h('div.block-bar', h('b', t('Check')), iconBtn('delete', t('Remove'), () => onRemove(el))), h('div.adm-grid', f(t('Type'), kind), f(t('Selector or pattern'), a), f(t('Text or property'), b), f(t('Expected value'), v)), label);
        el.read = () => {
            const base = {label: label.value()};
            if (kind.value === 're') return {...base, re: a.value};
            if (kind.value === 'css') return {...base, sel: a.value, prop: b.value, value: v.value};
            return {...base, sel: a.value, text: b.value};
        };
        return el;
    }

    function taskEditor(task) {
        const on = h('input', {type: 'checkbox', checked: !!task});
        // HTML and CSS have a live preview; the other languages are checked by patterns only
        const langs = TASK_LANGS.some(([v]) => v === task?.lang) || !task ? TASK_LANGS : [...TASK_LANGS, [task.lang, task.lang]];
        const lang = sel(langs, task ? task.lang : 'html');
        const prompt = multi(t('Instructions'), task && task.prompt, {rows: 2});
        const start = area({rows: 5, value: task ? task.start : '', class: 'mono'});
        const html = area({rows: 5, value: task ? task.html : '', class: 'mono'});
        const hint = area({rows: 4, value: task ? task.hint || '' : '', class: 'mono'});
        const checks = h('div.blocks');
        const addCheck = c => checks.appendChild(checkRow(c, el => el.remove()));
        ((task && task.checks) || []).forEach(addCheck);
        const body = h('div', f(t('Language'), lang), prompt, f(t('Starting code'), start), f(t('Fixed page (CSS exercises)'), html), f(t('Paid hint: solution shown'), hint), h('h3', t('Checks')), checks, btn(t('Add a check'), {kind: 'tonal', icon: 'add', onclick: () => addCheck()}));
        body.hidden = !task;
        on.addEventListener('change', () => body.hidden = !on.checked);
        const el = h('div', h('label.check', on, h('span', t('This level has a coding exercise'))), body);
        el.value = () => on.checked ? {lang: lang.value, prompt: prompt.value(), start: start.value, html: html.value, hint: hint.value, checks: [...checks.children].map(x => x.read())} : null;
        return el;
    }

    function lessonForm(courseId, l) {
        l = l || {title: {}, difficulty: 1, status: 'draft', body: [], task: null};
        const fTitle = multi(t('Title'), l.title);
        const slug = inp({value: l.slug || '', placeholder: t('Automatic')});
        const difficulty = sel(difficulties(), l.difficulty);
        const status = sel(statuses(), l.status);
        const body = blockEditor(l.body);
        const task = taskEditor(l.task);
        const save = btn(t('Save'), {
            icon: 'save', onclick: () => act(save, () => Api.post('admin.lesson.save', {
                id: l.id || 0, course: courseId, slug: slug.value, difficulty: difficulty.value, status: status.value, title: fTitle.value(), body: body.value(), task: task.value()
            }), () => go('course/' + courseId))
        });
        return h('div.adm-form', h('div.adm-grid', f(t('Slug'), slug, t('Progress refers to it: avoid changing it.')), f(t('Difficulty'), difficulty), f(t('Status'), status)), fTitle, h('h2', t('Theory')), body, h('h2', t('Exercise')), task, h('div.adm-actions', save));
    }

    function questionDialog(lessonId, q, done) {
        q = q || {q: {}, options: [{}, {}, {}, {}], answer: 0, why: {}, hint: null, difficulty: 1};
        const fq = multi(t('Question'), q.q, {rows: 2});
        const options = q.options.map((o, i) => multi(t('Answer {n}', {n: i + 1}), o));
        const answer = sel(q.options.map((_, i) => [i, t('Answer {n}', {n: i + 1})]), q.answer);
        const difficulty = sel(difficulties(), q.difficulty);
        const why = multi(t('Explanation'), q.why, {rows: 2});
        const hint = multi(t('Written hint (optional)'), q.hint, {rows: 2});
        const save = btn(t('Save'), {
            icon: 'save', onclick: async () => {
                try {
                    await busy(save, () => Api.post('admin.question.save', {id: q.id || 0, lessonId, q: fq.value(), options: options.map(o => o.value()), answer: answer.value, difficulty: difficulty.value, why: why.value(), hint: hint.value()}));
                    s.close();
                    done();
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = UI.sheet({title: q.id ? t('Edit question') : t('New question'), cls: 'wide', persistent: true, body: [fq, options, h('div.adm-grid', f(t('Correct answer'), answer), f(t('Difficulty'), difficulty)), why, hint], actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), save]});
    }

    const DRILL_KINDS = () => [['blanks', t('Tiles')], ['order', t('Sort it')], ['bug', t('Bug hunt')], ['match', t('Pairs')], ['flash', t('Lightning')], ['tune', t('Tune it')]];
    const both = (it, en) => ({it, en});
    const DRILL_SAMPLES = {
        blanks: {code: '<___>Ciao</h1>', gaps: ['h1'], extra: ['p', 'div']},
        order: {lines: ['<ul>', '  <li>Uno</li>', '</ul>']},
        bug: {lines: ['<h1>Ciao</h2>', '<p>Testo</p>'], bug: 0, fix: '<h1>Ciao</h1>'},
        match: {pairs: [['<p>', both('Paragrafo', 'Paragraph')], ['<ul>', both('Lista', 'List')], ['<a>', both('Link', 'Link')]]},
        flash: {cards: [[both('Vero', 'True'), true], [both('Falso', 'False'), false], [both('Vero', 'True'), true], [both('Falso', 'False'), false]]},
        tune: {layout: 'flex', items: 3, props: [{prop: 'justify-content', choices: ['flex-start', 'center'], target: 'center'}, {prop: 'gap', unit: 'px', min: 0, max: 24, step: 4, target: 12}], base: {}, itemBase: {}},
    };

    /** The content of a drill is edited as JSON: a text that changes with the language is {"it": "…", "en": "…"}. */
    function drillDialog(lessonId, d, done) {
        d = d || {kind: 'blanks', prompt: {}, why: {}, ...DRILL_SAMPLES.blanks};
        const {id, kind: startKind, prompt, why, stars, ...content} = d;
        const kind = sel(DRILL_KINDS(), startKind);
        const fPrompt = multi(t('Instructions'), prompt, {rows: 2});
        const fWhy = multi(t('Explanation'), why, {rows: 2});
        const data = area({rows: 12, value: JSON.stringify(content, null, 2)});
        data.classList.add('mono');
        kind.addEventListener('change', () => {
            data.value = JSON.stringify(DRILL_SAMPLES[kind.value], null, 2);
        });
        const save = btn(t('Save'), {
            icon: 'save', onclick: async () => {
                let parsed;
                try {
                    parsed = JSON.parse(data.value);
                } catch (e) {
                    return toast(t('The content is not valid JSON.'), 'error');
                }
                try {
                    await busy(save, () => Api.post('admin.drill.save', {id: id || 0, lessonId, kind: kind.value, prompt: fPrompt.value(), why: fWhy.value(), data: parsed}));
                    s.close();
                    done();
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = UI.sheet({title: id ? t('Edit drill') : t('New drill'), cls: 'wide', persistent: true, body: [f(t('Kind'), kind), fPrompt, f(t('Content'), data, t('Changing the kind replaces it with an example.')), fWhy], actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), save]});
    }

    async function viewLesson(courseId, slugOrId) {
        const [d, list] = await Promise.all([Api.get('admin.course&id=' + encodeURIComponent(courseId)), Api.get('admin.courses')]);
        LANGS = list.languages;
        const l = d.course.items.find(x => String(x.id) === slugOrId);
        if (!l) throw new Error(t('Not found.'));
        const del = btn(t('Delete'), {
            kind: 'danger', icon: 'delete', onclick: async () => {
                if (!await confirm(t('Delete this lesson?'), t('Its questions are deleted too.'), t('Delete'), true)) return;
                act(del, () => Api.post('admin.lesson.delete', {id: l.id}), () => go('course/' + courseId));
            }
        });
        return page(title(l.title), del, lessonForm(courseId, l),
            h('div.adm-head', h('h2', t('Quiz')), btn(t('New question'), {icon: 'add', onclick: () => questionDialog(l.id, null, render)})),
            table([t('Question'), t('Correct answer'), t('Difficulty'), ''], l.questions.map(q => h('tr',
                h('td', title(q.q)), h('td', title(q.options[q.answer])), h('td', difficulties().find(x => x[0] === q.difficulty)[1]),
                h('td.nowrap', iconBtn('edit', t('Edit'), () => questionDialog(l.id, q, render)), iconBtn('delete', t('Delete'), async () => {
                    if (!await confirm(t('Delete this question?'), title(q.q), t('Delete'), true)) return;
                    await Api.post('admin.question.delete', {id: q.id}).catch(e => toast(e.message, 'error'));
                    render();
                }))))),
            h('div.adm-head', h('h2', t('Drills')), btn(t('New drill'), {icon: 'add', onclick: () => drillDialog(l.id, null, render)})),
            table([t('Instructions'), t('Kind'), ''], l.drills.map(d => h('tr',
                h('td', title(d.prompt)), h('td', Object.fromEntries(DRILL_KINDS())[d.kind] || d.kind),
                h('td.nowrap', iconBtn('edit', t('Edit'), () => drillDialog(l.id, d, render)), iconBtn('delete', t('Delete'), async () => {
                    if (!await confirm(t('Delete this drill?'), title(d.prompt), t('Delete'), true)) return;
                    await Api.post('admin.drill.delete', {id: d.id}).catch(e => toast(e.message, 'error'));
                    render();
                }))))));
    }

    async function viewLessonNew(courseId) {
        const list = await Api.get('admin.courses');
        LANGS = list.languages;
        return page(t('New lesson'), null, lessonForm(courseId, null));
    }

    // ---------------------------------------------------------------- games

    const ITEM_GAMES = ['whotag', 'selectors', 'linehop'];
    const TRAIT_LIST = ['block', 'inline', 'text', 'semantic', 'attr', 'interactive', 'void', 'media', 'form', 'list', 'table'];

    async function viewGames() {
        const d = await Api.get('admin.games');
        return page(t('Games'), null, table([t('Game'), t('Status'), t('Levels'), t('Content'), t('Played'), ''], d.games.map(g => h('tr',
            h('td', h('a', {href: '#/game/' + g.id}, h('strong', title(g.title)))), h('td', g.enabled ? chip(t('Active'), 'ok') : chip(t('Off'), 'warn')), h('td', String(g.levels)),
            h('td', ITEM_GAMES.includes(g.id) ? String(g.items) : t('Automatic')), h('td', String(g.played)), h('td.nowrap', iconBtn('edit', t('Edit'), () => go('game/' + g.id)))))));
    }

    function itemDialog(game, item, done) {
        item = item || {level: 1, status: 'published', data: {}};
        const level = inp({type: 'number', min: 1, max: 30, value: item.level});
        const status = sel(statuses(), item.status);
        let read, body;
        if (game === 'selectors') {
            const s = inp({value: item.data.selector || '', class: 'mono', placeholder: '.r1.white'});
            read = () => ({selector: s.value});
            body = f(t('CSS selector'), s, t('Squares have the classes .white or .black, .r1–.r8 and .c1–.c8.'));
        } else if (game === 'linehop') {
            const ti = multi(t('Title'), item.data.title);
            const lines = area({rows: 10, value: (item.data.lines || []).join('\n'), class: 'mono'});
            read = () => ({title: ti.value(), lines: lines.value.split('\n')});
            body = [ti, f(t('Lines, in the right order'), lines)];
        } else {
            const name = inp({value: item.data.name || '', class: 'mono', placeholder: 'section'});
            const boxes = TRAIT_LIST.map(k => {
                const cb = h('input', {type: 'checkbox', checked: (item.data.traits || []).includes(k), value: k});
                return {cb, el: h('label.check', cb, h('span', k))};
            });
            read = () => ({name: name.value, traits: boxes.filter(b => b.cb.checked).map(b => b.cb.value)});
            body = [f(t('Tag'), name), h('div.adm-traits', boxes.map(b => b.el))];
        }
        const save = btn(t('Save'), {
            icon: 'save', onclick: async () => {
                try {
                    await busy(save, () => Api.post('admin.item.save', {id: item.id || 0, game, level: level.value, status: status.value, data: read()}));
                    s.close();
                    done();
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = UI.sheet({title: item.id ? t('Edit item') : t('New item'), cls: 'wide', persistent: true, body: [h('div.adm-grid', f(t('Level'), level), f(t('Status'), status)), body], actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), save]});
    }

    async function viewGame(id) {
        const [list, d, c] = await Promise.all([Api.get('admin.games'), Api.get('admin.game&id=' + encodeURIComponent(id)), Api.get('admin.courses')]);
        LANGS = c.languages;
        const g = list.games.find(x => x.id === id);
        if (!g) throw new Error(t('Not found.'));
        const fTitle = multi(t('Title'), g.title);
        const fDesc = multi(t('Description'), g.desc, {rows: 2});
        const levels = inp({type: 'number', min: 1, max: 30, value: g.levels});
        const enabled = h('input', {type: 'checkbox', checked: g.enabled});
        const save = btn(t('Save'), {icon: 'save', onclick: () => act(save, () => Api.post('admin.game.save', {id, title: fTitle.value(), desc: fDesc.value(), levels: levels.value, enabled: enabled.checked}), render)});
        const summary = i => id === 'selectors' ? i.data.selector : id === 'linehop' ? `${title(i.data.title)} (${i.data.lines.length})` : `<${i.data.name}> ${i.data.traits.join(', ')}`;
        return page(title(g.title), null, h('div.adm-form', h('div.adm-grid', f(t('Levels'), levels), h('label.check', enabled, h('span', t('Game active')))), fTitle, fDesc, h('div.adm-actions', save)),
            ITEM_GAMES.includes(id) ? [h('div.adm-head', h('h2', t('Content')), btn(t('New item'), {icon: 'add', onclick: () => itemDialog(id, null, render)})),
                id === 'whotag' ? h('p.muted', t('The cards of this game are shared by all the levels: keep them on level 1.')) : null,
                table([t('Level'), t('Content'), t('Status'), ''], d.items.map(i => h('tr', h('td', String(i.level)), h('td', h('code', summary(i))), h('td', statusChip(i.status)),
                    h('td.nowrap', iconBtn('edit', t('Edit'), () => itemDialog(id, i, render)), iconBtn('delete', t('Delete'), async () => {
                        if (!await confirm(t('Delete this item?'), summary(i), t('Delete'), true)) return;
                        await Api.post('admin.item.delete', {id: i.id}).catch(e => toast(e.message, 'error'));
                        render();
                    })))))] : h('p.muted', t('This game builds its levels on its own: here you can only set how many there are.')));
    }

    // ---------------------------------------------------------------- users, certificates, settings

    async function viewUsers() {
        const q = inp({placeholder: t('Search by name or email')});
        const holder = h('div');

        async function load() {
            const d = await Api.get('admin.users&q=' + encodeURIComponent(q.value));
            holder.replaceChildren(table([t('Name'), t('Username'), 'Email', t('Role'), t('Grade XP'), t('Sparks'), t('Levels'), t('Certificates'), ''], d.users.map(u => h('tr',
                h('td', u.name, +u.status ? '' : u.status === 'disabled' ? chip(t('Disabled'), 'danger') : null), h('td', u.username ? '@' + u.username : '—'), h('td', u.email, u.email_verified_at || +u.role >= 5 ? null : chip(t('To confirm'), 'warn')), h('td', +u.role >= 10 ? t('Master admin') : +u.role >= 5 ? t('Administrator') : t('Learner')),
                h('td', String(u.xp)), h('td', String(u.points)), h('td', String(u.lessons)), h('td', String(u.certificates)),
                h('td.nowrap', iconBtn('history', t('Activity'), () => userActivity(u)), iconBtn('edit', t('Edit'), () => userDialog(u, d.isMaster, load, +u.id === d.me)))))));
        }

        let timer;
        q.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(load, 250);
        });
        await load();
        return page(t('Users'), null, q, holder);
    }

    /** XP and Sparks of a user: a value to set, or an amount to add or take away. On your own account only these. */
    function userDialog(u, isMaster, done, self) {
        const role = sel([[0, t('Learner')], [5, t('Administrator')]], +u.role >= 5 ? 5 : 0);
        role.disabled = !isMaster || self;
        const status = sel([['active', t('Active')], ['disabled', t('Disabled')]], u.status);
        status.disabled = self;
        const xp = inp({type: 'number', min: 0, value: u.xp});
        const points = inp({type: 'number', min: 0, value: u.points});
        const nudge = (control, by) => iconBtn(by > 0 ? 'add' : 'remove', by > 0 ? t('Add {n}', {n: by}) : t('Take away {n}', {n: -by}), () => {
            control.value = Math.max(0, (parseInt(control.value, 10) || 0) + by);
        });
        const amount = (control, step) => h('div.adm-amount', nudge(control, -step), control, nudge(control, step));
        const save = btn(t('Save'), {
            icon: 'save', onclick: async () => {
                try {
                    await busy(save, () => Api.post('admin.user.save', {id: u.id, role: role.value, status: status.value, xp: xp.value, points: points.value}));
                    s.close();
                    done();
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const del = btn(t('Delete'), {
            kind: 'danger', onclick: async () => {
                if (!await confirm(t('Delete {name}?', {name: u.name}), t('Progress and certificates are deleted for good.'), t('Delete'), true)) return;
                try {
                    await Api.post('admin.user.delete', {id: u.id});
                    s.close();
                    done();
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = UI.sheet({
            title: u.name, body: [f(t('Role'), role), f(t('Status'), status), f(t('Grade XP'), amount(xp, 50), t('XP set the grade: the learner may move up or down.')), f(t('Sparks'), amount(points, 10), t('Every change shows in the activity of the user.')),
                btn(t('Activity'), {kind: 'tonal', icon: 'history', onclick: () => userActivity(u)})],
            actions: [self ? null : del, btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), save].filter(Boolean)
        });
    }

    /** What a user did, newest first, with date and time. Read only. */
    async function userActivity(u) {
        const list = h('div.loader');
        const s = UI.sheet({title: t('Activity') + ' · ' + (u.username ? '@' + u.username : u.name), body: [list], actions: [btn(t('Close'), {kind: 'text', onclick: () => s.close()})]});
        let d;
        try {
            d = await Api.get('admin.user.activity&id=' + u.id);
        } catch (e) {
            return list.replaceWith(h('p.muted', e.message));
        }
        const when = iso => new Date(iso).toLocaleString(I18N.locale, {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'});
        const stars = n => '★'.repeat(n || 0) + '☆'.repeat(Math.max(0, 3 - (n || 0)));
        const what = e => {
            switch (e.kind) {
                case 'lesson':
                    return [e.m ? 'fire' : 'book', e.m ? t('Level skipped by the exam: {c} / {l}', {c: e.a, l: e.b}) : t('Level completed: {c} / {l} {s}', {c: e.a, l: e.b, s: stars(e.n)})];
                case 'drill':
                    return ['bolt', t('Exercise done in {c} / {l} {s}', {c: e.a, l: e.b, s: stars(e.n)})];
                case 'game':
                    return ['game', e.m ? t('Game won: {g} {s}', {g: e.a, s: stars(e.n)}) : t('Game lost: {g}', {g: e.a})];
                case 'daily':
                    return ['calendar', t('Daily challenge: {a} of {b} right', {a: e.n, b: e.m})];
                case 'exam':
                    return ['school', t('Final exam {c}: {n}%', {c: e.a, n: e.n})];
                case 'certificate':
                    return ['cert', t('Certificate {c} claimed ({code})', {c: e.a, code: e.b})];
                case 'badge':
                    return ['trophy', t('Badge earned: {b}', {b: e.a})];
                case 'hint':
                    return ['bulb', t('Help bought for {n} Sparks ({r})', {n: e.n, r: e.a})];
                case 'prize':
                    return ['trophy', t('Leaderboard prize: place {p}, +{n} Sparks (end of day)', {p: e.m, n: e.n})];
                case 'avatar':
                    return ['image', t('Profile picture bought: {i} for {n} Sparks', {i: Avatars.item(e.a) ? Avatars.name(Avatars.item(e.a)) : e.a, n: e.n})];
                case 'points':
                    return ['edit', t('Changed from the portal · {f}: {a} → {b}', {f: e.a === 'xp' ? t('Grade XP') : t('Sparks'), a: e.n, b: e.m})];
                default:
                    return ['info', e.kind];
            }
        };
        const rows = d.events.map(e => {
            const [ic, text] = what(e);
            return h('li.adm-event', icon(ic), h('time', {datetime: e.at}, when(e.at)), h('span', text));
        });
        const days = d.days.length ? h('details.adm-days', h('summary', t('XP by day')), table([t('Day'), 'XP'], d.days.map(x => h('tr', h('td', new Date(x.day + 'T12:00:00').toLocaleDateString(I18N.locale, {weekday: 'short', day: 'numeric', month: 'short', year: 'numeric'})), h('td', String(x.xp)))))) : null;
        list.replaceWith(h('div', rows.length ? h('ul.adm-events', rows) : h('p.muted', t('No activity yet.')), days));
    }

    async function viewCertificates() {
        const d = await Api.get('admin.certificates');
        return page(t('Certificates'), null, d.certificates.length ? table([t('Name'), 'Email', t('Course'), t('Score'), t('Date'), ''], d.certificates.map(c => h('tr',
            h('td', h('a', {href: `certificate.php?c=${c.code}&lang=${I18N.lang}`, target: '_blank', rel: 'noopener'}, c.name)), h('td', c.email), h('td', c.course), h('td', c.score + '%'), h('td', UI.fmtDate(c.issued_at.slice(0, 10))),
            h('td.nowrap', iconBtn('edit', t('Name on the certificate'), () => renameCertificate(c)), iconBtn('delete', t('Revoke'), async () => {
                if (!await confirm(t('Revoke this certificate?'), t('Its link stops working.'), t('Revoke'), true)) return;
                await Api.post('admin.certificate.delete', {code: c.code}).catch(e => toast(e.message, 'error'));
                render();
            }))))) : h('p.muted', t('No certificates yet.')));
    }

    function renameCertificate(c) {
        const name = inp({value: c.name, maxlength: 80});
        const save = btn(t('Save'), {
            icon: 'save', onclick: async () => {
                try {
                    await busy(save, () => Api.post('admin.certificate.rename', {code: c.code, name: name.value}));
                    s.close();
                    toast(t('Saved'), 'ok');
                    render();
                } catch (e) {
                    toast(e.message, 'error');
                }
            }
        });
        const s = UI.sheet({title: t('Name on the certificate'), body: [f(t('Full name'), name)], actions: [btn(t('Cancel'), {kind: 'text', onclick: () => s.close()}), save]});
    }

    // ---------------------------------------------------------------- profile pictures

    /** Price in Sparks and sale of each character, accessory, frame and background; the drawings are in assets/avatars.js. */
    async function viewAvatars() {
        const d = await Api.get('admin.avatars');
        const previewCode = (kind, id) => kind === 'character' ? id + ':violet' : kind === 'accessory' ? ':violet:' + id : kind === 'frame' ? ':violet::' + id : ':' + id;
        const section = (kind, title) => {
            const rows = d.items.filter(x => x.kind === kind).sort((a, b) => a.price - b.price).map(x => {
                const art = Avatars.item(x.id);
                const price = inp({type: 'number', min: 0, max: 100000, value: x.price, 'aria-label': t('Price')});
                price.classList.add('adm-price');
                const on = h('input', {type: 'checkbox', checked: !!+x.enabled, 'aria-label': t('On sale')});
                const save = iconBtn('save', t('Save'), () => act(save, () => Api.post('admin.avatar.save', {id: x.id, price: price.value, enabled: on.checked})));
                return h('tr', h('td', Avatars.render(previewCode(kind, x.id), kind === 'accessory' ? '' : 'A', 'sm')), h('td', art ? Avatars.name(art) : x.id),
                    h('td', price), h('td', h('label.check', on, h('span', t('On sale')))), h('td', String(x.sold)), h('td.nowrap', save));
            });
            return [h('h2.section-title', title), table(['', t('Name'), t('Price (Sparks)'), t('Sale'), t('Sold'), ''], rows)];
        };
        return page(t('Profile pictures'), null,
            h('p.muted', t('Learners buy these with Sparks from their profile. At 0 an item is free for everyone. An item taken off sale stays with whoever already bought it.')),
            section('character', t('Characters')), section('accessory', t('Accessories')), section('frame', t('Frames')), section('background', t('Backgrounds')));
    }

    const SETTINGS = [
        ['XP and Sparks', [['xp_lesson_base', 'Level completed (× difficulty)'], ['xp_lesson_star', 'Each star of a level'], ['xp_task', 'Exercise solved'], ['xp_drill', 'Drill solved'], ['xp_drill_perfect', 'Drill: bonus without mistakes'], ['xp_daily_correct', 'Daily challenge: each right answer'], ['xp_daily_perfect', 'Daily challenge: bonus without mistakes'], ['xp_certificate', 'Exam passed'], ['xp_game_max', 'Game: maximum per match'], ['xp_game_daily_cap', 'Games: daily maximum'], ['xp_badge', 'Each new badge']]],
        ['Hint prices (Sparks)', [['hint_question', 'Hint on a question'], ['hint_task', 'Hint on an exercise'], ['hint_game', 'Help in a game'], ['hint_drill', 'Hint in a drill']]],
        ['Final exam', [['exam_questions', 'Number of questions'], ['exam_pass_percent', 'Pass mark (%)'], ['exam_minutes', 'Time limit in minutes (0 = none)'], ['cert_cost', 'Certificate price (Sparks)']]],
        ['Daily leaderboard', [['board_per_player', 'First prize: Sparks per participant'], ['board_max', 'First prize: maximum']]],
    ];

    async function viewSettings() {
        const d = await Api.get('admin.settings');
        const controls = {};
        const groups = SETTINGS.map(([name, list]) => [h('h2', t(name)), h('div.adm-grid', list.map(([k, label]) => f(t(label), controls[k] = inp({type: 'number', min: 0, value: d.settings[k]}), t('Default: {n}', {n: d.defaults[k]}))))]);
        const save = btn(t('Save'), {icon: 'save', onclick: () => act(save, () => Api.post('admin.settings.save', {settings: Object.fromEntries(Object.entries(controls).map(([k, c]) => [k, c.value]))}))});
        return page(t('Settings'), null, h('div.adm-form', groups, h('div.adm-actions', save)));
    }

    // ---------------------------------------------------------------- shell

    const go = path => {
        location.hash = '#/' + path;
    };

    function shell(active, content) {
        const lang = sel(I18N.langs, I18N.lang);
        lang.setAttribute('aria-label', t('Language'));
        lang.addEventListener('change', () => I18N.set(lang.value));
        // On a phone: a top bar (brand, language, back to the app) and the sections in a strip that scrolls sideways
        const lang2 = sel(I18N.langs, I18N.lang);
        lang2.setAttribute('aria-label', t('Language'));
        lang2.addEventListener('change', () => I18N.set(lang2.value));
        const top = h('header.adm-top', h('a.sn-brand', {href: 'index.html'}, h('img', {src: 'assets/icons/icon-192.png?v=2', alt: ''}), h('strong', 'Fucina'), h('span.muted.small', 'Admin')),
            h('div.grow'), lang2, iconBtn('back', t('Back to the app'), () => location.href = 'index.html'));
        const nav = h('nav.adm-side', {'aria-label': t('Sections')},
            h('a.sn-brand', {href: 'index.html'}, h('img', {src: 'assets/icons/icon-192.png?v=2', alt: ''}), h('strong', 'Fucina')), h('span.muted.small', t('Admin portal')),
            TABS.map(([id, label, ic]) => h('a.sn-link' + (active === id ? '.active' : ''), {href: '#/' + id}, icon(ic), h('span', t(label)))),
            h('div.grow'), lang, h('a.sn-link.back', {href: 'index.html'}, icon('back'), h('span', t('Back to the app'))));
        root.replaceChildren(h('div.adm', top, nav, h('main.adm-main', content)));
        const on = nav.querySelector('.sn-link.active');
        if (on && matchMedia('(max-width: 760px)').matches) setTimeout(() => nav.scrollLeft = on.offsetLeft - 12, 0);
    }

    async function render() {
        const [name, a, b] = (location.hash.replace(/^#\/?/, '') || 'overview').split('/').map(decodeURIComponent);
        const views = {overview: viewOverview, courses: viewCourses, 'course-new': viewCourseNew, course: viewCourse, lesson: viewLesson, 'lesson-new': viewLessonNew, games: viewGames, game: viewGame, users: viewUsers, certificates: viewCertificates, avatars: viewAvatars, settings: viewSettings};
        const active = {'course-new': 'courses', course: 'courses', lesson: 'courses', 'lesson-new': 'courses', game: 'games'}[name] || name;
        let content;
        try {
            content = await (views[name] || viewOverview)(a, b);
        } catch (e) {
            content = page(t('Something went wrong'), null, h('p', e.message));
        }
        shell(active, content);
        document.title = 'Fucina Admin';
    }

    async function start() {
        UI.applyTheme();
        try {
            const d = await Api.get('auth.me', {silentAuth: true});
            me = d.user;
        } catch (e) {
            me = null;
        }
        if (!me || me.role < 5) {
            root.replaceChildren(h('div.adm-denied', h('h1', 'Fucina'), h('p', me ? t('This area is for administrators.') : t('Sign in to the app with an administrator account.')), h('a.btn.btn-filled', {href: 'index.html'}, h('span', t('Back to the app')))));
            return;
        }
        render();
    }

    window.addEventListener('hashchange', () => {
        if (UI.sheetsOpen()) UI.closeAllSheets();
        render();
    });
    start();
})();
