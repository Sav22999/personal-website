<?php
/** Animations with CSS: transitions, transforms, keyframes and how to make them feel good. */

$page = '<div class="card">Una card</div>
<button class="btn">Inizia</button>
<div class="palla"></div>';

return [
    'id' => 'css-animations', 'family' => 'css', 'after' => 'css-intermediate',
    'title' => L('Animazioni con CSS', 'CSS Animations'), 'short' => L('Animazioni', 'Animations'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Transizioni, trasformazioni, keyframes, tempi e animazioni rispettose.', 'Transitions, transforms, keyframes, timing and respectful animation.'),
    'lessons' => [
        lesson('transitions', L('Transizioni', 'Transitions'), [
            P('Una **transizione** rende graduale un cambiamento. Indichi la proprietà, la durata e, se vuoi, la curva e il ritardo.', 'A **transition** makes a change gradual. You give the property, the duration and, if you want, the curve and the delay.'),
            C('.btn {
  background: tomato;
  transition: background-color .3s ease, transform .2s;
}
.btn:hover {
  background: gold;
  transform: scale(1.05);
}', 'css'),
            P('Si scrive sullo stato normale, non su `:hover`: così vale sia all’andata che al ritorno.', 'It goes on the normal state, not on `:hover`: so it works both ways.'),
        ], [
            Q('t1', L('Dove si scrive `transition`?', 'Where does `transition` go?'), [L('Sullo stato normale', 'On the normal state'), L('Solo su `:hover`', 'Only on `:hover`'), L('Nell’HTML', 'In the HTML'), L('In `@media`', 'In `@media`')], 0, L('Così l’effetto vale nei due sensi.', 'So the effect works both ways.')),
            Q('t2', L('Quanto dura `.3s`?', 'How long is `.3s`?'), [L('300 millisecondi', '300 milliseconds'), L('3 secondi', '3 seconds'), L('30 secondi', '30 seconds'), L('3 minuti', '3 minutes')], 0, L('Tra 150 e 300ms un’interfaccia sembra rapida.', 'Between 150 and 300ms an interface feels quick.')),
            Q('t3', L('Si può animare `display`?', 'Can you animate `display`?'), [L('Di solito no', 'Usually not'), L('Sempre', 'Always'), L('Solo in rosso', 'Only in red'), L('Solo nei link', 'Only in links')], 0, L('Si anima ciò che ha valori intermedi, come `opacity`.', 'You animate what has in-between values, like `opacity`.')),
        ], task('css', L('Dai a `.btn` una `transition` su `transform` di `.2s`, e su `:hover` un `transform: scale(1.1)`.', 'Give `.btn` a `transition` on `transform` of `.2s`, and on `:hover` a `transform: scale(1.1)`.'), '', [re('\.btn\s*\{[^}]*transition\s*:\s*transform\s+\.?0?\.2s', L('La transizione è giusta', 'The transition is right')), re('\.btn:hover\s*\{[^}]*scale\(1\.1\)', L('Si ingrandisce al passaggio', 'It grows on hover'))], $page, ".btn {\n  transition: transform .2s;\n}\n.btn:hover {\n  transform: scale(1.1);\n}"), [
            dBlanks(pFill(), ".card {\n  ___: transform .2s ___;\n}\n.card:___ {\n  transform: scale(1.1);\n}", ['transition', 'ease', 'hover'], ['animation', 'slow', 'active-on'], L('Proprietà, durata e curva nello stato di partenza.', 'Property, duration and curve on the starting state.')),
            dFlash(pFlash(), [[L('`transition` funziona nei due sensi se sta sullo stato normale', '`transition` works both ways on the normal state'), true], [L('`opacity` si può animare', '`opacity` can be animated'), true], [L('`.2s` sono due secondi', '`.2s` is two seconds'), false], [L('Si possono animare più proprietà insieme', 'Several properties can be animated together'), true], [L('La transizione serve solo ai colori', 'Transitions are only for colours'), false]], L('Brevi e mirate: le transizioni migliori quasi non si notano.', 'Short and focused: the best transitions are barely noticed.')),
        ]),
        lesson('transforms', L('Trasformazioni', 'Transforms'), [
            P('`transform` sposta, ruota, ingrandisce e inclina un elemento senza disturbare gli altri. È anche la proprietà più veloce da animare.', '`transform` moves, rotates, scales and skews an element without disturbing the others. It is also the fastest property to animate.'),
            C('.card {
  transform: translateX(20px) rotate(5deg) scale(1.1);
  transform-origin: top left;
}', 'css'),
            P('L’ordine conta: ogni funzione agisce sul risultato della precedente. `transform-origin` sceglie il punto attorno a cui avviene tutto.', 'Order matters: each function acts on the result of the previous one. `transform-origin` picks the point everything happens around.'),
        ], [
            Q('r1', L('Quale funzione sposta?', 'Which function moves?'), ['translate()', 'move()', 'shift()', 'go()'], 0, L('Esistono `translateX` e `translateY`.', 'There are `translateX` and `translateY`.')),
            Q('r2', L('`transform` sposta gli altri elementi?', 'Does `transform` move other elements?'), [L('No', 'No'), L('Sì', 'Yes'), L('Solo i fratelli', 'Only siblings'), L('Solo il genitore', 'Only the parent')], 0, L('Lo spazio occupato resta quello di prima.', 'The space taken stays as before.')),
            Q('r3', L('Che cosa cambia `transform-origin`?', 'What does `transform-origin` change?'), [L('Il punto di riferimento', 'The reference point'), L('La durata', 'The duration'), L('Il colore', 'The colour'), L('La curva', 'The curve')], 0, L('Una rotazione da un angolo sembra una porta.', 'A rotation from a corner looks like a door.')),
        ], task('css', L('Ruota `.card` di `45deg` e spostala di `20px` a destra.', 'Rotate `.card` by `45deg` and move it `20px` to the right.'), '.card {

}', [re('rotate\(45deg\)', L('Ruota di 45 gradi', 'It rotates by 45 degrees')), re('translateX\(20px\)|translate\(20px', L('Si sposta di 20px', 'It moves by 20px'))], $page, ".card {\n  transform: translateX(20px) rotate(45deg);\n}"), [
            dMatch(pMatch(), [['translate()', L('Sposta', 'Moves')], ['rotate()', L('Ruota', 'Rotates')], ['scale()', L('Ingrandisce', 'Scales')], ['skew()', L('Inclina', 'Skews')]], L('Quattro funzioni, infinite combinazioni.', 'Four functions, endless combinations.')),
            dBug(pBug(), ['.card {', '  transform: rotate(45);', '}'], 1, '  transform: rotate(45deg);', L('Una rotazione vuole l’unità: `deg`.', 'A rotation needs its unit: `deg`.')),
        ]),
        lesson('keyframes', L('Keyframes', 'Keyframes'), [
            P('Le transizioni vanno da A a B. Con `@keyframes` disegni una storia a più tappe, che può ripetersi da sola, senza bisogno di un `:hover`.', 'Transitions go from A to B. With `@keyframes` you draw a story of several stages, which can repeat by itself, with no `:hover` needed.'),
            C('@keyframes rimbalzo {
  0%   { transform: translateY(0); }
  50%  { transform: translateY(-30px); }
  100% { transform: translateY(0); }
}
.palla {
  animation: rimbalzo 1s ease-in-out infinite;
}', 'css'),
            P('`animation` riassume nome, durata, curva e ripetizioni. `infinite` non si ferma mai, `alternate` torna indietro ogni due giri.', '`animation` sums up name, duration, curve and repeats. `infinite` never stops, `alternate` goes back every second round.'),
        ], [
            Q('k1', L('Che cosa definisce `@keyframes`?', 'What does `@keyframes` define?'), [L('Le tappe di un’animazione', 'The stages of an animation'), L('Un font', 'A font'), L('Una variabile', 'A variable'), L('Una media query', 'A media query')], 0, L('Si usa poi con `animation`.', 'It is then used with `animation`.')),
            Q('k2', L('Come si ripete per sempre?', 'How does it repeat for ever?'), ['infinite', 'loop', 'always', 'repeat'], 0, L('Oppure un numero di giri.', 'Or a number of rounds.')),
            Q('k3', L('`from` e `to` equivalgono a…', '`from` and `to` are the same as…'), ['0% e 100%', '1 e 2', 'start e end', 'A e B'], 0, L('Per le tappe intermedie servono le percentuali.', 'In-between stages need percentages.')),
        ], task('css', L('Crea `@keyframes pulsa` e applicalo a `.palla` con `animation: pulsa 1s infinite`.', 'Create `@keyframes pulsa` and apply it to `.palla` with `animation: pulsa 1s infinite`.'), '', [re('@keyframes\s+pulsa', L('I keyframes esistono', 'The keyframes exist')), re('\.palla\s*\{[^}]*animation\s*:\s*pulsa\s+1s\s+infinite', L('La palla pulsa', 'The ball pulses'))], $page, "@keyframes pulsa {\n  from { opacity: 1; }\n  to { opacity: .4; }\n}\n.palla {\n  animation: pulsa 1s infinite;\n}"), [
            dOrder(pSort(), ['@keyframes sali {', '  from { opacity: 0; }', '  to { opacity: 1; }', '}', '.card { animation: sali .4s; }'], L('Prima le tappe, poi chi le usa.', 'First the stages, then whoever uses them.')),
            dBug(pBug(), ['@keyframes gira {', '  to { transform: rotate(360deg); }', '}', '.ruota { animation: giro 2s infinite; }'], 3, '.ruota { animation: gira 2s infinite; }', L('Il nome in `animation` deve coincidere con quello dei `@keyframes`.', 'The name in `animation` must match the `@keyframes` one.')),
        ]),
        lesson('timing', L('Tempi e curve', 'Timing and curves'), [
            P('La stessa animazione cambia carattere con la curva: `linear` è meccanica, `ease-out` frena alla fine, `ease-in` parte piano. Con `cubic-bezier()` crei la tua, anche con un rimbalzo.', 'The same animation changes character with its curve: `linear` is mechanical, `ease-out` brakes at the end, `ease-in` starts slowly. With `cubic-bezier()` you make your own, even with a bounce.'),
            C('.card {
  animation-name: entra;
  animation-duration: .4s;
  animation-delay: .1s;
  animation-timing-function: cubic-bezier(.3, 1.5, .5, 1);
  animation-fill-mode: backwards;
}', 'css'),
            P('`animation-fill-mode` decide lo stato prima e dopo: `forwards` mantiene l’ultima tappa, `backwards` applica subito la prima durante il ritardo.', '`animation-fill-mode` decides the state before and after: `forwards` keeps the last stage, `backwards` applies the first at once during the delay.'),
        ], [
            Q('c1', L('Quale curva frena alla fine?', 'Which curve brakes at the end?'), ['ease-out', 'ease-in', 'linear', 'step'], 0, L('È la più naturale per ciò che entra in scena.', 'It is the most natural for things entering the scene.')),
            Q('c2', L('Che cosa fa `forwards`?', 'What does `forwards` do?'), [L('Tiene l’ultima tappa', 'Keeps the last stage'), L('Salta avanti', 'Skips ahead'), L('Va più veloce', 'Goes faster'), L('Ripete', 'Repeats')], 0, L('Senza, l’elemento torna com’era.', 'Without it, the element goes back to how it was.')),
            Q('c3', L('Perché scaglionare i ritardi in una lista?', 'Why stagger delays in a list?'), [L('Per un effetto a cascata', 'For a cascade effect'), L('Per pesare meno', 'To weigh less'), L('Per la SEO', 'For SEO'), L('Per il colore', 'For colour')], 0, L('L’occhio segue meglio una cosa alla volta.', 'The eye follows one thing at a time better.')),
        ], task('css', L('Dai a `.card` un `animation-delay` di `.2s` e un `animation-fill-mode` di `forwards`.', 'Give `.card` an `animation-delay` of `.2s` and an `animation-fill-mode` of `forwards`.'), '.card {

}', [re('animation-delay\s*:\s*0?\.2s', L('Il ritardo è giusto', 'The delay is right')), re('animation-fill-mode\s*:\s*forwards', L('Mantiene l’ultima tappa', 'It keeps the last stage'))], $page, ".card {\n  animation-delay: .2s;\n  animation-fill-mode: forwards;\n}"), [
            dMatch(pMatch(), [['linear', L('Velocità costante', 'Constant speed')], ['ease-in', L('Parte piano', 'Starts slowly')], ['ease-out', L('Frena alla fine', 'Brakes at the end')], ['cubic-bezier()', L('Curva su misura', 'Custom curve')], ['steps()', L('A scatti', 'In jumps')]], L('La curva è la personalità del movimento.', 'The curve is the personality of the movement.')),
            dFlash(pFlash(), [[L('`animation-delay` aspetta prima di partire', '`animation-delay` waits before starting'), true], [L('`ease-out` accelera alla fine', '`ease-out` speeds up at the end'), false], [L('`cubic-bezier` può creare un rimbalzo', '`cubic-bezier` can create a bounce'), true], [L('`linear` è perfetta per ogni cosa', '`linear` is perfect for everything'), false]], L('Il movimento naturale raramente è lineare.', 'Natural movement is rarely linear.')),
        ]),
        lesson('respect', L('Prestazioni e rispetto', 'Performance and respect'), [
            P('Anima `transform` e `opacity`: il browser li gestisce senza ricalcolare la pagina. Animare `width` o `top` costa molto di più e può scattare.', 'Animate `transform` and `opacity`: the browser handles them without recalculating the page. Animating `width` or `top` costs far more and can stutter.'),
            C('@media (prefers-reduced-motion: reduce) {
  * {
    animation: none !important;
    transition: none !important;
  }
}', 'css'),
            P('Alcune persone stanno male con il movimento: `prefers-reduced-motion` rispetta la loro impostazione di sistema. Un’animazione deve spiegare, non distrarre.', 'Some people feel ill with movement: `prefers-reduced-motion` respects their system setting. An animation must explain, not distract.'),
        ], [
            Q('p1', L('Quali proprietà conviene animare?', 'Which properties are best to animate?'), [L('`transform` e `opacity`', '`transform` and `opacity`'), L('`width` e `height`', '`width` and `height`'), L('`margin` e `top`', '`margin` and `top`'), L('`font-size`', '`font-size`')], 0, L('Non costringono a ricalcolare la pagina.', 'They do not force the page to be recalculated.')),
            Q('p2', L('Che cosa rispetta `prefers-reduced-motion`?', 'What does `prefers-reduced-motion` respect?'), [L('Chi chiede meno movimento', 'People asking for less movement'), L('Chi ha uno schermo piccolo', 'People with a small screen'), L('Chi usa Safari', 'Safari users'), L('Chi è offline', 'Offline users')], 0, L('È una preferenza del sistema operativo.', 'It is an operating system preference.')),
            Q('p3', L('A che cosa deve servire un’animazione?', 'What should an animation do?'), [L('A spiegare', 'Explain'), L('A distrarre', 'Distract'), L('A rallentare', 'Slow things down'), L('A stupire sempre', 'Always amaze')], 0, L('Se non aiuta a capire, togliila.', 'If it does not help understanding, remove it.')),
        ], task('css', L('Scrivi la media query `prefers-reduced-motion: reduce` che mette `animation: none` su `.palla`.', 'Write the `prefers-reduced-motion: reduce` media query that sets `animation: none` on `.palla`.'), '', [re('@media\s*\(prefers-reduced-motion:\s*reduce\)', L('La media query è giusta', 'The media query is right')), re('\.palla\s*\{[^}]*animation\s*:\s*none', L('L’animazione si ferma', 'The animation stops'))], $page, "@media (prefers-reduced-motion: reduce) {\n  .palla {\n    animation: none;\n  }\n}"), [
            dBlanks(pFill(), "@media (___: reduce) {\n  .palla { ___: none; }\n}", ['prefers-reduced-motion', 'animation'], ['max-width', 'display', 'motion'], L('Una riga di rispetto per chi soffre il movimento.', 'One line of respect for those who suffer from movement.')),
            dFlash(pFlash(), [[L('Animare `transform` è più leggero di `left`', 'Animating `transform` is lighter than `left`'), true], [L('`opacity` è economica da animare', '`opacity` is cheap to animate'), true], [L('Più animazioni ci sono, meglio è', 'The more animations, the better'), false], [L('Il movimento può far stare male', 'Movement can make people feel ill'), true], [L('Animare `height` è gratis', 'Animating `height` is free'), false]], L('Bella, leggera e rispettosa: così si anima.', 'Beautiful, light and respectful: that is how to animate.')),
        ]),
    ],
];
