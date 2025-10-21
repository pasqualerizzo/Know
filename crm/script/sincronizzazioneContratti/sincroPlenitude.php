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

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrmNuovo();
$connCrm = $objCrm->apriConnessioneCrmNuovo();

$arrayStatoPda = arrayStatoPda($conn19);
//print_r($arrayStatoPda);
$arrayStatoLuce = arrayStatoLuce($conn19);
//print_r($arrayStatoLuce);    
$arrayStatoGas = arrayStatoGas($conn19);

$queryRicerca = "SELECT "
        . "date_format(plenicf.datasottoscrizionecontratto,'%d-%m-%Y') as 'dataStipula', "
        . "plenicf.commodity as 'comodity', "
        . "plenicf.codicefiscale as 'codiceFiscale', "
        . "plenicf.tariffaluce as 'offertaLuce', "
        . "plenicf.tariffagas as 'offertaGas', "
        . "plenicf.metodopagamento as 'Metodo Pagamento', "
        . "plenicf.statopda as 'Stato PDA', "
        . "plenicf.statoplicoluce as 'Stato Luce', "
        . "plenicf.statoplicogas 'Stato Gas', "
        . "plenicf.plenitudeid AS 'pratica', "
        . "plenicf.noteplicoluce AS 'Note Stato Luce', "
        . "plenicf.noteplicogas AS 'Note Stato Gas' "
        . "FROM "
        . "vtiger_plenitude as plenicf "
        . "inner join vtiger_plenitudecf as pleni on plenicf.plenitudeid=pleni.plenitudeid "
        . "inner join vtiger_crmentity as entity on pleni.plenitudeid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "plenicf.datasottoscrizionecontratto >'2025-01-31' and  entity.deleted=0 and plenicf.statopda<>'Annullata'";

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
    truncateSincroPlenitude($conn19);

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
        $offertaLuce = $conn19->real_escape_string($riga['offertaLuce']);
        $offertaGas = $conn19->real_escape_string($riga['offertaGas']);

        $fasePDA = $arrayStatoPda[$statoPDA][1];
        
        switch ($comodity) {
            case 'Luce':
                $statoPost = $statoLuce;
                $noteStato = $noteStatoLuce;
                $offerta = $offertaLuce;
                 $fase = $arrayStatoLuce[$statoLuce][1];
                break;
            case 'Gas':
                $statoPost = $statoGas;
                $noteStato = $noteStatoGas;
                $offerta = $offertaGas;
                $fase = $arrayStatoGas[$statoGas][1];
                break;
            case 'Polizza':
                if ($statoLuce !== "") {
                    $statoPost = $statoLuce;
                    $noteStato = $noteStatoLuce;
                    $offerta = $offertaLuce;
                     $fase = $arrayStatoLuce[$statoLuce][1];
                } else {
                    $statoPost = $statoGas;
                    $noteStato = $noteStatoGas;
                    $offerta = $offertaGas;
                    $fase = $arrayStatoGas[$statoGas][1];
                }

                break;
        }
        $mandato = "Plenitude";
        $dataImport = date("Y-m-d h:m:i");

        $queryInserimento = "INSERT INTO `sincroPlenitude`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$fasePDA','$fase','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}



    
