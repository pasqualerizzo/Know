<?php

header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}



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

$mese = filter_input(INPUT_POST, "mese");
$lunghezzaSede = 0;
$dataMinore = $mese . "-01";
$dataMaggiore = date('Y-m-d', strtotime("last day of " . $mese));

$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

// Inizializzazione variabili
$totaliPerSede = [];
$valoriMedi = [];
$totaliObiettivi = [];

if ($lunghezzaSede == 1) {
    $querySede .= " AND sede='$sede[0]' ";
} else {
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

// Query per i valori medi
foreach ($mandato as $idMandato) {
    if ($idMandato === "Union" || $idMandato === "Vodafone" || $idMandato === "Bo" || $idMandato === "TIM" || $idMandato === "TIMmq")  {
        continue;
    }

    $mandatoForQuery = $idMandato;
    if ($idMandato === "Vivigas Energia") {
        $mandatoForQuery = "Vivigas";
    }

    $queryvaloremedio = "SELECT mandato, mese, media 
                         FROM `mediaPraticaMese` 
                         WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
                         AND mandato = ? 
                         ORDER BY `id` DESC";
    
    $stmt = $conn19->prepare($queryvaloremedio);
    $stmt->bind_param("s", $mandatoForQuery); // Usa il nome normalizzato
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $valoriMedi[$idMandato] = $row['media']; // Ma conserva il nome originale come chiave
        error_log("Trovato valore medio per $idMandato ($mandatoForQuery): " . $row['media']);
    } else {
        error_log("Nessun valore medio trovato per $idMandato (cercato come $mandatoForQuery)");
    }
    $stmt->close();
}

$contattiUtiliHeracom = 0;

foreach ($mandato as $idMandato) {
    // Processa solo Heracom
    if ($idMandato !== "Heracom") {
        continue;
    }

    // Query per contatti utili di Heracom dalla tabella contattiUtili
    $queryCu = "SELECT SUM(`contattiUtili`) AS cu  
                FROM `contattiUtili` 
                WHERE data >= ? AND data <= ? 
                AND mandato = 'Heracom'";
    
    // Esegui la query con prepared statement
    $stmt = $conn19->prepare($queryCu);
    if ($stmt) {
        // Ora ci sono due parametri (i ? nella query) quindi passiamo due variabili
        $stmt->bind_param("ss", $dataMinore, $dataMaggiore);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $contattiUtiliHeracom = $row['cu'] ?? 0;
        }
        $stmt->close();
    } else {
        // Aggiunto debug per errori di preparazione
        error_log("Errore preparazione query: " . $conn19->error);
    }
}

$fatturatoCu = 1.5 * $contattiUtiliHeracom;





// Query principali per ogni mandato
foreach ($mandato as $idMandato) {
    if ($idMandato === "Union" || $idMandato === "Vodafone" || $idMandato === "Bo" || $idMandato === "59"  || $idMandato === "TIM" || $idMandato === "TIMmq") {
        continue;
    }

    switch ($idMandato) {
        case "Plenitude":
            $queryCrmSede = "SELECT
                p.`sede` AS sede,
                'Plenitude' AS plenitude,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 1 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 2 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 3 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 4 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 5 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(p.data, 1) - WEEK(DATE_SUB(p.data, INTERVAL DAYOFMONTH(p.data) - 1 DAY), 1) + 1 = 6 AND ap.fasePDA = 'OK' THEN ap.pezzoLordo ELSE 0 END) AS WEEK_6,
                (
                    SELECT SUM(o.plenitudePdp)
                    FROM obbiettivoTL o
                    WHERE o.sede = p.sede
                    AND o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM `plenitude` p
            INNER JOIN aggiuntaPlenitude ap ON p.id = ap.id
            WHERE p.data <= '$dataMaggiore' 
            AND p.data >= '$dataMinore'
            AND ap.fasePDA = 'OK'
            AND p.comodity <> 'Polizza'
            GROUP BY p.sede";
            
   
            
            break;
        case "Vivigas":  // Cambia da "Vivigas Energia" a "Vivigas"   
        case "Vivigas Energia":
            $queryCrmSede = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                'Vivigas' AS vivigas,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6,
                (
                    SELECT SUM(o2.vivigasPdp)
                    FROM obbiettivoTL o2
                    WHERE o2.sede = vivigas.sede
                    AND o2.mese <= '$dataMaggiore'
                    AND o2.mese >= '$dataMinore'
                ) AS obiettivo            
            FROM `vivigas`
            INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'Polizza'
            GROUP BY sede";
//        echo $queryCrmSede;    
                   
            break;
      case "ENEL":  // Cambia da "Vivigas Energia" a "Vivigas" 
        case "Enel":
            $queryCrmSede = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                'Enel' AS Enel,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6,
                (
                    SELECT SUM(o.enelPdp)
                    FROM obbiettivoTL o
                    WHERE o.sede = enel.sede
                    AND o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM enel
            INNER JOIN aggiuntaEnel ON enel.id = aggiuntaEnel.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'Fibra'
            GROUP BY sede";
            
         
            break;
                case "Iren":
            $queryCrmSede = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                'Iren' AS Iren,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6,
                (
                    SELECT SUM(o.irenPdp)
                    FROM obbiettivoTL o
                    WHERE o.sede = iren.sede
                    AND o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM iren
            INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'Fibra'
            GROUP BY sede";
                    
//                         echo $queryCrmSede;
                    
            break;
        
        
        
                        case "EnelIn":
            $queryCrmSede = "SELECT
                IFNULL('Rende', 'TOTALE') AS sede,
                'EnelIn' AS EnelIn,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6,
                (
                    SELECT SUM(o.enelInPdp)
                    FROM obbiettivoTL o
                    WHERE o.sede = enelIn.sede
                    AND o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM enelIn
            INNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'cONSENSO'
            GROUP BY sede";
                    
//                         echo $queryCrmSede;
                    
            break;
        
        
                                case "Heracom":
            $queryCrmSede = "SELECT
                IFNULL('Lamezia', 'TOTALE') AS sede,
                'Heracom' AS Heracom,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 AND fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS WEEK_6,
                (
                    SELECT SUM(o.heracomPdp)
                    FROM obbiettivoTL o
                    WHERE o.sede = heracom.sede
                    AND o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM heracom
            INNER JOIN aggiuntaHeracom ON heracom.id = aggiuntaHeracom.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'Consenso'
            GROUP BY sede";
                    
//                         echo $queryCrmSede;
                    
            break;
    }
//        echo $queryCrmSede;
    $risultatoCrmSede = $conn19->query($queryCrmSede);

    if ($risultatoCrmSede) {
        while ($rigaCRM = $risultatoCrmSede->fetch_assoc()) {
            $sede = $rigaCRM['sede'] ?? '';
            $pezzoLordow1 = round($rigaCRM['WEEK_1'] ?? 0, 0);
            $pezzoLordow2 = round($rigaCRM['WEEK_2'] ?? 0, 0);
            $pezzoLordow3 = round($rigaCRM['WEEK_3'] ?? 0, 0);
            $pezzoLordow4 = round($rigaCRM['WEEK_4'] ?? 0, 0);
            $pezzoLordow5 = round($rigaCRM['WEEK_5'] ?? 0, 0);
            $pezzoLordow6 = round($rigaCRM['WEEK_6'] ?? 0, 0);
            $obiettivo = round($rigaCRM['obiettivo'] ?? 0, 0);

            // Query per le ore
switch ($idMandato) {
    case "Plenitude":
    case "Vivigas Energia":
    case "Iren":
    case "Enel":
        $queryOreSettimana = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                ? AS mandato,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 1 THEN numero/3600 ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 2 THEN numero/3600 ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 3 THEN numero/3600 ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 4 THEN numero/3600 ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 5 THEN numero/3600 ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 6 THEN numero/3600 ELSE 0 END) AS WEEK_6
            FROM `stringheTotale`
            WHERE giorno >= ? AND giorno <= ?
            AND livello <= 6
            AND sede = ?
            AND idMandato = ?
            GROUP BY sede";
        $table = "stringheTotale";
        $dateField = "giorno";
        $hoursField = "numero/3600";
        $params = [$idMandato, $dataMinore, $dataMaggiore, $sede, $idMandato];
        $paramTypes = "sssss";
        break;
        
    case "EnelIn":
        $queryOreSettimana = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                ? AS mandato,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 THEN oreDichiarate ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 THEN oreDichiarate ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 THEN oreDichiarate ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 THEN oreDichiarate ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 THEN oreDichiarate ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 THEN oreDichiarate ELSE 0 END) AS WEEK_6
            FROM `oreEnelIn`
            WHERE data >= ? AND data <= ?
            AND sede = ?
            AND mandato = ?
            GROUP BY sede";
        $table = "oreEnelIn";
        $dateField = "data";
        $hoursField = "oreDichiarate";
        $params = [$idMandato, $dataMinore, $dataMaggiore, $sede, $idMandato];
        $paramTypes = "sssss";
        break;
        
    case "Heracom":
        $queryOreSettimana = "SELECT
    IFNULL(sede, 'TOTALE') AS sede,
    ? AS mandato,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 1 THEN numero/3600 ELSE 0 END), 2) AS WEEK_1,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 2 THEN numero/3600 ELSE 0 END), 2) AS WEEK_2,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 3 THEN numero/3600 ELSE 0 END), 2) AS WEEK_3,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 4 THEN numero/3600 ELSE 0 END), 2) AS WEEK_4,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 5 THEN numero/3600 ELSE 0 END), 2) AS WEEK_5,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 6 THEN numero/3600 ELSE 0 END), 2) AS WEEK_6
FROM `stringheSiscallLeadTC`
WHERE giorno >= ? AND giorno <= ?
AND sede = ?
AND mandato = ?
AND userGroup = 'OP_Lam_piannazzo'
GROUP BY sede";
        $table = "stringheSiscallLeadTC";
        $dateField = "giorno";
        $hoursField = "numero";
        $params = [$idMandato, $dataMinore, $dataMaggiore, $sede, $idMandato];
        $paramTypes = "sssss";
        break;
        
    case "TIMmq":
        $queryOreSettimana = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                ? AS mandato,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 THEN oreDichiarate ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 THEN oreDichiarate ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 THEN oreDichiarate ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 THEN oreDichiarate ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 THEN oreDichiarate ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 THEN oreDichiarate ELSE 0 END) AS WEEK_6
            FROM `oreTIMmq`
            WHERE data >= ? AND data <= ?
            AND sede = ?
            AND mandato = ?
            GROUP BY sede";
        $table = "oreTIMmq";
        $dateField = "data";
        $hoursField = "oreDichiarate";
        $params = [$idMandato, $dataMinore, $dataMaggiore, $sede, $idMandato];
        $paramTypes = "sssss";
        break;
        
    default:
        $oreW1 = $oreW2 = $oreW3 = $oreW4 = $oreW5 = $oreW6 = 0;
        return;
}

// Esecuzione della query con prepared statement
$stmt = $conn19->prepare($queryOreSettimana);
if ($stmt === false) {
    die("Errore nella preparazione della query: " . $conn19->error);
}

$stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$risultatoOre = $stmt->get_result();
$rigaOre = $risultatoOre->fetch_assoc();
$stmt->close();

$oreW1 = round($rigaOre['WEEK_1'] ?? 0, 0);
$oreW2 = round($rigaOre['WEEK_2'] ?? 0, 0);
$oreW3 = round($rigaOre['WEEK_3'] ?? 0, 0);
$oreW4 = round($rigaOre['WEEK_4'] ?? 0, 0);
$oreW5 = round($rigaOre['WEEK_5'] ?? 0, 0);
$oreW6 = round($rigaOre['WEEK_6'] ?? 0, 0);
            // Calcola fatturato
            $mediaMandato = $valoriMedi[$idMandato] ?? 0;
            
  
            $fatturato1 = $mediaMandato * $pezzoLordow1;
            $fatturato2 = $mediaMandato * $pezzoLordow2;
            $fatturato3 = $mediaMandato * $pezzoLordow3;
            $fatturato4 = $mediaMandato * $pezzoLordow4;
            $fatturato5 = $mediaMandato * $pezzoLordow5;
            $fatturato6 = $mediaMandato * $pezzoLordow6;
            
            
            
            // Inizializza array sede se non esiste
            if (!isset($totaliPerSede[$sede])) {
                $totaliPerSede[$sede] = [
                    'oreW1' => 0, 'pezziW1' => 0, 'fatturatoW1' => 0, 'resaW1' => 0,
                    'oreW2' => 0, 'pezziW2' => 0, 'fatturatoW2' => 0, 'resaW2' => 0,
                    'oreW3' => 0, 'pezziW3' => 0, 'fatturatoW3' => 0, 'resaW3' => 0,
                    'oreW4' => 0, 'pezziW4' => 0, 'fatturatoW4' => 0, 'resaW4' => 0,
                    'oreW5' => 0, 'pezziW5' => 0, 'fatturatoW5' => 0, 'resaW5' => 0,
                    'oreW6' => 0, 'pezziW6' => 0, 'fatturatoW6' => 0, 'resaW6' => 0,
                    'totaleOreMese' => 0, 'totalePezziMese' => 0, 'totaleFatturatoMese' => 0, 'resaMese' => 0
                ];
                $totaliObiettivi[$sede] = 0;
            }

            // Aggiorna totali
            $totaliObiettivi[$sede] += $obiettivo;
            
            $totaliPerSede[$sede]['oreW1'] += $oreW1;
            $totaliPerSede[$sede]['pezziW1'] += $pezzoLordow1;
            $totaliPerSede[$sede]['fatturatoW1'] += $fatturato1;
            
            $totaliPerSede[$sede]['oreW2'] += $oreW2;
            $totaliPerSede[$sede]['pezziW2'] += $pezzoLordow2;
            $totaliPerSede[$sede]['fatturatoW2'] += $fatturato2;
            
            $totaliPerSede[$sede]['oreW3'] += $oreW3;
            $totaliPerSede[$sede]['pezziW3'] += $pezzoLordow3;
            $totaliPerSede[$sede]['fatturatoW3'] += $fatturato3;
            
            $totaliPerSede[$sede]['oreW4'] += $oreW4;
            $totaliPerSede[$sede]['pezziW4'] += $pezzoLordow4;
            $totaliPerSede[$sede]['fatturatoW4'] += $fatturato4;
            
            $totaliPerSede[$sede]['oreW5'] += $oreW5;
            $totaliPerSede[$sede]['pezziW5'] += $pezzoLordow5;
            $totaliPerSede[$sede]['fatturatoW5'] += $fatturato5;
            
            $totaliPerSede[$sede]['oreW6'] += $oreW6;
            $totaliPerSede[$sede]['pezziW6'] += $pezzoLordow6;
            $totaliPerSede[$sede]['fatturatoW6'] += $fatturato6;

            // Ricalcola rese
            $totaliPerSede[$sede]['resaW1'] = ($totaliPerSede[$sede]['oreW1'] != 0) ? 
                round($totaliPerSede[$sede]['fatturatoW1'] / $totaliPerSede[$sede]['oreW1'], 0) : 0;
            $totaliPerSede[$sede]['resaW2'] = ($totaliPerSede[$sede]['oreW2'] != 0) ? 
                round($totaliPerSede[$sede]['fatturatoW2'] / $totaliPerSede[$sede]['oreW2'], 0) : 0;
            $totaliPerSede[$sede]['resaW3'] = ($totaliPerSede[$sede]['oreW3'] != 0) ? 
                round($totaliPerSede[$sede]['fatturatoW3'] / $totaliPerSede[$sede]['oreW3'], 0) : 0;
            $totaliPerSede[$sede]['resaW4'] = ($totaliPerSede[$sede]['oreW4'] != 0) ? 
                round($totaliPerSede[$sede]['fatturatoW4'] / $totaliPerSede[$sede]['oreW4'], 0) : 0;
            $totaliPerSede[$sede]['resaW5'] = ($totaliPerSede[$sede]['oreW5'] != 0) ? 
                round($totaliPerSede[$sede]['fatturatoW5'] / $totaliPerSede[$sede]['oreW5'], 0) : 0;
            $totaliPerSede[$sede]['resaW6'] = ($totaliPerSede[$sede]['oreW6'] != 0) ? 
                round($totaliPerSede[$sede]['fatturatoW6'] / $totaliPerSede[$sede]['oreW6'], 0) : 0;

            // Totali mensili
            $totaliPerSede[$sede]['totaleOreMese'] = 
                $totaliPerSede[$sede]['oreW1'] + $totaliPerSede[$sede]['oreW2'] + 
                $totaliPerSede[$sede]['oreW3'] + $totaliPerSede[$sede]['oreW4'] + 
                $totaliPerSede[$sede]['oreW5'] + $totaliPerSede[$sede]['oreW6'];
                
            $totaliPerSede[$sede]['totalePezziMese'] = 
                $totaliPerSede[$sede]['pezziW1'] + $totaliPerSede[$sede]['pezziW2'] + 
                $totaliPerSede[$sede]['pezziW3'] + $totaliPerSede[$sede]['pezziW4'] + 
                $totaliPerSede[$sede]['pezziW5'] + $totaliPerSede[$sede]['pezziW6'];
                
            $totaliPerSede[$sede]['totaleFatturatoMese'] = 
                $totaliPerSede[$sede]['fatturatoW1'] + $totaliPerSede[$sede]['fatturatoW2'] + 
                $totaliPerSede[$sede]['fatturatoW3'] + $totaliPerSede[$sede]['fatturatoW4'] + 
                $totaliPerSede[$sede]['fatturatoW5'] + $totaliPerSede[$sede]['fatturatoW6'];
                
            $totaliPerSede[$sede]['resaMese'] = ($totaliPerSede[$sede]['totaleOreMese'] != 0) ? 
                round($totaliPerSede[$sede]['totaleFatturatoMese'] / $totaliPerSede[$sede]['totaleOreMese'], 2) : 0;
            
            if ( $idMandato === 'Heracom') {
    $totaliPerSede[$sede]['totaleFatturatoMese'] += $fatturatoCu; // O qualsiasi altra variabile
}
            
        }
    } else {
        error_log("Errore nella query per il mandato: $idMandato");
    }
}




// Genera HTML
$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaAvanzamentoWeekSede.php";

foreach ($totaliPerSede as $sede => $totali) {
    $html .= "<tr>";
    $html .= "<td>$sede</td>";
    
    // Totali mese
    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['totaleOreMese'], 0) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['totalePezziMese'] . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . ($totaliObiettivi[$sede] ?? 0) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['totaleFatturatoMese'], 0) . " €</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['resaMese'], 0) . " €/h</td>";

    // Settimane 1-6 (ripetere lo stesso pattern per ogni settimana)
    for ($i = 1; $i <= 6; $i++) {
        $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['oreW'.$i], 0) . "</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['pezziW'.$i] . "</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['fatturatoW'.$i], 0) . " €</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['resaW'.$i], 0) . " €/h</td>";
    }

    $html .= "</tr>";
}

// Calcola totali generali
$totaleGenerale = [
    'ore' => 0,
    'pezzi' => 0,
    'fatturato' => 0,
    'obiettivi' => array_sum($totaliObiettivi),
    'perSettimana' => []
];

for ($i = 1; $i <= 6; $i++) {
    $totaleGenerale['perSettimana'][$i] = [
        'ore' => array_sum(array_column($totaliPerSede, 'oreW'.$i)),
        'pezzi' => array_sum(array_column($totaliPerSede, 'pezziW'.$i)),
        'fatturato' => array_sum(array_column($totaliPerSede, 'fatturatoW'.$i)),
        'resa' => 0
    ];
    
    $totaleGenerale['perSettimana'][$i]['resa'] = ($totaleGenerale['perSettimana'][$i]['ore'] != 0) ? 
        round($totaleGenerale['perSettimana'][$i]['fatturato'] / $totaleGenerale['perSettimana'][$i]['ore'], 0) : 0;
    
    $totaleGenerale['ore'] += $totaleGenerale['perSettimana'][$i]['ore'];
    $totaleGenerale['pezzi'] += $totaleGenerale['perSettimana'][$i]['pezzi'];
    $totaleGenerale['fatturato'] += $totaleGenerale['perSettimana'][$i]['fatturato'];
    
}

$totaleGenerale['resa'] = ($totaleGenerale['ore'] != 0) ? 
    round($totaleGenerale['fatturato'] / $totaleGenerale['ore'], 0) : 0;

            if ( $idMandato === 'Heracom') {
    $totaleGenerale['fatturato'] += $fatturatoCu; // O qualsiasi altra variabile
}

// Aggiungi riga totale generale
$html .= "<tr>";
$html .= "<td><strong>TOTALE</strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleGenerale['ore'], 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . $totaleGenerale['pezzi'] . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . $totaleGenerale['obiettivi'] . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleGenerale['fatturato'], 0) . " €</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . $totaleGenerale['resa'] . " €/h</strong></td>";

for ($i = 1; $i <= 6; $i++) {
    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleGenerale['perSettimana'][$i]['ore'], 0) . "</strong></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'><strong>" . $totaleGenerale['perSettimana'][$i]['pezzi'] . "</strong></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleGenerale['perSettimana'][$i]['fatturato'], 0) . " €</strong></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'><strong>" . $totaleGenerale['perSettimana'][$i]['resa'] . " €/h</strong></td>";
}

$html .= "</tr>";
$html .= "</table>";

echo $html;