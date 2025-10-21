
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";
require "/Applications/MAMP/htdocs/Know/funzione/funzioneCrm.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniHeracom.php";

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
$provenienza = "heracom";
$tipoCampagna = "";
$mandato = "Heracom";
$idMandato = 40;

$queryRicerca = "SELECT
	operatore.user_name AS 'operatore',
	c.heracomid AS id,
	c.heracom AS heracom,
	e.smownerid AS smownerid,
	e.createdtime AS createdtime,
	e.modifiedtime AS modifiedtime,
	c.heracomno AS heracomno,
	c.sede AS sede,
	c.datasottoscrizionecontratto AS datasottoscrizionecontratto,
	c.commodity AS commodity,
	c.nome AS nome,
	c.cognome AS cognome,
	c.ragionesociale AS ragionesociale,
	c.codicefiscale AS codicefiscale,
	c.cellulareprimario AS cellulareprimario,
	c.telefono AS telefono,
	c.pod AS pod,
	c.codicepdr AS codicepdr,
	c.tariffaluce AS tariffaluce,
	c.tariffagas AS tariffagas,
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
	c.consensorisparmiami AS consensorisparmiami,
	e.modifiedby AS modifiedby,
	e.smcreatorid AS smcreatorid,
	c.datacontratto AS datacontratto,
	c.codiceconsenso AS codiceconsenso,
	c.campagnaprovenienzaheracom AS campagnaprovenienzaheracom
FROM
	vtiger_heracomcf AS he
	INNER JOIN vtiger_heracom AS c ON c.heracomid = he.heracomid
	INNER JOIN vtiger_crmentity AS e ON c.heracomid = e.crmid
	INNER JOIN vtiger_users AS operatore ON e.smownerid = operatore.id
	WHERE 
	e.deleted=0 
	";

$risultato = $connCrm->query($queryRicerca);
/**
 * Se la ricerca da errore segna nei log l'errore
 */
if (!$risultato) {
    $dataErrore = date('Y-m-d H:i:s');
    $errore = $connCrm->real_escape_string($connCrm->error);
    scriviLog($conn19, $dataErrore, $provenienza, $errore, 0, $idStato);
} else {
    truncateHeracom($conn19);
    truncateAggiuntaHeracom($conn19);

    while ($riga = $risultato->fetch_array()) {
        $pesoFormazione = 0;
        $pezzoLordo = 0;
        $pezzoNetto = 0;
        $pezzoPagato = 0;
        $pezzoFormazione = 0;

        $operatore = $riga['operatore'];
        $heracom = $riga['heracom'];
        $smownerid = $riga['smownerid'];
        $createdtime = $riga['createdtime'];
        $modifiedtime = $riga['modifiedtime'];
        $heracomno = $riga['heracomno'];
        $sede = $riga['sede'];
        $datasottoscrizionecontratto = $riga['datasottoscrizionecontratto'];
        $commodity = $riga['commodity'];
        $nome = $riga['nome'];
        $cognome = $riga['cognome'];
        $ragionesociale = $riga['ragionesociale'];
        $codicefiscale = $riga['codicefiscale'];
        $cellulareprimario = $riga['cellulareprimario'];
        $telefono = $riga['telefono'];
        $pod = $riga['pod'];
        $codicepdr = $riga['codicepdr'];
        $tariffaluce = $riga['tariffaluce'];
        $tariffagas = $riga['tariffagas'];
        $metodopagamento = $riga['metodopagamento'];
        $iban = $riga['iban'];
        $ragionesocialetitolare = $riga['ragionesocialetitolare'];
        $codicefiscaletitolare = $riga['codicefiscaletitolare'];
        $metodoinviofattura = $riga['metodoinviofattura'];
        $noteoperatore = $riga['noteoperatore'];
        $notebackoffice = $riga['notebackoffice'];
        $statopda = $riga['statopda'];
        $motivazioneko = $riga['motivazioneko'];
        $codiceplicoluce = $riga['codiceplicoluce'];
        $statoplicoluce = $riga['statoplicoluce'];
        $noteplicoluce = $riga['noteplicoluce'];
        $codiceplicogas = $riga['codiceplicogas'];
        $statoplicogas = $riga['statoplicogas'];
        $noteplicogas = $riga['noteplicogas'];
        $consensorisparmiami = $riga['consensorisparmiami'];
        $modifiedby = $riga['modifiedby'];
        $smcreatorid = $riga['smcreatorid'];
        $datacontratto = $riga['datacontratto'];
        $codiceconsenso = $riga['codiceconsenso'];
        $campagnaprovenienzaheracom = $riga['campagnaprovenienzaheracom'];
        $idEntity = $riga['id'];

        $idStatoPda = idStatoPdaHeracom($conn19, $statopda);

        $idStatoLuce = idStatoLuceHeracom($conn19, $statoplicoluce);

        $idStatoGas = idStatoGasHeracom($conn19, $statoplicogas);

        $queryInserimento = "INSERT INTO `heracom`"
                . "(`assegnato`, `data`, `comodity`, `mercato`, `sede`, `metodoPagamento`, `statoPDA`, `statoLuce`, "
                . " `statoGas`, `dataImport`, `mandato`, idStatoPda, idStatoLuce, idStatoGas, winback, idMandato, dataCreazione, "
                . " idCampagna, campagna, metodoInvio, idEntity, dataModifica,codicePlicoLuce,codicePlicoGas )"
                . " VALUES ('$operatore', '$datacontratto', '$commodity', 'consumer', '$sede', '$metodopagamento', '$statopda', '$statoplicoluce', "
                . " '$statoplicogas', '$dataImport', '$mandato', '$idStatoPda', '$idStatoLuce', '$idStatoGas', 'No', 1, '$createdtime', "
                . " 1, '$campagnaprovenienzaheracom', '$metodoinviofattura', '$idEntity', '$modifiedtime','$codiceplicoluce','$codiceplicogas')";
        echo $queryInserimento;
        $conn19->query($queryInserimento);
        $indiceContratto = $conn19->insert_id;

        $pesoComodity = 0.5;
        $pesoInvio = 0;
        $pesoPagamento = 0;
        $pesoAssicurazione = 0;
        $pesoFibra = 0;
        $pesoAggiuntivi = 0;
        $mese = date('Y-m-1', strtotime($datacontratto));

        $queryFormazione = "SELECT ore FROM `formazioneTotale` where nomeCompleto = '$operatore' and giorno = '$datacontratto'";

        $risultatoFormazione = $conn19->query($queryFormazione);
        if (($risultatoFormazione->num_rows) > 0) {
            $rigaFormazione = $risultatoFormazione->fetch_array();
            $ore = $rigaFormazione[0];
            if ($ore < 45) {
                $isFormazione = true;
            } else {
                $isFormazione = false;
            }
        } else {
            $isFormazione = false;
        }

        $tipoCampagna = campagnaHeracom($conn19, $campagnaprovenienzaheracom);

////Peso Commodity
//        $queryPesoComodity = "SELECT peso FROM `heracomPesiComoditi` WHERE tipoCampagna = '$tipoCampagna' AND valore = '$commodity' AND dataInizioValidita = '$mese'";
//        $risultatoPesoComodity = $conn19->query($queryPesoComodity);
//        if (($risultatoPesoComodity->num_rows) > 0) {
//            $rigaPesoComodity = $risultatoPesoComodity->fetch_array();
//            $pesoComodity = $rigaPesoComodity[0];
//        }
//        $queryPesoInvio = "SELECT peso FROM `heracomPesiInvio` WHERE tipoCampagna = '$tipoCampagna' AND valore = '$metodoinviofattura' AND dataInizioValidita = '$mese'";
//        $risultatoPesoInvio = $conn19->query($queryPesoInvio);
//        if (($risultatoPesoInvio->num_rows) > 0) {
//            $rigaPesoInvio = $risultatoPesoInvio->fetch_array();
//            $pesoInvio = $rigaPesoInvio[0];
//        }
//        $queryPesoPagamento = "SELECT peso FROM `heracomPesiMetodoPagamento` WHERE tipoCampagna = '$tipoCampagna' AND valore = '$metodopagamento' AND dataInizioValidita = '$mese'";
//        $risultatoPesoPagamento = $conn19->query($queryPesoPagamento);
//        if (($risultatoPesoPagamento->num_rows) > 0) {
//            $rigaPesoPagamento = $risultatoPesoPagamento->fetch_array();
//            $pesoPagamento = $rigaPesoPagamento[0];
//        }



        switch ($metodopagamento) {
            case "Rid":
                switch ($campagnaprovenienzaheracom) {
                    case "CB_FAM":
                    case "SWO_Standard":
                        $pesoInvio = 0.5;
                        break;
                    default :
                        $pesoInvio = 0;
                        break;
                }
                break;
            default:
                $pesoInvio = 0;
                break;
        }



        $pesoTotaleLordo = $pesoComodity + $pesoInvio + $pesoPagamento;
        if ($commodity == "Dual") {
            $pezzoLordo = 2;
        } else {
            $pezzoLordo = 1;
        }

        $queryFaseLuce = "SELECT fase FROM `heracomStatoLuce` WHERE id = '$idStatoLuce'";
        $risultatoFaseLuce = $conn19->query($queryFaseLuce);
        $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
        $faseLuce = $rigaFaseLuce[0];

        $queryFaseGas = "SELECT fase FROM `heracomStatoGas` WHERE id = '$idStatoGas'";
        $risultatoFaseGas = $conn19->query($queryFaseGas);
        $rigaFaseGas = $risultatoFaseGas->fetch_array();
        $faseGas = $rigaFaseGas[0];

        $queryFasePDA = "SELECT fase FROM `heracomStatoPDA` WHERE id = '$idStatoPda'";
        $risultatoFasePDA = $conn19->query($queryFasePDA);
        $rigaFasePDA = $risultatoFasePDA->fetch_array();
        $fasePDA = $rigaFasePDA[0];

        $pesoTotaleNetto = 0;
        if ($faseGas == "OK" && $faseLuce == "OK" && $commodity == "Dual") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        } elseif ($faseLuce == "OK" && $commodity == "Luce") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        } elseif ($faseGas == "OK" && $commodity == "Gas") {
            $pesoTotaleNetto = $pesoTotaleLordo;
            $pezzoNetto = $pezzoLordo;
        }




        if ($isFormazione == true) {
            $pesoTotalePagato = 0;
            $pesoFormazione = $pesoTotaleNetto;
            $pezzoFormazione = $pezzoNetto;
        } else {
            $pesoFormazione = 0;
            $pesoTotalePagato = $pesoTotaleNetto;
            $pezzoPagato = $pezzoNetto;
        }

        $fasePost = "KO";
        $statoPost = "";
        if ($faseGas == $faseLuce && $commodity == "Dual") {
            $fasePost = $faseGas;
            $statoPost = $statoplicogas;
        } elseif ($commodity == "Luce") {
            $fasePost = $faseLuce;
            $statoPost = $statoplicoluce;
        } elseif ($commodity == "Gas") {
            $fasePost = $faseGas;
            $statoPost = $statoplicogas;
        } elseif ($faseLuce <> null && $commodity == "Polizza") {
            $fasePost = $faseLuce;
            $statoPost = $statoplicoluce;
        } elseif ($faseGas <> null && $commodity == "Polizza") {
            $fasePost = $faseGas;
            $statoPost = $statoplicogas;
        }


        $queryInserimentoSecondario = "INSERT INTO `aggiuntaHeracom`(`id`, `tipoCampagna`, `pesoComodity`, `pesoInvio`, `pesoMPagamento`, `totalePesoLordo`, "
                . " `faseLuce`, `faseGas`, `totalePesoNetto`, mese, fasePDA, pesoTotalePagato, idLuce, idGas, pesoFormazione, "
                . " pezzoLordo, pezzoNetto, pezzoPagato, pezzoFormazione, fasePost, statoPost) "
                . "VALUES ('$indiceContratto', '$tipoCampagna', '$pesoComodity', '$pesoInvio', '$pesoPagamento', '$pesoTotaleLordo', "
                . "'$faseLuce', '$faseGas', '$pesoTotaleNetto', '$mese', '$fasePDA', '$pesoTotalePagato', '$idStatoLuce', '$idStatoGas', '$pesoFormazione', "
                . " '$pezzoLordo', '$pezzoNetto', '$pezzoPagato', '$pezzoFormazione', '$fasePost', '$statoPost')";

        $conn19->query($queryInserimentoSecondario);
    }
}
//update aggiornamento pratiche



$objCrm->chiudiConnessioneCrm();
$obj19->chiudiConnessione();
