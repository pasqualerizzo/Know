
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$queryRicerca = "SELECT * FROM `tabellaCosti` where mese in (select max(mese) from obbiettivoTL)";
$risultato = $conn19->query($queryRicerca);
if($risultato->num_rows>0){
while ($riga = $risultato->fetch_array()) {
    $mese = date('Y-m-d', strtotime($riga['mese'] . " +1 months"));
   
   
    $sede=$riga["sede"];
    $costiStruttura=$riga["costiStruttura"];
    $costiIndiretti=$riga["costiIndiretti"];
    
    
    $query = "INSERT INTO `tabellaCosti`(`mese`,  `costiStruttura`, `costiIndiretti`,sede ) VALUES ('$mese','$costiStruttura','$costiIndiretti`',$sede')";
    $conn19->query($query);
}

}



$obj19->chiudiConnessione();
header("location:index.php");
?>
