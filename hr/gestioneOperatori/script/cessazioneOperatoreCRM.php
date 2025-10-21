<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";


$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

$oggi = date("Y-m-d");

$queryRicerca = ""
        . " SELECT nomeCompleto "
        . " FROM gestioneOperatori "
        . " WHERE "
        . " dataCessazione<='$oggi' "
        . " AND "
        . " importCrm=0";

$risultaoRuolo = $conn19->query($queryRicerca);

while ($riga = $risultaoRuolo->fetch_array()) {
    $nomeCompleto = $riga[0];

    $queryUpdate = ""
            . " UPDATE "
            . " vtiger_users "
            . " SET "
            . " status='Inactive' "
            . " WHERE "
            . " user_name='$nomeCompleto'";

    try {
        $connCrm->query($queryUpdate);
    } catch (Exception $ex) {
        
    }

    $queryUpdate19 = ""
            . "UPDATE "
            . " gestioneOperatori "
            . " SET "
            . " importCrm=1 "
            . " WHERE "
            . " nomeCompleto='$nomeCompleto' ";
    try {
        $conn19->query($queryUpdate19);
    } catch (Exception $ex) {
        
    }
    
    
}
$obj19->chiudiConnessione();
$objCrm->chiudiConnessioneCrm();

