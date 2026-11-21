const APP_VERSION = '1.2.0';

// Newest first. Each entry lists notes per language; English is the fallback.
const RELEASES = [
    {
        version: '1.2.0',
        notes: {
            en: ['New light theme: choose Auto, Light or Dark'],
            it: ['Nuovo tema chiaro: scegli Auto, Chiaro o Scuro'],
            es: ['Nuevo tema claro: elige Auto, Claro u Oscuro'],
            fr: ['Nouveau thème clair : choisis Auto, Clair ou Sombre'],
            de: ['Neues helles Design: wähle Auto, Hell oder Dunkel'],
        },
    },
    {
        version: '1.1.0',
        notes: {
            en: [
                'Phones: hint, undo, resign, draw and chat are now always within reach in a bar under the board',
                'Online: the winning move now appears for both players and rematches start properly',
                'Online: a reloaded page returns to your game, and you can claim the win if your opponent disappears',
                'Hard level is much stronger; discs now really fall down the column',
                'New icon and new typefaces, served from this site with no third-party requests',
                'The game also opens without a connection, for playing on one device or against the computer',
            ],
            it: [
                'Telefoni: suggerimento, annulla, abbandona, patta e chat sono sempre a portata di mano in una barra sotto la plancia',
                'Online: la mossa vincente ora compare a entrambi e la rivincita riparte correttamente',
                "Online: ricaricando la pagina torni alla partita e puoi rivendicare la vittoria se l'avversario sparisce",
                'Il livello Difficile è molto più forte; le fiches ora cadono davvero lungo la colonna',
                'Nuova icona e nuovi caratteri, serviti da questo sito senza richieste a terzi',
                'Il gioco si apre anche senza connessione, per giocare sullo stesso dispositivo o contro il computer',
            ],
            es: [
                'Móviles: pista, deshacer, rendirse, tablas y chat están siempre a mano en una barra bajo el tablero',
                'En línea: la jugada ganadora aparece ahora a ambos jugadores y la revancha se inicia correctamente',
                'En línea: al recargar la página vuelves a tu partida y puedes reclamar la victoria si tu rival desaparece',
                'El nivel Difícil es mucho más fuerte; las fichas ahora caen de verdad por la columna',
                'Nuevo icono y nuevas tipografías, servidas desde este sitio sin peticiones a terceros',
                'El juego también se abre sin conexión, para jugar en el mismo dispositivo o contra el ordenador',
            ],
            fr: [
                'Téléphones : indice, annuler, abandonner, nulle et chat sont toujours à portée de main dans une barre sous la grille',
                'En ligne : le coup gagnant apparaît désormais pour les deux joueurs et la revanche démarre correctement',
                'En ligne : recharger la page vous ramène à votre partie et vous pouvez réclamer la victoire si votre adversaire disparaît',
                'Le niveau Difficile est bien plus fort ; les jetons tombent désormais vraiment dans la colonne',
                'Nouvelle icône et nouvelles polices, servies par ce site sans requête vers des tiers',
                "Le jeu s'ouvre aussi sans connexion, pour jouer sur le même appareil ou contre l'ordinateur",
            ],
            de: [
                'Smartphones: Tipp, Rückgängig, Aufgeben, Remis und Chat sind jetzt immer in einer Leiste unter dem Spielbrett erreichbar',
                'Online: Der Gewinnzug erscheint jetzt bei beiden Spielern und die Revanche startet korrekt',
                'Online: Nach dem Neuladen kehrst du zu deinem Spiel zurück und kannst den Sieg beanspruchen, wenn dein Gegner verschwindet',
                'Die Stufe Schwer ist viel stärker; die Steine fallen jetzt wirklich durch die Spalte',
                'Neues Symbol und neue Schriften, direkt von dieser Website ohne Anfragen an Dritte',
                'Das Spiel startet auch ohne Verbindung, zum Spielen auf einem Gerät oder gegen den Computer',
            ],
        },
    },
    {
        version: '1.0.1',
        notes: {
            en: [
                'New Flat Modern icon: pure matte finish without reflections or blurry shadows, featuring a geometric board and fuchsia discs',
                'New color palettes: Red & Blue, White & Black, Coral & Cobalt, Lime & Hot Pink, Gold & Silver',
                'Refined Flat Modern disc style: completely matte with no reflections, white glares, or specular gradients',
                'Fixed language selection switcher on desktop devices',
                'Visual polish for all text input boxes and responsive layout improvements',
            ],
            it: [
                'Nuova icona in stile Flat Modern: pura finitura matte senza riflessi né ombre sfocate, con plancia geometrica e fiches fucsia',
                'Nuove palette di colori per le fiches: Rosso e Blu, Bianco e Nero, Corallo e Cobalto, Lime e Rosa Shocking, Oro e Argento',
                'Rifinito lo stile "Piatte moderne" (Flat): finitura completamente matte senza riflessi, riflessi bianchi o sfumature speculari',
                'Risolto il cambio della lingua su desktop',
                'Perfezionamento visivo delle caselle di testo e ottimizzazioni responsive',
            ],
            es: [
                'Nuevo icono de estilo Plano Moderno: acabado mate puro sin reflejos ni sombras borrosas, con tablero geométrico y fichas fucsia',
                'Nuevas paletas de colores: Rojo y Azul, Blanco y Negro, Coral y Cobalto, Lima y Rosa Intenso, Oro y Plata',
                'Estilo Plano moderno perfeccionado: acabado completamente mate sin brillos, reflejos ni degradados especulares',
                'Corregido el selector de cambio de idioma en ordenadores de sobremesa',
                'Mejoras visuales en las casillas de texto y diseño responsivo optimizado',
            ],
            fr: [
                'Nouvelle icône de style Plat Moderne : finition mate pure sans reflets ni ombres floues, avec grille géométrique et jetons fuchsia',
                'Nouvelles palettes de couleurs : Rouge et Bleu, Blanc et Noir, Corail et Cobalt, Citron Vert et Rose Vif, Or et Argent',
                'Style Moderne plat affiné : finition mate pure, sans reflets blancs ni dégradés spéculaires',
                'Correction du sélecteur de langue sur ordinateur de bureau',
                'Améliorations graphiques des champs de saisie et de la disposition réactive',
            ],
            de: [
                'Neues Flaches Modernes Symbol: reine matte Optik ohne Glanzlichter oder unscharfe Schatten, mit geometrischem Brett und fuchsiafarbenen Chips',
                'Neue Farbpaletten: Rot & Blau, Weiß & Schwarz, Koralle & Kobalt, Limette & Knalliges Pink, Gold & Silber',
                'Flacher moderner Stil verfeinert: komplett matt ohne Glanz, weiße Reflexionen oder Glanzverläufe',
                'Sprachauswahl auf Desktop-Geräten behoben',
                'Visuelle Verfeinerungen für alle Texteingabefelder und responsives Layout',
            ],
        },
    },
    {
        version: '1.0.0',
        notes: {
            en: [
                'Initial release of Connect Four (Forza Quattro)',
                '4 game modes: Online friend, Random opponent, Computer AI (Easy, Medium, Hard), and Same device',
                'Customise disc colors (Fuchsia, Gold, Cyan, Sunset, Emerald, Violet) and board styles',
                'Live interactive board with smooth drop physics, column guides and winning 4-in-a-row highlight',
                'In-game chat, move history, optional chess-style timers and sound effects',
            ],
            it: [
                'Lancio iniziale di Forza Quattro digitale',
                '4 modalità di gioco: Amico online, Avversario casuale, Contro il computer (Facile, Medio, Difficile) e Stesso dispositivo',
                'Personalizzazione dei colori delle fiches (Fucsia, Oro, Ciano, Tramonto, Smeraldo, Viola) e stili della griglia',
                'Griglia interattiva con animazione di caduta fluida, guide colonna ed evidenziazione della linea vincente',
                'Chat integrata, cronologia delle mosse, timer opzionali ed effetti sonori',
            ],
            es: [
                'Lanzamiento inicial de Conecta 4 (Forza Quattro)',
                '4 modos de juego: Amigo online, Rival aleatorio, Ordenador (Fácil, Medio, Difícil) y Mismo dispositivo',
                'Personaliza los colores de las fichas y los estilos del tablero',
                'Tablero interactivo con caída fluida, guías de columna y resaltado de la línea de 4 en raya',
                'Chat integrado, historial de jugadas, relojes opcionales y efectos de sonido',
            ],
            fr: [
                'Sortie initiale de Puissance 4 (Forza Quattro)',
                '4 modes de jeu : Ami en ligne, Adversaire aléatoire, Ordinateur (Facile, Moyen, Difficile) et Même appareil',
                'Personnalisation des couleurs des jetons et des styles de grille',
                'Grille interactive avec chutes de jetons fluides, repères de colonnes et ligne gagnante illuminée',
                'Tchat en direct, historique des coups, pendules optionnelles et effets sonores',
            ],
            de: [
                'Erstveröffentlichung von Vier gewinnt (Forza Quattro)',
                '4 Spielmodi: Online mit Freund, Zufälliger Gegner, Computer (Leicht, Mittel, Schwer) und Ein Gerät',
                'Anpassbare Chipfarben (Fuchsia, Gold, Cyan, Smaragd usw.) und Brettstile',
                'Interaktives Spielfeld mit flüssigen Fallanimationen, Spaltenführungen und Leuchtlinie bei 4 in einer Reihe',
                'Integrierter Chat, Zughistorie, optionale Uhren und Soundeffekte',
            ],
        },
    },
];
