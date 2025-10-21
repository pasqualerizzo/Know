<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$queryRicerca = "SELECT * FROM `garaPunti` where mese in (select max(mese) from garaPunti)";
$risultato = $conn19->query($queryRicerca);
while ($riga = $risultato->fetch_array()) {
    $meseNuovo = date('Y-m-d', strtotime($riga[2] . " +1 months"));

    $fascia = $riga[1];
    $mese = $riga[2];
    $puntiMinimi = $riga[4];
    $puntiMassimi = $riga[4];
    $valore = $riga[5];

    $query = ""
            . " INSERT INTO "
            . " `garaPunti`"
            . " (`fascia`, `mese`, `puntiMinimi`, `puntiMassimi`, `valore`) "
            . " VALUES "
            . " ('$fascia','$meseNuovo','$puntiMinimi','$puntiMassimi','$valore')";
    $conn19->query($query);
}
header("location:tabellaGaraPunti.php");
?>
