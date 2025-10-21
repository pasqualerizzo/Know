<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniPlenitude.php";

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

$queryRicerca = "SELECT "
        . "replace(operatore.user_name,'enel','') as 'Creato da', "
        . "date_format(plenicf.cf_3563,'%d-%m-%Y') as 'data', "
        . "plenicf.cf_3565 as 'comodity', "
        . "plenicf.cf_3567 as 'Mercato', "
        . "plenicf.cf_3569 as 'Sede', "
        . "plenicf.cf_3659 as 'Metodo Pagamento', "
        . "plenicf.cf_3609 as 'Metodo Invio', "
        . "plenicf.cf_3673 as 'Stato PDA', "
        . "plenicf.cf_3681 as 'Stato Luce', "
        . "plenicf.cf_3683 as 'Stato Gas', "
        . "entity.createdtime AS 'dataCreazione', "
        . "plenicf.cf_3571 as 'Codice Campagna', "
        . "plenicf.plenitudeid AS 'pratica', "
        . "plenicf.cf_3677 AS 'codicePlicoLuce', "
        . "plenicf.cf_3679 AS 'codicePlicoGas', "
        . "plenicf.cf_3739 AS 'tipo acquisizione', "
        . "plenicf.cf_4070 AS 'id gestione lead', "
        . "plenicf.cf_4072 AS 'id leadId', "
        . "plenicf.cf_3867 AS 'cod_matricola', "
        
        . "plenicf.cf_4851 AS 'data Switch In Luce', "
        . "plenicf.cf_4853 AS 'data Switch Out Luce', "
        . "plenicf.cf_4859 AS 'data Switch In Gas', "
        . "plenicf.cf_4861 AS 'data Switch Out Gas' "
        . "FROM "
        . "vtiger_plenitudecf as plenicf "
        . "inner join vtiger_plenitude as pleni on plenicf.plenitudeid=pleni.plenitudeid "
        . "inner join vtiger_crmentity as entity on pleni.plenitudeid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "plenicf.cf_3563 >'2023-01-31' and  entity.deleted=0 and plenicf.cf_3673<>'Annullata'";

$risultato = $connCrm->query($queryRicerca);

while ($riga = $risultato->fetch_array()) {
        $pesoFormazione = 0;
        $isFormazione = false;
        $pesoTotalePagato = 0;
        $pezzoLordo = 0;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pezzoFormazione = 0;

        $user = $riga[0];
        $data = date('Y-m-d', strtotime(strtr($riga[1], '/', '-')));
        $comodity = $riga[2];
        $mercato = $riga[3];
        $sede = $riga[4];
        $metodoPagamento = $riga[5];
        $metodoInvio = $riga[6];
        $statoPDA = $riga[7];
//        $statoLuce = $conn19->real_escape_string($riga[8]);
//        $statoGas = $conn19->real_escape_string($riga[9]);
        $winback = "no";
        $dataCreazione = $riga[10];
        $codiceCampagna = $riga[11];
        $pratica = $riga[12];
        $codicePlicoLuce = $riga[13];
        $codicePlicoGas = $riga[14];
        $sanataBo = "no";
        $tipoAcquisizione = $riga[15];
        $idGestioneLead = $riga[16];
        $leadId = $riga[17];
        $codMatricola = $riga[18];
        /**
         * Aggiunto il 11/10/2024 per aggiungere la gestio9ne delle date si switch
         */
        $dataSwitchInLuce=($riga[19]=="")?"0000-00-00":$riga[19];
        $dataSwitchOutLuce=($riga[20]=="")?"0000-00-00":$riga[20];
        $dataSwitchInGas=($riga[21]=="")?"0000-00-00":$riga[21];
        $dataSwitchOutGas=($riga[22]=="")?"0000-00-00":$riga[22];
        
         $dataSWILuce=new DateTime($dataSwitchInLuce);
        $dataSWOLuce=new DateTime($dataSwitchOutLuce);
        $differenzaLuce=$dataSWILuce->diff($dataSWOLuce);
        $giorniSWOLuce=$differenzaLuce->days;
        $deltaLuce= round(($giorniSWOLuce/30),0);
        
        $dataSWIGas=new DateTime($dataSwitchInGas);
        $dataSWOGas=new DateTime($dataSwitchOutGas);
        $differenzaGas=$dataSWIGas->diff($dataSWOGas);
        $giorniSWOGas=$differenzaGas->days;
        $deltaGas=round(($giorniSWOGas/30),0);
        
        echo $deltaLuce."-".$deltaGas."<br>";
}

?>    