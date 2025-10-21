<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Auto-rileva ambiente (locale MAMP o produzione)
$BASE_PATH = (strpos($_SERVER['DOCUMENT_ROOT'], 'MAMP') !== false) 
    ? '/Applications/MAMP/htdocs/Know' 
    : '/var/www/html/Know';

require $BASE_PATH . "/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

// Recupera i parametri
$mese = filter_input(INPUT_POST, "mese");
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

if (!$mese || !$mandato) {
    echo json_encode(['error' => 'Parametri mancanti']);
    exit;
}

$dataMinore = $mese . "-01";
$dataMaggiore = date('Y-m-d', strtotime("last day of " . $mese));

// Query sede
$querySede = "";
$lunghezzaSede = count($sede);
if ($lunghezzaSede == 1) {
    $querySede .= " AND sede='$sede[0]' ";
} elseif ($lunghezzaSede > 1) {
    for ($l = 0; $l < $lunghezzaSede; $l++) {
        if ($l == 0) {
            $querySede .= " AND ( ";
        }
        $querySede .= " sede='$sede[$l]' ";
        if ($l == ($lunghezzaSede - 1)) {
            $querySede .= " ) ";
        } else {
            $querySede .= " OR ";
        }
    }
}

$mandati_validi = ["Plenitude", "Vivigas Energia", "Enel", "Iren", "EnelIn", "Heracom"];
$risultati = [];

// Prima passata: valori medi
$valoriMedi = [];
foreach ($mandato as $idMandato) {
    if (in_array($idMandato, ["Union", "Vodafone", "Bo", "TIM", "TIMmq", 'Sorgenia']) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    $dbMandatoName = ($idMandato == "Vivigas Energia") ? "Vivigas" : $idMandato;
    
    $queryvaloremedio = "SELECT mandato, mese, media 
                        FROM `mediaPraticaMese` 
                        WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
                        AND mandato = '$dbMandatoName'
                        ORDER BY `id` DESC";
    
    $risultatovaloremedio = $conn19->query($queryvaloremedio);
    if ($risultatovaloremedio) {
        while ($rigaCRM = $risultatovaloremedio->fetch_array()) {
            if ($rigaCRM !== null) {
                $valoriMedi[$idMandato] = $rigaCRM[2] ?? 0;
            }
        }
    }
}

// Contatti utili Heracom
$contattiUtiliHeracom = 0;
foreach ($mandato as $idMandato) {
    if ($idMandato !== "Heracom") continue;
    
    $queryCu = "SELECT SUM(`contattiUtili`) AS cu  
                FROM `contattiUtili` 
                WHERE data >= ? AND data <= ? 
                AND mandato = 'Heracom'";
    
    $stmt = $conn19->prepare($queryCu);
    if ($stmt) {
        $stmt->bind_param("ss", $dataMinore, $dataMaggiore);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $contattiUtiliHeracom = $row['cu'] ?? 0;
        }
        $stmt->close();
    }
}

$fatturatoCu = 1.5 * $contattiUtiliHeracom;

// Totali generali
$totali = [
    'w1' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
    'w2' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
    'w3' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
    'w4' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
    'w5' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
    'w6' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0]
];

// Elaborazione dati per mandato
foreach ($mandato as $idMandato) {
    if (in_array($idMandato, ["Union", "Vodafone", "Bo"]) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    $risultati[$idMandato] = [
        'w1' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
        'w2' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
        'w3' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
        'w4' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
        'w5' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0],
        'w6' => ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0]
    ];

    // Query pezzi per settimana
    $queryCrmSede = "";
    switch ($idMandato) {
        case "Plenitude":
            $queryCrmSede = "SELECT
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 1 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 2 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 3 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 4 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 5 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 6 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_6
            FROM `plenitude` p
            INNER JOIN aggiuntaPlenitude ap ON p.id = ap.id
            WHERE p.data <= '$dataMaggiore' AND p.data >= '$dataMinore'
            AND ap.fasePDA = 'OK' AND p.comodity <> 'Polizza' $querySede";
            break;
        case "Vivigas Energia":
            $queryCrmSede = "SELECT
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6
            FROM `vivigas`
            INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore' AND fasePDA = 'OK' $querySede";
            break;
        case "Enel":
            $queryCrmSede = "SELECT
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6
            FROM enel
            INNER JOIN aggiuntaEnel ON enel.id = aggiuntaEnel.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore' AND fasePDA = 'OK' AND comodity <> 'Fibra' $querySede";
            break;
        case "Iren":
            $queryCrmSede = "SELECT
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6
            FROM iren
            INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore' AND fasePDA = 'OK' AND comodity <> 'Fibra' $querySede";
            break;
        case "EnelIn":
            $queryCrmSede = "SELECT
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6
            FROM enelIn
            INNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore' AND fasePDA = 'OK' AND comodity <> 'cONSENSO' $querySede";
            break;
        case "Heracom":
            $queryCrmSede = "SELECT
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6
            FROM heracom
            INNER JOIN aggiuntaHeracom ON heracom.id = aggiuntaHeracom.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore' AND fasePDA = 'OK' AND comodity <> 'Consenso' $querySede";
            break;
        default:
            continue 2;
    }

    $risultatoCrmSede = $conn19->query($queryCrmSede);
    if ($risultatoCrmSede && $rigaCRM = $risultatoCrmSede->fetch_array()) {
        for ($w = 1; $w <= 6; $w++) {
            $risultati[$idMandato]['w' . $w]['pezzi'] = round($rigaCRM[$w - 1] ?? 0, 0);
        }
    }

    // Query ore per settimana
    $queryOreSettimana = "";
    switch ($idMandato) {
        case "Plenitude":
        case "Vivigas Energia":
        case "Iren":
        case "Enel":
            $queryOreSettimana = "SELECT
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 1 THEN numero/3600 ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 2 THEN numero/3600 ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 3 THEN numero/3600 ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 4 THEN numero/3600 ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 5 THEN numero/3600 ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 6 THEN numero/3600 ELSE 0 END) AS WEEK_6
            FROM `stringheTotale`
            WHERE giorno >= '$dataMinore' AND giorno <= '$dataMaggiore'
            AND livello <= 6 AND idMandato = '$idMandato'";
            break;
        case "EnelIn":
            $queryOreSettimana = "SELECT
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 THEN oreDichiarate ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 THEN oreDichiarate ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 THEN oreDichiarate ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 THEN oreDichiarate ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 THEN oreDichiarate ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 THEN oreDichiarate ELSE 0 END) AS WEEK_6
            FROM `oreEnelIn`
            WHERE data >= '$dataMinore' AND data <= '$dataMaggiore'";
            break;
        case "Heracom":
            $queryOreSettimana = "SELECT
                ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 1 THEN numero ELSE 0 END)/3600, 2) AS WEEK_1,
                ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 2 THEN numero ELSE 0 END)/3600, 2) AS WEEK_2,
                ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 3 THEN numero ELSE 0 END)/3600, 2) AS WEEK_3,
                ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 4 THEN numero ELSE 0 END)/3600, 2) AS WEEK_4,
                ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 5 THEN numero ELSE 0 END)/3600, 2) AS WEEK_5,
                ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 6 THEN numero ELSE 0 END)/3600, 2) AS WEEK_6
            FROM `stringheSiscallLeadTC`
            WHERE giorno >= '$dataMinore' AND giorno <= '$dataMaggiore'
            AND mandato = 'Heracom' AND userGroup = 'OP_Lam_piannazzo'";
            break;
    }

    if ($queryOreSettimana) {
        $risultatoOreSettimana = $conn19->query($queryOreSettimana);
        if ($risultatoOreSettimana && $rigaOre = $risultatoOreSettimana->fetch_array()) {
            for ($w = 1; $w <= 6; $w++) {
                $ore = round($rigaOre[$w - 1] ?? 0, 0);
                $risultati[$idMandato]['w' . $w]['ore'] = $ore;
                
                // Calcola fatturato e resa
                $pezzi = $risultati[$idMandato]['w' . $w]['pezzi'];
                $valoreMedio = $valoriMedi[$idMandato] ?? 0;
                $fatturato = $valoreMedio * $pezzi;
                
                // Aggiungi fatturato CU solo per Heracom alla settimana 1
                if ($idMandato === "Heracom" && $w == 1) {
                    $fatturato += $fatturatoCu;
                }
                
                $risultati[$idMandato]['w' . $w]['fatturato'] = round($fatturato, 0);
                $risultati[$idMandato]['w' . $w]['resa'] = $ore > 0 ? round($fatturato / $ore, 0) : 0;
                
                // Aggiorna totali
                $totali['w' . $w]['ore'] += $ore;
                $totali['w' . $w]['pezzi'] += $pezzi;
                $totali['w' . $w]['fatturato'] += $fatturato;
            }
        }
    }
}

// Calcola resa totali
for ($w = 1; $w <= 6; $w++) {
    $totali['w' . $w]['resa'] = $totali['w' . $w]['ore'] > 0 ? 
        round($totali['w' . $w]['fatturato'] / $totali['w' . $w]['ore'], 0) : 0;
}

$risultati['TOTALE'] = $totali;

echo json_encode($risultati, JSON_NUMERIC_CHECK);

