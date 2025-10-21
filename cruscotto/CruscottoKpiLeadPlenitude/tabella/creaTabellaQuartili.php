<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneGt.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniCruscottoKpiPlenitude.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj = new connessioneSiscall2();
$conn = $obj->apriConnessioneSiscall2();

$objGt = new connessioneGt();
$connGt = $objGt->apriConnessioneGt();

$objL = new connessioneSiscallLead();
$connL = $objL->apriConnessioneSiscallLead();

$html = "";

// Converti le date nel formato italiano
$dataMinore = date('Y-m-d 00:00:00', strtotime(filter_input(INPUT_POST, "dataMinore")));
$dataMaggiore = date('Y-m-d 23:59:59', strtotime(filter_input(INPUT_POST, "dataMaggiore")));

$meseMinore = date('Y-m-01', strtotime($dataMinore));
$meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

$valoreMedio = 0;

$sedi = json_decode(filter_input(INPUT_POST, "sede"), true);
$sediOperatori = [];



foreach ($sedi as $s) {
    $operatore = elencoOperatoreSede($connL, $s);
    foreach ($operatore as $nomeOperatore => $dati) {
        $sediOperatori[$nomeOperatore] = $dati[1] ?? 'Sconosciuta';
        
//        $html .= $nomeOperatore;
//        $html .= "<br>";
    }
}


$query = "SELECT media FROM `mediaPraticaMese` 
                 WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                 AND mandato = 'Plenitude'
                 ORDER BY `id` DESC LIMIT 1";
if ($result = $conn19->query($query)) {
    if ($row = $result->fetch_assoc()) {
        $valoreMedio = $row['media'] ?? 0;
    }
}
$scaglioni = [
    '<10%' => ['min' => 0, 'max' => 10],
    '<15%' => ['min' => 10, 'max' => 15],
    '<20%' => ['min' => 15, 'max' => 20],
    '>20%' => ['min' => 20, 'max' => 100]
];

$cumulativo = [
    '<10%' => ['operatori' => 0, 'lead' => 0, 'cp' => 0, 'ore' => 0, 'ricavorario' => 0, 'cu' => 0],
    '<15%' => ['operatori' => 0, 'lead' => 0, 'cp' => 0, 'ore' => 0, 'ricavorario' => 0, 'cu' => 0],
    '<20%' => ['operatori' => 0, 'lead' => 0, 'cp' => 0, 'ore' => 0, 'ricavorario' => 0, 'cu' => 0],
    '>20%' => ['operatori' => 0, 'lead' => 0, 'cp' => 0, 'ore' => 0, 'ricavorario' => 0, 'cu' => 0]
];

$siscall2 = recuperoOre($conn, $dataMaggiore, $dataMinore);
$siscallGT = recuperoOre($connGt, $dataMaggiore, $dataMinore);
$siscallLead = recuperoOre($connL, $dataMaggiore, $dataMinore);
$lead = recuperoLead($conn19, $dataMaggiore, $dataMinore);
$plenitudeData = recuperoPlenitudeData($conn19, $dataMaggiore, $dataMinore);

foreach ($sediOperatori as $nomeOperatore => $value) {
        if (!array_key_exists($nomeOperatore, $lead)) continue;
        
        $totaleLead = $lead[$nomeOperatore][0];
        $oreIN = 0;
        
        if (array_key_exists($nomeOperatore, $siscall2)) $oreIN += $siscall2[$nomeOperatore][0];
        if (array_key_exists($nomeOperatore, $siscallGT)) $oreIN += $siscallGT[$nomeOperatore][0];
        if (array_key_exists($nomeOperatore, $siscallLead)) $oreIN += $siscallLead[$nomeOperatore][0];
        
        $OkProduzioneData = array_key_exists($nomeOperatore, $plenitudeData) ? $plenitudeData[$nomeOperatore][0] : 0;
        $percentualeOkData = ($totaleLead == 0) ? 0 : round(($OkProduzioneData / $totaleLead) * 100, 2);
        
        foreach ($scaglioni as $scaglione => $range) {
            if ($percentualeOkData >= $range['min'] && $percentualeOkData < $range['max']) {
                $cumulativo[$scaglione]['operatori']++;
                $cumulativo[$scaglione]['lead'] += $totaleLead;
                $cumulativo[$scaglione]['cp'] += $OkProduzioneData;
                $cumulativo[$scaglione]['ore'] += $oreIN;
                break;
            } elseif ($scaglione == '>20%' && $percentualeOkData >= $range['min']) {
                $cumulativo[$scaglione]['operatori']++;
                $cumulativo[$scaglione]['lead'] += $totaleLead;
                $cumulativo[$scaglione]['cp'] += $OkProduzioneData;
                $cumulativo[$scaglione]['ore'] += $oreIN;
                break;
            }
        }
    }

    // Calcolo ricavo orario e contatti utili per ogni scaglione
    foreach ($cumulativo as $scaglione => $dati) {
        $cumulativo[$scaglione]['ricavorario'] = ($dati['ore'] > 0) ? round(($dati['cp'] * $valoreMedio) / $dati['ore'], 2) : 0;
        $cumulativo[$scaglione]['cu'] = ($dati['ore'] > 0) ? round($dati['lead'] / $dati['ore'], 2) : 0;
    }

    // Calcolo dei totali
    $totali = [
        'operatori' => array_sum(array_column($cumulativo, 'operatori')),
        'lead' => array_sum(array_column($cumulativo, 'lead')),
        'cp' => array_sum(array_column($cumulativo, 'cp')),
        'ore' => array_sum(array_column($cumulativo, 'ore')),
        'ricavorario' => (array_sum(array_column($cumulativo, 'ore')) > 0) ? 
            round((array_sum(array_column($cumulativo, 'cp')) * $valoreMedio) / array_sum(array_column($cumulativo, 'ore')), 2) : 0,
        'cu' => (array_sum(array_column($cumulativo, 'ore')) > 0) ? 
            round(array_sum(array_column($cumulativo, 'lead')) / array_sum(array_column($cumulativo, 'ore')), 2) : 0
    ];

    $html = "<div class='scaglioni-container' style='font-family: Arial, sans-serif;'>";
    $html .= "<h3 style='margin-bottom: 10px;'>Performance Quartili</h3>";
    $html .= "<table style='border-collapse: collapse; width: 100%;'>";

    // Intestazione
    $html .= "<tr style='background-color: #f2f2f2;'>";
    $html .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>QUARTILI</th>";
    $html .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: center;'><10%</th>";
    $html .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: center;'><15%</th>";
    $html .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: center;'><20%</th>";
    $html .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: center;'>>20%</th>";
    $html .= "<th style='border: 1px solid #ddd; padding: 8px; text-align: center;'>Totali</th>";
    $html .= "</tr>";

    // Righe dei dati (alterno colori per migliorare la leggibilità)
    $rows = [
        'Operatori' => 'operatori',
        'Lead' => 'lead',
        'CP' => 'cp',
        'Ore' => 'ore',
        'Ric/h' => 'ricavorario',
        'Cu/h' => 'cu'
    ];

    $row_color = true;
    foreach ($rows as $label => $key) {
        $bg_color = $row_color ? '#ffffff' : '#f9f9f9';
        $html .= "<tr style='background-color: $bg_color;'>";
        $html .= "<td style='border: 1px solid #ddd; padding: 8px; font-weight: bold;'>$label</td>";
        
        foreach ($cumulativo as $dati) {
            $value = $dati[$key];
            // Aggiungo il simbolo € solo per la riga Ric/h
            $display_value = ($label == 'Ric/h') ? number_format($value, 2, ',', '.').'€' : $value;
            $html .= "<td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>$display_value</td>";
        }
        
        // Aggiungo la colonna dei totali
        $total_value = $totali[$key];
        $display_total = ($label == 'Ric/h') ? number_format($total_value, 2, ',', '.').'€' : $total_value;
        $html .= "<td style='border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;'>$display_total</td>";
        
        $html .= "</tr>";
        $row_color = !$row_color;
    }

    $html .= "</table></div>";

   




$obj19->chiudiConnessione();
$obj->chiudiConnessioneSiscall2();
$objGt->chiudiConnessioneGt();
$objL->chiudiConnessioneSiscallLead();

echo $html;
?>
