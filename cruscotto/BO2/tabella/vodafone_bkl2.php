<?php

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani

include_once 'connessioni.php';


$query1= 'SELECT count(vodafoneid) as AVANZAMENTO, cf_3146 AS STATO_PDA FROM `vtiger_vodafonecf`  WHERE cf_2986 = CURRENT_DATE and (cf_3146 = "OK FIRMA" or cf_3146 = "RECUPERO DATI" or cf_3146 = "OK CONTROLLO DATI") GROUP BY cf_3146 limit 1000';

//$query2= 'SELECT * FROM `vtiger_plenitudecf` WHERE cf_3563 = CURRENT_DATE and cf_3673 = "Ok Firma"';




$result= $db_instance->query($query1);



//echo $query1;



$tabella="<table  border=2>
                <tr><td colspan='2' style='background-color: red; color: white; width: 100%; height:100%; font-size: 30px'>MANDATO VODAFONE BACKLOG</td></tr>
                <tr><td style='width: 100%;height:100%; font-size: 30px'>STATO PDA</td><td style='width: 100%;height:100%; font-size: 30px'>NUMERO</td></tr>";
              
                //fwrite($myfile, $intestazione);      
                // fwrite($myfile, $rigcsv);
             
                

        while($riga=$result->fetch_assoc()){
                $tabella.="<tr><td style='background-color: green; color: white; width: 100%; height:100%; font-size: 40px'>".$riga['STATO_PDA']."</td><td style='background-color: green; color: white; width: 100%; height:100%; font-size: 40px'>".$riga['AVANZAMENTO']."</td></tr>";
               //$rigcsv=$riga['status'].";".$riga['campaign_id'].";".$riga['modify_date'].";".$riga['phone_number'].";".$riga['first_name'].";".$riga['last_name'].";".$riga['address1'].";".$riga['address2'].";".$riga['address3'].";".$riga['email'].";".$riga['comments']."\n";
               
                       //fwrite($myfile, $rigcsv);
         
        }

        $tabella.="</table>";

        print_r($tabella);

       