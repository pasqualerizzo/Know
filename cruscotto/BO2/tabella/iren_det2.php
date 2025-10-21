<?php

//query con tabella dati sede - ragione sociale - commodity - codice campagna  - stato pda - data sottoscrizione

include_once 'conenssioneCrm.php';

$query3= 'SELECT * FROM `vtiger_plenitudecf` WHERE cf_3563 = CURRENT_DATE and cf_3673 = "Acquisito"  limit 10';

$result = $db_instance->query($query3);

    
    $intestazione="SEDE;RAGIONE SOCIALE;COMMODITY;CODICE CAMPAGNA;DATA;STATO PDA\n";

$tabella="<table  border=2>
                <tr><td>SEDE</td><td>RAGIONE SOCIALE</td><td>COMMODITY</td><td>CODICE CAMPAGNA</td><td>DATA SOTTOSCRIZIONE</td><td>STATO PDA</td></tr>";
              
                //fwrite($myfile, $intestazione);      
                // fwrite($myfile, $rigcsv);
             
                

        while($riga=$result->fetch_assoc()){
                $tabella.="<tr><td>".$riga['cf_3569']."</td><td>".$riga['cf_3577']."</td><td>".$riga['cf_3565']."</td><td>".$riga['cf_3571']."</td><td>".$riga['cf_3563']."</td><td>".$riga['cf_3673']."</td></tr>";
               //$rigcsv=$riga['status'].";".$riga['campaign_id'].";".$riga['modify_date'].";".$riga['phone_number'].";".$riga['first_name'].";".$riga['last_name'].";".$riga['address1'].";".$riga['address2'].";".$riga['address3'].";".$riga['email'].";".$riga['comments']."\n";
               
                       //fwrite($myfile, $rigcsv);
         
        }

        $tabella.="</table>";

        print_r($tabella);

