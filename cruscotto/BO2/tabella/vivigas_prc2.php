<?php

//sommario contratti in stato ACQUISITO ed OK FIRMA quotidiani

include_once 'conenssioneCrm.php';


$db_instance = new mysqli("crm.novaholding.it", "vtiger_external", "2025N0v4k3y!@", "vtiger_db");


$query1= 'SELECT SUM(lavorati) AS somma_lavorati FROM ( 
    SELECT count(vivigasid) as lavorati, statopda AS STATO_PDA FROM `vtiger_vivigas`  WHERE datacontratto = CURRENT_DATE and 
    (statopda = "COMPLETATO FIRMA" OR statopda = "Ok Definitivo")   GROUP BY statopda) as t';
$result1 = $db_instance->query($query1);
if(($result1->num_rows)>0){
$row1 = $result1->fetch_assoc();
$count1 = $row1['somma_lavorati'];
}else{
    $count1=0;
}
$query2= 'SELECT SUM(in_bkl) AS somma_bkl FROM (
      SELECT count(vivigasid) as in_bkl, statopda AS STATO_PDA FROM `vtiger_vivigas`  WHERE datacontratto = CURRENT_DATE and 
      (statopda = "Acquisito" or statopda = "OK VOCAL LIGHT" or statopda = "OK OTP LUCE" or statopda = "OK OTP GAS"  or statopda = "In attesa QC" or statopda = "Recuperato Vocal" or statopda = "IN ATTESA IDENTIFICAZIONE" or statopda = "Ok Vocal" or statopda = "Recuperato Dati" or statopda = "Invio Prima Mail") as t';

$result2= $db_instance->query($query2);
if(($result2->num_rows)>0){
$row2 = $result2->fetch_assoc();
$count2 =+ $row2['somma_bkl'];
}else{
    $count2=0; 
}


//print_r($query1);
//print_r($query2);

$in_bkl = $count2;
$lavorati = $count1;
$totale = $count1 + $count2;

//print_r($lavorati);
//print_r($in_bkl);

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
$percentuale =calculatePercentage($in_bkl, $totale);
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
               <tr><td colspan='2' style='background-color: yellow; color: black; width: 100%; height:100%; font-size: 40px'>Percentuale Vivigas da lavorare</td></tr>";
               
              
             //   fwrite($myfile, $intestazione);      
              //  fwrite($myfile, $rigcsv);
             
                

        while($riga=$result->fetch_assoc()){
                $tabella.= 
                         "<tr><td style='background-color: green; color: white;  width: 100%; height:100%; font-size: 40px'>".$percentualeArrotondata." ".'%'."</td></tr>";
              // $rigcsv=$percentuale.";".$totale."\n";
               
                     //  fwrite($myfile, $rigcsv);
         
       }

       $tabella.="</table>";

       print_r($tabella);


