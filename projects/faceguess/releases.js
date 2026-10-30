const APP_VERSION = '1.6.1';

// Newest first. Each entry lists its notes per language; English is the fallback.
const RELEASES = [
    {
        version: '1.6.1',
        notes: {
            en: ['Hidden characters now flip face down, like real cards', 'When enlarged, a hidden character is clearly marked as “Hidden”'],
            it: ['I personaggi nascosti ora si girano a faccia in giù, come carte vere', 'Nella vista ingrandita, un personaggio nascosto è segnato chiaramente come “Nascosto”'],
            es: ['Los personajes ocultos ahora se giran boca abajo, como cartas de verdad', 'Al ampliarlo, un personaje oculto aparece claramente marcado como “Oculto”'],
            fr: ['Les personnages masqués se retournent face cachée, comme de vraies cartes', 'Agrandi, un personnage masqué est clairement indiqué comme « Masqué »'],
            de: ['Verdeckte Figuren werden jetzt wie echte Karten umgedreht', 'Vergrößert ist eine verdeckte Figur deutlich als „Verdeckt“ markiert'],
        },
    },
    {
        version: '1.6.0',
        notes: {
            en: ['New rules: a wrong guess loses the game', 'Fair turns: if the starting player guesses right, the other gets one last try — and a draw is possible', 'New “Game rules” page with illustrated examples', 'Rotating example questions in the question field'],
            it: ['Nuove regole: chi sbaglia il tentativo perde la partita', 'Turni equi: se indovina chi ha iniziato, l’altro ha un’ultima possibilità, ed è possibile pareggiare', 'Nuova pagina “Regole di gioco” con esempi illustrati', 'Esempi di domande a rotazione nel campo delle domande'],
            es: ['Nuevas reglas: un intento fallido hace perder la partida', 'Turnos justos: si acierta quien empezó, el otro tiene una última oportunidad, y puede haber empate', 'Nueva página “Reglas del juego” con ejemplos ilustrados', 'Ejemplos de preguntas que van cambiando en el campo de preguntas'],
            fr: ['Nouvelles règles : une mauvaise réponse fait perdre la partie', 'Tours équitables : si celui qui commence trouve, l’autre a une dernière chance, et l’égalité est possible', 'Nouvelle page « Règles du jeu » avec des exemples illustrés', 'Exemples de questions qui défilent dans le champ de question'],
            de: ['Neue Regeln: Wer falsch rät, verliert das Spiel', 'Faire Züge: Rät der Startspieler richtig, hat der andere einen letzten Versuch – Unentschieden möglich', 'Neue Seite „Spielregeln“ mit illustrierten Beispielen', 'Wechselnde Beispielfragen im Fragefeld'],
        },
    },
    {
        version: '1.5.1',
        notes: {
            en: ['Easier language choice on phones, with full language names', 'Clearer step indicator on the start page', 'FaceGuess icon next to the name'],
            it: ['Scelta della lingua più comoda su telefono, con i nomi completi delle lingue', 'Indicatore dei passi più chiaro nella pagina iniziale', 'Icona di FaceGuess accanto al nome'],
            es: ['Elección de idioma más cómoda en el móvil, con los nombres completos', 'Indicador de pasos más claro en la página de inicio', 'Icono de FaceGuess junto al nombre'],
            fr: ['Choix de la langue plus simple sur téléphone, avec les noms complets', 'Indicateur d’étapes plus clair sur la page d’accueil', 'Icône FaceGuess à côté du nom'],
            de: ['Einfachere Sprachwahl auf dem Handy, mit vollständigen Sprachnamen', 'Klarere Schrittanzeige auf der Startseite', 'FaceGuess-Symbol neben dem Namen'],
        },
    },
    {
        version: '1.5.0',
        notes: {
            en: ['New side panel on large screens: your character, score and the full question history always in view', 'New “How to play” guide on the start page', 'Share your invite straight to WhatsApp and other apps from your phone', 'Version and release notes in the footer'],
            it: ['Nuovo pannello laterale sugli schermi grandi: il tuo personaggio, il punteggio e tutta la cronologia delle domande sempre in vista', 'Nuova guida “Come si gioca” nella pagina iniziale', 'Condividi l’invito su WhatsApp e altre app direttamente dal telefono', 'Versione e note di rilascio nel footer'],
            es: ['Nuevo panel lateral en pantallas grandes: tu personaje, el marcador y todo el historial de preguntas siempre a la vista', 'Nueva guía “Cómo se juega” en la página de inicio', 'Comparte la invitación en WhatsApp y otras apps desde el móvil', 'Versión y notas de la versión en el pie de página'],
            fr: ['Nouveau panneau latéral sur grand écran : ton personnage, le score et tout l’historique des questions toujours visibles', 'Nouveau guide « Comment jouer » sur la page d’accueil', 'Partage l’invitation sur WhatsApp et d’autres apps depuis ton téléphone', 'Version et notes de version dans le pied de page'],
            de: ['Neue Seitenleiste auf großen Bildschirmen: deine Figur, der Spielstand und alle Fragen immer im Blick', 'Neue Anleitung „So wird gespielt“ auf der Startseite', 'Einladung direkt vom Handy per WhatsApp und anderen Apps teilen', 'Version und Versionshinweise in der Fußzeile'],
        },
    },
    {
        version: '1.4.0',
        notes: {
            en: ['New FaceGuess icon — add the game to your home screen', 'The board resizes to show all 24 characters on phones, tablets and desktops', 'More compact controls and bottom sheets on phones'],
            it: ['Nuova icona di FaceGuess: aggiungi il gioco alla schermata Home', 'La board si adatta per mostrare tutti i 24 personaggi su telefono, tablet e desktop', 'Comandi più compatti e pannelli dal basso su telefono'],
            es: ['Nuevo icono de FaceGuess: añade el juego a tu pantalla de inicio', 'El tablero se adapta para mostrar los 24 personajes en móvil, tableta y ordenador', 'Controles más compactos y paneles inferiores en el móvil'],
            fr: ['Nouvelle icône FaceGuess : ajoute le jeu à ton écran d’accueil', 'Le plateau s’adapte pour afficher les 24 personnages sur téléphone, tablette et ordinateur', 'Commandes plus compactes et panneaux en bas de l’écran sur téléphone'],
            de: ['Neues FaceGuess-Symbol – füge das Spiel deinem Startbildschirm hinzu', 'Das Spielfeld passt sich an und zeigt alle 24 Figuren auf Handy, Tablet und Desktop', 'Kompaktere Bedienung und Bottom Sheets auf dem Handy'],
        },
    },
    {
        version: '1.3.0',
        notes: {
            en: ['Play with a random opponent who speaks your language', 'In-person mode: ask your questions out loud, with “End turn”', 'A notice appears if your opponent leaves the game', 'More natural hair, beards and faces'],
            it: ['Gioca con un avversario casuale che parla la tua lingua', 'Modalità di persona: domande a voce, con “Turno finito”', 'Avviso se l’avversario abbandona la partita', 'Capelli, barbe e volti più naturali'],
            es: ['Juega con un oponente aleatorio que hable tu idioma', 'Modo en persona: preguntas en voz alta, con “Terminar turno”', 'Aviso si tu oponente abandona la partida', 'Pelo, barbas y caras más naturales'],
            fr: ['Joue contre un adversaire au hasard qui parle ta langue', 'Mode en personne : questions à voix haute, avec « Fin du tour »', 'Un avis s’affiche si ton adversaire quitte la partie', 'Cheveux, barbes et visages plus naturels'],
            de: ['Spiele gegen einen zufälligen Gegner, der deine Sprache spricht', 'Vor-Ort-Modus: Fragen laut stellen, mit „Zug beenden“', 'Hinweis, wenn dein Gegner das Spiel verlässt', 'Natürlichere Haare, Bärte und Gesichter'],
        },
    },
    {
        version: '1.2.0',
        notes: {
            en: ['Rematch with the same opponent, with a running score', 'Enlarge any character (magnifier or long press)', 'Hair always stands out from the skin', 'Bigger, clearer question field'],
            it: ['Rivincita con lo stesso avversario, con punteggio', 'Ingrandisci qualsiasi personaggio (lente o pressione prolungata)', 'Capelli sempre distinguibili dalla pelle', 'Campo delle domande più grande e chiaro'],
            es: ['Revancha con el mismo oponente, con marcador', 'Amplía cualquier personaje (lupa o pulsación larga)', 'El pelo siempre se distingue de la piel', 'Campo de preguntas más grande y claro'],
            fr: ['Revanche avec le même adversaire, avec score', 'Agrandis n’importe quel personnage (loupe ou appui long)', 'Les cheveux se distinguent toujours de la peau', 'Champ de question plus grand et plus clair'],
            de: ['Revanche mit demselben Gegner, mit Spielstand', 'Jede Figur vergrößern (Lupe oder langes Drücken)', 'Haare heben sich immer von der Haut ab', 'Größeres, klareres Fragefeld'],
        },
    },
    {
        version: '1.1.0',
        notes: {
            en: ['Each player now holds their own secret character', 'More realistic characters with unique names', 'Clearer two-step start page and invite link'],
            it: ['Ogni giocatore ora ha il proprio personaggio segreto', 'Personaggi più realistici, con nomi unici', 'Pagina iniziale in due passi più chiara e link d’invito'],
            es: ['Cada jugador tiene ahora su propio personaje secreto', 'Personajes más realistas, con nombres únicos', 'Página de inicio en dos pasos más clara y enlace de invitación'],
            fr: ['Chaque joueur a désormais son propre personnage secret', 'Personnages plus réalistes, avec des noms uniques', 'Page d’accueil en deux étapes plus claire et lien d’invitation'],
            de: ['Jeder Spieler hat jetzt seine eigene geheime Figur', 'Realistischere Figuren mit eindeutigen Namen', 'Klarere Startseite in zwei Schritten und Einladungslink'],
        },
    },
    {
        version: '1.0.0',
        notes: {
            en: ['First release: two-player games with a room code', 'New characters generated for every match', 'Available in English, Italian, Spanish, French and German'],
            it: ['Prima versione: partite a due giocatori con un codice stanza', 'Personaggi nuovi generati a ogni partita', 'Disponibile in inglese, italiano, spagnolo, francese e tedesco'],
            es: ['Primera versión: partidas de dos jugadores con un código de sala', 'Personajes nuevos generados en cada partida', 'Disponible en inglés, italiano, español, francés y alemán'],
            fr: ['Première version : parties à deux avec un code de salon', 'De nouveaux personnages générés à chaque partie', 'Disponible en anglais, italien, espagnol, français et allemand'],
            de: ['Erste Version: Spiele zu zweit mit einem Raumcode', 'Neue Figuren in jeder Partie', 'Verfügbar auf Englisch, Italienisch, Spanisch, Französisch und Deutsch'],
        },
    },
];
