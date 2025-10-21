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

$pezzoLordo = 0;
$pezzoOk = 0;

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
include "../../tabella/intestazioneTabellaAvanzamento.php";

foreach ($mandato as $idMandato) {

    switch ($idMandato) {
        case "Plenitude":
            $queryCrmSede = "SELECT
            sede,
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
    sede";
echo $queryCrmSede;
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
    sede";

            break;
        case "Vodafone":
            $queryCrmSede = "SELECT 
        'Lamezia',
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

";
            break;
        case "enel_out":
            $queryCrmSede = "SELECT 
        sede,
        'Enel' AS Enel,
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
FROM 
    enelOut
inner JOIN aggiuntaEnel on enelOut.id=aggiuntaEnel.id 
where data<='$dataMaggiore' and data>='$dataMinore'
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
               REPLACE('know.union', 'know.', '')  AS mandato,
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
FROM 
    know.union
inner JOIN aggiuntaUnion on know.union.id=aggiuntaUnion.id 
where data<='$dataMaggiore' and data>='$dataMinore'
    AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
GROUP BY 
    sede";
            break;
        
        
              case "Heracom":
            $queryCrmSede = "SELECT 
               sede,
               'Heracom' as heracom,
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
FROM 
    heracom
inner JOIN aggiuntaHeracom on heracom.id=aggiuntaHeracom    .id 
where data<='$dataMaggiore' and data>='$dataMinore'
    AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
GROUP BY 
    sede";
            break;
        
        
              case "EnelIn":
            $queryCrmSede = "SELECT 
               sede,
               'EnelIn' as EnelIn,
    SUM(pezzoLordo) AS Prodotto,
    SUM(CASE WHEN fasePDA = 'OK' THEN pezzoLordo ELSE 0 END) AS Inserito
FROM 
    enelIn
NNER JOIN aggiuntaEnelIn ON enelIn.id = aggiuntaEnelIn.id 
where data<='$dataMaggiore' and data>='$dataMinore'
    AND statoPda NOT IN ('bozza', 'annullata', 'pratica doppia', 'In attesa Sblocco')
GROUP BY 
    sede";
            break;
        
        
        
        
    }

    $pezzoLordoMandato = 0;
    $pezzoOkMandato = 0;
    $oreMandato = 0;


    $risultatoCrmSede = $conn19->query($queryCrmSede);

        while ($rigaCRM = $risultatoCrmSede->fetch_array()) {
        $sede = $rigaCRM[0];
        $sedeRicerca = ucwords($sede);
        $pezzoLordo = round($rigaCRM[2], 0);
        $pezzoOk = round($rigaCRM[3], 0);
        
        $caduta = ($pezzoLordo == 0) ? '0.00%' : number_format((($pezzoLordo - $pezzoOk) / $pezzoLordo) * 100, 2) . '%';
//        $resa = ($ore == 0) ? 0 : round($pezzoOk / $ore, 2);
//        $resalordo = ($ore == 0) ? 0 : round($pezzoLordo / $ore, 2);

        $queryGroupMandato = "SELECT sum(numero)/3600 as ore "
                . "FROM `stringheTotale`  "
                . "where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello<=6  "
                . " and sede='$sede'  and idMandato='$idMandato'  "
                . "group by sede";
        //echo $queryGroupMandato;
        $risultaOre = $conn19->query($queryGroupMandato);
        if (($risultaOre->num_rows) > 0) {
            $rigaOre = $risultaOre->fetch_array();
            $ore = $rigaOre[0];
        } else {
            $ore = 0;
        }
        
        
     $pezzoLordoMandato += $pezzoLordo;
        $pezzoOkMandato += $pezzoOk;
        $oreMandato += round($ore, 2);
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
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordo</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$resalordo</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOk</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$resa</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>$caduta</td>";
        $html .= "<td style='border-left: 2px solid lightslategray'>" . round($ore, 2) . "</td>";
        $html .= "</tr>";

//

        $sedePrecedente = $sede;

        $oreSede += $ore;
    }

 if ($pezzoLordo > 0) {
    $pezzoLordoTotale += $pezzoLordo;
}

if ($pezzoOk > 0) {
    $pezzoOkTotale += $pezzoOk;
}

if ($pezzoKoSede > 0) {
    $pezzoKoTotale += $pezzoKoSede;
}

if ($pezzoBklSede > 0) {
    $pezzoBklTotale += $pezzoBklSede;
}

if ($pezzoBklpSede > 0) {
    $pezzoBklpTotale += $pezzoBklpSede;
}

if ($oreSede > 0) {
    $oreTotale += round($oreSede, 2);
}
//    $pezzoPostOkTotale += $pezzoPostOkSede;

    
    
    
$pezzoLordoTotale += $pezzoLordoSede;
$pezzoOkTotale += $pezzoOkSede;
if ($pezzoLordoTotale > 0) {
    $cadutaTotale = number_format((($pezzoLordoTotale - $pezzoOkTotale) / $pezzoLordoTotale) * 100, 2) . '%';
} else {
    $cadutaTotale = '0.00%'; // Valore predefinito quando pezzoLordoTotale è zero
}

// Aggiunta oreSedeParziale solo se maggiore di zero
if ($oreSedeParziale > 0) {
    $oreTotale += $oreSedeParziale;
}

// Calcolo caduta mandato, verifica che $pezzoLordoMandato sia maggiore di zero
if ($pezzoLordoMandato > 0) {
    $cadutaMandato = number_format((($pezzoLordoMandato - $pezzoOkMandato) / $pezzoLordoMandato) * 100, 2) . '%';
} else {
    $cadutaMandato = '0.00%'; // Valore predefinito quando pezzoLordoMandato è zero
}
    $resaMandato = ($oreMandato == 0) ? 0 : round($pezzoOkMandato / ($oreMandato ), 2);
    $resalordoMandato = ($oreMandato == 0) ? 0 : round($pezzoLordoMandato / ($oreMandato ), 2);
    // Creazione della riga dei totali per il mandato
    $html .= "<tr style='background-color: orange; font-weight: bold;'>";
    $html .= "<td colspan='2'>Totali $idMandato</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoLordoMandato</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$resalordoMandato</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$pezzoOkMandato</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$resaMandato</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>$cadutaMandato</td>";
    $html .= "<td style='border-left: 2px solid lightslategray'>" . round($oreMandato , 2) . "</td>";
    $html .= "</tr>";

    // Azzero i dati per il mandato successivo
    $pezzoLordoMandato = 0;
    $pezzoOkMandato = 0;
    $oreMandato = 0;
}

echo $html;
