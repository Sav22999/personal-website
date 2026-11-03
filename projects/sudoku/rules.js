const rules = {
    en: {
        title: 'Sudoku Rules',
        content: `
            <p>Sudoku is played on a 9x9 grid divided into nine 3x3 subgrids (also called boxes or regions).</p>
            
            <h4>The Objective</h4>
            <p>Fill the grid so that each row, each column, and each of the nine 3x3 subgrids contains all of the digits from 1 to 9.</p>
            
            <h4>The Rules</h4>
            <ul class="rule-list">
                <li>Each row must contain all numbers from 1 to 9 without repetition</li>
                <li>Each column must contain all numbers from 1 to 9 without repetition</li>
                <li>Each 3x3 subgrid must contain all numbers from 1 to 9 without repetition</li>
                <li>The puzzle starts with some numbers already filled in (the "givens")</li>
                <li>Use logic and deduction to fill in the remaining empty cells</li>
            </ul>
            
            <h4>Tips for Beginners</h4>
            <ul class="rule-list">
                <li>Start by looking for rows, columns, or boxes with the most filled numbers</li>
                <li>Use the process of elimination to figure out which numbers can go where</li>
                <li>Look for "naked singles" - cells where only one number is possible</li>
                <li>As you fill in more numbers, more possibilities will reveal themselves</li>
            </ul>
        `
    },
    it: {
        title: 'Regole del Sudoku',
        content: `
            <p>Il Sudoku si gioca su una griglia 9x9 divisa in nove sottomatrici 3x3 (chiamate anche box o regioni).</p>
            
            <h4>L'Obiettivo</h4>
            <p>Riempire la griglia in modo che ogni riga, ogni colonna e ogni sottomatrice 3x3 contenga tutti i numeri da 1 a 9.</p>
            
            <h4>Le Regole</h4>
            <ul class="rule-list">
                <li>Ogni riga deve contenere tutti i numeri da 1 a 9 senza ripetizioni</li>
                <li>Ogni colonna deve contenere tutti i numeri da 1 a 9 senza ripetizioni</li>
                <li>Ogni sottomatrice 3x3 deve contenere tutti i numeri da 1 a 9 senza ripetizioni</li>
                <li>Il puzzle parte con alcuni numeri già inseriti (i "dati")</li>
                <li>Usa la logica e la deduzione per riempire le celle vuote rimanenti</li>
            </ul>
            
            <h4>Consigli per i Principianti</h4>
            <ul class="rule-list">
                <li>Inizia cercando righe, colonne o box con più numeri già inseriti</li>
                <li>Usa il processo di eliminazione per capire quali numeri possono andare dove</li>
                <li>Cerca i "singoli nudi" - celle in cui è possibile un solo numero</li>
                <li>Man mano che inserisci più numeri, si sbloccheranno più possibilità</li>
            </ul>
        `
    },
    es: {
        title: 'Reglas del Sudoku',
        content: `
            <p>El Sudoku se juega en una cuadrícula de 9x9 dividida en nueve subcuadrículas de 3x3 (también llamadas cajas o regiones).</p>
            
            <h4>El Objetivo</h4>
            <p>Llenar la cuadrícula para que cada fila, columna y subcuadrícula de 3x3 contenga todos los dígitos del 1 al 9.</p>
            
            <h4>Las Reglas</h4>
            <ul class="rule-list">
                <li>Cada fila debe contener todos los números del 1 al 9 sin repetición</li>
                <li>Cada columna debe contener todos los números del 1 al 9 sin repetición</li>
                <li>Cada subcuadrícula de 3x3 debe contener todos los números del 1 al 9 sin repetición</li>
                <li>El rompecabezas comienza con algunos números ya llenos (los "dados")</li>
                <li>Usa la lógica y la deducción para completar las celdas vacías restantes</li>
            </ul>
            
            <h4>Consejos para Principiantes</h4>
            <ul class="rule-list">
                <li>Comienza buscando filas, columnas o cajas con más números completos</li>
                <li>Usa el proceso de eliminación para determinar qué números pueden ir donde</li>
                <li>Busca "simples desnudos" - celdas donde solo es posible un número</li>
                <li>A medida que llenes más números, más posibilidades se revelarán</li>
            </ul>
        `
    },
    fr: {
        title: 'Règles du Sudoku',
        content: `
            <p>Le Sudoku se joue sur une grille de 9x9 divisée en neuf sous-grilles de 3x3 (également appelées cases ou régions).</p>
            
            <h4>L'Objectif</h4>
            <p>Remplir la grille de sorte que chaque ligne, colonne et sous-grille de 3x3 contienne tous les chiffres de 1 à 9.</p>
            
            <h4>Les Règles</h4>
            <ul class="rule-list">
                <li>Chaque ligne doit contenir tous les chiffres de 1 à 9 sans répétition</li>
                <li>Chaque colonne doit contenir tous les chiffres de 1 à 9 sans répétition</li>
                <li>Chaque sous-grille de 3x3 doit contenir tous les chiffres de 1 à 9 sans répétition</li>
                <li>Le puzzle commence avec certains chiffres déjà remplis (les "donnés")</li>
                <li>Utilisez la logique et la déduction pour remplir les cases vides restantes</li>
            </ul>
            
            <h4>Conseils pour Débutants</h4>
            <ul class="rule-list">
                <li>Commencez par chercher des lignes, colonnes ou cases avec le plus de chiffres remplis</li>
                <li>Utilisez le processus d'élimination pour déterminer quels chiffres peuvent aller où</li>
                <li>Cherchez les "simples nus" - des cases où un seul chiffre est possible</li>
                <li>Au fur et à mesure que vous remplissez plus de chiffres, plus de possibilités se révéleront</li>
            </ul>
        `
    },
    de: {
        title: 'Sudoku Regeln',
        content: `
            <p>Sudoku wird auf einem 9x9-Gitter gespielt, das in neun 3x3-Untergittern unterteilt ist (auch als Boxen oder Regionen bezeichnet).</p>
            
            <h4>Das Ziel</h4>
            <p>Füllen Sie das Gitter so, dass jede Zeile, jede Spalte und jedes der neun 3x3-Untergittern alle Ziffern von 1 bis 9 enthält.</p>
            
            <h4>Die Regeln</h4>
            <ul class="rule-list">
                <li>Jede Zeile muss alle Zahlen von 1 bis 9 ohne Wiederholung enthalten</li>
                <li>Jede Spalte muss alle Zahlen von 1 bis 9 ohne Wiederholung enthalten</li>
                <li>Jedes 3x3-Untergittern muss alle Zahlen von 1 bis 9 ohne Wiederholung enthalten</li>
                <li>Das Rätsel beginnt mit einigen bereits ausgefüllten Zahlen (die "Gegebenen")</li>
                <li>Nutzen Sie Logik und Deduktion, um die restlichen leeren Zellen zu füllen</li>
            </ul>
            
            <h4>Tipps für Anfänger</h4>
            <ul class="rule-list">
                <li>Beginnen Sie mit der Suche nach Zeilen, Spalten oder Boxen mit den meisten bereits ausgefüllten Zahlen</li>
                <li>Nutzen Sie den Ausschlussprozess, um herauszufinden, welche Zahlen wohin können</li>
                <li>Suchen Sie nach "Einzelne Nackte" - Zellen, in denen nur eine Zahl möglich ist</li>
                <li>Je mehr Zahlen Sie eintragen, desto mehr Möglichkeiten werden sichtbar</li>
            </ul>
        `
    }
};

function renderRules() {
    const container = document.getElementById('rules-body');
    if (!container) return;
    
    const lang = window.SudokuI18n?.getLang() || 'en';
    const ruleSet = rules[lang] || rules.en;
    
    container.innerHTML = ruleSet.content;
    document.getElementById('rules-title')?.textContent = ruleSet.title || 'Game Rules';
}

// Auto-render when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderRules);
} else {
    renderRules();
}
