<?php

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani

include_once 'connessioni.php';


$db_instance = new mysqli("crm2.novaholding.it", "bruno", "expqfEMlZ1Rzhzx0", "c1vtiger");


$query1= 'SELECT SUM(lavorati) as "somma_lavorati" from ( 
    SELECT count(vodafoneid) as lavorati, cf_3146 AS STATO_PDA FROM `vtiger_vodafonecf`  WHERE cf_2986 = CURRENT_DATE and (cf_3146 = "OK INSERITO" or cf_3146 = "OK DEFINITIVO") GROUP BY cf_3146) AS t';
$result1 = $db_instance->query($query1);
if(($result1->num_rows)>0){
$row1 = $result1->fetch_assoc();
$count1 = $row1['somma_lavorati'];
}else{
    $count1=0;
}
$query2= 'SELECT SUM(in_bkl) as "somma_bkl" from (
    SELECT count(vodafoneid) as in_bkl, cf_3146 AS STATO_PDA FROM `vtiger_vodafonecf`  WHERE cf_2986 = CURRENT_DATE and (cf_3146 = "OK FIRMA" or cf_3146 = "RECUPERO DATI") GROUP BY cf_3146) as t';
$result2= $db_instance->query($query2);
if(($result2->num_rows)>0){
$row2 = $result2->fetch_assoc();
$count2 = $row2['somma_bkl'];
}else{
    $count2=0; 
}


//print_r($query1);
//print_r($query2);

$in_bkl = $count2;
$totale = $count1 + $count2;
$sum = $in_bkl + $totale;

//print_r($in_bkl);
//echo    "<br>"; 
//print_r($totale);

function calculatePercentage($in_bkl, $totale) {

 

    if ($totale != 0) {
        return ($in_bkl * 100) / $totale;
    } else {
        return 0;
    }
}
$percentuale =calculatePercentage($in_bkl, $sum);
$percentualeArrotondata = round($percentuale, 2);

//echo " ".$percentuale."%";


//function calcuatePercentage($in_bkl,$totale);
  //          if($totale !=0){
    //            return($in_bkl * 100) / $totale;

      //      }else{
        //            return 0;
          //  }
            


//print_r($lavorati);
//print_r($in_bkl);






//$query2= 'SELECT * FROM `vtiger_plenitudecf` WHERE cf_3563 = CURRENT_DATE and cf_3673 = "Ok Firma"';




$result= $db_instance->query($query1);

//$result1= $db_instance2->query($query2);





//echo $query1;



$tabella="<table  border=2>
               <tr><td colspan='2' style='background-color: red; color: white; width: 30%; height:50%; font-size: 30px'>Percentuale Vodafone da Lavorare</td></tr>";
               
               
              
             //   fwrite($myfile, $intestazione);      
              //  fwrite($myfile, $rigcsv);
             
                

        while($riga=$result->fetch_assoc()){
                $tabella.= 
                            "<tr><td style='background-color: green; color: white; width: 30%; height:50%; font-size: 30px'>".$percentualeArrotondata." ".'%'."</td></tr>";
              // $rigcsv=$percentuale.";".$totale."\n";
               
                     //  fwrite($myfile, $rigcsv);
         
       }

       $tabella.="</table>";

       print_r($tabella);
