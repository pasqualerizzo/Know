<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";

$obj = new connessioneSiscallLead();
$conn = $obj->apriConnessioneSiscallLead();

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

//$dataMaggiore = '2025-04-26 23:59:59';
//$dataMinore = '2025-04-22 00:00:00';

$oggi = date('N');
if ($oggi == 3) {
    $giorni = 4;
} else {
    $giorni = 3;
}


$dataMaggiore = date('Y-m-d 23:59:59', strtotime("-" . $giorni . " days"));
$dataMinore = date('Y-m-d 00:00:00', strtotime("-" . $giorni . " days"));

//echo $dataMaggiore . "-" . $dataMinore;


$liste2000 = " ("
        . "2000,"
        . "2098, "
        . "2097 "
        . " )";

$listaEsitoNonSpostabile = " ("
        . "401,"
        . "402,"
        . "403,"
        . "404,"
        . "405,"
        . "499"
        . ")";

$elencoLordo = [];
$elencoSpostabili = [];
$elencoNonSpostabili = [];
$elencoDuplicati = [];

$queryRicerca = "SELECT "
        . " lead_id AS 'lead_id', "
        . " list_id AS 'list_id', "
        . " phone_number AS 'telefono', "
        . " status AS 'esito', "
        . " entry_date AS 'dataInserimento' "
        . " FROM  "
        . " vicidial_list  "
        . " WHERE "
        . " list_id between 1028 and 1099  "
        . " AND entry_date<='$dataMaggiore'  "
        . " AND entry_date>='$dataMinore' ";
//echo $queryRicerca;
$risultato = $conn->query($queryRicerca);
while ($riga = $risultato->fetch_array()) {
    $temp = [];
    $i = 0;
    for ($c = 0;
            $c < 5;
            $c++) {
        array_push($temp, $riga[$c]);
    }
    array_push($elencoLordo, $temp);
}
$lunghezza = sizeof($elencoLordo);
echo $lunghezza;
$query2000 = $queryDuplicato = "SELECT "
        . " distinct(phone_number) "
        . " FROM  "
        . "  vicidial_list  "
        . " WHERE "
        . " list_id IN $liste2000 ";

$risultatoNonSpostabili1 = $conn->query($query2000);

while ($rigaNonSpostabili = $risultatoNonSpostabili1->fetch_array()) {
    if (in_array($rigaNonSpostabili[0], $elencoNonSpostabili)) {
        
    } else {
        array_push($elencoNonSpostabili, $rigaNonSpostabili[0]);
    }
}


$queryNonSpostabili = "SELECT "
        . " distinct(phone_number) "
        . " FROM  "
        . " vicidial_list  "
        . " WHERE "
        . " list_id between 1028 and 1099  "
        . " AND status  in $listaEsitoNonSpostabile ";

//echo $queryNonSpostabili;

$risultatoNonSpostabili2 = $conn->query($queryNonSpostabili);

while ($rigaNonSpostabili = $risultatoNonSpostabili2->fetch_array()) {
    if (in_array($rigaNonSpostabili[0], $elencoNonSpostabili)) {
        
    } else {
        array_push($elencoNonSpostabili, $rigaNonSpostabili[0]);
    }
}


foreach ($elencoLordo as $tempOk) {
    $esito = $tempOk[3];
    $numero = $tempOk[2];
    if (in_array($numero, $elencoNonSpostabili)) {
        
    } else {
        array_push($elencoSpostabili, $tempOk);
    }
}
//echo var_dump($elencoSpostabili);

foreach ($elencoSpostabili as $daSpostare) {
    $leadId = $daSpostare[0];
    $lista = 2000;

    $queryUpdate = "UPDATE vicidial_list SET list_id='$lista',status='NEW',called_since_last_reset='N',called_count=0 where lead_id='$leadId'";
    $conn->query($queryUpdate);
}
$obj->chiudiConnessioneSiscallLead();
$obj19->chiudiConnessione();
?>
