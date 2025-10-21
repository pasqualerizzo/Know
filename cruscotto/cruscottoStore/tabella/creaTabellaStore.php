<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

require "/Applications/MAMP/htdocs/Know/connessione/connessionePonte.php";
$objP = new connessionePonte();
$connP = $objP->apriConnessionePonte();

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");

$dataMinoreRicerca = date('Y-m-d 00:00:00', strtotime($dataMinore));
$dataMaggioreRicerca = date('Y-m-d 23:59:59', strtotime($dataMaggiore));

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$conteggioSmsSede = 0;
$numeroLeadSede = 0;
$backlogSede = 0;
$nonUtiliSede = 0;
$utiliSede = 0;
$dncSede = 0;

$conteggioSmsTotale = 0;
$numeroLeadTotale = 0;
$backlogTotale = 0;
$nonUtiliTotale = 0;
$utiliTotale = 0;
$dncTotale = 0;

$queryStore = "SELECT "
        . " substr(brand,length(brand)-3,4) as sede, "
        . " substr(brand,1,length(brand)-5) as prodotto, "
        . " COUNT(substr(brand,1,length(brand)-5)), "
        . " lead((SUBSTR(brand, LENGTH(brand) -3,4))) over (order by sede,prodotto)"
        . " FROM `idSmsMusa` "
        . " where brand<>'vuoto' and data>='$dataMinoreRicerca' and data<='$dataMaggioreRicerca' "
        . " group by sede,prodotto";
//echo $queryStore;

$html = "<table class='blueTable' style='width: 40%'>";
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th colspan='2'>Dettaglio invio Messaggi</th>";
$html .= "<th colspan='2'>Messaggi</th>";
$html .= "<th colspan='4'>Telefonate</th>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>Sede</th>";
$html .= "<th>Prodotto</th>";
$html .= "<th>SMS inviati</th>";
$html .= "<th>Lead</th>";
$html .= "<th>BackLog</th>";
$html .= "<th>Non Utili</th>";
$html .= "<th>Utili</th>";
$html .= "<th>DNC</th>";
$html .= "</tr>";
$html .= "</thead>";

$risultatoStore = $connP->query($queryStore);

while ($rigaStore = $risultatoStore->fetch_array()) {
    $attuale = $rigaStore[0];

    switch ($attuale) {
        case "6099":
            $sede = "Bel Forte";
            $colore = "orange";
            break;
        case "6100":
            $sede = "Citta Fiera";
            $colore = "Yellow";
            break;
        case "7587":
            $sede = "Silea Mare";
            $colore = "Tomato";
            break;
    }
    $prodotto = $rigaStore[1];
    $conteggioSms = $rigaStore[2];
    $successivo = $rigaStore[3];

    $queryGroupMandatoStore = "SELECT"
            . " agenzia,"
            . " source,"
            . " utmCampagna, "
            . " count(source) as lead, "
            . " sum(IF(pleniTot=0,0,1)) as convertiti,"
            . " SUM(pleniTot) as 'Contratti Prodotti', "
            . " sum(pleniOk) as 'Contratti OK', "
            . " Sum(pleniKo) as 'Contratti KO', "
            . " sum(IF(vodaTot=0,0,1)) as convertiti, "
            . " SUM(vodaTot) as 'Contratti Prodotti', "
            . " sum(vodaOk) as 'Contratti OK', "
            . " Sum(vodaKo) as 'Contratti KO', "
            . " sum(IF(viviTot=0,0,1)) as convertiti, "
            . " SUM(viviTot) as 'Contratti Prodotti', "
            . " sum(viviOk) as 'Contratti OK', "
            . " Sum(viviKo) as 'Contratti KO', "
            . " sum(valoreMediaPleni) as 'VMpleni', "
            . " sum(valoreMediaVivi) as 'VMvivi', "
            . " sum(valoreMedioVoda) as 'VMvoda', "
            . " sum(IF(CategoriaUltima ='BACKLOG',1,0)) as BACKLOG, "
            . " sum(IF(CategoriaUltima ='NONUTILICHIUSI',1,0)) as NONUTILICHIUSI, "
            . " sum(IF(CategoriaUltima ='UTILICHIUSI',1,0)) as UTILICHIUSI , "
            . " sum(IF(CategoriaUltima ='KoDefinitivi',1,0)) as vuoto "
            . " FROM "
            . " `gestioneLead` "
            . " where dataImport<='$dataMaggioreRicerca' and dataImport>='$dataMinoreRicerca'  and utmCampagna='$sede' and brand='$prodotto' "
            . " group by "
            . " agenzia,source,utmCampagna";

  //  echo $queryGroupMandatoStore;
//    echo "<br>";

    $risultatoProdotto = $conn19->query($queryGroupMandatoStore);
    if (($rigaProdotto = $risultatoProdotto->fetch_array())) {
        $numeroLead = $rigaProdotto[3];
        $backlog = $rigaProdotto[19];
        $nonUtili = $rigaProdotto[20];
        $utili = $rigaProdotto[21];
        $dnc = $rigaProdotto[22];
    } else {
        $numeroLead = 0;
        $backlog = 0;
        $nonUtili = 0;
        $utili = 0;
        $dnc = 0;
    }
    $html .= "<tr style='background-color:$colore'>";
    $html .= "<td>" . $sede . "</td>";
    $html .= "<td>" . $prodotto . "</td>";
    $html .= "<td>" . $conteggioSms . "</td>";
    $html .= "<td>" . $numeroLead . "</td>";
    $html .= "<td>" . $backlog . "</td>";
    $html .= "<td>" . $nonUtili . "</td>";
    $html .= "<td>" . $utili . "</td>";
    $html .= "<td>" . $dnc . "</td>";
    $html .= "</tr>";

    if ($attuale == $successivo) {

        $conteggioSmsSede += $conteggioSms;
        $numeroLeadSede += $numeroLead;
        $backlogSede += $backlog;
        $nonUtiliSede += $nonUtili;
        $utiliSede += $utili;
        $dncSede += $dnc;
    } elseif ($successivo == null) {
        $conteggioSmsSede += $conteggioSms;
        $numeroLeadSede += $numeroLead;
        $backlogSede += $backlog;
        $nonUtiliSede += $nonUtili;
        $utiliSede += $utili;
        $dncSede += $dnc;

        $html .= "<tr style='background-color:Khaki'>";
        $html .= "<td>" . $sede . "</td>";
        $html .= "<td>" . "Totale" . "</td>";
        $html .= "<td>" . $conteggioSmsSede . "</td>";
        $html .= "<td>" . $numeroLeadSede . "</td>";
        $html .= "<td>" . $backlogSede . "</td>";
        $html .= "<td>" . $nonUtiliSede . "</td>";
        $html .= "<td>" . $utiliSede . "</td>";
        $html .= "<td>" . $dncSede . "</td>";
        $html .= "</tr>";

        $conteggioSmsTotale += $conteggioSmsSede;
        $numeroLeadTotale += $numeroLeadSede;
        $backlogTotale += $backlogSede;
        $nonUtiliTotale += $nonUtiliSede;
        $utiliTotale += $utiliSede;
        $dncTotale += $dncSede;

        $html .= "<tr style='background-color:DarkKhaki'>";
        $html .= "<td>" . "TOTALE" . "</td>";
        $html .= "<td>" . "" . "</td>";
        $html .= "<td>" . $conteggioSmsTotale . "</td>";
        $html .= "<td>" . $numeroLeadTotale . "</td>";
        $html .= "<td>" . $backlogTotale . "</td>";
        $html .= "<td>" . $nonUtiliTotale . "</td>";
        $html .= "<td>" . $utiliTotale . "</td>";
        $html .= "<td>" . $dncTotale . "</td>";
        $html .= "</tr>";
    } else {
        $conteggioSmsSede += $conteggioSms;
        $numeroLeadSede += $numeroLead;
        $backlogSede += $backlog;
        $nonUtiliSede += $nonUtili;
        $utiliSede += $utili;
        $dncSede += $dnc;

        $html .= "<tr style='background-color:Khaki'>";
        $html .= "<td>" . $sede . "</td>";
        $html .= "<td>" . "Totale" . "</td>";
        $html .= "<td>" . $conteggioSmsSede . "</td>";
        $html .= "<td>" . $numeroLeadSede . "</td>";
        $html .= "<td>" . $backlogSede . "</td>";
        $html .= "<td>" . $nonUtiliSede . "</td>";
        $html .= "<td>" . $utiliSede . "</td>";
        $html .= "<td>" . $dncSede . "</td>";
        $html .= "</tr>";

        $conteggioSmsTotale += $conteggioSmsSede;
        $numeroLeadTotale += $numeroLeadSede;
        $backlogTotale += $backlogSede;
        $nonUtiliTotale += $nonUtiliSede;
        $utiliTotale += $utiliSede;
        $dncTotale += $dncSede;

        $conteggioSmsSede = 0;
        $numeroLeadSede = 0;
        $backlogSede = 0;
        $nonUtiliSede = 0;
        $utiliSede = 0;
        $dncSede = 0;
    }
}






$html .= "</table>";

echo $html;

