<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj = new connessioneSiscall2();
$conn = $obj->apriConnessioneSiscall2();

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();
/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
//$oggi = date('Y-m-d', strtotime('2023-01-01'));
$ieri = date('Y-m-d', strtotime('-1 days'));
//$ieri = date('Y-m-d', strtotime('2023-04-04'));
$provenienza = "listaLead";
/**
 * Recupero valore idStato
 */
$queryIdStato = "SELECT max(idStato) FROM `logImport`";
$risultatoIdStato = $conn19->query($queryIdStato);
$rigaStato = $risultatoIdStato->fetch_array();
$idStato = $rigaStato[0] + 1;

$queryInzioLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataImport','$provenienza','Inizio Import da $provenienza',0,'$idStato')";
$conn19->query($queryInzioLog);

$queryRicerca = "SELECT "
        . "list_id,   lead_id, first_name, last_name, city, address2, called_count,lista.status,esito.status_name,entry_date "
        
        . "FROM "
        . "vicidial_list AS lista "
        . "left JOIN vicidial_campaign_statuses AS esito ON lista.`status`=esito.`status` "
        . "WHERE "
        . "list_id=1005 or "
        . "list_id=1006 or "
        . "list_id=11118 or "
        . "list_id=11113 or "
        . "list_id=11119"
        . " GROUP by lead_id";

echo $queryRicerca;
$risultato = $conn->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
    $queryErroreLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`) VALUES ('$dataErrore','$provenienza','$errore')";
    $conn19->query($queryErroreLog);
} else {
    $queryTruncate = "TRUNCATE TABLE `gestioneLeadSiscall`";
    $conn19->query($queryTruncate);
    while ($riga = $risultato->fetch_array()) {
        $lista = $riga[0];
        $leadId = $riga[1];
        $nome = $conn19->real_escape_string($riga[2]);
        $cognome = $conn19->real_escape_string($riga[3]);
        $origine = $riga[4];
        $utmC = $riga[5];
        $conteggioChiamate = $riga[6];
        $esito = $riga[7];
        $descrizioneEsito =$riga[8];
        $inserimento = $riga[9];
        $idCrm=0;
        switch ($lista) {
            case 1005:
                $queryCustom = "SELECT idCrm FROM custom_1005 WHERE lead_id='$leadId'";
                break;
            case 1006:
                $queryCustom = "SELECT idCrm FROM custom_1006 WHERE lead_id='$leadId'";
                break;
            case 11113:
                $queryCustom = "SELECT idCrm FROM custom_11113 WHERE lead_id='$leadId'";
                break;
            case 11118:
                $queryCustom = "SELECT idCrm FROM custom_11118 WHERE lead_id='$leadId'";
                break;
            case 11119:
                $queryCustom = "SELECT idCrm FROM custom_11119 WHERE lead_id='$leadId'";
                break;
        }

        $risultatoIdCrm = $conn->query($queryCustom);
        $count = $risultatoIdCrm->num_rows;
        if ($count > 0) {
            $rigaIdCrm = $risultatoIdCrm->fetch_array();
            $idCrm = $rigaIdCrm[0];
        } else {
            $idCrm = "0";
        }


        /**
         * ricerca id sede
         */
        $queryInserimento = "INSERT INTO "
                . "`gestioneLeadSiscall` "
                . "( `lista`, `leadId`, `nome`, `cognome`, `origine`, `utm_c`, `conteggioChiamate`, `status`, `descrizioneStatus`,idCrm,inserimento) "
                . "VALUES "
                . "('$lista','$leadId','$nome','$cognome','$origine','$utmC','$conteggioChiamate','$esito','$descrizioneEsito','$idCrm','$inserimento')";
        //echo $queryInserimento;
        $conn19->query($queryInserimento);
    }
    $dataFine = date('Y-m-d H:i:s');
    $queryFineLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataFine','$provenienza','Fine Import da $provenienza',1,'$idStato')";
    $conn19->query($queryFineLog);
}

/**
 * Inizio prelievo dati da crm2 tabella Vivigas
 */



