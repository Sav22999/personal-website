// ============================================
// Sudoku Game
// ============================================

// Game state
let gameState = {
    grid: [],           // Current grid state (9x9)
    solution: [],       // Solution grid (9x9)
    initial: [],        // Initial puzzle (9x9) - givens
    difficulty: 'medium',
    selectedCell: null, // {row, col}
    selectedNumber: null, // 1-9
    history: [],        // For undo functionality
    notes: [],          // Pencil marks (9x9, each is array of notes)
    playerName: '',
    errors: [],         // Track errors
    isChecking: false
};

// Difficulty settings
const DIFFICULTY_SETTINGS = {
    easy: { name: 'Easy', emptyCells: 35, hints: 54 },
    medium: { name: 'Medium', emptyCells: 45, hints: 45 },
    hard: { name: 'Hard', emptyCells: 55, hints: 35 }
};

// ============================================
// Initialization
// ============================================

function initGame() {
    // Load saved preferences
    loadPreferences();
    
    // Set up event listeners
    setupEventListeners();
    
    // Show lobby first
    showScreen('lobby');
    
    // Update version
    updateVersion();
}

function loadPreferences() {
    const savedName = localStorage.getItem('sudoku_name');
    if (savedName) {
        document.getElementById('player-name')?.setAttribute('value', savedName);
    }
}

function saveName(name) {
    localStorage.setItem('sudoku_name', name);
}

function updateVersion() {
    const versionEl = document.getElementById('app-version');
    if (versionEl) {
        versionEl.textContent = 'v1';
    }
}

// ============================================
// Event Listeners
// ============================================

function setupEventListeners() {
    // Language menu
    document.getElementById('btn-lang-menu')?.addEventListener('click', () => {
        showOverlay('lang-overlay');
    });
    
    // Language selector buttons
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.dataset.lang;
            if (window.SudokuI18n?.setLang) {
                window.SudokuI18n.setLang(lang);
            }
        });
    });
    
    // Language overlay options
    document.querySelectorAll('.lang-option').forEach(option => {
        option.addEventListener('click', () => {
            const lang = option.dataset.lang;
            if (window.SudokuI18n?.setLang) {
                window.SudokuI18n.setLang(lang);
            }
            hideOverlay('lang-overlay');
        });
    });
    
    // Close overlays
    document.querySelectorAll('[id^="btn-close-"]').forEach(btn => {
        const overlayId = btn.id.replace('btn-close-', '') + '-overlay';
        btn.addEventListener('click', () => hideOverlay(overlayId));
    });
    
    // Name form
    const nameForm = document.getElementById('step-name');
    nameForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        const nameInput = document.getElementById('player-name');
        const name = nameInput?.value.trim();
        if (name && name.length > 0) {
            saveName(name);
            goToStep(2);
        }
    });
    
    // Name input validation
    const nameInput = document.getElementById('player-name');
    const continueBtn = document.getElementById('btn-continue');
    nameInput?.addEventListener('input', () => {
        const name = nameInput.value.trim();
        if (continueBtn) {
            continueBtn.disabled = name.length === 0;
        }
    });
    
    // Change name
    document.getElementById('btn-change-name')?.addEventListener('click', () => {
        goToStep(1);
    });
    
    // Start AI game
    document.getElementById('btn-start-ai')?.addEventListener('click', () => {
        const difficulty = document.querySelector('input[name="ai-level"]:checked')?.value || 'medium';
        startNewGame(difficulty);
    });
    
    // How to play
    document.getElementById('btn-howto')?.addEventListener('click', () => {
        showHowTo();
    });
    
    // Open rules
    document.querySelectorAll('[data-open-rules]').forEach(btn => {
        btn.addEventListener('click', () => showOverlay('rules-overlay'));
    });
    
    // Open release notes
    document.getElementById('btn-version')?.addEventListener('click', () => {
        showOverlay('release-overlay');
    });
    
    // Leave game
    document.getElementById('btn-leave-game')?.addEventListener('click', () => {
        showConfirm('Leave the game?', 'The current game will be lost.', () => {
            hideOverlay('confirm-overlay');
            showScreen('lobby');
        });
    });
    
    // New game button
    document.getElementById('btn-new-game')?.addEventListener('click', () => {
        const difficulty = gameState.difficulty || 'medium';
        startNewGame(difficulty);
    });
    
    // Game over new game
    document.getElementById('btn-new-game-over')?.addEventListener('click', () => {
        hideOverlay('gameover-overlay');
        const difficulty = gameState.difficulty || 'medium';
        startNewGame(difficulty);
    });
    
    // Game over back home
    document.getElementById('btn-gameover-home')?.addEventListener('click', () => {
        hideOverlay('gameover-overlay');
        showScreen('lobby');
    });
    
    // Confirm overlay buttons
    document.getElementById('btn-confirm-no')?.addEventListener('click', () => {
        hideOverlay('confirm-overlay');
    });
    
    // Check button
    document.getElementById('btn-check')?.addEventListener('click', () => {
        checkSolution();
    });
    
    // Hint button
    document.getElementById('btn-hint')?.addEventListener('click', () => {
        provideHint();
    });
    
    // Undo button
    document.getElementById('btn-undo')?.addEventListener('click', () => {
        undoMove();
    });
}

// ============================================
// Navigation
// ============================================

function goToStep(step) {
    // Update step indicators
    document.querySelectorAll('.step').forEach((s, idx) => {
        s.classList.remove('active', 'completed');
        if (idx + 1 < step) s.classList.add('completed');
        if (idx + 1 === step) s.classList.add('active');
    });
    
    // Show the step
    document.querySelectorAll('.lobby-step').forEach(s => {
        s.classList.remove('active');
    });
    
    const stepElement = step === 1 ? document.getElementById('step-name') : document.getElementById('step-mode');
    stepElement?.classList.add('active');
    
    // Update greeting if step 2
    if (step === 2) {
        const name = localStorage.getItem('sudoku_name') || 'there';
        const greetingEl = document.getElementById('greeting-text');
        if (greetingEl) {
            const lang = window.SudokuI18n?.getLang() || 'en';
            const t = window.SudokuI18n?.translations[lang] || {};
            greetingEl.textContent = (t.greeting || 'Hi, {name}!').replace('{name}', name);
        }
    }
}

function showScreen(screenId) {
    document.querySelectorAll('.screen').forEach(screen => {
        screen.classList.remove('active');
    });
    document.getElementById(screenId)?.classList.add('active');
}

function showOverlay(overlayId) {
    document.getElementById(overlayId)?.classList.remove('hidden');
}

function hideOverlay(overlayId) {
    document.getElementById(overlayId)?.classList.add('hidden');
}

function showConfirm(title, text, onConfirm) {
    const titleEl = document.getElementById('confirm-title');
    const textEl = document.getElementById('confirm-text');
    const yesBtn = document.getElementById('btn-confirm-yes');
    
    if (titleEl) titleEl.textContent = title;
    if (textEl) textEl.textContent = text;
    if (yesBtn) {
        yesBtn.onclick = () => {
            onConfirm();
        };
    }
    
    showOverlay('confirm-overlay');
}

function showHowTo() {
    const container = document.getElementById('howto-sheet-body');
    if (!container) return;
    
    const lang = window.SudokuI18n?.getLang() || 'en';
    const t = window.SudokuI18n?.translations[lang] || {};
    
    container.innerHTML = `
        <ol class="howto-list" id="howto-list">
            <li>
                <span class="howto-num">1</span>
                <div>
                    <strong>${t.howto1_title || 'Fill the grid'}</strong>
                    <p>${t.howto1_text || 'Each row, column, and 3x3 box must contain all numbers from 1 to 9.'}</p>
                </div>
            </li>
            <li>
                <span class="howto-num">2</span>
                <div>
                    <strong>${t.howto2_title || 'No repeats'}</strong>
                    <p>${t.howto2_text || 'A number cannot appear twice in the same row, column, or 3x3 box.'}</p>
                </div>
            </li>
            <li>
                <span class="howto-num">3</span>
                <div>
                    <strong>${t.howto3_title || 'Use logic'}</strong>
                    <p>${t.howto3_text || 'Use the given numbers to deduce the missing ones. Try different difficulties!'}</p>
                </div>
            </li>
        </ol>
    `;
    
    showOverlay('howto-overlay');
}

// ============================================
// Sudoku Generator
// ============================================

function generateSudoku(difficulty) {
    // Start with an empty grid
    const grid = createEmptyGrid();
    
    // Fill the diagonal boxes (which are independent)
    fillDiagonalBoxes(grid);
    
    // Solve the grid completely
    solveSudoku(grid);
    
    // Create the puzzle by removing cells based on difficulty
    const puzzle = createPuzzle(grid, difficulty);
    
    return {
        puzzle: puzzle,
        solution: grid
    };
}

function createEmptyGrid() {
    return Array(9).fill().map(() => Array(9).fill(0));
}

function fillDiagonalBoxes(grid) {
    for (let i = 0; i < 9; i += 3) {
        fillBox(grid, i, i);
    }
}

function fillBox(grid, row, col) {
    const numbers = shuffle([1, 2, 3, 4, 5, 6, 7, 8, 9]);
    for (let i = 0; i < 3; i++) {
        for (let j = 0; j < 3; j++) {
            grid[row + i][col + j] = numbers[i * 3 + j];
        }
    }
}

function solveSudoku(grid) {
    for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
            if (grid[row][col] === 0) {
                for (let num = 1; num <= 9; num++) {
                    if (isValidPlacement(grid, row, col, num)) {
                        grid[row][col] = num;
                        if (solveSudoku(grid)) {
                            return true;
                        }
                        grid[row][col] = 0;
                    }
                }
                return false;
            }
        }
    }
    return true;
}

function isValidPlacement(grid, row, col, num) {
    // Check row
    for (let c = 0; c < 9; c++) {
        if (grid[row][c] === num) return false;
    }
    
    // Check column
    for (let r = 0; r < 9; r++) {
        if (grid[r][col] === num) return false;
    }
    
    // Check 3x3 box
    const boxRow = Math.floor(row / 3) * 3;
    const boxCol = Math.floor(col / 3) * 3;
    for (let r = 0; r < 3; r++) {
        for (let c = 0; c < 3; c++) {
            if (grid[boxRow + r][boxCol + c] === num) return false;
        }
    }
    
    return true;
}

function createPuzzle(solution, difficulty) {
    const puzzle = JSON.parse(JSON.stringify(solution));
    const settings = DIFFICULTY_SETTINGS[difficulty] || DIFFICULTY_SETTINGS.medium;
    const cellsToRemove = settings.emptyCells;
    
    // Create a list of all cells
    const cells = [];
    for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
            cells.push({ row, col });
        }
    }
    
    // Shuffle cells
    shuffle(cells);
    
    // Remove cells
    let removed = 0;
    for (const cell of cells) {
        if (removed >= cellsToRemove) break;
        
        const backup = puzzle[cell.row][cell.col];
        puzzle[cell.row][cell.col] = 0;
        
        // Make sure the puzzle still has a unique solution
        if (!hasUniqueSolution(puzzle)) {
            puzzle[cell.row][cell.col] = backup;
        } else {
            removed++;
        }
    }
    
    return puzzle;
}

function hasUniqueSolution(puzzle) {
    const gridCopy = JSON.parse(JSON.stringify(puzzle));
    let solutionCount = 0;
    
    function countSolutions(grid) {
        for (let row = 0; row < 9; row++) {
            for (let col = 0; col < 9; col++) {
                if (grid[row][col] === 0) {
                    for (let num = 1; num <= 9; num++) {
                        if (isValidPlacement(grid, row, col, num)) {
                            grid[row][col] = num;
                            countSolutions(grid);
                            grid[row][col] = 0;
                        }
                    }
                    return;
                }
            }
        }
        solutionCount++;
    }
    
    countSolutions(gridCopy);
    return solutionCount === 1;
}

function shuffle(array) {
    const arr = [...array];
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
}

// ============================================
// Game Logic
// ============================================

function startNewGame(difficulty) {
    gameState.difficulty = difficulty;
    gameState.isChecking = false;
    gameState.errors = [];
    
    // Generate puzzle
    const sudoku = generateSudoku(difficulty);
    gameState.grid = JSON.parse(JSON.stringify(sudoku.puzzle));
    gameState.solution = JSON.parse(JSON.stringify(sudoku.solution));
    gameState.initial = JSON.parse(JSON.stringify(sudoku.puzzle));
    gameState.history = [];
    gameState.notes = createEmptyNotes();
    gameState.selectedCell = null;
    gameState.selectedNumber = null;
    
    // Show game screen
    showScreen('game');
    
    // Update difficulty badge
    updateDifficultyBadge();
    
    // Render board
    renderBoard();
    
    // Show toast
    showToast('New game started!');
}

function createEmptyNotes() {
    return Array(9).fill().map(() => 
        Array(9).fill().map(() => [])
    );
}

function updateDifficultyBadge() {
    const badge = document.getElementById('difficulty-badge');
    const modeBadge = document.getElementById('mode-badge');
    
    if (badge) {
        const lang = window.SudokuI18n?.getLang() || 'en';
        const t = window.SudokuI18n?.translations[lang] || {};
        const difficultyNames = {
            easy: t.level_easy || 'Easy',
            medium: t.level_medium || 'Medium',
            hard: t.level_hard || 'Hard'
        };
        badge.textContent = difficultyNames[gameState.difficulty] || gameState.difficulty;
    }
    
    if (modeBadge) {
        modeBadge.textContent = 'Computer';
    }
}

function renderBoard() {
    const container = document.getElementById('sudoku-board');
    if (!container) return;
    
    container.innerHTML = '';
    
    for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
            const cell = document.createElement('div');
            cell.className = 'sudoku-cell';
            cell.dataset.row = row;
            cell.dataset.col = col;
            
            // Check if it's a given cell
            if (gameState.initial[row][col] !== 0) {
                cell.classList.add('given');
                cell.textContent = gameState.initial[row][col];
            } else if (gameState.grid[row][col] !== 0) {
                cell.classList.add('user');
                cell.textContent = gameState.grid[row][col];
            }
            
            // Check for errors
            if (gameState.errors.some(e => e.row === row && e.col === col)) {
                cell.classList.add('error');
            }
            
            // Check if selected
            if (gameState.selectedCell && gameState.selectedCell.row === row && gameState.selectedCell.col === col) {
                cell.classList.add('selected');
            }
            
            // Highlight same number
            if (gameState.selectedNumber && gameState.grid[row][col] === gameState.selectedNumber) {
                cell.classList.add('same-number');
            }
            
            // Add notes
            if (gameState.notes[row][col].length > 0) {
                cell.classList.add('has-note');
                const notesEl = document.createElement('div');
                notesEl.className = 'notes';
                for (let num = 1; num <= 9; num++) {
                    const noteEl = document.createElement('span');
                    noteEl.textContent = gameState.notes[row][col].includes(num) ? num : '';
                    notesEl.appendChild(noteEl);
                }
                cell.appendChild(notesEl);
            }
            
            // Click handler
            cell.addEventListener('click', () => handleCellClick(row, col));
            
            container.appendChild(cell);
        }
    }
}

function handleCellClick(row, col) {
    // If cell is given, ignore
    if (gameState.initial[row][col] !== 0) return;
    
    const prevSelected = gameState.selectedCell;
    
    // If clicking the same cell, deselect
    if (prevSelected && prevSelected.row === row && prevSelected.col === col) {
        gameState.selectedCell = null;
        gameState.selectedNumber = null;
    } else {
        gameState.selectedCell = { row, col };
        gameState.selectedNumber = gameState.grid[row][col] || null;
    }
    
    renderBoard();
    
    // Show number input
    if (gameState.selectedCell) {
        showNumberInput();
    } else {
        hideNumberInput();
    }
}

function showNumberInput() {
    const overlay = document.createElement('div');
    overlay.className = 'number-input-overlay';
    overlay.innerHTML = `
        <div class="number-input-panel">
            <h3>Select a number</h3>
            <div class="number-grid" id="number-grid"></div>
            <div class="number-actions">
                <button class="btn btn-ghost" id="btn-note-toggle">Toggle Notes</button>
                <button class="btn btn-primary" id="btn-clear-cell">Clear</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Populate number grid
    const grid = overlay.querySelector('.number-grid');
    for (let num = 1; num <= 9; num++) {
        const btn = document.createElement('button');
        btn.className = 'number-btn';
        btn.textContent = num;
        btn.dataset.num = num;
        
        // Highlight if this is the current number
        if (gameState.selectedNumber === num) {
            btn.classList.add('selected');
        }
        
        // Check if number is in notes
        const { row, col } = gameState.selectedCell;
        if (gameState.notes[row][col].includes(num)) {
            btn.classList.add('in-notes');
        }
        
        btn.addEventListener('click', () => {
            selectNumber(num);
            hideNumberInput();
        });
        
        grid.appendChild(btn);
    }
    
    // Toggle notes button
    overlay.querySelector('#btn-note-toggle').addEventListener('click', () => {
        toggleNoteMode();
        hideNumberInput();
    });
    
    // Clear button
    overlay.querySelector('#btn-clear-cell').addEventListener('click', () => {
        clearCell();
        hideNumberInput();
    });
    
    setTimeout(() => overlay.classList.add('show'), 10);
}

function hideNumberInput() {
    const overlay = document.querySelector('.number-input-overlay');
    if (overlay) {
        overlay.classList.remove('show');
        setTimeout(() => overlay.remove(), 250);
    }
}

let noteMode = false;

function toggleNoteMode() {
    noteMode = !noteMode;
    showToast(noteMode ? 'Note mode: ON' : 'Note mode: OFF');
}

function selectNumber(num) {
    const { row, col } = gameState.selectedCell;
    if (!row || !col) return;
    
    // Save to history
    saveToHistory(row, col);
    
    if (noteMode) {
        // Toggle note
        const notes = gameState.notes[row][col];
        const index = notes.indexOf(num);
        if (index > -1) {
            notes.splice(index, 1);
        } else {
            notes.push(num);
            notes.sort((a, b) => a - b);
        }
        showToast(`Added note: ${num}`);
    } else {
        // Place number
        gameState.grid[row][col] = num;
        gameState.selectedNumber = num;
        showToast(`Placed: ${num}`);
        
        // Check if complete
        checkCompletion();
    }
    
    renderBoard();
}

function clearCell() {
    const { row, col } = gameState.selectedCell;
    if (!row || !col) return;
    
    // Save to history
    saveToHistory(row, col);
    
    if (noteMode) {
        gameState.notes[row][col] = [];
        showToast('Notes cleared');
    } else {
        gameState.grid[row][col] = 0;
        gameState.selectedNumber = null;
        showToast('Cell cleared');
    }
    
    renderBoard();
}

function saveToHistory(row, col) {
    gameState.history.push({
        row,
        col,
        value: gameState.grid[row][col],
        notes: [...gameState.notes[row][col]]
    });
    
    // Limit history to 50 moves
    if (gameState.history.length > 50) {
        gameState.history.shift();
    }
}

function undoMove() {
    if (gameState.history.length === 0) {
        showToast('Nothing to undo');
        return;
    }
    
    const lastMove = gameState.history.pop();
    gameState.grid[lastMove.row][lastMove.col] = lastMove.value;
    gameState.notes[lastMove.row][lastMove.col] = lastMove.notes;
    
    // Update selected cell if it was the one we undid
    if (gameState.selectedCell && 
        gameState.selectedCell.row === lastMove.row && 
        gameState.selectedCell.col === lastMove.col) {
        gameState.selectedNumber = lastMove.value;
    }
    
    renderBoard();
    showToast('Move undone');
}

// ============================================
// Game Features
// ============================================

function checkSolution() {
    gameState.isChecking = true;
    let hasErrors = false;
    gameState.errors = [];
    
    // Check all cells
    for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
            const value = gameState.grid[row][col];
            if (value === 0) continue; // Empty cell
            
            if (value !== gameState.solution[row][col]) {
                gameState.errors.push({ row, col });
                hasErrors = true;
            }
        }
    }
    
    renderBoard();
    
    if (!hasErrors) {
        showToast('Correct! Well done!', 'success');
        // Check completion
        checkCompletion();
    } else {
        showToast(`${gameState.errors.length} error(s) found`, 'error');
    }
    
    gameState.isChecking = false;
}

function checkCompletion() {
    for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
            if (gameState.grid[row][col] !== gameState.solution[row][col]) {
                return; // Not complete
            }
        }
    }
    
    // Game is complete!
    showGameOver(true);
}

function provideHint() {
    // Find first empty cell
    for (let row = 0; row < 9; row++) {
        for (let col = 0; col < 9; col++) {
            if (gameState.grid[row][col] === 0 && gameState.initial[row][col] === 0) {
                // Found empty cell, reveal the solution
                gameState.grid[row][col] = gameState.solution[row][col];
                gameState.selectedCell = { row, col };
                gameState.selectedNumber = gameState.grid[row][col];
                
                // Save to history
                saveToHistory(row, col);
                
                renderBoard();
                showToast(`Hint: ${gameState.solution[row][col]}`);
                return;
            }
        }
    }
    
    // No empty cells found
    showToast('No hints available');
}

// ============================================
// Game Over
// ============================================

function showGameOver(isWin) {
    const icon = document.getElementById('gameover-icon');
    const title = document.getElementById('gameover-title');
    const text = document.getElementById('gameover-text');
    
    const lang = window.SudokuI18n?.getLang() || 'en';
    const t = window.SudokuI18n?.translations[lang] || {};
    
    if (icon) {
        icon.className = isWin ? 'win' : 'lose';
        icon.innerHTML = isWin ? '<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg>' : 
                                    '<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12"/></svg>';
    }
    
    if (title) title.textContent = isWin ? (t.you_win || 'You solved it!') : (t.you_lose || 'Game over');
    if (text) {
        const difficulty = DIFFICULTY_SETTINGS[gameState.difficulty]?.name || gameState.difficulty;
        text.textContent = isWin ? `You solved the ${difficulty.toLowerCase()} puzzle!` : 'The game is over.';
    }
    
    showOverlay('gameover-overlay');
}

// ============================================
// Toast Notifications
// ============================================

function showToast(message, type = '') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    toast.textContent = message;
    toast.className = 'toast';
    if (type) {
        toast.classList.add(type);
    }
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

// ============================================
// Keyboard Support
// ============================================

document.addEventListener('keydown', (e) => {
    // Only handle if we're in the game screen
    if (!document.getElementById('screen-game')?.classList.contains('active')) return;
    
    // Number keys 1-9
    if (e.key >= '1' && e.key <= '9') {
        const num = parseInt(e.key);
        if (gameState.selectedCell) {
            selectNumber(num);
            e.preventDefault();
        }
    }
    
    // Backspace or Delete
    if (e.key === 'Backspace' || e.key === 'Delete') {
        if (gameState.selectedCell) {
            clearCell();
            e.preventDefault();
        }
    }
    
    // Escape
    if (e.key === 'Escape') {
        if (gameState.selectedCell) {
            gameState.selectedCell = null;
            gameState.selectedNumber = null;
            renderBoard();
            e.preventDefault();
        }
        hideNumberInput();
    }
    
    // Arrow keys for navigation
    if (e.key.startsWith('Arrow')) {
        const { row, col } = gameState.selectedCell || { row: 0, col: 0 };
        let newRow = row;
        let newCol = col;
        
        switch (e.key) {
            case 'ArrowUp': newRow = Math.max(0, row - 1); break;
            case 'ArrowDown': newRow = Math.min(8, row + 1); break;
            case 'ArrowLeft': newCol = Math.max(0, col - 1); break;
            case 'ArrowRight': newCol = Math.min(8, col + 1); break;
        }
        
        // Only select if it's not a given cell
        if (gameState.initial[newRow][newCol] === 0) {
            gameState.selectedCell = { row: newRow, col: newCol };
            gameState.selectedNumber = gameState.grid[newRow][newCol] || null;
            renderBoard();
            e.preventDefault();
        }
    }
});

// ============================================
// Initialize on load
// ============================================

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGame);
} else {
    initGame();
}
