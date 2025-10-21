<?php

ini_set('memory_limit', '256M');
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
$confrontoCRM = date('Y-m-1', strtotime('-3 months'));

$queryRicerca = "SELECT "
        . " cf_1470 as 'nomeOperatore',"
        . " cf_1474 as 'dataModifica',"
        . " cf_1472 as 'oraModifica',"
        . " cf_1478 as 'modulo',"
        . " cf_1476 as 'stato',"
        . " cf_1480 as 'esitoChiamata',"
        . " cf_1482 as 'idPratica',"
        . " cf_1654 as 'idRigaLuce',"
        . " cf_1656 as 'idRigaGas'"
        . " FROM "
        . " vtiger_controllobocf as bocf "
        . " inner join vtiger_controllobo as bo on bocf.controlloboid=bo.controlloboid "
        . " inner join vtiger_crmentity as entity on bo.controlloboid=entity.crmid "
        . " inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . " WHERE "
        . " cf_1474>'2023-01-01'";

$risultato = $connCrm->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {
    $queryTruncate = "TRUNCATE TABLE `controlloBo`";
    $conn19->query($queryTruncate);
    while ($riga = $risultato->fetch_assoc()) {
        $nomeOperatore = $riga["nomeOperatore"];
        $dataModifica = $riga["dataModifica"];
        $oraModifica = $riga["oraModifica"];
        $modulo = $riga["modulo"];
        $stato = $riga["stato"];
        $esitoChiamata = $riga["esitoChiamata"];
        $idPratica = $riga["idPratica"];
        $idRigaLuce = $riga["idRigaLuce"];
        $idRigaGas = $riga["idRigaGas"];

        $dataOperazione = date("Y-m-d H:i:s", strtotime($dataModifica . " " . $oraModifica));

        $queryInserimento = "INSERT INTO `controlloBo`"
                . " (`nomeOperatore`, `dataOperazione`, `modulo`, `Stato`, `esitoChiamata`, `idPratica`, `idRigaLuce`, `idRigaGas`)"
                . " VALUES "
                . " ('$nomeOperatore','$dataOperazione','$modulo','$stato','$esitoChiamata','$idPratica','$idRigaGas','$idRigaGas')";
        try {
            $conn19->query($queryInserimento);
        } catch (Exception $ex) {
            echo $ex;
        }
        unset($riga); // Libera memoria inutilizzata
    }
}

// Chiudi le connessioni al database
$obj19->chiudiConnessione();
$objCrm->chiudiConnessioneCrm();

header("Location: ../pannello.php");
exit;
?>
