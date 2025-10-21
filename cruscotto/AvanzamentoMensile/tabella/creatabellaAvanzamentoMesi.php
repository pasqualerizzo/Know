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

$mese_anno_selezionato = filter_input(INPUT_POST, "mese_anno");

// Debug: verifica cosa stai ricevendo DAVVERO dal form
error_log("[DEBUG REALE] Data ricevuta dal form: " . print_r($_POST, true));
error_log("[DEBUG REALE] Valore mese_anno: " . $mese_anno_selezionato);

// Estrazione anno e mese dalla selezione
if ($mese_anno_selezionato && preg_match('/^(\d{2})\/(\d{4})$/', $mese_anno_selezionato, $matches)) {
    $mese_selezionato = $matches[1];
    $anno_selezionato = $matches[2];
    error_log("[DEBUG] Estrazione riuscita: Mese=$mese_selezionato, Anno=$anno_selezionato");
} else {
    // Fallback con data corrente
    $mese_selezionato = date('m');
    $anno_selezionato = date('Y');
    error_log("[WARNING] Formato data non valido o mancante. Usato mese/anno corrente: $mese_selezionato/$anno_selezionato");
}

$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

// Costruzione query per sede
$querySede = "";
if (!empty($sede)) {
    if (count($sede) == 1) {
        $querySede = " AND sede = '{$sede[0]}' ";
    } else {
        $querySede = " AND sede IN ('" . implode("','", $sede) . "') ";
    }
}

$html = "<table class='blueTable'>";

// Intestazione della tabella
$html .= "<thead><tr>";
$html .= "<th rowspan='2'>Mandato</th>";
for ($m = 1; $m <= 12; $m++) {
    $nomeMese = date("F", mktime(0, 0, 0, $m, 1, $anno_selezionato));
    $html .= "<th colspan='4' style='border-left: 2px solid lightslategray'>$nomeMese</th>";
    $html .= "<th style='border-left: 2px solid lightslategray; background-color: orange;'></th>";
}
$html .= "</tr><tr>";
for ($m = 1; $m <= 12; $m++) {
    $html .= "<td style='border-left: 2px solid lightslategray'>Ore</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>Pezzi</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>Fatturato</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>Resa</td>";
    $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
}
$html .= "</tr></thead><tbody>";

$totaleOreMese = array_fill(1, 12, 0);
$totalePezzoMese = array_fill(1, 12, 0);
$totaleFatturatoMese = array_fill(1, 12, 0);

$mandati_validi = ["Plenitude", "Vivigas Energia", "Enel", "Iren", "Heracom", "EnelIn", "TIM"];

foreach ($mandato as $idMandato) {
    if (in_array($idMandato, ["Union", "Vodafone", "Bo"]) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    // Gestione speciale per Vivigas
    $dbMandatoName = ($idMandato == "Vivigas Energia") ? "Vivigas" : $idMandato;

    // 1. QUERY PER VALORE MEDIO MESE PER MESE
    $queryMedia = "SELECT MONTH(mese) AS mese_num, media 
                  FROM `mediaPraticaMese` 
                  WHERE YEAR(mese) = '$anno_selezionato' 
                  AND mandato = '$dbMandatoName'
                  ORDER BY mese_num";

    error_log("[DEBUG] Query valori medi per $idMandato (cercando come $dbMandatoName): " . $queryMedia);

    $valoriMedi = array_fill(1, 12, 0);
    $risMedia = $conn19->query($queryMedia);
    if ($risMedia && $risMedia->num_rows > 0) {
        while ($row = $risMedia->fetch_assoc()) {
            $m = $row['mese_num'];
            $valoriMedi[$m] = $row['media'];
            error_log("[DEBUG] Valore medio per mese $m: " . $row['media']);
        }
    }

    // 2. QUERY PER PEZZI
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
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK' AND comodity <> 'Polizza'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
        case "Enel":
            $queryPezzi = "SELECT MONTH(data) AS mese, SUM(pezzoLordo) AS pezzi
                         FROM enel
                         INNER JOIN aggiuntaEnel ON enel.id = aggiuntaEnel.id
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK' AND comodity <> 'Polizza'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
        case "Iren":
            $queryPezzi = "SELECT MONTH(data) AS mese, SUM(pezzoLordo) AS pezzi
                         FROM iren
                         INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK' AND comodity <> 'Polizza'
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
                         WHERE YEAR(data) = '$anno_selezionato' AND fasePDA = 'OK' AND comodity <> 'Polizza'
                         $querySede
                         GROUP BY MONTH(data)";
            break;
    }

    $pezzoLordoMandato = array_fill(1, 12, 0);
    if (!empty($queryPezzi)) {
        $risPezzi = $conn19->query($queryPezzi);
        if ($risPezzi) {
            while ($row = $risPezzi->fetch_assoc()) {
                $pezzoLordoMandato[$row['mese']] = $row['pezzi'];
            }
        }
    }

    // 3. GESTIONE CONTATTI UTILI (SOLO PER HERACOM)
    $contattiUtili = array_fill(1, 12, 0);
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
                $contattiUtili[$m] = $row['cu'];
                $fatturatoCu[$m] = 1.5 * $row['cu']; // 1.5€ per contatto utile
            }
        }
    }

    // 4. QUERY PER ORE
    $queryOre = "";
    switch ($idMandato) {
        case "Plenitude":
        case "Vivigas Energia":
        case "Iren":
        case "Enel":
        case "TIM":
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
                        WHERE YEAR(giorno) = '$anno_selezionato' AND livello <= 6 AND idMandato = 'Heracom' AND userGroup = 'OP_Lam_piannazzo'
                        GROUP BY MONTH(giorno)";
            break;
        case "TIMmq":
            $queryOre = "SELECT MONTH(giorno) AS mese, SUM(oreDichiarate/3600) AS ore
                        FROM `oreTIMmq`
                        WHERE YEAR(giorno) = '$anno_selezionato'
                        GROUP BY MONTH(giorno)";
            break;
    }

    $oreMandato = array_fill(1, 12, 0);
    if (!empty($queryOre)) {
        $risOre = $conn19->query($queryOre);
        if ($risOre) {
            while ($row = $risOre->fetch_assoc()) {
                $oreMandato[$row['mese']] = $row['ore'];
            }
        }
    }

    // 5. CALCOLI FINALI PER MESE
    $fatturatoMandato = array_fill(1, 12, 0);
    $resaMandato = array_fill(1, 12, 0);
    
    for ($m = 1; $m <= 12; $m++) {
        // Calcolo fatturato base (media * pezzi)
        $fatturatoMandato[$m] = $valoriMedi[$m] * $pezzoLordoMandato[$m];
        
        // Aggiungi fatturato CU solo per Heracom
        if ($idMandato === "Heracom") {
            $fatturatoMandato[$m] += $fatturatoCu[$m];
        }
        
        // Calcola resa
        $resaMandato[$m] = ($oreMandato[$m] > 0) ? round($fatturatoMandato[$m] / $oreMandato[$m], 2) : 0;
        
        // Aggiorna totali
        $totaleOreMese[$m] += $oreMandato[$m];
        $totalePezzoMese[$m] += $pezzoLordoMandato[$m];
        $totaleFatturatoMese[$m] += $fatturatoMandato[$m];
    }

    // 6. GENERAZIONE RIGA TABELLA
    $html .= "<tr><td>$idMandato</td>";
    for ($m = 1; $m <= 12; $m++) {
        $html .= "<td>".number_format($oreMandato[$m], 0)."</td>";
        $html .= "<td>".number_format($pezzoLordoMandato[$m], 0);
        
        // Mostra contatti utili solo per Heracom
//        if ($idMandato === "Heracom" && $contattiUtili[$m] > 0) {
//            $html .= "<br><small>(CU: ".$contattiUtili[$m].")</small>";
//        }
        
        $html .= "</td>";
        $html .= "<td>".number_format($fatturatoMandato[$m], 0)."&nbsp;€</td>";
        $html .= "<td>".number_format($resaMandato[$m], 0)."&nbsp;€/h</td>";
        $html .= "<td style='background-color: orange;'></td>";
    }
    $html .= "</tr>";
}

// 7. RIGA TOTALI
$html .= "<tr><td><strong>TOTALE</strong></td>";
for ($m = 1; $m <= 12; $m++) {
    $resaTotale = ($totaleOreMese[$m] > 0) ? round($totaleFatturatoMese[$m] / $totaleOreMese[$m], 2) : 0;
    
    $html .= "<td><strong>".number_format($totaleOreMese[$m], 0)."</strong></td>";
    $html .= "<td><strong>".number_format($totalePezzoMese[$m], 0)."</strong></td>";
    $html .= "<td><strong>".number_format($totaleFatturatoMese[$m], 0)."&nbsp;€</strong></td>";
    $html .= "<td><strong>".number_format($resaTotale, 0)."&nbsp;€/h</strong></td>";
    $html .= "<td style='background-color: orange;'></td>";
}
$html .= "</tr></tbody></table>";

echo $html;