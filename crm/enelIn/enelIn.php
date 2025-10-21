<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniEnelIn.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrmNuovo();
$connCrm = $objCrm->apriConnessioneCrmNuovo();

$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$provenienza = "enelIn";
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

$queryRicerca = "SELECT "
        . "operatore.user_name as 'assegnata', "
        . "date_format(vtiger_enelin.datacontratto,'%d-%m-%Y') as 'data', "
        . "vtiger_enelin.commodity as 'comodity', "
        . "'Consumer' as 'Mercato', "
        . "'Rende' as 'Sede', "
        . "vtiger_enelin.metodopagamento as 'Metodo Pagamento', "
        . "vtiger_enelin.metodoinviofattura as 'Metodo Invio', "
        . "vtiger_enelin.statopda as 'Stato PDA', "
        . "vtiger_enelin.statoplicoluce as 'Stato Luce', "
        . "vtiger_enelin.statoplicogas as 'Stato Gas', "
        . "'NO' as 'winback', "
        . "entity.createdtime AS 'dataCreazione', "
        . "'EnelIn' as 'Codice Campagna', "
        . "vtiger_enelin.enelinid AS 'pratica', "
        . "'NO' as 'Sanata BO', "
        . "'NO' as 'assicurazione', "
        . "vtiger_enelin.fibraenel as 'fibra', "
        . "'NO' as 'aggiuntivi', "
        . "vtiger_enelin.codiceplicoluce as 'idLuce', "
        . "vtiger_enelin.codiceplicogas as 'idGas', "
        . "vtiger_enelin.nbacard as 'nbaCard' "
        . "FROM vtiger_enelin "
        . "INNER JOIN vtiger_crmentity as entity ON vtiger_enelin.enelinid = entity.crmid "
        . "INNER JOIN vtiger_users as operatore ON entity.smownerid = operatore.id "
        . "WHERE vtiger_enelin.datacontratto > '2023-01-31'";

$risultato = $connCrm->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    echo $errore;
} else {
    truncateEnelIn($conn19);
    truncateAggiuntaEnelIn($conn19);
    while ($riga = $risultato->fetch_array()) {
       

        $pezzoLordo = 0;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pezzoFormazione = 0;

        $user = $riga["assegnata"];
        $data = date('Y-m-d', strtotime(strtr($riga["data"], '/', '-')));
        $comodity = $riga["comodity"];
        $mercato = $riga["Mercato"];
        $sede = $riga["Sede"];
        $metodoPagamento = $riga["Metodo Pagamento"];
        $metodoInvio = $riga["Metodo Invio"];
        $statoPDA = $riga["Stato PDA"];
        $statoLuce = $conn19->real_escape_string($riga["Stato Luce"]);
        $statoGas = $conn19->real_escape_string($riga["Stato Gas"]);
        $winback = $riga["winback"];
        $dataCreazione = $riga["dataCreazione"];
        $codiceCampagna = $riga["Codice Campagna"];
        $pratica = $riga["pratica"];
        $sanataBo = $riga["Sanata BO"];
        $assicurazione = $riga["assicurazione"];
        $fibra = $riga["fibra"];
        $aggiuntivi = $riga["aggiuntivi"];
        $idLuce = $riga["idLuce"];
        $idGas = $riga["idGas"];
        $nbacard=$riga["nbaCard"];

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

        $mandato = "EnelIn";
        $idMandato = 35;
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
//        if (array_key_exists($noteStatoGas, $arrayMacroStato)) {
//            $faseMacroStatoGas = $arrayMacroStato[$noteStatoGas][1];
//            //echo $faseStatoGas . "<br>";
//        } else {
//            aggiuntaMacroStato($conn19, $noteStatoGas);
//            $arrayMacroStato = arrayMacroStato($conn19);
//            if (array_key_exists($noteStatoGas, $arrayMacroStato)) {
//                $faseMacroStatoGas = $arrayMacroStato[$noteStatoGas][1];
//            }
//        }
//        $faseMacroStatoLuce = "";
//        if (array_key_exists($noteStatoLuce, $arrayMacroStato)) {
//            $faseMacroStatoLuce = $arrayMacroStato[$noteStatoLuce][1];
//            //echo $faseStatoGas . "<br>";
//        } else {
//            aggiuntaMacroStato($conn19, $noteStatoLuce);
//            $arrayMacroStato = arrayMacroStato($conn19);
//            if (array_key_exists($noteStatoLuce, $arrayMacroStato)) {
//                $faseMacroStatoLuce = $arrayMacroStato[$noteStatoLuce][1];
//            }
//        }
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



        $queryInserimento = "INSERT INTO `enelIn`"
                . "( `creatoDa`, `data`, `comodity`, `mercato`, `sede`, `metodoPagamento`, `statoPDA`, `statoLuce`, `statoGas`, `dataImport`, `mandato`,idStatoPda,idStatoLuce,idStatoGas,winback,idMandato,dataCreazione,idCampagna,campagna,metodoInvio,pratica,sanataBo,assicurazione,fibra,aggiuntivi,nbaCard)"
                . " VALUES ('$user','$data','$comodity','$mercato','$sede','$metodoPagamento','$statoPDA','$statoLuce','$statoGas','$dataImport','$mandato','$idStatoPda','$idStatoLuce','$idStatoGas','$winback','$idMandato','$dataCreazione','$idCampagna','$codiceCampagna','$metodoInvio','$pratica','$sanataBo','$assicurazione','$fibra','$aggiuntivi','$nbacard')";
//echo $queryInserimento;
        $conn19->query($queryInserimento);

        /*
         * Aggiunta per calcolo pesi
         */
        $indiceContratto = $conn19->insert_id;
        $pesoComodity = 0;
        $pesoInvio = 0;
        $pesoPagamento = 0;
//$pesoTotaleLordo = 0;
        $pesoAssicurazione = 0;
        $pesoFibra = 0;
        $pesoAggiuntivi = 0;

        $pesoTotaleLordo = 0;
        $pesoTotaleNetto = 0;
        $pesoFormazione = 0;
        $pesoTotalePagato =0;
        $mese = date('Y-m-1', strtotime($data));

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

//Peso Commodity





        if ($comodity == "Dual") {
            $pezzoLordo = 2;
        } else {
            $pezzoLordo = 1;
        }

        $queryFaseLuce = "SELECT fase FROM `enelInStatoLuce` WHERE id='$idStatoLuce'";
        $risultatoFaseLuce = $conn19->query($queryFaseLuce);
        $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
        $faseLuce = $rigaFaseLuce[0];

        $queryFaseGas = "SELECT fase FROM `enelInStatoGas` WHERE id='$idStatoGas'";
        $risultatoFaseGas = $conn19->query($queryFaseGas);
        $rigaFaseGas = $risultatoFaseGas->fetch_array();
        $faseGas = $rigaFaseGas[0];

        $queryFasePDA = "SELECT fase FROM `enelInStatoPDA` WHERE id='$idStatoPda'";
        $risultatoFasePDA = $conn19->query($queryFasePDA);
        $rigaFasePDA = $risultatoFasePDA->fetch_array();
        $fasePDA = $rigaFasePDA[0];

        if ($faseGas == "OK" && $faseLuce == "OK" && $comodity == "Dual") {

            $pezzoNetto = $pezzoLordo;
        } elseif ($faseLuce == "OK" && $comodity == "Luce") {

            $pezzoNetto = $pezzoLordo;
        } elseif ($faseGas == "OK" && $comodity == "Gas") {

            $pezzoNetto = $pezzoLordo;
        }
        if ($comodity == "Enel X") {
            if ($faseGas == "OK" && $faseLuce == "OK") {

                $pezzoNetto = $pezzoLordo;
            } elseif ($faseGas == "OK" && $faseLuce == "") {

                $pezzoNetto = $pezzoLordo;
            } elseif ($faseGas == "" && $faseLuce == "OK") {

                $pezzoNetto = $pezzoLordo;
            }
        }if ($comodity == "Fibra Enel") {
            if ($faseGas == "OK" && $faseLuce == "OK") {

                $pezzoNetto = $pezzoLordo;
            } elseif ($faseGas == "OK" && $faseLuce == "") {

                $pezzoNetto = $pezzoLordo;
            } elseif ($faseGas == "" && $faseLuce == "OK") {

                $pezzoNetto = $pezzoLordo;
            }
        }

        if ($isFormazione == true) {

            $pezzoFormazione = $pezzoNetto;
        } else {

            $pezzoPagato = $pezzoNetto;
        }

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
        } elseif ($faseLuce <> null && $comodity == "Polizza") {
            $fasePost = $faseLuce;
            $statoPost = $statoLuce;
        } elseif ($faseGas <> null && $comodity == "Polizza") {
            $fasePost = $faseGas;
            $statoPost = $statoGas;
        }


        $queryInserimentoSecondario = "INSERT INTO `aggiuntaEnelIn`(`id`, `tipoCampagna`, `pesoComodity`, `pesoInvio`, `pesoMPagamento`, `totalePesoLordo`,"
                . " `faseLuce`, `faseGas`, `totalePesoNetto`,mese,fasePDA,pesoTotalePagato,pesoAssicurazione,pesoFibra,pesoAggiuntivi,idLuce,idGas,pesoFormazione,"
                . " pezzoLordo, pezzoNetto, pezzoPagato, pezzoFormazione,fasePost,statoPost) "
                . "VALUES ('$indiceContratto','$tipoCampagna','$pesoComodity','$pesoInvio','$pesoPagamento','$pesoTotaleLordo',"
                . "'$faseLuce','$faseGas','$pesoTotaleNetto','$mese','$fasePDA','$pesoTotalePagato','$pesoAssicurazione','$pesoFibra','$pesoAggiuntivi','$idLuce','$idGas','$pesoFormazione',"
                . " '$pezzoLordo', '$pezzoNetto', '$pezzoPagato', '$pezzoFormazione','$fasePost','$statoPost')";
//echo $queryInserimentoSecondario;
        $conn19->query($queryInserimentoSecondario);
    }


    header("location:../pannello.php");
}

$obj19->chiudiConnessione();
$objCrm->chiudiConnessioneCrm();

