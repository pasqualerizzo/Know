<?php
// Configurazione error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Inclusioni file di connessione
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneGt.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";

// Creazione connessione al database
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

if (!$conn19) {
    die("Connessione al database fallita: " . mysqli_connect_error());
}

// Recupero e validazione parametri
$mese = filter_input(INPUT_POST, "mese", FILTER_SANITIZE_STRING);
if (empty($mese)) {
    die("Mese non specificato");
}

// Calcolo date
$dataMinore = $mese . "-01";
$dataMaggiore = date('Y-m-d', strtotime("last day of " . $mese));

// Recupero array mandati e sedi
$mandato = json_decode($_POST["mandato"] ?? '[]', true) ?? [];
$sede = json_decode($_POST["sede"] ?? '[]', true) ?? [];

// Formattazione date per visualizzazione
$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

// Funzione per ottenere gli obiettivi TL
function getObbiettivoTLData($conn19, $dataMinore, $dataMaggiore, $sede) {
    $meseMinore = date('Y-m-01', strtotime($dataMinore));
    $meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

    $query = "SELECT 
                `sede` AS sede, 
                SUM(`ore`) AS ore, 
                SUM(`plenitudePdp`) AS plenitudePdp, 
                SUM(`enelPdp`) AS enelPdp, 
                SUM(`vivigasPdp`) AS vivigasPdp, 
                SUM(`polizzePdp`) AS polizzePdp,
                SUM(`enelInPdp`) AS enelInPdp,
                SUM(`heracomPdp`) AS heracomPdp,
                SUM(`irenPdp`) AS irenPdp
              FROM `obbiettivoTL` 
              WHERE 
                  `mese` >= ? 
                  AND `mese` <= ? 
                  AND `sede` = ?
              GROUP BY `sede`";
    
    $stmt = $conn19->prepare($query);
    $stmt->bind_param("sss", $meseMinore, $meseMaggiore, $sede);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Inizializzazione variabili
$oreSede = 0;
$pezzoLordow1 = 0;
$pezzoLordow6 = 0;
$oreW1 = 0;
$resaTotw1 = 0;
$fatturato1T = 0;
$connGt = 0;
$resawt1 = 0;
$sedePrecedente = "";
$querySede = "";
$lunghezzaSede = count($sede);
$pezzoLordo = 0;
$pezzoOk = 0;
$totalissimoobj = 0;

// Costruzione query per sedi
if ($lunghezzaSede == 1) {
    $querySede .= " AND sede=? ";
} elseif ($lunghezzaSede > 1) {
    $querySede .= " AND ( ";
    $placeholders = implode(" OR sede=?", array_fill(0, $lunghezzaSede, ""));
    $querySede .= " sede=?" . substr($placeholders, 1) . " ) ";
}

function calcolaGiorniLavorativi($dataInizio, $dataFine, $giorniFestiviPerMese = [], $dateDaEscludere = []) {
    $giorniLavorativiPrevisti = 0;
    $giorniLavoratiEffettivi = 0;
    
    $dataInizio = new DateTime($dataInizio);
    $dataFine = new DateTime($dataFine);

    $meseRiferimento = $dataInizio->format('m');
    $annoRiferimento = $dataInizio->format('Y');

    if ($dataInizio->format('m') !== $dataFine->format('m')) {
        $meseRiferimento = $dataFine->format('m');
        $annoRiferimento = $dataFine->format('Y');
    }

    $primoGiornoMeseRiferimento = new DateTime("$annoRiferimento-$meseRiferimento-01");
    $ultimoGiornoMeseRiferimento = new DateTime($primoGiornoMeseRiferimento->format('Y-m-t'));

    $interval = new DateInterval('P1D');
    $period = new DatePeriod($primoGiornoMeseRiferimento, $interval, $ultimoGiornoMeseRiferimento->modify('+1 day'));

    $giorniFestiviMese = 0;
    $chiaveFestivi = "$annoRiferimento-$meseRiferimento";
    if (isset($giorniFestiviPerMese[$chiaveFestivi])) {
        $giorniFestiviMese = $giorniFestiviPerMese[$chiaveFestivi];
    }

    $dateEsclusioneFormattate = array_map(function($data) {
        return (new DateTime($data))->format('Y-m-d');
    }, $dateDaEscludere);

    $giorniLavorativiPrevistiSenzaFestivi = 0;
    foreach ($period as $giorno) {
        $giornoFormattato = $giorno->format('Y-m-d');
        $giornoSettimana = $giorno->format('N');

        if (in_array($giornoFormattato, $dateEsclusioneFormattate)) {
            continue;
        }

        if ($giornoSettimana >= 1 && $giornoSettimana <= 5) {
            $giorniLavorativiPrevistiSenzaFestivi += 1;
        } elseif ($giornoSettimana == 6) {
            $giorniLavorativiPrevistiSenzaFestivi += 0.5;
        }
    }
    
    $giorniLavorativiPrevisti = $giorniLavorativiPrevistiSenzaFestivi - $giorniFestiviMese;

    $interval = new DateInterval('P1D');
    $period = new DatePeriod($dataInizio, $interval, $dataFine->modify('+1 day'));

    foreach ($period as $giorno) {
        $giornoFormattato = $giorno->format('Y-m-d');
        $giornoSettimana = $giorno->format('N');

        if (in_array($giornoFormattato, $dateEsclusioneFormattate)) {
            continue;
        }

        if ($giornoSettimana >= 1 && $giornoSettimana <= 5) {
            $giorniLavoratiEffettivi += 1;
        } elseif ($giornoSettimana == 6) {
            $giorniLavoratiEffettivi += 0.5;
        }
    }

    $giornirimanenti = $giorniLavorativiPrevisti - $giorniLavoratiEffettivi;

    return [
        'giorni_lavorabili_previsti' => $giorniLavorativiPrevisti,
        'giorni_lavorati_effettivi' => $giorniLavoratiEffettivi,
        'giorni_rimanenti' => $giornirimanenti
    ];
}

$giorniFestiviPerMese = [
    '2025-04' => 0,
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

$result = calcolaGiorniLavorativi($dataMinore, $dataMaggiore, $giorniFestiviPerMese, $dateDaEscludere);

echo "Giorni lavorabili previsti: " . $result['giorni_lavorabili_previsti'] . "<br>";
echo "Giorni lavorati effettivi: " . $result['giorni_lavorati_effettivi'] . "<br>";
echo "Giorni lavorativi rimanenti: " . $result['giorni_rimanenti'] . "<br>";

$giornirimanenti = $result['giorni_rimanenti'];
$giorniLavorativiPrevisti = $result['giorni_lavorabili_previsti'];
$giorniLavoratiEffettivi = $result['giorni_lavorati_effettivi'];

$mandati_validi = ["Plenitude", "Vivigas Energia", "Enel", "Iren", "EnelIn", "Heracom"];

// Prima passata: raccolta valori medi
$valoriMedi = [];
foreach ($mandato as $idMandato) {
    if (in_array($idMandato, ["Union", "Vodafone", "Bo" , "TIMmq"]) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    $meseMinore = date('Y-m-01', strtotime($dataMinore));
    $meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

    switch ($idMandato) {
        case "Plenitude":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                                AND mandato = 'Plenitude'
                                ORDER BY `id` DESC";
            break;
        case "Vivigas Energia":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                                AND mandato = 'Vivigas'
                                ORDER BY `id` DESC";
            break;
        case "Enel":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                                AND mandato = 'Enel'
                                ORDER BY `id` DESC";
            break;
        case "Iren":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                                AND mandato = 'Iren'
                                ORDER BY `id` DESC";
            break;
        case "EnelIn":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                                AND mandato = 'EnelIn'
                                ORDER BY `id` DESC";
            break;
        case "Heracom":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                                AND mandato = 'Heracom'
                                ORDER BY `id` DESC";
            break;
        default:
            continue 2;
    }

    $risultatovaloremedio = $conn19->query($queryvaloremedio);

    if ($risultatovaloremedio) {
        while ($rigaCRM = $risultatovaloremedio->fetch_array()) {
            if ($rigaCRM !== null) {
                $valoriMedi[$idMandato] = $rigaCRM[2] ?? 0;
            }
        }
    }
}

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaChiusuraSerale.php";

// Inizializzazione totali
$totaleOreW1 = 0;
$totalePezzoW1 = 0;
$valoremedio = 0;
$totaleMeseOre = 0;
$fatturatoMese = 0;
$totalepezzoLordoM = 0;
$totaliObiettivi = 0;
$totaleTarget= 0;

$mandati_validi = ["Plenitude", "Vivigas Energia", "Enel", "Iren", "EnelIn", "Heracom"];

// Seconda passata: elaborazione dati per ogni mandato
foreach ($mandato as $idMandato) {
    // Filtra i mandati non validi
    if (in_array($idMandato, ["Union", "Vodafone", "Bo", "59", "Tim"]) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    $pezzoLordow1 = 0;
    $oreW1 = 0;
    $obiettivo = 0;
    $totaliObiettivi = 0;

    switch ($idMandato) {
        case "Plenitude":
            $queryCrmSede = "SELECT
                p.`sede` AS sede,
                'Plenitude' AS plenitude,
                SUM(CASE WHEN p.comodity <> 'Polizza' AND ap.tipoCampagna = 'Lead' THEN ap.pezzoLordo ELSE 0 END) AS pezzo_lordo_no_polizza_lead,
                SUM(CASE WHEN p.comodity <> 'Polizza' AND ap.tipoCampagna <> 'Lead' THEN ap.pezzoLordo ELSE 0 END) AS pezzo_lordo_no_polizza_non_lead,
                SUM(CASE WHEN p.comodity = 'Polizza' THEN ap.pezzoLordo ELSE 0 END) AS pezzo_lordo_polizza,
                '0',
                '0',
                (
                    SELECT SUM(o.plenitudePdp)
                    FROM obbiettivoTL o
                    WHERE o.mese <= '$dataMinoreobj'
                    AND o.mese >= '$dataMinoreobj'
                ) AS obiettivo
            FROM `plenitude` p
            INNER JOIN aggiuntaPlenitude ap ON p.id = ap.id
            WHERE p.data <= '$dataMaggiore' 
            AND p.data >= '$dataMinore'
            AND ap.fasePDA = 'OK'
            GROUP BY plenitude";
            break;
            
        case "Vivigas":
        case "Vivigas Energia":
            $queryCrmSede = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                'Vivigas' AS vivigas,
                SUM(CASE WHEN tipoCampagna = 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_lead,
                SUM(CASE WHEN tipoCampagna <> 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_non_lead,
                '0' AS pezzo_lordo_polizza,
                '0',
                '0',
                (
                    SELECT SUM(o.vivigasPdp)
                    FROM obbiettivoTL o
                    WHERE o.mese <= '$dataMinoreobj'
                    AND o.mese >= '$dataMinoreobj'
                ) AS obiettivo
            FROM `vivigas`
            INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            GROUP BY vivigas";
            break;
            
        case "Enel":
            $queryCrmSede = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                'Enel' AS Enel,
                SUM(CASE WHEN comodity <> 'Fibra' AND tipoCampagna = 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_lead,
                SUM(CASE WHEN comodity <> 'Fibra' AND tipoCampagna <> 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_non_lead,
                SUM(CASE WHEN comodity = 'Fibra' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_fibra,
                '0' as consenso,
                '0' as fibraenel,
                (
                    SELECT SUM(o.enelPdp)
                    FROM obbiettivoTL o
                    WHERE o.mese <= '$dataMinoreobj'
                    AND o.mese >= '$dataMinoreobj'
                ) AS obiettivo
            FROM enel
            INNER JOIN aggiuntaEnel ON enel.id = aggiuntaEnel.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            GROUP BY Enel";
            break;
            
        case "Iren":
            $queryCrmSede = "SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                'Iren' AS Iren,
                SUM(CASE WHEN comodity <> 'Fibra' AND tipoCampagna = 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_lead,
                SUM(CASE WHEN comodity <> 'Fibra' AND tipoCampagna <> 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_non_lead,
                SUM(CASE WHEN comodity = 'Fibra' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_fibra,
                '0' as consenso,
                '0' as fibraenel,
                (
                    SELECT SUM(o.irenPdp)
                    FROM obbiettivoTL o
                    WHERE o.mese <= '$dataMinoreobj'
                    AND o.mese >= '$dataMinoreobj'
                ) AS obiettivo
            FROM iren
            INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            GROUP BY Iren";
            break;
            
        case "EnelIn":
            $queryCrmSede = "SELECT
                IFNULL('Rende', 'TOTALE') AS sede,
                'EnelIn' AS EnelIn,
                SUM(CASE WHEN comodity <> 'Fibra Enel' AND tipoCampagna = 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_lead,
                SUM(CASE WHEN comodity <> 'Fibra Enel' AND tipoCampagna <> 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_non_lead,
                '0' as polizza,
                '0' as consenso,
                SUM(CASE WHEN comodity = 'Fibra Enel' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_fibra,
                (
                    SELECT SUM(o.enelInPdp)
                    FROM obbiettivoTL o
                    WHERE o.mese <= '$dataMinoreobj'
                    AND o.mese >= '$dataMinoreobj'
                ) AS obiettivo
            FROM enelIn
            INNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            GROUP BY EnelIn";
            break;
            
        case "Heracom":
            $queryCrmSede = "SELECT
                IFNULL('Lamezia', 'TOTALE') AS sede,
                'Heracom' AS Heracom,
                SUM(CASE WHEN comodity <> 'Consenso' AND tipoCampagna = 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_lead,
                SUM(CASE WHEN comodity <> 'Consenso' AND tipoCampagna <> 'Lead' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_non_lead,
                '0' as polizza,
                SUM(CASE WHEN comodity = 'Consenso' THEN pezzoLordo ELSE 0 END) AS pezzo_lordo_consenso,
                '0'as fibraenel,
                (
                    SELECT SUM(o.heracomPdp)
                    FROM obbiettivoTL o
                    WHERE o.mese <= '$dataMinoreobj'
                    AND o.mese >= '$dataMinoreobj'
                ) AS obiettivo
            FROM heracom
            INNER JOIN aggiuntaHeracom ON heracom.id = aggiuntaHeracom.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            GROUP BY Heracom";
            break;
            
        default:
            // Nessuna azione per mandati non riconosciuti
            continue 2;
    }
    
    // Debug: mostra la query (puoi rimuoverlo in produzione)
     echo $queryCrmSede;
    
    // [Resto del codice per eseguire la query e processare i risultati...]

    $risultatoCrmSede = $conn19->query($queryCrmSede);

    if ($risultatoCrmSede) {
        while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
            if ($rigaCRM !== null) {
                $pezzoLordoLd = $rigaCRM[2] ?? 0;
                $pezzoLordoNonLd = $rigaCRM[3] ?? 0;
                $pezzoLordoPolizze = $rigaCRM[4] ?? 0;
                $consenso = $rigaCRM[5] ?? 0;
                $fibraenel = $rigaCRM[6] ?? 0;
                $obiettivo = $rigaCRM[8] ?? 0;

                $percentualePolizze = ($totalepezzoLordoM != 0) ? round(($pezzoLordoPolizze / $totalepezzoLordoM) * 100, 2) : 0;
                $totaliObiettivi += $obiettivo;
                $fatturato1 = ($valoriMedi[$idMandato] ?? 0) * $pezzoLordow1;
                $target = ($valoriMedi[$idMandato] ?? 0) * $totaliObiettivi;
                
                $totaleMeseOre = $oreW1;
                $fatturatoMese = $fatturato1;
                $totalepezzoLordoM = $pezzoLordow1;
                
                $resaw1 = $oreW1 != 0 ? round($fatturato1 / $oreW1, 0) : 0;
                $resaMese = $totaleMeseOre != 0 ? round($fatturatoMese / $totaleMeseOre, 2) : 0;
                $totalepezzoLordoM = $pezzoLordoLd + $pezzoLordoNonLd;
                
                $html .= "<tr>";
                $html .= "<td>$idMandato</td>";
                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totaleMeseOre, 0) . "</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$totalepezzoLordoM</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordoLd</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordoNonLd</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordoPolizze</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . $percentualePolizze ."  %</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$consenso</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$fibraenel</td>";
                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$obiettivo</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$totaliObiettivi</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturatoMese, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($target, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($resaMese, 0) . " €/h</td>";
                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "</tr>";

                $totaleOreW1 += $oreW1;
                $totalePezzoW1 += $pezzoLordow1;
                $fatturato1T += $fatturato1;
                $totalissimoobj += $totaliObiettivi;
                $totaleTarget += $target;
            }
        }
    }
}

// Calcolo totali finali
$totalissimoOre = $totaleOreW1;
$totalissimopezzi = $totalePezzoW1;
$fatturatissimo = $fatturato1T;
$resissima = $totalissimoOre != 0 ? round($fatturatissimo / $totalissimoOre, 1) : 0;
$resawt1 = $totaleOreW1 != 0 ? round($fatturato1T / $totaleOreW1, 0) : 0;

// Aggiungi la riga dei totali
$html .= "<tr>";
$html .= "<td><strong>TOTALE</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totalissimoOre, 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimoobj</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($fatturatissimo, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleTarget, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong><strong>" . round($resissima, 0) . "  €/h</td>";
$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "</tr>";

$html .= "</table>";

echo $html;