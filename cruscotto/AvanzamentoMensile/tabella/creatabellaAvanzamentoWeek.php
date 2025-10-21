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

$dataMinore = $mese . "-01";
$dataMaggiore = date('Y-m-d', strtotime("last day of " . $mese));
$dataMaggioreOggi = date('Y-m-d'); // OGGI (non ultimo giorno del mese)
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);

// Inizializzazione variabili
$oreSede = 0;
$pezzoLordow1 = 0;
$pezzoLordow2 = 0;
$pezzoLordow3 = 0;
$pezzoLordow4 = 0;
$pezzoLordow5 = 0;
$pezzoLordow6 = 0;
$oreW1 = 0;
$oreW2 = 0;
$oreW3 = 0;
$oreW4 = 0;
$oreW5 = 0;
$oreW6 = 0;
$resaTotw1 = 0;
$resaTotw2 = 0;
$resaTotw3 = 0;
$resaTotw4 = 0;
$resaTotw5 = 0;
$resaTotw6 = 0;
$totfatturatoPaf = 0;
$fatturato1T = 0;
$fatturato2T = 0;
$fatturato3T = 0;
$fatturato4T = 0;
$fatturato5T = 0;
$fatturato6T = 0;
$fatturatoPaf = 0;
$resawt1 = 0;
$resawt2 = 0;
$resawt3 = 0;
$resawt4 = 0;
$resawt5 = 0;
$resawt6 = 0;

$sedePrecedente = "";

$querySede = "";
$lunghezzaSede = count($sede);

$pezzoLordo = 0;
$pezzoOk = 0;
$totalissimoobj = 0;
if ($lunghezzaSede == 1) {
    $querySede .= " AND sede='$sede[0]' ";
} elseif ($lunghezzaSede > 1) {
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
    
     // Calcola i giorni rimanenti
    $giornirimanenti = $giorniLavorativiPrevisti - $giorniLavoratiEffettivi;
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
    '2025-08-11',
    '2025-08-12',
    '2025-08-13',
    '2025-08-14',
    '2025-08-15',
    '2025-08-16',
    '2025-08-17',
    '2025-08-15',
    '2025-11-01',
    '2025-12-08',
    '2025-12-25',
    '2025-12-26'
];
// Verifica se è il mese corrente
$meseCorrente = date('Y-m');
$meseRichiesto = date('Y-m', strtotime($dataMinore));

if ($meseRichiesto == $meseCorrente) {
    // Se è il mese corrente, usa la data di oggi
    $dataFineCalcolo = $dataMaggioreOggi;
} else {
    // Se è un mese passato, usa la fine del mese
    $dataFineCalcolo = $dataMaggiore;
}

$result = calcolaGiorniLavorativi($dataMinore, $dataFineCalcolo, $giorniFestiviPerMese, $dateDaEscludere);

$giorniLavoratiEffettivi = $result['giorni_lavorati_effettivi'];
$giorniLavorativiRimanenti = $result['giorni_rimanenti'];
$giorniLavorativiPrevisti = $result['giorni_lavorabili_previsti'];

echo "<br>";
$giornirimanenti = $result['giorni_rimanenti'];
$giorniLavorativiPrevisti = $result['giorni_lavorabili_previsti'];
$giorniLavoratiEffettivi = $result['giorni_lavorati_effettivi'];

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaAvanzamentoWeek.php";

// Inizializzazione totali
$totaleOreW1 = 0;
$totaleOreW2 = 0;
$totaleOreW3 = 0;
$totaleOreW4 = 0;
$totaleOreW5 = 0;
$totaleOreW6 = 0;
$totalePezzoW1 = 0;
$totalePezzoW2 = 0;
$totalePezzoW3 = 0;
$totalePezzoW4 = 0;
$totalePezzoW5 = 0;
$totalePezzoW6 = 0;

$valoremedio = 0;
$totaleMeseOre = 0;
$fatturatoMese = 0;
$totalepezzoLordoM = 0;
$totaliObiettivi = 0;
$totaleTarget= 0;
$fatturatoPaf = 0;
// Lista dei mandati validi
$mandati_validi = ["Plenitude", "Vivigas Energia", "Enel", "Iren", "EnelIn" , "Heracom"];

// Prima passata: raccolta valori medi
$valoriMedi = [];
foreach ($mandato as $idMandato) {
    // Salta i mandati speciali e quelli non validi
    if (in_array($idMandato, ["Union", "Vodafone", "Bo" , "59" , "TIM" , "TIMmq" , 'Sorgenia']) || !in_array($idMandato, $mandati_validi)) {
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
       case "Vivigas":  // Cambia da "Vivigas Energia" a "Vivigas"
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
                case "Iren":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
                                AND mandato = 'Iren'
                                ORDER BY `id` DESC";
            break;
                case "EnelIn":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
                                AND mandato = 'EnelIn'
                                ORDER BY `id` DESC";

            break;
                case "Heracom":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
                                AND mandato = 'Heracom'
                                ORDER BY `id` DESC";
            break;
//                case "Ondapiu":
//            $queryvaloremedio = "SELECT mandato, mese, media 
//                                FROM `mediaPraticaMese` 
//                                WHERE mese >= '$dataMinore' AND mese <= '$dataMaggiore' 
//                                AND mandato = 'Ondapiu'
//                                ORDER BY `id` DESC";
//            break;
        default:
            continue 2;
    }

    // Esegui la query per il valore medio
    $risultatovaloremedio = $conn19->query($queryvaloremedio);

    if ($risultatovaloremedio) {
        while ($rigaCRM = $risultatovaloremedio->fetch_array()) {
            if ($rigaCRM !== null) {
                $valoriMedi[$idMandato] = $rigaCRM[2] ?? 0;
            }
        }
    }
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







// Seconda passata: elaborazione dati per ogni mandato
foreach ($mandato as $idMandato) {
    // Salta i mandati speciali e quelli non validi
    if (in_array($idMandato, ["Union", "Vodafone", "Bo"]) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    // Inizializza variabili per questo mandato
    $pezzoLordow1 = 0;
    $pezzoLordow2 = 0;
    $pezzoLordow3 = 0;
    $pezzoLordow4 = 0;
    $pezzoLordow5 = 0;
    $pezzoLordow6 = 0;
    $oreW1 = 0;
    $oreW2 = 0;
    $oreW3 = 0;
    $oreW4 = 0;
    $oreW5 = 0;
    $oreW6 = 0;
    $obiettivo = 0;
    $totaliObiettivi = 0;
  
    // Query specifica per mandato
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
                    WHERE                     o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM `plenitude` p
            INNER JOIN aggiuntaPlenitude ap ON p.id = ap.id
            WHERE p.data <= '$dataMaggiore' 
            AND p.data >= '$dataMinore'
            AND ap.fasePDA = 'OK'
            AND p.comodity <> 'Polizza'";
            
//            echo $queryCrmSede;
            
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
                    WHERE 
                     o2.mese <= '$dataMaggiore'
                    AND o2.mese >= '$dataMinore'
                ) AS obiettivo            
            FROM `vivigas`
            INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            ";
//            echo $queryCrmSede;
            break;
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
                    WHERE 
                     o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM enel
            INNER JOIN aggiuntaEnel ON enel.id = aggiuntaEnel.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'Fibra'
            ";
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
                    WHERE 
                     o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM iren
            INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'Fibra'
            ";
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
                    WHERE                     o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM enelIn
            INNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'cONSENSO'
            ";
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
                    WHERE 
                     o.mese <= '$dataMaggiore'
                    AND o.mese >= '$dataMinore'
                ) AS obiettivo
            FROM heracom
            INNER JOIN aggiuntaHeracom ON heracom.id = aggiuntaHeracom.id
            WHERE data <= '$dataMaggiore' AND data >= '$dataMinore'
            AND fasePDA = 'OK'
            AND comodity <> 'Consenso'
            ";
                            
                            
                            
            break;
        default:
            continue 2;
    }

    $risultatoCrmSede = $conn19->query($queryCrmSede);

    if ($risultatoCrmSede) {
        while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
            if ($rigaCRM !== null) {
                $pezzoLordow1 = round($rigaCRM[2] ?? 0, 0);
                $pezzoLordow2 = round($rigaCRM[3] ?? 0, 0);
                $pezzoLordow3 = round($rigaCRM[4] ?? 0, 0);
                $pezzoLordow4 = round($rigaCRM[5] ?? 0, 0);
                $pezzoLordow5 = round($rigaCRM[6] ?? 0, 0);
                $pezzoLordow6 = round($rigaCRM[7] ?? 0, 0);
                $obiettivo = round($rigaCRM[8] ?? 0, 0);
                // Query per le ore suddivise per settimana
 switch ($idMandato) {
    case "Plenitude":
    case "Vivigas Energia":
    case "Iren":
    case "Enel":
        $queryOreSettimana = "
            SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                '$idMandato' AS mandato,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 1 THEN numero/3600 ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 2 THEN numero/3600 ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 3 THEN numero/3600 ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 4 THEN numero/3600 ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 5 THEN numero/3600 ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 6 THEN numero/3600 ELSE 0 END) AS WEEK_6
            FROM 
                `stringheTotale`
            WHERE 
                giorno >= '$dataMinore' AND giorno <= '$dataMaggiore'
                AND livello <= 6
                AND idMandato = '$idMandato'";
        break;
        
    case "EnelIn":
        $queryOreSettimana = "
            SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                'EnelIn' AS mandato,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 THEN oreDichiarate ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 THEN oreDichiarate ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 THEN oreDichiarate ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 THEN oreDichiarate ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 THEN oreDichiarate ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 THEN oreDichiarate ELSE 0 END) AS WEEK_6
            FROM 
                `oreEnelIn`
            WHERE 
                data >= '$dataMinore' AND data <= '$dataMaggiore'
                AND mandato = 'EnelIn'";
        break;
        
    case "Heracom":
        $queryOreSettimana = "
           SELECT
    IFNULL(sede, 'TOTALE') AS sede,
    'Heracom' AS mandato,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 1 THEN numero ELSE 0 END)/3600, 2) AS WEEK_1,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 2 THEN numero ELSE 0 END)/3600, 2) AS WEEK_2,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 3 THEN numero ELSE 0 END)/3600, 2) AS WEEK_3,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 4 THEN numero ELSE 0 END)/3600, 2) AS WEEK_4,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 5 THEN numero ELSE 0 END)/3600, 2) AS WEEK_5,
    ROUND(SUM(CASE WHEN WEEK(giorno, 1) - WEEK(DATE_SUB(giorno, INTERVAL DAYOFMONTH(giorno) - 1 DAY), 1) + 1 = 6 THEN numero ELSE 0 END)/3600, 2) AS WEEK_6
FROM 
    `stringheSiscallLeadTC`
WHERE 
    giorno >= '$dataMinore' AND giorno <= '$dataMaggiore'
    AND mandato = 'Heracom' AND userGroup = 'OP_Lam_piannazzo' ";
        break;
     
    echo $queryOreSettimana;
    
    case "TIMmq":
        $queryOreSettimana = "
            SELECT
                IFNULL(sede, 'TOTALE') AS sede,
                'TIMmq' AS mandato,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 1 THEN oreDichiarate ELSE 0 END) AS WEEK_1,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 2 THEN oreDichiarate ELSE 0 END) AS WEEK_2,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 3 THEN oreDichiarate ELSE 0 END) AS WEEK_3,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 4 THEN oreDichiarate ELSE 0 END) AS WEEK_4,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 5 THEN oreDichiarate ELSE 0 END) AS WEEK_5,
                SUM(CASE WHEN WEEK(data, 1) - WEEK(DATE_SUB(data, INTERVAL DAYOFMONTH(data) - 1 DAY), 1) + 1 = 6 THEN oreDichiarate ELSE 0 END) AS WEEK_6
            FROM 
                `oreTIMmq`
            WHERE 
                data >= '$dataMinore' AND data <= '$dataMaggiore'
                AND mandato = 'TIMmq'";
        break;
        
    default:
        $queryOreSettimana = "";
        break;
}

// Esecuzione della query e gestione dei risultati
$risultatoOreSettimana = $conn19->query($queryOreSettimana);
$rigaOre = $risultatoOreSettimana ? $risultatoOreSettimana->fetch_array() : null;

if ($rigaOre !== null) {
    $oreW1 = round($rigaOre[2] ?? 0, 0);
    $oreW2 = round($rigaOre[3] ?? 0, 0);
    $oreW3 = round($rigaOre[4] ?? 0, 0);
    $oreW4 = round($rigaOre[5] ?? 0, 0);
    $oreW5 = round($rigaOre[6] ?? 0, 0);
    $oreW6 = round($rigaOre[7] ?? 0, 0);
} else {
    $oreW1 = $oreW2 = $oreW3 = $oreW4 = $oreW5 = $oreW6 = 0;
}
//                echo $obiettivo;
              $totaliObiettivi += $obiettivo;
                // Calcolo fatturato e resa
                $fatturato1 = ($valoriMedi[$idMandato] ?? 0) * $pezzoLordow1;
                $fatturato2 = ($valoriMedi[$idMandato] ?? 0) * $pezzoLordow2;
                $fatturato3 = ($valoriMedi[$idMandato] ?? 0) * $pezzoLordow3;
                $fatturato4 = ($valoriMedi[$idMandato] ?? 0) * $pezzoLordow4;
                $fatturato5 = ($valoriMedi[$idMandato] ?? 0) * $pezzoLordow5;
                $fatturato6 = ($valoriMedi[$idMandato] ?? 0) * $pezzoLordow6;
                $target =  ($valoriMedi[$idMandato] ?? 0) * $totaliObiettivi;
                
                
                $totaleMeseOre = $oreW1 + $oreW2 + $oreW3 + $oreW4 + $oreW5 + $oreW6;
                $fatturatoMese = $fatturato1 + $fatturato2 + $fatturato3 + $fatturato4 + $fatturato5 + $fatturato6; 
                $totalepezzoLordoM = $pezzoLordow1 + $pezzoLordow2 + $pezzoLordow3 + $pezzoLordow4 + $pezzoLordow5 + $pezzoLordow6;
                
                // Somma i contatti utili SOLO per Heracom al totale mensile
if ($idMandato === "Heracom") {
    $fatturatoMese += $fatturatoCu;
}
                
                
                // Calcolo resa
                $resaw1 = $oreW1 != 0 ? round($fatturato1 / $oreW1, 0) : 0;
                $resaw2 = $oreW2 != 0 ? round($fatturato2 / $oreW2, 0) : 0;
                $resaw3 = $oreW3 != 0 ? round($fatturato3 / $oreW3, 0) : 0;
                $resaw4 = $oreW4 != 0 ? round($fatturato4 / $oreW4, 0) : 0;
                $resaw5 = $oreW5 != 0 ? round($fatturato5 / $oreW5, 0) : 0;
                $resaw6 = $oreW6 != 0 ? round($fatturato6 / $oreW6, 0) : 0;
                $resaMese = $totaleMeseOre != 0 ? round($fatturatoMese / $totaleMeseOre, 0) : 0;
                
                
                if ($giorniLavoratiEffettivi > 0 && is_numeric($pezzoOk) && is_numeric($giorniLavorativiPrevisti)) {
    $fatturatoPaf = round((round($fatturatoMese) / $giorniLavoratiEffettivi) * $giorniLavorativiPrevisti);
} else {
    $fatturatoPaf = 0; // Se non ci sono giorni lavorati o i valori non sono validi, i pezzi OK previsti devono essere 0
}
                
                
                
                
                // Genera la riga HTML
                $html .= "<tr>";
                $html .= "<td>$idMandato</td>";
                
                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totaleMeseOre, 0) . "</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$totalepezzoLordoM</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$totaliObiettivi</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturatoMese, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturatoPaf, 0) . "   €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($target, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($resaMese, 0) . "   €/h</td>";

                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreW1, 0) . "</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordow1</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturato1, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($resaw1, 0) . " €/h</td>";

                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreW2, 0) . "</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordow2</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturato2, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($resaw2, 0) . " €/h</td>";

                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreW3, 0) . "</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordow3</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturato3, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($resaw3, 0) . " €/h</td>";

                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreW4, 0) . "</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordow4</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturato4, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($resaw4, 0) . " €/h</td>";

                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreW5, 0) . "</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordow5</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturato5, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($resaw5, 0) . " €/h</td>";

                $html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreW6, 0) . "</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordow6</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($fatturato6, 0) . " €</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round($resaw6, 0) . " €/h</td>";

                $html .= "</tr>";

                // Aggiorna i totali
                $totaleOreW1 += $oreW1;
                $totaleOreW2 += $oreW2;
                $totaleOreW3 += $oreW3;
                $totaleOreW4 += $oreW4;
                $totaleOreW5 += $oreW5;
                $totaleOreW6 += $oreW6;

                $totalePezzoW1 += $pezzoLordow1;
                $totalePezzoW2 += $pezzoLordow2;
                $totalePezzoW3 += $pezzoLordow3;
                $totalePezzoW4 += $pezzoLordow4;
                $totalePezzoW5 += $pezzoLordow5;
                $totalePezzoW6 += $pezzoLordow6;
                
                $fatturato1T += $fatturato1;
                $fatturato2T += $fatturato2;
                $fatturato3T += $fatturato3;
                $fatturato4T += $fatturato4;
                $fatturato5T += $fatturato5;
                $fatturato6T += $fatturato6;
                $totalissimoobj += $totaliObiettivi;
                $totaleTarget += $target;
                $totfatturatoPaf += $fatturatoPaf;
            }
        }
    }
}

// Calcolo totali finali
$totalissimoOre = $totaleOreW1 + $totaleOreW2 + $totaleOreW3 + $totaleOreW4 + $totaleOreW5 + $totaleOreW6;
$totalissimopezzi = $totalePezzoW1 + $totalePezzoW2 + $totalePezzoW3 + $totalePezzoW4 + $totalePezzoW5 + $totalePezzoW6;
$fatturatissimo = $fatturato1T + $fatturato2T + $fatturato3T + $fatturato4T + $fatturato5T + $fatturato6T;


if ($idMandato === "Heracom") {
    $fatturatissimo += $fatturatoCu;
}
      


$resissima = $totalissimoOre != 0 ? round($fatturatissimo / $totalissimoOre, 1) : 0;
$resawt1 = $totaleOreW1 != 0 ? round($fatturato1T / $totaleOreW1, 0) : 0;
$resawt2 = $totaleOreW2 != 0 ? round($fatturato2T / $totaleOreW2, 0) : 0;
$resawt3 = $totaleOreW3 != 0 ? round($fatturato3T / $totaleOreW3, 0) : 0;
$resawt4 = $totaleOreW4 != 0 ? round($fatturato4T / $totaleOreW4, 0) : 0;
$resawt5 = $totaleOreW5 != 0 ? round($fatturato5T / $totaleOreW5, 0) : 0;
$resawt6 = $totaleOreW6 != 0 ? round($fatturato6T / $totaleOreW6, 0) : 0;

// Aggiungi la riga dei totali
$html .= "<tr>";
$html .= "<td><strong>TOTALE</strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totalissimoOre, 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimopezzi</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalissimoobj</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($fatturatissimo, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totfatturatoPaf, 0) . "  €</td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleTarget, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($resissima, 0) . "   €/h</td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW1, 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezzoW1</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($fatturato1T, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($resawt1, 0) . "  €/h<strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW2, 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezzoW2</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($fatturato2T, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($resawt2, 0) . "  €/h<strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW3, 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezzoW3</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($fatturato3T, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($resawt3, 0) . "  €/h<strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW4, 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezzoW4</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($fatturato4T, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($resawt4, 0) . "  €/h<strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW5, 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezzoW5</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($fatturato5T, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($resawt5, 0) . "  €/h<strong></td>";

$html .= "<td style='border-left: 2px solid lightslategray; background-color: orange;'></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($totaleOreW6, 0) . "</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>$totalePezzoW6</strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($fatturato6T, 0) . " €<strong></td>";
$html .= "<td style='border-left: 2px solid lightslategray'><strong>" . round($resawt6, 0) . "  €/h<strong></td>";

$html .= "</tr>";

$html .= "</table>";

echo $html;