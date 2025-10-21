<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessionePonte.php";
$obj19 = new connessionePonte();
$conn19 = $obj19->apriConnessionePonte();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");


$dataMinoreIta = date('d-m-Y ', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$dataMinoreRicerca = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggioreRicerca = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$mese = date('Y-m-01', strtotime($dataMaggiore));

$html = "<table class='blueTable' style='width: 40%'>";
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th colspan='3'>Dettaglio invio Messaggi</th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>Sede</th>";
$html .= "<th>Prodotto</th>";
$html .= "<th>Conteggio</th>";

$html .= "</tr>";
$html .= "</thead>";

$queryCrm = "SELECT substr(brand,length(brand)-3,4) as sede,substr(brand,1,length(brand)-5) as prodotto,COUNT(substr(brand,1,length(brand)-5)) FROM `idSmsMusa` where brand<>'vuoto' and data>='$dataMinoreRicerca' and data<='$dataMaggioreRicerca' group by sede,prodotto";


$risultatoCrm = $conn19->query($queryCrm);
while ($rigaCRM = $risultatoCrm->fetch_array()) {
    switch($rigaCRM[0]){
        case "6099":
            $sede="BelForte";
            $colore="orange";
            break;
        case "6100":
            $sede="Città Fiera";
            $colore="Yellow";
            break;
        case "7587":
            $sede="Silea Mare";
            $colore="Tomato";
            break;
    }
$html .= "<tr style='background-color:$colore'>";
$html .= "<td>" . $sede . "</td>";
$html .= "<td>" . $rigaCRM[1] . "</td>";
$html .= "<td>" . $rigaCRM[2] . "</td>";

$html .= "</tr>";
}
$queryCrmTotale = "SELECT substr(brand,length(brand)-3,4) as sede,substr(brand,1,length(brand)-5) as prodotto,COUNT(substr(brand,1,length(brand)-5)) FROM `idSmsMusa` where brand<>'vuoto' and data>='$dataMinoreRicerca' and data<='$dataMaggioreRicerca' group by sede";
$risultatoCrmTotale = $conn19->query($queryCrmTotale);
while ($rigaCRM = $risultatoCrmTotale->fetch_array()) {
    switch($rigaCRM[0]){
        case "6099":
            $sede="BelForte";
            break;
        case "6100":
            $sede="Città Fiera";
            break;
        case "7587":
            $sede="Silea Mare";
            break;
    }

$html .= "<tr style='background-color:Gold'>";
$html .= "<td>" . $sede . "</td>";
$html .= "<td>" . "Totale" . "</td>";
$html .= "<td>" . $rigaCRM[2] . "</td>";

$html .= "</tr>";
}
//$html .= "<tr>";
//$html .= "<td colspan='3' style='background-color:yellow'>Totale</td>";
//$html .= "<td  style='background-color:yellow'>" . $totaleGiorni . "</td>";
//$html .= "<td  style='background-color:yellow'>" . $totaleMese . "</td>";
//$html .= "<td  style='background-color:yellow'>" . round((($totaleGiorni / $totaleMese) * 100), 2) . "</td>";
//
//$html .= "</tr>";
//
//$html .= "<tr>";
//$html .= "<td colspan='6' style='background-color:mediumseagreen'></td>";
//$html .= "</tr>";
//
//
//$html .= "</tr></table><br>";

echo $html;

