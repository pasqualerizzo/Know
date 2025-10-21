<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

$lead = [];
$i = 0;
$oggi = date('Y-m-d');

$query = "Select "
        . " leadId"
        . " from plenitude "
        . " where "
        . " campagna='SPN_LEAD' "
        . " and data='$oggi' ";

$risultato = $conn19->query($query);
while ($riga = $risultato->fetch_array()) {
    $upadate = " update vtiger_gestionechiamatacf set cf_4687='No', cf_4461='Ok Adesione' where cf_4459='$riga[0]'  ";
    $connCrm->query($upadate);
    $i += $connCrm->affected_rows;

    $url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";
    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => 'idCrm',
        'lead_id' => $riga[0],
        'function' => "update_lead",
        'status' => 499,
    ];
    //echo $url . "?" . http_build_query($query_fields);
    $curl2 = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    echo $response;
    curl_close($curl2);
}



$query = "Select "
        . " leadId "
        . " from enel "
        . " where "
        . " campagna='SPN_LEAD' "
        . " and data='$oggi' ";

$risultato = $conn19->query($query);
while ($riga = $risultato->fetch_array()) {
    $upadate = " update vtiger_gestionechiamatacf set cf_4687='No', cf_4461='Ok Adesione' where cf_4459='$riga[0]'  ";
    $connCrm->query($upadate);
    $i += $connCrm->affected_rows;
    
    $url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";
    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => 'idCrm',
        'lead_id' => $riga[0],
        'function' => "update_lead",
        'status' => 499,
    ];
    //echo $url . "?" . http_build_query($query_fields);
    $curl2 = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    echo $response;
    curl_close($curl2);
}

$query = "Select "
        . " leadId "
        . " from vivigas "
        . " where "
        . " campagna='SPN_INB' "
        . " and data='$oggi' ";

$risultato = $conn19->query($query);
while ($riga = $risultato->fetch_array()) {
    $upadate = " update vtiger_gestionechiamatacf set cf_4687='No', cf_4461='Ok Adesione' where cf_4459='$riga[0]'  ";
    $connCrm->query($upadate);
    $i += $connCrm->affected_rows;
    
    $url = "https://siscalllead.novadirect.it/vicidial/non_agent_api.php";
    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => 'idCrm',
        'lead_id' => $riga[0],
        'function' => "update_lead",
        'status' => 499,
    ];
    //echo $url . "?" . http_build_query($query_fields);
    $curl2 = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl2);
    echo $response;
    curl_close($curl2);
}


echo $i;
//$connCrm->chiudiConnessioneCrm();
?>
