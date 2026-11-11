<?php
/**
 * Server-side translations. Source strings are English; the other languages map English -> translation.
 * The language comes from the X-Lang header sent by the app (falls back to Accept-Language, then English).
 */

declare(strict_types=1);

const LANGS = ['en', 'it'];

function lang(): string
{
    static $lang = null;
    if ($lang !== null) return $lang;
    $h = strtolower((string)($_SERVER['HTTP_X_LANG'] ?? ''));
    if (!in_array($h, LANGS, true)) {
        $al = strtolower((string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));
        $h = substr($al, 0, 2) === 'it' ? 'it' : 'en';
    }
    return $lang = $h;
}

/** Translates an English source string and fills {placeholders}. */
function tr(string $en, array $vars = []): string
{
    static $dict = null;
    if ($dict === null) $dict = ['it' => translations_it()];
    $s = $dict[lang()][$en] ?? $en;
    foreach ($vars as $k => $v) $s = str_replace('{' . $k . '}', (string)$v, $s);
    return $s;
}

function translations_it(): array
{
    return [
        'Create your Giftwise account' => 'Crea il tuo account Giftwise',
        'Enter your name.' => 'Inserisci il tuo nome.',
        'Giftwise is not installed yet: open install-db.php.' => 'Giftwise non è ancora installato: apri install-db.php.',
        'Google sign-in expired: please try again.' => 'L’accesso con Google è scaduto: riprova.',
        'Google sign-in failed: please try again.' => 'Accesso con Google non riuscito: riprova.',
        'Google sign-in is not configured.' => 'L’accesso con Google non è configurato.',
        'Internal server error.' => 'Errore interno del server.',
        'Invalid Google token.' => 'Token Google non valido.',
        'Invalid date.' => 'Data non valida.',
        'Invalid email address.' => 'Indirizzo email non valido.',
        'Invalid link: it must start with https://' => 'Link non valido: deve iniziare con https://',
        'Invalid price.' => 'Prezzo non valido.',
        'Invalid request.' => 'Richiesta non valida.',
        'Invalid security token: reload the page.' => 'Token di sicurezza non valido: ricarica la pagina.',
        'List not found.' => 'Lista non trovata.',
        'Message not found.' => 'Messaggio non trovato.',
        'Method not allowed.' => 'Metodo non consentito.',
        'Nobody uses Giftwise with this email yet: send them your invite link.' => 'Nessuno usa Giftwise con questa email: mandagli il tuo link d’invito.',
        'Paste a link first.' => 'Prima incolla un link.',
        'Request not found.' => 'Richiesta non trovata.',
        'Session expired: sign in again.' => 'Sessione scaduta: accedi di nuovo.',
        'That is your own email.' => 'È la tua email.',
        'The current password is not correct.' => 'La password attuale non è corretta.',
        'The email of your Google account is not verified.' => 'L’email del tuo account Google non è verificata.',
        'The link is too long.' => 'Il link è troppo lungo.',
        'The password is not correct.' => 'La password non è corretta.',
        'The password is too long.' => 'La password è troppo lunga.',
        'The password must be at least 8 characters long.' => 'La password deve avere almeno 8 caratteri.',
        'This Google token is not meant for Giftwise.' => 'Questo token Google non è destinato a Giftwise.',
        'This email is already used by another account.' => 'Questa email è già usata da un altro account.',
        'This invite link is not valid.' => 'Questo link d’invito non è valido.',
        'This is your own invite link: share it with your friends.' => 'È il tuo link d’invito: condividilo con i tuoi amici.',
        'This list is full (300 wishes).' => 'Questa lista è piena (300 desideri).',
        'This wish has been removed from the list.' => 'Questo desiderio è stato tolto dalla lista.',
        'To create the account you must accept the privacy policy.' => 'Per creare l’account devi accettare l’informativa sulla privacy.',
        'Too many attempts: try again in a few minutes.' => 'Troppi tentativi: riprova tra qualche minuto.',
        'Too many messages: wait a moment.' => 'Troppi messaggi: aspetta un momento.',
        'Too many requests: try again in a few minutes.' => 'Troppe richieste: riprova tra qualche minuto.',
        'Too many requests: try again later.' => 'Troppe richieste: riprova più tardi.',
        'Too many sign-ups from this network: try again later.' => 'Troppe registrazioni da questa rete: riprova più tardi.',
        'Unknown action.' => 'Azione sconosciuta.',
        'Unsupported Content-Type.' => 'Content-Type non supportato.',
        'Wish not found.' => 'Desiderio non trovato.',
        'Wrong email or password. If you signed up with Google, use “Continue with Google”.' => 'Email o password non corretti. Se ti sei registrato con Google, usa «Continua con Google».',
        'You are already friends.' => 'Siete già amici.',
        'You reached the maximum number of lists (100).' => 'Hai raggiunto il numero massimo di liste (100).',
        'Your account has been disabled.' => 'Il tuo account è stato disabilitato.',
        'Confirm your email first.' => 'Prima conferma la tua email.',
        'Too many emails: try again in an hour.' => 'Troppe email: riprova tra un’ora.',
        'This link has expired or was already used: sign in and ask for a new one.' => 'Questo link è scaduto o è già stato usato: accedi e chiedine uno nuovo.',
        '{field} is required.' => '{field}: campo obbligatorio.',
        '{field} is too long (max {max} characters).' => '{field}: testo troppo lungo (massimo {max} caratteri).',
        'The username can have 3–20 letters, digits, dots and underscores (no accents or spaces).' => 'Lo username può avere da 3 a 20 caratteri tra lettere, numeri, punti e trattini bassi (senza accenti né spazi).',
        'This username is not available.' => 'Questo username non è disponibile.',
        'This username is already taken.' => 'Questo username è già usato.',
        'User not found.' => 'Utente non trovato.',
        'You cannot follow yourself.' => 'Non puoi seguire te stesso.',
        'You already follow this person.' => 'Segui già questa persona.',
        'This is your list: what your friends decide stays a surprise.' => 'È la tua lista: quello che decidono i tuoi amici resta una sorpresa.',
        'Name' => 'Nome', 'Email' => 'Email', 'Title' => 'Titolo', 'Description' => 'Descrizione', 'Notes' => 'Note', 'Message' => 'Messaggio',
    ];
}
