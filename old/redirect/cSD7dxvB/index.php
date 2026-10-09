<html>
<head>
    <?php
    header('Access-Control-Allow-Origin: *');

    $title = "Redirect";
    include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/header.php");
    ?>
</head>
<body>
<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/include/menu.php");

$redirect_url = "https://www.saveriomorelli.com/...";
?>

<meta http-equiv="Refresh" content="2; url='<?php echo $redirect_url; ?>'"/>

<main>
    <div class="vertical-middle">
        <div class="horizontal-center">
            <h1 class="title-section">Redirect...</h1>
            <h2 class="subtitle-section no-bold font-small">You will be redirect to <a
                        href="<?php echo $redirect_url; ?>"><?php echo $redirect_url; ?></a> in some seconds</h2>
            <br class="big-space">
            <br>
            In case the redirect doesn't work, please click the follow button
            <br>
            <input type="button" class="button" value="Go to the page"
                   onclick="location.href='<?php echo $redirect_url; ?>'">
        </div>
    </div>
</main>

</body>
</html>

<?php
?>