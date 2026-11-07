<?php
/**
 * Server-side translations. Source strings are English; the other languages map English -> translation.
 * The language comes from the X-Lang header sent by the app (falls back to Accept-Language, then English).
 */

declare(strict_types=1);

const LANGS = ['it', 'en'];

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
        'This username is already taken.' => 'Questo username è già preso.',
        'Use 3 to 20 letters, numbers, dots or underscores.' => 'Usa da 3 a 20 lettere, numeri, punti o trattini bassi.',
        'User not found.' => 'Utente non trovato.',
        'You cannot add yourself.' => 'Non puoi aggiungere te stesso.',
        'You already passed this exam with full marks.' => 'Hai già superato questo esame con il massimo.',
        'Pass the exam first.' => 'Prima supera l’esame.',
        'You already have this certificate.' => 'Hai già questo certificato.',
        'Unknown kind of exercise.' => 'Tipo di esercizio sconosciuto.',
        'Write the instructions.' => 'Scrivi le istruzioni.',
        'Write the explanation.' => 'Scrivi la spiegazione.',
        'An account with this email already exists: sign in.' => 'Esiste già un account con questa email: accedi.',
        'Certificate not found.' => 'Certificato non trovato.',
        'Complete all the lessons of the course first.' => 'Prima completa tutte le lezioni del corso.',
        'Complete the previous lessons first.' => 'Prima completa le lezioni precedenti.',
        'Course not found.' => 'Corso non trovato.',
        'Create your Fucina account' => 'Crea il tuo account Fucina',
        'Enter your name.' => 'Inserisci il tuo nome.',
        'Exam not found: start it again.' => 'Esame non trovato: ricomincialo.',
        'Fucina is not installed yet: open install-db.php.' => 'Fucina non è ancora installata: apri install-db.php.',
        'Game not found.' => 'Gioco non trovato.',
        'Google sign-in expired: please try again.' => 'L’accesso con Google è scaduto: riprova.',
        'Google sign-in failed: please try again.' => 'Accesso con Google non riuscito: riprova.',
        'Google sign-in is not configured.' => 'L’accesso con Google non è configurato.',
        'Internal server error.' => 'Errore interno del server.',
        'Invalid Google token.' => 'Token Google non valido.',
        'Invalid email address.' => 'Indirizzo email non valido.',
        'Invalid request.' => 'Richiesta non valida.',
        'Invalid security token: reload the page.' => 'Token di sicurezza non valido: ricarica la pagina.',
        'Lesson not found.' => 'Lezione non trovata.',
        'Method not allowed.' => 'Metodo non consentito.',
        'Session expired: sign in again.' => 'Sessione scaduta: accedi di nuovo.',
        'The current password is not correct.' => 'La password attuale non è corretta.',
        'The email of your Google account is not verified.' => 'L’email del tuo account Google non è verificata.',
        'The password is not correct.' => 'La password non è corretta.',
        'The password must have at least 8 characters.' => 'La password deve avere almeno 8 caratteri.',
        'The password is too long.' => 'La password è troppo lunga.',
        'This Google token is not meant for Fucina.' => 'Questo token Google non è destinato a Fucina.',
        'This account uses Google: sign in with “Continue with Google”.' => 'Questo account usa Google: accedi con «Continua con Google».',
        'This email is already used by another account.' => 'Questa email è già usata da un altro account.',
        'To create the account you must accept the privacy policy.' => 'Per creare l’account devi accettare l’informativa sulla privacy.',
        'Today’s challenge is already done: come back tomorrow.' => 'La sfida di oggi è già fatta: torna domani.',
        'Too many attempts: try again in a few minutes.' => 'Troppi tentativi: riprova tra qualche minuto.',
        'Too many sign-ups from this network: try again later.' => 'Troppe registrazioni da questa rete: riprova più tardi.',
        'Unsupported Content-Type.' => 'Content-Type non supportato.',
        'Wrong email or password.' => 'Email o password errate.',
        'Your account has been disabled.' => 'Il tuo account è stato disattivato.',
        '{field} is required.' => '{field} è obbligatorio.',
        'Not enough Sparks: earn more with lessons and games.' => 'Non hai abbastanza Scintille: guadagnane altre con lezioni e giochi.',
        'Win the previous level first.' => 'Prima vinci il livello precedente.',
        'You don’t have permission for this action.' => 'Non hai il permesso per questa azione.',
        'Not found.' => 'Non trovato.',
        'This id is already used.' => 'Questo id è già usato.',
        'Fill in at least one language.' => 'Compila almeno una lingua.',
        'Write at least two options.' => 'Scrivi almeno due risposte.',
        'The correct answer is not valid.' => 'La risposta corretta non è valida.',
        'Only the master admin can change roles.' => 'Solo il master admin può cambiare i ruoli.',
        'You cannot change your own account here.' => 'Da qui non puoi modificare il tuo account.',
        'Name' => 'Nome',
        'Email' => 'Email',
    ];
}
