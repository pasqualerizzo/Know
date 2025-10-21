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
$connLead = $objL->apriConnessioneSiscallLead();

$siscall2 = [];
$siscallGT = [];
$siscallLead = [];

//$dataMinore = filter_input(INPUT_POST, "dataMinore");
//$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$dataMinore = "2025-02-14";
$dataMaggiore = "2025-02-14";
$agenzia = filter_input(INPUT_POST, "agenzieSelezionate");
$categoria = filter_input(INPUT_POST, "categoria");
// Converti le date nel formato italiano
$dataMinore = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggiore = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$dataMinoreOre = date('Y-m-d', strtotime($dataMinore));
$dataMaggioreOre = date('Y-m-d', strtotime($dataMaggiore));

$dataOggi = date("Y-m-d");

if ($dataMaggioreOre == $dataOggi) {
    $dataMaggioreIeri = date('Y-m-d 23:59:59', strtotime("-1 days " . $dataMaggioreOre));

    $dataOggiMinore = date('Y-m-d 00:00:00', strtotime($dataOggi));
    $dataOggiMaggiore = date('Y-m-d 23:59:59', strtotime($dataOggi));

    $siscall2 = recuperoOre($conn, $dataOggiMaggiore, $dataOggiMinore);
    $siscallGT = recuperoOre($connGt, $dataOggiMaggiore, $dataOggiMinore);
    $siscallLead = recuperoOre($connLead, $dataOggiMaggiore, $dataOggiMinore);
}
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
$totaleenergetico = 0;
$totaleTelco = 0;
$okPlenipv = 0;
$contattiUtili = 0;
$OkViviPv = 0;
$okUnionPv = 0;
$okVodaPv = 0;
$Okpvtotale = 0;
$gglavin = 0;
$oregglavin = 0;
$resavonv = 0;
$sedeIn = 0;
/**
 * Intestazione della Tabella
 */
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
$html .= "<th onclick='sortTableNumeroKPI(9)'>Totale CP <br>Data import lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(10)'>% CP_Lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(11)'>Convertito <br>Data import lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(12)'>% Convertito</th>";
$html .= "<th onclick='sortTableNumeroKPI(13)'>OK CP <br>Data import lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(14)'>% OK CP</th>";
$html .= "<th onclick='sortTableNumeroKPI(15)'>KO CP<br>Data import lead</th>";
$html .= "<th onclick='sortTableNumeroKPI(16)'>Livello<br> Range Selezionato</th>";
$html .= "<th onclick='sortTableNumeroKPI(17)'>Livello Centralino</th>";
$html .= "<th onclick='sortTableNumeroKPI(18)'>Ok <br>Data creazione contr. </th>";
$html .= "<th onclick='sortTableNumeroKPI(19)'>Perc. su Data</th>";
//$html .= "<th>Livello su Data</th>";
$html .= "<th onclick='sortTableNumeroKPI(20)'>Ko <br>Data creazione contr.</th>";
$html .= "<th onclick='sortTableNumeroKPI(21)'>Delta OK</th>";
$html .= "<th onclick='sortTableNumeroKPI(22)'>Energy</th>";
$html .= "<th onclick='sortTableNumeroKPI(23)'>Telco</th>";
$html .= "<th onclick='sortTableNumeroKPI(24)'>Contatti<br> Utili</th>";
$html .= "<th onclick='sortTableNumeroKPI(25)'>Ok<br>Post Vendita</th>";
$html .= "<th onclick='sortTableNumeroKPI(26)'>%<br>Post Vendita</th>";

$html .= "<th onclick='sortTableNumeroKPI(27)'>GG Lavorati<br>[a 15 GG]</th>";
$html .= "<th onclick='sortTableNumeroKPI(28)'>Resa convertito<br>[a 15 GG]</th>";

$html .= "</tr>";
$html .= "</thead>";
$html .= "<tbody>";
/*
 * Calcolo della percentuale di riferimento sul range
 */

$riferimento = calcoloRiferimento($conn19, $dataMaggiore, $dataMinore);

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

$riferimentoWeek = calcoloRiferimento($conn19, $dataMaggioreWeek, $dataMinoreWeek);
echo "<br>";
echo $riferimentoWeek;
echo "<br>";

/*
 * Calcolo delle righe risultati degli operatori
 */

$elencoLead = recuperoLead($conn19, $dataMaggiore, $dataMinore);
$elencoLeadOperatore = recuperoLeadOperatore($conn19, $dataMaggioreWeek, $dataMinoreWeek);
$elencoPlenitude = recuperoPlenitude($conn19, $dataMaggiore, $dataMinore);
$elencoPlenitudeOut = recuperoPlenitudeOut($conn19, $dataMaggiore, $dataMinore);
$elencoEnel = recuperoEnel($conn19, $dataMaggiore, $dataMinore);
$elencoEnelOut = recuperoEnelOut($conn19, $dataMaggiore, $dataMinore);
$elencoVivigas = recuperoVivigas($conn19, $dataMaggiore, $dataMinore);
$elencoVivigasOut = recuperoVivigasOut($conn19, $dataMaggiore, $dataMinore);
$elencoIren = recuperoIren($conn19, $dataMaggiore, $dataMinore);
$elencoIrenOut = recuperoIrenOut($conn19, $dataMaggiore, $dataMinore);
$elencoUnion = recuperoUnion($conn19, $dataMaggiore, $dataMinore);
$elencoUnionOut = recuperoUnionOut($conn19, $dataMaggiore, $dataMinore);


$elencoPlenitudeWeek = recuperoPlenitude($conn19, $dataMaggioreWeek, $dataMinoreWeek);

$elencoEnelWeek = recuperoEnel($conn19, $dataMaggioreWeek, $dataMinoreWeek);

$elencoVivigasWeek = recuperoVivigas($conn19, $dataMaggioreWeek, $dataMinoreWeek);

$elencoIrenWeek = recuperoIren($conn19, $dataMaggioreWeek, $dataMinoreWeek);

$elencoUnionWeek = recuperoUnion($conn19, $dataMaggioreWeek, $dataMinoreWeek);

foreach ($elencoLead as $rigaLead) {
    $operatore = $rigaLead[0];
    $totaleLead = $rigaLead[1];
    $leadLordo = $rigaLead[2];
    $energetico = $rigaLead[3];
    $telco = $rigaLead[4];
    /**
     * Modifica del 22/08/2024
     * modificata 15/11/2024 inserimento dati out
     */
    if (array_key_exists($operatore, $elencoPlenitude)) {
        $okPleni = $elencoPlenitude[$operatore][0];
        $koPleni = $elencoPlenitude[$operatore][1];
        $totPleni = $elencoPlenitude[$operatore][2];
        $convertitoPleni = $elencoPlenitude[$operatore][3];
        $okPlenipv = $elencoPlenitude[$operatore][4];
    } else {
        $okPleni = 0;
        $koPleni = 0;
        $totPleni = 0;
        $convertitoPleni = 0;
        $okPlenipv = 0;
    }
    if (array_key_exists($operatore, $elencoEnel)) {
        $okEnel = $elencoEnel[$operatore][0];
        $koEnel = $elencoEnel[$operatore][1];
        $totEnel = $elencoEnel[$operatore][2];
        $convertitoEnel = $elencoEnel[$operatore][3];
        $okEnelpv = $elencoEnel[$operatore][4];
    } else {
        $okEnel = 0;
        $koEnel = 0;
        $totEnel = 0;
        $convertitoEnel = 0;
        $okEnelpv = 0;
    }

    if (array_key_exists($operatore, $elencoVivigas)) {
        $okVivi = $elencoVivigas[$operatore][0];
        $koVivi = $elencoVivigas[$operatore][1];
        $totVivi = $elencoVivigas[$operatore][2];
        $convertitoVivi = $elencoVivigas[$operatore][3];
        $OkViviPv = $elencoVivigas[$operatore][4];
    } else {
        $okVivi = 0;
        $koVivi = 0;
        $totVivi = 0;
        $convertitoVivi = 0;
        $OkViviPv = 0;
    }

    if (array_key_exists($operatore, $elencoIren)) {
        $okIren = $elencoIren[$operatore][0];
        $koIren = $elencoIren[$operatore][1];
        $totIren = $elencoIren[$operatore][2];
        $convertitoIren = $elencoIren[$operatore][3];
    } else {
        $okIren = 0;
        $koIren = 0;
        $totIren = 0;
        $convertitoIren = 0;
    }

    if (array_key_exists($operatore, $elencoUnion)) {
        $okUnion = $elencoUnion[$operatore][0];
        $koUnion = $elencoUnion[$operatore][1];
        $totUnion = $elencoUnion[$operatore][2];
        $convertitoUnion = $elencoUnion[$operatore][3];
        $okUnionPv = $elencoUnion[$operatore][4];
    } else {
        $okUnion = 0;
        $koUnion = 0;
        $totUnion = 0;
        $convertitoUnion = 0;
        $okUnionPv = 0;
    }

    if (array_key_exists($operatore, $elencoPlenitudeOut)) {
        $okEnelOut = $elencoPlenitudeOut[$operatore][0]; // Valore di OK
        $koEnelOut = $elencoPlenitudeOut[$operatore][1]; // Valore di KO
        $totEnelOut = $elencoPlenitudeOut[$operatore][2]; // Totale
    } else {
        $okPleniOut = 0; // Valore di OK
        $koPleniOut = 0; // Valore di KO
        $totPleniOut = 0; // Totale
    }

    if (array_key_exists($operatore, $elencoVivigasOut)) {
        $okViviOut = $elencoVivigasOut[$operatore][0];
        $koViviOut = $elencoVivigasOut[$operatore][1];
        $totViviOut = $elencoVivigasOut[$operatore][2];
    } else {
        $okViviOut = 0;
        $koViviOut = 0;
        $totViviOut = 0;
    }

    if (array_key_exists($operatore, $elencoUnionOut)) {
        $okUnionOut = $elencoUnionOut[$operatore][0];
        $koUnionOut = $elencoUnionOut[$operatore][1];
        $totUnionOut = $elencoUnionOut[$operatore][2];
    } else {
        $okUnionOut = 0;
        $koUnionOut = 0;
        $totUnionOut = 0;
    }

    if (array_key_exists($operatore, $elencoEnelOut)) {
        $okEnelOut = $elencoEnelOut[$operatore][0];
        $koEnelOut = $elencoEnelOut[$operatore][1];
        $totEnelOut = $elencoEnelOut[$operatore][2];
    } else {
        $okEnelOut = 0;
        $koEnelOut = 0;
        $totEnelOut = 0;
    }
    if (array_key_exists($operatore, $elencoIrenOut)) {
        $okIrenOut = $elencoIrenOut[$operatore][0];
        $koIrenOut = $elencoIrenOut[$operatore][1];
        $totIrenOut = $elencoIrenOut[$operatore][2];
    } else {
        $okIrenOut = 0;
        $koIrenOut = 0;
        $totIrenOut = 0;
    }

// Uso dei risultati
// Ora puoi utilizzare $okVodaOut, $koVodaOut e $totVodaOut
// fine query out vodafone

    $totaleProdotto = $totPleni + $totIren + $totVivi + $totUnion + $totEnel;
    $okProdotto = $okPleni + $okVivi + $okIren + $okUnion + $okEnel;
    $koProdotto = $koPleni + $koVivi + $koIren + $koUnion + $koEnel;
    $convertito = $convertitoPleni + $convertitoVivi + $convertitoIren + $convertitoUnion + $convertitoEnel;
    $OkProduzioneOut = $okPleniOut + $okViviOut + $okUnionOut + $okIrenOut + $okEnelOut;
    $Okpvtotale = $okPlenipv + $okUnionPv + $OkViviPv + $okEnelpv;
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
    $okData = $okPleniOut + $okViviOut + $okIrenOut + $okUnionOut + $okEnelOut;
    $koData = $koPleniOut + $koViviOut + $koIrenOut + $koUnionOut + $koEnelOut;

    /*     * *
     * Calcolo dei pezzi del riferimento settimanale
     */


    if (array_key_exists($operatore, $elencoPlenitudeWeek)) {
        $okPleni = $elencoPlenitudeWeek[$operatore][0];
        $koPleni = $elencoPlenitudeWeek[$operatore][1];
        $totPleni = $elencoPlenitudeWeek[$operatore][2];
        $convertitoPleni = $elencoPlenitudeWeek[$operatore][3];
        $okPlenipv = $elencoPlenitudeWeek[$operatore][4];
    } else {
        $okPleni = 0;
        $koPleni = 0;
        $totPleni = 0;
        $convertitoPleni = 0;
        $okPlenipv = 0;
    }

    if (array_key_exists($operatore, $elencoVivigasWeek)) {
        $okVivi = $elencoVivigasWeek[$operatore][0];
        $koVivi = $elencoVivigasWeek[$operatore][1];
        $totVivi = $elencoVivigasWeek[$operatore][2];
        $convertitoVivi = $elencoVivigasWeek[$operatore][3];
        $OkViviPv = $elencoVivigasWeek[$operatore][4];
    } else {
        $okVivi = 0;
        $koVivi = 0;
        $totVivi = 0;
        $convertitoVivi = 0;
        $OkViviPv = 0;
    }

    if (array_key_exists($operatore, $elencoIrenWeek)) {
        $okIren = $elencoIrenWeek[$operatore][0];
        $koIren = $elencoIrenWeek[$operatore][1];
        $totIren = $elencoIrenWeek[$operatore][2];
        $convertitoIren = $elencoIrenWeek[$operatore][3];
    } else {
        $okIren = 0;
        $koIren = 0;
        $totIren = 0;
        $convertitoIren = 0;
    }

    if (array_key_exists($operatore, $elencoUnionWeek)) {
        $okUnion = $elencoUnionWeek[$operatore][0];
        $koUnion = $elencoUnionWeek[$operatore][1];
        $totUnion = $elencoUnionWeek[$operatore][2];
        $convertitoUnion = $elencoUnionWeek[$operatore][3];
        $okUnionPv = $elencoUnionWeek[$operatore][4];
    } else {
        $okUnion = 0;
        $koUnion = 0;
        $totUnion = 0;
        $convertitoUnion = 0;
        $okUnionPv = 0;
    }

    if (array_key_exists($operatore, $elencoEnelWeek)) {
        $okEnel = $elencoEnelWeek[$operatore][0];
        $koEnel = $elencoEnelWeek[$operatore][1];
        $totEnel = $elencoEnelWeek[$operatore][2];
        $convertitoEnel = $elencoEnelWeek[$operatore][3];
        $okEnelpv = $elencoEnelWeek[$operatore][4];
    } else {
        $okEnel = 0;
        $koEnel = 0;
        $totEnel = 0;
        $convertitoEnel = 0;
        $okEnelpv = 0;
    }


    $okWeek = $okPleni + $okVivi + $okIren + $okEnel + $okUnion;
    if (array_key_exists($operatore, $elencoLeadOperatore)) {
        $leadWeek = $elencoLeadOperatore[$operatore][0];
    } else {
        $leadWeek = 0;

    }

    $mediaWeek = ($leadWeek == 0) ? 0 : round(($okWeek / $leadWeek) * 100, 2);

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

    $sede = "";
//    $ore = 0;
    $oreOut = 0;
    $oreIN = 0;

    if ($dataMaggioreOre != $dataOggi) {

        /**
         * Calcolo ore inbound
         */
        $queryOre = "SELECT 
                SUM(CASE 
                    WHEN giorno BETWEEN '$dataMinore' AND '$dataMaggiore' 
                    THEN numero 
                    ELSE 0 
                END) / 3600 AS totale_ore, 
                sede, 
                COUNT(DISTINCT CASE 
                    WHEN giorno BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 16 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) 
                    THEN DATE(giorno) 
                END) AS giorni_lavorati,
                SUM(CASE 
                    WHEN giorno BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 16 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) 
                    THEN numero 
                    ELSE 0 
                END) / 3600 AS ore_giorni_lavorati 
            FROM 
                `stringheTotale` 
            WHERE 
                nomeCompleto = '$operatore' 
                AND (mandato = 'Lead Inbound' OR (mandato = 'Vodafone' AND provenienza = 'siscallLead')) 
            GROUP BY sede;";
//ECHO $queryOre;
        $risultatoOre = $conn19->query($queryOre);
        if (($rigaOre = $risultatoOre->fetch_array())) {
            $oreIN += round($rigaOre[0], 2);
            $sedeIn = $rigaOre[1];
            $gglavin = $rigaOre[2];
            $oregglavin += round($rigaOre[3], 2);
        }


        $queryOre = "SELECT "
            . " sum(numero)/3600, "
            . " sede "
            . " FROM "
            . " `stringheTotale` "
            . " where "
            . " giorno >='$dataMinore' "
            . " and giorno<='$dataMaggiore' "
            . " and nomeCompleto='$operatore' "
            . " and (mandato<>'Lead Inbound' or (mandato='Vodafone' and provenienza<>'siscallLead'))";

        $risultatoOre = $conn19->query($queryOre);
        if (($rigaOre = $risultatoOre->fetch_array())) {
            $oreOut += round($rigaOre[0], 2);
            $sedeOut = $rigaOre[1];
        }
    } elseif ($dataMaggioreOre == $dataMinoreOre) {

        if (array_key_exists($operatore, $siscall2)) {
            $oreIN += $siscall2[$operatore][0];
            $sedeIn = $siscall2[$operatore][2];
            $oreOut += $siscall2[$operatore][1];
        } else {
            $oreIN += 0;
            $sedeIn = "";
            $oreOut += 0;
        }
        if (array_key_exists($operatore, $siscallGT)) {
            $oreIN += $siscallGT[$operatore][0];
            $sedeOut = $siscallGT[$operatore][2];
            $oreOut += $siscallGT[$operatore][1];
        } else {
            $oreIN += 0;
            $sedeOut = "";
            $oreOut += 0;
        }
        if (array_key_exists($operatore, $siscallLead)) {
            $oreIN += $siscallLead[$operatore][0];
            if ($sedeIn == "") {
                $sedeIn = $siscallLead[$operatore][2];
            }
            $oreOut += $siscallLead[$operatore][1];
        } else {
            $oreIN += 0;
//$sedeIn = "";
            $oreOut += 0;
        }
    } else {

        /**
         * Calcolo ore inbound
         */
        $queryOre = "SELECT "
            . " sum(numero)/3600, "
            . " sede "
            . " FROM "
            . " `stringheTotale` "
            . " where "
            . " giorno >='$dataMinore' "
            . " and giorno<='$dataMaggioreIeri' "
            . " and nomeCompleto='$operatore' "
            . " and (mandato='Lead Inbound'  ";

        $risultatoOre = $conn19->query($queryOre);
        if (($rigaOre = $risultatoOre->fetch_array())) {
            $oreIN += round($rigaOre[0], 2);
            $sedeIn = $rigaOre[1];
        }


        $queryOre = "SELECT "
            . " sum(numero)/3600, "
            . " sede "
            . " FROM "
            . " `stringheTotale` "
            . " where "
            . " giorno >='$dataMinore' "
            . " and giorno<='$dataMaggioreIeri' "
            . " and nomeCompleto='$operatore' "
            . " and (mandato<>'Lead Inbound'  ";
//echo $queryOre;
        $risultatoOre = $conn19->query($queryOre);
        if (($rigaOre = $risultatoOre->fetch_array())) {
            $oreOut += round($rigaOre[0], 2);
            $sedeOut = $rigaOre[1];
        }
        if (array_key_exists($operatore, $siscall2)) {
            $oreIN += $siscall2[$operatore][0];
            if ($sedeIn == "") {
                $sedeIn = $siscall2[$operatore][2];
            }
            $oreOut += $siscall2[$operatore][1];
        } else {
            $oreIN += 0;
//$sedeIn = "";
            $oreOut += 0;
        }
        if (array_key_exists($operatore, $siscallGT)) {
            $oreIN += $siscallGT[$operatore][0];
            if ($sedeOut = "") {
                $sedeOut = $siscallGT[$operatore][2];
            }
            $oreOut += $siscallGT[$operatore][1];
        } else {
            $oreIN += 0;
//$sedeOut = "";
            $oreOut += 0;
        }
        if (array_key_exists($operatore, $siscallDGT)) {
            $oreIN += $siscallDGT[$operatore][0];
            if ($sedeIn == "") {
                $sedeIn = $siscallDGT[$operatore][2];
            }
            $oreOut += $siscallDGT[$operatore][1];
        } else {
            $oreIN += 0;
//$sedeIn = "";
            $oreOut += 0;
        }

        if (array_key_exists($operatore, $siscallLead)) {
            $oreIN += $siscallLead[$operatore][0];
            if ($sedeIn == "") {
                $sedeIn = $siscallLead[$operatore][2];
            }
            $oreOut += $siscallLead[$operatore][1];
        } else {
            $oreIN += 0;
//$sedeIn = "";
            $oreOut += 0;
        }
    }


    /**
     * Aggiunto  per controllare le chiamate solo lead
     */
// resa out 

    if ($oreOut == 0 && $OkProduzioneOut == 0) {
        $resaOut = 0;
    } else {
        if ($oreOut != 0) {
            $resaOut = round(($OkProduzioneOut / $oreOut), 2);
        } else {
            $resaOut = 0; // oppure un altro valore o messaggio di errore
        }
    }
    $sede = ($sedeIn == "") ? $sedeOut : $sedeIn;
//fine resa out            
//echo $risultatoOre;
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

    $differenzaUtili = 0;
    $colorRangeUtili = 0;

    $contattiUtili = ($oreIN == 0) ? 0 : round($totaleLead / $oreIN, 2);

    $percOkPv = ($okData != 0) ? round(($Okpvtotale / $okData) * 100, 2) : 0;

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

    if ($oregglavin != 0) {
        $resavonv = ($convertito / $oregglavin) * 100;
        $resavonv = number_format($resavonv, 2) . "%";
    } else {
        $resavonv = "0%"; // Oppure un messaggio personalizzato, es. "N/A"
    }

    $html .= "<tr>";
    $html .= "<td>$operatore</td>";
    $html .= "<td>$sede</td>";
    $html .= "<td>$oreIN</td>";
    $html .= "<td>$numeroChiamate</td>";

    $html .= "<td>$totaleLead</td>";

    $html .= "<td>$leadLordo</td>";

    $html .= "<td>$oreOut</td>";
    $html .= "<td>$OkProduzioneOut</td>";
    $html .= "<td>$resaOut</td>";
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
    $html .= "<td>$energetico</td>";
    $html .= "<td>$telco</td>";
    $html .= "<td style='background-color:$colorRangeUtili'>$contattiUtili</td>";
    $html .= "<td>$Okpvtotale</td>";
    $html .= "<td>$percOkPv '%</td>";
    $html .= "<td>$gglavin</td>";
    $html .= "<td>$resavonv</td>";
    $html .= "</tr>";
}


$html .= "</tbody>";
$html .= "</table>";

echo $html;

$conn19->close();

function siscallA15GG($_connS2)
{

    $dataOggi = date('Y-m-d');
    $dataMinore = date('Y-m-d 00:00:00', strtotime("-15 days " . $dataOggi));
    $dataMaggiore = date('Y-m-d 23:59:59', strtotime($dataOggi));

    $queryOre = "SELECT "
        . " full_name as operatore, "
        . " user_level AS livello, "
        . " territory AS citta, "
        . " campaign_description AS mandato, "
        . " SUM(CASE WHEN v.campaign_id = 'SPN_INB' OR v.campaign_id = 'VDF_TLCO' THEN (pause_sec + wait_sec + talk_sec + dispo_sec) ELSE 0 END) / 3600 AS 'oreSPN_VDF', "
        . " SUM(CASE WHEN v.campaign_id != 'SPN_INB' AND v.campaign_id != 'VDF_TLCO' THEN (pause_sec + wait_sec + talk_sec + dispo_sec) ELSE 0 END) / 3600 AS 'oreAltro' "
        . " FROM vicidial_agent_log AS v "
        . " INNER JOIN vicidial_users AS operatore ON v.user = operatore.user "
        . " INNER JOIN vicidial_campaigns AS campagna ON v.campaign_id = campagna.campaign_id "
        . " WHERE event_time >= '$dataMinore' AND event_time <= '$dataMaggiore' "
        . " GROUP BY full_name  ";
//echo $queryOre;
    try {
        $risultatoOre = $conn->query($queryOre);
    } catch (Exception $ex) {
        echo "Errore Siscall2: " . $ex;
    }
    while (($rigaOre = $risultatoOre->fetch_array())) {
        $operatore = $rigaOre['operatore'];
        $oreIN = round($rigaOre['oreSPN_VDF'], 2);
        $oreOut = round($rigaOre['oreAltro'], 2);
        $sede = $rigaOre['citta'];
        if (!isset($livello)) {
            $livello = $rigaOre['livello'];
        }
        $siscall2[$operatore] = [$oreIN, $oreOut, $sede, $livello];
    }
}

?>
