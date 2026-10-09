function loading() {
    $("#loading").fadeOut("slow");
}

function ridimensiona() {
    grandezza = $("body").width();
    if (grandezza <= 800) {
        //mobile
        if ($(".center-content").length > 0) {
            $(".center-content").css({"width": "100%"});
        }
        if ($(".w100-mobile").length > 0) {
            $(".w100-mobile").css({"width": "100%"});
        }
        if ($(".project").length > 0) {
            $(".project").css({"display": "block", "margin-left": "auto", "margin-right": "auto", "margin-top": "0px"});
        }
        if ($(".projects-div").length > 0) {
            $(".projects-div").css({"display": "block"});
        }
        if ($(".project-50").length > 0) {
            $(".project-50").css({"width": "auto", "margin-left": "20px", "margin-right": "20px"});
        }
        if ($(".project-100").length > 0) {
            $(".project-100").css({"width": "auto", "margin-left": "20px", "margin-right": "20px"});
        }
        /*
        setTimeout(function () {
            $("#install-from-mobile ").fadeIn("slow");
        }, 3000);
        $("#developed-by").css({"height": "140px"});
        */
    } else {
        //desktop
        if ($(".center-content").length > 0) {
            $(".center-content").css({"width": "74%"});
        }
        if ($(".w100-mobile").length > 0) {
            $(".w100-mobile").css({"width": "50%"});
        }
        if ($(".project").length > 0) {
            $(".project").css({"display": "inline-block", "margin": "10px"});
        }
        if ($(".projects-div").length > 0) {
            $(".projects-div").css({"display": "flex"});
        }
        if ($(".project-50").length > 0) {
            $(".project-50").css({"width": "50%"});
        }
        if ($(".project-100").length > 0) {
            $(".project-100").css({"width": "100%"});
        }
        /*
        setTimeout(function () {
            $("#install-from-mobile ").css({"display": "none"});
        }, 3000);
        $("#developed-by").css({"height": "auto"});
        */
    }
}

$(window).resize(function () {
    ridimensiona();
});
$(window).scroll(function () {
    ridimensiona();
});

function changeVideo(id, index) {
    let url = "https://www.youtube.com/embed/" + id + "?controls=0&autoplay=0&mute=0";
    $("#video-cv-promotional").attr("src", url);
    $(".button-video-6-sections").removeClass("background-black-color");
    $(".button-video-6-sections").addClass("background-secondary-color");
    $(".button-video-6-sections").eq(index).removeClass("background-secondary-color");
    $(".button-video-6-sections").eq(index).addClass("background-black-color");
}