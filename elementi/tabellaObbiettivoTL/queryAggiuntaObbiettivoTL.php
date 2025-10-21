<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$gruppoTL = filter_input(INPUT_POST, 'gruppotl', FILTER_SANITIZE_STRING);
$tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
$mese = filter_input(INPUT_POST, 'mese', FILTER_SANITIZE_STRING);
$sede=filter_input(INPUT_POST, 'sede', FILTER_SANITIZE_STRING);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "INSERT INTO `obbiettivoTL`( `gruppoTL`, `mese`, `tipo`,sede) VALUES ('$gruppoTL','$mese','$tipo','$sede')";
$conn19->query($query);

$obj19->chiudiConnessione();
header("location:index.php");
?>