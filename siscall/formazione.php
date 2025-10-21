<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/siscall/funzioni/funzioni.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

truncateStringheTotali($conn19);

caricamentoStringheTotaleSiscall($conn19);
caricamentoStringheTotaleSiscall2($conn19);
caricamentoStringheTotaleSiscall4($conn19);
caricamentoStringheTotaleSiscall4TC($conn19);
caricamentoStringheTotaleSiscallDigital($conn19);
caricamentoStringheTotaleSiscallGT($conn19);


$dataImport = date('Y-m-d H:i:s');
$dataControllo = date('Y-m-01', strtotime('-4 months'));

truncateFormazioneTemporanea($conn19);
caricamentoFormazioneTemporanea($conn19, $dataControllo);

truncateFormazioneTotale($conn19);
caricamentoFormazioneTotale($conn19);

$obj19->chiudiConnessione();



