<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");

$dataMinoreModificata = date("Y-m-d 00:00:00", strtotime($dataMinore));
$dataMaggioreModificato = date("Y-m-d 23:59:59", strtotime($dataMaggiore));

$agenzia = json_decode($_POST["agenzia"], true);
$queryAgenzia = "";
$lunghezza = count($agenzia);
$a = "";
$adavaceMe = false;


if ($lunghezza == 1) {
    $queryAgenzia .= " AND agenzia='$agenzia[0]' ";
} else {
    for ($i = 0; $i < $lunghezza; $i++) {
        if ($i == 0) {
            $queryAgenzia .= " AND ( ";
        }
        $queryAgenzia .= " agenzia='$agenzia[$i]' ";
        if ($i == ($lunghezza - 1)) {
            $queryAgenzia .= " ) ";
        } else {
            $queryAgenzia .= " OR ";
        }
    }
}


//if ($lunghezza == 1) {
//    switch ($agenzia[0]) {
//        case "AdviceMe":
//            $adavaceMe = true;
//            break;
//        case "Arkys":
//            $a = "NovaMarketing";
//            break;
//        case "NovaMarketing":
//            $a = "NovaMarketing";
//            break;
//        case "Muza":
//            $a = "Muza";
//            break;
//        case "DgtMedia":
//            $a = "DgtMedia";
//            break;
//    }
//    $queryAgenzia .= " AND agenzia='$a' ";
//} else {
//    for ($i = 0; $i < $lunghezza; $i++) {
//        if ($i == 0) {
//            $queryAgenzia .= " AND ( ";
//        }
//        switch ($agenzia[$i]) {
//            case "AdviceMe":
//                $adavaceMe = true;
//                break;
//            case "Arkys":
//                $a = "NovaMarketing";
//                break;
//            case "Muza":
//                $a = "Muza";
//                break;
//            case "NovaMarketing":
//                $a = "NovaMarketing";
//                break;
//            case "Servizio_Clienti_Energia":
//                $a = "NovaMarketing";
//                break;
//            case "DgtMedia":
//                $a = "DgtMedia";
//                break;
//        }
//        $queryAgenzia .= " agenzia='$a' ";
//        if ($i == ($lunghezza - 1)) {
//            $queryAgenzia .= " ) ";
//        } else {
//            $queryAgenzia .= " OR ";
//        }
//    }
//}


$queryGroupMandato = "SELECT "
        . " categoriaCampagna "
        . " FROM "
        . " `gestioneLead` "
        . " where "
        . " dataImport>='$dataMinoreModificata' and dataImport<='$dataMaggioreModificato'" . $queryAgenzia
        . " group by categoriaCampagna";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);
//$conteggioSede = $risultatoQueryGroupSede->num_rows;
$elencoMandato = [];
while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {
    array_push($elencoMandato, $rigaMandato[0]);
}
echo json_encode($elencoMandato);

