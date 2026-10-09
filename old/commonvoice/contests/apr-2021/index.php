<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->
<html>
<head>
    <title>CV Project &#8211; Saverio Morelli</title>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/commonvoice/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/variables.php"); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="./get-userids.js"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-153189423-2">
    </script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'UA-153189423-2');
    </script>
</head>
<body>
<div id="top-bar">
    <a href="/old/commonvoice/">
        <div id="main-title">
            CV Project
        </div>
    </a>
</div>
<div class="margin-top100"></div>
<div id="proudly-basilicata" class="background-primary-color text-white-color font-family-basic">
    Sviluppata con il <span class="font-family-twemoji">🤍</span> in Basilicata e Trentino
</div>
<div class="background-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-50 padding-top-50 text-center">
    <a href="http://mzl.la/3129PDG" class="just-link">
        <button class="margin-5 font-family-basic">Vedi l'evento</button>
    </a>
    <a href="http://mzl.la/3vMdMdC" class="just-link">
        <button class="margin-5 font-family-basic">Leggi il regolamento</button>
    </a>
    <a href="https://mzl.la/2PgPfwI" class="just-link">
        <button class="margin-5 font-family-basic">Presentazione contest e link utili</button>
    </a>
    <a href="https://mzl.la/39nfKaS" class="just-link">
        <button class="margin-5 font-family-basic">Comunicato stampa</button>
    </a>
</div>

<?php if (date("Y-m-d") > "2021-04-30") { ?>
    <div class="background-primary-color text-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic font-size-20 border-radius-0">
        Il contest è ufficialmente concluso. Grazie mille a tutti coloro che hanno partecipato <span
                class="font-family-twemoji">😊</span>
    </div>
<?php } ?>

<div id="body" class="font-family-basic text-black-color">
    <div class="margin-10 font-size-20">
        <h1 class="h1-center"><span class="font-family-twemoji">🇮🇹</span> Mozilla Italia - Contest</h1>
        Validità: 1➞30 aprile 2021
    </div>

    <div class="rank">
        <h1 class="no-padding no-margin h1-center">Classifica</h1>
        <hr class="hr-center">
        <div class="statistics-data-table">
            <table id="rank">
                <tr id="title">
                    <th>Posizione</th>
                    <th>Nickname</th>
                    <th>UserID</th>
                    <th>Punteggio totale*</th>
                </tr>
                <tr>
                    <!--<td colspan="4">Caricamento in corso, potrebbe volerci fino a qualche minuto</td>-->
                    <td colspan="4">Il contest è terminato da troppo tempo, la classifica non è più disponibile
                        pubblicamente.<br>In caso la si voglia ugualmente consultare, contattami.
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        /*
        var filter = "1➞30 Apr 2021";
        var filter_to_use = "start_date=2021-04-01&end_date=2021-04-30";

        const awarded_participants = 20;
        var total_contributions = 0;

        var users_list = [];

        let today = new Date();
        var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();

        if (today.getFullYear() >= 2021 || today.getFullYear() == 2021 && (today.getMonth() + 1) >= 4 && today.getDate() >= 1) {
            users_list = getUserIdsParticipants();
            var n_users = 0;
            var n_users_temp = 0;

            console.log("Participants number: " + users_list.length);

            $(document).ready(function () {
                for (key in users_list) {
                    n_users += users_list[key].ids.length;
                }
                if (n_users > 0) {
                    for (key in users_list) {
                        for (i in users_list[key].ids) {
                            load_data(users_list[key].ids[i], key);
                        }
                    }
                } else {
                    $(".statistics-data-table").html("Il contest non è ancora iniziato oppure non ci sono partecipanti.");
                    $(".statistics-data-table").css({"padding": "30px"})
                }
            });
        } else {
            $(".statistics-data-table").html("Il contest non è ancora iniziato. Torna il 1 primo aprile!");
            $(".statistics-data-table").css({"padding": "30px"})
        }


        function load_data(userid, index) {
            $.ajax({
                url: "/api/common-voice-android/v2/app-usage/get/user/?id=" + userid + "&" + filter_to_use,
                type: 'get',
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response != null) {
                        $(".date-data").html("");

                        let listen = response["user-stats"]["listen"];
                        let speak = response["user-stats"]["speak"];

                        users_list[index].clips_validated += listen["validated"];
                        users_list[index].recordings_sent += speak["sent"];
                        users_list[index].sentences_reported += speak["reported"];

                        users_list[index].total_score = (users_list[index].clips_validated * 1) + (users_list[index].sentences_reported * 1) + (users_list[index].recordings_sent * 2);
                    } else {
                        //null
                    }
                    n_users_temp++;

                    if (n_users_temp == n_users) {
                        showRank();
                    }
                },
                error: function () {
                    //error
                }
            });
        }

        function showRank() {
            calculatePosition();

            //let table_content = '<tr id="title"><th>Position</th><th>UserID</th><th>Recordings sent</th><th>Sentences reported</th><th>Clips validated</th><th>Total score</th></tr>';
            let table_content = '<tr id="title"><th>Posizione</th><th>Nickname</th><th>UserID</th><th>Punteggio totale*</th></tr>';

            let position = 0, position_temp = 0, last_score = 0;
            for (key in users_list) {
                if (!users_list[key].skip_rank) position++;
                let userid_to_use = "";

                let td_to_use = "<td>";

                for (i in users_list[key].ids) {
                    if (userid_to_use != "") userid_to_use += "<br><a href='/commonvoice/app-usage/user/?userid=" + users_list[key].ids[i] + "&filter=mozita-contest-apr-2021' class='text-black-color'>" + users_list[key].ids[i] + "</a>";
                    else userid_to_use = "<a href='/commonvoice/app-usage/user/?userid=" + users_list[key].ids[i] + "&filter=mozita-contest-apr-2021' class='text-black-color'>" + users_list[key].ids[i] + "</a>";
                }
                if (!users_list[key].skip_rank) {
                    if (position == 1 || users_list[key].total_score < last_score) {
                        last_score = users_list[key].total_score;
                        position_temp = position;
                    }
                    if (position <= awarded_participants) {
                        td_to_use = "<td class='awarded-user'>";
                    }
                } else {
                    position_temp = "-";
                }

                total_contributions += users_list[key].total_score;
                var nickname = users_list[key].nickname;
                if (nickname == undefined || nickname == "") nickname = "-";

                //table_content += '<tr><td>' + position + '</td><td>' + userid_to_use + '</td><td>' + users_list[key].recordings_sent + '</td><td>' + users_list[key].sentences_reported + '</td><td>' + users_list[key].clips_validated + '</td><td>' + users_list[key].total_score + '</td></tr>';
                table_content += '<tr>' + td_to_use + position_temp + '</td><td>' + nickname + '</td><td>' + userid_to_use + '</td><td>' + users_list[key].total_score + '</td></tr>';
            }

            console.log("Total contributions: " + total_contributions);

            $("#rank").html(table_content);
        }

        function calculatePosition() {
            users_list.sort(function (a, b) {
                    //DESC b-a (ASC a-b)
                    return b.total_score - a.total_score;
                }
            );
        }*/
    </script>
</div>
<div class="background-primary-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 font-family-basic">
    <div class="center-content padding-default">
        * Registrazioni accettate o rifiutate, segnalazioni di frasi o registrazioni: 1 punti
        <br>
        Frasi registrate (e inviate): 2 punti
    </div>
</div>
<div class="background-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic"
     id="partners">
    <h1 class="no-padding no-margin h1-center text-primary-color">Partner</h1>
    <hr class="hr-center">
    <div class="display-block margin-top"></div>
    <a href="https://bit.ly/318YB07" class="just-link">
        <img src="./partners/mozillaitalia.png" class="margin-25 width50 opacity-0-8-hover-1" title="Mozilla Italia">
    </a>
    <a href="http://bit.ly/3rbyvEf" class="just-link">
        <img src="./partners/italyinformatica.png" class="margin-25 width50 opacity-0-8-hover-1"
             title="/r/ItalyInformatica">
    </a>
    <a href="http://bit.ly/3rgvVN7" class="just-link">
        <img src="./partners/pnlug.png" class="margin-25 width50 opacity-0-8-hover-1" title="LUG Pordenone">
    </a>
    <a href="http://bit.ly/395Rw4U" class="just-link">
        <img src="./partners/caffe20.png" class="margin-25 width50 opacity-0-8-hover-1" title="Caffè 2.0">
    </a>
    <a href="https://bit.ly/3bQMcnS" class="just-link">
        <img src="/old/images/projects/cv-project.png" class="margin-25 width50 opacity-0-8-hover-1" title="/r/cvp">
    </a>
    <a href="https://bit.ly/3syqWsR" class="just-link">
        <img src="./partners/ils.png" class="margin-25 height50 opacity-0-8-hover-1" title="Italian Linux Society">
    </a>
    <a href="https://bit.ly/3df84sf" class="just-link">
        <img src="./partners/marcosbox.png" class="margin-25 height50 opacity-0-8-hover-1" title="Marco's box">
    </a>
    <a href="https://bit.ly/3u5dxJ3" class="just-link">
        <img src="./partners/tuttotech.png" class="margin-25 width50 opacity-0-8-hover-1" title="TuttoTech.net">
    </a>
    <a href="https://bit.ly/3rzIHqu" class="just-link">
        <img src="./partners/puntoinformatico.jpg" class="margin-25 width50 opacity-0-8-hover-1"
             title="Punto Informatico">
    </a>
    <a href="https://bit.ly/39pyGpx" class="just-link">
        <img src="./partners/lealternative.png" class="margin-25 width50 opacity-0-8-hover-1" title="LeAlternative">
    </a>
    <a href="https://bit.ly/31BXgzg" class="just-link">
        <img src="./partners/internetgs.jpg" class="margin-25 width50 opacity-0-8-hover-1" title="Internetgs.it">
    </a>
    <a href="https://bit.ly/3wo7gKs" class="just-link">
        <img src="./partners/eticadigitale.png" class="margin-25 height50 opacity-0-8-hover-1" title="Etica Digitale">
    </a>
    <a href="https://bit.ly/3g790SQ" class="just-link">
        <img src="./partners/cuenews.jpg" class="margin-25 height50 opacity-0-8-hover-1" title="Close-up Engineering">
    </a>
    <!--
    <a href="" class="just-link">
        <button class="margin-5 font-family-basic"></button>
    </a>
    -->
</div>
<div class="background-darkgrey-color margin-left-minus-8 margin-right-minus-8 padding-bottom-50 padding-top-50 text-center">
    <a href="https://f-droid.org/it/packages/org.commonvoice.saverio/"><img src="/old/images/icons/f-droid.png"
                                                                            class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://play.google.com/store/apps/details?id=org.commonvoice.saverio"><img
                src="/old/images/icons/play-store.png"
                class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://github.com/Sav22999/common-voice-android"><img src="/old/images/socials/github.png"
                                                                    class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://crowdin.com/project/common-voice-android"><img src="/old/images/icons/crowdin.png"
                                                                    class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://t.me/sav_projects/6"><img src="/old/images/socials/telegram.png"
                                               class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://liberapay.com/Sav22999/"><img src="/old/images/icons/liberapay.png"
                                                   class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://www.paypal.me/saveriomorelli"><img src="/old/images/icons/paypal.png"
                                                        class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
    <a href="https://ko-fi.com/saveriomorelli"><img src="/old/images/icons/ko-fi.png"
                                                    class="margin-25 width50 height50 opacity-0-8-hover-1"/></a>
</div>
<div id="developed-by" class="background-black-color text-white-color font-family-basic">
    Questa app è sviluppata da <a href="/old/" class="text-lightblue-color-hover">Saverio Morelli</a>
</div>
</body>
</html>
<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->