<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require_once 'apiCrmChiamate.php';

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objS2 = new connessioneSiscallLead();
$connS2 = $objS2->apriConnessioneSiscallLead();

$lists = [1028, 1029, 1030, 1031, 1032, 1033, 1034, 1035, 1036, 1037, 1038, 1040, 1041, 1042, 1045, 1046, 1047, 1051, 1052, 1053, 1054, 1055, 1056, 1057, 1058, 1059, 1060, 1061, 1062, 1063, 1064, 1065, 1066, 1067, 1068,1069,1070,1071,1072];
$lists = [1201,1202,1203];
$lists = [ 1500, 1501];
$lists = [  2098, 2099, 2097];


$results = array();

foreach ($lists as $list_id) {
    $table_name = "custom_" . $list_id;

    try {
        $sql = "SELECT lead_id, idcrm FROM $table_name WHERE (idcrm = '0' or idcrm = '')";
        $result = $connS2->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $lead_id = $row["lead_id"];
                $results[$lead_id] = array(
                    "list_id" => $list_id,
                    "idcrm" => $row["idcrm"],
                    "cellulare" => 0
                );
            }
        }
    } catch (Exception $e) {
        echo $e;
    }
}


foreach ($lists as $list_id) {
    $table_name = "custom_" . $list_id;

    try {
        $sql = "SELECT lead_id,phone_number FROM vicidial_list WHERE list_id=$list_id and lead_id not in (select lead_id from $table_name) ";
        //echo $sql."<br>";
        $result = $connS2->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $lead_id = $row["lead_id"];
                $results[$lead_id] = array(
                    "list_id" => $list_id,
                    "idcrm" => 0,
                    "cellulare" => $row["phone_number"]
                );
            }
        }
    } catch (Exception $e) {
        echo $e;
    }
}




foreach ($results as $lead_id => $data) {
    $list_id = $data["list_id"];
    $table_name = "custom_" . $list_id;
    $ricerca = "select idSponsorizzata from gestioneLead where leadId='$lead_id'";
    $risultatoRicerca = $conn19->query($ricerca);
    if ($risultatoRicerca->num_rows > 0) {
        while ($riga = $risultatoRicerca->fetch_assoc()) {
            $idCrm = $riga["idSponsorizzata"];
        }
    } else {
        $queryRicerca = "select numeroIngresso,campagna,azienda from numriInbound where lista='$list_id'";
        $risultatoLista = $conn19->query($queryRicerca);
        if ($risultatoLista->num_rows > 0) {
            $riga = $risultatoLista->fetch_array();
            $idCrm = importChiamateDuplicato($lead_id, $riga[0], $riga[1], $riga[2], date('Y-m-d'), $data["cellulare"], $riga[2], $list_id, "no");
        } else {
            $idCrm = 0;
        }
    }
    try {
        $sql = "SELECT lead_id FROM $table_name WHERE lead_id='$lead_id'";
        $r = $connS2->query($sql);
        if ($r->num_rows == 0) {
            $sqlInserimento = "INSERT INTO $table_name(lead_id,idcrm) VALUES ($lead_id,0)";
            $connS2->query($sqlInserimento);
        }

        $update_sql = "UPDATE $table_name SET idcrm = '$idCrm' WHERE lead_id = '$lead_id'";
        if ($connS2->query($update_sql) === TRUE) {
            echo "Record aggiornato correttamente per lead_id: $lead_id nella tabella $table_name <br>";
        } else {
            echo "Errore durante l'aggiornamento per lead_id: $lead_id nella tabella $table_name: " . $conn->error . "\n";
        }
    } catch (Exception $e) {
        echo "Errore con la tabella $table_name: " . $e->getMessage() . "\n";
    }
}

// Chiusura della connessione
$obj19->chiudiConnessione();
$objS2->chiudiConnessioneSiscallLead();
//$conn->close();
?>