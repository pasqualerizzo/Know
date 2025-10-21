<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


$media = filter_input(INPUT_POST, 'media', FILTER_SANITIZE_STRING);
$mandato = filter_input(INPUT_POST, 'mandato', FILTER_SANITIZE_STRING);
$mese = filter_input(INPUT_POST, 'mese', FILTER_SANITIZE_STRING);



require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "INSERT INTO `mediaPraticaMese`( `mandato`, `mese`, `media`) VALUES ('$mandato','$mese','$media')";
$conn19->query($query);

header("location:tabellaValoreMedio.php");

?>