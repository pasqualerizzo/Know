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
$a = "";
$adavaceMe = false;

if ($lunghezza == 1) {
    switch ($agenzia[0]) {
        case "AdviceMe":
            $adavaceMe = true;
            break;
        case "Arkys":
            $a = "Risparmiami";
            break;
        case "NovaMarketing":
            $a = "Risparmiami";
            break;
        case "Muza":
            $a = "DgtMedia";
            break;
        case "Retargeting":
            $a = "Retargeting";
            break;
        case "ScegliAdesso":
            $a = "ScegliAdesso";
            
        case "Benchmark":
            $a = "Benchmark";

            break;
        default:
            $a = $agenzia[$i];
            break;
    }
    $queryAgenzia .= " AND nome_account='$a' ";
} else {
    for ($i = 0; $i < $lunghezza; $i++) {
        if ($i == 0) {
            $queryAgenzia .= " AND ( ";
        }
        switch ($agenzia[$i]) {
            case "AdviceMe":
                $adavaceMe = true;
                break;
            case "Arkys":
                $a = "Risparmiami";
                break;
            case "Muza":
                $a = "DgtMedia";
                break;
            case "NovaMarketing":
                $a = "Risparmiami";
                break;
            case "Servizio_Clienti_Energia":
                $a = "Risparmiami";
                break;
            case "Retargeting":
                $a = "Retargeting";
                break;
            case "ScegliAdesso":
                $a = "ScegliAdesso";
                
            case "Benchmark":
                $a = "Benchmark";
                break;
            default:
                $a = $agenzia[$i];
                break;
        }
        $queryAgenzia .= " nome_account='$a' ";
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
$convertitiAgenzia = 0;
$totaleAgenzia = 0;
$okAgenzia = 0;
$koAgenzia = 0;
$roiAgenzia = 0;

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
$convertitiTotale = 0;
$totaleTotale = 0;
$okTotale = 0;
$koTotale = 0;
$costoTotale = 0;
$valoreMedioTotale = 0;

$importoTotale = 0;
$costoMetaArkys = 0;
$costoGoogleArkys = 0;
$costoMetaMuza = 0;
$costoGoogleMuza = 0;
$costoGoogleScegliAdesso = 0;

$queryAccount = "SELECT nome_account, gruppo,sum(importo_speso),(lead(nome_account) over (order by nome_account,provenienza desc)) as prossimo, provenienza"
        . " FROM `facebook` "
        . "where giorno<='$dataMaggiore' and giorno>='$dataMinore' " . $queryAgenzia
        . "GROUP by gruppo "
        . "order by  nome_account,provenienza desc";
//echo $queryAccount;




$html3 = "<table class='blueTable'>";
$html3 .= "<thead>";
$html3 .= "<tr>";
$html3 .= "<th>Account</th>";
$html3 .= "<th>Campagna</th>";
$html3 .= "<th>Provenienza</th>";

$html3 .= "<th  style='border-left: 5px double'>Costo</th>";

$html3 .= "<th  style='border-left: 5px double'>Lead</th>";
$html3 .= "<th>Conv.</th>";

$html3 .= "<th  style='border-left: 5px double'>BackLog</th>";
$html3 .= "<th>Non Utili</th>";
$html3 .= "<th>Utili</th>";
$html3 .= "<th>DNC</th>";

$html3 .= "<th  style='border-left: 5px double'>CP</th>";
$html3 .= "<th>OK</th>";
$html3 .= "<th>KO</th>";

$html3 .= "<th  style='border-left: 5px double'>% Conv/Lead</th>";
$html3 .= "<th>	% Cp/Lead</th>";
$html3 .= "<th>% Ok/Lead</th>";
$html3 .= "<th>Media Cp/Conv</th>";

$html3 .= "<th  style='border-left: 5px double'>CPL</th>";
$html3 .= "<th>	CPA</th>";
$html3 .= "<th>CPC</th>";
$html3 .= "<th>ROI</th>";

$html3 .= "</thead>";

$risultatoQueryGroupCosto = $conn19->query($queryAccount);
$importoAgenzia = 0;
$leadAgenzia = 0;
$convertitiAgenzia = 0;
$contrattiAgenzia = 0;
$okAgenzia = 0;
$koAgenzia = 0;
$valoreAgenzia = 0;
$backlogAgenzia = 0;
$nonUtiliChiusiAgenzia = 0;
$utiliChiusiAgenzia = 0;
$dncAgenzia = 0;

$importoTotale = 0;
$convertitiTotale = 0;
$contrattiTotale = 0;
$valoreTotale = 0;
$backlogTotale = 0;
$nonUtiliChiusiTotale = 0;
$utiliChiusiTotale = 0;
$dncTotale = 0;

while ($rigaCosto = $risultatoQueryGroupCosto->fetch_array()) {

    $account = $rigaCosto[0];
    $campagna = $rigaCosto[1];
    $importo = round($rigaCosto[2], 2);
    $prossimo = $rigaCosto[3];
    $provenienza = $rigaCosto[4];

    $queryGroupMandato = "SELECT"
            . " count(utmCampagna) as lead,"
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
            . " sum(valoreMediaPleni) as 'VMpleni',"
            . " sum(valoreMediaVivi) as 'VMvivi',"
            . " sum(valoreMedioVoda) as 'VMvoda', "
            . " sum(IF(CategoriaUltima ='BACKLOG',1,0)) as BACKLOG,"
            . " sum(IF(CategoriaUltima ='NONUTILICHIUSI',1,0)) as NONUTILICHIUSI,"
            . " sum(IF(CategoriaUltima ='UTILICHIUSI',1,0)) as UTILICHIUSI ,"
            . " sum(IF(CategoriaUltima ='KoDefinitivi',1,0)) as DNC, "
            . " sum(IF(irenTot=0,0,1)) as convertitiIren,"
            . " SUM(irenTot) as 'Contratti Prodotti Iren',"
            . " sum(irenOk) as 'Contratti OK Iren',"
            . " Sum(irenKo) as 'Contratti KO Iren',"
            . " sum(valoreMedioIren) as 'VMiren', "
            . " sum(convertito) as convertito,  "
            . " sum(IF(uniTot=0,0,1)) as convertitiUni, "
            . " SUM(uniTot) as 'Contratti Prodotti union', "
            . " sum(uniOk) as 'Contratti OK union',"
            . " Sum(uniKo) as 'Contratti KO union',"
            . " sum(valoreMedioUni) as 'VMunion' "
            . " FROM "
            . " `gestioneLead` "
            . " where dataImport<='$dataMaggioreRicerca' and dataImport>='$dataMinoreRicerca' AND utmCampagna='$campagna' and (duplicato='no' or duplicato='') "
            . " group by "
            . " utmCampagna ";
    //echo $queryGroupMandato;
    $risultatoCampagna = $conn19->query($queryGroupMandato);

    //      echo "<br>";
    if (($rigaCampagna = $risultatoCampagna->fetch_array())) {

        $lead = $rigaCampagna[0];
        $convertitiPleni = $rigaCampagna[1];
        $contrattiProdottiPleni = $rigaCampagna[2];
        $okPleni = $rigaCampagna[3];
        $koPleni = $rigaCampagna[4];
        $convertitiVoda = $rigaCampagna[5];
        $contrattiprodottiVoda = $rigaCampagna[6];
        $okVoda = $rigaCampagna[7];
        $koVoda = $rigaCampagna[8];
        $convertiVivi = $rigaCampagna[9];
        $contrattiProdottiVivi = $rigaCampagna[10];
        $okVivi = $rigaCampagna[11];
        $koVivi = $rigaCampagna[12];
        $valorePleni = $rigaCampagna[13];
        $valoreVoda = $rigaCampagna[14];
        $valoreVivi = $rigaCampagna[15];
        $backlog = $rigaCampagna[16];
        $nonUtiliChiusi = $rigaCampagna[17];
        $utiliChiusi = $rigaCampagna[18];
        $dnc = $rigaCampagna[19];
        $convertiIren = $rigaCampagna[20];
        $contrattiProdottiIren = $rigaCampagna[21];
        $okIren = $rigaCampagna[22];
        $koIren = $rigaCampagna[23];
        $valoreIren = $rigaCampagna[24];

        $contrattiProdottiUnion = $rigaCampagna[27];
        $okUnion = $rigaCampagna[28];
        $koUnion = $rigaCampagna[29];
        $valoreUnion = $rigaCampagna[30];

        //$convertito = $convertitiPleni + $convertitiVoda + $convertiVivi + $convertiIren;
        $convertito = $rigaCampagna[25];
        $contratti = $contrattiProdottiPleni + $contrattiprodottiVoda + $contrattiProdottiVivi + $contrattiProdottiIren + $contrattiProdottiUnion;
        $ok = $okPleni + $okVoda + $okVivi + $okIren + $okUnion;
        $ko = $koPleni + $koVoda + $koVivi + $koIren + $koUnion;
        $valore = $valorePleni + $valoreVoda + $valoreVivi + $valoreIren + $valoreUnion;
    } else {
        $lead = 0;
        $convertito = 0;
        $contratti = 0;
        $ok = 0;
        $ko = 0;
        $valore = 0;
        $backlog = 0;
        $nonUtiliChiusi = 0;
        $utiliChiusi = 0;
        $dnc = 0;
    }

    $convLead = ($lead == 0) ? 0 : round(($convertito / $lead) * 100, 2);
    $cpLead = ($lead == 0) ? 0 : round(($contratti / $lead) * 100, 2);
    $okLead = ($lead == 0) ? 0 : round(($ok / $lead) * 100, 2);
    $mediaCpCon = ($convertito == 0) ? 0 : round($contratti / $convertito, 2);

    $cpl = ($lead == 0) ? 0 : round(round($importo / $lead, 2), 2);
    $cpa = ($convertito == 0) ? 0 : round(round($importo / $convertito, 2), 2);
    $cpc = ($contratti == 0) ? 0 : round(round($importo / $contratti, 2), 2);
    $roi = ($importo == 0) ? 0 : round($valore / $importo, 2);

    $html3 .= "<tr>";
    $html3 .= "<td style='background-color: pink'>$account</td>";
    $html3 .= "<td style='background-color: pink'>$campagna</td>";
    $html3 .= "<td style='background-color: pink'>$provenienza</td>";

    $html3 .= "<td style='border-left: 5px double black'>$importo €</td>";

    $html3 .= "<td style='border-left: 5px double black'>$lead</td>";
    $html3 .= "<td>$convertito</td>";

    $html3 .= "<td style='border-left: 5px double black'>$backlog</td>";
    $html3 .= "<td>$nonUtiliChiusi</td>";
    $html3 .= "<td>$utiliChiusi</td>";
    $html3 .= "<td>$dnc</td>";

    $html3 .= "<td style='border-left: 5px double black'>$contratti</td>";
    $html3 .= "<td>$ok</td>";
    $html3 .= "<td>$ko</td>";

    $html3 .= "<td style='border-left: 5px double black'>$convLead %</td>";
    $html3 .= "<td>$cpLead %</td>";
    $html3 .= "<td>$okLead %</td>";
    $html3 .= "<td>$mediaCpCon</td>";

    $html3 .= "<td style='border-left: 5px double black'>$cpl €</td>";
    $html3 .= "<td>$cpa €</td>";
    $html3 .= "<td>$cpc €</td>";
    $html3 .= "<td>$roi</td>";

    $html3 .= "</tr>";
    if ($account == $prossimo) {
        $importoAgenzia += $importo;
        $leadAgenzia += $lead;
        $convertitiAgenzia += $convertito;
        $contrattiAgenzia += $contratti;
        $okAgenzia += $ok;
        $koAgenzia += $ko;
        $valoreAgenzia += $valore;
        $backlogAgenzia += $backlog;
        $nonUtiliChiusiAgenzia += $nonUtiliChiusi;
        $utiliChiusiAgenzia += $utiliChiusi;
        $dncAgenzia += $dnc;
    } elseif ($prossimo == null) {
        $importoAgenzia += $importo;
        $leadAgenzia += $lead;
        $convertitiAgenzia += $convertito;
        $contrattiAgenzia += $contratti;
        $okAgenzia += $ok;
        $koAgenzia += $ko;
        $valoreAgenzia += $valore;
        $backlogAgenzia += $backlog;
        $nonUtiliChiusiAgenzia += $nonUtiliChiusi;
        $utiliChiusiAgenzia += $utiliChiusi;
        $dncAgenzia += $dnc;
        $convLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($convertitiAgenzia / $leadAgenzia) * 100, 2);
        $cpLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($contrattiAgenzia / $leadAgenzia) * 100, 2);
        $okLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($okAgenzia / $leadAgenzia) * 100, 2);
        $mediaCpConAgenzia = ($convertitiAgenzia == 0) ? 0 : round($okAgenzia / $convertitiAgenzia, 2);

        $cplAgenzia = ($leadAgenzia == 0) ? 0 : round(round($importoAgenzia / $leadAgenzia, 2), 2);
        $cpaAgenzia = ($convertitiAgenzia == 0) ? 0 : round(round($importoAgenzia / $convertitiAgenzia, 2), 2);
        $cpcAgenzia = ($contrattiAgenzia == 0) ? 0 : round(round($importoAgenzia / $contrattiAgenzia, 2), 2);
        $roiAgenzia = ($importoAgenzia == 0) ? 0 : round($valoreAgenzia / $importoAgenzia, 2);

        $html3 .= "<tr>";
        $html3 .= "<td style='background-color: orange'>$account</td>";
        $html3 .= "<td style='background-color: orange'></td>";
        $html3 .= "<td style='background-color: orange'></td>";
        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$importoAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$leadAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$convertitiAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$backlogAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$nonUtiliChiusiAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$utiliChiusiAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$dncAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$contrattiAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$okAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$koAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$convLeadAgenzia %</td>";
        $html3 .= "<td style='background-color: orange'>$cpLeadAgenzia %</td>";
        $html3 .= "<td style='background-color: orange'>$okLeadAgenzia %</td>";
        $html3 .= "<td style='background-color: orange'>$mediaCpConAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$cplAgenzia €</td>";
        $html3 .= "<td style='background-color: orange'>$cpaAgenzia €</td>";
        $html3 .= "<td style='background-color: orange'>$cpcAgenzia €</td>";
        $html3 .= "<td style='background-color: orange'>$roiAgenzia</td>";

        $html3 .= "</tr>";
        $importoTotale += $importoAgenzia;
        $leadTotale += $leadAgenzia;
        $convertitiTotale += $convertitiAgenzia;
        $contrattiTotale += $contrattiAgenzia;
        $okTotale += $okAgenzia;
        $koTotale += $koAgenzia;
        $valoreTotale += $valoreAgenzia;
        $backlogTotale += $backlogAgenzia;
        $nonUtiliChiusiTotale += $nonUtiliChiusiAgenzia;
        $utiliChiusiTotale += $utiliChiusiAgenzia;
        $dncTotale += $dncAgenzia;
        $convLeadTotale = ($leadTotale == 0) ? 0 : round(($convertitiTotale / $leadTotale) * 100, 2);
        $cpLeadTotale = ($leadTotale == 0) ? 0 : round(($contrattiTotale / $leadTotale) * 100, 2);
        $okLeadTotale = ($leadTotale == 0) ? 0 : round(($okTotale / $leadTotale) * 100, 2);
        $mediaCpConTotale = ($convertitiTotale == 0) ? 0 : round($contrattiTotale / $convertitiTotale, 2);

        $cplTotale = ($leadTotale == 0) ? 0 : round(round($importoTotale / $leadTotale, 2), 2);
        $cpaTotale = ($convertitiTotale == 0) ? 0 : round(round($importoTotale / $convertitiTotale, 2), 2);
        $cpcTotale = ($contrattiTotale == 0) ? 0 : round(round($importoTotale / $contrattiTotale, 2), 2);
        $roiTotale = ($importoTotale == 0) ? 0 : round($valoreTotale / $importoTotale, 2);

        $html3 .= "<tr>";
        $html3 .= "<td style='background-color: orangered'>Totale</td>";
        $html3 .= "<td style='background-color: orangered'></td>";
        $html3 .= "<td style='background-color: orangered'></td>";
        $html3 .= "<td style='border-left: 5px double black;background-color: orangered'>$importoTotale</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orangered'>$leadTotale</td>";
        $html3 .= "<td style='background-color: orangered'>$convertitiTotale</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orangered'>$backlogTotale</td>";
        $html3 .= "<td style='background-color: orangered'>$nonUtiliChiusiTotale</td>";
        $html3 .= "<td style='background-color: orangered'>$utiliChiusiTotale</td>";
        $html3 .= "<td style='background-color: orangered'>$dncTotale</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orangered'>$contrattiTotale</td>";
        $html3 .= "<td style='background-color: orangered'>$okTotale</td>";
        $html3 .= "<td style='background-color: orangered'>$koTotale</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orangered'>$convLeadTotale %</td>";
        $html3 .= "<td style='background-color: orangered'>$cpLeadTotale %</td>";
        $html3 .= "<td style='background-color: orangered'>$okLeadTotale %</td>";
        $html3 .= "<td style='background-color: orangered'>$mediaCpConTotale</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orangered'>$cplTotale €</td>";
        $html3 .= "<td style='background-color: orangered'>$cpaTotale €</td>";
        $html3 .= "<td style='background-color: orangered'>$cpcTotale €</td>";
        $html3 .= "<td style='background-color: orangered'>$roiTotale</td>";

        $html3 .= "</tr>";
    } else {
        $importoAgenzia += $importo;
        $leadAgenzia += $lead;
        $convertitiAgenzia += $convertito;
        $contrattiAgenzia += $contratti;
        $okAgenzia += $ok;
        $koAgenzia += $ko;
        $valoreAgenzia += $valore;
        $backlogAgenzia += $backlog;
        $nonUtiliChiusiAgenzia += $nonUtiliChiusi;
        $utiliChiusiAgenzia += $utiliChiusi;
        $dncAgenzia += $dnc;
        $convLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($convertitiAgenzia / $leadAgenzia) * 100, 2);
        $cpLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($contrattiAgenzia / $leadAgenzia) * 100, 2);
        $okLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($okAgenzia / $leadAgenzia) * 100, 2);
        $mediaCpConAgenzia = ($convertitiAgenzia == 0) ? 0 : round($totaleAgenzia / $convertitiAgenzia, 2);

        $cplAgenzia = ($leadAgenzia == 0) ? 0 : round(round($importoAgenzia / $leadAgenzia, 2), 2);
        $cpaAgenzia = ($convertitiAgenzia == 0) ? 0 : round(round($importoAgenzia / $convertitiAgenzia, 2), 2);
        $cpcAgenzia = ($contrattiAgenzia == 0) ? 0 : round(round($importoAgenzia / $contrattiAgenzia, 2), 2);
        $roiAgenzia = ($importoAgenzia == 0) ? 0 : round($valoreAgenzia / $importoAgenzia, 2);

        $html3 .= "<tr>";
        $html3 .= "<td style='background-color: orange'>$account</td>";
        $html3 .= "<td style='background-color: orange'></td>";
        $html3 .= "<td style='background-color: orange'></td>";
        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$importoAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$leadAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$convertitiAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$backlogAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$nonUtiliChiusiAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$utiliChiusiAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$dncAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$contrattiAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$okAgenzia</td>";
        $html3 .= "<td style='background-color: orange'>$koAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$convLeadAgenzia %</td>";
        $html3 .= "<td style='background-color: orange'>$cpLeadAgenzia %</td>";
        $html3 .= "<td style='background-color: orange'>$okLeadAgenzia %</td>";
        $html3 .= "<td style='background-color: orange'>$mediaCpConAgenzia</td>";

        $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$cplAgenzia €</td>";
        $html3 .= "<td style='background-color: orange'>$cpaAgenzia €</td>";
        $html3 .= "<td style='background-color: orange'>$cpcAgenzia €</td>";
        $html3 .= "<td style='background-color: orange'>$roiAgenzia</td>";

        $html3 .= "</tr>";
        $importoTotale += $importoAgenzia;
        $leadTotale += $leadAgenzia;
        $convertitiTotale += $convertitiAgenzia;
        $contrattiTotale += $contrattiAgenzia;
        $okTotale += $okAgenzia;
        $koTotale += $koAgenzia;
        $valoreTotale += $valoreAgenzia;
        $backlogTotale += $backlogAgenzia;
        $nonUtiliChiusiTotale += $nonUtiliChiusiAgenzia;
        $utiliChiusiTotale += $utiliChiusiAgenzia;
        $dncTotale += $dncAgenzia;

        $importoAgenzia = 0;
        $leadAgenzia = 0;
        $convertitiAgenzia = 0;
        $contrattiAgenzia = 0;
        $okAgenzia = 0;
        $koAgenzia = 0;
        $valoreAgenzia = 0;
        $backlogAgenzia = 0;
        $nonUtiliChiusiAgenzia = 0;
        $utiliChiusiAgenzia = 0;
        $dncAgenzia = 0;
    }
}
/**
 * Aggiunta per il Calcolo di AdviceMe
 */
if ($adavaceMe) {
    $queryAdavaceMe = "SELECT source,COUNT(*), "
            . " sum(IF(pleniTot=0,0,1)) as convertiti,SUM(pleniTot) as 'Contratti Prodotti',sum(pleniOk) as 'Contratti OK',Sum(pleniKo) as 'Contratti KO', "
            . " sum(IF(vodaTot=0,0,1)) as convertiti,SUM(vodaTot) as 'Contratti Prodotti',sum(vodaOk) as 'Contratti OK',Sum(vodaKo) as 'Contratti KO', "
            . " sum(IF(viviTot=0,0,1)) as convertiti,SUM(viviTot) as 'Contratti Prodotti',sum(viviOk) as 'Contratti OK',Sum(viviKo) as 'Contratti KO', "
            . "  "
            . " sum(valoreMediaPleni) as 'VMpleni', sum(valoreMediaVivi) as 'VMvivi', sum(valoreMedioVoda) as 'VMvoda', "
            . " sum(IF(CategoriaUltima ='BACKLOG',1,0)) as BACKLOG, sum(IF(CategoriaUltima ='NONUTILICHIUSI',1,0)) as NONUTILICHIUSI, sum(IF(CategoriaUltima ='UTILICHIUSI',1,0)) as UTILICHIUSI , sum(IF(CategoriaUltima ='KoDefinitivi',1,0)) as DNC, "
            . " sum(convertito) as convertito "
            . "FROM `gestioneLead` "
            . "where dataImport<='$dataMaggioreRicerca' and dataImport>='$dataMinoreRicerca' AND agenzia='AdviceMe' "
            . "group by source";
    $risultatoAdavaceMe = $conn19->query($queryAdavaceMe);

    $importoAgenzia = 0;
    $leadAgenzia = 0;
    $convertitiAgenzia = 0;
    $contrattiAgenzia = 0;
    $okAgenzia = 0;
    $koAgenzia = 0;
    $valoreAgenzia = 0;
    $backlogAgenzia = 0;
    $nonUtiliChiusiAgenzia = 0;
    $utiliChiusiAgenzia = 0;
    $dncAgenzia = 0;
    $convLeadAgenzia = 0;
    $cpLeadAgenzia = 0;
    $okLeadAgenzia = 0;
    $mediaCpConAgenzia = 0;

    $cplAgenzia = 0;
    $cpaAgenzia = 0;
    $cpcAgenzia = 0;
    $roiAgenzia = 0;

    while ($rigaAdvaceMe = $risultatoAdavaceMe->fetch_array()) {

        $account = "AdviceMe";
        $campagna = $rigaAdvaceMe[0];
        $provenienza = "Fissa";
        $lead = $rigaAdvaceMe[1];
        $importo = $lead * 0.1;

        $convertitiPleni = $rigaAdvaceMe[2];
        $contrattiProdottiPleni = $rigaAdvaceMe[3];
        $okPleni = $rigaAdvaceMe[4];
        $koPleni = $rigaAdvaceMe[5];
        $convertitiVoda = $rigaAdvaceMe[6];
        $contrattiprodottiVoda = $rigaAdvaceMe[7];
        $okVoda = $rigaAdvaceMe[8];
        $koVoda = $rigaAdvaceMe[9];
        $convertiVivi = $rigaAdvaceMe[10];
        $contrattiProdottiVivi = $rigaAdvaceMe[11];
        $okVivi = $rigaAdvaceMe[12];
        $koVivi = $rigaAdvaceMe[13];
        $valorePleni = $rigaAdvaceMe[14];
        $valoreVoda = $rigaAdvaceMe[15];
        $valoreVivi = $rigaAdvaceMe[16];
        $backlog = $rigaAdvaceMe[17];
        $nonUtiliChiusi = $rigaAdvaceMe[18];
        $utiliChiusi = $rigaAdvaceMe[19];
        $dnc = $rigaAdvaceMe[20];

        $convertito = $rigaAdvaceMe[21];
        $contratti = $contrattiProdottiPleni + $contrattiprodottiVoda + $contrattiProdottiVivi;
        $ok = $okPleni + $okVoda + $okVivi;
        $ko = $koPleni + $koVoda + $koVivi;
        $valore = $valorePleni + $valoreVoda + $valoreVivi;

        $convLead = ($lead == 0) ? 0 : round(($convertito / $lead) * 100, 2);
        $cpLead = ($lead == 0) ? 0 : round(($contratti / $lead) * 100, 2);
        $okLead = ($lead == 0) ? 0 : round(($ok / $lead) * 100, 2);
        $mediaCpCon = ($convertito == 0) ? 0 : round($contratti / $convertito, 2);

        $cpl = ($lead == 0) ? 0 : round(round($importo / $lead, 2), 2);
        $cpa = ($convertito == 0) ? 0 : round(round($importo / $convertito, 2), 2);
        $cpc = ($contratti == 0) ? 0 : round(round($importo / $contratti, 2), 2);
        $roi = ($importo == 0) ? 0 : round($valore / $importo, 2);

        $html3 .= "<tr>";
        $html3 .= "<td style='background-color: pink'>$account</td>";
        $html3 .= "<td style='background-color: pink'>$campagna</td>";
        $html3 .= "<td style='background-color: pink'>$provenienza</td>";

        $html3 .= "<td style='border-left: 5px double black'>$importo €</td>";

        $html3 .= "<td style='border-left: 5px double black'>$lead</td>";
        $html3 .= "<td>$convertito</td>";

        $html3 .= "<td style='border-left: 5px double black'>$backlog</td>";
        $html3 .= "<td>$nonUtiliChiusi</td>";
        $html3 .= "<td>$utiliChiusi</td>";
        $html3 .= "<td>$dnc</td>";

        $html3 .= "<td style='border-left: 5px double black'>$contratti</td>";
        $html3 .= "<td>$ok</td>";
        $html3 .= "<td>$ko</td>";

        $html3 .= "<td style='border-left: 5px double black'>$convLead %</td>";
        $html3 .= "<td>$cpLead %</td>";
        $html3 .= "<td>$okLead %</td>";
        $html3 .= "<td>$mediaCpCon</td>";

        $html3 .= "<td style='border-left: 5px double black'>$cpl €</td>";
        $html3 .= "<td>$cpa €</td>";
        $html3 .= "<td>$cpc €</td>";
        $html3 .= "<td>$roi</td>";

        $html3 .= "</tr>";

        $importoAgenzia += $importo;
        $leadAgenzia += $lead;
        $convertitiAgenzia += $convertito;
        $contrattiAgenzia += $contratti;
        $okAgenzia += $ok;
        $koAgenzia += $ko;
        $valoreAgenzia += $valore;
        $backlogAgenzia += $backlog;
        $nonUtiliChiusiAgenzia += $nonUtiliChiusi;
        $utiliChiusiAgenzia += $utiliChiusi;
        $dncAgenzia += $dnc;
        $convLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($convertitiAgenzia / $leadAgenzia) * 100, 2);
        $cpLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($contrattiAgenzia / $leadAgenzia) * 100, 2);
        $okLeadAgenzia = ($leadAgenzia == 0) ? 0 : round(($okAgenzia / $leadAgenzia) * 100, 2);
        $mediaCpConAgenzia = ($convertitiAgenzia == 0) ? 0 : round($contrattiAgenzia / $convertitiAgenzia, 2);

        $cplAgenzia = ($leadAgenzia == 0) ? 0 : round(round($importoAgenzia / $leadAgenzia, 2), 2);
        $cpaAgenzia = ($convertitiAgenzia == 0) ? 0 : round(round($importoAgenzia / $convertitiAgenzia, 2), 2);
        $cpcAgenzia = ($contrattiAgenzia == 0) ? 0 : round(round($importoAgenzia / $contrattiAgenzia, 2), 2);
        $roiAgenzia = ($importoAgenzia == 0) ? 0 : round($valoreAgenzia / $importoAgenzia, 2);
    }
    $html3 .= "<tr>";
    $html3 .= "<td style='background-color: orange'>$account</td>";
    $html3 .= "<td style='background-color: orange'></td>";
    $html3 .= "<td style='background-color: orange'></td>";
    $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$importoAgenzia</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$leadAgenzia</td>";
    $html3 .= "<td style='background-color: orange'>$convertitiAgenzia</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$backlogAgenzia</td>";
    $html3 .= "<td style='background-color: orange'>$nonUtiliChiusiAgenzia</td>";
    $html3 .= "<td style='background-color: orange'>$utiliChiusiAgenzia</td>";
    $html3 .= "<td style='background-color: orange'>$dncAgenzia</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$contrattiAgenzia</td>";
    $html3 .= "<td style='background-color: orange'>$okAgenzia</td>";
    $html3 .= "<td style='background-color: orange'>$koAgenzia</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$convLeadAgenzia %</td>";
    $html3 .= "<td style='background-color: orange'>$cpLeadAgenzia %</td>";
    $html3 .= "<td style='background-color: orange'>$okLeadAgenzia %</td>";
    $html3 .= "<td style='background-color: orange'>$mediaCpConAgenzia</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: orange'>$cplAgenzia €</td>";
    $html3 .= "<td style='background-color: orange'>$cpaAgenzia €</td>";
    $html3 .= "<td style='background-color: orange'>$cpcAgenzia €</td>";
    $html3 .= "<td style='background-color: orange'>$roiAgenzia</td>";

    $html3 .= "</tr>";

    $importoTotale += $importoAgenzia;
    $leadTotale += $leadAgenzia;
    $convertitiTotale += $convertitiAgenzia;
    $contrattiTotale += $contrattiAgenzia;
    $okTotale += $okAgenzia;
    $koTotale += $koAgenzia;
    $valoreTotale += $valoreAgenzia;
    $backlogTotale += $backlogAgenzia;
    $nonUtiliChiusiTotale += $nonUtiliChiusiAgenzia;
    $utiliChiusiTotale += $utiliChiusiAgenzia;
    $dncTotale += $dncAgenzia;
    $convLeadTotale = ($leadTotale == 0) ? 0 : round(($convertitiTotale / $leadTotale) * 100, 2);
    $cpLeadTotale = ($leadTotale == 0) ? 0 : round(($contrattiTotale / $leadTotale) * 100, 2);
    $okLeadTotale = ($leadTotale == 0) ? 0 : round(($okTotale / $leadTotale) * 100, 2);
    $mediaCpConTotale = ($convertitiTotale == 0) ? 0 : round($contrattiTotale / $convertitiTotale, 2);

    $cplTotale = ($leadTotale == 0) ? 0 : round(round($importoTotale / $leadTotale, 2), 2);
    $cpaTotale = ($convertitiTotale == 0) ? 0 : round(round($importoTotale / $convertitiTotale, 2), 2);
    $cpcTotale = ($contrattiTotale == 0) ? 0 : round(round($importoTotale / $contrattiTotale, 2), 2);
    $roiTotale = ($importoTotale == 0) ? 0 : round($valoreTotale / $importoTotale, 2);

    $html3 .= "<tr>";

    $html3 .= "<td style='background-color: MediumOrchid'>Totale con AdviceMe</td>";
    $html3 .= "<td style='background-color: MediumOrchid'></td>";
    $html3 .= "<td style='background-color: MediumOrchid'></td>";
    $html3 .= "<td style='border-left: 5px double black;background-color: MediumOrchid'>$importoTotale</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: MediumOrchid'>$leadTotale</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$convertitiTotale</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: MediumOrchid'>$backlogTotale</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$nonUtiliChiusiTotale</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$utiliChiusiTotale</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$dncTotale</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: MediumOrchid'>$contrattiTotale</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$okTotale</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$koTotale</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: MediumOrchid'>$convLeadTotale %</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$cpLeadTotale %</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$okLeadTotale %</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$mediaCpConTotale</td>";

    $html3 .= "<td style='border-left: 5px double black;background-color: MediumOrchid'>$cplTotale €</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$cpaTotale €</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$cpcTotale €</td>";
    $html3 .= "<td style='background-color: MediumOrchid'>$roiTotale</td>";

    $html3 .= "</tr>";
}

$html3 .= "</table><br>";
$html3 .= "<br>";
echo $html3;

