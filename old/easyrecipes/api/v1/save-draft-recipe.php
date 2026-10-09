<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api)) {
    $c->set_charset("utf8");
    //POST request -> insert a new data to database
    $_POST = file_get_contents('php://input');
    $post = json_decode($_POST, true);

    $condition = isset($post["title"]) && isset($post["preparation"]) && isset($post["ingredients"]) && isset($post["categories"]) && isset($post["difficulty"]) && isset($post["preparation-time"]) && isset($post["cover"]);

    if (check_authorisation(5) && $condition) {
        $year = date('Y');
        $month = date("m");
        $day = date("d");

        $recipe_id = null;
        if (isset($post["recipe_id"])) {
            $recipe_id = $post["recipe_id"];
        }

        $title = htmlspecialchars(strval($post["title"]));
        $preparation = htmlspecialchars(strval(html_entity_decode($post["preparation"], ENT_HTML5)));
        $preparation = str_replace("'", "&#39;", $preparation);
        $preparation = str_replace("\\", "&#92;", $preparation);
        $preparation_time = $post["preparation-time"];
        $difficulty = $post["difficulty"];
        $cover = $post["cover"]["url"];

        $vegetarian = 0;// false
        $vegan = 0;// false
        $gluten_free = 0;// false
        $dairy_free = 0;// false

        $categories = $post["categories"];
        if (isset($categories["main-category"])) {
            if ($categories["main-category"] === "vegetarian") {
                $vegetarian = 1;
            } else if ($categories["main-category"] === "vegan") {
                $vegetarian = 1;
                $vegan = 1;
            }
        }
        if (isset($categories["gluten-free"])) {
            if ($categories["gluten-free"] === true) {
                $gluten_free = 1;
            }
        }
        if (isset($categories["dairy-free"])) {
            if ($categories["dairy-free"] === true) {
                $dairy_free = 1;
            }
        }

        $ingredients = $post["ingredients"];
        $tags = null;
        if (isset($post["tags"])) {
            $tags = $post["tags"];
        }
        $country = null;
        $country_id = "NULL";
        if (isset($post["country"])) {
            $country = $post["country"];
            if (isset($country["id"])) {
                $country_id = $country["id"];
            }
        }
        $calories = "NULL";
        if (isset($post["calories"]) && $post["calories"] != "") {
            $calories = $post["calories"];
        }
        $images = null;
        if (isset($post["images"])) {
            $images = $post["images"];
        }
        $author = $_SESSION["session_id"];

        $date = htmlspecialchars(date("Y-m-d H:i:s"));

        if ($recipe_id == null) {
            $sql = "INSERT INTO `recipes`(`id`, `title`,`description`,`cover`, `time`, `difficulty`, `status`, `user_inserted`, `user_approved`, `date_inserted`,`date_approved`,`origin_country`,`calories`,`vegetarian`,`vegan`,`gluten_free`,`dairy_free`) VALUES(NULL,'" . $title . "','" . $preparation . "','" . $cover . "','" . $preparation_time . "','" . $difficulty . "',1,'" . $author . "',NULL,'" . $date . "',NULL," . $country_id . "," . $calories . "," . $vegetarian . "," . $vegan . "," . $gluten_free . "," . $dairy_free . ")";
        } else {
            $sql = "UPDATE `recipes` SET `title` = '$title', `description`='$preparation',`cover`='$cover', `time`='$preparation_time', `difficulty`='$difficulty', `status`=1,`origin_country`='$country_id',`calories`='$calories',`vegetarian`='$vegetarian',`vegan`='$vegan',`gluten_free`='$gluten_free',`dairy_free`='$dairy_free') WHERE `id`=$recipe_id";
        }

        if (!$c->query($sql)) {
            response(500, "Error", "Can't update record in recipes: " . $sql);
            return;
        }

        if ($recipe_id != null) {
            // ingredients
            if ($ingredients != null) {
                for ($i = 1; $i <= sizeof($ingredients); $i++) {
                    $temp_i = "ingredient-n" . $i;

                    if (isset($ingredients[$temp_i]["id"]) && isset($ingredients[$temp_i]["name"]) && isset($ingredients[$temp_i]["quantity"])) {
                        $ingredient_id = $ingredients[$temp_i]["id"];
                        $ingredient_name = $ingredients[$temp_i]["name"];
                        $ingredient_quantity = $ingredients[$temp_i]["quantity"];

                        $ingredient_quantity = htmlspecialchars(strval(html_entity_decode($ingredient_quantity, ENT_HTML5)));
                        $ingredient_quantity = str_replace("'", "&#39;", $ingredient_quantity);
                        $ingredient_quantity = str_replace("\\", "&#92;", $ingredient_quantity);

                        $sql = "SELECT `id` FROM `rec_ing` WHERE `ingredient` = " . $ingredient_id . " AND `recipe`=" . $recipe_id;

                        if ($r = $c->query($sql)) {
                            if ($r->num_rows == 0) {
                                // new
                                $sql = "DELETE * FROM `rec_ing` WHERE `recipe` = " . $recipe_id;
                                if ($c->query($sql)) {
                                    $sql = "INSERT INTO `rec_ing`(`id`,`recipe`,`ingredient`,`quantity`) VALUES(NULL," . $recipe_id . "," . $ingredient_id . ",'" . $ingredient_quantity . "')";

                                    if (!$c->query($sql)) {
                                        response(504, "Error", "Can't insert record in rec_ing");
                                        return;
                                    }
                                }
                            }
                        } else {
                            response(503, "Error", "See the response code");
                            return;
                        }
                    }
                }
            }

            if ($tags != null) {
                for ($i = 1; $i <= sizeof($tags); $i++) {
                    $temp_i = "tag-n" . $i;

                    $tag_id = null;
                    $tag_value = $tags[$temp_i]["value"];
                    $tag_value = htmlspecialchars(strval(html_entity_decode($tag_value, ENT_HTML5)));
                    $tag_value = str_replace("'", "&#39;", $tag_value);
                    $tag_value = str_replace("\\", "&#92;", $tag_value);

                    $sql = "SELECT `id` FROM `tags` WHERE `value` = '" . $tag_value . "'";

                    if ($r = $c->query($sql)) {
                        if ($r->num_rows == 0) {
                            // new
                            $sql = "INSERT INTO `tags`(`id`,`value`) VALUES(NULL,'" . $tag_value . "')";

                            if (!$c->query($sql)) {
                                response(506, "Error", "Can't insert record in tags");
                                return;
                            }

                            $sql = "SELECT `id` FROM `tags` WHERE `value`='" . $tag_value . "'";

                            if ($r = $c->query($sql)) {
                                if ($r->num_rows == 1) {
                                    $row = $r->fetch_assoc();
                                    $tag_id = $row["id"];
                                }
                            } else {
                                response(507, "Error", "See the response code");
                                return;
                            }
                        } else {
                            $row = $r->fetch_assoc();
                            $tag_id = $row["id"];
                        }
                    } else {
                        response(505, "Error", "See the response code");
                        return;
                    }

                    $sql = "SELECT `id` FROM `rec_tag` WHERE `tag` = " . $tag_id . " AND `recipe`=" . $recipe_id;

                    if ($r = $c->query($sql)) {
                        if ($r->num_rows == 0) {
                            // new
                            $sql = "DELETE * FROM `rec_tag` WHERE `recipe` = " . $recipe_id;
                            if ($c->query($sql)) {
                                $sql = "INSERT INTO `rec_tag`(`id`,`recipe`,`tag`) VALUES(NULL," . $recipe_id . "," . $tag_id . ")";

                                if (!$c->query($sql)) {
                                    response(509, "Error", "Can't insert record in rec_tag");
                                    return;
                                }
                            }
                        }
                    } else {
                        response(508, "Error", "See the response code");
                        return;
                    }
                }
            }

            if ($images != null) {
                for ($i = 1; $i <= sizeof($images); $i++) {
                    $temp_i = "[[Image" . $i . "]]";

                    $image_url = $images[$temp_i];
                    $image_value = $temp_i;

                    $sql = "SELECT `id` FROM `images` WHERE `url` = '" . $image_url . "' AND `recipe`=" . $recipe_id;

                    if ($r = $c->query($sql)) {
                        if ($r->num_rows == 0) {
                            // new
                            $sql = "DELETE * FROM `images` WHERE `recipe` = " . $recipe_id;
                            if ($c->query($sql)) {
                                $sql = "INSERT INTO `images`(`id`,`recipe`,`url`,`value`) VALUES(NULL," . $recipe_id . ",'" . $image_url . "','" . $image_value . "')";

                                if (!$c->query($sql)) {
                                    response(511, "Error", "Can't insert record in images");
                                    return;
                                }
                            }
                        }
                    } else {
                        response(510, "Error", "See the response code");
                        return;
                    }
                }
            }
        }

        response(200, "OK", "Recipe inserted in the database");
    } else {
        if (!check_authorisation(5)) {
            global $your_privileges;
            response(401, "Error", "You don't have enough privileges.\nYour privileges: " . $your_privileges);
        } else if (!$condition) {
            $parameters_not_passed = "[";
            if (!isset($post["title"])) $parameters_not_passed .= "title,";
            if (!isset($post["preparation"])) $parameters_not_passed .= "preparation,";
            if (!isset($post["ingredients"])) $parameters_not_passed .= "ingredients,";
            if (!isset($post["categories"])) $parameters_not_passed .= "categories,";
            if (!isset($post["difficulty"])) $parameters_not_passed .= "difficulty,";
            if (!isset($post["preparation-time"])) $parameters_not_passed .= "preparation-time,";
            if (!isset($post["cover"])) $parameters_not_passed .= "cover,";
            $parameters_not_passed .= "]";

            response(400, "Error", "Parameters are not enough. Check all required fields.\nNot passed: $parameters_not_passed");
        } else {
            response(402, "Error", "Something was wrong in POST request");
        }
    }
} else {
    response(500, "Error", "Can't connect to the database");
}

function response($response_code, $response_status, $response_description)
{
    $response['code'] = $response_code;
    $response['status'] = $response_status;
    $response['description'] = $response_description;

    $json_response = json_encode($response);
    echo $json_response;
}

?>