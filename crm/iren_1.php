<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//
//date_default_timezone_set('Europe/Rome');
//
//require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
//require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
//require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
//
//$obj19 = new Connessione();
//$conn19 = $obj19->apriConnessione();
//
//$objCrm = new ConnessioneCrm();
//$connCrm = $objCrm->apriConnessioneCrm();
/**
 * Inizio Processo prelievo giornaliero Siscall1
 */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$confrontoCRM = date('Y-m-1', strtotime('-3 months'));
$provenienza = "iren";
$tipoCampagna = "";

/**
 * Recupero valore idStato
 */
$queryIdStato = "SELECT max(idStato) FROM `logImport`";
$risultatoIdStato = $conn19->query($queryIdStato);
$rigaStato = $risultatoIdStato->fetch_array();
$idStato = $rigaStato[0] + 1;
/**
 * Query inserimento log iniziale
 */
$queryInzioLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataImport','$provenienza','Inizio Import da $provenienza',0,'$idStato')";
$conn19->query($queryInzioLog);
/**
 * Query ricerca sul crm2
 */
$queryRicerca = "SELECT 
    operatore.user_name as 'operatore',
        e.smownerid AS smownerid,
c.capfatturazione AS capfatturazione,
c.capfornitura AS capfornitura,
c.capresidenza AS capresidenza,
c.cellulareprimario AS cellulareprimario,
c.codicecampagna AS codicecampagna,
c.codicefiscale AS codicefiscale,
c.codicefiscaletitolare AS codicefiscaletitolare,
c.codicematricola AS codicematricola,
c.codicematricolagas AS codicematricolagas,
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
e.smcreatorid AS smcreatorid,
c.dataarrivolead AS dataarrivolead,
c.datacontratto AS datacontratto,
c.datanascita AS datanascita,
c.datarilasciodocumento AS datarilasciodocumento,
c.datasottoscrizionecontratto AS datasottoscrizionecontratto,
c.dataswitchingas AS dataswitchingas,
c.dataswitchinluce AS dataswitchinluce,
c.dataswitchoutgas AS dataswitchoutgas,
c.dataswitchoutluce AS dataswitchoutluce,
c.deltaintervallogas AS deltaintervallogas,
c.deltaintervalloluce AS deltaintervalloluce,
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
c.iren AS iren,
c.irenno AS irenno,
e.modifiedby AS modifiedby,
c.leadid AS leadid,
c.listid AS listid,
c.luogonascita AS luogonascita,
c.mail AS mail,
c.mercato AS mercato,
c.metodoinviofattura AS metodoinviofattura,
c.metodopagamento AS metodopagamento,
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
c.partitaiva AS partitaiva,
c.pod AS pod,
c.potenzaimpianto AS potenzaimpianto,
c.precheck AS precheck,
c.provincianascita AS provincianascita,
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
c.irenid AS irenid

      FROM 
        vtiger_irencf as cf 
        inner join vtiger_iren as c on cf.irenid=c.irenid 
        inner join vtiger_crmentity as e on c.irenid=e.crmid 
       inner join vtiger_users as operatore on e.smownerid=operatore.id 
        WHERE 
        c.datasottoscrizionecontratto >'2025-01-01' and  e.deleted=0 
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
    $queryErroreLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataErrore','$provenienza','$errore',0,'$idStato')";
    $conn19->query($queryErroreLog);
}
/**
 * se non da errore svuota la tabella, segnando nei log l'operazione, e la ricarica riga per riga da zero
 */ else {
    //$queryTruncate = "TRUNCATE TABLE `iren`";
    $queryTruncate = "DELETE FROM iren WHERE data>='2025-01-01'";
    $conn19->query($queryTruncate);
    //$queryTruncate2 = "TRUNCATE TABLE `aggiuntaIren`";
    $queryTruncate2 = "DELETE FROM `aggiuntaIren` WHERE mese>='2025-01-01'";
    $conn19->query($queryTruncate2);
    $dataTruncate = date('Y-m-d H:i:s');
    $queryTruncateLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataTruncate','$provenienza','Truncate $provenienza',0,'$idStato')";
    $conn19->query($queryTruncateLog);

    while ($riga = $risultato->fetch_array()) {
        $pesoFormazione = 0;
        $isFormazione = false;
        $pesoTotalePagato = 0;

        $pezzoLordo = 0;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pezzoFormazione = 0;

        $user = $riga['operatore'];
        $data = date('Y-m-d', strtotime(strtr($riga['datasottoscrizionecontratto'], '/', '-')));
        $comodity = strtolower($riga['commodity']);
        $mercato = strtolower($riga['mercato']);
        $sede = $riga['sede'];
        $metodoPagamento = $riga['metodopagamento'];
        $metodoInvio = $riga['metodoinviofattura'];
        $statoPDA = $riga['statopda'];
        $statoLuce = $conn19->real_escape_string($riga['statoplicoluce']);
        $statoGas = $conn19->real_escape_string($riga['statoplicogas']);
        $winback = "no";
        $dataCreazione = $riga['createdtime'];
        $codiceCampagna = $riga['codicecampagna'];
        $pratica = $riga['irenid'];
        $codicePlicoLuce = $riga['codiceplicoluce'];
        $codicePlicoGas = $riga['codiceplicogas'];
        $sanataBo = "no";
        $tipoAcquisizione = $riga['tipoacquisizione'];
        $idGestioneLead = $riga['idsponsorizzata'];
        $leadId = $riga['leadid'];
        /**
         * ricerca id stato PDA
         */
        $queryStatoPda = "SELECT * FROM `irenStatoPDA` where descrizione='$statoPDA'";
        $risultatoStatoPda = $conn19->query($queryStatoPda);
        $conteggioStatoPda = $risultatoStatoPda->num_rows;
        if ($conteggioStatoPda == 0) {
            $queryInserimentoStatoPda = "INSERT INTO `irenStatoPDA`(`descrizione`) VALUES ('$statoPDA')";
            $conn19->query($queryInserimentoStatoPda);
            $idStatoPda = $conn19->insert_id;
        } else {
            $rigaStatoPda = $risultatoStatoPda->fetch_array();
            $idStatoPda = $rigaStatoPda[0];
        }

        /**
         * ricerca id stato luce
         */
        $queryStatoLuce = "SELECT * FROM `irenStatoLuce` where descrizione='$statoLuce'";
        $risultatoStatoLuce = $conn19->query($queryStatoLuce);
        $conteggioStatoLuce = $risultatoStatoLuce->num_rows;
        if ($conteggioStatoLuce == 0) {
            $queryInserimentoStatoLuce = "INSERT INTO `irenStatoLuce`( `descrizione`) VALUES ('$statoLuce')";
            $conn19->query($queryInserimentoStatoLuce);
            $idStatoLuce = $conn19->insert_id;
        } else {
            $rigaStatoLuce = $risultatoStatoLuce->fetch_array();
            $idStatoLuce = $rigaStatoLuce[0];
        }

        /**
         * Ricerca id stato Gas
         */
        $queryStatoGas = "SELECT * FROM `irenStatoGas` where descrizione='$statoGas'";
        //echo $queryStatoGas;
        $risultatoStatoGas = $conn19->query($queryStatoGas);
        $conteggioStatoGas = $risultatoStatoGas->num_rows;
        //echo $conteggioStatoGas;
        if ($conteggioStatoGas == 0) {
            $queryInserimentoStatoGas = "INSERT INTO `irenStatoGas`( `descrizione`) VALUES ('$statoGas')";
            //echo $queryInserimentoStatoGas;
            $conn19->query($queryInserimentoStatoGas);
            $idStatoGas = $conn19->insert_id;
        } else {
            $rigaStatoGas = $risultatoStatoGas->fetch_array();
            $idStatoGas = $rigaStatoGas[0];
        }



        if ($winback == "Si") {
            $mandato = "Iren Retention";
            $idMandato = 5;
        } else {
            $mandato = "Iren";
            $idMandato = 4;
        }
//
        $queryCampagna = "SELECT * FROM `irenCampagna` where nome='$codiceCampagna'";
        //echo $queryStatoGas;
        $risultatoCampagna = $conn19->query($queryCampagna);
        $conteggioCampagna = $risultatoCampagna->num_rows;
        //echo $conteggioStatoGas;
        if ($conteggioCampagna == 0) {
            $queryInserimentoCampagna = "INSERT INTO `irenCampagna`( `nome`) VALUES ('$codiceCampagna')";
            //echo $queryInserimentoStatoGas;
            $conn19->query($queryInserimentoCampagna);
            $idCampagna = $conn19->insert_id;
        } else {
            $rigaCampagna = $risultatoCampagna->fetch_array();
            $idCampagna = $rigaCampagna[0];
            $tipoCampagna = $rigaCampagna[2];
        }




        $queryInserimento = "INSERT INTO `iren`"
                . "( `creatoDa`, `data`, `comodity`, `mercato`, `sede`, `metodoPagamento`, `statoPDA`, `statoLuce`, `statoGas`, `dataImport`, `mandato`,idStatoPda,idStatoLuce,idStatoGas,winback,idMandato,dataCreazione,idCampagna,campagna,metodoInvio,pratica,sanataBo,tipoAcquisizione,idGestioneLead,leadId)"
                . " VALUES ('$user','$data','$comodity','$mercato','$sede','$metodoPagamento','$statoPDA','$statoLuce','$statoGas','$dataImport','$mandato','$idStatoPda','$idStatoLuce','$idStatoGas','$winback','$idMandato','$dataCreazione','$idCampagna','$codiceCampagna','$metodoInvio','$pratica','$sanataBo','$tipoAcquisizione','$idGestioneLead','$leadId')";
        //echo $queryInserimento;
        $conn19->query($queryInserimento);

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
        if ($data < "2024-03-01") {
            $queryPesoComodity = "SELECT peso FROM `irenPesiComoditi` WHERE tipoCampagna='$tipoCampagna' AND valore='$comodity' AND dataInizioValidita='$mese'";
            $risultatoPesoComodity = $conn19->query($queryPesoComodity);
            if (($risultatoPesoComodity->num_rows) > 0) {
                $rigaPesoComodity = $risultatoPesoComodity->fetch_array();
                $pesoComodity = $rigaPesoComodity[0];
            }
        } else {
            $queryPesoComodity = "SELECT peso FROM `irenPesiComoditi` WHERE tipoCampagna='$tipoCampagna' AND valore='$comodity' AND dataInizioValidita='$mese' AND tipoAcquisizione='$tipoAcquisizione'";
            //echo $queryPesoComodity;
            $risultatoPesoComodity = $conn19->query($queryPesoComodity);
            if (($risultatoPesoComodity->num_rows) > 0) {
                $rigaPesoComodity = $risultatoPesoComodity->fetch_array();
                $pesoComodity = $rigaPesoComodity[0];
            }
        }

        $queryPesoInvio = "SELECT peso FROM `irenPesiInvio` WHERE tipoCampagna='$tipoCampagna' AND valore='$metodoInvio' AND dataInizioValidita='$mese'";
        $risultatoPesoInvio = $conn19->query($queryPesoInvio);
        if (($risultatoPesoInvio->num_rows) > 0) {
            $rigaPesoInvio = $risultatoPesoInvio->fetch_array();
            $pesoInvio = $rigaPesoInvio[0];
        }


        $queryPesoPagamento = "SELECT peso FROM `irenPesiMetodoPagamento` WHERE tipoCampagna='$tipoCampagna' AND valore='$metodoPagamento' AND dataInizioValidita='$mese'";
        $risultatoPesoPagamento = $conn19->query($queryPesoPagamento);
        if (($risultatoPesoPagamento->num_rows) > 0) {
            $rigaPesoPagamento = $risultatoPesoPagamento->fetch_array();
            $pesoPagamento = $rigaPesoPagamento[0];
        }

        $queryFaseLuce = "SELECT fase FROM `irenStatoLuce` WHERE id='$idStatoLuce'";
        $risultatoFaseLuce = $conn19->query($queryFaseLuce);
        $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
        $faseLuce = $rigaFaseLuce[0];

        $queryFaseGas = "SELECT fase FROM `irenStatoGas` WHERE id='$idStatoGas'";
        $risultatoFaseGas = $conn19->query($queryFaseGas);
        $rigaFaseGas = $risultatoFaseGas->fetch_array();
        $faseGas = $rigaFaseGas[0];

        $queryFasePDA = "SELECT fase FROM `irenStatoPDA` WHERE id='$idStatoPda'";
        $risultatoFasePDA = $conn19->query($queryFasePDA);
        $rigaFasePDA = $risultatoFasePDA->fetch_array();
        $fasePDA = $rigaFasePDA[0];

        if ($fasePDA == "") {
            
        } else {
            $pesoTotaleLordo = 0.5;
            if ($comodity == "dual") {
                $pezzoLordo = 2;
            } else {
                $pezzoLordo = 1;
            }
        }
        $pesoSanataBo = 0;
        $tipoSanataBo = 0;
//        if ($sanataBo == "SI") {
//            $querySanataBo = "SELECT peso,tipoDetrazione FROM `irenPesiSanata` WHERE tipoCampagna='$tipoCampagna' AND valore='$sanataBo' AND dataInizioValidita='$mese' ";
//            //echo $querySanataBo;
//            $risultatoPesoSanato = $conn19->query($querySanataBo);
//            if (($risultatoPesoSanato->num_rows) > 0) {
//                $rigaPesoSanato = $risultatoPesoSanato->fetch_array();
//                $pesoSanataBo = $rigaPesoSanato[0];
//                $tipoSanataBo = $rigaPesoSanato[1];
////                echo $pesoSanataBo;
////                echo "<br>";
//            }
//        }


//        if ($tipoSanataBo == 1) {
//            $pesoTotaleLordo = $pesoTotaleLordo - $pesoSanataBo;
//        } elseif ($tipoSanataBo == 2) {
//            $pesoTotaleLordo = $pesoTotaleLordo - ($pesoTotaleLordo * $pesoSanataBo);
//        }

        $pesoTotaleNetto = 0;
        if ($faseGas == "OK" && $faseLuce == "OK" && $comodity == "dual") {
            if ($data >= '2024-06-01' && $metodoPagamento == "Bollettino Postale" && $pesoTotaleLordo >= 0.4) {
                $pesoTotaleNetto = 0.5;
                $pezzoNetto = $pezzoLordo;
            } else {
                $pesoTotaleNetto = $pesoTotaleLordo;
                $pezzoNetto = $pezzoLordo;
            }
        } elseif ($faseLuce == "OK" && $comodity == "luce") {
            if ($data >= '2024-06-01' && $metodoPagamento == "Bollettino Postale" && $pesoTotaleLordo >= 0.4) {
                $pesoTotaleNetto = 0.5;
                $pezzoNetto = $pezzoLordo;
            } else {
                $pesoTotaleNetto = $pesoTotaleLordo;
                $pezzoNetto = $pezzoLordo;
            }
        } elseif ($faseGas == "OK" && $comodity == "gas") {
            if ($data >= '2024-06-01' && $metodoPagamento == "Bollettino Postale" && $pesoTotaleLordo >= 0.4) {
                $pesoTotaleNetto = 0.5;
                $pezzoNetto = $pezzoLordo;
            } else {
                $pesoTotaleNetto = $pesoTotaleLordo;
                $pezzoNetto = $pezzoLordo;
            }
        } elseif ($faseLuce == "OK" && $comodity == "polizza") {
            if ($data >= '2024-06-01' && $metodoPagamento == "Bollettino Postale" && $pesoTotaleLordo >= 0.4) {
                $pesoTotaleNetto = 0.5;
                $pezzoNetto = $pezzoLordo;
            } else {
                $pesoTotaleNetto = $pesoTotaleLordo;
                $pezzoNetto = $pezzoLordo;
            }
        } elseif ($faseGas == "OK" && $comodity == "polizza") {
            if ($data >= '2024-06-01' && $metodoPagamento == "Bollettino Postale" && $pesoTotaleLordo >= 0.4) {
                $pesoTotaleNetto = 0.5;
                $pezzoNetto = $pezzoLordo;
            } else {
                $pesoTotaleNetto = $pesoTotaleLordo;
                $pezzoNetto = $pezzoLordo;
            }
        }

        $fasePost = "KO";
        $statoPost = "";
        if ($faseGas == $faseLuce && $comodity == "dual") {
            $fasePost = $faseGas;
            $statoPost = $statoGas;
        } elseif ($comodity == "luce") {
            $fasePost = $faseLuce;
            $statoPost = $statoLuce;
        } elseif ($comodity == "gas") {
            $fasePost = $faseGas;
            $statoPost = $statoGas;
        } elseif ($faseLuce <> null && $comodity == "polizza") {
            $fasePost = $faseLuce;
            $statoPost = $statoLuce;
        } elseif ($faseGas <> null && $comodity == "polizza") {
            $fasePost = $faseGas;
            $statoPost = $statoGas;
        }

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
//
        $queryInserimentoSecondario = "INSERT INTO `aggiuntaIren"
                . "`(`id`, `tipoCampagna`, `pesoComodity`, `pesoInvio`, `pesoMPagamento`, `totalePesoLordo`, `faseLuce`, `faseGas`, `totalePesoNetto`,"
                . "mese,fasePDA,pesoTotalePagato,codicePlicoLuce,codicePlicoGas,pesoFormazione, pezzoLordo, pezzoNetto, pezzoPagato, pezzoFormazione,fasePost,statoPost) "
                . "VALUES ('$indiceContratto','$tipoCampagna','$pesoComodity','$pesoInvio','$pesoPagamento','$pesoTotaleLordo','$faseLuce','$faseGas','$pesoTotaleNetto',"
                . "'$mese','$fasePDA','$pesoTotalePagato','$codicePlicoLuce','$codicePlicoGas','$pesoFormazione', '$pezzoLordo', '$pezzoNetto', '$pezzoPagato','$pezzoFormazione','$fasePost','$statoPost')";
        //echo $queryInserimentoSecondario;
        $conn19->query($queryInserimentoSecondario);
    }
}
$dataFine = date('Y-m-d H:i:s');
$queryFineLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataFine','$provenienza','Fine Import da $provenienza',1,'$idStato')";
$conn19->query($queryFineLog);
//header("location:../pannello.php");

/**
     * Inizio prelievo dati da crm2 tabella Vivigas
     */
    