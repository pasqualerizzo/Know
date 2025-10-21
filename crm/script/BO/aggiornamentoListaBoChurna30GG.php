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

$obj = new ConnessioneCrmNuovo();
$connCrm = $obj->apriConnessioneCrmNuovo();

$data = date('Y-m-d', strtotime('-28 days'));
//$data = '2025-02-24';
//echo $data;
$contrattiInseriti = [];

$url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";

$ko = "("
        . " 'CANCELLATO', "
        . " 'RIFIUTATO', "
        . " 'ANNULLATO', "
        . " 'Cancelled', "
        . " 'IN ATTESA FIRMA', "
        . " 'Pending Cancellation', "
        . " 'Superseded', "
        . " 'BOZZA', "
        . " 'Annullato' "
        . ")";

$queryRicerca = "SELECT "
       . "plenicf.nome AS 'nome', "
        . "plenicf.cognome AS 'cognome', "
        . "plenicf.cellulareprimario AS 'cellulare', "
        . "plenicf.codicefiscale AS 'codiceFiscale', "
        . "plenicf.noteplicoluce AS 'noteStatoLuce', "
        . "plenicf.noteplicogas AS 'noteStatoGas', "
        . " plenicf.datasottoscrizionecontratto AS 'dataContratto'"
        . "FROM "
        . "vtiger_plenitude as plenicf "
//        . "inner join vtiger_plenitude as pleni on plenicf.plenitudeid=pleni.plenitudeid "
//        . "inner join vtiger_crmentity as entity on pleni.plenitudeid=entity.crmid "
//        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . " plenicf.datasottoscrizionecontratto='$data' "
        . " AND statopda='Ok Definitivo' "
        . " AND ((commodity='Luce' and statoplicoluce not in $ko) "
        . "or (commodity='Gas' and statoplicogas not in $ko )) ";
//echo $queryRicerca;
$risultato = $connCrm->query($queryRicerca);
if ($risultato->num_rows > 0) {
    while ($riga = $risultato->fetch_array()) {
        if (in_array($riga['cellulare'], $contrattiInseriti)) {
            
        } else {

            $commento = $riga['noteStatoLuce'] . " " . $riga['noteStatoGas'];
            $query_fields = [
                'user' => "apiuserid",
                'pass' => "apipass",
                'source' => 'api',
                'function' => "add_lead",
                'add_to_hopper' => 'Y',
                'list_id' => '301',
                'phone_number' => $riga['cellulare'],
                'first_name' => $riga['nome'],
                'last_name' => $riga['cognome'],
                'comments' => $commento,
                'address1' => $riga['codiceFiscale'],
                'address2' => $riga['dataContratto']
            ];
            //echo $url . "?" . http_build_query($query_fields);
            $curl2 = curl_init($url . "?" . http_build_query($query_fields));
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl2);
            //echo $response;
            if (strpos($response, 'SUCCESS: add_lead LEAD HAS BEEN ADDED') !== false) {
                $contrattiInseriti[] = $riga['cellulare'];
                echo $riga['cellulare'] . "<br>";
            }
            curl_close($curl2);
        }
    }
}
$obj->chiudiConnessioneCrm();
$obj19->chiudiConnessione();
?>
