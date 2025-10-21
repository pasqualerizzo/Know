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

$queryRicerca = "SELECT "
        . "date_format(vivigascf.datacontratto,'%d-%m-%Y') as 'dataStipula', "
        . "vivigascf.commodity as 'comodity', "
        . "vivigascf.codicefiscale as 'codiceFiscale', "
        . "vivigascf.tariffaluce as 'offertaLuce', "
        . "vivigascf.tariffagas as 'offertaGas', "
        . "vivigascf.metodopagamento as 'Metodo Pagamento', "
        . "vivigascf.statopda as 'Stato PDA', "
        . "vivigascf.statoplicoluce as 'Stato Luce', "
        . "vivigascf.statoplicogas as 'Stato Gas', "
        . "vivigascf.vivigasid AS 'pratica', "
        . "vivigascf.noteplicoluce AS 'Note Stato Luce', "
        . "vivigascf.noteplicogas AS 'Note Stato Gas' "
        . "FROM "
        . "vtiger_vivigas as vivigascf "
        . "inner join vtiger_vivigascf as vivigas on vivigascf.vivigasid=vivigas.vivigasid "
        . "inner join vtiger_crmentity as entity on vivigas.vivigasid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "vivigascf.datacontratto >'2025-01-31' and  entity.deleted=0 and vivigascf.statopda<>'Annullata'";

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
    truncateSincroVivigas($conn19);

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

      
        
        switch ($comodity) {
            case 'LUCE':
                 case 'Luce':
                $statoPost = $statoLuce;
                $noteStato = $noteStatoLuce;
                $offerta = $offertaLuce;
                break;
            case 'GAS':
                case 'Gas':
                $statoPost = $statoGas;
                $noteStato = $noteStatoGas;
                $offerta = $offertaGas;
                break;
            case 'Polizza':
                if ($statoLuce !== "") {
                    $statoPost = $statoLuce;
                    $noteStato = $noteStatoLuce;
                    $offerta = $offertaLuce;
                } else {
                    $statoPost = $statoGas;
                    $noteStato = $noteStatoGas;
                    $offerta = $offertaGas;
                }
                break;
                case 'DUAL':
                    case 'Dual':
                if ($statoLuce !== "") {
                    $statoPost = $statoLuce;
                    $noteStato = $noteStatoLuce;
                    $offerta = $offertaLuce;
                } else {
                    $statoPost = $statoGas;
                    $noteStato = $noteStatoGas;
                    $offerta = $offertaGas;
                }
                break;

                
        }
        $mandato = "Vivigas";
        $dataImport = date("Y-m-d h:m:i");

        $queryInserimento = "INSERT INTO `sincroVivigas`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$statoPDA','$statoPost','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}



    
