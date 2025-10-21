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

$siscall2 = [];
$siscallGT = [];
$siscallLead = [];

$agenzia = filter_input(INPUT_POST, "agenzieSelezionate");
$categoria = filter_input(INPUT_POST, "categoria");
// Converti le date nel formato italiano
$dataMinore = date('Y-m-d 00:00:00', strtotime(filter_input(INPUT_POST, "dataMinore")));
$dataMaggiore = date('Y-m-d 23:59:59', strtotime(filter_input(INPUT_POST, "dataMaggiore")));

$dataMinoreOre = date('Y-m-d', strtotime($dataMinore));
$dataMaggioreOre = date('Y-m-d', strtotime($dataMaggiore));

$dataOggi = date("Y-m-d");

if ($dataMaggioreOre == $dataOggi) {
    $dataMaggioreIeri = date('Y-m-d 23:59:59', strtotime("-1 days " . $dataMaggioreOre));

    $dataOggiMinore = date('Y-m-d 00:00:00', strtotime($dataOggi));
    $dataOggiMaggiore = date('Y-m-d 23:59:59', strtotime($dataOggi));

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
        . " WHERE event_time >= '$dataOggiMinore' AND event_time <= '$dataOggiMaggiore' "
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

    $risultatoOre = $connGt->query($queryOre);

    while (($rigaOre = $risultatoOre->fetch_array())) {
        $operatore = $rigaOre['operatore'];
        $oreIN = round($rigaOre['oreSPN_VDF'], 2);
        $oreOut = round($rigaOre['oreAltro'], 2);
        $sede = $rigaOre['citta'];
        if (!isset($livello)) {
            $livello = $rigaOre['livello'];
        }
        $siscallGT[$operatore] = [$oreIN, $oreOut, $sede, $livello];
    }

    $risultatoOre = $connL->query($queryOre);

    while (($rigaOre = $risultatoOre->fetch_array())) {
        $operatore = $rigaOre['operatore'];
        $oreIN = round($rigaOre['oreSPN_VDF'], 2);
        $oreOut = round($rigaOre['oreAltro'], 2);
        $sede = $rigaOre['citta'];
        if (!isset($livello)) {
            $livello = $rigaOre['livello'];
        }
        $siscallLead[$operatore] = [$oreIN, $oreOut, $sede, $livello];
    }
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

$queryRiferimento = "SELECT"
    . " count(idSponsorizzata) as Lead, "
    . " (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)+ SUM(`uniOk`)+ sum(enelOk)) AS OK_CP"
    . " FROM "
    . " `gestioneLead` "
    . " WHERE "
    . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
    . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
    . " AND dataImport <= '$dataMaggiore' "
    . " AND dataImport >= '$dataMinore' "
    . " and idSponsorizzata like 'G%' "
    . " and (duplicato='no' or duplicato='') order by gestitoDa";
//echo $queryRiferimento;
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
    . " (SUM(`pleniOk`) + SUM(`vodaOk`) + SUM(`viviOk`) + SUM(`irenOk`)+ SUM(`uniOk`)+sum(enelOK)) AS OK_CP "
    . " FROM "
    . " `gestioneLead` "
    . " WHERE "
    . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
    . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
    . " AND dataImport <= '$dataMaggioreWeek' "
    . " AND dataImport >= '$dataMinoreWeek' "
    . " and idSponsorizzata like 'G%' "
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

$elencoEnel = recuperoEnel($conn19, $dataMaggiore, $dataMinore);
$elencoEnelOut = recuperoEnelOut($conn19, $dataMaggiore, $dataMinore);

$elencoEnelWeek = recuperoEnel($conn19, $dataMaggioreWeek, $dataMinoreWeek);

$queryleadokfinale = "SELECT"
    . " REPLACE(`gestitoDa`, 'enel', '') AS operatore, "
    . " sum(if(duplicato='no',1,0)) AS Lead, "
    . " count(idSponsorizzata) as LeadLordo, "
    . " SUM(IF(`categoriaCampagna` = 'Energetico' AND duplicato='no', 1, 0)) AS Energetico, "
    . " SUM(IF(`categoriaCampagna` = 'Telco' AND duplicato='no' , 1, 0)) AS Telco "
    . " FROM "
    . " `gestioneLead` "
    . " WHERE "
    . " `gestitoDa` NOT IN ( 'VDAD', '6666') "
    . " AND `CategoriaUltima` NOT IN ('NONUTILICHIUSI') "
    . " AND dataImport <= '$dataMaggiore' "
    . " AND dataImport >= '$dataMinore' "
    . " and idSponsorizzata like 'G%' "
    . " GROUP BY "
    . " REPLACE(`gestitoDa`, 'enel', '') "
    . " ORDER BY "
    . " gestitoDa asc";
//echo $queryleadokfinale;
$risultatoCrm = $conn19->query($queryleadokfinale);

while ($rigaCRM = $risultatoCrm->fetch_array()) {
    $operatore = $rigaCRM[0];
    $totaleLead = round($rigaCRM[1], 2);
    //$totaleProdotto = round($rigaCRM[2], 2);
    //$okProdotto = round($rigaCRM[3], 2);
    $leadLordo = round($rigaCRM[2], 2);
    $energetico = round($rigaCRM[3]);
    $telco = round($rigaCRM[4]);
    /**
     * Modifica del 22/08/2024
     * modificata 15/11/2024 inserimento dati out
     */
    $queryPleni = "SELECT "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito', "
        . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)) as 'OKPV' "
        . " FROM "
        . " `plenitude` "
        . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=plenitude.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and gestioneLead.dataImport <='$dataMaggiore' "
        . " and gestioneLead.dataImport >='$dataMinore' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    echo $queryPleni;

    $risultatoPleni = $conn19->query($queryPleni);
    if (($rigaPleni = $risultatoPleni->fetch_array())) {
        $okPleni = $rigaPleni[0];
        $koPleni = $rigaPleni[1];
        $totPleni = $rigaPleni[2];
        $convertitoPleni = $rigaPleni[3];
        $okPlenipv = $rigaPleni[4];
    } else {
        $okPleni = 0;
        $koPleni = 0;
        $totPleni = 0;
        $convertitoPleni = 0;
        $okPlenipv = 0;
    }
    /**
     * Query pleni out
     */
    $okPleniOut = 0;
    $queryPleni = "SELECT "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot'"
        . " FROM "
        . " `plenitude` "
        . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
        . " where "
        . " idGestioneLead not like 'G%' "
        . " and data <='$dataMaggioreOre' "
        . " and data >='$dataMinoreOre' "
        . " and creatoDa='$operatore' ";

//     echo $queryPleni;

    $risultatoPleni = $conn19->query($queryPleni);
    if (($rigaPleni = $risultatoPleni->fetch_array())) {
        $okPleniOut = $rigaPleni[0];
        $koPleniOut = $rigaPleni[1];
        $totPleniOut = $rigaPleni[2];
    } else {
        $okPleniOut = 0;
        $koPleniOut = 0;
        $totPleniOut = 0;
    }


    /**
     * query vivi
     */
    $queryvivigas = "SELECT "
        . " SUM(IF(aggiuntaVivigas.fasePDA='OK',pezzoLordo,0)) as 'OK', "
        . " SUM(IF(aggiuntaVivigas.fasePDA='KO',pezzoLordo,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito', "
        . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)) as 'OKPV' "
        . " FROM "
        . " `vivigas` "
        . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=vivigas.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and vivigas.data<='$dataMaggiore' "
        . " and vivigas.data>='$dataMinore' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    $risultatoVivi = $conn19->query($queryvivigas);
    if (($rigaVivi = $risultatoVivi->fetch_array())) {
        $okVivi = $rigaVivi[0];
        $koVivi = $rigaVivi[1];
        $totVivi = $rigaVivi[2];
        $convertitoVivi = $rigaVivi[3];
        $OkViviPv = $rigaVivi[4];
    } else {
        $okVivi = 0;
        $koVivi = 0;
        $totVivi = 0;
        $convertitoVivi = 0;
        $OkViviPv = 0;
    }


    $okViviOut = 0;
    $koViviOut = 0;
    $totViviOut = 0;

// Query SQL corretta
    $queryviviout = "
    SELECT 
        SUM(IF(aggiuntaVivigas.fasePDA = 'OK', pezzoLordo, 0)) AS OK,
        SUM(IF(aggiuntaVivigas.fasePDA = 'KO', pezzoLordo, 0)) AS KO,
        COUNT(*) AS Tot
    FROM 
        `vivigas`
    INNER JOIN 
        aggiuntaVivigas ON aggiuntaVivigas.id = vivigas.id
    WHERE 
        idGestioneLead NOT LIKE 'G%'
        AND vivigas.data <= '$dataMaggiore'
        AND vivigas.data >= '$dataMinore'
        AND creatoDa = '$operatore';
";

// Esecuzione della query e gestione degli errori
    $risultatoVivi = $conn19->query($queryviviout);
    if ($risultatoVivi) {
        if ($rigaVivi = $risultatoVivi->fetch_array()) {
            $okViviOut = $rigaVivi['OK'] ?? 0; // Valore di OK
            $koViviOut = $rigaVivi['KO'] ?? 0; // Valore di KO
            $totViviOut = $rigaVivi['Tot'] ?? 0; // Totale
        }
    } else {
        // Log dell'errore per debug
        error_log("Errore nella query: " . $conn19->error);
    }

    //fine queryviviout

    $queryIren = "SELECT "
        . " SUM(IF(aggiuntaIren.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaIren.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito' "
        . " FROM "
        . " `iren` "
        . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=iren.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and iren.data<='$dataMaggiore' "
        . " and iren.data>='$dataMinore' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    $risultatoIren = $conn19->query($queryIren);
    if (($rigaIren = $risultatoIren->fetch_array())) {
        $okIren = $rigaIren[0];
        $koIren = $rigaIren[1];
        $totIren = $rigaIren[2];
        $convertitoIren = $rigaIren[3];
    } else {
        $okIren = 0;
        $koIren = 0;
        $totIren = 0;
        $convertitoIren = 0;
    }

    $queryUnion = "SELECT "
        . " SUM(IF(aggiuntaUnion.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaUnion.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito', "
        . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)) as 'OKPV' "
        . " FROM "
        . " `union` "
        . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=union.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and union.data<='$dataMaggiore' "
        . " and union.data>='$dataMinore' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    $risultatoUnion = $conn19->query($queryUnion);
    if (($rigaUnion = $risultatoUnion->fetch_array())) {
        $okUnion = $rigaUnion[0];
        $koUnion = $rigaUnion[1];
        $totUnion = $rigaUnion[2];
        $convertitoUnion = $rigaUnion[3];
        $okUnionPv = $rigaUnion[4];
    } else {
        $okUnion = 0;
        $koUnion = 0;
        $totUnion = 0;
        $convertitoUnion = 0;
        $okUnionPv = 0;
    }
// query Union Out
// Impostazione delle variabili iniziali
    $okUnionOut = 0;
    $koUnionOut = 0;
    $totUnionOut = 0;

// Query SQL corretta
    $queryUnion = "
    SELECT 
        SUM(IF(aggiuntaUnion.fasePDA = 'OK', 1, 0)) AS OK,
        SUM(IF(aggiuntaUnion.fasePDA = 'KO', 1, 0)) AS KO,
        COUNT(*) AS Tot
    FROM 
        `union`
    INNER JOIN 
        aggiuntaUnion ON aggiuntaUnion.id = union.id
    WHERE 
        idGestioneLead NOT LIKE 'G%'
        AND union.data <= '$dataMaggiore'
        AND union.data >= '$dataMinore'
        AND creatoDa = '$operatore';
";

// Esecuzione della query e gestione degli errori
    $risultatoUnion = $conn19->query($queryUnion);
    if ($risultatoUnion) {
        if ($rigaUnion = $risultatoUnion->fetch_array()) {
            $okUnionOut = $rigaUnion['OK'] ?? 0; // Valore di OK
            $koUnionOut = $rigaUnion['KO'] ?? 0; // Valore di KO
            $totUnionOut = $rigaUnion['Tot'] ?? 0; // Totale
        }
    } else {
        // Log dell'errore per debug
        error_log("Errore nella query Union: " . $conn19->error);
    }


    // fine query union out

    $queryVoda = "SELECT "
        . " SUM(IF(aggiuntaVodafone.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaVodafone.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito' "
        . " FROM "
        . " `vodafone` "
        . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=vodafone.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and vodafone.dataVendita<='$dataMaggiore' "
        . " and vodafone.dataVendita>='$dataMinore' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    $risultatoVoda = $conn19->query($queryVoda);
    if (($rigaVoda = $risultatoVoda->fetch_array())) {
        $okVoda = $rigaVoda[0];
        $koVoda = $rigaVoda[1];
        $totVoda = $rigaVoda[2];
        $convertitoVoda = $rigaVoda[3];
    } else {
        $okVoda = 0;
        $koVoda = 0;
        $totVoda = 0;
        $convertitoVoda = 0;
    }
//query out VOdafone
// Inizializzazione delle variabili
    $okVodaOut = 0;
    $koVodaOut = 0;
    $totVodaOut = 0;

// Query SQL corretta
    $queryVoda = "
    SELECT 
        SUM(IF(aggiuntaVodafone.fasePDA = 'OK', 1, 0)) AS OK,
        SUM(IF(aggiuntaVodafone.fasePDA = 'KO', 1, 0)) AS KO,
        COUNT(*) AS Tot
    FROM 
        `vodafone`
    INNER JOIN 
        aggiuntaVodafone ON aggiuntaVodafone.id = vodafone.id
    WHERE 
        idGestioneLead NOT LIKE 'G%'
        AND vodafone.dataVendita <= '$dataMaggiore'
        AND vodafone.dataVendita >= '$dataMinore'
        AND creatoDa = '$operatore';
";

// Esecuzione della query e gestione degli errori
    $risultatoVoda = $conn19->query($queryVoda);
    if ($risultatoVoda) {
        if ($rigaVoda = $risultatoVoda->fetch_array()) {
            $okVodaOut = $rigaVoda['OK'] ?? 0; // Valore di OK
            $koVodaOut = $rigaVoda['KO'] ?? 0; // Valore di KO
            $totVodaOut = $rigaVoda['Tot'] ?? 0; // Totale
        }
    } else {
        // Log dell'errore per debug
        error_log("Errore nella query Vodafone: " . $conn19->error);
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


    $queryEnel = "SELECT "
        . " SUM(IF(aggiuntaEnel.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaEnel.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito', "
        . " sum(if(fasePost='OK',if(fasePDA='OK',pezzoLordo,0),0)) as 'OKPV' "
        . " FROM "
        . " `enel` "
        . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=enel.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and gestioneLead.dataImport <='$dataMaggiore' "
        . " and gestioneLead.dataImport >='$dataMinore' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

//     echo $queryPleni;

    $risultatoEnel = $conn19->query($queryEnel);
    if (($rigaEnel = $risultatoEnel->fetch_array())) {
        $okEnel = $rigaEnel[0];
        $koEnel = $rigaEnel[1];
        $totEnel = $rigaEnel[2];
        $convertitoEnel = $rigaEnel[3];
        $okEnelpv = $rigaEnel[4];
    } else {
        $okEnel = 0;
        $koEnel = 0;
        $totEnel = 0;
        $convertitoEnel = 0;
        $okEnelpv = 0;
    }


// Uso dei risultati
// Ora puoi utilizzare $okVodaOut, $koVodaOut e $totVodaOut
    // fine query out vodafone

    $totaleProdotto = $totPleni + $totIren + $totVivi + $totUnion + $totVoda + $totEnel;
    //echo "Pleni: ".$okPleni."okVivi: ".$okVivi."okIren:".$okIren."OkUnion:  ".$okUnion."okVoda: ".$okVoda."okEnel: ".$okEnel;

    $okProdotto = $okPleni + $okVivi + $okIren + $okUnion + $okVoda + $okEnel;
    $koProdotto = $koPleni + $koVivi + $koIren + $koUnion + $koVoda + $koEnel;
    $convertito = $convertitoPleni + $convertitoVivi + $convertitoIren + $convertitoUnion + $convertitoUnion + $convertitoEnel;
    $OkProduzioneOut = $okPleniOut + $okViviOut + $okUnionOut + $okVodaOut + $okEnelOut;
    $Okpvtotale = $okPlenipv + $okVodaPv + $okUnionPv + $OkViviPv + $okEnelpv;
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


    /**
     * Calcolo dei pezzi con la data creazione contratto
     */
    $queryPleni = "SELECT "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='KO',1,0)) as 'KO'  "
        . " FROM "
        . " `plenitude` "
        . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and plenitude.data<='$dataMaggiore' "
        . " and plenitude.data>='$dataMinore' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

//    echo $queryPleni;

    $risultatoPleni = $conn19->query($queryPleni);
    if (($rigaPleni = $risultatoPleni->fetch_array())) {
        $okPleni = $rigaPleni[0];
        $koPleni = $rigaPleni[1];
    } else {
        $okPleni = 0;
        $koPleni = 0;
    }

    $queryEnel = "SELECT "
        . " SUM(IF(aggiuntaEnel.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaEnel.fasePDA='KO',1,0)) as 'KO'  "
        . " FROM "
        . " `enel` "
        . " inner join aggiuntaEnel on aggiuntaEnel.id=enel.id "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and enel.data<='$dataMaggiore' "
        . " and enel.data>='$dataMinore' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

//    echo $queryPleni;

    $risultatoEnel = $conn19->query($queryEnel);
    if (($rigaEnel = $risultatoPleni->fetch_array())) {
        $okEnel = $rigaEnel[0];
        $koEnel = $rigaEnel[1];
    } else {
        $okEnel = 0;
        $koEnel = 0;
    }


    $queryvivigas = "SELECT "
        . " SUM(IF(aggiuntaVivigas.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaVivigas.fasePDA='KO',1,0)) as 'KO'  "
        . " FROM "
        . " `vivigas` "
        . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
        . " where "
        . " idGestioneLead like 'G%' "
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
        . " idGestioneLead like 'G%' "
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
        . " idGestioneLead like 'G%' "
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
        . " idGestioneLead like 'G%' "
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


    $okData = $okPleni + $okVivi + $okIren + $okVoda + $okUnion + $okEnel;
    $koData = $koPleni + $koVivi + $koIren + $koVoda + $koUnion + $koEnel;

    /*     * *
     * Calcolo dei pezzi del riferimento settimanale
     */

    $queryPleni = "SELECT "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaPlenitude.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito' "
        . " FROM "
        . " `plenitude` "
        . " inner join aggiuntaPlenitude on aggiuntaPlenitude.id=plenitude.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=plenitude.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and gestioneLead.dataImport <='$dataMaggioreWeek' "
        . " and gestioneLead.dataImport >='$dataMinoreWeek' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

//     echo $queryPleni;

    $risultatoPleni = $conn19->query($queryPleni);
    if (($rigaPleni = $risultatoPleni->fetch_array())) {
        $okPleni = $rigaPleni[0];
        $koPleni = $rigaPleni[1];
        $totPleni = $rigaPleni[2];
        $convertitoPleni = $rigaPleni[3];
    } else {
        $okPleni = 0;
        $koPleni = 0;
        $totPleni = 0;
        $convertitoPleni = 0;
    }

    $queryvivigas = "SELECT "
        . " SUM(IF(aggiuntaVivigas.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaVivigas.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito' "
        . " FROM "
        . " `vivigas` "
        . " inner join aggiuntaVivigas on aggiuntaVivigas.id=vivigas.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=vivigas.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and vivigas.data<='$dataMaggioreWeek' "
        . " and vivigas.data>='$dataMinoreWeek' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    $risultatoVivi = $conn19->query($queryvivigas);
    if (($rigaVivi = $risultatoVivi->fetch_array())) {
        $okVivi = $rigaVivi[0];
        $koVivi = $rigaVivi[1];
        $totVivi = $rigaVivi[2];
        $convertitoVivi = $rigaVivi[3];
    } else {
        $okVivi = 0;
        $koVivi = 0;
        $totVivi = 0;
        $convertitoVivi = 0;
    }

    $queryIren = "SELECT "
        . " SUM(IF(aggiuntaIren.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaIren.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito' "
        . " FROM "
        . " `iren` "
        . " inner join aggiuntaIren on aggiuntaIren.id=iren.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=iren.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and iren.data<='$dataMaggioreWeek' "
        . " and iren.data>='$dataMinoreWeek' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    $risultatoIren = $conn19->query($queryIren);
    if (($rigaIren = $risultatoIren->fetch_array())) {
        $okIren = $rigaIren[0];
        $koIren = $rigaIren[1];
        $totIren = $rigaIren[2];
        $convertitoIren = $rigaIren[3];
    } else {
        $okIren = 0;
        $koIren = 0;
        $totIren = 0;
        $convertitoIren = 0;
    }

    $queryUnion = "SELECT "
        . " SUM(IF(aggiuntaUnion.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaUnion.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito' "
        . " FROM "
        . " `union` "
        . " inner join aggiuntaUnion on aggiuntaUnion.id=union.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=union.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and union.data<='$dataMaggioreWeek' "
        . " and union.data>='$dataMinoreWeek' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    $risultatoUnion = $conn19->query($queryUnion);
    if (($rigaUnion = $risultatoUnion->fetch_array())) {
        $okUnion = $rigaUnion[0];
        $koUnion = $rigaUnion[1];
        $totUnion = $rigaUnion[2];
        $convertitoUnion = $rigaUnion[3];
    } else {
        $okUnion = 0;
        $koUnion = 0;
        $totUnion = 0;
        $convertitoUnion = 0;
    }


    $queryVoda = "SELECT "
        . " SUM(IF(aggiuntaVodafone.fasePDA='OK',1,0)) as 'OK', "
        . " SUM(IF(aggiuntaVodafone.fasePDA='KO',1,0)) as 'KO',  "
        . " count(*) as 'Tot',"
        . " sum(convertito) as 'Convertito' "
        . " FROM "
        . " `vodafone` "
        . " inner join aggiuntaVodafone on aggiuntaVodafone.id=vodafone.id "
        . " left join gestioneLead on gestioneLead.idSponsorizzata=vodafone.idGestioneLead "
        . " where "
        . " idGestioneLead like 'G%' "
        . " and vodafone.dataVendita<='$dataMaggioreWeek' "
        . " and vodafone.dataVendita>='$dataMinoreWeek' "
        . " and creatoDa='$operatore' "
        . " group by "
        . " creatoDa";

    $risultatoVoda = $conn19->query($queryVoda);
    if (($rigaVoda = $risultatoVoda->fetch_array())) {
        $okVoda = $rigaVoda[0];
        $koVoda = $rigaVoda[1];
        $totVoda = $rigaVoda[2];
        $convertitoVoda = $rigaVoda[3];
    } else {
        $okVoda = 0;
        $koVoda = 0;
        $totVoda = 0;
        $convertitoVoda = 0;
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

    $okWeek = $okPleni + $okVivi + $okIren + $okVoda + $okUnion + $okEnel;

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
        . " and idSponsorizzata like 'G%' "
        . " and REPLACE(`gestitoDa`, 'enel', '') ='$operatore' "
        . " and (duplicato='no' or duplicato='')";
//            . " $queryAgenzia ";

    $risultatoCrmWeek = $conn19->query($queryleadokfinaleWeek);
    if (($rigaWeek = $risultatoCrmWeek->fetch_array())) {
        $leadWeek = $rigaWeek[0];

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
                AND mandato = 'Lead Inbound'  
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
            . " and mandato<>'Lead Inbound' ";

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
            . " and mandato='Lead Inbound'  ";

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
            . " and mandato<>'Lead Inbound'  ";
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
    $html .= "<td>$percOkPv </td>";
    $html .= "<td>$gglavin</td>";
    $html .= "<td>$resavonv</td>";
    $html .= "</tr>";
}


$html .= "</tbody>";
$html .= "</table>";

echo $html;

$conn19->close();


?>
