var message_cont = 0;

function insert(language) {
    if ($("#word-text").val() != "" && $("#date-text").val() != "" && $("#phonetics-text").val() != "" && $("#type-text").val() != "" && $("#definition-text").val() != "") {
        showFullscreenMessage("fullscreen-inserting-word");

        let word_json = {};
        word_json["word"] = $("#word-text").val().toString().toLowerCase();
        word_json["date"] = $("#date-text").val().toString().toLowerCase();
        word_json["phonetics"] = $("#phonetics-text").val().toString()
        word_json["type"] = $("#type-text").val().toString().toLowerCase();
        word_json["definition"] = $("#definition-text").val().toString()
        word_json["etymology"] = $("#etymology-text").val().toString()
        word_json["source"] = $("#source-text").val().toString()
        word_json["language"] = language;


        let data = JSON.stringify(word_json);

        let url = "/old/wordoftheday/new-word.php";

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            contentType: "application/json",
            dataType: "json",
            processData: false,
            success: function (response) {
                if (response != 0) {
                    if (response["status"] == "OK") {
                        showMessageBottom("Parola inserita correttamente");
                        resetFields(true);
                    } else if (response["status"] == "Error" && response["code"] == 503) {
                        showMessageBottom("Errore: Parola già presente", 10, 2);
                    } else if (response["status"] == "Error" && response["code"] == 501) {
                        showMessageBottom("Errore: Una parola è già presente per questa data", 10, 2);
                    } else {
                        showMessageBottom("Errore: la parola non è stata inserita<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                    }
                    hideFullscreenMessages();
                } else {
                    showMessageBottom('A causa di un problema inaspettato la parola non è stata inserita. Riprovare.', -1, 2);
                    hideFullscreenMessages();
                }
            },
            error: function (response) {
                showMessageBottom("Errore inaspettato. Riprovare.<br>## " + JSON.stringify(word_json) + "<br>:: " + JSON.stringify(response), -1, 2);
                hideFullscreenMessages();
            }
        });
    } else {
        showMessageBottom("Errore: compila tutti i campi", 10, 2);
    }
}

function insertMultiple(lang) {
    try {
        let json_to_insert = JSON.parse($("#json-text").val());
        for (let item in json_to_insert) {
            let json = json_to_insert[item];
            insertWord(lang.toString(), json["word"], json["phonetics"], json["type"], json["definition"], json["etymology"], json["source"]);
        }
        showMessageBottom(`Inserimento multiplo iniziato`, 5, 1);
    } catch (e) {
        showMessageBottom(`JSON errato: ${e.toString()}`, -1, 2);
    }
}

function fixDate(id, date, lang) {
    //console.log(id, lang)

    let json_to_send = {};
    json_to_send["id"] = id;
    json_to_send["language"] = lang;


    let data = JSON.stringify(json_to_send);

    let url = "/old/wordoftheday/fix-date.php";

    $.ajax({
        url: url,
        type: 'post',
        data: data,
        contentType: "application/json",
        dataType: "json",
        processData: false,
        success: function (response) {
            if (response != 0) {
                if (response["status"] == "OK") {
                    showMessageBottom(`Parola aggiornata correttamente`, 5, 1);
                    let n_elements = document.getElementsByClassName(`word-duplicated-date-${date}`).length;
                    if (n_elements === 2) {
                        for (let i = 0; i < n_elements; i++) {
                            document.getElementsByClassName(`word-duplicated-date-${date}`)[0].remove();
                        }
                    } else if (n_elements > 2) {
                        document.getElementById(`word-duplicated-id-${id}`).remove();
                    }
                } else {
                    showMessageBottom("Errore<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                }
            } else {
                showMessageBottom('A causa di un problema inaspettato la parola non è stata inserita. Riprovare.', -1, 2);
            }
        },
        error: function (response) {
            console.error(response);
            showMessageBottom("Errore inaspettato. Riprovare.<br>" + JSON.stringify(json_to_send) + "<br>", -1, 2);
        }
    });
}

function insertWord(language, word, phonetics, type, definition, etymology, source) {
    showFullscreenMessage("fullscreen-inserting-word");

    let word_json = {};
    word_json["word"] = word.toString().toLowerCase();
    //word_json["date"] = phonetics.toString().toLowerCase();
    word_json["phonetics"] = phonetics.toString()
    word_json["type"] = type.toString().toLowerCase();
    word_json["definition"] = definition.toString()
    word_json["etymology"] = etymology.toString()
    word_json["source"] = source.toString()
    word_json["language"] = language.toString();


    let data = JSON.stringify(word_json);

    let url = "/old/wordoftheday/new-word-multiple.php";

    $.ajax({
        url: url,
        type: 'post',
        data: data,
        contentType: "application/json",
        dataType: "json",
        processData: false,
        success: function (response) {
            if (response !== 0) {
                if (response["status"] === "OK") {
                    showMessageBottom(`Parola <b>${word_json["word"]}</b> inserita correttamente`, 5, 1);
                    //resetFields(true);
                } else {
                    showMessageBottom("Errore: la parola non è stata inserita<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                }
                hideFullscreenMessages();
            } else {
                showMessageBottom('A causa di un problema inaspettato la parola non è stata inserita. Riprovare.', -1, 2);
                hideFullscreenMessages();
            }
        },
        error: function (response) {
            if (response.status === 503) {
                showMessageBottom(`Errore: Parola <b>${word_json["word"]}</b> già presente`, 5, 2);
            } else {
                let responseDetails = (JSON.parse(response.responseText)).description;
                showMessageBottom(`Errore inaspettato. (${response.status}) – "${responseDetails}" – Riprovare.<br>${JSON.stringify(word_json)}`, -1, 2);
            }
            hideFullscreenMessages();
        }
    });
}

function resetFields(incrementDate = false) {
    $("#element-to-hide").val("");
    if (incrementDate) {
        let date = incrementedDate($("#date-text").val());
        $("#date-text").val(date);
        loadNewWord(getFullDate(date));
    } else $("#date-text").val("");
    $("#word-text").val("");
    $("#type-text").val("");
    $("#phonetics-text").val("");
    $("#definition-text").val("");
    $("#etymology-text").val("");
    $("#source-text").val("Wiktionary");
}

function loadNewWord(full_date) {
    let year = 0;
    let month = 0;
    let day = 0;
    let monthToUse = "";
    if ($.isNumeric(full_date.toString().split("/")[0]) && $.isNumeric(full_date.toString().split("/")[1].split("_")[1])) {
        year = full_date.toString().split("/")[0];
        monthToUse = full_date.toString().split("/")[1].split("_")[0];
        day = full_date.toString().split("/")[1].split("_")[1];
    } else {
        year = full_date.getFullYear();
        month = (full_date.getMonth() + 1);
        day = full_date.getDate();

        switch (month) {
            case 1:
                monthToUse = "January";
                break;
            case 2:
                monthToUse = "February";
                break;
            case 3:
                monthToUse = "March";
                break;
            case 4:
                monthToUse = "April";
                break;
            case 5:
                monthToUse = "May";
                break;
            case 6:
                monthToUse = "June";
                break;
            case 7:
                monthToUse = "July";
                break;
            case 8:
                monthToUse = "August";
                break;
            case 9:
                monthToUse = "September";
                break;
            case 10:
                monthToUse = "October";
                break;
            case 11:
                monthToUse = "November";
                break;
            case 12:
                monthToUse = "December";
                break;
        }
    }
    let dateToUse = year + "/" + monthToUse + "_" + day;
    let url = "https://en.wiktionary.org/wiki/Wiktionary:Word_of_the_day/" + dateToUse;

    $("#element-to-hide").css({"display": "block"});
    $("#element-to-hide").html(dateToUse + " |  <a target='_blank' href='" + url + "'>See the new WOTD on Wiktionary</a>");

    if ($("#word-text").val() != "") {
        document.getElementsByClassName("last-words")[0].innerHTML = document.getElementsByClassName("last-words")[1].innerHTML;
        document.getElementsByClassName("last-words")[1].innerHTML = document.getElementsByClassName("last-words")[2].innerHTML;
        document.getElementsByClassName("last-words")[2].innerHTML = $("#word-text").val();
    }
}

function incrementedDate(oldDatePassed) {
    let oldDate = new Date(oldDatePassed);
    let newDateTemp = oldDate;
    newDateTemp.setDate(newDateTemp.getDate() + 1)
    let newDate = getFormattedDate(newDateTemp, oldDate);
    return newDate;
}

function getFormattedDate(newDateTempPassed, oldDatePassed) {
    let oldDate = new Date(oldDatePassed);
    let newDateTemp = new Date(newDateTempPassed);
    let day = newDateTemp.getDate();
    if (day < 10) {
        day = "0" + day;
    }
    let month = (oldDate.getMonth() + 1);
    if (month < 10) {
        month = "0" + month;
    }
    let year = oldDate.getFullYear();
    let date = year + "-" + month + "-" + day;
    return date;
}

function getFullDate(shortDate) {
    let dateToReturn = "";
    let date = new Date(shortDate);

    let year = date.getFullYear();
    let month = (date.getMonth() + 1);
    switch (month) {
        case 1:
            month = "January";
            break;
        case 2:
            month = "February";
            break;
        case 3:
            month = "March";
            break;
        case 4:
            month = "April";
            break;
        case 5:
            month = "May";
            break;
        case 6:
            month = "June";
            break;
        case 7:
            month = "July";
            break;
        case 8:
            month = "August";
            break;
        case 9:
            month = "September";
            break;
        case 10:
            month = "October";
            break;
        case 11:
            month = "November";
            break;
        case 12:
            month = "December";
            break;
    }
    let day = date.getDate();
    dateToReturn = year + "/" + month + "_" + day;
    return dateToReturn;
}

function showMessageBottom(text, seconds = 10, type = 1) {
    //you can pass also "seconds=-1": the message won't be hide automatically
    if (text != "") {
        let milliseconds = seconds * 1000;//to milliseconds
        let classToUse = "";
        let id_to_use = "message-bottom-" + message_cont;

        classToUse += "" + "message-bottom";
        if (type == 2) {
            classToUse += " message-warning";
        }
        let new_message = document.createElement("div");
        new_message.className = classToUse;
        new_message.id = id_to_use;
        new_message.innerHTML = text;
        $("body").append(new_message);
        let new_close_button = document.createElement("button");
        new_close_button.className = "close-message-button button no-box-shadow";
        new_close_button.innerHTML = "";
        new_close_button.onclick = function () {
            hideMessageBottom(id_to_use);
        };
        $("#" + id_to_use).append(new_close_button);
        $("#" + id_to_use).fadeIn();
        if (seconds > 0) {
            setTimeout(function () {
                hideMessageBottom(id_to_use);
            }, milliseconds);
        }

        message_cont++;
    }
}

function hideMessageBottom(id_to_use) {
    if ($("#" + id_to_use).length) {
        /*$("#" + id_to_use).fadeOut();
        setTimeout(function () {
            $("#" + id_to_use).remove();
        }, 1000);*/
        $("#" + id_to_use).remove();
    }
}

function showFullscreenMessage(id, change = true) {
    $("#" + id).fadeIn("fast");
}

function hideFullscreenMessages() {
    $(".fullscreen-message").fadeOut("fast");
}