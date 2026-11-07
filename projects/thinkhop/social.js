/*
 * ThinkHop — daily ranking and friends. The username is the only public data of an account: nothing else about a
 * player ever reaches another player.
 */
(function () {
    'use strict';

    const I = window.ThinkHopI18n;
    I.extend({
        en: {
            username_change: 'Change username', ranking_show: 'Show me in the ranking', ranking_hint: 'If you turn this off, other players no longer see you in the ranking. You can still find your own result.', ranking_on: 'You appear in the ranking', ranking_off: 'You no longer appear in the ranking', ranking_hidden: 'Only you can see your result.', email_fixed: 'The email can’t be changed.', notify_title: 'Notifications', notify_device: 'On this device', notify_text: 'Choose what you want to hear about. Your choices follow your account; this switch is only for this device.', notify_daily: 'Daily challenge', notify_daily_hint: 'A reminder if you haven’t played today', notify_streak: 'Streak in danger', notify_streak_hint: 'When your streak is about to end', notify_friends: 'New friends', notify_friends_hint: 'When someone adds you', notify_overtaken: 'Overtakes', notify_overtaken_hint: 'When a friend beats you in today’s ranking', notify_blocked: 'Notifications are blocked: allow them in your browser settings.', notify_unsupported: 'This browser or app can’t receive notifications. On iPhone, add the game to the Home Screen first.', err_push_unavailable: 'Notifications aren’t available right now.', username: 'Username', username_title: 'Choose your username', username_hint: '3–20 letters, numbers or _. It is public.',
            username_text: 'It is the only thing other players can see: it appears in the rankings and in the search for friends.',
            err_username_invalid: 'Use 3–20 letters, numbers or _.', err_username_taken: 'This username is already taken.', err_not_found: 'Player not found.', err_too_many_friends: 'You have reached the maximum number of friends.',
            board: 'Ranking', board_title: 'Today’s ranking', board_all: 'Everyone', board_friends: 'Friends', board_empty: 'Nobody here yet: play the daily challenge!', board_sign_in: 'Sign in to appear in the ranking.', board_row: '{n}/{m} · {t}',
            friends: 'Friends', friends_title: 'Your friends', friends_search: 'Search by username', friends_empty: 'No friends yet: search for them by username.', friends_none: 'No player found.', friends_sign_in: 'Sign in to add friends.',
            friend_add: 'Add', friend_remove: 'Remove', friend_added: '{n} added', friend_removed: '{n} removed',
        },
        it: {
            username_change: 'Cambia username', ranking_show: 'Mostrami in classifica', ranking_hint: 'Se lo disattivi, gli altri giocatori non ti vedono più in classifica. Il tuo risultato resta visibile a te.', ranking_on: 'Compari in classifica', ranking_off: 'Non compari più in classifica', ranking_hidden: 'Il tuo risultato lo vedi solo tu.', email_fixed: 'L’email non si può modificare.', notify_title: 'Notifiche', notify_device: 'Su questo dispositivo', notify_text: 'Scegli di cosa vuoi essere avvisato. Le scelte seguono il tuo account; questo interruttore vale solo per questo dispositivo.', notify_daily: 'Sfida del giorno', notify_daily_hint: 'Un promemoria se oggi non hai giocato', notify_streak: 'Serie in pericolo', notify_streak_hint: 'Quando la tua serie sta per finire', notify_friends: 'Nuovi amici', notify_friends_hint: 'Quando qualcuno ti aggiunge', notify_overtaken: 'Sorpassi', notify_overtaken_hint: 'Quando un amico ti supera nella classifica di oggi', notify_blocked: 'Le notifiche sono bloccate: consentile nelle impostazioni del browser.', notify_unsupported: 'Questo browser o questa app non possono ricevere notifiche. Su iPhone aggiungi prima il gioco alla schermata Home.', err_push_unavailable: 'Le notifiche non sono disponibili ora.', username: 'Username', username_title: 'Scegli il tuo username', username_hint: '3–20 lettere, numeri o _. È pubblico.',
            username_text: 'È l’unica cosa che gli altri giocatori possono vedere: compare nelle classifiche e nella ricerca amici.',
            err_username_invalid: 'Usa 3–20 lettere, numeri o _.', err_username_taken: 'Questo username è già in uso.', err_not_found: 'Giocatore non trovato.', err_too_many_friends: 'Hai raggiunto il numero massimo di amici.',
            board: 'Classifica', board_title: 'Classifica di oggi', board_all: 'Tutti', board_friends: 'Amici', board_empty: 'Ancora nessuno: gioca la sfida del giorno!', board_sign_in: 'Accedi per comparire in classifica.', board_row: '{n}/{m} · {t}',
            friends: 'Amici', friends_title: 'I tuoi amici', friends_search: 'Cerca per username', friends_empty: 'Ancora nessun amico: cercali per username.', friends_none: 'Nessun giocatore trovato.', friends_sign_in: 'Accedi per aggiungere amici.',
            friend_add: 'Aggiungi', friend_remove: 'Rimuovi', friend_added: '{n} aggiunto', friend_removed: '{n} rimosso',
        },
        es: {
            username_change: 'Cambiar nombre de usuario', ranking_show: 'Mostrarme en la clasificación', ranking_hint: 'Si lo desactivas, los demás jugadores ya no te ven en la clasificación. Tu resultado sigue visible para ti.', ranking_on: 'Apareces en la clasificación', ranking_off: 'Ya no apareces en la clasificación', ranking_hidden: 'Tu resultado solo lo ves tú.', email_fixed: 'El correo no se puede cambiar.', notify_title: 'Notificaciones', notify_device: 'En este dispositivo', notify_text: 'Elige de qué quieres recibir avisos. Tus elecciones siguen a tu cuenta; este interruptor vale solo para este dispositivo.', notify_daily: 'Desafío del día', notify_daily_hint: 'Un recordatorio si hoy no has jugado', notify_streak: 'Racha en peligro', notify_streak_hint: 'Cuando tu racha está a punto de acabar', notify_friends: 'Nuevos amigos', notify_friends_hint: 'Cuando alguien te añade', notify_overtaken: 'Adelantamientos', notify_overtaken_hint: 'Cuando un amigo te supera en la clasificación de hoy', notify_blocked: 'Las notificaciones están bloqueadas: permítelas en los ajustes del navegador.', notify_unsupported: 'Este navegador o app no puede recibir notificaciones. En iPhone añade antes el juego a la pantalla de inicio.', err_push_unavailable: 'Las notificaciones no están disponibles ahora.', username: 'Nombre de usuario', username_title: 'Elige tu nombre de usuario', username_hint: '3–20 letras, números o _. Es público.',
            username_text: 'Es lo único que ven los demás jugadores: aparece en las clasificaciones y en la búsqueda de amigos.',
            err_username_invalid: 'Usa 3–20 letras, números o _.', err_username_taken: 'Este nombre de usuario ya está en uso.', err_not_found: 'Jugador no encontrado.', err_too_many_friends: 'Has alcanzado el número máximo de amigos.',
            board: 'Clasificación', board_title: 'Clasificación de hoy', board_all: 'Todos', board_friends: 'Amigos', board_empty: 'Aún no hay nadie: ¡juega el desafío del día!', board_sign_in: 'Inicia sesión para aparecer en la clasificación.', board_row: '{n}/{m} · {t}',
            friends: 'Amigos', friends_title: 'Tus amigos', friends_search: 'Buscar por nombre de usuario', friends_empty: 'Aún no tienes amigos: búscalos por nombre de usuario.', friends_none: 'No se ha encontrado ningún jugador.', friends_sign_in: 'Inicia sesión para añadir amigos.',
            friend_add: 'Añadir', friend_remove: 'Quitar', friend_added: '{n} añadido', friend_removed: '{n} quitado',
        },
        fr: {
            username_change: 'Changer de pseudo', ranking_show: 'M’afficher au classement', ranking_hint: 'Si tu le désactives, les autres joueurs ne te voient plus au classement. Ton résultat reste visible pour toi.', ranking_on: 'Tu apparais au classement', ranking_off: 'Tu n’apparais plus au classement', ranking_hidden: 'Toi seul vois ton résultat.', email_fixed: 'L’e-mail ne peut pas être modifié.', notify_title: 'Notifications', notify_device: 'Sur cet appareil', notify_text: 'Choisis ce dont tu veux être averti. Tes choix suivent ton compte ; cet interrupteur ne vaut que pour cet appareil.', notify_daily: 'Défi du jour', notify_daily_hint: 'Un rappel si tu n’as pas joué aujourd’hui', notify_streak: 'Série en danger', notify_streak_hint: 'Quand ta série va se terminer', notify_friends: 'Nouveaux amis', notify_friends_hint: 'Quand quelqu’un t’ajoute', notify_overtaken: 'Dépassements', notify_overtaken_hint: 'Quand un ami te dépasse au classement du jour', notify_blocked: 'Les notifications sont bloquées : autorise-les dans les réglages du navigateur.', notify_unsupported: 'Ce navigateur ou cette appli ne peut pas recevoir de notifications. Sur iPhone, ajoute d’abord le jeu à l’écran d’accueil.', err_push_unavailable: 'Les notifications ne sont pas disponibles pour le moment.', username: 'Pseudo', username_title: 'Choisis ton pseudo', username_hint: '3 à 20 lettres, chiffres ou _. Il est public.',
            username_text: 'C’est la seule chose que les autres joueurs voient : il apparaît dans les classements et la recherche d’amis.',
            err_username_invalid: 'Utilise 3 à 20 lettres, chiffres ou _.', err_username_taken: 'Ce pseudo est déjà pris.', err_not_found: 'Joueur introuvable.', err_too_many_friends: 'Tu as atteint le nombre maximum d’amis.',
            board: 'Classement', board_title: 'Classement du jour', board_all: 'Tous', board_friends: 'Amis', board_empty: 'Personne pour l’instant : joue le défi du jour !', board_sign_in: 'Connecte-toi pour apparaître au classement.', board_row: '{n}/{m} · {t}',
            friends: 'Amis', friends_title: 'Tes amis', friends_search: 'Chercher par pseudo', friends_empty: 'Pas encore d’amis : cherche-les par pseudo.', friends_none: 'Aucun joueur trouvé.', friends_sign_in: 'Connecte-toi pour ajouter des amis.',
            friend_add: 'Ajouter', friend_remove: 'Retirer', friend_added: '{n} ajouté', friend_removed: '{n} retiré',
        },
        de: {
            username_change: 'Benutzernamen ändern', ranking_show: 'In der Rangliste zeigen', ranking_hint: 'Schaltest du das aus, sehen dich andere nicht mehr in der Rangliste. Dein Ergebnis siehst du weiterhin.', ranking_on: 'Du erscheinst in der Rangliste', ranking_off: 'Du erscheinst nicht mehr in der Rangliste', ranking_hidden: 'Dein Ergebnis siehst nur du.', email_fixed: 'Die E-Mail kann nicht geändert werden.', notify_title: 'Benachrichtigungen', notify_device: 'Auf diesem Gerät', notify_text: 'Wähle, worüber du informiert werden möchtest. Deine Auswahl gilt für dein Konto, dieser Schalter nur für dieses Gerät.', notify_daily: 'Tagesaufgabe', notify_daily_hint: 'Eine Erinnerung, wenn du heute nicht gespielt hast', notify_streak: 'Serie in Gefahr', notify_streak_hint: 'Wenn deine Serie bald endet', notify_friends: 'Neue Freunde', notify_friends_hint: 'Wenn dich jemand hinzufügt', notify_overtaken: 'Überholt', notify_overtaken_hint: 'Wenn dich ein Freund in der Tagesrangliste überholt', notify_blocked: 'Benachrichtigungen sind blockiert: Erlaube sie in den Browser-Einstellungen.', notify_unsupported: 'Dieser Browser oder diese App kann keine Benachrichtigungen empfangen. Füge das Spiel auf dem iPhone zuerst zum Home-Bildschirm hinzu.', err_push_unavailable: 'Benachrichtigungen sind gerade nicht verfügbar.', username: 'Benutzername', username_title: 'Wähle deinen Benutzernamen', username_hint: '3–20 Buchstaben, Ziffern oder _. Er ist öffentlich.',
            username_text: 'Das ist das Einzige, was andere sehen: Er erscheint in den Ranglisten und in der Freundessuche.',
            err_username_invalid: 'Verwende 3–20 Buchstaben, Ziffern oder _.', err_username_taken: 'Dieser Benutzername ist vergeben.', err_not_found: 'Spieler nicht gefunden.', err_too_many_friends: 'Du hast die Höchstzahl an Freunden erreicht.',
            board: 'Rangliste', board_title: 'Rangliste von heute', board_all: 'Alle', board_friends: 'Freunde', board_empty: 'Noch niemand: Spiel die Tagesaufgabe!', board_sign_in: 'Melde dich an, um in der Rangliste zu erscheinen.', board_row: '{n}/{m} · {t}',
            friends: 'Freunde', friends_title: 'Deine Freunde', friends_search: 'Nach Benutzername suchen', friends_empty: 'Noch keine Freunde: Suche sie nach Benutzername.', friends_none: 'Kein Spieler gefunden.', friends_sign_in: 'Melde dich an, um Freunde hinzuzufügen.',
            friend_add: 'Hinzufügen', friend_remove: 'Entfernen', friend_added: '{n} hinzugefügt', friend_removed: '{n} entfernt',
        },
    });

    const E = window.ThinkHopEngine;
    const A = window.ThinkHopAccount;
    const {h, toast, modal, skeleton} = window.ThinkHopUI;
    const t = I.t;
    const time = s => Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');

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
                    h('span.rank', String(r.rank)), h('span.who', r.username), h('b', r.score.toLocaleString(I.getLang())),
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
            return h('div.person', h('span.who', p.username), b);
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
    }

    async function unsubscribeDevice() {
        const reg = await pushRegistration();
        const sub = await reg.pushManager.getSubscription();
        if (!sub) return;
        await A.api('push.unsubscribe', {endpoint: sub.endpoint});
        await sub.unsubscribe();
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
        let off = settings.off;
        const supported = pushSupported();
        const reg = supported ? await navigator.serviceWorker.getRegistration() : null;
        const device = !!(reg && Notification.permission === 'granted' && await reg.pushManager.getSubscription());
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
        const rows = settings.types.map(k => h('div.notify-row',
            h('div', h('strong', t('notify_' + k)), h('small.muted', t('notify_' + k + '_hint'))),
            switchButton(!off.includes(k), t('notify_' + k), async on => {
                const next = on ? off.filter(x => x !== k) : [...off, k];
                try {
                    off = (await A.api('notify.save', {off: next})).off;
                    return true;
                } catch (e) {
                    toast(e.message, 'bad');
                    return false;
                }
            })));
        body.replaceChildren(h('div.notify-row.notify-master', h('strong', t('notify_device')), master), note, h('div.notify-list', rows));
    }

    window.ThinkHopSocial = {showBoard, showFriends, showNotifications};
})();
