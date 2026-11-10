<?php
/**
 * Fucina — initial content. The files in lib/seed/ describe the first courses with the helpers below; they are copied
 * into the database once, at installation. From then on the admin portal is where the content lives and changes.
 *
 * Texts are bilingual: L('italiano', 'English').
 */

declare(strict_types=1);

if (!function_exists('L')) {
    function L(string $it, string $en): array
    {
        return ['it' => $it, 'en' => $en];
    }
}

/** A paragraph. `Backticks` become inline code, **asterisks** bold. */
function P(string $it, string $en): array
{
    return ['t' => 'p', 'text' => L($it, $en)];
}

/** A code sample. */
function C(string $code, string $lang = 'html'): array
{
    return ['t' => 'code', 'code' => trim($code, "\n"), 'lang' => $lang];
}

/** A highlighted tip. */
function Tip(string $it, string $en): array
{
    return ['t' => 'tip', 'text' => L($it, $en)];
}

/** A multiple choice question. An option is a string (same in both languages) or L(). */
function Q(string $id, array $q, array $options, int $answer, array $why): array
{
    $options = array_map(fn($o) => is_array($o) ? $o : L($o, $o), $options);
    return ['id' => $id, 'q' => $q, 'options' => $options, 'a' => $answer, 'why' => $why];
}

/** A lesson: theory blocks, questions and an optional hands-on exercise. */
function lesson(string $id, array $title, array $body, array $quiz, ?array $task = null, array $drills = []): array
{
    return ['id' => $id, 'title' => $title, 'body' => $body, 'quiz' => $quiz, 'task' => $task, 'drills' => $drills];
}

// Drills written next to their lesson: see lib/drills.php
function dBlanks(array $prompt, string $code, array $gaps, array $extra, array $why): array
{
    return ['kind' => 'blanks', 'prompt' => $prompt, 'code' => $code, 'gaps' => $gaps, 'extra' => $extra, 'why' => $why];
}

function dOrder(array $prompt, array $lines, array $why): array
{
    return ['kind' => 'order', 'prompt' => $prompt, 'lines' => $lines, 'why' => $why];
}

function dBug(array $prompt, array $lines, int $bug, string $fix, array $why): array
{
    return ['kind' => 'bug', 'prompt' => $prompt, 'lines' => $lines, 'bug' => $bug, 'fix' => $fix, 'why' => $why];
}

function dMatch(array $prompt, array $pairs, array $why): array
{
    return ['kind' => 'match', 'prompt' => $prompt, 'pairs' => $pairs, 'why' => $why];
}

function dFlash(array $prompt, array $cards, array $why): array
{
    return ['kind' => 'flash', 'prompt' => $prompt, 'cards' => $cards, 'why' => $why];
}

/** The same instructions for every drill of a kind. */
function pFill(): array { return L('Completa il codice con le tessere giuste.', 'Complete the code with the right tiles.'); }
function pSort(): array { return L('Rimetti le righe nell’ordine giusto.', 'Put the lines back in the right order.'); }
function pBug(): array { return L('Tocca la riga con l’errore.', 'Tap the line with the mistake.'); }
function pMatch(): array { return L('Abbina ogni elemento al suo significato.', 'Match each item with its meaning.'); }
function pFlash(): array { return L('Vero o falso? Rispondi prima che scada il tempo!', 'True or false? Answer before time runs out!'); }

/**
 * Hands-on exercise, checked in the browser against the page the learner has written.
 * lang "html": $start is the page. lang "css": $start is the stylesheet, $html the fixed page it applies to.
 * lang "scss"/"sass": no live preview (the browser cannot compile them), only pattern checks.
 */
function task(string $lang, array $prompt, string $start, array $checks, string $html = '', string $hint = ''): array
{
    return ['lang' => $lang, 'prompt' => $prompt, 'start' => trim($start, "\n"), 'html' => trim($html, "\n"), 'checks' => $checks, 'hint' => $hint];
}

/** The element matching $selector exists (and contains $text, when given). */
function has(string $selector, array $label, string $text = ''): array
{
    return ['sel' => $selector, 'text' => $text, 'label' => $label];
}

/** The element matching $selector has the computed CSS $prop equal to $value. */
function css(string $selector, string $prop, string $value, array $label): array
{
    return ['sel' => $selector, 'prop' => $prop, 'value' => $value, 'label' => $label];
}

/** The source code matches the regular expression (case insensitive, multiline). */
function re(string $pattern, array $label): array
{
    return ['re' => $pattern, 'label' => $label];
}

/** Short solution shown by a paid hint, per lesson. It is code, so it needs no translation. */
const SEED_TASK_HINTS = [
    'html-base/structure' => '<h1>Ciao mondo</h1>',
    'html-base/text' => "<h2>Le novità</h2>\n<p>Un testo <strong>importante</strong>.</p>",
    'html-base/links' => "<a href=\"https://example.com\">Scopri</a>\n<img src=\"logo.png\" alt=\"Logo\">",
    'html-base/lists' => "<ol>\n  <li>Uno</li>\n  <li>Due</li>\n  <li>Tre</li>\n</ol>",
    'html-base/tables' => "<tr><th>Nome</th></tr>\n<tr><td>Sav</td></tr>",
    'html-base/forms' => "<label for=\"m\">Email</label>\n<input id=\"m\" type=\"email\" required>\n<button>Invia</button>",
    'html-advanced/semantic' => "<header>…</header>\n<main><article>…</article></main>\n<footer>…</footer>",
    'html-advanced/rich-forms' => "<fieldset>\n  <legend>Piano</legend>\n  <label><input type=\"radio\" name=\"piano\"> A</label>\n  <label><input type=\"radio\" name=\"piano\"> B</label>\n</fieldset>",
    'html-advanced/media' => "<video src=\"v.mp4\" controls></video>\n<img src=\"a.jpg\" alt=\"Foto\" loading=\"lazy\">",
    'html-advanced/a11y' => '<button aria-label="Chiudi">✕</button>',
    'html-advanced/meta' => "<details>\n  <summary>Titolo</summary>\n  <p>Testo</p>\n</details>",
    'css-base/selectors' => ".nota { color: red; }\n#box { width: 200px; }",
    'css-base/colors' => ".titolo {\n  text-align: center;\n  color: #E8590C;\n}",
    'css-base/box' => "padding: 20px;\nborder-radius: 12px;",
    'css-base/display' => "a { display: inline-block; padding: 10px; }\n.nota { display: none; }",
    'css-base/position' => "#box { position: relative; }\n.nota { position: absolute; top: 0; }",
    'css-base/cascade' => 'p.nota { color: blue; }',
    'css-advanced/flex' => "display: flex;\ngap: 16px;\njustify-content: space-between;",
    'css-advanced/grid' => ".lista {\n  display: grid;\n  grid-template-columns: repeat(3, 1fr);\n  gap: 12px;\n}",
    'css-advanced/responsive' => "@media (min-width: 700px) {\n  .lista { display: flex; }\n}",
    'css-advanced/variables' => ":root { --brand: #E8590C; }\n.card { color: var(--brand); }",
    'css-advanced/motion' => ".card { transition: transform .2s; }\n.card:hover { transform: scale(1.05); }",
    'scss/intro' => "\$brand: #E8590C;\n.titolo { color: \$brand; }",
    'scss/nesting' => "a {\n  &:hover { color: red; }\n}",
    'scss/mixins' => "@mixin ombra(\$colore) {\n  box-shadow: 0 2px 8px \$colore;\n}\n.card { @include ombra(gray); }",
    'scss/modules' => "@use \"colori\";\n@each \$nome, \$tinta in \$temi {\n  .avviso-#{\$nome} { color: \$tinta; }\n}",
    'sass/indented' => ".titolo\n  color: red",
    'sass/mixins' => "=tondo\n  border-radius: 50%\n\n.avatar\n  +tondo",
];

/** Courses of the first installation, in display order. */
const SEED_COURSES = ['html-base', 'html-intermediate', 'html-advanced', 'css-base', 'css-intermediate', 'css-advanced', 'css-animations', 'scss', 'sass', 'tailwind',
    'js-base', 'js-intermediate', 'js-advanced', 'ts', 'angular-base', 'angular-intermediate', 'vue-base', 'vue-intermediate',
    'php-base', 'php-intermediate', 'php-advanced', 'python-base', 'python-intermediate', 'python-advanced', 'java-base', 'java-intermediate', 'java-advanced',
    'sql-base', 'sql-intermediate', 'sql-advanced', 'mysql', 'mariadb', 'postgresql', 'sqlite', 'mssql', 'mongodb', 'r-base', 'r-intermediate',
    'flutter-base', 'flutter-intermediate', 'swift-base', 'swift-intermediate', 'kotlin-base', 'kotlin-intermediate',
    'rust-base', 'rust-intermediate', 'rust-advanced', 'vala-base', 'vala-intermediate'];

/**
 * What each course is for, to filter the catalogue. A course file may set its own 'categories'; otherwise its family decides.
 */
const FAMILY_CATEGORIES = [
    'html' => ['frontend'], 'css' => ['frontend'], 'scss' => ['frontend'], 'sass' => ['frontend'], 'tailwind' => ['frontend'],
    'js' => ['frontend', 'backend'], 'ts' => ['frontend', 'backend'], 'angular' => ['frontend'], 'vue' => ['frontend'],
    'php' => ['backend'], 'python' => ['backend', 'data'], 'java' => ['backend', 'mobile', 'desktop'], 'sql' => ['data', 'backend'], 'r' => ['data'],
    'mysql' => ['data', 'backend'], 'mariadb' => ['data', 'backend'], 'postgresql' => ['data', 'backend'], 'sqlite' => ['data', 'mobile', 'desktop'], 'mssql' => ['data', 'backend'], 'mongodb' => ['data', 'backend'],
    'ai' => ['ai'], 'ml' => ['ai', 'data'], 'dl' => ['ai', 'data'], 'prompt' => ['ai'], 'llm' => ['ai', 'backend'], 'assistants' => ['ai'],
    'flutter' => ['mobile', 'desktop'], 'swift' => ['mobile', 'desktop'], 'kotlin' => ['mobile', 'backend'],
    'rust' => ['systems', 'backend'], 'vala' => ['desktop', 'systems'],
];

function seed_categories(array $c): array
{
    return $c['categories'] ?? FAMILY_CATEGORIES[$c['family']] ?? [];
}

/** The courses whose file is on the server: a partial upload must not stop the update of the others. */
function seed_course_ids(): array
{
    return array_values(array_filter(SEED_COURSES, fn($id) => is_file(__DIR__ . '/seed/' . $id . '.php')));
}

/** A course of the catalogue, with the levels added to it after its first version. */
function seed_course(string $id): array
{
    static $extra = null;
    $extra = $extra ?? require __DIR__ . '/seed/extra-levels.php';
    $c = require __DIR__ . '/seed/' . $id . '.php';
    $c['lessons'] = array_merge($c['lessons'], $extra[$id] ?? []);
    return $c;
}

/** Base difficulty of a course (1 easy, 2 medium, 3 hard): the second half of a course is one step harder. */
const SEED_TIERS = ['html-base' => 1, 'html-intermediate' => 1, 'html-advanced' => 2, 'css-base' => 1, 'css-intermediate' => 1, 'css-advanced' => 2, 'css-animations' => 2, 'scss' => 2, 'sass' => 2,
    'js-base' => 1, 'js-intermediate' => 2, 'js-advanced' => 2, 'ts' => 2, 'php-base' => 1, 'php-intermediate' => 2, 'php-advanced' => 2, 'tailwind' => 1,
    'angular-base' => 2, 'angular-intermediate' => 2, 'vue-base' => 1, 'vue-intermediate' => 2, 'python-base' => 1, 'python-intermediate' => 2, 'python-advanced' => 2,
    'java-base' => 1, 'java-intermediate' => 2, 'java-advanced' => 2, 'sql-base' => 1, 'sql-intermediate' => 2, 'sql-advanced' => 2, 'mysql' => 2, 'mariadb' => 2, 'postgresql' => 2, 'sqlite' => 2, 'mssql' => 2, 'mongodb' => 1, 'r-base' => 1, 'r-intermediate' => 2,
    'flutter-base' => 1, 'flutter-intermediate' => 2, 'swift-base' => 1, 'swift-intermediate' => 2, 'kotlin-base' => 1, 'kotlin-intermediate' => 2,
    'rust-base' => 1, 'rust-intermediate' => 2, 'rust-advanced' => 3, 'vala-base' => 1, 'vala-intermediate' => 2];

/**
 * Version 12: what was "PHP advanced" becomes "PHP intermediate" (same lessons, same progress and certificates),
 * so that a new, really advanced PHP course can follow it.
 */
function install_seed_php_v12(PDO $pdo, string $p): void
{
    $has = $pdo->prepare("SELECT COUNT(*) FROM `{$p}courses` WHERE id = ?");
    $has->execute(['php-advanced']);
    $old = (int)$has->fetchColumn();
    $has->execute(['php-intermediate']);
    if (!$old || (int)$has->fetchColumn()) return;
    $c = seed_course('php-intermediate');
    $data = json_encode(['title' => $c['title'], 'short' => $c['short'], 'desc' => $c['desc'], 'level' => $c['level'], 'categories' => seed_categories($c)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // The lessons follow the course by themselves (ON UPDATE CASCADE); the other tables keep the id as plain text
    $pdo->prepare("UPDATE `{$p}courses` SET id = 'php-intermediate', data = ? WHERE id = 'php-advanced'")->execute([$data]);
    $pdo->exec("UPDATE `{$p}courses` SET after_id = 'php-intermediate' WHERE after_id = 'php-advanced'");
    foreach (['lesson_progress', 'exams', 'certificates'] as $table) $pdo->exec("UPDATE `{$p}{$table}` SET course = 'php-intermediate' WHERE course = 'php-advanced'");
    $pdo->exec("UPDATE `{$p}badges` SET badge = 'course_php-intermediate' WHERE badge = 'course_php-advanced'");
}

function seed_games(): array
{
    return [
        ['id' => 'whotag', 'levels' => 6, 'icon' => 'search', 'title' => L('Chi è il tag?', 'Who’s the tag?'), 'desc' => L('Fai domande, elimina le carte e scopri il tag nascosto.', 'Ask questions, knock down cards and find the hidden tag.')],
        ['id' => 'tagdoku', 'levels' => 6, 'icon' => 'grid', 'title' => L('Tagdoku', 'Tagdoku'), 'desc' => L('Riempi la griglia: ogni tag una sola volta per riga, colonna e riquadro.', 'Fill the grid: each tag once per row, column and box.')],
        ['id' => 'fourline', 'levels' => 6, 'icon' => 'columns', 'title' => L('Quattro di fila', 'Four in a line'), 'desc' => L('Rispondi bene per giocare la tua mossa e allinea quattro gettoni.', 'Answer correctly to play your move and line up four discs.')],
        ['id' => 'selectors', 'levels' => 6, 'icon' => 'board', 'title' => L('Scacchiera CSS', 'CSS board'), 'desc' => L('Tocca tutte le caselle scelte dal selettore.', 'Tap every square picked by the selector.')],
        ['id' => 'linehop', 'levels' => 6, 'icon' => 'sort', 'title' => L('Salti di riga', 'Line hop'), 'desc' => L('Rimetti in ordine le righe di codice con meno mosse possibile.', 'Put the lines of code back in order in as few moves as possible.')],
        ['id' => 'shade', 'levels' => 6, 'icon' => 'theme', 'title' => L('Tinta giusta', 'Right shade'), 'desc' => L('Dal codice al colore e ritorno: nomi, esadecimali, rgb() e hsl().', 'From code to colour and back: names, hex, rgb() and hsl().')],
        ['id' => 'duel', 'levels' => 6, 'icon' => 'swap', 'title' => L('Duello di selettori', 'Selector duel'), 'desc' => L('Due selettori si sfidano: scegli quello più specifico.', 'Two selectors face off: pick the more specific one.')],
        ['id' => 'flexlab', 'levels' => 6, 'icon' => 'spaces', 'title' => L('Laboratorio flex', 'Flex lab'), 'desc' => L('Regola le proprietà flex finché le scatole non combaciano.', 'Tune the flex properties until the boxes line up.')],
        ['id' => 'output', 'levels' => 6, 'icon' => 'print', 'title' => L('Che cosa stampa?', 'What does it print?'), 'desc' => L('Leggi il codice JavaScript e indovina cosa scrive in console.', 'Read the JavaScript and guess what it logs to the console.')],
    ];
}

/** Cards of "Who's the tag?": each tag with the traits the questions ask about. */
function seed_whotag_items(): array
{
    $t = ['h1' => 'block text', 'p' => 'block text', 'span' => 'inline text', 'strong' => 'inline text semantic', 'em' => 'inline text semantic', 'a' => 'inline attr interactive', 'img' => 'inline void attr media', 'video' => 'inline attr media interactive', 'br' => 'inline void', 'hr' => 'block void', 'input' => 'inline void attr interactive form', 'button' => 'inline interactive form', 'label' => 'inline form', 'form' => 'block attr form', 'ul' => 'block list', 'ol' => 'block list', 'li' => 'block list', 'table' => 'block table', 'td' => 'table', 'header' => 'block semantic', 'nav' => 'block semantic', 'main' => 'block semantic', 'article' => 'block semantic', 'footer' => 'block semantic'];
    $out = [];
    foreach ($t as $name => $traits) $out[] = ['level' => 1, 'data' => ['name' => $name, 'traits' => explode(' ', $traits)]];
    return $out;
}

/** Selectors of the CSS board, from a single class to :not() and structural pseudo-classes. Squares have .white/.black, .rN, .cN. */
function seed_selector_items(): array
{
    // The board grows with the level (3×3, 5×5, 7×7, 9×9, 10×10, 10×10): each selector is written for its size
    $by = [
        1 => ['.r1', '.c3', '.r3', '.c1', '.r2.c2'],
        2 => ['.r2.c4', '.r5.white', '.c1.black', '.r1, .r5', '.c3.white'],
        3 => ['.r1, .r7', '.c1, .c7', '.r2.white, .r3.black', '.r4, .c4', '.r7.black'],
        4 => [':nth-child(9n)', ':nth-child(9n+1)', ':nth-child(-n+9)', ':nth-child(n+73)', '.r5:nth-child(odd)'],
        5 => ['.black:not(.r1):not(.r2)', '.r3:not(.c1):not(.c10)', '.white:nth-child(-n+20)', ':not(.white):nth-child(n+81)', ':nth-child(10n+5)'],
        6 => [':nth-child(4n).black', ':nth-child(3n+1):not(.r1):not(.r10)', '.r5 ~ .r6.white', '.c4 + .c5.black', '.r9 ~ :nth-child(odd):not(.c1)'],
    ];
    $out = [];
    foreach ($by as $level => $list) foreach ($list as $sel) $out[] = ['level' => $level, 'data' => ['selector' => $sel]];
    return $out;
}

/** The selectors of the 8×8 board used before version 9: the update swaps them for the ones above. */
const SEED_SELECTORS_V8 = ['.r1', '.c3', '.r8', '.c1', '.r2.c2', '.r4.c5', '.r1.white', '.c8.black', '.r1, .r8', '.c1, .c8', '.r2.white, .r3.black', '.r4, .c4',
    ':nth-child(8n)', ':nth-child(8n+1)', ':nth-child(-n+8)', ':nth-child(n+57)', '.black:not(.r1):not(.r2)', '.r3:not(.c1):not(.c8)', '.white:nth-child(-n+16)',
    ':not(.white):nth-child(n+49)', ':nth-child(4n).black', ':nth-child(3n+1):not(.r1):not(.r8)', '.r5 ~ .r6.white', '.c4 + .c5.black'];

/** Version 9: the CSS board grows with the level, so the selectors of the old 8×8 board make way for new ones. Edited ones stay. */
function install_seed_selectors_v9(PDO $pdo, string $p): void
{
    $st = $pdo->prepare("SELECT id, data FROM `{$p}game_items` WHERE game = 'selectors'");
    $st->execute();
    $del = $pdo->prepare("DELETE FROM `{$p}game_items` WHERE id = ?");
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $sel = json_decode($r['data'], true)['selector'] ?? null;
        if (in_array($sel, SEED_SELECTORS_V8, true)) $del->execute([$r['id']]);
    }
    // Only what is not there yet, so running it twice adds nothing
    $have = [];
    $st->execute();
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) $have[] = json_decode($r['data'], true)['selector'] ?? '';
    $ins = $pdo->prepare("INSERT INTO `{$p}game_items` (game, level, status, data) VALUES ('selectors', ?, 'published', ?)");
    foreach (seed_selector_items() as $it) {
        if (in_array($it['data']['selector'], $have, true)) continue;
        $ins->execute([$it['level'], json_encode($it['data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
    }
}

/** Snippets of Line hop, longer and more nested at every level. The lines are stored in the right order. */
function seed_linehop_items(): array
{
    $by = [
        1 => [[L('Un titolo in una pagina', 'A heading in a page'), "<body>\n  <h1>Ciao</h1>\n</body>"], [L('Una regola CSS', 'A CSS rule'), "p {\n  color: red;\n}"]],
        2 => [[L('Una lista', 'A list'), "<ul>\n  <li>Pane</li>\n  <li>Latte</li>\n</ul>"], [L('Una card', 'A card'), ".card {\n  padding: 16px;\n  margin: 0 auto;\n}"]],
        3 => [[L('Lo scheletro della pagina', 'The page skeleton'), "<!doctype html>\n<html>\n  <head>\n    <title>Ciao</title>\n  </head>\n</html>"], [L('Una tabella', 'A table'), "<table>\n  <tr>\n    <td>Udine</td>\n  </tr>\n</table>"]],
        4 => [[L('Un modulo', 'A form'), "<form>\n  <label for=\"m\">Email</label>\n  <input id=\"m\" type=\"email\">\n  <button>Invia</button>\n</form>"], [L('Una media query', 'A media query'), "@media (min-width: 700px) {\n  .lista {\n    display: grid;\n    gap: 12px;\n  }\n}"]],
        5 => [[L('Una pagina semantica', 'A semantic page'), "<body>\n  <header>Logo</header>\n  <main>\n    <article>Testo</article>\n  </main>\n  <footer>Fine</footer>\n</body>"], [L('Un’animazione', 'An animation'), "@keyframes pulsa {\n  from {\n    opacity: 1;\n  }\n  to {\n    opacity: .4;\n  }\n}"]],
        6 => [[L('Un mixin SCSS', 'An SCSS mixin'), "@mixin centrato(\$d) {\n  display: flex;\n  flex-direction: \$d;\n}\n.hero {\n  @include centrato(column);\n}"], [L('Un ciclo SCSS', 'An SCSS loop'), "\$temi: (ok: green, errore: red);\n@each \$nome, \$tinta in \$temi {\n  .avviso-#{\$nome} {\n    color: \$tinta;\n  }\n}"]],
    ];
    $out = [];
    foreach ($by as $level => $list) foreach ($list as [$title, $code]) $out[] = ['level' => $level, 'data' => ['title' => $title, 'lines' => explode("\n", $code)]];
    return $out;
}

/**
 * Profile pictures and their starting price in Sparks (the admin can change prices and take items off sale).
 * The drawings are in assets/avatars.js under the same ids. The initial of the username is always free.
 */
const AVATAR_ITEMS = [
    // Characters
    'bit' => ['character', 0], 'pixel' => ['character', 0], 'duck' => ['character', 40], 'hamster' => ['character', 50],
    'frog' => ['character', 60], 'owl' => ['character', 60], 'hedgehog' => ['character', 70], 'turtle' => ['character', 70],
    'fox' => ['character', 80], 'crab' => ['character', 80], 'bee' => ['character', 80], 'axolotl' => ['character', 90],
    'octopus' => ['character', 100], 'ghost' => ['character', 100], 'snake' => ['character', 100], 'racoon' => ['character', 110],
    'sloth' => ['character', 110], 'snail' => ['character', 120], 'penguin' => ['character', 120], 'gopher' => ['character', 120],
    'corgi' => ['character', 130], 'panda' => ['character', 140], 'chameleon' => ['character', 140], 'koala' => ['character', 150],
    'alien' => ['character', 150], 'redpanda' => ['character', 160], 'narwhal' => ['character', 170], 'shark' => ['character', 180],
    'dragon' => ['character', 200], 'viking' => ['character', 210], 'pirate' => ['character', 210], 'ninja' => ['character', 220],
    'anvil' => ['character', 250], 'wizard' => ['character', 260], 'cyborg' => ['character', 280], 'favilla' => ['character', 300],
    'astronaut' => ['character', 300], 'griffin' => ['character', 320], 'phoenix' => ['character', 350], 'unicorn' => ['character', 400],
    // Accessories
    'glasses' => ['accessory', 0], 'bandage' => ['accessory', 30], 'bowtie' => ['accessory', 40], 'flower' => ['accessory', 40],
    'sprout' => ['accessory', 50], 'mustache' => ['accessory', 50], 'bandana' => ['accessory', 60], 'beanie' => ['accessory', 70],
    'cap' => ['accessory', 80], 'beret' => ['accessory', 80], 'eyepatch' => ['accessory', 80], 'scarf' => ['accessory', 90],
    'heartglasses' => ['accessory', 90], 'shades' => ['accessory', 100], 'antlers' => ['accessory', 100], 'monocle' => ['accessory', 110],
    'chef' => ['accessory', 110], 'coffee' => ['accessory', 120], 'gradcap' => ['accessory', 130], 'headphones' => ['accessory', 140],
    'tophat' => ['accessory', 150], 'halo' => ['accessory', 160], 'vikinghelm' => ['accessory', 170], 'wizardhat' => ['accessory', 180],
    'vr' => ['accessory', 200], 'tiara' => ['accessory', 220], 'crown' => ['accessory', 250],
    // Frames
    'ring' => ['frame', 0], 'dashed' => ['frame', 40], 'wood' => ['frame', 50], 'copper_ring' => ['frame', 60],
    'pixel_frame' => ['frame', 70], 'neon' => ['frame', 90], 'ice' => ['frame', 100], 'emerald' => ['frame', 110],
    'gear' => ['frame', 120], 'sakura' => ['frame', 130], 'gold_ring' => ['frame', 140], 'circuit' => ['frame', 150],
    'lightning_ring' => ['frame', 160], 'laurel' => ['frame', 180], 'rainbow_ring' => ['frame', 200], 'fire_ring' => ['frame', 220],
    'galaxy_ring' => ['frame', 260], 'royal' => ['frame', 300],
    // Backgrounds
    'violet' => ['background', 0], 'night' => ['background', 0], 'ember' => ['background', 40], 'mint' => ['background', 40],
    'sky' => ['background', 40], 'ocean' => ['background', 40], 'forest' => ['background', 40], 'lavender' => ['background', 40],
    'coral' => ['background', 40], 'teal' => ['background', 40], 'crimson' => ['background', 50], 'indigo' => ['background', 50],
    'amber' => ['background', 50], 'slate' => ['background', 50], 'gold' => ['background', 60], 'pink' => ['background', 60],
    'coal' => ['background', 60], 'cherry' => ['background', 70], 'copper' => ['background', 90], 'arctic' => ['background', 100],
    'jungle' => ['background', 100], 'aurora' => ['background', 120], 'terminal' => ['background', 120], 'cyber' => ['background', 120],
    'magma' => ['background', 130], 'synthwave' => ['background', 140], 'sunset' => ['background', 150], 'nebula' => ['background', 150],
    'forge' => ['background', 160], 'matrix' => ['background', 170], 'blueprint' => ['background', 180], 'prism' => ['background', 200],
];

/** Every row of the initial content, table by table. Questions refer to their lesson through "_lesson". */
function seed_rows(): array
{
    $j = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $rows = ['courses' => [], 'lessons' => [], 'questions' => [], 'games' => [], 'game_items' => []];
    foreach (seed_course_ids() as $sort => $id) {
        $c = seed_course($id);
        $rows['courses'][] = ['id' => $id, 'family' => $c['family'], 'sort' => $sort + 1, 'after_id' => $c['after'] ?? null, 'status' => 'published',
            'data' => $j(['title' => $c['title'], 'short' => $c['short'], 'desc' => $c['desc'], 'level' => $c['level'], 'categories' => seed_categories($c)])];
        $n = count($c['lessons']);
        foreach ($c['lessons'] as $i => $l) {
            $difficulty = min(3, SEED_TIERS[$id] + ($i >= $n / 2 ? 1 : 0));
            $task = $l['task'];
            if ($task && $task['hint'] === '') $task['hint'] = SEED_TASK_HINTS[$id . '/' . $l['id']] ?? '';
            $rows['lessons'][] = ['course' => $id, 'slug' => $l['id'], 'sort' => $i + 1, 'difficulty' => $difficulty, 'status' => 'published', 'title' => $j($l['title']), 'body' => $j($l['body']), 'task' => $task ? $j($task) : null];
            foreach ($l['quiz'] as $k => $q) {
                $rows['questions'][] = ['_lesson' => $id . '/' . $l['id'], 'sort' => $k + 1, 'difficulty' => $difficulty, 'q' => $j($q['q']), 'options' => $j($q['options']), 'answer' => $q['a'], 'why' => $j($q['why']), 'hint' => null];
            }
        }
    }
    $items = ['whotag' => seed_whotag_items(), 'selectors' => seed_selector_items(), 'linehop' => seed_linehop_items()];
    foreach (seed_games() as $sort => $g) {
        $rows['games'][] = ['id' => $g['id'], 'enabled' => 1, 'sort' => $sort + 1, 'levels' => $g['levels'], 'data' => $j(['title' => $g['title'], 'desc' => $g['desc'], 'icon' => $g['icon']])];
        foreach ($items[$g['id']] ?? [] as $it) $rows['game_items'][] = ['game' => $g['id'], 'level' => $it['level'], 'status' => 'published', 'data' => $j($it['data'])];
    }
    return $rows;
}

/**
 * Loads what the database does not have yet: it runs at installation and at every new version of the schema.
 * Existing lessons, questions and games are never touched, so what was edited in the portal stays.
 * Only the order of the courses and what opens them follow the catalogue, because new courses change the chain.
 */
function install_seed(PDO $pdo, string $p): void
{
    $rows = seed_rows();
    $insert = function (string $table, array $row) use ($pdo, $p): int {
        $cols = array_keys($row);
        $pdo->prepare("INSERT INTO `{$p}{$table}` (`" . implode('`,`', $cols) . '`) VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')')->execute(array_values($row));
        return (int)$pdo->lastInsertId();
    };
    $courses = $pdo->query("SELECT id, data FROM `{$p}courses`")->fetchAll(PDO::FETCH_KEY_PAIR);
    $move = $pdo->prepare("UPDATE `{$p}courses` SET sort = ?, after_id = ? WHERE id = ?");
    $tag = $pdo->prepare("UPDATE `{$p}courses` SET data = ? WHERE id = ?");
    foreach ($rows['courses'] as $r) {
        if (!isset($courses[$r['id']])) {
            $insert('courses', $r);
            continue;
        }
        $move->execute([$r['sort'], $r['after_id'], $r['id']]);
        // Courses made before the categories existed get them; categories changed in the portal stay
        $data = json_decode((string)$courses[$r['id']], true) ?: [];
        if (!array_key_exists('categories', $data)) {
            $data['categories'] = json_decode($r['data'], true)['categories'] ?? [];
            $tag->execute([json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $r['id']]);
        }
    }
    $lessonIds = [];
    foreach ($pdo->query("SELECT course, slug FROM `{$p}lessons`")->fetchAll(PDO::FETCH_ASSOC) as $l) $lessonIds[$l['course'] . '/' . $l['slug']] = 0;
    $added = [];
    foreach ($rows['lessons'] as $r) {
        $key = $r['course'] . '/' . $r['slug'];
        if (!isset($lessonIds[$key])) $added[$key] = $insert('lessons', $r);
    }
    foreach ($rows['questions'] as $r) {
        $key = $r['_lesson'];
        unset($r['_lesson']);
        if (isset($added[$key])) $insert('questions', ['lesson_id' => $added[$key]] + $r);
    }
    // Games: the new ones of the catalogue are added after the existing ones, with their items; existing games are never touched
    $games = $pdo->query("SELECT id FROM `{$p}games`")->fetchAll(PDO::FETCH_COLUMN);
    $sort = (int)$pdo->query("SELECT COALESCE(MAX(sort), 0) FROM `{$p}games`")->fetchColumn();
    $new = [];
    foreach ($rows['games'] as $r) {
        if (in_array($r['id'], $games, true)) continue;
        $new[] = $r['id'];
        $insert('games', ['sort' => ++$sort] + $r);
    }
    foreach ($rows['game_items'] as $r) if (in_array($r['game'], $new, true)) $insert('game_items', $r);
}

/** Drills of the lessons that have none, so they reach the installations made before they existed. */
function install_seed_drills(PDO $pdo, string $p): void
{
    $ids = [];
    foreach ($pdo->query("SELECT id, course, slug FROM `{$p}lessons`")->fetchAll(PDO::FETCH_ASSOC) as $l) $ids[$l['course'] . '/' . $l['slug']] = (int)$l['id'];
    $full = array_map('intval', $pdo->query("SELECT DISTINCT lesson_id FROM `{$p}drills`")->fetchAll(PDO::FETCH_COLUMN));
    $st = $pdo->prepare("INSERT INTO `{$p}drills` (lesson_id, sort, kind, data) VALUES (?,?,?,?)");
    foreach (seed_drills() as $lesson => $drills) {
        if (!isset($ids[$lesson]) || in_array($ids[$lesson], $full, true)) continue;
        foreach ($drills as $i => $d) {
            $kind = $d['kind'];
            unset($d['kind']);
            $st->execute([$ids[$lesson], $i + 1, $kind, json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        }
    }
}

/** Flash drills saved with three cards: when the saved cards are still the first ones of the catalogue, the catalogue's drill replaces them. */
function install_seed_flash_v17(PDO $pdo, string $p): void
{
    $seed = seed_drills();
    $rows = $pdo->query("SELECT d.id, d.data, l.course, l.slug FROM `{$p}drills` d JOIN `{$p}lessons` l ON l.id = d.lesson_id WHERE d.kind = 'flash'")->fetchAll(PDO::FETCH_ASSOC);
    $upd = $pdo->prepare("UPDATE `{$p}drills` SET data = ? WHERE id = ?");
    foreach ($rows as $row) {
        $data = json_decode((string)$row['data'], true);
        if (!is_array($data) || count($data['cards'] ?? []) >= 4) continue;
        foreach ($seed[$row['course'] . '/' . $row['slug']] ?? [] as $d) {
            if ($d['kind'] !== 'flash' || count($d['cards']) < 4 || array_slice($d['cards'], 0, count($data['cards'])) != $data['cards']) continue;
            unset($d['kind']);
            $upd->execute([json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $row['id']]);
            break;
        }
    }
}

/** The drills of the catalogue, 'course/lesson' => list: from lib/seed/drills.php and from the lessons that carry their own. */
function seed_drills(): array
{
    $all = require __DIR__ . '/seed/drills.php';
    foreach (seed_course_ids() as $id) {
        $c = seed_course($id);
        foreach ($c['lessons'] as $l) if (!empty($l['drills'])) $all[$id . '/' . $l['id']] = $l['drills'];
    }
    return $all;
}
