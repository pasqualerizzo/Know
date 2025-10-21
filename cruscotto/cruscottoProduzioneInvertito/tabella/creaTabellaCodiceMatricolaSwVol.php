<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));
$mese = date('Y-m-01', strtotime($dataMaggiore));

// Verifica se il mandato "Plenitude" è selezionato
if (in_array("Plenitude", $mandato)) {
    // Preparazione del filtro per la sede
    $querySede = "";
    $lunghezzaSede = count($sede);

    if ($lunghezzaSede == 1) {
        $querySede .= " AND sede='$sede[0]' ";
    } else {
        $querySede .= " AND (" . implode(" OR ", array_map(fn($s) => "sede='$s'", $sede)) . ")";
    }

    // HTML per la tabella dei dettagli delle matricole Plenitude
    $html = "<table class='blueTable' style='width: 40%'>";
    $html .= "<thead><tr><th colspan='4'>DETTAGLIO MATRICOLE PLENI</th></tr><tr><th>Matricola Agente</th><th>Mensile</th><th>Mortalità</th><th>% Mortalità</th></tr></thead>";

    // Query per ottenere i valori mensili
    $queryCrm = "SELECT codMaticola, COUNT(data) AS totaleGiorni, 
                 SUM(IF(dataSwitchOutLuce='0000-00-00',0,1)) AS SWOLuce, 
                 SUM(IF(dataSwitchOutGas='0000-00-00',0,1)) AS SWOGas 
                 FROM plenitude 
                 WHERE comodity <> 'Polizza' 
                 AND data BETWEEN '$dataMinore' AND '$dataMaggiore'
                 AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                 AND tipoAcquisizione <>'subentro'
                 GROUP BY codMaticola";
    
    $risultatoCrm = $conn19->query($queryCrm);

    $totaleGiorni = 0;
    $totaleSwoO = 0;

    while ($rigaCRM = $risultatoCrm->fetch_array()) {
        $codMaticola = $rigaCRM['codMaticola'];
        $valore = $rigaCRM['totaleGiorni'];
        $swoLuce = $rigaCRM['SWOLuce'];
        $swoGas = $rigaCRM['SWOGas'];
        $coloreCella = ($valore > 120) ? 'style="background-color:red"' : '';

        $totaleGiorni += $valore;
        $valoreSwitchOut = $swoLuce + $swoGas;
        $percentualeMortalita = ($valore == 0) ? 0 : round(($valoreSwitchOut / $valore) * 100, 2);
        $totaleSwoO += $valoreSwitchOut;

        $html .= "<tr><td>{$codMaticola}</td><td {$coloreCella}>{$valore}</td><td>{$valoreSwitchOut}</td><td>{$percentualeMortalita}%</td></tr>";
    }

    $percentualeSwo = ($totaleGiorni == 0) ? 0 : round(($totaleSwoO / $totaleGiorni) * 100, 2);
    $html .= "<tr><td colspan='1' style='background-color:yellow'>Totale</td><td style='background-color:yellow'>{$totaleGiorni}</td><td style='background-color:yellow'>{$totaleSwoO}</td><td style='background-color:yellow'>{$percentualeSwo}%</td></tr>";
    $html .= "</table><br>";

    // Tabella per Macro Stato KO
    $html .= "<table class='blueTable' style='width: 40%'>";
    $html .= "<thead><tr><th colspan='5'>MACRO STATO KO</th></tr><tr><th>Macro Stato</th><th>Totale Data Intervallo</th><th>Pezzi Inbound</th><th>Pezzi Outbound</th><th>%</th></tr></thead>";

    $queryMacroKo = "SELECT 
    CONCAT(IFNULL(REPLACE(faseMacroStatoGas, '-', ''), ''), IFNULL(REPLACE(faseMacroStatoLuce, '-', ''), '')) AS macroStati, 
    count(*) AS totaleDataIntervallo,
    SUM(IF(idGestioneLead LIKE 'G%', 1, 0)) AS pezziInbound,
    SUM(IF(idGestioneLead NOT LIKE 'G%', 1, 0)) AS pezziOutbound  
FROM plenitude
INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id
WHERE data BETWEEN '$dataMinore' AND '$dataMaggiore' AND comodity <> 'Polizza' AND fasePDA = 'OK' AND fasePost = 'KO' AND tipoAcquisizione <>'subentro' $querySede
GROUP BY macroStati";
//echo $queryMacroKo ;
    $risultatoMacroKo = $conn19->query($queryMacroKo);
    $totaleMacroKo = 0;
    $totaleInbound = 0;
    $totaleOutbound = 0;

    while ($rigaMacroKo = $risultatoMacroKo->fetch_array()) {
        $macroStati = $rigaMacroKo['macroStati'];
        $totaleDataIntervallo = $rigaMacroKo['totaleDataIntervallo'];
        $pezziInbound = $rigaMacroKo['pezziInbound'];
        $pezziOutbound = $rigaMacroKo['pezziOutbound'];
        
        $totaleMacroKo += $totaleDataIntervallo;
        $totaleInbound += $pezziInbound;
        $totaleOutbound += $pezziOutbound;

        $percentualeMacroKo = ($totaleMacroKo == 0) ? 0 : round(($totaleDataIntervallo / $totaleMacroKo) * 100, 2);
        $html .= "<tr><td>{$macroStati}</td><td>{$totaleDataIntervallo}</td><td>{$pezziInbound}</td><td>{$pezziOutbound}</td><td>{$percentualeMacroKo}%</td></tr>";
    }

    $html .= "<tr><td style='background-color:yellow'>Totale</td><td style='background-color:yellow'>{$totaleMacroKo}</td><td style='background-color:yellow'>{$totaleInbound}</td><td style='background-color:yellow'>{$totaleOutbound}</td><td style='background-color:yellow'></td></tr>";
    $html .= "</table><br>";

    // Stampa il risultato finale
    echo $html;
} else {
    // Messaggio di avviso per mandati non gestiti
    echo "<p></p>";
}

?>
