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

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));
$hmtl = 0;
$resatotIn =0;
$resapleniIn =0;

$idMandato = 0;
$mandato = 0;

$mandato = ["Union", "Vodafone", "Vivigas Energia", "Plenitude", "Enel", "Iren" ];

// Converti le date al primo giorno del mese
$meseMinore = date('Y-m-01', strtotime($dataMinore));
$meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

// Query per ottenere gli obiettivi PDP
$queryObiettivi = "SELECT 
    o.`gruppoTL` AS usergroup,
    o.`tipo` AS type,
    SUM(o.`plenitudePdp`) AS 'Plenitude',
    SUM(o.`enelPdp`) AS 'Enel',
    SUM(o.`vivigasPdp`) AS 'Vivi',
    SUM(o.`irenPdp`) AS 'Iren'
FROM `obbiettivoTL` o
WHERE o.`mese` BETWEEN '$meseMinore' AND '$meseMaggiore'
GROUP BY o.`gruppoTL`, o.`tipo`
ORDER BY o.`gruppoTL` ASC, o.`tipo` ASC";

$risultatoObiettivi = $conn19->query($queryObiettivi);
$obiettivi = array();

while ($rigaObiettivo = $risultatoObiettivi->fetch_assoc()) {
    $gruppo = $rigaObiettivo['usergroup'];
    $tipo = $rigaObiettivo['type'];
    
    if (!isset($obiettivi[$gruppo])) {
        $obiettivi[$gruppo] = array();
    }
    
    $obiettivi[$gruppo][$tipo] = array(
        'Plenitude' => $rigaObiettivo['Plenitude'],
        'Enel' => $rigaObiettivo['Enel'],
        'Vivi' => $rigaObiettivo['Vivi'],
        'Iren' => $rigaObiettivo['Iren']
    );
}

// Poi procediamo con la query originale come prima
$queryGroupMandato = "SELECT
    userGroup,
    nomeCompleto,
    SUM(CASE WHEN mandato <> 'Lead Inbound' THEN numero ELSE 0 END) / 3600 AS oreNoninb,
    SUM(CASE WHEN mandato = 'Lead Inbound' THEN numero ELSE 0 END) / 3600 AS oreinb
FROM
    stringheTotale
WHERE
    giorno >= '$dataMinore' AND giorno <= '$dataMaggiore'
    AND livello <= 6
    AND LENGTH(nomeCompleto) > 4
    AND mandato NOT IN ('BO', 'Bo')
GROUP BY
    userGroup
ORDER BY
    userGroup, nomeCompleto";

$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);



foreach ($mandato as $idMandato) {
    if ($idMandato === "Union" || $idMandato === "Vodafone" || $idMandato === "Bo") {
        continue;
    }

    $mandatoForQuery = $idMandato;
    if ($idMandato === "Vivigas Energia") {
        $mandatoForQuery = "Vivigas";
    }

    // Converti SOLO le date di ricerca al primo giorno del mese
    $meseMinore = date('Y-m-01', strtotime($dataMinore));
    $meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

    $queryvaloremedio = "SELECT mandato, mese, media 
                         FROM `mediaPraticaMese` 
                         WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                         AND mandato = ? 
                         ORDER BY `id` DESC";
    
    $stmt = $conn19->prepare($queryvaloremedio);
    $stmt->bind_param("s", $mandatoForQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $valoriMedi[$idMandato] = $row['media'];
        error_log("Trovato valore medio per $idMandato ($mandatoForQuery): " . $row['media']);
    } else {
        error_log("Nessun valore medio trovato per $idMandato (cercato come $mandatoForQuery)");
    }
    $stmt->close();
}
// Inizia la creazione della tabella
$html = "<table class='blueTable' id='table-1'>";

// Aggiungi l'intestazione (prima riga)
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th colspan='11' style='border-left: 5px double'>Tl UserGroup</th>";
$html .= "<th colspan='6' style='border-left: 5px double'>Plenitude</th>";
$html .= "<th colspan='6' style='border-left: 5px double'>Enel</th>";
$html .= "<th colspan='6' style='border-left: 5px double'>Vivigas</th>";
$html .= "<th colspan='6' style='border-left: 5px double'>Iren</th>";
$html .= "<th colspan='3' style='border-left: 5px double'>Polizze</th>";
$html .= "</tr>";

// Aggiungi l'intestazione (seconda riga)
$html .= "<tr>";
$html .= "<th>TL Group</th>";
$html .= "<th>Tipo</th>";
$html .= "<th>Ore</th>";
$html .= "<th>Lead</th>";
$html .= "<th>PDP</th>";
$html .= "<th>Resa</th>";
$html .= "<th>Convertito</th>";
$html .= "<th>R/H</th>";
$html .= "<th>Obj Mensile</th>";
$html .= "<th>Passo Giorno</th>";
$html .= "<th>Differenza Obj</th>";

$html .= "<th>Pdp</th>";
$html .= "<th>Resa</th>";
$html .= "<th>Conv</th>";
$html .= "<th>Obj Mensile</th>";
$html .= "<th>Passo Giorno</th>";
$html .= "<th>Differenza Obj</th>";

$html .= "<th>Pdp</th>";
$html .= "<th>Resa</th>";
$html .= "<th>Conv</th>";
$html .= "<th>Obj Mensile</th>";
$html .= "<th>Passo Giorno</th>";
$html .= "<th>Differenza Obj</th>";

$html .= "<th>Pdp</th>";
$html .= "<th>Resa</th>";
$html .= "<th>Conv</th>";
$html .= "<th>Obj Mensile</th>";
$html .= "<th>Passo Giorno</th>";
$html .= "<th>Differenza Obj</th>";

$html .= "<th>Pdp</th>";
$html .= "<th>Resa</th>";
$html .= "<th>Conv</th>";
$html .= "<th>Obj Mensile</th>";
$html .= "<th>Passo Giorno</th>";
$html .= "<th>Differenza Obj</th>";

$html .= "<th>Pdp</th>";
$html .= "<th>Resa</th>";
$html .= "<th>Conv</th>";
$html .= "</tr>";
$html .= "</thead>";

// Aggiungi il corpo della tabella
$html .= "<tbody>";

function calcolaGiorniLavorativi($dataInizio, $dataFine, $giorniFestiviPerMese = [], $dateDaEscludere = []) {
    $giorniLavorativiPrevisti = 0;
    $giorniLavoratiEffettivi = 0;

    // Converti le date in oggetti DateTime
    $dataInizio = new DateTime($dataInizio);
    $dataFine = new DateTime($dataFine);

    // Determina il mese di riferimento (mese di $dataInizio o $dataFine)
    $meseRiferimento = $dataInizio->format('m');
    $annoRiferimento = $dataInizio->format('Y');

    // Se il range di date copre due mesi, usa il mese di $dataFine
    if ($dataInizio->format('m') !== $dataFine->format('m')) {
        $meseRiferimento = $dataFine->format('m');
        $annoRiferimento = $dataFine->format('Y');
    }

    // Calcola i giorni lavorabili previsti per il mese di riferimento
    $primoGiornoMeseRiferimento = new DateTime("$annoRiferimento-$meseRiferimento-01");
    $ultimoGiornoMeseRiferimento = new DateTime($primoGiornoMeseRiferimento->format('Y-m-t'));

    $interval = new DateInterval('P1D');
    $period = new DatePeriod($primoGiornoMeseRiferimento, $interval, $ultimoGiornoMeseRiferimento->modify('+1 day'));

    // Calcola giorni festivi per il mese di riferimento
    $giorniFestiviMese = 0;
    $chiaveFestivi = "$annoRiferimento-$meseRiferimento";
    if (isset($giorniFestiviPerMese[$chiaveFestivi])) {
        $giorniFestiviMese = $giorniFestiviPerMese[$chiaveFestivi];
    }

    // Converti le date da escludere in formato stringa per confronto
    $dateEsclusioneFormattate = array_map(function($data) {
        return (new DateTime($data))->format('Y-m-d');
    }, $dateDaEscludere);

    $giorniLavorativiPrevistiSenzaFestivi = 0;
    foreach ($period as $giorno) {
        $giornoFormattato = $giorno->format('Y-m-d');
        $giornoSettimana = $giorno->format('N'); // 1 (Lunedì) - 7 (Domenica)

        // Se il giorno è tra quelli da escludere, salta
        if (in_array($giornoFormattato, $dateEsclusioneFormattate)) {
            continue;
        }

        // Calcolo dei giorni lavorabili previsti
        if ($giornoSettimana >= 1 && $giornoSettimana <= 5) {
            $giorniLavorativiPrevistiSenzaFestivi += 1;
        } elseif ($giornoSettimana == 6) {
            $giorniLavorativiPrevistiSenzaFestivi += 0.5;
        }
    }
    
    // Sottrai i giorni festivi dal totale
    $giorniLavorativiPrevisti = $giorniLavorativiPrevistiSenzaFestivi - $giorniFestiviMese;

    // Calcolo dei giorni lavorati effettivi
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($dataInizio, $interval, $dataFine->modify('+1 day'));

    foreach ($period as $giorno) {
        $giornoFormattato = $giorno->format('Y-m-d');
        $giornoSettimana = $giorno->format('N');

        // Se il giorno è tra quelli da escludere, salta
        if (in_array($giornoFormattato, $dateEsclusioneFormattate)) {
            continue;
        }

        // Calcolo dei giorni lavorati effettivi
        if ($giornoSettimana >= 1 && $giornoSettimana <= 5) {
            $giorniLavoratiEffettivi += 1;
        } elseif ($giornoSettimana == 6) {
            $giorniLavoratiEffettivi += 0.5;
        }
    }

    // Calcola i giorni rimanenti
    $giornirimanenti = $giorniLavorativiPrevisti - $giorniLavoratiEffettivi;

    return [
        'giorni_lavorabili_previsti' => $giorniLavorativiPrevisti,
        'giorni_lavorati_effettivi' => $giorniLavoratiEffettivi,
        'giorni_rimanenti' => $giornirimanenti
    ];
}

// Esempio di utilizzo
$giorniFestiviPerMese = [
    '2025-04' => 0, // Aprile 2025 ha 3 giorni festivi
];

$dateDaEscludere = [
       '2025-04-21',
    '2025-04-25',
    '2025-04-26',
    '2025-05-01',
    '2025-06-02',
    '2025-07-05',
    '2025-07-12',
    '2025-07-19',
    '2025-07-26',
    '2025-08-02',
    '2025-08-09',
    '2025-08-16',
    '2025-08-23',
    '2025-08-30',
    '2025-08-15',
    '2025-11-01',
    '2025-12-08',
    '2025-12-25',
    '2025-12-26'
];
//$giornirimanenti = $result['giorni_rimanenti'];
$result = calcolaGiorniLavorativi($dataMinore, $dataMaggiore, $giorniFestiviPerMese, $dateDaEscludere);


$giornirimanenti = $result['giorni_rimanenti'];
$giorniLavorativiPrevisti = $result['giorni_lavorabili_previsti'];
$giorniLavoratiEffettivi = $result[ 'giorni_lavorati_effettivi'];


//echo $giornirimanenti;
// Inizializza le variabili per i totali
$totaleOreIn = 0;
$totaleOreOut = 0;
$totaleLeadRiferimentoIn = 0;
$totalePezziIn = 0;
$totalePezziOut = 0;
$totalePezziPlenitudeIn = 0;
$totalePezziPlenitudeOut = 0;
$totalePezziEnelIn = 0;
$totalePezziEnelOut = 0;
$totalePezziVivigasIn = 0;
$totalePezziVivigasOut = 0;
$totalePezziIrenIn = 0;
$totalePezziIrenOut = 0;
$totalePezziPolizzeIn = 0;
$totalePezziPolizzeOut = 0;
$passogiornomandatoVo = 0;
$totalePezziirenOut = 0;
$totobjCTCTl = 0;
$totobjOUTTl = 0;
$objOUT = 0;
$objCTC = 0;
$obiettivoCTC = 0;
$obiettivoOUT = 0;
$totobjPCTC = 0;
$totobjECTC = 0;
$totobjVCTC = 0;
$totobjICTC = 0;
$totoobjPOUT = 0;
$totoobjEOUT = 0;
$totoobjVOUT = 0;
$totoobjIOUT = 0;
$totalePezziIrenIn = 0;
$differenzaOUTpg = 0;
$differenzaOUT = 0;
$differenzaCTC = 0;

$pezzoOkirenIn  =0;
    
// Inizializza variabili per somma obiettivi
$sommaObiettiviCTC = array('Plenitude' => 0, 'Enel' => 0, 'Vivi' => 0);
$sommaObiettiviOUT = array('Plenitude' => 0, 'Enel' => 0, 'Vivi' => 0);

while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {
    $usergoup = $rigaMandato[0];
    $user = $rigaMandato[1];
    $oreOut = round($rigaMandato[2], 2);
    $oreIn = round($rigaMandato[3], 2);

    // Query per Plenitude (IN)
    $queryCrmPlenitudeIn = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                          FROM plenitude
                          INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id
                          WHERE plenitude.DATA <= '$dataMaggiore' AND data >= '$dataMinore' AND aggiuntaPlenitude.tipoCampagna = 'Lead'
                          AND plenitude.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                          AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' AND comodity <> 'Polizza' GROUP BY nomeCompleto)";
    $risultatoCrmPlenitudeIn = $conn19->query($queryCrmPlenitudeIn);
    $rigaCRMPlenitudeIn = $risultatoCrmPlenitudeIn->fetch_array();
    $pezzoOkpleniIn = round($rigaCRMPlenitudeIn[1], 0);

    // Query per Plenitude (OUT)
    $queryCrmPlenitudeOut = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                          FROM plenitude
                          INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id
                          WHERE plenitude.DATA <= '$dataMaggiore' AND data >= '$dataMinore' AND aggiuntaPlenitude.tipoCampagna <> 'Lead'
                          AND plenitude.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                          AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' AND comodity <> 'Polizza' GROUP BY nomeCompleto)";
    $risultatoCrmPlenitudeOut = $conn19->query($queryCrmPlenitudeOut);
    $rigaCRMPlenitudeOut = $risultatoCrmPlenitudeOut->fetch_array();
    $pezzoOkpleniOut = round($rigaCRMPlenitudeOut[1], 0);

    // Query per Vivigas (IN)
    $queryCrmVivigasIn = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                        FROM vivigas
                        INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
                        WHERE vivigas.DATA <= '$dataMaggiore' AND data >= '$dataMinore' AND aggiuntaVivigas.tipoCampagna = 'Lead'
                        AND vivigas.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                        AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' GROUP BY nomeCompleto)";
    $risultatoCrmVivigasIn = $conn19->query($queryCrmVivigasIn);
    $rigaCRMVivigasIn = $risultatoCrmVivigasIn->fetch_array();
    $pezzoOkviviIn = round($rigaCRMVivigasIn[1], 0);

    // Query per Vivigas (OUT)
    $queryCrmVivigasOut = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                        FROM vivigas
                        INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
                        WHERE vivigas.DATA <= '$dataMaggiore' AND data >= '$dataMinore' AND aggiuntaVivigas.tipoCampagna <> 'Lead'
                        AND vivigas.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                        AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' GROUP BY nomeCompleto)";
    $risultatoCrmVivigasOut = $conn19->query($queryCrmVivigasOut);
    $rigaCRMVivigasOut = $risultatoCrmVivigasOut->fetch_array();
    $pezzoOkviviOut = round($rigaCRMVivigasOut[1], 0);

    // Query per Enel (IN)
    $queryCrmEnelIn = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                     FROM enel
                     INNER JOIN aggiuntaEnel ON enel.id = aggiuntaEnel.id
                     WHERE enel.DATA <= '$dataMaggiore' AND data >= '$dataMinore' AND aggiuntaEnel.tipoCampagna = 'Lead'
                     AND enel.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                     AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' GROUP BY nomeCompleto)";
    $risultatoCrmEnelIn = $conn19->query($queryCrmEnelIn);
    $rigaCRMEnelIn = $risultatoCrmEnelIn->fetch_array();
    $pezzoOkenelIn = round($rigaCRMEnelIn[1], 0);

    // Query per Enel (OUT)
    $queryCrmEnelOut = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                     FROM enel
                     INNER JOIN aggiuntaEnel ON enel.id = aggiuntaEnel.id
                     WHERE enel.DATA <= '$dataMaggiore' AND data >= '$dataMinore' AND aggiuntaEnel.tipoCampagna <> 'Lead'
                     AND enel.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                     AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' GROUP BY nomeCompleto)";
    $risultatoCrmEnelOut = $conn19->query($queryCrmEnelOut);
    $rigaCRMEnelOut = $risultatoCrmEnelOut->fetch_array();
    $pezzoOkenelOut = round($rigaCRMEnelOut[1], 0);

    
    
        // Query per iren (IN)
    $queryCrmIrenIn = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                     FROM iren
                     INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
                     WHERE iren.DATA <= '$dataMaggiore' AND data >= '$dataMinore' AND aggiuntaIren.tipoCampagna = 'Lead'
                     AND iren.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                     AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' GROUP BY nomeCompleto)";
    $risultatoCrmIrenIn = $conn19->query($queryCrmIrenIn);
    $rigaCRMIrenIn = $risultatoCrmIrenIn->fetch_array();
    $pezzoOkIrenIn = round($rigaCRMIrenIn[1], 0);

    // Query per iren (OUT)
    $queryCrmIrenOut = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                     FROM iren
                     INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
                     WHERE iren.DATA <= '$dataMaggiore' AND data >= '$dataMinore' AND aggiuntaIren.tipoCampagna <> 'Lead'
                     AND iren.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                     AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' GROUP BY nomeCompleto)";
    $risultatoCrmIrenOut = $conn19->query($queryCrmIrenOut);
    $rigaCRMIrenOut = $risultatoCrmIrenOut->fetch_array();
    $pezzoOkirenOut = round($rigaCRMIrenOut[1], 0);
    
    
    // Query per Polizze (IN)
    $queryCrmPolizzeIn = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                        FROM plenitude
                        INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id
                        WHERE plenitude.DATA <= '$dataMaggiore' AND data >= '$dataMinore'
                        AND plenitude.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                        AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' AND comodity = 'Polizza' GROUP BY nomeCompleto)";
    $risultatoCrmPolizzeIn = $conn19->query($queryCrmPolizzeIn);
    $rigaCRMPolizzeIn = $risultatoCrmPolizzeIn->fetch_array();
    $pezzoOkpleniPlzIn = round($rigaCRMPolizzeIn[1], 0);

    // Query per Polizze (OUT)
    $queryCrmPolizzeOut = "SELECT SUM(pezzoLordo) AS totalePezzi, SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) AS pezziOK
                        FROM plenitude
                        INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id
                        WHERE plenitude.DATA <= '$dataMaggiore' AND data >= '$dataMinore'
                        AND plenitude.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                        AND creatoDA IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' AND comodity = 'Polizza' GROUP BY nomeCompleto)";
    $risultatoCrmPolizzeOut = $conn19->query($queryCrmPolizzeOut);
    $rigaCRMPolizzeOut = $risultatoCrmPolizzeOut->fetch_array();
    $pezzoOkpleniPlzOut = round($rigaCRMPolizzeOut[1], 0);

    // Query per Lead Riferimento (IN)
    $queryRiferimentoIn = "SELECT count(idSponsorizzata) AS lead
                     FROM gestioneLead
                     WHERE gestioneLead.dataImport <= '$dataMaggiore' AND gestioneLead.dataImport >= '$dataMinore'
                     AND gestioneLead.idSponsorizzata LIKE 'G%'
                     AND gestitoDa IN (SELECT nomeCompleto FROM stringheTotale WHERE giorno BETWEEN '$dataMinore' AND '$dataMaggiore' AND userGroup = '$usergoup' AND mandato = 'Lead Inbound' and duplicato <> 'Si' GROUP BY nomeCompleto)";
//    echo $queryRiferimentoIn;
    
    

    
    $risultatoRiferimentoIn = $conn19->query($queryRiferimentoIn);
    $rigaRiferimentoIn = $risultatoRiferimentoIn->fetch_array();
    $leadRiferimentoIn = $rigaRiferimentoIn ? $rigaRiferimentoIn[0] : 0;

    // Calcolo totale pezzi (IN)
    $totalepezziIn = $pezzoOkenelIn + $pezzoOkviviIn + $pezzoOkpleniIn;

    // Calcolo totale pezzi (OUT)
    $totalepezziOut = $pezzoOkenelOut + $pezzoOkviviOut + $pezzoOkpleniOut;

    // Calcolo resa totale (IN)
    $resatotIn = ($oreIn != 0) ? ($totalepezziIn / $oreIn) : 0;
    $resatotIn = number_format($resatotIn, 2, '.', '');

    // Calcolo resa totale (OUT)
    $resatotOut = ($oreOut != 0) ? ($totalepezziOut / $oreOut) : 0;
    $resatotOut = number_format($resatotOut, 2, '.', '');

    // Calcolo resa Plenitude (IN)
    $resapleniIn = ($oreIn != 0 && $pezzoOkpleniIn != 0) ? ($pezzoOkpleniIn / $oreIn) : 0;
    $resapleniIn = number_format($resapleniIn, 2, '.', '');

    // Calcolo resa Plenitude (OUT)
    $resapleniOut = ($oreOut != 0 && $pezzoOkpleniOut != 0) ? ($pezzoOkpleniOut / $oreOut) : 0;
    $resapleniOut = number_format($resapleniOut, 2, '.', '');

    // Calcolo resa Enel (IN)
    $resaenelIn = ($oreIn != 0 && $pezzoOkenelIn != 0) ? ($pezzoOkenelIn / $oreIn) : 0;
    $resaenelIn = number_format($resaenelIn, 2, '.', '');

    // Calcolo resa Enel (OUT)
    $resaenelOut = ($oreOut != 0 && $pezzoOkenelOut != 0) ? ($pezzoOkenelOut / $oreOut) : 0;
    $resaenelOut = number_format($resaenelOut, 2, '.', '');

    // Calcolo resa Vivigas (IN)
    $resaviviIn = ($oreIn != 0 && $pezzoOkviviIn != 0) ? ($pezzoOkviviIn / $oreIn) : 0;
    $resaviviIn = number_format($resaviviIn, 2, '.', '');

    // Calcolo resa Vivigas (OUT)
    $resaviviOut = ($oreOut != 0 && $pezzoOkviviOut != 0) ? ($pezzoOkviviOut / $oreOut) : 0;
    $resaviviOut = number_format($resaviviOut, 2, '.', '');
    
        // Calcolo resa Vivigas (IN)
    $resairenIn = ($oreIn != 0 && $pezzoOkirenIn != 0) ? ($pezzoOkirenIn / $oreIn) : 0;
    $resairenIn = number_format($resairenIn, 2, '.', '');
    
    // Calcolo resa Vivigas (OUT)
    $resairenOut = ($oreOut != 0 && $pezzoOkirenOut != 0) ? ($pezzoOkirenOut / $oreOut) : 0;
    $resairenOut = number_format($resairenOut, 2, '.', '');

    // Calcolo resa Polizze (IN)
    $resapleniPlzIn = ($oreIn != 0 && $pezzoOkpleniPlzIn != 0) ? ($pezzoOkpleniPlzIn / $oreIn) : 0;
    $resapleniPlzIn = number_format($resapleniPlzIn, 2, '.', '');

    // Calcolo resa Polizze (OUT)
     $resapleniPlzOut = ($oreOut != 0 && $pezzoOkpleniPlzOut != 0) ? ($oreOut / $pezzoOkpleniPlzIn) : 0;
     $resapleniPlzOut = number_format($resapleniPlzOut, 2, '.', '');

        
        // CONVERSIONE PEZZI / LEAD	
    // Calcolo CONVERSIONE totale (IN) in percentuale
    $convtotIn = ($leadRiferimentoIn != 0) ? (($totalepezziIn / $leadRiferimentoIn) * 100) : 0;
    $convtotIn = number_format($convtotIn, 2, '.', '');

    // Calcolo CONVERSIONE Plenitude (IN) in percentuale
    $convpleniIn = ($leadRiferimentoIn != 0 && $pezzoOkpleniIn != 0) ? (($pezzoOkpleniIn / $leadRiferimentoIn) * 100) : 0;
    $ConvpleniIn = number_format($convpleniIn, 2, '.', '');

    // Calcolo CONVERSIONE Enel (IN) in percentuale
    $convenelIn = ($leadRiferimentoIn != 0 && $pezzoOkenelIn != 0) ? (($pezzoOkenelIn / $leadRiferimentoIn) * 100) : 0;
    $convaenelIn = number_format($convenelIn, 2, '.', '');

    // Calcolo CONVERSIONE Vivigas (IN) in percentuale
    $conviviIn = ($leadRiferimentoIn != 0 && $pezzoOkviviIn != 0) ? (($pezzoOkviviIn / $leadRiferimentoIn) * 100) : 0;
    $conviviIn = number_format($conviviIn, 2, '.', '');
    // Calcolo CONVERSIONE Vivigas (IN) in percentuale
    $conirenIn = ($leadRiferimentoIn != 0 && $pezzoOkirenIn != 0) ? (($pezzoOkirenIn / $leadRiferimentoIn) * 100) : 0;
    $conirenIn = number_format($conirenIn, 2, '.', '');
    // Calcolo CONVERSIONE Polizze (IN) in percentuale
    $convpleniPlzIn = ($pezzoOkpleniIn != 0 && $pezzoOkpleniPlzIn != 0) ? (($pezzoOkpleniPlzIn / $pezzoOkpleniIn) * 100) : 0;
    $convpleniPlzIn = number_format($convpleniPlzIn, 2, '.', '');

    // Aggiorna i totali
    $totaleOreIn += $oreIn;
    $totaleOreOut += $oreOut;
    $totaleLeadRiferimentoIn += $leadRiferimentoIn;
    $totalePezziIn += $totalepezziIn;
    $totalePezziOut += $totalepezziOut;
    $totalePezziPlenitudeIn += $pezzoOkpleniIn;
    $totalePezziPlenitudeOut += $pezzoOkpleniOut;
    $totalePezziEnelIn += $pezzoOkenelIn;
    $totalePezziEnelOut += $pezzoOkenelOut;
    $totalePezziVivigasIn += $pezzoOkviviIn;
    $totalePezziVivigasOut += $pezzoOkviviOut;
    $totalePezziIrenIn += $pezzoOkirenIn;
    $totalePezziIrenOut += $pezzoOkirenOut;
    $totalePezziPolizzeIn += $pezzoOkpleniPlzIn;
    $totalePezziPolizzeOut += $pezzoOkpleniPlzOut;
    
    $totobjCTCTl += $objCTC;

    $totobjOUTTl += $objOUT;
    
    
    
    

        // Recuperiamo gli obiettivi per questo gruppo
$defaultArray = ['Plenitude' => 0, 'Enel' => 0, 'Vivi' => 0 , 'Iren' => 0];

// Se $obiettivi[$usergoup]['CTC'] esiste ma non è un array, usa $defaultArray
$obiettivoCTC = (isset($obiettivi[$usergoup]['CTC']) && is_array($obiettivi[$usergoup]['CTC'])) 
    ? $obiettivi[$usergoup]['CTC'] 
    : $defaultArray;

$obiettivoOUT = (isset($obiettivi[$usergoup]['OUT']) && is_array($obiettivi[$usergoup]['OUT'])) 
    ? $obiettivi[$usergoup]['OUT'] 
    : $defaultArray;


$totobjPCTC += $obiettivoCTC['Plenitude'] ?? 0;
$totobjECTC += $obiettivoCTC['Enel'] ?? 0;
$totobjVCTC += $obiettivoCTC['Vivi'] ?? 0;
$totobjICTC += $obiettivoCTC['Iren'] ?? 0;
$totoobjPOUT += $obiettivoOUT['Plenitude'] ?? 0;
$totoobjEOUT += $obiettivoOUT['Enel'] ?? 0;
$totoobjVOUT += $obiettivoOUT['Vivi'] ?? 0;
$totoobjIOUT += $obiettivoOUT['Iren'] ?? 0;
        


// Calcolo differenze con segno invertito
$differenzaCTCpg = [
    'Plenitude' => (($obiettivoCTC['Plenitude'] ?? 0) - ($pezzoOkpleniIn ?? 0)) * -1,
    'Enel' => (($obiettivoCTC['Enel'] ?? 0) - ($pezzoOkenelIn ?? 0)) * -1,
    'Vivi' => (($obiettivoCTC['Vivi'] ?? 0) - ($pezzoOkviviIn ?? 0)) * -1,
    'Iren' => (($obiettivoCTC['Iren'] ?? 0) - ($pezzoOkviviIn ?? 0)) * -1
];

$differenzaOUTpg = [
    'Plenitude' => (($obiettivoOUT['Plenitude'] ?? 0) - ($pezzoOkpleniOut ?? 0)) * -1,
    'Enel' => (($obiettivoOUT['Enel'] ?? 0) - ($pezzoOkenelOut ?? 0)) * -1,
    'Vivi' => (($obiettivoOUT['Vivi'] ?? 0) - ($pezzoOkviviOut ?? 0)) * -1,
    'Iren' => (($obiettivoOUT['Iren'] ?? 0) - ($pezzoOkviviOut ?? 0)) * -1
];

// Calcolo passi giorno (versione ottimizzata)
$calculateDailyStep = function($diff, $days) {
    return $days > 0 ? abs(round($diff / $days)) : 0;
};

$passogiornomandatoP = $calculateDailyStep($differenzaCTCpg['Plenitude'], $giornirimanenti);
$passogiornomandatoE = $calculateDailyStep($differenzaCTCpg['Enel'], $giornirimanenti);
$passogiornomandatoV = $calculateDailyStep($differenzaCTCpg['Vivi'], $giornirimanenti);
$passogiornomandatoI = $calculateDailyStep($differenzaCTCpg['Iren'], $giornirimanenti);

$passogiornomandatoPo = $calculateDailyStep($differenzaOUTpg['Plenitude'], $giornirimanenti);
$passogiornomandatoEo = $calculateDailyStep($differenzaOUTpg['Enel'], $giornirimanenti);
$passogiornomandatoVo = $calculateDailyStep($differenzaOUTpg['Vivi'], $giornirimanenti);
$passogiornomandatoIo = $calculateDailyStep($differenzaOUTpg['Iren'], $giornirimanenti);

// Calcolo totali
$differenzaCTCTot = array_sum($differenzaCTCpg);
$differenzaOUTTot = array_sum($differenzaOUTpg);

$passogiornoCTC = $calculateDailyStep($differenzaCTCTot, $giornirimanenti);
$passogiornoOUT = $calculateDailyStep($differenzaOUTTot, $giornirimanenti);


// Calcolo del passo giorno TOTALE con valore assoluto e arrotondamento per difetto
$passogiornoCTC = $giornirimanenti > 0 ? abs(intval($differenzaCTCTot / $giornirimanenti)) : 0;
$passogiornoOUT = $giornirimanenti > 0 ? abs(intval($differenzaOUTTot / $giornirimanenti)) : 0;

// Se vuoi i valori assoluti (sempre positivi) per il passo giorno:
$passogiornoCTC_abs = abs($passogiornoCTC);
$passogiornoOUT_abs = abs($passogiornoOUT);


// Calcolo differenze totali
// Aggiorna la somma degli obiettivi
      // Calcola fatturato
            $mediaMandato = $valoriMedi[$idMandato] ?? 0;
            
           
            $fatturatoPIn = $mediaMandato * $pezzoOkpleniIn;
            $fatturatoEIn = $mediaMandato * $pezzoOkenelIn;
            $fatturatoVIn = $mediaMandato * $pezzoOkviviIn;
            
            $fatturatoIn = $fatturatoPIn + $fatturatoEIn + $fatturatoVIn;
            
            $fatturatoPout = $mediaMandato * $pezzoOkpleniOut;
            $fatturatoEout = $mediaMandato * $pezzoOkenelOut;
            $fatturatoVout = $mediaMandato * $pezzoOkviviOut;
            
            $fatturatoOut = $fatturatoPIn + $fatturatoEIn + $fatturatoVIn;
            
            $ricavoOrarioIn = $oreIn != 0 ? round($fatturatoIn / $oreIn, 0) : 0;
            
            $fatturatoOut = $mediaMandato * $totalepezziOut;
            
            $ricavoOrarioOut = $oreOut != 0 ? round($fatturatoOut / $oreOut, 0) : 0;

$html .= "<tr>";
$html .= "<td>$usergoup</td>"; // TL Group
$html .= "<td style='background-color: #FFFACD; color: black;'>CTC</td>"; // Giallo pastello
$html .= "<td style='border-left: 5px double #D0E4F5'>$oreIn</td>"; // Ore IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$leadRiferimentoIn</td>"; // Lead IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$totalepezziIn</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resatotIn</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$convtotIn%</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>" . round($ricavoOrarioIn) . " €</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$objCTC</td>"; // obj totale tutte i mandati
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoCTC</td>"; //passogiorno totale 
$html .= "<td style='border-left: 5px double #D0E4F5'>$differenzaCTCTot</td>"; // differenza tra prodotto ed obiettivo


$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkpleniIn</td>"; // PDP Plenitude IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resapleniIn</td>"; // Resa/Conv Plenitude IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$ConvpleniIn%</td>"; // Resa/Conv Plenitude IN
$html .= "<td style='border-left: 5px double #D0E4F5'>".$obiettivoCTC['Plenitude']."</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornomandatoP</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>".$differenzaCTCpg['Plenitude']."</td>"; // Differenza senza colore

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkenelIn</td>"; // PDP Enel IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaenelIn</td>"; // Resa/Conv Enel IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$convaenelIn%</td>"; // Resa/Conv Enel IN
$html .= "<td style='border-left: 5px double #D0E4F5'>".$obiettivoCTC['Enel']."</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornomandatoE</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>".$differenzaCTCpg['Enel']."</td>"; // Differenza senza colore

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkviviIn</td>"; // PDP Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaviviIn</td>"; // Resa/Conv Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$conviviIn%</td>"; // Resa/Conv Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>".$obiettivoCTC['Vivi']."</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornomandatoV</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>".$differenzaCTCpg['Vivi']."</td>"; // Differenza senza colore

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkirenIn</td>"; // PDP Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resairenIn</td>"; // Resa/Conv Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$conirenIn%</td>"; // Resa/Conv Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>".$obiettivoCTC['Iren']."</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornomandatoV</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>".$differenzaCTCpg['Iren']."</td>"; // Differenza senza colore

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkpleniPlzIn</td>"; // PDP Polizze IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resapleniPlzIn</td>"; // Resa/Conv Polizze IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$convpleniPlzIn%</td>"; // Resa/Conv Polizze IN
$html .= "</tr>";

// Aggiungi una riga per i dati OUT
$html .= "<tr>";
$html .= "<td>$usergoup</td>"; // TL Group
$html .= "<td>OUT</td>"; // Tipo
$html .= "<td style='border-left: 5px double #D0E4F5'>$oreOut</td>"; // Ore OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Lead OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$totalepezziOut</td>"; // PDP OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resatotOut</td>"; // Resa/Convertito OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>" . round($ricavoOrarioOut) . " €</td>"; // Resa/Convertito OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$objOUT</td>";
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoOUT</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>$differenzaOUTTot</td>";

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkpleniOut</td>"; // PDP Plenitude OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resapleniOut</td>"; // Resa/Conv Plenitude OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Plenitude OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>".$obiettivoOUT['Plenitude']."</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornomandatoPo</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>".$differenzaOUTpg['Plenitude']."</td>"; // Differenza senza colore

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkenelOut</td>"; // PDP Enel OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaenelOut</td>"; // Resa/Conv Enel OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Enel OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>".$obiettivoOUT['Enel']."</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornomandatoEo</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>".$differenzaOUTpg['Enel']."</td>"; // Differenza senza colore

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkviviOut</td>"; // PDP Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaviviOut</td>"; // Resa/Conv Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>".$obiettivoOUT['Vivi']."</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornomandatoIo</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>".$differenzaOUTpg['Vivi']."</td>"; // Differenza senza colore

$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkirenOut</td>"; // PDP Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resairenOut</td>"; // Resa/Conv Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>".$obiettivoOUT['Iren']."</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornomandatoIo</td>"; 
$html .= "<td style='border-left: 5px double #D0E4F5'>".$differenzaOUTpg['Iren']."</td>"; // Differenza senza colore


$html .= "<td style='border-left: 5px double #D0E4F5'>$pezzoOkpleniPlzOut</td>"; // PDP Polizze OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resapleniPlzOut</td>"; // Resa/Conv Polizze OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Polizze OUT
$html .= "</tr>";



}

// Calcola le rese totali
$resaTotaleIn = ($totaleOreIn != 0) ? ($totalePezziIn / $totaleOreIn) : 0;
$resaTotaleIn = number_format($resaTotaleIn, 2, '.', '');

$resaTotaleOut = ($totaleOreOut != 0) ? ($totalePezziOut / $totaleOreOut) : 0;
$resaTotaleOut = number_format($resaTotaleOut, 2, '.', '');

$resaPlenitudeIn = ($totaleOreIn != 0) ? ($totalePezziPlenitudeIn / $totaleOreIn) : 0;
$resaPlenitudeIn = number_format($resaPlenitudeIn, 2, '.', '');

$resaPlenitudeOut = ($totaleOreOut != 0) ? ($totalePezziPlenitudeOut / $totaleOreOut) : 0;
$resaPlenitudeOut = number_format($resaPlenitudeOut, 2, '.', '');

$resaEnelIn = ($totaleOreIn != 0) ? ($totalePezziEnelIn / $totaleOreIn) : 0;
$resaEnelIn = number_format($resaEnelIn, 2, '.', '');

$resaEnelOut = ($totaleOreOut != 0) ? ($totalePezziEnelOut / $totaleOreOut) : 0;
$resaEnelOut = number_format($resaEnelOut, 2, '.', '');

$resaVivigasIn = ($totaleOreIn != 0) ? ($totalePezziVivigasIn / $totaleOreIn) : 0;
$resaVivigasIn = number_format($resaVivigasIn, 2, '.', '');

$resaVivigasOut = ($totaleOreOut != 0) ? ($totalePezziVivigasOut / $totaleOreOut) : 0;
$resaVivigasOut = number_format($resaVivigasOut, 2, '.', '');

$resaIrenIn = ($totaleOreIn != 0) ? ($totalePezziIrenIn / $totaleOreIn) : 0;
$resaIrenIn = number_format($resaIrenIn, 2, '.', '');

$resaIrenOut = ($totaleOreOut != 0) ? ($totalePezziIrenOut / $totaleOreOut) : 0;
$resaIrenOut = number_format($resaIrenOut, 2, '.', '');



$resaPolizzeIn = ($totaleOreIn != 0) ? ($totalePezziPolizzeIn / $totaleOreIn) : 0;
$resaPolizzeIn = number_format($resaPolizzeIn, 2, '.', '');

$resaPolizzeOut = ($totaleOreOut != 0) ? ($totalePezziPolizzeOut / $totaleOreOut) : 0;
$resaPolizzeOut = number_format($resaPolizzeOut, 2, '.', '');


$totobjCTCTl += $objCTC;

$totobjOUTTl += $objOUT;

// Inizializza le variabili se non esistono
if (!isset($sommaObiettiviCTC)) {
    $sommaObiettiviCTC = ['Plenitude' => 0, 'Enel' => 0, 'Vivi' => 0];
    $sommaObiettiviOUT = ['Plenitude' => 0, 'Enel' => 0, 'Vivi' => 0];
}

// Se $obiettivoCTC è un array, somma i valori per tipo
if (is_array($obiettivoCTC)) {
    foreach (['Plenitude', 'Enel', 'Vivi'] as $tipo) {
        $sommaObiettiviCTC[$tipo] += ($obiettivoCTC[$tipo] ?? 0);
    }
} else {
    // Se è un numero, assegnalo a un tipo specifico o dividilo come preferisci
    $sommaObiettiviCTC['Plenitude'] += $obiettivoCTC;
}

// Stessa logica per $obiettivoOUT
if (is_array($obiettivoOUT)) {
    foreach (['Plenitude', 'Enel', 'Vivi'] as $tipo) {
        $sommaObiettiviOUT[$tipo] += ($obiettivoOUT[$tipo] ?? 0);
    }
} else {
    $sommaObiettiviOUT['Plenitude'] += $obiettivoOUT;
}


$totaleObiettiviCTC = array_sum($sommaObiettiviCTC);
$totaleObiettiviOUT = array_sum($sommaObiettiviOUT);
//echo $sommaObiettiviCTC ['Plenitude'];
// CALCOLO DIFFERENZE PER OGNI COMMESSA
// Per CTC (IN)
// Per CTC (IN)
$sommaDifferenzeCTC['Plenitude'] = abs($totalePezziPlenitudeIn - $totobjPCTC);
$sommaDifferenzeCTC['Enel'] = abs($totalePezziEnelIn - $totobjECTC);
$sommaDifferenzeCTC['Vivi'] = abs($totalePezziVivigasIn - $totobjVCTC);

// Per OUT
$sommaDifferenzeOUT['Plenitude'] = abs($totalePezziPlenitudeOut - $totobjOUTTl);
$sommaDifferenzeOUT['Enel'] = abs($totalePezziEnelOut - $totoobjPOUT);
$sommaDifferenzeOUT['Vivi'] = abs($totalePezziVivigasOut - $totoobjEOUT);

//echo $sommaDifferenzeOUT['Plenitude'];
// CALCOLO PASSI GIORNO PER OGNI COMMESSA
// Per CTC (IN)
$passogiornoTotaleP = ($giornirimanenti > 0 && isset($sommaObiettiviCTC['Plenitude'])) 
    ? max(0, round(($sommaObiettiviCTC['Plenitude'] - $totalePezziPlenitudeIn) / $giornirimanenti)) 
    : 0;
$passogiornoTotaleE  = ($giornirimanenti > 0 && isset($sommaObiettiviCTC['Enel'])) 
    ? max(0, round(($sommaObiettiviCTC['Enel'] - $totalePezziEnelIn) / $giornirimanenti)) 
    : 0;
$passogiornoTotaleV  = ($giornirimanenti > 0 && isset($sommaObiettiviCTC['Vivi'])) 
    ? max(0, round(($sommaObiettiviCTC['Vivi'] - $totalePezziVivigasIn) / $giornirimanenti)) 
    : 0;
$passogiornoTotaleI  = ($giornirimanenti > 0 && isset($sommaObiettiviCTC['Iren'])) 
    ? max(0, round(($sommaObiettiviCTC['Iren'] - $totalePezziIrenIn) / $giornirimanenti)) 
    : 0;
//echo $passogiornoTotaleP;
//echo $passogiornoTotaleE;
//echo $passogiornoTotaleV;
//// Per OUT
$passogiornoTotalePo = ($giornirimanenti > 0 && isset($sommaObiettiviOUT['Plenitude'])) 
    ? max(0, round(($sommaDifferenzeOUT['Plenitude']  - $totalePezziPlenitudeOut) / $giornirimanenti)) 
    : 0;
$passogiornoTotaleEo = ($giornirimanenti > 0 && isset($sommaObiettiviOUT['Enel'])) 
    ? max(0, round(($sommaObiettiviOUT['Enel'] - $totalePezziEnelOut) / $giornirimanenti)) : 0;

$passogiornoTotaleVo = ($giornirimanenti > 0 && isset($sommaObiettiviOUT['Vivigas'])) 
    ? max(0, round(($sommaObiettiviOUT['Vivigas'] - $totalePezziVivigasOut) / $giornirimanenti)) : 0;

$passogiornoTotaleIo = ($giornirimanenti > 0 && isset($sommaObiettiviOUT['Iren'])) 
    ? max(0, round(($sommaObiettiviOUT['Iren'] - $totalePezziirenOut) / $giornirimanenti)) : 0;

// CALCOLO TOTALE DIFFERENZE E PASSI GIORNO TOTALI
$differenzaTotaleCTC = $sommaDifferenzeCTC['Plenitude'] + $sommaDifferenzeCTC['Enel'] + $sommaDifferenzeCTC['Vivi'];
$differenzaTotaleOUT = $sommaDifferenzeOUT['Plenitude'] + $sommaDifferenzeOUT['Enel'] + $sommaDifferenzeOUT['Vivi'];

$passogiornoTotaleCTC = $giornirimanenti > 0 ? max(0, round($differenzaTotaleCTC / $giornirimanenti)) : 0;
$passogiornoTotaleOUT = $giornirimanenti > 0 ? max(0, round($differenzaTotaleOUT / $giornirimanenti)) : 0;




            $fatturatoTotPIn = $mediaMandato * $totalePezziPlenitudeIn;
            $fatturatoTotEIn = $mediaMandato * $totalePezziEnelIn;
            $fatturatoTotVIn = $mediaMandato * $totalePezziVivigasIn;
            $fatturatoTotIIn = $mediaMandato * $totalePezziIrenIn;
            
            $fatturatoTotIn = $fatturatoTotPIn + $fatturatoTotEIn + $fatturatoTotVIn + $fatturatoTotIIn;
            
            $fatturatoTotPout = $mediaMandato * $totalePezziPlenitudeOut;
            $fatturatoTotEout = $mediaMandato * $totalePezziEnelOut;
            $fatturatoTotVout = $mediaMandato * $totalePezziVivigasOut;
            $fatturatoTotIout = $mediaMandato * $totalePezziIrenOut;
            
            $fatturatoTotOut = $fatturatoTotPout + $fatturatoTotEout + $fatturatoTotVout + $fatturatoTotIout;
            
            $ricavoOrarioTotIn = $totaleOreIn != 0 ? round($fatturatoTotIn / $totaleOreIn, 0) : 0;
            
            $fatturatoOut = $mediaMandato * $totalepezziOut;
            
            $ricavoOrarioTotOut = $totaleOreOut != 0 ? round($fatturatoTotOut / $totaleOreOut, 0) : 0;



// Aggiungi la riga finale con i totali
$html .= "<tr style='background-color: #FFE4B5;'>"; // Arancione pastello
$html .= "<td>Totale</td>"; // TL Group
$html .= "<td>CTC</td>"; // Tipo
$html .= "<td style='border-left: 5px double #D0E4F5'>$totaleOreIn</td>"; // Ore IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$totaleLeadRiferimentoIn</td>"; // Lead IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziIn</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaTotaleIn</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>" . round($ricavoOrarioTotIn) . " €</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$totobjCTCTl</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN

$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziPlenitudeIn</td>"; // PDP Plenitude IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaPlenitudeIn</td>"; // Resa/Conv Plenitude IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Plenitude IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$totobjPCTC</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoTotaleP</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN


$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziEnelIn</td>"; // PDP Enel IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaEnelIn</td>"; // Resa/Conv Enel IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Enel IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$totobjECTC</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoTotaleE</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN

$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziVivigasIn</td>"; // PDP Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaVivigasIn</td>"; // Resa/Conv Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$totobjVCTC</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoTotaleV</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN


$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziIrenIn</td>"; // PDP Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaIrenIn</td>"; // Resa/Conv Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Vivigas IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$totobjICTC</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoTotaleI</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN

$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziPolizzeIn</td>"; // PDP Polizze IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaPolizzeIn</td>"; // Resa/Conv Polizze IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Polizze IN
$html .= "</tr>";

$html .= "<tr style='background-color: #FFE4B5;'>"; // Arancione pastello
$html .= "<td>Totale</td>"; // TL Group
$html .= "<td>OUT</td>"; // Tipo
$html .= "<td style='border-left: 5px double #D0E4F5'>$totaleOreOut</td>"; // Ore OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Lead OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziOut</td>"; // PDP OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaTotaleOut</td>"; // Resa/Convertito OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>" . round($ricavoOrarioTotOut) . " €</td>"; // Resa/Convertito OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$totobjOUTTl</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN

$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziPlenitudeOut</td>"; // PDP Plenitude OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaPlenitudeOut</td>"; // Resa/Conv Plenitude OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Plenitude OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$totoobjPOUT</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoTotalePo</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN

$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziEnelOut</td>"; // PDP Enel OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaEnelOut</td>"; // Resa/Conv Enel OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Enel OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$totoobjEOUT</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoTotaleEo</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN

$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziVivigasOut</td>"; // PDP Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaVivigasOut</td>"; // Resa/Conv Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$totoobjVOUT</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoTotaleVo</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN

$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziIrenOut</td>"; // PDP Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaIrenOut</td>"; // Resa/Conv Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Vivigas OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$totoobjIOUT</td>"; // PDP IN
$html .= "<td style='border-left: 5px double #D0E4F5'>$passogiornoTotaleIo</td>"; // Resa/Convertito IN
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Convertito IN

$html .= "<td style='border-left: 5px double #D0E4F5'>$totalePezziPolizzeOut</td>"; // PDP Polizze OUT
$html .= "<td style='border-left: 5px double #D0E4F5'>$resaPolizzeOut</td>"; // Resa/Conv Polizze OUT
$html .= "<td style='border-left: 5px double #D0E4F5'></td>"; // Resa/Conv Polizze OUT
$html .= "</tr>";

// Chiudi il corpo della tabella e la tabella stessa
$html .= "</tbody>";
$html .= "</table>";

// Output della tabella HTML
echo $html;