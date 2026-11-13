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
        staff_title: 'Staff', chefs: 'Chefs', waiters: 'Waiters', hire_chef: 'Hire a chef', hire_waiter: 'Hire a waiter', fire: 'Let go', chef_hint: 'One chef per stove.', waiter_hint: 'Waiters allowed at your level: {n}. Each one looks after their own tables: a waiter without a table cannot serve.',
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
        cat_lounge: 'Lounge & fun', cat_rug: 'Rugs', cleaners: 'Cleaners', hire_cleaner: 'Hire a cleaner', cleaner_hint: 'They clean the floor (paper, peels, crumbs, puddles and snow at the entrance), faster with a cleaning cart or station. Waiters clear the tables. Up to {n} at your level.',
        trait: 'Trait', trait_cheerful: 'Cheerful', trait_speedy: 'Speedy', trait_tireless: 'Tireless', trait_careful: 'Careful', trait_chatty: 'Chatty', trait_calm: 'Calm',
        trait_cheerful_d: 'Customers they serve are happier.', trait_speedy_d: 'Walks 25% faster.', trait_tireless_d: 'Gets tired half as fast.', trait_careful_d: 'Cooks a little slower, never in a rush.',
        trait_chatty_d: 'Takes longer orders, but customers love it.', trait_calm_d: 'Keeps cool: tires a bit slower.',
        mood: 'Mood', energy: 'Energy', doing: 'Now', ordered: 'Ordered',
        act_idle: 'Waiting for something to do', act_arriving: 'Coming in', act_wantOrder: 'Wants to order', act_waitFood: 'Waiting for the food', act_eating: 'Eating',
        act_leaving: 'Leaving', act_serve: 'Serving a dish', act_order: 'Taking an order', act_cook: 'Cooking', act_clean: 'Clearing a table', act_wash: 'Washing up', act_mop: 'Cleaning the floor', act_rest: 'Taking a break', act_restock: 'Restocking cleaning supplies',
        pie_greet: 'Greet', pie_joke: 'Tell a joke', pie_dessert: 'Offer a sweet', pie_cheer: 'Cheer on', pie_break: 'Send on a break', pie_praise: 'Praise',
        pie_clean: 'Clean', pie_music: 'Play music', pie_fun: 'Turn on', pie_cozy: 'Light the fire', pie_water: 'Water',
        act_greet: 'A warm welcome!', act_joke_ok: 'They loved the joke!', act_joke_bad: 'That joke fell flat…', act_dessert: 'A sweet on the house!', act_cheer: 'Full of energy again!', act_break: 'Off for a break as soon as possible.',
        act_praise: 'They beam with pride!', act_clean: 'Spotless!', act_music: 'Music fills the room', act_fun: 'Something to watch while waiting', act_cozy: 'A cozy fire is crackling', act_water: 'The plant perks up',
        status_open: 'Open', status_paused: 'No new guests', status_closed: 'Closed', status_title: 'Restaurant status', closed_status: 'Closed', paused_status: 'Not taking new guests · {n} inside',
        speed_pause: 'Pause', speed_1: 'Real time (×1)', speed_2: 'Fast (×2)', speed_3: 'Very fast (×5)',
        season_spring: 'Spring', season_summer: 'Summer', season_autumn: 'Autumn', season_winter: 'Winter',
        weather_clear: 'Clear', weather_cloudy: 'Cloudy', weather_rain: 'Rain', weather_snow: 'Snow',
        dock_reviews: 'Reviews', reviews_title: 'Reviews', reputation: 'Reputation', reviews_window: 'Average of the last {n} reviews', reviews_none: 'No reviews yet: serve some customers!',
        praised: 'What customers love', blamed: 'What to improve', how_improve: 'How to improve',
        f_fast: 'fast service', f_slow: 'slow service', f_tasty: 'delicious food', f_plain: 'plain food', f_pretty: 'lovely decor', f_bare: 'bare room', f_clean: 'spotless', f_dirty: 'dirty tables or litter',
        f_ambience: 'great atmosphere', f_friendly: 'friendly staff', f_dark: 'too dark in the evening', f_cozy: 'cozy evening',
        tip_slow: 'Hire another waiter or chef, add stoves, or a coffee machine and sofa so tired staff recover.', tip_plain: 'Improve your dishes in the Menu with ingredients: higher levels taste better.',
        tip_bare: 'Put decorations, plants and rugs near the tables: customers rate the beauty around their seat.', tip_dirty: 'More guests make more mess. Dirty tables: more waiters (each clears their own tables) and a sink. Dirty floor: hire cleaners, each with their own cleaning cart or station (when the floor is clean they help clear tables too), and add trash bins. You can also tap tables and dirt to clean them.',
        tip_dark: 'Add lamps, lanterns or candles: in the evening customers like a well-lit room.', tip_good: 'Keep it up! Music, a TV and greeting your guests make them even happier.',
        review_good: '{a}!', review_but: 'But {b}.', review_only_bad: 'Sadly, {b}.', review_neutral: 'A nice meal, nothing special.',
        levels_title: 'Levels', level_title: 'Level {n} · {t}', unlocks: 'Unlocks', reward: 'Reward', level_up_title: 'Level up!', level_up_ok: 'Great!', level_up_body: 'You are now a {t}. You received {c} coins.',
        unlock_items: 'New items', unlock_dishes: 'New dishes', unlock_crops: 'New crops', unlock_size: 'Enlarge to {n}×{n}', unlock_waiters: 'Up to {n} waiters', unlock_cleaners: 'Up to {n} cleaners', unlock_menu: '{n} dishes on the menu', unlock_plots: '{n} garden beds', current: 'You are here',
        title_kiosk: 'Kiosk', title_snack_bar: 'Snack bar', title_trattoria: 'Trattoria', title_bistro: 'Bistro', title_restaurant: 'Restaurant', title_fine_dining: 'Fine dining', title_gourmet: 'Gourmet house', title_renowned: 'Renowned restaurant', title_star_house: 'Starred house', title_legend: 'Legend of Forkville',
        saved_ok: 'Saved', customer: 'Customer', room_clean: 'Cleanliness',
        managers: 'Managers', hire_manager: 'Hire a manager', manager: 'Manager', manager_what: 'A manager runs the restaurant while you are away, as if the game were open.',
        manager_hint: 'While you are away they run the restaurant as if the game were open. Up to {n} at your level · now {h} hours covered.',
        manager_rank: 'covers {h} hours', promote: 'Promote', promoted: '{n} has been promoted!', fire_confirm: 'Let {n} go?', unlock_managers: 'Up to {n} managers',
        away_managed: 'Your managers ran the restaurant for {m} minutes.',
        away_slot_manager: 'From <b>{a}</b> to <b>{b}</b> your managers ran the restaurant ({p}% of the live pace).',
        away_slot_alone: 'From <b>{a}</b> to <b>{b}</b> the staff carried on without you, at a slow pace ({p}% of the live pace).',
        away_slot_closed: 'After <b>{a}</b> the restaurant stood still: nobody was running it.',
        away_tip_hire: 'Hire a manager (from level 7) to keep the restaurant running at a good pace while you are away.',
        away_tip_promote: 'Promote your managers or hire another one to cover more hours while you are away.',

        staff_intro: 'Hire people looking for a job, then give them a role: you can change it any time. Each CV shows their skill (0–10) as cook, waiter, cleaner and manager; it grows very slowly with experience in their role (about one point every 8 game years), only while the restaurant is open.',
        find_staff: 'See who is looking for a job', per_stove: 'one per stove', role_changed: '{n} is now {r}', candidates_title: 'Looking for a job', candidates_intro: 'Pick someone and the role you hire them for. Better CVs ask for more.',
        candidates_none: 'Nobody else today. Come back tomorrow or refresh the list.', candidates_refresh: 'New candidates', hire: 'Hire', hired: '{n} joins the team as {r}!',
        ledger_title: 'Coins: daily statement', ledger_readonly: 'What you earned and spent, day by day. This is only a record: nothing can be undone from here.', ledger_in: 'Earned', ledger_out: 'Spent', ledger_net: 'Balance of the day',
        ledger_empty: 'No coins moved on this day.', ledger_moves: 'Movements',
        lk_customers: 'Customers', lk_tips: 'Tips', lk_away: 'While you were away', lk_level: 'Level rewards', lk_visit: 'Visits to friends', lk_visited: 'Friends visiting you', lk_admin: 'Adjustments',
        lk_layout: 'Furniture and decor', lk_hire: 'Hiring', lk_promote: 'Promotions', lk_expand: 'Enlargements', lk_market: 'Market', lk_plant: 'Seeds', lk_style: 'Uniforms and hairdresser', lk_candidates: 'New candidates', lk_fire: 'Staff',
        lt_customers: '{n} customers served', lt_away: '{n} customers while you were away', lt_level: 'Reward for level {n}', lt_hire: 'Hired {n} as {r}', lt_market: 'Market: {i} ×{q}', lt_plant: 'Seeds: {i}', lt_expand: 'Restaurant enlarged to {n}',
        lt_promote: 'Manager promoted to rank {n}', lt_visit: 'Visit to {n}', lt_admin: 'Adjustment by an administrator',
        look_title: 'Look', look_of: '{n}’s look', look_cost: 'Cost of the changes', uniform: 'Uniform', hairstyle: 'Hairstyle', free: 'Free', look_done: '{n} has a new look!',
        uni_default: 'Role uniform', uni_terracotta: 'Terracotta apron', uni_stripes: 'Breton stripes', uni_olive: 'Olive waistcoat', uni_mint: 'Mint apron', uni_berry: 'Berry waistcoat', uni_sailor: 'Sailor', uni_denim: 'Denim dungarees', uni_tuxedo: 'Tuxedo', uni_gold: 'Golden tuxedo',
        hair_short: 'Short', hair_bob: 'Bob', hair_bun: 'Bun', hair_ponytail: 'Ponytail', hair_curly: 'Curly', hair_spiky: 'Spiky', hair_long: 'Long', hair_bald: 'Shaved',
        seniority: 'At work for {t}', next_point: '{p}% to the next point', days_n: '{n} days', years_days: '{y} years and {d} days',
        need_toilet: 'No restroom: guests will complain · add a toilet', act_toilet: 'Going to the restroom', act_toiletClean: 'Cleaning the restroom',
        f_no_toilet: 'no restroom', f_toilet_dirty: 'dirty restroom', f_toilet_clean: 'spotless restroom', tip_no_toilet: 'Add a restroom: a toilet, a washbasin and a hand dryer. Without one, guests write bad reviews.',
        tip_toilet_dirty: 'Toilets get dirty with use: a cleaner keeps them clean (or tap a toilet to clean it).', cat_restroom: 'Restroom', cat_partition: 'Walls & screens',
        waiter_no_table: 'Waiters without a table: {n} · add tables', my_tables: 'Tables', no_table: 'none: there are not enough tables', act_noTable: 'No table to look after',
        busy_rush: 'Rush hour', busy_quiet: 'Quiet hour', busy_rush_d: 'Lots of people go out to eat at this time.', busy_quiet_d: 'Hardly anyone goes out to eat at this time.',
        sound_title: 'Music and sounds', sound_music: 'Music', sound_effects: 'Sounds', sound_volume: 'Volume',
        calendar_title: 'Restaurant calendar', year_n: 'year {n}', year_short: 'Y{n}', season_progress: 'Day {d} of {n} of the season', next_season: '{s} in {n} days',
        calendar_note: 'Forkville has its own calendar: year 0 began when you opened the restaurant, and time passes only while you play (faster with the speed buttons).', closed_staff_home: 'Closed: the staff have gone home (closed hours are not paid).',
        lk_wages: 'Wages', lt_wages: 'Wages for {n} staff ({d} days)', wage_day: '{c} a day', payroll: 'Wages per day', wages_paid: 'Wages paid: {c} coins',
        kind_title: 'Kind of restaurant', kind_generic: 'Restaurant', kind_fast_food: 'Fast food', kind_cafe: 'Café', kind_inn: 'Inn', kind_luxury: 'Luxury restaurant',
        kind_generic_d: 'A bit of everything: the baseline.', kind_fast_food_d: 'Lower prices, many more customers who eat fast. Nobody minds the decor much.',
        kind_cafe_d: 'Slightly lower prices, more customers, quick visits.', kind_inn_d: 'Warm and welcoming: guests love a fireplace and soft lights in the evening.',
        kind_luxury_d: 'Much higher prices, fewer guests, and they expect comfortable seats and beautiful rooms.', kind_effects: 'Prices ×{p} · customers ×{c}', kind_switch: 'Switch · {c} coins', kind_current: 'Current',
        cat_outdoor: 'Outdoors', act_queue: 'Waiting outside', act_home: 'At home', act_arriving_staff: 'Coming to work',
        tier_1: 'Budget', tier_2: 'Standard', tier_3: 'Premium', tier_4: 'Luxury', f_comfy: 'comfortable seats',



        // Errors
        err_network: 'No connection. Try again.', err_server: 'Something went wrong on the server.', err_bad_credentials: 'Wrong email or password.', err_email_taken: 'This email already has an account.',
        err_username_taken: 'This username is taken.', err_username_invalid: 'Username: 3–20 letters, numbers or _.', err_password_short: 'The password needs at least 8 characters.',
        err_email_invalid: 'This email doesn’t look right.', err_name_required: 'Write your name.', err_privacy_required: 'Please accept the privacy notice.', err_rate_limited: 'Too many attempts. Wait a little.',
        err_use_google: 'This account uses Google sign-in.', err_token_invalid: 'This link has expired or was already used.', err_no_coins: 'Not enough coins.', err_no_ingredients: 'Missing ingredients.',
        err_level_locked: 'You need a higher level.', err_layout_overlap: 'Two pieces overlap.', err_layout_outside: 'A piece is outside the room.', err_need_stove: 'Each chef needs a stove: place one first.',
        err_staff_full: 'You have all the workers of this role your level allows.', cook_secs: '{b}s', cook_secs_here: '{b}s → {n}s here', cook_hint: 'Base cooking time → with your best stove, fridges and kitchen equipment. A skilled chef is faster still.', cook_cold: 'Served straight from the fridge or freezer', cook_cold_short: 'ready', err_chefs_full: 'You have all the chefs your level allows: level up to hire more.', chef_stoves: 'stoves: {n}', err_staff_last: 'You need at least one.', err_menu_full: 'The menu is full.', err_menu_empty: 'Keep at least one dish on the menu.',
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
        staff_title: 'Staff', chefs: 'Cuochi', waiters: 'Camerieri', hire_chef: 'Assumi un cuoco', hire_waiter: 'Assumi un cameriere', fire: 'Congeda', chef_hint: 'Un cuoco per ogni fornello.', waiter_hint: 'Camerieri consentiti al tuo livello: {n}. Ognuno segue i suoi tavoli: un cameriere senza tavolo non può servire.',
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
        cat_lounge: 'Relax e svago', cat_rug: 'Tappeti', cleaners: 'Addetti alle pulizie', hire_cleaner: 'Assumi un addetto alle pulizie', cleaner_hint: 'Puliscono per terra (carte, bucce, briciole, pozzanghere e neve all’ingresso), più in fretta con un carrello o una postazione delle pulizie. I tavoli li sparecchiano i camerieri. Fino a {n} al tuo livello.',
        trait: 'Carattere', trait_cheerful: 'Allegro', trait_speedy: 'Svelto', trait_tireless: 'Instancabile', trait_careful: 'Scrupoloso', trait_chatty: 'Chiacchierone', trait_calm: 'Tranquillo',
        trait_cheerful_d: 'I clienti che serve sono più contenti.', trait_speedy_d: 'Cammina il 25% più veloce.', trait_tireless_d: 'Si stanca la metà.', trait_careful_d: 'Cucina con calma, un po’ più lento.',
        trait_chatty_d: 'Prende le ordinazioni con calma, ma ai clienti piace.', trait_calm_d: 'Non si agita: si stanca un po’ meno.',
        mood: 'Umore', energy: 'Energia', doing: 'Adesso', ordered: 'Ha ordinato',
        act_idle: 'In attesa di qualcosa da fare', act_arriving: 'Sta entrando', act_wantOrder: 'Vuole ordinare', act_waitFood: 'Aspetta il piatto', act_eating: 'Sta mangiando',
        act_leaving: 'Se ne va', act_serve: 'Serve un piatto', act_order: 'Prende un’ordinazione', act_cook: 'Cucina', act_clean: 'Sparecchia', act_wash: 'Lava i piatti', act_mop: 'Pulisce per terra', act_rest: 'Fa una pausa', act_restock: 'Rifornisce i prodotti per le pulizie',
        pie_greet: 'Saluta', pie_joke: 'Racconta una barzelletta', pie_dessert: 'Offri un dolcetto', pie_cheer: 'Incoraggia', pie_break: 'Manda in pausa', pie_praise: 'Fai un complimento',
        pie_clean: 'Pulisci', pie_music: 'Metti musica', pie_fun: 'Accendi', pie_cozy: 'Accendi il fuoco', pie_water: 'Annaffia',
        act_greet: 'Un caloroso benvenuto!', act_joke_ok: 'La barzelletta è piaciuta!', act_joke_bad: 'La barzelletta non ha fatto ridere…', act_dessert: 'Un dolcetto offerto dalla casa!', act_cheer: 'Di nuovo pieno di energia!', act_break: 'Andrà in pausa appena possibile.',
        act_praise: 'Raggiante di orgoglio!', act_clean: 'Splendente!', act_music: 'La musica riempie la sala', act_fun: 'Qualcosa da guardare nell’attesa', act_cozy: 'Il camino scoppietta', act_water: 'La pianta si rianima',
        status_open: 'Aperto', status_paused: 'Niente nuovi clienti', status_closed: 'Chiuso', status_title: 'Stato del ristorante', closed_status: 'Chiuso', paused_status: 'Non accetta nuovi clienti · {n} in sala',
        speed_pause: 'Pausa', speed_1: 'Tempo reale (×1)', speed_2: 'Veloce (×2)', speed_3: 'Velocissimo (×5)',
        season_spring: 'Primavera', season_summer: 'Estate', season_autumn: 'Autunno', season_winter: 'Inverno',
        weather_clear: 'Sereno', weather_cloudy: 'Nuvoloso', weather_rain: 'Pioggia', weather_snow: 'Neve',
        dock_reviews: 'Recensioni', reviews_title: 'Recensioni', reputation: 'Reputazione', reviews_window: 'Media delle ultime {n} recensioni', reviews_none: 'Ancora nessuna recensione: servi qualche cliente!',
        praised: 'Cosa piace ai clienti', blamed: 'Cosa migliorare', how_improve: 'Come migliorare',
        f_fast: 'servizio veloce', f_slow: 'servizio lento', f_tasty: 'cibo delizioso', f_plain: 'cibo banale', f_pretty: 'arredamento bellissimo', f_bare: 'sala spoglia', f_clean: 'pulitissimo', f_dirty: 'tavoli sporchi o rifiuti',
        f_ambience: 'bella atmosfera', f_friendly: 'personale gentile', f_dark: 'troppo buio la sera', f_cozy: 'serata accogliente',
        tip_slow: 'Assumi un altro cameriere o cuoco, aggiungi fornelli, oppure macchina del caffè e divano perché lo staff stanco si riprenda.', tip_plain: 'Migliora i piatti nel Menù con gli ingredienti: ai livelli alti sono più buoni.',
        tip_bare: 'Metti decorazioni, piante e tappeti vicino ai tavoli: i clienti giudicano la bellezza intorno al loro posto.', tip_dirty: 'Più clienti fanno più sporco. Tavoli sporchi: più camerieri (ognuno sparecchia i suoi tavoli) e un lavello. Pavimento sporco: assumi addetti alle pulizie, ognuno con il suo carrello o la sua postazione (a pavimento pulito aiutano anche a sparecchiare), e aggiungi dei cestini. Puoi anche toccare tavoli e sporcizia per pulirli.',
        tip_dark: 'Aggiungi lampade, lanterne o candele: la sera i clienti amano una sala ben illuminata.', tip_good: 'Continua così! Musica, una TV e salutare i clienti li rendono ancora più felici.',
        review_good: '{a}!', review_but: 'Però {b}.', review_only_bad: 'Peccato: {b}.', review_neutral: 'Un buon pasto, niente di speciale.',
        levels_title: 'Livelli', level_title: 'Livello {n} · {t}', unlocks: 'Sblocca', reward: 'Premio', level_up_title: 'Nuovo livello!', level_up_ok: 'Fantastico!', level_up_body: 'Ora sei un {t}. Hai ricevuto {c} monete.',
        unlock_items: 'Nuovi oggetti', unlock_dishes: 'Nuovi piatti', unlock_crops: 'Nuove colture', unlock_size: 'Allarga a {n}×{n}', unlock_waiters: 'Fino a {n} camerieri', unlock_cleaners: 'Fino a {n} addetti alle pulizie', unlock_menu: '{n} piatti nel menù', unlock_plots: '{n} aiuole nell’orto', current: 'Sei qui',
        title_kiosk: 'Chiosco', title_snack_bar: 'Snack bar', title_trattoria: 'Trattoria', title_bistro: 'Bistrot', title_restaurant: 'Ristorante', title_fine_dining: 'Ristorante raffinato', title_gourmet: 'Casa gourmet', title_renowned: 'Ristorante rinomato', title_star_house: 'Casa stellata', title_legend: 'Leggenda di Forkville',
        saved_ok: 'Salvato', customer: 'Cliente', room_clean: 'Pulizia',
        managers: 'Gestori', hire_manager: 'Assumi un gestore', manager: 'Gestore', manager_what: 'Un gestore manda avanti il ristorante mentre sei via, come se il gioco fosse aperto.',
        manager_hint: 'Mentre sei via mandano avanti il ristorante come se il gioco fosse aperto. Fino a {n} al tuo livello · ora coprono {h} ore.',
        manager_rank: 'copre {h} ore', promote: 'Promuovi', promoted: '{n} è stato promosso!', fire_confirm: 'Congedare {n}?', unlock_managers: 'Fino a {n} gestori',
        away_managed: 'I tuoi gestori hanno mandato avanti il ristorante per {m} minuti.',
        away_slot_manager: 'Dalle <b>{a}</b> alle <b>{b}</b> i tuoi gestori hanno mandato avanti il ristorante ({p}% del ritmo dal vivo).',
        away_slot_alone: 'Dalle <b>{a}</b> alle <b>{b}</b> lo staff è andato avanti senza di te, a ritmo ridotto ({p}% del ritmo dal vivo).',
        away_slot_closed: 'Dopo le <b>{a}</b> il ristorante è rimasto fermo: nessuno lo gestiva.',
        away_tip_hire: 'Assumi un gestore (dal livello 7) per far lavorare bene il ristorante mentre sei via.',
        away_tip_promote: 'Promuovi i tuoi gestori o assumine un altro per coprire più ore mentre sei via.',

        staff_intro: 'Assumi chi cerca lavoro, poi assegna un ruolo: puoi cambiarlo quando vuoi. Ogni curriculum mostra la bravura (0–10) come cuoco, cameriere, addetto alle pulizie e gestore; cresce molto lentamente con l’esperienza nel ruolo (circa un punto ogni 8 anni di gioco), solo quando il ristorante è aperto.',
        find_staff: 'Guarda chi cerca lavoro', per_stove: 'uno per fornello', role_changed: '{n} ora è {r}', candidates_title: 'Cercano lavoro', candidates_intro: 'Scegli una persona e il ruolo per cui la assumi. I curriculum migliori chiedono di più.',
        candidates_none: 'Nessun altro per oggi. Torna domani o rinnova la lista.', candidates_refresh: 'Nuovi candidati', hire: 'Assumi', hired: '{n} entra in squadra come {r}!',
        ledger_title: 'Monete: resoconto giornaliero', ledger_readonly: 'Quanto hai guadagnato e speso, giorno per giorno. È solo un registro: da qui non si può annullare nulla.', ledger_in: 'Guadagnate', ledger_out: 'Spese', ledger_net: 'Saldo del giorno',
        ledger_empty: 'Nessun movimento di monete in questo giorno.', ledger_moves: 'Movimenti',
        lk_customers: 'Clienti', lk_tips: 'Mance', lk_away: 'Mentre eri via', lk_level: 'Premi di livello', lk_visit: 'Visite agli amici', lk_visited: 'Amici in visita da te', lk_admin: 'Rettifiche',
        lk_layout: 'Arredi e decori', lk_hire: 'Assunzioni', lk_promote: 'Promozioni', lk_expand: 'Ampliamenti', lk_market: 'Mercato', lk_plant: 'Semi', lk_style: 'Divise e parrucchiere', lk_candidates: 'Nuovi candidati', lk_fire: 'Personale',
        lt_customers: '{n} clienti serviti', lt_away: '{n} clienti mentre eri via', lt_level: 'Premio per il livello {n}', lt_hire: 'Assunto {n} come {r}', lt_market: 'Mercato: {i} ×{q}', lt_plant: 'Semi: {i}', lt_expand: 'Ristorante allargato a {n}',
        lt_promote: 'Gestore promosso al grado {n}', lt_visit: 'Visita da {n}', lt_admin: 'Rettifica di un amministratore',
        look_title: 'Aspetto', look_of: 'Aspetto di {n}', look_cost: 'Costo delle modifiche', uniform: 'Divisa', hairstyle: 'Acconciatura', free: 'Gratis', look_done: '{n} ha un nuovo look!',
        uni_default: 'Divisa del ruolo', uni_terracotta: 'Grembiule cotto', uni_stripes: 'Righe alla marinara', uni_olive: 'Gilet oliva', uni_mint: 'Grembiule menta', uni_berry: 'Gilet frutti di bosco', uni_sailor: 'Marinaio', uni_denim: 'Salopette di jeans', uni_tuxedo: 'Smoking', uni_gold: 'Smoking dorato',
        hair_short: 'Corti', hair_bob: 'Caschetto', hair_bun: 'Chignon', hair_ponytail: 'Coda', hair_curly: 'Ricci', hair_spiky: 'A punta', hair_long: 'Lunghi', hair_bald: 'Rasati',
        seniority: 'In servizio da {t}', next_point: '{p}% verso il prossimo punto', days_n: '{n} giorni', years_days: '{y} anni e {d} giorni',
        need_toilet: 'Manca il bagno: i clienti si lamenteranno · aggiungi un WC', act_toilet: 'Va in bagno', act_toiletClean: 'Pulisce il bagno',
        f_no_toilet: 'niente bagno', f_toilet_dirty: 'bagno sporco', f_toilet_clean: 'bagno pulitissimo', tip_no_toilet: 'Aggiungi un bagno: un WC, un lavandino e un asciugamani. Senza, i clienti scrivono recensioni negative.',
        tip_toilet_dirty: 'I bagni si sporcano con l’uso: l’addetto alle pulizie li tiene puliti (oppure tocca un WC per pulirlo).', cat_restroom: 'Bagno', cat_partition: 'Muri e separè',
        waiter_no_table: 'Camerieri senza tavolo: {n} · aggiungi tavoli', my_tables: 'Tavoli', no_table: 'nessuno: non ci sono abbastanza tavoli', act_noTable: 'Nessun tavolo da seguire',
        busy_rush: 'Ora di punta', busy_quiet: 'Ora tranquilla', busy_rush_d: 'A quest’ora molta gente va a mangiare fuori.', busy_quiet_d: 'A quest’ora quasi nessuno va a mangiare fuori.',
        sound_title: 'Musica e suoni', sound_music: 'Musica', sound_effects: 'Suoni', sound_volume: 'Volume',
        calendar_title: 'Calendario del ristorante', year_n: 'anno {n}', year_short: 'A{n}', season_progress: 'Giorno {d} di {n} della stagione', next_season: '{s} tra {n} giorni',
        calendar_note: 'Forkville ha il suo calendario: l’anno 0 è cominciato quando hai aperto il ristorante, e il tempo scorre solo mentre giochi (più veloce con i tasti della velocità).', closed_staff_home: 'Chiuso: il personale è andato a casa (le ore di chiusura non si pagano).',
        lk_wages: 'Stipendi', lt_wages: 'Stipendi di {n} dipendenti ({d} giorni)', wage_day: '{c} al giorno', payroll: 'Stipendi al giorno', wages_paid: 'Stipendi pagati: {c} monete',
        kind_title: 'Tipo di locale', kind_generic: 'Ristorante', kind_fast_food: 'Fast food', kind_cafe: 'Caffetteria', kind_inn: 'Locanda', kind_luxury: 'Ristorante di lusso',
        kind_generic_d: 'Un po’ di tutto: la base.', kind_fast_food_d: 'Prezzi più bassi, molti più clienti che mangiano in fretta. L’arredamento conta poco.',
        kind_cafe_d: 'Prezzi un po’ più bassi, più clienti, visite veloci.', kind_inn_d: 'Calda e accogliente: gli ospiti adorano il camino e le luci soffuse la sera.',
        kind_luxury_d: 'Prezzi molto più alti, meno ospiti, che però pretendono sedute comode e sale bellissime.', kind_effects: 'Prezzi ×{p} · clienti ×{c}', kind_switch: 'Cambia · {c} monete', kind_current: 'Attuale',
        cat_outdoor: 'Esterno', act_queue: 'Aspetta fuori', act_home: 'A casa', act_arriving_staff: 'Arriva al lavoro',
        tier_1: 'Economica', tier_2: 'Standard', tier_3: 'Pregiata', tier_4: 'Lusso', f_comfy: 'sedute comode',



        err_network: 'Nessuna connessione. Riprova.', err_server: 'Qualcosa è andato storto sul server.', err_bad_credentials: 'Email o password errate.', err_email_taken: 'Questa email ha già un account.',
        err_username_taken: 'Questo username è già preso.', err_username_invalid: 'Username: 3–20 lettere, numeri o _.', err_password_short: 'La password deve avere almeno 8 caratteri.',
        err_email_invalid: 'Questa email non sembra corretta.', err_name_required: 'Scrivi il tuo nome.', err_privacy_required: 'Accetta l’informativa privacy.', err_rate_limited: 'Troppi tentativi. Aspetta un po’.',
        err_use_google: 'Questo account usa l’accesso con Google.', err_token_invalid: 'Il link è scaduto o è già stato usato.', err_no_coins: 'Monete insufficienti.', err_no_ingredients: 'Mancano ingredienti.',
        err_level_locked: 'Serve un livello più alto.', err_layout_overlap: 'Due oggetti si sovrappongono.', err_layout_outside: 'Un oggetto è fuori dalla stanza.', err_need_stove: 'Ogni cuoco ha bisogno di un fornello: mettine uno prima.',
        err_staff_full: 'Hai già tutto il personale di questo ruolo che il tuo livello consente.', cook_secs: '{b}s', cook_secs_here: '{b}s → {n}s qui', cook_hint: 'Tempo base di cottura → con il tuo fornello migliore, i frigoriferi e l’attrezzatura della cucina. Un cuoco bravo è ancora più veloce.', cook_cold: 'Servito subito dal frigo o dal congelatore', cook_cold_short: 'pronto', err_chefs_full: 'Hai già tutti i cuochi che il tuo livello consente: sali di livello per assumerne altri.', chef_stoves: 'fornelli: {n}', err_staff_last: 'Ne serve almeno uno.', err_menu_full: 'Il menù è pieno.', err_menu_empty: 'Lascia almeno un piatto nel menù.',
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
        coffee_machine: 'Coffee machine', sofa: 'Sofa', tv: 'TV', arcade: 'Arcade cabinet', gramophone: 'Gramophone', fireplace: 'Brick fireplace', vending: 'Vending machine',
        cactus: 'Cactus', sunflowers: 'Sunflowers', coat_rack: 'Coat rack', umbrella_stand: 'Umbrella stand', candles: 'Candelabra', globe: 'Globe', lantern: 'Lantern', palm: 'Palm', easel: 'Easel',
        bonsai: 'Bonsai', neon_sign: 'Neon sign', trophy_case: 'Trophy case', flower_arch: 'Flower arch',
        rug_round: 'Round rug', rug_stripes: 'Striped runner', rug_red: 'Red rug', rug_checker: 'Checkered rug', rug_persian: 'Persian rug',
        floor_blue: 'Blue tiles', floor_herringbone: 'Herringbone', floor_hex: 'Hexagon tiles', wall_tile: 'White tiles', wall_wood: 'Wood panels', wall_flowers: 'Flower wallpaper',
        cleaner: 'Cleaner', manager: 'Manager',
        bench_park: 'Park bench', tree_oak: 'Oak tree', tree_pine: 'Pine tree', tree_cherry: 'Cherry tree', bush_round: 'Flowering bush', hedge: 'Hedge', planter_flowers: 'Flower planter', street_lamp: 'Garden lamp', parasol: 'Table with parasol', menu_sign: 'Menu A-board', bike_rack: 'Bike rack', gnome: 'Garden gnome', garden_fountain: 'Garden fountain', food_truck: 'Street food truck',
        toilet: 'Toilet', toilet_luxe: 'Designer toilet', washbasin: 'Washbasin with mirror', washbasin_marble: 'Marble vanity', hand_dryer: 'Hand dryer', toilet_cubicle: 'Toilet cubicle', wc_sign: 'Restroom sign',
        wall_thin: 'Thin wall', wall_medium: 'Wall', wall_thick: 'Thick wall', wall_long: 'Long wall', wall_brick_block: 'Brick wall', half_wall_planter: 'Low wall with plants', column: 'Column',
        screen_folding: 'Folding screen', screen_bamboo: 'Bamboo screen', glass_partition: 'Glass partition', plant_monstera: 'Monstera', plant_ficus: 'Ficus', plant_stand: 'Plant shelf', plant_trough: 'Grass planter',
        rug_jute: 'Jute rug', rug_runner: 'Long runner', rug_large: 'Large rug',
        cleaning_cart: 'Cleaning cart', cleaning_station: 'Cleaning station',
        woodstove: 'Cast-iron stove', fireplace_stone: 'Stone fireplace', fireplace_modern: 'Modern fireplace', fireplace_marble: 'Marble fireplace',
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
        coffee_machine: 'Macchina del caffè', sofa: 'Divano', tv: 'TV', arcade: 'Cabinato arcade', gramophone: 'Grammofono', fireplace: 'Camino in mattoni', vending: 'Distributore',
        cactus: 'Cactus', sunflowers: 'Girasoli', coat_rack: 'Appendiabiti', umbrella_stand: 'Portaombrelli', candles: 'Candelabro', globe: 'Mappamondo', lantern: 'Lanterna', palm: 'Palma', easel: 'Cavalletto',
        bonsai: 'Bonsai', neon_sign: 'Insegna al neon', trophy_case: 'Vetrina dei trofei', flower_arch: 'Arco fiorito',
        rug_round: 'Tappeto rotondo', rug_stripes: 'Passatoia a righe', rug_red: 'Tappeto rosso', rug_checker: 'Tappeto a scacchi', rug_persian: 'Tappeto persiano',
        floor_blue: 'Piastrelle blu', floor_herringbone: 'Spina di pesce', floor_hex: 'Esagonette', wall_tile: 'Piastrelle bianche', wall_wood: 'Boiserie', wall_flowers: 'Carta da parati a fiori',
        cleaner: 'Addetto alle pulizie', manager: 'Gestore',
        bench_park: 'Panchina', tree_oak: 'Quercia', tree_pine: 'Pino', tree_cherry: 'Ciliegio', bush_round: 'Cespuglio fiorito', hedge: 'Siepe', planter_flowers: 'Fioriera', street_lamp: 'Lampione da giardino', parasol: 'Tavolino con ombrellone', menu_sign: 'Cavalletto del menù', bike_rack: 'Rastrelliera per bici', gnome: 'Nano da giardino', garden_fountain: 'Fontana da giardino', food_truck: 'Camion street food',
        toilet: 'WC', toilet_luxe: 'WC di design', washbasin: 'Lavandino con specchio', washbasin_marble: 'Mobile lavabo in marmo', hand_dryer: 'Asciugamani elettrico', toilet_cubicle: 'Box del bagno', wc_sign: 'Cartello del bagno',
        wall_thin: 'Muro sottile', wall_medium: 'Muro', wall_thick: 'Muro spesso', wall_long: 'Muro lungo', wall_brick_block: 'Muro di mattoni', half_wall_planter: 'Muretto con piante', column: 'Colonna',
        screen_folding: 'Paravento', screen_bamboo: 'Separè di bambù', glass_partition: 'Divisorio di vetro', plant_monstera: 'Monstera', plant_ficus: 'Ficus', plant_stand: 'Scaffale per piante', plant_trough: 'Fioriera con erba',
        rug_jute: 'Tappeto di juta', rug_runner: 'Passatoia lunga', rug_large: 'Tappeto grande',
        cleaning_cart: 'Carrello delle pulizie', cleaning_station: 'Postazione delle pulizie',
        woodstove: 'Stufa in ghisa', fireplace_stone: 'Camino in pietra', fireplace_modern: 'Camino moderno', fireplace_marble: 'Camino in marmo',
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

// ---------------------------------------------------------------- enlargements, car park, tips between friends
Object.assign(STRINGS.en, {
    expand_title: 'Enlarge', expand_now: 'Your room is {w}×{d} tiles. At your level each side can reach {m}.',
    add_col: 'Add a column', add_col_d: 'The room gets wider: {w}×{d}', add_row: 'Add a row', add_row_d: 'The room gets deeper, towards the street: {w}×{d}',
    unlock_side: 'The room can grow up to {n} tiles per side', unlock_parking: 'Car park level {l} ({n} spaces)',
    parking_title: 'Restaurant car park', parking_d: 'Guests who come by car spend more (+{p}% on the bill) and leave bigger tips.',
    parking_have: 'Your car park has {n} spaces.', parking_buy: 'Build a car park ({n} spaces)', parking_more: 'Enlarge the car park ({n} spaces)', parking_max: 'Your car park is as big as it gets.',
    lk_parking: 'Car park', lt_parking: 'Car park, level {n}', lk_tip_given: 'Tips to friends', lk_tip_friend: 'Tips from friends', lt_tip_given: 'Tip at {n}', lt_tip_friend: 'Tip from {n}',
    tip_friend: 'Leave a tip', tip_sent: 'You left a tip of {c} coins to {u}', tip_given_today: 'Today: {c} coins in tips',
    err_tip_limit: 'You have already left the most tips you can here today.', err_visit_first: 'Visit the restaurant first.',
});
Object.assign(STRINGS.it, {
    expand_title: 'Amplia', expand_now: 'La sala è di {w}×{d} caselle. Al tuo livello ogni lato può arrivare a {m}.',
    add_col: 'Aggiungi una colonna', add_col_d: 'La sala diventa più larga: {w}×{d}', add_row: 'Aggiungi una riga', add_row_d: 'La sala diventa più profonda, verso la strada: {w}×{d}',
    unlock_side: 'La sala può crescere fino a {n} caselle per lato', unlock_parking: 'Parcheggio livello {l} ({n} posti)',
    parking_title: 'Parcheggio del ristorante', parking_d: 'Chi arriva in auto spende di più (+{p}% sul conto) e lascia mance più alte.',
    parking_have: 'Il tuo parcheggio ha {n} posti.', parking_buy: 'Costruisci un parcheggio ({n} posti)', parking_more: 'Allarga il parcheggio ({n} posti)', parking_max: 'Il parcheggio è già al massimo.',
    lk_parking: 'Parcheggio', lt_parking: 'Parcheggio, livello {n}', lk_tip_given: 'Mance agli amici', lk_tip_friend: 'Mance dagli amici', lt_tip_given: 'Mancia da {n}', lt_tip_friend: 'Mancia da {n}',
    tip_friend: 'Lascia una mancia', tip_sent: 'Hai lasciato una mancia di {c} monete a {u}', tip_given_today: 'Oggi: {c} monete di mancia',
    err_tip_limit: 'Oggi hai già lasciato qui tutte le mance possibili.', err_visit_first: 'Prima visita il ristorante.',
});

// ---------------------------------------------------------------- names of the big catalogue
Object.assign(NAMES.en, {
    table_white: 'White table', table_iron: 'Wrought-iron table', table_oak: 'Oak table', table_hex: 'Hexagonal table', table_mosaic: 'Mosaic table',
    table_cafe: 'Café table', table_marble_round: 'Round marble table', table_gold: 'Black and gold table', table_rustic_long: 'Farmhouse table', table_banquet: 'Banquet table',
    table_booth: 'Diner table', table_picnic_blue: 'Blue picnic table', chair_white: 'White chair', chair_bistro_red: 'Red bistro chair', chair_ladder: 'Rush-seat chair',
    chair_wicker: 'Wicker chair', chair_tolix: 'Metal chair', chair_ghost: 'Ghost chair', chair_leather: 'Leather club chair', chair_throne: 'Royal throne',
    chair_bamboo: 'Bamboo chair', chair_egg: 'Egg chair', stool_wood: 'Wooden stool', stool_cushion: 'Padded stool', chair_booth: 'Booth seat',
    bench_cushion: 'Cushioned bench', stove_rustic: 'Rustic range', stove_induction: 'Induction hob', wok_station: 'Wok station', oven_pizza_steel: 'Steel pizza oven',
    counter_steel: 'Steel counter', counter_butcher: 'Butcher block', counter_pastry: 'Pastry display', counter_bar: 'Bar counter', fridge_double: 'Double fridge',
    freezer_chest: 'Chest freezer', dishwasher: 'Dishwasher', spice_rack: 'Spice rack', shelf_pots: 'Pots and pans shelf', mixer_station: 'Mixer station',
    host_stand: 'Host stand', register_modern: 'Touch-screen till', trash_recycle: 'Recycling bins', water_station: 'Water station', bean_bags: 'Bean bags',
    sofa_leather: 'Chesterfield sofa', sofa_corner: 'Corner sofa', espresso_bar: 'Espresso bar', water_cooler: 'Water cooler', radio_retro: 'Retro radio',
    guitar_stand: 'Guitar on a stand', drum_kit: 'Drum kit', harp: 'Golden harp', dj_booth: 'DJ booth', piano_grand: 'Grand piano',
    pinball: 'Pinball', foosball: 'Table football', dartboard: 'Dartboard', pool_table: 'Pool table', board_games: 'Board games',
    claw_machine: 'Claw machine', stove_tile: 'Tiled stove', electric_fire: 'Electric fireplace', popcorn_machine: 'Popcorn machine', ice_cream_cart: 'Ice-cream cart',
    candy_jars: 'Candy jars', plant_snake: 'Snake plant', plant_olive: 'Olive tree in a pot', plant_fern: 'Fern', plant_lemon: 'Lemon tree',
    plant_bamboo: 'Lucky bamboo', plant_orchid: 'Orchid', plant_hanging: 'Hanging plant', plant_lavender: 'Lavender box', terrarium: 'Terrarium',
    cactus_trio: 'Three cacti', statue_bust: 'Marble bust', vase_tall: 'Tall vase', mirror_standing: 'Standing mirror', telescope: 'Telescope',
    armor_suit: 'Suit of armour', bird_cage: 'Bird cage', china_cabinet: 'China cabinet', wine_rack: 'Wine rack', wine_barrels: 'Wine barrels',
    magazine_rack: 'Magazine rack', record_shelf: 'Record shelf', flower_cart: 'Flower cart', xmas_tree: 'Christmas tree', pumpkins: 'Pumpkins',
    fishbowl: 'Goldfish bowl', sculpture: 'Golden sculpture', lamp_arc: 'Arc lamp', lamp_tiffany: 'Tiffany lamp', lamp_paper: 'Paper lantern lamp',
    lamp_tripod: 'Tripod lamp', candelabra_floor: 'Floor candelabra', rug_oval: 'Oval rug', rug_zigzag: 'Zigzag rug', rug_sun: 'Sun rug',
    rug_kilim: 'Kilim', rug_green: 'Grass rug', rug_star: 'Star rug', rug_hall: 'Hall runner', rug_shag: 'Shaggy rug',
    toilet_gold: 'Golden toilet', washbasin_bowl: 'Bowl washbasin', mirror_led: 'Basin with LED mirror', towel_rack: 'Towel rack', baby_change: 'Baby changing table',
    soap_station: 'Soap dispenser', wall_stone_block: 'Stone wall', wall_glass_brick: 'Glass-brick wall', lattice_screen: 'Trellis with ivy', curtain_red: 'Velvet curtain',
    bookshelf_divider: 'Bookshelf divider', screen_shoji: 'Paper screen', wall_ivy: 'Green wall', half_wall_wood: 'Wooden half wall', rope_barrier: 'Rope barrier',
    arch_flowers: 'Rose arch', lamp_lantern_post: 'Lantern post', lamp_double: 'Double street lamp', lamp_bollard: 'Bollard light', lamp_string: 'String lights',
    lamp_torch: 'Garden torch', lamp_modern: 'Modern street light', lamp_victorian: 'Victorian lamp post', heater_patio: 'Patio heater', flower_bed: 'Flower bed',
    pond: 'Garden pond', swing_garden: 'Garden swing', bird_bath: 'Bird bath', statue_garden: 'Garden statue', wheelbarrow: 'Wheelbarrow of flowers',
    pergola: 'Pergola with wisteria', rock_garden: 'Zen garden', bamboo_clump: 'Bamboo', tree_maple: 'Maple tree', tree_birch: 'Birch tree',
    tree_palm: 'Palm tree', bush_hydrangea: 'Hydrangea', topiary: 'Topiary', mailbox: 'Mailbox', bicycle: 'Bicycle',
    slide_kids: 'Kids’ slide', sandbox: 'Sandbox', bench_stone: 'Stone bench', parasol_big: 'Big parasol', fountain_wall: 'Wall fountain',
    beehive: 'Beehive', floor_walnut: 'Walnut planks', floor_slate: 'Slate', floor_terrazzo: 'Terrazzo', floor_diagonal: 'Black and white diamonds',
    floor_cork: 'Cork', floor_mosaic: 'Blue mosaic', floor_gold_parquet: 'Golden parquet', floor_concrete: 'Polished concrete', floor_red_tiles: 'Red tiles',
    wall_navy: 'Navy blue', wall_mint_stripes: 'Mint stripes', wall_stone: 'Stone', wall_gold_damask: 'Gold damask', wall_greenery: 'Leafy wallpaper',
    wall_chalk: 'Chalkboard', wall_tropical: 'Tropical', wall_concrete: 'Concrete', wall_pastel: 'Pastel blue',
});
Object.assign(NAMES.it, {
    table_white: 'Tavolo bianco', table_iron: 'Tavolino in ferro battuto', table_oak: 'Tavolo di rovere', table_hex: 'Tavolo esagonale', table_mosaic: 'Tavolo a mosaico',
    table_cafe: 'Tavolino da caffè', table_marble_round: 'Tavolo tondo di marmo', table_gold: 'Tavolo nero e oro', table_rustic_long: 'Tavolo rustico', table_banquet: 'Tavolo da banchetto',
    table_booth: 'Tavolo da diner', table_picnic_blue: 'Tavolo da picnic blu', chair_white: 'Sedia bianca', chair_bistro_red: 'Sedia bistrot rossa', chair_ladder: 'Sedia impagliata',
    chair_wicker: 'Sedia di vimini', chair_tolix: 'Sedia in metallo', chair_ghost: 'Sedia trasparente', chair_leather: 'Poltrona in pelle', chair_throne: 'Trono reale',
    chair_bamboo: 'Sedia di bambù', chair_egg: 'Poltrona a uovo', stool_wood: 'Sgabello di legno', stool_cushion: 'Sgabello imbottito', chair_booth: 'Divanetto da diner',
    bench_cushion: 'Panca con cuscini', stove_rustic: 'Cucina rustica', stove_induction: 'Piano a induzione', wok_station: 'Postazione wok', oven_pizza_steel: 'Forno per pizza in acciaio',
    counter_steel: 'Bancone in acciaio', counter_butcher: 'Bancone in legno massello', counter_pastry: 'Vetrina dei dolci', counter_bar: 'Bancone bar', fridge_double: 'Frigo doppio',
    freezer_chest: 'Congelatore a pozzo', dishwasher: 'Lavastoviglie', spice_rack: 'Rastrelliera delle spezie', shelf_pots: 'Scaffale delle pentole', mixer_station: 'Postazione impastatrice',
    host_stand: 'Leggio dell’accoglienza', register_modern: 'Cassa touch', trash_recycle: 'Raccolta differenziata', water_station: 'Angolo dell’acqua', bean_bags: 'Pouf',
    sofa_leather: 'Divano Chesterfield', sofa_corner: 'Divano angolare', espresso_bar: 'Banco espresso', water_cooler: 'Erogatore d’acqua', radio_retro: 'Radio d’epoca',
    guitar_stand: 'Chitarra sul supporto', drum_kit: 'Batteria', harp: 'Arpa dorata', dj_booth: 'Consolle del DJ', piano_grand: 'Pianoforte a coda',
    pinball: 'Flipper', foosball: 'Calcio balilla', dartboard: 'Freccette', pool_table: 'Biliardo', board_games: 'Giochi da tavolo',
    claw_machine: 'Pesca peluche', stove_tile: 'Stufa in maiolica', electric_fire: 'Camino elettrico', popcorn_machine: 'Macchina dei popcorn', ice_cream_cart: 'Carretto dei gelati',
    candy_jars: 'Barattoli di caramelle', plant_snake: 'Sansevieria', plant_olive: 'Ulivo in vaso', plant_fern: 'Felce', plant_lemon: 'Limone in vaso',
    plant_bamboo: 'Bambù in vaso', plant_orchid: 'Orchidea', plant_hanging: 'Pianta pendente', plant_lavender: 'Cassetta di lavanda', terrarium: 'Terrario',
    cactus_trio: 'Tre cactus', statue_bust: 'Busto di marmo', vase_tall: 'Vaso alto', mirror_standing: 'Specchio da terra', telescope: 'Cannocchiale',
    armor_suit: 'Armatura', bird_cage: 'Gabbietta con uccellino', china_cabinet: 'Credenza delle porcellane', wine_rack: 'Portabottiglie', wine_barrels: 'Botti di vino',
    magazine_rack: 'Portariviste', record_shelf: 'Scaffale dei vinili', flower_cart: 'Carretto dei fiori', xmas_tree: 'Albero di Natale', pumpkins: 'Zucche',
    fishbowl: 'Boccia con pesce rosso', sculpture: 'Scultura dorata', lamp_arc: 'Lampada ad arco', lamp_tiffany: 'Lampada Tiffany', lamp_paper: 'Lampada di carta',
    lamp_tripod: 'Lampada a treppiede', candelabra_floor: 'Candelabro da terra', rug_oval: 'Tappeto ovale', rug_zigzag: 'Tappeto a zigzag', rug_sun: 'Tappeto sole',
    rug_kilim: 'Kilim', rug_green: 'Tappeto erba', rug_star: 'Tappeto stella', rug_hall: 'Passatoia da salone', rug_shag: 'Tappeto a pelo lungo',
    toilet_gold: 'WC dorato', washbasin_bowl: 'Lavabo a bacinella', mirror_led: 'Lavabo con specchio LED', towel_rack: 'Portasciugamani', baby_change: 'Fasciatoio',
    soap_station: 'Dispenser di sapone', wall_stone_block: 'Muro di pietra', wall_glass_brick: 'Muro di vetrocemento', lattice_screen: 'Grigliato con edera', curtain_red: 'Tenda di velluto',
    bookshelf_divider: 'Libreria divisoria', screen_shoji: 'Paravento di carta', wall_ivy: 'Parete verde', half_wall_wood: 'Muretto di legno', rope_barrier: 'Cordone',
    arch_flowers: 'Arco di rose', lamp_lantern_post: 'Lampione a lanterna', lamp_double: 'Lampione doppio', lamp_bollard: 'Paletto luminoso', lamp_string: 'Filo di lucine',
    lamp_torch: 'Torcia da giardino', lamp_modern: 'Lampione moderno', lamp_victorian: 'Lampione vittoriano', heater_patio: 'Fungo riscaldante', flower_bed: 'Aiuola fiorita',
    pond: 'Laghetto', swing_garden: 'Dondolo', bird_bath: 'Fontanella per uccelli', statue_garden: 'Statua da giardino', wheelbarrow: 'Carriola di fiori',
    pergola: 'Pergolato con glicine', rock_garden: 'Giardino zen', bamboo_clump: 'Bambù', tree_maple: 'Acero', tree_birch: 'Betulla',
    tree_palm: 'Palma', bush_hydrangea: 'Ortensia', topiary: 'Pianta topiaria', mailbox: 'Cassetta della posta', bicycle: 'Bicicletta',
    slide_kids: 'Scivolo per bambini', sandbox: 'Sabbiera', bench_stone: 'Panchina di pietra', parasol_big: 'Ombrellone grande', fountain_wall: 'Fontana a muro',
    beehive: 'Arnia', floor_walnut: 'Parquet di noce', floor_slate: 'Ardesia', floor_terrazzo: 'Graniglia', floor_diagonal: 'Rombi bianchi e neri',
    floor_cork: 'Sughero', floor_mosaic: 'Mosaico blu', floor_gold_parquet: 'Parquet dorato', floor_concrete: 'Cemento lucido', floor_red_tiles: 'Piastrelle rosse',
    wall_navy: 'Blu notte', wall_mint_stripes: 'Righe menta', wall_stone: 'Pietra', wall_gold_damask: 'Damasco oro', wall_greenery: 'Carta da parati con foglie',
    wall_chalk: 'Lavagna', wall_tropical: 'Tropicale', wall_concrete: 'Cemento', wall_pastel: 'Azzurro pastello',
});

// ---------------------------------------------------------------- new dishes and ingredients
Object.assign(NAMES.en, {
    caprese: 'Caprese salad', garden_salad: 'Garden salad', iced_tea: 'Iced tea', pancakes: 'Pancakes with honey', milkshake: 'Strawberry milkshake', corn_soup: 'Corn soup',
    banana_split: 'Banana split', tacos: 'Tacos', pumpkin_soup: 'Pumpkin soup', veggie_lasagna: 'Vegetable lasagne', cream_puffs: 'Cream puffs', shrimp_pasta: 'Shrimp pasta',
    parmigiana: 'Aubergine parmigiana', pork_ribs: 'Honey-glazed ribs', tiramisu: 'Tiramisù', sushi: 'Sushi', paella: 'Paella', steak_fries: 'Steak and fries',
    lettuce: 'Lettuce', corn: 'Corn', eggplant: 'Aubergine', pumpkin: 'Pumpkin', shrimp: 'Shrimp', pork: 'Pork', cream: 'Cream', butter: 'Butter', honey: 'Honey', banana: 'Banana', beans: 'Beans', tea: 'Tea',
});
Object.assign(NAMES.it, {
    caprese: 'Caprese', garden_salad: 'Insalata dell’orto', iced_tea: 'Tè freddo', pancakes: 'Pancake al miele', milkshake: 'Frappè alla fragola', corn_soup: 'Zuppa di mais',
    banana_split: 'Banana split', tacos: 'Tacos', pumpkin_soup: 'Vellutata di zucca', veggie_lasagna: 'Lasagne alle verdure', cream_puffs: 'Bignè alla crema', shrimp_pasta: 'Pasta ai gamberi',
    parmigiana: 'Parmigiana di melanzane', pork_ribs: 'Costine al miele', tiramisu: 'Tiramisù', sushi: 'Sushi', paella: 'Paella', steak_fries: 'Bistecca e patatine',
    lettuce: 'Lattuga', corn: 'Mais', eggplant: 'Melanzana', pumpkin: 'Zucca', shrimp: 'Gamberi', pork: 'Maiale', cream: 'Panna', butter: 'Burro', honey: 'Miele', banana: 'Banana', beans: 'Fagioli', tea: 'Tè',
});

// ---------------------------------------------------------------- staff looks: new hairstyles, uniforms, accessories
Object.assign(STRINGS.en, {
    hair_afro: 'Afro', hair_mohawk: 'Mohawk', hair_braids: 'Braids', hair_pigtails: 'Pigtails', hair_pixie: 'Pixie', hair_topknot: 'Top knot', hair_wavy: 'Wavy', hair_sidepart: 'Side parting',
    uni_sunny: 'Sunny', uni_ocean: 'Ocean', uni_candy: 'Candy', uni_lavender: 'Lavender', uni_forest: 'Forest', uni_chef_white: 'White chef jacket', uni_diner: 'Diner', uni_ranch: 'Ranch',
    uni_chef_black: 'Black chef jacket', uni_night: 'Night blue', uni_chef_red: 'Red chef jacket', uni_royal: 'Royal',
    acc_glasses: 'Glasses', acc_facial: 'Moustache and beard', acc_headwear: 'On the head',
    acc_glasses_none: 'None', acc_glasses_round: 'Round', acc_glasses_square: 'Square', acc_glasses_sun: 'Sunglasses', acc_glasses_heart: 'Funny pink',
    acc_facial_none: 'None', acc_facial_moustache: 'Moustache', acc_facial_beard: 'Beard', acc_facial_goatee: 'Goatee',
    acc_headwear_none: 'Nothing', acc_headwear_cap: 'Cap', acc_headwear_beret: 'Beret', acc_headwear_bandana: 'Bandana', acc_headwear_beanie: 'Beanie', acc_headwear_flower: 'Flower', acc_headwear_bow: 'Bow',
});
Object.assign(STRINGS.it, {
    hair_afro: 'Afro', hair_mohawk: 'Cresta', hair_braids: 'Treccine', hair_pigtails: 'Codini', hair_pixie: 'Pixie', hair_topknot: 'Chignon alto', hair_wavy: 'Mossi', hair_sidepart: 'Riga di lato',
    uni_sunny: 'Sole', uni_ocean: 'Oceano', uni_candy: 'Caramella', uni_lavender: 'Lavanda', uni_forest: 'Bosco', uni_chef_white: 'Giacca da chef bianca', uni_diner: 'Diner', uni_ranch: 'Ranch',
    uni_chef_black: 'Giacca da chef nera', uni_night: 'Blu notte', uni_chef_red: 'Giacca da chef rossa', uni_royal: 'Reale',
    acc_glasses: 'Occhiali', acc_facial: 'Baffi e barba', acc_headwear: 'In testa',
    acc_glasses_none: 'Nessuno', acc_glasses_round: 'Tondi', acc_glasses_square: 'Squadrati', acc_glasses_sun: 'Da sole', acc_glasses_heart: 'Rosa buffi',
    acc_facial_none: 'Niente', acc_facial_moustache: 'Baffi', acc_facial_beard: 'Barba', acc_facial_goatee: 'Pizzetto',
    acc_headwear_none: 'Niente', acc_headwear_cap: 'Cappellino', acc_headwear_beret: 'Basco', acc_headwear_bandana: 'Bandana', acc_headwear_beanie: 'Berretto di lana', acc_headwear_flower: 'Fiore', acc_headwear_bow: 'Fiocco',
});

// ---------------------------------------------------------------- build mode
Object.assign(STRINGS.en, {
    moving_piece: 'Moving…', rotate_left: 'Turn left', rotate_right: 'Turn right', place_here: 'Place here', repeat_place: 'Place several', repeat_hint: 'After placing one, start placing another of the same',
    fits: 'fits here', copy_piece: 'Copy', key_delete: 'Delete key', drag_hint: 'Drag it to move it · R/Q turn · right click turns',
    build_hint: 'Pick a piece below, or drag one in the room to move it. The blue tile is the door: keep it free.',
});
Object.assign(STRINGS.it, {
    moving_piece: 'Sposti…', rotate_left: 'Ruota a sinistra', rotate_right: 'Ruota a destra', place_here: 'Metti qui', repeat_place: 'Più copie', repeat_hint: 'Dopo averne messo uno, ne prepara subito un altro uguale',
    fits: 'ci sta', copy_piece: 'Copia', key_delete: 'Tasto Canc', drag_hint: 'Trascinalo per spostarlo · R/Q ruota · clic destro ruota',
    build_hint: 'Scegli un pezzo qui sotto, o trascinane uno nella sala per spostarlo. La casella blu è la porta: lasciala libera.',
});

Object.assign(STRINGS.en, {elsewhere_title: 'Forkville is open somewhere else', elsewhere_body: 'You opened the game in another tab or on another device. To avoid mix-ups only one copy plays at a time: this one is paused.', play_here: 'Play here', err_other_instance: 'The game is open somewhere else.'});
Object.assign(STRINGS.it, {elsewhere_title: 'Forkville è aperto altrove', elsewhere_body: 'Hai aperto il gioco in un’altra scheda o su un altro dispositivo. Per evitare conflitti ne gioca uno solo alla volta: questo è in pausa.', play_here: 'Gioca qui', err_other_instance: 'Il gioco è aperto altrove.'});

// ---------------------------------------------------------------- upper floors
Object.assign(STRINGS.en, {
    floor_view: 'Floor', floor_ground: 'Ground floor', floor_ground_short: 'G', floor_n: 'Floor {n}', floor_build: 'Build floor {n}', floor_build_d: 'It starts small ({s}×{s}) and can be enlarged. Connect it with stairs or a lift.',
    floor_built: 'Floor {n} built! Now put stairs or a lift on the floor below.', floor_below_full: 'As big as the floor below', unlock_floor: 'Floor {n} of the restaurant', lt_floor: 'Floor {n} built',
    lk_floor: 'New floors', cat_stairs: 'Stairs and lift', err_stairs_nowhere: 'Stairs and lifts must lead to a floor above, inside its walls.', err_floor_below: 'A floor cannot be bigger than the floor below.',
    stairs_wood: 'Wooden stairs', stairs_stone: 'Stone stairs', stairs_spiral: 'Spiral staircase', lift_glass: 'Glass lift',
});
Object.assign(STRINGS.it, {
    floor_view: 'Piano', floor_ground: 'Piano terra', floor_ground_short: 'T', floor_n: 'Piano {n}', floor_build: 'Costruisci il piano {n}', floor_build_d: 'Parte piccolo ({s}×{s}) e si può ampliare. Collegalo con scale o ascensore.',
    floor_built: 'Piano {n} costruito! Ora metti scale o un ascensore al piano di sotto.', floor_below_full: 'Grande quanto il piano di sotto', unlock_floor: 'Piano {n} del ristorante', lt_floor: 'Costruito il piano {n}',
    lk_floor: 'Nuovi piani', cat_stairs: 'Scale e ascensore', err_stairs_nowhere: 'Scale e ascensori devono portare a un piano superiore, dentro i suoi muri.', err_floor_below: 'Un piano non può essere più grande di quello sotto.',
});
Object.assign(NAMES.en, {stairs_wood: 'Wooden stairs', stairs_stone: 'Stone stairs', stairs_spiral: 'Spiral staircase', lift_glass: 'Glass lift'});
Object.assign(NAMES.it, {stairs_wood: 'Scala di legno', stairs_stone: 'Scala di pietra', stairs_spiral: 'Scala a chiocciola', lift_glass: 'Ascensore di vetro'});

Object.assign(STRINGS.en, {stairs_under_floor: 'must stand under the floor above', lift_ground: 'lifts start on the ground floor', no_floor_above: 'build a floor above first'});
Object.assign(STRINGS.it, {stairs_under_floor: 'deve stare sotto il piano di sopra', lift_ground: 'l’ascensore parte dal piano terra', no_floor_above: 'prima costruisci un piano sopra'});

Object.assign(STRINGS.en, {floor_up: 'Go up a floor', floor_down: 'Go down a floor'});
Object.assign(STRINGS.it, {floor_up: 'Sali di un piano', floor_down: 'Scendi di un piano'});

// ---------------------------------------------------------------- weather kinds
Object.assign(STRINGS.en, {weather_sunny: 'Bright sunshine', weather_clear: 'Clear', weather_cloudy: 'Cloudy', weather_drizzle: 'Drizzle', weather_rain: 'Heavy rain', weather_hail: 'Hail', weather_light_snow: 'Light snow', weather_snow: 'Heavy snow'});
Object.assign(STRINGS.it, {weather_sunny: 'Sole splendente', weather_clear: 'Limpido', weather_cloudy: 'Nuvoloso', weather_drizzle: 'Pioggerella', weather_rain: 'Pioggia forte', weather_hail: 'Grandine', weather_light_snow: 'Nevicata leggera', weather_snow: 'Grande nevicata'});

// ---------------------------------------------------------------- seasonal pieces
Object.assign(NAMES.en, {
    heart_arch: 'Heart arch', rose_vase: 'Vase of roses', heart_balloons: 'Heart balloons', cupid_statue: 'Cupid statue', rug_heart: 'Heart rug',
    easter_egg_big: 'Giant Easter egg', bunny_statue: 'Easter bunny', egg_basket: 'Basket of eggs', easter_tree: 'Easter egg tree', chick_planter: 'Planter with chicks',
    lemonade_stand: 'Lemonade stand', surfboard: 'Surfboard', watermelon_cart: 'Watermelon cart', hammock: 'Hammock', deck_chair: 'Deck chair',
    flamingo: 'Pink flamingo', jack_lantern: 'Jack-o\'-lantern', scarecrow: 'Scarecrow', hay_bales: 'Hay bales', autumn_wreath: 'Autumn wreath',
    friendly_ghost: 'Friendly ghost', snowman: 'Snowman', gift_pile: 'Pile of presents', nutcracker: 'Nutcracker', reindeer: 'Reindeer',
    xmas_arch: 'Christmas light arch', advent_candles: 'Advent candles',
});
Object.assign(NAMES.it, {
    heart_arch: 'Arco di cuori', rose_vase: 'Vaso di rose', heart_balloons: 'Palloncini a cuore', cupid_statue: 'Statua di Cupido', rug_heart: 'Tappeto a cuore',
    easter_egg_big: 'Uovo di Pasqua gigante', bunny_statue: 'Coniglietto pasquale', egg_basket: 'Cestino di ovetti', easter_tree: 'Albero con le uova', chick_planter: 'Fioriera con pulcini',
    lemonade_stand: 'Chiosco della limonata', surfboard: 'Tavola da surf', watermelon_cart: 'Carretto delle angurie', hammock: 'Amaca', deck_chair: 'Sdraio',
    flamingo: 'Fenicottero rosa', jack_lantern: 'Zucca intagliata', scarecrow: 'Spaventapasseri', hay_bales: 'Balle di fieno', autumn_wreath: 'Ghirlanda d’autunno',
    friendly_ghost: 'Fantasmino simpatico', snowman: 'Pupazzo di neve', gift_pile: 'Pila di regali', nutcracker: 'Schiaccianoci', reindeer: 'Renna',
    xmas_arch: 'Arco di luci natalizie', advent_candles: 'Candele dell’Avvento',
});
Object.assign(STRINGS.en, {
    cat_seasonal: 'Seasonal', ev_valentine: 'Valentine’s Day', ev_easter: 'Easter', ev_summer: 'Summer', ev_autumn: 'Autumn and Halloween', ev_christmas: 'Christmas',
    ev_now: 'On now: {e}', ev_none: 'No event right now', ev_back: 'From {m}', ev_until: 'Until {m}', ev_kept: 'Pieces you buy stay for ever',
    ev_started: 'Seasonal pieces for {e} are on sale in Build!', err_out_of_season: 'This piece is on sale only during its event.', seasonal_tag: 'Seasonal',
});
Object.assign(STRINGS.it, {
    cat_seasonal: 'Stagionali', ev_valentine: 'San Valentino', ev_easter: 'Pasqua', ev_summer: 'Estate', ev_autumn: 'Autunno e Halloween', ev_christmas: 'Natale',
    ev_now: 'In corso: {e}', ev_none: 'Nessun evento in corso', ev_back: 'Dal {m}', ev_until: 'Fino al {m}', ev_kept: 'I pezzi acquistati restano per sempre',
    ev_started: 'In Arreda ci sono i pezzi di {e}!', err_out_of_season: 'Questo pezzo si compra solo durante il suo evento.', seasonal_tag: 'Stagionale',
});

Object.assign(STRINGS.en, {act_waterPlants: 'Watering the plants'});
Object.assign(STRINGS.it, {act_waterPlants: 'Annaffia le piante'});

// ---------------------------------------------------------------- door and walls
Object.assign(STRINGS.en, {
    door_title: 'Door', door_wood: 'Wooden door', door_rustic: 'Rustic door', door_glass: 'Glass door', door_red: 'Red door', door_double: 'Double door', door_arched: 'Arched door', door_french: 'French door', door_gold: 'Black and gold door',
    door_current: 'Current', door_move: 'Move the door', door_move_d: 'Then tap a green tile along the front wall.', door_pick: 'Tap a green tile of the front wall', door_moved: 'The door has moved!',
    door_changed: 'New door installed!', door_save_first: 'Save (or cancel) your changes first.', err_door_spot: 'The door cannot go there: the tile inside and the path outside must be free.', door_spot: 'The door cannot go there',
    walls_adaptive: 'Walls: adaptive (low on your side)', walls_low: 'Walls: always low', walls_high: 'Walls: always high', lk_door: 'Door',
});
Object.assign(STRINGS.it, {
    door_title: 'Porta', door_wood: 'Porta di legno', door_rustic: 'Porta rustica', door_glass: 'Porta a vetri', door_red: 'Porta rossa', door_double: 'Porta a due ante', door_arched: 'Porta ad arco', door_french: 'Porta-finestra', door_gold: 'Porta nera e oro',
    door_current: 'Attuale', door_move: 'Sposta la porta', door_move_d: 'Poi tocca una casella verde lungo il muro davanti.', door_pick: 'Tocca una casella verde del muro davanti', door_moved: 'La porta è stata spostata!',
    door_changed: 'Nuova porta montata!', door_save_first: 'Prima salva (o annulla) le modifiche.', err_door_spot: 'Lì la porta non può andare: servono libere la casella dentro e il vialetto fuori.', door_spot: 'Lì la porta non può andare',
    walls_adaptive: 'Pareti: adattive (basse dal tuo lato)', walls_low: 'Pareti: sempre basse', walls_high: 'Pareti: sempre alte', lk_door: 'Porta',
});

Object.assign(STRINGS.en, {sort_by: 'Sort by', sort_level: 'Level', sort_price_asc: 'Price: low to high', sort_price_desc: 'Price: high to low', sort_pop: 'Popularity', sort_tier: 'Quality', sort_name: 'Name'});
Object.assign(STRINGS.it, {sort_by: 'Ordina per', sort_level: 'Livello', sort_price_asc: 'Prezzo crescente', sort_price_desc: 'Prezzo decrescente', sort_pop: 'Popolarità', sort_tier: 'Qualità', sort_name: 'Nome'});

// ---------------------------------------------------------------- cold service
Object.assign(NAMES.en, {drinks_fridge: 'Drinks fridge', gelato_freezer: 'Gelato freezer', gelato: 'Gelato cup', sorbet: 'Lemon and strawberry sorbet', cold_brew: 'Iced coffee'});
Object.assign(NAMES.it, {drinks_fridge: 'Frigo delle bibite', gelato_freezer: 'Vetrina dei gelati', gelato: 'Coppa di gelato', sorbet: 'Sorbetto limone e fragola', cold_brew: 'Caffè freddo'});
Object.assign(STRINGS.en, {act_fetch: 'Fetching a cold drink or an ice cream'});
Object.assign(STRINGS.it, {act_fetch: 'Prende una bibita fresca o un gelato'});

// ---------------------------------------------------------------- cashier, joints, badges
Object.assign(NAMES.en, {cashier: 'Cashier', wall_corner_thin: 'Thin wall corner', wall_corner: 'Wall corner', wall_corner_thick: 'Thick wall corner', wall_tee_thin: 'Thin T-joint', wall_tee: 'Wall T-joint', wall_tee_thick: 'Thick T-joint', wall_cross_thin: 'Thin wall cross', wall_cross: 'Wall cross', toilet_cubicle: 'Toilet cubicle'});
Object.assign(NAMES.it, {cashier: 'Cassiere', wall_corner_thin: 'Angolo di muro sottile', wall_corner: 'Angolo di muro', wall_corner_thick: 'Angolo di muro spesso', wall_tee_thin: 'Muro a T sottile', wall_tee: 'Muro a T', wall_tee_thick: 'Muro a T spesso', wall_cross_thin: 'Incrocio di muri sottile', wall_cross: 'Incrocio di muri', toilet_cubicle: 'Box del bagno con WC'});
Object.assign(STRINGS.en, {
    act_paying: 'Paying at the till', cashier_no_till: 'Cashier without a till: add a cash register', err_need_register: 'A cashier needs a cash register: place one first.',
    for_role_chef: 'Used by the chef', for_role_waiter: 'Used by the waiters', for_role_cleaner: 'Used by the cleaners', for_role_cashier: 'Used by the cashier', for_role_staff: 'For the staff’s breaks',
    unlock_cashier: 'You can hire a cashier: higher bills and a tip from every guest',
});
Object.assign(STRINGS.it, {
    act_paying: 'Paga alla cassa', cashier_no_till: 'Cassiere senza cassa: aggiungi un registratore di cassa', err_need_register: 'Il cassiere ha bisogno di un registratore di cassa: mettine uno prima.',
    for_role_chef: 'Lo usa il cuoco', for_role_waiter: 'Lo usano i camerieri', for_role_cleaner: 'Lo usa l’addetto alle pulizie', for_role_cashier: 'Lo usa il cassiere', for_role_staff: 'Per le pause del personale',
    unlock_cashier: 'Puoi assumere un cassiere: conti più alti e una mancia da ogni cliente',
});
Object.assign(STRINGS.en, {
    act_noStation: 'No station of their own: nothing to do', chef_no_stove: 'Chefs without a stove: {n} · add a stove', cleaner_no_station: 'Cleaners without a cart or cleaning station: {n} · add one each',
    manager_no_stand: 'Manager without a host stand: add one by the entrance', for_role_manager: 'The manager’s station',
});
Object.assign(STRINGS.it, {
    act_noStation: 'Senza una postazione: non può lavorare', chef_no_stove: 'Cuochi senza fornello: {n} · aggiungi un fornello', cleaner_no_station: 'Addetti alle pulizie senza carrello o postazione: {n} · uno a testa',
    manager_no_stand: 'Manager senza leggio dell’accoglienza: mettine uno vicino all’ingresso', for_role_manager: 'La postazione del manager',
});
Object.assign(STRINGS.en, {
    garage_title: 'Underground car park (floor −1)', garage_d: 'A ramp down to a car park under the street: many more spaces than the one outside, and every guest who parks spends more.',
    garage_have: 'Your underground car park has {n} spaces.', garage_buy: 'Dig an underground car park ({n} spaces)', garage_more: 'Add a level of spaces ({n} spaces)',
    garage_max: 'The underground car park is as big as it gets.', lk_garage: 'Underground car park', lt_garage: 'Underground car park, level {n}', unlock_garage: 'Underground car park level {l} ({n} spaces)',
});
Object.assign(STRINGS.it, {
    garage_title: 'Parcheggio interrato (piano −1)', garage_d: 'Una rampa scende a un parcheggio sotto la strada: molti più posti di quello esterno, e chi parcheggia spende di più.',
    garage_have: 'Il tuo parcheggio interrato ha {n} posti.', garage_buy: 'Scava un parcheggio interrato ({n} posti)', garage_more: 'Aggiungi posti ({n} posti)',
    garage_max: 'Il parcheggio interrato è già al massimo.', lk_garage: 'Parcheggio interrato', lt_garage: 'Parcheggio interrato, livello {n}', unlock_garage: 'Parcheggio interrato livello {l} ({n} posti)',
});
Object.assign(STRINGS.en, {
    todo_title: 'Requirements', todo_chip: 'To do: {n}', todo_go: 'Fix', todo_intro: 'What your guests need, in order: first what it takes to open, then what keeps them happy, then a station for every worker you hired.',
    req_open: 'To open', req_happy: 'Happy guests', req_staff: 'A station for every worker',
    req_seat: 'Table with a chair ({n} seats)', req_seat_d: 'A chair pushed up to a table, reachable from the door.',
    req_stove: 'Chef’s station: a stove ({n})', req_stove_d: 'Where the chef cooks. One chef per stove.',
    req_counter: 'Waiter’s station: a counter ({n})', req_counter_d: 'The chef puts the dishes there and the waiters pick them up.',
    req_chef: 'A chef at a stove', req_chef_d: 'Hire a chef in Staff.',
    req_waiter: 'A waiter with tables', req_waiter_d: 'Waiters take the orders and serve their own tables.',
    req_menu: 'A dish on the menu ({n})', req_menu_d: 'Guests order from the menu.',
    req_toilet: 'Toilet (WC)', req_toilet_d: 'Without one guests are unhappy and leave worse reviews.',
    req_washbasin: 'Washbasin', req_washbasin_d: 'Guests wash their hands after the toilet.',
    req_sink: 'Sink for the dishes', req_sink_d: 'Plates are washed faster (waiters and cleaners use it).',
    req_cleaner: 'Cleaner with a cart or cleaning station', req_cleaner_d: 'Cleans floor and restrooms, helps clear tables, waters the plants.',
    req_trash: 'Trash bin', req_trash_d: 'Less litter on the floor.',
    req_light: 'Lamps (at least 2)', req_light_d: 'In the evening guests like a well-lit room.',
    req_st_chef: 'Chefs at a stove: {n}/{m}', req_st_chef_d: 'Each chef needs a stove of their own.',
    req_st_waiter: 'Waiters with tables: {n}/{m}', req_st_waiter_d: 'Tables are shared out among the waiters: add tables.',
    req_st_cleaner: 'Cleaners with a cart or station: {n}/{m}', req_st_cleaner_d: 'One cleaning cart or cleaning station each.',
    req_st_manager: 'Manager with a host stand: {n}/{m}', req_st_manager_d: 'The host stand by the entrance is the manager’s station.',
    req_st_cashier: 'Cashiers with a till: {n}/{m}', req_st_cashier_d: 'One cash register each.',
});
Object.assign(STRINGS.it, {
    todo_title: 'Requisiti', todo_chip: 'Da fare: {n}', todo_go: 'Vai', todo_intro: 'Cosa serve ai tuoi clienti, in ordine: prima quello che serve per aprire, poi quello che li rende contenti, poi una postazione per ogni dipendente assunto.',
    req_open: 'Per aprire', req_happy: 'Clienti contenti', req_staff: 'Una postazione per ogni dipendente',
    req_seat: 'Tavolo con sedia ({n} posti)', req_seat_d: 'Una sedia accostata a un tavolo, raggiungibile dalla porta.',
    req_stove: 'Postazione del cuoco: un fornello ({n})', req_stove_d: 'Dove il cuoco cucina. Un cuoco per fornello.',
    req_counter: 'Postazione del cameriere: un bancone ({n})', req_counter_d: 'Il cuoco ci posa i piatti e i camerieri li ritirano.',
    req_chef: 'Un cuoco al fornello', req_chef_d: 'Assumi un cuoco in Staff.',
    req_waiter: 'Un cameriere con i suoi tavoli', req_waiter_d: 'I camerieri prendono le ordinazioni e servono i loro tavoli.',
    req_menu: 'Un piatto nel menù ({n})', req_menu_d: 'I clienti ordinano dal menù.',
    req_toilet: 'Bagno (WC)', req_toilet_d: 'Senza, i clienti sono scontenti e lasciano recensioni peggiori.',
    req_washbasin: 'Lavabo', req_washbasin_d: 'I clienti si lavano le mani dopo il bagno.',
    req_sink: 'Lavello per i piatti', req_sink_d: 'I piatti si lavano più in fretta (lo usano camerieri e addetti alle pulizie).',
    req_cleaner: 'Addetto alle pulizie con carrello o postazione', req_cleaner_d: 'Pulisce pavimento e bagni, aiuta a sparecchiare, annaffia le piante.',
    req_trash: 'Cestino', req_trash_d: 'Meno sporcizia per terra.',
    req_light: 'Lampade (almeno 2)', req_light_d: 'La sera i clienti amano una sala ben illuminata.',
    req_st_chef: 'Cuochi con un fornello: {n}/{m}', req_st_chef_d: 'Ogni cuoco ha bisogno del suo fornello.',
    req_st_waiter: 'Camerieri con tavoli: {n}/{m}', req_st_waiter_d: 'I tavoli sono divisi tra i camerieri: aggiungi tavoli.',
    req_st_cleaner: 'Addetti alle pulizie con carrello o postazione: {n}/{m}', req_st_cleaner_d: 'Un carrello o una postazione delle pulizie a testa.',
    req_st_manager: 'Manager con leggio dell’accoglienza: {n}/{m}', req_st_manager_d: 'Il leggio vicino all’ingresso è la postazione del manager.',
    req_st_cashier: 'Cassieri con una cassa: {n}/{m}', req_st_cashier_d: 'Un registratore di cassa a testa.',
});
Object.assign(STRINGS.en, {
    hol_started: 'Holiday in town: {h}! The neighbourhood is decorated.', hol_christmas: 'Christmas', hol_halloween: 'Halloween', hol_easter: 'Easter',
    hol_carnival: 'Carnival', hol_valentine: 'Valentine’s Day', hol_summer: 'the summer festival',
});
Object.assign(STRINGS.it, {
    hol_started: 'Festa in città: {h}! Il quartiere è addobbato.', hol_christmas: 'Natale', hol_halloween: 'Halloween', hol_easter: 'Pasqua',
    hol_carnival: 'Carnevale', hol_valentine: 'San Valentino', hol_summer: 'la festa d’estate',
});
Object.assign(STRINGS.en, {
    cat_storage: 'Storage', store_piece: 'Put away', store_hint: 'Put it in storage without selling it: place it again whenever you like, for free',
    storage_empty: 'Nothing in storage. Select a piece in the room and choose “Put away” to keep it here without selling it.',
    storage_free: 'Free: already yours', storage_sell_hint: 'Sell this piece from storage', storage_sold: 'Sold from storage: +{c} coins', lk_storage_sell: 'Sold from storage',
});
Object.assign(STRINGS.it, {
    cat_storage: 'Archivio', store_piece: 'Archivia', store_hint: 'Mettilo in archivio senza venderlo: lo rimetti quando vuoi, gratis',
    storage_empty: 'L’archivio è vuoto. Seleziona un oggetto nella sala e scegli “Archivia” per tenerlo qui senza venderlo.',
    storage_free: 'Gratis: è già tuo', storage_sell_hint: 'Vendi questo oggetto dall’archivio', storage_sold: 'Venduto dall’archivio: +{c} monete', lk_storage_sell: 'Venduto dall’archivio',
});

// Windows, outdoor paving, wide doors, the later levels (31–40)
Object.assign(STRINGS.en, {
    cat_window: 'Windows', cat_ground: 'Paving', window_wall: 'goes along an outer wall', window_door: 'not in the doorway',
    err_window_wall: 'A window must stand along an outer wall.', err_window_door: 'A window cannot go where the door is.',
    door_wide_wood: 'Wide wooden door (2 tiles)', door_wide_glass: 'Wide glass door (2 tiles)', door_wide_french: 'Wide French door (2 tiles)', door_wide_grand: 'Grand entrance (2 tiles)',
});
Object.assign(STRINGS.it, {
    cat_window: 'Finestre', cat_ground: 'Pavimentazioni', window_wall: 'va lungo un muro esterno', window_door: 'non sulla porta',
    err_window_wall: 'Una finestra va lungo un muro esterno.', err_window_door: 'Una finestra non può stare dove c’è la porta.',
    door_wide_wood: 'Porta larga di legno (2 caselle)', door_wide_glass: 'Porta larga a vetri (2 caselle)', door_wide_french: 'Porta-finestra larga (2 caselle)', door_wide_grand: 'Grande ingresso (2 caselle)',
});
Object.assign(NAMES.en, {
    window_small: 'Small window', window_round: 'Round window', window_wide: 'Wide window', window_tall: 'Tall window', window_large: 'Large window', window_arched: 'Arched window',
    glass_wall_2: 'Glass wall (2)', window_stained: 'Stained glass window', glass_wall_3: 'Glass wall (3)', glass_wall_4: 'Glass wall (4)', window_panorama: 'Panoramic glazing',
    ground_grass: 'Lawn', ground_gravel: 'Gravel', ground_stone: 'Stone slabs', ground_deck: 'Wooden decking', ground_brick: 'Brick paving', ground_terracotta: 'Terracotta tiles',
    ground_flowers: 'Flowery meadow', ground_mosaic: 'Mosaic', ground_marble: 'Marble paving',
    table_glass: 'Glass table', table_banquet: 'Banquet table', table_gold_round: 'Black and gold table', chair_designer: 'Designer chair', chair_royal: 'Royal armchair',
    stove_teppanyaki: 'Teppanyaki grill', stove_master: 'Master range', counter_marble: 'Marble counter', counter_chef_table: 'Chef’s table counter',
    knife_rack: 'Knife rack', herb_wall: 'Herb wall', sous_vide: 'Sous-vide station', blast_chiller: 'Blast chiller', menu_tablet: 'Menu tablet stand', armchair_wing: 'Wing armchair',
    bonsai: 'Bonsai', wine_wall: 'Wine wall', chandelier_crystal: 'Crystal chandelier', sculpture_modern: 'Modern sculpture', aquarium_wall: 'Aquarium wall',
    planter_box_long: 'Long planter', trellis_roses: 'Rose trellis', olive_tree_big: 'Old olive tree', fire_pit: 'Fire pit', outdoor_bar: 'Garden bar', gazebo: 'Gazebo', water_wall: 'Water wall',
    floor_black_marble: 'Black marble', floor_terrazzo: 'Terrazzo', wall_velvet: 'Velvet and gold', wall_wood_panels: 'Wood panelling',
});
Object.assign(NAMES.it, {
    window_small: 'Finestrella', window_round: 'Oblò', window_wide: 'Finestra larga', window_tall: 'Finestra alta', window_large: 'Finestrone', window_arched: 'Finestra ad arco',
    glass_wall_2: 'Vetrata (2)', window_stained: 'Vetrata colorata', glass_wall_3: 'Vetrata (3)', glass_wall_4: 'Vetrata (4)', window_panorama: 'Vetrata panoramica',
    ground_grass: 'Prato', ground_gravel: 'Ghiaia', ground_stone: 'Lastre di pietra', ground_deck: 'Pedana di legno', ground_brick: 'Mattoni', ground_terracotta: 'Cotto',
    ground_flowers: 'Prato fiorito', ground_mosaic: 'Mosaico', ground_marble: 'Marmo',
    table_glass: 'Tavolo di vetro', table_banquet: 'Tavolo da banchetto', table_gold_round: 'Tavolo nero e oro', chair_designer: 'Sedia di design', chair_royal: 'Poltrona reale',
    stove_teppanyaki: 'Piastra teppanyaki', stove_master: 'Cucina da maestro', counter_marble: 'Bancone di marmo', counter_chef_table: 'Bancone dello chef',
    knife_rack: 'Portacoltelli', herb_wall: 'Parete di erbe aromatiche', sous_vide: 'Postazione sottovuoto', blast_chiller: 'Abbattitore', menu_tablet: 'Leggio con tablet', armchair_wing: 'Poltrona bergère',
    bonsai: 'Bonsai', wine_wall: 'Parete dei vini', chandelier_crystal: 'Lampadario di cristallo', sculpture_modern: 'Scultura moderna', aquarium_wall: 'Parete acquario',
    planter_box_long: 'Fioriera lunga', trellis_roses: 'Spalliera di rose', olive_tree_big: 'Ulivo secolare', fire_pit: 'Braciere', outdoor_bar: 'Bar in giardino', gazebo: 'Gazebo', water_wall: 'Parete d’acqua',
    floor_black_marble: 'Marmo nero', floor_terrazzo: 'Graniglia', wall_velvet: 'Velluto e oro', wall_wood_panels: 'Boiserie',
});
Object.assign(STRINGS.en, {stairs_exit: 'the top end needs a free tile on the floor above', err_stairs_exit: 'Stairs need a free tile where they come out on the floor above.'});
Object.assign(STRINGS.it, {stairs_exit: 'in cima serve una casella libera al piano di sopra', err_stairs_exit: 'Le scale hanno bisogno di una casella libera dove arrivano al piano di sopra.'});
Object.assign(NAMES.en, {dish_lift: 'Dish lift'});
Object.assign(NAMES.it, {dish_lift: 'Montavivande'});
