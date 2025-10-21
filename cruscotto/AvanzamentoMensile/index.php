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
    exit();
}

// Auto-rileva ambiente (locale MAMP o produzione)
$BASE_PATH = (strpos($_SERVER['DOCUMENT_ROOT'], 'MAMP') !== false) 
    ? '/Applications/MAMP/htdocs/Know' 
    : '/var/www/html/Know';

//include $BASE_PATH . "/cruscotto/js/funzioniCruscotto.php";
include $BASE_PATH . "/cruscotto/js/funzioniCruscottopv.php";
require $BASE_PATH . "/connessione/connessione.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$queryLimiteDate = "SELECT min(giorno), max(giorno) FROM `stringheTotale`";
$risultatoQueryLimiteDate = $conn19->query($queryLimiteDate);
$rigaLimiteDate = $risultatoQueryLimiteDate->fetch_array();
$dataMinima = $rigaLimiteDate[0];
$dataMassima = $rigaLimiteDate[1];
$datacorrente = date("Y-m-d");
$giornoDellaSettimana = date("N");
$dataDefault = ($giornoDellaSettimana == 1) ? date("Y-m-d", strtotime("-2 days")) : date("Y-m-d", strtotime("-1 days"));

$mese = "";
?>

<html>
    <head>
        <title>Metrics: Avanzamento Mensile</title>
        <link href="../../css/tabella.css" rel="stylesheet">
        <link href="../../css/sidebar.css" rel="stylesheet">
        <link href="grafici.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    </head>
    <body style="font-family: poppins;" <!--onload="aggiornaMandatoMese();-->">
        <header>
            <h1>Metrics: Avanzamento Mensile</h1>
        </header>
<?php include $BASE_PATH . '/elementi/sidebar.html'; ?>
        <div>
            <input type="hidden" id="permessi" value="<?= $visualizzazione ?>">
        </div>
        <div class="container2">
            <form action="">
                
                <div style="display: flex; flex-wrap: nowrap; align-content: flex-start; justify-content: center; align-items: center;">
                    <div style="display: flex; width: 25%; align-content: space-between; align-items: stretch; justify-content: space-between; flex-direction: column; flex-wrap: nowrap;">
                        <label for="dataInizio">Mese:
                            <input type="month" id="mese" name="mese"  onchange="aggiornaMandatoMese()"> 
                        </label>
                        <br>
                        
                    </div>

                    <div style="display: flex; width: 25%; align-content: center; align-items: center; justify-content: space-evenly;">
                        <label>Mandato:</label>
                        <select size="4" multiple id="mandato" onchange="aggiornaSedeMese()"></select>                        
                    </div>

                    <div style="display: flex; width: 25%; align-content: center; align-items: center; justify-content: space-evenly;">
                        <label>Sedi:</label>
                        <div style="display: flex; flex-direction: column;">
                            <div>
                            <button type="button" onclick="selezionaTutteSedi()">Seleziona tutte</button>
                            <select size="4" multiple id="sede"></select>   
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        
                       <button type="button" onclick="creaTabellaAvanzamentoWeek() ; creaTabellaAvanzamentoWeekSede()">Avanzamento Week</button>                       
                                                
                    </div>
                    
                       <div style="display: flex; flex-direction: column;">
                        
                       <button type="button" onclick="creaTabellaAvanzamentoMesi() ">Avanzamento Annuale</button>                       
                                                
                    </div>
<!--                    <div style="display: flex; flex-direction: column;">
                        
                       <button type="button" onclick="creaTabellaAvanzamentoWeekSede()">Avanzamento Sede</button>                       
                                                
                    </div>-->
                </div>
            </form>
        </div>
        <div>
            <a id="PdpInterno" style="display: flex; flex-wrap: nowrap; align-items: flex-start; flex-direction: column; align-content: center; justify-content: space-between;"></a>
        </div>
        <br>
        <div>
            <a id= "week" style="display: flex; flex-wrap: nowrap; align-items: flex-start; flex-direction: row; align-content: center; justify-content: space-between;"></a>
        </div>
        <br>
        <div>
            <a id="weeksede" style="display: flex; flex-wrap: nowrap; align-items: flex-start; flex-direction: row; align-content: center; justify-content: space-between;"></a>
        </div>
            <br>
        <div>
            <a id="weekgroup" style="display: flex; flex-wrap: nowrap; align-items: flex-start; flex-direction: row; align-content: center; justify-content: space-between;"></a>
        </div>        
         <div >
            <a id="week" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: row;align-content: center;justify-content: space-between;"></a>
        </div>
         <div >
            <a id="weeksede" style="display:flex;flex-wrap: nowrap;align-items: flex-start;flex-direction: row;align-content: center;justify-content: space-between;"></a>
        </div>
            <div>
            <a id="weekgroup" style="display: flex; flex-wrap: nowrap; align-items: flex-start; flex-direction: row; align-content: center; justify-content: space-between;"></a>
        </div> 
        
        <!-- Sezione Grafici -->
        <div id="sezione-grafici">
            <!-- Container per grafici settimanali -->
            <div class="grafici-container" id="grafici-week" style="display:none;">
                <h2>Grafici Avanzamento Settimanale</h2>
                <div class="grafico-row">
                    <div class="grafico-box">
                        <canvas id="chartWeekOre"></canvas>
                    </div>
                    <div class="grafico-box">
                        <canvas id="chartWeekResa"></canvas>
                    </div>
                </div>
                
                
            </div>

            <!-- Container per grafici annuali -->
            <div class="grafici-container" id="grafici-annuale" style="display:none;">
                <h2>Grafici Avanzamento Annuale</h2>
                
                <div class="grafico-row">
                    <div class="grafico-box">
                        <canvas id="chartAnnualeFatturato"></canvas>
                    </div>
                    <div class="grafico-box">
                        <canvas id="chartAnnualePezzi"></canvas>
                    </div>
                </div>
                
                <div class="grafico-row">
                    <div class="grafico-box">
                        <canvas id="chartAnnualeOre"></canvas>
                    </div>
                    <div class="grafico-box">
                        <canvas id="chartAnnualeResa"></canvas>
                    </div>
                </div>
                
                
            </div>
        </div>
    </body>
    <script>
<?php include $BASE_PATH . "/cruscotto/js/funzioniCruscottopv.js"; ?>
    </script>
    <script src="grafici.js"></script>
    <script src="integrazione-grafici.js"></script>
</html>
