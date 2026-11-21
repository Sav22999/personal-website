<?php
/** Testing: why test and the pyramid, unit tests, mocks and doubles, integration and end-to-end, TDD and CI, coverage, async code and fake time, network mocks, UI tests. */

/** The test pyramid: many unit tests at the base, some integration tests, a few end-to-end at the top; speed and realism pull in opposite directions. */
if (!function_exists('fig_test_pyramid')) {
    function fig_test_pyramid(string $l): string
    {
        $t = $l === 'it' ? ['unitari', 'integrazione', 'pochi', 'alcuni', 'tanti', 'più lenti|e realistici', 'più veloci|e precisi'] : ['unit', 'integration', 'few', 'some', 'many', 'slower and|more realistic', 'faster and|more precise'];
        foreach ([5, 6] as $i) { [$a, $b] = explode('|', $t[$i]); $t[$i] = '<tspan x="516">' . $a . '</tspan><tspan x="516" dy="17">' . $b . '</tspan>'; }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="tpya" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<path d="M240 16L313 92H167Z" fill="#FFF3EA" stroke="#E8590C" stroke-width="2" stroke-linejoin="round"/>
<path d="M167 98H313L381 162H99Z" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2" stroke-linejoin="round"/>
<path d="M93 168H387L460 244H20Z" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2" stroke-linejoin="round"/>
<g text-anchor="middle" font-weight="700"><text x="240" y="76" font-size="13" fill="#B84508">end-to-end</text><text x="240" y="136" fill="#1B66BC">{$t[1]}</text><text x="240" y="214" fill="#0B7F5C">{$t[0]}</text></g>
<g font-size="13" fill="#5B5880" text-anchor="end"><text x="150" y="64">{$t[2]}</text><text x="86" y="136">{$t[3]}</text><text x="40" y="200">{$t[4]}</text></g>
<path d="M500 230V28" stroke="#8A87B0" stroke-width="2" fill="none" marker-end="url(#tpya)" marker-start="url(#tpya)"/>
<g font-size="13" fill="#5B5880"><text y="44">{$t[5]}</text><text y="214">{$t[6]}</text></g>
</svg>
SVG;
    }
}

/** Arrange, Act, Assert: three steps of a unit test, each with its line of code. */
if (!function_exists('fig_test_aaa')) {
    function fig_test_aaa(string $l): string
    {
        $t = $l === 'it' ? ['prepara i dati', 'esegui la funzione', 'verifica il risultato'] : ['prepare the data', 'run the function', 'check the result'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 180" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="taaa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="30" width="186" height="120" rx="14" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<rect x="227" y="30" width="186" height="120" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="442" y="30" width="186" height="120" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<g font-weight="700" font-size="16"><text x="28" y="60" fill="#5A35C8">1 · Arrange</text><text x="243" y="60" fill="#1B66BC">2 · Act</text><text x="458" y="60" fill="#0B7F5C">3 · Assert</text></g>
<g font-size="13" fill="#5B5880"><text x="28" y="82">{$t[0]}</text><text x="243" y="82">{$t[1]}</text><text x="458" y="82">{$t[2]}</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12"><text x="28" y="120">righe = [ … ]</text><text x="243" y="120">totale(righe)</text><text x="458" y="120">expect(r).toBe(25)</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#taaa)"><path d="M198 90H223"/><path d="M413 90H438"/></g>
</svg>
SVG;
    }
}

/** A mock in place of the real email service: the code under test calls the fake, which answers at once and records the call for the test to check. */
if (!function_exists('fig_test_mock')) {
    function fig_test_mock(string $l): string
    {
        $t = $l === 'it' ? ['il test', 'codice da provare', 'server email vero', 'non chiamato', 'mock', 'risponde true e ricorda la chiamata', 'controlla con chi è stato chiamato'] : ['the test', 'code under test', 'real email server', 'not called', 'mock', 'answers true and records the call', 'checks who it was called with'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="tmka" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="82" width="140" height="66" rx="12" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/>
<rect x="196" y="82" width="170" height="66" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="420" y="16" width="208" height="62" rx="12" fill="#FFFFFF" stroke="#C9C7DD" stroke-width="2" stroke-dasharray="6 4"/>
<rect x="420" y="128" width="208" height="70" rx="12" fill="#ECE6FF" stroke="#7048E8" stroke-width="2"/>
<g font-size="13" font-weight="700"><text x="28" y="108">{$t[0]}</text><text x="212" y="108" fill="#1B66BC">{$t[1]}</text><text x="436" y="40" fill="#8A87B0">{$t[2]}</text><text x="436" y="152" fill="#5A35C8">{$t[4]} · <tspan font-family="ui-monospace, Menlo, monospace" font-weight="400">vi.fn()</tspan></text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12"><text x="28" y="132">it("…")</text><text x="212" y="132">registra(…)</text></g>
<g font-size="12" fill="#5B5880"><text x="436" y="62">✗ {$t[3]}</text><text x="436" y="176">{$t[5]}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#tmka)"><path d="M152 115H192"/><path d="M366 126C392 126 396 160 416 160"/></g>
<path d="M366 104C392 104 396 50 416 50" stroke="#C9C7DD" stroke-width="2" stroke-dasharray="5 4" fill="none"/>
<path d="M82 148V218H520V202" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="5 4" fill="none" marker-end="url(#tmka)"/>
<text x="96" y="210" font-size="12" font-weight="700" fill="#0B7F5C">{$t[6]}</text>
</svg>
SVG;
    }
}

/** The TDD cycle: red, green, refactor, going round; a highlight walks the three steps. */
if (!function_exists('fig_test_tdd')) {
    function fig_test_tdd(string $l): string
    {
        $t = $l === 'it' ? ['1 · rosso', 'scrivi un test che fallisce', '2 · verde', 'il codice minimo per farlo passare', '3 · refactor', 'migliora il codice, i test restano verdi'] : ['1 · red', 'write a test that fails', '2 · green', 'the least code to make it pass', '3 · refactor', 'improve the code, tests stay green'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 240" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<style>.ttd{opacity:0;animation:ttd 6s infinite}.ttd2{animation-delay:2s}.ttd3{animation-delay:4s}@keyframes ttd{0%{opacity:0}4%{opacity:1}30%{opacity:1}34%{opacity:0}100%{opacity:0}}@media (prefers-reduced-motion:reduce){.ttd{animation:none}}</style>
<defs><marker id="ttda" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="20" y="24" width="240" height="70" rx="14" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/>
<rect x="380" y="24" width="240" height="70" rx="14" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<rect x="190" y="152" width="260" height="70" rx="14" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<g fill="none" stroke="#7048E8" stroke-width="4"><rect class="ttd" x="14" y="18" width="252" height="82" rx="17"/><rect class="ttd ttd2" x="374" y="18" width="252" height="82" rx="17"/><rect class="ttd ttd3" x="184" y="146" width="272" height="82" rx="17"/></g>
<g font-weight="700"><text x="36" y="52" fill="#B0245A">{$t[0]}</text><text x="396" y="52" fill="#0B7F5C">{$t[2]}</text><text x="206" y="180" fill="#1B66BC">{$t[4]}</text></g>
<g font-size="13"><text x="36" y="76">{$t[1]}</text><text x="396" y="76">{$t[3]}</text><text x="206" y="204">{$t[5]}</text></g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ttda)"><path d="M260 59H376"/><path d="M500 94C500 140 470 170 454 180"/><path d="M186 186C140 176 140 130 140 98"/></g>
</svg>
SVG;
    }
}

/** MSW between the code and the network: the request is caught and answered with fake data, the real server is never reached. */
if (!function_exists('fig_test_msw')) {
    function fig_test_msw(string $l): string
    {
        $t = $l === 'it' ? ['il tuo codice', 'intercetta', 'server vero', 'non raggiunto', 'risposta finta', 'o un errore 500'] : ['your code', 'intercepts', 'real server', 'not reached', 'fake response', 'or a 500 error'];
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 210" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="tmsa" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" fill="#1E1C52"/></marker></defs>
<rect x="12" y="60" width="170" height="84" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="256" y="60" width="170" height="84" rx="12" fill="#FFF3EA" stroke="#E8590C" stroke-width="2"/>
<rect x="490" y="60" width="138" height="84" rx="12" fill="#FFFFFF" stroke="#C9C7DD" stroke-width="2" stroke-dasharray="6 4"/>
<g font-size="13" font-weight="700"><text x="28" y="86" fill="#1B66BC">{$t[0]}</text><text x="272" y="86" fill="#B84508">MSW · {$t[1]}</text><text x="506" y="86" fill="#8A87B0">{$t[2]}</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="12"><text x="28" y="116">caricaCorsi()</text><text x="272" y="116">http.get(…)</text></g>
<text x="506" y="116" font-size="12" fill="#8A87B0">✗ {$t[3]}</text>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#tmsa)"><path d="M182 84H252"/><path d="M256 122H186"/></g>
<path d="M426 102H486" stroke="#C9C7DD" stroke-width="2" stroke-dasharray="5 4"/>
<text x="217" y="48" text-anchor="middle" font-family="ui-monospace, Menlo, monospace" font-size="12">GET /corsi</text>
<text x="217" y="166" text-anchor="middle" font-size="12" font-weight="700" fill="#0B7F5C">{$t[4]}</text>
<text x="217" y="184" text-anchor="middle" font-size="12" fill="#5B5880">{$t[5]}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'testing', 'family' => 'testing', 'after' => 'js-base',
    'title' => L('Testing del software', 'Software Testing'), 'short' => L('Testing', 'Testing'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Scrivere codice di cui fidarsi: piramide dei test, test unitari, mock e rete simulata, codice asincrono, interfacce, integrazione ed end-to-end, TDD e integrazione continua.', 'Writing code you can trust: the test pyramid, unit tests, mocks and fake networks, async code, interfaces, integration and end-to-end tests, TDD and continuous integration.'),
    'lessons' => [
        lesson('why', L('Perché testare', 'Why test'), [
            P('Un **test automatico** è codice che controlla altro codice: lo esegui in un secondo, quante volte vuoi. I test trovano errori prima degli utenti, permettono di **modificare senza paura** (se rompi qualcosa lo scopri subito) e documentano come il codice dovrebbe comportarsi.', 'An **automated test** is code that checks other code: you run it in a second, as often as you like. Tests find bugs before users do, let you **change code without fear** (if you break something you know at once) and document how the code should behave.'),
            C('          /\\        end-to-end: pochi, lenti, simulano l’utente
         /  \\
        /----\\      integrazione: più parti insieme (API, database)
       /      \\
      /--------\\    unitari: tanti, velocissimi, una funzione alla volta', 'text'),
            P('La **piramide dei test** suggerisce tanti test unitari alla base, alcuni di integrazione e pochi end-to-end in cima: i primi sono veloci e precisi nel dire cosa si è rotto, gli ultimi più realistici ma lenti e fragili.', 'The **test pyramid** suggests many unit tests at the base, some integration tests and a few end-to-end ones at the top: the former are fast and precise about what broke, the latter more realistic but slow and brittle.'),
            Fig(L(fig_test_pyramid('it'), fig_test_pyramid('en')),
                L('Una piramide in tre fasce: alla base, larga, i test unitari, tanti; al centro i test di integrazione, alcuni; in cima, stretta, gli end-to-end, pochi. Una freccia a destra indica che salendo i test diventano più lenti e realistici, scendendo più veloci e precisi.', 'A pyramid in three bands: at the wide base the unit tests, many; in the middle the integration tests, some; at the narrow top the end-to-end tests, few. An arrow on the right shows that going up tests become slower and more realistic, going down faster and more precise.'),
                L('La base larga regge tutto: tanti test veloci, pochi test lenti.', 'The wide base holds everything up: many fast tests, few slow ones.')),
        ], [
            Q('w1', L('Quali test dovrebbero essere i più numerosi?', 'Which tests should be the most numerous?'), [L('Unitari', 'Unit'), L('End-to-end', 'End-to-end'), L('Manuali', 'Manual'), L('Nessuno', 'None')], 0, L('La base della piramide.', 'The pyramid’s base.')),
            Q('w2', L('Che vantaggio danno i test quando modifichi il codice?', 'What do tests give you when changing code?'), [L('Scopri subito se hai rotto qualcosa', 'You find out at once if you broke something'), L('Il codice diventa più lungo', 'Code gets longer'), L('Niente', 'Nothing'), L('Rallentano tutto', 'They slow everything')], 0, L('Modificare senza paura.', 'Change without fear.')),
            Q('w3', L('Perché i test end-to-end sono pochi?', 'Why are end-to-end tests few?'), [L('Sono lenti e fragili', 'They are slow and brittle'), L('Sono inutili', 'They are useless'), L('Sono vietati', 'They are forbidden'), L('Sono troppo veloci', 'They are too fast')], 0, L('Ma molto realistici.', 'But very realistic.')),
        ], task('text', L('Classifica ogni test, uno per riga come `test: tipo`: `controlla la funzione calcolaIva`, `apre il browser e completa un acquisto`, `chiama l’API e verifica il database`. Tipi: unitario, end-to-end, integrazione.', 'Classify each test, one per line as `test: type`: `checks the calcolaIva function`, `opens the browser and completes a purchase`, `calls the API and checks the database`. Types: unit, end-to-end, integration.'), '', [re('calcolaIva[^:\n]*:\s*(unitario|unit)', L('Funzione = unitario', 'Function = unit')), re('(browser)[^:\n]*:\s*end-to-end', L('Browser = end-to-end', 'Browser = end-to-end')), re('(API)[^:\n]*:\s*(integrazione|integration)', L('API + database = integrazione', 'API + database = integration'))], '', "controlla la funzione calcolaIva: unitario\napre il browser e completa un acquisto: end-to-end\nchiama l’API e verifica il database: integrazione"), [
            dOrder(pSort(), [L('Unitari (tanti)', 'Unit (many)'), L('Integrazione (alcuni)', 'Integration (some)'), L('End-to-end (pochi)', 'End-to-end (few)')], L('Dalla base alla cima della piramide.', 'From the base to the top of the pyramid.')),
            dFlash(pFlash(), [[L('Un test automatico si può rieseguire quante volte vuoi', 'An automated test can be rerun as often as you like'), true], [L('I test end-to-end sono i più veloci', 'End-to-end tests are the fastest'), false], [L('I test documentano il comportamento atteso', 'Tests document expected behaviour'), true], [L('Con i test non serve mai rileggere il codice', 'With tests you never need to review code'), false]], L('Fiducia automatica.', 'Automated confidence.')),
        ]),
        lesson('unit', L('Test unitari', 'Unit tests'), [
            P('Un **test unitario** controlla una piccola unità (una funzione) da sola. Lo schema classico è **Arrange, Act, Assert**: prepara i dati, esegui, verifica il risultato. Ogni test controlla una cosa sola e ha un nome che dice cosa si aspetta.', 'A **unit test** checks a small unit (a function) on its own. The classic pattern is **Arrange, Act, Assert**: prepare the data, run, verify the result. Each test checks one thing and has a name saying what it expects.'),
            C('// carrello.test.js (Vitest o Jest)
import { describe, it, expect } from "vitest";
import { totale } from "./carrello.js";

describe("totale", () => {
  it("somma i prezzi per le quantità", () => {
    const righe = [{ prezzo: 10, qta: 2 }, { prezzo: 5, qta: 1 }];   // Arrange
    const risultato = totale(righe);                                // Act
    expect(risultato).toBe(25);                                     // Assert
  });

  it("restituisce 0 per un carrello vuoto", () => {
    expect(totale([])).toBe(0);
  });
});', 'js'),
            P('Testa anche i **casi limite**: lista vuota, valori negativi, zero, testi enormi, input sbagliati. Sono lì che si nascondono i bug. In Python lo stesso si fa con **pytest** (`def test_...(): assert ...`).', 'Also test **edge cases**: empty list, negative values, zero, huge text, wrong input. That is where bugs hide. In Python the same is done with **pytest** (`def test_...(): assert ...`).'),
            Fig(L(fig_test_aaa('it'), fig_test_aaa('en')),
                L('Tre riquadri in fila: 1, Arrange, prepara i dati con righe = [ … ]; 2, Act, esegue totale(righe); 3, Assert, verifica il risultato con expect(r).toBe(25).', 'Three boxes in a row: 1, Arrange, prepares the data with righe = [ … ]; 2, Act, runs totale(righe); 3, Assert, checks the result with expect(r).toBe(25).'),
                L('Ogni test racconta una piccola storia: dati, azione, verifica.', 'Every test tells a small story: data, action, check.')),
            Tip('Un test deve fallire quando il codice è sbagliato: prova a rompere apposta la funzione e guarda se diventa rosso. Un test che passa sempre non protegge niente.', 'A test must fail when the code is wrong: try breaking the function on purpose and see whether it turns red. A test that always passes protects nothing.'),
        ], [
            Q('u1', L('Che cosa significa Arrange, Act, Assert?', 'What does Arrange, Act, Assert mean?'), [L('Prepara, esegui, verifica', 'Prepare, run, verify'), L('Scrivi, cancella, ripeti', 'Write, delete, repeat'), L('Compila, avvia, chiudi', 'Compile, start, close'), L('Niente', 'Nothing')], 0, L('Lo schema di ogni test.', 'The pattern of every test.')),
            Q('u2', L('Quale di questi è un caso limite?', 'Which of these is an edge case?'), [L('Una lista vuota', 'An empty list'), L('Una lista di tre elementi normali', 'A list of three normal items'), L('Il nome del file', 'The file name'), L('Il colore del test', 'The test colour')], 0, L('Lì si nascondono i bug.', 'Bugs hide there.')),
            Q('u3', L('Quale libreria si usa spesso per i test in Python?', 'Which library is often used for tests in Python?'), ['pytest', 'pandas', 'Django', 'NumPy'], 0, L('Con semplici assert.', 'With simple asserts.')),
        ], task('js', L('Scrivi un test Vitest che verifica `expect(somma(2, 3)).toBe(5)` dentro `it("somma due numeri", ...)`.', 'Write a Vitest test verifying `expect(somma(2, 3)).toBe(5)` inside `it("somma due numeri", ...)`.'), "import { it, expect } from \"vitest\";\nimport { somma } from \"./mate.js\";\n", [re('it\s*\(\s*["\']somma due numeri["\']\s*,\s*\(\s*\)\s*=>', L('Il test', 'The test')), re('expect\s*\(\s*somma\s*\(\s*2\s*,\s*3\s*\)\s*\)\.toBe\s*\(\s*5\s*\)', L('L’asserzione', 'The assertion'))], '', "it(\"somma due numeri\", () => {\n  expect(somma(2, 3)).toBe(5);\n});"), [
            dMatch(pMatch(), [['describe', L('Raggruppa i test', 'Groups tests')], ['it', L('Un singolo test', 'A single test')], ['expect', L('Valore da verificare', 'Value to check')], ['toBe', L('Uguaglianza', 'Equality')], ['toThrow', L('Deve lanciare un errore', 'Must throw an error')]], L('Il vocabolario dei test.', 'Test vocabulary.')),
            dBug(pBug(), ['it("calcola lo sconto", () => {', '  const prezzo = sconta(100, 0.2);', '  console.log(prezzo);', '});'], 2, '  expect(prezzo).toBe(80);', L('Un test senza asserzione non verifica niente.', 'A test without an assertion verifies nothing.')),
        ]),
        lesson('mocks', L('Mock e test di integrazione', 'Mocks and integration tests'), [
            P('Se una funzione chiama un servizio esterno (un’API di pagamento, l’invio di email), nel test unitario la sostituisci con un **mock**: un finto oggetto che risponde come vuoi e registra come è stato chiamato. Così il test è veloce, ripetibile e non manda email vere.', 'If a function calls an external service (a payment API, sending email), in the unit test you replace it with a **mock**: a fake object that answers as you wish and records how it was called. That way the test is fast, repeatable and sends no real email.'),
            Fig(L(fig_test_mock('it'), fig_test_mock('en')),
                L('Il test chiama registra(…), il codice da provare. Questo non chiama il server email vero, tratteggiato e segnato come non chiamato, ma il mock vi.fn(), che risponde true e ricorda la chiamata. Una freccia verde dal test al mock indica che il test controlla con chi è stato chiamato.', 'The test calls registra(…), the code under test. This does not call the real email server, dashed and marked as not called, but the mock vi.fn(), which answers true and records the call. A green arrow from the test to the mock shows that the test checks who it was called with.'),
                L('Il mock prende il posto del servizio vero e fa da testimone.', 'The mock takes the place of the real service and acts as a witness.')),
            C('import { vi, it, expect } from "vitest";
import { registra } from "./utenti.js";

it("invia l’email di benvenuto", async () => {
  const email = { invia: vi.fn().mockResolvedValue(true) };
  await registra({ nome: "Ada", indirizzo: "ada@example.com" }, email);
  expect(email.invia).toHaveBeenCalledWith("ada@example.com", expect.stringContaining("Benvenuta"));
});', 'js'),
            P('I **test di integrazione** invece verificano che più parti funzionino **insieme**: per esempio un’API vera con un database di test (in un container). Non esagerare con i mock: se finti tutto, il test non dimostra che il sistema reale funziona.', '**Integration tests** instead verify that several parts work **together**: for example a real API with a test database (in a container). Do not overdo mocks: if you fake everything, the test does not prove the real system works.'),
        ], [
            Q('m1', L('A che cosa serve un mock?', 'What is a mock for?'), [L('Sostituire una dipendenza esterna nei test', 'Replacing an external dependency in tests'), L('Cancellare il database', 'Deleting the database'), L('Velocizzare la produzione', 'Speeding up production'), L('Scrivere documentazione', 'Writing documentation')], 0, L('Niente email vere nei test.', 'No real email in tests.')),
            Q('m2', L('Che cosa verifica un test di integrazione?', 'What does an integration test verify?'), [L('Che più parti funzionino insieme', 'That several parts work together'), L('Una sola funzione isolata', 'One isolated function'), L('Il colore della pagina', 'The page colour'), L('Niente', 'Nothing')], 0, L('API + database, per esempio.', 'API + database, for example.')),
            Q('m3', L('Che rischio c’è con troppi mock?', 'What is the risk with too many mocks?'), [L('Il test non dimostra che il sistema reale funziona', 'The test does not prove the real system works'), L('I test diventano troppo veri', 'Tests get too real'), L('Nessun rischio', 'No risk'), L('Il codice si cancella', 'The code gets deleted')], 0, L('Equilibrio.', 'Balance.')),
        ], task('js', L('Crea un mock `pagamento` con `invia: vi.fn().mockResolvedValue({ ok: true })` e verifica dopo la chiamata che `pagamento.invia` sia stato chiamato con `toHaveBeenCalled()`.', 'Create a mock `pagamento` with `invia: vi.fn().mockResolvedValue({ ok: true })` and after the call verify `pagamento.invia` was called with `toHaveBeenCalled()`.'), '', [re('pagamento\s*=\s*\{\s*invia\s*:\s*vi\.fn\s*\(\s*\)\.mockResolvedValue\s*\(\s*\{\s*ok\s*:\s*true\s*\}\s*\)\s*\}', L('Il mock', 'The mock')), re('expect\s*\(\s*pagamento\.invia\s*\)\.toHaveBeenCalled\s*\(\s*\)', L('La verifica', 'The check'))], '', "const pagamento = { invia: vi.fn().mockResolvedValue({ ok: true }) };\nawait paga(ordine, pagamento);\nexpect(pagamento.invia).toHaveBeenCalled();"), [
            dMatch(pMatch(), [['vi.fn()', L('Crea una funzione finta', 'Creates a fake function')], ['mockResolvedValue', L('Risposta finta asincrona', 'Fake async response')], ['toHaveBeenCalled', L('È stata chiamata?', 'Was it called?')], [L('Database di test', 'Test database'), L('Per l’integrazione', 'For integration')]], L('Finti e veri.', 'Fake and real.')),
            dFlash(pFlash(), [[L('Un mock può registrare come è stato chiamato', 'A mock can record how it was called'), true], [L('I test unitari devono mandare email vere', 'Unit tests must send real email'), false], [L('Un database di test si può avviare in un container', 'A test database can run in a container'), true], [L('Più mock significa sempre test migliori', 'More mocks always means better tests'), false]], L('Isolare con giudizio.', 'Isolate wisely.')),
        ]),
        lesson('e2e-ci', L('End-to-end, TDD e CI', 'End-to-end, TDD and CI'), [
            P('I test **end-to-end** usano un browser automatizzato (come **Playwright** o Cypress) per fare quello che farebbe un utente: aprire la pagina, cliccare, compilare moduli, controllare il risultato. Si scrivono per i percorsi più importanti: registrazione, acquisto, accesso.', '**End-to-end** tests use an automated browser (like **Playwright** or Cypress) to do what a user would: open the page, click, fill forms, check the result. You write them for the most important paths: sign-up, purchase, login.'),
            C('import { test, expect } from "@playwright/test";

test("un utente può iscriversi alla newsletter", async ({ page }) => {
  await page.goto("http://localhost:3000");
  await page.getByLabel("Email").fill("ada@example.com");
  await page.getByRole("button", { name: "Iscriviti" }).click();
  await expect(page.getByText("Grazie per l’iscrizione")).toBeVisible();
});', 'js'),
            P('Nel **TDD** (Test-Driven Development) scrivi prima il test che fallisce, poi il codice minimo per farlo passare, poi migliori il codice (rosso, verde, refactor). Con l’**integrazione continua** (CI, per esempio GitHub Actions) i test girano automaticamente a ogni push e pull request: niente merge se qualcosa è rosso.', 'In **TDD** (Test-Driven Development) you first write the failing test, then the minimal code to make it pass, then improve the code (red, green, refactor). With **continuous integration** (CI, for example GitHub Actions) tests run automatically on every push and pull request: no merge if anything is red.'),
            Fig(L(fig_test_tdd('it'), fig_test_tdd('en')),
                L('Un ciclo di tre riquadri: 1, rosso, scrivi un test che fallisce; 2, verde, il codice minimo per farlo passare; 3, refactor, migliora il codice mentre i test restano verdi; poi si torna al rosso. Un’evidenziazione passa da un passo all’altro.', 'A cycle of three boxes: 1, red, write a test that fails; 2, green, the least code to make it pass; 3, refactor, improve the code while the tests stay green; then back to red. A highlight moves from one step to the next.'),
                L('Rosso, verde, refactor: piccoli passi, sempre con una rete sotto.', 'Red, green, refactor: small steps, always with a safety net.')),
        ], [
            Q('e1', L('Che cosa fa Playwright?', 'What does Playwright do?'), [L('Automatizza un browser per i test end-to-end', 'Automates a browser for end-to-end tests'), L('Gestisce database', 'Manages databases'), L('Compila C++', 'Compiles C++'), L('Disegna grafici', 'Draws charts')], 0, L('Simula l’utente.', 'It simulates the user.')),
            Q('e2', L('Qual è l’ordine del TDD?', 'What is the TDD order?'), [L('Rosso, verde, refactor', 'Red, green, refactor'), L('Verde, rosso, cancella', 'Green, red, delete'), L('Codice, poi test un anno dopo', 'Code, then tests a year later'), L('Refactor, rosso, verde', 'Refactor, red, green')], 0, L('Prima il test che fallisce.', 'Failing test first.')),
            Q('e3', L('Che cosa fa la CI con i test?', 'What does CI do with tests?'), [L('Li esegue a ogni push e pull request', 'Runs them on every push and pull request'), L('Li cancella', 'Deletes them'), L('Li scrive da sola', 'Writes them by itself'), L('Niente', 'Nothing')], 0, L('Niente merge se rosso.', 'No merge if red.')),
        ], task('yaml', L('Scrivi un workflow GitHub Actions che, a ogni `push`, su `ubuntu-latest` esegue `npm ci` e poi `npm test`.', 'Write a GitHub Actions workflow that, on every `push`, on `ubuntu-latest` runs `npm ci` and then `npm test`.'), "name: Test\n", [re('^on:\s*(\[\s*push|push|\n\s+push)', L('Si avvia al push', 'It triggers on push')), re('runs-on:\s*ubuntu-latest', L('La macchina', 'The machine')), re('-\s*run:\s*npm ci[\s\S]*-\s*run:\s*npm test', L('Installa e testa', 'It installs and tests'))], '', "name: Test\non: [push]\njobs:\n  test:\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@v6\n      - uses: actions/setup-node@v6\n        with: { node-version: lts/* }\n      - run: npm ci\n      - run: npm test"), [
            dOrder(pSort(), [L('Scrivi un test che fallisce (rosso)', 'Write a failing test (red)'), L('Scrivi il codice minimo per farlo passare (verde)', 'Write minimal code to pass it (green)'), L('Migliora il codice tenendo i test verdi (refactor)', 'Improve the code keeping tests green (refactor)')], L('Il ciclo del TDD.', 'The TDD cycle.')),
            dMatch(pMatch(), [['Playwright', L('Browser automatizzato', 'Automated browser')], ['TDD', L('Prima il test', 'Test first')], ['CI', L('Test automatici a ogni push', 'Automatic tests on every push')], ['GitHub Actions', L('Servizio di CI', 'CI service')], ['getByRole', L('Trova un elemento come un utente', 'Finds an element like a user')]], L('Qualità automatica.', 'Automated quality.')),
        ]),
        lesson('quality', L('Copertura, casi limite e test fragili', 'Coverage, edge cases and flaky tests'), [
            P('La **copertura** (coverage) dice quali righe sono state eseguite dai test, non se sono state **verificate**: l’80% di copertura con controlli deboli vale poco. Meglio cercare i **casi limite**: lista vuota, zero, numeri negativi, testo con accenti, date a fine mese.', '**Coverage** tells which lines the tests ran, not whether they were **checked**: 80% coverage with weak assertions is worth little. Better to look for **edge cases**: empty list, zero, negative numbers, accented text, month-end dates.'),
            C('test.each([
  [[], 0],
  [[5], 5],
  [[2, -3], -1],
  [[0.1, 0.2], 0.3],
])("somma(%j) = %d", (lista, atteso) => {
  expect(somma(lista)).toBeCloseTo(atteso);
});', 'js'),
            P('Un test **fragile** (flaky) a volte passa e a volte no: dipende dall’ora, dalla rete, da un ordine casuale o da un `sleep`. Si cura fissando l’orologio, simulando la rete e rendendo i test **indipendenti** tra loro.', 'A **flaky** test sometimes passes and sometimes fails: it depends on the time, the network, a random order or a `sleep`. It is cured by fixing the clock, faking the network and making tests **independent** of each other.'),
        ], [
            Q('qa1', L('Che cosa misura la copertura?', 'What does coverage measure?'), [L('Le righe eseguite dai test', 'The lines run by the tests'), L('I bug trovati', 'Bugs found'), L('La velocità', 'Speed'), L('La qualità dei controlli', 'The quality of assertions')], 0, L('Eseguito non vuol dire verificato.', 'Run does not mean checked.')),
            Q('qa2', L('Quale è un caso limite?', 'Which one is an edge case?'), [L('Una lista vuota', 'An empty list'), L('Una lista di tre numeri normali', 'A list of three normal numbers'), L('Il nome del file', 'The file name'), L('Il colore del report', 'The report colour')], 0, L('I bug si nascondono ai bordi.', 'Bugs hide at the edges.')),
            Q('qa3', L('Come si cura un test fragile legato all’ora?', 'How do you cure a flaky test tied to the time?'), [L('Fissando l’orologio nel test', 'By fixing the clock in the test'), L('Rilanciandolo finché passa', 'Rerunning it until it passes'), L('Aggiungendo uno sleep', 'Adding a sleep'), L('Cancellandolo', 'Deleting it')], 0, L('Il test deve essere ripetibile.', 'The test must be repeatable.')),
        ], task('js', L('Scrivi un `test.each` con almeno i casi `[[], 0]` e `[[5], 5]` che verifica `somma(lista)` con `toBe(atteso)`.', 'Write a `test.each` with at least the cases `[[], 0]` and `[[5], 5]` checking `somma(lista)` with `toBe(atteso)`.'), '', [re('test\.each\(\s*\[', L('Usi test.each', 'You use test.each')), re('\[\s*\[\s*\]\s*,\s*0\s*\]', L('C’è la lista vuota', 'The empty list is there')), re('expect\(\s*somma\(\s*lista\s*\)\s*\)\.toBe\(\s*atteso\s*\)', L('Verifichi il risultato', 'You check the result'))], '', "test.each([\n  [[], 0],\n  [[5], 5],\n])(\"somma\", (lista, atteso) => {\n  expect(somma(lista)).toBe(atteso);\n});"), [
            dMatch(pMatch(), [[L('Copertura', 'Coverage'), L('Righe eseguite', 'Lines run')], [L('Caso limite', 'Edge case'), L('Lista vuota, zero', 'Empty list, zero')], [L('Test fragile', 'Flaky test'), L('Passa a caso', 'Passes at random')], ['test.each', L('Molti casi in un test', 'Many cases in one test')]], L('Test di qualità.', 'Quality tests.')),
            dFlash(pFlash(), [[L('100% di copertura non garantisce zero bug', '100% coverage does not guarantee zero bugs'), true], [L('Un sleep rende un test più stabile', 'A sleep makes a test more stable'), false], [L('I test dovrebbero essere indipendenti tra loro', 'Tests should be independent of each other'), true], [L('Un test fragile si sistema rilanciandolo', 'A flaky test is fixed by rerunning it'), false]], L('Fiducia nei test.', 'Trust in tests.')),
        ]),
        lesson('async-time', L('Codice asincrono e tempo finto', 'Async code and fake time'), [
            P('Per testare una funzione asincrona rendi il test `async` e usa `await`, oppure le forme `resolves` e `rejects` di `expect`. Se dimentichi `await`, il test finisce prima della promessa e può passare anche quando il codice è sbagliato.', 'To test an async function make the test `async` and use `await`, or the `resolves` and `rejects` forms of `expect`. If you forget `await`, the test ends before the promise and can pass even when the code is wrong.'),
            C('import { it, expect, vi, beforeEach, afterEach } from "vitest";

it("carica il profilo", async () => {
  await expect(caricaProfilo(1)).resolves.toMatchObject({ id: 1 });
});

it("rifiuta un id negativo", async () => {
  await expect(caricaProfilo(-1)).rejects.toThrow("id non valido");
});

beforeEach(() => vi.useFakeTimers());
afterEach(() => vi.useRealTimers());

it("salva la bozza dopo 2 secondi", () => {
  const salva = vi.fn();
  avviaAutosalvataggio(salva);
  vi.advanceTimersByTime(2000);
  expect(salva).toHaveBeenCalledOnce();
});', 'js'),
            P('I **timer finti** sostituiscono `setTimeout` e `setInterval`: fai avanzare il tempo con `vi.advanceTimersByTime` invece di aspettarlo davvero. `vi.setSystemTime(new Date("2026-12-31"))` fissa anche la data, così un test su «fine anno» passa tutti i giorni.', '**Fake timers** replace `setTimeout` and `setInterval`: you move time forward with `vi.advanceTimersByTime` instead of really waiting. `vi.setSystemTime(new Date("2026-12-31"))` also fixes the date, so a test about “end of year” passes every day.'),
        ], [
            Q('at1', L('Che succede se dimentichi `await` su `expect(…).rejects`?', 'What happens if you forget `await` on `expect(…).rejects`?'), [L('Il test diventa più veloce e preciso', 'The test gets faster and more precise'), L('Vitest aggiunge `await` da solo', 'Vitest adds `await` by itself'), L('Il test può passare anche se il codice è sbagliato', 'The test can pass even if the code is wrong'), L('Il test viene saltato', 'The test is skipped')], 2, L('Il controllo arriva dopo la fine del test.', 'The check arrives after the test has ended.')),
            Q('at2', L('A che cosa serve `vi.advanceTimersByTime(2000)`?', 'What is `vi.advanceTimersByTime(2000)` for?'), [L('Aspetta davvero 2 secondi', 'Really waits 2 seconds'), L('Fa avanzare il tempo finto di 2 secondi', 'Moves fake time forward by 2 seconds'), L('Rallenta il test', 'Slows the test down'), L('Cambia il fuso orario', 'Changes the time zone')], 1, L('I timer scattano subito, senza attesa reale.', 'Timers fire at once, with no real wait.')),
            Q('at3', L('Come rendi ripetibile un test che dipende dalla data di oggi?', 'How do you make a test that depends on today’s date repeatable?'), [L('Eseguendolo sempre alla stessa ora', 'By always running it at the same time'), L('Con uno `sleep`', 'With a `sleep`'), L('Togliendo il test', 'By removing the test'), L('Con `vi.setSystemTime(…)`', 'With `vi.setSystemTime(…)`')], 3, L('La data finta è la stessa a ogni esecuzione.', 'The fake date is the same on every run.')),
        ], task('js', L('Scrivi un test `async` che verifica `await expect(dividi(1, 0)).rejects.toThrow()`.', 'Write an `async` test that checks `await expect(dividi(1, 0)).rejects.toThrow()`.'), 'import { it, expect } from "vitest";
import { dividi } from "./mate.js";
', [re('it\s*\(\s*["\'][^"\']+["\']\s*,\s*async\s*\(\s*\)\s*=>', L('Il test è asincrono', 'The test is async')), re('await\s+expect\s*\(\s*dividi\s*\(\s*1\s*,\s*0\s*\)\s*\)\.rejects\.toThrow\s*\(', L('Aspetti il rifiuto', 'You await the rejection'))], '', 'it("rifiuta la divisione per zero", async () => {
  await expect(dividi(1, 0)).rejects.toThrow();
});'), [
            dBug(pBug(), ['it("carica i dati", () => {', '  expect(carica()).resolves.toEqual([1, 2]);', '});'], 1, '  return expect(carica()).resolves.toEqual([1, 2]);', L('Senza `await` o `return` il test non aspetta la promessa.', 'Without `await` or `return` the test does not wait for the promise.')),
            dMatch(pMatch(), [['resolves', L('La promessa riesce con…', 'The promise succeeds with…')], ['rejects', L('La promessa fallisce', 'The promise fails')], ['vi.useFakeTimers()', L('Timer finti', 'Fake timers')], ['vi.advanceTimersByTime()', L('Fa avanzare il tempo', 'Moves time forward')], ['vi.setSystemTime()', L('Fissa la data', 'Fixes the date')]], L('Promesse e tempo sotto controllo.', 'Promises and time under control.')),
        ]),
        lesson('network-mocks', L('Simulare la rete con MSW', 'Mocking the network with MSW'), [
            P('Invece di sostituire `fetch` a mano, puoi intercettare le richieste di rete con **MSW** (Mock Service Worker): descrivi come risponde ogni indirizzo, e il tuo codice fa richieste vere che ricevono risposte finte. Funziona nei test con Node e anche nel browser.', 'Instead of replacing `fetch` by hand, you can intercept network requests with **MSW** (Mock Service Worker): you describe how each address answers, and your code makes real requests that get fake responses. It works in Node tests and in the browser too.'),
            Fig(L(fig_test_msw('it'), fig_test_msw('en')),
                L('Il tuo codice, caricaCorsi(), fa una richiesta GET /corsi. MSW la intercetta con http.get(…) e restituisce una risposta finta o un errore 500; il server vero, tratteggiato, non viene raggiunto.', 'Your code, caricaCorsi(), makes a GET /corsi request. MSW intercepts it with http.get(…) and returns a fake response or a 500 error; the real server, dashed, is never reached.'),
                L('La richiesta parte davvero, ma la risposta la decide il test.', 'The request really goes out, but the test decides the answer.')),
            C('import { http, HttpResponse } from "msw";
import { setupServer } from "msw/node";
import { beforeAll, afterEach, afterAll, it, expect } from "vitest";

const server = setupServer(
  http.get("https://api.esempio.it/corsi", () =>
    HttpResponse.json([{ id: 1, titolo: "Testing" }]),
  ),
);

beforeAll(() => server.listen());
afterEach(() => server.resetHandlers());
afterAll(() => server.close());

it("segnala un errore se il server cade", async () => {
  server.use(http.get("https://api.esempio.it/corsi", () => new HttpResponse(null, { status: 500 })));
  await expect(caricaCorsi()).rejects.toThrow();
});', 'js'),
            P('Così provi anche i casi difficili: errori 500, risposte lente, dati vuoti. `server.use` cambia la risposta solo per un test e `resetHandlers` la ripristina dopo. Il vantaggio rispetto a un mock di `fetch`: se cambi libreria HTTP, i test restano validi.', 'This way you also try the hard cases: 500 errors, slow responses, empty data. `server.use` changes the response for one test only and `resetHandlers` restores it afterwards. The advantage over a `fetch` mock: if you change HTTP library, the tests stay valid.'),
        ], [
            Q('nm1', L('Che cosa fa MSW?', 'What does MSW do?'), [L('Velocizza il server vero', 'Speeds up the real server'), L('Scrive i test da solo', 'Writes the tests by itself'), L('Sostituisce il database', 'Replaces the database'), L('Intercetta le richieste di rete e dà risposte finte', 'Intercepts network requests and gives fake responses')], 3, L('Il codice non sa di parlare con un finto server.', 'The code does not know it is talking to a fake server.')),
            Q('nm2', L('A che cosa serve `server.resetHandlers()` dopo ogni test?', 'What is `server.resetHandlers()` after each test for?'), [L('Spegne il server', 'Shuts down the server'), L('Toglie le risposte cambiate con `server.use`', 'Removes the responses changed with `server.use`'), L('Cancella i test', 'Deletes the tests'), L('Svuota la cache del browser', 'Clears the browser cache')], 1, L('Ogni test riparte dalle risposte di base.', 'Each test restarts from the base responses.')),
            Q('nm3', L('Che vantaggio ha MSW su un mock scritto a mano di `fetch`?', 'What advantage does MSW have over a hand-written `fetch` mock?'), [L('I test restano validi anche se cambi libreria HTTP', 'Tests stay valid even if you change HTTP library'), L('Non servono più test', 'Tests are no longer needed'), L('È sempre più veloce di tutto', 'It is always faster than anything'), L('Funziona senza JavaScript', 'It works without JavaScript')], 0, L('Simula la rete, non una funzione precisa.', 'It fakes the network, not one specific function.')),
        ], task('js', L('Aggiungi un gestore MSW che risponde a `GET /api/utenti` con `HttpResponse.json([])`.', 'Add an MSW handler that answers `GET /api/utenti` with `HttpResponse.json([])`.'), 'import { http, HttpResponse } from "msw";
import { setupServer } from "msw/node";

const server = setupServer(
);', [re('http\.get\s*\(\s*["\'][^"\']*/api/utenti["\']\s*,', L('Intercetti la richiesta', 'You intercept the request')), re('HttpResponse\.json\s*\(\s*\[\s*\]\s*\)', L('Rispondi con una lista vuota', 'You answer with an empty list'))], '', 'const server = setupServer(
  http.get("/api/utenti", () => HttpResponse.json([])),
);'), [
            dOrder(pSort(), ['const server = setupServer(...gestori);', 'beforeAll(() => server.listen());', 'afterEach(() => server.resetHandlers());', 'afterAll(() => server.close());'], L('Crea, avvia, ripristina dopo ogni test, chiudi alla fine.', 'Create, start, reset after each test, close at the end.')),
            dFlash(pFlash(), [[L('Con MSW il codice fa richieste vere che ricevono risposte finte', 'With MSW the code makes real requests that get fake responses'), true], [L('`server.use` cambia le risposte di tutti i test, per sempre', '`server.use` changes the responses of every test, forever'), false], [L('Con MSW si può simulare un errore 500', 'With MSW you can fake a 500 error'), true], [L('MSW funziona solo nel browser', 'MSW only works in the browser'), false]], L('La rete, sotto controllo.', 'The network, under control.')),
        ]),
        lesson('ui-components', L('Testare le interfacce come un utente', 'Testing interfaces like a user'), [
            P('Per i componenti di un’interfaccia (React, Vue, Angular o HTML semplice) la famiglia **Testing Library** propone la stessa idea: trova gli elementi come li trova una persona, per **ruolo**, etichetta o testo, e interagisci come lei con **user-event**.', 'For interface components (React, Vue, Angular or plain HTML) the **Testing Library** family offers the same idea: find elements the way a person does, by **role**, label or text, and interact like them with **user-event**.'),
            C('import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { it, expect } from "vitest";
import Iscrizione from "./Iscrizione.jsx";

it("conferma l’iscrizione", async () => {
  const utente = userEvent.setup();
  render(<Iscrizione />);
  await utente.type(screen.getByLabelText("Email"), "ada@example.com");
  await utente.click(screen.getByRole("button", { name: "Iscriviti" }));
  expect(await screen.findByText("Grazie!")).toBeInTheDocument();
});', 'jsx'),
            P('`getBy…` trova subito o fallisce, `queryBy…` restituisce `null` se non c’è (utile per verificare che qualcosa **non** compaia), `findBy…` aspetta che l’elemento appaia. Se un elemento si trova solo con un `data-testid`, spesso è il segnale che manca un’etichetta accessibile.', '`getBy…` finds at once or fails, `queryBy…` returns `null` if it is not there (handy to check that something does **not** appear), `findBy…` waits for the element to appear. If an element can only be found with a `data-testid`, it is often a sign that an accessible label is missing.'),
        ], [
            Q('ui1', L('Quale ricerca aspetta un elemento che apparirà dopo una richiesta?', 'Which query waits for an element that will appear after a request?'), ['getByText', 'queryByText', 'findByText', 'getAllByText'], 2, L('`findBy…` restituisce una promessa.', '`findBy…` returns a promise.')),
            Q('ui2', L('Come verifichi che un messaggio di errore NON ci sia?', 'How do you check that an error message is NOT there?'), ['expect(screen.getByText("Errore")).toBeNull()', 'expect(screen.queryByText("Errore")).toBeNull()', 'expect(findByText("Errore")).toBe(false)', 'expect(screen).not.toBe("Errore")'], 1, L('`getBy…` lancerebbe un errore prima del controllo.', '`getBy…` would throw before the check.')),
            Q('ui3', L('Perché preferire `getByRole` a una classe CSS?', 'Why prefer `getByRole` over a CSS class?'), [L('Trova gli elementi come un utente e premia l’accessibilità', 'It finds elements like a user and rewards accessibility'), L('È l’unico metodo che esiste', 'It is the only method there is'), L('È sempre più veloce da scrivere', 'It is always quicker to write'), L('Le classi non funzionano nei test', 'Classes do not work in tests')], 0, L('Il test sopravvive anche se cambi lo stile.', 'The test survives even if you change the styling.')),
        ], task('jsx', L('Con `userEvent`, clicca il bottone con nome `Salva`: `await utente.click(screen.getByRole("button", { name: "Salva" }))`.', 'With `userEvent`, click the button named `Salva`: `await utente.click(screen.getByRole("button", { name: "Salva" }))`.'), 'const utente = userEvent.setup();
render(<Nota />);
', [re('await\s+utente\.click\s*\(\s*screen\.getByRole\s*\(\s*["\']button["\']\s*,\s*\{\s*name\s*:\s*["\']Salva["\']\s*\}\s*\)\s*\)', L('Clicchi il bottone giusto', 'You click the right button'))], '', 'const utente = userEvent.setup();
render(<Nota />);
await utente.click(screen.getByRole("button", { name: "Salva" }));'), [
            dMatch(pMatch(), [['getBy…', L('Trova subito o fallisce', 'Finds at once or fails')], ['queryBy…', L('null se non c’è', 'null if it is not there')], ['findBy…', L('Aspetta che appaia', 'Waits for it to appear')], ['userEvent', L('Interazioni realistiche', 'Realistic interactions')], ['getByLabelText', L('Trova un campo dalla sua etichetta', 'Finds a field by its label')]], L('Cerca come cerca un utente.', 'Search the way a user searches.')),
            dFlash(pFlash(), [[L('`findBy…` restituisce una promessa', '`findBy…` returns a promise'), true], [L('`getBy…` restituisce `null` se non trova niente', '`getBy…` returns `null` if it finds nothing'), false], [L('Testing Library esiste per React, Vue e Angular', 'Testing Library exists for React, Vue and Angular'), true], [L('Un buon test controlla lo stato interno del componente', 'A good test checks the component’s internal state'), false]], L('Ciò che si vede, non ciò che c’è dentro.', 'What is seen, not what is inside.')),
        ]),
    ],
];
