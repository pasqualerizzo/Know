<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneSiscallLead.php";

$obj = new ConnessioneCrm();
$connCrm = $obj->apriConnessioneCrm();

$objS2=new connessioneSiscallLead();
$connS2=$objS2->apriConnessioneSiscallLead();

$oggi=date("Y-m-d 00:00:00");

$listaEsitoNonSpostabile = [
    401,
    402,
    403,
    404,
    405,
    421,
    422,
    423,
    424,
    'CBHOLD',
    'CALLBK'
];

$queryListaLead=""
        . " SELECT "
        . " cf_4459 as 'leadId', "
        . " gestionechiamataid as id "
        . " FROM "
        . " vtiger_gestionechiamatacf "
        . " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_gestionechiamatacf.gestionechiamataid"
        . " where "
       // . " vtiger_crmentity.createdtime>'$oggi' "
        //. " AND "
        . " cf_4469='' and 4459<>''";

$risultatoListaLead=$connCrm->query($queryListaLead);
while($rigaLista=$risultatoListaLead->fetch_array()){    
    $queryEsito=""
            . " SELECT "
            . " status "
            . " FROM "
            . " vicidial_list "
            . " WHERE "
            . " lead_id='$rigaLista[0]'";
   $risultatoEsito=$connS2->query($queryEsito);
   if(($rigaEsito=$risultatoEsito->fetch_array())){    
    echo $rigaLista[0]."-".$rigaEsito[0]."<br>";
    $queryUpdate=""
            . " UPDATE "
            . " vtiger_gestionechiamatacf "
            . " SET "
            . " cf_4659='$rigaEsito[0]' "
            . " WHERE "
            . " gestionechiamataid='$rigaLista[1]'";
    $connCrm->query($queryUpdate);
    
   }
}
echo "<br>";

$query = " SELECT"
        . " cf_4457 AS 'telefono', "
        . " cf_4659 AS 'esito', "
        . " cf_4687 AS 'duplicato', "
        . " vtiger_crmentity.createdtime AS 'data',"
        . " gestionechiamataid as id "
        . " FROM"
        . " vtiger_gestionechiamatacf"
        . " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_gestionechiamatacf.gestionechiamataid"
        . " WHERE"
        . " cf_4457 <> '' AND "
        . " vtiger_crmentity.createdtime>'$oggi' "
        . " AND cf_4457 IN ("
        . " SELECT"
        . " cf_4457"
        . " FROM"
        . " vtiger_gestionechiamatacf"
        . " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_gestionechiamatacf.gestionechiamataid "
        ." where "
        . " vtiger_crmentity.createdtime>'$oggi' "
        . " GROUP BY"
        . " cf_4457"
        . " HAVING"
        . " COUNT(cf_4457)> 1"
        . " )"
        . " ORDER BY"
        . " telefono,"
        . " data";

$risultato=$connCrm->query($query);
while($riga=$risultato->fetch_array()){
   $telefono=$riga[0];
   $esito=$riga[1];
   $duplicato=$riga[2];
   $dataCreazione=$riga[3];
   $id=$riga[4];
   if(in_array($esito, $listaEsitoNonSpostabile)) {
       $queryUpdate=""
            . " UPDATE "
            . " vtiger_gestionechiamatacf "
            . " SET "
            . " cf_4665='SI' "
            . " WHERE "
            . " gestionechiamataid='$id'";
    $connCrm->query($queryUpdate);
    echo "esito:".$id;
   }
   echo "<br>";
   echo $riga[0]." ".$riga[1]." ".$riga[2]."<br>";
}
?>