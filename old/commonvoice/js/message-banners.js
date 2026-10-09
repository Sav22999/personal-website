var message_cont = 0;

function insert_message() {
    if ($("#text-text").val() != "") {
        showFullscreenMessage("fullscreen-inserting-message");

        let message_json = {};
        message_json["type"] = $("#type-text").val().toString()
        message_json["user"] = $("#user-text").val().toString()
        message_json["source"] = $("#source-text").val().toString()
        message_json["versionCode"] = $("#version-code-text").val().toString()
        message_json["language"] = $("#language-text").val().toString()
        message_json["startDate"] = $("#date-start-text").val().toString()
        message_json["endDate"] = $("#date-end-text").val().toString()
        message_json["text"] = $("#text-text").val().toString()
        message_json["ableToClose"] = $("#able-to-close-text").val().toString()
        message_json["button1"] = $("#button1-text").val().toString()
        message_json["button1Link"] = $("#button1-link-text").val().toString()
        message_json["button2"] = $("#button2-text").val().toString()
        message_json["button2Link"] = $("#button2-link-text").val().toString()

        //TODO remove after tests
        if ($("#get-auth").val() != "") message_json["auth"] = $("#get-auth").val().toString();


        let data = JSON.stringify(message_json);

        let url = "/old/api/common-voice-android/v2/messages/new/";

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
                        showMessageBottom("Messaggio inserito correttamente.");
                        resetFields();
                    } else {
                        showMessageBottom("Errore: il messaggio non è stato inserito<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                    }
                    hideFullscreenMessages();
                } else {
                    showMessageBottom('A causa di un problema inaspettato il messaggio non è stato inserito. Riprovare.', -1, 2);
                    hideFullscreenMessages();
                }
            },
            error: function (response) {
                showMessageBottom("Errore inaspettato. Riprovare.<br>" + JSON.stringify(message_json) + "<br>" + JSON.stringify(response), -1, 2);
                hideFullscreenMessages();
            }
        });
    } else {
        showMessageBottom("Errore: compila tutti i campi", 10, 2);
    }
}

function resetFields() {
    $("#type-text").val("");
    $("#user-text").val("");
    $("#source-text").val("");
    $("#version-code-text").val("");
    $("#language-text").val("");
    $("#date-start-text").val("");
    $("#date-end-text").val("");
    $("#text-text").val("");
    $("#able-to-close-text").val("true");
    $("#button1-text").val("");
    $("#button1-link-text").val("");
    $("#button2-text").val("");
    $("#button2-link-text").val("");
}

function incrementedDate(oldDatePassed) {
    let oldDate = new Date(oldDatePassed);
    let newDateTemp = oldDate;
    newDateTemp.setDate(newDateTemp.getDate() + 1)
    let day = newDateTemp.getDate()
    if (day < 10) {
        day = "0" + day;
    }
    let month = (oldDate.getMonth() + 1);
    if (month < 10) {
        month = "0" + month;
    }
    let year = oldDate.getFullYear();
    let newDate = year + "-" + month + "-" + day;
    return newDate;
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