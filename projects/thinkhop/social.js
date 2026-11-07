/*
 * ThinkHop — daily ranking and friends. The username is the only public data of an account: nothing else about a
 * player ever reaches another player.
 */
(function () {
    'use strict';

    const I = window.ThinkHopI18n;
    I.extend({
        en: {
            username_change: 'Change username', username: 'Username', username_title: 'Choose your username', username_hint: '3–20 letters, numbers or _. It is public.',
            username_text: 'It is the only thing other players can see: it appears in the rankings and in the search for friends.',
            err_username_invalid: 'Use 3–20 letters, numbers or _.', err_username_taken: 'This username is already taken.', err_not_found: 'Player not found.', err_too_many_friends: 'You have reached the maximum number of friends.',
            board: 'Ranking', board_title: 'Today’s ranking', board_all: 'Everyone', board_friends: 'Friends', board_empty: 'Nobody here yet: play the daily challenge!', board_sign_in: 'Sign in to appear in the ranking.', board_row: '{n}/{m} · {t}',
            friends: 'Friends', friends_title: 'Your friends', friends_search: 'Search by username', friends_empty: 'No friends yet: search for them by username.', friends_none: 'No player found.', friends_sign_in: 'Sign in to add friends.',
            friend_add: 'Add', friend_remove: 'Remove', friend_added: '{n} added', friend_removed: '{n} removed',
        },
        it: {
            username_change: 'Cambia username', username: 'Username', username_title: 'Scegli il tuo username', username_hint: '3–20 lettere, numeri o _. È pubblico.',
            username_text: 'È l’unica cosa che gli altri giocatori possono vedere: compare nelle classifiche e nella ricerca amici.',
            err_username_invalid: 'Usa 3–20 lettere, numeri o _.', err_username_taken: 'Questo username è già in uso.', err_not_found: 'Giocatore non trovato.', err_too_many_friends: 'Hai raggiunto il numero massimo di amici.',
            board: 'Classifica', board_title: 'Classifica di oggi', board_all: 'Tutti', board_friends: 'Amici', board_empty: 'Ancora nessuno: gioca la sfida del giorno!', board_sign_in: 'Accedi per comparire in classifica.', board_row: '{n}/{m} · {t}',
            friends: 'Amici', friends_title: 'I tuoi amici', friends_search: 'Cerca per username', friends_empty: 'Ancora nessun amico: cercali per username.', friends_none: 'Nessun giocatore trovato.', friends_sign_in: 'Accedi per aggiungere amici.',
            friend_add: 'Aggiungi', friend_remove: 'Rimuovi', friend_added: '{n} aggiunto', friend_removed: '{n} rimosso',
        },
        es: {
            username_change: 'Cambiar nombre de usuario', username: 'Nombre de usuario', username_title: 'Elige tu nombre de usuario', username_hint: '3–20 letras, números o _. Es público.',
            username_text: 'Es lo único que ven los demás jugadores: aparece en las clasificaciones y en la búsqueda de amigos.',
            err_username_invalid: 'Usa 3–20 letras, números o _.', err_username_taken: 'Este nombre de usuario ya está en uso.', err_not_found: 'Jugador no encontrado.', err_too_many_friends: 'Has alcanzado el número máximo de amigos.',
            board: 'Clasificación', board_title: 'Clasificación de hoy', board_all: 'Todos', board_friends: 'Amigos', board_empty: 'Aún no hay nadie: ¡juega el desafío del día!', board_sign_in: 'Inicia sesión para aparecer en la clasificación.', board_row: '{n}/{m} · {t}',
            friends: 'Amigos', friends_title: 'Tus amigos', friends_search: 'Buscar por nombre de usuario', friends_empty: 'Aún no tienes amigos: búscalos por nombre de usuario.', friends_none: 'No se ha encontrado ningún jugador.', friends_sign_in: 'Inicia sesión para añadir amigos.',
            friend_add: 'Añadir', friend_remove: 'Quitar', friend_added: '{n} añadido', friend_removed: '{n} quitado',
        },
        fr: {
            username_change: 'Changer de pseudo', username: 'Pseudo', username_title: 'Choisis ton pseudo', username_hint: '3 à 20 lettres, chiffres ou _. Il est public.',
            username_text: 'C’est la seule chose que les autres joueurs voient : il apparaît dans les classements et la recherche d’amis.',
            err_username_invalid: 'Utilise 3 à 20 lettres, chiffres ou _.', err_username_taken: 'Ce pseudo est déjà pris.', err_not_found: 'Joueur introuvable.', err_too_many_friends: 'Tu as atteint le nombre maximum d’amis.',
            board: 'Classement', board_title: 'Classement du jour', board_all: 'Tous', board_friends: 'Amis', board_empty: 'Personne pour l’instant : joue le défi du jour !', board_sign_in: 'Connecte-toi pour apparaître au classement.', board_row: '{n}/{m} · {t}',
            friends: 'Amis', friends_title: 'Tes amis', friends_search: 'Chercher par pseudo', friends_empty: 'Pas encore d’amis : cherche-les par pseudo.', friends_none: 'Aucun joueur trouvé.', friends_sign_in: 'Connecte-toi pour ajouter des amis.',
            friend_add: 'Ajouter', friend_remove: 'Retirer', friend_added: '{n} ajouté', friend_removed: '{n} retiré',
        },
        de: {
            username_change: 'Benutzernamen ändern', username: 'Benutzername', username_title: 'Wähle deinen Benutzernamen', username_hint: '3–20 Buchstaben, Ziffern oder _. Er ist öffentlich.',
            username_text: 'Das ist das Einzige, was andere sehen: Er erscheint in den Ranglisten und in der Freundessuche.',
            err_username_invalid: 'Verwende 3–20 Buchstaben, Ziffern oder _.', err_username_taken: 'Dieser Benutzername ist vergeben.', err_not_found: 'Spieler nicht gefunden.', err_too_many_friends: 'Du hast die Höchstzahl an Freunden erreicht.',
            board: 'Rangliste', board_title: 'Rangliste von heute', board_all: 'Alle', board_friends: 'Freunde', board_empty: 'Noch niemand: Spiel die Tagesaufgabe!', board_sign_in: 'Melde dich an, um in der Rangliste zu erscheinen.', board_row: '{n}/{m} · {t}',
            friends: 'Freunde', friends_title: 'Deine Freunde', friends_search: 'Nach Benutzername suchen', friends_empty: 'Noch keine Freunde: Suche sie nach Benutzername.', friends_none: 'Kein Spieler gefunden.', friends_sign_in: 'Melde dich an, um Freunde hinzuzufügen.',
            friend_add: 'Hinzufügen', friend_remove: 'Entfernen', friend_added: '{n} hinzugefügt', friend_removed: '{n} entfernt',
        },
    });

    const E = window.ThinkHopEngine;
    const A = window.ThinkHopAccount;
    const {h, toast, modal} = window.ThinkHopUI;
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
        async function load() {
            if (scope === 'friends' && !A.user()) {
                list.replaceChildren(h('p.muted', t('friends_sign_in')));
                return;
            }
            try {
                const d = await A.api('board.daily', {day: E.dayText(), scope});
                list.replaceChildren(...(d.rows.length ? d.rows.map(r => h('div.person' + (r.me ? '.me' : ''),
                    h('span.rank', String(r.rank)), h('span.who', r.username), h('b', r.score.toLocaleString(I.getLang())),
                    h('small.muted', t('board_row', {n: r.correct, m: r.total, t: time(r.seconds)})))) : [h('p.muted', t('board_empty'))]));
            } catch (e) {
                list.replaceChildren(h('p.muted', e.message));
            }
        }
        const m = modal([h('h3', t('board_title')), tabs, list,
            A.user() ? null : h('p.muted', t('board_sign_in')),
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
        refresh().catch(e => mine.replaceChildren(h('p.muted', e.message)));
    }

    window.ThinkHopSocial = {showBoard, showFriends};
})();
