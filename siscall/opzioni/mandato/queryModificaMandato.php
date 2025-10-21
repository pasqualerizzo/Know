<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
$tipoCampagna = filter_input(INPUT_POST, 'tipoCampagna', FILTER_SANITIZE_STRING);



require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "UPDATE `mandato` SET tipo='$tipo',tipoCampagna='$tipoCampagna' WHERE id='$id'";
$conn19->query($query);

header("location:tabellaMandato.php");

?>