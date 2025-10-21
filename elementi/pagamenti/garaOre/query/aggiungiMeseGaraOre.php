<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

//$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
//$peso = filter_input(INPUT_POST, 'peso', FILTER_SANITIZE_STRING);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$queryRicerca = "SELECT * FROM `garaOre` where mese in (select max(mese) from garaOre)";
$risultato = $conn19->query($queryRicerca);
while ($riga = $risultato->fetch_array()) {
    $meseNuovo = date('Y-m-d', strtotime($riga[3] . " +1 months"));

    $nome = $riga[1];
    $fascia = $riga[2];
    $mese = $riga[3];
    $pezziMinimi = $riga[4];
    $pezziMassimi = $riga[5];
    $oreMinime = $riga[6];
    $oreAutorizzate = $riga[7];
    $valore = $riga[8];

    $query = "INSERT INTO `garaOre`(`mese`,  `nome`, `fascia`, `valore`,pezziMinimi,pezziMassimi,oreMinime,oreAutorizzate ) VALUES ('$meseNuovo','$nome','$fascia','$valore','$pezziMinimi','$pezziMassimi','$oreMinime','$oreAutorizzate')";
    $conn19->query($query);
}




header("location:../tabellaGaraOre.php");
?>
