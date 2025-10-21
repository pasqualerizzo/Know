<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneGt.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniCruscottoKpi.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj = new connessioneSiscall2();
$conn = $obj->apriConnessioneSiscall2();

$objGt = new connessioneGt();
$connGt = $objGt->apriConnessioneGt();

$objL = new connessioneSiscallLead();
$connL = $objL->apriConnessioneSiscallLead();

$agenzia = filter_input(INPUT_POST, "agenzieSelezionate");
$categoria = filter_input(INPUT_POST, "categoria");
// Converti le date nel formato italiano
$dataMinore = date('Y-m-d 00:00:00', strtotime(filter_input(INPUT_POST, "dataMinore")));
$dataMaggiore = date('Y-m-d 23:59:59', strtotime(filter_input(INPUT_POST, "dataMaggiore")));

//$dataMinore = "2025-02-01";
//$dataMaggiore = "2025-02-21";

$dataMinoreOre = date('Y-m-d', strtotime($dataMinore));
$dataMaggioreOre = date('Y-m-d', strtotime($dataMaggiore));

$dataOggi = date("Y-m-d");

$oggi = date('N');
if ($oggi > 3 and $oggi < 7) {
    $dataMaggioreWeek = date("Y-m-d 23:59:59", strtotime("Wednesday this week"));
    $dataMinoreWeek = date("Y-m-d 00:00:00", strtotime("monday this week "));
} else {
    $dataMaggioreWeek = date("Y-m-d 23:59:59", strtotime("Saturday  previous week"));
    $dataMinoreWeek = date("Y-m-d 00:00:00", strtotime("Thursday previous week "));
}

$siscall2 = recuperoOre($conn, $dataMaggiore, $dataMinore);
$siscallGT = recuperoOre($connGt, $dataMaggiore, $dataMinore);
$siscallLead = recuperoOre($connL, $dataMaggiore, $dataMinore);

$ore16GG = recuperoOre16GG($conn19);

$operatore = elencoOperatoreBmp($connL);

$lead = recuperoLead($conn19, $dataMaggiore, $dataMinore);
$plenitude = recuperoPlenitude($conn19, $dataMaggiore, $dataMinore);
$vivigas = recuperoVivigas($conn19, $dataMaggiore, $dataMinore);
$iren = recuperoIren($conn19, $dataMaggiore, $dataMinore);
$union = recuperoUnion($conn19, $dataMaggiore, $dataMinore);
$enel = recuperoEnel($conn19, $dataMaggiore, $dataMinore);

$leadWeek = recuperoLead($conn19, $dataMaggioreWeek, $dataMinoreWeek);
$plenitudeWeek = recuperoPlenitude($conn19, $dataMaggioreWeek, $dataMinoreWeek);
$vivigasWeek = recuperoVivigas($conn19, $dataMaggioreWeek, $dataMinoreWeek);
$irenWeek = recuperoIren($conn19, $dataMaggioreWeek, $dataMinoreWeek);
$unionWeek = recuperoUnion($conn19, $dataMaggioreWeek, $dataMinoreWeek);
$enelWeek = recuperoEnel($conn19, $dataMaggioreWeek, $dataMinoreWeek);

$plenitudeOut = recuperoPlenitudeOut($conn19, $dataMaggiore, $dataMinore);
$vivigasOut = recuperoVivigasOut($conn19, $dataMaggiore, $dataMinore);
$irenOut = recuperoIrenOut($conn19, $dataMaggiore, $dataMinore);
$unionOut = recuperoUnionOut($conn19, $dataMaggiore, $dataMinore);
$enelOut = recuperoEnelOut($conn19, $dataMaggiore, $dataMinore);

$plenitudeData = recuperoPlenitudeData($conn19, $dataMaggiore, $dataMinore);
$vivigasData = recuperoVivigasData($conn19, $dataMaggiore, $dataMinore);
$irenData = recuperoIrenData($conn19, $dataMaggiore, $dataMinore);
$unionData = recuperoUnionData($conn19, $dataMaggiore, $dataMinore);
$enelData = recuperoEnelData($conn19, $dataMaggiore, $dataMinore);

$riferimento = calcoloRiferimento($conn19, $dataMaggiore, $dataMinore);

echo "Riferimento: " . $riferimento . "<br>";

echo "Giorno della Settimana: " . $oggi . "<br>";
echo "Data Riferimento: " . $dataMaggioreWeek . "<br>";
echo "Data Riferimento: " . $dataMinoreWeek . "<br>";

$riferimentoWeek = calcoloRiferimento($conn19, $dataMaggiore, $dataMinore);

echo "Riferimento Week: " . $riferimentoWeek . "<br>";

$html = "<table class='blueTable' id='tabellaKPI'>";
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th onclick='sortTableKPI(0)'>Operatore</th>";
$html .= "<th onclick='sortTableKPI(1)'>Sede</th>";
$html .= "<th onclick='sortTableNumeroKPI(2)'>Ore</th>";

$html .= "<th onclick='sortTableNumeroKPI(3)'>Chiamate<br> Impostate</th>";
$html .= "<th onclick='sortTableNumeroKPI(4)'>Lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(5)'>Lead<br> Lordo</th>";
$html .= "<th onclick='sortTableNumeroKPI(6)'>Ore Out</th>";
$html .= "<th onclick='sortTableNumeroKPI(7)'>Ok CP OUT</th>";
$html .= "<th onclick='sortTableNumeroKPI(8)'>Resa OUT</th>";

$html .= "<th onclick='sortTableNumeroKPI(9)'>OK CP <br>Data import lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(10)'>% OK CP</th>";
$html .= "<th onclick='sortTableNumeroKPI(11)'>KO CP<br>Data import lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(12)'>Livello<br> Range Selezionato</th>";
$html .= "<th onclick='sortTableNumeroKPI(13)'>Livello Centralino</th>";
$html .= "<th onclick='sortTableNumeroKPI(14)'>Ok <br>Data creazione contr. </th>";
$html .= "<th onclick='sortTableNumeroKPI(15)'>Perc. su Data</th>";
//$html .= "<th>Livello su Data</th>";
$html .= "<th onclick='sortTableNumeroKPI(16)'>Ko <br>Data creazione contr.</th>";
$html .= "<th onclick='sortTableNumeroKPI(17)'>Delta OK</th>";
$html .= "<th onclick='sortTableNumeroKPI(18)'>Energy</th>";
$html .= "<th onclick='sortTableNumeroKPI(19)'>Telco</th>";
$html .= "<th onclick='sortTableNumeroKPI(20)'>Contatti<br> Utili</th>";
$html .= "<th onclick='sortTableNumeroKPI(21)'>Ok<br>Post Vendita</th>";
$html .= "<th onclick='sortTableNumeroKPI(22)'>%<br>Post Vendita</th>";

$html .= "<th onclick='sortTableNumeroKPI(23)'>GG Lavorati<br>[a 15 GG]</th>";
$html .= "<th onclick='sortTableNumeroKPI(24)'>Resa convertito<br>[a 15 GG]</th>";

$html .= "</tr>";
$html .= "</thead>";
$html .= "<tbody>";

foreach ($operatore as $nomeOperatore => $value) {
    $oreIN = 0;
    $oreOut = 0;
    $totaleLead = 0;
    $leadLordo = 0;
    $energetico = 0;
    $telco = 0;
    $okProdotto = 0;
    $koProdotto = 0;
    $totaleProdotto = 0;
    $convertito = 0;
    $Okpvtotale = 0;
    $OkProduzioneOut = 0;
    $OkProduzioneData = 0;
    $koProduzioneData = 0;
    $OkProduzioneWeek = 0;
    $totaleLeadWeek = 0;

    $livello = $value[0];
    $sede = $value[1];
    $userGroup = $value[2];
    if (array_key_exists($nomeOperatore, $siscall2)) {
        $oreIN += $siscall2[$nomeOperatore][0];
        $oreOut += $siscall2[$nomeOperatore][1];
    }

    if (array_key_exists($nomeOperatore, $siscallGT)) {
        $oreIN += $siscallGT[$nomeOperatore][0];
        $oreOut += $siscallGT[$nomeOperatore][1];
    }

    if (array_key_exists($nomeOperatore, $siscallLead)) {
        $oreIN += $siscallLead[$nomeOperatore][0];
        $oreOut += $siscallLead[$nomeOperatore][1];
    }

    if (array_key_exists($nomeOperatore, $lead)) {
        $totaleLead = $lead[$nomeOperatore][0];
        $leadLordo = $lead[$nomeOperatore][1];
        $energetico = $lead[$nomeOperatore][2];
        $telco = $lead[$nomeOperatore][3];
    }

    if (array_key_exists($nomeOperatore, $plenitude)) {
        $okProdotto += $plenitude[$nomeOperatore][0];
        $koProdotto += $plenitude[$nomeOperatore][1];
        $totaleProdotto += $plenitude[$nomeOperatore][2];
        $convertito += $plenitude[$nomeOperatore][3];
        $Okpvtotale += $plenitude[$nomeOperatore][4];
    }

    if (array_key_exists($nomeOperatore, $vivigas)) {
        $okProdotto += $vivigas[$nomeOperatore][0];
        $koProdotto += $vivigas[$nomeOperatore][1];
        $totaleProdotto += $vivigas[$nomeOperatore][2];
        $convertito += $vivigas[$nomeOperatore][3];
        $Okpvtotale += $vivigas[$nomeOperatore][4];
    }

    if (array_key_exists($nomeOperatore, $iren)) {
        $okProdotto += $iren[$nomeOperatore][0];
        $koProdotto += $iren[$nomeOperatore][1];
        $totaleProdotto += $iren[$nomeOperatore][2];
        $convertito += $iren[$nomeOperatore][3];
        $Okpvtotale += $iren[$nomeOperatore][4];
    }

    if (array_key_exists($nomeOperatore, $union)) {
        $okProdotto += $union[$nomeOperatore][0];
        $koProdotto += $union[$nomeOperatore][1];
        $totaleProdotto += $union[$nomeOperatore][2];
        $convertito += $union[$nomeOperatore][3];
        $Okpvtotale += $union[$nomeOperatore][4];
    }


    if (array_key_exists($nomeOperatore, $enel)) {
        $okProdotto += $enel[$nomeOperatore][0];
        $koProdotto += $enel[$nomeOperatore][1];
        $totaleProdotto += $enel[$nomeOperatore][2];
        $convertito += $enel[$nomeOperatore][3];
        $Okpvtotale += $enel[$nomeOperatore][4];
    }


    if (array_key_exists($nomeOperatore, $enelOut)) {
        $OkProduzioneOut += $enelOut[$nomeOperatore][0];
    }
    if (array_key_exists($nomeOperatore, $vivigasOut)) {
        $OkProduzioneOut += $vivigasOut[$nomeOperatore][0];
    }
    if (array_key_exists($nomeOperatore, $plenitudeOut)) {
        $OkProduzioneOut += $plenitudeOut[$nomeOperatore][0];
    }
    if (array_key_exists($nomeOperatore, $unionOut)) {
        $OkProduzioneOut += $unionOut[$nomeOperatore][0];
    }
    if (array_key_exists($nomeOperatore, $irenOut)) {
        $OkProduzioneOut += $irenOut[$nomeOperatore][0];
    }

    if (array_key_exists($nomeOperatore, $enelData)) {
        $OkProduzioneData += $enelData[$nomeOperatore][0];
        $koProduzioneData += $enelData[$nomeOperatore][1];
    }
    if (array_key_exists($nomeOperatore, $vivigasData)) {
        $OkProduzioneData += $vivigasData[$nomeOperatore][0];
        $koProduzioneData += $vivigasData[$nomeOperatore][1];
    }
    if (array_key_exists($nomeOperatore, $plenitudeData)) {
        $OkProduzioneData += $plenitudeData[$nomeOperatore][0];
        $koProduzioneData += $plenitudeData[$nomeOperatore][1];
    }
    if (array_key_exists($nomeOperatore, $unionData)) {
        $OkProduzioneData += $unionData[$nomeOperatore][1];
    }
    if (array_key_exists($nomeOperatore, $irenData)) {
        $OkProduzioneData += $irenData[$nomeOperatore][0];
        $koProduzioneData += $irenData[$nomeOperatore][1];
    }


    $percentualeProdotto = ($totaleLead == 0) ? 0 : round(($totaleProdotto / $totaleLead) * 100, 2);
    $percentualeConvertito = ($totaleLead == 0) ? 0 : round(($convertito / $totaleLead) * 100, 2);
    $percentualeOk = ($totaleLead == 0) ? 0 : round(($okProdotto / $totaleLead) * 100, 2);

    $differenza = $percentualeOk - $riferimento;
    if ($userGroup == 'INB_BMP') {
        $livelloRange = 2;
        $numeroChiamate = 15;
        $colorRange = "grey";
    } else {

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
    }
    if ($livelloRange < 3) {
        if ($userGroup == 'INB_BMP') {
            $livelloRange = 2;
            $numeroChiamate = 15;
            $colorRange = "grey";
        } else {
            $resaOutbound = ($oreOut == 0) ? 0 : round(($OkProduzioneOut / $oreOut), 2);
            if ($resaOutbound > 0.2) {
                $livelloRange = 3;
                $numeroChiamate = 20;
                $colorRange = "olive";
            } else {
                
            }
        }
    }

    if (array_key_exists($nomeOperatore, $enelWeek)) {
        $OkProduzioneWeek += $enelWeek[$nomeOperatore][0];
    }
    if (array_key_exists($nomeOperatore, $vivigasWeek)) {
        $OkProduzioneWeek += $vivigasWeek[$nomeOperatore][0];
    }
    if (array_key_exists($nomeOperatore, $plenitudeWeek)) {
        $OkProduzioneWeek += $plenitudeWeek[$nomeOperatore][0];
    }
    if (array_key_exists($nomeOperatore, $unionWeek)) {
        $OkProduzioneWeek += $unionWeek[$nomeOperatore][0];
    }
    if (array_key_exists($nomeOperatore, $irenWeek)) {
        $OkProduzioneWeek += $irenWeek[$nomeOperatore][0];
    }

    if (array_key_exists($nomeOperatore, $leadWeek)) {
        $totaleLeadWeek = $leadWeek[$nomeOperatore][0];
    }

    $mediaWeek = ($totaleLeadWeek == 0) ? 0 : round(($OkProduzioneWeek / $totaleLeadWeek) * 100, 2);

    $differenzaWeek = $mediaWeek - $riferimentoWeek;
    if ($userGroup == 'INB_BMP') {
         $livelloVicidial = 2;
        $numeroChiamate = 15;
        $color = "grey";
    } else {
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

        if ($livelloVicidial < 3) {
            $resaOutbound = ($oreOut == 0) ? 0 : round(($OkProduzioneOut / $oreOut), 2);
            if ($resaOutbound > 0.2) {
                $livelloVicidial = 3;
                $numeroChiamate = 20;
                $color = "olive";
            } else {
                
            }
        }
    }

    $resaOut = ($oreOut == 0) ? 0 : $resaOut = round(($OkProduzioneOut / $oreOut), 2);

    $percentualeOkData = ($totaleLead == 0) ? 0 : round(($OkProduzioneData / $totaleLead) * 100, 2);

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
//
    $differenzaUtili = 0;
    $colorRangeUtili = 0;

    $contattiUtili = ($oreIN == 0) ? 0 : round($totaleLead / $oreIN, 2);

    $percOkPv = ($OkProduzioneData != 0) ? round(($Okpvtotale / $OkProduzioneData) * 100, 2) : 0;

    if ($contattiUtili <= 2.5) {
//$numeroChiamate = 10;
        $colorRangeUtili = "green";
    } elseif ($contattiUtili > 2.5 and $contattiUtili <= 3.5) {
//$numeroChiamate = 15;
        $colorRangeUtili = "orange";
    } elseif ($contattiUtili > 3.5) {
//$numeroChiamate = 20;
        $colorRangeUtili = "red";
    }

    if (array_key_exists($nomeOperatore, $ore16GG)) {
        $oregglavin = $ore16GG[$nomeOperatore][1];
        $gglavin = $ore16GG[$nomeOperatore][0];
    } else {
        $oregglavin = 0;
        $gglavin = 0;
    }


    if ($gglavin != 0) {
        $resavonv = ($convertito / $gglavin) * 100;
        $resavonv = number_format($resavonv, 2) . "%";
    } else {
        $resavonv = "0"; // Oppure un messaggio personalizzato, es. "N/A"
    }

    if ($oreIN == 0 && $okProdotto == 0 && $OkProduzioneData == 0) {
        
    } else {
        $html .= "<tr>";
        $html .= "<td>$nomeOperatore</td>";
        $html .= "<td>$sede</td>";
        $html .= "<td>$oreIN</td>";
        $html .= "<td>$numeroChiamate</td>";

        $html .= "<td>$totaleLead</td>";

        $html .= "<td>$leadLordo</td>";

        $html .= "<td>$oreOut</td>";
        $html .= "<td>$OkProduzioneOut</td>";
        $html .= "<td>$resaOut</td>";

        $html .= "<td>$okProdotto</td>";
        $html .= "<td>$percentualeOk</td>";
        $html .= "<td>$koProdotto</td>";
        $html .= "<td style='background-color:$colorRange'> $livelloRange</td>";
        $html .= "<td style='background-color:$color'>$livelloVicidial</td>";
        $html .= "<td>$OkProduzioneData</td>";
        $html .= "<td>$percentualeOkData</td>";
//$html .= "<td style='background-color:$colorRangeData'>$livelloRangeData</td>";
        $html .= "<td>$koProduzioneData</td>";
        $delta = $okProdotto - $OkProduzioneData;
        $html .= "<td>$delta</td>";
        $html .= "<td>$energetico</td>";
        $html .= "<td>$telco</td>";
        $html .= "<td style='background-color:$colorRangeUtili'>$contattiUtili</td>";
        $html .= "<td>$Okpvtotale</td>";
        $html .= "<td>$percOkPv </td>";
        $html .= "<td>$gglavin</td>";
        $html .= "<td>$resavonv</td>";
        $html .= "</tr>";
    }
}



$html .= "</tbody>";
$html .= "</table>";

echo $html;

$conn19->close();
?>
