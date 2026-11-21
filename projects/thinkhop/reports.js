/*
 * ThinkHop — reporting a question. A signed-in player flags the question in front of them, says what is wrong and,
 * if they like, describes it; an administrator closes the report in the admin panel. When the question gets fixed the
 * player earns REWARD points, and is told about it (push notification and a message the next time the game opens).
 */
(function () {
    'use strict';

    const I = window.ThinkHopI18n;
    const A = window.ThinkHopAccount;
    const {h, icon, toast, modal} = window.ThinkHopUI;
    const t = I.t;
    const REWARD = 50;
    const REASONS = ['wrong_answer', 'unclear', 'graphics', 'translation', 'other'];

    I.extend({
        en: {
            report_btn: 'Report a problem', report_title: 'Report this question',
            report_intro: 'Found a mistake? Tell us: if the question gets fixed you earn {n} points.',
            report_reason: 'What is wrong?', report_wrong_answer: 'The right answer is wrong', report_unclear: 'The text is not clear',
            report_graphics: 'Something looks broken', report_translation: 'Translation mistake', report_other: 'Something else',
            report_desc: 'Description', report_desc_ph: 'What did you notice? (optional, needed for “Something else”)',
            report_send: 'Send report', report_sent: 'Thanks! We will let you know when your report is closed.',
            err_already_reported: 'You have already reported this question.', err_report_describe: 'Describe the problem in at least 10 characters.',
            report_login: 'Sign in to report a question: that way you can get the points if it is fixed.',
            reports_mine: 'My reports', reports_none: 'You have not reported any question yet.',
            report_status_open: 'Waiting', report_status_resolved: 'Fixed · +{n} points', report_status_not_error: 'Closed: not a mistake',
            report_status_duplicate: 'Closed: already reported', report_status_cannot_reproduce: 'Closed: could not be reproduced', report_status_wont_fix: 'Closed: will stay as it is',
            report_news_resolved: 'A question you reported has been fixed: +{n} points!', report_news_closed: 'A report of yours has been closed — {why}.',
            notify_reports: 'Reports', notify_reports_hint: 'When a question you reported is fixed or closed',
        },
        it: {
            report_btn: 'Segnala un problema', report_title: 'Segnala questa domanda',
            report_intro: 'Hai trovato un errore? Raccontacelo: se la domanda viene corretta ricevi {n} punti.',
            report_reason: 'Che cosa non va?', report_wrong_answer: 'La risposta giusta è sbagliata', report_unclear: 'Il testo non è chiaro',
            report_graphics: 'Qualcosa si vede male', report_translation: 'Errore di traduzione', report_other: 'Altro',
            report_desc: 'Descrizione', report_desc_ph: 'Che cosa hai notato? (facoltativa, obbligatoria per «Altro»)',
            report_send: 'Invia segnalazione', report_sent: 'Grazie! Ti avviseremo quando la segnalazione sarà chiusa.',
            err_already_reported: 'Hai già segnalato questa domanda.', err_report_describe: 'Descrivi il problema in almeno 10 caratteri.',
            report_login: 'Accedi per segnalare una domanda: così puoi ricevere i punti se viene corretta.',
            reports_mine: 'Le mie segnalazioni', reports_none: 'Non hai ancora segnalato nessuna domanda.',
            report_status_open: 'In attesa', report_status_resolved: 'Risolta · +{n} punti', report_status_not_error: 'Chiusa: non è un errore',
            report_status_duplicate: 'Chiusa: già segnalata', report_status_cannot_reproduce: 'Chiusa: non riproducibile', report_status_wont_fix: 'Chiusa: resterà così',
            report_news_resolved: 'Una domanda che hai segnalato è stata corretta: +{n} punti!', report_news_closed: 'Una tua segnalazione è stata chiusa — {why}.',
            notify_reports: 'Segnalazioni', notify_reports_hint: 'Quando una domanda che hai segnalato viene corretta o chiusa',
        },
        es: {
            report_btn: 'Reportar un problema', report_title: 'Reportar esta pregunta',
            report_intro: '¿Has encontrado un error? Cuéntanoslo: si la pregunta se corrige ganas {n} puntos.',
            report_reason: '¿Qué falla?', report_wrong_answer: 'La respuesta correcta está mal', report_unclear: 'El texto no está claro',
            report_graphics: 'Algo se ve mal', report_translation: 'Error de traducción', report_other: 'Otra cosa',
            report_desc: 'Descripción', report_desc_ph: '¿Qué has notado? (opcional, obligatoria para «Otra cosa»)',
            report_send: 'Enviar reporte', report_sent: '¡Gracias! Te avisaremos cuando se cierre el reporte.',
            err_already_reported: 'Ya has reportado esta pregunta.', err_report_describe: 'Describe el problema con al menos 10 caracteres.',
            report_login: 'Inicia sesión para reportar una pregunta: así puedes recibir los puntos si se corrige.',
            reports_mine: 'Mis reportes', reports_none: 'Todavía no has reportado ninguna pregunta.',
            report_status_open: 'En espera', report_status_resolved: 'Corregida · +{n} puntos', report_status_not_error: 'Cerrado: no es un error',
            report_status_duplicate: 'Cerrado: ya reportada', report_status_cannot_reproduce: 'Cerrado: no reproducible', report_status_wont_fix: 'Cerrado: se quedará así',
            report_news_resolved: 'Una pregunta que reportaste ha sido corregida: ¡+{n} puntos!', report_news_closed: 'Se ha cerrado un reporte tuyo — {why}.',
            notify_reports: 'Reportes', notify_reports_hint: 'Cuando una pregunta que reportaste se corrige o se cierra',
        },
        fr: {
            report_btn: 'Signaler un problème', report_title: 'Signaler cette question',
            report_intro: 'Tu as trouvé une erreur ? Dis-le-nous : si la question est corrigée, tu gagnes {n} points.',
            report_reason: 'Qu’est-ce qui ne va pas ?', report_wrong_answer: 'La bonne réponse est fausse', report_unclear: 'Le texte n’est pas clair',
            report_graphics: 'Quelque chose s’affiche mal', report_translation: 'Erreur de traduction', report_other: 'Autre chose',
            report_desc: 'Description', report_desc_ph: 'Qu’as-tu remarqué ? (facultatif, obligatoire pour « Autre chose »)',
            report_send: 'Envoyer le signalement', report_sent: 'Merci ! Nous te préviendrons quand le signalement sera clôturé.',
            err_already_reported: 'Tu as déjà signalé cette question.', err_report_describe: 'Décris le problème en au moins 10 caractères.',
            report_login: 'Connecte-toi pour signaler une question : tu pourras ainsi recevoir les points si elle est corrigée.',
            reports_mine: 'Mes signalements', reports_none: 'Tu n’as encore signalé aucune question.',
            report_status_open: 'En attente', report_status_resolved: 'Corrigée · +{n} points', report_status_not_error: 'Clôturé : pas une erreur',
            report_status_duplicate: 'Clôturé : déjà signalée', report_status_cannot_reproduce: 'Clôturé : non reproductible', report_status_wont_fix: 'Clôturé : restera ainsi',
            report_news_resolved: 'Une question que tu as signalée a été corrigée : +{n} points !', report_news_closed: 'Un de tes signalements a été clôturé — {why}.',
            notify_reports: 'Signalements', notify_reports_hint: 'Quand une question que tu as signalée est corrigée ou clôturée',
        },
        de: {
            report_btn: 'Problem melden', report_title: 'Diese Frage melden',
            report_intro: 'Einen Fehler gefunden? Sag es uns: Wird die Frage korrigiert, bekommst du {n} Punkte.',
            report_reason: 'Was stimmt nicht?', report_wrong_answer: 'Die richtige Antwort ist falsch', report_unclear: 'Der Text ist unklar',
            report_graphics: 'Etwas wird falsch angezeigt', report_translation: 'Übersetzungsfehler', report_other: 'Etwas anderes',
            report_desc: 'Beschreibung', report_desc_ph: 'Was ist dir aufgefallen? (optional, Pflicht bei „Etwas anderes“)',
            report_send: 'Meldung senden', report_sent: 'Danke! Wir sagen dir Bescheid, wenn die Meldung geschlossen wird.',
            err_already_reported: 'Du hast diese Frage schon gemeldet.', err_report_describe: 'Beschreibe das Problem mit mindestens 10 Zeichen.',
            report_login: 'Melde dich an, um eine Frage zu melden: So kannst du die Punkte bekommen, wenn sie korrigiert wird.',
            reports_mine: 'Meine Meldungen', reports_none: 'Du hast noch keine Frage gemeldet.',
            report_status_open: 'Offen', report_status_resolved: 'Korrigiert · +{n} Punkte', report_status_not_error: 'Geschlossen: kein Fehler',
            report_status_duplicate: 'Geschlossen: schon gemeldet', report_status_cannot_reproduce: 'Geschlossen: nicht nachvollziehbar', report_status_wont_fix: 'Geschlossen: bleibt so',
            report_news_resolved: 'Eine Frage, die du gemeldet hast, wurde korrigiert: +{n} Punkte!', report_news_closed: 'Eine deiner Meldungen wurde geschlossen — {why}.',
            notify_reports: 'Meldungen', notify_reports_hint: 'Wenn eine Frage, die du gemeldet hast, korrigiert oder geschlossen wird',
        },
    });

    const statusText = r => t('report_status_' + r.status, {n: r.reward || REWARD});

    /** The report form for question q; `picked` is the option the player chose (-1 if none yet). */
    function open(q, picked, hooks) {
        hooks = hooks || {};
        if (!A.enabled || !A.enabled()) return;
        if (!A.user()) {
            toast(t('report_login'));
            A.open();
            return;
        }
        let reason = '';
        const desc = h('textarea.ctl.report-desc', {rows: 4, maxlength: 1000, placeholder: t('report_desc_ph'), 'aria-label': t('report_desc')});
        const err = h('p.form-error', {role: 'alert', hidden: true});
        const send = h('button.btn.btn-primary', {type: 'button', disabled: true}, icon('flag'), h('span', t('report_send')));
        const choices = REASONS.map(k => h('label.report-reason',
            h('input', {type: 'radio', name: 'report-reason', value: k, onchange: () => { reason = k; send.disabled = false; }}),
            h('span', t('report_' + k))));
        const m = modal([
            h('h3', icon('flag'), ' ', t('report_title')),
            h('p.muted', t('report_intro', {n: REWARD})),
            h('fieldset.report-reasons', h('legend', t('report_reason')), choices),
            h('label.field', h('span', t('report_desc')), desc),
            err,
            h('div.actions', h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('cancel')), send),
        ], {onclose: hooks.onclose});
        m.box.classList.add('report-modal');
        if (hooks.onopen) hooks.onopen();
        send.addEventListener('click', async () => {
            const text = desc.value.trim();
            if (reason === 'other' && text.length < 10) {
                err.textContent = t('err_report_describe');
                err.hidden = false;
                desc.focus();
                return;
            }
            send.disabled = true;
            try {
                // A copy of the question as the player saw it: the admin panel draws it again exactly like this
                const copy = JSON.parse(JSON.stringify(q));
                delete copy.shown;
                await A.api('reports.create', {question: copy, picked, reason, description: text, lang: I.getLang()});
                m.close();
                toast(t('report_sent'), 'ok');
            } catch (e) {
                err.textContent = e.message;
                err.hidden = false;
                send.disabled = false;
            }
        });
    }

    async function showMine() {
        const body = h('div.report-list', h('p.muted', t('loading')));
        const m = modal([h('h3', icon('flag'), ' ', t('reports_mine')), body,
            h('div.actions', h('button.btn.btn-primary', {type: 'button', onclick: () => m.close()}, t('close')))]);
        try {
            const d = await A.api('reports.mine');
            if (!d.reports.length) return body.replaceChildren(h('p.muted', t('reports_none')));
            const date = s => new Date(s.replace(' ', 'T') + 'Z').toLocaleDateString(I.getLang(), {dateStyle: 'medium'});
            body.replaceChildren(...d.reports.map(r => h('div.report-item',
                h('div.report-item-head', h('strong', t('cat_' + r.cat)), h('span.report-status.st-' + r.status, statusText(r))),
                h('small.muted', date(r.created) + ' · ' + t('report_' + r.reason)),
                r.description ? h('p', r.description) : null)));
        } catch (e) {
            body.replaceChildren(h('p.muted', e.message));
        }
    }

    /** At start: tells once about the reports closed since last time. */
    async function checkClosed() {
        if (!A.user || !A.user()) return;
        try {
            const d = await A.api('reports.mine');
            const news = d.reports.filter(r => r.news);
            if (!news.length) return;
            A.api('reports.seen', {}).catch(() => {});
            news.slice(0, 3).forEach((r, i) => setTimeout(() => {
                if (r.status === 'resolved') toast(t('report_news_resolved', {n: r.reward}), 'ok');
                else toast(t('report_news_closed', {why: statusText(r).replace(/^[^:]*:\s*/, '')}));
            }, 600 + i * 2500));
        } catch (e) { /* offline: next time */
        }
    }

    window.ThinkHopReports = {open, showMine, checkClosed};
})();
