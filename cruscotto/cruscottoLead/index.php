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
    <title>Metrics: Cruscotto Lead</title>
    <link href="../../css/tabella.css" rel="stylesheet">
    <link href="../../css/sidebar.css" rel="stylesheet">

    <link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">

</head>
<body style="font-family: poppins;" onload="permessi();aggiornaAgenzia(),aggiornaCategoria()">
<header>
    <h1>Metrics: Cruscotto Lead</h1>
</header>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
<div>
    <input type="hidden" id="permessi" value=<?= $visualizzazione ?>>
</div>
<div class="container2">
    <form action="">
        <div style="display: flex;flex-wrap: nowrap;align-content: flex-start;justify-content: center;align-items: center;">

            <div style="display: flex;width: 25%;align-content: space-between;align-items: stretch;justify-content: space-between;flex-direction: column;flex-wrap: nowrap;">
                <label for="dataInizio">Data Inizio:
                    <input type="date" id="dataInizio" name="dataInizio" max="<?= $dataMassima ?>"
                           min="<?= $dataMinima ?>" value="<?= $dataDefault ?>"
                           onchange="aggiornaAgenzia(),aggiornaCategoria()">
                </label>
                <br>
                <label for="dataFine">Data Fine:
                    <input type="date" id="dataFine" name="datafine" max="<?= $dataMassima ?>" min="<?= $dataMinima ?>"
                           value="<?= $dataDefault ?>" onchange="aggiornaAgenzia(),aggiornaCategoria()">
                </label>
            </div>

            <div style="display: flex;width: 25%;align-content: center;align-items: center;justify-content: space-evenly;">
                <label>Agenzia:</label>
                <select size="4" multiple id="agenzia" onchange="aggiornaCategoria()">

                </select>
            </div>

            <div style="display: flex;width: 25%;align-content: center;align-items: center;justify-content: space-evenly;">
                <label>Categoria:</label>
                <select size="4" multiple id="categoria">

                </select>
            </div>

            <!--                    <div style="display: flex;width: 20%;align-content: center;align-items: center;justify-content: space-evenly;">
                                    <label>Valore Costo1:</label>
                                    <input  type="number" id="valoreCosto" min="0.1" max="50.0" step="0.1" value="12.5">
                                </div>-->


            <div style="display: flex;flex-direction: column;">
                <button style="background-color: aquamarine; padding:10px 20px; cursor:pointer; border:none; border-radius: 4px; font-size: larger;"
                        type="button"
                        onclick="creaTabellaLead();creaTabellaLeadCampagna();creaTabellaLeadProduzione()">Lead
                </button>
            </div>
        </div>
    </form>
</div>
<div>
    <a id="tabella"
       style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: column;align-content: center;justify-content: space-between;"></a>
</div>
<br>
<div>
    <a id="PdpInterno"
       style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: row;align-content: center;justify-content: space-between;"></a>
</div>
<br>
<div>
    <a id="PdpEsterno"
       style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: row;align-content: center;justify-content: space-between;"></a>
</div>
</body>
<script>
    <?php
    include "/Applications/MAMP/htdocs/Know/cruscotto/js/funzioniCruscotto.js";
    ?>

</script>
</html>