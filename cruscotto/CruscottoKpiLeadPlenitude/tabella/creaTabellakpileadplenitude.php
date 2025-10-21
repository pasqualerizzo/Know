<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneGt.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniCruscottoKpiPlenitude.php";





$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj = new connessioneSiscall2();
$conn = $obj->apriConnessioneSiscall2();

$objGt = new connessioneGt();
$connGt = $objGt->apriConnessioneGt();

$objL = new connessioneSiscallLead();
$connL = $objL->apriConnessioneSiscallLead();


// Converti le date nel formato italiano
$dataMinore = date('Y-m-d 00:00:00', strtotime(filter_input(INPUT_POST, "dataMinore")));
$dataMaggiore = date('Y-m-d 23:59:59', strtotime(filter_input(INPUT_POST, "dataMaggiore")));


$sedi = isset($_POST["sede"]) ? json_decode($_POST["sede"], true) : [];

// Modalità possibile: 'tutte', 'singola', 'multipla'
// Puoi anche gestirla via POST:
$modalita = $_POST['modalita'] ?? 'tutte';

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

$operatore = elencoOperatore($connL);

$lead = recuperoLead($conn19, $dataMaggiore, $dataMinore);
$lead15gg = recuperoLead($conn19, date('Y-m-d', strtotime(" -1 days")), date('Y-m-d', strtotime(" -16 days")));
$lead21gg = recuperoLead($conn19, date('Y-m-d', strtotime(" -1 days")), date('Y-m-d', strtotime(" -22 days")));

$plenitude = recuperoPlenitude($conn19, $dataMaggiore, $dataMinore);


$leadWeek = recuperoLead($conn19, $dataMaggioreWeek, $dataMinoreWeek);
$plenitudeWeek = recuperoPlenitude($conn19, $dataMaggioreWeek, $dataMinoreWeek);


$plenitudeOut = recuperoPlenitudeOut($conn19, $dataMaggiore, $dataMinore);


$plenitudeData = recuperoPlenitudeData($conn19, $dataMaggiore, $dataMinore);


$plenitude15gg = recuperoPlenitudeData($conn19, date('Y-m-d', strtotime(" -1 days")), date('Y-m-d', strtotime(" -16 days")));


$plenitude21gg = recuperoPlenitudeData($conn19, date('Y-m-d', strtotime(" -1 days")), date('Y-m-d', strtotime(" -22 days")));


$riferimento = calcoloRiferimento($conn19, $dataMaggiore, $dataMinore);


// Converti le date nel primo giorno del mese
$meseMinore = date('Y-m-01', strtotime($dataMinore));
$meseMaggiore = date('Y-m-01', strtotime($dataMaggiore));

$valoreMedio = 0; // Valore di default

$queryvaloremedio = "SELECT media 
                    FROM `mediaPraticaMese` 
                    WHERE mese >= '$meseMinore' 
                    AND mese <= '$meseMaggiore' 
                    AND mandato = 'Plenitude'
                    ORDER BY `id` DESC
                    LIMIT 1"; // Prendi solo l'ultimo record

$risultatovaloremedio = $conn19->query($queryvaloremedio);

if ($risultatovaloremedio && $risultatovaloremedio->num_rows > 0) {
    $riga = $risultatovaloremedio->fetch_assoc();
    $valoreMedio = $riga['media'] ?? 0;
}



echo "Riferimento: " . $riferimento . "<br>";

echo "Giorno della Settimana: " . $oggi . "<br>";
echo "Data Riferimento: " . $dataMaggioreWeek . "<br>";
echo "Data Riferimento: " . $dataMinoreWeek . "<br>";

$riferimentoWeek = calcoloRiferimento($conn19, $dataMaggioreWeek, $dataMinoreWeek);

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
$html .= "<th onclick='sortTableNumeroKPI(14)'>Ok <br>Data Sottoscrizione contr. </th>";
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
$html .= "<th onclick='sortTableNumeroKPI(25)'>Resa convertito<br>[a 21 GG]</th>";
$html .= "<th onclick='sortTableNumeroKPI(26)'>Ric/h</th>";

$html .= "</tr>";
$html .= "</thead>";
$html .= "<tbody>";
//operatore=""

$vuoto = recuperoLeadVuoto($conn19, $dataMaggiore, $dataMinore);
//echo var_dump($vuoto);
if (count($vuoto) == 0) {
    
} else {
    $leadVuoto = $vuoto[0][0];
    $leadLordoVuoto = $vuoto[0][1];
    $leadEnergyVuoto = $vuoto[0][2];
    $leadTelcoVuoto = $vuoto[0][3];

    $html .= "<tr>";
    $html .= "<td>VUOTO</td>";
    $html .= "<td>-</td>";
    $html .= "<td>0</td>";
    $html .= "<td>0</td>";

    $html .= "<td>$leadVuoto</td>";

    $html .= "<td>$leadLordoVuoto</td>";

    $html .= "<td>0</td>";
    $html .= "<td>0</td>";
    $html .= "<td>0</td>";

    $html .= "<td>0</td>";
    $html .= "<td>0</td>";
    $html .= "<td>0</td>";
    $html .= "<td> 0</td>";
    $html .= "<td >0</td>";
    $html .= "<td>0</td>";
    $html .= "<td>0</td>";

    $html .= "<td>0</td>";

    $html .= "<td>0</td>";
    $html .= "<td>$leadEnergyVuoto</td>";
    $html .= "<td>$leadTelcoVuoto</td>";
    $html .= "<td >0</td>";
    $html .= "<td>0</td>";
    $html .= "<td>0 </td>";
    $html .= "<td>0</td>";
    $html .= "<td>0</td>";
    $html .= "<td>0</td>";
    $html .= "<td>0</td>";
    $html .= "</tr>";
}




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



    if (array_key_exists($nomeOperatore, $plenitudeOut)) {
        $OkProduzioneOut += $plenitudeOut[$nomeOperatore][0];
    }


    if (array_key_exists($nomeOperatore, $plenitudeData)) {
        $OkProduzioneData += $plenitudeData[$nomeOperatore][0];
        $koProduzioneData += $plenitudeData[$nomeOperatore][1];
    }

     // Calcolo sicuro del ricavo orario
$ricavoOrario = 0; // Valore di default

// Verifica che tutti i valori necessari siano validi
if ($oreIN > 0 && $valoreMedio > 0 && $OkProduzioneData > 0) {
    $ricavoOrario = ($OkProduzioneData * $valoreMedio) / $oreIN;
    
    // Arrotonda a 2 decimali per una rappresentazione più pulita
    $ricavoOrario = round($ricavoOrario, 2);
    
    // Formatta come valuta (opzionale)
    $ricavoOrarioFormattato = '€ ' . number_format($ricavoOrario, 2, ',', '.');
} else {
    $ricavoOrarioFormattato = '€ 0,00'; // Valore di default formattato
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


    if (array_key_exists($nomeOperatore, $plenitudeWeek)) {
        $OkProduzioneWeek += $plenitudeWeek[$nomeOperatore][0];
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
        $giorniLavorati15gg = $ore16GG[$nomeOperatore][0];
    } else {
        $giorniLavorati15gg = 0;
    }

    $OkProduzione15gg = 0;
    $koProduzione15gg = 0;
    $totaleLead15gg = 0;


    if (array_key_exists($nomeOperatore, $plenitude15gg)) {
        $OkProduzione15gg += $plenitude15gg[$nomeOperatore][0];
        $koProduzione15gg += $plenitude15gg[$nomeOperatore][1];
    }


    if (array_key_exists($nomeOperatore, $lead15gg)) {
        $totaleLead15gg = $lead15gg[$nomeOperatore][0];
    }



    $resa15gg = ($totaleLead15gg == 0) ? 0 : round(($OkProduzione15gg / $totaleLead15gg) * 100, 2);
    if ($resa15gg <= 17) {
        $colore15gg = 'orange';
    } else {
        $colore15gg = '';
    }

    $OkProduzione21gg = 0;
    $koProduzione21gg = 0;
    $totaleLead21gg = 0;


    if (array_key_exists($nomeOperatore, $plenitude21gg)) {
        $OkProduzione21gg += $plenitude21gg[$nomeOperatore][0];
        $koProduzione21gg += $plenitude21gg[$nomeOperatore][1];
    }

    if (array_key_exists($nomeOperatore, $lead21gg)) {
        $totaleLead21gg = $lead21gg[$nomeOperatore][0];
    }



    $resa21gg = ($totaleLead21gg == 0) ? 0 : round(($OkProduzione21gg / $totaleLead21gg) * 100, 2);
    if ($resa21gg <= 17) {
        $colore15gg = 'tomato';
    } elseif ($resa15gg <= 17) {
        $colore15gg = 'orange';
    } else {
        $colore15gg = '';
    }

   


    if ($oreIN == 0 && $okProdotto == 0 && $OkProduzioneData == 0) {
        
    } else {
        $html .= "<tr>";
        $html .= "<td style='background-color:$colore15gg'>$nomeOperatore</td>";
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
        $html .= "<td>$giorniLavorati15gg</td>";
        $html .= "<td style='background-color:$colore15gg'>$resa15gg </td>";
        $html .= "<td style='background-color:$colore15gg'> $resa21gg</td>";
        $html .= "<td>". round($ricavoOrario)  . " €</td>";
        $html .= "</tr>";
    }
}



$html .= "</tbody>";
$html .= "</table>";


$obj19->chiudiConnessione();
$obj ->chiudiConnessioneSiscall2();
$objGt ->chiudiConnessioneGt();
$objL ->chiudiConnessioneSiscallLead();

echo $html;


?>
