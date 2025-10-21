<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Europe/Rome');
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Nome del file JSON da leggere
$filename = 'input1525.json';

// Leggi il contenuto del file JSON
$jsonData = file_get_contents($filename);

// Decodifica il JSON in un array associativo
$data = json_decode($jsonData, true);

// Nome del file CSV da scrivere
$csvFile = 'output.csv';
$handle = fopen($csvFile, 'w');

// Scrivi l'intestazione del CSV
fputcsv($handle, ['Nome', 'Cognome', 'Telefono', 'Data']);

// Cicla attraverso i dati e scrivi nel CSV
foreach ($data as $record) {
    $nome = '';
    $cognome = '';
    $telefono = '';
    $dataRichiesta = '';

    foreach ($record as $key => $value) {
        if (strpos($key, 'first_name') !== false || strpos($key, 'fname') !== false) {
            $nome = $value;
        } elseif (strpos($key, 'last_name') !== false || strpos($key, 'lname') !== false) {
            $cognome = $value;
        } elseif (strpos($key, 'phone_number') !== false || strpos($key, 'tel') !== false) {
            $telefono = $value;
        } elseif (strpos($key, 'dataRichiesta') !== false || strpos($key, 'submission_date') !== false) {
            $dataRichiesta = $value;
        }
    }

    fputcsv($handle, [$nome, $cognome, $telefono, $dataRichiesta]);
}

// Chiudi il file CSV
fclose($handle);

echo "CSV creato con successo: $csvFile";
?>
