<?php

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani

include_once 'conenssioneCrm.php';



$query1= 'SELECT count(vivigasid) as AVANZAMENTO, statopda AS STATO_PDA FROM `vtiger_vivigas`  WHERE datacontratto = CURRENT_DATE and (statopda = "Acquisito" or statopda = "OK VOCAL LIGHT" or statopda = "OK OTP LUCE" or statopda = "OK OTP GAS"  or statopda = "In attesa QC" or statopda = "Recuperato Vocal" or statopda = "IN ATTESA IDENTIFICAZIONE" or statopda = "Ok Vocal" or statopda = "Recuperato Dati" or statopda = "Invio Prima Mail")  GROUP BY statopda limit 1000';

//echo $query1;
//$query2= 'SELECT * FROM `vtiger_plenitudecf` WHERE cf_3563 = CURRENT_DATE and cf_3673 = "Ok Firma"';




$result= $db_instance->query($query1);





$tabella="<table  border=2>
                <tr><td colspan='2' style='background-color: yellow; color: black; width: 90%; height:90%; font-size: 25px'>MANDATO VIVIGAS BACKLOG</td></tr>
                <tr><td style='width: 90%;height:90%; font-size: 30px'>STATO PDA</td><td style='width: 90%;height:90%; font-size: 30px'>NUMERO</td></tr>";
              
                //fwrite($myfile, $intestazione);      
                // fwrite($myfile, $rigcsv);
             
                

        while($riga=$result->fetch_assoc()){
                $tabella.="<tr><td style='background-color: green; color: white;  width: 90%; height:100%; font-size: 30px'>".$riga['STATO_PDA']."</td><td style='background-color: green; color: white;  width: 90%; height:100%; font-size: 30px'>".$riga['AVANZAMENTO']."</td></tr>";
               //$rigcsv=$riga['status'].";".$riga['campaign_id'].";".$riga['modify_date'].";".$riga['phone_number'].";".$riga['first_name'].";".$riga['last_name'].";".$riga['address1'].";".$riga['address2'].";".$riga['address3'].";".$riga['email'].";".$riga['comments']."\n";
               
                       //fwrite($myfile, $rigcsv);
         
        }

        $tabella.="</table>";

        print_r($tabella);


