<?php

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani

include_once 'conenssioneCrm.php';


$query1= "SELECT 
    COUNT(plenitudeid) AS AVANZAMENTO,
    statopda AS STATO_PDA
FROM vtiger_plenitude
WHERE 
    datasottoscrizionecontratto = CURRENT_DATE
    AND commodity <> 'Polizza'
    AND (
        (statopda = 'Ok firma' AND codicecampagna <> 'SPN_LEAD')
        OR 
        (statopda = 'Ok Definitivo' AND codicecampagna = 'SPN_LEAD')
    )
GROUP BY statopda
LIMIT 1000";

//$query2= 'SELECT count(plenitudeid) as AVANZAMENTO, cf_3673 AS STATO_PDA FROM `vtiger_plenitudecf`  WHERE cf_3563 = CURRENT_DATE and (CF_3565 = "Polizza" and cf_3673 = "Ok firma") GROUP BY cf_3673  limit 1000';




$result= $db_instance->query($query1);



//echo $query1;



$tabella="<table  border=1>
                <tr><td colspan='2' style='background-color: purple; color: white; width: 100%; font-size: 30px'>MANDATO PLENITUDE FIRMATI</td></tr>
                <tr><td style='width: 100%; font-size: 30px'>STATO PDA</td><td style='width: 100%; font-size: 30px'>NUMERO</td></tr>";
              
                //fwrite($myfile, $intestazione);      
                // fwrite($myfile, $rigcsv);
             
                

        while($riga=$result->fetch_assoc()){
                $tabella.="<tr><td style='width: 100%; font-size: 45px'>".$riga['STATO_PDA']."</td><td style='width: 100%; font-size: 45px'>".$riga['AVANZAMENTO']."</td></tr>";
               //$rigcsv=$riga['status'].";".$riga['campaign_id'].";".$riga['modify_date'].";".$riga['phone_number'].";".$riga['first_name'].";".$riga['last_name'].";".$riga['address1'].";".$riga['address2'].";".$riga['address3'].";".$riga['email'].";".$riga['comments']."\n";
               
                       //fwrite($myfile, $rigcsv);
         
        }

        $tabella.="</table>";

        print_r($tabella);

        