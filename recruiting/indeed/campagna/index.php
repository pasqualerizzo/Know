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


include "/Applications/MAMP/htdocs/Know/cruscotto/js/funzioniCruscotto.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();
?>

<html>
    <head>
        <title>Metrics: Recruiting campagne</title>
        <link href="https://ssl.novadirect.it/Know/css/tabella.css" rel="stylesheet">
        <link href="https://ssl.novadirect.it/Know/css/sidebar.css" rel="stylesheet">
        <link href="https://ssl.novadirect.it/Know/css/style_1.css" rel="stylesheet">
        
        <link rel="icon" href="https://ssl.novadirect.it/Know/images/logo-metrics.png" type="image/x-icon">
        
    </head>
    <body style="font-family: poppins;" onload="permessi()">
        <header>
            <h1>Recruiting:<br>Import Indeed Campagne</h1>
        </header>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <div class="container2">
                  <form class="tabsRicercaForm" action="importCsv/importCsvCampagna.php" method="POST" enctype="multipart/form-data">
                      <fieldset>
                          <legend>Import</legend>
                          <label>Data del File: <input type="date" name="dataFile"></label>
                        <label>Importa file CSV Esportato da  INDEED:  </label>     
                        <input type="file" name="importCSVOperatori" accept=".csv" >     
                        <input type="submit" value="import CSV" name="importCSv">  
                      </fieldset>
             </form>  
        </div>
        
    </body>
    <script>
<?php
include "/Applications/MAMP/htdocs/Know/cruscotto/js/funzioniCruscotto.js";
?>
    </script>
</html>