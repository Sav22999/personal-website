<html>
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
    global $path_easyrepices;
    ?>
    <title>Easy Recipes &#8211; Supporto</title>
    <link rel="stylesheet" href="/old/easyrecipes/style/site.css"/>
    <link rel="icon" href="/old/easyrecipes/images/icon.png"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="/old/easyrecipes/script/site.js"></script>
</head>
<body>
<?php include_once($path_easyrepices . "/include/menu.php"); ?>

<span id="support-page" class="hidden"></span>

<aside class="left">
</aside>
<main class="padding-10 border-box">
    <h1>Supporto</h1>
    <div>
        In questa sezione troverai alcune risposte, guide o video che ti insegneranno a orientarti in Easy Recipes.
    </div>
    <div id="support-section" class="margin-bottom-10">
        <div class="clearfix">
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(0)">
                    Come inserire una nuova ricetta?
                    <div class="div-support-page-details hidden">
                        Per inserire una nuova ricetta è semplicissimo.
                        <br>
                        Per prima cosa è necessario aver effettuato l'accesso e disporre dei permessi minimi per poter
                        inserire una ricetta, in caso contrario contattare un moderatore.
                        <br>
                        Successivamente all'accesso, sarà presente un menu in alto a destra, dove c'è l'immagine di
                        profilo (impostabile su Gravatar).
                        <br>
                        Premendo su quell'immagine si aprirà il menu principale, dove è presente la voce "Nuova
                        ricetta". Premendo su questa voce si viene reindirizzati automaticamente alla pagina di
                        inserimento di una nuova ricetta.
                        <br>
                        Alternativamente è possibile premere sul pulsante "Inserisci una nuova ricetta" nella pagina
                        principale.
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(1)">
                    Non trovo un ingrediente nella lista degli ingredienti, come posso aggiungerne uno?
                    <div class="div-support-page-details hidden">
                        Se disponi dei permessi sufficienti dovresti trovare una voce nel menu principale "Nuovo
                        ingrediente" e nella pagina principale "Inserisci un nuovo ingrediente".
                        <br>
                        Una volta andati in questa pagina sarà sufficiente inserire il nome dell'ingrediente, il tipo e
                        premere su "Inserisci".
                        <br>
                        <b>Importante: il nome degli ingredienti deve essere sempre al singolare (<s>uova</s>→uovo,
                            ecc.)</b>
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(2)">
                    Ho inserito una ricetta ma non è ancora stata approvata e pubblicata, perché?
                    <div class="div-support-page-details hidden">
                        Una ricetta prima di essere pubblicata deve essere revisionata da un moderatore, che ne
                        verificherà la correttezza. Successivamente, se la ricetta è corretta, viene <b>Approvata</b>,
                        quindi viene anche pubblicata e il creatore della ricetta riceve <b>+5 punti</b>.
                        In caso contrario, quindi se viene <b>Rifiutata</b>, al creatore della ricetta vengono scalati
                        <b>-2 punti</b>.
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(3)">
                    Come posso inserire un'immagine in una ricetta?
                    <div class="div-support-page-details hidden">
                        Per inserire un'immagine in una ricetta è sufficiente fare clic su "Carica nuova immagine",
                        nella sezione "Immagini caricate", quindi scegliere l'immagine che si desidera inserire.
                        <br>
                        Una volta che l'immagine è stata caricata, quindi quando la si vede nella sezione "Immagini
                        inserite", fare clic sulla stessa; a questo punto l'immagine viene mostrata in una finestra
                        "pop-up" separata, quindi premere il pulsate "Inserisci questa immagine".
                        <br>
                        Attenzione: l'immagine, nella modalità "modifica" (o "creazione") non viene visualizzata
                        direttamente nella casella di testo, ma viene indicata con [[Image<b>N</b>]], dove <b>N</b> è un
                        numero intero.
                        <br>
                        Segue un esempio:
                        <br>
                        <br>
                        <div class="text-left">
                            1. Inserisco l'immagine desiderata:<br>
                            <img src="/old/easyrecipes/images/support/insert-image/1.png" class="width-100-perc"/>
                            <hr class="hr-center">
                            2. Faccio clic sopra l'immagine, quindi premo su "Inserisci questa immagine":<br>
                            <img src="/old/easyrecipes/images/support/insert-image/2.png" class="width-100-perc"/>
                            <hr class="hr-center">
                            3. L'immagine è stata inserita, infatti vedo [[Image1]] nella preparazione:<br>
                            <img src="/old/easyrecipes/images/support/insert-image/3.png" class="width-100-perc"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(4)">
                    Dove trovo le ricette che ho salvato come "Bozza"?
                    <div class="div-support-page-details hidden">
                        [Al momento il salvataggio come bozza non è disponibile].
                        <br>
                        Nella pagina principale è presenta la sezione "Ricette che hai salvato come bozza".
                        <br>
                        Qui vengono mostrate al massimo le ultime due ricette salvate come bozza
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(5)">
                    Come posso modificare una ricetta che ho inserito?
                    <div class="div-support-page-details hidden">
                        Al momento la modifica di una ricetta inserita non è disponibile.
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(6)">
                    Cosa si intende per "Tag"?
                    <div class="div-support-page-details hidden">
                        Tag o "etichette" sono delle parole chiave che permettono di ricercare e identificare, in
                        maniera più semplice, una ricetta.
                        <br>
                        Ad esempio è possibile specificare se sono presenti degli ingredienti allergeni, quali arachidi,
                        crostacei, eccetera.
                        <br>
                        Oppure è possibile specificare se è un primo piatto, un secondo, un dolce, un contorno.
                        <br>
                        E ancora, è possibile specificare per quante porzioni è la ricetta.
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(7)">
                    Come posso modificare o eliminare un ingrediente, il Paese di origine o un tag dopo averlo inserito?
                    <div class="div-support-page-details hidden">
                        Per modificare un ingrediente è sufficiente fare clic sul bottone relativo all'ingrediente che
                        si desidera modificare o eliminare.
                        <br>
                        Per modificare un tag, allo stesso modo, fare clic sul tag che si intende modificare o
                        eliminare.
                        <br>
                        Per modificare il Paese di origine è sufficiente fare clic sul bottone relativo, quindi
                        modificarlo o eliminarlo.
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(8)">
                    Qual è la differenza tra "Senza glutine" e "Senza latte e derivati"?
                    <div class="div-support-page-details hidden">
                        La dicitura "Senza glutine" è da impostare ai prodotti che non contengono glutine (proteina
                        tipica del frumento). Questa categorie potrebbe essere utile particolarmente per i celiaci o per
                        chi ha infiammazione intestinali causate dal glutine.
                        <br>
                        La dicitura "Senza latte e derivati" è utile per i soggetti intolleranti al lattosio.
                        <br>
                        Apporre queste due categorie in maniera corretta. Se non si è sicuri è preferibile non
                        selezionarla.
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(9)">
                    Non ricevo alcun codice OTP per effettuare l'accesso, cosa devo fare?
                    <div class="div-support-page-details hidden">
                        Le email provenienti da <code>noreply-easyrecipes@saveriomorelli.com</code> potrebbero essere
                        identificate come spam; per questo motivo, alcuni client email spostano le email provienti da
                        questo indirizzo email automaticamente nella Posta indesiderata (o Spam).
                        <br>
                        Verificare, pertanto, se l'email è in suddetta cartella, quindi fare click e copiare il codice
                        indicato nella email.
                        <br>
                        <br>
                        Nel caso in cui l'email non sia presente neanche in questa cartella, contattare
                        <code>@Sav22999</code> su Telegram oppure inviare un'email all'indirizzo <code>info@saveriomorelli.com</code>
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(10)">
                    Non ricordo più la password, come posso recuperarla?
                    <div class="div-support-page-details hidden">
                        Al momento la procedura per il ripristino della password non è disponibile.
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(11)">
                    Perché il codice OTP viene spostato automaticamente nella Posta indesiderata (o Spam)?
                    <div class="div-support-page-details hidden">
                        Questo avviene poiché il proprio client email rileva la email come indesiderata/spam. È
                        possibile aggiungere l'indirizzo email <code>noreply-easyrecipes@saveriomorelli.com</code> alle
                        eccezioni, in maniera tale che rimanga sempre nella Posta in arrivo (o Inbox).
                        <br>
                        Per far ciò aggiungere l'indirizzo email come Contatto.
                    </div>
                </div>
            </div>
            <div class="support-page-section-div">
                <div class="div-support-page" onclick="showDetailsSupportPage(12)">
                    Come si guadagnano i <i>Punti</i> e a che cosa servono?
                    <div class="div-support-page-details hidden">
                        I punti si guadagnano contribuendo a Easy Recipes. Si potranno richiedere delle ricompense in
                        base ai punti guadagnati.
                        <br>
                        Come guadagnare o perdere punti:
                        <ul>
                            <li><b>+1</b>: Quando si inserisce una nuova ricetta</li>
                            <li><b>+5</b>: Quando una ricetta viene approvata, quindi pubblicata</li>
                            <li><b class="red-button">-2</b>: Se una ricetta inserita viene rifiutata</li>
                            <li><b>+1</b>: Quando si esprime una reazione su una ricetta</li>
                            <li><b>+1</b>: Quando qualcuno esprime una reazione positiva su una ricetta</li>
                            <li><b class="red-button">-2</b>: Quando qualcuno esprime una reazione negativa su una
                                ricetta
                            </li>
                        </ul>
                        Anche i moderatori possono guadagnare punti facilmente:
                        <ul>
                            <li><b>+1</b>: Quando si revisiona una ricetta (rifiuta/approva)</li>
                            <li><b>+1</b>: Quando si aggiunge un nuovo Paese</li>
                            <li><b>+1</b>: Quando si aggiunge un nuovo ingrediente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<aside class="right">
</aside>
</body>
</html>
