/*
 * ThinkHop — guided, animated "How to play" walkthrough for each of the 28 question categories.
 * Opening the guide pauses the question clock and plays a 3-step visual demonstration.
 */
(function () {
    'use strict';

    const I = window.ThinkHopI18n;
    const M = window.ThinkHopMascots;
    const t = I.t;

    I.extend({
        en: {
            how_to_play: 'How to play',
            help_step: 'Step {n} of {m}',
            help_prev: 'Previous step',
            help_next: 'Next step',
            help_replay: 'Replay animation',
            help_got_it: 'Got it!',
            help_paused: 'Timer paused',

            help_sign_goal: 'Find which arithmetic sign (+, −, ×, ÷, ^) makes the equation true.',
            help_sign_s1: 'Look at the numbers and the final result on the right.',
            help_sign_s2: 'Try the candidate signs in the gap (remember × and ÷ come before + and −).',
            help_sign_s3: '6 × 2 = 12 hits the target: pick ×!',

            help_sequence_goal: 'Discover the rule linking consecutive numbers and find the next one.',
            help_sequence_s1: 'Compare each number with the one right after it.',
            help_sequence_s2: 'Measure the step between neighbours: here every jump adds +3.',
            help_sequence_s3: 'Apply the same step to the last number: 12 + 3 = 15!',

            help_symbols_goal: 'Find the value of each fruit line by line, then solve the last line.',
            help_symbols_s1: 'Start from the line with only one kind of fruit: 🍎 + 🍎 = 10 means 🍎 = 5.',
            help_symbols_s2: 'Substitute 🍎 = 5 into the next line: 5 + 🍌 = 8 gives 🍌 = 3.',
            help_symbols_s3: 'Now you know the value requested on the last line: 🍌 = 3!',

            help_oddone_goal: 'Three numbers share a hidden mathematical property; find the one that does not.',
            help_oddone_s1: 'Look at all four numbers together instead of one by one.',
            help_oddone_s2: '4, 9 and 16 are perfect squares (2², 3², 4²), while 15 is not.',
            help_oddone_s3: 'Pick 15 — the only number that breaks the rule!',

            help_order_goal: 'Put the characters in order using the clues, then answer who is at the requested spot.',
            help_order_s1: 'Read the first clue and place those two characters in order: 🦊 is ahead of 🐢.',
            help_order_s2: 'Add the next clue: 🐰 is ahead of 🦊, completing the line 🐰 > 🦊 > 🐢.',
            help_order_s3: 'Read off the position asked by the question: 🐰 is 1st!',

            help_weird_goal: 'The symbol ⊕ hides a secret rule shown in the examples. Find it and apply it to the last row.',
            help_weird_s1: 'Test how the first two numbers can produce the result: (2 × 3) + 1 = 7.',
            help_weird_s2: 'Verify the exact same rule on the other examples: (4 × 5) + 1 = 21 ✓.',
            help_weird_s3: 'Use that rule on the last line: (3 × 4) + 1 = 13!',

            help_nonsense_goal: 'Even if the words are silly, follow the logical chain strictly without guessing.',
            help_nonsense_s1: 'Treat the strange words as boxes: every “Plip” is inside “Zorg”.',
            help_nonsense_s2: 'Chain the second statement: every “Zorg” is inside “Blue”.',
            help_nonsense_s3: 'So every “Plip” is certainly “Blue”!',

            help_riddle_goal: 'Read every word carefully: riddles play with double meanings and unexpected details.',
            help_riddle_s1: 'Spot the paradoxical clues: “cities without houses, rivers without water”.',
            help_riddle_s2: 'Rule out literal answers that clash with the clues.',
            help_riddle_s3: 'A map has drawn cities and rivers, with no bricks or water!',

            help_pattern_goal: 'Spot how the pictures repeat, alternate, or shift, and pick what comes next.',
            help_pattern_s1: 'Group the pictures to see the repeating unit: 🌞, 🌙, ⭐.',
            help_pattern_s2: 'Match the second group against the first: 🌞, 🌙, …',
            help_pattern_s3: 'The next picture in the cycle is ⭐!',

            help_letters_goal: 'Treat the alphabet like a number line and count how many letters each step skips.',
            help_letters_s1: 'Locate the given letters along the alphabet: A, C, E.',
            help_letters_s2: 'Each jump skips one letter (+2 steps each time: B, D, F are skipped).',
            help_letters_s3: 'Two steps after E lands on G!',

            help_intruder_goal: 'Find what three of the four pictures have in common and pick the intruder.',
            help_intruder_s1: 'Observe all four pictures and look for a shared trait.',
            help_intruder_s2: '🐙, 🐬 and 🐟 all live in the sea, whereas 🦁 lives on land.',
            help_intruder_s3: 'Select 🦁 as the intruder!',

            help_grid_goal: 'Every row and column must contain each symbol once. Find what goes in “?”.',
            help_grid_s1: 'Focus on the yellow “?” cell and check the symbols already in its row and column.',
            help_grid_s2: 'Cross out 🍎 and 🍌 because they already appear in that row or column.',
            help_grid_s3: 'Only 🍇 is missing — place 🍇 in “?”!',

            help_compass_goal: 'Follow the turtle step by step (or count its turns) to see where it stops.',
            help_compass_s1: 'Find the starting cell with 🐢 and read the first arrow: ⬆️.',
            help_compass_s2: 'Move one cell up to B, then read the next arrow: ➡️.',
            help_compass_s3: 'Move one cell right: the turtle stops on C!',

            help_lamps_goal: 'Each button flips the listed lamps (on ↔ off). Track their state to the end.',
            help_lamps_s1: 'Start from the initial lamps (💡 🌑 💡) and apply the first button (1, 2).',
            help_lamps_s2: 'Lamps 1 and 2 flip to 🌑 💡 💡; now apply the second button (2, 3).',
            help_lamps_s3: 'Lamps 2 and 3 flip: the final state is 🌑 🌑 🌑!',

            help_code_goal: 'Compare the example words with their coded versions to crack the transformation rule.',
            help_code_s1: 'Compare the example letter by letter: CAT becomes TAC (written backwards).',
            help_code_s2: 'Apply the exact same letter movement to the target word DOG.',
            help_code_s3: 'Reading DOG backwards gives GOD!',

            help_ifthen_goal: 'Take the absurd premises as 100% true and check whether the conclusion must follow.',
            help_ifthen_s1: 'Read the rule: “If the dragon sneezes, then the moon turns purple.”',
            help_ifthen_s2: 'Check the known fact: the dragon sneezes (so the condition is triggered).',
            help_ifthen_s3: 'Only “Yes” agrees with all premises — if both Yes and No worked, you would pick “Cannot tell”!',

            help_liars_goal: 'Angels (😇) always tell the truth; devils (😈) always lie. Test which combination works.',
            help_liars_s1: 'Try assuming A is 😇: then A’s claim “B is 😈” must be true.',
            help_liars_s2: 'Check B’s claim “We are different”: since 😇 ≠ 😈, B would tell the truth — impossible for a 😈!',
            help_liars_s3: 'Try A = 😈 and B = 😇: A lies and B tells the truth. Everything fits!',

            help_boxes_goal: 'The treasure is in one chest. Test where it can be so the exact number of true labels matches.',
            help_boxes_s1: 'Suppose the treasure is in 🟥: count how many labels would be true (here 2 ≠ 1).',
            help_boxes_s2: 'Now suppose the treasure is in 🟦: check every label again.',
            help_boxes_s3: 'With the treasure in 🟦, exactly 1 label is true — the treasure is in 🟦!',

            help_whoowns_goal: 'Match each person with their unique pet (and house) by ruling out impossibilities.',
            help_whoowns_s1: 'Use the first clue to cross out impossible pairs: Ada has neither 🐶 nor 🐱, so Ada has 🐰.',
            help_whoowns_s2: 'Cross out 🐰 for everyone else; Bruno doesn’t have 🐶, so Bruno has 🐱.',
            help_whoowns_s3: 'Only 🐶 is left for Clara!',

            help_days_goal: 'Anchor the known day on the week cycle, then count forward or backward.',
            help_days_s1: 'Find “today” first: if yesterday was Tuesday, then today is Wednesday.',
            help_days_s2: 'Now count from Wednesday to the target (“the day after tomorrow” = +2 days).',
            help_days_s3: 'Two days after Wednesday lands on Friday!',

            help_trap_goal: 'Beware of the instinctive answer! Visualize the situation carefully before answering.',
            help_trap_s1: 'You overtake the runner in 2nd place: the gut reaction “1st place” is a trap!',
            help_trap_s2: 'Picture the swap: you take the 2nd runner’s spot, while the 1st runner is still ahead of you.',
            help_trap_s3: 'You are now in 2nd place!',

            help_scales_goal: 'Use the balanced scales (or tipping inequalities) to compare weights and find the answer.',
            help_scales_s1: 'From the first scale, note the equivalence: 1 🍍 weighs as much as 2 🍎.',
            help_scales_s2: 'Replace 🍍 with 2 🍎 on the second scale: 🍉 balances 2 🍎 + 1 🍎.',
            help_scales_s3: '1 🍉 weighs 3 🍎!',

            help_cups_goal: 'Follow the swaps or row moves from top to bottom to track where each item ends up.',
            help_cups_s1: 'Start from the top order (🍓 🍋 🫐) and perform the first swap: positions 1 ↔ 3.',
            help_cups_s2: 'The row is now 🫐 🍋 🍓; next perform the second swap: positions 2 ↔ 3.',
            help_cups_s3: 'The final order is 🫐 🍓 🍋!',

            help_locks_goal: 'Use 🎯 (right place) and 🔄 (wrong place) clues to deduce the secret code.',
            help_locks_s1: 'A clue with 0 🎯 and 0 🔄 rules out all its symbols (1, 2, 3 are out).',
            help_locks_s2: 'Compare clues with 🎯 and 🔄 to lock each valid symbol into its exact slot.',
            help_locks_s3: 'Only 4 6 7 matches the exact 🎯 and 🔄 count of every clue!',

            help_suspects_goal: 'Compare each suspect’s items (or statements) against the clues until only one remains.',
            help_suspects_s1: 'Check the first clue (“Did not have 🧤”): cross out 🐻.',
            help_suspects_s2: 'Check the next clue (“Had 🎩”): cross out 🐼.',
            help_suspects_s3: 'Only 🦊 matches every single clue!',

            help_alchemy_goal: 'Follow the cauldron transformation rules step by step (watch which ingredient runs out first!).',
            help_alchemy_s1: 'Each potion 🧪 requires 2 🍄 and 1 💎. Brew the first 🧪 from your stock.',
            help_alchemy_s2: 'Brew a second 🧪: you now have 1 🍄 and 0 💎 left (💎 runs out!).',
            help_alchemy_s3: 'You can brew at most 2 🧪 in total!',

            help_seats_goal: 'Place characters around the table or bench one clue at a time.',
            help_seats_s1: 'Put 🦊 at seat 1; “🐼 sits opposite 🦊” places 🐼 at seat 3.',
            help_seats_s2: '“🐸 sits immediately after 🦊, going clockwise” places 🐸 at seat 2, leaving seat 4 for 🦉.',
            help_seats_s3: 'Every seat is fixed — read off the requested seat!',

            help_mirror_goal: 'Follow how the grid flips (↔️ horizontal, ↕️ vertical) or rotates (↻, ↺, 🔄).',
            help_mirror_s1: 'Look at the starting 2×2 grid and the requested move: ↔️ (horizontal mirror).',
            help_mirror_s2: 'Flipping horizontally swaps the left and right columns (🍎 ↔ 🍌 and 🍇 ↔ 🍒).',
            help_mirror_s3: 'Pick the grid that matches the flipped positions!',

            help_gears_goal: 'Determine the rotation direction or speed of gears linked in a train.',
            help_gears_s1: 'Gear A turns clockwise (↻). When two gears touch, their directions invert.',
            help_gears_s2: 'Gear B meshes with A and turns counter-clockwise (↺).',
            help_gears_s3: 'Gear C meshes with B, reversing back to clockwise (↻)!',

            help_lasers_goal: 'Trace the laser beam as it reflects 90° off angled mirrors to hit a target gem.',
            help_lasers_s1: 'The emitter fires a laser beam horizontally to the right.',
            help_lasers_s2: 'The beam hits mirror \\ at the top corner and deflects 90° downwards.',
            help_lasers_s3: 'Travelling down, the beam strikes the target diamond 💎!',

            help_cubes_goal: 'Mentally fold the flat 2D net into a 3D cube to find opposite faces.',
            help_cubes_s1: 'Notice the straight strip of 4 squares: faces with 1 square between them become opposites.',
            help_cubes_s2: 'Here 🍌 and 🍓 have 🍇 between them, so they fold to face each other.',
            help_cubes_s3: 'The opposite face of 🍌 is 🍓!',

            help_pipes_goal: 'Follow the water through the pipes and find the first tank to fill to the brim.',
            help_pipes_s1: 'Water pours into A and its level rises. Look at A’s pipes: how high is each one, and is it open or closed (🚫)?',
            help_pipes_s2: 'The level reaches the lowest open pipe first: from there the water flows into the next tank, and A stops rising.',
            help_pipes_s3: 'Follow the water tank by tank: the first one with no open way out fills to the brim. Here it is B!',

            help_mobiles_goal: 'Find the missing counterweight that puts every hanging beam into perfect equilibrium.',
            help_mobiles_s1: 'Remember the lever principle: Torque = Weight × Distance from the center pivot.',
            help_mobiles_s2: 'Left arm has weight 6 at distance 1 (torque = 6). Right arm has distance 2.',
            help_mobiles_s3: 'To balance torque 6 at distance 2, we need 6 ÷ 2 = 3: pick 3!',

            help_origami_goal: 'Mentally unfold the paper step by step to find where the punched holes will appear.',
            help_origami_s1: 'Follow the fold lines and arrows in reverse, like opening the sheet back up.',
            help_origami_s2: 'Every fold line acts like a mirror: a hole reflects symmetrically across the fold.',
            help_origami_s3: 'Unfolding both folds creates a 4-way symmetrical pattern: pick matching card!',
        },
        it: {
            how_to_play: 'Come si gioca',
            help_step: 'Passo {n} di {m}',
            help_prev: 'Passo precedente',
            help_next: 'Passo successivo',
            help_replay: 'Rivedi animazione',
            help_got_it: 'Ho capito!',
            help_paused: 'Tempo in pausa',

            help_sign_goal: 'Trova quale segno matematico (+, −, ×, ÷, ^) rende vera l’uguaglianza.',
            help_sign_s1: 'Osserva i numeri e il risultato finale a destra dell’uguale.',
            help_sign_s2: 'Prova i segni nella casella vuota (ricorda che × e ÷ si calcolano prima di + e −).',
            help_sign_s3: '6 × 2 = 12 dà il risultato esatto: scegli ×!',

            help_sequence_goal: 'Scopri la regola che lega i numeri consecutivi e trova quello successivo.',
            help_sequence_s1: 'Confronta ogni numero con quello che viene subito dopo.',
            help_sequence_s2: 'Misura il salto tra un numero e l’altro: qui ogni passo aggiunge +3.',
            help_sequence_s3: 'Applica lo stesso salto all’ultimo numero: 12 + 3 = 15!',

            help_symbols_goal: 'Trova il valore di ogni frutto riga per riga, poi risolvi l’ultima riga.',
            help_symbols_s1: 'Parti dalla riga con un solo tipo di frutto: 🍎 + 🍎 = 10 significa 🍎 = 5.',
            help_symbols_s2: 'Sostituisci 🍎 = 5 nella seconda riga: 5 + 🍌 = 8 dà 🍌 = 3.',
            help_symbols_s3: 'Ora conosci il valore cercato nell’ultima riga: 🍌 = 3!',

            help_oddone_goal: 'Tre numeri condividono una proprietà matematica nascosta; trova l’unico intruso.',
            help_oddone_s1: 'Guarda tutti e quattro i numeri insieme cercando cosa accomuna tre di loro.',
            help_oddone_s2: '4, 9 e 16 sono quadrati perfetti (2², 3², 4²), mentre 15 non lo è.',
            help_oddone_s3: 'Scegli 15, l’unico numero fuori regola!',

            help_order_goal: 'Metti in fila i personaggi usando gli indizi, poi indica chi si trova nel posto richiesto.',
            help_order_s1: 'Dal primo indizio posiziona i primi due: 🦊 è davanti a 🐢.',
            help_order_s2: 'Aggiungi il secondo indizio: 🐰 è davanti a 🦊, quindi la fila è 🐰 > 🦊 > 🐢.',
            help_order_s3: 'Leggi chi occupa il posto richiesto: al 1° posto c’è 🐰!',

            help_weird_goal: 'Il simbolo ⊕ nasconde una regola segreta mostrata negli esempi. Scoprila e applicala all’ultima riga.',
            help_weird_s1: 'Cerca come i primi due numeri danno il risultato: (2 × 3) + 1 = 7.',
            help_weird_s2: 'Verifica che la stessa regola funzioni sul secondo esempio: (4 × 5) + 1 = 21 ✓.',
            help_weird_s3: 'Applica la regola alla domanda: (3 × 4) + 1 = 13!',

            help_nonsense_goal: 'Anche se le parole sono inventate, segui i legami logici senza farti distrarre.',
            help_nonsense_s1: 'Immagina le parole come scatole: ogni «Plip» sta dentro «Zorg».',
            help_nonsense_s2: 'Collega la seconda frase: ogni «Zorg» sta dentro «Blu».',
            help_nonsense_s3: 'Quindi ogni «Plip» è sicuramente «Blu»!',

            help_riddle_goal: 'Leggi con attenzione ogni parola: gli indovinelli giocano sui doppi sensi e sui dettagli.',
            help_riddle_s1: 'Individua gli indizi chiave: «città senza case, fiumi senza acqua».',
            help_riddle_s2: 'Scarta le risposte letterali che contraddicono gli indizi.',
            help_riddle_s3: 'Una mappa ha città e fiumi disegnati, ma senza mattoni né acqua!',

            help_pattern_goal: 'Osserva come si ripetono, si alternano o ruotano le figure e scegli quella successiva.',
            help_pattern_s1: 'Raggruppa le figure per individuare il blocco che si ripete: 🌞, 🌙, ⭐.',
            help_pattern_s2: 'Confronta il secondo blocco con il primo: 🌞, 🌙, …',
            help_pattern_s3: 'La figura che completa il ciclo è ⭐!',

            help_letters_goal: 'Segui l’ordine dell’alfabeto e conta di quante posizioni avanza o arretra ogni lettera.',
            help_letters_s1: 'Individua le lettere date lungo l’alfabeto: A, C, E.',
            help_letters_s2: 'Ogni passo salta una lettera (+2 posizioni alla volta: B, D, F vengono saltate).',
            help_letters_s3: 'Due passi dopo la E si arriva alla G!',

            help_intruder_goal: 'Scopri cosa hanno in comune tre delle quattro figure e tocca l’intruso.',
            help_intruder_s1: 'Osserva le quattro figure e cerca la caratteristica condivisa da tre di esse.',
            help_intruder_s2: '🐙, 🐬 e 🐟 vivono tutti nell’acqua, mentre 🦁 vive sulla terraferma.',
            help_intruder_s3: 'Scegli 🦁 come intruso!',

            help_grid_goal: 'Ogni riga e colonna deve contenere tutti i simboli una sola volta. Trova cosa va in «?».',
            help_grid_s1: 'Guarda la casella gialla «?» e controlla i simboli già presenti nella sua riga e colonna.',
            help_grid_s2: 'Escludi 🍎 e 🍌 perché compaiono già nella stessa riga o colonna.',
            help_grid_s3: 'Resta solo 🍇: metti 🍇 al posto di «?»!',

            help_compass_goal: 'Segui la tartaruga passo dopo passo (o conta i suoi giri) per vedere dove si ferma.',
            help_compass_s1: 'Trova la casella di partenza con 🐢 e leggi la prima freccia: ⬆️.',
            help_compass_s2: 'Sali di una casella fino a B, poi leggi la seconda freccia: ➡️.',
            help_compass_s3: 'Spostati a destra di una casella: la tartaruga si ferma su C!',

            help_lamps_goal: 'Ogni bottone inverte le lampadine indicate (accesa ↔ spenta). Segui i cambi fino alla fine.',
            help_lamps_s1: 'Parti dallo stato iniziale (💡 🌑 💡) e premi il primo bottone (1, 2).',
            help_lamps_s2: 'Le lampadine 1 e 2 cambiano in 🌑 💡 💡; ora premi il secondo bottone (2, 3).',
            help_lamps_s3: 'Le lampadine 2 e 3 si spengono: il risultato finale è 🌑 🌑 🌑!',

            help_code_goal: 'Confronta le parole di esempio con il loro codice per scoprire la regola di trasformazione.',
            help_code_s1: 'Confronta esempio e codice lettera per lettera: CAT diventa TAC (scritto al contrario).',
            help_code_s2: 'Applica lo stesso identico spostamento di lettere alla parola richiesta DOG.',
            help_code_s3: 'Leggendo DOG al contrario ottieni GOD!',

            help_ifthen_goal: 'Prendi per vere le premesse assurde e stabilisci se la conclusione è certa oppure no.',
            help_ifthen_s1: 'Leggi la regola: «Se il drago starnutisce, allora la luna diventa viola».',
            help_ifthen_s2: 'Controlla il fatto noto: il drago starnutisce (quindi la regola scatta).',
            help_ifthen_s3: 'Solo «Sì» rispetta tutte le frasi — se sia Sì sia No fossero possibili, sceglieresti «Non si sa»!',

            help_liars_goal: 'Gli angeli (😇) dicono sempre il vero, i diavoli (😈) mentono sempre. Trova l’unica combinazione coerente.',
            help_liars_s1: 'Prova a supporre A = 😇: allora la sua frase «B è 😈» deve essere vera.',
            help_liars_s2: 'Controlla B («Siamo diversi»): poiché 😇 ≠ 😈, B direbbe il vero — impossibile per un 😈!',
            help_liars_s3: 'Con A = 😈 e B = 😇 invece A mente e B dice il vero: tutto torna!',

            help_boxes_goal: 'Il tesoro è in un solo forziere. Prova ciascun forziere e conta quanti cartelli risultano veri.',
            help_boxes_s1: 'Se il tesoro fosse in 🟥, conta quanti cartelli sarebbero veri (qui 2 ≠ 1).',
            help_boxes_s2: 'Ora prova a mettere il tesoro in 🟦 e ricontrolla tutti i cartelli.',
            help_boxes_s3: 'Con il tesoro in 🟦 c’è esattamente 1 cartello vero: il tesoro è in 🟦!',

            help_whoowns_goal: 'Abbina a ciascuno il proprio animale (e la casa) cancellando via via gli abbinamenti impossibili.',
            help_whoowns_s1: 'Dal primo indizio escludi le coppie impossibili: Ada non ha 🐶 né 🐱, quindi Ada ha 🐰.',
            help_whoowns_s2: 'Segna che 🐰 è già preso; Bruno non ha 🐶, quindi a Bruno resta 🐱.',
            help_whoowns_s3: 'A Clara resta l’unico animale libero: 🐶!',

            help_days_goal: 'Fissa sul calendario il giorno che conosci, poi conta i passi in avanti o indietro.',
            help_days_s1: 'Trova prima «oggi»: se ieri era martedì, allora oggi è mercoledì.',
            help_days_s2: 'Ora conta da mercoledì fino al giorno richiesto («dopodomani» = +2 giorni).',
            help_days_s3: 'Due giorni dopo mercoledì è venerdì!',

            help_trap_goal: 'Attenzione alla prima risposta istintiva! Visualizza bene la scena prima di rispondere.',
            help_trap_s1: 'Sorpassi il 2° in gara: la risposta istintiva «1° posto» è una trappola!',
            help_trap_s2: 'Visualizza il sorpasso: prendi il posto del 2°, mentre il 1° è ancora davanti a te.',
            help_trap_s3: 'Ti trovi al 2° posto!',

            help_scales_goal: 'Usa i piatti in equilibrio (o le disuguaglianze) per confrontare i pesi e trovare la risposta.',
            help_scales_s1: 'Dalla prima bilancia ricavi che 1 🍍 pesa quanto 2 🍎.',
            help_scales_s2: 'Sostituisci 🍍 con 2 🍎 sulla seconda bilancia: 🍉 equilibra 2 🍎 + 1 🍎.',
            help_scales_s3: 'Quindi 1 🍉 pesa 3 🍎!',

            help_cups_goal: 'Segui gli scambi dall’alto verso il basso per capire dove finisce ogni elemento.',
            help_cups_s1: 'Parti dalla fila iniziale (🍓 🍋 🫐) ed esegui il primo scambio: posizioni 1 ↔ 3.',
            help_cups_s2: 'La fila diventa 🫐 🍋 🍓; ora esegui il secondo scambio: posizioni 2 ↔ 3.',
            help_cups_s3: 'L’ordine finale è 🫐 🍓 🍋!',

            help_locks_goal: 'Usa gli indizi 🎯 (posto giusto) e 🔄 (posto sbagliato) per dedurre il codice segreto.',
            help_locks_s1: 'Un tentativo con 0 🎯 e 0 🔄 esclude tutti i suoi simboli (1, 2 e 3 non ci sono).',
            help_locks_s2: 'Confronta gli altri indizi per bloccare ogni simbolo nella posizione esatta.',
            help_locks_s3: 'Solo 4 6 7 rispetta il numero esatto di 🎯 e 🔄 di ogni riga!',

            help_suspects_goal: 'Confronta gli oggetti (o le dichiarazioni) di ogni sospettato con gli indizi finché ne resta uno solo.',
            help_suspects_s1: 'Verifica il primo indizio («Non aveva 🧤»): escludi 🐻.',
            help_suspects_s2: 'Verifica il secondo indizio («Aveva 🎩»): escludi 🐼.',
            help_suspects_s3: 'Solo 🦊 rispetta tutti gli indizi!',

            help_alchemy_goal: 'Segui le regole del calderone passo dopo passo (attento a quale ingrediente finisce per primo!).',
            help_alchemy_s1: 'Ogni pozione 🧪 richiede 2 🍄 e 1 💎. Prepara la prima 🧪 usando le scorte.',
            help_alchemy_s2: 'Prepara la seconda 🧪: restano 1 🍄 e 0 💎 (i 💎 sono finiti!).',
            help_alchemy_s3: 'Puoi preparare al massimo 2 🧪 in totale!',

            help_seats_goal: 'Sistema i personaggi attorno al tavolo o sulla panchina seguendo un indizio alla volta.',
            help_seats_s1: 'Metti 🦊 al posto 1; «🐼 è di fronte a 🦊» colloca 🐼 al posto 3.',
            help_seats_s2: '«🐸 siede subito dopo 🦊 in senso orario» colloca 🐸 al posto 2, lasciando il posto 4 a 🦉.',
            help_seats_s3: 'Tutti i posti sono assegnati: leggi chi siede nel posto richiesto!',

            help_mirror_goal: 'Segui come si ribalta (↔️ orizzontale, ↕️ verticale) o ruota (↻, ↺, 🔄) la griglia.',
            help_mirror_s1: 'Osserva la griglia 2×2 di partenza e la mossa indicata: ↔️ (specchio orizzontale).',
            help_mirror_s2: 'Ribaltando in orizzontale, la colonna sinistra e quella destra si scambiano (🍎 ↔ 🍌 e 🍇 ↔ 🍒).',
            help_mirror_s3: 'Scegli la griglia con le posizioni specchiate!',

            help_gears_goal: 'Determina il verso di rotazione o il numero di giri degli ingranaggi collegati.',
            help_gears_s1: 'L’ingranaggio A gira in senso orario (↻). Due ruote a contatto invertono sempre il verso.',
            help_gears_s2: 'La ruota B tocca A e gira in senso antiorario (↺).',
            help_gears_s3: 'La ruota C tocca B e torna a girare in senso orario (↻)!',

            help_lasers_goal: 'Segui il raggio laser che rimbalza a 90° sugli specchi fino a colpire il bersaglio.',
            help_lasers_s1: 'Il laser spara verso destra in linea retta.',
            help_lasers_s2: 'Il fascio incontra lo specchio \\ e viene deviato verso il basso di 90°.',
            help_lasers_s3: 'Proseguendo verso il basso, il raggio colpisce il diamante 💎!',

            help_cubes_goal: 'Ripiega mentalmente lo sviluppo piatto per ricostruire il cubo 3D e trovare le facce opposte.',
            help_cubes_s1: 'Osserva la fila: due facce separate da una casella intermedia finiscono sempre opposte.',
            help_cubes_s2: 'Qui 🍌 e 🍓 sono separati da 🍇, quindi una volta piegati si guardano in faccia.',
            help_cubes_s3: 'La faccia opposta a 🍌 è 🍓!',

            help_pipes_goal: 'Segui l’acqua lungo i tubi e trova il primo serbatoio che si riempie fino all’orlo.',
            help_pipes_s1: 'L’acqua entra in A e il livello sale. Guarda i tubi di A: a che altezza sono, e sono aperti o chiusi (🚫)?',
            help_pipes_s2: 'Il livello arriva prima al tubo aperto più in basso: da lì l’acqua passa nel serbatoio successivo e A smette di salire.',
            help_pipes_s3: 'Segui l’acqua serbatoio dopo serbatoio: il primo senza uscite aperte si riempie fino all’orlo. Qui è B!',

            help_mobiles_goal: 'Trova il contrappeso mancante che mette in perfetto equilibrio ciascun braccio della scultura.',
            help_mobiles_s1: 'Ricorda il principio della leva: Momento = Peso × Distanza dal perno centrale.',
            help_mobiles_s2: 'Il braccio sinistro ha peso 6 a distanza 1 (momento = 6). Il braccio destro ha distanza 2.',
            help_mobiles_s3: 'Per bilanciare un momento di 6 a distanza 2 serve 6 ÷ 2 = 3: scegli 3!',

            help_origami_goal: 'Riapri mentalmente il foglio a ritroso per scoprire dove si posizionano i fori.',
            help_origami_s1: 'Ripercorri le pieghe e le frecce all’indietro, come se stessi spiegando il foglio.',
            help_origami_s2: 'Ogni linea di piega agisce come uno specchio: il foro si riflette simmetricamente.',
            help_origami_s3: 'Riaprendo entrambe le pieghe ottieni una simmetria quadrupla: scegli la carta corretta!',
        },
        es: {
            how_to_play: 'Cómo se juega',
            help_step: 'Paso {n} de {m}',
            help_prev: 'Paso anterior',
            help_next: 'Paso siguiente',
            help_replay: 'Repetir animación',
            help_got_it: '¡Entendido!',
            help_paused: 'Tiempo en pausa',

            help_sign_goal: 'Encuentra qué signo matemático (+, −, ×, ÷, ^) hace verdadera la igualdad.',
            help_sign_s1: 'Mira los números y el resultado final a la derecha.',
            help_sign_s2: 'Prueba los signos en el hueco (recuerda que × y ÷ van antes que + y −).',
            help_sign_s3: '6 × 2 = 12 da el resultado exacto: ¡elige ×!',

            help_sequence_goal: 'Descubre la regla que une los números consecutivos y halla el siguiente.',
            help_sequence_s1: 'Compara cada número con el que le sigue.',
            help_sequence_s2: 'Mide el salto entre vecinos: aquí cada paso suma +3.',
            help_sequence_s3: 'Aplica el mismo salto al último número: ¡12 + 3 = 15!',

            help_symbols_goal: 'Halla el valor de cada fruta línea por línea y resuelve la última.',
            help_symbols_s1: 'Empieza por la línea con una sola fruta: 🍎 + 🍎 = 10 significa 🍎 = 5.',
            help_symbols_s2: 'Sustituye 🍎 = 5 en la segunda línea: 5 + 🍌 = 8 da 🍌 = 3.',
            help_symbols_s3: 'Ya tienes el valor pedido en la última línea: ¡🍌 = 3!',

            help_oddone_goal: 'Tres números comparten una propiedad matemática oculta; encuentra el intruso.',
            help_oddone_s1: 'Mira los cuatro números juntos buscando qué tienen en común tres de ellos.',
            help_oddone_s2: '4, 9 y 16 son cuadrados perfectos (2², 3², 4²), mientras que 15 no lo es.',
            help_oddone_s3: '¡Elige 15, el único que rompe la regla!',

            help_order_goal: 'Ordena a los personajes con las pistas y responde quién está en el lugar pedido.',
            help_order_s1: 'Con la primera pista coloca a los dos primeros: 🦊 va delante de 🐢.',
            help_order_s2: 'Añade la segunda pista: 🐰 va delante de 🦊, formando 🐰 > 🦊 > 🐢.',
            help_order_s3: 'Mira el puesto que pide la pregunta: ¡🐰 va 1.º!',

            help_weird_goal: 'El símbolo ⊕ esconde una regla secreta en los ejemplos. Descúbrela y aplícala al final.',
            help_weird_s1: 'Busca cómo los dos números dan el resultado: (2 × 3) + 1 = 7.',
            help_weird_s2: 'Comprueba la misma regla en el segundo ejemplo: (4 × 5) + 1 = 21 ✓.',
            help_weird_s3: 'Aplica la regla a la pregunta: ¡(3 × 4) + 1 = 13!',

            help_nonsense_goal: 'Aunque las palabras sean inventadas, sigue la cadena lógica sin adivinar.',
            help_nonsense_s1: 'Imagina las palabras como cajas: todo «Plip» está dentro de «Zorg».',
            help_nonsense_s2: 'Une la segunda frase: todo «Zorg» está dentro de «Azul».',
            help_nonsense_s3: '¡Por tanto, todo «Plip» es seguro «Azul»!',

            help_riddle_goal: 'Lee despacio cada palabra: los acertijos juegan con dobles sentidos y detalles.',
            help_riddle_s1: 'Fíjate en las pistas clave: «ciudades sin casas, ríos sin agua».',
            help_riddle_s2: 'Descarta las respuestas literales que contradicen las pistas.',
            help_riddle_s3: '¡Un mapa tiene ciudades y ríos dibujados, sin ladrillos ni agua!',

            help_pattern_goal: 'Observa cómo se repiten, alternan o giran las figuras y elige la siguiente.',
            help_pattern_s1: 'Agrupa las figuras para ver el bloque que se repite: 🌞, 🌙, ⭐.',
            help_pattern_s2: 'Compara el segundo grupo con el primero: 🌞, 🌙, …',
            help_pattern_s3: '¡La figura que completa el ciclo es ⭐!',

            help_letters_goal: 'Sigue el alfabeto y cuenta cuántas letras salta cada paso.',
            help_letters_s1: 'Ubica las letras dadas en el alfabeto: A, C, E.',
            help_letters_s2: 'Cada salto avanza +2 letras (se saltan B, D y F).',
            help_letters_s3: '¡Dos pasos después de la E está la G!',

            help_intruder_goal: 'Descubre qué tienen en común tres de los cuatro dibujos y elige el intruso.',
            help_intruder_s1: 'Mira los cuatro dibujos y busca el rasgo común a tres de ellos.',
            help_intruder_s2: '🐙, 🐬 y 🐟 viven en el agua, mientras que 🦁 vive en tierra.',
            help_intruder_s3: '¡Elige 🦁 como intruso!',

            help_grid_goal: 'Cada fila y columna debe tener todos los símbolos una sola vez. Halla qué va en «?».',
            help_grid_s1: 'Fíjate en la casilla amarilla «?» y mira qué símbolos ya están en su fila y columna.',
            help_grid_s2: 'Tacha 🍎 y 🍌 porque ya aparecen en esa fila o columna.',
            help_grid_s3: 'Solo falta 🍇: ¡coloca 🍇 en «?»!',

            help_compass_goal: 'Sigue a la tortuga paso a paso (o cuenta sus giros) para ver dónde se detiene.',
            help_compass_s1: 'Busca la casilla inicial con 🐢 y lee la primera flecha: ⬆️.',
            help_compass_s2: 'Sube una casilla hasta B y lee la siguiente flecha: ➡️.',
            help_compass_s3: 'Muévete una casilla a la derecha: ¡la tortuga se para en C!',

            help_lamps_goal: 'Cada botón cambia las bombillas indicadas (encendida ↔ apagada). Síguelas hasta el final.',
            help_lamps_s1: 'Parte del estado inicial (💡 🌑 💡) y pulsa el primer botón (1, 2).',
            help_lamps_s2: 'Las bombillas 1 y 2 cambian a 🌑 💡 💡; ahora pulsa el segundo botón (2, 3).',
            help_lamps_s3: 'Las bombillas 2 y 3 cambian: ¡el estado final es 🌑 🌑 🌑!',

            help_code_goal: 'Compara las palabras de ejemplo con su código para descubrir la regla.',
            help_code_s1: 'Compara letra por letra: CAT pasa a ser TAC (escrito al revés).',
            help_code_s2: 'Aplica el mismo cambio de letras a la palabra DOG.',
            help_code_s3: '¡Al leer DOG al revés queda GOD!',

            help_ifthen_goal: 'Toma las premisas absurdas como verdaderas y decide si la conclusión se sigue con certeza.',
            help_ifthen_s1: 'Lee la regla: «Si el dragón estornuda, entonces la luna se vuelve morada».',
            help_ifthen_s2: 'Mira el hecho conocido: el dragón estornuda (la condición se cumple).',
            help_ifthen_s3: '¡Solo «Sí» encaja con todo (si Sí y No fueran posibles, elegirías «No se sabe»)!',

            help_liars_goal: 'Los ángeles (😇) siempre dicen la verdad; los diablos (😈) siempre mienten. Prueba qué combinación cuadra.',
            help_liars_s1: 'Supón que A = 😇: entonces su frase «B es 😈» tendría que ser cierta.',
            help_liars_s2: 'Mira lo que dice B («Somos diferentes»): como 😇 ≠ 😈, B diría la verdad — ¡imposible para un 😈!',
            help_liars_s3: 'Con A = 😈 y B = 😇, A miente y B dice la verdad: ¡todo cuadra!',

            help_boxes_goal: 'El tesoro está en un cofre. Prueba cada cofre y cuenta cuántos carteles resultan verdaderos.',
            help_boxes_s1: 'Si el tesoro estuviera en 🟥, cuenta cuántos carteles serían verdaderos (aquí 2 ≠ 1).',
            help_boxes_s2: 'Ahora prueba con el tesoro en 🟦 y vuelve a comprobar los carteles.',
            help_boxes_s3: 'Con el tesoro en 🟦 hay exactamente 1 cartel verdadero: ¡está en 🟦!',

            help_whoowns_goal: 'Empareja a cada persona con su mascota (y casa) tachando lo imposible.',
            help_whoowns_s1: 'Con la primera pista descarta parejas: Ada no tiene 🐶 ni 🐱, así que Ada tiene 🐰.',
            help_whoowns_s2: 'Tacha 🐰 para los demás; Bruno no tiene 🐶, así que Bruno tiene 🐱.',
            help_whoowns_s3: '¡A Clara le queda 🐶!',

            help_days_goal: 'Fija el día conocido en la semana y luego cuenta hacia delante o hacia atrás.',
            help_days_s1: 'Halla primero «hoy»: si ayer fue martes, hoy es miércoles.',
            help_days_s2: 'Cuenta desde el miércoles hasta el día pedido («pasado mañana» = +2 días).',
            help_days_s3: '¡Dos días después del miércoles es viernes!',

            help_trap_goal: '¡Cuidado con la respuesta impulsiva! Imagina bien la escena antes de contestar.',
            help_trap_s1: 'Adelantas al 2.º en una carrera: la respuesta rápida «1.º» es una trampa.',
            help_trap_s2: 'Visualiza el adelantamiento: ocupas el lugar del 2.º, y el 1.º sigue delante de ti.',
            help_trap_s3: '¡Ahora vas en 2.º lugar!',

            help_scales_goal: 'Usa las balanzas en equilibrio (o inclinadas) para comparar pesos y hallar la respuesta.',
            help_scales_s1: 'De la primera balanza sacas que 1 🍍 pesa lo mismo que 2 🍎.',
            help_scales_s2: 'Sustituye 🍍 por 2 🍎 en la segunda balanza: 🍉 equilibra 2 🍎 + 1 🍎.',
            help_scales_s3: '¡1 🍉 pesa 3 🍎!',

            help_cups_goal: 'Sigue los intercambios de arriba abajo para ver dónde termina cada elemento.',
            help_cups_s1: 'Empieza por la fila inicial (🍓 🍋 🫐) y haz el primer cambio: posiciones 1 ↔ 3.',
            help_cups_s2: 'La fila queda 🫐 🍋 🍓; ahora haz el segundo cambio: posiciones 2 ↔ 3.',
            help_cups_s3: '¡El orden final es 🫐 🍓 🍋!',

            help_locks_goal: 'Usa las pistas 🎯 (sitio correcto) y 🔄 (sitio incorrecto) para deducir el código.',
            help_locks_s1: 'Una pista con 0 🎯 y 0 🔄 descarta todos sus símbolos (1, 2 y 3 quedan fuera).',
            help_locks_s2: 'Compara las demás pistas para fijar cada símbolo en su posición exacta.',
            help_locks_s3: '¡Solo 4 6 7 cumple todos los 🎯 y 🔄!',

            help_suspects_goal: 'Compara los objetos (o declaraciones) de cada sospechoso con las pistas hasta que solo quede uno.',
            help_suspects_s1: 'Comprueba la primera pista («No llevaba 🧤»): descarta a 🐻.',
            help_suspects_s2: 'Comprueba la segunda pista («Llevaba 🎩»): descarta a 🐼.',
            help_suspects_s3: '¡Solo 🦊 cumple todas las pistas!',

            help_alchemy_goal: 'Sigue las reglas del caldero paso a paso (¡fíjate en qué ingrediente se agota primero!).',
            help_alchemy_s1: 'Cada poción 🧪 pide 2 🍄 y 1 💎. Prepara la primera 🧪 con tus ingredientes.',
            help_alchemy_s2: 'Prepara la segunda 🧪: te quedan 1 🍄 y 0 💎 (¡se acabaron los 💎!).',
            help_alchemy_s3: '¡Puedes preparar como máximo 2 🧪!',

            help_seats_goal: 'Coloca a los personajes alrededor de la mesa o en el banco pista por pista.',
            help_seats_s1: 'Pon a 🦊 en el asiento 1; «🐼 está frente a 🦊» coloca a 🐼 en el asiento 3.',
            help_seats_s2: '«🐸 se sienta justo después de 🦊 en sentido horario» coloca a 🐸 en el 2, dejando el 4 para 🦉.',
            help_seats_s3: 'Con todos sentados, ¡mira quién ocupa el asiento pedido!',

            help_mirror_goal: 'Sigue cómo se voltea (↔️ horizontal, ↕️ vertical) o gira (↻, ↺, 🔄) la cuadrícula.',
            help_mirror_s1: 'Mira la cuadrícula 2×2 inicial y el movimiento pedido: ↔️ (espejo horizontal).',
            help_mirror_s2: 'Al voltear en horizontal, las columnas izquierda y derecha se intercambian (🍎 ↔ 🍌 y 🍇 ↔ 🍒).',
            help_mirror_s3: '¡Elige la cuadrícula con las posiciones reflejadas!',

            help_gears_goal: 'Determina el sentido de giro o las vueltas de los engranajes conectados.',
            help_gears_s1: 'El engranaje A gira en sentido horario (↻). Dos ruedas en contacto invierten el sentido.',
            help_gears_s2: 'La rueda B engrana con A y gira en sentido antihorario (↺).',
            help_gears_s3: 'La rueda C engrana con B y vuelve a girar en sentido horario (↻)!',

            help_lasers_goal: 'Sigue el rayo láser al rebotar 90° en los espejos hasta alcanzar el objetivo.',
            help_lasers_s1: 'El emisor dispara un rayo horizontalmente hacia la derecha.',
            help_lasers_s2: 'El rayo choca con el espejo \\ y se desvía 90° hacia abajo.',
            help_lasers_s3: 'Siguiendo hacia abajo, ¡el rayo impacta en el diamante 💎!',

            help_cubes_goal: 'Pliega mentalmente el desarrollo plano para formar el cubo y hallar caras opuestas.',
            help_cubes_s1: 'En una tira recta, dos caras separadas por una casilla intermedia quedan opuestas.',
            help_cubes_s2: 'Aquí 🍌 y 🍓 tienen a 🍇 en medio, así que al plegarse quedan enfrentadas.',
            help_cubes_s3: '¡La cara opuesta a 🍌 es 🍓!',

            help_pipes_goal: 'Sigue el agua por los tubos y encuentra el primer tanque que se llena hasta el borde.',
            help_pipes_s1: 'El agua entra en A y el nivel sube. Mira los tubos de A: ¿a qué altura está cada uno, y está abierto o cerrado (🚫)?',
            help_pipes_s2: 'El nivel llega antes al tubo abierto más bajo: desde ahí el agua pasa al siguiente tanque y A deja de subir.',
            help_pipes_s3: 'Sigue el agua tanque a tanque: el primero sin salida abierta se llena hasta el borde. ¡Aquí es B!',

            help_mobiles_goal: 'Halla el contrapeso que equilibra perfectamente cada brazo de la escultura móvil.',
            help_mobiles_s1: 'Recuerda la ley de la palanca: Momento = Peso × Distancia al eje central.',
            help_mobiles_s2: 'El brazo izquierdo tiene peso 6 a distancia 1 (momento = 6). El derecho tiene distancia 2.',
            help_mobiles_s3: 'Para equilibrar un momento de 6 a distancia 2 hace falta 6 ÷ 2 = 3: ¡elige 3!',

            help_origami_goal: 'Desdobla mentalmente el papel paso a paso para ver dónde quedarán los agujeros.',
            help_origami_s1: 'Sigue los pliegues en orden inverso, como si abrieras de nuevo la hoja.',
            help_origami_s2: 'Cada pliegue actúa como un espejo: el orificio se refleja simétricamente.',
            help_origami_s3: 'Al desdoblar ambos pliegues se obtiene simetría cuádruple: ¡elige la carta correcta!',
        },
        fr: {
            how_to_play: 'Comment jouer',
            help_step: 'Étape {n} sur {m}',
            help_prev: 'Étape précédente',
            help_next: 'Étape suivante',
            help_replay: 'Rejouer l’animation',
            help_got_it: 'Compris !',
            help_paused: 'Temps en pause',

            help_sign_goal: 'Trouve quel signe mathématique (+, −, ×, ÷, ^) rend l’égalité vraie.',
            help_sign_s1: 'Observe les nombres et le résultat final à droite.',
            help_sign_s2: 'Essaie les signes dans la case vide (rappelle-toi que × et ÷ passent avant + et −).',
            help_sign_s3: '6 × 2 = 12 donne le bon résultat : choisis × !',

            help_sequence_goal: 'Découvre la règle qui relie les nombres consécutifs et trouve le suivant.',
            help_sequence_s1: 'Compare chaque nombre avec celui qui le suit.',
            help_sequence_s2: 'Mesure l’écart entre voisins : ici chaque saut ajoute +3.',
            help_sequence_s3: 'Applique le même saut au dernier nombre : 12 + 3 = 15 !',

            help_symbols_goal: 'Trouve la valeur de chaque fruit ligne par ligne, puis résous la dernière.',
            help_symbols_s1: 'Pars de la ligne avec un seul fruit : 🍎 + 🍎 = 10 signifie 🍎 = 5.',
            help_symbols_s2: 'Remplace 🍎 = 5 dans la deuxième ligne : 5 + 🍌 = 8 donne 🍌 = 3.',
            help_symbols_s3: 'Tu connais maintenant la valeur demandée : 🍌 = 3 !',

            help_oddone_goal: 'Trois nombres partagent une propriété mathématique cachée ; trouve l’intrus.',
            help_oddone_s1: 'Regarde les quatre nombres ensemble pour trouver ce qui en relie trois.',
            help_oddone_s2: '4, 9 et 16 sont des carrés parfaits (2², 3², 4²), mais pas 15.',
            help_oddone_s3: 'Choisis 15, le seul nombre qui brise la règle !',

            help_order_goal: 'Place les personnages dans l’ordre grâce aux indices, puis dis qui est à la place demandée.',
            help_order_s1: 'Avec le premier indice, place les deux premiers : 🦊 est devant 🐢.',
            help_order_s2: 'Ajoute le second indice : 🐰 est devant 🦊, ce qui donne 🐰 > 🦊 > 🐢.',
            help_order_s3: 'Lis la position demandée : 🐰 est 1er !',

            help_weird_goal: 'Le symbole ⊕ cache une règle secrète montrée dans les exemples. Trouve-la et applique-la.',
            help_weird_s1: 'Cherche comment les deux nombres donnent le résultat : (2 × 3) + 1 = 7.',
            help_weird_s2: 'Vérifie la même règle sur le deuxième exemple : (4 × 5) + 1 = 21 ✓.',
            help_weird_s3: 'Applique la règle à la question : (3 × 4) + 1 = 13 !',

            help_nonsense_goal: 'Même si les mots sont inventés, suis la chaîne logique sans deviner.',
            help_nonsense_s1: 'Imagine les mots comme des boîtes : chaque « Plip » est dans « Zorg ».',
            help_nonsense_s2: 'Relie la deuxième phrase : chaque « Zorg » est dans « Bleu ».',
            help_nonsense_s3: 'Donc chaque « Plip » est forcément « Bleu » !',

            help_riddle_goal: 'Lis attentivement chaque mot : les énigmes jouent sur les doubles sens et les détails.',
            help_riddle_s1: 'Repère les indices clés : « des villes sans maisons, des rivières sans eau ».',
            help_riddle_s2: 'Écarte les réponses littérales qui contredisent les indices.',
            help_riddle_s3: 'Une carte a des villes et des rivières dessinées, sans briques ni eau !',

            help_pattern_goal: 'Observe comment les images se répètent, alternent ou tournent, et choisis la suivante.',
            help_pattern_s1: 'Regroupe les images pour voir le bloc qui se répète : 🌞, 🌙, ⭐.',
            help_pattern_s2: 'Compare le deuxième groupe au premier : 🌞, 🌙, …',
            help_pattern_s3: 'L’image qui complète le cycle est ⭐ !',

            help_letters_goal: 'Suis l’alphabet et compte combien de lettres chaque pas saute.',
            help_letters_s1: 'Repère les lettres données dans l’alphabet : A, C, E.',
            help_letters_s2: 'Chaque saut avance de +2 lettres (B, D et F sont sautées).',
            help_letters_s3: 'Deux pas après E, on tombe sur G !',

            help_intruder_goal: 'Trouve ce que trois des quatre images ont en commun et touche l’intrus.',
            help_intruder_s1: 'Observe les quatre images et cherche le point commun à trois d’entre elles.',
            help_intruder_s2: '🐙, 🐬 et 🐟 vivent dans l’eau, tandis que 🦁 vit sur terre.',
            help_intruder_s3: 'Choisis 🦁 comme intrus !',

            help_grid_goal: 'Chaque ligne et colonne doit contenir chaque symbole une seule fois. Trouve ce qui va dans « ? ».',
            help_grid_s1: 'Regarde la case jaune « ? » et vérifie les symboles déjà présents dans sa ligne et sa colonne.',
            help_grid_s2: 'Barre 🍎 et 🍌 car ils figurent déjà dans cette ligne ou colonne.',
            help_grid_s3: 'Il ne manque que 🍇 : place 🍇 dans « ? » !',

            help_compass_goal: 'Suis la tortue pas à pas (ou compte ses quarts de tour) pour voir où elle s’arrête.',
            help_compass_s1: 'Trouve la case de départ avec 🐢 et lis la première flèche : ⬆️.',
            help_compass_s2: 'Monte d’une case jusqu’à B, puis lis la flèche suivante : ➡️.',
            help_compass_s3: 'Avance d’une case à droite : la tortue s’arrête sur C !',

            help_lamps_goal: 'Chaque bouton inverse les lampes indiquées (allumée ↔ éteinte). Suis-les jusqu’à la fin.',
            help_lamps_s1: 'Pars de l’état initial (💡 🌑 💡) et appuie sur le premier bouton (1, 2).',
            help_lamps_s2: 'Les lampes 1 et 2 passent à 🌑 💡 💡 ; appuie ensuite sur le second bouton (2, 3).',
            help_lamps_s3: 'Les lampes 2 et 3 changent : l’état final est 🌑 🌑 🌑 !',

            help_code_goal: 'Compare les mots d’exemple avec leur code pour découvrir la règle de transformation.',
            help_code_s1: 'Compare lettre par lettre : CAT devient TAC (écrit à l’envers).',
            help_code_s2: 'Applique exactement le même déplacement de lettres au mot DOG.',
            help_code_s3: 'En lisant DOG à l’envers, on obtient GOD !',

            help_ifthen_goal: 'Prends les prémisses absurdes pour vraies et vois si la conclusion suit à coup sûr.',
            help_ifthen_s1: 'Lis la règle : « Si le dragon éternue, alors la lune devient violette ».',
            help_ifthen_s2: 'Vérifie le fait connu : le dragon éternue (la condition est déclenchée).',
            help_ifthen_s3: 'Seul « Oui » colle avec tout (si Oui et Non étaient possibles, tu choisirais « On ne sait pas ») !',

            help_liars_goal: 'Les anges (😇) disent toujours vrai ; les démons (😈) mentent toujours. Trouve la seule combinaison possible.',
            help_liars_s1: 'Suppose A = 😇 : alors sa phrase « B est 😈 » doit être vraie.',
            help_liars_s2: 'Vérifie B (« Nous sommes différents ») : comme 😇 ≠ 😈, B dirait vrai — impossible pour un 😈 !',
            help_liars_s3: 'Avec A = 😈 et B = 😇, A ment et B dit vrai : tout fonctionne !',

            help_boxes_goal: 'Le trésor est dans un seul coffre. Teste chaque coffre et compte combien d’étiquettes sont vraies.',
            help_boxes_s1: 'Si le trésor était dans 🟥, compte combien d’étiquettes seraient vraies (ici 2 ≠ 1).',
            help_boxes_s2: 'Essaie maintenant avec le trésor dans 🟦 et revérifie chaque étiquette.',
            help_boxes_s3: 'Avec le trésor dans 🟦, exactement 1 étiquette est vraie : il est dans 🟦 !',

            help_whoowns_goal: 'Associe chaque personne à son animal (et sa maison) en éliminant ce qui est impossible.',
            help_whoowns_s1: 'Avec le premier indice, élimine les paires impossibles : Ada n’a ni 🐶 ni 🐱, donc Ada a 🐰.',
            help_whoowns_s2: 'Barre 🐰 pour les autres ; Bruno n’a pas 🐶, donc Bruno a 🐱.',
            help_whoowns_s3: 'Il ne reste que 🐶 pour Clara !',

            help_days_goal: 'Place le jour connu sur la semaine, puis compte en avant ou en arrière.',
            help_days_s1: 'Trouve d’abord « aujourd’hui » : si hier était mardi, aujourd’hui est mercredi.',
            help_days_s2: 'Compte depuis mercredi jusqu’au jour demandé (« après-demain » = +2 jours).',
            help_days_s3: 'Deux jours après mercredi, c’est vendredi !',

            help_trap_goal: 'Méfie-toi de la réponse instinctive ! Visualise bien la scène avant de répondre.',
            help_trap_s1: 'Tu dépasses le 2e de la course : la réponse réflexe « 1er » est un piège !',
            help_trap_s2: 'Visualise le dépassement : tu prends la place du 2e, et le 1er est toujours devant toi.',
            help_trap_s3: 'Tu es maintenant à la 2e place !',

            help_scales_goal: 'Utilise les balances en équilibre (ou penchées) pour comparer les poids et trouver la réponse.',
            help_scales_s1: 'De la première balance, retiens que 1 🍍 pèse autant que 2 🍎.',
            help_scales_s2: 'Remplace 🍍 par 2 🍎 sur la deuxième balance : 🍉 équilibre 2 🍎 + 1 🍎.',
            help_scales_s3: '1 🍉 pèse donc 3 🍎 !',

            help_cups_goal: 'Suis les échanges de haut en bas pour savoir où finit chaque élément.',
            help_cups_s1: 'Pars de la rangée du haut (🍓 🍋 🫐) et fais le premier échange : places 1 ↔ 3.',
            help_cups_s2: 'La rangée devient 🫐 🍋 🍓 ; fais ensuite le second échange : places 2 ↔ 3.',
            help_cups_s3: 'L’ordre final est 🫐 🍓 🍋 !',

            help_locks_goal: 'Utilise les indices 🎯 (bonne place) et 🔄 (mauvaise place) pour déduire le code secret.',
            help_locks_s1: 'Un indice avec 0 🎯 et 0 🔄 élimine tous ses symboles (1, 2 et 3 sont exclus).',
            help_locks_s2: 'Compare les autres indices pour fixer chaque symbole à sa place exacte.',
            help_locks_s3: 'Seul 4 6 7 donne le compte exact de 🎯 et 🔄 sur chaque ligne !',

            help_suspects_goal: 'Compare les objets (ou déclarations) de chaque suspect avec les indices jusqu’à ce qu’il n’en reste qu’un.',
            help_suspects_s1: 'Vérifie le premier indice (« N’avait pas 🧤 ») : élimine 🐻.',
            help_suspects_s2: 'Vérifie le second indice (« Avait 🎩 ») : élimine 🐼.',
            help_suspects_s3: 'Seul 🦊 correspond à tous les indices !',

            help_alchemy_goal: 'Suis les règles du chaudron étape par étape (regarde quel ingrédient manque en premier !).',
            help_alchemy_s1: 'Chaque potion 🧪 demande 2 🍄 et 1 💎. Prépare la première 🧪 avec ta réserve.',
            help_alchemy_s2: 'Prépare une deuxième 🧪 : il reste 1 🍄 et 0 💎 (plus de 💎 !).',
            help_alchemy_s3: 'Tu peux préparer au maximum 2 🧪 !',

            help_seats_goal: 'Place les personnages autour de la table ou sur le banc indice par indice.',
            help_seats_s1: 'Mets 🦊 à la place 1 ; « 🐼 est en face de 🦊 » place 🐼 en 3.',
            help_seats_s2: '« 🐸 est juste après 🦊 dans le sens horaire » place 🐸 en 2, laissant la place 4 à 🦉.',
            help_seats_s3: 'Toutes les places sont fixées : lis qui occupe la place demandée !',

            help_mirror_goal: 'Suis comment la grille se retourne (↔️ horizontal, ↕️ vertical) ou tourne (↻, ↺, 🔄).',
            help_mirror_s1: 'Observe la grille 2×2 de départ et le mouvement demandé : ↔️ (miroir horizontal).',
            help_mirror_s2: 'En retournant horizontalement, les colonnes gauche et droite s’échangent (🍎 ↔ 🍌 et 🍇 ↔ 🍒).',
            help_mirror_s3: 'Choisis la grille avec les positions inversées !',

            help_gears_goal: 'Détermine le sens de rotation ou le nombre de tours des engrenages reliés.',
            help_gears_s1: 'L’engrenage A tourne dans le sens horaire (↻). Deux roues en contact inversent leur sens.',
            help_gears_s2: 'La roue B touche A et tourne dans le sens antihoraire (↺).',
            help_gears_s3: 'La roue C touche B et tourne à nouveau dans le sens horaire (↻) !',

            help_lasers_goal: 'Suis le rayon laser qui rebondit à 90° sur les miroirs pour toucher une cible.',
            help_lasers_s1: 'L’émetteur tire un rayon en ligne droite vers la droite.',
            help_lasers_s2: 'Le faisceau rencontre le miroir \\ et est dévié de 90° vers le bas.',
            help_lasers_s3: 'En continuant vers le bas, le rayon frappe le diamant 💎 !',

            help_cubes_goal: 'Plie mentalement le patron à plat pour reconstituer le cube et trouver les faces opposées.',
            help_cubes_s1: 'Sur une bande, deux faces séparées par une case intermédiaire finissent opposées.',
            help_cubes_s2: 'Ici 🍌 et 🍓 sont séparés par 🍇, donc au pliage ils se font face.',
            help_cubes_s3: 'La face opposée à 🍌 est 🍓 !',

            help_pipes_goal: 'Suis l’eau dans les tuyaux et trouve le premier réservoir rempli à ras bord.',
            help_pipes_s1: 'L’eau entre dans A et le niveau monte. Regarde les tuyaux de A : à quelle hauteur est chacun, et est-il ouvert ou fermé (🚫) ?',
            help_pipes_s2: 'Le niveau atteint d’abord le tuyau ouvert le plus bas : de là, l’eau passe dans le réservoir suivant et A cesse de monter.',
            help_pipes_s3: 'Suis l’eau réservoir après réservoir : le premier sans sortie ouverte se remplit à ras bord. Ici, c’est B !',

            help_mobiles_goal: 'Trouve le contrepoids manquant qui équilibre parfaitement chaque bras du mobile.',
            help_mobiles_s1: 'Rappelle-toi la loi du levier : Couple = Poids × Distance au pivot central.',
            help_mobiles_s2: 'Le bras gauche porte 6 à distance 1 (couple = 6). Le bras droit a une distance de 2.',
            help_mobiles_s3: 'Pour équilibrer un couple de 6 à distance 2, il faut 6 ÷ 2 = 3 : choisis 3 !',

            help_origami_goal: 'Déplie mentalement la feuille à rebours pour trouver l’emplacement final des trous.',
            help_origami_s1: 'Repars des plis et des flèches en sens inverse, comme pour réouvrir le papier.',
            help_origami_s2: 'Chaque pli se comporte comme un miroir : le trou se reflète symétriquement.',
            help_origami_s3: 'En dépliant les deux plis, on obtient une symétrie quadruple : choisis la bonne carte !',
        },
        de: {
            how_to_play: 'Spielanleitung',
            help_step: 'Schritt {n} von {m}',
            help_prev: 'Vorheriger Schritt',
            help_next: 'Nächster Schritt',
            help_replay: 'Animation wiederholen',
            help_got_it: 'Verstanden!',
            help_paused: 'Zeit pausiert',

            help_sign_goal: 'Finde heraus, welches Rechenzeichen (+, −, ×, ÷, ^) die Gleichung wahr macht.',
            help_sign_s1: 'Schau dir die Zahlen und das Endergebnis rechts an.',
            help_sign_s2: 'Probiere die Zeichen in der Lücke aus (denk an Punkt- vor Strichrechnung: × und ÷ vor + und −).',
            help_sign_s3: '6 × 2 = 12 trifft das Ergebnis: Wähle ×!',

            help_sequence_goal: 'Entdecke die Regel zwischen aufeinanderfolgenden Zahlen und finde die nächste.',
            help_sequence_s1: 'Vergleiche jede Zahl mit der direkt folgenden.',
            help_sequence_s2: 'Miss den Abstand zwischen Nachbarn: Hier addiert jeder Schritt +3.',
            help_sequence_s3: 'Wende denselben Schritt auf die letzte Zahl an: 12 + 3 = 15!',

            help_symbols_goal: 'Bestimme den Wert jeder Frucht Zeile für Zeile und löse dann die letzte Zeile.',
            help_symbols_s1: 'Beginne mit der Zeile mit nur einer Fruchtsorte: 🍎 + 🍎 = 10 bedeutet 🍎 = 5.',
            help_symbols_s2: 'Setze 🍎 = 5 in die zweite Zeile ein: 5 + 🍌 = 8 ergibt 🍌 = 3.',
            help_symbols_s3: 'Jetzt kennst du den gesuchten Wert in der letzten Zeile: 🍌 = 3!',

            help_oddone_goal: 'Drei Zahlen teilen eine versteckte mathematische Eigenschaft; finde den Ausreißer.',
            help_oddone_s1: 'Betrachte alle vier Zahlen zusammen und suche, was drei davon verbindet.',
            help_oddone_s2: '4, 9 und 16 sind Quadratzahlen (2², 3², 4²), 15 dagegen nicht.',
            help_oddone_s3: 'Wähle 15 — die einzige Zahl, die aus der Reihe tanzt!',

            help_order_goal: 'Bringe die Figuren mithilfe der Hinweise in die richtige Reihenfolge.',
            help_order_s1: 'Ordne mit dem ersten Hinweis die ersten zwei: 🦊 ist vor 🐢.',
            help_order_s2: 'Nimm den zweiten Hinweis dazu: 🐰 ist vor 🦊, also 🐰 > 🦊 > 🐢.',
            help_order_s3: 'Lies den gesuchten Platz ab: 🐰 ist auf Platz 1!',

            help_weird_goal: 'Das Symbol ⊕ verbirgt eine geheime Regel in den Beispielen. Finde sie und wende sie an.',
            help_weird_s1: 'Prüfe, wie die ersten zwei Zahlen das Ergebnis bilden: (2 × 3) + 1 = 7.',
            help_weird_s2: 'Teste dieselbe Regel am zweiten Beispiel: (4 × 5) + 1 = 21 ✓.',
            help_weird_s3: 'Wende die Regel auf die Frage an: (3 × 4) + 1 = 13!',

            help_nonsense_goal: 'Auch bei Fantasiewörtern gilt: Folge streng der logischen Kette, ohne zu raten.',
            help_nonsense_s1: 'Stell dir die Wörter wie Schachteln vor: Jeder „Plip“ steckt in „Zorg“.',
            help_nonsense_s2: 'Verknüpfe den zweiten Satz: Jeder „Zorg“ steckt in „Blau“.',
            help_nonsense_s3: 'Also ist jeder „Plip“ garantiert „Blau“!',

            help_riddle_goal: 'Lies jedes Wort genau: Rätsel spielen mit Doppeldeutigkeiten und kleinen Details.',
            help_riddle_s1: 'Achte auf die Schlüsselhinweise: „Städte ohne Häuser, Flüsse ohne Wasser“.',
            help_riddle_s2: 'Schließe wörtliche Antworten aus, die den Hinweisen widersprechen.',
            help_riddle_s3: 'Eine Landkarte zeigt gezeichnete Städte und Flüsse, ganz ohne Steine oder Wasser!',

            help_pattern_goal: 'Erkenne, wie sich die Bilder wiederholen, abwechseln oder drehen, und wähle das nächste.',
            help_pattern_s1: 'Gruppiere die Bilder, um den wiederholten Block zu sehen: 🌞, 🌙, ⭐.',
            help_pattern_s2: 'Vergleiche die zweite Gruppe mit der ersten: 🌞, 🌙, …',
            help_pattern_s3: 'Das nächste Bild im Zyklus ist ⭐!',

            help_letters_goal: 'Folge dem Alphabet und zähle, wie viele Buchstaben jeder Schritt überspringt.',
            help_letters_s1: 'Suche die gegebenen Buchstaben im Alphabet: A, C, E.',
            help_letters_s2: 'Jeder Sprung geht +2 Buchstaben weiter (B, D und F werden übersprungen).',
            help_letters_s3: 'Zwei Schritte nach E landet man bei G!',

            help_intruder_goal: 'Finde heraus, was drei der vier Bilder gemeinsam haben, und wähle den Eindringling.',
            help_intruder_s1: 'Schau dir alle vier Bilder an und suche das gemeinsame Merkmal von dreien.',
            help_intruder_s2: '🐙, 🐬 und 🐟 leben im Wasser, während 🦁 an Land lebt.',
            help_intruder_s3: 'Wähle 🦁 als Eindringling!',

            help_grid_goal: 'Jede Zeile und Spalte muss jedes Symbol genau einmal enthalten. Finde, was in „?“ gehört.',
            help_grid_s1: 'Schau auf das gelbe Feld „?“ und prüfe die Symbole in seiner Zeile und Spalte.',
            help_grid_s2: 'Streiche 🍎 und 🍌, weil sie in dieser Zeile oder Spalte schon vorkommen.',
            help_grid_s3: 'Nur 🍇 fehlt noch — setze 🍇 in „?“ ein!',

            help_compass_goal: 'Folge der Schildkröte Schritt für Schritt (oder zähle ihre Drehungen) bis zum Ziel.',
            help_compass_s1: 'Finde das Startfeld mit 🐢 und lies den ersten Pfeil: ⬆️.',
            help_compass_s2: 'Geh ein Feld nach oben zu B und lies den nächsten Pfeil: ➡️.',
            help_compass_s3: 'Geh ein Feld nach rechts: Die Schildkröte hält auf C!',

            help_lamps_goal: 'Jeder Knopf schaltet die genannten Lampen um (an ↔ aus). Verfolge sie bis zum Ende.',
            help_lamps_s1: 'Starte beim Anfangszustand (💡 🌑 💡) und drücke den ersten Knopf (1, 2).',
            help_lamps_s2: 'Lampen 1 und 2 wechseln zu 🌑 💡 💡; drücke nun den zweiten Knopf (2, 3).',
            help_lamps_s3: 'Lampen 2 und 3 wechseln: Der Endzustand ist 🌑 🌑 🌑!',

            help_code_goal: 'Vergleiche die Beispielwörter mit ihrem Code, um die Umwandlungsregel zu knacken.',
            help_code_s1: 'Vergleiche Buchstabe für Buchstabe: CAT wird zu TAC (rückwärts gelesen).',
            help_code_s2: 'Wende genau dieselbe Buchstabenbewegung auf das Wort DOG an.',
            help_code_s3: 'DOG rückwärts gelesen ergibt GOD!',

            help_ifthen_goal: 'Nimm die absurden Sätze als wahr an und prüfe, ob die Schlussfolgerung zwingend folgt.',
            help_ifthen_s1: 'Lies die Regel: „Wenn der Drache niest, wird der Mond lila.“',
            help_ifthen_s2: 'Prüfe die bekannte Tatsache: Der Drache niest (die Bedingung tritt ein).',
            help_ifthen_s3: 'Nur „Ja“ passt zu allem (wären Ja und Nein möglich, würdest du „Nicht sicher“ wählen)!',

            help_liars_goal: 'Engel (😇) sagen immer die Wahrheit; Teufel (😈) lügen immer. Teste, welche Kombination aufgeht.',
            help_liars_s1: 'Nimm an, A = 😇: Dann müsste A’s Satz „B ist 😈“ wahr sein.',
            help_liars_s2: 'Prüfe B („Wir sind verschieden“): Da 😇 ≠ 😈, würde B die Wahrheit sagen — unmöglich für einen 😈!',
            help_liars_s3: 'Mit A = 😈 und B = 😇 lügt A und B sagt die Wahrheit: Alles passt!',

            help_boxes_goal: 'Der Schatz ist in einer Truhe. Probiere jede Truhe durch und zähle die wahren Schilder.',
            help_boxes_s1: 'Wäre der Schatz in 🟥, zähle, wie viele Schilder wahr wären (hier 2 ≠ 1).',
            help_boxes_s2: 'Probiere nun den Schatz in 🟦 und prüfe alle Schilder erneut.',
            help_boxes_s3: 'Mit dem Schatz in 🟦 ist genau 1 Schild wahr: Er liegt in 🟦!',

            help_whoowns_goal: 'Ordne jeder Person ihr Tier (und Haus) zu, indem du Unmögliches ausschließt.',
            help_whoowns_s1: 'Schließe mit dem ersten Hinweis Paare aus: Ada hat weder 🐶 noch 🐱, also hat Ada 🐰.',
            help_whoowns_s2: 'Streiche 🐰 für die anderen; Bruno hat nicht 🐶, also hat Bruno 🐱.',
            help_whoowns_s3: 'Für Clara bleibt 🐶 übrig!',

            help_days_goal: 'Verankere den bekannten Tag in der Woche und zähle dann vorwärts oder rückwärts.',
            help_days_s1: 'Bestimme zuerst „heute“: Wenn gestern Dienstag war, ist heute Mittwoch.',
            help_days_s2: 'Zähle von Mittwoch bis zum gesuchten Tag („übermorgen“ = +2 Tage).',
            help_days_s3: 'Zwei Tage nach Mittwoch ist Freitag!',

            help_trap_goal: 'Vorsicht vor der ersten Bauchgefühl-Antwort! Stell dir die Szene genau vor.',
            help_trap_s1: 'Du überholst den Zweiten im Rennen: Die schnelle Antwort „1. Platz“ ist eine Falle!',
            help_trap_s2: 'Stell dir das Überholen vor: Du nimmst den Platz des Zweiten ein, der Erste ist noch vor dir.',
            help_trap_s3: 'Du bist jetzt auf dem 2. Platz!',

            help_scales_goal: 'Nutze die Waagen im Gleichgewicht (oder mit Neigung), um Gewichte zu vergleichen.',
            help_scales_s1: 'Aus der ersten Waage folgt: 1 🍍 wiegt so viel wie 2 🍎.',
            help_scales_s2: 'Ersetze 🍍 auf der zweiten Waage durch 2 🍎: 🍉 hält 2 🍎 + 1 🍎 die Waage.',
            help_scales_s3: '1 🍉 wiegt also 3 🍎!',

            help_cups_goal: 'Verfolge die Tauschschritte von oben nach unten, um zu sehen, wo jedes Teil landet.',
            help_cups_s1: 'Starte mit der oberen Reihe (🍓 🍋 🫐) und führe den ersten Tausch aus: Platz 1 ↔ 3.',
            help_cups_s2: 'Die Reihe lautet nun 🫐 🍋 🍓; führe jetzt den zweiten Tausch aus: Platz 2 ↔ 3.',
            help_cups_s3: 'Die Endreihenfolge ist 🫐 🍓 🍋!',

            help_locks_goal: 'Nutze die Hinweise 🎯 (richtiger Platz) und 🔄 (falscher Platz), um den Code zu knacken.',
            help_locks_s1: 'Ein Hinweis mit 0 🎯 und 0 🔄 streicht alle seine Symbole (1, 2 und 3 fallen weg).',
            help_locks_s2: 'Vergleiche die übrigen Hinweise, um jedes Symbol am richtigen Platz festzulegen.',
            help_locks_s3: 'Nur 4 6 7 erfüllt alle 🎯- und 🔄-Angaben!',

            help_suspects_goal: 'Vergleiche die Gegenstände (oder Aussagen) jedes Verdächtigen mit den Hinweisen, bis nur einer übrig bleibt.',
            help_suspects_s1: 'Prüfe den ersten Hinweis („Hatte kein 🧤“): Streiche 🐻.',
            help_suspects_s2: 'Prüfe den zweiten Hinweis („Hatte 🎩“): Streiche 🐼.',
            help_suspects_s3: 'Nur 🦊 erfüllt alle Hinweise!',

            help_alchemy_goal: 'Folge den Kesselregeln Schritt für Schritt (achte darauf, welche Zutat zuerst ausgeht!).',
            help_alchemy_s1: 'Jeder Trank 🧪 braucht 2 🍄 und 1 💎. Braue den ersten 🧪 aus deinem Vorrat.',
            help_alchemy_s2: 'Braue einen zweiten 🧪: Es bleiben 1 🍄 und 0 💎 übrig (💎 ist aufgebraucht!).',
            help_alchemy_s3: 'Du kannst höchstens 2 🧪 brauen!',

            help_seats_goal: 'Setze die Figuren Hinweis für Hinweis um den Tisch oder auf die Bank.',
            help_seats_s1: 'Setze 🦊 auf Platz 1; „🐼 sitzt 🦊 gegenüber“ setzt 🐼 auf Platz 3.',
            help_seats_s2: '„🐸 sitzt im Uhrzeigersinn direkt nach 🦊“ setzt 🐸 auf Platz 2, Platz 4 bleibt für 🦉.',
            help_seats_s3: 'Alle Plätze sind vergeben — lies den gesuchten Platz ab!',

            help_mirror_goal: 'Verfolge, wie sich das Gitter spiegelt (↔️ horizontal, ↕️ vertikal) oder dreht (↻, ↺, 🔄).',
            help_mirror_s1: 'Schau dir das 2×2-Startgitter und die geforderte Bewegung an: ↔️ (horizontal spiegeln).',
            help_mirror_s2: 'Beim horizontalen Spiegeln tauschen linke und rechte Spalte (🍎 ↔ 🍌 und 🍇 ↔ 🍒).',
            help_mirror_s3: 'Wähle das Gitter mit den gespiegelten Positionen!',

            help_gears_goal: 'Bestimme Drehrichtung oder Umdrehungen der verbundenen Zahnräder.',
            help_gears_s1: 'Zahnrad A dreht sich im Uhrzeigersinn (↻). Berührende Räder kehren die Richtung um.',
            help_gears_s2: 'Zahnrad B greift in A und dreht sich gegen den Uhrzeigersinn (↺).',
            help_gears_s3: 'Zahnrad C greift in B und dreht sich wieder im Uhrzeigersinn (↻)!',

            help_lasers_goal: 'Verfolge den Laserstrahl bei 90°-Reflexionen an Spiegeln bis zum Ziel.',
            help_lasers_s1: 'Der Sender schießt einen Strahl geradeaus nach rechts.',
            help_lasers_s2: 'Der Strahl trifft auf Spiegel \\ und wird um 90° nach unten abgelenkt.',
            help_lasers_s3: 'Auf seinem Weg nach unten trifft der Strahl den Diamanten 💎!',

            help_cubes_goal: 'Falte das flache Netz im Kopf zu einem 3D-Würfel, um Gegenüberflächen zu finden.',
            help_cubes_s1: 'In einem Streifen liegen Flächen mit genau einem Feld dazwischen einander gegenüber.',
            help_cubes_s2: 'Hier haben 🍌 und 🍓 das Feld 🍇 zwischen sich; beim Falten stehen sie sich gegenüber.',
            help_cubes_s3: 'Die gegenüberliegende Fläche von 🍌 ist 🍓!',

            help_pipes_goal: 'Folge dem Wasser durch die Rohre und finde den ersten Tank, der bis zum Rand voll ist.',
            help_pipes_s1: 'Wasser fließt in A und der Pegel steigt. Sieh dir die Rohre von A an: Wie hoch liegt jedes, und ist es offen oder zu (🚫)?',
            help_pipes_s2: 'Der Pegel erreicht zuerst das tiefste offene Rohr: Von dort fließt das Wasser in den nächsten Tank, und A steigt nicht weiter.',
            help_pipes_s3: 'Folge dem Wasser von Tank zu Tank: Der erste ohne offenen Ausgang wird randvoll. Hier ist es B!',

            help_mobiles_goal: 'Finde das Gegengewicht, das jeden Hebelarm des Mobiles perfekt ausbalanciert.',
            help_mobiles_s1: 'Nutze das Hebelgesetz: Drehmoment = Gewicht × Abstand vom Drehpunkt.',
            help_mobiles_s2: 'Der linke Arm hat Gewicht 6 im Abstand 1 (Moment = 6). Der rechte Arm hat Abstand 2.',
            help_mobiles_s3: 'Um 6 im Abstand 2 auszugleichen, braucht man 6 ÷ 2 = 3: Wähle 3!',

            help_origami_goal: 'Falte das Papier in Gedanken rückwärts auf, um die Position der Löcher zu finden.',
            help_origami_s1: 'Verfolge Faltlinien und Pfeile rückwärts, als würdest du das Blatt wieder öffnen.',
            help_origami_s2: 'Jede Faltkante wirkt wie ein Spiegel: Das Loch spiegelt sich exakt zur Gegenseite.',
            help_origami_s3: 'Nach beiden Faltungen entsteht ein 4-fach symmetrisches Muster: Wähle das richtige Blatt!',
        },
    });

    // ---------- animated scene builders for all 28 categories ----------
    function chip(h, text, cls) {
        return h('span.h-chip' + (cls || ''), text);
    }

    const SCENES = {
        sign(s, h) {
            return h('div.h-scene',
                h('div.h-row.h-big',
                    chip(h, '6'),
                    chip(h, s === 0 ? '?' : (s === 1 ? '+ / ×' : '×'), s === 0 ? '.pulse' : (s === 2 ? '.ok.pop' : '.focus')),
                    chip(h, '2'),
                    h('span.h-op', '='),
                    chip(h, '12', s > 0 ? '.focus' : '')),
                h('div.h-chips',
                    chip(h, '+  (6+2=8)', s >= 1 ? '.bad' : ''),
                    chip(h, '−  (6−2=4)', s >= 1 ? '.bad' : ''),
                    chip(h, '×  (6×2=12)', s === 1 ? '.focus.pulse' : (s === 2 ? '.ok.pop' : '')),
                    chip(h, '÷  (6÷2=3)', s >= 1 ? '.bad' : '')));
        },
        sequence(s, h) {
            const nums = ['3', '6', '9', '12', s === 2 ? '15' : '?'];
            return h('div.h-scene',
                s >= 1 ? h('div.h-jumps', ['+3', '+3', '+3', '+3'].map((j, i) => h('span.h-jump' + (s === 1 && i < 3 ? '.pop' : '') + (i === 3 && s >= 1 ? '.focus.pop' : ''), j))) : null,
                h('div.h-row', nums.map((n, i) => chip(h, n, i === 4 ? (s === 2 ? '.ok.pop' : '.pulse') : (s === 1 ? '.focus' : '')))));
        },
        symbols(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line' + (s === 0 ? '.focus' : ''), '🍎 + 🍎 = 10', s >= 0 ? chip(h, '🍎 = 5', s === 0 ? '.focus.pop' : '.ok') : null),
                h('div.h-line' + (s === 1 ? '.focus' : ''), s >= 1 ? '5 + 🍌 = 8' : '🍎 + 🍌 = 8', s >= 1 ? chip(h, '🍌 = 3', s === 1 ? '.focus.pop' : '.ok') : null),
                h('div.h-line' + (s === 2 ? '.focus' : ''), '🍌 = ', chip(h, s === 2 ? '3' : '?', s === 2 ? '.ok.pop' : '.pulse')));
        },
        oddone(s, h) {
            const items = [
                ['4', '2² ✓', true],
                ['9', '3² ✓', true],
                ['15', '≠ □²', false],
                ['16', '4² ✓', true],
            ];
            return h('div.h-scene',
                h('div.h-grid4', items.map(([n, sub, sq]) => h('div.h-card' + (s === 1 ? (sq ? '.focus' : '.bad') : '') + (s === 2 ? (sq ? '.dim' : '.ok.pop') : ''),
                    h('strong', n),
                    s >= 1 ? h('small', sub) : null))));
        },
        order(s, h) {
            const slots = s === 0 ? ['🦊', '🐢', '·'] : ['🐰', '🦊', '🐢'];
            return h('div.h-scene',
                h('div.h-row',
                    chip(h, '1) 🦊 > 🐢', s === 0 ? '.focus.pulse' : ''),
                    chip(h, '2) 🐰 > 🦊', s === 1 ? '.focus.pulse' : '')),
                h('div.h-row', slots.map((x, i) => h('div.h-card' + (s === 2 && i === 0 ? '.ok.pop' : (s === 1 && i === 0 ? '.focus.pop' : '')),
                    h('small', (i + 1) + '°'),
                    h('strong', x)))));
        },
        weird(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line' + (s === 0 ? '.focus' : ''), '2 ⊕ 3 = 7', s >= 0 ? chip(h, '(2×3)+1 = 7', s === 0 ? '.focus.pop' : '') : null),
                h('div.h-line' + (s === 1 ? '.focus' : ''), '4 ⊕ 5 = 21', s >= 1 ? chip(h, '(4×5)+1 = 21 ✓', s === 1 ? '.focus.pop' : '.ok') : null),
                h('div.h-line' + (s === 2 ? '.focus' : ''), '3 ⊕ 4 = ', chip(h, s === 2 ? '13' : '?', s === 2 ? '.ok.pop' : '.pulse')));
        },
        nonsense(s, h) {
            return h('div.h-scene',
                h('div.h-row',
                    chip(h, 'Plip', s >= 0 ? '.focus' : ''),
                    h('span.h-op', '➔'),
                    chip(h, 'Zorg', s >= 0 ? '.focus' : ''),
                    s >= 1 ? [h('span.h-op', '➔'), chip(h, '🔵', s === 1 ? '.focus.pop' : '.ok')] : null),
                s === 2 ? h('div.h-row', chip(h, 'Plip ➔ 🔵 ✓', '.ok.pop')) : null);
        },
        riddle(s, h) {
            return h('div.h-scene',
                h('div.h-line.focus', '🏙️ ≠ 🏠  ·  🏞️ ≠ 💧'),
                h('div.h-grid4',
                    chip(h, '🗺️', s === 2 ? '.ok.pop' : (s === 0 ? '.pulse' : '')),
                    chip(h, '🏠', s >= 1 ? '.bad' : ''),
                    chip(h, '🌊', s >= 1 ? '.bad' : ''),
                    chip(h, '🪨', s >= 1 ? '.bad' : '')));
        },
        pattern(s, h) {
            return h('div.h-scene',
                h('div.h-row',
                    h('div.h-group' + (s >= 0 ? '.focus' : ''), chip(h, '🌞'), chip(h, '🌙'), chip(h, '⭐')),
                    h('div.h-group' + (s >= 1 ? '.focus' : ''), chip(h, '🌞'), chip(h, '🌙'), chip(h, s === 2 ? '⭐' : '?', s === 2 ? '.ok.pop' : '.pulse'))));
        },
        letters(s, h) {
            const abc = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            return h('div.h-scene',
                h('div.h-row', abc.map((l, i) => {
                    const even = i % 2 === 0;
                    const cls = i === 6 ? (s === 2 ? '.ok.pop' : (s === 1 ? '.pulse' : '')) : (even ? '.focus' : (s >= 1 ? '.bad' : ''));
                    return chip(h, l, cls);
                })),
                s >= 1 ? h('div.h-row', chip(h, 'A ➔ C (+2)', '.focus'), chip(h, 'C ➔ E (+2)', '.focus'), chip(h, 'E ➔ G (+2)', s === 2 ? '.ok.pop' : '.focus.pop')) : null);
        },
        intruder(s, h) {
            const pics = [
                ['🐙', '🌊', false],
                ['🐬', '🌊', false],
                ['🦁', '🌿', true],
                ['🐟', '🌊', false],
            ];
            return h('div.h-scene',
                h('div.h-grid4', pics.map(([p, env, odd]) => h('div.h-card' + (s === 1 ? (odd ? '.focus.pulse' : '.dim') : '') + (s === 2 ? (odd ? '.ok.pop' : '.dim') : ''),
                    h('strong', p),
                    s >= 1 ? h('small', env) : null))));
        },
        grid(s, h) {
            const cells = [
                ['🍎', '🍌', '🍇'],
                ['🍌', '🍇', '🍎'],
                ['🍇', '🍎', s === 2 ? '🍇' : '?'],
            ];
            // Valid 3x3 Latin square:
            // Row 0: 🍎 🍌 🍇
            // Row 1: 🍇 🍎 🍌
            // Row 2: 🍌 🍇 ? -> 🍎 (or rotated so 🍇 is the answer)
            const latin = [
                ['🍌', '🍇', '🍎'],
                ['🍇', '🍎', '🍌'],
                ['🍎', '🍌', s === 2 ? '🍇' : '?'],
            ];
            return h('div.h-scene',
                h('div.h-board3', latin.flatMap((row, r) => row.map((c, col) => {
                    const isAsk = r === 2 && col === 2;
                    const inLine = s === 1 && (r === 2 || col === 2) && !isAsk;
                    return h('span.h-cell' + (isAsk ? (s === 2 ? '.ok.pop' : '.ask.pulse') : (inLine ? '.focus' : '')), c);
                }))),
                s >= 1 ? h('div.h-row', chip(h, '🍎', '.bad'), chip(h, '🍌', '.bad'), chip(h, '🍇', s === 2 ? '.ok.pop' : '.focus.pulse')) : null);
        },
        compass(s, h) {
            const labels = [
                ['A', 'B', 'C'],
                ['D', 'E', 'F'],
                ['G', 'H', 'I'],
            ];
            const pos = s === 0 ? [1, 1] : (s === 1 ? [0, 1] : [0, 2]);
            return h('div.h-scene',
                h('div.h-board3', labels.flatMap((row, r) => row.map((c, col) => {
                    const here = r === pos[0] && col === pos[1];
                    return h('span.h-cell' + (here ? (s === 2 ? '.ok.pop' : '.focus.pop') : ''), here ? '🐢' : c);
                }))),
                h('div.h-row',
                    chip(h, '1) ⬆️ (E ➔ B)', s === 0 || s === 1 ? '.focus' : ''),
                    chip(h, '2) ➡️ (B ➔ C)', s === 2 ? '.ok.pop' : (s === 1 ? '.pulse' : ''))));
        },
        lamps(s, h) {
            const states = [
                ['💡', '🌑', '💡'],
                ['🌑', '💡', '💡'],
                ['🌑', '🌑', '🌑'],
            ][s];
            return h('div.h-scene',
                h('div.h-row', window.ThinkHopUI && window.ThinkHopUI.bulbs
                    ? h('span.h-chip' + (s === 2 ? '.ok.pop' : '.focus'), window.ThinkHopUI.bulbs(states.join(''), true))
                    : states.map((l, i) => chip(h, `${i + 1}: ${l}`, s === 2 ? '.ok.pop' : '.focus'))),
                h('div.h-row',
                    chip(h, '🔘 (1, 2)', s === 0 || s === 1 ? '.focus.pulse' : ''),
                    chip(h, '🔘 (2, 3)', s === 2 ? '.ok.pop' : (s === 1 ? '.pulse' : ''))));
        },
        code(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line' + (s === 0 ? '.focus' : ''), chip(h, 'C A T'), h('span.h-op', '⇄'), chip(h, 'T A C', '.focus')),
                h('div.h-line' + (s >= 1 ? '.focus' : ''), chip(h, 'D O G'), h('span.h-op', '⇄'), chip(h, s === 2 ? 'G O D' : '? ? ?', s === 2 ? '.ok.pop' : '.pulse')));
        },
        ifthen(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line' + (s >= 0 ? '.focus' : ''), chip(h, '🐉 ➔ 🟣🌙', s === 0 ? '.focus.pulse' : '')),
                s >= 1 ? h('div.h-line', chip(h, '🐉 ✓', '.focus.pop'), h('span.h-op', '⟹'), chip(h, '🟣🌙 ?', s === 1 ? '.pulse' : '.ok.pop')) : null,
                s === 2 ? h('div.h-row', chip(h, '✓ ' + t('yes'), '.ok.pop'), chip(h, t('no'), '.bad'), chip(h, t('unknown'), '.dim')) : null);
        },
        liars(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line', chip(h, '🅰️: “🅱️ = 😈”'), chip(h, '🅱️: “🅰️ ≠ 🅱️”')),
                s === 0 ? h('div.h-line', chip(h, '🅰️=😇 ➔ 🅱️=😈 ?', '.focus.pulse')) : null,
                s === 1 ? h('div.h-line', chip(h, '🅰️=😇, 🅱️=😈 ➔ 🅱️=😇 ✗', '.bad.pop')) : null,
                s === 2 ? h('div.h-line', chip(h, '🅰️=😈, 🅱️=😇 ✓', '.ok.pop')) : null);
        },
        boxes(s, h) {
            const cards = [
                ['🟥', '“💎 = 🟥”', s >= 0 ? (s === 0 ? '.focus.pulse' : '.bad') : ''],
                ['🟦', '“💎 ≠ 🟦”', s === 1 ? '.focus.pulse' : (s === 2 ? '.ok.pop' : '')],
                ['🟨', '“💎 ≠ 🟥”', s === 2 ? '.dim' : ''],
            ];
            return h('div.h-scene',
                h('div.h-row', cards.map(([box, lbl, cls], i) => h('div.h-card' + cls,
                    h('strong', s === 2 && i === 1 ? '🟦 💎' : box),
                    h('small', lbl)))));
        },
        whoowns(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line', 'Ada:', chip(h, '🐶', '.bad'), chip(h, '🐱', '.bad'), chip(h, '🐰 ✓', s === 0 ? '.focus.pop' : '.ok')),
                s >= 1 ? h('div.h-line', 'Bruno:', chip(h, '🐶', '.bad'), chip(h, '🐱 ✓', s === 1 ? '.focus.pop' : '.ok'), chip(h, '🐰', '.bad')) : null,
                s === 2 ? h('div.h-line', 'Clara:', chip(h, '🐶 ✓', '.ok.pop'), chip(h, '🐱', '.bad'), chip(h, '🐰', '.bad')) : null);
        },
        days(s, h) {
            const dKeys = ['day_0', 'day_1', 'day_2', 'day_3', 'day_4'];
            return h('div.h-scene',
                h('div.h-row', dKeys.map((k, i) => {
                    const cls = (s === 0 && i === 1) ? '.focus' : ((s >= 0 && i === 2) ? '.focus.pop' : ((s === 2 && i === 4) ? '.ok.pop' : (s === 1 && i === 3 ? '.pulse' : '')));
                    return chip(h, t(k).slice(0, 3), cls);
                })),
                h('div.h-row',
                    s === 0 ? chip(h, t('day_1') + ' ➔ ' + t('day_2'), '.focus.pop') : null,
                    s === 1 ? chip(h, t('day_2') + ' + 2', '.focus.pulse') : null,
                    s === 2 ? chip(h, '✓ ' + t('day_4'), '.ok.pop') : null));
        },
        trap(s, h) {
            return h('div.h-scene',
                h('div.h-row',
                    chip(h, '1° 🏃'),
                    chip(h, s === 0 ? '2° 🏃' : '2° 🏃 ⭐', s === 2 ? '.ok.pop' : (s === 1 ? '.focus.pop' : '')),
                    chip(h, s === 0 ? '3° 🏃 ⭐' : '3° 🏃', s === 0 ? '.focus.pulse' : '.dim')),
                h('div.h-row',
                    chip(h, '⚠️ 1°', s >= 0 ? '.bad' : ''),
                    chip(h, '✓ 2°', s === 2 ? '.ok.pop' : (s === 1 ? '.focus' : ''))));
        },
        scales(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line' + (s === 0 ? '.focus' : ''), '🍍  ⚖️  🍎🍎', s === 0 ? chip(h, '🍍 = 2 🍎', '.focus.pop') : null),
                h('div.h-line' + (s === 1 ? '.focus' : ''), s >= 1 ? '🍉  ⚖️  🍎🍎 + 🍎' : '🍉  ⚖️  🍍🍎'),
                h('div.h-line' + (s === 2 ? '.focus' : ''), '🍉  ⚖️  ', chip(h, s === 2 ? '3 🍎' : '? 🍎', s === 2 ? '.ok.pop' : '.pulse')));
        },
        cups(s, h) {
            const rows = [
                ['🍓', '🍋', '🫐'],
                ['🫐', '🍋', '🍓'],
                ['🫐', '🍓', '🍋'],
            ];
            return h('div.h-scene',
                h('div.h-row', rows[s].map((c, i) => h('div.h-card' + (s === 2 ? '.ok.pop' : ((s === 0 && (i === 0 || i === 2)) || (s === 1 && (i === 1 || i === 2)) ? '.focus.pop' : '')),
                    h('small', String(i + 1)),
                    h('strong', c)))),
                h('div.h-row',
                    chip(h, '1 ↔ 3', s === 0 || s === 1 ? '.focus' : ''),
                    chip(h, '2 ↔ 3', s === 2 ? '.ok' : (s === 1 ? '.focus.pulse' : ''))));
        },
        locks(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line', chip(h, '1 2 3', s >= 0 ? '.bad' : ''), h('small', '0 🎯 0 🔄')),
                h('div.h-line', chip(h, '4 5 6', s === 1 ? '.focus' : ''), h('small', '1 🎯 1 🔄')),
                h('div.h-line', chip(h, '4 6 7', s === 2 ? '.ok.pop' : (s === 1 ? '.focus.pulse' : '')), h('small', s === 2 ? '🔓 ✓' : '2 🎯 0 🔄')));
        },
        suspects(s, h) {
            const list = [
                ['🦊', '🎩 ☂️ ⌚', s === 2 ? '.ok.pop' : '.focus'],
                ['🐻', '🎩 🧤 ⌚', s >= 0 ? '.bad' : ''],
                ['🐼', '👓 ☂️ ⌚', s >= 1 ? '.bad' : ''],
            ];
            return h('div.h-scene',
                h('div.h-row', list.map(([who, items, cls]) => h('div.h-card' + cls, h('strong', who), h('small', items)))),
                h('div.h-row',
                    chip(h, '🚫 🧤', s === 0 ? '.focus.pulse' : ''),
                    s >= 1 ? chip(h, '✓ 🎩', s === 1 ? '.focus.pulse' : '.ok') : null));
        },
        alchemy(s, h) {
            return h('div.h-scene.h-lines',
                h('div.h-line', chip(h, '2 🍄 + 1 💎 ➔ 1 🧪', '.focus')),
                h('div.h-line',
                    s === 0 ? chip(h, '🍄🍄🍄 + 💎  (1 🧪)', '.focus.pop') : null,
                    s === 1 ? chip(h, '🍄 + 0 💎  (2 🧪)', '.focus.pop') : null,
                    s === 2 ? chip(h, '✓ 2 🧪', '.ok.pop') : null));
        },
        seats(s, h) {
            const p2 = s >= 1 ? '🐸' : '?';
            const p3 = s >= 0 ? '🐼' : '?';
            const p4 = s === 2 ? '🦉' : '?';
            return h('div.h-scene',
                h('div.h-table-demo',
                    chip(h, '1: 🦊', '.focus'),
                    h('div.h-table-mid',
                        chip(h, '4: ' + p4, s === 2 ? '.ok.pop' : ''),
                        h('span.h-table-round', '🪑'),
                        chip(h, '2: ' + p2, s === 1 ? '.focus.pop' : (s === 2 ? '.ok' : ''))),
                    chip(h, '3: ' + p3, s === 0 ? '.focus.pop' : (s === 2 ? '.ok' : ''))));
        },
        mirror(s, h) {
            const left = [
                ['🍎', '🍌'],
                ['🍇', '🍒'],
            ];
            const right = [
                ['🍌', '🍎'],
                ['🍒', '🍇'],
            ];
            return h('div.h-scene',
                h('div.h-row',
                    h('div.h-board2', left.flat().map(c => h('span.h-cell', c))),
                    chip(h, '↔️', s === 1 ? '.focus.pulse' : ''),
                    h('div.h-board2' + (s >= 1 ? '.flip-anim' : ''), (s >= 1 ? right : left).flat().map(c => h('span.h-cell' + (s === 2 ? '.ok.pop' : (s === 1 ? '.focus' : '')), c)))));
        },
        gears(s, h) {
            const cogSvg = (cls, spin, lbl) => `<svg class="help-gear-svg ${cls}" viewBox="-25 -25 50 50">
                <path class="help-gear-teeth ${spin}" d="M-5 -24h10l2 6h8l5 -4l7 7l-4 5v8l6 2v10l-6 2v8l4 5l-7 7l-5 -4h-8l-2 6h-10l-2 -6h-8l-5 4l-7 -7l4 -5v-8l-6 -2v-10l6 -2v-8l-4 -5l7 -7l5 4h8z" fill="currentColor" stroke="#1B1640" stroke-width="2" style="transform-origin:0 0"/>
                <circle class="help-gear-hub" cx="0" cy="0" r="10" fill="#fff" stroke="#1B1640" stroke-width="2"/>
                <text class="help-gear-label" x="0" y="4" font-size="11" font-weight="900" text-anchor="middle" fill="#1B1640">${lbl}</text>
            </svg>`;
            return h('div.h-scene',
                h('div.h-row.gears-demo-row',
                    h('div.help-gear-wrap', {html: cogSvg('gear-a', 'spin-cw', 'A')}),
                    h('div.help-gear-wrap', {html: cogSvg('gear-b', s >= 1 ? 'spin-ccw' : '', 'B')}),
                    h('div.help-gear-wrap', {html: cogSvg('gear-c' + (s === 2 ? ' ok' : ''), s === 2 ? 'spin-cw' : '', 'C')})),
                h('div.h-row',
                    chip(h, 'A: ↻', '.focus'),
                    chip(h, 'B: ↺', s >= 1 ? '.focus.pop' : '.dim'),
                    chip(h, 'C: ↻', s === 2 ? '.ok.pop' : (s === 1 ? '.pulse' : '.dim'))));
        },
        lasers(s, h) {
            const grid = [
                ['🔦', s >= 1 ? '—' : '', '\\'],
                ['', '', s >= 1 ? '|' : ''],
                ['⭐', '', '💎'],
            ];
            return h('div.h-scene',
                h('div.h-board3', grid.flatMap((row, r) => row.map((c, col) => {
                    const isTarget = r === 2 && col === 2;
                    const isMirror = r === 0 && col === 2;
                    const isEmitter = r === 0 && col === 0;
                    const cls = (isTarget && s === 2 ? '.ok.pop' : (isMirror && s === 1 ? '.focus.pulse' : '')) + (isEmitter ? '.laser-emitter-cell.dir-right' : '');
                    if (c === '/' || c === '\\') {
                        return h('span.h-cell.laser-mirror-cell' + cls, {html: (window.ThinkHopUI && window.ThinkHopUI.mirrorSvg) ? window.ThinkHopUI.mirrorSvg(c, s >= 1) : c});
                    }
                    if (isEmitter) {
                        return h('span.h-cell' + cls, h('span.laser-torch', c));
                    }
                    return h('span.h-cell' + cls, c);
                }))),
                h('div.h-row',
                    chip(h, '🔦 ➔', '.focus'),
                    chip(h, '\\ ➔ ⬇️', s >= 1 ? '.focus.pop' : '.dim'),
                    chip(h, '💎 ✓', s === 2 ? '.ok.pop' : (s === 1 ? '.pulse' : '.dim'))));
        },
        cubes(s, h) {
            const net = [
                ['', '🍎', '', ''],
                ['🍌', '🍇', '🍓', '🍊'],
                ['', '🥝', '', ''],
            ];
            return h('div.h-scene',
                h('div.cube-net-demo', net.map(row => h('div.cube-net-row', row.map(c => {
                    if (!c) return h('span.cube-net-empty');
                    const isFocus = (c === '🍌' || c === '🍓') && s >= 1;
                    const isOk = c === '🍓' && s === 2;
                    return h('span.cube-net-cell' + (isOk ? '.ok.pop' : (isFocus ? '.focus.pulse' : '')), c);
                })))),
                h('div.h-row',
                    chip(h, '🍌 ↔ ?', '.focus'),
                    chip(h, '🍌 · 🍇 · 🍓', s >= 1 ? '.focus.pop' : '.dim'),
                    chip(h, '🍌 ↔ 🍓 ✓', s === 2 ? '.ok.pop' : '.dim')));
        },
        pipes(s, h) {
            return h('div.h-scene',
                h('div.pipes-demo-stage',
                    h('div.pipe-tank-demo.tank-a' + (s === 0 ? '.focus' : ''), h('span.tank-lbl', 'A'), h('span.tank-water', '💧')),
                    h('div.pipe-connector-demo',
                        chip(h, 'A ➔ B: ✓', s >= 1 ? '.ok' : '.focus'),
                        chip(h, 'A ➔ C: 🚫', '.bad')),
                    h('div.h-row',
                        h('div.pipe-tank-demo.tank-b' + (s === 2 ? '.ok.pop' : (s === 1 ? '.focus' : '')), h('span.tank-lbl', 'B'), s >= 1 ? h('span.tank-water', '💧💧') : null),
                        h('div.pipe-tank-demo.tank-c.dim', h('span.tank-lbl', 'C')))),
                h('div.h-row',
                    chip(h, '1) A ➔ B', s === 1 ? '.focus.pop' : '.dim'),
                    chip(h, '2) B = 100% ✓', s === 2 ? '.ok.pop' : '.dim')));
        },
        mobiles(s, h) {
            return h('div.h-scene',
                h('div.h-row',
                    chip(h, '×1 ⚖️ ×2', '.focus'),
                    chip(h, s >= 1 ? '6 × 1 = 6' : '6 × 1 = ?', s >= 1 ? '.ok' : '.dim'),
                    chip(h, s === 2 ? '6 ÷ 2 = 3 ✓' : '? × 2 = 6', s === 2 ? '.ok.pop' : '.focus')),
                h('div.h-row',
                    h('div.scale-visual',
                        h('div.scale-beam-bar' + (s === 2 ? '' : '.tilt-left'),
                            h('div.scale-pan.pan-left', chip(h, '6 kg', '.ok')),
                            h('div.scale-pan.pan-right', chip(h, s === 2 ? '3 kg ✓' : '?', s === 2 ? '.ok.pop' : '.focus'))))));
        },
        origami(s, h) {
            return h('div.h-scene',
                h('div.h-row',
                    chip(h, '1) ➡️', s >= 0 ? '.focus' : '.dim'),
                    chip(h, '2) ⬇️', s >= 1 ? '.focus' : '.dim'),
                    chip(h, '3) ✂️ ●', s >= 1 ? '.target' : '.dim'),
                    chip(h, '4) ● ×4 ✓', s === 2 ? '.ok.pop' : '.dim')),
                h('div.h-row',
                    h('div.origami-mini-grid',
                        Array.from({length: 16}, (_, i) => {
                            const isCenter = [5, 6, 9, 10].includes(i);
                            return h('span.origami-mini-cell' + (isCenter && s >= 1 ? '.hole' + (s === 2 ? '.ok.pop' : '') : ''));
                        }))));
        },
    };

    const has = cat => Boolean(SCENES[cat]);

    function renderStep(cat, step, h) {
        const fn = SCENES[cat] || SCENES.sign;
        return fn(step, h, t);
    }

    /**
     * Opens the guided, animated help modal for `cat`.
     * opts: {onopen, onclose, paused}
     */
    function open(cat, opts) {
        opts = opts || {};
        const {h, icon, modal} = window.ThinkHopUI;
        const host = M.hostOf(cat);
        const STEPS = 3;
        let step = 0;
        let timer = null;
        let closed = false;

        if (opts.onopen) opts.onopen();

        const stageWrap = h('div.help-stage');
        const stepLabel = h('span.help-step-num');
        const mascotSlot = h('span.help-mascot');
        const captionText = h('p.help-caption');
        const dotsWrap = h('div.help-dots');

        function stopAuto() {
            if (timer !== null) {
                if (typeof clearTimeout === 'function') clearTimeout(timer);
                timer = null;
            }
        }

        function scheduleNext() {
            stopAuto();
            if (!closed && step < STEPS - 1) {
                timer = setTimeout(() => {
                    timer = null;
                    if (!closed) goTo(step + 1, true);
                }, 3100);
            }
        }

        function goTo(idx, auto) {
            step = (idx + STEPS) % STEPS;
            const mood = step === STEPS - 1 ? 'happy' : 'think';
            mascotSlot.replaceChildren(h('span.mascot.small' + (step === STEPS - 1 ? '.hop' : ''), {
                html: M.svg(host, mood),
                title: host[0].toUpperCase() + host.slice(1),
            }));
            stepLabel.textContent = t('help_step', {n: step + 1, m: STEPS});
            captionText.replaceChildren();
            if (window.ThinkHopTwemoji) {
                window.ThinkHopTwemoji.appendWithTwemoji(captionText, t(`help_${cat}_s${step + 1}`));
            } else {
                captionText.textContent = t(`help_${cat}_s${step + 1}`);
            }
            stageWrap.replaceChildren(renderStep(cat, step, h));
            dotsWrap.replaceChildren(...Array.from({length: STEPS}, (_, i) => h('button.help-dot' + (i === step ? '.active' : '') + (i < step ? '.done' : ''), {
                type: 'button',
                'aria-label': t('help_step', {n: i + 1, m: STEPS}),
                onclick: () => {
                    stopAuto();
                    goTo(i, false);
                },
            }, String(i + 1), i === step && auto ? h('i.help-dot-bar') : null)));

            if (auto) scheduleNext();
        }

        const content = h('div.help-modal',
            h('div.help-head',
                h('div.help-titles',
                    h('span.tag', t('how_to_play')),
                    opts.paused ? h('span.tag.help-pause-tag', icon('pause'), t('help_paused')) : null,
                    h('h3', t('cat_' + cat))),
                h('button.icon-btn', {
                    type: 'button',
                    title: t('close'),
                    'aria-label': t('close'),
                    onclick: () => m.close(),
                }, icon('close'))),
            h('p.help-goal', t(`help_${cat}_goal`)),
            stageWrap,
            h('div.help-guide-bar',
                mascotSlot,
                h('div.bubble.help-bubble',
                    h('b', host[0].toUpperCase() + host.slice(1), ' · ', stepLabel),
                    captionText)),
            h('div.help-controls',
                h('button.btn.btn-soft.small', {
                    type: 'button',
                    title: t('help_prev'),
                    'aria-label': t('help_prev'),
                    onclick: () => {
                        stopAuto();
                        goTo(step - 1, false);
                    },
                }, icon('back')),
                dotsWrap,
                h('button.btn.btn-soft.small', {
                    type: 'button',
                    title: t('help_next'),
                    'aria-label': t('help_next'),
                    onclick: () => {
                        stopAuto();
                        goTo(step + 1, false);
                    },
                }, icon('next')),
                h('button.btn.btn-soft.small', {
                    type: 'button',
                    title: t('help_replay'),
                    'aria-label': t('help_replay'),
                    onclick: () => goTo(0, true),
                }, icon('replay'))),
            h('div.help-tip',
                icon('bulb'),
                h('span', M.tip(cat))),
            h('div.actions',
                h('button.btn.btn-primary.btn-block', {
                    type: 'button',
                    onclick: () => m.close(),
                }, t('help_got_it'))));

        const m = modal(content, {
            onclose: () => {
                closed = true;
                stopAuto();
                if (opts.onclose) opts.onclose();
            },
        });

        goTo(0, true);
        return m;
    }

    window.ThinkHopHelp = {has, renderStep, open};
})();
