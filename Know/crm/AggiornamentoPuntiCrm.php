<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";



$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();
/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$provenienza = "Vivigas";

$queryVivigas = "SELECT pratica,totalePesoLordo,pesoTotalePagato FROM `vivigas` inner join aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id where mese>='2023-02-01'";
//echo $queryVivigas;
$risultatoVivigas = $conn19->query($queryVivigas);
while ($rigaVivigas = $risultatoVivigas->fetch_array()) {
    $pratica = $rigaVivigas[0];
    $pesoTotaleLordo = $rigaVivigas[1];
    $pesoTotalePagato = $rigaVivigas[2];
            $queryUpdatePesi = "UPDATE `vtiger_vivigascf` SET cf_3761='$pesoTotaleLordo',cf_3765='$pesoTotalePagato' WHERE vivigasid='$pratica'";
            //echo $queryUpdatePesi;
            $connCrm->query($queryUpdatePesi);
}

$queryPlenitude = "SELECT pratica,totalePesoLordo,pesoTotalePagato FROM `plenitude` inner join aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id where mese>='2023-02-01'";
$risultatoPlenitude = $conn19->query($queryPlenitude);
while ($rigaPlenitude = $risultatoPlenitude->fetch_array()) {
    $pratica = $rigaPlenitude[0];
    $pesoTotaleLordo = $rigaPlenitude[1];
    $pesoTotalePagato = $rigaPlenitude[2];
            $queryUpdatePesi = "UPDATE `vtiger_plenitudecf` SET cf_3771='$pesoTotaleLordo',cf_3773='$pesoTotalePagato' WHERE plenitudeid='$pratica'";
            //echo $queryUpdatePesi;
            $connCrm->query($queryUpdatePesi);
}

