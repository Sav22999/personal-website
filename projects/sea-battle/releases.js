const APP_VERSION = '1.2.0';

// Newest first. Each entry lists notes per language; English is the fallback.
const RELEASES = [
    {
        version: '1.2.0',
        notes: {
            en: ['Android app, and a shortcut to play the computer from the installed app'],
            it: ["App Android e scorciatoia per giocare contro il computer dall'app installata"],
            es: ['App para Android y acceso directo para jugar contra el ordenador desde la app instalada'],
            fr: ['Application Android et raccourci pour jouer contre l’ordinateur depuis l’app installée'],
            de: ['Android-App und Verknüpfung, um aus der installierten App gegen den Computer zu spielen'],
        },
    },
    {
        version: '1.1.0',
        notes: {
            en: ['Online: if your opponent stays disconnected for 2 minutes you win automatically; a countdown shows how long is left'],
            it: ["Online: se l'avversario resta disconnesso per 2 minuti vinci in automatico; un conto alla rovescia mostra quanto manca"],
            es: ['En línea: si tu rival sigue desconectado 2 minutos ganas automáticamente; una cuenta atrás muestra cuánto falta'],
            fr: ['En ligne : si ton adversaire reste déconnecté 2 minutes, tu gagnes automatiquement ; un compte à rebours indique le temps restant'],
            de: ['Online: Bleibt dein Gegner 2 Minuten getrennt, gewinnst du automatisch; ein Countdown zeigt die verbleibende Zeit'],
        },
    },
    {
        version: '1.0.0',
        notes: {
            en: ['First release: play a friend online, a random opponent, on the same device or against the computer (3 levels)', 'Optional rule: a hit gives you another shot', 'Five languages, light and dark theme, works offline for local games'],
            it: ['Prima versione: gioca con un amico online, con un avversario casuale, sullo stesso dispositivo o contro il computer (3 livelli)', 'Regola facoltativa: se colpisci, spari ancora', 'Cinque lingue, tema chiaro e scuro, funziona offline per le partite in locale'],
            es: ['Primera versión: juega con un amigo en línea, con un rival aleatorio, en el mismo dispositivo o contra el ordenador (3 niveles)', 'Regla opcional: si aciertas, disparas otra vez', 'Cinco idiomas, tema claro y oscuro, funciona sin conexión en las partidas locales'],
            fr: ['Première version : joue avec un ami en ligne, contre un adversaire au hasard, sur le même appareil ou contre l’ordinateur (3 niveaux)', 'Règle facultative : touché, on rejoue', 'Cinq langues, thème clair et sombre, fonctionne hors ligne pour les parties locales'],
            de: ['Erste Version: online gegen einen Freund, einen zufälligen Gegner, am selben Gerät oder gegen den Computer (3 Stufen)', 'Optionale Regel: Nach einem Treffer nochmal schießen', 'Fünf Sprachen, helles und dunkles Design, offline spielbar für lokale Spiele'],
        },
    },
];
