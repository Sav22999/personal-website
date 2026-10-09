<?php
?>

<div class="pop-up" id="pop-up-cover">
    <span id="span-cover-pop-up"></span>
    <select class="button width-100-perc capitalize-text" id="cover-selected-pop-up">
        <!--loaded in js-ajax-->
    </select>
    <br>
    <button onclick="removeCover()" class="button no-margin red-button" id="remove-cover">Elimina</button>
    <span class="no-margin no-padding v-separator-after" id="remove-cover-separator"></span>
    <button onclick="hidePopUp()" class="button no-margin">Annulla</button>
    <button onclick="okCover()" class="button no-margin" id="ok-country">Aggiungi</button>
</div>

<div class="pop-up" id="pop-up-origin-country">
    <span id="span-country-pop-up"></span>
    <select class="button width-100-perc capitalize-text" id="country-selected-pop-up">
        <!--loaded in js-ajax-->
    </select>
    <br>
    <button onclick="removeCountry()" class="button no-margin red-button" id="remove-country">Elimina</button>
    <span class="no-margin no-padding v-separator-after" id="remove-country-separator"></span>
    <button onclick="hidePopUp()" class="button no-margin">Annulla</button>
    <button onclick="okCountry()" class="button no-margin" id="ok-country">Aggiungi</button>
</div>

<div class="pop-up" id="pop-up-ingredients">
    <span id="span-ingredient-pop-up"></span>
    <select class="button width-100-perc capitalize-text" id="ingredient-selected-pop-up"
            onchange="setQuantity(-1, '');">
        <!--loaded in js-ajax-->
    </select>
    <input type="text" value="" class="textbox width-100-perc no-margin-bottom margin-top-10 hidden"
           id="text-ingredient-temp" disabled>
    <input class="textbox width-100-perc hidden" id="quantity-ingredient" placeholder="" disabled/>
    <span id="quantity-container">
        <button class="button ingredients-button button-start no-margin no-box-shadow first-element-choosing"
                id="ingredients-button0"
                onclick="setQuantity(0,'g')">grammi
        </button>
        <button class="button ingredients-button margin-left-minus-5 no-margin no-box-shadow button-middle"
                onclick="setQuantity(1,'qb')">
            q.b.
        </button>
        <button class="button ingredients-button margin-left-minus-5 button-end no-margin no-box-shadow"
                onclick="setQuantity(2,'c')">personalizzato
        </button>
    </span>
    <input type="text" value="" placeholder="" class="textbox width-100-perc no-margin-bottom margin-top-10"
           id="quantity-ingredient-temp" oncontextmenu="return false;">
    <div class="margin-top-10"></div>
    <button class="button no-margin red-button" id="remove-ingredient">Rimuovi</button>
    <span class="no-margin no-padding v-separator-after" id="remove-ingredient-separator"></span>
    <button onclick="hidePopUp()" class="button no-margin">Annulla</button>
    <button onclick="okIngredient()" class="button no-margin" id="ok-ingredient">Aggiungi</button>
</div>

<div class="pop-up" id="pop-up-inserted-images">
    <label class="image-title"></label>
    <input type="button" value="×" class="button close-pop-up red-button" onclick="hidePopUp()"/>
    <button onclick="insertImage();" class="button insert-image">Inserisci questa immagine</button>
</div>

<div class="pop-up" id="pop-up-tags">
    <span id="span-tag-pop-up"></span>
    <input type="text" value="" class="textbox width-100-perc no-margin-bottom margin-top-10 hidden"
           id="text-tag-temp" disabled>
    <input type="text" value="" placeholder="Tag" class="textbox width-100-perc no-margin-bottom margin-top-10"
           id="text-tag">
    <div class="margin-top-10"></div>
    <button class="button no-margin red-button" id="remove-tag">Rimuovi</button>
    <span class="no-margin no-padding v-separator-after" id="remove-tag-separator"></span>
    <button onclick="hidePopUp()" class="button no-margin">Annulla</button>
    <button onclick="okTag()" class="button no-margin" id="ok-tag">Aggiungi</button>
</div>