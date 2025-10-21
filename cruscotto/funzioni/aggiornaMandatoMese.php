<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$mese = filter_input(INPUT_POST, "mese");
//$mese_anno_selezionato = filter_input(INPUT_POST, "mese_anno");
//$mese="2024-07";
$dataMinore=$mese."-01";

$dataMaggiore=date('Y-m-d',strtotime("last day of ".$mese));

$queryGroupMandato = "SELECT idMandato FROM `stringheTotale` where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello=1 and idMandato<>'vodafone' and idMandato<>'' and idMandato<>'RECUPERI' and idMandato<>'ENEL' and idMandato <>'Heracom'group by idMandato";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);
//$conteggioSede = $risultatoQueryGroupSede->num_rows;
$elencoMandatomese = [];
while ($rigaMandatomese = $risultatoQueryGroupMandato->fetch_array()) {
    array_push($elencoMandatomese, $rigaMandatomese[0]);
}
array_push($elencoMandatomese, "Vodafone");
//echo json_encode($elencoMandato);
array_push($elencoMandatomese, "Union");
//array_push($elencoMandato, "Plenitude");
array_push($elencoMandatomese, "Enel");
//array_push($elencoMandato, "Plenitude")
array_push($elencoMandatomese, "Iren");
//array_push($elencoMandato, "Plenitude")
array_push($elencoMandatomese, "EnelIn");
//array_push($elencoMandato, "Plenitude")
array_push($elencoMandatomese, "Heracom");
//array_push($elencoMandato, "Plenitude")
echo json_encode($elencoMandatomese);
