<?php

/*
 * Enel_Out
 */

/**
 * Funzione che svuota le tabelle del database che contiene i dati per enel_out
 * 
 * @param resource $_conn connessione database di magiPunti
 * @return object data dell'operazione o errore
 */
function truncateEnelOut($_conn) {
    $queryTruncate = "TRUNCATE TABLE `enelOut`";
    try {
        $_conn->query($queryTruncate);
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    $queryTruncate2 = "TRUNCATE TABLE `aggiuntaEnelOut`";
    try {
        $_conn->query($queryTruncate2);
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    return date('Y-m-d H:i:s');
}

/**
 * Funzione aggiornamento peso delle pratiche nel CRM
 * 
 * @param resource $_conn connessione al crm2
 * @param data $_mese mese di riferimento della pratica
 * @param float $_pesoTotaleLordo valore lordo della pratica
 * @param float $_pesoTotalePagato valore netto della pratica
 * @param string $_pratica numero della pratica da aggiornare
 * @return bool
 */
function aggiornaPesiCrmEnelOut($_conn, $_mese, $_pesoTotaleLordo, $_pesoTotalePagato, $_pratica) {
    $controllo = date('Y-m-1', strtotime('-3 months'));
    if ($controllo <= $_mese) {
        $queryUpdatePesi = "UPDATE `vtiger_eneloutcf` SET cf_3775='$_pesoTotaleLordo',cf_3777='$_pesoTotalePagato' WHERE eneloutid='$_pratica'";
        try {
            $_conn->query($queryUpdatePesi);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        return true;
    }
}

/**
 * 
 * @param type $_conn
 * @param type $_statoPda
 * @return type
 */
function idStatoPdaEnelOut($_conn, $_statoPda) {
    $queryStatoPda = "SELECT * FROM `enelOutStatoPDA` where descrizione='$_statoPda'";
    try {
        $risultatoStatoPda = $_conn->query($queryStatoPda);
        $conteggioStatoPda = $risultatoStatoPda->num_rows;
        if ($conteggioStatoPda == 0) {
            $queryInserimentoStatoPda = "INSERT INTO `enelOutStatoPDA`(`descrizione`) VALUES ('$_statoPda')";
            $_conn->query($queryInserimentoStatoPda);
            $idStatoPda = $_conn->insert_id;
        } else {
            $rigaStatoPda = $risultatoStatoPda->fetch_array();
            $idStatoPda = $rigaStatoPda[0];
        }
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    return $idStatoPda;
}

/**
 * 
 * @param type $_conn
 * @param type $_statoLuce
 * @return type
 */
function idStatoLuceEnelOut($_conn, $_statoLuce) {
    try {
        $queryStatoLuce = "SELECT * FROM `enelOutStatoLuce` where descrizione='$_statoLuce'";
        $risultatoStatoLuce = $_conn->query($queryStatoLuce);
        $conteggioStatoLuce = $risultatoStatoLuce->num_rows;
        if ($conteggioStatoLuce == 0) {
            $queryInserimentoStatoLuce = "INSERT INTO `enelOutStatoLuce`( `descrizione`) VALUES ('$_statoLuce')";
            $_conn->query($queryInserimentoStatoLuce);
            $idStatoLuce = $_conn->insert_id;
        } else {
            $rigaStatoLuce = $risultatoStatoLuce->fetch_array();
            $idStatoLuce = $rigaStatoLuce[0];
        }
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    return $idStatoLuce;
}

/**
 * 
 * @param type $_conn
 * @param type $_statoGas
 * @return type
 */
function idStatoGasEnelOut($_conn, $_statoGas) {
    try {
        $queryStatoGas = "SELECT * FROM `enelOutStatoGas` where descrizione='$_statoGas'";
        $risultatoStatoGas = $_conn->query($queryStatoGas);
        $conteggioStatoGas = $risultatoStatoGas->num_rows;
        if ($conteggioStatoGas == 0) {
            $queryInserimentoStatoGas = "INSERT INTO `enelOutStatoGas`( `descrizione`) VALUES ('$_statoGas')";
            $_conn->query($queryInserimentoStatoGas);
            $idStatoGas = $_conn->insert_id;
        } else {
            $rigaStatoGas = $risultatoStatoGas->fetch_array();
            $idStatoGas = $rigaStatoGas[0];
        }
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    return $idStatoGas;
}

function idCampagnaEnelOut($_conn, $_codiceCampagna) {
    $queryCampagna = "SELECT * FROM `enelOutCampagna` where nome='$codiceCampagna'";
    $risultatoCampagna = $conn19->query($queryCampagna);
    $conteggioCampagna = $risultatoCampagna->num_rows;
    if ($conteggioCampagna == 0) {
        $queryInserimentoCampagna = "INSERT INTO `enelOutCampagna`( `nome`) VALUES ('$codiceCampagna')";
        $conn19->query($queryInserimentoCampagna);
        $idCampagna = $conn19->insert_id;
    } else {
        $rigaCampagna = $risultatoCampagna->fetch_array();
        $idCampagna = $rigaCampagna[0];
        $tipoCampagna = $rigaCampagna[2];
    }
}



function tipoCAmpagnaEnelOut($_conn, $_codiceCampagna) {
    $queryCampagna = "SELECT * FROM `enelOutCampagna` where nome='$codiceCampagna'";
    $risultatoCampagna = $conn19->query($queryCampagna);
    $conteggioCampagna = $risultatoCampagna->num_rows;
    if ($conteggioCampagna == 0) {
        $queryInserimentoCampagna = "INSERT INTO `enelOutCampagna`( `nome`) VALUES ('$codiceCampagna')";
        $conn19->query($queryInserimentoCampagna);
        $idCampagna = $conn19->insert_id;
    } else {
        $rigaCampagna = $risultatoCampagna->fetch_array();
        $idCampagna = $rigaCampagna[0];
        $tipoCampagna = $rigaCampagna[2];
    }
}

/*
 * Funzioni legate al log
 */

function scriviLog($_conn, $_data, $_provenienza, $_descrizione, $_stato, $_idStato) {
    $queryLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$_data','$_provenienza','$_descrizione ,'$_stato','$_idStato')";
    try {
        $_conn->query($queryLog);
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    return true;
}

function idStato($_conn) {
    $queryIdStato = "SELECT max(idStato) FROM `logImport`";
    try {
        $risultatoIdStato = $_conn->query($queryIdStato);
        $rigaStato = $risultatoIdStato->fetch_array();
        $idStato = $rigaStato[0] + 1;
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    return $idStato;
}
?>

