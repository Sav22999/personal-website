<?php
/**
 * Fourth batch of initial content: a much wider product catalog (Italian base name + English).
 * Rows are grouped by category: [Italian name, English name, icon, default unit, storage environment (optional)].
 * Without an environment a product takes the usual one of its category (see seed_product_environment()).
 */

declare(strict_types=1);

function seed4_catalog(): array
{
    return [
        'Frutta' => [
            ['Uva', 'Grapes', 'grapes', 'kg', 'cool'], ['Fragole', 'Strawberries', 'strawberry', 'g', 'cool'], ['Ciliegie', 'Cherries', 'cherry', 'g', 'cool'],
            ['Pesche', 'Peaches', 'peach', 'pz'], ['Nettarine', 'Nectarines', 'peach', 'pz'], ['Albicocche', 'Apricots', 'peach', 'pz'], ['Prugne', 'Plums', 'plum', 'pz'],
            ['Pere', 'Pears', 'pear', 'pz'], ['Kiwi', 'Kiwis', 'kiwi', 'pz'], ['Ananas', 'Pineapple', 'pineapple', 'pz'], ['Melone', 'Melon', 'melon', 'pz'],
            ['Mango', 'Mango', 'mango', 'pz'], ['Papaya', 'Papaya', 'mango', 'pz'], ['Frutto della passione', 'Passion fruit', 'mango', 'pz'], ['Melagrana', 'Pomegranate', 'pomegranate', 'pz'],
            ['Fichi', 'Figs', 'fig', 'pz', 'cool'], ['Cachi', 'Persimmons', 'plum', 'pz'], ['Mandarini', 'Mandarins', 'citrus', 'pz'], ['Clementine', 'Clementines', 'citrus', 'pz'],
            ['Pompelmo', 'Grapefruit', 'citrus', 'pz'], ['Cocco', 'Coconut', 'coconut', 'pz'], ['Mirtilli', 'Blueberries', 'blueberry', 'g', 'cool'], ['More', 'Blackberries', 'blueberry', 'g', 'cool'],
            ['Ribes', 'Redcurrants', 'blueberry', 'g', 'cool'], ['Frutti di bosco', 'Mixed berries', 'blueberry', 'g', 'cool'], ['Datteri', 'Dates', 'fig', 'g'], ['Castagne', 'Chestnuts', 'nuts', 'g'],
            ['Prugne secche', 'Prunes', 'plum', 'g'], ['Albicocche secche', 'Dried apricots', 'peach', 'g'], ['Fichi secchi', 'Dried figs', 'fig', 'g'],
        ],
        'Verdura' => [
            ['Sedano', 'Celery', 'celery', 'pz'], ['Finocchi', 'Fennel', 'fennel', 'pz'], ['Carciofi', 'Artichokes', 'artichoke', 'pz'], ['Bietole', 'Chard', 'spinach', 'g'],
            ['Cicoria', 'Chicory', 'spinach', 'g'], ['Rucola', 'Rocket', 'spinach', 'g'], ['Lattuga', 'Lettuce', 'vegetable', 'pz'], ['Radicchio', 'Radicchio', 'cabbage', 'pz'],
            ['Valeriana', 'Lamb’s lettuce', 'spinach', 'g'], ['Scarola', 'Escarole', 'vegetable', 'pz'], ['Indivia', 'Endive', 'vegetable', 'pz'], ['Insalata in busta', 'Bagged salad', 'vegetable', 'conf'],
            ['Cavolini di Bruxelles', 'Brussels sprouts', 'cabbage', 'g'], ['Cavolo nero', 'Black kale', 'cabbage', 'g'], ['Cavolo rosso', 'Red cabbage', 'cabbage', 'pz'], ['Cime di rapa', 'Turnip tops', 'broccoli', 'g'],
            ['Scalogno', 'Shallots', 'onion', 'pz', 'ambient'], ['Cipolle rosse', 'Red onions', 'onion', 'pz', 'ambient'], ['Peperoncino fresco', 'Fresh chilli', 'chili', 'pz'], ['Pomodorini', 'Cherry tomatoes', 'tomato', 'g', 'ambient'],
            ['Rape', 'Turnips', 'beet', 'pz'], ['Topinambur', 'Jerusalem artichokes', 'potato', 'g', 'ambient'], ['Erba cipollina', 'Chives', 'herbs', 'g'], ['Salvia', 'Sage', 'herbs', 'g'],
            ['Timo', 'Thyme', 'herbs', 'g'], ['Alloro', 'Bay leaves', 'herbs', 'g', 'ambient'], ['Aneto', 'Dill', 'herbs', 'g'], ['Dragoncello', 'Tarragon', 'herbs', 'g'],
            ['Citronella', 'Lemongrass', 'herbs', 'pz'], ['Germogli di soia', 'Bean sprouts', 'spinach', 'g'], ['Fave', 'Broad beans', 'bean', 'g'], ['Piselli freschi', 'Fresh peas', 'peas', 'g'],
            ['Funghi porcini', 'Porcini mushrooms', 'mushroom', 'g'], ['Funghi champignon', 'Button mushrooms', 'mushroom', 'g'], ['Pak choi', 'Pak choi', 'spinach', 'pz'], ['Daikon', 'Daikon', 'radish', 'pz'],
        ],
        'Latticini e uova' => [
            ['Latte scremato', 'Skimmed milk', 'milk', 'l'], ['Latte parzialmente scremato', 'Semi-skimmed milk', 'milk', 'l'], ['Latte di capra', 'Goat’s milk', 'milk', 'l'], ['Latte senza lattosio', 'Lactose-free milk', 'milk', 'l'],
            ['Panna da cucina', 'Cooking cream', 'cream', 'ml'], ['Panna da montare', 'Whipping cream', 'cream', 'ml'], ['Kefir', 'Kefir', 'yogurt', 'ml'], ['Yogurt alla frutta', 'Fruit yogurt', 'yogurt', 'pz'],
            ['Budino', 'Pudding', 'yogurt', 'pz'], ['Stracchino', 'Stracchino', 'cheese', 'g'], ['Gorgonzola', 'Gorgonzola', 'cheese', 'g'], ['Grana Padano', 'Grana Padano', 'cheese', 'g'],
            ['Provolone', 'Provolone', 'cheese', 'g'], ['Fontina', 'Fontina', 'cheese', 'g'], ['Asiago', 'Asiago', 'cheese', 'g'], ['Montasio', 'Montasio', 'cheese', 'g'],
            ['Taleggio', 'Taleggio', 'cheese', 'g'], ['Scamorza', 'Scamorza', 'cheese', 'g'], ['Caciocavallo', 'Caciocavallo', 'cheese', 'g'], ['Emmental', 'Emmental', 'cheese', 'g'],
            ['Brie', 'Brie', 'cheese', 'g'], ['Camembert', 'Camembert', 'cheese', 'g'], ['Burrata', 'Burrata', 'cheese', 'pz'], ['Robiola', 'Robiola', 'cheese', 'g'],
            ['Halloumi', 'Halloumi', 'cheese', 'g'], ['Fiocchi di latte', 'Cottage cheese', 'cheese', 'g'], ['Sottilette', 'Cheese slices', 'cheese', 'conf'], ['Margarina', 'Margarine', 'butter', 'g'],
            ['Burro chiarificato', 'Ghee', 'butter', 'g'], ['Albumi', 'Egg whites', 'egg', 'ml'],
        ],
        'Carne e salumi' => [
            ['Petto di pollo', 'Chicken breast', 'chicken', 'g'], ['Cosce di pollo', 'Chicken thighs', 'chicken', 'g'], ['Ali di pollo', 'Chicken wings', 'chicken', 'g'], ['Pollo intero', 'Whole chicken', 'chicken', 'kg'],
            ['Tacchino', 'Turkey', 'chicken', 'g'], ['Anatra', 'Duck', 'chicken', 'g'], ['Coniglio', 'Rabbit', 'meat', 'g'], ['Bistecca di manzo', 'Beef steak', 'steak', 'g'], ['Arrosto di manzo', 'Beef joint', 'steak', 'kg'], ['Carne macinata di agnello', 'Minced lamb', 'meat', 'g'],
            ['Arrosto di vitello', 'Roast veal', 'steak', 'g'], ['Hamburger di manzo', 'Beef burgers', 'burger', 'pz'], ['Polpette', 'Meatballs', 'meat', 'g'], ['Costine di maiale', 'Pork ribs', 'meat', 'g'],
            ['Lonza di maiale', 'Pork loin', 'steak', 'g'], ['Spezzatino', 'Stewing meat', 'meat', 'g'], ['Fegato', 'Liver', 'meat', 'g'], ['Speck', 'Speck', 'ham', 'g'],
            ['Bresaola', 'Bresaola', 'ham', 'g'], ['Mortadella', 'Mortadella', 'ham', 'g'], ['Coppa', 'Coppa', 'ham', 'g'], ['Porchetta', 'Porchetta', 'ham', 'g'],
            ['Fesa di tacchino', 'Sliced turkey breast', 'ham', 'g'], ['Guanciale', 'Guanciale', 'bacon', 'g'], ['Bacon', 'Bacon', 'bacon', 'g'], ['’Nduja', '’Nduja', 'sausage', 'g'],
            ['Cotechino', 'Cotechino', 'sausage', 'pz'], ['Luganega', 'Luganega sausage', 'sausage', 'g'], ['Kebab', 'Kebab meat', 'meat', 'g'],
        ],
        'Pesce' => [
            ['Salmone fresco', 'Fresh salmon', 'fish', 'g'], ['Tonno fresco', 'Fresh tuna', 'fish', 'g'], ['Orata', 'Sea bream', 'fish', 'pz'], ['Branzino', 'Sea bass', 'fish', 'pz'],
            ['Sogliola', 'Sole', 'fish', 'pz'], ['Trota', 'Trout', 'fish', 'pz'], ['Sgombro', 'Mackerel', 'fish', 'pz'], ['Sardine fresche', 'Fresh sardines', 'fish', 'g'],
            ['Alici fresche', 'Fresh anchovies', 'fish', 'g'], ['Baccalà', 'Salt cod', 'fish', 'g'], ['Calamari', 'Squid', 'squid', 'g'], ['Seppie', 'Cuttlefish', 'squid', 'g'],
            ['Polpo', 'Octopus', 'squid', 'g'], ['Vongole', 'Clams', 'mussel', 'g'], ['Ostriche', 'Oysters', 'mussel', 'pz'], ['Granchio', 'Crab', 'crab', 'g'],
            ['Astice', 'Lobster', 'crab', 'pz'], ['Scampi', 'Langoustines', 'shrimp', 'g'], ['Surimi', 'Surimi', 'fish', 'g'],
        ],
        'Pane e prodotti da forno' => [
            ['Pancarrè', 'Sliced bread', 'bread', 'conf'], ['Panini', 'Bread rolls', 'sandwich', 'pz'], ['Panini per hamburger', 'Burger buns', 'burger', 'pz'], ['Baguette', 'Baguette', 'baguette', 'pz'],
            ['Focaccia', 'Focaccia', 'bread', 'g'], ['Pane di segale', 'Rye bread', 'bread', 'g'], ['Pane senza glutine', 'Gluten-free bread', 'bread', 'g'], ['Bagel', 'Bagels', 'donut', 'pz'],
            ['Grissini', 'Breadsticks', 'baguette', 'g'], ['Crackers', 'Crackers', 'biscuit', 'g'], ['Fette biscottate', 'Rusks', 'biscuit', 'conf'], ['Gallette di riso', 'Rice cakes', 'biscuit', 'conf'],
            ['Taralli', 'Taralli', 'biscuit', 'g'], ['Croissant', 'Croissants', 'croissant', 'pz'], ['Brioche', 'Brioche', 'croissant', 'pz'], ['Pan di Spagna', 'Sponge cake', 'cake', 'pz'],
            ['Impasto per pizza', 'Pizza dough', 'pizza', 'pz', 'cool'], ['Pasta brisée', 'Shortcrust pastry', 'bread', 'pz', 'cool'], ['Pasta frolla', 'Sweet shortcrust pastry', 'bread', 'pz', 'cool'],
        ],
        'Pasta, riso e cereali' => [
            ['Penne', 'Penne', 'pasta', 'g'], ['Fusilli', 'Fusilli', 'pasta', 'g'], ['Rigatoni', 'Rigatoni', 'pasta', 'g'], ['Farfalle', 'Farfalle', 'pasta', 'g'],
            ['Linguine', 'Linguine', 'pasta', 'g'], ['Tagliatelle', 'Tagliatelle', 'pasta', 'g'], ['Lasagne (sfoglie)', 'Lasagne sheets', 'pasta', 'g'], ['Pasta integrale', 'Wholemeal pasta', 'pasta', 'g'],
            ['Pasta senza glutine', 'Gluten-free pasta', 'pasta', 'g'], ['Gnocchi', 'Gnocchi', 'pasta', 'g', 'cool'], ['Tortellini', 'Tortellini', 'pasta', 'g', 'cool'], ['Ravioli', 'Ravioli', 'pasta', 'g', 'cool'],
            ['Orecchiette', 'Orecchiette', 'pasta', 'g'], ['Ziti', 'Ziti', 'pasta', 'g'], ['Farina di nocciole', 'Hazelnut flour', 'flour', 'g'], ['Ramen', 'Ramen noodles', 'noodles', 'g'], ['Soba', 'Soba noodles', 'noodles', 'g'], ['Riso basmati', 'Basmati rice', 'rice', 'g'], ['Riso carnaroli', 'Carnaroli rice', 'rice', 'g'],
            ['Riso arborio', 'Arborio rice', 'rice', 'g'], ['Riso venere', 'Black rice', 'rice', 'g'], ['Farro', 'Spelt', 'cereal', 'g'], ['Orzo', 'Pearl barley', 'cereal', 'g'],
            ['Quinoa', 'Quinoa', 'cereal', 'g'], ['Polenta istantanea', 'Instant polenta', 'flour', 'g'], ['Semola', 'Semolina', 'flour', 'g'], ['Farina 00', 'Plain flour', 'flour', 'g'],
            ['Farina integrale', 'Wholemeal flour', 'flour', 'g'], ['Farina di ceci', 'Chickpea flour', 'flour', 'g'], ['Amido di mais', 'Cornflour', 'flour', 'g'], ['Corn flakes', 'Corn flakes', 'cereal', 'g'],
            ['Muesli', 'Muesli', 'cereal', 'g'], ['Granola', 'Granola', 'cereal', 'g'],
        ],
        'Conserve e scatolame' => [
            ['Sgombro in scatola', 'Tinned mackerel', 'tuna', 'pz'], ['Sardine in scatola', 'Tinned sardines', 'tuna', 'pz'], ['Acciughe sott’olio', 'Anchovies in oil', 'tuna', 'pz'], ['Pesto', 'Pesto', 'jar', 'g'],
            ['Ragù pronto', 'Ready-made ragù', 'sauce', 'g'], ['Sugo al pomodoro', 'Tomato sauce', 'sauce', 'g'], ['Polpa di pomodoro', 'Chopped tomatoes', 'can', 'g'], ['Concentrato di pomodoro', 'Tomato purée', 'mayo', 'g'],
            ['Pomodori secchi', 'Sun-dried tomatoes', 'jar', 'g'], ['Funghi sott’olio', 'Mushrooms in oil', 'jar', 'g'], ['Carciofini sott’olio', 'Artichokes in oil', 'jar', 'g'], ['Capperi', 'Capers', 'jar', 'g'],
            ['Cetriolini sottaceto', 'Gherkins', 'jar', 'g'], ['Giardiniera', 'Pickled vegetables', 'jar', 'g'], ['Piselli in scatola', 'Tinned peas', 'can', 'g'], ['Fagioli borlotti', 'Borlotti beans', 'can', 'g'],
            ['Fagioli cannellini', 'Cannellini beans', 'can', 'g'], ['Zuppa in scatola', 'Tinned soup', 'soup', 'pz'], ['Ananas sciroppato', 'Tinned pineapple', 'can', 'pz'], ['Pesche sciroppate', 'Tinned peaches', 'can', 'pz'],
            ['Marmellata di arance', 'Orange marmalade', 'jam', 'g'], ['Confettura di fragole', 'Strawberry jam', 'jam', 'g'], ['Confettura di albicocche', 'Apricot jam', 'jam', 'g'], ['Confettura di mirtilli', 'Blueberry jam', 'jam', 'g'], ['Burro di arachidi', 'Peanut butter', 'jam', 'g'],
            ['Dado da brodo', 'Stock cubes', 'spices', 'conf'],
        ],
        'Condimenti e spezie' => [
            ['Ketchup', 'Ketchup', 'ketchup', 'ml'], ['Maionese', 'Mayonnaise', 'mayo', 'ml'], ['Salsa barbecue', 'Barbecue sauce', 'ketchup', 'ml'], ['Salsa tartara', 'Tartar sauce', 'mayo', 'ml'],
            ['Salsa Worcestershire', 'Worcestershire sauce', 'sauce', 'ml'], ['Salsa piccante', 'Hot sauce', 'chili', 'ml'], ['Wasabi', 'Wasabi', 'mayo', 'g'], ['Harissa', 'Harissa', 'chili', 'g'],
            ['Aceto di mele', 'Cider vinegar', 'vinegar', 'ml'], ['Olio di girasole', 'Sunflower oil', 'olive_oil', 'ml'], ['Olio di arachidi', 'Peanut oil', 'olive_oil', 'ml'], ['Olio di cocco', 'Coconut oil', 'olive_oil', 'ml'],
            ['Noce moscata', 'Nutmeg', 'spices', 'g'], ['Curcuma', 'Turmeric', 'spices', 'g'], ['Zenzero in polvere', 'Ground ginger', 'spices', 'g'], ['Zafferano', 'Saffron', 'spices', 'g'],
            ['Chiodi di garofano', 'Cloves', 'spices', 'g'], ['Vaniglia', 'Vanilla', 'spices', 'pz'], ['Aglio in polvere', 'Garlic powder', 'spices', 'g'], ['Semi di sesamo', 'Sesame seeds', 'seeds', 'g'],
            ['Semi di chia', 'Chia seeds', 'seeds', 'g'], ['Semi di lino', 'Flax seeds', 'seeds', 'g'], ['Semi di zucca', 'Pumpkin seeds', 'seeds', 'g'], ['Semi di girasole', 'Sunflower seeds', 'seeds', 'g'],
            ['Bicarbonato', 'Baking soda', 'salt', 'g'], ['Sale grosso', 'Coarse salt', 'salt', 'g'], ['Zucchero di canna', 'Brown sugar', 'sugar', 'g'], ['Zucchero a velo', 'Icing sugar', 'sugar', 'g'],
            ['Dolcificante', 'Sweetener', 'sugar', 'g'], ['Gelatina in fogli', 'Gelatine sheets', 'generic', 'g'],
        ],
        'Bevande' => [
            ['Acqua frizzante', 'Sparkling water', 'water', 'l'], ['Aranciata', 'Orangeade', 'soda', 'ml'], ['Cola', 'Cola', 'soda', 'ml'], ['Gassosa', 'Lemonade', 'soda', 'ml'],
            ['Bevanda energetica', 'Energy drink', 'soda', 'ml'], ['Bevanda isotonica', 'Sports drink', 'soda', 'ml'], ['Tè freddo', 'Iced tea', 'tea', 'ml'], ['Tè verde', 'Green tea', 'tea', 'conf'],
            ['Tisana', 'Herbal tea', 'tea', 'conf'], ['Camomilla', 'Chamomile tea', 'tea', 'conf'], ['Caffè in capsule', 'Coffee capsules', 'coffee', 'conf'], ['Caffè solubile', 'Instant coffee', 'coffee', 'g'],
            ['Caffè in grani', 'Coffee beans', 'coffee', 'g'], ['Orzo solubile', 'Instant barley drink', 'coffee', 'g'], ['Cioccolata calda', 'Hot chocolate', 'coffee', 'g'], ['Latte di soia', 'Soy drink', 'plant_milk', 'l'],
            ['Latte di mandorla', 'Almond drink', 'plant_milk', 'l'], ['Latte d’avena', 'Oat drink', 'plant_milk', 'l'], ['Latte di riso', 'Rice drink', 'plant_milk', 'l'], ['Spremuta d’arancia', 'Fresh orange juice', 'juice', 'ml', 'cool'],
            ['Succo d’arancia', 'Orange juice', 'juice', 'ml'], ['Succo di mela', 'Apple juice', 'juice', 'ml'], ['Smoothie', 'Smoothie', 'smoothie', 'ml', 'cool'], ['Prosecco', 'Prosecco', 'champagne', 'ml'],
            ['Vino rosato', 'Rosé wine', 'wine', 'ml'], ['Birra analcolica', 'Alcohol-free beer', 'beer', 'ml'], ['Gin', 'Gin', 'wine', 'ml'], ['Vodka', 'Vodka', 'wine', 'ml'],
            ['Whisky', 'Whisky', 'wine', 'ml'], ['Rum', 'Rum', 'wine', 'ml'], ['Amaro', 'Herbal liqueur', 'wine', 'ml'], ['Limoncello', 'Limoncello', 'wine', 'ml', 'cold'], ['Kirsch', 'Kirsch', 'wine', 'ml'],
        ],
        'Surgelati' => [
            ['Spinaci surgelati', 'Frozen spinach', 'spinach', 'g'], ['Minestrone surgelato', 'Frozen minestrone', 'soup', 'g'], ['Verdure miste surgelate', 'Frozen mixed vegetables', 'frozen', 'g'], ['Fagiolini surgelati', 'Frozen green beans', 'frozen', 'g'],
            ['Edamame', 'Edamame', 'peas', 'g'], ['Frutti di bosco surgelati', 'Frozen berries', 'blueberry', 'g'], ['Bastoncini di pesce', 'Fish fingers', 'fish', 'pz'], ['Gamberi surgelati', 'Frozen prawns', 'shrimp', 'g'],
            ['Pizza surgelata', 'Frozen pizza', 'pizza', 'pz'], ['Lasagne surgelate', 'Frozen lasagne', 'ready_meal', 'pz'], ['Crocchette di patate', 'Potato croquettes', 'frozen', 'g'], ['Nuggets di pollo', 'Chicken nuggets', 'chicken', 'g'],
            ['Ghiaccioli', 'Ice lollies', 'icecream', 'pz'], ['Torta gelato', 'Ice cream cake', 'cake', 'pz'], ['Ghiaccio', 'Ice cubes', 'frozen', 'conf'],
        ],
        'Snack e dolci' => [
            ['Cioccolato al latte', 'Milk chocolate', 'chocolate', 'g'], ['Cioccolato bianco', 'White chocolate', 'chocolate', 'g'], ['Crema alle nocciole', 'Hazelnut spread', 'jam', 'g'], ['Merendine', 'Snack cakes', 'muffin', 'pz'],
            ['Muffin', 'Muffins', 'muffin', 'pz'], ['Ciambelle', 'Doughnuts', 'donut', 'pz'], ['Torta', 'Cake', 'cake', 'pz'], ['Crostata', 'Jam tart', 'cake', 'pz'],
            ['Panettone', 'Panettone', 'cake', 'pz'], ['Pandoro', 'Pandoro', 'cake', 'pz'], ['Colomba', 'Colomba', 'cake', 'pz'], ['Preparato per pancake', 'Pancake mix', 'pancake', 'g'],
            ['Preparato per torte', 'Cake mix', 'flour', 'g'], ['Frollini', 'Shortbread biscuits', 'biscuit', 'g'], ['Biscotti al cacao', 'Cocoa biscuits', 'biscuit', 'g'], ['Wafer', 'Wafers', 'biscuit', 'g'], ['Caramelle', 'Sweets', 'candy', 'g'],
            ['Gomme da masticare', 'Chewing gum', 'candy', 'conf'], ['Liquirizia', 'Liquorice', 'candy', 'g'], ['Gelatine di frutta', 'Fruit jellies', 'candy', 'g'], ['Marshmallow', 'Marshmallows', 'candy', 'g'],
            ['Popcorn', 'Popcorn', 'popcorn', 'g'], ['Noci', 'Walnuts', 'walnut', 'g'], ['Nocciole', 'Hazelnuts', 'nuts', 'g'], ['Anacardi', 'Cashews', 'nuts', 'g'],
            ['Pistacchi', 'Pistachios', 'nuts', 'g'], ['Frutta secca mista', 'Mixed nuts', 'nuts', 'g'], ['Barrette ai cereali', 'Cereal bars', 'snack_bar', 'pz'], ['Barrette proteiche', 'Protein bars', 'snack_bar', 'pz'],
        ],
        'Altro' => [
            ['Tempeh', 'Tempeh', 'tofu', 'g', 'cool'], ['Seitan', 'Seitan', 'tofu', 'g', 'cool'], ['Burger vegetali', 'Veggie burgers', 'burger', 'pz', 'cool'], ['Hummus', 'Hummus', 'jar', 'g', 'cool'],
            ['Guacamole', 'Guacamole', 'avocado', 'g', 'cool'], ['Sushi', 'Sushi', 'sushi', 'conf', 'cool'], ['Tramezzini', 'Sandwiches', 'sandwich', 'pz', 'cool'], ['Piatto pronto', 'Ready meal', 'ready_meal', 'pz', 'cool'],
            ['Zuppa fresca', 'Fresh soup', 'soup', 'ml', 'cool'], ['Insalata di riso', 'Rice salad', 'ready_meal', 'g', 'cool'], ['Omogeneizzati', 'Baby food jars', 'baby_jar', 'pz'], ['Latte in polvere per neonati', 'Infant formula', 'baby', 'g'],
            ['Pappa ai cereali', 'Baby cereal', 'baby_jar', 'g'], ['Crocchette per cani', 'Dry dog food', 'petfood', 'kg'], ['Crocchette per gatti', 'Dry cat food', 'petfood', 'kg'], ['Scatolette per cani', 'Wet dog food', 'petfood', 'pz'],
            ['Scatolette per gatti', 'Wet cat food', 'petfood', 'pz'], ['Snack per animali', 'Pet treats', 'petfood', 'g'],
        ],
    ];
}

/** Catalog rows in the same format as the second and third batch: [Italian, English, icon, category, unit]. */
function seed4_products(): array
{
    $rows = [];
    foreach (seed4_catalog() as $category => $products) {
        foreach ($products as $p) $rows[] = [$p[0], $p[1], $p[2], $category, $p[3]];
    }
    return $rows;
}

/** Storage environment of the products of this batch that differ from their category. */
function seed4_environments(): array
{
    $map = [];
    foreach (seed4_catalog() as $products) {
        foreach ($products as $p) if (isset($p[4])) $map[$p[0]] = $p[4];
    }
    return $map;
}

/** Earlier catalog products that now have a more specific icon: name => [previous icon, new icon]. */
function seed4_icon_updates(): array
{
    return [
        'Limoni' => ['citrus', 'lemon'], 'Lime' => ['citrus', 'lemon'], 'Avocado' => ['pear', 'avocado'], 'Lamponi' => ['strawberry', 'blueberry'],
        'Cetriolo' => ['zucchini', 'cucumber'], 'Peperoncino' => ['pepper', 'chili'], 'Cavolo verza' => ['vegetable', 'cabbage'], 'Cavolo cappuccio' => ['vegetable', 'cabbage'],
        'Spinaci' => ['vegetable', 'spinach'], 'Zenzero' => ['garlic', 'ginger'], 'Tofu' => ['cheese', 'tofu'], 'Salsiccia' => ['salami', 'sausage'],
        'Würstel' => ['salami', 'sausage'], 'Pancetta' => ['ham', 'bacon'], 'Miele' => ['jar', 'honey'], 'Cozze' => ['shrimp', 'mussel'],
        'Arachidi' => ['nuts', 'peanut'], 'Mandorle' => ['nuts', 'almond'],
    ];
}
