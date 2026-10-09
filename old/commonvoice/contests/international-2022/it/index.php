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
    <script src="/old/commonvoice/contests/international-2022/get-userids.js"></script>

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
    <a href="/old/commonvoice">
        <div id="main-title">
            CV Project
        </div>
    </a>
</div>
<div class="margin-top100"></div>
<div id="proudly-basilicata" class="background-primary-color text-white-color font-family-basic">
    Sviluppata con il <span class="font-family-twemoji">🤍</span> in Basilicata, Italia
</div>
<div class="background-white-color margin-left-minus-8 margin-right-minus-8 padding-top-25 text-center">
    <a href="../it/" class="just-link">
        <button class="margin-5 font-family-basic">Italiano</button>
    </a>
</div>
<div class="background-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center">
    <a href="" class="just-link">
        <button class="margin-5 font-family-basic">Vedi l'evento</button>
    </a>
    <a href="./rules/" class="just-link">
        <button class="margin-5 font-family-basic">Leggi il regolamento</button>
    </a>
</div>

<div class="background-secondary-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center">
    <a href="./join/" class="just-link">
        <button class="margin-5 font-family-basic">Partecipa!</button>
    </a>
</div>

<?php if (date("Y-m-d") > "2022-09-30") { ?>
    <div class="background-primary-color text-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic font-size-20 border-radius-0">
        Il contest è ufficialmente concluso. Grazie mille a tutti coloro che hanno partecipato <span
                class="font-family-twemoji">😊</span>
    </div>
<?php } ?>

<div id="body" class="font-family-basic text-black-color">
    <div class="margin-10 font-size-20">
        <h1 class="h1-center">1st International Contest</h1>
        Validità: 1➞30 settembre 2022
    </div>

    <div class="rank">
        <h1 class="no-padding no-margin h1-center">Classifica</h1>
        <hr class="hr-center">
        <div class="statistics-data-table" id="div-rank">
            <div style="padding: 30px;">
                Caricamento in corso, potrebbe volerci fino a qualche minuto.
            </div>
        </div>
    </div>

    <script>
        var filter = "1➞30 September 2022";

        const start_year = "2022";
        const start_month = "09";
        const start_day = "01";
        const end_year = "2022";
        const end_month = "09";
        const end_day = "30";

        var filter_to_use = "start_date=" + start_year + "-" + start_month + "-" + start_day + "&end_date=" + end_year + "-" + end_month + "-" + end_day;

        const awarded_participants = 20;
        var total_contributions = 0;

        var users_list = [];

        let force_show_rank = <?php if (isset($_GET["show-rank"])) echo "true"; else echo "false"; ?>;

        let today = new Date();
        var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();

        start_date = new Date();
        start_date.setFullYear(parseInt(start_year), parseInt(start_month) - 1, parseInt(start_day));
        start_date.setHours(0, 0, 0, 0);

        end_date = new Date();
        end_date.setFullYear(parseInt(end_year), parseInt(end_month) - 1, parseInt(end_day));
        end_date.setHours(23, 59, 59, 999);

        if ((force_show_rank && today.getTime() > end_date.getTime()) || today.getTime() >= start_date && today.getTime() <= end_date) {
            users_list = getUserIdsParticipants();
            if (users_list.length !== 0) {
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
                        $(".statistics-data-table").html("Non c'è ancora alcun partecipante. Sii il primo!");
                        $(".statistics-data-table").css({"padding": "30px"});
                    }
                });
            } else {
                $(".statistics-data-table").html("Non c'è ancora alcun partecipante. Sii il primo!");
                $(".statistics-data-table").css({"padding": "30px"});
            }
        } else if (today.getTime() < start_date.getTime()) {
            $(".statistics-data-table").html("Il contest non è ancora iniziato.");
            $(".statistics-data-table").css({"padding": "30px"});
        } else if (today.getTime() > end_date.getTime()) {
            $(".statistics-data-table").html("Il contest è già finito. Rimani aggiornato su futuri eventi sul nostro canale Telegram!<br><a href='./?show-rank' class='just-link'><button>Mostra la classifica</button></a>");
            $(".statistics-data-table").css({"padding": "30px"});
        } else {
            $(".statistics-data-table").html("C'è stato un problema durante il controllo delle date.");
            $(".statistics-data-table").css({"padding": "30px"});
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

                    if (n_users_temp === n_users) {
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
            let table_content = '<table id="rank"><tr id="title"><th>Posizione</th><th>Nickname</th><th>UserID</th><th>Punteggio totale*</th></tr>';

            let position = 0, position_temp = 0, last_score = 0;
            for (key in users_list) {
                if (!users_list[key].skip_rank) position++;
                let userid_to_use = "";

                let td_to_use = "<td>";

                for (i in users_list[key].ids) {
                    if (userid_to_use !== "") userid_to_use += "<br><a href='/commonvoice/app-usage/user?userid=" + users_list[key].ids[i] + "&filter=international-2022' class='text-black-color'>" + users_list[key].ids[i] + "</a>";
                    else userid_to_use = "<a href='/commonvoice/app-usage/user?userid=" + users_list[key].ids[i] + "&filter=international-2022' class='text-black-color'>" + users_list[key].ids[i] + "</a>";
                }
                if (!users_list[key].skip_rank) {
                    if (position === 1 || users_list[key].total_score < last_score) {
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
                if (nickname === undefined || nickname === "") nickname = "-";

                //table_content += '<tr><td>' + position + '</td><td>' + userid_to_use + '</td><td>' + users_list[key].recordings_sent + '</td><td>' + users_list[key].sentences_reported + '</td><td>' + users_list[key].clips_validated + '</td><td>' + users_list[key].total_score + '</td></tr>';
                table_content += '<tr>' + td_to_use + position_temp + '</td><td>' + nickname + '</td><td>' + userid_to_use + '</td><td>' + users_list[key].total_score + '</td></tr>';
            }

            table_content += "</table>";

            console.log("Total contributions: " + total_contributions);

            $("#div-rank").html(table_content);
        }

        function calculatePosition() {
            users_list.sort(function (a, b) {
                    //DESC b-a (ASC a-b)
                    return b.total_score - a.total_score;
                }
            );
        }
    </script>
</div>
<div class="background-primary-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 font-family-basic">
    <div class="center-content padding-default">
        * Registrazioni accettate o rifiutate, frasi o registrazioni segnalate: 1 punti
        <br>
        Frasi registrate (e inviate): 2 punti
    </div>
</div>
<div class="background-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic"
     id="partners">
    <h1 class="no-padding no-margin h1-center text-primary-color">Partner</h1>
    <hr class="hr-center">
    <div class="display-block margin-top"></div>
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