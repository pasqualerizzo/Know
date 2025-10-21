<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


$nomeCampagna = filter_input(INPUT_POST, 'nomeCampagna', FILTER_SANITIZE_STRING);
$pezzi = filter_input(INPUT_POST, 'pezzi', FILTER_SANITIZE_STRING);
$costo = filter_input(INPUT_POST, 'costo', FILTER_SANITIZE_STRING);
$dataInserimento = filter_input(INPUT_POST, 'dataInserimento', FILTER_SANITIZE_STRING);


require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "INSERT INTO `campagnaMarketing`(`nomeCampagna`, `pezzi`, `costo`, `dataInserimento`) VALUES ('$nomeCampagna','$pezzi','$costo','$dataInserimento')";
$conn19->query($query);

header("location:../index.php");

?>