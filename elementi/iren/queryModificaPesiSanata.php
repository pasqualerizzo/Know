<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$peso = filter_input(INPUT_POST, 'peso', FILTER_SANITIZE_STRING);
$idDescrizione=filter_input(INPUT_POST, 'idDescrizione', FILTER_SANITIZE_STRING);
$descrizione=filter_input(INPUT_POST, 'descrizione', FILTER_SANITIZE_STRING);


require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "UPDATE `irenPesiSanata` SET peso='$peso',tipoDetrazione='$idDescrizione',ds_tipoDetrazione='$descrizione' WHERE id='$id'";
$conn19->query($query);

header("location:tabellaPesiSanata.php");

?>