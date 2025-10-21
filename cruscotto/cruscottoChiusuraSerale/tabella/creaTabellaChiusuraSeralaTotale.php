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

$conn = 0;
//$objDgt = new connessioneDigital();
//$connDgt = $objDgt->apriConnessioneDigital();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$agenzia = filter_input(INPUT_POST, "agenzieSelezionate");
$categoria = filter_input(INPUT_POST, "categoria");
// Converti le date nel formato italiano
$dataMinore = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggiore = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$ore = 0;
$dataMinoreOre = date('Y-m-d', strtotime($dataMinore));
$dataMaggioreOre = date('Y-m-d', strtotime($dataMaggiore));
$polizze = 0;
$dataOggi = date("Y-m-d");
$oreIN = 0;
$oreOut = 0;
$operatore = 0;

$elencoMandati = json_decode($_POST["mandato"], true);
$elencoSede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggioreIta = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($elencoMandati);

$lunghezzaSede = count($elencoSede);

/*
 * Import DElla base della tabella
 */
$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaChiusuraTotale.php";
/**
 * Recupero Ore Inbound da siscall2
 */
$siscall2 = [];
$siscallGT = [];
//$siscallDGT = [];
$siscalLead = [];
$queryOre = "SELECT"
        . " territory AS citta,"
        . " SUBSTRING_INDEX(campaign_description, ' ', 1) AS mandato,"
        . " SUM(CASE WHEN v.campaign_id = 'SPN_INB' OR v.campaign_id = 'VDF_TLCO' THEN (pause_sec + wait_sec + talk_sec + dispo_sec) ELSE 0 END	) / 3600 AS 'Inbound',"
        . " SUM(CASE WHEN v.campaign_id != 'SPN_INB' AND v.campaign_id != 'VDF_TLCO' THEN (pause_sec + wait_sec + talk_sec + dispo_sec) ELSE 0 END) / 3600 AS 'Outbound'"
        . " FROM"
        . " vicidial_agent_log AS v"
        . " INNER JOIN vicidial_users AS operatore ON v.user = operatore.user"
        . " INNER JOIN vicidial_campaigns AS campagna ON v.campaign_id = campagna.campaign_id "
        . " WHERE"
        . " event_time >= '$dataMinore'	"
        . " AND event_time <= '$dataMaggiore' "
        . " AND territory<>'BO' and territory<>'tl'"
        . " GROUP BY"
        . " territory,mandato";
//echo $queryOre;
try {
    $risultatoOre = $connLead->query($queryOre);
} catch (Exception $ex) {
    echo "Errore Siscall2: " . $ex;
}
while (($rigaOre = $risultatoOre->fetch_array())) {
    $sede = strtoupper($rigaOre['citta']);
    $mandato = strtoupper($rigaOre['mandato']);
    $oreIn = round($rigaOre['Inbound'], 2);
    $oreOut = round($rigaOre['Outbound'], 2);
    $siscall2[$sede][$mandato] = [$oreIn, $oreOut];
}


$risultatoOre = $connGt->query($queryOre);

while (($rigaOre = $risultatoOre->fetch_array())) {
    $sede = strtoupper($rigaOre['citta']);
    $mandato = strtoupper($rigaOre['mandato']);
    $oreIn = round($rigaOre['Inbound'], 2);
    $oreOut = round($rigaOre['Outbound'], 2);
    $siscallGT[$sede][$mandato] = [$oreIn, $oreOut];
}

$risultatoOre = $connLead->query($queryOre);

while (($rigaOre = $risultatoOre->fetch_array())) {
    $sede = strtoupper($rigaOre['citta']);
    $mandato = strtoupper($rigaOre['mandato']);
    $oreIn = round($rigaOre['Inbound'], 2);
    $oreOut = round($rigaOre['Outbound'], 2);
    $siscallLead[$sede][$mandato] = [$oreIn, $oreOut];
}

/*
 * Plenitude
 */
$pezziPlenitude = [];
$queryCrmSede = "SELECT"
        . " sede,"
        . " 'Plenitude' AS Mandato,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze"
        . " FROM"
        . " plenitude "
        . " INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id"
        . " WHERE "
        . " DATA<= '$dataMaggiore' "
        . " AND DATA >= '$dataMinore' "
        . " AND statoPda ='Ok Firma'"
        . " GROUP BY"
        . " sede"
        . " order by"
        . " sede";

$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $sede = strtoupper($rigaCRM["sede"]);
    $cp = $rigaCRM["CP"];
    $polizze = $rigaCRM["Polizze"];
    $pezziPlenitude[$sede] = [$cp, $polizze];
}

$pezziGreen = [];
$queryCrmSede = "SELECT"
        . " sede,"
        . " 'Green Network' AS Mandato,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,"
        . " SUM( CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze"
        . " FROM"
        . " green "
        . " INNER JOIN aggiuntaGreen ON green.id = aggiuntaGreen.id "
        . " WHERE "
        . " DATA<= '$dataMaggiore' "
        . " AND DATA >= '$dataMinore' "
        . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')"
        . " GROUP BY"
        . " sede"
        . " order by"
        . " sede";

$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $sede = strtoupper($rigaCRM["sede"]);
    $cp = $rigaCRM["CP"];
    $polizze = $rigaCRM["Polizze"];
    $pezziGreen[$sede] = [$cp, $polizze];
}

$pezziViviGas = [];
$queryCrmSede = "SELECT"
        . " sede,"
        . " 'Vivigas' AS Mandato,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,"
        . " SUM( CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze"
        . " FROM"
        . " vivigas "
        . " INNER JOIN aggiuntaVivigas ON vivigas.id = aggiuntaVivigas.id"
        . " WHERE "
        . " DATA<= '$dataMaggiore' "
        . " AND DATA >= '$dataMinore' "
        . " AND statoPda IN ( 'Ok Definitivo' , 'Ok inserito' , 'Da Avanzare' , 'COMPLETATO FIRMA' , 'IN ATTESA IDENTIFICAZIONE'  , 'INVIATA SECONDA MAIL' , 'IN ATTESA SECONDA MAIL' , 'OK VOCAL RECALL')"
        . " GROUP BY"
        . " sede"
        . " order by"
        . " sede";

$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $sede = strtoupper($rigaCRM["sede"]);
    $cp = $rigaCRM["CP"];
    $polizze = $rigaCRM["Polizze"];
    $pezziViviGas[$sede] = [$cp, $polizze];
}


$pezziVodafone = [];
$queryCrmSede = "SELECT"
        . " 'Lamezia' as sede,"
        . " 'Vodafone' AS Mandato,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo  ELSE 0 END) AS CP,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo  ELSE 0 END) AS Polizze"
        . " FROM"
        . " vodafone "
        . " INNER JOIN  aggiuntaVodafone ON vodafone.id = aggiuntaVodafone.id"
        . " WHERE "
        . " dataVendita<= '$dataMaggiore' "
        . " AND dataVendita >= '$dataMinore' "
        . " AND statoPda IN ('OK FIRMA', 'OK INSERITO', 'OK INSERITO MOBILE', 'OK RECALL', 'OK VOCAL', 'RECUPERO DATI', 'RECUPERO RECALL')"
        . " GROUP BY"
        . " sede"
        . " order by"
        . " sede";

$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $sede = strtoupper($rigaCRM["sede"]);
    $cp = $rigaCRM["CP"];
    $polizze = $rigaCRM["Polizze"];
    $pezziVodafone[$sede] = [$cp, $polizze];
}

$pezziEnelOut = [];
$queryCrmSede = "SELECT"
        . " sede,"
        . " 'EnelOut' AS Mandato,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze"
        . " FROM"
        . " enelOut "
        . " inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id  "
        . " WHERE "
        . " DATA<= '$dataMaggiore' "
        . " AND DATA >= '$dataMinore' "
        . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco') AND comodity <> 'Fibra'"
        . " GROUP BY"
        . " sede"
        . " order by"
        . " sede";

$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $sede = strtoupper($rigaCRM["sede"]);
    $cp = $rigaCRM["CP"];
    $polizze = $rigaCRM["Polizze"];
    $pezziEnelOut[$sede] = [$cp, $polizze];
}



$pezziIren = [];
$queryCrmSede = "SELECT"
        . " sede,"
        . " 'iren' AS Mandato,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze"
        . " FROM"
        . " iren "
        . " inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id"
        . " WHERE "
        . " DATA<= '$dataMaggiore' "
        . " AND DATA >= '$dataMinore' "
        . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')"
        . " GROUP BY"
        . " sede"
        . " order by"
        . " sede";

$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $sede = strtoupper($rigaCRM["sede"]);
    $cp = $rigaCRM["CP"];
    $polizze = $rigaCRM["Polizze"];
    $pezziIren[$sede] = [$cp, $polizze];
}

$pezziHeracom = [];
$queryCrmSede = "SELECT"
        . " sede,"
        . " 'heracom' AS Mandato,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Consenso' ELSE 0 END) AS CP,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze"
        . " FROM"
        . " heracom "
        . " inner JOIN aggiuntaHeracom on heracom.id=aggiuntaHeracom.id"
        . " WHERE "
        . " DATA<= '$dataMaggiore' "
        . " AND DATA >= '$dataMinore' "
        . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')"
        . " GROUP BY"
        . " sede"
        . " order by"
        . " sede";

$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $sede = strtoupper($rigaCRM["sede"]);
    $cp = $rigaCRM["CP"];
    $polizze = $rigaCRM["Polizze"];
    $pezziHeracom[$sede] = [$cp, $polizze];
}



$pezziEnelIn = [];
//$queryCrmSede = "SELECT"
//        . " sede,"
//        . " 'enelIn' AS Mandato,"
//        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Fibra' ELSE 0 END) AS CP,"
//        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze"
//        . " FROM"
//        . " heracom "
//        . " inner JOIN aggiuntaEnelIn on enelIn.id=aggiuntaEnelIn.id"
//        . " WHERE "
//        . " DATA<= '$dataMaggiore' "
//        . " AND DATA >= '$dataMinore' "
//        . " AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')"
//        . " GROUP BY"
//        . " sede"
//        . " order by"
//        . " sede";
//
//$risultatoCrmSede = $conn19->query($queryCrmSede);
//while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
//    $sede = strtoupper($rigaCRM["sede"]);
//    $cp = $rigaCRM["CP"];
//    $polizze = $rigaCRM["Polizze"];
//    $pezziEnelIn[$sede] = [$cp, $polizze];
//}




$pezziUnion = [];
$queryCrmSede = "SELECT"
        . " sede,"
        . " 'Union' AS Mandato,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` <> 'Polizza' ELSE 0 END) AS CP,"
        . " SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo and `comodity` = 'Polizza' ELSE 0 END) AS Polizze"
        . " FROM"
        . " know.union "
        . " INNER JOIN aggiuntaUnion ON know.union.id = aggiuntaUnion.id"
        . " WHERE "
        . " DATA<= '$dataMaggiore' "
        . " AND DATA >= '$dataMinore' "
        . " AND statoPda in ( 'ok Firma' , 'ok firma')"
        . " GROUP BY"
        . " sede"
        . " order by"
        . " sede";
//echo $queryCrmSede;
$risultatoCrmSede = $conn19->query($queryCrmSede);
while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
    $sede = strtoupper($rigaCRM["sede"]);
    $cp = $rigaCRM["CP"];
    $polizze = $rigaCRM["Polizze"];
    $pezziUnion[$sede] = [$cp, $polizze];
}


$html = "<table class='blueTable'>";
include "../../tabella/intestazioneTabellaChiusuraTotale.php";
$oreSede = 0;
$cpSede = 0;
$polizzeSede = 0;
$ore = 0;
$cpTotale = 0;
$polizzeTotale = 0;
$oreTotale = 0;
foreach ($elencoSede as $idSede) {
    foreach ($elencoMandati as $idMandato) {

        $idSedeControllo = strtoupper($idSede);
        $idMandatoControllo = strtoupper($idMandato);
        $parole = explode(" ", $idMandatoControllo);
        $idMandatoControllo=$parole[0];
        //echo var_dump($pezziPlenitude);


        switch ($idMandato) {
            case "Plenitude":

                if (array_key_exists($idSedeControllo, $pezziPlenitude)) {
                    $cp = $pezziPlenitude[$idSedeControllo][0];
                }
                if (array_key_exists($idSedeControllo, $pezziPlenitude)) {
                    $polizze = $pezziPlenitude[$idSedeControllo][1];
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][1];
                    }
                }

                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists("LEAD", $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo]["LEAD"][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists("LEAD", $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo]["LEAD"][1];
                    }
                }

                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][0];
//                    }
//                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][1];
//                    }
//                }
                $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);

                $html .= "<tr>";
                $html .= "<td>$idSede</td>";
                $html .= "<td>$idMandato</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$cp</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$polizze</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round(($ore), 2) . "</td>";

                $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
                $html .= "</tr>";

                $oreSede += $ore;
                $ore = 0;
                $cpSede += $cp;
                $cp = 0;
                $polizzeSede += $polizze;
                $polizze = 0;
                break;
            case "Green Network":
                if (array_key_exists($idSedeControllo, $pezziGreen)) {
                    $cp = $pezziGreen[$idSedeControllo][0];
                }
                if (array_key_exists($idSedeControllo, $pezziGreen)) {
                    $polizze = $pezziGreen[$idSedeControllo][1];
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][0];
//                    }
//                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][1];
//                    }
//                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);

                $html .= "<tr>";
                $html .= "<td>$idSede</td>";
                $html .= "<td>$idMandato</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$cp</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$polizze</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round(($ore), 2) . "</td>";

                $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
                $html .= "</tr>";

                $oreSede += $ore;
                $ore = 0;
                $cpSede += $cp;
                $cp = 0;
                $polizzeSede += $polizze;
                $polizze = 0;
                break;
            case "Vivigas Energia":
                if (array_key_exists($idSedeControllo, $pezziViviGas)) {
                    $cp = $pezziViviGas[$idSedeControllo][0];
                }
                if (array_key_exists($idSedeControllo, $pezziViviGas)) {
                    $polizze = $pezziViviGas[$idSedeControllo][1];
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                       
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][0];
//                    }
//                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][1];
//                    }
//                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);

                $html .= "<tr>";
                $html .= "<td>$idSede</td>";
                $html .= "<td>$idMandato</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$cp</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$polizze</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round(($ore), 2) . "</td>";

                $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
                $html .= "</tr>";

                $oreSede += $ore;
                $ore = 0;
                $cpSede += $cp;
                $cp = 0;
                $polizzeSede += $polizze;
                $polizze = 0;
                break;
            case "Vodafone":
                if (array_key_exists($idSedeControllo, $pezziVodafone)) {
                    $cp = $pezziVodafone[$idSedeControllo][0];
                }
                if (array_key_exists($idSedeControllo, $pezziVodafone)) {
                    $polizze = $pezziVodafone[$idSedeControllo][1];
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][0];
//                    }
//                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][1];
//                    }
//                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);

                $html .= "<tr>";
                $html .= "<td>$idSede</td>";
                $html .= "<td>$idMandato</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$cp</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$polizze</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round(($ore), 2) . "</td>";

                $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
                $html .= "</tr>";

                $oreSede += $ore;
                $ore = 0;
                $cpSede += $cp;
                $cp = 0;
                $polizzeSede += $polizze;
                $polizze = 0;
                break;
            case "enel_out":
                if (array_key_exists($idSedeControllo, $pezziEnelOut)) {
                    $cp = $pezziEnelOut[$idSedeControllo][0];
                }
                if (array_key_exists($idSedeControllo, $pezziEnelOut)) {
                    $polizze = $pezziEnelOut[$idSedeControllo][1];
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][0];
//                    }
//                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][1];
//                    }
//                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);

                $html .= "<tr>";
                $html .= "<td>$idSede</td>";
                $html .= "<td>$idMandato</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$cp</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$polizze</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round(($ore), 2) . "</td>";

                $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
                $html .= "</tr>";

                $oreSede += $ore;
                $ore = 0;
                $cpSede += $cp;
                $cp = 0;
                $polizzeSede += $polizze;
                $polizze = 0;
                break;
            case "Iren":
                if (array_key_exists($idSedeControllo, $pezziEnelOut)) {
                    $cp = $pezziEnelOut[$idSedeControllo][0];
                }
                if (array_key_exists($idSedeControllo, $pezziEnelOut)) {
                    $polizze = $pezziEnelOut[$idSedeControllo][1];
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][0];
//                    }
//                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][1];
//                    }
//                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);

                $html .= "<tr>";
                $html .= "<td>$idSede</td>";
                $html .= "<td>$idMandato</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$cp</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$polizze</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round(($ore), 2) . "</td>";

                $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
                $html .= "</tr>";

                $oreSede += $ore;
                $ore = 0;
                $cpSede += $cp;
                $cp = 0;
                $polizzeSede += $polizze;
                $polizze = 0;
                break;
            case "Union":
                if (array_key_exists($idSedeControllo, $pezziUnion)) {
                    $cp = $pezziUnion[$idSedeControllo][0];
                }
                if (array_key_exists($idSedeControllo, $pezziUnion)) {
                    $polizze = $pezziUnion[$idSedeControllo][1];
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscall2)) {
                    if (array_key_exists($idMandatoControllo, $siscall2[$idSedeControllo])) {
                        $ore += $siscall2[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallGT)) {
                    if (array_key_exists($idMandatoControllo, $siscallGT[$idSedeControllo])) {
                        $ore += $siscallGT[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][0];
//                    }
//                }
//                if (array_key_exists($idSedeControllo, $siscallDGT)) {
//                    if (array_key_exists($idMandatoControllo, $siscallDGT[$idSedeControllo])) {
//                        $ore += $siscallDGT[$idSedeControllo][$idMandatoControllo][1];
//                    }
//                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][0];
                    }
                }
                if (array_key_exists($idSedeControllo, $siscallLead)) {
                    if (array_key_exists($idMandatoControllo, $siscallLead[$idSedeControllo])) {
                        $ore += $siscallLead[$idSedeControllo][$idMandatoControllo][1];
                    }
                }
                $resa = ($ore == 0) ? 0 : round($cp / $ore, 2);

                $html .= "<tr>";
                $html .= "<td>$idSede</td>";
                $html .= "<td>$idMandato</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$cp</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>$polizze</td>";
                $html .= "<td style='border-left: 2px solid lightslategray'>" . round(($ore), 2) . "</td>";

                $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
                $html .= "</tr>";

                $oreSede += $ore;
                $ore = 0;
                $cpSede += $cp;
                $cp = 0;
                $polizzeSede += $polizze;
                $polizze = 0;
                break;
        }
    }
    $resaSede = ($oreSede == 0) ? 0 : round($cpSede / $oreSede, 2);
    $html .= "<tr style='background-color: orange;'>";
    $html .= "<td colspan='2'>Totale $idSede</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$cpSede</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$polizzeSede</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreSede, 2) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$resaSede</td>";
    $html .= "</tr>";
    $cpTotale += $cpSede;
    $polizzeTotale += $polizzeSede;
    $oreTotale += $oreSede;
    $oreSede = 0;
    $cpSede = 0;
    $polizzeSede = 0;

    
}

$resaTotale = ($oreTotale == 0) ? 0 : round($cpTotale / $oreTotale, 2);
    $html .= "<tr style='background-color: red;'>";
    $html .= "<td colspan='2'>Totale </td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$cpTotale</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$polizzeTotale</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreTotale, 2) . "</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$resaTotale</td>";
    $html .= "</tr>";
  

echo $html;
?>  