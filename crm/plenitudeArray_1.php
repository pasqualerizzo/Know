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

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrm();
$connCrm = $objCrm->apriConnessioneCrm();

/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$confrontoCRM = date('Y-m-1', strtotime('-3 months'));
$provenienza = "plenitude";
$tipoCampagna = "";

$arrayStatoPda = arrayStatoPda($conn19);
//print_r($arrayStatoPda);
$arrayStatoLuce = arrayStatoLuce($conn19);
//print_r($arrayStatoLuce);    
$arrayStatoGas = arrayStatoGas($conn19);
//print_r($arrayStatoGas);
$arrayCampagna = arrayCampagna($conn19);
//print_r($arrayCampagna);
$arrayPesiComodity = arrayPesiComodity($conn19);

$arrayMacroStato = arrayMacroStato($conn19);
/**
 * Query ricerca sul crm2
 */
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
        . "plenicf.cf_4861 AS 'data Switch Out Gas', "
        . "plenicf.cf_3745 AS 'Note Stato Luce', "
        . "plenicf.cf_3747 AS 'Note Stato Gas', "
        . " plenicf.cf_4885 AS 'UTM Campaign'  "
        . "FROM "
        . "vtiger_plenitudecf as plenicf "
        . "inner join vtiger_plenitude as pleni on plenicf.plenitudeid=pleni.plenitudeid "
        . "inner join vtiger_crmentity as entity on pleni.plenitudeid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "plenicf.cf_3563 >'2023-01-31' and  entity.deleted=0 and plenicf.cf_3673<>'Annullata'";

//echo $queryRicerca;
$risultato = $connCrm->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {
    truncatePlenitude($conn19);
    truncateAggiuntaPlenitude($conn19);
    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {
        $pesoFormazione = 0;
        $isFormazione = false;
        $pesoTotalePagato = 0;
        $pezzoLordo = 0;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pezzoFormazione = 0;
        $pesoTotaleNetto=0;

        $user = $riga[0];
        $data = date('Y-m-d', strtotime(strtr($riga[1], '/', '-')));
        $comodity = $riga[2];
        $mercato = $riga[3];
        $sede = $riga[4];
        $metodoPagamento = $riga[5];
        $metodoInvio = $riga[6];
        $statoPDA = $riga[7];
        $statoLuce = $conn19->real_escape_string($riga[8]);
        $statoGas = $conn19->real_escape_string($riga[9]);
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
        $dataSwitchInLuce = ($riga[19] == "") ? "0000-00-00" : $riga[19];
        $dataSwitchOutLuce = ($riga[20] == "") ? "0000-00-00" : $riga[20];
        $dataSwitchInGas = ($riga[21] == "") ? "0000-00-00" : $riga[21];
        $dataSwitchOutGas = ($riga[22] == "") ? "0000-00-00" : $riga[22];

        $noteStatoLuce = $conn19->real_escape_string($riga[23]);
        $noteStatoGas = $conn19->real_escape_string($riga[24]);
        $utm = $conn19->real_escape_string($riga[25]);

        /**
         * ricerca id stato PDA
         */
        if (array_key_exists($statoPDA, $arrayStatoPda)) {
            $idStatoPda = $arrayStatoPda[$statoPDA][0];
            $fasePDA = $arrayStatoPda[$statoPDA][1];
//echo $idStatoPDA;
        } else {
            aggiuntaStatoPda($conn19, $statoPDA);
            $arrayStatoPda = arrayStatoPda($conn19);
        }
        /**
         * ricerca id stato luce
         */
        if (array_key_exists($statoLuce, $arrayStatoLuce)) {
            $idStatoLuce = $arrayStatoLuce[$statoLuce][0];
            $faseLuce = $arrayStatoLuce[$statoLuce][1];
//echo $idStatoLuce;
        } else {
            aggiuntaStatoLuce($conn19, $statoLuce);
            $arrayStatoLuce = arrayStatoLuce($conn19);
        }
        /**
         * Ricerca id stato Gas
         */
        if (array_key_exists($statoGas, $arrayStatoGas)) {
            $idStatoGas = $arrayStatoGas[$statoGas][0];
            $faseGas = $arrayStatoGas[$statoGas][1];
//echo $faseStatoGas . "<br>";
        } else {
            aggiuntaStatoGas($conn19, $statoGas);
            $arrayStatoGas = arrayStatoGas($conn19);
        }
        /**
         * Controllo se la pratica è winback
         */
        if ($winback == "Si") {
            $mandato = "Plenitude Retention";
            $idMandato = 5;
        } else {
            $mandato = "Plenitude";
            $idMandato = 4;
        }
        /**
         * Recupero dati Campagna
         */
        if (array_key_exists($codiceCampagna, $arrayCampagna)) {
            $idCampagna = $arrayCampagna[$codiceCampagna][0];
            $tipoCampagna = $arrayCampagna[$codiceCampagna][1];
            $tipoCampagna = ($tipoCampagna == "") ? "Prospect" : $tipoCampagna;
        } else {
            aggiuntaCampagna($conn19, $codiceCampagna);
            $arrayCampagna = arrayCampagna($conn19);
        }


        $faseMacroStatoGas = "";
        if (array_key_exists($noteStatoGas, $arrayMacroStato)) {
            $faseMacroStatoGas = $arrayMacroStato[$noteStatoGas][1];
//echo $faseStatoGas . "<br>";
        } else {
            aggiuntaMacroStato($conn19, $noteStatoGas);
            $arrayMacroStato = arrayMacroStato($conn19);
        }
        $faseMacroStatoLuce = "";
        if (array_key_exists($noteStatoLuce, $arrayMacroStato)) {
            $faseMacroStatoLuce = $arrayMacroStato[$noteStatoLuce][1];
//echo $faseStatoGas . "<br>";
        } else {
            aggiuntaMacroStato($conn19, $noteStatoLuce);
            $arrayMacroStato = arrayMacroStato($conn19);
        }

        /**
         * Differenza tra date di switch
         */
        $dataSWILuce = new DateTime($dataSwitchInLuce);
        $dataSWOLuce = new DateTime($dataSwitchOutLuce);
        $differenzaLuce = $dataSWILuce->diff($dataSWOLuce);
        $giorniSWOLuce = $differenzaLuce->days;
        $deltaLuce = round(($giorniSWOLuce / 30), 0);

        $dataSWIGas = new DateTime($dataSwitchInGas);
        $dataSWOGas = new DateTime($dataSwitchOutGas);
        $differenzaGas = $dataSWIGas->diff($dataSWOGas);
        $giorniSWOGas = $differenzaGas->days;
        $deltaGas = round(($giorniSWOGas / 30), 0);

        $delta = ($deltaLuce >= $deltaGas) ? $deltaLuce : $deltaGas;

        /**
         * Inserimento dei valori nella tabella plenitude di metrics
         */
        $queryInserimento = "INSERT INTO `plenitude`"
                . "( `creatoDa`, `data`, `comodity`, `mercato`, `sede`, `metodoPagamento`, `statoPDA`, `statoLuce`, `statoGas`, `dataImport`, `mandato`,"
                . "idStatoPda,idStatoLuce,idStatoGas,winback,idMandato,dataCreazione,idCampagna,campagna,metodoInvio,pratica,sanataBo,tipoAcquisizione,idGestioneLead,leadId,codMaticola,"
                . " `dataSwitchInLuce`, `dataSwitchOutLuce`, `dataSwitchInGas`, `dataSwitchOutGas`,deltaMortalitaLuce,deltaMortalitaGas,noteStatoLuce,noteStatoGas,utm)"
                . " VALUES "
                . " ('$user','$data','$comodity','$mercato','$sede','$metodoPagamento','$statoPDA','$statoLuce','$statoGas','$dataImport','$mandato',"
                . " '$idStatoPda','$idStatoLuce','$idStatoGas','$winback','$idMandato','$dataCreazione','$idCampagna','$codiceCampagna','$metodoInvio','$pratica','$sanataBo','$tipoAcquisizione','$idGestioneLead','$leadId','$codMatricola',"
                . " '$dataSwitchInLuce','$dataSwitchOutLuce','$dataSwitchInGas','$dataSwitchOutGas','$delta','$delta','$noteStatoLuce','$noteStatoGas','$utm')";
// echo $queryInserimento;
        $conn19->query($queryInserimento);
        /*
         * Aggiunta per calcolo pesi
         */
        $indiceContratto = $conn19->insert_id;
        $pesoComodity = 0;
       
        $pesoTotaleLordo = 0;
        $mese = date('Y-m-01', strtotime($data));

        $queryFormazione = "SELECT ore FROM `formazioneTotale` where nomeCompleto='$user' and giorno='$data'";
//        echo $queryFormazione;

        $risultatoFormazione = $conn19->query($queryFormazione);
        if (($risultatoFormazione->num_rows) > 0) {
            $rigaFormazione = $risultatoFormazione->fetch_array();
            $ore = $rigaFormazione[0];
//            echo "----".$ore;

            if ($ore < 45) {
                $isFormazione = true;
            } else {
                $isFormazione = false;
            }
        } else {
            $isFormazione = false;
        }
     
        if ($metodoPagamento == "Bollettino Postale" || $tipoAcquisizione == "Subentro" || $winback == "si") {
            $pesoComodity = 0.5;
        } else {
            $pesoComodity = 1;
        }
        $pesoInvio = 0;
        $pesoPagamento = 0;

        /**
         * Somma del peso Totale Lordo
         */
        if ($fasePDA == "") {
            
        } else {
            $pesoTotaleLordo = $pesoComodity + $pesoInvio + $pesoPagamento;
            if ($comodity == "Dual") {
                $pezzoLordo = 2;
            } else {
                $pezzoLordo = 1;
            }
        }


        if ($faseGas == "OK" && $faseLuce == "OK" && $comodity == "Dual") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        } elseif ($faseLuce == "OK" && $comodity == "Luce") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        } elseif ($faseGas == "OK" && $comodity == "Gas") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
            
            
        } elseif ($faseLuce == "OK" && $comodity == "Polizza") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        } elseif ($faseGas == "OK" && $comodity == "Polizza") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        }


        /**
         * CAlcolo della fase post 
         */
        $fasePost = "KO";
        $statoPost = "";
        if ($faseGas == $faseLuce && $comodity == "Dual") {
            $fasePost = $faseGas;
            $statoPost = $statoGas;
        } elseif ($comodity == "Luce") {
            $fasePost = $faseLuce;
            $statoPost = $statoLuce;
        } elseif ($comodity == "Gas") {
            $fasePost = $faseGas;
            $statoPost = $statoGas;
        } elseif ($statoLuce <> "" && $comodity == "Polizza") {
            $fasePost = $faseLuce;
            $statoPost = $statoLuce;
        } elseif ($statoGas <> "" && $comodity == "Polizza") {
            $fasePost = $faseGas;
            $statoPost = $statoGas;
        }



        /**
         * Calcolo peso totale pagato
         */
        if ($fasePDA == "OK") {
            if ($isFormazione == true) {
                $pesoTotalePagato = 0;
                $pesoFormazione = $pesoTotaleNetto;

                $pezzoFormazione = $pezzoNetto;
            } else {
                $pesoFormazione = 0;
                $pesoTotalePagato = $pesoTotaleNetto;
                $pezzoPagato = $pezzoNetto;
            }
        } else {
            $pesoFormazione = 0;
            $pesoTotalePagato = 0;
        }

        /**
         * Inserimento nella tabella aggiuntiva Plenitude su metrics
         */
        $queryInserimentoSecondario = "INSERT INTO `aggiuntaPlenitude"
                . "`(`id`, `tipoCampagna`, `pesoComodity`, `pesoInvio`, `pesoMPagamento`, `totalePesoLordo`, `faseLuce`, `faseGas`, `totalePesoNetto`,"
                . "mese,fasePDA,pesoTotalePagato,codicePlicoLuce,codicePlicoGas,pesoFormazione, pezzoLordo, pezzoNetto, pezzoPagato, pezzoFormazione,fasePost,statoPost,faseMacroStatoLuce,faseMacroStatoGas) "
                . "VALUES "
                . " ('$indiceContratto','$tipoCampagna','$pesoComodity','$pesoInvio','$pesoPagamento','$pesoTotaleLordo','$faseLuce','$faseGas','$pesoTotaleNetto', "
                . "'$mese','$fasePDA','$pesoTotalePagato','$codicePlicoLuce','$codicePlicoGas','$pesoFormazione', '$pezzoLordo', '$pezzoNetto', '$pezzoPagato','$pezzoFormazione','$fasePost','$statoPost','$faseMacroStatoLuce','$faseMacroStatoGas')";
//echo $queryInserimentoSecondario;
        try {
            $conn19->query($queryInserimentoSecondario);
        } catch (exception $e) {
            echo $e;
        }
    }
    $dataFine = date('Y-m-d H:i:s');

//header("location:../pannello.php");
}


    
