<?php
/** Redis: keys with expiry, data structures, caching, pub/sub and streams, persistence, transactions and Lua, JSON and search, licences and Valkey. */

/** The five classic Redis structures, each with the key that holds it. */
if (!function_exists('fig_redis_structures')) {
    function fig_redis_structures(string $str, string $list): string
    {
        $panel = fn(int $x, int $y, int $w, int $h, string $type, string $key, string $s, string $f, bool $below = false) => '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="' . $h . '" rx="12" fill="' . $f . '" stroke="' . $s . '" stroke-width="2"/><text x="' . ($x + 14) . '" y="' . ($y + 24) . '" font-weight="700">' . $type . '</text><text x="' . ($below ? $x + 14 : $x + $w - 14) . '" y="' . ($y + ($below ? 42 : 24)) . '"' . ($below ? '' : ' text-anchor="end"') . ' font-family="ui-monospace, monospace" font-size="12" fill="#5A5880">' . $key . '</text>';
        $box = fn(int $x, int $y, int $w, string $t, string $s = '#8A87B0') => '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="28" rx="7" fill="#FFFFFF" stroke="' . $s . '" stroke-width="2"/><text x="' . ($x + $w / 2) . '" y="' . ($y + 19) . '" text-anchor="middle" font-size="13">' . $t . '</text>';
        $g = $panel(20, 14, 190, 104, $str, 'utente:7:nome', '#8A87B0', '#F1F0F8') . $box(40, 60, 150, 'Ada');
        $g .= $panel(226, 14, 394, 104, $list, 'notizie', '#2F8CF0', '#DCEBFF');
        foreach (['Titolo 3', 'Titolo 2', 'Titolo 1'] as $i => $t) {
            $g .= $box(300 + $i * 104, 60, 90, $t, '#2F8CF0');
            if ($i < 2) $g .= '<path d="M' . (390 + $i * 104) . ' 74 H' . (404 + $i * 104) . '" stroke="#2F8CF0" stroke-width="2"/>';
        }
        $g .= '<text x="244" y="79" font-family="ui-monospace, monospace" font-size="12" font-weight="700" fill="#1B66BC">LPUSH</text><path d="M288 74 H296" stroke="#1B66BC" stroke-width="2"/><path d="M294 69 L301 74 L294 79 Z" fill="#1B66BC"/>';
        $g .= $panel(20, 134, 196, 152, 'hash', 'utente:7', '#0B7F5C', '#D5F6EA', true);
        foreach ([['nome', 'Ada'], ['citta', 'Trieste'], ['punti', '120']] as $i => [$k, $v]) {
            $y = 206 + $i * 30;
            $g .= '<rect x="34" y="' . ($y - 20) . '" width="168" height="28" rx="7" fill="#FFFFFF" stroke="#0B7F5C" stroke-width="2"/><line x1="100" y1="' . ($y - 20) . '" x2="100" y2="' . ($y + 8) . '" stroke="#0B7F5C" stroke-width="2"/><text x="44" y="' . $y . '" font-family="ui-monospace, monospace" font-size="12" font-weight="700">' . $k . '</text><text x="110" y="' . $y . '" font-size="13">' . $v . '</text>';
        }
        $g .= $panel(232, 134, 180, 152, 'set', 'tag:post:5', '#7048E8', '#ECE6FF', true);
        $g .= '<ellipse cx="322" cy="228" rx="74" ry="42" fill="#FFFFFF" stroke="#7048E8" stroke-width="2" stroke-dasharray="6 4"/><text x="300" y="220" text-anchor="middle" font-size="13">redis</text><text x="338" y="248" text-anchor="middle" font-size="13">database</text>';
        $g .= $panel(428, 134, 192, 152, 'sorted set', 'classifica', '#E8590C', '#FFF3EA', true);
        foreach ([['cy', 140], ['ada', 120], ['bo', 95]] as $i => [$k, $v]) {
            $y = 206 + $i * 30;
            $w = (int)round($v * 0.72);
            $g .= '<rect x="482" y="' . ($y - 16) . '" width="' . $w . '" height="22" rx="6" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="442" y="' . $y . '" font-size="13" font-weight="700">' . $k . '</text><text x="' . (488) . '" y="' . $y . '" font-size="12">' . $v . '</text>';
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 300" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">' . $g . '</svg>';
    }
}

/** Cache-aside as a sequence: the first request misses and fills the cache, the next one is a hit. */
if (!function_exists('fig_redis_cache')) {
    function fig_redis_cache(string $miss, string $slow, string $data, string $next, string $hit): string
    {
        $msg = function (int $x1, int $x2, int $y, string $t, string $c, bool $back = false, ?int $lx = null) {
            $dir = $x2 > $x1 ? 1 : -1;
            return '<path d="M' . $x1 . ' ' . $y . ' H' . ($x2 - $dir * 8) . '" stroke="' . $c . '" stroke-width="2"' . ($back ? ' stroke-dasharray="6 4"' : '') . '/><path d="M' . ($x2 - $dir * 8) . ' ' . ($y - 5) . ' L' . $x2 . ' ' . $y . ' L' . ($x2 - $dir * 8) . ' ' . ($y + 5) . ' Z" fill="' . $c . '"/><text x="' . ($lx ?? ($x1 + $x2) / 2) . '" y="' . ($y - 7) . '" text-anchor="middle" font-size="13"' . ($back ? '' : ' font-family="ui-monospace, monospace"') . '>' . $t . '</text>';
        };
        $g = $msg(90, 320, 86, 'GET prodotto:7', '#1E1C52') . $msg(320, 90, 116, $miss, '#8A87B0', true)
            . $msg(90, 550, 150, $slow, '#1E1C52', false, 435) . $msg(550, 90, 180, $data, '#8A87B0', true, 435)
            . $msg(90, 320, 214, 'SET prodotto:7 … EX 300', '#1E1C52')
            . $msg(90, 320, 270, 'GET prodotto:7', '#0B7F5C') . $msg(320, 90, 298, $hit, '#0B7F5C', true);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 312" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<g stroke="#C9C7DE" stroke-width="2" stroke-dasharray="3 4"><line x1="90" y1="50" x2="90" y2="306"/><line x1="320" y1="50" x2="320" y2="306"/><line x1="550" y1="50" x2="550" y2="306"/></g>
<rect x="40" y="14" width="100" height="36" rx="10" fill="#F1F0F8" stroke="#8A87B0" stroke-width="2"/><text x="90" y="37" text-anchor="middle" font-weight="700">app</text>
<rect x="270" y="14" width="100" height="36" rx="10" fill="#FFE3EE" stroke="#F03E82" stroke-width="2"/><text x="320" y="37" text-anchor="middle" font-weight="700" fill="#C2185B">Redis</text>
<rect x="490" y="14" width="120" height="36" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="550" y="37" text-anchor="middle" font-weight="700" fill="#1B66BC">database</text>
<line x1="20" y1="236" x2="620" y2="236" stroke="#8A87B0" stroke-width="2" stroke-dasharray="8 6"/>
<rect x="400" y="226" width="200" height="20" fill="#FFFFFF"/><text x="500" y="241" text-anchor="middle" font-size="12" font-weight="700" fill="#5A5880">{$next}</text>
{$g}
</svg>
SVG;
    }
}

/** Pub/Sub delivers only to whoever is listening now; a stream keeps the messages so a consumer can read them later. */
if (!function_exists('fig_redis_pubsub')) {
    function fig_redis_pubsub(string $offline, string $later, string $lost, string $kept): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="14" fill="#1E1C52">
<rect x="20" y="14" width="290" height="210" rx="12" fill="#FFFFFF" stroke="#C9C7DE" stroke-width="2"/>
<rect x="330" y="14" width="290" height="210" rx="12" fill="#FFFFFF" stroke="#C9C7DE" stroke-width="2"/>
<g font-weight="700"><text x="34" y="40">Pub/Sub</text><text x="344" y="40">Stream</text></g>
<g font-family="ui-monospace, monospace" font-size="12" font-weight="700">
<rect x="34" y="104" width="90" height="34" rx="9" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="79" y="126" text-anchor="middle">PUBLISH</text>
<rect x="164" y="56" width="130" height="32" rx="9" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="229" y="77" text-anchor="middle">client A</text>
<rect x="164" y="104" width="130" height="32" rx="9" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="229" y="125" text-anchor="middle">client B</text>
<rect x="164" y="152" width="130" height="32" rx="9" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2" stroke-dasharray="5 4"/><text x="229" y="173" text-anchor="middle" fill="#8A87B0">client C</text>
</g>
<g stroke="#0B7F5C" stroke-width="2" fill="none"><path d="M124 121 C 144 121, 144 72, 158 72"/><path d="M124 121 H158"/></g>
<g fill="#0B7F5C"><path d="M156 67 L164 72 L156 77 Z"/><path d="M156 116 L164 121 L156 126 Z"/></g>
<path d="M124 121 C 140 121, 140 168, 150 168" fill="none" stroke="#8A87B0" stroke-width="2" stroke-dasharray="4 4"/>
<path d="M140 160 l10 10 M150 160 l-10 10" stroke="#F03E82" stroke-width="2.5" stroke-linecap="round"/>
<text x="229" y="204" text-anchor="middle" font-size="12" fill="#5A5880">{$offline}</text>
<g font-family="ui-monospace, monospace" font-size="12" font-weight="700">
<rect x="344" y="104" width="62" height="34" rx="9" fill="#FFE8D6" stroke="#E8590C" stroke-width="2"/><text x="375" y="126" text-anchor="middle">XADD</text>
<rect x="420" y="98" width="186" height="46" rx="10" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/>
<rect x="430" y="106" width="50" height="30" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="455" y="126" text-anchor="middle">1-0</text>
<rect x="488" y="106" width="50" height="30" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="513" y="126" text-anchor="middle">2-0</text>
<rect x="546" y="106" width="50" height="30" rx="6" fill="#FFFFFF" stroke="#2F8CF0" stroke-width="2"/><text x="571" y="126" text-anchor="middle">3-0</text>
<rect x="430" y="168" width="166" height="32" rx="9" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2"/><text x="513" y="189" text-anchor="middle">XREAD … 0</text>
</g>
<path d="M406 121 H414" stroke="#E8590C" stroke-width="2"/>
<path d="M455 136 V162" stroke="#0B7F5C" stroke-width="2"/><path d="M450 160 L455 168 L460 160 Z" fill="#0B7F5C"/>
<text x="513" y="78" text-anchor="middle" font-size="12" fill="#5A5880">{$later}</text>
<text x="20" y="250" font-size="13" font-weight="700" fill="#C2185B">{$lost}</text>
<text x="330" y="250" font-size="13" font-weight="700" fill="#0B7F5C">{$kept}</text>
</svg>
SVG;
    }
}

return [
    'id' => 'redis', 'family' => 'redis', 'after' => null,
    'title' => L('Redis', 'Redis'), 'short' => L('Redis', 'Redis'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database in memoria velocissimo: chiavi con scadenza, liste, hash, insiemi e classifiche, cache, pub/sub e stream, persistenza, transazioni, JSON e ricerca, licenze e Valkey.', 'The lightning-fast in-memory database: keys with expiry, lists, hashes, sets and leaderboards, caching, pub/sub and streams, persistence, transactions, JSON and search, licences and Valkey.'),
    'lessons' => [
        lesson('keys', L('Chiavi, valori e scadenze', 'Keys, values and expiry'), [
            P('**Redis** tiene i dati **in memoria**: le operazioni richiedono frazioni di millisecondo. È un archivio **chiave-valore**: ogni dato ha un nome (la chiave) e un valore. Si usa per cache, sessioni, contatori, code e classifiche, accanto a un database principale.', '**Redis** keeps data **in memory**: operations take fractions of a millisecond. It is a **key-value** store: every piece of data has a name (the key) and a value. It is used for caches, sessions, counters, queues and leaderboards, alongside a main database.'),
            C('SET utente:7:nome "Ada"
GET utente:7:nome              # "Ada"
SET sessione:ab12 "dati" EX 3600   # scade tra un’ora
TTL sessione:ab12              # secondi rimanenti
INCR visite:home               # contatore atomico
DEL utente:7:nome
EXISTS utente:7:nome           # 0', 'redis'),
            P('Per convenzione le chiavi usano i due punti come separatore (`oggetto:id:campo`). `EX` imposta una **scadenza**: dopo quel tempo la chiave sparisce da sola, perfetto per cache e sessioni. `INCR` è **atomico**: anche con mille richieste insieme il conteggio resta giusto.', 'By convention keys use colons as separators (`object:id:field`). `EX` sets an **expiry**: after that time the key vanishes by itself, perfect for caches and sessions. `INCR` is **atomic**: even with a thousand simultaneous requests the count stays right.'),
        ], [
            Q('k1', L('Dove tiene i dati Redis?', 'Where does Redis keep data?'), [L('In memoria', 'In memory'), L('Solo su carta', 'Only on paper'), L('In un foglio Excel', 'In an Excel sheet'), L('Solo su disco, mai in memoria', 'Only on disk, never in memory')], 0, L('Per questo è velocissimo.', 'That is why it is so fast.')),
            Q('k2', L('Che cosa fa `EX 3600` in un SET?', 'What does `EX 3600` do in a SET?'), [L('Fa scadere la chiave dopo un’ora', 'Expires the key after an hour'), L('Esporta i dati', 'Exports the data'), L('Crea 3600 copie', 'Creates 3600 copies'), L('Cifra il valore', 'Encrypts the value')], 0, L('In secondi.', 'In seconds.')),
            Q('k3', L('Perché INCR è sicuro con tante richieste insieme?', 'Why is INCR safe with many simultaneous requests?'), [L('È un’operazione atomica', 'It is an atomic operation'), L('È lento', 'It is slow'), L('Usa un lucchetto manuale', 'It uses a manual lock'), L('Non lo è', 'It is not')], 0, L('Nessun conteggio perso.', 'No lost counts.')),
        ], task('redis', L('Salva nella chiave `token:xyz` il valore `"utente-5"` con scadenza di `900` secondi, poi controlla quanti secondi restano con `TTL`.', 'Store in key `token:xyz` the value `"utente-5"` expiring in `900` seconds, then check the remaining seconds with `TTL`.'), '', [re('SET\s+token:xyz\s+"?utente-5"?\s+EX\s+900', L('Salvi con scadenza', 'You store with expiry')), re('TTL\s+token:xyz', L('Controlli la scadenza', 'You check the expiry'))], '', "SET token:xyz \"utente-5\" EX 900\nTTL token:xyz"), [
            dMatch(pMatch(), [['SET', L('Salva', 'Stores')], ['GET', L('Legge', 'Reads')], ['EX', L('Scadenza in secondi', 'Expiry in seconds')], ['TTL', L('Tempo rimanente', 'Time left')], ['INCR', L('Contatore atomico', 'Atomic counter')]], L('I comandi di base.', 'Basic commands.')),
            dFlash(pFlash(), [[L('Una chiave con scadenza sparisce da sola', 'A key with expiry vanishes by itself'), true], [L('Redis è lento perché usa il disco', 'Redis is slow because it uses disk'), false], [L('Le chiavi usano spesso i due punti come separatore', 'Keys often use colons as separators'), true], [L('INCR può perdere conteggi', 'INCR can lose counts'), false]], L('Veloce e semplice.', 'Fast and simple.')),
        ]),
        lesson('structures', L('Strutture dati', 'Data structures'), [
            P('Oltre alle stringhe, Redis ha strutture dati pronte: **liste** (code, ultimi elementi), **hash** (oggetti con campi), **set** (insiemi senza doppioni) e **sorted set** (elementi ordinati per punteggio, perfetti per le classifiche).', 'Besides strings, Redis has ready data structures: **lists** (queues, latest items), **hashes** (objects with fields), **sets** (collections without duplicates) and **sorted sets** (items ordered by score, perfect for leaderboards).'),
            C('HSET utente:7 nome "Ada" citta "Trieste" punti 120
HGET utente:7 citta                  # "Trieste"

LPUSH notizie "Titolo 3"             # aggiunge in testa
LRANGE notizie 0 9                   # le ultime 10

SADD tag:post:5 "redis" "database"   # niente doppioni
SISMEMBER tag:post:5 "redis"         # 1

ZADD classifica 120 "ada" 95 "bo" 140 "cy"
ZRANGE classifica 0 2 REV WITHSCORES  # i primi 3: cy, ada, bo
ZINCRBY classifica 10 "bo"', 'redis'),
            Fig(L(fig_redis_structures('stringa', 'lista'), fig_redis_structures('string', 'list')),
                L('Cinque riquadri. Stringa: la chiave utente:7:nome vale Ada. Lista: notizie contiene Titolo 3, Titolo 2, Titolo 1, con LPUSH che aggiunge in testa. Hash: utente:7 ha i campi nome, citta e punti. Set: tag:post:5 contiene redis e database. Sorted set: classifica ordina cy 140, ada 120, bo 95 con barre proporzionali al punteggio.', 'Five panels. String: the key utente:7:nome holds Ada. List: notizie holds Titolo 3, Titolo 2, Titolo 1, with LPUSH adding at the head. Hash: utente:7 has the fields nome, citta and punti. Set: tag:post:5 holds redis and database. Sorted set: classifica orders cy 140, ada 120, bo 95 with bars sized by score.'),
                L('Ogni chiave contiene una struttura sola: il tipo lo decide il primo comando che la crea.', 'Each key holds a single structure: its type is set by the first command that creates it.')),
            P('Scegliere la struttura giusta rende tutto semplice: una classifica con milioni di giocatori in un sorted set restituisce i primi 10 in un istante, cosa lenta e complicata in SQL. `ZRANGE … REV` legge dal punteggio più alto; nel codice vecchio trovi ancora `ZREVRANGE`, che fa lo stesso ma è deprecato.', 'Picking the right structure makes everything simple: a leaderboard with millions of players in a sorted set returns the top 10 instantly, something slow and complicated in SQL. `ZRANGE … REV` reads from the highest score; in older code you still find `ZREVRANGE`, which does the same but is deprecated.'),
        ], [
            Q('s1', L('Quale struttura è perfetta per una classifica?', 'Which structure is perfect for a leaderboard?'), ['Sorted set', 'Lista', 'Stringa', 'Hash'], 0, L('Ordinato per punteggio.', 'Ordered by score.')),
            Q('s2', L('Che cosa garantisce un set?', 'What does a set guarantee?'), [L('Nessun doppione', 'No duplicates'), L('Ordine di inserimento', 'Insertion order'), L('Scadenza automatica', 'Automatic expiry'), L('Cifratura', 'Encryption')], 0, L('Elementi unici.', 'Unique items.')),
            Q('s3', L('A che cosa serve un hash?', 'What is a hash for?'), [L('Un oggetto con più campi', 'An object with several fields'), L('Una password cifrata', 'An encrypted password'), L('Una coda', 'A queue'), L('Un numero casuale', 'A random number')], 0, L('HSET e HGET.', 'HSET and HGET.')),
        ], task('redis', L('Aggiungi alla classifica `punteggi` il giocatore `"ada"` con 50 punti, poi ottieni i primi 5 con i punteggi, dal più alto.', 'Add to the `punteggi` leaderboard the player `"ada"` with 50 points, then get the top 5 with scores, highest first.'), '', [re('ZADD\s+punteggi\s+50\s+"?ada"?', L('Aggiungi il giocatore', 'You add the player')), re('ZRANGE\s+punteggi\s+0\s+4\s+REV\s+WITHSCORES|ZREVRANGE\s+punteggi\s+0\s+4\s+WITHSCORES', L('I primi 5', 'The top 5'))], '', "ZADD punteggi 50 \"ada\"\nZRANGE punteggi 0 4 REV WITHSCORES"), [
            dMatch(pMatch(), [[L('Lista', 'List'), L('Code e ultimi elementi', 'Queues and latest items')], ['Hash', L('Oggetto con campi', 'Object with fields')], ['Set', L('Elementi unici', 'Unique items')], ['Sorted set', L('Classifiche', 'Leaderboards')], ['String', L('Valore semplice', 'Simple value')]], L('La struttura giusta.', 'The right structure.')),
            dBlanks(pFill(), "___ utente:9 nome \"Bo\" punti 30\n___ classifica 30 \"bo\"\n___ notizie \"Nuovo articolo\"", ['HSET', 'ZADD', 'LPUSH'], ['SET', 'SADD', 'APPEND'], L('Hash, sorted set, lista.', 'Hash, sorted set, list.')),
        ]),
        lesson('cache', L('Cache e code', 'Caching and queues'), [
            P('L’uso più comune è la **cache**: prima di una query lenta, guardi in Redis; se il dato c’è lo usi (cache hit), altrimenti lo calcoli, lo salvi in Redis con una scadenza e lo restituisci (**cache-aside**). Quando i dati cambiano, cancelli o aggiorni la chiave.', 'The most common use is **caching**: before a slow query, you look in Redis; if the data is there you use it (cache hit), otherwise you compute it, store it in Redis with an expiry and return it (**cache-aside**). When data changes, you delete or update the key.'),
            C('import json, redis

r = redis.Redis()

def prodotto(id):
    chiave = f"prodotto:{id}"
    salvato = r.get(chiave)
    if salvato:
        return json.loads(salvato)              # cache hit
    dati = db.leggi_prodotto(id)               # query lenta
    r.set(chiave, json.dumps(dati), ex=300)    # 5 minuti
    return dati', 'python'),
            Fig(L(fig_redis_cache('(nil): miss', 'query lenta', 'dati', 'richiesta successiva', 'dati: hit, in un istante'), fig_redis_cache('(nil): miss', 'slow query', 'data', 'next request', 'data: hit, instantly')),
                L('Un diagramma di sequenza tra app, Redis e database. La prima volta l’app chiede GET prodotto:7 a Redis e riceve nil, cioè un miss; allora interroga il database, riceve i dati e li salva in Redis con SET … EX 300. Alla richiesta successiva lo stesso GET trova i dati in Redis: un hit.', 'A sequence diagram between app, Redis and database. The first time the app asks Redis for GET prodotto:7 and gets nil, a miss; so it queries the database, gets the data and stores them in Redis with SET … EX 300. On the next request the same GET finds the data in Redis: a hit.'),
                L('Per 300 secondi nessuno tocca più il database per quel prodotto.', 'For 300 seconds nobody touches the database for that product again.')),
            P('Redis fa anche da **coda di lavori** (con liste o con gli *stream*) e da **pub/sub**: un servizio pubblica messaggi su un canale e tutti gli iscritti li ricevono subito, utile per notifiche in tempo reale e chat.', 'Redis also works as a **job queue** (with lists or *streams*) and as **pub/sub**: a service publishes messages on a channel and all subscribers receive them at once, useful for real-time notifications and chats.'),
        ], [
            Q('c1', L('Che cosa fa il pattern cache-aside?', 'What does the cache-aside pattern do?'), [L('Cerca in cache, altrimenti calcola e salva', 'Looks in cache, otherwise computes and stores'), L('Salva tutto solo su disco', 'Stores everything on disk only'), L('Cancella il database', 'Deletes the database'), L('Non usa scadenze', 'Uses no expiry')], 0, L('Il più usato.', 'The most used.')),
            Q('c2', L('Perché dare una scadenza ai dati in cache?', 'Why give cached data an expiry?'), [L('Per non servire dati vecchi per sempre', 'So stale data is not served forever'), L('Per occupare più memoria', 'To use more memory'), L('Non serve', 'It is not needed'), L('Per renderli segreti', 'To make them secret')], 0, L('E liberare memoria.', 'And free memory.')),
            Q('c3', L('A che cosa serve il pub/sub?', 'What is pub/sub for?'), [L('Inviare messaggi in tempo reale agli iscritti', 'Sending real-time messages to subscribers'), L('Pubblicare libri', 'Publishing books'), L('Fare backup', 'Making backups'), L('Comprimere dati', 'Compressing data')], 0, L('Notifiche e chat.', 'Notifications and chats.')),
        ], task('python', L('Completa la cache: se `r.get(chiave)` restituisce un valore, restituiscilo con `json.loads`; altrimenti salva `dati` con `r.set(chiave, json.dumps(dati), ex=60)`.', 'Complete the cache: if `r.get(chiave)` returns a value, return it with `json.loads`; otherwise store `dati` with `r.set(chiave, json.dumps(dati), ex=60)`.'), "salvato = r.get(chiave)\n", [re('if\s+salvato\s*:\s*\n\s+return\s+json\.loads\s*\(\s*salvato\s*\)', L('Usi la cache', 'You use the cache')), re('r\.set\s*\(\s*chiave\s*,\s*json\.dumps\s*\(\s*dati\s*\)\s*,\s*ex\s*=\s*60\s*\)', L('Salvi con scadenza', 'You store with expiry'))], '', "if salvato:\n    return json.loads(salvato)\ndati = db.leggi(chiave)\nr.set(chiave, json.dumps(dati), ex=60)\nreturn dati"), [
            dOrder(pSort(), [L('Cerca la chiave in Redis', 'Look up the key in Redis'), L('Se c’è, restituisci il valore', 'If present, return the value'), L('Altrimenti leggi dal database', 'Otherwise read from the database'), L('Salva in Redis con scadenza', 'Store in Redis with expiry'), L('Restituisci il dato', 'Return the data')], L('Il pattern cache-aside.', 'The cache-aside pattern.')),
            dMatch(pMatch(), [['Cache hit', L('Dato trovato in cache', 'Data found in cache')], ['Cache miss', L('Dato da calcolare', 'Data to compute')], ['PUBLISH', L('Invia su un canale', 'Sends on a channel')], ['SUBSCRIBE', L('Ascolta un canale', 'Listens to a channel')], ['Stream', L('Coda di eventi', 'Event queue')]], L('Usi tipici.', 'Typical uses.')),
        ]),
        lesson('ops', L('Persistenza e buone pratiche', 'Persistence and good practice'), [
            P('Redis è in memoria, ma può salvare su disco: con gli **snapshot RDB** (fotografie periodiche) e con l’**AOF** (registro di ogni scrittura). Senza persistenza, un riavvio cancella tutto: va bene per una cache, non per dati che non puoi ricreare.', 'Redis lives in memory, but it can save to disk: with **RDB snapshots** (periodic pictures) and the **AOF** (a log of every write). Without persistence, a restart wipes everything: fine for a cache, not for data you cannot recreate.'),
            P('Buone pratiche: imposta un limite di memoria (`maxmemory`) e una politica di espulsione (per esempio togliere le chiavi usate meno di recente); non usare `KEYS *` in produzione (blocca il server: usa `SCAN`); proteggi l’accesso con utenti ACL (o almeno una password) e rete privata, mai esposto su internet.', 'Good practice: set a memory limit (`maxmemory`) and an eviction policy (for example removing the least recently used keys); do not use `KEYS *` in production (it blocks the server: use `SCAN`); protect access with ACL users (or at least a password) and a private network, never exposed to the internet.'),
            C('# redis.conf
maxmemory 512mb
maxmemory-policy allkeys-lru
appendonly yes
requirepass una-password-lunga
bind 127.0.0.1', 'text'),
        ], [
            Q('o1', L('Che cosa succede a Redis senza persistenza dopo un riavvio?', 'What happens to Redis without persistence after a restart?'), [L('Perde tutti i dati', 'It loses all data'), L('Niente', 'Nothing'), L('Raddoppia i dati', 'Doubles the data'), L('Li manda per email', 'Emails them')], 0, L('Ok per una cache.', 'Fine for a cache.')),
            Q('o2', L('Perché evitare `KEYS *` in produzione?', 'Why avoid `KEYS *` in production?'), [L('Blocca il server con molte chiavi', 'It blocks the server with many keys'), L('Cancella le chiavi', 'It deletes the keys'), L('Non esiste', 'It does not exist'), L('È a pagamento', 'It costs money')], 0, L('Usa SCAN.', 'Use SCAN.')),
            Q('o3', L('Che cosa fa `maxmemory-policy allkeys-lru`?', 'What does `maxmemory-policy allkeys-lru` do?'), [L('Toglie le chiavi usate meno di recente quando la memoria finisce', 'Removes least recently used keys when memory runs out'), L('Cifra le chiavi', 'Encrypts keys'), L('Fa un backup', 'Makes a backup'), L('Blocca le scritture', 'Blocks writes')], 0, L('Perfetto per una cache.', 'Perfect for a cache.')),
        ], task('text', L('Scrivi tre righe di configurazione Redis: limite di memoria `256mb`, politica `allkeys-lru`, ascolto solo su `127.0.0.1`.', 'Write three Redis configuration lines: memory limit `256mb`, policy `allkeys-lru`, listening only on `127.0.0.1`.'), '', [re('^maxmemory\s+256mb', L('Limite di memoria', 'Memory limit')), re('^maxmemory-policy\s+allkeys-lru', L('Politica di espulsione', 'Eviction policy')), re('^bind\s+127\.0\.0\.1', L('Solo locale', 'Local only'))], '', "maxmemory 256mb\nmaxmemory-policy allkeys-lru\nbind 127.0.0.1"), [
            dMatch(pMatch(), [['RDB', L('Snapshot periodici', 'Periodic snapshots')], ['AOF', L('Registro delle scritture', 'Write log')], ['maxmemory', L('Limite di memoria', 'Memory limit')], ['SCAN', L('Scorre le chiavi senza bloccare', 'Iterates keys without blocking')], ['requirepass', L('Password', 'Password')]], L('Redis in produzione.', 'Redis in production.')),
            dFlash(pFlash(), [[L('Redis può salvare su disco', 'Redis can save to disk'), true], [L('Conviene esporre Redis su internet senza password', 'Exposing Redis to the internet without a password is fine'), false], [L('SCAN è più sicuro di KEYS in produzione', 'SCAN is safer than KEYS in production'), true], [L('Una cache non deve mai perdere dati', 'A cache must never lose data'), false]], L('Veloce e protetto.', 'Fast and protected.')),
        ]),
        lesson('realtime', L('Pub/Sub, stream e limiti di velocità', 'Pub/Sub, streams and rate limits'), [
            P('Con **Pub/Sub** un client pubblica un messaggio su un canale e tutti gli iscritti lo ricevono subito: utile per notifiche e chat. I messaggi però non restano: chi non era collegato li perde. Gli **Stream** invece li conservano, con ID e gruppi di consumatori.', 'With **Pub/Sub** a client publishes a message to a channel and every subscriber receives it at once: useful for notifications and chat. But messages are not kept: whoever was not connected misses them. **Streams** instead keep them, with IDs and consumer groups.'),
            C('SUBSCRIBE notifiche
PUBLISH notifiche "Nuovo corso!"

XADD ordini * prodotto libro qty 1
XREAD COUNT 10 STREAMS ordini 0

INCR limite:utente42
EXPIRE limite:utente42 60     # finestra di 60 secondi', 'redis'),
            Fig(L(fig_redis_pubsub('scollegato', 'messaggi conservati con ID', 'C era offline: il messaggio è perso.', 'Chi arriva dopo legge dall’inizio.'), fig_redis_pubsub('offline', 'messages kept with IDs', 'C was offline: the message is lost.', 'Whoever comes later reads from the start.')),
                L('A sinistra un PUBLISH raggiunge i client A e B, collegati; il client C, scollegato e tratteggiato, non lo riceve. A destra XADD aggiunge messaggi a uno stream che li conserva con gli ID 1-0, 2-0 e 3-0, e un consumatore li legge dopo con XREAD partendo da 0.', 'On the left a PUBLISH reaches clients A and B, which are connected; client C, offline and dashed, does not get it. On the right XADD appends messages to a stream that keeps them with the IDs 1-0, 2-0 and 3-0, and a consumer reads them later with XREAD starting from 0.'),
                L('Pub/Sub è una radio in diretta, uno stream un registro che si può rileggere.', 'Pub/Sub is a live radio, a stream a log you can read again.')),
            P('Un **limite di velocità** semplice: `INCR` su una chiave per utente e `EXPIRE` di 60 secondi alla prima richiesta; se il contatore supera la soglia, rifiuti. Con `EXPIRE chiave 60 NX` la scadenza si imposta solo se non c’è già. Redis è veloce e atomico, perfetto per questi contatori.', 'A simple **rate limit**: `INCR` on a key per user and `EXPIRE` of 60 seconds on the first request; if the counter goes over the threshold, you refuse. With `EXPIRE key 60 NX` the expiry is set only if there is none yet. Redis is fast and atomic, perfect for these counters.'),
        ], [
            Q('rr1', L('Che cosa succede a un messaggio Pub/Sub se nessuno ascolta?', 'What happens to a Pub/Sub message if nobody listens?'), [L('Va perso', 'It is lost'), L('Resta in coda', 'It stays queued'), L('Torna al mittente', 'It goes back to the sender'), L('Finisce in uno stream', 'It goes into a stream')], 0, L('Pub/Sub non conserva.', 'Pub/Sub does not store.')),
            Q('rr2', L('Quale comando aggiunge a uno stream?', 'Which command adds to a stream?'), ['XADD', 'PUBLISH', 'LPUSH', 'SET'], 0, L('`*` genera l’ID.', '`*` generates the ID.')),
            Q('rr3', L('Come fai una finestra di 60 secondi per un contatore?', 'How do you make a 60-second window for a counter?'), ['EXPIRE chiave 60', 'TTL chiave 60', 'WAIT 60', 'SLEEP 60'], 0, L('Con `INCR` prima.', 'With `INCR` first.')),
        ], task('redis', L('Incrementa `richieste:ip1` con `INCR` e falla scadere dopo `60` secondi con `EXPIRE`.', 'Increment `richieste:ip1` with `INCR` and make it expire after `60` seconds with `EXPIRE`.'), '', [re('INCR\s+richieste:ip1', L('Incrementi il contatore', 'You increment the counter')), re('EXPIRE\s+richieste:ip1\s+60', L('Imposti la scadenza', 'You set the expiry'))], '', "INCR richieste:ip1\nEXPIRE richieste:ip1 60"), [
            dMatch(pMatch(), [['PUBLISH', L('Invia a un canale', 'Sends to a channel')], ['SUBSCRIBE', L('Ascolta un canale', 'Listens to a channel')], ['XADD', L('Aggiunge a uno stream', 'Adds to a stream')], ['INCR', L('Contatore atomico', 'Atomic counter')], ['EXPIRE', L('Scadenza', 'Expiry')]], L('Redis in tempo reale.', 'Redis in real time.')),
            dFlash(pFlash(), [[L('Gli stream conservano i messaggi', 'Streams keep messages'), true], [L('Pub/Sub ricorda i messaggi persi', 'Pub/Sub remembers missed messages'), false], [L('INCR è atomico', 'INCR is atomic'), true], [L('EXPIRE cancella subito la chiave', 'EXPIRE deletes the key at once'), false]], L('Messaggi e contatori.', 'Messages and counters.')),
        ]),
        lesson('transactions', L('Transazioni e script', 'Transactions and scripts'), [
            P('Ogni comando Redis è atomico, ma a volte servono **più comandi insieme**. `MULTI` apre una coda di comandi ed `EXEC` li esegue tutti di fila, senza che altri client si infilino in mezzo. Con **`WATCH`** la transazione si annulla se nel frattempo qualcuno ha cambiato una chiave sorvegliata: è un lock ottimistico.', 'Every Redis command is atomic, but sometimes you need **several commands together**. `MULTI` opens a queue of commands and `EXEC` runs them all in a row, with no other client slipping in between. With **`WATCH`** the transaction is aborted if someone changed a watched key in the meantime: an optimistic lock.'),
            C('WATCH saldo:ada
MULTI
DECRBY saldo:ada 50
INCRBY saldo:bo 50
EXEC          # nil se saldo:ada è cambiato dopo WATCH

EVAL "local v = redis.call(\'GET\', KEYS[1]) or 0
if tonumber(v) >= tonumber(ARGV[1]) then
  return redis.call(\'DECRBY\', KEYS[1], ARGV[1])
end
return -1" 1 saldo:ada 50', 'redis'),
            P('Attenzione: se un comando dentro `MULTI` fallisce, gli altri vengono eseguiti lo stesso, perché **non c’è rollback**. Per una logica con condizioni («togli 50 solo se il saldo basta») si usa uno **script Lua** con `EVAL` (o le *functions* di Redis 7+): lo script gira tutto intero, senza interruzioni.', 'Careful: if a command inside `MULTI` fails, the others still run, because **there is no rollback**. For logic with conditions (“take 50 only if the balance is enough”) you use a **Lua script** with `EVAL` (or Redis 7+ *functions*): the script runs as a whole, without interruptions.'),
        ], [
            Q('t1', L('Che cosa fa `EXEC` dopo `MULTI`?', 'What does `EXEC` do after `MULTI`?'), [L('Annulla tutti i comandi in coda', 'Cancels every queued command'), L('Esegue di fila tutti i comandi in coda', 'Runs every queued command in a row'), L('Salva i dati sul disco', 'Saves the data to disk'), L('Apre una nuova connessione', 'Opens a new connection')], 1, L('`DISCARD` invece svuota la coda.', '`DISCARD` empties the queue instead.')),
            Q('t2', L('Che cosa succede se una chiave sotto `WATCH` cambia prima di `EXEC`?', 'What happens if a key under `WATCH` changes before `EXEC`?'), [L('La transazione viene eseguita due volte', 'The transaction runs twice'), L('Redis si blocca finché la chiave torna com’era', 'Redis blocks until the key goes back'), L('La chiave viene cancellata', 'The key is deleted'), L('La transazione si annulla ed `EXEC` restituisce nil', 'The transaction aborts and `EXEC` returns nil')], 3, L('L’app riprova da capo.', 'The app retries from the start.')),
            Q('t3', L('Se un comando dentro `MULTI` dà errore, che cosa succede agli altri?', 'If a command inside `MULTI` errors, what happens to the others?'), [L('Vengono eseguiti lo stesso', 'They still run'), L('Vengono annullati tutti', 'They are all rolled back'), L('Vengono rimandati di un’ora', 'They are postponed by an hour'), L('Diventano uno script Lua', 'They become a Lua script')], 0, L('Redis non ha rollback.', 'Redis has no rollback.')),
        ], task('redis', L('Sposta 10 punti da `punti:ada` a `punti:bo` in una transazione: `MULTI`, `DECRBY`, `INCRBY`, `EXEC`.', 'Move 10 points from `punti:ada` to `punti:bo` in a transaction: `MULTI`, `DECRBY`, `INCRBY`, `EXEC`.'), '', [re('^\s*MULTI\s*$', L('Apri la transazione', 'You open the transaction')), re('DECRBY\s+punti:ada\s+10', L('Togli i punti ad Ada', 'You take the points from Ada')), re('INCRBY\s+punti:bo\s+10', L('Li dai a Bo', 'You give them to Bo')), re('^\s*EXEC\s*$', L('Esegui', 'You execute'))], '', "MULTI\nDECRBY punti:ada 10\nINCRBY punti:bo 10\nEXEC"), [
            dOrder(pSort(), ['WATCH stock:42', 'MULTI', 'DECR stock:42', 'RPUSH ordini "42"', 'EXEC'], L('Sorveglia, apri, metti in coda, esegui.', 'Watch, open, queue, execute.')),
            dMatch(pMatch(), [['MULTI', L('Apre la coda', 'Opens the queue')], ['EXEC', L('Esegue la coda', 'Runs the queue')], ['DISCARD', L('Svuota la coda', 'Empties the queue')], ['WATCH', L('Lock ottimistico', 'Optimistic lock')], ['EVAL', L('Esegue uno script Lua', 'Runs a Lua script')]], L('Più comandi, un solo blocco.', 'Several commands, one block.')),
        ]),
        lesson('json-search', L('JSON, ricerca e vettori', 'JSON, search and vectors'), [
            P('Da **Redis 8** le funzioni che prima stavano in Redis Stack sono incluse: documenti **JSON** con percorsi, il **Query Engine** per cercare e filtrare con indici secondari, serie temporali e strutture probabilistiche. Così Redis non è solo una cache, ma anche un piccolo motore di ricerca in memoria.', 'Since **Redis 8** the features that used to live in Redis Stack are included: **JSON** documents with paths, the **Query Engine** to search and filter with secondary indexes, time series and probabilistic structures. So Redis is not just a cache, but also a small in-memory search engine.'),
            C('JSON.SET prodotto:1 $ \'{"nome": "Tazza blu", "prezzo": 8.5}\'
JSON.GET prodotto:1 $.nome

FT.CREATE idx:prodotti ON JSON PREFIX 1 prodotto:
  SCHEMA $.nome AS nome TEXT $.prezzo AS prezzo NUMERIC

FT.SEARCH idx:prodotti "@nome:tazza @prezzo:[0 10]"', 'redis', '<pre style="margin:8px;font:13px/1.45 ui-monospace,monospace;color:#1E1C52">1) (integer) 1
2) "prodotto:1"
3) 1) "$"
   2) "{\"nome\":\"Tazza blu\",\"prezzo\":8.5}"</pre>'),
            P('`FT.CREATE` indicizza tutte le chiavi con un certo prefisso, anche quelle aggiunte dopo; `FT.SEARCH` cerca parole nei campi `TEXT` e intervalli nei campi `NUMERIC`. Lo stesso indice può avere campi `VECTOR` per la ricerca per significato, e Redis 8 ha anche i *vector set* (`VADD`, `VSIM`) dedicati agli embedding.', '`FT.CREATE` indexes every key with a given prefix, including keys added later; `FT.SEARCH` searches words in `TEXT` fields and ranges in `NUMERIC` fields. The same index can have `VECTOR` fields for search by meaning, and Redis 8 also has *vector sets* (`VADD`, `VSIM`) dedicated to embeddings.'),
        ], [
            Q('j1', L('Che cosa fa `JSON.GET prodotto:1 $.nome`?', 'What does `JSON.GET prodotto:1 $.nome` do?'), [L('Cancella il campo nome', 'Deletes the name field'), L('Crea un indice sul nome', 'Creates an index on the name'), L('Legge il campo nome del documento', 'Reads the document’s name field'), L('Rinomina la chiave', 'Renames the key')], 2, L('`$` è la radice del documento.', '`$` is the document root.')),
            Q('j2', L('Quali chiavi indicizza `FT.CREATE … PREFIX 1 prodotto:`?', 'Which keys does `FT.CREATE … PREFIX 1 prodotto:` index?'), [L('Solo le chiavi già presenti', 'Only the keys already present'), L('Tutte le chiavi che iniziano con `prodotto:`, anche le future', 'Every key starting with `prodotto:`, future ones too'), L('Solo la chiave `prodotto:1`', 'Only the key `prodotto:1`'), L('Tutte le chiavi del database', 'Every key in the database')], 1, L('L’indice si aggiorna da solo.', 'The index updates itself.')),
            Q('j3', L('Da quale versione JSON e Query Engine sono inclusi in Redis?', 'Since which version are JSON and the Query Engine included in Redis?'), ['Redis 8', 'Redis 2', 'Redis 5', L('Non sono mai inclusi', 'They are never included')], 0, L('Prima servivano i moduli di Redis Stack.', 'Before, you needed the Redis Stack modules.')),
        ], task('redis', L('Salva nella chiave `libro:1` il documento JSON `{"titolo": "Dune", "anno": 1965}` con `JSON.SET`, poi leggi `$.titolo` con `JSON.GET`.', 'Store in the key `libro:1` the JSON document `{"titolo": "Dune", "anno": 1965}` with `JSON.SET`, then read `$.titolo` with `JSON.GET`.'), '', [re('JSON\.SET\s+libro:1\s+\$\s+\'\{\s*"titolo"\s*:\s*"Dune"\s*,\s*"anno"\s*:\s*1965\s*\}\'', L('Salvi il documento', 'You store the document')), re('JSON\.GET\s+libro:1\s+\$\.titolo', L('Leggi il titolo', 'You read the title'))], '', "JSON.SET libro:1 $ '{\"titolo\": \"Dune\", \"anno\": 1965}'\nJSON.GET libro:1 $.titolo"), [
            dBlanks(pFill(), "___ utente:7 $ '{\"nome\": \"Ada\", \"eta\": 36}'\nFT.CREATE idx:utenti ON JSON PREFIX 1 utente:\n  ___ $.nome AS nome TEXT $.eta AS eta NUMERIC\n___ idx:utenti \"@eta:[30 40]\"", ['JSON.SET', 'SCHEMA', 'FT.SEARCH'], ['SET', 'FIELDS', 'FT.FIND'], L('Documento, schema dell’indice, ricerca.', 'Document, index schema, search.')),
            dFlash(pFlash(), [[L('Redis 8 include i documenti JSON', 'Redis 8 includes JSON documents'), true], [L('`FT.SEARCH` può filtrare intervalli numerici', '`FT.SEARCH` can filter numeric ranges'), true], [L('Un indice FT ignora le chiavi aggiunte dopo la sua creazione', 'An FT index ignores keys added after its creation'), false], [L('Redis non può fare ricerca vettoriale', 'Redis cannot do vector search'), false]], L('Più di una cache.', 'More than a cache.')),
        ]),
        lesson('licensing', L('Licenze, Valkey e alternative', 'Licences, Valkey and alternatives'), [
            P('Per anni Redis è stato open source con licenza BSD. Nel 2024, con la versione 7.4, l’azienda è passata a licenze più restrittive (RSAL e SSPL), che vietano ai fornitori cloud di rivenderlo come servizio. Dalla Linux Foundation è nato allora **Valkey**, un fork di Redis 7.2 che resta BSD. Con **Redis 8** (2025) è stata aggiunta come opzione la **AGPLv3**, una licenza open source riconosciuta.', 'For years Redis was open source under the BSD licence. In 2024, with version 7.4, the company moved to more restrictive licences (RSAL and SSPL), which forbid cloud providers from reselling it as a service. The Linux Foundation then launched **Valkey**, a fork of Redis 7.2 that stays BSD. With **Redis 8** (2025) the **AGPLv3**, a recognised open source licence, was added as an option.'),
            C('# stesso protocollo, stessi comandi di base
redis-cli -h cache.locale PING      # PONG
valkey-cli -h cache.locale PING     # PONG

# i client esistenti funzionano con entrambi
r = redis.Redis(host="cache.locale")', 'bash'),
            P('In pratica: Valkey e Redis parlano lo stesso protocollo e i client esistenti funzionano con tutti e due; molti servizi cloud gestiti offrono ormai Valkey. Le differenze stanno nelle funzioni più recenti (in Redis 8 JSON, ricerca e vector set sono inclusi; Valkey le offre con moduli propri) e nella licenza. Scegli in base ai comandi che ti servono e alle regole della tua azienda sulle licenze.', 'In practice: Valkey and Redis speak the same protocol and existing clients work with both; many managed cloud services now offer Valkey. The differences lie in the newest features (in Redis 8 JSON, search and vector sets are included; Valkey offers them through its own modules) and in the licence. Choose based on the commands you need and on your company’s licence rules.'),
        ], [
            Q('l1', L('Che cos’è Valkey?', 'What is Valkey?'), [L('Un client grafico per Redis', 'A graphical client for Redis'), L('Un fork open source di Redis, con licenza BSD', 'An open source fork of Redis, under BSD'), L('Il nuovo nome commerciale di Redis', 'Redis’s new commercial name'), L('Un database SQL basato su Redis', 'An SQL database based on Redis')], 1, L('Nato nel 2024 sotto la Linux Foundation.', 'Born in 2024 under the Linux Foundation.')),
            Q('l2', L('Quale licenza open source è stata aggiunta con Redis 8?', 'Which open source licence was added with Redis 8?'), ['MIT', 'GPLv2', 'Apache 2.0', 'AGPLv3'], 3, L('Accanto a RSAL e SSPL.', 'Alongside RSAL and SSPL.')),
            Q('l3', L('Un client scritto per Redis funziona con Valkey?', 'Does a client written for Redis work with Valkey?'), [L('No, serve riscrivere l’app', 'No, the app must be rewritten'), L('Solo in sola lettura', 'Only read-only'), L('Sì, il protocollo è lo stesso', 'Yes, the protocol is the same'), L('Solo dal browser', 'Only from the browser')], 2, L('Le differenze stanno nelle funzioni più nuove.', 'The differences lie in the newest features.')),
        ], task('text', L('Spiega in due righe la differenza tra Redis e Valkey: una riga che inizia con `Redis:` e una con `Valkey:`, citando la licenza di ciascuno.', 'Explain in two lines the difference between Redis and Valkey: one line starting with `Redis:` and one with `Valkey:`, naming each one’s licence.'), "Redis: \nValkey: ", [re('^Redis:\s*.*(AGPL|RSAL|SSPL)', L('La licenza di Redis', 'Redis’s licence')), re('^Valkey:\s*.*BSD', L('La licenza di Valkey', 'Valkey’s licence'))], '', "Redis: dalla versione 8 è disponibile anche con AGPLv3, oltre a RSAL e SSPL\nValkey: fork di Redis 7.2 della Linux Foundation, con licenza BSD"), [
            dMatch(pMatch(), [['BSD', L('Licenza storica di Redis, oggi di Valkey', 'Redis’s historical licence, now Valkey’s')], ['RSAL / SSPL', L('Licenze restrittive dal 2024', 'Restrictive licences since 2024')], ['AGPLv3', L('Opzione open source di Redis 8', 'Redis 8’s open source option')], ['Valkey', L('Fork della Linux Foundation', 'Linux Foundation fork')]], L('Una storia di licenze.', 'A story of licences.')),
            dFlash(pFlash(), [[L('Valkey usa lo stesso protocollo di Redis', 'Valkey uses the same protocol as Redis'), true], [L('Redis è sempre rimasto con licenza BSD', 'Redis always stayed under BSD'), false], [L('Redis 8 può essere usato con la AGPLv3', 'Redis 8 can be used under the AGPLv3'), true], [L('Valkey è nato da Redis 7.2', 'Valkey was born from Redis 7.2'), true]], L('Stessi comandi, licenze diverse.', 'Same commands, different licences.')),
        ]),
    ],
];
