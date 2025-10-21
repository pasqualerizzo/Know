<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$mandato = json_decode($_POST["mandato"],true);
//echo var_dump($mandato);
$queryMandato = "";
$lunghezza = count($mandato);

if ($lunghezza == 1) {
    $queryMandato .= " AND idMandato='$mandato[0]' ";
} else {
    for ($i = 0; $i < $lunghezza; $i++) {
        if($i==0){
            $queryMandato.=" AND ( ";
        }
        $queryMandato.=" idMandato='$mandato[$i]' ";
        if($i==($lunghezza-1)){
            $queryMandato.=" ) ";
        } else {
        $queryMandato.=" OR ";    
        }
    }
    
}

$queryGroupMandato = "SELECT sede FROM `stringheTotale` where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello<8  ". $queryMandato ."group by sede";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);
//$conteggioSede = $risultatoQueryGroupSede->num_rows;
$elencoMandato = [];
while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {
    array_push($elencoMandato, $rigaMandato[0]);
}
echo json_encode($elencoMandato);

