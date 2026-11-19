<?php
/** REST and GraphQL APIs: resources and HTTP methods, status codes and errors, versioning, pagination and auth, GraphQL, OpenAPI
 *  and webhooks, HTTP caching and ETags, idempotency and retries, OAuth 2 and JWT, API security (OWASP API Top 10). */

/** A sequence diagram: actors on top with dashed lifelines, messages as arrows from top to bottom.
 *  $xs are the lifeline positions; a message is [from, to, label, kind] with kind '' (request), 'back' (dashed reply),
 *  'ok' (green reply), 'lost' (red, cut halfway), 'note' (a box beside the lifeline of from) or 'gap' (a pause across the diagram). */
if (!function_exists('fig_api_seq')) {
    function fig_api_seq(array $actors, array $xs, array $msgs, int $step = 36): string
    {
        $h = 70 + count($msgs) * $step + 14;
        $out = '';
        foreach ($actors as $i => $a) {
            $x = $xs[$i];
            $out .= "<line x1=\"$x\" y1=\"50\" x2=\"$x\" y2=\"" . ($h - 8) . "\" stroke=\"#8A87B0\" stroke-width=\"2\" stroke-dasharray=\"5 5\"/>"
                  . "<rect x=\"" . ($x - 66) . "\" y=\"12\" width=\"132\" height=\"38\" rx=\"10\" fill=\"#ECE6FF\" stroke=\"#7048E8\" stroke-width=\"2\"/><text x=\"$x\" y=\"36\" text-anchor=\"middle\" font-weight=\"700\">$a</text>";
        }
        foreach ($msgs as $k => [$f, $t, $label, $kind]) {
            $y = 70 + $k * $step + 22; $x1 = $xs[$f]; $x2 = $xs[$t];
            if ($kind === 'gap') {
                $out .= "<line x1=\"16\" y1=\"" . ($y - 4) . "\" x2=\"624\" y2=\"" . ($y - 4) . "\" stroke=\"#D9D7E8\" stroke-width=\"2\"/><rect x=\"250\" y=\"" . ($y - 16) . "\" width=\"140\" height=\"24\" rx=\"12\" fill=\"#FFFFFF\" stroke=\"#D9D7E8\" stroke-width=\"2\"/><text x=\"320\" y=\"" . $y . "\" text-anchor=\"middle\" font-size=\"13\" font-style=\"italic\">$label</text>";
                continue;
            }
            if ($kind === 'note' || $kind === 'noteL') {
                $w = 12 + (int) ceil(mb_strlen(strip_tags($label)) * 6.9);
                $nx = $kind === 'note' ? $x1 + 10 : $x1 - 10 - $w;
                $out .= "<rect x=\"$nx\" y=\"" . ($y - 20) . "\" width=\"$w\" height=\"28\" rx=\"8\" fill=\"#FFF3EA\" stroke=\"#E8590C\" stroke-width=\"2\"/><text x=\"" . ($nx + 8) . "\" y=\"" . ($y - 1) . "\" font-size=\"13\">$label</text>";
                continue;
            }
            [$col, $dash, $mk] = match ($kind) { 'back' => ['#1E1C52', ' stroke-dasharray="6 4"', 'a'], 'ok' => ['#0B7F5C', ' stroke-dasharray="6 4"', 'g'], 'lost' => ['#F03E82', ' stroke-dasharray="6 4"', ''], default => ['#1E1C52', '', 'a'] };
            $mid = ($x1 + $x2) / 2; $dir = $x2 > $x1 ? 1 : -1;
            $end = $kind === 'lost' ? $mid : $x2 - $dir * 4;
            $marker = $mk ? " marker-end=\"url(#$mk)\"" : '';
            $out .= "<path d=\"M" . ($x1 + $dir * 2) . ",$y H$end\" stroke=\"$col\" stroke-width=\"2\" fill=\"none\"$dash$marker/>";
            if ($kind === 'lost') $out .= "<path d=\"M" . ($mid - 8) . "," . ($y - 8) . " L" . ($mid + 8) . "," . ($y + 8) . " M" . ($mid + 8) . "," . ($y - 8) . " L" . ($mid - 8) . "," . ($y + 8) . "\" stroke=\"#F03E82\" stroke-width=\"3\"/>";
            $tc = $kind === 'ok' ? '#0B7F5C' : ($kind === 'lost' ? '#B0205A' : '#1E1C52');
            $lx = $kind === 'lost' ? ($x1 + $mid) / 2 : $mid;
            $lw = 10 + (int) ceil(mb_strlen(html_entity_decode(strip_tags($label))) * 6.6);
            $out .= "<rect x=\"" . ($lx - $lw / 2) . "\" y=\"" . ($y - 21) . "\" width=\"$lw\" height=\"18\" rx=\"4\" fill=\"#FFFFFF\" opacity=\"0.9\"/>";
            $out .= "<text x=\"$lx\" y=\"" . ($y - 7) . "\" text-anchor=\"middle\" font-size=\"13\" fill=\"$tc\">$label</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 $h" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="a" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker>
<marker id="g" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#0B7F5C"/></marker></defs>
{$out}
</svg>
SVG;
    }
}

/** The anatomy of an HTTP request and its response, with each part labelled. */
if (!function_exists('fig_api_http')) {
    function fig_api_http(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 330" font-family="ui-monospace, Menlo, monospace" font-size="14" fill="#1E1C52">
<rect x="12" y="12" width="420" height="146" rx="12" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="12" y="172" width="420" height="146" rx="12" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/>
<text x="30" y="40"><tspan font-weight="700" fill="#1B66BC">GET /ordini/42</tspan> HTTP/1.1</text>
<text x="30" y="72">Host: api.esempio.it</text>
<text x="30" y="94">Authorization: Bearer eyJ…</text>
<text x="30" y="116">Accept: application/json</text>
<rect x="24" y="128" width="380" height="22" rx="6" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/>
<text x="30" y="200">HTTP/1.1 <tspan font-weight="700" fill="#0B7F5C">200 OK</tspan></text>
<text x="30" y="232">Content-Type: application/json</text>
<text x="30" y="254">Cache-Control: private</text>
<rect x="24" y="268" width="380" height="38" rx="8" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/>
<text x="36" y="292">{ "id": 42, "stato": "pagato" }</text>
<g font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<path d="M442,35 H456" stroke="#2F8CF0" stroke-width="2"/><text x="462" y="40" font-weight="700">{$t[0]}</text>
<path d="M442,93 H456" stroke="#2F8CF0" stroke-width="2"/><text x="462" y="98" font-weight="700">{$t[1]}</text>
<path d="M442,141 H456" stroke="#2F8CF0" stroke-width="2"/><text x="462" y="146">{$t[2]}</text>
<path d="M442,195 H456" stroke="#0B7F5C" stroke-width="2"/><text x="462" y="200" font-weight="700">{$t[3]}</text>
<path d="M442,243 H456" stroke="#0B7F5C" stroke-width="2"/><text x="462" y="248" font-weight="700">{$t[1]}</text>
<path d="M442,287 H456" stroke="#0B7F5C" stroke-width="2"/><text x="462" y="292" font-weight="700">{$t[4]}</text>
</g>
</svg>
SVG;
    }
}

/** REST against GraphQL for the same page: two requests that bring extra fields, or one query that asks for exactly the fields needed. */
if (!function_exists('fig_api_graphql')) {
    function fig_api_graphql(array $t): string
    {
        $chips = function (int $x, int $y, array $f): string {
            $o = ''; $cx = $x;
            foreach ($f as [$name, $used]) {
                $w = 16 + strlen($name) * 8;
                $o .= $used
                    ? "<rect x=\"$cx\" y=\"$y\" width=\"$w\" height=\"26\" rx=\"13\" fill=\"#D5F6EA\" stroke=\"#0B7F5C\" stroke-width=\"2\"/><text x=\"" . ($cx + $w / 2) . "\" y=\"" . ($y + 18) . "\" text-anchor=\"middle\" font-weight=\"700\">$name</text>"
                    : "<rect x=\"$cx\" y=\"$y\" width=\"$w\" height=\"26\" rx=\"13\" fill=\"#FFFFFF\" stroke=\"#8A87B0\" stroke-width=\"2\" stroke-dasharray=\"4 3\"/><text x=\"" . ($cx + $w / 2) . "\" y=\"" . ($y + 18) . "\" text-anchor=\"middle\" fill=\"#6E6B96\">$name</text>";
                $cx += $w + 6;
            }
            return $o;
        };
        $c1 = $chips(24, 96, [['id', 0], ['nome', 1], ['email', 0], ['indirizzo', 0]]);
        $c2 = $chips(24, 190, [['id', 0], ['totale', 1], ['data', 1], ['righe', 0]]);
        $c3 = $chips(344, 120, [['nome', 1], ['totale', 1], ['data', 1]]);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 290" font-family="ui-monospace, Menlo, monospace" font-size="13" fill="#1E1C52">
<rect x="8" y="8" width="306" height="274" rx="12" fill="#F1F0F8"/><rect x="326" y="8" width="306" height="274" rx="12" fill="#F1F0F8"/>
<g font-family="system-ui, sans-serif" font-size="15" font-weight="700"><text x="24" y="36">REST</text><text x="342" y="36">GraphQL</text></g>
<g font-family="system-ui, sans-serif" font-size="13"><text x="298" y="36" text-anchor="end">{$t[0]}</text><text x="616" y="36" text-anchor="end">{$t[1]}</text></g>
<rect x="24" y="52" width="200" height="30" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="36" y="72" font-weight="700">GET /clienti/7</text>
{$c1}
<rect x="24" y="146" width="230" height="30" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="36" y="166" font-weight="700">GET /clienti/7/ordini</text>
{$c2}
<rect x="342" y="52" width="160" height="30" rx="8" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="354" y="72" font-weight="700">POST /graphql</text>
<text x="344" y="106" font-family="system-ui, sans-serif">{$t[2]}</text>
{$c3}
<g font-family="system-ui, sans-serif" font-size="13">
<rect x="24" y="242" width="22" height="16" rx="8" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 3"/><text x="54" y="255">{$t[3]}</text>
<rect x="344" y="242" width="22" height="16" rx="8" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="374" y="255">{$t[4]}</text>
</g>
</svg>
SVG;
    }
}

return [
    'id' => 'api-design', 'family' => 'api', 'after' => null,
    'title' => L('API REST e GraphQL', 'REST and GraphQL APIs'), 'short' => L('API', 'APIs'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Progettare API chiare: risorse e metodi HTTP, codici di stato ed errori, paginazione e versioni, cache e idempotenza, OAuth 2 e sicurezza delle API, e quando scegliere GraphQL.', 'Designing clear APIs: resources and HTTP methods, status codes and errors, pagination and versioning, caching and idempotency, OAuth 2 and API security, and when to choose GraphQL.'),
    'lessons' => [
        lesson('rest', L('Risorse e metodi', 'Resources and methods'), [
            P('Un’API **REST** espone **risorse** (utenti, ordini, prodotti) a indirizzi chiari, usando i **metodi HTTP** per dire cosa fare. I percorsi usano nomi al plurale, non verbi: l’azione la dice il metodo.', 'A **REST** API exposes **resources** (users, orders, products) at clear addresses, using **HTTP methods** to say what to do. Paths use plural nouns, not verbs: the method states the action.'),
            C('GET    /ordini            elenco degli ordini
GET    /ordini/42         un ordine
POST   /ordini            crea un ordine (dati nel corpo JSON)
PUT    /ordini/42         sostituisce l’ordine intero
PATCH  /ordini/42         modifica alcuni campi
DELETE /ordini/42         cancella l’ordine
GET    /clienti/7/ordini  gli ordini del cliente 7', 'http'),
            P('`GET` non deve mai modificare dati: si può ripetere e mettere in cache. `PUT` e `DELETE` sono **idempotenti** (ripeterli dà lo stesso risultato), `POST` no: due `POST` creano due ordini.', '`GET` must never change data: it can be repeated and cached. `PUT` and `DELETE` are **idempotent** (repeating them gives the same result), `POST` is not: two `POST`s create two orders.'),
            P('Ogni chiamata è una **richiesta** con metodo, percorso, intestazioni e, se serve, un corpo JSON; torna indietro una **risposta** con un codice di stato, le sue intestazioni e il corpo. Il server non ricorda nulla tra una richiesta e l’altra (REST è **senza stato**): chi sei e cosa vuoi viaggiano ogni volta nella richiesta, di solito con un token nell’intestazione `Authorization`.', 'Every call is a **request** with a method, a path, headers and, when needed, a JSON body; back comes a **response** with a status code, its headers and the body. The server remembers nothing between one request and the next (REST is **stateless**): who you are and what you want travel every time in the request, usually with a token in the `Authorization` header.'),
            Fig(L(fig_api_http(['metodo e percorso', 'intestazioni', 'nessun corpo', 'codice di stato', 'corpo JSON']), fig_api_http(['method and path', 'headers', 'no body', 'status code', 'JSON body'])),
                L('Sopra la richiesta: la riga GET /ordini/42 con metodo e percorso evidenziati, poi le intestazioni Host, Authorization e Accept, e nessun corpo. Sotto la risposta: HTTP/1.1 200 OK con il codice evidenziato, le intestazioni Content-Type e Cache-Control e il corpo JSON con id 42 e stato pagato.', 'Above, the request: the GET /ordini/42 line with method and path highlighted, then the Host, Authorization and Accept headers, and no body. Below, the response: HTTP/1.1 200 OK with the code highlighted, the Content-Type and Cache-Control headers and the JSON body with id 42 and stato pagato.'),
                L('Richiesta e risposta hanno la stessa forma: una prima riga, le intestazioni, il corpo.', 'Request and response share the same shape: a first line, the headers, the body.')),
        ], [
            Q('r1', L('Qual è un buon percorso REST per gli utenti?', 'What is a good REST path for users?'), ['/utenti', '/getUtenti', '/creaUtente', '/utente_lista_tutti'], 0, L('Nomi, non verbi.', 'Nouns, not verbs.')),
            Q('r2', L('Quale metodo crea una nuova risorsa?', 'Which method creates a new resource?'), ['POST', 'GET', 'DELETE', 'HEAD'], 0, L('Con i dati nel corpo.', 'With data in the body.')),
            Q('r3', L('Che cosa significa che PUT è idempotente?', 'What does it mean that PUT is idempotent?'), [L('Ripeterlo dà lo stesso risultato', 'Repeating it gives the same result'), L('È più veloce', 'It is faster'), L('Non modifica niente', 'It changes nothing'), L('Crea duplicati', 'It creates duplicates')], 0, L('POST invece no.', 'POST is not.')),
        ], task('text', L('Scrivi metodo e percorso per: elencare i prodotti, creare un prodotto, cancellare il prodotto 15. Uno per riga, come `GET /prodotti`.', 'Write method and path for: listing products, creating a product, deleting product 15. One per line, like `GET /prodotti`.'), '', [re('^GET\s+/prodotti\s*$', L('Elenco', 'List')), re('^POST\s+/prodotti\s*$', L('Creazione', 'Creation')), re('^DELETE\s+/prodotti/15\s*$', L('Cancellazione', 'Deletion'))], '', "GET /prodotti\nPOST /prodotti\nDELETE /prodotti/15"), [
            dMatch(pMatch(), [['GET', L('Legge', 'Reads')], ['POST', L('Crea', 'Creates')], ['PUT', L('Sostituisce', 'Replaces')], ['PATCH', L('Modifica in parte', 'Partially updates')], ['DELETE', L('Cancella', 'Deletes')]], L('I metodi HTTP.', 'HTTP methods.')),
            dBug(pBug(), ['GET  /ordini', 'GET  /ordini/42', 'GET  /ordini/42/cancella', 'POST /ordini'], 2, 'DELETE /ordini/42', L('Per cancellare si usa DELETE, non un GET con un verbo.', 'Deleting uses DELETE, not a GET with a verb.')),
        ]),
        lesson('status', L('Codici di stato ed errori', 'Status codes and errors'), [
            P('Ogni risposta ha un **codice di stato**: `2xx` successo, `4xx` errore del client (richiesta sbagliata), `5xx` errore del server. Usare il codice giusto permette ai client di reagire senza leggere il testo.', 'Every response has a **status code**: `2xx` success, `4xx` client error (bad request), `5xx` server error. Using the right code lets clients react without reading the text.'),
            C('200 OK               lettura o modifica riuscita
201 Created          risorsa creata (+ header Location)
204 No Content       riuscito, niente da restituire (es. DELETE)
400 Bad Request      dati non validi
401 Unauthorized     non autenticato
403 Forbidden        autenticato ma senza permesso
404 Not Found        risorsa inesistente
409 Conflict         conflitto (es. email già usata)
429 Too Many Requests  troppe richieste
500 Internal Server Error', 'http'),
            C('{
  "errore": "validazione",
  "messaggio": "Alcuni campi non sono validi",
  "dettagli": [{ "campo": "email", "problema": "formato non valido" }]
}', 'json'),
            P('Gli errori devono avere sempre lo **stesso formato** JSON, con un codice leggibile dalla macchina e un messaggio per le persone; lo standard **Problem Details** (RFC 9457, tipo `application/problem+json`) ne propone uno pronto. Non mostrare mai stack trace o dettagli interni.', 'Errors must always have the **same** JSON **format**, with a machine-readable code and a message for people; the **Problem Details** standard (RFC 9457, type `application/problem+json`) offers a ready-made one. Never expose stack traces or internal details.'),
            P('Errori comuni: rispondere `200` con dentro `{"errore": …}`, così i client e i sistemi di monitoraggio credono che tutto sia andato bene; confondere `401` (non so chi sei: accedi) con `403` (so chi sei, ma non puoi). Una regola utile per chi chiama: con un `4xx` deve correggere la richiesta, con un `5xx` può riprovare più tardi.', 'Common mistakes: answering `200` with `{"error": …}` inside, so clients and monitoring systems believe all went well; mixing up `401` (I do not know who you are: sign in) with `403` (I know who you are, but you may not). A useful rule for callers: with a `4xx` they must fix the request, with a `5xx` they may retry later.'),
        ], [
            Q('s1', L('Quale codice per una risorsa appena creata?', 'Which code for a newly created resource?'), ['201', '200', '204', '302'], 0, L('Created.', 'Created.')),
            Q('s2', L('Che differenza c’è tra 401 e 403?', 'What is the difference between 401 and 403?'), [L('401 non autenticato, 403 autenticato ma senza permesso', '401 not authenticated, 403 authenticated but not allowed'), L('Nessuna', 'None'), L('403 è un errore del server', '403 is a server error'), L('401 significa successo', '401 means success')], 0, L('Chi sei vs cosa puoi fare.', 'Who you are vs what you may do.')),
            Q('s3', L('Che cosa indicano i codici 5xx?', 'What do 5xx codes mean?'), [L('Errori del server', 'Server errors'), L('Successo', 'Success'), L('Errori del client', 'Client errors'), L('Redirezioni', 'Redirects')], 0, L('4xx sono del client.', '4xx are client errors.')),
        ], task('text', L('Indica il codice giusto per ciascun caso, uno per riga come `caso: codice`: `email già registrata`, `prodotto inesistente`, `ordine cancellato senza corpo di risposta`.', 'Give the right code for each case, one per line as `case: code`: `email already registered`, `product not found`, `order deleted with no response body`.'), '', [re('(email)[^:\n]*:\s*409', L('Conflitto: 409', 'Conflict: 409')), re('(prodotto|product)[^:\n]*:\s*404', L('Non trovato: 404', 'Not found: 404')), re('(cancellat|deleted)[^:\n]*:\s*204', L('Nessun contenuto: 204', 'No content: 204'))], '', "email già registrata: 409\nprodotto inesistente: 404\nordine cancellato senza corpo di risposta: 204"), [
            dMatch(pMatch(), [['200', 'OK'], ['201', 'Created'], ['400', L('Dati non validi', 'Invalid data')], ['404', L('Non trovato', 'Not found')], ['500', L('Errore del server', 'Server error')]], L('I codici più comuni.', 'The most common codes.')),
            dFlash(pFlash(), [[L('Gli errori dovrebbero avere un formato coerente', 'Errors should have a consistent format'), true], [L('Conviene restituire 200 anche per gli errori', 'Returning 200 for errors is wise'), false], [L('429 significa troppe richieste', '429 means too many requests'), true], [L('Lo stack trace va mostrato al client', 'The stack trace should be shown to the client'), false]], L('Parlare con i codici.', 'Speaking in codes.')),
        ]),
        lesson('practices', L('Paginazione, versioni e autenticazione', 'Pagination, versioning and authentication'), [
            P('Le liste lunghe si restituiscono a **pagine**: con `?limit=20&offset=40` oppure, meglio per dati che cambiano, con un **cursore** (`?after=ord_8f2`). Filtri e ordinamento vanno nei parametri: `?stato=pagato&ordina=-data`.', 'Long lists are returned in **pages**: with `?limit=20&offset=40` or, better for changing data, with a **cursor** (`?after=ord_8f2`). Filters and sorting go in parameters: `?stato=pagato&ordina=-data`.'),
            C('GET /v1/ordini?stato=pagato&limit=20&after=ord_8f2
Authorization: Bearer eyJhbGciOi…

200 OK
{ "dati": [ … ], "prossimo": "ord_9a1" }', 'http'),
            P('Quando un cambiamento rompe i client esistenti, si crea una nuova **versione** (`/v2/…`) e si mantiene la vecchia per un po’. Per l’**autenticazione** si usano token nell’intestazione `Authorization` (spesso OAuth 2.0 o chiavi API), sempre su **HTTPS**. Limita le richieste (**rate limiting**) per proteggere il servizio.', 'When a change breaks existing clients, you create a new **version** (`/v2/…`) and keep the old one for a while. For **authentication** you use tokens in the `Authorization` header (often OAuth 2.0 or API keys), always over **HTTPS**. Limit requests (**rate limiting**) to protect the service.'),
        ], [
            Q('p1', L('Perché paginare una lista?', 'Why paginate a list?'), [L('Per non restituire migliaia di elementi insieme', 'To avoid returning thousands of items at once'), L('Per abbellirla', 'To make it pretty'), L('È vietato non farlo', 'It is forbidden not to'), L('Per cifrarla', 'To encrypt it')], 0, L('Risposte leggere.', 'Light responses.')),
            Q('p2', L('Dove si mette il token di accesso?', 'Where does the access token go?'), [L('Nell’intestazione Authorization', 'In the Authorization header'), L('Nell’URL', 'In the URL'), L('Nel nome del file', 'In the file name'), L('In un commento', 'In a comment')], 0, L('Mai nell’URL, finisce nei log.', 'Never in the URL, it ends up in logs.')),
            Q('p3', L('Quando creare una nuova versione dell’API?', 'When should you create a new API version?'), [L('Quando un cambiamento rompe i client esistenti', 'When a change breaks existing clients'), L('Ogni giorno', 'Every day'), L('Mai', 'Never'), L('Quando cambi il logo', 'When you change the logo')], 0, L('E mantieni la vecchia per un po’.', 'And keep the old one a while.')),
        ], task('text', L('Scrivi la richiesta per la seconda pagina di 10 clienti attivi della versione 1, con un token nell’intestazione: prima riga `GET …`, seconda riga `Authorization: Bearer …`.', 'Write the request for the second page of 10 active customers on version 1, with a token in the header: first line `GET …`, second line `Authorization: Bearer …`.'), '', [re('^GET\s+/v1/clienti\?', L('Versione e risorsa', 'Version and resource')), re('(limit=10)', L('10 per pagina', '10 per page')), re('(offset=10|page=2|pagina=2|after=\w+)', L('La seconda pagina', 'The second page')), re('(stato|attiv|active|status)=', L('Il filtro sugli attivi', 'The active filter')), re('^Authorization:\s*Bearer\s+\S+', L('Il token', 'The token'))], '', "GET /v1/clienti?stato=attivo&limit=10&offset=10\nAuthorization: Bearer abc123"), [
            dMatch(pMatch(), [['limit / offset', L('Paginazione semplice', 'Simple pagination')], [L('Cursore', 'Cursor'), L('Paginazione stabile', 'Stable pagination')], ['/v2', L('Nuova versione', 'New version')], ['Authorization: Bearer', L('Token di accesso', 'Access token')], ['Rate limiting', L('Limite di richieste', 'Request limit')]], L('Buone pratiche.', 'Good practice.')),
            dFlash(pFlash(), [[L('I token non vanno messi nell’URL', 'Tokens must not go in the URL'), true], [L('Un’API può fare a meno di HTTPS', 'An API can do without HTTPS'), false], [L('Il cursore è adatto a dati che cambiano', 'A cursor suits changing data'), true], [L('Cambiare un campo senza nuova versione non rompe mai nulla', 'Changing a field without a new version never breaks anything'), false]], L('API robuste.', 'Robust APIs.')),
        ]),
        lesson('graphql', L('GraphQL', 'GraphQL'), [
            P('**GraphQL** è un’alternativa a REST: c’è un solo endpoint e il client scrive una **query** che dice esattamente quali campi vuole, anche da risorse collegate. Niente dati in più, niente richieste multiple per comporre una pagina.', '**GraphQL** is an alternative to REST: there is a single endpoint and the client writes a **query** saying exactly which fields it wants, even from related resources. No extra data, no multiple requests to build a page.'),
            Fig(L(fig_api_graphql(['2 richieste', '1 richiesta', 'solo i campi chiesti:', 'campi scaricati ma non usati', 'campi usati dalla pagina']), fig_api_graphql(['2 requests', '1 request', 'only the fields asked for:', 'fields downloaded but not used', 'fields used by the page'])),
                L('A sinistra, con REST, due richieste: GET /clienti/7 restituisce id, nome, email e indirizzo, GET /clienti/7/ordini restituisce id, totale, data e righe, ma la pagina usa solo nome, totale e data. A destra, con GraphQL, una sola POST /graphql restituisce esattamente nome, totale e data.', 'On the left, with REST, two requests: GET /clienti/7 returns id, nome, email and indirizzo, GET /clienti/7/ordini returns id, totale, data and righe, but the page uses only nome, totale and data. On the right, with GraphQL, a single POST /graphql returns exactly nome, totale and data.'),
                L('GraphQL toglie le richieste in più e i campi inutili, ma sposta il lavoro sul server.', 'GraphQL removes the extra requests and the useless fields, but moves the work to the server.')),
            C('# schema
type Cliente { id: ID!  nome: String!  ordini(ultimi: Int): [Ordine!]! }
type Ordine  { id: ID!  totale: Float!  data: String! }
type Query   { cliente(id: ID!): Cliente }

# query del client
query {
  cliente(id: "7") {
    nome
    ordini(ultimi: 3) { totale data }
  }
}', 'graphql'),
            P('Le modifiche si fanno con le **mutation**. GraphQL è comodo per app con interfacce ricche e molti client diversi; REST resta più semplice da mettere in cache e da capire. Con GraphQL attenzione alle query troppo profonde o costose: servono limiti.', 'Changes are made with **mutations**. GraphQL is handy for apps with rich interfaces and many different clients; REST stays simpler to cache and to understand. With GraphQL beware of overly deep or expensive queries: limits are needed.'),
        ], [
            Q('g1', L('Quanti endpoint ha di solito un’API GraphQL?', 'How many endpoints does a GraphQL API usually have?'), [L('Uno', 'One'), L('Uno per risorsa', 'One per resource'), L('Nessuno', 'None'), L('Uno per campo', 'One per field')], 0, L('La query dice cosa vuoi.', 'The query says what you want.')),
            Q('g2', L('Come si modificano i dati in GraphQL?', 'How is data changed in GraphQL?'), [L('Con le mutation', 'With mutations'), L('Con GET', 'With GET'), L('Con le query', 'With queries'), L('Non si può', 'You cannot')], 0, L('Query leggono, mutation modificano.', 'Queries read, mutations change.')),
            Q('g3', L('Qual è un vantaggio di GraphQL?', 'What is an advantage of GraphQL?'), [L('Il client chiede esattamente i campi che servono', 'The client asks for exactly the fields it needs'), L('Si mette in cache più facilmente di REST', 'It caches more easily than REST'), L('Non ha bisogno di uno schema', 'It needs no schema'), L('Niente', 'Nothing')], 0, L('Niente dati in più.', 'No extra data.')),
        ], task('graphql', L('Scrivi una query GraphQL che chieda il `titolo` e il `prezzo` del prodotto con `id: "12"`.', 'Write a GraphQL query asking for the `titolo` and `prezzo` of the product with `id: "12"`.'), "query {\n\n}", [re('prodotto\s*\(\s*id\s*:\s*"12"\s*\)\s*\{', L('Selezioni il prodotto', 'You select the product')), re('\{[^}]*\btitolo\b[^}]*\bprezzo\b[^}]*\}|\{[^}]*\bprezzo\b[^}]*\btitolo\b[^}]*\}', L('Chiedi i due campi', 'You ask for both fields'))], '', "query {\n  prodotto(id: \"12\") {\n    titolo\n    prezzo\n  }\n}"), [
            dMatch(pMatch(), [['query', L('Legge dati', 'Reads data')], ['mutation', L('Modifica dati', 'Changes data')], ['type', L('Definisce la forma dei dati', 'Defines data shape')], ['ID!', L('Identificatore obbligatorio', 'Required identifier')], ['[Ordine!]!', L('Lista di ordini', 'List of orders')]], L('Il vocabolario di GraphQL.', 'GraphQL vocabulary.')),
            dFlash(pFlash(), [[L('In GraphQL il client sceglie i campi', 'In GraphQL the client picks the fields'), true], [L('GraphQL non usa uno schema', 'GraphQL uses no schema'), false], [L('Le query troppo profonde vanno limitate', 'Overly deep queries must be limited'), true], [L('GraphQL sostituisce sempre REST', 'GraphQL always replaces REST'), false]], L('REST o GraphQL: dipende.', 'REST or GraphQL: it depends.')),
        ]),
        lesson('docs', L('Documentazione, limiti e webhook', 'Documentation, limits and webhooks'), [
            P('Un’API si usa bene solo se è **documentata**. Lo standard **OpenAPI** descrive in YAML o JSON rotte, parametri, risposte ed errori; da quel file nascono pagine interattive, client generati e test di contratto.', 'An API is only easy to use if it is **documented**. The **OpenAPI** standard describes routes, parameters, responses and errors in YAML or JSON; from that file come interactive pages, generated clients and contract tests.'),
            C('paths:
  /corsi/{id}:
    get:
      summary: Un corso
      parameters:
        - name: id
          in: path
          required: true
          schema: { type: string }
      responses:
        "200": { description: Il corso }
        "404": { description: Corso non trovato }', 'yaml'),
            P('Il **rate limiting** protegge il servizio: oltre un certo numero di richieste si risponde `429 Too Many Requests` con l’intestazione `Retry-After`. I **webhook** fanno il contrario di una chiamata: è il servizio che avvisa te, con una POST al tuo indirizzo, quando succede qualcosa; va verificata con una **firma**.', '**Rate limiting** protects the service: beyond a certain number of requests you answer `429 Too Many Requests` with the `Retry-After` header. **Webhooks** do the opposite of a call: the service notifies you, with a POST to your address, when something happens; it must be verified with a **signature**.'),
        ], [
            Q('dc1', L('Che cosa descrive un file OpenAPI?', 'What does an OpenAPI file describe?'), [L('Rotte, parametri, risposte ed errori', 'Routes, parameters, responses and errors'), L('Il database', 'The database'), L('Il design grafico', 'The graphic design'), L('I server fisici', 'The physical servers')], 0, L('Da lì nascono documentazione e client.', 'Docs and clients come from it.')),
            Q('dc2', L('Quale codice indica troppe richieste?', 'Which code means too many requests?'), ['429', '404', '500', '201'], 0, L('Con `Retry-After`.', 'With `Retry-After`.')),
            Q('dc3', L('Perché verificare la firma di un webhook?', 'Why verify a webhook’s signature?'), [L('Per sapere che arriva davvero dal servizio', 'To know it really comes from the service'), L('Per renderlo più veloce', 'To make it faster'), L('Per tradurlo', 'To translate it'), L('Non serve', 'It is not needed')], 0, L('Chiunque può fare una POST.', 'Anyone can send a POST.')),
        ], task('yaml', L('Scrivi la risposta `"429"` con `description: Troppe richieste` sotto `responses:`.', 'Write the `"429"` response with `description: Troppe richieste` under `responses:`.'), '', [re('responses\s*:', L('C’è responses', 'responses is there')), re('"?429"?\s*:\s*\{?\s*description\s*:\s*Troppe richieste', L('La risposta 429 è descritta', 'The 429 response is described'))], '', "responses:\n  \"429\": { description: Troppe richieste }"), [
            dMatch(pMatch(), [['OpenAPI', L('Descrive l’API', 'Describes the API')], ['429', L('Troppe richieste', 'Too many requests')], ['Retry-After', L('Quando riprovare', 'When to retry')], ['Webhook', L('Il servizio ti avvisa', 'The service notifies you')], [L('Firma', 'Signature'), L('Prova l’origine', 'Proves the origin')]], L('API mature.', 'Mature APIs.')),
            dFlash(pFlash(), [[L('Da un file OpenAPI si generano client', 'Clients can be generated from an OpenAPI file'), true], [L('Un webhook è una GET del client', 'A webhook is a GET from the client'), false], [L('Il rate limiting protegge il servizio', 'Rate limiting protects the service'), true], [L('429 significa risorsa creata', '429 means resource created'), false]], L('Documentare e proteggere.', 'Documenting and protecting.')),
        ]),
        lesson('caching', L('Cache HTTP ed ETag', 'HTTP caching and ETags'), [
            P('HTTP ha una cache incorporata. `Cache-Control: max-age=300` permette a browser e CDN di riusare una risposta per 300 secondi; `private` la lascia solo al browser dell’utente; `no-store` vieta di salvarla (dati personali). Un **ETag** è l’impronta di una versione: il client lo rimanda con `If-None-Match` e, se nulla è cambiato, il server risponde `304 Not Modified` senza corpo.', 'HTTP has a built-in cache. `Cache-Control: max-age=300` lets browsers and CDNs reuse a response for 300 seconds; `private` leaves it to the user’s browser only; `no-store` forbids storing it (personal data). An **ETag** is the fingerprint of a version: the client sends it back with `If-None-Match` and, if nothing changed, the server answers `304 Not Modified` with no body.'),
            Fig(L(fig_api_seq(['Client', 'Server'], [140, 500], [[0, 1, 'GET /prodotti/7', ''], [1, 0, '200 OK · ETag: "v7" · corpo JSON', 'back'], [0, 0, 'cinque minuti dopo', 'gap'], [0, 1, 'GET /prodotti/7 · If-None-Match: "v7"', ''], [1, 0, '304 Not Modified · nessun corpo', 'ok']]), fig_api_seq(['Client', 'Server'], [140, 500], [[0, 1, 'GET /prodotti/7', ''], [1, 0, '200 OK · ETag: "v7" · JSON body', 'back'], [0, 0, 'five minutes later', 'gap'], [0, 1, 'GET /prodotti/7 · If-None-Match: "v7"', ''], [1, 0, '304 Not Modified · no body', 'ok']])),
                L('Un diagramma di sequenza tra client e server. Il client chiede GET /prodotti/7 e riceve 200 OK con ETag "v7" e il corpo JSON. Cinque minuti dopo richiede la stessa risorsa con If-None-Match "v7" e il server risponde 304 Not Modified, senza corpo.', 'A sequence diagram between client and server. The client asks GET /prodotti/7 and receives 200 OK with ETag "v7" and the JSON body. Five minutes later it asks for the same resource with If-None-Match "v7" and the server answers 304 Not Modified, with no body.'),
                L('La seconda volta viaggia solo una conferma: il client riusa la copia che ha già.', 'The second time only a confirmation travels: the client reuses the copy it already has.')),
            C('GET /corsi/42
200 OK
Cache-Control: public, max-age=300
ETag: "v7"

GET /corsi/42
If-None-Match: "v7"
304 Not Modified

PUT /corsi/42
If-Match: "v7"
412 Precondition Failed        ← qualcun altro l’ha già cambiato', 'http'),
            P('Lo stesso ETag protegge dagli **aggiornamenti persi** (concorrenza ottimistica): con `If-Match` la modifica riesce solo se la risorsa è ancora a quella versione; altrimenti il server risponde `412 Precondition Failed` e il client ricarica i dati prima di riprovare.', 'The same ETag protects against **lost updates** (optimistic concurrency): with `If-Match` the change succeeds only if the resource is still at that version; otherwise the server answers `412 Precondition Failed` and the client reloads the data before trying again.'),
        ], [
            Q('ch1', L('Che cosa significa `304 Not Modified`?', 'What does `304 Not Modified` mean?'), [L('La risorsa non esiste più', 'The resource no longer exists'), L('La copia del client è ancora valida: niente corpo da scaricare', 'The client’s copy is still valid: no body to download'), L('Il server non ha capito la richiesta', 'The server did not understand the request'), L('La modifica è stata rifiutata', 'The change was rejected')], 1, L('Si risparmiano banda e tempo.', 'Bandwidth and time are saved.')),
            Q('ch2', L('Quale intestazione per una risposta con dati personali da non salvare mai?', 'Which header for a response with personal data never to be stored?'), ['Cache-Control: public, max-age=3600', 'ETag: "v1"', 'Cache-Control: no-store', 'Retry-After: 60'], 2, L('Né browser né CDN la conservano.', 'Neither browsers nor CDNs keep it.')),
            Q('ch3', L('Un PUT con `If-Match: "v7"` arriva quando la risorsa è già a `"v8"`. Che cosa succede?', 'A PUT with `If-Match: "v7"` arrives when the resource is already at `"v8"`. What happens?'), [L('Il server sovrascrive comunque la versione nuova', 'The server overwrites the new version anyway'), L('Il server crea una seconda copia della risorsa', 'The server creates a second copy of the resource'), L('Il server risponde 304 Not Modified', 'The server answers 304 Not Modified'), L('Il server risponde 412 Precondition Failed', 'The server answers 412 Precondition Failed')], 3, L('Così non cancelli il lavoro di un altro.', 'That way you do not wipe someone else’s work.')),
        ], task('text', L('Scrivi la richiesta che modifica il corso 42 solo se è ancora alla versione `"v3"`: prima riga `PUT /corsi/42`, seconda riga l’intestazione giusta.', 'Write the request that changes course 42 only if it is still at version `"v3"`: first line `PUT /corsi/42`, second line the right header.'), '', [re('^PUT\s+/corsi/42\s*$', L('Metodo e percorso', 'Method and path')), re('^If-Match:\s*"v3"\s*$', L('La condizione sulla versione', 'The version condition'))], '', "PUT /corsi/42\nIf-Match: \"v3\""), [
            dMatch(pMatch(), [['max-age', L('Per quanti secondi riusare', 'For how many seconds to reuse')], ['no-store', L('Mai salvare', 'Never store')], ['ETag', L('Impronta della versione', 'Fingerprint of the version')], ['304', L('La tua copia va bene', 'Your copy is fine')], ['412', L('Versione cambiata nel frattempo', 'Version changed in the meantime')]], L('Risparmiare e proteggere.', 'Saving and protecting.')),
            dFlash(pFlash(), [[L('Una risposta 304 contiene tutto il corpo', 'A 304 response contains the whole body'), false], [L('If-None-Match serve per i GET condizionali', 'If-None-Match is for conditional GETs'), true], [L('L’ETag aiuta a evitare aggiornamenti persi', 'The ETag helps avoid lost updates'), true], [L('I dati personali vanno nella cache pubblica', 'Personal data belongs in the public cache'), false]], L('Cache con criterio.', 'Caching wisely.')),
        ]),
        lesson('idempotency', L('Idempotenza, tentativi e operazioni lunghe', 'Idempotency, retries and long operations'), [
            P('Le reti sbagliano: un client che non riceve risposta riprova. Con GET, PUT e DELETE non è un problema; con un POST (un pagamento, un ordine) il secondo tentativo potrebbe addebitare due volte. La soluzione è una **chiave di idempotenza**: il client genera un identificativo unico per ogni operazione e lo manda in `Idempotency-Key`; il server ricorda il risultato e, se la chiave ritorna, restituisce la stessa risposta senza rifare nulla.', 'Networks fail: a client that gets no answer tries again. With GET, PUT and DELETE that is not a problem; with a POST (a payment, an order) the second attempt might charge twice. The solution is an **idempotency key**: the client generates a unique identifier for each operation and sends it in `Idempotency-Key`; the server remembers the result and, if the key comes back, returns the same response without redoing anything.'),
            Fig(L(fig_api_seq(['Client', 'Server'], [80, 440], [[0, 1, 'POST /pagamenti · Idempotency-Key: k-81f', ''], [1, 1, 'addebita 20 € · salva k-81f', 'noteL'], [1, 0, '201 Created · persa', 'lost'], [0, 0, 'timeout: riprova', 'note'], [0, 1, 'POST /pagamenti · Idempotency-Key: k-81f', ''], [1, 1, 'chiave già vista: nessun addebito', 'noteL'], [1, 0, '201 Created · la stessa risposta', 'ok']]), fig_api_seq(['Client', 'Server'], [80, 440], [[0, 1, 'POST /pagamenti · Idempotency-Key: k-81f', ''], [1, 1, 'charges €20 · stores k-81f', 'noteL'], [1, 0, '201 Created · lost', 'lost'], [0, 0, 'timeout: retry', 'note'], [0, 1, 'POST /pagamenti · Idempotency-Key: k-81f', ''], [1, 1, 'key already seen: no charge', 'noteL'], [1, 0, '201 Created · the same response', 'ok']])),
                L('Un diagramma di sequenza. Il client manda POST /pagamenti con la chiave k-81f; il server addebita 20 euro e salva la chiave, ma la risposta 201 si perde. Il client va in timeout e riprova con la stessa chiave; il server la riconosce, non addebita di nuovo e restituisce la stessa risposta 201.', 'A sequence diagram. The client sends POST /pagamenti with the key k-81f; the server charges 20 euros and stores the key, but the 201 response is lost. The client times out and retries with the same key; the server recognises it, does not charge again and returns the same 201 response.'),
                L('Due tentativi, un solo addebito: la chiave rende sicuro riprovare.', 'Two attempts, one charge: the key makes retrying safe.')),
            C('POST /pagamenti
Idempotency-Key: 5f1c8e2a-7b3d-4c9e-9a11-2d6f0b7e4c21
Content-Type: application/json

{ "ordine": 42, "importo": 1990 }

POST /esportazioni
202 Accepted
Location: /esportazioni/exp_91

GET /esportazioni/exp_91
200 OK
{ "stato": "in_corso", "percentuale": 40 }', 'http'),
            P('Riprova con un’**attesa esponenziale** con un po’ di casualità (1 s, 2 s, 4 s…) e solo per gli errori che hanno senso (timeout, 429, 503), rispettando `Retry-After`. Per i lavori lunghi rispondi `202 Accepted` con l’indirizzo dove seguire lo stato, invece di tenere aperta la connessione per minuti.', 'Retry with **exponential backoff** plus some randomness (1 s, 2 s, 4 s…) and only for errors where it makes sense (timeouts, 429, 503), honouring `Retry-After`. For long jobs answer `202 Accepted` with the address where to follow the status, instead of keeping the connection open for minutes.'),
        ], [
            Q('ip1', L('Perché mandare una `Idempotency-Key` con il POST di un pagamento?', 'Why send an `Idempotency-Key` with a payment POST?'), [L('Per cifrare l’importo del pagamento', 'To encrypt the payment amount'), L('Per ottenere una risposta più veloce', 'To get a faster response'), L('Per non addebitare due volte se la richiesta viene ripetuta', 'To avoid charging twice if the request is repeated'), L('Per saltare l’autenticazione', 'To skip authentication')], 2, L('Stessa chiave, stesso risultato.', 'Same key, same result.')),
            Q('ip2', L('Per quali errori ha senso riprovare in automatico?', 'For which errors does an automatic retry make sense?'), [L('400 Bad Request e 422', '400 Bad Request and 422'), L('401 Unauthorized e 403 Forbidden', '401 Unauthorized and 403 Forbidden'), L('404 Not Found', '404 Not Found'), L('Timeout, 429 e 503', 'Timeouts, 429 and 503')], 3, L('Gli errori del client non cambiano riprovando.', 'Client errors do not change by retrying.')),
            Q('ip3', L('Che cosa significa `202 Accepted`?', 'What does `202 Accepted` mean?'), [L('La risorsa è stata creata subito', 'The resource was created at once'), L('La richiesta è accettata e verrà completata più tardi', 'The request is accepted and will be completed later'), L('La richiesta è stata rifiutata', 'The request was refused'), L('Il client deve prima autenticarsi', 'The client must authenticate first')], 1, L('Con un indirizzo dove seguire lo stato.', 'With an address to follow the status.')),
        ], task('text', L('Scrivi la richiesta per creare un ordine in modo sicuro da ripetere: prima riga `POST /ordini`, poi le intestazioni `Idempotency-Key` (con un valore qualsiasi) e `Content-Type: application/json`.', 'Write the request to create an order in a way that is safe to repeat: first line `POST /ordini`, then the `Idempotency-Key` header (with any value) and `Content-Type: application/json`.'), '', [re('^POST\s+/ordini\s*$', L('Metodo e percorso', 'Method and path')), re('^Idempotency-Key:\s*\S+', L('La chiave di idempotenza', 'The idempotency key')), re('^Content-Type:\s*application/json', L('Il tipo di contenuto', 'The content type'))], '', "POST /ordini\nIdempotency-Key: 9b2e4f70-1c3a-4d5e-8f60-7a8b9c0d1e2f\nContent-Type: application/json"), [
            dMatch(pMatch(), [['Idempotency-Key', L('Stessa operazione, stesso risultato', 'Same operation, same result')], [L('Attesa esponenziale', 'Exponential backoff'), L('Attese sempre più lunghe', 'Ever longer waits')], ['Jitter', L('Un po’ di casualità nelle attese', 'Some randomness in the waits')], ['202', L('Accettato, finirà dopo', 'Accepted, will finish later')], ['Retry-After', L('Quando riprovare', 'When to retry')]], L('Riprovare senza danni.', 'Retrying without harm.')),
            dOrder(pSort(), ['1 s', '2 s', '4 s', '8 s'], L('Ogni attesa raddoppia la precedente.', 'Each wait doubles the previous one.')),
        ]),
        lesson('auth', L('OAuth 2, token e JWT', 'OAuth 2, tokens and JWT'), [
            P('Una **chiave API** identifica un’applicazione ed è semplice, ma dura a lungo e chi la possiede la usa: va tenuta segreta sul server. **OAuth 2.0** rilascia **access token** di breve durata con degli **scope**, cioè ciò che il token permette. Il flusso **authorization code con PKCE** serve alle app in cui un utente accede (web, mobile, single page); **client credentials** ai servizi che parlano tra loro. I vecchi flussi implicit e password sono sconsigliati dalle buone pratiche di sicurezza (RFC 9700).', 'An **API key** identifies an application and is simple, but it lasts long and whoever holds it can use it: it must be kept secret on the server. **OAuth 2.0** issues short-lived **access tokens** with **scopes**, that is what the token allows. The **authorization code with PKCE** flow is for apps where a user signs in (web, mobile, single page); **client credentials** for services talking to each other. The old implicit and password flows are discouraged by security best practice (RFC 9700).'),
            Fig(L(fig_api_seq(['Utente', 'App', 'Server auth', 'API'], [76, 236, 404, 566], [[0, 1, 'Accedi', ''], [1, 2, 'redirect + code_challenge', ''], [0, 2, 'login e consenso agli scope', ''], [2, 1, 'code (con un redirect)', 'back'], [1, 2, 'code + code_verifier', ''], [2, 1, 'access token + refresh', 'ok'], [1, 3, 'Authorization: Bearer …', ''], [3, 1, '200 · i dati permessi', 'back']], 34), fig_api_seq(['User', 'App', 'Auth server', 'API'], [76, 236, 404, 566], [[0, 1, 'Sign in', ''], [1, 2, 'redirect + code_challenge', ''], [0, 2, 'login and consent to scopes', ''], [2, 1, 'code (via a redirect)', 'back'], [1, 2, 'code + code_verifier', ''], [2, 1, 'access token + refresh', 'ok'], [1, 3, 'Authorization: Bearer …', ''], [3, 1, '200 · the allowed data', 'back']], 34)),
                L('Un diagramma di sequenza con utente, app, server di autorizzazione e API. L’utente chiede di accedere; l’app lo rimanda al server di autorizzazione con il code_challenge; l’utente fa il login e dà il consenso; il server restituisce un code all’app; l’app lo scambia insieme al code_verifier con un access token e un refresh token; infine chiama l’API con Authorization: Bearer e riceve i dati.', 'A sequence diagram with user, app, authorisation server and API. The user asks to sign in; the app sends them to the authorisation server with the code_challenge; the user logs in and consents; the server returns a code to the app; the app exchanges it, together with the code_verifier, for an access token and a refresh token; finally it calls the API with Authorization: Bearer and gets the data.'),
                L('La password resta tra l’utente e il server di autorizzazione: l’app vede solo il token.', 'The password stays between the user and the authorisation server: the app only sees the token.')),
            C('POST /oauth/token
Authorization: Basic Y2xpZW50LWlkOmNsaWVudC1zZWdyZXRv
Content-Type: application/x-www-form-urlencoded

grant_type=client_credentials&scope=ordini:leggi

200 OK
{ "access_token": "eyJhbGciOi…", "token_type": "Bearer", "expires_in": 900, "scope": "ordini:leggi" }', 'http'),
            P('Gli access token sono spesso **JWT**: firmati ma **leggibili da chiunque** (base64, non cifrati), quindi niente segreti dentro. Il server deve verificare la firma con l’algoritmo atteso (mai accettare `none`), la scadenza `exp`, chi l’ha emesso `iss` e a chi è destinato `aud`. Token brevi, rinnovati con un refresh token.', 'Access tokens are often **JWTs**: signed but **readable by anyone** (base64, not encrypted), so no secrets inside. The server must verify the signature with the expected algorithm (never accept `none`), the expiry `exp`, who issued it `iss` and whom it is for `aud`. Short tokens, renewed with a refresh token.'),
        ], [
            Q('oa1', L('Quale flusso OAuth per un’app mobile in cui l’utente accede?', 'Which OAuth flow for a mobile app where the user signs in?'), ['Client credentials', 'Implicit', 'Authorization code + PKCE', 'Password grant'], 2, L('PKCE protegge il codice anche senza un segreto nell’app.', 'PKCE protects the code even without a secret in the app.')),
            Q('oa2', L('Il contenuto di un JWT è segreto?', 'Is a JWT’s content secret?'), [L('Sì, è cifrato con la chiave del server', 'Yes, it is encrypted with the server’s key'), L('No: è solo firmato, chiunque può leggerlo', 'No: it is only signed, anyone can read it'), L('Sì, se dura meno di un’ora', 'Yes, if it lasts less than an hour'), L('Solo il campo exp si può leggere', 'Only the exp field can be read')], 1, L('La firma prova che nessuno l’ha modificato.', 'The signature proves nobody changed it.')),
            Q('oa3', L('Che cosa esprime uno scope come `ordini:leggi`?', 'What does a scope like `ordini:leggi` express?'), [L('Il server che ha emesso il token', 'The server that issued the token'), L('La durata del token in secondi', 'The token’s lifetime in seconds'), L('Il nome dell’utente collegato', 'The signed-in user’s name'), L('Che cosa il token permette di fare', 'What the token allows you to do')], 3, L('Il server controlla lo scope a ogni richiesta.', 'The server checks the scope on every request.')),
        ], task('text', L('Scrivi la richiesta di un token con il flusso client credentials e lo scope `prodotti:leggi`: prima riga `POST /oauth/token`, poi il corpo.', 'Write a token request with the client credentials flow and the `prodotti:leggi` scope: first line `POST /oauth/token`, then the body.'), '', [re('^POST\s+/oauth/token\s*$', L('L’indirizzo dei token', 'The token endpoint')), re('grant_type=client_credentials', L('Il flusso', 'The flow')), re('scope=prodotti(:|%3A)leggi', L('Lo scope', 'The scope'))], '', "POST /oauth/token\nContent-Type: application/x-www-form-urlencoded\n\ngrant_type=client_credentials&scope=prodotti:leggi"), [
            dMatch(pMatch(), [['Access token', L('Accesso di breve durata', 'Short-lived access')], ['Refresh token', L('Ottiene nuovi access token', 'Gets new access tokens')], ['Scope', L('Che cosa è permesso', 'What is allowed')], ['PKCE', L('Protegge il codice di autorizzazione', 'Protects the authorisation code')], ['Client credentials', L('Da servizio a servizio', 'Service to service')]], L('Il vocabolario di OAuth.', 'OAuth vocabulary.')),
            dFlash(pFlash(), [[L('Il server deve verificare firma e scadenza del JWT', 'The server must verify the JWT’s signature and expiry'), true], [L('Un JWT con algoritmo none va accettato', 'A JWT with the none algorithm should be accepted'), false], [L('Il flusso implicit è consigliato per le single page app', 'The implicit flow is recommended for single page apps'), false], [L('Una chiave API dentro un’app mobile resta segreta', 'An API key inside a mobile app stays secret'), false]], L('Token con giudizio.', 'Tokens with judgement.')),
        ]),
        lesson('api-security', L('Sicurezza delle API', 'API security'), [
            P('OWASP ha una lista dedicata alle API (edizione 2023). Al primo posto c’è la **BOLA** (autorizzazione rotta a livello di oggetto): `GET /ordini/42` restituisce l’ordine 42 anche se è di un altro. Controlla la proprietà di ogni oggetto, a ogni richiesta. Al terzo posto i **campi**: restituirne troppi (l’hash della password) o accettarne troppi (`"ruolo": "admin"` in un PATCH, il **mass assignment**). Usa elenchi espliciti di campi permessi, in ingresso e in uscita.', 'OWASP has a list dedicated to APIs (2023 edition). In first place is **BOLA** (broken object level authorisation): `GET /ordini/42` returns order 42 even if it belongs to someone else. Check ownership of every object, on every request. In third place come **fields**: returning too many (the password hash) or accepting too many (`"ruolo": "admin"` in a PATCH, **mass assignment**). Use explicit lists of allowed fields, in input and output.'),
            C('app.patch("/utenti/:id", autentica, async (req, res) => {
  if (req.utente.id !== Number(req.params.id)) return res.status(403).end();  // BOLA
  const { nome, bio } = req.body;                 // solo i campi permessi, niente "ruolo"
  const utente = await db.utenti.aggiorna(req.params.id, { nome, bio });
  res.json({ id: utente.id, nome: utente.nome, bio: utente.bio });   // niente hash
});', 'js'),
            P('Il **CORS** non protegge la tua API: dice solo ai browser quali altre origini possono leggere le risposte, e strumenti come curl lo ignorano. Non riflettere mai qualsiasi `Origin` insieme a `Access-Control-Allow-Credentials: true`. E limita il consumo di risorse: dimensione massima delle pagine e dei corpi, rate limiting.', '**CORS** does not protect your API: it only tells browsers which other origins may read the responses, and tools like curl ignore it. Never reflect any `Origin` together with `Access-Control-Allow-Credentials: true`. And limit resource consumption: maximum page and body sizes, rate limiting.'),
        ], [
            Q('as1', L('Che cos’è la BOLA?', 'What is BOLA?'), [L('Un attacco che rallenta il server con troppe richieste', 'An attack slowing the server with too many requests'), L('Leggere o cambiare oggetti di altri cambiando l’id nella richiesta', 'Reading or changing others’ objects by changing the id in the request'), L('Un errore nel formato JSON delle risposte', 'An error in the responses’ JSON format'), L('Una password debole dell’amministratore', 'A weak administrator password')], 1, L('Il primo rischio della lista API di OWASP.', 'The top risk on OWASP’s API list.')),
            Q('as2', L('Un `PATCH /utenti/7` con il corpo `{"ruolo": "admin"}` promuove l’utente. Qual è il problema?', 'A `PATCH /utenti/7` with the body `{"ruolo": "admin"}` promotes the user. What is the problem?'), [L('Una SQL injection nella query', 'An SQL injection in the query'), L('Un cross-site scripting nella pagina', 'Cross-site scripting in the page'), L('Mass assignment: il server accetta campi che non dovrebbe', 'Mass assignment: the server accepts fields it should not'), L('Un attacco CSRF da un altro sito', 'A CSRF attack from another site')], 2, L('Accetta solo i campi che l’utente può cambiare.', 'Accept only the fields the user may change.')),
            Q('as3', L('Il CORS protegge la tua API da chi usa curl?', 'Does CORS protect your API from someone using curl?'), [L('Sì, blocca tutte le richieste da altre origini', 'Yes, it blocks every request from other origins'), L('Sì, se aggiungi Allow-Credentials', 'Yes, if you add Allow-Credentials'), L('Solo per le richieste POST', 'Only for POST requests'), L('No: lo applicano solo i browser, gli altri client lo ignorano', 'No: only browsers enforce it, other clients ignore it')], 3, L('La protezione vera è l’autorizzazione sul server.', 'The real protection is server-side authorisation.')),
        ], task('js', L('Correggi il gestore: prendi da `req.body` solo `nome` ed `email` con la destrutturazione e passa `{ nome, email }` ad `aggiorna`.', 'Fix the handler: take only `nome` and `email` from `req.body` with destructuring and pass `{ nome, email }` to `aggiorna`.'), "app.patch(\"/profilo\", autentica, async (req, res) => {\n  const dati = req.body; // pericoloso: accetta qualsiasi campo\n  await db.utenti.aggiorna(req.utente.id, dati);\n  res.status(204).end();\n});", [re('const\s*\{\s*(nome\s*,\s*email|email\s*,\s*nome)\s*\}\s*=\s*req\.body', L('Solo i campi permessi', 'Only the allowed fields')), re('aggiorna\s*\(\s*req\.utente\.id\s*,\s*\{\s*(nome\s*,\s*email|email\s*,\s*nome)\s*\}\s*\)', L('Passi solo quei campi', 'You pass only those fields'))], '', "app.patch(\"/profilo\", autentica, async (req, res) => {\n  const { nome, email } = req.body;\n  await db.utenti.aggiorna(req.utente.id, { nome, email });\n  res.status(204).end();\n});"), [
            dMatch(pMatch(), [['BOLA', L('Oggetti di altri tramite l’id', 'Others’ objects via the id')], ['Mass assignment', L('Campi non permessi accettati', 'Disallowed fields accepted')], ['CORS', L('Regole per i browser', 'Rules for browsers')], [L('Elenco di campi permessi', 'List of allowed fields'), L('Difesa dal mass assignment', 'Defence against mass assignment')], [L('Limite di pagina', 'Page limit'), L('Contro il consumo di risorse', 'Against resource exhaustion')]], L('I rischi delle API.', 'API risks.')),
            dBug(pBug(), ['app.get("/ordini/:id", autentica, async (req, res) => {', '  const ordine = await db.ordini.trova(req.params.id);', '  res.json(ordine);', '});'], 1, '  const ordine = await db.ordini.trova(req.params.id, { utenteId: req.utente.id });', L('Senza il filtro sull’utente chiunque legge gli ordini degli altri: è la BOLA.', 'Without the user filter anyone reads others’ orders: that is BOLA.')),
        ]),
    ],
];
