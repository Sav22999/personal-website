<?php
/**
 * OpenNewsletter — texts seen by subscribers: subscription and unsubscription pages, their emails, the footer of every
 * newsletter. Six languages; any other language falls back to English. {group}, {email}, {terms} are filled in.
 */

declare(strict_types=1);

const PUB_LANGS = ['en', 'it', 'fr', 'de', 'es', 'pt'];

const PUB_TEXT = [
    'en' => [
        'sub_title' => 'Subscribe to {group}', 'sub_intro' => 'Get our news straight to your inbox.',
        'email' => 'Email address', 'lang' => 'Newsletter language', 'button' => 'Subscribe',
        'accept_platform' => 'I accept the {terms} of OpenNewsletter', 'terms_link' => 'terms of use',
        'accept_group' => 'I accept the terms of {group}', 'read' => 'Read them',
        'privacy' => 'Your address is used only to send you this newsletter. We also record your browser’s language and name.',
        'check_email' => 'Almost done! We sent you an email: open the link inside it to confirm your subscription.',
        'confirm_title' => 'Confirm your subscription', 'confirm_body' => 'Press the button to confirm that you want to receive the newsletter of {group}.',
        'confirm_button' => 'Confirm subscription', 'confirmed_title' => 'You’re subscribed!', 'confirmed_body' => 'Thank you: the next newsletter of {group} will reach you at {email}.',
        'link_invalid' => 'This link is not valid any more. Please subscribe again.', 'not_ready' => 'Subscriptions to this newsletter are not open yet.',
        'not_found' => 'This newsletter does not exist.', 'unsub_title' => 'Unsubscribe from {group}',
        'unsub_intro' => 'Enter your email address: we will send you a link to confirm.', 'unsub_token' => 'You are about to unsubscribe {email} from the newsletter of {group}.',
        'reason' => 'Why are you leaving? (optional)', 'reason_too_many' => 'Too many emails', 'reason_not_relevant' => 'The content is not relevant to me',
        'reason_never' => 'I never subscribed', 'reason_other' => 'Other', 'reason_note' => 'Tell us more (optional)',
        'unsub_button' => 'Unsubscribe', 'unsub_done_title' => 'You have been unsubscribed', 'unsub_done_body' => 'You will not receive any more newsletters from {group}.',
        'unsub_check' => 'If this address is subscribed, we have sent it a link to confirm.', 'resubscribe' => 'Subscribe again',
        'mail_confirm_subject' => 'Confirm your subscription to {group}', 'mail_confirm_body' => 'Someone (hopefully you) asked to receive the newsletter of {group} at this address. Confirm with the button below; if it wasn’t you, ignore this email and nothing will happen.',
        'mail_unsub_subject' => 'Confirm you want to unsubscribe from {group}', 'mail_unsub_body' => 'Press the button to stop receiving the newsletter of {group}. If you didn’t ask for it, ignore this email.',
        'footer_why' => 'You receive this email because you subscribed to the newsletter of {group}.', 'footer_unsub' => 'Unsubscribe',
        'footer_made' => 'Newsletter created with', 'powered' => 'Powered by',
        'err_email' => 'Please enter a valid email address.', 'err_accept' => 'Please accept the terms to subscribe.', 'err_rate' => 'Too many attempts: try again later.',
        'err_generic' => 'Something went wrong. Please try again.', 'close' => 'Close',
    ],
    'it' => [
        'sub_title' => 'Iscriviti a {group}', 'sub_intro' => 'Ricevi le nostre novità direttamente nella tua casella email.',
        'email' => 'Indirizzo email', 'lang' => 'Lingua della newsletter', 'button' => 'Iscriviti',
        'accept_platform' => 'Accetto le {terms} di OpenNewsletter', 'terms_link' => 'condizioni d’uso',
        'accept_group' => 'Accetto le condizioni di {group}', 'read' => 'Leggile',
        'privacy' => 'Il tuo indirizzo serve solo a inviarti questa newsletter. Registriamo anche la lingua e il nome del tuo browser.',
        'check_email' => 'Ci siamo quasi! Ti abbiamo inviato un’email: apri il link che contiene per confermare l’iscrizione.',
        'confirm_title' => 'Conferma l’iscrizione', 'confirm_body' => 'Premi il pulsante per confermare che vuoi ricevere la newsletter di {group}.',
        'confirm_button' => 'Conferma l’iscrizione', 'confirmed_title' => 'Iscrizione completata!', 'confirmed_body' => 'Grazie: la prossima newsletter di {group} ti arriverà a {email}.',
        'link_invalid' => 'Questo link non è più valido. Iscriviti di nuovo.', 'not_ready' => 'Le iscrizioni a questa newsletter non sono ancora aperte.',
        'not_found' => 'Questa newsletter non esiste.', 'unsub_title' => 'Disiscriviti da {group}',
        'unsub_intro' => 'Inserisci il tuo indirizzo email: ti invieremo un link per confermare.', 'unsub_token' => 'Stai per disiscrivere {email} dalla newsletter di {group}.',
        'reason' => 'Perché te ne vai? (facoltativo)', 'reason_too_many' => 'Troppe email', 'reason_not_relevant' => 'I contenuti non mi interessano',
        'reason_never' => 'Non mi sono mai iscritto', 'reason_other' => 'Altro', 'reason_note' => 'Raccontaci di più (facoltativo)',
        'unsub_button' => 'Disiscriviti', 'unsub_done_title' => 'Disiscrizione completata', 'unsub_done_body' => 'Non riceverai più newsletter da {group}.',
        'unsub_check' => 'Se questo indirizzo è iscritto, gli abbiamo inviato un link per confermare.', 'resubscribe' => 'Iscriviti di nuovo',
        'mail_confirm_subject' => 'Conferma l’iscrizione a {group}', 'mail_confirm_body' => 'Qualcuno (speriamo tu) ha chiesto di ricevere la newsletter di {group} a questo indirizzo. Conferma con il pulsante qui sotto; se non sei stato tu, ignora questa email e non succederà nulla.',
        'mail_unsub_subject' => 'Conferma la disiscrizione da {group}', 'mail_unsub_body' => 'Premi il pulsante per non ricevere più la newsletter di {group}. Se non l’hai chiesto tu, ignora questa email.',
        'footer_why' => 'Ricevi questa email perché sei iscritto alla newsletter di {group}.', 'footer_unsub' => 'Disiscriviti',
        'footer_made' => 'Newsletter generata usando', 'powered' => 'Realizzato con',
        'err_email' => 'Inserisci un indirizzo email valido.', 'err_accept' => 'Accetta le condizioni per iscriverti.', 'err_rate' => 'Troppi tentativi: riprova più tardi.',
        'err_generic' => 'Qualcosa è andato storto. Riprova.', 'close' => 'Chiudi',
    ],
    'fr' => [
        'sub_title' => 'Abonnez-vous à {group}', 'sub_intro' => 'Recevez nos nouvelles directement dans votre boîte mail.',
        'email' => 'Adresse e-mail', 'lang' => 'Langue de la newsletter', 'button' => 'S’abonner',
        'accept_platform' => 'J’accepte les {terms} d’OpenNewsletter', 'terms_link' => 'conditions d’utilisation',
        'accept_group' => 'J’accepte les conditions de {group}', 'read' => 'Les lire',
        'privacy' => 'Votre adresse sert uniquement à vous envoyer cette newsletter. Nous enregistrons aussi la langue et le nom de votre navigateur.',
        'check_email' => 'Presque fini ! Nous vous avons envoyé un e-mail : ouvrez le lien qu’il contient pour confirmer votre abonnement.',
        'confirm_title' => 'Confirmez votre abonnement', 'confirm_body' => 'Appuyez sur le bouton pour confirmer que vous souhaitez recevoir la newsletter de {group}.',
        'confirm_button' => 'Confirmer l’abonnement', 'confirmed_title' => 'Vous êtes abonné !', 'confirmed_body' => 'Merci : la prochaine newsletter de {group} vous parviendra à {email}.',
        'link_invalid' => 'Ce lien n’est plus valide. Abonnez-vous à nouveau.', 'not_ready' => 'Les abonnements à cette newsletter ne sont pas encore ouverts.',
        'not_found' => 'Cette newsletter n’existe pas.', 'unsub_title' => 'Se désabonner de {group}',
        'unsub_intro' => 'Saisissez votre adresse e-mail : nous vous enverrons un lien de confirmation.', 'unsub_token' => 'Vous allez désabonner {email} de la newsletter de {group}.',
        'reason' => 'Pourquoi partez-vous ? (facultatif)', 'reason_too_many' => 'Trop d’e-mails', 'reason_not_relevant' => 'Le contenu ne m’intéresse pas',
        'reason_never' => 'Je ne me suis jamais abonné', 'reason_other' => 'Autre', 'reason_note' => 'Dites-nous en plus (facultatif)',
        'unsub_button' => 'Se désabonner', 'unsub_done_title' => 'Vous êtes désabonné', 'unsub_done_body' => 'Vous ne recevrez plus de newsletters de {group}.',
        'unsub_check' => 'Si cette adresse est abonnée, nous lui avons envoyé un lien de confirmation.', 'resubscribe' => 'Se réabonner',
        'mail_confirm_subject' => 'Confirmez votre abonnement à {group}', 'mail_confirm_body' => 'Quelqu’un (vous, espérons-le) a demandé à recevoir la newsletter de {group} à cette adresse. Confirmez avec le bouton ci-dessous ; si ce n’était pas vous, ignorez cet e-mail.',
        'mail_unsub_subject' => 'Confirmez votre désabonnement de {group}', 'mail_unsub_body' => 'Appuyez sur le bouton pour ne plus recevoir la newsletter de {group}. Si vous ne l’avez pas demandé, ignorez cet e-mail.',
        'footer_why' => 'Vous recevez cet e-mail car vous êtes abonné à la newsletter de {group}.', 'footer_unsub' => 'Se désabonner',
        'footer_made' => 'Newsletter créée avec', 'powered' => 'Propulsé par',
        'err_email' => 'Saisissez une adresse e-mail valide.', 'err_accept' => 'Acceptez les conditions pour vous abonner.', 'err_rate' => 'Trop de tentatives : réessayez plus tard.',
        'err_generic' => 'Une erreur s’est produite. Réessayez.', 'close' => 'Fermer',
    ],
    'de' => [
        'sub_title' => '{group} abonnieren', 'sub_intro' => 'Unsere Neuigkeiten direkt in Ihr Postfach.',
        'email' => 'E-Mail-Adresse', 'lang' => 'Sprache des Newsletters', 'button' => 'Abonnieren',
        'accept_platform' => 'Ich akzeptiere die {terms} von OpenNewsletter', 'terms_link' => 'Nutzungsbedingungen',
        'accept_group' => 'Ich akzeptiere die Bedingungen von {group}', 'read' => 'Lesen',
        'privacy' => 'Ihre Adresse wird nur für diesen Newsletter verwendet. Wir speichern außerdem Sprache und Namen Ihres Browsers.',
        'check_email' => 'Fast geschafft! Wir haben Ihnen eine E-Mail geschickt: Öffnen Sie den Link darin, um Ihr Abonnement zu bestätigen.',
        'confirm_title' => 'Abonnement bestätigen', 'confirm_body' => 'Drücken Sie die Schaltfläche, um zu bestätigen, dass Sie den Newsletter von {group} erhalten möchten.',
        'confirm_button' => 'Abonnement bestätigen', 'confirmed_title' => 'Sie sind angemeldet!', 'confirmed_body' => 'Danke: Der nächste Newsletter von {group} erreicht Sie unter {email}.',
        'link_invalid' => 'Dieser Link ist nicht mehr gültig. Bitte abonnieren Sie erneut.', 'not_ready' => 'Anmeldungen zu diesem Newsletter sind noch nicht möglich.',
        'not_found' => 'Diesen Newsletter gibt es nicht.', 'unsub_title' => 'Von {group} abmelden',
        'unsub_intro' => 'Geben Sie Ihre E-Mail-Adresse ein: Wir senden Ihnen einen Bestätigungslink.', 'unsub_token' => 'Sie melden {email} vom Newsletter von {group} ab.',
        'reason' => 'Warum gehen Sie? (optional)', 'reason_too_many' => 'Zu viele E-Mails', 'reason_not_relevant' => 'Die Inhalte interessieren mich nicht',
        'reason_never' => 'Ich habe mich nie angemeldet', 'reason_other' => 'Anderes', 'reason_note' => 'Erzählen Sie uns mehr (optional)',
        'unsub_button' => 'Abmelden', 'unsub_done_title' => 'Sie wurden abgemeldet', 'unsub_done_body' => 'Sie erhalten keine Newsletter mehr von {group}.',
        'unsub_check' => 'Falls diese Adresse angemeldet ist, haben wir ihr einen Bestätigungslink geschickt.', 'resubscribe' => 'Erneut abonnieren',
        'mail_confirm_subject' => 'Bestätigen Sie Ihr Abonnement von {group}', 'mail_confirm_body' => 'Jemand (hoffentlich Sie) möchte den Newsletter von {group} an diese Adresse erhalten. Bestätigen Sie mit der Schaltfläche unten; falls nicht, ignorieren Sie diese E-Mail.',
        'mail_unsub_subject' => 'Bestätigen Sie die Abmeldung von {group}', 'mail_unsub_body' => 'Drücken Sie die Schaltfläche, um den Newsletter von {group} nicht mehr zu erhalten. Falls Sie das nicht angefordert haben, ignorieren Sie diese E-Mail.',
        'footer_why' => 'Sie erhalten diese E-Mail, weil Sie den Newsletter von {group} abonniert haben.', 'footer_unsub' => 'Abmelden',
        'footer_made' => 'Newsletter erstellt mit', 'powered' => 'Bereitgestellt von',
        'err_email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.', 'err_accept' => 'Bitte akzeptieren Sie die Bedingungen.', 'err_rate' => 'Zu viele Versuche: Bitte später erneut versuchen.',
        'err_generic' => 'Etwas ist schiefgelaufen. Bitte erneut versuchen.', 'close' => 'Schließen',
    ],
    'es' => [
        'sub_title' => 'Suscríbete a {group}', 'sub_intro' => 'Recibe nuestras novedades directamente en tu correo.',
        'email' => 'Correo electrónico', 'lang' => 'Idioma de la newsletter', 'button' => 'Suscribirme',
        'accept_platform' => 'Acepto las {terms} de OpenNewsletter', 'terms_link' => 'condiciones de uso',
        'accept_group' => 'Acepto las condiciones de {group}', 'read' => 'Leerlas',
        'privacy' => 'Tu dirección solo se usa para enviarte esta newsletter. También registramos el idioma y el nombre de tu navegador.',
        'check_email' => '¡Casi listo! Te hemos enviado un correo: abre el enlace que contiene para confirmar tu suscripción.',
        'confirm_title' => 'Confirma tu suscripción', 'confirm_body' => 'Pulsa el botón para confirmar que quieres recibir la newsletter de {group}.',
        'confirm_button' => 'Confirmar suscripción', 'confirmed_title' => '¡Ya estás suscrito!', 'confirmed_body' => 'Gracias: la próxima newsletter de {group} te llegará a {email}.',
        'link_invalid' => 'Este enlace ya no es válido. Suscríbete de nuevo.', 'not_ready' => 'Las suscripciones a esta newsletter aún no están abiertas.',
        'not_found' => 'Esta newsletter no existe.', 'unsub_title' => 'Darse de baja de {group}',
        'unsub_intro' => 'Introduce tu correo: te enviaremos un enlace para confirmar.', 'unsub_token' => 'Vas a dar de baja {email} de la newsletter de {group}.',
        'reason' => '¿Por qué te vas? (opcional)', 'reason_too_many' => 'Demasiados correos', 'reason_not_relevant' => 'El contenido no me interesa',
        'reason_never' => 'Nunca me suscribí', 'reason_other' => 'Otro', 'reason_note' => 'Cuéntanos más (opcional)',
        'unsub_button' => 'Darme de baja', 'unsub_done_title' => 'Te has dado de baja', 'unsub_done_body' => 'No recibirás más newsletters de {group}.',
        'unsub_check' => 'Si esta dirección está suscrita, le hemos enviado un enlace para confirmar.', 'resubscribe' => 'Suscribirme de nuevo',
        'mail_confirm_subject' => 'Confirma tu suscripción a {group}', 'mail_confirm_body' => 'Alguien (esperamos que tú) ha pedido recibir la newsletter de {group} en esta dirección. Confirma con el botón; si no fuiste tú, ignora este correo.',
        'mail_unsub_subject' => 'Confirma tu baja de {group}', 'mail_unsub_body' => 'Pulsa el botón para dejar de recibir la newsletter de {group}. Si no lo pediste, ignora este correo.',
        'footer_why' => 'Recibes este correo porque te suscribiste a la newsletter de {group}.', 'footer_unsub' => 'Darse de baja',
        'footer_made' => 'Newsletter creada con', 'powered' => 'Funciona con',
        'err_email' => 'Introduce un correo válido.', 'err_accept' => 'Acepta las condiciones para suscribirte.', 'err_rate' => 'Demasiados intentos: inténtalo más tarde.',
        'err_generic' => 'Algo salió mal. Inténtalo de nuevo.', 'close' => 'Cerrar',
    ],
    'pt' => [
        'sub_title' => 'Subscreva {group}', 'sub_intro' => 'Receba as nossas novidades diretamente no seu e-mail.',
        'email' => 'Endereço de e-mail', 'lang' => 'Idioma da newsletter', 'button' => 'Subscrever',
        'accept_platform' => 'Aceito os {terms} do OpenNewsletter', 'terms_link' => 'termos de utilização',
        'accept_group' => 'Aceito os termos de {group}', 'read' => 'Ler',
        'privacy' => 'O seu endereço serve apenas para lhe enviar esta newsletter. Registamos também o idioma e o nome do seu navegador.',
        'check_email' => 'Quase pronto! Enviámos-lhe um e-mail: abra a ligação que contém para confirmar a subscrição.',
        'confirm_title' => 'Confirme a subscrição', 'confirm_body' => 'Prima o botão para confirmar que quer receber a newsletter de {group}.',
        'confirm_button' => 'Confirmar subscrição', 'confirmed_title' => 'Subscrição concluída!', 'confirmed_body' => 'Obrigado: a próxima newsletter de {group} chegará a {email}.',
        'link_invalid' => 'Esta ligação já não é válida. Subscreva novamente.', 'not_ready' => 'As subscrições desta newsletter ainda não estão abertas.',
        'not_found' => 'Esta newsletter não existe.', 'unsub_title' => 'Cancelar a subscrição de {group}',
        'unsub_intro' => 'Introduza o seu e-mail: enviaremos uma ligação para confirmar.', 'unsub_token' => 'Vai cancelar a subscrição de {email} da newsletter de {group}.',
        'reason' => 'Porque está a sair? (opcional)', 'reason_too_many' => 'Demasiados e-mails', 'reason_not_relevant' => 'O conteúdo não me interessa',
        'reason_never' => 'Nunca subscrevi', 'reason_other' => 'Outro', 'reason_note' => 'Conte-nos mais (opcional)',
        'unsub_button' => 'Cancelar subscrição', 'unsub_done_title' => 'Subscrição cancelada', 'unsub_done_body' => 'Não receberá mais newsletters de {group}.',
        'unsub_check' => 'Se este endereço estiver subscrito, enviámos-lhe uma ligação para confirmar.', 'resubscribe' => 'Subscrever novamente',
        'mail_confirm_subject' => 'Confirme a subscrição de {group}', 'mail_confirm_body' => 'Alguém (esperamos que você) pediu para receber a newsletter de {group} neste endereço. Confirme com o botão; se não foi você, ignore este e-mail.',
        'mail_unsub_subject' => 'Confirme o cancelamento de {group}', 'mail_unsub_body' => 'Prima o botão para deixar de receber a newsletter de {group}. Se não pediu, ignore este e-mail.',
        'footer_why' => 'Recebe este e-mail porque subscreveu a newsletter de {group}.', 'footer_unsub' => 'Cancelar subscrição',
        'footer_made' => 'Newsletter criada com', 'powered' => 'Criado com',
        'err_email' => 'Introduza um e-mail válido.', 'err_accept' => 'Aceite os termos para subscrever.', 'err_rate' => 'Demasiadas tentativas: tente mais tarde.',
        'err_generic' => 'Algo correu mal. Tente novamente.', 'close' => 'Fechar',
    ],
];

function pub_t(string $lang): array
{
    return PUB_TEXT[$lang] ?? PUB_TEXT['en'];
}

/** The page language: ?lang=, then the browser's languages, among those of the group (texts fall back to English). */
function pub_lang(array $groupLangs): string
{
    $want = lang_code((string)($_GET['lang'] ?? ''));
    if ($want && in_array($want, $groupLangs, true)) return $want;
    foreach (explode(',', (string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '')) as $part) {
        $c = lang_code($part);
        if ($c && in_array($c, $groupLangs, true)) return $c;
    }
    return $groupLangs[0] ?? 'en';
}
