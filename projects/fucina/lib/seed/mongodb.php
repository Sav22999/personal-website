<?php
/** MongoDB: documents and collections, find with operators, updates, aggregation, indexes and modelling, arrays, schema validation and transactions. */

return [
    'id' => 'mongodb', 'family' => 'mongodb', 'after' => null,
    'title' => L('MongoDB', 'MongoDB'), 'short' => L('MongoDB', 'MongoDB'), 'level' => L('Base', 'Basics'),
    'desc' => L('Il database NoSQL a documenti: collezioni, `find` e operatori, aggiornamenti, pipeline di aggregazione, indici e modellazione, array, validazione dello schema e transazioni.', 'The NoSQL document database: collections, `find` and operators, updates, the aggregation pipeline, indexes and modelling, arrays, schema validation and transactions.'),
    'lessons' => [
        lesson('documents', L('Documenti e collezioni', 'Documents and collections'), [
            P('MongoDB non usa tabelle ma **collezioni** di **documenti**: oggetti simili al JSON (salvati in BSON) con campi, array e oggetti annidati. Due documenti della stessa collezione possono avere campi diversi.', 'MongoDB uses no tables but **collections** of **documents**: JSON-like objects (stored as BSON) with fields, arrays and nested objects. Two documents in the same collection can have different fields.'),
            C('// mongosh
use negozio
db.prodotti.insertOne({ nome: "Tazza", prezzo: 8.5, tag: ["cucina", "regalo"] })
db.prodotti.insertMany([
  { nome: "Lampada", prezzo: 35, dimensioni: { h: 40, l: 15 } },
  { nome: "Quaderno", prezzo: 3 }
])
db.prodotti.find()', 'js'),
            P('Ogni documento riceve un campo **`_id`** unico (di solito un `ObjectId`) se non lo dai tu. Database e collezioni si creano da soli al primo inserimento.', 'Every document gets a unique **`_id`** field (usually an `ObjectId`) if you do not provide one. Databases and collections are created on the first insert.'),
            Tip('SQL → MongoDB: tabella = collezione, riga = documento, colonna = campo, JOIN = documento annidato o `$lookup`.', 'SQL → MongoDB: table = collection, row = document, column = field, JOIN = nested document or `$lookup`.'),
        ], [
            Q('d1', L('Come si chiama l’equivalente di una tabella?', 'What is the equivalent of a table called?'), [L('Collezione', 'Collection'), L('Documento', 'Document'), L('Campo', 'Field'), L('Cluster', 'Cluster')], 0, L('Una collezione contiene documenti.', 'A collection holds documents.')),
            Q('d2', L('Quale campo ha ogni documento?', 'Which field does every document have?'), ['_id', 'id', 'key', 'rowid'], 0, L('Unico nella collezione.', 'Unique in the collection.')),
            Q('d3', L('Due documenti della stessa collezione devono avere gli stessi campi?', 'Must two documents in the same collection have the same fields?'), [L('No', 'No'), L('Sì, sempre', 'Yes, always'), L('Solo se hanno lo stesso `_id`', 'Only if they share an `_id`'), L('Solo nei cluster', 'Only in clusters')], 0, L('Lo schema è flessibile (ma si può validare).', 'The schema is flexible (but can be validated).')),
        ], task('mongodb', L('Inserisci nella collezione `libri` un documento con `titolo: "Dune"` e `anno: 1965`.', 'Insert into the `libri` collection a document with `titolo: "Dune"` and `anno: 1965`.'), '', [re('db\.libri\.insertOne\s*\(\s*\{', L('Inserisci un documento in libri', 'You insert a document into libri')), re('titolo\s*:\s*["\']Dune["\']', L('C’è il titolo', 'There is the title')), re('anno\s*:\s*1965', L('C’è l’anno, come numero', 'There is the year, as a number'))], '', 'db.libri.insertOne({ titolo: "Dune", anno: 1965 })'), [
            dMatch(pMatch(), [[L('Tabella', 'Table'), L('Collezione', 'Collection')], [L('Riga', 'Row'), L('Documento', 'Document')], [L('Colonna', 'Column'), L('Campo', 'Field')], [L('Chiave primaria', 'Primary key'), '_id'], ['JOIN', '$lookup']], L('Da SQL a MongoDB.', 'From SQL to MongoDB.')),
            dFlash(pFlash(), [[L('MongoDB salva i documenti in BSON', 'MongoDB stores documents as BSON'), true], [L('Bisogna creare la collezione prima di inserire', 'You must create the collection before inserting'), false], [L('Un documento può contenere array', 'A document can contain arrays'), true], [L('`_id` può ripetersi nella collezione', '`_id` can repeat in the collection'), false]], L('Documenti flessibili.', 'Flexible documents.')),
        ]),
        lesson('find', L('Cercare con find', 'Searching with find'), [
            P('`find(filtro, proiezione)` restituisce i documenti che corrispondono al filtro. Gli **operatori** iniziano con `$`: `$gt` maggiore, `$lt` minore, `$in` in un elenco, `$ne` diverso. Più condizioni nello stesso oggetto valgono come AND.', '`find(filter, projection)` returns the documents matching the filter. **Operators** start with `$`: `$gt` greater, `$lt` less, `$in` in a list, `$ne` not equal. Several conditions in the same object act as AND.'),
            C('db.prodotti.find({ prezzo: { $lt: 10 } })
db.prodotti.find({ tag: "regalo", prezzo: { $gte: 5, $lte: 20 } })
db.prodotti.find({ $or: [{ prezzo: { $lt: 5 } }, { tag: "saldi" }] })
db.prodotti.find({ "dimensioni.h": { $gt: 30 } }, { nome: 1, _id: 0 })
db.prodotti.find().sort({ prezzo: -1 }).limit(5)', 'js'),
            P('Nei campi annidati si usa la **notazione col punto** tra virgolette (`"dimensioni.h"`). Cercare un valore in un campo array trova i documenti che lo contengono. Nella proiezione `1` include un campo, `0` lo esclude; `sort` con `-1` ordina dal più grande.', 'For nested fields you use **dot notation** in quotes (`"dimensioni.h"`). Searching for a value in an array field finds the documents that contain it. In the projection `1` includes a field, `0` excludes it; `sort` with `-1` sorts from largest.'),
        ], [
            Q('f1', L('Quale operatore significa “maggiore di”?', 'Which operator means “greater than”?'), ['$gt', '$lt', '$in', '$ne'], 0, L('greater than.', 'greater than.')),
            Q('f2', L('Come cerchi nel campo annidato `h` di `dimensioni`?', 'How do you search the nested field `h` of `dimensioni`?'), ['"dimensioni.h"', 'dimensioni->h', 'dimensioni[h]', 'dimensioni/h'], 0, L('Notazione col punto, tra virgolette.', 'Dot notation, in quotes.')),
            Q('f3', L('Che cosa fa `.sort({ prezzo: -1 })`?', 'What does `.sort({ prezzo: -1 })` do?'), [L('Ordina dal prezzo più alto', 'Sorts from the highest price'), L('Toglie un euro ai prezzi', 'Takes a euro off prices'), L('Ordina dal più basso', 'Sorts from the lowest'), L('Esclude il prezzo', 'Excludes the price')], 0, L('`1` crescente, `-1` decrescente.', '`1` ascending, `-1` descending.')),
        ], task('mongodb', L('Trova nella collezione `utenti` chi ha `eta` maggiore o uguale a 18 e `citta` uguale a `"Udine"`.', 'Find in the `utenti` collection those with `eta` greater than or equal to 18 and `citta` equal to `"Udine"`.'), '', [re('db\.utenti\.find\s*\(', L('Cerchi negli utenti', 'You search the users')), re('eta\s*:\s*\{\s*\$gte\s*:\s*18\s*\}', L('Maggiorenni', 'Adults')), re('citta\s*:\s*["\']Udine["\']', L('Di Udine', 'From Udine'))], '', 'db.utenti.find({ eta: { $gte: 18 }, citta: "Udine" })'), [
            dBlanks(pFill(), "db.ordini.find(\n  { totale: { ___: 50 }, stato: { ___: [\"pagato\", \"spedito\"] } },\n  { cliente: 1, totale: 1, _id: ___ }\n)", ['$gt', '$in', '0'], ['>', '$all', '-1'], L('Operatori e proiezione.', 'Operators and projection.')),
            dMatch(pMatch(), [['$gt', L('Maggiore', 'Greater')], ['$lte', L('Minore o uguale', 'Less or equal')], ['$in', L('In un elenco', 'In a list')], ['$ne', L('Diverso', 'Not equal')], ['$or', L('Almeno una condizione', 'At least one condition')]], L('Gli operatori di confronto.', 'The comparison operators.')),
        ]),
        lesson('update', L('Aggiornare e cancellare', 'Updating and deleting'), [
            P('`updateOne` e `updateMany` prendono un filtro e un **operatore di aggiornamento**: `$set` imposta campi, `$inc` somma, `$push` aggiunge a un array, `$unset` toglie un campo. Senza operatore sostituiresti il documento intero, e `updateOne` lo rifiuta.', '`updateOne` and `updateMany` take a filter and an **update operator**: `$set` sets fields, `$inc` adds, `$push` appends to an array, `$unset` removes a field. Without an operator you would replace the whole document, and `updateOne` refuses that.'),
            C('db.prodotti.updateOne({ nome: "Tazza" }, { $set: { prezzo: 9 } })
db.prodotti.updateMany({ tag: "saldi" }, { $inc: { prezzo: -2 } })
db.prodotti.updateOne({ nome: "Tazza" }, { $push: { tag: "novità" } })
db.contatori.updateOne({ _id: "visite" }, { $inc: { n: 1 } }, { upsert: true })

db.sessioni.deleteMany({ scadenza: { $lt: new Date() } })', 'js'),
            P('Con **`upsert: true`** il documento viene creato se il filtro non trova niente. `deleteOne` e `deleteMany` cancellano: come in SQL, prova prima lo stesso filtro con `find`.', 'With **`upsert: true`** the document is created if the filter finds nothing. `deleteOne` and `deleteMany` delete: as in SQL, try the same filter with `find` first.'),
        ], [
            Q('u1', L('Quale operatore aumenta un numero?', 'Which operator increases a number?'), ['$inc', '$set', '$push', '$add'], 0, L('Con un valore negativo diminuisce.', 'With a negative value it decreases.')),
            Q('u2', L('Che cosa fa `upsert: true`?', 'What does `upsert: true` do?'), [L('Crea il documento se non esiste', 'Creates the document if it does not exist'), L('Aggiorna tutti i documenti', 'Updates every document'), L('Annulla l’aggiornamento', 'Undoes the update'), L('Cancella i doppioni', 'Deletes duplicates')], 0, L('Update + insert.', 'Update + insert.')),
            Q('u3', L('Come aggiungi un elemento a un array?', 'How do you add an element to an array?'), ['$push', '$set', '$inc', '$unset'], 0, L('`$addToSet` lo aggiunge solo se manca.', '`$addToSet` adds it only if missing.')),
        ], task('mongodb', L('Nella collezione `utenti` aumenta di 10 i `punti` dell’utente con `nome: "Ada"`.', 'In the `utenti` collection increase by 10 the `punti` of the user with `nome: "Ada"`.'), '', [re('db\.utenti\.updateOne\s*\(\s*\{\s*nome\s*:\s*["\']Ada["\']\s*\}', L('Trovi Ada', 'You find Ada')), re('\$inc\s*:\s*\{\s*punti\s*:\s*10\s*\}', L('Sommi 10 punti', 'You add 10 points'))], '', 'db.utenti.updateOne({ nome: "Ada" }, { $inc: { punti: 10 } })'), [
            dBug(pBug(), ['db.prodotti.updateOne(', '  { nome: "Lampada" },', '  { prezzo: 30 }', ')'], 2, '  { $set: { prezzo: 30 } }', L('Serve un operatore come `$set`.', 'It needs an operator such as `$set`.')),
            dMatch(pMatch(), [['$set', L('Imposta un campo', 'Sets a field')], ['$inc', L('Somma a un numero', 'Adds to a number')], ['$push', L('Aggiunge a un array', 'Appends to an array')], ['$unset', L('Toglie un campo', 'Removes a field')], ['upsert', L('Crea se manca', 'Creates if missing')]], L('Gli strumenti dell’aggiornamento.', 'The update tools.')),
        ]),
        lesson('aggregation', L('La pipeline di aggregazione', 'The aggregation pipeline'), [
            P('`aggregate` fa passare i documenti attraverso una **pipeline** di fasi, una dopo l’altra: `$match` filtra, `$group` raggruppa e calcola, `$sort` ordina, `$project` sceglie i campi, `$lookup` unisce un’altra collezione.', '`aggregate` runs documents through a **pipeline** of stages, one after another: `$match` filters, `$group` groups and computes, `$sort` sorts, `$project` picks fields, `$lookup` joins another collection.'),
            C('db.ordini.aggregate([
  { $match: { stato: "pagato" } },
  { $group: { _id: "$cliente", totale: { $sum: "$importo" }, ordini: { $sum: 1 } } },
  { $sort: { totale: -1 } },
  { $limit: 3 }
])', 'js'),
            P('Dentro `$group`, `_id` è il campo per cui raggruppi e `"$nomecampo"` (con il dollaro, tra virgolette) legge il valore del campo. Metti `$match` il prima possibile: meno documenti scorrono nella pipeline.', 'Inside `$group`, `_id` is the field you group by and `"$fieldname"` (with the dollar, in quotes) reads the field’s value. Put `$match` as early as possible: fewer documents flow through the pipeline.'),
        ], [
            Q('a1', L('Quale fase corrisponde al `GROUP BY` di SQL?', 'Which stage matches SQL’s `GROUP BY`?'), ['$group', '$match', '$project', '$sort'], 0, L('Con accumulatori come `$sum`.', 'With accumulators such as `$sum`.')),
            Q('a2', L('Che cosa significa `"$importo"` dentro `$sum`?', 'What does `"$importo"` mean inside `$sum`?'), [L('Il valore del campo importo', 'The value of the importo field'), L('Il testo “$importo”', 'The text “$importo”'), L('Una variabile globale', 'A global variable'), L('Un errore', 'An error')], 0, L('Il dollaro legge il campo.', 'The dollar reads the field.')),
            Q('a3', L('Perché mettere `$match` all’inizio?', 'Why put `$match` first?'), [L('Meno documenti da elaborare', 'Fewer documents to process'), L('È obbligatorio', 'It is mandatory'), L('Ordina i risultati', 'It sorts the results'), L('Crea un indice', 'It creates an index')], 0, L('E può usare gli indici.', 'And it can use indexes.')),
        ], task('mongodb', L('Nella collezione `vendite` raggruppa per `prodotto` e calcola `totale` come somma di `"$qta"`.', 'In the `vendite` collection group by `prodotto` and compute `totale` as the sum of `"$qta"`.'), "db.vendite.aggregate([\n\n])", [re('db\.vendite\.aggregate\s*\(\s*\[', L('Usi la pipeline', 'You use the pipeline')), re('\$group\s*:\s*\{\s*_id\s*:\s*["\']\$prodotto["\']', L('Raggruppi per prodotto', 'You group by product')), re('totale\s*:\s*\{\s*\$sum\s*:\s*["\']\$qta["\']\s*\}', L('Sommi le quantità', 'You add up the quantities'))], '', "db.vendite.aggregate([\n  { \$group: { _id: \"\$prodotto\", totale: { \$sum: \"\$qta\" } } }\n])"), [
            dOrder(pSort(), ['db.ordini.aggregate([', '  { $match: { anno: 2026 } },', '  { $group: { _id: "$mese", incasso: { $sum: "$totale" } } },', '  { $sort: { _id: 1 } }', '])'], L('Filtra, raggruppa, ordina.', 'Filter, group, sort.')),
            dMatch(pMatch(), [['$match', 'WHERE'], ['$group', 'GROUP BY'], ['$sort', 'ORDER BY'], ['$limit', 'LIMIT'], ['$lookup', 'JOIN']], L('Le fasi e i loro parenti SQL.', 'The stages and their SQL relatives.')),
        ]),
        lesson('modeling', L('Indici e modellazione', 'Indexes and modelling'), [
            P('Come in SQL, gli **indici** rendono veloci le ricerche: `createIndex({ campo: 1 })`. `explain("executionStats")` dice se una query usa un indice (`IXSCAN`) o legge tutta la collezione (`COLLSCAN`).', 'As in SQL, **indexes** make lookups fast: `createIndex({ field: 1 })`. `explain("executionStats")` tells you whether a query uses an index (`IXSCAN`) or reads the whole collection (`COLLSCAN`).'),
            C('db.utenti.createIndex({ email: 1 }, { unique: true })
db.ordini.createIndex({ cliente: 1, data: -1 })
db.ordini.find({ cliente: "ada" }).explain("executionStats")

// Incorporare: i dati letti sempre insieme stanno nello stesso documento
{ _id: 1, titolo: "Post", commenti: [{ autore: "Bo", testo: "Bello!" }] }
// Riferire: dati grandi o condivisi in un’altra collezione
{ _id: 7, titolo: "Ordine", clienteId: ObjectId("…") }', 'js'),
            P('La regola d’oro della modellazione: **i dati che si leggono insieme si salvano insieme**. Si incorpora (documenti annidati) quando il legame è stretto e limitato; si usano riferimenti quando i dati crescono senza limite o sono condivisi. Un documento non può superare 16 MB.', 'The golden rule of modelling: **data read together is stored together**. You embed (nested documents) when the link is tight and bounded; you use references when data grows without limit or is shared. A document cannot exceed 16 MB.'),
        ], [
            Q('i1', L('Che cosa indica `COLLSCAN` in un explain?', 'What does `COLLSCAN` mean in an explain?'), [L('Legge tutta la collezione', 'It reads the whole collection'), L('Usa un indice', 'It uses an index'), L('La query è vuota', 'The query is empty'), L('C’è un errore di sintassi', 'There is a syntax error')], 0, L('`IXSCAN` vuol dire indice.', '`IXSCAN` means index.')),
            Q('i2', L('Quando conviene incorporare i dati?', 'When is embedding data a good idea?'), [L('Quando si leggono sempre insieme e sono limitati', 'When always read together and bounded'), L('Quando crescono senza limite', 'When they grow without limit'), L('Quando sono condivisi da tutti', 'When everyone shares them'), L('Mai', 'Never')], 0, L('Si leggono insieme, si salvano insieme.', 'Read together, stored together.')),
            Q('i3', L('Qual è la dimensione massima di un documento?', 'What is the maximum size of a document?'), ['16 MB', '1 MB', '1 GB', L('Nessun limite', 'No limit')], 0, L('Un motivo in più per non incorporare array infiniti.', 'One more reason not to embed endless arrays.')),
        ], task('mongodb', L('Crea un indice unico sul campo `username` della collezione `utenti`.', 'Create a unique index on the `username` field of the `utenti` collection.'), '', [re('db\.utenti\.createIndex\s*\(\s*\{\s*username\s*:\s*1\s*\}', L('Indice su username', 'Index on username')), re('\{\s*unique\s*:\s*true\s*\}', L('È unico', 'It is unique'))], '', 'db.utenti.createIndex({ username: 1 }, { unique: true })'), [
            dFlash(pFlash(), [[L('`IXSCAN` significa che usa un indice', '`IXSCAN` means it uses an index'), true], [L('Un documento può pesare 1 GB', 'A document can weigh 1 GB'), false], [L('I commenti di un post si possono incorporare', 'A post’s comments can be embedded'), true], [L('MongoDB non ha indici unici', 'MongoDB has no unique indexes'), false]], L('Velocità e forma dei dati.', 'Speed and shape of data.')),
            dBlanks(pFill(), "db.ordini.___({ cliente: 1, data: -1 })\ndb.ordini.find({ cliente: \"ada\" }).___(\"executionStats\")", ['createIndex', 'explain'], ['addIndex', 'analyze', 'index'], L('Creare l’indice e verificarlo.', 'Create the index and verify it.')),
        ]),
        lesson('arrays', L('Lavorare con gli array', 'Working with arrays'), [
            P('Gli array sono il cuore dei documenti. Per cercarli: `$all` vuole tutti i valori elencati, `$size` una lunghezza precisa, **`$elemMatch`** un elemento che rispetti **insieme** più condizioni. Per modificarli: `$addToSet` aggiunge senza doppioni, `$pull` toglie gli elementi che corrispondono.', 'Arrays are the heart of documents. To search them: `$all` wants every listed value, `$size` an exact length, **`$elemMatch`** one element meeting several conditions **together**. To change them: `$addToSet` adds without duplicates, `$pull` removes matching elements.'),
            C('db.post.find({ tag: { $all: ["mongodb", "database"] } })
db.ordini.find({ righe: { $elemMatch: { prodotto: "Tazza", qta: { $gte: 2 } } } })

db.post.updateOne({ _id: 1 }, { $addToSet: { tag: "nosql" } })
db.post.updateOne({ _id: 1 }, { $pull: { tag: "vecchio" } })

// $ = il primo elemento trovato dal filtro
db.ordini.updateOne(
  { _id: 7, "righe.prodotto": "Tazza" },
  { $set: { "righe.$.qta": 3 } }
)', 'js'),
            P('Senza `$elemMatch`, `{ "righe.prodotto": "Tazza", "righe.qta": { $gte: 2 } }` può trovare un documento in cui le due condizioni valgono per **elementi diversi**. L’operatore posizionale `$` aggiorna l’elemento trovato dal filtro; `$[]` li aggiorna tutti.', 'Without `$elemMatch`, `{ "righe.prodotto": "Tazza", "righe.qta": { $gte: 2 } }` can match a document where the two conditions hold for **different elements**. The positional `$` operator updates the element found by the filter; `$[]` updates them all.'),
        ], [
            Q('r1', L('Quale operatore aggiunge a un array solo se il valore manca?', 'Which operator adds to an array only if the value is missing?'), ['$push', '$addToSet', '$set', '$inc'], 1, L('`$push` invece aggiunge sempre.', '`$push` instead always adds.')),
            Q('r2', L('Perché usare `$elemMatch`?', 'Why use `$elemMatch`?'), [L('Per contare gli elementi di un array', 'To count an array’s elements'), L('Per ordinare gli elementi di un array', 'To sort an array’s elements'), L('Per unire due array in uno', 'To merge two arrays into one'), L('Perché più condizioni valgano per lo stesso elemento', 'So several conditions hold for the same element')], 3, L('Altrimenti possono valere per elementi diversi.', 'Otherwise they may hold for different elements.')),
            Q('r3', L('Che cosa aggiorna `"righe.$.qta"`?', 'What does `"righe.$.qta"` update?'), [L('Tutti gli elementi di `righe`', 'Every element of `righe`'), L('L’ultimo elemento di `righe`', 'The last element of `righe`'), L('L’elemento trovato dal filtro', 'The element found by the filter'), L('Un nuovo campo chiamato `$`', 'A new field called `$`')], 2, L('Per tutti si usa `$[]`.', 'For all of them you use `$[]`.')),
        ], task('mongodb', L('Nella collezione `post` togli il tag `"bozza"` dall’array `tag` del documento con `_id: 5`, usando `$pull`.', 'In the `post` collection remove the tag `"bozza"` from the `tag` array of the document with `_id: 5`, using `$pull`.'), '', [re('db\.post\.updateOne\s*\(\s*\{\s*_id\s*:\s*5\s*\}', L('Trovi il post', 'You find the post')), re('\$pull\s*:\s*\{\s*tag\s*:\s*["\']bozza["\']\s*\}', L('Togli il tag', 'You remove the tag'))], '', 'db.post.updateOne({ _id: 5 }, { $pull: { tag: "bozza" } })'), [
            dMatch(pMatch(), [['$all', L('Tutti i valori elencati', 'Every listed value')], ['$size', L('Lunghezza esatta', 'Exact length')], ['$elemMatch', L('Condizioni sullo stesso elemento', 'Conditions on the same element')], ['$addToSet', L('Aggiunge senza doppioni', 'Adds without duplicates')], ['$pull', L('Toglie gli elementi', 'Removes elements')]], L('Gli operatori degli array.', 'The array operators.')),
            dBlanks(pFill(), "db.utenti.updateOne(\n  { _id: 3, \"indirizzi.tipo\": \"casa\" },\n  { ___: { \"indirizzi.___.citta\": \"Udine\" } }\n)\ndb.utenti.updateOne({ _id: 3 }, { ___: { preferiti: \"sql\" } })", ['$set', '$', '$addToSet'], ['$update', '0', '$append'], L('Aggiornare un elemento trovato, poi aggiungere senza doppioni.', 'Update a found element, then add without duplicates.')),
        ]),
        lesson('validation', L('Validare lo schema', 'Schema validation'), [
            P('Lo schema flessibile non vuol dire «senza regole». Con un **validatore** `$jsonSchema` la collezione rifiuta i documenti che non rispettano la forma prevista: campi obbligatori, tipi, valori minimi, testi che seguono un modello.', 'A flexible schema does not mean “no rules”. With a `$jsonSchema` **validator** the collection rejects documents that do not match the expected shape: required fields, types, minimum values, texts following a pattern.'),
            C('db.createCollection("utenti", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["email", "nome"],
      properties: {
        email: { bsonType: "string", pattern: "^.+@.+$" },
        eta: { bsonType: "int", minimum: 0 }
      }
    }
  }
})

db.utenti.insertOne({ nome: "Ada" })   // rifiutato: manca email', 'js'),
            P('Per una collezione che esiste già si usa il comando `collMod`. `validationAction: "warn"` registra solo un avviso invece di rifiutare: utile per introdurre le regole senza rompere l’app. I campi non elencati in `properties` restano liberi, a meno di `additionalProperties: false`.', 'For an existing collection you use the `collMod` command. `validationAction: "warn"` only logs a warning instead of rejecting: useful to introduce rules without breaking the app. Fields not listed in `properties` stay free, unless you set `additionalProperties: false`.'),
        ], [
            Q('v1', L('Che cosa succede inserendo un documento che non rispetta il validatore?', 'What happens when inserting a document that breaks the validator?'), [L('Viene rifiutato con un errore', 'It is rejected with an error'), L('Viene corretto in automatico', 'It is fixed automatically'), L('Viene salvato in un’altra collezione', 'It is saved in another collection'), L('Viene salvato senza i campi sbagliati', 'It is saved without the wrong fields')], 0, L('Con l’azione predefinita, `error`.', 'With the default action, `error`.')),
            Q('v2', L('Come aggiungi un validatore a una collezione esistente?', 'How do you add a validator to an existing collection?'), [L('Ricreando il database', 'By recreating the database'), L('Con `createIndex`', 'With `createIndex`'), L('Con un trigger', 'With a trigger'), L('Con il comando `collMod`', 'With the `collMod` command')], 3, L('`db.runCommand({ collMod: "utenti", validator: … })`.', '`db.runCommand({ collMod: "utenti", validator: … })`.')),
            Q('v3', L('A che cosa serve `validationAction: "warn"`?', 'What is `validationAction: "warn"` for?'), [L('A cancellare i documenti sbagliati', 'Deleting wrong documents'), L('A registrare un avviso senza rifiutare', 'Logging a warning without rejecting'), L('A mandare un’email all’amministratore', 'Emailing the administrator'), L('A bloccare tutte le letture', 'Blocking every read')], 1, L('Per introdurre le regole con calma.', 'To bring rules in gently.')),
        ], task('mongodb', L('Crea la collezione `prodotti` con un validatore `$jsonSchema` che renda obbligatori i campi `nome` e `prezzo`.', 'Create the `prodotti` collection with a `$jsonSchema` validator making the fields `nome` and `prezzo` required.'), '', [re('db\.createCollection\s*\(\s*["\']prodotti["\']', L('Crei la collezione', 'You create the collection')), re('validator\s*:\s*\{\s*\$jsonSchema\s*:', L('Con un validatore', 'With a validator')), re('required\s*:\s*\[\s*(["\']nome["\']\s*,\s*["\']prezzo["\']|["\']prezzo["\']\s*,\s*["\']nome["\'])\s*\]', L('Nome e prezzo obbligatori', 'Name and price required'))], '', 'db.createCollection("prodotti", {
  validator: { $jsonSchema: { bsonType: "object", required: ["nome", "prezzo"] } }
})'), [
            dMatch(pMatch(), [['$jsonSchema', L('Descrive la forma dei documenti', 'Describes the documents’ shape')], ['required', L('Campi obbligatori', 'Required fields')], ['bsonType', L('Il tipo del campo', 'The field’s type')], ['collMod', L('Modifica una collezione esistente', 'Changes an existing collection')], ['warn', L('Avvisa senza rifiutare', 'Warns without rejecting')]], L('Regole per i documenti.', 'Rules for documents.')),
            dFlash(pFlash(), [[L('MongoDB può rifiutare documenti senza un campo obbligatorio', 'MongoDB can reject documents missing a required field'), true], [L('Un validatore si può aggiungere solo creando la collezione', 'A validator can only be added when creating the collection'), false], [L('I campi non elencati restano liberi di base', 'Unlisted fields stay free by default'), true], [L('Con `warn` i documenti sbagliati vengono cancellati', 'With `warn` wrong documents are deleted'), false]], L('Flessibile, ma con regole.', 'Flexible, but with rules.')),
        ]),
        lesson('transactions', L('Transazioni e replica set', 'Transactions and replica sets'), [
            P('In MongoDB un’operazione su **un solo documento** è già atomica: per questo incorporare i dati collegati spesso evita le transazioni. Quando devi modificare più documenti insieme (un bonifico tra due conti) usi una **transazione multi-documento**, che richiede un **replica set** o un cluster sharded.', 'In MongoDB an operation on **a single document** is already atomic: that is why embedding related data often avoids transactions. When you must change several documents together (a transfer between two accounts) you use a **multi-document transaction**, which requires a **replica set** or a sharded cluster.'),
            C('// Node.js, driver ufficiale
const session = client.startSession();
try {
  await session.withTransaction(async () => {
    await conti.updateOne({ _id: 1 }, { $inc: { saldo: -50 } }, { session });
    await conti.updateOne({ _id: 2 }, { $inc: { saldo: 50 } }, { session });
  });
} finally {
  await session.endSession();
}', 'js'),
            P('Ogni operazione riceve la `session`; `withTransaction` conferma alla fine e ripete in automatico in caso di errori temporanei. Un **replica set** è un gruppo di server con le stesse copie dei dati: uno primario riceve le scritture, i secondari lo seguono e lo sostituiscono se cade. Con `writeConcern: { w: "majority" }` una scrittura è confermata solo quando la maggioranza l’ha ricevuta.', 'Each operation receives the `session`; `withTransaction` commits at the end and retries automatically on transient errors. A **replica set** is a group of servers holding the same copies of the data: a primary receives writes, the secondaries follow it and replace it if it fails. With `writeConcern: { w: "majority" }` a write is confirmed only once the majority has received it.'),
        ], [
            Q('x1', L('Serve una transazione per aggiornare un solo documento?', 'Do you need a transaction to update a single document?'), [L('Sì, sempre', 'Yes, always'), L('Solo se il documento ha array', 'Only if the document has arrays'), L('No, è già atomico', 'No, it is already atomic'), L('Solo nei giorni festivi', 'Only on holidays')], 2, L('Anche se cambia più campi.', 'Even if it changes several fields.')),
            Q('x2', L('Che cosa richiedono le transazioni multi-documento?', 'What do multi-document transactions require?'), [L('Un replica set o un cluster sharded', 'A replica set or a sharded cluster'), L('Un server senza indici', 'A server with no indexes'), L('Una collezione con validatore', 'A collection with a validator'), L('Il pacchetto di SQL Server', 'The SQL Server package')], 0, L('Un singolo server autonomo non basta.', 'A single standalone server is not enough.')),
            Q('x3', L('Che cosa garantisce `w: "majority"`?', 'What does `w: "majority"` guarantee?'), [L('Che la scrittura sia più veloce', 'That the write is faster'), L('Che la scrittura vada su un solo server', 'That the write goes to a single server'), L('Che i dati siano cifrati', 'That the data is encrypted'), L('Che la maggioranza dei nodi abbia ricevuto la scrittura', 'That the majority of nodes received the write')], 3, L('Così sopravvive alla caduta del primario.', 'So it survives the primary failing.')),
        ], task('js', L('Completa la transazione: dentro `session.withTransaction(async () => { … })` aggiungi `-1` a `pezzi` del prodotto `_id: 9` in `magazzino`, passando `{ session }`.', 'Complete the transaction: inside `session.withTransaction(async () => { … })` add `-1` to `pezzi` of product `_id: 9` in `magazzino`, passing `{ session }`.'), "const session = client.startSession();\n", [re('session\.withTransaction\s*\(\s*async\s*\(\s*\)\s*=>', L('Usi withTransaction', 'You use withTransaction')), re('magazzino\.updateOne\s*\(\s*\{\s*_id\s*:\s*9\s*\}\s*,\s*\{\s*\$inc\s*:\s*\{\s*pezzi\s*:\s*-1\s*\}\s*\}\s*,\s*\{\s*session\s*(:\s*session\s*)?\}\s*\)', L('Aggiorni dentro la sessione', 'You update inside the session'))], '', "await session.withTransaction(async () => {\n  await magazzino.updateOne({ _id: 9 }, { \$inc: { pezzi: -1 } }, { session });\n});"), [
            dOrder(pSort(), ['const session = client.startSession();', 'await session.withTransaction(async () => {', '  await ordini.insertOne({ prodotto: 9 }, { session });', '  await magazzino.updateOne({ _id: 9 }, { $inc: { pezzi: -1 } }, { session });', '});', 'await session.endSession();'], L('Sessione, transazione, operazioni, chiusura.', 'Session, transaction, operations, close.')),
            dMatch(pMatch(), [[L('Primario', 'Primary'), L('Riceve le scritture', 'Receives writes')], [L('Secondario', 'Secondary'), L('Copia il primario', 'Copies the primary')], ['withTransaction', L('Conferma o ripete', 'Commits or retries')], ['majority', L('Conferma della maggioranza', 'Majority confirmation')], [L('Documento singolo', 'Single document'), L('Già atomico', 'Already atomic')]], L('Affidabilità in MongoDB.', 'Reliability in MongoDB.')),
        ]),
    ],
];
