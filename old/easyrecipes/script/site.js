$(document).ready(function () {
    loaded();
});

var images_inserted = 0;
var images_inserted_json = {};

var ingredient_index = 0;
var ingredients_json = {};

var tag_index = 0;
var tags_json = {};

var country_json = {};

var cover_json = {};

var categories_json = {};

var difficulty_json = 0;

var preparation_time_json = "";

var calories_json = "";

var ingredients_loaded = [];
var countries_loaded = [];
var cover_loaded = [];

var recipe_json = {};

var message_cont = 0;

var allowed = true; //permission successful? > "true", else "false"

var recipe_id = null;

function loaded() {
    $("h1.h1-account").on("click", function () {
        $("#account-popup").fadeToggle();
        $("#notifications-popup").fadeOut();
    });

    $("h1.h1-notifications").on("click", function () {
        $("#account-popup").fadeOut();
        if (!$("#notifications-popup").is(":visible")) {
            $("#notifications-popup").fadeIn();
            readLastNotifications();
        } else {
            $("#notifications-popup").fadeOut();
            loadNotificationsActual();
        }
    });
    loadNotifications();

    $("h1.h1-logo").on("click", function () {
        window.location.href = "/old/easyrecipes/";
    });

    var page_type = 1;

    if ($("#new-recipe-page").length > 0 || $("#edit-recipe-page").length > 0) {
        //New recipe page OR Edit recipe page
        page_type = 2;

        if ($("#edit-recipe-page").length > 0) {
            let newURL = new URL(window.location.href);
            recipe_id = newURL.searchParams.get("id");
            loadRecipeSaved(recipe_id);
        } else {
            setTypes(0);
            setGlutenFree(true, false);
            setDairyFree(true, false);
            setDifficulty(0);
        }

        $("#upload-image").change(function () {
            onChangeUploadImage();
        });

        $("#text-tag").on("keyup", function (e) {
            if (e.keyCode === 13 || e.key === "Enter") {
                okTag();
            }
        });

        $("#quantity-ingredient-temp").on("keyup", function (e) {
            if (e.keyCode === 13 || e.key === "Enter") {
                okIngredient();
            }
        });

        $("#country-selected-pop-up").on("keyup", function (e) {
            if (e.keyCode === 13 || e.key === "Enter") {
                okCountry();
            }
        });

        $("#cover-selected-pop-up").on("keyup", function (e) {
            if (e.keyCode === 13 || e.key === "Enter") {
                okCover();
            }
        });

        $("#title-recipe").on("keypress", function (e) {
                return allowedText(e);
            }
        );
        $("#title-recipe").on("keyup", function (e) {
            if (e.keyCode === 13 || e.key === "Enter") {
                $("#preparation-recipe").focus();
            }
        });

        $("#preparation-recipe").on("keypress", function (e) {
                //checkPreparationRecipeHeight();
                return allowedText(e);
            }
        );
        $("#preparation-recipe").on("keyup", function (e) {
                checkPreparationRecipeHeight();
            }
        );

        $(".preparation-time-value").on("keypress", function (e) {
                return allowedNumber(e, false);
            }
        );

        $(".calories-value").on("keypress", function (e) {
                return allowedNumber(e, true);
            }
        );
    }

    if ($("#new-ingredient-page").length > 0) {
        // New ingredient
        focus("ingredient-text");
        $("#ingredient-text").on("keypress", function (e) {
            return allowedText(e);
        });
        $("#ingredient-text").on("keyup", function (e) {
            checkIngredientText();
        });

        $("#ingredient-type-text").val("");
        setIngredientType(-1, "");
        $("#ingredient-type-container").css({"display": "none"});

        $("#ingredient-text").on("keyup", function (e) {
            if (e.keyCode === 13 || e.key === "Enter") {
                finishAndAddIngredient();
            }
        });
    }

    if ($("#new-country-page").length > 0) {
        // New country
        focus("country-text");
        $("#country-text").on("keypress", function (e) {
            return allowedText(e);
        });

        $("#country-text").on("keyup", function (e) {
            if (e.keyCode === 13 || e.key === "Enter") {
                finishAndAddCountry();
            }
        });
    }

    if ($("#home-page").length > 0) {
        // Home page
        page_type = 3;
    }

    checkSize(page_type);

    $(window).scroll(function () {
        checkSize(page_type);
    });
    $(window).resize(function () {
        checkSize(page_type);
        hidePopUp();
    });

    $(document).on("keyup", function (e) {
        if (e.keyCode === 27 || e.key === "Escape") {
            hidePopUp();
        }
    })
}

function allowedNumber(e, decimal = false) {
    let allowed_commands = [8, 13, 35, 36, 37, 38, 39, 40, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
    if (decimal) allowed_commands.push(190);
    let index = 0;
    let keyAllowed = false;
    for (index = 0; index < allowed_commands.length && !keyAllowed; index++) {
        if (e.keyCode === allowed_commands[index]) {
            keyAllowed = true;
        }
    }
    //showMessageBottom(e.keyCode);
    return keyAllowed;
}

function allowedText(e) {
    let allowed_commands = [8, 13, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 93, 94, 95, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 160, 163, 171, 173, 176, 188, 191, 192, 190, 222, 224, 232, 233, 236, 242, 249, 8364];
    let index = 0;
    let keyAllowed = false;
    for (index = 0; index < allowed_commands.length && !keyAllowed; index++) {
        if (e.keyCode === allowed_commands[index]) {
            keyAllowed = true;
        }
    }
    //showMessageBottom(e.keyCode);
    return keyAllowed;
}

function checkSize(type) {
    if ($(".top-menu").width() <= 1000) {
        //Mobile
        if (type == 2) {
            showFullscreenMessage("pop-up-display-too-small");
        } else if (type == 1) {
            $("main").css({"margin-left": "1%", "margin-right": "1%", "width": "auto", "float": "none"});
        } else if (type == 3) {
            $("main").css({"margin-left": "0%", "margin-right": "0%", "width": "79%", "float": "left"});
        }
    } else {
        //Desktop
        if (type == 2) {
            hideFullscreenMessages();
        } else if (type == 1 || type == 3) {
            $("main").css({"margin-left": "0%", "margin-right": "0%", "width": "60%", "float": "left"});
        }
    }

    if ($("#recipes-inserted-and-approved-page").length > 0 || $("#recipes-inserted-page").length > 0 || $("#recipes-drafted-page").length > 0) {
        let userAgentString = navigator.userAgent;
        let firefoxAgent = userAgentString.indexOf("Firefox") > -1;
        if ($(".top-menu").width() <= 1000) {
            //Mobile
            if (firefoxAgent) {
                setColumns(1);
            } else {
                setColumns(1);
            }
        } else if ($(".top-menu").width() <= 1500) {
            //Tablet
            if (firefoxAgent) {
                setColumns(1);
            } else {
                if (allowed) {
                    setColumns(2);
                } else {
                    setColumns(1);
                }
            }
        } else {
            //Desktop
            if (firefoxAgent) {
                setColumns(1);
            } else {
                if (allowed) {
                    setColumns(3);
                } else {
                    setColumns(1);
                }
            }
        }
    }
}

function setColumns(n) {
    $("#list").css({"column-count": n, "-webkit-column-count": n, "-moz-column-count": n});
}

function focus(id) {
    $("#" + id).focus();
}

function loadRecipeSaved() {
    showFullscreenMessage("pop-up-recipe-loding", false);
    $.ajax({
        url: '/easyrecipes/api/v1/get-recipe.php?id=' + recipe_id + '&status=-1',
        type: 'get',
        contentType: false,
        processData: false,
        success: function (response) {
            if (response != 0) {
                recipe_json = response["description"];
                loadRecipeDataFromJson();
                hideFullscreenMessages();
            } else {
                showMessageBottom('A causa di un problema inaspettato non è possibile visualizzare la ricetta. Riprovare.', -1, 2);
                hideFullscreenMessages();
            }
        },
        error: function () {
            showMessageBottom("Errore inaspettato. Riavviare.", 10, 2);
            hideFullscreenMessages();
        }
    });
}

function loadRecipeDataFromJson() {
    alert(JSON.stringify(recipe_json));
    $(".title-recipe").val(recipe_json["title"]);
    $(".preparation-recipe").val(recipe_json["preparation"]);
    cover_json = recipe_json["cover"];
    ingredients_json = recipe_json["ingredients"];
    loadIngredientsFromJson();
    categories_json = recipe_json["categories"];
    if (categories_json["main-category"] == "not-defined") setTypes(0);
    else if (categories_json["main-category"] == "vegetarian") setTypes(1);
    else if (categories_json["main-category"] == "vegan") setTypes(2);
    setGlutenFree(true, categories_json["gluten-free"]);
    setDairyFree(true, categories_json["dairy-free"]);

    images_inserted_json = recipe_json["images"];
    loadImagesFromJson();
    difficulty_json = recipe_json["difficulty"];
    setDifficulty(difficulty_json - 1);

    preparation_time_json = recipe_json["preparation-time"];
    $(".preparation-time-value").val(preparation_time_json);
    calories_json = recipe_json["calories"];
    $(".calories-value").val(calories_json);

    country_json = recipe_json["country"];
    $("#plus-country").val(country_json["value"]);
    $("#plus-country").addClass("width-100-perc");
    $("#plus-country").click(function () {
        $("#country-selected-pop-up").val($("#plus-country").val());
        $("#ok-country").html("Modifica");
        $("#remove-country").css({"display": "inline-block"});
        $("#remove-country-separator").css({"display": "inline-block"});
    });
    loadCountries();

    tags_json = recipe_json["tags"];
    loadTagsFromJson();
}

function loadImagesFromJson() {
    //all images
    let cover_index_to_use = "";

    let images_inserted_temp = images_inserted_json;
    images_inserted = 0;
    for (let item in images_inserted_temp) {
        let urlToUse = images_inserted_temp[item]["url"];

        if (cover_json != {} && urlToUse == cover_json) {
            cover_index_to_use = item;
        }

        images_inserted++;
        let classTypeToUse = "inserted-image";
        if (images_inserted >= 4) {
            if (images_inserted == 4) {
                $(".inserted-image").addClass("inserted-image-4");
                $(".inserted-image-4").removeClass("inserted-image");
            }
            classTypeToUse = "inserted-image-4";

            if (images_inserted >= 8) {
                if (images_inserted == 8) {
                    $(".inserted-image-4").addClass("inserted-image-8");
                    $(".inserted-image-8").removeClass("inserted-image-4");
                }
                classTypeToUse = "inserted-image-8";
            }
        }
        $("#images-uploaded").append(`<input type="button" class="${classTypeToUse}" style="background-image: url('${urlToUse}');"
onclick="showDetailsImage(this,'[[Image${images_inserted}]]')"/>`);
        //images_inserted_json["[[Image" + (images_inserted) + "]]"] = urlToUse;
    }

    //cover image
    if (cover_json != {}) {
        $("#plus-cover").val(cover_index_to_use);
        $("#plus-cover").addClass("width-100-perc");
        $("#plus-cover").click(function () {
            $("#cover-selected-pop-up").val($("#plus-cover").val());
            $("#ok-cover").html("Modifica");
            $("#remove-cover").css({"display": "inline-block"});
            $("#remove-cover-separator").css({"display": "inline-block"});
        });
        let cover_tmp = {};
        cover_tmp["url"] = cover_json;
        cover_tmp["value"] = cover_index_to_use;
        cover_json = cover_tmp;
        $("#cover-image").css({"display": "block", "background-image": "url('" + cover_json["url"] + "')"});
    }
}

function loadIngredientsFromJson() {
    loadIngredients();
    for (let item in ingredients_json) {

        ingredient_index++;
        let ingredient_id = "ingredient-n" + ingredients_json[item]["id"];
        let ingredient_name = ingredients_json[item]["name"];
        let ingredient_quantity = ingredients_json[item]["quantity"];

        $("#all-ingredients").append("<button id='" + ingredient_id + "' class='ingredient button inline-block' onclick='showPopUp(\"ingredients\",\"left\")'>" + ingredient_name + "</button>");
        let ingredient_details_json = {};
        ingredient_details_json["id"] = ingredient_id;
        ingredient_details_json["name"] = ingredient_name;
        ingredient_details_json["quantity"] = ingredient_quantity;
        //ingredients_json[ingredient_id] = ingredient_details_json;
        $("#" + ingredient_id).html(ingredients_json[ingredient_id].name);
        $("#" + ingredient_id).click(function () {
            $("#ingredient-selected-pop-up").val(ingredients_json[ingredient_id].name);
            $("#quantity-ingredient-temp").val($("#quantity-ingredient-temp").val());
            $("#text-ingredient-temp").val(ingredients_json[ingredient_id].name);
            let tmp_el = ingredients_json[ingredient_id].quantity;
            let last_1_char = tmp_el[tmp_el.length - 1];
            let last_2_char = "";
            if (tmp_el.length >= 2) last_2_char = tmp_el[tmp_el.length - 2];
            if (last_1_char == "g") {
                setQuantity(0, "g");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 1));
            } else if (last_2_char == "m" && last_1_char == "l") {
                setQuantity(0, "ml");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 2));
            } else if (last_1_char == "n") {
                setQuantity(0, "n");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 1));
            } else if (last_2_char == "q" && last_1_char == "b") {
                setQuantity(1, "qb");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 2));
            } else {
                setQuantity(2, "c");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 0));
            }
            $("#ok-ingredient").html("Modifica");
            $("#remove-ingredient").css({"display": "inline-block"});
            $("#remove-ingredient-separator").css({"display": "inline-block"});
            $("#remove-ingredient").off("click");
            $("#remove-ingredient").click(function () {
                removeIngredient(ingredient_id);
            });
        });
    }
}

function loadTagsFromJson() {
    alert(JSON.stringify(tags_json));
    let exists = false;
    let tag_id = "";
    if ($("#text-tag-temp").val() != "") {
        for (let tmp_index = 1; tmp_index <= tag_index && !exists; tmp_index++) {
            if (tags_json["tag-n" + tmp_index] !== undefined) {
                if (tags_json["tag-n" + tmp_index].value == $("#text-tag-temp").val()) {
                    tag_id = "tag-n" + tmp_index;
                    exists = true;
                }
            }
        }
    }
    if (!exists) {
        tag_index++;
        tag_id = "tag-n" + tag_index;
        $("#all-tags").append("<button id='" + tag_id + "' class='tag button inline-block' onclick='showPopUp(\"tags\",\"right\")'>" + $("#text-tag").val() + "</button>");
    } else {
        //tag già inserito, quindi modifico quello già esistente
        $("#" + tag_id).off("click");
    }
    let tag_details_json = {};
    tag_details_json["value"] = $("#text-tag").val();
    tags_json[tag_id] = tag_details_json;
    $("#" + tag_id).html(tags_json[tag_id].value);
    $("#" + tag_id).click(function () {
        $("#text-tag").val(tags_json[tag_id].value);
        $("#text-tag-temp").val(tags_json[tag_id].value);
        $("#ok-tag").html("Modifica");
        $("#remove-tag").css({"display": "inline-block"});
        $("#remove-tag-separator").css({"display": "inline-block"});
        $("#remove-tag").off("click");
        $("#remove-tag").click(function () {
            removeTag(tag_id);
        });
    });
}

function showMessageBottom(text, seconds = 10, type = 1) {
    //you can pass also "seconds=-1": the message won't be hide automatically
    if (text != "") {
        let milliseconds = seconds * 1000;//to milliseconds
        let classToUse = "";
        let id_to_use = "message-bottom-" + message_cont;

        classToUse += "" + "message-bottom";
        if (type == 2) {
            classToUse += " message-warning";
        }
        let new_message = document.createElement("div");
        new_message.className = classToUse;
        new_message.id = id_to_use;
        new_message.innerHTML = text;
        $("body").append(new_message);
        let new_close_button = document.createElement("button");
        new_close_button.className = "close-message-button button no-box-shadow";
        new_close_button.innerHTML = "";
        new_close_button.onclick = function () {
            hideMessageBottom(id_to_use);
        };
        $("#" + id_to_use).append(new_close_button);
        $("#" + id_to_use).fadeIn();
        if (seconds > 0) {
            setTimeout(function () {
                hideMessageBottom(id_to_use);
            }, milliseconds);
        }

        message_cont++;
    }
}

function hideMessageBottom(id_to_use) {
    if ($("#" + id_to_use).length) {
        /*$("#" + id_to_use).fadeOut();
        setTimeout(function () {
            $("#" + id_to_use).remove();
        }, 1000);*/
        $("#" + id_to_use).remove();
    }
}

function showFullscreenMessage(id, change = true) {
    if (change) {
        $("nav.top-menu").css({"position": "fixed"});
        $(".fullscreen-message").css({"top": "78px"});
    }
    $("#" + id).fadeIn("fast");
}

function hideFullscreenMessages() {
    $("nav.top-menu").css({"position": "relative"});
    $(".fullscreen-message").css({"top": "0px"});
    $(".fullscreen-message").fadeOut("fast");
}

/*New country*/

function resetNewCountryFields() {
    $("#country-text").val("");
}

function finishAndAddCountry() {
    if ($("#country-text").val() != "") {
        showFullscreenMessage("pop-up-display-inserting", false);

        let country_json = {};
        country_json["country"] = $("#country-text").val().toString().toLowerCase();

        let data = JSON.stringify(country_json);

        let url = "/old/easyrecipes/api/v1/insert-country.php";

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            contentType: "application/json",
            dataType: "json",
            processData: false,
            success: function (response) {
                if (response != 0) {
                    if (response["status"] == "OK") {
                        showMessageBottom("Paese inserito correttamente");
                        resetNewCountryFields();
                    } else if (response["status"] == "Error" && response["code"] == 502) {
                        showMessageBottom("Errore: Paese già presente", 10, 2);
                        resetNewCountryFields();
                    } else {
                        showMessageBottom("Errore: il Paese non è stato inserita<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                    }
                    hideFullscreenMessages();
                } else {
                    showMessageBottom('A causa di un problema inaspettato il Paese non è stato inserito. Riprovare.', -1, 2);
                    hideFullscreenMessages();
                }
            },
            error: function () {
                showMessageBottom("Errore inaspettato. Riprovare.<br>" + JSON.stringify(recipe_json), -1, 2);
                hideFullscreenMessages();
            }
        });
    } else {
        showMessageBottom("Errore: compila tutti i campi", 10, 2);
    }
}

/*New ingredient*/

function checkIngredientText() {
    if ($("#ingredient-text").val() != "") {
        $("#ingredient-type-container").css({"display": "block"});
    } else {
        $("#ingredient-type-text").val("");
        setIngredientType(-1, "");
        $("#ingredient-type-container").css({"display": "none"});
    }
}

function resetNewIngredientFields() {
    $("#ingredient-text").val("");
    $("#ingredient-type-text").val("");
    setIngredientType(-1, "");
    $("#ingredient-type-container").css({"display": "none"});
}

function setIngredientType(index, type) {
    $(".ingredients-button").removeClass("button-selected");
    if (index != -1) $(".ingredients-button").eq(index).addClass("button-selected");

    $("#ingredient-type-text").val(type);
    focus("ingredient-text");
}

function finishAndAddIngredient() {
    if ($("#ingredient-text").val() != "" && $("#ingredient-type-text").val() != "") {
        showFullscreenMessage("pop-up-display-inserting", false);

        let ingredient_json = {};
        ingredient_json["name"] = $("#ingredient-text").val().toString().toLowerCase();
        ingredient_json["type"] = $("#ingredient-type-text").val();

        let data = JSON.stringify(ingredient_json);

        let url = "/old/easyrecipes/api/v1/insert-ingredient.php";

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            contentType: "application/json",
            dataType: "json",
            processData: false,
            success: function (response) {
                if (response != 0) {
                    if (response["status"] == "OK") {
                        showMessageBottom("Ingrediente inserito correttamente");
                        resetNewIngredientFields();
                    } else if (response["status"] == "Error" && response["code"] == 502) {
                        showMessageBottom("Errore: ingrediente già presente", 10, 2);
                        resetNewIngredientFields();
                    } else {
                        showMessageBottom("Errore: l'ingrediente non è stato inserito<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                    }
                    hideFullscreenMessages();
                } else {
                    showMessageBottom('A causa di un problema inaspettato l\'ingrediente non è stato inserito. Riprovare.', -1, 2);
                    hideFullscreenMessages();
                }
            },
            error: function () {
                showMessageBottom("Errore inaspettato. Riprovare.<br>" + JSON.stringify(recipe_json), -1, 2);
                hideFullscreenMessages();
            }
        });
    } else {
        showMessageBottom("Errore: compila tutti i campi", 10, 2);
    }
}

function resetNewIngredientsFields() {
    $("#ingredients-text").val("");
}

let summary_ingredients_message_to_show = false;
let n_ingredients_temp = 0;
let n_ingredients_passed_temp = 0;
let n_inserted = 0;
let n_errors = 0;

function finishAndAddIngredients() {
    if ($("#ingredients-text").val() != "") {
        showFullscreenMessage("pop-up-display-inserting", false);

        let ingredients_text = $("#ingredients-text").val();
        let ingredients_text_temp = ingredients_text.toLowerCase().split(/\n/);

        summary_ingredients_message_to_show = true;

        let n_ingredients = ingredients_text_temp.length;
        let ingredients_json = {};
        let index_to_use = 0;
        let n_ingredients_to_use = n_ingredients;

        let ingredients_name_temp = [];

        for (index = 0; index < n_ingredients; index++) {
            let ingredient_temp = ingredients_text_temp[index].split("|");
            if ((ingredient_temp[1] == "g" || ingredient_temp[1] == "ml" || ingredient_temp[1] == "n") && (ingredients_name_temp.indexOf(ingredient_temp[0]) == -1)) {
                let ingredient_json = {};
                ingredient_json["name"] = ingredient_temp[0];
                ingredient_json["type"] = ingredient_temp[1];
                ingredients_name_temp.push(ingredient_json["name"]);

                ingredients_json["ingredient-n" + index_to_use] = ingredient_json;
            } else {
                index_to_use--;
                n_ingredients_to_use--;
                showMessageBottom("Alla riga " + (index + 1) + " è presente un errore e, pertanto, l'ingrediente \"" + ingredient_temp[0] + "\" non è stato inserito", -1, 2)
            }
            index_to_use++;
        }

        //console.log(JSON.stringify(ingredients_json));

        let url = "/old/easyrecipes/api/v1/insert-ingredient.php";

        n_ingredients_temp = n_ingredients_to_use;

        for (index = 0; index < n_ingredients_to_use; index++) {
            let ingredient_temp_json = {};
            ingredient_temp_json["name"] = ingredients_json["ingredient-n" + index]["name"];
            ingredient_temp_json["type"] = ingredients_json["ingredient-n" + index]["type"];

            let data = JSON.stringify(ingredient_temp_json);
            $.ajax({
                url: url,
                type: 'post',
                data: data,
                contentType: "application/json",
                dataType: "json",
                processData: false,
                success: function (response) {
                    if (response != 0) {
                        if (response["status"] == "OK") {
                            showMessageBottom("Ingrediente inserito correttamente");
                            n_inserted++;
                            showSummaryIngredientsMessage();
                        } else if (response["status"] == "Error" && response["code"] == 502) {
                            showMessageBottom("Errore: ingrediente \"" + ingredient_temp_json["name"] + "\" già presente", -1, 2);
                            n_errors++;
                            showSummaryIngredientsMessage();
                        } else {
                            showMessageBottom("Errore: l'ingrediente \"" + ingredient_temp_json["name"] + "\" non è stato inserito<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                            n_errors++;
                            showSummaryIngredientsMessage();
                        }
                    } else {
                        showMessageBottom('A causa di un problema inaspettato l\'ingrediente "' + ingredient_temp_json["name"] + '" non è stato inserito. Riprovare.', -1, 2);
                        n_errors++;
                        showSummaryIngredientsMessage();
                    }
                },
                error: function () {
                    showMessageBottom("Errore inaspettato. Riprovare.<br>" + JSON.stringify(recipe_json), -1, 2);
                    n_errors++;
                    showSummaryIngredientsMessage();
                }
            });
        }
    } else {
        showMessageBottom("Errore: compila tutti i campi", 10, 2);
    }
}

function showSummaryIngredientsMessage() {
    n_ingredients_passed_temp++;
    if (summary_ingredients_message_to_show && (n_ingredients_temp == n_ingredients_passed_temp)) {
        showMessageBottom("Sono stati inseriti " + n_inserted + " nuovi ingredienti, mentre ne sono stati ignorati " + n_errors + "", -1, 1);
        resetNewIngredientsFields();
        hideFullscreenMessages();
        summary_ingredients_message_to_show = false;
    }
}

/*New recipe*/

var checkPreparationRecipeHeight = function () {
    let ta = document.getElementById("preparation-recipe");
    ta.style.transition = "0s";
    let style = (window.getComputedStyle) ?
        window.getComputedStyle(ta) : ta.currentStyle;

    // This will get the line-height only if it is set in the css,
    // otherwise it's "normal"
    let taLineHeight = parseInt(style.lineHeight, 10);
    // Get the scroll height of the textarea
    let taHeight = ta.scrollHeight;
    // calculate the number of lines
    let numberOfLines = Math.floor(taHeight / taLineHeight);
    if (numberOfLines < 100) ta.style.height = ((numberOfLines * taLineHeight) + 4) + "px";
};

function setTypes(index) {
    document.getElementsByName("type")[index].checked = true;
    changedTypes(index);
    let string_category = "";
    switch (index) {
        case 0:
            string_category = "not-defined";
            break;

        case 1:
            string_category = "vegetarian";
            break;

        case 2:
        default:
            string_category = "vegan";
    }
    categories_json["main-category"] = string_category;
}

function changedTypes(index) {
    $(".types-button-radio").removeClass("button-selected");
    $(".types-button-radio").eq(index).addClass("button-selected");
}

function setGlutenFree(forced = false, status = true) {
    if (!forced) document.getElementsByName("gluten-free")[0].checked = !document.getElementsByName("gluten-free")[0].checked;
    else document.getElementsByName("gluten-free")[0].checked = status;
    toggleGlutenFree();
    categories_json["gluten-free"] = document.getElementsByName("gluten-free")[0].checked;
}

function toggleGlutenFree(forced = false, status = true) {
    if (document.getElementsByName("gluten-free")[0].checked || (forced && status)) {
        $(".gluten-free").addClass("button-selected");
    } else {
        $(".gluten-free").removeClass("button-selected");
    }
}

function setDairyFree(forced = false, status = true) {
    if (!forced) document.getElementsByName("dairy-free")[0].checked = !document.getElementsByName("dairy-free")[0].checked;
    else document.getElementsByName("dairy-free")[0].checked = status;
    toggleDairyFree(forced, status);
    categories_json["dairy-free"] = document.getElementsByName("dairy-free")[0].checked;
}

function toggleDairyFree(forced = false, status = true) {
    if (document.getElementsByName("dairy-free")[0].checked || (forced && status)) {
        $(".dairy-free").addClass("button-selected");
    } else {
        $(".dairy-free").removeClass("button-selected");
    }
}

function setDifficulty(index) {
    document.getElementsByName("difficulty")[index].checked = true;
    changedDifficulty(index);
    difficulty_json = (index + 1);
}

function changedDifficulty(index) {
    $(".difficulty-button").removeClass("button-selected");
    $(".difficulty-button").eq(index).addClass("button-selected");
}

function onChangePreparationTime() {
    preparation_time_json = $(".preparation-time-value").val();
}

function onChangeCalories() {
    calories_json = $(".calories-value").val();
}

function showPopUp(element, rightOrLeft) {
    hidePopUp();

    let y = $("." + element + "-section").position().top;
    let x = 0;
    if (rightOrLeft == "right") {
        $("#pop-up-" + element).css({"right": "10px", "left": "auto", "top": y});
    } else if (rightOrLeft == "left") {
        $("#pop-up-" + element).css({"left": "10px", "right": "auto", "top": y});
    }
    $("#pop-up-" + element).fadeIn("fast");

    if (element == "tags") {
        focus('text-tag');
    } else if (element == "ingredients") {
        loadIngredients();
        focus('ingredient-selected-pop-up');
    } else if (element == "origin-country") {
        loadCountries();
        focus('country-selected-pop-up');
    } else if (element == "cover") {
        loadCover();
        focus('cover-selected-pop-up');
    }
}

function onChangeUploadImage() {
    var fd = new FormData();
    var files = $('#upload-image')[0].files[0];
    fd.append('file', files);
    showFullscreenMessage("pop-up-display-uploading", false);
    $.ajax({
        url: '/easyrecipes/api/v1//upload.php',
        type: 'post',
        data: fd,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response != 0) {
                response = response.replace("/old/web/htdocs/www.saveriomorelli.com/home", "");
                images_inserted++;
                let classTypeToUse = "inserted-image";
                if (images_inserted >= 4) {
                    if (images_inserted == 4) {
                        $(".inserted-image").addClass("inserted-image-4");
                        $(".inserted-image-4").removeClass("inserted-image");
                    }
                    classTypeToUse = "inserted-image-4";

                    if (images_inserted >= 8) {
                        if (images_inserted == 8) {
                            $(".inserted-image-4").addClass("inserted-image-8");
                            $(".inserted-image-8").removeClass("inserted-image-4");
                        }
                        classTypeToUse = "inserted-image-8";
                    }
                }
                $("#images-uploaded").append(`<input type="button" class="${classTypeToUse}" style="background-image: url('${response}');"
onclick="showDetailsImage(this,'[[Image${images_inserted}]]')"/>`);
                images_inserted_json["[[Image" + (images_inserted) + "]]"] = response;
                hideFullscreenMessages();
                showMessageBottom("Immagine caricata correttamente.");
            } else {
                showMessageBottom('A causa di un problema inaspettato il file non è stato caricato. Riprovare.', -1, 2);
                hideFullscreenMessages();
            }
        },
        error: function () {
            showMessageBottom("Errore inaspettato. Riprovare.", 10, 2);
            hideFullscreenMessages();
        }
    });
}

function uploadImage() {
    $("#upload-image").click();
    hidePopUp();
}

function showDetailsImage(element, title) {
    showPopUp('inserted-images', 'left');
    let url_image = element.style.backgroundImage;
    $("#pop-up-inserted-images").css("background-image", url_image);
    $(".image-title").html(title);
}

function hidePopUp() {
    $(".pop-up").css("display", "none");
}

function okCountry() {
    if ($("#country-selected-pop-up").val() != null) {
        $("#plus-country").val($("#country-selected-pop-up").val());
        $("#plus-country").addClass("width-100-perc");
        $("#plus-country").click(function () {
            $("#country-selected-pop-up").val($("#plus-country").val());
            $("#ok-country").html("Modifica");
            $("#remove-country").css({"display": "inline-block"});
            $("#remove-country-separator").css({"display": "inline-block"});
        });
        let country_tmp = {};
        country_tmp["id"] = $('#country-selected-pop-up :selected').attr("id").replace("country-id-", "");
        country_tmp["value"] = $("#country-selected-pop-up").val();
        country_json = country_tmp;
        hidePopUp();
    }
}

function removeCountry() {
    $("#plus-country").val("+");
    $("#plus-country").off("click");
    $("#ok-country").html("Aggiungi");
    $("#remove-country").css({"display": "none"});
    $("#remove-country-separator").css({"display": "none"});
    $("#plus-country").removeClass("width-100-perc");
    country_json = "";
    hidePopUp();
}

function okCover() {
    if ($("#cover-selected-pop-up").val() != null) {
        $("#plus-cover").val($("#cover-selected-pop-up").val());
        $("#plus-cover").addClass("width-100-perc");
        $("#plus-cover").click(function () {
            $("#cover-selected-pop-up").val($("#plus-cover").val());
            $("#ok-cover").html("Modifica");
            $("#remove-cover").css({"display": "inline-block"});
            $("#remove-cover-separator").css({"display": "inline-block"});
        });
        let cover_tmp = {};
        cover_tmp["url"] = $('#cover-selected-pop-up :selected').attr("id");
        cover_tmp["value"] = $("#cover-selected-pop-up").val();
        cover_json = cover_tmp;
        $("#cover-image").css({"display": "block", "background-image": "url('" + cover_json["url"] + "')"});
        hidePopUp();
    }
}

function removeCover() {
    $("#plus-cover").val("+");
    $("#plus-cover").off("click");
    $("#ok-cover").html("Aggiungi");
    $("#remove-cover").css({"display": "none"});
    $("#remove-cover-separator").css({"display": "none"});
    $("#plus-cover").removeClass("width-100-perc");
    cover_json = "";
    $("#cover-image").css({"display": "none", "background-image": "url('')"});
    hidePopUp();
}

function okIngredient() {
    if ($("#quantity-ingredient-temp").val() != "") {
        let exists = false;
        let ingredient_id = "";

        let string_to_compare = $("#ingredient-selected-pop-up").val();
        if ($("#text-ingredient-temp").val() != "") {
            string_to_compare = $("#text-ingredient-temp").val();
        }
        for (let tmp_index = 1; tmp_index <= ingredient_index && !exists; tmp_index++) {
            if (ingredients_json["ingredient-n" + tmp_index] !== undefined) {
                if (ingredients_json["ingredient-n" + tmp_index].name == string_to_compare) {
                    ingredient_id = "ingredient-n" + tmp_index;
                    exists = true;
                }
            }
        }
        if (!exists) {
            ingredient_index++;
            ingredient_id = "ingredient-n" + ingredient_index;
            $("#all-ingredients").append("<button id='" + ingredient_id + "' class='ingredient button inline-block' onclick='showPopUp(\"ingredients\",\"left\")'>" + $("#ingredient-selected-pop-up").val() + "</button>");
        } else {
            //ingrediente già inserito, quindi modifico quello già esistente
            $("#" + ingredient_id).off("click");
        }
        let ingredient_details_json = {};
        ingredient_details_json["id"] = $('#ingredient-selected-pop-up :selected').attr("id").replace("ingredient-id-", "");
        ingredient_details_json["name"] = $("#ingredient-selected-pop-up").val();
        ingredient_details_json["quantity"] = $("#quantity-ingredient-temp").val() + $("#quantity-ingredient").val();
        ingredients_json[ingredient_id] = ingredient_details_json;
        $("#" + ingredient_id).html(ingredients_json[ingredient_id].name);
        $("#" + ingredient_id).click(function () {
            $("#ingredient-selected-pop-up").val(ingredients_json[ingredient_id].name);
            $("#quantity-ingredient-temp").val($("#quantity-ingredient-temp").val());
            $("#text-ingredient-temp").val(ingredients_json[ingredient_id].name);
            let tmp_el = ingredients_json[ingredient_id].quantity;
            let last_1_char = tmp_el[tmp_el.length - 1];
            let last_2_char = "";
            if (tmp_el.length >= 2) last_2_char = tmp_el[tmp_el.length - 2];
            if (last_1_char == "g") {
                setQuantity(0, "g");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 1));
            } else if (last_2_char == "m" && last_1_char == "l") {
                setQuantity(0, "ml");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 2));
            } else if (last_1_char == "n") {
                setQuantity(0, "n");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 1));
            } else if (last_2_char == "q" && last_1_char == "b") {
                setQuantity(1, "qb");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 2));
            } else {
                setQuantity(2, "c");
                $("#quantity-ingredient-temp").val(tmp_el.substring(0, tmp_el.length - 0));
            }
            $("#ok-ingredient").html("Modifica");
            $("#remove-ingredient").css({"display": "inline-block"});
            $("#remove-ingredient-separator").css({"display": "inline-block"});
            $("#remove-ingredient").off("click");
            $("#remove-ingredient").click(function () {
                removeIngredient(ingredient_id);
            });
        });
        hidePopUp();
    }
}

function removeIngredient(ingredient_id) {
    delete ingredients_json[ingredient_id];
    $("#" + ingredient_id).remove();
    hidePopUp();
}

function resetIngredientsPopUp() {
    $("#ok-ingredient").html("Aggiungi");
    $("#remove-ingredient").css({"display": "none"});
    $("#remove-ingredient-separator").css({"display": "none"});
    $("#ingredient-selected-pop-up").val("");
    $("#quantity-ingredient-temp").val("");
    $("#text-ingredient-temp").val("");
    setQuantity(-1, "");
    $("#quantity-ingredient-temp").css({"display": "none"});
    $("#quantity-container").css({"display": "none"});
}

function showQuantityContainer() {
    $("#quantity-container").css({"display": "block"});
    let type = $('#ingredient-selected-pop-up :selected').attr("class");
    setQuantityContainer(type);
}

function setQuantityContainer(type) {
    let first_button_quantity = $("#ingredients-button0");
    switch (type) {
        case 'g':
            first_button_quantity.attr({"onclick": "setQuantity(0,'g')"});
            first_button_quantity.html("grammi");
            break;

        case 'ml':
            first_button_quantity.attr({"onclick": "setQuantity(0,'ml')"});
            first_button_quantity.html("millilitri");
            break;

        case 'n':
            first_button_quantity.attr({"onclick": "setQuantity(0,'n')"});
            first_button_quantity.html("numero");
            break;

        default:
        //nothing
    }
}

function setQuantity(index, type) {
    //type can be: g (grams), ml (milliliter), n (number), qb ("quanto basta"), c (customised)
    $(".ingredients-button").removeClass("button-selected");
    if (index != -1) $(".ingredients-button").eq(index).addClass("button-selected");

    if (index != -1) $("#quantity-ingredient-temp").css({"display": "block"});
    else $("#quantity-ingredient-temp").css({"display": "none"});
    $("#quantity-ingredient-temp").attr({"type": "text"});
    $("#quantity-ingredient").val("");
    $("#quantity-ingredient-temp").val("");
    $("#quantity-ingredient-temp").off("keydown");
    showQuantityContainer();

    switch (type) {
        case 'g':
            $("#quantity-ingredient").val("g");
            $("#quantity-ingredient-temp").attr({
                "type": "number",
                "step": "0.1",
                "min": "0",
                "placeholder": "Quantità (in grammi)"
            });
            $("#quantity-ingredient-temp").on("keydown", function (e) {
                return allowedNumber(e, true);
            });
            break;

        case 'ml':
            $("#quantity-ingredient").val("ml");
            $("#quantity-ingredient-temp").attr({
                "type": "number",
                "step": "0.05",
                "min": "0",
                "placeholder": "Quantità (in millilitri)"
            });
            $("#quantity-ingredient-temp").on("keydown", function (e) {
                return allowedNumber(e, true);
            });
            break;

        case 'n':
            $("#quantity-ingredient").val("n");
            $("#quantity-ingredient-temp").attr({
                "type": "number",
                "step": "1",
                "min": "0",
                "placeholder": "Quantità (numero)"
            });
            $("#quantity-ingredient-temp").on("keydown", function (e) {
                return allowedNumber(e, false);
            });
            break;

        case 'qb':
            $("#quantity-ingredient").val("");
            $("#quantity-ingredient-temp").val("qb");
            $("#quantity-ingredient-temp").css({"display": "none"});
            break;

        case 'c':
            $("#quantity-ingredient").val("");
            $("#quantity-ingredient-temp").attr({"placeholder": "Quantità (specifica anche l'unità di misura)"});
            $("#quantity-ingredient-temp").on("keydown", function (e) {
                return allowedText(e);
            });
            break;

        default:
        //nothing
    }

    $("#quantity-ingredient-temp").focus();
}

function okTag() {
    if ($("#text-tag").val() != "") {
        let exists = false;
        let tag_id = "";
        if ($("#text-tag-temp").val() != "") {
            for (let tmp_index = 1; tmp_index <= tag_index && !exists; tmp_index++) {
                if (tags_json["tag-n" + tmp_index] !== undefined) {
                    if (tags_json["tag-n" + tmp_index].value == $("#text-tag-temp").val()) {
                        tag_id = "tag-n" + tmp_index;
                        exists = true;
                    }
                }
            }
        }
        if (!exists) {
            tag_index++;
            tag_id = "tag-n" + tag_index;
            $("#all-tags").append("<button id='" + tag_id + "' class='tag button inline-block' onclick='showPopUp(\"tags\",\"right\")'>" + $("#text-tag").val() + "</button>");
        } else {
            //tag già inserito, quindi modifico quello già esistente
            $("#" + tag_id).off("click");
        }
        let tag_details_json = {};
        tag_details_json["value"] = $("#text-tag").val();
        tags_json[tag_id] = tag_details_json;
        $("#" + tag_id).html(tags_json[tag_id].value);
        $("#" + tag_id).click(function () {
            $("#text-tag").val(tags_json[tag_id].value);
            $("#text-tag-temp").val(tags_json[tag_id].value);
            $("#ok-tag").html("Modifica");
            $("#remove-tag").css({"display": "inline-block"});
            $("#remove-tag-separator").css({"display": "inline-block"});
            $("#remove-tag").off("click");
            $("#remove-tag").click(function () {
                removeTag(tag_id);
            });
        });
        hidePopUp();
    }
}

function removeTag(tag_id) {
    delete tags_json[tag_id];
    $("#" + tag_id).remove();
    hidePopUp();
}

function resetTagsPopUp() {
    $("#ok-tag").html("Aggiungi");
    $("#remove-tag").css({"display": "none"});
    $("#remove-tag-separator").css({"display": "none"});
    $("#text-tag").val("");
    $("#text-tag-temp").val("");
}

function loadIngredients() {
    if (ingredients_loaded.length === 0) {
        $.ajax({
            url: 'https://www.saveriomorelli.com/easyrecipes/api/v1/ingredients.php',
            method: 'GET',
            success: function (response) {
                ingredients_loaded = response;

                showLoadedIngredients();
            },
            error: function () {
                showMessageBottom("Errore inaspettato. Riprovare.", 10, 2);
                hidePopUp();
            }
        });
    } else {
        showLoadedIngredients();
    }
}

function showLoadedIngredients() {
    $("#ingredient-selected-pop-up").html("");
    for (let index = 0; index < ingredients_loaded.length; index++) {
        let data = ingredients_loaded[index];
        $("#ingredient-selected-pop-up").append("<option class='" + data.type + "' id='ingredient-id-" + data.id + "'>" + data.name + "</option>");
    }
    $("#ingredient-selected-pop-up").val("");
}

function loadCountries() {
    if (countries_loaded.length === 0) {
        $.ajax({
            url: 'https://www.saveriomorelli.com/easyrecipes/api/v1/countries.php',
            method: 'GET',
            success: function (response) {
                countries_loaded = response;

                showLoadedCountries();
            },
            error: function () {
                showMessageBottom("Errore inaspettato. Riprovare.", 10, 2);
                hidePopUp();
            }
        });
    } else {
        showLoadedCountries();
    }
}

function showLoadedCountries() {
    $("#country-selected-pop-up").html("");
    for (let index = 0; index < countries_loaded.length; index++) {
        let data = countries_loaded[index];
        $("#country-selected-pop-up").append("<option id='country-id-" + data.id + "'>" + data.value + "</option>");
    }
    $("#country-selected-pop-up").val("");
}

function loadCover() {
    $("#cover-selected-pop-up").html("");
    let isEmpty = true;
    for (let index_to_use in images_inserted_json) {
        let url = images_inserted_json[index_to_use];
        $("#cover-selected-pop-up").append("<option id='" + url + "'>" + index_to_use + "</option>");
        isEmpty = false;
    }
    $("#cover-selected-pop-up").val("");

    if (isEmpty) {
        hidePopUp();
        showMessageBottom("Errore: inserire prima almeno una immagine nella sezione \"Immagini caricate\"", 10, 2);
    }
}

function saveDraft(insert_recipe = false) {
    let able_to_insert = true;

    if ($(".title-recipe").val() == "") {
        able_to_insert = false;
    }
    if ($(".preparation-recipe").val() == "") {
        able_to_insert = false;
    }
    if (JSON.stringify(cover_json).length == 2) {
        able_to_insert = false;
    }
    if (JSON.stringify(ingredients_json).length == 2) {
        able_to_insert = false;
    }
    if (JSON.stringify(categories_json).length == 2) {
        able_to_insert = false;
    }
    if (preparation_time_json == "") {
        able_to_insert = false;
    }
    if (difficulty_json == 0) {
        able_to_insert = false;
    }

    /*
    recipe_json["title"] = $(".title-recipe").val();
    recipe_json["preparation"] = $(".preparation-recipe").val();
    recipe_json["cover"] = cover_json;
    recipe_json["ingredients"] = ingredients_json;
    recipe_json["categories"] = categories_json;
    recipe_json["images"] = images_inserted_json;
    recipe_json["difficulty"] = difficulty_json;
    recipe_json["preparation-time"] = preparation_time_json;
    recipe_json["calories"] = calories_json;
    recipe_json["country"] = country_json;
    recipe_json["tags"] = tags_json;

    console.log(JSON.stringify(recipe_json));
    */

    if (able_to_insert) {
        recipe_json["title"] = $(".title-recipe").val();
        recipe_json["preparation"] = $(".preparation-recipe").val();
        recipe_json["cover"] = cover_json;
        recipe_json["ingredients"] = ingredients_json;
        recipe_json["categories"] = categories_json;
        recipe_json["images"] = images_inserted_json;
        recipe_json["difficulty"] = difficulty_json;
        recipe_json["preparation-time"] = preparation_time_json;
        recipe_json["calories"] = calories_json;
        recipe_json["country"] = country_json;
        recipe_json["tags"] = tags_json;
        recipe_json["recipe_id"] = recipe_id;

        alert(JSON.stringify(recipe_json))

        return;

        showFullscreenMessage("pop-up-display-inserting", false);

        let data = JSON.stringify(recipe_json);

        let url = "/old/easyrecipes/api/v1/save-draft-recipe.php";

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            contentType: "application/json",
            dataType: "json",
            processData: false,
            success: function (response) {
                if (response != 0) {
                    if (response["status"] == "OK") {
                        if (insert_recipe) {
                            resetAllFields();
                            showMessageBottom("Ricetta inserita correttamente.");
                        } else {
                            showMessageBottom("Ricetta salvata correttamente.");
                            if (recipe_id == null) {
                                recipe_id = response["recipeid"];
                                window.location.href = "https://www.saveriomorelli.com/easyrecipes/recipes/edit/?id=" + recipe_id;
                            }
                        }
                    } else {
                        if (insert_recipe) {
                            showMessageBottom("Errore: la ricetta non è stata inserita<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                        } else {
                            showMessageBottom("Errore: la ricetta non è stata salvata<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                        }
                    }
                    hideFullscreenMessages();
                } else {
                    if (insert_recipe) {
                        showMessageBottom('A causa di un problema inaspettato la ricetta non è stata inserita. Riprovare.', -1, 2);
                    } else {
                        showMessageBottom('A causa di un problema inaspettato la ricetta non è stata salvata. Riprovare.', -1, 2);
                    }
                    hideFullscreenMessages();
                }
            },
            error: function () {
                showMessageBottom("Errore inaspettato. Riprovare.<br>" + JSON.stringify(recipe_json), -1, 2);
                hideFullscreenMessages();
            }
        });
    } else {
        showMessageBottom("Errore: alcuni campi non sono corretti.<br>Accertati di aver compilato almeno il titolo, il procedimento, l'immagine in evidenza, gli ingredienti, la difficoltà e il tempo di preparazione.", 20, 2);
    }
}

function finishAndInsert() {
    saveDraft(true);
}

function insertImage() {
    $("#preparation-recipe").val($("#preparation-recipe").val() + "" + $(".image-title").html().toString());
    hidePopUp();
    $("#preparation-recipe").focus();
}

function resetAllFields() {
    //reset json and also fields

    //reset json
    images_inserted = 0;
    images_inserted_json = {};

    ingredient_index = 0;
    ingredients_json = {};

    tag_index = 0;
    tags_json = {};

    country_json = {};

    categories_json = {};

    difficulty_json = 0;

    preparation_time_json = "";

    calories_json = "";

    cover_json = "";

    ingredients_loaded = [];
    countries_loaded = [];
    cover_loaded = [];

    recipe_json = {};

    //reset fields
    $("#images-uploaded").html("");
    $("#all-ingredients").html("");
    $("#all-tags").html("");

    setTypes(0);
    setGlutenFree(true, false);
    setDairyFree(true, false);
    setDifficulty(0);

    $("#upload-image").change(function () {
        onChangeUploadImage();
    });

    $("#plus-country").val("+");
    $("#plus-country").removeClass("width-100-perc");

    $("#plus-cover").val("+");
    $("#plus-cover").removeClass("width-100-perc");
    $("#cover-image").css({"display": "none", "background-image": "url('')"});

    $(".title-recipe").val("");
    $(".preparation-recipe").val("");
    $(".preparation-time-value").val("");
    $(".calories-value").val("");
}

/*Support page*/
function showDetailsSupportPage(index) {
    if ($(".div-support-page-details").eq(index).css("display") != "block") {
        $(".div-support-page-details").slideUp("fast");
        $(".div-support-page").css({"background-image": "url('/easyrecipes/images/arrow-down-icon.png')"});
        $(".div-support-page-details").eq(index).slideDown();
        $(".div-support-page").eq(index).css({"background-image": "url('/easyrecipes/images/arrow-up-icon.png')"});
    }
}

/*Manage pages*/
function approveRecipe(id) {
    approveRejectRecipe(id, 3);
}

function rejectRecipe(id) {
    approveRejectRecipe(id, 4);
}

function approveRecipeAlreadyValidated(id) {
    approveRejectRecipe(id, 3, true);
}

function rejectRecipeAlreadyValidated(id) {
    approveRejectRecipe(id, 4, true);
}

function approveRejectRecipe(id, status, already_validated = false) {
    let approve_reject_json = {};
    approve_reject_json["recipe_id"] = id;
    approve_reject_json["status"] = status;

    showFullscreenMessage("pop-up-display-updating", false);

    let data = JSON.stringify(approve_reject_json);

    let url = "/old/easyrecipes/api/v1/approve-reject-recipe.php";

    $.ajax({
        url: url,
        type: 'post',
        data: data,
        contentType: "application/json",
        dataType: "json",
        processData: false,
        success: function (response) {
            if (response != 0) {
                if (response["status"] == "OK") {
                    if (response["code"] == 200) {
                        showMessageBottom("Ricetta approvata correttamente.");
                    } else {
                        showMessageBottom("Ricetta rifiutata correttamente.");
                    }
                    if (!already_validated) {
                        $("#div-id-" + id).fadeOut();
                        setTimeout(function () {
                            $("#div-id-" + id).remove();
                            if ($(".page-section-div").length == 0) {
                                $("#to-approve-clearfix").html("<h1 class='h1-section text-center text-color-light-black padding-10'>Non ci sono altre ricette da approvare (o rifiutare).</h1>");
                            }
                        }, 1000);
                    } else {
                        $("#div-id-" + id + " .button").removeClass("button-selected");
                        if (status == 3) $("#div-id-" + id + " #accept-button").addClass("button-selected");
                        else if (status == 4) $("#div-id-" + id + " #reject-button").addClass("button-selected");
                    }
                } else {
                    if (response["code"] != 504)
                        showMessageBottom("Errore: stato della ricetta non modificato.<br>Dettaglio errore: " + JSON.stringify(response), -1, 2);
                }
                hideFullscreenMessages();
            } else {
                showMessageBottom('A causa di un problema inaspettato non è stato possibile modificare lo stato della ricetta. Riprovare.', -1, 2);
                hideFullscreenMessages();
            }
        },
        error: function () {
            showMessageBottom("Errore inaspettato. Riprovare.<br>" + JSON.stringify(recipe_json), -1, 2);
            hideFullscreenMessages();
        }
    });
}

/*Recipe preview*/
function loadRecipePreview(id, status = -1) {
    //load recipe and set element (by jquery-ajax request)
    $("#pop-up-display-seeing").css("display", "block");
    let url = "/old/easyrecipes/api/v1/get-recipe.php?id=" + id + "&status=" + status;
    $.ajax({
        url: url,
        type: 'get',
        data: "{}",
        contentType: "application/json",
        dataType: "json",
        processData: false,
        success: function (response) {
            if (response != 0) {
                //successful
                let recipe_json = response["description"];
                console.log(JSON.stringify(recipe_json));
                $("#cover-recipe-preview").css("background-image", "url('" + recipe_json["cover"] + "')")
                $("#title-recipe-preview").html(recipe_json["title"]);
                $(document).prop('title', 'Easy Recipes – ' + recipe_json["title"]);

                $("#pop-up-display-seeing").fadeOut();

                $("#preparation-preview-section").html("<p class='preparation-recipe-preview'>" + recipe_json["preparation"] + "</p>");

                for (let image_tmp in recipe_json["images"]) {
                    $("#preparation-preview-section").html($("#preparation-preview-section").html().replaceAll(image_tmp, "</p><div class='image-recipe-preview' style='background-image:url(" + (recipe_json["images"][image_tmp]["url"]) + ");'></div><p class=\"preparation-recipe-preview\">"));
                }
                $("#preparation-preview-section").html($("#preparation-preview-section").html().replaceAll('<p class="preparation-recipe-preview"></p>', ""));
                $("#preparation-preview-section").html($("#preparation-preview-section").html().replaceAll('<p class="preparation-recipe-preview">', '<p class="preparation-recipe-preview">[[*n_paragraph*]]'));
                let n_image = 0;
                while ($("#preparation-preview-section").html().includes("[[*n_paragraph*]]")) {
                    n_image++;
                    $("#preparation-preview-section").html($("#preparation-preview-section").html().replace("[[*n_paragraph*]]", "<div class='paragraph-number-recipe-preview'>" + n_image + "</div>"))
                }

                let tags_counter = 0;
                for (let tag_tmp in recipe_json["tags"]) {
                    $("#tags-recipe-preview").html($("#tags-recipe-preview").html() + "<div class='tag-recipe-preview'>" + recipe_json["tags"][tag_tmp]["value"] + "</div>");
                    tags_counter++;
                }
                if (tags_counter == 0) {
                    $("#tags-recipe-preview").fadeOut();
                }

                let others_counter = 0;
                for (let other_tmp in recipe_json["tags"]) {
                    /*$("#others-recipe-preview").html($("#others-recipe-preview").html() + "<div class='others-recipe-preview'>" + recipe_json["tags"][other_tmp]["value"] + "</div>");
                    others_counter++;*/
                }
                if (others_counter == 0) {
                    //$("#others-recipe-preview").fadeOut();
                }

                let ingredients_counter = 0;
                $("#ingredients-recipe-preview").html("<h1>Ingredienti</h1><ul>");
                for (let ingredient in recipe_json["ingredients"]) {
                    $("#ingredients-recipe-preview").html($("#ingredients-recipe-preview").html() + "<li>" + recipe_json["ingredients"][ingredient]["name"] + ": <span class='quantity-recipe-preview'>" + recipe_json["ingredients"][ingredient]["quantity"] + "</span></li>");
                    ingredients_counter++;
                }
                $("#ingredients-recipe-preview").html($("#ingredients-recipe-preview").html() + "</ul>");
                if (ingredients_counter == 0) {
                    $("#ingredients-recipe-preview").fadeOut();
                }

                let information_counter = 0;
                $("#information-recipe-preview").html("<ul>");

                let difficulty_temp = "";
                switch (recipe_json["difficulty"]) {
                    case 1:
                        difficulty_temp = "";
                        break;
                    case 2:
                        difficulty_temp = "";
                        break;
                    case 3:
                        difficulty_temp = "";
                        break;
                    case 4:
                        difficulty_temp = "";
                        break;
                    case 5:
                        difficulty_temp = "";
                        break;
                    case 6:
                        difficulty_temp = "";
                        break;
                    case 7:
                        difficulty_temp = "";
                        break;
                }
                for (let index = 1; index <= 7; index++) {
                    if (index <= recipe_json["difficulty"]) {
                        difficulty_temp += "&#9679;";
                    } else {
                        difficulty_temp += "&#9675;";
                    }
                }

                let preparation_time_temp = recipe_json["preparation-time"];
                let hours_temp = 0;
                let minutes_temp = 0;

                minutes_temp = (preparation_time_temp % 60);

                let preparation_time_temp2 = preparation_time_temp;
                while (preparation_time_temp2 >= 60) {
                    hours_temp++;
                    preparation_time_temp2 -= 60;
                }

                let time_temp = "";
                if (hours_temp > 0) time_temp += (hours_temp + "h ");
                if (minutes_temp > 0) time_temp += (minutes_temp + "m");

                $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Tempo preparazione: <span class='quantity-recipe-preview'>" + time_temp + "</span></li>");
                information_counter++;

                $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Difficoltà: <span class='quantity-recipe-preview'>" + recipe_json["difficulty"] + "</span>/7 " + difficulty_temp + "</li>");
                information_counter++;

                if (JSON.stringify(recipe_json["calories"]) != "null") {
                    $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Calorie: <span class='quantity-recipe-preview capitalize-text'>~" + recipe_json["calories"] + " kcal</span></li>");
                    information_counter++;
                }
                if (JSON.stringify(recipe_json["country"]).length > 2) {
                    $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Paese di origine: <span class='quantity-recipe-preview capitalize-text'>" + recipe_json["country"]["value"] + "</span></li>");
                    information_counter++;
                }
                if (JSON.stringify(recipe_json["categories"]).length > 2) {
                    let vegetarian = false;
                    let vegan = false;
                    if (recipe_json["categories"]["main-category"] == "not-defined") {
                        vegetarian = false;
                        vegan = false;
                    } else if (recipe_json["categories"]["main-category"] == "vegetarian") {
                        vegetarian = true;
                        vegan = false;
                    } else if (recipe_json["categories"]["main-category"] == "vegan") {
                        vegetarian = true;
                        vegan = true;
                    }
                    if (vegetarian) {
                        $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Vegetariana: <span class='quantity-recipe-preview capitalize-text'>Sì</span></li>");
                    } else {
                        $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Vegetariana: <span class='quantity-recipe-preview capitalize-text'>No</span></li>");
                    }
                    if (vegan) {
                        $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Vegana: <span class='quantity-recipe-preview capitalize-text'>Sì</span></li>");
                    } else {
                        $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Vegana: <span class='quantity-recipe-preview capitalize-text'>No</span></li>");
                    }

                    if (recipe_json["categories"]["gluten-free"]) {
                        $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Senza glutine: <span class='quantity-recipe-preview capitalize-text'>Sì</span></li>");
                    } else {
                        $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Senza glutine: <span class='quantity-recipe-preview capitalize-text'>No</span></li>");
                    }
                    if (recipe_json["categories"]["daity-free"]) {
                        $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Senza latte e derivati: <span class='quantity-recipe-preview capitalize-text'>Sì</span></li>");
                    } else {
                        $("#information-recipe-preview").html($("#information-recipe-preview").html() + "<li>Senza latte e derivati: <span class='quantity-recipe-preview capitalize-text'>No</span></li>");
                    }
                    information_counter++;
                }
                $("#information-recipe-preview").html($("#information-recipe-preview").html() + "</ul>");
                if (information_counter == 0) {
                    $("#information-recipe-preview").fadeOut();
                }
            } else {
                //exeption
            }
        },
        error: function () {
        }
    });
}

/*Notifications*/
function loadNotifications() {
    loadNotificationsActual();//the first time

    let ms = 10 * 1000; //every 10 secs
    setInterval(function () {
        if (!$("#notifications-popup").is(":visible")) {
            loadNotificationsActual();
        }
    }, ms);
}

function loadNotificationsActual(show_all_on_dedicated_page = false) {
    let notifications_json = {};

    let data = JSON.stringify(notifications_json);

    let url = "/old/easyrecipes/api/v1/get-notifications.php";

    $.ajax({
        url: url,
        type: 'get',
        data: data,
        contentType: "application/json",
        dataType: "json",
        processData: false,
        success: function (response) {
            if (response != 0) {
                if (response["status"] == "OK") {
                    //loads data
                    notifications_json_received = response["description"];
                    let unread_notifications = 0;
                    let index_analysed = 0;
                    let element_added = 0;

                    let contain_where_show_notifications = "#notifications-popup";
                    if (show_all_on_dedicated_page) contain_where_show_notifications = "#all-notifications";

                    $(contain_where_show_notifications).html("");
                    for (let i = 0; i < notifications_json_received.length; i++) {
                        let unread_this = false;
                        if (notifications_json_received[i]["status"] == 0) {
                            unread_notifications++;
                            unread_this = true;
                        }

                        if ((element_added < 5 && unread_this) || show_all_on_dedicated_page) {
                            let plus_class = "";
                            let plus_text = "";
                            if (show_all_on_dedicated_page) {
                                if (unread_this) plus_class = "notifications-menu-span-new ";
                                plus_text = "<span class='notification-date'>" + notifications_json_received[i]["date"] + "</span>";
                            }
                            $(contain_where_show_notifications).html($(contain_where_show_notifications).html() + '<span class="' + plus_class + 'notifications-menu-span">' + plus_text + notifications_json_received[i]["message"] + '</span>');
                            element_added++;
                        }
                        index_analysed++;
                    }
                    if (unread_notifications > 0) {
                        $(".notifications-counter").css("display", "block");
                        $(".notifications-counter").html(unread_notifications);
                        if ($(".notifications-counter").css("width") < $(".notifications-counter").css("height")) {
                            $(".notifications-counter").css("width", $(".notifications-counter").css("height"));
                        } else {
                            $(".notifications-counter").css("height", $(".notifications-counter").css("width"));
                        }
                    } else {
                        $(".notifications-counter").css("display", "none");
                        $(".notifications-counter").html('');
                        if (!show_all_on_dedicated_page) $(contain_where_show_notifications).html('<span class="notifications-menu-span">Nessuna nuova notifica da mostrare</span>');
                    }
                    if (!show_all_on_dedicated_page) {
                        $(contain_where_show_notifications).html($(contain_where_show_notifications).html() + '<a href="/old/easyrecipes/profile/notifications/" class="no-link"><button class="notifications-menu-button">Vedi tutte le notifiche</button></a>')
                    }
                } else {

                }
            } else {
            }
        },
        error: function () {
        }
    });
}

function readAllNotifications() {
    readNotifications("/old/easyrecipes/api/v1/read-all-notifications.php");
}

function readLastNotifications() {
    readNotifications("/old/easyrecipes/api/v1/read-last-notifications.php");
}

function readNotifications(url) {
    let notifications_json = {};

    let data = JSON.stringify(notifications_json);

    $.ajax({
        url: url,
        type: 'get',
        data: data,
        contentType: "application/json",
        dataType: "json",
        processData: false,
        success: function (response) {
            if (response != 0) {
                //successful
            } else {
                //exeption
            }
        },
        error: function () {
        }
    });
}