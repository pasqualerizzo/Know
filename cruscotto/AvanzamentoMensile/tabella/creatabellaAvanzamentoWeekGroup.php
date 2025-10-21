<?php

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

$dataMinore = $mese . "-01";
$dataMaggiore = date('Y-m-d', strtotime("last day of " . $mese));

$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaAvanzamentoWeekGroup.php";

$totaliPerUserGroup = []; // Array per memorizzare i totali per ogni userGroup

foreach ($mandato as $idMandato) {
    // Salta i mandati "Union", "Vodafone" e "Bo"
    if ($idMandato === "Union" || $idMandato === "Vodafone" || $idMandato === "Bo") {
        continue;
    }

    // Query per il valore medio in base al mandato
    switch ($idMandato) {
        case "Plenitude":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                 FROM `mediaPraticaMese` 
                                 WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
                                 AND mandato = 'Plenitude'
                                 ORDER BY `id` DESC";
            break;
        case "Vivigas Energia":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                 FROM `mediaPraticaMese` 
                                 WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
                                 AND mandato = 'Vivigas'
                                 ORDER BY `id` DESC";
            break;
        case "Enel":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                 FROM `mediaPraticaMese` 
                                 WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
                                 AND mandato = 'Enel'
                                 ORDER BY `id` DESC";
            break;
        default:
            // Se il mandato non è riconosciuto, passa al prossimo
            continue 2;
    }

    // Esegui la query per il valore medio
    $risultatovaloremedio = $conn19->query($queryvaloremedio);

    if ($risultatovaloremedio) {
        while ($rigaCRM = $risultatovaloremedio->fetch_array()) {
            // Controllo che $rigaCRM non sia null
            if ($rigaCRM !== null) {
                $mandatomedia = $rigaCRM[0] ?? '';
                $mesemedia = $rigaCRM[1] ?? '';
                $media = $rigaCRM[2] ?? '';

                // Memorizza il valore medio per il mandato corrente
                $valoriMedi[$idMandato] = $media;
            }
        }
    } else {
        // Logga un errore se la query non restituisce risultati
        error_log("Errore nella query per il valore medio del mandato: $idMandato");
    }
}

foreach ($mandato as $idMandato) {
    // Salta i mandati "Union" e "Vodafone"
    if ($idMandato === "Union" || $idMandato === "Vodafone" || $idMandato === "Bo") {
        continue;
    }

    switch ($idMandato) {
        case "Plenitude":
            $queryCrmSede = "SELECT
    IFNULL(stringheTotale.userGroup, 'TOTALE') AS userGroup,
    'Plenitude' AS mandato,
    SUM(CASE WHEN WEEK(plenitude.data, 1) - WEEK(DATE_SUB(plenitude.data, INTERVAL DAYOFMONTH(plenitude.data) - 1 DAY), 1) + 1 = 1 AND aggiuntaPlenitude.fasePDA = 'OK' THEN aggiuntaPlenitude.pezzoLordo ELSE 0 END) AS WEEK_1,
    SUM(CASE WHEN WEEK(plenitude.data, 1) - WEEK(DATE_SUB(plenitude.data, INTERVAL DAYOFMONTH(plenitude.data) - 1 DAY), 1) + 1 = 2 AND aggiuntaPlenitude.fasePDA = 'OK' THEN aggiuntaPlenitude.pezzoLordo ELSE 0 END) AS WEEK_2,
    SUM(CASE WHEN WEEK(plenitude.data, 1) - WEEK(DATE_SUB(plenitude.data, INTERVAL DAYOFMONTH(plenitude.data) - 1 DAY), 1) + 1 = 3 AND aggiuntaPlenitude.fasePDA = 'OK' THEN aggiuntaPlenitude.pezzoLordo ELSE 0 END) AS WEEK_3,
    SUM(CASE WHEN WEEK(plenitude.data, 1) - WEEK(DATE_SUB(plenitude.data, INTERVAL DAYOFMONTH(plenitude.data) - 1 DAY), 1) + 1 = 4 AND aggiuntaPlenitude.fasePDA = 'OK' THEN aggiuntaPlenitude.pezzoLordo ELSE 0 END) AS WEEK_4,
    SUM(CASE WHEN WEEK(plenitude.data, 1) - WEEK(DATE_SUB(plenitude.data, INTERVAL DAYOFMONTH(plenitude.data) - 1 DAY), 1) + 1 = 5 AND aggiuntaPlenitude.fasePDA = 'OK' THEN aggiuntaPlenitude.pezzoLordo ELSE 0 END) AS WEEK_5,
    SUM(CASE WHEN WEEK(plenitude.data, 1) - WEEK(DATE_SUB(plenitude.data, INTERVAL DAYOFMONTH(plenitude.data) - 1 DAY), 1) + 1 = 6 AND aggiuntaPlenitude.fasePDA = 'OK' THEN aggiuntaPlenitude.pezzoLordo ELSE 0 END) AS WEEK_6
FROM 
    plenitude
INNER JOIN 
    aggiuntaPlenitude 
    ON plenitude.id = aggiuntaPlenitude.id 
INNER JOIN 
    stringheTotale 
    ON plenitude.creatoDa = stringheTotale.nomeCompleto
WHERE 
    plenitude.data <= '$dataMaggiore' AND plenitude.data >= '$dataMinore'
    AND aggiuntaPlenitude.fasePDA = 'OK'
    AND plenitude.comodity <> 'Polizza'
GROUP BY
    stringheTotale.userGroup, stringheTotale.nomeCompleto";
            
            echo $queryCrmSede;
            break;
        case "Vivigas Energia":
            $queryCrmSede = "SELECT
    IFNULL(stringheTotale.userGroup, 'TOTALE') AS userGroup,
    'Vivigas' AS mandato,
    SUM(CASE WHEN WEEK(vivigas.data, 1) - WEEK(DATE_SUB(vivigas.data, INTERVAL DAYOFMONTH(vivigas.data) - 1 DAY), 1) + 1 = 1 AND aggiuntaVivigas.fasePDA = 'OK' THEN aggiuntaVivigas.pezzoLordo ELSE 0 END) AS WEEK_1,
    SUM(CASE WHEN WEEK(vivigas.data, 1) - WEEK(DATE_SUB(vivigas.data, INTERVAL DAYOFMONTH(vivigas.data) - 1 DAY), 1) + 1 = 2 AND aggiuntaVivigas.fasePDA = 'OK' THEN aggiuntaVivigas.pezzoLordo ELSE 0 END) AS WEEK_2,
    SUM(CASE WHEN WEEK(vivigas.data, 1) - WEEK(DATE_SUB(vivigas.data, INTERVAL DAYOFMONTH(vivigas.data) - 1 DAY), 1) + 1 = 3 AND aggiuntaVivigas.fasePDA = 'OK' THEN aggiuntaVivigas.pezzoLordo ELSE 0 END) AS WEEK_3,
    SUM(CASE WHEN WEEK(vivigas.data, 1) - WEEK(DATE_SUB(vivigas.data, INTERVAL DAYOFMONTH(vivigas.data) - 1 DAY), 1) + 1 = 4 AND aggiuntaVivigas.fasePDA = 'OK' THEN aggiuntaVivigas.pezzoLordo ELSE 0 END) AS WEEK_4,
    SUM(CASE WHEN WEEK(vivigas.data, 1) - WEEK(DATE_SUB(vivigas.data, INTERVAL DAYOFMONTH(vivigas.data) - 1 DAY), 1) + 1 = 5 AND aggiuntaVivigas.fasePDA = 'OK' THEN aggiuntaVivigas.pezzoLordo ELSE 0 END) AS WEEK_5,
    SUM(CASE WHEN WEEK(vivigas.data, 1) - WEEK(DATE_SUB(vivigas.data, INTERVAL DAYOFMONTH(vivigas.data) - 1 DAY), 1) + 1 = 6 AND aggiuntaVivigas.fasePDA = 'OK' THEN aggiuntaVivigas.pezzoLordo ELSE 0 END) AS WEEK_6
FROM 
    vivigas
INNER JOIN 
    aggiuntaVivigas 
    ON vivigas.id = aggiuntaVivigas.id 
INNER JOIN 
    stringheTotale 
    ON vivigas.creatoDa = stringheTotale.nomeCompleto
WHERE 
    vivigas.data <= '$dataMaggiore' AND vivigas.data >= '$dataMinore'
    AND aggiuntaVivigas.fasePDA = 'OK'
    AND vivigas.comodity <> 'Polizza'
GROUP BY
    stringheTotale.userGroup, stringheTotale.nomeCompleto";
            break;
        case "Enel":
            $queryCrmSede = "SELECT
    IFNULL(stringheTotale.userGroup, 'TOTALE') AS userGroup,
    'Enel' AS mandato,
    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 1 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_1,
    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 2 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_2,
    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 3 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_3,
    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 4 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_4,
    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 5 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_5,
    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 6 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_6,
FROM 
    enel
INNER JOIN 
    aggiuntaEnel 
    ON enel.id = aggiuntaEnel.id 
INNER JOIN 
    stringheTotale 
    ON enel.creatoDa = stringheTotale.nomeCompleto
WHERE 
    enel.data <= '$dataMaggiore' AND enel.data >= '$dataMinore'
    AND aggiuntaEnel.fasePDA = 'OK'
    AND enel.comodity <> 'Fibra'
GROUP BY
    stringheTotale.userGroup , stringheTotale.nomeCompleto";
            break;
//                case "EnelIn":
//            $queryCrmSede = "SELECT
//    IFNULL(stringheTotale.userGroup, 'TOTALE') AS userGroup,
//    'Enel' AS mandato,
//    SUM(CASE WHEN WEEK(enelIn.data, 1) - WEEK(DATE_SUB(enelIn.data, INTERVAL DAYOFMONTH(enelIn.data) - 1 DAY), 1) + 1 = 1 AND aggiuntaEnelIn.fasePDA = 'OK' THEN aggiuntaEnelIn.pezzoLordo ELSE 0 END) AS WEEK_1,
//    SUM(CASE WHEN WEEK(enelIn.data, 1) - WEEK(DATE_SUB(enelIn.data, INTERVAL DAYOFMONTH(enelIn.data) - 1 DAY), 1) + 1 = 2 AND aggiuntaEnelIn.fasePDA= 'OK' THEN aggiuntaEnelIn.pezzoLordo ELSE 0 END) AS WEEK_2,
//    SUM(CASE WHEN WEEK(enelIn.data, 1) - WEEK(DATE_SUB(enelIn.data, INTERVAL DAYOFMONTH(enelIn.data) - 1 DAY), 1) + 1 = 3 AND aggiuntaEnelIn.fasePDA = 'OK' THEN aggiuntaEnelIn.pezzoLordoo ELSE 0 END) AS WEEK_3,
//    SUM(CASE WHEN WEEK(enelIn.data, 1) - WEEK(DATE_SUB(enelIn.data, INTERVAL DAYOFMONTH(enelIn.data) - 1 DAY), 1) + 1 = 4 AND aggiuntaEnelIn.fasePDA = 'OK' THEN aggiuntaEnelIn.pezzoLordo 0 END) AS WEEK_4,
//    SUM(CASE WHEN WEEK(enelIn.data, 1) - WEEK(DATE_SUB(enelIn.data, INTERVAL DAYOFMONTH(enelIn.data) - 1 DAY), 1) + 1 = 5 AND aggiuntaEnelIn.fasePDA = 'OK' THEN aggiuntaEnelIn.pezzoLordo ELSE 0 END) AS WEEK_5
//FROM 
//    enel
//INNER JOIN 
//    aggiuntaEnel 
//    ON enel.id = aggiuntaEnel.id 
//INNER JOIN 
//    stringheTotale 
//    ON enel.creatoDa = stringheTotale.nomeCompleto
//WHERE 
//    enel.data <= '$dataMaggiore' AND enel.data >= '$dataMinore'
//    AND aggiuntaEnel.fasePDA = 'OK'
//    AND enel.comodity <> 'Fibra'
//GROUP BY
//    stringheTotale.userGroup , stringheTotale.nomeCompleto";
//            break;
//                case "Enel":
//            $queryCrmSede = "SELECT
//    IFNULL(stringheTotale.userGroup, 'TOTALE') AS userGroup,
//    'Enel' AS mandato,
//    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 1 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_1,
//    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 2 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_2,
//    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 3 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_3,
//    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 4 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_4,
//    SUM(CASE WHEN WEEK(enel.data, 1) - WEEK(DATE_SUB(enel.data, INTERVAL DAYOFMONTH(enel.data) - 1 DAY), 1) + 1 = 5 AND aggiuntaEnel.fasePDA = 'OK' THEN aggiuntaEnel.pezzoLordo ELSE 0 END) AS WEEK_5
//FROM 
//    enel
//INNER JOIN 
//    aggiuntaEnel 
//    ON enel.id = aggiuntaEnel.id 
//INNER JOIN 
//    stringheTotale 
//    ON enel.creatoDa = stringheTotale.nomeCompleto
//WHERE 
//    enel.data <= '$dataMaggiore' AND enel.data >= '$dataMinore'
//    AND aggiuntaEnel.fasePDA = 'OK'
//    AND enel.comodity <> 'Fibra'
//GROUP BY
//    stringheTotale.userGroup , stringheTotale.nomeCompleto";
//            break;
    }

    $risultatoCrmSede = $conn19->query($queryCrmSede);

    if ($risultatoCrmSede) {
        while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
            // Controllo che $rigaCRM non sia null
            if ($rigaCRM !== null) {
                $userGroup = $rigaCRM[0] ?? '';
                $mandato = $rigaCRM[1] ?? '';
                $pezzoLordow1 = round($rigaCRM[2] ?? 0, 0);
                $pezzoLordow2 = round($rigaCRM[3] ?? 0, 0);
                $pezzoLordow3 = round($rigaCRM[4] ?? 0, 0);
                $pezzoLordow4 = round($rigaCRM[5] ?? 0, 0);
                $pezzoLordow5 = round($rigaCRM[6] ?? 0, 0);
                $pezzoLordow6 = round($rigaCRM[7] ?? 0, 0);

                // Query per le ore suddivise per settimana
                $queryOreSettimana = "
    SELECT
    userGroup,
    '$idMandato' AS mandato,
    SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 1 THEN numero/3600 ELSE 0 END) AS WEEK_1,
    SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 2 THEN numero/3600 ELSE 0 END) AS WEEK_2,
    SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 3 THEN numero/3600 ELSE 0 END) AS WEEK_3,
    SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 4 THEN numero/3600 ELSE 0 END) AS WEEK_4,
    SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 5 THEN numero/3600 ELSE 0 END) AS WEEK_5,
    SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 6 THEN numero/3600 ELSE 0 END) AS WEEK_6
FROM 
    stringheTotale
WHERE 
    giorno >= '$dataMinore' AND giorno <= '$dataMaggiore'
    AND livello <= 6
    AND idMandato = '$idMandato'
GROUP BY
    userGroup  , nomeCompleto ;
";

$risultatoOreSettimana = $conn19->query($queryOreSettimana);
$rigaOre = $risultatoOreSettimana->fetch_array();

if ($rigaOre !== null && count($rigaOre) >= 8) {
    $userGroup = $rigaOre[0]; // userGroup è la prima colonna
    $mandato = $rigaOre[1];   // mandato è la seconda colonna
    $oreW1 = round($rigaOre[2] ?? 0, 2); // WEEK_1 è la terza colonna
    $oreW2 = round($rigaOre[3] ?? 0, 2); // WEEK_2 è la quarta colonna
    $oreW3 = round($rigaOre[4] ?? 0, 2); // WEEK_3 è la quinta colonna
    $oreW4 = round($rigaOre[5] ?? 0, 2); // WEEK_4 è la sesta colonna
    $oreW5 = round($rigaOre[6] ?? 0, 2); // WEEK_5 è la settima colonna
    $oreW6 = round($rigaOre[7] ?? 0, 2); // WEEK_5 è la settima colonna
    $nomeCompleto = $rigaOre[8]; // nomeCompleto è l'ottava colonna
} else {
    // Valori di default se $rigaOre è null o non ha abbastanza colonne
    $userGroup = 'TOTALE';
    $mandato = $idMandato;
    $oreW1 = 0;
    $oreW2 = 0;
    $oreW3 = 0;
    $oreW4 = 0;
    $oreW5 = 0;
    $oreW6 = 0;
    $nomeCompleto = '';
}

                $fatturato1 = $valoriMedi[$idMandato] * $pezzoLordow1;
                $fatturato2 = $valoriMedi[$idMandato] * $pezzoLordow2;
                $fatturato3 = $valoriMedi[$idMandato] * $pezzoLordow3;
                $fatturato4 = $valoriMedi[$idMandato] * $pezzoLordow4;
                $fatturato5 = $valoriMedi[$idMandato] * $pezzoLordow5;
                $fatturato6 = $valoriMedi[$idMandato] * $pezzoLordow6;

                // Aggiorna i totali per userGroup
                if (!isset($totaliPerUserGroup[$userGroup])) {
                    $totaliPerUserGroup[$userGroup] = [
                        'oreW1' => 0,
                        'pezziW1' => 0,
                        'fatturatoW1' => 0,
                        'resaW1' => 0,
                        'oreW2' => 0,
                        'pezziW2' => 0,
                        'fatturatoW2' => 0,
                        'resaW2' => 0,
                        'oreW3' => 0,
                        'pezziW3' => 0,
                        'fatturatoW3' => 0,
                        'resaW3' => 0,
                        'oreW4' => 0,
                        'pezziW4' => 0,
                        'fatturatoW4' => 0,
                        'resaW4' => 0,
                        'oreW5' => 0,
                        'pezziW5' => 0,
                        'fatturatoW5' => 0,
                        'resaW5' => 0,
                        'pezziW6' => 0,
                        'fatturatoW6' => 0,
                        'resaW6' => 0,
                    ];
                }
                $totaliPerUserGroup[$userGroup]['oreW1'] += $oreW1;
                $totaliPerUserGroup[$userGroup]['pezziW1'] += $pezzoLordow1;
                $totaliPerUserGroup[$userGroup]['fatturatoW1'] += $fatturato1;
                $totaliPerUserGroup[$userGroup]['resaW1'] = ($totaliPerUserGroup[$userGroup]['oreW1'] != 0) ? round($totaliPerUserGroup[$userGroup]['fatturatoW1'] / $totaliPerUserGroup[$userGroup]['oreW1'], 2) : 0;

                $totaliPerUserGroup[$userGroup]['oreW2'] += $oreW2;
                $totaliPerUserGroup[$userGroup]['pezziW2'] += $pezzoLordow2;
                $totaliPerUserGroup[$userGroup]['fatturatoW2'] += $fatturato2;
                $totaliPerUserGroup[$userGroup]['resaW2'] = ($totaliPerUserGroup[$userGroup]['oreW2'] != 0) ? round($totaliPerUserGroup[$userGroup]['fatturatoW2'] / $totaliPerUserGroup[$userGroup]['oreW2'], 2) : 0;

                $totaliPerUserGroup[$userGroup]['oreW3'] += $oreW3;
                $totaliPerUserGroup[$userGroup]['pezziW3'] += $pezzoLordow3;
                $totaliPerUserGroup[$userGroup]['fatturatoW3'] += $fatturato3;
                $totaliPerUserGroup[$userGroup]['resaW3'] = ($totaliPerUserGroup[$userGroup]['oreW3'] != 0) ? round($totaliPerUserGroup[$userGroup]['fatturatoW3'] / $totaliPerUserGroup[$userGroup]['oreW3'], 2) : 0;

                $totaliPerUserGroup[$userGroup]['oreW4'] += $oreW4;
                $totaliPerUserGroup[$userGroup]['pezziW4'] += $pezzoLordow4;
                $totaliPerUserGroup[$userGroup]['fatturatoW4'] += $fatturato4;
                $totaliPerUserGroup[$userGroup]['resaW4'] = ($totaliPerUserGroup[$userGroup]['oreW4'] != 0) ? round($totaliPerUserGroup[$userGroup]['fatturatoW4'] / $totaliPerUserGroup[$userGroup]['oreW4'], 2) : 0;

                $totaliPerUserGroup[$userGroup]['oreW5'] += $oreW5;
                $totaliPerUserGroup[$userGroup]['pezziW5'] += $pezzoLordow5;
                $totaliPerUserGroup[$userGroup]['fatturatoW5'] += $fatturato5;
                $totaliPerUserGroup[$userGroup]['resaW5'] = ($totaliPerUserGroup[$userGroup]['oreW5'] != 0) ? round($totaliPerUserGroup[$userGroup]['fatturatoW5'] / $totaliPerUserGroup[$userGroup]['oreW5'], 2) : 0;
              
                $totaliPerUserGroup[$userGroup]['oreW6'] += $oreW6;
                $totaliPerUserGroup[$userGroup]['pezziW6'] += $pezzoLordow6;
                $totaliPerUserGroup[$userGroup]['fatturatoW6'] += $fatturato6;
                $totaliPerUserGroup[$userGroup]['resaW6'] = ($totaliPerUserGroup[$userGroup]['oreW6'] != 0) ? round($totaliPerUserGroup[$userGroup]['fatturatoW6'] / $totaliPerUserGroup[$userGroup]['oreW6'], 2) : 0;
            }
        }
    } else {
        // Logga un errore se la query non restituisce risultati
        error_log("Errore nella query per il mandato: $idMandato");
    }
}

foreach ($totaliPerUserGroup as $userGroup => $totali) {
    $html .= "<tr>";
    $html .= "<td>$userGroup</td>";

    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['oreW1'], 2) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['pezziW1'] . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['fatturatoW1'] . " €</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['resaW1'] . " €/h</td>";

    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['oreW2'], 2) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['pezziW2'] . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['fatturatoW2'] . " €</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['resaW2'] . " €/h</td>";

    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['oreW3'], 2) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['pezziW3'] . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['fatturatoW3'] . " €</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['resaW3'] . " €/h</td>";

    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['oreW4'], 2) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['pezziW4'] . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['fatturatoW4'] . " €</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['resaW4'] . " €/h</td>";

    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['oreW5'], 2) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['pezziW5'] . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['fatturatoW5'] . " €</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['resaW5'] . " €/h</td>";
    
    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totali['oreW6'], 2) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['pezziW6'] . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['fatturatoW6'] . " €</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . $totali['resaW6'] . " €/h</td>";

    $html .= "</tr>";
}

// Calcola i totali generali
$totaleOreW1 = array_sum(array_column($totaliPerUserGroup, 'oreW1'));
$totalePezziW1 = array_sum(array_column($totaliPerUserGroup, 'pezziW1'));
$totaleFatturatoW1 = array_sum(array_column($totaliPerUserGroup, 'fatturatoW1'));
$totaleResaW1 = ($totaleOreW1 != 0) ? round($totaleFatturatoW1 / $totaleOreW1, 2) : 0;

$totaleOreW2 = array_sum(array_column($totaliPerUserGroup, 'oreW2'));
$totalePezziW2 = array_sum(array_column($totaliPerUserGroup, 'pezziW2'));
$totaleFatturatoW2 = array_sum(array_column($totaliPerUserGroup, 'fatturatoW2'));
$totaleResaW2 = ($totaleOreW2 != 0) ? round($totaleFatturatoW2 / $totaleOreW2, 2) : 0;

$totaleOreW3 = array_sum(array_column($totaliPerUserGroup, 'oreW3'));
$totalePezziW3 = array_sum(array_column($totaliPerUserGroup, 'pezziW3'));
$totaleFatturatoW3 = array_sum(array_column($totaliPerUserGroup, 'fatturatoW3'));
$totaleResaW3 = ($totaleOreW3 != 0) ? round($totaleFatturatoW3 / $totaleOreW3, 2) : 0;

$totaleOreW4 = array_sum(array_column($totaliPerUserGroup, 'oreW4'));
$totalePezziW4 = array_sum(array_column($totaliPerUserGroup, 'pezziW4'));
$totaleFatturatoW4 = array_sum(array_column($totaliPerUserGroup, 'fatturatoW4'));
$totaleResaW4 = ($totaleOreW4 != 0) ? round($totaleFatturatoW4 / $totaleOreW4, 2) : 0;

$totaleOreW5 = array_sum(array_column($totaliPerUserGroup, 'oreW5'));
$totalePezziW5 = array_sum(array_column($totaliPerUserGroup, 'pezziW5'));
$totaleFatturatoW5 = array_sum(array_column($totaliPerUserGroup, 'fatturatoW5'));
$totaleResaW5 = ($totaleOreW5 != 0) ? round($totaleFatturatoW5 / $totaleOreW5, 2) : 0;

$totaleOreW6 = array_sum(array_column($totaliPerUserGroup, 'oreW6'));
$totalePezziW6 = array_sum(array_column($totaliPerUserGroup, 'pezziW6'));
$totaleFatturatoW6 = array_sum(array_column($totaliPerUserGroup, 'fatturatoW6'));
$totaleResaW6 = ($totaleOreW6 != 0) ? round($totaleFatturatoW6 / $totaleOreW6, 2) : 0;

// Aggiungi la riga dei totali generali
$html .= "<tr>";
$html .= "<td><strong>TOTALE</strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW1, 2) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezziW1</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleFatturatoW1 €</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleResaW1 €/h</strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW2, 2) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezziW2</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleFatturatoW2 €</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleResaW2 €/h</strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW3, 2) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezziW3</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleFatturatoW3 €</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleResaW3 €/h</strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW4, 2) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezziW4</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleFatturatoW4 €</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleResaW4 €/h</strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW5, 2) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezziW5</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleFatturatoW5 €</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleResaW5 €/h</strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW6, 2) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezziW6</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleFatturatoW6 €</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totaleResaW6 €/h</strong></td>";

$html .= "</tr>";

$html .= "</table>";

echo $html;