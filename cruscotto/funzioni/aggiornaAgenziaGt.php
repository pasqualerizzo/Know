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

$dataMinoreModificata= date("Y-m-d 00:00:00" ,strtotime($dataMinore));
$dataMaggioreModificato= date("Y-m-d 23:59:59", strtotime($dataMaggiore));

$queryGroupMandato = "SELECT agenzia FROM `gestioneLead` where dataImport>='$dataMinoreModificata' and dataImport<='$dataMaggioreModificato' AND agenzia in( 'GTEnergie' , 'NovaDirect')  group by agenzia";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);
//$conteggioSede = $risultatoQueryGroupSede->num_rows;
$elencoMandato = [];
while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {
    array_push($elencoMandato, $rigaMandato[0]);
}
echo json_encode($elencoMandato);

