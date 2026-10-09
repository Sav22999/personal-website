<?php
global $authorised;
if (!$authorised) {
    ?>
    <form action="/easyrecipes/" method="post" class="form-login margin-10 border-box padding-10">
        <input type="email" placeholder="Email" name="email" class="textbox login"/>
        <input type="password" placeholder="Password" name="password" class="textbox login"/>
        <input type="submit" value="Accedi" class="button submit login"/>
        <h3 id="forget-password" class="link" onclick="alert('Funzione non ancora disponibile')">
            Password dimenticata?
        </h3>
    </form>
    <?php
} else if ($authorised) {
    ?>
    <div class="form-login text-center no-margin no-padding">
        <?php
        global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
        $c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api);
        $c->set_charset("utf8");
        $points = 0;
        $sql = "SELECT SUM(`points`) AS `points` FROM `points` WHERE `user`='" . $_SESSION["session_id"] . "' GROUP BY `user`";
        if ($r = $c->query($sql)) {
            if ($r->num_rows > 0) {
                $points = $r->fetch_assoc()["points"];
            }
        }
        $c->close();

        if (isset($_SESSION["username"])) {
            echo "<h1 class='no-margin padding-10'>Ciao " . $_SESSION["username"] . "!</h1>";
            echo "<h1 class='no-margin no-padding font-size-20'>Punti: " . $points . "</h1>";
        }
        ?>
        <?php
        if (check_authorisation(5)) {
            ?>
            <a href="/old/easyrecipes/recipes/new" class="no-link">
                <button class="account-menu-button">Nuova ricetta</button>
            </a>
            <?php
        }
        ?>
        <?php
        if (check_authorisation(7)) {
            ?>
            <a href="/old/easyrecipes/manage/ingredient/" class="no-link">
                <button class="account-menu-button">Nuovo ingrediente</button>
            </a>
            <?php
        }
        ?>
        <?php
        if (check_authorisation(7)) {
            ?>
            <a href="/old/easyrecipes/manage/country/" class="no-link">
                <button class="account-menu-button">Nuovo Paese</button>
            </a>
            <?php
        }
        ?>
        <hr>
        <a href="/old/easyrecipes/profile/" class="no-link">
            <button class="account-menu-button">Il mio profilo</button>
        </a>
        <a href="/old/easyrecipes/?logout" class="no-link">
            <button class="account-menu-button">Disconnetti</button>
        </a>
    </div>
    <?php
}
?>