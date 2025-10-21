<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$mese = filter_input(INPUT_POST, 'mese', FILTER_SANITIZE_STRING);

$dataMinore = date("Y-m-01", strtotime($mese));
$dataMinoreIta = date("d-m-Y", strtotime($dataMinore));

$dataMaggiore = date("Y-m-t", strtotime($mese));
$dataMaggioreIta = date("d-m-Y", strtotime($dataMaggiore));

$html = "<table class='blueTable'>";
$html .= "<caption><strong>Scoring Totale</strong></caption>";
/**
 * Inserimento dell'intestazione della tabella
 */
include "../intestazioneTabella/intestazioneTabellaCruscotto.php";

$queryCampagnaMarketing = "SELECT nomeCampagna,pezzi,costo FROM `campagnaMarketing` WHERE dataInserimento BETWEEN '$dataMinore' and '$dataMaggiore' group by nomeCampagna";

$queryUtm = "SELECT count(*) as pezzi,sum(pleniTot+viviTot+irenTot+uniTot+enelTot+timTot) as 'contratti totale', sum(pleniOk+viviOk+irenOk+uniOk+enelOk+timok) as 'contratti ok',sum(pleniKo+viviKo+irenOk+uniKo+enelKo+timKo) as 'contratti ko'"
        . " ,sum(valoreMediaPleni+valoreMediaVivi+valoreMedioIren+valoreMedioUni+valoreMedioEnel+valoreMedioTim) as 'valore medio' "
        . " FROM `gestioneLead` "
        . " where dataImport BETWEEN '$dataMinore' and '$dataMaggiore' and utmCampagna='CTC_WA_Risparmiami' GROUP BY utmCampagna";

$risultatoUtm = $conn19->query($queryUtm);
if ($risultatoUtm->num_rows == 0) {
    $pezziRicevuti = 0;
    $contrattiTotali = 0;
    $contrattiOk = 0;
    $contrattiKo = 0;
    $valoreMedio=0;
} else {
    $rigaUtm = $risultatoUtm->fetch_array();
    $pezziRicevuti = $rigaUtm["pezzi"];
    $contrattiTotali = $rigaUtm["contratti totale"];
    $contrattiOk = $rigaUtm["contratti ok"];
    $contrattiKo = $rigaUtm["contratti ko"];
    $valoreMedio = $rigaUtm["valore medio"];
}

$risultato = $conn19->query($queryCampagnaMarketing);

if ($risultato->num_rows == 0) {
    
} else {
    while ($riga = $risultato->fetch_array()) {
        $pezziInviati=$riga['pezzi'];
        $costo=$riga['costo'];
        $costo1=0;
        $cpl = ($pezziInviati == 0) ? 0 : round(round($costo / $pezziInviati, 2), 2);
    $cpa = ($pezziRicevuti == 0) ? 0 : round(round($costo / $pezziRicevuti, 2), 2);
    $cpc = ($contrattiOk == 0) ? 0 : round(round($costo / $contrattiOk, 2), 2);
    $roas = ($costo == 0) ? 0 : round($valoreMedio / $costo, 2);
    $roas2 = ($costo + $costo1 == 0) ? 0 : round($valoreMedio / ($costo + $costo1), 2);
        
        
        $html .= "<tr>";
        $html .= "<td>" . $riga['nomeCampagna'] . "</td>";

        $html .= "<td style='border-left: 5px double'>" .$pezziInviati . "</td>";
        $html .= "<td>$pezziRicevuti</td>";

        $html .= "<td style='border-left: 5px double'>Ore</td>";
        $html .= "<td>" . $costo . " </td>";
        $html .= "<td>Bugdet </td>";
        $html .= "<td>" . round($costo / $pezziInviati, 2) . "</td>";
        $html .= "<td>R1 </td>";

        $html .= "<td style='border-left: 5px double'>BackLog</td>";
        $html .= "<td>Non utile </td>";
        $html .= "<td>Utile</td>";
        $html .= "<td>DNC</td>";

        $html .= "<td style='border-left: 5px double'>$contrattiTotali</td>";
        $html .= "<td>$contrattiOk</td>";
        $html .= "<td>$contrattiKo</td>";

        $html .= "<td style='border-left: 5px double'>".round($pezziRicevuti/$pezziInviati,2)."</td>";
        $html .= "<td>".round($contrattiTotali/$pezziInviati,2)."</td>";
        $html .= "<td>".round($contrattiOk/$pezziInviati,2)."</td>";
        $html .= "<td>".round($valoreMedio/$pezziRicevuti,2)."</td>";

        $html .= "<td style='border-left: 5px double'>$cpl</td>";

        $html .= "<td>$cpc</td>";
        $html .= "<td>$roas</td>";
        $html .= "<td>$roas2</td>";
        $html .= "</tr>";
    }
}



$html .= "</table>";

$html .= "<br>";

echo $html;

