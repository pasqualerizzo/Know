<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$mese = filter_input(INPUT_POST, "mese");
$dataMinore=$mese."-01";

$dataMaggiore=date('Y-m-d',strtotime("last day of ".$mese));

$mandato = json_decode($_POST["mandato"], true);
$queryMandato = "";
$elencoMandato = [];

if (empty($mandato) || !isset($mandato[0])) {
    echo json_encode([]);
    exit;
}

$idMandato = $mandato[0];

switch ($idMandato) {
    case "Plenitude":
        $queryCrm = "SELECT sede FROM plenitude 
                     INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id
                     WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore'
                     AND sede <> '' 
                     AND statoPda NOT IN ('bozza','annullata','pratica doppia','In attesa Sblocco')
                     AND comodity <> 'Polizza' 
                     GROUP BY sede";
        break;

    case "Green Network":
        $queryCrm = "SELECT sede FROM green 
                     INNER JOIN aggiuntaGreen ON green.id = aggiuntaGreen.id
                     WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore'
                     AND sede <> '' 
                     AND statoPda NOT IN ('bozza','annullata','pratica doppia')
                     GROUP BY sede";
        break;

    case "Vivigas Energia":
        $queryCrm = "SELECT sede FROM vivigas 
                     INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
                     WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore'
                     AND sede <> '' 
                     AND statoPda NOT IN ('bozza','annullata','pratica doppia')
                     GROUP BY sede";
        break;

    case "Vodafone":
        $queryCrm = "SELECT sede FROM vodafone 
                     INNER JOIN aggiuntaVodafone ON vodafone.id = aggiuntaVodafone.id
                     WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore'
                     AND sede <> '' 
                     AND statoPda NOT IN ('bozza','annullata','pratica doppia')
                     GROUP BY sede";
        break;

    case "enel_out":
        $queryCrm = "SELECT sede FROM enelOut 
                     INNER JOIN aggiuntaEnelOut ON enelOut.id = aggiuntaEnelOut.id
                     WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore'
                     AND sede <> '' 
                     AND statoPda NOT IN ('bozza','annullata','pratica doppia')
                     GROUP BY sede";
        break;

    case "Iren":
        $queryCrm = "SELECT sede FROM iren 
                     INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
                     WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore'
                     AND sede <> '' 
                     AND statoPda NOT IN ('bozza','annullata','pratica doppia','In attesa Sblocco')
                     AND comodity <> 'Polizza' 
                     GROUP BY sede";
        break;

    case "EnelIn":
        $queryCrm = "SELECT sede FROM enelIn 
                     INNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id
                     WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore'
                     AND sede <> '' 
                     AND statoPda NOT IN ('bozza','annullata','pratica doppia','In attesa Sblocco')
                     AND comodity <> 'Polizza' 
                     GROUP BY sede";
        break;

    case "Heracom":
        $queryCrm = "SELECT sede FROM heracom 
                     INNER JOIN aggiuntaHeracom ON heracom.id = aggiuntaHeracom.id
                     WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore'
                     AND sede <> '' 
                     AND statoPda NOT IN ('bozza','annullata','pratica doppia','In attesa Sblocco')
                     AND comodity <> 'Polizza' 
                     GROUP BY sede";
        break;

    case "Tim":
    case "tim":
        echo json_encode([]);
        exit;

    default:
        echo json_encode([]);
        exit;
}

$risultato = $conn19->query($queryCrm);
while ($riga = $risultato->fetch_array()) {
    $elencoMandato[] = $riga[0];
}

echo json_encode($elencoMandato);
exit;