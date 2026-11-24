<?php
/**
 * SavAccount — outgoing email (Italian and English). Messages leave after the JSON answer.
 * Settings: $mail_config in credentials.php. With $debug on and SAVACCOUNT_MAIL_DUMP set, messages are written to that
 * file instead (tests). Codes and links are never written to the error log.
 */

declare(strict_types=1);

require_once __DIR__ . '/smtp.php';

function mail_dump(): string
{
    return cfg('debug') ? (string)getenv('SAVACCOUNT_MAIL_DUMP') : '';
}

function mail_ready(): bool
{
    return mail_dump() !== '' || is_array(cfg('mail'));
}

function queue_mail(string $to, string $name, string $subject, string $html, string $text): void
{
    if (!isset($GLOBALS['sva_mail_queue'])) {
        $GLOBALS['sva_mail_queue'] = [];
        register_shutdown_function(function () {
            ignore_user_abort(true);
            @set_time_limit(60);
            $queue = $GLOBALS['sva_mail_queue'];
            if (mail_dump() !== '') {
                foreach ($queue as $m) file_put_contents(mail_dump(), json_encode($m) . "\n", FILE_APPEND | LOCK_EX);
                return;
            }
            foreach (smtp_send_all(cfg('mail'), $queue) as $i => $r) {
                if (!$r['ok']) error_log('SavAccount mail #' . $i . ' not sent: ' . $r['error']);
            }
        });
    }
    $GLOBALS['sva_mail_queue'][] = compact('to', 'name', 'subject', 'html', 'text');
}

/** Words of each message: [subject, title, paragraphs, button]. {x} are variables; they are escaped in the HTML part. */
function mail_words(string $kind, string $locale): array
{
    $valid = CODE_MINUTES;
    $it = [
        'register' => ['{code} è il tuo codice SavAccount', 'Conferma il tuo indirizzo', ['Ciao {name},', 'per completare la creazione del tuo SavAccount inserisci questo codice:', '{CODE}', "Oppure usa il pulsante qui sotto: ti verrà chiesta la password che hai scelto. Codice e link valgono $valid minuti.", 'Se non hai chiesto tu di creare un account, ignora questa email: senza conferma verrà eliminato.'], 'Conferma indirizzo'],
        'already' => ['Hai già un SavAccount', 'Hai già un SavAccount', ['Ciao {name},', 'qualcuno (forse tu) ha provato a creare un SavAccount con questo indirizzo, che è già associato al tuo. Puoi accedere, o reimpostare la password se non la ricordi.', 'Se non sei stato tu, non devi fare nulla.'], 'Accedi'],
        'email' => ['{code} è il codice per confermare il tuo indirizzo', 'Conferma il nuovo indirizzo', ['Ciao {name},', 'per aggiungere questo indirizzo al tuo SavAccount inserisci questo codice:', '{CODE}', "Oppure usa il pulsante dal browser in cui hai effettuato l’accesso. Codice e link valgono $valid minuti.", 'Se non sei stato tu, ignora questa email.'], 'Conferma indirizzo'],
        'email_taken' => ['Indirizzo già associato a un SavAccount', 'Indirizzo già in uso', ['Qualcuno (forse tu) ha provato ad aggiungere questo indirizzo a un altro SavAccount, ma è già associato a uno. Per spostarlo, rimuovilo prima dall’account attuale.', 'Se non sei stato tu, non devi fare nulla.'], ''],
        'reset' => ['{code} è il codice per la nuova password', 'Scegli una nuova password', ['Ciao {name},', 'è stata chiesta una nuova password per il tuo SavAccount. Inserisci questo codice:', '{CODE}', "Oppure usa il pulsante qui sotto. Codice e link valgono $valid minuti.", 'Se non sei stato tu, ignora questa email: la password non cambia.'], 'Scegli la password'],
        'mfa' => ['{code} è il tuo codice di verifica', 'Codice di verifica', ['Ciao {name},', 'usa questo codice per completare la verifica in due passaggi:', '{CODE}', "Vale $valid minuti. Non comunicarlo a nessuno: SavAccount non te lo chiederà mai.", 'Se non stai accedendo tu, qualcuno conosce la tua password: cambiala subito.'], ''],
        'new_login' => ['Nuovo accesso al tuo SavAccount', 'Nuovo accesso', ['Ciao {name},', 'è stato effettuato un accesso da un dispositivo che non riconosciamo: {device} ({ip}).', 'Se non sei stato tu, cambia la password e termina le altre sessioni.'], 'Controlla la sicurezza'],
        'password_changed' => ['La password del tuo SavAccount è cambiata', 'Password cambiata', ['Ciao {name},', 'la password del tuo SavAccount è stata appena cambiata e le altre sessioni sono state chiuse.', 'Se non sei stato tu, reimpostala subito.'], 'Reimposta la password'],
        'security' => ['Modifica alla sicurezza del tuo SavAccount', 'Modifica alla sicurezza', ['Ciao {name},', '{what}', 'Se non sei stato tu, cambia la password e controlla le impostazioni di sicurezza.'], 'Controlla la sicurezza'],
        'deleted' => ['Il tuo SavAccount è stato eliminato', 'Account eliminato', ['Ciao {name},', 'il tuo SavAccount e i suoi dati sono stati eliminati, come richiesto. I servizi che usavi potrebbero conservare dati propri: per quelli rivolgiti a loro.'], ''],
    ];
    $en = [
        'register' => ['{code} is your SavAccount code', 'Confirm your address', ['Hi {name},', 'to finish creating your SavAccount, enter this code:', '{CODE}', "Or use the button below: you will be asked for the password you chose. Code and link work for $valid minutes.", 'If you did not ask to create an account, ignore this email: without confirmation it will be deleted.'], 'Confirm address'],
        'already' => ['You already have a SavAccount', 'You already have a SavAccount', ['Hi {name},', 'someone (maybe you) tried to create a SavAccount with this address, which already belongs to yours. You can sign in, or reset your password if you forgot it.', 'If it was not you, there is nothing to do.'], 'Sign in'],
        'email' => ['{code} is the code to confirm your address', 'Confirm the new address', ['Hi {name},', 'to add this address to your SavAccount, enter this code:', '{CODE}', "Or use the button from the browser where you are signed in. Code and link work for $valid minutes.", 'If it was not you, ignore this email.'], 'Confirm address'],
        'email_taken' => ['Address already linked to a SavAccount', 'Address already in use', ['Someone (maybe you) tried to add this address to another SavAccount, but it already belongs to one. To move it, remove it from its current account first.', 'If it was not you, there is nothing to do.'], ''],
        'reset' => ['{code} is the code for your new password', 'Choose a new password', ['Hi {name},', 'a new password was requested for your SavAccount. Enter this code:', '{CODE}', "Or use the button below. Code and link work for $valid minutes.", 'If it was not you, ignore this email: your password does not change.'], 'Choose the password'],
        'mfa' => ['{code} is your verification code', 'Verification code', ['Hi {name},', 'use this code to complete two-step verification:', '{CODE}', "It works for $valid minutes. Do not share it: SavAccount will never ask you for it.", 'If you are not signing in, someone knows your password: change it now.'], ''],
        'new_login' => ['New sign-in to your SavAccount', 'New sign-in', ['Hi {name},', 'there was a sign-in from a device we do not recognise: {device} ({ip}).', 'If it was not you, change your password and end the other sessions.'], 'Check security'],
        'password_changed' => ['Your SavAccount password has changed', 'Password changed', ['Hi {name},', 'the password of your SavAccount has just been changed and the other sessions were closed.', 'If it was not you, reset it now.'], 'Reset password'],
        'security' => ['Security change on your SavAccount', 'Security change', ['Hi {name},', '{what}', 'If it was not you, change your password and check the security settings.'], 'Check security'],
        'deleted' => ['Your SavAccount has been deleted', 'Account deleted', ['Hi {name},', 'your SavAccount and its data have been deleted, as requested. The services you used may keep data of their own: ask them about it.'], ''],
    ];
    return ($locale === 'it' ? $it : $en)[$kind];
}

function security_words(string $what, string $locale): string
{
    $w = [
        'totp_on' => ['La verifica con app di autenticazione è stata attivata.', 'Verification with an authenticator app was turned on.'],
        'totp_off' => ['La verifica con app di autenticazione è stata disattivata.', 'Verification with an authenticator app was turned off.'],
        'email2fa_on' => ['La verifica in due passaggi via email è stata attivata.', 'Two-step verification by email was turned on.'],
        'email2fa_off' => ['La verifica in due passaggi via email è stata disattivata.', 'Two-step verification by email was turned off.'],
        'recovery_new' => ['Sono stati generati nuovi codici di recupero; i precedenti non valgono più.', 'New recovery codes were generated; the old ones no longer work.'],
        'recovery_used' => ['È stato usato un codice di recupero per accedere.', 'A recovery code was used to sign in.'],
        'email_added' => ['È stato aggiunto un nuovo indirizzo email.', 'A new email address was added.'],
        'email_removed' => ['È stato rimosso un indirizzo email.', 'An email address was removed.'],
        'email_primary' => ['È cambiato l’indirizzo email principale.', 'The primary email address was changed.'],
    ];
    return $w[$what][$locale === 'it' ? 0 : 1];
}

/** Sends one of the messages above. $url: where the button goes. */
function send_mail(string $kind, string $to, string $locale, array $vars = [], string $url = ''): void
{
    [$subject, $title, $paragraphs, $button] = mail_words($kind, $locale);
    $vars += ['name' => '', 'code' => ''];
    $fill = function (string $s) use ($vars): string {
        return preg_replace_callback('/\{([a-z]+)\}/', fn($m) => (string)($vars[$m[1]] ?? ''), $s);
    };
    $html = $text = '';
    foreach ($paragraphs as $p) {
        if ($p === '{CODE}') {
            $code = (string)$vars['code'];
            $html .= '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:4px 0 18px"><tr><td style="background:#e8f6ff;border-left:6px solid #66cbff;padding:14px 22px;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:30px;font-weight:700;letter-spacing:8px;color:#072536">' . e($code) . '</td></tr></table>';
            $text .= "    $code\n\n";
            continue;
        }
        $html .= '<p style="margin:0 0 14px">' . e($fill($p)) . '</p>';
        $text .= $fill($p) . "\n\n";
    }
    if ($button !== '' && $url !== '') {
        $html .= '<table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:6px"><tr><td style="background:#66cbff"><a href="' . e($url) . '" style="display:inline-block;padding:12px 24px;color:#072536;text-decoration:none;font-weight:700">' . e($button) . '</a></td></tr></table>';
        $text .= "$button:\n$url\n\n";
    }
    $foot = $locale === 'it' ? 'Email automatica di SavAccount (savaccount.eu). Non rispondere a questo messaggio.' : 'Automatic email from SavAccount (savaccount.eu). Please do not reply.';
    $page = '<!DOCTYPE html><html lang="' . $locale . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($title) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f6f7fb;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;color:#1a1f2e">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb"><tr><td align="center" style="padding:32px 16px">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px">'
        . '<tr><td style="padding:0 4px 18px;font-size:19px;letter-spacing:-.01em">Sav<b>Account</b></td></tr>'
        . '<tr><td style="background:#ffffff;padding:28px;border:1px solid #e2e5ee;font-size:15px;line-height:1.55"><h1 style="margin:0 0 16px;font-size:22px;line-height:1.25">' . e($title) . '</h1>' . $html . '</td></tr>'
        . '<tr><td style="padding:16px 4px 0;font-size:12px;color:#5f6880">' . e($foot) . '</td></tr></table></td></tr></table></body></html>';
    queue_mail($to, (string)$vars['name'], $fill($subject), $page, $text . "--\n$foot\n");
}
