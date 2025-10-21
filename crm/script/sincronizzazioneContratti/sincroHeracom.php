<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniPlenitude.php";

require "/Applications/MAMP/htdocs/Know/funzioni/funzioniHeracom.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrmNuovo();
$connCrm = $objCrm->apriConnessioneCrmNuovo();

$queryRicerca = "SELECT "
        . "date_format(enel.datasottoscrizionecontratto,'%d-%m-%Y') as 'dataStipula', "
        . "enel.commodity as 'comodity', "
        . "enel.codicefiscale as 'codiceFiscale', "
        . "enel.tariffaluce as 'offertaLuce', "
        . "enel.tariffagas as 'offertaGas', "
        . "enel.metodopagamento as 'Metodo Pagamento', "
        . "enel.statopda as 'Stato PDA', "
        . "enel.statoplicoluce as 'Stato Luce', "
        . "enel.statoplicogas as 'Stato Gas', "
        . "enel.heracomid AS 'pratica', "
        . "enel.noteplicoluce AS 'Note Stato Luce', "
        . "enel.noteplicogas AS 'Note Stato Gas' "
        . "FROM "
        . "vtiger_heracomcf as enelcf "
        . "inner join vtiger_heracom as enel on enelcf.heracomid=enel.heracomid "
        . "inner join vtiger_crmentity as entity on enel.heracomid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "enel.datasottoscrizionecontratto >'2025-01-31' and  entity.deleted=0 and enel.statopda<>'Annullata' and (enel.commodity <>'Polizza' and enel.commodity <>'Consenso')";

//echo $queryRicerca;
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
    truncateSincroHeracom($conn19);

    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $comodity = $riga['comodity'];
        $metodoPagamento = $riga['Metodo Pagamento'];
        $statoPDA = $riga['Stato PDA'];
        $statoLuce = $conn19->real_escape_string($riga['Stato Luce']);
        $statoGas = $conn19->real_escape_string($riga['Stato Gas']);
        $pratica = $riga['pratica'];
        $noteStatoLuce = $conn19->real_escape_string($riga['Note Stato Luce']);
        $noteStatoGas = $conn19->real_escape_string($riga['Note Stato Gas']);
        $codiceFiscale = $riga['codiceFiscale'];
        $offertaLuce = $riga['offertaLuce'];
        $offertaGas = $riga['offertaGas'];

        $idStatoPda = idStatoPdaHeracom($conn19, $statopda);
        $queryFasePDA = "SELECT fase FROM `heracomStatoPDA` WHERE id = '$idStatoPda'";
        $risultatoFasePDA = $conn19->query($queryFasePDA);
        $rigaFasePDA = $risultatoFasePDA->fetch_array();
        $fasePDA = $rigaFasePDA[0];

        switch ($comodity) {
            case 'Luce':
                $statoPost = $statoLuce;
                $noteStato = $noteStatoLuce;
                $offerta = $offertaLuce;

                $idStatoLuce = idStatoLuceHeracom($conn19, $statoLuce);
                $queryFaseLuce = "SELECT fase FROM `heracomStatoLuce` WHERE id = '$idStatoLuce'";
                $risultatoFaseLuce = $conn19->query($queryFaseLuce);
                $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
                $fase = $rigaFaseLuce[0];
                break;
            case 'Gas':
                $statoPost = $statoGas;
                $noteStato = $noteStatoGas;
                $offerta = $offertaGas;

                $idStatoGas = idStatoGasHeracom($conn19, $statoGas);
                $queryFaseGas = "SELECT fase FROM `heracomStatoGas` WHERE id = '$idStatoGas'";
                $risultatoFaseGas = $conn19->query($queryFaseGas);
                $rigaFaseGas = $risultatoFaseGas->fetch_array();
                $fase = $rigaFaseGas[0];
                break;
            case 'Polizza':
                if ($statoLuce !== "") {
                    $statoPost = $statoLuce;
                    $noteStato = $noteStatoLuce;
                    $offerta = $offertaLuce;

                    $idStatoLuce = idStatoLuceHeracom($conn19, $statoLuce);
                    $queryFaseLuce = "SELECT fase FROM `heracomStatoLuce` WHERE id = '$idStatoLuce'";
                    $risultatoFaseLuce = $conn19->query($queryFaseLuce);
                    $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
                    $fase = $rigaFaseLuce[0];
                } else {
                    $statoPost = $statoGas;
                    $noteStato = $noteStatoGas;
                    $offerta = $offertaGas;

                    $idStatoGas = idStatoGasHeracom($conn19, $statoGas);
                    $queryFaseGas = "SELECT fase FROM `heracomStatoGas` WHERE id = '$idStatoGas'";
                    $risultatoFaseGas = $conn19->query($queryFaseGas);
                    $rigaFaseGas = $risultatoFaseGas->fetch_array();
                    $fase = $rigaFaseGas[0];
                }
                break;
            case 'Dual':
                if ($statoLuce !== "") {
                    $statoPost = $statoLuce;
                    $noteStato = $noteStatoLuce;
                    $offerta = $offertaLuce;

                    $idStatoLuce = idStatoLuceHeracom($conn19, $statoLuce);
                    $queryFaseLuce = "SELECT fase FROM `heracomStatoLuce` WHERE id = '$idStatoLuce'";
                    $risultatoFaseLuce = $conn19->query($queryFaseLuce);
                    $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
                    $fase = $rigaFaseLuce[0];
                } else {
                    $statoPost = $statoGas;
                    $noteStato = $noteStatoGas;
                    $offerta = $offertaGas;

                    $idStatoGas = idStatoGasHeracom($conn19, $statoGas);
                    $queryFaseGas = "SELECT fase FROM `heracomStatoGas` WHERE id = '$idStatoGas'";
                    $risultatoFaseGas = $conn19->query($queryFaseGas);
                    $rigaFaseGas = $risultatoFaseGas->fetch_array();
                    $fase = $rigaFaseGas[0];
                }
                break;
        }
        $mandato = "Enel";
        $dataImport = date("Y-m-d h:m:i");

        $queryInserimento = "INSERT INTO `sincroHeracom`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}



    
