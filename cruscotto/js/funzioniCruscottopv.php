<?php

function gruppoSedi($_conn, $_mese = "") {
    if ($_mese == "") {
        $queryGroupSede = "SELECT sede FROM `stringheTotale`  group by sede";
    } else {
        $queryGroupSede = "SELECT sede FROM `stringheTotale` where mese='$_mese' group by sede";
    }
    $risultatoQueryGroupSede = $_conn->query($queryGroupSede);
    //$conteggioSede = $risultatoQueryGroupSede->num_rows;
    $elencoSedi = "";
    while ($rigaSede = $risultatoQueryGroupSede->fetch_array()) {
        $elencoSedi .= '<option value="' . $rigaSede[0] . '">' . $rigaSede[0] . '</option>';
    }
    return $elencoSedi;
}

function gruppoMandato($_conn, $_mese = "") {
    if ($_mese == "") {
        $queryGroupMandato = "SELECT idMandato FROM `stringheTotale`  group by idmandato";
    } else {
        $queryGroupMandato = "SELECT idMandato FROM `stringheTotale` where mese='$_mese' group by idmandato";
    }
    $risultatoQueryGroupMandato = $_conn->query($queryGroupMandato);
    //$conteggioSede = $risultatoQueryGroupSede->num_rows;
    $elencoSedi = "";
    $conteggio = 1;
    while ($rigaSede = $risultatoQueryGroupMandato->fetch_array()) {
        $check = ($conteggio == 1) ? "selected" : "";
        $elencoSedi .= '<option value="' . $rigaSede[0] . ' ' . $check . '">' . $rigaSede[0] . '</option>';
        $conteggio++;
    }
    return $elencoSedi;
}

function contaSedi($_conn, $_mese = "") {
    if ($_mese == "") {
        $queryGroupSede = "SELECT sede FROM `stringheTotale`  group by sede";
    } else {
        $queryGroupSede = "SELECT sede FROM `stringheTotale` where mese='$_mese' group by sede";
    }
    $risultatoQueryGroupSede = $_conn->query($queryGroupSede);
    $conteggioSede = $risultatoQueryGroupSede->num_rows;
    return $conteggioSede;
}

function gruppoOre($_conn) {
    $queryGroupSede = "SELECT mese FROM `stringheTotale`  group by mese";
    $risultatoQueryGroupSede = $_conn->query($queryGroupSede);
    //$conteggioSede = $risultatoQueryGroupSede->num_rows;
    $elencoSedi = "";
    while ($rigaSede = $risultatoQueryGroupSede->fetch_array()) {
        $elencoSedi .= '<option value="' . $rigaSede[0] . '">' . $rigaSede[0] . '</option>';
    }
    return $elencoSedi;
}

function contaOre($_conn) {
    $queryGroupSede = "SELECT mese FROM `stringheTotale`  group by mese";
    $risultatoQueryGroupSede = $_conn->query($queryGroupSede);
    $conteggioSede = $risultatoQueryGroupSede->num_rows;
    return $conteggioSede;
}

