<?php

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani

include_once 'conenssioneCrm.php';


$query1= 'SELECT count(vivigasid) as AVANZAMENTO, statopda AS STATO_PDA FROM `vtiger_vivigas`  WHERE datacontratto = CURRENT_DATE and (statopda = "COMPLETATO FIRMA" OR statopda = "Ok Definitivo" ) GROUP BY statopda limit 1000';

//$query2= 'SELECT * FROM `vtiger_plenitudecf` WHERE cf_3563 = CURRENT_DATE and cf_3673 = "Ok Firma"';




$result= $db_instance->query($query1);



//echo $query1;



$tabella="<table  border=2>
                <tr><td colspan='2' style='background-color: yellow; color: black; wight:100%; height:100%; font-size: 30px'>MANDATO VIVIGAS INSERITI</td></tr>
                <tr><td style='width: 100%; height:100%; font-size: 20px'>STATO PDA</td><td style='width: 100%; height:100%; font-size: 20px'>NUMERO</td></tr>";
              
                //fwrite($myfile, $intestazione);      
                // fwrite($myfile, $rigcsv);
             
                

        while($riga=$result->fetch_assoc()){
                $tabella.="<tr><td style='width: 100%; height:100%; font-size: 30px'>".$riga['STATO_PDA']."</td><td style='width: 100%; height:100%; font-size: 30px'>".$riga['AVANZAMENTO']."</td></tr>";
               //$rigcsv=$riga['status'].";".$riga['campaign_id'].";".$riga['modify_date'].";".$riga['phone_number'].";".$riga['first_name'].";".$riga['last_name'].";".$riga['address1'].";".$riga['address2'].";".$riga['address3'].";".$riga['email'].";".$riga['comments']."\n";
               
                       //fwrite($myfile, $rigcsv);
         
        }

        $tabella.="</table>";

        print_r($tabella);


