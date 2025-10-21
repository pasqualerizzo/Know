<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniCruscottoKpi.php";

// Funzione per calcolare i giorni lavorativi precedenti (esclude domeniche)
function calcolaGiorniLavorativiPrecedenti($data, $giorni) {
    $contatore = 0;
    $dataCorrente = new DateTime($data);
    
    while ($contatore < $giorni) {
        $dataCorrente->modify('-1 day');
        $giornoSettimana = $dataCorrente->format('w'); // 0 = domenica, 1 = lunedì, etc.
        
        if ($giornoSettimana != 0) { // Salta la domenica
            $contatore++;
        }
    }
    
    return $dataCorrente->format('Y-m-d');
}

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

    // Calcolo date mese precedente (intero mese solare)
    $mesePrecedenteMinore = date('Y-m-01', strtotime("$dataMinore -1 month"));
    $mesePrecedenteMaggiore = date('Y-m-t', strtotime("$dataMinore -1 month"));

    // Calcolo data 4 giorni lavorativi prima di dataMaggiore (escludendo domeniche)
    $data4GiorniPrima = calcolaGiorniLavorativiPrecedenti($dataMaggiore, 4);

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

    // 1. QUERY PER OPERATORI NON LOGGATI (ultimi 4 giorni lavorativi) - ALLERT
    $queryOperatoriNonLoggati = "
    SELECT 
        sede,
        COUNT(DISTINCT nomeCompleto) AS operatori_non_loggati
    FROM (
        -- Operatori che hanno lavorato nel mese corrente
        SELECT DISTINCT nomeCompleto, sede 
        FROM `stringheTotale`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore'
        AND livello <= 6 
        AND LENGTH(nomeCompleto) > 4 
        AND mandato NOT IN('BO', 'Bo')
        AND sede NOT IN ('benchmark', 'bo' , 'corigliano' , 'rende_out' , 'rende_tim','sanmarco' ,'vibo valentia' ,'tl','' ,'benchmark ' ,' benchmark')
        $querySede
        
        UNION
        
        SELECT DISTINCT nomeCompleto, sede 
        FROM `stringheSiscallLeadTC`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore'
        AND livello <= 6 
        AND LENGTH(nomeCompleto) > 4 
        AND idMandato = 'Heracom'
        AND sede NOT IN ('benchmark', 'bo' , 'corigliano' , 'rende_out' , 'rende_tim','sanmarco' ,'vibo valentia' ,'tl','' ,'benchmark ' ,' benchmark')
        $querySede
    ) AS operatori_mese_corrente
    WHERE nomeCompleto NOT IN (
        -- Operatori che si sono loggati negli ultimi 4 giorni
        SELECT DISTINCT nomeCompleto 
        FROM `stringheTotale`
        WHERE giorno BETWEEN '$data4GiorniPrima' AND '$dataMaggiore'
        AND livello <= 6
        AND mandato NOT IN('BO', 'Bo')
        AND sede NOT IN ('benchmark', 'bo' , 'corigliano' , 'rende_out' , 'rende_tim','sanmarco' ,'vibo valentia' ,'tl','' ,'benchmark ' ,' benchmark')
        $querySede
        
        UNION
        
        SELECT DISTINCT nomeCompleto 
        FROM `stringheSiscallLeadTC`
        WHERE giorno BETWEEN '$data4GiorniPrima' AND '$dataMaggiore'
        AND livello <= 6
        AND idMandato = 'Heracom'
        AND sede NOT IN ('benchmark', 'bo' , 'corigliano' , 'rende_out' , 'rende_tim','sanmarco' ,'vibo valentia' ,'tl','' ,'benchmark ' ,' benchmark')
        $querySede
    )
    GROUP BY sede";

    $risultatoNonLoggati = $conn19->query($queryOperatoriNonLoggati);
    $operatoriNonLoggati = [];
    while ($riga = $risultatoNonLoggati->fetch_assoc()) {
        $operatoriNonLoggati[$riga['sede']] = $riga['operatori_non_loggati'];
    }

    // 2. QUERY PER MESE CORRENTE
    $queryMeseCorrente = "
    SELECT
        sede,
        COUNT(DISTINCT nomeCompleto) AS numero_operatori_mese_corrente,
        COUNT(DISTINCT CASE WHEN dataAssunzione BETWEEN 
            DATE_FORMAT('$dataMinore', '%Y-%m-01') AND 
            LAST_DAY('$dataMaggiore')
            THEN nomeCompleto ELSE NULL END) AS operatori_assunti_mese_corrente
    FROM (
        SELECT DISTINCT
            nomeCompleto,
            sede,
            dataAssunzione
        FROM `stringheTotale`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore'
        AND livello <= 6 
        AND LENGTH(nomeCompleto) > 4 
        AND mandato NOT IN('BO', 'Bo')
        AND sede NOT IN ('benchmark', 'bo' , 'corigliano' , 'rende_out' , 'rende_tim','sanmarco' ,'vibo valentia' ,'tl','' ,'benchmark ' ,' benchmark')
        $querySede
        
        UNION
        
        SELECT DISTINCT
            nomeCompleto,
            sede,
            dataAssunzione
        FROM `stringheSiscallLeadTC`
        WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore'
        AND livello <= 6 
        AND LENGTH(nomeCompleto) > 4 
        AND idMandato = 'Heracom'
        AND sede NOT IN ('benchmark', 'bo' , 'corigliano' , 'rende_out' , 'rende_tim','sanmarco' ,'vibo valentia' ,'tl','' ,'benchmark ' ,' benchmark')
        $querySede
    ) AS combined_data
    GROUP BY sede";

    $risultatoMeseCorrente = $conn19->query($queryMeseCorrente);
    $datiMeseCorrente = [];
    while ($riga = $risultatoMeseCorrente->fetch_assoc()) {
        $datiMeseCorrente[$riga['sede']] = [
            'operatori_correnti' => $riga['numero_operatori_mese_corrente'],
            'assunti_correnti' => $riga['operatori_assunti_mese_corrente']
        ];
    }

    // 3. QUERY PER MESE PRECEDENTE (INTERO MESE SOLARE)
    $queryMesePrecedente = "
    SELECT
        sede,
        COUNT(DISTINCT nomeCompleto) AS numero_operatori_mese_precedente,
        COUNT(DISTINCT CASE WHEN dataAssunzione BETWEEN 
            '$mesePrecedenteMinore' AND 
            '$mesePrecedenteMaggiore'
            THEN nomeCompleto ELSE NULL END) AS operatori_assunti_mese_precedente
    FROM (
        SELECT DISTINCT
            nomeCompleto,
            sede,
            dataAssunzione
        FROM `stringheTotale`
        WHERE giorno BETWEEN '$mesePrecedenteMinore' AND '$mesePrecedenteMaggiore'
        AND livello <= 6 
        AND LENGTH(nomeCompleto) > 4 
        AND mandato NOT IN('BO', 'Bo')
        AND sede NOT IN ('benchmark', 'bo' , 'corigliano' , 'rende_out' , 'rende_tim','sanmarco' ,'vibo valentia' ,'tl','' ,'benchmark ' ,' benchmark')
        $querySede
        
        UNION
        
        SELECT DISTINCT
            nomeCompleto,
            sede,
            dataAssunzione
        FROM `stringheSiscallLeadTC`
        WHERE giorno BETWEEN '$mesePrecedenteMinore' AND '$mesePrecedenteMaggiore'
        AND livello <= 6 
        AND LENGTH(nomeCompleto) > 4 
        AND idMandato = 'Heracom'
        AND sede NOT IN ('benchmark', 'bo' , 'corigliano' , 'rende_out' , 'rende_tim','sanmarco' ,'vibo valentia' ,'tl','' ,'benchmark ' ,' benchmark')
        $querySede
    ) AS combined_data
    GROUP BY sede";

    $risultatoMesePrecedente = $conn19->query($queryMesePrecedente);
    $datiMesePrecedente = [];
    while ($riga = $risultatoMesePrecedente->fetch_assoc()) {
        $datiMesePrecedente[$riga['sede']] = [
            'operatori_precedenti' => $riga['numero_operatori_mese_precedente'],
            'assunti_precedenti' => $riga['operatori_assunti_mese_precedente']
        ];
    }

    // Generazione HTML
    $html = "<table class='blueTable' id='table-1'>
        <thead>
            <tr>
                <th>Sede</th>
                <th>Teste In forza</th>
                <th>Ingresso</th>
                <th>Abbandoni</th>
                <th>Allert (4gg)</th>
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
    $totale_non_loggati = 0;

    // Unione dei risultati
    $sediUniche = array_unique(array_merge(
        array_keys($datiMeseCorrente),
        array_keys($datiMesePrecedente),
        array_keys($operatoriNonLoggati)
    ));

    foreach ($sediUniche as $sede) {
        $corrente = $datiMeseCorrente[$sede] ?? ['operatori_correnti' => 0, 'assunti_correnti' => 0];
        $precedente = $datiMesePrecedente[$sede] ?? ['operatori_precedenti' => 0, 'assunti_precedenti' => 0];
        $non_loggati = $operatoriNonLoggati[$sede] ?? 0;

        $operatori_correnti = $corrente['operatori_correnti'];
        $assunti_correnti = $corrente['assunti_correnti'];
        $operatori_precedenti = $precedente['operatori_precedenti'];
        $assunti_precedenti = $precedente['assunti_precedenti'];

        // Calcolo abbandoni
        $abbandoni = ($operatori_precedenti - $operatori_correnti);
        if ($abbandoni < 0) $abbandoni = 0;

        // Calcolo abbandoni percentuale
        $abbandono_percentuale = 0;
        if ($operatori_precedenti > 0) {
            $abbandono_percentuale = round((($operatori_precedenti - $operatori_correnti) / $operatori_precedenti) * 100, 2);
        }

        // Aggiorna i totali
        $totale_operatori += $operatori_correnti;
        $totale_assunti_corrente += $assunti_correnti;
        $totale_abbandoni += $abbandoni;
        $totale_operatori_precedente += $operatori_precedenti;
        $totale_assunti_precedente += $assunti_precedenti;
        $somma_abbandoni += $abbandono_percentuale;
        $conta_sedi++;
        $totale_non_loggati += $non_loggati;
        
        $html .= "<tr>
            <td>$sede</td>
            <td>$operatori_correnti</td>
            <td>$assunti_correnti</td>
            <td>$abbandoni</td>
            <td>$non_loggati</td>
            <td>$abbandono_percentuale%</td>
            <td>$operatori_precedenti</td>
            <td>$assunti_precedenti</td>
        </tr>";
    }

    // Calcola la media degli abbandoni percentuali
    $media_abbandoni = $conta_sedi > 0 ? round($somma_abbandoni / $conta_sedi, 2) : 0;

    // Aggiungi la riga dei totali
    $html .= "<tr style='background-color: orange; font-weight: bold;'>
        <td><strong>TOTALE</strong></td>
        <td><strong>$totale_operatori</strong></td>
        <td><strong>$totale_assunti_corrente</strong></td>
        <td><strong>$totale_abbandoni</strong></td>
        <td><strong>$totale_non_loggati</strong></td>
        <td><strong>$media_abbandoni%</strong></td>
        <td><strong>$totale_operatori_precedente</strong></td>
        <td><strong>$totale_assunti_precedente</strong></td>
    </tr>";

    $html .= "</tbody></table>";
    echo $html;

} catch (Exception $e) {
    echo "<div class='error'>Errore: " . htmlspecialchars($e->getMessage()) . "</div>";
}