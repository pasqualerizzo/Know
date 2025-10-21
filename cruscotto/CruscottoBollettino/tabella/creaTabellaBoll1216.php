<?php

include_once 'connessioneCrm.php';

$query1= "SELECT 
    p.sede AS Sede,
    '' AS Obj,
    COUNT(CASE WHEN p.metodopagamento = 'Bollettino Postale' THEN 1 END) as Bollettino
FROM `vtiger_plenitude` p
INNER JOIN `vtiger_plenitudecf` cf ON p.plenitudeid = cf.plenitudeid
WHERE DATE(STR_TO_DATE(cf.cf_2162, '%Y-%m-%d %H:%i:%s')) = CURRENT_DATE 
  AND p.statopda IN ('Ok Definitivo')
  AND p.commodity <> 'Polizza'
  AND p.sede <> 'CATANZARO ESSE' 
  AND p.tipoacquisizione IN ('Switch', 'Voltura')
  AND TIME(STR_TO_DATE(cf.cf_2162, '%Y-%m-%d %H:%i:%s')) BETWEEN '12:00:00' AND '15:59:59'
GROUP BY p.sede";

$result= $db_instance->query($query1);

// Calcolo il totale
$totale_actual = 0;
$dati = array();
while($riga = $result->fetch_assoc()) {
    $dati[] = $riga;
    $totale_actual += $riga['Bollettino'];
}

$tabella="<table border='3' style='width: 100%; font-size: 36px;'>
                <tr><td colspan='3' style='background-color: orange; color: white; width: 100%; height: 100%; font-size: 30px;'>BOLL SW&VOLT 12-16</td></tr>
                <tr><td style='width: 100%; height: 100%; font-size: 30px;'>SEDE</td><td style='width: 100%; height: 100%; font-size: 30px;'>OBJ</td><td style='width: 100%; height: 100%; font-size: 30px;'>ACTUAL</td></tr>";
              
// Stampo le righe dei dati
foreach($dati as $riga) {
    $tabella.="<tr><td style='background-color: yellow; color: black; width: 100%; height: 100%; font-size: 40px;'>".$riga['Sede']."</td><td style='background-color: yellow; color: black; width: 100%; height: 100%; font-size: 40px'>".$riga['Obj']."</td><td style='background-color: yellow; color: black; width: 100%; height: 100%; font-size: 40px'>".$riga['Bollettino']."</td></tr>";
}

// Aggiungo la riga dei totali
$tabella.="<tr><td style='background-color: blue; color: white; width: 100%; height: 100%; font-size: 40px;'>TOTALE</td><td style='background-color: blue; color: white; width: 100%; height: 100%; font-size: 40px'>". - (19 - $totale_actual)."</td><td style='background-color: blue; color: white; width: 100%; height: 100%; font-size: 40px'>".$totale_actual."</td></tr>";

$tabella.="</table>";

print_r($tabella);