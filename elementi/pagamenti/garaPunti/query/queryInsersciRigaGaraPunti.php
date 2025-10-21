<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$fascia = filter_input(INPUT_POST, 'fascia', FILTER_SANITIZE_STRING);
$mese = filter_input(INPUT_POST, 'mese');
$valore = filter_input(INPUT_POST, 'valore');
$puntiMinimi = filter_input(INPUT_POST, 'puntiMinimi');
$puntiMassimi = filter_input(INPUT_POST, 'puntiMassimi');

$data=date('Y-m-1', strtotime($mese));

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "INSERT INTO"
        . " `garaPunti`"
        . " ( `fascia`, `mese`, `puntiMinimi`, `puntiMassimi`, `valore`)"
        . " VALUES"
        . " ('$fascia','$data',$puntiMinimi,$puntiMassimi,$valore)";
$conn19->query($query);

header("location:../tabellaGaraPunti.php");
?>
