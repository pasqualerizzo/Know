<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require "/Applications/MAMP/htdocs/Know/connessione/connessione.php";
$obj19 = new Connessione();
$conn19 = $obj19->apriConnessione();

require "/Applications/MAMP/htdocs/Know/funzioni/funzioniCruscottoKpi.php";

$dataMinore = filter_input(INPUT_POST, "dataMinore");
$dataMaggiore = filter_input(INPUT_POST, "dataMaggiore");
$mandato = json_decode($_POST["mandato"], true);
$sede = json_decode($_POST["sede"], true);

$dataMinoreIta = date('d-m-Y', strtotime($dataMinore));
$dataMaggioreIta = date('d-m-Y', strtotime($dataMaggiore));

$lead = recuperoLead($conn19, $dataMaggiore, $dataMinore);

$deltaCpL = 0;
$idMandato = 0;
$queryMandato = "";
$lunghezza = count($mandato);
$sedePrecedente = "inizio";
$performance = "";
$colorePerformance = "";
$idMandato = 0;
$dataSwo = 0;
    $idMandato=$mandato[0];
      $dataSWOLuce = 0;
        $dataSWOGas = 0;
        $dataSWODual = 0;
        $KoMorosig = 0;
        $KoMorosil = 0;
        $KoSwipg = 0;
        $KoSwipl = 0;
        $pezzoOkLead = 0;
        $dataSwo = 0;
        $row = 0;
        $result = 0;
$percmortalità = 0;
$querySede = "";
$lunghezzaSede = count($sede);
$oreimb = 0;
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
$queryGroupMandato = "SELECT nomeCompleto,sede,idMandato,sum(numero)/3600 as ore,sum(pause)/3600 as pause, sum(dispo)/3600 as dispo, sum(dead)/3600 as dead ,  SUM(CASE WHEN mandato = 'Lead Inbound' THEN numero ELSE 0 END) / 3600 AS oreinb "
        . "FROM `stringheTotale`  "
        . "where giorno>='$dataMinore' and giorno<='$dataMaggiore' and livello<=6 and LENGTH(nomeCompleto)>4 and mandato<>'BO' and mandato<>'Bo'  "
        . "" . $querySede . "group by nomeCompleto order by sede,idMandato,nomeCompleto";
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
$html .= "<th>CP OK</th>";
$html .= "<th>Pezzo Pagato</th>";
$html .= "<th>Ko POST</th>";
$html .= "<th>Mortalità</th>";
$html .= "<th>% Mortalità</th>";
$html .= "<th>Resa pezzi Lordo/ore</th>";
$html .= "<th>Resa pezzi Pagato/ore</th>";
$html .= "<th>ROAS</th>";
$html .= "<th>Performance</th>";
$html .= "<th>Peso Lordo</th>";
$html .= "<th>Peso Pagato</th>";
$html .= "<th>Ore InBound</th>";
$html .= "<th>% Ore InBound</th>";
$html .= "<th>Delta Ore</th>";
$html .= "<th>Cp Ok InBound</th>";
$html .= "<th>Delta Cp Inbound</th>";
$html .= "<th>Switch Prevalente</th>";
$html .= "<th>Ko Morosi</th>";

$html .= "</tr>";

$html .= "</thead>";

$dataswo = 0;
$sedePrecedente = "inizio";// Inizializza prima del ciclo
//while ($row = mysqli_fetch_assoc($result)) {
//    $currentValue = (float)$row['campo_database']; // Usa una variabile temporanea
//    if ($currentValue > 0) {  // Esempio di condizione
//        $dataswo = $currentValue;
//    }
//}

while ($rigaMandato = $risultatoQueryGroupMandato->fetch_array()) {


    $user = $rigaMandato[0];
    $sede = $rigaMandato[1];
    $sedeRicerca = ucwords($sede);
    //$idMandato = $rigaMandato[2];

    $ore = $rigaMandato[3];
    $pause = $rigaMandato[4];
    $dispo = $rigaMandato[5];
    $dead = $rigaMandato[6];
    $oreimb = $rigaMandato[7];

    if ($sedePrecedente == "inizio" || $sedePrecedente == $sede) {
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
        $pesoPostOk = 0;
        $pesoPostKo = 0;
        $pesoPostBkl = 0;
        $pezzoPostOk = 0;
        $pezzoPostKo = 0;
        $pezzoPostBkl = 0;
        $pezzoBollettino = 0;
        $pezzoRid = 0;

        $pezzoCartaceo = 0;
        $pezzoMail = 0;

        $pezzoLuce = 0;
        $pezzoGas = 0;
        $pezzoDual = 0;
        $pezzoPolizza = 0;
        $pesoTotaleLordo = 0;
        $pesoTotalePagato = 0;
        $pezzoPagato = 0;

        $dataSWOLuce = 0;
        $dataSWOGas = 0;
        $dataSWODual = 0;
      
        
        $pezzoOkLead = 0;
        $dataSwo = 0;
        $KoMorosig = 0;
        $KoMorosil = 0;
        $KoSwipg = 0;
        $KoSwipl = 0;
        $Komorosi = 0;
        $KoSwip = 0;
        $percentualeMorosi = 0;
        $percentualeSwip =0;
        $lead = 0;
        switch ($idMandato) {
            Case "Plenitude":
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato),  "
                        . " sum(if(dataSwitchOutLuce<>'0000-00-00'and dataSwitchOutGas='0000-00-00',1,0)), "
                        . " sum(if(dataSwitchOutLuce='0000-00-00' and dataSwitchOutGas<>'0000-00-00',1,0)), "
                        . " sum(if(dataSwitchOutLuce<>'0000-00-00' and dataSwitchOutGas<>'0000-00-00',1,0)), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)), "
                        . " SUM(IF(tipoCampagna = 'Lead' AND fasePDA = 'OK', pezzoLordo, 0)) AS pezzoLeadOK,"
                        . " SUM(IF(aggiuntaPlenitude.faseMacroStatoGas = 'KO Moroso', 1, 0)) AS ConteggioKoMorosoGas ,"
                        . " SUM(IF(aggiuntaPlenitude.faseMacroStatoLuce = 'KO Moroso', 1, 0)) AS ConteggioKoMorosoLuce,"
                        . " SUM(IF(aggiuntaPlenitude.faseMacroStatoGas = 'SW Prevalente', 1, 0)) AS ConteggioSwPrevalenteGas,"
                        . " SUM(IF(aggiuntaPlenitude.faseMacroStatoLuce = 'SW Prevalente', 1, 0)) AS ConteggioSwPrevalenteLuce "
                        . " FROM "
                        . "plenitude "
                        . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";
//        echo $queryCrm;
                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += round($rigaCRM[24], 0);
                    $pesoTotalePagato += round($rigaCRM[25], 0);
                    $pezzoPagato += round($rigaCRM[26], 0);
                    /**
                     * Valori aggiunti il 11/11/2024 come richiesto da marco
                     */
                    $dataSWOLuce += $rigaCRM[27];
                    $dataSWOGas += $rigaCRM[28];
                    $dataSWODual += $rigaCRM[29];
                    $dataSwo = $dataSWOLuce + $dataSWOGas + $dataSWODual;
//            $pezzoNetto = round($rigaCRM[30], 0); 
                    $pezzoOkLead += round($rigaCRM[30], 0);
                    $dataSwo += 0;
                    $KoMorosig += round($rigaCRM[33], 0);
                    $KoMorosil += round($rigaCRM[34], 0);
                    $KoSwipg += round($rigaCRM[35], 0);
                    $KoSwipl += round($rigaCRM[36], 0);
                    $Komorosi += ($KoMorosig + $KoMorosil);
                    $KoSwip +=  ($KoSwipg + $KoSwipl);
                    $percentualeMorosi = ($Komorosi == 0 || $pezzoOk == 0) ? 0 : round(($Komorosi / $pezzoOk  ) * 100, 2);
                    $percentualeSwip   = ($KoSwip == 0 || $pezzoOk == 0)   ? 0 : round(( $KoSwip / $pezzoOk   ) * 100, 2);
                    

                    
                    
                    
                    
                }


//echo $KoMorosig;
                break;

            case "iren" :
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)) "
                        . " FROM "
                        . "iren "
                        . "inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";
//echo $queryCrm;
                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += round($rigaCRM[24], 0);
                    $pesoTotalePagato += round($rigaCRM[25], 0);
                    $pezzoPagato += round($rigaCRM[26], 0);
//            $pezzoNetto += round ($rigaCRM [27], 0);
                }

                break;
            case "Union" :
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0))"
                        . " FROM "
                        . "know.union "
                        . "inner JOIN aggiuntaUnion on know.union.id=aggiuntaUnion.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += round($rigaCRM[24], 0);
                    $pesoTotalePagato += round($rigaCRM[25], 0);
                    $pezzoPagato += round($rigaCRM[26], 0);
//            $pezzoNetto += round ($rigaCRM [27], 0);
                }

                break;

            case "green network" :
//echo $queryCrm;

                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)) "
                        . "FROM "
                        . "green "
                        . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += 0;
                    $pesoTotalePagato += 0;
                    $pezzoPagato += 0;
//            $pezzoNetto += 0;
                }

                break;
            Case "Vivigas Energia" :

                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino',pezzoLordo,0)),sum(if(metodoPagamento='SSD',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Posta (Residenza)',pezzoLordo,0)),sum(if(metodoInvio='Mail',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)) "
                        . "FROM "
                        . "vivigas "
                        . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += round($rigaCRM[24], 0);
                    $pesoTotalePagato += round($rigaCRM[25], 0);
                    $pezzoPagato += round($rigaCRM[26], 0);
//            $pezzoNetto += round($rigaCRM[27], 0);
                }
                break;

            case "Enel":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0))"
                        . "FROM "
                        . "enel "
                        . "inner JOIN aggiuntaEnel on enel.id=aggiuntaEnel.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += 0;
                    $pesoTotalePagato += 0;
                    $pezzoPagato += 0;
//            $pezzoNetto += 0;
                }

                break;
            case "enelout":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0))"
                        . "FROM "
                        . "enelOut "
                        . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += 0;
                    $pesoTotalePagato += 0;
                    $pezzoPagato += 0;
//            $pezzoNetto += 0;
                }

                $queryCrm = "SELECT "
                        . " SUM(pesoTotaleLordo), "
                        . " SUM(pezzoLordo), "
                        . " SUM(pesoPagato), "
                        . " SUM(pezzoPagato)"
                        . " FROM vodafone "
                        . " INNER JOIN aggiuntaVodafone ON vodafone.id = aggiuntaVodafone.id "
                        . " WHERE "
                        . " dataVendita <= '$dataMaggiore' "
                        . " AND dataVendita  >= '$dataMinore'  "
                        . " AND statoPda <> 'bozza' "
                        . " AND statoPda <> 'annullata' "
                        . " AND statoPda <> 'pratica doppia' "
                        . " AND statoPda <> 'In attesa Sblocco' "
                        . " AND  creatoDa='$user'";
//echo $queryCrm."<br>";
                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {

                    $pesoLordo += $rigaCRM[0];
                    $pesoPdaOk += $rigaCRM[2];
                    $pesoPdaKo += 0;
                    $pesoPdaBkl += 0;
                    $pesoPdaBklp += 0;
                    $pezzoLordo += $rigaCRM[1];
                    $pezzoOk += $rigaCRM[3];
                    $pezzoKo += 0;
                    $pezzoBkl += 0;
                    $pezzoBklp += 0;
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += 0;
                    $pesoPostKo += 0;
                    $pesoPostBkl += 0;
                    $pezzoPostOk += 0;
                    $pezzoPostKo += 0;
                    $pezzoPostBkl += 0;
                    $pezzoBollettino += 0;
                    $pezzoRid += 0;
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += 0;
                    $pezzoMail += 0;
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += 0;
                    $pezzoGas += 0;
                    $pezzoDual += 0;
                    $pezzoPolizza += 0;
                    $pesoTotaleLordo += 0;
                    $pesoTotalePagato += 0;
                    $pezzoPagato += 0;
//            $pezzoNetto += 0;
                }
                break;
        }
        $percmortalità += 0;

        if ($pezzoPagato != 0) {
// Calcola la percentuale con due cifre decimali
            $percmortalità = round(($dataSwo / $pezzoPagato) * 100, 2);
        } else {
// Gestione del caso in cui il denominatore è zero
            $percmortalità = 0; // Oppure "N/A" se preferisci
        }
//    echo $queryCrm;

        $percmortalità += 0;
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
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoOk . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoPagato . " </td>";
        
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoPostKo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $dataSwo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $percmortalità . "%</td>";

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
        $html .= "<td style='border-left: 5px double #D0E4F5 ' ></td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 $colorePerformance' >" . $performance . "</td>";

        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pesoTotaleLordo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pesoTotalePagato . " </td>";

        $oreOperatoreimb = round(($oreimb), 2);

        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $oreOperatoreimb . "</td>";

        if ($oreOperatore > 0) {
            $percImb = round(($oreOperatoreimb / $oreOperatore) * 100, 2);
        } else {
            $percImb = 0; // O gestire il caso come preferito
        }

        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $percImb . "%</td>";
        if ($oreOperatore > 0) {
            $deltaOre = round(($oreOperatore - $oreOperatoreimb), 2);
        } else {
            $percImb = 0; // O gestire il caso come preferito
        }

        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $deltaOre . "</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pezzoOkLead . "</td>";

        if ($pezzoOk > 0) {
            $deltaCpL = round(($pezzoOk - $pezzoOkLead), 2);
        } else {
            $percImb = 0; // O gestire il caso come preferito
        }
        
    
//    " . $percentualeMorosi . "%
//     . "" . $percentualeSwip . "
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $deltaCpL . "</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $percentualeSwip . "%</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $percentualeMorosi . "%</td>";

        $html .= "</tr>";
       if ($sede != $sedePrecedente) {
    $sedePrecedente = $sede;
    $dataSWOLuce = 0;
    $dataSWOGas = 0;
    $dataSWODual = 0;
    $dataswo = 0;
}
        
    } else {
        $html .= "<tr style='background-color: orange'><td colspan=27></td></tr>";
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
        $pesoPostOk = 0;
        $pesoPostKo = 0;
        $pesoPostBkl = 0;
        $pezzoPostOk = 0;
        $pezzoPostKo = 0;
        $pezzoPostBkl = 0;
        $pezzoBollettino = 0;
        $pezzoRid = 0;
        $idmandato = 0;
        $pezzoCartaceo = 0;
        $pezzoMail = 0;

        $pezzoLuce = 0;
        $pezzoGas = 0;
        $pezzoDual = 0;
        $pezzoPolizza = 0;
        $pesoTotaleLordo = 0;
        $pesoTotalePagato = 0;
        $pezzoPagato = 0;
        $dataSwo = 0;
  
        
        switch ($idmandato) {
              Case "Plenitude":
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato),  "
                        . " sum(if(dataSwitchOutLuce<>'0000-00-00'and dataSwitchOutGas='0000-00-00',1,0)), "
                        . " sum(if(dataSwitchOutLuce='0000-00-00' and dataSwitchOutGas<>'0000-00-00',1,0)), "
                        . " sum(if(dataSwitchOutLuce<>'0000-00-00' and dataSwitchOutGas<>'0000-00-00',1,0)), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)), "
                        . " SUM(IF(tipoCampagna = 'Lead' AND fasePDA = 'OK', pezzoLordo, 0)) AS pezzoLeadOK,"
                        . " SUM(IF(aggiuntaPlenitude.faseMacroStatoGas = 'KO Moroso', 1, 0)) AS ConteggioKoMorosoGas ,"
                        . " SUM(IF(aggiuntaPlenitude.faseMacroStatoLuce = 'KO Moroso', 1, 0)) AS ConteggioKoMorosoLuce,"
                        . " SUM(IF(aggiuntaPlenitude.faseMacroStatoGas = 'SW Prevalente', 1, 0)) AS ConteggioSwPrevalenteGas,"
                        . " SUM(IF(aggiuntaPlenitude.faseMacroStatoLuce = 'SW Prevalente', 1, 0)) AS ConteggioSwPrevalenteLuce "
                        . " FROM "
                        . "plenitude "
                        . "inner JOIN aggiuntaPlenitude on plenitude.id=aggiuntaPlenitude.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";
//        echo $queryCrm;
                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += round($rigaCRM[24], 0);
                    $pesoTotalePagato += round($rigaCRM[25], 0);
                    $pezzoPagato += round($rigaCRM[26], 0);
                    /**
                     * Valori aggiunti il 11/11/2024 come richiesto da marco
                     */
                    $dataSWOLuce += $rigaCRM[27];
                    $dataSWOGas += $rigaCRM[28];
                    $dataSWODual += $rigaCRM[29];
                    $dataSwo = $dataSWOLuce + $dataSWOGas + $dataSWODual;
//            $pezzoNetto = round($rigaCRM[30], 0); 
                    $pezzoOkLead += round($rigaCRM[30], 0);
                    $dataSwo += 0;
                    $KoMorosig += round($rigaCRM[33], 0);
                    $KoMorosil += round($rigaCRM[34], 0);
                    $KoSwipg += round($rigaCRM[35], 0);
                    $KoSwipl += round($rigaCRM[36], 0);
                    $Komorosi += ($KoMorosig + $KoMorosil);
                    $KoSwip +=  ($KoSwipg + $KoSwipl);
                    $percentualeMorosi = ($Komorosi == 0 || $pezzoOk == 0) ? 0 : round(($Komorosi / $pezzoOk  ) * 100, 2);
                    $percentualeSwip   = ($KoSwip == 0 || $pezzoOk == 0)   ? 0 : round(( $KoSwip / $pezzoOk   ) * 100, 2);
                    

                    
                }
                
//         echo $queryCrm;       
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
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)), "
                        . " SUM(IF(tipoCampagna = 'Lead' AND fasePDA = 'OK', pezzoLordo, 0)) AS pezzoLeadOK"
                        . " FROM "
                        . "iren "
                        . "inner JOIN aggiuntaIren on iren.id=aggiuntaIren.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += round($rigaCRM[24], 0);
                    $pesoTotalePagato += round($rigaCRM[25], 0);
                    $pezzoPagato += round($rigaCRM[26], 0);
                    $pezzoOkLead += round($rigaCRM[27], 0);
//            $pezzoNetto += round($rigaCRM[27], 0);
                }

                break;

            case "Union":
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)), "
                        . " SUM(IF(tipoCampagna = 'Lead' AND fasePDA = 'OK', pezzoLordo, 0)) AS pezzoLeadOK"
                        . " FROM "
                        . "know.union "
                        . "inner JOIN aggiuntaUnion on know.union.id=aggiuntaUnion.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede'  "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user' and statoPda<>'In attesa Sblocco' ";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += round($rigaCRM[24], 0);
                    $pesoTotalePagato += round($rigaCRM[25], 0);
                    $pezzoPagato += round($rigaCRM[26], 0);
                    $pezzoOkLead += round($rigaCRM[27], 0);
//            $pezzoNetto += round($rigaCRM[27], 0);
                }
                break;

            case "Enel":
                $queryCrm = "SELECT "
                        . " sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . " sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)), "
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato), "
                        . " sum(if(dataSwitchOutLuce<>'0000-00-00'and dataSwitchOutGas='0000-00-00',1,0)), "
                        . " sum(if(dataSwitchOutLuce='0000-00-00' and dataSwitchOutGas<>'0000-00-00',1,0)), "
                        . " sum(if(dataSwitchOutLuce<>'0000-00-00' and dataSwitchOutGas<>'0000-00-00',1,0)), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)), "
                        . " SUM(IF(tipoCampagna = 'Lead' AND fasePDA = 'OK', pezzoLordo, 0)) AS pezzoLeadOK "
                        . " FROM "
                        . "enel "
                        . "inner JOIN aggiuntaEnel on enel.id=aggiuntaEnel.id "
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
//            $pezzoNetto = round($rigaCRM[27], 0);
                    $pezzoOkLead = round($rigaCRM[27], 0);
                }
                break;

//echo $queryCrm;
            case "Greennetwork":
                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)),"
                        . " SUM(IF(tipoCampagna = 'Lead' AND fasePDA = 'OK', pezzoLordo, 0)) AS pezzoLeadOK "
                        . "FROM "
                        . "green "
                        . "inner JOIN aggiuntaGreen on green.id=aggiuntaGreen.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += 0;
                    $pesoTotalePagato += 0;
                    $pezzoPagato += 0;
//            $pezzoNetto += 0;
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
                        . " sum(totalePesoLordo), sum(pesoTotalePagato), sum(pezzoPagato), "
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)), "
                        . " SUM(IF(tipoCampagna = 'Lead' AND fasePDA = 'OK', pezzoLordo, 0)) AS pezzoLeadOK "
                        . "FROM "
                        . "vivigas "
                        . "inner JOIN aggiuntaVivigas on vivigas.id=aggiuntaVivigas.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += round($rigaCRM[24], 0);
                    $pesoTotalePagato += round($rigaCRM[25], 0);
                    $pezzoPagato += round($rigaCRM[26], 0);
                    $pezzoOkLead += round($rigaCRM[27], 0);
            $pezzoNetto += round($rigaCRM[27], 0);
                }
                break;

            case "enelOut":

                $queryCrm = "SELECT "
                        . "sum(totalePesoLordo),sum(if(fasePDA='OK',totalePesoLordo,0)),sum(if(fasePDA='KO',totalePesoLordo,0)), sum(if(fasePDA='BKL',totalePesoLordo,0)), sum(if(fasePDA='BKLP',totalePesoLordo,0)), "
                        . "sum(pezzoLordo),sum(if(fasePDA='OK',pezzoLordo,0)),sum(if(fasePDA='KO',pezzoLordo,0)), sum(if(fasePDA='BKL',pezzoLordo,0)), sum(if(fasePDA='BKLP',pezzoLordo,0)), "
                        . " sum(if(fasePost='OK',pezzoLordo,0)),sum(if(fasePost='KO',pezzoLordo,0)), sum(if(fasePost='BKL',pezzoLordo,0)), "
                        . "sum(if(fasePost='OK',totalePesoLordo,0)),sum(if(fasePost='KO',totalePesoLordo,0)), sum(if(fasePost='BKL',totalePesoLordo,0)), "
                        . " sum(if(metodoPagamento='Bollettino Postale',pezzoLordo,0)),sum(if(metodoPagamento='RID',pezzoLordo,0)),"
                        . " sum(if(metodoInvio='Cartaceo',pezzoLordo,0)),sum(if(metodoInvio='Bollettaweb',pezzoLordo,0)), "
                        . " sum(if(comodity='Luce',pezzoLordo,0)),sum(if(comodity='Gas',pezzoLordo,0)),sum(if(comodity='Dual',pezzoLordo,0)),sum(if(comodity='Polizza',pezzoLordo,0)),"
                        . " SUM(pezzoNetto),SUM(IF(fasePDA = 'OK', pezzoNetto, 0)), "
                        . " SUM(IF(tipoCampagna = 'Lead' AND fasePDA = 'OK', pezzoLordo, 0)) AS pezzoLeadOK "
                        . "FROM "
                        . "enelOut "
                        . "inner JOIN aggiuntaEnelOut on enelOut.id=aggiuntaEnelOut.id "
                        . "where data<='$dataMaggiore' and data>='$dataMinore' and sede='$sede' "
                        . "and statoPda<>'bozza' and statoPda<>'annullata' and statoPda<>'pratica doppia' AND  creatoDa='$user'";
                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {


                    $pesoLordo += round($rigaCRM[0], 2);
                    $pesoPdaOk += round($rigaCRM[1], 2);
                    $pesoPdaKo += round($rigaCRM[2], 2);
                    $pesoPdaBkl += round($rigaCRM[3], 2);
                    $pesoPdaBklp += round($rigaCRM[4], 2);
                    $pezzoLordo += round($rigaCRM[5], 0);
                    $pezzoOk += round($rigaCRM[6], 0);
                    $pezzoKo += round($rigaCRM[7], 0);
                    $pezzoBkl += round($rigaCRM[8], 0);
                    $pezzoBklp += round($rigaCRM[9], 0);
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += round($rigaCRM[10], 2);
                    $pesoPostKo += round($rigaCRM[11], 2);
                    $pesoPostBkl += round($rigaCRM[12], 2);
                    $pezzoPostOk += round($rigaCRM[13], 0);
                    $pezzoPostKo += round($rigaCRM[14], 0);
                    $pezzoPostBkl += round($rigaCRM[15], 0);
                    $pezzoBollettino += round($rigaCRM[16], 0);
                    $pezzoRid += round($rigaCRM[17], 0);
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += round($rigaCRM[18], 0);
                    $pezzoMail += round($rigaCRM[19], 0);
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += round($rigaCRM[20], 0);
                    $pezzoGas += round($rigaCRM[21], 0);
                    $pezzoDual += round($rigaCRM[22], 0);
                    $pezzoPolizza += round($rigaCRM[23], 0);
                    $pesoTotaleLordo += 0;
                    $pesoTotalePagato += 0;
                    $pezzoPagato += 0;
//            $pezzoNetto +=0;
                }
                break;
            case "Vodafone":

                $queryCrm = "SELECT "
                        . " SUM(pesoTotaleLordo), "
                        . " SUM(pezzoLordo), "
                        . " SUM(pesoPagato), "
                        . " SUM(pezzoPagato)"
                        . " FROM vodafone "
                        . " INNER JOIN aggiuntaVodafone ON vodafone.id = aggiuntaVodafone.id "
                        . " WHERE "
                        . " dataVendita <= '$dataMaggiore' "
                        . " AND dataVendita  >= '$dataMinore'  "
                        . " AND statoPda <> 'bozza' "
                        . " AND statoPda <> 'annullata' "
                        . " AND statoPda <> 'pratica doppia' "
                        . " AND creatoDa = 'claudio.loreti' "
                        . " AND statoPda <> 'In attesa Sblocco' "
                        . " AND  creatoDa='$user'";

                $risultatoCrm = $conn19->query($queryCrm);
                while ($rigaCRM = $risultatoCrm->fetch_array()) {

                    $pesoLordo += $rigaCRM[0];
                    $pesoPdaOk += $rigaCRM[2];
                    $pesoPdaKo += 0;
                    $pesoPdaBkl += 0;
                    $pesoPdaBklp += 0;
                    $pezzoLordo += $rigaCRM[1];
                    $pezzoOk += $rigaCRM[3];
                    $pezzoKo += 0;
                    $pezzoBkl += 0;
                    $pezzoBklp += 0;
                    $resaPezzoLordo = round($pezzoLordo / $ore, 2);
                    $resaValoreLordo = round($pesoLordo / $ore, 2);
                    $resaPezzoOk = round($pezzoOk / $ore, 2);
                    $resaValoreOk = round($pesoPdaOk / $ore, 2);
                    $pesoPostOk += 0;
                    $pesoPostKo += 0;
                    $pesoPostBkl += 0;
                    $pezzoPostOk += 0;
                    $pezzoPostKo += 0;
                    $pezzoPostBkl += 0;
                    $pezzoBollettino += 0;
                    $pezzoRid += 0;
                    if (($pezzoBollettino + $pezzoRid) == 0) {
                        $percentualeBollettino = 0;
                    } else {
                        $percentualeBollettino = round((($pezzoBollettino / ($pezzoBollettino + $pezzoRid)) * 100), 2);
                    }
                    $pezzoCartaceo += 0;
                    $pezzoMail += 0;
                    if (($pezzoCartaceo + $pezzoMail) == 0) {
                        $percentualeInvio = 0;
                    } else {
                        $percentualeInvio = ($pezzoCartaceo / ($pezzoCartaceo + $pezzoMail)) * 100;
                    }
                    $pezzoLuce += 0;
                    $pezzoGas += 0;
                    $pezzoDual += 0;
                    $pezzoPolizza += 0;
                    $pesoTotaleLordo += 0;
                    $pesoTotalePagato += 0;
                    $pezzoPagato += 0;
//            $pezzoNetto += 0;
                }
                if ($pezzoPagato != 0) {
// Calcola la percentuale con due cifre decimali
                    $percmortalità = round(($dataSwo / $pezzoPagato) * 100, 2);
                } else {
// Gestione del caso in cui il denominatore è zero
                    $percmortalità = 0; // Oppure "N/A" se preferisci
                }
                break;
        }
//       $dataSwo = 0;
         $dataSwo += 0;
        $percmortalità += 0;
        
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
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoOk . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoPagato . " </td>";
        
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $pezzoPostKo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $dataSwo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 ' >" . $percmortalità . " %</td>";

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
        $html .= "<td style='border-left: 5px double #D0E4F5 ' ></td>";
        $html .= "<td style='border-left: 5px double #D0E4F5 $colorePerformance' >" . $performance . "</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pesoTotaleLordo . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pesoTotalePagato . " </td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $oreOperatoreimb . "</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $percImb . "%</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $deltaOre . "</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $pezzoOkLead . "</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $deltaCpL . "</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $percentualeSwip . "%</td>";
        $html .= "<td style='border-left: 5px double #D0E4F5'  >" . $percentualeMorosi . "%</td>";



        $html .= "</tr>";

        $sedePrecedente = $sede;

   
    }
}

$sedePrecedente = "inizio";

                
                $dataSWOLuce = 0;
                $dataSWOGas = 0;
                $dataSWODual = 0;
  
               $dataswo = 0;
$html .= "</tr></table>";
echo $html;

