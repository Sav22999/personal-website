<meta http-equiv="content-type" content="text/html; charset=UTF-16">
<meta name="viewport" content="width=device-width, initial-scale=0.8">

<nav class="top-menu">
    <div class="div-logo">
        <h1 class="h1-logo">Easy Recipes</h1>
    </div>
    <div class="div-account clearfix">
        <?php if (check_authorisation(2) && isset($_SESSION["username"])) { ?>
            <h1 class="h1-notifications">
                <span class="notifications-counter"></span>
            </h1>
            <h1 class="h1-account">
                <span id="user-profile-image"
                      style="background-image:url('https://www.gravatar.com/avatar/<?php echo $_SESSION["user_email"]; ?>?s=500&r=g')"></span>
                <?php
                //echo $_SESSION["username"];
                ?>
            </h1>
            <?php
        } else {
            ?>
            <h1 class="h1-account h1-account-not-logged">
                Accedi
            </h1>
            <?php
        }
        ?>
    </div>
</nav>
<nav class="account-menu" id="account-popup">
    <?php
    global $path_easyrepices;
    include($path_easyrepices . "/include/login-form.php");
    ?>
</nav>
<nav class="account-menu" id="notifications-popup">

</nav>
