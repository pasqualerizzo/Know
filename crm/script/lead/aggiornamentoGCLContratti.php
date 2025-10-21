<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');


require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$obj=new ConnessioneCrmNuovo();
$connCrm=$obj->apriConnessioneCrmNuovo();

$patrichePlenitude=[];

$queryRicerca = "SELECT "
        
        . "plenicf.plenitudeid AS 'pratica', "
        . "pleni.leadid AS 'leadId' "
        . "FROM "
        . "vtiger_plenitudecf as plenicf "
        . "inner join vtiger_plenitude as pleni on plenicf.plenitudeid=pleni.plenitudeid "
        . "inner join vtiger_crmentity as entity on pleni.plenitudeid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "pleni.idsponsorizzata not like 'G%'  and  pleni.leadid<>'' "
        . "AND pleni.codicecampagna='SPN_LEAD'";
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
    $queryUpadate="Update vtiger_plenitude set idsponsorizzata='$t[2]' where plenitudeid=$t[0]";
    $connCrm->query($queryUpadate);
}


/**
 * Vivigas
 */
$praticheVivigas=[];

$queryRicerca = "SELECT "        
        . "vivicf.vivigasid AS 'pratica', "
        . "vivi.leadid  AS 'id gestione lead' "
        . "FROM "
        . "vtiger_vivigascf as vivicf "
        . "inner join vtiger_vivigas as vivi on vivicf.vivigasid=vivi.vivigasid "
        . "inner join vtiger_crmentity as entity on vivi.vivigasid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "vivi.idsponsorizzata not like 'G%'  and  vivi.leadid<>'' "
        . "AND vivi.codicecampagna='SPN_LEAD'";
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
    $queryUpadate="Update vtiger_vivigas set idsponsorizzata='$t[2]' where vivigasid=$t[0]";
    $connCrm->query($queryUpadate);
}


$praticheEnel=[];

$queryRicerca = "SELECT "        
        . "enelcf.enelid AS 'pratica', "
        . "enel.leadid AS 'id gestione lead' "

        . "FROM "
        . "vtiger_enelcf as enelcf "
        . "inner join vtiger_enel as enel on enelcf.enelid=enel.enelid "
        . "inner join vtiger_crmentity as entity on enel.enelid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "enel.idsponsorizzata not like 'G%'  and  enel.leadid<>'' "
        . "AND enel.codicecampagna='SPN_LEAD'";
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
    $queryUpadate="Update vtiger_enel set leadid='$t[2]' where enelid=$t[0]";
    $connCrm->query($queryUpadate);
}


$praticheIren=[];

$queryRicerca = "SELECT "        
        . "irencf.irenid AS 'pratica', "
        . "iren.leadid AS 'id gestione lead' "

        . "FROM "
        . "vtiger_irencf as irencf "
        . "inner join vtiger_iren as iren on irencf.irenid=iren.irenid "
        . "inner join vtiger_crmentity as entity on iren.irenid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "iren.idsponsorizzata not like 'G%'  and  iren.leadid<>'' "
        . "AND iren.codicecampagna='SPN_LEAD'";
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
    $queryUpadate="Update vtiger_iren set leadid='$t[2]' where irenid=$t[0]";
    $connCrm->query($queryUpadate);
}



$praticheTim=[];

$queryRicerca = "SELECT "        
        . "irencf.irenid AS 'pratica', "
        . "iren.leadid AS 'id gestione lead' "

        . "FROM "
        . "vtiger_irencf as irencf "
        . "inner join vtiger_iren as iren on irencf.irenid=iren.irenid "
        . "inner join vtiger_crmentity as entity on iren.irenid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "iren.idsponsorizzata not like 'G%'  and  iren.leadid<>'' "
        . "AND iren.codicecampagna='SPN_LEAD'";
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
    $queryUpadate="Update vtiger_iren set leadid='$t[2]' where irenid=$t[0]";
    $connCrm->query($queryUpadate);
}


$obj->chiudiConnessioneCrm();
$obj19->chiudiConnessione();
?>
