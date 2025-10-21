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



$queryGroupMandato = "SELECT sede FROM `stringheTotale` where giorno>='$dataMinore' and giorno<='$dataMaggiore' and sede <>''   group by sede";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);
//$conteggioSede = $risultatoQueryGroupSede->num_rows;
$elencoMandato = [];
while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {
    array_push($elencoMandato, $rigaMandato[0]);
}
echo json_encode($elencoMandato);

