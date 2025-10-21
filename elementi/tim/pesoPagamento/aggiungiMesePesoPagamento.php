<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$peso = filter_input(INPUT_POST, 'peso', FILTER_SANITIZE_STRING);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$queryRicerca = "SELECT * FROM `timPesiMetodoPagamento` where dataInizioValidita in (select max(dataInizioValidita) from timPesiMetodoPagamento)";
$risultato = $conn19->query($queryRicerca);
while ($riga = $risultato->fetch_array()) {
    $dataInizioValidita = date('Y-m-d', strtotime($riga[1] . " +1 months"));


    $tipoCampagna = $riga[3];
    $valore = $riga[4];
    $peso = $riga[5];

    $query = "INSERT INTO `timPesiMetodoPagamento`(`dataInizioValidita`,  `tipoCampagna`, `valore`, `peso`) VALUES ('$dataInizioValidita','$tipoCampagna','$valore','$peso')";
    $conn19->query($query);
}
header("location:tabellaPesiPagamento.php");
?>