<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
require "/Applications/MAMP/htdocs/Know/siscall/funzioni/funzioni.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniDate.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniSiscall2.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";

$obj = new connessioneSiscallLead();
$connS2 = $obj->apriConnessioneSiscallLead();

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();
/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$provenienza = "siscall1";
$dataControllo = date('Y-m-01', strtotime('-4 months'));

echo $dataImport . "<br>";

truncateStringheTotali($conn19);

caricamentoStringheTotaleSiscall($conn19);
caricamentoStringheTotaleSiscall2($conn19);
caricamentoStringheTotaleSiscall4($conn19);
caricamentoStringheTotaleSiscall4TC($conn19);
caricamentoStringheTotaleSiscallDigital($conn19);
caricamentoStringheTotaleSiscallGT($conn19);
caricamentoStringheTotaleSiscallLead($conn19);
caricamentoImportOreHeracom($conn19);


truncateFormazioneTemporanea($conn19);
caricamentoFormazioneTemporanea($conn19, $dataControllo);

truncateFormazioneTotale($conn19);
caricamentoFormazioneTotale($conn19);

truncatePagamentoGiorno($conn19);
caricamentoPagamentoGiorno($conn19);

truncatePagamentoMese($conn19);
caricamentoPagamentoMese($conn19, $connS2);

$dataImport = date('Y-m-d H:i:s');
echo $dataImport . "<br>";

$obj->chiudiConnessioneSiscallLead();
$obj19->chiudiConnessione();