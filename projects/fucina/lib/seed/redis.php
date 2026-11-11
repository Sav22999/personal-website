<?php
/** Redis: keys and strings with expiry, data structures, caching patterns, pub/sub, persistence and good practice. */

return [
    'id' => 'redis', 'family' => 'redis', 'after' => null,
    'title' => L('Redis', 'Redis'), 'short' => L('Redis', 'Redis'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Il database in memoria velocissimo: chiavi con scadenza, liste, hash, insiemi e classifiche, cache, code e pub/sub, persistenza.', 'The lightning-fast in-memory database: keys with expiry, lists, hashes, sets and leaderboards, caching, queues and pub/sub, persistence.'),
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
ZREVRANGE classifica 0 2 WITHSCORES  # i primi 3: cy, ada, bo
ZINCRBY classifica 10 "bo"', 'redis'),
            P('Scegliere la struttura giusta rende tutto semplice: una classifica con milioni di giocatori in un sorted set restituisce i primi 10 in un istante, cosa lenta e complicata in SQL.', 'Picking the right structure makes everything simple: a leaderboard with millions of players in a sorted set returns the top 10 instantly, something slow and complicated in SQL.'),
        ], [
            Q('s1', L('Quale struttura è perfetta per una classifica?', 'Which structure is perfect for a leaderboard?'), ['Sorted set', 'Lista', 'Stringa', 'Hash'], 0, L('Ordinato per punteggio.', 'Ordered by score.')),
            Q('s2', L('Che cosa garantisce un set?', 'What does a set guarantee?'), [L('Nessun doppione', 'No duplicates'), L('Ordine di inserimento', 'Insertion order'), L('Scadenza automatica', 'Automatic expiry'), L('Cifratura', 'Encryption')], 0, L('Elementi unici.', 'Unique items.')),
            Q('s3', L('A che cosa serve un hash?', 'What is a hash for?'), [L('Un oggetto con più campi', 'An object with several fields'), L('Una password cifrata', 'An encrypted password'), L('Una coda', 'A queue'), L('Un numero casuale', 'A random number')], 0, L('HSET e HGET.', 'HSET and HGET.')),
        ], task('redis', L('Aggiungi alla classifica `punteggi` il giocatore `"ada"` con 50 punti, poi ottieni i primi 5 con i punteggi, dal più alto.', 'Add to the `punteggi` leaderboard the player `"ada"` with 50 points, then get the top 5 with scores, highest first.'), '', [re('ZADD\s+punteggi\s+50\s+"?ada"?', L('Aggiungi il giocatore', 'You add the player')), re('ZREVRANGE\s+punteggi\s+0\s+4\s+WITHSCORES', L('I primi 5', 'The top 5'))], '', "ZADD punteggi 50 \"ada\"\nZREVRANGE punteggi 0 4 WITHSCORES"), [
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
            P('Buone pratiche: imposta un limite di memoria (`maxmemory`) e una politica di espulsione (per esempio togliere le chiavi usate meno di recente); non usare `KEYS *` in produzione (blocca il server: usa `SCAN`); proteggi l’accesso con password e rete privata, mai esposto su internet.', 'Good practice: set a memory limit (`maxmemory`) and an eviction policy (for example removing the least recently used keys); do not use `KEYS *` in production (it blocks the server: use `SCAN`); protect access with a password and a private network, never exposed to the internet.'),
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
    ],
];
