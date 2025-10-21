    <?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$queryRicerca = "SELECT * FROM `obbiettivoTL` where mese in (select max(mese) from obbiettivoTL)";
$risultato = $conn19->query($queryRicerca);
if($risultato->num_rows>0){
while ($riga = $risultato->fetch_array()) {
    $mese = date('Y-m-d', strtotime($riga['mese'] . " +1 months"));
   
   
    $gruppoTL = $riga['gruppoTL'];
    $tipo = $riga['tipo'];
    $sede=$riga['sede'];
    
    $query = "INSERT INTO `obbiettivoTL`(`mese`,  `gruppoTL`, `tipo`,sede ) VALUES ('$mese','$gruppoTL','$tipo','$sede')";
    $conn19->query($query);
}

}else{
    $mese = date('Y-m-1');
    $gruppoTL="TL";
    $sede='-';
    $tipo="CTC";
    
        $query = "INSERT INTO `obbiettivoTL`(`mese`,  `gruppoTL`, `tipo` ) VALUES ('$mese','$gruppoTL','$tipo','$sede')";
    $conn19->query($query);
    $tipo="OUT";
    
    
        $query = "INSERT INTO `obbiettivoTL`(`mese`,  `gruppoTL`, `tipo` ) VALUES ('$mese','$gruppoTL','$tipo','$sede')";
    $conn19->query($query);
}



$obj19->chiudiConnessione();
header("location:index.php");
?>
