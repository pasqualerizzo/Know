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
$totaleGiorni = 0;
$arraySwitchOut = [];
$totaleSwoO = 0;

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
$html .= "<th colspan='3'>Macro Ko Post Vendita</th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>Macro KO</th>";
$html .= "<th>Pezzi</th>";
//$html .= "<th>Mortalità</th>";
$html .= "<th>%</th>";
$html .= "</tr>";
$html .= "</thead>";

foreach ($mandato as $idMandato) {
    if ($idMandato === "Plenitude") {
        $totaleGiorni = 0;

        // Query per ottenere i valori mensili
        $queryCrm = "SELECT "
                . " `codMaticola`,"
                . " count(data) AS totaleGiorni,"
                . " sum(if(dataSwitchOutLuce='0000-00-00',0,1)) as 'SWOLuce', "
                . " sum(if(dataSwitchOutGas='0000-00-00',0,1)) as 'SWOGas' "
                . "FROM `plenitude` "
                //. "INNER JOIN aggiuntaPlenitude ON plenitude.id = aggiuntaPlenitude.id "
                . "WHERE comodity <> 'Polizza' "
                . " and data <= '$dataMaggiore' AND data >= '$dataMinore' "
                . " and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' and statoPda<>'In attesa Sblocco' "
                . "GROUP BY `codMaticola`";
        echo $queryCrm;
        $risultatoCrm = $conn19->query($queryCrm);

        // Query per ottenere il valore di SwitchOut per Luce
//        $queryMortalitaLuce = "SELECT `codMaticola`, SUM(IF(dataSwitchOutLuce BETWEEN '$dataMinore' AND '$dataMaggiore', 1, 0)) AS sommaDataSwitchOutLuce "
//                            . "FROM plenitude "
//                            . "WHERE dataSwitchOutLuce BETWEEN '$dataMinore' AND '$dataMaggiore' "
//                            . "GROUP BY `codMaticola`";
//
//        $risultatoSwitchOut = $conn19->query($queryMortalitaLuce);
        // Riempimento dell'array con i risultati della query Mortalità
//        while ($rigaSwitchOut = $risultatoSwitchOut->fetch_array()) {
//            $arraySwitchOut[$rigaSwitchOut['codMaticola']] = $rigaSwitchOut['sommaDataSwitchOutLuce'];
//        }
        // Itera attraverso i risultati della query CRM
        while ($rigaCRM = $risultatoCrm->fetch_array()) {
            $codMaticola = $rigaCRM['codMaticola'];
            $valore = $rigaCRM['totaleGiorni'];
            $swoLuce = $rigaCRM['SWOLuce'];
            $swoGas = $rigaCRM['SWOGas'];
            $coloreCella = ($valore > 120) ? 'style="background-color:red"' : '';

            $totaleGiorni += $valore;

            // Ottieni il valore di SwitchOut dalla matricola corrispondente
            $valoreSwitchOut = $swoLuce + $swoGas;

            // Calcola la percentuale di mortalità
            $percentualeMortalita = ($valore == 0) ? 0 : round(($valoreSwitchOut / $valore) * 100, 2);

            $totaleSwoO += $valoreSwitchOut;
            $percentualeSwo = ($totaleGiorni == 0) ? 0 : round(($totaleSwoO / $totaleGiorni));
            $html .= "<tr>";
            $html .= "<td>" . $codMaticola . "</td>";
            $html .= "<td $coloreCella>" . $valore . "</td>";
            $html .= "<td>" . $valoreSwitchOut . "</td>";
            $html .= "<td>" . $percentualeMortalita . "%</td>";
            $html .= "</tr>";
        }

        // Aggiungi la riga del totale per il mandato "Plenitude"
        $html .= "<tr>";
        $html .= "<td colspan='1' style='background-color:yellow'>Totale</td>";
        $html .= "<td style='background-color:yellow'>" . $totaleGiorni . "</td>";
        $html .= "<td style='background-color:yellow'>" . $totaleSwoO . "</td>";
        $html .= "<td style='background-color:yellow'>" . $percentualeSwo . "</td>";
        $html .= "</tr>";

        // Riga di separazione
        $html .= "<tr>";
        $html .= "<td colspan='6' style='background-color:mediumseagreen'></td>";
        $html .= "</tr>";
    }
}

$html .= "</table><br>";

echo $html;
