<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
//echo $dataMinore;
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$testMode = $_POST["testMode"];
//echo $testMode;
//if ($testMode=="true") {
//    echo "si";
//}
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);
$arraySwitchOut = [];

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);
$orepafSede = 0;
$pezzoOkPafSede = 0;
$orepafTotale =0;
$pezzoOkPafTotale = 0;  

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

$pezzoBollettinoOKSede = 0;
$pezzoRidOKSede = 0;
$pezzoCartaceoOKSede = 0;
$pezzoMailOKSede = 0;

$deltaSwo1Sede = 0;
$deltaSwo3Sede = 0;
$deltaSwo6Sede = 0;
$deltaSwo9Sede = 0;
$deltaSwoEverSede = 0;

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

$pezzoBollettinoOKTotale = 0;
$pezzoRidOKTotale = 0;
$pezzoCartaceoOKTotale = 0;
$pezzoMailOKTotale = 0;
$dataSwoTotale = 0;

$deltaSwo1Totale = 0;
$deltaSwo3Totale = 0;
$deltaSwo6Totale = 0;
$deltaSwo9Totale = 0;
$deltaSwoEverTotale = 0;

$sedePrecedente = "";

$idMandato = $mandato[0];

$querySede = "";
$lunghezzaSede = count($sede);

if ($lunghezzaSede == 1) {
    $querySede .= " AND sede='$sede[0]' ";
} else {
    for ($l = 0;
            $l < $lunghezzaSede;
            $l++) {
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



// Funzione per calcolare i giorni lavorativi
function calcolaGiorniLavorativi($dataInizio, $dataFine) {
    $giorniLavorativiPrevisti = 0;
    $giorniLavoratiEffettivi = 0;

    // Converti le date in oggetti DateTime
    $dataInizio = new DateTime($dataInizio);
    $dataFine = new DateTime($dataFine);

    // Determina il mese di riferimento (mese di $dataInizio o $dataFine)
    $meseRiferimento = $dataInizio->format('m'); // Mese di $dataInizio
    $annoRiferimento = $dataInizio->format('Y'); // Anno di $dataInizio

    // Se il range di date copre due mesi, usa il mese di $dataFine
    if ($dataInizio->format('m') !== $dataFine->format('m')) {
        $meseRiferimento = $dataFine->format('m'); // Mese di $dataFine
        $annoRiferimento = $dataFine->format('Y'); // Anno di $dataFine
    }

    // Calcola i giorni lavorabili previsti per il mese di riferimento
    $primoGiornoMeseRiferimento = new DateTime("$annoRiferimento-$meseRiferimento-01");
    $ultimoGiornoMeseRiferimento = new DateTime($primoGiornoMeseRiferimento->format('Y-m-t')); // Ultimo giorno del mese

    $interval = new DateInterval('P1D');
    $period = new DatePeriod($primoGiornoMeseRiferimento, $interval, $ultimoGiornoMeseRiferimento->modify('+1 day'));

    foreach ($period as $giorno) {
        $giornoSettimana = $giorno->format('N'); // 1 (Lunedì) - 7 (Domenica)

        // Calcolo dei giorni lavorabili previsti (solo per il mese di riferimento)
        if ($giornoSettimana >= 1 && $giornoSettimana <= 5) {
            $giorniLavorativiPrevisti += 1; // Lunedì-venerdì = 1
        } elseif ($giornoSettimana == 6) {
            $giorniLavorativiPrevisti += 0.5; // Sabato = 0.5
        }
    }

    // Calcolo dei giorni lavorati effettivi (per il range di date specificato)
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($dataInizio, $interval, $dataFine->modify('+1 day'));

    foreach ($period as $giorno) {
        $giornoSettimana = $giorno->format('N'); // 1 (Lunedì) - 7 (Domenica)

        // Calcolo dei giorni lavorati effettivi
        if ($giornoSettimana >= 1 && $giornoSettimana <= 5) {
            $giorniLavoratiEffettivi += 1; // Lunedì-venerdì = 1
        } elseif ($giornoSettimana == 6) {
            $giorniLavoratiEffettivi += 0.5; // Sabato = 0.5
        }
    }

    return [
        'giorni_lavorabili_previsti' => $giorniLavorativiPrevisti,
        'giorni_lavorati_effettivi' => $giorniLavoratiEffettivi
    ];
}

// Calcola i giorni lavorativi per il range di date
$risultatoGiorni = calcolaGiorniLavorativi($dataMinore, $dataMaggiore);
$giornilavorativi = $risultatoGiorni['giorni_lavorabili_previsti'];
$giornilavorati = $risultatoGiorni['giorni_lavorati_effettivi'];
$giornirimanenti = $giornilavorativi - $giornilavorati;


echo "Giorni lavorabili previsti: " . $giornilavorativi . "<br>";
echo "Giorni lavorati effettivi: " . $giornilavorati . "<br>";
echo "Giorni lavorativi rimanenti: " . $giornirimanenti . "<br>";



switch ($idMandato) {
    case "Plenitude":
        $queryCrm = "SELECT "
                . " sede, "
                . " tipoCampagna, "
                . " sum(totalePesoLordo),"
                . " sum(if(fasePDA='OK',totalePesoLordo,0)),"
                . " sum(if(fasePDA='KO',totalePesoLordo,0)),"
                . " sum(if(fasePDA='BKL',totalePesoLordo,0)), "
                . " sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . " sum(pezzoLordo), "
                . " sum(if(fasePDA='OK',pezzoLordo,0)),"
                . " sum(if(fasePDA='KO',pezzoLordo,0)), "
                . " sum(if(fasePDA='BKL',pezzoLordo,0)), "
                . " sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), "
                . " sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . " sum(if(fasePost='OK',totalePesoLordo,0)),"
                . " sum(if(fasePost='KO',totalePesoLordo,0)), "
                . " sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino Postale',if(tipoAcquisizione<>'Subentro',if(fasePDA='OK',pezzoLordo,0),0),0)),"
                . " sum(if(metodoPagamento='RID',if(tipoAcquisizione<>'Subentro',if(fasePDA='OK',pezzoLordo,0),0),0)),"
                . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),"
                . " sum(if(comodity='Gas',pezzoLordo,0)),"
                . " sum(if(comodity='Dual',pezzoLordo,0)),"
                . " sum(if(comodity='Polizza',pezzoLordo,0)),"
                . " sum(if(metodoPagamento='Bollettino Postale',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoPagamento='RID',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoInvio='Cartaceo',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoInvio='Bollettaweb',if(fasePDA='OK',pezzoLordo,0),0)), "
                . " sum(if(dataSwitchOutLuce<>'0000-00-00'and dataSwitchOutGas='0000-00-00',1,0)), "
                . " sum(if(dataSwitchOutLuce='0000-00-00' and dataSwitchOutGas<>'0000-00-00',1,0)), "
                . " sum(if(dataSwitchOutLuce<>'0000-00-00' and dataSwitchOutGas<>'0000-00-00',1,0)), "
                . " sum(if(deltaMortalitaLuce=1,1,0)) as 'deltaSwo1', "
                . " 0, "
                . " sum(if(deltaMortalitaLuce>=2 and deltaMortalitaLuce<=3,1,0)) as 'deltaSwo3', "
                . " SUM(IF(deltaMortalitaLuce>9 , 1, 0)) AS 'deltaSwoEvere', "
                . " sum(if(deltaMortalitaLuce>=4 and deltaMortalitaLuce<=6,1,0)) as 'deltaSwo6', "
                . " 0, "
                . " sum(if(deltaMortalitaLuce>=7 and deltaMortalitaLuce<=9,1,0)) as 'deltaSwo9', "
                . " 0 "
                . " FROM "
                . " plenitude "
                . " inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                . " where data<='$dataMaggiore' and data>='$dataMinore' " 
                . " AND sede = 'Benchmark'" 
                . " and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza' "
                . " group by sede";

//        echo $queryCrm;
        $queryMortalitaLuce = "SELECT sede, SUM(IF(dataSwitchOutLuce BETWEEN '$dataMinore'  AND '$dataMaggiore', 1, 0)) AS sommaDataSwitchOutLuce FROM plenitude GROUP BY sede";
        $risultato = $conn19->query($queryMortalitaLuce);
        while ($riga = $risultato->fetch_array()) {
            $arraySwitchOut[$riga[0]] = $riga[1];
        }
        $queryMortalitaGas = "SELECT sede, SUM(IF(dataSwitchOutGas BETWEEN '$dataMinore'  AND '$dataMaggiore', 1, 0)) AS sommaDataSwitchOutGas FROM plenitude GROUP BY sede";
        $risultato = $conn19->query($queryMortalitaGas);
        while ($riga = $risultato->fetch_array()) {
            $arraySwitchOut[$riga[0]] += $riga[1];
        }

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
                . "where data<='$dataMaggiore' and data>='$dataMinore' " 
                . "AND sede = 'Benchmark'"  
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'"
                . " group by sede";
        break;
    case "Vivigas Energia":
        $queryCrm = "SELECT "
                . " sede, "
                . " tipoCampagna, "
                . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), "
                . " sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino',pezzoLordo,0)),sum(if(metodoPagamento='SSD',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Posta (Residenza)',pezzoLordo,0)),sum(if(metodoInvio='Mail',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)), "
                . " sum(if(comodity='Polizza',pezzoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoPagamento='SSD',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoInvio='Posta (Residenza)',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoInvio='Mail',if(fasePDA='OK',pezzoLordo,0),0)), "
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
                . "vivigas "
                . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                . "where data<='$dataMaggiore' and data>='$dataMinore' " 
                . "AND sede = 'Benchmark'" 
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'"
                . " group by sede";
//        echo $queryCrm;
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
                . "where data<='$dataMaggiore' and data>='$dataMinore' " 
                . "AND sede = 'Benchmark' " 
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia'"
                . " group by sede";
        break;
    case "Iren":
        $queryCrm = "SELECT "
                . " sede, "
                . " tipoCampagna, "
                . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)),sum(if(fasePost='KO',if(fasePDA='OK',pezzoLordo,0),0)), sum(if(fasePost='BKL',if(fasePDA='OK',pezzoLordo,0),0)), "
                . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                . " sum(if(metodoPagamento='Bollettino Postale',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoPagamento='RID',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoInvio='Cartaceo',if(fasePDA='OK',pezzoLordo,0),0)),"
                . " sum(if(metodoInvio='Bollettaweb',if(fasePDA='OK',pezzoLordo,0),0)),"
                . "0,"
                . "0, "
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
                . "iren "
                . "inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
                . "where data<='$dataMaggiore' and data>='$dataMinore'  " 
                . "AND sede = 'Benchmark' " 
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza'"
                . " group by sede";
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
                . "where data<='$dataMaggiore' and data>='$dataMinore'  " 
                . "AND sede = 'Benchmark'" 
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza'"
                . " group by sede";
        break;
        case "Enel":
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
                . "enel "
                . "inner JOIN aggiuntaEnel on enel.id=aggiuntaEnel.id "
                . "where data<='$dataMaggiore' and data>='$dataMinore'  " 
                . "AND sede = 'Benchmark'" 
                . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza'"
                . " group by sede";
        break;
}


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

    $queryGroupMandato = "SELECT "
            . " sum(numero)/3600 as ore "
            . " FROM `stringheTotale`  "
            . " where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello<=6  "
            . " and sede='Benchmark' "
            . " and idMandato='$idMandato'  "
            . " group by sede";

    //echo $queryGroupMandato;
    $risultaOre = $conn19->query($queryGroupMandato);
    if (($risultaOre->num_rows) > 0) {
        $rigaOre = $risultaOre->fetch_array();
        $ore = $rigaOre[0];
    } else {
        $ore = 0;
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

    
    //$pezzoMailOK
if ($giornilavorati > 0 && is_numeric($ore) && is_numeric($giornilavorativi)) {
    $orepaf = round((round($ore, 2) / $giornilavorati) * $giornilavorativi, 2);
} else {
    $orepaf = 0; // Se non ci sono giorni lavorati o i valori non sono validi, le ore previste devono essere 0
}

if ($giornilavorati > 0 && is_numeric($pezzoOk) && is_numeric($giornilavorativi)) {
    $pezzoOkPaf = round((round($pezzoOk) / $giornilavorati) * $giornilavorativi);
} else {
    $pezzoOkPaf = 0; // Se non ci sono giorni lavorati o i valori non sono validi, i pezzi OK previsti devono essere 0
}


$resaPezzoOkPf = ($orepaf == 0) ? 0 : round($pezzoOkPaf / $orepaf, 2);


    
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

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBollettino</td>";
    $html .= "<td>$pezzoRid</td>";
    $html .= "<td>$percentualeBollettino</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBollettinoOK</td>";
    $html .= "<td>$pezzoRidOK</td>";
    $html .= "<td>$percentualeBollettinoOK</td>";

    $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoCartaceo</td>";
    $html .= "<td>$pezzoMail</td>";
    $html .= "<td>$percentualeInvio</td>";

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

    $pezzoBollettinoOKSede += $pezzoBollettinoOK;
    $pezzoRidOKSede += $pezzoRidOK;
    $pezzoCartaceoOKSede += $pezzoCartaceoOK;
    $pezzoMailOKSede += $pezzoMailOK;
    $oreSede += $ore;
    $dataSwoSede += $dataSwo;
    $deltaSwo1Sede += $deltaSwo1;
    $deltaSwo3Sede += $deltaSwo3;
    $deltaSwo6Sede += $deltaSwo6;
    $deltaSwo9Sede += $deltaSwo9;
    $deltaSwoEverSede += $deltaSwoEver;
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

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBollettinoTotale</td>";
$html .= "<td>$pezzoRidTotale</td>";
$html .= "<td>$percentualeBollettinoTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBollettinoOKTotale</td>";
$html .= "<td>$pezzoRidOKTotale</td>";
$html .= "<td>$percentualeBollettinoOKTotale</td>";

$html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoCartaceoTotale</td>";
$html .= "<td>$pezzoMailTotale</td>";
$html .= "<td>$percentualeInvioTotale</td>";

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
    include "creaTabellaPolizzeInvertitoPercSubMandatiDue.php";
}

echo $html;

