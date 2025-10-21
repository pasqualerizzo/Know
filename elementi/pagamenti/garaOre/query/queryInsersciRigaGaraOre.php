<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$fascia = filter_input(INPUT_POST, 'fascia', FILTER_SANITIZE_STRING);
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$mese = filter_input(INPUT_POST, 'data');
$valore = filter_input(INPUT_POST, 'valore');
$pezziMinimi = filter_input(INPUT_POST, 'pezziMinimi');
$pezziMassimi = filter_input(INPUT_POST, 'pezziMassimi');
$oreMinime = filter_input(INPUT_POST, 'oreMinime');
$pezziAutorizzate = filter_input(INPUT_POST, 'pezziAutorizzate');

$data=date('Y-m-1', strtotime($mese));

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "INSERT INTO"
        . " `garaOre` "
        . " (`nome`, `fascia`, `mese`, `pezziMinimi`, `pezziMassimi`, `oreMinime`, `oreAutorizzate`, `valore`) "
        . " VALUES "
        . " ('$nome',''$fascia,'$data','$pezziMinimi','$pezziMassimi','$oreMinime','$oreAutorizzate','$valore')";
$conn19->query($query);

header("location:../tabellaGaraOre.php");
?>
