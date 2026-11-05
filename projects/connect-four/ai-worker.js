// Runs computer calculation off the main thread so animations and gestures never lag.
importScripts('engine.js', 'ai.js');

onmessage = (e) => {
    const { id, board, player, level } = e.data;
    const col = ConnectFourAI.bestMove(board, player, level);
    postMessage({ id, col });
};
