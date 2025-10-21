<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$valore = filter_input(INPUT_POST, 'valore');
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$fascia = filter_input(INPUT_POST, 'fascia', FILTER_SANITIZE_STRING);
$mese = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
$pezziMinimi = filter_input(INPUT_POST, 'pezziMinimi');
$pezziMassimi = filter_input(INPUT_POST, 'pezziMassimi');
$oreMinime = filter_input(INPUT_POST, 'oreMinime');
$oreAutorizzate = filter_input(INPUT_POST, 'oreAutorizzate');


require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$query = "UPDATE "
        . " `garaOre` "
        . " SET "
        . " `nome`='$nome',`fascia`='$fascia',`mese`='$mese',`pezziMinimi`='$pezziMinimi',`pezziMassimi`='$pezziMassimi',"
        . " `oreMinime`='$oreMinime',`oreAutorizzate`='$oreAutorizzate',`valore`=$valore "
        . " WHERE "
        . " id='$id'";
echo $query;
$conn19->query($query);

header("location:../tabellaGaraOre.php");

?>
