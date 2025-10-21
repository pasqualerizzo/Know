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

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");

$query = "SELECT idMandato FROM stringheTotale 
          WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' 
          AND livello = 1 AND idMandato = 'TIM' AND idMandato = 'Tim Bianco' 
          GROUP BY idMandato";

$risultato = $conn19->query($query);

$elencoMandato = [];
while ($riga = $risultato->fetch_array()) {
    $elencoMandato[] = $riga[0];
}

// Aggiunte fisse
$aggiunte = ["Tim"];
$elencoMandato = array_merge($elencoMandato, $aggiunte);

echo json_encode($elencoMandato);
exit;