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
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

function getObbiettivoTLData($conn19, $dataMinore, $dataMaggiore, $sede) {
    // Converti le date nel primo giorno del mese per allineamento con il DB
    $meseMinore = date('Y-m-01', strtotime($dataMinore));
    $meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

    $query = "SELECT 
                `sede` AS sede, 
                SUM(`ore`) AS ore, 
                SUM(`plenitudePdp`) AS plenitudePdp, 
                SUM(`enelPdp`) AS enelPdp, 
                SUM(`vivigasPdp`) AS vivigasPdp, 
                SUM(`polizzePdp`) AS polizzePdp,
                SUM(`irenPdp`) AS irenPdp
              FROM `obbiettivoTL` 
              WHERE 
                  `mese` >= '$meseMinore' 
                  AND `mese` <= '$meseMaggiore' 
                  AND `sede` = '$sede'  // Filtro per sede specifica
              GROUP BY `sede`";
    
    $result = $conn19->query($query);
    $data = array();
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");

$testMode = $_POST["testMode"];
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);
$arraySwitchOut = [];

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));
$dataMinoreSottoscrizione = $dataMinore;
$dataMaggioreSottoscrizione = $dataMaggiore;

$queryMandato = "";
$lunghezza = count($mandato);
$orepafSede = 0;
$pezzoOkPafSede = 0;
$pesoLordoSede = 0;
$pesoPdaOkSede = 0;
$pesoPdaKoSede = 0;
$pesoPdaBklSede = 0;
$pesoPdaBklpSede = 0;
$pezzoLordoSede = 0;
$pezzoOkSede = 0;
$pezzoKoSede = 0;
$pezzoBklSede = 0;
$pezzoBklpSede = 0;
$oreSede = 0;
$pesoPostOkSede = 0;
$pesoPostKoSede = 0;
$pesoPostBklSede = 0;
$pezzoPostOkSede = 0;
$pezzoPostKoSede = 0;
$pezzoPostBklSede = 0;
$pezzoBollettinoSede = 0;
$pezzoRidSede = 0;
$pezzoCartaceoSede = 0;
$pezzoMailSede = 0;
$pezzoLuceSede = 0;
$pezzoGasSede = 0;
$pezzoDualSede = 0;
$pezzoPolizzaSede = 0;
$oreSedeParziale = 0;
$dataSwoSede = 0;
$orepafTotale = 0;
$pezzoOkPafTotale = 0;
$pezzoBollettinoOKSede = 0;
$pezzoRidOKSede = 0;
$pezzoCartaceoOKSede = 0;
$pezzoMailOKSede = 0;

$deltaSwo1Sede = 0;
$deltaSwo3Sede = 0;
$deltaSwo6Sede = 0;
$deltaSwo9Sede = 0;
$deltaSwoEverSede = 0;

$giorniLavorativiPrevisti = 0;
$giorniLavoratiEffettivi = 0;

$pesoLordoTotale = 0;
$pesoPdaOkTotale = 0;
$pesoPdaKoTotale = 0;
$pesoPdaBklTotale = 0;
$pesoPdaBklpTotale = 0;
$pezzoLordoTotale = 0;
$pezzoOkTotale = 0;
$pezzoKoTotale = 0;
$pezzoBklTotale = 0;
$pezzoBklpTotale = 0;
$oreTotale = 0;
$pesoPostOkTotale = 0;
$pesoPostKoTotale = 0;
$pesoPostBklTotale = 0;
$pezzoPostOkTotale = 0;
$pezzoPostKoTotale = 0;
$pezzoPostBklTotale = 0;
$pezzoBollettinoTotale = 0;
$pezzoRidTotale = 0;
$pezzoCartaceoTotale = 0;
$pezzoMailTotale = 0;
$pezzoLuceTotale = 0;
$pezzoGasTotale = 0;
$pezzoDualTotale = 0;
$pezzoPolizzaTotale = 0;
$giornilavorati = 0;
$giornilavorativi = 0;
$pezzoBollettinoOKTotale = 0;
$pezzoRidOKTotale = 0;
$pezzoCartaceoOKTotale = 0;
$pezzoMailOKTotale = 0;
$dataSwoTotale = 0;
$meseCorrente = 0;
$deltaSwo1Totale = 0;
$deltaSwo3Totale = 0;
$deltaSwo6Totale = 0;
$deltaSwo9Totale = 0;
$deltaSwoEverTotale = 0;

$sedePrecedente = "";
$obbiettivoData = 0;
$idMandato = $mandato[0];
$totaleObj = 0;
$querySede = "";
$lunghezzaSede = count($sede);
$ObjSedeplus = 0;
$giornirimanenti = 0;
$totalepassogg = 0;
$passoGiornoSede = 0;
$giorniRimanenti = 0;
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
//$giornirimanenti = $result['giorni_rimanenti'];
$result = calcolaGiorniLavorativi($dataMinore, $dataMaggiore, $giorniFestiviPerMese, $dateDaEscludere);

echo "Giorni lavorabili previsti: " . $result['giorni_lavorabili_previsti'] . "<br>";
echo "Giorni lavorati effettivi: " . $result['giorni_lavorati_effettivi'] . "<br>";
echo "Giorni lavorativi rimanenti: " . $result['giorni_rimanenti'] . "<br>";

$giornirimanenti = $result['giorni_rimanenti'];
$giorniLavorativiPrevisti = $result['giorni_lavorabili_previsti'];
$giorniLavoratiEffettivi = $result[ 'giorni_lavorati_effettivi'];
//    foreach ($obbiettivoData as $row) {
//        $sedeObbiettivo = $row['sede'];
//        $ore = $row['ore'];
//        $plenitudePdp = $row['plenitudePdp'];
//        $enelPdp = $row['enelPdp'];
//    $vivigasPdp = $row['vivigasPdp'];
//    $polizzePdp = $row['polizzePdp'];
//
//}
$mandati_validi = ["Plenitude", "Vivigas Energia", "Enel", "Iren", "EnelIn", "Heracom"];

// Prima passata: raccolta valori medi
$valoriMedi = [];
foreach ($mandato as $idMandato) {
    // Salta i mandati speciali e quelli non validi
    if (in_array($idMandato, ["Union", "Vodafone", "Bo"]) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    // Converti $dataMinore e $dataMaggiore nel primo giorno del mese
    $meseMinore = date('Y-m-01', strtotime($dataMinore));
    $meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

    // Query per il valore medio in base al mandato
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
            break;
                      case "Tim":
            $queryvaloremedio = "SELECT mandato, mese, media 
                                FROM `mediaPraticaMese` 
                                WHERE mese >= '$meseMinore' AND mese <= '$meseMaggiore' 
                                AND mandato = 'Tim'
                                ORDER BY `id` DESC";
            break;
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

switch ($idMandato) {
    case "Plenitude":
        $queryCrm = "SELECT 
    p.sede, 
    a.tipoCampagna, 
    SUM(a.totalePesoLordo) AS totalePesoLordo,
    SUM(IF(a.fasePDA='OK', a.totalePesoLordo, 0)) AS pesoOK,
    SUM(IF(a.fasePDA='KO', a.totalePesoLordo, 0)) AS pesoKO,
    SUM(IF(a.fasePDA='BKL', a.totalePesoLordo, 0)) AS pesoBKL,
    SUM(IF(a.fasePDA='BKLP', a.totalePesoLordo, 0)) AS pesoBKLP,
    SUM(a.pezzoLordo) AS pezziTotali,
    SUM(IF(a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziOK,
    SUM(IF(a.fasePDA='KO', a.pezzoLordo, 0)) AS pezziKO,
    SUM(IF(a.fasePDA='BKL', a.pezzoLordo, 0)) AS pezziBKL,
    SUM(IF(a.fasePDA='BKLP', a.pezzoLordo, 0)) AS pezziBKLP,
    SUM(IF(a.fasePost='OK' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziPostOK,
    SUM(IF(a.fasePost='KO' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziPostKO,
    SUM(IF(a.fasePost='BKL' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziPostBKL,    
    SUM(IF(a.fasePost='OK', a.totalePesoLordo, 0)) AS pesoPostOK,
    SUM(IF(a.fasePost='KO', a.totalePesoLordo, 0)) AS pesoPostKO,
    SUM(IF(a.fasePost='BKL', a.totalePesoLordo, 0)) AS pesoPostBKL,
    SUM(IF(p.metodoPagamento='Bollettino Postale' AND p.tipoAcquisizione<>'Subentro' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziBollettinoNoSubentro,
    SUM(IF(p.metodoPagamento='RID' AND p.tipoAcquisizione<>'Subentro' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziRIDNoSubentro,
    SUM(IF(p.metodoInvio='Cartaceo', a.pezzoLordo, 0)) AS pezziCartaceo,
    SUM(IF(p.metodoInvio='Bollettaweb', a.pezzoLordo, 0)) AS pezziBollettaweb,
    SUM(IF(p.comodity='Luce', a.pezzoLordo, 0)) AS pezziLuce,
    SUM(IF(p.comodity='Gas', a.pezzoLordo, 0)) AS pezziGas,
    SUM(IF(p.comodity='Dual', a.pezzoLordo, 0)) AS pezziDual,
    SUM(IF(p.comodity='Polizza', a.pezzoLordo, 0)) AS pezziPolizza,
    SUM(IF(p.metodoPagamento='Bollettino Postale' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziBollettinoOK,
    SUM(IF(p.metodoPagamento='RID' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziRIDOK,
    SUM(IF(p.metodoInvio='Cartaceo' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziCartaceoOK,
    SUM(IF(p.metodoInvio='Bollettaweb' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziBollettawebOK,
    SUM(IF(p.dataSwitchOutLuce<>'0000-00-00' AND p.dataSwitchOutGas='0000-00-00', 1, 0)) AS switchLuceOnly,
    SUM(IF(p.dataSwitchOutLuce='0000-00-00' AND p.dataSwitchOutGas<>'0000-00-00', 1, 0)) AS switchGasOnly,
    SUM(IF(p.dataSwitchOutLuce<>'0000-00-00' AND p.dataSwitchOutGas<>'0000-00-00', 1, 0)) AS switchDual,
    SUM(IF(p.deltaMortalitaLuce=1, 1, 0)) AS deltaSwo1,
    0 AS placeholder1,
    SUM(IF(p.deltaMortalitaLuce>=2 AND p.deltaMortalitaLuce<=3, 1, 0)) AS deltaSwo3,
    SUM(IF(p.deltaMortalitaLuce>9, 1, 0)) AS deltaSwoEvere,
    SUM(IF(p.deltaMortalitaLuce>=4 AND p.deltaMortalitaLuce<=6, 1, 0)) AS deltaSwo6,
    0 AS placeholder2,
    SUM(IF(p.deltaMortalitaLuce>=7 AND p.deltaMortalitaLuce<=9, 1, 0)) AS deltaSwo9,
    0 AS placeholder3,
    
   (
        SELECT SUM(o2.plenitudePdp)
        FROM obbiettivoTL o2
        WHERE o2.sede = p.sede
        AND o2.mese <= DATE_FORMAT('$dataMaggiore', '%Y-%m-01')
        AND o2.mese >= DATE_FORMAT('$dataMinore', '%Y-%m-01')
    ) AS plenitude,
    (
        SELECT SUM(o2.polizzePdp)
        FROM obbiettivoTL o2
        WHERE o2.sede = p.sede
        AND o2.mese <= DATE_FORMAT('$dataMaggiore', '%Y-%m-01')
        AND o2.mese >= DATE_FORMAT('$dataMinore', '%Y-%m-01')
    ) AS polizze
    
FROM 
    plenitude p
INNER JOIN 
    aggiuntaPlenitude a ON p.id = a.id
WHERE 
    p.data <= '$dataMaggiore' 
    AND p.data >= '$dataMinore' 
    $querySede
    AND p.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
    AND p.comodity <> 'Polizza'
GROUP BY 
    p.sede";

//       echo $queryCrm;
        $queryMortalitaLuce = "SELECT sede, SUM(IF(dataSwitchOutLuce BETWEEN '$dataMinore'  AND '$dataMaggiore', 1, 0)) AS sommaDataSwitchOutLuce FROM plenitude GROUP BY sede";
//       echo $queryMortalitaLuce;
        $risultato = $conn19->query($queryMortalitaLuce);
        while ($riga = $risultato->fetch_array()) {
            $arraySwitchOut[$riga[0]] = $riga[1];
        }
        $queryMortalitaGas = "SELECT sede, SUM(IF(dataSwitchOutGas BETWEEN '$dataMinore'  AND '$dataMaggiore', 1, 0)) AS sommaDataSwitchOutGas FROM plenitude GROUP BY sede";
        $risultato = $conn19->query($queryMortalitaGas);
        while ($riga = $risultato->fetch_array()) {
            $arraySwitchOut[$riga[0]] += $riga[1];
        }
//echo $queryMortalitaLuce;

        
        break;

    case "Green Network":
        $queryCrm = "SELECT "
                . " sede,"
                . " tipoCampagna, "
                . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                . " 0,"
                . " 0,"
                . " 0, "
                . " 0,"
                . " 0,"
                . " 0, "
                . " 0,"
                . " 0,"
                . " 0, "
                . " 0,"
                . " 0 "
                . "FROM "
                . "green "
                . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                . "where data<='$dataMaggiore' and data>='$dataMinore' " . $querySede
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'"
                . " group by sede";
        break;
    
    case "Vivigas Energia":
        $queryCrm = "SELECT 
    v.sede, 
    a.tipoCampagna, 
    SUM(IF(v.data >= '$dataMinore' AND v.data <= '$dataMaggiore', a.totalePesoLordo, 0)) AS totalePesoLordo,
    SUM(IF(a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.totalePesoLordo, 0)) AS pesoOK,
    SUM(IF(a.fasePDA='KO' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.totalePesoLordo, 0)) AS pesoKO,
    SUM(IF(a.fasePDA='BKL' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.totalePesoLordo, 0)) AS pesoBKL,
    SUM(IF(a.fasePDA='BKLP' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.totalePesoLordo, 0)) AS pesoBKLP,
    SUM(IF(v.data >= '$dataMinore' AND v.data <= '$dataMaggiore', a.pezzoLordo, 0)) AS pezziTotali,
    SUM(IF(a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziOK,
    SUM(IF(a.fasePDA='KO' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziKO,
    SUM(IF(a.fasePDA='BKL' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziBKL,
    SUM(IF(a.fasePDA='BKLP' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziBKLP,
    SUM(IF(a.fasePost='OK' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziPostOK,
    SUM(IF(a.fasePost='KO' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziPostKO,
    SUM(IF(a.fasePost='BKL' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziPostBKL,    
    SUM(IF(a.fasePost='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.totalePesoLordo, 0)) AS pesoPostOK,
    SUM(IF(a.fasePost='KO' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.totalePesoLordo, 0)) AS pesoPostKO,
    SUM(IF(a.fasePost='BKL' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.totalePesoLordo, 0)) AS pesoPostBKL,
    SUM(IF(v.metodoPagamento='Bollettino' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziBollettinoOK,
    SUM(IF(v.metodoPagamento='SSD' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziSSDOK,
    SUM(IF(v.metodoInvio='Posta (Residenza)' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziPosta,
    SUM(IF(v.metodoInvio='Mail' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziMail,
    SUM(IF(v.comodity='Luce' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziLuce,
    SUM(IF(v.comodity='Gas' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziGas,
    SUM(IF(v.comodity='Dual' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziDual,
    SUM(IF(v.comodity='Polizza' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziPolizza,
    SUM(IF(v.metodoPagamento='Bollettino' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziBollettinoConfermati,
    SUM(IF(v.metodoPagamento='SSD' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziSSDConfermati,
    SUM(IF(v.metodoInvio='Posta (Residenza)' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziPostaConfermati,
    SUM(IF(v.metodoInvio='Mail' AND a.fasePDA='OK' AND v.datasottoscrizionecontratto >= '$dataMinoreSottoscrizione' AND v.datasottoscrizionecontratto <= '$dataMaggioreSottoscrizione', a.pezzoLordo, 0)) AS pezziMailConfermati,
    0 AS placeholder1,
    0 AS placeholder2,
    0 AS placeholder3,
    0 AS placeholder4,
    0 AS placeholder5,
    0 AS placeholder6,
    0 AS placeholder7,
    0 AS placeholder8,
    0 AS placeholder9,
    0 AS placeholder10,
    0 AS placeholder11,
    (
        SELECT SUM(o2.vivigasPdp)
        FROM obbiettivoTL o2
        WHERE o2.sede = v.sede
        AND o2.mese <= DATE_FORMAT('$dataMaggiore', '%Y-%m-01')
        AND o2.mese >= DATE_FORMAT('$dataMinore', '%Y-%m-01')
    ) AS vivigasPdp,
    0 AS polizzePdp
FROM 
    vivigas v
INNER JOIN 
    aggiuntaVivigas a ON v.id = a.id
WHERE 
    v.data <= '$dataMaggiore' 
    AND v.data >= '$dataMinore'
    $querySede
    AND v.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia')
GROUP BY 
    v.sede";
        
        
//      echo $queryCrm;
        break;
    case "Vodafone":
        $queryCrm = "SELECT "
                . " sede,"
                . " tipoCampagna, "
                . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                . " 0,"
                . " 0,"
                . " 0, "
                . " 0,"
                . " 0,"
                . " 0, "
                . " 0,"
                . " 0,"
                . " 0, "
                . " 0,"
                . " 0 "
                . "FROM "
                . "vodafone "
                . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
                . "where data<='$dataMaggiore' and data>='$dataMinore' " . $querySede
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'"
                . " group by sede";
        break;
    case "enel_out":
        $queryCrm = "SELECT "
                . " sede, "
                . " tipoCampagna, "
                . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                . "FROM "
                . "enelOut "
                . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                . "where data<='$dataMaggiore' and data>='$dataMinore' " . $querySede
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'"
                . " group by sede";
        break;
    case "Iren":
        $queryCrm = "SELECT 
    sede, 
    tipoCampagna,
    SUM(totalePesoLordo),
    SUM(IF(fasePDA='OK', totalePesoLordo, 0)),
    SUM(IF(fasePDA='KO', totalePesoLordo, 0)), 
    SUM(IF(fasePDA='BKL', totalePesoLordo, 0)), 
    SUM(IF(fasePDA='BKLP', totalePesoLordo, 0)),
    SUM(pezzoLordo),
    SUM(IF(fasePDA='OK', pezzoLordo, 0)),
    SUM(IF(fasePDA='KO', pezzoLordo, 0)), 
    SUM(IF(fasePDA='BKL', pezzoLordo, 0)), 
    SUM(IF(fasePDA='BKLP', pezzoLordo, 0)),
    SUM(IF(fasePost='OK', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(fasePost='KO', IF(fasePDA='OK', pezzoLordo, 0), 0)), 
    SUM(IF(fasePost='BKL', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(fasePost='OK', totalePesoLordo, 0)),
    SUM(IF(fasePost='KO', totalePesoLordo, 0)), 
    SUM(IF(fasePost='BKL', totalePesoLordo, 0)),
    SUM(IF(metodoPagamento='Bollettino Postale', pezzoLordo, 0)),
    SUM(IF(metodoPagamento='RID', pezzoLordo, 0)),
    SUM(IF(metodoInvio='Cartaceo', pezzoLordo, 0)),
    SUM(IF(metodoInvio='Bollettaweb', pezzoLordo, 0)),
    SUM(IF(comodity='Luce', pezzoLordo, 0)),
    SUM(IF(comodity='Gas', pezzoLordo, 0)),
    SUM(IF(comodity='Dual', pezzoLordo, 0)),
    SUM(IF(comodity='Polizza', pezzoLordo, 0)),
    SUM(IF(metodoPagamento='Bollettino Postale', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(metodoPagamento='RID', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(metodoInvio='Cartaceo', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(metodoInvio='Bollettaweb', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    (SELECT SUM(o2.vivigasPdp)
     FROM obbiettivoTL o2
     WHERE o2.sede = iren.sede
     AND o2.mese <= DATE_FORMAT('$dataMaggiore', '%Y-%m-01')
     AND o2.mese >= DATE_FORMAT('$dataMinore', '%Y-%m-01')) AS vivigasPdp,
    0 AS polizzePdp
FROM 
    iren 
    INNER JOIN aggiuntaIren ON iren.id = aggiuntaIren.id 
WHERE 
    data <= '$dataMaggiore' 
    AND data >= '$dataMinore'  
    $querySede
    AND statoPda <> 'bozza' 
    AND statoPda <> 'annullata' 
    AND statoPda <> 'pratica doppia' 
    AND statoPda <> 'In attesa Sblocco' 
    AND comodity <> 'Polizza'
GROUP BY 
    sede";
        break;

    case "Union":
        $queryCrm = "SELECT "
                . " sede,"
                . " tipoCampagna, "
                . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                . " sum(if(metodoPagamento='Bollettino Postale',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoPagamento='RID',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoInvio='Cartaceo',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoInvio='Bollettaweb',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " 0,"
                . " 0, "
                . " 0, "
                . " 0,"
                . " 0,"
                . " 0, "
                . " 0,"
                . " 0,"
                . " 0, "
                . " 0,"
                . " 0 "
                . "FROM "
                . "know.union "
                . "inner JOIN aggiuntaUnion on know.union.id=aggiuntaUnion.id "
                . "where data<='$dataMaggiore' and data>='$dataMinore'  " . $querySede
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza'"
                . " group by sede";
        break;
     case "Enel":
        $queryCrm = "SELECT 
    p.sede, 
    a.tipoCampagna, 
    SUM(a.totalePesoLordo) AS totalePesoLordo,
    SUM(IF(a.fasePDA='OK', a.totalePesoLordo, 0)) AS pesoOK,
    SUM(IF(a.fasePDA='KO', a.totalePesoLordo, 0)) AS pesoKO,
    SUM(IF(a.fasePDA='BKL', a.totalePesoLordo, 0)) AS pesoBKL,
    SUM(IF(a.fasePDA='BKLP', a.totalePesoLordo, 0)) AS pesoBKLP,
    SUM(a.pezzoLordo) AS pezziTotali,
    SUM(IF(a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziOK,
    SUM(IF(a.fasePDA='KO', a.pezzoLordo, 0)) AS pezziKO,
    SUM(IF(a.fasePDA='BKL', a.pezzoLordo, 0)) AS pezziBKL,
    SUM(IF(a.fasePDA='BKLP', a.pezzoLordo, 0)) AS pezziBKLP,
    SUM(IF(a.fasePost='OK' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziPostOK,
    SUM(IF(a.fasePost='KO' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziPostKO,
    SUM(IF(a.fasePost='BKL' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziPostBKL,
    SUM(IF(a.fasePost='OK', a.totalePesoLordo, 0)) AS pesoPostOK,
    SUM(IF(a.fasePost='KO', a.totalePesoLordo, 0)) AS pesoPostKO,
    SUM(IF(a.fasePost='BKL', a.totalePesoLordo, 0)) AS pesoPostBKL,
    SUM(IF(p.metodoPagamento='Bolletta' AND p.tipoAcquisizione<>'Subentro' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziBollettaNoSubentro,
    SUM(IF(p.metodoPagamento='RID' AND p.tipoAcquisizione<>'Subentro' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziRIDNoSubentro,
    SUM(IF(p.metodoInvio='Cartaceo', a.pezzoLordo, 0)) AS pezziCartaceo,
    SUM(IF(p.metodoInvio='Bollettaweb', a.pezzoLordo, 0)) AS pezziBollettaweb,
    SUM(IF(p.comodity='Luce', a.pezzoLordo, 0)) AS pezziLuce,
    SUM(IF(p.comodity='Gas', a.pezzoLordo, 0)) AS pezziGas,
    SUM(IF(p.comodity='Dual', a.pezzoLordo, 0)) AS pezziDual,
    SUM(IF(p.metodoPagamento='Bolletta' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziBollettaOK,
    SUM(IF(p.metodoPagamento='RID' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziRIDOK,
    SUM(IF(p.metodoInvio='Cartaceo' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziCartaceoOK,
    SUM(IF(p.metodoInvio='Bollettaweb' AND a.fasePDA='OK', a.pezzoLordo, 0)) AS pezziBollettawebOK,
    SUM(IF(p.dataSwitchOutLuce<>'0000-00-00' AND p.dataSwitchOutGas='0000-00-00', 1, 0)) AS switchLuceOnly,
    SUM(IF(p.dataSwitchOutLuce='0000-00-00' AND p.dataSwitchOutGas<>'0000-00-00', 1, 0)) AS switchGasOnly,
    SUM(IF(p.dataSwitchOutLuce<>'0000-00-00' AND p.dataSwitchOutGas<>'0000-00-00', 1, 0)) AS switchDual,
    SUM(IF(p.deltaMortalitaLuce=1, 1, 0)) AS deltaSwo1,
    0 AS placeholder1,
    SUM(IF(p.deltaMortalitaLuce>=2 AND p.deltaMortalitaLuce<=3, 1, 0)) AS deltaSwo3,
    SUM(IF(p.deltaMortalitaLuce>9, 1, 0)) AS deltaSwoEvere,
    SUM(IF(p.deltaMortalitaLuce>=4 AND p.deltaMortalitaLuce<=6, 1, 0)) AS deltaSwo6,
    0 AS placeholder2,
    SUM(IF(p.deltaMortalitaLuce>=7 AND p.deltaMortalitaLuce<=9, 1, 0)) AS deltaSwo9,
    0 AS placeholder3,
    0 AS placeholder4,
    (
        SELECT SUM(o.enelPdp)
        FROM obbiettivoTL o
        WHERE o.sede = p.sede
        AND o.mese <= DATE_FORMAT('$dataMaggiore', '%Y-%m-01')
        AND o.mese >= DATE_FORMAT('$dataMinore', '%Y-%m-01')
    ) AS enelPdp,
    0 AS polizzePdp
FROM 
    enel p
INNER JOIN 
    aggiuntaEnel a ON p.id = a.id
WHERE 
    p.data <= '$dataMaggiore' 
    AND p.data >= '$dataMinore'
    $querySede
    AND p.statoPda NOT IN ('bozza', 'annullata', 'pratica doppia')
    AND p.comodity <> 'Fibra'
GROUP BY 
    p.sede";
//
//        echo $queryCrm;
        $queryMortalitaLuce = "SELECT sede, SUM(IF(dataSwitchOutLuce BETWEEN '$dataMinore'  AND '$dataMaggiore', 1, 0)) AS sommaDataSwitchOutLuce FROM enel GROUP BY sede";
        $risultato = $conn19->query($queryMortalitaLuce);
        while ($riga = $risultato->fetch_array()) {
            $arraySwitchOut[$riga[0]] = $riga[1];
        }
        $queryMortalitaGas = "SELECT sede, SUM(IF(dataSwitchOutGas BETWEEN '$dataMinore'  AND '$dataMaggiore', 1, 0)) AS sommaDataSwitchOutGas FROM enel GROUP BY sede";
        $risultato = $conn19->query($queryMortalitaGas);
        while ($riga = $risultato->fetch_array()) {
            $arraySwitchOut[$riga[0]] += $riga[1];
        }

        break;
case "Heracom":
            $queryCrm = "SELECT 
        'Lamezia' as sede, 
        tipoCampagna,
        SUM(totalePesoLordo),
        SUM(IF(fasePDA='OK', totalePesoLordo, 0)),
        SUM(IF(fasePDA='KO', totalePesoLordo, 0)), 
        SUM(IF(fasePDA='BKL', totalePesoLordo, 0)), 
        SUM(IF(fasePDA='BKLP', totalePesoLordo, 0)),
        SUM(pezzoLordo),
        SUM(IF(fasePDA='OK', pezzoLordo, 0)),
        SUM(IF(fasePDA='KO', pezzoLordo, 0)), 
        SUM(IF(fasePDA='BKL', pezzoLordo, 0)), 
        SUM(IF(fasePDA='BKLP', pezzoLordo, 0)),
        SUM(IF(fasePost='OK', IF(fasePDA='OK', pezzoLordo, 0), 0)),
        SUM(IF(fasePost='KO', IF(fasePDA='OK', pezzoLordo, 0), 0)), 
        SUM(IF(fasePost='BKL', IF(fasePDA='OK', pezzoLordo, 0), 0)),
        SUM(IF(fasePost='OK', totalePesoLordo, 0)),
        SUM(IF(fasePost='KO', totalePesoLordo, 0)), 
        SUM(IF(fasePost='BKL', totalePesoLordo, 0)),
        SUM(IF(metodoPagamento='Bollettino Postale', pezzoLordo, 0)),
        SUM(IF(metodoPagamento='RID', pezzoLordo, 0)),
        SUM(IF(metodoInvio='Cartaceo', pezzoLordo, 0)),
        SUM(IF(metodoInvio='Bollettaweb', pezzoLordo, 0)),
        SUM(IF(comodity='Luce', pezzoLordo, 0)),
        SUM(IF(comodity='Gas', pezzoLordo, 0)),
        SUM(IF(comodity='Dual', pezzoLordo, 0)),
        SUM(IF(comodity='Consenso', pezzoLordo, 0)),
        SUM(IF(metodoPagamento='Bollettino Postale', IF(fasePDA='OK', pezzoLordo, 0), 0)),
        SUM(IF(metodoPagamento='RID', IF(fasePDA='OK', pezzoLordo, 0), 0)),
        SUM(IF(metodoInvio='Cartaceo', IF(fasePDA='OK', pezzoLordo, 0), 0)),
        SUM(IF(metodoInvio='Bollettaweb', IF(fasePDA='OK', pezzoLordo, 0), 0)),
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
    (
        SELECT SUM(o.heracomPdp)
        FROM obbiettivoTL o
        WHERE o.sede = heracom.sede
        AND o.mese <= DATE_FORMAT('$dataMaggiore', '%Y-%m-01')
        AND o.mese >= DATE_FORMAT('$dataMinore', '%Y-%m-01')

    ) AS hearcomPdp,
    0 AS polizzePdp
    FROM 
        heracom 
        INNER JOIN aggiuntaHeracom ON heracom.id = aggiuntaHeracom.id 
    WHERE 
        data <= '$dataMaggiore' 
        AND data >= '$dataMinore'  

        AND statoPda <> 'bozza' 

        AND statoPda <> 'pratica doppia' 
        AND statoPda <> 'In attesa Sblocco' 
        AND comodity <> 'Consenso'
    GROUP BY 
        sede";
    
//    echo $queryCrm;
    
        break;
    
    echo $queryCrm;
////      
//
        break;
case "EnelIn":
        $queryCrm = "SELECT 
    'Rende' as sede, 
    tipoCampagna,
    SUM(totalePesoLordo),
    SUM(IF(fasePDA='OK', totalePesoLordo, 0)),
    SUM(IF(fasePDA='KO', totalePesoLordo, 0)), 
    SUM(IF(fasePDA='BKL', totalePesoLordo, 0)), 
    SUM(IF(fasePDA='BKLP', totalePesoLordo, 0)),
    SUM(pezzoLordo),
    SUM(IF(fasePDA='OK', pezzoLordo, 0)),
    SUM(IF(fasePDA='KO', pezzoLordo, 0)), 
    SUM(IF(fasePDA='BKL', pezzoLordo, 0)), 
    SUM(IF(fasePDA='BKLP', pezzoLordo, 0)),
    SUM(IF(fasePost='OK', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(fasePost='KO', IF(fasePDA='OK', pezzoLordo, 0), 0)), 
    SUM(IF(fasePost='BKL', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(fasePost='OK', totalePesoLordo, 0)),
    SUM(IF(fasePost='KO', totalePesoLordo, 0)), 
    SUM(IF(fasePost='BKL', totalePesoLordo, 0)),
    SUM(IF(metodoPagamento='Bollettino Postale', pezzoLordo, 0)),
    SUM(IF(metodoPagamento='RID', pezzoLordo, 0)),
    SUM(IF(metodoInvio='Cartaceo', pezzoLordo, 0)),
    SUM(IF(metodoInvio='Bollettaweb', pezzoLordo, 0)),
    SUM(IF(comodity='Luce', pezzoLordo, 0)),
    SUM(IF(comodity='Gas', pezzoLordo, 0)),
    SUM(IF(comodity='Dual', pezzoLordo, 0)),
    SUM(IF(comodity='Fibra', pezzoLordo, 0)),
    SUM(IF(metodoPagamento='Bollettino Postale', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(metodoPagamento='RID', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(metodoInvio='Cartaceo', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    SUM(IF(metodoInvio='Bollettaweb', IF(fasePDA='OK', pezzoLordo, 0), 0)),
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    (
        SELECT SUM(o.enelInPdp)
        FROM obbiettivoTL o
        WHERE o.sede = enelIn.sede
        AND o.mese <= DATE_FORMAT('$dataMaggiore', '%Y-%m-01')
        AND o.mese >= DATE_FORMAT('$dataMinore', '%Y-%m-01')
        AND o.tipo = 'CTC' 
    ) AS enelInPdp,
    0 AS polizzePdp
FROM 
    enelIn 
    INNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id 
WHERE 
    data <= '$dataMaggiore' 
    AND data >= '$dataMinore'  
    $querySede
    AND statoPda <> 'bozza' 
    AND statoPda <> 'annullata' 
    AND statoPda <> 'pratica doppia' 
    AND statoPda <> 'In attesa Sblocco' 
    AND comodity <> 'Fibra Enel'
GROUP BY 
    sede";
       
//    echo $queryCrm;
}
//
//function calcolaGiorniLavorativi($dataInizio, $dataFine) {
//    $giorniLavorativiPrevisti = 0;
//    $giorniLavoratiEffettivi = 0;
//
//    // Converti le date in oggetti DateTime
//    $dataInizio = new DateTime($dataInizio);
//    $dataFine = new DateTime($dataFine);
//
//    // Itera attraverso ogni giorno nel range di date
//    $interval = new DateInterval('P1D');
//    $period = new DatePeriod($dataInizio, $interval, $dataFine->modify('+1 day'));
//
//    foreach ($period as $giorno) {
//        $giornoSettimana = $giorno->format('N'); // 1 (Lunedì) - 7 (Domenica)
//
//        // Calcola i giorni lavorativi previsti
//        if ($giornoSettimana >= 1 && $giornoSettimana <= 5) {
//            $giorniLavorativiPrevisti += 1;
//        } elseif ($giornoSettimana == 6) {
//            $giorniLavorativiPrevisti += 0.5;
//        }
//
//        // Calcola i giorni lavorati effettivi (supponiamo che tutti i giorni lavorativi siano lavorati)
//        // Puoi modificare questa parte per includere una logica specifica sui giorni effettivamente lavorati
//        if ($giornoSettimana >= 1 && $giornoSettimana <= 5) {
//            $giorniLavoratiEffettivi += 1;
//        } elseif ($giornoSettimana == 6) {
//            $giorniLavoratiEffettivi += 0.5;
//        }
//    }
//
//    return [
//        'giorni_lavorabili_previsti' => $giorniLavorativiPrevisti,
//        'giorni_lavorati_effettivi' => $giorniLavoratiEffettivi
//    ];
//}
//
//// Esempio di utilizzo
//$dataInizio = $dataMinore;
//$dataFine = $dataMaggiore;
//
//$risultato = calcolaGiorniLavorativi($dataInizio, $dataFine);
//
//echo "Giorni lavorabili previsti: " . $risultato['giorni_lavorabili_previsti'] . "<br>";
//echo "Giorni lavorati effettivi: " . $risultato['giorni_lavorati_effettivi'] . "<br>";

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabella.php";
//echo $queryCrm;
$risultatoCrm = $conn19->query($queryCrm);
while ($rigaCRM = $risultatoCrm->fetch_array()) {
    //echo var_dump($rigaCRM);
    $sede = $rigaCRM["sede"];
    $sedeRicerca = ucwords($sede);
    $descrizioneMandato = $rigaCRM[1];
    //$valoreSwicthOut = $arraySwitchOut[$sede];

switch ($idMandato) {
    case "Plenitude":
    case "Vivigas Energia":
    case "Iren":
    case "Enel":
        $queryGroupMandato = "SELECT "
            . "SUM(numero)/3600 AS ore "
            . "FROM `stringheTotale` "
            . "WHERE giorno >= '$dataMinore' "
            . "AND giorno <= '$dataMaggiore' "
            . "AND livello <= 6 "
            . "AND sede = '$sede' "
            . "AND idMandato = '$idMandato' "
            . "GROUP BY sede";
        break;
    
    case "EnelIn":
        $queryGroupMandato = "SELECT SUM(`oreDichiarate`) AS ore "
            . "FROM `oreEnelIn` "
            . "WHERE data >= '$dataMinore' "
            . "AND data <= '$dataMaggiore' "
            . "AND sede = '$sede' "
            . "AND mandato = '$idMandato'";
        break;
    
    
   echo $queryGroupMandato;
    
    
    case "Heracom":
        $queryGroupMandato = "SELECT SUM(`numero`)/3600 AS ore "
            . "FROM `stringheSiscallLeadTC` "
            . "WHERE giorno >= '$dataMinore' "
            . "AND giorno <= '$dataMaggiore' "
           . "AND sede = '$sede' "
            . "AND mandato = '$idMandato'"
            . "AND userGroup = 'OP_Lam_piannazzo'";
        break;
    
    case "TIMmq":
        $queryGroupMandato = "SELECT SUM(`oreDichiarate`) AS ore "
            . "FROM `oreTIMmq` "
            . "WHERE data >= '$dataMinore' "
            . "AND data <= '$dataMaggiore' "
           . "AND sede = '$sede' "
            . "AND mandato = '$idMandato'";
        break;
    
    default:
 
        break;
}

// Esecuzione della query e recupero dei risultati
if (!empty($queryGroupMandato)) {
    $risultaOre = $conn19->query($queryGroupMandato);
    if ($risultaOre && $risultaOre->num_rows > 0) {
        $rigaOre = $risultaOre->fetch_array();
        $ore = $rigaOre[0];
    } else {
        $ore = 0;
    }
} else {
    $ore = 0; // Se il mandato non è valido o la query è vuota
}
    $pesoLordo = round($rigaCRM[2], 2);
    $pesoPdaOk = round($rigaCRM[3], 2);
    $pesoPdaKo = round($rigaCRM[4], 2);
    $pesoPdaBkl = round($rigaCRM[5], 2);
    $pesoPdaBklp = round($rigaCRM[6], 2);
    $pezzoLordo = round($rigaCRM[7], 0);
    $pezzoOk = round($rigaCRM[8], 0);
    $pezzoKo = round($rigaCRM[9], 0);
    $pezzoBkl = round($rigaCRM[10], 0);
    $pezzoBklp = round($rigaCRM[11], 0);
    $resaPezzoLordo = ($ore == 0) ? 0 : round($pezzoLordo / $ore, 2);
    $resaValoreLordo = ($ore == 0) ? 0 : round($pesoLordo / $ore, 2);
    $resaPezzoOk = ($ore == 0) ? 0 : round($pezzoOk / $ore, 2);
    $resaValoreOk = ($ore == 0) ? 0 : round($pesoPdaOk / $ore, 2);
    $pesoPostOk = 0;
    $pesoPostKo = 0;
    $pesoPostBkl = 0;
    $pezzoPostOk = round($rigaCRM[12], 0);
    $pezzoPostKo = round($rigaCRM[13], 0);
    $pezzoPostBkl = round($rigaCRM[14], 0);
    $resaPostOK = ($pezzoOk == 0) ? 0 : round(($pezzoPostOk / $pezzoOk) * 100, 2);
    $resaPostKO = ($pezzoOk == 0) ? 0 : round(($pezzoPostKo / $pezzoOk) * 100, 2);
    $resaPostBkl = ($pezzoOk == 0) ? 0 : round(($pezzoPostBkl / $pezzoOk) * 100, 2);
    $pezzoBollettino = round($rigaCRM[18], 0);
    $pezzoRid = round($rigaCRM[19], 0);

    $percentualeBollettino = (($pezzoBollettino + $pezzoRid) == 0) ? 0 : round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);

    $pezzoCartaceo = round($rigaCRM[20], 0);
    $pezzoMail = round($rigaCRM[21], 0);

    $percentualeInvio = (($pezzoCartaceo + $pezzoMail) == 0) ? 0 : round(($pezzoMail / ($pezzoCartaceo + $pezzoMail)) * 100, 2);

    $pezzoLuce = round($rigaCRM[22], 0);
    $pezzoGas = round($rigaCRM[23], 0);
    $pezzoDual = round($rigaCRM[24], 0);
    $pezzoPolizza = round($rigaCRM[25], 0);

    $pezzoBollettinoOK = round($rigaCRM[26], 0);
    $pezzoRidOK = round($rigaCRM[27], 0);

    $percentualeBollettinoOK = (($pezzoBollettinoOK + $pezzoRidOK) == 0) ? 0 : round((($pezzoBollettinoOK / ($pezzoBollettinoOK + $pezzoRidOK)) * 100), 2);

    $pezzoCartaceoOK = round($rigaCRM[28], 0);
    $pezzoMailOK = round($rigaCRM[29], 0);

    $percentualeInvioOK = (($pezzoCartaceoOK + $pezzoMailOK) == 0) ? 0 : round(($pezzoMailOK / ($pezzoCartaceoOK + $pezzoMailOK)) * 100, 2);

    $dataSWOLuce = $rigaCRM[30];
    $dataSWOGas = $rigaCRM[31];
    $dataSWODual = $rigaCRM[32];
    $deltaSwo1 = $rigaCRM[33];
    
    $deltaSwo3 = $rigaCRM[35];
    $deltaSwoEver = $rigaCRM[36];
    $deltaSwo6 = $rigaCRM[37];
    
    $deltaSwo9 = $rigaCRM[39];
    

    $dataSwo = $dataSWOLuce + $dataSWOGas + $dataSWODual;
    $percentualeDataSwo = ($pezzoPostOk == 0) ? 0 : (round(($dataSwo / $pezzoPostOk) * 100, 2));
    
    $objsede = $rigaCRM[41];
// $pezzoCartaceoOK
//$pezzoMailOK
// Calcolo di orepaf e pezzoOkPaf
// Calcolo di orepaf e pezzoOkPaf
// Calcolo di orepaf e pezzoOkPaf
if ($giorniLavoratiEffettivi > 0 && is_numeric($ore) && is_numeric($giorniLavorativiPrevisti)) {
    $orepaf = round((round($ore, 2) / $giorniLavoratiEffettivi) * $giorniLavorativiPrevisti, 2);
} else {
    $orepaf = 0; // Se non ci sono giorni lavorati o i valori non sono validi, le ore previste devono essere 0
}

if ($giorniLavoratiEffettivi > 0 && is_numeric($pezzoOk) && is_numeric($giorniLavorativiPrevisti)) {
    $pezzoOkPaf = round((round($pezzoOk) / $giorniLavoratiEffettivi) * $giorniLavorativiPrevisti);
} else {
    $pezzoOkPaf = 0; // Se non ci sono giorni lavorati o i valori non sono validi, i pezzi OK previsti devono essere 0
}
//echo $giorniLavoratiEffettivi;
//echo $giorniLavorativiPrevisti;
//echo  $pezzoOkPaf ;

$resaPezzoOkPf = ($orepaf == 0) ? 0 : round($pezzoOkPaf / $orepaf, 2);


// Controllo se ci sono giorni rimanenti e se l'obiettivo è valido
if ($giornirimanenti > 0 && isset($objsede) && is_numeric($objsede)) {
    $passoGiorno = ($objsede - $pezzoOk) / $giornirimanenti;
    
    // Se abbiamo già superato l'obiettivo, impostiamo il passo a 0
    if ($passoGiorno < 0) {
        $passoGiorno = 0;
    }
} else {
    // Se non ci sono giorni rimanenti o obiettivo non valido, passo = 0
    $passoGiorno = 0;
}

// Arrotondamento a 2 decimali per una lettura più pulita
$passoGiorno = round($passoGiorno, 2);

$differenzaObj = ($objsede == 0) ? 0 : ($pezzoOk - $objsede);




$fatturato = ($valoriMedi[$idMandato] ?? 0) * $pezzoOk;


$ricavoOrario = $ore != 0 ? round($fatturato / $ore, 0) : 0;

    $html .= "<tr>";
    $html .= "<td >$idMandato</td>";
    $html .= "<td >$sede</td>";

    $html .= "<td >-</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoLordo</td>";
    $html .= "<td>$pesoLordo</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoOk</td>";
    $html .= "<td>$pesoPdaOk</td>";
    

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoKo</td>";
    $html .= "<td>$pesoPdaKo</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBkl</td>";
    $html .= "<td>$pesoPdaBkl</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBklp</td>";
    $html .= "<td>$pesoPdaBklp</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>" . round($ore, 2) . "</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$resaPezzoLordo</td>";
    $html .= "<td>$resaValoreLordo</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$resaPezzoOk</td>";
    $html .= "<td>$resaValoreOk</td>";
    $html .= "<td style='white-space: nowrap;'>" . round($ricavoOrario) . " €</td>";
     
    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBollettino</td>";
    $html .= "<td>$pezzoRid</td>";
    $html .= "<td>$percentualeBollettino</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBollettinoOK</td>";
    $html .= "<td>$pezzoRidOK</td>";
    $html .= "<td>$percentualeBollettinoOK</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$objsede</td>";
    $html .= "<td>" . round($passoGiorno) . "</td>";
    $html .= "<td>" . round($differenzaObj) . "</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$orepaf</td>";
    $html .= "<td>$pezzoOkPaf</td>";
    $html .= "<td>$resaPezzoOkPf</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoLuce</td>";
    $html .= "<td>$pezzoGas</td>";
    $html .= "<td>$pezzoDual</td>";
    $html .= "<td>$pezzoPolizza</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoPostOk</td>";
    $html .= "<td>$resaPostOK</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoPostKo</td>";
    $html .= "<td>$resaPostKO</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoPostBkl</td>";
    $html .= "<td>$resaPostBkl</td>";
    $html .= "<td style = 'border-left: 2px solid lightslategray'>$dataSwo</td>";
    $html .= "<td>$percentualeDataSwo</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$deltaSwo1</td>";
    $html .= "<td>$deltaSwo3</td>";
    $html .= "<td>$deltaSwo6</td>";
    $html .= "<td>$deltaSwo9</td>";
    $html .= "<td>$deltaSwoEver</td>";

    $html .= "</tr>";

    $pesoLordoSede += $pesoLordo;
    $pesoPdaOkSede += $pesoPdaOk;
    $pesoPdaKoSede += $pesoPdaKo;
    $pesoPdaBklSede += $pesoPdaBkl;
    $pesoPdaBklpSede += $pesoPdaBklp;
    $pezzoLordoSede += $pezzoLordo;
    $pezzoOkSede += $pezzoOk;
    $pezzoKoSede += $pezzoKo;
    $pezzoBklSede += $pezzoBkl;
    $pezzoBklpSede += $pezzoBklp;
    $oreSedeParziale += round($ore, 2);
    $pesoPostOkSede += $pesoPostOk;
    $pesoPostKoSede += $pesoPostKo;
    $pesoPostBklSede += $pesoPostBkl;
    $pezzoPostOkSede += $pezzoPostOk;
    $pezzoPostKoSede += $pezzoPostKo;
    $pezzoPostBklSede += $pezzoPostBkl;
    $pezzoBollettinoSede += $pezzoBollettino;
    $pezzoRidSede += $pezzoRid;
    $pezzoCartaceoSede += $pezzoCartaceo;
    $pezzoMailSede += $pezzoMail;
    $pezzoLuceSede += $pezzoLuce;
    $pezzoGasSede += $pezzoGas;
    $pezzoDualSede += $pezzoDual;
    $pezzoPolizzaSede += $pezzoPolizza;
    $sedePrecedente = $sede;
    $pezzoOkPafSede +=$pezzoOkPaf;
    $pezzoBollettinoOKSede += $pezzoBollettinoOK;
    $pezzoRidOKSede += $pezzoRidOK;
    $pezzoCartaceoOKSede += $pezzoCartaceoOK;
    $pezzoMailOKSede += $pezzoMailOK;
    $oreSede += $ore;
    $orepafSede += $orepaf;
    $dataSwoSede += $dataSwo;
    $deltaSwo1Sede += $deltaSwo1;
    $deltaSwo3Sede += $deltaSwo3;
    $deltaSwo6Sede += $deltaSwo6;
    $deltaSwo9Sede += $deltaSwo9;
    $deltaSwoEverSede += $deltaSwoEver;
    $ObjSedeplus += $objsede;
    $passoGiornoSede += $passoGiorno;
    
    
    
}

$pesoLordoTotale += $pesoLordoSede;
$pesoPdaOkTotale += $pesoPdaOkSede;
$pesoPdaKoTotale += $pesoPdaKoSede;
$pesoPdaBklTotale += $pesoPdaBklSede;
$pesoPdaBklpTotale += $pesoPdaBklpSede;
$pezzoLordoTotale += $pezzoLordoSede;
$pezzoOkTotale += $pezzoOkSede;
$pezzoKoTotale += $pezzoKoSede;
$pezzoBklTotale += $pezzoBklSede;
$pezzoBklpTotale += $pezzoBklpSede;
$oreTotale += round($oreSede, 2);
$pesoPostOkTotale += $pesoPostOkSede;
$pesoPostKoTotale += $pesoPostKoSede;
$pesoPostBklTotale += $pesoPostBklSede;
$pezzoPostOkTotale += $pezzoPostOkSede;
$pezzoPostKoTotale += $pezzoPostKoSede;
$pezzoPostBklTotale += $pezzoPostBklSede;
$pezzoBollettinoTotale += $pezzoBollettinoSede;
$pezzoRidTotale += $pezzoRidSede;
$pezzoCartaceoTotale += $pezzoCartaceoSede;
$pezzoMailTotale += $pezzoMailSede;
$pezzoLuceTotale += $pezzoLuceSede;
$pezzoGasTotale += $pezzoGasSede;
$pezzoDualTotale += $pezzoDualSede;
$pezzoOkPafTotale += $pezzoOkPafSede;
$orepafTotale +=$orepafSede;
$pezzoBollettinoOKTotale += $pezzoBollettinoOKSede;
$pezzoRidOKTotale += $pezzoRidOKSede;
$pezzoCartaceoOKTotale += $pezzoCartaceoOKSede;
$pezzoMailOKTotale += $pezzoMailOKSede;
$dataSwoTotale += $dataSwoSede;

$deltaSwo1Totale += $deltaSwo1Sede;
$deltaSwo3Totale += $deltaSwo3Sede;
$deltaSwo6Totale += $deltaSwo6Sede;
$deltaSwo9Totale += $deltaSwo9Sede;
$deltaSwoEverTotale += $deltaSwoEverSede;

$totalepassogg += $passoGiornoSede;
$totaleObj += $ObjSedeplus;

$differentaObjTot = ($totaleObj == 0) ? 0 : ($pezzoOkTotale - $totaleObj);



$resapezzoOkPafTotale = ($orepafTotale == 0) ? 0 : round($pezzoOkPafTotale / $orepafTotale, 2);
$resaPezzoLordoTotale = ($oreTotale == 0) ? 0 : round($pezzoLordoTotale / $oreTotale, 2);
$resaValoreLordoTotale = ($oreTotale == 0) ? 0 : round($pesoLordoTotale / $oreTotale, 2);
$resaPezzoOkTotale = ($oreTotale == 0) ? 0 : round($pezzoOkTotale / $oreTotale, 2);
$resaValoreOkTotale = ($oreTotale == 0) ? 0 : round($pesoPdaOkTotale / $oreTotale, 2);
$resaPostOKTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostOkTotale / $pezzoOkTotale) * 100, 2);
$resaPostKOTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostKoTotale / $pezzoOkTotale) * 100, 2);
$resaPostBklTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostBklTotale / $pezzoOkTotale) * 100, 2);
$percentualeBollettinoTotale = (($pezzoBollettinoTotale + $pezzoRidTotale) == 0) ? 0 : round((($pezzoBollettinoTotale / ($pezzoBollettinoTotale + $pezzoRidTotale)) * 100), 2);
$percentualeInvioTotale = (($pezzoCartaceoTotale + $pezzoMailTotale) == 0) ? 0 : round(($pezzoMailTotale / ($pezzoCartaceoTotale + $pezzoMailTotale)) * 100, 2);

$percentualeBollettinoOKTotale = (($pezzoBollettinoOKTotale + $pezzoRidOKTotale) == 0) ? 0 : round((($pezzoBollettinoOKTotale / ($pezzoBollettinoOKTotale + $pezzoRidOKTotale)) * 100), 2);
$percentualeInvioOKTotale = (($pezzoCartaceoOKTotale + $pezzoMailOKTotale) == 0) ? 0 : round(($pezzoMailOKTotale / ($pezzoCartaceoOKTotale + $pezzoMailOKTotale)) * 100, 2);
$percentualeDataSwoTotale = ($pezzoPostOkTotale == 0) ? 0 : (round(($dataSwoTotale / $pezzoPostOkTotale) * 100, 2));


$fatturatoTot = ($valoriMedi[$idMandato] ?? 0) * $pezzoOkTotale;


$ricavoOrarioTot = $oreTotale != 0 ? round($fatturatoTot / $oreTotale, 0) : 0;

$html .= "<tr style = 'background-color: orangered;border: 2px solid lightslategray'>";
$html .= "<td colspan = '3'>TOTALE</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoLordoTotale</td>";
$html .= "<td>$pesoLordoTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoOkTotale</td>";
$html .= "<td>$pesoPdaOkTotale</td>";


$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoKoTotale</td>";
$html .= "<td>$pesoPdaKoTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBklTotale</td>";
$html .= "<td>$pesoPdaBklTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBklpTotale</td>";
$html .= "<td>$pesoPdaBklpTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$oreTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$resaPezzoLordoTotale</td>";
$html .= "<td>$resaValoreLordoTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$resaPezzoOkTotale</td>";
$html .= "<td>$resaValoreOkTotale</td>";
 $html .= "<td style='white-space: nowrap;'>" . round($ricavoOrarioTot) . " €</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBollettinoTotale</td>";
$html .= "<td>$pezzoRidTotale</td>";
$html .= "<td>$percentualeBollettinoTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBollettinoOKTotale</td>";
$html .= "<td>$pezzoRidOKTotale</td>";
$html .= "<td>$percentualeBollettinoOKTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$totaleObj</td>";
$html .= "<td style = 'border-left: 2px solid lightslategray'>" . round($totalepassogg) . "</td>";
$html .= "<td>$differentaObjTot</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$orepafTotale</td>";
$html .= "<td>$pezzoOkPafTotale</td>";
$html .= "<td>$resapezzoOkPafTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoLuceTotale</td>";
$html .= "<td>$pezzoGasTotale</td>";
$html .= "<td>$pezzoDualTotale</td>";
$html .= "<td>$pezzoPolizzaTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoPostOkTotale</td>";
$html .= "<td>$resaPostOKTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoPostKoTotale</td>";
$html .= "<td>$resaPostKOTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoPostBklTotale</td>";
$html .= "<td>$resaPostBklTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$dataSwoTotale</td>";
$html .= "<td>$percentualeDataSwoTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$deltaSwo1Totale</td>";
$html .= "<td>$deltaSwo3Totale</td>";
$html .= "<td>$deltaSwo6Totale</td>";
$html .= "<td>$deltaSwo9Totale</td>";
$html .= "<td>$deltaSwoEverTotale</td>";

$html .= "</tr>";

$html .= "</tr></table>";
if ($idMandato == 'Plenitude') {
    include "creaTabellaPolizzeInvertitoPerc.php";
}


if ($idMandato == 'EnelIn') {
    include "creatabellaFibra.php";
}


if ($idMandato == 'Heracom') {
    include "creatabellaConsenso.php";

}



echo $html;

