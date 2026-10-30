// Game rules page: texts per language plus small example boards drawn with the real engine.
const RULES_TEXT = {
    en: {
        goal_t: 'Goal', goal: 'Checkmate the opponent’s king: attack it so that it has no way to escape. White always moves first, then the players take turns, one move each.',
        pieces_t: 'How the pieces move', pieces: 'Dots show where the piece can go; a ring means it can capture there.',
        special_t: 'Special moves',
        castle: 'Castling: the king moves two squares towards a rook, which jumps to the other side. Only if neither has moved, the squares between are empty and the king is not in check and doesn’t pass through check.',
        ep: 'En passant: a pawn that has just moved two squares can be captured by an enemy pawn beside it, as if it had moved one.',
        promo: 'Promotion: a pawn reaching the last rank becomes a queen, rook, bishop or knight.',
        end_t: 'Check, checkmate and stalemate',
        check: 'Check: the king is attacked. You must get it out of danger.',
        mate: 'Checkmate: the king is in check and there is no way out. Game over!',
        stalemate: 'Stalemate: no legal moves, but no check. It’s a draw.',
        draws_t: 'Draws', draws: ['Stalemate', 'Not enough pieces to checkmate (e.g. king against king)', 'The same position three times', '50 moves each without captures or pawn moves', 'Both players agree'],
        clock_t: 'Clock', clock: 'With a clock, each player has the same amount of time for the whole game. Each clock starts after that player’s first move. If your time runs out you lose — unless your opponent no longer has enough pieces to checkmate: then it’s a draw.',
        online_t: 'Online games', online: ['Leaving a game in progress counts as resigning.', 'If your opponent stays offline for a minute, you can claim the win.', 'You can offer a draw: your opponent accepts or declines. Moving declines an offer.', 'In a rematch the colours swap.'],
        tips_t: 'Tips and hints', tips: 'With Tips on, tap any piece to see what it can do. The colours of the markers:',
        legend: { safe: 'Safe square', unsafe: 'It could be captured there', capture: 'Capture', check: 'Gives check', special: 'Special move' },
        hint: 'Against the computer and on the same device you can also ask for a Hint (the suggested move appears as an arrow) and Undo moves.',
        score_t: 'Score', score: 'Win = 1 point, draw = ½ point each.',
    },
    it: {
        goal_t: 'Obiettivo', goal: 'Dare scacco matto al re avversario: attaccarlo in modo che non abbia vie di fuga. Il Bianco muove sempre per primo, poi si alterna una mossa a testa.',
        pieces_t: 'Come si muovono i pezzi', pieces: 'I pallini mostrano dove può andare il pezzo; il cerchio indica una cattura.',
        special_t: 'Mosse speciali',
        castle: 'Arrocco: il re si sposta di due caselle verso una torre, che salta dall’altra parte. Solo se nessuno dei due si è mai mosso, le caselle in mezzo sono libere e il re non è sotto scacco né passa per una casella attaccata.',
        ep: 'En passant: un pedone appena avanzato di due caselle può essere catturato da un pedone avversario al suo fianco, come se fosse avanzato di una.',
        promo: 'Promozione: un pedone che arriva in fondo diventa donna, torre, alfiere o cavallo.',
        end_t: 'Scacco, scacco matto e stallo',
        check: 'Scacco: il re è attaccato. Devi metterlo al sicuro.',
        mate: 'Scacco matto: il re è sotto scacco e non c’è via d’uscita. Partita finita!',
        stalemate: 'Stallo: nessuna mossa possibile, ma nessuno scacco. È patta.',
        draws_t: 'Patta', draws: ['Stallo', 'Pezzi insufficienti per dare matto (es. re contro re)', 'La stessa posizione per tre volte', '50 mosse a testa senza catture né mosse di pedone', 'Accordo tra i giocatori'],
        clock_t: 'Orologio', clock: 'Con l’orologio ogni giocatore ha lo stesso tempo per tutta la partita. Il tempo di ciascuno parte dopo la sua prima mossa. Se finisci il tempo perdi, a meno che l’avversario non abbia più pezzi sufficienti per dare matto: in quel caso è patta.',
        online_t: 'Partite online', online: ['Uscire da una partita in corso equivale ad abbandonare.', 'Se l’avversario resta offline per un minuto, puoi rivendicare la vittoria.', 'Puoi proporre la patta: l’avversario accetta o rifiuta. Muovere equivale a rifiutare.', 'Nella rivincita i colori si invertono.'],
        tips_t: 'Tip e suggerimenti', tips: 'Con i Tip attivi, tocca un pezzo qualsiasi per vedere cosa può fare. I colori dei segnali:',
        legend: { safe: 'Casella sicura', unsafe: 'Lì potrebbe essere catturato', capture: 'Cattura', check: 'Dà scacco', special: 'Mossa speciale' },
        hint: 'Contro il computer e sullo stesso dispositivo puoi anche chiedere un Suggerimento (la mossa consigliata compare come freccia) e Annullare le mosse.',
        score_t: 'Punteggio', score: 'Vittoria = 1 punto, patta = ½ punto a testa.',
    },
    es: {
        goal_t: 'Objetivo', goal: 'Dar jaque mate al rey rival: atacarlo de forma que no pueda escapar. Las blancas siempre empiezan y luego se alterna una jugada cada uno.',
        pieces_t: 'Cómo se mueven las piezas', pieces: 'Los puntos muestran adónde puede ir la pieza; el anillo indica una captura.',
        special_t: 'Jugadas especiales',
        castle: 'Enroque: el rey se mueve dos casillas hacia una torre, que salta al otro lado. Solo si ninguno se ha movido, las casillas intermedias están libres y el rey no está en jaque ni pasa por una casilla atacada.',
        ep: 'Al paso: un peón que acaba de avanzar dos casillas puede ser capturado por un peón rival a su lado, como si hubiera avanzado una.',
        promo: 'Coronación: un peón que llega al final se convierte en dama, torre, alfil o caballo.',
        end_t: 'Jaque, jaque mate y ahogado',
        check: 'Jaque: el rey está atacado. Debes ponerlo a salvo.',
        mate: 'Jaque mate: el rey está en jaque y no hay salida. ¡Fin de la partida!',
        stalemate: 'Ahogado: no hay jugadas legales, pero tampoco jaque. Son tablas.',
        draws_t: 'Tablas', draws: ['Ahogado', 'Piezas insuficientes para dar mate (p. ej. rey contra rey)', 'La misma posición tres veces', '50 jugadas por bando sin capturas ni movimientos de peón', 'Acuerdo entre los jugadores'],
        clock_t: 'Reloj', clock: 'Con reloj, cada jugador tiene el mismo tiempo para toda la partida. El reloj de cada uno arranca tras su primera jugada. Si se te acaba el tiempo pierdes, salvo que el rival ya no tenga piezas para dar mate: entonces son tablas.',
        online_t: 'Partidas online', online: ['Salir de una partida en curso cuenta como rendirse.', 'Si tu oponente sigue desconectado un minuto, puedes reclamar la victoria.', 'Puedes ofrecer tablas: tu oponente acepta o rechaza. Mover equivale a rechazar.', 'En la revancha se cambian los colores.'],
        tips_t: 'Consejos y pistas', tips: 'Con los consejos activados, toca cualquier pieza para ver lo que puede hacer. Los colores de las marcas:',
        legend: { safe: 'Casilla segura', unsafe: 'Podrían capturarla ahí', capture: 'Captura', check: 'Da jaque', special: 'Jugada especial' },
        hint: 'Contra el ordenador y en el mismo dispositivo también puedes pedir una Pista (la jugada sugerida aparece como flecha) y Deshacer jugadas.',
        score_t: 'Puntuación', score: 'Victoria = 1 punto, tablas = ½ punto cada uno.',
    },
    fr: {
        goal_t: 'But du jeu', goal: 'Mettre échec et mat le roi adverse : l’attaquer sans qu’il puisse s’échapper. Les Blancs commencent toujours, puis chacun joue un coup à tour de rôle.',
        pieces_t: 'Déplacement des pièces', pieces: 'Les points montrent où la pièce peut aller ; un anneau indique une prise.',
        special_t: 'Coups spéciaux',
        castle: 'Roque : le roi se déplace de deux cases vers une tour, qui saute de l’autre côté. Seulement si aucun des deux n’a bougé, que les cases entre eux sont libres et que le roi n’est pas en échec ni ne traverse une case attaquée.',
        ep: 'Prise en passant : un pion qui vient d’avancer de deux cases peut être pris par un pion adverse à côté de lui, comme s’il n’avait avancé que d’une.',
        promo: 'Promotion : un pion qui atteint la dernière rangée devient dame, tour, fou ou cavalier.',
        end_t: 'Échec, mat et pat',
        check: 'Échec : le roi est attaqué. Tu dois le mettre à l’abri.',
        mate: 'Échec et mat : le roi est en échec sans issue. Partie terminée !',
        stalemate: 'Pat : aucun coup légal, mais pas d’échec. Partie nulle.',
        draws_t: 'Parties nulles', draws: ['Pat', 'Pas assez de pièces pour mater (ex. roi contre roi)', 'La même position trois fois', '50 coups chacun sans prise ni coup de pion', 'Accord entre les joueurs'],
        clock_t: 'Pendule', clock: 'Avec une pendule, chaque joueur dispose du même temps pour toute la partie. La pendule de chacun démarre après son premier coup. Si ton temps s’écoule tu perds, sauf si l’adversaire n’a plus assez de pièces pour mater : c’est alors nulle.',
        online_t: 'Parties en ligne', online: ['Quitter une partie en cours compte comme un abandon.', 'Si ton adversaire reste hors ligne une minute, tu peux réclamer la victoire.', 'Tu peux proposer la nulle : l’adversaire accepte ou refuse. Jouer un coup revient à refuser.', 'Lors d’une revanche, les couleurs sont inversées.'],
        tips_t: 'Conseils et indices', tips: 'Avec les conseils activés, touche n’importe quelle pièce pour voir ce qu’elle peut faire. Les couleurs des repères :',
        legend: { safe: 'Case sûre', unsafe: 'Elle pourrait y être prise', capture: 'Prise', check: 'Donne échec', special: 'Coup spécial' },
        hint: 'Contre l’ordinateur et sur le même appareil, tu peux aussi demander un Indice (le coup conseillé apparaît sous forme de flèche) et Annuler des coups.',
        score_t: 'Score', score: 'Victoire = 1 point, nulle = ½ point chacun.',
    },
    de: {
        goal_t: 'Ziel', goal: 'Setze den gegnerischen König matt: Greife ihn so an, dass er nicht entkommen kann. Weiß beginnt immer, danach wird abwechselnd je ein Zug gemacht.',
        pieces_t: 'So ziehen die Figuren', pieces: 'Punkte zeigen, wohin die Figur ziehen kann; ein Ring bedeutet Schlagen.',
        special_t: 'Sonderzüge',
        castle: 'Rochade: Der König zieht zwei Felder in Richtung eines Turms, der auf die andere Seite springt. Nur wenn beide noch nicht gezogen haben, die Felder dazwischen frei sind und der König weder im Schach steht noch über ein angegriffenes Feld zieht.',
        ep: 'En passant: Ein Bauer, der gerade zwei Felder vorgerückt ist, kann von einem gegnerischen Bauern daneben geschlagen werden, als wäre er nur ein Feld gezogen.',
        promo: 'Umwandlung: Ein Bauer, der die letzte Reihe erreicht, wird zu Dame, Turm, Läufer oder Springer.',
        end_t: 'Schach, Matt und Patt',
        check: 'Schach: Der König wird angegriffen. Du musst ihn in Sicherheit bringen.',
        mate: 'Schachmatt: Der König steht im Schach und kann nicht entkommen. Partie vorbei!',
        stalemate: 'Patt: kein regelgerechter Zug, aber kein Schach. Remis.',
        draws_t: 'Remis', draws: ['Patt', 'Zu wenig Material für ein Matt (z. B. König gegen König)', 'Dreimal dieselbe Stellung', 'Je 50 Züge ohne Schlagen oder Bauernzug', 'Einigung der Spieler'],
        clock_t: 'Uhr', clock: 'Mit Uhr hat jeder Spieler gleich viel Zeit für die ganze Partie. Jede Uhr startet nach dem ersten Zug des Spielers. Läuft deine Zeit ab, verlierst du – außer dein Gegner kann nicht mehr mattsetzen: dann ist es Remis.',
        online_t: 'Online-Partien', online: ['Eine laufende Partie zu verlassen zählt als Aufgabe.', 'Ist dein Gegner eine Minute offline, kannst du den Sieg beanspruchen.', 'Du kannst Remis anbieten: Dein Gegner nimmt an oder lehnt ab. Ein Zug gilt als Ablehnung.', 'Bei einer Revanche werden die Farben getauscht.'],
        tips_t: 'Tipps und Hinweise', tips: 'Mit eingeschalteten Tipps tippst du eine beliebige Figur an und siehst, was sie kann. Die Farben der Markierungen:',
        legend: { safe: 'Sicheres Feld', unsafe: 'Dort könnte sie geschlagen werden', capture: 'Schlagen', check: 'Gibt Schach', special: 'Sonderzug' },
        hint: 'Gegen den Computer und an einem Gerät kannst du außerdem um einen Hinweis bitten (der empfohlene Zug erscheint als Pfeil) und Züge zurücknehmen.',
        score_t: 'Wertung', score: 'Sieg = 1 Punkt, Remis = je ½ Punkt.',
    },
};

// Small board from a FEN; moves of the piece on `from` are marked like on the real board.
function ruleDiagram(fen, from, caption, opts = {}) {
    const state = Chess.parseFEN(fen);
    const targets = new Map();
    if (from) {
        const a = Tips.analyze(state, Chess.sqIndex(from));
        a.targets.forEach(x => targets.set(x.to, x));
    }
    const marked = new Set((opts.highlight || []).map(Chess.sqIndex));
    const checkSq = opts.check ? Chess.sqIndex(opts.check) : -1;
    let html = '';
    for (let sq = 0; sq < 64; sq++) {
        const cls = ['sq', (Chess.row(sq) + Chess.col(sq)) % 2 === 0 ? 'light' : 'dark'];
        if (from && sq === Chess.sqIndex(from)) cls.push('sel');
        if (marked.has(sq)) cls.push('last');
        if (sq === checkSq) cls.push('check');
        const x = targets.get(sq);
        let mark = '';
        if (x) {
            cls.push(x.captured ? 'capture' : 'safe');
            if (x.special) cls.push('special');
            mark = '<span class="mark"></span>';
        }
        const p = state.board[sq];
        html += `<div class="${cls.join(' ')}">${p ? PIECE_SVG.html(p) : ''}${mark}</div>`;
    }
    return `<figure class="rule-diagram"><div class="mini-board${opts.tips ? ' tips-on' : ''}">${html}</div><figcaption>${escapeHtml(caption)}</figcaption></figure>`;
}

function renderRules() {
    const r = RULES_TEXT[currentLang] || RULES_TEXT.en;
    const pieceBoards = [
        ['k7/8/8/8/3K4/8/8/8 w - - 0 1', 'd4', 'k'],
        ['k7/8/8/8/3Q4/8/8/7K w - - 0 1', 'd4', 'q'],
        ['k7/8/8/8/3R4/8/8/7K w - - 0 1', 'd4', 'r'],
        ['k7/8/8/8/3B4/8/8/7K w - - 0 1', 'd4', 'b'],
        ['k7/8/8/8/3N4/8/8/7K w - - 0 1', 'd4', 'n'],
        ['k7/8/8/8/8/3p1n2/4P3/7K w - - 0 1', 'e2', 'p'],
    ].map(([fen, from, type]) => ruleDiagram(fen, from, `${t('piece_' + type)}: ${t('how_' + type)}`)).join('');
    const li = (items) => `<ul class="rule-list">${items.map(i => `<li>${escapeHtml(i)}</li>`).join('')}</ul>`;
    const legend = Object.entries(r.legend).map(([k, v]) => `<span><i class="ti ${k}"></i>${escapeHtml(v)}</span>`).join('');

    $('rules-body').innerHTML = `
        <section><h4>${escapeHtml(r.goal_t)}</h4><p>${escapeHtml(r.goal)}</p></section>
        <section><h4>${escapeHtml(r.pieces_t)}</h4><p>${escapeHtml(r.pieces)}</p><div class="rule-diagrams">${pieceBoards}</div></section>
        <section><h4>${escapeHtml(r.special_t)}</h4><div class="rule-diagrams">
            ${ruleDiagram('r3k2r/8/8/8/8/8/8/R3K2R w KQkq - 0 1', 'e1', r.castle, { tips: true })}
            ${ruleDiagram('k7/8/8/3pP3/8/8/8/7K w - d6 0 1', 'e5', r.ep, { tips: true, highlight: ['d7', 'd5'] })}
            ${ruleDiagram('k7/4P3/8/8/8/8/8/7K w - - 0 1', 'e7', r.promo, { tips: true })}
        </div></section>
        <section><h4>${escapeHtml(r.end_t)}</h4><div class="rule-diagrams">
            ${ruleDiagram('4k3/8/8/8/8/8/8/4R1K1 b - - 0 1', null, r.check, { check: 'e8', highlight: ['e1'] })}
            ${ruleDiagram('R5k1/5ppp/8/8/8/8/8/6K1 b - - 0 1', null, r.mate, { check: 'g8', highlight: ['a8'] })}
            ${ruleDiagram('k7/2Q5/1K6/8/8/8/8/8 b - - 0 1', null, r.stalemate, { highlight: ['a8'] })}
        </div></section>
        <section><h4>${escapeHtml(r.draws_t)}</h4>${li(r.draws)}</section>
        <section><h4>${escapeHtml(r.clock_t)}</h4><p>${escapeHtml(r.clock)}</p></section>
        <section><h4>${escapeHtml(r.online_t)}</h4>${li(r.online)}</section>
        <section><h4>${escapeHtml(r.tips_t)}</h4><p>${escapeHtml(r.tips)}</p><div class="legend">${legend}</div><p>${escapeHtml(r.hint)}</p></section>
        <section><h4>${escapeHtml(r.score_t)}</h4><p>${escapeHtml(r.score)}</p></section>`;
}
