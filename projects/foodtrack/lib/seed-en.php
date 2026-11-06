<?php
/**
 * English translations of the initial content (categories, products, recipes).
 * Keys are the Italian names used in lib/seed.php. Stored in the `i18n` JSON column as {"en": {...}}.
 */

declare(strict_types=1);

require_once __DIR__ . '/seed-recipes-2.php';
require_once __DIR__ . '/seed-recipes-3.php';

function seed_en_categories(): array
{
    return [
        'Frutta' => ['Fruit', 'Fresh fruit has no real expiry date: check its look, smell and texture. Small bruises can be cut away; mould, a fermented smell or soft dark flesh mean it is better not to eat it. Overripe fruit is perfect for smoothies, cakes and jams.'],
        'Verdura' => ['Vegetables', 'Wilted vegetables without mould can still be used cooked (soups, purées, sauces). Remove soft or dark parts; if you see mould, a bad smell or a slimy texture, throw them away.'],
        'Latticini e uova' => ['Dairy and eggs', 'For dairy and eggs the difference between “use by” and “best before” matters: the first is a safety limit, the second is only about quality. Always check smell, taste and look before eating.'],
        'Carne e salumi' => ['Meat and cold cuts', 'Fresh meat and sliced cold cuts usually say “use by”: after that date they should not be eaten. If they are about to expire, cook or freeze them right away to make them last longer.'],
        'Pesce' => ['Fish', 'Fresh fish is among the most delicate foods: respect the “use by” date. A strong ammonia smell, cloudy eyes or soft flesh mean it is no longer fresh. If you will not eat it soon, freeze it.'],
        'Pane e prodotti da forno' => ['Bread and bakery', 'Stale bread is still great: toast it, turn it into breadcrumbs or croutons, or use it in recipes like panzanella. If you see mould, throw the whole loaf away, not only the visible part.'],
        'Pasta, riso e cereali' => ['Pasta, rice and cereals', 'Dry pasta, rice and cereals say “best before”: kept dry and closed they are usually fine even months after the date. Check for insects, rancid smells or moisture.'],
        'Conserve e scatolame' => ['Canned and preserved', 'Canned food lasts a long time, even past the “best before” date, if the can is intact. Throw away swollen, rusty or deeply dented cans, or those that spurt or smell odd when opened.'],
        'Condimenti e spezie' => ['Condiments and spices', 'Oil, vinegar, salt and spices last very long. Spices lose aroma over time but do not become dangerous; oil can turn rancid (it smells and tastes like paint or stale).'],
        'Bevande' => ['Drinks', 'Drinks often say “best before”: if not much time has passed and the package is closed and intact, the product may still be fine. Once opened, drink them within a few days.'],
        'Surgelati' => ['Frozen food', 'Frozen food always kept at −18 °C stays safe for a long time, even if after the date the quality (taste, texture) may get worse. Never refreeze raw food once thawed.'],
        'Snack e dolci' => ['Snacks and sweets', 'Packaged biscuits, snacks and sweets almost always say “best before”: after the date they may be less crunchy but are generally still fine if the package is intact and there is no rancid smell.'],
        'Altro' => ['Other', 'Check the label: “use by” is a safety limit, “best before” only means that after that date the product may lose quality.'],
    ];
}

function seed_en_products(): array
{
    $b2 = [];
    foreach (array_merge(seed2_products(), seed3_products(), seed4_products()) as $p) $b2[$p[0]] = [$p[1], null];
    return array_merge(seed_en_products_1(), $b2);
}

function seed_en_products_1(): array
{
    return [
        'Mele' => ['Apples', 'Apples keep for a long time in the fridge. If they are a bit wrinkled but sound, they are perfect for cakes and compotes.'],
        'Banane' => ['Bananas', 'Bananas with dark skin are sweeter: ideal for banana bread, pancakes and smoothies. You can also peel and freeze them.'],
        'Anguria' => ['Watermelon', 'Once cut, cover the watermelon and keep it in the fridge for no more than 3–4 days.'],
        'Olive' => ['Olives', 'Olives in brine last long if they stay covered by the liquid. Throw away soft ones or those with a whitish film.'],
        'Pomodori' => ['Tomatoes', 'Very ripe tomatoes are great for sauces: just remove the bruised parts.'],
        'Passata di pomodoro' => ['Tomato purée', 'Closed, it lasts long; once opened keep it in the fridge and use it within 4–5 days.'],
        'Patate' => ['Potatoes', 'Remove sprouts and green parts (they contain solanine) before cooking. If they are soft or very wrinkled, throw them away.'],
        'Patate dolci' => ['Sweet potatoes', null],
        'Carote' => ['Carrots', 'Slightly soft carrots can be used in broths, sautés and soups.'],
        'Cipolle' => ['Onions', null],
        'Aglio' => ['Garlic', 'Sprouted garlic is still edible: remove the green germ in the middle, which is more bitter.'],
        'Porri' => ['Leeks', null],
        'Zucchine' => ['Courgettes', null],
        'Melanzane' => ['Aubergines', null],
        'Zucca' => ['Pumpkin', null],
        'Broccoli' => ['Broccoli', null],
        'Cavolfiore' => ['Cauliflower', null],
        'Asparagi' => ['Asparagus', null],
        'Funghi' => ['Mushrooms', 'Fresh mushrooms should be eaten within a few days: if they become slimy or dark, throw them away.'],
        'Mais' => ['Sweetcorn', null],
        'Piselli' => ['Peas', null],
        'Fagioli' => ['Beans', null],
        'Barbabietole' => ['Beetroot', null],
        'Ravanelli' => ['Radishes', null],
        'Basilico' => ['Basil', 'Basil darkens quickly: you can keep it blended with oil, or frozen in ice cubes.'],
        'Insalata' => ['Salad', null],
        'Latte intero' => ['Whole milk', 'Fresh milk says “use by”; UHT milk “best before” and, if closed, is often fine a few days later. If it smells sour or has lumps, do not drink it.'],
        'Latte UHT' => ['UHT milk', 'Closed UHT milk is often fine even past the “best before” date: smell and taste it. Once opened it keeps in the fridge for about 3 days.'],
        'Yogurt bianco' => ['Plain yogurt', 'Closed yogurt is often fine a few days after the date: if it is not swollen and has no mould or odd smell, taste it before throwing it away.'],
        'Yogurt greco' => ['Greek yogurt', 'Closed yogurt is often fine a few days after the date: if it is not swollen and has no mould or odd smell, taste it before throwing it away.'],
        'Mozzarella di bufala' => ['Buffalo mozzarella', 'Mozzarella must be eaten by its “use by” date. If it is sour, swollen or slimy, throw it away.'],
        'Parmigiano' => ['Parmesan', 'Aged cheeses last long: a small surface mould can be removed by cutting at least 1 cm around it.'],
        'Formaggio spalmabile' => ['Cream cheese', null],
        'Burro' => ['Butter', 'Butter turns rancid over time (sharp smell and taste): in the fridge and well wrapped it keeps for weeks; it can also be frozen.'],
        'Panna fresca' => ['Fresh cream', null],
        'Uova' => ['Eggs', 'Some people test freshness by putting the egg in a glass of water: if it sinks it is fresh, if it floats it is old and better not eaten. Eggs a few days past the date should be cooked thoroughly.'],
        'Uova biologiche' => ['Organic eggs', 'Some people test freshness by putting the egg in a glass of water: if it sinks it is fresh, if it floats it is old and better not eaten. Eggs a few days past the date should be cooked thoroughly.'],
        'Gelato' => ['Ice cream', null],
        'Carne di pollo' => ['Chicken', 'Raw chicken is very delicate: do not eat it after the “use by” date. If it is about to expire, cook or freeze it right away.'],
        'Prosciutto crudo' => ['Prosciutto', 'Sliced prosciutto in trays must be eaten by the date; if it loses colour or smells bad, throw it away.'],
        'Prosciutto cotto' => ['Cooked ham', 'Sliced cooked ham is delicate: if it becomes slimy or smells sour, do not eat it.'],
        'Salame' => ['Salami', 'Whole aged salami lasts long; a white “noble” mould on the casing is normal.'],
        'Pesce fresco' => ['Fresh fish', null],
        'Salmone affumicato' => ['Smoked salmon', 'Smoked salmon must be eaten by its “use by” date and, once opened, within 1–2 days.'],
        'Tonno in scatola' => ['Canned tuna', 'Canned tuna lasts years if the can is intact. Once opened, move it to a container and eat it within 2–3 days.'],
        'Pane integrale' => ['Wholemeal bread', 'Stale bread is perfect for bruschetta, breadcrumbs or panzanella. If you see mould, throw it all away.'],
        'Piadina' => ['Piadina flatbread', null],
        'Farina' => ['Flour', 'Flour lasts long even past the “best before” date if kept dry; throw it away if it smells rancid or has moths.'],
        'Zucchero' => ['Sugar', 'Sugar does not expire if kept dry.'],
        'Sale' => ['Salt', 'Salt does not expire.'],
        'Lievito di birra' => ['Fresh yeast', 'Expired fresh yeast loses strength: dissolve it in warm water with a pinch of sugar; if it foams after 10 minutes, it still works.'],
        'Lievito per dolci' => ['Baking powder', null],
        'Pinoli' => ['Pine nuts', null],
        'Pasta di semola' => ['Dry pasta', 'Dry pasta says “best before”: if stored well it is fine even many months later.'],
        'Pasta all’uovo' => ['Egg pasta', 'Fresh egg pasta must be eaten by its date; dry egg pasta keeps for a long time.'],
        'Riso integrale' => ['Brown rice', 'Brown rice has more fat than white rice and can turn rancid: if it smells stale, throw it away.'],
        'Biscotti integrali' => ['Wholemeal biscuits', null],
        'Patatine' => ['Crisps', null],
        'Patatine fritte surgelate' => ['Frozen fries', null],
        'Caffè macinato' => ['Ground coffee', 'Expired ground coffee is not harmful but loses aroma: keep it closed, away from light and moisture.'],
        'Acqua' => ['Water', null],
        'Succo di frutta' => ['Fruit juice', 'Juices often say “best before”: if not much time has passed and the package is closed and intact, the product may still be fine.'],
        'Vino rosso' => ['Red wine', null],
        'Vino bianco' => ['White wine', null],
        'Spumante' => ['Sparkling wine', null],
        'Olio extravergine d’oliva' => ['Extra virgin olive oil', null],
        'Olio di semi' => ['Seed oil', null],
        'Aceto balsamico' => ['Balsamic vinegar', 'Vinegar practically never expires: a deposit at the bottom is normal.'],
    ];
}

function seed_en_recipes(): array
{
    $b2 = [];
    foreach (array_merge(seed2_recipes(), seed3_recipes(), seed4_recipes(), seed5_recipes(), seed6_recipes()) as $r) {
        $notes = [];
        foreach ($r['ing'] as $i) if ($i[3] !== '' && $i[4] !== '') $notes[$i[3]] = $i[4];
        $b2[$r['it']] = [$r['en'], $r['desc'][1], $r['cookN'][1], array_map(function ($st) {
            return $st[1];
        }, $r['steps']), $notes];
    }
    return array_merge(seed_en_recipes_1(), $b2);
}

function seed_en_recipes_1(): array
{
    // Italian name => [name, description, cooking notes, steps, ingredient notes (Italian => English)]
    return [
        'Pizza Margherita' => ['Margherita pizza', 'The classic home-made pizza with tomato, buffalo mozzarella and basil.', 'Conventional oven, 250 °C for 15 minutes', [
            'Make the dough: dissolve the yeast in the warm water, add the flour, then the salt and the oil, and knead for 10 minutes.',
            'Let the dough rise, covered, for at least 2 hours, until it doubles in size.',
            'Roll out the dough on an oiled baking tray.',
            'Spread the tomato, seasoned with a drizzle of oil and a pinch of salt.',
            'Bake for 10 minutes, then add the mozzarella and bake for 5 more minutes.',
            'Finish with fresh basil.',
        ], ['tiepida' => 'warm', 'ben scolata' => 'well drained']],
        'Banana Bread' => ['Banana bread', 'A soft cake, perfect to use up overripe bananas.', 'Conventional oven, 180 °C for 45–50 minutes', [
            'Mash the bananas in a bowl with a fork.',
            'Add eggs, sugar and oil, and mix well.',
            'Add the sifted flour and baking powder, and mix until smooth.',
            'Pour the batter into a loaf tin lined with baking paper.',
            'Bake: check with a toothpick before taking it out.',
        ], ['le banane devono essere mature' => 'the bananas must be ripe']],
        'Pancake' => ['Pancakes', 'Fluffy pancakes for a special breakfast.', 'Cooked in a pan over medium heat', [
            'Mix the dry ingredients: flour, sugar and baking powder.',
            'In another bowl whisk eggs, milk and melted butter.',
            'Combine the two mixtures without forming lumps.',
            'Pour a small ladle of batter into a hot non-stick pan.',
            'Flip the pancake when bubbles appear on the surface and cook for one more minute.',
        ], ['fuso' => 'melted']],
        'Pasta al pesto' => ['Pasta with pesto', 'Pasta with home-made basil pesto, ready in 25 minutes.', 'Cooked in a pot of boiling water', [
            'Make the pesto: blend basil, pine nuts, garlic, parmesan and oil with a pinch of salt.',
            'Cook the pasta in plenty of salted water.',
            'Drain the pasta, keeping a little cooking water aside.',
            'Toss with the pesto, adding a spoonful of cooking water to make it creamy.',
        ], ['foglie fresche' => 'fresh leaves', 'grattugiato' => 'grated', 'uno spicchio' => 'one clove']],
        'Pasta con le cipolle' => ['Pasta with onions', 'A simple, cheap first course, sweet and tasty.', 'Cooked in a pan over low heat', [
            'Slice the onions thinly.',
            'Cook the onions in a pan with the oil over low heat for 20 minutes, adding a little water if needed.',
            'Cook the pasta in plenty of salted water.',
            'Drain the pasta and toss it with the onions.',
            'Finish with grated parmesan.',
        ], ['grattugiato' => 'grated']],
        'Piadina con pomodori e prosciutto' => ['Piadina with tomatoes and prosciutto', 'A quick lunch, ready in 10 minutes.', 'Cooked in a pan', [
            'Warm the piadina in a pan, one minute per side.',
            'Add the prosciutto and the sliced mozzarella.',
            'Add the thinly sliced tomatoes.',
            'Fold the piadina in half and serve right away.',
        ], []],
        'Cheesecake' => ['Cheesecake', 'No-bake cheesecake, fresh and creamy.', 'No baking: chill in the fridge for at least 4 hours', [
            'Make the base: crush the biscuits and mix them with the melted butter.',
            'Press into the tin and chill for 30 minutes.',
            'Mix the filling: cream cheese, yogurt and sugar, then fold in the whipped cream.',
            'Pour the cream over the base and level it.',
            'Chill for at least 4 hours before serving.',
        ], ['fuso' => 'melted']],
    ];
}

/** Fills the English translation of the seed content where it is still missing. */
function seed_translations(PDO $pdo, string $p): void
{
    $setI = function (string $table, int $id, ?string $current, array $en) use ($pdo, $p) {
        $data = $current ? (json_decode($current, true) ?: []) : [];
        if (!empty($data['en'])) return;
        $data['en'] = array_filter($en, function ($v) {
            return $v !== null && $v !== '' && $v !== [];
        });
        $pdo->prepare("UPDATE `{$p}{$table}` SET i18n = ? WHERE id = ?")->execute([json_encode($data, JSON_UNESCAPED_UNICODE), $id]);
    };
    $cats = seed_en_categories();
    foreach ($pdo->query("SELECT id, name, i18n FROM `{$p}categories`") as $r) {
        if (isset($cats[$r['name']])) $setI('categories', (int)$r['id'], $r['i18n'], ['name' => $cats[$r['name']][0], 'expiry_tip' => $cats[$r['name']][1]]);
    }
    $prods = seed_en_products();
    foreach ($pdo->query("SELECT id, name, i18n FROM `{$p}products`") as $r) {
        if (isset($prods[$r['name']])) $setI('products', (int)$r['id'], $r['i18n'], ['name' => $prods[$r['name']][0], 'expiry_tip' => $prods[$r['name']][1]]);
    }
    $recs = seed_en_recipes();
    foreach ($pdo->query("SELECT id, name, i18n FROM `{$p}recipes`") as $r) {
        if (!isset($recs[$r['name']])) continue;
        [$name, $desc, $cook, $steps, $notes] = $recs[$r['name']];
        $setI('recipes', (int)$r['id'], $r['i18n'], ['name' => $name, 'description' => $desc, 'cook_notes' => $cook, 'steps' => $steps, 'status' => 'published']);
        $st = $pdo->prepare("SELECT id, notes, i18n FROM `{$p}recipe_ingredients` WHERE recipe_id = ?");
        $st->execute([$r['id']]);
        foreach ($st->fetchAll() as $ing) {
            if ($ing['notes'] !== '' && isset($notes[$ing['notes']])) $setI('recipe_ingredients', (int)$ing['id'], $ing['i18n'], ['notes' => $notes[$ing['notes']]]);
        }
    }
}
