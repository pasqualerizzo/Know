<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";

require "/Applications/MAMP/htdocs/Know/funzioni/funzioniEnel.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrmNuovo();
$connCrm = $objCrm->apriConnessioneCrmNuovo();
/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$confrontoCRM = date('Y-m-1', strtotime('-3 months'));
$provenienza = "Enel";
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
$queryRicerca = " 
    SELECT
     operatore.user_name as 'operatore',
    e.smownerid AS assignedto,
c.capfatturazione AS capfatturazione,
c.capfornitura AS capfornitura,
c.capresidenza AS capresidenza,
c.cellulareprimario AS cellulareprimario,
c.codicecampagna AS codicecampagna,
c.codicefiscale AS codicefiscale,
c.codicefiscaletitolare AS codicefiscalesocialetitolare,
c.codicematricola AS codicematricola,
c.codicematricolagas AS codicematricolagas,
cf.cf_1615 AS codiceoperatore,
c.codicepdr AS codicepdr,
c.codiceplicogas AS codiceplicogas,
c.codiceplicoluce AS codiceplicoluce,
c.cognome AS cognome,
c.commodity AS commodity,
c.comunefatturazione AS comunefatturazione,
c.comunefornitura AS comunefornitura,
c.comuneresidenza AS comuneresidenza,
c.consumoannuogas AS consumoannuogas,
c.consumoenergiaelettrica AS consumoenergiaelettrica,
e.createdtime AS createdtime,
e.smcreatorid AS creator,
c.dataarrivolead AS dataarrivolead,
c.datacontratto AS datacontratto,
c.datanascita AS datadinascita,
cf.cf_1654 AS datainserimento,
c.datarilasciodocumento AS datarilasciodocumento,
c.datasottoscrizionecontratto AS datasottoscrizionecontratto,
c.dataswitchingas AS dataswitchingas,
c.dataswitchinluce AS dataswitchinluce,
c.dataswitchoutgas AS dataswitchoutgas,
c.dataswitchoutluce AS dataswitchoutluce,
c.deltaintervallogas AS deltaintervallogas,
c.deltaintervalloluce AS deltaintervalloluce,
c.enel AS enel,
c.enelno AS enelno,
c.enterilasciodocumento AS enterilasciodocumento,
c.finechiamata AS finechiamata,
c.fornitoreenergiaelettrica AS fornitoreenergiaelettrica,
c.fornitoregas AS fornitoregas,
c.iban AS iban,
c.idsponsorizzata AS idsponsorizzata,
c.indirizzofatturazione AS indirizzofatturazione,
c.indirizzofornitura AS indirizzofornitura,
c.indirizzoresidenza AS indirizzoresidenza,
c.iniziochiamata AS iniziochiamata,
e.modifiedby AS lastmodifiedby,
c.leadid AS leadid,
c.listid AS listid,
c.luogonascita AS luogodinascita,
c.mail AS mail,
c.mercato AS mercato,
c.metodoinviofattura AS metododiinviofattura,
c.metodopagamento AS metododipagamento,
e.modifiedtime AS modifiedtime,
c.motivazioneko AS motivazioneko,
c.nazionalita AS nazionalita,
c.nome AS nome,
c.notebackoffice AS notebackoffice,
c.noteoperatore AS noteoperatore,
c.noteplicogas AS noteplicogas,
c.noteplicoluce AS noteplicoluce,
c.numerodocumento AS numerodocumento,
c.numeroentante AS numeroentante,
c.orarioacquisito AS orarioacquisito,
c.orariofirma AS orariofirma,
cf.cf_1659 AS orarioprimatelefonata,
c.partitaiva AS partitaiva,
c.pod AS pod,
c.potenzaimpianto AS potenzaimpianto,
c.precheck AS precheck,
c.provincianascita AS provinciadinascita,
c.provinciafatturazione AS provinciafatturazione,
c.provinciafornitura AS provinciafornitura,
c.provinciaresidenza AS provinciaresidenza,
c.ragionesociale AS ragionesociale,
c.ragionesocialetitolare AS ragionesocialetitolare,
c.recall AS recall,
c.recapitoalternativo AS recapitoalternativo,
c.residente AS residente,
c.sede AS sede,
c.sesso AS sesso,
c.statopda AS statopda,
c.statoplicogas AS statoplicogas,
c.statoplicoluce AS statoplicoluce,
c.tariffagas AS tariffagas,
c.tariffaluce AS tariffaluce,
c.telefono AS telefono,
c.tipoacquisizione AS tipoacquisizione,
c.tipochiamata AS tipochiamata,
c.tipoesecuzione AS tipoesecuzione,
c.tipoprodotto AS tipoprodotto,
c.utm AS utm,
c.verificabopost AS verificabopost,
c.whatsapp AS whatsapp,
c.winback AS winback,
c.enelid AS enelid


FROM 
        vtiger_enelcf as cf 
        inner join vtiger_enel as c on cf.enelid=c.enelid 
        inner join vtiger_crmentity as e on c.enelid=e.crmid 
        inner join vtiger_users as operatore on e.smownerid=operatore.id 
        WHERE 
        c.datasottoscrizionecontratto >'2025-02-10' and  e.deleted=0 
        AND c.statopda not in ('Annullata','Annullato')  
";

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

        $user = $riga['operatore'];
        $data = date('Y-m-d', strtotime(strtr($riga['datasottoscrizionecontratto'], '/', '-')));
        $comodity = $riga['commodity'];
        $mercato = $riga['mercato'];
        $sede = $riga['sede'];
        $metodoPagamento = $riga['metododipagamento'];
        $metodoInvio = $riga['metododiinviofattura'];
        $statoPDA = $riga['statopda'];
        $statoLuce = $conn19->real_escape_string($riga['statoplicoluce']);
        $statoGas = $conn19->real_escape_string($riga['statoplicogas']);
        $winback = "no";
        $dataCreazione = $riga['createdtime'];
        $codiceCampagna = $riga['codicecampagna'];
        $pratica = $riga['enelid'];
        $codicePlicoLuce = $riga['codiceplicoluce'];
        $codicePlicoGas = $riga['codiceplicogas'];
        $sanataBo = "no";
        $tipoAcquisizione = $riga['tipoacquisizione'];
        $idGestioneLead = $riga['idsponsorizzata'];
        $leadId = $riga['leadid'];
        $codMatricola = $riga['codicematricola'];
        /**
         * Aggiunto il 11/10/2024 per aggiungere la gestio9ne delle date si switch
         */
        $dataSwitchInLuce = ($riga['dataswitchinluce'] == "") ? "0000-00-00" : $riga['dataswitchinluce'];
        $dataSwitchOutLuce = ($riga['dataswitchoutluce'] == "") ? "0000-00-00" : $riga['dataswitchoutluce'];
        $dataSwitchInGas = ($riga['dataswitchingas'] == "") ? "0000-00-00" : $riga['dataswitchingas'];
        $dataSwitchOutGas = ($riga['dataswitchoutgas'] == "") ? "0000-00-00" : $riga['dataswitchoutgas'];

        $noteStatoLuce = $conn19->real_escape_string($riga['noteplicoluce']);
        $noteStatoGas = $conn19->real_escape_string($riga['noteplicogas']);
        $utm = $conn19->real_escape_string($riga['utm']);

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
             echo $queryInserimento;
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
        echo $queryInserimentoSecondario;
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

