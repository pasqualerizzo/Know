<?php

function truncatePlenitude($_conn) {
    //$queryTruncate = "TRUNCATE TABLE `plenitude`";
    $queryTruncate = "DELETE FROM plenitude WHERE data>='2025-08-01'";
    $_conn->query($queryTruncate);
}

function truncateSincroPlenitude($_conn) {
    $queryTruncate = "TRUNCATE TABLE `sincroPlenitude`";
    $_conn->query($queryTruncate);
}



function truncateSincroVivigas($_conn) {
    $queryTruncate = "TRUNCATE TABLE `sincroVivigas`";
    $_conn->query($queryTruncate);
}

function truncateAggiuntaPlenitude($_conn) {
    $queryTruncate2 = "DELETE FROM `aggiuntaPlenitude` WHERE mese>='2025-08-01'";
    //$queryTruncate2 = "TRUNCATE TABLE `aggiuntaPlenitude`";
    $_conn->query($queryTruncate2);
}


function truncateSincronizzazione($_conn) {
    $queryTruncate2 = "TRUNCATE TABLE `sincronizzazione`";
    $_conn->query($queryTruncate2);
}
function arrayStatoPda($_conn) {
    $risposta = [];
    $queryStatoPda = "SELECT * FROM `plenitudeStatoPDA`";
    $risultatoStatoPda = $_conn->query($queryStatoPda);
    while ($riga = $risultatoStatoPda->fetch_array()) {
        $id = $riga[0];
        $statoPda = $riga[1];
        $fase = $riga[2];
        $risposta[$statoPda] = [$id, $fase];
    }
    return $risposta;
}

function aggiuntaStatoPda($_conn, $_statoPda) {
    $queryInserimentoStatoPda = "INSERT INTO `plenitudeStatoPDA`(`descrizione`) VALUES ('$_statoPda')";
    $_conn->query($queryInserimentoStatoPda);
}

function arrayStatoLuce($_conn) {
    $risposta = [];
    $queryStato = "SELECT * FROM `plenitudeStatoLuce`";
    $risultatoStato = $_conn->query($queryStato);
    while ($riga = $risultatoStato->fetch_array()) {
        $id = $riga[0];
        $stato = $riga[1];
        $fase = $riga[2];
        $risposta[$stato] = [$id, $fase];
    }
    return $risposta;
}

function aggiuntaStatoLuce($_conn, $_statoLuce) {
    $queryInserimento = "INSERT INTO `plenitudeStatoLuce`(`descrizione`) VALUES ('$_statoLuce')";
    $_conn->query($queryInserimento);
}

function arrayStatoGas($_conn) {
    $risposta = [];
    $queryStato = "SELECT * FROM `plenitudeStatoGas`";
    $risultatoStato = $_conn->query($queryStato);
    while ($riga = $risultatoStato->fetch_array()) {
        $id = $riga[0];
        $stato = $riga[1];
        $fase = $riga[2];
        $risposta[$stato] = [$id, $fase];
    }
    return $risposta;
}

function aggiuntaStatoGas($_conn, $_statoGas) {
    $queryInserimento = "INSERT INTO `plenitudeStatoGas`(`descrizione`) VALUES ('$_statoGas')";
    $_conn->query($queryInserimento);
}

function arrayCampagna($_conn) {
    $risposta = [];
    $queryStato = "SELECT * FROM `plenitudeCampagna`";
    $risultatoStato = $_conn->query($queryStato);
    while ($riga = $risultatoStato->fetch_array()) {
        $id = $riga[0];
        $stato = $riga[1];
        $fase = $riga[2];
        $risposta[$stato] = [$id, $fase];
    }
    return $risposta;
}

function aggiuntaCampagna($_conn, $_codiceCampagna) {
    $queryInserimento = "INSERT INTO plenitudeCampagna(`nome`) VALUES ('$_codiceCampagna')";
    $_conn->query($queryInserimento);
}

function arrayPesiComodity($_conn) {
    $risposta = [];
    $query = "SELECT * FROM `plenitudePesiComoditi`";
    $risultato = $_conn->query($query);
    if ($risultato) {
        while ($riga = $risultato->fetch_array()) {
            $dataValidita = $riga[1];
            $tipoAcquisizione = $riga[2] ?? "vuoto";
            $tipoCampagna = $riga[3] ?? "vuoto";
            $valore = $riga[4];
            $peso = $riga[5];
            $risposta[$dataValidita][$tipoAcquisizione][$tipoCampagna][$valore] = $peso;
        }
    } else {
        echo "Errore nella query: " . $_conn->error;
    }
    return $risposta;
}

function arrayMacroStato($_conn) {
    $risposta = [];
    $queryStato = "SELECT * FROM `plenitudeMacroStato`";
    $risultatoStato = $_conn->query($queryStato);
    while ($riga = $risultatoStato->fetch_array()) {
        $id = $riga[0];
        $stato = $riga[1];
        $fase = $riga[2];
        $risposta[$stato] = [$id, $fase];
    }
    return $risposta;
}

function aggiuntaMacroStato($_conn, $_macroStato) {
    $queryRicerca = "SELECT * FROM `plenitudeMacroStato` where descrizione='$_macroStato'";
    $risultato = $_conn->query($queryRicerca);
    if (($risultato->num_rows) > 0) {
        return false;
    } else {
        $queryInserimento = "INSERT INTO plenitudeMacroStato(`descrizione`) VALUES ('$_macroStato')";
        //echo $queryInserimento;
        $_conn->query($queryInserimento);
        return true;
    }
}

?>


