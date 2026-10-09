<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/old/easyrecipes/include/variables.php");
global $localhost_db, $username_db, $password_db, $database_easyrecipes_api;
header("Content-Type:application/json");
if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_easyrecipes_api)) {
    $c->set_charset("utf8");
    //POST request -> insert a new data to database
    $get = $_GET;

    $condition = isset($get["id"]);
    if ($condition) {
        $recipe_id = $get["id"];

        $status = 3;
        if (isset($get["status"]) && check_authorisation(8)) $status = $get["status"];

        $title = "";
        $preparation = "";
        $preparation_time = 0;
        $difficulty = 0;
        $cover = "";
        $calories = "NULL";

        $vegetarian = 0;// false
        $vegan = 0;// false
        $gluten_free = 0;// false
        $dairy_free = 0;// false

        $new_status = 0;

        $categories = array();

        $ingredients = array();
        $tags = null;
        $images = null;

        if ($status == 0 || $status == 1 || $status == 2 || $status == 3 || $status == 4) {
            $sql = "SELECT * FROM `recipes` WHERE `id`='" . $recipe_id . "' AND `status`='" . $status . "'";
        } else {
            $sql = "SELECT * FROM `recipes` WHERE `id`='" . $recipe_id . "'";
        }

        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_assoc();
                $title = $row["title"];
                $preparation = $row["description"];
                $country_id = $row["origin_country"];
                $calories = $row["calories"];
                $difficulty = $row["difficulty"];
                $vegan = $row["vegan"];
                $vegetarian = $row["vegetarian"];
                $gluten_free = $row["gluten_free"];
                $dairy_free = $row["dairy_free"];
                $new_status = $row["status"];
                $cover = $row["cover"];
                $preparation_time = $row["time"];
                $categories_json = "{";
                if ($vegan == 0 && $vegetarian == 0) {
                    $categories_json .= '"main-category":"not-defined"';
                } elseif ($vegetarian == 1) {
                    $categories_json .= '"main-category":"vegetarian"';
                } elseif ($vegan == 1) {
                    $categories_json .= '"main-category":"vegan"';
                }
                if ($gluten_free == 0) {
                    $categories_json .= ', "gluten-free":false';
                } else {
                    $categories_json .= ', "gluten-free":true';
                }
                if ($dairy_free == 0) {
                    $categories_json .= ', "dairy-free":false';
                } else {
                    $categories_json .= ', "dairy-free":true';
                }
                $categories_json .= "}";
                $categories = json_decode($categories_json, true);
            } else {
                response(502, "Error", "The recipe is not published or doesn't exist");
                return;
            }
        } else {
            response(501, "Error", "Can't get the record from recipes");
            return;
        }

        // ingredients
        $ingredients_json = "{";
        $sql = "SELECT * FROM `rec_ing` WHERE `recipe`=" . $recipe_id;
        if ($r = $c->query($sql)) {
            for ($i = 1; $i <= $r->num_rows; $i++) {
                $row = $r->fetch_assoc();
                $temp_i = "ingredient-n" . $i;
                $ingredient_id = $row["ingredient"];
                $quantity = $row["quantity"];

                $sql = "SELECT * FROM `ingredients` WHERE `id` = " . $ingredient_id;

                if ($r2 = $c->query($sql)) {
                    if ($r2->num_rows == 1) {
                        $row2 = $r2->fetch_assoc();
                        if ($i > 1) $ingredients_json .= ',';
                        $ingredients_json .= '"' . $temp_i . '":';
                        $ingredients_json .= '{"id":' . $row2["id"];
                        $ingredients_json .= ', "name":"' . $row2["value"] . '"';
                        $ingredients_json .= ', "quantity":"' . $quantity . '"}';
                    }
                } else {
                    response(503, "Error", "See the response code");
                    return;
                }
            }
        }
        $ingredients_json .= "}";
        $ingredients = json_decode($ingredients_json, true);


        $tags_json = "{";
        $sql = "SELECT * FROM `rec_tag` WHERE `recipe`=" . $recipe_id;
        if ($r = $c->query($sql)) {
            for ($i = 1; $i <= $r->num_rows; $i++) {
                $row = $r->fetch_assoc();
                $temp_i = "tag-n" . $i;
                $tag_id = $row["tag"];

                $sql = "SELECT * FROM `tags` WHERE `id` = " . $tag_id;

                if ($r2 = $c->query($sql)) {
                    if ($r2->num_rows == 1) {
                        $row2 = $r2->fetch_assoc();
                        if ($i > 1) $tags_json .= ',';
                        $tags_json .= '"' . $temp_i . '":';
                        $tags_json .= '{"value":"' . $row2["value"] . '"}';
                    }
                } else {
                    response(503, "Error", "See the response code");
                    return;
                }
            }
        }
        $tags_json .= "}";
        $tags = json_decode($tags_json, true);


        $images_json = "{";
        $sql = "SELECT * FROM `images` WHERE `recipe`=" . $recipe_id;
        if ($r = $c->query($sql)) {
            for ($i = 1; $i <= $r->num_rows; $i++) {
                $row = $r->fetch_assoc();
                $temp_i = "[[Image" . $i . "]]";

                if ($i > 1) $images_json .= ',';
                $images_json .= '"' . $temp_i . '":';
                $images_json .= '{"url":"' . $row["url"] . '"}';
            }
        }
        $images_json .= "}";
        $images = json_decode($images_json, true);


        $country_json = "";
        $sql = "SELECT * FROM `countries` WHERE `id`=" . $country_id;
        if ($r = $c->query($sql)) {
            if ($r->num_rows == 1) {
                $row = $r->fetch_assoc();

                $country_json .= '{"id":"' . $row["id"] . '"';
                $country_json .= ', "value":"' . $row["value"] . '"}';
            }
        }
        $country = json_decode($country_json, true);

        $total_json = json_decode("{}", true);
        $total_json["title"] = $title;
        $total_json["preparation"] = $preparation;
        $total_json["cover"] = $cover;
        $total_json["ingredients"] = $ingredients;
        $total_json["categories"] = $categories;
        $total_json["images"] = $images;
        $total_json["difficulty"] = $difficulty;
        $total_json["preparation-time"] = $preparation_time;
        $total_json["calories"] = $calories;
        $total_json["country"] = $country;
        $total_json["tags"] = $tags;
        $total_json["status"] = $new_status;

        response(200, "OK", $total_json);
    } else {
        if (!check_authorisation(5)) {
            global $your_privileges;
            response(401, "Error", "You don't have enough privileges to add recipes. Your privileges: " . $your_privileges);
        } else if (!$condition) {
            response(400, "Error", "Parameters are not enough. Check all required fields. Passed: " . $get["title"] . "");
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