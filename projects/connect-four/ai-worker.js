// Runs computer calculation off the main thread so animations and gestures never lag.
importScripts('engine.js' + self.location.search, 'ai.js' + self.location.search);

onmessage = (e) => {
    const {id, board, player, level} = e.data;
    const col = ConnectFourAI.bestMove(board, player, level);
    postMessage({id, col});
};
