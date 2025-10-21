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

$dataMinoreRicerca = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggioreRicerca = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$agenzia = json_decode($_POST["agenzia"], true);
$queryAgenzia = "";
$lunghezza = count($agenzia);

if ($lunghezza == 1) {
    $queryAgenzia .= " AND agenzia='$agenzia[0]' ";
} else {
    for ($i = 0; $i < $lunghezza; $i++) {
        if ($i == 0) {
            $queryAgenzia .= " AND ( ";
        }
        $queryAgenzia .= " agenzia='$agenzia[$i]' ";
        if ($i == ($lunghezza - 1)) {
            $queryAgenzia .= " ) ";
        } else {
            $queryAgenzia .= " OR ";
        }
    }
}


$leadAgenzia = 0;
$pleniConvertitiAgenzia = 0;
$pleniTotAgenzia = 0;
$pleniOkAgenzia = 0;
$pleniKoAgenzia = 0;
$vodaConvertitoAgenzia = 0;
$vodaTotAgenzia = 0;
$vodaOkAgenzia = 0;
$vodaKoAgenzia = 0;
$viviConvertitoAgenzia = 0;
$viviTotAgenzia = 0;
$viviOkAgenzia = 0;
$viviKoAgenzia = 0;
$convertitoAgenzia = 0;
$totaleAgenzia = 0;
$okAgenzia = 0;
$koAgenzia = 0;
$roiAgenzia = 0;
$backlogAgenzia = 0;
$utiliChiusiAgenzia = 0;
$nonUtiliChiusiAgenzia = 0;
$vuotoAgenzia = 0;

$impotoAgenzia = 0;
$costoAgenzia = 0;
$valoreMedioAgenzia = 0;

$oreAgenzia = 0;
$costo1Agenzia = 0;

$leadTotale = 0;
$pleniConvertitiTotale = 0;
$pleniTotTotale = 0;
$pleniOkTotale = 0;
$pleniKoTotale = 0;
$vodaConvertitoTotale = 0;
$vodaTotTotale = 0;
$vodaOkTotale = 0;
$vodaKoTotale = 0;
$viviConvertitoTotale = 0;
$viviTotTotale = 0;
$viviOkTotale = 0;
$viviKoTotale = 0;
$convertitoTotale = 0;
$totaleTotale = 0;
$okTotale = 0;
$koTotale = 0;
$costoTotale = 0;
$valoreMedioTotale = 0;
$backlogTotale = 0;
$utiliChiusiTotale = 0;
$nonUtiliChiusiTotale = 0;
$vuotoTotale = 0;

$oreTotale = 0;
$costo1Totale = 0;

$importoTotale = 0;
$costoMetaArkys = 0;

$costoGoogleMuza = 0;

$leadSitoT = 0;
$pleniConvertitiSitoT = 0;
$convertitoSitoT = 0;
$totaleSitoT = 0;
$okSitoT = 0;
$koSitoT = 0;
$valoreMedioSitoT = 0;
$backlogSitoT = 0;
$nonUtiliChiusiSitoT = 0;
$utiliChiusiSitoT = 0;
$vuotoSitoT = 0;
$costoSitoT = 0;
$convLeadSitoT = 0;
$cpLeadSitoT = 0;
$okLeadSitoT = 0;
$mediaCpConSitoT = 0;
$cplSitoT = 0;
$cpaSitoT = 0;
$cpcSitoT = 0;
$roiSitoT = 0;

$leadStoreT = 0;
$pleniConvertitiStoreT = 0;
$convertitoStoreT = 0;
$totaleStoreT = 0;
$okStoreT = 0;
$koStoreT = 0;
$valoreMedioStoreT = 0;
$backlogStoreT = 0;
$nonUtiliChiusiStoreT = 0;
$utiliChiusiStoreT = 0;
$vuotoStoreT = 0;
$costoStoreT = 0;
$convLeadStoreT = 0;
$cpLeadStoreT = 0;
$okLeadStoreT = 0;
$mediaCpConStoreT = 0;
$cplStoreT = 0;
$cpaStoreT = 0;
$cpcStoreT = 0;
$roiStoreT = 0;
/**
 * 02/12/2024
 * Aggiunta gestione ore
 */
$queryProporzione = "SELECT"
    . " agenzia,"
    . " count(source) as lead"
    . " FROM "
    . " gestioneLead "
    . " where "
    . " dataImport<'$dataMaggioreRicerca' "
    . " and dataImport>='$dataMinoreRicerca' "
    . " and source<>'Sito' "
    . " and utmCampagna not in ('Sito', 'VodafoneStore.it') "
    . " and (duplicato='no' or duplicato='') "
    . " group by "
    . " agenzia";

$proporzione = [];
$totaleProporzione = 0;

try {
    $risultatoProporzione = $conn19->query($queryProporzione);
} catch (Exception $e) {
    echo "Errore nella quey di proporzione: " . $e;
}
while ($rigaProporzione = $risultatoProporzione->fetch_array()) {
    $proporzione[$rigaProporzione["agenzia"]] = $rigaProporzione["lead"];
}
foreach ($proporzione as $value) {
    $totaleProporzione += $value;
}
$proporzione["totale"] = $totaleProporzione;

$queryOre = "SELECT "
    . " sum(numero)/3600 "
    . " FROM "
    . " `stringheTotale` "
    . " where "
    . " giorno >='$dataMinore' "
    . " and giorno<='$dataMaggiore' "
    . " and (mandato='Lead Inbound' or (mandato='Vodafone' and provenienza='siscall2'))";
//echo $queryOre;
$risultatoOre = $conn19->query($queryOre);
if (($rigaOre = $risultatoOre->fetch_array())) {
    $oreIN = round($rigaOre[0], 2);
}
/**
 * fine calcolo ore
 */
$queryGroupMandato = "SELECT"
    . " agenzia,"
    . " source,"
    . " count(source) as lead,"
    . " sum(IF(pleniTot=0,0,1)) as convertiti,"
    . " SUM(pleniTot) as 'Contratti Prodotti',"
    . " sum(pleniOk) as 'Contratti OK',"
    . " Sum(pleniKo) as 'Contratti KO', "
    . " sum(IF(vodaTot=0,0,1)) as convertiti,"
    . " SUM(vodaTot) as 'Contratti Prodotti',"
    . " sum(vodaOk) as 'Contratti OK',"
    . " Sum(vodaKo) as 'Contratti KO', "
    . " sum(IF(viviTot=0,0,1)) as convertiti,"
    . " SUM(viviTot) as 'Contratti Prodotti',"
    . " sum(viviOk) as 'Contratti OK',"
    . " Sum(viviKo) as 'Contratti KO', "
    . " (lead(agenzia) over (order by agenzia,source)) as prossimo, "
    . " sum(valoreMediaPleni) as 'VMpleni', "
    . " sum(valoreMediaVivi) as 'VMvivi', "
    . " sum(valoreMedioVoda) as 'VMvoda', "
    . " sum(IF(CategoriaUltima ='BACKLOG',1,0)) as BACKLOG, "
    . " sum(IF(CategoriaUltima ='NONUTILICHIUSI',1,0)) as NONUTILICHIUSI,"
    . " sum(IF(CategoriaUltima ='UTILICHIUSI',1,0)) as UTILICHIUSI ,"
    . " sum(IF(CategoriaUltima ='KoDefinitivi',1,0)) as vuoto, "
    . " sum(IF(irenTot=0,0,1)) as convertitiIren,"
    . " SUM(irenTot) as 'Contratti Prodotti Iren',"
    . " sum(irenOk) as 'Contratti OK Iren', "
    . " Sum(irenKo) as 'Contratti KO Iren', "
    . " sum(valoreMedioIren) as 'VMiren', "
    . " sum(convertito) as convertito, "
    . " sum(IF(uniTot=0,0,1)) as convertitiUni, "
    . " SUM(uniTot) as 'Contratti Prodotti union',"
    . " sum(uniOk) as 'Contratti OK union',"
    . " Sum(uniKo) as 'Contratti KO union',"
    . " sum(valoreMedioUni) as 'VMunion', "
    . " SUM(enelTot) as 'Contratti Prodotti Enel',"
    . " sum(enelOk) as 'Contratti OK Enel',"
    . " Sum(enelKo) as 'Contratti KO Enel',"
    . " sum(valoreMedioEnel) as 'VMEnel', "

    . " SUM(timTot) as 'Contratti Prodotti Tim',"
    . " sum(timOk) as 'Contratti OK Tim',"
    . " Sum(timKo) as 'Contratti KO Tim',"
    . " sum(valoreMedioTim) as 'VMTim' "

    . " FROM "
    . " gestioneLead "
    . " where "
    . " dataImport<'$dataMaggioreRicerca' "
    . " and dataImport>='$dataMinoreRicerca' "
    . " and source<>'Sito' "
    . " and utmCampagna not in ('Sito', 'VodafoneStore.it') "
    // . " and utmCampagna in (SELECT gruppo  FROM `facebook` where giorno<='$dataMaggiore' and giorno>='$dataMinore') "
    . " and (duplicato='no' or duplicato='') " . $queryAgenzia
    . " group by "
    . " agenzia";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);

$queryGroupMandatoSito = "SELECT"
    . " agenzia,"
    . " source,"
    . " count(source) as lead,"
    . " sum(IF(pleniTot=0,0,1)) as convertiti, "
    . " SUM(pleniTot) as 'Contratti Prodotti', "
    . " sum(pleniOk) as 'Contratti OK', "
    . " Sum(pleniKo) as 'Contratti KO', "
    . " sum(IF(vodaTot=0,0,1)) as convertiti, "
    . " SUM(vodaTot) as 'Contratti Prodotti',"
    . " sum(vodaOk) as 'Contratti OK',"
    . " Sum(vodaKo) as 'Contratti KO', "
    . " sum(IF(viviTot=0,0,1)) as convertiti,"
    . " SUM(viviTot) as 'Contratti Prodotti',"
    . " sum(viviOk) as 'Contratti OK',"
    . " Sum(viviKo) as 'Contratti KO', "
    . " (lead(agenzia) over (order by agenzia,source)) as prossimo, "
    . " sum(valoreMediaPleni) as 'VMpleni', "
    . " sum(valoreMediaVivi) as 'VMvivi',"
    . " sum(valoreMedioVoda) as 'VMvoda', "
    . " sum(IF(CategoriaUltima ='BACKLOG',1,0)) as BACKLOG,"
    . " sum(IF(CategoriaUltima ='NONUTILICHIUSI',1,0)) as NONUTILICHIUSI,"
    . " sum(IF(CategoriaUltima ='UTILICHIUSI',1,0)) as UTILICHIUSI ,"
    . " sum(IF(CategoriaUltima ='KoDefinitivi',1,0)) as vuoto, "
    . " sum(IF(irenTot=0,0,1)) as convertitiIren,"
    . " SUM(irenTot) as 'Contratti Prodotti Iren',"
    . " sum(irenOk) as 'Contratti OK Iren', "
    . " Sum(irenKo) as 'Contratti KO Iren', "
    . " sum(valoreMedioIren) as 'VMiren', "
    . " sum(convertito) as convertito, "
    . " sum(IF(uniTot=0,0,1)) as convertitiUni,"
    . " SUM(uniTot) as 'Contratti Prodotti union',"
    . " sum(uniOk) as 'Contratti OK union',"
    . " Sum(uniKo) as 'Contratti KO union',"
    . " sum(valoreMedioUni) as 'VMunion', "
    . " SUM(enelTot) as 'Contratti Prodotti Enel',"
    . " sum(enelOk) as 'Contratti OK Enel',"
    . " Sum(enelKo) as 'Contratti KO Enel',"
    . " sum(valoreMedioEnel) as 'VMEnel', "

    . " SUM(timTot) as 'Contratti Prodotti Tim',"
    . " sum(timOk) as 'Contratti OK Tim',"
    . " Sum(timKo) as 'Contratti KO Tim',"
    . " sum(valoreMedioTim) as 'VMTim' "


    . " FROM "
    . " gestioneLead "
    . " where dataImport<'$dataMaggioreRicerca' and dataImport>='$dataMinoreRicerca' and (source='Sito' OR utmCampagna IN ('Sito', 'Retargeting')) and (duplicato='no' or duplicato='') " . $queryAgenzia
    . " group by "
    . " agenzia";
//echo $queryGroupMandato;
$risultatoQueryGroupMandatoSito = $conn19->query($queryGroupMandatoSito);
$conteggioSito = $risultatoQueryGroupMandatoSito->num_rows;
$cs = 0;

$queryCosto = "SELECT nome_account, sum(importo_speso), id_account,giorno "
    . " FROM `facebook` "
    . " where giorno<='$dataMaggiore' and giorno>='$dataMinore' "
    . " GROUP by nome_account";
//echo $queryCosto;
$risultatoCosto = $conn19->query($queryCosto);
$costoMetaGTEnergie = 0;
$costoGoogleGTEnergie = 0;
$costoGoogleArkys = 0;
$costoGoogleNovaMarketing = 0;
$costoMetaNovaMarketing = 0;
$costoGoogleDGTMedia = 0;
$costoMetaDGTMedia = 0;
$costoGoogleRetargeting = 0;
$costoGoogleNovaDirect = 0;
$costoMetaNovaDirect = 0;

$costoGoogleNovaStart = 0;
$costoMetaNovaStart = 0;
$costoMetaRetargeting = 0;
$costoMetaMuza = 0;

$costoGoogleScegliAdesso = 0;

$costoGoogleBenchmark = 0;

while ($rigaCosto = $risultatoCosto->fetch_array()) {
    $accountCosto = $rigaCosto[0];
    $costoImporto = round($rigaCosto[1], 2);
    $id_account = $rigaCosto[2];
    $giorno = $rigaCosto[3];

    switch ($id_account) {
        case "761-724-0470":      //Novadirect
            if ($giorno < '2024-06-11') {
                $costoGoogleArkys = $costoImporto;
            } else {
                $costoGoogleNovaDirect = $costoImporto;
            }
            break;

        case "811-702-0343":  //novastart
            if ($giorno < '2024-06-11') {
                $costoGoogleArkys = $costoImporto;
            } else {
                $costoGoogleNovaStart = $costoImporto;
            }
            break;

        case "756-546-2747":  //scegli adesso

            $costoGoogleScegliAdesso = $costoImporto;
            break;

        case "11":  //Retargeting
            if ($giorno < '2024-06-11') {
                $costoGoogleArkys = $costoImporto;
            } else {
                $costoGoogleRetargeting = $costoImporto;
            }
            break;


        case "147216197542152":
            if ($giorno < '2024-06-11') {
                $costoMetaArkys = $costoImporto;
            } else {
                $costoMetaNovaDirect = $costoImporto;
            }


            break;
        case "990-158-2709": //dgtMedia
            $costoGoogleDGTMedia += $costoImporto;
            break;
        case "1286042745621446":
        case "1286042745621440":
        case "7341425775561834498":
            $costoMetaGTEnergie += $costoImporto;
//            echo $costoMetaMuza." ";
            break;


        case "858-819-9597": //GTEnergia

            $costoGoogleGTEnergie += $costoImporto;
            break;

        case "805-507-4937": //benchmark
            $costoGoogleBenchmark += $costoImporto;
            break;

    }
}


$html = "<table class='blueTable'>";
$html .= "<caption><strong>Scoring Totale</strong></caption>";
/**
 * Inserimento dell'intestazione della tabella
 */
include "../../tabella/intestazioneTabellaCruscotto.php";

while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {

    $agenzia = $rigaMandato[0];
    $source = $rigaMandato[1];
    $lead = $rigaMandato[2];
    $pleniConvertiti = $rigaMandato[3];
    $pleniTot = $rigaMandato[4];
    $pleniOk = $rigaMandato[5];
    $pleniKo = $rigaMandato[6];
    $vodaConvertito = $rigaMandato[7];
    $vodaTot = $rigaMandato[8];
    $vodaOk = $rigaMandato[9];
    $vodaKo = $rigaMandato[10];
    $viviConvertito = $rigaMandato[11];
    $viviTot = $rigaMandato[12];
    $viviOk = $rigaMandato[13];
    $viviKo = $rigaMandato[14];

    $prossimo = $rigaMandato[15];
    $valorePleni = $rigaMandato[16];
    $valoreVivi = $rigaMandato[17];
    $valoreVoda = $rigaMandato[18];

    $backlog = $rigaMandato[19];
    $nonUtiliChiusi = $rigaMandato[20];
    $utiliChiusi = $rigaMandato[21];
    $vuoto = $rigaMandato[22];

    $irenConvertiti = $rigaMandato[23];
    $irenTot = $rigaMandato[24];
    $irenOk = $rigaMandato[25];
    $irenKo = $rigaMandato[26];
    $valoreIren = $rigaMandato[27];

    $convertito = $rigaMandato[28];

    $uniTot = $rigaMandato[30];
    $uniOk = $rigaMandato[31];
    $uniKo = $rigaMandato[32];
    $valoreUnion = $rigaMandato[33];

    $enelTot = $rigaMandato[34];
    $enelOk = $rigaMandato[35];
    $enelKo = $rigaMandato[36];
    $valoreEnel = $rigaMandato[37];


    $timTot = $rigaMandato[38];
    $timOk = $rigaMandato[39];
    $timKo = $rigaMandato[40];
    $valoreTim = $rigaMandato[41];

    $totale = $pleniTot + $vodaTot + $viviTot + $irenTot + $uniTot + $enelTot + $timTot;
    $valoreMedio = $valorePleni + $valoreVivi + $valoreVoda + $valoreIren + $valoreUnion + $valoreEnel + $valoreTim;
    $ok = $pleniOk + $vodaOk + $viviOk + $irenOk + $uniOk + $enelOk + $timOk;
    $ko = $pleniKo + $vodaKo + $viviKo + $irenKo + $uniKo + $enelKo + $timKo;

    $convLead = ($lead == 0) ? 0 : round(($convertito / $lead) * 100, 2);
    $cpLead = ($lead == 0) ? 0 : round(($totale / $lead) * 100, 2);
    $okLead = ($lead == 0) ? 0 : round(($ok / $lead) * 100, 2);
    $mediaCpCon = ($convertito == 0) ? 0 : round($totale / $convertito, 2);

    $costo = 0;
    switch ($agenzia) {
        case "Arkys":
            $costo = $costoGoogleArkys + $costoMetaArkys;
            break;
        case "Muza":
            $costo = $costoGoogleMuza + $costoMetaMuza;
            break;
        case "AdviceMe":
            $costo = $lead * 0.1;
            break;
        case "NovaMarketing":
            $costo = $costoGoogleNovaMarketing + $costoMetaNovaMarketing;
            break;
        case "DgtMedia":
            $costo = $costoGoogleDGTMedia;
            break;
        case "dgtMedia":
            $costo = $costoGoogleDGTMedia;
            break;

        case "NovaDirect":
            $costo = $costoGoogleNovaDirect + $costoMetaNovaDirect;
            break;

        case "NovaStart":
            $costo = $costoGoogleNovaStart + $costoMetaNovaStart;
            break;

        case "Retargeting":
            $costo = $costoGoogleRetargeting + $costoMetaRetargeting;
            break;

        case "GTEnergie":
            $costo = $costoGoogleGTEnergie + $costoMetaGTEnergie;
            break;
        case "ScegliAdesso":
            $costo = $costoGoogleScegliAdesso;

            break;

        case "Benchmark":
            $costo = $costoGoogleBenchmark;
            break;
    }
    /**
     * 02/12/2024
     * Calcolo ore
     */
    $ore = round($oreIN * ($proporzione[$agenzia] / $proporzione["totale"]), 2);
    $costo1 = round($ore * 13.5, 2);
    $r1 = $valoreMedio - ($costo1 + $costo);
    /**
     *
     */
    $cpl = ($lead == 0) ? 0 : round(round($costo / $lead, 2), 2);
    $cpa = ($convertito == 0) ? 0 : round(round($costo / $convertito, 2), 2);
    $cpc = ($ok == 0) ? 0 : round(round($costo / $ok, 2), 2);
    $roas = ($costo == 0) ? 0 : round($valoreMedio / $costo, 2);
    $roas2 = ($costo + $costo1 == 0) ? 0 : round($valoreMedio / ($costo + $costo1), 2);

    $html .= "<tr>";
    $html .= "<td style='background-color: pink'>$agenzia</td>";

    $html .= "<td style='border-left: 5px double '>$lead</td>";
    $html .= "<td>$convertito</td>";

    $html .= "<td style='border-left: 5px double '>$ore</td>";
    $html .= "<td>$costo1 €</td>";
    $html .= "<td>$costo €</td>";
    $html .= "<td>" . round($valoreMedio, 2) . " €</td>";
    $html .= "<td>" . round($r1, 2) . " €</td>";

    $html .= "<td style='border-left: 5px double '>$backlog</td>";
    $html .= "<td>$nonUtiliChiusi</td>";
    $html .= "<td>$utiliChiusi</td>";
    $html .= "<td>$vuoto</td>";

    $html .= "<td style='border-left: 5px double '>$totale</td>";
    $html .= "<td>$ok</td>";
    $html .= "<td>$ko</td>";

    $html .= "<td style='border-left: 5px double '>$convLead%</td>";
    $html .= "<td>$cpLead%</td>";
    $html .= "<td>$okLead%</td>";
    $html .= "<td>$mediaCpCon</td>";

    $html .= "<td style='border-left: 5px double '>$cpl €</td>";

    $html .= "<td>$cpc €</td>";
    $html .= "<td>$roas </td>";
    $html .= "<td>$roas2 </td>";

    $html .= "</tr>";

    if ($agenzia == $prossimo) {
        $leadAgenzia += $lead;
        $pleniConvertitiAgenzia += $pleniConvertiti;
        $pleniTotAgenzia += $pleniTot;
        $pleniOkAgenzia += $pleniOk;
        $pleniKoAgenzia += $pleniKo;
        $vodaConvertitoAgenzia += $vodaConvertito;
        $vodaTotAgenzia += $vodaTot;
        $vodaOkAgenzia += $vodaOk;
        $vodaKoAgenzia += $vodaKo;
        $viviConvertitoAgenzia += $viviConvertito;
        $viviTotAgenzia += $viviTot;
        $viviOkAgenzia += $viviOk;
        $viviKoAgenzia += $viviKo;
        $convertitoAgenzia += $convertito;
        $totaleAgenzia += $totale;
        $okAgenzia += $ok;
        $koAgenzia += $ko;
        $costoAgenzia += $costo;
        $valoreMedioAgenzia += $valoreMedio;
        $backlogAgenzia += $backlog;
        $utiliChiusiAgenzia += $utiliChiusi;
        $nonUtiliChiusiAgenzia += $nonUtiliChiusi;
        $vuotoAgenzia += $vuoto;

        $oreAgenzia += $ore;
        $costo1Agenzia += $costo1;
    } elseif ($prossimo == null) {
        $leadAgenzia += $lead;
        $pleniConvertitiAgenzia += $pleniConvertiti;
        $pleniTotAgenzia += $pleniTot;
        $pleniOkAgenzia += $pleniOk;
        $pleniKoAgenzia += $pleniKo;
        $vodaConvertitoAgenzia += $vodaConvertito;
        $vodaTotAgenzia += $vodaTot;
        $vodaOkAgenzia += $vodaOk;
        $vodaKoAgenzia += $vodaKo;
        $viviConvertitoAgenzia += $viviConvertito;
        $viviTotAgenzia += $viviTot;
        $viviOkAgenzia += $viviOk;
        $viviKoAgenzia += $viviKo;
        $convertitoAgenzia += $convertito;
        $totaleAgenzia += $totale;
        $okAgenzia += $ok;
        $koAgenzia += $ko;
        $costoAgenzia += $costo;
        $valoreMedioAgenzia += $valoreMedio;
        $backlogAgenzia += $backlog;
        $utiliChiusiAgenzia += $utiliChiusi;
        $nonUtiliChiusiAgenzia += $nonUtiliChiusi;
        $vuotoAgenzia += $vuoto;

        $oreAgenzia += $ore;
        $costo1Agenzia += $costo1;

        $convLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($convertitoAgenzia / $leadAgenzia) * 100, 2);
        $cpLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($totaleAgenzia / $leadAgenzia) * 100, 2);
        $okLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($okAgenzia / $leadAgenzia) * 100, 2);
        $mediaCpConAgenzia = ($convertitoAgenzia == 0) ? 0 : round($totaleAgenzia / $convertitoAgenzia, 2);

        $cplAgenzia = ($leadAgenzia == 0) ? 0 : round(round($costoAgenzia / $leadAgenzia, 2), 2);
        $cpaAgenzia = ($convertitoAgenzia == 0) ? 0 : round(round($costoAgenzia / $convertitoAgenzia, 2), 2);
        $cpcAgenzia = ($okAgenzia == 0) ? 0 : round(round($costoAgenzia / $okAgenzia, 2), 2);
        $roiAgenzia = ($costoAgenzia == 0) ? 0 : round($valoreMedioAgenzia / $costoAgenzia, 2);

        $leadTotale += $leadAgenzia;
        $pleniConvertitiTotale += $pleniConvertitiAgenzia;
        $pleniTotTotale += $pleniTotAgenzia;
        $pleniOkTotale += $pleniOkAgenzia;
        $pleniKoTotale += $pleniKoAgenzia;
        $vodaConvertitoTotale += $vodaConvertitoAgenzia;
        $vodaTotTotale += $vodaTotAgenzia;
        $vodaOkTotale += $vodaOkAgenzia;
        $vodaKoTotale += $vodaKoAgenzia;
        $viviConvertitoTotale += $viviConvertitoAgenzia;
        $viviTotTotale += $viviTotAgenzia;
        $viviOkTotale += $viviOkAgenzia;
        $viviKoTotale += $viviKoAgenzia;
        $convertitoTotale += $convertitoAgenzia;
        $totaleTotale += $totaleAgenzia;
        $okTotale += $okAgenzia;
        $koTotale += $koAgenzia;
        $costoTotale += $costoAgenzia;
        $valoreMedioTotale += $valoreMedioAgenzia;
        $backlogTotale += $backlogAgenzia;
        $utiliChiusiTotale += $utiliChiusiAgenzia;
        $nonUtiliChiusiTotale += $nonUtiliChiusiAgenzia;
        $vuotoTotale += $vuotoAgenzia;
        $oreTotale += $oreAgenzia;
        $costo1Totale += $costo1Agenzia;

        $convLeadTotale = ($leadTotale == 0) ? 0 : round(($convertitoTotale / $leadTotale) * 100, 2);
        $cpLeadTotale = ($leadTotale == 0) ? 0 : round(($totaleTotale / $leadTotale) * 100, 2);
        $okLeadTotale = ($leadTotale == 0) ? 0 : round(($okTotale / $leadTotale) * 100, 2);
        $mediaCpConTotale = ($convertitoTotale == 0) ? 0 : round($totaleTotale / $convertitoTotale, 2);

        $cplTotale = ($leadTotale == 0) ? 0 : round(round($costoTotale / $leadTotale, 2), 2);
        $cpaTotale = ($convertitoTotale == 0) ? 0 : round(round($costoTotale / $convertitoTotale, 2), 2);
        $cpcTotale = ($okTotale == 0) ? 0 : round(round($costoTotale / $okTotale, 2), 2);
        $roasTotale = ($costoTotale == 0) ? 0 : round($valoreMedioTotale / $costoTotale, 2);
        $roas2Totale = ($costoTotale + $costo1Totale == 0) ? 0 : round(($valoreMedioTotale / ($costoTotale + $costo1Totale)), 2);

        $r1Totale = $valoreMedioTotale - ($costo1Totale + $costoTotale);

        $html .= "<tr style='background-color: Salmon'>";
        $html .= "<td  style='background-color: Salmon'>Totale</td>";

        $html .= "<td style='border-left: 5px double '>$leadTotale</td>";
        $html .= "<td>$convertitoTotale</td>";

        $html .= "<td style='border-left: 5px double '>$oreTotale</td>";
        $html .= "<td>$costo1Totale €</td>";
        $html .= "<td>$costoTotale €</td>";
        $html .= "<td>" . round($valoreMedioTotale, 2) . " €</td>";
        $html .= "<td>" . round($r1Totale, 2) . " €</td>";

        $html .= "<td style='border-left: 5px double '>$backlogTotale</td>";
        $html .= "<td>$nonUtiliChiusiTotale</td>";
        $html .= "<td>$utiliChiusiTotale</td>";
        $html .= "<td>$vuotoTotale</td>";

        $html .= "<td style='border-left: 5px double '>$totaleTotale</td>";
        $html .= "<td>$okTotale</td>";
        $html .= "<td>$koTotale</td>";

        $html .= "<td style='border-left: 5px double '>$convLeadTotale%</td>";
        $html .= "<td>$cpLeadTotale%</td>";
        $html .= "<td>$okLeadTotale%</td>";
        $html .= "<td>$mediaCpConTotale</td>";

        $html .= "<td style='border-left: 5px double '>$cplTotale €</td>";

        $html .= "<td>$cpcTotale €</td>";
        $html .= "<td>$roasTotale </td>";
        $html .= "<td>$roas2Totale </td>";

        $html .= "</tr>";

        while ($rigaMandatoSito = $risultatoQueryGroupMandatoSito->fetch_array()) {
            $agenziaSito = $rigaMandatoSito[0];
            $sourceSito = $rigaMandatoSito[1];
            $leadSito = $rigaMandatoSito[2];
            $pleniConvertitiSito = $rigaMandatoSito[3];
            $pleniTotSito = $rigaMandatoSito[4];
            $pleniOkSito = $rigaMandatoSito[5];
            $pleniKoSito = $rigaMandatoSito[6];
            $vodaConvertitoSito = $rigaMandatoSito[7];
            $vodaTotSito = $rigaMandatoSito[8];
            $vodaOkSito = $rigaMandatoSito[9];
            $vodaKoSito = $rigaMandatoSito[10];
            $viviConvertitoSito = $rigaMandatoSito[11];
            $viviTotSito = $rigaMandatoSito[12];
            $viviOkSito = $rigaMandatoSito[13];
            $viviKoSito = $rigaMandatoSito[14];

            $prossimoSito = $rigaMandatoSito[15];
            $valorePleniSito = $rigaMandatoSito[16];
            $valoreViviSito = $rigaMandatoSito[17];
            $valoreVodaSito = $rigaMandatoSito[18];
            $valoreMedioSito = $valorePleni + $valoreVivi + $valoreVoda;
            $backlogSito = $rigaMandatoSito[19];
            $nonUtiliChiusiSito = $rigaMandatoSito[20];
            $utiliChiusiSito = $rigaMandatoSito[21];
            $vuotoSito = $rigaMandatoSito[22];

            $convertitoSito = $rigaMandatoSito[28];

            $irenTotSito = $rigaMandatoSito[24];
            $irenOkSito = $rigaMandatoSito[25];
            $irenKoSito = $rigaMandatoSito[26];
            $valoreIrenSito = $rigaMandatoSito[27];

            $uniTotSito = $rigaMandatoSito[30];
            $uniOkSito = $rigaMandatoSito[31];
            $uniKoSito = $rigaMandatoSito[32];
            $valoreUnionSito = $rigaMandatoSito[33];

            $totaleSito = $pleniTotSito + $vodaTotSito + $viviTotSito + $irenTotSito + $uniTotSito;
            $okSito = $pleniOkSito + $vodaOkSito + $viviOkSito;
            $koSito = $pleniKoSito + $vodaKoSito + $viviKoSito;
            $valoreMedioSito = $valorePleni + $valoreVivi + $valoreVoda + $valoreUnionSito + $valoreIrenSito;

            $convLeadSito = ($leadSito == 0) ? 0 : round(($convertitoSito / $leadSito) * 100, 2);
            $cpLeadSito = ($leadSito == 0) ? 0 : round(($totaleSito / $leadSito) * 100, 2);
            $okLeadSito = ($leadSito == 0) ? 0 : round(($okSito / $leadSito) * 100, 2);
            $mediaCpConSito = ($convertitoSito == 0) ? 0 : round($okSito / $convertitoSito, 2);
            $costoSito = 0;
            $cplSito = ($leadSito == 0) ? 0 : round(round($costoSito / $leadSito, 2), 2);
            $cpaSito = ($convertitoSito == 0) ? 0 : round(round($costoSito / $convertitoSito, 2), 2);
            $cpcSito = ($okSito == 0) ? 0 : round(round($costoSito / $okSito, 2), 2);
            $roiSito = ($costoSito == 0) ? 0 : round($valoreMedioSito / $costoSito, 2);

            $html .= "<tr style='background-color: TAN'>";
            $html .= "<td style='background-color: TAN'>$agenziaSito (Sito)</td>";

            $html .= "<td style='border-left: 5px double '>$leadSito</td>";
            $html .= "<td>$convertitoSito</td>";

            $html .= "<td style='border-left: 5px double '>0</td>";
            $html .= "<td>0</td>";
            $html .= "<td>0</td>";
            $html .= "<td>0</td>";
            $html .= "<td>0</td>";

            $html .= "<td style='border-left: 5px double '>$backlogSito</td>";
            $html .= "<td>$nonUtiliChiusiSito</td>";
            $html .= "<td>$utiliChiusiSito</td>";
            $html .= "<td>$vuotoSito</td>";

            $html .= "<td style='border-left: 5px double '>$totaleSito</td>";
            $html .= "<td>$okSito</td>";
            $html .= "<td>$koSito</td>";

            $html .= "<td style='border-left: 5px double '>$convLeadSito%</td>";
            $html .= "<td>$cpLeadSito%</td>";
            $html .= "<td>$okLeadSito%</td>";
            $html .= "<td>$mediaCpConSito</td>";

            $html .= "<td style='border-left: 5px double '>$costoSito €</td>";
            $html .= "<td>$cplSito €</td>";
            $html .= "<td>$cpaSito €</td>";

            $html .= "<td>$roiSito</td>";

            $html .= "</tr>";

            $leadSitoT += $leadSito;
            $pleniConvertitiSitoT += $pleniConvertitiSito;
            $convertitoSitoT += $convertitoSito;
            $totaleSitoT += $totaleSito;
            $okSitoT += $okSito;
            $koSitoT += $koSito;
            $valoreMedioSitoT += $valoreMedioSito;
            $backlogSitoT += $backlogSito;
            $nonUtiliChiusiSitoT = $rigaMandatoSito[20];
            $utiliChiusiSitoT += $utiliChiusiSito;
            $vuotoSitoT += $vuotoSito;
            $costoSitoT = 0;
            $convLeadSitoT = ($leadSitoT == 0) ? 0 : round(($convertitoSitoT / $leadSitoT) * 100, 2);
            $cpLeadSitoT = ($leadSitoT == 0) ? 0 : round(($totaleSitoT / $leadSitoT) * 100, 2);
            $okLeadSitoT = ($leadSitoT == 0) ? 0 : round(($okSitoT / $leadSitoT) * 100, 2);
            $mediaCpConSitoT = ($convertitoSitoT == 0) ? 0 : round($okSitoT / $convertitoSitoT, 2);
            $cplSitoT = ($leadSitoT == 0) ? 0 : round(round($costoSitoT / $leadSitoT, 2), 2);
            $cpaSitoT = ($convertitoSitoT == 0) ? 0 : round(round($costoSitoT / $convertitoSitoT, 2), 2);
            $cpcSitoT = ($okSitoT == 0) ? 0 : round(round($costoSitoT / $okSitoT, 2), 2);
            $roiSitoT = ($costoSitoT == 0) ? 0 : round($valoreMedioSitoT / $costoSitoT, 2);
        }
    }
    {
        $leadAgenzia += $lead;
        $pleniConvertitiAgenzia += $pleniConvertiti;
        $pleniTotAgenzia += $pleniTot;
        $pleniOkAgenzia += $pleniOk;
        $pleniKoAgenzia += $pleniKo;
        $vodaConvertitoAgenzia += $vodaConvertito;
        $vodaTotAgenzia += $vodaTot;
        $vodaOkAgenzia += $vodaOk;
        $vodaKoAgenzia += $vodaKo;
        $viviConvertitoAgenzia += $viviConvertito;
        $viviTotAgenzia += $viviTot;
        $viviOkAgenzia += $viviOk;
        $viviKoAgenzia += $viviKo;
        $convertitoAgenzia += $convertito;
        $totaleAgenzia += $totale;
        $okAgenzia += $ok;
        $koAgenzia += $ko;
        $costoAgenzia += $costo;
        $valoreMedioAgenzia += $valoreMedio;
        $backlogAgenzia += $backlog;
        $utiliChiusiAgenzia += $utiliChiusi;
        $nonUtiliChiusiAgenzia += $nonUtiliChiusi;
        $vuotoAgenzia += $vuoto;
        $oreAgenzia += $ore;
        $costo1Agenzia += $costo1;

        $convLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($convertitoAgenzia / $leadAgenzia) * 100, 2);
        $cpLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($totaleAgenzia / $leadAgenzia) * 100, 2);
        $okLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($okAgenzia / $leadAgenzia) * 100, 2);
        $mediaCpConAgenzia = ($convertitoAgenzia == 0) ? 0 : round($okAgenzia / $convertitoAgenzia, 2);

        $cplAgenzia = ($leadAgenzia == 0) ? 0 : round(round($costoAgenzia / $leadAgenzia, 2), 2);
        $cpaAgenzia = ($convertitoAgenzia == 0) ? 0 : round(round($costoAgenzia / $convertitoAgenzia, 2), 2);
        $cpcAgenzia = ($okAgenzia == 0) ? 0 : round(round($costoAgenzia / $okAgenzia, 2), 2);
        $roiAgenzia = ($costoAgenzia == 0) ? 0 : round($valoreMedioAgenzia / $costoAgenzia, 2);

        $leadTotale += $leadAgenzia;
        $pleniConvertitiTotale += $pleniConvertitiAgenzia;
        $pleniTotTotale += $pleniTotAgenzia;
        $pleniOkTotale += $pleniOkAgenzia;
        $pleniKoTotale += $pleniKoAgenzia;
        $vodaConvertitoTotale += $vodaConvertitoAgenzia;
        $vodaTotTotale += $vodaTotAgenzia;
        $vodaOkTotale += $vodaOkAgenzia;
        $vodaKoTotale += $vodaKoAgenzia;
        $viviConvertitoTotale += $viviConvertitoAgenzia;
        $viviTotTotale += $viviTotAgenzia;
        $viviOkTotale += $viviOkAgenzia;
        $viviKoTotale += $viviKoAgenzia;
        $convertitoTotale += $convertitoAgenzia;
        $totaleTotale += $totaleAgenzia;
        $okTotale += $okAgenzia;
        $koTotale += $koAgenzia;
        $costoTotale += $costoAgenzia;
        $valoreMedioTotale += $valoreMedioAgenzia;
        $backlogTotale += $backlogAgenzia;
        $utiliChiusiTotale += $utiliChiusiAgenzia;
        $nonUtiliChiusiTotale += $nonUtiliChiusiAgenzia;
        $vuotoTotale += $vuotoAgenzia;
        $oreTotale += $oreAgenzia;
        $costo1Totale += $costo1Agenzia;

        $leadAgenzia = 0;
        $pleniConvertitiAgenzia = 0;
        $pleniTotAgenzia = 0;
        $pleniOkAgenzia = 0;
        $pleniKoAgenzia = 0;
        $vodaConvertitoAgenzia = 0;
        $vodaTotAgenzia = 0;
        $vodaOkAgenzia = 0;
        $vodaKoAgenzia = 0;
        $viviConvertitoAgenzia = 0;
        $viviTotAgenzia = 0;
        $viviOkAgenzia = 0;
        $viviKoAgenzia = 0;
        $convertitoAgenzia = 0;
        $totaleAgenzia = 0;
        $okAgenzia = 0;
        $koAgenzia = 0;
        $costoAgenzia = 0;
        $valoreMedioAgenzia = 0;
        $backlogAgenzia = 0;
        $utiliChiusiAgenzia = 0;
        $nonUtiliChiusiAgenzia = 0;
        $vuotoAgenzia = 0;

        $oreAgenzia = 0;
        $costo1Agenzia = 0;
    }
}


$html .= "</tr></table>";

$html .= "<br>";

echo $html;

