<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$logged = $_SESSION["login"];
$livello = $_SESSION["livello"];
$user = $_SESSION["username"];
$ip = $_SESSION["ip"];
$visualizzazione = $_SESSION["visualizzazione"];
$sede = $_SESSION["sede"];
$permessi = $_SESSION["permessi"];

if ($logged == false) {
    header("location:https://ssl.novadirect.it/Know/index.php?errore=logged");
}
?>

<html>
    <head>
        <title>Import Ore Heracom</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
        <link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">
    </head>
    <body>
        <?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        
        <h1 class="titolo">            
            Import Ore Heracom            
        </h1>
        
         <h2 style="color:green; text-align: center">Heracom</h2>
         
            <form class="accesso" name="importOreHeracom" action="importOreHeracom.php" method="POST" enctype="multipart/form-data">
            <fieldset style="border-color: green">
                <legend class="leggenda">Import Ore Heracom</legend>
                <p >HEADER: Operatore | data | Secondi</p>
                <br>
                <input type="file" name="importOreHeracom" accept=".csv" ><br/><br/>
                <input type="submit" value="import Ore Heracom" name="OreHeracom">
            </fieldset>
        </form>
             </body>
</html>