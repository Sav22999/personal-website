/*
 * ThinkHop — riddles written by hand, in five languages. The RIGHT OPTION ALWAYS COMES FIRST: the game shuffles them.
 *
 *   {id, level, o: [options the same in every language], t: {lang: [question, explanation, options if they change]}}
 */
(function (root) {
    'use strict';

    root.ThinkHopRiddles = [
        // ------------------------------------------------------------------ easy
        {
            id: 'e01', level: 'easy', o: ['12', '1', '2', '6'], t: {
                en: ['How many months of the year have at least 28 days?', 'All of them: every month has 28 days or more.'],
                it: ['Quanti mesi dell’anno hanno almeno 28 giorni?', 'Tutti: ogni mese ha 28 giorni o più.'],
                es: ['¿Cuántos meses del año tienen al menos 28 días?', 'Todos: cada mes tiene 28 días o más.'],
                fr: ['Combien de mois de l’année ont au moins 28 jours ?', 'Tous : chaque mois a 28 jours ou plus.'],
                de: ['Wie viele Monate im Jahr haben mindestens 28 Tage?', 'Alle: Jeder Monat hat 28 Tage oder mehr.'],
            },
        },
        {
            id: 'e02', level: 'easy', o: ['6', '9', '15', '21'], t: {
                en: ['A farmer has 15 sheep. All but 6 run away. How many are left?', '“All but 6” means that exactly 6 stayed.'],
                it: ['Un contadino ha 15 pecore. Scappano tutte tranne 6. Quante ne restano?', '“Tutte tranne 6” vuol dire che ne sono rimaste proprio 6.'],
                es: ['Un granjero tiene 15 ovejas. Se escapan todas menos 6. ¿Cuántas quedan?', '“Todas menos 6” significa que quedan justo 6.'],
                fr: ['Un fermier a 15 moutons. Tous s’enfuient sauf 6. Combien en reste-t-il ?', '« Tous sauf 6 » veut dire qu’il en reste exactement 6.'],
                de: ['Ein Bauer hat 15 Schafe. Alle bis auf 6 laufen weg. Wie viele bleiben?', '„Alle bis auf 6“ heißt: Genau 6 sind geblieben.'],
            },
        },
        {
            id: 'e03', level: 'easy', t: {
                en: ['In a race you overtake the runner in second place. What place are you in now?', 'You took the place of the second runner, and the first is still ahead.', ['Second', 'First', 'Third', 'Last']],
                it: ['In una gara superi chi è al secondo posto. In che posizione sei adesso?', 'Hai preso il posto del secondo: il primo è ancora davanti.', ['Seconda', 'Prima', 'Terza', 'Ultima']],
                es: ['En una carrera adelantas a quien va en segundo lugar. ¿En qué posición estás ahora?', 'Has ocupado el lugar del segundo: el primero sigue delante.', ['Segunda', 'Primera', 'Tercera', 'Última']],
                fr: ['Dans une course, tu dépasses le deuxième. À quelle place es-tu maintenant ?', 'Tu as pris la place du deuxième : le premier est toujours devant.', ['Deuxième', 'Première', 'Troisième', 'Dernière']],
                de: ['Bei einem Rennen überholst du den Zweiten. Auf welchem Platz bist du jetzt?', 'Du hast den Platz des Zweiten eingenommen, der Erste liegt weiter vorn.', ['Zweiter', 'Erster', 'Dritter', 'Letzter']],
            },
        },
        {
            id: 'e04', level: 'easy', o: ['Anna', 'Giulia', 'Paolo', 'Sara'], t: {
                en: ['Anna’s mother has three children: Luca, Marta and…?', 'Anna is one of the children: the question has just told you her name.'],
                it: ['La mamma di Anna ha tre figli: Luca, Marta e…?', 'Anna è una dei tre figli: il nome è già nella domanda.'],
                es: ['La madre de Anna tiene tres hijos: Luca, Marta y…?', 'Anna es una de los tres: su nombre ya está en la pregunta.'],
                fr: ['La mère d’Anna a trois enfants : Luca, Marta et… ?', 'Anna est l’une des trois : son prénom est déjà dans la question.'],
                de: ['Annas Mutter hat drei Kinder: Luca, Marta und…?', 'Anna ist eines der drei Kinder: Der Name steht schon in der Frage.'],
            },
        },
        {
            id: 'e05', level: 'easy', t: {
                en: ['One egg takes 4 minutes to boil. How long do 3 eggs take in the same pot?', 'They all cook at the same time.', ['4 min', '12 min', '8 min', '6 min']],
                it: ['Per cuocere un uovo servono 4 minuti. Quanti ne servono per 3 uova nella stessa pentola?', 'Cuociono tutte insieme.', ['4 min', '12 min', '8 min', '6 min']],
                es: ['Un huevo tarda 4 minutos en cocerse. ¿Cuánto tardan 3 huevos en la misma olla?', 'Se cuecen todos a la vez.', ['4 min', '12 min', '8 min', '6 min']],
                fr: ['Un œuf cuit en 4 minutes. Combien de temps pour 3 œufs dans la même casserole ?', 'Ils cuisent tous en même temps.', ['4 min', '12 min', '8 min', '6 min']],
                de: ['Ein Ei braucht 4 Minuten. Wie lange brauchen 3 Eier im selben Topf?', 'Sie kochen alle gleichzeitig.', ['4 Min.', '12 Min.', '8 Min.', '6 Min.']],
            },
        },
        {
            id: 'e06', level: 'easy', o: ['2', '1', '3', '0'], t: {
                en: ['There are 3 apples on a table and you take away 2. How many apples do you have?', 'You have the 2 you took: 1 stayed on the table.'],
                it: ['Sul tavolo ci sono 3 mele e tu ne prendi 2. Quante mele hai?', 'Hai le 2 che hai preso: 1 è rimasta sul tavolo.'],
                es: ['Hay 3 manzanas en la mesa y coges 2. ¿Cuántas manzanas tienes?', 'Tienes las 2 que has cogido: 1 se ha quedado en la mesa.'],
                fr: ['Il y a 3 pommes sur la table et tu en prends 2. Combien de pommes as-tu ?', 'Tu as les 2 que tu as prises : 1 est restée sur la table.'],
                de: ['Auf dem Tisch liegen 3 Äpfel und du nimmst 2 weg. Wie viele Äpfel hast du?', 'Du hast die 2, die du genommen hast: 1 ist auf dem Tisch geblieben.'],
            },
        },
        {
            id: 'e07', level: 'easy', t: {
                en: ['A rooster lays an egg on the top of a roof. Which side does it roll down?', 'Roosters don’t lay eggs.', ['Neither: no egg', 'Left', 'Right', 'It depends on the wind']],
                it: ['Un gallo fa un uovo in cima a un tetto. Da che parte rotola?', 'I galli non fanno le uova.', ['Da nessuna: niente uovo', 'A sinistra', 'A destra', 'Dipende dal vento']],
                es: ['Un gallo pone un huevo en lo alto de un tejado. ¿Hacia qué lado rueda?', 'Los gallos no ponen huevos.', ['Ninguno: no hay huevo', 'A la izquierda', 'A la derecha', 'Depende del viento']],
                fr: ['Un coq pond un œuf en haut d’un toit. De quel côté roule-t-il ?', 'Les coqs ne pondent pas d’œufs.', ['Aucun : pas d’œuf', 'À gauche', 'À droite', 'Ça dépend du vent']],
                de: ['Ein Hahn legt ein Ei auf den Dachfirst. Zu welcher Seite rollt es?', 'Hähne legen keine Eier.', ['Keiner: Es gibt kein Ei', 'Nach links', 'Nach rechts', 'Das hängt vom Wind ab']],
            },
        },
        {
            id: 'e08', level: 'easy', t: {
                en: ['Which weighs more: 1 kg of feathers or 1 kg of iron?', 'A kilogram is a kilogram, whatever it is made of.', ['They weigh the same', 'The iron', 'The feathers', 'It depends on the size']],
                it: ['Pesa di più 1 kg di piume o 1 kg di ferro?', 'Un chilo è un chilo, di qualunque cosa sia fatto.', ['Pesano uguale', 'Il ferro', 'Le piume', 'Dipende dalla grandezza']],
                es: ['¿Qué pesa más: 1 kg de plumas o 1 kg de hierro?', 'Un kilo es un kilo, sea de lo que sea.', ['Pesan lo mismo', 'El hierro', 'Las plumas', 'Depende del tamaño']],
                fr: ['Qu’est-ce qui pèse le plus : 1 kg de plumes ou 1 kg de fer ?', 'Un kilo reste un kilo, quelle que soit la matière.', ['Ils pèsent pareil', 'Le fer', 'Les plumes', 'Ça dépend de la taille']],
                de: ['Was wiegt mehr: 1 kg Federn oder 1 kg Eisen?', 'Ein Kilo ist ein Kilo, egal woraus.', ['Beide gleich viel', 'Das Eisen', 'Die Federn', 'Das hängt von der Größe ab']],
            },
        },
        {
            id: 'e09', level: 'easy', t: {
                en: ['A plane crashes exactly on the border between two countries. Where do they bury the survivors?', 'Survivors are alive: nobody buries them.', ['Nowhere', 'In the first country', 'In the second country', 'On the border']],
                it: ['Un aereo cade esattamente sul confine tra due stati. Dove seppelliscono i sopravvissuti?', 'I sopravvissuti sono vivi: nessuno li seppellisce.', ['Da nessuna parte', 'Nel primo stato', 'Nel secondo stato', 'Sul confine']],
                es: ['Un avión cae justo en la frontera entre dos países. ¿Dónde entierran a los supervivientes?', 'Los supervivientes están vivos: nadie los entierra.', ['En ningún sitio', 'En el primer país', 'En el segundo país', 'En la frontera']],
                fr: ['Un avion s’écrase pile sur la frontière entre deux pays. Où enterre-t-on les survivants ?', 'Les survivants sont vivants : on ne les enterre pas.', ['Nulle part', 'Dans le premier pays', 'Dans le second pays', 'Sur la frontière']],
                de: ['Ein Flugzeug stürzt genau auf der Grenze zweier Länder ab. Wo begräbt man die Überlebenden?', 'Überlebende sind am Leben: Niemand begräbt sie.', ['Nirgendwo', 'Im ersten Land', 'Im zweiten Land', 'Auf der Grenze']],
            },
        },
        {
            id: 'e10', level: 'easy', o: ['3', '2', '4', '1'], t: {
                en: ['What is half of 2, plus 2?', 'Half of 2 is 1, and 1 + 2 = 3.'],
                it: ['Quanto fa la metà di 2, più 2?', 'La metà di 2 è 1, e 1 + 2 = 3.'],
                es: ['¿Cuánto es la mitad de 2, más 2?', 'La mitad de 2 es 1, y 1 + 2 = 3.'],
                fr: ['Combien font la moitié de 2, plus 2 ?', 'La moitié de 2 est 1, et 1 + 2 = 3.'],
                de: ['Was ist die Hälfte von 2, plus 2?', 'Die Hälfte von 2 ist 1, und 1 + 2 = 3.'],
            },
        },
        {
            id: 'e11', level: 'easy', t: {
                en: ['If yesterday was Tuesday, what day will tomorrow be?', 'Yesterday Tuesday, today Wednesday, tomorrow Thursday.', ['Thursday', 'Wednesday', 'Friday', 'Monday']],
                it: ['Se ieri era martedì, che giorno sarà domani?', 'Ieri martedì, oggi mercoledì, domani giovedì.', ['Giovedì', 'Mercoledì', 'Venerdì', 'Lunedì']],
                es: ['Si ayer fue martes, ¿qué día será mañana?', 'Ayer martes, hoy miércoles, mañana jueves.', ['Jueves', 'Miércoles', 'Viernes', 'Lunes']],
                fr: ['Si hier c’était mardi, quel jour serons-nous demain ?', 'Hier mardi, aujourd’hui mercredi, demain jeudi.', ['Jeudi', 'Mercredi', 'Vendredi', 'Lundi']],
                de: ['Wenn gestern Dienstag war, welcher Tag ist dann morgen?', 'Gestern Dienstag, heute Mittwoch, morgen Donnerstag.', ['Donnerstag', 'Mittwoch', 'Freitag', 'Montag']],
            },
        },

        // ------------------------------------------------------------------ medium
        {
            id: 'm01', level: 'medium', o: ['2 kg', '1 kg', '3 kg', '4 kg'], t: {
                en: ['A brick weighs 1 kg plus half a brick. How much does a brick weigh?', 'If half a brick is the missing part, the other half weighs 1 kg: the whole brick weighs 2 kg.'],
                it: ['Un mattone pesa 1 kg più mezzo mattone. Quanto pesa un mattone?', 'Se il mezzo mattone è la parte che manca, l’altra metà pesa 1 kg: il mattone intero pesa 2 kg.'],
                es: ['Un ladrillo pesa 1 kg más medio ladrillo. ¿Cuánto pesa un ladrillo?', 'Si medio ladrillo es lo que falta, la otra mitad pesa 1 kg: el ladrillo entero pesa 2 kg.'],
                fr: ['Une brique pèse 1 kg plus une demi-brique. Combien pèse une brique ?', 'Si la demi-brique est la partie manquante, l’autre moitié pèse 1 kg : la brique entière pèse 2 kg.'],
                de: ['Ein Ziegel wiegt 1 kg plus einen halben Ziegel. Wie viel wiegt ein Ziegel?', 'Der halbe Ziegel ist der fehlende Teil, die andere Hälfte wiegt also 1 kg: Der ganze Ziegel wiegt 2 kg.'],
            },
        },
        {
            id: 'm02', level: 'medium', o: ['1 €', '2 €', '3 €', '6 €'], t: {
                en: ['A pen and a notebook cost 12 € together. The notebook costs 10 € more than the pen. How much is the pen?', 'Pen 1 € and notebook 11 €: together 12 €, with 10 € of difference. With a 2 € pen the difference would be 8 €.'],
                it: ['Una penna e un quaderno costano 12 € in tutto. Il quaderno costa 10 € più della penna. Quanto costa la penna?', 'Penna 1 € e quaderno 11 €: insieme 12 €, con 10 € di differenza. Con una penna da 2 € la differenza sarebbe 8 €.'],
                es: ['Un bolígrafo y un cuaderno cuestan 12 € en total. El cuaderno cuesta 10 € más que el bolígrafo. ¿Cuánto cuesta el bolígrafo?', 'Bolígrafo 1 € y cuaderno 11 €: juntos 12 €, con 10 € de diferencia. Con un bolígrafo de 2 € la diferencia sería de 8 €.'],
                fr: ['Un stylo et un cahier coûtent 12 € en tout. Le cahier coûte 10 € de plus que le stylo. Combien coûte le stylo ?', 'Stylo 1 € et cahier 11 € : ensemble 12 €, avec 10 € d’écart. Avec un stylo à 2 €, l’écart serait de 8 €.'],
                de: ['Ein Stift und ein Heft kosten zusammen 12 €. Das Heft kostet 10 € mehr als der Stift. Wie viel kostet der Stift?', 'Stift 1 € und Heft 11 €: zusammen 12 €, mit 10 € Unterschied. Bei einem Stift für 2 € wären es nur 8 € Unterschied.'],
            },
        },
        {
            id: 'm03', level: 'medium', t: {
                en: ['5 machines make 5 toys in 5 minutes. How long do 100 machines take to make 100 toys?', 'Each machine makes one toy in 5 minutes, and they all work at the same time.', ['5 min', '100 min', '20 min', '1 min']],
                it: ['5 macchine fanno 5 giocattoli in 5 minuti. Quanto ci mettono 100 macchine a fare 100 giocattoli?', 'Ogni macchina fa un giocattolo in 5 minuti, e lavorano tutte insieme.', ['5 min', '100 min', '20 min', '1 min']],
                es: ['5 máquinas hacen 5 juguetes en 5 minutos. ¿Cuánto tardan 100 máquinas en hacer 100 juguetes?', 'Cada máquina hace un juguete en 5 minutos, y trabajan todas a la vez.', ['5 min', '100 min', '20 min', '1 min']],
                fr: ['5 machines font 5 jouets en 5 minutes. Combien de temps faut-il à 100 machines pour faire 100 jouets ?', 'Chaque machine fait un jouet en 5 minutes, et elles travaillent toutes en même temps.', ['5 min', '100 min', '20 min', '1 min']],
                de: ['5 Maschinen bauen 5 Spielzeuge in 5 Minuten. Wie lange brauchen 100 Maschinen für 100 Spielzeuge?', 'Jede Maschine baut ein Spielzeug in 5 Minuten, und alle arbeiten gleichzeitig.', ['5 Min.', '100 Min.', '20 Min.', '1 Min.']],
            },
        },
        {
            id: 'm04', level: 'medium', o: ['47', '24', '46', '12'], t: {
                en: ['Water lilies double their area every day and cover a lake in 48 days. On which day do they cover half of it?', 'The day before the last: doubling half the lake gives the whole lake.'],
                it: ['Le ninfee raddoppiano la loro superficie ogni giorno e coprono un lago in 48 giorni. In che giorno ne coprono metà?', 'Il giorno prima dell’ultimo: raddoppiando metà lago si ottiene il lago intero.'],
                es: ['Los nenúfares duplican su superficie cada día y cubren un lago en 48 días. ¿Qué día cubren la mitad?', 'El día anterior al último: al duplicar medio lago se obtiene el lago entero.'],
                fr: ['Les nénuphars doublent leur surface chaque jour et couvrent un lac en 48 jours. Quel jour en couvrent-ils la moitié ?', 'La veille du dernier jour : en doublant la moitié du lac, on obtient le lac entier.'],
                de: ['Seerosen verdoppeln ihre Fläche jeden Tag und bedecken einen See in 48 Tagen. An welchem Tag bedecken sie die Hälfte?', 'Am Tag vor dem letzten: Verdoppelt man den halben See, ergibt das den ganzen See.'],
            },
        },
        {
            id: 'm05', level: 'medium', t: {
                en: ['A clock takes 5 seconds to strike 6 times. How long does it take to strike 12 times?', '6 strikes have 5 gaps, so one gap lasts 1 second. 12 strikes have 11 gaps.', ['11 s', '10 s', '12 s', '24 s']],
                it: ['Un orologio impiega 5 secondi per battere 6 rintocchi. Quanto impiega per batterne 12?', '6 rintocchi hanno 5 intervalli, quindi uno dura 1 secondo. 12 rintocchi hanno 11 intervalli.', ['11 s', '10 s', '12 s', '24 s']],
                es: ['Un reloj tarda 5 segundos en dar 6 campanadas. ¿Cuánto tarda en dar 12?', '6 campanadas tienen 5 intervalos, así que cada uno dura 1 segundo. 12 campanadas tienen 11 intervalos.', ['11 s', '10 s', '12 s', '24 s']],
                fr: ['Une horloge met 5 secondes à sonner 6 coups. Combien de temps met-elle à en sonner 12 ?', '6 coups font 5 intervalles, donc chacun dure 1 seconde. 12 coups font 11 intervalles.', ['11 s', '10 s', '12 s', '24 s']],
                de: ['Eine Uhr braucht 5 Sekunden für 6 Schläge. Wie lange braucht sie für 12 Schläge?', '6 Schläge haben 5 Abstände, einer dauert also 1 Sekunde. 12 Schläge haben 11 Abstände.', ['11 s', '10 s', '12 s', '24 s']],
            },
        },
        {
            id: 'm06', level: 'medium', o: ['3', '2', '4', '5'], t: {
                en: ['A dark drawer holds black socks and white socks. How many must you take to be sure of a matching pair?', 'With only two colours, the third sock always matches one of the first two.'],
                it: ['In un cassetto al buio ci sono calzini neri e bianchi. Quanti devi prenderne per avere di sicuro un paio uguale?', 'Con due soli colori, il terzo calzino è sempre uguale a uno dei primi due.'],
                es: ['En un cajón a oscuras hay calcetines negros y blancos. ¿Cuántos debes coger para tener seguro un par igual?', 'Con solo dos colores, el tercer calcetín siempre coincide con uno de los dos primeros.'],
                fr: ['Dans un tiroir sombre, il y a des chaussettes noires et blanches. Combien faut-il en prendre pour être sûr d’avoir une paire ?', 'Avec deux couleurs seulement, la troisième chaussette est toujours pareille à l’une des deux premières.'],
                de: ['In einer dunklen Schublade liegen schwarze und weiße Socken. Wie viele musst du nehmen, um sicher ein gleiches Paar zu haben?', 'Bei nur zwei Farben passt die dritte Socke immer zu einer der ersten beiden.'],
            },
        },
        {
            id: 'm07', level: 'medium', t: {
                en: ['Each cut of a log takes 3 minutes. How long does it take to cut it into 4 pieces?', '4 pieces need only 3 cuts: 3 × 3 = 9.', ['9 min', '12 min', '6 min', '15 min']],
                it: ['Ogni taglio di un tronco richiede 3 minuti. Quanto serve per tagliarlo in 4 pezzi?', 'Per 4 pezzi bastano 3 tagli: 3 × 3 = 9.', ['9 min', '12 min', '6 min', '15 min']],
                es: ['Cada corte de un tronco lleva 3 minutos. ¿Cuánto se tarda en cortarlo en 4 trozos?', 'Para 4 trozos bastan 3 cortes: 3 × 3 = 9.', ['9 min', '12 min', '6 min', '15 min']],
                fr: ['Chaque coupe d’un tronc prend 3 minutes. Combien de temps pour le couper en 4 morceaux ?', 'Pour 4 morceaux, 3 coupes suffisent : 3 × 3 = 9.', ['9 min', '12 min', '6 min', '15 min']],
                de: ['Jeder Schnitt durch einen Stamm dauert 3 Minuten. Wie lange dauert es, ihn in 4 Stücke zu teilen?', 'Für 4 Stücke reichen 3 Schnitte: 3 × 3 = 9.', ['9 Min.', '12 Min.', '6 Min.', '15 Min.']],
            },
        },
        {
            id: 'm08', level: 'medium', o: ['18', '24', '12', '20'], t: {
                en: ['Luca is 6 and Anna is twice his age. How old will Anna be when Luca is 12?', 'Anna is 12, 6 years older than Luca, and the gap never changes: 12 + 6 = 18.'],
                it: ['Luca ha 6 anni e Anna il doppio. Quanti anni avrà Anna quando Luca ne avrà 12?', 'Anna ha 12 anni, 6 più di Luca, e la differenza non cambia: 12 + 6 = 18.'],
                es: ['Luca tiene 6 años y Anna el doble. ¿Cuántos años tendrá Anna cuando Luca tenga 12?', 'Anna tiene 12 años, 6 más que Luca, y la diferencia no cambia: 12 + 6 = 18.'],
                fr: ['Luca a 6 ans et Anna le double. Quel âge aura Anna quand Luca aura 12 ans ?', 'Anna a 12 ans, 6 de plus que Luca, et l’écart ne change pas : 12 + 6 = 18.'],
                de: ['Luca ist 6 und Anna doppelt so alt. Wie alt ist Anna, wenn Luca 12 ist?', 'Anna ist 12, also 6 Jahre älter als Luca, und der Abstand bleibt gleich: 12 + 6 = 18.'],
            },
        },
        {
            id: 'm09', level: 'medium', o: ['8', '10', '9', '7'], t: {
                en: ['A snail is at the bottom of a 10 m well. It climbs 3 m by day and slips back 2 m by night. On which day does it get out?', 'After 7 days it is at 7 m: on the eighth day it climbs 3 m and reaches the top before slipping.'],
                it: ['Una lumaca è in fondo a un pozzo di 10 m. Di giorno sale di 3 m e di notte scivola giù di 2 m. In che giorno esce?', 'Dopo 7 giorni è a 7 m: l’ottavo giorno sale di 3 m e arriva in cima prima di scivolare.'],
                es: ['Un caracol está en el fondo de un pozo de 10 m. De día sube 3 m y de noche resbala 2 m. ¿Qué día sale?', 'Tras 7 días está a 7 m: el octavo día sube 3 m y llega arriba antes de resbalar.'],
                fr: ['Un escargot est au fond d’un puits de 10 m. Le jour il monte de 3 m, la nuit il glisse de 2 m. Quel jour sort-il ?', 'Après 7 jours il est à 7 m : le huitième jour il monte de 3 m et arrive en haut avant de glisser.'],
                de: ['Eine Schnecke sitzt am Grund eines 10 m tiefen Brunnens. Tagsüber klettert sie 3 m, nachts rutscht sie 2 m zurück. An welchem Tag ist sie draußen?', 'Nach 7 Tagen ist sie bei 7 m: Am achten Tag klettert sie 3 m und ist oben, bevor sie rutscht.'],
            },
        },
        {
            id: 'm10', level: 'medium', o: ['20', '10', '11', '19'], t: {
                en: ['Counting from 1 to 100, how many times do you write the digit 9?', '10 times as the last digit (9, 19… 99) and 10 as the tens digit (90… 99): 99 counts twice.'],
                it: ['Contando da 1 a 100, quante volte scrivi la cifra 9?', '10 volte come ultima cifra (9, 19… 99) e 10 come decina (90… 99): il 99 vale due volte.'],
                es: ['Contando de 1 a 100, ¿cuántas veces escribes la cifra 9?', '10 veces como última cifra (9, 19… 99) y 10 como decena (90… 99): el 99 cuenta dos veces.'],
                fr: ['En comptant de 1 à 100, combien de fois écris-tu le chiffre 9 ?', '10 fois en dernier chiffre (9, 19… 99) et 10 fois en dizaine (90… 99) : 99 compte deux fois.'],
                de: ['Wie oft schreibst du die Ziffer 9, wenn du von 1 bis 100 zählst?', '10-mal als letzte Ziffer (9, 19… 99) und 10-mal als Zehnerziffer (90… 99): Die 99 zählt doppelt.'],
            },
        },

        // ------------------------------------------------------------------ hard
        {
            id: 'h01', level: 'hard', o: ['7', '5', '6', '9'], t: {
                en: ['A boy has as many brothers as sisters. His sister has twice as many brothers as sisters. How many children are there?', '4 boys and 3 girls: a boy has 3 brothers and 3 sisters, a girl has 4 brothers and 2 sisters.'],
                it: ['Un bambino ha tanti fratelli quante sorelle. Sua sorella ha il doppio di fratelli rispetto alle sorelle. Quanti figli sono in tutto?', '4 maschi e 3 femmine: un maschio ha 3 fratelli e 3 sorelle, una femmina ha 4 fratelli e 2 sorelle.'],
                es: ['Un niño tiene tantos hermanos como hermanas. Su hermana tiene el doble de hermanos que de hermanas. ¿Cuántos hijos son en total?', '4 niños y 3 niñas: un niño tiene 3 hermanos y 3 hermanas, una niña tiene 4 hermanos y 2 hermanas.'],
                fr: ['Un garçon a autant de frères que de sœurs. Sa sœur a deux fois plus de frères que de sœurs. Combien d’enfants y a-t-il ?', '4 garçons et 3 filles : un garçon a 3 frères et 3 sœurs, une fille a 4 frères et 2 sœurs.'],
                de: ['Ein Junge hat gleich viele Brüder wie Schwestern. Seine Schwester hat doppelt so viele Brüder wie Schwestern. Wie viele Kinder sind es?', '4 Jungen und 3 Mädchen: Ein Junge hat 3 Brüder und 3 Schwestern, ein Mädchen 4 Brüder und 2 Schwestern.'],
            },
        },
        {
            id: 'h02', level: 'hard', t: {
                en: ['Each of A and B either always tells the truth or always lies. A says: “B always lies.” B says: “A and I are the same kind.” Who is who?', 'If A lied, B would be honest and would be wrong to say they are the same: impossible. So A is honest and B lies.', ['A honest, B liar', 'A liar, B honest', 'Both honest', 'Both liars']],
                it: ['A e B dicono sempre la verità oppure mentono sempre. A dice: “B mente sempre.” B dice: “A e io siamo dello stesso tipo.” Chi è chi?', 'Se A mentisse, B sarebbe sincero e sbaglierebbe a dire che sono uguali: impossibile. Quindi A è sincero e B mente.', ['A sincero, B bugiardo', 'A bugiardo, B sincero', 'Entrambi sinceri', 'Entrambi bugiardi']],
                es: ['A y B dicen siempre la verdad o mienten siempre. A dice: “B siempre miente.” B dice: “A y yo somos del mismo tipo.” ¿Quién es quién?', 'Si A mintiera, B sería sincero y se equivocaría al decir que son iguales: imposible. Así que A es sincero y B miente.', ['A sincero, B mentiroso', 'A mentiroso, B sincero', 'Ambos sinceros', 'Ambos mentirosos']],
                fr: ['A et B disent toujours la vérité ou mentent toujours. A dit : « B ment toujours. » B dit : « A et moi sommes du même genre. » Qui est qui ?', 'Si A mentait, B serait sincère et aurait tort de dire qu’ils sont pareils : impossible. Donc A est sincère et B ment.', ['A sincère, B menteur', 'A menteur, B sincère', 'Tous deux sincères', 'Tous deux menteurs']],
                de: ['A und B sagen entweder immer die Wahrheit oder lügen immer. A sagt: „B lügt immer.“ B sagt: „A und ich sind von derselben Sorte.“ Wer ist was?', 'Würde A lügen, wäre B ehrlich und läge falsch damit, dass sie gleich sind: unmöglich. Also ist A ehrlich und B lügt.', ['A ehrlich, B Lügner', 'A Lügner, B ehrlich', 'Beide ehrlich', 'Beide Lügner']],
            },
        },
        {
            id: 'h03', level: 'hard', t: {
                en: ['Three boxes are labelled Apples, Pears and Mixed, and all the labels are wrong. Which box do you take one fruit from to know them all?', 'The “Mixed” box holds only one kind of fruit: one piece tells you which, and the wrong labels settle the other two.', ['Mixed', 'Apples', 'Pears', 'You need two boxes']],
                it: ['Tre scatole hanno le etichette Mele, Pere e Miste, e sono tutte sbagliate. Da quale scatola prendi un frutto per capirle tutte?', 'La scatola “Miste” contiene un solo tipo di frutta: un pezzo ti dice quale, e le etichette sbagliate decidono le altre due.', ['Miste', 'Mele', 'Pere', 'Servono due scatole']],
                es: ['Tres cajas tienen las etiquetas Manzanas, Peras y Mezcla, y todas están mal. ¿De qué caja sacas una fruta para saberlo todo?', 'La caja “Mezcla” solo tiene un tipo de fruta: una pieza te dice cuál, y las etiquetas erróneas deciden las otras dos.', ['Mezcla', 'Manzanas', 'Peras', 'Hacen falta dos cajas']],
                fr: ['Trois boîtes portent les étiquettes Pommes, Poires et Mélange, et toutes sont fausses. Dans quelle boîte prends-tu un fruit pour tout savoir ?', 'La boîte « Mélange » ne contient qu’une sorte de fruit : un seul te dit laquelle, et les étiquettes fausses règlent les deux autres.', ['Mélange', 'Pommes', 'Poires', 'Il faut deux boîtes']],
                de: ['Drei Kisten tragen die Etiketten Äpfel, Birnen und Gemischt, und alle sind falsch. Aus welcher Kiste nimmst du eine Frucht, um alles zu wissen?', 'Die Kiste „Gemischt“ enthält nur eine Sorte: Eine Frucht verrät welche, und die falschen Etiketten klären den Rest.', ['Gemischt', 'Äpfel', 'Birnen', 'Man braucht zwei Kisten']],
            },
        },
        {
            id: 'h04', level: 'hard', o: ['15', '30', '36', '12'], t: {
                en: ['6 friends meet and everyone shakes hands once with everyone else. How many handshakes are there?', '5 + 4 + 3 + 2 + 1 = 15: each new person greets only those not yet greeted.'],
                it: ['6 amici si incontrano e ognuno stringe la mano una volta a tutti gli altri. Quante strette di mano ci sono?', '5 + 4 + 3 + 2 + 1 = 15: ognuno saluta solo chi non ha ancora salutato.'],
                es: ['6 amigos se encuentran y cada uno da la mano una vez a todos los demás. ¿Cuántos apretones de manos hay?', '5 + 4 + 3 + 2 + 1 = 15: cada uno saluda solo a quien aún no ha saludado.'],
                fr: ['6 amis se retrouvent et chacun serre la main une fois à tous les autres. Combien y a-t-il de poignées de main ?', '5 + 4 + 3 + 2 + 1 = 15 : chacun ne salue que ceux qu’il n’a pas encore salués.'],
                de: ['6 Freunde treffen sich und jeder gibt jedem anderen einmal die Hand. Wie oft werden Hände geschüttelt?', '5 + 4 + 3 + 2 + 1 = 15: Jeder begrüßt nur die, die er noch nicht begrüßt hat.'],
            },
        },
        {
            id: 'h05', level: 'hard', o: ['2', '3', '4', '1'], t: {
                en: ['Of 9 identical coins, one is lighter. How many weighings on a balance do you need to be sure of finding it?', 'Compare 3 against 3: you learn which group of 3 holds it. Then compare 1 against 1 from that group.'],
                it: ['Tra 9 monete uguali, una è più leggera. Quante pesate con una bilancia a due piatti servono per trovarla con certezza?', 'Confronta 3 contro 3: scopri in quale gruppo da 3 si trova. Poi confronta 1 contro 1 in quel gruppo.'],
                es: ['De 9 monedas iguales, una pesa menos. ¿Cuántas pesadas con una balanza de platillos hacen falta para encontrarla seguro?', 'Compara 3 contra 3: sabrás en qué grupo de 3 está. Luego compara 1 contra 1 en ese grupo.'],
                fr: ['Parmi 9 pièces identiques, une est plus légère. Combien de pesées sur une balance à deux plateaux faut-il pour la trouver à coup sûr ?', 'Compare 3 contre 3 : tu sais dans quel groupe de 3 elle se trouve. Puis compare 1 contre 1 dans ce groupe.'],
                de: ['Von 9 gleichen Münzen ist eine leichter. Wie viele Wägungen auf einer Balkenwaage brauchst du, um sie sicher zu finden?', 'Vergleiche 3 gegen 3: So weißt du, in welcher Dreiergruppe sie ist. Dann vergleiche in dieser Gruppe 1 gegen 1.'],
            },
        },
        {
            id: 'h06', level: 'hard', o: ['40', '30', '60', '50'], t: {
                en: ['A father is 4 times as old as his son. In 20 years he will be twice as old. How old is the father now?', 'Son 10 and father 40. In 20 years: 30 and 60.'],
                it: ['Un padre ha 4 volte l’età del figlio. Tra 20 anni ne avrà il doppio. Quanti anni ha ora il padre?', 'Figlio 10 e padre 40. Tra 20 anni: 30 e 60.'],
                es: ['Un padre tiene 4 veces la edad de su hijo. Dentro de 20 años tendrá el doble. ¿Cuántos años tiene ahora el padre?', 'Hijo 10 y padre 40. Dentro de 20 años: 30 y 60.'],
                fr: ['Un père a 4 fois l’âge de son fils. Dans 20 ans, il aura le double. Quel âge a le père maintenant ?', 'Fils 10 ans et père 40. Dans 20 ans : 30 et 60.'],
                de: ['Ein Vater ist 4-mal so alt wie sein Sohn. In 20 Jahren ist er doppelt so alt. Wie alt ist der Vater jetzt?', 'Sohn 10 und Vater 40. In 20 Jahren: 30 und 60.'],
            },
        },
        {
            id: 'h07', level: 'hard', o: ['15 km', '20 km', '30 km', '10 km'], t: {
                en: ['Two cyclists 20 km apart ride towards each other at 10 km/h. A fly goes back and forth between them at 15 km/h until they meet. How far does it fly?', 'They meet after 1 hour, and the fly flies for that hour at 15 km/h.'],
                it: ['Due ciclisti distanti 20 km si vengono incontro a 10 km/h. Una mosca vola avanti e indietro tra loro a 15 km/h finché si incontrano. Quanta strada fa?', 'Si incontrano dopo 1 ora, e la mosca vola per quell’ora a 15 km/h.'],
                es: ['Dos ciclistas a 20 km de distancia van el uno hacia el otro a 10 km/h. Una mosca vuela entre ellos a 15 km/h hasta que se encuentran. ¿Cuánta distancia recorre?', 'Se encuentran tras 1 hora, y la mosca vuela esa hora a 15 km/h.'],
                fr: ['Deux cyclistes à 20 km l’un de l’autre roulent l’un vers l’autre à 10 km/h. Une mouche fait l’aller-retour entre eux à 15 km/h jusqu’à leur rencontre. Quelle distance parcourt-elle ?', 'Ils se rencontrent après 1 heure, et la mouche vole pendant cette heure à 15 km/h.'],
                de: ['Zwei Radfahrer, 20 km entfernt, fahren mit 10 km/h aufeinander zu. Eine Fliege pendelt mit 15 km/h zwischen ihnen, bis sie sich treffen. Wie weit fliegt sie?', 'Sie treffen sich nach 1 Stunde, und die Fliege fliegt diese Stunde mit 15 km/h.'],
            },
        },
        {
            id: 'h08', level: 'hard', o: ['5050', '5000', '5100', '10100'], t: {
                en: ['What is the sum of all the whole numbers from 1 to 100?', '50 pairs that each make 101 (1 + 100, 2 + 99…): 50 × 101 = 5050.'],
                it: ['Quanto fa la somma di tutti i numeri interi da 1 a 100?', '50 coppie che fanno 101 (1 + 100, 2 + 99…): 50 × 101 = 5050.'],
                es: ['¿Cuánto es la suma de todos los números enteros del 1 al 100?', '50 parejas que suman 101 (1 + 100, 2 + 99…): 50 × 101 = 5050.'],
                fr: ['Quelle est la somme de tous les nombres entiers de 1 à 100 ?', '50 paires qui font 101 (1 + 100, 2 + 99…) : 50 × 101 = 5050.'],
                de: ['Wie groß ist die Summe aller ganzen Zahlen von 1 bis 100?', '50 Paare mit der Summe 101 (1 + 100, 2 + 99…): 50 × 101 = 5050.'],
            },
        },
        {
            id: 'h09', level: 'hard', o: ['3', '100', '33', '10'], t: {
                en: ['3 cats catch 3 mice in 3 minutes. How many cats are needed to catch 100 mice in 100 minutes?', 'The 3 cats catch 1 mouse a minute together: in 100 minutes, 100 mice.'],
                it: ['3 gatti prendono 3 topi in 3 minuti. Quanti gatti servono per prendere 100 topi in 100 minuti?', 'I 3 gatti insieme prendono 1 topo al minuto: in 100 minuti, 100 topi.'],
                es: ['3 gatos cazan 3 ratones en 3 minutos. ¿Cuántos gatos hacen falta para cazar 100 ratones en 100 minutos?', 'Los 3 gatos juntos cazan 1 ratón por minuto: en 100 minutos, 100 ratones.'],
                fr: ['3 chats attrapent 3 souris en 3 minutes. Combien de chats faut-il pour attraper 100 souris en 100 minutes ?', 'Les 3 chats ensemble attrapent 1 souris par minute : en 100 minutes, 100 souris.'],
                de: ['3 Katzen fangen 3 Mäuse in 3 Minuten. Wie viele Katzen braucht man für 100 Mäuse in 100 Minuten?', 'Die 3 Katzen fangen zusammen 1 Maus pro Minute: in 100 Minuten also 100 Mäuse.'],
            },
        },
        {
            id: 'h10', level: 'hard', o: ['6', '2', '4', '8'], t: {
                en: ['What is the last digit of 2 raised to the power of 100?', 'The last digits repeat: 2, 4, 8, 6. 100 is a multiple of 4, so it ends in 6.'],
                it: ['Qual è l’ultima cifra di 2 elevato alla 100?', 'Le ultime cifre si ripetono: 2, 4, 8, 6. 100 è multiplo di 4, quindi finisce con 6.'],
                es: ['¿Cuál es la última cifra de 2 elevado a 100?', 'Las últimas cifras se repiten: 2, 4, 8, 6. 100 es múltiplo de 4, así que termina en 6.'],
                fr: ['Quel est le dernier chiffre de 2 à la puissance 100 ?', 'Les derniers chiffres se répètent : 2, 4, 8, 6. 100 est un multiple de 4, donc il finit par 6.'],
                de: ['Was ist die letzte Ziffer von 2 hoch 100?', 'Die letzten Ziffern wiederholen sich: 2, 4, 8, 6. 100 ist durch 4 teilbar, also endet die Zahl auf 6.'],
            },
        },
        {
            id: 'h11', level: 'hard', o: ['22', '4', '12', '21'], t: {
                en: ['A drawer holds 10 black, 10 white and 10 blue socks. How many must you take in the dark to be sure of two black ones?', 'In the worst case you take the 20 others first: then 2 more are black.'],
                it: ['In un cassetto ci sono 10 calzini neri, 10 bianchi e 10 blu. Quanti devi prenderne al buio per averne di sicuro due neri?', 'Nel caso peggiore prendi prima gli altri 20: poi altri 2 sono neri.'],
                es: ['En un cajón hay 10 calcetines negros, 10 blancos y 10 azules. ¿Cuántos debes coger a oscuras para tener seguro dos negros?', 'En el peor caso coges primero los otros 20: luego otros 2 son negros.'],
                fr: ['Un tiroir contient 10 chaussettes noires, 10 blanches et 10 bleues. Combien faut-il en prendre dans le noir pour être sûr d’en avoir deux noires ?', 'Au pire, tu prends d’abord les 20 autres : les 2 suivantes sont noires.'],
                de: ['In einer Schublade liegen 10 schwarze, 10 weiße und 10 blaue Socken. Wie viele musst du im Dunkeln nehmen, um sicher zwei schwarze zu haben?', 'Im schlimmsten Fall nimmst du zuerst die 20 anderen: Die nächsten 2 sind schwarz.'],
            },
        },

        // ------------------------------------------------------------------ clever / lateral (hard)
        {
            id: 'x01', level: 'hard', o: ['10', '50', '25', '1'], t: {
                en: ['100 lockers are closed. Person 1 changes every locker, person 2 every second one, person 3 every third, up to person 100. How many stay open?', 'A locker changes once for each divisor of its number. Only perfect squares have an odd number of divisors: 1, 4, 9… 100.'],
                it: ['100 armadietti sono chiusi. La persona 1 li cambia tutti, la 2 uno ogni due, la 3 uno ogni tre, fino alla 100. Quanti restano aperti?', 'Un armadietto cambia una volta per ogni divisore del suo numero. Solo i quadrati perfetti hanno un numero dispari di divisori: 1, 4, 9… 100.'],
                es: ['100 taquillas están cerradas. La persona 1 las cambia todas, la 2 una de cada dos, la 3 una de cada tres, hasta la 100. ¿Cuántas quedan abiertas?', 'Una taquilla cambia una vez por cada divisor de su número. Solo los cuadrados perfectos tienen un número impar de divisores: 1, 4, 9… 100.'],
                fr: ['100 casiers sont fermés. La personne 1 les change tous, la 2 un sur deux, la 3 un sur trois, jusqu’à la 100. Combien restent ouverts ?', 'Un casier change une fois par diviseur de son numéro. Seuls les carrés parfaits ont un nombre impair de diviseurs : 1, 4, 9… 100.'],
                de: ['100 Spinde sind geschlossen. Person 1 ändert alle, Person 2 jeden zweiten, Person 3 jeden dritten, bis Person 100. Wie viele bleiben offen?', 'Ein Spind ändert sich einmal pro Teiler seiner Nummer. Nur Quadratzahlen haben eine ungerade Zahl von Teilern: 1, 4, 9… 100.'],
            },
        },
        {
            id: 'x02', level: 'hard', t: {
                en: ['One of A, B and C took the cake, and exactly one of them tells the truth. A: “B did it.” B: “It wasn’t me.” C: “It wasn’t me.” Who took it?', 'If it were A, both B and C would be telling the truth. If it were B, both A and C would. If it was C, only B tells the truth.', ['C', 'A', 'B', 'You can’t tell']],
                it: ['Uno tra A, B e C ha preso la torta, e solo uno dei tre dice la verità. A: “È stato B.” B: “Non sono stato io.” C: “Non sono stato io.” Chi l’ha presa?', 'Se fosse A, direbbero la verità sia B sia C. Se fosse B, sia A sia C. Se è C, dice la verità solo B.', ['C', 'A', 'B', 'Non si può dire']],
                es: ['Uno de A, B y C cogió la tarta, y solo uno de los tres dice la verdad. A: “Fue B.” B: “No fui yo.” C: “No fui yo.” ¿Quién la cogió?', 'Si fuera A, dirían la verdad B y C. Si fuera B, A y C. Si es C, solo B dice la verdad.', ['C', 'A', 'B', 'No se puede saber']],
                fr: ['L’un de A, B et C a pris le gâteau, et un seul des trois dit la vérité. A : « C’est B. » B : « Ce n’est pas moi. » C : « Ce n’est pas moi. » Qui l’a pris ?', 'Si c’était A, B et C diraient la vérité. Si c’était B, A et C. Si c’est C, seul B dit la vérité.', ['C', 'A', 'B', 'On ne peut pas savoir']],
                de: ['Einer von A, B und C hat den Kuchen genommen, und genau einer sagt die Wahrheit. A: „Es war B.“ B: „Ich war es nicht.“ C: „Ich war es nicht.“ Wer war es?', 'Wäre es A, sagten B und C die Wahrheit. Wäre es B, dann A und C. Ist es C, sagt nur B die Wahrheit.', ['C', 'A', 'B', 'Das lässt sich nicht sagen']],
            },
        },
        {
            id: 'x03', level: 'hard', t: {
                en: ['The day before yesterday Marco was 17, and next year he will turn 20. What is today’s date?', 'He was born on 31 December. On 30 December he was 17, on the 31st he turned 18, this year he turns 19 and next year 20.', ['1 January', '31 December', '30 December', '2 January']],
                it: ['L’altro ieri Marco aveva 17 anni, e l’anno prossimo ne compirà 20. Che giorno è oggi?', 'È nato il 31 dicembre. Il 30 dicembre aveva 17 anni, il 31 ne ha compiuti 18, quest’anno ne compie 19 e l’anno prossimo 20.', ['1 gennaio', '31 dicembre', '30 dicembre', '2 gennaio']],
                es: ['Anteayer Marco tenía 17 años, y el año que viene cumplirá 20. ¿Qué día es hoy?', 'Nació el 31 de diciembre. El 30 de diciembre tenía 17, el 31 cumplió 18, este año cumple 19 y el que viene 20.', ['1 de enero', '31 de diciembre', '30 de diciembre', '2 de enero']],
                fr: ['Avant-hier, Marco avait 17 ans, et l’an prochain il aura 20 ans. Quel jour sommes-nous ?', 'Il est né le 31 décembre. Le 30 décembre il avait 17 ans, le 31 il en a eu 18, cette année il en aura 19 et l’an prochain 20.', ['1er janvier', '31 décembre', '30 décembre', '2 janvier']],
                de: ['Vorgestern war Marco 17, und nächstes Jahr wird er 20. Welches Datum ist heute?', 'Er ist am 31. Dezember geboren. Am 30. Dezember war er 17, am 31. wurde er 18, dieses Jahr wird er 19 und nächstes Jahr 20.', ['1. Januar', '31. Dezember', '30. Dezember', '2. Januar']],
            },
        },
        {
            id: 'x04', level: 'hard', o: ['24', '20', '25', '10'], t: {
                en: ['Multiply all the numbers from 1 to 100. How many zeros does the result end with?', 'Each zero needs a factor 5, and there are plenty of 2s. Multiples of 5 give 20 fives, and those of 25 give 4 more.'],
                it: ['Moltiplica tutti i numeri da 1 a 100. Con quanti zeri finisce il risultato?', 'Ogni zero richiede un fattore 5, e i 2 non mancano. I multipli di 5 danno 20 cinque, quelli di 25 altri 4.'],
                es: ['Multiplica todos los números del 1 al 100. ¿En cuántos ceros termina el resultado?', 'Cada cero necesita un factor 5, y los 2 sobran. Los múltiplos de 5 dan 20 cincos y los de 25 otros 4.'],
                fr: ['Multiplie tous les nombres de 1 à 100. Par combien de zéros finit le résultat ?', 'Chaque zéro demande un facteur 5, et les 2 ne manquent pas. Les multiples de 5 donnent 20 cinq, ceux de 25 encore 4.'],
                de: ['Multipliziere alle Zahlen von 1 bis 100. Auf wie viele Nullen endet das Ergebnis?', 'Jede Null braucht einen Faktor 5, Zweien gibt es genug. Die Vielfachen von 5 liefern 20 Fünfen, die von 25 noch 4 dazu.'],
            },
        },
        {
            id: 'x05', level: 'hard', o: ['2/3', '1/2', '1/3', '1/4'], t: {
                en: ['A prize is behind one of 3 doors. You pick one; the host, who knows where it is, always opens another door without the prize and lets you switch. If you switch, what is your chance of winning?', 'Your first door wins 1 time in 3. In the other 2 cases out of 3 the prize is behind the door you switch to.'],
                it: ['Un premio è dietro una di 3 porte. Ne scegli una; il conduttore, che sa dov’è, apre sempre un’altra porta senza premio e ti lascia cambiare. Se cambi, che probabilità hai di vincere?', 'La prima porta vince 1 volta su 3. Negli altri 2 casi su 3 il premio è dietro la porta su cui cambi.'],
                es: ['Hay un premio detrás de una de 3 puertas. Eliges una; el presentador, que sabe dónde está, abre siempre otra puerta sin premio y te deja cambiar. Si cambias, ¿qué probabilidad tienes de ganar?', 'La primera puerta gana 1 vez de cada 3. En los otros 2 casos de 3, el premio está tras la puerta a la que cambias.'],
                fr: ['Un prix se cache derrière l’une de 3 portes. Tu en choisis une ; l’animateur, qui sait où il est, ouvre toujours une autre porte sans prix et te laisse changer. Si tu changes, quelle est ta probabilité de gagner ?', 'La première porte gagne 1 fois sur 3. Dans les 2 autres cas sur 3, le prix est derrière la porte vers laquelle tu changes.'],
                de: ['Hinter einer von 3 Türen ist ein Preis. Du wählst eine; der Moderator, der alles weiß, öffnet immer eine andere Tür ohne Preis und lässt dich wechseln. Wie hoch ist deine Gewinnchance, wenn du wechselst?', 'Die erste Tür gewinnt in 1 von 3 Fällen. In den anderen 2 von 3 Fällen ist der Preis hinter der Tür, zu der du wechselst.'],
            },
        },
        {
            id: 'x06', level: 'hard', o: ['40 km/h', '45 km/h', '50 km/h', '35 km/h'], t: {
                en: ['You drive to a town at 60 km/h and come back on the same road at 30 km/h. What is your average speed for the whole trip?', 'For 60 km you take 1 hour there and 2 hours back: 120 km in 3 hours.'],
                it: ['Vai in un paese a 60 km/h e torni sulla stessa strada a 30 km/h. Qual è la velocità media di tutto il viaggio?', 'Per 60 km ci metti 1 ora all’andata e 2 al ritorno: 120 km in 3 ore.'],
                es: ['Vas a un pueblo a 60 km/h y vuelves por la misma carretera a 30 km/h. ¿Cuál es la velocidad media de todo el viaje?', 'Para 60 km tardas 1 hora a la ida y 2 a la vuelta: 120 km en 3 horas.'],
                fr: ['Tu vas dans un village à 60 km/h et tu reviens par la même route à 30 km/h. Quelle est ta vitesse moyenne sur tout le trajet ?', 'Pour 60 km, il te faut 1 heure à l’aller et 2 au retour : 120 km en 3 heures.'],
                de: ['Du fährst mit 60 km/h in einen Ort und auf derselben Straße mit 30 km/h zurück. Wie hoch ist deine Durchschnittsgeschwindigkeit?', 'Für 60 km brauchst du hin 1 Stunde und zurück 2: 120 km in 3 Stunden.'],
            },
        },
        {
            id: 'x07', level: 'hard', o: ['10', '100', '500', '32'], t: {
                en: ['One of 1000 bottles is poisoned. You can test drops from many bottles at once on a strip, and all the strips show their result together after a day. How many strips do you need?', 'Write each bottle’s number in binary, with one strip per digit: 10 digits count up to 1024, and the strips that react spell out the number.'],
                it: ['Una di 1000 bottiglie è avvelenata. Su una striscia puoi provare gocce di molte bottiglie insieme, e tutte le strisce danno il risultato insieme dopo un giorno. Quante strisce servono?', 'Scrivi il numero di ogni bottiglia in binario, con una striscia per cifra: 10 cifre contano fino a 1024, e le strisce che reagiscono compongono il numero.'],
                es: ['Una de 1000 botellas está envenenada. En una tira puedes probar gotas de muchas botellas a la vez, y todas las tiras dan el resultado juntas al cabo de un día. ¿Cuántas tiras necesitas?', 'Escribe el número de cada botella en binario, con una tira por cifra: 10 cifras cuentan hasta 1024, y las tiras que reaccionan forman el número.'],
                fr: ['Une bouteille sur 1000 est empoisonnée. Sur une bandelette, tu peux tester des gouttes de plusieurs bouteilles, et toutes les bandelettes donnent le résultat ensemble après un jour. Combien en faut-il ?', 'Écris le numéro de chaque bouteille en binaire, une bandelette par chiffre : 10 chiffres comptent jusqu’à 1024, et les bandelettes qui réagissent forment le numéro.'],
                de: ['Eine von 1000 Flaschen ist vergiftet. Auf einem Teststreifen kannst du Tropfen vieler Flaschen prüfen, und alle Streifen zeigen ihr Ergebnis gemeinsam nach einem Tag. Wie viele Streifen brauchst du?', 'Schreibe die Nummer jeder Flasche binär, ein Streifen pro Ziffer: 10 Ziffern zählen bis 1024, und die Streifen, die reagieren, ergeben die Nummer.'],
            },
        },
        {
            id: 'x08', level: 'hard', o: ['22', '24', '23', '12'], t: {
                en: ['In a full day of 24 hours, how many times do the hour and minute hands of a clock overlap?', 'The minute hand catches the hour hand 11 times every 12 hours, not 12: 2 × 11 = 22.'],
                it: ['In un giorno intero di 24 ore, quante volte si sovrappongono le lancette delle ore e dei minuti?', 'La lancetta dei minuti raggiunge quella delle ore 11 volte ogni 12 ore, non 12: 2 × 11 = 22.'],
                es: ['En un día entero de 24 horas, ¿cuántas veces se superponen las agujas de las horas y los minutos?', 'La aguja de los minutos alcanza a la de las horas 11 veces cada 12 horas, no 12: 2 × 11 = 22.'],
                fr: ['En une journée entière de 24 heures, combien de fois les aiguilles des heures et des minutes se superposent-elles ?', 'L’aiguille des minutes rattrape celle des heures 11 fois toutes les 12 heures, pas 12 : 2 × 11 = 22.'],
                de: ['Wie oft decken sich Stunden- und Minutenzeiger an einem ganzen Tag von 24 Stunden?', 'Der Minutenzeiger holt den Stundenzeiger alle 12 Stunden 11-mal ein, nicht 12-mal: 2 × 11 = 22.'],
            },
        },
        {
            id: 'x09', level: 'hard', o: ['1', '2', '3', '4'], t: {
                en: ['Three switches outside a closed room control three old light bulbs inside. How many times must you enter the room to know which switch controls which bulb?', 'Turn on the first switch for a while, turn it off, turn on the second and go in: one bulb is lit, one is off but warm, one is off and cold.'],
                it: ['Tre interruttori fuori da una stanza chiusa comandano tre vecchie lampadine all’interno. Quante volte devi entrare per sapere quale interruttore comanda quale lampadina?', 'Accendi il primo per un po’, spegnilo, accendi il secondo ed entra: una lampadina è accesa, una spenta ma calda, una spenta e fredda.'],
                es: ['Tres interruptores fuera de una habitación cerrada controlan tres bombillas antiguas dentro. ¿Cuántas veces debes entrar para saber qué interruptor controla cada bombilla?', 'Enciende el primero un rato, apágalo, enciende el segundo y entra: una bombilla está encendida, otra apagada pero caliente y otra apagada y fría.'],
                fr: ['Trois interrupteurs à l’extérieur d’une pièce fermée commandent trois vieilles ampoules à l’intérieur. Combien de fois dois-tu entrer pour savoir quel interrupteur commande quelle ampoule ?', 'Allume le premier un moment, éteins-le, allume le deuxième et entre : une ampoule est allumée, une éteinte mais chaude, une éteinte et froide.'],
                de: ['Drei Schalter vor einem geschlossenen Raum steuern drei alte Glühbirnen darin. Wie oft musst du hineingehen, um zu wissen, welcher Schalter welche Birne steuert?', 'Schalte den ersten eine Weile ein, dann aus, schalte den zweiten ein und geh hinein: Eine Birne leuchtet, eine ist aus, aber warm, eine aus und kalt.'],
            },
        },
    ];
})(typeof window !== 'undefined' ? window : globalThis);
