/*
 * ThinkHop — Graphical step-by-step explanation system.
 * Renders rich visual deduction steps (with icons, mini-diagrams, arrows and winner badges)
 * when a question is answered (right or wrong) in the feedback card.
 *
 * Supports arbitrary N-step granular explanations (3, 4, 5, 6, 7+ steps) so every puzzle
 * category can clearly demonstrate the complete chain of deduction without artificial truncation.
 */
(function (root) {
    'use strict';

    const I = root.ThinkHopI18n;
    if (!I) return;
    const t = I.t;

    const TRANSLATIONS = {
        it: {
            sol_title: 'Come ci si arriva · Passo dopo passo',
            sol_s1: '1. Punto di partenza',
            sol_s2: '2. Passaggio chiave',
            sol_s3: '3. Soluzione finale',
            sol_step_start: 'Punto di partenza',
            sol_step_deduction: 'Passaggio logico',
            sol_step_final: 'Soluzione finale',
            sol_target_cell: 'Casella cercata: Riga {r}, Colonna {c}',
            sol_elim_row_col: 'Candidati esclusi (già presenti nella riga o colonna):',
            sol_single_cand: 'Unico simbolo superstite:',
            sol_gear_start_title: 'Ruota motrice iniziale',
            sol_gear_start: 'Ruota {cog} ({dir})',
            sol_gear_contact: 'Contatto {prev} ➔ {next}',
            sol_gear_step_note: '{prev} ruota in senso {prevDir}: a contatto ingrana {next} invertendone il senso in {nextDir}',
            sol_gear_target_reached: 'Raggiunto l’ingranaggio finale richiesto!',
            sol_gear_rule_note: 'Regola di trasmissione: ogni coppia di ingranaggi a contatto inverte il senso di rotazione (↻ ➔ ↺ e viceversa).',
            sol_gear_flip: 'A ogni contatto di denti il senso si inverte:',
            sol_gear_res: 'La ruota {cog} gira in senso {dir}',
            sol_gear_jam: 'Conflitto: ruote collegate tentano di girare nello stesso verso!',
            sol_gear_jam_res: 'Il sistema di ingranaggi è bloccato 🚫',
            sol_laser_source_title: 'Sorgente del fascio',
            sol_laser_start: 'Torcia laser da ({x}, {y}) punta verso {dir}',
            sol_laser_mirror_step: 'Deviazione {n}: specchio a ({x}, {y})',
            sol_laser_deflect_note: 'Lo specchio {m} devia il fascio a 90° verso {dir}',
            sol_laser_bounce: 'Rimbalzi a 90° sugli specchi:',
            sol_laser_hit: 'Il fascio colpisce {target} dopo {n} deviazioni',
            sol_cube_rule_title: 'Regola facce opposte',
            sol_cube_rule: 'Nello sviluppo piano, le facce opposte distano di 1 casella:',
            sol_cube_target_find: 'Identifica la faccia cercata',
            sol_cube_jump_step: 'Salto della casella intermedia',
            sol_cube_match: '{a} salta {mid} e si allinea con {b}',
            sol_cube_res: '{a} e {b} sono facce opposte',
            sol_cube_pairs_title: 'Tutte le coppie di facce opposte',
            sol_cube_discard_title: 'Esclusione viste non valide',
            sol_cube_discard_note: 'Le terne che contengono due facce opposte sono fisicamente impossibili.',
            sol_cube_view_rule: 'Tre facce possono essere adiacenti sul cubo solo se nessuna coppia è opposta nello sviluppo:',
            sol_cube_view_step: 'La terna corretta è formata da facce tra loro compatibili (nessuna coppia opposta):',
            sol_cube_view_res: 'Vista valida del cubo: {x}',
            sol_pipe_tank_source: 'Serbatoio sorgente',
            sol_pipe_start: 'L’acqua scende nel serbatoio {tank}',
            sol_pipe_blocked_title: 'Condotti ostruiti',
            sol_pipe_blocked_note: 'I condotti ostruiti (🚫) bloccano l’acqua: i serbatoi a valle rimangono asciutti.',
            sol_pipe_flow_title: 'Deflusso per gravità',
            sol_pipe_flow: 'Condotti verso i serbatoi inferiori:',
            sol_pipe_res: 'Il serbatoio {tank} si riempie per primo al 100%',
            sol_mobile_torque: 'Legge della leva: Momento a sinistra = Momento a destra',
            sol_mobile_calc: 'Calcolo dei bracci e contrappesi:',
            sol_mobile_calc_beam: 'Equilibrio asta {n}',
            sol_mobile_arm: 'Braccio: ×{l} vs ×{r}',
            sol_mobile_res: 'Il peso esatto per equilibrare la scultura è {x}',
            sol_origami_fold_n: '{n}ª piegatura ({fold})',
            sol_origami_fold_note: 'Piegatura {n}: dimezza la superficie e sovrappone gli strati di carta.',
            sol_origami_punch_title: 'Perforazione ✂️',
            sol_origami_unfold_title: 'Riapertura a ritroso',
            sol_origami_fold: 'Sequenza di piegatura e punto del foro:',
            sol_origami_reflect: 'Riapertura a ritroso: riflessioni simmetriche',
            sol_origami_res: 'La figura finale con i fori aperti è {x}',
            sol_origami_step2_chip: 'Simmetrie a ritroso: {n} fori totali',
            sol_holes: 'fori',
            sol_hole: 'foro',
            sol_scale_eq1: 'Prima bilancia in equilibrio:',
            sol_scale_sub: 'Sostituzione algebrica nella seconda:',
            sol_scale_res: 'Equilibrio finale: {x}',
            sol_cup_start_chip: 'Inizio: {seq}',
            sol_cup_start: 'Disposizione iniziale bicchieri:',
            sol_cup_swap: 'Sequenza degli scambi:',
            sol_cup_swap_step: '{n}ª mossa: {move}',
            sol_cup_state_after: 'Nuova disposizione:',
            sol_cup_res: 'La pallina termina sotto il bicchiere {n}',
            sol_grid_target: 'Casella cercata',
            sol_grid_row: 'Simboli presenti nella riga {r}',
            sol_grid_row_note: 'Ciascun simbolo può apparire al massimo una sola volta per riga.',
            sol_grid_col: 'Simboli presenti nella colonna {c}',
            sol_grid_col_note: 'Ciascun simbolo può apparire al massimo una sola volta per colonna.',
            sol_grid_all_elim: 'Tutti i candidati eliminati',
            sol_compass_start: 'Orientamento iniziale: {dir}',
            sol_compass_turn: 'Sequenza delle rotazioni:',
            sol_compass_turn_step: 'Virata {n}: {turn}',
            sol_compass_res: 'Orientamento finale: {dir}',
            sol_walk_start: 'Posizione di partenza: 🐢',
            sol_walk_step: 'Passo {n}: verso {dir}',
            sol_walk_moves: 'Sequenza dei passi sulla mappa:',
            sol_walk_res: 'Casella di arrivo: {ans}',
            sol_lock_clue_n: 'Indizio {n}',
            sol_lock_clue1: 'Analisi del primo indizio:',
            sol_lock_clue2: 'Analisi del secondo indizio:',
            sol_lock_deduction: 'Incrocio degli indizi ed eliminazione',
            sol_lock_res: 'Codice esatto decifrato: {code}',
            sol_seat_start: 'Punto fermo a tavola:',
            sol_seat_step: 'Posizionamento in base ai vincoli:',
            sol_seat_res: 'Al posto richiesto siede: {name}',
            sol_box_rule: 'Verifica veridicità dei cartelli:',
            sol_box_step: 'Confronto ipotesi sui forzieri:',
            sol_box_res: 'Il tesoro si trova nel forziere: {box}',
            sol_pt_repeat: 'Individua il blocco ripetuto nel ciclo:',
            sol_pt_step: 'Calcola la posizione del punto interrogativo:',
            sol_pt_res: 'Simbolo che completa la serie: {ans}',
            sol_seq_step: 'Passo costante tra numeri adiacenti:',
            sol_seq_calc: 'Applica la regola all’ultimo valore:',
            sol_seq_res: 'Numero successivo: {ans}',
            sol_sym_eq1: 'Trova il valore del primo simbolo:',
            sol_sym_sub: 'Sostituisci per ricavare il secondo:',
            sol_sym_res: 'Valore finale: {ans}',
            sol_mirror_rule: 'Mossa applicata alla griglia: {move}',
            sol_mirror_pos: 'La casella bersaglio riceve la cella riflessa:',
            sol_mirror_refl: '🪞 Riflessione',
            sol_target_pos: 'Casella bersaglio',
            sol_mirror_res: 'Simbolo sulla casella: {ans}',
            sol_lamp_start: 'Stato iniziale delle lampadine:',
            sol_lamp_sw: 'Interruttori azionati in sequenza:',
            sol_lamp_res: 'Configurazione finale risultante: {ans}',
            sol_alch_base: 'Formula di conversione alchemica:',
            sol_alch_step: 'Calcolo proporzionale degli ingredienti:',
            sol_alch_res: 'Risultato finale calcolato: {ans}',
            sol_susp_traits: 'Tratti identificativi richiesti dagli indizi:',
            sol_susp_elim: 'Eliminazione dei profili incongruenti:',
            sol_susp_res: 'L’unico profilo compatibile è: {ans}',
            sol_liar_rule: 'Analisi dichiarazioni veritieri e bugiardi:',
            sol_liar_step: 'Verifica coerenza delle affermazioni:',
            sol_liar_res: 'Composizione corretta: {ans}',
            sol_odd_rule: 'Tre elementi condividono la proprietà:',
            sol_odd_step: 'Verifica dell’elemento discordante:',
            sol_odd_res: 'L’intruso che rompe la regola è: {ans}',
            sol_days_start: 'Giorno di riferimento iniziale:',
            sol_days_step: 'Calcolo del salto temporale netto:',
            sol_days_res: 'Giorno risultante: {ans}',
            sol_sign_eq: 'Equazione da completare:',
            sol_sign_test: 'Verifica delle operazioni candidate:',
            sol_sign_res: 'Operazione corretta: {ans}',
            sol_gen_premise: 'Premessa e dati del quesito:',
            sol_gen_logic: 'Deduzione logica passo dopo passo:',
            sol_gen_res: 'Risposta esatta: {ans}',
            sol_riddle_premise: 'Testo dell’indovinello:',
            sol_riddle_logic: 'Spiegazione e ragionamento logico:',
            sol_skip: 'salta',
            sol_find: 'trova',
            sol_laser_torch: 'Torcia',
            cw: 'Orario (↻)',
            ccw: 'Antiorario (↺)',
        },
        en: {
            sol_title: 'How to get there · Step by step',
            sol_s1: '1. Starting point',
            sol_s2: '2. Key deduction',
            sol_s3: '3. Final solution',
            sol_step_start: 'Starting point',
            sol_step_deduction: 'Logical step',
            sol_step_final: 'Final solution',
            sol_target_cell: 'Target cell to find: Row {r}, Column {c}',
            sol_elim_row_col: 'Excluded candidates (already in row or column):',
            sol_single_cand: 'Single candidate left:',
            sol_gear_start_title: 'Initial driving gear',
            sol_gear_start: 'Gear {cog} ({dir})',
            sol_gear_contact: 'Contact {prev} ➔ {next}',
            sol_gear_step_note: '{prev} turns {prevDir}: meshing with {next} reverses rotation to {nextDir}',
            sol_gear_target_reached: 'Target gear reached!',
            sol_gear_rule_note: 'Transmission rule: each pair of meshed gears reverses rotation direction (↻ ➔ ↺ and vice versa).',
            sol_gear_flip: 'Every meshed tooth reverses rotation direction:',
            sol_gear_res: 'Gear {cog} turns {dir}',
            sol_gear_jam: 'Conflict: connected gears try to turn in the same direction!',
            sol_gear_jam_res: 'The gear system is jammed 🚫',
            sol_laser_source_title: 'Laser source',
            sol_laser_start: 'Laser flashlight at ({x}, {y}) points {dir}',
            sol_laser_mirror_step: 'Bounce {n}: mirror at ({x}, {y})',
            sol_laser_deflect_note: 'The mirror {m} deflects beam 90° towards {dir}',
            sol_laser_bounce: '90° reflections off mirrors:',
            sol_laser_hit: 'The beam hits {target} after {n} turns',
            sol_cube_rule_title: 'Opposite faces rule',
            sol_cube_rule: 'On a flat net, opposite faces are 1 square apart:',
            sol_cube_target_find: 'Locate target face',
            sol_cube_jump_step: 'Skip intermediate square',
            sol_cube_match: '{a} skips {mid} and aligns with {b}',
            sol_cube_res: '{a} and {b} are opposite faces',
            sol_cube_pairs_title: 'All opposite face pairs',
            sol_cube_discard_title: 'Eliminate invalid views',
            sol_cube_discard_note: 'Triplets containing two opposite faces are physically impossible.',
            sol_cube_view_rule: 'Three faces can touch only if no pair is opposite on the net:',
            sol_cube_view_step: 'The valid triplet contains mutually compatible faces (no opposite pair):',
            sol_cube_view_res: 'Valid cube view: {x}',
            sol_pipe_tank_source: 'Source tank',
            sol_pipe_start: 'Water pours into tank {tank}',
            sol_pipe_blocked_title: 'Blocked pipes',
            sol_pipe_blocked_note: 'Blocked pipes (🚫) stop water: downstream tanks stay dry.',
            sol_pipe_flow_title: 'Gravity flow',
            sol_pipe_flow: 'Pipes toward lower tanks:',
            sol_pipe_res: 'Tank {tank} fills to 100% first',
            sol_mobile_torque: 'Lever balance: Left torque = Right torque',
            sol_mobile_calc: 'Calculating lever arms and counterweights:',
            sol_mobile_calc_beam: 'Balance beam {n}',
            sol_mobile_arm: 'Arm: ×{l} vs ×{r}',
            sol_mobile_res: 'The exact weight needed to balance the mobile is {x}',
            sol_origami_fold_n: 'Fold {n} ({fold})',
            sol_origami_fold_note: 'Fold {n}: halves surface area and doubles sheet layers.',
            sol_origami_punch_title: 'Hole punch ✂️',
            sol_origami_unfold_title: 'Unfolding in reverse',
            sol_origami_fold: 'Folding sequence and punch point:',
            sol_origami_reflect: 'Reverse unfolding: symmetrical reflections',
            sol_origami_res: 'Final pattern with opened holes is {x}',
            sol_origami_step2_chip: 'Symmetries in reverse: {n} holes total',
            sol_holes: 'holes',
            sol_hole: 'hole',
            sol_scale_eq1: 'First balance in equilibrium:',
            sol_scale_sub: 'Algebraic substitution in the second:',
            sol_scale_res: 'Final equilibrium: {x}',
            sol_cup_start_chip: 'Start: {seq}',
            sol_cup_start: 'Initial cup arrangement:',
            sol_cup_swap: 'Swap sequence:',
            sol_cup_swap_step: 'Move {n}: {move}',
            sol_cup_state_after: 'New order:',
            sol_cup_res: 'The ball ends under cup {n}',
            sol_grid_target: 'Target cell',
            sol_grid_row: 'Symbols in row {r}',
            sol_grid_row_note: 'Each symbol can appear at most once per row.',
            sol_grid_col: 'Symbols in column {c}',
            sol_grid_col_note: 'Each symbol can appear at most once per column.',
            sol_grid_all_elim: 'All eliminated candidates',
            sol_compass_start: 'Initial orientation: {dir}',
            sol_compass_turn: 'Turn sequence:',
            sol_compass_turn_step: 'Turn {n}: {turn}',
            sol_compass_res: 'Final orientation: {dir}',
            sol_walk_start: 'Starting position: 🐢',
            sol_walk_step: 'Step {n}: towards {dir}',
            sol_walk_moves: 'Steps on the map:',
            sol_walk_res: 'Arrival cell: {ans}',
            sol_lock_clue_n: 'Clue {n}',
            sol_lock_clue1: 'Analyzing clue 1:',
            sol_lock_clue2: 'Analyzing clue 2:',
            sol_lock_deduction: 'Cross-clue deduction and elimination',
            sol_lock_res: 'Decrypted secret code: {code}',
            sol_seat_start: 'Fixed point at the table:',
            sol_seat_step: 'Placement by constraints:',
            sol_seat_res: 'Seated at requested place: {name}',
            sol_box_rule: 'Verify chest statement truth values:',
            sol_box_step: 'Comparing chest hypotheses:',
            sol_box_res: 'The treasure is in chest: {box}',
            sol_pt_repeat: 'Find the repeating cycle pattern:',
            sol_pt_step: 'Compute the question mark position:',
            sol_pt_res: 'Symbol completing the series: {ans}',
            sol_seq_step: 'Constant step between neighbors:',
            sol_seq_calc: 'Apply rule to last term:',
            sol_seq_res: 'Next number: {ans}',
            sol_sym_eq1: 'Find first symbol value:',
            sol_sym_sub: 'Substitute to solve second:',
            sol_sym_res: 'Final value: {ans}',
            sol_mirror_rule: 'Transformation applied: {move}',
            sol_mirror_pos: 'Target cell receives reflected cell:',
            sol_mirror_refl: '🪞 Reflection',
            sol_target_pos: 'Target cell',
            sol_mirror_res: 'Symbol on cell: {ans}',
            sol_lamp_start: 'Initial lamp states:',
            sol_lamp_sw: 'Switches toggled in sequence:',
            sol_lamp_res: 'Resulting configuration: {ans}',
            sol_alch_base: 'Alchemy conversion formula:',
            sol_alch_step: 'Proportional ingredient calculation:',
            sol_alch_res: 'Final calculated yield: {ans}',
            sol_susp_traits: 'Required suspect traits from clues:',
            sol_susp_elim: 'Eliminating inconsistent profiles:',
            sol_susp_res: 'Only matching suspect: {ans}',
            sol_liar_rule: 'Analyze truth-tellers and liars:',
            sol_liar_step: 'Checking statement consistency:',
            sol_liar_res: 'Correct arrangement: {ans}',
            sol_odd_rule: 'Three items share the property:',
            sol_odd_step: 'Check the outlier:',
            sol_odd_res: 'Odd one out breaking the rule: {ans}',
            sol_days_start: 'Starting reference day:',
            sol_days_step: 'Compute net day shift:',
            sol_days_res: 'Resulting day: {ans}',
            sol_sign_eq: 'Equation to complete:',
            sol_sign_test: 'Testing candidate operations:',
            sol_sign_res: 'Correct operation: {ans}',
            sol_gen_premise: 'Question premises and given data:',
            sol_gen_logic: 'Step-by-step logical deduction:',
            sol_gen_res: 'Correct answer: {ans}',
            sol_riddle_premise: 'Riddle question:',
            sol_riddle_logic: 'Reasoning and explanation:',
            sol_skip: 'skip',
            sol_find: 'find',
            sol_laser_torch: 'Torch',
            cw: 'Clockwise (↻)',
            ccw: 'Counterclockwise (↺)',
        },
        es: {
            sol_title: 'Cómo se llega · Paso a paso',
            sol_s1: '1. Punto de partida',
            sol_s2: '2. Deducción clave',
            sol_s3: '3. Solución final',
            sol_step_start: 'Punto de partida',
            sol_step_deduction: 'Paso lógico',
            sol_step_final: 'Solución final',
            sol_target_cell: 'Casilla buscada: Fila {r}, Columna {c}',
            sol_elim_row_col: 'Candidatos excluidos (ya en fila o columna):',
            sol_single_cand: 'Único símbolo superviviente:',
            sol_gear_start_title: 'Engranaje motriz inicial',
            sol_gear_start: 'Engranaje {cog} ({dir})',
            sol_gear_contact: 'Contacto {prev} ➔ {next}',
            sol_gear_step_note: '{prev} gira en sentido {prevDir}: al engranar con {next} invierte el sentido a {nextDir}',
            sol_gear_target_reached: '¡Engranaje final alcanzado!',
            sol_gear_rule_note: 'Regla de transmisión: cada par de engranajes en contacto invierte el sentido de rotación (↻ ➔ ↺ y viceversa).',
            sol_gear_flip: 'En cada contacto de dientes el sentido se invierte:',
            sol_gear_res: 'El engranaje {cog} gira en sentido {dir}',
            sol_gear_jam: '¡Conflicto: engranajes conectados intentan girar en el mismo sentido!',
            sol_gear_jam_res: 'El sistema de engranajes está bloqueado 🚫',
            sol_laser_source_title: 'Fuente del láser',
            sol_laser_start: 'Linterna láser desde ({x}, {y}) apunta hacia {dir}',
            sol_laser_mirror_step: 'Rebote {n}: espejo en ({x}, {y})',
            sol_laser_deflect_note: 'El espejo {m} desvía el rayo a 90° hacia {dir}',
            sol_laser_bounce: 'Rebotes a 90° en los espejos:',
            sol_laser_hit: 'El haz alcanza {target} tras {n} desvíos',
            sol_cube_rule_title: 'Regla de caras opuestas',
            sol_cube_rule: 'En el desarrollo plano, las caras opuestas distan 1 casilla:',
            sol_cube_target_find: 'Localiza la cara buscada',
            sol_cube_jump_step: 'Salto de casilla intermedia',
            sol_cube_match: '{a} salta {mid} y se alinea con {b}',
            sol_cube_res: '{a} y {b} son caras opuestas',
            sol_cube_pairs_title: 'Todos los pares de caras opuestas',
            sol_cube_discard_title: 'Descartar vistas no válidas',
            sol_cube_discard_note: 'Las ternas que contienen dos caras opuestas son físicamente imposibles.',
            sol_cube_view_rule: 'Tres caras solo pueden ser adyacentes si ninguna pareja es opuesta:',
            sol_cube_view_step: 'La terna válida contiene caras compatibles (ningún par opuesto):',
            sol_cube_view_res: 'Vista válida del cubo: {x}',
            sol_pipe_tank_source: 'Depósito de origen',
            sol_pipe_start: 'El agua baja al depósito {tank}',
            sol_pipe_blocked_title: 'Tuberías bloqueadas',
            sol_pipe_blocked_note: 'Las tuberías bloqueadas (🚫) impiden el paso: los depósitos aguas abajo no se llenan.',
            sol_pipe_flow_title: 'Flujo por gravedad',
            sol_pipe_flow: 'Conductos hacia depósitos inferiores:',
            sol_pipe_res: 'El depósito {tank} se llena primero al 100%',
            sol_mobile_torque: 'Ley de la palanca: Momento a la izquierda = Momento a la derecha',
            sol_mobile_calc: 'Cálculo de brazos y contrapesos:',
            sol_mobile_calc_beam: 'Equilibrio de barra {n}',
            sol_mobile_arm: 'Brazo: ×{l} vs ×{r}',
            sol_mobile_res: 'El peso exacto para equilibrar la escultura es {x}',
            sol_origami_fold_n: 'Pliegue {n} ({fold})',
            sol_origami_fold_note: 'Pliegue {n}: reduce a la mitad el área y superpone las capas de papel.',
            sol_origami_punch_title: 'Perforación ✂️',
            sol_origami_unfold_title: 'Desplegado en reversa',
            sol_origami_fold: 'Secuencia de doblado y punto del agujero:',
            sol_origami_reflect: 'Reapertura hacia atrás: reflexiones simétricas',
            sol_origami_res: 'La figura final con agujeros abiertos es {x}',
            sol_origami_step2_chip: 'Simetrías inversas: {n} agujeros en total',
            sol_holes: 'agujeros',
            sol_hole: 'agujero',
            sol_scale_eq1: 'Primera balanza en equilibrio:',
            sol_scale_sub: 'Sustitución algebraica en la segunda:',
            sol_scale_res: 'Equilibrio final: {x}',
            sol_cup_start_chip: 'Inicio: {seq}',
            sol_cup_start: 'Disposición inicial de vasos:',
            sol_cup_swap: 'Secuencia de intercambios:',
            sol_cup_swap_step: 'Movimiento {n}: {move}',
            sol_cup_state_after: 'Nuevo orden:',
            sol_cup_res: 'La bolita termina bajo el vaso {n}',
            sol_grid_target: 'Casilla buscada',
            sol_grid_row: 'Símbolos en fila {r}',
            sol_grid_row_note: 'Cada símbolo solo puede aparecer una vez por fila.',
            sol_grid_col: 'Símbolos en columna {c}',
            sol_grid_col_note: 'Cada símbolo solo puede aparecer una vez por columna.',
            sol_grid_all_elim: 'Todos los candidatos eliminados',
            sol_compass_start: 'Orientación inicial: {dir}',
            sol_compass_turn: 'Secuencia de giros:',
            sol_compass_turn_step: 'Giro {n}: {turn}',
            sol_compass_res: 'Orientación final: {dir}',
            sol_walk_start: 'Posición de partida: 🐢',
            sol_walk_step: 'Paso {n}: hacia {dir}',
            sol_walk_moves: 'Secuencia de pasos en el mapa:',
            sol_walk_res: 'Casilla de llegada: {ans}',
            sol_lock_clue_n: 'Pista {n}',
            sol_lock_clue1: 'Análisis de la primera pista:',
            sol_lock_clue2: 'Análisis de la segunda pista:',
            sol_lock_deduction: 'Cruce de pistas y eliminación',
            sol_lock_res: 'Código secreto descifrado: {code}',
            sol_seat_start: 'Punto fijo en la mesa:',
            sol_seat_step: 'Ubicación según restricciones:',
            sol_seat_res: 'En el asiento solicitado se sienta: {name}',
            sol_box_rule: 'Verificación de veracidad de carteles:',
            sol_box_step: 'Comparación de hipótesis de cofres:',
            sol_box_res: 'El tesoro está en el cofre: {box}',
            sol_pt_repeat: 'Identifica el bloque repetido del ciclo:',
            sol_pt_step: 'Calcula la posición del signo de interrogación:',
            sol_pt_res: 'Símbolo que completa la serie: {ans}',
            sol_seq_step: 'Paso constante entre números adyacentes:',
            sol_seq_calc: 'Aplica la regla al último valor:',
            sol_seq_res: 'Número siguiente: {ans}',
            sol_sym_eq1: 'Halla el valor del primer símbolo:',
            sol_sym_sub: 'Sustituye para hallar el segundo:',
            sol_sym_res: 'Valor final: {ans}',
            sol_mirror_rule: 'Transformación aplicada: {move}',
            sol_mirror_pos: 'La casilla objetivo recibe la celda reflejada:',
            sol_mirror_refl: '🪞 Reflexión',
            sol_target_pos: 'Casilla objetivo',
            sol_mirror_res: 'Símbolo en la casilla: {ans}',
            sol_lamp_start: 'Estado inicial de bombillas:',
            sol_lamp_sw: 'Interruptores accionados en secuencia:',
            sol_lamp_res: 'Configuración final resultante: {ans}',
            sol_alch_base: 'Fórmula de conversión alquímica:',
            sol_alch_step: 'Cálculo proporcional de ingredientes:',
            sol_alch_res: 'Resultado final calculado: {ans}',
            sol_susp_traits: 'Rasgos del sospechoso según pistas:',
            sol_susp_elim: 'Eliminación de perfiles incongruentes:',
            sol_susp_res: 'Único perfil compatible: {ans}',
            sol_liar_rule: 'Análisis de veraces y mentirosos:',
            sol_liar_step: 'Comprobación de coherencia:',
            sol_liar_res: 'Composición correcta: {ans}',
            sol_odd_rule: 'Tres elementos comparten la propiedad:',
            sol_odd_step: 'Comprobación del elemento discordante:',
            sol_odd_res: 'El elemento que rompe la regla es: {ans}',
            sol_days_start: 'Día de referencia inicial:',
            sol_days_step: 'Cálculo del salto temporal neto:',
            sol_days_res: 'Día resultante: {ans}',
            sol_sign_eq: 'Ecuación a completar:',
            sol_sign_test: 'Comprobación de operaciones candidatas:',
            sol_sign_res: 'Operación correcta: {ans}',
            sol_gen_premise: 'Premisa y datos del enigma:',
            sol_gen_logic: 'Deducción lógica paso a paso:',
            sol_gen_res: 'Respuesta exacta: {ans}',
            sol_riddle_premise: 'Texto del acertijo:',
            sol_riddle_logic: 'Razonamiento y explicación lógica:',
            sol_skip: 'salta',
            sol_find: 'encuentra',
            sol_laser_torch: 'Linterna',
            cw: 'Horario (↻)',
            ccw: 'Antihorario (↺)',
        },
        fr: {
            sol_title: 'Comment y parvenir · Pas à pas',
            sol_s1: '1. Point de départ',
            sol_s2: '2. Déduction clé',
            sol_s3: '3. Solution finale',
            sol_step_start: 'Point de départ',
            sol_step_deduction: 'Étape logique',
            sol_step_final: 'Solution finale',
            sol_target_cell: 'Case recherchée : Ligne {r}, Colonne {c}',
            sol_elim_row_col: 'Candidats exclus (déjà présents sur ligne/colonne) :',
            sol_single_cand: 'Seul symbole restant :',
            sol_gear_start_title: 'Roue motrice initiale',
            sol_gear_start: 'Roue {cog} ({dir})',
            sol_gear_contact: 'Contact {prev} ➔ {next}',
            sol_gear_step_note: '{prev} tourne dans le sens {prevDir} : en engrenant avec {next}, elle inverse le sens en {nextDir}',
            sol_gear_target_reached: 'Roue finale atteinte !',
            sol_gear_rule_note: 'Règle d’engrenage : chaque contact inverse le sens de rotation (↻ ➔ ↺ et inversement).',
            sol_gear_flip: 'À chaque contact de dents le sens s’inverse :',
            sol_gear_res: 'La roue {cog} tourne dans le sens {dir}',
            sol_gear_jam: 'Conflit : deux roues connectées tentent de tourner dans le même sens !',
            sol_gear_jam_res: 'Le système d’engrenages est bloqué 🚫',
            sol_laser_source_title: 'Source du laser',
            sol_laser_start: 'Lampe laser en ({x}, {y}) pointe vers {dir}',
            sol_laser_mirror_step: 'Déviation {n} : miroir en ({x}, {y})',
            sol_laser_deflect_note: 'Le miroir {m} dévie le faisceau à 90° vers {dir}',
            sol_laser_bounce: 'Rebonds à 90° sur les miroirs :',
            sol_laser_hit: 'Le faisceau touche {target} après {n} déviations',
            sol_cube_rule_title: 'Règle des faces opposées',
            sol_cube_rule: 'Sur le patron à plat, les faces opposées sont distantes d’1 case :',
            sol_cube_target_find: 'Localiser la face recherchée',
            sol_cube_jump_step: 'Saut de la case intermédiaire',
            sol_cube_match: '{a} saute {mid} et s’aligne avec {b}',
            sol_cube_res: '{a} et {b} sont des faces opposées',
            sol_cube_pairs_title: 'Toutes les paires de faces opposées',
            sol_cube_discard_title: 'Élimination des vues invalides',
            sol_cube_discard_note: 'Les triplets contenant deux faces opposées sont physiquement impossibles.',
            sol_cube_view_rule: 'Trois faces ne peuvent être adjacentes que si aucune paire n’est opposée :',
            sol_cube_view_step: 'Le triplet valide contient des faces compatibles (aucune paire opposée) :',
            sol_cube_view_res: 'Vue valide du cube : {x}',
            sol_pipe_tank_source: 'Réservoir source',
            sol_pipe_start: 'L’eau s’écoule dans le réservoir {tank}',
            sol_pipe_blocked_title: 'Tuyaux bouchés',
            sol_pipe_blocked_note: 'Les tuyaux bouchés (🚫) arrêtent l’eau : les réservoirs en aval restent secs.',
            sol_pipe_flow_title: 'Écoulement par gravité',
            sol_pipe_flow: 'Conduits vers les réservoirs inférieurs :',
            sol_pipe_res: 'Le réservoir {tank} se remplit en premier à 100%',
            sol_mobile_torque: 'Loi du levier : Moment à gauche = Moment à droite',
            sol_mobile_calc: 'Calcul des bras et contrepoids :',
            sol_mobile_calc_beam: 'Équilibre de la barre {n}',
            sol_mobile_arm: 'Bras : ×{l} vs ×{r}',
            sol_mobile_res: 'Le poids exact pour équilibrer la sculpture est {x}',
            sol_origami_fold_n: 'Pliage {n} ({fold})',
            sol_origami_fold_note: 'Pliage {n} : réduit la surface de moitié et superpose les couches de papier.',
            sol_origami_punch_title: 'Perforation ✂️',
            sol_origami_unfold_title: 'Dépliage à rebours',
            sol_origami_fold: 'Séquence de pliage et point du trou :',
            sol_origami_reflect: 'Dépliage à rebours : réflexions symétriques',
            sol_origami_res: 'La figure finale avec les trous ouverts est {x}',
            sol_origami_step2_chip: 'Symétries inverses : {n} trous au total',
            sol_holes: 'trous',
            sol_hole: 'trou',
            sol_scale_eq1: 'Première balance en équilibre :',
            sol_scale_sub: 'Substitution algébrique dans la seconde :',
            sol_scale_res: 'Équilibre final : {x}',
            sol_cup_start_chip: 'Départ : {seq}',
            sol_cup_start: 'Disposition initiale des gobelets :',
            sol_cup_swap: 'Séquence des échanges :',
            sol_cup_swap_step: 'Coup {n} : {move}',
            sol_cup_state_after: 'Nouvel ordre :',
            sol_cup_res: 'La bille finit sous le gobelet {n}',
            sol_grid_target: 'Case recherchée',
            sol_grid_row: 'Symboles sur la ligne {r}',
            sol_grid_row_note: 'Chaque symbole ne peut apparaître qu’une seule fois par ligne.',
            sol_grid_col: 'Symboles sur la colonne {c}',
            sol_grid_col_note: 'Chaque symbole ne peut apparaître qu’une seule fois par colonne.',
            sol_grid_all_elim: 'Tous les candidats éliminés',
            sol_compass_start: 'Orientation initiale : {dir}',
            sol_compass_turn: 'Séquence des rotations :',
            sol_compass_turn_step: 'Virage {n} : {turn}',
            sol_compass_res: 'Orientation finale : {dir}',
            sol_walk_start: 'Position de départ : 🐢',
            sol_walk_step: 'Étape {n} : vers {dir}',
            sol_walk_moves: 'Séquence des pas sur la carte :',
            sol_walk_res: 'Case d’arrivée : {ans}',
            sol_lock_clue_n: 'Indice {n}',
            sol_lock_clue1: 'Analyse du premier indice :',
            sol_lock_clue2: 'Analyse du deuxième indice :',
            sol_lock_deduction: 'Croisement des indices et élimination',
            sol_lock_res: 'Code secret décrypté : {code}',
            sol_seat_start: 'Point fixe à table :',
            sol_seat_step: 'Placement selon les contraintes :',
            sol_seat_res: 'À la place demandée s’assoit : {name}',
            sol_box_rule: 'Vérification de la véracité des coffres :',
            sol_box_step: 'Comparaison des hypothèses :',
            sol_box_res: 'Le trésor se trouve dans le coffre : {box}',
            sol_pt_repeat: 'Repérer le motif répété du cycle :',
            sol_pt_step: 'Calculer la position du point d’interrogation :',
            sol_pt_res: 'Symbole complétant la suite : {ans}',
            sol_seq_step: 'Pas constant entre voisins :',
            sol_seq_calc: 'Appliquer la règle au dernier terme :',
            sol_seq_res: 'Nombre suivant : {ans}',
            sol_sym_eq1: 'Trouver la valeur du premier symbole :',
            sol_sym_sub: 'Remplacer pour trouver le second :',
            sol_sym_res: 'Valeur finale : {ans}',
            sol_mirror_rule: 'Transformation appliquée : {move}',
            sol_mirror_pos: 'La case cible reçoit la cellule réfléchie :',
            sol_mirror_refl: '🪞 Réflexion',
            sol_target_pos: 'Case cible',
            sol_mirror_res: 'Symbole sur la case : {ans}',
            sol_lamp_start: 'État initial des lampes :',
            sol_lamp_sw: 'Interrupteurs activés en séquence :',
            sol_lamp_res: 'Configuration finale obtenue : {ans}',
            sol_alch_base: 'Formule de conversion alchimique :',
            sol_alch_step: 'Calcul proportionnel des ingrédients :',
            sol_alch_res: 'Résultat final calculé : {ans}',
            sol_susp_traits: 'Traits du suspect selon les indices :',
            sol_susp_elim: 'Élimination des profils incompatibles :',
            sol_susp_res: 'Seul suspect correspondant : {ans}',
            sol_liar_rule: 'Analyse véridiques et menteurs :',
            sol_liar_step: 'Vérification de la cohérence :',
            sol_liar_res: 'Répartition correcte : {ans}',
            sol_odd_rule: 'Trois éléments partagent la propriété :',
            sol_odd_step: 'Vérification de l’intrus :',
            sol_odd_res: 'L’intrus qui brise la règle est : {ans}',
            sol_days_start: 'Jour de référence initial :',
            sol_days_step: 'Calcul du décalage net :',
            sol_days_res: 'Jour résultant : {ans}',
            sol_sign_eq: 'Équation à compléter :',
            sol_sign_test: 'Vérification des opérations candidates :',
            sol_sign_res: 'Opération correcte : {ans}',
            sol_gen_premise: 'Énoncé et données du problème :',
            sol_gen_logic: 'Déduction logique pas à pas :',
            sol_gen_res: 'Bonne réponse : {ans}',
            sol_riddle_premise: 'Texte de l’énigme :',
            sol_riddle_logic: 'Raisonnement et explication logique :',
            sol_skip: 'saute',
            sol_find: 'trouve',
            sol_laser_torch: 'Lampe',
            cw: 'Sens horaire (↻)',
            ccw: 'Sens antihoraire (↺)',
        },
        de: {
            sol_title: 'Lösungsweg · Schritt für Schritt',
            sol_s1: '1. Ausgangspunkt',
            sol_s2: '2. Wichtiger Schritt',
            sol_s3: '3. Endergebnis',
            sol_step_start: 'Ausgangspunkt',
            sol_step_deduction: 'Logischer Schritt',
            sol_step_final: 'Endergebnis',
            sol_target_cell: 'Gesuchtes Feld: Zeile {r}, Spalte {c}',
            sol_elim_row_col: 'Ausgeschlossene Symbole (bereits in Zeile/Spalte):',
            sol_single_cand: 'Einzig verbleibendes Symbol:',
            sol_gear_start_title: 'Antriebsrad',
            sol_gear_start: 'Zahnrad {cog} ({dir})',
            sol_gear_contact: 'Kontakt {prev} ➔ {next}',
            sol_gear_step_note: '{prev} dreht {prevDir}: durch den Kontakt dreht {next} umgekehrt in {nextDir}',
            sol_gear_target_reached: 'Zielrad erreicht!',
            sol_gear_rule_note: 'Getrieberegel: Jedes ineinandergreifende Zahnradpaar kehrt die Drehrichtung um (↻ ➔ ↺ und umgekehrt).',
            sol_gear_flip: 'Bei jedem Zahnkontakt kehrt sich die Drehrichtung um:',
            sol_gear_res: 'Zahnrad {cog} dreht sich {dir}',
            sol_gear_jam: 'Konflikt: verbundene Zahnräder versuchen in dieselbe Richtung zu drehen!',
            sol_gear_jam_res: 'Das Getriebesystem ist blockiert 🚫',
            sol_laser_source_title: 'Laserquelle',
            sol_laser_start: 'Taschenlampe bei ({x}, {y}) leuchtet nach {dir}',
            sol_laser_mirror_step: 'Ablenkung {n}: Spiegel bei ({x}, {y})',
            sol_laser_deflect_note: 'Der Spiegel {m} lenkt den Strahl um 90° in Richtung {dir} ab',
            sol_laser_bounce: '90°-Reflexionen an den Spiegeln:',
            sol_laser_hit: 'Der Strahl trifft {target} nach {n} Umlenkungen',
            sol_cube_rule_title: 'Gegenüberliegende Flächen',
            sol_cube_rule: 'Im Würfelnetz haben gegenüberliegende Flächen 1 Feld Abstand:',
            sol_cube_target_find: 'Gesuchte Fläche finden',
            sol_cube_jump_step: 'Mittelfeld überspringen',
            sol_cube_match: '{a} überspringt {mid} und liegt gegenüber von {b}',
            sol_cube_res: '{a} und {b} liegen sich gegenüber',
            sol_cube_pairs_title: 'Alle Paare gegenüberliegender Flächen',
            sol_cube_discard_title: 'Ungültige Ansichten ausschließen',
            sol_cube_discard_note: 'Drillinge mit zwei gegenüberliegenden Flächen sind unmöglich.',
            sol_cube_view_rule: 'Drei Flächen können nur aneinanderliegen, wenn kein Paar im Netz gegenüberliegt:',
            sol_cube_view_step: 'Der gültige Drilling enthält miteinander verträgliche Flächen (kein Gegenüber-Paar):',
            sol_cube_view_res: 'Gültige Würfelansicht: {x}',
            sol_pipe_tank_source: 'Ausgangsbehälter',
            sol_pipe_start: 'Wasser fließt in Behälter {tank}',
            sol_pipe_blocked_title: 'Verstopfte Rohre',
            sol_pipe_blocked_note: 'Verstopfte Rohre (🚫) blockieren das Wasser: nachfolgende Behälter bleiben trocken.',
            sol_pipe_flow_title: 'Schwerkraftfluss',
            sol_pipe_flow: 'Leitungen zu tieferen Behältern:',
            sol_pipe_res: 'Behälter {tank} ist als Erster zu 100% voll',
            sol_mobile_torque: 'Hebelgesetz: Drehmoment links = Drehmoment rechts',
            sol_mobile_calc: 'Berechnung der Hebelarme und Gewichte:',
            sol_mobile_calc_beam: 'Balkengleichgewicht {n}',
            sol_mobile_arm: 'Arm: ×{l} vs ×{r}',
            sol_mobile_res: 'Das exakte Ausgleichsgewicht beträgt {x}',
            sol_origami_fold_n: 'Faltung {n} ({fold})',
            sol_origami_fold_note: 'Faltung {n}: halbiert die Fläche und verdoppelt die Papierschichten.',
            sol_origami_punch_title: 'Lochung ✂️',
            sol_origami_unfold_title: 'Rückwärtiges Auffalten',
            sol_origami_fold: 'Faltreihenfolge und Lochposition:',
            sol_origami_reflect: 'Rückwärts auffalten: symmetrische Spiegelung',
            sol_origami_res: 'Das aufgefaltete Blatt zeigt Muster {x}',
            sol_origami_step2_chip: 'Umgekehrte Symmetrie: {n} Löcher insgesamt',
            sol_holes: 'Löcher',
            sol_hole: 'Loch',
            sol_scale_eq1: 'Erste Waage im Gleichgewicht:',
            sol_scale_sub: 'Algebraische Ersetzung in der zweiten:',
            sol_scale_res: 'Endgültiges Gleichgewicht: {x}',
            sol_cup_start_chip: 'Start: {seq}',
            sol_cup_start: 'Ausgangsstellung der Becher:',
            sol_cup_swap: 'Reihenfolge der Züge:',
            sol_cup_swap_step: 'Zug {n}: {move}',
            sol_cup_state_after: 'Neue Reihenfolge:',
            sol_cup_res: 'Die Kugel endet unter Becher {n}',
            sol_grid_target: 'Gesuchtes Feld',
            sol_grid_row: 'Symbole in Zeile {r}',
            sol_grid_row_note: 'Jedes Symbol darf höchstens einmal pro Zeile vorkommen.',
            sol_grid_col: 'Symbole in Spalte {c}',
            sol_grid_col_note: 'Jedes Symbol darf höchstens einmal pro Spalte vorkommen.',
            sol_grid_all_elim: 'Alle ausgeschlossenen Kandidaten',
            sol_compass_start: 'Anfangsausrichtung: {dir}',
            sol_compass_turn: 'Reihenfolge der Drehungen:',
            sol_compass_turn_step: 'Drehung {n}: {turn}',
            sol_compass_res: 'Endausrichtung: {dir}',
            sol_walk_start: 'Startposition: 🐢',
            sol_walk_step: 'Schritt {n}: Richtung {dir}',
            sol_walk_moves: 'Schritte auf der Karte:',
            sol_walk_res: 'Zielfeld: {ans}',
            sol_lock_clue_n: 'Hinweis {n}',
            sol_lock_clue1: 'Analyse des ersten Hinweises:',
            sol_lock_clue2: 'Analyse des zweiten Hinweises:',
            sol_lock_deduction: 'Kombination der Hinweise und Ausschluss',
            sol_lock_res: 'Entschlüsselter Geheimcode: {code}',
            sol_seat_start: 'Fester Platz am Tisch:',
            sol_seat_step: 'Platzierung nach Bedingungen:',
            sol_seat_res: 'Am gesuchten Platz sitzt: {name}',
            sol_box_rule: 'Wahrheitsprüfung der Schilder:',
            sol_box_step: 'Vergleich der Truhen-Hypothesen:',
            sol_box_res: 'Der Schatz liegt in Truhe: {box}',
            sol_pt_repeat: 'Wiederkehrendes Zyklusmuster finden:',
            sol_pt_step: 'Position des Fragezeichens berechnen:',
            sol_pt_res: 'Passendes Symbol: {ans}',
            sol_seq_step: 'Konstanter Schritt zwischen Nachbarn:',
            sol_seq_calc: 'Regel auf letzten Wert anwenden:',
            sol_seq_res: 'Nächste Zahl: {ans}',
            sol_sym_eq1: 'Wert des ersten Symbols berechnen:',
            sol_sym_sub: 'Einsetzen für das zweite:',
            sol_sym_res: 'Endwert: {ans}',
            sol_mirror_rule: 'Angewendete Bewegung: {move}',
            sol_mirror_pos: 'Zielfeld erhält gespiegelte Zelle:',
            sol_mirror_refl: '🪞 Spiegelung',
            sol_target_pos: 'Zielfeld',
            sol_mirror_res: 'Symbol auf dem Feld: {ans}',
            sol_lamp_start: 'Anfangszustand der Lampen:',
            sol_lamp_sw: 'Reihenfolge der Schalter:',
            sol_lamp_res: 'Resultierende Konfiguration: {ans}',
            sol_alch_base: 'Alchemie-Umrechnungsformel:',
            sol_alch_step: 'Proportionale Berechnung:',
            sol_alch_res: 'Errechnetes Endergebnis: {ans}',
            sol_susp_traits: 'Merkmale des Verdächtigen:',
            sol_susp_elim: 'Ausschluss unpassender Profile:',
            sol_susp_res: 'Einziger passender Verdächtiger: {ans}',
            sol_liar_rule: 'Aussagen von Wahrheits- und Lügnern:',
            sol_liar_step: 'Widerspruchsfreiheit prüfen:',
            sol_liar_res: 'Richtige Aufteilung: {ans}',
            sol_odd_rule: 'Drei Elemente teilen die Eigenschaft:',
            sol_odd_step: 'Prüfen des Abweichlers:',
            sol_odd_res: 'Der Regelbrecher ist: {ans}',
            sol_days_start: 'Ausgangstag:',
            sol_days_step: 'Netto-Tagesverschiebung berechnen:',
            sol_days_res: 'Resultierender Wochentag: {ans}',
            sol_sign_eq: 'Gleichung vervollständigen:',
            sol_sign_test: 'Kandidaten-Rechenarten testen:',
            sol_sign_res: 'Richtige Rechenart: {ans}',
            sol_gen_premise: 'Aufgabenstellung und Vorgaben:',
            sol_gen_logic: 'Schritt-für-Schritt-Herleitung:',
            sol_gen_res: 'Exakte Lösung: {ans}',
            sol_riddle_premise: 'Text des Rätsels:',
            sol_riddle_logic: 'Überlegung und logische Erklärung:',
            sol_skip: 'überspringen',
            sol_find: 'finden',
            sol_laser_torch: 'Taschenlampe',
            cw: 'Im Uhrzeigersinn (↻)',
            ccw: 'Gegen den Uhrzeigersinn (↺)',
        },
    };

    I.extend(TRANSLATIONS);

    function optionStr(q, opt) {
        if (typeof opt === 'string' || typeof opt === 'number') return String(opt);
        if (opt && opt.k) return t(opt.k);
        if (opt && opt.r !== undefined) {
            return `${opt.r > 0 ? '+' : ''}${opt.r}°`;
        }
        if (opt && opt.letter) return opt.letter;
        if (opt && opt.text) return opt.text;
        return String(opt);
    }

    function getRiddleText(q) {
        if (!q) return null;
        if (q.t && q.t[I.getLang()]) return q.t[I.getLang()];
        if (q.t && q.t.en) return q.t.en;
        const list = root.ThinkHopRiddles || [];
        const r = list.find(x => x.id === (q.id || (q.why && q.why[0] && q.why[0].riddle)));
        if (r && r.t) return r.t[I.getLang()] || r.t.en || null;
        return null;
    }

    function lineStr(q, obj) {
        if (typeof obj === 'string') return obj;
        if (!obj) return '';
        if (obj.lit !== undefined) return String(obj.lit);
        if (obj.text !== undefined) return String(obj.text);
        if (obj.riddle !== undefined) {
            const rText = getRiddleText(q);
            if (rText && rText[1]) return rText[1];
            return (t('why_' + obj.riddle) !== 'why_' + obj.riddle) ? t('why_' + obj.riddle) : '';
        }
        if (obj.k) {
            const v = Object.assign({}, obj.v);
            if (obj.prop) v.prop = t(obj.prop.k, obj.prop.v);
            for (const key of Object.keys(v)) {
                if (typeof v[key] === 'object' && v[key] && v[key].k) {
                    v[key] = t(v[key].k, v[key].v);
                }
            }
            return t(obj.k, v);
        }
        return '';
    }

    /**
     * Renders a complete step-by-step graphical explanation flow for question `q`.
     * Does NOT constrain deductions to 3 steps; dynamically generates as many natural,
     * granular steps (3 to 7+) as needed to faithfully demonstrate how the answer is deduced.
     */
    function render(q, right) {
        const UI = root.ThinkHopUI;
        if (!UI || !UI.h) return null;
        const {h, icon} = UI;

        const ansVal = q.options && q.answer !== undefined ? optionStr(q, q.options[q.answer]) : '';

        function makeStep(num, title, visualEl, noteText, isWinner) {
            return h('div.why-step-card' + (isWinner ? '.winner' : ''),
                h('span.why-step-badge', String(num)),
                h('div.why-step-body',
                    h('strong.why-step-title', title),
                    noteText ? h('span.why-step-note', noteText) : null,
                    visualEl ? h('div.why-step-visual', visualEl) : null));
        }

        function chip(text, cls) {
            return h('span.why-chip' + (cls ? '.' + cls : ''), text);
        }

        function elimChip(text) {
            return h('span.why-elim-chip', text);
        }

        function flowRow(...items) {
            return h('div.why-flow-row', items);
        }

        function arrow(text) {
            return h('span.why-arrow', text || '➔');
        }

        function winnerChip(text) {
            return h('div.why-winner-chip', icon('tick'), text);
        }

        const cards = [];
        let stepCount = 0;

        function addStep(title, visualEl, noteText, isWinner) {
            stepCount++;
            cards.push(makeStep(stepCount, title, visualEl, noteText, isWinner));
        }

        function addFinalStep(title, visualEl, noteText) {
            addStep(title || t('sol_step_final'), visualEl, noteText, true);
        }

        // 1. Gears
        if (q.gearsData) {
            const gd = q.gearsData;
            const startCog = String.fromCharCode(65 + (gd.startIdx || 0));
            const startDirStr = gd.startDir === 'cw' ? t('cw') : t('ccw');
            const targetIdx = (gd.targetIdx !== undefined) ? gd.targetIdx : (gd.count - 1);
            const targetCog = String.fromCharCode(65 + targetIdx);

            // Step 1: Initial driving gear
            addStep(
                t('sol_gear_start_title'),
                flowRow(chip(t('sol_gear_start', {cog: startCog, dir: startDirStr}), 'initial')),
                t('sol_gear_rule_note')
            );

            if (gd.jamPair) {
                const c1 = String.fromCharCode(65 + gd.jamPair[0]);
                const c2 = String.fromCharCode(65 + gd.jamPair[1]);
                addStep(
                    t('sol_step_deduction'),
                    flowRow(chip(c1, 'target'), arrow('⚡'), chip(c2, 'wrong')),
                    t('sol_gear_jam')
                );
                addFinalStep(
                    t('sol_step_final'),
                    winnerChip(t('sol_gear_jam_res')),
                    t('why_gears_jam', {x: ansVal})
                );
            } else {
                // Granular contact steps: each meshed pair contact
                const count = gd.count || 2;
                for (let i = 0; i < count - 1; i++) {
                    const cA = String.fromCharCode(65 + i);
                    const cB = String.fromCharCode(65 + i + 1);
                    const dirA = (gd.dirs ? gd.dirs[i] : (i % 2 === 0 ? gd.startDir : (gd.startDir === 'cw' ? 'ccw' : 'cw')));
                    const dirB = (gd.dirs ? gd.dirs[i + 1] : (dirA === 'cw' ? 'ccw' : 'cw'));
                    const dirAStr = dirA === 'cw' ? t('cw') : t('ccw');
                    const dirBStr = dirB === 'cw' ? t('cw') : t('ccw');
                    const isTargetStep = (i + 1 === targetIdx);

                    addStep(
                        t('sol_gear_contact', {prev: cA, next: cB}),
                        flowRow(
                            chip(`${cA} (${dirA === 'cw' ? '↻' : '↺'})`),
                            arrow('➔'),
                            chip(`${cB} (${dirB === 'cw' ? '↻' : '↺'})`, isTargetStep ? 'target' : '')
                        ),
                        t('sol_gear_step_note', {prev: cA, next: cB, prevDir: dirAStr, nextDir: dirBStr})
                    );
                    if (isTargetStep && targetIdx < count - 1) {
                        break;
                    }
                }

                const targetDir = (gd.dirs && gd.dirs[targetIdx]) ? gd.dirs[targetIdx] : (targetIdx % 2 === 0 ? gd.startDir : (gd.startDir === 'cw' ? 'ccw' : 'cw'));
                const targetDirStr = targetDir === 'cw' ? t('cw') : t('ccw');
                addFinalStep(
                    t('sol_step_final'),
                    winnerChip(t('sol_gear_res', {cog: targetCog, dir: targetDirStr})),
                    t('sol_gear_target_reached')
                );
            }
        }
        // 2. Lasers
        else if (q.lasersData) {
            const ld = q.lasersData;
            const startStr = `(${ld.startX + 1}, ${ld.startY + 1})`;

            // Step 1: Laser source
            addStep(
                t('sol_laser_source_title'),
                flowRow(chip(`🔦 ${t('sol_laser_torch')} ${startStr}`, 'initial')),
                t('sol_laser_start', {x: ld.startX + 1, y: ld.startY + 1, dir: '➡️'})
            );

            // Step 2..K: Mirror bounces individually
            if (ld.path && ld.path.length > 2) {
                let bounceNum = 0;
                for (let i = 1; i < ld.path.length - 1; i++) {
                    const [px, py] = ld.path[i];
                    if (py >= 0 && py < ld.size && px >= 0 && px < ld.size) {
                        const cell = ld.grid[py][px];
                        if (cell === '/' || cell === '\\') {
                            bounceNum++;
                            const [nextX, nextY] = ld.path[i + 1];
                            const outDx = nextX - px;
                            const outDy = nextY - py;
                            const outDir = (outDx === 1) ? '➡️' : (outDx === -1 ? '⬅️' : (outDy === 1 ? '⬇️' : '⬆️'));

                            addStep(
                                t('sol_laser_mirror_step', {n: bounceNum, x: px + 1, y: py + 1}),
                                flowRow(
                                    chip(`🪞 ${cell}`),
                                    arrow('➔'),
                                    chip(outDir, 'target')
                                ),
                                t('sol_laser_deflect_note', {m: cell, dir: outDir})
                            );
                        }
                    }
                }
            }

            // Final step: hits target
            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_laser_hit', {target: ld.target, n: ld.reflections || 1})),
                ''
            );
        }
        // 3. Cubes
        else if (q.cubesData) {
            const cd = q.cubesData;
            const targetIcon = cd.targetIcon || (q.pv && q.pv.target) || (cd.icons && cd.icons[0]) || '?';
            const rightIcon = cd.rightIcon || ansVal || (q.why && q.why[0] && q.why[0].v ? q.why[0].v.x : '?');

            if (cd.isViewMode) {
                // View mode:
                // Step 1: Rule
                addStep(
                    t('sol_cube_rule_title'),
                    flowRow(chip('📦 ' + t('sol_cube_rule_title'), 'initial')),
                    t('sol_cube_view_rule')
                );

                // Step 2: Opposite face pairs
                const oppPairs = [];
                if (cd.icons) {
                    const opp = cd.opp || {0: 5, 5: 0, 1: 3, 3: 1, 2: 4, 4: 2};
                    const seen = new Set();
                    for (let i = 0; i < 6; i++) {
                        const j = opp[i];
                        const key = Math.min(i, j) + '-' + Math.max(i, j);
                        if (!seen.has(key) && cd.icons[i] && cd.icons[j]) {
                            seen.add(key);
                            oppPairs.push(chip(`${cd.icons[i]} ↔ ${cd.icons[j]}`));
                        }
                    }
                }
                if (oppPairs.length > 0) {
                    addStep(
                        t('sol_cube_pairs_title'),
                        flowRow(...oppPairs),
                        t('sol_cube_discard_note')
                    );
                }

                // Step 3: Triplet deduction
                addStep(
                    t('sol_cube_discard_title'),
                    flowRow(chip('❌ ' + t('sol_cube_discard_title'), 'wrong')),
                    t('sol_cube_view_step')
                );

                // Final step
                addFinalStep(
                    t('sol_step_final'),
                    winnerChip(t('sol_cube_view_res', {x: ansVal})),
                    ''
                );
            } else {
                // Opposite face mode:
                // Step 1: Rule
                addStep(
                    t('sol_cube_rule_title'),
                    flowRow(chip('📦 ' + t('sol_cube_rule_title'), 'initial')),
                    t('sol_cube_rule')
                );

                // Step 2: Locate target face
                addStep(
                    t('sol_cube_target_find'),
                    flowRow(chip(targetIcon, 'initial')),
                    t('sol_cube_target_find') + ': ' + targetIcon
                );

                // Step 3: Jump intermediate square
                addStep(
                    t('sol_cube_jump_step'),
                    flowRow(chip(targetIcon), arrow('➔ ' + t('sol_skip') + ' ➔'), chip('⬜'), arrow('➔ ' + t('sol_find') + ' ➔'), chip(rightIcon, 'target')),
                    t('sol_cube_match', {a: targetIcon, mid: '⬜', b: rightIcon})
                );

                // Final step
                addFinalStep(
                    t('sol_step_final'),
                    winnerChip(t('sol_cube_res', {a: targetIcon, b: rightIcon})),
                    ''
                );
            }
        }
        // 4. Pipes & communicating vessels
        else if (q.pipesData) {
            const pd = q.pipesData;
            const source = pd.source || (pd.tanks ? pd.tanks[0] : 'A');

            // Step 1: Water source
            addStep(
                t('sol_pipe_tank_source'),
                flowRow(chip(`🚰 ${source}`, 'initial')),
                t('sol_pipe_start', {tank: source})
            );

            // Step 2: Blocked pipes
            const connections = pd.connections || [];
            const openFlows = connections.filter(c => !c.blocked).map(c => chip(`${c.from} ➔ ${c.to} (✓)`));
            const blockedFlows = connections.filter(c => c.blocked).map(c => chip(`${c.from} ➔ ${c.to} (🚫)`, 'wrong'));

            if (blockedFlows.length > 0) {
                addStep(
                    t('sol_pipe_blocked_title'),
                    flowRow(...blockedFlows),
                    t('sol_pipe_blocked_note')
                );
            }

            // Step 3: Open flow path
            if (openFlows.length > 0) {
                addStep(
                    t('sol_pipe_flow_title'),
                    flowRow(...openFlows),
                    t('sol_pipe_flow')
                );
            }

            // Final step
            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_pipe_res', {tank: pd.winner})),
                ''
            );
        }
        // 5. Mobiles (sculptures with balance beams)
        else if (q.mobilesData) {
            const md = q.mobilesData;

            // Step 1: Law of the lever
            addStep(
                t('sol_step_start'),
                flowRow(chip('⚖️ ' + t('sol_mobile_torque'), 'initial')),
                t('sol_mobile_torque')
            );

            // Step 2..N: Calculate balance for each beam
            const getW = side => {
                if (!side) return 0;
                if (side.realVal !== undefined) return side.realVal;
                if (side.val !== undefined && side.val !== '?') return side.val;
                if (side.totalWeight !== undefined) return side.totalWeight;
                if (side.isTarget) return '?';
                return side.val || 0;
            };

            (md.beams || []).forEach((b, idx) => {
                const wL = getW(b.left);
                const wR = getW(b.right);
                const calcStr = `${wL} × ${b.dL} = ${wR} × ${b.dR}`;
                addStep(
                    t('sol_mobile_calc_beam', {n: idx + 1}),
                    flowRow(chip(t('sol_mobile_arm', {l: b.dL, r: b.dR})), arrow('➔'), chip(calcStr)),
                    t('sol_mobile_calc')
                );
            });

            // Final step
            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_mobile_res', {x: ansVal})),
                ''
            );
        }
        // 6. Origami
        else if (q.origamiData) {
            const od = q.origamiData;

            // Step 1: Blank sheet
            addStep(
                t('sol_step_start'),
                flowRow(chip('📄 ' + t('origami_blank'), 'initial')),
                t('origami_start')
            );

            // Step 2..N: Each fold step individually
            const foldName = f => {
                const arrowIcon = (f === 'L_TO_R') ? '➡️' : (f === 'R_TO_L' ? '⬅️' : (f === 'T_TO_B' ? '⬇️' : '⬆️'));
                return `${f.replace('_TO_', ' ➔ ')} ${arrowIcon}`;
            };
            (od.folds || []).forEach((f, idx) => {
                addStep(
                    t('sol_origami_fold_n', {n: idx + 1, fold: foldName(f)}),
                    flowRow(chip(`${idx + 1}ª: ${foldName(f)}`)),
                    t('sol_origami_fold_note', {n: idx + 1})
                );
            });

            // Punch step
            const punchCount = (od.punch || []).length;
            addStep(
                t('sol_origami_punch_title'),
                flowRow(chip(`✂️ ${punchCount} ${punchCount > 1 ? t('sol_holes') : t('sol_hole')}`, 'target')),
                t('sol_origami_fold')
            );

            // Reverse unfolding reflections
            const unfoldedCount = (od.unfoldedHoles || []).length;
            addStep(
                t('sol_origami_unfold_title'),
                flowRow(chip(t('sol_origami_step2_chip', {n: unfoldedCount}), 'target')),
                t('sol_origami_reflect')
            );

            // Final step
            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_origami_res', {x: ansVal})),
                ''
            );
        }
        // 7. Scales
        else if (q.cat === 'scales' && q.lines && q.lines.length >= 2) {
            addStep(
                t('sol_scale_eq1'),
                flowRow(chip(q.lines[0], 'initial')),
                t('sol_scale_eq1')
            );

            addStep(
                t('sol_scale_sub'),
                flowRow(chip(q.lines[1])),
                t('sol_scale_sub')
            );

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_scale_res', {x: ansVal})),
                ''
            );
        }
        // 8. Cups (step-by-step swap animation)
        else if (q.cat === 'cups' && q.lines && q.lines.length >= 2) {
            const startItems = q.lines[0].split(/\s+/).filter(Boolean);
            const moves = q.lines[1].split('·').map(s => s.trim()).filter(Boolean);

            // Step 1: Initial layout
            addStep(
                t('sol_step_start'),
                flowRow(...startItems.map(it => chip(it, 'initial'))),
                t('sol_cup_start')
            );

            // Replay each move individually
            let curRow = startItems.slice();
            moves.forEach((moveStr, idx) => {
                const swapMatch = moveStr.match(/(\d+)\s*↔\s*(\d+)/);
                if (swapMatch) {
                    const iA = parseInt(swapMatch[1], 10) - 1;
                    const iB = parseInt(swapMatch[2], 10) - 1;
                    if (iA >= 0 && iA < curRow.length && iB >= 0 && iB < curRow.length) {
                        const tmp = curRow[iA];
                        curRow[iA] = curRow[iB];
                        curRow[iB] = tmp;
                    }
                } else if (moveStr.includes('⇄')) {
                    curRow.reverse();
                } else if (moveStr.includes('⏪')) {
                    curRow.push(curRow.shift());
                }

                addStep(
                    t('sol_cup_swap_step', {n: idx + 1, move: moveStr}),
                    flowRow(
                        chip(moveStr, 'target'),
                        arrow('➔'),
                        ...curRow.map(it => chip(it))
                    ),
                    t('sol_cup_state_after') + ' ' + curRow.join(' ')
                );
            });

            // Final step
            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_cup_res', {n: ansVal})),
                ''
            );
        }
        // 9. Grid (Mini-Sudoku / Sudoku X)
        else if (q.cat === 'grid' && q.grid && Array.isArray(q.grid)) {
            let tr = -1, tc = -1;
            for (let r = 0; r < q.grid.length; r++) {
                for (let c = 0; c < q.grid[r].length; c++) {
                    if (q.grid[r][c] === '?') { tr = r; tc = c; break; }
                }
                if (tr >= 0) break;
            }

            if (tr >= 0) {
                // Step 1: Target cell
                addStep(
                    t('sol_grid_target'),
                    flowRow(chip(t('sol_target_cell', {r: tr + 1, c: tc + 1}), 'initial')),
                    ''
                );

                // Step 2: Row candidates
                const rowItems = q.grid[tr].filter(x => x && x !== '?');
                if (rowItems.length > 0) {
                    addStep(
                        t('sol_grid_row', {r: tr + 1}),
                        flowRow(...rowItems.map(s => elimChip(s))),
                        t('sol_grid_row_note')
                    );
                }

                // Step 3: Column candidates
                const colItems = q.grid.map(row => row[tc]).filter(x => x && x !== '?');
                if (colItems.length > 0) {
                    addStep(
                        t('sol_grid_col', {c: tc + 1}),
                        flowRow(...colItems.map(s => elimChip(s))),
                        t('sol_grid_col_note')
                    );
                }

                // Step 4: Summary of all eliminated candidates
                const seen = [...new Set([...rowItems, ...colItems])];
                addStep(
                    t('sol_grid_all_elim'),
                    flowRow(...seen.map(s => elimChip(s))),
                    t('sol_elim_row_col')
                );

                // Final step
                addFinalStep(
                    t('sol_step_final'),
                    winnerChip(`${t('sol_single_cand')} ${ansVal}`),
                    ''
                );
            }
        }
        // 10. Compass (Turn or Walk)
        else if (q.cat === 'compass' && q.lines && q.lines.length >= 1) {
            if (q.grid && q.lines.length === 1) {
                // walk grid
                addStep(
                    t('sol_step_start'),
                    flowRow(chip(t('sol_walk_start'), 'initial')),
                    ''
                );

                const steps = q.lines[0].split(/\s+/).filter(Boolean);
                steps.forEach((step, idx) => {
                    addStep(
                        t('sol_walk_step', {n: idx + 1, dir: step}),
                        flowRow(chip(step)),
                        ''
                    );
                });

                addFinalStep(
                    t('sol_step_final'),
                    winnerChip(t('sol_walk_res', {ans: ansVal})),
                    ''
                );
            } else {
                // turn angles
                const startDir = q.lines[0];
                addStep(
                    t('sol_step_start'),
                    flowRow(chip(startDir, 'initial')),
                    t('sol_compass_start', {dir: startDir})
                );

                const turns = (q.lines[1] || '').split('·').map(s => s.trim()).filter(Boolean);
                turns.forEach((turn, idx) => {
                    addStep(
                        t('sol_compass_turn_step', {n: idx + 1, turn}),
                        flowRow(chip(turn)),
                        ''
                    );
                });

                addFinalStep(
                    t('sol_step_final'),
                    winnerChip(t('sol_compass_res', {dir: ansVal})),
                    ''
                );
            }
        }
        // 11. Mirror
        else if (q.cat === 'mirror') {
            const moveStr = q.lines ? q.lines.join(' · ') : (q.pv && q.pv.target ? `Specchia ${q.pv.target}` : 'Simmetria');
            addStep(
                t('sol_step_start'),
                flowRow(chip(t('sol_mirror_rule', {move: moveStr}), 'initial')),
                ''
            );

            addStep(
                t('sol_step_deduction'),
                flowRow(chip(t('sol_mirror_refl')), arrow('➔'), chip(t('sol_target_pos'))),
                t('sol_mirror_pos')
            );

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_mirror_res', {ans: ansVal})),
                ''
            );
        }
        // 12. Locks
        else if (q.cat === 'locks' && q.lines && q.lines.length >= 1) {
            q.lines.forEach((line, idx) => {
                addStep(
                    t('sol_lock_clue_n', {n: idx + 1}),
                    flowRow(chip(line, 'initial')),
                    ''
                );
            });

            addStep(
                t('sol_lock_deduction'),
                flowRow(chip('🔍 ' + t('sol_lock_deduction'), 'target')),
                t('why_locks', {x: ansVal})
            );

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_lock_res', {code: ansVal})),
                ''
            );
        }
        // 13. Fruit algebra (symbols)
        else if (q.cat === 'symbols' && q.lines && q.lines.length >= 2) {
            addStep(
                t('sol_sym_eq1'),
                flowRow(chip(q.lines[0], 'initial')),
                t('sol_sym_eq1')
            );

            for (let i = 1; i < q.lines.length; i++) {
                addStep(
                    t('sol_sym_sub') + (q.lines.length > 2 ? ` (${i})` : ''),
                    flowRow(chip(q.lines[i])),
                    t('sol_sym_sub')
                );
            }

            if (q.why && q.why[0] && q.why[0].lit) {
                addStep(
                    t('sol_step_deduction'),
                    flowRow(chip(q.why[0].lit, 'target')),
                    t('sol_gen_logic')
                );
            }

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_sym_res', {ans: ansVal})),
                ''
            );
        }
        // 14. Seats
        else if (q.cat === 'seats' && q.facts && q.facts.length >= 2) {
            addStep(
                t('sol_seat_start'),
                flowRow(chip(lineStr(q, q.facts[0]), 'initial')),
                t('sol_seat_start')
            );

            q.facts.slice(1).forEach((fact, idx) => {
                addStep(
                    t('sol_step_deduction') + ` (${idx + 1})`,
                    flowRow(chip(lineStr(q, fact))),
                    t('sol_seat_step')
                );
            });

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_seat_res', {name: ansVal})),
                ''
            );
        }
        // 15. Boxes
        else if (q.cat === 'boxes' && q.facts && q.facts.length >= 1) {
            addStep(
                t('sol_box_rule'),
                flowRow(chip(lineStr(q, q.facts[0]), 'initial')),
                t('sol_box_rule')
            );

            q.facts.slice(1).forEach((fact, idx) => {
                addStep(
                    t('sol_step_deduction') + ` (${idx + 1})`,
                    flowRow(chip(lineStr(q, fact))),
                    t('sol_box_step')
                );
            });

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_box_res', {box: ansVal})),
                ''
            );
        }
        // 16. Who owns what (pets / houses)
        else if (q.cat === 'whoowns' && q.facts && q.facts.length >= 1) {
            addStep(
                t('sol_step_start'),
                flowRow(chip(q.ask ? lineStr(q, q.ask) : t('sol_title'), 'initial')),
                t('sol_gen_premise')
            );

            q.facts.forEach((fact, idx) => {
                addStep(
                    t('sol_step_deduction') + ` (${idx + 1})`,
                    flowRow(chip(lineStr(q, fact))),
                    ''
                );
            });

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_gen_res', {ans: ansVal})),
                ''
            );
        }
        // 17. Lamps
        else if (q.cat === 'lamps' && q.lines && q.lines.length >= 2) {
            addStep(
                t('sol_lamp_start'),
                flowRow(chip(q.lines[0], 'initial')),
                t('sol_lamp_start')
            );

            q.lines.slice(1).forEach((line, idx) => {
                addStep(
                    t('sol_lamp_sw') + ` (${idx + 1})`,
                    flowRow(chip(line.trim())),
                    ''
                );
            });

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_lamp_res', {ans: ansVal})),
                ''
            );
        }
        // 18. Signs / Equations
        else if (q.cat === 'sign' && q.nums && q.result !== undefined) {
            addStep(
                t('sol_sign_eq'),
                flowRow(chip(`${q.nums.join(' [ ? ] ')} = ${q.result}`, 'initial')),
                t('sol_sign_eq')
            );

            const ops = ['+', '−', '×', '÷'].map(o => chip(`${q.nums[0]} ${o} ${q.nums[1]}`));
            addStep(
                t('sol_sign_test'),
                flowRow(...ops),
                t('sol_sign_test')
            );

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_sign_res', {ans: ansVal})),
                ''
            );
        }
        // 19. Pattern / Sequence
        else if ((q.cat === 'pattern' || q.cat === 'sequence') && q.items) {
            const preview = q.items.slice(0, 4).map(x => chip(String(x)));
            addStep(
                t('sol_pt_repeat'),
                flowRow(...preview),
                t('sol_pt_repeat')
            );

            const flowSeq = [];
            for (let i = 0; i < Math.min(q.items.length, 6); i++) {
                if (i > 0) flowSeq.push(arrow('➔'));
                flowSeq.push(chip(String(q.items[i])));
            }
            flowSeq.push(arrow('➔'));
            flowSeq.push(chip('?', 'target'));

            addStep(
                t('sol_pt_step'),
                flowRow(...flowSeq),
                t('sol_pt_step')
            );

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_pt_res', {ans: ansVal})),
                ''
            );
        }
        // 20. Riddles (riddles.js & riddles-more.js)
        else if (q.cat === 'riddle' || q.view === 'riddle' || (q.why && q.why.some(w => w && w.riddle))) {
            const rText = getRiddleText(q);
            const questionText = (rText && rText[0]) || (q.ask ? lineStr(q, q.ask) : t('cat_riddle'));
            const explanationText = (rText && rText[1]) || (q.why && lineStr(q, q.why[0])) || '';

            addStep(
                t('sol_step_start'),
                h('div.why-riddle-box', questionText),
                t('sol_riddle_premise')
            );

            if (explanationText) {
                addStep(
                    t('sol_step_deduction'),
                    h('div.why-riddle-box.highlight', explanationText),
                    t('sol_riddle_logic')
                );
            }

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_gen_res', {ans: ansVal})),
                ''
            );
        }
        // 21. Fallback for all other puzzle types
        else {
            const catLabel = q.cat ? t('cat_' + q.cat) : '';
            addStep(
                t('sol_step_start'),
                flowRow(chip(q.ask ? lineStr(q, q.ask) : (catLabel || t('sol_title')), 'initial')),
                t('sol_gen_premise')
            );

            let addedDeduction = false;
            if (q.why && q.why.length > 0) {
                q.why.forEach((w, idx) => {
                    const whyText = lineStr(q, w);
                    if (whyText) {
                        addedDeduction = true;
                        addStep(
                            t('sol_step_deduction') + (q.why.length > 1 ? ` (${idx + 1})` : ''),
                            flowRow(chip(whyText)),
                            t('sol_gen_logic')
                        );
                    }
                });
            }

            if (!addedDeduction) {
                // If why had no textual representation or lines exist, use lines or generic step
                if (q.lines && q.lines.length > 0) {
                    q.lines.forEach((l, idx) => {
                        addStep(
                            t('sol_step_deduction') + (q.lines.length > 1 ? ` (${idx + 1})` : ''),
                            flowRow(chip(lineStr(q, l))),
                            t('sol_gen_logic')
                        );
                    });
                } else {
                    addStep(
                        t('sol_step_deduction'),
                        flowRow(chip(catLabel || t('sol_title'))),
                        t('sol_gen_logic')
                    );
                }
            }

            addFinalStep(
                t('sol_step_final'),
                winnerChip(t('sol_gen_res', {ans: ansVal})),
                ''
            );
        }

        return h('details.why-stepflow',
            h('summary.why-stepflow-head',
                h('div.why-stepflow-title',
                    icon('bulb'),
                    h('span.why-stepflow-pill', t('sol_title'))),
                h('span.why-stepflow-chevron', icon('chevron'))),
            h('div.why-stepflow-cards', cards));
    }

    root.ThinkHopSolution = {render};
})(typeof window !== 'undefined' ? window : globalThis);
