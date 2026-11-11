const APP_VERSION = '1.1.0';

// Newest first. Each entry lists notes per language; English is the fallback.
const RELEASES = [
    {
        version: '1.1.0',
        notes: {
            en: ['New example game: a guided mine, step by step, that shows how beams, bounces and colours work', 'Android app and shortcuts for the installed app'],
            it: ['Nuova partita di esempio: una miniera guidata, passo dopo passo, che mostra come funzionano raggi, rimbalzi e colori', "App Android e scorciatoie per l'app installata"],
            es: ['Nueva partida de ejemplo: una mina guiada, paso a paso, que muestra cómo funcionan los rayos, los rebotes y los colores', 'App para Android y accesos directos para la app instalada'],
            fr: ['Nouvelle partie d’exemple : une mine guidée, pas à pas, qui montre comment marchent rayons, rebonds et couleurs', 'Application Android et raccourcis pour l’app installée'],
            de: ['Neues Beispielspiel: eine geführte Mine, Schritt für Schritt, die zeigt, wie Strahlen, Abpraller und Farben funktionieren', 'Android-App und Verknüpfungen für die installierte App'],
        },
    },
    {
        version: '1.0.0',
        notes: {
            en: ['First release: solo mines on three levels, the mine of the day and online duels', 'Sketch checker and beam paths to help your reasoning', 'Five languages, light and dark theme, works offline for solo games'],
            it: ['Prima versione: miniere da solo su tre livelli, la miniera del giorno e i duelli online', 'Controllo della bozza e percorso dei raggi per aiutarti a ragionare', 'Cinque lingue, tema chiaro e scuro, funziona offline per le partite da solo'],
            es: ['Primera versión: minas en solitario con tres niveles, la mina del día y duelos en línea', 'Comprobación del boceto y recorrido de los rayos para ayudarte a razonar', 'Cinco idiomas, tema claro y oscuro, funciona sin conexión en solitario'],
            fr: ['Première version : mines en solo sur trois niveaux, la mine du jour et les duels en ligne', 'Vérification de l’esquisse et trajet des rayons pour t’aider à raisonner', 'Cinq langues, thème clair et sombre, fonctionne hors ligne en solo'],
            de: ['Erste Version: Solo-Minen in drei Stufen, die Mine des Tages und Online-Duelle', 'Skizzenprüfung und Strahlenwege als Denkhilfe', 'Fünf Sprachen, helles und dunkles Design, solo auch offline spielbar'],
        },
    },
];
