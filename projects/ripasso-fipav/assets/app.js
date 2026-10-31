/* Fischietto (FIPAV referee rulebook review): portal (web version with accounts).
 * Questions and references come from the authenticated API (boot.js loads them into window.APP_CONTENT);
 * statistics, bookmarks and suspended quizzes are saved to the account through Store (api.js). */
// ======= DATA (from the API) =======
const REFS = window.APP_CONTENT.refs;
const QUESTIONS = window.APP_CONTENT.questions;
const REF_MAP = {};
REFS.forEach(r => REF_MAP[r['ref-code']] = r);
const SOURCES = ['Regole di gioco', 'Casistica', 'Indicazioni tecniche', 'Normative federali', 'Vademecum'];
const SOURCE_CLASS = { 'Regole di gioco': 'rg', 'Casistica': 'cas', 'Indicazioni tecniche': 'it', 'Normative federali': 'nf', 'Vademecum': 'vad' };
const SOURCE_SHORT = { 'Regole di gioco': 'RG', 'Casistica': 'CAS', 'Indicazioni tecniche': 'IT', 'Normative federali': 'NF', 'Vademecum': 'VAD' };
const APP_VERSION = {major: 3, minor: 0, patch: 0, build: 59};
const DIFFICULTIES = ['e', 'm', 'd', 'dd'];

// ======= RULEBOOK CHANGES =======
// The FIPAV season starts on 1 July: July 2026 to June 2027 is 2026/27.
function seasonOf(d) {
  const start = d.getMonth() >= 6 ? d.getFullYear() : d.getFullYear() - 1;
  return start + '/' + String((start + 1) % 100).padStart(2, '0');
}
const CURRENT_SEASON = seasonOf(new Date());
function refChange(code) { const r = REF_MAP[code]; return (r && r['ref-change']) || null; }
function isCurrentChange(code) { const c = refChange(code); return !!c && c.season === CURRENT_SEASON; }
function questionHasCurrentChange(q) { return q['question-ref'].some(isCurrentChange); }
function changeBadgeHtml(c) {
  if (!c) return '';
  const label = (c.type === 'new' ? 'Nuova' : 'Modificata') + ' ' + c.season;
  const tip = c.type === 'new' ? 'Regola introdotta nella stagione ' + c.season : 'Regola modificata nella stagione ' + c.season;
  return '<span class="change-badge ' + (c.season === CURRENT_SEASON ? 'current' : 'past') + '" title="' + escapeHtml(tip) + '">' + escapeHtml(label) + '</span>';
}
function changeNoteHtml(c) {
  return c && c.note ? '<div class="change-note"><strong>Cosa cambia:</strong> ' + escapeHtml(c.note) + '</div>' : '';
}
const DIFF_LABELS = { 'e': 'Facile', 'm': 'Media', 'd': 'Difficile', 'dd': 'Molto difficile' };
const DIFF_COLORS = { 'e': 'diff-e', 'm': 'diff-m', 'd': 'diff-d', 'dd': 'diff-dd' };
const SUSPENDED_KEY = 'fipav-suspended';
const AUTOSAVE_KEY = 'fipav-autosave';

// ======= TIMER =======
let quizTimerInterval = null;
let quizTimerStart = null; // Date.now() minus any prior elapsed

function formatTimer(ms) {
  const totalSec = Math.floor(ms / 1000);
  const min = Math.floor(totalSec / 60);
  const sec = totalSec % 60;
  return min + ':' + (sec < 10 ? '0' : '') + sec;
}

function startQuizTimer(elapsedMs) {
  stopQuizTimer();
  quizTimerStart = Date.now() - (elapsedMs || 0);
  updateTimerDisplay();
  quizTimerInterval = setInterval(updateTimerDisplay, 1000);
}

function getQuizElapsed() {
  return quizTimerStart ? Date.now() - quizTimerStart : 0;
}

function stopQuizTimer() {
  if (quizTimerInterval) { clearInterval(quizTimerInterval); quizTimerInterval = null; }
  return quizTimerStart ? Date.now() - quizTimerStart : 0;
}

function updateTimerDisplay() {
  const el = document.getElementById('quiz-timer-value');
  if (el && quizTimerStart) el.textContent = formatTimer(Date.now() - quizTimerStart);
}

// Flag helpers
function isQuestionFlagged(code) {
  return appData.bookmarks.flagged.some(f => f.code === code && !f.resolved);
}
function getFlagEntry(code) {
  return appData.bookmarks.flagged.find(f => f.code === code && !f.resolved);
}
function getOpenFlags() {
  return appData.bookmarks.flagged.filter(f => !f.resolved);
}
function getResolvedFlags() {
  return appData.bookmarks.flagged.filter(f => f.resolved);
}
function formatFlagDate(iso) {
  try {
    const d = new Date(iso);
    return d.toLocaleDateString('it-IT', {day:'2-digit',month:'2-digit',year:'numeric'}) + ' ' + d.toLocaleTimeString('it-IT', {hour:'2-digit',minute:'2-digit'});
  } catch(e) { return ''; }
}

// ======= PERSISTENCE =======
function loadData() {
  try {
    const raw = Store.getItem('fipav-data');
    if (raw) {
      const d = JSON.parse(raw);
      if (!d.bookmarks.flagged) d.bookmarks.flagged = [];
      // Migrate old string[] flagged to object[]
      if (d.bookmarks.flagged.length > 0 && typeof d.bookmarks.flagged[0] === 'string') {
        d.bookmarks.flagged = d.bookmarks.flagged.map(code => ({code: code, text: '', date: new Date().toISOString(), resolved: false, resolvedDate: null}));
      }
      // Migrate: add notes maps if missing
      if (!d.bookmarks.questionNotes) d.bookmarks.questionNotes = {};
      if (!d.bookmarks.refNotes) d.bookmarks.refNotes = {};
      // Migrate ref codes to Normative Federali / Vademecum 2026-27 numbering (single pass)
      if (d.refsVersion !== '2026-27') {
        const RN = {"NF-2.9": "NF-2.7", "NF-2.6": "NF-2.9", "NF-2.6.1": "NF-2.9.1", "NF-2.6.2": "NF-2.9.2", "NF-2.6.3": "NF-2.9.3", "NF-2.6.4": "NF-2.9.4", "NF-2.8": "NF-2.11", "VAD-2": "VAD-3", "VAD-4.1": "VAD-5.1", "VAD-4.2": "VAD-5.2", "VAD-4.3": "VAD-5.3", "VAD-4.5": "VAD-5.4", "VAD-4.6": "VAD-5.5", "VAD-4.7": "VAD-5.6", "VAD-4.8": "VAD-5.7", "VAD-4.9": "VAD-5.9", "VAD-4.11": "VAD-5.11", "VAD-5.1": "VAD-6.1", "VAD-5.2": "VAD-6.2", "VAD-5.3": "VAD-6.3", "VAD-5.4": "VAD-6.4", "VAD-5.5": "VAD-6.5", "VAD-5.6": "VAD-6.6", "VAD-8": "VAD-9", "VAD-10.2": "VAD-11.2", "VAD-11": "VAD-12", "VAD-12.2": "VAD-13.2", "NF-2.7": "NF-2.10", "VAD-3": "VAD-4", "VAD-4.4": "VAD-5.8", "VAD-4.4.1": "VAD-5.8.1", "VAD-4.4.2": "VAD-5.8", "VAD-4.4.3": "VAD-5.8", "VAD-4.10": "VAD-5.10", "VAD-6": "VAD-7", "VAD-7": "VAD-8", "VAD-9.1": "NF-4.1", "VAD-9.2": "NF-4.2", "VAD-10": "VAD-11", "VAD-10.1": "VAD-11.1", "VAD-12": "VAD-13", "VAD-12.1": "VAD-13.1", "VAD-12.3": "VAD-13.3"};
        const mv = c => RN[c] || c;
        const remapCounts = obj => { const o = {}; Object.keys(obj || {}).forEach(k => { const n = mv(k); o[n] = (o[n] || 0) + obj[k]; }); return o; };
        if (d.stats) { d.stats.refCorrect = remapCounts(d.stats.refCorrect); d.stats.refWrong = remapCounts(d.stats.refWrong); }
        d.bookmarks.refs = [...new Set((d.bookmarks.refs || []).map(mv))];
        const rn = {}; Object.keys(d.bookmarks.refNotes).forEach(k => { const n = mv(k); rn[n] = rn[n] ? rn[n] + '\n' + d.bookmarks.refNotes[k] : d.bookmarks.refNotes[k]; });
        d.bookmarks.refNotes = rn;
        d.refsVersion = '2026-27';
        try { Store.setItem('fipav-data', JSON.stringify(d)); } catch(e) {}
      }
      return d;
    }
  } catch(e) {}
  return { refsVersion: '2026-27', stats: { correct: 0, wrong: 0, refCorrect: {}, refWrong: {} }, bookmarks: { questions: [], refs: [], flagged: [], questionNotes: {}, refNotes: {} } };
}
function saveData() { try { Store.setItem('fipav-data', JSON.stringify(appData)); } catch(e) {} }
let appData = loadData();

// ======= THEME =======
function getSystemTheme() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}
function getSavedTheme() {
  try { return localStorage.getItem('fipav-theme') || 'system'; } catch(e) { return 'system'; }
}
function applyTheme(theme) {
  if (theme === 'system') document.documentElement.removeAttribute('data-theme');
  else document.documentElement.setAttribute('data-theme', theme);
  document.querySelectorAll('.theme-switch button').forEach(b => b.setAttribute('aria-checked', b.dataset.themeValue === theme ? 'true' : 'false'));
}
applyTheme(getSavedTheme());
document.querySelectorAll('.theme-switch button').forEach(b => b.addEventListener('click', () => {
  const v = b.dataset.themeValue;
  try { localStorage.setItem('fipav-theme', v); } catch(e) {}
  applyTheme(v);
}));

// ======= SETTINGS DROPDOWN =======
function setMenuOpen(open) {
  document.getElementById('settings-dropdown').hidden = !open;
  document.getElementById('menu-backdrop').hidden = !open;
  document.getElementById('btn-settings').setAttribute('aria-expanded', open ? 'true' : 'false');
}
document.getElementById('btn-settings').addEventListener('click', (e) => {
  e.stopPropagation();
  setMenuOpen(document.getElementById('settings-dropdown').hidden);
});
document.addEventListener('click', (e) => {
  if (!e.target.closest('.settings-dropdown-wrapper') && !e.target.closest('#settings-dropdown')) setMenuOpen(false);
});
document.querySelectorAll('#settings-dropdown a').forEach(a => a.addEventListener('click', () => setMenuOpen(false)));
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && !document.getElementById('settings-dropdown').hidden) { setMenuOpen(false); document.getElementById('btn-settings').focus(); }
});

// ======= TOAST =======
let toastTimer;
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.classList.remove('show'), 2200);
}

// ======= SECTIONS (addressable as #quiz, #ripasso, #documenti, #statistiche, #profilo, #novita) =======
const SECTION_HASH = { quiz: 'quiz', theory: 'ripasso', docs: 'documenti', stats: 'statistiche', profile: 'profilo', news: 'novita' };
const HASH_SECTION = { quiz: 'quiz', ripasso: 'theory', documenti: 'docs', statistiche: 'stats', profilo: 'profile', novita: 'news' };
let currentSection = 'quiz';

function showSection(tab, opts) {
  opts = opts || {};
  if (!SECTION_HASH[tab]) tab = 'quiz';
  const prev = currentSection;
  currentSection = tab;
  document.body.dataset.section = tab;
  document.querySelectorAll('.tab-btn').forEach(b => {
    const on = b.dataset.tab === tab;
    b.classList.toggle('active', on);
    if (on) b.setAttribute('aria-current', 'page'); else b.removeAttribute('aria-current');
  });
  document.querySelectorAll('.section').forEach(s => s.classList.toggle('active', s.id === 'sec-' + tab));
  if (!opts.fromHash && location.hash !== '#' + SECTION_HASH[tab]) history.pushState(null, '', '#' + SECTION_HASH[tab]);
  if (tab === 'theory') { renderTheory(); renderOverview(); }
  if (tab === 'stats') renderStats();
  if (tab === 'docs') renderDocuments();
  if (tab === 'news') renderReleases();
  if (tab === 'profile' && window.renderProfile) window.renderProfile();
  document.getElementById('btn-settings').classList.toggle('active', tab === 'profile');
  if (tab === 'quiz') { renderSuspendedSessions(); refreshQuizBookmarkState(); }
  // Leaving the quiz results goes back to the quiz setup
  if (tab !== 'quiz') {
    const quizResult = document.getElementById('quiz-result');
    if (quizResult && quizResult.style.display === 'block') resetQuiz();
  }
  if (prev !== tab && !opts.keepScroll) window.scrollTo(0, 0);
}

document.querySelectorAll('.tab-btn').forEach(btn => btn.addEventListener('click', () => showSection(btn.dataset.tab)));
window.addEventListener('popstate', () => showSection(HASH_SECTION[location.hash.slice(1)] || 'quiz', { fromHash: true }));

function setQuizRunning(on) { document.body.classList.toggle('quiz-running', !!on); }

// ======= SUB-TABS =======
function initSubTabs(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const buttons = container.querySelectorAll('.sub-tab');
  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const parentSection = container.closest('.section');
      parentSection.querySelectorAll(':scope > .sub-section').forEach(ss => ss.classList.remove('active'));
      const target = parentSection.querySelector('#sub-' + btn.dataset.sub);
      if (target) target.classList.add('active');
      if (btn.dataset.sub === 'quiz-main') refreshQuizBookmarkState();
      if (btn.dataset.sub === 'quiz-saved') renderSavedQuestions();
      if (btn.dataset.sub === 'quiz-flagged') renderFlaggedQuestions();
      if (btn.dataset.sub === 'theory-refs') renderTheory();
      if (btn.dataset.sub === 'theory-overview') renderOverview();
      if (btn.dataset.sub === 'theory-saved') renderTheorySavedRefs();
    });
  });
}

initSubTabs('quiz-sub-tabs');
initSubTabs('theory-sub-tabs');

// ======= QUIZ =======
let quizState = null;

function getQuestionSources(q) {
  const srcs = new Set();
  q['question-ref'].forEach(rc => {
    const r = REF_MAP[rc];
    if (r) srcs.add(r['ref-from']);
    else {
      const p = rc.split('-')[0];
      if (p === 'RG') srcs.add('Regole di gioco');
      else if (p === 'CAS') srcs.add('Casistica');
      else if (p === 'IT') srcs.add('Indicazioni tecniche');
      else if (p === 'NF') srcs.add('Normative federali');
      else if (p === 'VAD') srcs.add('Vademecum');
    }
  });
  return srcs;
}

function initSourceFilters() {
  const grid = document.getElementById('source-filters');
  grid.innerHTML = '';
  const counts = {};
  QUESTIONS.forEach(q => getQuestionSources(q).forEach(src => { counts[src] = (counts[src] || 0) + 1; }));
  SOURCES.forEach(src => {
    const chip = document.createElement('div');
    chip.className = 'chip-check checked ' + (SOURCE_CLASS[src] || '');
    chip.dataset.source = src;
    chip.dataset.checked = 'true';
    chip.innerHTML = '<span>' + escapeHtml(src) + '</span><span class="chip-count">' + (counts[src] || 0) + ' domande</span>';
    chip.setAttribute('role', 'checkbox');
    chip.setAttribute('aria-checked', 'true');
    chip.setAttribute('tabindex', '0');
    chip.addEventListener('click', () => setSourceChecked(chip, chip.dataset.checked !== 'true'));
    chip.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); chip.click(); }
    });
    grid.appendChild(chip);
  });
}
function setSourceChecked(chip, on, silent) {
  chip.dataset.checked = on ? 'true' : 'false';
  chip.classList.toggle('checked', on);
  chip.setAttribute('aria-checked', on ? 'true' : 'false');
  if (!silent) updateMaxCount();
}
document.querySelectorAll('[data-select-sources]').forEach(b => b.addEventListener('click', () => {
  const on = b.dataset.selectSources === 'all';
  document.querySelectorAll('#source-filters .chip-check').forEach(c => setSourceChecked(c, on, true));
  updateMaxCount();
}));

function initDifficultyFilters() {
  const grid = document.getElementById('difficulty-filters');
  grid.innerHTML = '';
  DIFFICULTIES.forEach(diff => {
    const chip = document.createElement('div');
    chip.className = 'chip-check checked';
    chip.dataset.difficulty = diff;
    chip.dataset.checked = 'true';
    chip.textContent = DIFF_LABELS[diff];
    chip.setAttribute('role', 'checkbox');
    chip.setAttribute('aria-checked', 'true');
    chip.setAttribute('tabindex', '0');
    chip.addEventListener('click', () => {
      const on = chip.dataset.checked === 'true';
      chip.dataset.checked = on ? 'false' : 'true';
      chip.classList.toggle('checked', !on);
      chip.setAttribute('aria-checked', !on);
      updateMaxCount();
    });
    chip.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); chip.click(); }
    });
    grid.appendChild(chip);
  });
}

function getSelectedDifficulties() {
  return Array.from(document.querySelectorAll('#difficulty-filters .chip-check[data-checked="true"]')).map(c => c.dataset.difficulty);
}

function getSelectedSources() {
  return Array.from(document.querySelectorAll('#source-filters .chip-check[data-checked="true"]')).map(c => c.dataset.source);
}

function getFilteredQuestions() {
  const sources = getSelectedSources();
  const diffs = getSelectedDifficulties();
  const bmOnly = document.getElementById('bookmarks-only-toggle').checked;
  const wrongOnly = document.getElementById('wrong-only-toggle').checked;
  const changesOnly = document.getElementById('changes-only-toggle').checked;
  // Build set of question codes that have been answered wrong
  let wrongCodes = null;
  if (wrongOnly) {
    wrongCodes = new Set();
    // A question is "wrong" if any of its refs appear in refWrong
    QUESTIONS.forEach(q => {
      const hasWrong = q['question-ref'].some(rc => appData.stats.refWrong[rc] > 0);
      if (hasWrong) wrongCodes.add(q['question-code']);
    });
  }
  return QUESTIONS.filter(q => {
    const qSrcs = getQuestionSources(q);
    const matchSource = sources.some(s => qSrcs.has(s));
    if (!matchSource) return false;
    if (diffs.length < DIFFICULTIES.length && !diffs.includes(q['question-difficulty'])) return false;
    if (bmOnly && !appData.bookmarks.questions.includes(q['question-code'])) return false;
    if (wrongOnly && !wrongCodes.has(q['question-code'])) return false;
    if (changesOnly && !questionHasCurrentChange(q)) return false;
    return true;
  });
}

let selectedQCount = 20; // current selection: number or 'all'
const PRESET_COUNTS = [10, 20, 30, 50];

function updateMaxCount() {
  const filtered = getFilteredQuestions();
  const hint = document.getElementById('q-count-hint');
  hint.textContent = filtered.length === 1 ? '1 domanda disponibile con questi filtri' : filtered.length + ' domande disponibili con questi filtri';
  if (selectedQCount !== 'all' && selectedQCount > filtered.length) {
    selectedQCount = filtered.length;
  }

  const container = document.getElementById('q-count-chips');
  const allBtn = document.getElementById('q-all-toggle');
  const customBtn = document.getElementById('q-custom-btn');

  // Remove any previously inserted custom-value chip
  const oldCustomChip = container.querySelector('.q-count-chip-dynamic');
  if (oldCustomChip) oldCustomChip.remove();

  // If selected value is custom (not a preset and not 'all'), insert a chip
  const isCustom = selectedQCount !== 'all' && !PRESET_COUNTS.includes(selectedQCount);
  if (isCustom) {
    const chip = document.createElement('button');
    chip.className = 'q-count-chip q-count-chip-dynamic active';
    chip.dataset.value = String(selectedQCount);
    chip.textContent = selectedQCount;
    chip.addEventListener('click', () => {
      selectedQCount = parseInt(chip.dataset.value);
      updateMaxCount();
    });
    // Insert in sorted position before the first preset > selectedQCount, or before "Tutte"
    let inserted = false;
    for (const c of container.querySelectorAll('.q-count-chip')) {
      const v = parseInt(c.dataset.value);
      if (!isNaN(v) && v > selectedQCount && !c.classList.contains('q-count-chip-dynamic')) {
        container.insertBefore(chip, c);
        inserted = true;
        break;
      }
    }
    if (!inserted) container.insertBefore(chip, allBtn);
  }

  // Update active state on all chips
  container.querySelectorAll('.q-count-chip').forEach(c => {
    if (c.dataset.value === 'custom') return;
    c.classList.toggle('active', String(selectedQCount) === c.dataset.value);
  });
  customBtn.classList.remove('active');
  renderQuizSummary(filtered.length);
}

function renderQuizSummary(available) {
  const n = selectedQCount === 'all' ? available : Math.min(selectedQCount, available);
  document.getElementById('quiz-summary-count').textContent = n;
  const sources = getSelectedSources();
  const ul = document.getElementById('quiz-summary-sources');
  ul.innerHTML = sources.length
    ? sources.map(s => '<li>' + escapeHtml(s) + '</li>').join('')
    : '<li class="none">Nessuna fonte selezionata</li>';
  document.getElementById('btn-start-quiz').disabled = n === 0;
}

function getQuizCount() {
  if (selectedQCount === 'all') return Infinity;
  return selectedQCount;
}

function shuffleArray(arr) {
  const a = [...arr];
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]];
  }
  return a;
}

function startQuiz() {
  const filtered = getFilteredQuestions();
  if (filtered.length === 0) { showToast('Nessuna domanda disponibile'); return; }
  const count = Math.min(getQuizCount(), filtered.length);
  const shuffled = shuffleArray(filtered).slice(0, count);
  const doShuffle = document.getElementById('shuffle-toggle').checked;
  const saveStats = document.getElementById('save-stats-toggle').checked;

  quizState = {
    questions: shuffled,
    current: 0,
    answers: [],
    shuffleAnswers: doShuffle,
    saveStats: saveStats,
    answered: false
  };

  autoSaveQuiz();
  renderSuspendedSessions();
  document.getElementById('quiz-settings').style.display = 'none';
  document.getElementById('quiz-active').classList.add('visible');
  document.getElementById('quiz-result').style.display = 'none';
  setQuizRunning(true);
  window.scrollTo(0, 0);
  startQuizTimer(0);
  renderQuestion();
}

function questionTypeLabel(q) {
  if (q['question-type'] === 'yes-no') return 'Vero / Falso';
  if (q['question-type'] === 'multi-select') return 'Più risposte';
  return 'Scelta multipla';
}
function questionTypeShort(q) {
  if (q['question-type'] === 'yes-no') return 'V/F';
  if (q['question-type'] === 'multi-select') return 'Più risposte';
  return 'Multipla';
}

function renderQuestion() {
  const qs = quizState;
  const q = qs.questions[qs.current];
  qs.answered = false;
  qs.viewIndex = qs.current; // reset view to current

  document.getElementById('quiz-progress').style.width = ((qs.current / qs.questions.length) * 100) + '%';
  document.getElementById('quiz-stats-label').innerHTML = '<span class="stats-dot ' + (qs.saveStats ? 'on' : 'off') + '"></span>' + (qs.saveStats ? 'Statistiche attive' : 'Statistiche disattivate');
  document.getElementById('quiz-counter').textContent = (qs.current + 1) + ' / ' + qs.questions.length;
  document.getElementById('quiz-type-badge').textContent = questionTypeLabel(q);

  // Difficulty badge
  const diffBadge = document.getElementById('quiz-diff-badge');
  const diff = q['question-difficulty'] || 'm';
  diffBadge.textContent = DIFF_LABELS[diff] || diff;
  diffBadge.className = 'quiz-diff-badge ' + (DIFF_COLORS[diff] || '');

  document.getElementById('question-text').textContent = q['question-text'];
  setQuestionChangeBadge(q);

  let answers = [...q['question-answers']];
  if (qs.shuffleAnswers) answers = shuffleArray(answers);

  const list = document.getElementById('answers-list');
  list.innerHTML = '';
  const isMulti = q['question-type'] === 'multi-select';
  list.classList.toggle('multi', isMulti);
  document.getElementById('multi-hint').hidden = !isMulti;
  answers.forEach((a, i) => {
    const btn = document.createElement('button');
    btn.className = 'answer-btn';
    const letter = q['question-type'] === 'yes-no' ? a.value : String.fromCharCode(65 + i);
    btn.innerHTML = '<span class="answer-letter">' + letter + '</span><span>' + escapeHtml(a.text) + '</span>';
    if (isMulti) {
      btn.setAttribute('aria-pressed', 'false');
      btn.addEventListener('click', () => toggleMultiOption(btn));
      btn._answer = a;
    } else {
      btn.addEventListener('click', () => handleAnswer(btn, a, answers));
    }
    list.appendChild(btn);
  });
  if (isMulti) {
    const row = document.createElement('div');
    row.className = 'multi-confirm-row';
    const confirm = document.createElement('button');
    confirm.className = 'btn btn-primary btn-confirm-multi';
    confirm.id = 'btn-confirm-multi';
    confirm.textContent = 'Conferma risposta';
    confirm.disabled = true;
    confirm.addEventListener('click', () => handleMultiAnswer());
    row.appendChild(confirm);
    list.appendChild(row);
  }

  const bmBtn = document.getElementById('btn-bm-question');
  const isBm = appData.bookmarks.questions.includes(q['question-code']);
  bmBtn.classList.toggle('bookmarked', isBm);
  bmBtn.querySelector('span').textContent = isBm ? 'Salvata' : 'Salva';

  // Flag button state
  const flagBtn = document.getElementById('btn-flag-question');
  const isFlagged = isQuestionFlagged(q['question-code']);
  flagBtn.classList.toggle('flagged', isFlagged);
  flagBtn.querySelector('svg').setAttribute('fill', isFlagged ? 'currentColor' : 'none');
  flagBtn.querySelector('span').textContent = isFlagged ? 'Segnalata' : 'Segnala';

  document.getElementById('btn-skip').style.display = isFlagged ? 'inline-block' : 'none';
  document.getElementById('btn-next').style.display = 'none';

  const peekBtn = document.getElementById('btn-ref-peek');
  peekBtn.classList.remove('open');
  populateRefAccordion(q['question-ref']);
  document.getElementById('ref-reveal-container').style.display = 'none';

  // Show/hide action sections
  document.querySelector('.question-actions').style.display = '';
  document.querySelector('.quiz-session-actions').style.display = '';

  updateNavArrows();
}

function showQuestionAt(index) {
  const qs = quizState;
  if (index < 0 || index >= qs.questions.length) return;
  qs.viewIndex = index;

  // If navigating back to the current active question
  if (index === qs.current) {
    renderQuestion();
    return;
  }

  // Show a previously answered question read-only
  const q = qs.questions[index];
  const ans = qs.answers.find(a => a.code === q['question-code']);
  if (!ans) return; // shouldn't happen — can only go back to answered questions

  document.getElementById('quiz-counter').textContent = (index + 1) + ' / ' + qs.questions.length;
  document.getElementById('quiz-type-badge').textContent = questionTypeLabel(q);

  const diffBadge = document.getElementById('quiz-diff-badge');
  const diff = q['question-difficulty'] || 'm';
  diffBadge.textContent = DIFF_LABELS[diff] || diff;
  diffBadge.className = 'quiz-diff-badge ' + (DIFF_COLORS[diff] || '');

  document.getElementById('question-text').textContent = q['question-text'];
  setQuestionChangeBadge(q);

  const list = document.getElementById('answers-list');
  list.innerHTML = '';
  const answers = [...q['question-answers']];
  const isSkipped = ans.skipped;
  const isMultiView = q['question-type'] === 'multi-select';
  list.classList.toggle('multi', isMultiView);
  document.getElementById('multi-hint').hidden = !isMultiView;
  const chosen = ans.selectedTexts || (ans.selectedText !== undefined ? [ans.selectedText] : []);
  answers.forEach((a, i) => {
    const btn = document.createElement('button');
    btn.className = 'answer-btn disabled';
    const letter = q['question-type'] === 'yes-no' ? a.value : String.fromCharCode(65 + i);
    btn.innerHTML = '<span class="answer-letter">' + letter + '</span><span>' + escapeHtml(a.text) + '</span>';
    if (isSkipped) {
      btn.classList.add('skipped');
    } else {
      const picked = chosen.includes(a.text);
      if (a.correct && (picked || !isMultiView)) btn.classList.add('correct');
      if (a.correct && !picked && isMultiView) btn.classList.add('missed');
      if (!a.correct && picked) btn.classList.add('wrong');
    }
    list.appendChild(btn);
  });

  // Show refs only for answered questions, not skipped
  populateRefAccordion(q['question-ref']);
  if (isSkipped) {
    document.getElementById('ref-reveal-container').style.display = 'none';
    document.getElementById('btn-ref-peek').classList.remove('open');
  } else {
    document.getElementById('ref-reveal-container').style.display = 'block';
    document.getElementById('btn-ref-peek').classList.add('open');
  }

  // Hide action buttons in read-only mode
  document.querySelector('.question-actions').style.display = 'none';
  document.querySelector('.quiz-session-actions').style.display = 'none';

  updateNavArrows();
}

function setQuestionChangeBadge(q) {
  const el = document.getElementById('quiz-change-badge');
  const on = questionHasCurrentChange(q);
  el.hidden = !on;
  el.textContent = on ? 'Novità ' + CURRENT_SEASON : '';
  el.title = on ? 'Riguarda una regola nuova o modificata nella stagione ' + CURRENT_SEASON : '';
}

function updateNavArrows() {
  const qs = quizState;
  if (!qs) return;
  const prevBtn = document.getElementById('quiz-nav-prev');
  const nextBtn = document.getElementById('quiz-nav-next');
  prevBtn.disabled = qs.viewIndex <= 0;
  // Can go forward only up to current question
  nextBtn.disabled = qs.viewIndex >= qs.current;
}

function toggleMultiOption(btn) {
  if (quizState.answered) return;
  const on = !btn.classList.contains('selected');
  btn.classList.toggle('selected', on);
  btn.setAttribute('aria-pressed', on ? 'true' : 'false');
  const any = document.querySelectorAll('#answers-list .answer-btn.selected').length > 0;
  const confirm = document.getElementById('btn-confirm-multi');
  if (confirm) confirm.disabled = !any;
}

function handleMultiAnswer() {
  if (quizState.answered) return;
  const buttons = Array.from(document.querySelectorAll('#answers-list .answer-btn'));
  const selected = buttons.filter(b => b.classList.contains('selected'));
  if (selected.length === 0) return;
  quizState.answered = true;

  const q = quizState.questions[quizState.current];
  let isCorrect = true;
  buttons.forEach(btn => {
    const a = btn._answer;
    const picked = btn.classList.contains('selected');
    btn.classList.remove('selected');
    btn.classList.add('disabled');
    if (a.correct && picked) btn.classList.add('correct');
    else if (a.correct && !picked) { btn.classList.add('missed'); isCorrect = false; }
    else if (!a.correct && picked) { btn.classList.add('wrong'); isCorrect = false; }
  });
  const confirmRow = document.querySelector('.multi-confirm-row');
  if (confirmRow) {
    confirmRow.innerHTML = '<span class="multi-legend">' +
      (isCorrect ? 'Risposta esatta: hai selezionato tutte e sole le risposte corrette.'
                 : 'Bordo verde tratteggiato = risposta corretta non selezionata.') + '</span>';
  }

  quizState.answers.push({ code: q['question-code'], correct: isCorrect, refs: q['question-ref'],
    selectedTexts: selected.map(b => b._answer.text) });
  finishAnswer(q, isCorrect);
}

function handleAnswer(clickedBtn, answer, allAnswers) {
  if (quizState.answered) return;
  quizState.answered = true;

  const q = quizState.questions[quizState.current];
  const isCorrect = answer.correct;

  document.querySelectorAll('.answer-btn').forEach(btn => btn.classList.add('disabled'));
  clickedBtn.classList.add(isCorrect ? 'correct' : 'wrong');

  if (!isCorrect) {
    const correctAns = allAnswers.find(a => a.correct);
    document.querySelectorAll('.answer-btn').forEach(btn => {
      if (btn.querySelector('span:last-child').textContent === correctAns.text) {
        btn.classList.add('correct');
      }
    });
  }

  quizState.answers.push({ code: q['question-code'], correct: isCorrect, refs: q['question-ref'], selectedText: answer.text });
  finishAnswer(q, isCorrect);
}

function finishAnswer(q, isCorrect) {
  if (quizState.saveStats) {
    if (isCorrect) {
      appData.stats.correct++;
      q['question-ref'].forEach(rc => {
        appData.stats.refCorrect[rc] = (appData.stats.refCorrect[rc] || 0) + 1;
      });
    } else {
      appData.stats.wrong++;
      q['question-ref'].forEach(rc => {
        appData.stats.refWrong[rc] = (appData.stats.refWrong[rc] || 0) + 1;
      });
    }
    saveData();
  }
  autoSaveQuiz();

  document.getElementById('ref-reveal-container').style.display = 'block';
  document.getElementById('btn-ref-peek').classList.add('open');

  const btnNext = document.getElementById('btn-next');
  btnNext.style.display = 'inline-block';
  btnNext.textContent = quizState.current < quizState.questions.length - 1 ? 'Prossima' : 'Risultati';
  updateNavArrows();
}

function populateRefAccordion(refCodes) {
  const container = document.getElementById('ref-reveal-container');
  const wrapper = document.createElement('div');
  wrapper.className = 'ref-reveal';
  wrapper.innerHTML = '<div class="ref-reveal-title">Riferimenti normativi</div>';

  refCodes.forEach(rc => {
    const r = REF_MAP[rc];
    const item = document.createElement('div');
    item.className = 'ref-reveal-item';
    const isBm = appData.bookmarks.refs.includes(rc);
    if (r) {
      const title = r['ref-title'] ? ' — ' + escapeHtml(r['ref-title']) : '';
      const hasFullText = !!r['ref-full-text'];
      item.innerHTML =
        '<div class="ref-reveal-header">' +
          '<span class="ref-source-tag ' + (SOURCE_CLASS[r['ref-from']] || '') + '">' + (SOURCE_SHORT[r['ref-from']] || '') + '</span>' +
          '<span class="ref-code-tag">' + rc + title + '</span>' + changeBadgeHtml(r['ref-change']) +
          '<button class="ref-reveal-bm ' + (isBm ? 'bookmarked' : '') + '" data-ref="' + rc + '" title="' + (isBm ? 'Rimuovi dai salvati' : 'Salva riferimento') + '">' +
            '<svg width="14" height="14" viewBox="0 0 16 16" fill="' + (isBm ? 'currentColor' : 'none') + '" stroke="currentColor" stroke-width="1.5"><path d="M4 2h8a1 1 0 011 1v11.5l-4.5-3-4.5 3V3a1 1 0 011-1z"/></svg>' +
          '</button>' +
        '</div>' +
        '<div style="margin-top:2px">' + escapeHtml(r['ref-text']) + '</div>' + changeNoteHtml(r['ref-change']) +
        (hasFullText ?
          '<div class="ref-fulltext-wrapper">' +
            (r['ref-full-text'].trim() === r['ref-text'].trim() ?
              '<span class="ref-fulltext-same">testo completo uguale al riassunto</span>' :
              '<button class="ref-fulltext-toggle">Mostra dettagli</button>' +
              '<div class="ref-fulltext-content" hidden>' + r['ref-full-text'].replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>') + '</div>') +
          '</div>' : '') +
        docLinkHtml(r['ref-from']);
    } else {
      item.innerHTML = '<span class="ref-code-tag">' + rc + '</span>';
    }
    wrapper.appendChild(item);
  });

  container.innerHTML = '';
  container.appendChild(wrapper);

  // Attach ref bookmark handlers
  container.querySelectorAll('.ref-reveal-bm').forEach(btn => {
    btn.addEventListener('click', async e => {
      e.stopPropagation();
      const code = btn.dataset.ref;
      const idx = appData.bookmarks.refs.indexOf(code);
      if (idx >= 0) {
        appData.bookmarks.refs.splice(idx, 1);
        delete appData.bookmarks.refNotes[code];
        saveData();
        btn.classList.toggle('bookmarked', false);
        btn.querySelector('svg').setAttribute('fill', 'none');
        btn.title = 'Salva riferimento';
        updateSavedCount();
        showToast('Riferimento rimosso');
      } else {
        const note = await showBmNoteModal('Salva riferimento', '');
        if (note === null) return;
        appData.bookmarks.refs.push(code);
        if (note) appData.bookmarks.refNotes[code] = note;
        saveData();
        btn.classList.toggle('bookmarked', true);
        btn.querySelector('svg').setAttribute('fill', 'currentColor');
        btn.title = 'Rimuovi dai salvati';
        updateSavedCount();
        showToast('Riferimento salvato');
      }
    });
  });

  // Attach full-text toggle handlers
  container.querySelectorAll('.ref-fulltext-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const content = btn.nextElementSibling;
      const isOpen = !content.hidden;
      content.hidden = isOpen;
      btn.textContent = isOpen ? 'Mostra dettagli' : 'Nascondi dettagli';
    });
  });
}

// Navigation arrows
document.getElementById('quiz-nav-prev').addEventListener('click', () => {
  if (!quizState || quizState.viewIndex <= 0) return;
  showQuestionAt(quizState.viewIndex - 1);
});
document.getElementById('quiz-nav-next').addEventListener('click', () => {
  if (!quizState || quizState.viewIndex >= quizState.current) return;
  showQuestionAt(quizState.viewIndex + 1);
});

document.getElementById('btn-ref-peek').addEventListener('click', () => {
  const container = document.getElementById('ref-reveal-container');
  const btn = document.getElementById('btn-ref-peek');
  const isOpen = container.style.display !== 'none';
  container.style.display = isOpen ? 'none' : 'block';
  btn.classList.toggle('open', !isOpen);
});

function nextQuestion() {
  if (!quizState) return;
  // If viewing a past question, return to the current active question first
  if (quizState.viewIndex !== quizState.current) {
    showQuestionAt(quizState.current);
    return;
  }
  // Guard: ensure we have an answer for the current question before advancing
  const currentCode = quizState.questions[quizState.current]['question-code'];
  const hasAnswer = quizState.answers.some(a => a.code === currentCode);
  if (!hasAnswer) return; // not yet answered/skipped — don't advance
  if (quizState.current < quizState.questions.length - 1) {
    quizState.current++;
    renderQuestion();
  } else {
    showResults();
  }
}

function showResults() {
  clearAutoSave();
  const totalElapsed = stopQuizTimer();
  document.getElementById('quiz-active').classList.remove('visible');
  const result = document.getElementById('quiz-result');
  result.style.display = 'block';

  const flagged = quizState.answers.filter(a => a.flagged).length;
  const validAnswers = quizState.answers.filter(a => !a.flagged);
  const correct = validAnswers.filter(a => a.correct).length;
  const skipped = validAnswers.filter(a => a.skipped).length;
  const answered = validAnswers.filter(a => !a.skipped).length;
  const pct = answered > 0 ? Math.round((correct / answered) * 100) : 0;

  document.getElementById('result-score').textContent = pct + '%';
  document.getElementById('result-correct').textContent = correct;
  document.getElementById('result-wrong').textContent = answered - correct;

  const skippedItem = document.getElementById('result-skipped-item');
  if (skipped > 0) {
    skippedItem.style.display = '';
    document.getElementById('result-skipped').textContent = skipped;
  } else {
    skippedItem.style.display = 'none';
  }

  const flaggedItem = document.getElementById('result-flagged-item');
  if (flagged > 0) {
    flaggedItem.style.display = '';
    document.getElementById('result-flagged').textContent = flagged;
  } else {
    flaggedItem.style.display = 'none';
  }

  // Total and average time (global timer)
  const totalMs = totalElapsed;
  const avgMs = quizState.answers.length > 0 ? Math.round(totalMs / quizState.answers.length) : 0;
  document.getElementById('result-time').textContent = formatTimer(totalMs);
  document.getElementById('result-avg-time').textContent = formatTimer(avgMs);
  document.getElementById('result-ring').style.setProperty('--p', pct);
  document.getElementById('result-ring-label').textContent = answered ? correct + '/' + answered : '–';
  setQuizRunning(false);
  window.scrollTo(0, 0);
  renderResultReview(quizState.answers);
}

// End-of-quiz summary: wrong and skipped questions, with the references to review
function renderResultReview(answers) {
  const box = document.getElementById('result-review');
  const toReview = answers.filter(a => !a.flagged && (!a.correct || a.skipped));
  if (!toReview.length) {
    box.innerHTML = answers.length ? '<div class="empty-state">Nessun errore in questo quiz.</div>' : '';
    return;
  }
  box.innerHTML = '<div class="review-title">Da ripassare (' + toReview.length + ')</div>';
  toReview.forEach(a => {
    const q = QUESTIONS.find(x => x['question-code'] === a.code);
    if (!q) return;
    const item = document.createElement('div');
    item.className = 'review-item';
    item.innerHTML = '<span class="review-mark ' + (a.skipped ? 'skip' : '') + '">' + (a.skipped ? '–' : '✕') + '</span>' +
      '<span class="review-text">' + escapeHtml(q['question-text']) +
      '<span class="review-refs">' + q['question-ref'].map(rc => '<button type="button" data-ref="' + escapeHtml(rc) + '">' + escapeHtml(rc) + '</button>').join(', ') + '</span></span>';
    item.querySelectorAll('[data-ref]').forEach(b => b.addEventListener('click', () => jumpToRef(b.dataset.ref)));
    box.appendChild(item);
  });
}
document.getElementById('btn-result-stats').addEventListener('click', () => showSection('stats'));

function resetQuiz() {
  clearAutoSave();
  stopQuizTimer();
  quizState = null;
  document.getElementById('quiz-active').classList.remove('visible');
  document.getElementById('quiz-result').style.display = 'none';
  document.getElementById('quiz-settings').style.display = '';
  setQuizRunning(false);
  updateMaxCount();
  renderSuspendedSessions();
}

// ======= PAUSE / RESUME / STOP =======
function loadSuspendedSessions() {
  try {
    const raw = Store.getItem(SUSPENDED_KEY);
    if (raw) {
      const parsed = JSON.parse(raw);
      // Migrate old single-session format to array
      if (parsed && !Array.isArray(parsed)) return [parsed];
      return parsed || [];
    }
  } catch(e) {}
  return [];
}

function saveSuspendedSessions(sessions) {
  try { Store.setItem(SUSPENDED_KEY, JSON.stringify(sessions)); } catch(e) {}
}

function addSuspendedSession() {
  if (!quizState) return;
  const data = {
    id: Date.now().toString(36) + Math.random().toString(36).slice(2, 6),
    questionCodes: quizState.questions.map(q => q['question-code']),
    current: quizState.current,
    answers: quizState.answers,
    shuffleAnswers: quizState.shuffleAnswers,
    saveStats: quizState.saveStats,
    currentQuestionElapsed: getQuizElapsed(),
    suspendedAt: new Date().toISOString()
  };
  const sessions = loadSuspendedSessions();
  sessions.unshift(data);
  saveSuspendedSessions(sessions);
}

function removeSuspendedSession(id) {
  const sessions = loadSuspendedSessions().filter(s => s.id !== id);
  saveSuspendedSessions(sessions);
}

function clearAllSuspendedSessions() {
  try { Store.removeItem(SUSPENDED_KEY); } catch(e) {}
}

function autoSaveQuiz() {
  if (!quizState) { clearAutoSave(); return; }
  try {
    Store.setItem(AUTOSAVE_KEY, JSON.stringify({
      questionCodes: quizState.questions.map(q => q['question-code']),
      current: quizState.current,
      answers: quizState.answers,
      shuffleAnswers: quizState.shuffleAnswers,
      saveStats: quizState.saveStats,
      currentQuestionElapsed: getQuizElapsed(),
      savedAt: new Date().toISOString()
    }));
  } catch(e) {}
}

function clearAutoSave() {
  try { Store.removeItem(AUTOSAVE_KEY); } catch(e) {}
}

function restoreAutoSave() {
  try {
    const raw = Store.getItem(AUTOSAVE_KEY);
    if (!raw) return false;
    const saved = JSON.parse(raw);
    if (!saved || !saved.questionCodes || !saved.questionCodes.length) { clearAutoSave(); return false; }
    // Convert autosave into a suspended session so user sees it as a resumable quiz
    const session = {
      id: 'auto_' + Date.now().toString(36),
      questionCodes: saved.questionCodes,
      current: saved.current,
      answers: saved.answers || [],
      shuffleAnswers: saved.shuffleAnswers,
      saveStats: saved.saveStats,
      currentQuestionElapsed: saved.currentQuestionElapsed || 0,
      suspendedAt: saved.savedAt || new Date().toISOString()
    };
    const sessions = loadSuspendedSessions();
    sessions.unshift(session);
    saveSuspendedSessions(sessions);
    clearAutoSave();
    return true;
  } catch(e) { clearAutoSave(); return false; }
}

function renderSuspendedSessions() {
  const container = document.getElementById('suspended-list');
  const sessions = loadSuspendedSessions();
  container.innerHTML = '';
  if (sessions.length === 0 || quizState) return;

  sessions.forEach((s, idx) => {
    if (idx > 0 && !showAllSuspended) return;
    const answered = s.answers ? s.answers.length : 0;
    const total = s.questionCodes ? s.questionCodes.length : 0;
    const remaining = total - answered;
    const dateStr = s.suspendedAt ? formatFlagDate(s.suspendedAt) : (s.timestamp ? formatFlagDate(new Date(s.timestamp).toISOString()) : '');
    const totalTimeMs = s.currentQuestionElapsed || 0;
    const timeStr = totalTimeMs > 0 ? ' · ' + formatTimer(totalTimeMs) : '';

    const card = document.createElement('div');
    card.className = 'suspended-card';
    const pctDone = total > 0 ? Math.round((answered / total) * 100) : 0;
    card.innerHTML =
      '<div class="suspended-card-body">' +
        '<div class="suspended-card-title"><strong>Quiz in sospeso</strong>: ' + answered + ' di ' + total + ' risposte' + timeStr + '</div>' +
        '<div class="suspended-card-meta">Sospeso il ' + dateStr + ' · ' + remaining + (remaining === 1 ? ' domanda rimanente' : ' domande rimanenti') + '</div>' +
        '<div class="suspended-progress"><span style="width:' + pctDone + '%"></span></div>' +
      '</div>' +
      '<div class="suspended-card-actions">' +
        '<button class="btn btn-primary btn-sm btn-resume-session">Riprendi</button>' +
        '<button class="btn btn-ghost btn-sm btn-discard-session">Scarta</button>' +
      '</div>';

    card.querySelector('.btn-resume-session').addEventListener('click', () => {
      resumeSession(s.id);
    });

    card.querySelector('.btn-discard-session').addEventListener('click', async () => {
      const ok = await showModal('Scartare la sessione?', 'La sessione sospesa verrà eliminata. Le risposte già date sono state salvate nelle statistiche.');
      if (!ok) return;
      removeSuspendedSession(s.id);
      renderSuspendedSessions();
      showToast('Sessione scartata');
    });

    container.appendChild(card);
  });
  if (sessions.length > 1) {
    const more = document.createElement('button');
    more.className = 'btn-link suspended-more';
    more.textContent = showAllSuspended ? 'Mostra solo il più recente' : (sessions.length === 2 ? 'Mostra l\'altro quiz in sospeso' : 'Mostra gli altri ' + (sessions.length - 1) + ' quiz in sospeso');
    more.addEventListener('click', () => { showAllSuspended = !showAllSuspended; renderSuspendedSessions(); });
    container.appendChild(more);
  }
}
let showAllSuspended = false;

function resumeSession(id) {
  const sessions = loadSuspendedSessions();
  const suspended = sessions.find(s => s.id === id);
  if (!suspended) return;

  const questions = suspended.questionCodes.map(code => QUESTIONS.find(q => q['question-code'] === code)).filter(Boolean);
  if (questions.length === 0) { removeSuspendedSession(id); renderSuspendedSessions(); return; }

  quizState = {
    questions: questions,
    current: suspended.answers ? suspended.answers.length : suspended.current,
    answers: suspended.answers || [],
    shuffleAnswers: suspended.shuffleAnswers,
    saveStats: suspended.saveStats,
    answered: false
  };

  removeSuspendedSession(id);
  autoSaveQuiz();
  renderSuspendedSessions();
  document.getElementById('quiz-settings').style.display = 'none';
  document.getElementById('quiz-active').classList.add('visible');
  document.getElementById('quiz-result').style.display = 'none';
  setQuizRunning(true);
  window.scrollTo(0, 0);
  startQuizTimer(suspended.currentQuestionElapsed || 0);
  renderQuestion();
  showToast('Quiz ripreso');
}

function pauseQuiz() {
  if (!quizState) return;
  clearAutoSave();
  stopQuizTimer();
  addSuspendedSession();
  quizState = null;
  document.getElementById('quiz-active').classList.remove('visible');
  document.getElementById('quiz-settings').style.display = '';
  setQuizRunning(false);
  renderSuspendedSessions();
  showToast('Quiz sospeso');
}

async function stopQuiz() {
  if (!quizState) return;
  const ok = await showModal('Terminare il quiz?', 'Il quiz verrà terminato. Le risposte già date sono state salvate nelle statistiche.');
  if (!ok) return;
  if (quizState.answers.length > 0) {
    showResults();
  } else {
    resetQuiz();
  }
}

document.getElementById('btn-start-quiz').addEventListener('click', startQuiz);
document.getElementById('btn-next').addEventListener('click', nextQuestion);
document.getElementById('btn-restart').addEventListener('click', resetQuiz);
document.getElementById('btn-pause-quiz').addEventListener('click', pauseQuiz);
document.getElementById('btn-stop-quiz').addEventListener('click', stopQuiz);

// Keyboard shortcuts for quiz
document.addEventListener('keydown', (e) => {
  if (!quizState) return;
  // Ignore if user is typing in a text field
  const tag = e.target.tagName;
  if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || e.target.isContentEditable) return;
  // Ignore if a modal is open
  if (document.querySelector('.modal-overlay:not([hidden])')) return;
  // Only act when viewing the current (active) question
  if (quizState.viewIndex !== quizState.current) return;

  const key = e.key.toUpperCase();

  // If already answered, Enter/Space → next question
  if (quizState.answered) {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      nextQuestion();
    }
    return;
  }

  // Map keys to answer index
  const q = quizState.questions[quizState.current];
  if (q['question-type'] === 'multi-select' && e.key === 'Enter') {
    e.preventDefault();
    handleMultiAnswer();
    return;
  }
  const buttons = document.querySelectorAll('#answers-list .answer-btn:not(.disabled):not(.btn-confirm-multi)');
  if (buttons.length === 0) return;

  let targetIdx = -1;
  if (q['question-type'] === 'yes-no') {
    // V → Vero, F → Falso
    if (key === 'V' || key === 'F') {
      targetIdx = Array.from(buttons).findIndex(b => b.querySelector('.answer-letter').textContent.trim().toUpperCase().charAt(0) === key);
    }
  } else {
    // A=0, B=1, C=2, D=3
    const idx = key.charCodeAt(0) - 65;
    if (idx >= 0 && idx < buttons.length) targetIdx = idx;
  }

  if (targetIdx >= 0 && targetIdx < buttons.length) {
    e.preventDefault();
    buttons[targetIdx].click();
  }
});

document.getElementById('btn-bm-question').addEventListener('click', async () => {
  if (!quizState) return;
  const q = quizState.questions[quizState.current];
  const code = q['question-code'];
  const idx = appData.bookmarks.questions.indexOf(code);
  if (idx >= 0) {
    appData.bookmarks.questions.splice(idx, 1);
    delete appData.bookmarks.questionNotes[code];
    saveData();
    const btn = document.getElementById('btn-bm-question');
    btn.classList.toggle('bookmarked', false);
    btn.querySelector('span').textContent = 'Salva';
    updateSavedCount();
    showToast('Domanda rimossa');
  } else {
    const note = await showBmNoteModal('Salva domanda', '');
    if (note === null) return;
    appData.bookmarks.questions.push(code);
    if (note) appData.bookmarks.questionNotes[code] = note;
    saveData();
    const btn = document.getElementById('btn-bm-question');
    btn.classList.toggle('bookmarked', true);
    btn.querySelector('span').textContent = 'Salvata';
    updateSavedCount();
    showToast('Domanda salvata');
  }
});

document.getElementById('bookmarks-only-toggle').addEventListener('change', updateMaxCount);
document.getElementById('save-stats-toggle').addEventListener('change', e => {
  document.getElementById('save-stats-hint').textContent = e.target.checked ? 'Le risposte contano nei tuoi progressi' : 'Prova libera: le risposte non vengono conteggiate';
});
document.getElementById('wrong-only-toggle').addEventListener('change', updateMaxCount);
document.getElementById('changes-only-toggle').addEventListener('change', updateMaxCount);
(function initChangesToggle() {
  const n = QUESTIONS.filter(questionHasCurrentChange).length;
  document.getElementById('changes-only-title').textContent = 'Solo novità della stagione ' + CURRENT_SEASON;
  document.getElementById('changes-only-hint').textContent = n
    ? n + (n === 1 ? ' domanda su una regola nuova o modificata quest’anno' : ' domande su regole nuove o modificate quest’anno')
    : 'Nessuna regola è ancora segnata come nuova o modificata per questa stagione';
  document.getElementById('changes-only-toggle').disabled = !n;
  document.getElementById('changes-only-row').classList.toggle('disabled', !n);
})();

// Question count chips
document.querySelectorAll('.q-count-chip').forEach(chip => {
  chip.addEventListener('click', () => {
    const val = chip.dataset.value;
    const customRow = document.getElementById('q-count-custom-row');
    if (val === 'custom') {
      customRow.hidden = !customRow.hidden;
      if (!customRow.hidden) document.getElementById('q-count').focus();
      return;
    }
    customRow.hidden = true;
    selectedQCount = val === 'all' ? 'all' : parseInt(val);
    updateMaxCount();
  });
});
document.getElementById('q-custom-ok').addEventListener('click', () => {
  const v = parseInt(document.getElementById('q-count').value);
  if (v > 0) {
    selectedQCount = v;
    document.getElementById('q-count-custom-row').hidden = true;
    updateMaxCount();
  }
});
document.getElementById('q-count').addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    e.preventDefault();
    document.getElementById('q-custom-ok').click();
  }
});

// ======= FLAG / SKIP =======
document.getElementById('btn-flag-question').addEventListener('click', async () => {
  if (!quizState) return;
  const q = quizState.questions[quizState.current];
  const code = q['question-code'];
  const existing = getFlagEntry(code);
  if (existing) {
    // Already flagged (open) — remove the open flag and restore stats
    const idx = appData.bookmarks.flagged.indexOf(existing);
    if (idx >= 0) appData.bookmarks.flagged.splice(idx, 1);
    // Restore stats for this answer if it was answered during this quiz
    if (quizState.saveStats) {
      const ans = quizState.answers.find(a => a.code === code && a.flagged);
      if (ans) {
        ans.flagged = false;
        if (ans.correct) {
          appData.stats.correct++;
          (ans.refs || []).forEach(rc => { appData.stats.refCorrect[rc] = (appData.stats.refCorrect[rc] || 0) + 1; });
        } else if (!ans.skipped) {
          appData.stats.wrong++;
          (ans.refs || []).forEach(rc => { appData.stats.refWrong[rc] = (appData.stats.refWrong[rc] || 0) + 1; });
        }
      }
    }
    saveData();
    updateFlagUI(code);
    updateFlaggedCount();
    showToast('Segnalazione rimossa');
  } else {
    // Show text input modal
    const note = await showFlagModal();
    if (note === null) return; // cancelled
    appData.bookmarks.flagged.push({code: code, text: note, date: new Date().toISOString(), resolved: false, resolvedDate: null});
    // Undo stats for this answer if it was answered during this quiz
    if (quizState.saveStats) {
      const ans = quizState.answers.find(a => a.code === code && !a.flagged);
      if (ans) {
        ans.flagged = true;
        if (ans.correct) {
          appData.stats.correct = Math.max(0, appData.stats.correct - 1);
          (ans.refs || []).forEach(rc => { if (appData.stats.refCorrect[rc]) appData.stats.refCorrect[rc]--; });
        } else if (!ans.skipped) {
          appData.stats.wrong = Math.max(0, appData.stats.wrong - 1);
          (ans.refs || []).forEach(rc => { if (appData.stats.refWrong[rc]) appData.stats.refWrong[rc]--; });
        }
      }
    }
    saveData();
    updateFlagUI(code);
    updateFlaggedCount();
    showToast('Domanda segnalata');
  }
});

function updateFlagUI(code) {
  const flagBtn = document.getElementById('btn-flag-question');
  const isFlagged = isQuestionFlagged(code);
  flagBtn.classList.toggle('flagged', isFlagged);
  flagBtn.querySelector('svg').setAttribute('fill', isFlagged ? 'currentColor' : 'none');
  flagBtn.querySelector('span').textContent = isFlagged ? 'Segnalata' : 'Segnala';
  if (!quizState.answered) {
    document.getElementById('btn-skip').style.display = isFlagged ? 'inline-block' : 'none';
  }
}

function showBmNoteModal(title, existingNote) {
  return new Promise(resolve => {
    const overlay = document.getElementById('bm-note-modal');
    const input = document.getElementById('bm-note-modal-input');
    document.getElementById('bm-note-modal-title').textContent = title;
    input.value = existingNote || '';
    overlay.hidden = false;
    input.focus();
    const cleanup = () => { overlay.hidden = true; };
    document.getElementById('bm-note-modal-skip').onclick = () => { cleanup(); resolve(null); };
    document.getElementById('bm-note-modal-confirm').onclick = () => { cleanup(); resolve(input.value.trim()); };
    input.onkeydown = (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); e.stopPropagation(); cleanup(); resolve(input.value.trim()); } };
  });
}

function showFlagModal() {
  return new Promise(resolve => {
    const overlay = document.getElementById('flag-modal');
    const input = document.getElementById('flag-modal-input');
    input.value = '';
    overlay.hidden = false;
    input.focus();
    const cleanup = () => { overlay.hidden = true; };
    document.getElementById('flag-modal-cancel').onclick = () => { cleanup(); resolve(null); };
    document.getElementById('flag-modal-confirm').onclick = () => { cleanup(); resolve(input.value.trim()); };
    input.onkeydown = (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); e.stopPropagation(); cleanup(); resolve(input.value.trim()); } };
  });
}

function showFlagEditModal(flag) {
  const overlay = document.getElementById('flag-modal');
  const input = document.getElementById('flag-modal-input');
  const title = overlay.querySelector('.modal-title');
  const confirmBtn = document.getElementById('flag-modal-confirm');
  const origTitle = title.textContent;
  const origConfirm = confirmBtn.textContent;
  title.textContent = 'Modifica segnalazione ' + flag.code;
  confirmBtn.textContent = 'Salva';
  input.value = flag.text || '';
  overlay.hidden = false;
  input.focus();
  const cleanup = () => {
    overlay.hidden = true;
    title.textContent = origTitle;
    confirmBtn.textContent = origConfirm;
  };
  document.getElementById('flag-modal-cancel').onclick = () => { cleanup(); };
  document.getElementById('flag-modal-confirm').onclick = () => {
    flag.text = input.value.trim();
    saveData();
    cleanup();
    renderFlaggedQuestions();
    showToast('Segnalazione aggiornata');
  };
  input.onkeydown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      flag.text = input.value.trim();
      saveData();
      cleanup();
      renderFlaggedQuestions();
      showToast('Segnalazione aggiornata');
    }
  };
}

document.getElementById('btn-skip').addEventListener('click', () => {
  if (!quizState || quizState.answered) return;
  quizState.answered = true;
  const q = quizState.questions[quizState.current];
  quizState.answers.push({ code: q['question-code'], correct: false, skipped: true, refs: q['question-ref'] });
  autoSaveQuiz();

  // Grey out answers without revealing correct/wrong
  document.querySelectorAll('.answer-btn').forEach(btn => btn.classList.add('disabled', 'skipped'));
  document.getElementById('btn-skip').style.display = 'none';

  const btnNext = document.getElementById('btn-next');
  btnNext.style.display = 'inline-block';
  btnNext.textContent = quizState.current < quizState.questions.length - 1 ? 'Prossima' : 'Risultati';
  updateNavArrows();
});

function updateFlaggedCount() {
  const el = document.getElementById('flagged-count');
  if (el) el.textContent = getOpenFlags().length;
}

function renderFlaggedQuestions() {
  const container = document.getElementById('flagged-questions-list');
  const open = getOpenFlags();
  const resolved = getResolvedFlags();

  if (open.length === 0 && resolved.length === 0) {
    container.innerHTML = '<div class="refs-empty">Nessuna segnalazione</div>';
    return;
  }

  container.innerHTML = '';

  // Open section
  const openTitle = document.createElement('h3');
  openTitle.className = 'list-heading';
  openTitle.textContent = 'Segnalazioni aperte (' + open.length + ')';
  container.appendChild(openTitle);

  if (open.length === 0) {
    const empty = document.createElement('div');
    empty.className = 'refs-empty';
    empty.textContent = 'Nessuna segnalazione aperta';
    container.appendChild(empty);
  } else {
    open.forEach(flag => renderFlagCard(container, flag, false));
  }

  // Resolved section
  if (resolved.length > 0) {
    const resTitle = document.createElement('h3');
    resTitle.className = 'list-heading';
    resTitle.textContent = 'Segnalazioni chiuse (' + resolved.length + ')';
    container.appendChild(resTitle);
    resolved.forEach(flag => renderFlagCard(container, flag, true));
  }
}

function renderFlagCard(container, flag, isResolved) {
  const q = QUESTIONS.find(qq => qq['question-code'] === flag.code);
  if (!q) return;
  const card = document.createElement('div');
  card.className = 'saved-q-card' + (isResolved ? ' flag-resolved' : '');

  let answersHtml = '';
  q['question-answers'].forEach(a => {
    answersHtml += '<div class="saved-q-answer' + (a.correct ? ' is-correct' : '') + '">' +
      (a.correct ? '&#10003; ' : '&nbsp;&nbsp;&nbsp; ') + escapeHtml(a.text) + '</div>';
  });

  const refsText = q['question-ref'].map(rc => {
    const r = REF_MAP[rc];
    const title = r && r['ref-title'] ? ' — ' + escapeHtml(r['ref-title']) : '';
    return rc + title + (r ? ' (' + r['ref-from'] + ')' : '');
  }).join(', ');

  const dateStr = flag.date ? formatFlagDate(flag.date) : '';
  const resolvedStr = flag.resolvedDate ? formatFlagDate(flag.resolvedDate) : '';

  let actionsHtml = '';
  const editBtnHtml =
    '<button class="flag-action-btn flag-edit-btn" title="Modifica descrizione">' +
      '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M11.5 1.5l3 3L5 14H2v-3L11.5 1.5z"/></svg>' +
    '</button>';

  if (!isResolved) {
    actionsHtml =
      '<button class="flag-action-btn flag-copy-btn" title="Copia segnalazione">' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="5" y="5" width="9" height="9" rx="1"/><path d="M5 11H3a1 1 0 01-1-1V3a1 1 0 011-1h7a1 1 0 011 1v2"/></svg>' +
      '</button>' +
      editBtnHtml +
      '<button class="flag-action-btn flag-resolve-btn" title="Segna come risolta">' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 8 6.5 11.5 13 5"/></svg>' +
      '</button>' +
      '<button class="flag-action-btn flag-delete-btn" title="Elimina segnalazione">' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5.5 4V3a1 1 0 011-1h3a1 1 0 011 1v1M6.5 7v4M9.5 7v4M4 4l.7 8.4a1 1 0 001 .9h4.6a1 1 0 001-.9L12 4"/></svg>' +
      '</button>';
  } else {
    actionsHtml =
      '<button class="flag-action-btn flag-reopen-btn" title="Riapri segnalazione">' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 8a6 6 0 0110.5-4M14 2v4h-4M14 8a6 6 0 01-10.5 4M2 14v-4h4"/></svg>' +
      '</button>' +
      '<button class="flag-action-btn flag-delete-btn" title="Elimina segnalazione">' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5.5 4V3a1 1 0 011-1h3a1 1 0 011 1v1M6.5 7v4M9.5 7v4M4 4l.7 8.4a1 1 0 001 .9h4.6a1 1 0 001-.9L12 4"/></svg>' +
      '</button>';
  }

  card.innerHTML =
    '<div class="saved-q-top">' +
      '<span class="saved-q-code">' + flag.code + '</span>' +
      '<span class="saved-q-type">' + questionTypeShort(q) + '</span>' +
      (isResolved ? '<span class="flag-status-badge resolved">Risolta</span>' : '<span class="flag-status-badge open">Aperta</span>') +
      '<span class="flag-actions">' + actionsHtml + '</span>' +
    '</div>' +
    '<div class="flag-meta">' +
      '<span class="flag-date">Segnalata: ' + dateStr + '</span>' +
      (isResolved && resolvedStr ? '<span class="flag-date">Chiusa: ' + resolvedStr + '</span>' : '') +
    '</div>' +
    (flag.text ? '<div class="flag-note">' + flag.text.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>') + '</div>' : '') +
    '<div class="saved-q-text">' + escapeHtml(q['question-text']) + '</div>' +
    '<div class="saved-q-answers">' + answersHtml + '</div>' +
    '<div class="saved-q-refs"><strong>Rif:</strong> ' + refsText + '</div>';

  // Actions
  const resolveBtn = card.querySelector('.flag-resolve-btn');
  if (resolveBtn) {
    resolveBtn.addEventListener('click', () => {
      flag.resolved = true;
      flag.resolvedDate = new Date().toISOString();
      saveData();
      updateFlaggedCount();
      renderFlaggedQuestions();
      showToast('Segnalazione chiusa');
    });
  }

  const reopenBtn = card.querySelector('.flag-reopen-btn');
  if (reopenBtn) {
    reopenBtn.addEventListener('click', () => {
      flag.resolved = false;
      flag.resolvedDate = null;
      saveData();
      updateFlaggedCount();
      renderFlaggedQuestions();
      showToast('Segnalazione riaperta');
    });
  }

  const copyBtn = card.querySelector('.flag-copy-btn');
  if (copyBtn) {
    copyBtn.addEventListener('click', () => {
      let text = 'Segnalazione: ' + flag.code + '\n';
      text += 'Data: ' + (flag.date ? formatFlagDate(flag.date) : 'N/D') + '\n';
      if (flag.text) text += 'Descrizione: ' + flag.text;
      navigator.clipboard.writeText(text.trim()).then(() => showToast('Segnalazione copiata')).catch(() => showToast('Errore nella copia'));
    });
  }

  const deleteBtn = card.querySelector('.flag-delete-btn');
  if (deleteBtn) {
    deleteBtn.addEventListener('click', async () => {
      const ok = await showModal('Eliminare la segnalazione?', 'La segnalazione per ' + flag.code + ' verrà eliminata definitivamente.');
      if (!ok) return;
      const idx = appData.bookmarks.flagged.indexOf(flag);
      if (idx >= 0) appData.bookmarks.flagged.splice(idx, 1);
      saveData();
      updateFlaggedCount();
      renderFlaggedQuestions();
      showToast('Segnalazione eliminata');
    });
  }

  const editBtn = card.querySelector('.flag-edit-btn');
  if (editBtn) {
    editBtn.addEventListener('click', () => {
      showFlagEditModal(flag);
    });
  }

  container.appendChild(card);
}

function refreshQuizBookmarkState() {
  if (!quizState) return;
  const q = quizState.questions[quizState.current];
  if (!q) return;
  // Refresh question bookmark button
  const bmBtn = document.getElementById('btn-bm-question');
  const isBm = appData.bookmarks.questions.includes(q['question-code']);
  bmBtn.classList.toggle('bookmarked', isBm);
  bmBtn.querySelector('span').textContent = isBm ? 'Salvata' : 'Salva';
  // Refresh flag button
  const flagBtn = document.getElementById('btn-flag-question');
  const isFlagged = isQuestionFlagged(q['question-code']);
  flagBtn.classList.toggle('flagged', isFlagged);
  flagBtn.querySelector('svg').setAttribute('fill', isFlagged ? 'currentColor' : 'none');
  flagBtn.querySelector('span').textContent = isFlagged ? 'Segnalata' : 'Segnala';
  if (!quizState.answered) {
    document.getElementById('btn-skip').style.display = isFlagged ? 'inline-block' : 'none';
  }
  // Refresh ref bookmark buttons in accordion
  document.querySelectorAll('#ref-reveal-container .ref-reveal-bm').forEach(btn => {
    const code = btn.dataset.ref;
    const nowBm = appData.bookmarks.refs.includes(code);
    btn.classList.toggle('bookmarked', nowBm);
    btn.querySelector('svg').setAttribute('fill', nowBm ? 'currentColor' : 'none');
    btn.title = nowBm ? 'Rimuovi dai salvati' : 'Salva riferimento';
  });
}

// ======= THEORY =======
let theoryFilterSources = new Set();
let theoryOnlyChanges = false;
const REFS_PAGE = 60;
let refsLimit = REFS_PAGE;   // how many references to show when the list is rebuilt
let refsShown = 0;
let theoryFiltered = [];
let theoryShowBookmarks = false;

function initTheoryFilters() {
  const container = document.getElementById('theory-filters');
  container.innerHTML = '';
  SOURCES.forEach(src => {
    const chip = document.createElement('button');
    chip.className = 'filter-chip ' + (SOURCE_CLASS[src] || '');
    chip.innerHTML = '<span class="dot" style="background:var(--src)"></span>' + escapeHtml(src);
    chip.dataset.source = src;
    chip.addEventListener('click', () => {
      if (theoryFilterSources.has(src)) theoryFilterSources.delete(src);
      else theoryFilterSources.add(src);
      updateTheoryChips();
      refsLimit = REFS_PAGE;
      renderTheory();
    });
    container.appendChild(chip);
  });
  // "Season changes" filter: only references that are new or changed this season
  const nChanges = REFS.filter(r => isCurrentChange(r['ref-code'])).length;
  if (nChanges) {
    const chip = document.createElement('button');
    chip.className = 'filter-chip chip-changes';
    chip.id = 'theory-changes-chip';
    chip.innerHTML = '<span class="dot"></span>Novità ' + CURRENT_SEASON + ' <span class="chip-num">' + nChanges + '</span>';
    chip.title = 'Solo regole nuove o modificate nella stagione ' + CURRENT_SEASON;
    chip.addEventListener('click', () => {
      theoryOnlyChanges = !theoryOnlyChanges;
      updateTheoryChips();
      refsLimit = REFS_PAGE;
      renderTheory();
    });
    container.appendChild(chip);
  }
}

function updateTheoryChips() {
  document.querySelectorAll('#theory-filters .filter-chip[data-source]').forEach(c => {
    c.classList.toggle('active', theoryFilterSources.has(c.dataset.source));
  });
  const ch = document.getElementById('theory-changes-chip');
  if (ch) ch.classList.toggle('active', theoryOnlyChanges);
}

function renderTheory() {
  const search = document.getElementById('theory-search').value.toLowerCase().trim();
  const container = document.getElementById('refs-list');
  container.innerHTML = '';

  let filtered = REFS.filter(r => {
    if (theoryFilterSources.size > 0 && !theoryFilterSources.has(r['ref-from'])) return false;
    if (theoryShowBookmarks && !appData.bookmarks.refs.includes(r['ref-code'])) return false;
    if (theoryOnlyChanges && !isCurrentChange(r['ref-code'])) return false;
    if (search) {
      const hay = normText(r['ref-code'] + ' ' + (r['ref-title'] || '') + ' ' + r['ref-text'] + ' ' + r['ref-from']);
      if (!normText(search).split(/\s+/).every(w => hay.includes(w))) return false;
    }
    return true;
  });

  document.getElementById('refs-count').textContent = filtered.length + ' riferiment' + (filtered.length === 1 ? 'o' : 'i');

  theoryFiltered = filtered;
  refsShown = 0;
  if (filtered.length === 0) {
    document.getElementById('refs-more').hidden = true;
    container.innerHTML = '<div class="refs-empty">Nessun riferimento trovato. Prova con un\'altra parola o togli i filtri.</div>';
    return;
  }
  appendTheoryRefs(refsLimit);
  checkMoreRefs();
}

// Appends the next `count` references of the current list (expanded cards stay as they are)
function appendTheoryRefs(count) {
  const container = document.getElementById('refs-list');
  const next = theoryFiltered.slice(refsShown, refsShown + count);
  const frag = document.createDocumentFragment();
  next.forEach(r => frag.appendChild(makeRefCard(r)));
  container.appendChild(frag);
  refsShown += next.length;
  refsLimit = Math.max(refsLimit, refsShown);
  document.getElementById('refs-more').hidden = refsShown >= theoryFiltered.length;
  return next.length > 0 && refsShown < theoryFiltered.length;
}

function makeRefCard(r) {
    const card = document.createElement('div');
    card.className = 'ref-card';
    const title = r['ref-title'] || '';
    const preview = escapeHtml(title || r['ref-text'].substring(0, 80) + (r['ref-text'].length > 80 ? '...' : ''));
    const isBm = appData.bookmarks.refs.includes(r['ref-code']);
    const srcClass = SOURCE_CLASS[r['ref-from']] || 'rg';

    card.innerHTML =
      '<div class="ref-card-header" tabindex="0" role="button" aria-expanded="false">' +
        '<span class="ref-source-tag ' + srcClass + '">' + (SOURCE_SHORT[r['ref-from']] || r['ref-from']) + '</span>' +
        '<span class="ref-card-code">' + r['ref-code'] + '</span>' + changeBadgeHtml(r['ref-change']) +
        '<span class="ref-card-preview">' + preview + '</span>' +
        '<span class="ref-card-actions">' +
          '<button class="ref-bm-btn ' + (isBm ? 'bookmarked' : '') + '" data-ref="' + r['ref-code'] + '" aria-label="Salva riferimento">' +
            '<svg width="14" height="14" viewBox="0 0 16 16" fill="' + (isBm ? 'currentColor' : 'none') + '" stroke="currentColor" stroke-width="1.5"><path d="M4 2h8a1 1 0 011 1v11.5l-4.5-3-4.5 3V3a1 1 0 011-1z"/></svg>' +
          '</button>' +
          '<span class="ref-expand-icon">&#9660;</span>' +
        '</span>' +
      '</div>' +
      '<div class="ref-card-body">' +
        changeNoteHtml(r['ref-change']) +
        '<div class="ref-summary-text">' + escapeHtml(r['ref-text']) + '</div>' +
        (r['ref-full-text'] ?
          '<div class="ref-fulltext-wrapper">' +
            (r['ref-full-text'].trim() === r['ref-text'].trim() ?
              '<span class="ref-fulltext-same">testo completo uguale al riassunto</span>' :
              '<button class="ref-fulltext-toggle">Nascondi dettagli</button>' +
              '<div class="ref-fulltext-content">' + r['ref-full-text'].replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g, '<br>') + '</div>') +
          '</div>' : '') +
        docLinkHtml(r['ref-from']) +
      '</div>';

    const header = card.querySelector('.ref-card-header');
    header.addEventListener('click', e => {
      if (e.target.closest('.ref-bm-btn')) return;
      card.classList.toggle('expanded');
      header.setAttribute('aria-expanded', card.classList.contains('expanded'));
    });
    header.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); header.click(); }
    });

    const bmBtn = card.querySelector('.ref-bm-btn');
    bmBtn.addEventListener('click', async e => {
      e.stopPropagation();
      const code = bmBtn.dataset.ref;
      const idx = appData.bookmarks.refs.indexOf(code);
      if (idx >= 0) {
        appData.bookmarks.refs.splice(idx, 1);
        delete appData.bookmarks.refNotes[code];
        saveData();
        bmBtn.classList.toggle('bookmarked', false);
        bmBtn.querySelector('svg').setAttribute('fill', 'none');
        updateSavedCount();
        showToast('Riferimento rimosso');
      } else {
        const note = await showBmNoteModal('Salva riferimento', '');
        if (note === null) return;
        appData.bookmarks.refs.push(code);
        if (note) appData.bookmarks.refNotes[code] = note;
        saveData();
        bmBtn.classList.toggle('bookmarked', true);
        bmBtn.querySelector('svg').setAttribute('fill', 'currentColor');
        updateSavedCount();
        showToast('Riferimento salvato');
      }
    });

    card.querySelectorAll('.ref-fulltext-toggle').forEach(btn => {
      btn.addEventListener('click', e => {
        e.stopPropagation();
        const content = btn.nextElementSibling;
        const isOpen = !content.hidden;
        content.hidden = isOpen;
        btn.textContent = isOpen ? 'Mostra dettagli' : 'Nascondi dettagli';
      });
    });

    return card;
}

const checkMoreRefs = InfiniteList.watch(document.getElementById('refs-more'), () => appendTheoryRefs(REFS_PAGE));

document.getElementById('theory-search').addEventListener('input', () => { refsLimit = REFS_PAGE; renderTheory(); });
document.getElementById('theory-bm-toggle').addEventListener('change', () => {
  theoryShowBookmarks = document.getElementById('theory-bm-toggle').checked;
  renderTheory();
});

// ======= OVERVIEW =======
function renderOverview() {
  const container = document.getElementById('overview-weak-list');
  const emptyMsg = document.getElementById('overview-empty');
  container.innerHTML = '';

  const s = appData.stats;
  const total = s.correct + s.wrong;
  if (total === 0) {
    emptyMsg.hidden = false;
    return;
  }
  emptyMsg.hidden = true;

  const allRefCodes = new Set([...Object.keys(s.refCorrect), ...Object.keys(s.refWrong)]);
  const refStats = [];
  allRefCodes.forEach(code => {
    const c = s.refCorrect[code] || 0;
    const w = s.refWrong[code] || 0;
    const tot = c + w;
    const pct = tot > 0 ? Math.round((c / tot) * 100) : 0;
    refStats.push({ code, correct: c, wrong: w, total: tot, pct });
  });

  refStats.sort((a, b) => a.pct - b.pct || b.wrong - a.wrong);

  const weak = refStats.filter(r => r.pct < 80).slice(0, 15);
  if (weak.length === 0) {
    container.innerHTML = '<div class="refs-empty" style="padding:20px">Ottimo lavoro! Nessun riferimento particolarmente debole.</div>';
    return;
  }

  weak.forEach((item, i) => {
    const r = REF_MAP[item.code];
    const source = r ? r['ref-from'] : '';
    const title = r ? (r['ref-title'] || '') : '';
    const preview = escapeHtml(title || (r ? r['ref-text'].substring(0, 60) + '...' : ''));
    const barClass = item.pct < 40 ? 'bad' : item.pct < 65 ? 'ok' : 'good';

    const card = document.createElement('div');
    card.className = 'overview-card';
    card.innerHTML =
      '<span class="overview-rank">' + (i + 1) + '</span>' +
      '<div class="overview-card-body">' +
        '<div class="overview-ref-code">' + item.code + (title ? ' — ' + escapeHtml(title) : '') + '</div>' +
        '<div class="overview-ref-source">' + source + '</div>' +
        '<div class="overview-ref-preview">' + preview + '</div>' +
      '</div>' +
      '<div class="overview-bar-wrap">' +
        '<div class="overview-bar"><div class="overview-bar-fill ' + barClass + '" style="width:' + item.pct + '%"></div></div>' +
        '<span class="overview-pct">' + item.pct + '% (' + item.correct + '/' + item.total + ')</span>' +
      '</div>';

    card.addEventListener('click', () => jumpToRef(item.code));
    container.appendChild(card);
  });
}

// ======= STATS =======
function renderStats() {
  const s = appData.stats;
  const total = s.correct + s.wrong;
  const pct = total > 0 ? Math.round((s.correct / total) * 100) : 0;
  const pctWrong = total > 0 ? 100 - pct : 0;
  document.getElementById('stat-total').textContent = total;
  document.getElementById('stat-correct').innerHTML = total > 0 ? s.correct + '<small>' + pct + '%</small>' : '0';
  document.getElementById('stat-wrong').innerHTML = total > 0 ? s.wrong + '<small>' + pctWrong + '%</small>' : '0';
  document.getElementById('stats-ring').style.setProperty('--p', pct);
  document.getElementById('stats-ring-label').textContent = total ? pct + '%' : '–';
  renderSourceStats();

  const wrongContainer = document.getElementById('stats-wrong-refs');
  const wrongEntries = Object.entries(s.refWrong).sort((a, b) => b[1] - a[1]);
  if (wrongEntries.length === 0) {
    wrongContainer.innerHTML = '<div class="stats-empty">Nessun errore registrato</div>';
  } else {
    wrongContainer.innerHTML = '';
    wrongEntries.forEach(([code, count]) => {
      const r = REF_MAP[code];
      const title = r && r['ref-title'] ? ' — ' + escapeHtml(r['ref-title']) : '';
      const item = document.createElement('div');
      item.className = 'stats-ref-item';
      item.innerHTML = '<span class="stats-ref-code">' + code + title + '</span><span class="stats-ref-count red">' + count + '</span>';
      item.addEventListener('click', () => jumpToRef(code));
      wrongContainer.appendChild(item);
    });
  }

  const correctContainer = document.getElementById('stats-correct-refs');
  const correctEntries = Object.entries(s.refCorrect).sort((a, b) => b[1] - a[1]);
  if (correctEntries.length === 0) {
    correctContainer.innerHTML = '<div class="stats-empty">Nessuna risposta corretta registrata</div>';
  } else {
    correctContainer.innerHTML = '';
    correctEntries.forEach(([code, count]) => {
      const r = REF_MAP[code];
      const title = r && r['ref-title'] ? ' — ' + escapeHtml(r['ref-title']) : '';
      const item = document.createElement('div');
      item.className = 'stats-ref-item';
      item.innerHTML = '<span class="stats-ref-code">' + code + title + '</span><span class="stats-ref-count green">' + count + '</span>';
      item.addEventListener('click', () => jumpToRef(code));
      correctContainer.appendChild(item);
    });
  }
}

// Share of correct answers for each source (from the per-reference counts)
function renderSourceStats() {
  const s = appData.stats;
  const by = {};
  SOURCES.forEach(src => { by[src] = { c: 0, w: 0 }; });
  const srcOf = code => { const r = REF_MAP[code]; if (r) return r['ref-from']; const p = String(code).split('-')[0]; return SOURCES.find(x => SOURCE_SHORT[x] === p); };
  Object.keys(s.refCorrect).forEach(k => { const src = srcOf(k); if (by[src]) by[src].c += s.refCorrect[k]; });
  Object.keys(s.refWrong).forEach(k => { const src = srcOf(k); if (by[src]) by[src].w += s.refWrong[k]; });
  const box = document.getElementById('stats-by-source');
  box.innerHTML = SOURCES.map(src => {
    const d = by[src]; const t = d.c + d.w; const p = t ? Math.round(d.c / t * 100) : 0;
    return '<div class="source-stat ' + SOURCE_CLASS[src] + '">' +
      '<div class="source-stat-name"><span class="ref-source-tag ' + SOURCE_CLASS[src] + '">' + SOURCE_SHORT[src] + '</span>' + escapeHtml(src) + '</div>' +
      '<div class="source-stat-bar' + (t ? '' : ' empty') + '"><span style="width:' + p + '%"></span></div>' +
      '<div class="source-stat-num">' + (t ? '<strong>' + p + '%</strong>' + d.c + '/' + t : 'Nessun dato') + '</div>' +
    '</div>';
  }).join('');
}

function jumpToRef(code) {
  showSection('theory');
  // Show the References sub-section
  const theorySubTabs = document.querySelectorAll('#theory-sub-tabs .sub-tab');
  theorySubTabs.forEach(b => b.classList.remove('active'));
  theorySubTabs[0].classList.add('active');
  document.querySelectorAll('#sec-theory > .sub-section').forEach(ss => ss.classList.remove('active'));
  document.getElementById('sub-theory-refs').classList.add('active');

  document.getElementById('theory-search').value = code;
  theoryFilterSources.clear();
  theoryOnlyChanges = false;
  theoryShowBookmarks = false;
  document.getElementById('theory-bm-toggle').checked = false;
  updateTheoryChips();
  renderTheory();
  setTimeout(() => {
    const first = document.querySelector('.ref-card');
    if (first) first.classList.add('expanded');
  }, 50);
}

// ======= CONFIRMATIONS AND RESET =======
let modalResolve = null;
function showModal(title, text, confirmLabel) {
  return new Promise(resolve => {
    modalResolve = resolve;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-text').textContent = text;
    document.getElementById('modal-confirm').textContent = confirmLabel || 'Conferma';
    document.getElementById('modal').hidden = false;
  });
}
document.getElementById('modal-cancel').addEventListener('click', () => {
  document.getElementById('modal').hidden = true;
  if (modalResolve) { modalResolve(false); modalResolve = null; }
});
document.getElementById('modal-confirm').addEventListener('click', () => {
  document.getElementById('modal').hidden = true;
  if (modalResolve) { modalResolve(true); modalResolve = null; }
});

document.getElementById('btn-reset').addEventListener('click', async () => {
  const ok = await showModal('Azzerare le statistiche?', 'Risposte e percentuali verranno cancellate definitivamente. Domande e riferimenti salvati restano.', 'Azzera');
  if (ok) {
    appData.stats = { correct: 0, wrong: 0, refCorrect: {}, refWrong: {} };
    saveData();
    renderStats();
    if (window.renderProfile) window.renderProfile();
    showToast('Statistiche azzerate');
  }
});

// ======= SAVED QUESTIONS & REFS =======
function updateSavedCount() {
  document.getElementById('saved-count').textContent = appData.bookmarks.questions.length;
  const refsCount = document.getElementById('theory-saved-refs-count');
  if (refsCount) refsCount.textContent = appData.bookmarks.refs.length;
  updateFlaggedCount();
}

function buildSavedQuestionCards(container) {
  const codes = appData.bookmarks.questions;
  if (codes.length === 0) {
    container.innerHTML = '<div class="refs-empty">Nessuna domanda salvata</div>';
    return;
  }
  container.innerHTML = '';
  codes.forEach(code => {
    const q = QUESTIONS.find(qq => qq['question-code'] === code);
    if (!q) return;
    const card = document.createElement('div');
    card.className = 'saved-q-card';

    let answersHtml = '';
    q['question-answers'].forEach(a => {
      answersHtml += '<div class="saved-q-answer' + (a.correct ? ' is-correct' : '') + '">' +
        (a.correct ? '&#10003; ' : '&nbsp;&nbsp;&nbsp; ') + escapeHtml(a.text) + '</div>';
    });

    const refsText = q['question-ref'].map(rc => {
      const r = REF_MAP[rc];
      const title = r && r['ref-title'] ? ' — ' + escapeHtml(r['ref-title']) : '';
      return rc + title + (r ? ' (' + r['ref-from'] + ')' : '');
    }).join(', ');

    const qNote = appData.bookmarks.questionNotes[code] || '';
    card.innerHTML =
      '<div class="saved-q-top">' +
        '<span class="saved-q-code">' + code + '</span>' +
        '<span class="saved-q-type">' + questionTypeShort(q) + '</span>' +
        '<button class="saved-q-edit-note" data-code="' + code + '" title="' + (qNote ? 'Modifica nota' : 'Aggiungi nota') + '">' +
          '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11.5 1.5l3 3L5 14H2v-3z"/></svg>' +
        '</button>' +
        '<button class="saved-q-remove" data-code="' + code + '" title="Rimuovi">' +
          '<svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" stroke="currentColor" stroke-width="1.5"><path d="M4 2h8a1 1 0 011 1v11.5l-4.5-3-4.5 3V3a1 1 0 011-1z"/></svg>' +
        '</button>' +
      '</div>' +
      (qNote ? '<div class="saved-q-note"><strong>Nota:</strong> ' + qNote.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</div>' : '') +
      '<div class="saved-q-text">' + escapeHtml(q['question-text']) + '</div>' +
      '<div class="saved-q-answers">' + answersHtml + '</div>' +
      '<div class="saved-q-refs"><strong>Rif:</strong> ' + refsText + '</div>';

    card.querySelector('.saved-q-edit-note').addEventListener('click', async () => {
      const existing = appData.bookmarks.questionNotes[code] || '';
      const note = await showBmNoteModal('Modifica nota — ' + code, existing);
      if (note === null) return;
      if (note) appData.bookmarks.questionNotes[code] = note;
      else delete appData.bookmarks.questionNotes[code];
      saveData();
      renderSavedQuestions();
      showToast(note ? 'Nota aggiornata' : 'Nota rimossa');
    });

    card.querySelector('.saved-q-remove').addEventListener('click', () => {
      const idx = appData.bookmarks.questions.indexOf(code);
      if (idx >= 0) appData.bookmarks.questions.splice(idx, 1);
      delete appData.bookmarks.questionNotes[code];
      saveData();
      updateSavedCount();
      renderSavedQuestions();
      showToast('Domanda rimossa');
    });

    container.appendChild(card);
  });
}

function renderSavedQuestions() {
  buildSavedQuestionCards(document.getElementById('saved-questions-list'));
}

function renderTheorySavedRefs() {
  const container = document.getElementById('theory-saved-refs-list');
  const codes = appData.bookmarks.refs;
  if (codes.length === 0) {
    container.innerHTML = '<div class="refs-empty">Nessun riferimento salvato</div>';
    return;
  }
  container.innerHTML = '';
  codes.forEach(code => {
    const r = REF_MAP[code];
    if (!r) return;
    const card = document.createElement('div');
    card.className = 'ref-card';
    const title = r['ref-title'] || '';
    const preview = escapeHtml(title || r['ref-text'].substring(0, 80) + (r['ref-text'].length > 80 ? '...' : ''));
    const srcClass = SOURCE_CLASS[r['ref-from']] || 'rg';

    const refNote = appData.bookmarks.refNotes[code] || '';
    card.innerHTML =
      '<div class="ref-card-header" tabindex="0" role="button" aria-expanded="false">' +
        '<span class="ref-source-tag ' + srcClass + '">' + (SOURCE_SHORT[r['ref-from']] || r['ref-from']) + '</span>' +
        '<span class="ref-card-code">' + r['ref-code'] + '</span>' + changeBadgeHtml(r['ref-change']) +
        '<span class="ref-card-preview">' + preview + '</span>' +
        '<span class="ref-card-actions">' +
          '<button class="ref-edit-note-btn" data-ref="' + r['ref-code'] + '" title="' + (refNote ? 'Modifica nota' : 'Aggiungi nota') + '">' +
            '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11.5 1.5l3 3L5 14H2v-3z"/></svg>' +
          '</button>' +
          '<button class="ref-bm-btn bookmarked" data-ref="' + r['ref-code'] + '" aria-label="Rimuovi riferimento">' +
            '<svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" stroke="currentColor" stroke-width="1.5"><path d="M4 2h8a1 1 0 011 1v11.5l-4.5-3-4.5 3V3a1 1 0 011-1z"/></svg>' +
          '</button>' +
          '<span class="ref-expand-icon">&#9660;</span>' +
        '</span>' +
      '</div>' +
      (refNote ? '<div class="saved-q-note"><strong>Nota:</strong> ' + refNote.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</div>' : '') +
      '<div class="ref-card-body">' +
        changeNoteHtml(r['ref-change']) +
        '<div class="ref-summary-text">' + escapeHtml(r['ref-text']) + '</div>' +
        (r['ref-full-text'] ?
          '<div class="ref-fulltext-wrapper">' +
            (r['ref-full-text'].trim() === r['ref-text'].trim() ?
              '<span class="ref-fulltext-same">testo completo uguale al riassunto</span>' :
              '<button class="ref-fulltext-toggle">Nascondi dettagli</button>' +
              '<div class="ref-fulltext-content">' + r['ref-full-text'].replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g, '<br>') + '</div>') +
          '</div>' : '') +
      '</div>';

    const header = card.querySelector('.ref-card-header');
    header.addEventListener('click', e => {
      if (e.target.closest('.ref-bm-btn')) return;
      card.classList.toggle('expanded');
      header.setAttribute('aria-expanded', card.classList.contains('expanded'));
    });
    header.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); header.click(); }
    });

    const editNoteBtn = card.querySelector('.ref-edit-note-btn');
    editNoteBtn.addEventListener('click', async (e) => {
      e.stopPropagation();
      const refCode = editNoteBtn.dataset.ref;
      const existing = appData.bookmarks.refNotes[refCode] || '';
      const note = await showBmNoteModal('Modifica nota — ' + refCode, existing);
      if (note === null) return;
      if (note) appData.bookmarks.refNotes[refCode] = note;
      else delete appData.bookmarks.refNotes[refCode];
      saveData();
      renderTheorySavedRefs();
      showToast(note ? 'Nota aggiornata' : 'Nota rimossa');
    });

    const bmBtn = card.querySelector('.ref-bm-btn');
    bmBtn.addEventListener('click', e => {
      e.stopPropagation();
      const refCode = bmBtn.dataset.ref;
      const idx = appData.bookmarks.refs.indexOf(refCode);
      if (idx >= 0) appData.bookmarks.refs.splice(idx, 1);
      delete appData.bookmarks.refNotes[refCode];
      saveData();
      updateSavedCount();
      renderTheorySavedRefs();
      showToast('Riferimento rimosso');
    });

    card.querySelectorAll('.ref-fulltext-toggle').forEach(btn => {
      btn.addEventListener('click', e => {
        e.stopPropagation();
        const content = btn.nextElementSibling;
        const isOpen = !content.hidden;
        content.hidden = isOpen;
        btn.textContent = isOpen ? 'Mostra dettagli' : 'Nascondi dettagli';
      });
    });

    container.appendChild(card);
  });
}

// ======= OFFICIAL DOCUMENTS =======
// The PDFs live in the documenti/ folder. If a file is missing, its card says so:
// uploading it with the name given in "file" makes it downloadable.
const DOCUMENTS = [
  { source: 'Regole di gioco', title: 'Regole di Gioco', edition: '2025–2028', file: 'regole-di-gioco-2025-2028.pdf',
    desc: 'Il regolamento ufficiale della pallavolo: campo, squadre, svolgimento del gioco, interruzioni, falli e sanzioni.' },
  { source: 'Casistica', title: 'Casistica Ufficiale delle Regole di Gioco', edition: '2025–2028, 1ª edizione', file: 'casistica-ufficiale-2025-2028.pdf',
    desc: 'Casi di gioco reali con la decisione corretta e la regola applicata.' },
  { source: 'Indicazioni tecniche', title: 'Indicazioni Tecniche', edition: 'Stagione 2025/2026', file: 'indicazioni-tecniche-2025-2026.pdf',
    desc: 'Interpretazioni e indicazioni operative per arbitri e ufficiali di gara.' },
  { source: 'Normative federali', title: 'Normative Federali', edition: 'Stagione 2026/2027, con modifiche evidenziate', file: 'normative-federali-2026-2027.pdf',
    desc: 'Norme della stagione, comprese le precisazioni sulle sperimentazioni regolamentari.' },
  { source: 'Vademecum', title: 'Vademecum (estratti dalla Guida Pratica)', edition: 'Stagione 2026/2027', file: 'vademecum-2026-2027.pdf',
    desc: 'Controlli prima della gara, documenti dei partecipanti, categorie, limiti di età e altezze della rete.',
    alt: { label: 'Guida Pratica 2026/27 sul sito FIPAV', url: 'https://guidapratica.federvolley.it/campionati-indoor/campionati/gp_campionati_2627.pdf' } },
];
// "Other useful documents": they come from the API and administrators manage them from the panel
function extraDocuments() {
  return (window.APP_CONTENT.documents || []).map(d => ({
    title: d.title, edition: d.edition, desc: d.description, url: d.file + (d.version ? '?v=' + d.version : ''), size: d.size, managed: true,
  }));
}
const docInfo = {};   // file -> { ok, size }
let docsChecked = false;

function docBySource(src) { return DOCUMENTS.find(d => d.source === src); }
function docLinkHtml(src) {
  const d = docBySource(src);
  if (!d || (docsChecked && docInfo[d.file] && !docInfo[d.file].ok)) return '';
  return '<a class="ref-doc-link" href="' + docUrl(d) + '" target="_blank" rel="noopener">' +
    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>' +
    'Apri ' + escapeHtml(d.title) + '</a>';
}
function fmtSize(bytes) {
  if (!bytes) return '';
  return bytes >= 1048576 ? (bytes / 1048576).toFixed(1).replace('.', ',') + ' MB' : Math.round(bytes / 1024) + ' kB';
}
function docUrl(d) { return d.url || ('documenti/' + d.file); }
// Check which official PDFs exist (the API already knows the ones managed from the panel)
async function checkDocuments() {
  await Promise.all(DOCUMENTS.map(async d => {
    try {
      const res = await fetch('documenti/' + d.file, { method: 'HEAD', cache: 'no-cache' });
      const type = res.headers.get('content-type') || '';
      docInfo[d.file] = { ok: res.ok && type.indexOf('pdf') >= 0, size: parseInt(res.headers.get('content-length') || '0', 10) };
    } catch (e) { docInfo[d.file] = { ok: false, size: 0 }; }
  }));
  docsChecked = true;
}
function docCardHtml(d) {
  const info = d.managed ? { ok: true, size: d.size } : (docInfo[d.file] || {});
  const cls = d.source ? SOURCE_CLASS[d.source] : '';
  const href = docUrl(d);
  const available = d.managed || !docsChecked || info.ok;
  const qCount = d.source ? QUESTIONS.filter(q => getQuestionSources(q).has(d.source)).length : 0;
  const rCount = d.source ? REFS.filter(r => r['ref-from'] === d.source).length : 0;
  return '<article class="doc-card ' + cls + '">' +
    '<div class="doc-head"><div class="doc-icon">PDF</div><div><div class="doc-title">' + escapeHtml(d.title) + '</div>' +
      '<div class="doc-edition">' + escapeHtml(d.edition) + '</div></div></div>' +
    (d.desc ? '<p class="doc-desc">' + escapeHtml(d.desc) + '</p>' : '') +
    '<div class="doc-meta">' + (d.source ? '<span>' + qCount + ' domande</span><span>' + rCount + ' riferimenti</span>' : '') +
      (info.size ? '<span>' + fmtSize(info.size) + '</span>' : '') + '</div>' +
    (available
      ? '<div class="doc-actions"><a class="btn btn-primary btn-sm" href="' + href + '" download>Scarica PDF</a>' +
        '<a class="btn btn-secondary btn-sm" href="' + href + '" target="_blank" rel="noopener">Apri</a>' +
        (d.source ? consultBtn(d.source) : '') + '</div>'
      : '<div class="doc-missing">Il PDF non è ancora stato caricato sul portale.' +
        (d.alt ? ' Nel frattempo puoi consultare la <a href="' + d.alt.url + '" target="_blank" rel="noopener">' + escapeHtml(d.alt.label) + '</a>.' : '') + '</div>' +
        (d.source ? '<div class="doc-actions">' + consultBtn(d.source) + '</div>' : '')) +
  '</article>';
}
function consultBtn(src) {
  return '<button type="button" class="btn btn-ghost btn-sm doc-consult" data-consult="' + escapeHtml(src) + '" title="Apri i riferimenti di questa fonte nel Ripasso">' +
    '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4h6a4 4 0 014 4v13a3 3 0 00-3-3H2z"/><path d="M22 4h-6a4 4 0 00-4 4v13a3 3 0 013-3h7z"/></svg>' +
    'Consulta</button>';
}

// Open Review > References showing only the given source
function openTheoryForSource(src) {
  showSection('theory');
  const subTabs = document.querySelectorAll('#theory-sub-tabs .sub-tab');
  subTabs.forEach(b => b.classList.toggle('active', b.dataset.sub === 'theory-refs'));
  document.querySelectorAll('#sec-theory > .sub-section').forEach(ss => ss.classList.toggle('active', ss.id === 'sub-theory-refs'));
  document.getElementById('theory-search').value = '';
  theoryFilterSources.clear();
  theoryFilterSources.add(src);
  theoryOnlyChanges = false;
  theoryShowBookmarks = false;
  document.getElementById('theory-bm-toggle').checked = false;
  refsLimit = REFS_PAGE;
  updateTheoryChips();
  renderTheory();
}
['docs-list', 'docs-extra'].forEach(id => document.getElementById(id).addEventListener('click', e => {
  const b = e.target.closest('[data-consult]');
  if (b) openTheoryForSource(b.dataset.consult);
}));

async function renderDocuments() {
  const main = document.getElementById('docs-list');
  const extra = document.getElementById('docs-extra');
  const extras = extraDocuments();
  document.getElementById('docs-extra-title').hidden = !extras.length;
  extra.hidden = !extras.length;
  extra.innerHTML = extras.map(docCardHtml).join('');
  main.innerHTML = DOCUMENTS.map(docCardHtml).join('');
  if (!docsChecked) {
    await checkDocuments();
    main.innerHTML = DOCUMENTS.map(docCardHtml).join('');
  }
}

function normText(s) { return String(s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); }

// ======= RELEASE NOTES (from assets/version.js) =======
function renderReleases() {
  const box = document.getElementById('release-list');
  const list = window.APP_RELEASES || [];
  const fmt = d => {
    if (!d) return '';
    const parts = d.split('-').map(Number);
    const months = ['gennaio','febbraio','marzo','aprile','maggio','giugno','luglio','agosto','settembre','ottobre','novembre','dicembre'];
    return parts.length === 3 ? parts[2] + ' ' + months[parts[1] - 1] + ' ' + parts[0] : months[parts[1] - 1] + ' ' + parts[0];
  };
  box.innerHTML = list.map((r, i) =>
    '<article class="release' + (i === 0 ? ' current' : '') + '" id="v' + escapeHtml(r.version) + '">' +
      '<header class="release-head"><span class="release-version scoreboard">v' + escapeHtml(r.version) + '</span>' +
        (i === 0 ? '<span class="pill-current">Versione attuale</span>' : '') +
        (r.date ? '<time class="release-date" datetime="' + escapeHtml(r.date) + '">' + fmt(r.date) + '</time>' : '') + '</header>' +
      '<h2 class="release-title">' + escapeHtml(r.title) + '</h2>' +
      r.groups.map(g => (g.title ? '<h3 class="release-group">' + escapeHtml(g.title) + '</h3>' : '') +
        '<ul class="release-items">' + g.items.map(it => '<li>' + escapeHtml(it) + '</li>').join('') + '</ul>').join('') +
    '</article>').join('');
}

// ======= INIT =======
document.getElementById('app-version').textContent = window.APP_VERSION_LABEL || ('v' + APP_VERSION.major + '.' + APP_VERSION.minor + '.' + APP_VERSION.patch);
initSourceFilters();
initDifficultyFilters();
updateMaxCount();
initTheoryFilters();
renderStats();
updateSavedCount();
restoreAutoSave();
renderSuspendedSessions();
checkDocuments();
showSection(HASH_SECTION[location.hash.slice(1)] || 'quiz', { fromHash: true });
if (!location.hash) history.replaceState(null, '', '#quiz');

// ======= ACCOUNT AND PROFILE =======
(function initAccount() {
  const user = window.APP_USER;
  const $ = id => document.getElementById(id);
  const initials = n => n.split(/\s+/).filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase() || '?';
  const fmtDay = utc => { if (!utc) return ''; const d = new Date(String(utc).replace(' ', 'T') + 'Z'); return isNaN(d) ? '' : d.toLocaleDateString('it-IT', { day: 'numeric', month: 'long', year: 'numeric' }); };

  function paintUser() {
    $('account-name').textContent = user.name;
    $('account-email').textContent = user.email;
    Avatar.apply($('btn-settings'), user.name, user.gravatar, { size: 42 });
    Avatar.apply($('menu-initials'), user.name, user.gravatar, { size: 42 });
    ['account-role', 'profile-role'].forEach(id => {
      const el = $(id);
      el.textContent = user.role;
      el.classList.toggle('editor', user.canEdit && !user.canManageUsers);
      el.classList.toggle('admin', user.canManageUsers && !user.isMaster);
      el.classList.toggle('master', !!user.isMaster);
    });
    Avatar.apply($('profile-initials'), user.name, user.gravatar, { size: 72 });
    $('profile-name').textContent = user.name;
    $('profile-email').textContent = user.email;
    $('profile-since').textContent = user.createdAt ? 'Iscritto dal ' + fmtDay(user.createdAt) : '';
  }
  paintUser();
  $('btn-admin').hidden = !user.canEdit;
  $('nav-admin').hidden = !user.canEdit;

  const ROLE_PERMS = [
    [1, 'Fare quiz, ripassare i riferimenti, consultare i documenti e vedere le tue statistiche'],
    [5, 'Creare e modificare domande e riferimenti, e leggere le segnalazioni degli utenti'],
    [9, 'Abilitare i nuovi account e gestire utenti e moderatori'],
    [10, 'Gestire gli amministratori e passare il ruolo di master admin'],
  ];

  function goSub(section, sub) {
    showSection(section);
    const btn = document.querySelector('.sub-tab[data-sub="' + sub + '"]');
    if (btn) btn.click();
  }

  window.renderProfile = function () {
    paintUser();
    $('profile-name-input').value = user.name;
    $('profile-save').disabled = true;
    $('profile-email-input').value = user.email;
    $('pw-username').value = user.email;
    const s = appData.stats;
    const total = s.correct + s.wrong;
    const items = [
      [total, total === 1 ? 'risposta data' : 'risposte date', 'stats'],
      [total ? Math.round(s.correct / total * 100) + '%' : '–', 'risposte corrette', 'stats'],
      [appData.bookmarks.questions.length, 'domande salvate', 'quiz-saved'],
      [appData.bookmarks.refs.length, 'riferimenti salvati', 'theory-saved'],
    ];
    $('profile-stats').innerHTML = items.map(([v, l, go]) =>
      '<button type="button" class="profile-stat" data-go="' + go + '"><span class="scoreboard">' + v + '</span><small>' + l + '</small></button>').join('');
    $('profile-perms').innerHTML = ROLE_PERMS.filter(([lvl]) => user.permissions >= lvl).map(([, t]) => '<li>' + t + '</li>').join('');
    $('profile-admin-link').hidden = !user.canEdit;
    showAvatarStatus(false);
  };

  // Profile picture from Gravatar (initials as a fallback)
  function showAvatarStatus(refresh) {
    $('avatar-status').textContent = 'Controllo Gravatar…';
    Avatar.apply($('avatar-preview'), user.name, user.gravatar, { size: 64, refresh, onResult: found => {
      $('avatar-status').textContent = found
        ? 'Usi l’immagine del tuo account Gravatar collegato a ' + user.email + '. Per cambiarla, modificala su Gravatar.'
        : 'Nessuna immagine su Gravatar per ' + user.email + ', quindi mostriamo le tue iniziali. Per aggiungerla crea un account Gravatar con questa email, poi premi «Aggiorna».';
      if (refresh) {
        Avatar.apply($('btn-settings'), user.name, user.gravatar, { size: 42, refresh: true });
        Avatar.apply($('menu-initials'), user.name, user.gravatar, { size: 42, refresh: true });
        Avatar.apply($('profile-initials'), user.name, user.gravatar, { size: 72, refresh: true });
      }
    } });
  }
  $('avatar-refresh').addEventListener('click', () => showAvatarStatus(true));
  $('profile-stats').addEventListener('click', e => {
    const b = e.target.closest('[data-go]');
    if (!b) return;
    if (b.dataset.go === 'stats') showSection('stats');
    else if (b.dataset.go === 'quiz-saved') goSub('quiz', 'quiz-saved');
    else goSub('theory', 'theory-saved');
  });
  $('profile-name-input').addEventListener('input', e => {
    const v = e.target.value.trim();
    $('profile-save').disabled = v === user.name || v.length < 2;
  });

  // Opening and closing the password form
  function setPwOpen(open) {
    $('password-form').hidden = !open;
    $('pw-closed').hidden = open;
    if (open) { setTimeout(() => $('pw-current').focus(), 0); }
    else { $('password-form').reset(); $('password-error').hidden = true; if (window.PwStrength) PwStrength.reset('pw-new'); }
  }
  $('pw-open').addEventListener('click', () => setPwOpen(true));
  $('pw-cancel').addEventListener('click', () => setPwOpen(false));

  // Save status indicator
  const syncEl = $('sync-indicator');
  const syncLabel = $('sync-label');
  const SYNC_TEXT = { saving: 'Salvataggio…', saved: 'Salvato', error: 'Non salvato · riprovo', auth: 'Sessione scaduta' };
  let lastError = 0;
  Store.onStatus(state => {
    syncEl.dataset.state = state;
    syncLabel.textContent = SYNC_TEXT[state] || '';
    syncEl.title = state === 'error' ? 'Connessione assente: i progressi verranno salvati appena possibile.' : 'I tuoi progressi sono salvati sul tuo account';
    if (state === 'error' && Date.now() - lastError > 30000) {
      lastError = Date.now();
      showToast('Salvataggio non riuscito: riprovo appena torna la connessione');
    }
  });

  // Sign out (from the menu and from the profile)
  async function logout() {
    setMenuOpen(false);
    try { await Store.flush(); } catch (e) {}
    if (Store.hasPending()) {
      const ok = await showModal('Uscire comunque?', 'Alcuni progressi non sono ancora stati salvati sul server. Se esci ora potrebbero andare persi.', 'Esci');
      if (!ok) return;
    }
    try { await Api.logout(); } catch (e) {}
    window.location.replace(location.pathname);
  }
  $('btn-logout').addEventListener('click', logout);
  $('btn-logout-profile').addEventListener('click', logout);

  // Name
  $('profile-form').addEventListener('submit', async e => {
    e.preventDefault();
    const err = $('profile-error');
    err.hidden = true;
    const name = $('profile-name-input').value.trim();
    if (name.length < 2) { err.textContent = 'Inserisci nome e cognome.'; err.hidden = false; return; }
    if (name === user.name) return;
    const btn = $('profile-save');
    btn.disabled = true;
    try {
      const res = await Api.post('auth.profile', { name });
      user.name = res.user.name;
      window.renderProfile();
      showToast('Modifiche salvate');
    } catch (ex) {
      err.textContent = ex.message;
      err.hidden = false;
    } finally {
      btn.disabled = $('profile-name-input').value.trim() === user.name;
    }
  });

  // Password
  const pwError = $('password-error');
  $('password-form').addEventListener('submit', async e => {
    e.preventDefault();
    const current = $('pw-current').value;
    const next = $('pw-new').value;
    pwError.hidden = true;
    if (!current) { pwError.textContent = 'Inserisci la password attuale.'; pwError.hidden = false; return; }
    const problem = window.PwStrength ? PwStrength.problem(next, user) : '';
    if (problem) { pwError.textContent = problem; pwError.hidden = false; return; }
    if (next !== $('pw-new2').value) { pwError.textContent = 'Le due password non coincidono.'; pwError.hidden = false; return; }
    const btn = $('password-save');
    btn.disabled = true;
    try {
      await Api.post('auth.password', { current, new: next });
      setPwOpen(false);
      showToast('Password cambiata');
    } catch (err) {
      pwError.textContent = err.message;
      pwError.hidden = false;
    } finally {
      btn.disabled = false;
    }
  });

  if (currentSection === 'profile') window.renderProfile();
})();

// ======= INSTALLABLE WEB APP =======
(function initInstall() {
  const $ = id => document.getElementById(id);
  const BANNER_KEY = 'fipav-install-banner-dismissed';
  const isPhone = window.matchMedia('(pointer: coarse)').matches && window.innerWidth < 900;
  // Installing is offered only on phones and tablets: on desktop the menu item and the profile section are hidden
  const isMobileDevice = Install.isIOS() || /android|mobile/i.test(navigator.userAgent) ||
    (window.matchMedia('(pointer: coarse)').matches && !window.matchMedia('(pointer: fine)').matches);
  let dismissed = false;
  try { dismissed = localStorage.getItem(BANNER_KEY) === '1'; } catch (e) {}

  function showSteps(mode) {
    const steps = mode === 'ios'
      ? ['Tocca il pulsante <strong>Condividi</strong> del browser (il quadrato con la freccia verso l’alto).',
         'Scorri e scegli <strong>Aggiungi alla schermata Home</strong>.',
         'Conferma con <strong>Aggiungi</strong>: troverai l’icona di Fischietto tra le tue app.']
      : ['Apri il menu del browser (di solito i tre puntini <strong>⋮</strong>).',
         'Scegli <strong>Installa app</strong> oppure <strong>Aggiungi a schermata Home</strong>.',
         'Conferma: troverai l’icona di Fischietto tra le tue app.'];
    $('install-steps').innerHTML = steps.map(s => '<li>' + s + '</li>').join('');
    $('ios-install-modal').hidden = false;
  }
  $('ios-install-close').addEventListener('click', () => { $('ios-install-modal').hidden = true; });

  async function install() {
    setMenuOpen(false);
    const mode = Install.mode();
    if (mode === 'prompt') {
      const ok = await Install.prompt();
      if (ok) showToast('Fischietto è stato installato');
    } else if (mode !== 'installed') {
      showSteps(mode);
    }
  }
  document.querySelectorAll('[data-install]').forEach(b => b.addEventListener('click', install));
  $('btn-install').addEventListener('click', install);
  $('install-banner-close').addEventListener('click', () => {
    $('install-banner').hidden = true;
    try { localStorage.setItem(BANNER_KEY, '1'); } catch (e) {}
  });

  Install.onChange(mode => {
    const installed = mode === 'installed';
    $('btn-install').hidden = installed || !isMobileDevice;
    // Already running as the installed app: nothing to offer
    $('profile-app').hidden = !isMobileDevice || installed;
    $('install-banner').hidden = installed || dismissed || !isPhone;
    $('install-status-title').textContent = installed ? 'App installata' : 'Installa l’app';
    $('install-status-text').textContent = installed
      ? 'Stai usando Fischietto come app.'
      : mode === 'prompt' ? 'Il browser può installarla con un tocco.'
      : mode === 'ios' ? 'Su iPhone e iPad si aggiunge dal pulsante Condividi del browser.'
      : 'Si aggiunge dal menu del browser: «Installa app» o «Aggiungi a schermata Home».';
    $('profile-install-btn').hidden = installed;
    $('profile-install-btn').textContent = mode === 'prompt' ? 'Installa' : 'Come si fa';
  });
})();
