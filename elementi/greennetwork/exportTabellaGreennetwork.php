<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$meseSelezionato = $_GET["meseSelezionato"];

$query = "SELECT * FROM `green` inner join aggiuntaGreen on green.id=aggiuntaGreen.id where mese='$meseSelezionato'";
$risultato = $conn19->query($query);
$conteggio = $risultato->num_rows;
$intestazione = $risultato->fetch_fields();
$el = 0;

$directory = "/";
$file = "file.csv";
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

    $intestazioneChiamate .= $lista[1];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[2];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[3];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[4];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[5];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[6];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[7];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[8];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[9];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[10];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[11];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[12];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[13];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[14];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[15];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[16];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[17];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[18];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[19];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[20];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[21];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[22];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[23];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[24];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[25]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[26]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[27]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[28]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[29];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[30];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[31]);
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[32];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= $lista[33];
    $intestazioneChiamate .= ";";
    $intestazioneChiamate .= str_replace(".", ",", $lista[34]);
    $intestazioneChiamate .= ";";
        $intestazioneChiamate .= $lista[35];
    $intestazioneChiamate .= ";";
        $intestazioneChiamate .= $lista[36];
    $intestazioneChiamate .= ";";

    $intestazioneChiamate .= "\n";
}
file_put_contents($file, $intestazioneChiamate);
header('Content-Description: File Transfer');
header('Content-type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header("Content-Type: " . mime_content_type($file));
header("Content-type: text/csv");
header('Content-Disposition: attachment; filename=' . $file);
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
ob_clean();
flush();
readfile($file);

