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
        . " agenzia,source,count(source) as lead,"
        . " sum(IF(pleniTot=0,0,1)) as convertiti,SUM(pleniTot) as 'Contratti Prodotti',sum(pleniOk) as 'Contratti OK',Sum(pleniKo) as 'Contratti KO', "
        . " sum(IF(vodaTot=0,0,1)) as convertiti,SUM(vodaTot) as 'Contratti Prodotti',sum(vodaOk) as 'Contratti OK',Sum(vodaKo) as 'Contratti KO', "
        . " sum(IF(viviTot=0,0,1)) as convertiti,SUM(viviTot) as 'Contratti Prodotti',sum(viviOk) as 'Contratti OK',Sum(viviKo) as 'Contratti KO', "
        . " (lead(agenzia) over (order by agenzia,source)) as prossimo, "
        . " sum(valoreMediaPleni) as 'VMpleni', sum(valoreMediaVivi) as 'VMvivi', sum(valoreMedioVoda) as 'VMvoda', "
        . " sum(IF(CategoriaUltima ='BACKLOG',1,0)) as BACKLOG, sum(IF(CategoriaUltima ='NONUTILICHIUSI',1,0)) as NONUTILICHIUSI, sum(IF(CategoriaUltima ='UTILICHIUSI',1,0)) as UTILICHIUSI ,"
        . " sum(IF(CategoriaUltima ='KoDefinitivi',1,0)) as vuoto, sum(convertito) as convertito "
        . "  "
        . " FROM "
        . " gestioneLead "
        . " where dataImport<='$dataMaggioreRicerca' and dataImport>='$dataMinoreRicerca' and source<>'Sito' and agenzia<>'VodafoneStore.it' "
        . " group by "
        . " agenzia";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);

$queryGroupMandatoSito = "SELECT"
        . " agenzia,source,count(source) as lead,"
        . " sum(IF(pleniTot=0,0,1)) as convertiti,SUM(pleniTot) as 'Contratti Prodotti',sum(pleniOk) as 'Contratti OK',Sum(pleniKo) as 'Contratti KO', "
        . " sum(IF(vodaTot=0,0,1)) as convertiti,SUM(vodaTot) as 'Contratti Prodotti',sum(vodaOk) as 'Contratti OK',Sum(vodaKo) as 'Contratti KO', "
        . " sum(IF(viviTot=0,0,1)) as convertiti,SUM(viviTot) as 'Contratti Prodotti',sum(viviOk) as 'Contratti OK',Sum(viviKo) as 'Contratti KO', "
        . " (lead(agenzia) over (order by agenzia,source)) as prossimo, "
        . " sum(valoreMediaPleni) as 'VMpleni', sum(valoreMediaVivi) as 'VMvivi', sum(valoreMedioVoda) as 'VMvoda', "
        . " sum(IF(CategoriaUltima ='BACKLOG',1,0)) as BACKLOG, sum(IF(CategoriaUltima ='NONUTILICHIUSI',1,0)) as NONUTILICHIUSI, sum(IF(CategoriaUltima ='UTILICHIUSI',1,0)) as UTILICHIUSI ,"
        . " sum(IF(CategoriaUltima ='KoDefinitivi',1,0)) as vuoto , sum(convertito) as convertito "
        . " FROM "
        . " gestioneLead "
        . " where dataImport<='$dataMaggioreRicerca' and dataImport>='$dataMinoreRicerca' and source = 'Sito' OR utmCampagna = 'Sito' and (duplicato='no' or duplicato='') "
        . " group by "
        . " agenzia,source";
//echo $queryGroupMandato;
$risultatoQueryGroupMandatoSito = $conn19->query($queryGroupMandatoSito);
$conteggioSito = $risultatoQueryGroupMandatoSito->num_rows;
$cs = 0;

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
        case "11":
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


$html = "<table class='blueTable'>";
$html2 = "<table class='blueTable'>";
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
    //$convertito = $pleniConvertiti + $vodaConvertito + $viviConvertito;
    $convertito = $rigaMandato[23];
    $totale = $pleniTot + $vodaTot + $viviTot;
    $ok = $pleniOk + $vodaOk + $viviOk;
    $ko = $pleniKo + $vodaKo + $viviKo;
    $prossimo = $rigaMandato[15];
    $valorePleni = $rigaMandato[16];
    $valoreVivi = $rigaMandato[17];
    $valoreVoda = $rigaMandato[18];
    $valoreMedio = $valorePleni + $valoreVivi + $valoreVoda;
    $backlog = $rigaMandato[19];
    $nonUtiliChiusi = $rigaMandato[20];
    $utiliChiusi = $rigaMandato[21];
    $vuoto = $rigaMandato[22];

    $convLead = ($lead == 0) ? 0 : round(($convertito / $lead) * 100, 2);
    $cpLead = ($lead == 0) ? 0 : round(($totale / $lead) * 100, 2);
    $okLead = ($lead == 0) ? 0 : round(($ok / $lead) * 100, 2);
    $mediaCpCon = ($convertito == 0) ? 0 : round($totale / $convertito, 2);

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
        case "dgtMedia":
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

        case "805-507-4937'":
            $costo = $costoGoogleBenchmark;
            break;
        default:
            $costo = 0;
    }

    $cpl = ($lead == 0) ? 0 : round(round($costo / $lead, 2), 2);
    $cpa = ($convertito == 0) ? 0 : round(round($costo / $convertito, 2), 2);
    $cpc = ($totale == 0) ? 0 : round(round($costo / $totale, 2), 2);
    $roi = ($costo == 0) ? 0 : round($valoreMedio / $costo, 2);

    $html .= "<tr>";
    $html .= "<td style='background-color: pink'>$agenzia</td>";
    $html .= "<td style='background-color: pink'>$source</td>";
    $html .= "<td style='background-color: pink'></td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$lead</td>";
    $html .= "<td>$convertito</td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$backlog</td>";
    $html .= "<td>$nonUtiliChiusi</td>";
    $html .= "<td>$utiliChiusi</td>";
    $html .= "<td>$vuoto</td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$totale</td>";
    $html .= "<td>$ok</td>";
    $html .= "<td>$ko</td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$convLead%</td>";
    $html .= "<td>$cpLead%</td>";
    $html .= "<td>$okLead%</td>";
    $html .= "<td>$mediaCpCon</td>";

    $html .= "<td style='border-left: 5px double #D0E4F5'>$costo €</td>";
    $html .= "<td>$cpl €</td>";
    $html .= "<td>$cpa €</td>";
    $html .= "<td>$cpc €</td>";
    $html .= "<td>$roi</td>";

    $html .= "</tr>";

    $html2 .= "<tr>";

    $html2 .= "<td style='background-color: pink'>$agenzia</td>";
    $html2 .= "<td style='background-color: pink'>$source</td>";
    $html2 .= "<td style='background-color: pink'></td>";

    $html2 .= "<td style='border-left: 5px double #D0E4F5'>$pleniTot</td>";
    $html2 .= "<td>$pleniOk</td>";
    $html2 .= "<td>$pleniKo</td>";

    $html2 .= "<td style='border-left: 5px double #D0E4F5'>$vodaTot</td>";
    $html2 .= "<td>$vodaOk</td>";
    $html2 .= "<td>$vodaKo</td>";

    $html2 .= "<td style='border-left: 5px double #D0E4F5'>$viviTot</td>";
    $html2 .= "<td>$viviOk</td>";
    $html2 .= "<td>$viviKo</td>";

    $html2 .= "</tr>";
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

        $convLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($convertitoAgenzia / $leadAgenzia) * 100, 2);
        $cpLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($totaleAgenzia / $leadAgenzia) * 100, 2);
        $okLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($okAgenzia / $leadAgenzia) * 100, 2);
        $mediaCpConAgenzia = ($convertitoAgenzia == 0) ? 0 : round($totaleAgenzia / $convertitoAgenzia, 2);

        $cplAgenzia = ($leadAgenzia == 0) ? 0 : round(round($costoAgenzia / $leadAgenzia, 2), 2);
        $cpaAgenzia = ($convertitoAgenzia == 0) ? 0 : round(round($costoAgenzia / $convertitoAgenzia, 2), 2);
        $cpcAgenzia = ($totaleAgenzia == 0) ? 0 : round(round($costoAgenzia / $totaleAgenzia, 2), 2);
        $roiAgenzia = ($costoAgenzia == 0) ? 0 : round($valoreMedioAgenzia / $costoAgenzia, 2);

        $html .= "<tr style='background-color: orange'>";
        $html .= "<td colspan='3' style='background-color: orange'>$agenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$leadAgenzia</td>";
        $html .= "<td>$convertitoAgenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$backlogAgenzia</td>";
        $html .= "<td>$nonUtiliChiusiAgenzia</td>";
        $html .= "<td>$utiliChiusiAgenzia</td>";
        $html .= "<td>$vuotoAgenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$totaleAgenzia</td>";
        $html .= "<td>$okAgenzia</td>";
        $html .= "<td>$koAgenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$convLeadAgenzia%</td>";
        $html .= "<td>$cpLeadAgenzia%</td>";
        $html .= "<td>$okLeadAgenzia%</td>";
        $html .= "<td>$mediaCpConAgenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$costoAgenzia €</td>";
        $html .= "<td>$cplAgenzia €</td>";
        $html .= "<td>$cpaAgenzia €</td>";
        $html .= "<td>$cpcAgenzia €</td>";
        $html .= "<td>$roiAgenzia</td>";

        $html .= "</tr>";

        $html2 .= "<tr style='background-color: orange'>";
        $html2 .= "<td colspan='3' style='background-color: orange'>$agenzia</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$pleniTotAgenzia</td>";
        $html2 .= "<td>$pleniOkAgenzia</td>";
        $html2 .= "<td>$pleniKoAgenzia</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$vodaTotAgenzia</td>";
        $html2 .= "<td>$vodaOkAgenzia</td>";
        $html2 .= "<td>$vodaKoAgenzia</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$viviTotAgenzia</td>";
        $html2 .= "<td>$viviOkAgenzia</td>";
        $html2 .= "<td>$viviKoAgenzia</td>";

        $html2 .= "</tr>";
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

        $convLeadTotale = ($leadTotale == 0) ? 0 : round(($convertitoTotale / $leadTotale) * 100, 2);
        $cpLeadTotale = ($leadTotale == 0) ? 0 : round(($totaleTotale / $leadTotale) * 100, 2);
        $okLeadTotale = ($leadTotale == 0) ? 0 : round(($okTotale / $leadTotale) * 100, 2);
        $mediaCpConTotale = ($convertitoTotale == 0) ? 0 : round($totaleTotale / $convertitoTotale, 2);

        $cplTotale = ($leadTotale == 0) ? 0 : round(round($costoTotale / $leadTotale, 2), 2);
        $cpaTotale = ($convertitoTotale == 0) ? 0 : round(round($costoTotale / $convertitoTotale, 2), 2);
        $cpcTotale = ($totaleTotale == 0) ? 0 : round(round($costoTotale / $totaleTotale, 2), 2);
        $roiTotale = ($costoTotale == 0) ? 0 : round($valoreMedioTotale / $costoTotale, 2);

        $html .= "<tr style='background-color: orangered'>";
        $html .= "<td colspan='3' style='background-color: orangered'>Totale</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$leadTotale</td>";
        $html .= "<td>$convertitoTotale</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$backlogTotale</td>";
        $html .= "<td>$nonUtiliChiusiTotale</td>";
        $html .= "<td>$utiliChiusiTotale</td>";
        $html .= "<td>$vuotoTotale</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$totaleTotale</td>";
        $html .= "<td>$okTotale</td>";
        $html .= "<td>$koTotale</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$convLeadTotale%</td>";
        $html .= "<td>$cpLeadTotale%</td>";
        $html .= "<td>$okLeadTotale%</td>";
        $html .= "<td>$mediaCpConTotale</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$costoTotale €</td>";
        $html .= "<td>$cplTotale €</td>";
        $html .= "<td>$cpaTotale €</td>";
        $html .= "<td>$cpcTotale €</td>";
        $html .= "<td>$roiTotale</td>";

        $html .= "</tr>";

        $html2 .= "<tr style='background-color: orangered'>";

        $html2 .= "<td colspan='3' style='background-color: orangered'>Totale</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$pleniTotTotale</td>";
        $html2 .= "<td>$pleniOkTotale</td>";
        $html2 .= "<td>$pleniKoTotale</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$vodaTotTotale</td>";
        $html2 .= "<td>$vodaOkTotale</td>";
        $html2 .= "<td>$vodaKoTotale</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$viviTotTotale</td>";
        $html2 .= "<td>$viviOkTotale</td>";
        $html2 .= "<td>$viviKoTotale</td>";

        $html2 .= "</tr>";

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
            //$convertitoSito = $pleniConvertitiSito + $vodaConvertitoSito + $viviConvertitoSito;
            $convertitoSito = $rigaMandatoSito[23];
            $totaleSito = $pleniTotSito + $vodaTotSito + $viviTotSito;
            $okSito = $pleniOkSito + $vodaOkSito + $viviOkSito;
            $koSito = $pleniKoSito + $vodaKoSito + $viviKoSito;
            $prossimoSito = $rigaMandatoSito[15];
            $valorePleniSito = $rigaMandatoSito[16];
            $valoreViviSito = $rigaMandatoSito[17];
            $valoreVodaSito = $rigaMandatoSito[18];
            $valoreMedioSito = $valorePleni + $valoreVivi + $valoreVoda;
            $backlogSito = $rigaMandatoSito[19];
            $nonUtiliChiusiSito = $rigaMandatoSito[20];
            $utiliChiusiSito = $rigaMandatoSito[21];
            $vuotoSito = $rigaMandatoSito[22];

            $convLeadSito = ($leadSito == 0) ? 0 : round(($convertitoSito / $leadSito) * 100, 2);
            $cpLeadSito = ($leadSito == 0) ? 0 : round(($totaleSito / $leadSito) * 100, 2);
            $okLeadSito = ($leadSito == 0) ? 0 : round(($okSito / $leadSito) * 100, 2);
            $mediaCpConSito = ($convertitoSito == 0) ? 0 : round($totaleSito / $convertitoSito, 2);
            $costoSito = 0;
            $cplSito = ($leadSito == 0) ? 0 : round(round($costoSito / $leadSito, 2), 2);
            $cpaSito = ($convertitoSito == 0) ? 0 : round(round($costoSito / $convertitoSito, 2), 2);
            $cpcSito = ($totaleSito == 0) ? 0 : round(round($costoSito / $totaleSito, 2), 2);
            $roiSito = ($costoSito == 0) ? 0 : round($valoreMedioSito / $costoSito, 2);

            $html .= "<tr style='background-color: TAN'>";
            $html .= "<td style='background-color: TAN'>$agenziaSito</td>";
            $html .= "<td style='background-color: TAN'>$sourceSito</td>";
            $html .= "<td style='background-color: TAN'></td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$leadSito</td>";
            $html .= "<td>$convertitoSito</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$backlogSito</td>";
            $html .= "<td>$nonUtiliChiusiSito</td>";
            $html .= "<td>$utiliChiusiSito</td>";
            $html .= "<td>$vuotoSito</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$totaleSito</td>";
            $html .= "<td>$okSito</td>";
            $html .= "<td>$koSito</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$convLeadSito%</td>";
            $html .= "<td>$cpLeadSito%</td>";
            $html .= "<td>$okLeadSito%</td>";
            $html .= "<td>$mediaCpConSito</td>";

            $html .= "<td style='border-left: 5px double #D0E4F5'>$costoSito €</td>";
            $html .= "<td>$cplSito €</td>";
            $html .= "<td>$cpaSito €</td>";
            $html .= "<td>$cpcSito €</td>";
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
            $mediaCpConSitoT = ($convertitoSitoT == 0) ? 0 : round($totaleSitoT / $convertitoSitoT, 2);
            $cplSitoT = ($leadSitoT == 0) ? 0 : round(round($costoSitoT / $leadSitoT, 2), 2);
            $cpaSitoT = ($convertitoSitoT == 0) ? 0 : round(round($costoSitoT / $convertitoSitoT, 2), 2);
            $cpcSitoT = ($totaleSitoT == 0) ? 0 : round(round($costoSitoT / $totaleSitoT, 2), 2);
            $roiSitoT = ($costoSitoT == 0) ? 0 : round($valoreMedioSitoT / $costoSitoT, 2);
            $cs++;
            if ($cs == $conteggioSito) {
                $html .= "<tr style='background-color: SANDYBROWN'>";
                $html .= "<td style='background-color: SANDYBROWN'>Totale Sito</td>";
                $html .= "<td style='background-color: SANDYBROWN'></td>";
                $html .= "<td style='background-color: SANDYBROWN'></td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$leadSitoT</td>";
                $html .= "<td>$convertitoSitoT</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$backlogSitoT</td>";
                $html .= "<td>$nonUtiliChiusiSitoT</td>";
                $html .= "<td>$utiliChiusiSitoT</td>";
                $html .= "<td>$vuotoSitoT</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$totaleSitoT</td>";
                $html .= "<td>$okSitoT</td>";
                $html .= "<td>$koSitoT</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$convLeadSitoT%</td>";
                $html .= "<td>$cpLeadSitoT%</td>";
                $html .= "<td>$okLeadSitoT%</td>";
                $html .= "<td>$mediaCpConSitoT</td>";

                $html .= "<td style='border-left: 5px double #D0E4F5'>$costoSitoT €</td>";
                $html .= "<td>$cplSitoT €</td>";
                $html .= "<td>$cpaSitoT €</td>";
                $html .= "<td>$cpcSitoT €</td>";
                $html .= "<td>$roiSitoT</td>";

                $html .= "</tr>";
            }
        }
    } else {
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

        $convLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($convertitoAgenzia / $leadAgenzia) * 100, 2);
        $cpLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($totaleAgenzia / $leadAgenzia) * 100, 2);
        $okLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($okAgenzia / $leadAgenzia) * 100, 2);
        $mediaCpConAgenzia = ($convertitoAgenzia == 0) ? 0 : round($totaleAgenzia / $convertitoAgenzia, 2);

        $cplAgenzia = ($leadAgenzia == 0) ? 0 : round(round($costoAgenzia / $leadAgenzia, 2), 2);
        $cpaAgenzia = ($convertitoAgenzia == 0) ? 0 : round(round($costoAgenzia / $convertitoAgenzia, 2), 2);
        $cpcAgenzia = ($totaleAgenzia == 0) ? 0 : round(round($costoAgenzia / $totaleAgenzia, 2), 2);
        $roiAgenzia = ($costoAgenzia == 0) ? 0 : round($valoreMedioAgenzia / $costoAgenzia, 2);

        $html .= "<tr style='background-color: orange'>";
        $html .= "<td colspan='3' style='background-color: orange'>$agenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$leadAgenzia</td>";
        $html .= "<td>$convertitoAgenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$backlogAgenzia</td>";
        $html .= "<td>$nonUtiliChiusiAgenzia</td>";
        $html .= "<td>$utiliChiusiAgenzia</td>";
        $html .= "<td>$vuotoAgenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$totaleAgenzia</td>";
        $html .= "<td>$okAgenzia</td>";
        $html .= "<td>$koAgenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$convLeadAgenzia%</td>";
        $html .= "<td>$cpLeadAgenzia%</td>";
        $html .= "<td>$okLeadAgenzia%</td>";
        $html .= "<td>$mediaCpConAgenzia</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'>$costoAgenzia €</td>";
        $html .= "<td>$cplAgenzia €</td>";
        $html .= "<td>$cpaAgenzia €</td>";
        $html .= "<td>$cpcAgenzia €</td>";
        $html .= "<td>$roiAgenzia</td>";

        $html .= "</tr>";

        $html2 .= "<tr style='background-color: orange'>";

        $html2 .= "<td colspan='3' style='background-color: orange'>$agenzia</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$pleniTotAgenzia</td>";
        $html2 .= "<td>$pleniOkAgenzia</td>";
        $html2 .= "<td>$pleniKoAgenzia</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$vodaTotAgenzia</td>";
        $html2 .= "<td>$vodaOkAgenzia</td>";
        $html2 .= "<td>$vodaKoAgenzia</td>";

        $html2 .= "<td style='border-left: 5px double #D0E4F5'>$viviTotAgenzia</td>";
        $html2 .= "<td>$viviOkAgenzia</td>";
        $html2 .= "<td>$viviKoAgenzia</td>";

        $html2 .= "</tr>";

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
    }
}




$html .= "</tr></table>";
$html2 .= "</tr></table>";

$html .= "<br>";
$html .= "<br>";
$html .= $html2;

echo $html;

