<?php
/** Animations with CSS: transitions, transforms, keyframes and how to make them feel good, then 3D, entry and exit, scroll-driven animations and view transitions. */

$page = '<div class="card">Una card</div>
<button class="btn">Inizia</button>
<div class="palla"></div>';

return [
    'id' => 'css-animations', 'family' => 'css', 'after' => 'css-intermediate',
    'title' => L('Animazioni con CSS', 'CSS Animations'), 'short' => L('Animazioni', 'Animations'), 'level' => L('Avanzato', 'Advanced'),
    'desc' => L('Transizioni, trasformazioni e 3D, keyframes, tempi, entrate e uscite, animazioni legate allo scorrimento, transizioni di vista e animazioni rispettose.', 'Transitions, transforms and 3D, keyframes, timing, entry and exit, scroll-driven animations, view transitions and respectful animation.'),
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
            Q('t3', L('Si può animare `display`?', 'Can you animate `display`?'), [L('Di solito no', 'Usually not'), L('Sempre', 'Always'), L('Solo in rosso', 'Only in red'), L('Solo nei link', 'Only in links')], 0, L('Si anima ciò che ha valori intermedi, come `opacity`. `display` non ne ha: al massimo, con `transition-behavior: allow-discrete`, si sceglie quando scatta.', 'You animate what has in-between values, like `opacity`. `display` has none: at most, with `transition-behavior: allow-discrete`, you choose when it flips.')),
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
        lesson('individual-3d', L('Trasformazioni separate e 3D', 'Separate transforms and 3D'), [
            P('Oltre a `transform` esistono le proprietà separate `translate`, `rotate` e `scale`: ognuna si cambia e si anima da sola, senza riscrivere le altre. Il browser le applica sempre in quest’ordine: spostamento, rotazione, scala.', 'Besides `transform` there are the separate properties `translate`, `rotate` and `scale`: each changes and animates on its own, without rewriting the others. The browser always applies them in this order: translation, rotation, scale.'),
            C('.card {
  rotate: 5deg;
  transition: scale .2s, translate .2s;
}
.card:hover { scale: 1.05; translate: 0 -4px; } /* la rotazione resta */

.scena { perspective: 600px; }
.moneta { transition: rotate .6s; }
.moneta:hover { rotate: y 180deg; }
.moneta .retro { backface-visibility: hidden; }', 'css'),
            P('`perspective` sul genitore dà profondità alla scena: più il valore è piccolo, più l’effetto è forte. `rotate: y 180deg` gira l’elemento attorno all’asse verticale, come una moneta; `backface-visibility: hidden` nasconde il lato che finisce sul retro.', '`perspective` on the parent gives the scene depth: the smaller the value, the stronger the effect. `rotate: y 180deg` turns the element around the vertical axis, like a coin; `backface-visibility: hidden` hides the side that ends up at the back.'),
        ], [
            Q('i1', L('Che vantaggio hanno `translate`, `rotate` e `scale` separate?', 'What is the advantage of separate `translate`, `rotate` and `scale`?'), [L('Sono più veloci di `opacity`', 'They are faster than `opacity`'), L('Funzionano senza unità di misura', 'They work without units'), L('Ognuna si cambia senza riscrivere le altre', 'Each one changes without rewriting the others'), L('Spostano anche gli elementi vicini', 'They also move nearby elements')], 2, L('Con `transform` dovresti ripetere tutta la lista di funzioni.', 'With `transform` you would have to repeat the whole list of functions.')),
            Q('i2', L('Che cosa succede se riduci `perspective` da 1000px a 300px?', 'What happens if you reduce `perspective` from 1000px to 300px?'), [L('L’effetto 3D diventa più forte', 'The 3D effect gets stronger'), L('L’effetto 3D sparisce', 'The 3D effect disappears'), L('L’elemento si rimpicciolisce', 'The element shrinks'), L('Niente, conta solo sui telefoni', 'Nothing, it only matters on phones')], 0, L('È come guardare la scena da più vicino.', 'It is like looking at the scene from closer up.')),
            Q('i3', L('Che cosa fa `rotate: y 180deg`?', 'What does `rotate: y 180deg` do?'), [L('Ruota nel piano dello schermo', 'It rotates in the plane of the screen'), L('Sposta l’elemento in verticale', 'It moves the element vertically'), L('Capovolge solo il testo', 'It flips only the text'), L('Gira l’elemento attorno all’asse verticale', 'It turns the element around the vertical axis')], 3, L('Come una moneta che mostra il retro.', 'Like a coin showing its back.')),
        ], task('css', L('Dai a `.card` `rotate: 10deg` e una `transition` su `scale`; su `.card:hover` imposta `scale: 1.1`.', 'Give `.card` `rotate: 10deg` and a `transition` on `scale`; on `.card:hover` set `scale: 1.1`.'), '', [css('.card', 'rotate', '10deg', L('La card è ruotata', 'The card is rotated')), re('\.card\s*\{[^}]*transition\s*:[^;}]*\bscale\b', L('La scala ha una transizione', 'The scale has a transition')), re('\.card:hover\s*\{[^}]*\bscale\s*:\s*1\.1', L('Cresce al passaggio', 'It grows on hover'))], $page, ".card {\n  rotate: 10deg;\n  transition: scale .2s;\n}\n.card:hover {\n  scale: 1.1;\n}"), [
            dMatch(pMatch(), [['translate', L('Sposta', 'Moves')], ['rotate', L('Ruota', 'Rotates')], ['scale', L('Ingrandisce', 'Scales')], ['perspective', L('Profondità della scena', 'Depth of the scene')], ['backface-visibility', L('Mostra o nasconde il retro', 'Shows or hides the back')]], L('Proprietà separate, animazioni più semplici.', 'Separate properties, simpler animations.')),
            dBug(pBug(), ['.scena {', '  perspective: 600px;', '}', '.moneta:hover {', '  rotate: y 180;', '}'], 4, '  rotate: y 180deg;', L('Un angolo vuole la sua unità: `deg`.', 'An angle needs its unit: `deg`.')),
        ]),
        lesson('entry-exit', L('Animare entrate e uscite', 'Animating entry and exit'), [
            P('Un elemento che compare (passa da `display: none` a visibile, o viene appena aggiunto) non ha uno stato «prima», quindi una transizione non saprebbe da dove partire. `@starting-style` gli dà quello stato di partenza. `transition-behavior: allow-discrete`, o la parola `allow-discrete` nella transizione, fa scattare `display` al momento giusto: all’inizio quando entra, alla fine quando esce.', 'An element that appears (goes from `display: none` to visible, or has just been added) has no “before” state, so a transition would not know where to start. `@starting-style` gives it that starting state. `transition-behavior: allow-discrete`, or the word `allow-discrete` in the transition, makes `display` flip at the right moment: at the start when entering, at the end when leaving.'),
            C('.avviso {
  opacity: 1;
  translate: 0 0;
  transition: opacity .3s, translate .3s, display .3s allow-discrete;
}
.avviso[hidden] {
  display: none;
  opacity: 0;
}
@starting-style {
  .avviso { opacity: 0; translate: 0 20px; }
}', 'css'),
            P('Quando togli `hidden`, l’avviso parte trasparente e 20px più in basso e arriva al suo posto; quando lo rimetti, svanisce e solo alla fine sparisce davvero. Nei browser che non conoscono queste regole l’elemento compare e scompare di colpo: nessun danno.', 'When you remove `hidden`, the notice starts transparent and 20px lower and moves into place; when you add it back, it fades and only disappears for real at the end. In browsers that do not know these rules the element appears and disappears at once: no harm done.'),
        ], [
            Q('e1', L('Che cosa definisce `@starting-style`?', 'What does `@starting-style` define?'), [L('Lo stile della pagina all’apertura', 'The page style when it opens'), L('Lo stato da cui parte la transizione d’entrata', 'The state the entry transition starts from'), L('L’ultima tappa di un’animazione', 'The last stage of an animation'), L('Lo stile di stampa', 'The print style')], 1, L('Vale solo nel primo istante in cui l’elemento viene disegnato.', 'It only applies at the first moment the element is drawn.')),
            Q('e2', L('Perché serve `allow-discrete` su `display`?', 'Why does `display` need `allow-discrete`?'), [L('Per animare `display` a valori intermedi', 'To animate `display` through in-between values'), L('Per rendere la transizione più veloce', 'To make the transition faster'), L('Per far scattare `display` al momento giusto', 'To make `display` flip at the right moment'), L('Per nascondere l’elemento ai lettori di schermo', 'To hide the element from screen readers')], 2, L('`display` non ha valori intermedi: si sceglie solo quando cambia.', '`display` has no in-between values: you only choose when it changes.')),
            Q('e3', L('Che cosa succede nei browser che non supportano `@starting-style`?', 'What happens in browsers that do not support `@starting-style`?'), [L('La pagina va in errore', 'The page breaks'), L('L’elemento non compare mai', 'The element never appears'), L('Il CSS viene ignorato tutto', 'All the CSS is ignored'), L('L’elemento compare senza animazione', 'The element appears without animation')], 3, L('È un miglioramento progressivo.', 'It is a progressive enhancement.')),
        ], task('css', L('Scrivi un `@starting-style` che dia a `.card` `opacity: 0`, e dai a `.card` una `transition` su `opacity` di `.4s`.', 'Write a `@starting-style` that gives `.card` `opacity: 0`, and give `.card` a `transition` on `opacity` of `.4s`.'), '', [re('@starting-style\s*\{[^{}]*\.card\s*\{[^}]*opacity\s*:\s*0', L('C’è lo stato di partenza', 'The starting state is there')), re('\.card\s*\{[^}]*transition\s*:[^;}]*opacity\s+0?\.4s', L('L’opacità ha una transizione', 'Opacity has a transition'))], $page, "@starting-style {\n  .card { opacity: 0; }\n}\n.card {\n  transition: opacity .4s;\n}"), [
            dBlanks(pFill(), ".avviso {\n  transition: opacity .3s, display .3s ___;\n}\n___ {\n  .avviso { opacity: 0; }\n}", ['allow-discrete', '@starting-style'], ['ease-out', '@keyframes', '@media'], L('`display` che scatta al momento giusto e uno stato di partenza.', '`display` flipping at the right moment and a starting state.')),
            dFlash(pFlash(), [[L('Un elemento appena comparso non ha uno stato «prima»', 'A newly shown element has no “before” state'), true], [L('Con `allow-discrete`, `display` sfuma tra `none` e `block`', 'With `allow-discrete`, `display` fades between `none` and `block`'), false], [L('`@starting-style` funziona anche con i popover', '`@starting-style` also works with popovers'), true], [L('Per animare un’entrata serve per forza JavaScript', 'Animating an entry always needs JavaScript'), false]], L('Entrate e uscite morbide, scritte solo in CSS.', 'Smooth entries and exits, written in CSS alone.')),
        ]),
        lesson('scroll-driven', L('Animazioni guidate dallo scorrimento', 'Scroll-driven animations'), [
            P('Con `animation-timeline` un’animazione segue lo scorrimento invece dell’orologio. `scroll()` la lega allo scorrimento di un contenitore, per esempio per una barra che mostra quanto hai letto; `view()` al passaggio dell’elemento dentro lo schermo, per farlo apparire mentre entra.', 'With `animation-timeline` an animation follows scrolling instead of the clock. `scroll()` ties it to a container’s scrolling, for example for a bar showing how much you have read; `view()` to the element passing through the screen, to make it appear as it enters.'),
            C('@keyframes cresci { from { scale: 0 1; } to { scale: 1 1; } }
.progresso {
  position: fixed; top: 0; left: 0;
  width: 100%; height: 4px;
  background: #FF5B2E;
  transform-origin: left;
  animation: cresci linear both;
  animation-timeline: scroll();
}

@keyframes appari { from { opacity: 0; translate: 0 40px; } }
.card {
  animation: appari linear both;
  animation-timeline: view();
  animation-range: entry 0% cover 30%;
}', 'css'),
            P('Scrivi `animation-timeline` **dopo** la scorciatoia `animation`, che altrimenti la riporta al valore predefinito. Non serve una durata: la dà lo scorrimento. I browser principali le supportano solo nelle versioni più recenti, quindi proteggile con `@supports (animation-timeline: scroll())` e rispetta `prefers-reduced-motion`.', 'Write `animation-timeline` **after** the `animation` shorthand, which would otherwise reset it to its default. No duration is needed: scrolling provides it. The main browsers support them only in their latest versions, so guard them with `@supports (animation-timeline: scroll())` and respect `prefers-reduced-motion`.'),
        ], [
            Q('d1', L('Qual è la differenza tra `scroll()` e `view()`?', 'What is the difference between `scroll()` and `view()`?'), [L('`scroll()` segue lo scorrimento del contenitore, `view()` il passaggio dell’elemento nello schermo', '`scroll()` follows the container’s scrolling, `view()` the element passing through the screen'), L('Sono due nomi della stessa cosa', 'They are two names for the same thing'), L('`view()` funziona solo con i video', '`view()` only works with videos'), L('`scroll()` serve solo in orizzontale', '`scroll()` is only for horizontal scrolling')], 0, L('Barra di avanzamento con `scroll()`, comparse con `view()`.', 'A progress bar with `scroll()`, reveals with `view()`.')),
            Q('d2', L('Perché `animation-timeline` va scritta dopo `animation`?', 'Why must `animation-timeline` come after `animation`?'), [L('Per una questione di stile', 'As a matter of style'), L('Perché la scorciatoia `animation` la azzera', 'Because the `animation` shorthand resets it'), L('Perché il browser legge il CSS al contrario', 'Because the browser reads CSS backwards'), L('Non importa l’ordine', 'The order does not matter')], 1, L('Una scorciatoia reimposta tutte le sue sotto-proprietà.', 'A shorthand resets all of its sub-properties.')),
            Q('d3', L('Che durata serve a un’animazione guidata dallo scorrimento?', 'What duration does a scroll-driven animation need?'), [L('Almeno 1 secondo', 'At least 1 second'), L('Esattamente 100ms', 'Exactly 100ms'), L('Una durata per ogni pagina', 'One duration per page'), L('Nessuna: il tempo lo sostituisce lo scorrimento', 'None: scrolling replaces time')], 3, L('Di solito si usa la curva `linear`, per seguire il dito alla pari.', 'The `linear` curve is usually used, to follow the finger evenly.')),
        ], task('css', L('Scrivi `@keyframes cresci` e applicalo a `.palla` con `animation: cresci linear both` seguita da `animation-timeline: scroll()`.', 'Write `@keyframes cresci` and apply it to `.palla` with `animation: cresci linear both` followed by `animation-timeline: scroll()`.'), '', [re('@keyframes\s+cresci', L('I keyframes esistono', 'The keyframes exist')), re('\.palla\s*\{[^}]*animation\s*:[^;}]*cresci[^}]*animation-timeline\s*:\s*scroll\(\s*\)', L('La palla segue lo scorrimento', 'The ball follows scrolling'))], $page, "@keyframes cresci {\n  from { scale: 0; }\n  to { scale: 1; }\n}\n.palla {\n  animation: cresci linear both;\n  animation-timeline: scroll();\n}"), [
            dOrder(pSort(), ['.progresso {', '  transform-origin: left;', '  animation: cresci linear both;', '  animation-timeline: scroll();', '}'], L('La scorciatoia prima, la linea temporale dopo.', 'The shorthand first, the timeline after.')),
            dMatch(pMatch(), [['scroll()', L('Scorrimento del contenitore', 'Container scrolling')], ['view()', L('Passaggio dell’elemento nello schermo', 'The element passing through the screen')], ['animation-range', L('Da dove a dove gira', 'From where to where it runs')], ['@supports', L('Protegge i browser vecchi', 'Guards old browsers')]], L('Il dito che scorre diventa l’orologio.', 'The scrolling finger becomes the clock.')),
        ]),
        lesson('view-transitions', L('Transizioni di vista', 'View transitions'), [
            P('Le **View Transitions** animano il passaggio tra due stati della pagina: il browser fotografa lo stato vecchio, tu aggiorni il contenuto, e lui sfuma dall’uno all’altro. Nella stessa pagina si avviano da JavaScript con `document.startViewTransition()`; tra due pagine dello stesso sito basta una regola CSS su entrambe.', '**View Transitions** animate the change between two states of the page: the browser snapshots the old state, you update the content, and it fades from one to the other. Within a page you start them from JavaScript with `document.startViewTransition()`; between two pages of the same site one CSS rule on both is enough.'),
            C('@view-transition { navigation: auto; }

.foto-prodotto { view-transition-name: foto; }

::view-transition-old(root),
::view-transition-new(root) {
  animation-duration: .4s;
}', 'css'),
            C('document.startViewTransition(() => mostraDettaglio());', 'js'),
            P('Un elemento con lo stesso `view-transition-name` nei due stati «vola» dalla vecchia posizione alla nuova. Ogni nome deve essere unico nella pagina. Nei browser senza supporto la pagina cambia normalmente, quindi è un miglioramento sicuro; ricordati di ridurlo con `prefers-reduced-motion`.', 'An element with the same `view-transition-name` in both states “flies” from the old position to the new one. Each name must be unique on the page. In browsers without support the page changes normally, so it is a safe enhancement; remember to tone it down with `prefers-reduced-motion`.'),
        ], [
            Q('v1', L('Che cosa fa `view-transition-name` su un elemento?', 'What does `view-transition-name` do on an element?'), [L('Gli dà un titolo per i motori di ricerca', 'It gives it a title for search engines'), L('Lo nasconde durante la transizione', 'It hides it during the transition'), L('Lo fa animare dalla vecchia alla nuova posizione', 'It makes it animate from the old position to the new one'), L('Lo carica prima degli altri', 'It loads it before the others')], 2, L('Lo stesso nome lega i due stati dell’elemento.', 'The same name links the element’s two states.')),
            Q('v2', L('Come si attivano le transizioni tra due pagine dello stesso sito?', 'How do you turn on transitions between two pages of the same site?'), ['@view-transition { navigation: auto; }', '<meta name="transition">', 'transition: page 1s;', '@keyframes page'], 0, L('La regola va nel CSS di entrambe le pagine.', 'The rule goes in the CSS of both pages.')),
            Q('v3', L('Che cosa succede in un browser senza supporto?', 'What happens in a browser without support?'), [L('La pagina non si apre', 'The page does not open'), L('La pagina cambia normalmente, senza animazione', 'The page changes normally, without animation'), L('Compare un avviso all’utente', 'A warning appears for the user'), L('Il sito diventa lento', 'The site becomes slow')], 1, L('Nessun danno: solo niente effetto.', 'No harm: just no effect.')),
        ], task('css', L('Attiva le transizioni tra pagine con `@view-transition { navigation: auto; }` e dai a `.card` `view-transition-name: scheda`.', 'Turn on transitions between pages with `@view-transition { navigation: auto; }` and give `.card` `view-transition-name: scheda`.'), '', [re('@view-transition\s*\{[^}]*navigation\s*:\s*auto', L('Le transizioni tra pagine sono attive', 'Transitions between pages are on')), re('\.card\s*\{[^}]*view-transition-name\s*:\s*scheda', L('La card ha un nome di transizione', 'The card has a transition name'))], $page, "@view-transition {\n  navigation: auto;\n}\n.card {\n  view-transition-name: scheda;\n}"), [
            dMatch(pMatch(), [['startViewTransition()', L('Transizione nella stessa pagina', 'Transition within a page')], ['@view-transition', L('Transizione tra pagine', 'Transition between pages')], ['view-transition-name', L('L’elemento che vola', 'The element that flies')], ['::view-transition-old()', L('La foto dello stato vecchio', 'The snapshot of the old state')], ['::view-transition-new()', L('Lo stato nuovo', 'The new state')]], L('Il browser fa le foto, tu decidi il movimento.', 'The browser takes the snapshots, you choose the movement.')),
            dFlash(pFlash(), [[L('Due elementi della stessa pagina possono avere lo stesso `view-transition-name`', 'Two elements on the same page can share a `view-transition-name`'), false], [L('Senza supporto il sito funziona lo stesso', 'Without support the site still works'), true], [L('Le View Transitions richiedono un framework', 'View Transitions need a framework'), false], [L('La durata si cambia con gli pseudo-elementi `::view-transition-*`', 'The duration is changed with the `::view-transition-*` pseudo-elements'), true]], L('Un effetto da app nativa, con poche righe.', 'A native-app feel, in a few lines.')),
        ]),
    ],
];
