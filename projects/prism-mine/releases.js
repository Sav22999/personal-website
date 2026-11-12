const APP_VERSION = '1.4.0';

// Newest first. Each entry lists notes per language; English is the fallback.
const RELEASES = [
    {
        version: '1.4.0',
        notes: {
            en: ['Each level has its own mine: easy 6 × 6, medium 8 × 8, hard 10 × 10', 'Online duel: the clock is now per turn; when it runs out, the turn passes'],
            it: ['Ogni livello ha la sua miniera: facile 6 × 6, medio 8 × 8, difficile 10 × 10', "Duello online: l'orologio ora è per turno; quando scade, il turno passa"],
            es: ['Cada nivel tiene su mina: fácil 6 × 6, medio 8 × 8, difícil 10 × 10', 'Duelo en línea: el reloj ahora es por turno; cuando se acaba, pasa el turno'],
            fr: ['Chaque niveau a sa mine : facile 6 × 6, moyen 8 × 8, difficile 10 × 10', 'Duel en ligne : la pendule est maintenant par tour ; quand elle s’arrête, le tour passe'],
            de: ['Jede Stufe hat ihre eigene Mine: leicht 6 × 6, mittel 8 × 8, schwer 10 × 10', 'Online-Duell: Die Uhr läuft jetzt pro Zug; ist sie abgelaufen, geht der Zug weiter'],
        },
    },
    {
        version: '1.3.0',
        notes: {
            en: ['Online duel: each player hides the gems in their own mine, like in Sea Battle, then you take turns finding the other', 'Switch between their mine and yours during the duel', 'Placing gems: drag them, tap one to rotate or remove it, drag it out to put it back; easier on phones'],
            it: ["Duello online: ognuno nasconde le gemme nella propria miniera, come a battaglia navale, poi a turno cercate quella dell'altro", 'Durante il duello passa dalla sua miniera alla tua', 'Posizionare le gemme: trascinale, tocca una gemma per ruotarla o toglierla, trascinala fuori per rimetterla nel vassoio; più comodo sul telefono'],
            es: ['Duelo en línea: cada uno esconde las gemas en su propia mina, como en la batalla naval, y luego por turnos buscáis la del otro', 'Durante el duelo cambia entre su mina y la tuya', 'Colocar gemas: arrástralas, toca una para girarla o quitarla, arrástrala fuera para devolverla; más cómodo en el móvil'],
            fr: ['Duel en ligne : chacun cache les gemmes dans sa propre mine, comme à la bataille navale, puis vous cherchez à tour de rôle celle de l’autre', 'Pendant le duel, passe de sa mine à la tienne', 'Placer les gemmes : fais-les glisser, touche-en une pour la tourner ou l’enlever, fais-la glisser dehors pour la remettre ; plus simple sur téléphone'],
            de: ['Online-Duell: Jeder versteckt die Edelsteine in seiner eigenen Mine, wie bei Schiffe versenken, dann sucht ihr abwechselnd die des anderen', 'Wechsle während des Duells zwischen seiner und deiner Mine', 'Edelsteine legen: ziehen, antippen zum Drehen oder Entfernen, hinausziehen zum Zurücklegen; einfacher am Handy'],
        },
    },
    {
        version: '1.2.0',
        notes: {
            en: ['Four new gems: rod, peak, corner and bridge', 'The rules show how each gem bounces the beam, with pictures of real beams'],
            it: ['Quattro nuove gemme: barra, vetta, angolo e ponte', 'Le regole mostrano come ogni gemma devia il raggio, con le figure di raggi veri'],
            es: ['Cuatro gemas nuevas: barra, pico, esquina y puente', 'Las reglas muestran cómo desvía el rayo cada gema, con dibujos de rayos reales'],
            fr: ['Quatre nouvelles gemmes : barre, pic, équerre et pont', 'Les règles montrent comment chaque gemme renvoie le rayon, avec des dessins de vrais rayons'],
            de: ['Vier neue Edelsteine: Stab, Gipfel, Winkel und Brücke', 'Die Regeln zeigen mit Bildern echter Strahlen, wie jeder Edelstein den Strahl lenkt'],
        },
    },
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
