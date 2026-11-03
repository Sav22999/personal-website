const releases = [
    {
        version: '1.0',
        date: '2024',
        notes: [
            'Initial release of Sudoku game',
            'Three difficulty levels: Easy, Medium, Hard',
            'Check function to verify solutions',
            'Hint system to help with tricky puzzles',
            'Undo functionality',
            'Support for multiple languages: English, Italian, Spanish, French, German'
        ]
    }
];

function formatReleases() {
    const lang = window.SudokuI18n?.getLang() || 'en';
    const t = window.SudokuI18n?.translations[lang] || window.SudokuI18n?.translations.en || {};
    
    return releases.map(release => {
        const formattedDate = new Date(release.date).toLocaleDateString(lang === 'en' ? 'en-US' : lang, {
            year: 'numeric',
            month: 'short'
        });
        
        return {
            version: release.version,
            date: formattedDate,
            notes: release.notes.map(note => {
                // Simple translation mapping for release notes
                const translations = {
                    en: note,
                    it: note.replace('Sudoku', 'Sudoku').replace('Easy, Medium, Hard', 'Facile, Media, Difficile'),
                    es: note.replace('Sudoku', 'Sudoku').replace('Easy, Medium, Hard', 'Fácil, Media, Difícil'),
                    fr: note.replace('Sudoku', 'Sudoku').replace('Easy, Medium, Hard', 'Facile, Moyenne, Difficile'),
                    de: note.replace('Sudoku', 'Sudoku').replace('Easy, Medium, Hard', 'Leicht, Mittel, Schwer')
                };
                return translations[lang] || translations.en || note;
            })
        };
    });
}

function renderReleases() {
    const container = document.getElementById('release-list');
    if (!container) return;
    
    const formatted = formatReleases();
    let html = '';
    
    formatted.forEach(release => {
        html += `
            <div class="release-item">
                <div class="release-version">Version ${release.version}</div>
                <div class="release-date">${release.date}</div>
                <ul class="release-notes">
                    ${release.notes.map(note => `<li>${note}</li>`).join('')}
                </ul>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Auto-render when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderReleases);
} else {
    renderReleases();
}
