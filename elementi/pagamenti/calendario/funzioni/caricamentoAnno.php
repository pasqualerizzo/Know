<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataInizio='2024-01-01';

$dataFine = date('Y-m-d', strtotime('last day of December ' . date('Y', strtotime($dataInizio))));

while($dataInizio<=$dataFine){
    $mese=date('Y-m-1', strtotime($dataInizio));
    $sabato=0.5;
    $domenica=0;
    $giornoSettimana=date('N', strtotime($dataInizio));
    switch ($giornoSettimana){
        case 6:
            $query="INSERT INTO `calendario`( `giorno`, `peso`, `mese`) VALUES ('$dataInizio',$sabato,'$mese')";
            break;
        case 7:
            $query="INSERT INTO `calendario`( `giorno`, `peso`, `mese`) VALUES ('$dataInizio',$domenica,'$mese')";
            break;
        default:
            $query="INSERT INTO `calendario`( `giorno`, `peso`, `mese`) VALUES ('$dataInizio',1,'$mese')";
            break;
    }
    $conn19->query($query);
    $dataInizio= date('Y-m-d', strtotime($dataInizio .' +1 day'));
    
}


?>
