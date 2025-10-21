<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Auto-rileva ambiente (locale MAMP o produzione)
$BASE_PATH = (strpos($_SERVER['DOCUMENT_ROOT'], 'MAMP') !== false) 
    ? '/Applications/MAMP/htdocs/Know' 
    : '/var/www/html/Know';

require $BASE_PATH . "/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$mese = filter_input(INPUT_POST, "mese");

//$mese="2024-07";
$dataMinore = $mese . "-01<br>";
$dataMaggiore = date('Y-m-d', strtotime("last day of " . $mese));

//$testMode = $_POST["testMode"];
//echo $testMode;
//if ($testMode=="true") {
//    echo "si";
//}
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);

$pezzoLordoSede = 0;
$pezzoOkSede = 0;
$pezzoKoSede = 0;
$pezzoBklSede = 0;
$pezzoBklpSede = 0;
$oreSede = 0;
$pezzoPostOkSede = 0;
$pezzoPostKoSede = 0;
$pezzoPostBklSede = 0;
$pezzoBollettinoSede = 0;
$pezzoRidSede = 0;
$pezzoCartaceoSede = 0;
$pezzoMailSede = 0;
$pezzoLuceSede = 0;
$pezzoGasSede = 0;
$pezzoDualSede = 0;
$pezzoPolizzaSede = 0;
$oreSedeParziale = 0;

$pezzoBollettinoOKSede = 0;
$pezzoRidOKSede = 0;
$pezzoCartaceoOKSede = 0;
$pezzoMailOKSede = 0;

$pezzoLordoTotale = 0;
$pezzoOkTotale = 0;
$pezzoKoTotale = 0;
$pezzoBklTotale = 0;
$pezzoBklpTotale = 0;
$oreTotale = 0;
$pezzoPostOkTotale = 0;
$pezzoPostKoTotale = 0;
$pezzoPostBklTotale = 0;
$pezzoBollettinoTotale = 0;
$pezzoRidTotale = 0;
$pezzoCartaceoTotale = 0;
$pezzoMailTotale = 0;
$pezzoLuceTotale = 0;
$pezzoGasTotale = 0;
$pezzoDualTotale = 0;
$pezzoPolizzaTotale = 0;

$pezzoBollettinoOKTotale = 0;
$pezzoRidOKTotale = 0;
$pezzoCartaceoOKTotale = 0;
$pezzoMailOKTotale = 0;

$sedePrecedente = "";

$querySede = "";
$lunghezzaSede = count($sede);

if ($lunghezzaSede == 1) {
    $querySede .= " AND sede='$sede[0]' ";
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
include "../../tabella/intestazioneTabellaAvanzamento.php";

foreach ($mandato as $idMandato) {

    switch ($idMandato) {
        case "Plenitude":
            $queryCrmSede = "SELECT
       'Plenitude' AS plenitude,
    
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito,
    SUM(CASE WHEN fasePDA = 'KO' THEN pezzoLordo ELSE 0 END) AS KO,
    SUM(CASE WHEN fasePDA = 'BKL' THEN pezzoLordo ELSE 0 END) AS Backlog
FROM 
    `plenitude`
INNER JOIN 
    aggiuntaPlenitude 
    ON plenitude.id = aggiuntaPlenitude.id 
WHERE 
data<='$dataMaggiore' and data>='$dataMinore'   
    AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
    AND comodity <> 'Polizza'
GROUP BY 
    mandato";

            break;
        case "Green Network":
            $queryCrmSede = "SELECT 
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
    mandato";
            break;
        case "Vivigas Energia":
            $queryCrmSede = "SELECT 
            'Vivigas' AS vivigas,
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
FROM 
    `vivigas`
inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id
where data<='$dataMaggiore' and data>='$dataMinore'
    AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
    AND comodity <> 'Polizza'
GROUP BY 
    mandato";

            break;
        case "Vodafone":
            $queryCrmSede = "SELECT 
             'Vodafone' AS vodafone,
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
FROM 
    vodafone
INNER JOIN 
    aggiuntaVodafone 
    ON vodafone.id = aggiuntaVodafone.id 
where `dataVendita`<='$dataMaggiore' and `dataVendita`>='$dataMinore'  
    AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia')
GROUP BY 
    `cbVdf`
";
            break;
        case "enel_out":
            $queryCrmSede = "SELECT 
             'Enel' AS Enel,
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
FROM 
    enelOut
inner JOIN aggiuntaEnel on enel.id=aggiuntaEnel.id 
where data<='$dataMaggiore' and data>='$dataMinore'
    AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
    AND comodity <> 'Fibra'
GROUP BY 
    mandato";
            break;
        case "Iren":
                $queryCrmSede = "SELECT 
                  'iren' AS iren,
        SUM(pezzoLordo) AS Prodotto,
        SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
    FROM 
        `iren`
    inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id
    where data<='$dataMaggiore' and data>='$dataMinore'
        AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
        AND comodity <> 'Polizza'
    GROUP BY 
        mandato ";
            break;

        case "Union":
            $queryCrmSede = "SELECT 
               REPLACE('know.union', 'know.', '')  AS mandato,
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
FROM 
    know.union
inner JOIN aggiuntaUnion on know.union.id=aggiuntaUnion.id 
where data<='$dataMaggiore' and data>='$dataMinore'
    AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
GROUP BY 
    mandato";
            break;
    }
    $risultatoCrmSede = $conn19->query($queryCrmSede);

    while ($rigaCRM = $risultatoCrmSede->fetch_array()) {

        $sede = $rigaCRM[0];
        $sedeRicerca = ucwords($sede);
        $descrizioneMandato = $rigaCRM[1];

        $queryGroupMandato = "SELECT sum(numero)/3600 as ore "
                . "FROM `stringheTotale`  "
                . "where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello<=6  "
                . "  and idMandato='$idMandato'  "
               ;
        //echo $queryGroupMandato;
        $risultaOre = $conn19->query($queryGroupMandato);
        if (($risultaOre->num_rows) > 0) {
            $rigaOre = $risultaOre->fetch_array();
            $ore = $rigaOre[0];
        } else {
            $ore = 0;
        }
        $pezzoLordo = round($rigaCRM[1], 0);
        $pezzoOk = round($rigaCRM[2], 0);
        if ($pezzoLordo == 0) {
    $caduta = '0.00%'; // O qualsiasi valore di default che preferisci
} else {
    $caduta = number_format((($pezzoLordo - $pezzoOk) / $pezzoLordo) * 100, 2) . '%';
}
        $resa = ($ore == 0) ? 0 : round($pezzoOk / $ore, 2);
        $resalordo = ($ore == 0) ? 0 : round($pezzoLordo / $ore, 2);
 

        $html .= "<tr>";
        $html .= "<td >$sede</td>";
        $html .= "<td >$idMandato</td>";

        $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoLordo</td>";

        $html .= "<td style = 'border-left: 2px solid lightslategray'>$resalordo</td>";

        $html .= "<td style = 'border-left: 2px solid lightslategray'>$pezzoOk</td>";

        $html .= "<td style = 'border-left: 2px solid lightslategray'>$resa</td>";

        $html .= "<td style = 'border-left: 2px solid lightslategray'>$caduta</td>";

//  $html .= "<td style = 'border-left: 2px solid lightslategray'></td>";



        $html .= "<td style = 'border-left: 2px solid lightslategray'>" . round($ore, 2) . "</td>";
        $html .= "</tr>";

        $pezzoLordoSede += $pezzoLordo;
        $pezzoOkSede += $pezzoOk;

    $oreSedeParziale += round($ore, 2);

//  
    $oreSede += $ore;
    }
}


$oreTotale += round($oreSede, 2);
//$pezzoPostOkTotale += $pezzoPostOkSede;
// Calcolo dei totali
$pezzoLordoTotale += $pezzoLordoSede;
$pezzoOkTotale += $pezzoOkSede;
 $cadutaTotale = number_format((($pezzoLordoTotale - $pezzoOkTotale) / $pezzoLordoTotale) * 100, 2) . '%';
$oreTotale += $oreSedeParziale;
$resaT = ($oreTotale == 0) ? 0 : round($pezzoOkTotale / ($oreTotale / 2), 2);
$resalordoT = ($oreTotale == 0) ? 0 : round($pezzoLordoTotale / ($oreTotale / 2), 2);
// Creazione della riga dei totali in arancione
$html .= "<tr style='background-color: orange; font-weight: bold;'>";
$html .= "<td colspan='2'>Totali</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordoTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$resalordoT</td>"; // puoi inserire il totale lordo per ora qui se necessario
$html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOkTotale</td>";
$html .= "<td style='border-left: 2px solid lightslategray'>$resaT</td>"; // puoi inserire la resa totale qui se necessario
$html .= "<td style='border-left: 2px solid lightslategray'>$cadutaTotale</td>"; // puoi inserire il totale della caduta qui se necessario
$html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreTotale, 2)/2 . "</td>";
$html .= "</tr>";
//    $html .= "</tr>";
//    
//    
//    </table>";

//if ($idMandato == 'Plenitude') {
//    include "creaTabellaPolizzeInvertito.php";
//}

echo $html;

