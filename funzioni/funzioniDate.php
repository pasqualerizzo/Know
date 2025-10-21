<?php

function ultimoGiornoMese($_giornoRiferimento) {
    $date = new DateTime($_giornoRiferimento);
    $date->modify('last day of this month');
    $lastDay = $date->format('Y-m-d');

    return $lastDay;
}

function giorniLavoratiMese($_giornoValutazione, $_valoreSabato) {
    $inizioMese = date('Y-m-1', strtotime($_giornoValutazione));
    $giornoRiferimento = $inizioMese;
    $somma = 0;
    while ($giornoRiferimento <= $_giornoValutazione) {
        $giornoSettimana = date('w', strtotime($giornoRiferimento));
        switch ($giornoSettimana) {
            case 0:
                $somma += 0;
                $giornoRiferimento = date('Y-m-d', strtotime($giornoRiferimento . " + 1 days"));
                break;
            case 6:
                $somma += $_valoreSabato;
                $giornoRiferimento = date('Y-m-d', strtotime($giornoRiferimento . " + 1 days"));
                break;
            default:
                $somma += 1;
                $giornoRiferimento = date('Y-m-d', strtotime($giornoRiferimento . " + 1 days"));
                break;
        }
    }
    return $somma;
}


?>
