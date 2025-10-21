<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');


require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj=new ConnessioneCrm();
$connCrm=$obj->apriConnessioneCrm();

$patrichePlenitude=[];

$queryRicerca = "SELECT "
        
        . "plenicf.plenitudeid AS 'pratica', "
        . "plenicf.cf_4072 AS 'id gestione lead' "

        . "FROM "
        . "vtiger_plenitudecf as plenicf "
        . "inner join vtiger_plenitude as pleni on plenicf.plenitudeid=pleni.plenitudeid "
        . "inner join vtiger_crmentity as entity on pleni.plenitudeid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "plenicf.cf_4070 not like 'G%'  and  plenicf.cf_4072<>'' "
        . "AND plenicf.cf_3571='SPN_LEAD'";
    //echo $queryRicerca;
$risultato=$connCrm->query($queryRicerca);
while($riga=$risultato->fetch_array()){
    $temp=[];
    array_push($temp, $riga[0]);
    array_push($temp, $riga[1]);
    
    $queryGestioneLead="SELECT idSponsorizzata FROM `gestioneLead` where leadId='$riga[1]'";
    //echo $queryGestioneLead;
    $risultatoGLC=$conn19->query($queryGestioneLead);
    if($risultatoGLC->num_rows>0){
    $rigaGLC=$risultatoGLC->fetch_array();
    array_push($temp,$rigaGLC[0]);    
     
    }else{
        array_push($temp,'Non Presente');  
    }
    array_push($patrichePlenitude,$temp);   
}
foreach ($patrichePlenitude as $t){
    //echo "pratica: ". $t[0]." leadId: ".$t[1]." e gcl: ".$t[2]."<br>";
    $queryUpadate="Update vtiger_plenitudecf set cf_4070='$t[2]' where plenitudeid=$t[0]";
    $connCrm->query($queryUpadate);
}


/**
 * Vivigas
 */
$praticheVivigas=[];

$queryRicerca = "SELECT "        
        . "vivicf.vivigasid AS 'pratica', "
        . "vivicf.cf_4132 AS 'id gestione lead' "

        . "FROM "
        . "vtiger_vivigascf as vivicf "
        . "inner join vtiger_vivigas as vivi on vivicf.vivigasid=vivi.vivigasid "
        . "inner join vtiger_crmentity as entity on vivi.vivigasid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "vivicf.cf_4132 not like 'G%'  and  vivicf.cf_2978<>'' "
        . "AND vivicf.cf_1860='SPN_LEAD'";
    //echo $queryRicerca;
$risultato=$connCrm->query($queryRicerca);
while($riga=$risultato->fetch_array()){
    $temp=[];
    array_push($temp, $riga[0]);
    array_push($temp, $riga[1]);
    
    $queryGestioneLead="SELECT idSponsorizzata FROM `gestioneLead` where leadId='$riga[1]'";
    //echo $queryGestioneLead;
    $risultatoGLC=$conn19->query($queryGestioneLead);
    if($risultatoGLC->num_rows>0){
    $rigaGLC=$risultatoGLC->fetch_array();
    array_push($temp,$rigaGLC[0]);    
     
    }else{
        array_push($temp,'Non Presente');  
    }
    array_push($patrichePlenitude,$temp);   
}
foreach ($patrichePlenitude as $t){
    //echo "pratica: ". $t[0]." leadId: ".$t[1]." e gcl: ".$t[2]."<br>";
    $queryUpadate="Update vtiger_vivigascf set cf_4132='$t[2]' where vivigasid=$t[0]";
    $connCrm->query($queryUpadate);
}


?>
