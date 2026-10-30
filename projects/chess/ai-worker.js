// Runs the computer's search off the main thread so the board stays responsive.
importScripts('engine.js', 'ai.js');

onmessage = (e) => {
    const { id, state, level } = e.data;
    postMessage({ id, uci: ChessAI.bestMove(state, level) });
};
