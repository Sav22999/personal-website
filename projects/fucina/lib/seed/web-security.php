<?php
/** Web security with OWASP: injection, XSS, authentication and sessions, access control, CSRF and security headers, SSRF and
 *  dependencies, the OWASP Top 10 2025, modern authentication (NIST, passkeys), cryptography and secrets, CORS and clickjacking. */

/** A flow drawing: boxes, numbered arrows and the list of numbered steps underneath. Each box is [x, y, w, title, subtitle, colour key]
 *  (height 64), each arrow [svg path, step number, x and y of its number, bad: true when it is the attack]. */
if (!function_exists('fig_ws_flow')) {
    function fig_ws_flow(array $boxes, array $arrows, array $steps, string $extra = ''): string
    {
        $pal = ['me' => ['#DCEBFF', '#2F8CF0'], 'bad' => ['#FFE3EE', '#F03E82'], 'site' => ['#D5F6EA', '#0B7F5C'], 'in' => ['#ECE6FF', '#7048E8'], 'grey' => ['#F1F0F8', '#8A87B0']];
        $out = '';
        foreach ($boxes as [$x, $y, $w, $title, $sub, $k]) {
            [$f, $s] = $pal[$k]; $cx = $x + $w / 2;
            $out .= "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"64\" rx=\"12\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"/><text x=\"$cx\" y=\"" . ($y + 28) . "\" text-anchor=\"middle\" font-weight=\"700\">$title</text><text x=\"$cx\" y=\"" . ($y + 48) . "\" text-anchor=\"middle\" font-size=\"13\">$sub</text>";
        }
        foreach ($arrows as [$d, $n, $nx, $ny, $bad]) {
            $c = $bad ? '#F03E82' : '#1E1C52'; $m = $bad ? 'p' : 'a';
            $out .= "<path d=\"$d\" fill=\"none\" stroke=\"$c\" stroke-width=\"2\"" . ($bad ? ' stroke-dasharray="7 4"' : '') . " marker-end=\"url(#$m)\"/>"
                  . "<circle cx=\"$nx\" cy=\"$ny\" r=\"11\" fill=\"" . ($bad ? '#F03E82' : '#1E1C52') . "\"/><text x=\"$nx\" y=\"" . ($ny + 5) . "\" text-anchor=\"middle\" font-size=\"13\" font-weight=\"700\" fill=\"#FFFFFF\">$n</text>";
        }
        $top = 0; foreach ($boxes as $b) $top = max($top, $b[1] + 64);
        $y = $top + 34;
        foreach ($steps as $i => [$txt, $bad]) {
            $out .= "<circle cx=\"30\" cy=\"" . ($y - 5) . "\" r=\"10\" fill=\"" . ($bad ? '#F03E82' : '#1E1C52') . "\"/><text x=\"30\" y=\"$y\" text-anchor=\"middle\" font-size=\"12\" font-weight=\"700\" fill=\"#FFFFFF\">" . ($i + 1) . "</text><text x=\"50\" y=\"$y\" font-size=\"14\">$txt</text>";
            $y += 26;
        }
        $h = $y - 6;
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 $h" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker>
<marker id="p" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#F03E82"/></marker></defs>
{$extra}{$out}
</svg>
SVG;
    }
}

/** SQL injection: the same input concatenated into the query becomes code, passed as a parameter it stays data. */
if (!function_exists('fig_ws_sqli')) {
    function fig_ws_sqli(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 340" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<text x="16" y="38" font-weight="700">email</text>
<rect x="70" y="16" width="200" height="34" rx="8" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/>
<text x="82" y="38" font-family="ui-monospace, Menlo, monospace" font-size="14">x' OR '1'='1</text>
<rect x="8" y="66" width="624" height="124" rx="12" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="7 4"/>
<text x="24" y="92" font-weight="700" fill="#B0205A">{$t[0]}</text>
<text x="24" y="128" font-family="ui-monospace, Menlo, monospace" font-size="14">SELECT * FROM utenti WHERE email = 'x' <tspan font-weight="700" fill="#B0205A">OR '1'='1'</tspan></text>
<path d="M352,136 V146 H446 V136" fill="none" stroke="#F03E82" stroke-width="2"/>
<text x="399" y="164" text-anchor="middle" font-size="13" fill="#B0205A" font-weight="700">{$t[1]}</text>
<text x="24" y="176" font-size="14">{$t[2]}</text>
<rect x="8" y="204" width="624" height="128" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="24" y="230" font-weight="700" fill="#0B7F5C">{$t[3]}</text>
<text x="24" y="264" font-family="ui-monospace, Menlo, monospace" font-size="14">SELECT * FROM utenti WHERE email = <tspan font-weight="700" fill="#0B7F5C">?</tspan></text>
<rect x="420" y="240" width="190" height="56" rx="10" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/>
<text x="515" y="264" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="14">x' OR '1'='1</text>
<text x="515" y="285" text-anchor="middle" font-size="13">{$t[4]}</text>
<path d="M418,262 H352" stroke="#1E1C52" stroke-width="2" marker-end="url(#a)"/>
<text x="24" y="316" font-size="14">{$t[5]}</text>
</svg>
SVG;
    }
}

/** A passkey: the private key never leaves the device and the key works only for the domain it was made for, so a look-alike site gets nothing. */
if (!function_exists('fig_ws_passkey')) {
    function fig_ws_passkey(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker>
<marker id="p" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#F03E82"/></marker></defs>
<rect x="16" y="60" width="200" height="150" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="116" y="88" text-anchor="middle" font-weight="700">{$t[0]}</text>
<rect x="36" y="104" width="160" height="84" rx="10" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/>
<text x="116" y="132" text-anchor="middle" font-size="14" font-weight="700">{$t[1]}</text>
<text x="116" y="154" text-anchor="middle" font-size="13" font-family="ui-monospace, Menlo, monospace">esempio.it</text>
<text x="116" y="176" text-anchor="middle" font-size="13">{$t[2]}</text>
<rect x="424" y="20" width="200" height="100" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="524" y="48" text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace">esempio.it</text>
<text x="524" y="74" text-anchor="middle" font-size="14">{$t[3]}</text>
<text x="524" y="98" text-anchor="middle" font-size="13">{$t[4]}</text>
<rect x="424" y="180" width="200" height="100" rx="14" fill="#FFE3EE" stroke="#F03E82" stroke-width="2" stroke-dasharray="7 4"/>
<text x="524" y="208" text-anchor="middle" font-weight="700" font-family="ui-monospace, Menlo, monospace">esernpio-login.it</text>
<text x="524" y="234" text-anchor="middle" font-size="14">{$t[5]}</text>
<g stroke-width="2" fill="none"><path d="M418,58 C330,58 300,96 222,104" stroke="#1E1C52" marker-end="url(#a)"/><path d="M222,128 C300,124 330,90 418,86" stroke="#1E1C52" marker-end="url(#a)"/>
<path d="M418,226 C330,226 300,190 222,180" stroke="#F03E82" stroke-dasharray="7 4" marker-end="url(#p)"/></g>
<g font-size="13" text-anchor="middle"><text x="320" y="62">{$t[6]}</text><text x="320" y="122">{$t[7]}</text></g>
<path d="M292,192 L312,212 M312,192 L292,212" stroke="#F03E82" stroke-width="3"/>
<text x="320" y="246" text-anchor="middle" font-size="13" fill="#B0205A" font-weight="700">{$t[8]}</text>
</svg>
SVG;
    }
}

/** Clickjacking as an exploded view: the visible bait page behind, the invisible framed site in front, its button right over the bait. */
if (!function_exists('fig_ws_clickjack')) {
    function fig_ws_clickjack(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<rect x="40" y="70" width="300" height="200" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<text x="60" y="100" font-weight="700" fill="#B84508">{$t[0]}</text>
<text x="60" y="136" font-size="17" font-weight="700">{$t[1]}</text>
<rect x="110" y="190" width="160" height="44" rx="22" fill="#E8590C"/><text x="190" y="218" text-anchor="middle" font-weight="700" fill="#FFFFFF">{$t[2]}</text>
<rect x="300" y="20" width="300" height="200" rx="12" fill="#FFFFFF" fill-opacity="0.85" stroke="#7048E8" stroke-width="2" stroke-dasharray="8 5"/>
<text x="320" y="50" font-weight="700" fill="#5A35C9">{$t[3]}</text>
<text x="320" y="74" font-size="13" font-family="ui-monospace, Menlo, monospace">banca.esempio.it</text>
<rect x="370" y="140" width="160" height="44" rx="22" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/><text x="450" y="168" text-anchor="middle" font-weight="700" fill="#5A35C9">{$t[4]}</text>
<path d="M270,212 L370,162" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 4"/>
<path d="M210,226 l0,22 l6,-6 l5,11 l5,-2 l-5,-11 l8,0 z" fill="#1E1C52" stroke="#FFFFFF" stroke-width="1.5"/>
<text x="360" y="252" font-size="13">{$t[5]}</text>
<text x="360" y="272" font-size="13">{$t[6]}</text>
</svg>
SVG;
    }
}


return [
    'id' => 'web-security', 'family' => 'security', 'after' => null,
    'title' => L('Sicurezza web (OWASP)', 'Web Security (OWASP)'), 'short' => L('Sicurezza web', 'Web Security'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Le vulnerabilità più comuni delle applicazioni web secondo la OWASP Top 10 2025 e come evitarle: injection, XSS, password, passkey e sessioni, controllo degli accessi, CSRF, CORS e intestazioni di sicurezza, crittografia e segreti.', 'The most common web application vulnerabilities according to the OWASP Top 10 2025 and how to avoid them: injection, XSS, passwords, passkeys and sessions, access control, CSRF, CORS and security headers, cryptography and secrets.'),
    'lessons' => [
        lesson('injection', L('Injection', 'Injection'), [
            P('Si ha **injection** quando dati dell’utente finiscono dentro un comando (una query SQL, un comando di sistema) e vengono **interpretati come codice**. È una delle vulnerabilità più gravi della lista **OWASP Top 10** (nell’edizione 2025 è al quinto posto e comprende anche l’XSS): permette di leggere o cancellare interi database.', '**Injection** happens when user data ends up inside a command (an SQL query, a system command) and is **interpreted as code**. It is one of the most serious vulnerabilities on the **OWASP Top 10** list (in the 2025 edition it is fifth and also includes XSS): it can let attackers read or delete whole databases.'),
            C('// ✗ vulnerabile: con email = "x\' OR \'1\'=\'1" restituisce tutti gli utenti
$sql = "SELECT * FROM utenti WHERE email = \'" . $_POST["email"] . "\'";

// ✓ query preparata: il dato non può diventare codice
$st = $pdo->prepare("SELECT * FROM utenti WHERE email = ?");
$st->execute([$_POST["email"]]);', 'php'),
            P('La difesa è sempre la stessa: **mai concatenare** i dati dell’utente nei comandi. Usa **query preparate** (parametri `?` o con nome), gli ORM, e per i comandi di sistema le funzioni che passano gli argomenti separati, senza shell.', 'The defence is always the same: **never concatenate** user data into commands. Use **prepared statements** (`?` or named parameters), ORMs, and for system commands functions that pass arguments separately, without a shell.'),
            Fig(L(fig_ws_sqli(['Query concatenata', 'diventa codice', 'risultato: la condizione è sempre vera, escono tutti gli utenti', 'Query preparata', 'viaggia a parte: solo un dato', 'risultato: nessun utente ha quella email strana']), fig_ws_sqli(['Concatenated query', 'becomes code', 'result: the condition is always true, every user comes out', 'Prepared query', 'travels separately: just data', 'result: no user has that odd email'])),
                L('In alto il campo email con il testo x’ OR ’1’=’1. Nel riquadro rosso la query concatenata: il testo entra nella query e la parte OR ’1’=’1’ diventa codice, così escono tutti gli utenti. Nel riquadro verde la query preparata con il punto di domanda: lo stesso testo viaggia a parte come dato e non trova nessuno.', 'At the top the email field with the text x’ OR ’1’=’1. In the red box the concatenated query: the text enters the query and the OR ’1’=’1’ part becomes code, so every user comes out. In the green box the prepared query with the question mark: the same text travels separately as data and finds no one.'),
                L('Con i parametri il database sa sempre dove finisce il codice e dove comincia il dato.', 'With parameters the database always knows where the code ends and the data begins.')),
        ], [
            Q('i1', L('Che cos’è una SQL injection?', 'What is SQL injection?'), [L('Dati dell’utente interpretati come codice SQL', 'User data interpreted as SQL code'), L('Un database lento', 'A slow database'), L('Un backup', 'A backup'), L('Un errore di battitura', 'A typo')], 0, L('Può esporre tutto il database.', 'It can expose the whole database.')),
            Q('i2', L('Qual è la difesa principale?', 'What is the main defence?'), [L('Query preparate con parametri', 'Prepared statements with parameters'), L('Togliere gli spazi', 'Removing spaces'), L('Usare il maiuscolo', 'Using capitals'), L('Nascondere il form', 'Hiding the form')], 0, L('Il dato resta dato.', 'Data stays data.')),
            Q('i3', L('Che cos’è l’OWASP Top 10?', 'What is the OWASP Top 10?'), [L('L’elenco dei rischi più critici per le app web', 'The list of the most critical web app risks'), L('Una classifica di linguaggi', 'A language ranking'), L('Un framework', 'A framework'), L('Un antivirus', 'An antivirus')], 0, L('Aggiornato periodicamente.', 'Updated periodically.')),
        ], task('php', L('Riscrivi la query in modo sicuro con PDO: prepara `SELECT * FROM ordini WHERE id = ?` ed esegui passando `[$id]`.', 'Rewrite the query safely with PDO: prepare `SELECT * FROM ordini WHERE id = ?` and execute passing `[$id]`.'), "\$sql = \"SELECT * FROM ordini WHERE id = \" . \$id;", [re('\$\w+\s*=\s*\$pdo->prepare\s*\(\s*["\']SELECT \* FROM ordini WHERE id = \?["\']\s*\)', L('La query preparata', 'The prepared statement')), re('->execute\s*\(\s*\[\s*\$id\s*\]\s*\)', L('Il parametro separato', 'The separate parameter'))], '', "\$st = \$pdo->prepare(\"SELECT * FROM ordini WHERE id = ?\");\n\$st->execute([\$id]);"), [
            dBug(pBug(), ['$nome = $_GET["nome"];', '$st = $pdo->query("SELECT * FROM prodotti WHERE nome = \'$nome\'");', '$righe = $st->fetchAll();'], 1, '$st = $pdo->prepare("SELECT * FROM prodotti WHERE nome = ?"); $st->execute([$nome]);', L('I dati dell’utente non vanno mai concatenati nella query.', 'User data must never be concatenated into the query.')),
            dFlash(pFlash(), [[L('Le query preparate separano dati e codice', 'Prepared statements separate data from code'), true], [L('Togliere gli apici basta sempre a proteggersi', 'Removing quotes is always enough'), false], [L('L’injection può riguardare anche i comandi di sistema', 'Injection can also affect system commands'), true], [L('L’injection è un rischio minore', 'Injection is a minor risk'), false]], L('Il dato resta dato.', 'Data stays data.')),
        ]),
        lesson('xss', L('Cross-site scripting (XSS)', 'Cross-site scripting (XSS)'), [
            P('Con l’**XSS** un attaccante fa eseguire il suo JavaScript nel browser di altri utenti, per esempio scrivendo `<script>…</script>` in un commento che il sito mostra senza filtrarlo. Così può rubare sessioni, modificare la pagina o agire a nome della vittima.', 'With **XSS** an attacker gets their JavaScript run in other users’ browsers, for example by writing `<script>…</script>` in a comment the site shows unfiltered. That way they can steal sessions, change the page or act on the victim’s behalf.'),
            Fig(L(fig_ws_flow([[16, 20, 160, 'Attaccante', 'scrive un commento', 'bad'], [240, 20, 160, 'Sito', 'salva e mostra', 'site'], [464, 20, 160, 'Vittima', 'apre la pagina', 'me']], [['M178,44 H232', 1, 205, 30, true], ['M402,52 H456', 2, 429, 38, false], ['M544,86 C544,130 96,130 96,92', 3, 320, 120, true]], [['L’attaccante pubblica un commento con dentro uno &lt;script&gt;.', true], ['Il sito lo salva e lo inserisce nella pagina senza codificarlo.', false], ['Il browser della vittima esegue lo script, che manda i suoi cookie all’attaccante.', true]]), fig_ws_flow([[16, 20, 160, 'Attacker', 'writes a comment', 'bad'], [240, 20, 160, 'Site', 'stores and shows', 'site'], [464, 20, 160, 'Victim', 'opens the page', 'me']], [['M178,44 H232', 1, 205, 30, true], ['M402,52 H456', 2, 429, 38, false], ['M544,86 C544,130 96,130 96,92', 3, 320, 120, true]], [['The attacker posts a comment with a &lt;script&gt; inside.', true], ['The site stores it and puts it in the page without encoding it.', false], ['The victim’s browser runs the script, which sends their cookies to the attacker.', true]])),
                L('Tre riquadri: attaccante, sito, vittima. 1: l’attaccante pubblica un commento con uno script. 2: il sito lo salva e lo mostra senza codificarlo. 3: il browser della vittima esegue lo script, che rimanda i cookie all’attaccante.', 'Three boxes: attacker, site, victim. 1: the attacker posts a comment with a script. 2: the site stores it and shows it without encoding. 3: the victim’s browser runs the script, which sends the cookies back to the attacker.'),
                L('Il passo da spezzare è il 2: codificato, lo script diventa testo innocuo.', 'The step to break is number 2: encoded, the script becomes harmless text.')),
            C('// ✗ vulnerabile
elemento.innerHTML = commento.testo;
echo "<p>" . $_GET["q"] . "</p>";

// ✓ testo trattato come testo
elemento.textContent = commento.testo;
echo "<p>" . htmlspecialchars($_GET["q"], ENT_QUOTES, "UTF-8") . "</p>";', 'js', L('<p style="font:13px system-ui;color:#555;margin:0 0 6px">Il commento <code>&lt;img src=x onerror=alert(1)&gt; Bel post!</code> mostrato come testo:</p>
<p style="border:1px solid #ccc;border-radius:8px;padding:8px 12px;margin:0">&lt;img src=x onerror=alert(1)&gt; Bel post!</p>', '<p style="font:13px system-ui;color:#555;margin:0 0 6px">The comment <code>&lt;img src=x onerror=alert(1)&gt; Nice post!</code> shown as text:</p>
<p style="border:1px solid #ccc;border-radius:8px;padding:8px 12px;margin:0">&lt;img src=x onerror=alert(1)&gt; Nice post!</p>')),
            P('Difese: **codificare l’output** (escaping) quando inserisci dati in una pagina, usare `textContent` invece di `innerHTML`, affidarsi a framework che fanno l’escaping da soli (React, Vue, i template moderni) e aggiungere una **Content Security Policy** che limita da dove possono arrivare gli script.', 'Defences: **encode output** (escaping) when inserting data into a page, use `textContent` instead of `innerHTML`, rely on frameworks that escape by themselves (React, Vue, modern templates) and add a **Content Security Policy** limiting where scripts can come from.'),
        ], [
            Q('x1', L('Che cosa fa un attacco XSS?', 'What does an XSS attack do?'), [L('Esegue JavaScript dell’attaccante nel browser della vittima', 'Runs the attacker’s JavaScript in the victim’s browser'), L('Rallenta il server', 'Slows the server'), L('Cancella il disco', 'Wipes the disk'), L('Cambia la password del Wi-Fi', 'Changes the Wi-Fi password')], 0, L('Può rubare le sessioni.', 'It can steal sessions.')),
            Q('x2', L('Che cosa usare al posto di innerHTML per testo dell’utente?', 'What to use instead of innerHTML for user text?'), ['textContent', 'outerHTML', 'eval', 'document.write'], 0, L('Il testo resta testo.', 'Text stays text.')),
            Q('x3', L('Che cosa fa una Content Security Policy?', 'What does a Content Security Policy do?'), [L('Limita da dove possono arrivare gli script', 'Limits where scripts can come from'), L('Cifra il database', 'Encrypts the database'), L('Blocca le immagini sempre', 'Always blocks images'), L('Velocizza il sito', 'Speeds up the site')], 0, L('Una seconda difesa.', 'A second defence.')),
        ], task('php', L('Stampa il parametro `$_GET["nome"]` in un `<h1>` in modo sicuro con `htmlspecialchars(..., ENT_QUOTES, "UTF-8")`.', 'Print the `$_GET["nome"]` parameter in an `<h1>` safely with `htmlspecialchars(..., ENT_QUOTES, "UTF-8")`.'), '', [re('htmlspecialchars\s*\(\s*\$_GET\s*\[\s*["\']nome["\']\s*\]\s*,\s*ENT_QUOTES\s*,\s*["\']UTF-8["\']\s*\)', L('Escaping corretto', 'Correct escaping')), re('<h1>', L('Dentro un h1', 'Inside an h1'))], '', "echo \"<h1>\" . htmlspecialchars(\$_GET[\"nome\"], ENT_QUOTES, \"UTF-8\") . \"</h1>\";"), [
            dBug(pBug(), ['const box = document.querySelector("#saluto");', 'const nome = new URLSearchParams(location.search).get("nome");', 'box.innerHTML = "Ciao " + nome;'], 2, 'box.textContent = "Ciao " + nome;', L('Dati dall’URL con innerHTML aprono la porta all’XSS.', 'URL data with innerHTML opens the door to XSS.')),
            dMatch(pMatch(), [['XSS', L('Script iniettati nella pagina', 'Scripts injected into the page')], ['htmlspecialchars', L('Escaping in PHP', 'Escaping in PHP')], ['textContent', L('Inserisce solo testo', 'Inserts only text')], ['CSP', L('Limita gli script', 'Limits scripts')]], L('Difendersi dall’XSS.', 'Defending against XSS.')),
        ]),
        lesson('auth', L('Password, sessioni e accessi', 'Passwords, sessions and access'), [
            P('Le password non si salvano mai in chiaro né con hash veloci (MD5, SHA-1): si usano algoritmi lenti e con **salt** come **bcrypt** o **Argon2** (`password_hash` in PHP). Aggiungi limiti ai tentativi e, dove possibile, l’autenticazione a più fattori.', 'Passwords are never stored in plain text or with fast hashes (MD5, SHA-1): you use slow, **salted** algorithms like **bcrypt** or **Argon2** (`password_hash` in PHP). Add attempt limits and, where possible, multi-factor authentication.'),
            C('$hash = password_hash($password, PASSWORD_DEFAULT);       // alla registrazione
if (password_verify($tentativo, $hash)) { /* accesso */ }  // al login

// cookie di sessione sicuro
setcookie("sessione", $token, ["httponly" => true, "secure" => true, "samesite" => "Lax"]);', 'php'),
            P('Il **controllo degli accessi** rotto è il primo rischio della lista OWASP: verificare sul server, a ogni richiesta, che l’utente possa davvero vedere o modificare **quella** risorsa (`/ordini/42` è suo?). Nascondere un pulsante nell’interfaccia non è una protezione.', 'Broken **access control** is the top risk on the OWASP list: verify on the server, on every request, that the user can really see or change **that** resource (is `/ordini/42` theirs?). Hiding a button in the interface is not protection.'),
        ], [
            Q('a1', L('Come si salvano le password?', 'How should passwords be stored?'), [L('Con un hash lento e con salt come bcrypt', 'With a slow, salted hash like bcrypt'), L('In chiaro', 'In plain text'), L('Con MD5', 'With MD5'), L('In un file di testo', 'In a text file')], 0, L('`password_hash` in PHP.', '`password_hash` in PHP.')),
            Q('a2', L('Che cosa fa il flag `httponly` di un cookie?', 'What does a cookie’s `httponly` flag do?'), [L('Impedisce a JavaScript di leggerlo', 'Prevents JavaScript from reading it'), L('Lo rende pubblico', 'Makes it public'), L('Lo cancella subito', 'Deletes it at once'), L('Lo cifra con la password', 'Encrypts it with the password')], 0, L('Così uno script iniettato con l’XSS non può rubare la sessione.', 'That way a script injected through XSS cannot steal the session.')),
            Q('a3', L('Nascondere un pulsante basta a proteggere una funzione?', 'Is hiding a button enough to protect a feature?'), [L('No, serve il controllo sul server', 'No, a server-side check is needed'), L('Sì', 'Yes'), L('Solo su mobile', 'Only on mobile'), L('Solo con CSS', 'Only with CSS')], 0, L('Le richieste si possono fare a mano.', 'Requests can be crafted by hand.')),
        ], task('php', L('Alla registrazione salva in `$hash` il risultato di `password_hash($password, PASSWORD_DEFAULT)`; al login verifica `$tentativo` con `password_verify`.', 'At sign-up store in `$hash` the result of `password_hash($password, PASSWORD_DEFAULT)`; at login check `$tentativo` with `password_verify`.'), '', [re('\$hash\s*=\s*password_hash\s*\(\s*\$password\s*,\s*PASSWORD_DEFAULT\s*\)', L('L’hash sicuro', 'The safe hash')), re('password_verify\s*\(\s*\$tentativo\s*,\s*\$hash\s*\)', L('La verifica', 'The check'))], '', "\$hash = password_hash(\$password, PASSWORD_DEFAULT);\nif (password_verify(\$tentativo, \$hash)) {\n    // accesso consentito\n}"), [
            dMatch(pMatch(), [['bcrypt / Argon2', L('Hash sicuri per password', 'Safe password hashes')], ['MD5', L('Da non usare per le password', 'Not for passwords')], ['httponly', L('Cookie non leggibile da JS', 'Cookie unreadable by JS')], ['secure', L('Cookie solo su HTTPS', 'Cookie only over HTTPS')], [L('Controllo accessi', 'Access control'), L('Verificato sul server', 'Checked on the server')]], L('Identità al sicuro.', 'Identity kept safe.')),
            dBug(pBug(), ['$id = (int)$_GET["ordine"];', '$st = $pdo->prepare("SELECT * FROM ordini WHERE id = ?");', '$st->execute([$id]);'], 1, '$st = $pdo->prepare("SELECT * FROM ordini WHERE id = ? AND utente_id = ?");', L('Manca il controllo che l’ordine sia dell’utente.', 'The check that the order belongs to the user is missing.')),
        ]),
        lesson('csrf', L('CSRF e intestazioni di sicurezza', 'CSRF and security headers'), [
            P('Con il **CSRF** (Cross-Site Request Forgery) un sito malevolo fa partire dal browser della vittima una richiesta verso il tuo sito, dove la vittima è già collegata (per esempio un modulo nascosto che cambia l’email). Difese: **token CSRF** casuali nei moduli verificati dal server e cookie con **SameSite**.', 'With **CSRF** (Cross-Site Request Forgery) a malicious site makes the victim’s browser send a request to your site, where the victim is already logged in (for example a hidden form changing the email). Defences: random **CSRF tokens** in forms checked by the server and cookies with **SameSite**.'),
            Fig(L(fig_ws_flow([[16, 20, 170, 'Sito malevolo', 'pagina con trappola', 'bad'], [235, 20, 170, 'Browser', 'della vittima', 'me'], [454, 20, 170, 'banca.esempio.it', 'cookie di sessione', 'site']], [['M407,40 H446', 1, 426, 28, false], ['M233,40 H194', 2, 213, 28, false], ['M188,70 H227', 3, 207, 82, true], ['M320,86 C320,130 539,130 539,92', 4, 430, 120, true]], [['La vittima accede alla banca: il browser conserva il cookie di sessione.', false], ['Poi apre il sito malevolo.', false], ['La pagina contiene un modulo nascosto che parte da solo verso la banca.', true], ['Il browser aggiunge il cookie: senza token CSRF né SameSite, la banca esegue.', true]]), fig_ws_flow([[16, 20, 170, 'Malicious site', 'page with a trap', 'bad'], [235, 20, 170, 'Browser', 'of the victim', 'me'], [454, 20, 170, 'banca.esempio.it', 'session cookie', 'site']], [['M407,40 H446', 1, 426, 28, false], ['M233,40 H194', 2, 213, 28, false], ['M188,70 H227', 3, 207, 82, true], ['M320,86 C320,130 539,130 539,92', 4, 430, 120, true]], [['The victim signs in to the bank: the browser keeps the session cookie.', false], ['Then they open the malicious site.', false], ['The page holds a hidden form that submits itself to the bank.', true], ['The browser adds the cookie: with no CSRF token or SameSite, the bank obeys.', true]])),
                L('Tre riquadri: sito malevolo, browser della vittima, banca. 1: la vittima accede alla banca e ha il cookie. 2: apre il sito malevolo. 3: la pagina fa partire un modulo nascosto. 4: la richiesta arriva alla banca con il cookie e, senza difese, viene eseguita.', 'Three boxes: malicious site, the victim’s browser, the bank. 1: the victim signs in to the bank and has the cookie. 2: they open the malicious site. 3: the page fires a hidden form. 4: the request reaches the bank with the cookie and, with no defences, is carried out.'),
                L('Il cookie viaggia da solo: per questo serve un token che il sito malevolo non può conoscere.', 'The cookie travels by itself: that is why you need a token the malicious site cannot know.')),
            C('Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: default-src \'self\'; img-src \'self\' data:
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()', 'http'),
            P('Le **intestazioni di sicurezza** aggiungono protezioni del browser: HSTS forza HTTPS, CSP limita script e risorse, `nosniff` impedisce interpretazioni pericolose dei file. Infine: tieni aggiornate le dipendenze, registra gli errori senza dati sensibili e non mostrare dettagli tecnici agli utenti.', '**Security headers** add browser protections: HSTS forces HTTPS, CSP limits scripts and resources, `nosniff` prevents dangerous file interpretation. Finally: keep dependencies updated, log errors without sensitive data and do not show technical details to users.'),
        ], [
            Q('c1', L('Che cosa sfrutta un attacco CSRF?', 'What does a CSRF attack exploit?'), [L('La sessione già aperta della vittima', 'The victim’s already open session'), L('Una password debole', 'A weak password'), L('Un server lento', 'A slow server'), L('Il Wi-Fi pubblico', 'Public Wi-Fi')], 0, L('Il browser invia i cookie da solo.', 'The browser sends cookies by itself.')),
            Q('c2', L('Che cosa fa l’intestazione HSTS?', 'What does the HSTS header do?'), [L('Obbliga il browser a usare HTTPS', 'Forces the browser to use HTTPS'), L('Blocca i cookie', 'Blocks cookies'), L('Cifra il database', 'Encrypts the database'), L('Nasconde il sito', 'Hides the site')], 0, L('Strict-Transport-Security.', 'Strict-Transport-Security.')),
            Q('c3', L('Quale difesa contro il CSRF?', 'Which defence against CSRF?'), [L('Token CSRF nei moduli e cookie SameSite', 'CSRF tokens in forms and SameSite cookies'), L('Password più lunghe', 'Longer passwords'), L('Più RAM', 'More RAM'), L('Niente', 'Nothing')], 0, L('Il server verifica il token.', 'The server checks the token.')),
        ], task('http', L('Scrivi le intestazioni HTTP per: forzare HTTPS per un anno (`max-age=31536000`), permettere script solo dal proprio sito (`default-src \'self\'`) e attivare `nosniff`.', 'Write the HTTP headers to: force HTTPS for a year (`max-age=31536000`), allow scripts only from your own site (`default-src \'self\'`) and turn on `nosniff`.'), '', [re('^Strict-Transport-Security:\s*max-age=31536000', L('HSTS', 'HSTS')), re('^Content-Security-Policy:\s*default-src\s+\'self\'', L('CSP', 'CSP')), re('^X-Content-Type-Options:\s*nosniff', L('nosniff', 'nosniff'))], '', "Strict-Transport-Security: max-age=31536000\nContent-Security-Policy: default-src 'self'\nX-Content-Type-Options: nosniff"), [
            dMatch(pMatch(), [['CSRF', L('Richieste falsificate da un altro sito', 'Requests forged from another site')], ['SameSite', L('Cookie non inviati da altri siti', 'Cookies not sent from other sites')], ['HSTS', L('Solo HTTPS', 'HTTPS only')], ['CSP', L('Limita le risorse', 'Limits resources')], ['nosniff', L('Niente interpretazioni dei file', 'No file sniffing')]], L('Le difese del browser.', 'Browser defences.')),
            dFlash(pFlash(), [[L('Un token CSRF deve essere casuale e verificato', 'A CSRF token must be random and verified'), true], [L('HSTS permette di usare HTTP senza cifratura', 'HSTS allows unencrypted HTTP'), false], [L('Le dipendenze vanno tenute aggiornate', 'Dependencies should be kept updated'), true], [L('Mostrare gli errori tecnici agli utenti aiuta la sicurezza', 'Showing technical errors to users helps security'), false]], L('Sicurezza a strati.', 'Security in layers.')),
        ]),
        lesson('ssrf-supply', L('SSRF, upload e dipendenze', 'SSRF, uploads and dependencies'), [
            P('Con la **SSRF** (server-side request forgery) un attaccante fa scaricare al tuo server un indirizzo che sceglie lui, magari interno (`http://localhost/admin` o i metadati del cloud). Se un utente può dare un URL, accetta solo domini in una **lista consentita** e blocca gli indirizzi interni.', 'With **SSRF** (server-side request forgery) an attacker makes your server fetch an address of their choosing, perhaps an internal one (`http://localhost/admin` or cloud metadata). If a user can supply a URL, accept only domains on an **allow list** and block internal addresses.'),
            Fig(L(fig_ws_flow([[16, 20, 150, 'Attaccante', 'sceglie l’URL', 'bad'], [222, 20, 170, 'La tua app', 'scarica l’URL', 'site'], [448, 20, 176, 'Rete interna', '169.254.169.254', 'in']], [['M168,44 H214', 1, 191, 30, true], ['M394,44 H440', 2, 417, 30, true], ['M440,72 H394', 3, 417, 86, true], ['M220,72 H170', 4, 195, 86, true]], [['L’attaccante manda ?url=http://169.254.169.254/… a una funzione «anteprima link».', true], ['Il server scarica l’indirizzo dall’interno, dove l’attaccante non arriverebbe.', true], ['Il servizio dei metadati del cloud risponde con credenziali temporanee.', true], ['L’app le restituisce all’attaccante. Difesa: lista consentita e niente indirizzi interni.', true]]), fig_ws_flow([[16, 20, 150, 'Attacker', 'picks the URL', 'bad'], [222, 20, 170, 'Your app', 'fetches the URL', 'site'], [448, 20, 176, 'Internal network', '169.254.169.254', 'in']], [['M168,44 H214', 1, 191, 30, true], ['M394,44 H440', 2, 417, 30, true], ['M440,72 H394', 3, 417, 86, true], ['M220,72 H170', 4, 195, 86, true]], [['The attacker sends ?url=http://169.254.169.254/… to a “link preview” feature.', true], ['The server fetches the address from inside, where the attacker could not reach.', true], ['The cloud metadata service answers with temporary credentials.', true], ['The app hands them back. Defence: an allow list and no internal addresses.', true]])),
                L('Tre riquadri: attaccante, la tua app, rete interna con l’indirizzo dei metadati 169.254.169.254. 1: l’attaccante passa un URL interno. 2: l’app lo scarica. 3: il servizio interno risponde con credenziali. 4: l’app le restituisce all’attaccante.', 'Three boxes: attacker, your app, internal network with the metadata address 169.254.169.254. 1: the attacker passes an internal URL. 2: the app fetches it. 3: the internal service answers with credentials. 4: the app hands them back to the attacker.'),
                L('Il server ha accessi che l’attaccante non ha: la SSRF li prende in prestito.', 'The server has access the attacker does not: SSRF borrows it.')),
            P('Nei **caricamenti di file** controlla tipo e dimensione, rinomina il file, salvalo **fuori** dalla cartella pubblica o in modo che non venga mai eseguito. Le **dipendenze** sono codice di altri: aggiornale, controllale con `npm audit` o strumenti simili, e fissa le versioni con il file di lock. Nella OWASP Top 10 2025 la SSRF rientra nel controllo degli accessi, mentre i guasti della catena di fornitura del software sono al terzo posto.', 'For **file uploads** check type and size, rename the file, store it **outside** the public folder or so that it is never executed. **Dependencies** are other people’s code: keep them updated, check them with `npm audit` or similar tools, and pin versions with the lock file. In the OWASP Top 10 2025 SSRF falls under access control, while software supply chain failures are in third place.'),
        ], [
            Q('ss1', L('Che cos’è la SSRF?', 'What is SSRF?'), [L('Far scaricare al server un indirizzo scelto dall’attaccante', 'Making the server fetch an attacker-chosen address'), L('Un attacco al browser', 'An attack on the browser'), L('Un tipo di password', 'A kind of password'), L('Un errore CSS', 'A CSS error')], 0, L('Spesso verso indirizzi interni.', 'Often towards internal addresses.')),
            Q('ss2', L('Dove salvi i file caricati?', 'Where do you store uploaded files?'), [L('Fuori dalla cartella pubblica, senza esecuzione', 'Outside the public folder, never executed'), L('Nella cartella del codice', 'In the code folder'), L('Con il nome originale ovunque', 'With the original name anywhere'), L('Nel database come codice', 'In the database as code')], 0, L('Un file .php caricato non deve girare.', 'An uploaded .php file must not run.')),
            Q('ss3', L('A che cosa serve `npm audit`?', 'What is `npm audit` for?'), [L('Trovare dipendenze con falle note', 'Finding dependencies with known flaws'), L('Pubblicare un pacchetto', 'Publishing a package'), L('Avviare il server', 'Starting the server'), L('Comprimere il codice', 'Minifying code')], 0, L('Le falle note hanno una correzione.', 'Known flaws have a fix.')),
        ], task('bash', L('Controlla le dipendenze con `npm audit` e poi installa le versioni fissate con `npm ci`.', 'Check dependencies with `npm audit` and then install the pinned versions with `npm ci`.'), '', [re('npm\s+audit', L('Controlli le falle', 'You check flaws')), re('npm\s+ci', L('Installi dal lock', 'You install from the lock'))], '', "npm audit\nnpm ci"), [
            dMatch(pMatch(), [['SSRF', L('Il server scarica dove vuole l’attaccante', 'Server fetches where the attacker wants')], [L('Lista consentita', 'Allow list'), L('Solo domini noti', 'Known domains only')], ['Upload', L('Controlla tipo e dimensione', 'Check type and size')], ['npm audit', L('Falle nelle dipendenze', 'Flaws in dependencies')], ['Lock file', L('Versioni fissate', 'Pinned versions')]], L('Difese moderne.', 'Modern defences.')),
            dFlash(pFlash(), [[L('localhost può essere un bersaglio della SSRF', 'localhost can be an SSRF target'), true], [L('Un file caricato può tenere il suo nome e stare ovunque', 'An uploaded file can keep its name and go anywhere'), false], [L('Le dipendenze vanno aggiornate', 'Dependencies should be updated'), true], [L('npm audit corregge tutto da solo, sempre', 'npm audit always fixes everything itself'), false]], L('Superficie d’attacco.', 'Attack surface.')),
        ]),
        lesson('top10-2025', L('La OWASP Top 10 2025', 'The OWASP Top 10 2025'), [
            P('La **OWASP Top 10** elenca i rischi più critici per le applicazioni web e viene aggiornata ogni pochi anni. L’edizione 2025 conferma al primo posto il **controllo degli accessi rotto** (che ora comprende la SSRF), fa salire la **configurazione sbagliata** al secondo e introduce due categorie nuove: i **guasti della catena di fornitura del software** e la **gestione sbagliata delle condizioni eccezionali**.', 'The **OWASP Top 10** lists the most critical risks for web applications and is updated every few years. The 2025 edition keeps **broken access control** in first place (now including SSRF), moves **security misconfiguration** up to second and introduces two new categories: **software supply chain failures** and **mishandling of exceptional conditions**.'),
            C('A01  Broken Access Control             accesso a dati e funzioni di altri (anche SSRF)
A02  Security Misconfiguration          impostazioni di default, debug, permessi larghi
A03  Software Supply Chain Failures     dipendenze, build e strumenti compromessi
A04  Cryptographic Failures             dati non cifrati, algoritmi deboli
A05  Injection                          dati interpretati come codice (anche XSS)
A06  Insecure Design                    difetti nel progetto, non nel codice
A07  Authentication Failures            login, sessioni, credenziali
A08  Software or Data Integrity Failures  aggiornamenti e dati non verificati
A09  Security Logging & Alerting Failures attacchi che nessuno vede
A10  Mishandling of Exceptional Conditions errori gestiti male', 'text'),
            C('async function puoLeggereSbagliato(utente, risorsa) {
  try { return await permessi.verifica(utente, risorsa); }
  catch { return true; }                      // errore = accesso: fallisce aperto
}

async function puoLeggere(utente, risorsa) {
  try { return await permessi.verifica(utente, risorsa); }
  catch (e) {
    log.error("verifica dei permessi fallita", e);
    return false;                             // errore = niente accesso: fallisce chiuso
  }
}', 'js'),
            P('Per A10 la regola è **fallire chiusi**: se un controllo va in errore, si nega invece di concedere. Per A09 registra accessi, errori e permessi negati (senza password né token) e imposta allarmi, altrimenti un attacco passa inosservato. Usa la lista come promemoria, non come garanzia completa.', 'For A10 the rule is to **fail closed**: if a check errors out, you deny instead of granting. For A09 log sign-ins, errors and denied permissions (without passwords or tokens) and set alerts, otherwise an attack goes unnoticed. Use the list as a reminder, not as a complete guarantee.'),
        ], [
            Q('tt1', L('Che cosa c’è al primo posto della OWASP Top 10 2025?', 'What is in first place in the OWASP Top 10 2025?'), ['Injection', 'Broken Access Control', 'Security Misconfiguration', 'Cryptographic Failures'], 1, L('Per la quarta edizione di fila.', 'For the fourth edition in a row.')),
            Q('tt2', L('Che cosa vuol dire “fallire chiusi”?', 'What does “fail closed” mean?'), [L('Spegnere il server durante la notte', 'Switching the server off at night'), L('Chiudere la connessione dopo ogni richiesta', 'Closing the connection after every request'), L('Se qualcosa va storto si nega l’accesso invece di concederlo', 'If something goes wrong access is denied instead of granted'), L('Nascondere tutti i messaggi di errore', 'Hiding every error message')], 2, L('Un errore non deve aprire porte.', 'An error must not open doors.')),
            Q('tt3', L('Dove è finita la SSRF nell’edizione 2025?', 'Where did SSRF end up in the 2025 edition?'), [L('È stata tolta perché non esiste più', 'It was dropped because it no longer exists'), L('Dentro Injection', 'Inside Injection'), L('Dentro Cryptographic Failures', 'Inside Cryptographic Failures'), L('Dentro Broken Access Control', 'Inside Broken Access Control')], 3, L('Era la A10 del 2021.', 'It was A10 in 2021.')),
        ], task('js', L('Correggi il `catch` perché fallisca chiuso: registra l’errore con `console.error` (o `log.error`) e restituisci `false`.', 'Fix the `catch` so it fails closed: log the error with `console.error` (or `log.error`) and return `false`.'), "async function puoModificare(utente, risorsa) {\n  try {\n    return await permessi.verifica(utente, risorsa);\n  } catch (e) {\n    return true;\n  }\n}", [re('catch\s*\(\s*\w+\s*\)\s*\{[^}]*(console|log)\.(error|warn)\s*\(', L('Registri l’errore', 'You log the error')), re('catch\s*\(\s*\w+\s*\)\s*\{[^}]*return\s+false', L('Neghi l’accesso', 'You deny access'))], '', "async function puoModificare(utente, risorsa) {\n  try {\n    return await permessi.verifica(utente, risorsa);\n  } catch (e) {\n    console.error(\"verifica dei permessi fallita\", e);\n    return false;\n  }\n}"), [
            dMatch(pMatch(), [['A01 Broken Access Control', L('Dati e funzioni di altri', 'Others’ data and functions')], ['A02 Security Misconfiguration', L('Impostazioni sbagliate o di default', 'Wrong or default settings')], ['A03 Supply Chain Failures', L('Dipendenze e build compromesse', 'Compromised dependencies and builds')], ['A05 Injection', L('Dati interpretati come codice', 'Data interpreted as code')], ['A10 Exceptional Conditions', L('Errori gestiti male', 'Errors handled badly')]], L('La lista del 2025.', 'The 2025 list.')),
            dFlash(pFlash(), [[L('La lista viene aggiornata periodicamente', 'The list is updated periodically'), true], [L('Nel 2025 Injection è al primo posto', 'In 2025 Injection is in first place'), false], [L('L’XSS rientra in Injection', 'XSS falls under Injection'), true], [L('Rispettare la Top 10 garantisce un’app sicura', 'Following the Top 10 guarantees a secure app'), false]], L('Conoscere i rischi.', 'Knowing the risks.')),
        ]),
        lesson('modern-auth', L('Password secondo il NIST, passkey e sessioni', 'Passwords by NIST, passkeys and sessions'), [
            P('Le linee guida **NIST SP 800-63B-4** (2025) dicono: se la password è l’unico fattore, almeno **15 caratteri** (8 se fa parte di un’autenticazione a più fattori); accettarne almeno 64; **niente regole di composizione** (maiuscole, simboli obbligatori); **niente cambio periodico** forzato, solo se c’è un indizio di furto; confronto con un elenco di password **violate o troppo comuni**; permettere gestori di password e incolla.', 'The **NIST SP 800-63B-4** guidelines (2025) say: if the password is the only factor, at least **15 characters** (8 if it is part of multi-factor authentication); accept at least 64; **no composition rules** (mandatory capitals, symbols); **no forced periodic change**, only when there is evidence of theft; check against a list of **breached or overly common** passwords; allow password managers and paste.'),
            P('Le **passkey** (WebAuthn/FIDO2) usano una coppia di chiavi: la privata resta sul dispositivo (o sincronizzata dal gestore di password), il sito conserva solo la **pubblica**. Sono legate al dominio, quindi un sito falso non può usarle: resistono al phishing. Come secondo fattore preferisci passkey e chiavi di sicurezza, poi le app di codici, e solo per ultimi gli SMS.', '**Passkeys** (WebAuthn/FIDO2) use a key pair: the private key stays on the device (or synced by the password manager), the site keeps only the **public** one. They are bound to the domain, so a fake site cannot use them: they resist phishing. As a second factor prefer passkeys and security keys, then code apps, and SMS only last.'),
            Fig(L(fig_ws_passkey(['Il tuo dispositivo', 'chiave privata', 'non esce mai', 'chiave pubblica', 'niente segreti da rubare', 'nessuna chiave per questo dominio', 'sfida', 'firma', 'sito falso: niente firma']), fig_ws_passkey(['Your device', 'private key', 'never leaves', 'public key', 'no secrets to steal', 'no key for this domain', 'challenge', 'signature', 'fake site: no signature'])),
                L('A sinistra il dispositivo con la chiave privata legata a esempio.it, che non esce mai. In alto a destra il sito vero esempio.it, che conserva solo la chiave pubblica: manda una sfida e riceve una firma. In basso a destra il sito falso esernpio-login.it: il dispositivo non ha una chiave per quel dominio e non firma.', 'On the left the device with the private key bound to esempio.it, which never leaves. Top right the real site esempio.it, which keeps only the public key: it sends a challenge and receives a signature. Bottom right the fake site esernpio-login.it: the device has no key for that domain and does not sign.'),
                L('Il browser controlla il dominio al posto tuo: con una passkey non puoi «sbagliare sito».', 'The browser checks the domain for you: with a passkey you cannot “pick the wrong site”.')),
            C('const credenziale = await navigator.credentials.create({
  publicKey: {
    challenge: sfidaDalServer,                // byte casuali generati dal server
    rp: { name: "Fucina", id: "fucina.esempio.it" },
    user: { id: idUtente, name: "ada@esempio.it", displayName: "Ada" },
    pubKeyCredParams: [{ type: "public-key", alg: -7 }, { type: "public-key", alg: -257 }],
    authenticatorSelection: { residentKey: "required", userVerification: "preferred" },
  },
});
// poi si manda al server la risposta: il server la verifica e salva la chiave pubblica', 'js'),
            P('Per le **sessioni**: genera un nuovo identificativo dopo il login, falle scadere dopo un periodo di inattività, invalidale al logout e al cambio password; limita i tentativi di accesso e usa messaggi generici come “credenziali non valide”.', 'For **sessions**: generate a new identifier after sign-in, let them expire after a period of inactivity, invalidate them on sign-out and on password change; limit sign-in attempts and use generic messages such as “invalid credentials”.'),
        ], [
            Q('ma1', L('Secondo il NIST (2025), che cosa un sito NON deve imporre?', 'According to NIST (2025), what must a site NOT impose?'), [L('Una lunghezza minima', 'A minimum length'), L('Il confronto con le password già violate', 'The check against breached passwords'), L('Il cambio periodico obbligatorio della password', 'A mandatory periodic password change'), L('Il permesso di usare gestori di password', 'Permission to use password managers')], 2, L('Si cambia solo se c’è un sospetto di furto.', 'You change it only if theft is suspected.')),
            Q('ma2', L('Perché le passkey resistono al phishing?', 'Why do passkeys resist phishing?'), [L('Perché sono più lunghe delle password', 'Because they are longer than passwords'), L('Perché sono legate al dominio: su un sito falso non funzionano', 'Because they are bound to the domain: they do not work on a fake site'), L('Perché cambiano ogni giorno da sole', 'Because they change by themselves every day'), L('Perché arrivano via SMS', 'Because they arrive by SMS')], 1, L('Il browser le offre solo al dominio giusto.', 'The browser offers them only to the right domain.')),
            Q('ma3', L('Che cosa conserva il server per una passkey?', 'What does the server keep for a passkey?'), [L('La chiave privata dell’utente', 'The user’s private key'), L('L’impronta digitale dell’utente', 'The user’s fingerprint'), L('L’hash di una password', 'A password hash'), L('Solo la chiave pubblica', 'Only the public key')], 3, L('Un furto del database non rivela segreti.', 'A database theft reveals no secrets.')),
        ], task('text', L('Scrivi la politica delle password del tuo sito secondo il NIST, una regola per riga con `-`: la lunghezza minima, il controllo delle password violate o comuni, e cosa fare con il cambio periodico.', 'Write your site’s password policy according to NIST, one rule per line with `-`: the minimum length, the check of breached or common passwords, and what to do about periodic changes.'), "- \n- \n- ", [re('^-.*\b15\b', L('Almeno 15 caratteri', 'At least 15 characters')), re('^-.*(violat|breach|compromess|blocklist|elenco|lista|comun|common)', L('Il controllo delle password violate', 'The breached-password check')), re('^-.*(periodic|scadenz|expir|rotaz|rotation)', L('Il cambio periodico', 'Periodic changes'))], '', "- almeno 15 caratteri se la password è l’unico fattore\n- confronto con un elenco di password violate o troppo comuni\n- nessun cambio periodico obbligatorio, solo se c’è un sospetto di furto"), [
            dMatch(pMatch(), [['Passkey', L('Chiave legata al dominio', 'Key bound to the domain')], ['WebAuthn', L('L’API del browser per le passkey', 'The browser API for passkeys')], ['challenge', L('Byte casuali contro le ripetizioni', 'Random bytes against replays')], [L('15 caratteri', '15 characters'), L('Minimo NIST con la sola password', 'NIST minimum for password alone')], ['Blocklist', L('Password violate o comuni', 'Breached or common passwords')]], L('Accedere senza paura.', 'Signing in without fear.')),
            dFlash(pFlash(), [[L('Il NIST impone maiuscole e simboli obbligatori', 'NIST requires mandatory capitals and symbols'), false], [L('La chiave privata di una passkey viene inviata al server', 'A passkey’s private key is sent to the server'), false], [L('Dopo il login si rigenera l’identificativo di sessione', 'After sign-in the session identifier is regenerated'), true], [L('Bisogna permettere di incollare la password', 'Pasting the password should be allowed'), true]], L('Identità moderne.', 'Modern identities.')),
        ]),
        lesson('crypto-secrets', L('Crittografia, segreti e configurazione', 'Cryptography, secrets and configuration'), [
            P('Contro i **guasti crittografici**: HTTPS ovunque con TLS 1.2 o superiore (meglio 1.3) e HSTS, dati sensibili cifrati anche sul disco, token generati con un generatore **crittografico** e mai con `Math.random()`. Usa librerie collaudate, mai algoritmi inventati da te, e niente MD5 o SHA-1 per la sicurezza.', 'Against **cryptographic failures**: HTTPS everywhere with TLS 1.2 or higher (preferably 1.3) and HSTS, sensitive data encrypted at rest too, tokens generated with a **cryptographic** generator and never with `Math.random()`. Use proven libraries, never home-made algorithms, and no MD5 or SHA-1 for security.'),
            C('// Node.js
import { randomBytes } from "node:crypto";
const token = randomBytes(32).toString("base64url");   // token di reset sicuro

// PHP
$token = bin2hex(random_bytes(32));

# Python
import secrets
token = secrets.token_urlsafe(32)', 'text'),
            P('I **segreti** (chiavi API, password del database) vanno in variabili d’ambiente o in un gestore di segreti, mai nel repository; attiva la scansione dei segreti e ruotali se escono. La **configurazione sbagliata** è al secondo posto della Top 10 2025: password di default, modalità debug attiva in produzione, elenchi di cartelle visibili, errori troppo dettagliati, pannelli di amministrazione aperti a tutti.', '**Secrets** (API keys, database passwords) go in environment variables or a secrets manager, never in the repository; turn on secret scanning and rotate them if they leak. **Security misconfiguration** is second in the 2025 Top 10: default passwords, debug mode on in production, visible directory listings, overly detailed errors, admin panels open to everyone.'),
        ], [
            Q('cs1', L('Come generi un token per reimpostare la password?', 'How do you generate a password-reset token?'), [L('Con `Math.random()` e la data di oggi', 'With `Math.random()` and today’s date'), L('Con un generatore crittografico come `crypto.randomBytes`', 'With a cryptographic generator such as `crypto.randomBytes`'), L('Con un hash MD5 dell’email', 'With an MD5 hash of the email'), L('Con un contatore che cresce di uno', 'With a counter that grows by one')], 1, L('Deve essere impossibile da indovinare.', 'It must be impossible to guess.')),
            Q('cs2', L('Quale di queste è una configurazione sbagliata?', 'Which of these is a security misconfiguration?'), [L('Usare query preparate', 'Using prepared statements'), L('Attivare HSTS', 'Turning on HSTS'), L('Cifrare i backup', 'Encrypting backups'), L('Lasciare attiva la modalità debug in produzione', 'Leaving debug mode on in production')], 3, L('Il debug mostra dettagli interni a chiunque.', 'Debug shows internal details to anyone.')),
            Q('cs3', L('Dove va la password del database di produzione?', 'Where does the production database password go?'), [L('In un gestore di segreti o in una variabile d’ambiente', 'In a secrets manager or an environment variable'), L('Nel codice, in una costante', 'In the code, in a constant'), L('In un commento del file di configurazione', 'In a comment in the configuration file'), L('Nel README del repository', 'In the repository’s README')], 0, L('Fuori dal codice e fuori da Git.', 'Out of the code and out of Git.')),
        ], task('php', L('Sostituisci il token prevedibile con `$token = bin2hex(random_bytes(32));`.', 'Replace the predictable token with `$token = bin2hex(random_bytes(32));`.'), "\$token = md5(time()); // prevedibile!", [re('\$token\s*=\s*bin2hex\s*\(\s*random_bytes\s*\(\s*32\s*\)\s*\)', L('Un token casuale e sicuro', 'A random, secure token'))], '', "\$token = bin2hex(random_bytes(32));"), [
            dMatch(pMatch(), [['TLS 1.3', L('Cifratura dei dati in viaggio', 'Encryption of data in transit')], ['random_bytes', L('Casualità crittografica', 'Cryptographic randomness')], ['Math.random', L('Non sicuro per i token', 'Not safe for tokens')], [L('Gestore di segreti', 'Secrets manager'), L('Custodisce chiavi e password', 'Keeps keys and passwords')], [L('Modalità debug', 'Debug mode'), L('Da spegnere in produzione', 'To switch off in production')]], L('Chiavi e impostazioni.', 'Keys and settings.')),
            dBug(pBug(), ['const crypto = require("node:crypto");', 'const token = Math.random().toString(36).slice(2);', 'salvaTokenReset(utente, token);'], 1, 'const token = crypto.randomBytes(32).toString("base64url");', L('`Math.random()` è prevedibile: per i token serve un generatore crittografico.', '`Math.random()` is predictable: tokens need a cryptographic generator.')),
        ]),
        lesson('cors-clickjacking', L('CORS e clickjacking', 'CORS and clickjacking'), [
            P('La **same-origin policy** impedisce al JavaScript di una pagina di leggere le risposte di un’altra origine. Il **CORS** permette a un server di allentarla per origini scelte: `Access-Control-Allow-Origin: https://app.esempio.it`. Elenca origini precise; usa `*` solo per dati pubblici senza credenziali (i browser non accettano `*` con le credenziali); non rimandare mai qualsiasi `Origin` insieme alle credenziali. E ricorda: il CORS non è un controllo degli accessi.', 'The **same-origin policy** stops a page’s JavaScript from reading responses from another origin. **CORS** lets a server relax it for chosen origins: `Access-Control-Allow-Origin: https://app.esempio.it`. List exact origins; use `*` only for public data without credentials (browsers do not accept `*` with credentials); never echo back any `Origin` together with credentials. And remember: CORS is not access control.'),
            C('# richiesta di controllo (preflight) inviata dal browser
OPTIONS /api/ordini
Origin: https://app.esempio.it
Access-Control-Request-Method: PATCH

204 No Content
Access-Control-Allow-Origin: https://app.esempio.it
Access-Control-Allow-Methods: GET, POST, PATCH
Access-Control-Allow-Credentials: true
Vary: Origin

# contro il clickjacking
Content-Security-Policy: frame-ancestors \'self\'
X-Frame-Options: SAMEORIGIN', 'http'),
            P('Con il **clickjacking** un sito malevolo carica il tuo in un iframe invisibile e fa cliccare agli utenti pulsanti che non vedono (“conferma il bonifico”). La difesa è dire al browser chi può incorniciare le tue pagine: `frame-ancestors \'self\'` (o `\'none\'`) nella CSP, più `X-Frame-Options` per i browser vecchi.', 'With **clickjacking** a malicious site loads yours in an invisible iframe and makes users click buttons they cannot see (“confirm the transfer”). The defence is telling the browser who may frame your pages: `frame-ancestors \'self\'` (or `\'none\'`) in the CSP, plus `X-Frame-Options` for old browsers.'),
            Fig(L(fig_ws_clickjack(['Pagina esca, visibile', 'Hai vinto un premio!', 'Ritira ora', 'iframe invisibile', 'Conferma bonifico', 'Il clic sembra su «Ritira ora»,', 'ma arriva a «Conferma bonifico».']), fig_ws_clickjack(['Bait page, visible', 'You have won a prize!', 'Claim now', 'Invisible iframe', 'Confirm transfer', 'The click seems to land on “Claim now”,', 'but it reaches “Confirm transfer”.'])),
                L('Una vista esplosa di due strati. Dietro, la pagina esca con la scritta «Hai vinto un premio!» e il pulsante «Ritira ora». Davanti, tratteggiato, un iframe invisibile con il sito della banca e il pulsante «Conferma bonifico», allineato sopra quello dell’esca; il puntatore clicca lì.', 'An exploded view of two layers. Behind, the bait page with “You have won a prize!” and the “Claim now” button. In front, dashed, an invisible iframe with the bank’s site and the “Confirm transfer” button, lined up over the bait’s button; the pointer clicks there.'),
                L('Con `frame-ancestors` il tuo sito si rifiuta di stare nell’iframe di altri.', 'With `frame-ancestors` your site refuses to sit in someone else’s iframe.')),
        ], [
            Q('cc1', L('Che cosa fa il CORS?', 'What does CORS do?'), [L('Permette ai browser di leggere risposte da origini scelte dal server', 'Lets browsers read responses from origins chosen by the server'), L('Blocca le richieste fatte con curl', 'Blocks requests made with curl'), L('Cifra le risposte del server', 'Encrypts the server’s responses'), L('Sostituisce l’autenticazione', 'Replaces authentication')], 0, L('È un permesso per i browser, non una barriera.', 'It is a permission for browsers, not a barrier.')),
            Q('cc2', L('Quale configurazione è pericolosa?', 'Which configuration is dangerous?'), [L('Allow-Origin con un elenco preciso di origini', 'Allow-Origin with an exact list of origins'), L('Allow-Origin: * su dati pubblici senza credenziali', 'Allow-Origin: * on public data without credentials'), L('Rimandare qualsiasi Origin insieme ad Allow-Credentials: true', 'Echoing any Origin together with Allow-Credentials: true'), L('Aggiungere Vary: Origin alla risposta', 'Adding Vary: Origin to the response')], 2, L('Qualsiasi sito leggerebbe i dati dei tuoi utenti.', 'Any site could read your users’ data.')),
            Q('cc3', L('Quale intestazione blocca il clickjacking?', 'Which header blocks clickjacking?'), ['Strict-Transport-Security', 'Content-Security-Policy: frame-ancestors \'none\'', 'X-Content-Type-Options: nosniff', 'Referrer-Policy: no-referrer'], 1, L('Nessuno può mettere la pagina in un iframe.', 'Nobody can put the page in an iframe.')),
        ], task('http', L('Scrivi le intestazioni che permettono solo a `https://shop.esempio.it` di leggere le risposte con le credenziali, e che vietano a chiunque di incorniciare la pagina con `frame-ancestors \'none\'`.', 'Write the headers that let only `https://shop.esempio.it` read responses with credentials, and that forbid anyone from framing the page with `frame-ancestors \'none\'`.'), '', [re('^Access-Control-Allow-Origin:\s*https://shop\.esempio\.it\s*$', L('Un’origine precisa', 'An exact origin')), re('^Access-Control-Allow-Credentials:\s*true\s*$', L('Con le credenziali', 'With credentials')), re('^Content-Security-Policy:[^\n]*frame-ancestors\s+\'none\'', L('Niente iframe', 'No iframes'))], '', "Access-Control-Allow-Origin: https://shop.esempio.it\nAccess-Control-Allow-Credentials: true\nContent-Security-Policy: frame-ancestors 'none'"), [
            dMatch(pMatch(), [['Same-origin policy', L('Il JS non legge altre origini', 'JS cannot read other origins')], ['Access-Control-Allow-Origin', L('Chi può leggere le risposte', 'Who may read the responses')], ['Preflight', L('Richiesta OPTIONS di controllo', 'Checking OPTIONS request')], ['frame-ancestors', L('Chi può incorniciare la pagina', 'Who may frame the page')], ['Clickjacking', L('Clic rubati in un iframe', 'Clicks stolen in an iframe')]], L('Origini e cornici.', 'Origins and frames.')),
            dFlash(pFlash(), [[L('Il CORS protegge l’API dagli script che girano su un server', 'CORS protects the API from scripts running on a server'), false], [L('I browser accettano * insieme alle credenziali', 'Browsers accept * together with credentials'), false], [L('frame-ancestors \'none\' impedisce di incorniciare la pagina', 'frame-ancestors \'none\' prevents framing the page'), true], [L('Il preflight usa il metodo OPTIONS', 'The preflight uses the OPTIONS method'), true]], L('Confini del browser.', 'Browser boundaries.')),
        ]),
    ],
];
