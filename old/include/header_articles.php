<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153389726-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-153389726-1');
</script>


<script data-ad-client="ca-pub-4441008333572114" async
        src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

<?php
global $localhost_db, $username_db, $password_db, $database_db, $article_id;
if (!variables_permission_yes_or_not(1) && $article_id != 0) {
    $c_add_view = new mysqli($localhost_db, $username_db, $password_db, $database_db);
    $c_add_view->set_charset("utf8");
    $sql_view = "INSERT INTO views(`id`, `article`, `date`, `ip`) VALUES(NULL, '" . $article_id . "', '" . date("Y-m-d H:i:s") . "', '" . get_user_ip() . "')";
    $c_add_view->query($sql_view);
    $c_add_view->close();
}
?>

<script>
    var images_number = new Array();
    var current_slide = new Array();
    var path_slider = new Array();
    var extension_slider = new Array();

    function initialise_slider(slider, number, path, extension) {
        images_number = [number].concat(images_number);
        path_slider = [path].concat(path_slider);
        extension_slider = [extension].concat(extension_slider);
        current_slide = [1].concat(current_slide);
        set_slider_by_parameters(number, path, extension, 1, slider);
    }

    function set_slider_by_parameters(images_number, path_slider, extension_slider, current_slide, slider) {
        var url_img = path_slider + current_slide + "." + extension_slider;
        $(".slider-" + slider).attr("src", url_img);
        $(".slider-" + slider).css("display", "none");
        $(".slider-loading-" + slider).css({
            "background-color": "#222222",
            "background-image": "url('/old/images/loading/loading.gif')"
        });
        $(".slider-" + slider).load(function () {
            $(".slider-" + slider).css("display", "block");
            $(".slider-loading-" + slider).css({"background-color": "#222222", "background-image": "none"});
        });
        if (current_slide == 1) {
            $(".back-arrow-" + slider).css("display", "none");
        } else {
            $(".back-arrow-" + slider).css("display", "block");
        }
        if (current_slide == images_number) {
            $(".forward-arrow-" + slider).css("display", "none");
        } else {
            $(".forward-arrow-" + slider).css("display", "block");
        }
    }

    function set_slider(slider) {
        set_slider_by_parameters(images_number[slider], path_slider[slider], extension_slider[slider], current_slide[slider], slider)
    }

    function back_slider(slider) {
        if (current_slide[slider] > 1) {
            current_slide[slider]--;
            set_slider(slider);
        }
    }

    function forward_slider(slider) {
        if (current_slide[slider] < images_number[slider]) {
            current_slide[slider]++;
            set_slider(slider);
        }
    }
</script>
