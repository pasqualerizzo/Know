<?php

include_once 'connessioneCrm.php';

$query1= "SELECT 
    sede AS Sede,
    '' AS Obj,
    COUNT(CASE WHEN metodopagamento = 'RID' THEN 1 END) as RID
FROM `vtiger_plenitude`  
WHERE datasottoscrizionecontratto = CURRENT_DATE 
  AND statopda IN ('Ok Firma',  'Ok Definitivo')
  AND commodity <> 'Polizza'
  AND sede <> 'CATANZARO ESSE' 
  AND tipoacquisizione IN ('Switch', 'Voltura')
  AND TIME(STR_TO_DATE(orariofirma, '%Y-%m-%d %H:%i:%s')) BETWEEN '09:00:00' AND '12:59:59'
GROUP BY sede";

$result= $db_instance->query($query1);

// Calcolo il totale
$totale_actual = 0;
$dati = array();
while($riga = $result->fetch_assoc()) {
    $dati[] = $riga;
    $totale_actual += $riga['RID'];
}

$tabella="<table border='3' style='width: 100%; font-size: 36px;'>
                <tr><td colspan='3' style='background-color: purple; color: white; width: 100%; height: 100%; font-size: 30px;'>RID SW&VOLT 9-13</td></tr>
                <tr><td style='width: 100%; height: 100%; font-size: 30px;'>SEDE</td><td style='width: 100%; height: 100%; font-size: 30px;'>OBJ</td><td style='width: 100%; height: 100%; font-size: 30px;'>ACTUAL</td></tr>";
              
// Stampo le righe dei dati
foreach($dati as $riga) {
    $tabella.="<tr><td style='background-color: green; color: white; width: 100%; height: 100%; font-size: 40px;'>".$riga['Sede']."</td><td style='background-color: green; color: white; width: 100%; height: 100%; font-size: 40px'>".$riga['Obj']."</td><td style='background-color: green; color: white; width: 100%; height: 100%; font-size: 40px'>".$riga['RID']."</td></tr>";
}

// Aggiungo la riga dei totali
$tabella.="<tr><td style='background-color: blue; color: white; width: 100%; height: 100%; font-size: 40px;'>TOTALE</td><td style='background-color: blue; color: white; width: 100%; height: 100%; font-size: 40px'>". - (39 - $totale_actual)."</td><td style='background-color: blue; color: white; width: 100%; height: 100%; font-size: 40px'>".$totale_actual."</td></tr>";

$tabella.="</table>";

print_r($tabella);