<?php

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani
//
//include_once 'connessioni.php';

include_once 'conenssioneCrm.php';

$query1= 'SELECT count(plenitudeid) as AVANZAMENTO, statopda AS STATO_PDA 
FROM `vtiger_plenitude`  
WHERE datasottoscrizionecontratto = CURRENT_DATE 
  AND (
        (statopda = "Trasferito BO"  OR statopda = "Recuperato Dati" OR statopda = "Recupero Recall") 
        OR 
        (
            (statopda = "Acquisito" OR statopda = "da firmare" OR statopda = "Ok Controllo dati") 
            AND codicecampagna <> "SPN_LEAD"
        )
      )
  AND commodity <> "Polizza" 
GROUP BY statopda 
LIMIT 10000';


//echo $query1;
//$query2= 'SELECT * FROM `vtiger_plenitudecf` WHERE cf_3563 = CURRENT_DATE and cf_3673 = "Ok Firma"';




$result= $db_instance->query($query1);



//echo $query1;

$tabella="<table border='2' style='width: 100%; font-size: 36px;'>
                <tr><td colspan='2' style='background-color: purple; color: white; width: 100%; height: 100%; font-size: 30px;'>MANDATO PLENITUDE BACKLOG</td></tr>
                <tr><td style='width: 100%; height: 100%; font-size: 30px;'>STATO PDA</td><td style='width: 100%; height: 100%; font-size: 30px;'>NUMERO</td></tr>";
              
while($riga = $result->fetch_assoc()) {
    $tabella.="<tr><td style='background-color: green; color: white; width: 100%; height: 100%; font-size: 40px;'>".$riga['STATO_PDA']."</td><td style='background-color: green; color: white; width: 100%; height: 100%; font-size: 40px'>".$riga['AVANZAMENTO']."</td></tr>";
}

$tabella.="</table>";

print_r($tabella);



