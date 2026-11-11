<?php
/** REST and GraphQL APIs: resources and HTTP methods, status codes and errors, versioning, pagination and auth, GraphQL. */

return [
    'id' => 'api-design', 'family' => 'api', 'after' => null,
    'title' => L('API REST e GraphQL', 'REST and GraphQL APIs'), 'short' => L('API', 'APIs'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Progettare API chiare: risorse e metodi HTTP, codici di stato ed errori, paginazione, versioni e autenticazione, e quando scegliere GraphQL.', 'Designing clear APIs: resources and HTTP methods, status codes and errors, pagination, versioning and authentication, and when to choose GraphQL.'),
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
            P('Gli errori devono avere sempre lo **stesso formato** JSON, con un codice leggibile dalla macchina e un messaggio per le persone. Non mostrare mai stack trace o dettagli interni.', 'Errors must always have the **same** JSON **format**, with a machine-readable code and a message for people. Never expose stack traces or internal details.'),
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
    ],
];
