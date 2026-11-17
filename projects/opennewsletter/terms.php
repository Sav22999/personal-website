<?php
/**
 * OpenNewsletter — terms of use and privacy notice of the platform (accepted at sign-up and on every subscription).
 * English and Italian; ?lang=it|en. Each group may add its own terms (shown on its subscription page).
 */

declare(strict_types=1);

require __DIR__ . '/lib/public-page.php';

public_headers(false);
$lang = ($_GET['lang'] ?? '') === 'it' || (!isset($_GET['lang']) && lang_code((string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '')) === 'it') ? 'it' : 'en';

$T = [
    'en' => [
        'title' => 'OpenNewsletter — Terms of use and privacy',
        'updated' => 'Last updated: 16 November 2026',
        'sections' => [
            ['What OpenNewsletter is', 'OpenNewsletter is a free service by Saverio Morelli for writing and sending newsletters. People create groups; each group collects its own subscribers and sends its newsletters from an email address configured by its administrators. Each group is responsible for what it sends and for its own subscribers (it is the data controller for them); OpenNewsletter only provides the tool.'],
            ['Accounts', 'An account is made with an email address and a password (the address must be confirmed through a link) or with Google. The address of an account is unique and cannot be changed. You can delete your account at any time from your profile, once you are not the owner of any group.'],
            ['Rules for groups', 'Groups may only write to people who subscribed through their OpenNewsletter page or widget, and confirmed it through the link they received. Spam, deceptive content, malware, illegal material and messages that infringe the rights of others are forbidden. The master administrators of OpenNewsletter may suspend a group that breaks these rules.'],
            ['Subscribers', 'When you subscribe to a newsletter we record your email address, the language you chose, the language and the name of your browser (not the full browser string), the date and the version of the terms you accepted. Your address is used only to send you that group’s newsletter and the messages needed to manage the subscription. It is never sold or shared with other groups.'],
            ['Unsubscribing', 'Every newsletter contains a link to unsubscribe in one click. You can also unsubscribe from the group’s unsubscription page by typing your address (you receive a link to confirm). You may tell us why you leave: it is optional and only the group sees it. Subscriptions that are never confirmed are deleted after a few days.'],
            ['Retention and security', 'Passwords are stored only as salted hashes. The SMTP settings of each group are encrypted; not even the master administrators can read them. The IP addresses of sign-in and subscription attempts are kept for at most 48 hours, only to stop abuse. Data stays as long as the account, the group or the subscription exists.'],
            ['Who sees what', 'The members of a group see its subscribers according to their role (publishers and administrators). The master administrators of OpenNewsletter can see every group, its members, newsletters and subscribers, to keep the service safe, but not the group’s SMTP settings.'],
            ['Your rights', 'Under the GDPR you can access, correct or erase your data and object to its use. For a subscription, write to the group (its address is in every newsletter it sends) or unsubscribe. For your account or the platform, use the contact page of saveriomorelli.com.'],
            ['No warranty', 'The service is provided as it is, free of charge, without guarantees of availability. These terms may change: the new version applies from its publication, and the date at the top shows the latest one.'],
        ],
        'contact' => 'Contact',
    ],
    'it' => [
        'title' => 'OpenNewsletter — Condizioni d’uso e privacy',
        'updated' => 'Ultimo aggiornamento: 16 novembre 2026',
        'sections' => [
            ['Cos’è OpenNewsletter', 'OpenNewsletter è un servizio gratuito di Saverio Morelli per scrivere e inviare newsletter. Le persone creano gruppi; ogni gruppo raccoglie i propri iscritti e invia le proprie newsletter da un indirizzo email configurato dai suoi amministratori. Ogni gruppo è responsabile di ciò che invia e dei propri iscritti (ne è il titolare del trattamento); OpenNewsletter fornisce soltanto lo strumento.'],
            ['Account', 'Un account si crea con indirizzo email e password (l’indirizzo va confermato tramite un link) oppure con Google. L’indirizzo di un account è unico e non può essere modificato. Puoi eliminare il tuo account in qualsiasi momento dal profilo, quando non sei proprietario di alcun gruppo.'],
            ['Regole per i gruppi', 'I gruppi possono scrivere solo a chi si è iscritto dalla loro pagina o dal loro widget OpenNewsletter e lo ha confermato con il link ricevuto. Sono vietati spam, contenuti ingannevoli, malware, materiale illegale e messaggi che violano i diritti altrui. Gli amministratori master di OpenNewsletter possono sospendere un gruppo che viola queste regole.'],
            ['Iscritti', 'Quando ti iscrivi a una newsletter registriamo il tuo indirizzo email, la lingua scelta, la lingua e il nome del tuo browser (non l’intera stringa del browser), la data e la versione delle condizioni accettate. Il tuo indirizzo serve solo a inviarti la newsletter di quel gruppo e i messaggi necessari a gestire l’iscrizione. Non viene mai venduto né condiviso con altri gruppi.'],
            ['Disiscrizione', 'Ogni newsletter contiene un link per disiscriversi con un clic. Puoi anche disiscriverti dalla pagina di disiscrizione del gruppo inserendo il tuo indirizzo (riceverai un link di conferma). Puoi dirci perché te ne vai: è facoltativo e lo vede solo il gruppo. Le iscrizioni mai confermate vengono eliminate dopo pochi giorni.'],
            ['Conservazione e sicurezza', 'Le password sono conservate solo come hash con sale. Le impostazioni SMTP di ogni gruppo sono cifrate; nemmeno gli amministratori master possono leggerle. Gli indirizzi IP dei tentativi di accesso e di iscrizione sono conservati al massimo 48 ore, solo per fermare gli abusi. I dati restano finché esistono l’account, il gruppo o l’iscrizione.'],
            ['Chi vede cosa', 'I membri di un gruppo vedono i suoi iscritti in base al ruolo (publisher e amministratori). Gli amministratori master di OpenNewsletter possono vedere ogni gruppo, i suoi membri, le newsletter e gli iscritti, per mantenere sicuro il servizio, ma non le impostazioni SMTP del gruppo.'],
            ['I tuoi diritti', 'In base al GDPR puoi accedere ai tuoi dati, correggerli, cancellarli e opporti al loro uso. Per un’iscrizione scrivi al gruppo (il suo indirizzo è in ogni newsletter che invia) oppure disiscriviti. Per il tuo account o per la piattaforma usa la pagina contatti di saveriomorelli.com.'],
            ['Nessuna garanzia', 'Il servizio è fornito così com’è, gratuitamente, senza garanzie di disponibilità. Queste condizioni possono cambiare: la nuova versione vale dalla pubblicazione e la data in alto indica l’ultima.'],
        ],
        'contact' => 'Contatti',
    ],
][$lang];

echo '<!doctype html><html lang="' . $lang . '" data-theme="auto"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
    . '<title>' . e($T['title']) . '</title><link rel="icon" href="icons/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="assets/public.css?v=1"></head><body><main class="doc">'
    . '<nav class="lang-switch"><a href="?lang=en"' . ($lang === 'en' ? ' class="on"' : '') . '>English</a><a href="?lang=it"' . ($lang === 'it' ? ' class="on"' : '') . '>Italiano</a></nav>'
    . '<h1>' . e($T['title']) . '</h1><p style="color:var(--soft)">' . e($T['updated']) . '</p>';
foreach ($T['sections'] as [$h, $p]) echo '<h2>' . e($h) . '</h2><p>' . e($p) . '</p>';
echo '<p><a href="https://www.saveriomorelli.com/contact-me/">' . e($T['contact']) . '</a></p>'
    . '<p><a class="pub-powered" href="./"><img src="icons/favicon.svg" width="16" height="16" alt=""> <strong>OpenNewsletter</strong></a></p></main></body></html>';
