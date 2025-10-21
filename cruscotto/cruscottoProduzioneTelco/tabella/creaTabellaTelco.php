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
$oreSedeParziale = 0;
$orepafTotale = 0;
$pezzoOkPafTotale = 0;
$pezzoBollettinoOKSede = 0;
$pezzoRidOKSede = 0;
$pezzoCartaceoOKSede = 0;
$pezzoMailOKSede = 0;

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
$pezzoUllTotale = 0;
$pezzoNipTotale = 0;
$giornilavorati = 0;
$giornilavorativi = 0;
$pezzoBollettinoOKTotale = 0;
$pezzoRidOKTotale = 0;
$pezzoCartaceoOKTotale = 0;
$pezzoMailOKTotale = 0;
$dataSwoTotale = 0;
$meseCorrente = 0;

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
    '2025-04-21', '2025-04-25', '2025-04-26', '2025-05-01', '2025-06-02',
    '2025-07-05', '2025-07-12', '2025-07-19', '2025-07-26', '2025-08-02',
    '2025-08-09', '2025-08-16', '2025-08-23', '2025-08-30', '2025-08-11',
    '2025-08-12', '2025-08-13', '2025-08-14', '2025-08-15', '2025-08-16',
    '2025-08-17', '2025-08-15', '2025-11-01', '2025-12-08', '2025-12-25',
    '2025-12-26'
];

$result = calcolaGiorniLavorativi($dataMinore, $dataMaggiore, $giorniFestiviPerMese, $dateDaEscludere);

$giornirimanenti = $result['giorni_rimanenti'];
$giorniLavorativiPrevisti = $result['giorni_lavorabili_previsti'];
$giorniLavoratiEffettivi = $result['giorni_lavorati_effettivi'];

$mandati_validi = ["Tim", "TIM"];

$valoriMedi = [];
foreach ($mandato as $idMandato) {
    if (in_array($idMandato, ["Union", "Vodafone", "Bo" , "Vivigas Energia", "Enel", "Iren", "EnelIn", "Heracom", "Plenitude"]) || !in_array($idMandato, $mandati_validi)) {
        continue;
    }

    $meseMinore = date('Y-m-01', strtotime($dataMinore));
    $meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

    switch ($idMandato) {
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
    case "Tim":
        $queryCrm = "SELECT 
            'Lamezia' AS sede,
            tipoCampagna,
            SUM(IF(fasePDA = 'OK', pesoTotaleLordo, 0)) as peso_ok,
            SUM(IF(fasePDA = 'KO', pesoTotaleLordo, 0)) as peso_ko,
            SUM(IF(fasePDA = 'BKL', pesoTotaleLordo, 0)) as peso_bkl,
            SUM(IF(fasePDA = 'OK', pezzoLordo, 0)) as pezzo_ok,
            SUM(IF(fasePDA = 'KO', pezzoLordo, 0)) as pezzo_ko,
            SUM(IF(fasePDA = 'BKL', pezzoLordo, 0)) as pezzo_bkl,
            SUM(IF(fasePDA = 'BKLP', pezzoLordo, 0)) as pezzo_bklp,
            SUM(IF(fasePost = 'OK' AND fasePDA = 'OK', pezzoLordo, 0)) as pezzo_post_ok,
            SUM(IF(fasePost = 'KO' AND fasePDA = 'OK', pezzoLordo, 0)) as pezzo_post_ko,
            SUM(IF(fasePost = 'BKL' AND fasePDA = 'OK', pezzoLordo, 0)) as pezzo_post_bkl,
            SUM(IF(fasePost = 'OK', pesoTotaleLordo, 0)) as peso_post_ok,
            SUM(IF(fasePost = 'KO', pesoTotaleLordo, 0)) as peso_post_ko,
            SUM(IF(fasePost = 'BKL', pesoTotaleLordo, 0)) as peso_post_bkl,
            SUM(IF(metodoDiPagamento = 'Bollettino Postale', pezzoLordo, 0)) as pezzo_bollettino,
            SUM(IF(metodoDiPagamento = 'RID', pezzoLordo, 0)) as pezzo_rid,
            SUM(IF(tipoAttivazione = 'Migrazione', pezzoLordo, 0)) as pezzo_Ull,
            SUM(IF(tipoAttivazione = 'Nuova Linea', pezzoLordo, 0)) as pezzo_Nip
       
        FROM tim
        INNER JOIN aggiuntaTim ON tim.id = aggiuntaTim.id 

        WHERE dataVendita BETWEEN ? AND ?
        AND statoPda <> 'Bozza'  AND statoPda <> 'Annullata'   AND statoPda <> 'Pratica doppia' 
        AND tipoVendita = 'FISSO' 
        GROUP BY sede";
        break;
}

$stmtCrm = $conn19->prepare($queryCrm);
$stmtCrm->bind_param("ss", $dataMinore, $dataMaggiore);
$stmtCrm->execute();
$risultatoCrm = $stmtCrm->get_result();

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaTelco.php";

$valoriMedi = ['Tim' => 150];

while ($rigaCRM = $risultatoCrm->fetch_assoc()) {
    $sede = $rigaCRM["sede"];
    $sedeRicerca = ucwords($sede);
    $descrizioneMandato = $rigaCRM["tipoCampagna"];
    
    $queryGroupMandato = "SELECT SUM(numero)/3600 AS ore 
                         FROM `stringheTotale` 
                         WHERE giorno BETWEEN ? AND ? 
                         AND livello <= 6 
                         AND sede = ? 
                         AND idMandato = ? 
                         GROUP BY sede";
    
    $stmtOre = $conn19->prepare($queryGroupMandato);
    $stmtOre->bind_param("ssss", $dataMinore, $dataMaggiore, $sede, $idMandato);
    $stmtOre->execute();
    $risultaOre = $stmtOre->get_result();
    
    $ore = 0;
    if ($risultaOre && $risultaOre->num_rows > 0) {
        $rigaOre = $risultaOre->fetch_assoc();
        $ore = (float)$rigaOre['ore'];
    }
    $stmtOre->close();

    $pesoLordo = round($rigaCRM['peso_ok'] + $rigaCRM['peso_ko'] + $rigaCRM['peso_bkl'], 2);
    $pesoPdaOk = round($rigaCRM['peso_ok'], 2);
    $pesoPdaKo = round($rigaCRM['peso_ko'], 2);
    $pesoPdaBkl = round($rigaCRM['peso_bkl'], 2);
    
    $pezzoLordo = (int)($rigaCRM['pezzo_ok'] + $rigaCRM['pezzo_ko'] + $rigaCRM['pezzo_bkl']);
    $pezzoOk = (int)$rigaCRM['pezzo_ok'];
    $pezzoKo = (int)$rigaCRM['pezzo_ko'];
    $pezzoBkl = (int)$rigaCRM['pezzo_bkl'];
    $pezzoBklp = (int)$rigaCRM['pezzo_bklp'];
    
    $resaPezzoLordo = ($ore == 0) ? 0 : round($pezzoLordo / $ore, 2);
    $resaValoreLordo = ($ore == 0) ? 0 : round($pesoLordo / $ore, 2);
    $resaPezzoOk = ($ore == 0) ? 0 : round($pezzoOk / $ore, 2);
    $resaValoreOk = ($ore == 0) ? 0 : round($pesoPdaOk / $ore, 2);
    
    $pesoPostOk = round($rigaCRM['peso_post_ok'], 2);
    $pesoPostKo = round($rigaCRM['peso_post_ko'], 2);
    $pesoPostBkl = round($rigaCRM['peso_post_bkl'], 2);
    
    $pezzoPostOk = (int)$rigaCRM['pezzo_post_ok'];
    $pezzoPostKo = (int)$rigaCRM['pezzo_post_ko'];
    $pezzoPostBkl = (int)$rigaCRM['pezzo_post_bkl'];
    
    $resaPostOK = ($pezzoOk == 0) ? 0 : round(($pezzoPostOk / $pezzoOk) * 100, 2);
    $resaPostKO = ($pezzoOk == 0) ? 0 : round(($pezzoPostKo / $pezzoOk) * 100, 2);
    $resaPostBkl = ($pezzoOk == 0) ? 0 : round(($pezzoPostBkl / $pezzoOk) * 100, 2);
    
    $pezzoBollettino = (int)$rigaCRM['pezzo_bollettino'];
    $pezzoRid = (int)$rigaCRM['pezzo_rid'];
    $pezzoUll = (int)$rigaCRM['pezzo_Ull'];
    $pezzoNip = (int)$rigaCRM['pezzo_Nip'];
    $percentualeBollettino = (($pezzoBollettino + $pezzoRid) == 0) ? 0 : 
        round(($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100, 2);
    
    $orepaf = ($giorniLavoratiEffettivi > 0) ? 
        round(($ore / $giorniLavoratiEffettivi) * $giorniLavorativiPrevisti, 2) : 0;
    
    $pezzoOkPaf = ($giorniLavoratiEffettivi > 0) ? 
        (int)round(($pezzoOk / $giorniLavoratiEffettivi) * $giorniLavorativiPrevisti) : 0;
    
    $resaPezzoOkPf = ($orepaf == 0) ? 0 : round($pezzoOkPaf / $orepaf, 2);
    
    $passoGiorno = 0;
    
    $valoreMedio = $valoriMedi[$idMandato] ?? 0;
    $fatturato = $valoreMedio * $pezzoOk;
    $ricavoOrario = ($ore != 0) ? round($fatturato / $ore, 0) : 0;
    
    // Aggiungi ai totali
    $pesoLordoTotale += $pesoLordo;
    $pesoPdaOkTotale += $pesoPdaOk;
    $pesoPdaKoTotale += $pesoPdaKo;
    $pesoPdaBklTotale += $pesoPdaBkl;
    $pezzoLordoTotale += $pezzoLordo;
    $pezzoOkTotale += $pezzoOk;
    $pezzoKoTotale += $pezzoKo;
    $pezzoBklTotale += $pezzoBkl;
    $pezzoBklpTotale += $pezzoBklp;
    $oreTotale += $ore;
    $pesoPostOkTotale += $pesoPostOk;
    $pesoPostKoTotale += $pesoPostKo;
    $pesoPostBklTotale += $pesoPostBkl;
    $pezzoPostOkTotale += $pezzoPostOk;
    $pezzoPostKoTotale += $pezzoPostKo;
    $pezzoPostBklTotale += $pezzoPostBkl;
    $pezzoBollettinoTotale += $pezzoBollettino;
    $pezzoRidTotale += $pezzoRid;
    $pezzoUllTotale += $pezzoUll;
    $pezzoNipTotale += $pezzoNip;
    $orepafTotale += $orepaf;
    $pezzoOkPafTotale += $pezzoOkPaf;
    
    $html .= "<tr>";    
    $html .= "<td>$idMandato</td>";
    $html .= "<td>$sede</td>";
    $html .= "<td>-</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoLordo</td>";
    $html .= "<td>$pesoLordo</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoOk</td>";
    $html .= "<td>$pesoPdaOk</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoKo</td>";
    $html .= "<td>$pesoPdaKo</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoBkl</td>";
    $html .= "<td>$pesoPdaBkl</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoBklp</td>";
    $html .= "<td></td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>" . round($ore, 2) . "</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$resaPezzoLordo</td>";
    $html .= "<td>$resaValoreLordo</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$resaPezzoOk</td>";
    $html .= "<td>$resaValoreOk</td>";
    $html .= "<td style='white-space: nowrap;'>" . round($ricavoOrario) . " €</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoBollettino</td>";
    $html .= "<td>$pezzoRid</td>";
    $html .= "<td>$percentualeBollettino</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'></td>";
    $html .= "<td>" . round($passoGiorno) . "</td>";
    $html .= "<td></td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$orepaf</td>";
    $html .= "<td>$pezzoOkPaf</td>";
    $html .= "<td>$resaPezzoOkPf</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoUll</td>";
    $html .= "<td>$pezzoNip</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoPostOk</td>";
    $html .= "<td>$resaPostOK%</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoPostKo</td>";
    $html .= "<td>$resaPostKO%</td>";
    
    $html .= "<td style='border-right: 2px solid lightslategray'>$pezzoPostBkl</td>";
    $html .= "<td>$resaPostBkl%</td>";
    
    $html .= "</tr>";
}

$stmtCrm->close();

// Aggiungi la riga dei totali in giallo
$html .= "<tr style='background-color: yellow; font-weight: bold;'>";
$html .= "<td>TOTALE</td>";
$html .= "<td>-</td>";
$html .= "<td>-</td>";

$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoLordoTotale</td>";
$html .= "<td>" . round($pesoLordoTotale, 2) . "</td>";

$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoOkTotale</td>";
$html .= "<td>" . round($pesoPdaOkTotale, 2) . "</td>";

$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoKoTotale</td>";
$html .= "<td>" . round($pesoPdaKoTotale, 2) . "</td>";

$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoBklTotale</td>";
$html .= "<td>" . round($pesoPdaBklTotale, 2) . "</td>";

$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoBklpTotale</td>";
$html .= "<td></td>";

$html .= "<td style='border-right: 2px solid lightslategray'>" . round($oreTotale, 2) . "</td>";

$resaPezzoLordoTotale = ($oreTotale == 0) ? 0 : round($pezzoLordoTotale / $oreTotale, 2);
$resaValoreLordoTotale = ($oreTotale == 0) ? 0 : round($pesoLordoTotale / $oreTotale, 2);
$html .= "<td style='border-right: 2px solid lightslategray'>$resaPezzoLordoTotale</td>";
$html .= "<td>$resaValoreLordoTotale</td>";

$resaPezzoOkTotale = ($oreTotale == 0) ? 0 : round($pezzoOkTotale / $oreTotale, 2);
$resaValoreOkTotale = ($oreTotale == 0) ? 0 : round($pesoPdaOkTotale / $oreTotale, 2);
$html .= "<td style='border-right: 2px solid lightslategray'>$resaPezzoOkTotale</td>";
$html .= "<td>$resaValoreOkTotale</td>";

$ricavoOrarioTotale = ($oreTotale == 0) ? 0 : round(($valoreMedio * $pezzoOkTotale) / $oreTotale, 0);
$html .= "<td style='white-space: nowrap;'>$ricavoOrarioTotale €</td>";

$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoBollettinoTotale</td>";
$html .= "<td>$pezzoRidTotale</td>";
$percentualeBollettinoTotale = (($pezzoBollettinoTotale + $pezzoRidTotale) == 0) ? 0 : 
    round(($pezzoBollettinoTotale / ($pezzoBollettinoTotale + $pezzoRidTotale)) * 100, 2);
$html .= "<td>$percentualeBollettinoTotale</td>";

$html .= "<td style='border-right: 2px solid lightslategray'></td>";
$html .= "<td>" . round($passoGiornoSede, 2) . "</td>";
$html .= "<td></td>";

$html .= "<td style='border-right: 2px solid lightslategray'>" . round($orepafTotale, 2) . "</td>";
$html .= "<td>$pezzoOkPafTotale</td>";
$resaPezzoOkPfTotale = ($orepafTotale == 0) ? 0 : round($pezzoOkPafTotale / $orepafTotale, 2);
$html .= "<td>$resaPezzoOkPfTotale</td>";

$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoUllTotale</td>";
$html .= "<td>$pezzoNipTotale</td>";

$resaPostOkTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostOkTotale / $pezzoOkTotale) * 100, 2);
$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoPostOkTotale</td>";
$html .= "<td>$resaPostOkTotale%</td>";

$resaPostKoTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostKoTotale / $pezzoOkTotale) * 100, 2);
$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoPostKoTotale</td>";
$html .= "<td>$resaPostKoTotale%</td>";

$resaPostBklTotale = ($pezzoOkTotale == 0) ? 0 : round(($pezzoPostBklTotale / $pezzoOkTotale) * 100, 2);
$html .= "<td style='border-right: 2px solid lightslategray'>$pezzoPostBklTotale</td>";
$html .= "<td>$resaPostBklTotale%</td>";

$html .= "</tr>";

// Chiudi la tabella
$html .= "</table>";
echo $html;