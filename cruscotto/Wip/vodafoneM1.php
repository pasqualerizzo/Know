<?php



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani

require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
$obj19 = new ConnessioneCrm();
$connCrm = $obj19->apriConnessioneCrm();



$query1= "SELECT COUNT(vodafoneid) AS PEZZI, cf_3146 AS STATO_PDA FROM `vtiger_vodafonecf` WHERE DATE_FORMAT(cf_2986, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH), '%Y-%m') AND cf_3146 = 'OK DEFINITIVO' GROUP BY cf_3146 LIMIT 1000";

//ECHO $query1;

//$query1= 'SELECT * FROM `vtiger_plenitudecf` WHERE cf_3563 = CURRENT_DATE and cf_3673 = "Ok Firma"';




$result= $connCrm->query($query1);



//echo $query1;



$tabella="<table  border=2>
                <tr><td colspan='2' style='background-color: red; color: white;  width: 100%; height: 100%; font-size: 30px'>PEZZI MESE -1</td></tr>
                <tr><td style='width: 100%; height: 100%; font-size: 30px'>STATO PDA</td><td style='width: 100%; height: 100%; font-size: 30px'>PEZZI</td></tr>";
              
                //fwrite($myfile, $intestazione);      
                // fwrite($myfile, $rigcsv);
             
                

        while($riga=$result->fetch_assoc()){
                $tabella.="<tr><td style='width: 100%; height: 100%; font-size: 30px'>".$riga['STATO_PDA']."</td><td style='width: 100%; height: 100%; font-size: 30px'>".$riga['PEZZI']."</td></tr>";
               //$rigcsv=$riga['status'].";".$riga['campaign_id'].";".$riga['modify_date'].";".$riga['phone_number'].";".$riga['first_name'].";".$riga['last_name'].";".$riga['address1'].";".$riga['address2'].";".$riga['address3'].";".$riga['email'].";".$riga['comments']."\n";
               
                       //fwrite($myfile, $rigcsv);
         
        }

        $tabella.="</table>";

        print_r($tabella);

