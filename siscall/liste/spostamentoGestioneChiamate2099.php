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

//$dataMaggiore = '2024-08-05 23:59:59';
//$dataMinore = '2024-08-05 00:00:00';

$dataMaggiore = date('Y-m-d 23:59:59');
$dataMinore = date('Y-m-d 00:00:00');

$url = "https://siscallLead.novadirect.it/vicidial/non_agent_api.php";

$listaEsito = " ("
        . "'INBND',"
        . "'TIMEOT',"
        . "'DROP',"
        . "'NANQUE'"
        . ")";
$arrayEsiti = [
    'INBND',
    'TIMEOT',
    'DROP',
    'NANQUE'
];

$queryRicerca = "SELECT "
        . " lead_id AS 'lead_id', "
        . " list_id AS 'list_id', "
        . " phone_number AS 'telefono', "
        . " status AS 'esito', "
        . " entry_date AS 'dataInserimento' "
        . " FROM  "
        . "  vicidial_list  "
        . " WHERE "
        . " list_id between 1028 and 1099  "
        . " AND entry_date<='$dataMaggiore'  "
        . " AND entry_date>='$dataMinore'"
        . " AND phone_number in "
        . "(SELECT "
        . " distinct(phone_number) "
        . " FROM  "
        . "  vicidial_list  "
        . " WHERE "
        . " list_id between 1028 and 1099  "
        . " AND status  in $listaEsito "
        . " AND entry_date<='$dataMaggiore'  "
        . " AND entry_date>='$dataMinore') ";
//echo $queryRicerca;

$elencoLordo = [];
$elencoSpostabili = [];
$elencoNonSpostabili = [];
$elenco2099 = [];
//$elencoDuplicati = [];
$risultato = $conn->query($queryRicerca);
//
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

$query2099 = "SELECT "
        . " distinct(phone_number) "
        . " FROM  "
        . "  vicidial_list  "
        . " WHERE "
        . " list_id=2097 "
        . " AND status  in $listaEsito "
        . " AND entry_date<='$dataMaggiore'  "
        . " AND entry_date>='$dataMinore' ";
//echo $query2099;
$risultato2099 = $conn->query($query2099);

while ($riga2099 = $risultato2099->fetch_array()) {
    array_push($elencoNonSpostabili, $riga2099[0]);
}
/**
 * Modifica del 17/12/2024 Aggiunta query per il riscontro se il numero di telefono è presente con un esito diverso dei 4 precedenti
 */
$query2099 = "SELECT "
        . " distinct(phone_number) "
        . " FROM  "
        . "  vicidial_list  "
        . " WHERE "
        . " list_id between 1028 and 1099  "
        . " AND status not in $listaEsito "
        . " AND entry_date<='$dataMaggiore'  "
        . " AND entry_date>='$dataMinore' ";
//echo $query2099;
$risultato2099 = $conn->query($query2099);

while ($riga2099 = $risultato2099->fetch_array()) {
    array_push($elencoNonSpostabili, $riga2099[0]);
}


$lunghezza = sizeof($elencoLordo);
foreach ($elencoLordo as $tempOk) {
    $esito = $tempOk[3];
    $numero = $tempOk[2];
    $spostabile = false;
    if (in_array($numero, $elencoNonSpostabili)) {
        
    } else {
        if (in_array($esito, $arrayEsiti)) {
            foreach ($elencoLordo as $confronto) {
                if (in_array($numero, $confronto)) {
                    if (in_array($confronto[3], $arrayEsiti)) {
                        $spostabile = true;
                    } else {
                        $spostabile = false;
                        break;
                    }
                }
            }
        } else {
            array_push($elencoNonSpostabili, $numero);
        }
        if ($spostabile) {
            array_push($elencoSpostabili, $tempOk);
        }
    }
}
echo var_dump($elencoNonSpostabili);
echo "<br><br>";

echo var_dump($elencoSpostabili);

foreach ($elencoSpostabili as $daSpostare) {
    $leadId = $daSpostare[0];
    $numero = $daSpostare[2];
    if (in_array($numero, $elencoNonSpostabili)) {
        
    } else {
        $lista = 2097;
        $queryUpdate = "UPDATE vicidial_list SET list_id='$lista',status='NEW',called_since_last_reset='N',called_count=0,phone_code=1 where lead_id='$leadId'";
        $conn->query($queryUpdate);
        array_push($elencoNonSpostabili, $numero);
    }
}
$obj->chiudiConnessioneSiscallLead();
$obj19->chiudiConnessione();
?>
