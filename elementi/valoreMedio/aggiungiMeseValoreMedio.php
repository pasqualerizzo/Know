<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$queryRicerca = "SELECT * FROM `mediaPraticaMese` where mese in (select max(mese) from mediaPraticaMese)";
$risultato = $conn19->query($queryRicerca);
while ($riga = $risultato->fetch_array()) {
    $mese = date('Y-m-d', strtotime($riga[2] . " +1 months"));
   
   
    $mandato = $riga[1];
    $media = $riga[3];
    
    $query = "INSERT INTO `mediaPraticaMese`(`mese`,  `mandato`, `media` ) VALUES ('$mese','$mandato','$media')";
    $conn19->query($query);
}




header("location:tabellaValoreMedio.php");
?>
