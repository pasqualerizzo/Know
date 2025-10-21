<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$sede = filter_input(INPUT_POST, 'sede', FILTER_SANITIZE_STRING);
$costiStruttura = filter_input(INPUT_POST, 'costiStruttura', FILTER_SANITIZE_STRING);
$mese = filter_input(INPUT_POST, 'mese', FILTER_SANITIZE_STRING);
$costiIndiretti=filter_input(INPUT_POST, 'costiIndiretti', FILTER_SANITIZE_STRING);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "INSERT INTO `tabellaCosti`( `sede`, `mese`, `costiStruttura`,costiIndiretti) VALUES ('$sede','$mese','$costiStruttura','$costiIndiretti')";
$conn19->query($query);

$obj19->chiudiConnessione();
header("location:index.php");
?>