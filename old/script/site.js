$(window).load(function () {
    check_mode();
    loaded();
});
$(window).resize(function () {
    check_mode();
});
$(window).scroll(function () {
    check_mode();
});

function loaded() {
    $("#loading").fadeOut();
}

function isMobile() {
    if ($("#menu").length) {
        // not home page
        var size = $("#menu").width();
        if (size <= 1200) return true;
    } else if ($("#home-header").length) {
        // home page
        var size = $("#home-header").width();
        if (size <= 1200) return true;
    }
    return false;
}

function check_mode() {
    if ($("#menu").length) {
        // not home page
        var size = $("#menu").width();
        if (size <= 700) set_menu("mobile"); else if (size <= 1200) set_menu("tablet"); else set_menu("desktop");
        check_position();
    } else if ($("#home-header").length) {
        // home page
        var size = $("#home-header").width();
        if (size <= 700) set_home("mobile"); else if (size <= 1200) set_home("tablet"); else set_home("desktop");
    }
}

function check_position() {
    var position = $(window).scrollTop();
    if (position <= 30) {
        if (is_menu_mobile() && !is_menu_mobile_opened()) {
            $("#menu").css({"background-color": "rgba(0,0,0,0)"});
        }
    } else {
        if (is_menu_mobile() && !is_menu_mobile_opened()) {
            $("#menu").css({"background-color": "rgba(0,0,0,0.9)"});
        }
    }

    if (position > 200) {
        $("#back-to-top").fadeIn();
    } else {
        $("#back-to-top").fadeOut();
    }
}

function set_menu(mode) {
    let height_header_not_home = ($("#header_not_home").height() + 40) + "px";
    if (mode == "mobile") {
        //mobile
        $("main").css({"width": "100%", "margin-top": height_header_not_home});

        if (is_menu_mobile() && !is_menu_mobile_opened()) {
            $("#cont-menu").css({
                "min-height": "130px", "max-height": "130px"
            });
            $("#menu").css({"height": "auto"});
            $(".item-menu").css({"display": "none"});
        }
        $("#show-mobile-menu").css({"display": "block"});
        $("aside").css({"width": "0%", "display": "none"});
        $("section").css({"width": "100%"});

        $("#cont-menu").css({"width": "100%"});
        $("#title-menu").css({"width": "100%", "text-align": "center", "position": "relative", "font-size": "50px"});
        //$("#items-menu").css({"width": "0%", "display": "none"});
        $("#items-menu").css({
            "width": "100%", "display": "block", "text-align": "center", "position": "relative", "left": "0%"
        });
        $("#menu").css({"position": "fixed"});
        $(".center-content").css({"width": "100%"});
        $(".img-float-right").css({
            "float": "none", "margin-left": "20px", "margin-right": "20px", "margin-bottom": "20px", "width": "auto"
        });
        $(".img-float-left").css({
            "float": "none", "margin-left": "20px", "margin-right": "20px", "margin-bottom": "20px", "width": "auto"
        });
        $(".hr-right").css({"margin-left": "10px"});
        $(".project").css({"display": "block", "margin-left": "auto", "margin-right": "auto", "margin-top": "0px"});
        $(".projects-div").css({"display": "block"});
        $(".project-30, .project-50").css({"width": "auto", "margin-left": "20px", "margin-right": "20px"});
    } else if (mode == "tablet") {
        //tablet
        set_menu("mobile")
    } else if (mode == "desktop") {
        //desktop
        $("main").css({"width": "74%", "margin-top": height_header_not_home});
        $("#menu").css({"position": "absolute", "height": "auto", "background-color": "transparent"});
        $("#cont-menu").css({"min-height": "40px", "max-height": "40px"});
        $(".item-menu").css({
            "display": "inline-block",
            "font-size": "18px",
            "padding": "2px",
            "border-bottom": "0px solid transparent",
            "height": "20px",
            "margin-left": "10px"
        });
        $("#show-mobile-menu").css({"display": "none"});
        $("aside").css({"width": "30%", "display": "block"});
        $("section").css({"width": "70%"});

        $("#cont-menu").css({"width": "70%"});
        $("#title-menu").css({"width": "30%", "text-align": "left", "position": "absolute", "font-size": "30px"});
        $("#items-menu").css({
            "width": "70%", "display": "inline-block", "text-align": "right", "position": "absolute", "left": "30%"
        });
        $("#search-mobile").css({"display": "none"});
        $(".center-content").css({"width": "74%"});
        $(".img-float-right").css({
            "float": "right", "margin-left": "20px", "margin-right": "20px", "margin-bottom": "0px", "width": "400px"
        });
        $(".img-float-left").css({
            "float": "left", "margin-left": "20px", "margin-right": "20px", "margin-bottom": "0px", "width": "400px"
        });
        $(".hr-right").css({"margin-left": "0px"});
        $(".project").css({"display": "inline-block", "margin": "10px"});
        $(".projects-div").css({"display": "flex"});
        $(".project-30").css({"width": "33%"});
        $(".project-50").css({"width": "50%"});
    }
}

function set_home(mode) {
    if (mode == "mobile") {
        //mobile
        $("#home-header-image").css({"float": "none", "width": "300px", "height": "300px", "margin-bottom": "20px"});
        $("#home-header-middle-text").css({"text-align": "center", "margin-left": "0px", "width": "100%"});
        $("#home-header-middle").css({"width": "100%"});
        $(".home-header-bottom-item").css({"font-size": "30px"});
    } else if (mode == "tablet") {
        //tablet
        set_home("mobile");
    } else if (mode == "desktop") {
        //desktop
        $("#home-header-image").css({"float": "left", "width": "200px", "height": "200px", "margin-bottom": "0px"});
        $("#home-header-middle-text").css({"text-align": "center", "margin-left": "20px", "width": "auto"});
        $("#home-header-middle").css({"width": "690px"});
        $(".home-header-bottom-item").css({"font-size": "18px"});
    }
}

function open_close_mobile_menu(mode) {
    if ($("#menu").css("height") != "130px" || mode == "close") {
        //close menu
        $("#cont-menu").css({
            "min-height": "130px", "max-height": "130px"
        });
        $("#menu").css({"height": "auto"});
        $(".item-menu").css({"display": "none", "margin-left": "10px"});
        $("#search-mobile").css({"display": "none"});

        check_position();
    } else if ($("#menu").css("height") == "130px" || mode == "open") {
        //open menu
        $("#cont-menu").css({"height": "100%"});
        $("#menu").css({"height": "100%", "background-color": "rgba(0,0,0,1)"});
        $(".item-menu").css({
            "display": "block",
            "margin-left": "0px",
            "font-size": "24px",
            "padding": "20px",
            "border-bottom": "2px solid #222222",
            "height": "auto"
        });
        $("#search-mobile").css({"display": "block"});
    }
}

function is_menu_mobile_opened() {
    //true -> opened, false -> closed
    var height = $("#menu").css("height").replace("px", "");
    return (height > 130);
}

function is_menu_mobile() {
    //true -> menu mobile/tablet, false -> menu desktop
    var width = $("#menu").css("width").replace("px", "");
    return (width <= 1200);
}

function apri_black_background() {
    $("#black_background").fadeIn("fast");
}

function chiudi_black_background() {
    $("#black_background").fadeOut("slow");
}

function open_edit_date() {
    apri_black_background();
    $("#div_edit_date").fadeIn("slow");
}

function close_edit_date() {
    chiudi_black_background();
    $("#div_edit_date").fadeOut("slow");
}

function open_edit_title() {
    apri_black_background();
    $("#div_edit_title").fadeIn("slow");
}

function close_edit_title() {
    chiudi_black_background();
    $("#div_edit_title").fadeOut("slow");
}

function open_edit_image() {
    set_preview_div_edit_image();
    $("#div_edit_image").fadeIn("slow");
    apri_black_background();
}

function close_edit_image() {
    chiudi_black_background();
    $("#div_edit_image").fadeOut("slow");
}

function set_preview_div_edit_image() {
    document.getElementById("img_preview_div_edit_image").style.backgroundImage = "url(), url(/img/image_transparency.png)";
    var url = document.getElementById("img_copertina_div_edit_image").value;
    if (url != "no") {
        if (!url.includes("https://www.")) url = url.replace("https://", "https://www.");
        $.get(url)
            .done(function () {
                document.getElementById("img_preview_div_edit_image").style.backgroundImage = "url(" + url + "), url(/img/image_transparency.png)";
            }).fail(function () {
            // not exists code
            document.getElementById("img_preview_div_edit_image").style.backgroundImage = "url(/img/image_error.jpg), url(/img/image_transparency.png)";
        })
    }
}

function back_to_top() {
    scrollTo(0, 0);
}

function generate_path(value) {
    var value_to_set = value;
    value_to_set = value_to_set.toLowerCase();
    value_to_set = replaceAll(value_to_set, /[?!:.;,\\/'"*\]\[^%$£€&*#@_|=)(}{~ç§<>°+]/, " ");
    value_to_set = replaceAll(value_to_set, /[èéÈÉ]/, "e");
    value_to_set = replaceAll(value_to_set, /[àÀ]/, "a");
    value_to_set = replaceAll(value_to_set, /[ìÌ]/, "i");
    value_to_set = replaceAll(value_to_set, /[òÒ]/, "o");
    value_to_set = replaceAll(value_to_set, /[ùÙ]/, "u");
    value_to_set = replaceAll(value_to_set, " ", "-");
    value_to_set = replaceAll(value_to_set, /[\-]+[\-]/, "-");
    last_char_position = value_to_set.length - 1;
    if (last_char_position > 2 && value_to_set.substr(last_char_position, 1) == "-") {
        //if the last char is "-" it replace with ""
        value_to_set = value_to_set.substr(0, last_char_position);
    }
    $("#admin-path-article").val(value_to_set);
    $("#admin-image-url").val("/old/images/articles/" + value_to_set + "/1.png");
}

function replaceAll(string, search, replace) {
    return string.split(search).join(replace);
}

function show_hide_image() {
    if ($("#admin-image-preview-url").css("display") == "block") {
        $("#admin-image-preview-url").css("display", "none");
        $("#admin-image-show-hide").css("background-image", "url('/images/icons/show.png')");
    } else {
        if ($("#admin-image-url").val() != "") {
            $("#admin-image-preview-url").css("display", "block");
            $("#admin-image-show-hide").css("background-image", "url('/images/icons/hide.png')");
            update_show_image_preview();
        }
    }
}

function update_show_image_preview() {
    var url_image = $("#admin-image-url").val();
    $("#admin-image-preview-url").css({"background-image": "url('" + url_image + "'), url('/images/backgrounds/transparency.png')"})
}

function yes_no_working(value) {
    var xmlhttp = new XMLHttpRequest;
    var post = new FormData();
    post.append('value', value);
    xmlhttp.open("POST", "/old/include/request_yes_no_working.php", true);
    xmlhttp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText == "true") {
                if (value == 1) {
                    $("#yes-working").addClass("admin-yes-not-selected");
                    $("#no-working").removeClass("admin-yes-not-selected");
                } else {
                    $("#yes-working").removeClass("admin-yes-not-selected");
                    $("#no-working").addClass("admin-yes-not-selected");
                }
            } else {
                alert("Error: " + this.responseText);
            }
        } else {
            //not working
        }
    };
    xmlhttp.send(post);
}

function change_status(value) {
    $("#status-0").removeClass("admin-update-article-status-selected");
    $("#status-1").removeClass("admin-update-article-status-selected");
    $("#status-2").removeClass("admin-update-article-status-selected");
    $("#status-3").removeClass("admin-update-article-status-selected");
    $("#status-" + value).addClass("admin-update-article-status-selected");
    document.getElementById("status0").checked = false
    document.getElementById("status1").checked = false;
    document.getElementById("status2").checked = false;
    document.getElementById("status3").checked = false;
    document.getElementById("status" + value).checked = true;
}

function mark_as_closed(ticket, email) {
    mark_as("close", ticket, email);
}

function mark_as_reopened(ticket, email) {
    mark_as("open", ticket, email);
}

function mark_as_deleted(ticket, email) {
    mark_as("delete", ticket, email);
}

function mark_as(type, ticket, email) {
    var status = 0;
    var display_block_none = "block";
    if (type == "open") {
        status = 2;
    } else if (type == "close") {
        status = 4;
    } else if (type == "delete") {
        status = 3;
    }

    var xmlhttp = new XMLHttpRequest;
    var post = new FormData();
    post.append('status', status);
    post.append('ticket', ticket);
    post.append('to_email', email);
    xmlhttp.open("POST", "/old/include/request_open_close_conversation.php", true);
    xmlhttp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText == "true") {
                if (status == 4) {
                    $("#button-open-conversation").css("display", "inline-block");
                    $("#button-close-conversation").css("display", "none");
                    $(".div-form-to-reply").css("display", "none");
                    $(".reply-to-hide").css("display", "none");
                    $(".text-status-conversation").html("Conversazione conclusa");
                } else if (status == 2) {
                    $("#button-open-conversation").css("display", "none");
                    $("#button-close-conversation").css("display", "inline-block");
                    $(".div-form-to-reply").css("display", "block");
                    $(".reply-to-hide").css("display", "none");
                    $(".text-status-conversation").html("Conversazione riaperta");
                } else if (status == 3) {
                    $("#button-open-conversation").css("display", "none");
                    $("#button-delete-conversation").css("display", "none");
                    $("#button-close-conversation").css("display", "none");
                    $(".div-form-to-reply").css("display", "none");
                    $(".reply-to-hide").css("display", "none");
                    $(".text-status-conversation").html("Conversazione eliminata");
                    $(".ticket-status").css("background-color", "red");
                }
            } else if (this.responseText == "deleted") {
                $("#button-open-conversation").css("display", "none");
                $("#button-delete-conversation").css("display", "none");
                $("#button-close-conversation").css("display", "none");
                $(".div-form-to-reply").css("display", "none");
                $(".reply-to-hide").css("display", "none");
                $(".text-status-conversation").html("Conversazione eliminata");
                $(".ticket-status").css("background-color", "red");
            } else {
                //alert("Error:\n" + this.responseText);
            }
        } else {
            //not working
        }
    };
    xmlhttp.send(post);
}

var night_mode = false

function toggle_night_mode() {
    if (night_mode) {
        $("body").css("background-color", "#EEEEEE");
        $(".div-aside").css({"background-color": "#FFFFFF", "color": "#222222"});
        $("article").css({"background-color": "#FFFFFF", "color": "#222222"});
        night_mode = false;
    } else {
        $("body").css("background-color", "#222222");
        $(".div-aside").css({"background-color": "#000000", "color": "#eeeeee"});
        $("article").css({"background-color": "#000000", "color": "#eeeeee"});
        night_mode = true;
    }
}