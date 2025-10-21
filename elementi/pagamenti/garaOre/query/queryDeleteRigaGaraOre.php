<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = filter_input(INPUT_POST, 'id');


require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "DELETE FROM `garaOre` WHERE id='$id'";
$conn19->query($query);

header("location:../tabellaGaraOre.php");
?>
