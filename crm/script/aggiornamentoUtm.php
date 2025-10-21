<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj=new ConnessioneCrmNuovo();
$connCrm=$obj->apriConnessioneCrmNuovo();

$patrichePlenitude = [];

$queryRicercaPlenitude = "SELECT "
        . " plenitudeid, "
        . " idsponsorizzata "
        . " FROM "
        . " vtiger_plenitude "
        . " WHERE "
        . " idsponsorizzata LIKE 'G%' "
        . " AND "
        . " utm='' ";
//        . " LIMIT 10";
echo $queryRicercaPlenitude;
$risultato = $connCrm->query($queryRicercaPlenitude);
while ($riga = $risultato->fetch_array()) {
    $idPlenitude = $riga[0];
    $idSponsorizzato = $riga[1];

    $queryGestioneLead = "SELECT utmCampagna,dataImport FROM `gestioneLead` where idSponsorizzata='$idSponsorizzato'";
    //echo $queryGestioneLead."<br>";
    $risultatoGLC = $conn19->query($queryGestioneLead);
    if ($conteggio = $risultatoGLC->num_rows == 0) {
        echo $idSponsorizzato . "<br>";
    } else {
        $rigaGLC = $risultatoGLC->fetch_array();

        $utm = $rigaGLC[0];
        $dataImport = $rigaGLC[1];
        $queryInserimento = "UPDATE "
                . " vtiger_plenitude "
                . " SET "
                . " utm='$utm' ,dataarrivolead='$dataImport' "
                . " WHERE "
                . " plenitudeid='$idPlenitude'";
echo $queryInserimento;
        $connCrm->query($queryInserimento);
    }
}

$obj->chiudiConnessioneCrm();
$obj19->chiudiConnessione();
?>
