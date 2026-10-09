<link rel="icon" href="/old/images/icon.png"/>
<script src="/old/script/jquery.js"></script>

<style>
    #loading {
        position: fixed;
        top: 0px;
        bottom: 0px;
        left: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        display: block;
        background-color: rgb(60, 60, 60);
        background-size: 100px 100px;
        background-position: center center;
        background-repeat: no-repeat;
        background-image: url("/old/images/loading/loading.gif");
        z-index: 998;
    }
</style>

<div id="loading"></div>

<script>
    function loading() {
        $("#loading").fadeOut("slow");
    }

    $(window).load(function () {
        loading();
    });
</script>