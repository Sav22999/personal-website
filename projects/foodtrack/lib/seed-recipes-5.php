<?php
/**
 * Fifth batch of recipes: regional classics of Italy and the Alps, and home-made cakes.
 * Same format as seed-recipes-2.php (Italian base language + English, short schematic steps).
 */

declare(strict_types=1);

function seed5_recipes(): array
{
    $R = function (string $it, string $en, array $origin, int $prep, ?int $cook, int $serv, string $diff, array $desc, array $cookN, array $ing, array $steps) {
        return compact('it', 'en', 'origin', 'prep', 'cook', 'serv', 'diff', 'desc', 'cookN', 'ing', 'steps');
    };
    $pot = ['In pentola', 'In a pot'];
    $potPan = ['In pentola e in padella', 'Pot and pan'];
    $oven = ['Forno a 180 °C', 'Oven at 180 °C'];
    $fridge = ['Senza forno, poi in frigo', 'No baking, then chilled'];
    $oil = ['Olio extravergine d’oliva', 2, 'cucchiai', '', ''];
    $salt = ['Sale', null, 'q.b.', '', ''];
    return [
        // ---------------------------------------------------------------- First courses
        $R('Tagliatelle alla bolognese', 'Tagliatelle bolognese', ['IT'], 20, 150, 4, 'media',
            ['Il ragù di Bologna: poco pomodoro, pancetta e latte.', 'The ragù of Bologna: little tomato, pancetta and milk.'], $potPan,
            [['Tagliatelle', 320, 'g', '', ''], ['Carne macinata di manzo', 300, 'g', '', ''], ['Pancetta', 100, 'g', 'tritata', 'chopped'], ['Carote', 1, 'pz', '', ''], ['Sedano', 1, 'pz', '', ''], ['Cipolle', 1, 'pz', '', ''], ['Concentrato di pomodoro', 2, 'cucchiai', '', ''], ['Vino rosso', 100, 'ml', '', ''], ['Latte intero', 100, 'ml', '', ''], $salt],
            [['Rosola la pancetta con le verdure tritate.', 'Fry the pancetta with the chopped vegetables.'], ['Aggiungi la carne, rosola e sfuma con il vino.', 'Add the meat, brown it and deglaze with the wine.'], ['Unisci concentrato e poca acqua e cuoci a fuoco basso 2 ore, poi aggiungi il latte.', 'Add the tomato purée and a little water, simmer for 2 hours, then add the milk.'], ['Lessa le tagliatelle e condiscile con il ragù.', 'Boil the tagliatelle and toss with the ragù.']]),
        $R('Pasta alla genovese', 'Pasta alla genovese', ['IT'], 20, 180, 4, 'media',
            ['Ragù napoletano di cipolle e manzo, senza pomodoro.', 'Neapolitan ragù of onions and beef, with no tomato.'], $potPan,
            [['Ziti', 320, 'g', 'spezzati', 'broken'], ['Cipolle', 1, 'kg', '', ''], ['Carne di manzo a cubetti', 500, 'g', 'a pezzi grandi', 'in large pieces'], ['Carote', 1, 'pz', '', ''], ['Sedano', 1, 'pz', '', ''], ['Vino bianco', 100, 'ml', '', ''], ['Parmigiano', 50, 'g', 'grattugiato', 'grated'], ['Olio extravergine d’oliva', 4, 'cucchiai', '', ''], $salt],
            [['Rosola la carne nell’olio con carote e sedano tritati.', 'Brown the meat in the oil with chopped carrot and celery.'], ['Aggiungi le cipolle affettate e copri.', 'Add the sliced onions and cover.'], ['Cuoci a fuoco minimo per 3 ore, sfumando con il vino verso la fine.', 'Cook on the lowest heat for 3 hours, adding the wine towards the end.'], ['Lessa la pasta e condiscila con il sugo e il parmigiano.', 'Boil the pasta and toss with the sauce and parmesan.']]),
        $R('Pasta alla Norma', 'Pasta alla Norma', ['IT'], 15, 25, 2, 'facile',
            ['Melanzane fritte, pomodoro e ricotta salata.', 'Fried aubergine, tomato and salted ricotta.'], $potPan,
            [['Maccheroni', 200, 'g', '', ''], ['Melanzane', 1, 'pz', '', ''], ['Passata di pomodoro', 300, 'g', '', ''], ['Ricotta', 60, 'g', 'salata, grattugiata', 'salted, grated'], ['Basilico', null, 'q.b.', '', ''], ['Olio di semi', 100, 'ml', '', ''], $salt],
            [['Taglia le melanzane a cubetti e friggile.', 'Dice the aubergine and fry it.'], ['Cuoci la passata con sale e basilico per 15 minuti.', 'Cook the passata with salt and basil for 15 minutes.'], ['Lessa la pasta e condiscila con sugo e melanzane.', 'Boil the pasta and toss with sauce and aubergine.'], ['Completa con la ricotta.', 'Finish with the ricotta.']]),
        $R('Spaghetti alla puttanesca', 'Spaghetti puttanesca', ['IT'], 5, 15, 2, 'facile',
            ['Olive, capperi e acciughe.', 'Olives, capers and anchovies.'], $potPan,
            [['Spaghetti', 200, 'g', '', ''], ['Pomodori pelati', 300, 'g', '', ''], ['Olive', 50, 'g', 'nere', 'black'], ['Capperi', 1, 'cucchiai', '', ''], ['Acciughe sott’olio', 3, 'pz', 'filetti', 'fillets'], ['Aglio', 1, 'pz', '', ''], $oil],
            [['Sciogli le acciughe nell’olio con l’aglio.', 'Melt the anchovies in the oil with the garlic.'], ['Aggiungi pelati, olive e capperi e cuoci 10 minuti.', 'Add tomatoes, olives and capers and cook for 10 minutes.'], ['Lessa la pasta e saltala nel sugo.', 'Boil the pasta and toss it in the sauce.']]),
        $R('Orecchiette alle cime di rapa', 'Orecchiette with turnip tops', ['IT'], 10, 15, 2, 'facile',
            ['Il classico pugliese.', 'The classic from Apulia.'], $potPan,
            [['Orecchiette', 200, 'g', '', ''], ['Cime di rapa', 500, 'g', 'pulite', 'trimmed'], ['Acciughe sott’olio', 2, 'pz', 'filetti', 'fillets'], ['Aglio', 1, 'pz', '', ''], ['Peperoncino', 1, 'pz', '', ''], $oil],
            [['Lessa cime di rapa e pasta nella stessa acqua.', 'Boil the turnip tops and pasta in the same water.'], ['Sciogli acciughe, aglio e peperoncino nell’olio.', 'Melt anchovies, garlic and chilli in the oil.'], ['Scola e salta tutto in padella per 1 minuto.', 'Drain and toss everything in the pan for 1 minute.']]),
        $R('Ribollita', 'Ribollita', ['IT'], 20, 60, 4, 'facile',
            ['Zuppa toscana di cavolo nero, fagioli e pane.', 'Tuscan soup of black kale, beans and bread.'], $pot,
            [['Cavolo nero', 300, 'g', '', ''], ['Fagioli cannellini', 400, 'g', 'già cotti', 'cooked'], ['Patate', 2, 'pz', '', ''], ['Carote', 1, 'pz', '', ''], ['Cipolle', 1, 'pz', '', ''], ['Pane', 200, 'g', 'raffermo', 'stale'], ['Brodo vegetale', 1, 'l', '', ''], $oil, $salt],
            [['Rosola le verdure tagliate nell’olio.', 'Fry the chopped vegetables in the oil.'], ['Aggiungi fagioli e brodo e cuoci 45 minuti.', 'Add beans and stock and cook for 45 minutes.'], ['Unisci il pane a pezzi e lascia che si ammorbidisca.', 'Add the torn bread and let it soften.'], ['Fai riposare e scalda di nuovo prima di servire.', 'Leave to rest and reheat before serving.']]),
        $R('Canederli allo speck', 'Speck dumplings', ['IT'], 20, 15, 4, 'media',
            ['Gnocchi di pane del Trentino-Alto Adige, in brodo.', 'Bread dumplings from Trentino-South Tyrol, in broth.'], $pot,
            [['Pane', 300, 'g', 'raffermo', 'stale'], ['Speck', 100, 'g', 'a dadini', 'diced'], ['Uova', 2, 'pz', '', ''], ['Latte intero', 150, 'ml', '', ''], ['Farina', 40, 'g', '', ''], ['Erba cipollina', null, 'q.b.', '', ''], ['Brodo vegetale', 1, 'l', '', '']],
            [['Taglia il pane a cubetti e bagnalo con latte e uova.', 'Dice the bread and soak it with milk and eggs.'], ['Unisci speck, farina ed erba cipollina e lascia riposare 15 minuti.', 'Add speck, flour and chives and rest for 15 minutes.'], ['Forma le palline con le mani umide.', 'Shape into balls with wet hands.'], ['Cuoci nel brodo per 15 minuti e servi.', 'Simmer in the stock for 15 minutes and serve.']]),
        $R('Spätzle agli spinaci', 'Spinach spätzle', ['IT', 'DE'], 15, 10, 4, 'media',
            ['Gnocchetti verdi con panna e speck.', 'Little green dumplings with cream and speck.'], $potPan,
            [['Farina', 300, 'g', '', ''], ['Spinaci', 200, 'g', 'lessati e frullati', 'cooked and blended'], ['Uova', 2, 'pz', '', ''], ['Acqua', 100, 'ml', '', ''], ['Speck', 100, 'g', 'a listarelle', 'in strips'], ['Panna da cucina', 150, 'ml', '', ''], $salt],
            [['Mescola farina, spinaci, uova, acqua e sale.', 'Mix flour, spinach, eggs, water and salt.'], ['Passa l’impasto nell’apposito attrezzo sopra l’acqua bollente.', 'Press the batter through a spätzle maker into boiling water.'], ['Scolali quando salgono a galla.', 'Drain when they float.'], ['Saltali in padella con speck e panna.', 'Toss them in a pan with speck and cream.']]),
        $R('Gulaschsuppe', 'Goulash soup', ['AT', 'HU'], 20, 90, 4, 'facile',
            ['Zuppa di manzo alla paprika, con patate.', 'Beef and paprika soup with potatoes.'], $pot,
            [['Carne di manzo a cubetti', 500, 'g', '', ''], ['Cipolle', 3, 'pz', '', ''], ['Patate', 400, 'g', 'a cubetti', 'diced'], ['Paprika', 2, 'cucchiai', '', ''], ['Cumino', 1, 'cucchiaini', '', ''], ['Concentrato di pomodoro', 1, 'cucchiai', '', ''], ['Brodo vegetale', 1.2, 'l', '', ''], $oil, $salt],
            [['Rosola a lungo le cipolle affettate nell’olio.', 'Fry the sliced onions in the oil until golden.'], ['Aggiungi carne, paprika, cumino e concentrato.', 'Add meat, paprika, cumin and tomato purée.'], ['Versa il brodo e cuoci 1 ora a fuoco basso.', 'Pour in the stock and simmer for 1 hour.'], ['Unisci le patate e cuoci altri 25 minuti.', 'Add the potatoes and cook for 25 more minutes.']]),
        // ---------------------------------------------------------------- Cakes and sweets
        $R('Sachertorte', 'Sachertorte', ['AT'], 30, 50, 10, 'difficile',
            ['La torta viennese al cioccolato con confettura di albicocche.', 'The Viennese chocolate cake with apricot jam.'], $oven,
            [['Cioccolato fondente', 280, 'g', '100 g per la glassa', '100 g for the glaze'], ['Burro', 120, 'g', '', ''], ['Zucchero', 150, 'g', '', ''], ['Uova', 6, 'pz', 'tuorli e albumi separati', 'yolks and whites separated'], ['Farina', 120, 'g', '', ''], ['Confettura di albicocche', 200, 'g', '', ''], ['Panna fresca', 100, 'ml', '', '']],
            [['Monta burro, metà zucchero e tuorli, poi unisci 180 g di cioccolato fuso.', 'Beat butter, half the sugar and the yolks, then add 180 g of melted chocolate.'], ['Incorpora gli albumi montati con il resto dello zucchero e la farina.', 'Fold in the whites whisked with the remaining sugar, and the flour.'], ['Cuoci in forno 50 minuti, fai raffreddare e taglia a metà.', 'Bake for 50 minutes, cool and cut in half.'], ['Farcisci e spennella con la confettura, poi copri con cioccolato e panna sciolti.', 'Fill and brush with the jam, then cover with melted chocolate and cream.']]),
        $R('Strudel di mele', 'Apple strudel', ['AT', 'IT'], 40, 40, 8, 'media',
            ['Pasta sottilissima fatta in casa, mele, uvetta e pinoli.', 'Very thin home-made pastry, apples, raisins and pine nuts.'], $oven,
            [['Farina', 250, 'g', '', ''], ['Acqua', 100, 'ml', 'tiepida', 'warm'], ['Olio di semi', 2, 'cucchiai', '', ''], ['Mele', 1, 'kg', '', ''], ['Uvetta', 60, 'g', '', ''], ['Pinoli', 40, 'g', '', ''], ['Zucchero', 80, 'g', '', ''], ['Cannella', 1, 'cucchiaini', '', ''], ['Pangrattato', 80, 'g', 'tostato nel burro', 'toasted in butter'], ['Burro', 80, 'g', 'fuso', 'melted']],
            [['Impasta farina, acqua, olio e un pizzico di sale e fai riposare 30 minuti.', 'Knead flour, water, oil and a pinch of salt and rest for 30 minutes.'], ['Mescola mele a fette, uvetta, pinoli, zucchero e cannella.', 'Mix sliced apples, raisins, pine nuts, sugar and cinnamon.'], ['Stendi la pasta sottilissima, cospargi il pangrattato e aggiungi le mele.', 'Stretch the pastry very thin, sprinkle the breadcrumbs and add the apples.'], ['Arrotola, spennella di burro e cuoci in forno 40 minuti.', 'Roll up, brush with butter and bake for 40 minutes.']]),
        $R('Treccia mochena', 'Mòcheni braid', ['IT'], 40, 30, 8, 'media',
            ['Treccia lievitata trentina con crema e confettura di mirtilli.', 'Trentino yeast braid with custard and blueberry jam.'], $oven,
            [['Farina', 500, 'g', '', ''], ['Latte intero', 450, 'ml', '200 ml per l’impasto, 250 ml per la crema', '200 ml for the dough, 250 ml for the custard'], ['Lievito di birra', 12, 'g', '', ''], ['Zucchero', 130, 'g', '', ''], ['Burro', 80, 'g', '', ''], ['Uova', 4, 'pz', '1 intero nell’impasto, 2 tuorli nella crema, 1 per spennellare', '1 whole in the dough, 2 yolks in the custard, 1 for brushing'], ['Amido di mais', 25, 'g', '', ''], ['Confettura di mirtilli', 200, 'g', '', '']],
            [['Impasta 480 g di farina, 200 ml di latte, lievito, 60 g di zucchero, burro e un uovo; fai lievitare 2 ore.', 'Knead 480 g of flour, 200 ml of milk, yeast, 60 g of sugar, butter and an egg; leave to rise for 2 hours.'], ['Cuoci il latte rimasto con tuorli, zucchero rimasto e amido fino a una crema densa e raffredda.', 'Cook the remaining milk with yolks, remaining sugar and cornflour into a thick custard and cool.'], ['Stendi un rettangolo, metti crema e confettura al centro e taglia strisce ai lati.', 'Roll out a rectangle, put custard and jam in the middle and cut strips along the sides.'], ['Intreccia le strisce, fai lievitare 30 minuti, spennella e cuoci 30 minuti.', 'Braid the strips, leave to rise for 30 minutes, brush with egg and bake for 30 minutes.']]),
        $R('Torta bacio', 'Chocolate hazelnut kiss cake', ['IT'], 25, 35, 8, 'media',
            ['Torta al cioccolato e nocciole con crema gianduia.', 'Chocolate and hazelnut cake with gianduja cream.'], $oven,
            [['Cioccolato fondente', 200, 'g', '', ''], ['Burro', 150, 'g', '', ''], ['Zucchero', 150, 'g', '', ''], ['Uova', 4, 'pz', '', ''], ['Farina di nocciole', 150, 'g', '', ''], ['Farina', 60, 'g', '', ''], ['Lievito per dolci', 8, 'g', '', ''], ['Crema alle nocciole', 200, 'g', '', ''], ['Nocciole', 50, 'g', 'tritate', 'chopped']],
            [['Sciogli cioccolato e burro e monta uova e zucchero.', 'Melt chocolate and butter and whisk eggs and sugar.'], ['Unisci tutto con le farine e il lievito.', 'Combine everything with the flours and baking powder.'], ['Cuoci in forno 35 minuti e fai raffreddare.', 'Bake for 35 minutes and cool.'], ['Taglia a metà, farcisci con la crema e copri con crema e nocciole.', 'Cut in half, fill with the cream and top with cream and hazelnuts.']]),
        $R('Torta Pan di Stelle', 'Cocoa star biscuit cake', ['IT'], 25, null, 8, 'facile',
            ['Base di biscotti al cacao, crema di panna e nocciole.', 'A cocoa biscuit base, cream and hazelnut spread.'], $fridge,
            [['Biscotti al cacao', 300, 'g', 'tipo Pan di Stelle', 'Pan di Stelle type'], ['Burro', 120, 'g', 'fuso', 'melted'], ['Panna da montare', 300, 'ml', '', ''], ['Mascarpone', 250, 'g', '', ''], ['Zucchero a velo', 50, 'g', '', ''], ['Crema alle nocciole', 200, 'g', '', '']],
            [['Trita 250 g di biscotti, unisci il burro e compatta sul fondo di una tortiera.', 'Crush 250 g of biscuits, add the butter and press into a cake tin.'], ['Spalma la crema alle nocciole sulla base.', 'Spread the hazelnut cream over the base.'], ['Monta panna, mascarpone e zucchero e versa sopra.', 'Whip cream, mascarpone and sugar and pour on top.'], ['Lascia in frigo 3 ore e decora con i biscotti rimasti sbriciolati.', 'Chill for 3 hours and decorate with the remaining crumbled biscuits.']]),
        $R('Torta al cioccolato', 'Chocolate cake', [], 15, 35, 8, 'facile',
            ['Semplice e morbida.', 'Simple and soft.'], $oven,
            [['Cioccolato fondente', 200, 'g', '', ''], ['Burro', 120, 'g', '', ''], ['Zucchero', 150, 'g', '', ''], ['Uova', 3, 'pz', '', ''], ['Farina', 150, 'g', '', ''], ['Lievito per dolci', 8, 'g', '', '']],
            [['Sciogli cioccolato e burro.', 'Melt chocolate and butter.'], ['Monta uova e zucchero e unisci il cioccolato.', 'Whisk eggs and sugar and add the chocolate.'], ['Aggiungi farina e lievito e cuoci in forno 35 minuti.', 'Add flour and baking powder and bake for 35 minutes.']]),
        $R('Torta di carote', 'Carrot cake', ['GB-ENG', 'CH'], 20, 45, 8, 'facile',
            ['Carote e mandorle, umida e profumata.', 'Carrots and almonds, moist and fragrant.'], $oven,
            [['Carote', 300, 'g', 'grattugiate', 'grated'], ['Mandorle', 200, 'g', 'tritate', 'ground'], ['Farina', 150, 'g', '', ''], ['Zucchero', 180, 'g', '', ''], ['Uova', 3, 'pz', '', ''], ['Olio di semi', 80, 'ml', '', ''], ['Lievito per dolci', 16, 'g', '', ''], ['Limoni', 1, 'pz', 'la scorza', 'the zest']],
            [['Monta uova e zucchero e aggiungi l’olio.', 'Whisk eggs and sugar and add the oil.'], ['Unisci carote, mandorle e scorza di limone.', 'Add carrots, almonds and lemon zest.'], ['Aggiungi farina e lievito e cuoci in forno 45 minuti.', 'Add flour and baking powder and bake for 45 minutes.']]),
        $R('Linzer Torte', 'Linzer torte', ['AT'], 25, 40, 8, 'media',
            ['Crostata alle mandorle con confettura di ribes.', 'An almond tart with redcurrant jam.'], $oven,
            [['Farina', 250, 'g', '', ''], ['Mandorle', 150, 'g', 'tritate', 'ground'], ['Burro', 180, 'g', '', ''], ['Zucchero', 120, 'g', '', ''], ['Uova', 1, 'pz', '', ''], ['Cannella', 1, 'cucchiaini', '', ''], ['Confettura di fragole', 300, 'g', 'o di ribes', 'or redcurrant']],
            [['Impasta tutto tranne la confettura e fai riposare 30 minuti in frigo.', 'Knead everything except the jam and chill for 30 minutes.'], ['Stendi due terzi dell’impasto in una tortiera e aggiungi la confettura.', 'Roll two thirds of the dough into a tin and add the jam.'], ['Fai una griglia con il resto e cuoci in forno 40 minuti.', 'Make a lattice with the rest and bake for 40 minutes.']]),
        $R('Crostata di marmellata', 'Jam tart', ['IT'], 20, 35, 8, 'facile',
            ['Pasta frolla e la tua confettura preferita.', 'Shortcrust pastry and your favourite jam.'], $oven,
            [['Farina', 300, 'g', '', ''], ['Burro', 150, 'g', 'freddo', 'cold'], ['Zucchero', 120, 'g', '', ''], ['Uova', 1, 'pz', '', ''], ['Confettura di albicocche', 350, 'g', '', '']],
            [['Impasta farina, burro, zucchero e uovo e fai riposare 30 minuti in frigo.', 'Knead flour, butter, sugar and egg and chill for 30 minutes.'], ['Stendi la frolla in una tortiera e aggiungi la confettura.', 'Roll the pastry into a tin and add the jam.'], ['Decora con le strisce di impasto e cuoci in forno 35 minuti.', 'Decorate with strips of pastry and bake for 35 minutes.']]),
        $R('Ciambellone', 'Italian ring cake', ['IT'], 10, 40, 8, 'facile',
            ['Il dolce della colazione.', 'The breakfast cake.'], $oven,
            [['Farina', 300, 'g', '', ''], ['Zucchero', 200, 'g', '', ''], ['Uova', 3, 'pz', '', ''], ['Latte intero', 120, 'ml', '', ''], ['Olio di semi', 100, 'ml', '', ''], ['Lievito per dolci', 16, 'g', '', ''], ['Limoni', 1, 'pz', 'la scorza', 'the zest']],
            [['Monta uova e zucchero.', 'Whisk eggs and sugar.'], ['Aggiungi latte, olio e scorza di limone.', 'Add milk, oil and lemon zest.'], ['Unisci farina e lievito e cuoci in forno 40 minuti in uno stampo a ciambella.', 'Add flour and baking powder and bake for 40 minutes in a ring tin.']]),
        $R('Panna cotta', 'Panna cotta', ['IT'], 10, 5, 4, 'facile',
            ['Dolce al cucchiaio con frutti di bosco.', 'A spoon dessert with mixed berries.'], ['In pentola, poi in frigo', 'In a pot, then chilled'],
            [['Panna fresca', 500, 'ml', '', ''], ['Zucchero', 80, 'g', '', ''], ['Gelatina in fogli', 8, 'g', '', ''], ['Vaniglia', 1, 'pz', '', ''], ['Frutti di bosco', 150, 'g', '', '']],
            [['Ammolla la gelatina in acqua fredda.', 'Soak the gelatine in cold water.'], ['Scalda panna, zucchero e vaniglia senza far bollire.', 'Heat cream, sugar and vanilla without boiling.'], ['Sciogli la gelatina strizzata e versa negli stampini.', 'Dissolve the squeezed gelatine and pour into moulds.'], ['Lascia in frigo 4 ore e servi con i frutti di bosco.', 'Chill for 4 hours and serve with the berries.']]),
        $R('Salame di cioccolato', 'Chocolate salami', ['IT'], 15, null, 8, 'facile',
            ['Biscotti e cioccolato, senza forno.', 'Biscuits and chocolate, no baking.'], $fridge,
            [['Biscotti integrali', 250, 'g', 'o secchi', 'or plain'], ['Cioccolato fondente', 150, 'g', '', ''], ['Burro', 100, 'g', '', ''], ['Zucchero', 80, 'g', '', ''], ['Uova', 1, 'pz', 'freschissimo', 'very fresh'], ['Cacao amaro', 30, 'g', '', '']],
            [['Sciogli cioccolato e burro.', 'Melt chocolate and butter.'], ['Unisci uovo, zucchero, cacao e biscotti spezzettati.', 'Add egg, sugar, cocoa and broken biscuits.'], ['Forma un salame nella carta forno e lascia in frigo 4 ore.', 'Shape into a log in baking paper and chill for 4 hours.']]),
    ];
}
