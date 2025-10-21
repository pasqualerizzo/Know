<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$mese = filter_input(INPUT_POST, "mese");

$dataMinore = $mese . "-01";
$dataMaggiore = date('Y-m-d', strtotime("last day of " . $mese));

$testMode = $_POST["testMode"] ?? false;
$mandato = json_decode($_POST["mandato"] ?? '[]', true);
$sede = json_decode($_POST["sede"] ?? '[]', true);
$arraySwitchOut = [];

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);

$pezzoLordoSede = 0;
$pezzoOkSede = 0;
$pezzoKoSede = 0;
$pezzoBklSede = 0;
$pezzoBklpSede = 0;
$oreSede = 0;
$pezzoPostOkSede = 0;
$pezzoPostKoSede = 0;
$pezzoPostBklSede = 0;

$pezzoOkSwVoSede = 0;
$pezzoOkLeadSede = 0;
$dataSwoSede = 0;

$giorniLavorativiPrevisti = 0;
$giorniLavoratiEffettivi = 0;

$pezzoLordoTotale = 0;
$pezzoOkTotale = 0;
$pezzoKoTotale = 0;
$pezzoBklTotale = 0;
$pezzoBklpTotale = 0;
$oreTotale = 0;
$pezzoPostOkTotale = 0;
$pezzoPostKoTotale = 0;
$pezzoPostBklTotale = 0;
$pezzoOkSwVoTotale = 0;
$pezzoOkLeadTotale = 0;
$dataSwoTotale = 0;

$meseCorrente = 0;
$sedePrecedente = "";
$idMandato = $mandato[0] ?? '';
$querySede = "";
$lunghezzaSede = count($sede);

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
$mandati_validi = ["Plenitude", "Vivigas Energia", "Enel", "Iren", "EnelIn", "Heracom"];
$mandati_speciali = ["Union", "Vodafone", "Bo"];

// Verifica se ci sono solo mandati speciali
$solo_mandati_speciali = true;
foreach ($mandato as $idMandato) {
    if (!in_array($idMandato, $mandati_speciali)) {
        $solo_mandati_speciali = false;
        break;
    }
}

if ($solo_mandati_speciali) {
    echo ""; // Restituisce vuoto se ci sono solo mandati speciali
    exit;
}

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaMensile.php";

foreach ($mandato as $idMandato) {
    // Salta i mandati speciali e quelli non validi
    if (in_array($idMandato, $mandati_speciali) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }
    
switch ($idMandato) {
    case "Plenitude":
        $queryCrm = "SELECT 
                p.data, 
                p.sede, 
                CASE DAYOFWEEK(p.data)
                    WHEN 1 THEN 'Domenica'
                    WHEN 2 THEN 'Lunedì'
                    WHEN 3 THEN 'Martedì'
                    WHEN 4 THEN 'Mercoledì'
                    WHEN 5 THEN 'Giovedì'
                    WHEN 6 THEN 'Venerdì'
                    WHEN 7 THEN 'Sabato'
                END AS giorno_settimana,
                'Plenitude' AS mandato,
                
                SUM(a.pezzoLordo) AS pezziTotali,
                SUM(IF(a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziOK,
                SUM(IF(a.fasePDA = 'OK' AND p.tipoAcquisizione <> 'Subentro', a.pezzoLordo, 0)) AS pezziSwVoOK,
                SUM(IF(a.fasePDA = 'OK' AND a.tipoCampagna = 'Lead', a.pezzoLordo, 0)) AS pezziLeadOk,
                SUM(IF(a.fasePDA = 'KO', a.pezzoLordo, 0)) AS pezziKO,
                SUM(IF(a.fasePDA = 'BKL', a.pezzoLordo, 0)) AS pezziBKL,
                SUM(IF(a.fasePost = 'OK' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostOK,
                SUM(IF(a.fasePost = 'KO' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostKO,
                SUM(IF(a.fasePost = 'BKL' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostBKL,
                SUM(IF(p.dataSwitchOutLuce <> '0000-00-00' AND p.dataSwitchOutGas = '0000-00-00', 1, 0)) AS switchLuceOnly,
                SUM(IF(p.dataSwitchOutLuce = '0000-00-00' AND p.dataSwitchOutGas <> '0000-00-00', 1, 0)) AS switchGasOnly

            FROM 
                plenitude p
            INNER JOIN 
                aggiuntaPlenitude a ON p.id = a.id

            WHERE 
                p.data BETWEEN '$dataMinore' AND '$dataMaggiore'
                AND p.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                AND p.comodity <> 'Polizza' 
                $querySede

            GROUP BY 
                p.data";
        break;
        
    case "Vivigas Energia":
        $queryCrm = "SELECT 
            v.data, 
            v.sede, 
            CASE DAYOFWEEK(v.data)
                WHEN 1 THEN 'Domenica'
                WHEN 2 THEN 'Lunedì'
                WHEN 3 THEN 'Martedì'
                WHEN 4 THEN 'Mercoledì'
                WHEN 5 THEN 'Giovedì'
                WHEN 6 THEN 'Venerdì'
                WHEN 7 THEN 'Sabato'
            END AS giorno_settimana,
            'Vivigas' AS mandato,
            
            SUM(a.pezzoLordo) AS pezziTotali,
            SUM(IF(a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziOK,
            0,
            SUM(IF(a.fasePDA = 'OK' AND a.tipoCampagna = 'Lead', a.pezzoLordo, 0)) AS pezziLeadOk,
            SUM(IF(a.fasePDA = 'KO', a.pezzoLordo, 0)) AS pezziKO,
            SUM(IF(a.fasePDA = 'BKL', a.pezzoLordo, 0)) AS pezziBKL,
            SUM(IF(a.fasePost = 'OK' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostOK,
            SUM(IF(a.fasePost = 'KO' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostKO,
            SUM(IF(a.fasePost = 'BKL' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostBKL,
            0,
            0

        FROM 
            vivigas v
        INNER JOIN 
            aggiuntaVivigas a ON v.id = a.id

        WHERE 
            v.data BETWEEN '$dataMinore' AND '$dataMaggiore'
            AND v.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
            AND v.comodity <> 'Polizza' 
         $querySede 
        GROUP BY 
            v.data";
        break;
            
    case "Iren":
        $queryCrm = "SELECT 
            i.data,
            i.sede, 
            CASE DAYOFWEEK(i.data)
                WHEN 1 THEN 'Domenica'
                WHEN 2 THEN 'Lunedì'
                WHEN 3 THEN 'Martedì'
                WHEN 4 THEN 'Mercoledì'
                WHEN 5 THEN 'Giovedì'
                WHEN 6 THEN 'Venerdì'
                WHEN 7 THEN 'Sabato'
            END AS giorno_settimana,
            'Iren' AS mandato,
            
            SUM(a.pezzoLordo) AS pezziTotali,
            SUM(IF(a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziOK,
            0,
            SUM(IF(a.fasePDA = 'OK' AND a.tipoCampagna = 'Lead', a.pezzoLordo, 0)) AS pezziLeadOk,
            SUM(IF(a.fasePDA = 'KO', a.pezzoLordo, 0)) AS pezziKO,
            SUM(IF(a.fasePDA = 'BKL', a.pezzoLordo, 0)) AS pezziBKL,
            SUM(IF(a.fasePost = 'OK' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostOK,
            SUM(IF(a.fasePost = 'KO' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostKO,
            SUM(IF(a.fasePost = 'BKL' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostBKL,
            0,
            0

        FROM 
            iren i
        INNER JOIN 
            aggiuntaIren a ON i.id = a.id

        WHERE 
            i.data BETWEEN '$dataMinore' AND '$dataMaggiore'
            AND i.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
            AND i.comodity <> 'Polizza' 
           $querySede
        GROUP BY 
            i.data";
        break;

    case "Enel":
        $queryCrm = "SELECT 
            e.data, 
            e.sede, 
            CASE DAYOFWEEK(e.data)
                WHEN 1 THEN 'Domenica'
                WHEN 2 THEN 'Lunedì'
                WHEN 3 THEN 'Martedì'
                WHEN 4 THEN 'Mercoledì'
                WHEN 5 THEN 'Giovedì'
                WHEN 6 THEN 'Venerdì'
                WHEN 7 THEN 'Sabato'
            END AS giorno_settimana,
            'Enel' AS mandato,
            
            SUM(a.pezzoLordo) AS pezziTotali,
            SUM(IF(a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziOK,
            0,
            SUM(IF(a.fasePDA = 'OK' AND a.tipoCampagna = 'Lead', a.pezzoLordo, 0)) AS pezziLeadOk,
            SUM(IF(a.fasePDA = 'KO', a.pezzoLordo, 0)) AS pezziKO,
            SUM(IF(a.fasePDA = 'BKL', a.pezzoLordo, 0)) AS pezziBKL,
            SUM(IF(a.fasePost = 'OK' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostOK,
            SUM(IF(a.fasePost = 'KO' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostKO,
            SUM(IF(a.fasePost = 'BKL' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostBKL,
            0,
            0

        FROM 
            enel e
        INNER JOIN 
            aggiuntaEnel a ON e.id = a.id

        WHERE 
            e.data BETWEEN '$dataMinore' AND '$dataMaggiore'
            AND e.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
            AND e.comodity <> 'Polizza' 
            $querySede
        GROUP BY 
            e.data";
        break;
            
    case "Heracom":
        $queryCrm = "SELECT 
            h.data,
            'Lamezia' as sede, 
            CASE DAYOFWEEK(h.data)
                WHEN 1 THEN 'Domenica'
                WHEN 2 THEN 'Lunedì'
                WHEN 3 THEN 'Martedì'
                WHEN 4 THEN 'Mercoledì'
                WHEN 5 THEN 'Giovedì'
                WHEN 6 THEN 'Venerdì'
                WHEN 7 THEN 'Sabato'
            END AS giorno_settimana,
            'Heracom' AS mandato,
            
            SUM(a.pezzoLordo) AS pezziTotali,
            SUM(IF(a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziOK,
            0,
            SUM(IF(a.fasePDA = 'OK' AND a.tipoCampagna = 'Lead', a.pezzoLordo, 0)) AS pezziLeadOk,
            SUM(IF(a.fasePDA = 'KO', a.pezzoLordo, 0)) AS pezziKO,
            SUM(IF(a.fasePDA = 'BKL', a.pezzoLordo, 0)) AS pezziBKL,
            SUM(IF(a.fasePost = 'OK' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostOK,
            SUM(IF(a.fasePost = 'KO' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostKO,
            SUM(IF(a.fasePost = 'BKL' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostBKL,
            0,
            0

        FROM 
            heracom h
        INNER JOIN 
            aggiuntaHeracom a ON h.id = a.id

        WHERE 
            h.data BETWEEN '$dataMinore' AND '$dataMaggiore'
            AND h.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
            AND h.comodity <> 'Polizza' 
            $querySede
        GROUP BY 
            h.data";
        break;
        
    case "EnelIn":
        $queryCrm = "SELECT 
            ei.data,
            'Rende' as sede, 
            CASE DAYOFWEEK(ei.data)
                WHEN 1 THEN 'Domenica'
                WHEN 2 THEN 'Lunedì'
                WHEN 3 THEN 'Martedì'
                WHEN 4 THEN 'Mercoledì'
                WHEN 5 THEN 'Giovedì'
                WHEN 6 THEN 'Venerdì'
                WHEN 7 THEN 'Sabato'
            END AS giorno_settimana,
            'EnelIn' AS mandato,
            
            SUM(a.pezzoLordo) AS pezziTotali,
            SUM(IF(a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziOK,
            0,
            SUM(IF(a.fasePDA = 'OK' AND a.tipoCampagna = 'Lead', a.pezzoLordo, 0)) AS pezziLeadOk,
            SUM(IF(a.fasePDA = 'KO', a.pezzoLordo, 0)) AS pezziKO,
            SUM(IF(a.fasePDA = 'BKL', a.pezzoLordo, 0)) AS pezziBKL,
            SUM(IF(a.fasePost = 'OK' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostOK,
            SUM(IF(a.fasePost = 'KO' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostKO,
            SUM(IF(a.fasePost = 'BKL' AND a.fasePDA = 'OK', a.pezzoLordo, 0)) AS pezziPostBKL,
            0,
            0

        FROM 
            enelIn ei
        INNER JOIN 
            aggiuntaEnelIn a ON ei.id = a.id

        WHERE 
            ei.data BETWEEN '$dataMinore' AND '$dataMaggiore'
            AND ei.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
            AND ei.comodity <> 'Polizza' 
            $querySede
        GROUP BY 
            ei.data";
        break;
}
}
$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaMensile.php";

$risultatoCrm = $conn19->query($queryCrm);
    while ($rigaCRM = $risultatoCrm->fetch_array()) {
        $data = $rigaCRM["data"];
        $sedeCorrente = $rigaCRM["sede"];
        $giorno = $rigaCRM["giorno_settimana"];
        $Mandato = $rigaCRM["mandato"];
        $pezziTotali = $rigaCRM["pezziTotali"];
        $pezziOK = $rigaCRM["pezziOK"];
        $pezziLeadOk = $rigaCRM["pezziLeadOk"];
        
        $queryGroupMandato = "SELECT 
                sum(numero)/3600 as ore 
            FROM `stringheTotale`  
            WHERE giorno= '$data' AND livello<=6  

            AND idMandato='$idMandato'  
            GROUP BY giorno";

        $risultaOre = $conn19->query($queryGroupMandato);
        if (($risultaOre->num_rows) > 0) {
            $rigaOre = $risultaOre->fetch_array();
            $ore = $rigaOre[0];
        } else {
            $ore = 0;
        }
        
        $pezzoLordo = round($rigaCRM["pezziTotali"] ?? 0, 0);
        $pezzoOk = round($rigaCRM["pezziOK"] ?? 0, 0);
        $pezzoKo = round($rigaCRM["pezziKO"] ?? 0, 0);
        $pezzoBkl = round($rigaCRM["pezziBKL"] ?? 0, 0);
        $pezzoPostOk = round($rigaCRM["pezziPostOK"] ?? 0, 0);
        $pezzoPostKo = round($rigaCRM["pezziPostKO"] ?? 0, 0);
        $pezzoPostBkl = round($rigaCRM["pezziPostBKL"] ?? 0, 0);
        $pezzoOkSwVo = round($rigaCRM["pezziSwVoOK"] ?? 0, 0);
        $pezzoOkLead = round($rigaCRM["pezziLeadOk"] ?? 0, 0);
        
        // Calcolo resa inserimento
        $resaIns = ($ore > 0) ? round($pezzoOk / $ore, 2) : 0;
        
        // Calcolo resa post
        $resaPostOK = ($pezzoOk > 0) ? round(($pezzoPostOk / $pezzoOk) * 100, 2) : 0;
        $resaPostKO = ($pezzoOk > 0) ? round(($pezzoPostKo / $pezzoOk) * 100, 2) : 0;
        $resaPostBkl = ($pezzoOk > 0) ? round(($pezzoPostBkl / $pezzoOk) * 100, 2) : 0;
        
        // Switch out data
        $dataSwo = ($rigaCRM["switchLuceOnly"] ?? 0) + ($rigaCRM["switchGasOnly"] ?? 0);
        $percentualeDataSwo = ($pezzoOk > 0) ? round(($dataSwo / $pezzoOk) * 100, 2) : 0;

        $html .= "<tr>";
        $html .= "<td>$data</td>";
        $html .= "<td>$giorno</td>";
        $html .= "<td>$idMandato</td>";
        $html .= "<td>" . round($ore, 2) . "</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordo</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOk</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOkSwVo</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOkLead</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$resaIns</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoKo</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBkl</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostOk</td>";
        $html .= "<td>$resaPostOK</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostKo</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$dataSwo</td>";
        $html .= "<td>$percentualeDataSwo</td>";
        $html .= "</tr>";

        // Aggiorna totali sede
        $pezzoLordoSede += $pezzoLordo;
        $pezzoOkSede += $pezzoOk;
        $pezzoKoSede += $pezzoKo;
        $pezzoBklSede += $pezzoBkl;
        $oreSede += $ore;
        $pezzoPostOkSede += $pezzoPostOk;
        $pezzoPostKoSede += $pezzoPostKo;
        $pezzoPostBklSede += $pezzoPostBkl;
        $pezzoOkSwVoSede += $pezzoOkSwVo;
        $pezzoOkLeadSede += $pezzoOkLead;
        $dataSwoSede += $dataSwo;
    }


// Calcola totali generali
$pezzoLordoTotale += $pezzoLordoSede;
$pezzoOkTotale += $pezzoOkSede;
$pezzoKoTotale += $pezzoKoSede;
$pezzoBklTotale += $pezzoBklSede;
$oreTotale += round($oreSede, 2);
$pezzoPostOkTotale += $pezzoPostOkSede;
$pezzoPostKoTotale += $pezzoPostKoSede;
$pezzoPostBklTotale += $pezzoPostBklSede;
$pezzoOkSwVoTotale += $pezzoOkSwVoSede;
$pezzoOkLeadTotale += $pezzoOkLeadSede;
$dataSwoTotale += $dataSwoSede;

// Calcola resa totale
$resaInsTotale = ($oreTotale > 0) ? round($pezzoOkTotale / $oreTotale, 2) : 0;
$resaPostOkTotale = ($pezzoOkTotale > 0) ? round(($pezzoPostOkTotale / $pezzoOkTotale) * 100, 2) : 0;
$percentualeDataSwoTotale = ($pezzoOkTotale > 0) ? round(($dataSwoTotale / $pezzoOkTotale) * 100, 2) : 0;

// Aggiungi riga totale
$html .= "<tr style='background-color: orangered;border: 2px solid lightslategray'>";
$html .= "<td colspan='3'>TOTALE</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$oreTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordoTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOkTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOkSwVoTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOkLeadTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$resaInsTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoKoTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoBklTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostOkTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$resaPostOkTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoPostKoTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$dataSwoTotale</td>";
$html .= "<td>$percentualeDataSwoTotale</td>";
$html .= "</tr>";

$html .= "</table>";

echo $html;