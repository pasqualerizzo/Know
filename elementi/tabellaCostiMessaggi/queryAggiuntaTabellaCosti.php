<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$utm = filter_input(INPUT_POST, 'utm', FILTER_SANITIZE_STRING);
$costoVariabile = filter_input(INPUT_POST, 'costoVariabile');
$costoFisso=filter_input(INPUT_POST, 'costoFisso');
$dataImport = filter_input(INPUT_POST, 'dataImport');
$pezzi= filter_input(INPUT_POST, 'pezzi');


require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$mese=date('Y-m-1',strtotime($dataImport));
$spesa=($pezzi*$costoVariabile)+$costoFisso;


$query = "INSERT INTO `tabellaCostiMessaggi`(`UTM`, `costoVariabile`, `costiFisso`, `mese`, `dataImport`,pezzi, `spesa`) VALUES ('$utm','$costoVariabile','$costoFisso','$mese','$dataImport','$pezzi','$spesa')";
$conn19->query($query);

$obj19->chiudiConnessione();
header("location:index.php");