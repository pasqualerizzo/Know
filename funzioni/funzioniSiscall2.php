<?php

function sedeOperatore($_conn, $_operatore) {
    $query = "SELECT "
            . "territory AS citta "
            . "FROM "
            . " vicidial_users AS operatore  "
            . "WHERE  "
            . " operatore.full_name ='$_operatore' ";
    //echo $query;
    try {
        $risultato = $_conn->query($query);
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
    if (($risultato->num_rows) > 0) {
        $riga = $risultato->fetch_array();
        $sede = $riga[0];
    } else {
        $sede = "-";
    }
    return $sede;
}
