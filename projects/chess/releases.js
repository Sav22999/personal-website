const APP_VERSION = '1.4.1';

// Newest first. Each entry lists its notes per language; English is the fallback.
const RELEASES = [
    {
        version: '1.4.1',
        notes: {
            en: ['The clock now starts as soon as the game begins', 'You can play the computer with a clock too'],
            it: ['L’orologio ora parte appena inizia la partita', 'Puoi giocare contro il computer anche con l’orologio'],
            es: ['El reloj ahora empieza en cuanto comienza la partida', 'También puedes jugar contra el ordenador con reloj'],
            fr: ['La pendule démarre désormais dès le début de la partie', 'Tu peux aussi jouer contre l’ordinateur avec une pendule'],
            de: ['Die Uhr läuft jetzt ab Spielbeginn', 'Auch gegen den Computer kannst du mit Uhr spielen'],
        },
    },
    {
        version: '1.4.0',
        notes: {
            en: ['Black & white is now the default board', 'Redrawn pieces with Staunton proportions, two-step bases and a glossy finish', 'Customise on large screens: two columns with a live preview of your choices', 'Possible-move markers easier to see on dark squares'],
            it: ['La scacchiera bianco e nero è ora quella predefinita', 'Pezzi ridisegnati con proporzioni Staunton, basi a due gradini e finitura lucida', 'Personalizza su schermi grandi: due colonne con l’anteprima dal vivo delle scelte', 'Segni delle mosse possibili più visibili sulle caselle scure'],
            es: ['El tablero blanco y negro es ahora el predeterminado', 'Piezas redibujadas con proporciones Staunton, bases de dos niveles y acabado brillante', 'Personalizar en pantallas grandes: dos columnas con vista previa en directo', 'Marcas de jugadas posibles más visibles en las casillas oscuras'],
            fr: ['L’échiquier noir et blanc est désormais celui par défaut', 'Pièces redessinées aux proportions Staunton, avec socle à deux niveaux et finition brillante', 'Personnaliser sur grand écran : deux colonnes avec un aperçu en direct', 'Repères des coups possibles plus visibles sur les cases sombres'],
            de: ['Schwarz-Weiß ist jetzt das Standardbrett', 'Neu gezeichnete Figuren mit Staunton-Proportionen, zweistufigem Sockel und Glanz', 'Anpassen auf großen Bildschirmen: zwei Spalten mit Live-Vorschau', 'Markierungen möglicher Züge auf dunklen Feldern besser sichtbar'],
        },
    },
    {
        version: '1.3.0',
        notes: {
            en: ['New options in Customise: coordinates, last-move highlight, possible moves, automatic queen promotion and animation speed'],
            it: ['Nuove opzioni in Personalizza: coordinate, evidenziazione dell’ultima mossa, mosse possibili, promozione automatica a donna e velocità delle animazioni'],
            es: ['Nuevas opciones en Personalizar: coordenadas, resaltado de la última jugada, jugadas posibles, coronación automática en dama y velocidad de las animaciones'],
            fr: ['Nouvelles options dans Personnaliser : coordonnées, surlignage du dernier coup, coups possibles, promotion automatique en dame et vitesse des animations'],
            de: ['Neue Optionen unter Anpassen: Koordinaten, Hervorhebung des letzten Zugs, mögliche Züge, automatische Damenumwandlung und Animationstempo'],
        },
    },
    {
        version: '1.2.1',
        notes: {
            en: ['New black & white board', 'Player names line up with their avatar'],
            it: ['Nuova scacchiera bianco e nero', 'Nomi dei giocatori allineati all’icona'],
            es: ['Nuevo tablero blanco y negro', 'Nombres de los jugadores alineados con su icono'],
            fr: ['Nouvel échiquier noir et blanc', 'Noms des joueurs alignés avec leur icône'],
            de: ['Neues schwarz-weißes Brett', 'Spielernamen sind jetzt mit ihrem Symbol ausgerichtet'],
        },
    },
    {
        version: '1.2.0',
        notes: {
            en: ['Choose the board colours: green, brown, blue, grey, purple or coral', 'Capital letters on the board (A–H) and in the moves, which now show the piece instead of its letter'],
            it: ['Scegli il colore della scacchiera: verde, marrone, blu, grigio, viola o corallo', 'Lettere maiuscole sulla scacchiera (A–H) e nelle mosse, che ora mostrano il pezzo al posto della sua lettera'],
            es: ['Elige el color del tablero: verde, marrón, azul, gris, morado o coral', 'Letras mayúsculas en el tablero (A–H) y en las jugadas, que ahora muestran la pieza en lugar de su letra'],
            fr: ['Choisis la couleur de l’échiquier : vert, marron, bleu, gris, violet ou corail', 'Lettres majuscules sur l’échiquier (A–H) et dans les coups, qui montrent maintenant la pièce au lieu de sa lettre'],
            de: ['Wähle die Brettfarben: Grün, Braun, Blau, Grau, Lila oder Koralle', 'Großbuchstaben auf dem Brett (A–H) und in den Zügen, die jetzt die Figur statt ihres Buchstabens zeigen'],
        },
    },
    {
        version: '1.1.0',
        notes: {
            en: ['Text chat with your opponent during online games', 'Choose the piece style: Classic, Wood, Flat, Marble, Glass or Pixel', 'Redrawn pieces that are easier to read, with a new pawn', 'Captured pieces spin away with a short animation', 'With Tips on, moves you can’t play right now are shown in grey'],
            it: ['Chat testuale con l’avversario nelle partite online', 'Scegli lo stile dei pezzi: Classiche, Legno, Piatte, Marmo, Vetro o Pixel', 'Pezzi ridisegnati e più leggibili, con un nuovo pedone', 'I pezzi catturati spariscono con una breve animazione', 'Con i Tip attivi, le mosse che non puoi giocare ora sono in grigio'],
            es: ['Chat de texto con tu oponente en las partidas online', 'Elige el estilo de las piezas: Clásicas, Madera, Planas, Mármol, Cristal o Píxel', 'Piezas redibujadas y más fáciles de leer, con un peón nuevo', 'Las piezas capturadas desaparecen con una breve animación', 'Con los Consejos activos, las jugadas que no puedes hacer ahora se ven en gris'],
            fr: ['Chat écrit avec ton adversaire pendant les parties en ligne', 'Choisis le style des pièces : Classiques, Bois, Plates, Marbre, Verre ou Pixel', 'Pièces redessinées et plus lisibles, avec un nouveau pion', 'Les pièces prises disparaissent avec une courte animation', 'Avec les Conseils activés, les coups que tu ne peux pas jouer maintenant sont en gris'],
            de: ['Text-Chat mit deinem Gegner in Online-Partien', 'Wähle den Figurenstil: Klassisch, Holz, Flach, Marmor, Glas oder Pixel', 'Neu gezeichnete, besser lesbare Figuren mit einem neuen Bauern', 'Geschlagene Figuren verschwinden mit einer kurzen Animation', 'Mit aktiven Tipps erscheinen Züge, die du gerade nicht spielen kannst, in Grau'],
        },
    },
    {
        version: '1.0.0',
        notes: {
            en: ['Play a friend online with a room code, or a random opponent who speaks your language', 'Two players on the same device, with a board that turns around', 'Play the computer at three levels, with undo and hints', 'Optional clock: 5, 10 or 15 minutes per player', 'Tips: see how each piece moves, what it can capture, which moves give check and which squares are risky'],
            it: ['Gioca online con un amico tramite codice, o con un avversario casuale che parla la tua lingua', 'In due sullo stesso dispositivo, con la scacchiera che si gira', 'Gioca contro il computer su tre livelli, con annulla e suggerimenti', 'Orologio a scelta: 5, 10 o 15 minuti a testa', 'Tip: vedi come si muove ogni pezzo, cosa può catturare, quali mosse danno scacco e quali caselle sono rischiose'],
            es: ['Juega online con un amigo mediante un código, o con un oponente aleatorio que hable tu idioma', 'Dos jugadores en el mismo dispositivo, con un tablero que gira', 'Juega contra el ordenador en tres niveles, con deshacer y pistas', 'Reloj opcional: 5, 10 o 15 minutos por jugador', 'Consejos: mira cómo se mueve cada pieza, qué puede capturar, qué jugadas dan jaque y qué casillas son peligrosas'],
            fr: ['Joue en ligne avec un ami grâce à un code, ou avec un adversaire au hasard qui parle ta langue', 'À deux sur le même appareil, avec un échiquier qui se retourne', 'Joue contre l’ordinateur sur trois niveaux, avec annulation et indices', 'Pendule au choix : 5, 10 ou 15 minutes par joueur', 'Conseils : vois comment chaque pièce se déplace, ce qu’elle peut prendre, quels coups donnent échec et quelles cases sont risquées'],
            de: ['Online mit Freunden per Code oder gegen einen zufälligen Gegner, der deine Sprache spricht', 'Zu zweit an einem Gerät, mit einem Brett, das sich dreht', 'Gegen den Computer in drei Stufen, mit Zugrücknahme und Hinweisen', 'Optionale Uhr: 5, 10 oder 15 Minuten pro Spieler', 'Tipps: Sieh, wie jede Figur zieht, was sie schlagen kann, welche Züge Schach geben und welche Felder riskant sind'],
        },
    },
];
