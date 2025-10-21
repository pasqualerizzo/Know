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

$queryLimiteDate = "SELECT min(giorno),max(giorno) FROM `stringheTotale`";
$risultatoQueryLimiteDate = $conn19->query($queryLimiteDate);
$rigaLimiteDate = $risultatoQueryLimiteDate->fetch_array();
$dataMinima = $rigaLimiteDate[0];
$dataMassima = $rigaLimiteDate[1];
$datacorrente = date("Y-m-d");
$giornoDellaSettimana = date("N");
if ($giornoDellaSettimana == 1) {
    $dataDefault = date("Y-m-d", strtotime("-2 days"));
} else {
    $dataDefault = date("Y-m-d", strtotime("-1 days"));
}
//echo $dataDefault;

$mese = "";
?>

<html>
    <head>
        <title>Metrics: Cruscotto Digitale Operatore</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    </head>
    <body style="font-family: poppins;" onload="permessi()">
        <header>
            <h1>Metrics: Cruscotto Digitale Operatore</h1>
        </header>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <div class="container2">
            <form action="">
                <div style="display: flex;flex-wrap: nowrap;align-content: flex-start;justify-content: center;align-items: center;">

                    <div style="display: flex;width: 25%;align-content: space-between;align-items: stretch;justify-content: space-between;flex-direction: column;flex-wrap: nowrap;">
                        
                        
                    </div>

                    
                    <div style="display: flex;flex-direction: column;">                                         
                        <button type="button" onclick="creaTabellaOperatoreMese();pdpInternoVuoto();pdpEsternoVuoto()">Operatori</button>           
                    </div>
                </div>
            </form>
        </div>
        <div>
            <a id="tabella" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: column;align-content: center;justify-content: space-between;"></a>
        </div>
        <br>
        <div >
            <a id="PdpInterno" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: row;align-content: center;justify-content: space-between;"></a>
        </div>
        <br>
        <div >
            <a id="PdpEsterno" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: row;align-content: center;justify-content: space-between;"></a>
        </div>
    </body>
    <script type="text/javascript" src="sortabletable.js"></script>
    <script>
<?php
include "/Applications/MAMP/htdocs/Know/cruscotto/js/funzioniCruscotto.js";
?>
    </script>
    <script>
        var st1 = new SortableTable(document.getElementById("table-1"),
                ["String", "String", "String", "String", "Number", "Number", "Number", "Number", "Number", "Number", "Number", "Number", "String", "Number", "Number"]);

    </script>
</html>

