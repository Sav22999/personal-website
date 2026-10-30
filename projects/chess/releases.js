const APP_VERSION = '1.0.0';

// Newest first. Each entry lists its notes per language; English is the fallback.
const RELEASES = [
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
