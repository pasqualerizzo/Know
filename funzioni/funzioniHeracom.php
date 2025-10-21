<?php


/**
 * 
 * @param type $_conn
 * @param type $_statoPda
 * @return type
 */

function truncateHeracom($_conn) {
    $queryTruncate = "DELETE FROM heracom WHERE data>='2025-01-01'";
    $_conn->query($queryTruncate);
}

function truncateAggiuntaHeracom($_conn) {
    $queryTruncate2 = "DELETE FROM `aggiuntaHeracom` WHERE mese>='2025-01-01'";
    $_conn->query($queryTruncate2);
}

function truncateSincroHeracom($_conn) {
    $queryTruncate = "DELETE FROM sincroHeracom ";
    $_conn->query($queryTruncate);
}

function idStatoPdaHeracom($_conn, $_statoPda) {
    $queryStatoPda = "SELECT * FROM `heracomStatoPDA` where descrizione='$_statoPda'";
    try {
        $risultatoStatoPda = $_conn->query($queryStatoPda);
        $conteggioStatoPda = $risultatoStatoPda->num_rows;
        if ($conteggioStatoPda == 0) {
            $queryInserimentoStatoPda = "INSERT INTO `heracomStatoPDA`(`descrizione`) VALUES ('$_statoPda')";
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
function idStatoLuceHeracom($_conn, $_statoLuce) {
    try {
        $queryStatoLuce = "SELECT * FROM `heracomStatoLuce` where descrizione='$_statoLuce'";
        $risultatoStatoLuce = $_conn->query($queryStatoLuce);
        $conteggioStatoLuce = $risultatoStatoLuce->num_rows;
        if ($conteggioStatoLuce == 0) {
            $queryInserimentoStatoLuce = "INSERT INTO `heracomStatoLuce`( `descrizione`) VALUES ('$_statoLuce')";
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
function idStatoGasHeracom($_conn, $_statoGas) {
    try {
        $queryStatoGas = "SELECT * FROM `heracomStatoGas` where descrizione='$_statoGas'";
        $risultatoStatoGas = $_conn->query($queryStatoGas);
        $conteggioStatoGas = $risultatoStatoGas->num_rows;
        if ($conteggioStatoGas == 0) {
            $queryInserimentoStatoGas = "INSERT INTO `heracomStatoGas`( `descrizione`) VALUES ('$_statoGas')";
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

function campagnaHeracom($_conn, $_codiceCampagna) {
   try{
    $queryCampagna = "SELECT tipo FROM `heracomCampagna` where nome='$_codiceCampagna'";
    $risultatoCampagna = $_conn->query($queryCampagna);
    $conteggioCampagna = $risultatoCampagna->num_rows;
    if ($conteggioCampagna == 0) {
        $queryInserimentoCampagna = "INSERT INTO `heracomCampagna`( `nome`) VALUES ('$_codiceCampagna')";
        $_conn->query($queryInserimentoCampagna);
        $idCampagna = "standard";
    } else {
        $rigaCampagna = $risultatoCampagna->fetch_array();
        $idCampagna = $rigaCampagna[0];
            }
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    return $idCampagna;
}



function tipoCampagnaHeracom($_conn, $_codiceCampagna) {
    try{
        $queryCampagna = "SELECT * FROM `heracomCampagna` where nome='$_codiceCampagna'";
    $risultatoCampagna = $_conn->query($queryCampagna);
    $conteggioCampagna = $risultatoCampagna->num_rows;
    if ($conteggioCampagna == 0) {
        $queryInserimentoCampagna = "INSERT INTO `heracomCampagna`( `nome`) VALUES ('$_codiceCampagna')";
        $_conn->query($queryInserimentoCampagna);
        $idCampagna = $conn19->insert_id;
    } else {
        $rigaCampagna = $risultatoCampagna->fetch_array();
        $idCampagna = $rigaCampagna[0];
        $tipoCampagna = $rigaCampagna[2];
    }
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
    return $tipoCampagna;
}

/*
 * Funzioni legate al log
 */

//function scriviLog($_conn, $_data, $_provenienza, $_descrizione, $_stato, $_idStato) {
//    $queryLog = "INSERT INTO `logImport`(`datImport`, `provenienza`, `descrizione`,stato,idStato) VALUES ('$_data','$_provenienza','$_descrizione ,'$_stato','$_idStato')";
//    try {
//        $_conn->query($queryLog);
//    } catch (Exception $ex) {
//        return $ex->getMessage();
//    }
//    return true;
//}


?>

