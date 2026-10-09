function generate_redirect_link_savpdfviewer() {

    let link = getRandomLetter(true, false) + getRandomInteger().toString() + getRandomLetter(true, true) + getRandomLetter(false, true) + getRandomLetter(true, true) + getRandomInteger().toString() + getRandomLetter(true, true);

    let url = "https://savpdfviewer.com/redirect/" + link + "/old/index.php";

    //TODO: doesn't work the checking!
    $.ajax({
        type: 'get',
        crossOrigin: true,
        crossDomain: true,
        url: url,
        success: function () {
            // page exists
            generate_redirect_link_savpdfviewer();
            console.log("Already exists, call again the page: " + link);
        },
        error: function () {
            // page does not exist
            let textCode = document.getElementById("code-text");
            textCode.value = link;
            console.log("Not exists: " + link);
        }
    });

}

function getRandomInteger() {
    let start = 48; //0
    let end = 57; //9
    let allNumbers = getBetweenIntervals(start, end);
    let number = allNumbers[getRandomInt(allNumbers.length)];
    return number;
}

function getRandomLetter(uppercase = true, lowercase = true) {
    let allLetters = [];
    if (uppercase) allLetters = allLetters.concat(getUpperCaseLetters());
    if (lowercase) allLetters = allLetters.concat(getLowerCaseLetters());
    let letter = allLetters[getRandomInt(allLetters.length)];
    return letter;
}

function getUpperCaseLetters() {
    let start = 65; //A
    let end = 90; //Z
    return getBetweenIntervals(start, end);
}

function getLowerCaseLetters() {
    let start = 97; //a
    let end = 122; //z
    return getBetweenIntervals(start, end);
}

function getBetweenIntervals(start, end) {
    toReturn = []
    let tmp = start
    while (tmp <= end) {
        toReturn.push(String.fromCharCode(tmp));
        tmp++;
    }
    return toReturn;
}

function getRandomInt(max) {
    return Math.floor(Math.random() * max);
}