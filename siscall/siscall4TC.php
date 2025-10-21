<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall4.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj = new connessioneSiscall4();
$conn = $obj->apriConnessioneSiscall4();

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
$provenienza = "siscall4TC";
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
        . "tempo.user AS USER, "
        . "operatore.full_name AS 'Nome Completo', "
        . "user_level AS livello, "
        . "territory AS citta, "
        . " DATE_FORMAT(event_date,'%d-%m-%Y') AS giorno, "
        . " DATE_FORMAT(event_date,'%m-%Y') AS mese, "
        . "tempo.user_group AS user_group,"
        . "'enel_out' AS mandato,"
        . "'0' AS 'Pause',"
        . "'0' AS 'Wait',"
        . "'0' AS 'Talk',"
        . "'0' AS 'Dispo',"
        . " SUM(tempo.login_sec) AS 'numero',"
        . " '0' AS 'Dead',"
        . "operatore.custom_one AS 'Data Assunzione', "
        . " operatore.custom_three AS 'Nome Cognome' "
        . "From "
        . "vicidial_timeclock_log AS tempo "
        . "INNER JOIN vicidial_users AS operatore ON tempo.user=operatore.user "
        . "WHERE "
        . "event_date >= '2022-01-01 00:00:00' AND "
        . "event ='LOGIN' "

        . "GROUP BY tempo.user,event_date "
        . "ORDER BY USER ASC";

//echo $queryRicerca;
$risultato = $conn->query($queryRicerca);
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $conn->real_escape_string($conn->error);
    $queryErroreLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`) VALUES ('$dataErrore','$provenienza','$errore')";
    $conn19->query($queryErroreLog);
} else {
    $queryTruncate = "TRUNCATE TABLE `stringheSiscall4TC`";
    $conn19->query($queryTruncate);
    
    while ($riga = $risultato->fetch_array()) {
        $user = $riga[0];
        $nomeCompleto = $riga[1];
        $livello = $riga[2];
        $sede = strtolower($riga[3]);
        $giorno = date('Y-m-d', strtotime($riga[4]));
        $mese = $riga[5];
        $userGroup = $riga[6];
        $mandato = $riga[7];
        $pause = $riga[8];
        $wait = $riga[9];
        $talk = $riga[10];
        $dispo = $riga[11];
        $numero = $riga[12];
        $dead = $riga[13];
        $dataAssunzione = date('Y-m-d', strtotime($riga[14]));
        $nomeCognome = $riga[15];

        /**
         * ricerca id sede
         */
        $querySede = "SELECT * FROM `sede` where descrizione='$sede'";
        $risultatoSede = $conn19->query($querySede);
        $conteggioSede = $risultatoSede->num_rows;
        if ($conteggioSede == 0) {
            $queryInserimentoSede = "INSERT INTO `sede`(`descrizione`) VALUES ('$sede')";
            $conn19->query($queryInserimentoSede);
            $idSede = $conn19->insert_id;
        } else {
            $rigaSede = $risultatoSede->fetch_array();
            $idSede = $rigaSede[0];
        }
        /**
         * ricerca id Mandato
         */
        $queryMandato = "SELECT * FROM `mandato` where descrizione='$mandato'";
        $risultatoMandato = $conn19->query($queryMandato);
        $conteggioMandato = $risultatoMandato->num_rows;
        if ($conteggioMandato == 0) {
            $queryInserimentoMandato = "INSERT INTO `mandato`(`descrizione`) VALUES ('$mandato')";
            $conn19->query($queryInserimentoMandato);
            $idMandato = $conn19->insert_id;
        } else {
            $rigaMandato = $risultatoMandato->fetch_array();
            $idMandato = $rigaMandato[2];
        }

        $queryInserimento = "INSERT INTO `stringheSiscall4TC`"
                . "(`user`, `nomeCompleto`, `livello`, `sede`, `giorno`, `mese`, `userGroup`, `mandato`, `pause`, `wait`, `talk`, `dispo`, `numero`, `dead`, `dataAssunzione`, `NomeCognome`, `dataImport`, `provenienza`,idSede,idMandato) "
                . "VALUES ('$user','$nomeCompleto','$livello','$sede','$giorno','$mese','$userGroup','$mandato','$pause','$wait','$talk','$dispo','$numero','$dead','$dataAssunzione','$nomeCognome','$dataImport','$provenienza','$idSede','$idMandato')";
       echo $queryInserimento;
        $conn19->query($queryInserimento);
    }
    $dataFine = date('Y-m-d H:i:s');
    $queryFineLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataFine','$provenienza','Fine Import da $provenienza',1,'$idStato')";
    $conn19->query($queryFineLog);
}

/**
 * Inizio prelievo dati da crm2 tabella Vivigas
 */



