<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Europe/Rome');

require "/Applications/MAMP/htdocs/Know/connessione/connessione_msqli_vici.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
require "/Applications/MAMP/htdocs/Know/connessione/connessioneCrmNuovo.php";
require "/Applications/MAMP/htdocs/Know/funzioni/funzioniPlenitude.php";

$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$objCrm = new ConnessioneCrmNuovo();
$connCrm = $objCrm->apriConnessioneCrmNuovo();

$queryRicerca = "SELECT "
        . "date_format(enel.datasottoscrizionecontratto,'%d-%m-%Y') as 'dataStipula', "
        . "enel.commodity as 'comodity', "
        . "enel.codicefiscale as 'codiceFiscale', "
        . "enel.tariffaluce as 'offertaLuce', "
        . "enel.tariffagas as 'offertaGas', "
        . "enel.metodopagamento as 'Metodo Pagamento', "
        . "enel.statopda as 'Stato PDA', "
        . "enel.statoplicoluce as 'Stato Luce', "
        . "enel.statoplicogas as 'Stato Gas', "
        . "enel.irenid AS 'pratica', "
        . "enel.noteplicoluce AS 'Note Stato Luce', "
        . "enel.noteplicogas AS 'Note Stato Gas' "
        . "FROM "
        . "vtiger_irencf as enelcf "
        . "inner join vtiger_iren as enel on enelcf.irenid=enel.irenid "
        . "inner join vtiger_crmentity as entity on enel.irenid=entity.crmid "
        . "inner join vtiger_users as operatore on entity.smownerid=operatore.id "
        . "WHERE "
        . "enel.datasottoscrizionecontratto >'2025-01-31' and  entity.deleted=0 and enel.statopda<>'Annullata' and enel.commodity <>'Polizza'";

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
    //truncateSincroEnel($conn19);


    $queryTruncate = "TRUNCATE TABLE `sincroIren`";
    $conn19->query($queryTruncate);

    /**
     * Recupero informazione CRM
     */
    while ($riga = $risultato->fetch_array()) {

        $dataStipula = date('Y-m-d', strtotime(strtr($riga['dataStipula'], '/', '-')));
        $comodity = $riga['comodity'];
        $metodoPagamento = $riga['Metodo Pagamento'];
        $statoPDA = $riga['Stato PDA'];
        $statoLuce = $conn19->real_escape_string($riga['Stato Luce']);
        $statoGas = $conn19->real_escape_string($riga['Stato Gas']);
        $pratica = $riga['pratica'];
        $noteStatoLuce = $conn19->real_escape_string($riga['Note Stato Luce']);
        $noteStatoGas = $conn19->real_escape_string($riga['Note Stato Gas']);
        $codiceFiscale = $riga['codiceFiscale'];
        $offertaLuce = $riga['offertaLuce'];
        $offertaGas = $riga['offertaGas'];

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

        $queryFasePDA = "SELECT fase FROM `irenStatoPDA` WHERE id='$idStatoPda'";
        $risultatoFasePDA = $conn19->query($queryFasePDA);
        $rigaFasePDA = $risultatoFasePDA->fetch_array();
        $fasePDA = $rigaFasePDA[0];

        switch ($comodity) {
            case 'Luce':
                $statoPost = $statoLuce;
                $noteStato = $noteStatoLuce;
                $offerta = $offertaLuce;

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

                $queryFaseLuce = "SELECT fase FROM `irenStatoLuce` WHERE id='$idStatoLuce'";
                $risultatoFaseLuce = $conn19->query($queryFaseLuce);
                $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
                $fase = $rigaFaseLuce[0];
                break;
            case 'Gas':
                $statoPost = $statoGas;
                $noteStato = $noteStatoGas;
                $offerta = $offertaGas;

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

                $queryFaseGas = "SELECT fase FROM `irenStatoGas` WHERE id='$idStatoGas'";
                $risultatoFaseGas = $conn19->query($queryFaseGas);
                $rigaFaseGas = $risultatoFaseGas->fetch_array();
                $fase = $rigaFaseGas[0];
                break;
            case 'Polizza':
                if ($statoLuce !== "") {
                    $statoPost = $statoLuce;
                    $noteStato = $noteStatoLuce;
                    $offerta = $offertaLuce;

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

                    $queryFaseLuce = "SELECT fase FROM `irenStatoLuce` WHERE id='$idStatoLuce'";
                    $risultatoFaseLuce = $conn19->query($queryFaseLuce);
                    $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
                    $fase = $rigaFaseLuce[0];
                } else {
                    $statoPost = $statoGas;
                    $noteStato = $noteStatoGas;
                    $offerta = $offertaGas;

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

                    $queryFaseGas = "SELECT fase FROM `irenStatoGas` WHERE id='$idStatoGas'";
                    $risultatoFaseGas = $conn19->query($queryFaseGas);
                    $rigaFaseGas = $risultatoFaseGas->fetch_array();
                    $fase = $rigaFaseGas[0];
                }
                break;
            case 'Dual':
                if ($statoLuce !== "") {
                    $statoPost = $statoLuce;
                    $noteStato = $noteStatoLuce;
                    $offerta = $offertaLuce;

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

                    $queryFaseLuce = "SELECT fase FROM `irenStatoLuce` WHERE id='$idStatoLuce'";
                    $risultatoFaseLuce = $conn19->query($queryFaseLuce);
                    $rigaFaseLuce = $risultatoFaseLuce->fetch_array();
                    $fase = $rigaFaseLuce[0];
                } else {
                    $statoPost = $statoGas;
                    $noteStato = $noteStatoGas;
                    $offerta = $offertaGas;

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

                    $queryFaseGas = "SELECT fase FROM `irenStatoGas` WHERE id='$idStatoGas'";
                    $risultatoFaseGas = $conn19->query($queryFaseGas);
                    $rigaFaseGas = $risultatoFaseGas->fetch_array();
                    $fase = $rigaFaseGas[0];
                }
                break;
        }
        $mandato = "Enel";
        $dataImport = date("Y-m-d h:m:i");

        $queryInserimento = "INSERT INTO `sincroIren`"
                . "( `dataStipula`, `comodity`,  `metodoPagamento`, `statoPDA`, `statoPost`, `noteStato`, `dataImport`, `mandato`,codiceFiscale,pratica,offerta) "
                . " VALUES "
                . " ('$dataStipula','$comodity','$metodoPagamento','$fasePDA','$fase','$noteStato','$dataImport','$mandato','$codiceFiscale','$pratica','$offerta')";

        $conn19->query($queryInserimento);
    }
}



    
