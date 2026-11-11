/* Forkville — texts in Italian and English. Error codes from the API are err_<code>. */

const STRINGS = {
    en: {
        tagline: 'Open your own little restaurant, cook, decorate and visit your friends.',
        play: 'Play', signin: 'Sign in', signup: 'Create account', or: 'or', google_soon: 'Google sign-in is not set up yet',
        name: 'Your name', username: 'Username (public)', username_hint: '3–20 letters, numbers or _. It is the only thing other players see.',
        email: 'Email', password: 'Password', password_hint: 'At least 8 characters', privacy_accept: 'I have read the <a href="privacy.html" target="_blank">privacy notice</a>',
        have_account: 'Already have an account?', no_account: 'New here?', loading: 'Loading…', logout: 'Sign out',
        verify_title: 'Check your inbox', verify_body: 'We sent a link to <b>{email}</b>. Open it to confirm your address: your restaurant opens right after.',
        verify_resend: 'Send the link again', verify_sent: 'Link sent again.', verify_check: 'I have confirmed', verify_still: 'Not confirmed yet: open the link in the email.',
        verify_signin_hint: 'Already confirmed on another device? Sign in here.', verify_nomail: 'Email sending is not configured on this server, so the link cannot arrive. Please contact the site owner.',
        verify_link_title: 'Confirm your email', verify_link_body: 'One tap and your restaurant is ready.', verify_link_btn: 'Confirm my email', verify_done: 'Email confirmed. Welcome to Forkville!',
        google_finish: 'Almost there', google_finish_body: 'Choose the username other players will see.', continue: 'Continue', cancel: 'Cancel', save: 'Save', close: 'Close', back: 'Back',
        // HUD
        level: 'Level', coins: 'coins', popularity: 'Popularity', open_status: 'Open · guests: {n}', closed_status: 'Closed', top_view: 'Top view', free_view: '3D view',
        rotate_left: 'Turn left', rotate_right: 'Turn right', zoom_in: 'Zoom in', zoom_out: 'Zoom out', recenter: 'Recenter',
        dock_build: 'Build', dock_menu: 'Menu', dock_garden: 'Garden', dock_market: 'Market', dock_staff: 'Staff', dock_friends: 'Friends', dock_board: 'Ranking', account: 'Account',
        need_stove: 'Place a stove', need_counter: 'Place a counter', need_seat: 'Place a chair facing a table', need_chef: 'Hire a chef', need_waiter: 'Hire a waiter', need_menu: 'Put a dish on the menu',
        // Build
        build_title: 'Build mode', cat_table: 'Tables', cat_chair: 'Chairs', cat_kitchen: 'Kitchen', cat_deco: 'Decor', cat_floor: 'Floors', cat_wall: 'Walls',
        spend: 'Spend', refund: 'Refund', rotate: 'Rotate', move: 'Move', sell: 'Sell', placing: 'Tap a tile to place it', owned: 'Owned', locked_level: 'Level {n}',
        enlarge: 'Enlarge to {n}×{n}', enlarge_cost: '{c} coins · level {l}', max_size: 'Your restaurant is as big as it gets', no_room: 'No room there',
        door_hint: 'The blue tile is the door: keep it free.', pop: '+{n} popularity', speed: 'cooks ×{n}', saved: 'Restaurant saved',
        // Menu / recipes
        menu_title: 'Menu and recipes', on_menu: 'On the menu', menu_slots: '{n}/{m} dishes on the menu', learn: 'Learn', upgrade: 'Improve', price: '{c} coins',
        max_dish: 'Top level', not_learned: 'To learn', pantry: 'Pantry', pantry_empty: 'No ingredients yet: grow them in the garden or buy them at the market.',
        wish: 'Ask friends for', wish_none: 'Nothing', learned: 'New recipe: {d}!', improved: '{d} is now level {n}!',
        // Garden
        garden_title: 'Garden', plant: 'Plant', harvest: 'Harvest', harvest_all: 'Harvest everything ripe', ready: 'Ripe!', grows_in: 'Ready in {t}', empty_plot: 'Empty bed',
        choose_crop: 'What do you want to grow?', seed: 'Seeds {c} · {t} · ×{y}', harvested: 'Harvested: {list}',
        // Market
        market_title: 'Market', market_hint: 'Fresh every day. Each ingredient: up to {n} a day.', buy: 'Buy', buy_n: 'Buy {n}', left: '{n} left today', sold_out: 'Sold out today',
        // Staff
        staff_title: 'Staff', chefs: 'Chefs', waiters: 'Waiters', hire_chef: 'Hire a chef', hire_waiter: 'Hire a waiter', fire: 'Let go', chef_hint: 'One chef per stove.', waiter_hint: 'Waiters allowed at your level: {n}.',
        // Friends
        friends_title: 'Friends', search_ph: 'Find a player by username', add: 'Add', requested: 'Requested', friend: 'Friend', requests: 'Requests', accept: 'Accept', decline: 'Decline',
        no_friends: 'No friends yet. Search for a username to add someone.', visit: 'Visit', visit_bonus: 'Visit +{c}', visited: 'Visited today', gift: 'Gift', gifted: 'Gift sent',
        gift_title: 'Send a free ingredient to {u}', gift_sent: 'Gift sent to {u}!', wants: 'wants {i}', remove: 'Remove', remove_confirm: 'Remove {u} from your friends?',
        inbox: 'Gifts', claim_all: 'Take all', gift_from: 'from {u}', got: 'You got {list}', inbox_empty: 'No gifts waiting.',
        visiting: 'Visiting {u}', back_home: 'Back home', rate_it: 'Rate this restaurant', rated: 'Thanks for your rating!', visit_reward: 'Visit bonus: +{c} coins',
        // Board
        board_title: 'Ranking', scope_friends: 'Friends', scope_global: 'Everyone', period_week: 'This week', period_all: 'All time', week_value: '{n} coins', all_value: '{n} popularity', board_empty: 'Nobody here yet.',
        // Account
        account_title: 'Account', admin_panel: 'Admin panel', err_not_on_sale: 'Not on sale right now.', restaurant_name: 'Restaurant name', language: 'Language', change_password: 'Change password', current_password: 'Current password', new_password: 'New password',
        delete_account: 'Delete account', delete_confirm: 'This deletes your restaurant and everything in it, forever. Type your password to confirm.', privacy: 'Privacy',
        away_title: 'While you were away', away_body: '{n} customers in {m} minutes earned you <b>{c} coins</b>.', level_up: 'Level {n}! New items and dishes unlocked.',
        credit_by: 'Made by', credit_ai: 'Built with Claude',
        // Sims-style additions
        cat_lounge: 'Lounge & fun', cat_rug: 'Rugs', cleaners: 'Cleaners', hire_cleaner: 'Hire a cleaner', cleaner_hint: 'They clear tables and pick up litter. Up to {n} at your level.',
        trait: 'Trait', trait_cheerful: 'Cheerful', trait_speedy: 'Speedy', trait_tireless: 'Tireless', trait_careful: 'Careful', trait_chatty: 'Chatty', trait_calm: 'Calm',
        trait_cheerful_d: 'Customers they serve are happier.', trait_speedy_d: 'Walks 25% faster.', trait_tireless_d: 'Gets tired half as fast.', trait_careful_d: 'Cooks a little slower, never in a rush.',
        trait_chatty_d: 'Takes longer orders, but customers love it.', trait_calm_d: 'Keeps cool: tires a bit slower.',
        mood: 'Mood', energy: 'Energy', doing: 'Now', ordered: 'Ordered',
        act_idle: 'Waiting for something to do', act_arriving: 'Coming in', act_wantOrder: 'Wants to order', act_waitFood: 'Waiting for the food', act_eating: 'Eating',
        act_leaving: 'Leaving', act_serve: 'Serving a dish', act_order: 'Taking an order', act_cook: 'Cooking', act_clean: 'Clearing a table', act_wash: 'Washing up', act_mop: 'Picking up litter', act_rest: 'Taking a break',
        pie_greet: 'Greet', pie_joke: 'Tell a joke', pie_dessert: 'Offer a sweet', pie_cheer: 'Cheer on', pie_break: 'Send on a break', pie_praise: 'Praise',
        pie_clean: 'Clean', pie_music: 'Play music', pie_fun: 'Turn on', pie_cozy: 'Light the fire', pie_water: 'Water',
        act_greet: 'A warm welcome!', act_joke_ok: 'They loved the joke!', act_joke_bad: 'That joke fell flat…', act_dessert: 'A sweet on the house!', act_cheer: 'Full of energy again!', act_break: 'Off for a break as soon as possible.',
        act_praise: 'They beam with pride!', act_clean: 'Spotless!', act_music: 'Music fills the room', act_fun: 'Something to watch while waiting', act_cozy: 'A cozy fire is crackling', act_water: 'The plant perks up',
        status_open: 'Open', status_paused: 'No new guests', status_closed: 'Closed', status_title: 'Restaurant status', closed_status: 'Closed', paused_status: 'Not taking new guests · {n} inside',
        speed_pause: 'Pause', speed_1: 'Normal speed', speed_2: 'Fast', speed_3: 'Very fast',
        season_spring: 'Spring', season_summer: 'Summer', season_autumn: 'Autumn', season_winter: 'Winter',
        weather_clear: 'Clear', weather_cloudy: 'Cloudy', weather_rain: 'Rain', weather_snow: 'Snow',
        dock_reviews: 'Reviews', reviews_title: 'Reviews', reputation: 'Reputation', reviews_window: 'Average of the last {n} reviews', reviews_none: 'No reviews yet: serve some customers!',
        praised: 'What customers love', blamed: 'What to improve', how_improve: 'How to improve',
        f_fast: 'fast service', f_slow: 'slow service', f_tasty: 'delicious food', f_plain: 'plain food', f_pretty: 'lovely decor', f_bare: 'bare room', f_clean: 'spotless', f_dirty: 'dirty tables or litter',
        f_ambience: 'great atmosphere', f_friendly: 'friendly staff', f_dark: 'too dark in the evening', f_cozy: 'cozy evening',
        tip_slow: 'Hire another waiter or chef, add stoves, or a coffee machine and sofa so tired staff recover.', tip_plain: 'Improve your dishes in the Menu with ingredients: higher levels taste better.',
        tip_bare: 'Put decorations, plants and rugs near the tables: customers rate the beauty around their seat.', tip_dirty: 'Hire a cleaner, add a sink and trash bins, or click dirty tables and litter to clean them.',
        tip_dark: 'Add lamps, lanterns or candles: in the evening customers like a well-lit room.', tip_good: 'Keep it up! Music, a TV and greeting your guests make them even happier.',
        review_good: '{a}!', review_but: 'But {b}.', review_only_bad: 'Sadly, {b}.', review_neutral: 'A nice meal, nothing special.',
        levels_title: 'Levels', level_title: 'Level {n} · {t}', unlocks: 'Unlocks', reward: 'Reward', level_up_title: 'Level {n}!', level_up_body: 'You are now a {t}. You received {c} coins.',
        unlock_items: 'New items', unlock_dishes: 'New dishes', unlock_crops: 'New crops', unlock_size: 'Enlarge to {n}×{n}', unlock_waiters: 'Up to {n} waiters', unlock_cleaners: 'Up to {n} cleaners', unlock_menu: '{n} dishes on the menu', unlock_plots: '{n} garden beds', current: 'You are here',
        title_kiosk: 'Kiosk', title_snack_bar: 'Snack bar', title_trattoria: 'Trattoria', title_bistro: 'Bistro', title_restaurant: 'Restaurant', title_fine_dining: 'Fine dining', title_gourmet: 'Gourmet house', title_legend: 'Legend of Forkville',
        saved_ok: 'Saved', customer: 'Customer', room_clean: 'Cleanliness',
        managers: 'Managers', hire_manager: 'Hire a manager', manager: 'Manager', manager_what: 'A manager runs the restaurant while you are away, as if the game were open.',
        manager_hint: 'While you are away they run the restaurant as if the game were open. Up to {n} at your level · now {h} hours covered.',
        manager_rank: 'covers {h} hours', promote: 'Promote', promoted: '{n} has been promoted!', fire_confirm: 'Let {n} go?', unlock_managers: 'Up to {n} managers',
        away_managed: 'Your managers ran the restaurant for {m} minutes.',
        away_slot_manager: 'From <b>{a}</b> to <b>{b}</b> your managers ran the restaurant ({p}% of the live pace).',
        away_slot_alone: 'From <b>{a}</b> to <b>{b}</b> the staff carried on without you, at a slow pace ({p}% of the live pace).',
        away_slot_closed: 'After <b>{a}</b> the restaurant stood still: nobody was running it.',
        away_tip_hire: 'Hire a manager (from level 6) to keep the restaurant running at a good pace while you are away.',
        away_tip_promote: 'Promote your managers or hire another one to cover more hours while you are away.',

        staff_intro: 'Hire people looking for a job, then give them a role: you can change it any time. Each CV shows their skill (0–10) as cook, waiter, cleaner and manager; it grows with experience.',
        find_staff: 'See who is looking for a job', per_stove: 'one per stove', role_changed: '{n} is now {r}', candidates_title: 'Looking for a job', candidates_intro: 'Pick someone and the role you hire them for. Better CVs ask for more.',
        candidates_none: 'Nobody else today. Come back tomorrow or refresh the list.', candidates_refresh: 'New candidates', hire: 'Hire', hired: '{n} joins the team as {r}!',
        ledger_title: 'Coins: daily statement', ledger_readonly: 'What you earned and spent, day by day. This is only a record: nothing can be undone from here.', ledger_in: 'Earned', ledger_out: 'Spent', ledger_net: 'Balance of the day',
        ledger_empty: 'No coins moved on this day.', ledger_moves: 'Movements',
        lk_customers: 'Customers', lk_tips: 'Tips', lk_away: 'While you were away', lk_level: 'Level rewards', lk_visit: 'Visits to friends', lk_visited: 'Friends visiting you', lk_admin: 'Adjustments',
        lk_layout: 'Furniture and decor', lk_hire: 'Hiring', lk_promote: 'Promotions', lk_expand: 'Enlargements', lk_market: 'Market', lk_plant: 'Seeds', lk_style: 'Uniforms and hairdresser', lk_candidates: 'New candidates', lk_fire: 'Staff',
        lt_customers: '{n} customers served', lt_away: '{n} customers while you were away', lt_level: 'Reward for level {n}', lt_hire: 'Hired {n} as {r}', lt_market: 'Market: {i} ×{q}', lt_plant: 'Seeds: {i}', lt_expand: 'Restaurant enlarged to {n}×{n}',
        lt_promote: 'Manager promoted to rank {n}', lt_visit: 'Visit to {n}', lt_admin: 'Adjustment by an administrator',
        look_title: 'Look', look_of: '{n}’s look', look_cost: 'Cost of the changes', uniform: 'Uniform', hairstyle: 'Hairstyle', free: 'Free', look_done: '{n} has a new look!',
        uni_default: 'Role uniform', uni_terracotta: 'Terracotta apron', uni_stripes: 'Breton stripes', uni_olive: 'Olive waistcoat', uni_mint: 'Mint apron', uni_berry: 'Berry waistcoat', uni_sailor: 'Sailor', uni_denim: 'Denim dungarees', uni_tuxedo: 'Tuxedo', uni_gold: 'Golden tuxedo',
        hair_short: 'Short', hair_bob: 'Bob', hair_bun: 'Bun', hair_ponytail: 'Ponytail', hair_curly: 'Curly', hair_spiky: 'Spiky', hair_long: 'Long', hair_bald: 'Shaved',
        tier_1: 'Budget', tier_2: 'Standard', tier_3: 'Premium', tier_4: 'Luxury', f_comfy: 'comfortable seats',



        // Errors
        err_network: 'No connection. Try again.', err_server: 'Something went wrong on the server.', err_bad_credentials: 'Wrong email or password.', err_email_taken: 'This email already has an account.',
        err_username_taken: 'This username is taken.', err_username_invalid: 'Username: 3–20 letters, numbers or _.', err_password_short: 'The password needs at least 8 characters.',
        err_email_invalid: 'This email doesn’t look right.', err_name_required: 'Write your name.', err_privacy_required: 'Please accept the privacy notice.', err_rate_limited: 'Too many attempts. Wait a little.',
        err_use_google: 'This account uses Google sign-in.', err_token_invalid: 'This link has expired or was already used.', err_no_coins: 'Not enough coins.', err_no_ingredients: 'Missing ingredients.',
        err_level_locked: 'You need a higher level.', err_layout_overlap: 'Two pieces overlap.', err_layout_outside: 'A piece is outside the room.', err_need_stove: 'Each chef needs a stove: place one first.',
        err_staff_full: 'You have all the waiters your level allows.', err_staff_last: 'You need at least one.', err_menu_full: 'The menu is full.', err_menu_empty: 'Keep at least one dish on the menu.',
        err_not_ripe: 'Nothing is ripe yet.', err_plot_busy: 'This bed is already planted.', err_market_limit: 'Daily limit reached for this ingredient.', err_already_gifted: 'You already sent a gift to this friend today.',
        err_already_rated: 'You already rated today.', err_not_found: 'Player not found.', err_not_friends: 'You are not friends.', err_bad_password: 'Wrong password.', err_email_unverified: 'Confirm your email first.',
        err_google_token: 'Google sign-in failed.', err_not_installed: 'The game is not installed on this server yet.', err_max_size: 'Your restaurant is as big as it gets.', err_csrf: 'Session expired: reload the page.',
    },
    it: {
        tagline: 'Apri il tuo ristorantino, cucina, arreda e vai a trovare gli amici.',
        play: 'Gioca', signin: 'Accedi', signup: 'Crea account', or: 'oppure', google_soon: 'L’accesso con Google non è ancora configurato',
        name: 'Il tuo nome', username: 'Username (pubblico)', username_hint: '3–20 lettere, numeri o _. È l’unica cosa che vedono gli altri giocatori.',
        email: 'Email', password: 'Password', password_hint: 'Almeno 8 caratteri', privacy_accept: 'Ho letto l’<a href="privacy.html" target="_blank">informativa privacy</a>',
        have_account: 'Hai già un account?', no_account: 'Sei nuovo?', loading: 'Caricamento…', logout: 'Esci',
        verify_title: 'Controlla la posta', verify_body: 'Abbiamo mandato un link a <b>{email}</b>. Aprilo per confermare l’indirizzo: subito dopo si apre il tuo ristorante.',
        verify_resend: 'Rimanda il link', verify_sent: 'Link inviato di nuovo.', verify_check: 'Ho confermato', verify_still: 'Non ancora confermato: apri il link nell’email.',
        verify_signin_hint: 'Hai già confermato su un altro dispositivo? Accedi qui.', verify_nomail: 'Su questo server l’invio delle email non è configurato, quindi il link non può arrivare. Contatta il gestore del sito.',
        verify_link_title: 'Conferma la tua email', verify_link_body: 'Un tocco e il tuo ristorante è pronto.', verify_link_btn: 'Conferma la mia email', verify_done: 'Email confermata. Benvenuto a Forkville!',
        google_finish: 'Ci siamo quasi', google_finish_body: 'Scegli lo username che vedranno gli altri giocatori.', continue: 'Continua', cancel: 'Annulla', save: 'Salva', close: 'Chiudi', back: 'Indietro',
        level: 'Livello', coins: 'monete', popularity: 'Popolarità', open_status: 'Aperto · clienti: {n}', closed_status: 'Chiuso', top_view: 'Vista dall’alto', free_view: 'Vista 3D',
        rotate_left: 'Ruota a sinistra', rotate_right: 'Ruota a destra', zoom_in: 'Avvicina', zoom_out: 'Allontana', recenter: 'Ricentra',
        dock_build: 'Arreda', dock_menu: 'Menù', dock_garden: 'Orto', dock_market: 'Mercato', dock_staff: 'Staff', dock_friends: 'Amici', dock_board: 'Classifica', account: 'Account',
        need_stove: 'Metti un fornello', need_counter: 'Metti un bancone', need_seat: 'Metti una sedia rivolta verso un tavolo', need_chef: 'Assumi un cuoco', need_waiter: 'Assumi un cameriere', need_menu: 'Metti un piatto nel menù',
        build_title: 'Modalità arredo', cat_table: 'Tavoli', cat_chair: 'Sedie', cat_kitchen: 'Cucina', cat_deco: 'Decori', cat_floor: 'Pavimenti', cat_wall: 'Pareti',
        spend: 'Spesa', refund: 'Rimborso', rotate: 'Ruota', move: 'Sposta', sell: 'Vendi', placing: 'Tocca una casella per posizionarlo', owned: 'Già tuo', locked_level: 'Livello {n}',
        enlarge: 'Allarga a {n}×{n}', enlarge_cost: '{c} monete · livello {l}', max_size: 'Il tuo ristorante è già al massimo', no_room: 'Lì non c’è spazio',
        door_hint: 'La casella blu è la porta: lasciala libera.', pop: '+{n} popolarità', speed: 'cuoce ×{n}', saved: 'Ristorante salvato',
        menu_title: 'Menù e ricette', on_menu: 'Nel menù', menu_slots: '{n}/{m} piatti nel menù', learn: 'Impara', upgrade: 'Migliora', price: '{c} monete',
        max_dish: 'Livello massimo', not_learned: 'Da imparare', pantry: 'Dispensa', pantry_empty: 'Ancora nessun ingrediente: coltivali nell’orto o comprali al mercato.',
        wish: 'Chiedi agli amici', wish_none: 'Niente', learned: 'Nuova ricetta: {d}!', improved: '{d} ora è al livello {n}!',
        garden_title: 'Orto', plant: 'Pianta', harvest: 'Raccogli', harvest_all: 'Raccogli tutto ciò che è maturo', ready: 'Maturo!', grows_in: 'Pronto tra {t}', empty_plot: 'Aiuola vuota',
        choose_crop: 'Cosa vuoi coltivare?', seed: 'Semi {c} · {t} · ×{y}', harvested: 'Raccolto: {list}',
        market_title: 'Mercato', market_hint: 'Fresco ogni giorno. Per ogni ingrediente: fino a {n} al giorno.', buy: 'Compra', buy_n: 'Compra {n}', left: 'ne restano {n} oggi', sold_out: 'Esaurito per oggi',
        staff_title: 'Staff', chefs: 'Cuochi', waiters: 'Camerieri', hire_chef: 'Assumi un cuoco', hire_waiter: 'Assumi un cameriere', fire: 'Congeda', chef_hint: 'Un cuoco per ogni fornello.', waiter_hint: 'Camerieri consentiti al tuo livello: {n}.',
        friends_title: 'Amici', search_ph: 'Cerca un giocatore per username', add: 'Aggiungi', requested: 'Richiesta inviata', friend: 'Amico', requests: 'Richieste', accept: 'Accetta', decline: 'Rifiuta',
        no_friends: 'Ancora nessun amico. Cerca uno username per aggiungere qualcuno.', visit: 'Visita', visit_bonus: 'Visita +{c}', visited: 'Visitato oggi', gift: 'Regalo', gifted: 'Regalo inviato',
        gift_title: 'Manda un ingrediente in regalo a {u}', gift_sent: 'Regalo inviato a {u}!', wants: 'vorrebbe {i}', remove: 'Rimuovi', remove_confirm: 'Rimuovere {u} dagli amici?',
        inbox: 'Regali', claim_all: 'Prendi tutto', gift_from: 'da {u}', got: 'Hai ricevuto {list}', inbox_empty: 'Nessun regalo in attesa.',
        visiting: 'In visita da {u}', back_home: 'Torna a casa', rate_it: 'Vota questo ristorante', rated: 'Grazie per il voto!', visit_reward: 'Bonus visita: +{c} monete',
        board_title: 'Classifica', scope_friends: 'Amici', scope_global: 'Tutti', period_week: 'Questa settimana', period_all: 'Sempre', week_value: '{n} monete', all_value: '{n} popolarità', board_empty: 'Ancora nessuno qui.',
        account_title: 'Account', admin_panel: 'Pannello admin', err_not_on_sale: 'Al momento non è in vendita.', restaurant_name: 'Nome del ristorante', language: 'Lingua', change_password: 'Cambia password', current_password: 'Password attuale', new_password: 'Nuova password',
        delete_account: 'Elimina account', delete_confirm: 'Elimina per sempre il tuo ristorante e tutto ciò che contiene. Scrivi la password per confermare.', privacy: 'Privacy',
        away_title: 'Mentre eri via', away_body: '{n} clienti in {m} minuti ti hanno fatto guadagnare <b>{c} monete</b>.', level_up: 'Livello {n}! Nuovi oggetti e piatti sbloccati.',
        credit_by: 'Creato da', credit_ai: 'Realizzato con Claude',
        cat_lounge: 'Relax e svago', cat_rug: 'Tappeti', cleaners: 'Addetti alle pulizie', hire_cleaner: 'Assumi un addetto alle pulizie', cleaner_hint: 'Sparecchiano e raccolgono i rifiuti. Fino a {n} al tuo livello.',
        trait: 'Carattere', trait_cheerful: 'Allegro', trait_speedy: 'Svelto', trait_tireless: 'Instancabile', trait_careful: 'Scrupoloso', trait_chatty: 'Chiacchierone', trait_calm: 'Tranquillo',
        trait_cheerful_d: 'I clienti che serve sono più contenti.', trait_speedy_d: 'Cammina il 25% più veloce.', trait_tireless_d: 'Si stanca la metà.', trait_careful_d: 'Cucina con calma, un po’ più lento.',
        trait_chatty_d: 'Prende le ordinazioni con calma, ma ai clienti piace.', trait_calm_d: 'Non si agita: si stanca un po’ meno.',
        mood: 'Umore', energy: 'Energia', doing: 'Adesso', ordered: 'Ha ordinato',
        act_idle: 'In attesa di qualcosa da fare', act_arriving: 'Sta entrando', act_wantOrder: 'Vuole ordinare', act_waitFood: 'Aspetta il piatto', act_eating: 'Sta mangiando',
        act_leaving: 'Se ne va', act_serve: 'Serve un piatto', act_order: 'Prende un’ordinazione', act_cook: 'Cucina', act_clean: 'Sparecchia', act_wash: 'Lava i piatti', act_mop: 'Raccoglie i rifiuti', act_rest: 'Fa una pausa',
        pie_greet: 'Saluta', pie_joke: 'Racconta una barzelletta', pie_dessert: 'Offri un dolcetto', pie_cheer: 'Incoraggia', pie_break: 'Manda in pausa', pie_praise: 'Fai un complimento',
        pie_clean: 'Pulisci', pie_music: 'Metti musica', pie_fun: 'Accendi', pie_cozy: 'Accendi il fuoco', pie_water: 'Annaffia',
        act_greet: 'Un caloroso benvenuto!', act_joke_ok: 'La barzelletta è piaciuta!', act_joke_bad: 'La barzelletta non ha fatto ridere…', act_dessert: 'Un dolcetto offerto dalla casa!', act_cheer: 'Di nuovo pieno di energia!', act_break: 'Andrà in pausa appena possibile.',
        act_praise: 'Raggiante di orgoglio!', act_clean: 'Splendente!', act_music: 'La musica riempie la sala', act_fun: 'Qualcosa da guardare nell’attesa', act_cozy: 'Il camino scoppietta', act_water: 'La pianta si rianima',
        status_open: 'Aperto', status_paused: 'Niente nuovi clienti', status_closed: 'Chiuso', status_title: 'Stato del ristorante', closed_status: 'Chiuso', paused_status: 'Non accetta nuovi clienti · {n} in sala',
        speed_pause: 'Pausa', speed_1: 'Velocità normale', speed_2: 'Veloce', speed_3: 'Velocissimo',
        season_spring: 'Primavera', season_summer: 'Estate', season_autumn: 'Autunno', season_winter: 'Inverno',
        weather_clear: 'Sereno', weather_cloudy: 'Nuvoloso', weather_rain: 'Pioggia', weather_snow: 'Neve',
        dock_reviews: 'Recensioni', reviews_title: 'Recensioni', reputation: 'Reputazione', reviews_window: 'Media delle ultime {n} recensioni', reviews_none: 'Ancora nessuna recensione: servi qualche cliente!',
        praised: 'Cosa piace ai clienti', blamed: 'Cosa migliorare', how_improve: 'Come migliorare',
        f_fast: 'servizio veloce', f_slow: 'servizio lento', f_tasty: 'cibo delizioso', f_plain: 'cibo banale', f_pretty: 'arredamento bellissimo', f_bare: 'sala spoglia', f_clean: 'pulitissimo', f_dirty: 'tavoli sporchi o rifiuti',
        f_ambience: 'bella atmosfera', f_friendly: 'personale gentile', f_dark: 'troppo buio la sera', f_cozy: 'serata accogliente',
        tip_slow: 'Assumi un altro cameriere o cuoco, aggiungi fornelli, oppure macchina del caffè e divano perché lo staff stanco si riprenda.', tip_plain: 'Migliora i piatti nel Menù con gli ingredienti: ai livelli alti sono più buoni.',
        tip_bare: 'Metti decorazioni, piante e tappeti vicino ai tavoli: i clienti giudicano la bellezza intorno al loro posto.', tip_dirty: 'Assumi un addetto alle pulizie, aggiungi un lavello e dei cestini, oppure tocca tavoli sporchi e rifiuti per pulirli.',
        tip_dark: 'Aggiungi lampade, lanterne o candele: la sera i clienti amano una sala ben illuminata.', tip_good: 'Continua così! Musica, una TV e salutare i clienti li rendono ancora più felici.',
        review_good: '{a}!', review_but: 'Però {b}.', review_only_bad: 'Peccato: {b}.', review_neutral: 'Un buon pasto, niente di speciale.',
        levels_title: 'Livelli', level_title: 'Livello {n} · {t}', unlocks: 'Sblocca', reward: 'Premio', level_up_title: 'Livello {n}!', level_up_body: 'Ora sei un {t}. Hai ricevuto {c} monete.',
        unlock_items: 'Nuovi oggetti', unlock_dishes: 'Nuovi piatti', unlock_crops: 'Nuove colture', unlock_size: 'Allarga a {n}×{n}', unlock_waiters: 'Fino a {n} camerieri', unlock_cleaners: 'Fino a {n} addetti alle pulizie', unlock_menu: '{n} piatti nel menù', unlock_plots: '{n} aiuole nell’orto', current: 'Sei qui',
        title_kiosk: 'Chiosco', title_snack_bar: 'Snack bar', title_trattoria: 'Trattoria', title_bistro: 'Bistrot', title_restaurant: 'Ristorante', title_fine_dining: 'Ristorante raffinato', title_gourmet: 'Casa gourmet', title_legend: 'Leggenda di Forkville',
        saved_ok: 'Salvato', customer: 'Cliente', room_clean: 'Pulizia',
        managers: 'Gestori', hire_manager: 'Assumi un gestore', manager: 'Gestore', manager_what: 'Un gestore manda avanti il ristorante mentre sei via, come se il gioco fosse aperto.',
        manager_hint: 'Mentre sei via mandano avanti il ristorante come se il gioco fosse aperto. Fino a {n} al tuo livello · ora coprono {h} ore.',
        manager_rank: 'copre {h} ore', promote: 'Promuovi', promoted: '{n} è stato promosso!', fire_confirm: 'Congedare {n}?', unlock_managers: 'Fino a {n} gestori',
        away_managed: 'I tuoi gestori hanno mandato avanti il ristorante per {m} minuti.',
        away_slot_manager: 'Dalle <b>{a}</b> alle <b>{b}</b> i tuoi gestori hanno mandato avanti il ristorante ({p}% del ritmo dal vivo).',
        away_slot_alone: 'Dalle <b>{a}</b> alle <b>{b}</b> lo staff è andato avanti senza di te, a ritmo ridotto ({p}% del ritmo dal vivo).',
        away_slot_closed: 'Dopo le <b>{a}</b> il ristorante è rimasto fermo: nessuno lo gestiva.',
        away_tip_hire: 'Assumi un gestore (dal livello 6) per far lavorare bene il ristorante mentre sei via.',
        away_tip_promote: 'Promuovi i tuoi gestori o assumine un altro per coprire più ore mentre sei via.',

        staff_intro: 'Assumi chi cerca lavoro, poi assegna un ruolo: puoi cambiarlo quando vuoi. Ogni curriculum mostra la bravura (0–10) come cuoco, cameriere, addetto alle pulizie e gestore; cresce con l’esperienza.',
        find_staff: 'Guarda chi cerca lavoro', per_stove: 'uno per fornello', role_changed: '{n} ora è {r}', candidates_title: 'Cercano lavoro', candidates_intro: 'Scegli una persona e il ruolo per cui la assumi. I curriculum migliori chiedono di più.',
        candidates_none: 'Nessun altro per oggi. Torna domani o rinnova la lista.', candidates_refresh: 'Nuovi candidati', hire: 'Assumi', hired: '{n} entra in squadra come {r}!',
        ledger_title: 'Monete: resoconto giornaliero', ledger_readonly: 'Quanto hai guadagnato e speso, giorno per giorno. È solo un registro: da qui non si può annullare nulla.', ledger_in: 'Guadagnate', ledger_out: 'Spese', ledger_net: 'Saldo del giorno',
        ledger_empty: 'Nessun movimento di monete in questo giorno.', ledger_moves: 'Movimenti',
        lk_customers: 'Clienti', lk_tips: 'Mance', lk_away: 'Mentre eri via', lk_level: 'Premi di livello', lk_visit: 'Visite agli amici', lk_visited: 'Amici in visita da te', lk_admin: 'Rettifiche',
        lk_layout: 'Arredi e decori', lk_hire: 'Assunzioni', lk_promote: 'Promozioni', lk_expand: 'Ampliamenti', lk_market: 'Mercato', lk_plant: 'Semi', lk_style: 'Divise e parrucchiere', lk_candidates: 'Nuovi candidati', lk_fire: 'Personale',
        lt_customers: '{n} clienti serviti', lt_away: '{n} clienti mentre eri via', lt_level: 'Premio per il livello {n}', lt_hire: 'Assunto {n} come {r}', lt_market: 'Mercato: {i} ×{q}', lt_plant: 'Semi: {i}', lt_expand: 'Ristorante allargato a {n}×{n}',
        lt_promote: 'Gestore promosso al grado {n}', lt_visit: 'Visita da {n}', lt_admin: 'Rettifica di un amministratore',
        look_title: 'Aspetto', look_of: 'Aspetto di {n}', look_cost: 'Costo delle modifiche', uniform: 'Divisa', hairstyle: 'Acconciatura', free: 'Gratis', look_done: '{n} ha un nuovo look!',
        uni_default: 'Divisa del ruolo', uni_terracotta: 'Grembiule cotto', uni_stripes: 'Righe alla marinara', uni_olive: 'Gilet oliva', uni_mint: 'Grembiule menta', uni_berry: 'Gilet frutti di bosco', uni_sailor: 'Marinaio', uni_denim: 'Salopette di jeans', uni_tuxedo: 'Smoking', uni_gold: 'Smoking dorato',
        hair_short: 'Corti', hair_bob: 'Caschetto', hair_bun: 'Chignon', hair_ponytail: 'Coda', hair_curly: 'Ricci', hair_spiky: 'A punta', hair_long: 'Lunghi', hair_bald: 'Rasati',
        tier_1: 'Economica', tier_2: 'Standard', tier_3: 'Pregiata', tier_4: 'Lusso', f_comfy: 'sedute comode',



        err_network: 'Nessuna connessione. Riprova.', err_server: 'Qualcosa è andato storto sul server.', err_bad_credentials: 'Email o password errate.', err_email_taken: 'Questa email ha già un account.',
        err_username_taken: 'Questo username è già preso.', err_username_invalid: 'Username: 3–20 lettere, numeri o _.', err_password_short: 'La password deve avere almeno 8 caratteri.',
        err_email_invalid: 'Questa email non sembra corretta.', err_name_required: 'Scrivi il tuo nome.', err_privacy_required: 'Accetta l’informativa privacy.', err_rate_limited: 'Troppi tentativi. Aspetta un po’.',
        err_use_google: 'Questo account usa l’accesso con Google.', err_token_invalid: 'Il link è scaduto o è già stato usato.', err_no_coins: 'Monete insufficienti.', err_no_ingredients: 'Mancano ingredienti.',
        err_level_locked: 'Serve un livello più alto.', err_layout_overlap: 'Due oggetti si sovrappongono.', err_layout_outside: 'Un oggetto è fuori dalla stanza.', err_need_stove: 'Ogni cuoco ha bisogno di un fornello: mettine uno prima.',
        err_staff_full: 'Hai già tutti i camerieri che il tuo livello consente.', err_staff_last: 'Ne serve almeno uno.', err_menu_full: 'Il menù è pieno.', err_menu_empty: 'Lascia almeno un piatto nel menù.',
        err_not_ripe: 'Non c’è ancora niente di maturo.', err_plot_busy: 'Questa aiuola è già piantata.', err_market_limit: 'Limite giornaliero raggiunto per questo ingrediente.', err_already_gifted: 'Oggi hai già mandato un regalo a questo amico.',
        err_already_rated: 'Oggi hai già votato.', err_not_found: 'Giocatore non trovato.', err_not_friends: 'Non siete amici.', err_bad_password: 'Password errata.', err_email_unverified: 'Prima conferma la tua email.',
        err_google_token: 'Accesso con Google non riuscito.', err_not_installed: 'Il gioco non è ancora installato su questo server.', err_max_size: 'Il tuo ristorante è già al massimo.', err_csrf: 'Sessione scaduta: ricarica la pagina.',
    },
};

const NAMES = {
    en: {
        table_small: 'Bistro table', table_round: 'Round table', table_marble: 'Marble table', table_long: 'Long table',
        chair_wood: 'Wooden chair', chair_cushion: 'Cushioned chair', stool_bar: 'Bar stool', chair_velvet: 'Velvet armchair',
        stove_basic: 'Stove', stove_steel: 'Steel range', oven_brick: 'Brick oven', counter_wood: 'Wooden counter', counter_tile: 'Tiled counter',
        plant_pot: 'Potted plant', plant_tall: 'Tall plant', lamp_floor: 'Floor lamp', bookshelf: 'Bookshelf', jukebox: 'Jukebox', piano: 'Piano', aquarium: 'Aquarium',
        fountain: 'Fountain', statue_fork: 'Golden fork statue', clock_tall: 'Grandfather clock', cake_stand: 'Cake stand',
        floor_oak: 'Oak planks', floor_checker: 'Checkerboard', floor_terracotta: 'Terracotta', floor_mint: 'Mint tiles', floor_marble: 'Marble',
        wall_cream: 'Cream', wall_stripes: 'Peach stripes', wall_brick: 'Brick', wall_sage: 'Sage panels', wall_berry: 'Berry damask',
        bruschetta: 'Bruschetta', fries: 'Fries', tomato_soup: 'Tomato soup', lemonade: 'Lemonade', cappuccino: 'Cappuccino', omelette: 'Omelette', pasta_tomato: 'Tomato pasta',
        pizza: 'Pizza', carrot_cake: 'Carrot cake', burger: 'Burger', risotto: 'Mushroom risotto', strawberry_tart: 'Strawberry tart', grilled_fish: 'Grilled fish',
        stuffed_peppers: 'Stuffed peppers', roast_chicken: 'Roast chicken', chocolate_cake: 'Chocolate cake',
        tomato: 'Tomato', basil: 'Basil', potato: 'Potato', onion: 'Onion', garlic: 'Garlic', carrot: 'Carrot', lemon: 'Lemon', strawberry: 'Strawberry', pepper: 'Pepper', mushroom: 'Mushroom',
        flour: 'Flour', cheese: 'Cheese', egg: 'Egg', milk: 'Milk', rice: 'Rice', fish: 'Fish', beef: 'Beef', chicken: 'Chicken', chocolate: 'Chocolate', coffee: 'Coffee', olive: 'Olive oil', sugar: 'Sugar',
        chef: 'Chef', waiter: 'Waiter',
        table_picnic: 'Picnic table', table_bar: 'High bar table', table_glass: 'Glass table', chair_rattan: 'Rattan chair', bench_wood: 'Wooden bench', chair_modern: 'Modern chair', chair_armchair: 'Armchair',
        stove_grill: 'Grill', fridge: 'Fridge', sink: 'Sink', cash_register: 'Cash register', trash_bin: 'Trash bin', menu_board: 'Menu board',
        coffee_machine: 'Coffee machine', sofa: 'Sofa', tv: 'TV', arcade: 'Arcade cabinet', gramophone: 'Gramophone', fireplace: 'Fireplace', vending: 'Vending machine',
        cactus: 'Cactus', sunflowers: 'Sunflowers', coat_rack: 'Coat rack', umbrella_stand: 'Umbrella stand', candles: 'Candelabra', globe: 'Globe', lantern: 'Lantern', palm: 'Palm', easel: 'Easel',
        bonsai: 'Bonsai', neon_sign: 'Neon sign', trophy_case: 'Trophy case', flower_arch: 'Flower arch',
        rug_round: 'Round rug', rug_stripes: 'Striped runner', rug_red: 'Red rug', rug_checker: 'Checkered rug', rug_persian: 'Persian rug',
        floor_blue: 'Blue tiles', floor_herringbone: 'Herringbone', floor_hex: 'Hexagon tiles', wall_tile: 'White tiles', wall_wood: 'Wood panels', wall_flowers: 'Flower wallpaper',
        cleaner: 'Cleaner', manager: 'Manager',
        stove_retro: 'Retro stove', stove_pro: 'Pro range', stove_gold: 'Golden range', counter_marble: 'Marble counter', counter_gold: 'Golden counter', fridge_retro: 'Retro fridge', fridge_pro: 'Pro fridge', sink_steel: 'Double steel sink', coffee_luxe: 'Luxury espresso bar', table_rustic: 'Rustic table', chair_folding: 'Folding chair', chair_bistro: 'Bistro chair',


    },
    it: {
        table_small: 'Tavolino', table_round: 'Tavolo rotondo', table_marble: 'Tavolo di marmo', table_long: 'Tavolo lungo',
        chair_wood: 'Sedia di legno', chair_cushion: 'Sedia imbottita', stool_bar: 'Sgabello', chair_velvet: 'Poltroncina di velluto',
        stove_basic: 'Fornello', stove_steel: 'Cucina in acciaio', oven_brick: 'Forno a legna', counter_wood: 'Bancone di legno', counter_tile: 'Bancone piastrellato',
        plant_pot: 'Pianta in vaso', plant_tall: 'Pianta alta', lamp_floor: 'Lampada da terra', bookshelf: 'Libreria', jukebox: 'Jukebox', piano: 'Pianoforte', aquarium: 'Acquario',
        fountain: 'Fontana', statue_fork: 'Statua della forchetta d’oro', clock_tall: 'Pendolo', cake_stand: 'Alzatina dei dolci',
        floor_oak: 'Parquet di rovere', floor_checker: 'Scacchiera', floor_terracotta: 'Cotto', floor_mint: 'Piastrelle menta', floor_marble: 'Marmo',
        wall_cream: 'Crema', wall_stripes: 'Righe pesca', wall_brick: 'Mattoni', wall_sage: 'Pannelli salvia', wall_berry: 'Damasco frutti di bosco',
        bruschetta: 'Bruschetta', fries: 'Patatine', tomato_soup: 'Zuppa di pomodoro', lemonade: 'Limonata', cappuccino: 'Cappuccino', omelette: 'Frittata', pasta_tomato: 'Pasta al pomodoro',
        pizza: 'Pizza', carrot_cake: 'Torta di carote', burger: 'Hamburger', risotto: 'Risotto ai funghi', strawberry_tart: 'Crostata di fragole', grilled_fish: 'Pesce alla griglia',
        stuffed_peppers: 'Peperoni ripieni', roast_chicken: 'Pollo arrosto', chocolate_cake: 'Torta al cioccolato',
        tomato: 'Pomodoro', basil: 'Basilico', potato: 'Patata', onion: 'Cipolla', garlic: 'Aglio', carrot: 'Carota', lemon: 'Limone', strawberry: 'Fragola', pepper: 'Peperone', mushroom: 'Fungo',
        flour: 'Farina', cheese: 'Formaggio', egg: 'Uovo', milk: 'Latte', rice: 'Riso', fish: 'Pesce', beef: 'Manzo', chicken: 'Pollo', chocolate: 'Cioccolato', coffee: 'Caffè', olive: 'Olio d’oliva', sugar: 'Zucchero',
        chef: 'Cuoco', waiter: 'Cameriere',
        table_picnic: 'Tavolo da picnic', table_bar: 'Tavolino alto', table_glass: 'Tavolo di vetro', chair_rattan: 'Sedia in rattan', bench_wood: 'Panca di legno', chair_modern: 'Sedia moderna', chair_armchair: 'Poltrona',
        stove_grill: 'Griglia', fridge: 'Frigorifero', sink: 'Lavello', cash_register: 'Registratore di cassa', trash_bin: 'Cestino', menu_board: 'Lavagna del menù',
        coffee_machine: 'Macchina del caffè', sofa: 'Divano', tv: 'TV', arcade: 'Cabinato arcade', gramophone: 'Grammofono', fireplace: 'Camino', vending: 'Distributore',
        cactus: 'Cactus', sunflowers: 'Girasoli', coat_rack: 'Appendiabiti', umbrella_stand: 'Portaombrelli', candles: 'Candelabro', globe: 'Mappamondo', lantern: 'Lanterna', palm: 'Palma', easel: 'Cavalletto',
        bonsai: 'Bonsai', neon_sign: 'Insegna al neon', trophy_case: 'Vetrina dei trofei', flower_arch: 'Arco fiorito',
        rug_round: 'Tappeto rotondo', rug_stripes: 'Passatoia a righe', rug_red: 'Tappeto rosso', rug_checker: 'Tappeto a scacchi', rug_persian: 'Tappeto persiano',
        floor_blue: 'Piastrelle blu', floor_herringbone: 'Spina di pesce', floor_hex: 'Esagonette', wall_tile: 'Piastrelle bianche', wall_wood: 'Boiserie', wall_flowers: 'Carta da parati a fiori',
        cleaner: 'Addetto alle pulizie', manager: 'Gestore',
        stove_retro: 'Cucina retrò', stove_pro: 'Cucina professionale', stove_gold: 'Cucina dorata', counter_marble: 'Bancone di marmo', counter_gold: 'Bancone dorato', fridge_retro: 'Frigo retrò', fridge_pro: 'Frigo professionale', sink_steel: 'Lavello doppio in acciaio', coffee_luxe: 'Angolo espresso di lusso', table_rustic: 'Tavolo rustico', chair_folding: 'Sedia pieghevole', chair_bistro: 'Sedia da bistrot',


    },
};


let lang = 'en';
try {
    lang = localStorage.getItem('forkville-lang') || '';
} catch (e) { /* storage blocked */ }
if (!STRINGS[lang]) lang = (navigator.language || 'en').toLowerCase().startsWith('it') ? 'it' : 'en';

export const getLang = () => lang;

export function setLang(l) {
    if (!STRINGS[l]) return;
    lang = l;
    document.documentElement.lang = l;
    try {
        localStorage.setItem('forkville-lang', l);
    } catch (e) { /* storage blocked */ }
}

/** Text with {placeholders}. */
export function t(key, vars = {}) {
    const s = STRINGS[lang][key] ?? STRINGS.en[key] ?? key;
    return s.replace(/\{(\w+)\}/g, (_, k) => vars[k] ?? '');
}

/** Name of an item, dish, ingredient or role. */
export const n = (key) => NAMES[lang][key] ?? NAMES.en[key] ?? key;

export const errText = (code) => STRINGS[lang]['err_' + code] ?? STRINGS.en['err_' + code] ?? t('err_server');

/** Name in a given language (the admin panel is always in Italian). */
export const nameIn = (key, l) => NAMES[l]?.[key] ?? NAMES.en[key] ?? key;
