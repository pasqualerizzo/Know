<?php

header('Access-Control-Allow-Origin: 51.195.30.121');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

$comodity = $_POST["comodity"];
$descrizione = $_POST["descrizione"];

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj = new Connessione();
$conn = $obj->apriConnessione();

$risposta = "KO";

if ($comodity == "GAS") {
    $query = "SELECT fase FROM `plenitudeStatoGas` where descrizione='$descrizione'";
} elseif ($comodity == "LUCE") {
    $query = "SELECT fase FROM `plenitudeStatoLuce` where descrizione='$descrizione'";
}

$risultato = $conn->query($query);
if (($riga = $risultato->fetch_array())) {
    $risposta = $riga[0];
}

echo $risposta;
?>
