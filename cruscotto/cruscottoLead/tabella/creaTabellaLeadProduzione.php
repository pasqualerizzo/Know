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
$irenTotAgenzia = 0;
$irenOkAgenzia = 0;
$irenKoAgenzia = 0;

$unionTotAgenzia = 0;
$unionOkAgenzia = 0;
$unionKoAgenzia = 0;

$enelTotAgenzia = 0;
$enelOkAgenzia = 0;
$enelKoAgenzia = 0;

$impotoAgenzia = 0;
$costoAgenzia = 0;
$valoreMedioAgenzia = 0;

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

$irenTotTotale = 0;
$irenOkTotale = 0;
$irenKoTotale = 0;

$timTotTotale = 0;
$timOkTotale = 0;
$timKoTotale = 0;
$timTotAgenzia = 0;
$timOkAgenzia = 0;
$timKoAgenzia = 0;

$importoTotale = 0;
$costoMetaArkys = 0;
$costoGoogleArkys = 0;
$costoMetaMuza = 0;
$costoGoogleMuza = 0;
$costoGoogleScegliAdesso = 0;
$costoGoogleBenchmark = 0;

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

$queryGroupMandato = "SELECT"
    . " agenzia,"
    . " source,"
    . " count(source) as lead,"
    . " sum(IF(pleniTot=0,0,1)) as convertiti,"
    . " SUM(pleniTot) as 'Contratti Prodotti', "
    . " sum(pleniOk) as 'Contratti OK',"
    . " Sum(pleniKo) as 'Contratti KO', "
    . " sum(IF(vodaTot=0,0,1)) as convertiti,"
    . " SUM(vodaTot) as 'Contratti Prodotti', "
    . " sum(vodaOk) as 'Contratti OK',"
    . " Sum(vodaKo) as 'Contratti KO', "
    . " sum(IF(viviTot=0,0,1)) as convertiti,"
    . " SUM(viviTot) as 'Contratti Prodotti',"
    . " sum(viviOk) as 'Contratti OK',"
    . " Sum(viviKo) as 'Contratti KO', "
    . " (lead(agenzia) over (order by agenzia,source)) as prossimo, "
    . " sum(valoreMediaPleni) as 'VMpleni',"
    . " sum(valoreMediaVivi) as 'VMvivi', "
    . " sum(valoreMedioVoda) as 'VMvoda', "
    . " sum(IF(CategoriaUltima ='BACKLOG',1,0)) as BACKLOG, "
    . " sum(IF(CategoriaUltima ='NONUTILICHIUSI',1,0)) as NONUTILICHIUSI, "
    . " sum(IF(CategoriaUltima ='UTILICHIUSI',1,0)) as UTILICHIUSI ,"
    . " sum(IF(CategoriaUltima ='KoDefinitivi',1,0)) as vuoto, "
    . " sum(IF(irenTot=0,0,1)) as convertitiIren, "
    . " SUM(irenTot) as 'Contratti Prodotti Iren', "
    . " sum(irenOk) as 'Contratti OK Iren',"
    . " Sum(irenKo) as 'Contratti KO Iren',"
    . " sum(valoreMedioIren) as 'VMiren', "
    . " sum(convertito) as convertito,  "
    . " sum(IF(uniTot=0,0,1)) as convertitiUni, "
    . " SUM(uniTot) as 'Contratti Prodotti union', "
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
    . " where dataImport<'$dataMaggioreRicerca' and dataImport>='$dataMinoreRicerca' and source<>'Sito' and utmCampagna<>'Sito' and agenzia<>'VodafoneStore.it' and (duplicato='no' or duplicato='')" . $queryAgenzia
    . " group by "
    . " agenzia";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);

$queryCosto = "SELECT nome_account, sum(importo_speso), id_account "
    . " FROM `facebook` "
    . " where giorno<='$dataMaggiore' and giorno>='$dataMinore' "
    . " GROUP by id_account";
//echo $queryCosto;
$risultatoCosto = $conn19->query($queryCosto);
$costoMetaMuza = 0;
$costoGoogleArkys = 0;
while ($rigaCosto = $risultatoCosto->fetch_array()) {
    $accountCosto = $rigaCosto[0];
    $costoImporto = round($rigaCosto[1], 2);
    $id_account = $rigaCosto[2];

    switch ($id_account) {
        case "761-724-0470":
        case "811-702-0343":
            $costoGoogleArkys = $costoImporto;
            break;
        case "147216197542152":
            $costoMetaArkys = $costoImporto;
            break;
        case "1286042745621446":
        case "1286042745621440":
            $costoMetaMuza += $costoImporto;
//            echo $costoMetaMuza." ";
            break;
        case "805-507-4937'":
            $costoGoogleBenchmark += $costoImporto;
            break;
    }
}


$html2 = "<table class='blueTable'>";
$html2 .= "<caption>Produzione Gestione Lead</caption>";
include "../../tabella/intestazioneTabellaCruscottoProduzione.php";

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

    $unionConvertiti = $rigaMandato[29];
    $unionTot = $rigaMandato[30];
    $unionOk = $rigaMandato[31];
    $unionKo = $rigaMandato[32];
    $valoreUnion = $rigaMandato[33];

    $enelTot = $rigaMandato[34];
    $enelOk = $rigaMandato[35];
    $enelKo = $rigaMandato[36];
    $valoreEnel = $rigaMandato[37];

    $timTot = $rigaMandato[38];
    $timOk = $rigaMandato[39];
    $timKo = $rigaMandato[40];
    $valoreTim = $rigaMandato[41];

    $totale = $pleniTot + $vodaTot + $viviTot + $irenTot + $unionTot + $enelTot + $timTot;
    $ok = $pleniOk + $vodaOk + $viviOk + $irenOk + $unionOk + $enelOk + $timOk;
    $ko = $pleniKo + $vodaKo + $viviKo + $irenKo + $unionKo + $enelKo + $timKo;
    $valoreMedio = $valorePleni + $valoreVoda + $valoreVivi + $valoreIren + $valoreUnion + $valoreEnel + $valoreTim;

    $costo = 0;
    switch ($agenzia) {
        case "Arkys":
            switch ($source) {
                case "Google ADS":
                    $costo = $costoGoogleArkys;
                    break;
                case "Meta":
                    $costo = $costoMetaArkys;
                    break;
                default:
                    $costo = 0;
            }
            break;
        case "Muza":
            switch ($source) {
                case "Google ADS":
                    $costo = $costoGoogleMuza;
                    break;
                case "Meta":
                    $costo = $costoMetaMuza;
                    break;
                default:
                    $costo = 0;
            }
            break;
        case "DgtMedia":
            switch ($source) {
                case "Google ADS":
                    $costo = $costoGoogleMuza;
                    break;
                case "Meta":
                    $costo = $costoMetaMuza;
                    break;
                default:
                    $costo = 0;
            }
            break;
        case "ScegliAdesso":
            $costo = $costoGoogleScegliAdesso;
            break;
        case "Benchmark":
            $costo = $costoGoogleBenchmark;
            break;
        default:
            $costo = 0;
    }


    $html2 .= "<tr>";

    $html2 .= "<td style='background-color: pink'>$agenzia</td>";

    $html2 .= "<td style='border-left: 5px double '>$pleniTot</td>";
    $html2 .= "<td>$pleniOk</td>";
    $html2 .= "<td>$pleniKo</td>";

    $html2 .= "<td style='border-left: 5px double'>$enelTot</td>";
    $html2 .= "<td>$enelOk</td>";
    $html2 .= "<td>$enelKo</td>";

    $html2 .= "<td style='border-left: 5px double '>$viviTot</td>";
    $html2 .= "<td>$viviOk</td>";
    $html2 .= "<td>$viviKo</td>";

    $html2 .= "<td style='border-left: 5px double '>$irenTot</td>";
    $html2 .= "<td>$irenOk</td>";
    $html2 .= "<td>$irenKo</td>";

    $html2 .= "<td style='border-left: 5px double '>$unionTot</td>";
    $html2 .= "<td>$unionOk</td>";
    $html2 .= "<td>$unionKo</td>";

    $html2 .= "<td style='border-left: 5px double '>$timTot</td>";
    $html2 .= "<td>$timOk</td>";
    $html2 .= "<td>$timKo</td>";

    $html2 .= "</tr>";

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

    $totaleAgenzia += $totale;
    $okAgenzia += $ok;
    $koAgenzia += $ko;
    $costoAgenzia += $costo;
    $valoreMedioAgenzia += $valoreMedio;
    $backlogAgenzia += $backlog;
    $utiliChiusiAgenzia += $utiliChiusi;
    $nonUtiliChiusiAgenzia += $nonUtiliChiusi;
    $vuotoAgenzia += $vuoto;

    $irenTotAgenzia += $irenTot;
    $irenOkAgenzia += $irenOk;
    $irenKoAgenzia += $irenKo;

    $unionTotAgenzia += $unionTot;
    $unionOkAgenzia += $unionOk;
    $unionKoAgenzia += $unionKo;

    $enelTotAgenzia += $enelTot;
    $enelOkAgenzia += $enelOk;
    $enelKoAgenzia += $enelKo;

    $timTotAgenzia += $timTot;
    $timOkAgenzia += $timOk;
    $timKoAgenzia += $timKo;
}


$html2 .= "<tr style='background-color: orangered'>";

$html2 .= "<td  style='background-color: orangered'>Totale</td>";

$html2 .= "<td style='border-left: 5px double '>$pleniTotAgenzia</td>";
$html2 .= "<td>$pleniOkAgenzia</td>";
$html2 .= "<td>$pleniKoAgenzia</td>";

$html2 .= "<td style='border-left: 5px double '>$enelTotAgenzia</td>";
$html2 .= "<td>$enelOkAgenzia</td>";
$html2 .= "<td>$enelKoAgenzia</td>";

$html2 .= "<td style='border-left: 5px double '>$viviTotAgenzia</td>";
$html2 .= "<td>$viviOkAgenzia</td>";
$html2 .= "<td>$viviKoAgenzia</td>";

$html2 .= "<td style='border-left: 5px double '>$irenTotAgenzia</td>";
$html2 .= "<td>$irenOkAgenzia</td>";
$html2 .= "<td>$irenKoAgenzia</td>";

$html2 .= "<td style='border-left: 5px double '>$unionTotAgenzia</td>";
$html2 .= "<td>$unionOkAgenzia</td>";
$html2 .= "<td>$unionKoAgenzia</td>";

$html2 .= "<td style='border-left: 5px double '>$timTotAgenzia</td>";
$html2 .= "<td>$timOkAgenzia</td>";
$html2 .= "<td>$timKoAgenzia</td>";

$html2 .= "</tr>";

$html2 .= "</tr></table>";

echo $html2;

