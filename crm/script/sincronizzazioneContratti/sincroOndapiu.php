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

require "/Applications/MAMP/htdocs/Know/funzioni/funzioniOndapiu.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrmNuovo();
$connCrm = $objCrm->apriConnessioneCrmNuovo();

$arrayStatoPda = arrayStatoPda($conn19);
//print_r($arrayStatoPda);
$arrayStatoLuce = arrayStatoLuce($conn19);
//print_r($arrayStatoLuce);    
$arrayStatoGas = arrayStatoGas($conn19);
//print_r($arrayStatoGas);

$queryRicerca = "SELECT "
        . "date_format(enel.datasottoscrizionecontratto,'%d-%m-%Y') as 'dataStipula', "
        . "enel.commodity as 'comodity', "
        . "enel.codicefiscale as 'codiceFiscale', "
        . "enel.tariffaluce as 'offertaLuce', "
        . "enel.tariffagas as 'offertaGas', "
        . "enel.metodpPagamento as 'Metodo Pagamento', "
        . "enel.statopdaondapiu as 'Stato PDA', "
        . "enel.statoplicoluce as 'Stato Luce', "
        . "enel.statoplicogas as 'Stato Gas', "
        . "enel.ondapiuid AS 'pratica', "
        . "enel.noteplicoluce AS 'Note Stato Luce', "
        . "enel.noteplicogas AS 'Note Stato Gas' "
        . "FROM "
        . "vtiger_ondapiucf as enelcf "
        . "inner join vtiger_ondapiu as enel on enelcf.ondapiuid=enel.ondapiuid "
        . "inner join vtiger_crmentity as entity on enel.ondapiuid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "enel.datasottoscrizionecontratto >'2025-01-31' and  entity.deleted=0 and enel.statopdaondapiu<>'Annullata' and enel.commodity <>'Polizza'";

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
    //truncateSincroEnel($conn19);
    truncateSincroOndapiu($conn19);

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
            case 'Dual':
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
        $mandato = "Ondapiu";
        $dataImport = date("Y-m-d h:m:i");

        $queryInserimento = "INSERT INTO `sincroOndapiu`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$fasePDA','$fase','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}



    
