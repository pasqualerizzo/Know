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

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniCruscottoKpi.php";

try {
    $obj19 = new Connessione();
    $conn19 = $obj19->apriConnessione();

    // Ricezione e sanitizzazione parametri
    $dataMinore = filter_input(INPUT_POST, "dataMinore", FILTER_SANITIZE_STRING);
    $dataMaggiore = filter_input(INPUT_POST, "dataMaggiore", FILTER_SANITIZE_STRING);
    $mandato = json_decode($_POST["mandato"] ?? '[]', true) ?? [];
    $sede = json_decode($_POST["sede"] ?? '[]', true) ?? [];

    // Validazione date
    if (empty($dataMinore) || empty($dataMaggiore)) {
        throw new Exception("Le date sono obbligatorie");
    }

    $dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
    $dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

    // Calcolo delle 6 settimane nel range di date
    $startDate = new DateTime($dataMinore);
    $endDate = new DateTime($dataMaggiore);
    $interval = $startDate->diff($endDate);
    $totalDays = $interval->days + 1;
    
    $weeks = array();
    $weekDuration = ceil($totalDays / 6);
    
    for ($i = 0; $i < 6; $i++) {
        $weekStart = clone $startDate;
        $weekStart->add(new DateInterval('P'.($i * $weekDuration).'D'));
        
        $weekEnd = clone $weekStart;
        $weekEnd->add(new DateInterval('P'.($weekDuration - 1).'D'));
        if ($weekEnd > $endDate) {
            $weekEnd = $endDate;
        }
        
        $weeks[] = array(
            'start' => $weekStart->format('Y-m-d'),
            'end' => $weekEnd->format('Y-m-d'),
            'label' => $weekStart->format('d/m').' - '.$weekEnd->format('d/m')
        );
    }

    // Generazione HTML per le 6 settimane
    echo '<div class="week-container">';
    foreach ($weeks as $week) {
        echo '<div class="week-box">';
        echo '<h4>'.$week['label'].'</h4>';
        
        // Qui puoi aggiungere il contenuto di ogni riquadro settimanale
        // Ad esempio, potresti richiamare una funzione che calcola le statistiche per quel periodo
        // getWeeklyStats($conn19, $week['start'], $week['end'], $sede);
        
        echo '</div>';
    }
    echo '</div>';

    // Stile CSS per i riquadri settimanali
    echo '<style>
    .week-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    .week-box {
        border: 1px solid #ddd;
        padding: 10px;
        flex: 1 1 calc(16.66% - 20px);
        min-width: 150px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        background-color: #f9f9f9;
    }
    .week-box h4 {
        margin-top: 0;
        text-align: center;
        color: #333;
    }
    </style>';

    // Il resto del tuo codice originale per la tabella
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

    // Query principale per statistiche operatori
    $queryStatisticheOperatori = "
    SELECT
    sede,
    COUNT(DISTINCT nomeCompleto) AS numero_operatori_mese_corrente,
    COUNT(DISTINCT CASE WHEN dataAssunzione BETWEEN 
        DATE_FORMAT('$dataMinore', '%Y-%m-01') AND 
        LAST_DAY('$dataMaggiore')
        THEN nomeCompleto ELSE NULL END) AS operatori_assunti_mese_corrente,
    COUNT(DISTINCT CASE WHEN nomeCompleto IN (
        SELECT DISTINCT nomeCompleto FROM `stringheTotale`
        WHERE giorno BETWEEN DATE_SUB('$dataMinore', INTERVAL 1 MONTH) AND DATE_SUB('$dataMaggiore', INTERVAL 1 MONTH)
        AND livello <= 6 AND LENGTH(nomeCompleto) > 4 AND mandato NOT IN('BO', 'Bo')
        UNION
        SELECT DISTINCT nomeCompleto FROM `stringheSiscallLeadTC`
        WHERE giorno BETWEEN DATE_SUB('$dataMinore', INTERVAL 1 MONTH) AND DATE_SUB('$dataMaggiore', INTERVAL 1 MONTH)
        AND livello <= 6 AND LENGTH(nomeCompleto) > 4 AND idMandato = 'Heracom'
    ) THEN nomeCompleto ELSE NULL END) AS numero_operatori_mese_precedente,
    COUNT(DISTINCT CASE WHEN dataAssunzione BETWEEN 
        DATE_FORMAT(DATE_SUB('$dataMinore', INTERVAL 1 MONTH), '%Y-%m-01') AND 
        LAST_DAY(DATE_SUB('$dataMinore', INTERVAL 1 MONTH))
        THEN nomeCompleto ELSE NULL END) AS operatori_assunti_mese_precedente
    FROM (
        SELECT 
            nomeCompleto,
            sede,
            dataAssunzione
        FROM `stringheTotale`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore'
        AND livello <= 6 
        AND LENGTH(nomeCompleto) > 4 
        AND mandato NOT IN('BO', 'Bo')
        $querySede
        
        UNION
        
        SELECT 
            nomeCompleto,
            sede,
            dataAssunzione
        FROM `stringheSiscallLeadTC`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore'
        AND livello <= 6 
        AND LENGTH(nomeCompleto) > 4 
        AND idMandato = 'Heracom'
        $querySede
    ) AS combined_data
    GROUP BY sede
    ORDER BY sede";

    // Esegui la query e genera la tabella come nel tuo codice originale
    $risultatoStatistiche = $conn19->query($queryStatisticheOperatori);

    // Generazione HTML
    $html = "<table class='blueTable' id='table-1'>
        <thead>
            <tr>
                <th>Sede</th>
                <th>Teste In forza</th>
                <th>Ingresso</th>
                <th>Abbandoni</th>
                <th>Allert</th>
                <th><div>Abbandono %</div></th>
                <th><div>In Forza Mese -1</div></th>
                <th><div>Ingresso Mese -1</div></th>
            </tr>
        </thead>
        <tbody>";

    // Variabili per i totali
    $totale_operatori = 0;
    $totale_assunti_corrente = 0;
    $totale_abbandoni = 0;
    $totale_operatori_precedente = 0;
    $totale_assunti_precedente = 0;
    $somma_abbandoni = 0;
    $conta_sedi = 0;

    while ($riga = $risultatoStatistiche->fetch_assoc()) {
        $sede = htmlspecialchars($riga['sede']);
        $operatori_mese_corrente = $riga['numero_operatori_mese_corrente'];
        $operatori_assuntimese_corrente = $riga['operatori_assunti_mese_corrente'];
        $numero_operatori_mese_precedente = $riga['numero_operatori_mese_precedente'];
        $operatori_assunti_mese_precedente = $riga['operatori_assunti_mese_precedente'];
   
        $andati = ($numero_operatori_mese_precedente - $operatori_mese_corrente);
        if ($andati < 0) {
            $andati = 0;
        }
        
        $abbandono_mese_corrente = 0;
        if ($numero_operatori_mese_precedente > 0) {
            $abbandono_mese_corrente = round((($numero_operatori_mese_precedente - $operatori_mese_corrente) / $numero_operatori_mese_precedente) * 100, 2);
        }

        // Aggiorna i totali
        $totale_operatori += $operatori_mese_corrente;
        $totale_assunti_corrente += $operatori_assuntimese_corrente;
        $totale_abbandoni += $andati;
        $totale_operatori_precedente += $numero_operatori_mese_precedente;
        $totale_assunti_precedente += $operatori_assunti_mese_precedente;
        $somma_abbandoni += $abbandono_mese_corrente;
        $conta_sedi++;
        
        $html .= "<tr>
            <td>$sede</td>
            <td>$operatori_mese_corrente</td>
            <td>$operatori_assuntimese_corrente</td>
            <td>$andati</td>
            <td></td>
            <td>$abbandono_mese_corrente%</td>
            <td>$numero_operatori_mese_precedente</td>
            <td>$operatori_assunti_mese_precedente</td>
        </tr>";
    }

    $media_abbandoni = $conta_sedi > 0 ? round($somma_abbandoni / $conta_sedi, 2) : 0;

    $html .= "<tr style='background-color: orange; font-weight: bold;'>
        <td><strong>TOTALE</strong></td>
        <td><strong>$totale_operatori</strong></td>
        <td><strong>$totale_assunti_corrente</strong></td>
        <td><strong>$totale_abbandoni</strong></td>
        <td></td>
        <td><strong>$media_abbandoni%</strong></td>
        <td><strong>$totale_operatori_precedente</strong></td>
        <td><strong>$totale_assunti_precedente</strong></td>
    </tr>";

    $html .= "</tbody></table>";
    echo $html;

} catch (Exception $e) {
    echo "<div class='error'>Errore: " . htmlspecialchars($e->getMessage()) . "</div>";
}