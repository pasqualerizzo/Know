<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj = new connessioneSiscall2();
$conn = $obj->apriConnessioneSiscall2();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$agenzia = filter_input(INPUT_POST, "agenzieSelezionate");

// Converti le date nel formato italiano
$dataMinore = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggiore = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$dataOggi = date("Y-m-d");

// Inizializza i totali
$totale_lead_Totale = 0;
$totalecp_Totale = 0;
$okcp_Totale = 0;
$kopc_Totale = 0;
$convertito_totale = 0;
$oreTotale = 0;
$okDataTotale = 0;
$koDataTotale = 0;
$sommaLivelloRange = 0;
$conteggioLivelloRange = 0;
$sommaLivelloVicidial = 0;
$leadLordoTotale = 0;

// Decodifica le agenzie selezionate
//$agenzia = json_decode($_POST["agenzia"], true);
//$queryAgenzia = "";
//$lunghezza = count($agenzia);

//if ($lunghezza == 1) {
//    $queryAgenzia .= " AND agenzia='$agenzia[0]' ";
//} else {
//    for ($i = 0;
//            $i < $lunghezza;
//            $i++) {
//        if ($i == 0) {
//            $queryAgenzia .= " AND ( ";
//        }
//        $queryAgenzia .= " agenzia='$agenzia[$i]' ";
//        if ($i == ($lunghezza - 1)) {
//            $queryAgenzia .= " ) ";
//        } else {
//            $queryAgenzia .= " OR ";
//        }
//    }
//}

// Genera l'HTML della tabella
$html = "<table class='blueTable' id='tabellaKPI'>";
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th onclick='sortTableKPI(0)'>Operatore</th>";
$html .= "<th onclick='sortTableKPI(1)'>Sede</th>";
$html .= "<th onclick='sortTableNumeroKPI(2)'>Ore</th>";

$html .= "<th onclick='sortTableNumeroKPI(3)'>Chiamate<br> Impostate</th>";
$html .= "<th onclick='sortTableNumeroKPI(4)'>Lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(5)'>Lead<br> Lordo</th>";
$html .= "<th onclick='sortTableNumeroKPI(6)'>Totale CP</th>";
$html .= "<th onclick='sortTableNumeroKPI(7)'>% CP_Lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(8)'>Convertito</th>";
$html .= "<th onclick='sortTableNumeroKPI(9)'>% Convertito</th>";
$html .= "<th onclick='sortTableNumeroKPI(10)'>OK CP</th>";
$html .= "<th onclick='sortTableNumeroKPI(11)'>% OK CP</th>";
$html .= "<th onclick='sortTableNumeroKPI(12)'>KO CP</th>";
$html .= "<th onclick='sortTableNumeroKPI(13)'>Livello<br> Range Selezionato</th>";
$html .= "<th onclick='sortTableNumeroKPI(14)'>Livello Vicidial</th>";
$html .= "<th onclick='sortTableNumeroKPI(15)'>Ok su Data</th>";
$html .= "<th onclick='sortTableNumeroKPI(16)'>Perc. su Data</th>";
//$html .= "<th>Livello su Data</th>";
$html .= "<th onclick='sortTableNumeroKPI(17)'>Ko su Data</th>";
$html .= "<th onclick='sortTableNumeroKPI(18)'>Delta OK</th>";

$html .= "</tr>";
$html .= "</thead>";
$html .= "<tbody>";
/*
 * Calcolo della percentuale di riferimento sul range
 */

$queryRiferimento = "SELECT"
        . " count(idSponsorizzata) as Lead, "
        . " (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)+ SUM(`uniOk`)) AS OK_CP"
        . " FROM "
        . " `gestioneLead` "
        . " WHERE "
        . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
        . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
        . " AND dataImport <= '$dataMaggiore' "
        . " AND dataImport >= '$dataMinore' "
        . " and idSponsorizzata like 'GCL%' "
        . " and (duplicato='no' or duplicato='')";

$risultatoRiferimento = $conn19->query($queryRiferimento);
if (($rigaRiferimento = $risultatoRiferimento->fetch_array())) {
    $leadRiferimento = $rigaRiferimento[0];
    $okRiferimerimento = $rigaRiferimento[1];

    $riferimento = ($leadRiferimento == 0) ? 0 : round(($okRiferimerimento / $leadRiferimento) * 100, 2);
}

//echo $riferimento;



$oggi = date('N');
if ($oggi > 3 and $oggi < 7) {
    $dataMaggioreWeek = date("Y-m-d 23:59:59", strtotime("Wednesday this week"));
    $dataMinoreWeek = date("Y-m-d 00:00:00", strtotime("monday this week "));
} else {
    $dataMaggioreWeek = date("Y-m-d 23:59:59", strtotime("Saturday  previous week"));
    $dataMinoreWeek = date("Y-m-d 00:00:00", strtotime("Thursday previous week "));
}
echo $oggi . "<br>";
echo "Riferimento: " . $dataMaggioreWeek . "<br>";
echo "Riferimento: " . $dataMinoreWeek;

$queryRiferimentoWeek = "SELECT"
        . " COUNT(`idSponsorizzata`) AS Lead, "
        . " (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)+ SUM(`uniOk`)) AS OK_CP "
        . " FROM "
        . " `gestioneLead` "
        . " WHERE "
        . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
        . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
        . " AND dataImport <= '$dataMaggioreWeek' "
        . " AND dataImport >= '$dataMinoreWeek' "
        . " and idSponsorizzata like 'GCL%' "
        . " and (duplicato='no' or duplicato='')";

$risultatoRiferimentoWeek = $conn19->query($queryRiferimentoWeek);
if (($rigaRiferimentoWeek = $risultatoRiferimentoWeek->fetch_array())) {
    $leadRiferimentoWeek = $rigaRiferimentoWeek[0];
    $okRiferimerimentoWeek = $rigaRiferimentoWeek[1];
    $riferimentoWeek = ($leadRiferimentoWeek == 0) ? 0 : round(($okRiferimerimentoWeek / $leadRiferimentoWeek) * 100, 2);
}


/*
 * Calcolo delle righe risultati degli operatori
 */

$queryleadokfinale = "SELECT"
        . " REPLACE(`gestitoDa`, 'enel', '') AS operatore, "
        . " sum(if(duplicato='no',1,0)) AS Lead, "
        . " (SUM(`pleniTot`) + SUM(`vodaTot`) + SUM(`viviTot`) + SUM(`irenTot`))  AS TOT_CP, "
        . " (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)+ SUM(`uniOk`)) AS OK_CP, "
        . " (SUM(`pleniKo`) + SUM(`vodaKo`) + SUM(`viviKo`) + SUM(`irenKo`)+ SUM(`uniKo`)) AS KO_CP,  "
        . " SUM(`convertito`)  AS CONVERTITO, "
        . " count(idSponsorizzata) as LeadLordo "
        . " FROM "
        . " `gestioneLead` "
        . " WHERE "
        . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
        . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
        . " AND dataImport <= '$dataMaggiore' "
        . " AND dataImport >= '$dataMinore' "
        . " and idSponsorizzata like 'GCL%' "
//        . " and (duplicato='no' or duplicato='') "
//        . " $queryAgenzia "
        . " GROUP BY "
        . " REPLACE(`gestitoDa`, 'enel', '') "
        . " ORDER BY "
        . " `valoreMedioIren` ASC";
//echo $queryleadokfinale;
$risultatoCrm = $conn19->query($queryleadokfinale);

while ($rigaCRM = $risultatoCrm->fetch_array()) {
    $operatore = $rigaCRM[0];
    $totaleLead = round($rigaCRM[1], 2);
    $totaleProdotto = round($rigaCRM[2], 2);
    $okProdotto = round($rigaCRM[3], 2);
    $koProdotto = round($rigaCRM[4], 2);
    $convertito = round($rigaCRM[5], 2);
    $leadLordo = round($rigaCRM[6], 2);

    $percentualeProdotto = ($totaleLead == 0) ? 0 : round(($totaleProdotto / $totaleLead) * 100, 2);
    $percentualeConvertito = ($totaleLead == 0) ? 0 : round(($convertito / $totaleLead) * 100, 2);
    $percentualeOk = ($totaleLead == 0) ? 0 : round(($okProdotto / $totaleLead) * 100, 2);

    $differenza = $percentualeOk - $riferimento;
    if ($differenza <= -5) {
        $livelloRange = 1;
        $numeroChiamate = 10;
        $colorRange = "tomato";
    } elseif ($differenza > -5 and $differenza <= 0) {
        $livelloRange = 2;
        $numeroChiamate = 15;
        $colorRange = "yellow";
    } elseif ($differenza > 0 and $differenza <= 5) {
        $livelloRange = 3;
        $numeroChiamate = 20;
        $colorRange = "green";
    } elseif ($differenza > 5 and $differenza <= 10) {
        $livelloRange = 4;
        $numeroChiamate = 0;
        $colorRange = "silver";
    } elseif ($differenza > 10) {
        $livelloRange = 5;
        $numeroChiamate = 0;
        $colorRange = "gold";
    }




    $queryPleni = "SELECT "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK', "
            . " SUM(IF(aggiuntaPlenitude.fasePDA='KO',1,0)) as 'KO'  "
            . " FROM "
            . " `plenitude` "
            . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and plenitude.data<='$dataMaggiore' "
            . " and plenitude.data>='$dataMinore' "
            . " and creatoDa='$operatore' "
            . " group by "
            . " creatoDa";

    $risultatoPleni = $conn19->query($queryPleni);
    if (($rigaPleni = $risultatoPleni->fetch_array())) {
        $okPleni = $rigaPleni[0];
        $koPleni = $rigaPleni[1];
    } else {
        $okPleni = 0;
        $koPleni = 0;
    }

    $queryvivigas = "SELECT "
            . " SUM(IF(aggiuntaVivigas.fasePDA='OK',1,0)) as 'OK', "
            . " SUM(IF(aggiuntaVivigas.fasePDA='KO',1,0)) as 'KO'  "
            . " FROM "
            . " `vivigas` "
            . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and vivigas.data<='$dataMaggiore' "
            . " and vivigas.data>='$dataMinore' "
            . " and creatoDa='$operatore' "
            . " group by "
            . " creatoDa";

    $risultatoVivi = $conn19->query($queryvivigas);
    if (($rigaVivi = $risultatoVivi->fetch_array())) {
        $okVivi = $rigaVivi[0];
        $koVivi = $rigaVivi[1];
    } else {
        $okVivi = 0;
        $koVivi = 0;
    }

    $queryIren = "SELECT "
            . " SUM(IF(aggiuntaIren.fasePDA='OK',1,0)) as 'OK', "
            . " SUM(IF(aggiuntaIren.fasePDA='KO',1,0)) as 'KO'  "
            . " FROM "
            . " `iren` "
            . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and iren.data<='$dataMaggiore' "
            . " and iren.data>='$dataMinore' "
            . " and creatoDa='$operatore' "
            . " group by "
            . " creatoDa";

    $risultatoIren = $conn19->query($queryIren);
    if (($rigaIren = $risultatoIren->fetch_array())) {
        $okIren = $rigaIren[0];
        $koIren = $rigaIren[1];
    } else {
        $okIren = 0;
        $koIren = 0;
    }

    $queryUnion = "SELECT "
            . " SUM(IF(aggiuntaUnion.fasePDA='OK',1,0)) as 'OK', "
            . " SUM(IF(aggiuntaUnion.fasePDA='KO',1,0)) as 'KO'  "
            . " FROM "
            . " `union` "
            . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and union.data<='$dataMaggiore' "
            . " and union.data>='$dataMinore' "
            . " and creatoDa='$operatore' "
            . " group by "
            . " creatoDa";

    $risultatoUnion = $conn19->query($queryUnion);
    if (($rigaUnion = $risultatoUnion->fetch_array())) {
        $okUnion = $rigaUnion[0];
        $koUnion = $rigaUnion[1];
    } else {
        $okUnion = 0;
        $koUnion = 0;
    }


    $queryVoda = "SELECT "
            . " SUM(IF(aggiuntaVodafone.fasePDA='OK',1,0)) as 'OK', "
            . " SUM(IF(aggiuntaVodafone.fasePDA='KO',1,0)) as 'KO'  "
            . " FROM "
            . " `vodafone` "
            . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
            . " where "
            . " idGestioneLead like 'GCL%' "
            . " and vodafone.dataVendita<='$dataMaggiore' "
            . " and vodafone.dataVendita>='$dataMinore' "
            . " and creatoDa='$operatore' "
            . " group by "
            . " creatoDa";

    $risultatoVoda = $conn19->query($queryVoda);
    if (($rigaVoda = $risultatoVoda->fetch_array())) {
        $okVoda = $rigaVoda[0];
        $koVoda = $rigaVoda[1];
    } else {
        $okVoda = 0;
        $koVoda = 0;
    }


    $okData = $okPleni + $okVivi + $okIren + $okVoda + $okUnion;
    $koData = $koPleni + $koVivi + $koIren + $koVoda + $koUnion;

    $queryleadokfinaleWeek = "SELECT"
            . " sum(if(duplicato='no',1,0)) AS Lead, "
            . " (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)+ SUM(`uniOk`)) AS OK_CP "
            . " FROM "
            . " `gestioneLead` "
            . " WHERE "
            . " `gestitoDa` NOT IN ('', 'VDAD', '6666') "
            . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
            . " AND dataImport <= '$dataMaggioreWeek' "
            . " AND dataImport >= '$dataMinoreWeek' "
            . " and idSponsorizzata like 'GCL%' "
            . " and REPLACE(`gestitoDa`, 'enel', '') ='$operatore' "
            . " and (duplicato='no' or duplicato='')";
//            . " $queryAgenzia ";

    $risultatoCrmWeek = $conn19->query($queryleadokfinaleWeek);
    if (($rigaWeek = $risultatoCrmWeek->fetch_array())) {
        $leadWeek = $rigaWeek[0];
        $okWeek = $rigaWeek[1];
        $mediaWeek = ($leadWeek == 0) ? 0 : round(($okWeek / $leadWeek) * 100, 2);
    }

    $differenzaWeek = $mediaWeek - $riferimentoWeek;
    if ($differenzaWeek <= -5) {
        $livelloVicidial = 1;
        $numeroChiamate = 10;
        $color = "tomato";
    } elseif ($differenzaWeek > -5 and $differenzaWeek <= 0) {
        $livelloVicidial = 2;
        $numeroChiamate = 15;
        $color = "yellow";
    } elseif ($differenzaWeek > 0 and $differenzaWeek <= 5) {
        $livelloVicidial = 3;
        $numeroChiamate = 20;
        $color = "green";
    } elseif ($differenzaWeek > 5 and $differenzaWeek <= 10) {
        $livelloVicidial = 4;
        $numeroChiamate = 0;
        $color = "silver";
    } elseif ($differenzaWeek > 10) {
        $livelloVicidial = 5;
        $numeroChiamate = 0;
        $color = "gold";
    }
    
    $queryOre = "SELECT "
            
        . "user_level AS livello, "
        . "territory AS citta, "
        
        . "campaign_description AS mandato,"
        
        . "SUM(pause_sec+wait_sec+talk_sec+dispo_sec) AS 'numero'"
        
        . "FROM vicidial_agent_log AS v "
        . "INNER JOIN vicidial_users AS operatore ON v.user=operatore.user "
        . "INNER JOIN vicidial_campaigns as campagna ON v.campaign_id=campagna.campaign_id "
        . "WHERE event_time >='$dataMinore' and "
            . " event_time <='$dataMaggiore' and "
            . " operatore.full_name ='$operatore' "
            . " ";

       echo $queryOre;
    

//    $queryOre = "SELECT "
//            . " sum(numero)/3600, "
//            . " sede "
//            . " FROM "
//            . " `stringheTotale` "
//            . " where "
//            . " giorno >='$dataMinore' "
//            . " and giorno<='$dataMaggiore' "
//            . " and nomeCompleto='$operatore' "
//            . " and mandato='Lead Inbound'";
//
//    $risultatoOre = $conn19->query($queryOre);
//    if (($rigaOre = $risultatoOre->fetch_array())) {
//        $ore = round($rigaOre[0], 2);
//        $sede = $rigaOre[1];
//    }
    
     $risultatoOre = $conn->query($queryOre);
    if (($rigaOre = $risultatoOre->fetch_array())) {
    $ore = round($rigaOre['numero']/3600, 2);
    $sede = $rigaOre['citta'];
    $livello= $rigaOre['livello'];
    
    }
    
    
    $percentualeOkData = ($totaleLead == 0) ? 0 : round(($okData / $totaleLead) * 100, 2);

    $differenzaData = $percentualeOkData - $riferimento;
    if ($differenza <= -5) {
        $livelloRangeData = 1;
        //$numeroChiamate = 10;
        $colorRangeData = "tomato";
    } elseif ($differenza > -5 and $differenza <= 0) {
        $livelloRangeData = 2;
        //$numeroChiamate = 15;
        $colorRangeData = "yellow";
    } elseif ($differenza > 0 and $differenza <= 5) {
        $livelloRangeData = 3;
        //$numeroChiamate = 20;
        $colorRangeData = "green";
    } elseif ($differenza > 5 and $differenza <= 10) {
        $livelloRangeData = 4;
        //$numeroChiamate = 0;
        $colorRangeData = "silver";
    } elseif ($differenza > 10) {
        $livelloRangeData = 5;
        //$numeroChiamate = 0;
        $colorRangeData = "gold";
    }

    $html .= "<tr>";
    $html .= "<td>$operatore</td>";
    $html .= "<td>$sede</td>";
    $html .= "<td>$ore</td>";
    $html .= "<td>$numeroChiamate</td>";

    $html .= "<td>$totaleLead</td>";

    $html .= "<td>$leadLordo</td>";
    $html .= "<td>$totaleProdotto</td>";
    $html .= "<td>$percentualeProdotto</td>";
    $html .= "<td>$convertito</td>";
    $html .= "<td>$percentualeConvertito</td>";
    $html .= "<td>$okProdotto</td>";
    $html .= "<td>$percentualeOk</td>";
    $html .= "<td>$koProdotto</td>";
    $html .= "<td style='background-color:$colorRange'> $livelloRange</td>";
    $html .= "<td style='background-color:$color'>$livelloVicidial</td>";
    $html .= "<td>$okData</td>";
    $html .= "<td>$percentualeOkData</td>";
    //$html .= "<td style='background-color:$colorRangeData'>$livelloRangeData</td>";
    $html .= "<td>$koData</td>";
    $delta = $okProdotto - $okData;
    $html .= "<td>$delta</td>";

    $html .= "</tr>";

//    $totale_lead_Totale += $totaleLead;
//    $totalecp_Totale += $totaleProdotto;
//    $okcp_Totale += $okProdotto;
//    $kopc_Totale += $koProdotto;
//    $convertito_totale += $convertito;
//    $oreTotale += $ore;
//    $okDataTotale += $okData;
//    $koDataTotale += $koData;
//    $sommaLivelloRange += $livelloRange;
//    $conteggioLivelloRange++;
//    $sommaLivelloVicidial += $livelloVicidial;
//    $leadLordoTotale += $leadLordo;
}

// Calcolo dei totali complessivi
//$percentuale_cp_totale = ($totale_lead_Totale > 0) ? ($totalecp_Totale / $totale_lead_Totale) * 100 : 0;
//$percentuale_convertito_totale = ($totale_lead_Totale > 0) ? ($convertito_totale / $totale_lead_Totale) * 100 : 0;
//$percentuale_okcp_totale = ($totale_lead_Totale > 0) ? ($okcp_Totale / $totale_lead_Totale) * 100 : 0;
//$percentuale_okcp_totaleData = ($totale_lead_Totale > 0) ? round(($okDataTotale / $totale_lead_Totale) * 100, 2) : 0;
//$mediaLivelloRange = round($sommaLivelloRange / $conteggioLivelloRange, 1);
//$mediaLivelloVicidial = round($sommaLivelloVicidial / $conteggioLivelloRange, 1);

// Riga dei totali complessivi
//$html .= "<tr style='background-color: orange; font-weight: bold;'>";
//$html .= "<td>TOTALE</td>";
//$html .= "<td>-</td>";
//$html .= "<td>" . round($oreTotale, 2) . "</td>";
//$html .= "<td>-</td>";
//$html .= "<td>$totale_lead_Totale</td>";
//$html .= "<td>$leadLordoTotale</td>";
//$html .= "<td>$totalecp_Totale</td>";
//$html .= "<td>" . round($percentuale_cp_totale, 2) . "%</td>";
//$html .= "<td>$convertito_totale</td>";
//$html .= "<td>" . round($percentuale_convertito_totale, 2) . "%</td>";
//$html .= "<td>$okcp_Totale</td>";
//$html .= "<td>" . round($percentuale_okcp_totale, 2) . "%</td>";
//$html .= "<td>$kopc_Totale</td>";
//$html .= "<td>$mediaLivelloRange</td>"; // Lasciare vuoto per i totali
//$html .= "<td>$mediaLivelloVicidial</td>"; // Lasciare vuoto per i totali
//$html .= "<td>$okDataTotale</td>";
//$html .= "<td>$percentuale_okcp_totaleData%</td>";
////$html .= "<td>-</td>";
//$html .= "<td>$koDataTotale</td>";
//$delta = $okcp_Totale - $okDataTotale;
//$html .= "<td>$delta</td>";
//$html .= "</tr>";

$html .= "</tbody>";
$html .= "</table>";



echo $html;

$conn19->close();
?>
