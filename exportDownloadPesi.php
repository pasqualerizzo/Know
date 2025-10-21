<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$meseSelezionato = $_GET["meseSelezionato"];

$query = "SELECT creatoDa,round(sum(totalePesoLordo),5) as 'Peso Lordo' ,round(sum(pesoTotalePagato),5)as 'Peso Pagato (Escluse Polizze)',round(sum(pesoFormazione),5)as 'Peso Formazione',0 as 'Peso Pagato Polizze', 'GreenNetwork' as 'Mandato' FROM `green` inner join aggiuntaGreen on green.id=aggiuntaGreen.id where mese='$meseSelezionato' group by creatoDa";
//echo $query;
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;
$intestazione = $risultato->fetch_fields();
$el = 0;

$directory = "../elementi/";
$file = "/Applications/MAMP/htdocs/Know/elementi/file.csv";
$intestazioneChiamate = "";

foreach ($intestazione as $info) {
    $intestazioneChiamate .= $info->name;
    $intestazioneChiamate .= ";";
    $el++;
}
$intestazioneChiamate .= "\n";
while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}




$query = "SELECT creatoDa,round(sum(totalePesoLordo),5) as 'Peso Lordo' ,round(sum(if(comodity<>'Polizza',pesoTotalePagato,0)),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione',round(sum(if(comodity='Polizza',pesoTotalePagato,0)),5)as 'Peso Pagato polizze', 'Plenitude' as 'Mandato' FROM `plenitude` inner join aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id where mese='$meseSelezionato' group by creatoDa";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}
$query = "SELECT creatoDa,round(sum(totalePesoLordo),5) as 'Peso Lordo' ,round(sum(pesoTotalePagato),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione','0' as 'Peso Pagato polizze', 'Vivigas' as 'Mandato' FROM `vivigas` inner join aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id where mese='$meseSelezionato' group by creatoDa";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}
$query = "SELECT creatoDa,round(sum(totalePesoLordo),5) as 'Peso Lordo' ,round(sum(pesoTotalePagato),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione',0 as 'Peso Pagato polizze', 'EnelOut' as 'Mandato' FROM `enelOut` inner join aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id where mese='$meseSelezionato' group by creatoDa";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}


$query = "SELECT creatoDa,round(sum(pesoTotaleLordo),5) as 'Peso Lordo' ,round(sum(pesoPagato),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione',0 as 'Peso Pagato polizze', 'Vodafone' as 'Mandato' FROM `vodafone` inner join aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id where mese='$meseSelezionato' group by creatoDa";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}
//echo $intestazioneChiamate;
$query = "SELECT creatoDa,round(sum(totalePesoLordo),5) as 'Peso Lordo' ,round(sum(pesoTotalePagato),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione',0 as 'Peso Pagato polizze', 'iren' as 'Mandato' FROM `iren` inner join aggiuntaIren on iren.id=aggiuntaIren.id where mese='$meseSelezionato' group by creatoDa";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}

$query = "SELECT creatoDa,round(sum(totalePesoLordo),5) as 'Peso Lordo' ,round(sum(pesoTotalePagato),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione',0 as 'Peso Pagato polizze', 'union' as 'Mandato' FROM `union` inner join aggiuntaUnion on union.id=aggiuntaUnion.id where mese='$meseSelezionato' group by creatoDa";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}


$query = "SELECT creatoDa,round(sum(totalePesoLordo),5) as 'Peso Lordo' ,round(sum(if(comodity<>'Polizza',pesoTotalePagato,0)),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione',round(sum(if(comodity='Polizza',pesoTotalePagato,0)),5)as 'Peso Pagato polizze', 'Enel' as 'Mandato' FROM `enel` inner join aggiuntaEnel on enel.id=aggiuntaEnel.id where mese='$meseSelezionato' group by creatoDa";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}

$query = "SELECT assegnato,round(sum(totalePesoLordo),5) as 'Peso Lordo' ,round(sum(if(comodity<>'Polizza',pesoTotalePagato,0)),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione',round(sum(if(comodity='Polizza',pesoTotalePagato,0)),5)as 'Peso Pagato polizze', 'Heracom' as 'Mandato' FROM `heracom` inner join aggiuntaHeracom on heracom.id=aggiuntaHeracom.id where mese='$meseSelezionato' group by assegnato";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}

/**
 * Aggiunta tim 2025/09/12
 */

$query = "SELECT creatoDa,round(sum(pesoTotaleLordo),5) as 'Peso Lordo' ,round(sum(pesoPagato),5)as 'Peso Pagato',round(sum(pesoFormazione),5)as 'Peso Formazione',0 as 'Peso Pagato polizze', 'Tim' as 'Mandato' FROM `tim` inner join aggiuntaTim on tim.id=aggiuntaTim.id where mese='$meseSelezionato' group by creatoDa";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;

while ($lista = $risultato->fetch_array()) {

    $intestazioneChiamate .= $lista[0];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[1]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[2]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[3]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[4]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= "\n";
}



file_put_contents($file, $intestazioneChiamate);
header('Content-Description: File Transfer');
header('Content-type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header("Content-Type: " . mime_content_type($file));
header("Content-type: text/csv");
header('Content-Disposition: attachment; filename=pesiTotale.csv');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
ob_clean();
flush();
readfile($file);

$obj19->chiudiConnessione();

