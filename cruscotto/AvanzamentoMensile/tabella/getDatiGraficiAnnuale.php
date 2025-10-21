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

$mese_anno_selezionato = filter_input(INPUT_POST, "mese_anno");
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

if (!$mese_anno_selezionato || !$mandato) {
    echo json_encode(['error' => 'Parametri mancanti']);
    exit;
}

// Estrazione anno
if (preg_match('/^(\d{2})\/(\d{4})$/', $mese_anno_selezionato, $matches)) {
    $anno_selezionato = $matches[2];
} else {
    $anno_selezionato = date('Y');
}

// Query sede
$querySede = "";
if (!empty($sede)) {
    if (count($sede) == 1) {
        $querySede = " AND sede = '{$sede[0]}' ";
    } else {
        $querySede = " AND sede IN ('" . implode("','", $sede) . "') ";
    }
}

$mandati_validi = ["Plenitude", "Vivigas Energia", "Enel", "Iren", "Heracom", "EnelIn"];
$risultati = [];

// Totali
$totali = [];
for ($m = 1; $m <= 12; $m++) {
    $totali['m' . $m] = ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0];
}

foreach ($mandato as $idMandato) {
    if (in_array($idMandato, ["Union", "Vodafone", "Bo"]) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    $risultati[$idMandato] = [];
    for ($m = 1; $m <= 12; $m++) {
        $risultati[$idMandato]['m' . $m] = ['ore' => 0, 'pezzi' => 0, 'fatturato' => 0, 'resa' => 0];
    }

    $dbMandatoName = ($idMandato == "Vivigas Energia") ? "Vivigas" : $idMandato;

    // 1. VALORI MEDI
    $queryMedia = "SELECT MONTH(mese) AS mese_num, media 
                  FROM `mediaPraticaMese` 
                  WHERE YEAR(mese) = '$anno_selezionato' 
                  AND mandato = '$dbMandatoName'
                  ORDER BY mese_num";

    $valoriMedi = array_fill(1, 12, 0);
    $risMedia = $conn19->query($queryMedia);
    if ($risMedia && $risMedia->num_rows > 0) {
        while ($row = $risMedia->fetch_assoc()) {
            $m = $row['mese_num'];
            $valoriMedi[$m] = $row['media'];
        }
    }

    // 2. PEZZI
    $queryPezzi = "";
    switch ($idMandato) {
        case "Plenitude":
            $queryPezzi = "SELECT MONTH(data) AS mese, SUM(pezzoLordo) AS pezzi
                         FROM `plenitude`
                         INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id 
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK' AND comodity <> 'Polizza'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
        case "Vivigas Energia":
            $queryPezzi = "SELECT MONTH(data) AS mese, SUM(pezzoLordo) AS pezzi
                         FROM `vivigas`
                         INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
        case "Enel":
            $queryPezzi = "SELECT MONTH(data) AS mese, SUM(pezzoLordo) AS pezzi
                         FROM enel
                         INNER JOIN aggiuntaEnel ON enel.id = aggiuntaEnel.id
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK' AND comodity <> 'Fibra'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
        case "Iren":
            $queryPezzi = "SELECT MONTH(data) AS mese, SUM(pezzoLordo) AS pezzi
                         FROM iren
                         INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK' AND comodity <> 'Fibra'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
        case "Heracom":
            $queryPezzi = "SELECT MONTH(data) AS mese, SUM(pezzoLordo) AS pezzi
                         FROM heracom
                         INNER JOIN aggiuntaHeracom ON heracom.id = aggiuntaHeracom.id
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK' AND comodity <> 'Consenso'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
        case "EnelIn":
            $queryPezzi = "SELECT MONTH(data) AS mese, SUM(pezzoLordo) AS pezzi
                         FROM enelIn
                         INNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
    }

    if (!empty($queryPezzi)) {
        $risPezzi = $conn19->query($queryPezzi);
        if ($risPezzi) {
            while ($row = $risPezzi->fetch_assoc()) {
                $risultati[$idMandato]['m' . $row['mese']]['pezzi'] = round($row['pezzi'], 0);
            }
        }
    }

    // 3. CONTATTI UTILI (SOLO HERACOM)
    $fatturatoCu = array_fill(1, 12, 0);
    if ($idMandato === "Heracom") {
        $queryCu = "SELECT MONTH(data) AS mese, SUM(contattiUtili) AS cu 
                   FROM `contattiUtili` 
                   WHERE YEAR(data) = '$anno_selezionato' 
                   AND mandato = 'Heracom'
                   GROUP BY MONTH(data)";
        
        $risCu = $conn19->query($queryCu);
        if ($risCu && $risCu->num_rows > 0) {
            while ($row = $risCu->fetch_assoc()) {
                $m = $row['mese'];
                $fatturatoCu[$m] = 1.5 * $row['cu'];
            }
        }
    }

    // 4. ORE
    $queryOre = "";
    switch ($idMandato) {
        case "Plenitude":
        case "Vivigas Energia":
        case "Iren":
        case "Enel":
            $queryOre = "SELECT MONTH(giorno) AS mese, SUM(numero/3600) AS ore
                        FROM `stringheTotale`
                        WHERE YEAR(giorno) = '$anno_selezionato' AND livello <= 6 AND idMandato = '$idMandato'
                        GROUP BY MONTH(giorno)";
            break;
        case "EnelIn":
            $queryOre = "SELECT MONTH(data) AS mese, SUM(oreDichiarate) AS ore
                        FROM `oreEnelIn`
                        WHERE YEAR(data) = '$anno_selezionato'
                        GROUP BY MONTH(data)";
            break;
        case "Heracom":
            $queryOre = "SELECT MONTH(giorno) AS mese, SUM(numero/3600) AS ore
                        FROM `stringheSiscallLeadTC`
                        WHERE YEAR(giorno) = '$anno_selezionato' AND mandato = 'Heracom' AND userGroup = 'OP_Lam_piannazzo'
                        GROUP BY MONTH(giorno)";
            break;
    }

    if (!empty($queryOre)) {
        $risOre = $conn19->query($queryOre);
        if ($risOre) {
            while ($row = $risOre->fetch_assoc()) {
                $risultati[$idMandato]['m' . $row['mese']]['ore'] = round($row['ore'], 0);
            }
        }
    }

    // 5. CALCOLI FINALI
    for ($m = 1; $m <= 12; $m++) {
        $ore = $risultati[$idMandato]['m' . $m]['ore'];
        $pezzi = $risultati[$idMandato]['m' . $m]['pezzi'];
        $fatturato = $valoriMedi[$m] * $pezzi;
        
        if ($idMandato === "Heracom") {
            $fatturato += $fatturatoCu[$m];
        }
        
        $risultati[$idMandato]['m' . $m]['fatturato'] = round($fatturato, 0);
        $risultati[$idMandato]['m' . $m]['resa'] = $ore > 0 ? round($fatturato / $ore, 0) : 0;
        
        // Aggiorna totali
        $totali['m' . $m]['ore'] += $ore;
        $totali['m' . $m]['pezzi'] += $pezzi;
        $totali['m' . $m]['fatturato'] += $fatturato;
    }
}

// Calcola resa totali
for ($m = 1; $m <= 12; $m++) {
    $totali['m' . $m]['resa'] = $totali['m' . $m]['ore'] > 0 ? 
        round($totali['m' . $m]['fatturato'] / $totali['m' . $m]['ore'], 0) : 0;
}

$risultati['TOTALE'] = $totali;

echo json_encode($risultati, JSON_NUMERIC_CHECK);

