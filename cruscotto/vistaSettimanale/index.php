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
$datacorrente = date("Y-m");

$meseMinima = date("m-Y", strtotime($dataMinima));
$meseMassima = date("m-Y", strtotime($dataMassima));


?>

<html>
    <head>
        <title>Metrics: Vista Settimanale</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">

        <link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">
    </head>
    <body style="font-family: poppins;" onload="permessi()">
        <header>
            <h1>Metrics: Vista Settimanale</h1>
        </header>
<?php include '/Applications/MAMP/htdocs/Know/elementi/sidebar.html' ?>
        <div>
            <input type="hidden"  id="permessi" value=<?= $visualizzazione ?>>
        </div>
        <div class="container2">
            <form action="">
                <div style="display: flex;flex-wrap: nowrap;align-content: flex-start;justify-content: space-around;align-items: center;">

                    <div style="display: flex;align-content: space-between;align-items: center;justify-content: space-around;flex-direction: column;flex-wrap: nowrap;">
                        <label for="dataInizio">Data Inizio:
                            <input type="month" id="meseRiferimento" name="meseRiferimento" max="<?= $meseMassima ?>"  min="<?= $meseMinima ?>" value="<?= $datacorrente ?>"  > 
                        </label>
                       
                    </div>
                    
                    
                    <div style="display: flex;align-content: center;align-items: center;justify-content: space-evenly;">                                         
                        <button type="button" onclick="creaVistaMandato()">Aggiorna KPI</button>           
                    </div>
                    
                    
                    

                   
                    
                </div>
        </div>
    </form>
</div>
<div>
    <a id="tabella" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: column;align-content: center;justify-content: space-between;"></a>
</div>
<br>

</body>
<script>
<?php
include "/Applications/MAMP/htdocs/Know/cruscotto/js/funzioniCruscotto.js";
?>
</script>
</html>
