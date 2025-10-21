<?php

header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

$obj = new Connessione();
$conn = $obj->apriConnessione();

$objLead = new connessioneSiscallLead();
$connLead = $objLead->apriConnessioneSiscallLead();

$oggi = date('Y-m-d 23:59:59');
$data7GG = date('Y-m-d 00:00:00', strtotime("- 120 days"));
//echo $data7GG;
$elencoLead = [];
$query_lead = "SELECT"
        . " vtiger_gestioneleadcf.cf_4066 AS lead, "
        . " vtiger_gestioneleadcf.cf_4058 AS dataImport, "
        . "  vtiger_gestioneleadcf.gestioneleadid AS id "
        . " FROM"
        . " vtiger_gestioneleadcf"
        . " WHERE"
        //. " (vtiger_gestioneleadcf.cf_4100 = '' OR vtiger_gestioneleadcf.cf_4100 = 'VDAD') and "
        . "  vtiger_gestioneleadcf.cf_4058 between '$data7GG' and '$oggi'";
echo $query_lead; 
$f1 = $connCrm->query($query_lead);
while ($f2 = $f1->fetch_array()) {
    $leadId = $f2[0];
    $dataImport = date('Y-m-d', strtotime($f2[1]));
    $id = $f2[2];
    $elencoLead[$leadId] = [$dataImport, $id];
}
echo count($elencoLead);
//foreach ($elencoLead as $leadId => $dati) {
//    echo $leadId." ".$dati[0]." ".$dati[1]."<br>";
//    
//}
foreach ($elencoLead as $leadId => $dati) {
    $query = "SELECT "
            . " vicidial_users.full_name,"
            . " STATUS,"
            . " comments "
            . " FROM "
            . " vicidial_log "
            . " INNER JOIN vicidial_users ON vicidial_log.`user`=vicidial_users.user "
            . " WHERE "
            . " lead_id='$leadId' "
            . " and vicidial_log.user='VDAD' "
            //. " and call_date>'$dati[0]' "
            . " AND (list_id like '15__' "
            . " or list_id=2099 or list_id=2098 or list_id=2097)"
            . " and call_date>'2025-01-01 00:00:00'";

    $risultato = $connLead->query($query);
    $conteggio = $risultato->num_rows;
    if ($conteggio > 0) {
        while ($riga = $risultato->fetch_array()) {
            $nomeCompleto = $riga[0];
            $esito = $riga[1];
            $commento = $riga[2];
            $queryUpdate = "UPDATE vtiger_gestioneleadcf set "
                    . " cf_4100='$nomeCompleto', "
                    . " cf_4098 = '$esito' "
                    //. " cf_4467 = '$commento' "
                    . " WHERE "
                    . " vtiger_gestioneleadcf.gestioneleadid = '$dati[1]'";
            $connCrm->query($queryUpdate);
        }
    } else {

        $query = "SELECT"
                . " vicidial_users.full_name,"
                . " STATUS,"
                . " comments "
                . " FROM "
                . " vicidial_closer_log "
                . " INNER JOIN vicidial_users ON vicidial_closer_log.`user`=vicidial_users.user "
                . " WHERE "
                . " lead_id='$leadId' "
                //. " and call_date>'$dati[0]' "
                . " and vicidial_closer_log.user<>'VDCL' "
                . " AND (list_id like '15__'"
                . " or list_id=2099  or list_id=2098 or list_id=2097)"
                . " and call_date>'2025-01-01 00:00:00'";

        $risultato = $connLead->query($query);
        $conteggio = $risultato->num_rows;
        if ($conteggio > 0) {
            while ($riga = $risultato->fetch_array()) {
                $nomeCompleto = $riga[0];
                $esito = $riga[1];
                $commento = $riga[2];
                $queryUpdate = "UPDATE vtiger_gestioneleadcf set "
                        . " cf_4100='$nomeCompleto', "
                        . " cf_4098 = '$esito' "
                        //. " cf_4467 = '$commento' "
                        . " WHERE "
                        . " vtiger_gestioneleadcf.gestioneleadid = '$dati[1]'";
                $connCrm->query($queryUpdate);
            }
        }
    }
}
$objCrm->chiudiConnessioneCrm();
$obj->chiudiConnessione();
?>
