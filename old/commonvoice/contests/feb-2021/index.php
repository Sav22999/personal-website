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
<div id="proudly-basilicata" class="background-gradient-primary text-white-color font-family-basic">
    <script>
        document.write(
            twemoji.parse("Developed with 🤍 in Basilicata, Italy")
        );
    </script>
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

<?php if (date("Y-m-d") > "2021-02-28") { ?>
    <div class="background-primary-color text-white-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 text-center font-family-basic font-size-20 border-radius-0">
        Il contest è ufficialmente concluso. Grazie mille a tutti coloro che hanno partecipato <span
                class="font-family-twemoji">😊</span>
    </div>
<?php } ?>

<div id="body" class="font-family-basic text-black-color">
    <div class="margin-10 font-size-20">
        <span class="font-family-twemoji">🇮🇹</span> Mozilla Italia - Contest (sperimentale)
        <br>
        Validità: 14➞28 Febbraio 2021
    </div>

    <script>
        var filter = "14➞28 Feb 2021";
        var filter_to_use = "start_date=2021-02-14&end_date=2021-02-28";

        var users_list = [
            {
                ids: ["User202102121846437050::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            },
            {
                ids: ["User202102100031227300::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            },
            {
                ids: ["User202011032154539690::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            },
            {
                ids: ["User202101181950303760::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            },
            {
                ids: ["User202102131647086480::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            },
            {
                ids: ["User202102131850585320::CVAppSav", "User202102280018031320::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            },
            {
                ids: ["User202006201027482930::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            },
            {
                ids: ["User202101061555311910::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            },
            {
                ids: ["User202102191353364020::CVAppSav"],
                total_score: 0,
                recordings_sent: 0,
                sentences_reported: 0,
                clips_validated: 0
            }
        ];

        var n_users = 0;
        var n_users_temp = 0;

        var total_contributions = 0;

        $(document).ready(function () {
            for (key in users_list) {
                n_users += users_list[key].ids.length;
            }

            for (key in users_list) {
                for (i in users_list[key].ids) {
                    load_data(users_list[key].ids[i], key);
                }
            }
        });

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
            let table_content = '<tr id="title"><th>Position</th><th>UserID</th><th>Total score*</th></tr>';

            let position = 0, position_temp = 0, last_score = 0;
            for (key in users_list) {
                position++;
                let userid_to_use = "";
                for (i in users_list[key].ids) {
                    if (userid_to_use != "") userid_to_use += "<br><a href='/commonvoice/app-usage/user/?userid=" + users_list[key].ids[i] + "&filter=cvcontest' class='text-black-color'>" + users_list[key].ids[i] + "</a>";
                    else userid_to_use = "<a href='/commonvoice/app-usage/user/?userid=" + users_list[key].ids[i] + "&filter=cvcontest' class='text-black-color'>" + users_list[key].ids[i] + "</a>";
                }
                if (position == 1 || users_list[key].total_score < last_score) {
                    last_score = users_list[key].total_score;
                    position_temp = position;
                }

                total_contributions += users_list[key].total_score;

                //table_content += '<tr><td>' + position + '</td><td>' + userid_to_use + '</td><td>' + users_list[key].recordings_sent + '</td><td>' + users_list[key].sentences_reported + '</td><td>' + users_list[key].clips_validated + '</td><td>' + users_list[key].total_score + '</td></tr>';
                table_content += '<tr><td>' + position_temp + '</td><td>' + userid_to_use + '</td><td>' + users_list[key].total_score + '</td></tr>';
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
        }
    </script>

    <div class="rank">
        <h1 class="no-padding no-margin h1-center">Classifica</h1>
        <hr class="hr-center">
        <div class="statistics-data-table">
            <table id="rank">
                <tr id="title">
                    <th>Position</th>
                    <th>UserID</th>
                    <th>Total score*</th>
                </tr>
                <tr>
                    <td>Loading ···</td>
                    <td>···</td>
                    <td>···</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="background-primary-color margin-left-minus-8 margin-right-minus-8 padding-bottom-25 padding-top-25 font-family-basic">
    <div class="center-content padding-default">
        * Registrazioni accettate o rifiutate, segnalazioni di frasi o registrazioni: 1 punti
        <br>
        Frasi registrate (e inviate): 2 punti
    </div>
</div>
<div id="developed-by" class="background-black-color text-white-color font-family-basic">
    This app is developed by <a href="/old/" class="text-lightblue-color-hover">Saverio Morelli</a>
</div>
</body>
</html>
<!---
SITE REALISED BY: SAVERIO MORELLI

> > > www.saveriomorelli.com < < <
--->