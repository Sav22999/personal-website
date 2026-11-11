/*
 * ThinkHop — daily ranking and friends. The username is the only public data of an account: nothing else about a
 * player ever reaches another player.
 */
(function () {
    'use strict';

    const I = window.ThinkHopI18n;
    I.extend({
        en: {
            username_change: 'Change username', ranking_show: 'Show me in the ranking', ranking_hint: 'If you turn this off, other players no longer see you in the ranking. You can still find your own result.', ranking_on: 'You appear in the ranking', ranking_off: 'You no longer appear in the ranking', ranking_hidden: 'Only you can see your result.', email_fixed: 'The email can’t be changed.', verify_title: 'Confirm your email', verify_signup_text: 'We sent a link to {email}: open it to activate your account and sign in. It may take a minute, check spam too. If you already have an account with this address, just sign in.', verify_text: 'We sent a link to {email}. Open it to start using your account: until then the account stays locked.', verify_resend: 'Send the link again', verify_sent: 'New link sent to {email}. It may take a minute: check spam too.', verify_done: 'I’ve confirmed it', verify_not_yet: 'Not confirmed yet: open the link in the email.', verify_ok: 'Email confirmed. Welcome!', verify_confirm_title: 'Confirm your email', verify_confirm_text: 'Tap the button to confirm the address of your ThinkHop account.', verify_confirm_btn: 'Confirm email', err_email_unverified: 'Confirm your email first.', err_token_invalid: 'This link has expired or was already used: sign in and ask for a new one.', notify_title: 'Notifications', notify_device: 'On this device', notify_text: 'Choose what you want to hear about. Your choices follow your account; this switch is only for this device.', notify_daily: 'Daily challenge', notify_daily_hint: 'A reminder if you haven’t played today', notify_streak: 'Streak in danger', notify_streak_hint: 'When your streak is about to end', notify_friends: 'New friends', notify_friends_hint: 'When someone adds you', notify_overtaken: 'Overtakes', notify_overtaken_hint: 'When a friend beats you in today’s ranking', notify_blocked: 'Notifications are blocked: allow them in your browser settings.', notify_unsupported: 'This browser or app can’t receive notifications. On iPhone, add the game to the Home Screen first.', err_push_unavailable: 'Notifications aren’t available right now.',
            notify_live: 'Immediate', notify_scheduled: 'Reminder', notify_hour_label: 'Time', notify_default: 'default',
            notify_tz_note: 'Times are based on the server timezone ({tz}, current server time: {time}).',
            notify_tz_local: '{h} on your device', notify_saved: 'Notification settings saved',
            username: 'Username', username_title: 'Choose your username', username_hint: '3–20 letters, numbers or _. It is public.',
            username_text: 'It is the only thing other players can see: it appears in the rankings and in the search for friends.',
            err_username_invalid: 'Use 3–20 letters, numbers or _.', err_username_taken: 'This username is already taken.', err_not_found: 'Player not found.', err_too_many_friends: 'You have reached the maximum number of friends.',
            board: 'Ranking', board_title: 'Today’s ranking', board_all: 'Everyone', board_friends: 'Friends', board_empty: 'Nobody here yet: play the daily challenge!', board_sign_in: 'Sign in to appear in the ranking.', board_row: '{n}/{m} · {t}',
            friends: 'Friends', friends_title: 'Your friends', friends_search: 'Search by username', friends_empty: 'No friends yet: search for them by username.', friends_none: 'No player found.', friends_sign_in: 'Sign in to add friends.',
            friend_add: 'Add', friend_remove: 'Remove', friend_added: '{n} added', friend_removed: '{n} removed',
            avatar_title: 'Profile picture', avatar_change: 'Change profile picture', avatar_initial: 'Your initial', avatar_none: 'None',
            avatar_characters: 'Characters', avatar_accessories: 'Accessories', avatar_frames: 'Frames', avatar_backgrounds: 'Backgrounds',
            avatar_owned: 'Owned', avatar_free: 'Free', avatar_buy_btn: 'Buy {name} · {n}', avatar_buy_title: 'Buy {name}?',
            avatar_buy_text: 'It costs {n} points: you have {m}.', avatar_buy_ok: 'Buy', avatar_bought: 'Bought: {name}',
            avatar_need: 'You need {n} more points.', avatar_wallet: 'You have {n} points. Earn more with levels, survival and the daily challenge.',
            avatar_saved: 'Profile picture updated', err_not_enough_points: 'Not enough points.', err_not_owned: 'Buy this item first.',
        },
        it: {
            username_change: 'Cambia username', ranking_show: 'Mostrami in classifica', ranking_hint: 'Se lo disattivi, gli altri giocatori non ti vedono più in classifica. Il tuo risultato resta visibile a te.', ranking_on: 'Compari in classifica', ranking_off: 'Non compari più in classifica', ranking_hidden: 'Il tuo risultato lo vedi solo tu.', email_fixed: 'L’email non si può modificare.', verify_title: 'Conferma la tua email', verify_signup_text: 'Abbiamo inviato un link a {email}: aprilo per attivare l’account e accedere. Può volerci un minuto, controlla anche lo spam. Se con questo indirizzo hai già un account, accedi e basta.', verify_text: 'Abbiamo inviato un link a {email}. Aprilo per iniziare a usare il tuo account: fino ad allora l’account resta bloccato.', verify_resend: 'Invia di nuovo il link', verify_sent: 'Nuovo link inviato a {email}. Può volerci un minuto: controlla anche lo spam.', verify_done: 'L’ho confermata', verify_not_yet: 'Non ancora confermata: apri il link nell’email.', verify_ok: 'Email confermata. Si parte!', verify_confirm_title: 'Conferma la tua email', verify_confirm_text: 'Tocca il pulsante per confermare l’indirizzo del tuo account ThinkHop.', verify_confirm_btn: 'Conferma email', err_email_unverified: 'Prima conferma la tua email.', err_token_invalid: 'Questo link è scaduto o già usato: accedi e chiedine uno nuovo.', notify_title: 'Notifiche', notify_device: 'Su questo dispositivo', notify_text: 'Scegli di cosa vuoi essere avvisato. Le scelte seguono il tuo account; questo interruttore vale solo per questo dispositivo.', notify_daily: 'Sfida del giorno', notify_daily_hint: 'Un promemoria se oggi non hai giocato', notify_streak: 'Serie in pericolo', notify_streak_hint: 'Quando la tua serie sta per finire', notify_friends: 'Nuovi amici', notify_friends_hint: 'Quando qualcuno ti aggiunge', notify_overtaken: 'Sorpassi', notify_overtaken_hint: 'Quando un amico ti supera nella classifica di oggi', notify_blocked: 'Le notifiche sono bloccate: consentile nelle impostazioni del browser.', notify_unsupported: 'Questo browser o questa app non possono ricevere notifiche. Su iPhone aggiungi prima il gioco alla schermata Home.', err_push_unavailable: 'Le notifiche non sono disponibili ora.',
            notify_live: 'Immediata', notify_scheduled: 'Promemoria', notify_hour_label: 'Orario', notify_default: 'predefinito',
            notify_tz_note: 'Gli orari fanno fede al fuso orario del server ({tz}, ora attuale: {time}).',
            notify_tz_local: 'ore {h} sul tuo dispositivo', notify_saved: 'Impostazioni notifiche salvate',
            username: 'Username', username_title: 'Scegli il tuo username', username_hint: '3–20 lettere, numeri o _. È pubblico.',
            username_text: 'È l’unica cosa che gli altri giocatori possono vedere: compare nelle classifiche e nella ricerca amici.',
            err_username_invalid: 'Usa 3–20 lettere, numeri o _.', err_username_taken: 'Questo username è già in uso.', err_not_found: 'Giocatore non trovato.', err_too_many_friends: 'Hai raggiunto il numero massimo di amici.',
            board: 'Classifica', board_title: 'Classifica di oggi', board_all: 'Tutti', board_friends: 'Amici', board_empty: 'Ancora nessuno: gioca la sfida del giorno!', board_sign_in: 'Accedi per comparire in classifica.', board_row: '{n}/{m} · {t}',
            friends: 'Amici', friends_title: 'I tuoi amici', friends_search: 'Cerca per username', friends_empty: 'Ancora nessun amico: cercali per username.', friends_none: 'Nessun giocatore trovato.', friends_sign_in: 'Accedi per aggiungere amici.',
            friend_add: 'Aggiungi', friend_remove: 'Rimuovi', friend_added: '{n} aggiunto', friend_removed: '{n} rimosso',
            avatar_title: 'Immagine profilo', avatar_change: 'Cambia immagine profilo', avatar_initial: 'La tua iniziale', avatar_none: 'Nessuno',
            avatar_characters: 'Personaggi', avatar_accessories: 'Accessori', avatar_frames: 'Cornici', avatar_backgrounds: 'Sfondi',
            avatar_owned: 'Tuo', avatar_free: 'Gratis', avatar_buy_btn: 'Compra {name} · {n}', avatar_buy_title: 'Comprare {name}?',
            avatar_buy_text: 'Costa {n} punti: ne hai {m}.', avatar_buy_ok: 'Compra', avatar_bought: 'Comprato: {name}',
            avatar_need: 'Ti mancano {n} punti.', avatar_wallet: 'Hai {n} punti. Guadagnane altri con i livelli, la sopravvivenza e la sfida del giorno.',
            avatar_saved: 'Immagine profilo aggiornata', err_not_enough_points: 'Non hai abbastanza punti.', err_not_owned: 'Prima compra questo elemento.',
        },
        es: {
            username_change: 'Cambiar nombre de usuario', ranking_show: 'Mostrarme en la clasificación', ranking_hint: 'Si lo desactivas, los demás jugadores ya no te ven en la clasificación. Tu resultado sigue visible para ti.', ranking_on: 'Apareces en la clasificación', ranking_off: 'Ya no apareces en la clasificación', ranking_hidden: 'Tu resultado solo lo ves tú.', email_fixed: 'El correo no se puede cambiar.', verify_title: 'Confirma tu correo', verify_signup_text: 'Hemos enviado un enlace a {email}: ábrelo para activar tu cuenta y entrar. Puede tardar un minuto, revisa también el spam. Si ya tienes una cuenta con esta dirección, simplemente inicia sesión.', verify_text: 'Hemos enviado un enlace a {email}. Ábrelo para empezar a usar tu cuenta: hasta entonces la cuenta queda bloqueada.', verify_resend: 'Enviar de nuevo el enlace', verify_sent: 'Nuevo enlace enviado a {email}. Puede tardar un minuto: revisa también el spam.', verify_done: 'Ya lo he confirmado', verify_not_yet: 'Aún no confirmado: abre el enlace del correo.', verify_ok: 'Correo confirmado. ¡Vamos!', verify_confirm_title: 'Confirma tu correo', verify_confirm_text: 'Pulsa el botón para confirmar la dirección de tu cuenta de ThinkHop.', verify_confirm_btn: 'Confirmar correo', err_email_unverified: 'Primero confirma tu correo.', err_token_invalid: 'Este enlace ha caducado o ya se usó: inicia sesión y pide uno nuevo.', notify_title: 'Notificaciones', notify_device: 'En este dispositivo', notify_text: 'Elige de qué quieres recibir avisos. Tus elecciones siguen a tu cuenta; este interruptor vale solo para este dispositivo.', notify_daily: 'Desafío del día', notify_daily_hint: 'Un recordatorio si hoy no has jugado', notify_streak: 'Racha en peligro', notify_streak_hint: 'Cuando tu racha está a punto de acabar', notify_friends: 'Nuevos amigos', notify_friends_hint: 'Cuando alguien te añade', notify_overtaken: 'Adelantamientos', notify_overtaken_hint: 'Cuando un amigo te supera en la clasificación de hoy', notify_blocked: 'Las notificaciones están bloqueadas: permítelas en los ajustes del navegador.', notify_unsupported: 'Este navegador o app no puede recibir notificaciones. En iPhone añade antes el juego a la pantalla de inicio.', err_push_unavailable: 'Las notificaciones no están disponibles ahora.',
            notify_live: 'Inmediata', notify_scheduled: 'Recordatorio', notify_hour_label: 'Hora', notify_default: 'predeterminado',
            notify_tz_note: 'Los horarios se basan en la zona horaria del servidor ({tz}, hora actual: {time}).',
            notify_tz_local: '{h} en tu dispositivo', notify_saved: 'Ajustes de notificaciones guardados',
            username: 'Nombre de usuario', username_title: 'Elige tu nombre de usuario', username_hint: '3–20 letras, números o _. Es público.',
            username_text: 'Es lo único que ven los demás jugadores: aparece en las clasificaciones y en la búsqueda de amigos.',
            err_username_invalid: 'Usa 3–20 letras, números o _.', err_username_taken: 'Este nombre de usuario ya está en uso.', err_not_found: 'Jugador no encontrado.', err_too_many_friends: 'Has alcanzado el número máximo de amigos.',
            board: 'Clasificación', board_title: 'Clasificación de hoy', board_all: 'Todos', board_friends: 'Amigos', board_empty: 'Aún no hay nadie: ¡juega el desafío del día!', board_sign_in: 'Inicia sesión para aparecer en la clasificación.', board_row: '{n}/{m} · {t}',
            friends: 'Amigos', friends_title: 'Tus amigos', friends_search: 'Buscar por nombre de usuario', friends_empty: 'Aún no tienes amigos: búscalos por nombre de usuario.', friends_none: 'No se ha encontrado ningún jugador.', friends_sign_in: 'Inicia sesión para añadir amigos.',
            friend_add: 'Añadir', friend_remove: 'Quitar', friend_added: '{n} añadido', friend_removed: '{n} quitado',
            avatar_title: 'Foto de perfil', avatar_change: 'Cambiar foto de perfil', avatar_initial: 'Tu inicial', avatar_none: 'Ninguno',
            avatar_characters: 'Personajes', avatar_accessories: 'Accesorios', avatar_frames: 'Marcos', avatar_backgrounds: 'Fondos',
            avatar_owned: 'Tuyo', avatar_free: 'Gratis', avatar_buy_btn: 'Comprar {name} · {n}', avatar_buy_title: '¿Comprar {name}?',
            avatar_buy_text: 'Cuesta {n} puntos: tienes {m}.', avatar_buy_ok: 'Comprar', avatar_bought: 'Comprado: {name}',
            avatar_need: 'Te faltan {n} puntos.', avatar_wallet: 'Tienes {n} puntos. Gana más con los niveles, la supervivencia y el desafío del día.',
            avatar_saved: 'Foto de perfil actualizada', err_not_enough_points: 'No tienes suficientes puntos.', err_not_owned: 'Primero compra este artículo.',
        },
        fr: {
            username_change: 'Changer de pseudo', ranking_show: 'M’afficher au classement', ranking_hint: 'Si tu le désactives, les autres joueurs ne te voient plus au classement. Ton résultat reste visible pour toi.', ranking_on: 'Tu apparais au classement', ranking_off: 'Tu n’apparais plus au classement', ranking_hidden: 'Toi seul vois ton résultat.', email_fixed: 'L’e-mail ne peut pas être modifié.', verify_title: 'Confirme ton e-mail', verify_signup_text: 'Nous avons envoyé un lien à {email} : ouvre-le pour activer ton compte et te connecter. Cela peut prendre une minute, vérifie aussi les spams. Si tu as déjà un compte avec cette adresse, connecte-toi simplement.', verify_text: 'Nous avons envoyé un lien à {email}. Ouvre-le pour commencer à utiliser ton compte : d’ici là, le compte reste bloqué.', verify_resend: 'Renvoyer le lien', verify_sent: 'Nouveau lien envoyé à {email}. Cela peut prendre une minute : vérifie aussi les spams.', verify_done: 'C’est confirmé', verify_not_yet: 'Pas encore confirmé : ouvre le lien de l’e-mail.', verify_ok: 'E-mail confirmé. Bienvenue !', verify_confirm_title: 'Confirme ton e-mail', verify_confirm_text: 'Appuie sur le bouton pour confirmer l’adresse de ton compte ThinkHop.', verify_confirm_btn: 'Confirmer l’e-mail', err_email_unverified: 'Confirme d’abord ton e-mail.', err_token_invalid: 'Ce lien a expiré ou a déjà servi : connecte-toi et demandes-en un nouveau.', notify_title: 'Notifications', notify_device: 'Sur cet appareil', notify_text: 'Choisis ce dont tu veux être averti. Tes choix suivent ton compte ; cet interrupteur ne vaut que pour cet appareil.', notify_daily: 'Défi du jour', notify_daily_hint: 'Un rappel si tu n’as pas joué aujourd’hui', notify_streak: 'Série en danger', notify_streak_hint: 'Quand ta série va se terminer', notify_friends: 'Nouveaux amis', notify_friends_hint: 'Quand quelqu’un t’ajoute', notify_overtaken: 'Dépassements', notify_overtaken_hint: 'Quand un ami te dépasse au classement du jour', notify_blocked: 'Les notifications sont bloquées : autorise-les dans les réglages du navigateur.', notify_unsupported: 'Ce navigateur ou cette appli ne peut pas recevoir de notifications. Sur iPhone, ajoute d’abord le jeu à l’écran d’accueil.', err_push_unavailable: 'Les notifications ne sont pas disponibles pour le moment.',
            notify_live: 'Immédiate', notify_scheduled: 'Rappel', notify_hour_label: 'Heure', notify_default: 'par défaut',
            notify_tz_note: 'Les horaires se réfèrent au fuseau horaire du serveur ({tz}, heure actuelle : {time}).',
            notify_tz_local: '{h}h sur ton appareil', notify_saved: 'Préférences de notifications enregistrées',
            username: 'Pseudo', username_title: 'Choisis ton pseudo', username_hint: '3 à 20 lettres, chiffres ou _. Il est public.',
            username_text: 'C’est la seule chose que les autres joueurs voient : il apparaît dans les classements et la recherche d’amis.',
            err_username_invalid: 'Utilise 3 à 20 lettres, chiffres ou _.', err_username_taken: 'Ce pseudo est déjà pris.', err_not_found: 'Joueur introuvable.', err_too_many_friends: 'Tu as atteint le nombre maximum d’amis.',
            board: 'Classement', board_title: 'Classement du jour', board_all: 'Tous', board_friends: 'Amis', board_empty: 'Personne pour l’instant : joue le défi du jour !', board_sign_in: 'Connecte-toi pour apparaître au classement.', board_row: '{n}/{m} · {t}',
            friends: 'Amis', friends_title: 'Tes amis', friends_search: 'Chercher par pseudo', friends_empty: 'Pas encore d’amis : cherche-les par pseudo.', friends_none: 'Aucun joueur trouvé.', friends_sign_in: 'Connecte-toi pour ajouter des amis.',
            friend_add: 'Ajouter', friend_remove: 'Retirer', friend_added: '{n} ajouté', friend_removed: '{n} retiré',
            avatar_title: 'Photo de profil', avatar_change: 'Changer la photo de profil', avatar_initial: 'Ton initiale', avatar_none: 'Aucun',
            avatar_characters: 'Personnages', avatar_accessories: 'Accessoires', avatar_frames: 'Cadres', avatar_backgrounds: 'Fonds',
            avatar_owned: 'Acquis', avatar_free: 'Gratuit', avatar_buy_btn: 'Acheter {name} · {n}', avatar_buy_title: 'Acheter {name} ?',
            avatar_buy_text: 'Cela coûte {n} points : tu en as {m}.', avatar_buy_ok: 'Acheter', avatar_bought: 'Acheté : {name}',
            avatar_need: 'Il te manque {n} points.', avatar_wallet: 'Tu as {n} points. Gagnes-en d’autres avec les niveaux, la survie et le défi du jour.',
            avatar_saved: 'Photo de profil mise à jour', err_not_enough_points: 'Tu n’as pas assez de points.', err_not_owned: 'Achète d’abord cet élément.',
        },
        de: {
            username_change: 'Benutzernamen ändern', ranking_show: 'In der Rangliste zeigen', ranking_hint: 'Schaltest du das aus, sehen dich andere nicht mehr in der Rangliste. Dein Ergebnis siehst du weiterhin.', ranking_on: 'Du erscheinst in der Rangliste', ranking_off: 'Du erscheinst nicht mehr in der Rangliste', ranking_hidden: 'Dein Ergebnis siehst nur du.', email_fixed: 'Die E-Mail kann nicht geändert werden.', verify_title: 'Bestätige deine E-Mail', verify_signup_text: 'Wir haben einen Link an {email} geschickt: Öffne ihn, um dein Konto zu aktivieren und dich anzumelden. Das kann eine Minute dauern, schau auch im Spam nach. Hast du mit dieser Adresse schon ein Konto, melde dich einfach an.', verify_text: 'Wir haben einen Link an {email} geschickt. Öffne ihn, um dein Konto zu nutzen: Bis dahin bleibt es gesperrt.', verify_resend: 'Link erneut senden', verify_sent: 'Neuer Link an {email} gesendet. Das kann eine Minute dauern: Schau auch im Spam nach.', verify_done: 'Ich habe bestätigt', verify_not_yet: 'Noch nicht bestätigt: Öffne den Link in der E-Mail.', verify_ok: 'E-Mail bestätigt. Willkommen!', verify_confirm_title: 'Bestätige deine E-Mail', verify_confirm_text: 'Tippe auf die Schaltfläche, um die Adresse deines ThinkHop-Kontos zu bestätigen.', verify_confirm_btn: 'E-Mail bestätigen', err_email_unverified: 'Bestätige zuerst deine E-Mail.', err_token_invalid: 'Dieser Link ist abgelaufen oder wurde schon benutzt: Melde dich an und fordere einen neuen an.', notify_title: 'Benachrichtigungen', notify_device: 'Auf diesem Gerät', notify_text: 'Wähle, worüber du informiert werden möchtest. Deine Auswahl gilt für dein Konto, dieser Schalter nur für dieses Gerät.', notify_daily: 'Tagesaufgabe', notify_daily_hint: 'Eine Erinnerung, wenn du heute nicht gespielt hast', notify_streak: 'Serie in Gefahr', notify_streak_hint: 'Wenn deine Serie bald endet', notify_friends: 'Neue Freunde', notify_friends_hint: 'Wenn dich jemand hinzufügt', notify_overtaken: 'Überholt', notify_overtaken_hint: 'Wenn dich ein Freund in der Tagesrangliste überholt', notify_blocked: 'Benachrichtigungen sind blockiert: Erlaube sie in den Browser-Einstellungen.', notify_unsupported: 'Dieser Browser oder diese App kann keine Benachrichtigungen empfangen. Füge das Spiel auf dem iPhone zuerst zum Home-Bildschirm hinzu.', err_push_unavailable: 'Benachrichtigungen sind gerade nicht verfügbar.',
            notify_live: 'Sofort', notify_scheduled: 'Erinnerung', notify_hour_label: 'Uhrzeit', notify_default: 'Standard',
            notify_tz_note: 'Zeiten beziehen sich auf die Server-Zeitzone ({tz}, aktuelle Zeit: {time}).',
            notify_tz_local: '{h} Uhr auf deinem Gerät', notify_saved: 'Benachrichtigungseinstellungen gespeichert',
            username: 'Benutzername', username_title: 'Wähle deinen Benutzernamen', username_hint: '3–20 Buchstaben, Ziffern oder _. Er ist öffentlich.',
            username_text: 'Das ist das Einzige, was andere sehen: Er erscheint in den Ranglisten und in der Freundessuche.',
            err_username_invalid: 'Verwende 3–20 Buchstaben, Ziffern oder _.', err_username_taken: 'Dieser Benutzername ist vergeben.', err_not_found: 'Spieler nicht gefunden.', err_too_many_friends: 'Du hast die Höchstzahl an Freunden erreicht.',
            board: 'Rangliste', board_title: 'Rangliste von heute', board_all: 'Alle', board_friends: 'Freunde', board_empty: 'Noch niemand: Spiel die Tagesaufgabe!', board_sign_in: 'Melde dich an, um in der Rangliste zu erscheinen.', board_row: '{n}/{m} · {t}',
            friends: 'Freunde', friends_title: 'Deine Freunde', friends_search: 'Nach Benutzername suchen', friends_empty: 'Noch keine Freunde: Suche sie nach Benutzername.', friends_none: 'Kein Spieler gefunden.', friends_sign_in: 'Melde dich an, um Freunde hinzuzufügen.',
            friend_add: 'Hinzufügen', friend_remove: 'Entfernen', friend_added: '{n} hinzugefügt', friend_removed: '{n} entfernt',
            avatar_title: 'Profilbild', avatar_change: 'Profilbild ändern', avatar_initial: 'Dein Anfangsbuchstabe', avatar_none: 'Keines',
            avatar_characters: 'Figuren', avatar_accessories: 'Accessoires', avatar_frames: 'Rahmen', avatar_backgrounds: 'Hintergründe',
            avatar_owned: 'Im Besitz', avatar_free: 'Gratis', avatar_buy_btn: 'Kaufen: {name} · {n}', avatar_buy_title: '{name} kaufen?',
            avatar_buy_text: 'Es kostet {n} Punkte: Du hast {m}.', avatar_buy_ok: 'Kaufen', avatar_bought: 'Gekauft: {name}',
            avatar_need: 'Dir fehlen noch {n} Punkte.', avatar_wallet: 'Du hast {n} Punkte. Verdiene mehr in den Leveln, im Überlebensmodus und bei der Tagesaufgabe.',
            avatar_saved: 'Profilbild aktualisiert', err_not_enough_points: 'Nicht genug Punkte.', err_not_owned: 'Kaufe diesen Gegenstand zuerst.',
        },
    });

    const E = window.ThinkHopEngine;
    const A = window.ThinkHopAccount;
    const Av = window.ThinkHopAvatars;
    const {store, h, toast, modal, skeleton, icon} = window.ThinkHopUI;
    const t = I.t;
    const time = s => Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
    const face = (code, name) => Av ? Av.render(code, name, 'sm') : h('span.avatar.sm', (name || '?').slice(0, 1).toUpperCase());

    /** Accounts off, signed out or no username yet: says what is missing. True when the player can go on. */
    function ready(signInText) {
        if (!A.user()) {
            const m = modal([h('h3', t('account')), h('p.muted', A.enabled() ? signInText : t('sign_in_off')), h('div.actions',
                h('button.btn.btn-ghost', {type: 'button', onclick: () => m.close()}, t('close')),
                A.enabled() ? h('button.btn.btn-primary', {
                    type: 'button', onclick: () => {
                        m.close();
                        A.open();
                    }
                }, t('sign_in')) : null)]);
            return false;
        }
        return true;
    }

    function showBoard() {
        if (!A.enabled()) return ready('');
        const list = h('div.people');
        let scope = 'all';
        const tabs = h('div.seg', [['all', 'board_all'], ['friends', 'board_friends']].map(([k, label]) => h('button' + (k === scope ? '.sel' : ''), {
            type: 'button', 'data-k': k, onclick: () => {
                scope = k;
                [...tabs.children].forEach(b => b.classList.toggle('sel', b.dataset.k === k));
                load();
            }
        }, t(label))));
        let asked = 0;
        async function load() {
            if (scope === 'friends' && !A.user()) {
                list.replaceChildren(h('p.muted', t('friends_sign_in')));
                return;
            }
            const mine = ++asked;
            list.replaceChildren(skeleton('rows', 5));
            try {
                const d = await A.api('board.daily', {day: E.dayText(), scope});
                if (mine !== asked) return; // the other tab was chosen meanwhile
                list.replaceChildren(...(d.rows.length ? d.rows.map(r => h('div.person' + (r.me ? '.me' : ''),
                    h('span.rank', String(r.rank)), face(r.avatar, r.username), h('span.who', r.username), h('b', r.score.toLocaleString(I.getLang())),
                    h('small.muted', t('board_row', {n: r.correct, m: r.total, t: time(r.seconds)})))) : [h('p.muted', t('board_empty'))]));
            } catch (e) {
                if (mine === asked) list.replaceChildren(h('p.muted', e.message));
            }
        }
        const m = modal([h('h3', t('board_title')), tabs, list,
            A.user() ? (A.user().ranking ? null : h('p.muted', t('ranking_hidden'))) : h('p.muted', t('board_sign_in')),
            h('div.actions', h('button.btn.btn-primary', {type: 'button', onclick: () => m.close()}, t('close')))]);
        load();
    }

    function showFriends() {
        if (!ready(t('friends_sign_in'))) return;
        if (!A.user().username) return A.askUsername(showFriends);
        const search = h('input.ctl', {type: 'search', placeholder: t('friends_search'), 'aria-label': t('friends_search'), maxlength: 20, autocapitalize: 'none', spellcheck: 'false'});
        const found = h('div.people');
        const mine = h('div.people');
        const row = (p, add) => {
            const b = h('button.btn.small' + (add ? '.btn-primary' : '.btn-ghost'), {
                type: 'button', onclick: async () => {
                    b.disabled = true;
                    try {
                        await A.api(add ? 'friends.add' : 'friends.remove', {username: p.username});
                        toast(t(add ? 'friend_added' : 'friend_removed', {n: p.username}), 'ok');
                        await refresh();
                        await find();
                    } catch (e) {
                        toast(e.message, 'bad');
                        b.disabled = false;
                    }
                }
            }, t(add ? 'friend_add' : 'friend_remove'));
            return h('div.person', face(p.avatar, p.username), h('span.who', p.username), b);
        };
        async function refresh() {
            const d = await A.api('friends.list', {});
            mine.replaceChildren(...(d.friends.length ? d.friends.map(p => row(p, false)) : [h('p.muted', t('friends_empty'))]));
        }
        async function find() {
            const q = search.value.trim();
            mine.hidden = q.length >= 2; // while searching, the results already say who is a friend
            if (q.length < 2) return found.replaceChildren();
            if (!found.firstChild || found.firstChild.classList.contains('skeleton')) found.replaceChildren(skeleton('rows', 2));
            const d = await A.api('friends.search', {q});
            if (q !== search.value.trim()) return; // a newer search is on its way
            found.replaceChildren(...(d.players.length ? d.players.map(p => row(p, !p.friend)) : [h('p.muted', t('friends_none'))]));
        }
        let wait = 0;
        search.addEventListener('input', () => {
            clearTimeout(wait);
            wait = setTimeout(() => find().catch(e => toast(e.message, 'bad')), 250);
        });
        const m = modal([h('h3', t('friends_title')), search, found, mine,
            h('div.actions', h('button.btn.btn-primary', {type: 'button', onclick: () => m.close()}, t('close')))]);
        mine.replaceChildren(skeleton('rows', 3));
        refresh().catch(e => mine.replaceChildren(h('p.muted', e.message)));
    }

    // ---------- notifications ----------
    const pushSupported = () => 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;

    async function pushRegistration() {
        return (await navigator.serviceWorker.getRegistration()) || navigator.serviceWorker.register('sw.js');
    }

    const keyBytes = text => Uint8Array.from(atob(text.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0));

    async function subscribeDevice() {
        const reg = await pushRegistration();
        await navigator.serviceWorker.ready;
        const {key} = await A.api('push.key');
        const sub = (await reg.pushManager.getSubscription()) || await reg.pushManager.subscribe({userVisibleOnly: true, applicationServerKey: keyBytes(key)});
        await A.api('push.subscribe', {endpoint: sub.endpoint, lang: I.getLang()});
        store.set('notify_device', true);
        store.set('tip_dismiss_notify', true);
    }

    async function unsubscribeDevice() {
        const reg = await pushRegistration();
        const sub = await reg.pushManager.getSubscription();
        if (!sub) return;
        await A.api('push.unsubscribe', {endpoint: sub.endpoint});
        await sub.unsubscribe();
        store.set('notify_device', false);
    }

    const switchButton = (on, label, change) => {
        const b = h('button.switch' + (on ? '.on' : ''), {
            type: 'button', role: 'switch', 'aria-checked': String(on), 'aria-label': label, onclick: async () => {
                const now = !b.classList.contains('on');
                b.disabled = true;
                const ok = await change(now);
                b.disabled = false;
                if (ok === false) return;
                b.classList.toggle('on', now);
                b.setAttribute('aria-checked', String(now));
            }
        });
        return b;
    };

    /** The window opens at once: what the server knows arrives inside it. */
    async function showNotifications() {
        const body = h('div', skeleton('rows', 5));
        const m = modal([h('h3', t('notify_title')), h('p.muted', t('notify_text')), body,
            h('div.actions', h('button.btn.btn-primary', {type: 'button', onclick: () => m.close()}, t('close')))]);
        let settings;
        try {
            settings = await A.api('notify.get', {});
        } catch (e) {
            body.replaceChildren(h('p.muted', e.message));
            return;
        }
        let off = settings.off || [];
        let hours = settings.hours || {daily: 18, streak: 18};
        const scheduled = settings.scheduled || ['daily', 'streak'];
        const live = settings.live || ['friends', 'overtaken'];
        const serverOffset = typeof settings.server_offset === 'number' ? settings.server_offset : 1;
        const localOffset = -new Date().getTimezoneOffset() / 60;
        const tzDiff = Math.round(localOffset - serverOffset);

        const supported = pushSupported();
        const reg = supported ? await navigator.serviceWorker.getRegistration() : null;
        const device = !!(reg && Notification.permission === 'granted' && await reg.pushManager.getSubscription());
        store.set('notify_device', device);
        const note = h('p.muted.notify-note', {hidden: supported && Notification.permission !== 'denied'}, t(supported ? 'notify_blocked' : 'notify_unsupported'));
        const master = switchButton(device, t('notify_device'), async on => {
            try {
                if (!on) {
                    await unsubscribeDevice();
                    return true;
                }
                if (await Notification.requestPermission() !== 'granted') {
                    note.hidden = false;
                    return false;
                }
                await subscribeDevice();
                return true;
            } catch (e) {
                toast(e.message || t('err_push_unavailable'), 'bad');
                return false;
            }
        });
        if (!supported) master.disabled = true;

        const rows = (settings.types || []).map(k => {
            const isSched = scheduled.includes(k);
            let hourSelect = null;
            let hourRow = null;

            if (isSched) {
                hourSelect = h('select.ctl.notify-select', {
                    disabled: off.includes(k) || undefined,
                    onchange: async () => {
                        const newHour = parseInt(hourSelect.value, 10);
                        hours[k] = newHour;
                        try {
                            const res = await A.api('notify.save', {off, hours});
                            if (res.hours) hours = res.hours;
                            toast(t('notify_saved'), 'ok');
                        } catch (e) {
                            toast(e.message, 'bad');
                        }
                    }
                });
                for (let hVal = 0; hVal < 24; hVal++) {
                    let label = String(hVal);
                    if (hVal === 18) label += ' (' + t('notify_default') + ')';
                    if (tzDiff !== 0) {
                        let localH = (hVal + tzDiff) % 24;
                        if (localH < 0) localH += 24;
                        label += ' · ' + t('notify_tz_local', {h: localH});
                    }
                    const opt = h('option', {value: hVal, selected: (hours[k] ?? 18) === hVal || undefined}, label);
                    hourSelect.append(opt);
                }
                hourRow = h('div.notify-hour-row',
                    h('label', t('notify_hour_label') + ':'),
                    hourSelect
                );
            }

            const sw = switchButton(!off.includes(k), t('notify_' + k), async on => {
                const next = on ? off.filter(x => x !== k) : [...off, k];
                try {
                    const res = await A.api('notify.save', {off: next, hours});
                    off = res.off;
                    if (hourSelect) hourSelect.disabled = !on;
                    return true;
                } catch (e) {
                    toast(e.message, 'bad');
                    return false;
                }
            });

            const tag = isSched
                ? h('span.notify-tag.tag-sched', t('notify_scheduled'))
                : h('span.notify-tag.tag-live', t('notify_live'));

            return h('div.notify-row',
                h('div.notify-main',
                    h('div.notify-head', h('strong', t('notify_' + k)), tag),
                    h('small.muted', t('notify_' + k + '_hint')),
                    hourRow
                ),
                sw
            );
        });

        const tzBox = h('div.notify-tz-box',
            icon('clock'),
            h('span', t('notify_tz_note', {
                tz: settings.server_tz || 'Europe/Rome',
                time: settings.server_time || '18:00'
            }))
        );

        body.replaceChildren(
            h('div.notify-row.notify-master', h('strong', t('notify_device')), master),
            note,
            h('div.notify-list', rows),
            tzBox
        );
    }

    window.ThinkHopSocial = {showBoard, showFriends, showNotifications, pushSupported};
})();
