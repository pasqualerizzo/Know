<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";

require "/Applications/MAMP/htdocs/Know/funzioni/funzioniSorgenia.php";

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
$provenienza = "sorgenia";
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
//print_r($arrayPesiComodity);
$arrayMacroStato = arrayMacroStato($conn19);
/**
 * Query ricerca sul crm2
 */
$queryRicerca = "SELECT "
        . "operatore.user_name as 'CreatoDa', "
        . "date_format(sorgeniacf.cf_5641,'%d-%m-%Y') as 'data', "
        . "sorgeniacf.cf_5643 as 'comodity', "
        . "sorgeniacf.cf_5645 as 'Mercato', "
        . "sorgeniacf.cf_5647 as 'Sede', "
        . "sorgeniacf.cf_5751 as 'MetodoPagamento', "
        . "sorgeniacf.cf_5703 as 'MetodoInvio', "
        . "sorgeniacf.cf_5751 as 'StatoPDA', "
        . "sorgeniacf.cf_5771 as 'StatoLuce', "
        . "sorgeniacf.cf_5777 as 'StatoGas', "
        . "entity.createdtime AS 'dataCreazione', "
        . "sorgeniacf.cf_5649 as 'CodiceCampagna', "
        . "sorgeniacf.sorgeniaid AS 'pratica', "
        . "sorgeniacf.cf_5769 AS 'codicePlicoLuce', "
        . "sorgeniacf.cf_5775 AS 'codicePlicoGas', "
        . "sorgeniacf.cf_5651 AS 'tipoAcquisizione', "
        . "sorgeniacf.cf_5793 AS 'idGestioneLead', "
        . "sorgeniacf.cf_5795 AS 'idLeadId', "
        . "sorgeniacf.cf_5653 AS 'cod_matricola', "
        //. "sorgeniacf.cf_5480 AS 'dataSwitchInLuce', "
        //. "sorgeniacf.cf_5482 AS 'dataSwitchOutLuce', "
        //. "sorgeniacf.cf_5484 AS 'dataSwitchInGas', "
        //. "sorgeniacf.cf_5486 AS 'dataSwitchOutGas', "
        . "sorgeniacf.cf_5773 AS 'NoteStatoLuce', "
        . "sorgeniacf.cf_5779 AS 'NoteStatoGas', "
        . " sorgeniacf.cf_5655 AS 'UTMCampaign'  "
        . "FROM "
        //
        . "vtiger_sorgeniacf as sorgeniacf "
        . "inner join vtiger_sorgenia as sorgenia on sorgeniacf.sorgeniaid=sorgenia.sorgeniaid "
        . "inner join vtiger_crmentity as entity on sorgenia.sorgeniaid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        //
        . "WHERE "
        . "sorgeniacf.cf_5641 >'2025-02-10' "
        . "and  entity.deleted=0 "
        . "and sorgeniacf.cf_5751 not in ('Annullata','Annullato') ";

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
    truncateSorgenia($conn19);
    truncateAggiuntaSorgenia($conn19);
    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {
        $pesoFormazione = 0;
        $isFormazione = false;
        $pesoTotalePagato = 0;
        $pesoFormazione = 0;
        $pesoComodity = 0;
        $pesoInvio = 0;
        $pesoPagamento = 0;
        $pesoTotaleLordo = 0;
        $pezzoLordo = 0;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pezzoFormazione = 0;
        $pesoTotalePagato = 0;
        $pesoTotaleNetto = 0;

        $user = $riga['CreatoDa'];
        $data = date('Y-m-d', strtotime(strtr($riga['data'], '/', '-')));
        $comodity = $riga['comodity'];
        $mercato = $riga['Mercato'];
        $sede = $riga['Sede'];
        $metodoPagamento = $riga['MetodoPagamento'];
        $metodoInvio = $riga['MetodoInvio'];
        $statoPDA = $riga['StatoPDA'];
        $statoLuce = $conn19->real_escape_string($riga['StatoLuce']);
        $statoGas = $conn19->real_escape_string($riga['StatoGas']);
        $winback = "no";
        $dataCreazione = $riga['dataCreazione'];
        $codiceCampagna = $riga['CodiceCampagna'];
        $pratica = $riga['pratica'];
        $codicePlicoLuce = $riga['codicePlicoLuce'];
        $codicePlicoGas = $riga['codicePlicoGas'];
        $sanataBo = "no";
        $tipoAcquisizione = $riga['tipoAcquisizione'];
        $idGestioneLead = $riga['idGestioneLead'];
        $leadId = $riga['idLeadId'];
        $codMatricola = $riga['cod_matricola'];
        /**
         * Aggiunto il 11/10/2024 per aggiungere la gestio9ne delle date si switch
         */
        $dataSwitchInLuce = "0000-00-00";
        $dataSwitchOutLuce = "0000-00-00";
        $dataSwitchInGas = "0000-00-00";
        $dataSwitchOutGas = "0000-00-00";

        $noteStatoLuce = $conn19->real_escape_string($riga['NoteStatoLuce']);
        $noteStatoGas = $conn19->real_escape_string($riga['NoteStatoGas']);
        $utm = $conn19->real_escape_string($riga['UTMCampaign']);

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
            $idStatoPda = $arrayStatoPda[$statoPDA][0];
            $fasePDA = $arrayStatoPda[$statoPDA][1];
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
            $idStatoLuce = $arrayStatoLuce[$statoLuce][0];
            $faseLuce = $arrayStatoLuce[$statoLuce][1];
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
            $idStatoGas = $arrayStatoGas[$statoGas][0];
            $faseGas = $arrayStatoGas[$statoGas][1];
        }
        /**
         * Controllo se la pratica è winback
         */
        $mandato = "Enel";
        $idMandato = 1;
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
            $idCampagna = $arrayCampagna[$codiceCampagna][0];
            $tipoCampagna = $arrayCampagna[$codiceCampagna][1];
            $tipoCampagna = ($tipoCampagna == "") ? "Prospect" : $tipoCampagna;
        }


        $faseMacroStatoGas = "";
        if (array_key_exists($noteStatoGas, $arrayMacroStato)) {
            $faseMacroStatoGas = $arrayMacroStato[$noteStatoGas][1];
            //echo $faseStatoGas . "<br>";
        } else {
            aggiuntaMacroStato($conn19, $noteStatoGas);
            $arrayMacroStato = arrayMacroStato($conn19);
            if (array_key_exists($noteStatoGas, $arrayMacroStato)) {
                $faseMacroStatoGas = $arrayMacroStato[$noteStatoGas][1];
            }
        }
        $faseMacroStatoLuce = "";
        if (array_key_exists($noteStatoLuce, $arrayMacroStato)) {
            $faseMacroStatoLuce = $arrayMacroStato[$noteStatoLuce][1];
            //echo $faseStatoGas . "<br>";
        } else {
            aggiuntaMacroStato($conn19, $noteStatoLuce);
            $arrayMacroStato = arrayMacroStato($conn19);
            if (array_key_exists($noteStatoLuce, $arrayMacroStato)) {
                $faseMacroStatoLuce = $arrayMacroStato[$noteStatoLuce][1];
            }
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
        try {
            $queryInserimento = "INSERT INTO `sorgenia`"
                    . "( `creatoDa`, `data`, `comodity`, `mercato`, `sede`, `metodoPagamento`, `statoPDA`, `statoLuce`, `statoGas`, `dataImport`, `mandato`,"
                    . "idStatoPda,idStatoLuce,idStatoGas,winback,idMandato,dataCreazione,idCampagna,campagna,metodoInvio,pratica,sanataBo,tipoAcquisizione,idGestioneLead,leadId,codMaticola,"
                    . " `dataSwitchInLuce`, `dataSwitchOutLuce`, `dataSwitchInGas`, `dataSwitchOutGas`,deltaMortalitaLuce,deltaMortalitaGas,noteStatoLuce,noteStatoGas,utm)"
                    . " VALUES "
                    . " ('$user','$data','$comodity','$mercato','$sede','$metodoPagamento','$statoPDA','$statoLuce','$statoGas','$dataImport','$mandato',"
                    . " '$idStatoPda','$idStatoLuce','$idStatoGas','$winback','$idMandato','$dataCreazione','$idCampagna','$codiceCampagna','$metodoInvio','$pratica','$sanataBo','$tipoAcquisizione','$idGestioneLead','$leadId','$codMatricola',"
                    . " '$dataSwitchInLuce','$dataSwitchOutLuce','$dataSwitchInGas','$dataSwitchOutGas','$delta','$delta','$noteStatoLuce','$noteStatoGas','$utm')";
            // echo $queryInserimento;
            $conn19->query($queryInserimento);
        } catch (Exception $e) {
            echo "Errore inverimento nella tabella Enel " . $e;
        }
        if ($data >= "2025-03-01") {
            /*
             * Aggiunta per calcolo pesi
             */
            $indiceContratto = $conn19->insert_id;
            $pesoComodity = 0;
            $pesoInvio = 0;
            $pesoPagamento = 0;
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
            /**
             * peso comodity
             * aggiornamento del 5/12/2023 aggiunto il tipo di acquisizione
             */
            $pesoComodity = 0.5;

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
        } else {

            /*
             * Aggiunta per calcolo pesi
             */
            $indiceContratto = $conn19->insert_id;
            $pesoComodity = 0;
            $pesoInvio = 0;
            $pesoPagamento = 0;
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
            /**
             * peso comodity
             * aggiornamento del 5/12/2023 aggiunto il tipo di acquisizione
             */
            if ($metodoPagamento == "Bolletta" || $tipoAcquisizione == "Subentro" || $winback == "si") {
                $pesoComodity = 0.5;
                $pesoInvio = 0;
                $pesoPagamento = 0;
            } else {
                $pesoComodity = 1;
                $pesoInvio = 0;
                $pesoPagamento = 0;
            }

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
        }
        /**
         * Inserimento nella tabella aggiuntiva Plenitude su metrics
         */
        $queryInserimentoSecondario = "INSERT INTO `aggiuntaSorgenia"
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
$obj19->chiudiConnessione();
$objCrm->chiudiConnessioneCrm();

