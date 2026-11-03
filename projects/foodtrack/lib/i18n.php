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
        'Google sign-in failed: please try again.' => 'Accesso con Google non riuscito: riprova.',
        'Unknown action.' => 'Azione sconosciuta.',
        'Invalid barcode.' => 'Codice a barre non valido.',
        'Invalid invite code.' => 'Codice invito non valido.',
        'Unsupported Content-Type.' => 'Content-Type non supportato.',
        'Invalid date.' => 'Data non valida.',
        'Item not found (another household member may have deleted it).' => 'Elemento non trovato (forse è stato eliminato da un altro membro della casa).',
        'This email is already used by another account.' => 'Email già usata da un altro account.',
        'Invalid email address.' => 'Indirizzo email non valido.',
        'Wrong email or password.' => 'Email o password non corretti.',
        'Internal server error.' => 'Errore interno del server.',
        'An account with this email already exists.' => 'Esiste già un account con questa email.',
        'An account with this email already exists: sign in.' => 'Esiste già un account con questa email: accedi.',
        'A product with this name already exists.' => 'Esiste già un prodotto con questo nome.',
        'A category with this name already exists.' => 'Esiste già una categoria con questo nome.',
        'FoodTrack is not installed yet: open install-db.php.' => 'FoodTrack non è ancora installato: apri install-db.php.',
        'Unsupported format: use JPG, PNG or WebP.' => 'Formato non supportato: usa JPG, PNG o WebP.',
        'You reached the maximum number of lists (100).' => 'Hai raggiunto il numero massimo di liste (100).',
        'You reached the maximum number of products (3000).' => 'Hai raggiunto il numero massimo di prodotti (3000).',
        'You reached the maximum number of spaces (50).' => 'Hai raggiunto il numero massimo di spazi (50).',
        'The master admin cannot delete their own account: first hand the role over to another administrator.' => 'Il master admin non può eliminare il proprio account: prima trasferisci il ruolo a un altro amministratore.',
        'The new master admin must have an active account.' => 'Il nuovo master admin deve avere un account attivo.',
        'Your account has been disabled. Contact the administrator.' => 'Il tuo account è stato disabilitato. Contatta l’amministratore.',
        'Your account has been disabled.' => 'Il tuo account è stato disabilitato.',
        'Image too large.' => 'Immagine troppo grande.',
        'Upload failed.' => 'Caricamento non riuscito.',
        'Enter the product name.' => 'Inserisci il nome del prodotto.',
        'Enter your name.' => 'Inserisci il tuo nome.',
        'The uploads/recipes folder is not writable.' => 'La cartella uploads/recipes non è scrivibile.',
        'The current password is wrong.' => 'La password attuale non è corretta.',
        'The password must be at least 8 characters long.' => 'La password deve avere almeno 8 caratteri.',
        'The password is too long.' => 'La password è troppo lunga.',
        'Google sign-in is not configured.' => 'L’accesso con Google non è configurato.',
        'Google sign-in expired: please try again.' => 'L’accesso con Google è scaduto: riprova.',
        'The email of your Google account is not verified.' => 'L’email del tuo account Google non è verificata.',
        'The image cannot exceed 6 MB.' => 'L’immagine non può superare 6 MB.',
        'This user is not a member of this household.' => 'L’utente non fa parte di questa casa.',
        'Method not allowed.' => 'Metodo non consentito.',
        'No image received.' => 'Nessuna immagine ricevuta.',
        'You are not a member of this household.' => 'Non fai parte di questa casa.',
        'You don\'t have permission for this action.' => 'Non hai i permessi per questa operazione.',
        'You cannot delete this account.' => 'Non puoi eliminare questo account.',
        'You cannot edit this account.' => 'Non puoi modificare questo account.',
        'Wrong password.' => 'Password non corretta.',
        'Invalid meal.' => 'Pasto non valido.',
        'To hand over the role, first set a password for your account (Profile → Set a password).' => 'Per cedere il ruolo imposta prima una password per il tuo account (Profilo → Imposta una password).',
        'To create the account you must accept the privacy policy.' => 'Per creare l’account devi accettare l’informativa sulla privacy.',
        'To leave the household use “Leave this household”.' => 'Per uscire dalla casa usa «Esci da questa casa».',
        'Before continuing you must choose a new password.' => 'Prima di continuare devi scegliere una nuova password.',
        'Set a password first, otherwise you could no longer sign in.' => 'Prima imposta una password, altrimenti non potresti più accedere.',
        'Product not found (another household member may have deleted it).' => 'Prodotto non trovato (forse è stato eliminato da un altro membro della casa).',
        'Product not found.' => 'Prodotto non trovato.',
        'You can be a member of at most 10 households.' => 'Puoi far parte al massimo di 10 case.',
        'This household already has the maximum number of members (20).' => 'Questa casa ha già raggiunto il numero massimo di membri (20).',
        'This list already has too many products (500).' => 'Questa lista ha già troppi prodotti (500).',
        'This Google account is already linked to another FoodTrack account.' => 'Questo account Google è già collegato a un altro account FoodTrack.',
        'This account uses Google sign-in: tap “Continue with Google”.' => 'Questo account usa l’accesso con Google: tocca «Continua con Google».',
        'Recipe not found.' => 'Ricetta non trovata.',
        'Invalid request.' => 'Richiesta non valida.',
        'Could not save the image.' => 'Salvataggio dell’immagine non riuscito.',
        'Choose the day.' => 'Scegli il giorno.',
        'Choose a recipe or write what you want to eat.' => 'Scegli una ricetta o scrivi cosa vuoi mangiare.',
        'Type DELETE to confirm.' => 'Scrivi ELIMINA per confermare.',
        'You are already the master admin.' => 'Sei già il master admin.',
        'Session expired: sign in again.' => 'Sessione scaduta: accedi di nuovo.',
        'Only the master admin can create administrators.' => 'Solo il master admin può creare amministratori.',
        'Only the master admin can appoint or remove administrators.' => 'Solo il master admin può nominare o rimuovere gli amministratori.',
        'Only the owner can hand over the household.' => 'Solo il proprietario può cedere la casa.',
        'Only the owner can delete the household.' => 'Solo il proprietario può eliminare la casa.',
        'Only the owner can regenerate the invite code.' => 'Solo il proprietario può rigenerare il codice invito.',
        'Only the owner can remove members.' => 'Solo il proprietario può rimuovere i membri.',
        'Only the owner can rename the household.' => 'Solo il proprietario può rinominare la casa.',
        'This Google token is not meant for FoodTrack.' => 'Token Google non destinato a FoodTrack.',
        'Invalid Google token.' => 'Token Google non valido.',
        'Invalid security token: reload the page.' => 'Token di sicurezza non valido: ricarica la pagina.',
        'Too many sign-ups from this network: try again later.' => 'Troppe registrazioni da questa rete: riprova più tardi.',
        'Too many attempts: try again later.' => 'Troppi tentativi: riprova più tardi.',
        'Too many attempts: try again in a few minutes.' => 'Troppi tentativi: riprova tra qualche minuto.',
        'User not found.' => 'Utente non trovato.',
        'Create your FoodTrack account' => 'Crea il tuo account FoodTrack',
        'Description' => 'Descrizione',
        'Image' => 'Immagine',
        'Expiry information' => 'Informazioni sulla scadenza',
        'Brand' => 'Marca',
        'Name' => 'Nome',
        'Cooking notes' => 'Note di cottura',
        'Notes' => 'Note',
        'Title' => 'Titolo',
        'Code' => 'Codice',
        'The field “{label}” is required.' => 'Il campo «{label}» è obbligatorio.',
        'The field “{label}” is too long (max {max} characters).' => 'Il campo «{label}» è troppo lungo (max {max} caratteri).',
        '{name}\'s home' => 'Casa di {name}',
        'User' => 'Utente',
        'Administrator' => 'Amministratore',
        'Master admin' => 'Master admin',
        'Fridge' => 'Frigorifero',
        'Freezer' => 'Freezer',
        'Pantry' => 'Dispensa',
        'For fresh food' => 'Spazio per conservare alimenti freschi',
        'For frozen food' => 'Spazio per conservare alimenti surgelati',
        'For dry and long-life food' => 'Spazio per conservare alimenti secchi e a lunga conservazione',
    ];
}
