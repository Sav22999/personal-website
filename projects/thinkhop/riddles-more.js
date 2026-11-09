/*
 * ThinkHop — more riddles, the silly and the devious. Same format as riddles.js (the RIGHT OPTION COMES FIRST), plus
 * d: the difficulty, from 0.00 to 1.99. `R` keeps each entry short: R(id, d, options shared by all languages or null, texts).
 */
(function (root) {
    'use strict';

    const R = (id, d, o, en, it, es, fr, de) => root.ThinkHopRiddles.push(Object.assign({id, d, t: {en, it, es, fr, de}}, o ? {o} : {}));

    R('n01', .06, null,
        ['What gets wetter the more it dries?', 'A towel dries you by soaking up the water.', ['A towel', 'A sponge in the sun', 'Ice', 'Rain']],
        ['Che cosa si bagna sempre di più man mano che asciuga?', 'Un asciugamano asciuga assorbendo l’acqua.', ['L’asciugamano', 'Una spugna al sole', 'Il ghiaccio', 'La pioggia']],
        ['¿Qué se moja cada vez más cuanto más seca?', 'Una toalla seca absorbiendo el agua.', ['La toalla', 'Una esponja al sol', 'El hielo', 'La lluvia']],
        ['Qu’est-ce qui se mouille de plus en plus à mesure qu’il sèche ?', 'Une serviette sèche en absorbant l’eau.', ['La serviette', 'Une éponge au soleil', 'La glace', 'La pluie']],
        ['Was wird immer nasser, je mehr es trocknet?', 'Ein Handtuch trocknet, indem es das Wasser aufnimmt.', ['Das Handtuch', 'Ein Schwamm in der Sonne', 'Das Eis', 'Der Regen']]);
    R('n02', .08, null,
        ['What do cows drink?', 'Cows give milk, but they drink water.', ['Water', 'Milk', 'Cream', 'Orange juice']],
        ['Che cosa bevono le mucche?', 'Le mucche fanno il latte, ma bevono acqua.', ['Acqua', 'Latte', 'Panna', 'Aranciata']],
        ['¿Qué beben las vacas?', 'Las vacas dan leche, pero beben agua.', ['Agua', 'Leche', 'Nata', 'Zumo de naranja']],
        ['Que boivent les vaches ?', 'Les vaches donnent du lait, mais elles boivent de l’eau.', ['De l’eau', 'Du lait', 'De la crème', 'Du jus d’orange']],
        ['Was trinken Kühe?', 'Kühe geben Milch, aber sie trinken Wasser.', ['Wasser', 'Milch', 'Sahne', 'Orangensaft']]);
    R('n03', .1, null,
        ['Before Mount Everest was discovered, what was the highest mountain in the world?', 'It was already the highest even before anyone discovered it.', ['Mount Everest', 'K2', 'Mont Blanc', 'Kilimanjaro']],
        ['Prima che venisse scoperto l’Everest, qual era la montagna più alta del mondo?', 'Era già la più alta anche prima che qualcuno la scoprisse.', ['L’Everest', 'Il K2', 'Il Monte Bianco', 'Il Kilimangiaro']],
        ['Antes de que se descubriera el Everest, ¿cuál era la montaña más alta del mundo?', 'Ya era la más alta aunque nadie la hubiera descubierto aún.', ['El Everest', 'El K2', 'El Mont Blanc', 'El Kilimanjaro']],
        ['Avant que l’Everest ne soit découvert, quelle était la plus haute montagne du monde ?', 'C’était déjà la plus haute, même avant d’être découverte.', ['L’Everest', 'Le K2', 'Le Mont Blanc', 'Le Kilimandjaro']],
        ['Welcher war der höchste Berg der Welt, bevor der Mount Everest entdeckt wurde?', 'Er war auch schon vor seiner Entdeckung der höchste.', ['Der Mount Everest', 'Der K2', 'Der Mont Blanc', 'Der Kilimandscharo']]);
    R('n04', .12, ['0', '1', '2', '7'],
        ['How many animals of each kind did Moses take on the ark?', 'It was Noah, not Moses.'],
        ['Quanti animali di ogni specie portò Mosè sull’arca?', 'Sull’arca c’era Noè, non Mosè.'],
        ['¿Cuántos animales de cada especie llevó Moisés en el arca?', 'Fue Noé, no Moisés.'],
        ['Combien d’animaux de chaque espèce Moïse a-t-il pris sur l’arche ?', 'C’était Noé, pas Moïse.'],
        ['Wie viele Tiere jeder Art nahm Moses mit auf die Arche?', 'Das war Noah, nicht Moses.']);
    R('n05', .14, null,
        ['An electric train heads north and the wind blows south. Which way does the smoke go?', 'Electric trains make no smoke.', ['There is no smoke', 'North', 'South', 'Straight up']],
        ['Un treno elettrico va a nord e il vento soffia verso sud. Da che parte va il fumo?', 'I treni elettrici non fanno fumo.', ['Non c’è fumo', 'A nord', 'A sud', 'Dritto in alto']],
        ['Un tren eléctrico va al norte y el viento sopla hacia el sur. ¿Hacia dónde va el humo?', 'Los trenes eléctricos no echan humo.', ['No hay humo', 'Al norte', 'Al sur', 'Hacia arriba']],
        ['Un train électrique va au nord et le vent souffle vers le sud. Où va la fumée ?', 'Les trains électriques ne font pas de fumée.', ['Il n’y a pas de fumée', 'Au nord', 'Au sud', 'Tout droit vers le haut']],
        ['Ein Elektrozug fährt nach Norden, der Wind weht nach Süden. Wohin zieht der Rauch?', 'Elektrozüge machen keinen Rauch.', ['Es gibt keinen Rauch', 'Nach Norden', 'Nach Süden', 'Gerade nach oben']]);
    R('n06', .16, null,
        ['How much dirt is there in a hole 2 metres long, 2 metres wide and 2 metres deep?', 'It’s a hole: the dirt has been taken out.', ['None: it’s a hole', '8 cubic metres', '6 cubic metres', '4 cubic metres']],
        ['Quanta terra c’è in una buca lunga 2 metri, larga 2 metri e profonda 2 metri?', 'È una buca: la terra è stata tolta.', ['Nessuna: è una buca', '8 metri cubi', '6 metri cubi', '4 metri cubi']],
        ['¿Cuánta tierra hay en un agujero de 2 metros de largo, 2 de ancho y 2 de fondo?', 'Es un agujero: la tierra se ha sacado.', ['Ninguna: es un agujero', '8 metros cúbicos', '6 metros cúbicos', '4 metros cúbicos']],
        ['Combien de terre y a-t-il dans un trou de 2 mètres de long, 2 de large et 2 de profondeur ?', 'C’est un trou : la terre a été enlevée.', ['Aucune : c’est un trou', '8 mètres cubes', '6 mètres cubes', '4 mètres cubes']],
        ['Wie viel Erde ist in einem Loch, das 2 Meter lang, 2 Meter breit und 2 Meter tief ist?', 'Es ist ein Loch: Die Erde wurde herausgeholt.', ['Gar keine: Es ist ein Loch', '8 Kubikmeter', '6 Kubikmeter', '4 Kubikmeter']]);
    R('n07', .18, null,
        ['You enter a dark cabin with one match. There is a candle, a stove and an oil lamp. What do you light first?', 'Without the match, nothing else lights.', ['The match', 'The candle', 'The stove', 'The lamp']],
        ['Entri in una baita buia con un solo fiammifero. Ci sono una candela, una stufa e una lampada a olio. Che cosa accendi per primo?', 'Senza il fiammifero non si accende nient’altro.', ['Il fiammifero', 'La candela', 'La stufa', 'La lampada']],
        ['Entras en una cabaña oscura con una sola cerilla. Hay una vela, una estufa y un candil. ¿Qué enciendes primero?', 'Sin la cerilla no se enciende nada más.', ['La cerilla', 'La vela', 'La estufa', 'El candil']],
        ['Tu entres dans une cabane sombre avec une seule allumette. Il y a une bougie, un poêle et une lampe à huile. Qu’allumes-tu d’abord ?', 'Sans l’allumette, rien d’autre ne s’allume.', ['L’allumette', 'La bougie', 'Le poêle', 'La lampe']],
        ['Du betrittst eine dunkle Hütte mit einem einzigen Streichholz. Dort stehen eine Kerze, ein Ofen und eine Öllampe. Was zündest du zuerst an?', 'Ohne das Streichholz brennt nichts anderes.', ['Das Streichholz', 'Die Kerze', 'Den Ofen', 'Die Lampe']]);
    R('n08', .2, ['0', '9', '10', '1'],
        ['Ten birds sit on a wire. A hunter shoots one. How many are left on the wire?', 'The noise scares the others away.'],
        ['Dieci uccelli sono su un filo. Un cacciatore ne colpisce uno. Quanti ne restano sul filo?', 'Il rumore fa volare via tutti gli altri.'],
        ['Diez pájaros están en un cable. Un cazador abate uno. ¿Cuántos quedan en el cable?', 'El ruido espanta a los demás.'],
        ['Dix oiseaux sont sur un fil. Un chasseur en abat un. Combien en reste-t-il sur le fil ?', 'Le bruit fait fuir tous les autres.'],
        ['Zehn Vögel sitzen auf einem Draht. Ein Jäger schießt einen ab. Wie viele bleiben auf dem Draht?', 'Der Knall vertreibt alle anderen.']);
    R('n09', .22, null,
        ['Can you jump higher than a house?', 'Of course: houses can’t jump.', ['Yes: houses don’t jump', 'No, never', 'Only a small house', 'Only with a trampoline']],
        ['Sai saltare più in alto di una casa?', 'Certo: le case non saltano.', ['Sì: le case non saltano', 'No, mai', 'Solo una casa piccola', 'Solo col tappeto elastico']],
        ['¿Puedes saltar más alto que una casa?', 'Claro: las casas no saltan.', ['Sí: las casas no saltan', 'No, nunca', 'Solo una casa pequeña', 'Solo con cama elástica']],
        ['Sais-tu sauter plus haut qu’une maison ?', 'Bien sûr : les maisons ne sautent pas.', ['Oui : les maisons ne sautent pas', 'Non, jamais', 'Seulement une petite maison', 'Seulement avec un trampoline']],
        ['Kannst du höher springen als ein Haus?', 'Klar: Häuser springen nicht.', ['Ja: Häuser springen nicht', 'Nein, nie', 'Nur ein kleines Haus', 'Nur mit Trampolin']]);
    R('n10', .24, null,
        ['Mary’s father has five daughters: Nana, Nene, Nini and Nono. What is the name of the fifth?', 'It says so at the start: Mary’s father.', ['Mary', 'Nunu', 'Nina', 'You can’t know']],
        ['Il padre di Maria ha cinque figlie: Nana, Nene, Nini e Nono. Come si chiama la quinta?', 'Lo dice all’inizio: il padre di Maria.', ['Maria', 'Nunu', 'Nina', 'Non si può sapere']],
        ['El padre de María tiene cinco hijas: Nana, Nene, Nini y Nono. ¿Cómo se llama la quinta?', 'Lo dice al principio: el padre de María.', ['María', 'Nunu', 'Nina', 'No se puede saber']],
        ['Le père de Marie a cinq filles : Nana, Nene, Nini et Nono. Comment s’appelle la cinquième ?', 'C’est dit au début : le père de Marie.', ['Marie', 'Nunu', 'Nina', 'Impossible à savoir']],
        ['Marias Vater hat fünf Töchter: Nana, Nene, Nini und Nono. Wie heißt die fünfte?', 'Es steht am Anfang: Marias Vater.', ['Maria', 'Nunu', 'Nina', 'Das kann man nicht wissen']]);
    R('n11', .26, ['10', '5', '7', '8'],
        ['An aquarium holds 10 fish. 2 drown, 3 swim away and 1 falls asleep. How many fish are in the aquarium?', 'Fish don’t drown, and swimming away doesn’t leave an aquarium.'],
        ['In un acquario ci sono 10 pesci. 2 annegano, 3 scappano a nuoto e 1 si addormenta. Quanti pesci ci sono nell’acquario?', 'I pesci non annegano, e nuotando non si esce da un acquario.'],
        ['En un acuario hay 10 peces. 2 se ahogan, 3 se van nadando y 1 se duerme. ¿Cuántos peces hay en el acuario?', 'Los peces no se ahogan, y nadando no se sale de un acuario.'],
        ['Un aquarium contient 10 poissons. 2 se noient, 3 s’enfuient à la nage et 1 s’endort. Combien de poissons y a-t-il dans l’aquarium ?', 'Les poissons ne se noient pas, et on ne sort pas d’un aquarium à la nage.'],
        ['In einem Aquarium sind 10 Fische. 2 ertrinken, 3 schwimmen weg, 1 schläft ein. Wie viele Fische sind im Aquarium?', 'Fische ertrinken nicht, und aus einem Aquarium schwimmt man nicht weg.']);
    R('n12', .28, ['6', '10', '9', '5'],
        ['A mother has 5 daughters and each daughter has one brother. How many children does she have?', 'They all share the same brother: 5 + 1.'],
        ['Una madre ha 5 figlie e ogni figlia ha un fratello. Quanti figli ha in tutto?', 'Il fratello è lo stesso per tutte: 5 + 1.'],
        ['Una madre tiene 5 hijas y cada hija tiene un hermano. ¿Cuántos hijos tiene en total?', 'El hermano es el mismo para todas: 5 + 1.'],
        ['Une mère a 5 filles et chaque fille a un frère. Combien a-t-elle d’enfants ?', 'C’est le même frère pour toutes : 5 + 1.'],
        ['Eine Mutter hat 5 Töchter und jede Tochter hat einen Bruder. Wie viele Kinder hat sie?', 'Alle haben denselben Bruder: 5 + 1.']);
    R('n13', .3, ['4', '12', '3', '16'],
        ['Four cats sit in the corners of a room. Each cat sees three cats. How many cats are in the room?', 'Each one simply sees the other three.'],
        ['Quattro gatti sono negli angoli di una stanza. Ogni gatto vede tre gatti. Quanti gatti ci sono?', 'Ognuno vede semplicemente gli altri tre.'],
        ['Cuatro gatos están en las esquinas de una habitación. Cada gato ve tres gatos. ¿Cuántos gatos hay?', 'Cada uno ve simplemente a los otros tres.'],
        ['Quatre chats sont dans les coins d’une pièce. Chaque chat voit trois chats. Combien y a-t-il de chats ?', 'Chacun voit simplement les trois autres.'],
        ['Vier Katzen sitzen in den Ecken eines Zimmers. Jede sieht drei Katzen. Wie viele Katzen sind im Zimmer?', 'Jede sieht einfach die drei anderen.']);
    R('n14', .32, null,
        ['One shirt dries in the sun in one hour. How long do three shirts take, hung side by side?', 'They all dry at the same time.', ['One hour', 'Three hours', 'Twenty minutes', 'Two hours']],
        ['Una camicia al sole si asciuga in un’ora. Quanto ci mettono tre camicie stese una accanto all’altra?', 'Si asciugano tutte insieme.', ['Un’ora', 'Tre ore', 'Venti minuti', 'Due ore']],
        ['Una camisa se seca al sol en una hora. ¿Cuánto tardan tres camisas tendidas una al lado de otra?', 'Se secan todas a la vez.', ['Una hora', 'Tres horas', 'Veinte minutos', 'Dos horas']],
        ['Une chemise sèche au soleil en une heure. Combien de temps pour trois chemises étendues côte à côte ?', 'Elles sèchent toutes en même temps.', ['Une heure', 'Trois heures', 'Vingt minutes', 'Deux heures']],
        ['Ein Hemd trocknet in der Sonne in einer Stunde. Wie lange brauchen drei Hemden nebeneinander?', 'Sie trocknen alle gleichzeitig.', ['Eine Stunde', 'Drei Stunden', 'Zwanzig Minuten', 'Zwei Stunden']]);
    R('n15', .34, null,
        ['You pass the runner in last place. What place are you in now?', 'You can’t: nobody is behind the last runner.', ['It can’t happen', 'Last', 'Second to last', 'First']],
        ['Superi l’ultimo in classifica in una corsa. In che posizione sei?', 'Non si può: dietro all’ultimo non c’è nessuno.', ['È impossibile', 'Ultimo', 'Penultimo', 'Primo']],
        ['Adelantas al último de la carrera. ¿En qué puesto vas?', 'No se puede: detrás del último no hay nadie.', ['Es imposible', 'Último', 'Penúltimo', 'Primero']],
        ['Tu dépasses le dernier de la course. À quelle place es-tu ?', 'C’est impossible : personne n’est derrière le dernier.', ['C’est impossible', 'Dernier', 'Avant-dernier', 'Premier']],
        ['Du überholst den Letzten eines Rennens. Auf welchem Platz bist du?', 'Das geht nicht: Hinter dem Letzten ist niemand.', ['Das ist unmöglich', 'Letzter', 'Vorletzter', 'Erster']]);
    R('n16', .36, ['1', '5', '4', '25'],
        ['How many times can you subtract 5 from 25?', 'Once: after that it’s no longer 25.'],
        ['Quante volte puoi sottrarre 5 da 25?', 'Una: dopo non è più 25.'],
        ['¿Cuántas veces puedes restar 5 a 25?', 'Una: después ya no es 25.'],
        ['Combien de fois peux-tu soustraire 5 de 25 ?', 'Une fois : ensuite ce n’est plus 25.'],
        ['Wie oft kannst du 5 von 25 abziehen?', 'Einmal: Danach ist es nicht mehr 25.']);
    R('n17', .4, null,
        ['Two fathers and two sons go fishing. Each catches one fish, yet only three fish are caught. How?', 'Grandfather, father and son: the father is both.', ['They are three: grandfather, father, son', 'One fish escaped', 'One of them cheated', 'One fish was shared']],
        ['Due padri e due figli vanno a pesca. Ognuno prende un pesce, ma i pesci sono solo tre. Come mai?', 'Nonno, padre e figlio: il padre è entrambe le cose.', ['Sono tre: nonno, padre, figlio', 'Un pesce è scappato', 'Uno ha barato', 'Un pesce era in comune']],
        ['Dos padres y dos hijos van a pescar. Cada uno pesca un pez, pero solo hay tres peces. ¿Cómo es posible?', 'Abuelo, padre e hijo: el padre es ambas cosas.', ['Son tres: abuelo, padre, hijo', 'Un pez se escapó', 'Uno hizo trampa', 'Un pez era compartido']],
        ['Deux pères et deux fils vont à la pêche. Chacun attrape un poisson, mais il n’y a que trois poissons. Comment ?', 'Grand-père, père et fils : le père est les deux.', ['Ils sont trois : grand-père, père, fils', 'Un poisson s’est échappé', 'L’un a triché', 'Un poisson était partagé']],
        ['Zwei Väter und zwei Söhne gehen angeln. Jeder fängt einen Fisch, aber es sind nur drei Fische. Wie das?', 'Großvater, Vater und Sohn: Der Vater ist beides.', ['Es sind drei: Großvater, Vater, Sohn', 'Ein Fisch entkam', 'Einer hat geschummelt', 'Ein Fisch wurde geteilt']]);
    R('n18', .44, null,
        ['A horse is tied to a 3 metre rope. The hay is 5 metres away, yet it eats it. How?', 'Nobody said the rope was tied to anything else.', ['The other end of the rope is free', 'The rope stretches', 'The hay rolled closer', 'It has a long neck']],
        ['Un cavallo è legato a una corda di 3 metri. Il fieno è a 5 metri, eppure lo mangia. Come?', 'Nessuno ha detto che la corda fosse legata ad altro.', ['L’altro capo della corda è libero', 'La corda si allunga', 'Il fieno è rotolato vicino', 'Ha il collo lungo']],
        ['Un caballo está atado a una cuerda de 3 metros. El heno está a 5 metros y aun así se lo come. ¿Cómo?', 'Nadie dijo que la cuerda estuviera atada a otra cosa.', ['El otro extremo de la cuerda está suelto', 'La cuerda se estira', 'El heno rodó cerca', 'Tiene el cuello largo']],
        ['Un cheval est attaché à une corde de 3 mètres. Le foin est à 5 mètres et pourtant il le mange. Comment ?', 'Personne n’a dit que la corde était attachée à autre chose.', ['L’autre bout de la corde est libre', 'La corde s’allonge', 'Le foin a roulé plus près', 'Il a un long cou']],
        ['Ein Pferd ist an einem 3 Meter langen Seil festgebunden. Das Heu liegt 5 Meter entfernt, trotzdem frisst es davon. Wie?', 'Niemand hat gesagt, dass das Seil woanders befestigt ist.', ['Das andere Seilende ist frei', 'Das Seil dehnt sich', 'Das Heu rollte näher', 'Es hat einen langen Hals']]);
    R('n19', .48, null,
        ['Two girls have the same mother and father, were born on the same day of the same year, but aren’t twins. How?', 'There’s a third sister: they are triplets.', ['They are triplets', 'They were adopted', 'One was born abroad', 'It’s a mistake at the registry']],
        ['Due ragazze hanno la stessa madre e lo stesso padre, sono nate lo stesso giorno dello stesso anno, ma non sono gemelle. Come mai?', 'C’è una terza sorella: sono trigemine.', ['Sono trigemine', 'Sono adottate', 'Una è nata all’estero', 'È un errore dell’anagrafe']],
        ['Dos chicas tienen la misma madre y el mismo padre, nacieron el mismo día del mismo año, pero no son gemelas. ¿Cómo?', 'Hay una tercera hermana: son trillizas.', ['Son trillizas', 'Son adoptadas', 'Una nació en el extranjero', 'Es un error del registro']],
        ['Deux filles ont la même mère et le même père, sont nées le même jour de la même année, mais ne sont pas jumelles. Comment ?', 'Il y a une troisième sœur : ce sont des triplées.', ['Ce sont des triplées', 'Elles sont adoptées', 'L’une est née à l’étranger', 'C’est une erreur de l’état civil']],
        ['Zwei Mädchen haben dieselben Eltern und wurden am selben Tag desselben Jahres geboren, sind aber keine Zwillinge. Wie das?', 'Es gibt eine dritte Schwester: Sie sind Drillinge.', ['Sie sind Drillinge', 'Sie sind adoptiert', 'Eine wurde im Ausland geboren', 'Ein Fehler im Standesamt']]);
    R('n20', .52, ['2', '1', '0', '4'],
        ['How many sides does a circle have?', 'Two: the inside and the outside.'],
        ['Quanti lati ha un cerchio?', 'Due: quello interno e quello esterno.'],
        ['¿Cuántos lados tiene un círculo?', 'Dos: el de dentro y el de fuera.'],
        ['Combien de côtés a un cercle ?', 'Deux : l’intérieur et l’extérieur.'],
        ['Wie viele Seiten hat ein Kreis?', 'Zwei: die Innenseite und die Außenseite.']);
    R('n21', .56, null,
        ['How far can a dog run into a wood?', 'After halfway it’s running out of the wood.', ['Halfway', 'All the way', 'A kilometre', 'As far as it likes']],
        ['Fin dove può correre un cane dentro un bosco?', 'Dopo metà sta già correndo fuori dal bosco.', ['Fino a metà', 'Fino in fondo', 'Per un chilometro', 'Fin dove vuole']],
        ['¿Hasta dónde puede correr un perro adentrándose en un bosque?', 'Pasada la mitad ya está saliendo del bosque.', ['Hasta la mitad', 'Hasta el final', 'Un kilómetro', 'Hasta donde quiera']],
        ['Jusqu’où un chien peut-il courir dans un bois ?', 'Après la moitié, il en ressort déjà.', ['Jusqu’à la moitié', 'Jusqu’au bout', 'Un kilomètre', 'Aussi loin qu’il veut']],
        ['Wie weit kann ein Hund in einen Wald hineinlaufen?', 'Ab der Mitte läuft er schon wieder hinaus.', ['Bis zur Mitte', 'Bis ans Ende', 'Einen Kilometer', 'So weit er will']]);
    R('n22', .6, null,
        ['There are 5 apples in a basket and 5 children. Each child gets one apple, yet one apple stays in the basket. How?', 'The last child takes the apple with the basket.', ['The last child takes the basket too', 'One child gets none', 'An apple is cut in two', 'One apple is fake']],
        ['In un cesto ci sono 5 mele e ci sono 5 bambini. Ognuno riceve una mela, eppure una resta nel cesto. Come?', 'L’ultimo bambino prende la mela insieme al cesto.', ['L’ultimo prende anche il cesto', 'Un bambino resta senza', 'Una mela viene tagliata', 'Una mela è finta']],
        ['En una cesta hay 5 manzanas y hay 5 niños. Cada uno recibe una manzana, pero una se queda en la cesta. ¿Cómo?', 'El último niño coge la manzana con la cesta.', ['El último se lleva también la cesta', 'Un niño se queda sin', 'Una manzana se corta', 'Una manzana es falsa']],
        ['Un panier contient 5 pommes et il y a 5 enfants. Chacun reçoit une pomme, pourtant une reste dans le panier. Comment ?', 'Le dernier enfant prend la pomme avec le panier.', ['Le dernier prend aussi le panier', 'Un enfant n’en a pas', 'Une pomme est coupée', 'Une pomme est fausse']],
        ['In einem Korb liegen 5 Äpfel, und da sind 5 Kinder. Jedes bekommt einen Apfel, trotzdem bleibt einer im Korb. Wie?', 'Das letzte Kind nimmt den Apfel samt Korb.', ['Das letzte Kind nimmt auch den Korb', 'Ein Kind geht leer aus', 'Ein Apfel wird geteilt', 'Ein Apfel ist unecht']]);
    R('n23', .64, ['70', '25', '40', '15'],
        ['Divide 30 by a half and add 10. What do you get?', 'Dividing by ½ means doubling: 60 + 10.'],
        ['Dividi 30 per un mezzo e aggiungi 10. Quanto fa?', 'Dividere per ½ vuol dire raddoppiare: 60 + 10.'],
        ['Divide 30 entre un medio y suma 10. ¿Cuánto da?', 'Dividir entre ½ es duplicar: 60 + 10.'],
        ['Divise 30 par un demi et ajoute 10. Combien ça fait ?', 'Diviser par ½ revient à doubler : 60 + 10.'],
        ['Teile 30 durch ein Halb und addiere 10. Was kommt heraus?', 'Durch ½ teilen heißt verdoppeln: 60 + 10.']);
    R('n24', .7, null,
        ['A man lives on floor 20. Going down he takes the lift all the way; going up he stops at floor 10 and walks, except on rainy days. Why?', 'He can’t reach the top buttons, except with his umbrella.', ['He’s short and needs his umbrella', 'He likes exercise when it’s dry', 'The lift is afraid of the sun', 'His friend lives on floor 10']],
        ['Un uomo abita al piano 20. In discesa usa l’ascensore fino in fondo; in salita si ferma al 10 e prosegue a piedi, tranne quando piove. Perché?', 'Non arriva ai pulsanti alti, se non con l’ombrello.', ['È basso e gli serve l’ombrello', 'Col secco ama fare moto', 'L’ascensore teme il sole', 'Al 10 abita un amico']],
        ['Un hombre vive en el piso 20. Al bajar usa el ascensor hasta abajo; al subir se para en el 10 y sigue a pie, salvo cuando llueve. ¿Por qué?', 'No llega a los botones altos, salvo con el paraguas.', ['Es bajo y necesita el paraguas', 'Con buen tiempo le gusta andar', 'El ascensor teme al sol', 'En el 10 vive un amigo']],
        ['Un homme habite au 20e étage. Il descend en ascenseur jusqu’en bas ; en montant il s’arrête au 10e et finit à pied, sauf quand il pleut. Pourquoi ?', 'Il n’atteint pas les boutons du haut, sauf avec son parapluie.', ['Il est petit et a besoin du parapluie', 'Par temps sec il aime marcher', 'L’ascenseur craint le soleil', 'Un ami habite au 10e']],
        ['Ein Mann wohnt im 20. Stock. Abwärts fährt er ganz mit dem Aufzug; aufwärts steigt er im 10. aus und geht zu Fuß, außer wenn es regnet. Warum?', 'Er kommt nicht an die oberen Knöpfe, außer mit dem Schirm.', ['Er ist klein und braucht den Schirm', 'Bei Trockenheit bewegt er sich gern', 'Der Aufzug fürchtet die Sonne', 'Im 10. wohnt ein Freund']]);
    R('n25', .74, null,
        ['A surgeon’s brother dies, but the man who died had no brother. How?', 'The surgeon is his sister.', ['The surgeon is his sister', 'The surgeon lied', 'They were cousins', 'He had a stepbrother']],
        ['Il fratello di un chirurgo muore, ma il morto non aveva fratelli. Come mai?', 'Il chirurgo è sua sorella.', ['Il chirurgo è sua sorella', 'Il chirurgo mentiva', 'Erano cugini', 'Aveva un fratellastro']],
        ['El hermano de una persona cirujana muere, pero el muerto no tenía hermanos. ¿Cómo?', 'Es su hermana.', ['La cirujana es su hermana', 'Mentía', 'Eran primos', 'Tenía un hermanastro']],
        ['Le frère d’un chirurgien meurt, mais le mort n’avait pas de frère. Comment ?', 'Le chirurgien est sa sœur.', ['Le chirurgien est sa sœur', 'Le chirurgien mentait', 'Ils étaient cousins', 'Il avait un demi-frère']],
        ['Der Bruder einer Person, die Chirurgie betreibt, stirbt, doch der Tote hatte keinen Bruder. Wie das?', 'Sie ist seine Schwester.', ['Sie ist seine Schwester', 'Sie hat gelogen', 'Sie waren Cousins', 'Er hatte einen Stiefbruder']]);
    R('n26', .8, null,
        ['“This sentence is false.” Is it true or false?', 'If it’s true it’s false, and if it’s false it’s true: a paradox.', ['Neither: it’s a paradox', 'True', 'False', 'True on Sundays']],
        ['«Questa frase è falsa.» È vera o falsa?', 'Se è vera è falsa, e se è falsa è vera: un paradosso.', ['Nessuna delle due: è un paradosso', 'Vera', 'Falsa', 'Vera la domenica']],
        ['«Esta frase es falsa.» ¿Es verdadera o falsa?', 'Si es verdadera es falsa, y si es falsa es verdadera: una paradoja.', ['Ninguna: es una paradoja', 'Verdadera', 'Falsa', 'Verdadera los domingos']],
        ['« Cette phrase est fausse. » Est-elle vraie ou fausse ?', 'Si elle est vraie elle est fausse, et inversement : un paradoxe.', ['Ni l’un ni l’autre : c’est un paradoxe', 'Vraie', 'Fausse', 'Vraie le dimanche']],
        ['„Dieser Satz ist falsch.“ Ist er wahr oder falsch?', 'Ist er wahr, ist er falsch, und umgekehrt: ein Paradox.', ['Keins von beiden: ein Paradox', 'Wahr', 'Falsch', 'Sonntags wahr']]);
    R('n27', .86, null,
        ['One clock has stopped, another loses one minute a day. Which one shows the right time more often?', 'The stopped one is right twice a day; the other only once every two years or so.', ['The stopped one', 'The slow one', 'They are equal', 'Neither, ever']],
        ['Un orologio è fermo, un altro perde un minuto al giorno. Quale segna l’ora giusta più spesso?', 'Quello fermo ha ragione due volte al giorno; l’altro circa una volta ogni due anni.', ['Quello fermo', 'Quello lento', 'Sono uguali', 'Nessuno, mai']],
        ['Un reloj está parado y otro pierde un minuto al día. ¿Cuál da la hora correcta más a menudo?', 'El parado acierta dos veces al día; el otro, más o menos cada dos años.', ['El parado', 'El lento', 'Igual los dos', 'Ninguno, nunca']],
        ['Une horloge est arrêtée, une autre perd une minute par jour. Laquelle donne l’heure juste le plus souvent ?', 'L’arrêtée a raison deux fois par jour ; l’autre environ tous les deux ans.', ['L’arrêtée', 'La lente', 'Pareil', 'Aucune, jamais']],
        ['Eine Uhr steht still, eine andere verliert täglich eine Minute. Welche zeigt öfter die richtige Zeit?', 'Die stehende stimmt zweimal am Tag, die andere nur etwa alle zwei Jahre.', ['Die stehende', 'Die langsame', 'Beide gleich', 'Keine, nie']]);
    R('n28', .7, ['7', '5', '3', '9'],
        ['A farmer must take a wolf, a goat and a cabbage across a river, one at a time. How many crossings at least?', 'The goat goes first and comes back once: goat, back, wolf, goat back, cabbage, back, goat.'],
        ['Un contadino deve portare lupo, capra e cavolo oltre il fiume, uno alla volta. Quante traversate servono almeno?', 'La capra va per prima e torna indietro una volta: capra, ritorno, lupo, capra indietro, cavolo, ritorno, capra.'],
        ['Un granjero debe cruzar un río con un lobo, una cabra y una col, de uno en uno. ¿Cuántos cruces hacen falta como mínimo?', 'La cabra va primero y vuelve una vez: cabra, vuelta, lobo, cabra atrás, col, vuelta, cabra.'],
        ['Un fermier doit faire traverser un loup, une chèvre et un chou, un à la fois. Combien de traversées au minimum ?', 'La chèvre passe d’abord et revient une fois : chèvre, retour, loup, chèvre en arrière, chou, retour, chèvre.'],
        ['Ein Bauer muss Wolf, Ziege und Kohl einzeln über den Fluss bringen. Wie viele Überfahrten braucht er mindestens?', 'Die Ziege fährt zuerst und einmal zurück: Ziege, zurück, Wolf, Ziege zurück, Kohl, zurück, Ziege.']);
    R('n29', .76, null,
        ['“Yesterday I was 10 and next year I’ll turn 13.” When can this be true?', 'Said on 1 January, by someone born on 31 December.', ['On 1 January, born on 31 December', 'Never', 'In a leap year', 'On 29 February']],
        ['«Ieri avevo 10 anni e l’anno prossimo ne compirò 13.» Quando può essere vero?', 'Detto il 1° gennaio da chi è nato il 31 dicembre.', ['Il 1° gennaio, se nato il 31 dicembre', 'Mai', 'In un anno bisestile', 'Il 29 febbraio']],
        ['«Ayer tenía 10 años y el año que viene cumpliré 13.» ¿Cuándo puede ser verdad?', 'Dicho el 1 de enero por quien nació un 31 de diciembre.', ['El 1 de enero, si nació el 31 de diciembre', 'Nunca', 'En un año bisiesto', 'El 29 de febrero']],
        ['« Hier j’avais 10 ans et l’an prochain j’en aurai 13. » Quand est-ce possible ?', 'Dit le 1er janvier par quelqu’un né un 31 décembre.', ['Le 1er janvier, si né un 31 décembre', 'Jamais', 'Une année bissextile', 'Le 29 février']],
        ['„Gestern war ich 10, und nächstes Jahr werde ich 13.“ Wann kann das stimmen?', 'Gesagt am 1. Januar von jemandem, der am 31. Dezember geboren ist.', ['Am 1. Januar, geboren am 31. Dezember', 'Nie', 'In einem Schaltjahr', 'Am 29. Februar']]);
    R('n30', .72, ['87', '78', '89', '97'],
        ['In a car park the spaces read 16, 06, 68, 88, ?, 98. What number is under the car?', 'Turn it upside down: 86, 87, 88, 89, 90, 91.'],
        ['In un parcheggio i posti sono 16, 06, 68, 88, ?, 98. Che numero c’è sotto l’auto?', 'Capovolgi tutto: 86, 87, 88, 89, 90, 91.'],
        ['En un aparcamiento las plazas son 16, 06, 68, 88, ?, 98. ¿Qué número hay bajo el coche?', 'Dale la vuelta: 86, 87, 88, 89, 90, 91.'],
        ['Sur un parking, les places sont 16, 06, 68, 88, ?, 98. Quel numéro est sous la voiture ?', 'Retourne tout : 86, 87, 88, 89, 90, 91.'],
        ['Auf einem Parkplatz stehen die Nummern 16, 06, 68, 88, ?, 98. Welche Zahl ist unter dem Auto?', 'Dreh alles um: 86, 87, 88, 89, 90, 91.']);
    R('n31', .78, null,
        ['A man looks at a portrait: “I have no brothers or sisters, but that man’s father is my father’s son.” Who is in the portrait?', '“My father’s son” is himself, so that man’s father is him.', ['His son', 'Himself', 'His father', 'His nephew']],
        ['Un uomo guarda un ritratto: «Non ho fratelli né sorelle, ma il padre di quell’uomo è il figlio di mio padre.» Chi è nel ritratto?', '«Il figlio di mio padre» è lui stesso: quindi il padre di quell’uomo è lui.', ['Suo figlio', 'Lui stesso', 'Suo padre', 'Suo nipote']],
        ['Un hombre mira un retrato: «No tengo hermanos, pero el padre de ese hombre es el hijo de mi padre.» ¿Quién está en el retrato?', '«El hijo de mi padre» es él mismo: el padre de ese hombre es él.', ['Su hijo', 'Él mismo', 'Su padre', 'Su sobrino']],
        ['Un homme regarde un portrait : « Je n’ai ni frère ni sœur, mais le père de cet homme est le fils de mon père. » Qui est sur le portrait ?', '« Le fils de mon père », c’est lui : le père de cet homme, c’est donc lui.', ['Son fils', 'Lui-même', 'Son père', 'Son neveu']],
        ['Ein Mann betrachtet ein Porträt: „Ich habe keine Geschwister, aber der Vater dieses Mannes ist der Sohn meines Vaters.“ Wer ist auf dem Bild?', '„Der Sohn meines Vaters“ ist er selbst: Der Vater dieses Mannes ist also er.', ['Sein Sohn', 'Er selbst', 'Sein Vater', 'Sein Neffe']]);
    R('n32', .56, null,
        ['A doctor gives you 3 pills and tells you to take one every half hour. How long do they last?', 'You take the first at 0:00, the second at 0:30 and the third at 1:00: one hour in total.', ['1 hour', '1 hour and a half', '2 hours', '30 minutes']],
        ['Il medico ti dà 3 pillole e ti dice di prenderne una ogni mezz’ora. Quanto durano?', 'Prendi la prima subito, la seconda dopo 30 minuti e la terza dopo 60: un’ora in tutto.', ['1 ora', '1 ora e mezza', '2 ore', '30 minuti']],
        ['El médico te da 3 pastillas para tomar una cada media hora. ¿Cuánto duran?', 'Tomas la primera al instante, la segunda a los 30 minutos y la tercera a los 60: una hora en total.', ['1 hora', '1 hora y media', '2 horas', '30 minutos']],
        ['Un médecin te donne 3 pilules à prendre toutes les demi-heures. Combien de temps durent-elles ?', 'Tu prends la première tout de suite, la deuxième à 30 min et la troisième à 60 min : une heure en tout.', ['1 heure', '1 heure et demie', '2 heures', '30 minutes']],
        ['Ein Arzt gibt dir 3 Tabletten, jede halbe Stunde eine. Wie lange reichen sie?', 'Die erste nimmst du sofort, die zweite nach 30 Minuten, die dritte nach 60: insgesamt eine Stunde.', ['1 Stunde', '1,5 Stunden', '2 Stunden', '30 Minuten']]);
    R('n33', .66, ['4', '3', '6', '12'],
        ['Three ducks walk in a line: two ducks in front of a duck, two ducks behind a duck, and one duck in the middle. How many ducks are enough for this to be true — wait, what if there is also one duck with three behind it?', 'Four ducks in a single line satisfy every part of the description.'],
        ['In fila indiana ci sono due papere davanti a una papera, due dietro a una papera, una in mezzo, più una quarta che le segue tutte. Quante papere bastano in tutto?', 'Basta una fila di 4 papere perché tutte le frasi siano vere.'],
        ['En fila india van dos patos delante de un pato, dos detrás de un pato, uno en medio y un cuarto siguiéndolos a todos. ¿Cuántos patos bastan en total?', 'Basta una fila de 4 patos para que todo sea cierto.'],
        ['En file indienne marchent deux canards devant un canard, deux derrière un canard, un au milieu, plus un quatrième qui les suit tous. Combien de canards suffisent ?', 'Une file de 4 canards suffit pour que tout soit vrai.'],
        ['Im Gänsemarsch laufen zwei Enten vor einer Ente, zwei hinter einer Ente, eine in der Mitte und eine vierte hinter allen. Wie viele Enten reichen insgesamt?', 'Eine Reihe aus 4 Enten erfüllt alle Aussagen.']);
    R('n34', .94, null,
        ['Two doors: one leads to freedom. Two guards: one always lies, one always tells the truth, and you don’t know which. With one question to one guard, what do you ask?', 'Both guards point at the wrong door, so you take the other one.', ['“Which door would the other guard say is safe?”, then take the other one', '“Are you the liar?”', '“Which door is safe?”', '“Is the sky blue?”']],
        ['Due porte: una porta alla libertà. Due guardiani: uno mente sempre, uno dice sempre il vero, e non sai chi è chi. Con una domanda a uno solo, che cosa chiedi?', 'Entrambi indicano la porta sbagliata: quindi scegli l’altra.', ['«Quale porta indicherebbe l’altro?», poi scegli l’opposta', '«Sei tu il bugiardo?»', '«Qual è la porta giusta?»', '«Il cielo è blu?»']],
        ['Dos puertas: una lleva a la libertad. Dos guardias: uno siempre miente y otro siempre dice la verdad, y no sabes cuál es cuál. Con una pregunta a uno solo, ¿qué preguntas?', 'Los dos señalan la puerta equivocada: así que eliges la otra.', ['«¿Qué puerta señalaría el otro?», y eliges la opuesta', '«¿Eres tú el mentiroso?»', '«¿Cuál es la puerta buena?»', '«¿El cielo es azul?»']],
        ['Deux portes : l’une mène à la liberté. Deux gardes : l’un ment toujours, l’autre dit toujours vrai, et tu ne sais pas qui est qui. Avec une question à un seul, que demandes-tu ?', 'Les deux désignent la mauvaise porte : tu prends donc l’autre.', ['« Quelle porte l’autre indiquerait-il ? », puis tu prends l’autre', '« Es-tu le menteur ? »', '« Quelle est la bonne porte ? »', '« Le ciel est-il bleu ? »']],
        ['Zwei Türen: Eine führt in die Freiheit. Zwei Wachen: Eine lügt immer, eine sagt immer die Wahrheit, und du weißt nicht, welche welche ist. Was fragst du mit einer einzigen Frage an eine Wache?', 'Beide zeigen auf die falsche Tür, also nimmst du die andere.', ['„Welche Tür würde die andere Wache nennen?“, dann die andere nehmen', '„Bist du der Lügner?“', '„Welche Tür ist die richtige?“', '„Ist der Himmel blau?“']]);
    R('n35', .8, ['6', '4', '5', '8'],
        ['With a 3 litre and a 5 litre jug and a tap, how many moves at least (fill, empty or pour) to get exactly 4 litres?', 'Fill 5, pour into 3, empty 3, pour the 2 into 3, fill 5, top up the 3: 4 are left.'],
        ['Con una brocca da 3 litri, una da 5 e un rubinetto, quante mosse servono almeno (riempire, svuotare o travasare) per avere esattamente 4 litri?', 'Riempi 5, versa in 3, svuota 3, versa i 2 in 3, riempi 5, rabbocca il 3: restano 4.'],
        ['Con una jarra de 3 litros, otra de 5 y un grifo, ¿cuántos movimientos hacen falta como mínimo (llenar, vaciar o trasvasar) para tener exactamente 4 litros?', 'Llena 5, vierte en 3, vacía 3, pasa los 2 a la de 3, llena 5, completa la de 3: quedan 4.'],
        ['Avec une cruche de 3 litres, une de 5 et un robinet, combien de gestes au minimum (remplir, vider ou transvaser) pour avoir exactement 4 litres ?', 'Remplis 5, verse dans 3, vide 3, verse les 2 dans 3, remplis 5, complète la 3 : il reste 4.'],
        ['Mit einem 3-Liter-Krug, einem 5-Liter-Krug und einem Hahn: Wie viele Schritte (füllen, leeren, umgießen) braucht man mindestens für genau 4 Liter?', 'Fülle 5, gieße in 3, leere 3, gieße die 2 in 3, fülle 5, fülle 3 auf: Es bleiben 4.']);
    R('n36', .64, null,
        ['A lily pad in a pond doubles every day. If you start with TWO lily pads instead of one, and one pad took 30 days to fill the pond, how many days will two pads take?', 'Starting with two is the same as starting on day 2: it takes one day less, so 29 days.', ['29 days', '15 days', '28 days', '30 days']],
        ['Una ninfea nello stagno raddoppia ogni giorno e da sola lo copre in 30 giorni. Se invece parti subito con DUE ninfee, quanti giorni impiegano?', 'Partire con due equivale a partire dal secondo giorno: basta un giorno in meno, quindi 29.', ['29 giorni', '15 giorni', '28 giorni', '30 giorni']],
        ['Un nenúfar en un estanque se duplica cada día y tarda 30 días en cubrirlo. Si empiezas con DOS nenúfares, ¿cuántos días tardarán?', 'Empezar con dos equivale a empezar en el segundo día: tarda un día menos, 29.', ['29 días', '15 días', '28 días', '30 días']],
        ['Un nénuphar double chaque jour et couvre l’étang en 30 jours. Si tu commences avec DEUX nénuphars, combien de jours leur faut-il ?', 'Commencer avec deux revient à partir du deuxième jour : il faut un jour de moins, soit 29.', ['29 jours', '15 jours', '28 jours', '30 jours']],
        ['Eine Seerose verdoppelt sich täglich und bedeckt den Teich in 30 Tagen. Wenn du gleich mit ZWEI Seerosen startest, wie viele Tage brauchen sie?', 'Mit zwei zu starten ist wie am zweiten Tag zu beginnen: Es dauert einen Tag weniger, also 29.', ['29 Tage', '15 Tage', '28 Tage', '30 Tage']]);
    R('n37', .84, ['312211', '1112221', '132112', '311211'],
        ['What comes next? 1, 11, 21, 1211, 111221, …', 'Each row describes the one before: “one 1”, “two 1s”, “one 2, one 1”… so three 1s, two 2s, one 1.'],
        ['Che cosa viene dopo? 1, 11, 21, 1211, 111221, …', 'Ogni riga descrive la precedente: «un 1», «due 1», «un 2, un 1»… quindi tre 1, due 2, un 1.'],
        ['¿Qué viene después? 1, 11, 21, 1211, 111221, …', 'Cada fila describe la anterior: «un 1», «dos 1», «un 2, un 1»… así que tres 1, dos 2, un 1.'],
        ['Que vient-il ensuite ? 1, 11, 21, 1211, 111221, …', 'Chaque ligne décrit la précédente : « un 1 », « deux 1 », « un 2, un 1 »… donc trois 1, deux 2, un 1.'],
        ['Was kommt als Nächstes? 1, 11, 21, 1211, 111221, …', 'Jede Zeile beschreibt die vorherige: „eine 1“, „zwei 1“, „eine 2, eine 1“… also drei 1, zwei 2, eine 1.']);
    R('n38', .98, null,
        ['Two ropes each burn in exactly one hour, but unevenly. How do you measure 45 minutes?', 'The first rope, lit at both ends, lasts 30 minutes; then lighting the other end of the second leaves 15.', ['Light one at both ends and the other at one end; when the first ends, light the other’s free end', 'Cut one at three quarters', 'Light both at one end', 'Fold them in two']],
        ['Due corde bruciano ciascuna in un’ora esatta, ma in modo irregolare. Come misuri 45 minuti?', 'La prima, accesa ai due capi, dura 30 minuti; accendendo poi il secondo capo dell’altra restano 15.', ['Accendine una ai due capi e l’altra a un capo; quando la prima finisce, accendi l’altro capo', 'Tagliane una a tre quarti', 'Accendile entrambe a un capo', 'Piegale in due']],
        ['Dos cuerdas tardan cada una una hora exacta en arder, pero de forma irregular. ¿Cómo mides 45 minutos?', 'La primera, encendida por los dos extremos, dura 30 minutos; al encender el otro extremo de la segunda quedan 15.', ['Enciende una por los dos extremos y otra por uno; al acabar la primera, enciende el otro extremo', 'Corta una a tres cuartos', 'Enciende las dos por un extremo', 'Dóblalas por la mitad']],
        ['Deux cordes brûlent chacune en une heure exacte, mais de façon irrégulière. Comment mesurer 45 minutes ?', 'La première, allumée aux deux bouts, dure 30 minutes ; allumer ensuite l’autre bout de la seconde laisse 15.', ['Allume l’une aux deux bouts et l’autre à un bout ; quand la première finit, allume l’autre bout', 'Coupe-en une aux trois quarts', 'Allume les deux à un bout', 'Plie-les en deux']],
        ['Zwei Seile brennen je genau eine Stunde, aber ungleichmäßig. Wie misst du 45 Minuten?', 'Das erste, an beiden Enden angezündet, brennt 30 Minuten; danach bleiben durch das zweite Ende des anderen 15.', ['Eines an beiden Enden, das andere an einem anzünden; ist das erste aus, das andere Ende anzünden', 'Eines bei drei Vierteln abschneiden', 'Beide an einem Ende anzünden', 'Beide falten']]);
    R('n39', 1.02, ['1', '3', '9', '10'],
        ['10 bags hold 10 gold coins each (10 g per coin), but one bag holds only 9 g fakes. With a digital scale that shows the exact weight in grams, how many weighings do you need to find the fake bag?', 'Take 1 coin from bag 1, 2 from bag 2, … 10 from bag 10 and weigh them all together once: the missing grams tell you the bag number!'],
        ['Hai 10 sacchetti con 10 monete ciascuno (da 10 g l’una), ma un sacchetto contiene solo monete false da 9 g. Con una bilancia digitale che segna il peso esatto in grammi, quante pesate bastano per trovare il sacchetto falso?', 'Prendi 1 moneta dal sacchetto 1, 2 dal 2, … 10 dal 10 e pesale tutte insieme una sola volta: i grammi mancanti da 550 g indicano il numero del sacchetto!'],
        ['Tienes 10 bolsas con 10 monedas cada una (de 10 g), pero una bolsa solo tiene monedas falsas de 9 g. Con una báscula digital que muestra el peso exacto, ¿cuántas pesadas bastan para hallar la bolsa falsa?', 'Coge 1 moneda de la bolsa 1, 2 de la 2… 10 de la 10 y pésalas juntas una sola vez: los gramos que falten hasta 550 g te dicen qué bolsa es.'],
        ['Tu as 10 sacs de 10 pièces (de 10 g chacune), mais un sac ne contient que de fausses pièces de 9 g. Avec une balance digitale qui affiche le poids exact, combien de pesées suffisent ?', 'Prends 1 pièce du sac 1, 2 du sac 2… 10 du sac 10 et pèse-les toutes ensemble en une seule fois : les grammes manquants sur 550 g donnent le numéro du sac !'],
        ['Du hast 10 Beutel mit je 10 Münzen (je 10 g), aber ein Beutel enthält nur 9-g-Fälschungen. Wie viele Wägungen auf einer genauen Digitalwaage brauchst du, um den falschen Beutel zu finden?', 'Nimm 1 Münze aus Beutel 1, 2 aus Beutel 2 … 10 aus Beutel 10 und wiege alle zusammen ein einziges Mal: Die fehlenden Gramm zu 550 g verraten die Beutelnummer!']);
    R('n40', .86, ['1', '0', '2', '3'],
        ['A list has four sentences: “Exactly one of these is false”, “Exactly two are false”, “Exactly three are false”, “All four are false”. How many are true?', 'They contradict each other, so at most one is true. That makes three false, which is what the third one says.'],
        ['Una lista ha quattro frasi: «Esattamente una di queste è falsa», «Esattamente due sono false», «Esattamente tre sono false», «Tutte e quattro sono false». Quante sono vere?', 'Si contraddicono, quindi al massimo una è vera. Allora tre sono false, ed è ciò che dice la terza.'],
        ['Una lista tiene cuatro frases: «Exactamente una de estas es falsa», «Exactamente dos son falsas», «Exactamente tres son falsas», «Las cuatro son falsas». ¿Cuántas son verdaderas?', 'Se contradicen, así que como mucho una es verdadera. Entonces tres son falsas, y eso dice la tercera.'],
        ['Une liste a quatre phrases : « Exactement une est fausse », « Exactement deux sont fausses », « Exactement trois sont fausses », « Les quatre sont fausses ». Combien sont vraies ?', 'Elles se contredisent, donc une au plus est vraie. Trois sont alors fausses, ce que dit la troisième.'],
        ['Eine Liste hat vier Sätze: „Genau einer ist falsch“, „Genau zwei sind falsch“, „Genau drei sind falsch“, „Alle vier sind falsch“. Wie viele sind wahr?', 'Sie widersprechen sich, also ist höchstens einer wahr. Dann sind drei falsch, und genau das sagt der dritte.']);
    R('n41', 1.04, ['17', '19', '18', '21'],
        ['Four people cross a bridge at night in 1, 2, 5 and 10 minutes, two at a time at the slower one’s pace, with one torch that must be carried back. What is the shortest total in minutes?', '1+2 go (2), 1 back (1), 5+10 go (10), 2 back (2), 1+2 go (2): 17.'],
        ['Quattro persone attraversano un ponte di notte in 1, 2, 5 e 10 minuti, a due a due al passo del più lento, con una sola torcia da riportare indietro. Qual è il tempo minimo in minuti?', '1+2 vanno (2), 1 torna (1), 5+10 vanno (10), 2 torna (2), 1+2 vanno (2): 17.'],
        ['Cuatro personas cruzan un puente de noche en 1, 2, 5 y 10 minutos, de dos en dos al paso del más lento, con una linterna que hay que devolver. ¿Cuál es el tiempo mínimo en minutos?', '1+2 van (2), 1 vuelve (1), 5+10 van (10), 2 vuelve (2), 1+2 van (2): 17.'],
        ['Quatre personnes traversent un pont de nuit en 1, 2, 5 et 10 minutes, deux par deux au pas du plus lent, avec une seule lampe à ramener. Quel est le temps minimal en minutes ?', '1+2 passent (2), 1 revient (1), 5+10 passent (10), 2 revient (2), 1+2 passent (2) : 17.'],
        ['Vier Personen überqueren nachts eine Brücke in 1, 2, 5 und 10 Minuten, zu zweit im Tempo der langsameren, mit einer Lampe, die zurückgebracht werden muss. Wie viele Minuten dauert es mindestens?', '1+2 gehen (2), 1 zurück (1), 5+10 gehen (10), 2 zurück (2), 1+2 gehen (2): 17.']);
    R('n42', 1.0, null,
        ['There are 3 white hats and 2 black ones. Ada, Bruno and Carla stand in a line: Ada sees Bruno and Carla, Bruno sees Carla, Carla sees nobody. Ada says “I don’t know my hat”, then Bruno says the same. What is Carla’s hat?', 'Two blacks ahead would have told Ada hers. If Carla’s were black, Bruno would then know his is white. So it’s white.', ['White', 'Black', 'You can’t know', 'The same as Bruno’s']],
        ['Ci sono 3 cappelli bianchi e 2 neri. Ada, Bruno e Carla sono in fila: Ada vede Bruno e Carla, Bruno vede Carla, Carla nessuno. Ada dice «Non so il mio cappello», poi lo dice anche Bruno. Di che colore è quello di Carla?', 'Due neri davanti avrebbero rivelato il suo ad Ada. Se Carla avesse il nero, Bruno saprebbe di avere il bianco. Quindi è bianco.', ['Bianco', 'Nero', 'Non si può sapere', 'Uguale a quello di Bruno']],
        ['Hay 3 sombreros blancos y 2 negros. Ada, Bruno y Carla están en fila: Ada ve a Bruno y Carla, Bruno ve a Carla, Carla a nadie. Ada dice «No sé mi sombrero» y luego Bruno dice lo mismo. ¿De qué color es el de Carla?', 'Dos negros delante le habrían revelado el suyo a Ada. Si Carla llevara negro, Bruno sabría que el suyo es blanco. Así que es blanco.', ['Blanco', 'Negro', 'No se puede saber', 'Igual que el de Bruno']],
        ['Il y a 3 chapeaux blancs et 2 noirs. Ada, Bruno et Carla sont en file : Ada voit Bruno et Carla, Bruno voit Carla, Carla personne. Ada dit « Je ne connais pas mon chapeau », puis Bruno dit pareil. De quelle couleur est celui de Carla ?', 'Deux noirs devant auraient révélé le sien à Ada. Si Carla avait le noir, Bruno saurait que le sien est blanc. Il est donc blanc.', ['Blanc', 'Noir', 'Impossible à savoir', 'Comme celui de Bruno']],
        ['Es gibt 3 weiße und 2 schwarze Hüte. Ada, Bruno und Carla stehen in einer Reihe: Ada sieht Bruno und Carla, Bruno sieht Carla, Carla niemanden. Ada sagt „Ich kenne meinen Hut nicht“, dann sagt Bruno dasselbe. Welche Farbe hat Carlas Hut?', 'Zwei schwarze vorn hätten Ada ihren verraten. Wäre Carlas schwarz, wüsste Bruno, dass seiner weiß ist. Also ist er weiß.', ['Weiß', 'Schwarz', 'Das kann man nicht wissen', 'Wie der von Bruno']]);
    R('n43', 1.07, ['3', '2', '4', '5'],
        ['Of 12 identical coins, one has a different weight, heavier or lighter, you don’t know which. How many weighings on a two-pan balance are enough to find it and say which it is?', 'Each weighing has three outcomes: 3 weighings give 27, enough for the 24 possibilities.'],
        ['Su 12 monete uguali, una ha un peso diverso, non sai se maggiore o minore. Quante pesate con una bilancia a due piatti bastano per trovarla e dire com’è?', 'Ogni pesata ha tre esiti: 3 pesate ne danno 27, abbastanza per i 24 casi.'],
        ['De 12 monedas iguales, una pesa distinto, no sabes si más o menos. ¿Cuántas pesadas en una balanza de dos platos bastan para encontrarla y decir cómo es?', 'Cada pesada tiene tres resultados: 3 pesadas dan 27, suficiente para los 24 casos.'],
        ['Sur 12 pièces identiques, une a un poids différent, plus lourde ou plus légère. Combien de pesées sur une balance à deux plateaux suffisent pour la trouver et dire laquelle ?', 'Chaque pesée a trois issues : 3 pesées en donnent 27, assez pour les 24 cas.'],
        ['Von 12 gleichen Münzen hat eine ein anderes Gewicht, schwerer oder leichter, du weißt es nicht. Wie viele Wägungen mit einer Balkenwaage reichen, um sie zu finden und zu sagen, wie sie ist?', 'Jede Wägung hat drei Ergebnisse: 3 Wägungen ergeben 27, genug für die 24 Fälle.']);
})(typeof window !== 'undefined' ? window : globalThis);
