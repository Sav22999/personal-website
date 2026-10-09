<html>
<head>
    <title>Saverio Morelli</title>
    <?php
    include_once($_SERVER["DOCUMENT_ROOT"] . "/old/download/header.php");
    include_once($_SERVER["DOCUMENT_ROOT"] . "/old/include/variables.php");
    ?>
</head>
<body>
<?php
session_start();
if (variables_permission_yes_or_not(8)) {
    $list_files_folders = "";
    $file_path = "/web/htdocs/www.saveriomorelli.com/home/download/TotalCyc/list_of_files_to_download.totalcyc";

    $root_directory = "/web/htdocs/www.saveriomorelli.com/home/download/TotalCyc/";

    $directories = glob($root_directory . '*', GLOB_ONLYDIR);
    $files = glob($root_directory . '*.*');

    echo "Questo script genera automaticamente il file 'list_of_files_to_download.totalcyc' del progetto TotalCyc<br/>";
    echo "Numero cartelle: " . sizeof($directories) . "<br/>";
    echo "<br>Contenuto scritto su file:<hr>";

    // file nella cartella principale
    for ($i = 0; $i < sizeof($files); $i++) {
        $name_file = str_replace($root_directory, "", $files[$i]);
        if ($name_file != "list_of_files_to_download.totalcyc") {
            // è possibile escludere determinati file
            if ($i != 0) $list_files_folders .= "\n" . $name_file;
            else $list_files_folders .= $name_file;
        }
    }

    // file nelle sotto-cartelle
    for ($i = 0; $i < sizeof($directories); $i++) {
        $name_directory = str_replace($root_directory, "", $directories[$i]);
        if ($name_directory != "") {
            // è possibile escludere determinate cartelle
            $files = glob($root_directory . $name_directory . "/" . '*.*');
            for ($i = 0; $i < sizeof($files); $i++) {
                $name_file = str_replace($root_directory, "", $files[$i]);
                $list_files_folders .= "\n" . $name_file;
            }
        }
    }

    $file = fopen($file_path, "w");
    fwrite($file, $list_files_folders);
    fclose($file);

    echo str_replace("\n", "<br>", $list_files_folders);
} else {
    echo "<center><h1>Effettua l'accesso come admin per poter accedere al file.</h1></center>";
    ?>
    <center>
        <a href="/old/admin/index.php">Vai in Area riservata</a>
    </center>
    <?php
}
?>
</body>
</html>