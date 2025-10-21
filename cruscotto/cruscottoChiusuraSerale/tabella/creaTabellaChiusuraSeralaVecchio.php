<?php

//echo bananone_fuffolone_;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscall2.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneGt.php";
//require "/Applications/MAMP/htdocs/Know/connessione/connessioneDigital.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objS2 = new connessioneSiscall2();
$connS2 = $objS2->apriConnessioneSiscall2();

$objGt = new connessioneGt();
$connGt = $objGt->apriConnessioneGt();

//$objDgt = new connessioneDigital();
//$connDgt = $objDgt->apriConnessioneDigital();

$objLead = new connessioneSiscallLead();
$connLead = $objLead->apriConnessioneSiscallLead();


$mese = filter_input(INPUT_POST, "mese");

//$mese="2024-07";
$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");

//$testMode = $_POST["testMode"];
//echo $testMode;
//if ($testMode=="true") {
//    echo "si";
//}
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggioreIta = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);

$pezzoLordoSede = 0;
$pezzoOkSede = 0;
$pezzoKoSede = 0;
$pezzoBklSede = 0;
$pezzoBklpSede = 0;
$oreSede = 0;
$totalepolizze = 0;
$pezzoLordoTotale = 0;
$pezzoOkTotale = 0;
$pezzoKoTotale = 0;
$pezzoBklTotale = 0;
$pezzoBklpTotale = 0;
$oreTotale = 0;
$totaleResa = 0;  
$sedePrecedente = "";

$querySede = "";
$lunghezzaSede = count($sede);

if ($lunghezzaSede == 1) {
    $querySede .= " AND sede='$sede[0]' ";
} elseif ($lunghezzaSede == 1) {
    $querySede = "";
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

$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaChiusura.php";

foreach ($mandato as $idMandato) {

    switch ($idMandato) {
        case "Plenitude":
            $queryCrmSede = "SELECT
                                   Sede,
                                  'Plenitude' AS Mandato,
                                  'All_Out' as tipoCampagna ,
                                  'OutBound' as tipo,
                            SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,
                            SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze
                            FROM
                                 `plenitude`

                           INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id
                           WHERE DATA
                                   <= '$dataMaggiore' AND DATA >= '$dataMinore' AND statoPda ='Ok Firma'
        
                           and tipoCampagna <> 'Lead'
                           GROUP BY
                                  sede 
                                  order by  sede";
            
//echo $queryCrmSede;

            break;
        case "Green Network":
            $queryCrmSede = "SELECT 
                                    sede,
                                   'Green Network' AS green,
                            SUM(pezzoLordo) AS Prodotto,
                            SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
                            FROM 
                                    green
                            INNER JOIN 
                                    aggiuntaGreen 
                            ON green.id = aggiuntaGreen.id 
                            WHERE 
                                  data<='$dataMaggiore' and data>='$dataMinore' 
                            AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                            AND comodity <> 'Polizza'
                            GROUP BY 
                                   sede";
            break;
        case "Vivigas Energia":
            $queryCrmSede = "SELECT
                                   sede,
                                  'Vivigas' AS Mandato,
                                  'All_Out' AS tipoCampagna,
                                  'OutBound' AS tipo,
                            SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,
                            SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze
                            FROM
                                 `vivigas`
                           INNER JOIN
                                  aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
                           WHERE 
                                  data<='$dataMaggiore' and data>='$dataMinore' AND statoPda IN ( 'Ok Definitivo' , 'Ok inserito' , 'Da Avanzare' , 'COMPLETATO FIRMA' , 'IN ATTESA IDENTIFICAZIONE'  , 'INVIATA SECONDA MAIL' , 'IN ATTESA SECONDA MAIL' , 'OK VOCAL RECALL')  AND tipoCampagna NOT IN  ('Lead')
                           GROUP BY
                                  sede
                           ORDER BY
                                   sede
";

            break;
        case "Vodafone":
            $queryCrmSede = "SELECT 
                                   'Lamezia' AS Sede,
                                   'Vodafone' AS Mandato,
                                   'All_Out' AS Campagna,
                                   'OutBound' AS tipo,
                            SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito,
                            SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo = 'Polizza' ELSE 0 END) AS Polizze
                            FROM 
                                   vodafone
                            INNER JOIN 
                                   aggiuntaVodafone 
                            ON vodafone.id = aggiuntaVodafone.id 
                            WHERE 
                                    dataVendita <= '$dataMaggiore' 
                            AND dataVendita >= '$dataMinore'
                            AND statoPda IN ('OK FIRMA', 'OK INSERITO', 'OK INSERITO MOBILE', 'OK RECALL', 'OK VOCAL', 'RECUPERO DATI', 'RECUPERO RECALL')
                            AND codiceCampagna NOT IN ('AMAZON', 'inBound', 'SPN_LEAD', 'SPN_TELCO', 'SPONS LEAD', 'VDF_SPON', 'Vodafone Leads')
                            GROUP BY
                                Campagna;
";
//            echo $queryCrmSede;

            break;
        case "enel_out":
            $queryCrmSede = "SELECT 
                                    sede,
                                   'EnelOut' AS EnelOut,
                            SUM(pezzoLordo) AS Prodotto,
                            SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
                            FROM 
                                    enelOut
                            inner JOIN
                                    aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id 
                            where 
                                    data<='$dataMaggiore' and data>='$dataMinore'
                           AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                           AND comodity <> 'Fibra'
                           GROUP BY 
                           sede";
            break;

        case "Iren":
            $queryCrmSede = "SELECT "
                    . " sede, "
                    . " 'iren' AS iren, "
                    . " SUM(pezzoLordo) AS Prodotto, "
                    . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito "
                    . " FROM "
                    . "`iren`  "
                    . " inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
                    . " where "
                    . " data<='$dataMaggiore' and data>='$dataMinore' "
                    . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco') "
                    . " AND comodity <> 'Polizza' "
                    . " GROUP BY "
                    . " sede ";
            break;

        case "Union":
            $queryCrmSede = "SELECT
                                    sede,
                             REPLACE
                                   ('know.union', 'know.', '') AS mandato,
                                    'All_Out' AS tipoCampagna ,
                                    'OutBound' as tipo,
                             SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,
                             SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze
                             FROM
                                    know.union
                            INNER JOIN aggiuntaUnion ON know.union.id = aggiuntaUnion.id
                            WHERE data<='$dataMaggiore' and data>='$dataMinore'
                            AND statoPda in ( 'ok Firma' , 'ok firma')
                            AND tipoCampagna <> 'Lead'
                            GROUP BY
                                     sede
                                    order by sede";
//                        echo $queryCrmSede;
            break;
        
//                case "Heracom":
//            $queryCrmSede = "SELECT "
//                    . " sede, "
//                    . " 'heracom' AS heracom, "
//                    . " SUM(pezzoLordo) AS Prodotto, "
//                    . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito "
//                    . " FROM "
//                    . "`heracom`  "
//                    . " inner JOIN aggiuntaHeracom on heracom.id=aggiuntaHeracom.id "
//                    . " where "
//                    . " data<='$dataMaggiore' and data>='$dataMinore' "
//                    . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco') "
//                    . " AND comodity <> 'Polizza' "
//                    . " GROUP BY "
//                    . " sede ";
//            break;
//        
//                case "EnelIn":
//            $queryCrmSede = "SELECT "
//                    . " sede, "
//                    . " 'enelIn' AS enelIn, "
//                    . " SUM(pezzoLordo) AS Prodotto, "
//                    . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito "
//                    . " FROM "
//                    . "`enelIn`  "
//                    . " inner JOIN aggiuntaEnelIn on enelIn.id=aggiuntaEnelIn.id "
//                    . " where "
//                    . " data<='$dataMaggiore' and data>='$dataMinore' "
//                    . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco') "
//                    . " AND comodity <> 'Polizza' "
//                    . " GROUP BY "
//                    . " sede ";
//            break;

    }




    $risultatoCrmSede = $conn19->query($queryCrmSede);

    while ($rigaCRM = $risultatoCrmSede->fetch_array()) {

        $sede = $rigaCRM[0];
        $sedeRicerca = ucwords($sede);
        $descrizioneMandato = $rigaCRM[1];
        $tipoCampagna = $rigaCRM[2];
        $tipo = $rigaCRM[3];
        $cp = $rigaCRM[4];
        $polizze = $rigaCRM[5];

        /*
          $queryGroupMandato = "SELECT sum(numero)/3600 as ore "
          . "FROM `stringheTotale`  "
          . "where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello<=6  "
          . " and sede='$sede'  and idMandato='$idMandato'  and mandato <> 'Lead Inbound' "
          . "group by sede";
          //        echo $queryGroupMandato;
          $risultaOre = $conn19->query($queryGroupMandato);
          if (($risultaOre->num_rows) > 0) {
          $rigaOre = $risultaOre->fetch_array();
          $ore = $rigaOre[0];
          } else {
          $ore = 0;
          }
         * 
         */

        /**
         * Query di ricerca dal siscall dell'orario
         */
        $queryRicerca = "SELECT "
                . "territory AS citta, "
                . "campaign_description AS mandato,"
                . "SUM(pause_sec+wait_sec+talk_sec+dispo_sec)/3600 AS 'numero'"
                . "FROM vicidial_agent_log AS v "
                . "INNER JOIN vicidial_users AS operatore ON v.user=operatore.user "
                . "INNER JOIN vicidial_campaigns as campagna ON v.campaign_id=campagna.campaign_id "
                . "WHERE event_time >= '$dataMinoreIta' AND event_time <= '$dataMaggioreIta' "
                . " AND territory='$sede' "
                . " AND campaign_description <> 'Lead Inbound' "
                . " AND campaign_description like '$idMandato%' ";

        $risultaOre = $connS2->query($queryRicerca);
        if (($risultaOre->num_rows) > 0) {
            $rigaOre = $risultaOre->fetch_array();
            $ore = $rigaOre[2];
        } else {
            $ore = 0;
        }

//        echo $queryRicerca;

        $pezzoLordo = round($rigaCRM[2], 0);
        $pezzoOk = round($rigaCRM[3], 0);
           if ($pezzoLordo == 0) {
            $caduta = '0.00%'; // O qualsiasi valore di default che preferisci
        } else {
            $caduta = number_format((($pezzoLordo - $pezzoOk) / $pezzoLordo) * 100, 2) . '%';
        }
        $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);
        $resalordo = ($ore == 0) ? 0 : round($pezzoLordo / $ore, 2);

        $html .= "<tr>";
        $html .= "<td >$sede</td>";
        $html .= "<td >$idMandato</td>";
        $html .= "<td >$tipoCampagna</td>";
        $html .= "<td style = 'border-left: 2px solid lightslategray'>$tipo</td>";

        $html .= "<td style = 'border-left: 2px solid lightslategray'>" . round($ore, 2) . "</td>";

        $html .= "<td style = 'border-left: 2px solid lightslategray'>$cp</td>";
        $html .= "<td style = 'border-left: 2px solid lightslategray'>$polizze</td>";

        $html .= "<td style = 'border-left: 2px solid lightslategray'>$resa</td>";

//        $html .= "<td style = 'border-left: 2px solid lightslategray'>$caduta</td>";
//        $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoBkl</td>";


        $html .= "</tr>";

        $sedePrecedente = $sede;

        $oreSede += $ore;
    }


    $html = "<table class='blueTable'>";

// Include dell'intestazione
    include "../../tabella/intestazioneTabellaChiusura.php";

// Ciclo su ogni mandato
    foreach ($mandato as $idMandato) {

        // Azzera i contatori per il mandato corrente
        $totaleOre = 0;
        $totaleCp = 0;
        $totalePezzoLordo = 0;
        $totalePezzoOk = 0;

        // Switch per costruire la query basata sul mandato
        switch ($idMandato) {
            // Esempio con "Plenitude"
            case "Plenitude":
                $queryCrmSede = "SELECT
                                        Sede,
                                       'Plenitude' AS Mandato,
                                       'All_Out' as tipoCampagna,
                                        'OutBound' as tipo,
                                SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,
                                SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze
                                FROM
                                       `plenitude`
                                INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id
                                WHERE DATA <= '$dataMaggiore' AND DATA >= '$dataMinore' AND statoPda ='Ok Firma'
                                AND tipoCampagna <> 'Lead'
                                GROUP BY sede
                                ORDER BY sede";
                break;

            case "Green Network":
                $queryCrmSede = "SELECT 
                                      sede,
                                     'Green Network' AS green,
                                 SUM(pezzoLordo) AS Prodotto,
                                 SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
                                 FROM 
                                      green
                                INNER JOIN 
                                      aggiuntaGreen 
                                      ON green.id = aggiuntaGreen.id 
                                 WHERE 
                                      data<='$dataMaggiore' and data>='$dataMinore' 
                                 AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                                 AND comodity <> 'Polizza'
                                 GROUP BY 
                                       sede";
                break;
            case "Vivigas Energia":
                $queryCrmSede = "SELECT
                                       sede,
                                      'Vivigas' AS Mandato,
                                      'All_Out' AS tipoCampagna,
                                      'OutBound' AS tipo,
                                SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,
                                SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze
                                FROM
                                     `vivigas`
                                INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id
                                WHERE 
                                        data<='$dataMaggiore' and data>='$dataMinore' AND statoPda IN ( 'Ok Definitivo' , 'Ok inserito' , 'Da Avanzare' , 'COMPLETATO FIRMA' , 'IN ATTESA IDENTIFICAZIONE'  , 'INVIATA SECONDA MAIL' , 'IN ATTESA SECONDA MAIL' , 'OK VOCAL RECALL')  AND tipoCampagna NOT IN  ('Lead')
                                GROUP BY
                                        sede
                                ORDER BY
                                        sede
";

                break;
            case "Vodafone":
                $queryCrmSede = "SELECT 
                                      'Lamezia' AS Sede,
                                      'Vodafone' AS Mandato,
                                      'All_Out' AS Campagna,
                                      'OutBound' AS tipo,
                                SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito,
                                  SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo = 'Polizza' ELSE 0 END) AS Polizze
                                FROM 
                                       vodafone
                                INNER JOIN 
                                       aggiuntaVodafone 
                                ON vodafone.id = aggiuntaVodafone.id 
                                WHERE 
                                        dataVendita <= '$dataMaggiore' 
                                AND
                                       dataVendita >= '$dataMinore'
                                AND 
                                       statoPda IN ('OK FIRMA', 'OK INSERITO', 'OK INSERITO MOBILE', 'OK RECALL', 'OK VOCAL', 'RECUPERO DATI', 'RECUPERO RECALL')
                                AND 
                                        codiceCampagna NOT IN ('AMAZON', 'inBound', 'SPN_LEAD', 'SPN_TELCO', 'SPONS LEAD', 'VDF_SPON', 'Vodafone Leads')
                                GROUP BY
                                         Campagna;


";
//            echo $queryCrmSede;

                break;
            case "enel_out":
                $queryCrmSede = "SELECT 
                                        sede,
                                       'EnelOut' AS EnelOut,
                                SUM(pezzoLordo) AS Prodotto,
                                SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
                                FROM 
                                       enelOut
                               inner JOIN 
                                       aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id 
                               where 
                                       data<='$dataMaggiore' and data>='$dataMinore'
                                AND 
                                       statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
                                AND 
                                       comodity <> 'Fibra'
                                GROUP BY 
                                          sede";
                break;

            case "Iren":
                $queryCrmSede = "SELECT "
                        . " sede, "
                        . " 'iren' AS iren, "
                        . " SUM(pezzoLordo) AS Prodotto, "
                        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito "
                        . " FROM "
                        . "`iren`  "
                        . " inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
                        . " where "
                        . " data<='$dataMaggiore' and data>='$dataMinore' "
                        . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco') "
                        . " AND comodity <> 'Polizza' "
                        . " GROUP BY "
                        . " sede ";
                break;

//            case "Heracom":
//                $queryCrmSede = "SELECT "
//                        . " sede, "
//                        . " 'heracom' AS heracom, "
//                        . " SUM(pezzoLordo) AS Prodotto, "
//                        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito "
//                        . " FROM "
//                        . "`heracom`  "
//                        . " inner JOIN aggiuntaHeracom on heracom.id=aggiuntaHeracom.id "
//                        . " where "
//                        . " data<='$dataMaggiore' and data>='$dataMinore' "
//                        . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco') "
//                        . " AND comodity <> 'Polizza' "
//                        . " GROUP BY "
//                        . " sede ";
//                break;
//            
//            case "EnelIn":
//                $queryCrmSede = "SELECT "
//                        . " sede, "
//                        . " 'enelIn' AS enelIn, "
//                        . " SUM(pezzoLordo) AS Prodotto, "
//                        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito "
//                        . " FROM "
//                        . "`enelIn`  "
//                        . " inner JOIN aggiuntaEnelIn on enelIn.id=aggiuntaEnelIn.id "
//                        . " where "
//                        . " data<='$dataMaggiore' and data>='$dataMinore' "
//                        . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco') "
//                        . " AND comodity <> 'Polizza' "
//                        . " GROUP BY "
//                        . " sede ";
//                break;

            case "Union":
                $queryCrmSede = "SELECT
                                       sede,
                                REPLACE
                                     ('know.union', 'know.', '') AS mandato,
                                      'All_Out' AS tipoCampagna ,
                                      'OutBound' as tipo,
                                SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,
                                SUM(  CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze
                                FROM
                                      know.union
                               INNER JOIN 
                                      aggiuntaUnion ON know.union.id = aggiuntaUnion.id
                               WHERE 
                                     data<='$dataMaggiore' and data>='$dataMinore'
                               AND 
                                    statoPda in ( 'ok Firma' , 'ok firma')
                               AND 
                                    tipoCampagna <> 'Lead'
                               GROUP BY
                                    sede
                                    order by sede";
//                        echo $queryCrmSede;
                break;
            // Aggiungere gli altri casi come nel tuo codice
            // ...
        }
$polizze = 0;
        // Esegue la query per il mandato corrente
        $risultatoCrmSede = $conn19->query($queryCrmSede);

        // Ciclo sui risultati per la sede corrente
        while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
            $sede = $rigaCRM[0];
            $cp = $rigaCRM[4];
            $polizze =  $rigaCRM[5];
//            // Calcolo delle ore
//            $queryGroupMandato = "SELECT sum(numero)/3600 as ore
//            FROM `stringheTotale`
//            WHERE giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello<=6
//            AND sede='$sede' AND idMandato='$idMandato' AND mandato <> 'Lead Inbound'
//            GROUP BY sede";
//            $risultaOre = $conn19->query($queryGroupMandato);
//            if ($risultaOre->num_rows > 0) {
//                $rigaOre = $risultaOre->fetch_array();
//                $ore = $rigaOre[0];
//            } else {
//                $ore = 0;
//            }


            $queryRicerca = "SELECT "
                    . "territory AS citta, "
                    . "campaign_description AS mandato,"
                    . "SUM(pause_sec+wait_sec+talk_sec+dispo_sec)/3600 AS 'numero'"
                    . "FROM vicidial_agent_log AS v "
                    . "INNER JOIN vicidial_users AS operatore ON v.user=operatore.user "
                    . "INNER JOIN vicidial_campaigns as campagna ON v.campaign_id=campagna.campaign_id "
                    . "WHERE event_time >= '$dataMinoreIta' AND event_time <= '$dataMaggioreIta' "
                    . " AND territory='$sede' "
                    . " AND campaign_description <> 'Lead Inbound' "
                    . " AND campaign_description like '$idMandato%' ";

            $ore=0;
            $risultaOre = $connS2->query($queryRicerca);
            if (($risultaOre->num_rows) > 0) {
                $rigaOre = $risultaOre->fetch_array();
                $ore = $rigaOre[2];
            } else {
                $ore = 0;
            }

            $risultaOre = $connGt->query($queryRicerca);
            if (($risultaOre->num_rows) > 0) {
                $rigaOre = $risultaOre->fetch_array();
                $ore += $rigaOre[2];
            } else {
                $ore += 0;
            }
            
            
//            $risultaOre = $connDgt->query($queryRicerca);
//            if (($risultaOre->num_rows) > 0) {
//                $rigaOre = $risultaOre->fetch_array();
//                $ore += $rigaOre[2];
//            } else {
//                $ore += 0;
//            }
            
            $risultaOre = $connLead->query($queryRicerca);
            if (($risultaOre->num_rows) > 0) {
                $rigaOre = $risultaOre->fetch_array();
                $ore += $rigaOre[2];
            } else {
                $ore += 0;
            }


            // Calcolo della resa
            $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);
            
            $totalepolizze += $polizze;
            $totaleOre += $ore;
            $totaleCp += $cp;
            
            $totaleResa = ($totaleOre == 0) ? 0 : round($totaleCp / $totaleOre, 2);
            // Aggiunta della riga per la sede corrente
            $html .= "<tr>";
            $html .= "<td>$sede</td>";
            $html .= "<td>$idMandato</td>";
            $html .= "<td>$tipoCampagna</td>";
            $html .= "<td style='border-left: 2px solid lightslategray'>$tipo</td>";
            $html .= "<td style='border-left: 2px solid lightslategray'>" . round($ore, 2) . "</td>";
            $html .= "<td style='border-left: 2px solid lightslategray'>$cp</td>";
            $html .= "<td style='border-left: 2px solid lightslategray'>$polizze</td>";
            $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
            $html .= "</tr>";
        }

        // Aggiunta della riga con il totale per il mandato corrente
        $html .= "<tr style='background-color: orange;'>";
        $html .= "<td colspan='4'>Totale $idMandato</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>" . round($totaleOre, 2) . "</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$totaleCp</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$totalepolizze</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$totaleResa</td>";
        $html .= "</tr>";
    }

    $html .= "</table>";
}
echo $html;

