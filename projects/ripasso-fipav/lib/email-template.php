<?php
/**
 * Fischietto email template: header with icon and project name, content, footer with the credit.
 * Built with tables and inline styles so it renders the same in Gmail, Outlook, Apple Mail and on phones.
 * The texts are user-facing and stay in Italian.
 */

function email_esc(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/** Primary button (table-based, so Outlook keeps padding and colors). */
function email_button(string $label, string $url): string
{
    return '<table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto;"><tr>'
        . '<td align="center" bgcolor="#2F6FD0" style="border-radius:10px;">'
        . '<a href="' . email_esc($url) . '" target="_blank" style="display:inline-block;padding:14px 30px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;">'
        . email_esc($label) . '</a></td></tr></table>';
}

/**
 * Wraps the content in the common layout.
 * $o: preheader (preview line in the inbox), icon_url, home_url, credit_url.
 */
function email_layout(string $content, array $o): string
{
    $font = "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif";
    $brand = "'Stack Sans Notch'," . $font;
    return '<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title>Fischietto</title>
<link href="https://fonts.googleapis.com/css2?family=Stack+Sans+Notch&display=swap" rel="stylesheet">
<style>
  @media (max-width: 600px) {
    .px { padding-left: 22px !important; padding-right: 22px !important; }
    .feature { display: block !important; width: auto !important; margin-bottom: 12px; }
    .gap, .row-gap { display: none !important; }
    .h1 { font-size: 23px !important; }
  }
</style>
</head>
<body style="margin:0;padding:0;background-color:#F3F5F9;font-family:' . $font . ';color:#14264A;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;">' . email_esc($o['preheader'] ?? '') . '</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#F3F5F9;">
<tr><td align="center" style="padding:32px 12px;">
  <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;">
    <!-- Header -->
    <tr><td class="px" style="background-color:#14264A;border-radius:16px 16px 0 0;padding:22px 32px;">
      <a href="' . email_esc($o['home_url']) . '" target="_blank" style="text-decoration:none;">
      <table role="presentation" cellspacing="0" cellpadding="0"><tr>
        <td style="vertical-align:middle;padding-right:12px;"><img src="' . email_esc($o['icon_url']) . '" width="40" height="40" alt="" style="display:block;border:0;border-radius:10px;"></td>
        <td style="vertical-align:middle;">
          <div style="font-size:21px;font-weight:800;color:#ffffff;letter-spacing:-0.01em;line-height:1.1;">Fischietto</div>
          <div style="font-size:12px;color:#AFC2E4;line-height:1.4;">Ripasso del regolamento FIPAV</div>
        </td>
      </tr></table>
      </a>
    </td></tr>
    <tr><td style="height:4px;line-height:4px;font-size:0;background-color:#F6BB42;">&nbsp;</td></tr>
    <!-- Body -->
    <tr><td class="px" style="background-color:#ffffff;padding:36px 40px 32px;">' . $content . '</td></tr>
    <!-- Footer -->
    <tr><td class="px" style="background-color:#F7F9FC;border-top:1px solid #E3E8F0;border-radius:0 0 16px 16px;padding:22px 40px;text-align:center;">
      <p style="margin:0 0 10px;font-size:14px;color:#7A879E;">Sviluppato con <span style="color:#F2A27A;font-size:15px;">&#9829;</span> da <a href="' . email_esc($o['credit_url']) . '" target="_blank" style="font-family:' . $brand . ';color:#14264A;text-decoration:none;font-size:15px;">Saverio Morelli</a></p>
      <p style="margin:0;font-size:12px;line-height:1.6;color:#9AA5B8;">' . ($o['footer_note'] ?? '') . '</p>
    </td></tr>
  </table>
</td></tr>
</table>
</body>
</html>';
}

/**
 * Builds an email. $kind: 'approved' (account activated), 'rejected' (registration not approved)
 * 'password_reset' (temporary password set by an administrator: $d['temp_password'])
 * or 'role_changed' (new permission level: $d['old_level'], $d['new_level']).
 * $d: name, email, portal_url, icon_url, assets_url (folder with the email icons), contact_url, credit_url.
 * The icons are drawn as SVG in assets/email/ and sent as PNG: Gmail and Outlook do not display SVG images.
 * Returns ['subject' => ..., 'html' => ..., 'text' => ...].
 */
function render_account_email(string $kind, array $d): array
{
    $first = email_esc(trim(explode(' ', trim($d['name']))[0]) ?: $d['name']);
    $portal = $d['portal_url'];
    $img = function (string $name, int $size, string $style = '') use ($d) {
        return '<img src="' . email_esc(rtrim($d['assets_url'], '/') . '/' . $name . '.png') . '" width="' . $size . '" height="' . $size . '" alt="" style="display:block;border:0;' . $style . '">';
    };
    $muted = 'margin:0 0 18px;font-size:15px;line-height:1.65;color:#4B5B78;';
    $o = [
        'icon_url'   => $d['icon_url'],
        'home_url'   => $portal,
        'credit_url' => $d['credit_url'],
    ];

    if ($kind === 'approved') {
        $subject = 'Il tuo account Fischietto è attivo';
        $o['preheader'] = 'Puoi accedere da subito: quiz, ripasso del regolamento, documenti ufficiali e statistiche.';
        $o['footer_note'] = 'Ricevi questa email perché ti sei registrato a Fischietto. È un messaggio automatico: non rispondere.';

        $step = function (string $n, string $title, string $text) {
            return '<tr><td style="vertical-align:top;padding:0 14px 16px 0;width:32px;">'
                . '<div style="width:30px;height:30px;line-height:30px;border-radius:15px;background-color:#E6EEFB;color:#2459AD;font-weight:800;font-size:14px;text-align:center;">' . $n . '</div></td>'
                . '<td style="vertical-align:top;padding:4px 0 16px;font-size:15px;line-height:1.55;color:#14264A;"><strong>' . $title . '</strong><br><span style="color:#4B5B78;">' . $text . '</span></td></tr>';
        };
        // Each card is a table cell, so the two cards of a row always have the same height
        $feature = function (string $icon, string $title, string $text) use ($img) {
            return '<td class="feature" width="48%" style="vertical-align:top;background-color:#F7F9FC;border:1px solid #E3E8F0;border-radius:12px;padding:16px;">'
                . '<div style="margin-bottom:10px;">' . $img($icon, 40) . '</div>'
                . '<div style="font-size:15px;font-weight:700;color:#14264A;margin-bottom:4px;">' . $title . '</div>'
                . '<div style="font-size:13px;line-height:1.5;color:#4B5B78;">' . $text . '</div></td>';
        };
        $gap = '<td class="gap" width="4%" style="width:4%;min-width:12px;font-size:0;line-height:0;">&nbsp;</td>';
        $rowGap = '<tr class="row-gap"><td colspan="3" style="height:12px;font-size:0;line-height:0;">&nbsp;</td></tr>';

        $content =
            '<table role="presentation" cellspacing="0" cellpadding="0" style="margin-bottom:18px;"><tr><td style="background-color:#E3F4EC;border-radius:999px;padding:6px 12px 6px 7px;">'
          .   '<table role="presentation" cellspacing="0" cellpadding="0"><tr><td style="padding-right:7px;">' . $img('check', 18) . '</td>'
          .   '<td style="color:#1F8A5B;font-size:13px;font-weight:700;">Account attivo</td></tr></table></td></tr></table>'
          . '<h1 class="h1" style="margin:0 0 12px;font-size:26px;line-height:1.25;font-weight:800;color:#14264A;">Benvenuto in Fischietto, ' . $first . '!</h1>'
          . '<p style="' . $muted . '">La tua registrazione è stata approvata: da adesso puoi allenarti sul regolamento FIPAV quando vuoi, dal computer o dal telefono.</p>'
          . '<div style="padding:8px 0 30px;">' . email_button('Accedi a Fischietto', $portal) . '</div>'
          . '<div style="background-color:#FDF8EC;border:1px solid #F6E2B3;border-radius:14px;padding:22px 22px 6px;margin-bottom:28px;">'
          .   '<div style="font-size:13px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#B57A0C;margin-bottom:14px;">Come accedere</div>'
          .   '<table role="presentation" width="100%" cellspacing="0" cellpadding="0">'
          .     $step('1', 'Apri il portale', '<a href="' . email_esc($portal) . '" style="color:#2459AD;text-decoration:underline;">' . email_esc(preg_replace('#^https?://#', '', rtrim($portal, '/'))) . '</a>')
          .     $step('2', 'Inserisci la tua email', '<span style="color:#14264A;">' . email_esc($d['email']) . '</span>')
          .     $step('3', 'Usa la password che hai scelto', 'quella indicata al momento della registrazione.')
          .   '</table>'
          . '</div>'
          . '<div style="font-size:18px;font-weight:800;color:#14264A;margin-bottom:8px;">Cosa trovi dentro</div>'
          . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 22px;">'
          .   '<tr>' . $feature('quiz', 'Quiz su misura', 'Scegli fonti, numero di domande e difficoltà. Risposte in ordine casuale.') . $gap
          .            $feature('ripasso', 'Ripasso', 'Tutti i riferimenti normativi, con le novità della stagione in evidenza.') . '</tr>' . $rowGap
          .   '<tr>' . $feature('documenti', 'Documenti ufficiali', 'Regole di gioco, Casistica, Normative e Vademecum da aprire o scaricare.') . $gap
          .            $feature('statistiche', 'Statistiche', 'Vedi dove sbagli di più e riparti dai riferimenti più deboli.') . '</tr>'
          . '</table>'
          . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:24px;"><tr>'
          .   '<td style="border-left:4px solid #2F6FD0;background-color:#F3F7FE;border-radius:0 10px 10px 0;padding:14px 16px;">'
          .     '<table role="presentation" cellspacing="0" cellpadding="0"><tr><td style="vertical-align:top;padding-right:14px;">' . $img('telefono', 40) . '</td>'
          .     '<td style="vertical-align:middle;font-size:14px;line-height:1.55;color:#14264A;"><strong>Mettilo sul telefono.</strong> Dopo l’accesso, apri il menu dell’account e scegli «Installa l’app»: Fischietto si apre come un’app, a portata di pollice.</td></tr></table>'
          .   '</td></tr></table>'
          . '<p style="margin:0;font-size:14px;line-height:1.6;color:#7A879E;">Hai dimenticato la password o qualcosa non funziona? <a href="' . email_esc($d['contact_url']) . '" style="color:#2459AD;">Scrivi all’amministratore</a>.</p>';

        $text = "Ciao {$d['name']},\n\nil tuo account Fischietto è attivo: puoi accedere da subito.\n\n"
              . "Come accedere\n1. Apri il portale: $portal\n2. Inserisci la tua email: {$d['email']}\n3. Usa la password che hai scelto in registrazione.\n\n"
              . "Cosa trovi dentro: quiz su misura, ripasso dei riferimenti normativi, documenti ufficiali, statistiche personali.\n\n"
              . "Password dimenticata? Scrivi all'amministratore: {$d['contact_url']}\n\n-- \nFischietto · Sviluppato da Saverio Morelli · {$d['credit_url']}\n";
    } elseif ($kind === 'role_changed') {
        $old = (int)$d['old_level'];
        $new = (int)$d['new_level'];
        $up = $new > $old;
        $roleName = function (int $l) { return [1 => 'Utente', 5 => 'Moderatore contenuti', 9 => 'Amministratore', 10 => 'Master admin'][$l] ?? 'Utente'; };
        // What each level can do (every level includes the ones below)
        $abilities = [
            1  => ['Quiz su misura, ripasso dei riferimenti, documenti ufficiali e statistiche personali'],
            5  => ['Creare, modificare e disattivare domande e riferimenti', 'Vedere e chiudere le segnalazioni degli utenti'],
            9  => ['Abilitare le nuove registrazioni e gestire utenti e moderatori', 'Gestire gli «Altri documenti utili» della sezione Documenti'],
            10 => ['Nominare e gestire gli amministratori', 'Passare ad altri il ruolo di master admin'],
        ];
        $list = [];
        foreach ($abilities as $lvl => $items) if ($lvl <= $new) foreach ($items as $it) $list[] = [$it, $lvl > 1 && $lvl > $old];
        $subject = 'Fischietto: il tuo ruolo ora è ' . $roleName($new);
        $o['preheader'] = 'Il tuo ruolo su Fischietto è passato da ' . $roleName($old) . ' a ' . $roleName($new) . '.';
        $o['footer_note'] = 'Ricevi questa email perché un amministratore ha cambiato il ruolo del tuo account Fischietto. È un messaggio automatico: non rispondere.';
        $pill = function (string $label, bool $current) {
            return '<span style="display:inline-block;padding:7px 14px;border-radius:999px;font-size:14px;font-weight:700;'
                . ($current ? 'background-color:#2F6FD0;color:#ffffff;' : 'background-color:#EBEFF5;color:#7A879E;text-decoration:line-through;') . '">' . email_esc($label) . '</span>';
        };
        $rows = '';
        foreach ($list as [$text, $isNew]) {
            $rows .= '<tr><td style="vertical-align:top;padding:0 10px 10px 0;width:20px;">' . $img('check', 18) . '</td>'
                . '<td style="vertical-align:top;padding:0 0 10px;font-size:15px;line-height:1.5;color:#14264A;">' . email_esc($text)
                . ($isNew ? ' <span style="display:inline-block;padding:1px 8px;border-radius:999px;background-color:#FDF3DC;color:#B57A0C;font-size:11px;font-weight:800;letter-spacing:.04em;vertical-align:1px;">NUOVO</span>' : '') . '</td></tr>';
        }
        $panel = $d['portal_url'] . 'admin.html';
        $content =
            '<table role="presentation" cellspacing="0" cellpadding="0" style="margin-bottom:18px;"><tr><td>' . $img('ruolo', 48) . '</td></tr></table>'
          . '<h1 class="h1" style="margin:0 0 12px;font-size:26px;line-height:1.25;font-weight:800;color:#14264A;">' . ($up ? 'Nuovo ruolo, ' . $first . '!' : 'Il tuo ruolo è cambiato, ' . $first) . '</h1>'
          . '<p style="' . $muted . '">' . ($up ? 'Un amministratore ti ha affidato un nuovo ruolo su Fischietto. Grazie per il tuo contributo!' : 'Un amministratore ha aggiornato il ruolo del tuo account su Fischietto. Puoi continuare a usare il portale come sempre.') . '</p>'
          . '<table role="presentation" cellspacing="0" cellpadding="0" style="margin:4px 0 28px;"><tr>'
          .   '<td style="padding-right:10px;">' . $pill($roleName($old), false) . '</td>'
          .   '<td style="padding-right:10px;font-size:20px;color:#7A879E;">&rarr;</td>'
          .   '<td>' . $pill($roleName($new), true) . '</td>'
          . '</tr></table>'
          . '<div style="background-color:#F7F9FC;border:1px solid #E3E8F0;border-radius:14px;padding:20px 22px 10px;margin-bottom:28px;">'
          .   '<div style="font-size:13px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#7A879E;margin-bottom:14px;">Cosa puoi fare ora</div>'
          .   '<table role="presentation" width="100%" cellspacing="0" cellpadding="0">' . $rows . '</table>'
          . '</div>'
          . ($new >= 5
              ? '<p style="' . $muted . '">Trovi gli strumenti di gestione nel menu del tuo account, alla voce «Pannello di gestione».</p><div style="padding:0 0 26px;">' . email_button('Apri la gestione', $panel) . '</div>'
              : '<div style="padding:0 0 26px;">' . email_button('Apri Fischietto', $portal) . '</div>')
          . '<p style="margin:0;font-size:14px;line-height:1.6;color:#7A879E;">Pensi che sia un errore? <a href="' . email_esc($d['contact_url']) . '" style="color:#2459AD;">Scrivi all’amministratore</a>.</p>';
        $text = "Ciao {$d['name']},

il tuo ruolo su Fischietto è passato da " . $roleName($old) . ' a ' . $roleName($new) . ".

Cosa puoi fare ora:
"
              . implode('', array_map(function ($x) { return '- ' . $x[0] . "
"; }, $list))
              . "
" . ($new >= 5 ? "Gestione: $panel
" : "Portale: $portal
")
              . "
Pensi che sia un errore? Scrivi all'amministratore: {$d['contact_url']}

-- 
Fischietto · Sviluppato da Saverio Morelli · {$d['credit_url']}
";
    } elseif ($kind === 'password_reset') {
        $subject = 'La tua password di Fischietto è stata reimpostata';
        $o['preheader'] = 'Ecco la password temporanea per accedere: al primo accesso ne sceglierai una nuova.';
        $o['footer_note'] = 'Ricevi questa email perché un amministratore ha reimpostato la password del tuo account Fischietto. È un messaggio automatico: non rispondere.';
        $pw = email_esc((string)$d['temp_password']);
        $step = function (string $n, string $title, string $text) {
            return '<tr><td style="vertical-align:top;padding:0 14px 14px 0;width:32px;">'
                . '<div style="width:30px;height:30px;line-height:30px;border-radius:15px;background-color:#E6EEFB;color:#2459AD;font-weight:800;font-size:14px;text-align:center;">' . $n . '</div></td>'
                . '<td style="vertical-align:top;padding:4px 0 14px;font-size:15px;line-height:1.55;color:#14264A;"><strong>' . $title . '</strong>' . ($text !== '' ? '<br><span style="color:#4B5B78;">' . $text . '</span>' : '') . '</td></tr>';
        };
        $content =
            '<h1 class="h1" style="margin:0 0 12px;font-size:24px;line-height:1.3;font-weight:800;color:#14264A;">Ciao ' . $first . ', ecco la tua nuova password</h1>'
          . '<p style="' . $muted . '">Un amministratore ha reimpostato la password del tuo account. Per entrare usa questa password temporanea:</p>'
          . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:4px 0 12px;"><tr>'
          .   '<td align="center" style="background-color:#F7F9FC;border:2px dashed #C9D4E5;border-radius:14px;padding:20px 16px;">'
          .     '<div style="font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#7A879E;margin-bottom:8px;">Password temporanea</div>'
          .     '<div style="font-family:\'SFMono-Regular\',Menlo,Consolas,\'Courier New\',monospace;font-size:24px;font-weight:700;letter-spacing:.06em;color:#14264A;word-break:break-all;">' . $pw . '</div>'
          .   '</td></tr></table>'
          . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:26px;"><tr>'
          .   '<td style="border-left:4px solid #F6BB42;background-color:#FDF8EC;border-radius:0 10px 10px 0;padding:12px 16px;font-size:14px;line-height:1.55;color:#14264A;">'
          .     '<strong>È temporanea:</strong> appena entri ti verrà chiesto di sceglierne una nuova, che solo tu conoscerai. Da quel momento questa password non funzionerà più.</td>'
          . '</tr></table>'
          . '<div style="padding:0 0 28px;">' . email_button('Accedi e scegli la nuova password', $portal) . '</div>'
          . '<div style="font-size:13px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#7A879E;margin-bottom:14px;">Come fare</div>'
          . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:12px;">'
          .   $step('1', 'Apri il portale', '<a href="' . email_esc($portal) . '" style="color:#2459AD;text-decoration:underline;">' . email_esc(preg_replace('#^https?://#', '', rtrim($portal, '/'))) . '</a>')
          .   $step('2', 'Accedi con la tua email e la password temporanea', email_esc($d['email']))
          .   $step('3', 'Scegli una nuova password', 'almeno 10 caratteri, con tre tipi tra minuscole, maiuscole, numeri e simboli.')
          . '</table>'
          . '<p style="margin:0;font-size:14px;line-height:1.6;color:#7A879E;">Non hai chiesto tu il reset? <a href="' . email_esc($d['contact_url']) . '" style="color:#2459AD;">Avvisa l’amministratore</a>.</p>';
        $text = "Ciao {$d['name']},

un amministratore ha reimpostato la password del tuo account Fischietto.

"
              . "Password temporanea: {$d['temp_password']}

"
              . "È temporanea: appena entri ti verrà chiesto di sceglierne una nuova. Da quel momento questa password non funzionerà più.

"
              . "1. Apri il portale: $portal
2. Accedi con la tua email ({$d['email']}) e la password temporanea
3. Scegli una nuova password

"
              . "Non hai chiesto tu il reset? Avvisa l'amministratore: {$d['contact_url']}

-- 
Fischietto · Sviluppato da Saverio Morelli · {$d['credit_url']}
";
    } else {
        $subject = 'La tua richiesta di accesso a Fischietto';
        $o['preheader'] = 'La tua registrazione non è stata approvata.';
        $o['footer_note'] = 'Ricevi questa email perché è stata richiesta una registrazione a Fischietto con questo indirizzo. È un messaggio automatico: non rispondere.';
        $content =
            '<h1 class="h1" style="margin:0 0 12px;font-size:24px;line-height:1.3;font-weight:800;color:#14264A;">Ciao ' . $first . ',</h1>'
          . '<p style="' . $muted . '">grazie per l’interesse verso Fischietto. Purtroppo la tua richiesta di accesso <strong style="color:#14264A;">non è stata approvata</strong>.</p>'
          . '<p style="' . $muted . '">Il portale è riservato agli arbitri e agli ufficiali di gara FIPAV. I dati che hai inserito sono stati eliminati.</p>'
          . '<div style="background-color:#F7F9FC;border:1px solid #E3E8F0;border-radius:14px;padding:20px 22px;margin:8px 0 26px;font-size:15px;line-height:1.6;color:#14264A;">'
          .   '<strong>Pensi che sia un errore?</strong><br><span style="color:#4B5B78;">Scrivi all’amministratore indicando nome, cognome e comitato di appartenenza: verificherà la tua richiesta.</span></div>'
          . '<div style="padding:0 0 6px;">' . email_button('Contatta l’amministratore', $d['contact_url']) . '</div>';
        $text = "Ciao {$d['name']},\n\ngrazie per l'interesse verso Fischietto. Purtroppo la tua richiesta di accesso non è stata approvata e i dati inseriti sono stati eliminati.\n\n"
              . "Pensi che sia un errore? Scrivi all'amministratore: {$d['contact_url']}\n\n-- \nFischietto · Sviluppato da Saverio Morelli · {$d['credit_url']}\n";
    }

    return ['subject' => $subject, 'html' => email_layout($content, $o), 'text' => $text];
}

/** Notice to the administrator: a new registration is waiting for approval. */
function render_registration_notice(string $name, string $email, array $links): array
{
    $admin = $links['portal_url'] . 'admin.html#utenti';
    $content =
        '<h1 class="h1" style="margin:0 0 12px;font-size:24px;line-height:1.3;font-weight:800;color:#14264A;">Nuova registrazione da abilitare</h1>'
      . '<p style="margin:0 0 20px;font-size:15px;line-height:1.65;color:#4B5B78;">Una nuova persona ha chiesto di accedere a Fischietto.</p>'
      . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#F7F9FC;border:1px solid #E3E8F0;border-radius:14px;margin-bottom:26px;">'
      .   '<tr><td style="padding:16px 20px 6px;font-size:13px;color:#7A879E;">Nome</td></tr>'
      .   '<tr><td style="padding:0 20px 12px;font-size:16px;font-weight:700;color:#14264A;">' . email_esc($name) . '</td></tr>'
      .   '<tr><td style="padding:0 20px 6px;font-size:13px;color:#7A879E;">Email</td></tr>'
      .   '<tr><td style="padding:0 20px 16px;font-size:16px;color:#14264A;">' . email_esc($email) . '</td></tr>'
      . '</table>'
      . '<div style="padding:0 0 6px;">' . email_button('Abilita o rifiuta', $admin) . '</div>';
    $o = [
        'preheader'   => $name . ' attende di essere abilitato.',
        'footer_note' => 'Ricevi questa email perché sei indicato come amministratore di Fischietto.',
        'icon_url'    => $links['icon_url'],
        'home_url'    => $links['portal_url'],
        'credit_url'  => $links['credit_url'],
    ];
    return [
        'subject' => 'Fischietto: nuova registrazione da abilitare',
        'html'    => email_layout($content, $o),
        'text'    => "Nuovo utente in attesa di abilitazione:\n\nNome: $name\nEmail: $email\n\nAbilita o rifiuta: $admin\n",
    ];
}
