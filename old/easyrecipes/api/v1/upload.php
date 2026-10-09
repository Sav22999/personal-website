<?php
session_start();
session_regenerate_id();
/* Getting file name */
$filename = $_FILES['file']['name'];

/* Location */
$uploadOk = 1;
$imageFileType = pathinfo($filename, PATHINFO_EXTENSION);
$location = "/web/htdocs/www.saveriomorelli.com/home/easyrecipes/images/uploads/" . date("YmdHis") . "" . session_id() . "." . $imageFileType;

/* Check the file is a "real" image or a fake one */
if (isset($_POST["upload"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
}

/* Check file size */
if ($_FILES["file"]["size"] > (10 * 1024 * 1024)) {
    //10 MB (10B, 10*1024=10KB, 10*1024*1024=10MB)
    $uploadOk = 0;
}

/* Valid Extensions */
$valid_extensions = array("jpg", "jpeg", "png", "bmp", "ico", "tif", "gif");
/* Check file extension */
if (!in_array(strtolower($imageFileType), $valid_extensions)) {
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    echo 0;//failed
} else {
    /* Upload file */
    if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
        echo $location;//success
    } else {
        echo 0;//failed
    }
}
?>