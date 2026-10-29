<?php
/** Questions and references: reading, validation and saving. */

declare(strict_types=1);

const SOURCE_PREFIX = [
    'Regole di gioco'      => 'RG',
    'Casistica'            => 'CAS',
    'Indicazioni tecniche' => 'IT',
    'Normative federali'   => 'NF',
    'Vademecum'            => 'VAD',
];

function bump_content_version(): void
{
    meta_set('content_version', (string)time());
}

function sort_refs(array &$refs): void
{
    $order = array_flip(SOURCES);
    usort($refs, function ($a, $b) use ($order) {
        $sa = $order[$a['ref-from']] ?? 99;
        $sb = $order[$b['ref-from']] ?? 99;
        if ($sa !== $sb) return $sa <=> $sb;
        return strnatcmp($a['ref-code'], $b['ref-code']);
    });
}

/**
 * Returns questions and references in the same format as the original portal,
 * so the frontend does not need to change.
 */
function fetch_content(bool $forAdmin = false): array
{
    $pdo = db();

    $refs = [];
    $usage = [];
    if ($forAdmin) {
        foreach ($pdo->query('SELECT ref_code, COUNT(*) n FROM ' . t('question_refs') . ' GROUP BY ref_code') as $r) {
            $usage[$r['ref_code']] = (int)$r['n'];
        }
    }
    foreach ($pdo->query('SELECT code, source, title, summary, full_text, change_season, change_type, change_note, updated_at FROM ' . t('refs')) as $r) {
        $item = [
            'ref-code'      => $r['code'],
            'ref-title'     => $r['title'],
            'ref-text'      => $r['summary'],
            'ref-full-text' => $r['full_text'],
            'ref-from'      => $r['source'],
        ];
        if (!empty($r['change_season']) && !empty($r['change_type'])) {
            $item['ref-change'] = ['season' => $r['change_season'], 'type' => $r['change_type'], 'note' => (string)$r['change_note']];
        }
        if ($forAdmin) {
            $item['usage'] = $usage[$r['code']] ?? 0;
            $item['updated_at'] = $r['updated_at'];
        }
        $refs[] = $item;
    }
    sort_refs($refs);

    $where = $forAdmin ? '' : ' WHERE active = 1';
    $questions = [];
    foreach ($pdo->query('SELECT id, code, text, type, difficulty, active, updated_at FROM ' . t('questions') . $where) as $q) {
        $questions[(int)$q['id']] = [
            'question-code'       => $q['code'],
            'question-text'       => $q['text'],
            'question-type'       => $q['type'],
            'question-answers'    => [],
            'question-ref'        => [],
            'question-difficulty' => $q['difficulty'],
        ];
        if ($forAdmin) {
            $questions[(int)$q['id']]['id'] = (int)$q['id'];
            $questions[(int)$q['id']]['active'] = (bool)$q['active'];
            $questions[(int)$q['id']]['updated_at'] = $q['updated_at'];
        }
    }
    foreach ($pdo->query('SELECT question_id, value, text, is_correct FROM ' . t('answers') . ' ORDER BY question_id, position') as $a) {
        $qid = (int)$a['question_id'];
        if (!isset($questions[$qid])) continue;
        $questions[$qid]['question-answers'][] = ['correct' => (bool)$a['is_correct'], 'text' => $a['text'], 'value' => $a['value']];
    }
    foreach ($pdo->query('SELECT question_id, ref_code FROM ' . t('question_refs') . ' ORDER BY question_id, position') as $r) {
        $qid = (int)$r['question_id'];
        if (!isset($questions[$qid])) continue;
        $questions[$qid]['question-ref'][] = $r['ref_code'];
    }
    $questions = array_values($questions);
    usort($questions, function ($a, $b) { return strnatcmp($a['question-code'], $b['question-code']); });

    return [
        'version'   => meta_get('content_version', '0'),
        'refs'      => $refs,
        'questions' => $questions,
    ];
}

// ---------------- References ----------------

function validate_ref(array $in): array
{
    $code   = str_param($in, 'code', 32, true);
    $source = str_param($in, 'source', 40, true);
    if (!in_array($source, SOURCES, true)) fail('Fonte non valida.', 422, 'validation');
    $prefix = SOURCE_PREFIX[$source];
    if (!preg_match('/^(RG|CAS|IT|NF|VAD)-[0-9A-Za-z.]+$/', $code)) {
        fail('Codice non valido. Formato atteso: ' . $prefix . '-1.2.3', 422, 'validation');
    }
    if (strpos($code, $prefix . '-') !== 0) {
        fail("Per la fonte \"$source\" il codice deve iniziare con \"$prefix-\".", 422, 'validation');
    }
    // Rulebook change: season (e.g. 2026/27) and type, or none
    $changeType = str_param($in, 'change_type', 10);
    $changeSeason = str_param($in, 'change_season', 7);
    $changeNote = str_param($in, 'change_note', 300);
    if ($changeType === '') {
        $changeSeason = '';
        $changeNote = '';
    } else {
        if (!in_array($changeType, ['new', 'changed'], true)) fail('Tipo di modifica non valido.', 422, 'validation');
        if (!preg_match('/^(\d{4})\/(\d{2})$/', $changeSeason, $m) || (((int)$m[1] + 1) % 100) !== (int)$m[2]) {
            fail('Stagione non valida: usa il formato 2026/27.', 422, 'validation');
        }
    }
    return [
        'code'      => $code,
        'source'    => $source,
        'title'     => str_param($in, 'title', 255, true),
        'summary'   => str_param($in, 'summary', 5000, true),
        'full_text' => str_param($in, 'full_text', 60000),
        'change_season' => $changeSeason === '' ? null : $changeSeason,
        'change_type'   => $changeType === '' ? null : $changeType,
        'change_note'   => $changeNote,
    ];
}

function save_ref(array $in, int $userId): array
{
    $ref = validate_ref($in);
    $original = str_param($in, 'original_code', 32);
    $pdo = db();
    $exists = function (string $code) use ($pdo): bool {
        $st = $pdo->prepare('SELECT 1 FROM ' . t('refs') . ' WHERE code = ?');
        $st->execute([$code]);
        return (bool)$st->fetchColumn();
    };

    if ($original === '') {
        if ($exists($ref['code'])) fail('Esiste già un riferimento con codice ' . $ref['code'] . '.', 409, 'duplicate');
        $pdo->prepare('INSERT INTO ' . t('refs') . ' (code, source, title, summary, full_text, change_season, change_type, change_note, created_at, updated_at, updated_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$ref['code'], $ref['source'], $ref['title'], $ref['summary'], $ref['full_text'], $ref['change_season'], $ref['change_type'], $ref['change_note'], now_utc(), now_utc(), $userId]);
    } else {
        if (!$exists($original)) fail('Riferimento non trovato.', 404, 'not_found');
        if ($original !== $ref['code'] && $exists($ref['code'])) fail('Esiste già un riferimento con codice ' . $ref['code'] . '.', 409, 'duplicate');
        // A code change propagates to the linked questions (ON UPDATE CASCADE)
        $pdo->prepare('UPDATE ' . t('refs') . ' SET code = ?, source = ?, title = ?, summary = ?, full_text = ?, change_season = ?, change_type = ?, change_note = ?, updated_at = ?, updated_by = ? WHERE code = ?')
            ->execute([$ref['code'], $ref['source'], $ref['title'], $ref['summary'], $ref['full_text'], $ref['change_season'], $ref['change_type'], $ref['change_note'], now_utc(), $userId, $original]);
    }
    bump_content_version();
    return $ref;
}

function delete_ref(string $code): void
{
    $pdo = db();
    $st = $pdo->prepare('SELECT q.code FROM ' . t('question_refs') . ' qr JOIN ' . t('questions') . ' q ON q.id = qr.question_id WHERE qr.ref_code = ? ORDER BY q.code');
    $st->execute([$code]);
    $used = $st->fetchAll(PDO::FETCH_COLUMN);
    if ($used) {
        fail('Il riferimento è usato da ' . count($used) . ' domande (' . implode(', ', array_slice($used, 0, 8)) . (count($used) > 8 ? '…' : '') . '). Rimuovilo prima da quelle domande.', 409, 'in_use', ['questions' => $used]);
    }
    $st = $pdo->prepare('DELETE FROM ' . t('refs') . ' WHERE code = ?');
    $st->execute([$code]);
    if ($st->rowCount() === 0) fail('Riferimento non trovato.', 404, 'not_found');
    bump_content_version();
}

// ---------------- Questions ----------------

function next_question_code(): string
{
    $max = 0;
    foreach (db()->query('SELECT code FROM ' . t('questions')) as $r) {
        if (preg_match('/^Q(\d+)$/', $r['code'], $m)) $max = max($max, (int)$m[1]);
    }
    return 'Q' . ($max + 1);
}

/** Validates and normalizes a question coming from the management panel. */
function validate_question(array $in): array
{
    $text = str_param($in, 'text', 3000, true);
    $type = str_param($in, 'type', 20, true);
    if (!in_array($type, QUESTION_TYPES, true)) fail('Tipo di domanda non valido.', 422, 'validation');
    $difficulty = str_param($in, 'difficulty', 4, true);
    if (!in_array($difficulty, DIFFICULTIES, true)) fail('Difficoltà non valida.', 422, 'validation');

    $rawAnswers = $in['answers'] ?? [];
    if (!is_array($rawAnswers)) fail('Risposte non valide.', 422, 'validation');
    $answers = [];
    foreach (array_values($rawAnswers) as $a) {
        if (!is_array($a)) continue;
        $t = trim((string)($a['text'] ?? ''));
        if ($t === '') continue;
        if (mb_strlen($t) > 1000) fail('Una risposta è troppo lunga (max 1000 caratteri).', 422, 'validation');
        $answers[] = ['text' => $t, 'correct' => !empty($a['correct'])];
    }

    if ($type === 'yes-no') {
        $trueCorrect = null;
        foreach ($rawAnswers as $a) {
            if (is_array($a) && (($a['value'] ?? '') === 'V' || ($a['text'] ?? '') === 'Vero')) $trueCorrect = !empty($a['correct']);
        }
        if ($trueCorrect === null) fail('Indica se l\'affermazione è vera o falsa.', 422, 'validation');
        $answers = [
            ['text' => 'Vero',  'correct' => $trueCorrect,  'value' => 'V'],
            ['text' => 'Falso', 'correct' => !$trueCorrect, 'value' => 'F'],
        ];
    } else {
        $n = count($answers);
        $correct = count(array_filter($answers, function ($a) { return $a['correct']; }));
        if ($n < 2) fail('Servono almeno 2 risposte.', 422, 'validation');
        if ($n > 10) fail('Massimo 10 risposte.', 422, 'validation');
        if ($type === 'multiple-choice' && $correct !== 1) fail('Nella scelta multipla deve esserci esattamente una risposta corretta.', 422, 'validation');
        if ($type === 'multi-select' && $correct < 1) fail('Seleziona almeno una risposta corretta.', 422, 'validation');
        $texts = array_map(function ($a) { return mb_strtolower($a['text']); }, $answers);
        if (count(array_unique($texts)) !== $n) fail('Ci sono risposte duplicate.', 422, 'validation');
        foreach ($answers as $i => &$a) $a['value'] = chr(65 + $i);
        unset($a);
    }

    $refs = $in['refs'] ?? [];
    if (!is_array($refs)) fail('Riferimenti non validi.', 422, 'validation');
    $refs = array_values(array_unique(array_filter(array_map(function ($r) { return trim((string)$r); }, $refs), 'strlen')));
    if (count($refs) < 1) fail('Collega almeno un riferimento normativo.', 422, 'validation');
    if (count($refs) > 15) fail('Massimo 15 riferimenti per domanda.', 422, 'validation');
    $place = implode(',', array_fill(0, count($refs), '?'));
    $st = db()->prepare('SELECT code FROM ' . t('refs') . " WHERE code IN ($place)");
    $st->execute($refs);
    $found = $st->fetchAll(PDO::FETCH_COLUMN);
    $missing = array_values(array_diff($refs, $found));
    if ($missing) fail('Riferimenti inesistenti: ' . implode(', ', $missing), 422, 'validation');

    return [
        'text'       => $text,
        'type'       => $type,
        'difficulty' => $difficulty,
        'active'     => !array_key_exists('active', $in) || !empty($in['active']),
        'answers'    => $answers,
        'refs'       => $refs,
    ];
}

function write_question_children(int $qid, array $q): void
{
    $pdo = db();
    $pdo->prepare('DELETE FROM ' . t('answers') . ' WHERE question_id = ?')->execute([$qid]);
    $pdo->prepare('DELETE FROM ' . t('question_refs') . ' WHERE question_id = ?')->execute([$qid]);
    $insA = $pdo->prepare('INSERT INTO ' . t('answers') . ' (question_id, position, value, text, is_correct) VALUES (?,?,?,?,?)');
    foreach ($q['answers'] as $i => $a) $insA->execute([$qid, $i, $a['value'], $a['text'], $a['correct'] ? 1 : 0]);
    $insR = $pdo->prepare('INSERT INTO ' . t('question_refs') . ' (question_id, ref_code, position) VALUES (?,?,?)');
    foreach ($q['refs'] as $i => $code) $insR->execute([$qid, $code, $i]);
}

function save_question(array $in, int $userId): string
{
    $q = validate_question($in);
    $pdo = db();
    $code = str_param($in, 'code', 32);
    $pdo->beginTransaction();
    try {
        if ($code === '') {
            $code = next_question_code();
            $pdo->prepare('INSERT INTO ' . t('questions') . ' (code, text, type, difficulty, active, created_at, updated_at, updated_by) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$code, $q['text'], $q['type'], $q['difficulty'], $q['active'] ? 1 : 0, now_utc(), now_utc(), $userId]);
            $qid = (int)$pdo->lastInsertId();
        } else {
            $st = $pdo->prepare('SELECT id FROM ' . t('questions') . ' WHERE code = ? FOR UPDATE');
            $st->execute([$code]);
            $qid = (int)$st->fetchColumn();
            if (!$qid) fail('Domanda non trovata.', 404, 'not_found');
            $pdo->prepare('UPDATE ' . t('questions') . ' SET text = ?, type = ?, difficulty = ?, active = ?, updated_at = ?, updated_by = ? WHERE id = ?')
                ->execute([$q['text'], $q['type'], $q['difficulty'], $q['active'] ? 1 : 0, now_utc(), $userId, $qid]);
        }
        write_question_children($qid, $q);
        bump_content_version();
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
    return $code;
}

function set_question_active(string $code, bool $active, int $userId): void
{
    $st = db()->prepare('UPDATE ' . t('questions') . ' SET active = ?, updated_at = ?, updated_by = ? WHERE code = ?');
    $st->execute([$active ? 1 : 0, now_utc(), $userId, $code]);
    if ($st->rowCount() === 0) fail('Domanda non trovata.', 404, 'not_found');
    bump_content_version();
}

function delete_question(string $code): void
{
    $st = db()->prepare('DELETE FROM ' . t('questions') . ' WHERE code = ?');
    $st->execute([$code]);
    if ($st->rowCount() === 0) fail('Domanda non trovata.', 404, 'not_found');
    bump_content_version();
}

// ---------------- Initial import ----------------

function import_seed(array $seed): array
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $insRef = $pdo->prepare('INSERT INTO ' . t('refs') . ' (code, source, title, summary, full_text, created_at, updated_at) VALUES (?,?,?,?,?,?,?)');
        foreach ($seed['refs'] as $r) {
            $insRef->execute([$r['ref-code'], $r['ref-from'], $r['ref-title'] ?? '', $r['ref-text'] ?? '', $r['ref-full-text'] ?? '', now_utc(), now_utc()]);
        }
        $insQ = $pdo->prepare('INSERT INTO ' . t('questions') . ' (code, text, type, difficulty, active, created_at, updated_at) VALUES (?,?,?,?,1,?,?)');
        foreach ($seed['questions'] as $q) {
            $insQ->execute([$q['question-code'], $q['question-text'], $q['question-type'], $q['question-difficulty'] ?: 'm', now_utc(), now_utc()]);
            $qid = (int)$pdo->lastInsertId();
            $answers = [];
            foreach ($q['question-answers'] as $a) $answers[] = ['text' => $a['text'], 'correct' => (bool)$a['correct'], 'value' => $a['value']];
            write_question_children($qid, ['answers' => $answers, 'refs' => array_values(array_unique($q['question-ref']))]);
        }
        bump_content_version();
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
    return ['refs' => count($seed['refs']), 'questions' => count($seed['questions'])];
}

// ---------------- New or changed rules (initial fill) ----------------

/**
 * Initial fill (migration v4), taken from the official documents:
 * - Normative federali 2026/27 "con modifiche evidenziate": the sections highlighted in yellow;
 * - Precisazioni ed istruzioni operative sperimentazioni regolamentari 2026/27 (version 09.10.2026).
 * Only updates references that exist and do not already have a season set.
 * The notes are user-facing text and stay in Italian.
 */
function prefill_ref_changes(): void
{
    $season = '2026/27';
    $rows = [
        ['NF-2.8',   'changed', 'Dopo aver chiesto un Video Check, la squadra non può chiedere un tempo di riposo finché non è completata un’azione di gioco (sperimentazione FIVB).'],
        ['NF-4.2',   'changed', 'La squadra in ricezione può cambiare posizione dal primo movimento del giocatore al servizio (un passo, un movimento preparatorio di braccia o gambe).'],
        ['NF-4.3',   'changed', 'Fino a 8 sostituzioni per set (in deroga alla regola 15.1); restano le limitazioni della regola 15.6.'],
        ['NF-4.4',   'changed', 'A referto fino a 14 atleti (sperimentazione FIVB); con più di 12 iscritti uno deve essere il Libero.'],
        ['NF-4.5',   'changed', 'Dopo il 1° o 2° tocco la palla che colpisce soffitto o strutture può essere rigiocata dalla stessa squadra; se rimbalza nel campo avversario è fallo.'],
        ['NF-4.6',   'changed', 'Stretta applicazione della regola 9.2.2 in attacco: vietati cambi di direzione durante il contatto, attacchi a 2 mani, palle spinte o accompagnate.'],
        ['NF-4.6.1', 'changed', 'Attacco a due mani verso il campo avversario: con traiettoria verso il basso è sempre fallo; verso l’alto è un palleggio, valutato come al solito.'],
        ['NF-4.7',   'changed', 'Niente fischio per i servizi che non superano la rete o finiscono dentro/fuori senza tocchi; resta la segnaletica del 1° arbitro.'],
        ['NF-4.7.1', 'changed', 'Servizio senza tocchi dentro, fuori o in rete: niente fischio; si fischia se la palla è toccata o se un giocatore tenta il recupero.'],
        ['NF-4.8',   'changed', 'Ultimi 3 minuti del riscaldamento ufficiale con servizi alternati: squadra A dal 7° minuto all’8’30”, squadra B dall’8’30” al 10°.'],
        ['NF-PR.1',  'new',     'Il «primo movimento» del giocatore al servizio deve essere legato al servizio: palleggi, rimbalzi o spostamenti non contano.'],
        ['NF-PR.2',  'new',     'La palla può toccare il soffitto più volte, anche sopra il campo avversario; se resta bloccata perde l’azione chi l’ha toccata per ultimo.'],
        ['NF-PR.3',  'new',     'Nel riscaldamento il 1° arbitro fischia inizio, turno di servizio di A, turno di B e fine.'],
        ['NF-PR.4',  'new',     'Nuove indicazioni sull’utilizzo del secondo Libero nella Guida Pratica 2026/27 (pag. 44).'],
    ];
    $st = db()->prepare('UPDATE ' . t('refs') . ' SET change_season = ?, change_type = ?, change_note = ? WHERE code = ? AND change_season IS NULL');
    foreach ($rows as [$code, $type, $note]) $st->execute([$season, $type, $note, $code]);
}
