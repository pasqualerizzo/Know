<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";

require "/Applications/MAMP/htdocs/Know/funzioni/funzioniEnel.php";

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
//print_r($arrayPesiComodity);
$arrayMacroStato = arrayMacroStato($conn19);
/**
 * Query ricerca sul crm2
 */
$queryRicerca = "SELECT "
        . "replace(operatore.user_name,'enel','') as 'Creato da', "
        . "date_format(enelcf.cf_884,'%d-%m-%Y') as 'data', "
        . "enelcf.cf_886 as 'comodity', "
        . "enelcf.cf_888 as 'Mercato', "
        . "enelcf.cf_890 as 'Sede', "
        . "enelcf.cf_988 as 'Metodo Pagamento', "
        . "enelcf.cf_932 as 'Metodo Invio', "
        . "enelcf.cf_1006 as 'Stato PDA', "
        . "enelcf.cf_1036 as 'Stato Luce', "
        . "enelcf.cf_1034 as 'Stato Gas', "
        . "entity.createdtime AS 'dataCreazione', "
        . "enelcf.cf_1256 as 'Codice Campagna', "
        . "enelcf.enelid AS 'pratica', "
        . "enelcf.cf_1032 AS 'codicePlicoLuce', "
        . "enelcf.cf_1030 AS 'codicePlicoGas', "
        . "enelcf.cf_1574 AS 'tipo acquisizione', "
        . "enelcf.cf_5478 AS 'id gestione lead', "
        . "enelcf.cf_1048 AS 'id leadId', "
        . "enelcf.cf_894 AS 'cod_matricola', "
        . "enelcf.cf_5480 AS 'data Switch In Luce', "
        . "enelcf.cf_5482 AS 'data Switch Out Luce', "
        . "enelcf.cf_5484 AS 'data Switch In Gas', "
        . "enelcf.cf_5486 AS 'data Switch Out Gas', "
        . "enelcf.cf_5490 AS 'Note Stato Luce', "
        . "enelcf.cf_5492 AS 'Note Stato Gas', "
        . " enelcf.cf_5488 AS 'UTM Campaign'  "
        . "FROM "
        . "vtiger_enelcf as enelcf "
        . "inner join vtiger_enel as enel on enelcf.enelid=enel.enelid "
        . "inner join vtiger_crmentity as entity on enel.enelid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "enelcf.cf_884 >'2025-02-10' and  entity.deleted=0 "
        . "and enelcf.cf_1006 not in ('Annullata','Annullato') ";

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
    truncateEnel($conn19);
    truncateAggiuntaEnel($conn19);
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
            $queryInserimento = "INSERT INTO `enel`"
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
        $queryInserimentoSecondario = "INSERT INTO `aggiuntaEnel"
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
    
