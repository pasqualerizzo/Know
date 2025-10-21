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
        
    </head>
    <body style="font-family: poppins;" onload="aggiornaSedeData();permessi();aggiornaMandato()">
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
                        <label for="dataInizio">Data Inizio:
                            <input type="date" id="dataInizio" name="dataInizio" max="<?= $dataMassima ?>"  min="<?= $dataMinima ?>" value="<?= $dataDefault ?>" onchange="aggiornaSedeData();aggiornaMandato()" > 
                        </label>
                        <br>
                        <label for="dataFine">Data  Fine: 
                            <input type="date" id="dataFine" name="datafine" max="<?= $dataMassima ?>"  min="<?= $dataMinima ?>" value="<?= $dataDefault ?>" onchange="aggiornaSedeData();aggiornaMandato()">
                        </label>
                    </div>

                                        <div style="display: flex;width: 25%;align-content: center;align-items: center;justify-content: space-evenly;">
                        <label>Mandato:</label>
                        <select  size="4" multiple id="mandato" onchange="aggiornaSedeInvertito()">

                        </select>                        
                    </div>


                    <div style="display: flex;width: 25%;align-content: center;align-items: center;justify-content: space-evenly;">
                        <label>Sedi:

                        </label>
                        <div style="display: flex;flex-direction: column;">
                            <div>
                            <button type="button" onclick="selezionaTutteSedi()">Seleziona tutte</button>
                            <select  size="4" multiple id="sede" >
                            </select>   
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex;flex-direction: column;">                                         
                        <button type="button" onclick="creaTabellaOperatore();pdpInternoVuoto();pdpEsternoVuoto()">Operatori</button>           
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

