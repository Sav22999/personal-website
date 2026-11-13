/**
 * MyCurriculum — texts.
 * UI: the app itself (English and Italian). CV: the words printed on a CV, in every language a CV can be written in.
 */

export const UI_LANGS = ['en', 'it'];
export const CV_LANGS = ['it', 'en', 'fr', 'de', 'es', 'pt', 'nl', 'pl', 'ro', 'sv'];

/** Language names in their own language (public language menu). */
export const LANG_NAMES = {it: 'Italiano', en: 'English', fr: 'Français', de: 'Deutsch', es: 'Español', pt: 'Português', nl: 'Nederlands', pl: 'Polski', ro: 'Română', sv: 'Svenska'};

/** Flags are SVG images (assets/flags/, from flag-icons), never emoji: emoji flags do not show on Windows. */
const FLAG_FILES = {it: 'it', en: 'gb', fr: 'fr', de: 'de', es: 'es', pt: 'pt', nl: 'nl', pl: 'pl', ro: 'ro', sv: 'se'};

export function flagSrc(lang) {
    return 'assets/flags/' + (FLAG_FILES[lang] || 'gb') + '.svg';
}

const UI = {
    en: {
        tagline: 'Your CV, in every language you speak',
        hero_title: 'A clear, beautiful CV in minutes',
        hero_body: 'Write it once, translate it into as many languages as you need, choose a template and download each version as a PDF. Every CV also gets its own web page to share.',
        feat_langs: 'Many languages', feat_langs_b: 'One CV, a version for each language, each one downloadable.',
        feat_tpl: 'Templates', feat_tpl_b: 'Four layouts, your colour, sans or serif fonts.',
        feat_web: 'Web page', feat_web_b: 'A public link with a language menu: only what you choose to show.',
        feat_import: 'PDF import', feat_import_b: 'Start from the CV you already have: the fields fill themselves.',
        signin: 'Sign in', signup: 'Create account', or: 'or', email: 'Email', password: 'Password', name: 'Full name',
        username: 'Address of your public CV', username_hint: 'Letters, digits and dashes (3–30). You can change it later.',
        accept_privacy: 'I have read the <a href="privacy.html" target="_blank">privacy policy</a>',
        no_account: 'No account yet?', have_account: 'Already registered?',
        check_email_title: 'Check your inbox', check_email_body: 'If {email} can be used, we have just sent a link to confirm it. Open it to start writing your CV.',
        verify_title: 'Confirm your email', verify_body: 'We sent a confirmation link to <b>{email}</b>. Open it to use MyCurriculum.',
        verify_resend: 'Send the link again', verify_sent: 'Sent: check your inbox (and the spam folder).',
        verify_nomail: 'Email sending is not configured on this server, so the link cannot arrive.',
        verify_link_title: 'Confirm your email', verify_link_body: 'Press the button to confirm your address and sign in.', verify_link_btn: 'Confirm and sign in',
        google_finish: 'Almost done', google_finish_body: 'Choose the address of your public CV and accept the privacy policy.',
        continue: 'Continue', cancel: 'Cancel', close: 'Close', save: 'Save', delete: 'Delete', confirm: 'Confirm', back: 'Back',
        logout: 'Sign out', account: 'Account',
        tab_content: 'Content', tab_design: 'Design', tab_share: 'Share', tab_preview: 'Preview', tab_edit: 'Edit', preview_show: 'Show preview', preview_hide: 'Hide preview',
        saving: 'Saving…', saved: 'Saved', save_failed: 'Not saved — retrying',
        cv_lang: 'CV language', add_lang: 'Add a language', add_lang_from: 'Start from', add_lang_empty: 'Empty sections', remove_lang: 'Remove this language',
        remove_lang_confirm: 'Remove the {lang} version of your CV? Its texts will be lost.',
        lang_copy_hint: 'The texts are copied: translate them afterwards.',
        profile: 'Personal details', photo: 'Photo', photo_upload: 'Upload', photo_change: 'Change', photo_remove: 'Remove',
        first_name: 'First name', last_name: 'Last name', headline: 'Job title / headline', location: 'City, country',
        phone: 'Phone', website: 'Website', birth: 'Date of birth', links: 'Links', link_label: 'Label', link_url: 'URL', add_link: 'Add link',
        custom_fields: 'Custom fields', custom_fields_hint: 'E.g. Nationality, Driving licence, Availability.', add_field: 'Add field', field_label: 'Label', field_value: 'Value',
        show_on_cv: 'Show on CV', hidden_on_cv: 'Hidden', shared_all_langs: 'Shared by every language',
        summary: 'Profile', summary_hint: 'Two or three lines about you.',
        sections: 'Sections', add_section: 'Add section', add_item: 'Add', move_up: 'Move up', move_down: 'Move down', section_title: 'Title',
        section_hidden: 'Hidden section',
        t_role: 'Role / degree', t_org: 'Company / school', t_place: 'Place', t_start: 'From', t_end: 'To', t_current: 'Ongoing', t_text: 'Description',
        date_hint: 'MM/YYYY or YYYY', t_skill: 'Skill', t_level: 'Level', t_lang: 'Language', t_title: 'Title', t_subtitle: 'Subtitle', t_date: 'Date', t_url: 'Link',
        text_section: 'Text',
        footer: 'Footer', footer_hint: 'Printed at the bottom of the CV. In Italy and Poland CVs usually carry a data-processing consent.',
        footer_consent: 'Insert the privacy consent',
        k_experience: 'Work experience', k_education: 'Education', k_skills: 'Skills', k_languages: 'Languages', k_certifications: 'Certifications',
        k_projects: 'Projects', k_volunteering: 'Volunteering', k_awards: 'Awards', k_publications: 'Publications', k_interests: 'Interests',
        k_list: 'Custom list', k_text: 'Custom text',
        design_template: 'Template', design_color: 'Colour', design_font: 'Font', font_sans: 'Sans serif', font_serif: 'Serif',
        tpl_classic: 'Classic', tpl_modern: 'Modern', tpl_minimal: 'Minimal', tpl_elegant: 'Elegant',
        share_public: 'Public web page', share_public_on: 'Your CV is online at this address:', share_public_off: 'Your CV is private: only you can see it.',
        share_copy: 'Copy link', share_copied: 'Link copied', share_open: 'Open page',
        share_address: 'Address', share_change: 'Change address',
        share_default_lang: 'Language shown first', share_allow_pdf: 'Visitors can download the PDF',
        share_views: '{n} visits', share_note: 'The page shows exactly what the PDF shows: hidden fields and sections never leave the server.',
        pdf: 'Download PDF', pdf_making: 'Preparing the PDF…', pdf_all: 'All languages',
        import_pdf: 'Import from PDF', import_title: 'Import an existing CV',
        import_body: 'Choose the PDF of your current CV: the text is read in your browser (the file is not uploaded) and the fields are filled in automatically.',
        import_into: 'Import into the {lang} version', import_replace: 'The sections of this language will be replaced. Personal details are filled only where empty.',
        import_choose: 'Choose PDF', import_reading: 'Reading the PDF…', import_done: 'Imported {n} sections. Check every field carefully: automatic import is never perfect.',
        import_undo: 'Undo', import_scanned: 'No text found in this PDF (maybe it is a scanned image).', import_failed: 'The PDF could not be read.',
        acc_email: 'Email of the account', acc_email_note: 'The email of an account cannot be changed.', acc_password: 'Change password', acc_set_password: 'Set a password',
        acc_current: 'Current password', acc_new: 'New password', acc_delete: 'Delete account', acc_delete_body: 'Your CV, photo and account will be deleted forever.',
        acc_google: 'Linked to Google',
        privacy: 'Privacy', made_by: 'Made by',
        preview_private: 'Preview: this CV is private. Only you can see this page.',
        not_found: 'This CV does not exist or is not public.', go_home: 'Create your CV with MyCurriculum',
        empty_name: 'Your name',
        err_generic: 'Something went wrong. Try again.', err_not_installed: 'MyCurriculum is not installed yet (run install-db.php).',
        err_bad_credentials: 'Wrong email or password.', err_rate_limited: 'Too many attempts: wait a few minutes.', err_email_invalid: 'Invalid email address.',
        err_password_short: 'The password needs at least 8 characters.', err_name_required: 'Write your name.', err_username_invalid: 'Address not valid: 3–30 letters, digits or dashes.',
        err_username_taken: 'This address is already taken.', err_privacy_required: 'Please accept the privacy policy.', err_token_invalid: 'The link is expired or was already used.',
        err_bad_password: 'Wrong password.', err_disabled: 'This account is disabled.', err_google_token: 'Google sign-in failed.', err_google_email: 'Your Google email is not verified.',
        err_photo_invalid: 'Image not valid.', err_photo_too_big: 'Image too large.', err_cv_too_big: 'This CV is too long.', err_csrf: 'Session expired: reload the page.',
        err_unauthenticated: 'Please sign in again.', err_network: 'No connection.',
    },
    it: {
        tagline: 'Il tuo CV, in tutte le lingue che parli',
        hero_title: 'Un curriculum chiaro e curato, in pochi minuti',
        hero_body: 'Scrivilo una volta, traducilo in tutte le lingue che ti servono, scegli un modello e scarica ogni versione in PDF. Ogni CV ha anche la sua pagina web da condividere.',
        feat_langs: 'Più lingue', feat_langs_b: 'Un CV, una versione per lingua, ognuna scaricabile.',
        feat_tpl: 'Modelli', feat_tpl_b: 'Quattro impaginazioni, il tuo colore, caratteri con o senza grazie.',
        feat_web: 'Pagina web', feat_web_b: 'Un link pubblico con menu delle lingue: solo ciò che scegli di mostrare.',
        feat_import: 'Importa da PDF', feat_import_b: 'Parti dal CV che hai già: i campi si compilano da soli.',
        signin: 'Accedi', signup: 'Crea account', or: 'oppure', email: 'Email', password: 'Password', name: 'Nome e cognome',
        username: 'Indirizzo del tuo CV pubblico', username_hint: 'Lettere, numeri e trattini (3–30). Potrai cambiarlo dopo.',
        accept_privacy: 'Ho letto l’<a href="privacy.html" target="_blank">informativa privacy</a>',
        no_account: 'Non hai un account?', have_account: 'Hai già un account?',
        check_email_title: 'Controlla la posta', check_email_body: 'Se {email} può essere usato, ti abbiamo appena inviato un link per confermarlo. Aprilo per iniziare a scrivere il tuo CV.',
        verify_title: 'Conferma la tua email', verify_body: 'Abbiamo inviato un link di conferma a <b>{email}</b>. Aprilo per usare MyCurriculum.',
        verify_resend: 'Invia di nuovo il link', verify_sent: 'Inviato: controlla la posta (anche lo spam).',
        verify_nomail: 'L’invio di email non è configurato su questo server: il link non può arrivare.',
        verify_link_title: 'Conferma la tua email', verify_link_body: 'Premi il pulsante per confermare l’indirizzo e accedere.', verify_link_btn: 'Conferma e accedi',
        google_finish: 'Ci siamo quasi', google_finish_body: 'Scegli l’indirizzo del tuo CV pubblico e accetta l’informativa privacy.',
        continue: 'Continua', cancel: 'Annulla', close: 'Chiudi', save: 'Salva', delete: 'Elimina', confirm: 'Conferma', back: 'Indietro',
        logout: 'Esci', account: 'Account',
        tab_content: 'Contenuti', tab_design: 'Aspetto', tab_share: 'Condividi', tab_preview: 'Anteprima', tab_edit: 'Modifica', preview_show: 'Mostra anteprima', preview_hide: 'Nascondi anteprima',
        saving: 'Salvataggio…', saved: 'Salvato', save_failed: 'Non salvato — nuovo tentativo',
        cv_lang: 'Lingua del CV', add_lang: 'Aggiungi una lingua', add_lang_from: 'Parti da', add_lang_empty: 'Sezioni vuote', remove_lang: 'Rimuovi questa lingua',
        remove_lang_confirm: 'Rimuovere la versione in {lang} del CV? I suoi testi andranno persi.',
        lang_copy_hint: 'I testi vengono copiati: traducili dopo.',
        profile: 'Dati personali', photo: 'Foto', photo_upload: 'Carica', photo_change: 'Cambia', photo_remove: 'Rimuovi',
        first_name: 'Nome', last_name: 'Cognome', headline: 'Professione / titolo', location: 'Città, paese',
        phone: 'Telefono', website: 'Sito web', birth: 'Data di nascita', links: 'Link', link_label: 'Etichetta', link_url: 'URL', add_link: 'Aggiungi link',
        custom_fields: 'Campi personalizzati', custom_fields_hint: 'Es. Nazionalità, Patente, Disponibilità.', add_field: 'Aggiungi campo', field_label: 'Etichetta', field_value: 'Valore',
        show_on_cv: 'Mostra nel CV', hidden_on_cv: 'Nascosto', shared_all_langs: 'Comune a tutte le lingue',
        summary: 'Profilo', summary_hint: 'Due o tre righe su di te.',
        sections: 'Sezioni', add_section: 'Aggiungi sezione', add_item: 'Aggiungi', move_up: 'Sposta su', move_down: 'Sposta giù', section_title: 'Titolo',
        section_hidden: 'Sezione nascosta',
        t_role: 'Ruolo / titolo di studio', t_org: 'Azienda / scuola', t_place: 'Luogo', t_start: 'Dal', t_end: 'Al', t_current: 'In corso', t_text: 'Descrizione',
        date_hint: 'MM/AAAA o AAAA', t_skill: 'Competenza', t_level: 'Livello', t_lang: 'Lingua', t_title: 'Titolo', t_subtitle: 'Sottotitolo', t_date: 'Data', t_url: 'Link',
        text_section: 'Testo',
        footer: 'Piè di pagina', footer_hint: 'Stampato in fondo al CV. In Italia e in Polonia si usa aggiungere il consenso al trattamento dei dati.',
        footer_consent: 'Inserisci il consenso privacy',
        k_experience: 'Esperienza professionale', k_education: 'Istruzione', k_skills: 'Competenze', k_languages: 'Lingue', k_certifications: 'Certificazioni',
        k_projects: 'Progetti', k_volunteering: 'Volontariato', k_awards: 'Premi', k_publications: 'Pubblicazioni', k_interests: 'Interessi',
        k_list: 'Elenco personalizzato', k_text: 'Testo personalizzato',
        design_template: 'Modello', design_color: 'Colore', design_font: 'Carattere', font_sans: 'Senza grazie', font_serif: 'Con grazie',
        tpl_classic: 'Classico', tpl_modern: 'Moderno', tpl_minimal: 'Minimal', tpl_elegant: 'Elegante',
        share_public: 'Pagina web pubblica', share_public_on: 'Il tuo CV è online a questo indirizzo:', share_public_off: 'Il tuo CV è privato: lo vedi solo tu.',
        share_copy: 'Copia link', share_copied: 'Link copiato', share_open: 'Apri pagina',
        share_address: 'Indirizzo', share_change: 'Cambia indirizzo',
        share_default_lang: 'Lingua mostrata per prima', share_allow_pdf: 'I visitatori possono scaricare il PDF',
        share_views: '{n} visite', share_note: 'La pagina mostra esattamente ciò che mostra il PDF: campi e sezioni nascosti non escono mai dal server.',
        pdf: 'Scarica PDF', pdf_making: 'Preparo il PDF…', pdf_all: 'Tutte le lingue',
        import_pdf: 'Importa da PDF', import_title: 'Importa un CV esistente',
        import_body: 'Scegli il PDF del tuo CV attuale: il testo viene letto nel tuo browser (il file non viene caricato) e i campi si compilano in automatico.',
        import_into: 'Importa nella versione in {lang}', import_replace: 'Le sezioni di questa lingua verranno sostituite. I dati personali vengono compilati solo dove sono vuoti.',
        import_choose: 'Scegli PDF', import_reading: 'Leggo il PDF…', import_done: 'Importate {n} sezioni. Controlla con attenzione ogni campo: l’importazione automatica non è mai perfetta.',
        import_undo: 'Annulla', import_scanned: 'Nessun testo trovato in questo PDF (forse è un’immagine scansionata).', import_failed: 'Impossibile leggere il PDF.',
        acc_email: 'Email dell’account', acc_email_note: 'L’email di un account non può essere modificata.', acc_password: 'Cambia password', acc_set_password: 'Imposta una password',
        acc_current: 'Password attuale', acc_new: 'Nuova password', acc_delete: 'Elimina account', acc_delete_body: 'CV, foto e account verranno eliminati per sempre.',
        acc_google: 'Collegato a Google',
        privacy: 'Privacy', made_by: 'Realizzato da',
        preview_private: 'Anteprima: questo CV è privato. Solo tu puoi vedere questa pagina.',
        not_found: 'Questo CV non esiste o non è pubblico.', go_home: 'Crea il tuo CV con MyCurriculum',
        empty_name: 'Il tuo nome',
        err_generic: 'Qualcosa è andato storto. Riprova.', err_not_installed: 'MyCurriculum non è ancora installato (esegui install-db.php).',
        err_bad_credentials: 'Email o password errate.', err_rate_limited: 'Troppi tentativi: aspetta qualche minuto.', err_email_invalid: 'Indirizzo email non valido.',
        err_password_short: 'La password deve avere almeno 8 caratteri.', err_name_required: 'Scrivi il tuo nome.', err_username_invalid: 'Indirizzo non valido: 3–30 lettere, numeri o trattini.',
        err_username_taken: 'Questo indirizzo è già occupato.', err_privacy_required: 'Accetta l’informativa privacy.', err_token_invalid: 'Il link è scaduto o è già stato usato.',
        err_bad_password: 'Password errata.', err_disabled: 'Questo account è disattivato.', err_google_token: 'Accesso con Google non riuscito.', err_google_email: 'La tua email Google non è verificata.',
        err_photo_invalid: 'Immagine non valida.', err_photo_too_big: 'Immagine troppo grande.', err_cv_too_big: 'Questo CV è troppo lungo.', err_csrf: 'Sessione scaduta: ricarica la pagina.',
        err_unauthenticated: 'Accedi di nuovo.', err_network: 'Nessuna connessione.',
    },
};

let uiLang = (() => {
    try {
        const saved = localStorage.getItem('mc_lang');
        if (UI_LANGS.includes(saved)) return saved;
    } catch (e) { /* storage blocked */ }
    return (navigator.language || 'en').toLowerCase().startsWith('it') ? 'it' : 'en';
})();

export function getLang() {
    return uiLang;
}

export function setLang(l) {
    if (!UI_LANGS.includes(l)) return;
    uiLang = l;
    try {
        localStorage.setItem('mc_lang', l);
    } catch (e) { /* storage blocked */ }
    document.documentElement.lang = l;
}

export function t(key, vars = {}) {
    let s = UI[uiLang][key] ?? UI.en[key] ?? key;
    for (const [k, v] of Object.entries(vars)) s = s.replaceAll('{' + k + '}', v);
    return s;
}

// ---------------------------------------------------------------- words printed on the CV

/** Section kinds: their type (how items look) and default title in every CV language. */
export const SECTION_KINDS = {
    experience: {type: 'timeline', it: 'Esperienza professionale', en: 'Work experience', fr: 'Expérience professionnelle', de: 'Berufserfahrung', es: 'Experiencia laboral', pt: 'Experiência profissional', nl: 'Werkervaring', pl: 'Doświadczenie zawodowe', ro: 'Experiență profesională', sv: 'Arbetslivserfarenhet'},
    education: {type: 'timeline', it: 'Istruzione e formazione', en: 'Education', fr: 'Formation', de: 'Ausbildung', es: 'Formación', pt: 'Formação', nl: 'Opleiding', pl: 'Wykształcenie', ro: 'Educație', sv: 'Utbildning'},
    skills: {type: 'skills', it: 'Competenze', en: 'Skills', fr: 'Compétences', de: 'Kenntnisse', es: 'Habilidades', pt: 'Competências', nl: 'Vaardigheden', pl: 'Umiejętności', ro: 'Competențe', sv: 'Färdigheter'},
    languages: {type: 'langs', it: 'Lingue', en: 'Languages', fr: 'Langues', de: 'Sprachen', es: 'Idiomas', pt: 'Idiomas', nl: 'Talen', pl: 'Języki', ro: 'Limbi', sv: 'Språk'},
    certifications: {type: 'list', it: 'Certificazioni', en: 'Certifications', fr: 'Certifications', de: 'Zertifikate', es: 'Certificaciones', pt: 'Certificações', nl: 'Certificaten', pl: 'Certyfikaty', ro: 'Certificări', sv: 'Certifieringar'},
    projects: {type: 'list', it: 'Progetti', en: 'Projects', fr: 'Projets', de: 'Projekte', es: 'Proyectos', pt: 'Projetos', nl: 'Projecten', pl: 'Projekty', ro: 'Proiecte', sv: 'Projekt'},
    volunteering: {type: 'timeline', it: 'Volontariato', en: 'Volunteering', fr: 'Bénévolat', de: 'Ehrenamt', es: 'Voluntariado', pt: 'Voluntariado', nl: 'Vrijwilligerswerk', pl: 'Wolontariat', ro: 'Voluntariat', sv: 'Ideellt arbete'},
    awards: {type: 'list', it: 'Premi e riconoscimenti', en: 'Awards', fr: 'Distinctions', de: 'Auszeichnungen', es: 'Premios', pt: 'Prémios', nl: 'Prijzen', pl: 'Nagrody', ro: 'Premii', sv: 'Utmärkelser'},
    publications: {type: 'list', it: 'Pubblicazioni', en: 'Publications', fr: 'Publications', de: 'Publikationen', es: 'Publicaciones', pt: 'Publicações', nl: 'Publicaties', pl: 'Publikacje', ro: 'Publicații', sv: 'Publikationer'},
    interests: {type: 'text', it: 'Interessi', en: 'Interests', fr: 'Centres d’intérêt', de: 'Interessen', es: 'Intereses', pt: 'Interesses', nl: 'Interesses', pl: 'Zainteresowania', ro: 'Interese', sv: 'Intressen'},
    list: {type: 'list', it: 'Altro', en: 'Other', fr: 'Divers', de: 'Sonstiges', es: 'Otros', pt: 'Outros', nl: 'Overig', pl: 'Inne', ro: 'Altele', sv: 'Övrigt'},
    text: {type: 'text', it: 'Altro', en: 'Other', fr: 'Divers', de: 'Sonstiges', es: 'Otros', pt: 'Outros', nl: 'Overig', pl: 'Inne', ro: 'Altele', sv: 'Övrigt'},
};

/** Small words of the CV itself. */
export const CV_WORDS = {
    it: {present: 'presente', born: 'Data di nascita', profile: 'Profilo', contacts: 'Contatti', native: 'Madrelingua'},
    en: {present: 'present', born: 'Date of birth', profile: 'Profile', contacts: 'Contact', native: 'Native'},
    fr: {present: 'aujourd’hui', born: 'Date de naissance', profile: 'Profil', contacts: 'Contact', native: 'Langue maternelle'},
    de: {present: 'heute', born: 'Geburtsdatum', profile: 'Profil', contacts: 'Kontakt', native: 'Muttersprache'},
    es: {present: 'actualidad', born: 'Fecha de nacimiento', profile: 'Perfil', contacts: 'Contacto', native: 'Lengua materna'},
    pt: {present: 'presente', born: 'Data de nascimento', profile: 'Perfil', contacts: 'Contacto', native: 'Língua materna'},
    nl: {present: 'heden', born: 'Geboortedatum', profile: 'Profiel', contacts: 'Contact', native: 'Moedertaal'},
    pl: {present: 'obecnie', born: 'Data urodzenia', profile: 'Profil', contacts: 'Kontakt', native: 'Język ojczysty'},
    ro: {present: 'prezent', born: 'Data nașterii', profile: 'Profil', contacts: 'Contact', native: 'Limba maternă'},
    sv: {present: 'nu', born: 'Födelsedatum', profile: 'Profil', contacts: 'Kontakt', native: 'Modersmål'},
};

/**
 * The data-processing consent usually printed at the bottom of a CV. It is customary in Italy (D.Lgs. 196/2003 and
 * GDPR) and in Poland (the "klauzula RODO"); elsewhere it is not expected, so the other languages get a neutral GDPR line.
 */
export const CONSENT = {
    it: 'Autorizzo il trattamento dei miei dati personali presenti nel curriculum vitae ai sensi del D.Lgs. 196/2003 e del Regolamento UE 2016/679 (GDPR).',
    en: 'I authorise the processing of the personal data contained in my CV pursuant to Regulation (EU) 2016/679 (GDPR).',
    fr: 'J’autorise le traitement des données personnelles contenues dans mon CV conformément au Règlement (UE) 2016/679 (RGPD).',
    de: 'Ich willige in die Verarbeitung der in meinem Lebenslauf enthaltenen personenbezogenen Daten gemäß der Verordnung (EU) 2016/679 (DSGVO) ein.',
    es: 'Autorizo el tratamiento de los datos personales contenidos en mi CV conforme al Reglamento (UE) 2016/679 (RGPD).',
    pt: 'Autorizo o tratamento dos dados pessoais constantes do meu CV nos termos do Regulamento (UE) 2016/679 (RGPD).',
    nl: 'Ik geef toestemming voor de verwerking van de persoonsgegevens in mijn cv overeenkomstig Verordening (EU) 2016/679 (AVG).',
    pl: 'Wyrażam zgodę na przetwarzanie moich danych osobowych dla potrzeb niezbędnych do realizacji procesu rekrutacji zgodnie z Rozporządzeniem Parlamentu Europejskiego i Rady (UE) 2016/679 z dnia 27 kwietnia 2016 r. (RODO).',
    ro: 'Sunt de acord cu prelucrarea datelor mele personale cuprinse în CV, în conformitate cu Regulamentul (UE) 2016/679 (GDPR).',
    sv: 'Jag samtycker till att mina personuppgifter i detta CV behandlas i enlighet med förordning (EU) 2016/679 (GDPR).',
};

/** Suggestions for language levels. */
export const LANG_LEVELS = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
