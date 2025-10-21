<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

//require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniPlenitude.php";

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
$queryRicerca = "SELECT 
    operatore.user_name as 'operatore',
        c.plenitude AS plenitude,
e.smownerid AS smownerid,
e.createdtime AS createdtime,
e.modifiedtime AS modifiedtime,
c.plenitudeno AS plenitudeno,
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
c.doisaleup AS doisaleup,
c.privacycodiceinformativa AS privacycodiceinformativa,
c.privacydatasottoscrizione AS privacydatasottoscrizione,
c.channel AS channel,
c.touchpoint AS touchpoint,
c.prodotto AS prodotto,
c.orariodoc AS orariodoc,
c.descrizioneesito AS descrizioneesito,
c.resultcode AS resultcode,
c.resultcodedichiarato AS resultcodedichiarato,
c.resultcodelavorato AS resultcodelavorato,
e.modifiedby AS modifiedby,
cf.cf_1595 AS moroso,
cf.cf_1597 AS processowinback,
e.smcreatorid AS smcreatorid,
cf.cf_1617 AS codicematricole,
c.datacontratto AS datacontratto,
c.plenitudeid AS plenitudeid 


FROM 
        vtiger_plenitudecf as cf 
        inner join vtiger_plenitude as c on cf.plenitudeid=c.plenitudeid 
        inner join vtiger_crmentity as e on c.plenitudeid=e.crmid 
        inner join vtiger_users as operatore on e.smownerid=operatore.id 
        WHERE 
        c.datasottoscrizionecontratto >'2025-08-01' and  e.deleted=0 and c.statopda<>'Annullata'";

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
        $pesoTotaleNetto = 0;

        $user = $riga['operatore'];
        $data = date('Y-m-d', strtotime(strtr($riga['datasottoscrizionecontratto'], '/', '-')));
        $comodity = $riga['commodity'];
        $mercato = $riga['mercato'];
        $sede = $riga['sede'];
        $metodoPagamento = $riga['metodopagamento'];
        $metodoInvio = $riga['metodoinviofattura'];
        $statoPDA = $riga['statopda'];
        $statoLuce = $conn19->real_escape_string($riga['statoplicoluce']);
        $statoGas = $conn19->real_escape_string($riga['statoplicogas']);
        $winback = "no";
        $dataCreazione = $riga['createdtime'];
        $codiceCampagna = $riga['codicecampagna'];
        $pratica = $riga['plenitudeid'];
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
                . "( `creatoDa`, `data`, `comodity`, `mercato`, `sede`, `metodoPagamento`, `statoPDA`, `statoLuce`, `statoGas`, `dataImport`, `mandato`, "
                . "idStatoPda, idStatoLuce, idStatoGas, winback, idMandato, dataCreazione, idCampagna, campagna, metodoInvio, pratica, sanataBo, tipoAcquisizione, idGestioneLead, leadId, codMaticola, "
                . " `dataSwitchInLuce`, `dataSwitchOutLuce`, `dataSwitchInGas`, `dataSwitchOutGas`, deltaMortalitaLuce, deltaMortalitaGas, noteStatoLuce, noteStatoGas, utm)"
                . " VALUES "
                . " ('$user', '$data', '$comodity', '$mercato', '$sede', '$metodoPagamento', '$statoPDA', '$statoLuce', '$statoGas', '$dataImport', '$mandato', "
                . " '$idStatoPda', '$idStatoLuce', '$idStatoGas', '$winback', '$idMandato', '$dataCreazione', '$idCampagna', '$codiceCampagna', '$metodoInvio', '$pratica', '$sanataBo', '$tipoAcquisizione', '$idGestioneLead', '$leadId', '$codMatricola', "
                . " '$dataSwitchInLuce', '$dataSwitchOutLuce', '$dataSwitchInGas', '$dataSwitchOutGas', '$delta', '$delta', '$noteStatoLuce', '$noteStatoGas', '$utm')";
// echo $queryInserimento;
        $conn19->query($queryInserimento);
        /*
         * Aggiunta per calcolo pesi
         */
        $indiceContratto = $conn19->insert_id;
        $pesoComodity = 0;

        $pesoTotaleLordo = 0;
        $mese = date('Y-m-01', strtotime($data));

        $queryFormazione = "SELECT ore FROM `formazioneTotale` where nomeCompleto = '$user' and giorno = '$data'";
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
            $fasePost = $conn19->real_escape_string($faseGas);
            $statoPost = $conn19->real_escape_string($statoGas);
        } elseif ($comodity == "Luce") {
            $fasePost = $conn19->real_escape_string($faseLuce);
            $statoPost = $conn19->real_escape_string($statoLuce);
        } elseif ($comodity == "Gas") {
            $fasePost = $conn19->real_escape_string($faseGas);
            $statoPost = $conn19->real_escape_string($statoGas);
        } elseif ($statoLuce <> "" && $comodity == "Polizza") {
            $fasePost = $conn19->real_escape_string($faseLuce);
            $statoPost = $conn19->real_escape_string($statoLuce);
        } elseif ($statoGas <> "" && $comodity == "Polizza") {
            $fasePost = $conn19->real_escape_string($faseGas);
            $statoPost = $conn19->real_escape_string($statoGas);
        }

        $fasePost = str_replace("'", "''", $fasePost);

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
                . "`(`id`, `tipoCampagna`, `pesoComodity`, `pesoInvio`, `pesoMPagamento`, `totalePesoLordo`, `faseLuce`, `faseGas`, `totalePesoNetto`, "
                . "mese, fasePDA, pesoTotalePagato, codicePlicoLuce, codicePlicoGas, pesoFormazione, pezzoLordo, pezzoNetto, pezzoPagato, pezzoFormazione, fasePost, statoPost, faseMacroStatoLuce, faseMacroStatoGas) "
                . "VALUES "
                . " ('$indiceContratto', '$tipoCampagna', '$pesoComodity', '$pesoInvio', '$pesoPagamento', '$pesoTotaleLordo', '$faseLuce', '$faseGas', '$pesoTotaleNetto', "
                . "'$mese', '$fasePDA', '$pesoTotalePagato', '$codicePlicoLuce', '$codicePlicoGas', '$pesoFormazione', '$pezzoLordo', '$pezzoNetto', '$pezzoPagato', '$pezzoFormazione', '$fasePost', '$statoPost', '$faseMacroStatoLuce', '$faseMacroStatoGas')";

        try {
            $conn19->query($queryInserimentoSecondario);
        } catch (exception $e) {
            echo $e;
            echo $queryInserimentoSecondario;
        }
    }
    $dataFine = date('Y-m-d H:i:s');

//header("location:../pannello.php");
}


    
