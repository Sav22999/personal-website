function loading() {
    $("#loading").fadeOut("slow");
}

function ridimensiona() {
    grandezza = $("body").width();
    if (grandezza <= 700) {
        $("#body").css({"margin-left": "0%", "margin-right": "0%"});
    } else {
        $("#body").css({"margin-left": "15%", "margin-right": "15%"});
    }
}

$(window).resize(function () {
    ridimensiona();
});
$(window).scroll(function () {
    ridimensiona();
});