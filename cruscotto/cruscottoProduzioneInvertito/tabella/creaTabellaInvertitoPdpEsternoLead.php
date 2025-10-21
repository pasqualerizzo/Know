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
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$mese = date('Y-m-01', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);

$querySede = "";
$lunghezzaSede = count($sede);

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
$html .= "<th colspan='6'>DETTAGLIO PDP Esterno</th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>Mandato</th>";
$html .= "<th>Stato Post</th>";
$html .= "<th>Macro Post</th>";
$html .= "<th>Range</th>";
$html .= "<th>Mensile</th>";
$html .= "<th>%</th>";
$html .= "</tr>";
$html .= "</thead>";
foreach ($mandato as $idMandato) {
    switch ($idMandato) {
        case "Plenitude":
            $queryCrm = "SELECT "
                    . "statoPost, fasePost, COUNT(statoPost) ,SUM(if((data<='$dataMaggiore'and data>='$dataMinore'),1,0)) "
                    . "FROM "
                    . "`plenitude`  "
                    . "inner join aggiuntaPlenitude ON  plenitude.id=aggiuntaPlenitude.id "
                    . "where mese='$mese'  and comodity<>'Polizza' AND fasePDA='OK'  and tipoCampagna = 'Lead' "
                    . $querySede
                    . "group by statoPost";

            $queryCrmRange = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "`plenitude`  "
                    . "inner join aggiuntaPlenitude ON  plenitude.id=aggiuntaPlenitude.id "
                    . "where    comodity<>'Polizza' AND fasePDA='OK'  and tipoCampagna = 'Lead' "
                    . "and data<='$dataMaggiore'and data>='$dataMinore' "
                    . $querySede;

            $queryCrmTotale = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "`plenitude`  "
                    . "inner join aggiuntaPlenitude ON  plenitude.id=aggiuntaPlenitude.id "
                    . "where mese='$mese'  and comodity<>'Polizza' AND fasePDA='OK' and tipoCampagna = 'Lead'  "
                    . $querySede;

//            echo $queryCrm;
            break;
        
        case "Iren":
            $queryCrm = "SELECT "
                    . "statoPost, fasePost, COUNT(statoPost) ,SUM(if((data<='$dataMaggiore'and data>='$dataMinore'),1,0)) "
                    . "FROM "
                    . "`iren`  "
                    . "inner join aggiuntaIren ON  iren.id=aggiuntaIren.id "
                    . "where mese='$mese'  and comodity<>'Polizza' AND fasePDA='OK' and tipoCampagna = 'Lead'  "
                    . $querySede
                    . "group by statoPost";

            $queryCrmRange = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "`iren`  "
                    . "inner join aggiuntaIren ON  iren.id=aggiuntaIren.id "
                    . "where    comodity<>'Polizza' AND fasePDA='OK'  and tipoCampagna = 'Lead' "
                    . "and data<='$dataMaggiore'and data>='$dataMinore' "
                    . $querySede;

            $queryCrmTotale = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "`iren`  "
                    . "inner join aggiuntaIren ON  iren.id=aggiuntaIren.id "
                    . "where mese='$mese'  and comodity<>'Polizza' AND fasePDA='OK'  and tipoCampagna = 'Lead' "
                    . $querySede;

            //echo $queryCrm;
            break;
        
        case "Union":
            $queryCrm = "SELECT "
                    . "statoPost, fasePost, COUNT(statoPost) ,SUM(if((data<='$dataMaggiore'and data>='$dataMinore'),1,0)) "
                    . "FROM "
                    . "`union`  "
                    . "inner join aggiuntaUnion ON  union.id=aggiuntaUnion.id "
                    . "where mese='$mese'  and comodity<>'Polizza' AND fasePDA='OK' and tipoCampagna = 'Lead'  "
                    . $querySede
                    . "group by statoPost";

            $queryCrmRange = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "`union`  "
                    . "inner join aggiuntaUnion ON  union.id=aggiuntaUnion.id "
                    . "where    comodity<>'Polizza' AND fasePDA='OK'  and tipoCampagna = 'Lead' "
                    . "and data<='$dataMaggiore'and data>='$dataMinore' "
                    . $querySede;

            $queryCrmTotale = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "`union`  "
                    . "inner join aggiuntaUnion ON  union.id=aggiuntaUnion.id "
                    . "where mese='$mese'  and comodity<>'Polizza' AND fasePDA='OK'  and tipoCampagna = 'Lead' "
                    . $querySede;

//            echo $queryCrm;
            break;
        
        
        case "Green Network":
            $queryCrm = "SELECT "
                    . "statoPda, fasePost, COUNT(statoPDA) ,SUM(if((data<='$dataMaggiore'and data>='$dataMinore'),1,0)) "
                    . "FROM "
                    . "green "
                    . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>''  and tipoCampagna = 'Lead' "
                    . $querySede
                    . "group by statoPost";
            $queryCrmRange = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "green "
                    . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                    . "where   statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>''  and tipoCampagna = 'Lead' "
                    . "and data<='$dataMaggiore'and data>='$dataMinore' "
                    . $querySede;

            $queryCrmTotale = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "green "
                    . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>''  and tipoCampagna = 'Lead' "
                    . $querySede;
            break;
        case "Vivigas Energia":
            $queryCrm = "SELECT "
                    . "statoPost, fasePost, sum(pezzoLordo) ,SUM(if((data<='$dataMaggiore'and data>='$dataMinore'),pezzoLordo,0)) "
                    . "FROM "
                    . "vivigas "
                    . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                    . "where mese='$mese' AND statoPda IN ('Ok Definitivo', 'Ok inserito')   and tipoCampagna = 'Lead' "
                    . $querySede
                    . "group by statoPost";
//echo $queryCrm;
            $queryCrmRange = "SELECT "
                    . " sum(pezzoLordo) ,SUM(if((fasePost='OK'),pezzoLordo,0)),SUM(if((fasePost='KO'),pezzoLordo,0)),SUM(if((fasePost='BKL'),pezzoLordo,0)),SUM(if((fasePost='BKLP'),pezzoLordo,0)),SUM(if((fasePost=''),pezzoLordo,0)) "
                    . "FROM "
                    . "vivigas "
                    . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                    . "where   statoPda IN ('Ok Definitivo', 'Ok inserito')  and tipoCampagna = 'Lead'  "
                    . "and data<='$dataMaggiore'and data>='$dataMinore' "
                    . $querySede;

            $queryCrmTotale = "SELECT "
                    . " sum(pezzoLordo) ,SUM(if((fasePost='OK'),pezzoLordo,0)),SUM(if((fasePost='KO'),pezzoLordo,0)),SUM(if((fasePost='BKL'),pezzoLordo,0)),SUM(if((fasePost='BKLP'),pezzoLordo,0)),SUM(if((fasePost=''),pezzoLordo,0)) "
                    . "FROM "
                    . "vivigas "
                    . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                    . "where mese='$mese' AND statoPda IN ('Ok Definitivo', 'Ok inserito')  and tipoCampagna = 'Lead'    "
                    . $querySede;
            break;
        case "Vodafone":
            $queryCrm = "SELECT "
                    . "statoPda, fasePDA, COUNT(statoPDA) ,SUM(if((data<='$dataMaggiore'and data>='$dataMinore'),1,0)) "
                    . "FROM "
                    . "vodafone "
                    . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>''  and tipoCampagna = 'Lead'  "
                    . $querySede
                    . "group by statoPost";

            $queryCrmRange = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "vodafone "
                    . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
                    . "where   statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>''  and tipoCampagna = 'Lead'  "
                    . "and data<='$dataMaggiore'and data>='$dataMinore' "
                    . $querySede;

            $queryCrmTotale = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "vodafone "
                    . "inner JOIN aggiuntaVodafone on vodafone.id=aggiuntaVodafone.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>''  and tipoCampagna = 'Lead'  "
                    . $querySede;
            break;
        case "enel_out":
            $queryCrm = "SELECT "
                    . "statoPda, fasePDA, COUNT(statoPDA) ,SUM(if((data<='$dataMaggiore'and data>='$dataMinore'),1,0)) "
                    . "FROM "
                    . "enelOut "
                    . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>'' and tipoCampagna = 'Lead'  "
                    . $querySede
                    . "group by statoPost";

            $queryCrmRange = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "enelOut "
                    . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                    . "where   statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>''  and tipoCampagna = 'Lead'  "
                    . "and data<='$dataMaggiore'and data>='$dataMinore' "
                    . $querySede;

            $queryCrmTotale = "SELECT "
                    . " COUNT(statoPost) ,SUM(if((fasePost='OK'),1,0)),SUM(if((fasePost='KO'),1,0)),SUM(if((fasePost='BKL'),1,0)),SUM(if((fasePost='BKLP'),1,0)),SUM(if((fasePost=''),1,0)) "
                    . "FROM "
                    . "enelOut "
                    . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPost<>''  and tipoCampagna = 'Lead'  "
                    . $querySede;
            break;
    }
//echo "<br>";
//echo $queryCrm;
    $risultatoCrm = $conn19->query($queryCrm);
    while ($rigaCRM = $risultatoCrm->fetch_array()) {
        $html .= "<tr>";
        $html .= "<td>" . $idMandato . "</td>";
        $html .= "<td>" . $rigaCRM[0] . "</td>";
        $html .= "<td>" . $rigaCRM[1] . "</td>";
        $html .= "<td>" . $rigaCRM[3] . "</td>";
        $html .= "<td>" . $rigaCRM[2] . "</td>";
        $html .= "<td>" . round((($rigaCRM[3] / $rigaCRM[2]) * 100), 2) . "</td>";
        $html .= "</tr>";
    }
    $html .= "<tr>";
    $html .= "<td colspan='6' style='background-color:mediumseagreen'></td>";
    $html .= "</tr>";

    $risultatoCrmRange = $conn19->query($queryCrmRange);
    $rigaCRMRange = $risultatoCrmRange->fetch_array();
    $totaleRange = $rigaCRMRange[0];
    $okRange = $rigaCRMRange[1];
    $koRange = $rigaCRMRange[2];
    $bklRange = $rigaCRMRange[3];
    $bklpRange = $rigaCRMRange[4];
    $vuotoRange = $rigaCRMRange[5];

    $html .= "<tr style='background-color:yellow'>";
    $html .= "<td>OK Range</td>";
    $html .= "<td>KO Range</td>";
    $html .= "<td>BKL Range</td>";
    $html .= "<td>BKLP Range</td>";
    $html .= "<td>No Post Range</td>";
    $html .= "<td>Totale Range</td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td>" . $okRange . "</td>";
    $html .= "<td>" . $koRange . "</td>";
    $html .= "<td>" . $bklRange . "</td>";
    $html .= "<td>" . $bklpRange . "</td>";
    $html .= "<td>" . $vuotoRange . "</td>";
    $html .= "<td>" . $totaleRange . "</td>";
    $html .= "</tr>";

    $risultatoCrmTotale = $conn19->query($queryCrmTotale);
    $rigaCRMTotale = $risultatoCrmTotale->fetch_array();
    $totale = $rigaCRMTotale[0];
    $ok = $rigaCRMTotale[1];
    $ko = $rigaCRMTotale[2];
    $bkl = $rigaCRMTotale[3];
    $bklp = $rigaCRMTotale[4];
    $vuoto = $rigaCRMTotale[5];

    $html .= "<tr style='background-color:yellow'>";
    $html .= "<td>OK Totale</td>";
    $html .= "<td>KO Totale</td>";
    $html .= "<td>BKL Totale</td>";
    $html .= "<td>BKLP Totale</td>";
    $html .= "<td>No Post Totale</td>";
    $html .= "<td>Totale </td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td>" . $ok . "</td>";
    $html .= "<td>" . $ko . "</td>";
    $html .= "<td>" . $bkl . "</td>";
    $html .= "<td>" . $bklp . "</td>";
    $html .= "<td>" . $vuoto . "</td>";
    $html .= "<td>" . $totale . "</td>";
    $html .= "</tr>";

    
    if ($ok <> 0) {
        $okP = round(($okRange / $ok), 2)*100;
    } else {
        $okP = "-";
    }
    if ($ko <> 0) {
        $koP = round(($koRange / $ko), 2)*100;
    } else {
        $koP = "-";
    }
    if ($bkl <> 0) {
        $bklP = round(($bklRange / $bkl), 2)*100;
    } else {
        $bklP = "-";
    }
    
    
    if ($totale <> 0) {
        $totaleP = round(($totaleRange / $totale), 2)*100;
    } else {
        $totaleP = "-";
    }
    if ($bklp <> 0) {
        $bklpP = round(($bklpRange / $bklp), 2)*100;
    } else {
        $bklpP = "-";
    }
    if ($vuoto <> 0) {
        $vuotoP = round(($vuotoRange / $vuoto), 2)*100;
    } else {
        $vuotoP = "-";
    }

    $html .= "<tr style='background-color:yellow'>";
    $html .= "<td>%OK R/T </td>";
    $html .= "<td>%KO R/T</td>";
    $html .= "<td>%BKL R/T</td>";
    $html .= "<td>%BKLP R/T</td>";
    $html .= "<td>%No Post R/T</td>";
    $html .= "<td>%Totale R/T </td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td>" . $okP . "</td>";
    $html .= "<td>" . $koP . "</td>";
    $html .= "<td>" . $bklP . "</td>";
    $html .= "<td>" . $bklpP . "</td>";
    $html .= "<td>" . $vuotoP . "</td>";
    $html .= "<td>" . $totaleP . "</td>";
    $html .= "</tr>";
    
    
    if ($totale <> 0) {
        $okT = ($totaleRange==0?0:round(($okRange / $totaleRange), 2)*100);
        $koT =($totaleRange==0?0: round(($koRange / $totaleRange), 2)*100);
        $bklT = ($totaleRange==0?0:round(($bklRange / $totaleRange), 2)*100);
        $totaleT = ($totaleRange==0?0:round(($totaleRange / $totaleRange), 2)*100);
        $bklpT = ($totaleRange==0?0:round(($bklpRange / $totaleRange), 2)*100);
        $vuotoT =($totaleRange==0?0: round(($vuotoRange / $totaleRange), 2)*100);
    } else {
        $okT = "-";
        $koT = "-";
        $bklT = "-";
        $totaleT = "-";
        $bklpT = "-";
        $vuotoT = "-";
    }
    

    $html .= "<tr style='background-color:yellow'>";
    $html .= "<td>%OK Totale </td>";
    $html .= "<td>%KO Totale</td>";
    $html .= "<td>%BKL Totale</td>";
    $html .= "<td>%BKLP Totale</td>";
    $html .= "<td>%No Post Totale</td>";
    $html .= "<td>% </td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td>" . $okT . "</td>";
    $html .= "<td>" . $koT . "</td>";
    $html .= "<td>" . $bklT . "</td>";
    $html .= "<td>" . $bklpT . "</td>";
    $html .= "<td>" . $vuotoT . "</td>";
    $html .= "<td>" . $totaleT . "</td>";
    $html .= "</tr>";
}

$html .= "</tr></table>";
if ($idMandato == 'Plenitude') {
    include "creaTabellaPdpEsternoPolizzeLead.php";
}

echo $html;

