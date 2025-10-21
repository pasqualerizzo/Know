<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniCruscottoKpi.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

// Ricezione e sanitizzazione parametri
$dataMinore = filter_input(INPUT_POST, "dataMinore", FILTER_SANITIZE_STRING);
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore", FILTER_SANITIZE_STRING);
$mandato = json_decode($_POST["mandato"] ?? '[]', true) ?? [];
$sede = json_decode($_POST["sede"] ?? '[]', true) ?? [];

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$lead = recuperoLead($conn19, $dataMaggiore, $dataMinore);
$idMandato = is_array($mandato) ? ($mandato[0] ?? '') : $mandato;

// Costruzione query sede
$querySede = "";
if (!empty($sede)) {
    $sedeEscaped = array_map([$conn19, 'real_escape_string'], $sede);
    if (count($sedeEscaped) == 1) {
        $querySede = " AND sede='{$sedeEscaped[0]}' ";
    } else {
        $querySede = " AND (sede='" . implode("' OR sede='", $sedeEscaped) . "') ";
    }
}

// Funzione per tradurre i giorni della settimana
function translateDay($englishDay) {
    $days = [
        'Monday' => 'Lunedì',
        'Tuesday' => 'Martedì',
        'Wednesday' => 'Mercoledì',
        'Thursday' => 'Giovedì',
        'Friday' => 'Venerdì',
        'Saturday' => 'Sabato',
        'Sunday' => 'Domenica'
    ];
    return $days[$englishDay] ?? $englishDay;
}

// Funzione per ottenere i giorni lavorativi precedenti saltando le domeniche
function getPreviousWorkdays($referenceDate, $days) {
    $workdays = [];
    $date = new DateTime($referenceDate);
    
    while(count($workdays) < $days) {
        $date->modify('-1 day');
        if ($date->format('N') != 7) { // 7 = domenica
            $workdays[] = $date->format('Y-m-d');
        }
    }
    
    return array_slice($workdays, 0, $days);
}

// Calcola i 4 giorni lavorativi precedenti (escludendo domeniche)
$workDays = getPreviousWorkdays($dataMaggiore, 4);

// Verifica se è stato selezionato "Tutti" i mandati
$tuttiMandati = (is_array($mandato) && in_array('Tutti', $mandato)) || $idMandato === 'Tutti';

$queryGroupMandato = "";
if ($tuttiMandati) {
    // Query per tutti i mandati (unione di tutte le tabelle)
    $queryGroupMandato = "SELECT 
        nomeCompleto,
        sede,
        'Tutti' as idMandato,
        SUM(ore) as ore,
        SUM(giorno_1) as giorno_1,
        SUM(giorno_2) as giorno_2,
        SUM(giorno_3) as giorno_3,
        SUM(giorno_4) as giorno_4,
        MAX(dataAssunzione) as dataAssunzione
    FROM (
        SELECT 
            nomeCompleto,
            sede,
            SUM(numero)/3600 as ore,
            SUM(CASE WHEN giorno = '{$workDays[0]}' THEN numero ELSE 0 END)/3600 as giorno_1,
            SUM(CASE WHEN giorno = '{$workDays[1]}' THEN numero ELSE 0 END)/3600 as giorno_2,
            SUM(CASE WHEN giorno = '{$workDays[2]}' THEN numero ELSE 0 END)/3600 as giorno_3,
            SUM(CASE WHEN giorno = '{$workDays[3]}' THEN numero ELSE 0 END)/3600 as giorno_4,
            MAX(dataAssunzione) as dataAssunzione
        FROM `stringheTotale`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' 
            AND livello <= 6 
            AND LENGTH(nomeCompleto) > 4 
            AND mandato NOT IN ('BO', 'Bo', 'EnelIn', 'Tl', 'hr')
            $querySede
        GROUP BY nomeCompleto, sede
        
        UNION ALL
        
        SELECT 
            nomeCompleto,
            sede,
            SUM(numero)/3600 as ore,
            SUM(CASE WHEN giorno = '{$workDays[0]}' THEN numero ELSE 0 END)/3600 as giorno_1,
            SUM(CASE WHEN giorno = '{$workDays[1]}' THEN numero ELSE 0 END)/3600 as giorno_2,
            SUM(CASE WHEN giorno = '{$workDays[2]}' THEN numero ELSE 0 END)/3600 as giorno_3,
            SUM(CASE WHEN giorno = '{$workDays[3]}' THEN numero ELSE 0 END)/3600 as giorno_4,
            MAX(dataAssunzione) as dataAssunzione
        FROM `stringheSiscallLeadTC`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' 
            AND livello <= 6 
            AND LENGTH(nomeCompleto) > 4 
            AND idMandato = 'Heracom' 
            AND userGroup = 'OP_Lam_piannazzo'
            $querySede
        GROUP BY nomeCompleto, sede
        
        UNION ALL
        
        SELECT 
            nomeCompleto,
            sede,
            SUM(oreDichiarate) as ore,
            SUM(CASE WHEN giorno = '{$workDays[0]}' THEN oreDichiarate ELSE 0 END) as giorno_1,
            SUM(CASE WHEN giorno = '{$workDays[1]}' THEN oreDichiarate ELSE 0 END) as giorno_2,
            SUM(CASE WHEN giorno = '{$workDays[2]}' THEN oreDichiarate ELSE 0 END) as giorno_3,
            SUM(CASE WHEN giorno = '{$workDays[3]}' THEN oreDichiarate ELSE 0 END) as giorno_4,
            NULL as dataAssunzione
        FROM `oreTIMmq`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore'
            $querySede
            AND idMandato = 'TIMmq'
        GROUP BY nomeCompleto, sede
    ) as combined
    GROUP BY nomeCompleto, sede
    ORDER BY sede, nomeCompleto";
} else {
    // Query per mandato specifico
    switch ($idMandato) {
        case "Plenitude":
        case "Vivigas Energia":
        case "Iren":
        case "Tim":
            $queryGroupMandato = "SELECT 
                nomeCompleto,
                sede,
                idMandato,
                SUM(numero)/3600 as ore,
                SUM(CASE WHEN giorno = '{$workDays[0]}' THEN numero ELSE 0 END)/3600 as giorno_1,
                SUM(CASE WHEN giorno = '{$workDays[1]}' THEN numero ELSE 0 END)/3600 as giorno_2,
                SUM(CASE WHEN giorno = '{$workDays[2]}' THEN numero ELSE 0 END)/3600 as giorno_3,
                SUM(CASE WHEN giorno = '{$workDays[3]}' THEN numero ELSE 0 END)/3600 as giorno_4,
                MAX(dataAssunzione) as dataAssunzione
            FROM `stringheTotale`
            WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' 
                AND livello <= 6 
                AND LENGTH(nomeCompleto) > 4 
                AND mandato NOT IN ('BO', 'Bo', 'EnelIn', 'Tl', 'hr')
                $querySede
                AND idMandato = '".$conn19->real_escape_string($idMandato)."'
            GROUP BY nomeCompleto, sede
            ORDER BY sede, nomeCompleto";
            break;
        
        case "Heracom":
            $queryGroupMandato = "SELECT 
                nomeCompleto,
                sede,
                idMandato,
                SUM(numero)/3600 as ore,
                SUM(CASE WHEN giorno = '{$workDays[0]}' THEN numero ELSE 0 END)/3600 as giorno_1,
                SUM(CASE WHEN giorno = '{$workDays[1]}' THEN numero ELSE 0 END)/3600 as giorno_2,
                SUM(CASE WHEN giorno = '{$workDays[2]}' THEN numero ELSE 0 END)/3600 as giorno_3,
                SUM(CASE WHEN giorno = '{$workDays[3]}' THEN numero ELSE 0 END)/3600 as giorno_4,
                MAX(dataAssunzione) as dataAssunzione
            FROM `stringheSiscallLeadTC`
            WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' 
                AND livello <= 6 
                AND LENGTH(nomeCompleto) > 4 
                AND idMandato = 'Heracom' 
                AND userGroup = 'OP_Lam_piannazzo'
                $querySede
            GROUP BY nomeCompleto, sede
            ORDER BY sede, nomeCompleto";
            break;
        
        case "TIMmq":
            $queryGroupMandato = "SELECT 
                nomeCompleto,
                sede,
                idMandato,
                SUM(oreDichiarate) as ore,
                SUM(CASE WHEN giorno = '{$workDays[0]}' THEN oreDichiarate ELSE 0 END) as giorno_1,
                SUM(CASE WHEN giorno = '{$workDays[1]}' THEN oreDichiarate ELSE 0 END) as giorno_2,
                SUM(CASE WHEN giorno = '{$workDays[2]}' THEN oreDichiarate ELSE 0 END) as giorno_3,
                SUM(CASE WHEN giorno = '{$workDays[3]}' THEN oreDichiarate ELSE 0 END) as giorno_4,
                NULL as dataAssunzione
            FROM `oreTIMmq`
            WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore'
                $querySede
                AND idMandato = 'TIMmq'
            GROUP BY nomeCompleto, sede
            ORDER BY sede, nomeCompleto";
            break;
        default:
            // Query di default simile alla prima versione
            $queryGroupMandato = "SELECT 
                nomeCompleto,
                sede,
                idMandato,
                SUM(numero)/3600 as ore,
                SUM(CASE WHEN giorno = '{$workDays[0]}' THEN numero ELSE 0 END)/3600 as giorno_1,
                SUM(CASE WHEN giorno = '{$workDays[1]}' THEN numero ELSE 0 END)/3600 as giorno_2,
                SUM(CASE WHEN giorno = '{$workDays[2]}' THEN numero ELSE 0 END)/3600 as giorno_3,
                SUM(CASE WHEN giorno = '{$workDays[3]}' THEN numero ELSE 0 END)/3600 as giorno_4,
                MAX(dataAssunzione) as dataAssunzione
            FROM `stringheTotale`
            WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' 
                AND livello <= 6 
                AND LENGTH(nomeCompleto) > 4 
                AND mandato NOT IN ('BO', 'Bo')
                $querySede
            GROUP BY nomeCompleto, sede
            ORDER BY sede, nomeCompleto";
    }
}

$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);

// Generazione HTML
$html = "<table class='blueTable' id='table-1'>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Sede</th>
            <th>Ingresso</th>
            <th>Allert</th>
            <th><div>".translateDay(date('l', strtotime($workDays[0])))."</div><div>".date('d/m/Y', strtotime($workDays[0]))."</div></th>
            <th><div>".translateDay(date('l', strtotime($workDays[1])))."</div><div>".date('d/m/Y', strtotime($workDays[1]))."</div></th>
            <th><div>".translateDay(date('l', strtotime($workDays[2])))."</div><div>".date('d/m/Y', strtotime($workDays[2]))."</div></th>
            <th><div>".translateDay(date('l', strtotime($workDays[3])))."</div><div>".date('d/m/Y', strtotime($workDays[3]))."</div></th>
        </tr>
    </thead>
    <tbody>";

$sedePrecedente = "inizio";
$meseCorrente = date('m');
$annoCorrente = date('Y');

while ($rigaMandato = $risultatoQueryGroupMandato->fetch_assoc()) {
    $user = htmlspecialchars($rigaMandato['nomeCompleto']);
    $sede = htmlspecialchars($rigaMandato['sede']);
    
    // Gestione data assunzione
    $dataAssunzioneDisplay = '';
    if (!empty($rigaMandato['dataAssunzione'])) {
        $dataAss = date_create($rigaMandato['dataAssunzione']);
        $meseAssunzione = date_format($dataAss, 'm');
        $annoAssunzione = date_format($dataAss, 'Y');
        
        if ($meseAssunzione == $meseCorrente && $annoAssunzione == $annoCorrente) {
            $dataAssunzioneDisplay = 'Ingresso';
        }
    }
    
    // Separatore tra sedi diverse
    if ($sedePrecedente != "inizio" && $sedePrecedente != $sede) {
        $html .= "<tr style='background-color: orange'><td colspan='8'></td></tr>";
    }
    
    $sommaOreGiorni = round($rigaMandato['giorno_1'], 2) + 
                      round($rigaMandato['giorno_2'], 2) + 
                      round($rigaMandato['giorno_3'], 2) + 
                      round($rigaMandato['giorno_4'], 2);

    $allert = ($sommaOreGiorni == 0) ? 'ALLERT' : '';

    $html .= "<tr>
        <td>$user</td>
        <td>$sede</td>
        <td>$dataAssunzioneDisplay</td>
        <td>$allert</td>
        <td>".round($rigaMandato['giorno_1'], 2)."</td>
        <td>".round($rigaMandato['giorno_2'], 2)."</td>
        <td>".round($rigaMandato['giorno_3'], 2)."</td>
        <td>".round($rigaMandato['giorno_4'], 2)."</td>
    </tr>";
    
    $sedePrecedente = $sede;
}

$html .= "</tbody></table>";
echo $html;
?>