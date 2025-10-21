<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

$oggi = date("Y-m-d");
$giornoSettimana = date("N", strtotime($oggi));
$g1 = 0;
$g2 = 0;
$g3 = 0;
$g4 = 0;
$g5 = 0;

switch (6) {
    case 6:
        $g1 = 1;
        $g2 = 2;
        $g3 = 3;
        $g4 = 4;
        $g5 = 5;
        break;
    case 5:
        $g1 = 1;
        $g2 = 2;
        $g3 = 3;
        $g4 = 4;
        $g5 = 6;
        break;
    case 4:
        $g1 = 1;
        $g2 = 2;
        $g3 = 3;
        $g4 = 5;
        $g5 = 6;
        break;
    case 3:
        $g1 = 1;
        $g2 = 2;
        $g3 = 4;
        $g4 = 5;
        $g5 = 6;
        break;
    case 2:
        $g1 = 1;
        $g2 = 3;
        $g3 = 4;
        $g4 = 5;
        $g5 = 6;
        break;
    case 1:
        $g1 = 2;
        $g2 = 3;
        $g3 = 4;
        $g4 = 5;
        $g5 = 6;
        break;
}

echo date("Y-m-d", strtotime($oggi."-".$g1." days"))."<br>";
echo date("Y-m-d", strtotime($oggi."-".$g2." days"))."<br>";
echo date("Y-m-d", strtotime($oggi."-".$g3." days"))."<br>";
echo date("Y-m-d", strtotime($oggi."-".$g4." days"))."<br>";
echo date("Y-m-d", strtotime($oggi."-".$g5." days"))."<br>";






$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$queryMandato = "";
$lunghezza = count($mandato);
$sedePrecedente = "inizio";
$performance = "";
$colorePerformance = "";

if ($lunghezza == 1) {
    $queryMandato .= " AND idMandato='$mandato[0]' ";
} else {
    for ($i = 0;
            $i < $lunghezza;
            $i++) {
        if ($i == 0) {
            $queryMandato .= " AND ( ";
        }
        $queryMandato .= " idMandato='$mandato[$i]' ";
        if ($i == ($lunghezza - 1)) {
            $queryMandato .= " ) ";
        } else {
            $queryMandato .= " OR ";
        }
    }
}

$querySede = "";
$lunghezzaSede = count($sede);

if ($lunghezzaSede == 1) {
    $querySede .= " AND sede='$sede[0]' ";
} else {
    for ($l = 0;
            $l < $lunghezzaSede;
            $l++) {
        if ($l == 0) {
            $querySede .= " AND ( ";
        }
        $querySede .= " sede='$sede[$l]' ";
        if ($l == ($lunghezzaSede - 1)) {
            $querySede .= " ) ";
        } else {
            $querySede .= " OR ";
        }
    }
}

$tipoCampagnaEscluso = "";
$queryGroupMandato = "SELECT nomeCompleto,sede,idMandato,sum(numero)/3600 as ore,sum(pause)/3600 as pause, sum(dispo)/3600 as dispo, sum(dead)/3600 as dead  "
        . "FROM `stringheTotale`  "
        . "where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello=1  "
        . "" . $queryMandato . $querySede . "group by nomeCompleto order by sede,nomeCompleto";
//echo $queryGroupMandato;
$risultatoQueryGroupMandato = $conn19->query($queryGroupMandato);

$html = "<table class='blueTable' id='table-1'>";
$html .= "<thead>";
$html .= "<tr>";
$html .= "<th>Nome</th>";
$html .= "<th>Mandato</th>";
$html .= "<th>Sede</th>";
$html .= "<th></th>";

$html .= "<th>Ore</th>";
$html .= "<th>%Dispo/ore [>=6%]</th>";
$html .= "<th>%Dead/ore [>=3%]</th>";
$html .= "<th>Pausa</th>";
$html .= "<th>% pausa/ore [>=12,5%]</th>";
$html .= "<th>Pezzi Lordo</th>";
$html .= "<th>Pezzo Pagato</th>";
$html .= "<th>Resa pezzi Lordo/ore</th>";
$html .= "<th>Resa pezzi Pagato/ore</th>";
$html .= "<th>Performance</th>";
$html .= "<th>Peso Lordo</th>";
$html .= "<th>Peso Pagato</th>";

$html .= "</tr>";

$html .= "</thead>";

while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {


    $user = $rigaMandato[0];
    $sede = $rigaMandato[1];
    $sedeRicerca = ucwords($sede);
    $idMandato = $rigaMandato[2];

    $ore = $rigaMandato[3];
    $pause = $rigaMandato[4];
    $dispo = $rigaMandato[5];
    $dead = $rigaMandato[6];

    if ($sedePrecedente == "inizio" || $sedePrecedente == $sede) {

        switch ($idMandato) {
            case "Plenitude":
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato) "
                        . " FROM "
                        . "plenitude "
                        . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }

//echo $queryCrm;
                break;

            case "Iren":
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato) "
                        . " FROM "
                        . "iren "
                        . "inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }

//echo $queryCrm;
                break;
            case "Green Network":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                        . "FROM "
                        . "green "
                        . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }

                break;
            case "Vivigas Energia":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino',pezzoLordo,0)),sum(if(metodoPagamento='SSD',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Posta (Residenza)',pezzoLordo,0)),sum(if(metodoInvio='Mail',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato) "
                        . "FROM "
                        . "vivigas "
                        . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }
                break;

            case "enel_out":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                        . "FROM "
                        . "enelOut "
                        . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }

                break;
            case "Vodafone":


                $pesoLordo = 0;
                $pesoPdaOk = 0;
                $pesoPdaKo = 0;
                $pesoPdaBkl = 0;
                $pesoPdaBklp = 0;
                $pezzoLordo = 0;
                $pezzoOk = 0;
                $pezzoKo = 0;
                $pezzoBkl = 0;
                $pezzoBklp = 0;
                $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                $resaValoreLordo = round($pesoLordo / $ore, 2);
                $resaPezzoOk = round($pezzoOk / $ore, 2);
                $resaValoreOk = round($pesoPdaOk / $ore, 2);
                $pesoPostOk = 0;
                $pesoPostKo = 0;
                $pesoPostBkl = 0;
                $pezzoPostOk = 0;
                $pezzoPostKo = 0;
                $pezzoPostBkl = 0;
                $pezzoBollettino = 0;
                $pezzoRid = 0;
                if (($pezzoBollettino + $pezzoRid) == 0) {
                    $percentualeBollettino = 0;
                } else {
                    $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                }
                $pezzoCartaceo = 0;
                $pezzoMail = 0;
                if (($pezzoCartaceo + $pezzoMail) == 0) {
                    $percentualeInvio = 0;
                } else {
                    $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                }
                $pezzoLuce = 0;
                $pezzoGas = 0;
                $pezzoDual = 0;
                $pezzoPolizza = 0;
                $pesoTotaleLordo = 0;
                $pesoTotalePagato = 0;
                $pezzoPagato = 0;

                break;
        }

//    echo $queryCrm;


        $html .= "<tr>";
        $html .= "<td>$user</td>";
        $html .= "<td>$idMandato</td>";
        $html .= "<td>$sede</td>";
        $html .= "<td></td>";
        $oreOperatore = round(($ore), 2);
        $html .= "<td style='border-left: 5px double #D0E4F5'>$oreOperatore</td>";
        $percentualeDispo = round((($dispo / $ore) * 100), 2);
        if ($percentualeDispo >= 6) {
            $colore = ';background-color:red';
        } else {
            $colore = '';
        }
        $html .= "<td style='border-left: 5px double #D0E4F5 $colore'>$percentualeDispo %</td>";
        $percentualeDead = round((($dead / $ore) * 100), 2);
        if ($percentualeDead >= 3) {
            $colore = ';background-color:red';
        } else {
            $colore = '';
        }
        $html .= "<td style='border-left: 5px double #D0E4F5 $colore'>$percentualeDead %</td>";
        $pausaOperatore = round($pause, 2);
        $html .= "<td style='border-left: 5px double #D0E4F5'>$pausaOperatore</td>";
        $percentualePausa = round((($pause / $ore) * 100), 2);
        if ($percentualePausa >= 12.5) {
            $colore = ';background-color:red';
        } else {
            $colore = '';
        }
        $html .= "<td style='border-left: 5px double #D0E4F5 $colore' >" . $percentualePausa . " %</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoLordo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoPagato . " </td>";

        $resaPezziOre = round(($pezzoLordo / $ore), 2);
        $resaPezziPagatoOre = round(($pezzoPagato / $ore), 2);
        if ($idMandato == "Vodafone") {
            if ($resaPezziPagatoOre >= 0.16) {
                $performance = "ALTO";
                $colorePerformance = ';background-color:green';
            } elseif ($resaPezziPagatoOre >= 0.11 && $resaPezziPagatoOre <= 0.15) {
                $performance = "MEDIO";
                $colorePerformance = ';background-color:yellow';
            } elseif ($resaPezziPagatoOre > 0 && $resaPezziPagatoOre <= 0.10) {
                $performance = "BASSO";
                $colorePerformance = ';background-color:orange';
            } else {
                $performance = "ZERO";
                $colorePerformance = ';background-color:red';
            }
        } else {
            if ($resaPezziPagatoOre >= 0.26) {
                $performance = "ALTO";
                $colorePerformance = ';background-color:green';
            } elseif ($resaPezziPagatoOre >= 0.16 && $resaPezziPagatoOre <= 0.25) {
                $performance = "MEDIO";
                $colorePerformance = ';background-color:yellow';
            } elseif ($resaPezziPagatoOre > 0 && $resaPezziPagatoOre <= 0.15) {
                $performance = "BASSO";
                $colorePerformance = ';background-color:orange';
            } else {
                $performance = "ZERO";
                $colorePerformance = ';background-color:red';
            }
        }

        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $resaPezziOre . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $resaPezziPagatoOre . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 $colorePerformance' >" . $performance . "</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pesoTotaleLordo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pesoTotalePagato . " </td>";

        $html .= "</tr>";
        $sedePrecedente = $sede;
    } else {
        $html .= "<tr style='background-color: orange'><td colspan=15></td></tr>";
        switch ($idMandato) {
            case "Plenitude":
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato) "
                        . " FROM "
                        . "plenitude "
                        . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }

//echo $queryCrm;
                break;

            case "Iren":
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato) "
                        . " FROM "
                        . "iren "
                        . "inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }
//echo $queryCrm;
                break;
            case "Green Network":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                        . "FROM "
                        . "green "
                        . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }

                break;
            case "Vivigas Energia":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino',pezzoLordo,0)),sum(if(metodoPagamento='SSD',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Posta (Residenza)',pezzoLordo,0)),sum(if(metodoInvio='Mail',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato) "
                        . "FROM "
                        . "vivigas "
                        . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }


                break;

            case "enel_out":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0))"
                        . "FROM "
                        . "enelOut "
                        . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";
                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo = round($rigaCRM[0], 2);
                    $pesoPdaOk = round($rigaCRM[1], 2);
                    $pesoPdaKo = round($rigaCRM[2], 2);
                    $pesoPdaBkl = round($rigaCRM[3], 2);
                    $pesoPdaBklp = round($rigaCRM[4], 2);
                    $pezzoLordo = round($rigaCRM[5], 0);
                    $pezzoOk = round($rigaCRM[6], 0);
                    $pezzoKo = round($rigaCRM[7], 0);
                    $pezzoBkl = round($rigaCRM[8], 0);
                    $pezzoBklp = round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk = round($rigaCRM[10], 2);
                    $pesoPostKo = round($rigaCRM[11], 2);
                    $pesoPostBkl = round($rigaCRM[12], 2);
                    $pezzoPostOk = round($rigaCRM[13], 0);
                    $pezzoPostKo = round($rigaCRM[14], 0);
                    $pezzoPostBkl = round($rigaCRM[15], 0);
                    $pezzoBollettino = round($rigaCRM[16], 0);
                    $pezzoRid = round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo = round($rigaCRM[18], 0);
                    $pezzoMail = round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce = round($rigaCRM[20], 0);
                    $pezzoGas = round($rigaCRM[21], 0);
                    $pezzoDual = round($rigaCRM[22], 0);
                    $pezzoPolizza = round($rigaCRM[23], 0);
                    $pesoTotaleLordo = round($rigaCRM[24], 0);
                    $pesoTotalePagato = round($rigaCRM[25], 0);
                    $pezzoPagato = round($rigaCRM[26], 0);
                }

                break;
            case "Vodafone":

                $pesoLordo = 0;
                $pesoPdaOk = 0;
                $pesoPdaKo = 0;
                $pesoPdaBkl = 0;
                $pesoPdaBklp = 0;
                $pezzoLordo = 0;
                $pezzoOk = 0;
                $pezzoKo = 0;
                $pezzoBkl = 0;
                $pezzoBklp = 0;
                $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                $resaValoreLordo = round($pesoLordo / $ore, 2);
                $resaPezzoOk = round($pezzoOk / $ore, 2);
                $resaValoreOk = round($pesoPdaOk / $ore, 2);
                $pesoPostOk = 0;
                $pesoPostKo = 0;
                $pesoPostBkl = 0;
                $pezzoPostOk = 0;
                $pezzoPostKo = 0;
                $pezzoPostBkl = 0;
                $pezzoBollettino = 0;
                $pezzoRid = 0;
                if (($pezzoBollettino + $pezzoRid) == 0) {
                    $percentualeBollettino = 0;
                } else {
                    $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                }
                $pezzoCartaceo = round($rigaCRM[18], 0);
                $pezzoMail = round($rigaCRM[19], 0);
                if (($pezzoCartaceo + $pezzoMail) == 0) {
                    $percentualeInvio = 0;
                } else {
                    $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                }
                $pezzoLuce = 0;
                $pezzoGas = 0;
                $pezzoDual = 0;
                $pezzoPolizza = 0;
                $pesoTotaleLordo = 0;
                $pesoTotalePagato = 0;
                $pezzoPagato = 0;
                break;
        }

//    echo $queryCrm;


        $html .= "<tr>";
        $html .= "<td>$user</td>";
        $html .= "<td>$idMandato</td>";
        $html .= "<td>$sede</td>";
        $html .= "<td></td>";
        $oreOperatore = round(($ore), 2);
        $html .= "<td style='border-left: 5px double #D0E4F5'>$oreOperatore</td>";
        $percentualeDispo = round((($dispo / $ore) * 100), 2);
        if ($percentualeDispo >= 6) {
            $colore = ';background-color:red';
        } else {
            $colore = '';
        }
        $html .= "<td style='border-left: 5px double #D0E4F5 $colore'>$percentualeDispo %</td>";
        $percentualeDead = round((($dead / $ore) * 100), 2);
        if ($percentualeDead >= 3) {
            $colore = ';background-color:red';
        } else {
            $colore = '';
        }
        $html .= "<td style='border-left: 5px double #D0E4F5 $colore'>$percentualeDead %</td>";
        $pausaOperatore = round($pause, 2);
        $html .= "<td style='border-left: 5px double #D0E4F5'>$pausaOperatore</td>";
        $percentualePausa = round((($pause / $ore) * 100), 2);
        if ($percentualePausa >= 12.5) {
            $colore = ';background-color:red';
        } else {
            $colore = '';
        }
        $html .= "<td style='border-left: 5px double #D0E4F5 $colore' >" . $percentualePausa . " %</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoLordo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoPagato . " </td>";

        $resaPezziOre = round(($pezzoLordo / $ore), 2);
        $resaPezziPagatoOre = round(($pezzoPagato / $ore), 2);
        if ($idMandato == "Vodafone") {
            if ($resaPezziPagatoOre >= 0.16) {
                $performance = "ALTO";
                $colorePerformance = ';background-color:green';
            } elseif ($resaPezziPagatoOre >= 0.11 && $resaPezziPagatoOre <= 0.15) {
                $performance = "MEDIO";
                $colorePerformance = ';background-color:yellow';
            } elseif ($resaPezziPagatoOre > 0 && $resaPezziPagatoOre <= 0.10) {
                $performance = "BASSO";
                $colorePerformance = ';background-color:orange';
            } else {
                $performance = "ZERO";
                $colorePerformance = ';background-color:red';
            }
        } else {
            if ($resaPezziPagatoOre >= 0.26) {
                $performance = "ALTO";
                $colorePerformance = ';background-color:green';
            } elseif ($resaPezziPagatoOre >= 0.16 && $resaPezziPagatoOre <= 0.25) {
                $performance = "MEDIO";
                $colorePerformance = ';background-color:yellow';
            } elseif ($resaPezziPagatoOre > 0 && $resaPezziPagatoOre <= 0.15) {
                $performance = "BASSO";
                $colorePerformance = ';background-color:orange';
            } else {
                $performance = "ZERO";
                $colorePerformance = ';background-color:red';
            }
        }
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $resaPezziOre . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $resaPezziPagatoOre . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 $colorePerformance' >" . $performance . "</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pesoTotaleLordo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pesoTotalePagato . " </td>";

        $html .= "</tr>";
    }

    $sedePrecedente = "inizio";
}


$html .= "</tr></table>";
echo $html;

