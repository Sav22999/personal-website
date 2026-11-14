<?php
/** Testing: why test and the pyramid, unit tests, mocks and doubles, integration and end-to-end, TDD and CI. */

return [
    'id' => 'testing', 'family' => 'testing', 'after' => 'js-base',
    'title' => L('Testing del software', 'Software Testing'), 'short' => L('Testing', 'Testing'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Scrivere codice che si può fidare: piramide dei test, test unitari, mock, test di integrazione ed end-to-end, TDD e integrazione continua.', 'Writing code you can trust: the test pyramid, unit tests, mocks, integration and end-to-end tests, TDD and continuous integration.'),
    'lessons' => [
        lesson('why', L('Perché testare', 'Why test'), [
            P('Un **test automatico** è codice che controlla altro codice: lo esegui in un secondo, quante volte vuoi. I test trovano errori prima degli utenti, permettono di **modificare senza paura** (se rompi qualcosa lo scopri subito) e documentano come il codice dovrebbe comportarsi.', 'An **automated test** is code that checks other code: you run it in a second, as often as you like. Tests find bugs before users do, let you **change code without fear** (if you break something you know at once) and document how the code should behave.'),
            C('          /\\        end-to-end: pochi, lenti, simulano l’utente
         /  \\
        /----\\      integrazione: più parti insieme (API, database)
       /      \\
      /--------\\    unitari: tanti, velocissimi, una funzione alla volta', 'text'),
            P('La **piramide dei test** suggerisce tanti test unitari alla base, alcuni di integrazione e pochi end-to-end in cima: i primi sono veloci e precisi nel dire cosa si è rotto, gli ultimi più realistici ma lenti e fragili.', 'The **test pyramid** suggests many unit tests at the base, some integration tests and a few end-to-end ones at the top: the former are fast and precise about what broke, the latter more realistic but slow and brittle.'),
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
        ], [
            Q('e1', L('Che cosa fa Playwright?', 'What does Playwright do?'), [L('Automatizza un browser per i test end-to-end', 'Automates a browser for end-to-end tests'), L('Gestisce database', 'Manages databases'), L('Compila C++', 'Compiles C++'), L('Disegna grafici', 'Draws charts')], 0, L('Simula l’utente.', 'It simulates the user.')),
            Q('e2', L('Qual è l’ordine del TDD?', 'What is the TDD order?'), [L('Rosso, verde, refactor', 'Red, green, refactor'), L('Verde, rosso, cancella', 'Green, red, delete'), L('Codice, poi test un anno dopo', 'Code, then tests a year later'), L('Refactor, rosso, verde', 'Refactor, red, green')], 0, L('Prima il test che fallisce.', 'Failing test first.')),
            Q('e3', L('Che cosa fa la CI con i test?', 'What does CI do with tests?'), [L('Li esegue a ogni push e pull request', 'Runs them on every push and pull request'), L('Li cancella', 'Deletes them'), L('Li scrive da sola', 'Writes them by itself'), L('Niente', 'Nothing')], 0, L('Niente merge se rosso.', 'No merge if red.')),
        ], task('yaml', L('Scrivi un workflow GitHub Actions che, a ogni `push`, su `ubuntu-latest` esegue `npm ci` e poi `npm test`.', 'Write a GitHub Actions workflow that, on every `push`, on `ubuntu-latest` runs `npm ci` and then `npm test`.'), "name: Test\n", [re('^on:\s*(\[\s*push|push|\n\s+push)', L('Si avvia al push', 'It triggers on push')), re('runs-on:\s*ubuntu-latest', L('La macchina', 'The machine')), re('-\s*run:\s*npm ci[\s\S]*-\s*run:\s*npm test', L('Installa e testa', 'It installs and tests'))], '', "name: Test\non: [push]\njobs:\n  test:\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@v4\n      - uses: actions/setup-node@v4\n        with: { node-version: 22 }\n      - run: npm ci\n      - run: npm test"), [
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
    ],
];
