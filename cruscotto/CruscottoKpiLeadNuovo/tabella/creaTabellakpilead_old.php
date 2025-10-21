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
$agenzia = filter_input(INPUT_POST, "agenzieSelezionate");

// Converti le date nel formato italiano
$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

// Inizializza i totali
$totale_lead_Totale = 0;
$totalecp_Totale = 0;
$okcp_Totale = 0;
$kopc_Totale = 0;
$convertito_totale = 0;

// Decodifica le agenzie selezionate
$agenzia = json_decode($_POST["agenzia"], true);
$queryAgenzia = "";
$lunghezza = count($agenzia);

if ($lunghezza == 1) {
    $queryAgenzia .= " AND agenzia='$agenzia[0]' ";
} else {
    for ($i = 0; $i < $lunghezza; $i++) {
        if ($i == 0) {
            $queryAgenzia .= " AND ( ";
        }
        $queryAgenzia .= " agenzia='$agenzia[$i]' ";
        if ($i == ($lunghezza - 1)) {
            $queryAgenzia .= " ) ";
        } else {
            $queryAgenzia .= " OR ";
        }
    }
}

// Genera l'HTML della tabella
$html = "<table class='blueTable'>";
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th>Operatore</th>";
$html .= "<th>Lead</th>";
$html .= "<th>Totale CP</th>";
$html .= "<th>% CP_Lead</th>";
$html .= "<th>Convertito</th>";
$html .= "<th>% Convertito</th>";
$html .= "<th>OK CP</th>";
$html .= "<th>% OK CP</th>";
$html .= "<th>KO CP</th>";
$html .= "<th>Livello</th>";
$html .= "<th>Livello Vicidial</th>";
$html .= "</tr>";
$html .= "</thead>";
$html .= "<tbody>";

// Funzione per calcolare la media delle percentuali CP_LEAD per un determinato intervallo di date
function calcolaMediaCP($conn, $startDate, $endDate, $queryAgenzia) {
    $query = "SELECT"
            . " (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)) / COUNT(`idSponsorizzata`) * 100 AS cp_lead_media"
            . " FROM "
            . " `gestioneLead` "
            . " WHERE"
            . " dataImport >= '$startDate' "
            . " AND dataImport <= '$endDate' "
            . " and idSponsorizzata like 'GCL%' "
            . $queryAgenzia;

    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row ? $row['cp_lead_media'] : 0;
}

// Funzione per determinare il livello di competenza
function determinareCompetenza($percentuale, $media) {
    $differenza = $percentuale - $media;
    if ($differenza <= 5) {
        $livello = 3;
    } elseif ($differenza > 5 and $differenza <= 10) {
        $livello = 4;
    } else {
        $livello = 5;
    }

    return $livello;
}

//// Funzione per calcolare la media delle percentuali CP_LEAD per un determinato intervallo di date
//function calcolaMediaCP($conn, $startDate, $endDate, $queryAgenzia) {
//    $query = "
//    SELECT 
//        (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)) / COUNT(`idSponsorizzata`) * 100 AS cp_lead_media
//    FROM 
//        `gestioneLead`
//    WHERE 
//        dataImport >= '$startDate'
//        AND dataImport <= '$endDate'" .$queryAgenzia;
//    
//    $result = $conn->query($query);
//    $row = $result->fetch_assoc();
//    return $row ? $row['cp_lead_media'] : 0;
//}
// Funzione per ottenere gli ultimi tre giorni lavorativi
function getLastThreeWorkdays($day) {


    $oggi = date('N', strtotime($day));
    if ($oggi > 3 and $oggi > 7) {
        $dataMaggiore = date("Y-m-d 23:59:59", strtotime("Wednesday this week"));
        $days[] = $dataMaggiore;
        $dataMinore = date("Y-m-d 00:00:00", strtotime("monday this week "));
        $days[] = $dataMinore;
    } else {
        $dataMaggiore = date("Y-m-d 23:59:59", strtotime("Saturday  previous week"));
        $days[] = $dataMaggiore;
        $dataMinore = date("Y-m-d 00:00:00", strtotime("Thursday previous week "));
        $days[] = $dataMinore;
    }
    return $days;
}

// Variabile per salvare i livelli Vicidial
$livelliVicidial = [];

// Calcolo del giorno corrente
$currentDay = date('N', strtotime($dataMaggiore));

// Calcolo delle date degli ultimi tre giorni lavorativi precedenti
$lastThreeWorkdays = getLastThreeWorkdays($dataMaggiore);
$startLastThreeWorkdays = $lastThreeWorkdays[0];
$endLastThreeWorkdays = end($lastThreeWorkdays);

// Media degli ultimi tre giorni lavorativi
$mediaUltimiTreGiorni = calcolaMediaCP($conn19, $startLastThreeWorkdays, $endLastThreeWorkdays, $queryAgenzia);

// Media della giornata corrente
$mediaGiornaliera = calcolaMediaCP($conn19, $dataMinore, $dataMaggiore, $queryAgenzia);

// Query per ottenere i dati aggregati
$queryleadokfinale = "
SELECT 
    REPLACE(`gestitoDa`, 'enel', '') AS operatore, 
    COUNT(`idSponsorizzata`) AS Lead, 
    (SUM(`pleniTot`) + SUM(`vodaTot`) + SUM(`viviTot`) + SUM(`irenTot`))  AS TOT_CP, 
    SUM(`convertito`) / COUNT(`idSponsorizzata`) * 100 AS `% CP_LEAD`,
    (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)) AS OK_CP, 
    (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)) / COUNT(`idSponsorizzata`) * 100 AS `% OK_CP`, 
    (SUM(`pleniKo`) + SUM(`vodaKo`) + SUM(`viviKo`) + SUM(`irenKo`)) AS KO_CP, 
    SUM(`convertito`)  AS CONVERTITO,
    (SUM(`pleniTot`) + SUM(`vodaTot`) + SUM(`viviTot`) + SUM(`irenTot`))/ COUNT(`idSponsorizzata`) * 100 AS `% CONVERTITO`
FROM 
    `gestioneLead`
WHERE 
    `gestitoDa` NOT IN ('', 'VDAD', '6666') 
    AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI')
    AND dataImport <= '$dataMaggiore'
    AND dataImport >= '$dataMinore' 
    and idSponsorizzata like 'GCL%' 
    $queryAgenzia 
GROUP BY 
    `gestitoDa` 
ORDER BY 
    `valoreMedioIren` ASC";

$risultatoCrm = $conn19->query($queryleadokfinale);

// Array per gestire operatore già inserito
$operatoreArray = [];

while ($rigaCRM = $risultatoCrm->fetch_assoc()) {
    $operatore = $rigaCRM['operatore'];

    // Verifica se l'operatore è già stato processato
    if (in_array($operatore, $operatoreArray)) {
        continue; // Salta il record se l'operatore è già stato aggiunto
    }

    $operatoreArray[] = $operatore; // Aggiungi l'operatore all'array

    $totale_lead = round($rigaCRM['Lead'], 1);
    $totalecp = round($rigaCRM['TOT_CP'], 1);
    $perccp = round($rigaCRM['% CP_LEAD'], 1);
    $okcp = round($rigaCRM['OK_CP'], 1);
    $percokcp = round($rigaCRM['% OK_CP'], 1);
    $kopc = round($rigaCRM['KO_CP'], 1);
    $convertito = round($rigaCRM['CONVERTITO'], 1);
    $perconvertito = round($rigaCRM['% CONVERTITO'], 1);

    // Calcola livello Vicidial solo il lunedì e giovedì
    if ($currentDay == 1 || $currentDay == 4) { // 1 = Lunedì, 4 = Giovedì
        $livelloVicidial = determinareCompetenza($mediaUltimiTreGiorni, $perccp);
        $livelliVicidial[$operatore] = $livelloVicidial;
    } else {
        // Usa l'ultimo livello calcolato se non è lunedì o giovedì
        if (!isset($livelliVicidial[$operatore])) {
            $livelliVicidial[$operatore] = determinareCompetenza($mediaUltimiTreGiorni, $perccp);
        }
        $livelloVicidial = $livelliVicidial[$operatore];
    }

    $livelloCompetenza = determinareCompetenza($perccp, $mediaGiornaliera);

    $html .= "<tr>";
    $html .= "<td>$operatore</td>";
    $html .= "<td>$totale_lead</td>";
    $html .= "<td>$totalecp</td>";
    $html .= "<td>$perccp%</td>";
    $html .= "<td>$convertito</td>";
    $html .= "<td>$perconvertito%</td>";
    $html .= "<td>$okcp</td>";
    $html .= "<td>$percokcp%</td>";
    $html .= "<td>$kopc</td>";
    $html .= "<td>$livelloCompetenza</td>";
    $html .= "<td>$livelloVicidial</td>";
    $html .= "</tr>";

    $totale_lead_Totale += $totale_lead;
    $totalecp_Totale += $totalecp;
    $okcp_Totale += $okcp;
    $kopc_Totale += $kopc;
    $convertito_totale += $convertito;
}

// Calcolo dei totali complessivi
$percentuale_cp_totale = ($totale_lead_Totale > 0) ? ($totalecp_Totale / $totale_lead_Totale) * 100 : 0;
$percentuale_convertito_totale = ($totale_lead_Totale > 0) ? ($convertito_totale / $totale_lead_Totale) * 100 : 0;
$percentuale_okcp_totale = ($totale_lead_Totale > 0) ? ($okcp_Totale / $totale_lead_Totale) * 100 : 0;

// Riga dei totali complessivi
$html .= "<tr style='background-color: orange; font-weight: bold;'>";
$html .= "<td>TOTALE</td>";
$html .= "<td>$totale_lead_Totale</td>";
$html .= "<td>$totalecp_Totale</td>";
$html .= "<td>" . round($percentuale_cp_totale, 1) . "%</td>";
$html .= "<td>$convertito_totale</td>";
$html .= "<td>" . round($percentuale_convertito_totale, 1) . "%</td>";
$html .= "<td>$okcp_Totale</td>";
$html .= "<td>" . round($percentuale_okcp_totale, 1) . "%</td>";
$html .= "<td>$kopc_Totale</td>";
$html .= "<td></td>"; // Lasciare vuoto per i totali
$html .= "<td></td>"; // Lasciare vuoto per i totali
$html .= "</tr>";

$html .= "</tbody>";
$html .= "</table>";

echo $html;

$conn19->close();
?>
