<?php

$totaleGiorniPolizza = 0;
$totaleMesePolizza = 0;
//
//if ($lunghezzaSede == 1) {
//    $querySede .= " AND sede='$sede[0]' ";
//} else {
//    for ($l = 0; $l < $lunghezzaSede; $l++) {
//        if ($l == 0) {
//            $querySede .= " AND ( ";
//        }
//        $querySede .= " sede='$sede[$l]' ";
//        if ($l == ($lunghezzaSede - 1)) {
//            $querySede .= " ) ";
//        } else {
//            $querySede .= " OR ";
//        }
//    }
//}

$html .= "<table class='blueTable' style='width: 40%'>";
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th colspan='6'>DETTAGLIO PDP INTERNO Polizze</th>";
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
                    . "where mese='$mese' and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' and comodity='Polizza' and tipoCampagna = 'Lead'"
                    . $querySede
                    . " group by statoPda";

            break;

    }

    $risultatoCrm = $conn19->query($queryCrm);
    while ($rigaCRM = $risultatoCrm->fetch_array()) {
        $html .= "<tr>";
        $html .= "<td>" . $idMandato . "</td>";
        $html .= "<td>" . $rigaCRM[0] . "</td>";
        $html .= "<td>" . $rigaCRM[1] . "</td>";
        $html .= "<td>" . $rigaCRM[3] . "</td>";
        $totaleGiorniPolizza += $rigaCRM[3];
        $html .= "<td>" . $rigaCRM[2] . "</td>";
        $totaleMesePolizza += $rigaCRM[2];
        $html .= "<td>" . round((($rigaCRM[3] / $rigaCRM[2]) * 100), 2) . "</td>";
        $html .= "</tr>";
    }
    $html .= "<tr>";
    $html .= "<td colspan='3' style='background-color:yellow'>Totale</td>";
    $html .= "<td  style='background-color:yellow'>" . $totaleGiorniPolizza . "</td>";
    $html .= "<td  style='background-color:yellow'>" . $totaleMesePolizza . "</td>";
    $c1=($totaleMesePolizza==0?0:round((($totaleGiorniPolizza / $totaleMesePolizza) * 100), 2));
    $html .= "<td  style='background-color:yellow'>" . $c1 . "</td>";

    $html .= "</tr>";
     $html .= "<tr>";
    $html .= "<td colspan='6' style='background-color:mediumseagreen'></td>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<td colspan='3' style='background-color:yellow'>Rapporto Totale/Polizze</td>";
    $c2=($totaleGiorni==0?0:round($totaleGiorniPolizza/$totaleGiorni,2));
    $c3=($totaleMese==0?0:round($totaleMesePolizza/$totaleMese,2));
    $c4=($totaleMesePolizza==0?0:round((($totaleGiorniPolizza / $totaleMesePolizza) * 100), 2));
    
    
    $html .= "<td  style='background-color:yellow'>" . $c2 . "</td>";
    $html .= "<td  style='background-color:yellow'>" . $c3 . "</td>";
    $html .= "<td  style='background-color:yellow'>" . $c4 . "</td>";

    $html .= "</tr>";

    $html .= "<tr>";
    $html .= "<td colspan='6' style='background-color:mediumseagreen'></td>";
    $html .= "</tr>";
}

$html .= "</tr></table>";
//echo $html;

