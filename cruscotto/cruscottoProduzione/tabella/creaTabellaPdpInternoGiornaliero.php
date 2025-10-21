<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$mandato =  ["Plenitude"];

$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$mese = date('Y-m-01', strtotime($dataMaggiore));
$data = date("Y-m-d");

$queryMandato = "";
//$lunghezza = count($mandato);

$querySede = "";
$lunghezzaSede = count($sede);
$totaleGiorni = 0;
$totaleMese = 0;

if ($lunghezzaSede == 1) {
    $querySede .= " AND sede='$sede[0]' ";
} else {
    for ($l = 0; $l < $lunghezzaSede; $l++) {
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

$html = "<table class='blueTable' style='width: 40%'>";
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th colspan='6'>DETTAGLIO PDP INTERNO</th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>Mandato</th>";
$html .= "<th>Stato PDA</th>";
$html .= "<th>Macro CRM</th>";
$html .= "<th>GG</th>";
$html .= "<th>Mensile</th>";
$html .= "<th>%</th>";
$html .= "</tr>";
$html .= "</thead>";
foreach ($mandato as $idMandato) {
    switch ($idMandato) {
        case "Plenitude":
            $queryCrm = "SELECT "
                    . "statoPda, fasePDA, COUNT(statoPDA) ,SUM(if((data='$data'),1,0)) "
                    . "FROM "
                    . "`plenitude`  "
                    . "inner join aggiuntaPlenitude ON  plenitude.id=aggiuntaPlenitude.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza' and tipoCampagna = 'Lead'"
                    . $querySede
                    . " group by statoPda";
//echo $queryCrm;
            break;
        
        case "Iren":
            $queryCrm = "SELECT "
                    . "statoPda, fasePDA, COUNT(statoPDA) ,SUM(if((data='$data'),1,0)) "
                    . "FROM "
                    . "`iren`  "
                    . "inner join aggiuntaIren ON  iren.id=aggiuntaIren.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity<>'Polizza' and tipoCampagna = 'Lead'"
                    . $querySede
                    . " group by statoPda";
//echo $queryCrm;
            break;
        case "Green Network":
            $queryCrm = "SELECT "
                    . "statoPda, fasePDA, COUNT(statoPDA) ,SUM(if((data='$data'),1,0)) "
                    . "FROM "
                    . "green "
                    . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and comodity<>'Polizza' and tipoCampagna = 'Lead'"
                    . $querySede
                    . " group by statoPda";
            break;
        case "Vivigas Energia":
            $queryCrm = "SELECT "
                    . "statoPda, fasePDA, sum(pezzoLordo) ,SUM(if((data='$data'),pezzoLordo,0)) "
                    . "FROM "
                    . "vivigas "
                    . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and tipoCampagna = 'Lead' "
                    . $querySede
                    . " group by statoPda";
            break;
        case "Vodafone":
            $queryCrm = "SELECT "
                    . "statoPda, fasePDA, COUNT(statoPDA) ,SUM(if((data='$data'),1,0)) "
                    . "FROM "
                    . "vodafone "
                    . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and tipoCampagna = 'Lead' "
                    . $querySede
                    . " group by statoPda";
            break;
        case "enel_out":
            $queryCrm = "SELECT "
                    . "statoPda, fasePDA, COUNT(statoPDA) ,SUM(if((data='$data'),1,0)) "
                    . "FROM "
                    . "enelOut "
                    . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and tipoCampagna = 'Lead' "
                    . $querySede
                    . " group by statoPda";
            break;
    }

    echo "<br>";
//echo $queryCrm;
    $risultatoCrm = $conn19->query($queryCrm);
    while ($rigaCRM = $risultatoCrm->fetch_array()) {
        $html .= "<tr>";
        $html .= "<td>" . $idMandato . "</td>";
        $html .= "<td>" . $rigaCRM[0] . "</td>";
        $html .= "<td>" . $rigaCRM[1] . "</td>";
        $html .= "<td>" . $rigaCRM[3] . "</td>";
        $totaleGiorni += $rigaCRM[3];
        $html .= "<td>" . $rigaCRM[2] . "</td>";
        $totaleMese += $rigaCRM[2];
        $html .= "<td>" . round((($rigaCRM[3] / $rigaCRM[2]) * 100), 2) . "</td>";
        $html .= "</tr>";
    }
    $html .= "<tr>";
    $html .= "<td colspan='3' style='background-color:yellow'>Totale</td>";
    $html .= "<td  style='background-color:yellow'>" . $totaleGiorni . "</td>";
    $html .= "<td  style='background-color:yellow'>" . $totaleMese . "</td>";
    $html .= "<td  style='background-color:yellow'>" .  (($totaleMese==0)?0:round((($totaleGiorni / $totaleMese) * 100), 2)) . "</td>";

    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<td colspan='6' style='background-color:mediumseagreen'></td>";
    $html .= "</tr>";
}

$html .= "</tr></table><br>";

if ($idMandato == 'Plenitude') {
    include_once "creaTabellaPdpInternoPolizzeGiornaliero.php";
//    include_once "creaTabellaPdpEsternoLeadInvertito.php";
}
echo $html;


