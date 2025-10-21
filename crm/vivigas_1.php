<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//
//date_default_timezone_set('Europe/Rome');

//require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
//require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
//require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrm.php";
//require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";
//
//$obj = new ConnessioneVici();
//$conn = $obj->apriConnessioneVici();
//
//$obj19 = new Connessione();
//$conn19 = $obj19->apriConnessione();
//
//$objCrm = new ConnessioneCrm();
//$connCrm = $objCrm->apriConnessioneCrm();
///**
// * Inizio Processo prelievo giornaliero Siscall1
// */
$dataImport = date('Y-m-d H:i:s');
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 days'));
$provenienza = "Vivigas";

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
$queryRicerca = "
    SELECT 
    operatore.user_name as 'operatore',
        c.vivigas AS vivigas,
e.smownerid AS smownerid,
e.createdtime AS createdtime,
e.modifiedtime AS modifiedtime,
c.vivigasno AS vivigasno,
c.leadid AS leadid,
c.idsponsorizzata AS idsponsorizzata,
c.winback AS winback,
c.utm AS utm,
c.tipoesecuzione AS tipoesecuzione,
c.listid AS listid,
c.codicecampagna AS codicecampagna,
c.numeroentante AS numeroentante,
c.sede AS sede,
c.iniziochiamata AS iniziochiamata,
c.finechiamata AS finechiamata,
c.dataarrivolead AS dataarrivolead,
c.datasottoscrizionecontratto AS datasottoscrizionecontratto,
c.commodity AS commodity,
c.mercato AS mercato,
c.tipoacquisizione AS tipoacquisizione,
c.codicematricola AS codicematricola,
c.tipoprodotto AS tipoprodotto,
c.tipochiamata AS tipochiamata,
c.nome AS nome,
c.cognome AS cognome,
c.ragionesociale AS ragionesociale,
c.sesso AS sesso,
c.luogonascita AS luogonascita,
c.provincianascita AS provincianascita,
c.datanascita AS datanascita,
c.codicefiscale AS codicefiscale,
c.partitaiva AS partitaiva,
c.cellulareprimario AS cellulareprimario,
c.telefono AS telefono,
c.recapitoalternativo AS recapitoalternativo,
c.mail AS mail,
c.numerodocumento AS numerodocumento,
c.enterilasciodocumento AS enterilasciodocumento,
c.datarilasciodocumento AS datarilasciodocumento,
c.nazionalita AS nazionalita,
c.indirizzofornitura AS indirizzofornitura,
c.provinciafornitura AS provinciafornitura,
c.capfornitura AS capfornitura,
c.comunefornitura AS comunefornitura,
c.indirizzoresidenza AS indirizzoresidenza,
c.provinciaresidenza AS provinciaresidenza,
c.capresidenza AS capresidenza,
c.comuneresidenza AS comuneresidenza,
c.indirizzofatturazione AS indirizzofatturazione,
c.provinciafatturazione AS provinciafatturazione,
c.capfatturazione AS capfatturazione,
c.comunefatturazione AS comunefatturazione,
c.pod AS pod,
c.tariffaluce AS tariffaluce,
c.fornitoreenergiaelettrica AS fornitoreenergiaelettrica,
c.consumoenergiaelettrica AS consumoenergiaelettrica,
c.potenzaimpianto AS potenzaimpianto,
c.codicepdr AS codicepdr,
c.tariffagas AS tariffagas,
c.fornitoregas AS fornitoregas,
c.consumoannuogas AS consumoannuogas,
c.codicematricolagas AS codicematricolagas,
c.residente AS residente,
c.metodopagamento AS metodopagamento,
c.iban AS iban,
c.ragionesocialetitolare AS ragionesocialetitolare,
c.codicefiscaletitolare AS codicefiscaletitolare,
c.metodoinviofattura AS metodoinviofattura,
c.noteoperatore AS noteoperatore,
c.notebackoffice AS notebackoffice,
c.statopda AS statopda,
c.motivazioneko AS motivazioneko,
c.codiceplicoluce AS codiceplicoluce,
c.statoplicoluce AS statoplicoluce,
c.noteplicoluce AS noteplicoluce,
c.codiceplicogas AS codiceplicogas,
c.statoplicogas AS statoplicogas,
c.noteplicogas AS noteplicogas,
c.dataswitchinluce AS dataswitchinluce,
c.dataswitchoutluce AS dataswitchoutluce,
c.dataswitchingas AS dataswitchingas,
c.dataswitchoutgas AS dataswitchoutgas,
c.deltaintervalloluce AS deltaintervalloluce,
c.deltaintervallogas AS deltaintervallogas,
c.orariofirma AS orariofirma,
c.orarioacquisito AS orarioacquisito,
c.precheck AS precheck,
c.verificabopost AS verificabopost,
c.recall AS recall,
c.whatsapp AS whatsapp,
e.modifiedby AS modifiedby,
e.smcreatorid AS smcreatorid,
c.datacontratto AS datacontratto,
c.vivigasid AS vivigasid

    FROM 
        `vtiger_vivigascf` as cf 
        inner join vtiger_vivigas as c on cf.vivigasid=c.vivigasid 
        inner join vtiger_crmentity as e on c.vivigasid=e.crmid 
        inner join vtiger_users as operatore on e.smownerid=operatore.id 
        WHERE 
        c.datacontratto >'2025-01-01' and  e.deleted=0 
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
    //$queryTruncate = "TRUNCATE TABLE `vivigas`";
    $queryTruncate = "DELETE FROM vivigas WHERE data>='2025-01-01'";
    $conn19->query($queryTruncate);
    $queryTruncate2 = "DELETE FROM `aggiuntaVivigas` WHERE mese>='2025-01-01'";
    //$queryTruncate2 = "TRUNCATE TABLE `aggiuntaVivigas`";
    $conn19->query($queryTruncate2);
    $dataTruncate = date('Y-m-d H:i:s');
    $queryTruncateLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataTruncate','$provenienza','Truncate $provenienza',0,'$idStato')";
    $conn19->query($queryTruncateLog);
    while ($riga = $risultato->fetch_array()) {
        $isFormazione = false;
        $pesoTotalePagato = 0;
        $pesoFormazione = 0;

        $pezzoLordo = 0;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pezzoFormazione = 0;

        $user = $riga['operatore'];
        $data = $riga['datacontratto'];
        $comodity = $riga['commodity'];
        $mercato = $riga['mercato'];
        $sede = $riga['sede'];
        $metodoPagamento = $riga['metodopagamento'];
        $metodoInvio = $riga['metodoinviofattura'];
        $statoPDA = $riga['statopda'];
        $statoLuce = $conn19->real_escape_string($riga['statoplicoluce']);
        $statoGas = $conn19->real_escape_string($riga['statoplicogas']);
        $winback = $riga['winback'];
        $dataCreazione = $riga['createdtime'];
        $codiceCampagna = $riga['codicecampagna'];
        $pratica = $riga['vivigasid'];
        $sanataBo = "no";
        $idRigaLuce = $riga['codiceplicoluce'];
        $idRigaGas = $riga['codiceplicogas'];
        $codiceFornituraLuce = $riga['codiceplicoluce'];
        $codiceFornituraGas = $riga['codiceplicogas'];
        $idGestioneLead = $riga['idsponsorizzata'];
        $leadId = $riga['leadid'];
        $datasottoscrizionecontratto = $riga['datasottoscrizionecontratto'] ?? '0000-00-00';
        /**
         * ricerca id stato PDA
         */
        $queryStatoPda = "SELECT * FROM `vivigasStatoPDA` where descrizione='$statoPDA'";
        $risultatoStatoPda = $conn19->query($queryStatoPda);
        $conteggioStatoPda = $risultatoStatoPda->num_rows;
        if ($conteggioStatoPda == 0) {
            $queryInserimentoStatoPda = "INSERT INTO `vivigasStatoPDA`(`descrizione`) VALUES ('$statoPDA')";
            $conn19->query($queryInserimentoStatoPda);
            $idStatoPda = $conn19->insert_id;
        } else {
            $rigaStatoPda = $risultatoStatoPda->fetch_array();
            $idStatoPda = $rigaStatoPda[0];
        }
        /**
         * ricerca id stato luce
         */
        $queryStatoLuce = "SELECT * FROM `vivigasStatoLuce` where descrizione='$statoLuce'";
        $risultatoStatoLuce = $conn19->query($queryStatoLuce);
        $conteggioStatoLuce = $risultatoStatoLuce->num_rows;
        if ($conteggioStatoLuce == 0) {
            $queryInserimentoStatoLuce = "INSERT INTO `vivigasStatoLuce`( `descrizione`) VALUES ('$statoLuce')";
            $conn19->query($queryInserimentoStatoLuce);
            $idStatoLuce = $conn19->insert_id;
        } else {
            $rigaStatoLuce = $risultatoStatoLuce->fetch_array();
            $idStatoLuce = $rigaStatoLuce[0];
        }
        /**
         * Ricerca id stato Gas
         */
        $queryStatoGas = "SELECT * FROM `vivigasStatoGas` where descrizione='$statoGas'";
        //echo $queryStatoGas;
        $risultatoStatoGas = $conn19->query($queryStatoGas);
        $conteggioStatoGas = $risultatoStatoGas->num_rows;
        //echo $conteggioStatoGas;
        if ($conteggioStatoGas == 0) {
            $queryInserimentoStatoGas = "INSERT INTO `vivigasStatoGas`( `descrizione`) VALUES ('$statoGas')";
            //echo $queryInserimentoStatoGas;
            $conn19->query($queryInserimentoStatoGas);
            $idStatoGas = $conn19->insert_id;
        } else {
            $rigaStatoGas = $risultatoStatoGas->fetch_array();
            $idStatoGas = $rigaStatoGas[0];
        }
        if ($winback == "Si") {
            $mandato = "Vivigas Energia Retention";
            $idMandato = 2;
        } else {
            $mandato = "Vivigas Energia";
            $idMandato = 1;
        }

        $queryCampagna = "SELECT * FROM `vivigasCampagna` where nome='$codiceCampagna'";
        //echo $queryStatoGas;
        $risultatoCampagna = $conn19->query($queryCampagna);
        $conteggioCampagna = $risultatoCampagna->num_rows;
        //echo $conteggioStatoGas;
        if ($conteggioCampagna == 0) {
            $queryInserimentoCampagna = "INSERT INTO `vivigasCampagna`( `nome`) VALUES ('$codiceCampagna')";
            //echo $queryInserimentoStatoGas;
            $conn19->query($queryInserimentoCampagna);
            $idCampagna = $conn19->insert_id;
        } else {
            $rigaCampagna = $risultatoCampagna->fetch_array();
            $idCampagna = $rigaCampagna[0];
            $tipoCampagna = $rigaCampagna[2];
        }




        $queryInserimento = "INSERT INTO `vivigas`"
                . "( `creatoDa`, `data`, `comodity`, `mercato`, `sede`, `metodoPagamento`, `statoPDA`, `statoLuce`, `statoGas`, `dataImport`, `mandato`,idStatoPda,idStatoLuce,idStatoGas,winback,idMandato,dataCreazione,idCampagna,campagna,metodoInvio,pratica,sanataBo,idGestioneLead,leadId,datasottoscrizionecontratto)"
                . " VALUES ('$user','$data','$comodity','$mercato','$sede','$metodoPagamento','$statoPDA','$statoLuce','$statoGas','$dataImport','vivigas','$idStatoPda','$idStatoLuce','$idStatoGas','$winback','$idMandato','$dataCreazione','$idCampagna','$codiceCampagna','$metodoInvio','$pratica','$sanataBo','$idGestioneLead','$leadId','$datasottoscrizionecontratto')";
        //
        //echo $queryInserimento."<br>";
        $conn19->query($queryInserimento);
        
        /*
         * Aggiunta per calcolo pesi
         */
        $indiceContratto = $conn19->insert_id;
        $pesoComodity = 0;
        $pesoInvio = 0;
        $pesoPagamento = 0;
        $pesoTotaleLordo = 0;
        $mese = date('Y-m-1', strtotime($data));
        //formazione

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
//        echo "++++".intval($isFormazione);
// echo "<br>";
        //Peso Commodity

        $tipoAcquisizione = "switch";
        if ($mese >= '2025-02-01') {
            if ($metodoPagamento == "Bollettino Postale" || $tipoAcquisizione == "Subentro" || $winback == "Si") {
                $pesoComodity = 0.5;
            } else {
                $pesoComodity = 1;
            }
        } else {

            $queryPesoComodity = "SELECT peso FROM `vivigasPesiComoditi` WHERE tipoCampagna='$tipoCampagna' AND valore='$comodity' AND dataInizioValidita='$mese'";
            $risultatoPesoComodity = $conn19->query($queryPesoComodity);
            if (($risultatoPesoComodity->num_rows) > 0) {
                $rigaPesoComodity = $risultatoPesoComodity->fetch_array();
                $pesoComodity = $rigaPesoComodity[0];
            }
        }

        if ($mese >= '2025-02-01') {
            $pesoInvio = 0;
        } else {

            $queryPesoInvio = "SELECT peso FROM `vivigasPesiInvio` WHERE tipoCampagna='$tipoCampagna' AND valore='$metodoInvio' AND dataInizioValidita='$mese'";
            $risultatoPesoInvio = $conn19->query($queryPesoInvio);
            if (($risultatoPesoInvio->num_rows) > 0) {
                $rigaPesoInvio = $risultatoPesoInvio->fetch_array();
                $pesoInvio = $rigaPesoInvio[0];
            }
        }


        if ($mese >= '2025-02-01') {
            $pesoPagamento = 0;
        } else {
            $queryPesoPagamento = "SELECT peso FROM `vivigasPesiMetodoPagamento` WHERE tipoCampagna='$tipoCampagna' AND valore='$metodoPagamento' AND dataInizioValidita='$mese'";
            $risultatoPesoPagamento = $conn19->query($queryPesoPagamento);
            if (($risultatoPesoPagamento->num_rows) > 0) {
                $rigaPesoPagamento = $risultatoPesoPagamento->fetch_array();
                $pesoPagamento = $rigaPesoPagamento[0];
            }
        }



        $queryFaseLuce = "SELECT fase FROM `vivigasStatoLuce` WHERE id='$idStatoLuce'";
        $risultatoFaseLuce = $conn19->query($queryFaseLuce);
        $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
        $faseLuce = $rigaFaseLuce[0];

        $queryFaseGas = "SELECT fase FROM `vivigasStatoGas` WHERE id='$idStatoGas'";
        $risultatoFaseGas = $conn19->query($queryFaseGas);
        $rigaFaseGas = $risultatoFaseGas->fetch_array();
        $faseGas = $rigaFaseGas[0];

        $queryFasePDA = "SELECT fase FROM `vivigasStatoPDA` WHERE id='$idStatoPda'";
        $risultatoFasePDA = $conn19->query($queryFasePDA);
        $rigaFasePDA = $risultatoFasePDA->fetch_array();
        $fasePDA = $rigaFasePDA[0];

        if ($fasePDA == "") {
            
        } else {
            $pesoTotaleLordo = $pesoComodity + $pesoInvio + $pesoPagamento;
            if ($comodity == "DUAL") {
                $pezzoLordo = 2;
            } else {
                $pezzoLordo = 1;
            }
        }


        if ($mese >= '2025-02-01') {
             $pesoSanataBo = 0;
        } else {
            $pesoSanataBo = 0;
            $tipoSanataBo = 0;
            if ($sanataBo == "SI") {
                $querySanataBo = "SELECT peso,tipoDetrazione FROM `vivigasPesiSanata` WHERE tipoCampagna='$tipoCampagna' AND valore='$sanataBo' AND dataInizioValidita='$mese' ";
                //echo $querySanataBo;
                $risultatoPesoSanato = $conn19->query($querySanataBo);
                if (($risultatoPesoSanato->num_rows) > 0) {
                    $rigaPesoSanato = $risultatoPesoSanato->fetch_array();
                    $pesoSanataBo = $rigaPesoSanato[0];
                    $tipoSanataBo = $rigaPesoSanato[1];
//                echo $pesoSanataBo;
//                echo "<br>";
                }
            }



            if ($tipoSanataBo == 1) {
                $pesoTotaleLordo = $pesoTotaleLordo - $pesoSanataBo;
            } elseif ($tipoSanataBo == 2) {
                $pesoTotaleLordo = $pesoTotaleLordo - ($pesoTotaleLordo * $pesoSanataBo);
            }
        }

        $pesoTotaleNetto = 0;
        if ($faseGas == "OK" && $faseLuce == "OK" && $comodity == "DUAL") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        } elseif ($faseLuce == "OK" && $comodity == "Luce") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        } elseif ($faseGas == "OK" && $comodity == "Gas") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
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
        }

        $fasePost = "KO";
        $statoPost = "";
        if ($faseGas == $faseLuce && $comodity == "DUAL") {
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

        $queryInserimentoSecondario = "INSERT INTO `aggiuntaVivigas`(`id`, `tipoCampagna`, `pesoComodity`, `pesoInvio`, `pesoMPagamento`, `totalePesoLordo`,"
                . " `faseLuce`, `faseGas`, `totalePesoNetto`,mese,fasePDA,pesoTotalePagato,idRigaLuce,idRigaGas,"
                . "codiceFornituraLuce,codiceFornituraGas,pesoFormazione,pezzoLordo,pezzoNetto,PezzoPagato,PezzoFormazione,fasePost,statoPost) "
                . "VALUES ('$indiceContratto','$tipoCampagna','$pesoComodity','$pesoInvio','$pesoPagamento','$pesoTotaleLordo',"
                . "'$faseLuce','$faseGas','$pesoTotaleNetto','$mese','$fasePDA','$pesoTotalePagato','$idRigaLuce','$idRigaGas',"
                . "'$codiceFornituraLuce','$codiceFornituraGas','$pesoFormazione','$pezzoLordo','$pezzoNetto','$pezzoPagato','$pezzoFormazione','$fasePost','$statoPost')";
        //echo $queryInserimentoSecondario;
        $conn19->query($queryInserimentoSecondario);

//        if ($mese >= "2023-02-01") {
//            $queryUpdatePesi = "UPDATE `vtiger_vivigascf` SET cf_3761='$pesoTotaleLordo',cf_3765='$pesoTotalePagato' WHERE vivigasid='$pratica'";
//            //echo $queryUpdatePesi;
//            $connCrm->query($queryUpdatePesi);
//        }
    }
    $dataFine = date('Y-m-d H:i:s');
    $queryFineLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$dataFine','$provenienza','Fine Import da $provenienza',1,'$idStato')";
    $conn19->query($queryFineLog);
//    header("location:../pannello.php");
}

/**
 * Inizio prelievo dati da crm2 tabella Vivigas
 */



