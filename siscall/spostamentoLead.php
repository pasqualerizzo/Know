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

$url = "https://siscall2.novadirect.it/vicidial/non_agent_api.php";

$esiti1GG = [
    '406',
    '411',
    'A',
    'N',
    '414',
];

$esiti3GG = [
    '409',
    'NI',
    'CALLBK'
];

$oggi = date("Y-m-d");
$data1GG = date("Y-m-d", strtotime("-1 days " . $oggi));
$data3GG = date("Y-m-d", strtotime("-3 days " . $oggi));
//echo $data3GG;
//esecuzione a 3 giorno
$n = 0;
$elencoQuery3GG = "";
foreach ($esiti3GG as $esito) {
    $elencoQuery3GG .= ($n == 0) ? "(LIST.`status`=" : "OR LIST.`status`=";
    $elencoQuery3GG .= " '$esito' ";
    $n++;
}
$elencoQuery3GG .= " )";
//echo $elencoQuery3GG;

$queryRicerca3GG = "SELECT "
        . " LOG.lead_id AS 'lead_id', "
        . " LOG.list_id AS 'list_id', "
        . " LIST.phone_number AS 'telefono', "
        . " LIST.first_name AS 'nome', "
        . " LIST.last_name AS 'cognome', "
        . " LIST.`status` AS 'esito', "
        . " LIST.entry_date AS 'dataInserimento', "
        . " LIST.address1 AS 'dataImport', "
        . " LIST.address2 AS 'UTMCampagna', "
        . " LIST.address3 AS 'source', "
        . " LIST.city AS 'origine', "
        . " LIST.email AS 'email' "
        . " FROM  "
        . " vicidial_log AS LOG "
        . " right JOIN vicidial_list AS LIST ON LOG.lead_id=LIST.lead_id "
        . " WHERE "
        . " (LOG.list_id=1006 OR LOG.list_id=1005) "
        . " AND LIST.entry_date<='$data3GG'  "
        . " AND " . $elencoQuery3GG;

echo $queryRicerca3GG;

$risultato3GG = $conn->query($queryRicerca3GG);
while ($riga = $risultato3GG->fetch_assoc()) {

    $lead_IdOld = $riga['lead_id'];
    $list_idOld = $riga['list_id'];
    $telefono = $riga['telefono'];
    $nome = $riga['nome'];
    $cognome = $riga['cognome'];
    $esitoOld = $riga['esito'];
    $dataInserimento = $riga['dataInserimento'];
    $dataImport = $riga['dataImport'];
    $utmCampagna = $riga['UTMCampagna'];
    $origine = $riga['origine'];
    $email = $riga['email'];
    $source = $riga['source'];

    $source = ($source == "") ? "copia" : $source;

    switch ($esitoOld) {
        case "409":
            $esito = "809";
            break;
        case "NI":
            $esito = "815";
            break;
        case "CALLBK":
            $esito = "816";
            break;
    }

    $lista = "1010";
    $campagna = "SPL_LED2";
    $leadId = "INVALID";

    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => $source,
        'list_id' => $lista,
        'campaign_id' => $campagna,
        'function' => "add_lead",
        'first_name' => $nome,
        'last_name' => $cognome,
        'phone_number' => $telefono,
        'add_to_hopper' => "Y",
        'address1' => $dataImport,
        'address2' => $utmCampagna,
        'address3' => $source,
        'city' => $origine,
        'email' => $email,
        'duplicate_check'=>'DUPLIST'
    ];
    $curl = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);

    if (curl_errno($curl)) {//import non possibile
        echo "Errore IMPORT NUOVO LEAD";
    } else { //import possibile
        if (strpos($response, "SUCCESS") === 0) {
            $ricerca = "|" . $lista . "|";
            $listaPosizione = strpos($response, $ricerca) + strlen($ricerca);
            $finePosizione = strpos($response, "|", $listaPosizione);
            $lunghezza = $finePosizione - $listaPosizione;
            $leadId = substr($response, $listaPosizione, $lunghezza);

            $source = ($source == "") ? "ricerca" : $source;
            $query_fields = [
                'user' => "apiuserid",
                'pass' => "apipass",
                'source' => $source,
                'lead_id' => $lead_IdOld,
                'function' => "lead_field_info",
                'field_name' => "idCrm",
                'custom_fields' => 'Y',
                'list_id' => $list_idOld,
            ];
            //echo $url . "?" . http_build_query($query_fields);
            $curl2 = curl_init($url . "?" . http_build_query($query_fields));
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl2);
            curl_close($curl2);

            $query_fields = [
                'user' => "apiuserid",
                'pass' => "apipass",
                'source' => $source,
                'lead_id' => $leadId,
                'function' => "update_lead",
                'idCrm' => $response,
            ];
            echo $url . "?" . http_build_query($query_fields);
            $curl2 = curl_init($url . "?" . http_build_query($query_fields));
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl2);
            //echo $response;
            curl_close($curl2);

            $query_fields = [
                'user' => "apiuserid",
                'pass' => "apipass",
                'source' => $source,
                'lead_id' => $lead_IdOld,
                'function' => "update_lead",
                'status' => $esito,
            ];
            echo $url . "?" . http_build_query($query_fields);
            $curl2 = curl_init($url . "?" . http_build_query($query_fields));
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl2);
            //echo $response;
            curl_close($curl2);
        }//chiusura else 
    }
}//chiusura while 3gg

//esecuzione a 1 giorno
$n = 0;
$elencoQuery1GG = "";
foreach ($esiti1GG as $esito) {
    $elencoQuery1GG .= ($n == 0) ? "(LIST.`status`=" : "OR LIST.`status`=";
    $elencoQuery1GG .= " '$esito' ";
    $n++;
}
$elencoQuery1GG .= " )";
//echo $elencoQuery3GG;

$queryRicerca1GG = "SELECT "
        . " LOG.lead_id AS 'lead_id', "
        . " LOG.list_id AS 'list_id', "
        . " LIST.phone_number AS 'telefono', "
        . " LIST.first_name AS 'nome', "
        . " LIST.last_name AS 'cognome', "
        . " LIST.`status` AS 'esito', "
        . " LIST.entry_date AS 'dataInserimento', "
        . " LIST.address1 AS 'dataImport', "
        . " LIST.address2 AS 'UTMCampagna', "
        . " LIST.address3 AS 'source', "
        . " LIST.city AS 'origine', "
        . " LIST.email AS 'email' "
        . " FROM  "
        . " vicidial_log AS LOG "
        . " right JOIN vicidial_list AS LIST ON LOG.lead_id=LIST.lead_id "
        . " WHERE "
        . " (LOG.list_id=1006 OR LOG.list_id=1005) "
        . " AND LIST.entry_date<='$data1GG'  "
        . " AND " . $elencoQuery1GG;

echo $queryRicerca1GG;

$risultato1GG = $conn->query($queryRicerca1GG);
while ($riga = $risultato1GG->fetch_assoc()) {

    $lead_IdOld = $riga['lead_id'];
    $list_idOld = $riga['list_id'];
    $telefono = $riga['telefono'];
    $nome = $riga['nome'];
    $cognome = $riga['cognome'];
    $esitoOld = $riga['esito'];
    $dataInserimento = $riga['dataInserimento'];
    $dataImport = $riga['dataImport'];
    $utmCampagna = $riga['UTMCampagna'];
    $origine = $riga['origine'];
    $email = $riga['email'];
    $source = $riga['source'];

    $source = ($source == "") ? "copia" : $source;

    switch ($esitoOld) {
        case "406":
            $esito = "806";
            break;
        case "411":
            $esito = "811";
            break;
        case "N":
            $esito = "817";
            break;
        case "A":
            $esito = "818";
            break;
        case "414":
            $esito = "814";
            break;
    }

    $lista = "1010";
    $campagna = "SPL_LED2";
    $leadId = "INVALID";

    $query_fields = [
        'user' => "apiuserid",
        'pass' => "apipass",
        'source' => $source,
        'list_id' => $lista,
        'campaign_id' => $campagna,
        'function' => "add_lead",
        'first_name' => $nome,
        'last_name' => $cognome,
        'phone_number' => $telefono,
        'add_to_hopper' => "Y",
        'address1' => $dataImport,
        'address2' => $utmCampagna,
        'address3' => $source,
        'city' => $origine,
        'email' => $email,
        'duplicate_check'=>'DUPLIST'
    ];
    $curl = curl_init($url . "?" . http_build_query($query_fields));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);

    if (curl_errno($curl)) {//import non possibile
        echo "Errore IMPORT NUOVO LEAD";
    } else { //import possibile
        if (strpos($response, "SUCCESS") === 0) {
            $ricerca = "|" . $lista . "|";
            $listaPosizione = strpos($response, $ricerca) + strlen($ricerca);
            $finePosizione = strpos($response, "|", $listaPosizione);
            $lunghezza = $finePosizione - $listaPosizione;
            $leadId = substr($response, $listaPosizione, $lunghezza);

            $source = ($source == "") ? "ricerca" : $source;
            $query_fields = [
                'user' => "apiuserid",
                'pass' => "apipass",
                'source' => $source,
                'lead_id' => $lead_IdOld,
                'function' => "lead_field_info",
                'field_name' => "idCrm",
                'custom_fields' => 'Y',
                'list_id' => $list_idOld,
            ];
            //echo $url . "?" . http_build_query($query_fields);
            $curl2 = curl_init($url . "?" . http_build_query($query_fields));
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl2);
            curl_close($curl2);

            $query_fields = [
                'user' => "apiuserid",
                'pass' => "apipass",
                'source' => $source,
                'lead_id' => $leadId,
                'function' => "update_lead",
                'idCrm' => $response,
            ];
            echo $url . "?" . http_build_query($query_fields);
            $curl2 = curl_init($url . "?" . http_build_query($query_fields));
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl2);
            //echo $response;
            curl_close($curl2);

            $query_fields = [
                'user' => "apiuserid",
                'pass' => "apipass",
                'source' => $source,
                'lead_id' => $lead_IdOld,
                'function' => "update_lead",
                'status' => $esito,
            ];
            echo $url . "?" . http_build_query($query_fields);
            $curl2 = curl_init($url . "?" . http_build_query($query_fields));
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl2);
            //echo $response;
            curl_close($curl2);
        }//chiusura else 
    }
}//chiusura while 3gg

$obj->chiudiConnessioneSiscall2();
$obj19->chiudiConnessione();
    ?>

