<?php
/**
 * Initial data: categories, product catalog and recipes.
 * The seed is idempotent: categories and products are added only when missing (by name),
 * recipes only when the recipe table is empty. Everything can then be edited from the admin portal.
 */

declare(strict_types=1);

require_once __DIR__ . '/seed-recipes-2.php';
require_once __DIR__ . '/seed-recipes-3.php';
require_once __DIR__ . '/seed-images.php';

function seed_categories(): array
{
    // name => generic expiry tip shown when a product of this category is expiring or expired
    return [
        'Frutta' => 'La frutta fresca non ha una vera data di scadenza: controlla aspetto, odore e consistenza. Piccole ammaccature si possono eliminare; muffa, odore fermentato o polpa molle e scura indicano che è meglio non consumarla. La frutta troppo matura è perfetta per frullati, dolci e marmellate.',
        'Verdura' => 'Le verdure appassite ma senza muffa possono essere ancora usate cotte (minestre, vellutate, sughi). Elimina le parti molli o annerite; se compaiono muffa, odore sgradevole o consistenza viscida, buttala.',
        'Latticini e uova' => 'Per latticini e uova la differenza tra «da consumarsi entro» e «preferibilmente entro» è importante: la prima indica un limite di sicurezza, la seconda solo di qualità. In ogni caso controlla odore, sapore e aspetto prima del consumo.',
        'Carne e salumi' => 'Carne fresca e salumi affettati riportano di solito «da consumarsi entro»: dopo quella data è sconsigliato consumarli. Se ti accorgi che sta per scadere, puoi cuocerla o congelarla subito per allungarne la durata.',
        'Pesce' => 'Il pesce fresco è tra i prodotti più delicati: rispetta la data «da consumarsi entro». Un odore forte di ammoniaca, occhi opachi o carni molli indicano che non è più fresco. Se non lo consumi subito, congelalo.',
        'Pane e prodotti da forno' => 'Il pane raffermo è ancora ottimo: puoi tostarlo, farne pangrattato, crostini o usarlo per ricette come la panzanella. Se compare muffa va buttato tutto, non solo la parte visibile.',
        'Pasta, riso e cereali' => 'Pasta secca, riso e cereali riportano «da consumarsi preferibilmente entro»: conservati asciutti e chiusi, restano generalmente buoni anche mesi dopo la data. Controlla che non ci siano insetti, odori di rancido o umidità.',
        'Conserve e scatolame' => 'Le conserve in scatola durano a lungo, anche oltre il «preferibilmente entro», se la confezione è integra. Butta subito le scatole gonfie, arrugginite, ammaccate in modo profondo o che all’apertura schizzano o hanno odore anomalo.',
        'Condimenti e spezie' => 'Olio, aceto, sale e spezie hanno una durata molto lunga. Le spezie con il tempo perdono aroma ma non diventano pericolose; l’olio può diventare rancido (odore e sapore di vernice o di stantio).',
        'Bevande' => 'Le bevande riportano spesso «da consumarsi preferibilmente entro»: se non è passato troppo tempo e la confezione è chiusa e integra, il prodotto potrebbe essere ancora buono. Una volta aperte vanno consumate in pochi giorni.',
        'Surgelati' => 'I surgelati conservati sempre a −18 °C restano sicuri a lungo, anche se dopo la data la qualità (gusto, consistenza) può peggiorare. Un prodotto scongelato non va ricongelato da crudo.',
        'Snack e dolci' => 'Biscotti, snack e dolci confezionati riportano quasi sempre «preferibilmente entro»: dopo la data possono essere meno fragranti ma generalmente ancora consumabili se la confezione è integra e non ci sono odori di rancido.',
        'Altro' => 'Controlla la dicitura sulla confezione: «da consumarsi entro» è un limite di sicurezza, «da consumarsi preferibilmente entro» indica solo che dopo quella data il prodotto potrebbe perdere qualità.',
    ];
}

function seed_products(): array
{
    // [name, icon, category, default unit, specific expiry tip (optional)]
    return array_merge(seed_products_1(), array_map(function ($p) {
        return [$p[0], $p[2], $p[3], $p[4], null];
    }, array_merge(seed2_products(), seed3_products())));
}

/** Storage environment of a catalog product: the usual one of its category, unless it is an exception. */
function seed_product_environment(string $name, string $category): string
{
    $exceptions = [
        'Mele' => 'cool', 'Lamponi' => 'cool', 'Kimchi' => 'cool', 'Pasta di miso' => 'cool', 'Pasta sfoglia' => 'cool', 'Tofu' => 'cool',
        'Patate' => 'ambient', 'Patate dolci' => 'ambient', 'Cipolle' => 'ambient', 'Aglio' => 'ambient', 'Zucca' => 'ambient', 'Pomodori' => 'ambient',
        'Latte UHT' => 'ambient', 'Latte condensato' => 'ambient',
    ];
    $categories = ['Surgelati' => 'cold', 'Verdura' => 'cool', 'Latticini e uova' => 'cool', 'Carne e salumi' => 'cool', 'Pesce' => 'cool'];
    return $exceptions[$name] ?? $categories[$category] ?? 'ambient';
}

function seed_products_1(): array
{
    return [
        ['Mele', 'apple', 'Frutta', 'pz', 'Le mele durano a lungo in frigorifero. Se sono un po’ raggrinzite ma sane sono perfette per torte e composte.'],
        ['Banane', 'banana', 'Frutta', 'pz', 'Le banane con la buccia scura sono più dolci: ideali per banana bread, pancake e frullati. Puoi anche sbucciarle e congelarle.'],
        ['Anguria', 'watermelon', 'Frutta', 'kg', 'Una volta tagliata, l’anguria va coperta e conservata in frigorifero per non più di 3–4 giorni.'],
        ['Olive', 'olive', 'Conserve e scatolame', 'g', 'Le olive in salamoia durano a lungo se restano coperte dal liquido. Elimina quelle molli o con patina biancastra in superficie.'],
        ['Pomodori', 'tomato', 'Verdura', 'pz', 'I pomodori molto maturi sono ottimi per sughi e salse: elimina solo le parti ammaccate.'],
        ['Passata di pomodoro', 'sauce', 'Conserve e scatolame', 'g', 'Chiusa si conserva a lungo; una volta aperta va tenuta in frigorifero e consumata entro 4–5 giorni.'],
        ['Patate', 'potato', 'Verdura', 'kg', 'Elimina i germogli e le parti verdi (contengono solanina) prima di cucinarle. Se sono molli o raggrinzite in modo marcato, buttale.'],
        ['Patate dolci', 'sweet_potato', 'Verdura', 'kg', null],
        ['Carote', 'carrot', 'Verdura', 'pz', 'Le carote un po’ molli possono essere usate in brodi, soffritti e vellutate.'],
        ['Cipolle', 'onion', 'Verdura', 'pz', null],
        ['Aglio', 'garlic', 'Verdura', 'pz', 'Se l’aglio germoglia è ancora commestibile: togli il germoglio centrale, più amaro.'],
        ['Porri', 'leek', 'Verdura', 'pz', null],
        ['Zucchine', 'zucchini', 'Verdura', 'pz', null],
        ['Melanzane', 'eggplant', 'Verdura', 'pz', null],
        ['Zucca', 'squash', 'Verdura', 'kg', null],
        ['Broccoli', 'broccoli', 'Verdura', 'g', null],
        ['Cavolfiore', 'cauliflower', 'Verdura', 'pz', null],
        ['Asparagi', 'asparagus', 'Verdura', 'g', null],
        ['Funghi', 'mushroom', 'Verdura', 'g', 'I funghi freschi vanno consumati in pochi giorni: se diventano viscidi o anneriscono, buttali.'],
        ['Mais', 'corn', 'Conserve e scatolame', 'g', null],
        ['Piselli', 'peas', 'Surgelati', 'g', null],
        ['Fagioli', 'bean', 'Conserve e scatolame', 'g', null],
        ['Barbabietole', 'beet', 'Verdura', 'pz', null],
        ['Ravanelli', 'radish', 'Verdura', 'pz', null],
        ['Basilico', 'herbs', 'Verdura', 'g', 'Il basilico annerisce in fretta: puoi conservarlo frullato con olio, oppure congelato in cubetti.'],
        ['Insalata', 'vegetable', 'Verdura', 'pz', null],
        ['Latte intero', 'milk', 'Latticini e uova', 'l', 'Il latte fresco riporta «da consumarsi entro»; quello UHT «preferibilmente entro» e, se chiuso, è spesso buono anche qualche giorno dopo. Se ha odore acido o grumi, non berlo.'],
        ['Latte UHT', 'milk', 'Latticini e uova', 'l', 'Il latte UHT chiuso è spesso buono anche oltre il «preferibilmente entro»: annusalo e assaggialo. Una volta aperto si conserva in frigo per circa 3 giorni.'],
        ['Yogurt bianco', 'yogurt', 'Latticini e uova', 'g', 'Lo yogurt chiuso è spesso buono anche alcuni giorni dopo la data: se non è gonfio e non ha muffa o odore anomalo, assaggialo prima di buttarlo.'],
        ['Yogurt greco', 'yogurt', 'Latticini e uova', 'g', 'Lo yogurt chiuso è spesso buono anche alcuni giorni dopo la data: se non è gonfio e non ha muffa o odore anomalo, assaggialo prima di buttarlo.'],
        ['Mozzarella di bufala', 'cheese', 'Latticini e uova', 'g', 'La mozzarella va consumata entro la data «da consumarsi entro». Se è acida, gonfia o ha una consistenza viscida, buttala.'],
        ['Parmigiano', 'cheese', 'Latticini e uova', 'g', 'I formaggi stagionati durano a lungo: una piccola muffa superficiale si può eliminare tagliando almeno 1 cm attorno.'],
        ['Formaggio spalmabile', 'cheese', 'Latticini e uova', 'g', null],
        ['Burro', 'butter', 'Latticini e uova', 'g', 'Il burro irrancidisce con il tempo (odore e sapore pungente): conservato in frigo e ben chiuso dura settimane; si può anche congelare.'],
        ['Panna fresca', 'cream', 'Latticini e uova', 'ml', null],
        ['Uova', 'egg', 'Latticini e uova', 'pz', 'Alcune persone verificano la freschezza mettendo l’uovo in un bicchiere d’acqua: se va a fondo è fresco, se resta a galla è vecchio ed è meglio non consumarlo. Le uova scadute da pochi giorni vanno comunque consumate ben cotte.'],
        ['Uova biologiche', 'egg', 'Latticini e uova', 'pz', 'Alcune persone verificano la freschezza mettendo l’uovo in un bicchiere d’acqua: se va a fondo è fresco, se resta a galla è vecchio ed è meglio non consumarlo. Le uova scadute da pochi giorni vanno comunque consumate ben cotte.'],
        ['Gelato', 'icecream', 'Surgelati', 'g', null],
        ['Carne di pollo', 'chicken', 'Carne e salumi', 'g', 'Il pollo crudo è molto delicato: non consumarlo dopo la data «da consumarsi entro». Se sta per scadere, cuocilo o congelalo subito.'],
        ['Prosciutto crudo', 'ham', 'Carne e salumi', 'g', 'Il prosciutto affettato in vaschetta va consumato entro la data indicata; se perde colore o ha odore sgradevole, buttalo.'],
        ['Prosciutto cotto', 'ham', 'Carne e salumi', 'g', 'Il prosciutto cotto affettato è delicato: se diventa viscido o ha odore acido non va consumato.'],
        ['Salame', 'salami', 'Carne e salumi', 'g', 'I salumi stagionati interi durano a lungo; una patina bianca di muffa nobile sul budello è normale.'],
        ['Pesce fresco', 'fish', 'Pesce', 'g', null],
        ['Salmone affumicato', 'fish2', 'Pesce', 'g', 'Il salmone affumicato va consumato entro la data «da consumarsi entro» e, una volta aperto, entro 1–2 giorni.'],
        ['Tonno in scatola', 'tuna', 'Conserve e scatolame', 'g', 'Il tonno in scatola dura anni se la latta è integra. Una volta aperto, trasferiscilo in un contenitore e consumalo entro 2–3 giorni.'],
        ['Pane integrale', 'bread', 'Pane e prodotti da forno', 'g', 'Il pane raffermo è perfetto per bruschette, pangrattato o panzanella. Se compare muffa va buttato tutto.'],
        ['Piadina', 'bread', 'Pane e prodotti da forno', 'pz', null],
        ['Farina', 'flour', 'Pasta, riso e cereali', 'g', 'La farina dura a lungo anche dopo il «preferibilmente entro» se conservata asciutta; buttala se ha odore di rancido o se ci sono farfalline.'],
        ['Zucchero', 'sugar', 'Condimenti e spezie', 'g', 'Lo zucchero non scade se conservato all’asciutto.'],
        ['Sale', 'salt', 'Condimenti e spezie', 'g', 'Il sale non scade.'],
        ['Lievito di birra', 'yeast', 'Latticini e uova', 'g', 'Il lievito di birra fresco scaduto perde forza: prova a scioglierlo in acqua tiepida con un pizzico di zucchero, se dopo 10 minuti fa schiuma funziona ancora.'],
        ['Lievito per dolci', 'yeast', 'Snack e dolci', 'g', null],
        ['Pinoli', 'nuts', 'Snack e dolci', 'g', null],
        ['Pasta di semola', 'pasta', 'Pasta, riso e cereali', 'g', 'La pasta secca riporta «da consumarsi preferibilmente entro»: se conservata bene è buona anche molti mesi dopo.'],
        ['Pasta all’uovo', 'noodles', 'Pasta, riso e cereali', 'g', 'La pasta all’uovo fresca va consumata entro la data indicata; quella secca si conserva a lungo.'],
        ['Riso integrale', 'rice', 'Pasta, riso e cereali', 'g', 'Il riso integrale contiene più grassi del riso bianco e può irrancidire: se ha odore di stantio, buttalo.'],
        ['Biscotti integrali', 'biscuit', 'Snack e dolci', 'g', null],
        ['Patatine', 'crisps', 'Snack e dolci', 'g', null],
        ['Patatine fritte surgelate', 'chips', 'Surgelati', 'g', null],
        ['Caffè macinato', 'coffee', 'Bevande', 'g', 'Il caffè macinato scaduto non è dannoso ma perde aroma: conservalo chiuso, al riparo da luce e umidità.'],
        ['Acqua', 'water', 'Bevande', 'l', null],
        ['Succo di frutta', 'juice', 'Bevande', 'l', 'Spesso i succhi riportano la scritta «da consumarsi preferibilmente entro»: se non è passato troppo tempo e la confezione è chiusa e integra, il prodotto potrebbe essere comunque buono.'],
        ['Vino rosso', 'wine', 'Bevande', 'ml', null],
        ['Vino bianco', 'wine2', 'Bevande', 'ml', null],
        ['Spumante', 'champagne', 'Bevande', 'ml', null],
        ['Olio extravergine d’oliva', 'olive_oil', 'Condimenti e spezie', 'ml', null],
        ['Olio di semi', 'olive_oil', 'Condimenti e spezie', 'ml', null],
        ['Aceto balsamico', 'vinegar', 'Condimenti e spezie', 'ml', 'L’aceto praticamente non scade: un deposito sul fondo è normale.'],
    ];
}

/** All the initial recipes: batch 1 (first version) + batches 2 and 3 (recipes from many countries). */
function seed_recipes(): array
{
    $conv = function (array $list, int $batch) {
        return array_map(function ($r) use ($batch) {
            return [
                'name' => $r['it'], 'origin' => implode(',', $r['origin']), 'image' => seed_recipe_image($r['it']), 'description' => $r['desc'][0],
                'prep' => $r['prep'], 'cook' => $r['cook'], 'cook_notes' => $r['cookN'][0], 'servings' => $r['serv'], 'difficulty' => $r['diff'],
                'ingredients' => array_map(function ($i) {
                    return [$i[0], $i[1], $i[2], $i[3]];
                }, $r['ing']),
                'steps' => array_map(function ($st) {
                    return $st[0];
                }, $r['steps']),
                'batch' => $batch,
            ];
        }, $list);
    };
    return array_merge(seed_recipes_1(), $conv(seed2_recipes(), 2), $conv(seed3_recipes(), 3));
}

function seed_recipes_1(): array
{
    // ingredients: [product name (catalog) or free text, quantity, unit, notes]
    return [
        [
            'name' => 'Pizza Margherita', 'origin' => 'IT', 'image' => 'assets/img/recipes/pizza_margherita.jpg',
            'description' => 'La classica pizza fatta in casa con pomodoro, mozzarella di bufala e basilico.',
            'prep' => 30, 'cook' => 15, 'cook_notes' => 'Forno statico, 250 °C per 15 minuti', 'servings' => 4, 'difficulty' => 'media',
            'ingredients' => [
                ['Farina', 500, 'g', ''], ['Acqua', 300, 'ml', 'tiepida'], ['Lievito di birra', 7, 'g', ''],
                ['Passata di pomodoro', 300, 'g', ''], ['Mozzarella di bufala', 250, 'g', 'ben scolata'],
                ['Olio extravergine d’oliva', 2, 'cucchiai', ''], ['Basilico', null, 'q.b.', ''], ['Sale', 10, 'g', ''],
            ],
            'steps' => [
                'Preparare l’impasto: sciogliere il lievito nell’acqua tiepida, aggiungere la farina, poi il sale e l’olio, e impastare per 10 minuti.',
                'Lasciare lievitare l’impasto coperto per almeno 2 ore, finché raddoppia di volume.',
                'Stendere la pasta su una teglia unta d’olio.',
                'Aggiungere il pomodoro condito con un filo d’olio e un pizzico di sale.',
                'Cuocere in forno per 10 minuti, poi aggiungere la mozzarella e cuocere altri 5 minuti.',
                'Completare con il basilico fresco.',
            ],
        ],
        [
            'name' => 'Banana Bread', 'origin' => 'US', 'image' => 'assets/img/recipes/banana_bread.jpg',
            'description' => 'Un dolce soffice perfetto per usare le banane troppo mature.',
            'prep' => 15, 'cook' => 50, 'cook_notes' => 'Forno statico, 180 °C per 45–50 minuti', 'servings' => 8, 'difficulty' => 'facile',
            'ingredients' => [
                ['Banane', 3, 'pz', 'le banane devono essere mature'], ['Farina', 250, 'g', ''], ['Zucchero', 100, 'g', ''],
                ['Uova', 2, 'pz', ''], ['Olio di semi', 80, 'ml', ''], ['Lievito per dolci', 8, 'g', ''],
            ],
            'steps' => [
                'Schiacciare le banane in una ciotola con una forchetta.',
                'Aggiungere uova, zucchero e olio, e mescolare bene.',
                'Unire la farina e il lievito setacciati, e impastare fino a ottenere un composto omogeneo.',
                'Versare l’impasto in uno stampo da plumcake foderato con carta forno.',
                'Cuocere in forno: fai la prova stecchino prima di sfornare.',
            ],
        ],
        [
            'name' => 'Pancake', 'origin' => 'US', 'image' => 'assets/img/recipes/pancakes.jpg',
            'description' => 'Pancake soffici per una colazione speciale.',
            'prep' => 10, 'cook' => 15, 'cook_notes' => 'Cottura in padella a fuoco medio', 'servings' => 4, 'difficulty' => 'facile',
            'ingredients' => [
                ['Farina', 200, 'g', ''], ['Latte intero', 250, 'ml', ''], ['Uova', 2, 'pz', ''],
                ['Zucchero', 20, 'g', ''], ['Burro', 30, 'g', 'fuso'], ['Lievito per dolci', 8, 'g', ''],
            ],
            'steps' => [
                'Mescolare gli ingredienti secchi: farina, zucchero e lievito.',
                'In un’altra ciotola sbattere uova, latte e burro fuso.',
                'Unire i due composti mescolando senza formare grumi.',
                'Versare un mestolino di impasto in una padella antiaderente calda.',
                'Girare il pancake quando compaiono le bollicine in superficie e cuocere un altro minuto.',
            ],
        ],
        [
            'name' => 'Pasta al pesto', 'origin' => 'IT', 'image' => 'assets/img/recipes/pasta_pesto.jpg',
            'description' => 'Pasta con pesto di basilico fatto in casa, pronta in 25 minuti.',
            'prep' => 15, 'cook' => 10, 'cook_notes' => 'Cottura in pentola con acqua bollente', 'servings' => 4, 'difficulty' => 'facile',
            'ingredients' => [
                ['Pasta di semola', 320, 'g', ''], ['Basilico', 50, 'g', 'foglie fresche'], ['Parmigiano', 50, 'g', 'grattugiato'],
                ['Pinoli', 20, 'g', ''], ['Aglio', 1, 'pz', 'uno spicchio'], ['Olio extravergine d’oliva', 80, 'ml', ''], ['Sale', null, 'q.b.', ''],
            ],
            'steps' => [
                'Preparare il pesto: frullare basilico, pinoli, aglio, parmigiano e olio con un pizzico di sale.',
                'Cuocere la pasta in abbondante acqua salata.',
                'Scolare la pasta tenendo da parte un po’ di acqua di cottura.',
                'Condire con il pesto, aggiungendo un cucchiaio di acqua di cottura per renderlo cremoso.',
            ],
        ],
        [
            'name' => 'Pasta con le cipolle', 'origin' => 'IT', 'image' => 'assets/img/recipes/pasta_cipolle.jpg',
            'description' => 'Un primo semplice ed economico, dolce e saporito.',
            'prep' => 10, 'cook' => 25, 'cook_notes' => 'Cottura in padella a fuoco basso', 'servings' => 4, 'difficulty' => 'facile',
            'ingredients' => [
                ['Pasta di semola', 320, 'g', ''], ['Cipolle', 3, 'pz', ''], ['Olio extravergine d’oliva', 3, 'cucchiai', ''],
                ['Parmigiano', 40, 'g', 'grattugiato'], ['Sale', null, 'q.b.', ''],
            ],
            'steps' => [
                'Affettare finemente le cipolle.',
                'Cuocere le cipolle in padella con l’olio a fuoco basso per 20 minuti, aggiungendo un po’ d’acqua se necessario.',
                'Cuocere la pasta in abbondante acqua salata.',
                'Scolare la pasta e condire con le cipolle.',
                'Completare con il parmigiano grattugiato.',
            ],
        ],
        [
            'name' => 'Piadina con pomodori e prosciutto', 'origin' => 'IT', 'image' => 'assets/img/recipes/piadina.jpg',
            'description' => 'Un pranzo veloce, pronto in 10 minuti.',
            'prep' => 5, 'cook' => 4, 'cook_notes' => 'Cottura in padella', 'servings' => 2, 'difficulty' => 'facile',
            'ingredients' => [
                ['Piadina', 2, 'pz', ''], ['Prosciutto crudo', 100, 'g', ''], ['Pomodori', 2, 'pz', ''],
                ['Mozzarella di bufala', 125, 'g', ''],
            ],
            'steps' => [
                'Scaldare la piadina in padella, un minuto per lato.',
                'Aggiungere il prosciutto e la mozzarella a fette.',
                'Aggiungere i pomodori tagliati a fette sottili.',
                'Chiudere la piadina a metà e servire subito.',
            ],
        ],
        [
            'name' => 'Cheesecake', 'origin' => 'US', 'image' => 'assets/img/recipes/cheesecake.jpg',
            'description' => 'Cheesecake senza cottura, fresca e cremosa.',
            'prep' => 30, 'cook' => null, 'cook_notes' => 'Senza cottura: raffreddare in frigo almeno 4 ore', 'servings' => 8, 'difficulty' => 'media',
            'ingredients' => [
                ['Biscotti integrali', 200, 'g', ''], ['Burro', 100, 'g', 'fuso'], ['Formaggio spalmabile', 500, 'g', ''],
                ['Yogurt greco', 150, 'g', ''], ['Zucchero', 80, 'g', ''], ['Panna fresca', 200, 'ml', ''],
            ],
            'steps' => [
                'Preparare la base: tritare i biscotti e mescolarli con il burro fuso.',
                'Versare nello stampo e compattare bene, poi mettere in frigo per 30 minuti.',
                'Mescolare il ripieno: formaggio, yogurt e zucchero, poi incorporare la panna montata.',
                'Versare la crema sulla base e livellare.',
                'Raffreddare in frigo per almeno 4 ore prima di servire.',
            ],
        ],
    ];
}

/** Inserts the missing data. Returns how many rows were added per type. */
function seed_database(PDO $pdo, string $p, ?int $adminId = null): array
{
    $now = gmdate('Y-m-d H:i:s');
    $added = ['categories' => 0, 'products' => 0, 'recipes' => 0];

    $sort = 0;
    $insCat = $pdo->prepare("INSERT IGNORE INTO `{$p}categories` (name, expiry_tip, sort) VALUES (?,?,?)");
    foreach (seed_categories() as $name => $tip) {
        $insCat->execute([$name, $tip, $sort += 10]);
        $added['categories'] += $insCat->rowCount();
    }
    $cats = [];
    foreach ($pdo->query("SELECT id, name FROM `{$p}categories`") as $r) $cats[$r['name']] = (int)$r['id'];

    $insProd = $pdo->prepare("INSERT IGNORE INTO `{$p}products` (name, icon, category_id, default_unit, environment, expiry_tip, active, created_at, updated_at) VALUES (?,?,?,?,?,?,1,?,?)");
    foreach (seed_products() as [$name, $icon, $cat, $unit, $tip]) {
        $insProd->execute([$name, $icon, $cats[$cat] ?? null, $unit, seed_product_environment($name, $cat), $tip, $now, $now]);
        $added['products'] += $insProd->rowCount();
    }
    $prods = [];
    foreach ($pdo->query("SELECT id, name FROM `{$p}products`") as $r) $prods[mb_strtolower($r['name'])] = (int)$r['id'];

    // Recipes: everything on a new installation; on an existing one only the batches added later
    // (once: a recipe deleted by an admin is not brought back).
    $hasRecipes = (int)$pdo->query("SELECT COUNT(*) FROM `{$p}recipes`")->fetchColumn() > 0;
    $doneBatch = 0;
    try {
        $doneBatch = (int)$pdo->query("SELECT v FROM `{$p}meta` WHERE k = 'seed_batch'")->fetchColumn();
    } catch (Throwable $e) {
        $doneBatch = 0;
    }
    if (!$hasRecipes) $doneBatch = 0; elseif ($doneBatch === 0) $doneBatch = 1;
    $existing = [];
    foreach ($pdo->query("SELECT name FROM `{$p}recipes`") as $row) $existing[mb_strtolower($row['name'])] = true;
    $insRec = $pdo->prepare("INSERT INTO `{$p}recipes` (name, description, image, prep_minutes, cook_minutes, cook_notes, servings, difficulty, origin, published, created_by, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,1,?,?,?)");
    $insIng = $pdo->prepare("INSERT INTO `{$p}recipe_ingredients` (recipe_id, product_id, name, quantity, unit, notes, sort) VALUES (?,?,?,?,?,?,?)");
    $insStep = $pdo->prepare("INSERT INTO `{$p}recipe_steps` (recipe_id, sort, text) VALUES (?,?,?)");
    $maxBatch = 1;
    foreach (seed_recipes() as $r) {
        $batch = $r['batch'] ?? 1;
        $maxBatch = max($maxBatch, $batch);
        if ($batch <= $doneBatch || isset($existing[mb_strtolower($r['name'])])) continue;
        $insRec->execute([$r['name'], $r['description'], $r['image'], $r['prep'], $r['cook'], $r['cook_notes'], $r['servings'], $r['difficulty'], $r['origin'] ?? '', $adminId, $now, $now]);
        $rid = (int)$pdo->lastInsertId();
        foreach ($r['ingredients'] as $i => [$name, $qty, $unit, $notes]) {
            $insIng->execute([$rid, $prods[mb_strtolower($name)] ?? null, $name, $qty, $unit, $notes, $i]);
        }
        foreach ($r['steps'] as $i => $text) $insStep->execute([$rid, $i, $text]);
        $added['recipes']++;
    }
    $pdo->prepare("INSERT INTO `{$p}meta` (k, v) VALUES ('seed_batch', ?) ON DUPLICATE KEY UPDATE v = VALUES(v)")->execute([(string)$maxBatch]);
    $upImg = $pdo->prepare("UPDATE `{$p}recipes` SET image = ?, updated_at = ? WHERE name = ? AND image = ''");
    foreach (seed_recipes() as $r) {
        if ($r['image'] !== '') $upImg->execute([$r['image'], $now, $r['name']]);
    }
    require_once __DIR__ . '/seed-en.php';
    seed_translations($pdo, $p);
    return $added;
}

/** Default spaces created for every new household (English source, translated with tr()). */
function default_spaces(): array
{
    return [
        ['Fridge', 'fridge', 'For fresh food', 'cool'],
        ['Freezer', 'freezer', 'For frozen food', 'cold'],
        ['Pantry', 'pantry', 'For dry and long-life food', 'ambient'],
    ];
}
